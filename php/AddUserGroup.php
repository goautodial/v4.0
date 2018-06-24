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
require_once('APIHandler.php');
$api = \creamy\APIHandler::getInstance();

//$url = gourl."/goUserGroups/goAPI.php"; # URL to GoAutoDial API file

$postfields = array(
	'goAction' => 'goAddUserGroup',
	'user_group' => $_POST['usergroup_id'],
	'group_name' => $_POST['groupname'],
	'group_level' => $_POST['grouplevel']
);

$output = $api->API_addUserGroup($postfields);

if ($output->result=="success") {
	$status = 1;
	//$return['msg'] = "New User has been successfully saved.";
} else {
	//$status = 0;
	// $return['msg'] = "Something went wrong please see input data on form.";
	$status = $output->result;
}

echo $status;

?>
