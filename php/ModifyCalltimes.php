<?php
/**
 * @file        ModifyCallTimes.php
 * @brief       
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja
 * @author		Demian Lizandro A, Biscocho 
 * @author		Jerico James F. Milo
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
	$api = \creamy\APIHandler::getInstance();

	// check required fields
	$reason = "Unable to Modify Call Times";

	$validated = 1;
	if (!isset($_POST["modifyid"])) {
		$validated = 0;
	}

	if ($validated == 1) {		
		// collect new user data.	
		$modifyid = $_POST["modifyid"];		
		$calltime_name = NULL; if (isset($_POST["calltime_name"])) { 
			$calltime_name = $_POST["calltime_name"]; 
			$calltime_name = stripslashes($calltime_name);
		}
		
		$calltime_comments = NULL; if (isset($_POST["calltime_comments"])) { 
			$calltime_comments = $_POST["calltime_comments"];
			$calltime_comments = stripslashes($calltime_comments);
		}

		$usergroup = NULL; if (isset($_POST["usergroup"])) { 
			$usergroup = $_POST["usergroup"]; 
			$usergroup = stripslashes($usergroup);
		}

		$url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
		
		if($_POST['start_default'] != "")
			$start_default =  date('Hi', strtotime($_POST['start_default']));
		else
			$start_default =  "0";
		if($_POST['stop_default'] != "")
			$stop_default =  date('Hi', strtotime($_POST['stop_default']));
		else
			$stop_default =  "0";
			
		if($_POST['start_sunday'] != "")
			$start_sunday =  date('Hi', strtotime($_POST['start_sunday']));
		else
			$start_sunday =  "0";
		if($_POST['stop_sunday'] != "")
			$stop_sunday =  date('Hi', strtotime($_POST['stop_sunday']));
		else
			$stop_sunday =  "0";
			
		if($_POST['start_monday'] != "")
			$start_monday =  date('Hi', strtotime($_POST['start_monday']));
		else
			$start_monday =  "0";
		if($_POST['stop_monday'] != "")
			$stop_monday =  date('Hi', strtotime($_POST['stop_monday']));
		else
			$stop_monday =  "0";
			
		if($_POST['start_tuesday'] != "")
			$start_tuesday =  date('Hi', strtotime($_POST['start_tuesday']));
		else
			$start_tuesday =  "0";
		if($_POST['stop_tuesday'] != "")
			$stop_tuesday =  date('Hi', strtotime($_POST['stop_tuesday']));
		else
			$stop_tuesday =  "0";
			
		if($_POST['start_wednesday'] != "")
			$start_wednesday =  date('Hi', strtotime($_POST['start_wednesday']));
		else
			$start_wednesday =  "0";
		if($_POST['stop_wednesday'] != "")
			$stop_wednesday =  date('Hi', strtotime($_POST['stop_wednesday']));
		else
			$stop_wednesday =  "0";
			
		if($_POST['start_thursday'] != "")
			$start_thursday =  date('Hi', strtotime($_POST['start_thursday']));
		else
			$start_thursday =  "0";
		if($_POST['stop_thursday'] != "")
			$stop_thursday =  date('Hi', strtotime($_POST['stop_thursday']));
		else
			$stop_thursday =  "0";
			
		if($_POST['start_friday'] != "")
			$start_friday =  date('Hi', strtotime($_POST['start_friday']));
		else
			$start_friday =  "0";
		if($_POST['stop_friday'] != "")
			$stop_friday =  date('Hi', strtotime($_POST['stop_friday']));
		else
			$stop_friday =  "0";
			
		if($_POST['start_saturday'] != "")
			$start_saturday =  date('Hi', strtotime($_POST['start_saturday']));
		else
			$start_saturday =  "0";
		if($_POST['stop_saturday'] != "")
			$stop_saturday =  date('Hi', strtotime($_POST['stop_saturday']));
		else
			$stop_saturday =  "0";
		
		$postfields = array(
			"goAction" => "goEditCalltime",		
			"call_time_id" => $modifyid,
			"call_time_name" => $calltime_name,
			"call_time_comments" => $calltime_comments,
			"user_group" => $usergroup,			
			"ct_default_start"  => $start_default,
			"ct_default_stop"   => $stop_default,
			"ct_sunday_start"   => $start_sunday,
			"ct_sunday_stop"    => $stop_sunday,
			"ct_monday_start"   => $start_monday,
			"ct_monday_stop"    => $stop_monday,
			"ct_tuesday_start"  => $start_tuesday,
			"ct_tuesday_stop"   => $stop_tuesday,
			"ct_wednesday_start"=> $start_wednesday,
			"ct_wednesday_stop" => $stop_wednesday,
			"ct_thursday_start" => $start_thursday,
			"ct_thursday_stop"  => $stop_thursday,
			"ct_friday_start"   => $start_friday,
			"ct_friday_stop"    => $stop_friday,
			"ct_saturday_start" => $start_saturday,
			"ct_saturday_stop"  => $stop_saturday,
			"default_audio" => $_POST["audio_default"],
			"sunday_audio" => $_POST["audio_sunday"],
			"monday_audio" => $_POST["audio_monday"],
			"tuesday_audio" => $_POST["audio_tuesday"],
			"wednesday_audio" => $_POST["audio_wednesday"],
			"thursday_audio" => $_POST["audio_thursday"],
			"friday_audio" => $_POST["audio_friday"],
			"saturday_audio" => $_POST["audio_saturday"]		
		);	
		
		$output = $api->API_editCalltime($postfields);

		if ($output->result=="success") { 
			$status = 1; 
		} else { 
			$status = $output->result; 
		}

		echo json_encode($status);	
	} else { 
		//ob_clean(); 
		print $reason; 
	}
?>
