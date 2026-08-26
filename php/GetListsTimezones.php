<?php
/**
 * @file        GetListsTimezones.php
 * @brief       Handles Lists Timezones
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
	$output										= $api->API_getTZonesWithCountCalledNCalled($list_id);

	$data 										= '';
	$t											= 0;
	$tcalled 									= [];
	$tncalled 									= [];
    $counter = (isset($output->gmt_offset_now) && is_countable($output->gmt_offset_now) ? count($output->gmt_offset_now) : 0);

	for($t=0;$t<$counter;$t++){
		$gmtOffsetRaw = $output->gmt_offset_now[$t] ?? 0;
		$gmtOffset = is_numeric($gmtOffsetRaw) ? (float) $gmtOffsetRaw : 0;
		$counttList = (int) ($output->counttlist[$t] ?? 0);

		if(($output->called_since_last_reset[$t] ?? '') === 'N'){
			$counttCalled = 0;
			$counttNCalled = $counttList;
		}else{
			$counttCalled = $counttList;
			$counttNCalled = 0;
		}

		$timezone = htmlspecialchars((string) $gmtOffsetRaw, ENT_QUOTES, 'UTF-8');
		$tcalled[] = $counttCalled;
		$tncalled[] = $counttNCalled;
		$data .= '<tr>';
		$data .= '<td>'.$timezone.' ('.gmdate("D M Y H:i", time() + (int) (3600 * $gmtOffset)).')</td>';
		$data .= '<td style="text-align: center; width: 15%;">'.$counttCalled.'</td>';
		$data .= '<td style="text-align: center; width: 15%;">'.$counttNCalled.'</td>';
		$data .= '</tr>';
	}
	$totalt = array_sum($tcalled) + array_sum($tncalled);

	$data .= '<tr>';
	$data .= '<td style="text-align: right;"><b>SUBTOTAL</b></td>';
	$data .= '<td style="text-align: center; width: 15%;">'.array_sum($tcalled).'</td>';
	$data .= '<td style="text-align: center; width: 15%;">'.array_sum($tncalled).'</td>';
	$data .= '</tr>';
	$data .= '<tr>';
	$data .= '<td style="text-align: right;"><b>TOTAL</b></td>';
	$data .= '<td colspan="2" style="text-align: center; width: 30%;">'.$totalt.'</td>';
	$data .= '</tr>';

	echo json_encode($data, true);

?>
