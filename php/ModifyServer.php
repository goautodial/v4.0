<?php
/**
 * @file        ModifyServer.php
 * @brief       Handles modifying server requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho 
 * @author      Alexander Jim H. Abenoja
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require_once('APIHandler.php');
	
	$api 										= \creamy\APIHandler::getInstance();

	// collect new user data.       
	$modifyid 									= $_POST["modifyid"];
	$server_description 						= NULL; 
	$server_ip 									= NULL;
	$active 									= NULL;
	$user_group 								= NULL;
	$asterisk_version 							= NULL; 
	$max_vicidial_trunks 						= NULL;
	$outbound_calls_per_second 					= NULL; 
	$vicidial_balance_active 					= NULL; 
	$local_gmt 									= NULL; 
	$generate_vicidial_conf 					= NULL;
	$rebuild_conf_files 						= NULL;
	$rebuild_music_on_hold 						= NULL;
	$recording_web_link 						= NULL;
	$alt_server_ip 								= NULL;
	$external_server_ip 						= NULL;
	$vicidial_balance_rank 						= NULL;
	
	if ( isset($_POST["server_description"]) ) { 
		$server_description 					= $_POST["server_description"]; 
		$server_description 					= stripslashes($server_description);
	}
	 
	if ( isset($_POST["server_ip"]) ) { 
		$server_ip 								= $_POST["server_ip"];
		$server_ip 								= stripslashes($server_ip);
	}
	
	if (isset($_POST["active"])) { 
		$active 								= $_POST["active"]; 
		$active 								= stripslashes($active);
	}
	 
	if ( isset($_POST["user_group"]) ) { 
		$user_group 							= $_POST["user_group"]; 
		$user_group 							= stripslashes($user_group);
	}
	
	if ( isset($_POST["asterisk_version"]) ) { 
		$asterisk_version 						= $_POST["asterisk_version"]; 
		$asterisk_version 						= stripslashes($asterisk_version);
	}
	
	if ( isset($_POST["max_vicidial_trunks"]) ) { 
		$max_vicidial_trunks 					= $_POST["max_vicidial_trunks"]; 
		$max_vicidial_trunks 					= stripslashes($max_vicidial_trunks);
	}
	
	if ( isset($_POST["outbound_calls_per_second"]) ) { 
		$outbound_calls_per_second 				= $_POST["outbound_calls_per_second"]; 
		$outbound_calls_per_second 				= stripslashes($outbound_calls_per_second);
	}
	
	if ( isset($_POST["vicidial_balance_active"]) ) { 
		$vicidial_balance_active 				= $_POST["vicidial_balance_active"]; 
		$vicidial_balance_active 				= stripslashes($vicidial_balance_active);
	}
	
	if ( isset($_POST["local_gmt"]) ) { 
		$local_gmt 								= $_POST["local_gmt"]; 
		$local_gmt 								= stripslashes($local_gmt);
	}
	
	if ( isset($_POST["generate_vicidial_conf"]) ) { 
		$generate_vicidial_conf 				= $_POST["generate_vicidial_conf"]; 
		$generate_vicidial_conf 				= stripslashes($generate_vicidial_conf);
	}
	
	if ( isset($_POST["rebuild_conf_files"]) ) { 
		$rebuild_conf_files 					= $_POST["rebuild_conf_files"]; 
		$rebuild_conf_files 					= stripslashes($rebuild_conf_files);
	}
	
	if ( isset($_POST["rebuild_music_on_hold"]) ) { 
		$rebuild_music_on_hold 					= $_POST["rebuild_music_on_hold"]; 
		$rebuild_music_on_hold 					= stripslashes($rebuild_music_on_hold);
	}
	if ( isset($_POST["recording_web_link"]) ) { 
		$recording_web_link 					= $_POST["recording_web_link"]; 
		$recording_web_link 					= stripslashes($recording_web_link);
	}
	
	if ( isset($_POST["alt_server_ip"]) ) { 
		$alt_server_ip 							= $_POST["alt_server_ip"]; 
		$alt_server_ip 							= stripslashes($alt_server_ip);
	}
	
	if ( isset($_POST["external_server_ip"]) ) { 
		$external_server_ip 					= $_POST["external_server_ip"]; 
		$external_server_ip 					= stripslashes($external_server_ip);
	}
	
	if ( isset($_POST["vicidial_balance_rank"]) ) { 
		$vicidial_balance_rank 					= $_POST["vicidial_balance_rank"]; 
		$vicidial_balance_rank 					= stripslashes($vicidial_balance_rank);
	}

	$postfields 								= array(
		'goAction' 									=> 'goEditServers',		
		'server_id' 								=> $modifyid,
		'server_description' 						=> $server_description,
		'server_ip' 								=> $server_ip,
		'active' 									=> $active,		
		'user_group' 								=> $user_group,
		'asterisk_version' 							=> $asterisk_version,
		'max_vicidial_trunks' 						=> $max_vicidial_trunks,
		'outbound_calls_per_second' 				=> $outbound_calls_per_second,
		'vicidial_balance_active'					=> $vicidial_balance_active,
		'vicidial_balance_rank'						=> $vicidial_balance_rank,
		'local_gmt' 								=> $local_gmt,
		'generate_vicidial_conf' 					=> $generate_vicidial_conf,		
		'rebuild_conf_files' 						=> $rebuild_conf_files,
		'rebuild_music_on_hold' 					=> $rebuild_music_on_hold,
		'recording_web_link' 						=> $recording_web_link,
		'alt_server_ip' 							=> $alt_server_ip,
		'external_server_ip' 						=> $external_server_ip
	);	
			
	$output 									= $api->API_editServer($postfields);
	
	if ($output->result == "success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}
	
	echo json_encode($status);
?>
