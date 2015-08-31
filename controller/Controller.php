<?
 include_once("model/Model.php");          // connect to the Model part of our MVC pattern
 include_once("model/Settings.php");       // connect to the Settings 
 include_once("lessc.inc.php");            // the lesscss server-side compiler
// include_once("getxls.php");             // the MS Excel simple export class
 include_once("StringForge.1.1.php");      // the string processing class
 include_once("Template.1.89.php");             // add Template class
 include_once("Session.1.2.php");
 
 class Controller {                        // the Controller class
  public $model;                           // used to handle the link to the Model
  public $settings;                        // used to handle the link to the Settings (settings.ini in an object representation)
  public $StringForge;
  public $viewroot;
  public $themeinfo;
  
  public function __construct() {          // the constructor function
   $this->settings = Settings::init();     // load settings file
   $this->sess = new SessionSaveHandler(getrootdirsrv().$this->settings->sessionsavepath);
   
   date_default_timezone_set($this->settings->timezone);       // set the default timezone. may be we need to customize it for particular user
   $this->model       = new Model($this->settings);            // instantiate the Model class
   $this->less        = new lessc;
   $this->StringForge = new StringForge($this->settings);
  }
  
  public function run() {                                                      // the main sub in our application
   if(!isset($_SESSION)) session_start();
   
   $action = getvariablereq('action');          // get action from the request
   $data   = getvariablereq('data'  );          // get JSON data from the request
   
   $data = ajaxdecode($data);            // fix some escaped paths (if any)
   
//   echo htmlspecialchars($data);
   if ($data) {
    $json=json_decode($data);                      // parse Json data came from AJAX (if any)
   }
   
   
   
   
   $userid     = getvariable('userid'     ,0);
   if (!$userid) {
    $userid     = getsecurevariable('userid'     ,0);
   }
   
//    ajax_echo_r ($userid);
//    ajax_echo_r (session_id());
//    ajax_echo_r ($_SESSION);
//    ajax_echo_r ($this->userdetails);
   
//    $hue        = getvariable       ('hue'       );
   $hue                = getsecurevariable ('hue'            ,-1);
   $theme              = getsecurevariable ('theme'          ,-1);
   $SmoothAnimation    = getsecurevariable ('SmoothAnimation',-1);
   $this->userdetails = $this->model->getUserDetails($userid);
   $isadmin = $this->userdetails->usergroup->Password==2;
   
//   ajax_echo_r ($this->userdetails);
   
   if (!is_dir("view/".$theme."/")) {
    $theme=$this->settings->defaulttheme;
   }
   if ((int)$SmoothAnimation==-1) {                                            // if it's not stored in session
    if ($userid>-1) {                                                             // if the user is logged
     $SmoothAnimation = $this->userdetails->SmoothAnimation;                         // get hue value from user details
    } else {                                                                   // if the user is came for the first time
     $SmoothAnimation = 0;                                                     // set the default value
    }
   }
   
//   $this->userdetails = new stdClass();
//   $this->model->setUserDetails($this->userdetails);
   
   $this->viewroot = "view/".$theme;
   setsecurevariable("viewroot",$this->viewroot);
   $this->imgfolder = $this->viewroot."/img";
   
   if (file_exists($this->viewroot."theme.php")) {
    $this->themeinfo = parse_ini_file($this->viewroot."theme.php");
   }
   
   
   
   
   if (sizeof($_FILES)>0) {                        // file upload
//    echo "Fileupload...";
//    echo session_id();
    
//    ajax_echo_r($_FILES);
    
    $file = $_FILES['fileupload'];
//    ajax_echo_r($file);
    mkdirr($this->settings->temppath);
    
    switch ($action) {
     case ('balance'):
      $fname = $this->settings->temppath.'/'.session_id().".xls";
      $rslt = move_uploaded_file($file['tmp_name'], $fname);
      
      $this->model->loadBalance($fname);
      
     break;
     case ('integration'):
      $ext = substr($file['name'], strrpos($file['name'], "."));
      
      $fname = $this->settings->temppath.'/'.session_id().$ext;
//      echo $fname;
      $rslt = move_uploaded_file($file['tmp_name'], $fname);
      
      switch ($ext) {
       case ('.csv'): // import youtube stats
        $this->model->importYoutubeStats($fname);
       break;
      }
      
      //$this->model->loadBalance($fname);
      
     break;
     case ('steps'):
      $ext = substr($file['name'], strrpos($file['name'], "."));
      
      $dir = sdir($this->settings->temppath, "full_".session_id().'_*.*');
//      ajax_echo_r ($dir);
      if ($dir) {
       foreach ($dir as $f) {
        $pos0 = strrpos($f,"_")+1;
        $pos1 = strrpos($f,".");
        $fid = (int)substr($f, $pos0, $pos1-$pos0);
        if ($fid>$thisid) $thisid = $fid;
//           echo $fid."<br>";
       }
      }
      
      $thisid++;
      
      $filef     = $this->settings->temppath.'/full_'.session_id()."_".$thisid.$ext;
      $filet     = $this->settings->temppath.'/thb_' .session_id()."_".$thisid.$ext;
      
//      $fname = $this->settings->temppath.'/'.session_id().$ext;
//      echo $filef."<br>";
//      echo $filet."<br>";
      
      $rslt = move_uploaded_file($file['tmp_name'], $filef);
//      echo $rslt;
      
      
      $pic = $this->model->preparePic($filef, $filet, 0);
      
//      echo $this->showTempPics();
      
      $ajax_fileupload_img_str = $this->fillAttachments($action, $this->settings->temppath."/", session_id());
      echo $ajax_fileupload_img_str;
      
     break;
    }
    
    if (($file['error']==0) && $rslt) {
//     echo localize("<en>File uploaded successfully</en><ru>Файл загружен успешно</ru>");
    } else {
     echo localize($this->getMessage('error_upload'));
    }
   } else {                                               // no file upload
    $go      = getvariablereq('go');
    $key     = getvariablereq('key').getvariablereq('k');          // get JSON data from the request
    
    if ($key) {
     $go="ownlogin";
//     echo strlen($key);
//     $key = str_replace(".", "", $key);
     $key = substr($key, 0, 32);
//     echo $key;
    }
    
    if ($go=='ownlogin') {
     $login = $this->model->ownLogin($key);
     
     if ($login) {
      //$tmp = new Template($this->viewroot, 'redir.htt');
      //$tmp->fill('%Link%', '?go=users');
      
//      ajax_echo_r($login);
      
      if ($login->IsProject) {
       $virtuser = new stdClass;
       $virtuser->ID = -2;
       $virtuser->Name = $login->Firstname." ".$login->Surname;
       $virtuser->usergroup = $this->model->getUserGroup(0);
       
       setsecurevariable('userid',$virtuser->ID);
       setsecurevariable('tmpcnt',0         );
 //      session_write_close();
       
       $this->sess->close();
       
       $this->userdetails = $virtuser;
       $this->projectdetails = $login;
       
       $userid = $virtuser->ID;
       
       
      } else {
       setsecurevariable('userid',$login->ID);
       setsecurevariable('tmpcnt',0         );
 //      session_write_close();
       
       $this->sess->close();
       
       $this->userdetails = $login;
       $userid = $login->ID;
      }
     } else {
      //$tmp = new Template($this->viewroot, 'wrong_key.htt');
     }
    }
    $isadmin = $this->userdetails->usergroup->Password==2;
    
    
//    $userstate = $this->model->isUserAuth($userid);                             // check user's session and login state
    if ($action) {                                                             // start the processing of ajax commands
     header("Pragma:        no-cache");
     header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
     header("Expires:       0");
     
//     echo $action;
     if (($userid>-1) || ($action=='dologin') || ($action=='dologout') || ($action=='api') || ($action=='ownlogin')) {
      switch ($action) {
       case ("savefield"):
       case ("saveField"):
        $this->model->saveField($json);
       break;
       case ("dologin"):                                             // used to login the user
        $is_auth = $this->model->userAuth($json);
        if ($is_auth) {
         setsecurevariable('userid',$is_auth->ID);
         setsecurevariable('tmpcnt',0           );
  //       $tmp = new Template($this->viewroot, 'ajax_nouser_logged.htt');   // load JS user successful login template
         ajax_echo ("##refresh##");
        } else {
         $tmp = new Template($this->viewroot, 'ajax_nouser_error.htt');      // load JS user login error template
         $tmp->fill('%imgfolder%'          , $this->imgfolder                    );
         ajax_echo($tmp->output());
        }
       break;
       case ("dologout"):                                                       // used to log out
        setsecurevariable(             "hue", "-1" );                           // set to default (abstracted)
        setsecurevariable(           "theme", $this->settings->defaulttheme );  // set to default (abstracted)
        setsecurevariable( "SmoothAnimation", "1"  );                           // enabled by default
        setsecurevariable(          "userid", "-1" );
        $userid = -1;
  //       ajax_echo_r($_SESSION);
        ajax_echo ("##refresh##");
       break;
       case ("setUserParameter"):                                               // used to save user's setting
        setsecurevariable($json->name, $json->value);
 //        $userid = 0;
 //        ajax_echo_r($_SESSION);
        if (($json->name=='hue') || ($json->name=='theme')) {
         ajax_echo ("##refresh##");
        }
       break;
       
       case ("newpartner"):                                   // used to login the user
        $is_sent = (trim($json->name)!='') && (trim($json->email)!='') && (trim($json->comments)!='');
        if ($is_sent) {
         $is_sent = sendmail("Email from ".$json->name,"Bricks Pro partnership request: <br>\n".$json->comments);
        }
        if ($is_sent) {
         $tmp = new Template($this->viewroot, 'ajax_new_partner.htt');            // load JS success template
        } else {
         $tmp = new Template($this->viewroot, 'ajax_new_partner_error.htt');      // load JS error template
        }
        $tmp->fill('%imgfolder%'  ,$this->imgfolder );
        ajax_echo($tmp->output());
       break;
       
       case ("showgallery"):
 //        ajax_echo_r($json);
        if ($json->operation=='download') {
         $ret = $this->model->getGalleryItem($json);
        } else {
         if ($json->operation=='delete') {
          $ret = $this->model->deleteGalleryItem($json);
         }
         $gal = $this->getGallery($json);
         $ret = $gal->compiled;
        }
        ajax_echo ($ret);
       break;
       
       
       case ('postevent'):
        $ret=$this->model->addEvent($json);
 //       ajax_echo_r ($ret->rowsAffected);
        ajax_echo_r($json);
       break;
       
       case ('getnewevents'):
        $events = $this->model->getNewEvents($json);
        
        echo json_encode($events);
       break;
       
       case ('processxls'):
        $xls = $this->model->loadXls(0);
        
        setCache('settings',$json);
        
        if ($xls) {
         $data = $this->model->processXls($xls, $json);
        } else {
         $data = "";
        }
        ajax_echo_r($data);
        
       break;
       
       case ('showtable'):
//        ajax_echo_r ($this->userdetails->usergroup);
        
        $json->tablename = $json->go;
        
        $thisdebug = 0;
        if ($thisdebug) $mtime = microtime(true);
        switch ($json->go) {
         case ('news'):
          $params = new stdClass;
          $params->listid=-1;
          $table = $this->model->getTasks($params);
         break;
         case ('users'):
          if ($this->userdetails->GroupID!=1) $id = $this->userdetails->ID;
          $table[1] = $this->model->getUsers($json, $id);
         break;
         case ('dashboard'):
          // see case sections for each dashboard subpage
         break;
         case ('objects'):
          $table = $this->model->getObjects($json);
         break;
         case ('customers'):
          $table = $this->model->getCustomers($json);
         break;
         case ('happiness'):
          $table = $this->model->getHappiness($json);
         break;
         case ('system'):
         break;
         default:
          $table = $this->model->getTable($json, $this->userdetails);
         break;
        }
        
//        ajax_echo_r($table);
//        ajax_echo_r($json);
        
        if ($json->isexport) {
         $tmp = new Template($this->viewroot, 'output_list.htt');                                      // load common parent template
         $block_item = new Template($this->viewroot, $tmp->returnloop('block_item'));
         
         $thislist = "";
         if (sizeof($table[1])) {
          foreach ($table[1] as $item) {
           $block_item->reload();
           $block_item->fill("%Item%"           , $item->Item     );
           $block_item->fill("%Name%"           , $item->Name     );
           $thislist.=$block_item->output();
          }
         }
         
         $tmp->fillloop("block_item"           , $thislist     );
         
         $tmp->fill("%numrows%",sizeof($table[1]));
         
         ajax_echo(sup($tmp->output()));
        } else {
//         ajax_echo_r ($json);
         if ($json->r_viewmode) {
          $tmp_src = new Template($this->viewroot, 'main_'.$json->go.'_'.$json->r_viewmode.'.htt');             // load common parent template
         } else {
          $tmp_src = new Template($this->viewroot, 'main_'.$json->go.'_'.$json->r_cutby.'.htt');                                   // load common parent template
          if (!$tmp_src->is_template) {
//           echo $isadmin;
           if ($isadmin) {
            $tmp_src = new Template($this->viewroot, 'main_'.$json->go.'.htt');                                   // load common parent template
           } else {
            $tmp_src = new Template($this->viewroot, 'main_'.$json->go.'_manager.htt');                                   // load common parent template
           }
          }
         }
         $tmp_src = $this->fillStricts($tmp_src, 1);
         $tmp_src->fill(  '%imgfolder%' , $this->imgfolder                    );
         
//         ajax_echo_r ($json);
         switch ($json->go) {
          case ('dashboard'):
//           $tmp_src->loadloop('block_dashboard');
           
  //         $cutBy = "customersources";
           $cutBy   = $json->r_cutby;
           $caption = $json->r_cutby_caption;
           
           $tmp_src->fill('%caption%', $caption);
           
           switch ($json->r_cutby) {
            case ('common'):
             $t = $this->model->getStats('common','', $json->s_UserID);
             
             $vars = $tmp_src->getVariables();
//             ajax_echo_r ($t);
             foreach ($vars as $var) {
              if (is_numeric($item->$var)) {
               $tmp_src->fill(        '%'.$var.'%', format($t->$var, "#.#")   );
              } else {
               $tmp_src->fill(        '%'.$var.'%',        $t->$var           );
              }
             }
             
            break;
            case ('ads'):
             $t = $this->model->getStats('ads','', $json->s_UserID);
             
             
             
             $c = 0;
             $loop1  = new Template($this->viewroot, $tmp_src->returnloop('loop_cell1'));
             $loop2  = new Template($this->viewroot, $tmp_src->returnloop('loop_cell2'));
             $loop3  = new Template($this->viewroot, $tmp_src->returnloop('loop_cell3'));
             $loopstr = "";
             
             if ($t) {
              foreach ($t as $k=>$v) {
               $loop1->reload();
               $loop2->reload();
               $loop3->reload();
               
               $loop1->fill(  "%h1%", (int)($v->Cnt*10   ));
               $loop2->fill(  "%h2%", (int)($v->Value/1000));
               $loop3->fill(  "%h3%", (int)($v->Views/10));
               $loopstr1.= $loop1->output();
               $loopstr2.= $loop2->output();
               $loopstr3.= $loop3->output();
               $c++;
               
//               echo (int)($v->Views)."<br>";
               
              }
             } else {
              $block_norows = new Template($this->viewroot, $tmp_src->returnloop('block_norows'));
              $loopstr = $block_norows->output();
             }
             $tmp_src->fillloop( 'loop_cell1', $loopstr1 );
             $tmp_src->fillloop( 'loop_cell2', $loopstr2 );
             $tmp_src->fillloop( 'loop_cell3', $loopstr3 );
             
             
             $vars = $tmp_src->getVariables();
//             ajax_echo_r ($t);
             
             echo sizeof($t);
             
             
             foreach ($vars as $var) {
              if (is_numeric($item->$var)) {
               $tmp_src->fill(        '%'.$var.'%', format($t->$var, "#.#")   );
              } else {
               $tmp_src->fill(        '%'.$var.'%',        $t->$var           );
              }
             }
             
            break;
            case ('monthly'):
             $list = $this->model->getMoneyStats($json->r_cutby);
//             ajax_echo_r ($list);
             
             $loop = new Template($this->viewroot, $tmp_src->returnloop('loop_row'));
             
             $vars = $loop->getVariables();
//             ajax_echo_r ($t);
             foreach ($list->data as $t) {
              $loop->reload();
              foreach ($vars as $var) {
               if (is_numeric($t->$var)) {
                $loop->fill(        '%'.$var.'%', format($t->$var, "#.#")   );
               } else {
                $loop->fill(        '%'.$var.'%',        $t->$var           );
               }
              }
              $loopstr .= $loop->output();
             }
             $tmp_src->fillloop('loop_row', $loopstr);
             
             
             $vars = $tmp_src->getVariables();
             foreach ($vars as $var) {
              if (is_numeric($list->$var)) {
               $tmp_src->fill(        '%'.$var.'%', format($list->$var, "#.#")   );
              } else {
               $tmp_src->fill(        '%'.$var.'%',        $list->$var           );
              }
             }
             
            break;
            case ('deposits'):
             $list = $this->model->getDepositsStats($json);
//             ajax_echo_r ($list);
             
             foreach (array('objects', 'customers') as $tablename) {
              $c = 0;
              $loop = new Template($this->viewroot, $tmp_src->returnloop('loop_table_row_'.$tablename));
              $loopstr = "";
              
              if ($list->$tablename) {
               $vars = $loop->getVariables();
  //             ajax_echo_r ($t);
               foreach ($list->$tablename as $t) {
                $loop->reload();
                $loop->fill("%c%",  $c%2);
                foreach ($vars as $var) {
                 if (is_numeric($t->$var)) {
                  $loop->fill(        '%'.$var.'%', format($t->$var, "#.#")   );
                 } else {
                  $loop->fill(        '%'.$var.'%',        $t->$var           );
                 }
                }
                $loopstr .= $loop->output();
                $c++;
               }
              } else {
               $block_norows = new Template($this->viewroot, $tmp_src->returnloop('block_norows'));
               $loopstr = $block_norows->output();
              }
              $tmp_src->fillloop('loop_table_row_'.$tablename, $loopstr);
             }
             
             /*
             $vars = $tmp_src->getVariables();
             foreach ($vars as $var) {
              if (is_numeric($list->$var)) {
               $tmp_src->fill(        '%'.$var.'%', format($list->$var, "#.#")   );
              } else {
               $tmp_src->fill(        '%'.$var.'%',        $list->$var           );
              }
             }
             */
             
            break;
            case ('handshakes'):
             $list = $this->model->getHandshakesStats($json);
//             ajax_echo_r ($list);
             
             foreach (array('objects', 'customers') as $tablename) {
              $c = 0;
              $loop = new Template($this->viewroot, $tmp_src->returnloop('loop_table_row_'.$tablename));
              $loopstr = "";
              
              if ($list->$tablename) {
               $vars = $loop->getVariables();
  //             ajax_echo_r ($t);
               foreach ($list->$tablename as $t) {
                $loop->reload();
                $loop->fill("%c%",  $c%2);
                foreach ($vars as $var) {
                 if (is_numeric($t->$var)) {
                  $loop->fill(        '%'.$var.'%', format($t->$var, "#.#")   );
                 } else {
                  $loop->fill(        '%'.$var.'%',        $t->$var           );
                 }
                }
                $loopstr .= $loop->output();
                $c++;
               }
              } else {
               $block_norows = new Template($this->viewroot, $tmp_src->returnloop('block_norows'));
               $loopstr = $block_norows->output();
              }
              $tmp_src->fillloop('loop_table_row_'.$tablename, $loopstr);
             }
             
             /*
             $vars = $tmp_src->getVariables();
             foreach ($vars as $var) {
              if (is_numeric($list->$var)) {
               $tmp_src->fill(        '%'.$var.'%', format($list->$var, "#.#")   );
              } else {
               $tmp_src->fill(        '%'.$var.'%',        $list->$var           );
              }
             }
             */
             
            break;
            case ('userseff'):
             $loop = new Template($this->viewroot, $tmp_src->returnloop('loop'));
             $t = $this->model->getStatsUsersEff($json);
//             ajax_echo_r ($t);
             $vars = $loop->getVariables();
             $loopstr = "";
             foreach ($t as $k=>$item) {
              $loop->reload();
              $loop->fill('%total%', $k);
              foreach ($vars as $var) {
               if ($var) {
                if (is_numeric($item->$var)) {
                 $loop->fill(        '%'.$var.'%', format($item->$var, "#.#")   );
                } else {
                 $loop->fill(        '%'.$var.'%', $item->$var   );
                }
               }
              }
              $loopstr .= $loop->output();
             }
             $tmp_src->fillloop('loop', $loopstr);
            break;
            default:
             $loop = new Template($this->viewroot, $tmp_src->returnloop('loop_customersources'));
             $t = $this->model->getStats('customers', $cutBy, $json->s_UserID);
//             ajax_echo_r ($t);
             $vars = $loop->getVariables();
             $loopstr = "";
             foreach ($t as $k=>$item) {
              $loop->reload();
              $loop->fill('%total%', $k);
              foreach ($vars as $var) {
               if (is_numeric($item->$var)) {
                $loop->fill(        '%'.$var.'%', format($item->$var, "#.#")   );
               } else {
                $loop->fill(        '%'.$var.'%', $item->$var   );
               }
              }
              $loopstr .= $loop->output();
             }
             $tmp_src->fillloop('loop_customersources', $loopstr);
             
             $loop = new Template($this->viewroot, $tmp_src->returnloop('loop_objectsources'));
             $t = $this->model->getStats('objects', $cutBy, $json->s_UserID);
             $vars = $loop->getVariables();
             $loopstr = "";
             foreach ($t as $k=>$item) {
              $loop->reload();
              $loop->fill('%total%', $k);
              foreach ($vars as $var) {
               if (is_numeric($item->$var)) {
                $loop->fill(        '%'.$var.'%', format($item->$var, "#.#")   );
               } else {
                $loop->fill(        '%'.$var.'%', $item->$var   );
               }
              }
              $loopstr .= $loop->output();
             }
             $tmp_src->fillloop('loop_objectsources', $loopstr);
            break;
           }
           
           $tmp_src->fillloop(       "block_no","" );
           $tmp_src->fillloop(   "block_norows","" );
           $tmp_src->fillloop(    "block_type1","" );
           $tmp_src->fillloop(    "block_type2","" );
           $tmp_src->fillloop(     "block_dir0","" );
           $tmp_src->fillloop(     "block_dir1","" );
          break;
          case ('news'):
           $looptmp = new Template($this->viewroot, $tmp_src->returnloop('loop_table'));
           $loop_table_row = new Template($this->viewroot, $tmp_src->returnloop('loop_table_row'));
           $vars = $loop_table_row->getVariables();
           
           $c = 0;
           
           $thistable = "";
           if (sizeof($table)) {
            $thisidcolumnname = $json->tablename."_ID";
            foreach ($table as $item) {
             $loop_table_row->reload();
             foreach ($vars as $vn) {
              if ($vn!=="Children") {
               $loop_table_row->fill("%".$vn."%", $item->$vn);
              }
             }
             
             $thistable.=$loop_table_row->output();
             $c++;
            }
           }
           $looptmp->fillloop('loop_table_row',$thistable);
           $tmp_src->fillloop("loop_table",$looptmp->output());
           
           $tmp_src->fill("%numrows%",sizeof($table));
           
          break;
          case ('money'):
           $looptmp        = new Template($this->viewroot, $tmp_src->returnloop('loop_table'));
           $loop_table_row = new Template($this->viewroot, $tmp_src->returnloop('loop_table_row'));
           $vars = $loop_table_row->getVariables();
           
           $block_type = array();
           $block_type[1]     = new Template($this->viewroot, $tmp_src->returnloop('block_type1'));
           $block_type[2]     = new Template($this->viewroot, $tmp_src->returnloop('block_type2'));
           
           $block_type[1] = $block_type[1]->output();
           $block_type[2] = $block_type[2]->output();
           
           $lastTypeID = -1;
           $c = 0;
//           ajax_echo_r ($table[1]);
           $thistable = "";
           if (sizeof($table)) {
            $thisidcolumnname = $json->tablename."_ID";
            foreach ($table[1] as $item) {
             if ($lastTypeID!=$item->TypeID) {
              $lastTypeID = $item->TypeID;
              $c = 1;
             }
             
             $loop_table_row->reload();
             $loop_table_row->fill( "%TypePicture%", $block_type[$item->TypeID]);
             $loop_table_row->fill("%c%",  $c%2+($item->TypeID-1)*2);
             foreach ($vars as $vn) {
              $loop_table_row->fill("%".$vn."%", $item->$vn);
             }
             
             $thistable.=$loop_table_row->output();
             $c++;
            }
           }
           $looptmp->fillloop('loop_table_row',$thistable);
           $tmp_src->fillloop("loop_table",$looptmp->output());
           
           $tmp_src->fill("%numrows%",sizeof($table[1]));
           
           $tmp_src->fillloop(       "block_no","" );
           $tmp_src->fillloop(   "block_norows","" );
           $tmp_src->fillloop(    "block_type1","" );
           $tmp_src->fillloop(    "block_type2","" );
          break;
          case ('events'):
//           ajax_echo_r ($json);
           
           if (($json->go=='events') || ($json->go=='customers') || ($json->r_viewmode=='all') || ($json->r_viewmode=='current')) {
            $looptmp = clone $tmp_src;
            
            $looptmp->loadloop("loop_table");
            $tmp = clone $looptmp;
            
            if (sizeof($table[1])) {
             $block_no = clone $looptmp;
             $block_no->loadloop('block_no');
             $block_no=$block_no->output();
             
             $tmp->loadloop("checkbox_th");
             $checkbox_th=clone $tmp;
             $tmp = clone $looptmp;
             
             $tmp->loadloop("checkbox_td");
             $checkbox_td=clone $tmp;
             $tmp = clone $looptmp;
             
             $loop_table_row = clone $looptmp;                                  // load common parent template
             $loop_table_cell = clone $looptmp;                                 // load common parent template
             
             $looptmp->loadloop("loop_table_header");
             $loopdata = "";
             $loopdata.=$checkbox_th->output();
             
             if ($table[0]) {
              foreach ($table[0] as $k => $v ) {
               if ($v) {
                if ($v!='CustomerID') {
                 $looptmp->reload();
                 $looptmp->fill("%th%"       , $this->StringForge->prepare($v));
                 $loopdata.=$looptmp->output();
                }
               }
              }
             }
             
             $tmp->fillloop("loop_table_header"   ,$loopdata           );
             $loop_table_row->loadloop("loop_table_row");
             $loop_table_cell->loadloop("loop_table_cell");
             $emptyinfo = file_get_contents($this->viewroot.'/templates/emptyinfo.htt');
             $c = 0;
             
             $thistable = "";
             
             $thisidcolumnname = $json->tablename."_ID";
             foreach ($table[1] as $item) {
              $loop_table_row->reload();
              $thisrow   = "";
              $checkbox_td->reload();
              $checkbox_td->fill('%checked%' , 'checked'            );
              $checkbox_td->fill('%id%'      , $item->$thisidcolumnname );
              $thisrow.=$checkbox_td->output();
              foreach ($item as $k=>$v) {
               if ($k && ($k!='CustomerID')) {
                $loop_table_cell->reload();
                if ($k==$json->tablename."_AuxInfo") {
                 if (trim($v)=="") $v=$emptyinfo;
                }
                $loop_table_cell->fill("%celldata%"     , (((int)$v==0) && ((string)(int)$v==$v))?$block_no:($v));
                $loop_table_cell->fill("%k%"     , $k);
                $thisrow.=$loop_table_cell->output();
               }
              }
              $loop_table_row->fillloop('loop_table_cell',$thisrow);
              $loop_table_row->fill("%c%",  $c%2);
              
              $loop_table_row->fill("%id%"           , $item->ID             );
              $loop_table_row->fill("%tablename%"    , $json->tablename      );
              
              $thistable.=$loop_table_row->output();
              $c++;
             }
             $tmp->fillloop('loop_table_row',$thistable);
             $tmp->fillloop("checkbox_th","");
             $tmp->fillloop("checkbox_td","");
             $tmp_src->fillloop("block_norows","");
             
             
             $tmp_src->fillloop("loop_table",$tmp->output());
            } else {
             $block_norows = clone $looptmp;
             $block_norows->loadloop('block_norows');
             $block_norows=$block_norows->output();
             
             $tmp_src->fillloop("loop_table",$block_norows);
            }
            
            $tmp_src->fill("%numrows%",sizeof($table[1]));
           } else {
            switch ($json->r_viewmode=='monthly') {
             case ('monthly'):
              $loop_month = clone $tmp_src;
              $loop_month->loadloop('loop_month');
              
              $loop_bytype = clone $tmp_src;
              $loop_bytype->loadloop('loop_bytype');
              
              $months = "";
              if (sizeof($table[1])) {
               $report=array();
               $prevmonth = -1;
               
               //ajax_echo_r($table[1][0]);
               $thismonth = new stdClass();
               
               $fakeitem = new stdClass();
               $fakeitem->Month=-2;
               $table[1][]=$fakeitem;
               
               foreach ($table[1] as $item) {
                if (($prevmonth!=$item->Month) && ($thismonth->month)) {
                 $days = cal_days_in_month(CAL_GREGORIAN, $thismonth->month, $thismonth->year);
                 
                 $thismonth->etm    = $etm;
                 $thismonth->itm    = $itm;
                 $thismonth->aetm   = sprintf("%01F", $etm/$days);
                 $thismonth->aitm   = sprintf("%01F", $itm/$days);
                 $thismonth->ietm   = $ietm;
                 $thismonth->eetm   = $eetm;
                 
                 arsort($bytype);
                 $thismonth->bytype = $bytype;
                 
                 $report[] = clone $thismonth;
                 
                 $etm    = 0;
                 $itm    = 0;
                 $ietm   = 0;
                 $eetm   = 0;
                 $bytype = array();
                }
                
   //             if ($item->Month==5) {
   //              ajax_echo_r($bytype);
   //             }
                
                $thismonth->year  = $item->Year  ;
                $thismonth->month = $item->Month ;
                
                $bytype[$item->Description] += $item->Value;
                
                If (($item->EZID == 1) And ($item->TypeID == 2)) {
                 $eetm += $item->Value;
                }
                If (($item->EZID == 2) And ($item->TypeID == 2)) {
                 $ietm += $item->Value;
                }
                
                If (($item->TypeID == 1) || ($item->TypeID == 3)) {                   // income
                 $itm += $item->Value;
                } else {                                                               // expenditure
                 $etm += $item->Value;
                }
                
                $prevmonth=$item->Month;
                
               }
               
               foreach ($report as $thismonth) {
                $loop_month->reload();
                $loop_month->fill( "%year%",$thismonth->year );
                $loop_month->fill("%month%",$thismonth->month);
                
                $loop_month->fill(  "%etm%",$thismonth->etm  );
                $loop_month->fill(  "%itm%",$thismonth->itm  );
                $loop_month->fill( "%aetm%",$thismonth->aetm );
                $loop_month->fill( "%aitm%",$thismonth->aitm );
                $loop_month->fill( "%eetm%",$thismonth->eetm );
                $loop_month->fill( "%ietm%",$thismonth->ietm );
                
                $bytype="";
                if (is_array($thismonth->bytype)) {
                 foreach ($thismonth->bytype as $k=>$v) {
                  $loop_bytype->reload();
                  
                  $loop_bytype->fill("%k%",$k);
                  $loop_bytype->fill("%v%",$v);
                  
                  $bytype.= $loop_bytype->output();
                 }
                }
                
                $loop_month->fillloop("loop_bytype",$bytype);
                $months.=$loop_month->output();
               }
              }
              $tmp_src->fillloop("loop_month",$months);
             break;
            }
           }
           
          break;
          
          case ('users'):
//           echo $id;
           if ($id) {
            $tmp_src->fillloop('block_delete', '');
           } else {
            $tmp_src->removeloop('block_delete');
           }
           
           $block_no       = new Template($this->viewroot, $tmp_src->returnloop('block_no'));
           $block_no=$block_no->output();
           
           $block_norows   = new Template($this->viewroot, $tmp_src->returnloop('block_norows'));
           $block_norows=$block_norows->output();
           
           $loop_table_row = new Template($this->viewroot, $tmp_src->returnloop('loop_table_row'));
           
           $block_dir = array();
           $block_dir[0]     = new Template($this->viewroot, $tmp_src->returnloop('block_dir0'));
           $block_dir[1]     = new Template($this->viewroot, $tmp_src->returnloop('block_dir1'));
           
           $block_dir[0] = $block_dir[0]->output();
           $block_dir[1] = $block_dir[1]->output();
           
           $loopdata = "";
           
           $cols = $loop_table_row->getVariables();
//           ajax_echo_r ($cols);
           
           $emptyinfo = file_get_contents($this->viewroot.'/templates/emptyinfo.htt');
           $c = 0;
           
           $thistable = "";
           if (sizeof($table[1])) {
            $thisidcolumnname = $json->tablename."_ID";
    //          echo $thisidcolumnname."<br>";
            foreach ($table[1] as $item) {
             $loop_table_row->reload();
             $thisrow   = "";
             
             $loop_table_row->fill(           "%ID%", $item->ID);
             $loop_table_row->fill( "%calldiretion%", $block_dir[$item->DirectionID]);
//             $loop_table_row->fill("%c%",  ($c%2) + (((date_timestamp_get(date_create($item->LastAccess)) + 3600*72)>date_timestamp_get(date_create()) )?0:2) );
             $loop_table_row->fill("%c%",  $c%2);
             
             foreach ($cols as $cv) {
              $k=$cv;
              $v = $item->$k;
              if ($k && ($k!='CustomerID')) {
               if ($k==$json->tablename."_AuxInfo") {
                if (trim($v)=="") $v=$emptyinfo;
               }
               
               $loop_table_row->fill(  "%".$k."%", (((int)$v==0) && ((string)(int)$v==$v) && ($k!='Cost'))?$block_no:($v));
              }
             }
             $loop_table_row->fillloop('loop_table_cell',$thisrow);
             
//             echo (date_timestamp_get(date_create($item->DateTarget))."-".date_timestamp_get(date_create())."<br>");
             
             $loop_table_row->fill("%id%"           , $item->ID             );
             $loop_table_row->fill("%tablename%"    , $json->tablename      );
             
             $thistable.=$loop_table_row->output();
             $c++;
            }
           } else {
            /*
            $thisrow   = "";
            $thisrow.=$block_norows;
            $loop_table_row->fillloop('loop_table_cell',$thisrow);
            $loop_table_row->fill("%c%",  $c%2);
            $thistable.=$loop_table_row->output();
            */
            $tmp_src->fillloop('loop_table', $block_norows);
           }
           
           $tmp_src->removeloop('loop_table');
           
           $tmp_src->fillloop('loop_table_row',$thistable);
           
           $tmp_src->fillloop(     "block_no","" );
           $tmp_src->fillloop( "block_norows","" );
           $tmp_src->fillloop(   "block_dir0","" );
           $tmp_src->fillloop(   "block_dir1","" );
           
           
           $tmp_src->fill("%numrows%",sizeof($table[1]));          
           
          break;
          default:
           $looptmp = new Template($this->viewroot, $tmp_src->returnloop('loop_table'));
           $loop_table_row = new Template($this->viewroot, $tmp_src->returnloop('loop_table_row'));
           $vars = $loop_table_row->getVariables();
           
           $block_no       = new Template($this->viewroot, $tmp_src->returnloop('block_no'));
           $block_no=$block_no->output();
           
           $c = 0;
           
           $thistable = "";
           if (sizeof($table[1])) {
            $thisidcolumnname = $json->tablename."_ID";
            foreach ($table[1] as $item) {
             $loop_table_row->reload();
             $loop_table_row->fill("%c%",  $c%2);
             
             foreach ($vars as $vn) {
              if ($vn!=="Children") {
               $loop_table_row->fill("%".$vn."%", ($item->$vn)?$item->$vn:$block_no);
              }
             }
             
             $thistable.=$loop_table_row->output();
             $c++;
            }
           } else {
            $block_norows = new Template($this->viewroot, $tmp_src->returnloop('block_norows'));
            $thistable = $block_norows->output();
           }
           
           $looptmp->fillloop(  'loop_table_row', $thistable);
           $tmp_src->fillloop(      "loop_table", $looptmp->output());
           $tmp_src->fillloop(    "block_norows", "" );
           $tmp_src->fillloop(        "block_no", "" );
           
           $tmp_src->fill("%numrows%",sizeof($table[1]));
          break;
         }
         
         $tmp_src = $this->fillStricts($tmp_src);
         $tmp_src->processfcb('');
         $tmp_src->fill(  '%imgfolder%' , $this->imgfolder                    );
         $tmp_src->fill(   '%viewroot%' , $this->viewroot                     );
         
         ajax_echo(sup($tmp_src->output()));
        }
       break;
       
       case ('clearColumn'):
        echo $this->model->clearColumn($json->id);
       break;
       
       
       
       
       case ("setListID"):
        $this->model->setListID($json);
       break;
       
       case ("addTask"):
        $json->AddedBy = $userid;
 //       echo $userid;
        $this->model->addTask($json);
       break;
       
       case ("deleteTask"):
        $this->model->deleteTask($json);
       break;
       
       case ("resumeTask"):
        $this->model->resumeTask($json);
       break;
       
       case ("pauseTask"):
        $this->model->pauseTask($json);
       break;
       
       
       
       
       
       case ('newProject'):
//        ajax_echo_r($json);
        $leftcontent = new Template($this->viewroot, "editor_projects.htt");
        if ($json->ID) {
         $rec = $this->model->getProject($json->ID);
         $leftcontent->fill('%mode%', 'edit');
        } else {
         $rec = new stdClass;
         $rec->TypeID   = 1;
         $rec->SendSMS  = 1;
         $rec->HasDoors = 1;
         $leftcontent->fill('%mode%', 'create');
        }
        
//        ajax_echo_r ($rec);
        
//        $leftcontent->processfcb();
        
        $leftcontent = $this->fillStricts($leftcontent, $json->ID);
        
        if (!$rec->City        ) $rec->City        = "Санкт-Петербург";
        if (!$rec->DateFinish  ) $rec->DateFinish  = date('Y-m-d', strtotime('+2 weeks'));   // date_add dateadd
        if (!$rec->DateMeasure ) $rec->DateMeasure = date('Y-m-d H:i:s');
        if (!$rec->DateInstall ) $rec->DateInstall = date('Y-m-d H:i:s');
        
//        ajax_echo_r ($rec);
        
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_users"));
        $list = $this->model->getUsersLst();
        $loopdata = $this->fillList($list, $loop, $rec->UserID);
        $leftcontent->fillloop('loop_users', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("block_Customer"));
        $loopdata = $this->fillList($list, $loop, $rec->CustomerID);
        $leftcontent->fillloop('block_Customer', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("block_AssignedTo"));
        $loopdata = $this->fillList($list, $loop, $rec->AssignedTo);
        $leftcontent->fillloop('block_AssignedTo', $loopdata);
        
        
        
        $loopdata = $this->getParticipants($json);
        $leftcontent->fillloop('block_participants', $loopdata);
        
//        ajax_echo_r ($json);
//        ajax_echo_r ($rec);
//        ajax_echo_r ($this->userdetails);
        $json->IsFull = $this->getIsFull();
        $loopdata = $this->getProjectSteps($json);
        $leftcontent->fillloop('loop_step', $loopdata);
        $leftcontent->fillloop('loop_stepa', '');
        
//        echo htmlentities($loopdata);
        
        $vars = $leftcontent->getVariables();
        
        foreach ($vars as $var) {
         if ($var) {
          $leftcontent->fill(       "%".$var."%" , $rec->$var);
         }
        }
        
        $leftcontent->processfcb('');
        $leftcontent->fill(  '%imgfolder%' , $this->imgfolder                    );
        $leftcontent->fill(   '%viewroot%' , $this->viewroot                     );
        ajax_echo (localize($leftcontent->output()));        
        
//        $users=$this->model->getList('users');
       break;
       
       case ('closeProject'):
//        ajax_echo_r ($json);
        $ret = $this->model->closeProject($json);
        ajax_echo_r ($ret);
       break;
       
       case ('addProject'):
        $json->DateMeasure = dateTimeAdaptWrite($json->DateMeasure);
        $json->DateInstall = dateTimeAdaptWrite($json->DateInstall);
        if (!$json->ID) {
         $json->AddedBy   = $this->userdetails->ID;
         $json->DateAdded = date("Y-m-d H:i:s");
        }
        
//        ajax_echo_r ($json);
//        if (substr_count($json->DateMeasure,":")==1) $json->DateMeasure.=":00";
//        if (substr_count($json->DateInstall,":")==1) $json->DateInstall.=":00";
        
        $ret = new stdClass;
        
        if ((gettype($json->Firstname)!="NULL") && ($json->Firstname=='')) {
         $ret->message = $this->getMessage('addproject_error_nofirstname');
         $ret->result = 0;
        } elseif ($json->lastid=='div_Email'       && !$json->City        ) {
         $ret->message = $this->getMessage('addproject_error_nocity');
         $ret->result = 0;
        } elseif ($json->lastid=='div_OrderNumber' && (!$json->PhoneMain || $json->PhoneMain=='+7(___) ___-____')   ) {
         $ret->message = $this->getMessage('addproject_error_nophone');
         $ret->result = 0;
        } elseif ($json->lastid=='div_Email'       && !$json->Street      ) {
         $ret->message = $this->getMessage('addproject_error_nostreet');
         $ret->result = 0;
        } elseif ($json->lastid=='div_OrderNumber' && !$json->PhoneMain   ) {
         $ret->message = $this->getMessage('addproject_error_nophone');
         $ret->result = 0;
        } elseif ($json->lastid=='div_DateFinish'  && !$json->OrderNumber ) {
         $ret->message = $this->getMessage('addproject_error_noordernumber');
         $ret->result = 0;
        } elseif ($json->lastid=='div_Doors'       && !$json->Cost        ) {
         $ret->message = $this->getMessage('addproject_error_nocost');
         $ret->result = 0;
        } elseif ((date_create($json->DateTarget)) && ((int)date_format(date_create($json->DateTarget), 'Y')<1985)) {
         $ret->message = $this->getMessage('addmoney_error_nodate');
         $ret->result = 0;
        } else {
         if ($json->ID) {
          $rec = $this->model->getProject($json->ID);
//          echo gettype($json->Cost);
//          ajax_echo_r ($json);
          if ($json->PhoneMain) $rec->PhoneMain = $json->PhoneMain;
          if ($json->Email    ) $rec->Email     = $json->Email;
//          ajax_echo_r ($rec);
          
          if (
           (!$rec->IsCompleted) && 
           (
            ($json->lastid=='div_Finish') ||
            ($json->lastid=='0')
           )
          ) {
           
//           echo "send now<br>";
           $json->IsCompleted = 1;
           
           $params = new stdClass;
           $params->ProjectID = $json->ID;
           $params->StepID = 1;
           $params->DateTarget = date("Y-m-d H:i:s");
           $this->model->addProjectStep($params);
           
           $this->sendStepMessages(1, $rec);
           
           
          }
          
         }
         
         unset ($json->lastid);
         $r = $this->model->addProject($json, $userid);
         if ($r) {
          $ret->message = $this->getMessage('addmoney_success');
          $ret->result = 1;
         } else {
          $ret->message = $this->getMessage('addmoney_error_unknown');
          $ret->result = 0;
         }
        }
        $ret->ID = $r;
        echo json_encode($ret);
        
       break;
       
       case ('deleteProject'):
        $this->model->deleteProject($json);
       break;
       
       case ('addComment'):
        $json->UserID = $userid;
        ajax_echo_r ($json);
        $ret = $this->model->addComment($json);
        ajax_echo_r ($ret);
        
       break;
       
       case ('getComments'):
 //       ajax_echo_r($json);
 //       ajax_echo_r (htmlentities($this->getComments($json)));
        ajax_echo (($this->getComments($json)));
        
       break;
       
       case ('deleteComment'):
 //       ajax_echo_r($json);
        $ret = $this->model->deleteComment($json);
 //       ajax_echo_r($ret);
        echo $this->getComments($ret);
        
       break;
       
       
       
       
       
       
       case ('loadProjectParticipants'):
//        ajax_echo_r($json);
        echo $this->getParticipants($json);
       break;
       
       case ('addProjectParticipant'):
        $this->model->addProjectParticipant($json);
        echo $this->getParticipants($json);
       break;
       
       case ('removeProjectParticipant'):
//        ajax_echo_r($json);
        $this->model->removeProjectParticipant($json);
        echo $this->getParticipants($json);
       break;
       
       
       
       case ('addProjectStep'):
        if ($json->StepID) {
         $json->DateTarget = date("Y-m-d H:i:s");
         
         $step = $this->model->getStep($json->StepID);
         
 //        ajax_echo_r ($json);
 //        ajax_echo_r ($step);
         
         $success = 1;
         
         $usr = $this->model->getCustomerFromProject($json->ProjectID);
         $project = $this->model->getProject($json->ProjectID);
         if ($json->DateMeasure || $json->DateInstall) {
          if ($json->DateMeasure) {
           $project->DateMeasure = $json->DateMeasure;
           unset ($json->DateMeasure);
          }
          if ($json->DateInstall) {
           $project->DateInstall = $json->DateInstall;
           unset ($json->DateInstall);
          }
          
          $r = $this->model->addProject($project, $userid);
          
         }
         
         if (($step->NeedDate)) {
          switch ($step->ID) {
           case (2):
            $project->DateAux = dateAdaptRead($project->DateMeasure).", ".$daynames[dayWeekAdaptRead($project->DateMeasure)];
            $project->TimeAux = timeAdaptRead($project->DateMeasure);
           break;
           case (13):
            $project->DateAux = dateAdaptRead($project->DateInstall).", ".$daynames[dayWeekAdaptRead($project->DateInstall)];
            $project->TimeAux = timeAdaptRead($project->DateInstall);
           break;
          }
          
          if (($project->DateAux==0) || ($project->DateAux=='')) {
           switch ($step->ID) {
            case (2):
             echo localize($this->getMessage('error_nodatemeasure'));
            break;
            case (13):
             echo localize($this->getMessage('error_nodateinstall'));
            break;
           }
           $success = 0;
          }
         }
         
         if ($step->NeedImg) {
          $rslt = $this->moveUploads('steps', $json);
 //         echo $rslt;
          
          if ($rslt==0) {
           $success = 0;
           echo localize($this->getMessage('error_nopictures'));
          }
         }
         
 //        ajax_echo_r ($usr);
         
         if ($success) {
          switch ($step->ID) {
           case (14):
            $project->DateFinished = date("Y-m-d H:i:s");
            unset ($project->DateAux);
            unset ($project->TimeAux);
            $r = $this->model->addProject($project, $userid);
           break;
          }
          
          $this->model->addProjectStep($json);
          
          $this->sendStepMessages($step->ID, $project);
          
         }
        }
        
        $json->IsFull = $this->getIsFull();
        echo $this->getProjectSteps($json);
       break;
       
       
       
       case ('api'):
        $method = getvariablereq('method');
        
        switch ($method) {
         case ('getCurrentProjects'):
          $json = new stdClass;
          $json->tablemode = 0;
          $json->isexport = 0;
          $json->go = "projects";
          $json->r_projectsview = "current";
          $json->tablename = "projects";
          $json->isapi = 1;
          
          $table = $this->model->getTable($json);
          
         break;
         default:
          echo "Unknown api method: ".$method;
         break;
        }
        
        echo json_encode($table);
        
       break;
       
       
       
       case ('loadUserPrivileges'):
        echo $this->getUserPrivileges($json);
       break;
       
       case ('addUserPrivilege'):
        $this->model->addUserPrivilege($json);
        echo $this->getUserPrivileges($json);
       break;
       
       case ('removeUserPrivilege'):
        $this->model->removeUserPrivilege($json);
        echo $this->getUserPrivileges($json);
       break;
       
       
       case ('selectChange'):
//        ajax_echo_r($json);
        $ret = new stdClass;
        $ret->debug = ajax_return_r($json);
        
        switch ($json->id) {
         case ('TypeID'):
          $leftcontent = new Template($this->viewroot, 'left_money.htt');                                      // load common parent template
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_groupids"));
          $list = $this->model->getExpenditureGroups($json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_groupids', $loopdata);
          $ret->GroupID = localize($loopdata);
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placenames"));
          $list = $this->model->getPlaceNames($json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_placenames', $loopdata);
          $ret->PlaceName = localize($loopdata);
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placetypes"));
          $list = $this->model->getPlaceTypes($json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_placetypes', $loopdata);
          $ret->PlaceType = localize($loopdata);
         break;
         
         case ('GroupID'):
          $leftcontent = new Template($this->viewroot, 'left_money.htt');                                      // load common parent template
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placenames"));
          $list = $this->model->getPlaceNames(0, 0, $json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_placenames', $loopdata);
          $ret->PlaceName = localize($loopdata);
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placetypes"));
          $list = $this->model->getPlaceTypes(0, 0, $json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_placetypes', $loopdata);
          $ret->PlaceType = localize($loopdata);
         break;
         
         case ('PlaceType'):
          $leftcontent = new Template($this->viewroot, 'left_money.htt');                                      // load common parent template
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placenames"));
          $list = $this->model->getPlaceNames(0, $json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_placenames', $loopdata);
          $ret->PlaceName = localize($loopdata);
         break;
         
         case ('PlaceName'):
          $leftcontent = new Template($this->viewroot, 'left_money.htt');                                      // load common parent template
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placetypes"));
          $list = $this->model->getPlaceTypes(0, $json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
//          $leftcontent->fillloop('loop_placetypes', $loopdata);
          $ret->PlaceType = localize($loopdata);
         break;
         
         case ('CustomerTypeID'):
          $leftcontent = new Template($this->viewroot, 'left_objects.htt');                                      // load common parent template
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_customersources"));
          $list = $this->model->getCustomerSources($json->value);
          $loopdata = $this->fillList($list, $loop, 0);
          $ret->SourceID = localize($loopdata);
          
          $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_customersubtypes"));
          $list = $this->model->getCustomerSubtypes($json->value);
//          ajax_echo_r ($list);
          $loopdata = $this->fillList($list, $loop, 0);
          $ret->CustomerSubtypeID = localize($loopdata);
         break;
        }
        
        echo json_encode($ret);
       break;
       
       
       case ('newMoney'):
//        ajax_echo_r ($json);
        if ($json->ID) {
         $rec = $this->model->getMoney($json->ID);
        } else {
         $rec = new stdClass;
         $rec->TypeID=1;
        }
//        ajax_echo_r ($rec);
        
        $leftcontent = new Template($this->viewroot, "editor_money.htt");
//        $leftcontent->loadloop('block_popupdefault','');
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_typeids"));
        $list = $this->model->getList('moneyrecordtypes');
//        ajax_echo_r ($list);
//        ajax_echo_r ($rec);
        $loopdata = $this->fillList($list, $loop, $rec->TypeID);
        $leftcontent->fillloop('loop_typeids', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_groupids"));
        $list = $this->model->getExpenditureGroups($rec->TypeID);
//         ajax_echo_r ($list);
        $loopdata = $this->fillList($list, $loop, $rec->GroupID);
        $leftcontent->fillloop('loop_groupids', $loopdata);
        
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_customersources_objects"));
        $list = $this->model->getCustomerSources(1);
//        ajax_echo_r ($list);
        $loopdata = $this->fillList($list, $loop, $rec->SourceID);
        $leftcontent->fillloop('loop_customersources_objects', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_customersources_customers"));
        $list = $this->model->getCustomerSources(2);
//        ajax_echo_r ($list);
        $loopdata = $this->fillList($list, $loop, $rec->SourceID);
        $leftcontent->fillloop('loop_customersources_customers', $loopdata);
        
        
        /*
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_months"));
        $list = $this->model->getMonths('money','DateAdded');
        $loopdata = $this->fillList($list, $loop, 0);
        $leftcontent->fillloop('loop_months', $loopdata);
        */
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_objects"));
        $list = $this->model->getObjects(false, 1);
//        ajax_echo_r ($list);
        $loopdata = $this->fillList($list, $loop, $rec->ObjectID);
        $leftcontent->fillloop('loop_objects', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_customers"));
        $list = $this->model->getCustomers();
//        ajax_echo_r ($list);
        $loopdata = $this->fillList($list, $loop, $rec->CustomerID);
        $leftcontent->fillloop('loop_customers', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_users"));
        $list = $this->model->getUsersLst();
        $loopdata = $this->fillList($list, $loop, $rec->UserID);
        $leftcontent->fillloop('loop_users', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placenames"));
        $list = $this->model->getPlaceNames($rec->TypeID);
        $loopdata = $this->fillList($list, $loop, $rec->PlaceName, "PlaceName");
        $leftcontent->fillloop('loop_placenames', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_placetypes"));
        $list = $this->model->getPlaceTypes($rec->TypeID);
        $loopdata = $this->fillList($list, $loop, $rec->PlaceType, "PlaceType");
        $leftcontent->fillloop('loop_placetypes', $loopdata);
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_accounts"));
        $list = $this->model->getListOrdered('accounts', 'ID');
        $loopdata = $this->fillList($list, $loop, $rec->AccountID);
        $leftcontent->fillloop('loop_accounts', $loopdata);
        
        
//        $leftcontent
        $vars = $leftcontent->getVariables();
        
        foreach ($vars as $var) {
         if ($var) {
          $leftcontent->fill(       "%".$var."%" , $rec->$var);
         }
        }
        
        $leftcontent->fill(  '%imgfolder%' , $this->imgfolder                    );
        $leftcontent->fill(   '%viewroot%' , $this->viewroot                     );
        $leftcontent->processfcb('');
        ajax_echo (localize($leftcontent->output()));
       break;
       
       case ('addMoney'):
        $ret = new stdClass;
//        if (date_create($json->DateAdded)) {
//         ajax_echo_r (date_create($json->DateAdded));
//         echo "test: ".(date_format(date_create($json->DateAdded), 'Y'));
//        }
        
//        return 1;
        if (floatval($json->Value)<=0) {
         $ret->message = $this->getMessage('addmoney_error_novalue');
         $ret->result = 0;
        } elseif ((date_create($json->DateAdded)) && ((int)date_format(date_create($json->DateAdded), 'Y')<1985)) {
         $ret->message = $this->getMessage('addmoney_error_nodate');
         $ret->result = 0;
        } else {
         if ($this->model->addMoney($json)) {
          $ret->message = $this->getMessage('addmoney_success');
          $ret->result = 1;
         } else {
          $ret->message = $this->getMessage('addmoney_error_unknown');
          $ret->result = 0;
         }
        }
        echo json_encode($ret);
        
       break;
       
       case ('deleteMoney'):
        $this->model->deleteMoney($json);
       break;
       
       
       
       
       case ('newDiary'):
//        ajax_echo_r ($json);
        if ($json->ID) {
         $rec = $this->model->getDiary($json->ID);
        } else {
         $rec = new stdClass;
         $rec->TypeID=1;
        }
        
//        ajax_echo_r ($rec);
        
        $leftcontent = new Template($this->viewroot, "left_diary.htt");
        $leftcontent->loadloop('block_popupdefault','');
        
        $vars = $leftcontent->getVariables();
        
        foreach ($vars as $var) {
         if ($var) {
          $leftcontent->fill(       "%".$var."%" , brtonl($rec->$var));
         }
        }
        
        $leftcontent->fill(  '%imgfolder%' , $this->imgfolder                    );
        $leftcontent->fill(   '%viewroot%' , $this->viewroot                     );
        $leftcontent->processfcb('');
        ajax_echo (localize($leftcontent->output()));
       break;
       
       case ('addDiary'):
        $ret = new stdClass;
        if (($json->Description)=='') {
         $ret->message = $this->getMessage('addmoney_error_novalue');
         $ret->result = 0;
        } elseif ((date_create($json->DateTarget)) && ((int)date_format(date_create($json->DateTarget), 'Y')<1985)) {
         $ret->message = $this->getMessage('addmoney_error_nodate');
         $ret->result = 0;
        } else {
         if ($this->model->addDiary($json)) {
          $ret->message = $this->getMessage('addmoney_success');
          $ret->result = 1;
         } else {
          $ret->message = $this->getMessage('addmoney_error_unknown');
          $ret->result = 0;
         }
        }
        echo json_encode($ret);
        
       break;
       
       case ('deleteDiary'):
        $this->model->deleteDiary($json);
       break;
       
       
       
       
       
       
       
       
       
       
       
       
       
       
       case ('newUser'):
        $leftcontent = new Template($this->viewroot, "left_users.htt");
        $leftcontent->loadloop('block_popupdefault','');
        
        if ($this->userdetails->GroupID==1) {
         $leftcontent->removeloop('block_adminonly2');
        } else {
         $leftcontent->fillloop('block_adminonly2','');
        }
        
        
        if ($json->ID) {
         $rec = $this->model->getUser($json->ID);
         $leftcontent->removeloop('block_editonly');
        } else {
         $rec = new stdClass;
         $rec->TypeID=1;
         $leftcontent->fillloop('block_editonly', '');
        }
        
        
        
        $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_usergroups"));
        $list = $this->model->getList('usergroups');
//        ajax_echo_r ($list);
//        ajax_echo_r ($rec);
        $loopdata = $this->fillList($list, $loop, $rec->GroupID);
        $leftcontent->fillloop('loop_usergroups', $loopdata);
        
        
        
        $leftcontent->fillloop('block_p','');
        
        $vars = $leftcontent->getVariables();
        
        foreach ($vars as $var) {
         if ($var) {
          $leftcontent->fill(       "%".$var."%" , brtonl($rec->$var));
         }
        }
        
        $leftcontent->processfcb('');
        $leftcontent->fill(  '%imgfolder%' , $this->imgfolder                    );
        $leftcontent->fill(   '%viewroot%' , $this->viewroot                     );
        ajax_echo (localize($leftcontent->output()));
       break;
       
       case ('addUser'):
        $ret = new stdClass;
        
//        if (($json->Username=='') && ($this->userdetails->ID!=1)) {
//         $ret->message = $this->getMessage('adduser_error_nousername');
//         $ret->result = 0;
//        } elseif (($json->Email=='') && ($this->userdetails->ID!=1)) {
//         $ret->message = $this->getMessage('adduser_error_noemail');
//         $ret->result = 0;
//        } else {
//         ajax_echo_r ($json);
         
         $d = dateAdaptWrite($json->DateBirth);
         if ($d!="1970-01-01") {
          $json->DateBirth = $d;
         }
         
         $d = dateAdaptWrite($json->DateRemoved);
         if ($d!="1970-01-01") {
          $json->DateRemoved = $d;
         }
         
//         ajax_echo_r ($json->DateBirth);
         
         if ($this->model->addUser($json)) {
          $ret->message = $this->getMessage('addmoney_success');
          $ret->result = 1;
         } else {
          $ret->message = $this->getMessage('addmoney_error_unknown');
          $ret->result = 0;
         }
//        }
        echo json_encode($ret);
       break;
       
       case ('deleteUser'):
        $this->model->deleteUser($json);
       break;
       
       case ('updatePassword'):
        $ret = new stdClass;
        if ($json->Password=='') {
         $ret->message = $this->getMessage('updatepassword_error_nopassword');
         $ret->result = 0;
        } else {
         if ($this->model->updatePassword($json)) {
          $ret->message = $this->getMessage('updatepassword_success');
          $ret->result = 1;
         } else {
          $ret->message = $this->getMessage('addmoney_error_unknown');
          $ret->result = 0;
         }
        }
        echo json_encode($ret);
       break;
       
       
       
       
       
       
       
       
       case ("savefield"):
        $this->model->saveField($json);
       break;
       
       
       
       
       
       
       
       
       
       
       
       case ('addStatus'):
//        ajax_echo_r ($json);
        
        if ($json->ParentID) {
         if ($json->Address && $json->Comment) {
          $ret = $this->model->addStatus($json);
//          ajax_echo_r ($ret);
         }
         
         $leftcontent = new Template($this->viewroot, "editor_objects.htt");
         $typepicture = array();
         $typepicture[1] = $leftcontent->returnloop('block_typepicture_1');
         $typepicture[2] = $leftcontent->returnloop('block_typepicture_2');
         $typepicture[3] = $leftcontent->returnloop('block_typepicture_3');
         $typepicture[4] = $leftcontent->returnloop('block_typepicture_4');
         $typepicture[5] = $leftcontent->returnloop('block_typepicture_5');
         $typepicture[6] = $leftcontent->returnloop('block_typepicture_6');
         
         $leftcontent->loadloop('block_statuses','');
         
         $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_statuses"));
         $list = $this->model->getStatuses($json->ParentID, $json->ParentName);
         foreach ($list as $item) {
          $item->typepicture = $typepicture[$item->TypeID];
         }
         $loopdata = $this->fillList($list, $loop);
         $leftcontent->fillloop('loop_statuses', $loopdata);
         
         $leftcontent->fillloop('block_typepicture_1', '');
         $leftcontent->fillloop('block_typepicture_2', '');
         $leftcontent->fillloop('block_typepicture_3', '');
         $leftcontent->fillloop('block_typepicture_4', '');
         $leftcontent->fillloop('block_typepicture_5', '');
         $leftcontent->fillloop('block_typepicture_6', '');
         
         $leftcontent->fill(  '%imgfolder%' , $this->imgfolder                    );
         $leftcontent->fill(   '%viewroot%' , $this->viewroot                     );
         $leftcontent->processfcb('');
         ajax_echo (localize($leftcontent->output()));
        }
       break;
       
       case ('fillSelect'):
        $tmp = new Template($this->viewroot, 'left_objects.htt');
        $t   = new Template($this->viewroot, $tmp->returnloop("loop_addr"));
        
        $req = "http://rosreestr.ru/api/online/regions/".$json->elsid;
        $f = file_get_contents($req);
        $j = json_decode($f);
        
        sortbyname ($j);
        
        $loopitems = $this->fillSelect($t, $j);
//        $t = new Template($this->viewroot, $tmp->returnloop("loop_addr_default"));
//        $loop_addr_default = $t->output();
        
        echo localize($loopitems);
       break;
       
       case ('ownlogin'):
        $key = getvariablereq('key').getvariablereq('k');          // get JSON data from the request
        $login = $this->model->ownLogin($key);
        
        if ($login) {
         $tmp = new Template($this->viewroot, 'redir.htt');
         $tmp->fill('%Link%', '?go=users');
         
         setsecurevariable('userid',$login->ID);
         setsecurevariable('tmpcnt',0         );
         
        } else {
         $tmp = new Template($this->viewroot, 'wrong_key.htt');
        }
        
        echo localize($tmp->output());
       break;
       
       case ('sendEmail'):
        $user = $this->model->getUser($json->ID);
        
        $tmp = new Template($this->viewroot, 'mail1.htt');
        $tmp->fill(  "%LoginKey%", $user->LoginKey  );
        $tmp->fill( "%Firstname%", $user->Firstname );
        
        $mail = localize($tmp->output());
        
        $is_sent = sendmail("Welcome to Alchemy", $mail, $user->Email);
        
        echo "Emails sent: ".$is_sent."<br>";
        
       break;
       
       case ('setUserStatus'):
        if ($json->ID) {
         $sta = $this->model->setUserStatus($this->userdetails->ID, $json->ID);
        }
        $sta = $this->model->getUserStatus($this->userdetails->ID);
        
        $tmp = new Template($this->viewroot, 'adminparent.htt');                                       // load common parent template
        
        $tmp->loadloop('block_status_'.$sta,'');
        
        $tmp->processfcb('');
        $tmp->fill(  '%imgfolder%' , $this->imgfolder                    );
        $tmp->fill(   '%viewroot%' , $this->viewroot                     );
        ajax_echo (localize($tmp->output()));
       break;
       
       
       
       
       case ('getBalance'):
        $bal = $this->model->getBalance();
        
        $tmp = new Template($this->viewroot, 'left_money.htt');                                       // load common parent template
        
        $tmp->loadloop('block_balance');
        $tmp->fill('%balance%', $bal);
        
        $tmp->processfcb('');
        $tmp->fill(  '%imgfolder%' , $this->imgfolder                    );
        $tmp->fill(   '%viewroot%' , $this->viewroot                     );
        ajax_echo (localize($tmp->output()));
       break;
       
       case ('exportToExcel'):
        echo $this->model->backup();
       break;
       
       
       
       case ('deleteAttachment'):
        $json->filename = $json->id;
        if (strpos($json->id,"?")) {
         $json->filename = substr($json->id,0,strpos($json->id,"?"));
        }
        
//        ajax_echo_r ($json);
        
        unlink ($json->filename);
        unlink (str_replace("full_", "thb_", $json->filename));
//        unlink (str_replace("full_", "dst_", $json->filename));
        
        
       case ('fullUploads'):
        $action = "steps";
        $ajax_fileupload_img_str = $this->fillAttachments($action, $this->settings->temppath."/", session_id());
        echo $ajax_fileupload_img_str;
       break;
       
       
       default:                                                                   // for me if I miss something.
        ajax_echo ("Unknown AJAX action: ".$action);
       break;
       
      }
     } else {
      echo "You need to login again.";
      
     }
    } else {                                                                        // for plain HTML (not an Ajax)
     $privileges_this = $this->model->getUserPrivileges($userid);
//     ajax_echo_r ($privileges_this);
//     ajax_echo_r ($this->userdetails);
//     ajax_echo_r ($userid);
     
     $mode = getvariablereq ('mode', 0);
//     echo $mode;
//     echo $userid;
//     echo $this->userdetails->usergroup->ID>0;
//     echo !$this->userdetails->usergroup;
//     echo sizeof($privileges_this);
     
     if (((sizeof($privileges_this)!=0) || ($userid==-1) || ($userid==-2 && !$this->userdetails->usergroup) || ($this->userdetails->usergroup->ID>0)) && ($mode==0)) {         // if the admin wants to see his admin-panel
      
      $id      = getvariablereq('id'     );
      $auxmode = getvariablereq('auxmode');
      
//      echo "isadmin: ".$isadmin;
      
      if ($isadmin) {
       $go      = getvariablereq('go');
       if (!$go) $go="projects";
       
       $tmp = new Template($this->viewroot, 'adminparent.htt');                                      // load common parent template
      } else {
       if ($userid>-1) {
        $go="projects";
       } else {
        $go="login";
       }
       
       $tmp = new Template($this->viewroot, 'managerparent.htt');                                    // load common parent template
      }
      
      $tmp->fill(     "%Title%", $this->userdetails->usergroup->Title   );
      
      $tmp->fillloop('block_status_1','');
      $tmp->fillloop('block_status_2','');
      
      if ($this->userdetails->usergroup->Password) {
       $privileges_all  = $this->model->getUserPrivileges(0);
       
       if (!$privileges_this[$go]) $go=array_keys($privileges_this)[0];
       $allowed = 0;
       
       foreach ($privileges_this as $p) {
        $tmp->removeloop('block_link_'.$p->PageName);
        if ($go == $p->PageName) $allowed = 1;
       }
       
       foreach ($privileges_all as $p) {
        $tmp->fillloop('block_link_'.$p->PageName, '');
       }
       
       if (!$allowed) {
        $go = $privileges_this[0]->PageName;
       }
      }
      if (!$go) $go="projects";
      
      $loop_clean = clone $tmp;
      
      if ($userid>-1) {
       $tmp_user_info = clone $loop_clean;
       $tmp_user_info->loadloop("loop_user_info");
       
       $tmp_user_info->fill(  "%username%",$this->userdetails->Username  );
       $tmp_user_info->fill( "%Firstname%",$this->userdetails->Firstname );
       $tmp_user_info->fill(   "%Surname%",$this->userdetails->Surname   );
       
       $loop_user_auth=$tmp_user_info->output();
       
       $tmp->removeloop("loop_globalparent");
       $tmp->fillloop("loop_user_auth"      , $loop_user_auth );
       $tmp->fillloop("loop_user_info"      , ""              );
       
       
       
       
       if ($isadmin) {
        $leftcontent = new Template($this->viewroot, 'left_'  .$go.'.htt');                                    // load common parent template
        $maincontent = new Template($this->viewroot, 'main_'  .$go.'.htt');                                    // load common parent template
       } else {
        $leftcontent = new Template($this->viewroot, 'left_'  .$go.'_manager.htt');                                    // load common parent template
        $maincontent = new Template($this->viewroot, 'main_'  .$go.'_manager.htt');                                    // load common parent template
       }
       
       if ($this->userdetails->GroupID==1) {
        $leftcontent->removeloop('block_adminonly');
       } else {
        $leftcontent->fillloop('block_adminonly', '');
       }
       
       $leftcontent->fill('%today%', date("Y-m-d"));
       
       switch ($go) {
        case ('dashboard'):
         $maincontent->fillloop('block_dashboard', '');
         
         $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_users"));
         $list = $this->model->getUsersLst();
         $loopdata = $this->fillList($list, $loop, $rec->UserID);
         $leftcontent->fillloop('loop_users', $loopdata);
         
        break;
        case ('users'):
         
         
         
        break;
        case ('money'):
         $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_typeids"));
         $list = $this->model->getList('moneyrecordtypes');
         $loopdata = $this->fillList($list, $loop, 0);
         $leftcontent->fillloop('loop_typeids', $loopdata);
         
         $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_groupids"));
         $list = $this->model->getExpenditureGroups(0);
         $loopdata = $this->fillList($list, $loop, 0);
         $leftcontent->fillloop('loop_groupids', $loopdata);
         
         $loop = new Template($this->viewroot, $leftcontent->returnloop("loop_months"));
         $list = $this->model->getMonths('money','DateAdded');
         $loopdata = $this->fillList($list, $loop, 0);
         $leftcontent->fillloop('loop_months', $loopdata);
         
         $leftcontent->fillloop( 'block_popupdefault','' );
         $leftcontent->fillloop(      'block_balance','' );
        break;
        case ('projects'):
         
         
        break;
       }
       
       $tmp->fill("%leftcontent%"      , $leftcontent->output());                                      // fill the template with actual data
       $tmp->fill("%maincontent%"      , $maincontent->output());                                      // fill the template with actual data
       
       $tmp->fill("%fromajax%"                 , 0                   );
       
       $json = getFromCache('settings');
       
       $tmp->fillloop('block_split_none'       ,'');
       $tmp->fillloop('block_split_vertical'   ,'');
       $tmp->fillloop('block_split_horizontal' ,'');
       
       $tmp->fillloop('loop_table'    ,'');
       $tmp->fillloop('block_no'      ,'');
       $tmp->fillloop('block_norows'  ,'');
       $tmp->fillloop("block_limited" ,"");
       
       $params = new stdClass();
       
       $params->tablename   = $objecttype;
       $params->OfferTypeID = "2";
       
       $tmp->fill("%justlogged%"         , $this->userdetails->JustLogged          );        // fill template
       $tmp->fill("%tableview%"          , $this->imgfolder                  );        // fill the template with actual data
       $tmp->fill("%agentname%"          , $this->userdetails->FirstName." ".$this->userdetails->LastName                );        // fill the template with actual data
       
       
       
       
       
      } else {
       $loop_user_auth=$loop_clean->returnloop("loop_user_auth");
       $tmp->fillloop("loop_globalparent", $loop_user_auth);
      }
      
      if ($isadmin) {
       $this->processstylesheet($hue,'admin');
      } else {
       $this->processstylesheet($hue,'manager');
      }
      
      $tmp->fill("%theme%"              , $this->userdetails->Theme                 );
      $tmp->fill('%objecttype%'         , $objecttype                         );
      $tmp->fill('%SmoothAnimation%'    , $SmoothAnimation                    );
      $tmp->fill('%auxmode%'            , $auxmode                            );
      
      if ((int)$this->userdetails->AccountType!=1) {
       $tmp->fillloop("adminpanellink","");
      }
      $tmp->fillloop("block_link","");
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      $tmp->fillloop(     "block_no","" );
      $tmp->fillloop( "block_norows","" );
      $tmp->fillloop(   "block_dir0","" );
      $tmp->fillloop(   "block_dir1","" );
      
      
      
      
      $tmp->fill( "%projectname%", $this->settings->projectname    );        // fill the template with actual data
      $tmp->fill(         '%go%' , $go                                 );
      $tmp->fill(         '%id%' , $id                                 );
      $tmp->processfcb('');
      
      $tmp = $this->fillStricts($tmp);
      
      $tmp->fill(  '%imgfolder%' , $this->imgfolder                    );
      $tmp->fill(   '%viewroot%' , $this->viewroot                     );
      
      
      echo localize($tmp->output());                                              // show it
     } else {
      $tmp = new Template($this->viewroot, 'mainparent.htt');                                       // load common parent template
      $tmp->fill(    "%username%", $this->userdetails->Username         );
      $tmp->fill(   "%Firstname%", $this->projectdetails->Firstname     );
      $tmp->fill(     "%Surname%", $this->projectdetails->Surname       );
      $tmp->fill(          "%ID%", $this->projectdetails->ID            );
      $tmp->fill( "%OrderNumber%", $this->projectdetails->OrderNumber   );
      
//      ajax_echo_r ($this->projectdetails);
      
      $tmp_loop_project = $tmp->returnloop("loop_project");
      $loop_project = new Template($this->viewroot, $tmp_loop_project);
      
//      ajax_echo_r ($this->userdetails);
      if ($this->projectdetails) {
       $projects = array();
       $projects[] = $this->projectdetails;
      } else {
       $projects = $this->model->getProjectsFromCustomer($this->userdetails->ID);
      }
//      ajax_echo_r ($projects);
//      $projects = $this->model->getProjectPictures($projects);
      
      if (!$json) $json = new stdClass;
      $json->IsFull = $this->getIsFull();
      
      $loop_projects = "";
      foreach ($projects as $project) {
       $loop_project->reload();
       $loop_project->fill('%Title%', $project->Street);
       
       $loopdata = $this->getProjectSteps($project, $tmp_loop_project);
       $loop_project->fillloop('loop_step', $loopdata);
       $loop_project->fillloop('loop_stepa', '');
       
       
       $loop_projects.=$loop_project->output();
      }
      
      
      $tmp->fillloop('loop_project', $loop_projects);
      
      
      
      $tmp->fill(         '%go%' , $go                                 );
      $tmp->fill(         '%id%' , $id                                 );
      $tmp->processfcb('');
      
      $tmp = $this->fillStricts($tmp);
      
      $this->processstylesheet($hue,'style');
      $tmp->fill(  '%imgfolder%' , $this->imgfolder                    );
      $tmp->fill(   '%viewroot%' , $this->viewroot                     );
      
      echo localize($tmp->output());                                               // show it
     }
    }
   }
  }
  
  function getstylesheet($hue,$viewroot,$filename) {
   addtolog("Controller getstylesheet begin");
   $style = "";
   $stylefilename = $filename.".less";
   $f = file_get_contents($this->viewroot."/styles/".$stylefilename);
   $f = str_replace(             "%hue%", $hue                           , $f);
   $f = str_replace( "%SmoothAnimation%", $SmoothAnimation               , $f);
   $f = str_replace(    "%previewwidth%", $this->settings->previewwidth  , $f);
   $f = str_replace(   "%previewheight%", $this->settings->previewheight , $f);
   $f = str_replace(       "%imgfolder%", $viewroot."/img"               , $f);
   $f = str_replace(        "%viewroot%", $viewroot                      , $f);
   
   $style.=($this->less->compile($f));
   addtolog("Controller getstylesheet end");
   return ($style);
  }
  
  function processstylesheet($hue,$filename) {
   addtolog("Controller processstylesheet begin");
   file_put_contents($this->viewroot."/styles/".$filename.".css",$this->getstylesheet($hue,$this->viewroot,$filename)        );         // fill the template with compiled less stylesheet
   addtolog("Controller processstylesheet end");
  }
  
  public function import() {                         // import functionality to get data from ISCentre
   addtolog("Controller import begin");
   include_once ("controller/DBImport.php");
   $this->dbimport = new DBImport($this->settings);  // instantiate a class
   
   $action = getvariablereq   ('action');            // get action from the request
   $data   = getvariablereq   ('data'  );            // get JSON data from the request
   
   $data = ajaxdecode($data);              // fix some escaped paths (if any)
   
   $mtime = microtime(true);
   $thisfolder = substr($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'] ,"/"))."/data/temp/dbupdate";
   @mkdirr ($thisfolder);
   
   switch ($action) {
    case ('update'):
     $this->dbimport->importtables($thisfolder);
     echo "Job done\n";
    break;
    default:
     if (sizeof($_FILES)>0) {
      foreach ($_FILES as $file) {
       if (file_exists($thisfolder."/".$file['name'])) {
        unlink($thisfolder."/".$file['name']);
       }
       move_uploaded_file ($file['tmp_name'],$thisfolder."/".$file['name']);
       echo "File accepted";
      }
     }
    break;
   }
   
   $fieldid = array();
   
   addtolog("Controller import end");
  }
  
  public function getComments($json) {
   $Comments = $this->model->getComments($json);
   
   $loop = new Template($this->viewroot, 'main_tasks.htt');                                      // load common parent template
   $block_comment = new Template($this->viewroot, $loop->returnloop('block_comment'));
   $block_comments = "";
   if (sizeof($Comments)) {
    foreach ($Comments as $comment) {
     $block_comment->reload();
     $block_comment->fill('%ID%'        ,$comment->ID        );
     $block_comment->fill('%Comment%'   ,$comment->Comment   );
     $block_comment->fill('%DateAdded%' ,$comment->DateAdded );
     $block_comment->fill('%Username%'  ,$comment->Username  );
     $block_comment->fill('%ParentID%'  ,$comment->ParentID  );
     
     $block_comment->processfcb('');
     $block_comment->fill(  '%imgfolder%' , $this->imgfolder                    );
     $block_comment->fill(   '%viewroot%' , $this->viewroot                     );
     $block_comments .= $block_comment->output();
    }
   }
   return localize($block_comments);
  }
  
  function fillList($list, $loop, $selectedid=0, $key="") {
   $vars = $loop->getVariables();
//   ajax_echo_r ($vars);
//   echo $selectedid."<br>";
   $loopdata = "";
   if (sizeof($list)>0)  {
    foreach ($list as $ik=>$iv) {
     $loop->reload();
     
     if ($key) {
      $loop->fill(      "%selected%",  ($iv->$key==$selectedid)?"selected":"");
      $loop->fill(   "%fcb_checked%",  ($iv->$key==$selectedid)?"1":""       );
     } else {
      $loop->fill(      "%selected%",  ($iv->ID  ==$selectedid)?"selected":"");
      $loop->fill(   "%fcb_checked%",  ($iv->ID  ==$selectedid)?"1":""       );
     }
     
     foreach ($vars as $var) {
      if ($var) {
       $loop->fill(       "%".$var."%" , $iv->$var);
      }
     }
     $loopdata.=$loop->output();
    }
   }
   
   return $loopdata;
  }
  
  function getUserPrivileges($json) {
   $list = $this->model->getUserPrivileges($json->UserID);
//   ajax_echo_r ($list);
   
   $loop_par = new Template($this->viewroot,'left_users.htt');                                        // load common parent template
   $block_none = new Template($this->viewroot, $loop_par->returnloop('block_none'));
   
   $loop_par->loadloop('block_p');
   
   $loop_this = new Template($this->viewroot, $loop_par->returnloop('loop_this'));
   $loop_all  = new Template($this->viewroot, $loop_par->returnloop('loop_all'));
   
   $vars = $loop_this->getVariables();
   $loop = "";
   if (sizeof($list)) {
    foreach ($list as $data) {
     if ($data->Email) {
      $loop_this->reload();
      foreach ($vars as $var) {
       if ($var) {
        $loop_this->fill(       "%".$var."%" , $data->$var);
       }
      }
      $loop.=$loop_this->output();
     }
    }
   }
   if ($loop=='') $loop = $block_none->output();
   $loop_par->fillloop('loop_this', $loop);
   
   $list_all = $this->model->getUserPrivileges();
   $vars = $loop_all->getVariables();
   $loop = "";
   if (sizeof($list_all)) {
    foreach ($list_all as $k=>$data) {
     if (!$list[$k]) {
      $loop_all->reload();
      foreach ($vars as $var) {
       if ($var) {
        $loop_all->fill(       "%".$var."%" , $data->$var);
       }
      }
      $loop.=$loop_all->output();
     }
    }
   }
   $loop_par->fillloop('loop_all', $loop);
   $loop_par->fillloop('block_none', '');
   
   $loop_par->processfcb('');
   $loop_par->fill(  '%imgfolder%' , $this->imgfolder                    );
   $loop_par->fill(   '%viewroot%' , $this->viewroot                     );
   return localize($loop_par->output());                                               // show it
  }
  
  function fillSelect($looptmp, $requests, $selectedID="") {
   $b = "";
   foreach ($requests as $k=>$v) {
    $looptmp->reload();
    $looptmp->fill(          "%id%", $v->id   );
    $looptmp->fill(        "%name%", $v->name );
    $b.=$looptmp->output();
   }
   return $b;
  }
  
  function getParticipants($json) {
   $list_participants = $this->model->getProjectParticipants($json->ProjectID);
   
   $excludeIDs = array();
//   ajax_echo_r ($list_participants);
   $excludeIDs=array_keys($list_participants);
   if (!in_array($json->UserID, $excludeIDs)) $excludeIDs[]=$json->UserID;
   $list_users = $this->model->getNewUsers($excludeIDs);
   
//        ajax_echo_r($list_participants);
//        ajax_echo_r($list_users);
   
//        <block_participants>
   
//        $loop_participants = ($this->fillList($list, 'left_projects.htt', 'loop_participants'));
//        $loop_users        = ($this->fillList($users, 'left_projects.htt', 'loop_users'));
   
   $loop_par = new Template($this->viewroot,'editor_projects.htt');                                        // load common parent template
   $loop_par->loadloop('block_participants');
   
   $loop_participants = clone $loop_par;
   $loop_users = clone $loop_par;
   
   $loop_participants->loadloop('loop_participants');
   $loop_users->loadloop('loop_users');
   
   $vars = $loop_participants->getVariables();
   $loop = "";
   if (sizeof($loop_participants)) {
    foreach ($list_participants as $data) {
     if ($data->Username) {
      $loop_participants->reload();
      foreach ($vars as $var) {
       if ($var) {
        $loop_participants->fill(       "%".$var."%" , $data->$var);
       }
      }
      $loop.=$loop_participants->output();
     }
    }
   }
   $loop_par->fillloop('loop_participants', $loop);
   
   $vars = $loop_users->getVariables();
   $loop = "";
   if (sizeof($list_users)) {
    foreach ($list_users as $data) {
     $loop_users->reload();
     foreach ($vars as $var) {
      if ($var) {
       $loop_users->fill(       "%".$var."%" , $data->$var);
      }
     }
     $loop.=$loop_users->output();
    }
   }
   $loop_par->fillloop('loop_users', $loop);
   
   $loop_par->processfcb('');
   $loop_par->fill(  '%imgfolder%' , $this->imgfolder                    );
   $loop_par->fill(   '%viewroot%' , $this->viewroot                     );
   return localize($loop_par->output());                                               // show it
  }
  
  function getProjectSteps($json, $htt='editor_projects.htt') {
   $loop = "";
   
   $usergroup = $this->userdetails->usergroup;
//   ajax_echo_r ($usergroup);
   $json->IsFull = $this->getIsFull();
//   ajax_echo_r ($json);
   
   if ($json->ID) $json->ProjectID = $json->ID;
   if ((!$json->DateMeasure) || (!$json->DateInstall)) {
    $project = $this->model->getProject($json->ProjectID);
    $json->DateMeasure = $project->DateMeasure;
    $json->DateInstall = $project->DateInstall;
    $json->HasDoors    = $project->HasDoors;
   }
//   ajax_echo_r ($json);
//   ajax_echo_r ($project);
   
   if ($json->ProjectID) {
    $loop_par = new Template($this->viewroot, $htt);                                        // load common parent template
    $loop_par = $this->fillStricts($loop_par);
    
    $loop_par->fill(  '%ProjectID%', $json->ProjectID          );
    $loop_par->fill(  '%imgfolder%', $this->imgfolder          );
    $loop_par->fill(   '%viewroot%', $this->viewroot           );
    
    $loop_step  = new Template($this->viewroot, $loop_par->returnloop('loop_step' ));
    $loop_stepa = new Template($this->viewroot, $loop_par->returnloop('loop_stepa'));
    
    $loop_files  = new Template($this->viewroot, $loop_step->returnloop('loop_files' ));
    $loop_filesa = new Template($this->viewroot, $loop_stepa->returnloop('loop_filesa'));
    
    $list = $this->model->getProjectSteps($json);
    $list = $this->model->getProjectStepsPictures($json, $list);
//    ajax_echo_r ($list);
    
    $prevMode = 'active';
    
    $daynames = getDayNames();
    
    $n = 1;
    $vars  = $loop_step->getVariables();
    $varsa = $loop_stepa->getVariables();
    if (sizeof($list)) {
     foreach ($list as $data) {
//      ajax_echo_r ($data);
      $k = 'Step'.$data->ID;
      if (((($data->ID==12) && ($json->HasDoors)) || ($data->ID!=12)) && ($usergroup->$k>0)) {
//      ajax_echo_r ($data);
       $Auxinfo = "";
       if (($data->NeedDate)) {
        switch ($data->ID) {
         case (2):
          if ($data->Mode=='active') {
           $data->Auxinfo = dateTimeAdaptRead($json->DateMeasure).", ".$daynames[dayWeekAdaptRead($json->DateMeasure)];
          } else {
 //          $data->Auxinfo = "...";
          }
         break;
         case (13):
          if ($data->Mode=='active') {
           $data->Auxinfo = dateTimeAdaptRead($json->DateInstall).", ".$daynames[dayWeekAdaptRead($json->DateInstall)];
          } else {
 //          $data->Auxinfo = "...";
          }
         break;
        }
       }
       
       if (($prevMode!=$data->Mode) && ($usergroup->$k==2)) {
        $prevMode=$data->Mode;
        $loop_stepa->reload();
        
        $loop_stepa->fill('%last%', ($n==sizeof($list)?"hidden":""));
        
        $files_str = "";
        if ($data->Files) {
         foreach ($data->Files as $File) {
          $loop_filesa->reload();
          $filename = $File;
          $loop_filesa->fill(      '%filename%', $filename);
          $loop_filesa->fill( '%filename_full%', str_replace("thb_", "full_", $filename));
          $files_str .= $loop_filesa->output();
         }
        }
        $loop_stepa->fillloop('loop_filesa', $files_str);
        
        foreach ($varsa as $var) {
         if ($var) {
          $loop_stepa->fill(       "%".$var."%" , $data->$var);
         }
        }
        $loop.=$loop_stepa->output();
       } else {
        $loop_step->reload();
        
        $loop_step->fill('%last%', ($n==sizeof($list)?"hidden":""));
        
        
        $files_str = "";
        if ($data->Files) {
         foreach ($data->Files as $File) {
          $loop_files->reload();
          $filename = $File;
          $loop_files->fill(      '%filename%', $filename);
          $loop_files->fill( '%filename_full%', str_replace("thb_", "full_", $filename));
          $files_str .= $loop_files->output();
         }
        }
        $loop_step->fillloop('loop_files', $files_str);
        
        foreach ($vars as $var) {
         if ($var) {
          $loop_step->fill(       "%".$var."%" , $data->$var);
         }
        }
        $loop.=$loop_step->output();
       }
      }
      $n++;
     }
    }
   }
   
   return localize($loop);                                               // show it
  }
  
  function getMessage($id) {
   $tmp = new Template($this->viewroot,'messages.htt');
   $tmp->loadloop("block_".$id);
   $tmp->fill('%imgfolder%', getrootdir().$this->imgfolder);
   return localize($tmp->output());
  }
  
  public function sms() {                                                      // the main sub in our application
   include_once("model/transport.php");       // connect to the Settings 
   
   if(!isset($_SESSION)) session_start();
   
   $action = getvariablereq('action');          // get action from the request
   $data   = getvariablereq('data'  );          // get JSON data from the request
   
   $t = $this->model->getStats('common');
   
   echo "Report date: ".date("Y-m-d H:i:s")."<br>";
   
   $phones_first  = array();	
   $phones_second = array();	
   $users=$this->model->getUsersSms();
   $this->model->resetUsersFirstSms();
   
//   ajax_echo_r ($users);
   
   foreach ($users as $user) {
    if ($user->Phone) {
     $user->Phone = str_replace("+7", "8", $user->Phone);
     $user->Phone = str_replace( "-",  "", $user->Phone);
     
     if ($user->FirstSms) {
      $phones_first[]  .= $user->Phone;
     } else {
      $phones_second[] .= $user->Phone;
     }
    }
   }
   
   $info = "Всего собственников ".$t->objects_total.", из них новых ".$t->objects_yesterday.". Всего покупателей ".$t->customers_total.", из них новых ".$t->customers_yesterday.". ";
//   $info.= "Потенциальных сделок ".$t->handshakes_auto.". ";
   $info.= "Сделок на этой неделе ".$t->handshakes_thisweek." (осталось сделать  ".($t->handshakes_thisweek_plan - $t->handshakes_thisweek).").";
   
   echo $info;
   
   $api = new Transport($this->settings);
   $params_first  = array(
    "text" => "Привет, это СМСка от Изума. ".$info
   );
   $params_second = array(
    "text" => "Доброе утро. Изум-информ сообщает: ".$info
   );
   
//   $phones_first  = array('89376411426');
//   $phones_second = array('89276047754');
   
   ajax_echo_r ($phones_first);
   ajax_echo_r ($phones_second);
   
   $send_first  = $api->send($params_first  ,$phones_first);
   $send_second = $api->send($params_second ,$phones_second);
   
   ajax_echo_r ($send_first);
   ajax_echo_r ($send_second);
   
   if ($send['code'] == 1) {
//    echo 'Отправлено '.$send['colSendAbonent'].', не отправлено';
   } else {
//    echo $send['descr'];
   }
   
   
   
  }
  
  public function backup() {
   $ret = $this->model->backup();
   
   
   
  }
  
  
  
  public function util() {
   $action = getvariablereq   ('action');          // get action from the request
   $data   = getvariablereq   ('data'  );          // get JSON data from the request
   switch ($action) {
    case ('latestversion'):
     $ret = sdir('data/dbconvert','*.zip');
     
     $latestversion = 0;
//     ajax_echo_r ($ret);
     if ($ret) {
      foreach ($ret as $file) {
       $p0 = strpos($file,".")+1;
       $p1 = strrpos($file,".");
       
       $thisversion = substr($file, $p0, $p1-$p0);
       
       $thisver = explode(".",$thisversion);
       $ma = (int)$thisver[0];
       $mi = (int)$thisver[1];
       $re = (int)$thisver[2];
       
       $v = $ma*100000 + $mi*1000 + $re;
       if ($v>$latestversion) $latestversion = $v;
       
      }
      echo $latestversion;
     }
     
    break;
    case ('getlatestversion'):
     $ret = sdir('data/dbconvert','*.zip');
     
     $latestversion = 0;
     
     if ($ret) {
      foreach ($ret as $file) {
       $p0 = strpos($file,".")+1;
       $p1 = strrpos($file,".");
       
       $thisversion = substr($file, $p0, $p1-$p0);
       
       $thisver = explode(".",$thisversion);
       $ma = (int)$thisver[0];
       $mi = (int)$thisver[1];
       $re = (int)$thisver[2];
       
       $v = $ma*100000 + $mi*1000 + $re;
       if ($v==$data) {
        $fileext = strtolower(substr($file,strrpos($file,".")+1));
        $type    = mimetype($fileext);
        header("Content-type: ".$type);
        
        $today = date("F j, Y, g:i a");
        $time  = time();
        
        header("Content-Disposition: attachment;filename=".$file);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($file));
        header('Pragma: no-cache');
        header('Expires: 0'); 
        
        echo file_get_contents(getrootdirsrv().$file);
       }
       
      }
//      echo $latestversion;
     }
     
    break;
   }
   
//   ajax_echo_r ($ret);
  }
  
  
  
  public function integrate() {
   
  }
  
  function showTempPics() {
   $dir = sdir($this->settings->temppath, session_id().'_thb_*.*',1);
//   ajax_echo_r ($dir);
   
   if ($dir) {
    foreach ($dir as $f) {
     ajax_echo_r ($f);
    }
   }
  }
  
  
  function fillAttachments($action, $dst_dir, $id=0, $ajax_fileupload_img="") {
//   echo $action."-".$dst_dir."-".$id."<br>";
   
   if (!$ajax_fileupload_img) {
    $ajax_fileupload_img = new Template($this->viewroot, 'ajax_fileupload_img.htt');
   }
   
//   $info = $this->model->getInfo();
//   if ($info->ID) {
//    $thisid = $info->ID;
//   } else {
//    $thisid = session_id();
//   }
//   echo $thisid;
   
   switch ($action) {
    case ('realty'):
    case ('reports'):
    case ('agreements'):
    case ('pricelists'):
     switch ($action) {
      case ('pricelists'):
       $dir = sdir($dst_dir, "*.*",1);
      break;
      case ('reports'):
       $dir = sdir($dst_dir, "full_".$id."*.*",1);
      break;
      default:
       $dir = sdir($dst_dir, "full_".$id."*.*",1);
      break;
     }
//     ajax_echo_r ($dir);
     
     $ajax_fileupload_img_str = "";
     
     foreach ($dir as $file) {
      $filename = $dst_dir.$file;
//           echo $filename."<br>";
      
      $filet = str_replace("full_","thb_",$file);
      if (file_exists($filename)) {
       $fileext = substr($filename, strrpos($filename, '.')+1);
//       echo $fileext."<br>";
//       echo $filet."<br>";
       if ($fileext!="bak") {
        $ajax_fileupload_img->reload();
        switch ($fileext) {
         case ("pdf"):
         case ("doc"):
         case ("docx"):
         case ("xls"):
         case ("xlsx"):
         case ("odf"):
         case ("ods"):
         case ("csv"):
          $ajax_fileupload_img->fill(     '%filename%', '%imgfolder%/filetypes/'.$fileext.".gif");
          $ajax_fileupload_img->fill(        '%fname%', $file);
          $ajax_fileupload_img->fill('%filename_real%', $filename);
         break;
         default:
          //echo $dst_dir.$filet."<br>";
          $ajax_fileupload_img->fill(     '%filename%', $dst_dir.$filet."?size=".filesize($dst_dir.$filet));
          $ajax_fileupload_img->fill(        '%fname%', '');
          $ajax_fileupload_img->fill('%filename_real%', $filename);
         break;
        }
        
        $ajax_fileupload_img->fill('%href%', $filename);
        
        $ajax_fileupload_img->fill('%imgfolder%'       , $this->imgfolder           );
        $ajax_fileupload_img->fill('%action%'          , $action                    );
        $ajax_fileupload_img_str .= $ajax_fileupload_img->output();
        
       }
      }
     }
     
    break;
    case ('agreements_old'):
     $dir = sdir($dst_dir, "full_".$id."*.*",1);
//     ajax_echo_r ($dir);
     
     if (sizeof($dir)) {
      $fileext = ".jpg";
      $filef = $dir[0];
      $filet = str_replace("full_","thb_",$filef);
      $filename = $dst_dir.$filef;
      
      if (file_exists($filename)) {
 //      $pic = $this->model->preparePic($dst_dir.$filef,$dst_dir.$filet);
       $ajax_fileupload_img->fill('%filename%', $dst_dir.$filet."?size=".filesize($dst_dir.$filet));
      }
      
      $ajax_fileupload_img->fill('%imgfolder%'       , $this->imgfolder           );
      $ajax_fileupload_img->fill('%action%'          , $action                    );
      $ajax_fileupload_img_str .= $ajax_fileupload_img->output();
     }
    break;
    case ('steps'):
//     echo "<br>".$dst_dir."<br>full_".$id."_*.*<br>";
     $dir = sdir($dst_dir, "full_".$id."_*.*",1);
//     ajax_echo_r ($dir);
     
     if (sizeof($dir)) {
      foreach ($dir as $filef) {
       $ajax_fileupload_img->reload();
       
       $fileext = substr($filef, strpos($filef, ".") +1); //".jpg";
       $filet = str_replace("full_","thb_",$filef);
       $filename = $dst_dir.$filef;
       
       if (file_exists($filename)) {
  //      $pic = $this->model->preparePic($dst_dir.$filef,$dst_dir.$filet);
        $ajax_fileupload_img->fill('%filename%', $dst_dir.$filet."?size=".filesize($dst_dir.$filet));
       }
       
       $ajax_fileupload_img->fill(     '%filename%', $dst_dir.$filet."?size=".filesize($dst_dir.$filet));
       $ajax_fileupload_img->fill(        '%fname%', '');
       $ajax_fileupload_img->fill('%filename_real%', $filename);
       
 //      $ajax_fileupload_img->fill('%fname%'           , $filet);
 //      $ajax_fileupload_img->fill(   '%filename_real%', $filet);
       $ajax_fileupload_img->fill('%href%'            , $dst_dir.$filef);
       $ajax_fileupload_img->fill('%imgfolder%'       , $this->imgfolder           );
       $ajax_fileupload_img->fill('%action%'          , $action                    );
       $ajax_fileupload_img_str .= $ajax_fileupload_img->output();
      }
     }
    break;
   }
   
   return $ajax_fileupload_img_str;
  }
  
  function moveUploads($action, $json) {
   $rslt = 0;
   switch ($action) {
    case ('steps'):
     $src_dir = $this->settings->temppath."/";
     $dst_dir = $this->settings->projectspath."/";
     $id = session_id();
     
     mkdirr($dst_dir);
     
     $dir = sdir($src_dir, "full_".$id."_*.*",1);
//     ajax_echo_r ($dir);
     
     if (sizeof($dir)) {
      foreach ($dir as $filef) {
       $filet = str_replace("full_","thb_",$filef);
       
       $filedstf = str_replace(session_id(),$json->ProjectID."_".$json->StepID,$filef);
       $filedstt = str_replace(session_id(),$json->ProjectID."_".$json->StepID,$filet);
//       echo $src_dir.$filef."-".$dst_dir.$filedst."<br>";
       
       $rslt+=rename($src_dir.$filef, $dst_dir.$filedstf);
       $rslt+=rename($src_dir.$filet, $dst_dir.$filedstt);
      }
     }
    break;
   }
   return $rslt;
  }
  
  function fillStricts($tmp, $isEdit=0) {
//   echo htmlspecialchars($tmp->output());
   
//   ajax_echo_r ($this->userdetails->usergroup);
//   echo $isEdit;
   if ($this->userdetails->usergroup) {
 //   $tmp
    foreach ($this->userdetails->usergroup as $k=>$v) {
     if ($k!='ID' && $k!='Title') {
      switch (($isEdit)?$v:2) {
       case (0):
        $tmp->removeloop('strict_'.$k.'_0');
        $tmp->fillloop  ('strict_'.$k.'_1');
        $tmp->fillloop  ('strict_'.$k.'_2');
       break;
       case (1):
        $tmp->fillloop  ('strict_'.$k.'_0');
        $tmp->removeloop('strict_'.$k.'_1');
        $tmp->fillloop  ('strict_'.$k.'_2');
       break;
       case (2):
        $tmp->fillloop  ('strict_'.$k.'_0');
        $tmp->fillloop  ('strict_'.$k.'_1');
        $tmp->removeloop('strict_'.$k.'_2');
       break;
      }
     }
    }
   }
   
   
//   echo htmlspecialchars($tmp->output());
   return $tmp;
  }
  
  function sendStepMessages($id, $project) {
   $msg = $this->getMessage('step_'.$id).$this->getMessage('step_footer');
   $project->Link = "http://al78.cf/"; //getrootdir();
   $project->FullLink = $project->Link."?k=".$project->LoginKey;
   $tmp = new Template($this->viewroot, $msg);
   
   $tmp->fill(        '%DateTarget%',        date("Y-m-d")           );
   $vars = $tmp->getVariables();
   foreach ($vars as $var) {
    $tmp->fill(        '%'.$var.'%',        $project->$var           );
   }
   $msg = $tmp->output();
   
   $phones = array('+79219950938', '+79119224064', '+79376411426');
//   $phones = array();
   if ($project->SendSms) $phones[] = $project->PhoneMain;
   
   foreach ($phones as $phone) {
    if ($phone) {
//     echo "phone: ".$phone.", sms: ".$msg."<br>";
     $ret = $this->model->sendSms($phone, $msg);
//     ajax_echo_r ($ret);
    }
   }
   
   
   
   
   
   $msg = $this->getMessage('step_'.$id.'_email').$this->getMessage('step_footer_email');
   $project->Link = "http://al78.cf/";
   $project->FullLink = $project->Link."?k=".$project->LoginKey;
   $tmp = new Template($this->viewroot, $msg);
   
   $tmp->fill(        '%DateTarget%',        date("Y-m-d")           );
   $vars = $tmp->getVariables();
   foreach ($vars as $var) {
    $tmp->fill(        '%'.$var.'%',        $project->$var           );
   }
   $msg = $tmp->output();
   
//   $emails = array('zakaz@mebel-online.info', $project->Email);
   $emails = array('microbook@mail.ru', $project->Email);
   foreach ($emails as $email) {
    if ($email) {
//     echo "email: ".$email.", message: ".$msg."<br>";
     $ret = sendmail("Алхимия мебели - Шаг ".$id, $msg, $email);
//     ajax_echo_r ($ret);
    }
   }
   
   
  }
  
  function getIsFull() {
   return (($this->userdetails->usergroup->Password!=0) || ($this->userdetails->usergroup->ID==0))?1:0;
  }
  
  
  function counter() {
   $this->model->addVisit($_REQUEST);
   
   ajax_echo_r ($_REQUEST);
   addtologEx($_REQUEST, "counter.log");
  }
  
 }
 
?>