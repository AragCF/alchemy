<?php
 /**
 * Description of StringForge
 *
 * @author Arag
 */
 class StringForge {
  private $settings;  // store the settings here
  private $f;
  function __construct($settings) {               // the constructor function for our class 
   $this->settings   = $settings;
   $fname = "data/dict/ru.dat";
   if (is_readable($fname)) {
    $this->f=file($fname);
//   } else if (is_readable("../ru_lite.dat")) {
//    $this->f=file("../ru_lite.dat");
   } else {
    $text = $fname." not found";
   }
  }
  public function _echo ($text) {
   echo $this->ru_translate($text);
  }
  
  public function insertrulinebreaks ($text) {
   if ($this->f) {
    foreach ($this->f as $fel) {
     $rpl=explode("­",$fel);
     $rpl[0]=trim($rpl[0]);
     $rpl[1]=trim($rpl[1]);
     $text=str_replace($rpl[0].$rpl[1],$rpl[0]."­".$rpl[1],$text);
  //   $text=str_replace($rpl[0].$rpl[1],$rpl[0]."-".$rpl[1],$text);
     
     $rpl[0]=ucfirst($rpl[0]);
     $text=str_replace($rpl[0].$rpl[1],$rpl[0]."­".$rpl[1],$text);
  //   $text=str_replace($rpl[0].$rpl[1],$rpl[0]."-".$rpl[1],$text);
    }
   }
   
   $Ls1 = Array("à", "å", "¸", "è", "î", "ó", "û", "ý", "þ", "ÿ", "À", "Å", "¨", "È", "Î", "Ó", "Û", "Ý", "Þ", "ß");
   $Ls2 = Array("á", "â", "ã", "ä", "æ", "ç", "é", "ê", "ë", "ì", "í", "ï", "ð", "ñ", "ò", "ô", "õ", "ö", "÷", "ø", "ù", "ü", "ú", "Á", "Â", "Ã", "Ä", "Æ", "Ç", "É", "Ê", "Ë", "Ì", "Í", "Ï", "Ð", "Ñ", "Ò", "Ô", "Õ", "Ö", "×", "Ø", "Ù");
   $Ls  = Array("à", "å", "¸", "è", "î", "ó", "û", "ý", "þ", "ÿ", "À", "Å", "¨", "È", "Î", "Ó", "Û", "Ý", "Þ", "ß", "á", "â", "ã", "ä", "æ", "ç", "é", "ê", "ë", "ì", "í", "ï", "ð", "ñ", "ò", "ô", "õ", "ö", "÷", "ø", "ù", "ü", "ú", "Á", "Â", "Ã", "Ä", "Æ", "Ç", "É", "Ê", "Ë", "Ì", "Í", "Ï", "Ð", "Ñ", "Ò", "Ô", "Õ", "Ö", "×", "Ø", "Ù");
   for ($n=0;$n<=(strlen($text)-4);$n++) {
    $char0 = substr($text,$n-1,1);
    $char1 = substr($text,$n  ,1);
    $char2 = substr($text,$n+1,1);
    $char3 = substr($text,$n+2,1);
    $char4 = substr($text,$n+3,1);
    $char5 = substr($text,$n+4,1);
    $char6 = substr($text,$n+5,1);
    
    if (
     ((in_array($char1,$Ls1)) && (in_array($char2,$Ls2)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls1))) ||
     ((in_array($char1,$Ls2)) && (in_array($char2,$Ls1)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls1))) ||
     ((in_array($char1,$Ls2)) && (in_array($char2,$Ls1)) && (in_array($char3,$Ls1)) && (in_array($char4,$Ls2))) || // && (in_array(substr($text,$n+3,1),$Ls))
     ((in_array($char1,$Ls1)) && (in_array($char2,$Ls2)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls1))) ||
     ((in_array($char1,$Ls1)) && (in_array($char2,$Ls1)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls1)) && (in_array($char5,$Ls))) ||
     ((in_array($char1,$Ls1)) && (in_array($char2,$Ls2)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls2)) && (in_array($char5,$Ls1))) || 
     ((in_array($char1,$Ls2)) && (in_array($char2,$Ls1)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls2)) && (in_array($char5,$Ls2)) && (in_array($char6,$Ls1))) ||
     ((in_array($char0,$Ls1)) && (in_array($char1,$Ls2)) && (in_array($char2,$Ls2)) && (in_array($char3,$Ls2)) && (in_array($char4,$Ls2)) && (in_array($char6,$Ls1)))
    ) {
     if (!(($char2=="ñ") && ($char3=="ò") || (($char2=="ò") && ($char3=="â")) || (($char3=="ü")) || (($char3=="ú")))) {
      $text=substr($text,0,$n+2)."­".substr($text,$n+2);
     } else {
      if (in_array(substr($text,$n-1,1),$Ls2)) {
       $text=substr($text,0,$n+1)."­".substr($text,$n+1);
      }
     }
    }
   }
   $text=str_replace("/","/­",$text);
   return $text;
  }
  
  public function prepare ($text) {
//   return $text;
   $search  = array("," , "." , ":" , ";" , "?" , "!!!" , "!!" , "!" , "! ! !", "! !", ". . ." );
   $replace = array(", ", ". ", ": ", "; ", "? ", "!!! ", "!! ", "! ", "!!!"  , "!!" , "..."   );
   $text = str_replace($search, $replace, $text);
   
//   return $this->insertrulinebreaks(str_replace("  "," ",$text));
   return (str_replace("  "," ",$text));
  }
 }
?>