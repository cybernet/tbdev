function show_details(id, file) {
container = $('id-'+id);
if(container.className == 'details_view'){
container.innerHTML = '';
container.className = 'toggle_descr';
}else{
container.className = 'details_view';
$('id-'+id).innerHTML = '<img src="/pic/loading.gif" alt="Loading.." />';

req('/'+file+'.php?id='+id+'&ajax',
function(res) {
$('id-'+id).innerHTML = res.responseText;
}
)
}

}

function req(url, user_function) {
var http_request = false;

if (window.XMLHttpRequest) {
http_request = new XMLHttpRequest();
if (http_request.overrideMimeType) {
http_request.overrideMimeType('text/xml');
}
} else if (window.ActiveXObject) {
try {
http_request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
http_request = new ActiveXObject("Microsoft.XMLHTTP");
} catch (e) {}
}
}

if (!http_request) {
return false;
}
http_request.open('GET', url, true);
http_request.onreadystatechange = function() { statechange(http_request, user_function); };
http_request.send(null);

}

function statechange(http_request, user_function) {
if (http_request.readyState == 4) {
if (http_request.status == 200) {
user_function(http_request);
} else {
alert('There was a problem with the request.');
}
}
}