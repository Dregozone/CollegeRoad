<?php

    ob_start(); // Start output buffering
    session_start(); // Start PHP session

    // Load common functions
    require_once("functions.php");

    // Determine page requested, default to login page
    $page = (isset($_GET["p"]) && file_exists("app/" . h($_GET["p"]) . ".php"))
        ? h($_GET["p"])
        : "Login"
    ;

    $modelString        = "app\\Model\\$page";
    $controllerString   = "app\\Controller\\$page";
    $viewString         = "app\\View\\$page";

    // Page specific object creation
    $model      = new $modelString($page);
    $controller = new $controllerString($model);
    $view       = new $viewString($model, $controller);

    // Log page and action
    app\Model\Logger::Log(log_note());

    // Page to be processed
    include "app/$page.php";

    // Only display HTML content once we're finished processing to keep header modifications possible
    ob_flush();
