<?php
/**
 * @file        ModifyCustomField.php
 * @brief       Modify custom field entries
 * @copyright   Copyright (c) 2025 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho <demian@goautodial.com>
 * @author      Alexander Jim H. Abenoja <dev@goautodial.com>
 * @author		Noel Umandap <dev@goautodial.com>
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

	// check required fields
	$reason = "Unable to Modify Custom Fields";
	$validated = 1;
	if (!isset($_POST["list_id"])) {
		$validated = 0;
	}

	if ($validated == 1) {
		$list_id = $_POST['list_id'];
		$field_id = $_POST['field_id'];
		$field_name = $_POST['field_name'];
		$field_rank = $_POST['field_rank'];
		$field_order = $_POST['field_order'];
		$field_label = $_POST['field_label'];
		$field_label_old = $_POST['field_label_old'];
		$field_position = $_POST['field_position'];
		$field_description = $_POST['field_description'];
		$field_type = $_POST['field_type'];
		$field_options = $_POST['field_options'];
		$field_option_position = $_POST['field_option_position'];
		$field_size = $_POST['field_size'];
		$field_max = $_POST['field_max'];
		$field_default = $_POST['field_default'];
		$field_required = $_POST['field_required'];

		$postfields = array(
			"goAction" => "goModifyCustomField", #action performed by the [[API:Functions]]
			"list_id" => $list_id,
			"field_id" => $field_id,
			"field_name" => $field_name,
			"field_rank" => $field_rank,
			"field_order" => $field_order,
			"field_label" => $field_label,
			"field_label_old" => $field_label_old,
			"field_position" => $field_position,
			"field_description" => $field_description,
			"field_type" => $field_type,
			"field_options" => $field_options,
			"field_option_position" => $field_option_position,
			"field_size" => $field_size,
			"field_max" => $field_max,
			"field_default" => $field_default,
			"field_required" => $field_required
		);

		$output = $api->API_Request("goCustomFields", $postfields);

		if ($output->result=="success") { $status = 1; }
			else { $status = $output->result; }

		echo json_encode($status);
	}
?>
