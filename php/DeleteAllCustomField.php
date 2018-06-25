<?php
/**
 * @file        DeleteAllCustomField.php
 * @brief       Handles Delete Custom Field Requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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

  $url = gourl."/goCustomFields/goAPI.php"; #URL to GoAutoDial API. (required)
  $postfields["goUser"] = goUser; #Username goes here. (required)
  $postfields["goPass"] = goPass; #Password goes here. (required)
  $postfields["goAction"] = "goDeleteAllCustomFields"; #action performed by the [[API:Functions]]. (required)
  $postfields["responsetype"] = responsetype; #json. (required)
  $postfields["list_id"] = $_POST['list_id'];
*/
	$postfields = array(
		'goAction' => 'goDeleteAllCustomFields',
		'list_id' => $_POST['list_id']
	);

	$output = $api->API_Request("goCustomFields", $postfields);

	if ($output->result=="success") {
		$status = "success";
	} else {
		$status = "error";
	}

	echo $status;
?>
