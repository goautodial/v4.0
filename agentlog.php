<?php
/**
 * @file        agentlog.php
 * @brief       View agent logs
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
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

	require_once('./php/APIHandler.php');
	$api 									= \creamy\APIHandler::getInstance();

	$user 									= ($_POST['user'] ?? '');
	$start_date								= ($_POST['start_date'] ?? '');
	$end_date								= ($_POST['end_date'] ?? '');
	$agentlog								= ($_POST['agentlog'] ?? '');

	$output 								= $api->API_getAgentLog($user, $start_date, $end_date, $agentlog);
	$data                                   = (isset($output->data) && is_object($output->data)) ? $output->data : new stdClass();
	$i										= 0;

	if ($agentlog == "outbound") {
		$outbound 							= '[';

		$outboundUsers                     = (isset($data->user) && is_countable($data->user)) ? $data->user : [];
		for($i=0;$i<(is_countable($outboundUsers) ? count($outboundUsers) : 0);$i++) {
			if (!empty($data->phone_number[$i] ?? '')) {
				$outbound 					.= '[';
				$outbound 					.= '"'.date('M. d, Y h:i A', strtotime($data->call_date[$i] ?? '')).'",';
				$outbound 					.= '"'.($data->status[$i] ?? '').'",';
				$outbound 					.= '"'.($data->phone_number[$i] ?? '').'",';
				$outbound 					.= '"'.($data->campaign_id[$i] ?? '').'",';
				$outbound 					.= '"'.($data->user_group[$i] ?? '').'",';
				$outbound 					.= '"'.($data->list_id[$i] ?? '').'",';
				$outbound 					.= '"'.($data->lead_id[$i] ?? '').'",';
				$outbound 					.= '"'.($data->term_reason[$i] ?? '').'"';
				$outbound 					.= '],';
			}
		}

		$outbound 							= rtrim($outbound, ",");
		$outbound 							.= ']';

		echo json_encode($outbound);

	} elseif ($agentlog == "inbound") {
		$inbound 							= '[';

		$inboundDates                      = (isset($data->call_date) && is_countable($data->call_date)) ? $data->call_date : [];
		for($i=0;$i<(is_countable($inboundDates) ? count($inboundDates) : 0);$i++) {
			if (!empty($data->phone_number[$i] ?? '')) {
				$inbound 					.= '[';
				$inbound 					.= '"'.date('M. d, Y h:i A', strtotime($data->call_date[$i] ?? '')).'",';
				$inbound 					.= '"'.($data->status[$i] ?? '').'",';
				$inbound 					.= '"'.($data->phone_number[$i] ?? '').'",';
				$inbound 					.= '"'.($data->campaign_id[$i] ?? '').'",';
				$inbound 					.= '"'.($data->user_group[$i] ?? '').'",';
				$inbound 					.= '"'.($data->list_id[$i] ?? '').'",';
				$inbound 					.= '"'.($data->lead_id[$i] ?? '').'",';
				$inbound 					.= '"'.($data->term_reason[$i] ?? '').'"';
				$inbound 					.= '],';
			}
		}

		$inbound 							= rtrim($inbound, ",");
		$inbound 							.= ']';

		echo json_encode($inbound);

	} elseif ($agentlog == "userlog"){
		$userlog 							= '[';

		$userlogIds                        = (isset($data->agent_log_id) && is_countable($data->agent_log_id)) ? $data->agent_log_id : [];
		for($i=0;$i<(is_countable($userlogIds) ? count($userlogIds) : 0);$i++) {
			if (!empty($data->agent_log_id[$i] ?? '')) {
				$userlog 					.= '[';
				$userlog 					.= '"'.($data->agent_log_id[$i] ?? '').'",';
				$userlog 					.= '"'.($data->user[$i] ?? '').'",';
				$userlog 					.= '"'.($data->sub_status[$i] ?? '').'",';
				$userlog 					.= '"'.date('M. d, Y h:i A', strtotime($data->event_time[$i] ?? '')).'",';
				$userlog 					.= '"'.($data->campaign_id[$i] ?? '').'",';
				$userlog 					.= '"'.($data->user_group[$i] ?? '').'"';
				$userlog 					.= '],';
			}
		}

		$userlog 							= rtrim($userlog, ",");
		$userlog 							.= ']';

		echo json_encode($userlog);
	}

?>
