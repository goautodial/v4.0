<?php
/**
 * @file        AddCarrier.php
 * @brief       Handles Add Carrier Request
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
$api = \creamy\APIHandler::getInstance();
	
	//$url = gourl."/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
	/*
	$postfields["carrier_type"]	= $_POST['carrier_type'];
	$postfields["carrier_id"]	= $carrier_id;
	$postfields["carrier_name"]	= $_POST['carrier_name'];
	$postfields["active"]	= $_POST['active'];
	$postfields["protocol"]	= $_POST['protocol'];
	
	
	if(strtoupper($_POST['carrier_type']) == "MANUAL"){
		$postfields["carrier_description"]	= $_POST['carrier_description'];
		$postfields["user_group"]	= $_POST['user_group'];
		$postfields["authentication"]	= $_POST['authentication'];

		if(strtolower($postfields["authentication"]) == "auth_reg"){
			$postfields["username"]	= $_POST['username'];
			$postfields["password"]	= $_POST['password'];
			$postfields["reg_host"]	= $_POST['reg_host'];
			$postfields["reg_port"]	= $_POST['reg_port'];
		}
		if(strtolower($postfields["authentication"]) == "auth_ip"){
			$postfields["sip_server_ip"]	= $_POST['sip_server_ip'];
		}
		
		if(isset($_POST['codecs'])){
			$codecs = implode("&", $_POST['codecs']);
			$postfields["codecs"]	= $codecs;	
		}
		
		$postfields["dtmf"]	= $_POST['dtmf'];
			
		if(isset($_POST['custom_dtmf']))	
		    $postfields["custom_dtmf"]	= $_POST['custom_dtmf'];
		
		$postfields["dialprefix"]	= $_POST['dialprefix'];
		
		if(strtoupper($_POST['protocol']) == "CUSTOM"){
			$postfields["cust_protocol"]	= $_POST['cust_protocol'];
			$postfields["registration_string"]	= $_POST['registration_string'];
			$postfields["account_entry"]	= $_POST['account_entry'];
			$postfields["global_string"]	= $_POST['globals_string'];
			$postfields["dialplan_entry"]	= $_POST['dialplan_entry'];
		}
		
		$postfields["manual_server_ip"]	= $_POST['server_ip'];
	}
	
	if(strtoupper($_POST['carrier_type']) == "COPY"){
		$postfields["copy_server_ip"]	= $_POST['copy_server_ip'];
		$postfields["source_carrier"]	= $_POST['source_carrier'];
	}
	*/

	$carrier_id = $_POST['carrier_id'];

	$postfields = array(
			'goAction' => 'goAddCarrier',
			'carrier_type'	=> $_POST['carrier_type'],
			'carrier_id' => $carrier_id,
			'carrier_name'	=> $_POST['carrier_name'],
			'active' => $_POST['active'],
			'protocol'	=> $_POST['protocol'],
			'carrier_description' => $_POST['carrier_description'],
			'user_group' => $_POST['user_group'],
			'authentication' => $_POST['authentication'],
			'username'	=> $_POST['username'],
			'password'	=> $_POST['password'],
			'reg_host'	=> $_POST['reg_host'],
			'reg_port'	=> $_POST['reg_port'],
			'sip_server_ip' => $_POST['sip_server_ip'],
			'codecs' => $codecs,
			'dtmf'	=> $_POST['dtmf'],
			'custom_dtmf' => $_POST['custom_dtmf'],
			'dialprefix' => $_POST['dialprefix'],
			'cust_protocol' => $_POST['cust_protocol'],
			'registration_string' => $_POST['registration_string'],
			'account_entry' => $_POST['account_entry'],
			'global_string' => $_POST['globals_string'],
			'dialplan_entry' => $_POST['dialplan_entry'],
			'manual_server_ip'	=> $_POST['server_ip'],
			'copy_server_ip' => $_POST['copy_server_ip'],
			'source_carrier' => $_POST['source_carrier']			
		);

	$output = $api->API_addCarrier($postfields);

	if ($output->result=="success") {
		$status = 1;
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		// $return['msg'] = "Something went wrong please see input data on form.";
		$status = $output->data;
	}

	echo  $status;
?>