<?php

    // If navigated here without logging in
    if ( !isset($_SESSION["user"]) ) {
        // Send user back to login screen
        header("location: ?p=Login");
    }

    echo $view->searchCriteria();

    if ( isset($_POST["action"]) ) {

        $now = new \DateTime();

        if ( $_POST["action"] == "insert" ) {

            $raceid = $_POST["type"] == "Practice" ? NULL : h($_POST["type"]); // Filler but can include race names in future

            $data = array(
                 "userid"     => h($_POST["username"])
                ,"raceid"       => $raceid
                ,"finishtime"   => h($_POST["time"])
                ,"daterecorded" => $now->format("Y-m-d H:i:s")
            );

            $model->createResult(
                 $data["userid"]
                ,$data["daterecorded"]
                ,$data["raceid"]
                ,$data["finishtime"]
                ,NULL
            );

            header("location: ?p=Data");
        } else if ($_POST["action"] == "edit") {
            // UPDATE results SET ((all)) WHERE id=h($_POST["id"])
            $model->updateResultsFinishTime(h($_POST["id"]), h($_POST["time"]));

            // Un-validate after the time has been edited
            $model->updateResultsValidate( h($_POST["id"]), NULL );

            header("location: ?p=Data");
        }

    } else if ( is_post_request() ) {

        // Show user selection
        echo $view->showCriteria();

        // Interogate data
        $model->findResults(
             h($_POST["swimmer"])
            ,h($_POST["type"])
            ,h($_POST["from"])
            ,h($_POST["to"])
        );

        // Display results
        echo $view->results();
    }

    if ( isset($_GET["action"]) ) {

        if ($_GET["action"] == "edit") {
            // Send to ?p=Data&action=update w/ POST values
            $result = $model->readResultsById(h($_GET["id"]))[0];

            echo $view->showEditForm($result);

        } else if ( $_GET["action"] == "validate" ) {
            // UPDATE results SET IsValidated=1 WHERE id=h($_GET["id"])
            $model->updateResultsValidate( h($_GET["id"]), 1 );

            header("location: ?p=Data");

        } else if ( $_GET["action"] == "delete" ) {
            // DELETE FROM results WHERE id=h($_GET["id"])
            $model->deleteResult(h($_GET["id"]));

            header("location: ?p=Data");
        }

    } else if ( $model->getIsCoach() || $model->getIsOfficial() ) { // Coaches can add Practice data, ClubOfficials can add Gala data
        echo $view->addRace();
    }
