<?php 
ini_set ('display_errors', '0');
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
//=== get info from DB
$res_settings = mysql_query("SELECT * FROM gallery_admin") or sqlerr(__FILE__, __LINE__);
$arr_settings = mysql_fetch_assoc($res_settings);
$max_file_size = $arr_settings['max_file_size'];                 //1048576; 
$perpage = $arr_settings['per_page']; 
$num_rows = $arr_settings['num_rows']; 

$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	

$page_links = "<p><a class=altlink href=photo_gallery.php?my_gallery=1>view your gallerys</a> | <a class=altlink href=photo_gallery.php?public_gallerys=1>view all galleries</a> | ".  
"<a class=altlink href=photo_gallery.php?manage_gallerys=1>manage gallerys</a> | <a class=altlink href=photo_gallery.php?upload=1>upload images</a>".
"".((get_user_class() >= UC_ADMINISTRATOR) ? " | <a class=altlink href=/photo_gallery.php?gallery_admin=1>gallery admin</a>" : "")." </p>";

$res_gal = mysql_query("SELECT DISTINCT p_g.in_gallery, m_g.gallery_name  FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id WHERE m_g.user_id = $CURUSER[id] ORDER BY m_g.gallery_name") or sqlerr(__FILE__, __LINE__);
while ($arr_gal = mysql_fetch_assoc($res_gal))
$gal_name .= "• <span class=small><a class=altlink href=?my_gallery=$CURUSER[id]&gallery=$arr_gal[in_gallery]>".safechar($arr_gal['gallery_name'])."</a></span>".($arr_gal["share_gallery"] == 'friends' ? "<img src=/pic/buddylist.gif title=\"Friends only gallery\">" : '')." </span>";

//=== get defaults from DB
$res_classes = mysql_query("SELECT * FROM gallery_admin_users WHERE user_class = $CURUSER[class]") or sqlerr(__FILE__, __LINE__);
$arr_classes = mysql_fetch_assoc($res_classes);
$number_of_pics = $arr_classes['number_of_pics'];
$number_total = $arr_classes['number_total'];
$gal_per_member = $arr_classes['gal_per_member'];

//=== make filename safe
function repstr($str){
    $bad_stuff=array('<', '>','&gt;', '&lt;', ';', 'script', 'alert', 'php', 'include', '*', "'", '"', '(', ')', '=', '!', '#', '/', '-', '?', chr(0));
    $tmp=str_replace($bad_stuff,'',strtolower($str));
    if($str!==$tmp)
        $str=repstr($tmp);
    return $str;
}

//=== rating for images function
function ratingpic_image($num) {
    global $pic_base_url;
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 10)
        return;
    return "<img src=/pic/image_ratings/$r.gif align=absmiddle alt=\"rating: $num / 10\" />";
}

//=== drop down gallery box select and go to gallery
function on_select_gallery_change(){
global $CURUSER;
?>
<style type="text/css">

#popitmenu{
position: absolute;
background-color: white;
border:1px solid black;
font: normal 12px Verdana;
line-height: 18px;
z-index: 100;
visibility: hidden;
}

#popitmenu a{
text-decoration: none;
padding-left: 6px;
color: black;
display: block;
}

#popitmenu a:hover{ /*hover background color*/
background-color: #CCFF9D;
}

</style>
<script language="Javascript">
function goTo (page) {
	if (page != "" ) {
		if (page == "--" ) {
			resetMenu();
		} else {
			document.location.href = page;
		}
	}
	return false;
}
</script>
<?
$res_change = mysql_query("SELECT * FROM my_gallerys WHERE user_id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
$galleryz = "";
while ($arr_change = mysql_fetch_assoc($res_change))
$galleryz .= "<option value=?my_gallery=1&gallery=$arr_change[id] ".($arr_change["id"]==(0+$_GET['gallery'])?"selected='selected'":"").">".$arr_change["gallery_name"]."</option>";
echo "<br><b>jump to: </b><form name=gmenu><select class=select2 name=page onChange=\"goTo(this.options[this.selectedIndex].value)\"><option value=0>jump to my gallery</option>$galleryz</select></form>";
}

//=== drop down gallery select
function gallery_select(){
global $CURUSER;
$res_select = mysql_query("SELECT * FROM `my_gallerys` WHERE `user_id` =$CURUSER[id]") or sqlerr(__FILE__, __LINE__);
$list_select = "";
if (!$res_select)
$list_select .= '<option value=0>no gallery yet</option>';
else
while ($arr_select = mysql_fetch_assoc($res_select))
$list_select .= "<option value='$arr_select[id]'>".$arr_select["gallery_name"]."</option>";
echo "<select class=select2 name=add_to_gallery>$list_select</select>";
}

//=== galery admin
if ($_GET["gallery_admin"] || $_POST["gallery_admin"]){

if (get_user_class() < UC_ADMINISTRATOR)
stderr("Error", "staff only!"); 

if ($_POST["edit"]){
mysql_query("UPDATE `gallery_admin` SET `max_file_size` = ".sqlesc(0 + $_POST['max_file_size']).", `per_page` = ".sqlesc(0 + $_POST['per_page']).", `num_rows` = ".sqlesc(0 + $_POST['num_rows']).", `max_file_size` = ".sqlesc(0 + $_POST['max_file_size'])) or sqlerr(__FILE__, __LINE__);
$edited = '<h1>Changes accepted</h1>';
}

//===  update admin settings users
if ($_POST["update_admin_settings"]){

$gal_per_member = $_POST['gal_per_member'];
if (!$gal_per_member)
stderr("Error", "Nothing selected go <a class=altlink href=\"javascript: history.go(-1)\">back</a>.");

foreach ($gal_per_member as $key => $add_it) {

$number_total = $_POST['number_total'];
$number_of_pics = $_POST['number_of_pics'];
$user_class = $_POST['user_class'];
$gal_per_member = sqlesc(0 + $add_it);
$number_total = sqlesc(0 + $number_total[$key]);
$number_of_pics = sqlesc(0 + $number_of_pics[$key]);
$user_class = sqlesc(0 + $user_class[$key]);

mysql_query("UPDATE gallery_admin_users SET gal_per_member = $gal_per_member, number_total = $number_total, number_of_pics = $number_of_pics WHERE user_class = $user_class") or sqlerr(__FILE__, __LINE__);
} //=== end foreach
$edited = '<h1>Changes accepted</h1>';
}//=== ends update_admin_settings users

$res_settings = mysql_query("SELECT * FROM gallery_admin") or sqlerr(__FILE__, __LINE__);
$arr_settings = mysql_fetch_assoc($res_settings);
//=== make the page
stdhead("Gallery Admin");
begin_table();
echo "<form method=post action=?gallery_admin=1 enctype='multipart/form-data'><p><h1>Photo Gallery Admin Page</h1></p>$page_links<br>my galleries: $gal_name<br>$edited\n".
"<table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=2 align=center><h1>Photo Gallery Admin Page</h1></td></tr>\n".
"<tr><td class=clearalt6 colspan=2 align=center><h1>general settings:</h1></td></tr>\n".
"<tr><td class=clearalt7 align=right width=30%><b>Max file size:</b></td><td class=clearalt7 align=left width=70%><input type=text name=max_file_size value=$arr_settings[max_file_size] size=8 maxlength=16>".
" [ in kb ] currently set to <b>".mksize($arr_settings['max_file_size'])."</b></td></tr>\n".
"<tr><td class=clearalt6 align=right><b>Images per page:</b></td><td class=clearalt6 align=left><input type=text name=per_page value=$arr_settings[per_page] size=4 maxlength=4></td></tr>\n".
"<tr><td class=clearalt7 align=right><b>Number or rows per page:</b></td><td class=clearalt7 align=left><input type=text name=num_rows value=$arr_settings[num_rows] size=4 maxlength=4></td></tr>\n".
"<tr><td class=colhead align=center colspan=2><br><input type=submit value=\"update basic settings\" class=button><input type=hidden value=1 name=edit></form><br></td></tr>";
end_table();
begin_table();
echo "<br><br><form method=post action=?gallery_admin=1 enctype='multipart/form-data'><input type=hidden value=1 name=update_admin_settings>".
"<table border=1 cellspacing=0 cellpadding=5 width=737>".
"<tr><td class=colhead colspan=5 align=center><h1>user class settings:</h1></td></tr>\n".
"<tr><td class=clearalt7 align=center colspan=5><br>more stuff here like stuff about user calsses etc.<br><br></td></tr>\n".
"<tr><td class=colhead>user class</td><td class=colhead align=center>class id</td><td class=colhead>".
"# of pics at a time</td><td class=colhead>total number of pics</td><td class=colhead>number of galleries total</td></tr>\n";

for ($i = 0; $i <= UC_SYSOP; ++$i){
$res_classes = mysql_query("SELECT * FROM gallery_admin_users WHERE user_class = $i") or sqlerr(__FILE__, __LINE__);
$arr_classes = mysql_fetch_assoc($res_classes);
//=======change colors
$count2= (++$count2)%2;
$class = 'clearalt'.($count2==0?'6':'7');
echo"<tr><td class=$class align=right width=15%><b>" . get_user_class_name($i) . ":</b></td><td align=center class=$class><b>$i</b></td>".
"<td align=left class=$class><input type=text name=number_of_pics[] value=$arr_classes[number_of_pics] size=4 maxlength=4></td>".
"<td align=left class=$class><input type=text name=number_total[] value=$arr_classes[number_total] size=4 maxlength=4></td>".
"<td align=left class=$class><input type=text name=gal_per_member[] value=$arr_classes[gal_per_member] size=4 maxlength=4>".
"<input type=hidden value=$i name=user_class[]></td></tr>\n";
}
echo"<tr><td class=colhead align=center colspan=5><br><input type=submit value=\"update per class settings\" class=button></form><br></td></tr>";
stdfoot();
end_table();
die();
}//=== end galery admin

