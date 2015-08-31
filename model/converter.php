<?
 function convertfield ($result, $ISCcolumnname, $reserved) {
  switch ($ISCcolumnname) {
   case "ZVTH":
    switch (conv(_odbc_result($result, 'ZVTH'))) {
     case "-"   : $housetype= 1;      break;
     case "Хру" : $housetype= 2;      break;
     case "МС"  : $housetype= 3;      break;
     case "Улу" : $housetype= 4;      break;
     case "Ста" : $housetype= 5;      break;
     case "Эли" : $housetype= 6;      break;
     case "Гос" : $housetype= 7;      break;
     case "Мал" : $housetype= 8;      break;
     case "Лен" : $housetype= 9;      break;
     case "Ком" : $housetype=10;      break;
     case "Стф" : $housetype=11;      break;
     case "НС"  : $housetype=12;      break;
     case "90"  : $housetype=13;      break;
     default    : $housetype= 1;      break;
    }
    return $housetype;
   break;
   
   
   
   
   
   
   case "ZVMATER":
   case "ZVMATERWALL":
    switch (conv(_odbc_result($result, 'ZVMATER'))) {
     case "-"   : $wallsmaterial= 1;      break;
     case "Кир" : $wallsmaterial= 2;      break;
     case "Пан" : $wallsmaterial= 3;      break;
     case "Дер" : $wallsmaterial= 4;      break;
     case "Ком" : $wallsmaterial= 5;      break;
     case "Шла" : $wallsmaterial= 6;      break;
     case "Мон" : $wallsmaterial= 7;      break;
     case "Бре" : $wallsmaterial= 8;      break;
     case "Кар" : $wallsmaterial= 9;      break;
     case "ПБ"  : $wallsmaterial=10;      break;
     default    : $wallsmaterial= 1;      break;
    }
    return $wallsmaterial;
   break;
  
   case "ZCONDITION":
   case "ZCONDITIONID":
    if ($t=="RoomsSell") {
     $condition=1;
    } else {
     switch (conv(_odbc_result($result, 'ZCONDITION'))) {
      case ('1303'): $condition=2;       break;
      case ('1304'): $condition=3;       break;
      case ('1305'): $condition=4;       break;
      case ('1306'): $condition=5;       break;
      case ('1307'): $condition=6;       break;
      
      case ('812' ): $condition=1;       break;
      case ('823' ): $condition=2;       break;
      case ('822' ): $condition=3;       break;
      case ('813' ): $condition=4;       break;
      case ('824' ): $condition=5;       break;
      case ('928' ): $condition=6;       break;
      case ('929' ): $condition=7;       break;
      
      case ('-1'  ): $condition=1;       break;
      default      : $condition=1;       break;
     }
    }
    return $condition;
   break;
   
   
   
   case "ZVPHONE":
    switch (conv(_odbc_result($result, 'ZVPHONE'))) {
     case ('-' ): $landline=2;        break;
     case ('+' ): $landline=3;        break;
     case ('IP'): $landline=4;        break;
     default    : $landline=1;        break;
    }
    return $landline;
   break;
   case "ZVWC":
    switch (conv(_odbc_result($result, 'ZVWC'))) {
     case ('-' ): $toilettype=1;      break;
     case ('Р' ): $toilettype=2;      break;
     case ('С' ): $toilettype=3;      break;
     case ('Т' ): $toilettype=4;      break;
     case ('2C'): $toilettype=5;      break;
     default    : $toilettype=1;      break;
    }
    return $toilettype;
   break;
   
   case "ZVPLAN":
    switch (_odbc_result($result, 'ZVPLAN')) {
     case('Изол.'): $layouttype=4;      break;
     case('Смежн'): $layouttype=3;      break;
     case('Трамв'): $layouttype=2;      break;
     default      : $layouttype=1;      break;
    }
   break;
   
   case "ZVSELLTYPE":
    switch (conv(_odbc_result($result, 'ZVSELLTYPE'))) {
     case "-" : $selltypeid=1;      break;
     case "А" : $selltypeid=2;      break;
     case "П" : $selltypeid=3;      break;
     case "О" : $selltypeid=4;      break;
     case "Ч" : $selltypeid=5;      break;
     case "С" : $selltypeid=6;      break;
     case "ПО": $selltypeid=7;      break;
     default  : $selltypeid=1;      break;
    }
    return $selltypeid;
   break;
   
   case "ZVDOCS":
    switch (conv(_odbc_result($result, 'ZVDOCS'))) {
     case "Экскл."   : $relationship=1;      break;
     case "Откр."    : $relationship=2;      break;
     case "Без дог." : $relationship=3;      break;
     default         : $relationship=4;      break;
    }
    return $relationship;
   break;
   
   case "ZDOCSID":
    switch (conv(_odbc_result($result, 'ZDOCSID'))) {
     case "15"   : $relationship=1;      break;
     case "16"   : $relationship=2;      break;
     case "17"   : $relationship=3;      break;
     default     : $relationship=4;      break;
    }
    return $relationship;
   break;
   
   case "ZVFROM":
    switch (conv(_odbc_result($result, 'ZVFROM'))) {
     case "Продавца"    : $source=1;      break;
     case "АН"          : $source=2;      break;
     case "Эксклюз."    : $source=3;      break;
     case "Застройщик"  : $source=4;      break;
     default            : $source=5;      break;
    }
    return $source;
   break;
   
   case "ZFROMID":
    switch (conv(_odbc_result($result, 'ZFROMID'))) {
     case "341"         : $source=1;      break;
     case "342"         : $source=2;      break;
     case "343."        : $source=3;      break;
     case "344"         : $source=4;      break;
     default            : $source=5;      break;
    }
    return $source;
   break;
   
   case "ZVSALETYPE":
    $offertypeid = 0;
    switch (conv(_odbc_result($result, 'ZVSALETYPE'))) {
     case "Продаю"                        : $offertypeid=2;      break;
     case "Продажа"                       : $offertypeid=2;      break;
     case "Сдам в аренду"                 : $offertypeid=4;      break;
     case "Аренда"                        : $offertypeid=4;      break;
     case "Сдам в субаренду"              : $offertypeid=6;      break;
     case "Меняю"                         : $offertypeid=7;      break;
     case "Аренда ПВ"                     : $offertypeid=8;      break;
     default                              : $offertypeid=1;      break;
    }
    if ($offertypeid==0) echo "Unknown offertypeid: "._odbc_result($result, 'ZVSALETYPE');
    return $offertypeid;
   break;
   
   case "ZVGARAGE":
    $ret = 0;
    switch (conv(_odbc_result($result, 'ZVGARAGE'))) {
     case "Нет"            : $ret=2;      break;
     case "Встроенный"     : $ret=3;      break;
     case "Отдельный"      : $ret=4;      break;
     default               : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ZVGARAGE result: "._odbc_result($result, 'ZVGARAGE');
    return $ret;
   break;
   
   case "ZVBANIA":
    $ret = 0;
    switch (conv(_odbc_result($result, 'ZVBANIA'))) {
     case "Нет"            : $ret=2;      break;
     case "Есть"           : $ret=3;      break;
     case "Сауна"          : $ret=4;      break;
     default               : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ZVGARAGE result: "._odbc_result($result, 'ZVBANIA');
    return $ret;
   break;
   
   case "ZVFLOOR":
    $ret = 0;
    switch (conv(_odbc_result($result, 'ZVFLOOR'))) {
     case "-"              : $ret=1;      break;
     case "Бет"            : $ret=2;      break;
     case "Дер"            : $ret=3;      break;
     case "Гру"            : $ret=4;      break;
     default               : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ZVFLOOR result: "._odbc_result($result, 'ZVFLOOR');
    return $ret;
   break;
   
   case "ZVROOF":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "-"              : $ret=1;      break;
     case "Кир"            : $ret=2;      break;
     case "Жел"            : $ret=3;      break;
     case "Жл1"            : $ret=4;      break;
     case "Бет"            : $ret=5;      break;
     case "Залитая"        : $ret=6;      break;
     case "Смешанная"      : $ret=7;      break;
     default               : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZVWHAT":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "Гараж"          : $ret=2;      break;
     case "Участок"        : $ret=3;      break;
     default               : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZVPOD":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "Асфальт"          : $ret=2;      break;
     case "Грунт"            : $ret=3;      break;
     default                 : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZCONDITIONID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "839"              : $ret=2;      break;
     case "840"              : $ret=3;      break;
     default                 : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZRIGHTBUILDID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "835"              : $ret=2;      break;
     default                 : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZPROPISKAID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "837"               : $ret=2;      break;
     default                  : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZTARGET":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "1047"              : $ret=2;      break;
     case "1048"              : $ret=3;      break;
     case "1049"              : $ret=4;      break;
     case "1050"              : $ret=5;      break;
     case "1051"              : $ret=6;      break;
     case "1052"              : $ret=7;      break;
     case "1053"              : $ret=8;      break;
     default                  : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZVOWNING":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "частная"              : $ret=2;      break;
     case "Фед."                 : $ret=4;      break;
     case "Мун."                 : $ret=5;      break;
     case "Вед."                 : $ret=6;      break;
     case "Ар.с пр.вык."         : $ret=7;      break;
     case "Суб КУГИ"             : $ret=8;      break;
     case "БСП"                  : $ret=9;      break;
     case "Не оф."               : $ret=17;     break;
     default                     : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   
   
   
   
   
   case "ZOTDELID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "209"                 : $ret=2;      break;
     case "210"                 : $ret=3;      break;
     case "211"                 : $ret=4;      break;
     case "212"                 : $ret=5;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZWALLSID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "214"                 : $ret=2;      break;
     case "215"                 : $ret=3;      break;
     case "216"                 : $ret=5;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZFLOORID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "218"                 : $ret=2;      break;
     case "219"                 : $ret=3;      break;
     case "220"                 : $ret=4;      break;
     case "221"                 : $ret=5;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZWCID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "230"                 : $ret=2;      break;
     case "231"                 : $ret=3;      break;
     case "232"                 : $ret=4;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZGASID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "234"                 : $ret=2;      break;
     case "235"                 : $ret=3;      break;
     case "236"                 : $ret=4;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZDOORSID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "226"                 : $ret=2;      break;
     case "227"                 : $ret=3;      break;
     case "228"                 : $ret=4;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZWINDOWSID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "223"                 : $ret=2;      break;
     case "224"                 : $ret=3;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZVHEAT":
   case "ZVHEATING":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "-"                   : $ret=1;      break;
     case "нет"                 : $ret=2;      break;
     case "АГВ"                 : $ret=3;      break;
     case "котельная"           : $ret=4;      break;
     case "паровое"             : $ret=5;      break;
     case "комбинир."           : $ret=6;      break;
     case "центр."              : $ret=7;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZSECURITYID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "-"                   : $ret=1;      break;
     case "818"                 : $ret=3;      break;
     case "292"                 : $ret=2;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZTYPEOWNINGID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "52"                 : $ret=2;      break;
     case "53"                 : $ret=3;      break;
     case "310"                : $ret=1;      break;
     case "311"                : $ret=4;      break;
     case "312"                : $ret=5;      break;
     case "313"                : $ret=6;      break;
     case "314"                : $ret=7;      break;
     case "315"                : $ret=8;      break;
     case "316"                : $ret=9;      break;
     case "317"                : $ret=17;     break;
     default                   : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZSTANDID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "-"                   : $ret=1;      break;
     case "809"                 : $ret=2;      break;
     case "810"                 : $ret=3;      break;
     case "811"                 : $ret=4;      break;
     case "930"                 : $ret=5;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZPLACEPUBLICID":                                            // this list field in ISC to be converted to 1/0 in sl.
    $ret = -1;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "819"                 : $ret=0;      break;
     case "820"                 : $ret=1;      break;
     default                    : $ret=0;      break;
    }
    if ($ret==-1) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   case "ZINTERNETID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "-"                   : $ret=1;      break;
     case "804"                 : $ret=1;      break;
     case "805"                 : $ret=2;      break;
     case "806"                 : $ret=4;      break;
     case "807"                 : $ret=3;      break;
     case "808"                 : $ret=6;      break;
     case "825"                 : $ret=5;      break;
     case "827"                 : $ret=7;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   
   
   case "ZINSERTIONID":
    $ret = 0;
    switch (conv(_odbc_result($result, $ISCcolumnname))) {
     case "-"                   : $ret=1;      break;
     case "814"                 : $ret=1;      break;
     case "815"                 : $ret=2;      break;
     default                    : $ret=1;      break;
    }
    if ($ret==0) echo "Unknown ".$ISCcolumnname." result: "._odbc_result($result, $ISCcolumnname);
    return $ret;
   break;
   
   
   
   
   default:
    return (conv(_odbc_result($result, $ISCcolumnname)));
   break;
  }
 }
?>
