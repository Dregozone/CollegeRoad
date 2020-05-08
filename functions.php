<?php

    $root_end = strpos($_SERVER['SCRIPT_NAME'], '/index.php');
    $doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $root_end);
    define("WWW_ROOT", $doc_root);

    define("PROJECT_PATH", WWW_ROOT);
    define("PRIVATE_PATH", PROJECT_PATH . "/app");
    define("PUBLIC_PATH", PROJECT_PATH . "/public");


    function myAutoload($class) {
        require "{$class}.php";
    }

    spl_autoload_register('myAutoload');

    function h($string) {

        return htmlspecialchars(trim($string), ENT_QUOTES);
    }

    function u($url) {

        return urlencode($url);
    }

    function raw_u($url) {

        return rawurlencode($url);
    }

    function url_for($link) {

        if ( $link[0] != '/' ) {
            $link = "/" . $link;
        }

        return PROJECT_PATH . "{$link}";
    }

    function is_post_request() {

        return $_SERVER['REQUEST_METHOD'] == "POST";
    }

    function is_get_request() {

        return $_SERVER['REQUEST_METHOD'] == "GET";
    }

    function log_note($custom="") {
        global $page;

        if ($custom != "") { // If application logs a custom msg, return this

            return $custom;
        }

        $note = $page . ': ';

        foreach ( $_REQUEST as $index => $value ) {

            // Exclude passwords from logging table for security
            if ( $index != "password" && $index != "confirmPassword" ) {
                $note .= "{$index}={$value}, ";
            }
        }

        return $note;
    }
