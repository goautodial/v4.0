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

require_once('CRMDefaults.php');
require_once('DbHandler.php');
require_once('ImageHandler.php');
require_once('LanguageHandler.php');

$lh = \creamy\LanguageHandler::getInstance();

// check required fields
$validated = 1;
$reason = $lh->translationFor("some_fields_missing");
if (!isset($_POST["name"])) {
	$validated = 0;
}
if (!isset($_POST["password1"])) {
	$validated = 0;
}
if (!isset($_POST["password2"])) {
	$validated = 0;
}

$avatarOrigin = NULL;
$imageFileType = NULL;
if ((!empty($_FILES["avatar"])) && (!empty($_FILES["avatar"]["name"]))) {
	// check if the image is actually an image.
	$check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if($check !== false) { // check file size.
		if ($_FILES["avatar"]["size"] > 2097152) { // max file size 2Mb.
			$reason = $lh->translationFor("image_file_too_large");
			$validated = 0;
		} else { // check file type
			$imageFileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			    $reason = $lh->translationFor("image_file_wrong_type");
			    $validated = 0;
			} else {
				$avatarOrigin = $_FILES["avatar"]["tmp_name"];
			}
		}
    } else {
        $reason = $lh->translationFor("image_file_is_not_image");
        $validated = 0;
    }
	
}

if ($validated == 1) {
	$db = new \creamy\DbHandler();

	// check password	
	$name = $_POST["name"];
	$name = stripslashes($name);
	$name = $db->escape_string($name);
	
	$password1 = $_POST["password1"];
	$password2 = $_POST["password2"];
	if ($password1 !== $password2) {
		$lh->translateText("passwords_dont_match");
		exit;
	}
	
	$email = NULL; if (isset($_POST["email"])) { 
		$email = $_POST["email"]; 
		$email = stripslashes($email);
		$email = $db->escape_string($email);
	}
	$phone = NULL; if (isset($_POST["phone"])) { 
		$phone = $_POST["phone"];
		$phone = stripslashes($phone);
		$phone = $db->escape_string($phone); 
	}
	$avatar = NULL;
	if (!empty($avatarOrigin)) {
		$imageHandler = new \creamy\ImageHandler();
		$avatar = $imageHandler->generateAvatarFileAndReturnURL($avatarOrigin, $imageFileType);
		if (empty($avatar)) {
			ob_clean(); 
			$lh->translateText("unable_generate_user_image");
			exit;
		}
	}
	// create user
	$role = CRM_DEFAULTS_USER_ROLE_GUEST; if (isset($_POST["role"])) { $role = $_POST["role"]; } 	
	$result = $db->createUser($name, $password1, $email, $phone, $role, $avatar);

	// analyze result
	if ($result === USER_CREATED_SUCCESSFULLY) { ob_clean(); print CRM_DEFAULT_SUCCESS_RESPONSE; }
	else if ($result === USER_ALREADY_EXISTED) { ob_clean(); $lh->translateText("user_already_exists"); } 
	else if ($result === USER_CREATE_FAILED)   { ob_clean(); $lh->translateText("unable_create_user"); } 
} else {
	ob_clean(); 
	print $reason;
}
?>