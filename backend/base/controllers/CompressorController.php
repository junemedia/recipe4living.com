<?php

/**
 * CSS and Javascript Combinator and Compressor Controller
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package BluApplication
 * @subpackage SharedControllers
 * @copyright Copyright 2006 by Niels Leenheer
 */
class CompressorController extends BackendController
{
	/**
	 * Get compressed content
	 */
	public function view()
	{
		// Settings
		if (DEBUG) {
			$minify = false;
			$cache = false;
		} else {
			$minify = true;
			$cache = true;
		}
		$cacheDir = BLUPATH_CACHE.'/text';
		
		// Check for valid type
		$type = Request::getString('type');
		if (($type != 'css') && ($type != 'js')) {
			$this->_doc->setStatus('HTTP/1.0 503 Not Implemented');
			return;
		}
		
		// Get file list and base dir
		$pos = strrpos(Request::getVar('files'), '/');
		$base = BLUPATH_BASE.'/'.substr($_GET['files'], 0, $pos);
		$files = explode(',', substr($_GET['files'], $pos+1));
		
		// Determine last modification date of the files
		$lastModified = 0;
		while (list(,$file) = each($files)) {
			$path = realpath($base.'/'.$file);
		
			if (($type == 'js' && substr($path, -3) != '.js') || ($type == 'css' && substr($path, -4) != '.css')) {
				$this->_doc->setStatus('HTTP/1.0 403 Forbidden');
				return;
			}
			
			if (!file_exists($path)) {
				$this->_doc->setStatus('HTTP/1.0 404 Not Found');
				return;
			}
		
			$lastModified = max($lastModified, filemtime($path));
		}
		
		// Determine fileset and etag hashes
		$eTag = Utility::makeETag($_GET['files'], $lastModified);
		
		// Set cache control headers
		$this->_doc->setModifiedDate($lastModified);
		$this->_doc->setEtag($eTag);
		$this->_doc->setMaxAge(864000);
		
		// Browser has sent up-to-date ETag?
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			$ifNoneMatch = str_replace('"', '', $_SERVER['HTTP_IF_NONE_MATCH']);
			if ($ifNoneMatch == $eTag) {
				$this->_doc->setStatus('HTTP/1.1 304 Not Modified');
				return;
			}
		}
		
		// Browser has sent up-to-date modification stamp?
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$ifModifiedSince = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);					
			$gmdateMod = gmdate('D, d M Y H:i:s', $lastModified);
			if (strstr($ifModifiedSince, 'GMT')) {
				$gmdateMod .= ' GMT';
			}
			if ($ifModifiedSince == $gmdateMod) {
				$this->_doc->setStatus('HTTP/1.1 304 Not Modified');
				return;
			}
		}
		
		// Determine supported compression method
		$gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
		$deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');
	
		// Determine used compression method
		$encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');
		
		// Check for buggy versions of Internet Explorer
		if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
		preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
			$version = floatval($matches[1]);
			if ($version < 6) { $encoding = 'none'; }
			if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1')) { $encoding = 'none'; }
		}
		
		// Set mime type
		$this->_doc->setMimeType('text/'.$type);

		// Try the cache first to see if the combined files were already generated
		if ($cache)	{
			$filesHash = md5($_GET['files']);
			$cacheFile = $filesHash.'.'.$type.($encoding != 'none' ? '.'.$encoding : '');
			$cachePath = $cacheDir.'/'.$cacheFile;
			
			if (file_exists($cachePath) && (filemtime($cachePath) >= $lastModified)) {
				if ($fh = fopen($cachePath, 'rb')) {
					if (isset($encoding) && $encoding != 'none') {
						$this->_doc->setEncoding($encoding);
					}
					fpassthru($fh);
					fclose($fh);
					return;
				}
			}
		}

		// Get contents of the files
		$contents = '';
		reset($files);
		while (list(,$file) = each($files)) {
			$path = realpath($base.'/'.$file);
			$contents .= "\n\n".file_get_contents($path);
		}

		// Minify
		if ($minify) {
			if ($type=='js') {
				$contents = JSMin::minify($contents);
			} else if ($type=='css') {
				$contents = CSSMin::minify($contents);
			}
		}
		
		// Send contents
		if (isset($encoding) && $encoding != 'none') {
			$contents = gzencode($contents, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
			$this->_doc->setEncoding($encoding);
		}
		echo $contents;
	
		// Store cache
		if ($cache) {
			
			// Create cache directory if it doesn't exist
			if(!is_dir($cacheDir)) {
				mkdir($cacheDir, 0700, true);
			}
			
			// Create cache file
			touch($cachePath);
			chmod($cachePath, 0600);
		
			// Write contents to cache file
			if ($fh = fopen($cachePath, 'wb')) {
				fwrite($fh, $contents);
				fclose($fh);
			}
		}
	}
}
?>
