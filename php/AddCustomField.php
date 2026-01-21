<?php
/**
 * @file        AddCustomField.php
 * @brief       Handles Add Custom Field Request
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

	$postfields = array(
			'goAction' => 'goAddCustomFields',
			'list_id' => $_POST['list_id'],
			'field_name' => $_POST['field_name'],
			'field_rank' => $_POST['field_rank'],
			'field_order' => $_POST['field_order'],
			'field_label' => $_POST['field_label'],
			'field_position' => $_POST['field_position'],
			'field_description' => $_POST['field_description'],
			'field_type' => $_POST['field_type'],
			'field_options' => $_POST['field_options'],
			'field_option_position' => $_POST['field_option_position'],
			'field_size' => $_POST['field_size'],
			'field_max' => $_POST['field_max'],
			'field_default' => $_POST['field_default'],
			'field_required' => $_POST['field_required'],
			'log_user' => $_POST['log_user'],
			'log_group' => $_POST['log_group']
		);

	$output = $api->API_addCustomFields($postfields);

	if (!preg_match("/^ERROR/i", $output->result)) {
		$status = "success";
	} else {
		$status = "\n\n".$output->result;
	}

	echo $status;
?>
