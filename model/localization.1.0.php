<?
// set_error_handler('safeerrors');
 function echo_loc($buf) {
  echo ($buf);
 } 
 
 function localize($buf) {
  $langs = array("ru", "en");                 // list of available languages
  $lang='ru';                                 // default language
  
  if(!isset($_SESSION)) session_start();
  if (isset($_GET['lang'])) {
   if (in_array($_GET['lang'],$langs)) {
    $lang=$_GET['lang'];
    $_SESSION['lang']=$lang;
   }
  } else if (isset($_SESSION['lang'])) {
   $lang=$_SESSION['lang'];
  }
  $startat =0;
  $prevlang="common";
  $buf=" ".$buf." ";
  $ret ="";
  
  do {
   $sellang   = "common";
   $length    = strlen($buf)-$startat-1;
   $sublength = $length;
   foreach ($langs as $thislang) {
    $len           =strpos($buf,"<" .$thislang.">",$startat)-$startat;
    $thissublength =strpos($buf,"</".$thislang.">",$startat)-$startat;
    if (($len<$length) && ($len>0)) {
     $length=$len;
     $sellang=$thislang;
     $sublength=$thissublength;
    }
   }

   if ($length) {
    $thistoken=substr($buf,$startat+1,$length-1);
   } else {
    $thistoken=substr($buf,$startat);
   }
   $ret.= "".$thistoken."";

   // for localization strings
   if ($sellang==$lang) {
    if (($length) && ($sublength>0)) {
     $thistoken=substr($buf,$startat+$length+4,$sublength-$length-4);
     $ret.= "".$thistoken;
    }
   }
   $startat+=$sublength+4;
   $prevlang=$sellang;
  } while ($startat<strlen($buf));

  $ret=str_replace("%lang%",$lang,$ret);
//  if ($lang=='ru') {
//   echo strprepare("ì<sup>2</sup>");
//   $ret=str_replace(strprepare("ì2"),strprepare("ì<sup>2</sup>"),$ret);
//  }
  return $ret;
 }
 function echo_loc2($buf) {
//  if ($_GET['test']!='1') {
//   echo ($buf);
//  } else {
   echo (localize($buf));
//  }
 }
 function buf_manage() {                     // maintenance function for proper output redirection
  $buffer = ob_get_contents();               // get contents of the output buffer to the variable
  ob_clean();                            // clear the buffer
  echo_loc2($buffer);                        // apply localization and output
 }
/*
 function addtolog($str) {
  ini_set("display_errors", "0");
  date_default_timezone_set('Europe/Moscow');
  
  $path = $_SERVER['DOCUMENT_ROOT'];
  $filename = $path."/events.log"; 
  $fh = fopen($filename, "a+"); 
  $success = fwrite($fh, date("Y.m.d H:i:s")." - ".$str."\n"); 
  fclose($fh);
 }
*/
 
 function safeerrors($errno, $errstr, $errfile, $errline) {
//  $str = $errno.'-'.$errstr.'-'.$errfile.'-'.$errline;
//  $filename = $_SERVER['DOCUMENT_ROOT']."/errors_ajax.log"; 
//  $fh = fopen($filename, "a+"); 
//  $success = fwrite($fh, date("Y.m.d H:i:s")." - ".$str."\n"); 
//  fclose($fh);
 }
 
 
 function ru_translate($text) {
  $text = strrevprepare($text);
  $fname = "data/dict/ru.dat";
  if (file_exists($fname)) {
   $f=file($fname);
   foreach ($f as $fel) {
    $rpl=explode("­",$fel);
    $rpl[0]=trim($rpl[0]);
    $rpl[1]=trim($rpl[1]);
    $text=str_replace($rpl[0].$rpl[1],$rpl[0]."­".$rpl[1],$text);
    
    $rpl[0]=ucfirst($rpl[0]);
    $text=str_replace($rpl[0].$rpl[1],$rpl[0]."­".$rpl[1],$text);
   }
  } else {
   $text = $fname." not found";
  }
//  $text=str_replace("</","<#/",$text);
  $text=str_replace("/","/­",$text);
//  $text=str_replace("<#/","</",$text);
  $text = strprepare($text);
  return $text;
 }
 
 function _echo($text) {
  echo ru_translate($text);
 }
 
 
?>