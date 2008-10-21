<?php
require "../include/bittorrent.php";
dbconn(false);

session_cache_limiter( 'nocache' );
session_start( );
header( 'Cache-Control: no-cache, must-revalidate, post-check=3600, pre-check=3600' );

function rotateImg($sourcefile, $targetfile, $degrees)
{
    $img_size = getImageSize($sourcefile);
    $x = $img_size[0];
    $y = $img_size[1];
    if ($x > $y)
    {
            $dst_x = 0;
            $dst_y = $x - $y;
            $newd = $x;
    }
    else
    {
            $dst_x = $y - $x;
            $dst_y = 0;
            $newd = $y;
    }

	switch ( $img_size[2] ) {
            case IMAGETYPE_GIF:
                $src_img = imagecreatefromGIF($sourcefile);
            break;
            case IMAGETYPE_JPEG:
                $src_img = ImageCreateFromJPEG($sourcefile);
            break;
            case IMAGETYPE_PNG:
                $src_img = imagecreatefromPNG($sourcefile);
            break;
            default:
                return false;
        }

    if ((($x > y) && ($degrees < 180)) || (($y > $x) && ($degrees > 180))){
		$dst_img = ImageCreateTrueColor($newd,$newd);
		imagecolortransparent($dst_img, imagecolorallocatealpha($dst_img, 0, 0, 0, 127) );
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
		ImageCopyResampled($dst_img,$src_img,0,0,0,0,$x,$y,$x,$y);
		}
    else {
		$dst_img = ImageCreateTrueColor($newd,$newd);
		imagecolortransparent($dst_img, imagecolorallocatealpha($dst_img, 0, 0, 0, 127) );
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
        ImageCopyResampled($dst_img,$src_img,$dst_x,$dst_y,0,0,$x,$y,$x,$y);
		}
    
    $rotated_img = ImageRotate($dst_img, $degrees,0);

    if ($degrees != 180) {
        $final_img = ImageCreateTrueColor($y,$x);
		imagecolortransparent($final_img, imagecolorallocatealpha($final_img, 0, 0, 0, 127) );
        imagealphablending($final_img, false);
        imagesavealpha($final_img, true);
        ImageCopyResampled($final_img,$rotated_img,0,0,0,0,$y,$x,$y,$x);
    }else{
        $final_img = imagecreatetruecolor( $x,$y );
		imagecolortransparent($final_img, imagecolorallocatealpha($final_img, 0, 0, 0, 127) );
        imagealphablending($final_img, false);
        imagesavealpha($final_img, true);
        imagecopyresampled($final_img, $rotated_img, 0, 0, 0, 0,$x,$y,$x,$y);
    }
	switch ( $img_size[2] ) {
            case IMAGETYPE_GIF:
                ImageGIF($final_img, $targetfile); 
            break;
            case IMAGETYPE_JPEG:
                ImageJPEG($final_img, $targetfile); 
            break;
            case IMAGETYPE_PNG:
                ImagePNG($final_img, $targetfile); 
            break;
            default:
                return false;
        }     
}

if (!function_exists('mime_content_type'))
{
   function mime_content_type ($f)
   {
       return trim (exec('file -bi '.escapeshellarg($f)));
   }
}

//=== smart image resize but Maxim Chernyak 
//=== http://www.mediumexposure.com/techblog/smart-image-resizing-while-preserving-transparency-php-and-gd-library
function smart_resize_image( $file, $width = 0, $height = 0, $proportional)
    {
        if ( $height <= 0 && $width <= 0 ) {
            return false;
        }
       
        $info = getimagesize($file);
        $image = '';
       
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
       
        if ($proportional) {
           
            $proportion = $width_old / $height_old;
           
            if ( $width > $height && $height != 0) {
                $final_height = $height;
                $final_width = $final_height * $proportion;
            }
            elseif ( $width < $height && $width != 0) {
                $final_width = $width;
                $final_height = $final_width / $proportion;
            }
            elseif ( $width == 0 ) {
                $final_height = $height;
                $final_width = $final_height * $proportion;           
            }
            elseif ( $height == 0) {
                $final_width = $width;
                $final_height = $final_width / $proportion;           
            }
            else {
                $final_width = $width;
                $final_height = $height;
            }
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
        }
   
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
            break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
            break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
            break;
            default:
                return false;
        }
       
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        imagecolortransparent($image_resized, imagecolorallocate($image_resized, 0, 0, 0) );
        imagealphablending($image_resized, false);
        imagesavealpha($image_resized, true);
       
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
       
            @unlink($file);
       
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $file);
            break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $file);
            break;
            case IMAGETYPE_PNG:
                imagepng($image_resized, $file);
            break;
            default:
                return false;
        }
       
        return true;
    }
	
