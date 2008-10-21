<?php

/**
 * $Id: inc.cropinterface.php 49 2006-11-29 14:35:46Z Andrew $
 *
 * [Description]
 *
 * Required file for class.cropinterface.php.
 *
 * [Author]
 *
 * Andrew Collington <php@amnuts.com> <http://php.amnuts.com/>
 */

list($w, $h) = $this->calculateCropDimensions($this->crop['default']);
list($x, $y) = $this->calculateCropPosition($w, $h);
$photo_id = isset($_GET['image']) ? (int)$_GET['image'] : 0;

session_cache_limiter( 'nocache' );
session_start( );
header( 'Cache-Control: no-cache, must-revalidate, post-check=3600, pre-check=3600' );

if($this->getImageWidth() < 500)
$this_width = 500;
else
$this_width = $this->getImageWidth();
?>
<!--
    Styles for the interface.
    Don't change any of the php code segments or #theCrop, unless you know
    what you are doing.  Feel free to change the rest if you so desire.
-->

<style type="text/css">
    #cropInterface {
        border: 1px solid black;
        padding: 0;
        margin: 0;
        text-align: center;
        background-color: #241a28;
        color: #f5f0c1;
		font-family: "tahoma", "arial", "helvetica", "sans-serif";
		font-size: 12px;
        width: <?php echo $this_width; ?>px;
    }
    #cropDetails {
  font-weight: bold;
  color: #cecff3;
  background-image: url("/pic/default/colhead.gif");
    }
    #cropResize, #cropResize p {
        margin: 5px;
        padding: 0;
        font-size: 11px;
        display: <?php echo ($this->crop['change'] && $this->crop['resize']) ? 'inherit' : 'none'; ?>;
    }
    #cropSizes {
        margin: 5px;
        padding: 0;
        font-size: 11px;
        display: <?php echo (!empty($this->crop['sizes']) && $this->crop['resize']) ? 'inherit' : 'none'; ?>;
    }
    #cropImage {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        margin: 0;
        padding: 0;
    }
    #cropSubmitButton {
		background-image: url("/pic/default/header.gif"); 
		color: #f5f2d8; 
		font-size: 10pt;
		font-family: verdana;
		background-color: #f5f2d8;
		font-weight: bold;
    }
	#cropSubmitButton:hover {
		background-image: url("/pic/default/header.gif"); 
		color: #ffffff; 
		font-size: 10pt;
		font-family: verdana;
		background-color: #ffffff;
		font-weight: bold;
		text-decoration: underline;
	}
    #theCrop {
        position: absolute;
        background-color: transparent;
        border: 1px solid yellow;
        background-image: url(<?php echo $this->getImageSource(); ?>?nocache=<?php echo time(); ?>);
        background-repeat: no-repeat;
        padding: 0;
        margin: 0;
    }
    #cropImage, #theImage {
        width: <?php echo $this->getImageWidth(); ?>px;
        height: <?php echo $this->getImageHeight(); ?>px;
    }

    /* box model hack stuff */

    #theCrop {
        width: <?php echo $w; ?>px;
        font-family: "\"}\"";
        font-family:inherit;
        width:<?php echo ($w - 2); ?>px;
    }
    #theCrop {
        height: <?php echo $h; ?>px;
        font-family: "\"}\"";
        font-family:inherit;
        height:<?php echo ($h - 2); ?>px;
    }
    html>body #theCrop {
        width:<?php echo ($w - 2); ?>px;
        height:<?php echo ($h - 2); ?>px;
    }
</style>

<!--
    The main interface.
    You must not rename the ids because things may break!
-->

<div id="theCrop"></div>
<div id="cropInterface">
    <div id="cropDetails">
        <strong><?php echo basename($this->file); ?> (<?php echo $this->img['sizes'][0]; ?> x <?php echo $this->img['sizes'][1]; ?>)</strong>
        <div id="cropDimensions">&nbsp;</div>
    </div>
    <div id="cropImage"><img src="<?php echo $this->getImageSource(); ?>?nocache=<?php echo time(); ?>" alt="image" title="crop this image" name="theImage" id="theImage" /></div>
    <div id="cropResize">
        <p><b>Cropping:</b> move the crop box with your mouse<br>hold down <b>shift</b> to resize crop box while dragging, then click <b>crop</b></p>
        <input type="radio" id="cropResizeAny" name="resize" onClick="cc_SetResizingType(0);"<?php if ($this->crop['type'] == ccRESIZEANY) { echo ' checked="checked"'; } ?> /> <label for="cropResizeAny">Any Dimensions</label> &nbsp; <input type="radio" name="resize" id="cropResizeProp" onClick="cc_SetResizingType(1);"<?php if ($this->crop['type'] == ccRESIZEPROP) { echo ' checked="checked"'; } ?> /> <label for="cropResizeProp">Proportional</label>
    </div>
    <div id="cropSizes">
        <select id="setSize" name="setSize" onchange="cc_setSize();">
            <option value="-1">Select a cropping size</option>
            <?php
                if (!empty($this->crop['sizes'])) {
                    foreach ($this->crop['sizes'] as $size => $desc) {
                        echo '<option value="', $size, '">', $desc, '</option>';
                    }
                }
            ?>
        </select>
		    <div id="cropSubmit">
        <br><input type="submit" value="crop" id="cropSubmitButton" onClick="cc_Submit();" /><br><br>
    </div><p><b>Rotating:</b> select the amount from the drop down box.</p>
		<select name="rotate" name=page onChange="goTo(this.options[this.selectedIndex].value)">
		<option> rotate image </option>
		<option value=photo_gallery_crop.php?image=<?php echo $photo_id; ?>&rotate=-90> 90 &#730;</option>
		<option value=photo_gallery_crop.php?image=<?php echo $photo_id; ?>&rotate=180> 180 &#730;</option>
		<option value=photo_gallery_crop.php?image=<?php echo $photo_id; ?>&rotate=270> 270 &#730;</option>
		</select>
    
 <br><p><b>Other Options:</b> click the buttons below</p> <a class=altlink2 href=photo_gallery_crop.php?convert_to_greyscale=<?php echo $photo_id; ?>><input type="submit" value="convert to greyscale" class=button /></a>
 <a class=altlink2 href=photo_gallery_crop.php?resize_image=<?php echo $photo_id; ?>><input type="submit" value="re-size image" class=button /></a><br><br>
