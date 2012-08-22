<?php

/**
 * Class for handling Images
 *
 * PHP Versions 5.3+
 *
 * flexMVC(tm) : Rapid Development Framework
 * Copyright 2012, Michael Munsie
 *
 * @copyright     Copyright 2012, Michael Munsie, http://mikemunsie.com
 * @link          http://mikemunsie.com
 * @package       flexMVC
 * @since         flexMVC V.1
 */

class Image extends \flexMVC{

/**
 * Image width
 *
 * @public static width
 * @access public
 */
	public static $width;

/**
 * Image mime_type
 *
 * @public static width
 * @access public
 */	
	public static $mime_type;
	
/**
 * Image height
 *
 * @public static height
 * @access public
 */
	public static $height;

/**
 * Image type
 *
 * @public static type
 * @access public
 */
	public static $type;

/**
 * Image size
 *
 * @public static size
 * @access public
 */
	public static $size;
	
/**
 * Image type
 *
 * @public static type
 * @access public
 */
	public static $attr;

/**
 * Resizing dimensions for images
 *
 * @public static resizeDimensions
 * @access public
 */
	public static $resizeDimensions = array(   'mini_thumb' => 33,
									 'thumb' => 85, 
									 'small' => 150, 
									 'medium' => 200, 
									 'width' => NULL, 
									 'height' => NULL, 
									 'cropLeft' => 0, 
									 'cropTop' => 0,
									 'cropRight' => 0,
									 'cropBottom' => 0,
									 'scale' => 0);	
/**
 * Overall quality of created photo (0-100)
 *
 * @public static quality
 * @access public
 */
	public static $quality = 100; 
/**
 * Degree for rotating photos
 *
 * @public static degree
 * @access public
 */
	public static $degree = 0;
/**
 * Temporary image content
 *
 * @public static image
 * @access public
 */
	public static $image = NULL;
/**
 * Extension type for image
 *
 * @public static imageType
 * @access public
 */
	public static $imageType = NULL;
/**
 * RGB value for passing colors
 *
 * @public static rgb
 * @access public
 */
	public static $rgb = array('red' => 255,
					 'green' => 255,
					 'blue' => 255);
/**
 * Size of thumbnail
 *
 * @public static thumbSize
 * @access public
 */
	public static $thumbSize = "thumb";
/**
 * Contrast filter strength
 *
 * @public static contrast
 * @access public
 */
	public static $contrast = 0;
/**
 * Brightness filter strength
 *
 * @public static brightness
 * @access public
 */
	public static $brightness = 0; 
/**
 * Smooth filter strength
 *
 * @public static smooth
 * @access public
 */
	public static $smooth = 0;
/**
 * Pixel block size for pixel filter
 *
 * @public static pixelBlockSize
 * @access public
 */
	public static $pixelBlockSize = 0;
/**
 * Pixel mode for pixel filter (Advanced mode filtering)
 *
 * @public static pixelMode
 * @access public
 */
	public static $pixelMode = FALSE; 
	
/**
 * Autoresize
 *
 * @public static pixelMode
 * @access public
 */	
	public static $autoResize = FALSE;
	
/**
 * Get the information about an image
 *
 * @access public
 */	
	public static function getImageInfo($filename){
		if(file_exists($filename)){
			list(Image::$width, Image::$height, Image::$type, Image::$attr) = Image::$size = getimagesize($filename);
			Image::$mime_type = image_type_to_mime_type(Image::$type);
		}
	}

/**
 * Loads an image and returns true if successful
 *
 * @return boolean Returns TRUE on success, FALSE on failure
 * @access public
 */
	public static function loadImage(){
		$image = NULL;
		$image_info = getimagesize(Image::$filePath);
		$imageType = $image_info[2];
		if($imageType == IMAGETYPE_JPEG){
			$image = imagecreatefromjpeg(Image::$filePath);
		}elseif($imageType == IMAGETYPE_GIF){
			$image = imagecreatefromgif(Image::$filePath);
		}elseif($imageType == IMAGETYPE_PNG){
			$image = imagecreatefrompng(Image::$filePath);
		}
		Image::$image = $image;
		Image::$image_type = $imageType;
		if(!empty($image)) return TRUE;
		return FALSE;
	}

/**
 * Saves an image and returns true if successful
 *
 * @return boolean Returns TRUE on success, FALSE on failure
 * @access public
 */	
	public static function saveImage() {
		if(Image::$image_type == IMAGETYPE_JPEG){
			if(!imagejpeg(Image::$image,Image::$filePath,Image::$quality)) return FALSE;
		}elseif(Image::$image_type == IMAGETYPE_GIF){
			if(!imagegif(Image::$image,Image::$filePath)) return FALSE;         
		}elseif(Image::$image_type == IMAGETYPE_PNG){
			if(!imagepng(Image::$image,Image::$filePath)) return FALSE;
		}
		
		// Reset Dimensions after save
		Image::$resizeDimensions['width'] = NULL;
		Image::$resizeDimensions['height'] = NULL; 
		Image::$resizeDimensions['cropLeft'] = 0; 
		Image::$resizeDimensions['cropTop'] = 0;
		Image::$resizeDimensions['cropRight'] = 0;
		Image::$resizeDimensions['cropBottom'] = 0;
		Image::$resizeDimensions['scale'] = 0;	
		return TRUE;
	}

/**
 * Erases an image from memory and returns true if successful
 *
 * @return boolean Returns TRUE on success, FALSE on failure
 * @access public
 */		
	public static function destroyImage(){
		if(imagedestroy(Image::$image)) return TRUE;
		return FALSE;
	}
	
/**
 * Rectangle crops an image and then resizes based on dimensions
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$resizeDimensions['width'] = 200;
 * Image::$resizeDimensions['height'] = 100;
 * Image::rectangleCrop();
 * }}}
 *
 * @return void
 * @access public
 */		
	public static function rectangleCrop(){
		
		// First things first, resize
		$new_width = Image::$resizeDimensions['width'];
		$new_height = Image::$resizeDimensions['height'];
		
		// Resize Accordingly
		if($new_width > $new_height) Image::$resizeDimensions['height'] = null;
		if($new_height > $new_width) Image::$resizeDimensions['width'] = null;
		Image::resize();
		
		// Get new image information
		Image::loadImage();
		$width  = imagesx(Image::$image);
		$height = imagesy(Image::$image);
		$offsetX = 0;
		$offsetY = 0;	
		
		$crop_left = 0;
		$crop_right = 0;
		$crop_top = 0;
		$crop_bottom = 0;
		
		// Crop Image Accordingly
		if($width > $new_width){
			$crop_left = ceil($width-$new_width)/2;
			$crop_right =  ceil($width-$new_width)/2;
		}
		
		if($height > $new_height){
			$crop_top = ceil($height - $new_height)/2;
			$crop_bottom = ceil($height - $new_height)/2;
		}
		
		Image::$resizeDimensions['cropBottom'] = $crop_bottom;
		Image::$resizeDimensions['cropRight'] = $crop_right;
		Image::$resizeDimensions['cropLeft'] = $crop_left;
		Image::$resizeDimensions['cropTop'] = $crop_top;		 
		Image::crop();
		 
	}		

/**
 * Crops an image based on dimensions
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$resizeDimensions['cropTop'] = 10;
 * Image::$resizeDimensions['cropLeft'] = 10;
 * Image::$resizeDimensions['cropBottom'] = 10;
 * Image::$resizeDimensions['cropRight'] = 10;
 * Image::squareCrop();
 * }}}
 *
 * @return void
 * @access public
 */			
	public static function crop(){
		Image::loadImage();
		$width  = imagesx(Image::$image);
		$height  = imagesy(Image::$image);
	    $new_width = $width-(Image::$resizeDimensions['cropLeft']+Image::$resizeDimensions['cropRight']);
	    $new_height = $height-(Image::$resizeDimensions['cropTop']+Image::$resizeDimensions['cropBottom']);
		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagecopy($new_image, Image::$image, 0, 0, Image::$resizeDimensions['cropLeft'], Image::$resizeDimensions['cropTop'], $new_width, $new_height);
		Image::$image = $new_image;
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Square crops an image and then resizes based on dimensions
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$resizeDimensions['squareSize'] = 50;
 * Image::squareCrop();
 * }}}
 *
 * @return void
 * @access public
 */		
	public static function squareCrop(){
		Image::loadImage();
		$width  = imagesx(Image::$image);
		$height  = imagesy(Image::$image);
		$maxWidth = $width;
		$maxHeight = $height;
		$offsetX	= 0;
		$offsetY	= 0;
		$ratioComputed		= $width / $height;
		$cropRatioComputed	= 1;	
		if ($ratioComputed < $cropRatioComputed){ 
			$origHeight	= $height;
			$height		= $width / $cropRatioComputed;
			$offsetY	= ($origHeight - $height) / 2;
		}else if ($ratioComputed > $cropRatioComputed){ 
			$origWidth	= $width;
			$width		= $height * $cropRatioComputed;
			$offsetX	= ($origWidth - $width) / 2;
		}
		$xRatio		= $maxWidth / $width;
		$yRatio		= $maxHeight / $height;
		if ($xRatio * $height < $maxHeight) { 
			$tnHeight	= ceil($xRatio * $height);
			$tnWidth	= $maxWidth;
		}else{ 
			$tnWidth	= ceil($yRatio * $width);
			$tnHeight	= $maxHeight;
		}
		$new_image = imagecreatetruecolor($tnWidth, $tnHeight);
		imagecopyresampled($new_image, Image::$image, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);
		Image::$image = $new_image;
		Image::saveImage();
		Image::$resizeDimensions['width'] = Image::$resizeDimensions['squareSize'];
		Image::$resizeDimensions['height'] = Image::$resizeDimensions['squareSize'];
		Image::resize();
	}
	
