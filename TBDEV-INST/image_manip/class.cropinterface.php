<?php

/**
 * $Id: class.cropinterface.php 49 2006-11-29 14:35:46Z Andrew $
 *
 * [Description]
 *
 * This class allows you to use all the power of the crop canvas class
 * (class.cropcanvas.php) with a very easy to use and understand user
 * interface.
 *
 * Using your browser you can drag and resize the cropping area and
 * select if you want to resize in any direction or proportional to
 * the image.  If you wanted to provide users a cropping area without
 * any resizing options, then this can easily be acheived.
 *
 * In the interface you can also have preset sizes as set pixel dimensions
 * (50x100), ratios of the image size (16:9), or divisions of the image
 * size (4/3).
 *
 * [Requirements]
 *
 * This file requires the class.cropcanvas.php, inc.cropinterface.php,
 * inc.cropjavascript.php and inc.cropimage.php files also from the
 * amnuts php website <http://php.amnuts.com/>.
 *
 * The cropping area implements the 'Drag & Drop API' javascript by
 * Walter Zorn <http://www.walterzorn.com/dragdrop/dragdrop_e.htm>.
 *
 * [Author]
 *
 * Andrew Collington <php@amnuts.com> <http://php.amnuts.com/>
 *
 * [Feedback]
 *
 * There is message board at the following address:
 *
 *    <http://php.amnuts.com/forums/>
 *
 * Please use that to post up any comments, questions, bug reports, etc.
 * You can also use the board to show off your use of the script.
 *
 * [Support]
 *
 * If you like this script, or any of my others, then please take a moment
 * to consider giving a donation.  This will encourage me to make updates
 * and create new scripts which I would make available to you, and to give
 * support for my current scripts.  If you would like to donate anything,
 * then there is a link from my website to PayPal.
 *
 * [Example of use]
 *
 *  require('class.cropinterface.php');
 *  $ci =& new CropInterface();
 *  if ($_GET['file']) {
 *      $ci->loadImage($_GET['file']);
 *      $ci->cropToDimensions($_GET['sx'], $_GET['sy'], $_GET['ex'], $_GET['ey']);
 *      $ci->showImage('jpg', 90);
 *      exit;
 *  } else {
 *      $ci->setCropAllowResize(true);
 *      $ci->setCropTypeDefault(ccRESIZEPROP);
 *      $ci->setCropTypeAllowChange(true);
 *      $ci->setCropSizeDefault('5:3');
 *      $ci->setCropMinSize(10, 10);
 *      $ci->setExtraParameters('fake', 'this_var');
 *      $ci->setCropSizeList(array(
 *              '200x200' => '200 x 200 pixels',
 *              '320x240' => '320 x 240 pixels',
 *              '3:5'     => '3x5 ratio',
 *              '5:3'     => '5x3 ratio',
 *              '8:10'    => '8x10 ratio',
 *              '10:8'    => '10x8 ratio',
 *              '4:3'     => 'TV screen',
 *              '16:9'    => 'Widescreen',
 *              '2/2'     => 'Half size',
 *              '4/2'     => 'Quater width and half height'
 *              ));
 *      $ci->setMaxDisplaySize('200x300');
 *      $ci->loadInterface('mypicture.jpg');
 *      $ci->loadJavascript();
 *  }
 *
 */


require_once(dirname(__FILE__) . '/class.cropcanvas.php');

define('ccCROPSIZEMIN', 5);
define('ccRESIZEANY',   0);
define('ccRESIZEPROP',  1);

class CropInterface extends CropCanvas
{
    /**
     * The filename
     *
     * @var string
     * @access public
     */
    var $file = '';
    /**
     * Holds information about the image
     *
     * @var array
     * @access public
     */
    var $img  = array();
    /**
     * Holds information about the crop
     *
     * @var array
     * @access public
     */
    var $crop = array(
                    'type'        => ccRESIZEANY,
                    'change'      => true,
                    'resize'      => true,
                    'min-width'   => ccCROPSIZEMIN,
                    'min-height'  => ccCROPSIZEMIN,
                    'sizes'       => array(),
                    'default'     => '',
                    'position'    => ccCENTRE,
                    'max-display' => array()
                    );
    /**
     * Holds any extra parameters supplied by user.
     *
     * @var array
     * @access public
     */
    var $params = array(
                    'list' => array(),
                    'str'  => ''
                    );