//=== convert to greyscale
function convert_to_greyscale($sourcefile, $targetfile)
{
	$img_size = getImageSize($sourcefile);
    $x = $img_size[0];
    $y = $img_size[1];
	
		switch ( $img_size[2] ) {
            case IMAGETYPE_GIF:
                $src_img = imagecreatefromGIF($sourcefile);
            break;
            case IMAGETYPE_JPEG:
                $src_img = ImageCreateFromJPEG($sourcefile);
            break;
            case IMAGETYPE_PNG:
                $src_img = imagecreatefromPNG($sourcefile);
            break;
            default:
                return false;
        }
	
	for($i=0; $i<$y; $i++)
	{
		for($j=0; $j<$x; $j++)
		{
			$rgb = imagecolorat($src_img, $j, $i);
			$color_index = imagecolorsforindex($src_img, $rgb);
			$index = $color_index['red']*0.15 + $color_index['green']*0.5 + $color_index['blue']*0.35;
			$icr = imagecolorresolve($src_img, $index, $index, $index);
			imagesetpixel($src_img, $j, $i, $icr);
		}
	}
	imagejpeg($src_img,$targetfile,90);
}


$page_links = "<a class=altlink href=/photo_gallery.php?my_gallery=1>view your gallerys</a> | <a class=altlink href=/photo_gallery.php?public_gallerys=1>view all galleries</a> | ".  
"<a class=altlink href=/photo_gallery.php?manage_gallerys=1>manage gallerys</a> | <a class=altlink href=/photo_gallery.php?upload=1>upload images</a>".
"".((get_user_class() >= UC_ADMINISTRATOR) ? " | <a class=altlink href=/photo_gallery.php?gallery_admin=1>gallery admin</a>" : "")."";

$res_gal = mysql_query("SELECT DISTINCT p_g.in_gallery, m_g.gallery_name  FROM photo_gallery AS p_g LEFT JOIN my_gallerys AS m_g ON p_g.in_gallery = m_g.id WHERE m_g.user_id = $CURUSER[id] ORDER BY m_g.gallery_name") or sqlerr(__FILE__, __LINE__);
while ($arr_gal = mysql_fetch_assoc($res_gal))
$gal_name .= "• <span class=small><a class=altlink href=/photo_gallery.php?my_gallery=$CURUSER[id]&gallery=$arr_gal[in_gallery]>".htmlentities($arr_gal['gallery_name'])."</a></span>".($arr_gal["share_gallery"] == 'friends' ? "<img src=/pic/buddylist.gif title=\"Friends only gallery\">" : '')." </span>";

$photo_id = isset($_GET['image']) ? (int)$_GET['image'] : 0;
$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$imagename = htmlspecialchars($arr_gal['location']);

?>
<script language="Javascript">
   function PopupPic(sPicURL) {
     window.open( "/photo_popup.htm?"+sPicURL, "",  
     "resizable=1,HEIGHT=200,WIDTH=200");
   }
</script>   
<?