//=== count of entered images
    $res = mysql_query("SELECT COUNT(*) FROM photo_gallery WHERE user_id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_row($res);
    $count = $arr[0];

//=== rate image ///=== if image rated, this will replace the old rating with the new one
if ($_GET["takerate"]){
$id = isset($_GET['takerate']) ? (int)$_GET['takerate'] : 0;
if(!is_valid_id($id))
stderr("Error", "Bad Id!"); 

$rate_me = isset($_GET['rate_me']) ? (int)$_GET['rate_me'] : 0;
if ($rate_me <= 0 || $rate_me > 10)
stderr("Error", "invalid rating number");

$res = mysql_query("SELECT image_id, user_id, rating FROM image_ratings WHERE image_id = $id AND user_id = $CURUSER[id]");
$row = mysql_fetch_array($res);

if (!$row){ //=== add new rating
mysql_query("INSERT INTO image_ratings (image_id, user_id, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rate_me, NOW())");
mysql_query("UPDATE photo_gallery SET numratings = numratings + 1, ratingsum = ratingsum + $rate_me WHERE id = $id");
header("Location: /photo_gallery.php?info=$id&rated=1");
die();
}
else { //=== change rating
mysql_query("UPDATE image_ratings SET rating = $rate_me WHERE image_id = $id AND user_id = $CURUSER[id]");
mysql_query("UPDATE photo_gallery SET ratingsum = ratingsum + $rate_me - $row[rating] WHERE id = $id");
header("Location: /photo_gallery.php?info=$id&rate_changed=1");
die();
}
}

//=== add comment
if ($_GET['comment']){	
$photo_id = isset($_GET['comment']) ? (int)$_GET['comment'] : 0;
if(!is_valid_id($photo_id))
stderr("Error", "Bad Id!");

if(isset($_POST['pic_comment']) && $_POST['pic_comment'] == '')
stderr("Error", "comment body can not be empty! use your back button and fill in some text!");

$added = sqlesc(get_date_time());
$pic_comment = sqlesc($_POST["pic_comment"]);

mysql_query("INSERT INTO comments (user, text, ori_text, photo_gallery, added) VALUES(".sqlesc($CURUSER["id"]).", $pic_comment, $pic_comment, $photo_id, $added)") or sqlerr(__FILE__, __LINE__);
header("Location: photo_gallery.php?info=$photo_id&edited=1");
die();
}

//=== edit comment
if ($_GET['edit_comment']){
$edit_comment = isset($_GET['edit_comment']) ? (int)$_GET['edit_comment'] : 0;
if(!is_valid_id($edit_comment))
stderr("Error", "Bad Id!");

$photo_id = isset($_GET['photo_id']) ? (int)$_GET['photo_id'] : 0;
if(!is_valid_id($photo_id))
stderr("Error", "Bad Id!");

if(isset($_POST['pic_comment']) && $_POST['pic_comment'] == '')
stderr("Error", "comment body can not be empty! use your back button and fill in some text!");

$sure = isset($_GET['sure']) ? (int)$_GET['sure'] : 0;
if ($sure === 1){

$res_gal = mysql_query("SELECT text,id FROM comments WHERE id = ".sqlesc($edit_comment)) or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

stdhead("edit comment");
begin_table();

echo "$page_links<table width=737><form method=post action=?edit_comment=".$arr_gal["id"]."&photo_id=$photo_id&sure=2 enctype='multipart/form-data'><p><b>Edit comment</b><p>".
"<tr><td colspan=2 align=center class=colhead>Edit comment</td></tr><tr><td class=clearalt6 align=right valign=top width=25%><br><b>comment:</b></td>".
"<td class=clearalt6 align=left width=75%><br><textarea name=pic_comment cols=100 rows=3>".safechar($arr_gal["text"])."</textarea><br></td></tr>".
"<td colspan=2 align=center class=clearalt6><input type=submit value=edit class=button><br><br></td></tr>".
"</form></table>";
stdfoot();
end_table();
}
if ($sure === 2){
$added = sqlesc(get_date_time());
$pic_comment = sqlesc($_POST["pic_comment"]);

mysql_query("UPDATE comments SET text = $pic_comment, editedby = ".sqlesc($CURUSER["id"]).", editedat = $added WHERE id = ".sqlesc($edit_comment))or sqlerr(__FILE__, __LINE__);
header("Location: photo_gallery.php?info=$photo_id&edited=1");
}
}

//=== delete comment
if ($_GET['delete_comment']){

$comment_id = isset($_GET['delete_comment']) ? (int)$_GET['delete_comment'] : 0;
if(!is_valid_id($comment_id))
stderr("Error", "Bad Id!");

$photo_id = isset($_GET['photo_id']) ? (int)$_GET['photo_id'] : 0;
if(!is_valid_id($photo_id))
stderr("Error", "Bad Id!");

if (!$_GET['sure'])
stderr("Confirm!", "are you sure you want to delete this comment? <a class=altlink href=?delete_comment=$comment_id&photo_id=$photo_id&sure=1><b>YES</b></a> - <a class=altlink href=?info=$photo_id><b>NO</b></a>..");
mysql_query("DELETE FROM comments WHERE id = ".sqlesc($comment_id));
header("Location: photo_gallery.php?info=$photo_id&edited=1");
}

//=== add new gallery
if ($_GET['manage']){

//=== count of entered images
    $res_c = mysql_query("SELECT COUNT(*) FROM my_gallerys WHERE user_id=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
    $arr_c = mysql_fetch_row($res_c);
if(($count_c = $arr_c[0]) >= $gal_per_member)
stderr("Error!", "As a <b>".get_user_class_name($CURUSER['class'])."</b> you may have up to <b>$gal_per_member</b> gallerys, you now have <b>$count_c</b>.");

$need = array('public', 'private', 'friends');
if ($_POST["new_gallery"] === '' || !in_array($_POST["gal_share_new"], $need) || $_POST["new_gallery"] == 'add gallery name here and click add!')
stderr("Error!", "you must enter both a name and a share for your new gallery.  go <a class=altlink href=\"javascript: history.go(-1)\">back</a> and fix it.");

$name = mysql_real_escape_string(safechar($_POST["new_gallery"]));

//=== add check to see if name exists...
$res_gal_name = mysql_query("SELECT gallery_name FROM my_gallerys WHERE user_id = ".sqlesc($CURUSER['id'])." AND gallery_name = '$name'") or sqlerr(__FILE__, __LINE__);
$arr_gal_name = mysql_num_rows($res_gal_name);

if ($arr_gal_name > 0)
stderr("Error", "That gallery name exists! go <a class=altlink href=\"javascript: history.go(-1)\">back</a> and select another name.");

$gal_share_new = sqlesc($_POST["gal_share_new"]);

mysql_query("INSERT INTO my_gallerys (user_id, gallery_name, share_gallery) VALUES(".sqlesc($CURUSER["id"]).", '$name', $gal_share_new)") or sqlerr(__FILE__, __LINE__);
header("Location: photo_gallery.php?manage_gallerys=1&edited=1");
}

//=== edit gallery
if ($_GET['edit']){

if ($_POST["gallery_name"] === '')
stderr("Error!", "you must enter a name for your gallery. go <a class=altlink href=\"javascript: history.go(-1)\">back</a> and select a name.");

$gallery_name = mysql_real_escape_string(safechar($_POST["gallery_name"]));
$gal_share = sqlesc($_POST["gal_share"]);
$id = sqlesc(0 + $_POST["id"]);

//=== add check to see if name exists...
$res_gal_name = mysql_query("SELECT gallery_name FROM my_gallerys WHERE user_id = ".sqlesc($CURUSER['id'])." AND gallery_name = '$gallery_name' AND id != $id") or sqlerr(__FILE__, __LINE__);
$arr_gal_name = mysql_num_rows($res_gal_name);

if ($arr_gal_name > 0)
stderr("Error", "That gallery name exists. go <a class=altlink href=\"javascript: history.go(-1)\">back</a> and select another name.");

mysql_query("UPDATE my_gallerys SET gallery_name = '$gallery_name', share_gallery = $gal_share WHERE user_id = ".sqlesc($CURUSER["id"])." AND id = $id") or sqlerr(__FILE__, __LINE__);
header("Location: photo_gallery.php?manage_gallerys=1&edited=1");
}

//=== delete gallery
if ($_GET['delete_gallery']){
$id = sqlesc(0 + $_GET["id"]);

$res_gal = mysql_query("SELECT user_id FROM my_gallerys WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

if ($arr_gal['user_id'] !== $CURUSER['id'] && get_user_class() < UC_MODERATOR)
stderr("Error", "This is not your gallery to delete!");

mysql_query("DELETE FROM my_gallerys WHERE id = $id");
header("Location: photo_gallery.php?manage_gallerys=1&edited=1");
}

//=== manage galleries
if ($_GET['manage_gallerys']){

$res_mg = mysql_query("SELECT * FROM my_gallerys WHERE user_id=" . sqlesc($CURUSER['id'])) or sqlerr(__FILE__,__LINE__);
?>
<style type="text/css">

#popitmenu{
position: absolute;
background-color: white;
border:1px solid black;
font: normal 12px Verdana;
line-height: 18px;
z-index: 100;
visibility: hidden;
}

#popitmenu a{
text-decoration: none;
padding-left: 6px;
color: black;
display: block;
}

#popitmenu a:hover{ /*hover background color*/
background-color: #CCFF9D;
}

</style>
<script type="text/javascript" src="javascripts/cleartext.js"></script>
<?
stdhead("Manage Gallerys");
begin_table();

echo "<p><b>$CURUSER[username]'s Photo Gallery Manager</b><br><br>$page_links<br>my galleries: $gal_name".
"".($_GET["edited"] == '1' ? "<h1>Update successfull</h1>" : "")."<table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=2>My Gallery  Manager</td></tr>".
"<tr><td class=clearalt6 colspan=2><br><br><p>From here you can add new galleries, edit existing ones, and change how you would like to share them...".
" publically, with friends or not at all</p><br></td></tr><tr><td class=clearalt6 colspan=2><form method=post action=?manage=1 enctype='multipart/form-data'>".
"<br><b>add new gallery:</b> <input type=text name=new_gallery size=60 value='add gallery name here and click add!' maxlength=60 class=cleardefault> <b>share this gallery:</b> ".
"<input type=radio name=gal_share_new value=public> public <input type=radio name=gal_share_new value=private> private".
"<input type=radio name=gal_share_new value=friends> friends<br><br></td></tr><tr><td class=clearalt6 colspan=2 align=center><br><br>".
"<input type=submit value=add class=button><br></form></td></tr><tr><td class=colhead colspan=2>Manage My Gallerys</td></tr>";
while ($row_mg = mysql_fetch_assoc($res_mg)){
$id = 0 + $row_mg['id'];
echo"<form method=post action=?edit=1 enctype='multipart/form-data'><tr><td class=clearalt6 colspan=2 align=center><br><b>gallery name:</b>".
" <input type=text name=gallery_name value=\"".safechar($row_mg['gallery_name'])."\" size=60 maxlength=60><input type=hidden name=id value=$id>".
"<input type=radio name=gal_share" . ($row_mg["share_gallery"] == "public" ? " checked" : "") . " value=public> public ".
"<input type=radio name=gal_share" . ($row_mg["share_gallery"] == "private" ? " checked" : "") . " value=private> private".
"<input type=radio name=gal_share" . ($row_mg["share_gallery"] == "friends" ? " checked" : "") . " value=friends> friends ".
"<input class=button type=submit value=Edit></form> <a class=altlink href=?delete_gallery=1&id=$id>".
"<input class=button type=submit value=Delete></a></td></tr>";
}
echo "</table>"; 
on_select_gallery_change();
stdfoot();
end_table();
die();
}	

