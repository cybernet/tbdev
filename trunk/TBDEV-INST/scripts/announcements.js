//this is preview.js but i combined it with announcement.js
//start//
var http_request = false;
function makePOSTRequest(url, parameters) {
document.getElementById('loading').style.visibility = "visible";
http_request = false;
if (window.XMLHttpRequest) { // Mozilla, Safari,...
http_request = new XMLHttpRequest();
if (http_request.overrideMimeType) {
// set type accordingly to anticipated content type
//http_request.overrideMimeType('text/xml');
http_request.overrideMimeType('text/html');
}
} else if (window.ActiveXObject) { // IE
try {
http_request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
http_request = new ActiveXObject("Microsoft.XMLHTTP");
} catch (e) {}
}
}
if (!http_request) {
alert(l_ajaxerror2);
return false;
}

http_request.onreadystatechange = alertContents;
http_request.open('POST', url, true);
http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
http_request.setRequestHeader("Content-length", parameters.length);
http_request.setRequestHeader("Connection", "close");
http_request.send(parameters);
}

function alertContents() {
if (http_request.readyState == 4) {
if (http_request.status == 200) {
//alert(http_request.responseText);
result = http_request.responseText;
document.getElementById('preview').innerHTML = result;
document.getElementById('loading').style.visibility = "hidden";
} else {
alert(l_ajaxerror);
}
}
}

function get(obj) {
var poststr = "msg=" + encodeURI( document.getElementById(obj).value );
makePOSTRequest(baseurl + '/preview.php', poststr);
}


function makeArequestR(url, parameters) {
document.getElementById('loading').style.visibility = "visible";
http_request = false;
if (window.XMLHttpRequest) { // Mozilla, Safari,...
http_request = new XMLHttpRequest();
if (http_request.overrideMimeType) {
// set type accordingly to anticipated content type
//http_request.overrideMimeType('text/xml');
http_request.overrideMimeType('text/html');
}
} else if (window.ActiveXObject) { // IE
try {
http_request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
http_request = new ActiveXObject("Microsoft.XMLHTTP");
} catch (e) {}
}
}
if (!http_request) {
alert(l_ajaxerror2);
return false;
}

http_request.onreadystatechange = makeArequestAlertContentsR;
http_request.open('POST', url, true);
http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
http_request.setRequestHeader("Content-length", parameters.length);
http_request.setRequestHeader("Connection", "close");
http_request.send(parameters);
}

function makeArequestAlertContentsR() {
if (http_request.readyState == 4) {
if (http_request.status == 200) {
//alert(http_request.responseText);
result = http_request.responseText;
document.getElementById('previewr').innerHTML = result;
document.getElementById('loading').style.visibility = "hidden";
if (result == '')
{
document.ratetorrent.ratebutton.value=l_thankyou;
document.ratetorrent.rating.disabled=true;
document.ratetorrent.ratebutton.disabled=true;
}
} else {
alert(l_ajaxerror);
}
}
}

function rate(obj,filename) {
var poststr = "id=" + encodeURI( document.getElementById("torrentid").value ) +
"&rating=" + encodeURI( document.getElementById("rating").value );
makeArequestR(filename, poststr);
}

function makeArequestT(url, parameters) {
document.getElementById('loadingt').style.visibility = "visible";
http_request = false;
if (window.XMLHttpRequest) { // Mozilla, Safari,...
http_request = new XMLHttpRequest();
if (http_request.overrideMimeType) {
// set type accordingly to anticipated content type
//http_request.overrideMimeType('text/xml');
http_request.overrideMimeType('text/html');
}
} else if (window.ActiveXObject) { // IE
try {
http_request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
http_request = new ActiveXObject("Microsoft.XMLHTTP");
} catch (e) {}
}
}
if (!http_request) {
alert(l_ajaxerror2);
return false;
}

http_request.onreadystatechange = makeArequestAlertContentsT;
http_request.open('POST', url, true);
http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
http_request.setRequestHeader("Content-length", parameters.length);
http_request.setRequestHeader("Connection", "close");
http_request.send(parameters);
}