//=== resize image
if (isset($_GET['resize_image'])) {
$photo_id = isset($_GET['resize_image']) ? (int)$_GET['resize_image'] : 0;

$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$imagename = htmlspecialchars($arr_gal['location']);
$sourcefile = htmlentities("../bitbucket/$imagename"); 
$dest_file = htmlentities("../bitbucket/fuckme_".$imagename); 
$file_type = mime_content_type ($sourcefile);

		$image = array_values(getimagesize($sourcefile));
		list($width, $height) = $image;
		
if (isset($_GET['do_it'])) {

if (($_POST['image_resizer_width'] == 0) && ($_POST['image_resizer_hight'] == 0))
stderr("Error", "Nothing selected go <a class=altlink href=\"javascript: history.go(-1)\">back</a>.");
if (($_POST['image_resizer_width'] > 0) && ($_POST['image_resizer_hight'] > 0))
stderr("Error", "you can't select both width and hight! go <a class=altlink href=\"javascript: history.go(-1)\">back</a> and make a decision!");

if (isset($_POST['image_resizer_width']))
$image_resizer_width = isset($_POST['image_resizer_width']) ? (int)$_POST['image_resizer_width'] : 0;
if (isset($_GET['image_resizer_hight']))
$image_resizer_hight = isset($_POST['image_resizer_hight']) ? (int)$_POST['image_resizer_hight'] : 0;

smart_resize_image( $sourcefile, $image_resizer_width, image_resizer_hight, $proportional = true);
}

stdhead("resize image");	

echo"<p><b>edit images</b><br><br></p>$page_links<br><br>$gal_name".((0 + $_GET["do_it"]) == '1' ? "<h1>Image resized!</h1>" : "")."";	

//=== try to fool firefox
$no_cache = "?nocache=".time();

if ($width >= "500")
$show_image = "<a class=altlink href=\"javascript:PopupPic('$sourcefile$no_cache')\">".
"<img width=500 src=$sourcefile$no_cache title=\"click to open image full size in new window\"></a>".
"<span class=small><br><br>[ this image has been re-sized to fit this window. click the image to view full size ]</span>";
else
$show_image = "<a class=altlink href=\"javascript:PopupPic('$sourcefile$no_cache')\">".
"<img src=$sourcefile$no_cache title=\"click to open in new window\"></a>";

echo"<table width=90%><tr><td class=colhead align=center><h1>Image Resize: ".htmlentities($imagename)."</h1></td></tr>".
"<tr><td class=clearalt6 align=center valign=middle><br><br>$show_image<p><b>current size:</b> $width x $height [ ".mksize(filesize($sourcefile))." ]</p></td></tr>".
"<tr><td class=clearalt6 align=center>";
?>
<p><b>to resize your image:</b><br>select <i>either </i> the desired width <b>or</b> hight.<br>
the script will keep the image in proportion<br>
<!-- alternately you can fill in a custom size below. --></p>
<form method=post action=?resize_image=<?php echo $photo_id; ?>&do_it=1 enctype='multipart/form-data'>
<select name=image_resizer_width>
	<option value=0> • set width • </option>
	<option value=80> • set width 80 px • </option>
	<option value=100> • set width 100 px • </option>
	<option value=150> • set width 150 px • </option>
	<option value=200> • set width 200 px • </option>
	<option value=250> • set width 250 px • </option>
	<option value=300> • set width 300 px • </option>
	<option value=350> • set width 350 px • </option>
	<option value=400> • set width 400 px • </option>
	<option value=450> • set width 450 px • </option>
	<option value=500> • set width 500 px • </option>
	<option value=550> • set width 550 px • </option>
	<option value=600> • set width 600 px • </option>
</select> <b>or</b> <select name=image_resizer_hight>
	<option value=0> • set hight • </option>
	<option value=80> • set hight 80 px • </option>
	<option value=100> • set hight 100 px • </option>
	<option value=150> • set hight 150 px • </option>
	<option value=200> • set hight 200 px • </option>
	<option value=250> • set hight 250 px • </option>
	<option value=300> • set hight 300 px • </option>
	<option value=350> • set hight 350 px • </option>
	<option value=400> • set hight 400 px • </option>
	<option value=450> • set hight 450 px • </option>
	<option value=500> • set hight 500 px • </option>
	<option value=550> • set hight 550 px • </option>
	<option value=600> • set hight 600 px • </option>
</select>
<!-- if you wish to have a user input for width OR height, uncomment the following -->
<!-- <br><br>
<b>width:</b> <input type=text name=image_resizer_hight size=4 maxlength=4> <b>or</b> <b>hight:</b> <input type=text name=image_resizer_width size=4 maxlength=4>  -->
<br><br><br>  

<?php
echo"</td></tr><tr><td class=colhead align=center><br><br><input type=submit value=resize! class=button /></form> ".
" <a class=altlink href=photo_gallery_crop.php?image=$photo_id><input type=submit value=\"back to edit image\" class=button /></a> ".
" <a class=altlink href=/photo_gallery.php?info=$photo_id><input type=submit value=\"back to image page\" class=button /></a><br></td></tr>";

end_table();
stdfoot();	
die();
}