/**
 * Resizes an image based on dimensions
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$resizeDimensions['width'] = 50;
 * Image::$resizeDimensions['height'] = 50;
 * Image::resize();
 * }}}
 *
 * @return void
 * @access public
 */			
	public static function resize(){
		Image::loadImage();
		$width = imagesx(Image::$image);
		$height = imagesy(Image::$image);
		if(!Image::$resizeDimensions['width'] && Image::$resizeDimensions['height']){
			$ratio = Image::$resizeDimensions['height'] / $height;
			Image::$resizeDimensions['width'] = $width * $ratio;
		}
		if(Image::$resizeDimensions['width'] && !Image::$resizeDimensions['height']){
			$ratio = Image::$resizeDimensions['width'] / $width;
			Image::$resizeDimensions['height'] = $height * $ratio;
		}
		$new_image = imagecreatetruecolor(Image::$resizeDimensions['width'], Image::$resizeDimensions['height']);
		imagecopyresampled($new_image, Image::$image, 0, 0, 0, 0, Image::$resizeDimensions['width'], Image::$resizeDimensions['height'], $width, $height);
		Image::$image = $new_image;
		Image::saveImage();
		Image::destroyImage();
	}

 
 /**
 * Scales an image based on percent
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$resizeDimensions['scale'] = 50;
 * Image::scale();
 * }}}
 *
 * @return void
 * @access public
 */	
	public static function scale(){
		Image::loadImage();
		$width = imagesx(Image::$image);
		$height = imagesy(Image::$image);
		Image::$resizeDimensions['width'] = imagesx(Image::$image) * Image::$resizeDimensions['scale']/100;
		Image::$resizeDimensions['height']= imagesy(Image::$image) * Image::$resizeDimensions['scale']/100; 
		$new_image = imagecreatetruecolor(Image::$resizeDimensions['width'], Image::$resizeDimensions['height']);
		imagecopyresampled($new_image, Image::$image, 0, 0, 0, 0, Image::$resizeDimensions['width'], Image::$resizeDimensions['height'], $width, $height);
		Image::$image = $new_image;
		Image::saveImage();
		Image::destroyImage();
	}
	
