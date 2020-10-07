<?php
/**
 * @file        ActivateWhatsapp.php
 * @brief       Activate/Deactivate Whatsapp
 * @copyright   Copyright (C) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
	
	$id = $_POST['action_id'];
	
	$postfields = array(
		'goAction' => 'goWhatsappActivation',
		'action_whatsapp' => $id
	);	
    
	$output = $api->API_WhatsappActivation($postfields);

	if($output->result == "success"){
		$status = $output->result;
	}else{
		$status = "error";
	}
	
	echo $status;
?>
