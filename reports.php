<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once('php/goCRMAPISettings.php');

$pageTitle = $_POST['pageTitle'];

$url = gourl."/goJamesReports/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = goUser; #Username goes here. (required)
$postfields["goPass"] = goPass; #Password goes here. (required)
$postfields["goAction"] = "goGetReports"; #action performed by the [[API:Functions]]. (required)
$postfields["responsetype"] = responsetype; #json. (required)
$postfields["pageTitle"] = $pageTitle;

if(isset($_POST["fromDate"])){
	$fromDate = date('Y-m-d H:i:s', strtotime($_POST['fromDate']));
}else{
	$fromDate = date('Y-m-d H:i:s');
}

if($_POST["toDate"] != "" && $_POST["fromDate"] != ""){
	$toDate = date('Y-m-d H:i:s', strtotime($_POST['toDate']));
}else{
	$toDate = date('Y-m-d H:i:s');
}

$postfields["fromDate"] 	= $fromDate;
$postfields["toDate"] 		= $toDate;

if(isset($_POST["campaignID"]))
$postfields["campaignID"] 	= $_POST["campaignID"];

if(isset($_POST["request"]))
$postfields["request"] 		= $_POST["request"];

if(isset($_POST["userID"]))
$postfields["userID"] 		= $_POST["userID"];