//=== rotate image
if (isset($_GET['rotate'])) {
$photo_id = isset($_GET['image']) ? (int)$_GET['image'] : 0;
$degrees = isset($_GET['rotate']) ? (int)$_GET['rotate'] : 0;

$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$imagename = htmlspecialchars($arr_gal['location']);
$sourcefile = htmlentities("../bitbucket/$imagename"); 
$targetfile = htmlentities("../bitbucket/$imagename"); 
$source_thumb = htmlentities("../bitbucket/thumbs/$imagename"); 
$target_thumb = htmlentities("../bitbucket/thumbs/$imagename");

rotateImg($sourcefile, $targetfile, $degrees);
rotateImg($source_thumb, $target_thumb, $degrees);

$rotated_image_path = htmlentities("../bitbucket/".$imagename);

stdhead("Image rotated");	
//=== try to fool firefox
$no_cache = "?nocache=".time();

if ($width >= "500")
$show_image = "<a class=altlink href=\"javascript:PopupPic('$rotated_image_path$no_cache')\">".
"<img width=500 src=$rotated_image_path$no_cache title=\"click to open image full size in new window\"></a>".
"<span class=small><br><br>[ this image has been re-sized to fit this window. click the image to view full size ]</span>";
else
$show_image = "<a class=altlink href=\"javascript:PopupPic('$rotated_image_path$no_cache')\">".
"<img src=$rotated_image_path$no_cache title=\"click to open in new window\"></a>";

echo"<table width=90%><tr><td class=colhead align=center><h1>Rotated Image : ".htmlentities($imagename)."</h1></td></tr>".
"<tr><td class=clearalt6 align=center valign=middle><br><br>$show_image</td></tr>".
"<tr><td class=colhead align=center><br><br> <a class=altlink href=photo_gallery_crop.php?image=$photo_id><input type=submit value=\"back to edit image\" class=button /></a> ".
" <a class=altlink href=/photo_gallery.php?info=$photo_id><input type=submit value=\"back to image page\" class=button /></a></td></tr>";

end_table();
stdfoot();	
die();
}

//=== convert to greyscale
if (isset($_GET['convert_to_greyscale'])) {
$photo_id = isset($_GET['convert_to_greyscale']) ? (int)$_GET['convert_to_greyscale'] : 0;

$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$imagename = htmlspecialchars($arr_gal['location']);
$sourcefile = htmlentities("../bitbucket/$imagename"); 
$targetfile = htmlentities("../bitbucket/$imagename"); 
$source_thumb = htmlentities("../bitbucket/thumbs/$imagename"); 
$target_thumb = htmlentities("../bitbucket/thumbs/$imagename");

convert_to_greyscale($sourcefile, $targetfile);
convert_to_greyscale($source_thumb, $target_thumb);

$greyscale_image_path = htmlentities("../bitbucket/".$imagename);

stdhead("Image to greyscale");	
//=== try to fool firefox
$no_cache = "?nocache=".time();

if ($width >= "500")
$show_image = "<a class=altlink href=\"javascript:PopupPic('$greyscale_image_path$no_cache')\">".
"<img width=500 src=$greyscale_image_path$no_cache title=\"click to open image full size in new window\"></a>".
"<span class=small><br><br>[ this image has been re-sized to fit this window. click the image to view full size ]</span>";
else
$show_image = "<a class=altlink href=\"javascript:PopupPic('$greyscale_image_path$no_cache')\">".
"<img src=$greyscale_image_path$no_cache title=\"click to open in new window\"></a>";

echo"<table width=90%><tr><td class=colhead align=center><h1>Black and white Image : ".htmlentities($imagename)."</h1></td></tr>".
"<tr><td class=clearalt6 align=center valign=middle><br><br>$show_image</td></tr>".
"<tr><td class=colhead align=center><br><br> <a class=altlink href=photo_gallery_crop.php?image=$photo_id><input type=submit value=\"back to edit image\" class=button /></a> ".
" <a class=altlink href=/photo_gallery.php?info=$photo_id><input type=submit value=\"back to image page\" class=button /></a></td></tr>";

end_table();
stdfoot();	
die();
}

