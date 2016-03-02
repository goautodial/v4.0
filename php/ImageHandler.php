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
require_once('CRMUtils.php');
require_once('RandomStringGenerator.php');

/**
 * Class to handle all image manipulation.
 */
class ImageHandler {
	
    function __construct() {
    }

	private function generateThumbnailForImage($imgSrc, $imageFileType) {
		//getting the image dimensions
		list($width, $height) = getimagesize($imgSrc);
		
		//saving the image into memory (for manipulation with GD Library)
		if ($imageFileType == "jpg" || $imageFileType == "jpeg") $myImage = imagecreatefromjpeg($imgSrc);
		else if ($imageFileType == "png") $myImage = imagecreatefrompng($imgSrc);
		else if ($imageFileType == "gif") $myImage = imagecreatefromgif($imgSrc);
		
		// calculating the part of the image to use for thumbnail
		if ($width > $height) {
		  $y = 0;
		  $x = ($width - $height) / 2;
		  $smallestSide = $height;
		} else {
		  $x = 0;
		  $y = ($height - $width) / 2;
		  $smallestSide = $width;
		}
		
		// copying the part into thumbnail
		$thumbSize = AVATAR_IMAGE_DEFAULT_SIZE;
		$thumb = imagecreatetruecolor($thumbSize, $thumbSize);
		imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);
		
		//final output
		header('Content-type: image/jpeg');
		return $thumb;	
	}
	
	/**
	 * Processes a image uploaded to the system and generates a processed thumbnail image
	 * suitable for the user avatar. The newly generated file is stored in the avatars images dir
	 * and the relative URL to the file is returned. This relative path can be accessed through URL
	 * by appending the CRMUtils class method creamyBaseURL, and through disk by appending the 
	 * CRMUtils class method creamyBaseDirectoryPath.
	 * @param String $imgSrc image 	file source (i.e: the uploaded $_FILES[parameter][tmp_name][i] value.
	 * @param String $imageFileType image file type. If empty, it will be set to .jpg.
	 * @return a relative path for the generated image. i.e: img/avatars/q4q893pv57hqc9m.jpg or NULL if an error happened.
	 */
	public function generateAvatarFileAndReturnURL($imgSrc, $imageFileType = null) {
		// generate a random image name file (and make sure it's not in use.
		if (empty($imageFileType)) $imageFileType = AVATAR_IMAGE_FILENAME_EXTENSION;
		$rnd = new \creamy\RandomStringGenerator();
		$filename = $rnd->generate(AVATAR_IMAGE_FILENAME_LENGTH).".".$imageFileType;
		
		// get the filepath and check if the file already exists.
		$basedir = \creamy\CRMUtils::creamyBaseDirectoryPath();
		while (file_exists($basedir.AVATAR_IMAGE_FILEDIR.$filename)) {
			$filename = $rnd->generate(AVATAR_IMAGE_FILENAME_LENGTH).".".$imageFileType;
		}
		// touch file (to lock it from other processes trying to write that same filename).
		touch($basedir.AVATAR_IMAGE_FILEDIR.$filename);
		
		// process source image, generating a square image.
		$thumb = $this->generateThumbnailForImage($imgSrc, $imageFileType);
		// if successful, write the image to the generated path and return it.
		if ($thumb) {
			if (imagejpeg($thumb, $basedir.AVATAR_IMAGE_FILEDIR.$filename)) { imagedestroy($thumb); return AVATAR_IMAGE_FILEDIR.$filename; }
			else { imagedestroy($thumb); return null; }
		} else { return NULL; }
	}
	
	/**
	 * Processes a image uploaded to the system to become the custom logo for the company,
	 * to be shown in the header. Any previous file will be deleted. The file will be stored
	 * in the default location img/customCompanyLogo.jpg
	 * @param String $imgSrc image 	file source (i.e: the uploaded $_FILES[parameter][tmp_name][i] value.
	 * @param String $imageFileType image file type. If empty, it will be set to .jpg.
	 * @return a relative path for the generated image. i.e: img/customCompanyLogo.jpg or NULL if an error happened.
	 */
	public function generateCustomCompanyLogoAndReturnURL($imgSrc, $imageFileType = null) {
		$basedir = \creamy\CRMUtils::creamyBaseDirectoryPath();
		$filename = CRM_DEFAULT_COMPANY_LOGO;	
		if ($imageFileType == "jpg" || $imageFileType == "jpeg") $myImage = imagecreatefromjpeg($imgSrc);
		else if ($imageFileType == "png") $myImage = imagecreatefrompng($imgSrc);
		else if ($imageFileType == "gif") $myImage = imagecreatefromgif($imgSrc);
		error_log("image file type = $imageFileType, my image = $myImage, path = ".$basedir.$filename);
		
		imagealphablending($myImage, false);
		imagesavealpha($myImage, true);
		
		if (imagepng($myImage, $basedir.$filename)) { imagedestroy($myImage); return $filename; }
		else { imagedestroy($myImage); return null; }
	}
	
	
	public function removeUserAvatar($avatarpath) {
		$basedir = \creamy\CRMUtils::creamyBaseDirectoryPath();
		if (strpos($basedir.$avatarpath, CRM_DEFAULTS_USER_AVATAR_IMAGE_NAME) === false) { // don't remove default avatars.
			return unlink($basedir.$avatarpath);
		}
		else return true;
	}

}
?>