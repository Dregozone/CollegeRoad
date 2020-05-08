<?php


namespace app\View;


class Admin extends AppView
{
    protected $model;
    protected $controller;

    public function __construct($model, $controller) {
        $this->model = $model;
        $this->controller = $controller;

        parent::__construct($this->model->getPage());
    }

    public function header($title) {

        return "
            <h1 style='text-align: center;'>
                {$title}
            </h1>
        ";
    }

    public function showAddRace() {

        return '
            <section style="width: 80%; margin: 1% 10%;">
                <!-- Add race -->
                <form action="?p=Admin" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Add race</legend>
                        
                        <p>
                            <label for="raceName">Race name</label>
                            <input type="text" name="raceName" id="raceName" placeholder="Race name" />
                        </p>
                        
                        <p>
                            <label for="dateOfRace">Date of race</label>
                            <input type="date" name="dateOfRace" id="dateOfRace" placeholder="Date (Y-m-d)" />
                        </p>
                        
                        <p>
                            <input type="hidden" name="action" value="addRace" />
                            <input type="submit" value="Add" />
                        </p>
                    </fieldset>
                </form>
            </section>
        ';
    }

    public function removeUserFromSquad() {

        return '
            <!-- //// REMOVE USER FROM SQUAD //// -->
            <section style="width: 80%; margin: 1% 10%;">
                <form action="?p=Admin" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Delete squad</legend>
                        
                       <!-- - list all squads this person coaches -->
                        <p>
                            <label for="squadId">Squad</label>
                            <select name="squadId" id="squadId">
                                <option value="a">a</option>
                            </select>
                        </p>
                        
                        <p>
                            <input type="hidden" name="action" value="deleteSquad" />
                            <input type="submit" value="Delete" />
                        </p>
                    </fieldset>
                </form>
            </section>
        ';
    }

    public function addUserToSquad($users, $squads) {

        $html = '
            <section style="width: 80%; margin: 1% 10%;">
                <!-- Manage squad -->
                <form action="?p=Admin" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Add user to squad</legend>
                        
                        <p> 
                            <label for="squadId">Squad</label>
                            <select name="squadId" id="squadId">
        ';

        foreach ( $squads as $squad ) {
            $html .= '
                <option value="' . $squad["squadid"] . '">' . $squad["squadname"] . '</option>    
            ';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p>
                            <label for="userId">User</label>
                            <select name="userId">
        ';

        foreach ( $users as $user ) {
            $html .= '
                <option value="' . $user["id"] . '">' . $user["username"] . '</option>    
            ';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p>
                            <input type="hidden" name="action" value="addUserToSquad" />
                            <input type="submit" value="Add" />
                        </p>
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }

    public function createSquad($users) {

        $html = '
            <section style="width: 80%; margin: 1% 10%;">                
                <!-- Create new squad -->
                <form action="?p=Admin" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Create new squad</legend>
                        
                        <p>
                            <label for="squadName">Squad name:</label>
                            <input type="text" name="squadName" id="squadName" placeholder="Squad name" />
                        </p>
                        
                        <p>
                            <label for="coach">Coach:</label>
                            <select id="coach" name="coach">
        ';

        foreach ( $users as $user ) {
            $html .= '<option value="' . $user["id"] . '">' . $user["username"] . '</option>';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p>
                            <input type="hidden" name="action" value="createSquad" />
                            <input type="submit" value="Create" />
                        </p>
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }

    public function addParentChildRelationship($users) {

        $html = '
            <!-- Manage parent/child relationships -->
            <section style="width: 80%; margin: 1% 10%;">
                <form action="?p=Admin" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Add Parent/Child relationships</legend>
                        
                        <p>
                            <label for="parent">Parent</label>
                            <select name="parentId" id="parent">
        ';

        foreach ( $users as $user ) {
            $html .= '<option value="' . $user["id"] . '">' . $user["username"] . '</option>';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p>
                            <label for="child">Child</label>
                            <select name="childId" id="child">
        ';

        foreach ( $users as $user ) {
            $html .= '<option value="' . $user["id"] . '">' . $user["username"] . '</option>';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p>
                            <input type="hidden" name="action" value="addParentChild" />
                            <input type="submit" value="Add" />
                        </p>
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }

    public function removeParentChildRelationship($parents) {

        $html = '
        <!-- Manage parent/child relationships -->
            <section style="width: 80%; margin: 1% 10%;">
                <form action="?p=Admin" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Remove Parent/Child relationships</legend>
                        
                        <p>
                            <label for="relationship">Relationship:</label>
                            <select name="relationship" id="relationship">
        ';

        foreach ( $parents as $parent ) {
            $html .= '
                <option value="' . $parent["id"] . '">(Parent) ' . $parent["Parent"] . ', (Child) ' . $parent["Child"] . '</option>
            ';
        }

        $html .= '
                            </select>
                        </p>
                        
                        <p>
                            <input type="hidden" name="action" value="removeParentChild" />
                            <input type="submit" value="Remove" />
                        </p>
                    </fieldset>
                </form>
            </section>
        ';

        return $html;
    }
}
