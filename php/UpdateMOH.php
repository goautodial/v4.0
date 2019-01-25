<?php
/**
 * @file        UpdateMOH.php
 * @brief       Handles modifying MOH entries
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

	require_once("APIHandler.php");
    
    $api = \creamy\APIHandler::getInstance();

    $moh_id = $_POST['moh_id'];
    $moh_name = $_POST['moh_name'];
    $user_group = $_POST['user_group'];
    $active = $_POST['active'];
    $filename = $_POST['filename'];
    $random = $_POST['random'];
        
	$postfields = array(
		'goAction' => 'goEditMOH',		
		'moh_id' => $moh_id,
		'moh_name' => $moh_name,
		'user_group' => $user_group,
		'filename' => $filename,
		'active' => $active,
		'random' => $random
	);				

	$output 									= $api->API_editMOH($postfields);
	
	if ($output->result=="success") { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}

	echo json_encode($status);

?>