</div></div><br><br>
<!--
    Main javascript routines.
    Changing things here may break functionality, so don't tweak unless you
    know what you are doing.
-->

<script type="text/javascript" src="wz_dragdrop.js"></script>
<script type="text/javascript">

function goTo (page) 
{
	 	if (page != "" ) 
	{
		if (page == "--" ) {
		resetMenu();
		} 
	else
		{
		document.location.href = page;
		}
	}
	return false;
}


    function my_DragFunc()
    {
        dd.elements.theCrop.maxoffr = dd.elements.theImage.w - dd.elements.theCrop.w;
        dd.elements.theCrop.maxoffb = dd.elements.theImage.h - dd.elements.theCrop.h;
        dd.elements.theCrop.maxw    = <?php echo $this->getImageWidth(); ?>;
        dd.elements.theCrop.maxh    = <?php echo $this->getImageHeight(); ?>;
        cc_showCropSize();
		cc_reposBackground();
    }

    function my_ResizeFunc()
    {
        dd.elements.theCrop.maxw = (dd.elements.theImage.w + dd.elements.theImage.x) - dd.elements.theCrop.x;
        dd.elements.theCrop.maxh = (dd.elements.theImage.h + dd.elements.theImage.y) - dd.elements.theCrop.y;
        cc_showCropSize();
		cc_reposBackground();
    }

    function cc_Submit()
    {
        self.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?image=<?php echo $photo_id; ?>&file=<?php echo $this->file; ?>&sx=' +
                Math.round((dd.elements.theCrop.x - dd.elements.theImage.x)<?php echo ($this->getRatio()) ? ' * ' . $this->getRatio() : ''; ?>) + '&sy=' +
                Math.round((dd.elements.theCrop.y - dd.elements.theImage.y)<?php echo ($this->getRatio()) ? ' * ' . $this->getRatio() : ''; ?>) + '&ex=' +
                Math.round(((dd.elements.theCrop.x - dd.elements.theImage.x) + dd.elements.theCrop.w)<?php echo ($this->getRatio()) ? ' * ' . $this->getRatio() : ''; ?>) + '&ey=' +
                Math.round(((dd.elements.theCrop.y - dd.elements.theImage.y) + dd.elements.theCrop.h)<?php echo ($this->getRatio()) ? ' * ' . $this->getRatio() : ''; ?>) +
                '<?php echo $this->params['str']; ?>';
    }

    function cc_SetResizingType(proportional)
    {
        if (proportional) {
            dd.elements.theCrop.defw = dd.elements.theCrop.w;
            dd.elements.theCrop.defh = dd.elements.theCrop.h;
            dd.elements.theCrop.scalable  = 1;
            dd.elements.theCrop.resizable = 0;
        } else {
            dd.elements.theCrop.scalable  = 0;
            dd.elements.theCrop.resizable = 1;
        }
    }

    function cc_reposBackground()
    {
        xPos = (dd.elements.theCrop.x - dd.elements.theImage.x + 1);
        yPos = (dd.elements.theCrop.y - dd.elements.theImage.y + 1);

        if (document.getElementById) {
            document.getElementById('theCrop').style.backgroundPosition = '-' + xPos + 'px -' + yPos + 'px';
        } else if (document.all) {
            document.all['theCrop'].style.backgroundPosition = '-' + xPos + 'px -' + yPos + 'px';
        } else {
            document.layers['theCrop'].backgroundPosition = '-' + xPos + 'px -' + yPos + 'px';
        }
    }

    function cc_showCropSize()
    {
        dd.elements.cropDimensions.write('Crop size: ' + dd.elements.theCrop.w + ' / ' + dd.elements.theCrop.h);
    }

    function cc_setSize()
    {
        element = document.getElementById('setSize');
        switch(element.value) {
        <?php
            $str = "case '%s':
                        cc_setCropDimensions(%d, %d);
                        dd.elements.theCrop.moveTo(dd.elements.theImage.x + %d, dd.elements.theImage.y + %d);
                        cc_reposBackground();
                        break\n";
            if ($this->crop['sizes']) {
                foreach ($this->crop['sizes'] as $s => $d) {
                    list($w, $h) = $this->calculateCropDimensions($s);
                    list($x, $y) = $this->calculateCropPosition($w, $h);
                    printf($str, $s, $w, $h, $x, $y);
                }
            }
        ?>
        }
        cc_showCropSize();
    }

    function cc_setCropDimensions(w, h)
    {
        dd.elements.theCrop.moveTo(dd.elements.theImage.x, dd.elements.theImage.y);
        dd.elements.theCrop.resizeTo(w, h);
        dd.elements.theCrop.defw = w;
        dd.elements.theCrop.defh = h;
        cc_reposBackground();
    }
</script>
