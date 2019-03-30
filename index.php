<?php
    header('Content-type:text/html;charset=utf-8');
header('Access-Control-Allow-Origin: *');
    $db = require_once __DIR__."/lib/db.php";
    require_once __DIR__."/class/User.php";
    require_once __DIR__."/class/Article.php";
    require_once __DIR__."/class/Reset.php";



    $user = new User($db);
    $article = new Article($db);
    $api = new Reset($user,$article);
    $api->_run();

//echo "api</br></br></br></br>";

//
////error_reporting(E_ALL);
////ini_set("display_errors", 1);
////    $user = new User($db);
//    $article = new Article($db);
//
////    dd($article->view(1));
//
//
//dd($article->edit(1,"我是标题","我是内容",1));




