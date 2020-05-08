<?php


namespace app\View;


class Login extends AppView
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

    public function form($username) {

        $html = '
            <form action="?p=Login" method="post" autocomplete="off">
                <fieldset class="login">
                    <legend>Login</legend>
        ';

        if ( sizeof($this->model->getErrors()) > 0 ) { // If there are errors, display them here
            $html .= '
                <section class="errorList">
                    <h1>Form errors:</h1>
                    <ul>
            ';

            foreach ( $this->model->getErrors() as $index => $value ) {
                $html .= "<li>{$value}</li>";

                $this->model->setClass($index, ' class="red" ');
            }

            $html .= '
                    </ul>
                </section>
            ';
        }

        $html .= '
                    <p>
                        <label for="username">Username <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["username"] . ' type="text" name="username" id="username" placeholder="Username" value="' . $username . '" />
                    </p>
                    
                    <p>
                        <label for="password">Password <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["password"] . ' type="password" name="password" id="password" placeholder="Password" />
                    </p>
                    
                    <p>
                        <small style="font-weight: 100;">(<span class="required">*</span>) Required fields</small>
                    </p>
                    
                    <p>
                        <input type="submit" value="Login" />
                    </p>
                               
                    <p class="register">
                        Don\'t have an account? 
                        <a href="?p=Register">
                            Register
                        </a>
                    </p>
                </fieldset>
            </form>
        ';

        return $html;
    }
}