if (isset($_GET['cropped'])) {
$photo_id = isset($_GET['image']) ? (int)$_GET['image'] : 0;
$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$cropped_image_path = htmlentities("../bitbucket/crop_".$imagename);
$image = array_values(getimagesize(htmlentities($cropped_image_path)));
list($width, $height) = $image;

if (isset($_GET['save_new'])) {

$photo_id = isset($_GET['image']) ? (int)$_GET['image'] : 0;
$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$imagename = htmlspecialchars($arr_gal['location']);
rename($cropped_image_path, "../bitbucket/$imagename");
$new_croped_img = htmlentities("../bitbucket/$imagename"); 
 
//=== re-make thumbnails
list($width, $height) = getimagesize($new_croped_img);
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

$thumb_dir="../bitbucket/thumbs/$imagename"; 
$thumb = imagecreatetruecolor($thumb_width, $thumb_height);
$source = imagecreatefromjpeg($new_croped_img);

imagecopyresized($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

if (function_exists("imagegif")) 
ImageGIF($thumb,$thumb_dir);
elseif (function_exists("imagejpeg"))
ImageJPEG($thumb,$thumb_dir);
chmod("$thumb_dir",0777);
unlink("./bitbucket/crop_".$imagename);
header("Location: photo_gallery_crop.php?image=$photo_id&done=1");
}

stdhead("keep cropped?");				
if ($width >= "500")
$show_image = "<a class=altlink href=\"javascript:PopupPic('$cropped_image_path')\">".
"<img width=500 src=$cropped_image_path title=\"click to open image full size in new window\"></a>".
"<span class=small><br><br>[ this image has been re-sized to fit this window. click the image to view full size ]</span>";
else
$show_image = "<a class=altlink href=\"javascript:PopupPic('$cropped_image_path')\">".
"<img src=$cropped_image_path title=\"click to open in new window\"></a>";

echo"<table width=90%><tr><td class=colhead align=center><h1>Cropped Image : ".htmlentities($imagename)."</h1></td></tr>".
"<tr><td class=clearalt6 align=center valign=middle><br><br>$show_image</td></tr>".
"<tr><td class=clearalt6 align=center><br><b>new size</b><br><br>$width x $height [ ".mksize(filesize($cropped_image_path))." ]<br><br></td></tr>".
"<tr><td class=colhead align=center><br><br><a class=altlink2 href=photo_gallery_crop.php?image=$photo_id&cropped=1&save_new=1>".
"<input type=submit value=\"keep cropped image\" class=button /></a>".
" <input type=submit value=revert! class=button onclick=\"javascript: history.go(-1)\"/><br></td></tr>";

on_select_gallery_change();
end_table();
stdfoot();	
die();
}
 
require('class.cropinterface.php');
$ci =& new CropInterface(true);

if (isset($_GET['file'])) {
    $ci->loadImage($_GET['file']);
	$ci->cropToDimensions($_GET['sx'], $_GET['sy'], $_GET['ex'], $_GET['ey']);
	$ci->saveImage("../bitbucket/crop_".$imagename, $quality = 100);
    $ci->flushImages();
header("Location: photo_gallery_crop.php?cropped=1&image=$photo_id");
}
else {

$photo_id = isset($_GET['image']) ? (int)$_GET['image'] : 0;
$res_gal = mysql_query("SELECT location FROM photo_gallery WHERE id = $photo_id") or sqlerr(__FILE__, __LINE__);
$arr_gal = mysql_fetch_assoc($res_gal);

$imagename = htmlspecialchars($arr_gal['location']);
$image_src = "../bitbucket/$imagename";
stdhead('Edit Image');

echo"<p><b>edit images</b><br><br></p>$page_links<br><br>$gal_name".((0 + $_GET["done"]) == '1' ? "<h1>Image edited!</h1>" : "")."";
begin_table();
echo'<div style="margin:5em;">';

$ci->setCropAllowResize(true);
$ci->setCropTypeDefault(ccRESIZEANY);
$ci->setCropTypeAllowChange(true);
$ci->setCropSizeDefault('2/2');
$ci->setCropPositionDefault(ccCENTRE);
$ci->setCropMinSize(10, 10);
$ci->setExtraParameters(array('test' => '1', 'fake' => 'this_var'));
$ci->setCropSizeList(array(
        '200x200' => '200 x 200 pixels',
        '320x240' => '320 x 240 pixels',
        '3:5'     => '3x5 portrait',
        '5:3'     => '3x5 landscape',
        '8:10'    => '8x10 portrait',
        '10:8'    => '8x10 landscape',
        '4:3'     => 'TV screen',
        '16:9'    => 'Widescreen',
        '2/2'     => 'Half size',
        '4/2'     => 'Quater width and half height'
        ));
$ci->setMaxDisplaySize('500x500');
$ci->loadInterface($image_src);

echo'</div>';
end_table();
stdfoot();	
}
$ci->loadJavascript(); 
?>
