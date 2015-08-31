<?
 class Database {
  private $DBH, $STH;                         // handle the database and query handlers
  private $settings;
  
 // mysql_query("SET NAMES cp1251");                                                  // Устанавливаем кодировку для соединения базы данных
  
  function __construct($settings) {
//   echo_r ($settings);
   $this->settings = $settings;
//   $this->openConnection();
  }
  
  public function query($sql,$ret=array()) {                            // execute query
   $this->openConnection();
   if ($this->DBH) {                                       // if we connected
//    $sql = substr_replace($sql,"",(strpos($sql," ;")+2));  // filter SQL code
    $this->STH = "";
    $sql = $this->sqlreplace($sql);
    try {
//     echo ("SQL:--".$sql."--");
     $this->STH = $this->DBH->query($sql);  
    } catch(PDOException $e) {
     if ($settings->displaydberrors!="0") echo "query - Error in SQL: <br>\n".nl2br($sql)."<br>\n".$e->getMessage()."<br>";
     @mkdirr('data/logs');
     file_put_contents('data/logs/PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);  // write to log in case of errors
    }
//    echo_r($this->STH);
    if ($this->STH) {                                // if there's a query results
     $this->STH->setFetchMode(PDO::FETCH_OBJ);       // выбираем режим выборки
//     $ret = array();                                 // initialize the results array
     while($row = $this->STH->fetch()) {             // выводим результат
      $ret[] = $row;                                 // add the record to array
     }
     
     return $ret;         // send array to Model
    }
   }
   $this->closeConnection();
  }
  
  public function query_first($sql) {                      // execute query
   $ret = $this->query($sql);
   return $ret[0];                                    // send array to Model
  }
  
  public function query_assoc($sql,$keyfieldname,$ret=array()) {                            // execute query
   $this->openConnection();
   if ($this->DBH) {                                       // if we connected
//    $sql = substr_replace($sql,"",(strpos($sql," ;")+2));  // filter SQL code
    $this->STH = "";
    $sql = $this->sqlreplace($sql);
    try {
//     echo ("SQL:--".$sql."--");
     $this->STH = $this->DBH->query($sql);  
    } catch(PDOException $e) {
     if ($settings->displaydberrors!="0") echo "query_assoc - Error in SQL: <br>\n".nl2br($sql)."<br>\n".$e->getMessage()."<br>";
     @mkdirr('data/logs');
     file_put_contents('data/logs/PDOErrors.txt', (date("Y-m-d H:i:s"))."\t".$e->getMessage()."\n", FILE_APPEND);  // write to log in case of errors
    }
//    echo_r($this->STH);
    if ($this->STH) {                                // if there's a query results
     $this->STH->setFetchMode(PDO::FETCH_OBJ);       // выбираем режим выборки
//     $ret = array();                                 // initialize the results array
     
     while($row = $this->STH->fetch()) {             // выводим результат
      $ret[$row->$keyfieldname] = $row;                                 // add the record to array
//      echo $row->ID;
     }
     return $ret;         // send array to Model
    }
   }
   $this->closeConnection();
  }
  
  public function query_flat($sql) {                       // execute query
   $this->openConnection();
   if ($this->DBH) {                                       // if we connected
//    $sql = substr_replace($sql,"",(strpos($sql," ;")+2));  // filter SQL code
    $this->STH = "";
    $sql = $this->sqlreplace($sql);
    try {
//     echo ("SQL:--".$sql."--");
     $this->STH = $this->DBH->query($sql);  
    } catch(PDOException $e) {
     if ($settings->displaydberrors!="0") echo "query_flat - Error in SQL: <br>\n".nl2br($sql)."<br>\n".$e->getMessage()."<br>";
     @mkdirr('data/logs');
     file_put_contents('data/logs/PDOErrors.txt', (date("Y-m-d H:i:s"))."\t".$e->getMessage()."\n", FILE_APPEND);  // write to log in case of errors
    }
//    echo_r($this->STH);
    if ($this->STH) {                                // if there's a query results
     $this->STH->setFetchMode(PDO::FETCH_NUM);       // выбираем режим выборки
     $ret = array();                                 // initialize the results array
     
     while($row = $this->STH->fetch()) {             // выводим результат
      $ret[] = $row[0];                              // add the record to array
     }
     
     return $ret;         // send array to Model
    }
   }
   $this->closeConnection();
  }
  
  public function exec($sql) {                            // execute query
   $this->openConnection();
   if ($this->DBH) {                                       // if we connected
    $ret = new stdClass();
    $sql = $this->sqlreplace($sql);
    try {
//     echo ("SQL:--".$sql."--");
//     file_put_contents('data/logs/SQL-exec.txt', $sql."\n", FILE_APPEND);  // write to log in case of errors
     $ret->rowsAffected = $this->DBH->exec($sql);  
    } catch(PDOException $e) {
     if ($settings->displaydberrors!="0") {
      echo "exec - Error in SQL: <br>\n".nl2br($sql)."<br>\n".$e->getMessage()."<br>";
      ajax_echo_r ($e->getMessage()."<br>\n");
      debug_print_backtrace();
     }
     @mkdirr('data/logs');
     file_put_contents('data/logs/PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);  // write to log in case of errors
    }
    $ret->lastInsertID = $this->DBH->lastInsertId();
    return $ret;
   }
   $this->closeConnection();
  }
  
  public function getRecord($table, $id) {
   $sql="
    SELECT  *
    FROM    `".escape($table)."`
    WHERE   `ID`=".escape($id)."
    LIMIT   1
    ;
   ";
   $ret = $this->query($sql);
   return $ret[0];
//   return $this->query($sql);
  }
  
  public function getList($tablename) {
   $sql="
    SELECT  *
    FROM    `".escape($tablename)."`
    ;
   ";
   return $this->query_assoc($sql,'ID');
  }
  
  public function getListAssoc($tablename,$keyfieldname) {
   $sql="
    SELECT   *
    FROM     `".escape($tablename)."`
    ; 
   ";
   $listitems=$this->query_assoc($sql,$keyfieldname);
   return $listitems;
  }
  
  public function getListOrdered($tablename, $orderby) {
   $sql="
    SELECT   *
    FROM     `".escape($tablename)."`
    ORDER BY `".escape($orderby)."`
    ; 
   ";
   $listitems=$this->query($sql);
   return $listitems;
  }
  
  public function getListOrderedFiltered($tablename, $orderby, $fltname, $fltvalue) {
   $sql="
    SELECT   *
    FROM     `".escape($tablename)."`
    WHERE    `".escape($fltname)."`='".escape($fltvalue)."'
    ORDER BY `".escape($orderby)."`
    ; 
   ";
   $listitems=$this->query($sql);
   return $listitems;
  }
  
  public function getListEx($tablename,$auxfilter) {   // this is from Model
   $sql="
    SELECT   *
    FROM     `".escape($tablename)."`
    ".((escape($auxfilter))?"WHERE ".escape($auxfilter):"")."
    ; 
   ";
   $listitems=$this->query($sql);
   return $listitems;
  }
  
  public function getListEx2($tablename,$parentcolumnname="",$parentcolumnid="") {
//   addtolog("Model getListEx begin");
//   echo $parentcolumnname;
//   echo $parentcolumnid;
   if ($parentcolumnname && $parentcolumnid) {
    $auxfilter = "`".escape($parentcolumnname)."`='".escape($parentcolumnid)."'";
   }
   
   $sql="
    SELECT   *
    FROM     `".escape($tablename)."`
    ".(($auxfilter)?"WHERE ".$auxfilter:"")."
    ;
   ";
   
   echo $sql;
   
   return $this->query_assoc($sql,'ID');
//   $listitems=$this->query($sql);
//   addtolog("Model getListEx end");
//   return $listitems;
  }
  
  public function getListEx3($tablename, $auxfilters="") {   // this is from Model
   if ($auxfilters) {
    foreach ($auxfilters as $k=>$v) {
     $sql_where .= "`".escape($k)."`='".escape($v)."', ";
    }
    $sql_where = "WHERE ".substr($sql_set,0,strlen($sql_set)-2);
   }
   
   $sql="
    SELECT   *
    FROM     `".escape($tablename)."`
    ".$sql_where."
    ; 
   ";
   $listitems=$this->query($sql);
   return $listitems;
  }
  
  public function getListByParentID($tablename, $parentname, $parentid) {
   $sql="
    SELECT   `".escape($tablename)."`.*
    FROM     `".escape($tablename)."`
    WHERE    `".escape($parentname)."` = '".escape($parentid)."'
    ; 
   ";
   $listitems=$this->query($sql);
   return $listitems;
  }
  
  
  public function saveRecord($table, $data) {
   if ($data->ID>0) {
    foreach ($data as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `".escape($table)."`
     SET    ".$sql_set."
     WHERE  `ID` = '".escape($data->ID)."'
     ; 
    ";
   } else {
    foreach ($data as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `".$table."`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
   }
   $ret=$this->exec($sql);                                   // save the new GalleryID to the parent object's table
   return $ret;
  }
  
  public function addToLogPDOErrors($sql, $message) {
   @mkdirr('data/logs');
   file_put_contents('data/logs/PDOErrors.txt', date("Y-m-d H:i:s")."\t".$sql."\t".$message."\n", FILE_APPEND);  // write to log in case of errors
  }
  
  public function addItem($json) {
//   $keys   = "`ServerTime`, `SessionID`";
//   $values = "NOW(), '".session_id()."'";
   
//   $json->targettable = "orders";
   foreach ($json as $k=>$v) {
    if ($k!='targettable') {
     if ($keys) $keys.=",";
     $keys.="`".escape($k)."`";
     
     if ($values) $values.=",";
     $values.="'".escape($v)."'";
    }
   }
   
   $sql="
    INSERT INTO `".$json->targettable."`
     (".$keys.")
    VALUES
     (".$values.")
    ;
   ";
   
//   echo $sql;
   
   $ret=$this->exec($sql);
   return $ret;
  }
  
  public function deleteRow($tablename, $id) {
   $sql="
    DELETE FROM `".escape($tablename)."`
    WHERE
     `ID` = '".escape($id)."'
    ; 
   ";
   $ret=$this->exec($sql);
   return $ret;
  }
  
  public function deleteRows($tablename, $columnname, $id) {
   $sql="
    DELETE FROM `".escape($tablename)."`
    WHERE
     `".escape($columnname)."` = '".escape($id)."'
    ; 
   ";
   $ret=$this->exec($sql);
   return $ret;
  }
  
  public function sqlreplace($sql) {
   $sql = str_replace("&#58;", ":", $sql);
   return $sql;
  }
  
  public function openConnection() {
   $this->DBH = "";
   try {
//    ajax_echo_r ($this->settings);
    $this->DBH = new PDO("mysql:host=".$this->settings->dbhost.";dbname=".$this->settings->dbname, $this->settings->dbuser, $this->settings->dbpassword);          // Соединяемся с базой данных
//    ajax_echo_r ($this->DBH);
   } catch(PDOException $e) {                 // если ошибка
    if ($this->settings->displaydberrors!="0") echo $e->getMessage();
   }
   
   if ($this->DBH) {
    $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->STH = $this->DBH->query("SET NAMES 'utf8' ; ");
    $this->STH = $this->DBH->query("SET SQL_BIG_SELECTS=1 ; ");
//    $this->STH = $this->DBH->query("SET lc_time_names = 'ru_RU' ; ");
   }
   
  }
  
  public function closeConnection() {
   $this->DBH = "";
  }
  
 }
?>
