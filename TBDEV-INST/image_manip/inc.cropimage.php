<?php

/**
 * $Id: inc.cropimage.php 49 2006-11-29 14:35:46Z Andrew $
 *
 * [Description]
 *
 * Required file for class.cropinterface.php.
 *
 * [Author]
 *
 * Andrew Collington <php@amnuts.com> <http://php.amnuts.com/>
 */

list($w, $h) = explode('x', $_GET['s']);
require_once(dirname(__FILE__) . '/class.cropinterface.php');
$ci =& new CropInterface();

if (isset($_GET['nch'])) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

$ci->showImageAtSize($_GET['f'], $w, $h)

?>