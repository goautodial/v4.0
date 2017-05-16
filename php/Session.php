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

// Start session if not already started.
$con = new \creamy\DbHandler();
if (version_compare(phpversion(), '5.4.0', '<')) {
     if(session_id() == '') {
		session_start();
     }
} else {
	if (session_status() == PHP_SESSION_NONE) {
		if (CRM_SESSION_DRIVER == 'database') {
			var_dump('test');
			function on_session_start($save_path, $session_name) {
				//error_log($session_name . " ". session_id());
				var_export("Session created: $session_name");
				return true;
			}
			
			function on_session_end() {
				// Noting to do here...
				var_export("Session closed.");
				return true;
			}
			
			function on_session_read($id) {
				global $con;
				error_log($id);
				//$stmt = "SELECT session_data from sessions ";
				//$stmt .= "where session_id ='$key' ";
				//$stmt .= "and unix_timestamp(session_expiration) > unix_timestamp(date_add(now(),interval 1 hour))";
				//$sth = mysql_query($stmt);
				
				//$con->where('id', $id);
				//$con->where('last_activity', 'UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL 1 HOUR))', '>');
				//$result = $con->getOne('go_sessions', 'user_data');
				//
				//if ($result) {
				//	return($result['user_data']);
				//} else {
				//	return $result;
				//}
				return true;
			}
			
			function on_session_write($id, $data) {
				global $con;
				var_export("$id = $data");
				//$val = addslashes($val);
				//$insert_stmt  = "insert into sessions values('$key', ";
				//$insert_stmt .= "'$val',unix_timestamp(date_add(now(), interval 1 hour)))";
				//
				//$update_stmt  = "update sessions set session_data ='$val', ";
				//$update_stmt .= "session_expiration = unix_timestamp(date_add(now(), interval 1 hour))";
				//$update_stmt .= "where session_id ='$key '";
				//
				//// First we try to insert, if that doesn't succeed, it means
				//// session is already in the table and we try to update
				//
				//
				//mysql_query($insert_stmt);
				//
				//$err = mysql_error();
				//
				//if ($err != 0)
				//{
				//	error_log( mysql_error());
				//	mysql_query($update_stmt);
				//}
			}
			
			function on_session_destroy($id) {
				global $con;
				var_export("Session destroyed.");
				//$id = mysql_real_escape_string($id);
				//$sql = "DELETE
				//	   FROM   sessions
				//	   WHERE  id = '$id'";
				//return mysql_query($sql, $con);
				return true;
			}
			 
			function on_session_gc($max) {
				global $con;
				var_export("Session cleaned.");
			
				//$old = time() - $max;
				//$old = mysql_real_escape_string($old);
				//
				//$sql = "DELETE
				//	   FROM   sessions
				//	   WHERE  access < '$old'";
				//
				//return mysql_query($sql, $con);
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