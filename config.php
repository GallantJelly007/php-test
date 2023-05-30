<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Almaty");

define('SITE_NAME',"PHP-TEST");
define('DB_USER',"root");
define('DB_NAME',"php-test");
define('DB_PASS',"");
define('DB_HOST',"localhost");
define('STANDART_LANG','ru');
define('COOKIE_SECURE',false);
define('COOKIE_ONLYHTTP',false);
define('LTT',2);
define('LTRT',30);

require_once ROOT.'/core/Connect.php';
require_once ROOT.'/core/Renderer.php';
require_once ROOT.'/core/Router.php';
require_once ROOT.'/core/InputData.php';
require_once ROOT.'/core/Token.php';


$server = InputData::getServer();
define('DOMAIN','http://'.$server['HTTP_HOST']);

if(!isset($_SESSION['LANG'])){
    $_SESSION['LANG']=STANDART_LANG;
}
