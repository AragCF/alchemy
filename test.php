<?
 
 $keys = array();
 
 for ($n=0; $n<50000; $n++) {
  $thiskey = $n.substr(md5($n), 0, 4);
  
//  echo "key: ".$thiskey."<br>";
  if (in_array($thiskey, $keys)) {
   echo "found dup: ".$n."<br>";
  }
  $keys[] = $thiskey;
  
 }
 
?>