/**
 * Converts an RGB value to HEX
 *
 * ### Usage:
 *
 * {{{
 * $hex = Image::$rgbToHex("255,255,255");
 * }}}
 *
 * @param string $color
 * @return string Returns a string in hex format
 * @access public
 */		
	public static function rgbToHex($color){
		if(!$color) return false;
		$color = trim($color);
		$out = false;
		if(preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $color)){
			$color = str_replace('#','', $color);
			$l = strlen($color) == 3 ? 1 : (strlen($color) == 6 ? 2 : false);
			if($l){
				unset($out);
				$out[0] = $out['r'] = $out['red'] = hexdec(substr($color, 0,1*$l));
				$out[1] = $out['g'] = $out['green'] = hexdec(substr($color, 1*$l,1*$l));
				$out[2] = $out['b'] = $out['blue'] = hexdec(substr($color, 2*$l,1*$l));
			}else $out = false;      
		}elseif (preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $color)){
			$out = "0x";
			$spr = str_replace(array(',',' ','.'), ':', $color);
			$e = explode(":", $spr);
			if(count($e) != 3) return false;
			for($i = 0; $i<3; $i++)
			$e[$i] = dechex(($e[$i] <= 0)?0:(($e[$i] >= 255)?255:$e[$i])); 
			for($i = 0; $i<3; $i++)
			$out .= ((strlen($e[$i]) < 2)?'0':'').$e[$i];
			$out = strtoupper($out);
		}else $out = false;     
		return $out;
	} 

 /**
 * Rotates an image based on degree and background color
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$rgb['red'] = 255;
 * Image::$rgb['green'] = 255;
 * Image::$rgb['blue'] = 255;
 * Image::$degree = 45;
 * Image::rotate();
 * }}}
 *
 * @return void
 * @access public
 */	 
	public static function rotate(){
		Image::loadImage();
		$backgroundColor = Image::$rgbToHex(Image::$rgb['red'].",".Image::$rgb['green'].",".Image::$rgb['blue']); // change to rgb later
	    Image::$image = imagerotate(Image::$image, Image::$degree, $backgroundColor);
		Image::saveImage();
		Image::destroyImage();
	}
	
