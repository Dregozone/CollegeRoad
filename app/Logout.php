<?php

    $username = $_SESSION["user"]; ////

    // Record log of user logging out of system
    app\Model\Logger::Log("Logged out: {$username}.");

    // Process logging out action
    unset($_SESSION["user"]);

    // Redirect user back to login page
    header("location: ?p=Login");
