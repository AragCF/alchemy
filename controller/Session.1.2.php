<?
 /* custom session handler class for bricks pro
  revision history:
  
  1.0 2014apr08 20:02 Khimki, Moscow region, Russia
  Initial release
  
  1.1 2014apr08 20:02 Village, Samara region, Russia
  Session data saved after loading thus refreshing the file
  
  1.2 2014sep22 0:54 Village, Samara region, Russia
  Major corrections made to the storing and the loading algorhytms. Bugfix: Session data is never lost.
  
 */
 
 class SessionSaveHandler {
  protected $savePath;
  protected $sessionName;
  protected $id;
  
  public function __construct($ssp) {
   $this->savePath = $ssp;
   session_set_save_handler(
    array($this, "open"),
    array($this, "close"),
    array($this, "read"),
    array($this, "write"),
    array($this, "destroy"),
    array($this, "gc")
   );
  }
  
  public function open($savePath, $sessionName) {
//   $this->savePath = $savePath;
   $this->id = session_id();
   $this->sessionName = $sessionName;
   if (!is_dir($this->savePath)) {
    @mkdirr($this->savePath, 0777);
   }
   @chmod($this->savePath, 0777);
//   echo $this->savePath;
   
   if (file_exists($this->savePath.'/sess_'.$this->id)) {
    $sessiondata = file_get_contents ($this->savePath.'/sess_'.$this->id); // open file containing session data
    session_decode($sessiondata); // Decode the session data
   }
   
   return true;
  }
  
  public function close() {
   $rid = rand();
//   addtologEx ('session', $rid.' saving session file '.$this->savePath."/sess_".$this->id);
   
   $data = session_encode();
   if ($data) {
    file_put_contents($this->savePath.'/sess_'.$this->id, $data);
   }
   
//   addtologEx ('session', $rid.' data: '.print_r($data,true));
   
//   echo "close";
   return true;
  }
  
  public function read($id) {
   $rid = rand();
   
//   addtologEx ('session', $rid.' reading session file '.$this->savePath."/sess_".$id);
   
   if (file_exists($this->savePath.'/sess_'.$this->id)) {
    $data = (string)@file_get_contents($this->savePath."/sess_".$id);
//    addtologEx ('session', $rid.' data: '.print_r($data,true));
    
    if (!$data) {
     $data = (string)@file_get_contents($this->savePath."/sess_".$id);
//     addtologEx ('session', $rid.' data: '.print_r($data,true));
    }
    
    if (!$data) {
     $data = (string)@file_get_contents($this->savePath."/sess_".$id);
//     addtologEx ('session', $rid.' data: '.print_r($data,true));
    }
    
    if (!$data) {
     $data = (string)@file_get_contents($this->savePath."/sess_".$id);
//     addtologEx ('session', $rid.' data: '.print_r($data,true));
    }
    
    if (!$data) {
     $data = (string)@file_get_contents($this->savePath."/sess_".$id);
//     addtologEx ('session', $rid.' data: '.print_r($data,true));
    }
    
    if (!$data) {
     $data = (string)@file_get_contents($this->savePath."/sess_".$id);
//     addtologEx ('session', $rid.' data: '.print_r($data,true));
    }
   }
   
//   $data = (string)@file_get_contents($this->savePath."/sess_".$id);
   
//   file_put_contents($this->savePath."/sess_".$id, $data);
   return $data;
  }
  
  public function write($id, $data) {
//   addtologEx ('session', 'writing session file '.$this->savePath."/sess_".$id.": ".print_r($data,true));
   return file_put_contents($this->savePath."/sess_".$id, $data) === false ? false : true;
  }
  
  public function destroy($id) {
   // your code
  }
  
  public function gc($maxlifetime) {
   // your code
  }
 }
 
// new SessionSaveHandler();
 
?>
