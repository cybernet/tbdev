<?php

/**
 * $Id: class.cropcanvas.php 44 2006-06-26 10:05:41Z Andrew $
 * 
 * [Description]
 * 
 * This is a class allows you to crop an image in a variety of ways.
 * You can crop in an absolute or relative way (to a certain size or
 * by a certain size), both as a pixel number or a percentage.  You
 * can also save or display the cropped image.  The cropping can be
 * done in 9 different positions: top left, top, top right, left, 
 * centre, right, bottom left, bottom, or bottom right.  Or you can
 * crop automatically based on a threshold limit.  The original
 * image can be loaded from the file system or from a string (for
 * example, data returned from a database.)
 * 
 * [Author]
 * 
 * Andrew Collington <php@amnuts.com> <http://php.amnuts.com/>
 * 
 * [Feedback]
 * 
 * There is message board at the following address:
 * 
 *     <http://php.amnuts.com/forums/>
 * 
 * Please use that to post up any comments, questions, bug reports, etc.  You
 * can also use the board to show off your use of the script.
 * 
 * [Support]
 * 
 * If you like this script, or any of my others, then please take a moment
 * to consider giving a donation.  This will encourage me to make updates and
 * create new scripts which I would make available to you.  If you would like
 * to donate anything, then there is a link from my website to PayPal.
 * 
 * [Examples of use]
 * 
 * 	  require('class.cropcanvas.php');
 * 	  $cc =& new CropCanvas();
 * 
 *    $cc->loadImage('original1.png');
 * 	  $cc->cropBySize(100, 100, ccBOTTOMRIGHT);
 * 	  $cc->saveImage('final1.png');
 * 
 * 	  $cc->flushImages(false);
 * 
 * 	  $cc->cropByPercent(15, 50, ccCENTER);
 * 	  $cc->saveImage('final2.jpg', 90);
 * 
 * 	  $cc->flushImages(true);
 * 
 * 	  $cc->loadImage('original3.png');
 * 	  $cc->cropToDimensions(67, 37, 420, 255);
 * 	  $cc->showImage('png');
 */


define("ccTOPLEFT",     0);
define("ccTOP",         1);
define("ccTOPRIGHT",    2);
define("ccLEFT",        3);
define("ccCENTRE",      4);
define("ccCENTER",      4);
define("ccRIGHT",       5);
define("ccBOTTOMLEFT",  6);
define("ccBOTTOM",      7);
define("ccBOTTOMRIGHT", 8);


/**
 * This class extends the cropCanvas class purely to support and class name
 * change and facilitate backwards compatability.
 */
class canvasCrop extends CropCanvas
{
    /**
     * Class constructor.
     * 
     * @param  string $debug 
     * @return cavasCrop 
     * @access public
     */
    function cavasCrop($debug = false)
    {
        parent::CropCanvas($debug);
    }
}

/**
 * The newly renamed class.
 */
class CropCanvas
{
    var $_imgOrig   = null;
    var $_imgFinal  = null;
    var $_showDebug = false;
    var $gdInfo     = array();

    /**
     * Class constructor.
     * 
     * @param  string $debug 
     * @return cropCanvas 
     * @access public
     */
    function CropCanvas($debug = false)
    {
        $this->setDebugging($debug);
        $this->gdInfo = $this->getGDInfo();
    }

    /**
     * Toggles debugging.
     * 
     * @param  bool $do
     * @return void 
     * @access public
     */
    function setDebugging($do = false)
    {
        $this->_showDebug = ($do === true) ? true : false;
    }

    /**
     * Load an image from the file system.
     * 
     * An image is loaded using the appropriate function by automatically 
     * determining the file extension and then seeing if there is support
     * for it in the user's installation of GD.
     * 
     * @param  string $filename
     * @return bool 
     * @access public
     */
    function loadImage($filename)
    {
        $ext  = strtolower($this->_getExtension($filename));
        $func = 'imagecreatefrom' . ($ext == 'jpg' ? 'jpeg' : $ext);
        if (!$this->_isSupported($filename, $ext, $func, false)) {
            return false;
        }

        $this->_imgOrig = $func($filename);
        if ($this->_imgOrig == null) {
            $this->_debug("The image could not be created from the '$filename' file using the '$func' function.");
            return false;
        }

        return true;
    }

    /**
     * Load an image from a string (eg. from a database table)
     * 
     * @param  string $string
     * @return bool 
     * @access public
     */
    function loadImageFromString($string)
    {
        $this->_imgOrig = imagecreatefromstring($string);
        if (!$this->_imgOrig) {
            $this->_debug('The image (supplied as a string) could not be created.');
            return false;
        }

        return true;
    }

