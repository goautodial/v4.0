<?php
/**
 * @file        reports.php
 * @brief       Handles report requests
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

	require_once('APIHandler.php');
	
	$api 										= \creamy\APIHandler::getInstance();
	$pageTitle									= $_POST['pageTitle'];
	$fromDate 									= date('Y-m-d 00:00:01');
	$toDate 									= date('Y-m-d 23:59:59');
	$campaign_id 								= NULL;
	$request									= NULL;
	$userID										= NULL;
	$userGroup									= NULL;
	$statuses									= NULL;
	
	if (isset($_POST['pageTitle']) && $pageTitle != "call_export_report") {
		$pageTitle 								= $_POST['pageTitle'];
		$pageTitle								= stripslashes($pageTitle);
	}
			
	if (isset($_POST["fromDate"])) {
		$fromDate 								= date('Y-m-d H:i:s', strtotime($_POST['fromDate']));
	}
	
	if ($_POST["toDate"] != "" && $_POST["fromDate"] != "") {
		$toDate 								= date('Y-m-d H:i:s', strtotime($_POST['toDate']));
	}
	
			
	if (isset($_POST["campaignID"])) { 
		$campaign_id 							= $_POST["campaignID"]; 
		$campaign_id 							= stripslashes($campaign_id);
	}
		
	if (isset($_POST["request"])) {
		$request 								= $_POST["request"];
		$request								= stripslashes($request);
	}
			
	if (isset($_POST["userID"])) {
		$userID 								= $_POST["userID"];
		$userID									= stripslashes($userID);
	}
	
	if (isset($_POST["userGroup"])) {
		$userGroup 								= $_POST["userGroup"];
		$userGroup								= stripslashes($userGroup);
	}
		
	if (isset($_POST["statuses"])) {
		$statuses 								= $_POST["statuses"];
		$statuses								= stripslashes($statuses);
	}
		
	$postfields 								= array(
		'goAction'									=> 'goGetStatisticalReports',		
		'pageTitle' 								=> $pageTitle,
		'fromDate' 									=> $fromDate,
		'toDate' 									=> $toDate,
		'campaignID' 								=> $campaign_id,
		'request' 									=> $request,
		'statuses' 									=> $statuses
	);				

	$output 									= $api->API_getStatisticalReports($postfields);

	if ($output->result == "success") {
		echo '<div class="animated bounceInUp">';

	// STATISTICAL REPORT
		if ($pageTitle == "stats") {
			//print_r($output->getReports);
			//$increment_color = "009688";
			if ($_POST["request"] == "daily") {
				$max = max($output->data_calls->hour0, $output->data_calls->hour1, $output->data_calls->hour2, $output->data_calls->hour3, $output->data_calls->hour4, $output->data_calls->hour5, $output->data_calls->hour6, $output->data_calls->hour7, $output->data_calls->hour8, $output->data_calls->hour9, $output->data_calls->hour10, $output->data_calls->hour11, $output->data_calls->hour12, $output->data_calls->hour13, $output->data_calls->hour14, $output->data_calls->hour15, $output->data_calls->hour16, $output->data_calls->hour17, $output->data_calls->hour18, $output->data_calls->hour19, $output->data_calls->hour20, $output->data_calls->hour21, $output->data_calls->hour22, $hour23);
			}
			if ($_POST["request"] == "weekly") {
				$max = max($output->data_calls->Day0, $output->data_calls->Day1, $output->data_calls->Day2, $output->data_calls->Day3, $output->data_calls->Day4, $output->data_calls->Day5, $output->data_calls->Day6);
			}
			if ($_POST["request"] == "monthly") {
				$max = max($output->data_calls->Month1, $output->data_calls->Month2, $output->data_calls->Month3, $output->data_calls->Month4, $output->data_calls->Month5, $output->data_calls->Month6, $output->data_calls->Month7, $output->data_calls->Month8, $output->data_calls->Month9, $output->data_calls->Month10, $output->data_calls->Month11, $output->data_calls->Month12);
			}
			
			if ($max != NULL) {
				$max_count = max($max);
			}else{
				$max_count = $max;
			}			

			if ($max_count <= 4) {
				$max_count = 4;
			}

		?>
			<div collapse="panelChart9" class="panel-wrapper">
				<div class="panel-body">
				<div class="chart-splinev1 flot-chart"></div> <!-- data is in JS -> demo-flot.js -> search (Overall/Home/Pagkain)--> 
				</div>
			</div>
			<div id="legendBox"></div>

			<br/><br/>
			<legend><small>Call Statistics</small></legend>
				
			<div class="row">
				<div class="col-lg-4">
					<div class="panel widget bg-gray-light" style="height: 95px;">
						<div class="row status-box">
							<div class="col-xs-6 text-center bg-gray-lighter pv-md animated pulse">
								<h2><?php echo $output->total_calls;?></h2>
							</div>
							<div class="col-xs-6 pv-lg">
								<div class="text-sm">Total Calls</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="panel widget bg-gray-light" style="height: 95px;">
						<div class="row status-box">
							<div class="col-xs-6 text-center bg-gray-lighter pv-md animated pulse">
								<h2><?php echo count($output->data_agents->cuser);?></h2>
							</div>
							<div class="col-xs-6 pv-lg">
								<div class="text-sm">Total Agents</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="panel widget bg-gray-light" style="height: 95px;">
						<div class="row status-box">
							<div class="col-xs-6 text-center bg-gray-lighter pv-md animated pulse">
								<h2><?php echo $output->total_leads;?></h2>
							</div>
							<div class="col-xs-6 pv-lg">
								<div class="text-sm">Lead Count</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<br/><br/>
			<legend><small>Disposition Stats</small></legend>

			<?php 
				if ($output->data_status->status != NULL) {
			?>
			<div class="row">
				<div class="col-lg-4">
						<?php
							for($i = 0; $i < count($output->data_status->status); $i++) {
								$percentage_stats = ($output->data_status->ccount[$i]/$output->total_calls)*100;
								$percentage_stats = number_format($percentage_stats, 2);
						?>
							<div class="row mb">
								<div class="col-lg-6">
									<h2 class="pull-right"><?php echo $percentage_stats;?>%</h2>
								</div>
								<div class="col-lg-6">
									<h5><?php echo $output->data_status->status_name[$i];?> (<?php echo $output->data_status->status[$i];?>)</h5>
									<span class="label label-purple"><?php echo $output->data_status->ccount[$i];?> calls</span>
								</div>
							</div>	
						<?php
							}
						?>
				</div>

				<div class="col-lg-8">
					<div class="panel-body">
					<div class="chart-pie flot-chart"></div>
					</div>
				</div>
			</div>
			<?php
				}else{
			?>
				<div class="row mb">
					<center><h3> - - - NO DATA - - - </h3></center>
				</div>	
			<?php
				}
			?>

		<script>
			$(function() {
				var datav3 = [
				
				<?php
						if ($_POST["request"] == "daily") {
							if ($output->data_calls->cdate != NULL) { // if data exists
								for($i = 0; $i < count($output->data_calls->cdate); $i++) {
									$cdate = $output->data_calls->cdate[$i];
									$hour0 = $output->data_calls->hour0[$i];
										if ($hour0 == NULL)$hour0 = 0;
									$hour1 = $output->data_calls->hour1[$i];
										if ($hour1 == NULL)$hour1 = 0;
									$hour2 = $output->data_calls->hour2[$i];
										if ($hour2 == NULL)$hour2 = 0;
									$hour3 = $output->data_calls->hour3[$i];
										if ($hour3 == NULL)$hour3 = 0;
									$hour4 = $output->data_calls->hour4[$i];
										if ($hour4 == NULL)$hour4 = 0;
									$hour5 = $output->data_calls->hour5[$i];
										if ($hour5 == NULL)$hour5 = 0;
									$hour6 = $output->data_calls->hour6[$i];
										if ($hour6 == NULL)$hour6 = 0;
									$hour7 = $output->data_calls->hour7[$i];
										if ($hour7 == NULL)$hour7 = 0;
									$hour8 = $output->data_calls->hour8[$i];
										if ($hour8 == NULL)$hour8 = 0;
									$hour9 = $output->data_calls->hour9[$i];
										if ($hour9 == NULL)$hour9 = 0;
									$hour10 = $output->data_calls->hour10[$i];
										if ($hour10 == NULL)$hour10 = 0;
									$hour11 = $output->data_calls->hour11[$i];
										if ($hour11 == NULL)$hour11 = 0;
									$hour12 = $output->data_calls->hour12[$i];
										if ($hour12 == NULL)$hour12 = 0;
									$hour13 = $output->data_calls->hour13[$i];
										if ($hour13 == NULL)$hour13 = 0;
									$hour14 = $output->data_calls->hour14[$i];
										if ($hour14 == NULL)$hour14 = 0;
									$hour15 = $output->data_calls->hour15[$i];
										if ($hour15 == NULL)$hour15 = 0;
									$hour16 = $output->data_calls->hour16[$i];
										if ($hour16 == NULL)$hour16 = 0;
									$hour17 = $output->data_calls->hour17[$i];
										if ($hour17 == NULL)$hour17 = 0;
									$hour18 = $output->data_calls->hour18[$i];
										if ($hour18 == NULL)$hour18 = 0;
									$hour19 = $output->data_calls->hour19[$i];
										if ($hour19 == NULL)$hour19 = 0;
									$hour20 = $output->data_calls->hour20[$i];
										if ($hour20 == NULL)$hour20 = 0;
									$hour21 = $output->data_calls->hour21[$i];
										if ($hour21 == NULL)$hour21 = 0;
									$hour22 = $output->data_calls->hour22[$i];
										if ($hour22 == NULL)$hour22 = 0;
									$hour23 = $output->data_calls->hour23[$i];
										if ($hour23 == NULL)$hour23 = 0;
				?>
					{"label": <?php echo '"'.$cdate.'"';?>,
					"color": getRandomColor(),
					"data": [
						<?php
							echo '["8 AM",'. $hour8 .'],';
							echo '["9 AM",'. $hour9 .'],';
							echo '["10 AM",'. $hour10 .'],';
							echo '["11 AM",'. $hour11 .'],';
							echo '["12 NN",'. $hour12 .'],';
							echo '["1 PM",'. $hour13 .'],';
							echo '["2 PM",'. $hour14 .'],';
							echo '["3 PM",'. $hour15 .'],';
							echo '["4 PM",'. $hour16 .'],';
							echo '["5 PM",'. $hour17 .'],';
							echo '["6 PM",'. $hour18 .'],';
							echo '["7 PM",'. $hour19 .'],';
							echo '["8 PM",'. $hour20 .'],';
							echo '["9 PM",'. $hour21 .'],';
							echo '["10 PM",'. $hour22 .']';
							echo ']}';
							/*echo '["11 PM",'. $hour23 .'],';
							echo '["12 AM",'. $hour0 .'],';
							echo '["1 AM",'. $hour1 .'],';
							echo '["2 AM",'. $hour2 .'],';
							echo '["3 AM",'. $hour3 .'],';
							echo '["4 AM",'. $hour4 .'],';
							echo '["5 AM",'. $hour5 .'],';
							echo '["6 AM",'. $hour6 .'],';
							echo '["7 AM",'. $hour7 .']';*/

								$a = $i + 1;
									if (count($output->data_calls->cdate) > $a)
									echo ", ";
								}
							}else{ //if data is null
									$cdate = "";
				?>
								{"label": <?php echo '"'.$cdate.'"';?>,
								"color": getRandomColor(),
								"data": [
				<?php
									echo '["8 AM", 0],';
									echo '["9 AM", 0],';
									echo '["10 AM", 0],';
									echo '["11 AM", 0],';
									echo '["12 NN", 0],';
									echo '["1 PM", 0],';
									echo '["2 PM", 0],';
									echo '["3 PM", 0],';
									echo '["4 PM", 0],';
									echo '["5 PM", 0],';
									echo '["6 PM", 0],';
									echo '["7 PM", 0],';
									echo '["8 PM", 0],';
									echo '["9 PM", 0],';
									echo '["10 PM", 0]';
									/*echo '["11 PM",'. $hour15 .'],';
									echo '["12 AM",'. $hour16 .'],';
									echo '["1 AM",'. $hour17 .'],';
									echo '["2 AM",'. $hour18 .'],';
									echo '["3 AM",'. $hour19 .'],';
									echo '["4 AM",'. $hour20 .'],';
									echo '["5 AM",'. $hour21 .'],';
									echo '["6 AM",'. $hour22 .'],';
									echo '["7 AM",'. $hour23 .']';*/
									echo "]}";
							}
						} //end of daily

						else if ($_POST["request"] == "weekly") { // weekly
							if ($output->data_calls->weekno != NULL) {
								for($i = 0; $i < count($output->data_calls->weekno); $i++) {
									$weekno = $output->data_calls->weekno[$i];
									$day0 = $output->data_calls->Day0[$i];
										if ($day0 == NULL)$day0 = 0;
									$day1 = $output->data_calls->Day1[$i];
										if ($day1 == NULL)$day1 = 0;
									$day2 = $output->data_calls->Day2[$i];
										if ($day2 == NULL)$day2 = 0;
									$day3 = $output->data_calls->Day3[$i];
										if ($day3 == NULL)$day3 = 0;
									$day4 = $output->data_calls->Day4[$i];
										if ($day4 == NULL)$day4 = 0;
									$day5 = $output->data_calls->Day5[$i];
										if ($day5 == NULL)$day5 = 0;
									$day6 = $output->data_calls->Day6[$i];
										if ($day6 == NULL)$day6 = 0;
				?>
					{"label": <?php echo '"'.$weekno.'"';?>,
					"color": getRandomColor(),
					"data": [
						<?php
							echo '["Mon",'. $day0 .'],';
							echo '["Tue",'. $day1 .'],';
							echo '["Wed",'. $day2 .'],';
							echo '["Thu",'. $day3 .'],';
							echo '["Fri",'. $day4 .'],';
							echo '["Sat",'. $day5 .'],';
							echo '["Sun",'. $day6 .']';
							echo ']}';

								$a = $i + 1;
									if (count($output->data_calls->weekno) > $a)
									echo ", ";
								}
							}else{ //if data is null
									$weekno = "";
				?>
								{"label": <?php echo '"'.$weekno.'"';?>,
								"color": getRandomColor(),
								"data": [
				<?php
									echo '["Mon", 0],';
									echo '["Tue", 0],';
									echo '["Wed", 0],';
									echo '["Thu", 0],';
									echo '["Fri", 0],';
									echo '["Sat", 0],';
									echo '["Sun", 0]';
									echo ']}';
							}
				}
						else if ($_POST["request"] == "monthly") { // weekly
							if ($output->data_calls->monthname != NULL) {
								for($i = 0; $i < count($output->data_calls->monthname); $i++) {
									$monthname = $output->data_calls->monthname[$i];
									$month1 = $output->data_calls->Month1[$i];
										if ($month1 == NULL)$month1 = 0;
									$month2 = $output->data_calls->Month2[$i];
										if ($month2 == NULL)$month2 = 0;
									$month3 = $output->data_calls->Month3[$i];
										if ($month3 == NULL)$month3 = 0;
									$month4 = $output->data_calls->Month4[$i];
										if ($month4 == NULL)$month4 = 0;
									$month5 = $output->data_calls->Month5[$i];
										if ($month5 == NULL)$month5 = 0;
									$month6 = $output->data_calls->Month6[$i];
										if ($month6 == NULL)$month6 = 0;
									$month7 = $output->data_calls->Month7[$i];
										if ($month7 == NULL)$month7 = 0;
									$month8 = $output->data_calls->Month8[$i];
										if ($month8 == NULL)$month8 = 0;
									$month9 = $output->data_calls->Month9[$i];
										if ($month9 == NULL)$month9 = 0;
									$month10 = $output->data_calls->Month10[$i];
										if ($month10 == NULL)$month10 = 0;
									$month11 = $output->data_calls->Month11[$i];
										if ($month11 == NULL)$month11 = 0;
									$month12 = $output->data_calls->Month12[$i];
										if ($month12 == NULL)$month12 = 0;
					?>
						{"label": <?php echo '"'.$monthname.'"';?>,
						"color": getRandomColor(),
						"data": [
				<?php
							echo '["Jan",'. $month1 .'],';
							echo '["Feb",'. $month2 .'],';
							echo '["Mar",'. $month3 .'],';
							echo '["Apr",'. $month4 .'],';
							echo '["May",'. $month5 .'],';
							echo '["Jun",'. $month6 .'],';
							echo '["Jul",'. $month7 .'],';
							echo '["Aug",'. $month8 .'],';
							echo '["Sep",'. $month9 .'],';
							echo '["Oct",'. $month10 .'],';
							echo '["Nov",'. $month11 .'],';
							echo '["Dec",'. $month12 .']';
							echo ']}';

								$a = $i + 1;
									if (count($output->data_calls->monthname) > $a)
									echo ", ";
								}
							}else{ //if data is null
									$monthname = "";
				?>
								{"label": <?php echo '"'.$monthname.'"';?>,
								"color": getRandomColor(),
								"data": [
				<?php
									echo '["Jan", 0],';
									echo '["Feb", 0],';
									echo '["Mar", 0],';
									echo '["Apr", 0],';
									echo '["May", 0],';
									echo '["Jun", 0],';
									echo '["Jul", 0],';
									echo '["Aug", 0],';
									echo '["Sep", 0],';
									echo '["Oct", 0],';
									echo '["Nov", 0],';
									echo '["Dec", 0]';
									echo ']}';
							}
						}

					
				?>
				];

				var options = { series: { lines: {show: false}, points: {show: true,radius: 4},
						splines: {show: true,tension: 0.4,lineWidth: 1,fill: 0.5}
					},
					grid: { borderColor: '#eee', borderWidth: 1, hoverable: true, backgroundColor: '#fcfcfc' },
					tooltip: true, 
					tooltipOpts: { content: function (label, x, y) { return y + ' Calls in ' + label; } },
					xaxis: { tickColor: '#fcfcfc', mode: 'categories' },
					yaxis: { min: 0, max: <?php echo $max_count;?>, // optional: use it for a clear represetation
						tickColor: '#eee',
						//position: 'right' or 'left',
						tickFormatter: function (v) { return v/* + ' visitors'*/; }
					},
					shadowSize: 0,
					legend: {
						show:true, 
						noColumns: 8, 
						container: $('#legendBox')
					}
				};
				var chartv3 = $('.chart-splinev1');
				if (chartv3.length)
					$.plot(chartv3, datav3, options);
			});

			function getRandomColor() {
				var letters = '0123456789ABCDEF';
				var color = '#';
				for (var i = 0; i < 6; i++ ) {
					color += letters[Math.floor(Math.random() * 16)];
				}
				return color;
			}

			// CHART PIE
			// ----------------------------------- 
			$(function() {

				var data = [
					<?php
						if ($output->data_status->status != NULL) {
							for($i = 0; $i < count($output->data_status->status); $i++) {
					?>
							{ "label": <?php echo '"'.$output->data_status->status_name[$i].'('.$output->data_status->status[$i].')"'; ?>,
							"color": getRandomColor(),
							"data": <?php echo $output->data_status->ccount[$i]; ?>
							}
					<?php
								$a = $i + 1;
								if (count($output->data_status->status) > $a)
								echo ", ";
							}
						}else{
					?>
							{ "label": "",
							"color": getRandomColor(),
							"data": 0
							}
					<?php
						}
					?>
				];

				var options = {
					series: {
						pie: {
							show: true,
							innerRadius: 0,
							label: {
								show: true,
								radius: 1,
								formatter: function (label, series) {
									return '<div class="flot-pie-label">' +
									//label + ' : ' +
									Math.round(series.percent) +
									'%</div>';
								},
								background: {
									opacity: 0.8,
									color: '#222'
								}
							}
						}
					}
				};

				var chart = $('.chart-pie');
				if (chart.length)
				$.plot(chart, data, options);

			});
		</script>
		
		<?php
		}
	}
//print_r($output);
?>
