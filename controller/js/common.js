var         popupdefault = new String();
var              ajaxRet = new Array();
var       cardsDisplayed = new Array();
var           cardsTotal = new Array();
var        scrollPercent = new Array();
var      buflocationinfo = new Array();
var lastselectChangejson = new Array();

cardsDisplayed['supply'] = 0;
cardsDisplayed['demand'] = 0;

    cardsTotal['supply'] = 0;
    cardsTotal['demand'] = 0;

var               buf = '';
var         buf_theme = '';
var          filename = '';

var         userAgent = '';
var         operation = '';
var          tabidstr = 'objectdetails_primaryinfo';
var  popupdefault_buf = '';
var       thisobjname = '';
var         tablename = '';
var                go = '';

var       buf_jsonstr = '';
var      buf_jsonstr0 = '';
var      buf_jsonstr1 = '';
var      buf_jsonstr2 = '';
var      buf_jsonstr3 = '';

var          jsonstr0 = '';
var          jsonstr1 = '';
var          jsonstr2 = '';
var          jsonstr3 = '';
var   lasthappinessgo = '';

var          isLoaded = 0;
var      lastMsgCount = 0;
var     prevscrolltop = 0;
var         splitmode = -1;

var           newitem = 0;
var           popupid = 0;
var         tablemode = 0;
var      forcerefresh = 0;
var             tabid = 0;
var            lastid = 0;
var         lastauxid = 0;

var           lastaid = 0;
var       lastphotoid = 0;
var            itemid = 0;
var           isadmin = 0;
var       thispanelid = 0;
var       requestsent = 0;

var            supply = 0;
var            demand = 0;
var        buf_supply = 0;
var        buf_demand = 0;
var                 h = 0;
var              tick = 0;
var          ReportID = 0;
//var      lastinsertid = 0;
var         NumEvents = 0;
var                si = 0;
var            buf_si = 0;
var          isexport = 0;
var    lastexpandedid = 0;
var                id = 0;
var             ad_a  = 0;
var             ad_t  = 0;
var             ad_ta = 0;
var     tasklistcount = 0;
var         autoclose = 0;
var         lastelsid = 0;
var      lastmarketid = 0;

var       scrollThres = 0.9;

//var prevsupplyobjname = '';
//var prevdemandobjname = '';
var   SmoothAnimation = 1;       // enabled by default

var          docWidth = 0;
var      prevdocWidth = 0;

function el(elid){
 if (document.getElementById(elid)) {
  return document.getElementById(elid);
 }
}

function ch_vis(mid){
 el(mid).style.display = (el(mid).style.display == 'block')?'none':'block';
}
function ch_vis2(mid){
 el(mid).style.visibility = (el(mid).style.visibility == 'visible')?'hidden':'visible';
 el(mid).style.height = (el(mid).style.visibility == 'visible')?0:440;
}
function ch_vis_inl(mid){
 el(mid).style.display = (el(mid).style.display == 'inline')?'none':'inline';
}

function hide(mid){
 var ele=el(mid);
 if (ele) {
  if (ele.style.display != 'none') ele.style.display = 'none';
 }
 if (mid=='popupeditor') {
  if (userAgent.indexOf('trident')>0) {      // if ie
   hide('popupeditorbkg');
  } else {
   hideEx('popupeditorbkg');
//   hide('popupeditorbkg');
  }
//  el('popupeditor').innerHTML='';
 }
}
function show(mid){
 if (mid=='popupeditor') {
  if (userAgent.indexOf('trident')>0) {      // if ie
   show('popupeditorbkg');
  } else {
   showEx('popupeditorbkg');
  }
 }
 var ele=el(mid);
 if (ele) {
  if (ele.style.display != 'block') ele.style.display = 'block';
 }
}
function showi(mid){
 var ele=el(mid);
 if (ele) {
  if (ele.style.display != 'inline') ele.style.display = 'inline';
 }
}
function showtr(mid){
 var ele=el(mid);
 if (ele) {
  if (ele.style.display != 'inline') ele.style.display = 'table-row';
 }
}
function showtd(mid){
 var ele=el(mid);
 if (ele) {
  if (ele.style.display != 'inline') ele.style.display = 'table-cell';
 }
}


function hideo(o){
 if (o.style.display != 'none') o.style.display = 'none';
}
function showo(o){
 if (o.style.display != 'block') o.style.display = 'block';
}
function showoi(o){
 if (o.style.display != 'inline') o.style.display = 'inline';
}


function showEx(id) {
 if (SmoothAnimation) {
  jQuery('#'+id).fadeIn('fast');
 } else {
  jQuery('#'+id).show();
 }
}

function hideEx(id) {
 if (SmoothAnimation) {
  jQuery('#'+id).fadeOut('fast');
 } else {
  jQuery('#'+id).hide();
 }
}

function toggle(id) {
 supertoggle(id);
}

function supertoggle(id) {
 if (jQuery('#'+id).css('display')!='block') {
  showEx(id);
 } else {
  hideEx(id);
 }
}

function is_visible(id) {
 ele = el(id);
 if (ele) {
  return (ele.style.display == 'block');
 }
}

function is_visibleo(ele) {
 if (ele) {
  return (ele.style.display == 'block');
 }
}

//function setvalue(elid,text){
// if (el(elid)) {
//  el(elid).value=filter(text);
// }
//}

function setvaluehtml(elid,src){
 if (el(elid) && el(src)) {
  el(elid).value=filter(el(src).innerHTML);
 }
}

function gethtml (id) {
 if (el(id)) return el(id).innerHTML;
}

function sethtml (id, text) {
 if (el(id)) el(id).innerHTML=text;
}

function copyhtml (id, id2) {
 if (el(id) && el(id2)) el(id).innerHTML=el(id2).innerHTML;
}

function addhtml (id, text) {
 if (el(id)) el(id).innerHTML+=", "+text;
}

function addhtmlraw(id, text) {
 if (el(id)) el(id).innerHTML+=text;
}

function setvalue (id, text) {
 if (el(id)) el(id).value=text;
}

function setactiveoptionid (id, optionid) {
 var e = el(id);
 if (e) {
  for (var i=0; i<e.options.length; i++) {
   if (e.options[i].id==optionid) {
//    alert (i);
    e.selectedIndex = i;
   }
  }
 }
// alert (e.selectedIndex);
}

function setactiveoptionhtml (id, optionhtml) {
 var e = el(id);
 if (e) {
  for (var i=0; i<e.options.length; i++) {
   if (e.options[i].innerHTML==optionhtml) {
//    alert (i);
    e.selectedIndex = i;
   }
  }
 }
// alert (e.selectedIndex);
}











function trim(str) {
 return trimBoth(str);
}
function trimLeft(str) {
 return str.replace(/^\s+/, '');
}
function trimRight(str) {
 return str.replace(/\s+$/, '');
}
function trimBoth(str) {
 return trimRight(trimLeft(str));
}
function trimSpaces(str) {
 return str.replace(/\s{2,}/g, ' ');
}

var keyStr = "ABCDEFGHIJKLMNOP" +
             "QRSTUVWXYZabcdef" +
             "ghijklmnopqrstuv" +
             "wxyz0123456789+/" +
             "=";

function encode64(input) {
 input = escape(input);
 var output = "";
 var chr1, chr2, chr3 = "";
 var enc1, enc2, enc3, enc4 = "";
 var i = 0;

 do {
  chr1 = input.charCodeAt(i++);
  chr2 = input.charCodeAt(i++);
  chr3 = input.charCodeAt(i++);

  enc1 = chr1 >> 2;
  enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
  enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
  enc4 = chr3 & 63;

  if (isNaN(chr2)) {
   enc3 = enc4 = 64;
  } else if (isNaN(chr3)) {
   enc4 = 64;
  }
  
  output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
  chr1 = chr2 = chr3 = "";
  enc1 = enc2 = enc3 = enc4 = "";
 } while (i < input.length);

 return output;
}

function strip_tags (input, allowed) {
 allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
 var tags               = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
     commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
 var ret = input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
  return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
 });
// ret = ret.replace(/<\s*(\w+).*?>/, '<$1>');
 return ret;
}

/** Function count the occurrences of substring in a string;
 * @param {String} string   Required. The string;
 * @param {String} subString    Required. The string to search for;
 * @param {Boolean} allowOverlapping    Optional. Default: false;
 */
function substr_count(string, subString, allowOverlapping) {
 string+=""; subString+="";
 if(subString.length<=0) return string.length+1;
 
 var n=0, pos=0;
 var step=(allowOverlapping)?(1):(subString.length);
 
 while(true){
  pos=string.indexOf(subString,pos);
  if(pos>=0){ n++; pos+=step; } else break;
 }
 return(n);
}


function substr(str, start, length) {
 if (length>0) {
  return str.substr(start, length);
 } else {
  return str.substr(start);
 }
}

function strpos(str, needle) {
 return str.indexOf(needle);
}
function strrpos(str, needle) {
 return str.lastIndexOf(needle);
}

function strlen(str) {
 return str.length;
}

function sizeof(arr) {
 return arr.length;
}

function str_replace(search, replace, subject) {
	var f = search, r = replace, s = "" + subject;
	var ra = is_array(r), sa = is_array(s), f = [].concat(f), r = [].concat(r), i = (s = [].concat(s)).length;
 
	while (j = 0, i--) {
		while (s[i] = s[i].split(f[j]).join(ra ? r[j] || "" : r[0]), ++j in f){};
	};
 
	return sa ? s : s[0];
}

function is_array( mixed_var ) {
	return ( mixed_var instanceof Array );
} 


