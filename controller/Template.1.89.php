<?
 /* template class for bricks pro
  revision history:
  
  1.0
  Initial release
  
  
  1.4 2014feb19 21:14 Khimki, Moscow region, Russia
  function preview() added
  
  1.5 2015feb25 05:59 Khimki, Moscow region, Russia
  public variable is_template added
  
  1.7 2014may05 0:00 Donino, Ramensky district, Moscow region, Russia
  public function getVariables added
  
  1.8 2014aug28 0:32 Village, Samara region, Russia
  params to template added
  
  1.82 2014sep07 2:50 Village, Samara region, Russia
  getVariables optimized
  
  1.83 2014sep09 0:53 On the road, Somewhere in Mordovia, Russia
  getVariables optimized: added limit of 20 chars for each variable.
  
  1.84 2014oct31 3:40 32, Gaya st., Samara, Russia
  added check for wrong Template class instantiation syntax
  
  1.85 2014dec12 16:28 Samara, 32 Gaya st.
  Fixed checked state apply for radio buttons
  
  1.87 2014dec31 9:22 Samara, 32 Gaya st.
  Fixed returnloop returning non-relative data when loop id not found in source template
  
  1.88 2015feb09 23:56 On the road (in the train), Saratovskaya obl.
  Now single percent symbol is not treated as wrong template name
  
  1.89 2015aug05 2:14 Saint-Petersburg, Mother's room
  Added default empty $loopblock in fillloop. Also fillloop and removeloop now able to process multiple blocks of the same name.
  
 */
 
 $templates_buffer = Array();
 class Template {                      // used to handle the View template files
  public  $srctemplate;
  public  $template;
  public  $filename;
  public  $viewroot;
  public  $is_template;
  
  function Template($viewroot, $filename) {       // the constructor function
   if ($filename=="") {
    echo "Wrong declaration of new Template. Backtrace:<br>";
    echo backtrace();
    return;
   }
   $this->viewroot = $viewroot."/templates/";
   $this->filename=$this->viewroot.$filename;
   if (is_file($this->filename)) {                                       // load template from file
    $this->template = filestr($this->filename);
    $this->srctemplate = $this->template;
    $this->is_template = 1;
   } else {
    $this->template = $filename ;                                        // if this is not a file - load as raw data
    $this->srctemplate = $this->template;
    $this->is_template = 0;
   }
  }
  
  function load($filename) {             // loads the template
   $this->filename=$filename;
   global $templates_buffer;
   if ($templates_buffer[$this->filename]) {
    $this->template = $templates_buffer[$this->filename];
   } else {
    if (is_file($this->filename)) {
     $this->template = filestr($this->filename);
    } else {
     echo "File ".$this->filename." not found!";
     $this->template    = " ";
    }
    $templates_buffer[$this->filename] = $this->template;
   }
   $this->srctemplate = $this->template;
  }
  
  function loadtext($data) {             // loads the template from text
   $this->template      = $data;
   $this->srctemplate   = $data;
  }
  
  function reload() {                                                           // used to refresh the template (useful in series of multiple fills of a single template)
   $this->template = $this->srctemplate;
  }
  
  function fill($from, $to) {                                                   // used to replace the placeholders with actual data
   $this->template = str_replace($from, $to,   $this->template);                // do a replace
  }
  
  function filloptional($from, $to) {                                           // used to replace the optional placeholders with actual data
   $from_clean = str_replace("%", "", $from);
   $opentag = "<optional ".$from_clean.">";
   $startat_L = strpos(" ".strtolower($this->template), strtolower($opentag))-1;
   $startat_R = $startat_L + strlen($opentag);
//   echo "startat_L: ".$startat_L."<br>";
//   echo "opentag: ".htmlspecialchars($opentag)."<br>";
   if ($startat_L>=0) {
//    echo $startat."<br>";
    $closetag = "</optional ".$from_clean.">";
    $endat_L = strpos(" ".strtolower($this->template), strtolower($closetag))-1;
    $endat_R = $endat_L + strlen($closetag);
    if ($endat_L>$startat_L) {
/*
     echo $startat_L."<br>";
     echo $startat_R."<br>";
     echo $endat_L."<br>";
     echo $endat_R."<br><br>";
*/
     if ($to) {
      $this->template = substr($this->template, 0, $startat_L).substr($this->template, $startat_R, $endat_L-$startat_R).substr($this->template, $endat_R);
      $this->template = str_replace($from, $to,   $this->template);              // do a replace
     } else {
      $this->template = substr($this->template, 0, $startat_L).substr($this->template, $endat_R);
     }
    }
   }
//   if ($to) {
   
//   } else {
//    $this->template = str_replace($from, $to,   $this->template);               // do a replace
//   }
  }
  
  function loadloop($loopid) {                                                  // used to load the groups of placeholders
   if (strlen($this->template)<2) return;
   $startat = strpos(" ".$this->template, "<".$loopid.">")+strlen($loopid)+1;
   if ($startat>=0) {
//    echo $startat."<br>";
//    echo $startat.' - '.strlen($this->template);
    if ($startat<strlen($this->template)) { 
     $endat = strpos(" ".$this->template, "</".$loopid.">",$startat)-1;
 //    echo "</".$loopid.">"."<br>";
     if ($endat) {
 //     echo $endat."<br>";
      $this->template=substr($this->template, $startat, $endat-$startat);
      $this->srctemplate = $this->template;
 //     echo $this->template."<br>";
     } else {
      $this->template = "";
      $this->srctemplate = $this->template;
     }
    } else {
     $this->template = "";
     $this->srctemplate = $this->template;
    }
   } else {
    $this->template = "";
    $this->srctemplate = $this->template;
   }
   if (($startat==-1) || ($endat==-1)) {
    $this->template = "";
    $this->srctemplate = $this->template;
   }
//   echo "startat: ".$startat.", endat: ".$startat."<br>, this->template: ".$this->template;
  }
  
  function returnloop($loopid) {                                                  // used to load the groups of placeholders
   $startat = strpos(" ".$this->template, "<".$loopid.">")+strlen($loopid)+1;
//    echo $loopid."-".$startat."<br>";
   if ($startat>=strlen("<".$loopid.">")) {
//    $endat = strpos(" ".$this->template, "</".$loopid.">")-2-strlen($loopid);
    $endat = strpos(" ".$this->template, "</".$loopid.">",$startat)-1;
    if ($endat) {
//     echo $endat."<br>";
     return substr($this->template, $startat, $endat-$startat);
    }
   }
   return $loopid;
  }
  
  function removeloop($loopid) {                                                // used to remove specific loop tags
//   str_replace("<" .$loopid.">","",$this->template);
//   str_replace("</".$loopid.">","",$this->template);
   
   do {
    $needle = "<" .$loopid.">";
    $tagstart = strpos($this->template, $needle);
    if ($tagstart) {
     $this->template = substr($this->template, 0, $tagstart).substr($this->template, $tagstart+strlen($needle));
    }
    
    $needle = "</" .$loopid.">";
    $tagstart = strpos($this->template, $needle);
    if ($tagstart) {
     $this->template = substr($this->template, 0, $tagstart).substr($this->template, $tagstart+strlen($needle));
    }
   } while ($tagstart);
  }
  
  function fillloop($loopid, $loopblock='') {                                      // used to replace the groups of placeholders with actual data
   do {
    $endat = 0;
    $startat = strpos(" ".$this->template, "<".$loopid.">")-1;
    if ($startat>=0) {
//     echo "loopid: ".$loopid."<br>";
//     echo "startat: ".$startat."<br>";
     $endat = strpos(" ".$this->template, "</".$loopid.">")+2+strlen($loopid);
     if ($endat) {
//      echo "endat: ".$endat."<br>";
      $this->template=substr_replace($this->template, $loopblock, $startat, $endat-$startat);
     }
    }
   } while ($endat);
  }
  
  function processfcb($block_fcb_orig='') {
//   $ctltypes  = array('fcb','frb','ccb','combtn','comctl','cinput');
//   $ctltypes  = array('ccb','combtn','cinput','cinput_p','comctl','fcb','frb','ctxt','cinputs','ctxts','sendform','comctl_2s','combtn_t','calendar', 'vbtn', 'frbs', 'comctl_to');
   $ctltypes  = array('fcb','frb','ccb','combtn','comctl','comctl_po','comhref','cinputs','ctxts', 'calendar', 'cinputsp');
   foreach ($ctltypes as $ctltype) {
    $tmpname = "block_".$ctltype."_orig";
    $block_fcb_orig = "";
    
    $pos_start = -1;
    $pos_end   = -1;
    do {
     $pos_start = (int)strpos(" ".$this->template, "<".$ctltype." ", $pos_end+1);
     if ($pos_start>0) {
      if (strlen($this->template)<=$pos_start) {
       $pos_end=-1;
      } else {
       $pos_end=(int)strpos(" ".$this->template, "</".$ctltype.">", $pos_start+1);
      }
      if ($pos_end>0) {
       $params_start = (int)strpos(" ".$this->template," ",$pos_start);
       if ($params_start>0) {
        $params_end = (int)strpos(" ".$this->template,">",$params_start);
        $pos0 = (int)strpos(" ".$this->template,'"',$params_start);
        if ($pos0 && ($pos0 < $params_end)) {
         $pos1 = (int)strpos(" ".$this->template,'"',$params_end);
         
//         $params_end_b = $params_end;
         
//         echo $params_end;
         $params_end = (int)strpos(" ".$this->template,">",$pos1);
         
//         echo htmlspecialchars(substr($this->template,$params_start,$params_end-$params_start));
         
//         echo "-";
//         echo $params_end;
//         echo " | ";
         
         $title = (substr($this->template,$pos0,$pos1-$pos0-1));
//         echo " | ";
         
//         $params_end = $params_end_b;
         
        } else {
         
        }
        
        
        if ($params_end>0) {
         if ($block_fcb_orig=='') {
          if ($this->$tmpname=='') {
           $this->$tmpname=file_get_contents($this->viewroot."block_".$ctltype.".htt");
//           echo $ctltype."<br>";
          }
          $block_fcb_orig=$this->$tmpname;
         }
         
         $block_fcb   = $block_fcb_orig;
         $thistag     = substr($this->template,$params_start,$params_end-$params_start-1);
         
         $inner_start = (int)strpos(" ".$this->template,">",$params_end);
         $inner_end   = (int)strpos(" ".$this->template,"</".$ctltype.">",$inner_start);
         $inner       = substr($this->template,$inner_start,$inner_end-$inner_start-1);
         
         $params = explode(" ",str_replace("'","",str_replace('"','',$thistag)));
         
         $block_fcb = str_replace('%id%'     , $params[0],$block_fcb);
         $block_fcb = str_replace('%caption%', $inner    ,$block_fcb);
         if (
           (($ctltype=="comhref") && ($params[1]==$params[2])) ||
           ((($ctltype=="frb") || ($ctltype=="fcb")) && (($params[1]=='checked') || ($params[1]=='1')))
         ) {
          $block_fcb = str_replace('%ctltype%', $ctltype.'_checked'  ,$block_fcb);
         } else {
          $block_fcb = str_replace('%ctltype%', $ctltype  ,$block_fcb);
         }
         
         $block_fcb  = str_replace( "%groupid%", $params[2], $block_fcb);
         $block_fcb  = str_replace( "%subtype%", $params[3], $block_fcb);
         
         $block_fcb  = str_replace(  '%param0%', $params[0], $block_fcb);
         $block_fcb  = str_replace(  '%param1%', $params[1], $block_fcb);
         $block_fcb  = str_replace(  '%param2%', $params[2], $block_fcb);
         $block_fcb  = str_replace(  '%param3%', $params[3], $block_fcb);
         
         $block_fcb  = str_replace(   "%title%", $title    , $block_fcb);
         $block_fcb  = str_replace( "%checked%", $checked  , $block_fcb);
         
         if ($pos1) {
//          ajax_echo_r (substr($thistag,strpos($thistag,'"')+1,strrpos($thistag,'"')-strpos($thistag,'"')-2));
//          echo " | ";
         }
         
         $total_end = (int)strpos(" ".$this->template,">",$pos_end);
         
         if ($ctltype=='combtn') {
          
//          echo htmlentities($this->template);
         }
         $this->template=substr_replace($this->template, $block_fcb, $pos_start-1, $pos_end-$pos_start+$total_end-$pos_end+1);
         
         $pos_start +=($pos_end-$pos_start-strlen($thistag))+1;
        }
       }
      } else {
       $pos_end=$pos_start+1;
      }
     }
    } while ($pos_start);
   }
//   $this->srctemplate=$this->template;
   
  }
  
  function output() {                    // used to get the results
   return $this->template;
  }
  
  function preview() {
   ajax_echo_r(htmlentities($this->template));
  }
  
  function show() {
   ajax_echo(htmlentities($this->template));
  }
  
  function getVariables() {
   $pos_start = -1;
   $pos_end   = -1;
   
   $ret = Array();
   
   do {
    $pos_start = (int)strpos(" ".$this->template, "%", $pos_end+1);
    if ($pos_start>0) {
     if (strlen($this->template)<=$pos_start) {
      $pos_end=-1;
     } else {
      $pos_end=(int)strpos(" ".$this->template, "%", $pos_start+1);
     }
     
     if ($pos_end==0) {
      $pos_start = 0;
     } else {
      $pos_space = strpos(" ".$this->template, " ", $pos_start+1);
      if (($pos_space<$pos_end) && ($pos_space>$pos_start)) {
 //      echo "alert: ".$pos_start."-".$pos_end."-".$pos_space."-".(substr($this->template,$pos_start,$pos_end-$pos_start-1))."<br>";
       $pos_end = $pos_end-1;
       
      } else {
       if (($pos_end-$pos_start)>30) {
        $pos_end = $pos_start+1;
       } else {
        if ($pos_end>0) {
         $thisitem = (substr($this->template,$pos_start,$pos_end-$pos_start-1));
         if (!in_array($thisitem, $ret)) {
          $ret[] = $thisitem;
         }
        }
       }
      }
     }
    }
   } while ($pos_start);
   
   return $ret;
  }
 }
?>