/**
 * Rotates an image 180 degrees
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::flip();
 * }}}
 *
 * @return void
 * @access public
 */	 
	public static function flip(){
		Image::$degree = 180;
		Image::rotate();
	}

/**
 * Mirrors an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::mirror();
 * }}}
 *
 * @return void
 * @access public
 */	 
	public static function mirror(){
		Image::loadImage();
		$width = imagesx(Image::$image);
		$height = imagesy(Image::$image);
	    $imgdest = imagecreatetruecolor($width, $height); 
	    for($x=0;$x<$width;$x++){
			for($y=0;$y<$height;$y++){
	             imagecopy($imgdest, Image::$image, $width-$x-1, $y, $x, $y, 1, 1);
	        }
	    }
		Image::$image = $imgdest;
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Creates a thumbnail of image 
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$thumbSize = "small"; // Small or medium
 * Image::thumbnail();
 * }}}
 *
 * @return void
 * @access public
 */	 	
	public static function thumbnail(){
		Image::loadImage();
		$width = imagesx(Image::$image);
		$height = imagesy(Image::$image);
		Image::$resizeDimensions['squareSize'] = Image::$resizeDimensions[Image::$thumbSize];
		Image::$squareCrop();		
	}
	
/**
 * Applies a "grayscale" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::grayscale();
 * }}}
 *
 * @return void
 * @access public
 */	 
	public static function grayscale(){
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_GRAYSCALE); 
		Image::saveImage();
		Image::destroyImage();
	}
	
/**
 * Applies a "negative" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::negative();
 * }}}
 *
 * @return void
 * @access public
 */	 	
	public static function negative(){
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_NEGATE); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "colorize" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$rgb['red'] = 255;
 * Image::$rgb['green'] = 255;
 * Image::$rgb['blue'] = 255;
 * Image::colorize();
 * }}}
 *
 * @return void
 * @access public
 */	 	
	public static function colorize(){
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_COLORIZE, Image::$rgb['red'], Image::$rgb['green'], Image::$rgb['blue']); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "contrast" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$contrast = 5;
 * Image::contrast();
 * }}}
 *
 * @return void
 * @access public
 */	 	
	public static function contrast(){
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_CONTRAST, Image::$contrast); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "brightness" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$brightness = 5;
 * Image::brightness();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function brightness() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_BRIGHTNESS, Image::$brightness); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies an "edgeDetect" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::edgeDetect();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function edgeDetect() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_EDGEDETECT); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies an "emboss" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::emboss();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function emboss() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_EMBOSS); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "Gaussian Blur" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::gaussianBlur();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function gaussianBlur() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_GAUSSIAN_BLUR); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "selective blur" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::selectiveBlur();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function selectiveBlur() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_SELECTIVE_BLUR); 
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "mean removal" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::meanRemoval();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function meanRemoval() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_MEAN_REMOVAL);
		Image::saveImage();
		Image::destroyImage();
	}

/**
 * Applies a "smooth" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$smooth = 5;
 * Image::smooth();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function smooth() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_SMOOTH, Image::$smooth); 
		Image::saveImage();
		Image::destroyImage();
	}
	
/**
 * Applies a "pixelate" filter to an image
 *
 * ### Usage:
 *
 * {{{
 * Image::$filePath = "imageurlhere.ext";
 * Image::$pixelBlockSize = 10;
 * Image::$pixelMode = FALSE; // Advanced pixel filtering
 * Image::pixelate();
 * }}}
 *
 * @return void
 * @access public
 */	 		
	public static function pixelate() {
		Image::loadImage();
		imagefilter(Image::$image, IMG_FILTER_PIXELATE, Image::$pixelBlockSize, Image::$pixelMode); 
		Image::saveImage();
		Image::destroyImage();
	}
}
