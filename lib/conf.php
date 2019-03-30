<?php


define("HOST","localhost");
define("DBNAME","rest");
define("DBUSER","root");
define("DBPASS","root");
define("SALT","api");
define("VERSION","1.0");

if (!function_exists('dd')) {
    function dd($data)
    {
        echo "<pre>";
        print_r($data);
        exit;
    }
}