if(isset($_POST["userGroup"]))
$postfields["userGroup"] 	= $_POST["userGroup"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$data = curl_exec($ch);
curl_close($ch);
$output = json_decode($data);

if($output->result == "success"){
	echo '<div class="responsive animated bounceInUp">';

// AGENT TIME DETAIL
	if($pageTitle == "agent_detail"){
		$agent_detail = '';
		
		// top table
			$agent_detail .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover" id="agent_detail_top">
					<thead>
						<tr>
				            <th nowrap> Full Name </th>
				            <th nowrap> User Name </th>
				            <th nowrap> Calls </th>
				            <th nowrap> Agent Time </th>
				            <th nowrap> WAIT </th>
				            <th nowrap> Talk </th>
				            <th nowrap> Dispo </th>
				            <th nowrap> Pause </th>
				            <th nowrap> Wrap-Up </th>
				            <th nowrap> Customer </th>
			            </tr>
			        </thead>
			        <tbody>
			';

			if($output->getReports->TOPsorted_output != NULL){
				for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
			    	$agent_detail .= $output->getReports->TOPsorted_output[$i];
			    }
			}else{
				$agent_detail .= "";
			}
				
		    $agent_detail .= '</tbody>';

		    if($output->getReports->TOTcalls != NULL){
			   		$agent_detail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
					    $agent_detail .= $output->getReports->TOT_AGENTS;
					    $agent_detail .= '<th nowrap>'.$output->getReports->TOTcalls.'</th>';
					    $agent_detail .= $output->getReports->TOTALtime;
					    $agent_detail .= $output->getReports->TOTwait;
					    $agent_detail .= $output->getReports->TOTtalk;
					    $agent_detail .= $output->getReports->TOTdispo;
					    $agent_detail .= $output->getReports->TOTpause;
					    $agent_detail .= $output->getReports->TOTdead;
					    $agent_detail .= $output->getReports->TOTcustomer;
					$agent_detail .= '</tr></tfoot>';
				}

			$agent_detail .= '</table></div><br/>'; 


	    // login table
		    if($output->getReports->sub_statusesTOP != NULL){
			    $agent_detail .= '<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="agent_detail_login">
						<thead>
							<tr>';

					            $agent_detail .= $output->getReports->sub_statusesTOP;
				
				$agent_detail .= '</tr>
				        </thead>
				        <tbody>
				';

				if($output->getReports->BOTsorted_output != NULL){
					for($i=0; $i <= count($output->getReports->BOTsorted_output); $i++){
				    	$agent_detail .= $output->getReports->BOTsorted_output[$i];
				    }
				}else{
					$agent_detail .= "";
				}
		   		
		   		$agent_detail .= '</tbody>';

		   		if($output->getReports->SUMstatuses != NULL){
			   		$agent_detail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
					    $agent_detail .= $output->getReports->SUMstatuses;
					$agent_detail .= '</tr></tfoot>';
				}

				$agent_detail .= '</table></div><br/>'; 

			}

	    echo $agent_detail; // return for agent details

	}// end of agent_detail


// AGENT PERFORMANCE DETAIL
	if($pageTitle == "agent_pdetail"){
		$agent_pdetail = '';

		// start of top table
			$agent_pdetail .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover" id="agent_pdetail_top">
					<thead>
						<tr>
				            <th nowrap> Full Name </th>
				            <th nowrap> ID </th>
				            <th nowrap> Calls </th>
				            <th nowrap> Time </th>
							<th nowrap> Pause </th>
							<th nowrap> » Avg </th>
				            <th nowrap> WAIT </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Talk </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Dispo </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Wrap-Up </th>
							<th nowrap> » Avg </th>
				            <th nowrap> Customer </th>
							<th nowrap> » Avg </th>
			            </tr>
			        </thead>
			        <tbody>';
						if($output->getReports->TOPsorted_output != NULL){
							for($i=0; $i <= count($output->getReports->TOPsorted_output); $i++){
						    	$agent_pdetail .= $output->getReports->TOPsorted_output[$i];
						    }
						}else{
							$agent_pdetail .= "";
						}
		   		$agent_pdetail .= '</tbody>';

		   		if($output->getReports->TOPsorted_output != NULL){
			   		$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
							$agent_pdetail .= $output->getReports->TOT_AGENTS;
							$agent_pdetail .= $output->getReports->TOTcalls;
							$agent_pdetail .= $output->getReports->TOTtime_MS;
							$agent_pdetail .= $output->getReports->TOTtotPAUSE_MS;
							$agent_pdetail .= $output->getReports->TOTavgPAUSE_MS;
							$agent_pdetail .= $output->getReports->TOTtotWAIT_MS;
							$agent_pdetail .= $output->getReports->TOTavgWAIT_MS;
							$agent_pdetail .= $output->getReports->TOTtotTALK_MS;
							$agent_pdetail .= $output->getReports->TOTavgTALK_MS;
							$agent_pdetail .= $output->getReports->TOTtotDISPO_MS;
							$agent_pdetail .= $output->getReports->TOTavgDISPO_MS;
							$agent_pdetail .= $output->getReports->TOTtotDEAD_MS;
							$agent_pdetail .= $output->getReports->TOTavgDEAD_MS;
							$agent_pdetail .= $output->getReports->TOTtotCUSTOMER_MS;
							$agent_pdetail .= $output->getReports->TOTavgCUSTOMER_MS;
					$agent_pdetail .= '</tr></tfoot>';
				}

				$agent_pdetail .= '</table></div><br/>'; 
	    // end of top table

	    // start of middle table
			if($output->getReports->MIDsorted_output != NULL){
			    $agent_pdetail .= '<div class="table-responsive">
				    <table class="table table-striped table-bordered table-hover" id="agent_pdetail_mid">
				    	<thead>
							<tr>
					            <th nowrap> Full Name </th>';

								if($output->getReports->SstatusesTOP != NULL){
								    $agent_pdetail .= $output->getReports->SstatusesTOP;
								}
					        
					$agent_pdetail .=  '</tr></thead><tbody>';

						    for($i=0; $i <= count($output->getReports->MIDsorted_output); $i++){
						        $agent_pdetail .= $output->getReports->MIDsorted_output[$i];
						    }

					$agent_pdetail .= '</tbody>';

					if($output->getReports->MIDsorted_output != NULL){
						$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';

							if($output->getReports->SstatusesSUM != NULL){
							    $agent_pdetail .= $output->getReports->SstatusesSUM;
							}

						$agent_pdetail .= '</tr></tfoot>';
					}

					$agent_pdetail .= '</table></div><br/>';
			}
		//end of middle table
		
		// start of legend
				if($output->getReports->MIDsorted_output != NULL){
					$agent_pdetail .= '<table class="table">
					    	<tr><th colspan="2"><small>LEGEND: </th></tr>
					    	<tr><td><small> DNC = DO NOT CALL </small></td><td><small> A = Answering Machine </small></td></tr>
					    	<tr><td><small> N = No Answer </small></td><td><small> MAIL = MAILER REQUESTED </small></td></tr>
					    	<tr><td><small> DC	= Disconnected Number </small></td><td><small> YESJM = WILL VOTE FOR JMEDINA </small></td></tr>
					    	</table><br/>';
			    }
		// end of legend

		// start of bottom table
			$agent_pdetail .= '<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover" id="agent_pdetail_bottom">
			    	<thead>
						<tr>
				            <th nowrap> Full Name </th>
				            <th nowrap> ID </th>
				            <th nowrap> Total </th>
				            <th nowrap> NonPause </th>
				            <th nowrap> Pause </th>
			            </tr>
			        </thead>
			        <tbody>';
					    if($output->getReports->BOTsorted_output != NULL){
						    for($i=0; $i <= count($output->getReports->BOTsorted_output); $i++){
						        $agent_pdetail .= $output->getReports->BOTsorted_output[$i];
						    }
						}else{
							$agent_pdetail .= "";
						}
				$agent_pdetail .= '</tbody>';

				if($output->getReports->BOTsorted_output != NULL){
					$agent_pdetail .= '<tfoot><tr class="warning"><th nowrap> Total </th>';
							$agent_pdetail .= $output->getReports->TOT_AGENTS;
							$agent_pdetail .= $output->getReports->TOTtotTOTAL_MS;
							$agent_pdetail .= $output->getReports->TOTtotNONPAUSE_MS;
							$agent_pdetail .= $output->getReports->TOTtotPAUSEB_MS;
					$agent_pdetail .= '</tr></tfoot>';
				}

				$agent_pdetail .= '</table></div><br/>'; 
		// end of bottom table


		// start of login table
			if($output->getReports->SstatusesBOT != NULL){
				$agent_pdetail .= '<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="agent_pdetail_login">
				    	<thead>
							<tr>';
								$agent_pdetail .= $output->getReports->SstatusesBOT;
								
				    $agent_pdetail .='</tr>
				        </thead>
				        <tbody>';
						    if($output->getReports->SstatusesBOTR != NULL){
							    for($i=0; $i <= count($output->getReports->SstatusesBOTR); $i++){
							        $agent_pdetail .= '<tr>'.$output->getReports->SstatusesBOTR[$i].'</tr>';
							    }
							}else{
								$agent_pdetail .= "";
							}
					$agent_pdetail .= '</tbody>';

					if($output->getReports->SstatusesBOTR != NULL){
						$agent_pdetail .= '<tfoot><tr class="warning">';
									if($output->getReports->SstatusesBSUM != NULL){
									    $agent_pdetail .= $output->getReports->SstatusesBSUM;
									}
						$agent_pdetail .= '</tr></tfoot>';
					}
			}
				$agent_pdetail .= '</table></div><br/>';
		// -- end of login table
		
		echo $agent_pdetail; // return for agent performance detail

	}// end of agent_pdetail

// DIAL STATUSES SUMMARY
	if($pageTitle == "dispo"){
		echo '<div class="table-responsive">'.$output->getReports->TOPsorted_output.'</div>';
	}// end of dispo

// STATISTICAL REPORT
	if($pageTitle == "stats"){
		//var_dump($output->getReports);
		//$increment_color = "009688";
		$max = max(/*$output->getReports->data_calls->hour0, $output->getReports->data_calls->hour1, $output->getReports->data_calls->hour2, $output->getReports->data_calls->hour3, 
				$output->getReports->data_calls->hour4, $output->getReports->data_calls->hour5, $output->getReports->data_calls->hour6, $output->getReports->data_calls->hour7, */
				$output->getReports->data_calls->hour8, $output->getReports->data_calls->hour9, $output->getReports->data_calls->hour10, $output->getReports->data_calls->hour11, 
				$output->getReports->data_calls->hour12, $output->getReports->data_calls->hour13, $output->getReports->data_calls->hour14, $output->getReports->data_calls->hour15, 
				$output->getReports->data_calls->hour16, $output->getReports->data_calls->hour17, $output->getReports->data_calls->hour18, $output->getReports->data_calls->hour19, 
				$output->getReports->data_calls->hour20, $output->getReports->data_calls->hour21, $output->getReports->data_calls->hour22);/*, $hour23);*/
		if($max != NULL){
			$max_count = max($max);
		}else{
			$max_count = $max;
		}
		

		if($max_count <= 4){
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
							<h2><?php echo $output->getReports->total_calls;?></h2>
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
							<h2><?php echo count($output->getReports->data_agents->cuser);?></h2>
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
							<h2><?php echo $output->getReports->total_leads;?></h2>
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
       		if($output->getReports->data_status->status != NULL){
       	?>
    	<div class="row">
        	<div class="col-lg-4">
	       			<?php
	       				for($i = 0; $i < count($output->getReports->data_status->status); $i++){
	       					$percentage_stats = ($output->getReports->data_status->ccount[$i]/$output->getReports->total_calls)*100;
	       					$percentage_stats = number_format($percentage_stats, 2);
	       			?>
	       				<div class="row mb">
	       					<div class="col-lg-6">
	       						<h2 class="pull-right"><?php echo $percentage_stats;?>%</h2>
	       					</div>
	       					<div class="col-lg-6">
	       						<h5><?php echo $output->getReports->data_status->status_name[$i];?> (<?php echo $output->getReports->data_status->status[$i];?>)</h5>
	       						<span class="label label-purple"><?php echo $output->getReports->data_status->ccount[$i];?> calls</span>
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
        $(function(){
            var datav3 = [
                <?php
	                if($output->getReports->data_calls->cdate != NULL){ // if data exists
	                	for($i = 0; $i < count($output->getReports->data_calls->cdate); $i++){
	                		//$color = "#".$increment_color;
	                		//$increment_color = $increment_color + 155001;

							$cdate = $output->getReports->data_calls->cdate[$i];
							$hour0 = $output->getReports->data_calls->hour0[$i];
							if($hour0 == NULL)
								$hour0 = 0;
							
							$hour1 = $output->getReports->data_calls->hour1[$i];
							if($hour1 == NULL){
								$hour1 = 0;
							}
							$hour2 = $output->getReports->data_calls->hour2[$i];
							if($hour2 == NULL){
								$hour2 = 0;
							}
							$hour3 = $output->getReports->data_calls->hour3[$i];
							if($hour3 == NULL){
								$hour3 = 0;
							}
							$hour4 = $output->getReports->data_calls->hour4[$i];
							if($hour4 == NULL){
								$hour4 = 0;
							}
							$hour5 = $output->getReports->data_calls->hour5[$i];
							if($hour5 == NULL){
								$hour5 = 0;
							}
							$hour6 = $output->getReports->data_calls->hour6[$i];
							if($hour6 == NULL){
								$hour6 = 0;
							}
							$hour7 = $output->getReports->data_calls->hour7[$i];
							if($hour7 == NULL){
								$hour7 = 0;
							}
							$hour8 = $output->getReports->data_calls->hour8[$i];
							if($hour8 == NULL){
								$hour8 = 0;
							}
							$hour9 = $output->getReports->data_calls->hour9[$i];
							if($hour9 == NULL){
								$hour9 = 0;
							}
							$hour10 = $output->getReports->data_calls->hour10[$i];
							if($hour10 == NULL){
								$hour10 = 0;
							}
							$hour11 = $output->getReports->data_calls->hour11[$i];
							if($hour11 == NULL){
								$hour11 = 0;
							}
							$hour12 = $output->getReports->data_calls->hour12[$i];
							if($hour12 == NULL){
								$hour12 = 0;
							}
							$hour13 = $output->getReports->data_calls->hour13[$i];
							if($hour13 == NULL){
								$hour13 = 0;
							}
							$hour14 = $output->getReports->data_calls->hour14[$i];
							if($hour14 == NULL){
								$hour14 = 0;
							}
							$hour15 = $output->getReports->data_calls->hour15[$i];
							if($hour15 == NULL){
								$hour15 = 0;
							}
							$hour16 = $output->getReports->data_calls->hour16[$i];
							if($hour16 == NULL){
								$hour16 = 0;
							}
							$hour17 = $output->getReports->data_calls->hour17[$i];
							if($hour17 == NULL){
								$hour17 = 0;
							}
							$hour18 = $output->getReports->data_calls->hour18[$i];
							if($hour18 == NULL){
								$hour18 = 0;
							}
							$hour19 = $output->getReports->data_calls->hour19[$i];
							if($hour19 == NULL){
								$hour19 = 0;
							}
							$hour20 = $output->getReports->data_calls->hour20[$i];
							if($hour20 == NULL){
								$hour20 = 0;
							}
							$hour21 = $output->getReports->data_calls->hour21[$i];
							if($hour21 == NULL){
								$hour21 = 0;
							}
							$hour22 = $output->getReports->data_calls->hour22[$i];
							if($hour22 == NULL){
								$hour22 = 0;
							}
							$hour23 = $output->getReports->data_calls->hour23[$i];
							if($hour23 == NULL){
								$hour23 = 0;
							}
							

							if($hour8 <= 0 && $hour9 <= 0 && 
								$hour10 <= 0 && $hour11 <= 0 && $hour12 <= 0 && $hour13 <= 0 && $hour14 == 0 && $hour15 == 0 && $hour16 == 0 && $hour17 == 0 && $hour18 == 0 && 
								$hour19 == 0 && $hour20 == 0 && $hour21 == 0 && $hour22 == 0){
								$cdate = "";
							}

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
                    /*echo '["11 PM",'. $hour23 .'],';
                    echo '["12 AM",'. $hour0 .'],';
                    echo '["1 AM",'. $hour1 .'],';
                    echo '["2 AM",'. $hour2 .'],';
                    echo '["3 AM",'. $hour3 .'],';
                    echo '["4 AM",'. $hour4 .'],';
                    echo '["5 AM",'. $hour5 .'],';
                    echo '["6 AM",'. $hour6 .'],';
                    echo '["7 AM",'. $hour7 .']';*/
                ?>]}
                <?php 
		                $a = $i + 1;
		                	if(count($output->getReports->data_calls->cdate) > $a)
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
              if(chartv3.length)
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
		  $(function(){

		    var data = [
		    	<?php
		    		if($output->getReports->data_status->status != NULL){
		    			for($i = 0; $i < count($output->getReports->data_status->status); $i++){
		    	?>
					    { "label": <?php echo '"'.$output->getReports->data_status->status_name[$i].'('.$output->getReports->data_status->status[$i].')"'; ?>,
					      "color": getRandomColor(),
					      "data": <?php echo $output->getReports->data_status->ccount[$i]; ?>
					    }
		    	<?php
			    			$a = $i + 1;
		                	if(count($output->getReports->data_status->status) > $a)
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
		    if(chart.length)
		      $.plot(chart, data, options);

		  });

	</script>
		<?php
	}

// SALES PER AGENT
	if($pageTitle == "sales_agent"){
		var_dump($output->getReports);
	}

// SALES TRACKER
	if($pageTitle == "sales_tracker"){
		var_dump($output->getReports);
	}

// EXPORT CALL REPORT
	if($pageTitle == "call_export_report"){
		var_dump($output->getReports);
	}

	echo '</div>';

}else{
	echo $output->result;
}

//print_r($output);
?>