//===  edit multi
if ($_GET["multi_edit"]){

$user_id = 0 + $_POST['user_id'];
if ($user_id != $CURUSER['id'] && get_user_class() < UC_MODERATOR) 
stderr("Error", "this in not your gallery to edit!.");
$edit= $_POST['image'];
if (!$edit)
stderr("Error", "Nothing selected go <a class=altlink href=\"javascript: history.go(-1)\">back</a>.");

foreach ($edit as $key => $add_it) {

$image_id = $_POST['image_id'];
$move_to_gallery = $_POST['move_to_gallery'];
$image_id = sqlesc(0 + $image_id[$key]);
$move_to_gallery = sqlesc(0 + $move_to_gallery[$key]);
$name = " name = '".mysql_real_escape_string(safechar($add_it))."', ";
mysql_query("UPDATE photo_gallery SET $name in_gallery = $move_to_gallery WHERE user_id = ".sqlesc($user_id)." AND id = $image_id") or sqlerr(__FILE__, __LINE__);
} //=== end foreach

stderr("Sucess!", "<center>Images have been up-dated. Would you like to <br><br> ".
"<a class=altlink href=photo_gallery.php?my_gallery=1>view your gallerys</a> | <a class=altlink href=photo_gallery.php?public_gallerys=1>view all galleries</a>  | ".  
"<a class=altlink href=photo_gallery.php?manage_gallerys=1>manage gallerys</a> | <a class=altlink href=photo_gallery.php?upload=1>upload images</a>".
"".((get_user_class() >= UC_ADMINISTRATOR) ? " | <a class=altlink href=/photo_gallery.php?gallery_admin=1>gallery admin</a>" : "")." <br><br>my galleries: $gal_name<br></center>");
die();
}//=== end edit multi

//===  Delete and edit images 
if ($_GET["delete_or_edit"]){

if (!isset($_POST[delete_image]) && !isset($_POST[edit_image]))
stderr("Error", "Nothing selected go <a class=altlink href=\"javascript: history.go(-1)\">back</a>.");
if (isset($_POST[delete_image]) && isset($_GET[edit_image]))
stderr("Error", "you can't both delete AND edit images! go <a class=altlink href=\"javascript: history.go(-1)\">back</a> and make a decision!");

if ($_POST['edit_image']){

//=== make the page
stdhead("Edit image multi");
begin_table();
echo "<form method=post action=?multi_edit=1 enctype='multipart/form-data'><p><b>Edit Images</b></p>$page_links<br>my galleries: $gal_name<br>\n".
"<table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=3>Photo Gallery multi edit</td></tr>\n";

$edit= $_POST['edit_image'];
foreach ($edit as $edit_id) {
//=======change colors
$count2= (++$count2)%2;
$class = 'clearalt'.($count2==0?'6':'7');

$res = mysql_query("SELECT * FROM photo_gallery WHERE id=".sqlesc((0 + $edit_id)));
$arr = mysql_fetch_assoc($res);

	   		$image = array_values(getimagesize("bitbucket/".safechar($arr['location']).""));
			list($width, $height) = $image;
			$image_size = "bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."";

$res_select = mysql_query("SELECT * FROM `my_gallerys` WHERE `user_id` =$arr[user_id]") or sqlerr(__FILE__, __LINE__);
$list_select = "";
if (!$res_select)
$list_select .= '<option value=0>no gallerys yet</option>';
else
while ($arr_select = mysql_fetch_assoc($res_select))
$list_select .= "<option value='$arr_select[id]' ".($arr["in_gallery"]==$arr_select["id"]?"selected='selected'> • • • keep in gallery:: ".$arr_select["gallery_name"]." • • •":"> • move to gallery:: ".$arr_select["gallery_name"]."")."</option>";

$our_image = safechar("/bitbucket/thumbs/$arr[location]");
echo"<tr><td class=$class align=center valign=middle width=120><img src=\"$our_image\"><br>".
"<br>$width x $height <br>[ ".mksize(filesize($image_size))." ]</td><td class=$class align=right valign=middle width=120>".
"<b>Image title:</b><br><br><b>move to gallery:</b></td><td class=$class align=left valign=middle> ".
"<input type=text name=\"image[]\" value=\"".safechar($arr['name'])."\" size=60><input type=hidden name=\"image_id[]\" value=\"$arr[id]\"><br><br>".
"<select name=\"move_to_gallery[]\">$list_select</select><br><input type=hidden name=user_id value=$arr[user_id]></td></tr>\n";
} //=== end for each
echo"<tr><td class=colhead align=center colspan=3><br><input type=submit value=\"update image info\" class=button></form><br></td></tr>";
stdfoot();
end_table();
die();
} //=== end edit images

if ($_POST['delete_image'])

$checked= $_POST['delete_image'];

foreach ($checked as $delete) {
//=== get file to delete
$res = mysql_query("SELECT location, user_id FROM photo_gallery WHERE id=".sqlesc((0 + $delete)));
$arr = mysql_fetch_assoc($res);

if ($arr['user_id'] !== $CURUSER['id'] && get_user_class() < UC_MODERATOR)
stderr("Error", "This is not your image to delete!");

//=== delete image and thumb
$filepath_thumb = "./bitbucket/thumbs/$arr[location]";
$filepath = "./bitbucket/$arr[location]";
unlink($filepath); 
unlink($filepath_thumb); 

//=== remove other stuff 
mysql_query ("DELETE FROM photo_gallery WHERE user_id = $arr[user_id] AND id=".sqlesc($delete));
mysql_query ("DELETE FROM image_ratings WHERE image_id=".sqlesc($delete));
mysql_query ("DELETE FROM comments WHERE photo_gallery=".(0 + $delete));
}
header("Location: ".$_SERVER['HTTP_REFERER']."&deleted=1");
}

