<?php
/**
 * @file        GetListsStatuses.php
 * @brief       Handles List Statuses Requests
 * @copyright   Copyright (c) 2018 GOautoial Inc.
 * @author      Noel Umandap
 * @author		Demian Lizandro A. Biscocho
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

	require_once(__DIR__ . '/APIHandler.php');
	$api 										= \creamy\APIHandler::getInstance();
	$list_id 									= ($_POST["list_id"] ?? '');
	$output 									= $api->API_getStatusesWithCountCalledNCalled($list_id);
	$data 										= '';
	$s											= 0;
	$called 									= [];
	$ncalled 									= [];
    $counter = (isset($output->stats) && is_countable($output->stats) ? count($output->stats) : 0);

	for($s=0;$s<$counter;$s++){
		$status = htmlspecialchars((string) ($output->stats[$s] ?? ''), ENT_QUOTES, 'UTF-8');
		$statusName = htmlspecialchars((string) ($output->status_name[$s] ?? ''), ENT_QUOTES, 'UTF-8');
		$countCalled = (int) ($output->is_called[$s] ?? 0);
		$countNotCalled = (int) ($output->not_called[$s] ?? 0);

		$called[] = $countCalled;
		$ncalled[] = $countNotCalled;
		$data 									.= '<tr>';
		$data 									.= '<td>'.$status.'</td>';
		$data 									.= '<td>'.$statusName.'</td>';
		$data 									.= '<td style="text-align: center; width: 15%;">'.$countCalled.'</td>';
		$data 									.= '<td style="text-align: center; width: 15%;">'.$countNotCalled.'</td>';
		$data 									.= '</tr>';
	}

	$total 										= array_sum($called) + array_sum($ncalled);

	$data 										.= '<tr>';
	$data 										.= '<td colspan="2" style="text-align: right;"><b>SUBTOTAL</b></td>';
	$data 										.= '<td style="text-align: center; width: 15%;">'.array_sum($called).'</td>';
	$data 										.= '<td style="text-align: center; width: 15%;">'.array_sum($ncalled).'</td>';
	$data 										.= '</tr>';
	$data 										.= '<tr>';
	$data 										.= '<td colspan="2" style="text-align: right;"><b>TOTAL</b></td>';
	$data 										.= '<td colspan="2" style="text-align: center; width: 30%;">'.$total.'</td>';
	$data 										.= '</tr>';

	echo json_encode($data, true);
?>
