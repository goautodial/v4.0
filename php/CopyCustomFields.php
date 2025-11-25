<?php
/**
 * @file        CopyCustomFields.php
 * @brief       Modify custom field entries
 * @copyright   Copyright (c) 2025 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho <demian@goautodial.com>
 * @author      Alexander Jim H. Abenoja <dev@goautodial.com>
 * @author		Noel Umandap <dev@goautodial.com>*
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

	$list_to = $_POST['list_to'];
	$list_from = $_POST['list_from'];
	$copy_option = $_POST['copy_option'];

	$postfields = array(
		"goAction" => "goCopyCustomFields", #action performed by the [[API:Functions]]
		"list_to" => $list_to,
		"list_from" => $list_from,
		"copy_option" => $copy_option
	);

	$output = $api->API_Request("goCustomFields", $postfields);

	if ($output->result=="success") { $status = 1; }
		else { $status = $output->result; }

	echo json_encode($status);
?>