//=== image info edit
if ($_GET['edit_image_info']){

$photo_id = isset($_GET['edit_image_info']) ? (int)$_GET['edit_image_info'] : 0;
if(!is_valid_id($photo_id))
stderr("Error", "Bad Id!");

$res_gal = mysql_query("SELECT p_g.*, m_g.gallery_name, u.username, u.id AS uid FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id LEFT JOIN users AS u ON u.id = p_g.user_id WHERE p_g.id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

if ($arr_gal['user_id'] !== $CURUSER['id'] && get_user_class() < UC_MODERATOR)
stderr("Error", "This is not your image to edit!");

if (!$_GET['sure']){

$res_select = mysql_query("SELECT * FROM `my_gallerys` WHERE `user_id` =$arr_gal[user_id]") or sqlerr(__FILE__, __LINE__);
$list_select = "";
if (!$res_select)
$list_select .= '<option value=0>no gallerys yet</option>';
else
while ($arr_select = mysql_fetch_assoc($res_select))
$list_select .= "<option value='$arr_select[id]' ".($arr_gal["in_gallery"]==$arr_select["id"]?"selected='selected'> • • • keep in gallery:: ".$arr_select["gallery_name"]." • • •":"> • move to gallery:: ".$arr_select["gallery_name"]."")."</option>";

stdhead("Edit image info");
begin_table();

			$image = array_values(getimagesize("bitbucket/$arr_gal[location]"));
			list($width, $height) = $image;
			$image_size = "bitbucket/$arr_gal[location]";

if ($width >= "500")
$show_image = "<img width=500 src=bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))." title=\"".safechar($arr_gal['name'])."\">";
else
$show_image = "<img src=bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))." title=\"".safechar($arr_gal['name'])."\">";
			
echo "<p><b><a class=altlink href=userdetails.php?id=$arr_gal[uid]>$arr_gal[username]'s</a> Photo Gallery</b>".
"<br><br></p>$page_links<p><p><b>Edit image info</b></p>my galleries: $gal_name".($_GET["edited"] == '1' ? "<h1>Update successfull</h1>" : "")."".
"<form method=post action=?edit_image_info=$arr_gal[id]&sure=1 enctype='multipart/form-data'><table border=1 cellspacing=0 cellpadding=5 width=737>".
"<tr><td class=colhead><b>Edit: $arr_gal[name]</b></td></tr><tr><td align=center valign=top class=clearalt6><b>added on: </b>$arr_gal[added]".
"<br><br>$show_image<br><br><b>currently in gallery:</b> ".safechar($arr_gal['gallery_name'])."<br><br> <b>Image title:</b>".
" <input type=text name=name value=\"".safechar($arr_gal['name'])."\" size=60>  <select class=select2 name=gallery>$list_select</select><br><br>".
"<b>file name:</b> ".safechar(str_replace(" ", "%20", $arr_gal['location']))." <b>dimentions:</b> $width x $height | <b>file size:</b> ".mksize(filesize($image_size))."<br>".
"<br>location: <a class=altlink href=$BASEURL/bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."><b>$BASEURL/bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."</b></a><br><br> ".
"</td></tr><tr><td class=clearalt6 align=center colspan=2><input type=submit value=\"Update Image Info\" class=button><br><br></td></tr></table></form><br><br>";

on_select_gallery_change();
stdfoot();
end_table();
die();
}
//=== make the changes
if($_POST['name'] == '')
stderr("Error", "This image must have a name! use your back button and fill in some text!");

$name = mysql_real_escape_string(safechar($_POST["name"]));
$in_gallery = sqlesc(0 + $_POST['gallery']);

mysql_query("UPDATE photo_gallery SET name = '$name', in_gallery = $in_gallery WHERE user_id = ".sqlesc($arr_gal['user_id'])." AND id = $photo_id") or sqlerr(__FILE__, __LINE__);
header("Location: photo_gallery.php?info=$photo_id&edited=1");
}

//===  Delete single image
if ($_GET["delete_single"]){

$photo_id = isset($_GET['delete_single']) ? (int)$_GET['delete_single'] : 0;
if(!is_valid_id($photo_id))
stderr("Error", "Bad Id!");

//=== get file to delete
$res = mysql_query("SELECT location, user_id FROM photo_gallery WHERE id=".sqlesc($photo_id));
$arr = mysql_fetch_assoc($res);

if ($arr['user_id'] !== $CURUSER['id'] && get_user_class() < UC_MODERATOR)
stderr("Error", "This is not your image to delete!");

//=== delete image and thumb
$filepath_thumb = "./bitbucket/thumbs/$arr[location]";
$filepath = "./bitbucket/$arr[location]";
unlink($filepath); 
unlink($filepath_thumb); 

//=== remove other stuff 
mysql_query ("DELETE FROM photo_gallery WHERE user_id = $arr[user_id] AND id=".sqlesc($photo_id));
mysql_query ("DELETE FROM image_ratings WHERE image_id=".sqlesc($photo_id));
mysql_query ("DELETE FROM comments WHERE photo_gallery=".sqlesc($photo_id));

stderr("Sucess!", "<center>Image has been deleted. Would you like to <br><br> ".
"<a class=altlink href=photo_gallery.php?my_gallery=1>view your gallerys</a> | <a class=altlink href=photo_gallery.php?public_gallerys=1>view all galleries</a>  | ".  
"<a class=altlink href=photo_gallery.php?manage_gallerys=1>manage gallerys</a> | <a class=altlink href=photo_gallery.php?upload=1>upload images</a>".
"".((get_user_class() >= UC_ADMINISTRATOR) ? " | <a class=altlink href=/photo_gallery.php?gallery_admin=1>gallery admin</a>" : "")." <br><br>my galleries: $gal_name</center>");
}

//=== image info page
if ($_GET['info']){

$photo_id = isset($_GET['info']) ? (int)$_GET['info'] : 0;
if(!is_valid_id($photo_id))
stderr("Error", "Bad Id!");

$res_gal = mysql_query("SELECT p_g.*, m_g.gallery_name, u.username, u.id AS uid FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id LEFT JOIN users AS u ON u.id = p_g.user_id WHERE p_g.id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

?>
<style type="text/css">

#popitmenu{
position: absolute;
background-color: white;
border:1px solid black;
font: normal 12px Verdana;
line-height: 18px;
z-index: 100;
visibility: hidden;
}

#popitmenu a{
text-decoration: none;
padding-left: 6px;
color: black;
display: block;
}

#popitmenu a:hover{ /*hover background color*/
background-color: #CCFF9D;
}

</style>
<script language="Javascript">
function insertAtCursor(myField, myValue)
{
if (document.selection)
{
myField.focus();
sel = document.selection.createRange();
sel.text = myValue;
}
else if (myField.selectionStart || myField.selectionStart == '0')
{
var startPos = myField.selectionStart;
var endPos = myField.selectionEnd;
myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
   myField.focus();
}
else
{
myField.value += myValue;
}
}

function SmileIT(smile,form,text){

    smile = " "+smile+" ";
    insertAtCursor(document.forms[form].elements[text],smile);
    document.forms[form].elements[text].focus();
}

function openTable(tableid) { 
which = document.getElementById(tableid);
if (which.style.display == "block") {
which.style.display = "none";
}
else {
which.style.display = "block";
}
}

   function PopupPic(sPicURL) {
     window.open( "photo_popup.htm?"+sPicURL, "",  
     "resizable=1,HEIGHT=200,WIDTH=200");
   }


