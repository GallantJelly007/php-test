<?php

session_start();
define('ROOT', dirname(__FILE__));
require_once ROOT.'/config.php';
$router = new Router(require ROOT."/routes.php");
$router->run();
  
  
 
  
