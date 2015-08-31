<?
 class Element {                // the Element class.
  public $id;
  public $title;
  public $message;
  public $filename;
  public $dateadded;
  
  public function __construct($id, $title, $message, $filename, $dateadded) {        // constructor function
   $this->id           = $id;
   $this->title        = $title;
   $this->message      = $message;
   $this->filename     = $filename;
   $this->dateadded    = $dateadded;
  }
 }
?>