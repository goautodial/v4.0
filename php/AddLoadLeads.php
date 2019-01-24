<?php 
/**
 * @file        AddLoadLeads.php
 * @brief       Handles Upload Leads Request
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap
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

	//ini_set('memory_limit','1024M');
	//ini_set('upload_max_filesize', '6000M');
	//ini_set('post_max_size', '6000M');

	$postfields = array(
		'goAction' => 'goUploadMe',
		'goFileMe' => curl_file_create($_FILES['file_upload']['tmp_name'], $_FILES['file_upload']['type'], $_FILES["file_upload"]["name"]),
		'goListId' => $_REQUEST['list_id'], 
		'goDupcheck' => $_REQUEST['goDupcheck']
	);

	$output = $api->API_addLoadLeads($postfields);

	if ($output->result=="success") { $status = $output->message; } 
		else { $status = $output->result; }

	echo json_encode($status);

?>
