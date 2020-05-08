<?php


namespace app\Model;


class Logger extends AppModel
{
    public static function Log($msg) {

        // echo $msg;

        // perform logging actions here,
        // add username, page, action, datetime to logging db table

        // Enable this on release
        self::createLogStatic($msg);
    }
}
