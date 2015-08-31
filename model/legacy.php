<?
 // some legacy but very useful helper functions. thanks to my dad for this code. =)
 include "cache.1.3.php";
 include_once("localization.1.0.php");     // add some more legacy code
 
 function filestr($fname) {           // replacement for file_get_contents
  $ret="";
  
  if (file_exists($fname)) {
   if (is_readable($fname)) {
    $fh = fopen($fname, "r") or die("Can't open file!");   // ������� ���� � ���������� ��������� ������� ������� � ����� �����
    while (! feof($fh)) {
     $ret.= fgets($fh, 4096);
     $n++;
    }
    fclose($fh);
   } else {
    $ret.="$fname is not readable!";
   }
  } else {
   $ret.="File ".$fname." not found!" ;
  }
  
  return ($ret);
  
 }
 
 function ajax_echo($txt) {            // used to solve some ajax output problems on Unix systems if any (usually if the UTF-8 and Windows-1251 meets together)
  echo localize(cfstrconv($txt));
 }
 function ajax_return($txt) {            // used to solve some ajax output problems on Unix systems if any (usually if the UTF-8 and Windows-1251 meets together)
  return localize(cfstrconv($txt));
 }
 
 function cfstrconv($str) {            // used to friendly convert the text from one codepage to another
  if (getsecurevariable('settings')->enableajaxrecode) {
   return iconv("windows-1251","UTF-8",$str);
  } else {
   return $str;
  }
  //return iconv("UTF-8","windows-1251",$str);
  //return ($str);
 }
 
 function cfstrinvconv($str) {         // used to friendly convert the text back. seems to be obsolete.
  if (getsecurevariable('settings')->enableajaxrecode) {
   return iconv("UTF-8","windows-1251",$str);
  } else {
   return $str;
  }
  //return iconv("UTF-8","windows-1251",$str);
//  return ($str);
 }
 
 function cf_strtolower($s) {          // used to convert the uppercase characters to lowercase ones
  return (strtr( $s, 'ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮЁ', 'йцукенгшщзхъфывапролджэячсмитьбюё' ));
 }
 
 function mimetype($fileext) {         // used to get a nice mime type from file extension. used in file downloader.
  $known_mime_types=array(
   "css"  => "text/css",
   "pdf"  => "application/pdf",
   "txt"  => "text/plain",
   "js"   => "text/javascript",
   "html" => "text/html",
   "htm"  => "text/html",
   "exe"  => "application/octet-stream",
   "zip"  => "application/zip",
   "doc"  => "application/msword",
   "xls"  => "application/vnd.ms-excel",
   "ppt"  => "application/vnd.ms-powerpoint",
   "gif"  => "image/gif",
   "png"  => "image/png",
   "jpeg" => "image/jpg",
   "jpg"  => "image/jpg",
   "php"  => "text/plain",
   "mp3"  => "audio/mpeg",
   "mp4"  => "audio/mpeg",
   "ogg"  => "audio/vorbis",
   "rar"  => "application/octet-stream"  // "application/x-rar-compressed"
  );
  return($known_mime_types[$fileext]);
 }
 
 function filter($t) {                                       // used to protect us from various hacks
  return preg_replace ("/^[^a-zA-ZА-Яа-я0-9\s]*$/","",$t);
 }
 
 function replace_unicode_escape_sequence($match) {
//  $match = "\u00ed";
//  $match = str_replace("%u","U+",$match);
//  return mb_convert_encoding($match, 'UTF-8', 'HTML-ENTITIES');
//  return ($match);;
//  return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
 }
