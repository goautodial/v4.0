<?php
/**
 * @file        AddServer.php
 * @brief       Handles Add Server Request
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
/*
require_once('goCRMAPISettings.php');	

	$url = gourl."/goServers/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 		= goUser; #Username goes here. (required)
	$postfields["goPass"] 		= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddServer"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	
	$postfields["server_id"] 		= $_POST['server_id']; 
	$postfields["server_description"] 		= $_POST['server_description']; 
	$postfields["server_ip"] 		= $_POST['server_ip'];
	$postfields["active"] 		= $_POST['active'];
	$postfields["asterisk_version"] 		= $_POST['asterisk_version'];
	$postfields["user_group"] 		= $_POST['user_group'];
	
	$postfields["log_user"] 		= $_POST['log_user']; 
	$postfields["log_group"] 		= $_POST['log_group'];
*/
	$postfields = array(
		'goAction' => 'goAddServer',
		'server_id' => $_POST['server_id'], 
		'server_description' => $_POST['server_description'], 
		'server_ip' => $_POST['server_ip'],
		'active' => $_POST['active'],
		'asterisk_version' => $_POST['asterisk_version'],
		'user_group' => $_POST['user_group']
	);
	
	$output = $api->API_addServer($postfields);

	echo $output->result;;

?>