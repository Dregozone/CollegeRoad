<?php


namespace app\Model;


class Details extends AppModel
{
    private $page;

    public function __construct($page) {

        parent::__construct($page);
    }

    // Start Getters
        public function getIsParent() {

            $parents = $this->readParents();
            $parentId = $this->readUser($_SESSION["user"])[0]["id"];

            $isParent = false;

            foreach ( $parents as $row ) {
                if ( $row["parentuserid"] == $parentId ) {
                    $isParent = true;
                }
            }

            return $isParent;
        }

        public function getIsChild() {

            return !$this->getIsAdult();
        }
    // End   Getters
}
