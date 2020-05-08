<?php

    // If navigated here without logging in
    if ( !isset($_SESSION["user"]) ) {
        // Send user back to login screen
        header("location: ?p=Login");
    }

    // Set up defaults
    $errors = $model->getErrors();
    $class = $model->getClass();

    if ( is_post_request() ) { // Form has been submit

        // Validate server-side
        $register = new app\Model\Register($model->getPage());

        // If user submit changes meet the same requirements as on registration, only adults are allowed to update their info
        if ( ($register->is_valid_registration("update") === true) && $model->getIsAdult() ) {

            // Record a log of the user registering and account
            app\Model\Logger::Log("User updated: {$_SESSION["user"]} to {$_POST["username"]}.");

            // Process update
            $model->updateUser(
                 h($_POST["id"])
                ,h($_POST["username"])
                ,h($_POST["forenames"])
                ,h($_POST["surname"])
                ,h($_POST["password"])
                ,$model->convertDate( h($_POST["dateofbirth"]) )
                ,h($_POST["email"])
                ,h($_POST["telephone"])
                ,h($_POST["address"])
                ,h($_POST["postcode"])
                ,h($_POST["parent"])
            );

            if ( !array_key_exists("child", $_POST) ) { // This was not an adult updating the child
                // Update logged in user if they just changed username
                $_SESSION["user"] = h($_POST["username"]);
            }

        } else {
            // Prepare to notify user of failure to update and show issues
            $errors = $register->getErrors();
            $class = $register->getClass();
        }
    }

    echo $view->personalDetails( $errors, $class );

    if ( $model->getIsParent() ) {
        // Show relevant parents / Children
        // if parent, show edit options for all children
        echo $view->genealogy( $errors, $class );
    }

    if ( $model->getIsCoach() || $model->getIsInSquad() ) {
        // Show squad information: Name of squad, coach, date joined squad
        echo $view->squadDetails();
    }