// $str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
 
 function strprepare($s) {
  return iconv("windows-1251","UTF-8",$s);
 }
 function strrevprepare($s) {
  return iconv("UTF-8","windows-1251",$s);
 }
 
 function safetext ($s) {                                    // used to protect us from god-level hacks and special characters
//  return strprepare(filter(rawurldecode(base64_decode($s))));
  return (filter($s));
 }
 
 function softtrunc($str, $len) {
  if (strlen($str)>=$len) {
   $pos  = strpos($str, " ", $len);
   return substr($str, 0, $pos)."...";
  } else {
   return $str;
  }
 }
 
 function echo_r($txt, $return=false) {
  if ($return) {
   return (nl2br(str_replace(" ","&nbsp",(print_r($txt,true)))))."<br>";
  } else {
   echo (nl2br(str_replace(" ","&nbsp",(print_r($txt,true)))))."<br>";
  }
 }
 function ajax_echo_r($txt) {
  ajax_echo (nl2br(str_replace(" ","&nbsp",(print_r($txt,true)))))."<br>";
 }
 function ajax_return_r($txt) {
  return ajax_return(nl2br(str_replace(" ","&nbsp",(print_r($txt,true)))))."<br>";
 }
 
 function getvariable($vn) {
  // Short introduction to variable variables ($$) 
  // $v = 'hue';
  // $hue = 'test';
  // $$v now represents the value of $hue which is 'test'
  if(!isset($_SESSION)) session_start();
  
  if (isset($_GET[$vn])) {
   $$vn  = $_GET[$vn];                        // get hue from the request
   $_SESSION[$vn]=$$vn;
  } else if (isset($_POST[$vn])) {
   $$vn = $_POST[$vn];                        // if we used POST
   $_SESSION[$vn]=$$vn;
  } else if (isset($_SESSION[$vn])) {
   $$vn=$_SESSION[$vn];
  } else {
   $$vn  = -1;
  }
  return $$vn;
  
 }
 
 function getsecurevariable($vn) {
  // Short introduction to variable variables ($$) 
  // $v = 'hue';
  // $hue = 'test';
  // $$v now represents the value of $hue which is 'test'
  if(!isset($_SESSION)) session_start();
  
  if (isset($_SESSION[$vn])) {
   $$vn=$_SESSION[$vn];
  } else {
   $$vn  = -1;
  }
  return $$vn;
 }
 
 function setsecurevariable($vn,$vv) {
  if(!isset($_SESSION)) session_start();
  $_SESSION[$vn]=$vv;
  return $vv;
 }
 
 function getvariablereq($vn) {
  $$vn    = $_GET[$vn];                  // get action from the request
  if (!$$vn) $$vn = $_POST[$vn];         // oops... may be here?
  return $$vn;
 }
 
 function sendmail($subject, $mail_template, $email = "main@cfteam.ru", $from_name = "Alchemy") {
  $from_mail = "main@cfteam.ru"; //"main@cfteam.ru";
  
  $charset = 'UTF-8';
  $headers = "From: " .$from_name." <".$from_mail.">\n"."Content-Type: text/html; charset=$charset; format=flowed\n"."MIME-Version: 1.0\n"."Content-Transfer-Encoding: 16bit\n"."X-Mailer: PHP/".phpversion()."\n";
  
  $ret = extmail2($subject, $mail_template, $email, $from_name, $from_mail);
  
  $ret    = 1; // emulate successful mail send
  
  return $ret;
 }
 
 function extmail2($subject, $mail_template, $email = "main@cfteam.ru", $from_name = "cfteam", $from_mail = "main@cfteam.ru") {
//  $base = "http://www.cfteam.ru/standalone/extmail2/index.php";
//  $base = "http://localhost/extmail2/";
  $base = "http://fromair.ru/parser/extmail2/";
  
//  echo "<br>from_name: ".$from_name."<br>";
//  echo "<br>from_mail: ".$from_mail."<br>";
  
  $postfields = "subject=".urlencode($subject)."&mail_template=".urlencode($mail_template)."&email=".urlencode($email)."&from_name=".urlencode($from_name)."&from_mail=".urlencode($from_mail);
//  echo $postfields;
  
  $headers  = array(
   "Accept: */*",
   "Content-Length: ".strlen($postfields),
   "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
  );
  
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL,        $base);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,  true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,      $headers);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  
  // receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  $server_output = curl_exec ($ch);
  
  curl_close ($ch);  
  
