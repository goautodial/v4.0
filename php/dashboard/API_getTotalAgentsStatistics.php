<?php
/**
 * @file        API_getAgentsStatistics.php
 * @brief       Displays agent statistics (in-call, waiting and paused)
 * @copyright   Copyright (c) 2020 GOautodial Inc.
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
$output 									= $api->API_getTotalAgentsStatistics();

if (empty($output->data)) {
	echo '<div class="row">
		<div class="col-lg-4 col-sm-6">
			<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
				<div class="panel widget bg-purple" style="height: 95px;">
					<div class="row status-box">
						<div class="col-xs-4 text-center bg-purple-dark pv-md">
							<em class="icon-earphones fa-3x"></em>
						</div>
						<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
							<div class="h2 mt0"><span class="text-lg">0</span></div>
							<div class="text-sm">Agent(s) in Call</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-4 col-md-6">
			<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-id="" style="text-decoration : none">
				<div class="panel widget bg-purple" style="height: 95px;">
					<div class="row status-box">
						<div class="col-xs-4 text-center bg-purple-dark pv-md">
							<em class="icon-clock fa-3x"></em>
						</div>
						<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
							<div class="h2 mt0"><span class="text-lg">0</span></div>
							<div class="text-sm">Agent(s) Wating</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12">
			<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="PAUSED" data-id="" style="text-decoration : none">
				<div class="panel widget bg-green" style="height: 95px;">
						<div class="row status-box">
						<div class="col-xs-4 text-center bg-gray-dark pv-md">
							<em class="icon-hourglass fa-3x"></em>
						</div>
						<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
							<div class="h2 mt0"><span class="text-lg">0</span></div>
							<div class="text-sm">Agent(s) on Pause</div>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>';

} else {
	if (is_array($output->data)) {
		foreach ($output->data as $key => $value) {

			$totalAgentsCall 					= (isset($value->totalAgentsCall) ? $value->totalAgentsCall : "0");
			$totalAgentsPaused 					= (isset($value->totalAgentsPaused) ? $value->totalAgentsPaused : "0");
			$totalAgentsWaitCalls 				= (isset($value->totalAgentsWaitCalls) ? $value->totalAgentsWaitCalls : "0");

			echo '<div class="col-lg-4 col-sm-6">
				<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
					<div class="panel widget bg-purple" style="height: 95px;">
						<div class="row status-box">
							<div class="col-xs-4 text-center bg-purple-dark pv-md">
								<em class="icon-earphones fa-3x"></em>
							</div>
							<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
								<div class="h2 mt0"><span class="text-lg">'.$totalAgentsCall.'</span></div>
								<span class="text-sm">Agent(s) in Call</span>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-4 col-md-6">
				<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-id="" style="text-decoration : none">
					<div class="panel widget bg-purple" style="height: 95px;">
						<div class="row status-box">
							<div class="col-xs-4 text-center bg-purple-dark pv-md">
								<em class="icon-clock fa-3x"></em>
							</div>
							<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
								<div class="h2 mt0"><span class="text-lg">'.$totalAgentsWaitCalls.'</span></div>
								<div class="text-sm">Agent(s) Wating</div>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-4 col-md-6 col-sm-12">
				<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="PAUSED" data-id="" style="text-decoration : none">
					<div class="panel widget bg-green" style="height: 95px;">
							<div class="row status-box">
							<div class="col-xs-4 text-center bg-gray-dark pv-md">
								<em class="icon-hourglass fa-3x"></em>
							</div>
							<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
								<div class="h2 mt0"><span class="text-lg">'.$totalAgentsPaused.'</span></div>
								<div class="text-sm">Agent(s) on Paused</div>
							</div>
						</div>
					</div>
				</a>
			</div>';
		}
	}
}
?>