/***********************************************
* Pop-it menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

var defaultMenuWidth="180px" //set default menu width.

var linkset=new Array()
//SPECIFY MENU SETS AND THEIR LINKS. FOLLOW SYNTAX LAID OUT
linkset[0]='<p align=center><b>Rate Image!</b></p>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=10><img src="pic/image_ratings/10.gif" alt="10 - tops"> 10</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=9><img src="pic/image_ratings/9.gif" alt="9 out of 10"> 9</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=8><img src="pic/image_ratings/8.gif" alt="8 out of 10"> 8</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=7><img src="pic/image_ratings/7.gif" alt="7 out of 10"> 7</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=6><img src="pic/image_ratings/6.gif" alt="6 out of 10"> 6</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=5><img src="pic/image_ratings/5.gif" alt="5 out of 10"> 5</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=4><img src="pic/image_ratings/4.gif" alt="4 out of 10"> 4</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=3><img src="pic/image_ratings/3.gif" alt="3 out of 10"> 3</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=2><img src="pic/image_ratings/2.gif" alt="2 out of 10"> 2</a>'
linkset[0]+='<a class=altlink href=?takerate=<?php echo $photo_id?>&rate_me=1><img src="pic/image_ratings/1.gif" alt="1 - bad"> 1</a>'
////No need to edit beyond here

var ie5=document.all && !window.opera
var ns6=document.getElementById

if (ie5||ns6)
document.write('<div id="popitmenu" onMouseover="clearhidemenu();" onMouseout="dynamichide(event)"></div>')

function iecompattest(){
return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}

function showmenu(e, which, optWidth){
if (!document.all&&!document.getElementById)
return
clearhidemenu()
menuobj=ie5? document.all.popitmenu : document.getElementById("popitmenu")
menuobj.innerHTML=which
menuobj.style.width=(typeof optWidth!="undefined")? optWidth : defaultMenuWidth
menuobj.contentwidth=menuobj.offsetWidth
menuobj.contentheight=menuobj.offsetHeight
eventX=ie5? event.clientX : e.clientX
eventY=ie5? event.clientY : e.clientY
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? iecompattest().clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie5? iecompattest().clientHeight-eventY : window.innerHeight-eventY
//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by it's width
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX-menuobj.contentwidth+"px" : window.pageXOffset+eventX-menuobj.contentwidth+"px"
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX+"px" : window.pageXOffset+eventX+"px"
//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.style.top=ie5? iecompattest().scrollTop+eventY-menuobj.contentheight+"px" : window.pageYOffset+eventY-menuobj.contentheight+"px"
else
menuobj.style.top=ie5? iecompattest().scrollTop+event.clientY+"px" : window.pageYOffset+eventY+"px"
menuobj.style.visibility="visible"
return false
}

function contains_ns6(a, b) {
//Determines if 1 element in contained in another- by Brainjar.com
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function hidemenu(){
if (window.menuobj)
menuobj.style.visibility="hidden"
}

function dynamichide(e){
if (ie5&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}

if (ie5||ns6)
document.onclick=hidemenu
</script>
<?	

//=== get image rating
if ($arr_gal["numratings"] != 0)
$rating =  ROUND($arr_gal["ratingsum"] / $arr_gal["numratings"], 1);
$rpic = ratingpic_image($rating); 

if ($rpic == '')
$rate_first = "<br>Rate Image:";
else
$rate_first = "<br>Rating:";

stdhead("Image Info");
begin_table();

			$image = array_values(getimagesize("bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location'])).""));
			list($width, $height) = $image;
			$image_size = "bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."";
			$pop_up_thingie = "bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']));

//=== try to fool firefox
$no_cache = "?nocache=".time();
			
if ($width >= "500")
$show_image = "<a class=altlink href=\"javascript:PopupPic('$pop_up_thingie$no_cache')\">".
"<img width=500 src=bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."$no_cache title=\"click to open $arr_gal[name] full size in new window\"></a>".
"<span class=small><br><br>[ this image has been re-sized to fit this window. click the image to view full size ]</span>";
else
$show_image = "<a class=altlink href=\"javascript:PopupPic('$pop_up_thingie$no_cache')\">".
"<img src=bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."$no_cache title=\"".safechar($arr_gal['name'])." click to open in new window\"></a>";
			
echo "<p><b><a class=altlink href=userdetails.php?id=$arr_gal[uid]>$arr_gal[username]'s</a> Photo Gallery</b><br><br></p>$page_links<p><img src=/pic/arrow_prev.gif>".
"<a class=altlink href=\"javascript: history.go(-1)\">back to gallery</a></p>my galleries: $gal_name".
"".($_GET["edited"] == '1' ? "<h1>Update successfull</h1>" : "").($_GET["rated"] == '1' ? "<h1>Image rated</h1>" : "").($_GET["rate_changed"] == '1' ? "<h1>Image rating changed</h1>" : "")."".
"<form method=post action=?comment=$arr_gal[id] name=compose enctype='multipart/form-data'><table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead><b>".safechar($arr_gal['name'])."</b></td></tr>".
"<tr><td align=center valign=top class=clearalt6><b>added on: </b>$arr_gal[added] by: <a class=altlink href=userdetails.php?id=$arr_gal[user_id]>$arr_gal[username]</a>".
"$spacer".($arr_gal['user_id'] === $CURUSER['id'] || get_user_class() >= UC_MODERATOR ? "[ <a class=altlink href=?edit_image_info=$photo_id>edit info</a> ] [ <a class=altlink href=/image_manip/photo_gallery_crop.php?image=$photo_id>edit image</a>".
" ]  [ <a class=altlink href=?delete_single=$photo_id>delete</a> ]" : "")."<br><br>$show_image<br><a class= altlink href=\"#\" onMouseover=\"showmenu(event,linkset[0])\" onMouseout=\"delayhidemenu()\"><b>$rate_first</b> $rpic</a> $spacer ".($arr_gal['numratings'] > 0 ? "[ $rating / 10 with $arr_gal[numratings] votes]" : "$rating")."<br>".
"<br><b>title:</b> $arr_gal[name] | <b>file name:</b> $arr_gal[location] | <b>dimentions:</b> $width x $height | <b>file size:</b> ".mksize(filesize($image_size))."<br>".
"<br><b>in gallery:</b> ".(!$arr_gal['gallery_name'] ? " [ no gallery selected ]" : "".safechar($arr_gal['gallery_name'])."")." <br><br><b>image location:</b> <a class=altlink href=$BASEURL/bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."><b>$BASEURL/bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."</b></a><br><br> ".
"<a class=altlink href=\"#\" onclick=\"openTable('comments'); return false;\"><b>view comments</b></a> |".
"<a class=altlink href=\"#\" onclick=\"openTable('comment'); return false;\"><b>add a comment</b></a><br><br><table id=comment style=\"display: none;\" align=center><tr>".
"<td class=clear align=right valign=top width=25%><br><b>comment:</b></td><td class=clear align=left width=75%><br><textarea name=pic_comment cols=100 rows=3></textarea>".
"<br></td></tr><tr><td class=clear align=center></td><td class=clearalign=center>";
while ((list($code, $url) = each($smilies)) && $t<25) {
echo"<a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','compose','pic_comment')\"><img src=/pic/smilies/".$url."></a>";
$t++;
}
echo "<br><br></tr><tr><td class=clear align=center colspan=2><input type=submit value=\"add comment\" class=button><br></td></tr></table></form><br>".
"<table id=comments style=\"display: none;\" align=center>";

$res_gal = mysql_query("SELECT c.text, c.id, c.added AS c_added, c.editedby, c.ori_text, u.username, u.avatar, u.id AS uid FROM comments AS c LEFT JOIN users AS u ON u.id = c.user WHERE c.photo_gallery = $photo_id ORDER BY c.id DESC $limit") or sqlerr(__FILE__, __LINE__);

while ($arr_gal = mysql_fetch_assoc($res_gal)) {

$avatar = "";
$avatar = ($CURUSER["avatars"] == "yes" ? safechar($arr_gal["avatar"]) : "");
if (!$avatar)
$avatar = "/pic/default_avatar.gif";	

$added = $arr_gal["c_added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr_gal["c_added"]))) . " ago)";

if ($arr_gal['editedby'] > 0){
$res2 = mysql_query("SELECT username FROM users WHERE id=$arr_gal[editedby]");
$arr2 = mysql_fetch_assoc($res2);
$edited = "<p><hr align=left width=40%><font size=1 class=small>Last edited by <a href=userdetails.php?id=$arr_gal[editedby]><b>$arr2[username]</b></a> at $arr_gal[editedat] GMT</font></p>";
}
 
//=======change colors
$count2= (++$count2)%2;
$class = 'clearalt'.($count2==0?'6':'7');
echo"<tr><td class=colhead width=900 colspan=2><b>#$arr_gal[id]</b>$spacer by: <a class=altlink href=userdetails.php?id=$arr_gal[uid]>$arr_gal[username]</a>".
" $spacer at: $added $spacer$spacer ".($arr_gal['uid'] === $CURUSER['id'] || get_user_class() >= UC_MODERATOR ? "[ <a class=altlink href=?delete_comment=$arr_gal[id]&photo_id=$photo_id>delete</a> ] $spacer [ <a class=altlink href=?edit_comment=$arr_gal[id]&photo_id=$photo_id&sure=1>edit</a> ]" : "")."".
" ".(get_user_class() >= UC_MODERATOR && $arr_gal[editedby] > 0 ? "$spacer [ <a class=altlink href=\"#\" onclick=\"openTable('v_o$arr_gal[id]'); return false;\">".
"view original</a> ]" : "")." $spacer [ <a class=altlink href=sendmessage.php?receiver=$arr_gal[uid]>pm</a> ]".
"</td></tr><tr><td class=$class width=80><img width=80 src=$avatar></td><td class=$class valign=top>".format_comment($arr_gal["text"])."$edited".
"<table id=v_o$arr_gal[id] style=\"display: none;\" width=100%><tr><td><hr><font color=red>original comment:<hr></font>".format_comment($arr_gal["ori_text"])."</td></tr></table></td></tr>";
$edited = '';
}
echo '</td></tr></table></td></tr></table>'; 
on_select_gallery_change();
stdfoot();
end_table();
die();
}

//=== other members gallery pages
if ($_GET['member_gallery']){
?>
<style type="text/css">

#popitmenu{
position: absolute;
background-color: white;
border:1px solid black;
font: normal 12px Verdana;
line-height: 18px;
z-index: 100;
visibility: hidden;
}

#popitmenu a{
text-decoration: none;
padding-left: 6px;
color: black;
display: block;
}

#popitmenu a:hover{ /*hover background color*/
background-color: #CCFF9D;
}

</style>
<script language = "Javascript">
<!-- 

var form='all_my_gal'

function SetChecked(val,chkName) {
dml=document.forms[form];
len = dml.elements.length;
var i=0;
for( i=0 ; i<len ; i++) {
if (dml.elements[i].name==chkName) {
dml.elements[i].checked=val;
}
}
}

// -->
</script>
<?
$member_id = sqlesc(0 + $_GET['member_gallery']);

//=== get friends
$res_pals = mysql_query("SELECT id FROM friends WHERE $CURUSER[id] = friendid AND $member_id = userid") or sqlerr();
$arr_pals = mysql_num_rows($res_pals);

if ($arr_pals > 0)
$where .= "WHERE p_g.user_id=$member_id AND m_g.share_gallery !='private'";
else
$where .= "WHERE p_g.user_id=$member_id AND m_g.share_gallery = 'public'";

//=== if a gallery is selected...
if ($_GET["gallery"]){
$where .= "AND p_g.in_gallery =".sqlesc(( 0 + $_GET['gallery']));
$get_gallery = (0 + $_GET['gallery']);
}

$page = 0 + $_GET['page'];

$res = mysql_query("SELECT p_g.*, m_g.share_gallery, m_g.gallery_name FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id $where") or sqlerr(__FILE__, __LINE__);
$arr = mysql_num_rows($res);
$gallery_name = mysql_fetch_assoc($res);

$pages = floor($arr / $perpage);
if ($pages * $perpage < $arr)
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "<b>$i</b>\n";
  else
    $pagemenu .= "<a class=altlink href=?member_gallery=".(0 + $_GET['member_gallery'])."&gallery=$get_gallery&$q&page=$i><b>$i</b></a>\n";

if ($page == 1)
  $browsemenu .= "<b><img src=/pic/arrow_prev.gif =alt=\"&lt;&lt;\"> Prev</b>";
else
  $browsemenu .= "<a class=altlink href=?member_gallery=".(0 + $_GET['member_gallery'])."&gallery=$get_gallery&" . ($page - 1) . "><b><img src=/pic/arrow_prev.gif =alt=\"&lt;&lt;\"> Prev</b></a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;$pagemenu&nbsp;&nbsp;&nbsp;";

if ($page == $pages || $pages < 1)
  $browsemenu .= "<b>Next <img src=/pic/arrow_next.gif =alt=\"&gt;&gt;\"></b>";
else
  $browsemenu .= "<a class=altlink href=?member_gallery=".(0 + $_GET['member_gallery'])."&gallery=$get_gallery&page=" . ($page + 1) . "><b>Next <img src=/pic/arrow_next.gif =alt=\"&gt;&gt;\"></b></a>";

$offset = ($page * $perpage) - $perpage;

$res_name = mysql_query("SELECT username FROM users WHERE id=$member_id LIMIT 1") or sqlerr(__FILE__, __LINE__);
$arr_name = mysql_fetch_assoc($res_name);
$name = $arr_name['username'];

