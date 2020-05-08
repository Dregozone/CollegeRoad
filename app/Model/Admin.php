<?php


namespace app\Model;


class Admin extends AppModel
{
    public function addRace($raceName, $dateOfRace) {

        // Check is string and less than max field length
        if ( gettype($raceName) != "string" || strlen($raceName) > 55 ) {

            $this->errors[] = "Race name must be a string of length 55 characters or less";

            return false;
        }

        $this->createRace($raceName, $dateOfRace);

        return true;
    }

    public function addSquad($squadName, $coach) {
        $this->createSquad($squadName, $coach);
    }

    public function addSquadUser($squadId, $userId) {

        if ( !is_numeric($squadId) || !is_numeric($userId) ) {

            $this->errors[] = "SquadID and UserID must be numeric";

            return false;
        }

        $this->createSquadUser($squadId, $userId);

        return true;
    }

    public function addParent($parentUserId, $childUserId) {

        if (!is_numeric($parentUserId) || !is_numeric($childUserId)) {

            $this->errors[] = "Parent and Child user IDs must be numeric";

            return false;
        }

        $this->createParent($childUserId, $parentUserId);

        return true;
    }

    public function removeParent($parentId) {

        if (!is_numeric($parentId)) {

            $this->errors[] = "Parent ID must be numeric";

            return false;
        }

        $this->deleteParent($parentId);

        return true;
    }
}
