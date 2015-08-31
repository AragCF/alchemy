/*
 1.0
 initial release
 
 1.1 2014may04 23:19 Donino, Ramensky district, Moscow region, Russia
 function updateItem() added
 
*/

var JSON = new function() {
 var myjson = null;
 
 if (typeof JSON !== "undefined") {
  myjson = JSON;
 }
 
 this.params = new Array();
 this.values = new Array();
 
 this.parse = function(text) {
  if (myjson !== null ) {
   return myjson.parse(text);
  }
  
  var ret;
  ret = new Function("return " + text)();
  if (!ret) {
   ret = eval("(" + text + ")");
  }
  return ret;
 }
 this.clear = function() {
  this.params = new Array();
  this.values = new Array();
 }
 this.addItem = function(param, value) {
  this.params.push(param);
  this.values.push(value);
  
  return this.params.length;
 }
 this.updateItem = function(param, value) {
  for (var n=0; n<this.params.length; n++) {
   if (this.params[n]==param) {
    this.values[n]=value;
   }
  }
  
  return this.params.length;
 }
 this.make = function() {
  var ret = "{";
  var isSubEntity;
  
  for (var n=0; n<this.params.length; n++) {
   thisValue = this.values[n];
   if (typeof(thisValue)=='string') {
    if (thisValue.indexOf("{")==0) {
     isSubEntity =1;
    } else {
     isSubEntity =0;
    }
   } else {
    isSubEntity =0;
   }
//   alert (isSubEntity);
   if (isSubEntity) {
//    alert (thisValue);
    ret += '"'+this.params[n]+'":'+this.values[n];
   } else {
    ret += '"'+this.params[n]+'":"'+this.values[n]+'"';
   }
   if ((n+1)!=this.params.length) ret +=',';
  }
  
  ret += "}";
  
  return ret;
 }
};     

//var beginTime = new Date();
//for (i = 0; i < count; i++) {
// o = JSON.parse(jsonString);
//}

//Console.output("wrapper:" + (new Date() - beginTime));