    /**
     * Save the cropped image
     * 
     * @param  string $filename 
     * @param  int    $quality 
     * @param  string $forcetype 
     * @return bool 
     * @access public
     */
    function saveImage($filename, $quality = 90, $forcetype = '')
    {
        if ($this->_imgFinal == null) {
            $this->_debug('There is no cropped image to save.');
            return false;
        }

        $ext  = ($forcetype == '') ? $this->_getExtension($filename) : strtolower($forcetype);
        $func = 'image' . ($ext == 'jpg' ? 'jpeg' : $ext);
        if (!$this->_isSupported($filename, $ext, $func, true)) {
            return false;
        }

        $saved = false;
        switch($ext) {
            case 'gif':
                if ($this->gdInfo['Truecolor Support'] && imageistruecolor($this->_imgFinal)) {
                    imagetruecolortopalette($this->_imgFinal, false, 255);
                }
            case 'png':
                $saved = $func($this->_imgFinal, $filename);
                break;
            case 'jpg':
                $saved = $func($this->_imgFinal, $filename, $quality);
                break;
        }

        if ($saved == false) {
            $this->_debug("The image could not be saved to the '$filename' file as the file type '$ext' using the '$func' function.");
            return false;
        }

        return true;
    }

    /**
     * Shows the masked image without any saving
     * 
     * @param  string $type 
     * @param  int    $quality 
     * @return bool 
     * @access public
     */
    function showImage($type = 'png', $quality = 90)
    {
        if ($this->_imgFinal == null) {
            $this->_debug('There is no cropped image to show.');
            return false;
        }

        $type = strtolower($type);
        $func = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
        $head = 'image/' . ($type == 'jpg' ? 'jpeg' : $type);
        
        if (!$this->_isSupported('[showing file]', $type, $func, false)) {
            return false;
        }

        header("Content-type: $head");
        switch($type) {
            case 'gif':
                if ($this->gdInfo['Truecolor Support'] && imageistruecolor($this->_imgFinal)) {
                    imagetruecolortopalette($this->_imgFinal, false, 255);
                }
            case 'png':
                $func($this->_imgFinal);
                break;
            case 'jpg':
                $func($this->_imgFinal, '', $quality);
                break;
        }

        return true;
    }

    /**
	 * Determines the dimensions for cropping image by a certain amount.
	 * 
	 * @param  int $x
	 * @param  int $y
	 * @param  int $position
	 * @return bool
     * @access public
	 */
    function cropBySize($x, $y, $position = ccCENTRE)
    {
        $nx = (!$x) ? imagesx($this->_imgOrig) : imagesx($this->_imgOrig) - $x;
        $ny = (!$y) ? imagesy($this->_imgOrig) : imagesy($this->_imgOrig) - $y;
        return ($this->_cropSize(-1, -1, $nx, $ny, $position));
    }

    /**
	 * Determines the dimensions for cropping image to a certain size.
	 * 
	 * @param  int $x
	 * @param  int $y
	 * @param  int $position
	 * @return bool
     * @access public
	 */
    function cropToSize($x, $y, $position = ccCENTRE)
    {
        return ($this->_cropSize(-1, -1, ($x <= 0 ? 1 : $x), ($y <= 0 ? 1 : $y), $position));
    }

    /**
	 * Used for cropping at a specific location (given start x/y and end x/y).
	 * 
	 * @param  int $sx
	 * @param  int $sy
	 * @param  int $ex
	 * @param  int $ey
	 * @return bool
     * @access public
	 */
    function cropToDimensions($sx, $sy, $ex, $ey)
    {
        return ($this->_cropSize($sx, $sy, abs($ex - $sx), abs($ey - $sy), null));
    }

    /**
	 * Determines the dimensions for cropping image by a certain amount.
	 * 
	 * Calculations based on the size given as a percentage of the image size.
	 * 
	 * @param  int $px
	 * @param  int $py
	 * @param  int $position
	 * @return bool
     * @access public
	 */
    function cropByPercent($px, $py, $position = ccCENTRE)
    {
        $nx = (!$px) ? imagesx($this->_imgOrig) : (imagesx($this->_imgOrig) - (($px / 100) * imagesx($this->_imgOrig)));
        $ny = (!$py) ? imagesy($this->_imgOrig) : (imagesy($this->_imgOrig) - (($py / 100) * imagesy($this->_imgOrig)));
        return ($this->_cropSize(-1, -1, $nx, $ny, $position));
    }

