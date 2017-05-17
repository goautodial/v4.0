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

if (CRM_SESSION_DRIVER == 'database') {
	require_once('./php/SessionHandler.php');
	$session_class = new \creamy\SessionHandler();
}

// Start session if not already started.
if (version_compare(phpversion(), '5.4.0', '<')) {
     if(session_id() == '') {
		session_start();
     }
} else {
	if (session_status() == PHP_SESSION_NONE) {
		if (CRM_SESSION_DRIVER == 'database') {
			require_once('./php/DbHandler.php');
			
			function on_session_start($save_path, $session_name) {
				//error_log($session_name . " ". session_id());
				//error_log("Session created: $session_name");
			}
			
			function on_session_end() {
				// Noting to do here...
				//error_log("Session closed.");
			}
			
			function on_session_read($id) {
				$sDB = new \creamy\DbHandler();
				//error_log($id);
				$result = $sDB->onSessionRead($id);
				
				if ($result) {
					return($result['user_data']);
				} else {
					return $result;
				}
			}
			
			function on_session_write($id, $data) {
				$sDB = new \creamy\DbHandler();
				$postData = array(
					'session_id' => $sDB->escape_string($id),
					'user_data' => $sDB->escape_string($data),
					'last_activity' => time(),
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'user_agent' => $_SERVER['HTTP_USER_AGENT']
				);
				
				$result = $sDB->onSessionWrite($postData);
			}
			
			function on_session_destroy($id) {
				$sDB = new \creamy\DbHandler();
				//var_dump("Session destroyed.");
				$result = $sDB->onSessionDestroy($id);
				return $result;
			}
			 
			function on_session_gc($max) {
				global $con;
				//var_dump("Session cleaned.");
				$result = $sDB->onSessionGC($max);
				return true;
			}
			
			session_set_save_handler('on_session_start',
								'on_session_end',
								'on_session_read',
								'on_session_write',
								'on_session_destroy',
								'on_session_gc');
		}
		session_start();
	}
}

// force https protocol
if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

if (!isset($_SESSION["username"])) {
	header('Location: login.php'); // Redirecting To Login Page
}
if (!isset($_SESSION["userid"])) {
	header('Location: login.php'); // Redirecting To Login Page
}
if (!isset($_SESSION["userrole"])) {
	$_SESSION["userrole"] = CRM_DEFAULTS_USER_ROLE_GUEST; // no privileged account by default.
}
if (!isset($_SESSION["avatar"])) {
	$_SESSION["avatar"] = CRM_DEFAULTS_USER_AVATAR;
}

include_once('CreamyUser.php');
?>