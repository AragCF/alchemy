<?
 class Gallery {
  private $db;        // handle the DB handler. oops.
  private $settings;
  
  function __construct($db, $settings) {
   $this->db       = $db;
   $this->settings = $settings;
  }
  
  public function getPhotos($params) {
   $ret = array();
   $sql="
    SELECT   `gallery`.`ID`, `gallery`.`Description`, `gallery`.`DateAdded`, `gallery`.`FileExt`
    FROM     `".$params->objname."` INNER JOIN `gallery` ON `".$params->objname."`.`GalleryID`=`gallery`.`GalleryID`
    WHERE    (`".$params->objname."`.`ID`='".$params->id."')
    ;
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function addPicture($el, $params) {
//   $ret = 0;
   $allowed = array('.gif','.jpg','.png');
   $fileext=strtolower(substr($el->filename, strrpos($el->filename,".")));                             // get original file extension
   $params->fileext = $fileext;
   if (in_array($fileext,$allowed)) {                        // now we will check it and create the thumbnail from big (or maybe small) source image
    $image = new SimpleImage();                              // instantiate image manipulation class
    $image->load($el->tmp_name);                             // load source image
    if ($image->getHeight()>0) {                             // if it's a good image file - proceed
     mkdirr($this->settings->photofolder."/".$params->id);
     $thisid = (int)$this->addGalleryItem($params);
//     echo("ID: ".$thisid);
     $filename    = $this->settings->photofolder."/".$params->id."/".$thisid.$fileext;               // build filename
     $filenamethb = $this->settings->photofolder."/".$params->id."/thb_".$thisid.$fileext;           // build thumbnail filename
     $filenamepre = $this->settings->photofolder."/".$params->id."/pre_".$thisid.$fileext;           // build thumbnail filename
     if (file_exists($filename)   ) unlink($filename   );
     if (file_exists($filenamethb)) unlink($filenamethb);
     if (file_exists($filenamepre)) unlink($filenamepre);
     
     move_uploaded_file($el->tmp_name, $filename)."<br>";
     
     if (($image->getHeight()>$this->settings->previewheight) || ($image->getWidth()>$this->settings->previewwidth)) {
      $image->resizepic(
       $this->settings->previewwidth, 
       $this->settings->previewheight
      );                                                       // resize to height as desired in technical requirements
      $image->save($filenamepre);                              // save resized file to disk
     } else {
      copy($filename, $filenamepre);
     }
     
     echo "Success";
     $image->load($filename);                                  // load source image
     $image->resizepic(
      $this->settings->thumbwidth, 
      $this->settings->thumbheight
     );                                                         // resize to height as desired in technical requirements
     $image->save($filenamethb);                                // save resized file to disk
//     $el->fname = $el->id.$fileext."?e=".rand(0,99999);       // we attach "e=" parameter here to force the web browser to update its cache (otherwise the photo may not be updated)
//     $el->id=$this->db->saveElementData($el);                 // save filename to database (we must do it because of various file extensions possible: GIF, JPG, PNG)
    } else {                                                    // possible attack
     unlink($el->tmp_name);                                     // delete file
    }
    
    
    
//    echo $filename."<br>";
   }
//   echo $ret;
  }
  
  
  public function getLastGalleryItemID($params) {
   $sql="
    SELECT   MAX(`gallery`.`ID`) as `MaxID`
    FROM     `".$params->objname."` INNER JOIN `gallery` ON `".$params->objname."`.`GalleryID`=`gallery`.`ID`
    WHERE    (`".$params->objname."`.`ID`='".$params->id."')
    ;
   ";
   $listitems=$this->db->query($sql);
   return $listitems[0]->MaxID;
  }
  public function addGalleryItem($params) {
   $sql="
    SELECT   `GalleryID`
    FROM     `".$params->objname."`
    WHERE    (`".$params->objname."`.`ID`='".$params->id."') ;
   ";
   $listitems=$this->db->query($sql);                                    // first we get the GalleryID from this object
   $gal_id = (int)$listitems[0]->GalleryID;
//   echo   ("Listitems: ");
//   echo_r ($listitems[0]);
   if (!$gal_id) {                                                        // if the gallery for this object is not created yet
    $sql="
     SELECT   MAX(`gallery`.`GalleryID`) as `MaxGalID`
     FROM     `gallery` ;
    ";
    $listitems=$this->db->query($sql);                                   // get the maximum GalleryID
//    echo ("MaxGalID: ".$listitems[0]->MaxGalID."<br>");
    $gal_id = $listitems[0]->MaxGalID + 1;
    $sql="
     UPDATE `".$params->objname."`
     SET    `GalleryID` = '".($listitems[0]->MaxGalID+1)."'
     WHERE  `".$params->objname."`.`ID` ='".$params->id."' ;
    ";
    $listitems=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
//    echo   ("MaxGalID: ");
//    echo_r ($listitems);
//    echo   ("New gal id: ".$gal_id."<br>");
   }
//   echo   ("gal id: ".$gal_id."<br>");
   $sql="
    INSERT INTO `gallery`
     (`GalleryID`, `FileExt`)
    VALUES
     ('".$gal_id."', '".$params->fileext."') ;
   ";
   $listitems=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
//   echo_r ($listitems);
   return $listitems->lastInsertID;
  }
  
  public function fixPhoto($filename,$filenamepre,$filenamethb) {
   $image = new SimpleImage();                                // instantiate image manipulation class
   $image->load($filenamepre);                                // load source image
   if (($image->getWidth()>$this->settings->previewwidth) || ($image->getHeight()>$this->settings->previewheight)) {
    $image->load($filename);                                  // load source image
    $image->resizepic(
     $this->settings->previewwidth, 
     $this->settings->previewheight
    );                                                        // resize to height as desired in technical requirements
    $image->save($filenamepre);                               // save resized file to disk
   }
   $image->load($filenamethb);                                // load source image
   if (($image->getWidth()>$this->settings->thumbwidth) || ($image->getHeight()>$this->settings->thumbheight)) {
    $image->load($filename);                                  // load source image
    $image->resizepic(
     $this->settings->thumbwidth, 
     $this->settings->thumbheight
    );                                                        // resize to height as desired in technical requirements
    $image->save($filenamethb);                               // save resized file to disk
   }
  }
  
  public function deleteGalleryItem($params) {
//   ajax_echo_r ($params);
   $ret = array();
   $sql="
    SELECT   `gallery`.`ID`, `gallery`.`Description`, `gallery`.`DateAdded`, `gallery`.`FileExt`
    FROM     `".$params->objname."` INNER JOIN `gallery` ON `".$params->objname."`.`GalleryID`=`gallery`.`GalleryID`
    WHERE    (`gallery`.`ID`='".$params->itemid."')
    ;
   ";
   $listitems=$this->db->query($sql);
   
   if ($listitems[0]->ID) {
    $sql="
     DELETE FROM `gallery`
     WHERE
      `gallery`.`ID`='".$params->itemid."'
     ;
    ";
    $r=$this->db->exec($sql);
    
    if ($r->rowsAffected>0) {
     $fname = $this->settings->photofolder."/".$params->id."/".$listitems[0]->ID.$listitems[0]->FileExt;
     if (file_exists($fname)) unlink($fname);
     $fname = $this->settings->photofolder."/".$params->id."/pre_".$listitems[0]->ID.$listitems[0]->FileExt;
     if (file_exists($fname)) unlink($fname);
     $fname = $this->settings->photofolder."/".$params->id."/thb_".$listitems[0]->ID.$listitems[0]->FileExt;
     if (file_exists($fname)) unlink($fname);
    }
   }
  }
  public function getGalleryItem($params) {
//   ajax_echo_r ($params);
   $ret = array();
   $sql="
    SELECT   `gallery`.`ID`, `gallery`.`Description`, `gallery`.`DateAdded`, `gallery`.`FileExt`
    FROM     `".$params->objname."` INNER JOIN `gallery` ON `".$params->objname."`.`GalleryID`=`gallery`.`GalleryID`
    WHERE    (`gallery`.`ID`='".$params->itemid."')
    ;
   ";
   $listitems=$this->db->query($sql);
//   ajax_echo_r($listitems);
   $fname = getrootdir().$this->settings->photofolder."/".$params->id."/".$listitems[0]->ID.$listitems[0]->FileExt;
   return $fname;
  }
 }
?>