    /**
	 * Determines the dimensions for cropping image to a certain size.
	 * 
	 * Calculations based on the size given as a percentage of the image size.
	 * 
	 * @param  int $px
	 * @param  int $py
	 * @param  int $position
	 * @return bool
     * @access public
	 */
    function cropToPercent($px, $py, $position = ccCENTRE)
    {
        $nx = (!$px) ? imagesx($this->_imgOrig) : (($px / 100) * imagesx($this->_imgOrig));
        $ny = (!$py) ? imagesy($this->_imgOrig) : (($py / 100) * imagesy($this->_imgOrig));
        return ($this->_cropSize(-1, -1, $nx, $ny, $position));
    }

    /**
	 * Determines cropping dimensions based on threshold level.
	 * 
	 * The threshold scale is 0 (black) to 255 (white).
	 * 
	 * @param  int $threshold
 	 * @return bool
     * @access public
	 */
    function cropByAuto($threshold = 254)
    {
        if ($threshold < 0) {
            $threshold = 0;
        }
        if ($threshold > 255) {
            $threshold = 255;
        }
        $sizex = imagesx($this->_imgOrig);
        $sizey = imagesy($this->_imgOrig);
        $sx = $sy = $ex = $ey = -1;
        for ($y = 0; $y < $sizey; $y++) {
            for ($x = 0; $x < $sizex; $x++) {
                if ($threshold >= $this->_getThresholdValue($this->_imgOrig, $x, $y)) {
                    if ($sy == -1) {
                        $sy = $y;
                    } else {
                        $ey = $y;
                    }
                    if ($sx == -1) {
                        $sx = $x;
                    } else {
                        if ($x < $sx) {
                            $sx = $x;
                        } else if ($x > $ex) {
                            $ex = $x;
                        }
                    }
                }
            }
        }
        return ($this->_cropSize($sx, $sy, abs($ex - $sx), abs($ey - $sy), ccTOPLEFT));
    }

    /**
	 * Destroy the resources used by the images.
	 * 
	 * @param  bool $original
	 * @return void
     * @access public
	 */
    function flushImages($original = true)
    {
        imagedestroy($this->_imgFinal);
        $this->_imgFinal = null;
        if ($original) {
            imagedestroy($this->_imgOrig);
            $this->_imgOrig = null;
        }
    }

    /**
	 * Creates the cropped image based on passed parameters
	 * 
	 * @param int    $ox Original image width
	 * @param int    $oy Original image height
	 * @param int    $nx New width
	 * @param int    $ny New height
	 * @param int    $position Where to place the crop
	 * @return bool
	 */
    function _cropSize($ox, $oy, $nx, $ny, $position)
    {
        if ($this->_imgOrig == null) {
            $this->_debug('The original image has not been loaded.');
            return false;
        }
        if (($nx <= 0) || ($ny <= 0)) {
            $this->_debug('The image could not be cropped because the size given is not valid.');
            return false;
        }
        if (($nx > imagesx($this->_imgOrig)) || ($ny > imagesy($this->_imgOrig))) {
            $this->_debug('The image could not be cropped because the size given is larger than the original image.');
            return false;
        }
        if ($ox == -1 || $oy == -1) {
            list($ox, $oy) = $this->_getCopyPosition($nx, $ny, $position);
        }
        if ($this->gdInfo['Truecolor Support']) {
            $this->_imgFinal = imagecreatetruecolor($nx, $ny);
            imagecopyresampled($this->_imgFinal, $this->_imgOrig, 0, 0, $ox, $oy, $nx, $ny, $nx, $ny);
        } else {
            $this->_imgFinal = imagecreate($nx, $ny);
            imagecopyresized($this->_imgFinal, $this->_imgOrig, 0, 0, $ox, $oy, $nx, $ny, $nx, $ny);
        }
        return true;
    }

    /**
	 * Determine position of the crop.
	 * 
	 * @param  int $nx
	 * @param  int $ny
	 * @param  int $position
	 * @return array
	 */
    function _getCopyPosition($nx, $ny, $position)
    {
        $ox = imagesx($this->_imgOrig);
        $oy = imagesy($this->_imgOrig);

        switch($position) {
            case ccTOPLEFT:
                return array(0, 0);
            case ccTOP:
                return array(ceil(($ox - $nx) / 2), 0);
            case ccTOPRIGHT:
                return array(($ox - $nx), 0);
            case ccLEFT:
                return array(0, ceil(($oy - $ny) / 2));
            case ccCENTRE:
                return array(ceil(($ox - $nx) / 2), ceil(($oy - $ny) / 2));
            case ccRIGHT:
                return array(($ox - $nx), ceil(($oy - $ny) / 2));
            case ccBOTTOMLEFT:
                return array(0, ($oy - $ny));
            case ccBOTTOM:
                return array(ceil(($ox - $nx) / 2), ($oy - $ny));
            case ccBOTTOMRIGHT:
                return array(($ox - $nx), ($oy - $ny));
        }
        
        return array();
    }

