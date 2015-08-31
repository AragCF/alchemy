<?
 include_once("model/Database.1.1.php");         // add Database class
 include_once("model/converter.php");
 
 $ca = 0;
// include_once ("controller/ZipArchive.php");
 class DBImport {
  public $db;                                        // handle the DB handler. oops.
  public $settings;                                  // used to handle the link to the Settings (settings.ini in an object representation)
  public $NBSAparts;
  
  public function __construct($settings) {           // the constructor function
   $this->settings = $settings;
   $this->db=new Database($this->settings);
   if(!isset($_SESSION)) session_start();
   
   
   
   
   
   
   
  }
  
  public function importtables($thisfolder) {                            // the main sub in our application
   $PHP_EOL = "\r\n";
//  if (file_exists($filename)) unlink ($filename);
   
   $za = new ZipArchive(); 
   $za->open($thisfolder.'/update.zip');
   
//   if ($txt=="update") {
    echo "Files in zip archive: ".$za->numFiles.$PHP_EOL;
    echo "Extracting to ".$thisfolder."".$PHP_EOL;
    $za->extractTo($thisfolder."");
//   } else {
//    echo "Single table mode".$PHP_EOL;
//   }
   
   $tablenames = array(
    'Customers',
    'Orders'
   );
   
   $filenames = array(
    'Customers.dbu',
    'Orders.dbu'
   );
   
   echo "------\r";
   for($i = 0; $i < $za->numFiles; $i++) {
    $stat = $za->statIndex($i);
    if (in_array($stat['name'],$filenames)) {
     $filename=$thisfolder."/".$stat['name'];
     if (file_exists($filename)) echo $filename." extracted successfully".$PHP_EOL;
    }
   }
   
   echo "------\r";
   for($i = 0; $i < sizeof($filenames); $i++) {
    $filename=$thisfolder."/".$filenames[$i];
    if (file_exists($filename)) {
     echo "Reading data from ".$filenames[$i].$PHP_EOL;
     $file = conv(file_get_contents($filename));
     $json = json_decode($file);
     if (sizeof($json)) {
      echo "Importing data (".strlen($file)." Kb)".$PHP_EOL;
      $this->importtable($json, $txt);
     } else {
      echo "JSON decode error ".echo_jerror(json_last_error()).$PHP_EOL;
     }
     echo "------\r";
    } else {
     echo "File not found".$PHP_EOL;
    }
   }
   
 //    ajax_echo_r(json_last_error());
 //    echo_r($json);
   
   if ($za->numFiles) {
    $za->close();
   }
  }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
  
  function importtable($json,$txt) {
   $t = array_keys(get_object_vars($json));
   $t=$t[0];
 //  echo_r ($t);
   $tablespreview = 0; //(1==2) || !(($t=='ApartSell')||($t=='ArendSell')||($t=='RoomsSell'));
   
   global $fieldid;
   $fieldid = array();
   
   $thistable = $json->$t;
 //  ajax_echo_r ($thistable); 
   
   if ($tablespreview) {
    echo "<hr><h3>".$t."</h3>";
   }
   
   
   
   switch ($t) {
    case ('Orders'):
     $thissqltablename = 'orders';
     
 //    echo "tablespreview: ".$tablespreview."<br>";
//     $sql_l_tr  ="DELETE `locationinfo`.* FROM `locationinfo`,`lands`  WHERE (`locationinfo`.`IsTemplate` = 0) AND (`locationinfo`.`ID`=`lands`.`LocationID`) ; ";
     $sql_b_tr  ="DELETE FROM `".$thissqltablename."`   WHERE `SourceID` = 2 ; ";
     
//     $r=$this->db->exec($sql_l_tr);
//     $mar=$r->rowsAffected;
//     echo_log ("lands->locationinfo items deleted: ".$mar);
     
     $r=$this->db->exec($sql_b_tr);
     $mar=$r->rowsAffected;
     echo_log ($thissqltablename." items deleted: ".$mar);
     
     if ($tablespreview) {
      echo "<table border=\"1\" borderColor=\"\" cellpadding=\"0\" cellspacing=\"0\">\n";
      echo "<tr> ";
     }
     
     // -- print field name
     for ($j=0; $j<=sizeof($thistable->fields); $j++) {
      if ($tablespreview) {
       echo "<th  align=\"left\" bgcolor=\"#CCCCCC\" > <font color=\"#990000\"> ";
       echo($thistable->fields[$j]);
       echo "</font> </th>";
      }
      $fieldid[$thistable->fields[$j]] = $j;
     }
     $j=$j-1;
     
     $c  = 0;
     $ca = 0;
     
     $mar_b=0;
     do { // getting data
      $rowid = "row".$c;
      $result = $thistable->$rowid;
      
      $items=array();
      
      $items['DateAdded']        = dateAdaptWrite(_odbc_result($result, 'OrderDate'));
      $items['SourceID']         = 2;
      $items['SrcID']            = _odbc_result($result, 'OrderID'          );
      $items['OrderNo']          = _odbc_result($result, 'OrderNo'          );
      $items['CustomerSrcID']    = _odbc_result($result, 'CustomerID'       );
      $items['Value']            = _odbc_result($result, 'DeclaimValue'     );
      
//      $items['DateCreated']      = convertfield($result, 'ZDATE'                  ,0);
      
      $keys   = "";
      $values = "";
      
      foreach ($items as $k => $v) {
       if ($keys) $keys.=", ";
       $keys.="`".$k."`";
       
       if ($values) $values.=", ";
       $values.="'".$v."'";
      }
      
      $sql_this_b="INSERT INTO `".$thissqltablename."` 
       (
      ".$keys."
       ) VALUES (
      ".$values."
       ) ; 
      ";
      
      $r=$this->db->exec($sql_this_b);
      $lastid_b=$r->lastInsertID;
      if ($r->rowsAffected<1) {
       echo((($tags_enabled)?"<p>":"").$sql_this_b.": ".mysql_error().(($tags_enabled)?"</p>":"\n"));
       break;
      } else {
       $mar_b += 1;
      }
      
      $c++;
      if ($tablespreview) {
       if ($c%2==0)
        echo("<tr bgcolor=\"#d0d0d0\">\n");
       else
        echo "<tr bgcolor=\"#eeeeee\">\n";
      }
      for ($i=1; $i<=sizeof($thistable->fields); $i++) {
       //        $thissql.="'".iconv("windows-1251", "UTF-8", odbc_result($result, $i))."'";
       if ($tablespreview) {
        echo("<td>");
        echo(_odbc_result($result, $i));
        echo "</td>";
       }
       if ($i%$j==0) {
        $nrows+=1; // counting no of rows    
       }
      }
  //    $res=odbc_fetch_row($result);
      if ($tablespreview) {
       echo "</tr>";
      }
     } while ($result);
     $thissql.=";";
     if ($tablespreview) {
      echo "</td> </tr>\n";
      echo "</table >\n<br>";
     }
//     echo (($tags_enabled)?"<p>":"")."locationinfo items added: ".$mar_l.(($tags_enabled)?"</p>":"<br>\r");
     echo (($tags_enabled)?"<p>":"").$thissqltablename." items added: "       .$mar_b.(($tags_enabled)?"</p>":"<br>\r");
     
    break;
    
    case ('Customers'):
     $thissqltablename = 'customers';
     
 //    echo "tablespreview: ".$tablespreview."<br>";
//     $sql_l_tr  ="DELETE `locationinfo`.* FROM `locationinfo`,`lands`  WHERE (`locationinfo`.`IsTemplate` = 0) AND (`locationinfo`.`ID`=`lands`.`LocationID`) ; ";
     $sql_b_tr  ="DELETE FROM `".$thissqltablename."`   WHERE `SourceID` = 2 ; ";
     
//     $r=$this->db->exec($sql_l_tr);
//     $mar=$r->rowsAffected;
//     echo_log ("lands->locationinfo items deleted: ".$mar);
     
     $r=$this->db->exec($sql_b_tr);
     $mar=$r->rowsAffected;
     echo_log ($thissqltablename." items deleted: ".$mar);
     
     if ($tablespreview) {
      echo "<table border=\"1\" borderColor=\"\" cellpadding=\"0\" cellspacing=\"0\">\n";
      echo "<tr> ";
     }
     
     // -- print field name
     for ($j=0; $j<=sizeof($thistable->fields); $j++) {
      if ($tablespreview) {
       echo "<th  align=\"left\" bgcolor=\"#CCCCCC\" > <font color=\"#990000\"> ";
       echo($thistable->fields[$j]);
       echo "</font> </th>";
      }
      $fieldid[$thistable->fields[$j]] = $j;
     }
     $j=$j-1;
     
     $c  = 0;
     $ca = 0;
     
     $mar_b=0;
     do { // getting data
      $rowid = "row".$c;
      $result = $thistable->$rowid;
      
      $items=array();
      
//      $items['DateTarget']       = dateAdaptWrite(_odbc_result($result, 'OrderDate'));
      $items['SourceID']         = 2;
      $items['SrcID']            = _odbc_result($result, 'CustomerID'          );
      $items['Name']             = _odbc_result($result, 'custName'          );
      $items['Country']          = _odbc_result($result, 'custCountry'       );
      $items['PostalCode']       = _odbc_result($result, 'custPostalCode'     );
      $items['Region']           = _odbc_result($result, 'custRegion'     );
      $items['City']             = _odbc_result($result, 'custCity'     );
      $items['Address']          = _odbc_result($result, 'custAddress'     );
      $items['Email']            = _odbc_result($result, 'custEmail'     );
      $items['Phone']            = _odbc_result($result, 'custPhone'     );
      $items['Metro']            = _odbc_result($result, 'custUndergrSt'     );
      $items['Entrance']         = _odbc_result($result, 'custEntranceNum'     );
      $items['Intercom']         = _odbc_result($result, 'custIntercomCode'     );
      
//      $items['DateCreated']      = convertfield($result, 'ZDATE'                  ,0);
      
      $keys   = "";
      $values = "";
      
      foreach ($items as $k => $v) {
       if ($keys) $keys.=", ";
       $keys.="`".$k."`";
       
       if ($values) $values.=", ";
       $values.="'".$v."'";
      }
      
      $sql_this_b="INSERT INTO `".$thissqltablename."` 
       (
      ".$keys."
       ) VALUES (
      ".$values."
       ) ; 
      ";
      
      $r=$this->db->exec($sql_this_b);
      $lastid_b=$r->lastInsertID;
      if ($r->rowsAffected<1) {
       echo((($tags_enabled)?"<p>":"").$sql_this_b.": ".mysql_error().(($tags_enabled)?"</p>":"\n"));
       break;
      } else {
       $mar_b += 1;
      }
      
      $c++;
      if ($tablespreview) {
       if ($c%2==0)
        echo("<tr bgcolor=\"#d0d0d0\">\n");
       else
        echo "<tr bgcolor=\"#eeeeee\">\n";
      }
      for ($i=1; $i<=sizeof($thistable->fields); $i++) {
       //        $thissql.="'".iconv("windows-1251", "UTF-8", odbc_result($result, $i))."'";
       if ($tablespreview) {
        echo("<td>");
        echo(_odbc_result($result, $i));
        echo "</td>";
       }
       if ($i%$j==0) {
        $nrows+=1; // counting no of rows    
       }
      }
  //    $res=odbc_fetch_row($result);
      if ($tablespreview) {
       echo "</tr>";
      }
     } while ($result);
     $thissql.=";";
     if ($tablespreview) {
      echo "</td> </tr>\n";
      echo "</table >\n<br>";
     }
//     echo (($tags_enabled)?"<p>":"")."locationinfo items added: ".$mar_l.(($tags_enabled)?"</p>":"<br>\r");
     echo (($tags_enabled)?"<p>":"").$thissqltablename." items added: "       .$mar_b.(($tags_enabled)?"</p>":"<br>\r");
     
    break;
    
    
    
    
    case ('Money'):
     $thissqltablename = 'money';
     
 //    echo "tablespreview: ".$tablespreview."<br>";
//     $sql_l_tr  ="DELETE `locationinfo`.* FROM `locationinfo`,`lands`  WHERE (`locationinfo`.`IsTemplate` = 0) AND (`locationinfo`.`ID`=`lands`.`LocationID`) ; ";
     $sql_b_tr  ="DELETE FROM `".$thissqltablename."`   WHERE `SourceID` = 0 ; ";
     
//     $r=$this->db->exec($sql_l_tr);
//     $mar=$r->rowsAffected;
//     echo_log ("lands->locationinfo items deleted: ".$mar);
     
     $r=$this->db->exec($sql_b_tr);
     $mar=$r->rowsAffected;
     echo_log ($thissqltablename." items deleted: ".$mar);
     
     if ($tablespreview) {
      echo "<table border=\"1\" borderColor=\"\" cellpadding=\"0\" cellspacing=\"0\">\n";
      echo "<tr> ";
     }
     
     // -- print field name
     for ($j=0; $j<=sizeof($thistable->fields); $j++) {
      if ($tablespreview) {
       echo "<th  align=\"left\" bgcolor=\"#CCCCCC\" > <font color=\"#990000\"> ";
       echo($thistable->fields[$j]);
       echo "</font> </th>";
      }
      $fieldid[$thistable->fields[$j]] = $j;
     }
     $j=$j-1;
     
     $c  = 0;
     $ca = 0;
     
     $mar_b=0;
     do { // getting data
      $rowid = "row".$c;
      $result = $thistable->$rowid;
      
      $items=array();
      
      $items['DateAdded']        = _odbc_result($result, 'DateTime'         );
      $items['TypeID']           = _odbc_result($result, 'Type'             );
      $items['Value']            = _odbc_result($result, 'Value'            );
      $items['Content']          = _odbc_result($result, 'Content'          );
      $items['ProjectID']        = _odbc_result($result, 'ProjectID'        );
      $items['GroupID']          = _odbc_result($result, 'Group'            );
      $items['PlaceName']        = _odbc_result($result, 'PlaceName'        );
      $items['PlaceType']        = _odbc_result($result, 'PlaceType'        );
//      $items['DateEdited']       = 'NOW()';
      $items['SourceID']         = '0';
      
//      $items['DateCreated']      = convertfield($result, 'ZDATE'                  ,0);
      
      $keys   = "";
      $values = "";
      
      foreach ($items as $k => $v) {
       if ($keys) $keys.=", ";
       $keys.="`".$k."`";
       
       if ($values) $values.=", ";
       $values.="'".$v."'";
      }
      
      $sql_this_b="INSERT INTO `".$thissqltablename."` 
       (
      ".$keys."
       ) VALUES (
      ".$values."
       ) ; 
      ";
      
      $r=$this->db->exec($sql_this_b);
      $lastid_b=$r->lastInsertID;
      if ($r->rowsAffected<1) {
       echo((($tags_enabled)?"<p>":"").$sql_this_b.": ".mysql_error().(($tags_enabled)?"</p>":"\n"));
       break;
      } else {
       $mar_b += 1;
      }
      
      $c++;
      if ($tablespreview) {
       if ($c%2==0)
        echo("<tr bgcolor=\"#d0d0d0\">\n");
       else
        echo "<tr bgcolor=\"#eeeeee\">\n";
      }
      for ($i=1; $i<=sizeof($thistable->fields); $i++) {
       //        $thissql.="'".iconv("windows-1251", "UTF-8", odbc_result($result, $i))."'";
       if ($tablespreview) {
        echo("<td>");
        echo(_odbc_result($result, $i));
        echo "</td>";
       }
       if ($i%$j==0) {
        $nrows+=1; // counting no of rows    
       }
      }
  //    $res=odbc_fetch_row($result);
      if ($tablespreview) {
       echo "</tr>";
      }
     } while ($result);
     $thissql.=";";
     if ($tablespreview) {
      echo "</td> </tr>\n";
      echo "</table >\n<br>";
     }
//     echo (($tags_enabled)?"<p>":"")."locationinfo items added: ".$mar_l.(($tags_enabled)?"</p>":"<br>\r");
     echo (($tags_enabled)?"<p>":"").$thissqltablename." items added: "       .$mar_b.(($tags_enabled)?"</p>":"<br>\r");
     
    break;
    
    case ('Projects'):
     
     $thissqltablename = 'projects';
     
 //    echo "tablespreview: ".$tablespreview."<br>";
//     $sql_l_tr  ="DELETE `locationinfo`.* FROM `locationinfo`,`lands`  WHERE (`locationinfo`.`IsTemplate` = 0) AND (`locationinfo`.`ID`=`lands`.`LocationID`) ; ";
     $sql_b_tr  ="DELETE FROM `".$thissqltablename."`   WHERE `SourceID` = 0 ; ";
     
//     $r=$this->db->exec($sql_l_tr);
//     $mar=$r->rowsAffected;
//     echo_log ("lands->locationinfo items deleted: ".$mar);
     
     $r=$this->db->exec($sql_b_tr);
     $mar=$r->rowsAffected;
     echo_log ($thissqltablename." items deleted: ".$mar);
     
     if ($tablespreview) {
      echo "<table border=\"1\" borderColor=\"\" cellpadding=\"0\" cellspacing=\"0\">\n";
      echo "<tr> ";
     }
     
     // -- print field name
     for ($j=0; $j<=sizeof($thistable->fields); $j++) {
      if ($tablespreview) {
       echo "<th  align=\"left\" bgcolor=\"#CCCCCC\" > <font color=\"#990000\"> ";
       echo($thistable->fields[$j]);
       echo "</font> </th>";
      }
      $fieldid[$thistable->fields[$j]] = $j;
     }
     $j=$j-1;
     
     $c  = 0;
     $ca = 0;
     
     $mar_b=0;
     do { // getting data
      $rowid = "row".$c;
      $result = $thistable->$rowid;
      
      $items=array();
      
      $items['ID']               = _odbc_result($result, 'ID'               );
      $items['Title']            = _odbc_result($result, 'ProjectName'      );
      $items['DateAdded']        = _odbc_result($result, 'DateStarted'      );
      $items['DateFinished']     = _odbc_result($result, 'DateFinished'     );
      $items['AddedBy']          = '1';
      $items['Description']      = _odbc_result($result, 'Description'      );
      $items['Cost']             = _odbc_result($result, 'Cost'             );
      $items['CustomerID']       = _odbc_result($result, 'Customer'         );
      $items['TypeID']           = _odbc_result($result, 'Type'             );
//      $items['DateEdited']       = 'NOW()';
      $items['SourceID']         = '0';
      
//      $items['DateCreated']      = convertfield($result, 'ZDATE'                  ,0);
      
      $keys   = "";
      $values = "";
      
      foreach ($items as $k => $v) {
       if ($keys) $keys.=", ";
       $keys.="`".$k."`";
       
       if ($values) $values.=", ";
       $values.="'".$v."'";
      }
      
      $sql_this_b="INSERT INTO `".$thissqltablename."` 
       (
      ".$keys."
       ) VALUES (
      ".$values."
       ) ; 
      ";
      
      $r=$this->db->exec($sql_this_b);
      $lastid_b=$r->lastInsertID;
      if ($r->rowsAffected<1) {
       echo((($tags_enabled)?"<p>":"").$sql_this_b.": ".mysql_error().(($tags_enabled)?"</p>":"\n"));
       break;
      } else {
       $mar_b += 1;
      }
      
      $c++;
      if ($tablespreview) {
       if ($c%2==0)
        echo("<tr bgcolor=\"#d0d0d0\">\n");
       else
        echo "<tr bgcolor=\"#eeeeee\">\n";
      }
      for ($i=1; $i<=sizeof($thistable->fields); $i++) {
       //        $thissql.="'".iconv("windows-1251", "UTF-8", odbc_result($result, $i))."'";
       if ($tablespreview) {
        echo("<td>");
        echo(_odbc_result($result, $i));
        echo "</td>";
       }
       if ($i%$j==0) {
        $nrows+=1; // counting no of rows    
       }
      }
  //    $res=odbc_fetch_row($result);
      if ($tablespreview) {
       echo "</tr>";
      }
     } while ($result);
     $thissql.=";";
     if ($tablespreview) {
      echo "</td> </tr>\n";
      echo "</table >\n<br>";
     }
//     echo (($tags_enabled)?"<p>":"")."locationinfo items added: ".$mar_l.(($tags_enabled)?"</p>":"<br>\r");
     echo (($tags_enabled)?"<p>":"").$thissqltablename." items added: "       .$mar_b.(($tags_enabled)?"</p>":"<br>\r");
     
    break;
    
    
    default:
     echo_log ('This table ('.$t.') is not supported by converter.');
    break;
    
   }
  }
  
  
  
  
  
  public function addlocationinfo($result) {
   $sql_l="INSERT INTO `locationinfo`
    (
     `CountryID`,
     `RegionID`,
     `CityID`,
     `StreetID`,
     `CrossID`,
     `DistrictID`,
     `MicrodistrictID`,
     `House`,
     `Building`,
     `Liter`,
     `Flat`,
     `Landmark`
    ) VALUES 
   ";
   
   
   //////////    locationinfo import start    //////////
   
   $cityid   = 2;
   $district = 0;
   switch (conv(_odbc_result($result, 'ZVRAION'))) {
    default :        $district=1;    break;
    case ('-'):      $district=1;    break;
    case ('ПРО'):    $district=1;    break;          // "ПРО" means "all another"
    case ('ЖЕЛ'):    $district=2;    break;
    case ('КИР'):    $district=3;    break;
    case ('КРА'):    $district=4;    break;
    case ('КБШ'):    $district=5;    break;
    case ('ЛЕН'):    $district=6;    break;
    case ('ОКТ'):    $district=7;    break;
    case ('ПРМ'):    $district=8;    break;
    case ('САМ'):    $district=9;    break;
    case ('СОВ'):    $district=10;   break;
    
    case ('ЧАП'):    $district=1; $cityid=6 ;     break;
    case ('НОВОК'):  $district=1; $cityid=5 ;     break;
    case ('ОТР'):    $district=1; $cityid=8 ;     break;
    
    case ('АЛЕК'):   $district=1; $cityid=13;     break;
    case ('БЕЗ'):    $district=1; $cityid=14;     break;
    case ('БОГ'):    $district=1; $cityid=15;     break;
    case ('БОЛГ'):   $district=1; $cityid=16;     break;
    case ('БОЛЧ'):   $district=1; $cityid=17;     break;
    case ('БОР'):    $district=1; $cityid=18;     break;
    case ('ВОЛ'):    $district=1; $cityid=19;     break;
    case ('ЕЛХ'):    $district=1; $cityid=20;     break;
    case ('ИСА'):    $district=1; $cityid=21;     break;
    case ('КАМ'):    $district=1; $cityid=22;     break;
    case ('КИН'):    $district=1; $cityid=23;     break;
    case ('КИНЧ'):   $district=1; $cityid=24;     break;
    case ('КЛЯ'):    $district=1; $cityid=25;     break;
    case ('КОШ'):    $district=1; $cityid=26;     break;
    case ('КРАС'):   $district=1; $cityid=27;     break;
    case ('КРЯ'):    $district=1; $cityid=28;     break;
    case ('НЕФ'):    $district=1; $cityid=29;     break;
    case ('ПЕС'):    $district=1; $cityid=30;     break;
    case ('ПОХ'):    $district=1; $cityid=31;     break;
    case ('ПРИ'):    $district=1; $cityid=32;     break;
    case ('СЕР'):    $district=1; $cityid=33;     break;
    case ('СТА'):    $district=1; $cityid=34;     break;
    case ('СЫЗ'):    $district=1; $cityid=35;     break;
    case ('ХВО'):    $district=1; $cityid=36;     break;
    case ('ЧЕЛ'):    $district=1; $cityid=37;     break;
    case ('ШЕН'):    $district=1; $cityid=38;     break;
    case ('ШИГ'):    $district=1; $cityid=39;     break;
   }
   
   if ($district==0) {
    echo("Unknown district: ".conv(_odbc_result($result, 'ZVRAION'))."<br>");
   }
   
 //        echo odbc_result($result,'ZMRAIONID')."<br>";
   switch (conv(_odbc_result($result, 'ZMRAIONID'))) {
    default : $microdistrict=1;
     break;
    case ('361'): $microdistrict=2;
     break;
    case ('362'): $microdistrict=3;
     break;
    case ('363'): $microdistrict=4;
     break;
    case ('364'): $microdistrict=5;
     break;
    case ('365'): $microdistrict=6;
     break;
    case ('366'): $microdistrict=7;
     break;
    case ('367'): $microdistrict=8;
     break;
    case ('368'): $microdistrict=9;
     break;
   }
   if (conv(_odbc_result($result, 'ZVRAION'))=="КРЯ")
    $microdistrict=7; // hack for Kryazh since it's a microdistrict rather than district
   if (_odbc_result($result, 'ZSTREETID')==2000) {
    $streetid=1;
   } else {
    $streetid=($this->getStreetIDByISCZVSTREET(_odbc_result($result, 'ZVSTREET'), $district));
   }
//   echo _odbc_result($result, 'ZVSTREET')."->".$streetid."<br>";
   
   if (_odbc_result($result, 'ZVSTRCROSS')==2000) {
    $crossid=1;
   } else {
    $crossid=($this->getStreetIDByISCZVSTREET(_odbc_result($result, 'ZVSTRCROSS'), $district));
   }
//   echo _odbc_result($result, 'ZVSTRCROSS')."->".$crossid."<br>";
   
   $sql_this_l=$sql_l."
    (
     'RU', 
     '57',
     '".$cityid."', 
     '".$streetid."', 
     '".$crossid."',
     '".$district."',
     '".$microdistrict."',
     '"._odbc_result($result, 'ZHOUSE')."', 
     '"._odbc_result($result, 'ZCORPUS')."', 
     '', 
     '"._odbc_result($result, 'ZAPART')."',
     '"._odbc_result($result, 'ZSTREET')."'
    ) ; 
   ";
   
   $r=$this->db->exec($sql_this_l);
   $lastid_l=$r->lastInsertID;
   if ($r->rowsAffected<1) {
    echo((($tags_enabled)?"<p>":"").$sql_this_l.(($tags_enabled)?"</p>":"\n"));
    return 0;
   } else {
 //   $mar_l += 1;
    return $lastid_l;
   }
   
   //////////    locationinfo import end    //////////
  }
  
  function getStreetIDByISCZVSTREET($ZVSTREET, $district) {
   $sql_ag="SELECT `ID` FROM `streets` WHERE `IscStreet`='".strprepare($ZVSTREET)."' AND `IscDistrict`='".$district."' ; ";
   $row=$this->db->query_first($sql_ag);
//   ajax_echo_r($row);
   
   if ($row->ID) {                                            // if found then return it
    $ret = $row->ID;
   } else {
    $ret = 1;                                                 // else return default value
   }
   return $ret;
  }
  
  function getagent($result) { // look for desired agent and return its ID. if not found then create one and return its ID. also manage company ID in similar way.
   global $ca;
   $debug = 0;
   
   if ($debug) echo $sql_ag."<hr>";
   $ret=new stdClass();  
   
   $zvagent = conv(_odbc_result($result, 'ZVAGENT'));
   $zvagph  = conv(_odbc_result($result, 'ZVAGPH'));
   $zvcomp  = conv(_odbc_result($result, 'ZVCOMP'));
   
   $sql_ag="
    SELECT `ID` 
    FROM `agentinfo` 
    WHERE 
     (`FirstName`='".$zvagent."') AND 
     (    `Phone`='".$zvagph."')
    LIMIT 0, 1
    ; 
   ";
   if ($debug) echo $sql_ag."<br>";
   $row=$this->db->query_first($sql_ag);
   $ret->lastid_agent = -1; // $row['ID'];
   if ($debug) echo "Row: ".print_r($row,true)."<br>";
//   if ($debug) echo "Row: ".print_r($row,true)."<br>";
   if (!$row->ID) {        // desired agent not found - we will create the new agent
    if ($debug) echo "Adding agentinfo record.<br>";
 //   echo "MySQL Agent C: ".$row['ID'].", ";
 //   echo "MS Access zagentc: "._odbc_result($result, 'ZAGENTC')."<br>";
    
    // find corresponding company
    $sql_ag="SELECT `ID` FROM `companies` WHERE `Name`='".$zvcomp."' ; ";
    $row=$this->db->query_first($sql_ag);
 //   echo "Company ID: ".$row['ID']."<br>";
    if (!$row->ID) {                                            // if not found then create
     $sql_ag="INSERT INTO `companies` (`Name`, `IsTemplate`) VALUES ('".$zvcomp."','0') ; ";
     if ($debug) echo $sql_ag."<br>";
     $row=$this->db->exec($sql_ag);
     $mar=$row->rowsAffected;
     if ($debug) echo "Result: ".$mar."<br>";
//     if ($debug) echo "Result: ".print_r($row,true)."<br>";
     $ret->lastid_company=$row->lastInsertID;
    } else {                                                    // if found then use it
     $ret->lastid_company = $row->ID;
    }
    
 //   echo "Company ID to insert to agentinfo: ".$lastid_company."<br>";
    
 //   $ret->lastid_agent = (int)_odbc_result($result, 'ZAGENTC');
    
    if (!$ca) $ca=0;
    
    $sql_ag="INSERT INTO `agentinfo`
     (
      ".(($ret->lastid_agent==-1)?"":"`ID`,")."
      `FirstName`,
      `LastName`,
      `Phone`,
      `Email`,
      `AuxPhone`,
      `CompanyID`,
      `Username`,
      `Password`,
      `LastAccess`
     ) VALUES (
      ".(($ret->lastid_agent==-1)?"":"'".$ret->lastid_agent."',")."
      '".$zvagent."',
      '',
      '".$zvagph."',
      '',
      '',
      '".$ret->lastid_company."',
             'User_".$ca."',
      '".md5("User_".$ca)."',
      '".(date("Y-m-d H:i:s"))."'
     ) ; 
    ";
    //       ajax_echo ($sql_ag."<br>");
    
    if ($debug) echo $sql_ag."<br>";
    $r=$this->db->exec($sql_ag);
//    if ($debug) echo "Result: ".print_r($r,true)."<br>";
    
    $mar=$r->rowsAffected;
    if ($debug) echo "Result: ".$mar."<br>";
    $ret->lastid_agent = $r->lastInsertID;
    if ($mar>0) {
     $ca++;
    } else {
     echo_r ("Cannot execute SQL statement: ".$sql_ag);
    }
   } else {
    $ret->lastid_agent = $row->ID;
   }
   
   return $ret;
  }
  
  
  
  
  
  
 }
 
 
 
 function echo_log ($txt){
  global $tags_enabled;
  echo (($tags_enabled)?"<p>":"").$txt.(($tags_enabled)?"</p>":"<br>\r");
 }
 
?>
