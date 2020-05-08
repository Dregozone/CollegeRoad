<?php


namespace app\Model;


class AppModel extends Db
{
    private $page;

    protected $errors = []; // Default to no errors

    protected $class = array(
         'username'         => ''
        ,'password'         => ''
        ,'forenames'        => ''
        ,'surname'          => ''
        ,'confirmPassword'  => ''
        ,'dateofbirth'      => ''
        ,'parent'           => ''
        ,'email'            => ''
        ,'telephone'        => ''
        ,'address'          => ''
        ,'postcode'         => ''
    );

    public function __construct($page) {

        $this->page = $page;

        $this->openMysql();
    }

    public function __destruct() {
        $this->closeMySql();
    }

    public function h($string) {

        return htmlspecialchars(trim($string), ENT_QUOTES);
    }

    protected function is_blank($value) {

        return !isset($value) || trim($value) == "";
    }

    protected function has_presence($value) {

        return !$this->is_blank($value);
    }

    protected function has_length_exactly( $value, $length ) {

        $len = strlen($value);

        return $len == $length;
    }

    protected function has_length_between( $value, $min, $max ) {

        $len = strlen($value);

        return ( ($len >= $min) && ($len <= $max) );
    }

    protected function has_length_greater_than( $value, $length ) {

        $len = strlen($value);

        return $len > $length;
    }

    protected function has_length_less_than( $value, $length ) {

        $len = strlen($value);

        return $len < $length;
    }

    protected function has_inclusion_of($value, $set) {

        return in_array($value, $set);
    }

    protected function has_exclusion_of($value, $set) {

        return !in_array($value, $set);
    }

    protected function has_string($value, $required_string) {

        return strpos($value, $required_string) !== false;
    }

    protected function has_valid_email_format($value) {

        return filter_var($value, FILTER_VALIDATE_EMAIL);

        //$email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
        //return preg_match($email_regex, $value) === 1;
    }

    public function convertDate($orig) { // 01/01/2020 => 2020-01-01

        // This is already okay to use
        if ( $this->has_string($orig, "-") ) {

            return $orig;
        }

        $dates = explode("/", $orig);

        $date = array_key_exists(2, $dates) ? $dates[2] . "-" . $dates[1] . "-" . $dates[0] : "";

        return $date;
    }

    public function niceDate( $orig ) {

        // $orig in format (Y-m-d), need to return as 04/12/2018

        $parts = explode("-", $orig);

        return "{$parts[2]}/{$parts[1]}/{$parts[0]}";
    }

    protected function dob_is_reasonable($dob) {

        $dob = $this->convertDate($dob);

        $dobs = explode("-", $dob);

        $minAge = date('Y') - 10; // 10 years old?

        return ( ((int)$dobs[0] > 1900) && ((int)$dobs[0] <= $minAge));
    }

    protected function dob_is_child($dob) {

        $dob = $this->convertDate($dob);

        $dobs = explode("-", $dob);

        $minAge = date('Y') - 18; // 18 years old

        return (int)$dobs[0] >= $minAge;
    }

    // Start Getters
        public function getPage() {

            return $this->page;
        }

        public function getSquadByUsername($username) {

            return $this->readSquadByUsername($username);
        }

        public function getUserIdByUserName($username) {

            $user = $this->readUser($username);

            return $user[0]["id"];
        }

        public function getIsCoach() {

            $isCoach = false; // Default to not a coach
            $squadDetails = $this->readSquadByUsername($_SESSION["user"]);

            foreach ( $squadDetails as $row ) {
                if ( strtoupper( $row["coach"] ) == strtoupper( $_SESSION["user"] ) ) { // Logged in user is a coach
                    $isCoach = true;
                }
            }

            return $isCoach;
        }

        public function getIsInSquad() {

            $isInSquad = false; // Default to not in a squad
            $squadDetails = $this->readSquadByUsername($_SESSION["user"]);

            foreach ( $squadDetails as $row ) {
                if ( strtoupper( $row["user"] ) == strtoupper( $_SESSION["user"] ) ) { // Logged in user is a coach
                    $isInSquad = true;
                }
            }

            return $isInSquad;
        }

        public function getIsOfficial() {

            $results = $this->readUserGroupUsers($_SESSION["user"]);

            $isOfficial = false; // Default to false

            foreach ( $results as $result ) {
                // Check if user meets criteria
                if ( $result["usergroupname"] == "Club official" ) {
                    $isOfficial = true;
                }
            }

            return $isOfficial;
        }

        public function getIsAdmin() {

            $results = $this->readUserGroupUsers($_SESSION["user"]);

            $isAdmin = false; // Default to false

            foreach ( $results as $result ) {
                // Check if user meets criteria
                if ( $result["usergroupname"] == "Admin" ) {
                    $isAdmin = true;
                }
            }

            return $isAdmin;
        }

        public function getIsAdult() {

            $userDetails = $this->readUser($_SESSION["user"])[0];
            $dob = $userDetails["dateofbirth"];

            $adult = new \DateTime();
            $adult = $adult->modify("-18 years");
            $adult = $adult->format("Y-m-d");

            //echo $dob;   echo " - " . strtotime($dob) . "<br />";
            //echo $adult; echo " - " . strtotime($adult) . "<br />";
            //echo strtotime( $dob ) . ' => ' . strtotime( $adult ) . '<hr />';

            $isAdult = ( strtotime( $dob ) < strtotime( $adult ) );

            return $isAdult; ////
        }

        public function getErrors() {

            return $this->errors;
        }

        public function getClass() {

            return $this->class;
        }
    // End   Getters
}
