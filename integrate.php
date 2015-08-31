<?
 error_reporting(E_ALL ^ E_NOTICE);
 ini_set('memory_limit', '700M');
 
 include_once("controller/Controller.php");      // this is the initializer (if I could say so) for the controller part of our MVC pattern
 
 $controller = new Controller();                 // instantiate a class
 
 $controller->integrate();                       // start it. nice, isn't it?
 
?>