//  echo $server_output;
  return $server_output;
 }
 
 
 function mkdirr($path) {
  $PHP_EOL = "\r\n";
  $dirs = explode("/",$path);
  $prevdirs = "";
  foreach ($dirs as $dir) {
   $prevdirs.=$dir."/";
   @mkdir($prevdirs);
//   echo $prevdirs.$PHP_EOL;
  }
//  print_r ($dirs);
 }
 
 function getrootdir() {
  return "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/'))."/";
 }
 function getrootdirsrv() {
  $s = $_SERVER['SCRIPT_FILENAME'];
  $ret = (substr($s, 0, strrpos($s, "/")))."/";
  return $ret;
 }
 
 function sort_by_order($f1,$f2) {
//  $_f1 = substr($f1, 0, strrpos($f1, '.')).'-';
//  $_f2 = substr($f2, 0, strrpos($f2, '.')).'-';
//  echo $f1."  ".$_f1." ".$_f2."<br>";
  if     ($f1->Order < $f2->Order)  return -1;
  elseif ($f1->Order > $f2->Order)  return  1;
  else                              return  0;
 }
 
 function addtolog($txt, $fname='events.txt') {
  @mkdirr('data/logs');
  file_put_contents('data/logs/'.$fname, (date("Y-m-d H:i:s"))."\t".print_r($txt,1)."\n", FILE_APPEND);  // write to log in case of errors
//  echo $txt."<br>";
 }
 
 function addtologEx($txt, $fname = 'events.txt') {
  @mkdirr('data/logs');
  file_put_contents('data/logs/'.$fname, (date("Y-m-d H:i:s"))."\t".print_r($txt, true)."\n", FILE_APPEND);  // write to log in case of errors
//  ajax_echo_r ($txt);
 }
 
 function sup($buf) {
//  $ret=str_replace(strprepare("�2"),strprepare("�<sup>2</sup>"),$buf);
  $ret=str_replace(("�2"),("�<sup>2</sup>"),$buf);
  return $ret;
 }
 
