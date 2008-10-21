// mainly for mailbox.php
function toggleChecked(state)
{
 var x=document.getElementsByTagName('input');
 for(var i=0;i<x.length;i++){
   if(x[i].type=='checkbox'){
     x[i].checked=state;
   }
 }
}

function toggleDisplay(id)
{
 var x=document.getElementById(id);
 if(x.style.display=='')x.style.display='none';
 else x.style.display='';
}

function toggleTemplate(x)
{
var y=true;
if(x.form.usetemplate.selectedIndex==0)y=false;
x.form.subject.disabled=y;
x.form.msg.disabled=y;
x.form.draft.disabled=y;
x.form.template.disabled=y;
}

function read(id)
{
var x=document.getElementById('msg_'+id);
var y=document.getElementById('img_'+id);
if(x.style.display==''){
 x.style.display='none';
 y.src='/pic/plus.gif';
}else{
 x.style.display='';
 y.src='/pic/minus.gif';
}
}