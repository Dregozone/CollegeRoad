<?php


namespace app\View;


class Register extends AppView
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

    public function form($username, $forenames, $surname, $password, $confirmPassword, $dateofbirth, $email, $tel, $address, $postcode) {

        $html = '
            <form action="?p=Register" method="post" autocomplete="off">
                <fieldset class="register">
                    <legend>Register</legend>
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
                        <label for="forenames">Forename(s) <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["forenames"] . ' type="text" name="forenames" id="forenames" placeholder="Forename(s)" value="' . $forenames . '" />
                    </p>
                    
                    <p>
                        <label for="surname">Surname <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["surname"] . ' type="text" name="surname" id="surname" placeholder="Surname" value="' . $surname . '" />
                    </p>
                    
                    <p>
                        <label for="password">Password <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["password"] . ' type="password" name="password" id="password" placeholder="Password" />
                    </p>
                    
                    <p>
                        <label for="confirmPassword">Confirm password <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["confirmPassword"] . ' type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm password" />
                    </p>
                    
                    <p>
                        <label for="dateofbirth">Date of Birth <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["dateofbirth"] . ' type="text" name="dateofbirth" id="dateofbirth" placeholder="DoB (dd/mm/yyyy)" value="' . $dateofbirth . '" />
                    </p>
                    
                    <p>
                        <label for="parent">Parent <small>(If under 18)</small></label>
                        <select name="parent" ' . $this->model->getClass()["parent"] . '>
                            <option value=""></option>
        ';

        foreach ( $this->model->readUsers() as $user ) {

            $currentSelectedParent = array_key_exists("parent", $_POST) ? $this->h($_POST["parent"]) : "";

            $selected = $currentSelectedParent == $user["id"] ? " selected " : "";

            $html .= '
                <option value="' . $user["id"] . '" ' . $selected . '>' . $user["forenames"] . " " . $user["surname"] . '</option>    
            ';
        }

        $html .= '
                        </select>
                    </p>
                    
                    <p>
                        <label for="email">Email <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["email"] . ' type="text" name="email" id="email" placeholder="Email" value="' . $email . '" /> <!-- type="email" -->
                    </p>
                    
                    <p>
                        <label for="telephone">Telephone <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["telephone"] . ' type="text" name="telephone" id="telephone" placeholder="Telephone" value="' . $tel . '" />
                    </p>
                    
                    <p>
                        <label for="address">Address <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["address"] . ' type="text" name="address" id="address" placeholder="Address" value="' . $address . '" />
                    </p>
                    
                    <p>
                        <label for="postcode">Postcode <span class="required">*</span></label>
                        <input ' . $this->model->getClass()["postcode"] . ' type="text" name="postcode" id="postcode" placeholder="Postcode" value="' . $postcode . '" />
                    </p>
                    
                    <p>
                        <small style="font-weight: 100;">(<span class="required">*</span>) Required fields</small>
                    </p>
                    
                    <p>
                        <input type="submit" value="Register" />
                   </p>
                               
                    <p class="login">
                        Already have an account? 
                        <a href="?p=login">
                            Login
                        </a>
                    </p>
                </fieldset>
            </form>
        ';

        return $html;
    }
}
