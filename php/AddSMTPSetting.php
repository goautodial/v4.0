<?php

require_once('goCRMAPISettings.php');

	$debug = NULL; if (isset($_POST["debug"])) { 
		$debug = $_POST["debug"]; 
		$debug = stripslashes($debug);
	}
	$ipv6_support = NULL;
	if (isset($_POST["ipv6_support"]))
		$ipv6_support = 1;
	else
		$ipv6_support = 0;
	
	$host = NULL; if (isset($_POST["host"])) { 
		$host = $_POST["host"]; 
		$host = stripslashes($host);
	}
	$port = NULL; if (isset($_POST["port"])) { 
		$port = $_POST["port"]; 
		$port = stripslashes($port);
	}
	$smtp_security = NULL; if (isset($_POST["smtp_security"])) { 
		$smtp_security = $_POST["smtp_security"]; 
		$smtp_security = stripslashes($smtp_security);
	}
	$smtp_auth = NULL;
	if (isset($_POST["smtp_auth"])){
		$smtp_auth = 1; 
	}
	else{
		$smtp_auth = 0;
	}
		
	$username = NULL; if (isset($_POST["username"])) { 
		$username = $_POST["username"]; 
		$username = stripslashes($username);
	}
	$password = NULL; if (isset($_POST["password"])) { 
		$password = $_POST["password"]; 
		$password = stripslashes($password);
	}
	
	$url = gourl."/goSMTP/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"]           = goUser; #Username goes here. (required)
	$postfields["goPass"]           = goPass; #Password goes here. (required)
	$postfields["goAction"]         = "goAddSMTPSettings"; #action performed by the [[API:Functions]]
	$postfields["responsetype"]     = responsetype; #json. (required)
	$postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["timezone"] = "Etc/UTC";
	$postfields["debug"]   	= $debug;
	$postfields["ipv6_support"]  	= $ipv6_support;
	$postfields["host"]           	= $host;
	$postfields["port"]           	= $port;
	$postfields["smtp_security"]           	= $smtp_security;
	$postfields["smtp_auth"]          	= $smtp_auth;
	$postfields["username"]    	= $username;
	$postfields["password"]           	= $password;
	
	$postfields["log_user"]         = $_POST['log_user'];
	$postfields["log_group"]        = $_POST['log_group'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);
//	var_dump($output);

	if ($output->result=="success") {
	   # Result was OK!
		echo $output->result;
	 } else {
	   # An error occured
		echo $output->result;
	}
?>
