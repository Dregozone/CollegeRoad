<?php


namespace app\View;


class Logout extends AppView
{
    protected $model;
    protected $controller;

    public function __construct($model, $controller) {
        $this->model        = $model;
        $this->controller   = $controller;

        parent::__construct($this->model->getPage());
    }

    public function __destruct() {
        parent::__destruct();
    }

}
