<?php


namespace app\Controller;


class Logout extends AppController
{
    private $model;

    public function __construct($model) {
        $this->model = $model;

        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }

}
