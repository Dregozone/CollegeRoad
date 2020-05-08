<?php

    // If navigated here without logging in
    if ( !isset($_SESSION["user"]) || (!$model->getIsAdmin() && !$model->getIsCoach() && !$model->getIsOfficial()) ) {
        // Send user back to login screen
        header("location: ?p=Login");
    }

    echo $view->header("Admin tools");

    // If admin
    if ( $model->getIsAdmin() ) {

        if ( isset($_POST["action"]) && $_POST["action"] == "createSquad" ) { // Form has been submit
            // Process form (Create new squad)
            $squadName  = $_POST["squadName"]   ?? '';
            $coachId    = $_POST["coach"]       ?? '';

            $model->addSquad(
                 h($squadName)
                ,h($coachId)
            );

            // array(3) { ["squadName"]=> string(7) "Squaddy" ["coach"]=> string(1) "1" ["action"]=> string(11) "createSquad" }

        } else if ( isset($_POST["action"]) && $_POST["action"] == "addParentChild" ) { // Form has been submit
            // Process form (Add parent child relationship)
            $parentUserId  = $_POST["parentId"] ?? '';
            $childUserId   = $_POST["childId"]  ?? '';

            $model->addParent(
                 h($parentUserId)
                ,h($childUserId)
            );

            // array(3) { ["parent"]=> string(1) "1" ["child"]=> string(1) "3" ["action"]=> string(14) "addParentChild" }

        } else if ( isset($_POST["action"]) && $_POST["action"] == "removeParentChild" ) { // Form has been submit
            // Process form (Remove parent child relationship)
            $parentId = $_POST["relationship"] ?? '';

            $model->removeParent(
                h($parentId)
            );

            // array(2) { ["relationship"]=> string(1) "2" ["action"]=> string(17) "removeParentChild" }
        }

        $users = $model->readUsers();
        echo $view->createSquad($users);

        echo $view->addParentChildRelationship($users);

        $parents = $model->readParents();
        echo $view->removeParentChildRelationship($parents);
    }

    // If coach
    if ( $model->getIsCoach() ) {
        if ( isset($_POST["action"]) && $_POST["action"] == "addUserToSquad" ) {
            // Add a user to squad
            $squadId    = $_POST["squadId"] ?? '';
            $userId     = $_POST["userId"]  ?? '';

            if (
                $model->addSquadUser(
                     h($squadId)
                    ,h($userId)
                ) === true
            ) {
                echo "<p style='text-align: center;'>Successfully processed!</p>";
            }
        }

        if ( isset($_POST["action"]) && $_POST["action"] == "deleteSquad" ) {
            // Delete an existing squad
            $squadId = $_POST["squadId"]    ?? '';

            $model->deleteSquad(
                h($squadId)
            );
        }

        $users  = $model->readUsers();
        $squads = $model->readSquadsByCoachUsername(h($_SESSION["user"]));
        echo $view->addUserToSquad($users, $squads);

        //echo $view->removeUserFromSquad(); //// Can be a future addition
    }

    // If official
    if ( $model->getIsOfficial() ) {

        if ( isset($_POST["action"]) && $_POST["action"] == "addRace" ) {
            // Add a new race
            $raceName   = $_POST["raceName"]    ?? '';
            $dateOfRace = $_POST["dateOfRace"]  ?? '';

            $model->createRace(
                 h($raceName)
                ,h($dateOfRace)
            );
        }

        echo $view->showAddRace();
    }
