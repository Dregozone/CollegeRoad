<?php


namespace app\View;


class Data extends AppView
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

    public function searchCriteria() {

        $today = new \Datetime();
        $lastWeek = new \Datetime();
        $lastWeek = $lastWeek->modify("-1 month");

        $swimmerDefault = $_POST["swimmer"] ?? "";
        $typeDefault = $_POST["type"] ?? "";
        $fromDefault = $_POST["from"] ?? $lastWeek->format("d/m/Y");
        $toDefault = $_POST["to"] ?? $today->format("d/m/Y");

        $html = '
            <section>
                <form action="?p=Data" method="post" autocomplete="off">
                    <fieldset class="criteriaUI">
                        <h1 class="criteriaUI">Search criteria:</h1>
                        
                        <select name="swimmer" style="width: 12%;">
        ';

        $this->model->findSwimmers();

        foreach ($this->model->getSwimmers() as $swimmer) {

            $default = $swimmerDefault == $swimmer["username"] ? " selected" : "";

            $html .= '<option value="' . $swimmer["username"] . '" ' . $default . ' >' . $swimmer["username"] . '</option>';
        }

        $html .= '
                        </select>
                        
                        <select name="type" style="width: 12%;">
        ';

        $types = array(
             'Practice'
            ,'Gala'
        );

        foreach ( $types as $type ) {

            $default = $typeDefault == $type ? " selected" : "";

            $html .= '<option value="' . $type . '" ' . $default . ' >' . $type . '</option>';
        }

        $html .= '
                        </select>
                        
                        <label for="from" style="margin-top: 10px;">Date from:</label>
                        <input type="text" name="from" id="from" placeholder="Date from (dd/mm/yyyy)" value="' . $fromDefault . '" style="width: 12%;" />
                        
                        <label for="to" style="margin-top: 10px;">Date to:</label>
                        <input type="text" name="to" id="to" placeholder="Date to (dd/mm/yyyy)" value="' . $toDefault . '" style="width: 12%;" />
                        
                        <input type="submit" value="Search" aria-label="Submit search" />
                    </fieldset>
                </form>
                <hr style="border: none; border-top: 1px dotted cornflowerblue;" />
            </section>
        ';

        return $html;
    }

    public function showCriteria() {

        $type    = isset($_POST["type"])    && $_POST["type"]    != "" ? " For {$this->h($_POST['type'])} swims" : '';
        $swimmer = isset($_POST["swimmer"]) && $_POST["swimmer"] != "" ? " by {$this->h($_POST['swimmer'])} "    : '';
        $from    = isset($_POST["from"])    && $_POST["from"]    != "" ? " from {$this->h($_POST['from'])}"      : '';
        $to      = isset($_POST["to"])      && $_POST["to"]      != "" ? " until {$this->h($_POST['to'])}."      : '';

        $html = "
            <section class='criteria'>
                Showing results {$type}{$swimmer}{$from}{$to}
            </section>
        ";

        return $html;
    }

    public function results() {

        $isCoach    = $this->model->getIsCoach();
        $isOfficial = $this->model->getIsOfficial();

        $html = '
            <table class="results">
                <thead>
                    <tr>
                        <th style="width: 16%;">Swimmer</th>
                        <th style="width: 16%;">Type</th>
                        <th style="width: 16%;">Time</th>
                        <th style="width: 16%;">Date Recorded</th>
                        <th style="width: 16%;">Status</th>
        ';

        if ( ($isCoach && $_POST["type"] == "Practice") || ($isOfficial && $_POST["type"] == "Gala") ) {
            $html .= '
                <th style="width: 16%;">Options</th>
            ';
        }

        $html .= '
                    </tr>
                </thead>
                <tbody>
        ';

        foreach ( $this->model->getResults() as $row ) {

            $dates = explode(" ", $row["daterecorded"]);

            $html .= '
                        <tr>
                            <td>' . $row["swimmer"] . '</td>
                            <td>' . $row["type"] . '</td>
                            <td>' . $row["finishtime"] . '</td>
                            <td>' . $this->model->niceDate($dates[0]) . '</td>
                            <td>' . $row["isvalidated"] . '</td>
            ';

            if ( ($isCoach && $row["type"] == "Practice") || ($isOfficial && substr($row["type"], 0, 4) == "Gala") ) {
                $html .= '
                    <td>
                        <a href="?p=Data&action=edit&id=' . $row["resultid"] . '">Edit</a> | 
                        <a href="?p=Data&action=validate&id=' . $row["resultid"] . '">Validate</a> | 
                        <a href="?p=Data&action=delete&id=' . $row["resultid"] . '">Delete</a>
                    </td>
                ';
            }

            $html .= '
                </tr>
            ';
        }
                    
        $html .= '            
                </tbody>
            </table>
        ';

        return $html;
    }

    public function addRace() {

        $html = '
            <section class="results" style="border: none;">
                <form action="?p=Data" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Add result</legend>
                        
                        <section style="width: 80%; margin-left: 10%; display: flex;">
                            <p class="subSection">
                                <label for="username">Username</label>
                            
                                <select name="username" id="username">
        ';

        foreach ($this->model->getSwimmers() as $swimmer) {
            $html .= '<option value="' . $this->model->getUserIdByUserName( $swimmer["username"] ) . '">' . $swimmer["username"] . '</option>';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p class="subSection">
                            <label for="type">Race/Type</label>
                            
                            <select name="type" id="type">
        ';

        if ( $this->model->getIsCoach() ) { // Coach may add practice swim
            $html .= '<option value="Practice">Practice</option>';
        }

        if ( $this->model->getIsOfficial() ) { // Official may add gala swim


            $races = $this->model->readRaces();

            //var_dump( $races );

            foreach ( $races as $race ) {
                $html .= '<option value="' . $race["raceid"] . '">' . $race["racename"] . '</option>';
            }

            //$html .= '<option value="Gala">Gala</option>';
        }

        $html .= '
                                </select>
                            </p>
                            
                            <p class="subSection">
                                <label for="time">Time</label>
                                <input type="text" name="time" id="time" placeholder="hh:mm:ss" required />
                            </p>
                            
                            <p class="subSection">
                                <input type="hidden" name="action" value="insert" />
                                <input type="submit" value="Add Race" />
                            </p>
                        
                        </section>
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }

    public function showEditForm($result) {

        $html = '
            <section style="width: 80%; margin-left: 10%;">
                <form action="?p=Data" method="post" autocomplete="off">
                    <fieldset>
                        <input type="hidden" name="id" value="' . h($_GET["id"]) . '" />
                        
                        <label for="swimmer">Swimmer</label>
                        <input type="text" name="swimmer" id="swimmer" value="' . $result["swimmer"] . '" disabled />
                        
                        <label for="type">Type</label>
                        <input type="text" name="type" id="type" value="' . $result["type"] . '" disabled />
                        
                        <label for="time">Time</label>
                        <input type="text" name="time" id="time" placeholder="hh:mm:ss" value="' . $result["finishtime"] . '" />
                    
                        <label for="daterecorded">Date recorded</label>
                        <input type="text" name="daterecorded" id="daterecorded" value="' . $result["daterecorded"] . '" disabled />
                        
                        <label for="isvalidated">Status</label>
                        <input type="text" name="isvalidated" id="isvalidated" value="' . $result["isvalidated"] . '" disabled />
                        
                        <input type="hidden" name="action" value="edit" />
                        <input type="submit" value="Edit" />
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }
}
