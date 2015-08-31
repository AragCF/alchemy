function ajax_get(action, dest_id, url, data) {
 var AJAX = get_http();                                 // Получаем объект XMLHTTPRequest
 AJAX.working = false;
 if (AJAX) {                                            // Запрос
  stillexecuting=1;
  if (!action) return;                                  // Если не указано действие - не делаем ничего
  url = url + "?action="+encodeURIComponent(action)     // добавляем закодированный текст в URL запроса
  if (data)    url = url + "&data="+encodeURIComponent(data)
//    document.getElementById(dest_id).innerHTML='Выполнение запроса...';
  AJAX.open("GET", url, true);  // создаём запрос
  AJAX.onreadystatechange = function() {                // прикрепляем к запросу функцию-обработчик событий
   if (AJAX.readyState == 4) {                          // 4 - данные готовы для обработки
    switch (dest_id) {
     default:
      fill(dest_id, AJAX.responseText);
     break;
    }
    this.working = false;
   } else {    // данные в процессе получения, можно повеселить пользователя сообщениями 
//    document.getElementById(dest_id).innerHTML='Выполнение запроса...';
//    fill(dest_id, "Loading...");
   }
  }
//  AJAX.setRequestHeader("Content-type", "text/html; charset=utf-8");
  AJAX.send(null);
 } else {
  alert ('AJAX not available.');
 }
// if(!AJAX){
//  alert('Ошибка при создании XMLHTTP объекта!')
// }
}




function ajax_post(action, dest_id, url, data, callback) {
 var           params =         "action="+encodeURIComponent(action) ;   // добавляем закодированный текст
 if (data)     params = params + "&data="+encodeURIComponent(data);
 
 
 var AJAX = get_http();
 if (AJAX) {
  AJAX.open("POST", url, true);
  
  //Send the proper header information along with the request
  AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//  AJAX.setRequestHeader("Content-length", params.length);
//  AJAX.setRequestHeader("Connection", "close");
  
  AJAX.onreadystatechange = function() {                             // Call a function when the state changes.
   if((AJAX.readyState == 4) && (AJAX.status == 200)) {
    fill(dest_id, AJAX.responseText);
//    alert (callback);
    if (callback) {
     callback();
    }
 //   alert(AJAX.responseText);
   }
  }
  AJAX.send(params);
 } else {
  alert ('AJAX not available.');
 }
}

function get_http(){
 var xmlhttp;
 /*@cc_on
 @if (@_jscript_version >= 5)
  try {
   xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
   try {
    xmlhttp = new 
    ActiveXObject("Microsoft.XMLHTTP");
   } catch (E) {
    xmlhttp = false;
   }
  }
 @else
  xmlhttp = false;
 @end @*/
 if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
  try {
   xmlhttp = new XMLHttpRequest();
  } catch (e) {
   xmlhttp = false;
  }
 }
 return xmlhttp;
}

function fill (dest_id, data){
 if (el(dest_id)) {
  el(dest_id).innerHTML = data;
 } else {
  ajaxRet[dest_id]  = data;
 }
}

function sjax_get(action, dest_id, url, data) {
 var AJAX = get_http();
 if (AJAX) {
            url += "?action="+encodeURIComponent(action);                // добавляем закодированный текст в URL запроса
  if (data) url += "&data="+encodeURIComponent(data);
            url += "&random="+Math.random(100);
  AJAX.open("GET", url, false);
//  AJAX.setRequestHeader("Content-type", "text/html");
  AJAX.send(null);
  fill(dest_id,AJAX.responseText);
//  alert (AJAX.responseText);
  return AJAX.responseText;
 } else {
  return false;
 }
}


function sjax_post(action, dest_id, url, data) {
 var AJAX = get_http();
 if (AJAX) {
  var                  params =  "action="+encodeURIComponent(action)    // добавляем закодированный текст в URL запроса
  if (data)            params += "&data="+encodeURIComponent(data)
  AJAX.open("POST", url, false);
  AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  AJAX.send(params);
  fill(dest_id,AJAX.responseText);
 } else {
  return false;
 }                                             
}

