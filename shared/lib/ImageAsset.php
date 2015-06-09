<?php

/**
 * Image Asset Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class ImageAsset extends FileAsset
{
	/**
	 * Valid image asset types
	 *
	 * @var array
	 */
	private $_validTypes = array(
		'adimages',
		'blogheaderimages',
		'groupimages',
		'groupphotoimages',
		'indeximages',
		'itemimages',
		'landingimages',
		'marketimages',
		'partnersimages',
		'pressimages',
		'slideshowimages',
		'siteimages',
		'teamimages',
		'tempimages',
		'userimages'
	);

	/**
	 * Render requested image
	 *
	 * @param int Width
	 * @param int Height
	 * @param int Zoom/crop
	 * @param int Quality
	 */
	public function serve($width = null, $height = null, $zoomCrop = 1, $quality = null)
	{
		// Cast arguments
		$width = 	($width ? (int)$width : null);
		$height = 	($height ? (int)$height : null);
		$zoomCrop = (int) $zoomCrop;

		$effect = floor ($zoomCrop/10);
		$zoomCrop = $zoomCrop%10;

		if (!$quality) {
			$quality = BluApplication::getSetting('imageAssetQuality', 80);
		}
		$quality = (int) $quality;

		// Create cached image if we have a valid request
		if ($this->isValid()) {
			$cachePath = $this->_getCachedImage($this->_assetType, $this->_srcPath, $width, $height, $zoomCrop, $quality, $effect);
		} else {
			$cachePath = false;
		}

		// If something went awry, to try to render the default
		if (!$cachePath) {
			$cachePath = $this->_getCachedImage('default', BLUPATH_ASSETS.'/default.jpg', $width, $height, $zoomCrop, $quality, $effect);
		}

		// Serve the file
		return $this->_serveFile($cachePath);
	}

	/**
	 * Check whether the requested image is valid
	 *
	 * @return bool True if valid, false otherwise
	 */
	public function isValid()
	{
		// Check requested type
		if (!in_array($this->_assetType, $this->_validTypes)) {
			return false;
		}

		// Get image details
		if (!$details = $this->getImageDetails()) {
			return false;
		}

		// Check image mime type
		if (!preg_match('/jpg|jpeg|gif|png|bmp/i', $details['mime'])) {
			return false;
		}

		return true;
	}

	/**
	 * Get image size and mime type
	 *
	 * @return array Image details
	 */
	public function getImageDetails()
	{
		static $imageDetails;

		// Check file exists
		if (!is_file($this->_srcPath)) {
			return false;
		}

		// Get image details
		if (!$imageDetails) {
			$imageDetails = getimagesize($this->_srcPath);
		}

		return $imageDetails;
	}

	/**
	 * Create a cached image from requested source
	 *
	 * @param string Asset type
	 * @param string Source image path
	 * @param int Width
	 * @param int Height
	 * @param int Zoom/crop
	 * @param int Quality
	 * @return string Cache path on success, false otherwise
	 */
	private function _getCachedImage($assetType, $srcPath, $width, $height, $zoomCrop, $quality, $effect=0)
	{
		// Determine cache directory and path
		$cacheDir = BLUPATH_CACHE.'/images/'.$assetType.'/'.$width.'x'.$height.'/zc'.(($effect*10)+$zoomCrop).'/'.$quality;
		list($cacheName, $ext) = explode('.', basename($srcPath));
		$cachePath = $cacheDir.'/'.$cacheName.'.jpg';

		// Nothing to do if cache file exists
		if ($this->_cacheExists($cachePath, filemtime($srcPath))) {
			return $cachePath;
		}

		$cachePath = $cacheDir.'/'.$cacheName.'.'.$ext;
		// Double check if its cached native
		if ($this->_cacheExists($cachePath, filemtime($srcPath))) {
			return $cachePath;
		}

		$details = getimagesize($srcPath);
		$mimeType = $details['mime'];

		// Open the existing image
		$image = $this->_createGDImageResouce($srcPath,$mimeType);
		if (!$image) {
			return false;
		}

		// Get original width and height
		$srcWidth = imagesx($image);
		$srcHeight = imagesy($image);

		// Generate new w/h if not provided
		if ($width && !$height) {
			$height = $srcHeight * ($width / $srcWidth);
		} elseif ($height && !$width) {
			$width = $srcWidth * ($height / $srcHeight);
		} elseif (!$width && !$height) {
			$width = $srcWidth;
			$height = $srcHeight;
		}

		// Create a new true color image
		$canvas = imagecreatetruecolor($width, $height);
		imagealphablending ($canvas, false);
		imagesavealpha ($canvas, true);
		$fillRGB = Color::hexToRgb(BluApplication::getSetting('imageFill', 'FFFFFF'));
		$transparency = BluApplication::getSetting('imageFillTransparency', '127');
		$fill = imagecolorallocatealpha($canvas, $fillRGB[0], $fillRGB[1], $fillRGB[2], $transparency);
		imagefilledrectangle($canvas, 0, 0, $width, $height, $fill);

		// Zoom and crop image
		switch ($zoomCrop) {

			// Copy and resize part of an image with resampling
			case 0:
				imagecopyresampled($canvas, $image, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
				break;

			// Fit image to dimensions
			case 1:

				$imWidth = $srcWidth;
				$imHeight = $srcHeight;

				// Constrain width and height, scaling proportionally
				if ($imWidth > $width) {
					$imHeight = floor($imHeight * ($width / $imWidth));
					$imWidth = $width;
				}
				if ($imHeight > $height) {
					$imWidth = floor($imWidth * ($height / $imHeight));
					$imHeight = $height;
				}

				// Calculate x and y coordinate in destination
				$destX = round(($width - $imWidth) / 2);
				$destY = round(($height - $imHeight) / 2);

				imagecopyresampled($canvas, $image, $destX, $destY, 0, 0, $imWidth, $imHeight, $srcWidth, $srcHeight);
				break;

			// Take a chunk of the specified size from the center of the image, and automatically get a bit that isn't white.
			case 2:
				$offset = 10;
				do {
					imagecopyresampled($canvas, $image, 0, 0, $offset, (($srcHeight/2)-($height/2)), $width, $height, $width, $height);
					$notGoodEnough = false;
					$topLeftColor = imagecolorat($canvas,1,1);
					$r = ($topLeftColor >> 16) & 0xFF;
					$g = ($topLeftColor >> 8) & 0xFF;
					$b = $topLeftColor & 0xFF;
					if (($r+$g+$b) > 740) {
						$notGoodEnough = true;
						$offset += 180;
					} else {
						$notGoodEnough = false;
					}
				} while ($notGoodEnough && ($offset < ($srcWidth - $width)));
				break;

			// Crop image to dimensions
			case 3:

				$srcX = $srcY = 0;
				$imWidth = $srcWidth;
				$imHeight = $srcHeight;

				// Calculate ratios for comparison
				$cmpX = $srcWidth  / $width;
				$cmpY = $srcHeight / $height;

				// Calculate x or y coordinate and width or height of source
				if ($cmpX > $cmpY) {
					$imWidth = round(($srcWidth / $cmpX * $cmpY));
					$srcX = round(($srcWidth - ($srcWidth / $cmpX * $cmpY)) / 2);
				} elseif ($cmpY > $cmpX) {
					$imHeight = round(($srcHeight / $cmpY * $cmpX));
					$srcY = round(($srcHeight - ($srcHeight / $cmpY * $cmpX)) / 2);
				}

				imagecopyresampled($canvas, $image, 0, 0, $srcX, $srcY, $width, $height, $imWidth, $imHeight);
				break;
		}

		switch ($effect) {
			// Gaussian Blur
			case '1':
				for ($a=0; $a<20; $a++) {
					imagefilter($canvas, IMG_FILTER_GAUSSIAN_BLUR);
				}
				imagefilter ($canvas, IMG_FILTER_COLORIZE, 255, 255, 255, 10);
			break;
		}

		$returnType = 'jpeg';
		
		if (stristr($mimeType, 'png')) {
			$returnType = 'png';
		}

		// Output image to cache
		$ret = $this->_writeCacheFile($canvas, $cacheDir, $cachePath, $quality, $returnType);

		// Remove image from memory
		imagedestroy($canvas);

		// Return cache path on succes, false otherwise
		return $ret ? $cachePath : false;
	}

	/**
	 * Get GD image resource from requested source image
	 *
	 * @param string Source image path
	 * @return resource GD image resource
	 */
	private function _createGDImageResouce($srcPath, $mimeType)
	{
		// Get image details

		// Create image correctly based on mime type
		if (stristr($mimeType, 'gif')) {
			$image = imagecreatefromgif($srcPath);
		} elseif (stristr($mimeType, 'jpeg')) {
			@ini_set('gd.jpeg_ignore_warning', 1);
			$image = imagecreatefromjpeg($srcPath);
		} elseif (stristr($mimeType, 'png')) {
			$image = imagecreatefrompng($srcPath);
		} elseif (stristr($mimeType, 'bmp')) {
			$image = $this->_imageCreateFromBMP($srcPath);
		}

		// Return image resource
		return $image;
	}

	/**
	 * Write an image resource to the cache file
	 *
	 * @param resource Image resource to write
	 * @param string Cache directory
	 * @param string Cache file path
	 * @param int JPEG quality
	 */
	private function _writeCacheFile($image, $cacheDir, $cachePath, $quality, $returnType = 'jpeg')
	{
		// Create cache directory if it doesn't exist
		if(!is_dir($cacheDir)) {
			@mkdir($cacheDir, 0700, true);
		}

		// Create cache file
		touch($cachePath);
		chmod($cachePath, 0600);

		// Write image to cache file
		switch ($returnType) {
			case "png" :
				return imagepng($image, $cachePath, (9-floor(($quality-1)/10)));
			break;
			default:
				return imagejpeg($image, $cachePath, $quality);
			break;
		}
	}

	/**
	 * Create image from bitmap
	 *
	 * @param string Source filename
	 * @retrun resource Image resource
	 */
	private	function _imageCreateFromBMP($filename)
	{
		// Get unique temp filename
		$tmpName = tempnam('/tmp', 'GD');

		// Convert bitmap to gif
		if (!$this->_convertBMP2GD($filename, $tmpName)) {
			return false;
		}

		// Load image
		$img = imagecreatefromgd($tmpName);

		// Remove temporary file
		unlink($tmpName);

		// Return image resource
		return $img;
	}

	/**
	 * Check whether an up to date file exits in the cache
	 *
	 * @param string Cache file name
	 * @param int Required timestamp
	 * @return bool True if exists and up to date, false otherwise
	 */
	private function _cacheExists($cachePath, $timestamp) {
		return (is_file($cachePath) && (filemtime($cachePath) >= $timestamp));
	}

	/**
	 * Convert bitmap to GD image
	 *
	 * @param string Source image file path
	 * @param resource Destination file path
	 */
	private function _convertBMP2GD($sourceImage, $dest = false)
	{
		if(!($sourceImage_f = fopen($sourceImage, 'rb'))) {
			return false;
		}
		if(!($dest_f = fopen($dest, 'wb'))) {
			return false;
		}
		$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($sourceImage_f, 14));
		$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant", fread($sourceImage_f, 40));

		extract($info);
		extract($header);

		if($type != 0x4D42) { // signature "BM"
			return false;
		}

		$palette_size = $offset - 54;
		$ncolor = $palette_size / 4;
		$gd_header = "";

		// true-color vs. palette
		$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
		$gd_header .= pack("n2", $width, $height);
		$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
		if($palette_size) {
			$gd_header .= pack("n", $ncolor);
		}

		// no transparency
		$gd_header .= "\xFF\xFF\xFF\xFF";

		fwrite($dest_f, $gd_header);

		if($palette_size) {
			$palette = fread($sourceImage_f, $palette_size);
			$gd_palette = "";
			$j = 0;
			while($j < $palette_size) {
				$b = $palette{$j++};
				$g = $palette{$j++};
				$r = $palette{$j++};
				$a = $palette{$j++};
				$gd_palette .= "$r$g$b$a";
			}
			$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
			fwrite($dest_f, $gd_palette);
		}

		$scan_line_size = (($bits * $width) + 7) >> 3;
		$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
				0x03) : 0;

		for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
			// BMP stores scan lines starting from bottom
			fseek($sourceImage_f, $offset + (($scan_line_size + $scan_line_align) *
						$l));
			$scan_line = fread($sourceImage_f, $scan_line_size);
			if($bits == 24) {
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) {
					$b = $scan_line{$j++};
					$g = $scan_line{$j++};
					$r = $scan_line{$j++};
					$gd_scan_line .= "\x00$r$g$b";
				}
			}
			else if($bits == 8) {
				$gd_scan_line = $scan_line;
			}
			else if($bits == 4) {
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) {
					$byte = ord($scan_line{$j++});
					$p1 = chr($byte >> 4);
					$p2 = chr($byte & 0x0F);
					$gd_scan_line .= "$p1$p2";
				}
				$gd_scan_line = substr($gd_scan_line, 0, $width);
			}
			else if($bits == 1) {
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) {
					$byte = ord($scan_line{$j++});
					$p1 = chr((int) (($byte & 0x80) != 0));
					$p2 = chr((int) (($byte & 0x40) != 0));
					$p3 = chr((int) (($byte & 0x20) != 0));
					$p4 = chr((int) (($byte & 0x10) != 0));
					$p5 = chr((int) (($byte & 0x08) != 0));
					$p6 = chr((int) (($byte & 0x04) != 0));
					$p7 = chr((int) (($byte & 0x02) != 0));
					$p8 = chr((int) (($byte & 0x01) != 0));
					$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
				}
				$gd_scan_line = substr($gd_scan_line, 0, $width);
			}

			fwrite($dest_f, $gd_scan_line);
		}
		fclose($sourceImage_f);
		fclose($dest_f);

		return true;
	}

}
?>
