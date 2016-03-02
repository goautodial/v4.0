<?php
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

namespace creamy;

require_once('CRMDefaults.php');

/** General utilities */
class CRMUtils {
	/** 
	 * Gets the URL for the last directory path of the given URL. 
	 * i.e: http://localhost:8080/creamy/composemail.php => http://localhost:8080/creamy/
	 * Warning: directories not ending in "/" will be slashed, which may not be what you want. 
	 * i.e: http://localhost:8080/creamy/somedir => http://localhost:8080/creamy/
	 */
	static public function getBasedirFromURL($url) {
	    if ($first_query = strpos($url, '?')) $url = substr($url, 0, $first_query);
	    if ($first_fragment = strpos($url, '#')) $url = substr($url, 0, $first_fragment);
	    $last_slash = strrpos($url, '/');
	    if (!$last_slash) {
	        return '/';
	    }
	    if (($first_colon = strpos($url, '://')) !== false && $first_colon + 2 == $last_slash) {
	        return $url . '/';
	    }
	    return substr($url, 0, $last_slash + 1);
	}
	
	/** 
	 * Gets the current URL path of the running script (not this file, but the running script that invoked this method).  
	 * i.e: http://localhost:8080/creamy/index.php	
	 */
	static public function getCurrentURLPath() {
		$port = ""; if ($_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) { $port = ":".$_SERVER["SERVER_PORT"]; }
		$result = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].":".$port.$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
		return $result;
	}
	
	/** Returns an associative array with all the timezones as keys and a friendly description of each of them as values */
	static public function getTimezonesAsArray() {
        $utc = new \DateTimeZone('UTC');
		$dt = new \DateTime('now', $utc);
		$result = array();
		foreach(\DateTimeZone::listIdentifiers() as $tz) {
		    $current_tz = new \DateTimeZone($tz);
		    $offset =  $current_tz->getOffset($dt);
		    $transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
		    $abbr = $transition[0]['abbr'];

			$formatted = sprintf('%+03d:%02u', floor($offset / 3600), floor(abs($offset) % 3600 / 60));
			$result[$tz] = "$tz [ $abbr $formatted ]";
		}
		return $result;
    }
    
	public static function startsWith($haystack, $needle) {
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
	}
	
	public static function endsWith($haystack, $needle) {
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }
	
	    return (substr($haystack, -$length) === $needle);
	}
	
	/** 
	 * Deletes a directory recursively, including all files and directories 
	 * @param String $dir full path for the directory to delete.
	 * @return Bool true if directory and all contained files/directories were successfully deleted, false otherwise.
	 */
	public static function deleteDirectoryRecursively($dir) { 
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) {
			(is_dir($dir.DIRECTORY_SEPARATOR.$file)) ? \creamy\CRMUtils::deleteDirectoryRecursively($dir.DIRECTORY_SEPARATOR.$file) : unlink($dir.DIRECTORY_SEPARATOR.$file); 
		} 
		return rmdir($dir); 
	}

	/**
	 * Returns the base directory path in disk for the current Creamy installation.
	 *. i.e: /var/www/myCreamyCRM/
	 * @param Bool $includeLastSlash if true, adds a final slash (/).
	 */
	public static function creamyBaseDirectoryPath($includeLastSlash = true) {
		$result = dirname(dirname(__FILE__));
		if ($includeLastSlash) $result .= DIRECTORY_SEPARATOR;
		return $result;
	}

	/**
	 * Returns the base creamy URL for this creamy installation, including any root directory (if applicable). 
	 * I.E: returns http://www.yoursite.com/your_crm_path/ (if Creamy is installed there).
	 * This setting must be changed if the server changes.
	 */
	public static function creamyBaseURL() {
		$db = new \creamy\DbHandler();
		return $db->getSettingValueForKey(CRM_SETTING_CRM_BASE_URL);
	}

	/**
	 * returns the file path for the upload directory of this Creamy installation, ending in 
	 * directory_separator.
	 */
	public static function uploadDirectoryPath() {
		return realpath(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.CRM_UPLOADS_DIRNAME).DIRECTORY_SEPARATOR;
	}

	/**
	 * Generates a new upload filepath. The base dir is the uploads directory, appending the
	 * current year and month. Then a randomly generated filename will be used, with the
	 * given extension. The method will create the intermediate directories if they don't exist.
	 * @param String $extension		(optional) Extension to be added to the filename.
	 * @param String $lockFile		If true, touches the file to lock it.
	 */
	public static function generateUploadRelativePath($filename = null, $lockFile = false) {
		require_once('RandomStringGenerator.php');
		$basedir = CRM_UPLOADS_DIRNAME."/".date('Y')."/".date('m')."/";
		$baseDirInDisk = \creamy\CRMUtils::creamyBaseDirectoryPath().$basedir;
		if (!is_dir($baseDirInDisk)) { mkdir($baseDirInDisk, 0775, true); } // create dir if it doesn't exists
		// check filename
		if (empty($filename)) {
			// return a random filename.
			$rnd = new \creamy\RandomStringGenerator();
			$filename = $rnd->generate(CRM_UPLOAD_FILENAME_LENGTH).".dat";
		}
		// check if file already exists.
		$i = 1;
		$filepath = $baseDirInDisk.$filename;
		while (file_exists($filepath)) { // add -$i to filename
			$components = pathinfo($filename, PATHINFO_DIRNAME | PATHINFO_BASENAME | PATHINFO_EXTENSION | PATHINFO_FILENAME);
			$filename = $components["filename"]."-$i".(isset($components["extension"]) ? $components["extension"] : "");
			$filepath = $baseDirInDisk.$filename;
			$i++;
		}
		// lock file (if $lockFile is set) so no other upload can access it.
		touch($filepath);
		// return relative url
		return $basedir.$filename;
	}
	
	/**
	 * Generates a random RGB color. If $includeAlpha is true, it includes an extra "a" value for alpha.
	 * @returns Array an associative array with a random color. i.e: "r" => 23, "g" => 141, "b" => 88.
	 */
	public static function randomRGBAColor($includeAlpha = false) {
		$r = rand(0, 255);
		$g = rand(0, 255);
		$b = rand(0, 255);
		if ($includeAlpha) {
			$a = rand(0, 255);
			return array("r" => $r, "g" => $g, "b" => $b, "a" => $a);
		} else { return array("r" => $r, "g" => $g, "b" => $b); }
	}

	/**
	 * Generates a random Hex color as a string, including the # sign. 
	 * @returns String a RGB hex color. i.e: #FA043B
	 */
	public static function randomHexColor($minRGB = 1, $maxRGB = 255) {
		$r = dechex(rand(0, 255)); if (strlen($r) < 2) $r = "0".$r;
		$g = dechex(rand(0, 255)); if (strlen($g) < 2) $g = "0".$g;
		$b = dechex(rand(0, 255)); if (strlen($b) < 2) $b = "0".$b;
		return "#$r$g$b";
	}

}
?>