<?php

    // Determine user request
    $username = $_POST["username"] ?? "";

    // If user has submit this form AND it is valid (server side validation)
    if ( is_post_request() && (sizeof($_POST) > 0) && ($model->is_valid_login() === true) ) {

        // Process logging in action, assign $_SESSION
        $_SESSION["user"] = $username;

        // Record a log of the user logging in to system
        app\Model\Logger::Log("Logged in: {$username}.");

        // Redirect to details page
        header("location: ?p=Details");
    }

    /** Display the form:
     *  If user hasnt submit data it will be blank,
     *  Else will use values assigned by users previous failed attempt
     */
    echo $view->form( h($username) );
