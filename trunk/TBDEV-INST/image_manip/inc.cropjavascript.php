<?php

/**
 * $Id: inc.cropjavascript.php 49 2006-11-29 14:35:46Z Andrew $
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

?>

<script type="text/javascript">
    SET_DHTML("theCrop"+MAXOFFLEFT+0+MAXOFFRIGHT+<?php echo $this->getImageWidth(); ?>+MAXOFFTOP+0+MAXOFFBOTTOM+<?php echo $this->getImageHeight(), ($this->crop['resize'] ? '+RESIZABLE' : ''); ?>+MAXWIDTH+<?php echo $this->getImageWidth(); ?>+MAXHEIGHT+<?php echo $this->getImageHeight(); ?>+MINHEIGHT+<?php echo $this->crop['min-height']; ?>+MINWIDTH+<?php echo $this->crop['min-width']; ?>,"theImage"+NO_DRAG,"cropDimensions"+NO_DRAG);

    dd.elements.theCrop.moveTo(dd.elements.theImage.x + <?php echo $x; ?>, dd.elements.theImage.y + <?php echo $y; ?>);
    dd.elements.theCrop.setZ(dd.elements.theImage.z+1);
    dd.elements.theImage.addChild("theCrop");
    dd.elements.theCrop.defx = dd.elements.theImage.x;
    dd.elements.theCrop.defy = dd.elements.theImage.y;
    dd.elements.theImage.setOpacity(0.3);

    <?php if ($this->crop['resize']) { echo 'cc_SetResizingType(', (string)$this->crop['type'], ');'; } ?>
    cc_showCropSize();
    cc_reposBackground();
</script>