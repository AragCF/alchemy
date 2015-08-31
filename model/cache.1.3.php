<?
 /*
  Cache library.
  
  revision history:
  
  1.0 2014jan21 15:20 Bakinskaya, Krasnodarsky Krai, Russia
  Initial release (cutpaste from legacy.php with minor enhancements)
  
  1.1 2014feb03 14:22 Khimki, Moskovskaya region, Russia
  Added secure functions (operates with data by session_id)
  
  1.2b1 2014may26 23:38 Khimki, Moskovskaya region, Russia
  Added alternate paths
  
  1.2 2014jun09 1:36 Ufa, Bashkortostan, Russia
  Added support for raw data in cache
  
  1.2 backfork join: 2014mar10 22:53 Khimki, Moskovskaya region, Russia
  Added support for custom file types (default ".dat" replaced by custom file type if passed with filename).
  eraseCache renamed to eraseCacheSecure (using session_id)
  added eraseCache - erases common cache
  
  1.3 2014nov05 Samara, 32 Gaya st.
  Added cacheExists function
  
 */
 
 
 
 
 function getCacheSubdir($mode, $name) {
  if ($mode==0) {
   return "data/cache/";
  } else {
   return "data/".$name."/";
  }
 }
 
 function getCacheDir($mode, $name) {
  $s = $_SERVER['SCRIPT_FILENAME'];
  $ret = (substr($s, 0, strrpos($s, "/")))."/".getCacheSubdir($mode, $name);
  mkdirr($ret);
  return $ret;
 }
 
 function getCacheFName($id, $secure, $mode, $name) {
  $path = getCacheDir($mode, $name);
  @mkdirr ($path);
  if ($secure) {
   if ($id) {
    return $path.$id."&".session_id().".dat";
   } else {
    return $path.session_id().".dat";
   }
  } else {
   if (strpos($id,".")>-1) {
    return $path.$id;
   } else {
    return $path.$id.".dat";
   }
  }
 }
 
 function setCache($id,$data) {
  $cachefname = getCacheFName($id, 0, 0, '');
  
  file_put_contents($cachefname,serialize($data));
  return $data;
 }
 
 function setCacheSecure($id,$data) {
  $cachefname = getCacheFName($id, 1, 0, '');
  
  file_put_contents($cachefname,serialize($data));
  return $data;
 }
 
 function getFromCache($id) {
  $cachefname = getCacheFName($id, 0, 0, '');
  
  if (file_exists($cachefname)) {
   $buf=file_get_contents($cachefname);
   $data=unserialize($buf);
   $data = cache_prepare($buf);
  }
  return $data;
 }
 
 function getFromCacheSecure($id) {
  $cachefname = getCacheFName($id, 1, 0, '');
  
  if (file_exists($cachefname)) {
   $buf=file_get_contents($cachefname);
   $data=unserialize($buf);
   $data = cache_prepare($buf);
  }
  return $data;
 }
 
 function eraseCache($id) {
  $path = getCacheDir();
  @mkdirr ($path);
  if (strpos($id,".")>-1) {
   return unlink($path.$id);
  } else {
   return unlink($path.$id.".dat");
  }
 }
 function eraseCacheSecure($id) {
  $path = getCacheDir();
  @mkdirr ($path);
  return unlink($path.$id."&".session_id().".dat");
 }
 
 
 
 function setCacheEx($id,$data,$path) {
  $cachefname = getCacheFName($id, 0, 1, $path);
  
  file_put_contents($cachefname,serialize($data));
  return $data;
 }
 
 function getFromCacheEx($id,$path) {
  $cachefname = getCacheFName($id, 0, 1, $path);
  
  if (file_exists($cachefname)) {
   $buf=file_get_contents($cachefname);
   $data = cache_prepare($buf);
//   ajax_echo_r ($data);
  }
  return $data;
 }
 
 function cacheExists($id) {
  $cachefname = getCacheFName($id, 0, 0, '');
  
  return file_exists($cachefname);
 }
 function cacheExistsEx($id,$path) {
  $cachefname = getCacheFName($id, 0, 1, $path);
  
  return file_exists($cachefname);
 }
 
 function cache_prepare($buf) {
  $data=unserialize($buf);
  if (!$data) {
   $buf = str_replace("\l","\n",$buf);
   $buf = str_replace("\n\n","\n",$buf);
   $data=explode("\n",$buf);
  }
  return $data;
 }
 
?>