function makeArequestAlertContentsT() {
if (http_request.readyState == 4) {
if (http_request.status == 200) {
//alert(http_request.responseText);
result = http_request.responseText;
document.getElementById('previewt').innerHTML = result;
document.getElementById('loadingt').style.visibility = "hidden";
if (result == '')
{
document.thanksform.thanksbutton.value=l_thankyoutoo;
document.thanksform.thanksbutton.disabled=true;
document.getElementById('newthanksby').innerHTML = "<a href='" + baseurl +"/userdetails.php?id="+ userid +"'>"+ username +"";
}
} else {
alert(l_ajaxerror);
}
}
}

function thanks(obj,filename) {
var poststr = "id=" + encodeURI( document.getElementById("thankstorrentid").value );
makeArequestT(filename, poststr);
}

function clearannouncement(obj,filename) {
var poststr = "clear=yes";
makeArequestR(filename, poststr);
setTimeout('dismissbox()',3000)
}

//end start//

/*// Drop-in content box- By Dynamic Drive
// For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
// This credit MUST stay intact for use
// --
// Annoucement Mod v.0.2 by xam.

var ie=document.all
var dom=document.getElementById
var ns4=document.layers
var calunits=document.layers? "" : "px"

var bouncelimit=32 //(must be divisible by dirol.gif
var direction="up"

function initbox(){
if (!dom&&!ie&&!ns4)
return
crossobj=(dom)?document.getElementById("dropin").style : ie? document.all.dropin : document.dropin
scroll_top=(ie)? truebody().scrollTop : window.pageYOffset
crossobj.top=scroll_top-250+calunits
crossobj.visibility=(dom||ie)? "visible" : "show"
dropstart=setInterval("dropin()",50)
}

function dropin(){
scroll_top=(ie)? truebody().scrollTop : window.pageYOffset
if (parseInt(crossobj.top)<100+scroll_top)
crossobj.top=parseInt(crossobj.top)+40+calunits
else{
clearInterval(dropstart)
bouncestart=setInterval("bouncein()",50)
}
}

function bouncein(){
crossobj.top=parseInt(crossobj.top)-bouncelimit+calunits
if (bouncelimit<0)
bouncelimit+=8
bouncelimit=bouncelimit*-1
if (bouncelimit==0){
clearInterval(bouncestart)
}
}
function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=60
0,height=10');");
}
function dismissbox(){
if (window.bouncestart) clearInterval(bouncestart)
crossobj.visibility="hidden"
window.location=requesturl
}

function show_annoucement(){
bouncelimit=32
direction="up"
initbox()
}

function truebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body*/
// Drop-in content box- By Dynamic Drive
// For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
// This credit MUST stay intact for use

var ie=document.all
var dom=document.getElementById
var ns4=document.layers
var calunits=document.layers? "" : "px"

var bouncelimit=32 //(must be divisible by dirol.gif
var direction="up"

function initbox(){
if (!dom&&!ie&&!ns4)
return
crossobj=(dom)?document.getElementById("dropin").style : ie? document.all.dropin : document.dropin
scroll_top=(ie)? truebody().scrollTop : window.pageYOffset
crossobj.top=scroll_top-250+calunits
crossobj.visibility=(dom||ie)? "visible" : "show"
dropstart=setInterval("dropin()",50)
}

function dropin(){
scroll_top=(ie)? truebody().scrollTop : window.pageYOffset
if (parseInt(crossobj.top)<100+scroll_top)
crossobj.top=parseInt(crossobj.top)+40+calunits
else{
clearInterval(dropstart)
bouncestart=setInterval("bouncein()",50)
}
}

function bouncein(){
crossobj.top=parseInt(crossobj.top)-bouncelimit+calunits
if (bouncelimit<0)
bouncelimit+=8
bouncelimit=bouncelimit*-1
if (bouncelimit==0){
clearInterval(bouncestart)
}
}

function dismissbox(){
if (window.bouncestart) clearInterval(bouncestart)
crossobj.visibility="hidden"
window.location="index.php";
}

function redo(){
bouncelimit=32
direction="up"
initbox()
}

function truebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}
window.onload=initbox