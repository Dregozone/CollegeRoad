<?php

    // Determine user request
    $username           = $_POST["username"]        ?? "";
    $forenames          = $_POST["forenames"]       ?? "";
    $surname            = $_POST["surname"]         ?? "";
    $password           = $_POST["password"]        ?? "";
    $confirmPassword    = $_POST["confirmPassword"] ?? "";
    $dateofbirth        = $_POST["dateofbirth"]     ?? "";
    $email              = $_POST["email"]           ?? "";
    $telephone          = $_POST["telephone"]       ?? "";
    $address            = $_POST["address"]         ?? "";
    $postcode           = $_POST["postcode"]        ?? "";

    // Determine user request
    $username = $_POST["username"] ?? "";

    // If user has submit this form AND it is valid (server side validation)
    if ( is_post_request() && (sizeof($_POST) > 0) && ($model->is_valid_registration("new") === true) ) {

        // Record a log of the user registering and account
        app\Model\Logger::Log("User registered: {$username}.");

        // Create new user in DB
        $model->createUser(
             h($_POST["username"])
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

        // Process logging in action, assign $_SESSION and redirect to details page
        $_SESSION["user"] = $username;
        header("location: ?p=Details");
    }

    /** Display the form:
     *  If user hasnt submit data it will be blank,
     *  Else will use values assigned by users previous failed attempt
     */
    echo $view->form( h($username), h($forenames), h($surname), h($password), h($confirmPassword), h($dateofbirth), h($email), h($telephone), h($address), h($postcode) );
