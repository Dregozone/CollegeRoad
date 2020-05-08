<?php


namespace app\Model;


class Login extends AppModel
{
    protected $class = array(
         'username' => ''
        ,'password' => ''
    );

    public function __construct($page) {

        parent::__construct($page);
    }

    protected function user_exists($username) {

        $userDetails = $this->readUser($username);

        return ( gettype($userDetails) == "array" && sizeof($userDetails) > 0 );
    }

    protected function credentials_match($username, $password) {

        $userDetails = $this->readUser($username);

        if ( !array_key_exists(0, $userDetails) ) { // No results found

            return false;
        } else {
            $userDetails = $userDetails[0]; // Look at user data from Db store
        }

        if ( array_key_exists("password", $userDetails) ) { // A value was found
            $dbPassword = $userDetails["password"];
        } else {

            return false;
        }

        return $password == $dbPassword;
    }

    public function is_valid_login() {

        // Form hasnt been submit yet
        if ( !array_key_exists("username", $_POST) ) {

            return false;
        }

        if ( !$this->has_presence($_POST["username"]) ) {
            $this->errors["username"] = "Missing username";
            $this->class["username"] = " class=\"red\" ";
        }

        if ( !$this->has_presence($_POST["password"]) ) {
            $this->errors["password"] = "Missing password";
            $this->class["password"] = " class=\"red\" ";
        }

        if ( !$this->user_exists($_POST["username"]) ) {
            $this->errors["usernameExist"] = "Username doesn't exist";
            $this->class["username"] = " class=\"red\" ";
        }

        if ( !$this->credentials_match($_POST["username"], $_POST["password"]) ) {
            $this->errors["incorrectCredentials"] = "Credentials do not match";
            //$this->class["username"] = " class=\"red\" ";
            //$this->class["password"] = " class=\"red\" ";
        }

        if (sizeof($this->errors) > 0) {

            return $this->errors;
        } else {

            return true;
        }
    }

    // Start Setters
        public function setErrors($to) {
            $this->errors = $to;
        }

        public function setClass($index, $value) {
            $this->class[$index] = $value;
        }
    // End   Setters

    // Start Getters
        public function getErrors() {

            return $this->errors;
        }

        public function getClass() {

            return $this->class;
        }
    // End   Getters
}
