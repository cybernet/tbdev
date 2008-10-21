function link_to_post(pid,tid)
{
var baseurl = "http://chat2pals.co.uk" //without ending slash
prompt("Link to this post (copy and send it to your friends)", baseurl+"/forums.php?action=viewtopic&topicid="+tid+"&page=p"+pid+"#"+pid);
return false;
}