<?php


namespace app\View;


class Details extends AppView
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

    public function personalDetails( $errors, $class ) {

        // Fixed values
        $class["id"]                    = "";
        $class["dateaccountcreated"]    = "";

        $details = $this->model->readUser($_SESSION["user"])[0];

        $disabled    = !$this->model->getIsAdult() ? " disabled " : "";
        $disabledMsg = !$this->model->getIsAdult() ? "
            <div style='text-align: center; padding: 1% 0; font-size: 120%; font-weight: bold;'>
                Please ask your parent to maintain these details
            </div>
        " : "";

        $html = "
            <h1 class='details'>
                Details
            </h1>
        ";

        if ( sizeof($errors) > 0 ) { // If there are errors, display them here
            $html .= '
                <section class="personalDetails" style="margin-bottom: 2%;">
                    <section class="errorList">
                        <h1>Form errors:</h1>
                        <ul>
            ';

            foreach ( $errors as $index => $value ) {
                $html .= "<li>{$value}</li>";
            }

            $html .= '
                        </ul>
                    </section>
                </section>
            ';
        }

        $html .= '
            <section class="personalDetails">
                <form action="?p=Details" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Personal details</legend>
        ';

        $previous = '';

        foreach ( $details as $index => $value ) {

            // If this is the next one after password entry
            if ( $previous == "password" ) {
                // Also show a confirmPassword input
                $html .= "
                    <label for='confirmPassword'>Confirm password</label>
                    <input type='password' {$class[$index]} name='confirmPassword' id='confirmPassword' value='{$details["password"]}' {$disabled} /><br />
                ";
            }

            if ( $previous == "dateofbirth" ) {
                // Also show parent select
                $html .= "
                    <label for='parent'>Parent</label>
                    <select name='parent' {$class[$index]} {$disabled} >
                        <option value=\"\"></option>
                ";

                foreach ( $this->model->readUsers() as $user ) {

                    $currentSelectedParent = array_key_exists("parent", $_POST) ? $this->h($_POST["parent"]) : "";

                    $selected = $currentSelectedParent == $user["id"] ? " selected " : "";

                    $html .= '
                        <option value="' . $user["id"] . '" ' . $selected . '>' . $user["forenames"] . " " . $user["surname"] . '</option>    
                    ';
                }

                $html .= "
                    </select>
                    <br />
                ";
            }

            $label = "<label for='{$index}'>" . ucfirst($index) . "</label>";

            // Set up fields to hide
            if (
                in_array($index, array(
                        'id'
                        ,'dateaccountcreated'
                    )
                )
            ) {
                $type = "hidden";
                $label = "";
            } else if ( $index == "password" ) {
                $type = "password";
            } else {
                $type = "text";
            }

            $html .= "
                {$label}
                <input type='{$type}' {$class[$index]} name='{$index}' id='{$index}' value='{$value}' {$disabled} /><br />
            ";

            $previous = $index;
        }

        $html .= "
            <input type='submit' value='Update' {$disabled} />
            {$disabledMsg}
        ";

        $html .= '
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }

    public function genealogy( $errors, $class ) {

        $userId = $this->model->readUser($_SESSION["user"])[0]["id"];

        $children = $this->model->readParent($userId);

        $html = '
            <section>
                <h1 style="text-align: center; margin-top: 3%;">
                    Your children\'s details
                </h1>
        ';

        foreach ( $children as $child ) {
            $childUserId = $child["childuserid"];

            $details = $this->model->readUserById($childUserId)[0];

            $html .= '
                <section class="personalDetails" style="margin-bottom: 2%;">
                    <form action="?p=Details" method="post" autocomplete="off">
                        <fieldset>
                            <legend>' . ucfirst($details["forenames"]) . ' ' . $details["surname"] . ' details (Child)</legend>
                                <input type="hidden" name="child" value="1" />
            ';

            $previous = '';

            foreach ( $details as $index => $value ) {

                // If this is the next one after password entry
                if ($previous == "password") {
                    // Also show a confirmPassword input
                    $html .= "
                        <label for='confirmPassword'>Confirm password</label>
                        <input type='password' {$class[$index]} name='confirmPassword' id='confirmPassword' value='{$details["password"]}' /><br />
                    ";
                }

                if ($previous == "dateofbirth") {
                    // Also show parent select
                    $html .= "
                        <label for='parent'>Parent</label>
                        <select name='parent' {$class[$index]}>
                            <option value=\"\"></option>
                    ";

                    foreach ($this->model->readUsers() as $user) {

                        $currentSelectedParent = array_key_exists("parent", $_POST) ? $this->h($_POST["parent"]) : "";

                        $selected = $currentSelectedParent == $user["id"] ? " selected " : "";

                        $html .= '
                            <option value="' . $user["id"] . '" ' . $selected . '>' . $user["forenames"] . " " . $user["surname"] . '</option>    
                        ';
                    }

                    $html .= "
                            </select>
                            <br />
                        ";
                }

                $label = "<label for='{$index}{$childUserId}'>" . ucfirst($index) . "</label>";

                if ($index == "password") {
                    $type = "password";
                } else if ( $index == "id" || $index == "dateaccountcreated" ) {
                    $type = "hidden";

                    $label = '';
                    $class[$index] = '';
                } else {
                    $type = "text";
                }

                $html .= "
                    {$label}
                    <input type='{$type}' {$class[$index]} name='{$index}' id='{$index}{$childUserId}' value='{$value}' /><br />
                ";


                $previous = $index;
            }

            $html .= '
                            <input type="submit" value="Update" />
                        </fieldset>
                    </form>
                </section>
            ';

        }

        $html .= '
            </section>
        ';


















        return $html;
    }

    public function squadDetails() {

        if ( $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["squadid"]) ) { // Coach has updated squad details

            $this->model->updateSquad(
                 $this->model->h( $_POST["squadid"] )
                ,$this->model->h( $_POST["squadName"] )
                ,$this->model->h( $_POST["squadCoach"] )
            );

            // Check if user now qualifies, after an update
            if ( !($this->model->getIsCoach() || $this->model->getIsInSquad()) ) {

                return ''; // User no longer had squad details to show
            }
        }

        $squad = $this->model->getSquadByUsername($_SESSION["user"]);

        // Only coaches are allowed to edit these fields
        $editable = $this->model->getIsCoach() ? "" : " disabled ";

        $squadName  = $squad[0]["squadname"];
        $squadId    = $squad[0]["squadid"];
        $coach      = $squad[0]["coach"];

        $html = '
            <section class="personalDetails" style="margin-top: 1%">
            
                <form action="?p=Details" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Squad details</legend>
                        
                        <label for="squadName">Squad name</label>
                        <input type="text" name="squadName" id="squadName" value="' . $squadName . '" ' . $editable . ' />
                        
                        <label for="squadCoach">Coach</label>
                        <select name="squadCoach" id="squadCoach" value="' . $coach . '" ' . $editable . '>
        ';

        $users = $this->model->readUsers();

        foreach ( $users as $user ) {
            $selected = strtoupper( $coach ) == strtoupper( $user["username"] ) ? " selected " : "";

            $html .= "<option value='{$user["id"]}' {$selected}>{$user['username']}</option>";
        }

        $html .= '
                        </select>
                        
                        <article style="border: 2px dotted steelblue; margin: 1.5% 30%; width: 40%; padding: 0.5% 1.5%;">
                            <h1>Current members of squad:</h1>
                            <ul>
        ';

        // List all users currently in Squad
        foreach ($squad as $user) {
            // If this is the logged in user, highlight
            $bold = strtoupper($user["user"]) == strtoupper($_SESSION["user"]) ? ' style="font-weight: bold;" ' : '';

            // list users with and without bold
            $html .= "
                <li {$bold}>{$user["user"]}</li>
            ";
        }
                        
        $html .= '      
                            </ul>
                        </article>
        ';

        if ( $this->model->getIsCoach() ) {
            $html .= '
                <input type="hidden" name="squadid" id="squadid" value="' . $squadId . '" />
                <input type="submit" value="Update" />
            ';
        }

        $html .= '    
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }
}
