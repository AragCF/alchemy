<?
 include_once('model/legacy.php');
 
 $file = $_GET['name'];
 
 $allowedfileext = array('jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'js', 'htm', 'html', 'css', 'svg');
 $fileext = strtolower(substr($file,strrpos($file,".")+1));
 
 if (in_array($fileext, $allowedfileext)) {
  $type    = mimetype($fileext);
  header("Content-type: ".$type);
  
  $today = date("F j, Y, g:i a");
  $time  = time();
  
  header("Content-Disposition: attachment;filename=".$file);
  header("Content-Transfer-Encoding: binary");
  header("Content-Length: ".filesize($file));
  header('Pragma: no-cache');
  header('Expires: 0'); 
  
  echo file_get_contents(getrootdirsrv().$file);
 }
?>
