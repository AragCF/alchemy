<?
 error_reporting(E_ALL ^ E_NOTICE);
 $txt = $_POST['txt'];
 $mthd = "POST";
 if (!$txt) {
  $txt = $_GET['txt'];
  $mthd = "GET";
 }
// $fname = "test.txt";
 
 $thisfolder = substr($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'] ,"/"));
 $PHP_EOL = "\r\n";
 
// mkdirr ('test/test2/test3/123/12345');
 if ($txt) {
  switch ($txt) {
   case "update":
    $now = date("Y.m.d.h.i.s");
    $ret = "Update date and time: ".$now.$PHP_EOL;
    
    $za = new ZipArchive(); 
    
    $za->open('update.zip'); 
    
    for( $i = 0; $i < $za->numFiles; $i++ ){ 
     $stat = $za->statIndex( $i ); 
     if (file_exists($stat['name'])) {
      @mkdirr ("backups");
      $ret.="Creating backup: ".$stat['name'].$PHP_EOL;
      if (substr_count($stat['name'],"/")>0) {
       $subpath = substr($stat['name'],0, strrpos($stat['name'],"/"));
//       $ret.="Subfolder: ".$subpath.$PHP_EOL;
       @mkdirr ("backups/".$subpath);
      }
      $ret.=((rename($stat['name'],"backups/".$stat['name'].".".$now.".bak"))?"Successful":"Failed").$PHP_EOL;
     } else {
      $ret.="Cannot create backup: ".$stat['name']." - file not found!".$PHP_EOL;
     }
    }
    
    $ret.="Extracting to ".$thisfolder.$PHP_EOL;
    $za->extractTo($thisfolder);
    
//    print_r($_SERVER);
    $za->close();
    
    if (file_exists('revision.ver')) {
     $c = (int)file_get_contents('revision.ver');
    } else {
     $c=0;
    }
    $c++;
    $ret.="Current revision: ".$c.$PHP_EOL;
    
    file_put_contents('revision.ver',$c);
    
    $ret.="Job done".$PHP_EOL;
    
    
   break;
  }
/*  for tests
  $fh = fopen($fname, "a+");
  fwrite($fh, "\n".$mthd."\n");
  $txt = str_replace("/newln", "\n" , $txt);
  $txt = str_replace("\'", "'", $txt);
  fwrite($fh, $txt);
  fclose($fh); 
  print "Data written to file.";
*/
 } else {
//  $fh = fopen($fname, "a+");
//  fwrite($fh, "\n".$mthd."\n");
//  $txt = str_replace("/newln", "\n" , $txt);
//  $txt = str_replace("\'", "'", $txt);
  foreach ($_FILES as $field) {
//   fwrite($fh,$field."\n\n");
   if (file_exists($thisfolder."/".$field['name'])) {
    unlink($thisfolder."/".$field['name']);
   }
   move_uploaded_file ($field['tmp_name'],$thisfolder."/".$field['name']);
   $ret= "File accepted";
//   fwrite($fh, base64_decode(str_replace(" ","+",substr($field, strpos($field, "base64,")+7))));  // decode picture from our magic query ;) and write it to disk 
//   fwrite($fh, base64_decode($field));  // decode picture from our magic query ;) and write it to disk 
//   fwrite($fh, $field);
  }
//  fclose($fh);
//  print "No data to write.";
 }
 
 if (!$ret) $ret="Nothing changed";
 
 echo $ret;
 
 function mkdirr($path) {
  $PHP_EOL = "\r\n";
  $dirs = explode("/",$path);
  $prevdirs = "";
  foreach ($dirs as $dir) {
   $prevdirs.=$dir."/";
   @mkdir($prevdirs);
//   echo $prevdirs.$PHP_EOL;
  }
//  print_r ($dirs);
 }
 
?>