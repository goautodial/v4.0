<?php

require_once('goCRMAPISettings.php');

	// collect new user data.       
	$modifyid = $_POST["modifyid"];

	$server_description = NULL; if (isset($_POST["server_description"])) { 
		$server_description = $_POST["server_description"]; 
		$server_description = stripslashes($server_description);
	}
	$server_ip = NULL; if (isset($_POST["server_ip"])) { 
		$server_ip = $_POST["server_ip"];
		$server_ip = stripslashes($server_ip);
	}
	$active = NULL; if (isset($_POST["active"])) { 
		$active = $_POST["active"]; 
		$active = stripslashes($active);
	}
	$user_group = NULL; if (isset($_POST["user_group"])) { 
		$user_group = $_POST["user_group"]; 
		$user_group = stripslashes($user_group);
	}
	
	$asterisk_version = NULL; if (isset($_POST["asterisk_version"])) { 
		$asterisk_version = $_POST["asterisk_version"]; 
		$asterisk_version = stripslashes($asterisk_version);
	}
	$max_vicidial_trunks = NULL; if (isset($_POST["max_vicidial_trunks"])) { 
		$max_vicidial_trunks = $_POST["max_vicidial_trunks"]; 
		$max_vicidial_trunks = stripslashes($max_vicidial_trunks);
	}
	$outbound_calls_per_second = NULL; if (isset($_POST["outbound_calls_per_second"])) { 
		$outbound_calls_per_second = $_POST["outbound_calls_per_second"]; 
		$outbound_calls_per_second = stripslashes($outbound_calls_per_second);
	}
	$vicidial_balance_active = NULL; if (isset($_POST["vicidial_balance_active"])) { 
		$vicidial_balance_active = $_POST["vicidial_balance_active"]; 
		$vicidial_balance_active = stripslashes($vicidial_balance_active);
	}
	$local_gmt = NULL; if (isset($_POST["local_gmt"])) { 
		$local_gmt = $_POST["local_gmt"]; 
		$local_gmt = stripslashes($local_gmt);
	}
	$generate_vicidial_conf = NULL; if (isset($_POST["generate_vicidial_conf"])) { 
		$generate_vicidial_conf = $_POST["generate_vicidial_conf"]; 
		$generate_vicidial_conf = stripslashes($generate_vicidial_conf);
	}
	$rebuild_conf_files = NULL; if (isset($_POST["rebuild_conf_files"])) { 
		$rebuild_conf_files = $_POST["rebuild_conf_files"]; 
		$rebuild_conf_files = stripslashes($rebuild_conf_files);
	}
	$rebuild_music_on_hold = NULL; if (isset($_POST["rebuild_music_on_hold"])) { 
		$rebuild_music_on_hold = $_POST["rebuild_music_on_hold"]; 
		$rebuild_music_on_hold = stripslashes($rebuild_music_on_hold);
	}

	$url = gourl."/goServers/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"]           = goUser; #Username goes here. (required)
	$postfields["goPass"]           = goPass; #Password goes here. (required)
	$postfields["goAction"]         = "goEditServer"; #action performed by the [[API:Functions]]
	$postfields["responsetype"]     = responsetype; #json. (required)
	$postfields["hostname"] = $_SERVER['SERVER_ADDR']; #Default value
	
	$postfields["server_id"] 	= $modifyid; #Desired script id. (required)
	$postfields["server_description"]   	= $server_description;
	$postfields["server_ip"]  	= $server_ip;
	$postfields["active"]           	= $active;
	$postfields["user_group"]           	= $user_group;
	$postfields["asterisk_version"]           	= $asterisk_version;
	$postfields["max_vicidial_trunks"]          	= $max_vicidial_trunks;
	$postfields["outbound_calls_per_second"]    	= $outbound_calls_per_second;
	$postfields["vicidial_balance_active"]           	= $vicidial_balance_active;
	$postfields["local_gmt"]           	= $local_gmt;
	$postfields["generate_vicidial_conf"]           	= $generate_vicidial_conf;
	$postfields["rebuild_conf_files"]           	= $rebuild_conf_files;
	$postfields["rebuild_music_on_hold"]           	= $rebuild_music_on_hold;
	
	$postfields["log_user"]         = $_POST['log_user'];
	$postfields["log_group"]        = $_POST['log_group'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);
	
	if ($output->result=="success") {
	   # Result was OK!
			echo "success";  
	 } else {
	   # An error occured
			echo $output->result;
	}
?>