////////
if ($arr_pals > 0)
$res_gal2 = mysql_query("SELECT * FROM my_gallerys WHERE share_gallery != 'private' AND user_id = $member_id ORDER BY gallery_name") or sqlerr(__FILE__, __LINE__);
else 
$res_gal2 = mysql_query("SELECT * FROM my_gallerys  WHERE share_gallery = 'public' AND user_id = $member_id ORDER BY gallery_name") or sqlerr(__FILE__, __LINE__);

while ($arr_gal2 = mysql_fetch_assoc($res_gal2))
$gal_name2 .= "• <span class=small><a class=altlink href=?member_gallery=".(0 + $_GET['member_gallery'])."&gallery=$arr_gal2[id]>".safechar($arr_gal2['gallery_name'])."</a></span>".($arr_gal2["share_gallery"] == 'friends' ? "<img src=/pic/buddylist.gif title=\"Friends only gallery\">" : '')." </span>";
////////////////////////

//=== make the page
stdhead("Photo Gallery");
begin_table();

//=== try to fool firefox
$no_cache = "?nocache=".time();

echo "<p><b><a class=altlink href=userdetails.php?id=".(0 + $_GET['member_gallery']).">$name's</a> Photo Gallery</b><br><br></p>$page_links".($_GET["deleted"] == '1' ? "<h1>Image deleted</h1>" : "").($_GET["rated"] == '1' ? "<h1>Image rated</h1>" : "")."".
"$browsemenu<br><p>$name's other galleries:: $gal_name2</p><table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=$num_rows>Gallery :: ".safechar($gallery_name[gallery_name])." $spacer<span class=small>[ $arr images]</span></td></tr>".
"<form action=?delete_or_edit=1 method=post name=all_my_gal>";
$c2 = 0;
$c = 1;
$res_gal = mysql_query("SELECT p_g.*, m_g.share_gallery FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id $where LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
while ($arr_gal = mysql_fetch_assoc($res_gal)) {
         if ($c = $num_rows)
		 $c = 1;
		 if ($c2 % $num_rows==0)
            echo'<tr>';
			
			$image = array_values(getimagesize("bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location'])).""));
			list($width, $height) = $image;
			$image_size = "bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."";
			
			//=== get image rating
			if ($arr_gal["numratings"] != 0)
			$rating =  ROUND($arr_gal["ratingsum"] / $arr_gal["numratings"], 1);
			$rpic = ratingpic_image($rating);
			$test = ($c2 % $num_rows==0);
            echo"<td align=center border=4 valign=bottom><a class=altlink href=?info=$arr_gal[id] title=\"Click for image details page\">".
			"<img src=bitbucket/thumbs/".safechar(str_replace(" ", "%20", $arr_gal['location']))."$no_cache><br><br>".($arr_gal["numratings"] != 0 ? "rating: $rpic<br>" : "")."<b>".safechar($arr_gal['name'])."</a></b><br>".
			"$width x $height [ ".mksize(filesize($image_size))." ]".
			"<span class=small>".($CURUSER['id'] === $arr_gal['user_id'] || get_user_class() >= UC_MODERATOR ? "<br>[ <input type=checkbox name=\"edit_image[]\" value=$arr_gal[id]> edit ] ".
			" [ <input type=checkbox name=\"delete_image[]\" value=$arr_gal[id] /> delete ]" : "")."</span>".
			"<br><span class=small>added: $arr_gal[added]</span><br><br></td>";
            $c2++;
			$c++;
        if ($c2 % $num_rows==0 && $c = $num_rows)
            echo'</tr>';
      }
if ($c < $num_rows){
while ($c < $num_rows){
echo '<td align=center valign=bottom></td>';
$c++;
}
echo '</tr>';
}

echo "<tr><td align=center class=clearalt6 colspan=$num_rows>".
"".($CURUSER['id'] === $arr_gal['user_id'] || get_user_class() >= UC_MODERATOR ? "<a class=altlink href=\"javascript:SetChecked(1,'delete_image[]')\" onclick=\"javascript:SetChecked(0,'edit_image[]')\">select all delete</a> - ".
"<a class=altlink href=\"javascript:SetChecked(0,'delete_image[]')\">un-select all delete</a>$spacer $spacer".
"<a class=altlink href=\"javascript:SetChecked(1,'edit_image[]')\" onclick=\"javascript:SetChecked(0,'delete_image[]')\">select all edit</a> - ".
"<a class=altlink href=\"javascript:SetChecked(0,'edit_image[]')\">un-select all edit</a>".
"<br><br><input class=button type=submit name=delete value=\"delete selected images\">$spacer $spacer".
"<input class=button type=submit name=edit value=\"edit selected images\"></form>" : "Gallery :: ".safechar($gallery_name['gallery_name'])." $spacer<span class=small>[ $arr images]</span>")."</td></tr><table><br>$browsemenu<br>"; 

on_select_gallery_change();
stdfoot();
end_table();
die();
}
	
//=== members gallery pages
if ($_GET['my_gallery']){
?>
<style type="text/css">

#popitmenu{
position: absolute;
background-color: white;
border:1px solid black;
font: normal 12px Verdana;
line-height: 18px;
z-index: 100;
visibility: hidden;
}

#popitmenu a{
text-decoration: none;
padding-left: 6px;
color: black;
display: block;
}

#popitmenu a:hover{ /*hover background color*/
background-color: #CCFF9D;
}

</style>
<script language = "Javascript">
<!-- 

var form='all_my_gal'

function SetChecked(val,chkName) {
dml=document.forms[form];
len = dml.elements.length;
var i=0;
for( i=0 ; i<len ; i++) {
if (dml.elements[i].name==chkName) {
dml.elements[i].checked=val;
}
}
}

// -->
</script>
<?
$where .= "WHERE p_g.user_id=".sqlesc($CURUSER['id']);
//=== if a gallery is selected...
if ($_GET["gallery"]){
$where .= " AND p_g.in_gallery =".sqlesc(( 0 + $_GET["gallery"]));
$get_gallery = "&gallery=".( 0 + $_GET["gallery"]);
}

$page = 0 + $_GET['page'];

$res = mysql_query("SELECT p_g.*, m_g.share_gallery, m_g.gallery_name FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id $where") or sqlerr(__FILE__, __LINE__);
$arr = mysql_num_rows($res);
$name = mysql_fetch_assoc($res);

$pages = floor($arr / $perpage);
if ($pages * $perpage < $arr)
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "<b>$i</b>\n";
  else
    $pagemenu .= "<a class=altlink href=?my_gallery=1$get_gallery&$q&page=$i><b>$i</b></a>\n";

if ($page == 1)
  $browsemenu .= "<b><img src=/pic/arrow_prev.gif =alt=\"&lt;&lt;\"> Prev</b>";
else
  $browsemenu .= "<a class=altlink href=?my_gallery=1$get_gallery&" . ($page - 1) . "><b><img src=/pic/arrow_prev.gif =alt=\"&lt;&lt;\"> Prev</b></a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;$pagemenu&nbsp;&nbsp;&nbsp;";

if ($page == $pages || $pages < 1)
  $browsemenu .= "<b>Next <img src=/pic/arrow_next.gif =alt=\"&gt;&gt;\"></b>";
else
  $browsemenu .= "<a class=altlink href=?my_gallery=1$get_gallery&page=" . ($page + 1) . "><b>Next <img src=/pic/arrow_next.gif =alt=\"&gt;&gt;\"></b></a>";

$offset = ($page * $perpage) - $perpage;

//=== make the page
stdhead("Photo Gallery");
begin_table();

//=== try to fool firefox
$no_cache = "?nocache=".time();