// function format($v, $format) {
//  return sprintf("%010s",   $v);
// }
 
 function format($v, $f="#.#") {
  if (strpos($v,".")) {
   $c = strpos($f,".");
   $t = strlen($f);
   
   $v = (string)$v;
   $v = substr($v, 0 , strpos($v,".")+$t-$c);
  }
  return $v;
//  return sprintf("%0".$c."s",   $v);
 }
 
 function fucase ($string) {
  $string = mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, mb_strlen($string), 'UTF-8');
  return $string;
 }
 
 function gettemplatespath() {
  return getsecurevariable("viewroot","view/")."templates/";
 }
 
 function getColNames() {
  $colnames = array(
       'N'=>'Номер',
   'ArtID'=>'Артикул',
   'Name0'=>'Группа',
   'Name1'=>'Подгруппа',
   'Name2'=>'Наименование',
   'Name3'=>'Модификация',
     'Qty'=>'Кол-во',
   'Cost0'=>'Цена поставщика',
   'Cost1'=>'Цена покупки',
   'Cost2'=>'Цена продажи',
   
   'DateAdded'=>'Дата добавления',
   'DateTarget'=>'Дата обращения',
   'RoomsTotal'=>'Всего комнат',
   'RoomsIsolated'=>'Изол. комнат',
   'SpaceTotal'=>'Площадь общая',
   'SpaceKitchenText'=>'Площадь кухни (текст.)',
   'SpaceLivingText'=>'Площадь жилая (текст.)',
   'SpaceTotalText'=>'Площадь общая (текст.)',
   'SpaceLiving'=>'Площадь жилая',
   'SpaceKitchen'=>'Площадь кухни',
   'Cost'=>'Цена',
   'Address'=>'Адрес',
   'DistrictID'=>'Район',
   'Phone'=>'Тел.',
   'ContactName'=>'Имя контакта',
   'ContactEmail'=>'Email контакта',
   'Duration'=>'Продолжительность звонка',
   'CustomerSubtypeID'=>'Подтип покупателя',
   'MethodOfPaymentID'=>'Способ оплаты',
   'SourceID'=>'Источник',
   'UserID'=>'Пользователь',
   'Status'=>'Статус',
   'DirectionID'=>'Направление',
   'TargetContact'=>'Контакт с целью',
   'TargetMeeting'=>'Встреча с целью',
   'OfficeVisit'=>'Посещение офиса',
   'ObjectShow'=>'Показ объекта',
   'TargetAgreed'=>'Собственник согласен работать',
   'HasAgreement'=>'Договор подписан',
   'DepositReceived'=>'Задаток получен',
   'Handshake'=>'Сделка совершена',
   'GiftsGiven'=>'Подарки вручены',
   'HasCertificate'=>'Есть свидетельство',
   'Floor'=>'Этаж',
   'Floors'=>'Этажей',
   'TechnicalFloor'=>'Тех. этаж',
   'Layers'=>'Уровней',
   'Toilets'=>'Санузлов',
   'Balconies'=>'Балконов',
   'Loggias'=>'Лоджий',
   'CeilingHeight'=>'Высота потолка',
   'CostPerMeter'=>'Цена за кв. метр',
   'Entrances'=>'Подъездов',
   'Elevators'=>'Лифтов',
   'HouseTypeID'=>'Тип планировки',
   'MarketID'=>'Рынок',
   'AgentFee'=>'Комиссия агента',
   'MediatorID'=>'Посредник',
   'ExchangeOption'=>'Обменный вариант',
   'TargetAudience'=>'Целевая аудитория',
   'Problem'=>'Проблема с объектом',
   'OverlappingTypeID'=>'Тип перекрытий',
   'Security'=>'Охрана',
   'Concierge'=>'Консъерж',
   'OperationService'=>'Эксплуатационная служба',
   'Chute'=>'Мусоропровод',
   'Gas'=>'Газ',
   'Parking'=>'Парковка',
   'CompletionDateQuarter'=>'Срок сдачи - квартал',
   'CompletionDateYear'=>'Срок сдачи - год',
   'CompletionDateComment'=>'Срок сдачи - комментарий',
   'LayoutTypeID'=>'Вид планировки',
   'ToiletTypeID'=>'Тип санузла',
   'ConditionID'=>'Состояние',
   'FinishingID'=>'Отделка',
   'FloorSurfaceID'=>'Материал пола',
   'StoveTypeID'=>'Плита',
   'DoorsTypeID'=>'Двери',
   'WallsSurfaceID'=>'Отделка стен',
   'WallsMaterialID'=>'Материал стен',
   'BathroomEquipmentID'=>'Сантехника',
   'WindowsTypeID'=>'Окна',
   'RightsSourceID'=>'Источник права',
   'RightsTransmissionID'=>'Форма перехода права',
   'ResidentialComplexName'=>'Название ЖК',
   'Developer'=>'ЗАстройщик',
   'PhoneDenied'=>'Отказ от разговора по телефону',
   'MeetingDenied'=>'Отказ от встречи',
   'ServiceDenied'=>'Отказ от услуги',
   'NoAgreement'=>'Нет договора',
   'SaleCanceled'=>'Отказ от продажи',
   'SoldSelf'=>'Продал сам или другие риелторы',
   'CostTooHigh'=>'Цена завышена',
   'SalePaused'=>'Продажа на паузе',
   'AnotherProblem'=>'Другая проблема',
   'FinishingText'=>'Отделка (текст.)',
   'RightsTransmissionText'=>'Форма перехода права (текст.)',
   'MortgageText'=>'Ипотека (текст.)',
   'AgreementNumber'=>'Договор (текст.)',
   'KadNumber'=>'Кадастровый номер',
   
   'FloorID'=>'Этажи',
   'HouseTypeIDs'=>'Типы планировок',
   'DesiredRoomsIDs'=>'Комнаты',
   'MaxCost'=>'Макс. цена',
   'DistrictIDs'=>'Районы',
   'ObjectID'=>'ID объекта',
   'Username'=>'Логин',
   'Password'=>'Пароль',
   'Banned'=>'Забанен',
   'Avatar'=>'Аватар',
   'Visible'=>'Видимый',
   'DateBirth'=>'Дата рождения',
   'GroupID'=>'ID группы',
   'Email'=>'Email',
   'SessionID'=>'ID сессии',
   'LastAccess'=>'Последний доступ',
   'SmoothAnimation'=>'Плавная анимация',
   'LastObjectType'=>'Последний тип объекта',
   'CompanyID'=>'ID компании',
   'IsManager'=>'Является менеджером',
   'Skype'=>'Скайп',
   'VK'=>'ВКонтакте',
   'Firstname'=>'Имя',
   'Surname'=>'Фамилия',
   'Email2'=>'Email 2',
   'Location'=>'Местоположение',
   'About'=>'О себе',
   'LoginKey'=>'Ключ для взода',
   'FirstSms'=>'Первое смс',
   'SendSms'=>'Отправлять смс',
   'DateRemoved'=>'Дата увольнения'
  );
  
  
  
  
  return $colnames;
 }
 
 function conv($s) {
  return iconv("windows-1251", "UTF-8", $s);
  //return (trim($s));
 }
 
 function _odbc_result($r, $id) {
  global $fieldid;
  
  $fid = $id;
  
  if (!$r[$fid]) {
   $fid = $fieldid[$id];
  }
  
  if ($id=="ZAGENTID") {
   $r[$fid]=1;
  }
  
  if (
   ($id=="DateTime") ||
   ($id=="DateStarted") ||
   ($id=="DateEdited") ||
   ($id=="DateFinished")
  ) {
   $buf = str_replace(" ",".",str_replace(":",".",$r[$fid]));
   $e = explode(".",$buf);
   
   $ret = $e[2]."-".$e[1]."-".$e[0]." ".$e[3]."-".$e[4]."-".$e[5];
  } else {
   $ret = $r[$fid];
  }
  
//  return iconv("UTF-8", "windows-1251", $r[$fid]);
  return $ret;
 }
 
 function EventNameToString($name) {
  switch ($name) {
   case ('TaskListIDChanged'):
    $ret = "Task status changed";
   break;
   case ('CommentDeleted'):
    $ret = "Comment deleted";
   break;
   case ('TaskAdded'):
    $ret = "Task added";
   break;
   case ('CommentAdded'):
    $ret = "Comment added";
   break;
   case ('TaskDeleted'):
    $ret = "Task deleted";
   break;
   case ('ProjectAdded'):
    $ret = "Project added";
   break;
   default:
    $ret = $name;
   break;
  }
  return $ret;
 }
 
 function TaskListNameFromTaskListID($ListID) {
  switch ($ListID) {
   case (0):
    $ret = "Added";
   break;
   case (1):
    $ret = "In Progress";
   break;
   case (2):
    $ret = "Completed";
   break;
   case (3):
    $ret = "Approved";
   break;
   case (4):
    $ret = "Deleted tasks";
   break;
  }
  return $ret;
 }
 
 function brtonl($str) {
  $str = str_replace("<br>","\n",$str);
  return $str;
 }
 
 function cmp_sbn($a, $b) {
//  return strcmp($a->name, $b->name);
//  echo $a->id."-".$b->id."-".strcmp($a->id, $b->id)."<br>";
  return strcmp($a->name, $b->name);
 }
 
 function sortbyname(&$your_data) {
  usort($your_data, "cmp_sbn");
//  ajax_echo_r ($your_data);
 }
 
 function cmp_sbn_dt($a, $b) {
  return strcmp($b->DateTarget, $a->DateTarget);
 }
 
 function sortbyDateTarget(&$your_data) {
  usort($your_data, "cmp_sbn_dt");
 }
 
 
 
 function cmp($a, $b) {
  if ($a->ID) {
   global $fieldname;
   return ($b->$fieldname > $a->$fieldname);
  } else {
   return 1;
  }
 }
 
 function sortby(&$your_data, $fieldname_new) {
  global $fieldname;
  $fieldname = $fieldname_new;
  uasort($your_data, "cmp");
 }
 
 
 
 function cmpstr($a, $b) {
  if ($a->ID) {
   global $fieldname;
   return strcmp($a->$fieldname, $b->$fieldname);
  } else {
   return 1;
  }
 }
 
 function sortbystr(&$your_data, $fieldname_new) {
  global $fieldname;
  $fieldname = $fieldname_new;
  uasort($your_data, "cmpstr");
 }
 
 
 
 function backtrace() {
  $bt = debug_backtrace();
  $ret = "";
  for ($i=1; $i<sizeof($bt); $i++) {
   $bti = $bt[$i];
   $args = "";
   foreach ($bti['args'] as $arg) {
    if ($args) $args.=", ";
    $args.=$arg;
   }
   
   $ret.=$bti['file'].":".$bti['line']." ".$bti['class']."->".$bti['function']."(".$args.")<br>";
   
  }
  return $ret;
 }
 
 function escape($txt) {
  $txt = str_replace(';','',$txt);
  $txt = str_replace('"','',$txt);
  $txt = str_replace("'",'',$txt);
  return $txt;
 }
 
 function formatCost($v) {
  return  number_format((double)$v, 0, ',', '&nbsp;');
//  return $v;
 }
 
 
 function HSVtoRGB($H,$S,$V) {
  //1
  $H *= 6;
  //2
  $I = floor($H);
  $F = $H - $I;
  //3
  $M = $V * (1 - $S);
  $N = $V * (1 - $S * $F);
  $K = $V * (1 - $S * (1 - $F));
  //4
  
  switch ($I) {
   case 0:
    list($R,$G,$B) = array($V,$K,$M);
   break;
   case 1:
    list($R,$G,$B) = array($N,$V,$M);
   break;
   case 2:
    list($R,$G,$B) = array($M,$V,$K);
   break;
   case 3:
    list($R,$G,$B) = array($M,$N,$V);
   break;
   case 4:
    list($R,$G,$B) = array($K,$M,$V);
   break;
   case 5:
   case 6: //for when $H=1 is given
    list($R,$G,$B) = array($V,$M,$N);
   break;
  }
  
  return (dechex($R*15).dechex($G*15).dechex($B*15));
 }
 
 function CSVRead($fname) {
  if (file_exists($fname)) {
   $row = 1;
   if (($handle = fopen($fname, "r")) !== FALSE) {
    $ret = array();
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
     $num = count($data);
//     echo "<p> $num полей в строке $row: <br /></p>\n";
     $row++;
//     for ($c=0; $c < $num; $c++) {
//      echo $data[$c] . "<br />\n";
//     }
     $ret[] = $data;
    }
    fclose($handle);
    return ($ret);
   }
  }
 }
 
	function dateAdaptWrite ($info) {
		if ($info) $info = date('Y-m-d', strtotime($info));
		return $info;
	}
	function dateTimeAdaptWrite ($info) {
		if ($info) $info = date('Y-m-d H:i:s', strtotime($info));
		return $info;
	}
 
	function dateTimeAdaptRead ($info) {
		if (strtotime($info)>0) {$info = date('d.m.Y H:i', strtotime($info)); } else {$info = "";};
		return $info;
	}
 
	function dateAdaptRead ($info) {
		if (strtotime($info)>0) {$info = date('d.m.Y', strtotime($info)); } else {$info = "";};
		return $info;
	}
	function timeAdaptRead ($info) {
		if (strtotime($info)>0) {$info = date('H:i', strtotime($info)); } else {$info = "";};
		return $info;
	}
	function dayWeekAdaptRead ($info) {
		if (strtotime($info)>0) {$info = (date('w', strtotime($info))+6) % 7; } else {$info = "";};
		return $info;
	}
	
	function monthAdaptRead ($info) {
		if (strtotime($info)>0) {
   $months = getMonthNames();
   
   $buf = strtotime($info);
   $info = $months[(int)date('m', $buf)]." ".date('Y', $buf); } else {$info = "";
  };
		return $info;
	}
	
 
 function getMonthNames($short = 0) {
  if ($short) {
   $ret = array (
    1 =>"<ru>Янв</ru>",
    2 =>"<ru>Фев</ru>",
    3 =>"<ru>Мар</ru>",
    4 =>"<ru>Апр</ru>",
    5 =>"<ru>Май</ru>",
    6 =>"<ru>Июн</ru>",
    7 =>"<ru>Июл</ru>",
    8 =>"<ru>Авг</ru>",
    9 =>"<ru>Сен</ru>",
    10=>"<ru>Окт</ru>",
    11=>"<ru>Ноя</ru>",
    12=>"<ru>Дек</ru>"
   );
  } else {
   $ret = array (
    1 =>"<ru>Январь</ru>",
    2 =>"<ru>Февраль</ru>",
    3 =>"<ru>Март</ru>",
    4 =>"<ru>Апрель</ru>",
    5 =>"<ru>Май</ru>",
    6 =>"<ru>Июнь</ru>",
    7 =>"<ru>Июль</ru>",
    8 =>"<ru>Август</ru>",
    9 =>"<ru>Сентябрь</ru>",
    10=>"<ru>Октябрь</ru>",
    11=>"<ru>Ноябрь</ru>",
    12=>"<ru>Декабрь</ru>"
   );
  }
  return $ret;
 }
 
 
 function getDayNames($short = 0) {
  if ($short) {
   $ret = array (
    0 =>"<ru>Пн</ru>",
    1 =>"<ru>Вт</ru>",
    2 =>"<ru>Ср</ru>",
    3 =>"<ru>Чт</ru>",
    4 =>"<ru>Пт</ru>",
    5 =>"<ru>Сб</ru>",
    6 =>"<ru>Вс</ru>"
   );
  } else {
   $ret = array (
    0 =>"<ru>Понедельник</ru>",
    1 =>"<ru>Вторник</ru>",
    2 =>"<ru>Среда</ru>",
    3 =>"<ru>Четверг</ru>",
    4 =>"<ru>Пятница</ru>",
    5 =>"<ru>Суббота</ru>",
    6 =>"<ru>Воскресенье</ru>"
   );
  }
  return $ret;
 }
 
 
 
 
 
 
 
 
 
 function sdir($path='.', $mask='*', $nocache=0) {
  if (is_dir($path)) {
   static $dir = array();                                            // cache result in memory
   if ( !isset($dir[$path]) || $nocache) {
    $dir[$path] = scandir($path);
   }
   foreach ($dir[$path] as $i=>$entry) {
    if ($entry!='.' && $entry!='..' && fnmatch($mask, $entry) ) {
     $sdir[] = $entry;
    }
   }
  }
  return ($sdir); 
 }
 
 function ajaxdecode($data) {
  $data = str_replace(    '\"',  '"', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&amp;',  '&', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#96;',  '`', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#39;',  "'", $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#34;',  '"', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#92;', '\\', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#58;',  ':', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#60;',  '<', $data);            // fix some escaped paths (if any)
  $data = str_replace( '&#62;',  '>', $data);            // fix some escaped paths (if any)
  $data = str_replace('&#123;',  '{', $data);            // fix some escaped paths (if any)
  $data = str_replace('&#125;',  '}', $data);            // fix some escaped paths (if any)
  
  return $data;
 }
 
 function getKey($id, $v) {
  return $id.substr(md5($v), 0, 4);
 }
 
?>