<?php
 /*
 * File: SimpleImage.php
 * Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Date: 08/11/06
 * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 * 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 *
 */
 
 class SimpleImage {
  var $image;
  var $image_type;
  
  function load($filename) {                             // used to load the image file
   if (is_file($filename)) {                             // we can proceed if the file exists
    $image_info = getimagesize($filename);
    $this->image_type = $image_info[2];                  // retrieve infos
    if( $this->image_type == IMAGETYPE_JPEG ) {
     $this->image = imagecreatefromjpeg($filename);
    } elseif( $this->image_type == IMAGETYPE_GIF ) {
     $this->image = imagecreatefromgif($filename);
    } elseif( $this->image_type == IMAGETYPE_PNG ) {
     $this->image = imagecreatefrompng($filename);
    }
   } else {
    echo "File not found: ".$filename;                   // display error message if the file is not found
   }
  }
  
  function save($filename, $image_type=IMAGETYPE_JPEG, $compression=98, $permissions=null) {  // used to save the file
   if($image_type == IMAGETYPE_JPEG) {               // we should select the proper method to handle file
    imagejpeg($this->image,$filename,$compression);
   } elseif($image_type == IMAGETYPE_GIF) {
    imagegif($this->image,$filename);         
   } elseif($image_type == IMAGETYPE_PNG) {
    imagepng($this->image,$filename);
   }   
   if( $permissions != null) {
    chmod($filename,$permissions);
   }
  }
  function output($image_type=IMAGETYPE_JPEG) {   // maybe used to display picture (I'm not sure)
   if( $image_type == IMAGETYPE_JPEG ) {
    imagejpeg($this->image);
   } elseif( $image_type == IMAGETYPE_GIF ) {
    imagegif($this->image);         
   } elseif( $image_type == IMAGETYPE_PNG ) {
    imagepng($this->image);
   }   
  }
  function getWidth() {
   return imagesx($this->image);
  }
  function getHeight() {
   return imagesy($this->image);
  }
  function resizeToHeight($height) {           // resizes to height (variable width)
   $ratio = $height / $this->getHeight();
   $width = $this->getWidth() * $ratio;
   $this->resize($width,$height);
  }
  function resizeToWidth($width) {            // resizes to width (variable height)
   $ratio = $width / $this->getWidth();
   $height = $this->getheight() * $ratio;
   $this->resize($width,$height);
  }
  function scale($scale) {                    // resizes proportionally in percent
   $width = $this->getWidth() * $scale/100;
   $height = $this->getheight() * $scale/100; 
   $this->resize($width,$height);
  }
  function resize($width,$height) {           // just resize
   $new_image = imagecreatetruecolor($width, $height);
   imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
   $this->image = $new_image;   
  }      
  
  function resizepic($width, $height) {       // this was used in one of my previous projects to fit the picture to given dimensions
   $thisheight = $this->getHeight();
   $thiswidth  = $this->getWidth();
   $newheight=($thisheight/$thiswidth)*$width;
   if (($newheight)>$height) {
    $this->resizetoHeight($height);
   } else {
    $this->resizetoWidth($width);
   }
  }
  
  function getpicsize() {                     // used to get the formatted picture dimensions
   $height = $this->getHeight();
   $width  = $this->getWidth();
   $ret = new stdClass;
   $ret->width  = $width;
   $ret->height = $height;
   return ($ret);
  }
  
 }
?>
