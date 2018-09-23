<?php
/**
 * @file        AddCarrier.php
 * @brief       Handles Add Carrier Request
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho 
 * @author      Alexander Jim Abenoja
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
	
	$carrier_id = $_POST['carrier_id'];

	$postfields = array(
			'goAction' 				=> 'goAddCarrier',
			'carrier_type'			=> $_POST['carrier_type'],
			'carrier_id' 			=> $carrier_id,
			'carrier_name'			=> $_POST['carrier_name'],
			'active' 				=> $_POST['active'],
			'protocol'				=> $_POST['protocol'],
			'carrier_description' 	=> $_POST['carrier_description'],
			'user_group' 			=> $_POST['user_group'],
			'authentication' 		=> $_POST['authentication'],
			'username'				=> $_POST['username'],
			'password'				=> $_POST['password'],
			'reg_host'				=> $_POST['reg_host'],
			'reg_port'				=> $_POST['reg_port'],
			'sip_server_ip' 		=> $_POST['sip_server_ip'],
			'codecs' 				=> $_POST['codecs'],
			'dtmf'					=> $_POST['dtmf'],
			'custom_dtmf' 			=> $_POST['custom_dtmf'],
			'dialprefix' 			=> $_POST['dialprefix'],
			'cust_protocol' 		=> $_POST['cust_protocol'],
			'registration_string' 	=> $_POST['registration_string'],
			'account_entry' 		=> $_POST['account_entry'],
			'globals_string' 		=> $_POST['globals_string'],
			'dialplan_entry' 		=> $_POST['dialplan_entry'],
			'manual_server_ip'		=> $_POST['server_ip'],
			'copy_server_ip' 		=> $_POST['copy_server_ip'],
			'source_carrier' 		=> $_POST['source_carrier']
		);

	$output = $api->API_addCarrier($postfields);

	if ($output->result=="success") { 
		$status = 1; 
	} else { 
		$status = $output->result; 
	}

	echo json_encode($status);
?>
