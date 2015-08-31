<?
 
 error_reporting(E_ALL ^ E_NOTICE);
 ini_set('memory_limit', '700M');

 include_once("controller/Controller.php");      // this is the initializer (if I could say so) for the controller part of our MVC pattern

 
 $controller = new Controller();   // instantiate a class
 
 $code = $_GET['code'];
 if ($code) {
  $controller->rbimport();                          // start it. nice, isn't it?
 } else {
  $controller->run();               // start it. nice, isn't it?
 }
 
//echo_r ($_SERVER);

?>