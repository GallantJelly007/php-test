<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Almaty");

define('SITE_NAME',"JAS DARYN");
define('DB_USER',"root");
define('DB_NAME',"jasdaryn");
define('DB_PASS',"");
define('DB_HOST',"localhost");
define('EMAIL_ADMIN',"aidarpavl@mail.ru");
define('SMTP_MAIL','');
define('SMTP_MAIL_PASS','');
define('SMTP_MAIL_PORT',465);
define('SMTP_MAIL_SERVER','smtp.gmail.com');
define('STANDART_LANG','ru');
define('COOKIE_SECURE',false);
define('COOKIE_ONLYHTTP',false);
define('START_YEAR',2021);
define('LTT',2);
define('LTRT',30);
define('LANGUAGES', [
    'kz',
    'ru'
]);

define('CLASSES',[
    '5A', '5Б', '5В', '5Г', '6A', '6Б', '6В', '6Г', '7A', '7Б', '7В', '7Г', '8A', '8Б', '8В', '8Г', '9A', '9Б', '9В', '9Г'
]);

require_once ROOT.'/core/Connect.php';
require_once ROOT.'/core/Lang.php';
require_once ROOT.'/core/Router.php';
require_once ROOT.'/core/InputData.php';
require_once ROOT.'/core/Token.php';
require_once ROOT.'/core/PHPMailer/src/PHPMailer.php';
require_once ROOT.'/core/PHPMailer/src/SMTP.php';  
require_once ROOT.'/core/Mailer.php';
require_once ROOT.'/core/PHPExcel.php';
require_once ROOT.'/core/Renderer.php';

$server = InputData::getServer();
$protocol = stripos($server['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
define('DOMAIN',$protocol.$server['HTTP_HOST']);

if(!isset($_SESSION['LANG'])){
    $_SESSION['LANG']=STANDART_LANG;
}


