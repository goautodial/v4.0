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

	require_once('APIHandler.php');
	$api 										= \creamy\APIHandler::getInstance();
	$list_id 									= $_POST["list_id"];
	$output										= $api->API_getTZonesWithCountCalledNCalled($list_id);
	
	$data 										= '';
	$t											= 0;
	$tcalled 									= array();
	$tncalled 									= array();
	
	for($t=0;$t<count($output->gmt_offset_now);$t++){
		if($output->called_since_last_reset[$t] == 'N'){
			$counttCalled = 0;
			$counttNCalled = $output->counttlist[$t];

		}else{
			$counttCalled = $output->counttlist[$t];
			$counttNCalled = 0;
		}
		array_push($tcalled, $counttCalled);
		array_push($tncalled, $counttNCalled);
		$data .= '<tr>';
		$data .= '<td>'.$output->gmt_offset_now[$t].' ('.gmdate("D M Y H:i", time() + 3600 * $output->gmt_offset_now[$t]).')</td>';
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
