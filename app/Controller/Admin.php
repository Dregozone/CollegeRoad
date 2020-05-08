<?php


namespace app\Controller;


class Admin extends AppController
{
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

}
