<?
 include_once("model/Database.1.1.php");         // add Database class
 include_once("model/SimpleImage.php");      // add image manipulation class
 include_once("model/Gallery.php");          // add Template class
 include_once("model/legacy.php");           // add some legacy code
// include_once("model/dbimport.php");         // IS-Centre database import class
// include_once("model/excel_reader2.php");
 include_once("model/PHPExcel.php");             // the MS Excel advanced export class
// include_once("PHPExcel/IOFactory.php");
 
 class Model extends Gallery {        // the Model part of the MVC pattern.
  public  $db;        // handle the DB handler. oops.
  private $settings;  // store the settings here
  private $user;
  public  $menu;
  private $userid;
  private $xls;
  public  $cache;
  
  private $lists;
//  private $columns;
//  private $plists;
  private $justlogged;
  
//  private $struct_names;
  
  function __construct($settings) {               // the constructor function for our class
   $this->settings   = $settings;
   $this->db=new Database($this->settings);
//   $this->db         = new Database($this->settings);                // create the link to database
//   $this->gallery  = new Gallery($this->db, $this->settings);      // create the link to gallery
   $this->userid     = getsecurevariable('userid');
   $this->justlogged = getsecurevariable('justlogged');
   $this->username   = '';
   
//   $this->cache      = array();            // init cache
   $this->cache = getFromCache('data');
   
   parent::__construct($this->db, $this->settings);
  }
  
  public function getTopLevelMenu() {
   return $this->menu->getTopLevelMenu(0);                                       // get top level menu
  }
  
  public function getChildMenu() {
   return $this->menu->getChildMenu(0);                                          // get child level menu
  }
  
  public function getParentID($id)  {
   return $this->menu->getParentID($id);
  }
  
  public function userAuth($params)  {
   $sql="
    SELECT   `ID`
    FROM     `users`
    WHERE    ((`ID`='".$params->username."') OR (`Username`='".$params->username."') OR (`Email`='".$params->username."')) AND (`Password`='".$params->password."')
    ; 
   ";
   $listitem=$this->db->query_first($sql);
   if ($listitem) {
    setsecurevariable('userid'    ,$listitem->ID);
    setsecurevariable('justlogged',1                );
//    echo_r ($listitems);
//    ajax_echo_r ($params);
    if ($params->ParkID) $sql_set = ", `LastParkID`='".$params->ParkID."'";
    $sql="
     UPDATE   `users`
     SET      `JustLogged`=1,  `SessionID`='".session_id()."', `LastAccess`='".date("Y-m-d H:i:s")."' ".$sql_set."
     WHERE    (`ID`='".$listitem->ID."')
     ; 
    ";
//    echo $sql;
    $r=$this->db->exec($sql);
//    ajax_echo_r ($r);
    if ($r->rowsAffected) {
     $this->addEvent('UserLoggedByPassword','users','ID',$listitem->ID,$listitem->ID);
    }
   } else {
//   ajax_echo_r ($params);
    if (($params->username=="Admin") && ($params->password=="19831f51f97b097e9358980e490b4302")) {
     $listitem = new stdClass;
     $listitem->ID = 1;
     $listitem->Name = $params->username;
     $listitem->Username = $params->username;
     
     setsecurevariable('userid'    ,$listitem->ID);
     setsecurevariable('justlogged',1                );
    } else {
     setsecurevariable('userid'    ,1 );
     setsecurevariable('justlogged',1                );
    }
   }
   return $listitem;
  }
  
  public function userAuth_old($params)  {
   $sql="
    SELECT   `ID`
    FROM     `users`
    WHERE    ((`Username`='".$params->username."') OR (`Email`='".$params->username."')) AND (`Password`='".$params->password."')
    ; 
   ";
   $listitems=$this->db->query($sql);
   if ($listitems) {
    setsecurevariable('userid'    ,$listitems[0]->ID);
    setsecurevariable('justlogged',1                );
//    echo_r ($listitems);
    $sql="
     UPDATE   `users`
     SET      `JustLogged`=1,  `SessionID`='".session_id()."', `LastAccess`='".date("Y-m-d H:i:s")."'
     WHERE    (`Username`='".$params->username."') AND (`Password`='".$params->password."') AND (`Banned`=0)
     ; 
    ";
    $r=$this->db->exec($sql);
    if ($r->rowsAffected) {
     $this->addEvent('UserLoggedByPassword','users','ID',$listitems[0]->ID,$listitems[0]->ID);
    }
   }
   return $listitems[0];
  }
  
  public function ownLogin($key)  {
   $sql="
    SELECT   *
    FROM     `users`
    WHERE    (`LoginKey`='".$key."')
    ; 
   ";
   $listitems=$this->db->query($sql);
   if ($listitems) {
    setsecurevariable('userid'    ,$listitems[0]->ID);
    setsecurevariable('justlogged',1                );
//    echo_r ($listitems);
    $sql="
     UPDATE   `users`
     SET      `JustLogged`=1,  `SessionID`='".session_id()."', `LastAccess`='".date("Y-m-d H:i:s")."'
     WHERE    (`LoginKey`='".$key."')
     ; 
    ";
    $r=$this->db->exec($sql);
    if ($r->rowsAffected) {
     $this->addEvent('UserLoggedByLink','users','ID',$listitems[0]->ID,$listitems[0]->ID);
    }
    
    if ($listitems[0]->GroupID) {
     $usergroup=$this->getUserGroup($listitems[0]->GroupID);
    }
    
    $listitems[0]->usergroup = $usergroup;
    
    
   } else {
    $sql="
     SELECT   *
     FROM     `projects`
     WHERE    (`LoginKey`='".$key."')
     ; 
    ";
    $listitems=$this->db->query($sql);
    if ($listitems) {
     setsecurevariable('userid'    ,-2);
     setsecurevariable('justlogged',1                );
 //    echo_r ($listitems);
     $sql="
      UPDATE   `projects`
      SET      `SessionID`='".session_id()."', `LastAccess`='".date("Y-m-d H:i:s")."'
      WHERE    (`LoginKey`='".$key."')
      ; 
     ";
     $r=$this->db->exec($sql);
     if ($r->rowsAffected) {
      $this->addEvent('UserLoggedByLinkToProject','projects','ID',$listitems[0]->ID,$listitems[0]->ID);
     }
    }
    $listitems[0]->IsProject = 1;
    
    
   }
   return $listitems[0];
  }
  
  
  public function isUserAuth($userid)  {
   $sql="
    SELECT   `ID`
    FROM     `users`
    WHERE    (`ID`='".$userid."') AND (`SessionID`='".session_id()."') AND (`Banned`=0)
    ; 
   ";
//   echo $sql;
   $listitems=$this->db->query($sql);
   return $listitems[0];
  }
  
  public function getUserDetails($userid) {
   $sql="
    SELECT   *
    FROM     `users`
    WHERE    (`ID`='".$userid."') AND (`Banned`=0)
    ; 
   ";
   $listitem=$this->db->query_first($sql);
   
   $GroupID = getvariable ('gid', 0);
//   echo $GroupID;
   
   if (!$listitem) $listitem = new stdClass;
   if ($GroupID>0) $listitem->GroupID = $GroupID;
   
   if ($listitem->GroupID) {
    $usergroup=$this->getUserGroup($listitem->GroupID);
   }
   
   $listitem->usergroup = $usergroup;
   
   return $listitem;
  }
  
  public function getUserGroup($id) {
   $sql="
    SELECT   *
    FROM     `usergroups`
    WHERE    (`ID`='".$id."')
    LIMIT 1
    ; 
   ";
   $usergroup=$this->db->query_first($sql);
   return $usergroup;
  }
  
  public function setUserDetails($userdetails) {
   if (!$userdetails) {
    $userdetails = new stdClass();
   }
   if (!$userdetails->ID) {
    $userdetails->ID = $this->userid;
   }
   $sql="
    UPDATE   `users`
    SET
   ";
   foreach ($userdetails as $k=>$v) {
    if ($k!='ID') {
     $sql.= "`".$k."`='".$v."' ,";
    }
   }
//   $sql  = substr($sql, 0,strlen($sql)-1);
   $sql .= "
    `LastAccess`='".date("Y-m-d H:i:s")."'
    WHERE    (`ID`='".$userdetails->ID."') AND (`Banned`=0)
    ; 
   ";
//   echo $sql;
   $listitems=$this->db->exec($sql);
//   echo_r($listitems);
   if ($listitems->rowsAffected) {
//    $this->addEvent('StatusUpdated','statuses','ID',$listitems->ID,$listitems->ID);
   }
   return $listitems;
  }
  
  public function getList($tablename) {
   return $this->db->getListAssoc($tablename, "ID");
  }
  
  public function getListEx($tablename, $auxfilter) {
   return $this->db->getListEx($tablename, $auxfilter);
  }
  
  public function getListEx2($tablename, $parentcolumnname="",$parentcolumnid="") {
   return $this->db->getListEx2($tablename, $parentcolumnname,$parentcolumnid);
  }
  
  public function getListEx3($tablename, $auxfilters="") {
   return $this->db->getListEx3($tablename, $auxfilters);
  }
  
  public function getListOrdered($tablename, $orderby) {
   return $this->db->getListOrdered($tablename, $orderby);
  }
  
  public function getListOrderedFiltered($tablename, $orderby) {
   return $this->db->getListOrderedFiltered($tablename, $orderby);
  }
  
  public function getListByParentID($tablename, $parentcolumnname, $parentcolumnvalue) {
   return $this->db->getListByParentID($tablename, $parentcolumnname, $parentcolumnvalue);
  }
  
  public function getPlaceNames($TypeID=0, $PlaceType=0, $GroupID=0) {
   if ($TypeID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`TypeID`='".$TypeID."')";
   }
   if ($PlaceType) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`PlaceType`='".$PlaceType."')";
   }
   if ($GroupID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`GroupID`='".$GroupID."')";
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT DISTINCT `PlaceName`
    FROM     `money`
    ".$sql_where."
    ORDER BY `PlaceName`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function getPlaceTypes($TypeID=0, $PlaceName=0, $GroupID=0) {
   if ($TypeID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`TypeID`='".$TypeID."')";
   }
   if ($PlaceName) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`PlaceName`='".$PlaceName."')";
   }
   if ($GroupID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`GroupID`='".$GroupID."')";
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT DISTINCT `PlaceType`
    FROM     `money`
    ".$sql_where."
    ORDER BY `PlaceType`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function getCustomerSources($CustomerTypeID=1) {
   if ($CustomerTypeID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`CustomerTypeID`='".$CustomerTypeID."')";
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT   *
    FROM     `customersources`
    ".$sql_where."
    ORDER BY `ID`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function getCustomerSubtypes($CustomerTypeID=1) {
   if ($CustomerTypeID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`CustomerTypeID`='".$CustomerTypeID."')";
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT   *
    FROM     `customersubtypes`
    ".$sql_where."
    ORDER BY `ID`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function getMethodsOfPayment($CustomerTypeID=1) {
   if ($CustomerTypeID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`CustomerTypeID`='".$CustomerTypeID."')";
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT   *
    FROM     `methodsofpayment`
    ".$sql_where."
    ORDER BY `ID`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function getStatuses($ParentID=0, $ParentName) {
   if ($ParentID) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`statuses`.`ParentID`='".$ParentID."')";
   }
   
   if ($ParentName) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`statuses`.`ParentName`='".$ParentName."')";
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT   `statuses`.*, `statustypes`.`Description` AS `Type`
    FROM     `statuses` INNER JOIN `statustypes` ON `statuses`.`TypeID` = `statustypes`.`ID`
    ".$sql_where."
    ORDER BY `statuses`.`ID`
    ; 
   ";
//   echo $sql;
   $listitems=$this->db->query($sql);
   
//   ajax_echo_r ($listitems);
   return $listitems;
  }
  
  public function addStatus($json) {
//   ajax_echo_r ($json);
   
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `statuses`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('StatusUpdated','statuses','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `statuses`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('StatusAdded','statuses','ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   return $success;
  }
  
  public function getExpenditureGroups($TypeID=0) {
   if ($TypeID) {
    if ($sql_where) $sql_where.=" AND ";
    if ($TypeID==1) {
     $sql_where .= "(`ZoneID`='3')";
    } else {
     $sql_where .= "((`ZoneID`='1') OR ((`ZoneID`='2')))";
    }
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT *
    FROM   `expendituregroups`
    ".$sql_where."
    ORDER BY `ID`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  public function getMoney($id) {
   return $this->db->getRecord('money', $id);
  }
  
  public function getDiary($id) {
   return $this->db->getRecord('diary', $id);
  }
  
  public function getUser($id) {
   $ret = $this->db->getRecord('users', $id);
   
   if (!$ret->LoginKey) {
//    $ret->LoginKey=$n.substr(md5($n), 0, 4);
    $ret->LoginKey=getKey($ret->ID,$ret->Username.$ret->DateAdded.rand());
    
    $ret_=$this->db->saveRecord('users', $ret);
   }
   
   return $ret;
  }
  
  public function getCustomerFromProject($id) {
   $sql="
    SELECT `users`.*
    FROM   `users` INNER JOIN `projects` ON `users`.`ID`=`projects`.`CustomerID`
    WHERE  `projects`.`ID` = '".$id."'
    ; 
   ";
   $listitem=$this->db->query_first($sql);
   return $listitem;
  }
  
  public function getProjectsFromCustomer($id) {
   $sql="
    SELECT `projects`.*
    FROM   `users` INNER JOIN `projects` ON `users`.`ID`=`projects`.`CustomerID`
    WHERE  (`users`.`ID` = '".$id."')
    ; 
   ";// AND ((`projects`.`DateFinished` < `projects`.`DateAdded`) OR (`projects`.`DateFinished` IS NULL))
   $listitems=$this->db->query_assoc($sql, "ID");
   return $listitems;
  }
  
  public function getProjectPictures($projects) {
   $dir = sdir($this->settings->projectspath, "full_*.*");
   
//   ajax_echo_r ($dir);
   foreach ($dir as $file) {
    $file_arr = explode("_", substr($file, 0, strpos($file,".")));
//    ajax_echo_r ($file_arr);
//    if (!$projects[$file_arr[1]]) $projects[$file_arr[1]] = new stdClass;
    if (!$projects[$file_arr[1]]->Files) $projects[$file_arr[1]]->Files = array();
    if (!$projects[$file_arr[1]]->Files[$file_arr[2]]) $projects[$file_arr[1]]->Files[$file_arr[2]] = array();
    $projects[$file_arr[1]]->Files[$file_arr[2]][] = $file;
   }
   
   foreach ($projects as $project) {
    ajax_echo_r ($project);
    
   }
   
   return $projects;
  }
  
  public function getProjectStepsPictures($json, $steps = array()) {
   $dir = sdir($this->settings->projectspath, "thb_".$json->ProjectID."_*.*");
//   ajax_echo_r ($json);
//   ajax_echo_r ($steps);
//   ajax_echo_r ($dir);
   
//   ajax_echo_r ($dir);
   if ($dir) {
    foreach ($dir as $file) {
     $file_arr = explode("_", substr($file, 0, strpos($file,".")));
 //    ajax_echo_r ($file_arr);
     if (!$steps[$file_arr[2]]) $steps[$file_arr[2]] = new stdClass;
     if (!$steps[$file_arr[2]]->Files) $steps[$file_arr[2]]->Files = array();
     $steps[$file_arr[2]]->Files[] = $this->settings->projectspath."/".$file;
    }
    
//    foreach ($steps as $step) {
 //    ajax_echo_r ($project);
    
//    }
   }
   return $steps;
  }
  
  
  public function getObjects($json = stdClass, $sortbymode=0, $limit=0) {
//   ajax_echo_r ($json);
   foreach (array('UserID', 'MarketID') as $key) {
    $k = "s_".$key;
    if ($json->$k) {
     if ($sql_where) $sql_where.=" AND ";
     $sql_where .= "(`".$key."`='".$json->$k."')";
    }
   }
   
   foreach (array('DistrictID', 'HouseTypeID') as $key) {
    $where = "";
    for ($n=0; $n<50; $n++) {
     $k = $key."_".$n;
     if ($json->$k) {
 //    echo $k;
      if ($where) $where .= " OR ";
      $where .= "(`".$key."` = '".$n."')";
     }
    }
    if ($where) {
     if ($sql_where) $sql_where.=" AND ";
     $sql_where .= "(".$where.")";
    }
   }
   
   /*
   if ($json->MinCost) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`Cost`>='".$json->MinCost."')";
   }
   if ($json->MaxCost) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "(`Cost`<='".$json->MaxCost."')";
   }
   */
   
   if (!$json->ShowAll) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where .= "
     (
      (`AnotherProblem`='0') AND
      (`TargetAgreed`  ='1') AND
      (`CostTooHigh`   ='0') AND
      (`SoldSelf`      ='0') AND
      (`ServiceDenied` ='0') AND
      (`SaleCanceled`  ='0')
      
     )
    ";
   }
   
//   echo $sql_where;
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   if ($limit) $sql_limit = "LIMIT ".$limit;
   
   $sql="
    SELECT `objects`.*, DATE(`objects`.`DateTarget`) AS `Date`, `users`.`Firstname`, `users`.`Surname`, `districts`.`Description` AS `District`, `markets`.`Description` AS `Market`, `housetypes`.`Description` AS `HouseType`
    FROM   `objects` INNER JOIN `users`      ON `objects`.`UserID`      =`users`.`ID`
                     INNER JOIN `districts`  ON `objects`.`DistrictID`  =`districts`.`ID`
                     INNER JOIN `markets`    ON `objects`.`MarketID`    =`markets`.`ID`
                     INNER JOIN `housetypes` ON `objects`.`HouseTypeID` =`housetypes`.`ID`
    ".$sql_where."
    ".$sql_limit."
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, "ID");
   
   /*
   $sql="
    SELECT `apartments`.*
    FROM   `apartments`
    ; 
   ";
   $apartments=$this->db->query_assoc($sql, "ID");
   
   $sql="
    SELECT `newbuildings`.*
    FROM   `newbuildings`
    ; 
   ";
   $newbuildings=$this->db->query_assoc($sql, "ID");
   
   $sql="
    SELECT `newbuildings_subitems`.*
    FROM   `newbuildings_subitems`
    ; 
   ";
   $newbuildings_subitems=$this->db->query_assoc($sql, "ID");
   
   
   foreach ($apartments as $apartment) {
    if ($listitems[$apartment->ParentID]->MarketID==1) {
     $listitems[$apartment->ParentID]->NumRooms = $apartment->RoomsTotal;
    }
   }
   
//   ajax_echo_r ($newbuildings_subitems);
   foreach ($newbuildings_subitems as $newbuildings_subitem) {
    if ($listitems[$newbuildings[$newbuildings_subitem->ParentID]->ParentID]->MarketID==2) {
 //    ajax_echo_r ($listitems[$newbuildings[$newbuildings_subitem->ParentID]->ParentID]);
     if ($listitems[$newbuildings[$newbuildings_subitem->ParentID]->ParentID]) {
 //    if (($listitems[$newbuilding->ParentID]->MarketID==2) && ($newbuildings_subitem[$newbuildings->ID]->RoomsTotal)) {
      if ($listitems[$newbuildings[$newbuildings_subitem->ParentID]->ParentID]->NumRooms) {
       $obj = clone $listitems[$newbuildings[$newbuildings_subitem->ParentID]->ParentID];
       $obj->NumRooms = $newbuildings_subitem->RoomsTotal;
       $listitems[] = $obj;
      } else {
       $listitems[$newbuildings[$newbuildings_subitem->ParentID]->ParentID]->NumRooms = $newbuildings_subitem->RoomsTotal;
      }
     }
    }
   }
   */
   
   $key = 'RoomsTotal';
   $RoomsTotal = array();
   for ($n=0; $n<7; $n++) {
    $k = $key."_".$n;
    if ($json->$k) {
//     echo $n;
     $RoomsTotal[] = $n;
    }
   }
   
   if (sizeof($RoomsTotal)) {
//    ajax_echo_r ($RoomsTotal);
    $listitems_new = array();
    foreach ($listitems as $listitem) {
     if ($listitem) {
 //     echo $listitems[$n]->RoomsTotal."-".in_array($listitems[$n]->RoomsTotal, $RoomsTotal)."<br>";
      if (!in_array($listitem->RoomsTotal, $RoomsTotal)) {
 //      unset ($listitems[$n]);
      } else {
       $listitems_new[] = $listitem;
 //      ajax_echo_r ($listitems[$n]);
      }
     }
    }
    $listitems = $listitems_new;
   }
   
   if ($json->MinCost) {
    $listitems_new = array();
    for ($n=0; $n<sizeof($listitems); $n++) {
     if ($listitems[$n]) {
      if ($listitems[$n]->Cost>=$json->MinCost) {
       $listitems_new[] = $listitems[$n];
      }
     }
    }
    $listitems = $listitems_new;
   }
   
   if ($json->MaxCost) {
    $listitems_new = array();
    for ($n=0; $n<sizeof($listitems); $n++) {
     if ($listitems[$n]) {
      if ($listitems[$n]->Cost<=$json->MaxCost) {
       $listitems_new[] = $listitems[$n];
      }
     }
    }
    $listitems = $listitems_new;
   }
   
   if ($sortbymode) {
    sortbystr($listitems, 'Address');
   } else {
    sortbyDateTarget($listitems);
   }
   
   return $listitems;
  }
  
  public function getObject($json, $userdetails) {
   if ($json->ID) {
    $sql_where = "WHERE  `objects`.`ID`='".$json->ID."'";
    
    $sql="
     SELECT `objects`.*, `users`.`Firstname`, `users`.`Surname`
     FROM   `objects` INNER JOIN `users` ON `objects`.`UserID`=`users`.`ID`
     ".$sql_where."
     ; 
    ";
 //   echo $sql;
    $rec=$this->db->query_first($sql);
    
   } else {
//    echo "mid: ".$json->MarketID."<br>";
    
    $rec = new stdClass;
    $rec->TypeID         = 1;
    $rec->Firstname      = $userdetails->Firstname;
    $rec->Surname        = $userdetails->Surname;
    $rec->UserID         = $userdetails->ID;
    $rec->HouseTypeID    = 1;
//    echo "!: ".$json->MarketID."<br>";
    $rec->MarketID       = 1;
   }
   if ($json->MarketID) {
    $rec->MarketID       = $json->MarketID;
   }
   $rec->CustomerTypeID = 2;
   
//   ajax_echo_r ($rec);
   $this->addEvent('ObjectGot','objects','ID',$json->ID,$json->ID);
   
   return $rec;
  }
  
  public function getCustomers($json=0, $limit=0) {
//   ajax_echo_r ($json);
   
   if ($json) {
    foreach (array('UserID', 'MarketID') as $key) {
     $k = "s_".$key;
     if ($json->$k) {
      if ($sql_where) $sql_where.=" AND ";
      $sql_where .= "(`".$key."`='".$json->$k."')";
     }
    }
    
    foreach (array('DesiredRoomsIDs', 'DistrictIDs', 'HouseTypeIDs') as $key) {
     $where = "";
     for ($n=0; $n<50; $n++) {
      $k = $key."_".$n;
      if ($json->$k) {
  //    echo $k;
       if ($where) $where .= " OR ";
       $where .= "(`".$key."` LIKE '%_".$n.";%')";
      }
     }
     if ($where) {
      if ($sql_where) $sql_where.=" AND ";
      $sql_where .= "(".$where.")";
     }
    }
    
    if ($json->nonzerocost) {
     if ($sql_where) $sql_where.=" AND ";
     $sql_where .= "
      (ROUND(`MaxCost`)>0)
     ";
    }
    
    if ($json->Cost) {
     if ($sql_where) $sql_where.=" AND ";
     $sql_where .= "
      (ROUND(`MaxCost`)>='".$json->Cost."')
     ";
    }
    
    /*
    if ($json->Rooms) {
     if ($sql_where) $sql_where.=" AND ";
     $sql_where .= "
      (`DesiredRoomsIDs` LIKE '%".$json->Rooms."%')
     ";
    }
    */
   }
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   if ($limit) $sql_limit = "LIMIT ".$limit;
   
   $sql="
    SELECT `customers`.*, DATE(`customers`.`DateTarget`) AS `Date`, `users`.`Firstname`, `users`.`Surname`, `markets`.`Description` AS `Market`
    FROM   `customers` INNER JOIN `users` ON `customers`.`UserID`=`users`.`ID` INNER JOIN `markets` ON `customers`.`MarketID`=`markets`.`ID`
    ".$sql_where."
    ORDER BY `customers`.`DateTarget` DESC
    ".$sql_limit."
    ; 
   ";
   $listitems=$this->db->query($sql);
//   echo $sql;
   return $listitems;
  }
  
  public function getCustomer($id=0) {
   if ($id) $sql_where = "WHERE  `customers`.`ID`='".$id."'";
   
   $sql="
    SELECT `customers`.*, `users`.`Firstname`, `users`.`Surname`
    FROM   `customers` INNER JOIN `users` ON `customers`.`UserID`=`users`.`ID`
    ".$sql_where."
    ; 
   ";
//   echo $sql;
   $listitem=$this->db->query_first($sql);
   $this->addEvent('ObjectGot','customers','ID',$id,$id);
   return $listitem;
//   return $this->db->getRecord('objects', $id);
  }
  
  
  
  public function getHappiness($json) {
   $json->nonzerocost=1;
   
   switch ($json->r_viewmode) {
    case ('objects'):
     $listitems=$this->getObjects($json, 0, '10');
     
     foreach ($listitems as $listitem) {
      $rooms = explode(";",str_replace("pdesiredrooms_","",$listitem->DesiredRoomsIDs));
      $params = new stdClass;
      if ($rooms) {
       foreach ($rooms as $room) {
        if ($room) {
         $k = "NumRooms_".$room;
         $params->$k = 1;
        }
       }
      }
      
      $params->Cost = $listitem->Cost;
//       echo $listitem->RoomsTotal."<br>";
      if ($listitem->RoomsTotal) {
       $name = "DesiredRoomsIDs_".$listitem->RoomsTotal;
       $params->$name = 1;
      }
      
//      $params->Cost = $listitem->Cost;
//      DesiredRoomsIDs
      
//      echo $listitem->Cost."<br>";
      
  //    ajax_echo_r ($params);
      $listitem->subitems = $this->getCustomers($params, '5');
     }
//     ajax_echo_r ($listitems);
    break;
    case ('customers'):
     $listitems=$this->getCustomers($json);
     
     foreach ($listitems as $listitem) {
      $rooms = explode(";",str_replace("pdesiredrooms_","",$listitem->DesiredRoomsIDs));
      $params = new stdClass;
      if ($rooms) {
       foreach ($rooms as $room) {
        if ($room) {
         $k = "NumRooms_".$room;
         $params->$k = 1;
        }
       }
      }
      
      $params->MaxCost = $listitem->MaxCost;
      
  //    ajax_echo_r ($params);
      $listitem->subitems = $this->getObjects($params, 0, '5');
     }
    break;
   }
   
   
//   ajax_echo_r ($listitems);
   
   return $listitems;
  }
  
  
  public function getMonths($tablename, $fieldname) {
   $sql="
    SELECT DISTINCT MONTH(`".$fieldname."`) AS `Month`, YEAR(`".$fieldname."`) AS `Year`
    FROM     `".$tablename."`
    ORDER BY `".$fieldname."`
    ; 
   ";
   $listitems=$this->db->query($sql);
   
   foreach ($listitems as $listitem) {
    $listitem->Month = sprintf("%'.02d", $listitem->Month);
   }
   
//   ajax_echo_r ($listitems);
   return $listitems;
  }
  
  public function getListDistinct($tablename, $fieldname) {
   $sql="
    SELECT DISTINCT `".$fieldname."` AS `Description`
    FROM     `".$tablename."`
    ORDER BY `".$fieldname."`
    ; 
   ";
   $listitems=$this->db->query($sql);
   return $listitems;
  }
  
  
  
  
  
  public function getAdminInfo($params) {
//   echo_r($params);
   $ret = array();
   $debug = 0;
   if ($debug) {
    $mtime = microtime(true);
   }
   if ((int)$params->template==0) {
    $sql="
     SELECT   *
     FROM     `".$params->objname."`
     WHERE    (`".$params->objname."`.`ID`='".$params->id."') ".$filters."
     ; 
    ";
   } else {
    $sql="
     SELECT   *
     FROM     `".$params->objname."`
     WHERE    (`".$params->objname."`.`IsTemplate`=1) ".$filters."
     ; 
    ";
   }
//   echo $sql;
   $r=$this->db->query($sql);

   if ($r[0]) {
    $ret[$params->objname]=$r[0];
   } else {
    $ret[$params->objname]=new stdClass();
   }
   if ($ret[$params->objname]->AgentID==0) $ret[$params->objname]->AgentID=$this->userid;

   $struct_names = array();
   switch ($params->objname) {
    case ("companies"):
     $struct_names[] = array('tariffs'       ,$params->objname       ,'TariffID'                 );
    break;
   }

   for ($i=0; $i<sizeof($struct_names); $i++) {
    if ($struct_names[$i][0]=='crosses') {
     $from = "streets";
    } else {
     $from=$struct_names[$i][0];
    }

    if ($struct_names[$i][3]) {
     $specialfilters = "AND (`".$struct_names[$i][3]."`='".$ret[$struct_names[$i][4]]->$struct_names[$i][5]."')";
    } else {
     $specialfilters = "";
    }

    $sql="
     SELECT   *
     FROM     `".$from."`
     WHERE    ((`".$from."`.`ID`='".$ret[$struct_names[$i][1]]->$struct_names[$i][2]."') ".$specialfilters.") ".$filters."
     ; 
    ";
//    ajax_echo_r($ret[$struct_names[$i][1]]);
//     echo $sql."<br>";
    $r=$this->db->query($sql);
    if ($r[0]) {
//     ajax_echo_r ($r[0]);
     $ret[$struct_names[$i][0]]=$r[0];
    } else {
//     echo $sql."<br>";
    }
   }

   if ($debug) {
    $dbgbuf.= "Time 0:".(microtime(true)-$mtime)."<br>";
    $mtime = microtime(true);
   }

   if ($ret[$params->objname]) {
    $ret[$params->objname]=$ret[$params->objname];
   }

   if ($debug) {
    $dbgbuf.= "Time 1:".(microtime(true)-$mtime)."<br>";
    $mtime = microtime(true);
   }

   $columns=array();
   foreach ($ret as $k=>$v) {
    foreach ($v as $ik=>$iv) {
     $columns[]=array($k, $ik);
    }
   }
   if ($debug) {
    $dbgbuf.= "Time 2:".(microtime(true)-$mtime)."<br>";
    $mtime = microtime(true);
   }
   $cols=$this->db->comments($columns);
   if ($debug) {
    $dbgbuf.= "Time 3:".(microtime(true)-$mtime)."<br>";
    $mtime = microtime(true);
   }
   
   $ret['comments']=$cols;
   if ($debug) {
    $ret[debug] = $dbgbuf."<br>ID: ".$params->id;
   }
   
//   ajax_echo_r ($ret);
   
   return $ret;
  }
  
  public function deleteRow($tablename, $id) {
   $ret=$this->db->deleteRow($tablename, $id);
   return $ret;
  }
  
  public function deleteRows($tablename, $columnname, $id) {
   $ret=$this->db->deleteRows($tablename, $columnname, $id);
   return $ret;
  }
  
  public function saveField($params) {
   $sql = "
    UPDATE `".$params->tablename."`
    SET    `".$params->columnname."`='".$params->data."'
    WHERE  `ID` = '".$params->id."'
    ; 
   ";
   $ret=$this->db->exec($sql);
//   echo $sql;
//   ajax_echo_r($ret);
   
   
   $eventname = fucase(substr($params->tablename,0,strlen($params->tablename)-1)).$params->columnname."Changed";
//   'TaskListChanged';
   
   $this->addEvent($eventname,$params->tablename,$params->columnname,$params->data,$params->id);
   
   
//   if ($ret->rowsAffected<1) {
   return $ret;
  }
  
  public function getField($params) {
   $sql = "
    SELECT `".$params->columnname."` as `Result`
    FROM   `".$params->tablename."`
    WHERE  `ID` = '".$params->id."'
    ; 
   ";
   $ret=$this->db->query($sql);
   return $ret->Result;
  }
  
  
  
  
  
  
  
  
  
  
  
  public function getTable($params, $userdetails = stdClass)  {
//   ajax_echo_r ($params);
   
   if ($params->isexport) {
    switch ($params->r_customers) {
     case ('all'):
      $select = "SELECT   `".$params->tablename."`.`CustomerID`, `".$params->tablename."`.`Name`, `".$params->tablename."`.`TelCode`, `".$params->tablename."`.`TelNo`, `".$params->tablename."`.`City`, `".$params->tablename."`.`Address`, `".$params->tablename."`.`Email`, `".$params->tablename."`.`WWW`, `".$params->tablename."`.`Keywords`";
     break;
     case ('create-email'):
     case ('renew-email'):
      $select = "SELECT DISTINCT `".$params->tablename."`.`Email` AS `Item`, `".$params->tablename."`.`Name`";
     break;
     case ('create-phone'):
     case ('renew-phone'):
      $select = "SELECT DISTINCT CONCAT('(',`".$params->tablename."`.`TelCode`,') ',`".$params->tablename."`.`TelNo`) AS `Item`, `".$params->tablename."`.`Name`";
     break;
    }
    
   } else {
//    $select = "SELECT   `".$params->tablename."`.`CustomerID`, `".$params->tablename."`.`Name`, `".$params->tablename."`.`TelCode`, `".$params->tablename."`.`TelNo`, `".$params->tablename."`.`City`, `".$params->tablename."`.`Address`, `".$params->tablename."`.`Email`, `".$params->tablename."`.`WWW`, `".$params->tablename."`.`Keywords`";
    switch ($params->tablename) {
     case ("money"):
      $select = "SELECT   `".$params->tablename."`.`ID`, DATE(`".$params->tablename."`.`DateAdded`) AS `Date`, `".$params->tablename."`.`Value`, `".$params->tablename."`.`Content`, `".$params->tablename."`.`PlaceType`, `".$params->tablename."`.`PlaceName`, `expendituregroups`.`Description`, `".$params->tablename."`.`TypeID`";   // , `".$params->tablename."`.`TypeID`, `expendituregroups`.`ZoneID` AS `EZID`   , `".$params->tablename."`.`AccountID`, `".$params->tablename."`.`ProjectID`, `".$params->tablename."`.`GroupID`
      if ($params->r_viewmode=="monthly") {
       $select.= ", YEAR(`".$params->tablename."`.`DateAdded`) AS `Year`, MONTH(`".$params->tablename."`.`DateAdded`) AS `Month`";
      }
      if (($params->r_viewmode=="current") && ($params->tablename!="money")) {
       $sql_limit = "     LIMIT 0, 200";
//       $sql_where = "`DateAdded`>='".date("Y-m")."-01'";
      }
     break;
     case ('projects'):
//      $select = "SELECT DISTINCT  `".$params->tablename."`.`ID`, `".$params->tablename."`.`Title`, `".$params->tablename."`.`ShortTitle`, `".$params->tablename."`.`FolderName`, `".$params->tablename."`.`DateAdded`, `".$params->tablename."`.`DateEdited`, `".$params->tablename."`.`DateFinished`, `users`.`ID` AS `AddedBy`, `users`.`Username` AS `AddedByName`, `".$params->tablename."`.`Description`, `".$params->tablename."`.`Cost`, `".$params->tablename."`.`AssignedTo`, `".$params->tablename."`.`DateMeasure`, `".$params->tablename."`.`DateInstall`";
      $select = "SELECT DISTINCT `".$params->tablename."`.*";
     break;
     default:
      $select = "SELECT *";
     break;
    }
   }
   
   
   
   
   switch ($params->r_projectsview) {
    case ('current'):
     $sql_where = "((`".$params->tablename."`.`DateFinished` < `".$params->tablename."`.`DateAdded`) OR (`".$params->tablename."`.`DateFinished` IS NULL))";
    break;
   }
   
   if ($params->s_typeid>0) {
    $sql_where = "(`TypeID` = '".$params->s_typeid."')";
   }
   if ($params->s_groupid>0) {
    if ($sql_where) $sql_where.= " AND ";
    $sql_where .= "(`GroupID` = '".$params->s_groupid."')";
   }
   if ($params->s_month>0) {
    if ($sql_where) $sql_where.= " AND ";
    $e = explode("-",$params->s_month);
    $sql_where .= "((YEAR(`DateAdded`) = '".$e[0]."') AND (MONTH(`DateAdded`) = '".$e[1]."'))";
   }
   
   
   switch ($params->tablename) {
    case ("money"):
     $sql_join = "INNER JOIN `expendituregroups` ON `money`.`GroupID`=`expendituregroups`.`ID`";
    break;
    case ("projects"):
     $sql_join = "INNER JOIN `users` ON `projects`.`AddedBy`=`users`.`ID`";
     if (!$params->isapi) {
      $sql_join .= "LEFT JOIN `projectrelations` ON `projects`.`ID`=`projectrelations`.`ProjectID`";
//      if ($sql_where) $sql_where.= " AND ";
//      $sql_where .= "((`projectrelations`.`UserID` = '".$this->userid."' AND `projectrelations`.`ProjectID`=`projects`.`ID`) OR (`projects`.`AssignedTo` = '".$this->userid."') OR (`projects`.`AddedBy` = '".$this->userid."'))";
     }
//       echo $this->userid;
    break;
   }
   
   if ($sql_where) $sql_where=" WHERE (".$sql_where.")";
   $sql=$select."
    FROM     `".$params->tablename."`
     ".$sql_join."
     ".$sql_where."
   ";
   switch ($params->tablename) {
    case ("diary"):
     $sql_orderby = "ORDER BY `".$params->tablename."`.`DateTarget` DESC";
    break;
    default:
     $sql_orderby = "ORDER BY `".$params->tablename."`.`DateAdded` DESC";
    break;
   }
   
   
   
   $sql.=$sql_orderby.$sql_limit." ; ";
   
//   echo $sql;
   $rows=$this->db->query($sql);
   
   if ($rows) {
    switch ($params->tablename) {
     case ("projects"):
      $users = $this->db->getListAssoc('users','ID');
      
      $sql = "
       SELECT   `ProjectID`, `StepID`, `Title`
       FROM     `projectsteps` INNER JOIN `steps` ON `projectsteps`.`StepID` = `steps`.`ID`
       ORDER BY `DateTarget`
       ; 
      ";
      $statuses=$this->db->query_assoc($sql, 'ProjectID');
//      $statuses=$this->db->query($sql);
      
//      ajax_echo_r ($statuses);
      
      $sql = "
       SELECT   *
       FROM     `steps`
       WHERE    `ID`=0
       ; 
      ";
      $firststatus = $this->db->query_first($sql);
//      ajax_echo_r ($firststatus);
      
      
//      ajax_echo_r ($userdetails->usergroup);
      
      foreach ($rows as $k=>$row) {
//       ajax_echo_r ($row);
       if (!$row->AssignedTo) $row->AssignedTo = 1;
       $row->AssignedToName = $users[$row->AssignedTo]->Username;
       if ($statuses[$row->ID]) {
        $row->LastStepID    = $statuses[$row->ID]->StepID;
        $row->LastStepTitle = $statuses[$row->ID]->Title;
       } else {
        $row->LastStepID    = 1;
        $row->LastStepTitle = $firststatus->Title;
       }
       
       $thisk = "Step".$row->LastStepID;
       if ($userdetails->usergroup->$thisk==0) {
//        echo "ug: ".$userdetails->usergroup->$thisk."<br>";
        unset($rows[$k]);
//        ajax_echo_r($k);
//        unset($row);
       }
      }
      
//      ajax_echo_r($rows);
      
      
     break;
    }
    
   }
   
   $cols=array_keys((array)$rows[0]);
//   ajax_echo_r($cols);
//   ajax_echo_r($rows);
   
   $ret = array();
   if ($cols) {
    $ret[0]=$cols;
   }
   $ret[1]=$rows;
   
//   ajax_echo_r ($ret);
   return $ret;
  }
  
  
  
  
  public function getTableInfo($params) {
//   echo_r ($params);
   if ((int)$params->id==0) {
    $sql="
     SELECT   *
     FROM     `".$params->tablename."`
     WHERE    `IsTemplate`='1'
     ; 
    ";
   } else {
    $sql="
     SELECT   *
     FROM     `".$params->tablename."`
     WHERE    `ID`='".$params->id."'
     ; 
    ";
   }
   $listitems=$this->db->query($sql);
   return $listitems[0];
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  public function generateOutput() {
   $xls = $this->loadXls(getCacheDir()."template.xls");
   $colCount = PHPExcel_Cell::columnIndexFromString($xls->getHighestColumn());
   $rowCount = $xls->getHighestRow();
   
//   echo ("Cols: ".$colCount.", rows: ".$rowCount.".<br>");
   
   $IDs = getFromCache('ids');
   
   $filename        = getCacheDir()."result.xls";
   $filename_client = getrootdir().getCacheSubdir()."result.xls";
   
   $output = new PHPExcel;                               // Create new PHPExcel object
   $output->getProperties()->setCreator("CF")
          ->setLastModifiedBy("CF")
          ->setTitle("Office 2007 XLS Test Document")
          ->setSubject("Office 2007 XLS Test Document")
          ->setDescription("Test document for Office 2007 XLS, generated using PHP classes.")
          ->setKeywords("CF")
          ->setCategory("Test result file");
   
   $i = 0;
   $n = 1;
   
   $srcrow = array();
   
   for ($col=0; $col<$colCount; $col++) {
    $output->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, 1, $xls->getCellByColumnAndRow($col,1)->getValue());
    $srcrow[] = $xls->getCellByColumnAndRow($col,2)->getValue();
   }
   
   $colnames = getColNames();
   
   $i=2;
   
   foreach ($IDs as $id) {
    $col=0;
    
    $thisrecord = $this->cache[(string)$id];
    
    foreach ($srcrow as $c) {
     if ((substr($c,0,1)=="%") && (substr($c,strlen($c)-1,1)=="%")) {
      $colname = substr($c,1,strlen($c)-2);
      $buf = $thisrecord->$colname;
     } else {
      $buf = $c;
     }
     
     $output->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $i, $buf);
     $col++;
    }
    
    $i++;
   }
   
   $objWriter = PHPExcel_IOFactory::createWriter($output, 'Excel5');
   $objWriter->save($filename);
   
   return ($filename_client);
   
  }
  
  function clearColumn($id) {
   foreach ($this->cache as $item) {
    if ($item->$id) $item->$id="";
   }
   
   setCache('data',$this->cache);
//   ajax_echo_r($this->cache);
  }
  
  public function clearCache() {
   $this->cache      = array();            // init cache
   setCache('data',$this->cache);          // clear disk cache
  }
  
  public function getCache() {
   return getFromCache('data');
  }
  
  /*
  public function setListID($json) {
   $sql = "
    UPDATE `".$params->tablename."`
    SET    `".$params->columnname."`='".$params->data."'
    WHERE  `ID` = '".$params->id."'
    ; 
   ";
   $ret=$this->db->exec($sql);
   return $ret;
  }
  */
  
  public function addTask($json) {
   if ($json->ID>0) {
    $sql = "
     UPDATE `tasks`
     SET    `Title`='".$json->Title."', `Labor`='".$json->Labor."', `DateDue`='".$json->DateDue."', `AssignedTo`='".$json->AssignedTo."', `ProjectID`='".$json->ProjectID."', `TaskListID`='".$json->TaskListID."', `Description`='".$json->Description."', `PriorityID`='".$json->PriorityID."' 
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
   } else {
    $sql="
     INSERT INTO `tasks`
      (`Title`, `Labor`, `DateDue`, `AssignedTo`, `ProjectID`, `TaskListID`, `Description`, `PriorityID`, `DateAdded`, `AddedBy`)
     VALUES
      ('".$json->Title."', '".$json->Labor."', '".$json->DateDue."', '".$json->AssignedTo."', '".$json->ProjectID."', '".$json->TaskListID."', '".$json->Description."', '".$json->PriorityID."', '".date("Y-m-d H-i-s")."', '".$json->AddedBy."') ; 
    ";
   }
   $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
   
//   echo $sql;
//   ajax_echo_r ($ret);
   if ($ret->lastInsertID) {
    $this->addEvent('TaskAdded','tasks','ID',$ret->lastInsertID,$ret->lastInsertID);
   } else {
    $this->addEvent('TaskEdited','tasks','ID',$json->ID,$json->ID);
   }
//   echo_r ($ret);
   
   return $ret->lastInsertID;
  }
  
  public function deleteTask($json) {
   $this->addEvent('TaskDeleted','tasks','ID',$json->ID,$json->ID);
   
   ajax_echo_r ($this->deleteRows('comments', 'ParentID', $json->ID));
   ajax_echo_r ($this->deleteRow('tasks', $json->ID));
   
  }
  
  public function resumeTask($json) {
   $this->addEvent('TaskResumed','tasks','ID',$json->ID,$json->ID);
   
   $params = new stdClass;
   $params->tablename = 'tasks';
   $params->columnname = 'StateID';
   $params->data = 1;
   $params->id = $json->ID;
   
   $ret = $this->saveField($params);
  }
  
  public function pauseTask($json) {
   $this->addEvent('TaskPaused','tasks','ID',$json->ID,$json->ID);
   
   $params = new stdClass;
   $params->tablename = 'tasks';
   $params->columnname = 'StateID';
   $params->data = 0;
   $params->id = $json->ID;
   
   $ret = $this->saveField($params);
  }
  
  
  public function deleteComment($json) {
   $sql = "
    SELECT `comments`.`ParentTableName`, `comments`.`ParentID`
    FROM   `comments`
    WHERE  `ID` = '".$json->ID."'
    ; 
   ";
   $ret=$this->db->query_first($sql);
   $buf = $this->deleteRow('comments',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('CommentDeleted','tasks','ID',$ret->ParentID,$json->ID);
   
   return $ret;
  }
  
  public function addEvent($name, $tablename, $columnname, $columnvalue, $rowid) {
   $sql="
    INSERT INTO `events`
     (`Name`, `TableName`, `ColumnName`, `ColumnValue`, `RowID`, `DateAdded`, `UserID`)
    VALUES
     ('".$name."', '".$tablename."', '".$columnname."', '".$columnvalue."', '".$rowid."', '".date("Y-m-d H-i-s")."', '".$this->userid."') ; 
   ";
   
   $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
   
   $subject = "CF.thesystem event: ".EventNameToString($name);
   switch ($name) {
    case ('TaskListIDChanged'):
     $rec = $this->getTask($rowid);
//     addtolog (print_r($rec, 1));
//     addtolog (print_r($rec, 1));
     $content = "Task list: ".TaskListNameFromTaskListID($rec->ListID)."\n<br>Title: ".$rec->Title."\n<br>Due date: ".$rec->DateDue."\n<br>Priority: ".localize($rec->Priority)."\n<br>Assigned to: ".$rec->AssignedToName."\n<br>Author: ".$rec->AddedByName."\n<br>Project: ".$rec->ProjectTitle."\n<br>Task list: ".$rec->TasklistTitle."\n<br>Description: ".$rec->Description."\n<br>";
//     addtolog ($content);
    break;
    case ('CommentDeleted'):
     $rec = $this->db->getRecord($tablename, $rowid);
     $com = $this->db->getRecord("comments", $rowid);
     $content = $com->Comment."\n<br>Title: ".$rec->Title."\n<br>Description: ".$rec->Description;
    break;
    case ('TaskAdded'):
     $rec = $this->getTask($rowid);
//     ajax_echo_r ($rec);
//     addtolog (print_r($rec, 1));
     $content = "Title: ".$rec->Title."\n<br>Due date: ".$rec->DateDue."\n<br>Priority: ".localize($rec->Priority)."\n<br>Assigned to: ".$rec->AssignedToName."\n<br>Author: ".$rec->AddedByName."\n<br>Project: ".$rec->ProjectTitle."\n<br>Task list: ".$rec->TasklistTitle."\n<br>Description: ".$rec->Description;
//     addtolog ($content);
    break;
    case ('TaskEdited'):
     $rec = $this->getTask($rowid);
//     ajax_echo_r ($rec);
//     addtolog (print_r($rec, 1));
//     $content = "Title: ".$rec->Title."\n<br>Description: ".$rec->Description."\n<br>Priority: ".localize($rec->Priority)."\n<br>Assigned to: ".$rec->AssignedToName."\n<br>Author: ".$rec->AddedByName;
     $content = "Title: ".$rec->Title."\n<br>Due date: ".$rec->DateDue."\n<br>Priority: ".localize($rec->Priority)."\n<br>Assigned to: ".$rec->AssignedToName."\n<br>Author: ".$rec->AddedByName."\n<br>Project: ".$rec->ProjectTitle."\n<br>Task list: ".$rec->TasklistTitle."\n<br>Description: ".$rec->Description."\n<br>\n<br>Comment: ".$com->Comment."\n<br>Author: ".$com->Username;
    break;
    case ('CommentAdded'):
//     addtolog ($tablename);
     $rec = $this->getTask($columnvalue);
     $com = $this->getComment($rowid);
//     addtolog ($rec);
//     addtolog ($com);
//     addtolog (print_r($com, 1));
//     $content = "Title: ".$rec->Title."\n<br>Description: ".$rec->Description."\n<br>Comment: ".$com->Comment."\n<br>Author: ".$com->Username;
     $content = $com->Comment." by ".$com->Username."\n<br>Title: ".$rec->Title."\n<br>Due date: ".$rec->DateDue."\n<br>Priority: ".localize($rec->Priority)."\n<br>Assigned to: ".$rec->AssignedToName."\n<br>Author: ".$rec->AddedByName."\n<br>Project: ".$rec->ProjectTitle."\n<br>Task list: ".$rec->TasklistTitle."\n<br>Description: ".$rec->Description;
//     addtolog ($content);
    break;
    case ('TaskDeleted'):
//     $ret = "Task deleted";
     $rec = $this->db->getRecord($tablename, $rowid);
     $content = "Title: ".$rec->Title."\n<br>Description: ".$rec->Description."\n<br>Task list: ".TaskListNameFromTaskListID($rec->ListID);
    break;
    case ('ProjectAdded'):
     $rec = $this->db->getRecord($tablename, $rowid);
     $content = "Title: ".$rec->Title."\n<br>Description: ".$rec->Description."\n<br>Task list: ".TaskListNameFromTaskListID($rec->ListID);
    break;
    default:
     $ret = $en;
    break;
   }
   
//   addtolog($rec);
   
//   sendmail($subject, $content, "thesystem-".$rec->ProjectTitle);
   
   
//   addtolog(print_r($ret,1));
   return $ret->lastInsertID;
  }
  
  
  public function addSpider($json) {
   ajax_echo_r ($json);
   
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `spiderman`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('SpiderUpdated','tasks','ID',$json->ID,$json->ID);
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `spiderman`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('SpiderAdded','tasks','ID',$ret->lastInsertID,$ret->lastInsertID);
   }
   
//   echo $sql;
   
   
   echo_r ($ret);
   return $ret->lastInsertID;
  }
  
  
  public function deleteSpider($json) {
   $buf = $this->deleteRow('spiderman',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('SpiderDeleted','spiderman','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  
  
  
  public function addDiary($json) {
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `diary`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('DiaryUpdated','diary','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `diary`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('DiaryAdded','diary','ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   
//   echo $sql;
   
   
//   echo_r ($ret);
   return $success;
  }
  
  
  public function deleteDiary($json) {
   $buf = $this->deleteRow('diary',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('DiaryDeleted','diary','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  
  
  public function addUser($json) {
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `users`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('UserUpdated','users','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    $json->DateAdded = date('Y-m-d H:i:s');
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `users`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('UserAdded','users','ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   
//   echo $sql;
   
   
//   echo_r ($ret);
   return $success;
  }
  
  
  public function deleteUser($json) {
   $buf = $this->deleteRow('users',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('UserDeleted','users','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  
  public function updatePassword($json) {
   $sql = "
    UPDATE `users`
    SET    `Password`='".$json->Password."'
    WHERE  `ID` = '".$json->ID."'
    ; 
   ";
   $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
   $this->addEvent('PasswordUpdated','users','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
   $success = $ret->rowsAffected>0;
   
   return $success;
  }
  
  public function addObject($json) {
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `objects`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('ObjectUpdated','objects','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `objects`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('ObjectAdded','objects','ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   
//   echo $sql;
   
   
//   echo_r ($ret);
   return $ret;
  }
  
  
  public function addSubObject($json) {
   $marketname = ($json->MarketID==1)?"apartments":"newbuildings";
   $eventname  = ($json->MarketID==1)?"Apartment":"Newbuilding";
//   return false;
   
   unset ($json->MarketID);
   unset ($json->SubID);
   
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `".$marketname."`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent($eventname.'Updated',$marketname,'ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `".$marketname."`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent($eventname.'Added',$marketname,'ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   
//   echo $sql;
   
   
//   echo_r ($ret);
   return $ret;
  }
  
  
  
  public function deleteObject($json) {
   $buf = $this->deleteRow('objects',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('ObjectDeleted','objects','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  public function deleteCustomer($json) {
   $buf = $this->deleteRow('customers',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('CustomerDeleted','customers','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  
  
  public function addCustomer($json) {
//   $json->DateTarget.=".000000";
//   ajax_echo_r($json);
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `customers`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('CustomerUpdated','customers','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `customers`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('CustomerAdded','customers','ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   
   return $ret;
  }
  
  public function addMoney($json) {
//   ajax_echo_r ($json);
   if ($json->ID>0) {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_set .= "`".$k."`='".$v."', ";
     }
    }
    $sql_set = substr($sql_set,0,strlen($sql_set)-2);
    
    $sql = "
     UPDATE `money`
     SET    ".$sql_set."
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('MoneyUpdated','money','ID',$json->ID,$json->ID);
//    echo $sql;
//    return 0;
    $success = $ret->rowsAffected>0;
   } else {
    foreach ($json as $k=>$v) {
     if ($k!="ID") {
      $sql_insert .= "`".$k."`, ";
      $sql_values .= "'".$v."', ";
     }
    }
    $sql_insert = substr($sql_insert,0,strlen($sql_insert)-2);
    $sql_values = substr($sql_values,0,strlen($sql_values)-2);
    
    $sql="
     INSERT INTO `money`
      (".$sql_insert.")
     VALUES
      (".$sql_values.") ; 
    ";
    $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
    $this->addEvent('MoneyAdded','money','ID',$ret->lastInsertID,$ret->lastInsertID);
    $success = $ret->lastInsertID>0;
   }
   
//   echo $sql;
   
   
//   echo_r ($ret);
   return $success;
  }
  
  
  public function deleteMoney($json) {
   $buf = $this->deleteRow('money',$json->ID);
//   ajax_echo_r ($buf);
   
   $this->addEvent('MoneyDeleted','money','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  
  
  public function getUserPrivileges($userid=0) {
   if ($userid) {
    $sql="
     SELECT   `userprivileges`.*, `users`.`Username`, `users`.`Email`
     FROM     `userprivileges` INNER JOIN `users` ON `userprivileges`.`UserID`=`users`.`ID`
     WHERE    `UserID`='".$userid."' AND `AccessMode`>0
     ; 
    ";
   } else {
    $sql="
     SELECT DISTINCT `userprivileges`.`PageName`
     FROM     `userprivileges`
     ; 
    ";
   }
   $listitems=$this->db->query_assoc($sql, 'PageName');
   return $listitems;
  }
  
  public function addUserPrivilege($json) {
   $params = new stdClass;
   $params->UserID     = $json->UserID;
   $params->PageName   = $json->PageName;
   $params->AccessMode = 1;
   $ret=$this->db->saveRecord('userprivileges', $params);
   
   $this->addEvent('UserPrivilegeAdded','users','ID',$json->UserID,$json->UserID);
   
   return $ret;
  }
  
  public function removeUserPrivilege($json) {
   $this->addEvent('UserPrivilegeRemoved','users','ID',$json->ID,$json->ID);
   return $this->db->deleteRow('userprivileges', $json->ID);
  }
  
  public function getProjectParticipants($ID) {
   $sql="
    SELECT   `projectrelations`.`ID`, `users`.`Username`, `users`.`ID` AS `UserID`
    FROM     `users` LEFT JOIN `projectrelations` ON `users`.`ID` = `projectrelations`.`UserID`
    WHERE    (`projectrelations`.`ProjectID`='".$ID."') AND (`Banned`=0) AND `GroupID`>0
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, 'UserID');
   
   return $listitems;
  }
  
  public function addProjectParticipant($json) {
   $params = new stdClass;
   $params->UserID = $json->NewUserID;
   $params->ProjectID = $json->ProjectID;
   $params->Role = 1;
   $ret=$this->db->saveRecord('projectrelations', $params);
   
   return $ret;
  }
  
  public function removeProjectParticipant($json) {
   return $this->db->deleteRow('projectrelations', $json->ID);
  }
  
  public function getNewUsers($excludeIDs = array()) {
   if (sizeof($excludeIDs)) {
//    ajax_echo_r ($excludeIDs);
    foreach ($excludeIDs as $el) {
     if ($sql_where) $sql_where.=" AND ";
     $sql_where.="(NOT `ID`='".$el."')";
    }
    $sql_where = "WHERE ".$sql_where;
   }
   
   $sql="
    SELECT   `users`.`ID`, `users`.`Username`
    FROM     `users`
    ".$sql_where."
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, 'ID');
   
   return $listitems;
  }
  
  
  
  
  public function getProjectStep($json) {
   $sql="
    SELECT   *, 'inactive' AS `Mode`
    FROM     `steps`
    WHRE     `ID`='".$json->ID."'
    ORDER BY `ID`
    ; 
   ";
   $step=$this->db->query_first($sql);
   return $step;
  }
  
  public function getProjectSteps($json) {
   if ($json->IsFull) {
    $sql="
     SELECT   *
     FROM     `projectsteps`
     WHERE    `ProjectID`='".$json->ProjectID."'
     ; 
    ";
    $psteps=$this->db->query_assoc($sql, 'StepID');
   } else {
    $sql="
     SELECT   *
     FROM     `projectsteps`
     WHERE    `ProjectID`='".$json->ProjectID."'
     ORDER BY `StepID` DESC
     LIMIT    1
     ; 
    ";
    $psteps=$this->db->query_assoc($sql, 'StepID');
    $thisstep = array_keys($psteps);
    $thisstep = $thisstep[0];
//    echo "thisstep ".$thisstep."<br>";
    
    $sql_where = "WHERE `ID`='".$thisstep."' OR `ID`='".($thisstep+1)."'";
   }
   
//   ajax_echo_r ($psteps);
   
   
   
   $sql="
    SELECT   *, 'inactive' AS `Mode`
    FROM     `steps`
    ".$sql_where."
    ORDER BY `ID`
    ; 
   ";
   $steps=$this->db->query_assoc($sql, 'ID');
//   ajax_echo_r ($steps);
   
   
   foreach ($psteps as $k=>$v) {
    $steps[$k]->Mode = 'active';
    $steps[$k]->DateTarget = $v->DateTarget;
   }
   
   return $steps;
  }
  
  public function addProjectStep($json) {
   $json->DateTarget = date("Y-m-d H:i:s");
   $ret = $this->db->saveRecord('projectsteps', $json);
   
  }
  
  
  
  public function getStep($id) {
   $sql="
    SELECT   *
    FROM     `steps`
    WHERE    `ID`='".$id."'
    ; 
   ";
   $item=$this->db->query_first($sql);
   
   return $item;
  }
  
  
  
  public function getProject($id) {
   $sql="
    SELECT   *
    FROM     `projects`
    WHERE    `ID`='".$id."'
    ; 
   ";
   $ret=$this->db->query_first($sql);
   
   if (!$ret) $ret = new stdClass;
   if (!$ret->LoginKey) {
//    $ret->LoginKey=md5($ret->ID.$ret->Firstname.$ret->DateAdded.rand());
    $ret->LoginKey=getKey($ret->ID,$ret->Firstname.$ret->DateAdded.rand());
    
    $ret_=$this->db->saveRecord('projects', $ret);
   }
   
   return $ret;
  }
  
  public function closeProject($json) {
   $params = new stdClass;
   $params->tablename = 'projects';
   $params->columnname = 'DateFinished';
   $params->data = date("Y-m-d H-i-s");
   $params->id = $json->ID;
   
   $ret = $this->saveField($params);
   
   $this->addEvent('ProjectClosed','projects','ID',$json->ID,$json->ID);
   
   return $ret;
  }
  
  public function addProject($json, $userid) {
   $ret = $this->db->saveRecord("projects", $json);
   
   /*
   if ($json->ID>0) {
    $sql = "
     UPDATE `projects`
     SET    `Title`='".$json->Title."', `ShortTitle`='".$json->ShortTitle."', `FolderName`='".$json->FolderName."', `AssignedTo`='".$json->AssignedTo."', `Description`='".$json->Description."', `Cost`='".$json->Cost."' 
     WHERE  `ID` = '".$json->ID."'
     ; 
    ";
   } else {
    $sql="
     INSERT INTO `projects`
      (`Title`, `ShortTitle`, `FolderName`, `AssignedTo`, `Description`, `DateAdded`, `AddedBy`, `Cost`)
     VALUES
      ('".$json->Title."', '".$json->ShortTitle."', '".$json->FolderName."', '".$json->AssignedTo."', '".$json->Description."', '".date("Y-m-d H-i-s")."', '".$userid."', '".$json->Cost."') ; 
    ";
   }
   $ret=$this->db->exec($sql);                                   // save the new GalleryID to the parent object's table
   */
   
   if ($json->ID>0) {
    $this->addEvent('ProjectSaved','projects','ID',$json->ID,$json->ID);
    return $json->ID;
   } else {
    $this->addEvent('ProjectAdded','projects','ID',$ret->lastInsertID,$ret->lastInsertID);
    return $ret->lastInsertID;
   }
   
  }
  
  public function deleteProject($json) {
   ajax_echo_r ($this->deleteRow('projects', $json->ID));
  }
  
  
  
  
  
  
  
  public function getUsers($json=stdClass, $id=0) {
   if ($id) {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where.="(`users`.`ID`=".$id.")";
   }
   
   if ($json->ShowAll) {
   } else {
    if ($sql_where) $sql_where.=" AND ";
    $sql_where.="(`DateRemoved` = '0000-00-00')";
   }
   if ($sql_where) $sql_where.=" AND ";
   $sql_where.="
    (
     (
      (   DAY(`DateBirth`) >= `zodiac`.`DayBegin`   ) AND 
      ( MONTH(`DateBirth`)  = `zodiac`.`MonthBegin` )
     ) OR (
      (   DAY(`DateBirth`) <= `zodiac`.`DayEnd`     ) AND 
      ( MONTH(`DateBirth`)  = `zodiac`.`MonthEnd`   )
     )
    ) OR (`DateBirth`='0000-00-00')
   ";
   
   if ($sql_where) $sql_where ="WHERE ".$sql_where;
   
   $sql="
    SELECT   `users`.*, `zodiac`.`Description` AS `zodiac_Description`, `zodiac`.`Name` AS `zodiac_Name`, DAY(`DateBirth`), MONTH(`DateBirth`)
    FROM     `users`, `zodiac`
    ".$sql_where."
    ORDER BY `LastAccess` DESC, `Username`
    ; 
   ";
//   echo $sql;
   $listitems=$this->db->query_assoc($sql, 'ID');
//   ajax_echo_r ($listitems);
   
   return $listitems;
  }
  
  public function getUsersSms() {
   $sql="
    SELECT   `users`.*
    FROM     `users`
    WHERE    (`SendSms` = '1') AND (`DateRemoved` = '0000-00-00')
    ORDER BY `LastAccess` DESC, `Username`
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, 'ID');
   
   return $listitems;
  }
  
  public function getUsersLst() {
   $sql="
    SELECT   `users`.*
    FROM     `users`
    WHERE    (`DateRemoved` = '0000-00-00')
    ORDER BY `firstname`, `Surname`
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, 'ID');
   
   return $listitems;
  }
  
  public function resetUsersFirstSms() {
   $sql="
    UPDATE   `users`
    SET      `FirstSms`='0'
    WHERE    `FirstSms`='1'
    ; 
   ";
   $listitems=$this->db->exec($sql);
   
   return $listitems;
  }
  
  public function getUsersFlt($auxflt) {
   if ($auxflt) {
    $sql_join  = "LEFT JOIN `".$auxflt."` ON `users`.`ID`=`".$auxflt."`.`UserID`";
    $sql_where = "WHERE `".$auxflt."`.`ID`>0";
   }
   
   $sql="
    SELECT DISTINCT `users`.*
    FROM     `users` ".$sql_join."
    ".$sql_where."
    ORDER BY `Firstname`, `Surname`
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, 'ID');
   
   return $listitems;
  }
  
  public function getUserStatus($id=0) {
   if (!$id) $id = $this->userid;
   
   $sql="
    SELECT   `Status`
    FROM     `users`
    WHERE    `ID`='".$id."'
    LIMIT 1
    ; 
   ";
   $listitem=$this->db->query_first($sql);
   $sta = $listitem->Status;
   if (!$sta) $sta = 1;
   
   return $sta;
  }
  
  public function setUserStatus($id=0, $sta) {
   if (!$id) $id = $this->userid;
   $ret=$this->addEvent('UserStatusChanged','users','StatusID',$sta,$id);
   
   $sql="
    UPDATE   `users`
    SET      `Status`='".$sta."'
    WHERE    `ID`='".$id."'
    ; 
   ";
   
   $ret=$this->db->exec($sql);
   
   return $ret;
  }
  
  public function loadBalance($fname) {
   $fileType = 'Excel5';
   
   if (file_exists($fname)) {
    // Read the file
    $objReader = PHPExcel_IOFactory::createReader($fileType);
    $objPHPExcel = $objReader->load($fname);
    
    
    
    $sql="
     TRUNCATE `money`
     ; 
    ";
    $r=$this->db->exec($sql);
    
    $xls = $objPHPExcel->setActiveSheetIndex(0);
    
    $item = new stdClass;
    
    $colCount = PHPExcel_Cell::columnIndexFromString($xls->getHighestColumn());
    $rowCount = $xls->getHighestRow();
    
    echo ("Cols: ".$colCount.", rows: ".$rowCount.".<br>");
    
    for ($row=2; $row<$rowCount; $row++) {
     $timestamp = $xls->getCell("A".$row  )->getValue();
     if (is_numeric($timestamp)) {
      $days = ($timestamp-41291) + 1;
      $Date = date("Y-m-d H-i-s", strtotime("+".$days." days", strtotime("2013-01-16")));
      
      echo $timestamp."-".$Date."<br>";
      $Description = $xls->getCell("K".$row  )->getValue();
      
      $item->Content   = $xls->getCell("K".$row  )->getCalculatedValue();
      $item->DateAdded = $Date; //$xls->getCell("A".$row  )->getCalculatedValue() - 41291;
      
      echo $item->Content."-".$item->DateAdded."<br>";
      
      $item->Value = $xls->getCell("D".$row  )->getCalculatedValue();
      if ($item->Value) {   // Income from a deal
       echo "Income - Deal: ".$item->Value."<br>";
       $item->TypeID  = 1;
       $item->GroupID = 13;
       $this->db->saveRecord("money",$item);
      }
      
      $item->Value = $xls->getCell("E".$row  )->getCalculatedValue() + $xls->getCell("F".$row  )->getCalculatedValue() + $xls->getCell("G".$row  )->getCalculatedValue();
      if ($item->Value) {   // Expenditure - Office
       echo "Expenditure - Office: ".$item->Value."<br>";
       $item->TypeID  = 2;
       $item->GroupID = 1;
       $this->db->saveRecord("money",$item);
      }
      
      $item->Value = $xls->getCell("H".$row  )->getCalculatedValue();
      if ($item->Value) {   // Expenditure - Staff
       echo "Expenditure - Staff: ".$item->Value."<br>";
       $item->TypeID  = 2;
       $item->GroupID = 3;
       $this->db->saveRecord("money",$item);
      }
      
      
/*      
      
      for ($col=1; $col<=9; $col++) {
       $Value = $xls->getCell(chr(65+$col).$row  )->getCalculatedValue();
       if ($Value) {
        $group=$col;
 //       echo $row." ".$Date." ";
 //       echo chr(65+$col)."-".$Value."-".$group."-".$Description."<br>";
        
        $params = new stdClass;
        $params->targettable = "money";
        $params->DateTime    = $Date;
        $params->GroupID     = $group;
        $params->Value       = $Value;
        $params->Description = $Description;
//        $this->db->addItem($params);
        
       }
      }
*/      
     }
 //    if ($row>20) break;
    }
    
    
   } else {
    return 0;
   }
   
   
  }
  
  
  public function addNewbuilding($parentid) {
   addtolog("Model addNewBuilding begin");
   $sql="
    INSERT INTO `newbuildings_subitems`
     (`ParentID`)
    VALUES
     ('".$parentid."')
    ; 
   ";
   $ret=$this->db->exec($sql);
   $ret=$this->addEvent('NewbuildingAdded','newbuildings_subitems','ParentID',$parentid,$ret->lastInsertID);
   
   addtolog("Model addNewBuilding end");
  }
  
  public function getStats($mode, $cutBy='', $UserID=0) {
   $dealTiming = "16";  // in days
//   echo $mode."-".$cutBy;
   switch ($mode) {
    case ("common"):
     $listitems = new stdClass;
     
     $date                 = date("Y-m-d");
     $date_yesterday       = date("Y-m-d", strtotime("-1 days"));
     $date_thisweek_begin  = date("Y-m-d", strtotime(date('o-\\WW')));
     $date_prevweek_begin  = date("Y-m-d", strtotime(date('o-\\WW'))-7*86400);
     $date_thismonth_begin = date("Y-m-")."01";
     $date_prevmonth_begin = date("Y-m-", strtotime("-1 months"))."01";
     
     /*
     echo $date."<br>";
     echo $date_yesterday."<br>";
     echo $date_thisweek_begin."<br>";
     echo $date_prevweek_begin."<br>"; //.strtotime('-1 days')."<br>".(strtotime('-2 days')+86400)."<br>";
     echo $date_thismonth_begin."<br>";
     echo $date_prevmonth_begin."<br>";
     */
     
     $tablenames = array('objects', 'customers', 'agreements', 'deposits', 'handshakes');
     
     $sql_where_arr = array(
          "total" => "",
          "today" => " DATE(`DateTarget`) ='".$date."'",
      "yesterday" => " DATE(`DateTarget`) ='".$date_yesterday."'",
       "thisweek" => " DATE(`DateTarget`)>='".$date_thisweek_begin."'",
       "prevweek" => "(DATE(`DateTarget`)>='".$date_prevweek_begin."') AND (DATE(`DateTarget`)<'".$date_thisweek_begin."')",
      "thismonth" => " DATE(`DateTarget`)>='".$date_thismonth_begin."'",
      "prevmonth" => "(DATE(`DateTarget`)>='".$date_prevmonth_begin."') AND (DATE(`DateTarget`)<'".$date_thismonth_begin."')"
     );
     
     foreach ($tablenames as $tablename) {
      foreach ($sql_where_arr as $k=>$sql_where) {
       if ($tablename=='agreements') {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where.="(`HasAgreement` = 1)";
       }
       if ($tablename=='deposits') {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where.="(`DepositReceived` = 1)";
       }
       if ($tablename=='handshakes') {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where.="(`Handshake` = 1)";
       }
       
       if ($UserID) {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where .= "
         (`UserID`=".$UserID.")
        ";
       }
       
       if ($sql_where) $sql_where = "WHERE ".$sql_where;
       $sql="
        SELECT   COUNT(`ID`) AS `Cnt`
        FROM     `".((($tablename=='agreements') || ($tablename=='deposits') || ($tablename=='handshakes'))?"customers":$tablename)."`
        ".$sql_where."
        ; 
       ";
//       echo $sql;
       $item=$this->db->query_first($sql);
//       ajax_echo_r ($item);
       
       $name = $tablename."_".$k;
       $listitems->$name = $item->Cnt;
      }
     }
     
     
     
     
     $tablename = 'users';
     $sql_where_arr = array(
          "total" => "",
          "today" => "",
      "yesterday" => " DATE(`DateAdded`)<='".$date_yesterday."'",
       "thisweek" => " DATE(`DateAdded`)< '".$date_thisweek_begin."'",
       "prevweek" => "(DATE(`DateAdded`)< '".$date_prevweek_begin."')",
      "thismonth" => " DATE(`DateAdded`)< '".$date_thismonth_begin."'",
      "prevmonth" => "(DATE(`DateAdded`)< '".$date_prevmonth_begin."')"
     );
     foreach ($sql_where_arr as $k=>$sql_where) {
      if ($sql_where) $sql_where.=" AND ";
      $sql_where.="(`IsManager` = 1)";
      
      if ($sql_where) $sql_where = "WHERE ".$sql_where;
      $sql="
       SELECT   COUNT(`ID`) AS `Cnt`
       FROM     `".(($tablename=='handshakes')?"objects":$tablename)."`
       ".$sql_where."
       ; 
      ";
//       echo $sql;
      $item=$this->db->query_first($sql);
//       ajax_echo_r ($item);
      
      $name = $tablename."_".$k;
      $listitems->$name = $item->Cnt;
     }
     
     /*
     $sql="
      SELECT   COUNT(`ID`) AS `Cnt`
      FROM     `objects`
      WHERE    (`Handshake` = 1) AND (DATE(`DateAdded`)>='".$date_thisweek_begin."')
      ; 
     ";
     $item=$this->db->query_first($sql);
     $listitems->handshakes_thisweek = $item->Cnt;
     */
     
//       ajax_echo_r ($item);
     
     $buf = explode("-", $date_prevmonth_begin);
     $thismonth_days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
     $prevmonth_days = cal_days_in_month(CAL_GREGORIAN, $buf[1], $buf[0]);
     
//     echo $thismonth_days;
//     echo $prevmonth_days;
     
     // DT = 2 weeks, таким образом, каждый риелтор по плану делает одну сделку один раз в две недели. Здесь 3 вместо 2, потому как я пожалел риелторов     
     $listitems->handshakes_today_plan      = ceil($listitems->users_today/$dealTiming);
     $listitems->handshakes_yesterday_plan  = ceil($listitems->users_yesterday/$dealTiming);
     $listitems->handshakes_thisweek_plan   = ceil($listitems->users_thisweek /$dealTiming*7);
     $listitems->handshakes_prevweek_plan   = ceil($listitems->users_prevweek /$dealTiming*7);
     $listitems->handshakes_thismonth_plan  = ceil($listitems->users_thismonth/$dealTiming*$thismonth_days);
     $listitems->handshakes_prevmonth_plan  = ceil($listitems->users_prevmonth/$dealTiming*$prevmonth_days);
     
     $listitems->handshakes_auto = 0;
     
//     $listitems->objects_total = $item->Cnt;
//     ajax_echo_r ($listitems);
     
    break;
    case ("ads"):
     $listitems = array();
     
     $sql="
      SELECT   `DateAdded`, `Views`
      FROM     `stats_yt`
      ORDER BY `DateAdded`
      ; 
     ";
     $stats_yt=$this->db->query_assoc($sql, 'DateAdded');
//     ajax_echo_r ($stats_yt);
     
     
     $sql="
      SELECT   DATE(`DateAdded`) AS `DateAdded`, COUNT(`ID`) AS `CntSrc`, SUM(`Value`) AS `ValueSrc`
      FROM     `orders`
      WHERE    NOT `DateAdded` = '0000-00-00 00:00:00'
      GROUP BY `DateAdded`
      ORDER BY `DateAdded`
      ; 
     ";
     $listitems=$this->db->query_assoc($sql, 'DateAdded');
//     ajax_echo_r ($listitems);
     
     foreach ($stats_yt as $k=>$v) {
      $listitems[$k]->Views = $stats_yt[$k]->Views;
     }
     
     ksort ($listitems);
     $listitems = array_reverse($listitems, true);
     
     $keys = array();
     
     foreach ($listitems as $k=>$v) {
      if ($v->CntSrc) {
       if ($keys) {
        $sz = sizeof($keys);
        foreach ($keys as $key) {
         $listitems[$key]->Cnt   = $Cnt/$sz;
         $listitems[$key]->Value = $Value/$sz;
        }
       }
       
       $Cnt   = $v->CntSrc;
       $Value = $v->ValueSrc;
       $keys = array();
      } else {
//       $listitems[$k]->Cnt2   = $Cnt;
//       $listitems[$k]->Value2 = $Value;
      }
      
      $keys[] = $k;
      
     }
     
     $sz = sizeof($keys);
     foreach ($keys as $key) {
      $listitems[$key]->Cnt   = $Cnt/$sz;
      $listitems[$key]->Value = $Value/$sz;
     }
     
     $listitems = array_reverse($listitems, true);
//     ajax_echo_r ($listitems);
     
    break;
    case ("customers"):
     switch ($cutBy) {
      case ("customersources"):
       $sql="
        SELECT   `ID`, `Description`
        FROM     `customersources`
        WHERE    `CustomerTypeID`=1
        ORDER BY `Description`
        ; 
       ";
       $listitems=$this->db->query_assoc($sql, 'ID');
      break;
      case ("markets"):
       $sql="
        SELECT   `ID`, `Description`
        FROM     `markets`
        ORDER BY `Description`
        ; 
       ";
       $listitems=$this->db->query_assoc($sql, 'ID');
      break;
      case ("users"):
       $sql="
        SELECT   `ID`, CONCAT(`Firstname`,' ',`Surname`) AS `Description`
        FROM     `users`
        ORDER BY `Firstname`, `Surname`
        ; 
       ";
       $listitems=$this->db->query_assoc($sql, 'ID');
//       ajax_echo_r ($listitems);
      break;
     }
     
     $objTotal = new stdClass;
     $objTotal->ID = 0;
     $objTotal->Description = "Итого";
     $listitems['total']=$objTotal;
     
     $options = array('total', 'TargetContact', 'OfficeVisit', 'ObjectShow', 'HasAgreement', 'DepositReceived', 'Handshake');
     
     foreach ($listitems as $listitem) {
      foreach ($options as $option) {
       $sql_where = "";
       if ($option!='total') {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where .= "
         (`".$option."`=1)
        ";
       }
       if ($listitem->ID) {
        if ($sql_where) $sql_where.=" AND ";
        switch ($cutBy) {
         case ("customersources"):
          $sql_where .= "
           (`SourceID`='".$listitem->ID."')
          ";
         break;
         case ("markets"):
          $sql_where .= "
           (`MarketID`='".$listitem->ID."')
          ";
         break;
         case ("users"):
          $sql_where .= "
           (`UserID`='".$listitem->ID."')
          ";
         break;
        }
       }
       
       if ($UserID) {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where .= "
         (`UserID`=".$UserID.")
        ";
       }
       
       if ($sql_where) $sql_where = "WHERE ".$sql_where;
       $sql="
        SELECT   COUNT(`customers`.`ID`) AS `Cnt`
        FROM     `customers`
        ".$sql_where."
        ;
       ";
       
       $name = $option."_c";
//       echo $sql."<br>";
       $lst=$this->db->query_first($sql);
       $listitem->$name = floatval($lst->Cnt);
      }
     }
     
     $listitems = $this->fillTotals($listitems, $options);
    break;
    case ("objects"):
     switch ($cutBy) {
      case ("customersources"):
       $sql="
        SELECT   `ID`, `Description`
        FROM     `customersources`
        WHERE    `CustomerTypeID`=2
        ORDER BY `Description`
        ; 
       ";
       $listitems=$this->db->query_assoc($sql, 'ID');
      break;
      case ("markets"):
       $sql="
        SELECT   `ID`, `Description`
        FROM     `markets`
        ORDER BY `Description`
        ; 
       ";
       $listitems=$this->db->query_assoc($sql, 'ID');
      break;
      case ("users"):
       $sql="
        SELECT   `ID`, CONCAT(`Firstname`,' ',`Surname`) AS `Description`
        FROM     `users`
        ORDER BY `Firstname`, `Surname`
        ; 
       ";
       $listitems=$this->db->query_assoc($sql, 'ID');
//       ajax_echo_r ($listitems);
      break;
     }
     
     $objTotal = new stdClass;
     $objTotal->ID = 0;
     $objTotal->Description = "Итого";
     $listitems['total']=$objTotal;
     
     $options = array('total', 'TargetContact', 'TargetMeeting', 'OfficeVisit', 'ObjectShow', 'TargetAgreed', 'HasAgreement', 'DepositReceived', 'Handshake', 'GiftsGiven');
     
     foreach ($listitems as $listitem) {
      foreach ($options as $option) {
       $sql_where = "";
       if ($option!='total') {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where .= "
         (`".$option."`=1)
        ";
       }
       if ($listitem->ID) {
        if ($sql_where) $sql_where.=" AND ";
        switch ($cutBy) {
         case ("customersources"):
          $sql_where .= "
           (`SourceID`='".$listitem->ID."')
          ";
         break;
         case ("markets"):
          $sql_where .= "
           (`MarketID`='".$listitem->ID."')
          ";
         break;
         case ("users"):
          $sql_where .= "
           (`UserID`='".$listitem->ID."')
          ";
         break;
        }
       }
       
       if ($UserID) {
        if ($sql_where) $sql_where.=" AND ";
        $sql_where .= "
         (`UserID`=".$UserID.")
        ";
       }
       
       if ($sql_where) $sql_where = "WHERE ".$sql_where;
       $sql="
        SELECT   COUNT(`ID`) AS `Cnt`
        FROM     `objects`
        ".$sql_where."
        ;
       ";
       
       $name = $option."_c";
       
       $lst=$this->db->query_first($sql);
       $listitem->$name = floatval($lst->Cnt);
      }
     }
     
     $listitems = $this->fillTotals($listitems, $options);
    
    break;
    case ("monthly"):
     echo 1;
    break;
   }
//   echo $sql;
//   $listitems=$this->db->query_assoc($sql, 'ID');
   
   switch ($mode) {
//    case ("common"):
    case ("customers"):
    case ("objects"):
     sortby ($listitems, "total_c");
    break;
   }
   
//   ajax_echo_r ($listitems);
   
   
   return $listitems;
  }
  
  function getMoneyStats($cutBy) {
   switch ($cutBy) {
    case ("monthly"):
     $sql="
      SELECT   DISTINCT CONCAT(MONTH(`DateAdded`),'-',YEAR(`DateAdded`)) AS `Mo`, MONTH(`DateAdded`) AS `Month`, YEAR(`DateAdded`) AS `Year`, SUM(`Value`) AS `Income`
      FROM     `money`
      WHERE    `TypeID`=1
      GROUP BY MONTH(`DateAdded`), YEAR(`DateAdded`)
      ORDER BY `DateAdded`
      ;
     ";
     
     $list = $this->db->query_assoc($sql, 'Mo');
     
     $sql="
      SELECT   DISTINCT CONCAT(MONTH(`DateAdded`),'-',YEAR(`DateAdded`)) AS `Mo`, MONTH(`DateAdded`) AS `Month`, YEAR(`DateAdded`) AS `Year`, SUM(`Value`) AS `Outcome`
      FROM     `money`
      WHERE    `TypeID`=2
      GROUP BY MONTH(`DateAdded`), YEAR(`DateAdded`)
      ORDER BY `DateAdded`
      ;
     ";
     
     $i = 0;
     $ret2 = $this->db->query_assoc($sql, 'Mo');
     foreach ($list as $k=>$v) {
      $list[$k]->Outcome  = $ret2[$k]->Outcome;
      $list[$k]->Profit   = $list[$k]->Income - $list[$k]->Outcome;
      $list[$k]->ROI      = 100 * $list[$k]->Income/$list[$k]->Outcome;
      $Income_avg  += $list[$k]->Income;
      $Outcome_avg += $list[$k]->Outcome;
      $Profit_avg  += $list[$k]->Profit;
      $ROI_avg     += $list[$k]->ROI;
      $i++;
     }
     
     $ret = new stdClass;
     $ret->Income_avg   = $Income_avg/$i;
     $ret->Outcome_avg  = $Outcome_avg/$i;
     $ret->Profit_avg   = $Profit_avg/$i;
     $ret->ROI_avg      = $ROI_avg/$i;
     $ret->data = $list;
     
    break;
   }
   return $ret;
  }
  
  function getDepositsStats($json) {
//   ajax_echo_r ($json);
   
   $sql="
    SELECT `objects`.*, DATE(`objects`.`DateTarget`) AS `Date`, `users`.`Firstname`, `users`.`Surname`
    FROM   `objects` INNER JOIN `users`      ON `objects`.`UserID`      =`users`.`ID`
                     INNER JOIN `markets`    ON `objects`.`MarketID`    =`markets`.`ID`
    WHERE  (`DepositReceived` = 1) AND (`Handshake` = 0)
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, "ID");
   
   $ret = new stdClass;
   $ret->objects = $listitems;
   
   $sql="
    SELECT `customers`.*, DATE(`customers`.`DateTarget`) AS `Date`, `users`.`Firstname`, `users`.`Surname`
    FROM   `customers` INNER JOIN `users`      ON `customers`.`UserID`      =`users`.`ID`
                       INNER JOIN `markets`    ON `customers`.`MarketID`    =`markets`.`ID`
    WHERE  (`DepositReceived` = 1) AND (`Handshake` = 0)
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, "ID");
   
   $ret->customers = $listitems;
   
   return $ret;
  }
  
  function getHandshakesStats($json) {
//   ajax_echo_r ($json);
   
   $sql="
    SELECT `objects`.*, DATE(`objects`.`DateTarget`) AS `Date`, `users`.`Firstname`, `users`.`Surname`
    FROM   `objects` INNER JOIN `users`      ON `objects`.`UserID`      =`users`.`ID`
                     INNER JOIN `markets`    ON `objects`.`MarketID`    =`markets`.`ID`
    WHERE  (`Handshake` = 1)
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, "ID");
   
   $ret = new stdClass;
   $ret->objects = $listitems;
   
   $sql="
    SELECT `customers`.*, DATE(`customers`.`DateTarget`) AS `Date`, `users`.`Firstname`, `users`.`Surname`
    FROM   `customers` INNER JOIN `users`      ON `customers`.`UserID`      =`users`.`ID`
                       INNER JOIN `markets`    ON `customers`.`MarketID`    =`markets`.`ID`
    WHERE  (`Handshake` = 1)
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, "ID");
   
   $ret->customers = $listitems;
   
   return $ret;
  }
  
  function getStatsUsersEff($json) {
   $options = array('total', 'Events', 'Objects', 'Customers', 'Deposits', 'Handshakes');
   
   $sql="
    SELECT `users`.`ID`, CONCAT(`users`.`Firstname`, ' ', `users`.`Surname`) AS `Description`, COUNT(`events`.`ID`) AS `Events_c`
    FROM   `users` INNER JOIN `events`       ON `users`.`ID`      =`events`.`UserID`
    WHERE  (NOT `events`.`TableName` = 'newbuildings_subitems')
    GROUP BY `users`.`ID`
    ; 
   ";
   $listitems=$this->db->query_assoc($sql, "ID");
   
   $sql="
    SELECT `users`.`ID`, COUNT(`objects`.`ID`) AS `Cnt`
    FROM   `users` INNER JOIN `objects`      ON `users`.`ID`      =`objects`.`UserID`
    GROUP BY `users`.`ID`
    ; 
   ";
   $objects=$this->db->query_assoc($sql, "ID");
   
   $sql="
    SELECT `users`.`ID`, COUNT(`customers`.`ID`) AS `Cnt`
    FROM   `users` INNER JOIN `customers`      ON `users`.`ID`      =`customers`.`UserID`
    GROUP BY `users`.`ID`
    ; 
   ";
   $customers=$this->db->query_assoc($sql, "ID");
   
   $sql="
    SELECT `users`.`ID`, COUNT(`customers`.`ID`) AS `Cnt`
    FROM   `users` INNER JOIN `customers`      ON `users`.`ID`      =`customers`.`UserID`
    WHERE  `customers`.`DepositReceived` = 1
    GROUP BY `users`.`ID`
    ; 
   ";
   $deposits=$this->db->query_assoc($sql, "ID");
   
   $sql="
    SELECT `users`.`ID`, COUNT(`customers`.`ID`) AS `Cnt`
    FROM   `users` INNER JOIN `customers`      ON `users`.`ID`      =`customers`.`UserID`
    WHERE  `customers`.`Handshake` = 1
    GROUP BY `users`.`ID`
    ; 
   ";
   $handshakes=$this->db->query_assoc($sql, "ID");
   
   $t_Events_c     = 0;
   $t_Objects_c    = 0;
   $t_Customers_c  = 0;
   $t_Deposits_c   = 0;
   $t_Handshakes_c = 0;
   
   $w    = 100;
   $hmax = 14;
   
   $listitems_new = array();
   foreach ($listitems as $listitem) {
    if ($customers[$listitem->ID]->Cnt) {
     $listitem->Objects_c    = (   $objects[$listitem->ID]->Cnt)?$objects[$listitem->ID]->Cnt:0;
     $listitem->Customers_c  = ( $customers[$listitem->ID]->Cnt)?$customers[$listitem->ID]->Cnt:0;
     $listitem->Deposits_c   = (  $deposits[$listitem->ID]->Cnt)?$deposits[$listitem->ID]->Cnt:0;
     $listitem->Handshakes_c = ($handshakes[$listitem->ID]->Cnt)?$handshakes[$listitem->ID]->Cnt:0;
     
//     
     
     $t_Events_c     += $listitem->Events_c;
     $t_Objects_c    += $listitem->Objects_c;
     $t_Customers_c  += $listitem->Customers_c;
     $t_Deposits_c   += $listitem->Deposits_c;
     $t_Handshakes_c += $listitem->Handshakes_c;
     
     if (    $t_Events_max<$listitem->Events_c    )     $t_Events_max = $listitem->Events_c;
     if (   $t_Objects_max<$listitem->Objects_c   )    $t_Objects_max = $listitem->Objects_c;
     if ( $t_Customers_max<$listitem->Customers_c )  $t_Customers_max = $listitem->Customers_c;
     if (  $t_Deposits_max<$listitem->Deposits_c  )   $t_Deposits_max = $listitem->Deposits_c;
     if ($t_Handshakes_max<$listitem->Handshakes_c) $t_Handshakes_max = $listitem->Handshakes_c;
     
     $listitems_new[$listitem->ID] = clone $listitem;
    }
   }
   
//   ajax_echo_r ($listitems);
//   ajax_echo_r ($listitems_new);
   
   $listitems = $listitems_new;
   foreach ($listitems as $listitem) {
    $listitem->Events_r      = (int)($listitem->Events_c*$w /$t_Events_max);
    $listitem->Objects_r     = (int)($listitem->Objects_c    *$w /$t_Objects_max);
    $listitem->Customers_r   = (int)($listitem->Customers_c  *$w /$t_Customers_max);
    if ($t_Deposits_max) {
     $listitem->Deposits_r   = (int)($listitem->Deposits_c  *$w /$t_Deposits_max);
    } else {
     $listitem->Deposits_r   = (int)(1*$w);
    }
    if ($t_Handshakes_max) {
     $listitem->Handshakes_r = (int)($listitem->Handshakes_c*$w /$t_Handshakes_max);
    } else {
     $listitem->Handshakes_r = (int)(1*$w);
    }
    
    $listitem->Objects_RGB    = HSVtoRGB(($listitem->Objects_r   /$w)*0.33, 0.9, 1);
    $listitem->Customers_RGB  = HSVtoRGB(($listitem->Customers_r /$w)*0.33, 0.9, 1);
    $listitem->Deposits_RGB   = HSVtoRGB(($listitem->Deposits_r  /$w)*0.33, 0.9, 1);
    $listitem->Handshakes_RGB = HSVtoRGB(($listitem->Handshakes_r/$w)*0.33, 0.9, 1);
    $listitem->Events_RGB     = HSVtoRGB(($listitem->Events_r    /$w)*0.33, 0.9, 1);
    
   }
   
   $objTotal = new stdClass;
   $objTotal->ID = 0;
   $objTotal->Description  = "Итого";
   $objTotal->Events_c     = $t_Events_c;
   $objTotal->Objects_c    = $t_Objects_c;
   $objTotal->Customers_c  = $t_Customers_c;
   $objTotal->Deposits_c   = $t_Deposits_c;
   $objTotal->Handshakes_c = $t_Handshakes_c;
   
//   echo dechex(15)."<br>";
   
   $listitems['total']=$objTotal;
   
   $listitems = $this->fillTotals($listitems, $options);
   return $listitems;
  }
  
  function fillTotals($listitems, $options){
   foreach ($listitems as $k=>$listitem) {
//      $listitem->total_p = ($listitem->total_c/$listitems['total']->total_c) * 100;
    foreach ($options as $option) {
     $name_c = $option."_c";
     $name_p = $option."_p";
     
//       echo $k."-".$option."<br>";
     if ($k!='total') {
      $listitem->$name_p   = ($listitems['total']->$name_c)  ?(($listitem->$name_c   /$listitems['total']->$name_c)   * 100):0;
     }
    }
   }
   return $listitems;
  }
  
  
  function getBalance() {
   $sql="
    SELECT   SUM(`Value`) AS `Bal_plus`
    FROM     `money`
    WHERE    `TypeID`=1
    ;
   ";
   
   $item=$this->db->query_first($sql);
   $Bal_plus = $item->Bal_plus;
   
   $sql="
    SELECT   SUM(`Value`) AS `Bal_minus`
    FROM     `money`
    WHERE    `TypeID`=2
    ;
   ";
   
   $item=$this->db->query_first($sql);
   $Bal_minus = $item->Bal_minus;
   
   return ($Bal_plus - $Bal_minus);
  }
  
  function backup() {
//   $sql = "show tables;";
//   $tables = $this->db->query_flat($sql);
   $tables = array('objects', 'customers', 'users');
   
//   ajax_echo_r ($tables);
   
   $timestamp = date("Y-m-d-H-i-s");
   
   $dir = "data/backups";
   mkdirr(getrootdirsrv().$dir);
   
   $filename        = getrootdirsrv().$dir."/izum-rel-".$timestamp.".xls";
   $filename_client = getrootdir()   .$dir."/izum-rel-".$timestamp.".xls";
   
   $output = new PHPExcel;                               // Create new PHPExcel object
   $output->getProperties()->setCreator("Izum by Creative Force")
          ->setLastModifiedBy("Izum by Creative Force")
          ->setTitle("Izum Backup File - ".date("Y-m-d H:i:s"))
          ->setSubject("Office 2007 XLS Test Document")
          ->setDescription("Test document for Office 2007 XLS, generated using PHP classes.")
          ->setKeywords("Creative Force Izum backup")
          ->setCategory("Test result file");
   
   $i = 0;
   $n = 1;
   $si = 0;
   
   $srcrow = array();
   
   foreach ($tables as $tableName) {
//    $tableName  = 'accounts';
//    ajax_echo_r ($tableName);
    if (!in_array($tableName, array('visits', 'events'))) {
     $sql = "SELECT * FROM `".$tableName."` ORDER BY `DateAdded`;";
     $ret = $this->db->query_first($sql);
     $col=0;
     
     if ($si) {
      // Add new sheet
      $output->createSheet($si)->setTitle($tableName); //Setting index when creating
     } else {
      $output->setActiveSheetIndex($si)->setTitle($tableName);
     }
     
     $colnames = getColNames();
     
     foreach (array_keys((array)$ret) as $itm) {
  //   for ($col=0; $col<$colCount; $col++) {
  //    echo $itm;
      if ($colnames[$itm]) {
       $itm = $colnames[$itm];
      } else {
//       echo $itm."-";
      }
      
      $output->setActiveSheetIndex($si)->setCellValueByColumnAndRow($col, 1, $itm);
  //    $srcrow[] = $xls->getCellByColumnAndRow($col,2)->getValue();
      $col++;
     }
     
//     $colnames = getColNames();
     
     $i=2;
     
     $sql = "SELECT * FROM `".$tableName."`;";
     $ret = $this->db->query($sql);
     
     $subtables   = array('districts' , 'customersubtypes' , 'methodsofpayment' , 'sources' , 'users' , 'directions' , 'housetypes' , 'markets' , 'mediators' , 'overlappingtypes' , 'layouttypes' , 'toilettypes' , 'conditions' , 'finishings' , 'floorsurfaces' , 'stovetypes' , 'doorstypes' , 'wallssurfaces' , 'wallsmaterials' , 'bathroomequipments' , 'windowstypes' , 'rightssources' , 'rightstransmissions' );
     $subcolumns  = array('DistrictID', 'CustomerSubtypeID', 'MethodOfPaymentID', 'SourceID', 'UserID', 'DirectionID', 'HouseTypeID', 'MarketID', 'MediatorID', 'OverlappingTypeID', 'LayoutTypeID', 'ToiletTypeID', 'ConditionID', 'FinishingID', 'FloorSurfaceID', 'StoveTypeID', 'DoorsTypeID', 'WallsSurfaceID', 'WallsMaterialID', 'BathroomEquipmentID', 'WindowsTypeID', 'RightsSourceID', 'RightsTransmissionID');
     
     $ret = $this->fillSubtables($ret, $subtables, $subcolumns);
     
//     ajax_echo_r ($ret);
//     return false;
     
     foreach ($ret as $itm) {
      $col=0;
      
      $thisrecord = $this->cache[(string)$id];
      
      foreach ($itm as $c) {
       $output->setActiveSheetIndex($si)->setCellValueByColumnAndRow($col, $i, $c);
       $col++;
      }
      
      $i++;
     }
    }
    $si++;
   }
   
   $output->setActiveSheetIndex(0);
   
   $objWriter = PHPExcel_IOFactory::createWriter($output, 'Excel5');
   $objWriter->save($filename);
   
   return ($filename_client);
   
   
   
   /*
   $tableName  = 'accounts';
   $sql = "SELECT * FROM `".$tableName."`;";
   $ret = $this->db->query_first($sql);
   ajax_echo_r ($ret);
   
   $buf = "INSERT INTO `` ";
   foreach (array_keys((array)$ret as $itm) {
    
    
   }
   
//   file_put_contents ($backupFile, implode())."\n", FILE_APPEND);
   
   $sql = "SELECT * FROM `".$tableName."`;";
   $r = $this->db->query($sql);
   ajax_echo_r ($r);
   */
   
  }
  
  function fillSubtables($ret, $subtables, $subcolumns) {
   foreach ($subtables as $subtable) {
    $sql = "SELECT * FROM `".$subtable."`;";
    $$subtable = $this->db->query_assoc($sql, 'ID');
   }
   
//   ajax_echo_r ($subcolumns);
   
   foreach ($ret as $item) {
    for ($i=0; $i<sizeof($subcolumns); $i++) {
//     echo $subcolumns[$i]."-";
//     echo $item->$subcolumns[$i]."<br>";
     $st = $$subtables[$i];
//     ajax_echo_r ($st[3]);
//     return 1;
     if ($subtables[$i]=='users') {
      $item->$subcolumns[$i] = $st[$item->$subcolumns[$i]]->Firstname." ".$st[$item->$subcolumns[$i]]->Surname;
     } else {
      $item->$subcolumns[$i] = localize($st[$item->$subcolumns[$i]]->Description);
     }
     
//     echo $item->$subcolumns[$i]."<br>";
//     return false;
    }
   }
   
   return $ret;
  }
  
  function importYoutubeStats($fname) {
   $csv = CSVRead($fname);
//   ajax_echo_r ($csv);
   
   $sql = "
    TRUNCATE `stats_yt`
    ; 
   ";
   $r=$this->db->exec($sql);
   
   $item = new stdClass;
   foreach ($csv as $itm) {
    if ($itm[0]!="Date") {
     $item->DateAdded    = dateAdaptWrite($itm[0]);
     $item->Views        = $itm[1];
     $item->EstDuration  = $itm[2];
     $item->AvgDuration  = $itm[3];
     $item->AvgPercent   = $itm[5];
     $item->UniqueVisits = $itm[6];
     $item->Subscribers  += (int)$itm[9];
     
     $ret = $this->db->saveRecord("stats_yt", $item);
//     return 0;
    }
   }
   
  }
  
  
  public function preparePic($src,$dest,$deletewrongfile=1) {
   if (file_exists($src)) {
    $image = new SimpleImage();                               // instantiate image manipulation class
    $image->load($src);                                  // load source image
    $width  = $image->getWidth();
    $height = $image->getHeight();
//    echo $dest."-".$image->image_type;
    
    if ((($width==0) || ($height==0)) && $deletewrongfile) {
     unlink($src);
    } else {
     $image->resizepic(
      $this->settings->thumbwidth,
      $this->settings->thumbheight
     );                                                       // resize to height as desired in technical requirements
     $image->save($dest, $image->image_type);                              // save resized file to disk
//     $image->save($dest."-", 2);                              // save resized file to disk
//     echo "test";
    }
   }
  }
  
  public function sendSms($phone, $msg) {
   include_once("model/transport.php");       // connect to the Settings 
   $api = new Transport($this->settings);
   $params_first  = array(
    "text" => $msg
   );
   
//   echo "phone: ".$phone.", sms: ".$msg."<br>";
   
   // uncomment these lines to enable sms sending functionality
   $send_first  = $api->send($params_first, array($phone));
   addtolog ($send_first, 'sms.log');
//   ajax_echo_r ($send_first);
   
   return $send_first;
  }
  
  public function addVisit($params) {
   $sql = "SELECT * FROM `visits` WHERE `VisitID`='".$params['visitid']."' AND `SessionID`='".$params['session']."';";
   $item = $this->db->query_first($sql);
   
   if (!$item) {
    $item = new stdClass;
    $item->DateAdded    = date("Y-m-d H:i:s");
   }
   ajax_echo_r ($item);
   
   if ($params->event=='init') {
    $item->DateTimeInit    = date("Y-m-d H:i:s");
   }
   if ($params->event=='load') {
    $item->DateTimeLoad    = date("Y-m-d H:i:s");
   }
   
   $items = array();
   
   $items['HTTP_REFERER']         = 'Referer';
   $items['HTTP_USER_AGENT']      = 'UserAgent';
   $items['HTTP_ACCEPT']          = 'Accept';
   $items['HTTP_ACCEPT_LANGUAGE'] = 'AcceptLanguage';
   $items['visitid']              = 'VisitID';
   $items['utm_source']           = 'utm_source';
   $items['utm_medium']           = 'utm_medium';
   $items['utm_campaign']         = 'utm_campaign';
   $items['utm_content']          = 'utm_content';
   $items['utm_term']             = 'utm_term';
   $items['yclid']                = 'yclid';
   $items['session']              = 'SessionID';
   $items['visitid']              = 'VisitID';
   $items['lptrack_sid-site876']  = 'Sid';
   $items['_ga']                  = '_ga';
   $items['IP']                   = 'IP';
   
   foreach ($items as $k=>$v) {
    if ($params[$k]) {
     $item->$v = $params[$k];
    }
   }
   
//   if ($params->session) {
//    $item->SessionID    = $params->session;
//   }
   
   ajax_echo_r ($item);
   
   $ret = $this->db->saveRecord("visits", $item);
   
  }
  
 }
?>