function filter(v) {
 return (strip_tags(v, "<b></b><i></i><u></u><sup></sup><br>"));
}
function linkfilter(v) {
// var s=new String;
// s=v;
// s=s.replace("/[&;']+/i",   '');
 return (strip_tags(v, ""));
}
function msgfilter(v) {
 var s=new String;
 if (v) {
  s=v;
  s=s.replace(/`/g,     '&#96;'  );
  s=s.replace(/'/g,     '&#39;'  );
  s=s.replace(/"/g,     '&#34;'  );
  s=s.replace(/\\/g,    '&#92;'  );
  s=s.replace(/:/g,     '&#58;'  );
  s=s.replace(/</g,     '&#60;'  );
  s=s.replace(/>/g,     '&#62;'  );
  s=s.replace(/{/g,     '&#123;' );
  s=s.replace(/}/g,     '&#125;' );
  
  s=s.split("\n").join("<br>");
  s=s.split("\r").join("");
  s=s.split("\f").join("");
  s=s.split("\t").join(" ");
  s=filter(s);
 }
 return (s);
}

function addpopupeditortable() {
 if (popupdefault.length ==0) {
  popupdefault = el('popupdefault').innerHTML;
  el('popupdefault').innerHTML = "";
 }
 
 if (isadmin) {
  el('popupeditor').innerHTML=popupdefault;
 } else {
//  alert (popupdefault);
  el('xlspreview').innerHTML=popupdefault;
 }
}





var ua = navigator.userAgent.toLowerCase();
var isOpera = (ua.indexOf('opera')  > -1);
var isIE = (!isOpera && ua.indexOf('msie') > -1);

function getDocumentHeight() {
 return Math.max(document.compatMode != 'CSS1Compat' ? document.body.scrollHeight : document.documentElement.scrollHeight, getViewportHeight());
}

function getViewportHeight() {
 return ((document.compatMode || isIE) && !isOpera) ? (document.compatMode == 'CSS1Compat') ? document.documentElement.clientHeight : document.body.clientHeight : (document.parentWindow || document.defaultView).innerHeight;
}

function f_scrollTop() {
 return f_filterResults (
  window.pageYOffset ? window.pageYOffset : 0,
  document.documentElement ? document.documentElement.scrollTop : 0,
  document.body ? document.body.scrollTop : 0
 );
}
function f_filterResults(n_win, n_docel, n_body) {
 var n_result = n_win ? n_win : 0;
 if (n_docel && (!n_result || (n_result > n_docel)))
  n_result = n_docel;
 return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

function doscroll() {
 if (el('popupeditor')) {
  if (prevscrolltop!=f_scrollTop()) {
   prevscrolltop=f_scrollTop();
   el('popupeditor').style.top    = prevscrolltop+'px';
   el('popupeditorbkg').style.top = prevscrolltop+'px';
  }
 }
}






function menuover(id) {
 el(id).src="view/img/topmenu_active"+id+".gif";
}

function menuout(id) {
 el(id).src="view/img/topmenu"+id+".gif";
}










function confirmhidepopup() {
 if (is_visible('popupeditor')) {
  var r = confirm(gethtml('confirmhidepopup'));
  if (r) {
   id = 0;
   hidepopup();
  }
 } else {
  forcerefresh = 1;
  showtable(0);
 }
}

function showpopup(buttons) {
 addpopupeditortable();
 if (isadmin) {
  show('popupeditor');
  
  resizepopup();
 } else {
  
 }
 
 showbuttons(buttons);
}

function hidepopup() {
 hide('popupeditor');
 stopautodate();
 forcerefresh = 1;
 showtable(0);
}

function lastnumber(s) {
 return(s.substr(s.length-1));
}





function load(mode) {
 isadmin = 1-mode;
 
 if (el('adminmenu')) {
  getAdminMenu();
 }
 
 if (go=='tasks') {
  sethtml('flt_AssignedTo', el('option_any').innerHTML + el('AssignedTo').innerHTML);
  sethtml('flt_ProjectID' , el('option_any').innerHTML + el('ProjectID').innerHTML );
  sethtml('flt_TaskListID', el('option_any').innerHTML + el('TaskListID').innerHTML);
 }
 
 var e = el('loading');
 if (e) {
  resize(mode);
  showtable(0);
  setupfilterevents();
  
 // if (el('ticker')) {
 //  h=setInterval("main()",1000);
 // }
 }
 
 if (el('userStatus')) {
  setUserStatus(0);
 }
 
}
function resize(mode) {
 if (!mode) {
  autosize('xlspreview', 'leftcolumn');
 }
 
 if (is_visible('popupeditor')) {
  resizepopup();
 }
 cal_load();
}

function resize_user() {
// vline
}

function resizepopup() {
 if (isadmin) {
  
  var scrollTop=f_scrollTop();
 // alert (scrollTop);
  // alert (el('body').style.overflow);
  
  // jQuery('popupeditorbkg').offset({ top: 10, left: 0 });
  
  if (el('body')) {
   var id='body';
  } else {
   var id='body_admin';
  }
  
  el(id).style.overflow="hidden";
  el('popupeditorbkg').style.top = scrollTop+"px";
  el('popupeditor').style.top    = scrollTop+"px";
  
  var h = jQuery('body').height();
  
 // alert (h);
  
 // .editor
  
 // alert (jQuery('table.editor').height() +" - "+ (h-120));
  
  if (jQuery('table.editor').height()>(h-120)) {
   jQuery('div#popupcanvas').height(h-120);
  } else {
   jQuery('div#popupcanvas').height(jQuery('table.editor').height());
  }
  
  
 // jQuery('#popupcanvas').height(h-150);
  // jQuery('#popupcanvas').width(w-200);
 }
 
// resizepopup();
 cal_load();
 
}


function autosize(id, id2) {
 var e  = el(id);
 var e2 = el(id2);
 if (e && e2) {
  var buf = e.innerHTML;
  e.innerHTML="Resizing...";
  jQuery("#"+id).height(10);
  
  var buf2 = e2.innerHTML;
  e2.innerHTML="Resizing...";
  jQuery("#"+id2).height(10);
  
  var excl  = 0;
  var excl2 = 0;
  
  if (id=='xlspreview') {
   excl = jQuery('#editor').height();
  }
  
//  var par=jQuery('#'+e.parentNode.id).height();
  var par  = e.parentNode;
  var par2 = e2.parentNode;
  
//  alert (par);
 // alert (par.clientHeight);
  jQuery("#"+id ).height(par.clientHeight  - excl);
  jQuery("#"+id2).height(par2.clientHeight - excl2);
  
  e.innerHTML  = buf;
  e2.innerHTML = buf2;
 }
}






function setupfilterevents() {
 jQuery('#leftfilters input:checkbox.filteritem, #leftfilters select.filteritem').click(function(){showtable(0)});
// jQuery('input:checkbox.filteritem, select.filteritem').select(function(){showtable(0)});
 jQuery('#leftfilters select.filteritem').keyup(function(){showtable(0)});
 
 jQuery('#leftfilters input:text.filteritem').keyup(function(){showtable(0)});
 jQuery('body').keyup(function(event){if((event.keyCode==27) && (!event.altKey)) confirmhidepopup();});
// el('body').onkeyup=function(event){if(event.keyCode==27) hidepopup();};
// alert(el('body').onkeyup) ;
 
 jQuery('a.flt_a').click(
  function(event){
   if (jQuery('#div_'+this.id).css('display')=='none') {
    jQuery('a#'+this.id+' .nav-icon-right').attr('class','nav-icon-down');
   } else {
    jQuery('a#'+this.id+' .nav-icon-down').attr('class','nav-icon-right');
   }
   if (SmoothAnimation) {
    jQuery('#div_'+this.id).toggle('fast');
   } else {
    jQuery('#div_'+this.id).toggle();
   }
   return false;
  }
 );
}










function dosubmit(mode) {
 JSON.clear();                                          // here we use JSON to send data to server
 switch (mode) {
  case (1):    // user login
   el('loginresult').innerHTML=el('authorizing').innerHTML;
   JSON.addItem('username',  msgfilter(el('username').value)             );
   JSON.addItem('password',  hex_md5(el('password').value).toLowerCase() );
   var jsonstr = JSON.make();
   ajaxRet['loginres'] = "";
   sjax_post('dologin','loginres','index.php',jsonstr);
//   alert (ajaxRet['loginres']);
   if (ajaxRet['loginres']=='##refresh##') {
    document.location.href = document.location.href;
   } else {
    el('loginresult').innerHTML=ajaxRet['loginres'];
   }
  break;
  case (2):    // partnership request
   JSON.addItem('name',      msgfilter(el('name'    ).value)             );
   JSON.addItem('email',     msgfilter(el('email'   ).value)             );
   JSON.addItem('comments',  msgfilter(el('comments').value)             );
   var jsonstr = JSON.make();
   sjax_post('newpartner','submitresult','index.php',jsonstr);
  break;
 }
}

function dologout() {
// JSON.clear();                                          // here we use JSON to send data to server
// JSON.addItem('a_ction',    msgfilter(el('dologout'    ).value)             );
 
// var jsonstr = JSON.make();
 
// ajaxRet['logoutres'] = "a";
 sjax_post('dologout','logoutres','index.php');
 if (ajaxRet['logoutres']=='##refresh##') {
  document.location.href = substr(document.location.href, 0, strrpos(document.location.href, '/'));
 } else {
  alert ('dologout() result: '+ajaxRet['logoutres']);
 }
}

function menuItemClick(miid) {
 switch (miid) {
  case ('test'):
  break;
  case ('dev/null'):
  break;
  default:
   alert ("Unknown menu action: "+miid);
  break;
 }
}



function getAdminMenu() {
 sjax_get('showadminmenu','adminmenu','index.php');
}

function adminmenuaction(actionid, id, order, placement) {
 switch (actionid) {
  case ('movedown'):
   JSON.clear();                                          // here we use JSON to send data to server
   JSON.addItem('id'           , id                     );
   JSON.addItem('order'        , order+1                );
   JSON.addItem('placement'    , placement              );
   JSON.addItem('prevorder'    , order                  );
   var jsonstr = JSON.make();
   sjax_get('movemenuitem','adminmenu','index.php',jsonstr);
  break;
  case ('moveup'):
   JSON.clear();                                          // here we use JSON to send data to server
   JSON.addItem('id'           , id                     );
   JSON.addItem('order'        , order-1                );
   JSON.addItem('placement'    , placement              );
   JSON.addItem('prevorder'    , order                  );
   var jsonstr = JSON.make();
   sjax_get('movemenuitem','adminmenu','index.php',jsonstr);
  break;
  case ('toggle'):
   JSON.clear();                                          // here we use JSON to send data to server
   JSON.addItem('id'           , id                     );
   JSON.addItem('isvisible'    , 1-order                );
   var jsonstr = JSON.make();
   sjax_get('togglemenuitem','adminmenu','index.php',jsonstr);
  break;
  default:
   alert ('Unsupported action: '+actionid);
  break;
 }
}























function fb_click(id,groupid) {
 var v = 0;
 
 if (el('p'+id).className=='fcb') {
  el('p'+id).className='fcb_checked';
  v=1;
//  el(id).value=v;
  
 } else if (el('p'+id).className=='fcb_checked') {
  el('p'+id).className='fcb';
  v=0;
//  el(id).value=v;
  
 } else  if (el('p'+id).className=='frb') {
  jQuery("#"+groupid+" .frb_checked").each(
   function(){
    this.className='frb';
   }
  );
  el('p'+id).className='frb_checked';
 } else if (el('p'+id).className=='frb_checked') {
  el('p'+id).className='frb';
 }
 
 switch (groupid) {
  case ('r_customers'):
  case ('r_viewmode'):
  case ('r_projectsview'):
  case ('r_cutby'):
  
  case ('r_rooms'):
  case ('r_districts'):
  case ('r_housetypes'):
  case ('r_ShowAll'):
   showtable(0);
  break;
  case ('r_priorityid'):
  break;
  default:
//   alert ('fb_click: '+id+', value: '+v+', groupid: '+groupid);
  break;
 }
 
 //showtable(0);
}

function fcb_value (id) {
 return (el('p'+id).className=='fcb_checked')?1:0;
}

function fcb_getvalue (id) {
 if (el('p'+id)) {
  return (el('p'+id).className=='fcb_checked')?1:0;
 }
}

function fcb_setvalue (id, v) {
 if (v) {
  el('p'+id).className='fcb_checked';
 } else {
  el('p'+id).className='fcb';
 }
}


function goToPage(id) {
 var els = el('fields').getElementsByClassName('field');
 
// alert (lastauxid);
 
 for (var i=0; i<els.length; i++) {
  if (is_visibleo(els[i])) {
   if (lastauxid=='next') {
    id = els[i+1].id;
   } else {
    id = els[i-1].id;
   }
   break;
  }
 }
// alert ('goToPage: '+id);
 
 for (var i=0; i<els.length; i++) {
//  alert (els[i].id);
  if (els[i].id==id) {
//   alert ('show: '+els[i].id);
   showo (els[i]);
   var elsi = els[i].getElementsByTagName('input');
//   for (var j=0; j<elsi.length; j++) {
   if (elsi[0]) {
//    alert (elsi[0].id);
    
    if (elsi[0].id=='City') {
     elsi[1].focus();
    } else {
     elsi[0].focus();
    }
   }
//   }
   
  } else {
//   alert ('hide: '+els[i].id);
   hideo (els[i]);
  }
 }
// alert ('test');
 
 resizepopup();
}

function test() {
 fcb_setvalue('HasDoors', 1);
 
// alert (123);
 
}

function btn_click(id,groupid,auxid) {
 switch (groupid) {
  case ('goToPageSave-HasDoors-No'):
   fcb_setvalue('HasDoors', 0);
   
   lastid = id;
   lastauxid = auxid;
   autoclose = 2;
   btn_click(0, 'addProject');
   
  break;
  case ('goToPageSave-HasDoors-Yes'):
   fcb_setvalue('HasDoors', 1);
   
   lastid = id;
   lastauxid = auxid;
   autoclose = 2;
   btn_click(0, 'addProject');
   
  break;
  case ('goToPageSave'):
   // ajax save code
   lastid = id;
   lastauxid = auxid;
   autoclose = 2;
   btn_click(0, 'addProject');
   
  break;
  case ('goToPageSkip'):
   goToPage(id);
  break;
  
  case ('popupBtnClick'):
   popupBtnClick(id);
  break;
  
  case ('post'):
   if (el('content').value!="") {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem('id'           , id                             );
    JSON.addItem('content'      , msgfilter(el('content').value) );
    JSON.addItem('ispinned'     , fcb_value('ispinned')          );
    JSON.addItem('reportid'     , ReportID                       );
    JSON.addItem('usertime'     , el('usertime').innerHTML       );
    
    var jsonstr = JSON.make();
//    alert (jsonstr);
    ajax_post('postevent','buf','index.php',jsonstr,c_postevent);
    el('content').value = "";
    
   }
  break;
  
  case ('download'):
   showtable(1);
  break;
  
  case ('toList0'):
  case ('toList1'):
  case ('toList2'):
  case ('toList3'):
  case ('toList4'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(    'tablename', 'tasks'  );
   JSON.addItem(   'columnname', 'ListID' );
   switch (groupid) {
    case ('toList0'):
     JSON.addItem(         'data', '0'      );
    break;
    case ('toList1'):
     JSON.addItem(         'data', '1'      );
    break;
    case ('toList2'):
     JSON.addItem(         'data', '2'      );
    break;
    case ('toList3'):
     JSON.addItem(         'data', '3'      );
    break;
    case ('toList4'):
     JSON.addItem(         'data', '4'      );
    break;
   }
   JSON.addItem(           'id', id       );
   
   var jsonstr = JSON.make();
   ajax_post('saveField','buf','index.php',jsonstr,c_setListID);
  break;
  
  case ('expandTask'):
   if (el('da_'+id).style.display != 'table-cell') {
    lastexpandedid = id;
    showtd('da_'+id);
//    showtr('ab_'+id);
    showtd('pt_'+id);
    show  ('tt_'+id);
    showtd('co_'+id);
    showtd('nc_'+id);
    hide  ('et_'+id);
    showi ('mt_'+id);
    dest_id = 'co_'+id;
    getComments('tasks', id);
   }
  break;
  
  case ('minimizeTask'):
   if (el('da_'+id).style.display == 'table-cell') {
    lastexpandedid = 0;
    hide('da_'+id);
//    hide('ab_'+id);
    hide('pt_'+id);
    hide('tt_'+id);
    hide('co_'+id);
    hide('nc_'+id);
    hide('mt_'+id);
    showi('et_'+id);
   }
  break;
  
  case ('addTask'):
   var priorityid = frb_getvalue('r_priorityid');
   if (priorityid<1) priorityid = 3;
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', el('ID').value                      );
   JSON.addItem(       'Title', msgfilter(el('Title').value)        );
   JSON.addItem(       'Labor', msgfilter(el('Labor').value)        );
   JSON.addItem(     'DateDue', el('DateDue').value                 );
   JSON.addItem(  'AssignedTo', getSelectedId('AssignedTo')         );
   JSON.addItem(   'ProjectID', getSelectedId('ProjectID')          );
   JSON.addItem(  'TaskListID', getSelectedId('TaskListID')         );
   JSON.addItem( 'Description', msgfilter(el('Description').value)  );
   JSON.addItem(  'PriorityID', priorityid                          );
   var jsonstr = JSON.make();
   
//   alert (jsonstr);
   
   ajax_post('addTask','addTaskRslt','index.php',jsonstr,c_addTask);
  break;
  
  case ('deleteTask'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', id       );
   
   var jsonstr = JSON.make();
   ajax_post('deleteTask','addTaskRslt','index.php',jsonstr,c_setListID);
  break;
  
  case ('editTask'):
   var Title       = (gethtml('ti_' +id));
   var Labor       = (gethtml('lab_'+id));
   var DateDue     = (gethtml('dat_'+id));
   var AssignedTo  = (gethtml('ast_'+id));
   var ProjectName = (gethtml('prt_'+id));
   var TaskList    = (gethtml('tlt_'+id));
   var Priority    = (gethtml('pri_'+id));
   var Description = (gethtml('des_'+id));
   
   showpopup(Array('popup_btn_save','popup_btn_close'));
   
   el('ID'     ).value = id;
   el('Title'  ).value = Title;
   el('Labor'  ).value = Labor;
   el('DateDue').value = DateDue;
   setSelectedIdByTitle( 'AssignedTo', AssignedTo  );
   setSelectedIdByTitle(  'ProjectID', ProjectName );
   setSelectedIdByTitle( 'TaskListID', TaskList    );
   el('Description').value = str_replace("<br>", "\n", Description);
   frb_setvalueByTitle('r_priorityid',Priority);
   el('CommentToTask_-1').id = 'CommentToTask_'+id;
   el('sendComment_id').id   = id;
   
   dest_id = 'comments';
   getComments('tasks',id,'comments');
  break;
  
  case ('newTask'):
   showpopup(Array('popup_btn_save','popup_btn_close'));
   
   newTask();
  break;
  
  case ('resumeTask'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', id       );
   
   var jsonstr = JSON.make();
//   alert (jsonstr);
   ajax_post('resumeTask','ajaxdebug','index.php',jsonstr,c_setListID);
  break;
  
  case ('pauseTask'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', id       );
   
   var jsonstr = JSON.make();
//   alert (jsonstr);
   ajax_post('pauseTask','ajaxdebug','index.php',jsonstr,c_setListID);
  break;
  
  case ('deleteComment'):
   parentid = substr(id, strpos(id,'_')+1, 0);
   id       = substr(id, 0, strpos(id,'_'));
   
   deleteComment(id, parentid);
  break;
  
  
  
  
  case ('newProject'):
//   alert (isadmin);
   if (isadmin) {
    showpopup(Array('popup_btn_save','popup_btn_close'));
   } else {
    showpopup(Array());
   }
   
   showloader('popupcanvas');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', 0);
   var jsonstr = JSON.make();
   ajax_post('newProject','popupcanvas','index.php',jsonstr,c_newProject);
  break;
  
  case ('addProject'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', el('ID').value        );
   
   var ids = new Array(
    'Firstname', 'Surname', 'Middlename', 'City', 'Street', 'House', 'Building', 'Flat', 'Comment', 'Email', 'PhoneMain', 'PhoneAux', 'SendSMS',
     'PhoneHome', 'OrderNumber', 'DateFinish', 'Cost', 'CostPrepay', 'Description', 'DateMeasure', 'DateInstall', 'ShortTitle', 'FolderName', 'DateMeasure', 'DateInstall'
    );
   
   for (var i=0; i<sizeof(ids); i++) {
    if (el(ids[i])) {
     JSON.addItem(   ids[i], elvf(ids[i])     );
    }
   }
   
   /*
   JSON.addItem(   'Firstname', elvf('Firstname')     );
   JSON.addItem(     'Surname', elvf('Surname')       );
   JSON.addItem(  'Middlename', elvf('Middlename')    );
   JSON.addItem(        'City', elvf('City')          );
   JSON.addItem(      'Street', elvf('Street')        );
   JSON.addItem(       'House', elvf('House')         );
   JSON.addItem(    'Building', elvf('Building')      );
   JSON.addItem(        'Flat', elvf('Flat')          );
   
   JSON.addItem(     'Comment', elvf('Comment')       );
   JSON.addItem(       'Email', elvf('Email')         );
   JSON.addItem(   'PhoneMain', elvf('PhoneMain')     );
   JSON.addItem(    'PhoneAux', elvf('PhoneAux')      );
   JSON.addItem(   'PhoneHome', elvf('PhoneHome')     );
   JSON.addItem( 'OrderNumber', elvf('OrderNumber')   );
   
   JSON.addItem(  'DateFinish', elvf('DateFinish')    );
   JSON.addItem(        'Cost', elvf('Cost')          );
   JSON.addItem(  'CostPrepay', elvf('CostPrepay')    );
   JSON.addItem( 'Description', elvf('Description')   );
   JSON.addItem( 'DateMeasure', elvf('DateMeasure')   );
   JSON.addItem( 'DateInstall', elvf('DateInstall')   );
   
   JSON.addItem(  'ShortTitle', elvf('ShortTitle')    );
   JSON.addItem(  'FolderName', elvf('FolderName')    );
   */
   
   /*
   JSON.addItem(  'AssignedTo', getSelectedId('AssignedTo')         );
   JSON.addItem(  'CustomerID', getSelectedId('CustomerID')         );
   JSON.addItem( 'Description', msgfilter(el('Description').value)  );
   JSON.addItem(        'Cost', msgfilter(el('Cost').value)         );
   */
   
   /*
   JSON.addItem( 'DateMeasure', elvf('DateMeasure')   );
   JSON.addItem( 'DateInstall', elvf('DateInstall')   );
   */
   
   JSON.addItem(    'HasDoors', fcbv('HasDoors'   )   );
   JSON.addItem(     'SendSMS', fcbv('SendSMS'    )   );
   
//   JSON.addItem( 'IsCompleted', is_visible('div_Cost'));
   JSON.addItem(      'lastid', lastid                );
   
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('addProject','addProjectRslt','index.php',jsonstr,c_addProject);
  break;
  
  case ('editProject'):
   showpopup(Array('popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('newProject','popupcanvas','index.php',jsonstr,fillUploads);
  break;
  
  case ('closeProject'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', id       );
   
   var jsonstr = JSON.make();
   ajax_post('closeProject','debug','index.php',jsonstr,c_setListID);
  break;
  
  case ('deleteProject'):
   var r = confirm(gethtml('confirmrecorddelete'));
   if (r) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(           'ID', id       );
    
    var jsonstr = JSON.make();
    ajax_post('deleteProject','debug','index.php',jsonstr,c_setListID);
   }
  break;
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  case ('addSpider'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', el('ID').value                  );
   JSON.addItem(    'ParentID', el('ParentID').value            );
   JSON.addItem(        'Name', el('Name').value                );
   JSON.addItem(       'Phone', el('Phone').value               );
   JSON.addItem(       'Email', el('Email').value               );
   JSON.addItem(     'Comment', msgfilter(el('Comment').value)  );
   JSON.addItem(    'NextCall', el('NextCall').value            );
   JSON.addItem(       'Place', el('Place').value               );
//   JSON.addItem(  'AssignedTo', getSelectedId('AssignedTo')         );
   
   var jsonstr = JSON.make();
   ajax_post('addSpider','addSpiderRslt','index.php',jsonstr,c_setListID);
  break;
  
  case ('editSpider'):
   var ParentID = (gethtml('ParentID_' +id));
   var Name     = (gethtml('Name_'     +id));
   var Phone    = (gethtml('Phone_'    +id));
   var Email    = (gethtml('Email_'    +id));
   var Comment  = (gethtml('Comment_'  +id));
   var NextCall = (gethtml('NextCall_' +id));
   var Place    = (gethtml('Place_'    +id));
   
   Comment = str_replace("<br>", "\n", Comment);
   
   el('ID'      ).value = id;
   el('ParentID').value = ParentID;
   el('Name'    ).value = Name;
   el('Phone'   ).value = Phone;
   el('Email'   ).value = Email;
   el('Comment' ).value = Comment;
   el('NextCall').value = NextCall;
   el('Place'   ).value = Place;
   
   
  break;
  
  case ('deleteSpider'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', id       );
   
   var jsonstr = JSON.make();
   ajax_post('deleteSpider','addSpiderRslt','index.php',jsonstr,c_setListID);
  break;
  
  case ('sendComment'):
   addCommentToTask(id);
  break;
  
  case ('addProjectParticipant'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(    'ProjectID', el('ID').value               );
   JSON.addItem(       'UserID', getSelectedId('AssignedTo')  );
   JSON.addItem(    'NewUserID', getSelectedId('users')       );
   
   var jsonstr = JSON.make();
   setstatus('participants', el('loading').innerHTML);
   ajax_post('addProjectParticipant','participants','index.php',jsonstr,c_addParticipant);
  break;
  
  case ('removeProjectParticipant'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(    'ProjectID', el('ID').value               );
   JSON.addItem(       'UserID', getSelectedId('AssignedTo')  );
   JSON.addItem(           'ID', id                           );
   
   var jsonstr = JSON.make();
   setstatus('participants', el('loading').innerHTML);
   ajax_post('removeProjectParticipant','participants','index.php',jsonstr,c_addParticipant);
  break;
  
  
  
  case ('addUserPrivilege'):
   if (eloc('pages')) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(      'UserID', elv ('ID'   )  );
    JSON.addItem(    'PageName', elsh('pages')  );
    
    var jsonstr = JSON.make();
    setstatus('privileges', el('loading').innerHTML);
    ajax_post('addUserPrivilege','privileges','index.php',jsonstr,c_addUserPrivilege);
   }
  break;
  
  case ('removeUserPrivilege'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id             );
   JSON.addItem(      'UserID', elv ('ID'   )  );
   
   var jsonstr = JSON.make();
   setstatus('privileges', el('loading').innerHTML);
   ajax_post('removeUserPrivilege','privileges','index.php',jsonstr,c_addUserPrivilege);
  break;
  
  
  
  
  
  case ('newMoney'):
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', 0);
   var jsonstr = JSON.make();
   ajax_post('newMoney','popupcanvas','index.php',jsonstr,newMoney);
  break;
  
  case ('addMoney'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', elvf ('ID'             ));
   JSON.addItem(      'TypeID', elsid('TypeID'         ));
   JSON.addItem(   'DateAdded', elvf ('DateAdded'      ));
   JSON.addItem(       'Value', elvf ('Value'          ));
   JSON.addItem(   'AccountID', elsid('AccountID'      ));
   JSON.addItem(    'ObjectID', elsid('ObjectID'       ));
   JSON.addItem(  'CustomerID', elsid('CustomerID'     ));
   JSON.addItem(      'UserID', elsid('UserID'         ));
   JSON.addItem(     'GroupID', elsid('GroupID'        ));
   JSON.addItem(    'SourceID', elsid('SourceID'       ));
   JSON.addItem(   'PlaceName', elvf ('PlaceName_text' ));
   JSON.addItem(   'PlaceType', elvf ('PlaceType_text' ));
   JSON.addItem(     'Content', elvf ('Content'        ));
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('addMoney','addMoneyRslt','index.php',jsonstr,c_addMoney);
  break;
  
  case ('editMoney'):
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('newMoney','popupcanvas','index.php',jsonstr,c_newMoney);
  break;
  
  case ('deleteMoney'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', id       );
   
   var jsonstr = JSON.make();
   ajax_post('deleteMoney','deleteMoneyRslt','index.php',jsonstr,c_deleteMoney);
  break;
  
  
  
  case ('newDiary'):
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', 0);
   var jsonstr = JSON.make();
   ajax_post('newDiary','popupcanvas','index.php',jsonstr,newDiary);
  break;
  
  
  case ('addDiary'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(           'ID', elvf ('ID'             ));
   JSON.addItem(   'DateTarget', elvf ('DateTarget'     ));
   JSON.addItem(  'Description', elvf ('Description'    ));
   JSON.addItem(    'WhatsDone', elvf ('WhatsDone'      ));
   JSON.addItem( 'WhatsNotDone', elvf ('WhatsNotDone'   ));
   JSON.addItem(         'Plan', elvf ('Plan'           ));
   JSON.addItem(     'Insights', elvf ('Insights'       ));
   JSON.addItem(     'HomeWork', elvf ('HomeWork'       ));
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('addDiary','addDiaryRslt','index.php',jsonstr,c_addDiary);
  break;
  
  case ('editDiary'):
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('newDiary','popupcanvas','index.php',jsonstr,c_newDiary);
  break;
  
  case ('deleteDiary'):
   var r = confirm(gethtml('confirmrecorddelete'));
   if (r) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(           'ID', id       );
    
    var jsonstr = JSON.make();
    ajax_post('deleteDiary','deleteDiaryRslt','index.php',jsonstr,c_deleteDiary);
   }
  break;
  
  
  
  
  
  
  
  case ('newObject'):
   lasthappinessgo = 'objects';
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test2');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', 0);
   var jsonstr = JSON.make();
   ajax_post('newObject','popupcanvas','index.php',jsonstr,newObject);
  break;
  
  
  
  
  
  
  
  
  case ('addObject'):
   lasthappinessgo = 'objects';
   var allow = 0;
   if (parseInt(elvf('Cost'))<300000) {
    if (confirm(gethtml('confirmlowcost'))) {
     allow = 1;
    }
   } else {
    allow = 1;
   }
   
   if (allow) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(                    'ID', elvf ('ID'                    ));
    JSON.addItem(            'DateTarget', elvf ('DateTarget'            ));
    if (el('UserID')) {
     JSON.addItem(               'UserID', elsid('UserID'                ));
    } else {
     JSON.addItem(               'UserID', elh  ('UserID_static'         ));
    }
    JSON.addItem(                 'Phone', elvf ('Phone'                 ));
    JSON.addItem(              'SourceID', elsid('SourceID'              ));
    JSON.addItem(           'ContactName', elvf ('ContactName'           ));
    JSON.addItem(          'ContactEmail', elvf ('ContactEmail'          ));
    JSON.addItem(                  'Cost', elvf ('Cost'                  ));
    JSON.addItem(     'MethodOfPaymentID', elsid('MethodOfPaymentID'     ));
    JSON.addItem(     'CustomerSubtypeID', elsid('CustomerSubtypeID'     ));
    JSON.addItem(            'MediatorID', elsid('MediatorID'            ));
    JSON.addItem(              'MarketID', elsid('MarketID'              ));
    JSON.addItem(              'AgentFee', elvf ('AgentFee'              ));
    JSON.addItem(            'DistrictID', elsid('DistrictID'            ));
    JSON.addItem(               'Address', elvf ('Address'               ));
    JSON.addItem(        'ExchangeOption', elvf ('ExchangeOption'        ));
    JSON.addItem(        'TargetAudience', elvf ('TargetAudience'        ));
    JSON.addItem(               'Problem', elvf ('Problem'               ));
    
    JSON.addItem(                 'Floor', elvf ('Floor'                 ));
    JSON.addItem(                'Floors', elvf ('Floors'                ));
    JSON.addItem(             'Entrances', elvf ('Entrances'             ));
    JSON.addItem(             'Elevators', elvf ('Elevators'             ));
    
    JSON.addItem(           'HouseTypeID', elsid('HouseTypeID'           ));
    JSON.addItem(     'OverlappingTypeID', elsid('OverlappingTypeID'     ));
    JSON.addItem( 'CompletionDateQuarter', elsid('CompletionDateQuarter' ));
    JSON.addItem(    'CompletionDateYear', elvf ('CompletionDateYear'    ));
    
    JSON.addItem(              'Security', fcbv ('Security'              ));
    JSON.addItem(             'Concierge', fcbv ('Concierge'             ));
    JSON.addItem(      'OperationService', fcbv ('OperationService'      ));
    JSON.addItem(                 'Chute', fcbv ('Chute'                 ));
    JSON.addItem(                   'Gas', fcbv ('Gas'                   ));
    JSON.addItem(               'Parking', fcbv ('Parking'               ));
    JSON.addItem(        'TechnicalFloor', fcbv ('TechnicalFloor'        ));
    
    JSON.addItem(            'RoomsTotal', elvf ('RoomsTotal'            ));
    JSON.addItem(            'SpaceTotal', elvf ('SpaceTotal'            ));
    JSON.addItem(          'SpaceKitchen', elvf ('SpaceKitchen'          ));
    JSON.addItem(         'RoomsIsolated', elvf ('RoomsIsolated'         ));
    JSON.addItem(           'SpaceLiving', elvf ('SpaceLiving'           ));
    JSON.addItem(             'Balconies', elvf ('Balconies'             ));
    
    JSON.addItem(          'LayoutTypeID', elsid('LayoutTypeID'          ));
    JSON.addItem(          'ToiletTypeID', elsid('ToiletTypeID'          ));
    JSON.addItem(           'ConditionID', elsid('ConditionID'           ));
    
    JSON.addItem(           'FinishingID', elsid('FinishingID'           ));
    JSON.addItem(        'FloorSurfaceID', elsid('FloorSurfaceID'        ));
    JSON.addItem(           'StoveTypeID', elsid('StoveTypeID'           ));
    JSON.addItem(           'DoorsTypeID', elsid('DoorsTypeID'           ));
    JSON.addItem(        'WallsSurfaceID', elsid('WallsSurfaceID'        ));
    JSON.addItem(       'WallsMaterialID', elsid('WallsMaterialID'       ));
    JSON.addItem(   'BathroomEquipmentID', elsid('BathroomEquipmentID'   ));
    JSON.addItem(         'WindowsTypeID', elsid('WindowsTypeID'         ));
    
    JSON.addItem(        'RightsSourceID', elsid('RightsSourceID'        ));
    JSON.addItem(  'RightsTransmissionID', elsid('RightsTransmissionID'  ));
    JSON.addItem('ResidentialComplexName', elvf ('ResidentialComplexName'));
    JSON.addItem(             'Developer', elvf ('Developer'             ));
    JSON.addItem(       'AgreementNumber', elvf ('AgreementNumber'       ));
    JSON.addItem(             'KadNumber', elvf ('KadNumber'             ));
    
    JSON.addItem(                'Status', elvf ('Status'                ));
    
    if (elsid('HouseTypeID')) {
     JSON.addItem(          'HouseTypeID', elsid('HouseTypeID'           ));
    } else {
     JSON.addItem(          'HouseTypeID', 1);
    }
    
    JSON.addItem(     'TargetContact', fcbv ('TargetContact'       ));
    JSON.addItem(     'TargetMeeting', fcbv ('TargetMeeting'       ));
    JSON.addItem(       'OfficeVisit', fcbv ('OfficeVisit'         ));
    JSON.addItem(        'ObjectShow', fcbv ('ObjectShow'          ));
    JSON.addItem(      'TargetAgreed', fcbv ('TargetAgreed'        ));
    JSON.addItem(      'HasAgreement', fcbv ('HasAgreement'        ));
    JSON.addItem(   'DepositReceived', fcbv ('DepositReceived'     ));
    JSON.addItem(         'Handshake', fcbv ('Handshake'           ));
    JSON.addItem(        'GiftsGiven', fcbv ('GiftsGiven'          ));
    JSON.addItem(    'HasCertificate', fcbv ('HasCertificate'      ));
    JSON.addItem(       'PhoneDenied', fcbv ('PhoneDenied'         ));
    JSON.addItem(     'MeetingDenied', fcbv ('MeetingDenied'       ));
    JSON.addItem(     'ServiceDenied', fcbv ('ServiceDenied'       ));
    JSON.addItem(       'NoAgreement', fcbv ('NoAgreement'         ));
    JSON.addItem(      'SaleCanceled', fcbv ('SaleCanceled'        ));
    JSON.addItem(          'SoldSelf', fcbv ('SoldSelf'            ));
    JSON.addItem(       'CostTooHigh', fcbv ('CostTooHigh'         ));
    JSON.addItem(        'SalePaused', fcbv ('SalePaused'          ));
    
    showloader('result');
    var jsonstr = JSON.make();
    ajax_post('addObject','addObjectRslt','index.php',jsonstr,c_addObject);
   }
  break;
  
  case ('editObject'):
   lasthappinessgo = 'objects';
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('newObject','popupcanvas','index.php',jsonstr,c_newObject);
  break;
  
  case ('deleteObject'):
   lasthappinessgo = 'objects';
   var r = confirm(gethtml('confirmrecorddelete'));
   if (r) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(           'ID', id       );
    
    var jsonstr = JSON.make();
    ajax_post('deleteObject','deleteObjectRslt','index.php',jsonstr,c_deleteObject);
   }
  break;
  
  case ('cloneObject'):
   lasthappinessgo = 'objects';
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('cloneObject','popupcanvas','index.php',jsonstr,c_newObject);
  break;
  
  
  case ('deleteNBSubitem'):
   deleteNBSubitem(id);
  break;
  
  case ('addNBSubitem'):
   addNBSubitem(id);
  break;
  
  
  
  
  
  
  
  
  
  case ('newCustomer'):
   lasthappinessgo = 'customers';
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test2');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem('ID', 0);
   var jsonstr = JSON.make();
   ajax_post('newCustomer','popupcanvas','index.php',jsonstr,newCustomer);
  break;
  
  
  
  
  
  
  
  
  case ('addCustomer'):
   lasthappinessgo = 'customers';
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(                'ID', elvf ('ID'                  ));
//   JSON.addItem(            'UserID', elvf ('UserID'              ));
   if (el('UserID')) {
    JSON.addItem(           'UserID', elsid('UserID'              ));
   } else {
    JSON.addItem(           'UserID', elh  ('UserID_static'       ));
   }
   JSON.addItem(        'DateTarget', elvf ('DateTarget'          ));
   JSON.addItem(           'Address', elvf ('Address'             ));
   JSON.addItem(             'Phone', elvf ('Phone'               ));
   JSON.addItem(              'Name', elvf ('Name'                ));
   JSON.addItem(             'Email', elvf ('Email'               ));
   JSON.addItem(          'MarketID', elsid('MarketID'            ));
   
//   JSON.addItem(          'Duration', elvf ('Duration'            ));
//   JSON.addItem(    'CustomerTypeID', elsid('CustomerTypeID'      ));
   
   JSON.addItem( 'CustomerSubtypeID', elsid('CustomerSubtypeID'   ));
   JSON.addItem( 'MethodOfPaymentID', elsid('MethodOfPaymentID'   ));
   JSON.addItem(          'SourceID', elsid('SourceID'            ));
   JSON.addItem(            'Status', elvf ('Status'              ));
   JSON.addItem(       'DirectionID', elsid('DirectionID'         ));
   
   JSON.addItem(           'FloorID', elsid('FloorID'             ));
   JSON.addItem(           'MaxCost', elvf ('MaxCost'             ));
   JSON.addItem(       'DistrictIDs', getDistricts()               );
   JSON.addItem(          'ObjectID', elsid('ObjectID'            ));
   
   
//   JSON.addItem(       'HouseTypeID', elsid('HouseTypeID'         ));
   
   var DesiredRoomsIDs = fcbso('desiredrooms' );
   var HouseTypeIDs    = fcbso('housetypes'   );
   
//   alert (DesiredRoomsIDs);
   
   JSON.addItem(   'DesiredRoomsIDs', DesiredRoomsIDs    );
   JSON.addItem(      'HouseTypeIDs', HouseTypeIDs       );
   
   JSON.addItem(     'TargetContact', fcbv ('TargetContact'       ));
//   JSON.addItem(     'TargetMeeting', fcbv ('TargetMeeting'       ));
   JSON.addItem(       'OfficeVisit', fcbv ('OfficeVisit'         ));
   JSON.addItem(        'ObjectShow', fcbv ('ObjectShow'          ));
//   JSON.addItem(      'TargetAgreed', fcbv ('TargetAgreed'        ));
   JSON.addItem(      'HasAgreement', fcbv ('HasAgreement'        ));
   JSON.addItem(   'DepositReceived', fcbv ('DepositReceived'     ));
   JSON.addItem(         'Handshake', fcbv ('Handshake'           ));
   
   JSON.addItem(       'PhoneDenied', fcbv ('PhoneDenied'         ));
   JSON.addItem(    'HasCertificate', fcbv ('HasCertificate'      ));
   JSON.addItem(     'MeetingDenied', fcbv ('MeetingDenied'       ));
   JSON.addItem(     'ServiceDenied', fcbv ('ServiceDenied'       ));
   JSON.addItem(          'SoldSelf', fcbv ('SoldSelf'            ));
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('addCustomer','addCustomerRslt','index.php',jsonstr,c_addCustomer);
  break;
  
  case ('editCustomer'):
   lasthappinessgo = 'customers';
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('newCustomer','popupcanvas','index.php',jsonstr,c_newCustomer);
  break;
  
  case ('deleteCustomer'):
   lasthappinessgo = 'customers';
   var r = confirm(gethtml('confirmrecorddelete'));
   if (r) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(           'ID', id       );
    
    var jsonstr = JSON.make();
    ajax_post('deleteCustomer','deleteCustomerRslt','index.php',jsonstr,c_deleteCustomer);
   }
  break;
  
  
  
  
  
  case ('addStatus'):
   JSON.clear();                                                        // here we use JSON to send data to server
//   JSON.addItem(                'ID', elvf ('ID'                  ));
   JSON.addItem(          'DateTime', elvf ('DateTime'            ));
   JSON.addItem(           'Address', elvf ('sAddress'            ));
   JSON.addItem(          'Duration', elvf ('Duration_Min')+":"+elvf ('Duration_Sec'));
   JSON.addItem(           'Comment', elvf ('Comment'             ));
   JSON.addItem(            'TypeID', elsid('TypeID'              ));
   JSON.addItem(          'NextStep', elvf ('NextStep'            ));
   JSON.addItem(          'ParentID', elvf ('ID'                  ));
   JSON.addItem(        'ParentName', go                           );
   
   showloader('statuses');
   var jsonstr = JSON.make();
   ajax_post('addStatus','statuses','index.php',jsonstr,c_addStatus);
  break;
  
  
  
  
  case ('newUser'):
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', 0);
   var jsonstr = JSON.make();
   ajax_post('newUser','popupcanvas','index.php',jsonstr,newUser);
  break;
  
  case ('addUser'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(                'ID', elvf ('ID'                  ));
   JSON.addItem(         'Firstname', elvf ('Firstname'           ));
   JSON.addItem(           'Surname', elvf ('Surname'             ));
   JSON.addItem(        'Middlename', elvf ('Middlename'          ));
   
   JSON.addItem(           'Address', elvf ('Address'             ));
   JSON.addItem(          'Username', elvf ('Username'            ));
   JSON.addItem(             'Email', elvf ('Email'               ));
   JSON.addItem(            'Email2', elvf ('Email2'              ));
   JSON.addItem(             'Skype', elvf ('Skype'               ));
   JSON.addItem(             'Phone', elvf ('Phone'               ));
   JSON.addItem(                'VK', elvf ('VK'                  ));
   JSON.addItem(         'DateBirth', elvf ('DateBirth'           ));
   JSON.addItem(       'DateRemoved', elvf ('DateRemoved'         ));
   JSON.addItem(             'About', elvf ('About'               ));
   JSON.addItem(           'GroupID', elsid('s_usergroupid'       ));
   
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('addUser','addUserRslt','index.php',jsonstr,c_addUser);
  break;
  
  case ('editUser'):
   showpopup(Array('popup_btn_saveandclose','popup_btn_save','popup_btn_close'));
   showloader('popupcanvas');
//   alert ('test');
   
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(          'ID', id);
   var jsonstr = JSON.make();
   ajax_post('newUser','popupcanvas','index.php',jsonstr,c_newUser);
  break;
  
  case ('deleteUser'):
   var r = confirm(gethtml('confirmrecorddelete'));
   if (r) {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(           'ID', id       );
    
    var jsonstr = JSON.make();
    ajax_post('deleteUser','deleteUserRslt','index.php',jsonstr,c_deleteUser);
   }
  break;
  
  case ('updatePassword'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem(               'ID',         elvf ('ID'         ));
   JSON.addItem(         'Password', hex_md5(elvf ('Password'  )));
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('updatePassword','addUserRslt','index.php',jsonstr,c_updatePassword);
  break;
  
  case ('sendEmail'):
   JSON.clear();                                                        // here we use JSON to send data to server
   JSON.addItem( 'ID', elvf ('ID'));
   
   showloader('result');
   var jsonstr = JSON.make();
   ajax_post('sendEmail','addUserRslt','index.php',jsonstr,c_addUser);
  break;
  
  case ('setUserStatus'):
   setUserStatus(id);
  break;
  
  case ('deleteDistrict'):
   remove("District_"+id);
  break;
  
  case ('exportToExcel'):
   showloader('exportToExcel_rslt');
   ajax_post('exportToExcel','exportToExcel_rslt','index.php','',c_exportToExcel);
  break;
  
  default:
   alert ('btn_click: '+id+', '+groupid);
  break;
 }
}

function addProjectStep(ProjectID, StepID) {
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem(  'ProjectID', ProjectID   );
 JSON.addItem(     'StepID', StepID      );
 
 if (el('DateMeasure')) {
  JSON.addItem( 'DateMeasure', elvf('DateMeasure')   );
 }
 
 if (el('DateInstall')) {
  JSON.addItem( 'DateInstall', elvf('DateInstall')   );
 }
 
 var jsonstr = JSON.make();
 showloader('steps');
 ajax_post('addProjectStep','steps','index.php',jsonstr,fillUploads);
}






function c_exportToExcel() {
 var fname = elh('exportToExcel_rslt');
 sethtml('exportToExcel_rslt','<a href="'+fname+'">Скачать файл</a>');
 document.location = (fname);
}

function c_addStatus() {
// hideloader('result');
 el('Duration_Min').value = '';
 el('Duration_Sec').value = '';
 el(     'Comment').value = '';
 el(    'NextStep').value = '';
 el(    'sAddress').value = '';
 
 resizepopup();
}








function c_newMoney() {
 resizepopup();
 startautodate();
}

function c_newProject() {
 resizepopup();
 startautodate();
 fillUploads();
 show('div_Firstname');
 
 jQuery("#PhoneMain, #PhoneAux, #PhoneHome").mask("+7(999) 999-9999");
 
 var elsi = el('fields').getElementsByTagName('input');
 if (elsi[0]) {
  elsi[0].focus();
 }
 
 
}


function newMoney() {
 setvalue('ID', '');
 setvalue('Content', '');
 setvalue('Value', '');
 
 resizepopup();
 startautodate();
}

function c_addMoney() {
 // addMoneyRslt
 sethtml('result', ajaxRet['addMoneyRslt']);
 var ret = JSON.parse(ajaxRet['addMoneyRslt']);
 sethtml('result', ret.message);
 
 resizepopup();
 if (ret.result) {
//  alert (ret.result);
  setvalue('Value','');
  if (autoclose==1) {
   hidepopup();
  }
 }
}

function c_deleteMoney() {
 forcerefresh = 1;
 showtable(0);
}









function c_newDiary() {
 
}

function newDiary() {
 el(           'ID').value = '';
 el(   'DateTarget').value = '';
 el(  'Description').value = '';
 el(    'WhatsDone').value = '';
 el( 'WhatsNotDone').value = '';
 el(         'Plan').value = '';
 el(     'HomeWork').value = '';
}

function c_addDiary() {
 // addMoneyRslt
 sethtml('result', ajaxRet['addDiaryRslt']);
 var ret = JSON.parse(ajaxRet['addDiaryRslt']);
 sethtml('result', ret.message);
 
 if (ret.result) {
//  alert (ret.result);
  setvalue('Value','');
  if (autoclose==1) {
   hidepopup();
  }
 }
}

function c_deleteDiary() {
 forcerefresh = 1;
 showtable(0);
}





function c_newUser() {
 resizepopup();
 loadUserPrivileges();
}

function newUser() {
 /*
 el(           'ID').value = '';
 el(   'DateTarget').value = '';
 el(  'Description').value = '';
 el(    'WhatsDone').value = '';
 el( 'WhatsNotDone').value = '';
 el(         'Plan').value = '';
 el(     'HomeWork').value = '';
 */
 resizepopup();
// startautodate();
}

function c_addUser() {
 sethtml('result', ajaxRet['addUserRslt']);
 var ret = JSON.parse(ajaxRet['addUserRslt']);
 sethtml('result', ret.message);
 
 if (ret.result) {
//  alert (ret.result);
  setvalue('Value','');
  if (autoclose==1) {
   hidepopup();
  }
 }
}

function c_updatePassword() {
 sethtml('result', ajaxRet['addUserRslt']);
 var ret = JSON.parse(ajaxRet['addUserRslt']);
 sethtml('result', ret.message);
}



function c_deleteUser() {
 forcerefresh = 1;
 showtable(0);
}




function c_addProject() {
 var ret = ajaxRet['addProjectRslt'];
 sethtml('result', ret);
 resizepopup();
// alert (ret);
 
// alert (lastid+"-"+elvf('ID'));
 if (lastid=='div_Finish') {
  addProjectStep(elvf('ID'), 1);
 }
 
 
 ret = JSON.parse(ret);
 sethtml('result', ret.message);
 resizepopup();
 
 if (ret.result) {
//  alert (ret.result);
  setvalue('ID',ret.ID);
  
//  alert (lastid+"-"+ret.ID);
  if (lastid=='div_Finish') {
   addProjectStep(ret.ID, 1);
  } else {
   if (lastid!='div_Finish') {
    JSON.clear();                                                        // here we use JSON to send data to server
    JSON.addItem(  'ProjectID', ret.ID   );
  //  JSON.addItem(     'StepID', StepID      );
    
    var jsonstr = JSON.make();
    ajax_post('addProjectStep','steps','index.php',jsonstr,fillUploads);
   }
  }
  
  if (autoclose==1) {
   hidepopup();
  }
  if (autoclose==2) {
   goToPage(lastid);
  }
  
 }
}




function c_addUserPrivilege() {
 //loadProjectParticipants(id, AssignedTo);
 resizepopup();
}

function loadUserPrivileges() {
 JSON.clear();                                                        // here we use JSON to send data to server
// JSON.addItem(    'ProjectID', id       );
 JSON.addItem(       'UserID', elv('ID')   );
 
 var jsonstr = JSON.make();
 setstatus('privileges', el('loading').innerHTML);
 ajax_post('loadUserPrivileges','privileges','index.php',jsonstr,c_loadUserPrivileges);
}

function c_loadUserPrivileges() {
 resizepopup();
}







function newObject() {
// el(         'ID').value = '';
// el( 'DateTarget').value = '';
// el(   'NumRooms').value = '';
// el(    'Address').value = '';
// el(      'Phone').value = '';
// el(     'Status').value = '';
// alert ('test');
 resizepopup();
 startautodate();
 fillnewbuildingssubitems();  // loadnewbuildingssubitems, loadNBSubitems
 lastmarketid = elsid('MarketID');
}

function c_newObject() {
 resizepopup();
 startautodate();
 fillnewbuildingssubitems();  // loadnewbuildingssubitems, loadNBSubitems
 lastmarketid = elsid('MarketID');
}

function c_addObject() {
 sethtml('result', ajaxRet['addObjectRslt']);
 var ret = JSON.parse(ajaxRet['addObjectRslt']);
 sethtml('result', ret.message);
 if (ret.canproceed) {
//  alert (ajaxRet['addObjectRslt']);
//  setvalue( 'Value', ''        );
  setvalue(    'ID', ret.ID    );
//  setvalue( 'SubID', ret.SubID );
//  addSubObject();
  if (autoclose==1) {
   hidepopup();
  }
 }
}


function addSubObject() {
// alert (elsid('MarketID'));
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem(          'MarketID', elsid('MarketID'          ));
 JSON.addItem(          'ParentID', elvf ('ID'                ));
 JSON.addItem(                'ID', elvf ('SubID'             ));
 
 if (elsid('MarketID')==1) {
  JSON.addItem(              'Floor', elvf ('Floor'               ));
  JSON.addItem(             'Floors', elvf ('Floors'              ));
  JSON.addItem(               'Year', elvf ('Year'                ));
  JSON.addItem(        'HouseTypeID', elsid('HouseTypeID'         ));
  JSON.addItem(    'WallsMaterialID', elsid('WallsMaterialID'     ));
  JSON.addItem(  'OverlappingTypeID', elsid('OverlappingTypeID'   ));
  
  JSON.addItem(           'Security', fcbv ('Security'            ));
  JSON.addItem(          'Concierge', fcbv ('Concierge'           ));
  JSON.addItem(   'OperationService', fcbv ('OperationService'    ));
  JSON.addItem(              'Chute', fcbv ('Chute'               ));
  JSON.addItem(                'Gas', fcbv ('Gas'                 ));
  JSON.addItem(            'Parking', fcbv ('Parking'             ));
  
  JSON.addItem(         'RoomsTotal', elvf ('RoomsTotal'          ));
  JSON.addItem(         'SpaceTotal', elvf ('SpaceTotal'          ));
  JSON.addItem(       'SpaceKitchen', elvf ('SpaceKitchen'        ));
  JSON.addItem(      'RoomsIsolated', elvf ('RoomsIsolated'       ));
  JSON.addItem(        'SpaceLiving', elvf ('SpaceLiving'         ));
  JSON.addItem(          'Balconies', elvf ('Balconies'           ));
  
  JSON.addItem(       'LayoutTypeID', elsid('LayoutTypeID'        ));
  JSON.addItem(       'ToiletTypeID', elsid('ToiletTypeID'        ));
  JSON.addItem(        'ConditionID', elsid('ConditionID'         ));
  JSON.addItem(           'SourceID', elsid('SourceID'            ));
 } else {
  JSON.addItem(             'Floors', elvf ('Floors'              ));
  JSON.addItem(          'Entrances', elvf ('Entrances'           ));
  JSON.addItem(         'Apartments', elvf ('Apartments'          ));
  JSON.addItem(          'Elevators', elvf ('Elevators'           ));
  JSON.addItem('CompletionDateQuarter', elsid('CompletionDateQuarter'));
  JSON.addItem(   'CompletionDateYear', elsid('CompletionDateYear'   ));
  
  JSON.addItem(           'Security', fcbv ('Security'            ));
  JSON.addItem(          'Concierge', fcbv ('Concierge'           ));
  JSON.addItem(   'OperationService', fcbv ('OperationService'    ));
  JSON.addItem(              'Chute', fcbv ('Chute'               ));
  JSON.addItem(                'Gas', fcbv ('Gas'                 ));
  JSON.addItem(            'Parking', fcbv ('Parking'             ));
  
  JSON.addItem(        'FinishingID', elsid('FinishingID'         ));
  JSON.addItem(     'FloorSurfaceID', elsid('FloorSurfaceID'      ));
  JSON.addItem(        'StoveTypeID', elsid('StoveTypeID'         ));
  JSON.addItem(        'DoorsTypeID', elsid('DoorsTypeID'         ));
  JSON.addItem(     'WallsSurfaceID', elsid('WallsSurfaceID'      ));
  JSON.addItem(    'WallsMaterialID', elsid('WallsMaterialID'     ));
  JSON.addItem('BathroomEquipmentID', elsid('BathroomEquipmentID' ));
  JSON.addItem(      'WindowsTypeID', elsid('WindowsTypeID'       ));
  JSON.addItem(     'RightsSourceID', elsid('RightsSourceID'      ));
  JSON.addItem(  'RightsTransmissionID', elsid('RightsTransmissionID'   ));
  JSON.addItem('ResidentialComplexName', elvf ('ResidentialComplexName' ));
  JSON.addItem(             'Developer', elvf ('Developer'              ));
 }
 
 showloader('result');
 var jsonstr = JSON.make();
// alert (jsonstr);
 ajax_post('addSubObject','addSubObjectRslt','index.php',jsonstr,c_addSubObject);
}

function c_addSubObject() {
 sethtml('result', ajaxRet['addSubObjectRslt']);
 var ret = JSON.parse(ajaxRet['addSubObjectRslt']);
 sethtml('result', ret.message);
 if (ret.result) {
//  alert (ajaxRet['addObjectRslt']);
//  setvalue( 'Value', ''        );
//  setvalue(    'ID', ret.ID    );
  setvalue( 'SubID', ret.ID );
  if (autoclose==1) {
   hidepopup();
  }
 }
}

function c_deleteObject() {
 forcerefresh = 1;
 showtable(0);
}







function newCustomer() {
// alert (ajaxRet['popupcanvas_']);
 
 resizepopup();
 startautodate();
}

function c_newCustomer() {
 resizepopup();
 startautodate();
}

function c_addCustomer() {
 sethtml('result', ajaxRet['addCustomerRslt']);
 var ret = JSON.parse(ajaxRet['addCustomerRslt']);
 sethtml('result', ret.message);
// alert (ret.ID);
 
 if (ret.canproceed) {
//  alert (ajaxRet['addObjectRslt']);
//  setvalue( 'Value', ''        );
  setvalue(    'ID', ret.ID    );
//  setvalue( 'SubID', ret.SubID );
  if (autoclose==1) {
   hidepopup();
  }
 }
}

function c_deleteCustomer() {
 forcerefresh = 1;
 showtable(0);
}










function c_addParticipant() {
 //loadProjectParticipants(id, AssignedTo);
 
}

function loadProjectParticipants(id, UserID) {
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem(    'ProjectID', id       );
 JSON.addItem(       'UserID', UserID   );
 
 var jsonstr = JSON.make();
 setstatus('participants', el('loading').innerHTML);
 ajax_post('loadProjectParticipants','participants','index.php',jsonstr,c_loadProjectParticipants);
}

function c_loadProjectParticipants() {
 
}

function c_addTask() {
// alert (ajaxRet['addTaskRslt']);
// alert (el('debug').innerHTML);
 
 hidepopup();
// showtable(0);
}

function newTask() {
 el('ID').value = '';
 el('Title').value = '';
 el('Labor').value = '1.0';
 el('DateDue').value = today_();
 el('Description').value = '';
 frb_setvalueById('r_priorityid',3);
 
 var si = getSelectedId('flt_AssignedTo');
 if (si>-1) {
  setactiveoptionid('AssignedTo', si);
 }
 
 var si = getSelectedId('flt_ProjectID');
 if (si>-1) {
  setactiveoptionid('ProjectID', si);
 }
 
 var si = getSelectedId('flt_TaskListID');
 if (si>-1) {
  setactiveoptionid('TaskListID', si);
 } else {
  setactiveoptionid('TaskListID', 2);
 }
}

function newProject() {
 alert ('test');
}












function c_setListID() {
// alert (ajaxRet['buf']);
 if (autoclose==1) hidepopup();
}

function frb_getvalue (groupid) {
 var ret=0;
 jQuery("#"+groupid+" .frb_checked").each(
  function(){
   ret=substr(this.id,strlen(groupid)+2,0);
  }
 );
 return ret;
}

function frb_setvalueByTitle (groupid, title) {
 var ret=0;
 jQuery("#"+groupid+" a").each(
  function(){
//   alert (this.id);
//   alert (el('c'+this.id).innerHTML);
   el('p'+this.id).className=(el('c'+this.id).innerHTML==title)?'frb_checked':'frb';
//   ret=substr(this.id,strlen(groupid)+2,0);
  }
 );
 return ret;
}


function frb_setvalueById (groupid, id) {
 var ret=0;
 jQuery("#"+groupid+" a").each(
  function(){
//   alert (this.id);
//   alert (el('c'+this.id).innerHTML);
   el('p'+this.id).className=(this.id==groupid+"_"+id)?'frb_checked':'frb';
//   ret=substr(this.id,strlen(groupid)+2,0);
  }
 );
 return ret;
}





















function addevent() {
// alert ('test');
// alert (el('event_template').innerHTML);
}

function c_postevent() {
// el('debug').innerHTML = ajaxRet['buf'];
}

function c_getnewevents() {
// el('debug').innerHTML = ajaxRet['buf'];
 
 var doautoscroll = 0;
 var docheight = getDocumentHeight();
 
 //el('debug').innerHTML = f_scrollTop() + " - " + getDocumentHeight() + " - " + getViewportHeight();
 if ((f_scrollTop()+getViewportHeight())==docheight) {
//  alert ('autoscroll');
  doautoscroll=1;
 }
 
 JSON.clear();
 var ret = JSON.parse(ajaxRet['buf']);
 var result="";
 
// for (item in ret) {
//  result+=ret[item].ID+"<br>";
// }
 
 var template = "";
 var html = "";
 
 for (var i=0; i<ret.length; i++) {
//  result+=ret[i].Content+"<br>";
  var template = el('event_template').innerHTML;
  
  template=template.replace("%block_img%" , ""              );
  template=template.replace("%usertime%"  , ret[i].UserTime );
  template=template.replace("%content%"   , ret[i].Content  );
  template=template.replace("%evt_id%"    , ret[i].ID       );
  
  html+=template;
 }
 
// el(insertpoint_id).insertAfter('added text!');
 
// el('debug').innerHTML = insertpoint_id;
// el('debug').innerHTML = 'data_ready: ' + ajaxRet['buf'] + "<br><br>" + ret[1]['Content'];
// el('debug').innerHTML = 'data_ready: ' + ajaxRet['buf']=='[]';
// if (el('insertpoint').innerHTML!=html) {
 
 if (ajaxRet['buf']!='[]') {
  var container = document.createElement('div'); 
  container.innerHTML = html;
  el('event_viewer').appendChild(container);
//  alert (ret.length);
  if (doautoscroll) {
   window.scrollTo(0, docheight);
  }
 }
 
}

function keypress(e) {
 switch (e.keyCode) {
  case (10):                             // ENTER key
  case (13):                             // ENTER key
   if (e.ctrlKey) {
    btn_click('post','report');
   }
  break;
  default:
//   alert (e.ctrlKey);
//   alert (e.keyCode);
  break;
 }
}

function time() {
 var zerom  = '';
 var zeros  = '';
 var zeromo = '';
 var zerod  = '';
 
 var today = new Date();
 var date_   = today.getDate();
 var month_  = today.getMonth()+1;
 var year_   = today.getFullYear();
 var hours_  = today.getHours();
 var min_    = today.getMinutes();
 var sec_    = today.getSeconds();
 var zerom = zeros = '';
 if(min_   < 10) zerom  = '0';
 if(sec_   < 10) zeros  = '0';
 if(month_ < 10) zeromo = '0';
 if(date_  < 10) zerod  = '0';
 return (year_+'-'+zeromo+month_+'-'+zerod+date_+' '+hours_+':'+zerom+min_+':'+zeros+sec_);
}

function today_() {
 var zeromo = '';
 var zerod  = '';
 
 var today = new Date();
 var date_   = today.getDate();
 var month_  = today.getMonth()+1;
 var year_   = today.getFullYear();
 if(month_ < 10) zeromo = '0';
 if(date_  < 10) zerod  = '0';
 return (year_+'-'+zeromo+month_+'-'+zerod+date_);
}













function getIframeDocument(iframeNode) {
 if (iframeNode.contentDocument) return iframeNode.contentDocument
 if (iframeNode.contentWindow) return iframeNode.contentWindow.document
 return iframeNode.document
}

function setstatus (id, text) {
 if (el(id)) el(id).innerHTML=text;
}

function doupload() {
 showloader('status0');
 uploadform.submit();
}

function uploaded(id) {
// setstatus('status0','Ready');
 var doc=getIframeDocument(el(id));
 if (doc.getElementsByTagName("body")[0].innerHTML) {
  setstatus('status0', doc.getElementsByTagName("body")[0].innerHTML);
  resizepopup();
 }
}

function deleteAttachment(id, action) {
 switch (action) {
  case ('steps'):
   dest_id = 'status0';
  break;
 }
 
// alert (dest_id);
 
 JSON.clear();                                                           // here we use JSON to send data to server
 JSON.addItem(     'id', id     );
 JSON.addItem( 'action', action );
 
 var jsonstr = JSON.make();
//  alert (jsonstr);
 
 ajax_post('deleteAttachment',dest_id,'index.php',jsonstr,resizepopup);
}








function showtable(isexport) {      // gettable
// alert (forcerefresh);
 if (forcerefresh==1) {
  buf_jsonstr0 = '';
  buf_jsonstr1 = '';
  buf_jsonstr2 = '';
  buf_jsonstr3 = '';
  buf_jsonstr  = '';
  forcerefresh=0;
 }
 
 showloader('tblstatus');
 
// if ((go!='dashboard')) {
  JSON.clear();                                          // here we use JSON to send data to server
//  JSON.addItem('tablename' , go          );
  JSON.addItem('tablemode' , tablemode   );
  JSON.addItem( 'isexport' , isexport    );
  JSON.addItem(       'go' , go          );
  
  if ((jQuery('div#leftfilters input[type=checkbox].filteritem#offertype0').attr('checked')==undefined) && (jQuery('input[type=checkbox].filteritem#offertype1').attr('checked')==undefined)) {
   jQuery('div#leftfilters input[type=checkbox].filteritem#offertype0').attr('checked','checked');
  }
  if ((jQuery('div#leftfilters input[type=checkbox].filteritem#offertype2').attr('checked')==undefined) && (jQuery('input[type=checkbox].filteritem#offertype3').attr('checked')==undefined)) {
   jQuery('div#leftfilters input[type=checkbox].filteritem#offertype2').attr('checked','checked');
  }
  
  jQuery('#leftfilters div.selectbutton0, div.selectbutton1').each(
   function(){
    JSON.addItem(this.id,            jQuery(this).attr('class')=='selectbutton1');
   }
  )
  
  jQuery('#leftfilters input:checkbox.filteritem, #leftfilters_common input:checkbox.filteritem').each(
   function(){
    JSON.addItem(this.id,            this.checked       );
   }
  );
  jQuery('#leftfilters input:text.filteritem, #leftfilters_common input:text.filteritem').each(
   function(){
    JSON.addItem(this.id,            this.value         );
   }
  );
  jQuery('#leftfilters select.filteritem, #leftfilters_common select.filteritem').each(
   function(){
    if (this.options[this.selectedIndex]) {
     JSON.addItem(this.id,            this.options[this.selectedIndex].id );
    }
   }
  );
  
  jQuery('#leftfilters div.frb_checked').each(
   function(){
    JSON.addItem(
     substr(this.id,1,strrpos(this.id,'_')-1),
     substr(this.id,strrpos(this.id,'_')+1)
    )
    
//    alert ("c"+substr(this.id,1));
    JSON.addItem(
     substr(this.id,1,strrpos(this.id,'_')-1)+"_caption",
     elh("c"+substr(this.id,1))
    )
    
//    if (this.options[this.selectedIndex]) {
//     JSON.addItem(this.id,            this.options[this.selectedIndex].id );
//    }
   }
  );
  
  jQuery('#leftfilters div.fcb_checked').each(
   function(){
    JSON.addItem(substr(this.id,1), 1);
   }
  );
  
  
  switch (go) {
   case ('tasks'):
    JSON.addItem(          'listid' , '0'          );
    jsonstr0 = JSON.make();
    
    JSON.updateItem(       'listid' , '1'          );
    jsonstr1 = JSON.make();
    
    JSON.updateItem(       'listid' , '2'          );
    jsonstr2 = JSON.make();
    
    JSON.updateItem(       'listid' , '3'          );
    jsonstr3 = JSON.make();
//    alert (jsonstr0);
//    alert (lasttasklist);
    
    setstatus('tblstatus',el('loading').innerHTML);
    showtasks();
    
   break;
   default:
    var jsonstr = JSON.make();
    if (buf_jsonstr!=jsonstr) {
     buf_jsonstr = jsonstr;
     setstatus('tblstatus',el('loading').innerHTML);
     ajax_post('showtable','xlspreview','index.php',jsonstr,c_showtable);
    }
   break;
  }
// }
}

function showtasks() {
// if (buf_jsonstr0!=jsonstr0 || buf_jsonstr1!=jsonstr1 || buf_jsonstr2!=jsonstr2 || buf_jsonstr3!=jsonstr3) {
//  lasttasklist = 0;
// }
 
 var cnt = 0;
 tasklistcount=0;
 if (buf_jsonstr0!=jsonstr0) {
  sethtml('tasks0_cnt', cnt);
  tasklistcount++;
  copyhtml('tasks0','popupdefault_tbl');
  ajax_post('showtasks','tasks0','index.php',jsonstr0,c_showtable);
  buf_jsonstr0=jsonstr0;
 }
 if (buf_jsonstr1!=jsonstr1) {
  sethtml('tasks1_cnt', cnt);
  tasklistcount++;
  copyhtml('tasks1','popupdefault_tbl');
  ajax_post('showtasks','tasks1','index.php',jsonstr1,c_showtable);
  buf_jsonstr1=jsonstr1;
 }
 if (buf_jsonstr2!=jsonstr2) {
  sethtml('tasks2_cnt', cnt);
  tasklistcount++;
  copyhtml('tasks2','popupdefault_tbl');
  ajax_post('showtasks','tasks2','index.php',jsonstr2,c_showtable);
  buf_jsonstr2=jsonstr2;
 }
 if (buf_jsonstr3!=jsonstr3) {
  sethtml('tasks3_cnt', cnt);
  tasklistcount++;
  copyhtml('tasks3','popupdefault_tbl');
  ajax_post('showtasks','tasks3','index.php',jsonstr3,c_showtable);
  buf_jsonstr3=jsonstr3;
 }
 
}

function c_showtable() {
 tasklistcount--;
 if ((go=='tasks')) {
  for (var n=0; n<4; n++) {
   var ele = el('tasks'+(n));
   var ch  = ele.getElementsByClassName ('taskborder');
   var cnt = ch.length;
   sethtml('tasks'+(n)+'_cnt', cnt);
  }
 }
 
 if (((go=='tasks') && (tasklistcount==0)) || (go=='money') || (go=='dashboard')) {
  var e = el('xlspreview');
  if (e) {
   if (e.innerHTML) {
   } else {
    setstatus('fileinfo0',el('error404').innerHTML);
   }
  }
 }
 
 if (go=='money') {
  getBalance('tblstatus');
 } else {
  var ele = el('xlspreview');
  var ch  = ele.getElementsByClassName ('row');
  var cnt = ch.length-1;
  if (cnt<0) cnt = 0;
//  sethtml('tasks'+(n)+'_cnt', cnt);
  
  setstatus('tblstatus',el('count').innerHTML + cnt);
 }
 
 switch (go) {
  case ('dashboard'):
   updateGauges();
  break;
  case ('tasks'):
   if ((id>0) && (tasklistcount==0)) {
//    alert (id);
    btn_click(id, 'editTask');
   }
  break;
 }
}


function getBalance(id) {
// JSON.clear();                                                        // here we use JSON to send data to server
// JSON.addItem('id', id);
// var jsonstr = JSON.make();
 var jsonstr = "";
 
 ajax_post('getBalance',id,'index.php',jsonstr);
}





function updateGauges() {
 var g1, g2, g3, g4;
 
 if (el('gauge1')) {
  g1 = new JustGage(
   {
    id: "gauge1", 
    value: parseInt(elh('gauge1_v')), 
    min  : 0,
    max  : parseInt(elh('gauge1_max')),
    title:          elh('gauge1_title'),
    label: " из " + elh('gauge1_max'),
    levelColors: [
     "#FF0000",
     "#FFFF00",
     "#00FF00"
    ]
   }
  );
 }
 
 /*
 var g2 = new JustGage(
  {
   id: "g2", 
   value: getRandomInt(0, 100), 
   min: 0,
   max: 100,
   title: "Small Buddy",
   label: "oz"
  }
 );
 
 var g3 = new JustGage(
  {
   id: "g3", 
   value: getRandomInt(0, 100), 
   min: 0,
   max: 100,
   title: "Tiny Lad",
   label: "oz"
  }
 );
 
 var g4 = new JustGage(
  {
   id: "g4", 
   value: getRandomInt(0, 100), 
   min: 0,
   max: 100,
   title: "Little Pal",
   label: "oz"
  }
 );
 */
 
// g1.refresh();
 
 
 
 
 
}









function elv(id) {             // returns element value
 var e = el(id);
 if (e) {
  return e.value;
 } else {
  return '';
 }
}

function elh(id) {             // returns element value
 var e = el(id);
 if (e) {
  return e.innerHTML;
 } else {
  return '';
 }
}

function elvf(id) {            // returns element value filtered
 return msgfilter (elv(id));
}

function elvfc(id) {            // returns element value filtered
 return msgfilter (cinput_value(id));
}

function elsid(id) {           // returns element selected index (helper)
 return getSelectedId(id);
}

function elsh(id) {           // returns element selected index (helper)
 return getSelectedHtml(id);
}

function elvrid(id) {           // returns element selected index (helper)
 var els = el(id).getElementsByTagName('input');
 
 for (var i=0; i<els.length; i++) {
  if (els[i].checked) {
   return els[i].id;
  }
 }
 
// return getSelectedId(id);
}

function elc(id) {
 var e = el(id);
 if (e) {
  return e.checked;
 } else {
  return false;
 }
}

function elci(id) {
 var e = el(id);
 if (e) {
  return (e.checked)?1:0;
 } else {
  return 0;
 }
}

function fcbv(id) {
 return fcb_getvalue(id);
}

function fcbso(id) {
 var buf = "";
 
 var els = el(id).getElementsByClassName('fcb_checked');
 
 for (var i=0; i<els.length; i++) {
  buf += els[i].id+";";
  
 }
 
 
 
 return buf;
}





function getSelectedId(id) {
 ele = el(id);
 if (ele) {
  if (ele.options[ele.selectedIndex]) {
   return ele.options[ele.selectedIndex].id;
  }
 }
}

function getSelectedHtml(id) {
 ele = el(id);
 if (ele) {
  if (ele.options[ele.selectedIndex]) {
   return ele.options[ele.selectedIndex].innerHTML;
  }
 }
}

function setSelectedIdByTitle(id, title) {
 ele = el(id);
 if (ele) {
  for (var i=0; i<ele.options.length; i++) {
   if (ele.options[i].innerHTML == title) {
    ele.selectedIndex = i;
    return i;
   }
  }
 }
}

function setSelectedId(id, value) {
 ele = el(id);
 if (ele) {
  for (var i=0; i<ele.options.length; i++) {
   if (ele.options[i].id == value) {
    ele.selectedIndex = i;
    return i;
   }
  }
 }
}

function eloc(id) {
 ele = el(id);
 if (ele) {
  return ele.options.length;
 } else {
  return -1;
 }
}


function updaterubrics() {
 si = el('headerid').options[el('headerid').selectedIndex].id;
 if (buf_si!=si) {
  buf_si=si;
//  alert (si);
  JSON.clear();                                          // here we use JSON to send data to server
  JSON.addItem('HeaderID',  si );
  var jsonstr = JSON.make();
  ajax_post('getrubrics', 'rubricid', 'index.php', jsonstr, c_updaterubrics);
//  showtable(0);
 }
}

function c_updaterubrics() {
// alert (el('rubricid').innerHTML);
}

function checkboxclick(id) {
 //alert (el("checkbox_"+id).className);
 if (id==-1) {
  if (jQuery(".cf_checkbox").length!=0) {
   jQuery(".cf_checkbox").each(
    function(){
     this.className="cf_checkboxchecked";
    }
   );
  } else {
   jQuery(".cf_checkboxchecked").each(
    function() {
     this.className="cf_checkbox";
    }
   )
  }
 } else {
  el("checkbox_"+id).className=(el("checkbox_"+id).className=="cf_checkboxchecked")?"cf_checkbox":"cf_checkboxchecked";
 }
}


function generateresult() {
 ajax_post('generateresult','buf','index.php','',c_generateresult);
}
function c_generateresult() {
// el('resultinfo').innerHTML = ajaxRet['buf'];
 document.location = ajaxRet['buf'];
}

function showHint(id) {
 var data = el('hint_'+id).innerHTML;
 
 if (el('hint_'+id).className=='hint') {
  var hint = el('hint_template').innerHTML;
  
  hint = hint.replace('%hint_id%'      , id);
  hint = hint.replace('%hint_content%' , data);
  
  el('hint_'+id).innerHTML = hint;
  el('hint_'+id).className ='hint_filled';
 }
 
 supertoggle ('hint_'+id);
}

function hideHint(id) {
 hideEx ('hint_'+id);
}

function cleardatabase() {
 ajax_post('cleardatabase','fileinfo0','index.php','',c_cleardatabase);
}
function c_cleardatabase() {
 setstatus('tblstatus',el('cleardatabase').innerHTML);
 showtable();
}


function clearColumn(id) {
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem('id', id);
 
 var jsonstr = JSON.make();
 
 ajax_post('clearColumn','buf','index.php',jsonstr,c_clearColumn);
}
function c_clearColumn() {
// setstatus('tblstatus',ajaxRet['buf']);
 showtable();
}












function tableitemclick(id,newtablename) {
 newitem = 0;
 tabid   = 0;
 tablename = newtablename;
// alert (id);
// alert (tablename);
 
// alert (el(tablename+'_'+id).innerHTML);
 
// alert (el(id+'_TypeID').innerHTML);
 
 setvaluehtml(         'id', id+'_ID'       );
 setvaluehtml( 'txtcontent', id+'_Content'  );
 setvaluehtml(  'dateadded', id+'_DateAdded' );
 setvaluehtml(      'value', id+'_Value'    );
 
 
 
 setactiveoptionid ('typeid', el(id+'_TypeID').innerHTML);
 setactiveoptionid ('typeid', el(id+'_TypeID').innerHTML);
 
// tabidstr = 'objectdetails_primaryinfo';
// showobjectinfo(id);
}


function showbuttons(btns) {
 jQuery('.popupbottombuttons input[type=button], .popupbottombuttons td.popup_option').each(
  function(){
   if (jQuery.inArray(this.id,btns)>-1) {
    showi (this.id);
   } else {
    hide (this.id);
   }
  }
 );
}



function cinput_keyup(id, e) {
// alert (id);
 
 cmd = substr(id, 0, strpos(id,'_'));
// alert (cmd);
 
 
// alert ("cinput_keyup: "+id);
 id  = substr(id, strpos(id,'_')+1, 0);
// alert ("cinput_keyup: "+id);
 
 switch (cmd) {
  case ('CommentToTask'):
   if (((e.keyCode==13) || (e.keyCode==10)) && e.ctrlKey) {
    addCommentToTask(id);
   }
  break;
  case ('DateAdded'):
  case ('DateTarget'):
  case ('DateTime'):
   stopautodate();
  break;
  
  case ('Firstname'):
  case ('Surname'):
  case ('Middlename'):
  case ('City'):
  case ('Street'):
  case ('House'):
  case ('Building'):
  case ('Flat'):
  case ('Comment'):
  case ('Email'):
  case ('PhoneMain'):
  case ('PhoneAux'):
  case ('PhoneHome'):
  case ('OrderNumber'):
  case ('Cost'):
  case ('CostPrepay'):
  case ('DateFinish'):
  case ('DateMeasure'):
  case ('DateInstall'):
  case ('ShortTitle'):
  case ('FolderName'):
   if ((e.keyCode==13) || (e.keyCode==10)) {
//    alert (1);
    var els = el('fields').getElementsByClassName('field');
//    alert ('len: '+els.length);
    for (var i=0; i<els.length; i++) {
//     alert (els[i].id+"-"+is_visible(els[i].id));
     
     if (is_visibleo(els[i])) {
//      var thisid  = substr(els[i].id, strpos(els[i].id,'_')+1, 0);
      
      
//      var elsi = els[i].getElementsByTagName('input');
   //   for (var j=0; j<elsi.length; j++) {
//      if (elsi[0]) {
   //    alert (elsi[0].id);
//       elsi[0].focus();
//      }
      var els = els[i].getElementsByClassName('next');
      els[0].onclick();
//      alert ('test');
      
      break;
     }
    }
    
    
//    var els = el('div_'+cmd).getElementsByClassName('next');
    
   //   alert (id);
    
//    for (var i=0; i<els.length; i++) {
//     els[i].onclick();
//    }
    
    
    
    
//    div_Firstname
//    comctl
    
    
   }
  break;
 }
}

function cal_load() {
 var myCalendar;
 var cals = new Array();
 
 var els = document.getElementsByClassName("calendar");
 for (var i=0; i<els.length; i++) {
  cals.push(els[i].id);
//  alert (els[i].id);
 }
 
 myCalendar = new dhtmlXCalendarObject(cals);
 
}


function addCommentToTask(id) {
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem('Comment', msgfilter(el('CommentToTask_'+id).value));
 JSON.addItem('ParentTableName', 'tasks');
 JSON.addItem('ParentID', id);
 lastid = id;
 
 var jsonstr = JSON.make();
 
 ajax_post('addComment','buf','index.php',jsonstr,c_addCommentToTask);
}

function c_addCommentToTask() {
 el('CommentToTask_'+lastid).value="";
 getComments('tasks', lastid);
 
}

function getComments(parenttablename, id) {
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem('ParentTableName', parenttablename);
 JSON.addItem('ParentID', id);
 
 var jsonstr = JSON.make();
// alert (dest_id);
 
// ajax_post('getComments','addTaskRslt','index.php',jsonstr,c_getComments);
 copyhtml('co_'+id,'popupdefault_tbl');
 ajax_post('getComments',dest_id,'index.php',jsonstr,c_getComments);
}

function c_getComments() {
 
}

function deleteComment(id, parentid) {
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem('ID', id);
 
 var jsonstr = JSON.make();
 
// alert (id);
// alert (parentid);
 
// id = 
// ajax_post('getComments','addTaskRslt','index.php',jsonstr,c_getComments);
 ajax_post('deleteComment',dest_id,'index.php',jsonstr,c_deleteComment);
}

function c_deleteComment() {
// sethtml ('addTaskRslt', ajaxRet['buf']);
 
}


function popupBtnClick(btnid) {
 autoclose = 1;
 switch (go) {
  case ('tasks'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addTask');
    break;
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addTask');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('projects'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addProject');
    break;   
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addProject');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('money'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addMoney');
    break;
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addMoney');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('diary'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addDiary');
    break;
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addDiary');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('objects'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addObject');
    break;
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addObject');
    break;
    case ('popup_btn_addStatus'):
     btn_click(-1,'addStatus');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('customers'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addCustomer');
    break;
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addCustomer');
    break;
    case ('popup_btn_addStatus'):
     btn_click(-1,'addStatus');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('users'):
   switch (btnid) {
    case ('popup_btn_save'):
     autoclose = 0;
     btn_click(-1,'addUser');
    break;
    case ('popup_btn_saveandclose'):
     autoclose = 1;
     btn_click(-1,'addUser');
    break;
    case ('popup_btn_close'):
     hidepopup();
     id = 0;
    break;
   }
  break;
  case ('happiness'):
  case ('dashboard'):
   switch (lasthappinessgo) {
    case ('objects'):
     switch (btnid) {
      case ('popup_btn_save'):
       autoclose = 0;
       btn_click(-1,'addObject');
      break;
      case ('popup_btn_saveandclose'):
       autoclose = 1;
       btn_click(-1,'addObject');
      break;
      case ('popup_btn_addStatus'):
       btn_click(-1,'addStatus');
      break;
      case ('popup_btn_close'):
       hidepopup();
       id = 0;
      break;
     }
    break;
    case ('customers'):
     switch (btnid) {
      case ('popup_btn_save'):
       autoclose = 0;
       btn_click(-1,'addCustomer');
      break;
      case ('popup_btn_saveandclose'):
       autoclose = 1;
       btn_click(-1,'addCustomer');
      break;
      case ('popup_btn_addStatus'):
       btn_click(-1,'addStatus');
      break;
      case ('popup_btn_close'):
       hidepopup();
       id = 0;
      break;
     }
    break;
   }
  break;
  default:
   alert('popupBtnClick - Unknown go: '+go);
  break;
 }
}

function cb_click(id,groupid) {
 var ids='';
 var thisid='';
 
 switch (groupid) {
  case ('CompactView'):
   if (el('tasks').className == 'fullwidth') {
    el('tasks').className = 'fullwidth_compact';
   } else {
    el('tasks').className = 'fullwidth';
   }
  break;
  default:
   alert ('cb_click: '+id+', '+groupid+', '+strlen(groupid));
  break;
 }
}

function removeParticipant(id) {
 alert ('removeParticipant: '+id);
}


function selectChange(id) {
 if (elsid(id)) {
  var value = elsid(id);
 } else {
  var value = elsh(id);
  setvalue(id+"_text", value);
 }
 
 JSON.clear();                                                        // here we use JSON to send data to server
 JSON.addItem(    'id', id      );
 JSON.addItem( 'value', value   );
 
 var jsonstr = JSON.make();
 if (lastselectChangejson[id] != jsonstr) {
  lastselectChangejson[id] = jsonstr;
 // alert (dest_id);
  
 // ajax_post('getComments','addTaskRslt','index.php',jsonstr,c_getComments);
  showloader('debug');
  ajax_post('selectChange','buf','index.php',jsonstr,c_selectChange);
 }
}

function c_selectChange() {
 sethtml('debug', ajaxRet['buf']);
 var ret = JSON.parse(ajaxRet['buf']);
 sethtml('debug', ret.debug);
 
 if (ret.GroupID) {
//  alert (ret.GroupID);
  var buf = elsid('GroupID');
  sethtml('GroupID', ret.GroupID);
  setSelectedId('GroupID', buf)
 }
 if (ret.PlaceName) {
  var buf = elsh('PlaceName');
  sethtml('PlaceName', ret.PlaceName);
  setSelectedIdByTitle('PlaceName', buf);
 }
 if (ret.PlaceType) {
  var buf = elsh('PlaceType');
  sethtml('PlaceType', ret.PlaceType);
  setSelectedIdByTitle('PlaceType', buf);
 }
 if (ret.SourceID) {
  var buf = elsh('SourceID');
  sethtml('SourceID', ret.SourceID);
  setSelectedIdByTitle('SourceID', buf);
 }
 if (ret.CustomerSubtypeID) {
  var buf = elsh('CustomerSubtypeID');
  sethtml('CustomerSubtypeID', ret.CustomerSubtypeID);
  setSelectedIdByTitle('CustomerSubtypeID', buf);
 }
 
}


function showloader(id) {
 copyhtml(id,'loading');
}

function hideloader(id) {
 sethtml(id,'');
}

function startautodate() {
// alert (ad);
 if (el('DateAdded') && !ad_a && (elv('DateAdded')=='')) {
  ad_a=setInterval("autodate('DateAdded')",1000);
 }
 if (el('DateTime') && !ad_t && (elv('DateTime')=='')) {
  ad_t=setInterval("autodate('DateTime')",1000);
 }
 if (el('DateTarget') && !ad_ta && (elv('DateTarget')=='')) {
  ad_ta=setInterval("autodate('DateTarget')",1000);
 }
 
}

function stopautodate() {
 if (el('DateAdded')) {
  clearInterval(ad_a);
  ad_a=0;
 }
 if (el('DateTime')) {
  clearInterval(ad_t);
  ad_t=0;
 }
 if (el('DateTarget')) {
  clearInterval(ad_ta);
  ad_ta=0;
 }
 
}

function autodate(id) {
 setvalue(id, time());
// alert (time());
}







function fillSelect(id) {
 var dest = "addr"+(id+1);
 if (lastelsid != elsid('DistrictID')) {
  lastelsid = elsid('DistrictID');
  if (lastelsid=='-1') {
   sethtml(dest,select_defaults[dest]);
  } else {
   lastid = id;
   copyhtml("loader_addr"+id, "loadingsmall");
   
   JSON.clear();                                          // here we use JSON to send data to server
   JSON.addItem(    'id', id                );
   JSON.addItem( 'elsid', lastelsid         );
   var jsonstr = JSON.make();
   
   ajax_post('fillSelect',dest,'index.php',jsonstr,c_fillSelect);
  }
 }
}

function c_fillSelect() {
 var dest = "addr"+(lastid+1);
 
// copyhtml("debug", dest);
 
 sethtml("loader_addr"+lastid, "");
// sethtml("loader_addr"+lastid, elh("addr"+(lastid+1)));
 
 var els = el(dest).getElementsByTagName('option');
 
 if (els.length==2) {
  setSelectedIndex(dest,1);
//  if (lastid<2) fillSelect(lastid+1);
 }
 
}


function setUserStatus(id) {
 JSON.clear();                                          // here we use JSON to send data to server
 JSON.addItem( 'ID', id );
 var jsonstr = JSON.make();
 
 showloader('userStatus');
 ajax_post('setUserStatus','userStatus','index.php',jsonstr);
}



function loadObjectDetails() {
 if (lastmarketid != elsid('MarketID')) {
  lastmarketid = elsid('MarketID');
  JSON.clear();                                                        // here we use JSON to send data to server
  JSON.addItem(           'ID', elvf ('ID'              ));
  JSON.addItem(     'MarketID', lastmarketid             );
  
  showloader('objectdetails');
  var jsonstr = JSON.make();
  ajax_post('loadObjectDetails','objectdetails','index.php',jsonstr,c_loadObjectDetails);
 }
}

function c_loadObjectDetails() {
 fillnewbuildingssubitems();  // loadnewbuildingssubitems, loadNBSubitems
}

function fillnewbuildingssubitems() {
 if (el('newbuildings_subitems_container')) {
  JSON.clear();                                                        // here we use JSON to send data to server
  JSON.addItem(           'ID', elvf ('SubID'              ));
  
  showloader('newbuildings_subitems_container');
  var jsonstr = JSON.make();
  ajax_post('fillnewbuildingssubitems','newbuildings_subitems_container','index.php',jsonstr, c_fillnewbuildingssubitems);
 }
}

function c_fillnewbuildingssubitems() {
 resizepopup();
}

function addNBSubitem() {
 if (elvf ('SubID')) {
  JSON.clear();                                          // here we use JSON to send data to server
 // JSON.addItem('id'           , lastid                               );
  JSON.addItem(           'ID', elvf ('SubID'             ));
  JSON.addItem(        'addID', '1'                        ); // send any non-zero value here
 // JSON.addItem('SubitemsID'   , el('newbuildings_SubitemsID').value  );
  var jsonstr = JSON.make();
  ajax_get('fillnewbuildingssubitems','newbuildings_subitems_container','index.php',jsonstr, c_fillnewbuildingssubitems);
 } else {
  alert (elh('savefirst_nbs'));
 }
}

function deleteNBSubitem(id) {
 newitem=0;
 
 JSON.clear();                                          // here we use JSON to send data to server
// JSON.addItem('id'           , lastid                               );
 JSON.addItem(         'ID', elvf ('SubID'                       ));
 JSON.addItem(   'deleteID', id                                   );
// JSON.addItem( 'SubitemsID', el('newbuildings_SubitemsID').value  );
 var jsonstr = JSON.make();
 ajax_get('fillnewbuildingssubitems','newbuildings_subitems_container','index.php',jsonstr, c_fillnewbuildingssubitems);
}


function savefield(tablename, columnname, id, data) {
 JSON.clear();                                          // here we use JSON to send data to server
 JSON.addItem('id'           , id                                   );
 JSON.addItem('tablename'    , tablename                            );
 JSON.addItem('columnname'   , columnname                           );
 JSON.addItem('data'         , data                                 );
 var jsonstr = JSON.make();
 ajax_get('savefield','results','index.php',jsonstr);
}

function addDistrict() {
 var DistrictID = elsid('DistrictID');
// var buf = "";
 var deny = 0;
 
// alert (DistrictID);
 
 if (DistrictID>1) {
  var els = el('Districts').getElementsByClassName('DistrictID');
  
  for (var i=0; i<els.length; i++) {
//   alert (els[i].id);
   if ("District_"+DistrictID == els[i].id) deny = 1;
  }
  
  if (!deny) {
   var tmp = elh('district_tmp');
//   alert (tmp);
   tmp = str_replace("%ID%", DistrictID, tmp);
   tmp = str_replace("%Description%", elsh('DistrictID'), tmp);
   addhtmlraw('Districts', tmp);
   
   setactiveoptionid('DistrictID', 1);
  }
  
 }
 
 resizepopup();
 
 return buf;
 //district_tmp
}

function getDistricts() {
 var buf = "";
 var els = el('Districts').getElementsByClassName('DistrictID');
 
 for (var i=0; i<els.length; i++) {
  buf += els[i].id+";";
 }
 
 return buf;
}

function remove(id) {
 var element = el(id);
 if (element) {
  element.outerHTML = "";
  delete element;
  resizepopup();
 }
}

function input_click(id) {
 if (parseFloat(elv(id))==0) {
  el(id).value = "";
 }
}


function fillUploads() {
 resizepopup();
 
// alert ('test');
 ajax_post('fullUploads','status0','index.php','', resizepopup);
}