echo "<p><b><a class=altlink href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]'s</a> Photo Gallery</b><br><br></p>$page_links".($_GET["deleted"] == '1' ? "<h1>Image deleted</h1>" : "").($_GET["rated"] == '1' ? "<h1>Image rated</h1>" : "").($_GET["edited"] == '1' ? "<h1>Image edited</h1>" : "")."".
"$browsemenu<br><br>my galleries: $gal_name<table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=$num_rows>".(!$_GET["gallery"] ? "My Gallerys " : "My Gallery :: ".safechar($name['gallery_name'])."")." $spacer<span class=small>[ $arr images]</span></td></tr>".
"<form action=?delete_or_edit=1 method=post name=all_my_gal>";
$c2 = 0;
$c = 1;
$res_gal = mysql_query("SELECT p_g.*, m_g.share_gallery FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id $where LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
while ($arr_gal = mysql_fetch_assoc($res_gal)) {

         if ($c = $num_rows)
		 $c = 1;
		 if ($c2 % $num_rows==0)
            echo'<tr>';
			
			$image = array_values(getimagesize("bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location'])).""));
			list($width, $height) = $image;
			$image_size = "bitbucket/".safechar(str_replace(" ", "%20", $arr_gal['location']))."";
			
			//=== get image rating
			if ($arr_gal["numratings"] != 0)
			$rating =  ROUND($arr_gal["ratingsum"] / $arr_gal["numratings"], 1);
			$rpic = ratingpic_image($rating);
			$test = ($c2 % $num_rows==0);
            echo"<td align=center border=4 valign=bottom><a class=altlink href=?info=$arr_gal[id] title=\"Click for image details page\">".
			"<img src=bitbucket/thumbs/".safechar(str_replace(" ", "%20", $arr_gal['location']))."$no_cache><br><br>".($arr_gal["numratings"] != 0 ? "rating: $rpic<br>" : "")."<b>".safechar($arr_gal['name'])."</a></b><br>".
			"$width x $height [ ".mksize(filesize($image_size))." ]".
			"<span class=small>".($CURUSER['id'] === $arr_gal['user_id'] || get_user_class() >= UC_MODERATOR ? "<br>[ <input type=checkbox name=\"edit_image[]\" value=$arr_gal[id]> edit ] ".
			" [ <input type=checkbox name=\"delete_image[]\" value=$arr_gal[id] /> delete ]" : "")."</span>".
			"<br><span class=small>added: $arr_gal[added]</span><br><br></td>";
            $c2++;
			$c++;
        if ($c2 % $num_rows==0 && $c = $num_rows)
            echo'</tr>';
      }
if ($c < $num_rows){
while ($c < $num_rows){
echo '<td align=center valign=bottom></td>';
$c++;
}
echo '</tr>';
}

echo "<tr><td align=center class=clearalt6 colspan=$num_rows><a class=altlink href=\"javascript:SetChecked(1,'delete_image[]')\" onclick=\"javascript:SetChecked(0,'edit_image[]')\">select all delete</a> - ".
"<a class=altlink href=\"javascript:SetChecked(0,'delete_image[]')\">un-select all delete</a>$spacer $spacer".
"<a class=altlink href=\"javascript:SetChecked(1,'edit_image[]')\" onclick=\"javascript:SetChecked(0,'delete_image[]')\">select all edit</a> - ".
"<a class=altlink href=\"javascript:SetChecked(0,'edit_image[]')\">un-select all edit</a>".
"<br><br><input class=button type=submit name=delete value=\"delete selected images\">$spacer $spacer".
"<input class=button type=submit name=edit value=\"edit selected images\"></form></td></tr><table><br>$browsemenu<br>"; 

on_select_gallery_change();
stdfoot();
end_table();
die();
}

//=== enter the images into the DB with user ID names and comments
if ($_GET['name_images'])	{

$added = sqlesc(get_date_time());
$image= $_POST['image'];

foreach ($image as $key => $add_it) {

$location = $_POST['location'];
$pic_comment = $_POST['pic_comment'];
$add_to_gallery = $_POST['add_to_gallery'];

$name = mysql_real_escape_string(safechar($add_it));
$location = sqlesc(str_replace(" ", "_", safechar($location[$key])));
if ($pic_comment[$key] != '')
$pic_comment = sqlesc($pic_comment[$key]);
else
$no_comment = 1;
$add_to_gallery = sqlesc(0 + $add_to_gallery[$key]);

mysql_query("INSERT INTO photo_gallery (user_id, name, location, in_gallery, added) VALUES(".sqlesc($CURUSER["id"]).", '$name', $location, $add_to_gallery, $added)") or sqlerr(__FILE__, __LINE__);
$photo_gallery = mysql_insert_id();
if(!$no_comment)
mysql_query("INSERT INTO comments (user, text, photo_gallery, added) VALUES(".sqlesc($CURUSER["id"]).", $pic_comment, $photo_gallery, $added)") or sqlerr(__FILE__, __LINE__);
}
stderr("Sucess!", "<center>all images have been placed. Would you like to <br><br> ".
"<a class=altlink href=photo_gallery.php?my_gallery=1>view your gallerys</a> | <a class=altlink href=photo_gallery.php?public_gallerys=1>view all galleries</a>  | ".  
"<a class=altlink href=photo_gallery.php?manage_gallerys=1>manage gallerys</a> | <a class=altlink href=photo_gallery.php?upload=1>upload images</a>".
"".((get_user_class() >= UC_ADMINISTRATOR) ? " | <a class=altlink href=/photo_gallery.php?gallery_admin=1>gallery admin</a>" : "")." <br><br>my galleries: $gal_name</center>");

}

//===  if post
if ($_SERVER["REQUEST_METHOD"] === "POST"){ //=== must change this to something like if get upload_images or something :P

if ($count >= $number_total)
stderr("Error", "You have reached the max number of images to upload. <br>Your total number of allowed images are  ".
"<b>$number_total</b> and you have uploaded <b>$count</b>$page_links");

$image_count = 0; 
$size_error_count = 0;
$file_exists_error_count = 0;
while(list($key,$value) = each($_FILES[images][name]))
{
if(!empty($value)){

$value=repstr($value);
//=== add some random numbers to the file name
$rand_num= rand(0,99999); 
$value = str_replace(".", "$rand_num.", $value);

$filename = safechar($value); 
$add = "bitbucket/$filename"; 
$file_type = $_FILES[images][type][$key];  

//===== all accepted file types check 1 (simple, just checks the extention...)
$accepted_filetypes_simple  = array('.jpeg', '.jpg', '.gif', '.png');
$i = strrpos($value, ".");
if ($i !== false){
$ext = strtolower(substr($value, $i));
if(!in_array($ext, $accepted_filetypes_simple))
stderr("Error", "Image MUST be in jpg, gif or png format.");
}

//===== all accepted file types check 2 
$accepted_filetypes  = array('image/pjpeg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
if(!in_array($file_type, $accepted_filetypes))
stderr("Error", "Image MUST be in jpg, gif or png format.");

//=== get image size 
$file_size = $_FILES[images][size][$key];
//=== error if file too big
if ($file_size > $max_file_size){
$size_error_count = + 1;
$size_error[] = array ('image'  => $filename, 'size' => $file_size);
}
elseif (file_exists($add)){
$file_exists_error_count = + 1;
$file_exists_error[] = array ('image'  => $filename);
}
else {
copy($_FILES[images][tmp_name][$key], $add);  
chmod("$add",0777); 

if($filename)
$image_count = (++$image_count);

//=== make thumbnails
$thumb_dir="bitbucket/thumbs/$filename"; 
$img_size = getImageSize("bitbucket/$filename");

	switch ( $img_size[2] ) {
            case IMAGETYPE_GIF:
                $im=ImageCreateFromGIF($add);
            break;
            case IMAGETYPE_JPEG:
                $im=ImageCreateFromJPEG($add);
            break;
            case IMAGETYPE_PNG:
                $im=ImageCreateFrompng($add);
            break;
            default:
                return false;
        }

$width=ImageSx($im); 
$height=ImageSy($im); 
//=== lets do some math :P
if ($height > 99 || $width > 99){
if ($height >= $width){
$thumb_height=100; 
$thumb_width = ($thumb_height / $height * $width);
}
else {
$thumb_width=100;
$thumb_height = ($thumb_width / $width * $height);
}
}
else {
$thumb_width=$width;
$thumb_height =$height;
}

        $newimage = imagecreatetruecolor( $thumb_width,$thumb_height );
		imagecolortransparent($newimage, imagecolorallocatealpha($newimage, 0, 0, 0, 127) );
        imagealphablending($newimage, false);
        imagesavealpha($newimage, true);
        imagecopyresampled($newimage, $im,0,0,0,0,$thumb_width,$thumb_height,$width,$height);

	switch ( $img_size[2] ) {
            case IMAGETYPE_GIF:
                ImageGIF($newimage,$thumb_dir); 
            break;
            case IMAGETYPE_JPEG:
                ImageJPEG($newimage,$thumb_dir);
            break;
            case IMAGETYPE_PNG:
                ImagePNG($newimage,$thumb_dir);
            break;
            default:
                return false;
        }  

chmod("$thumb_dir",0777);
 
}
}//=== end if image exists

//====  let's put all the info into an array, thanks Laffin! :P
if (!empty($value))
$image_info[] = array ( 'location' => $thumb_dir, 'image'  => $filename, 'size' => $file_size, 'width' => $width, 'height' => $height);
}//=== end while

$res_select = mysql_query("SELECT * FROM `my_gallerys` WHERE `user_id` =$CURUSER[id]") or sqlerr(__FILE__, __LINE__);
$list_select = "";
while ($arr_select = mysql_fetch_assoc($res_select))
$list_select .= "<option value='$arr_select[id]'>".$arr_select["gallery_name"]."</option>";

//=== if they don't have any galleries yet make them one!
if (mysql_num_rows($res_select) === 0){
$list_select = "";
mysql_query("INSERT INTO my_gallerys (user_id, gallery_name, share_gallery) VALUES(".sqlesc($CURUSER["id"]).", 'Default', 'public')") or sqlerr(__FILE__, __LINE__);
$gallery_id = mysql_insert_id();
$list_select .= "<option value=$gallery_id>Default</option>";
}

stdhead("Names and comments");
begin_table(); 
echo "<form method=post action=?name_images=1 enctype='multipart/form-data'><p><b>$SITENAME Photo Gallery</b></p>$page_links<br>".
"<table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=3>Photo Gallery  upload</td></tr>";

if ($image_count > 0){ 
echo"<tr><td class=clearalt6 colspan=2 align=center><br>If you wish to give your images titles, or add comments to them, ".
"do it now. If not just click the add to gallery button below.<br></td></tr>";
$button = "<tr><td class=clearalt7 align=center colspan=2><input type=submit value=\"add to gallery\" class=button><br><br></td></tr>";
}

$count_image = count($image_info);
for ($i=0;$i<$count_image;$i++) {
$image_size = "<br>".mksize($image_info[$i]['size'])."<br> ".$image_info[$i]['width']." x ".$image_info[$i]['height'];
//=== set stuff for loop
if($image_info[$i]['width'] > 0)
echo"<tr><td class=clearalt7 align=center valign=middle width=120><img src=\"".safechar($image_info[$i]['location'])."\">$image_size</td><td class=clearalt7 align=left valign=middle>".
"<b>Add to gallery:</b> <select name=\"add_to_gallery[]\">$list_select</select>".
"<br><br><b>Image title:</b><br><input type=text name=\"image[]\" value=\"".safechar($image_info[$i]['image'])."\" size=60> [ default is the image name ]<br><br><b>Comment:</b><br>".
"<textarea name=\"pic_comment[]\" cols=80 rows=5></textarea> [ BBcode is ok ]<br><input type=hidden name=\"location[]\" value=\"".safechar($image_info[$i]['image'])."\"></td></tr>";
}

//=== file_exists_error
if ($file_exists_error_count > 0){
$file_exists_error_count = count($file_exists_error);
echo"<tr><td class=clearalt6 align=center valign=middle width=120><h1>File name<br> <img src=pic/warned8.gif> error! <img src=pic/warned8.gif></h1></td><td class=clearalt6 align=left valign=middle><br>".
"The following file".($file_exists_error_count > 1 ? "s were" : " was")." <b>not</b> uploaded!<br>";

for ($i=0;$i<$file_exists_error_count;$i++) {
echo "<br><b>".safechar($file_exists_error[$i]['image'])."</b>";
}
echo"".($file_exists_error_count > 1 ? "<br><br> Files with the same names exist on the server. Re-name the files and try again." : "<br><br> a file with that name exists on the server. Re-name the file and try again.")."<br><br><br></td></tr>";
}//=== end file exists error

//=== file too big error //== not totally tested...
if ($size_error_count > 0){
$size_error_count = count($size_error);
echo"<tr><td class=clearalt6 align=center valign=middle width=120><h1>File size<br> <img src=pic/warned8.gif> error! <img src=pic/warned8.gif></h1></td><td class=clearalt6 align=left valign=middle><br>".
"The following file".($size_error_count > 1 ? "s were" : " was")." <b>not</b> uploaded!<br>";

for ($i=0;$i<$size_error_count;$i++) {
echo "<br><b>".safechar($size_error[$i]['image'])."</b> size was <b>".mksize($size_error[$i]['size'])."</b>";
}
echo"<br><br>Maximum file size is:<b> ".mksize($max_file_size).".</b><br><br><br></td></tr>";
}//=== end file too big error

echo $button;

echo'</table></form>';
on_select_gallery_change();
stdfoot();
end_table();
die();
}//=== end if $_POST

if ($_GET['upload']){
//=== the upload page 
stdhead("Photo Gallery upload");
begin_table();

echo "<form method=post action=".$_SERVER['PHP_SELF']." enctype='multipart/form-data'><p><b>$SITENAME Photo Gallery</b>$page_links<br>my galleries: $gal_name".
"<table border=1 cellspacing=0 cellpadding=5 width=737><tr><td class=colhead colspan=2>Photo Gallery  upload</td></tr><tr><td class=clearalt6 colspan=2 align=center>".
"<br>".($number_total == 0 ? "<p>Sorry, you do not have access to this feature</p><br><br></td></tr>" : "<p>The maximum file size to upload is: <b>".mksize($max_file_size).".</b> [ per file ]<br>".
"<br>Allowed formats are <b>.jpg .gif .png</b><br><br>As a <b>".get_user_class_name($CURUSER['class'])."</b> you may upload up to <b>$number_of_pics</b> images at a time. ".
"<br>You may also have up to <b>$number_total</b> images in the gallery.<br>Currently you have <b>$count</b> images in the gallery.<br><br>Before uploading anything please read the <a class=altlink href=/rules.php><b>Rules</b>".
"</a> and <a class=altlink href=/faq.php><b>FAQ</b></a></p><br></td></tr>")."";
for($i=1; $i<=$number_of_pics; $i++){
echo "<tr><td class=clearalt6 align=right><b>Upload photo $i:</b></td><td class=clearalt6 align=left><input type=file name='images[]'  size=60></td></tr>";
}

echo "<tr><td colspan=2 align=center class=clearalt6><input type=submit value=Upload class=button></td></tr>".
"</form></table><p><table class=main width=410 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>".
"<font class=small><b>Disclaimer:</b> Do not upload unauthorized or illegal pictures. Please see the ".
"<a class=altlink href=/faq.php><b>FAQ</b></a> for details.</font></td></tr></table>";

on_select_gallery_change();
stdfoot();
end_table();
die();
}

//=== list public galleries
$search = trim($HTTP_GET_VARS['search']);

if ($search = '*')
{
  $query = "u.status='confirmed' AND p_g.user_id > '0'";
  
	if ($search)
		  $q = "search=" . safechar($search);
}
elseif ($_GET["letter"])
{
	$letter = trim($_GET["letter"]);
  if (strlen($letter) > 1)
    die;

  if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
    $letter = "a";
  $query = "username LIKE '$letter%' AND u.status='confirmed' AND p_g.user_id > '0'";
  $q = "letter=$letter";
}
else
{
  $query = "u.username LIKE " . sqlesc("%$search%") . " AND u.status='confirmed' AND p_g.user_id > '0'";
  
	if ($search)
		  $q = "search=" . safechar($search);
}

stdhead("Members public galleries");

echo("<h1>Members public galleries</h1>$page_links<form method=get action=?>\n".
"<b>Search:</b> <input type=text size=30 name=search>\n".
"<input class=button type=submit value='Search!'></form><p>\n");

for ($i = 97; $i < 123; ++$i){
	$l = chr($i);
	$L = chr($i - 32);
	if ($l == $letter)
    echo("<b>$L</b>\n");
	else
    echo("<a href=?letter=$l><b>$L</b></a>\n");
}

echo("</p>\n");
  
$page = 0 + $_GET['page'];

$res = mysql_query("SELECT DISTINCT u.id AS uid FROM users AS u LEFT JOIN photo_gallery AS p_g ON u.id = p_g.user_id  WHERE $query");
$arr = mysql_num_rows($res);
$pages = floor($arr / $perpage);
if ($pages * $perpage < $arr)
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "<b>$i</b>\n";
  else
    $pagemenu .= "<a class=altlink href=?$q&page=$i><b>$i</b></a>\n";

if ($page == 1)
  $browsemenu .= "<b><img src=/pic/arrow_prev.gif =alt=\"&lt;&lt;\"> Prev</b>";
else
  $browsemenu .= "<a class=altlink href=?$q&page=" . ($page - 1) . "><b><img src=/pic/arrow_prev.gif =alt=\"&lt;&lt;\"> Prev</b></a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;$pagemenu&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "<b>Next <img src=/pic/arrow_next.gif =alt=\"&gt;&gt;\"></b>";
else
  $browsemenu .= "<a class=altlink href=?$q&page=" . ($page + 1) . "><b>Next <img src=/pic/arrow_next.gif =alt=\"&gt;&gt;\"></b></a>";

$offset = ($page * $perpage) - $perpage;

$res = mysql_query("SELECT DISTINCT u.id AS uid, u.username, u.donor, u.added, u.last_access, u.class, u.country, u.avatar FROM users AS u LEFT JOIN photo_gallery AS p_g ON u.id = p_g.user_id WHERE $query ORDER BY username LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);

if($num > 0){
echo("<p>$browsemenu</p>");
echo("<table border=1 cellspacing=0 cellpadding=5>\n".
"<tr><td class=colhead></td><td class=colhead align=left>Member name</td><td class=colhead align=left>Gallerys</td><td class=colhead>Registered</td>".
"<td class=colhead>Last access</td><td class=colhead align=left>Class</td><td class=colhead>Country</td></tr>\n");
for ($i = 0; $i < $num; ++$i){	
$count= (++$count)%2;
$class = 'clearalt'.($count==0?'6':'7');
 
  $arr = mysql_fetch_assoc($res);
  
  if ($arr['country'] > 0){
    $cres = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]");
    if (mysql_num_rows($cres) == 1){
      $carr = mysql_fetch_assoc($cres);
      $country = "<td style='padding: 0px' align=center class=$class><img src=pic/flag/$carr[flagpic] alt=\"$carr[name]\"></td>";
    }
  }
  else
    $country = "<td align=center class=$class>---</td>";
  if ($arr['added'] == '0000-00-00 00:00:00')
    $arr['added'] = '-';
  if ($arr['last_access'] == '0000-00-00 00:00:00')
    $arr['last_access'] = '-';
	
$avatar = safechar($arr['avatar']);
if (!$avatar)
$avatar = 'pic/default_avatar.gif';

//=== get friends
$res_pals = mysql_query("SELECT id FROM friends WHERE $CURUSER[id] = friendid AND $arr[uid] = userid") or sqlerr(__FILE__, __LINE__);
$arr_pals = mysql_num_rows($res_pals);
$gal_name = '';
if ($arr_pals > 0)
$res_gal = mysql_query("SELECT DISTINCT p_g.in_gallery, m_g.gallery_name, m_g.share_gallery FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id WHERE m_g.share_gallery != 'private' AND m_g.user_id = $arr[uid] ORDER BY m_g.gallery_name") or sqlerr(__FILE__, __LINE__);
else			
$res_gal = mysql_query("SELECT DISTINCT p_g.in_gallery, m_g.gallery_name  FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id WHERE m_g.share_gallery = 'public' AND m_g.user_id = $arr[uid] ORDER BY m_g.gallery_name") or sqlerr(__FILE__, __LINE__);
while ($arr_gal = mysql_fetch_assoc($res_gal))
$gal_name .= "• <span class=small><a class=altlink href=?member_gallery=$arr[uid]&gallery=$arr_gal[in_gallery]>".safechar($arr_gal['gallery_name'])."</a></span>".($arr_gal["share_gallery"] == 'friends' ? "<img src=/pic/buddylist.gif title=\"Friends only gallery\">" : '')."<br>";

  echo("<tr><td class=$class><img src=$avatar width=30></td><td align=left class=$class><a class=altlink href=userdetails.php?id=$arr[uid]><b>$arr[username]</b></a>" .($arr["donated"] > 0 ? "<img src=/pic/star.gif border=0 alt='Donor'>" : "")."</td>" .
  "<td class=$class>$gal_name</td><td class=$class>$arr[added]</td><td class=$class>$arr[last_access]</td>".
    "<td align=left class=$class>" . get_user_class_name($arr["class"]) . "</td>$country</tr>\n");
}
echo("</table><p>$browsemenu</p>\n");
}
else
echo '<p>nothing found. select a letter or enter a username.</p>';
on_select_gallery_change();
stdfoot();
die;
?>