    /**
     * Class constructor.
     *
     * @param  boolean $debug
     * @access public
     * @return CropInterface
     */
    function CropInterface($debug = false)
    {
        parent::CropCanvas($debug);
    }

    /**
     * Set the default cropping area resize type.
     *
     * Cropping area resize type is either ccRESIZEANY or ccRESIZEPROP for
     * any dimensions or proportional (respectively).
     *
     * @param interger $type
     * @access public
     * @see CropInterface::setCropTypeAllowChange
     */
    function setCropTypeDefault($type = ccRESIZEANY)
    {
        $this->crop['type'] = $type;
    }

    /**
     * Allow the user to change the crop area resize type.
     *
     * By default the user can swap between resizing the cropping area by any
     * dimension of proportionally based on what the cropping area's dimensions
     * are currently.  Passing flase as a parameter will stop the user being
     * able to swap which type or resize they are using.
     *
     * @param boolean $allow
     * @access public
     * @see CropInterface::setCropTypeDefault
     */
    function setCropTypeAllowChange($allow = true)
    {
        $this->crop['change'] = $allow;
    }

    /**
     * Allow any type of resizing for the cropping area.
     *
     * Sometimes you may want to set a default size and not allow the user
     * to resize the cropping area but instead just move it around.  If this
     * were the case, you'd call this method passing false as a parameter.
     *
     * @param boolean $allow
     * @access public
     */
    function setCropAllowResize($allow = true)
    {
        $this->crop['resize'] = $allow;
    }

    /**
     * Set preset size options.
     *
     * The $size parameter can be an array with the size string as the index
     * and description as the value, or passed as a string and used in
     * conjunction with the second parameter.
	 *
	 * The size string can be one of three format:
	 *
	 *     o 50x100 - 50 pixels by 100 pixels
	 *     o 4:3 - a ratio of 4 to 3
	 *     o 3/2 - a thrid of the width and half the height
	 *
	 * This will give a drop-down list of preset options in the interface.
     *
     * @param array|string $size
     * @param string $description
     * @access public
     */
    function setCropSizeList($size, $description = '')
    {
        if (is_array($size)) {
            foreach ($size as $s => $d) {
                if (preg_match('!^(\d+)[:xX/](\d+)$!', $s)) {
                    $this->crop['sizes'][strtolower($s)] = $d;
                }
            }
        } else if (preg_match('!^(\d+)[:xX/](\d+)$!', $size)) {
            $this->crop['sizes'][strtolower($size)] = $description;
        }
    }

    /**
     * Allows you to set the default position of the crop window.
     *
     * Valid positions are:
     *
     *     o ccTOPLEFT
     *     o ccTOP
     *     o ccTOPRIGHT
     *     o ccLEFT
     *     o ccCENTRE (or) ccCENTER
     *     o ccRIGHT
     *     o ccBOTTOMLEFT
     *     o cBOTTOM
     *     o ccBOTTOMRIGHT
     *
     * @param int $position
     * @access public
     */
    function setCropPositionDefault($position)
    {
        $this->crop['position'] = $position;
    }

    /**
     * Set the initial size of the cropping area.
	 *
	 * The size string can be one of three format:
	 *
	 *     o 50x100 - 50 pixels by 100 pixels
	 *     o 4:3 - a ratio of 4 to 3
	 *     o 3/2 - a third of the width and half the height
     *
     * @param string $size
     * @access public
     */
    function setCropSizeDefault($size)
    {
        if (preg_match('!^(\d+)[:xX/](\d+)$!', $size)) {
            $this->crop['default'] = $size;
        }
    }

    /**
     * Set the smallest size of the cropping area.
     *
     * @param int $w
     * @param int $h
     * @access public
     */
    function setCropMinSize($w = 25, $h = 25)
    {
        $this->crop['min-width']  = ($w < ccCROPSIZEMIN) ? ccCROPSIZEMIN : $w;
        $this->crop['min-height'] = ($h < ccCROPSIZEMIN) ? ccCROPSIZEMIN : $h;
    }

