<?php


namespace app\Model;


class Db
{
    protected $conn;

    protected $logs = [];
    protected $userDetails = [];
    protected $userGroupUsers = [];
    protected $results = [];
    protected $parents = [];
    protected $squads = [];

    protected function openMysql() {

        // Fetch DB credentials
        require_once("config/database.php");

        // Open PDO connection
        try {
            $this->conn = new \PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USER, DB_PASS);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
        }






        // Start testing
            //
        // End   testing





    }

    protected function closeMySql() {
        if ( $this->conn !== null ) {
            $this->conn = null;
        }
    }


    // Start CRUD
        // Logs
        public static function createLogStatic($action) {


            // User id of 0 is Guest
            $userid = array_key_exists("user", $_SESSION) ? self::readUserStatic($_SESSION["user"])["id"] : 0; // Get user id from user name of logged in person

            try {
                // Open PDO connection
                $conn = new \PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USER, DB_PASS);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $sql = "
                    INSERT INTO logs (action, userid, dateadded)
                    VALUES (:action, :userid, NOW())
                ";

                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':action', $action);
                $stmt->bindParam(':userid', $userid);

                $stmt->execute(); // Run the insert

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            if ( $conn !== null ) {
                $conn = null;
            }
        }

        public function createLog($action) {

            $userid = $this->readUser($_SESSION["user"])["id"]; // Get user id from user name of logged in person

            try {
                $sql = "
                    INSERT INTO logs (action, userid, dateadded)
                    VALUES (:action, :userid, NOW())
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':action', $action);
                $stmt->bindParam(':userid', $userid);

                $stmt->execute(); // Run the insert

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        public function readLog($id) {

            try {
                $sql = "
                    SELECT 
                         id
                        ,action
                        ,userid
                        ,dateadded
                         
                    FROM logs 
                    WHERE id=:id                    
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->logs = $stmt->fetchAll();

                return $this->logs;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function readLogs() {

            try {
                $sql = "
                    SELECT 
                         id
                        ,action
                        ,userid
                        ,dateadded
                         
                    FROM logs
                ";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->logs = $stmt->fetchAll();

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        public function deleteLog($id) {

            try {
                $sql = "
                    DELETE 
                    FROM logs 
                    WHERE id=:id 
                    LIMIT 1             
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->logs = $stmt->fetchAll();

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }


        // Parents
        public function createParent($childId, $parentId) {
            // If this relationship does not already exists, add new relationship
            $alreadyExists = false;

            // Check to see if parent/child relationship has already been recorded to avoid duplicates
            foreach ($this->readParents() as $row) {
                if ($row["parentuserid"] == $parentId && $row["childuserid"] == $childId) {
                    $alreadyExists = true;
                }
            }

            if ( !$alreadyExists ) { // This is a new relationship
                try {
                    $sql = "
                        INSERT INTO parents (parentuserid, childuserid)
                        VALUES (:parentuserid, :childuserid)
                    ";

                    $stmt = $this->conn->prepare($sql);

                    $stmt->bindParam(':parentuserid', $parentId);
                    $stmt->bindParam(':childuserid', $childId);

                    $stmt->execute(); // Run the insert

                } catch(\PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // This relationship already exists
            }
        }

        public function readParents() {
            try {
                $sql = "
                    SELECT 
                         P.id
                        ,P.parentuserid
                        ,P.childuserid
                        ,ParentU.username AS Parent
                        ,ChildU.username AS Child
                         
                    FROM parents P
                        INNER JOIN users ParentU ON P.parentuserid = ParentU.id
                        INNER JOIN users ChildU ON P.childuserid = ChildU.id
                ";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->parents = $stmt->fetchAll();

                return $this->parents;
            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return array();
            }
        }

        public function readParent($parentId) {
            try {
                $sql = "
                        SELECT 
                             id
                            ,parentuserid
                            ,childuserid
                             
                        FROM parents
                        
                        WHERE parentuserid = :parentuserid                         
                    ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':parentuserid', $parentId);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->parents = $stmt->fetchAll();

                return $this->parents;
            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return array();
            }
        }

        public function deleteParent($parentId) {

            try {
                $sql = "
                            DELETE 
                            FROM parents 
                            WHERE id=:id 
                            LIMIT 1             
                        ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $parentId);

                $stmt->execute();

                //$result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                //$this->logs = $stmt->fetchAll();

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }


        // Races
        public function createRace($raceName, $raceDate) {
            // If this relationship does not already exists, add new relationship
            $alreadyExists = false;

            // Check to see if parent/child relationship has already been recorded to avoid duplicates
            foreach ($this->readRaces() as $row) {
                if ($row["racename"] == $raceName) {
                    $alreadyExists = true;
                }
            }

            if ( !$alreadyExists ) { // This is a new relationship
                try {
                    $sql = "
                        INSERT INTO races (name, dateofrace)
                        VALUES (:name, :dateofrace)
                    ";

                    $stmt = $this->conn->prepare($sql);

                    $stmt->bindParam(':name', $raceName);
                    $stmt->bindParam(':dateofrace', $raceDate);

                    $stmt->execute(); // Run the insert

                } catch(\PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // This race already exists
                //echo "Already exists";
            }
        }

        public function readRaces() {

            try {
                $sql = "
                     SELECT 
                        R.id AS raceid 
                       ,R.name AS racename 
                        
                    FROM races R                          
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->races = $stmt->fetchAll();

                return $this->races;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }


        // Results
        public function createResult($userid, $daterecorded, $raceid, $finishtime, $isvalidated) {

            try {
                $sql = "
                    INSERT INTO results (userid, daterecorded, raceid, finishtime, isvalidated)
                    VALUES (:userid, :daterecorded, :raceid, :finishtime, :isvalidated)
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':userid', $userid);
                $stmt->bindParam(':daterecorded', $daterecorded);
                $stmt->bindParam(':raceid', $raceid);
                $stmt->bindParam(':finishtime', $finishtime);
                $stmt->bindParam(':isvalidated', $isvalidated);

                $stmt->execute(); // Run the insert

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        public function readResults($username, $type, $from, $to) {

            try {
                $sql = "
                                SELECT 
                                     R.id AS resultid
                                    ,U.username AS swimmer
                                    ,R.daterecorded
                                    ,CASE 
                                        WHEN RA.name IS NULL 
                                            THEN 'Practice' 
                                        ELSE 
                                                 CONCAT('Gala (', RA.name, ')') 
                                     END AS type
                                    ,R.finishtime
                                    ,CASE 
                                        WHEN R.isvalidated IS NULL                                         
                                            THEN 'Pending'
                                        ELSE 
                                                 'Validated'
                                     END AS isvalidated                                        
                                     
                                FROM results R 
                                    INNER JOIN users U ON R.userid = U.id
                                    
                                    LEFT JOIN races RA ON R.raceid = RA.id
                                
                                
                                WHERE 
                                        U.username = :username
                                     
                                     AND CASE 
                                        WHEN RA.name IS NULL 
                                            THEN 'Practice' 
                                        ELSE 
                                                 'Gala' 
                                     END = :type 
                                                        
                                    AND R.daterecorded >= :from 
                                    AND R.daterecorded <= :to
                                    
                                ORDER BY R.daterecorded DESC                                    
                            ";

                $stmt = $this->conn->prepare($sql);

                $fromMod = $from . ' 00:00:00';
                $toMod = $to . ' 23:59:59';

                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':from', $fromMod);
                $stmt->bindParam(':to', $toMod);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->results = $stmt->fetchAll();

                return $this->results;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function readResultsById($id) {

            try {
                $sql = "
                    SELECT 
                         R.id AS resultid
                        ,U.username AS swimmer
                        ,R.daterecorded
                        ,CASE 
                            WHEN RA.name IS NULL 
                                THEN 'Practice' 
                            ELSE 
                                     'Gala' 
                         END AS type
                        ,R.finishtime
                        ,CASE 
                            WHEN R.isvalidated IS NULL                                         
                                THEN 'Pending'
                            ELSE 
                                     'Validated'
                         END AS isvalidated                                        
                         
                    FROM results R 
                        INNER JOIN users U ON R.userid = U.id
                        
                        LEFT JOIN races RA ON R.raceid = RA.id
                    
                    
                    WHERE R.id=:id
                        
                    ORDER BY R.daterecorded DESC                                    
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->results = $stmt->fetchAll();

                return $this->results;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function updateResultsFinishTime($id, $finishtime) {

            try {
                $sql = "
                    UPDATE results 
                    SET finishtime=:finishtime 
                    WHERE id=:id 
                    
                    LIMIT 1
                ";

                // Prepare statement
                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':finishtime', $finishtime);

                // execute the query
                $stmt->execute();

                // echo a message to say the UPDATE succeeded
                if ( $stmt->rowCount() > 0 ) { // Something was updated
                    echo '
                                <section class="notification">
                                    Personal details updated successfully
                                </section>
                            ';
                }
            }
            catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }

        public function updateResultsValidate($id, $isvalidated) {

            try {
                $sql = "
                        UPDATE results 
                        SET isvalidated=:isvalidated 
                        WHERE id=:id 
                        
                        LIMIT 1
                    ";

                // Prepare statement
                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':isvalidated', $isvalidated);

                // execute the query
                $stmt->execute();

                // echo a message to say the UPDATE succeeded
                if ( $stmt->rowCount() > 0 ) { // Something was updated
                    echo '
                                    <section class="notification">
                                        Personal details updated successfully
                                    </section>
                                ';
                }
            }
            catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }

        public function deleteResult($id) {

            try {
                $sql = "
                        DELETE 
                        FROM results 
                        WHERE id=:id 
                        LIMIT 1             
                    ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->logs = $stmt->fetchAll();

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }


        // Roles




        // Squads
        public function createSquad($squadName, $coachId) {
            // If this relationship does not already exists, add new relationship
            $alreadyExists = false;

            // Check to see if parent/child relationship has already been recorded to avoid duplicates
            foreach ($this->readSquads() as $row) {
                if ($row["squadname"] == $squadName) {
                    $alreadyExists = true;
                }
            }

            if ( !$alreadyExists ) { // This is a new relationship
                try {
                    $sql = "
                        INSERT INTO squads (name, coachuserid, datecreated)
                        VALUES (:name, :coachuserid, NOW())
                    ";

                    $stmt = $this->conn->prepare($sql);

                    $stmt->bindParam(':name', $squadName);
                    $stmt->bindParam(':coachuserid', $coachId);

                    $stmt->execute(); // Run the insert

                } catch(\PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // This race already exists
                //echo "Already exists";
            }
        }

        public function readSquads() {

            try {
                $this->squads = []; // reset

                $sql = "
                    SELECT 
                         S.name AS squadname
                        ,S.id AS squadid
                        ,CoachU.username AS coach 
                        ,CoachU.id AS coachid
                        ,U.username AS user
                    
                    FROM squadusers SU 
                        INNER JOIN users U ON SU.userid = U.id
                        INNER JOIN squads S ON SU.squadid = S.id
                        INNER JOIN users CoachU ON S.coachuserid = CoachU.id
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->squads = $stmt->fetchAll();

                return $this->squads;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function readSquadByUsername($username) {

            try {
                $this->squads = []; // reset

                $sql = "
                    SELECT 
                         S.name AS squadname
                        ,S.id AS squadid
                        ,CoachU.username AS coach 
                        ,CoachU.id AS coachid
                        ,U.username AS user
                    
                    FROM squadusers SU 
                        INNER JOIN users U ON SU.userid = U.id
                        INNER JOIN squads S ON SU.squadid = S.id
                        INNER JOIN users CoachU ON S.coachuserid = CoachU.id
                    
                    WHERE 
                        S.name = (
                            SELECT 
                                 S.name AS squadname
                    
                            FROM squadusers SU 
                                INNER JOIN users U ON SU.userid = U.id
                                INNER JOIN squads S ON SU.squadid = S.id
                                INNER JOIN users CoachU ON S.coachuserid = CoachU.id
                            
                            WHERE 
                                    U.username = :username
                                OR 	CoachU.username = :username
                            
                            LIMIT 1
                        )
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':username', $username);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->squads = $stmt->fetchAll();

                return $this->squads;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function readSquadsByCoachUsername($coachUsername) {

            try {
                $this->squads = []; // reset

                $sql = "
                        SELECT 
                             S.name AS squadname
                            ,S.id AS squadid
                            
                        FROM squads S 
                            INNER JOIN users CoachU ON S.coachuserid = CoachU.id
                        
                        WHERE 
                            CoachU.username = :username
                            
                        GROUP BY S.name, S.id
                    ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':username', $coachUsername);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->squads = $stmt->fetchAll();

                return $this->squads;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function updateSquad($id, $squadName, $coachUserId) {

            try {
                $sql = "
                    UPDATE squads 
                    SET                         
                      name = :squadname
                     ,coachuserid = :coachuserid                  
                     
                    WHERE id=:id 
                    
                    LIMIT 1
                ";

                // Prepare statement
                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':squadname', $squadName);
                $stmt->bindParam(':coachuserid', $coachUserId);

                // execute the query
                $stmt->execute();

                // echo a message to say the UPDATE succeeded
                if ( $stmt->rowCount() > 0 ) { // Something was updated
                    echo '
                        <section class="notification">
                            Squad details updated successfully
                        </section>
                    ';
                }
            }
            catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }

        public function deleteSquad($squadId) {

            try {
                $sql = "
                    DELETE 
                    FROM squads 
                    WHERE id=:id 
                    LIMIT 1             
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $squadId);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                //$this->logs = $stmt->fetchAll();

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }


        // SquadUsers
        public function createSquadUser($squadId, $userId) {
            // If this relationship does not already exists, add new relationship
            $alreadyExists = false;

            $username = $this->readUserById( $userId )[0]["username"];

            // Check to see if parent/child relationship has already been recorded to avoid duplicates
            foreach ($this->readSquadUsers() as $row) {

                //echo strtoupper($row["username"]) . " - " . strtoupper($username) . "<br />";

                if (strtoupper($row["username"]) == strtoupper($username)) {
                    $alreadyExists = true;
                }
            }

            if ( !$alreadyExists ) { // This is a new relationship
                try {
                    $sql = "
                        INSERT INTO squadusers (squadid, userid, datemodified)
                        VALUES (:squadid, :userid, NOW())
                    ";

                    $stmt = $this->conn->prepare($sql);

                    $stmt->bindParam(':squadid', $squadId);
                    $stmt->bindParam(':userid', $userId);

                    $stmt->execute(); // Run the insert

                    return true;
                } catch(\PDOException $e) {
                    echo "Error: " . $e->getMessage();

                    return false;
                }
            } else {
                // This race already exists
                //echo "Already exists";

                return false;
            }
        }

        public function readSquadUsers() {

            try {
                $this->squadusers = []; // reset

                $sql = "
                    SELECT 
                         SU.id AS squadid
                        ,U.username AS username
                    
                    FROM squadusers SU 
                        INNER JOIN users U ON SU.userid = U.id
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->squads = $stmt->fetchAll();

                return $this->squads;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }


        // UserGroupRoles




        // UserGroups




        // UserGroupUsers
        public function readUserGroupUsers($username) {

            try {
                $sql = "
                    SELECT 
                         UG.name AS usergroupname  
                        
                    FROM usergroupusers UGU  
                        INNER JOIN usergroups UG ON UGU.usergroupid = UG.id 
                        INNER JOIN users U ON UGU.userid = U.id 
                    
                    WHERE 
                        U.username = :username
                        
                    GROUP BY UG.name                   
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':username', $username);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->userGroupUsers = $stmt->fetchAll();

                return $this->userGroupUsers;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }


        // Users
        public function createUser($username, $forenames, $surname, $password, $dateofbirth, $email, $telephone, $address, $postcode, $parent) {

            try {
                $sql = "
                        INSERT INTO users (username, forenames, surname, password, dateofbirth, email, telephone, address, postcode, dateaccountcreated)
                        VALUES (:username, :forenames, :surname, :password, :dateofbirth, :email, :telephone, :address, :postcode, NOW())
                    ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':forenames', $forenames);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':dateofbirth', $dateofbirth);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telephone', $telephone);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':postcode', $postcode);

                $stmt->execute(); // Run the insert

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            // Check if parent relationship should be added
            if ( $parent != "" ) { // If parent exists

                $childId = $this->readUser($username)["id"];

                $this->createParent($childId, $parent);
            }
        }

        public static function readUserStatic($username) {

            try {

                $conn = new \PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USER, DB_PASS);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $sql = "
                    SELECT 
                         id
                        ,username
                        ,forenames
                        ,surname
                        ,password
                        ,dateofbirth
                        ,email
                        ,telephone
                        ,address
                        ,postcode
                        ,dateaccountcreated
                         
                    FROM users 
                    WHERE username=:username                    
                ";

                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':username', $username);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $userDetails = $stmt->fetchAll();

                if ( $conn !== null ) {
                    $conn = null;
                }

                return $userDetails[0];

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                if ( $conn !== null ) {
                    $conn = null;
                }

                return false;
            }
        }

        public function readUser($username) {

            try {
                $sql = "
                    SELECT 
                         id
                        ,username
                        ,forenames
                        ,surname
                        ,password
                        ,dateofbirth
                        ,email
                        ,telephone
                        ,address
                        ,postcode
                        ,dateaccountcreated
                         
                    FROM users 
                    WHERE username=:username                    
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':username', $username);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->userDetails = $stmt->fetchAll();

                return $this->userDetails;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function readUserById($id) {

            try {
                $sql = "
                            SELECT 
                                 id
                                ,username
                                ,forenames
                                ,surname
                                ,password
                                ,dateofbirth
                                ,email
                                ,telephone
                                ,address
                                ,postcode
                                ,dateaccountcreated
                                 
                            FROM users 
                            WHERE id = :id                    
                        ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->userDetails = $stmt->fetchAll();

                return $this->userDetails;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function readUsers() {

            try {
                $sql = "
                    SELECT 
                         id
                        ,username
                        ,forenames
                        ,surname
                        ,password
                        ,dateofbirth
                        ,email
                        ,telephone
                        ,address
                        ,postcode
                        ,dateaccountcreated
                         
                    FROM users                
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->execute();

                $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);

                $this->userDetails = $stmt->fetchAll();

                return $this->userDetails;

            } catch(\PDOException $e) {
                echo "Error: " . $e->getMessage();

                return false;
            }
        }

        public function updateUser($id, $username, $forenames, $surname, $password, $dateofbirth, $email, $telephone, $address, $postcode, $parent) {

            try {
                $sql = "
                    UPDATE users 
                    SET                         
                      username=:username
                     ,forenames=:forenames
                     ,surname=:surname
                     ,password=:password
                     ,dateofbirth=:dateofbirth
                     ,email=:email
                     ,telephone=:telephone
                     ,address=:address
                     ,postcode=:postcode                        
                     
                    WHERE id=:id 
                    
                    LIMIT 1
                ";

                // Prepare statement
                $stmt = $this->conn->prepare($sql);

                $stmt->bindParam(':id', $id);

                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':forenames', $forenames);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':dateofbirth', $dateofbirth);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telephone', $telephone);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':postcode', $postcode);

                // execute the query
                $stmt->execute();

                // echo a message to say the UPDATE succeeded
                if ( $stmt->rowCount() > 0 ) { // Something was updated
                    echo '
                        <section class="notification">
                            Personal details updated successfully
                        </section>
                    ';
                }
            }
            catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }

            // Check if parent relationship should be added
            if ( $parent != "" ) { // If parent exists

                $childId = $this->readUser($username)[0]["id"];

                $this->createParent($childId, $parent);
            }
        }

    // End   CRUD



    // Start Getters
        public function getConn() {

            return $this->conn;
        }

        public function getResults() {

            return $this->results;
        }

        public function getParents() {

            return $this->results;
        }
    // End   Getters
}
