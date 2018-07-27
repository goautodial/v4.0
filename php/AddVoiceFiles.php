<?php
/**
 * @file        AddUserGroup.php
 * @brief       
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author     Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author     Jerico James F. Milo
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

	$postfields = array(
		'goAction' => 'goAddVoiceFiles',
		'files' => curl_file_create($_FILES['voice_file']['tmp_name'], $_FILES['voice_file']['type'], $_FILES["voice_file"]["name"]),
		'stage' => "upload"
	);

	$output = $api->API_addVoiceFiles($postfields);

	header("location: ../audiofiles.php?upload_result=".$output->result);
