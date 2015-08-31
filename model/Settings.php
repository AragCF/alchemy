<?
 class Settings {                                               // the friendly Settins class. it gives us the access to the very useful settings.ini file
  private static $instance;            // to allow only one instance of a class
  private $settings;
  
  public function __construct($filename) {
   $this->settings = parse_ini_file($filename, true);
  }
  
  public static function init() {
   if(! isset(self::$instance)) {
    self::$instance = new Settings("settings.php"); 
   }
   return self::$instance;
  }
  
  public static function init2() {
   if(! isset(self::$instance)) {
    self::$instance = new Settings("../settings.php"); 
   }
   return self::$instance;
  }
  
  public function __get($setting) {
   if(array_key_exists($setting, $this->settings)) {
    return $this->settings[$setting];
   } else {
    foreach($this->settings as $section) {
     if(array_key_exists($setting, $section)) {
      return $section[$setting];
     }
    }
   }
  }
 }
?>