    /**
     * Set the maximum display size for the image.
	 *
	 * The size string can be the format:
	 *
	 *     o 100x75 - 100 pixels by 75 pixels
	 *
	 * which will mean that the image displayed in the interface will fit
	 * within 100x75 pixel size box and will be proportionally resized
	 * accordingly.
     *
     * @param string $size
     * @access public
     */
    function setMaxDisplaySize($size)
    {
        if (preg_match('!^(\d+)[xX](\d+)$!', $size, $sizes)) {
            $this->crop['max-display'] = $sizes;
        }
    }

	/**
	 * Allows user to supply additiona parameters sent in the form.
	 *
	 * @param array|string $name
	 * @param string $value
     * @access public
	 */
	function setExtraParameters($name, $value = '')
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->params['list'][$key] = $value;
            }
        } else {
            $this->params['list'][$name] = $value;
        }
    }

	/**
	 * Return path to image.
	 *
	 * If the maximum display size is set then the image needs to be generated
	 * dynamically, else it can directly be the image filename.
	 *
	 * @return string
     * @access public
	 */
    function getImageSource()
    {
        return (!empty($this->crop['max-display']))
            ? "inc.cropimage.php?f={$this->file}&s={$this->crop['max-display'][0]}"
            : $this->img['src'];
    }

    /**
     * Get the image width.
     *
     * @return int
     */
    function getImageWidth()
    {
        return $this->img['sizes'][0];
    }

    /**
     * Get the image height.
     *
     * @return int
     * @access public
     */
    function getImageHeight()
    {
        return $this->img['sizes'][1];
    }

    /**
     * Get the resize ratio.
     *
     * If the maximum display size is used then we need to work out the ratio
     * of the cropping positions to correct crop the original image.  This
     * returns 0 is no maximum display size is used else the ratio figure.
     *
     * @return int|float
     * @access public
     */
    function getRatio()
    {
        return (isset($this->crop['max-display'][3]))
            ? $this->crop['max-display'][3]
            : 0;
    }

	/**
	 * Initiates the cropping interface and javascript functions.
	 *
	 * @param string $filename
	 * @access public
	 * @see file://inc.cropinterface.php
	 */
    function loadInterface($filename)
    {
        if (!file_exists($filename)) {
            die("The file '$filename' cannot be found.");
        } else {
            $this->file = $filename;
            $this->img['sizes'] = getimagesize($filename);
            $this->img['src'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->file);
            if ($this->crop['default'] == '') {
                $this->crop['default'] = '3/3';
            }

            if (!empty($this->crop['max-display'])
                && ($this->img['sizes'][0] > $this->crop['max-display'][1]
                    || $this->img['sizes'][1] > $this->crop['max-display'][2])) {
                if ($this->crop['max-display'][1] > $this->crop['max-display'][2]) {
                    $this->crop['max-display'][2] = round($this->img['sizes'][1] / ($this->img['sizes'][0] / $this->crop['max-display'][1]));
                } else {
                    $this->crop['max-display'][1] = round(($this->crop['max-display'][2] / $this->img['sizes'][1]) * $this->img['sizes'][0]);
                }
                $this->crop['max-display'][0] = $this->crop['max-display'][1] . 'x' . $this->crop['max-display'][2];
                $this->crop['max-display'][3] = $this->img['sizes'][0] / $this->crop['max-display'][1];
                $this->img['sizes'][0] = $this->crop['max-display'][1];
                $this->img['sizes'][1] = $this->crop['max-display'][2];
            } else {
                $this->crop['max-display'] = array();
            }
        }
        if (!empty($this->params['list'])) {
            $params = array();
            foreach ($this->params['list'] as $key => $val) {
                $params[] = $key . '=' . urlencode($val);
            }
            $this->params['str'] = '&' . join('&', $params);
        }
        include('inc.cropinterface.php');
    }

	/**
	 * Initiates the javascript elements.
	 *
	 * This needs to be called after all other HTML on your page has ended,
	 * but just before the closing body tag.
	 *
	 * @access public
	 * @see file://inc.cropjavascript.php
	 */
	function loadJavascript()
	{
	    include('inc.cropjavascript.php');
	}

	/**
	 * Calculate the width and height based on the size string.
	 *
	 * The size string can be one of three format:
	 *
	 *     o 50x100 - 50 pixels by 100 pixels
	 *     o 4:3 - a ratio of 4 to 3
	 *     o 3/2 - a third of the width and half the height
	 *
	 * @param  string $size
	 * @return array
	 * @access public
	 */
	function calculateCropDimensions($size)
	{
	    if (!isset($this->img['sizes'])) {
	        return array(ccCROPSIZEMIN, ccCROPSIZEMIN);
	    }

        if (strstr($size, 'x')) {
            list($w, $h) = explode('x', $size);
        } else if (strstr($size, '/')) {
            list($dw, $dh) = explode('/', $size);
            $w = round($this->img['sizes'][0] / $dw);
            $h = round($this->img['sizes'][1] / $dh);
        } else {
            list($pw, $ph) = explode(':', $size);
            $w = $this->img['sizes'][0];
            $h = round($ph * $this->img['sizes'][0] / $pw);
            if ($h > $this->img['sizes'][1]) {
                $w = round($this->img['sizes'][1] * $pw / $ph);
                $h = $this->img['sizes'][1];
            }
        }
        if ($w < ccCROPSIZEMIN) {
            $w = ccCROPSIZEMIN;
        }
        if ($h < ccCROPSIZEMIN) {
            $h = ccCROPSIZEMIN;
        }
        if ($w > $this->img['sizes'][0]) {
            $w = $this->img['sizes'][0];
        }
        if ($h > $this->img['sizes'][1]) {
            $h = $this->img['sizes'][1];
        }
        return array($w, $h);
	}

    /**
	 * Determine position of the crop.
	 *
	 * @param  int $cw
	 * @param  int $ch
	 * @return array
     * @access public
	 */
    function calculateCropPosition($cw, $ch)
    {
        if (!isset($this->img['sizes']) || !isset($this->crop['position'])) {
	        return array(0, 0);
	    }

        switch($this->crop['position']) {
            case ccTOPLEFT:
                return array(0, 0);
            case ccTOP:
                return array(ceil(($this->img['sizes'][0] - $cw) / 2), 0);
            case ccTOPRIGHT:
                return array(($this->img['sizes'][0] - $cw), 0);
            case ccLEFT:
                return array(0, ceil(($this->img['sizes'][1] - $ch) / 2));
            case ccCENTRE:
                return array(ceil(($this->img['sizes'][0] - $cw) / 2), ceil(($this->img['sizes'][1] - $ch) / 2));
            case ccRIGHT:
                return array(($this->img['sizes'][0] - $cw), ceil(($this->img['sizes'][1] - $ch) / 2));
            case ccBOTTOMLEFT:
                return array(0, ($this->img['sizes'][1] - $ch));
            case ccBOTTOM:
                return array(ceil(($this->img['sizes'][0] - $cw) / 2), ($this->img['sizes'][1] - $ch));
            case ccBOTTOMRIGHT:
                return array(($this->img['sizes'][0] - $cw), ($this->img['sizes'][1] - $ch));
            default:
                return array(0, 0);
        }
    }


    /**
     * Show an image resized on-the-fly.
     *
     * If the maximum display size is set then we need to resize the image
     * dynamically.  This method will take a filename and dimensions and make
     * the resulting image that size.
     *
     * @param string $imagefile
     * @param int $w
     * @param int $h
     * @access public
     */
    function showImageAtSize($imagefile, $w, $h)
    {
        if ($this->loadImage($imagefile)) {
            if ($this->gdInfo['Truecolor Support']) {
                $this->_imgFinal = imagecreatetruecolor($w, $h);
                imagecopyresampled($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $w, $h, imagesx($this->_imgOrig), imagesy($this->_imgOrig));
                $this->showImage('jpg', 90);
            } else {
                $this->_imgFinal = imagecreate($w, $h);
                imagecopy($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $w, $h, imagesx($this->_imgOrig), imagesy($this->_imgOrig));
                $this->showImage('gif');
            }
        } else {
            $this->_debug("Failed to load image '$imagefile' in CropInterface::showImageAtSize");
        }
    }


}

?>
