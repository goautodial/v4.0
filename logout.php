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
	require_once('./php/goCRMAPISettings.php');
	require_once('./php/CRMDefaults.php');

	if (CRM_SESSION_DRIVER == 'database') {
		require_once('./php/SessionHandler.php');
		$session_class = new \creamy\SessionHandler();
	} else {
		session_start(); // Starting Session
	}
	
	$log_user = (isset($_SESSION['user']) ? $_SESSION['user'] : '');
	
	if (strlen($log_user) > 0) {
		$log_group = (isset($_SESSION['usergroup']) ? $_SESSION['usergroup'] : '');
		$details = "User {$log_user} logging out";
	} else {
		$log_user = 'sess_expired';
		$details = "Session Expired";
		$log_group = '';
	}
	
	
	$session_destroyed = session_destroy();
	
	if($session_destroyed) // Destroying All Sessions
	{
		$url = gourl."/goAdminLogs/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goLogActions"; #action performed by the [[API:Functions]]
		$postfields["action"] = "LOGOUT";
		$postfields["user"] = $log_user;
		$postfields["user_group"] = $log_group;
		$postfields["details"] = $details;
		$postfields["ip_address"] = $_SERVER['REMOTE_ADDR'];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$output = json_decode($data);
		
		header("Location: login.php"); // Redirecting To Login Page
	}

?>