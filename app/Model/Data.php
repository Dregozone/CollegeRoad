<?php


namespace app\Model;


class Data extends AppModel
{
    private $swimmers = [];

    public function __construct($page) {

        parent::__construct($page);
    }

    public function findResults($username, $type, $from, $to) {
        $this->readResults($username, $type, $this->convertDate($from), $this->convertDate($to));
    }

    public function findSwimmers() {
        $this->swimmers = $this->readUsers();
    }

    // Start Getters
        public function getSwimmers() {

            return $this->swimmers;
        }

        public function getResults() {

            return $this->results;
        }
    // End   Getters
}