    /**
	 * Determines the intensity value of a pixel at the passed co-ordinates.
	 * 
	 * @param  resource $im
	 * @param  int $x
	 * @param  int $y
	 * @return float
	 */
    function _getThresholdValue($im, $x, $y)
    {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return (($r + $g + $b) / 3);
    }

    /**
	 * Get the extension of a file name
	 * 
	 * @param  string $file
 	 * @return string
	 */
    function _getExtension($file)
    {
        $ext = '';
        if (strrpos($file, '.')) {
            $ext = strtolower(substr($file, (strrpos($file, '.') ? strrpos($file, '.') + 1 : strlen($file)), strlen($file)));
        }
        return $ext;
    }

    /**
	 * Validate whether image reading/writing routines are valid.
	 * 
	 * @param  string $filename
	 * @param  string $extension
	 * @param  string $function
	 * @param  bool   $write
	 * @return bool
     * @access private
	 */
    function _isSupported($filename, $extension, $function, $write = false)
    {
        $giftype = ($write) ? ' Create Support' : ' Read Support';
        $support = strtoupper($extension) . ($extension == 'gif' ? $giftype : ' Support');

        if (!isset($this->gdInfo[$support]) || $this->gdInfo[$support] == false) {
            $request = ($write) ? 'saving' : 'reading';
            $this->_debug("Support for $request the file type '$extension' cannot be found.");
            return false;
        }
        if (!function_exists($function)) {
            $request = ($write) ? 'save' : 'read';
            $this->_debug("The '$function' function required to $request the '$filename' file cannot be found.");
            return false;
        }

        return true;
    }

    /**
     * Gathers the GD version information
     *
     * Sometimes a check for the GD version like this:
     *
     *     (function_exists('imagecreatetruecolor')) ? 2 : 1;
     *
     * can fail (at least, in my experience).  This method retrieves the GD 
     * information based on what phpinfo() reports to be installed.
     *
     * @param  bool $justVersion
     * @return array
     * @access public
     */
    function getGDInfo($justVersion = false)
    {
        $gdinfo = array();

        if (function_exists('gd_info')) {
            $gdinfo = gd_info();
        } else {
            $gd = array(
                    'GD Version'         => '',
                    'FreeType Support'   => false,
                    'FreeType Linkage'   => '',
                    'T1Lib Support'      => false,
                    'GIF Read Support'   => false,
                    'GIF Create Support' => false,
                    'JPG Support'        => false,
                    'PNG Support'        => false,
                    'WBMP Support'       => false,
                    'XBM Support'        => false
                    );
            ob_start();
            phpinfo();
            $buffer = ob_get_contents();
            ob_end_clean();
            foreach (explode("\n", $buffer) as $line) {
                $line = array_map('trim', (explode('|', strip_tags(str_replace('</td>', '|', $line)))));
                if (isset($gd[$line[0]])) {
                    if (strtolower($line[1]) == 'enabled') {
                        $gd[$line[0]] = true;
                    } else {
                        $gd[$line[0]] = $line[1];
                    }
                }
            }
            $gdinfo = $gd;
        }

        if (isset($gdinfo['JIS-mapped Japanese Font Support'])) {
            unset($gdinfo['JIS-mapped Japanese Font Support']);
        }
        if (function_exists('imagecreatefromgd')) {
            $gdinfo['GD Support'] = true;
        }
        if (function_exists('imagecreatefromgd2')) {
            $gdinfo['GD2 Support'] = true;
        }
        if (preg_match('/^(bundled|2)/', $gdinfo['GD Version'])) {
            $gdinfo['Truecolor Support'] = true;
        } else {
            $gdinfo['Truecolor Support'] = false;
        }
        if ($gdinfo['GD Version'] != '') {
            $match = array();
            if (preg_match('/([0-9\.]+)/', $gdinfo['GD Version'], $match)) {
                $foo = explode('.', $match[0]);
                $gdinfo['Version'] = array('major' => $foo[0], 'minor' => $foo[1], 'patch' => $foo[2]);
            }
        }

        return ($justVersion) ? $gdinfo['Version'] : $gdinfo;
    }

    /**
	 * Display some simple textual deugging.
	 * 
	 * @param  string $string
	 * @return void
     * @access private
	 */
    function _debug($string)
    {
        if ($this->_showDebug) {
            echo '<p class="debug">', $string, "</p>\n";
        }
    }
}

?>
