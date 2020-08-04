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
require_once('ImageHandler.php');
require_once('APIHandler.php');
require('Session.php');

$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();
$api = \creamy\APIHandler::getInstance();

// check required fields
$validated = 1;
if (!isset($_POST["timezone"])) {
	$validated = 0;
}
if (!isset($_POST["locale"])) {
	$validated = 0;
}

// check if we have a file for the company logo
$customLogoOrigin = NULL;
$imageFileType = NULL;
if ((!empty($_FILES["company_logo"])) && (!empty($_FILES["company_logo"]["name"]))) {
	// check if the image is actually an image.
	$check = getimagesize($_FILES["company_logo"]["tmp_name"]);
    if($check !== false) { // check file size.
		if ($_FILES["company_logo"]["size"] > 2097152) { // max file size 2Mb.
			$reason = $lh->translationFor("image_file_too_large");
			$validated = 0;
		} else { // check file type
			$imageFileType = strtolower(pathinfo($_FILES["company_logo"]["name"], PATHINFO_EXTENSION));
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			    $reason = $lh->translationFor("image_file_wrong_type");
			    $validated = 0;
			} else {
				$customLogoOrigin = $_FILES["company_logo"]["tmp_name"];
			}
		}
    } else {
        $reason = $lh->translationFor("image_file_is_not_image");
        $validated = 0;
    }
	
}

if ($validated == 1) {
	$db = new \creamy\DbHandler();

	// check permissions
	if (!$user->userHasAdminPermission()) {
		$this->lh->translateText("you_dont_have_permission");
		return;
	}
	
	// build data for setting.	
	$timezone = $_POST["timezone"];
	$locale = $_POST["locale"];
	$confirmationEmail = isset($_POST["confirmationEmail"]) ? true : false;
	$eventEmail = isset($_POST["eventEmail"]) ? true : false;
	$theme = $_POST["theme"];
	$baseURL = htmlentities($_POST["base_url"]);
	$minFreq = $_POST["jobScheduling"];
	$customCompanyName = isset($_POST["company_name"]) ? htmlentities($_POST["company_name"]) : null;
	$googleAPIKey = htmlentities($_POST["google_api_key"]);
	$slave_db_ip = htmlentities($_POST["slave_db_ip"]);
	$voicemail_greeting = $_POST["voicemail_greeting"];
	// generate settings array
	$data = array(
		CRM_SETTING_CONFIRMATION_EMAIL => $confirmationEmail, 
		CRM_SETTING_THEME => $theme, 
		CRM_SETTING_TIMEZONE => $timezone, 
		CRM_SETTING_LOCALE => $locale,
		CRM_SETTING_COMPANY_NAME => $customCompanyName,
		CRM_SETTING_EVENTS_EMAIL => $eventEmail,
		CRM_SETTING_JOB_SCHEDULING_MIN_FREQ => $minFreq,
		CRM_SETTING_GOOGLE_API_KEY => $googleAPIKey,
		CRM_SETTING_SLAVE_DB_IP => $slave_db_ip
	);
	if (!empty($baseURL)) { $data[CRM_SETTING_CRM_BASE_URL] = $baseURL; }
	
	// if we have a company custom logo, try to generate if first.
	if (isset($customLogoOrigin)) {
		$ih = new \creamy\ImageHandler();
		$customLogoURL = $ih->generateCustomCompanyLogoAndReturnURL($customLogoOrigin, $imageFileType);
		if (isset($customLogoURL)) { $data[CRM_SETTING_COMPANY_LOGO] = $customLogoURL; }
	}
	
	// set settings
	$result = $db->setSettings($data);

	// allow voicemail greeting
	$result2 = $api->API_editSystemSetting($voicemail_greeting);	

	// return results.
	if ($result === true && $result2->result === 'success') {
		ob_clean();
		print CRM_DEFAULT_SUCCESS_RESPONSE;
	} else {
		ob_clean(); $lh->translateText("error_accessing_database"); 
	}	
} else { ob_clean(); $lh->translateText("some_fields_missing"); }
?>
