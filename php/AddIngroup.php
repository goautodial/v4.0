<?php
/**
 * @file        AddIngroup.php
 * @brief       Handles Add Ingroup Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
	$color 										= $_POST["color"];
	$color 										= str_replace("#", '', $color);

	$postfields 								= array(
		'goAction' 									=> 'goAddIngroup',
		'group_id' 									=> $_POST['groupid'],
		'group_name' 								=> $_POST['groupname'],
		'group_color' 								=> $color,
		'active' 									=> $_POST['active'],
		'web_form_address' 							=> $_POST['web_form'],
		'user_group' 								=> $_POST['user_group'],
		'voicemail_ext' 							=> $_POST['ingroup_voicemail'],
		'next_agent_call' 							=> $_POST['next_agent_call'], 
		'fronter_display'							=> $_POST['display'], #Y or N (required)
		'ingroup_script' 							=> $_POST['script'],
		'get_call_launch' 							=> $_POST['call_launch']
	);

	$output 									= $api->API_addIngroup($postfields);

	if ($output->result=="success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}
	
	echo json_encode($status);


?>
