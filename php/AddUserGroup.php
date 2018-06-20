<?php
/**
 * @file        AddUserGroup.php
 * @brief       
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
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

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

$url = gourl."/goUserGroups/goAPI.php"; # URL to GoAutoDial API file	
$postfields = array(
	'goUser' => goUser,
	'goPass' => goPass,
	'goAction' => 'goAddUserGroup',		
	'responsetype' => responsetype,
	'user_group' => $_POST['usergroup_id'],
	'group_name' => $_POST['groupname'],
	'group_level' => $_POST['grouplevel'],
	'session_user' => $_POST['log_user'],
	'log_group' => $_POST['log_group'],
	'hostname' => $_SERVER['REMOTE_ADDR']
);		

// Call the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
$data = curl_exec($ch);
curl_close($ch);
$output = json_decode($data);

if ($output->result=="success") {
	# Result was OK!
	$status = 1;
	//$return['msg'] = "New User has been successfully saved.";
} else {
	# An error occured
	//$status = 0;
	// $return['msg'] = "Something went wrong please see input data on form.";
	$status = $output->result;
}

echo $status;

?>
