<?php


namespace app\Model;


class Register extends AppModel
{
    public function __construct($page) {

        parent::__construct($page);
    }

    protected function user_exists($username) {

        $userDetails = $this->readUser($username);

        return ( gettype($userDetails) == "array" && sizeof($userDetails) > 0 );
    }

    public function is_valid_registration($type="new") { // Shared function for new and updating

        // Form hasnt been submit yet
        if ( !array_key_exists("username", $_POST) ) {

            return false;
        }

        // Presence checks
        foreach ( $this->class as $index => $value ) {

            if ( $index != "parent" ) { // Skip parent requirement, this is handled later
                if ( !$this->has_presence($_POST[$index]) ) {
                    $this->errors[$index] = "Missing {$index}";
                }
            }
        }

        // Existing user keeps same name, case insensitive
        if ( $type == "update" && strtoupper($_POST["username"]) == strtoupper($_SESSION["user"]) ) {
            // Allowed
        } else {
            // Check if username already exists, or if the adult is modifying a child
            if ( $this->user_exists($_POST["username"]) && !array_key_exists("child", $_POST) ) {
                $this->errors["usernameTaken"] = "Username is taken";
                $this->class["username"] = " class=\"red\" ";
            }
        }

        // Length checks
        //// names: max 50 chars, min 2 chars. forenames, surname, password 3-10, confirm password 3-10
        $toCheck = array(
             'username'
            ,'forenames'
            ,'surname'
        );

        foreach ( $toCheck as $string ) {
            if ( !$this->has_length_between($_POST[$string], 2, 50) ) {
                $this->errors[$string . "Length"] = ucfirst($string) . " must be 2-50 characters";
                $this->class[$string] = " class=\"red\" ";
            }
        }


        // Password and confirm password match
        if ( !array_key_exists("confirmPassword", $_POST) || ($_POST["password"] != $_POST["confirmPassword"]) ) {
            $this->errors["passwordsMismatch"] = "Passwords do not match";
            $this->class["password"] = " class=\"red\" ";
            $this->class["confirmPassword"] = " class=\"red\" ";
        }

        // Pattern match email format, post code and DoB
        if ( !$this->has_valid_email_format($_POST["email"]) ) {
            $this->errors["emailFormat"] = "Email format incorrect";
            $this->class["email"] = " class=\"red\" ";
        }

        // Type check names are strings
        ////


        // Range check DoB is reasonable, falls within range
        // dob/dateofbirth
        $_POST["dob"] = isset($_POST["dob"]) ? $_POST["dob"] : $_POST["dateofbirth"];
        if ( !$this->dob_is_reasonable($_POST["dob"]) ) {
            $this->errors["dobRange"] = "DOB is out of range";
            $this->class["dateofbirth"] = " class=\"red\" ";
        }

        // Check for parent requirement
        if ( $this->dob_is_child($_POST["dateofbirth"]) ) { // This user is a child

            if ( !$this->has_presence($_POST["parent"]) ) { // Needs parent filled out

                $this->errors["parent"] = "Missing Parent";
                $this->class["parent"] = " class=\"red\" ";
            }
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
