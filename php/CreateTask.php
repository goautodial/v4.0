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

require_once('DbHandler.php');
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('Session.php');

$lh = \creamy\LanguageHandler::getInstance();

// check required fields
$validated = 1;
if (!isset($_POST["taskDescription"])) {
	$validated = 0;
}
if (!isset($_POST["userid"]) && (!isset($_POST["touserid"]))) {
	$validated = 0;
}

if ($validated == 1) {
	$db = new \creamy\DbHandler();

	// check password	
	$userid = (isset($_POST["touserid"])) ? $_POST["touserid"] : $_POST["userid"];
	$taskDescription = $_POST["taskDescription"];
	$taskDescription = stripslashes($taskDescription);
	$taskDescription = $db->escape_string($taskDescription);
	$taskInitialProgress = 0;

	$result = $db->createTask($userid, $taskDescription, $taskInitialProgress);
	if ($result === true) {
		// if the task was assigned to the user by another user, send the receiver a message.
		$user = \creamy\CreamyUser::currentUser();
		if (isset($user)) {
			$myId = $user->getUserId();
			if ($myId != $userid) { // I'm creating a task for another user. Mail that user.
				require_once('MailHandler.php');
				$mh = \creamy\MailHandler::getInstance();
				$mh->sendNewTaskMailToUser($myId, $userid, $taskDescription);
			}
		} 
		// return result	
		ob_clean();
		print CRM_DEFAULT_SUCCESS_RESPONSE; 
	} else { ob_clean(); print $lh->translationFor("unable_create_task")." ($result)"; } 
} else { ob_clean(); $lh->translateText("some_fields_missing"); }
?>