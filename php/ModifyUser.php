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
require_once('LanguageHandler.php');
require_once('DbHandler.php');
require('Session.php');

$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

// check required fields
$reason = $lh->translationFor("unable_modify_user");
$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

// check and process new avatar file if necessary
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

	// collect new user data.	
	$modifyid = $_POST["modifyid"];
	$name = NULL; if (isset($_POST["name"])) { 
		$name = $_POST["name"]; 
		$name = stripslashes($name);
		$name = $db->escape_string($name); 

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
			$lh->translateText("unable_generate_user_image");
			return;
		}
	}
	$userrole = CRM_DEFAULTS_USER_ROLE_GUEST; if (isset($_POST["role"])) { $userrole = $_POST["role"]; } 
		
	// modify user data
	$result = $db->modifyUser($modifyid, $name, $email, $phone, $userrole, $avatar);
	
	// analyze results.
	if ($result === true) {
		if ($modifyid == $user->getUserId()) { // am I modifying myself?
			// if so, update user data locally.
			if (!empty($name)) { $user->setUserName($name); }
			if (!empty($userrole)) { $user->setUserRole($userrole); }
			if (!empty($avatar)) { $user->setUserAvatar($avatar); }
		}
		ob_clean();
		print CRM_DEFAULT_SUCCESS_RESPONSE; 
	} else { ob_clean(); $lh->translateText("unable_modify_user"); };	
} else { ob_clean(); print $reason; }
?>