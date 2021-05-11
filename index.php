<?php
/**
 * @file 		index.php
 * @brief 		Dashboard application
 * @copyright 	Copyright (c) 2020 GOautodial Inc. 
 * @author     	Christopher Lomuntad 
 * @author		Demian Lizandro A. Biscocho
 * @author		Alexander Jim H. Abenoja
 * @author		Ignacio Nieto Carvajal
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
**/

	// check if Creamy has been installed.
	require_once('./php/CRMDefaults.php');
	require_once('./php/APIHandler.php');
	require_once('./php/UIHandler.php');
	require_once('./php/LanguageHandler.php');
	require_once('./php/DbHandler.php');
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
		
	// Try to get the authenticated user.
	require_once('./php/Session.php');
	try {
		$user = \creamy\CreamyUser::currentUser();	
	} catch (\Exception $e) {
		header("location: ./logout.php");
		die();
	}
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}


	$perms = $api->goGetPermissions('dashboard,servers', $_SESSION['usergroup']);
	if ($perms->dashboard->dashboard_display === 'N') {
		header("location: crm.php");
	}

	// calculate number of statistics and customers
	$db = new \creamy\DbHandler();
	$statsOk = $db->weHaveSomeValidStatistics();
	$custsOk = $db->weHaveAtLeastOneCustomerOrContact();
	$checkWebRTC = $api->CheckWebrtc($user->getUserId());
	$use_webrtc = $_SESSION['use_webrtc'];
	if (is_numeric($checkWebRTC)) {
			$use_webrtc = $checkWebRTC;
	}
	
	$goAPI = (empty($_SERVER['HTTPS'])) ? str_replace('https:', 'http:', gourl) : str_replace('http:', 'https:', gourl);
	
	// APIs FOR FILTER LIST
		//$campaign = $api->API_getAllCampaigns($_SESSION['usergroup']);
		//$ingroup = $api->API_getAllInGroups($_SESSION['usergroup']);
	/*
	 * API for call statistics - Demian
	*/
	$dropped_calls_today = $ui->API_goGetTotalDroppedCalls($_SESSION['user']);
	$calls_incoming_queue = $ui->API_goGetIncomingQueue($_SESSION['user']);
	$callsperhour = $ui->API_goGetCallsPerHour($_SESSION['user'], 'json');
	$ingroup_list = $api->API_getAllInGroups();
	$max = 0;
	//$callsperhour = explode(";",trim($callsperhour, ';'));
	$callsperhour = json_decode($callsperhour);
	
	foreach ($callsperhour AS $idx => $temp){
		//$temp = explode("=",$temp);
		if ($idx == 'result') {
			$results[$idx] = $temp;
		} else {
			foreach ($temp as $id2 => $item) {
				$results[$id2] = $item;
			}
		}
	}
	
	$outbound_calls = max($results["Hour8o"],$results["Hour9o"], $results["Hour10o"], $results["Hour11o"], $results["Hour12o"], $results["Hour13o"], $results["Hour14o"], $results["Hour15o"], $results["Hour16o"], $results["Hour17o"], $results["Hour18o"], $results["Hour19o"], $results["Hour20o"], $results["Hour21o"]);	
	$inbound_calls = max($results["Hour8"],$results["Hour9"], $results["Hour10"], $results["Hour11"], $results["Hour12"], $results["Hour13"], $results["Hour14"], $results["Hour15"], $results["Hour16"], $results["Hour17"], $results["Hour18"], $results["Hour19"], $results["Hour20"], $results["Hour21"]);	
	$dropped_calls = max($results["Hour8d"],$results["Hour9d"], $results["Hour10d"], $results["Hour11d"], $results["Hour12d"], $results["Hour13d"], $results["Hour14d"], $results["Hour15d"], $results["Hour16d"], $results["Hour17d"], $results["Hour18d"], $results["Hour19d"], $results["Hour20d"], $results["Hour21d"]);
	$max = max($inbound_calls, $outbound_calls, $dropped_calls);
	
	if($max <= 5)
		$max = 5;
	if($outbound_calls == NULL || $outbound_calls == 0)
		$outbound_calls = 0;
    if($outbound_calls_today == NULL || $outbound_calls_today == 0)
		$outbound_calls_today = 0;
	if($inbound_calls == NULL || $inbound_calls == 0)
		$inbound_calls = 0;
	if($calls_incoming_queue == NULL || $calls_incoming_queue == 0)
		$calls_incoming_queue = 0;
	if($dropped_calls == NULL || $dropped_calls == 0)
		$dropped_calls = 0;
	if($dropped_calls_today == NULL || $dropped_calls_today == 0)
		$dropped_calls_today = 0;

	//Whatsapp
	$whatsapp_status = $ui->API_getWhatsappActivation();
	$callWhatsAppWebHookURL = $api->API_WhatsAppWebHookURL();
	
	//echo "\n\n<!--";
	//var_dump($ingroup_list);
	//echo "-->\n";
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?=$lh->translateText("portal_title")?> <?=$lh->translateText("Dashboard")?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
     
		<!--<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />-->
		
		<?php print $ui->standardizedThemeCSS();?>
        <!-- Creamy style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
		
        <!-- dashboard status boxes -->
        <script src="js/bootstrap-editable.js" type="text/javascript"></script> 
        <script src="js/dashboard/moment/min/moment-with-locales.min.js" type="text/javascript"></script>
        <script src="js/modules/now.js" type="text/javascript"></script>         
	    <!-- ChartJS 1.0.1 -->
	    <script src="js/plugins/chartjs/Chart.min.js" type="text/javascript"></script>
		
		<!-- Data Tables -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <script src="js/plugins/datatables/FROMjquery.dataTables.js" type="text/javascript"></script>
        <script src="js/fnProcessingIndicator.js" type="text/javascript"></script>

         <!-- css/dashboard folder -->
					<!-- WHIRL (spinners)-->
			<link rel="stylesheet" href="js/dashboard/whirl/dist/whirl.css">
				<!-- =============== PAGE VENDOR STYLES ===============-->
					<!-- WEATHER ICONS-->
			<link rel="stylesheet" href="js/dashboard/weather-icons/css/weather-icons.min.css">

	<style>
		/*Conversation*/

.conversation {
  padding: 0 !important;
  margin: 0 !important;
  height: 100%;
  /*width: 100%;*/
  border-left: 1px solid rgba(0, 0, 0, .08);
  /*overflow-y: auto;*/
}

.message {
  padding: 0 !important;
  margin: 0 !important;
  background: url("w.jpg") no-repeat fixed center;
  background-size: cover;
  overflow-y: auto;
  border: 1px solid #f7f7f7;
  height: calc(100% - 165px);
}
.message-previous {
  margin : 0 !important;
  padding: 0 !important;
  height: auto;
  width: 100%;
}
.previous {
  font-size: 15px;
  text-align: center;
  padding: 10px !important;
  cursor: pointer;
}

.previous a {
  text-decoration: none;
  font-weight: 700;
}
.message-body {
  margin: 0 !important;
  padding: 0 !important;
  width: auto;
  height: auto;
}

.message-main-receiver {
  /*padding: 10px 20px;*/
  max-width: 60%;
}

.message-main-sender {
  padding: 3px 20px !important;
  margin-left: 40% !important;
  max-width: 60%;
}

.message-text {
  margin: 0 !important;
  padding: 5px !important;
  word-wrap:break-word;
  font-weight: 200;
  font-size: 14px;
  padding-bottom: 0 !important;
}

.message-time {
  margin: 0 !important;
  margin-left: 50px !important;
  font-size: 12px;
  text-align: right;
  color: #9a9a9a;

}

.receiver {
  width: auto !important;
  padding: 4px 10px 7px !important;
  border-radius: 10px 10px 10px 0;
  background: #ffffff;
  font-size: 12px;
  text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
  word-wrap: break-word;
  display: inline-block;
}

.sender {
  float: right;
  width: auto !important;
  background: #dcf8c6;
  border-radius: 10px 10px 0 10px;
  padding: 4px 10px 7px !important;
  font-size: 12px;
  text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
  display: inline-block;
  word-wrap: break-word;
}

	</style>
			
		<link rel="stylesheet" href="css/custom.css">
    </head>
    <?php print $ui->creamyBody(); ?>
        <div data-ui-view="" data-autoscroll="false" class="wrapper ng-scope">
	        <!-- header logo: style can be found in header.less -->
			<?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar(), $_SESSION['usergroup']); ?>
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-heading">
                        <!-- Page title -->
                        <?=$lh->translateText("Dashboard")?>
                        <small class="ng-binding"><?php $lh->translateText("welcome_to"); ?><?php print($ui->creamyHeaderName()); ?>! <?php /*$lh->translateText("dashboard_sub-header") */?></small>
                </section>

				<!-- Main content -->
                <section class="content">
					<?php //var_dump($outSalesHour); ?>
					<!-- STATUS BOXES -->
					<div class="row">
						<!-- <div class="col-lg-3 col-sm-6">
							<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
								<div class="panel widget bg-purple" style="height: 95px;">
									<div class="row status-box">
										<div class="col-xs-4 text-center bg-purple-dark pv-md">
											<em class="icon-earphones fa-3x"></em>
										</div>
										<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
											<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentscall">0</span></div>
											<div class="text-sm"><?=$lh->translateText("agents_on_call")?></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-md-6">
							<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-id="" style="text-decoration : none">
								<div class="panel widget bg-purple" style="height: 95px;">
									<div class="row status-box">
										<div class="col-xs-4 text-center bg-purple-dark pv-md">
											<em class="icon-clock fa-3x"></em>
										</div>
										<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
											<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentswaitcalls"></span></div>
											<div class="text-sm"><?=$lh->translateText("agents_waiting")?></div>
										</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-12">
							<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="PAUSED" data-id="" style="text-decoration : none">
								<div class="panel widget bg-green" style="height: 95px;">
										<div class="row status-box">
										<div class="col-xs-4 text-center bg-gray-dark pv-md">
											<em class="icon-hourglass fa-3x"></em>
										</div>
										<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
											<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentspaused"></span></div>
											<div class="text-sm"><?=$lh->translateText("agents_on_pause")?></div>
										</div>
									</div>
								</div>
							</a>
						</div> -->
						<div class="col-lg-9 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
										<div class="panel widget bg-purple" style="height: 95px;">
											<div class="row status-box">
												<div class="col-xs-4 text-center bg-purple-dark pv-md">
													<em class="icon-earphones fa-3x"></em>
												</div>
												<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
													<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentscall">0</span></div>
													<div class="text-sm"><?=$lh->translateText("agents_on_call")?></div>
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
													<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentswaitcalls">0</span></div>
													<div class="text-sm"><?=$lh->translateText("agents_waiting")?></div>
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
													<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentspaused">0</span></div>
													<div class="text-sm"><?=$lh->translateText("agents_on_pause")?></div>
												</div>
											</div>
										</div>
									</a>
								</div>
							</div>
							<?php /*if($whatsapp_status) { ?>	
							<!-- WHATSAPP AGENT CHAT BOXES -->
							<div class="row">
								<div class="col-lg-4 col-sm-6">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_chat_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
										<div class="panel widget bg-info" style="height: 95px;">
											<div class="row status-box">
												<div class="col-xs-4 text-center bg-info-dark pv-md">
													<em class="fa fa-comments-o fa-3x"></em>
												</div>
												<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
													<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentschat">0</span></div>
													<div class="text-sm"><?=$lh->translateText("agents_on_chat")?></div>
												</div>
											</div>
										</div>
									</a>
								</div>
								<div class="col-lg-4 col-md-6">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_chat_monitoring" data-id="" style="text-decoration : none">
										<div class="panel widget bg-info" style="height: 95px;">
											<div class="row status-box">
												<div class="col-xs-4 text-center bg-info-dark pv-md">
													<em class="icon-clock fa-3x"></em>
												</div>
												<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
													<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentswaitchats">0</span></div>
													<div class="text-sm"><?=$lh->translateText("agents_waiting_chat")?></div>
												</div>
											</div>
										</div>
									</a>
								</div>
								<div class="col-lg-4 col-md-6 col-sm-12">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_chat_monitoring" data-status="PAUSED" data-id="" style="text-decoration : none">
										<div class="panel widget bg-green" style="height: 95px;">
												<div class="row status-box">
												<div class="col-xs-4 text-center bg-gray-dark pv-md">
													<em class="icon-hourglass fa-3x"></em>
												</div>
												<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
													<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentspausedchat">0</span></div>
													<div class="text-sm"><?=$lh->translateText("agents_on_pause_chat")?></div>
												</div>
											</div>
										</div>
									</a>
								</div>
							</div>
							<!-- END OF WHATSAPP AGENT CHAT BOXES -->

							<!-- CHAT STATUS BOXES -->
							<div class="row">
								<div class="col-lg-12">
									<div class="panel panel-default">
										<div class="panel-heading">
											<div class="panel-title"><?=$lh->translateText("chat_status")?></div>
										</div>
										<div class="panel-body">
											<div class="row">
												<div class="col-lg-4 col-sm-6">
													<a href="#" data-toggle="modal" data-target="#realtime_chats_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
														<div class="panel widget bg-danger" style="height: 95px;">
															<div class="row status-box">
																<div class="col-xs-4 text-center bg-red pv-md">
																	<em class="fa fa-eye-slash fa-3x"></em>
																</div>
																<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
																	<div class="h2 mt0"><span class="text-lg" id="refresh_totalunreadchats">0</span></div>
																	<div class="text-sm"><?=$lh->translateText("unread_chats")?></div>
																</div>
															</div>
														</div>
													</a>
												</div>
												<div class="col-lg-4 col-md-6">
													<a href="#" data-toggle="modal" data-target="#realtime_chats_monitoring" data-id="" style="text-decoration : none">
														<div class="panel widget bg-green" style="height: 95px;">
															<div class="row status-box">
																<div class="col-xs-4 text-center bg-gray-dark pv-md">
																	<em class="fa fa-users fa-3x"></em>
																</div>
																<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
																	<div class="h2 mt0"><span class="text-lg" id="refresh_totalqueuechats">0</span></div>
																	<div class="text-sm"><?=$lh->translateText("chats_in_queue")?></div>
																</div>
															</div>
														</div>
													</a>
												</div>
												<div class="col-lg-4 col-md-12 col-sm-12">
													<a href="#" data-toggle="modal" data-target="#realtime_chats_monitoring" data-status="PAUSED" data-id="" style="text-decoration : none">
														<div class="panel widget bg-warning" style="height: 95px;">
															<div class="row status-box">
																<div class="col-xs-4 text-center bg-warning-dark pv-md">
																	<em class="fa fa-comment-o fa-3x"></em>
																</div>
																<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
																	<div class="h2 mt0"><span class="text-lg" id="refresh_totalactivechats">0</span></div>
																	<div class="text-sm"><?=$lh->translateText("active_chats")?></div>
																</div>
															</div>
														</div>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>	
							</div>	
							<!-- END OF CHAT STATUS BOXES -->
							<?php }*/ ?>
						</div>
						<!-- date widget    -->
						<div class="col-lg-3 col-md-6 col-sm-12">
							<div class="panel widget" style="height: 95px;">
								<div class="row status-box">
									<div class="col-xs-4 text-center bg-green pv-lg">
										<div data-now="" data-format="MMMM" class="text-sm"></div><br>
										<div data-now="" data-format="D" class="h2 mt0"></div> 
									</div>
									<div class="col-xs-8 pv-lg">
										<div data-now="" data-format="dddd" class="text-uppercase"></div><br>
										<div data-now="" data-format="h:mm" class="h2 mt0"></div>
										<div data-now="" data-format="a" class="text-muted text-sm"></div>
									</div>
								</div>
							</div>
						</div>
						<!-- END date widget    -->
				
						<?php //if($whatsapp_status) { ?>
						<div class="col-lg-9" id="row_for_rest">
                                                        <!-- CALLS PER HOUR CHART -->
                                                        <div class="row">
                                                          <div id="panelChart9" ng-controller="FlotChartController" class="panel panel-default">
                                                                 <div class="panel-heading">
                                                                        <div class="panel-title"><?=$lh->translateText("calls_per_hour")?></div>
                                                                 </div>
                                                                 <div collapse="panelChart9" class="panel-wrapper">
                                                                        <div class="panel-body">
                                                                           <div class="chart-splinev3 flot-chart"></div> <!-- data is in JS -> demo-flot.js -> search (Overall/Home/Pagkain)-->
                                                                        </div>
                                                                 </div>
                                                          </div>
                                                        </div>
                                                        <!-- END widget-->
						</div>
						<div class="col-lg-3 col-md-6 col-sm-12">	
							<!-- DROPPED PERCENTAGE -->
							<div class="panel panel-default">
								<?php
									// Fix on bug #8671
									$droppedpercentage = $ui->API_goGetDroppedPercentage($_SESSION['user']);
									$dropped_percentage = $droppedpercentage->data->getDroppedPercentage;
									
									if ($dropped_percentage == NULL)
										$dropped_percentage = "0";
									if ($dropped_percentage < "10")
										$color = "#5d9cec";
									if ($dropped_percentage >= "10")
										$color = "#f05050";
									if ($dropped_percentage > "100"){
										$color = "#f05050";
										$dropped_percentage = "100";
									}
								?>
							   <div class="panel-body">
									<div class="panel-title"><?=$lh->translateText("dropped_calls_percentage")?></div>
									<center>
										<div width="200" height="200" style="margin-top: 40px;margin-bottom: 40px;">
											<input type="text"
											class="knob" value="<?php echo $dropped_percentage; ?>" id="refresh_DroppedCallsPercentage" data-width="150" data-height="150" data-padding="21px"
											data-fgcolor="<?php echo $color; ?>" data-readonly="true" readonly="readonly"
											style="	width: 49px;
												height: 100px;
												position: absolute;
												margin-top: 45px;
												margin-left: -98px;
												vertical-align: middle;
												border: 0px;
												font-style: normal;
												font-variant: normal;
												font-weight: bold;
												/* font-stretch: normal; */
												font-size: 30px;
												line-height: normal;
												font-family: Arial;
												text-align: center;
												color: <?php echo $color; ?>;
												padding: 0px;
												-webkit-appearance: none;
												background: none;">
										</div>
									</center>
								   <div class="panel-footer">
										<p class="text-muted">
											<em class="fa fa-upload fa-fw"></em>
											<span><?=$lh->translateText("dropped_calls")?>: </span>
											<span class="text-dark" id="refresh_DroppedCalls"></span>
										</p>
								   </div>
								</div>
							</div>
						</div>
							<!-- END loader widget-->
						<?php //} ?>
					</div>
					<!-- END OF CHAT WIDGETS --> 
	
					<!-- ROW FOR THE REST -->
					<div class="row"> 
						<div class="col-lg-9" id="row_for_rest">
							<!-- Today's Phone Calls --> 
							<div class="row">
								<div class="col-lg-12" style="padding: 0px;">
									<!-- demian -->
									<a href="#" data-toggle="modal" data-target="#<?php if(REALTIME_CALLS_MONITORING === 'y') echo 'realtime_calls_monitoring'?>">
										<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center bg-info info_sun_boxes">
											<em class="fa fa-sun-o fa-3x"></em><div class="h2 m0"><span class="text-lg"></span></div>
											<div class="text-white"><?=$lh->translateText("realtime_calls_monitor")?></div>                                 
										</div>
									</a>
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0"><span class="text-lg text-muted" id="refresh_RingingCalls">0</span></div>
										<div class="text"><?=$lh->translateText("ringing_calls")?></div>
									</div>
									<a href="#" data-toggle="modal" data-target="#<?php if(REALTIME_INBOUND_MONITORING === 'y') echo 'realtime_inbound_monitoring'?>" style="text-decoration : none; color: rgba(0, 0, 0, 0.8);">
										<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
											<div class="h2 m0"><span class="text-lg text-muted" id="refresh_IncomingQueue">0</span></div>
											<div class="text"><?=$lh->translateText("incoming_calls")?></div>
										</div>
									</a>
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0"><span class="text-lg text-muted" id="refresh_AnsweredCalls">0</span></div>
											<div class="text"><?=$lh->translateText("answered_calls")?></div>
									</div>
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0"><span class="text-lg text-muted" id="refresh_TotalInCalls">0</span></div>
										<div class="text"><?=$lh->translateText("inbound_calls_today")?></div>
									</div>	                	
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0"><span class="text-lg text-muted" id="refresh_TotalOutCalls">0</span></div>
											<div class="text" style="font-size: small;"><?=$lh->translateText("outbound_calls_today")?></div>
									</div>
								</div>
							</div>
							
							<!--  SALES -->
							<div class="row">
								<div class="panel panel-default" tabindex="-1">
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center bg-info info_sun_boxes">
										<em class="fa fa-dollar fa-3x"></em>
										<div class="text"><?=$lh->translateText("sales_monitoring")?></div>
									</div>
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0">
											<span class="text-lg text-muted" id="refresh_GetTotalSales">0</span>
										</div>
										<div class="text"><?=$lh->translateText("total_sales")?></div>
									</div>
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0">
											<span class="text-lg text-muted" id="refresh_GetTotalInSales">0</div>
										<div class="text"><?=$lh->translateText("inbound_sales")?></div>
									</div>	                	
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0">
											<span class="text-lg text-muted" id="refresh_GetTotalOutSales">0</span>
										</div>
										<div class="text"><?=$lh->translateText("outbound_sales")?></div>
									</div>
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0">
											<span class="text-lg text-muted" id="refresh_GetInSalesHour">0</span>
										</div>
										<div class="text"><?=$lh->translateText("in_sale")?></div>
									</div>	                	
									<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes" style="padding: 10px;">
										<div class="h2 m0">
											<span class="text-lg text-muted" id="refresh_GetOutSalesHour">0</span>
										</div>
										<div class="text"><?=$lh->translateText("out_sale")?></div>
									</div>
								</div>
							</div>

							<!--  CLUSTER STATUS -->
							<div class="row <?=($perms->servers->servers_read === 'N' ? 'hidden' : '')?>">
								<div class="panel panel-default" tabindex="-1">
									<div class="panel-heading">
										<div class="panel-title"><h4><?=$lh->translateText("cluster_status")?></h4></div>
									</div>
									<div class="responsive">
										<div class="col-sm-12">
											<table id="cluster-status" class="table table-striped table-hover display compact" style="width: 100%">
												<thead>
													<tr>
														<th style="font-size: small;"><?=$lh->translateText("server_id")?></th>
														<th style="font-size: small;"><?=$lh->translateText("server_ip")?></th>
														<th style="font-size: small;"><?=$lh->translateText("active")?></th>
														<th style="font-size: small;"><?=$lh->translateText("load")?></th>
														<th style="font-size: small;"><?=$lh->translateText("channels")?></th>
														<th style="font-size: small;"><?=$lh->translateText("disk")?></th>
														<th style="font-size: small;"><?=$lh->translateText("date_and_time")?></th>
													</tr>
												</thead>
												<tbody>
													<!-- data is in API_GetClusterStatus.php -->
												</tbody>
											</table>
										</div>
										<div class="panel-footer clearfix">
											<a href="#" class="pull-left">
											<small></small>
											</a>
										</div>
									</div>
								</div>
							</div>
							<?php if(STATEWIDE_SALES_REPORT === 'y') { ?>
							<!--  AGENT SALES REPORT -->
							<div class="row">
								<div class="panel panel-default" tabindex="-1">
									<div class="panel-heading">
										<div class="panel-title"><h4><?=$lh->translateText("sales_agent")?></h4></div>
									</div>
									<div class="responsive">
										<div class="col-sm-12">
											<table id="agent-sales" class="table table-striped table-hover display compact" style="width: 100%">
												<thead>
													<tr>
														<th style="font-size: small;">Agent Name</th>
														<th style="font-size: small;">Agent ID</th>
														<th style="font-size: small;">Sales Count</th>
														<th style="font-size: small;">Amount</th>
												</thead>
												<tbody>
													<!-- data is in API_GetAgentSales.php -->
												</tbody>
											</table>
										</div>
										<div class="panel-footer clearfix">
											<a href="#" class="pull-left">
											<small></small>
											</a>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</div><!-- END OF COLUMN 9 -->
	
						<aside class="col-lg-3">
							<!-- TASK ACTIVITIES -->
							<div class="panel panel-default">
								<div class="panel-heading">
									<div class="panel-title"><?=$lh->translateText("campaign_leads_resources")?></div>
								</div>
								<div class="list-group">
									<div class="list-group-item">
										<span id="refresh_campaigns_resources"></span>
									</div>
								 </div>                     
								<div class="panel-footer clearfix">
									<a href="#" data-toggle="modal" data-target="#campaigns_monitoring" class="pull-right">
										<medium><?=$lh->translateText("view_more")?></medium> <em class="fa fa-arrow-right"></em>
									</a>
								</div>
							</div>
							<?php if(STATEWIDE_SALES_REPORT === 'y') { ?>
							<!-- Agent Monitoring Summary -->
							<div class="panel panel-default">
							   <div class="panel-heading">
								  <div class="panel-title"><?=$lh->translateText("agent_monitoring_summary")?></div>
							   </div>
							   <div data-height="230" data-scrollable="yes" class="list-group">
									<span id="refresh_agents_monitoring_summary"></span>
							   </div>
							   <div class="panel-footer clearfix">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" class="pull-right">
										<medium><?=$lh->translateText("view_more")?></medium> <em class="fa fa-arrow-right"></em>
									</a>
							   </div>
							</div>
							<!-- End Agent Monitoring Summary -->
							<?php } ?>
						</aside><!-- END OF COLUMN 3 -->
	
					</div><!-- END OF ROW -->
					
					<div class="row">
						<?php if(STATEWIDE_SALES_REPORT !== 'y') { ?>
						<!-- Agent Monitoring Summary -->
						<div class="col-lg-3">
							<div class="panel panel-default">
							   <div class="panel-heading">
								  <div class="panel-title"><?=$lh->translateText("agent_monitoring_summary")?></div>
							   </div>
							   <div data-height="230" data-scrollable="yes" class="list-group">
									<span id="refresh_agents_monitoring_summary"></span>
							   </div>
							   <div class="panel-footer clearfix">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" class="pull-right">
										<medium><?=$lh->translateText("view_more")?></medium> <em class="fa fa-arrow-right"></em>
									</a>
							   </div>
							</div>
						</div>
						<!-- End Agent Monitoring Summary -->

						<!-- VECTOR MAP LOADER -->
						<div class="col-lg-9">
							<div class="panel panel-transparent">
							   <!--<div data-vector-map="" data-height="450" data-scale='0' data-map-name="world_mill"></div>-->
							   <div id="world-map" style="height: 390px"></div>
							</div>
						</div>
						<?php } ?>
							<br>
					</div>
						
					<?php print $ui->hooksForDashboard(); ?>
				
                </section><!-- /.content -->
				
            </aside><!-- /.right-side -->
			
			<?php
				print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar());
			?>
			
        </div><!-- ./wrapper -->

	<!--================= MODALS =====================-->
	
		<!-- Realtime Agent Monitoring -->
		<div class="modal fade" id="realtime_agents_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("realtime_agents_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="content table-responsive table-full-width">
						<!-- <div class="col-sm-12">-->
							<table class="table table-striped table-hover display compact" id="realtime_agents_monitoring_table" style="width: 100%">
								<thead>                                            
									<th style="color: white;">Pic</th>
									<th style="font-size: small;"><?=$lh->translateText("agent_name")?></th>                                                    
									<th style="font-size: small;"><?=$lh->translateText("user_group")?></th>
									<th style="font-size: small;"><?=$lh->translateText("status")?></th>
									<th style="font-size: small;"><?=$lh->translateText("phone_number")?></th>
									<th style="font-size: small;"><?=$lh->translateText("mm:ss")?></th>
									<th style="font-size: small;"><?=$lh->translateText("campaign")?></th>                                                    
								</thead>
								<tbody>
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
				</div>
			</div>	
		</div>
		<!-- End of Realtime Agent Monitoring -->
			
		<!-- Realtime Calls Monitoring -->
		<div class="modal fade" id="realtime_calls_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("realtime_calls_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="responsive">
						<!-- <div class="col-sm-12">-->
							<table class="table table-striped table-hover display compact" id="realtime_calls_monitoring_table" style="width: 100%">
								<thead>
										<th style="color: white;">Pic</th>
										<th style="font-size: small;"><?=$lh->translateText("status")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("phone_number")?></th>
										<th style="font-size: small;"><?=$lh->translateText("call_type")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("campaign")?></th>
										<th style="font-size: small;"><?=$lh->translateText("mm:ss")?></th>
										<!-- <th>User Group</th> -->
								</thead>
								<tbody>
								
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
				</div>
			</div>
		</div>
        <!-- End of Realtime Calls Monitoring -->
	
		<!-- Realtime Agents Chats Monitoring -->
		<div class="modal fade" id="realtime_agents_chat_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("realtime_agents_chat_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="responsive">
						<!-- <div class="col-sm-12">-->
							<table class="table table-striped table-hover display compact" id="realtime_agents_chat_monitoring_table" style="width: 100%">
								<thead>
										<th style="font-size: small;"><?=$lh->translateText("agent")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("contacts")?></th>
										<th style="font-size: small;"><?=$lh->translateText("unread")?></th>
										<th style="font-size: small;"><?=$lh->translateText("status")?></th>
										<th style="font-size: small;"><?=$lh->translateText("action")?></th>
										<!-- <th>User Group</th> -->
								</thead>
								<tbody>
								
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
				</div>
			</div>
		</div>
        	<!-- End of Realtime Agents chats Monitoring -->

		<!-- Realtime Chats Monitoring -->
		<div class="modal fade" id="realtime_chats_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("realtime_chats_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="responsive">
						<!-- <div class="col-sm-12">-->
							<table class="table table-striped table-hover display compact" id="realtime_chats_monitoring_table" style="width: 100%">
								<thead>
										<th style="font-size: small;"><?=$lh->translateText("client")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("telephone")?></th>
										<th style="font-size: small;"><?=$lh->translateText("assigned_agent")?></th>
										<th style="font-size: small;"><?=$lh->translateText("unseen")?></th>
										<th style="font-size: small;"><?=$lh->translateText("entry")?></th>
										<th style="font-size: small;"><?=$lh->translateText("action")?></th>
								</thead>
								<tbody>
								
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
					<div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-warning" id="btn-assign-chat"><?=$lh->translateText("assign")?></button>
                                        </div>
				</div>
			</div>
		</div>
	        <!-- End of Realtime chats Monitoring -->
	
		<!-- Agents Transaction Modal -->
                <div class="modal fade" id="modal_view_assigned_chats" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                                <div class="modal-content">
                                        <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                <h4><?=$lh->translateText("agents_transactions_monitoring")?></h4>
                                        </div>
                                        <div class="modal-body">
                                                <div class="responsive">
                                                <!-- <div class="col-sm-12">-->
                                                        <table class="table table-striped table-hover display compact" id="assigned_chats_monitoring_table" style="width: 100%">
                                                                <thead>
                                                                                <th style="font-size: small;"><?=$lh->translateText("client")?></th>
                                                                                <th style="font-size: small;"><?=$lh->translateText("social_network")?></th>
                                                                                <th style="font-size: small;"><?=$lh->translateText("telephone")?></th>
                                                                                <th style="font-size: small;"><?=$lh->translateText("action")?></th>
                                                                                <!-- <th>User Group</th> -->
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                        </table>
                                                <!--</div>-->
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
	        <!-- End of Agents Transactions Modal -->

		<!-- Transfer Chat Modal -->
                <div class="modal fade" id="modal_transfer_chat" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-sm modal-dialog">
                                <div class="modal-content">
                                        <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                <h4><?=$lh->translateText("transfer_chat")?><span id="transfer_chatid"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                                <div class="responsive">
							<form id="transfer-form" class="form-horizontal">
								<div class="form-group">
									<label for="transfer_agent_list">Agent:</label>
									<div>
										<select class="form-control" name="transfer_agent_list" id="transfer_agent_list">
											<option value="" selected disabled>Select Agent</option>
										</select>
									</div>
								</div>
							</form>
                                                </div>
                                        </div>
					<div class="modal-footer justify-content-between">
						<input type="hidden" name="transfer_chatId" id="transfer_chatId">
                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$lh->translateText("cancel")?></button>
						<button type="button" class="btn btn-primary" id="btn-transfer-chat" data-dismiss="modal"><?=$lh->translateText("transfer_chat")?></button>
                                        </div>
                                </div>
                        </div>
                </div>
                <!-- End of Transfer Chat Modal -->

		<!-- View Conversation Modal -->
                <div class="modal fade" id="modal_view_conversation" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-sm modal-dialog" style="">
                                <div class="modal-content">
                                        <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                <h4><?=$lh->translateText("view_conversation")?> <span class="sender-name"></h4>
                                        </div>
                                        <div class="modal-body">
                                                <div class="responsive" id="wa-conversation-div" style="height: 450px; overflow-y: auto; overflow-x: hidden; background-color: #eeeee; padding: 10px;">
                                                </div>
                                        </div>
					<div class="modal-footer justify-content-between">
						<input type="hidden" name="conversation_chatId" id="conversation_chatId">
                            			<button type="button" class="btn btn-default" data-dismiss="modal"><?=$lh->translateText("cancel")?></button>
                        		</div>
                                </div>
                        </div>
                </div>
                <!-- End of View Conversation Modal -->
			
		<!-- Campaigns Monitoring -->
		<div class="modal fade" id="campaigns_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("campaigns_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="responsive">
						<!-- <div class="col-sm-12">-->
							<table id="campaigns_monitoring_table" class="table table-striped table-hover display compact" cellspacing="0" style="width: 100%">
								<thead>
										<th style="color: white;">Pic</th>
										<th style="font-size: small;"><?=$lh->translateText("campaign_id")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("campaign_name")?></th>
										<th style="font-size: small;"><?=$lh->translateText("leads_on_hopper")?></th>
										<th style="font-size: small;"><?=$lh->translateText("call_times")?></th>                                                  
										<th style="font-size: small;"><?=$lh->translateText("user_group")?></th>                                                    
								</thead>
								<tbody>
								
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
				</div>
			</div>	
		</div>
		<!-- End of Campaigns Monitoring -->
		
		<!-- Realtime Inbound Monitoring -->
		<div class="modal fade" id="realtime_inbound_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("realtime_inbound_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="content table-responsive table-full-width">
						<!-- <div class="col-sm-12">-->
							<div class="inbound_filter">
								<select id="inbound_filter" class="form-control">
									<option value="">--- SELECT ALL ---</option>
									<?php
									if (!empty($ingroup_list)) {
										$group_id = $ingroup_list->group_id;
										$group_name = $ingroup_list->group_name;
										foreach ($group_id as $idx => $group) {
											if (!preg_match("/^(AGENTDIRECT|AGENTDIRECT_CHAT)$/", $group)) {
												echo "<option value='" . $group . "'>" . $group_name[$idx] . "</option>";
											}
										}
									}
									?>
								</select>
							</div>
							<table class="table table-striped table-hover display compact" id="realtime_inbound_monitoring_table" style="width: 100%">
								<thead>
									<th style="color: white;">Pic</th>
									<th style="font-size: small;"><?=$lh->translateText("agent_name")?></th>
									<th style="font-size: small;"><?=$lh->translateText("inbound_group")?></th>
									<th style="font-size: small;"><?=$lh->translateText("status")?></th>
									<th style="font-size: small;"><?=$lh->translateText("calls_in_queue")?></th>
									<th style="font-size: small;"><?=$lh->translateText("mm:ss")?></th>
									<th style="font-size: small;"><?=$lh->translateText("campaign")?></th>
									<th style="font-size: small;"><?=$lh->translateText("phone_number")?></th>
								</thead>
								<tbody>
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
				</div>
			</div>	
		</div>
		<!-- End of Realtime Inbound Monitoring -->
		
		<!-- Realtime Service Level Monitoring -->
		<div class="modal fade" id="realtime_sla_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-lg modal-dialog" style="min-width: 75%">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4><?=$lh->translateText("service_level_agreement_monitoring")?></h4>
					</div>
					<div class="modal-body">
						<div class="responsive">
						<!-- <div class="col-sm-12">-->
							<table class="table table-striped table-hover display compact" id="realtime_sla_monitoring_table" style="width: 100%">
								<thead>
										<th style="color: white;">Pic</th>
										<th style="font-size: small;"><?=$lh->translateText("user_groups")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("calls_today")?></th>
										<th style="font-size: small;"><?=$lh->translateText("answered_calls")?></th>                                                    
										<th style="font-size: small;"><?=$lh->translateText("ans_calls_less_20s")?></th>
										<th style="font-size: small;"><?=$lh->translateText("abandon")?></th>
										<th style="font-size: small;"><?=$lh->translateText("sla")?>SLA</th>
										<th style="font-size: small;"><?=$lh->translateText("aht")?>AHT</th>                                                    
										<!-- <th>User Group</th> -->
								</thead>
								<tbody>
								
								</tbody>
							</table>
						<!--</div>-->
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End of Realtime Service Level Monitoring -->
		
        <!-- Agent Information -->
		<div class="modal fade" id="modal_view_agent_information" tabindex="-1" role="dialog" aria-hidden="true"> 
			<div class="modal-dialog"> 
				<div class="modal-content"> 
					<div class="modal-header"> 
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
						<h4 class="modal-title"><?=$lh->translateText("more_about")?> <span id="modal-username"></span>:</h4> 
					</div>
					<div class="modal-body" style="min-height: initial;"> 
						<center> 
							<div id="modal-avatar"></div>
							<!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
							<h3 class="media-heading"><span id="modal-fullname"></span> <small></small></h3>
							<span><strong><?=$lh->translateText("logged_into")?>:</strong></span> 
							<span class="label label-warning" id="modal-phonelogin-vu"></span>
							<span class="label label-info" id="modal-campaign"></span> 
							<!-- <span class="label label-info" id="modal-userlevel-vu"></span> -->
							<span class="label label-success" id="modal-usergroup-vu"></span>
							<span class="label label-primary" id="modal-conf-exten"></span>
							<span class="hidden" id="modal-server-ip"></span>
						</center> 
						<div class="responsive hidden">
							<table class="table table-striped table-hover" id="view_agent_information_table" style="width: 100%">
								<thead>
									<th style="font-size: small;"><?=$lh->translateText("agent_id")?></th> 
									<!-- <th style="font-size: small;">Agent Phone</th> -->
									<th style="font-size: small;"><?=$lh->translateText("status")?></th>                                                                
									<th style="font-size: small;"><?=$lh->translateText("cust_phone")?></th>
									<th style="font-size: small;"><?=$lh->translateText("mm:ss")?></th>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<a href="#" class="pull-right" onClick="goGetModalUsernameValue();">
							<button class="btn btn-danger btn-sm"><?=$lh->translateText("emergency_logout")?> &nbsp;<i class="fa fa-arrow-right"></i></button>
						</a>
						<div class="pull-left">
							<a href="#" onClick="goGetInSession('BARGE');">
								<button class="btn btn-success btn-sm"><?=$lh->translateText("barge")?> &nbsp;<i class="fa fa-microphone"></i></button>
							</a>
							<a href="#" onClick="goGetInSession('MONITOR');">
								<button class="btn btn-primary btn-sm"><?=$lh->translateText("listen")?> &nbsp;<i class="fa fa-microphone-slash"></i></button>
							</a>
							<a href="#" onClick="goGetInSession('WHISPER');">
								<button class="btn btn-warning btn-sm"><?=$lh->translateText("whisper")?> &nbsp;<i class="fa fa-user-secret"></i></button>
							</a>
						</div>
					</div> 
				</div>
			</div>
		</div>
		<!-- End of Agent Information -->
                        
		<!-- Campaign Information -->
        <div class="modal fade" id="view_campaign_information" tabindex="-1" role="dialog" aria-hidden="true"> 
			<div class="modal-dialog"> 
				<div class="modal-content"> 
					<div class="modal-header"> 
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
						<h4 class="modal-title"><?=$lh->translateText("more_about_campaign_id")?>: <span id="modal-campaignid-mod"></span></h4> 
					</div>
					<div class="modal-body"> 
						<center> 
							<div id="modal-avatar-campaign"></div>
							<!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
							<h3 class="media-heading"><span id="modal-campaignname-mod"></span></h3> 
							<span><?=$lh->translateText("campaign_details")?>: </span> 
							<span class="label label-info" id="modal-camptype"></span> 
							<span class="label label-info" id="modal-dial_method"></span> 
							<span class="label label-info" id="modal-auto_dial_level-mod"></span>                                    
							<span class="label label-success" id="modal-next_agent_call"></span>
						</center>
						<div class="responsive">
							<table class="table table-striped table-hover" id="view_campaign_information_table" style="width: 100%">
								<thead>
									<th style="font-size: small;"><?=$lh->translateText("campaign_cid")?></th> 
									<th style="font-size: small;"><?=$lh->translateText("call_recordings")?></th>
									<!-- <th style="font-size: small;">Campaign ID</th> -->                                                            
									<!-- <th style="font-size: small;">Phone Number</th> -->
									<th style="font-size: small;"><?=$lh->translateText("calling_hours")?></th>
									<th style="font-size: small;"><?=$lh->translateText("script")?></th>
								</thead>
								<tbody>
									<tr>
									<td><span id="modal-campaigncid-mod"></span></td>
									<td><span id="modal-callrecordings-mod"></span></td>
									<!-- <td><span id="modal-campaign_id"></td> -->
									<!-- <td><span id="modal-phone_number"></td> -->
									<td><span id="modal-localcalltime-mod"></span></td>
									<td><span id="modal-campaignscript"></span></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">                                        
						<!-- <center> 
							<button type="button" class="btn btn-default" data-dismiss="modal">I'm done</button> 
						</center> -->
					</div>
				</div>
			</div>
		</div>
		<!-- End of Campaign Information --> 
                        
<?php
        /*
        * Modal Dialogs
        */
        //include_once ("./php/ModalPasswordDialogs.php");
?>
<script src="js/dashboard/sweetalert/dist/sweetalert.min.js"></script>
<script>
	/*
	 * JQuery Knob = need for dropped calls percentage and other pie loader
	*/
		$(function () {
		  /* jQueryKnob */
	  
		  $(".knob").knob({
			draw: function () {
	  
			  // "tron" case
			  if (this.$.data('skin') == 'tron') {
	  
				var a = this.angle(this.cv)  // Angle
					, sa = this.startAngle          // Previous start angle
					, sat = this.startAngle         // Start angle
					, ea                            // Previous end angle
					, eat = sat + a                 // End angle
					, r = true;
	  
				this.g.lineWidth = this.lineWidth;
	  
				this.o.cursor
				&& (sat = eat - 0.3)
				&& (eat = eat + 0.3);
	  
				if (this.o.displayPrevious) {
				  ea = this.startAngle + this.angle(this.value);
				  this.o.cursor
				  && (sa = ea - 0.3)
				  && (ea = ea + 0.3);
				  this.g.beginPath();
				  this.g.strokeStyle = this.previousColor;
				  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
				  this.g.stroke();
				}
	  
				this.g.beginPath();
				this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
				this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
				this.g.stroke();
	  
				this.g.lineWidth = 2;
				this.g.beginPath();
				this.g.strokeStyle = this.o.fgColor;
				this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
				this.g.stroke();
	  
				return false;
			  }
			}
		  });
		  /* END JQUERY KNOB */
			var series = {
				'PH': 23456,   // Philippines
				'CA': 11100,   // Canada
				'DE': 2510,    // Germany
				'FR': 3710,    // France
				'AU': 5710,    // Australia
				'GB': 8310,    // Great Britain
				'RU': 9310,    // Russia
				'BR': 6610,    // Brazil
				'IN': 7810,    // India
				'CN': 4310,    // China
				'US': 839,     // USA
				'SA': 410      // Saudi Arabia
			};
			var markers = [
				//{ latLng:[14.57, 121.03], name:'Philippines'           },
				//{ latLng:[41.90, 12.45],  name:'Vatican City'          },
				//{ latLng:[43.73, 7.41],   name:'Monaco'                },
				//{ latLng:[-0.52, 166.93], name:'Nauru'                 },
				//{ latLng:[-8.51, 179.21], name:'Tuvalu'                },
				//{ latLng:[7.11,171.06],   name:'Marshall Islands'      },
				//{ latLng:[17.3,-62.73],   name:'Saint Kitts and Nevis' },
				//{ latLng:[3.2,73.22],     name:'Maldives'              },
				//{ latLng:[35.88,14.5],    name:'Malta'                 },
				//{ latLng:[41.0,-71.06],   name:'New England'           },
				//{ latLng:[12.05,-61.75],  name:'Grenada'               },
				//{ latLng:[13.16,-59.55],  name:'Barbados'              },
				//{ latLng:[17.11,-61.85],  name:'Antigua and Barbuda'   },
				//{ latLng:[-4.61,55.45],   name:'Seychelles'            },
				//{ latLng:[7.35,134.46],   name:'Palau'                 },
				//{ latLng:[42.5,1.51],     name:'Andorra'               }
			];
			
			$('#world-map').vectorMap({
				map: 'world_mill_en',
				backgroundColor: 'transparent',
				regionStyle: {
					initial: {
						'fill':           '#bbbec6',
						'fill-opacity':   1,
						'stroke':         'none',
						'stroke-width':   1.5,
						'stroke-opacity': 1
					},
					hover: {
						'fill-opacity': 0.8
					},
					selected: {
						fill: 'blue'
					},
					selectedHover: {
					}
				},
				focusOn:{ x:0.4, y:0.6, scale: 1},
				markerStyle: {
					initial: {
						fill: '#23b7e5',
						stroke: '#23b7e5'
					}
				},
				onRegionLabelShow: function(e, el, code) {
					if ( series && series[code] )
						el.html(el.html() + ': ' + series[code] + ' visitors');
				},
				markers: markers,
				series: {
					regions: [{
						values: series,
						scale: ['#878c9a'],
						normalizeFunction: 'polynomial'
					}]
				}
			});
		});
</script>
	
<!--========== REFRESH DIVS ==============-->
	<!-- <script src="js/load_statusboxes.js"></script> -->
        <script src="js/dashboardv4.js"></script>
	<!-- <script src="js/load_clusterstatus.js"></script> -->

	<script>
	/*
	 * Inbound and Outbound Calls Per Hour Data
	*/
		(function(window, document, $, undefined){
			$(function(){
				var datav3 = [
					{
					"label": "Outbound Calls",
					"color": "#009688",
					"data": [
					<?php
					if($results["result"] == "success" && isset($results["result"]) && isset($results["Hour9o"]) && $outbound_calls != 0){
						echo '["9 AM",'.$results["Hour9o"].'],';
						echo '["10 AM",'.$results["Hour10o"].'],';
						echo '["11 AM",'.$results["Hour11o"].'],';
						echo '["12 NN",'.$results["Hour12o"].'],';
						echo '["1 PM",'.$results["Hour13o"].'],';
						echo '["2 PM",'.$results["Hour14o"].'],';
						echo '["3 PM",'.$results["Hour15o"].'],';
						echo '["4 PM",'.$results["Hour16o"].'],';
						echo '["5 PM",'.$results["Hour17o"].'],';
						echo '["6 PM",'.$results["Hour18o"].'],';
						echo '["7 PM",'.$results["Hour19o"].'],';
						echo '["8 PM",'.$results["Hour20o"].'],';
						echo '["9 PM",'.$results["Hour21o"].']';
					}else{
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
						echo '["9 PM", 0]';
					}
					?>]
					},{
						"label": "Inbound Calls",
						"color": "#23b7e5",
						"data": [
						<?php
						if($results["result"] == "success" && isset($results["result"]) && isset($results["Hour9"]) && $inbound_calls != 0){
							echo '["9 AM",'.$results["Hour9"].'],';
							echo '["10 AM",'.$results["Hour10"].'],';
							echo '["11 AM",'.$results["Hour11"].'],';
							echo '["12 NN",'.$results["Hour12"].'],';
							echo '["1 PM",'.$results["Hour13"].'],';
							echo '["2 PM",'.$results["Hour14"].'],';
							echo '["3 PM",'.$results["Hour15"].'],';
							echo '["4 PM",'.$results["Hour16"].'],';
							echo '["5 PM",'.$results["Hour17"].'],';
							echo '["6 PM",'.$results["Hour18"].'],';
							echo '["7 PM",'.$results["Hour19"].'],';
							echo '["8 PM",'.$results["Hour20"].'],';
							echo '["9 PM",'.$results["Hour21"].']';
						}else{
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
							echo '["9 PM", 0]';
						}
						?>]
					},{
					"label": "Dropped Calls",
					"color": "#512e90",
					"data": [
					<?php
						if($results["result"] == "success" && isset($results["result"]) && isset($results["Hour9d"]) && $dropped_calls != 0){
							echo '["9 AM",'.$results["Hour9d"].'],';
							echo '["10 AM",'.$results["Hour10d"].'],';
							echo '["11 AM",'.$results["Hour11d"].'],';
							echo '["12 NN",'.$results["Hour12d"].'],';
							echo '["1 PM",'.$results["Hour13d"].'],';
							echo '["2 PM",'.$results["Hour14d"].'],';
							echo '["3 PM",'.$results["Hour15d"].'],';
							echo '["4 PM",'.$results["Hour16d"].'],';
							echo '["5 PM",'.$results["Hour17d"].'],';
							echo '["6 PM",'.$results["Hour18d"].'],';
							echo '["7 PM",'.$results["Hour19d"].'],';
							echo '["8 PM",'.$results["Hour20d"].'],';
							echo '["9 PM",'.$results["Hour21d"].']';
						}else{
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
							echo '["9 PM", 0]';
						}
						?>]
					}];
					
				var options = {
					series: {
						lines: {
							show: false
						},
						points: {
							show: true,
							radius: 4
						},
						splines: {
							show: true,
							tension: 0.4,
							lineWidth: 1,
							fill: 0.5
						}
					},
					grid: {
						borderColor: '#eee',
						borderWidth: 1,
						hoverable: true,
						backgroundColor: '#fcfcfc'
					},
					tooltip: true,
					tooltipOpts: {
						content: function (label, x, y) { return y + ' ' + label + ' around ' + x; }
					},
					xaxis: {
						tickColor: '#fcfcfc',
						mode: 'categories'
					},
					yaxis: {
						min: 0,
						max: <?php echo $max;?>, // optional: use it for a clear represetation
						tickColor: '#eee',
						//position: 'right' or 'left',
						tickFormatter: function (v) {
							return v/* + ' visitors'*/;
						}
					},
					shadowSize: 0
				  };
				  var chartv3 = $('.chart-splinev3');
				  if(chartv3.length)
					$.plot(chartv3, datav3, options);
			});
		})(window, document, window.jQuery);

// Clear user information
function clear_agent_form(){

    $('#modal-userid').html("");
    $('#modal-username').html("");
    $('#modal-fullname').html("");
    $('#modal-status-vu').html("");
    $('#modal-campaign').html("");
    $('#modal-usergroup-vu').html("");     
    //$('#modal-userlevel-vu').html("");
    $('#modal-phonelogin-vu').html("");
	$('#modal-conf-exten').html("");
	$('#modal-server-ip').html("");
    //$('#modal-custphone').html("");
    //$('#modal-voicemail').html("");   
}

// Clear campaign information
function clear_campaign_form(){

    $('#modal-campaignid-mod').html("");                                    
    $('#modal-campaigndesc').html("");
    $('#modal-callrecordings-mod').html("");
    $('#modal-camptype').html("");
    $('#modal-campaigncid-mod').html("");                                        
    $('#modal-localcalltime-mod').html("");
    $('#modal-dial_method').html("");
    $('#modal-auto_dial_level-mod').html("");
    $('#modal-avatar-campaign').html("");
    $('#modal-campaignname-mod').html("");
    //$('#modal-hopper_level-mod').html("");
    $('#modal-campaignscript').html("");
                                                               
}

function goGetModalUsernameValue(){
	var goModalUsername = document.getElementById("modal-username").innerText;
	console.log(goModalUsername);
	swal({
		title: "Are you sure?",
		text: "Agent "+goModalUsername+" will be logged out of the dialer.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Yes, I'm sure!",
		closeOnConfirm: false
	}, function(){
		$.ajax({
			type: 'POST',
		//	url: "./php/dashboard/API_EmergencyLogout.php",
			url: "./php/EmergencyLogout.php",
			data: {goUserAgent: goModalUsername},
			cache: false,
			success: function(data){
				clear_agent_form();
				sweetAlert("Emergency Logout",data, "warning");
				$('#modal_view_agent_information').modal('hide');
			}
		});
	});

}

function goGetInSession(type) {
	var phone_login = "<?php echo $_SESSION['phone_login'];?>";
	var phone_pass = "<?php echo $_SESSION['phone_this'];?>";
	var uName = "<?php echo $_SESSION["user"]; ?>";
	var uPass = "<?php echo $_SESSION['phone_this'];?>";
	//console.log(phone_login);
	if (phone_login.length > 0 && phone_pass.length > 0) {
		var use_webrtc = <?=($use_webrtc ? $use_webrtc : 0)?>;
		if (use_webrtc) {
			registerPhone(phone_login, phone_pass);
		}
		
		if ((use_webrtc && typeof phone !== 'undefined') || !use_webrtc) {
			if (use_webrtc) {
				phone.start();
			}
			
			var who = document.getElementById("modal-username").innerText;
			var agent_session_id = document.getElementById("modal-conf-exten").innerText;
			var server_ip = document.getElementById("modal-server-ip").innerText;
			var ip_address = '<?=$_SERVER['REMOTE_ADDR']?>';
			var thisTimer,
				bTitle,
				bText,
				isMonitoring = false,
				checkIfConnected,
				somethingWentWrong = false;
			
			if (type == 'BARGE') {
				bTitle = "Barging...";
				bText = "You're currently barging "+who+"...";
			} else if (type == 'WHISPER') {
				bTitle = "Whispering...";
				bText = "You're currently whispering to "+who+"...";
			} else {
				bTitle = "Listening...";
				bText = "You're currently listening to "+who+"...";
			}
			
			var postData = {
				goAction: 'goMonitorAgent',
				goUser: uName,
				goPass: uPass,
				goAgent: who,
				goPhoneLogin: phone_login,
				goSource: 'realtime',
				goFunction: 'blind_monitor',
				goSessionID: agent_session_id,
				goServerIP: server_ip,
				goStage: type,
				goUserIP: ip_address,
				responsetype: 'json'
			};
			
			checkIfConnected = setInterval(function () {
				if ((use_webrtc && phone.isConnected()) || !use_webrtc) {
					$.ajax({
						type: 'POST',
						url: '<?=$goAPI?>/goBarging/goAPI.php',
						processData: true,
						data: postData,
						dataType: "json",
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded'
						}
					})
					.done(function (result) {
						if (result.result == 'success') {
							isMonitoring = true;
						} else {
							isMonitoring = false;
							somethingWentWrong = true;
						}
						clearInterval(checkIfConnected);
					});
				}
			}, 1000);
			
			if (!somethingWentWrong) {
				swal({
					title: bTitle,
					text: bText + "<br><h1 id='bTimer' class='text-center'>00:00:00</h1>",
					html: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Disconnect",
					closeOnConfirm: false
				}, function() {
					clearInterval(thisTimer);
					isMonitoring = false;
					if (use_webrtc) {
						phone.stop();
					}
					swal.close();
				});
				
				thisTimer = setInterval(function() {
					if (((use_webrtc && phone.isConnected()) || !use_webrtc) && isMonitoring) {
						var bt = $("#bTimer").html().split(':');
						var bHour = parseInt(bt[0]);
						var bMin = parseInt(bt[1]);
						var bSec = parseInt(bt[2]);
						bSec++;
						if (bSec > 59) {
							bSec = 0;
							bMin++;
						}
						if (bMin > 59) {
							bMin = 0;
							bHour++;
						}
						if (bHour < 10) {bHour = "0"+bHour;}
						if (bMin < 10) {bMin = "0"+bMin;}
						if (bSec < 10) {bSec = "0"+bSec;}
						
						$("#bTimer").html(bHour+":"+bMin+":"+bSec);
					}
				}, 1000);
			} else {
				somethingWentWrong = false;
				swal({
					title: "ERROR",
					text: "Can't connect to the asterisk server. Please contact your Administrator.",
					type: "error"
				});
			}
		}
	} else {
		swal({
			title: "ERROR",
			text: "You're account doesn't have a phone login or pass set..."
		});
	}
}

	var inbTable = $('#realtime_inbound_monitoring_table').DataTable({
		destroy:true,
		responsive:true,
		searching: false,
		order: [[ 5, "desc" ]],
		data:{},
		stateSave: true,
		drawCallback: function() {
			var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
			pagination.toggle(this.api().page.info().pages > 1);
		}
	});

		$(document).ready(function(){
				
			// Clear previous agent info
			$('#modal_view_agent_information').on('hidden.bs.modal', function () {
				clear_agent_form();
			});
			
			//global varible
			var global_userid = "";
			
			// Get user information and post results in view_agent_information modal
			$(document).on('click','#onclick-userinfo',function(){							
				var userid = $(this).attr('data-id');
				var user = $(this).attr('data-user');
				var b64image = "./php/ViewImage.php?user_id=" + userid;
				$.ajax({                            
					type: 'POST',
					url: "./php/dashboard/API_getAgentInformation.php",
					data: {
						user: user,
						filter: 'userInfo'
					},
					cache: false,
					dataType: 'json',
					success: function(JSONObject){ 
						console.log(userid);
						if (typeof JSONObject.data[0] !== 'undefined') {
								$('#modal-userid').html(JSONObject.data[0].vu_user_id);
								//global_userid = JSONObject.data[0].vu_user_id;                                        
								$('#modal-username').html(JSONObject.data[0].vla_user);
								$('#modal-fullname').html(JSONObject.data[0].vu_full_name);
								$('#modal-status-vu').html(JSONObject.data[0].vla_status);
								$('#modal-campaign').html(JSONObject.data[0].vla_campaign_id);
								$('#modal-usergroup-vu').html(JSONObject.data[0].vu_user_group);     
								//$('#modal-userlevel-vu').html(JSONObject.data[0].vu_user_level);                                        
								$('#modal-phonelogin-vu').html(JSONObject.data[0].vu_phone_login);
								$('#modal-custphone').html(JSONObject.data[0].vl_phone_number);
								$('#modal-conf-exten').html(JSONObject.data[0].vla_conf_exten);
								$('#modal-server-ip').html(JSONObject.data[0].vla_server_ip);
								//$('#modal-campaign_cid').html(JSONObject.data[0].campaign_cid);
								var avatardata = '<avatar username="'+ JSONObject.data[0].vu_full_name +'" src="'+ b64image +'" :size="160"></avatar>';
								$('#modal-avatar').html(avatardata);
								goAvatar._init(goOptions);
						} else {
								$('#modal-username').html(user);
						}
					}
				});                        
			});

			// Clear previous agent info
			$('#view_campaign_information').on('hidden.bs.modal', function () {
				clear_campaign_form();
			});
			
			// Get campaign information
			$(document).on('click','#onclick-campaigninfo',function(){
				var campid = $(this).attr('data-id');
				$.ajax({
					type: 'POST',
					url: "./php/ViewCampaign.php",
					data: {campaign_id: campid},
					cache: false,
					//dataType: 'json',
					success: function(data){ 
						var JSONString = data;
						var JSONObject = JSON.parse(JSONString);                                    
						//console.log(JSONObject);
						$('#modal-campaignid-mod').html(JSONObject.data.campaign_id);                                    
						$('#modal-campaigndesc').html(JSONObject.data.campaign_description);                                    
						$('#modal-camptype').html(JSONObject.campaign_type);
						$('#modal-campaigncid-mod').html(JSONObject.data.campaign_cid);                                        
						$('#modal-localcalltime-mod').html(JSONObject.data.local_call_time);
						$('#modal-dial_method').html(JSONObject.data.dial_method);
						$('#modal-auto_dial_level-mod').html(JSONObject.data.auto_dial_level);
						//$('#modal-hopper_level-mod').html(JSONObject.data.hopper_level);
						//$('#modal-campaignscript').html(JSONObject.data.campaign_script);
						var campname = JSONObject.data.campaign_name;
						var avatar = '<avatar username="'+campname+'" :size="160"></avatar>';
						$('#modal-avatar-campaign').html(avatar);
						$('#modal-campaignname-mod').html(campname);
						
						var callrecordings = JSONObject.data.campaign_recording;
							if (callrecordings == "ALLFORCE") {
								var callrecordings = "ENABLED";
							}
							if (callrecordings == "ONDEMAND") {
								var callrecordings = "ON-DEMAND";
							}                                        
							if (callrecordings == "NEVER") {
								var callrecordings = "DISABLED";
							}                                        
						$('#modal-callrecordings-mod').html(callrecordings);
						
						var campaignscript = JSONObject.data.campaign_script;
							if (campaignscript == null) {
								var campaignscript = "NONE";
							}
							if (campaignscript == "") {
								var campaignscript = "NONE";
							}                                        
						$('#modal-campaignscript').html(campaignscript);
						
						goAvatar._init(goOptions);                                        
					}
				 });                        
			 });
			
			// ---- loads datatable functions
			
			// ---- Fixed Action Button
					$(".bottom-menu").on('mouseenter mouseleave', function () {
					  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
					});
				
			// ---- status boxes
				// ---- agents
					load_totalagentscall(); 
					load_totalagentspaused();
					load_totalagentswaitingcall();
				// ---- sales
					load_totalSales();
					load_totalOutSales();
					load_totalInSales();
					load_INSalesHour();
					load_OUTSalesPerHour();
				// ---- leads
					//load_TotalActiveLeads();
					//load_LeadsinHopper();
					//load_TotalDialableLeads();
				// ---- calls
					load_RingingCalls();
					load_IncomingQueue();
					load_AnsweredCalls();
					load_DroppedCalls();
					load_DroppedCallsPercentage();
					//load_TotalCalls();
					load_TotalInboundCalls();
					load_TotalOutboundCalls();
					load_LiveOutbound();
				// ---- WhatsApp
					/*load_whatsapp_realtime_monitoring();
					load_whatsapp_agents_chat_monitoring();
					load_whatsapp_chat_monitoring();*/

			// ---- clusterstatus table
					load_cluster_status();
				// ---- agent and campaign resources
					load_campaigns_resources();
					load_campaigns_monitoring();
					load_agents_monitoring_summary();
				// ---- realtime monitoring
					load_realtime_agents_monitoring();
					<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
					load_realtime_calls_monitoring();
					<?php } ?>
					<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
					load_realtime_inbound_monitoring(inbTable);
					<?php } ?>
					//load_realtime_sla_monitoring();
				// ---- view agent information modal
					load_view_agent_information();

					<?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
				// ---- Statewide Customization
					load_agent_sales();
					<?php } ?>
		});
		
		//Refresh functions() after 5000 milliseconds
			// ... status boxes ...
				var int_1 = setInterval(load_totalagentscall,5000);
				var int_2 = setInterval(load_totalagentspaused,5000);
				var int_3 = setInterval(load_totalagentswaitingcall,5000);
				
				//setInterval(load_TotalActiveLeads,5000);
				//setInterval(load_LeadsinHopper,5000);
				//setInterval(load_TotalDialableLeads,5000);
				
				var int_4 = setInterval(load_RingingCalls,15000);
				var int_5 = setInterval(load_IncomingQueue,15000);
				var int_6 = setInterval(load_AnsweredCalls,15000);
				var int_7 = setInterval(load_DroppedCalls,15000);
				var int_24 = setInterval(load_DroppedCallsPercentage,15000);
				//setInterval(load_TotalCalls,5000);
				var int_8 = setInterval(load_TotalInboundCalls,30000);
				var int_9 = setInterval(load_TotalOutboundCalls,30000);
				var int_10 = setInterval(load_LiveOutbound,30000);
				
			// ... cluster status table ...
				var int_11 = setInterval(load_cluster_status,60000);
				
			// ... agent and campaign resources ...
				var int_12 = setInterval(load_campaigns_resources,30000);
				var int_13 = setInterval(load_campaigns_monitoring,20000);
				var int_14 = setInterval(load_agents_monitoring_summary,15000);
			
			// ... realtime monitoring ...
				var int_15 = setInterval(load_realtime_agents_monitoring,3000);
				<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
				var int_16 = setInterval(load_realtime_calls_monitoring,3000);
				<?php } ?>
				<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
				var int_17;
				//var int_17 = setInterval(load_realtime_inbound_monitoring, 3000, inbTable);
				<?php } ?>
				//var int_17 = setInterval(load_realtime_sla_monitoring,10000);
			
			// ... view agent information modal  ...
				var int_18 = setInterval(load_view_agent_information,3000);
				
			// ... sales
				var int_19 = setInterval(load_totalSales,30000);
				var int_20 = setInterval(load_totalOutSales,30000);
				var int_21 = setInterval(load_totalInSales,30000);
				var int_22 = setInterval(load_INSalesHour,60000);
				var int_23 = setInterval(load_OUTSalesPerHour,60000);

			 <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                         // ---- Statewide Customization
                         	var int_25 = setInterval(load_agent_sales,15000);
                         <?php } ?>

			<?php /*if($whatsapp_status) { ?>
			// ----	WhatsApp
				var int_26 = setInterval(load_whatsapp_realtime_monitoring, 5000);
				var int_27 = setInterval(load_whatsapp_agents_chat_monitoring, 3000);
				var int_28 = setInterval(load_whatsapp_chat_monitoring, 3000);
				var int_29 = setInterval(load_whatsapp_queue, 3000);
				//var int_30 = setInterval(load_whatsapp_assigned_chats(), 3000);
			<?php }*/ ?>
		
		$('#modal_view_agent_information').on('show.bs.modal', function () {
			clearInterval(int_1);
			clearInterval(int_2);
			clearInterval(int_3);
			clearInterval(int_4);
			clearInterval(int_5);
			clearInterval(int_6);
			clearInterval(int_7);
			clearInterval(int_8);
			clearInterval(int_9);
			clearInterval(int_10);
			clearInterval(int_11);
			clearInterval(int_12);
			clearInterval(int_13);
			clearInterval(int_14);
			clearInterval(int_15);
			<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
			clearInterval(int_16);
			<?php } ?>
			<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
			if (typeof int_17 !== 'undefined') {
				clearInterval(int_17);
            }
			<?php } ?>
			clearInterval(int_18);
			clearInterval(int_19);
			clearInterval(int_20);
			clearInterval(int_21);
			clearInterval(int_22);
			clearInterval(int_23);
			clearInterval(int_24);
			<?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
			clearInterval(int_25);	
                        <?php } ?>
			<?php if($whatsapp_status) { ?>
			clearInterval(int_26);
			clearInterval(int_27);
			clearInterval(int_28);
			<?php } ?>
		});
		
		$('#modal_view_agent_information').on('hidden.bs.modal', function () {
			int_1 = setInterval(load_totalagentscall,5000);
			int_2 = setInterval(load_totalagentspaused,5000);
			int_3 = setInterval(load_totalagentswaitingcall,5000);
			int_4 = setInterval(load_RingingCalls,15000);
			int_5 = setInterval(load_IncomingQueue,15000);
			int_6 = setInterval(load_AnsweredCalls,15000);
			int_7 = setInterval(load_DroppedCalls,15000);
			int_24 = setInterval(load_DroppedCallsPercentage,15000);
			int_8 = setInterval(load_TotalInboundCalls,30000);
			int_9 = setInterval(load_TotalOutboundCalls,30000);
			int_10 = setInterval(load_LiveOutbound,30000);
			int_11 = setInterval(load_cluster_status,60000);
			int_12 = setInterval(load_campaigns_resources,30000);
			int_13 = setInterval(load_campaigns_monitoring,20000);
			int_14 = setInterval(load_agents_monitoring_summary,15000);
			int_15 = setInterval(load_realtime_agents_monitoring,3000);
			<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
			int_16 = setInterval(load_realtime_calls_monitoring,3000);
			<?php } ?>
			<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
			//int_17 = setInterval(load_realtime_inbound_monitoring,3000,inbTable);
			<?php } ?>
			//int_17 = setInterval(load_realtime_sla_monitoring,10000);
			int_18 = setInterval(load_view_agent_information,3000);
			int_19 = setInterval(load_totalSales,30000);
			int_20 = setInterval(load_totalOutSales,30000);
			int_21 = setInterval(load_totalInSales,30000);
			int_22 = setInterval(load_INSalesHour,60000);
			int_23 = setInterval(load_OUTSalesPerHour,60000);
			int_24 = setInterval(load_DroppedCallsPercentage,15000);
			<?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
                        int_25 = setInterval(load_agent_sales,15000);
                        <?php } ?>
			<?php /*if($whatsapp_status) { ?>
			int_26 = setInterval(load_whatsapp_realtime_monitoring, 5000);
			int_27 = setInterval(load_whatsapp_agents_chat_monitoring, 3000);
			int_28 = setInterval(load_whatsapp_chat_monitoring, 3000);
			<?php }*/ ?>
		});

		<?php /*if($whatsapp_status) { ?>
		// Whatsapp
		var userid = "";
		var int_30 = setInterval(load_whatsapp_assigned_chats(userid),3000);

		$(document).on('click', '#onclick-whatsappinfo', function(){
			userid = $(this).attr('data-id');
			load_whatsapp_assigned_chats(userid);
		});

		$(document).on('click', '#onclick-whatsapplogout', function(){
			clearInterval(int_1);
                        clearInterval(int_2);
                        clearInterval(int_3);
                        clearInterval(int_4);
                        clearInterval(int_5);
                        clearInterval(int_6);
                        clearInterval(int_7);
                        clearInterval(int_8);
                        clearInterval(int_9);
                        clearInterval(int_10);
                        clearInterval(int_11);
                        clearInterval(int_12);
                        clearInterval(int_13);
                        clearInterval(int_14);
                        clearInterval(int_15);
                        <?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
                        clearInterval(int_16);
                        <?php } ?>
                        //clearInterval(int_17);
                        clearInterval(int_18);
                        clearInterval(int_19);
                        clearInterval(int_20);
                        clearInterval(int_21);
                        clearInterval(int_22);
                        clearInterval(int_23);
                        <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
                        clearInterval(int_25);
                        <?php } ?>
                        <?php /*if($whatsapp_status) { ?>
                        clearInterval(int_26);
                        clearInterval(int_27);
                        clearInterval(int_28);
                        <?php } ?>

                        userid = $(this).attr('data-id');
			swal({
                            title: "Are you sure?",
                            text: "Agent "+userid+" will be logged out of the WhatsApp.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, I'm sure!",
                            closeOnConfirm: false
                        }, function(){
                                $.ajax({
                                type: 'POST',
                                url: "./php/EmergencyWhatsAppLogout.php",
                                data: {userid: userid},
                                cache: false,
                                success: function(data){
                                        sweetAlert("Emergency Whatsapp Logout",data, "warning");
                                }
                            });
				int_1 = setInterval(load_totalagentscall,5000);
	                        int_2 = setInterval(load_totalagentspaused,5000);
        	                int_3 = setInterval(load_totalagentswaitingcall,5000);
                	        int_4 = setInterval(load_RingingCalls,15000);
	                       	int_5 = setInterval(load_IncomingQueue,15000);
        	                int_6 = setInterval(load_AnsweredCalls,15000);
                	        int_7 = setInterval(load_DroppedCalls,15000);
                        	int_24 = setInterval(load_DroppedCallsPercentage,15000);
	                        int_8 = setInterval(load_TotalInboundCalls,30000);
        	                int_9 = setInterval(load_TotalOutboundCalls,30000);
                	        int_10 = setInterval(load_LiveOutbound,30000);
                        	int_11 = setInterval(load_cluster_status,60000);
	                        int_12 = setInterval(load_campaigns_resources,30000);
        	                int_13 = setInterval(load_campaigns_monitoring,20000);
                	        int_14 = setInterval(load_agents_monitoring_summary,15000);
                        	int_15 = setInterval(load_realtime_agents_monitoring,3000);
	                        <?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
        	                int_16 = setInterval(load_realtime_calls_monitoring,3000);
                	        <?php } ?>
                        	//int_17 = setInterval(load_realtime_sla_monitoring,10000);
	                        int_18 = setInterval(load_view_agent_information,3000);
        	                int_19 = setInterval(load_totalSales,30000);
                	        int_20 = setInterval(load_totalOutSales,30000);
                        	int_21 = setInterval(load_totalInSales,30000);
	                        int_22 = setInterval(load_INSalesHour,60000);
        	                int_23 = setInterval(load_OUTSalesPerHour,60000);
                	        <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        	// ---- Statewide Customization
	                        int_25 = setInterval(load_agent_sales,15000);
        	                <?php } ?>
                	        <?php if($whatsapp_status) { ?>
                        	int_26 = setInterval(load_whatsapp_realtime_monitoring, 5000);
	                        int_27 = setInterval(load_whatsapp_agents_chat_monitoring, 3000);
        	                int_28 = setInterval(load_whatsapp_chat_monitoring, 3000);
                	        <?php } ?>
                        });
			
                });

		$('#modal_view_assigned_chats').on('show.bs.modal', function () {
			int_30 = setInterval(load_whatsapp_assigned_chats(userid),3000);
		});

		$('#modal_view_assigned_chats').on('hidden.bs.modal', function () {
			$('#assigned_chats_monitoring_table').empty();
		});

		 $(document).on('click', '#onclick-whatsapptransfer', function(){
                        var chatId = $(this).attr('data-id');
                        $("#transfer_chatId").val(chatId);
                });

		$('#modal_transfer_chat').on('show.bs.modal', function () {
			clearInterval(int_1);
                        clearInterval(int_2);
                        clearInterval(int_3);
                        clearInterval(int_4);
                        clearInterval(int_5);
                        clearInterval(int_6);
                        clearInterval(int_7);
                        clearInterval(int_8);
                        clearInterval(int_9);
                        clearInterval(int_10);
                        clearInterval(int_11);
                        clearInterval(int_12);
                        clearInterval(int_13);
                        clearInterval(int_14);
                        clearInterval(int_15);
                        <?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
                        clearInterval(int_16);
                        <?php } ?>
                        //clearInterval(int_17);
                        clearInterval(int_18);
                        clearInterval(int_19);
                        clearInterval(int_20);
                        clearInterval(int_21);
                        clearInterval(int_22);
                        clearInterval(int_23);
                        <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
                        clearInterval(int_25);
                        <?php } ?>
                        <?php if($whatsapp_status) { ?>
                        clearInterval(int_26);
                        clearInterval(int_27);
                        clearInterval(int_28);
                        <?php } ?>

			$.ajax({
                	        type: 'POST',
	                        url: "./php/GetWhatsappUsers.php",
        	                cache: false,
                	        success: function(data){
					var obj = JSON.parse(data);
	                                $('#transfer_agent_list').html(obj);
					console.log(obj);
        	                }
                	});
                });

		$('#modal_transfer_chat').on('hidden.bs.modal', function () {
			int_1 = setInterval(load_totalagentscall,5000);
                        int_2 = setInterval(load_totalagentspaused,5000);
                        int_3 = setInterval(load_totalagentswaitingcall,5000);
                        int_4 = setInterval(load_RingingCalls,15000);
                        int_5 = setInterval(load_IncomingQueue,15000);
                        int_6 = setInterval(load_AnsweredCalls,15000);
                        int_7 = setInterval(load_DroppedCalls,15000);
                        int_24 = setInterval(load_DroppedCallsPercentage,15000);
                        int_8 = setInterval(load_TotalInboundCalls,30000);
                        int_9 = setInterval(load_TotalOutboundCalls,30000);
                        int_10 = setInterval(load_LiveOutbound,30000);
                        int_11 = setInterval(load_cluster_status,60000);
                        int_12 = setInterval(load_campaigns_resources,30000);
                        int_13 = setInterval(load_campaigns_monitoring,20000);
                        int_14 = setInterval(load_agents_monitoring_summary,15000);
                        int_15 = setInterval(load_realtime_agents_monitoring,3000);
                        <?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
                        int_16 = setInterval(load_realtime_calls_monitoring,3000);
                        <?php } ?>
                        //int_17 = setInterval(load_realtime_sla_monitoring,10000);
                        int_18 = setInterval(load_view_agent_information,3000);
                        int_19 = setInterval(load_totalSales,30000);
                        int_20 = setInterval(load_totalOutSales,30000);
                        int_21 = setInterval(load_totalInSales,30000);
                        int_22 = setInterval(load_INSalesHour,60000);
                        int_23 = setInterval(load_OUTSalesPerHour,60000);
                        <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
                        int_25 = setInterval(load_agent_sales,15000);
                        <?php } ?>
                        <?php if($whatsapp_status) { ?>
                        int_26 = setInterval(load_whatsapp_realtime_monitoring, 5000);
                        int_27 = setInterval(load_whatsapp_agents_chat_monitoring, 3000);
                        int_28 = setInterval(load_whatsapp_chat_monitoring, 3000);
                        <?php } ?>
		});

		$(document).on("click", "#btn-transfer-chat", function(){
			var transferId = $("#transfer_agent_list").val();
			var chatId = $("#transfer_chatId").val();
			$.ajax({
                                type: 'POST',
                                url: "./php/WhatsappTransferChat.php",
				data: {id:chatId, userid:transferId},
                                cache: false,
                                success: function(data){
					console.log(data);
                                        console.log(transferId + ' ' + chatId);
                                }
                        });
		});

		$(document).on("click", "#btn-assign-chat", function(){
                        $.ajax({
                                type: 'POST',
                                url: "./php/WhatsappAssignChats.php",
                                cache: false,
                                success: function(data){
                                        console.log(data);
                                }
                        });
                });
		
		$('#modal_view_conversation').on('show.bs.modal', function () {
			clearInterval(int_1);
                        clearInterval(int_2);
                        clearInterval(int_3);
                        clearInterval(int_4);
                        clearInterval(int_5);
                        clearInterval(int_6);
                        clearInterval(int_7);
                        clearInterval(int_8);
                        clearInterval(int_9);
                        clearInterval(int_10);
                        clearInterval(int_11);
                        clearInterval(int_12);
                        clearInterval(int_13);
                        clearInterval(int_14);
                        clearInterval(int_15);
                        <?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
                        clearInterval(int_16);
                        <?php } ?>
                        //clearInterval(int_17);
                        clearInterval(int_18);
                        clearInterval(int_19);
                        clearInterval(int_20);
                        clearInterval(int_21);
                        clearInterval(int_22);
                        clearInterval(int_23);
                        <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
                        clearInterval(int_25);
                        <?php } ?>
                        <?php if($whatsapp_status) { ?>
                        clearInterval(int_26);
                        clearInterval(int_27);
                        clearInterval(int_28);
                        <?php } ?>	
		});

		$('#modal_view_conversation').on('hidden.bs.modal', function () {
			int_1 = setInterval(load_totalagentscall,5000);
                        int_2 = setInterval(load_totalagentspaused,5000);
                        int_3 = setInterval(load_totalagentswaitingcall,5000);
                        int_4 = setInterval(load_RingingCalls,15000);
                        int_5 = setInterval(load_IncomingQueue,15000);
                        int_6 = setInterval(load_AnsweredCalls,15000);
                        int_7 = setInterval(load_DroppedCalls,15000);
                        int_24 = setInterval(load_DroppedCallsPercentage,15000);
                        int_8 = setInterval(load_TotalInboundCalls,30000);
                        int_9 = setInterval(load_TotalOutboundCalls,30000);
                        int_10 = setInterval(load_LiveOutbound,30000);
                        int_11 = setInterval(load_cluster_status,60000);
                        int_12 = setInterval(load_campaigns_resources,30000);
                        int_13 = setInterval(load_campaigns_monitoring,20000);
                        int_14 = setInterval(load_agents_monitoring_summary,15000);
                        int_15 = setInterval(load_realtime_agents_monitoring,3000);
                        <?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
                        int_16 = setInterval(load_realtime_calls_monitoring,3000);
                        <?php } ?>
                        //int_17 = setInterval(load_realtime_sla_monitoring,10000);
                        int_18 = setInterval(load_view_agent_information,3000);
                        int_19 = setInterval(load_totalSales,30000);
                        int_20 = setInterval(load_totalOutSales,30000);
                        int_21 = setInterval(load_totalInSales,30000);
                        int_22 = setInterval(load_INSalesHour,60000);
                        int_23 = setInterval(load_OUTSalesPerHour,60000);
                        <?php if(STATEWIDE_SALES_REPORT === 'y'){ ?>
                        // ---- Statewide Customization
                        int_25 = setInterval(load_agent_sales,15000);
                        <?php } ?>
                        <?php if($whatsapp_status) { ?>
                        int_26 = setInterval(load_whatsapp_realtime_monitoring, 5000);
                        int_27 = setInterval(load_whatsapp_agents_chat_monitoring, 3000);
                        int_28 = setInterval(load_whatsapp_chat_monitoring, 3000);
                        <?php } ?>
		});

		$(document).on("click", "#onclick-whatsappconversation", function(){
                        var chatId = $(this).attr('data-id');
			
			console.log(chatId);
                        $.ajax({
                                type: 'POST',
                                url: "./php/WhatsappGetConversation.php",
				dataType: 'json',
                                data: {chatId:chatId},
                                cache: false,
                                success: function(data){
                                        console.log(data);
					var obj = data.conversation;
					chatListArr = [];
					var conversation = "";
					var username = "";
					obj.message.forEach(function(key){
						chatListArr.push(key.id);
						var author = key.author.split("@");
						phonenumber = author[0];
						time = new Date(key.time);
						time = moment(time).format("MMM D YYYY h:mm:ss a");

						if(phonenumber == chatId){
								type = "receiver";
								username = key.senderName;
						} else {
								type = "sender";
						}

						var body = "";
						if(key.type == "image"){
								body = "<img src='"+key.body+"' style='max-height: 450px; max-width: 100%' class='img-chat'>";
						} else if(key.type == "video"){
								body = "<video style='max-height: 450px; max-width: 100%' class='video-chat' controls><source src='"+key.body+"' type='video/mp4'></video>";
						} else if(key.type == "chat") {
								body = key.body;
						}

						conversation += "<div class='row message-body'>";
						conversation += "<div class='col-sm-12 message-main-" + type + "'>";
						conversation += "<div class='" + type + "'>";
						conversation += "<div class='message-text'>";
						conversation += body;
						conversation += "</div>";
						conversation += "<span class='message-time pull-right'>";
						conversation += time;
						conversation += "</span>";
						conversation += "</div>";
						conversation += "</div>";
						conversation += "</div>";
					});

					$(".sender-name").html(chatId);
					$("#wa-conversation-div").html(conversation);
					
					$('#wa-conversation-div').ready(function(){
						$('#wa-conversation-div').scrollTop($('#wa-conversation-div')[0].scrollHeight);
					});
                                }
                        });
                });
		<?php }*/ ?>
	</script>
	
   <!-- FLOT CHART-->
   <script src="js/dashboard/js/Flot/jquery.flot.js"></script>
   <script src="js/dashboard/js/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
   <script src="js/dashboard/js/Flot/jquery.flot.resize.js"></script>
   <script src="js/dashboard/js/Flot/jquery.flot.time.js"></script>
   <script src="js/dashboard/js/Flot/jquery.flot.categories.js"></script>
   <script src="js/dashboard/js/flot-spline/js/jquery.flot.spline.min.js"></script>
   <!-- VECTOR MAP-->
   <script src="js/dashboard/js/ika.jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
   <script src="js/dashboard/js/ika.jvectormap/jquery-jvectormap-world-mill-en.js"></script>
   <script src="js/dashboard/js/ika.jvectormap/jquery-jvectormap-us-mill-en.js"></script>
   
   <!--<script src="js/dashboard/js/demo/demo-vector-map.js"></script>-->
   
   <!-- CLASSY LOADER-->
   <script src="js/dashboard/js/jquery-classyloader/js/jquery.classyloader.min.js"></script>
   
   <!-- =============== APP SCRIPTS ===============-->
    <!--<script src="js/dashboard/js/app.js"></script>-->
	<script src="adminlte/js/app.min.js" type="text/javascript"></script>
    <script src="js/dashboard/js/jquery-knob/dist/jquery.knob.min.js"></script>

	<!-- Rocket Chat -->
        <!--<script src="js/rocketchat.js" type="text/javascript" ></script>
	
	<!-- Live Helper Chat -->
	<!--<script src="js/livehelperchat.js" type="text/javascript" ></script>-->
        
	<!-- Vue Avatar -->
	<script src="js/vue-avatar/vue.min.js" type="text/javascript"></script>
	<script src="js/vue-avatar/vue-avatar.min.js" type="text/javascript"></script>
	<script type='text/javascript'>
		var goOptions = {
			el: 'body',
			components: {
				'avatar': Avatar.Avatar,
				'rules': {
					props: ['items'],
					template: 'For example:' +
						'<ul id="example-1">' +
						'<li v-for="item in items"><b>{{ item.username }}</b> becomes <b>{{ item.initials }}</b></li>' +
						'</ul>'
				}
			},
	
			data: {
				items: []
			},
	
			methods: {
				initials: function(username, initials) {
					this.items.push({username: username, initials: initials});
				}
			}
		};
		var goAvatar = new Vue(goOptions);
	</script>
		<?php print $ui->creamyFooter(); ?>
	<script>
		$(function(){
			if ($("#realtime_inbound_monitoring").length > 0) {
				var open = false;
				function isOpen(){
					if(open) {
						return "inbound filter is open";
					} else {
						load_realtime_inbound_monitoring(inbTable);
						return "inbound filter is closed";
					}
				}
				
				$("#inbound_filter").on("click", function() {
					open = !open;
					console.log(isOpen());
				});
				
				$("#inbound_filter").on("blur", function() {
					if(open){
						open = !open;
						console.log(isOpen());
					}
				});
				
				$(document).keyup(function(e) {
					if (e.keyCode == 27) { 
						if(open){
							open = !open;
							console.log(isOpen());
						}
					}
				});

				$('#realtime_inbound_monitoring').on('show.bs.modal', function () {
					clearInterval(int_1);
					clearInterval(int_2);
					clearInterval(int_3);
					clearInterval(int_4);
					clearInterval(int_5);
					clearInterval(int_6);
					clearInterval(int_7);
					clearInterval(int_8);
					clearInterval(int_9);
					clearInterval(int_10);
					clearInterval(int_11);
					clearInterval(int_12);
					clearInterval(int_13);
					clearInterval(int_14);
					clearInterval(int_15);
					<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
					clearInterval(int_16);
					<?php } ?>
					<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
					int_17 = setInterval(function() {
						if (!open) {
							load_realtime_inbound_monitoring(inbTable);
                        }
					},3000);
					<?php } ?>
					clearInterval(int_18);
					clearInterval(int_19);
					clearInterval(int_20);
					clearInterval(int_21);
					clearInterval(int_22);
					clearInterval(int_23);
					clearInterval(int_24);
				});
				
				$('#realtime_inbound_monitoring').on('hidden.bs.modal', function () {
					int_1 = setInterval(load_totalagentscall,5000);
					int_2 = setInterval(load_totalagentspaused,5000);
					int_3 = setInterval(load_totalagentswaitingcall,5000);
					int_4 = setInterval(load_RingingCalls,15000);
					int_5 = setInterval(load_IncomingQueue,15000);
					int_6 = setInterval(load_AnsweredCalls,15000);
					int_7 = setInterval(load_DroppedCalls,15000);
					int_8 = setInterval(load_TotalInboundCalls,30000);
					int_9 = setInterval(load_TotalOutboundCalls,30000);
					int_10 = setInterval(load_LiveOutbound,30000);
					int_11 = setInterval(load_cluster_status,60000);
					int_12 = setInterval(load_campaigns_resources,30000);
					int_13 = setInterval(load_campaigns_monitoring,20000);
					int_14 = setInterval(load_agents_monitoring_summary,15000);
					int_15 = setInterval(load_realtime_agents_monitoring,3000);
					<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
					int_16 = setInterval(load_realtime_calls_monitoring,3000);
					<?php } ?>
					<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
					clearInterval(int_17);
					<?php } ?>
					//int_17 = setInterval(load_realtime_sla_monitoring,10000);
					int_18 = setInterval(load_view_agent_information,3000);
					int_19 = setInterval(load_totalSales,30000);
					int_20 = setInterval(load_totalOutSales,30000);
					int_21 = setInterval(load_totalInSales,30000);
					int_22 = setInterval(load_INSalesHour,60000);
					int_23 = setInterval(load_OUTSalesPerHour,60000);
					int_24 = setInterval(load_DroppedCallsPercentage,15000);
				});
            }
				if ($("#change-password-dialog-modal").length > 0) {
					 var old_password_placeholder = '<?=$lh->translationFor("insert_old_password")?>';
					 var new_password_placeholder = '<?=$lh->translationFor("insert_new_password")?>';
					 var new_password_again_placeholder = '<?=$lh->translationFor("insert_new_password_again")?>';
						
						$('#change-password-dialog-modal').on('show.bs.modal', function () {
							clearInterval(int_1);
							clearInterval(int_2);
							clearInterval(int_3);
							clearInterval(int_4);
							clearInterval(int_5);
							clearInterval(int_6);
							clearInterval(int_7);
							clearInterval(int_8);
							clearInterval(int_9);
							clearInterval(int_10);
							clearInterval(int_11);
							clearInterval(int_12);
							clearInterval(int_13);
							clearInterval(int_14);
							clearInterval(int_15);
							clearInterval(int_16);
							<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
							if (typeof int_17 !== 'undefined') {
								clearInterval(int_17);
							}
							<?php } ?>
							clearInterval(int_18);
							clearInterval(int_19);
							clearInterval(int_20);
							clearInterval(int_21);
							clearInterval(int_22);
							clearInterval(int_23);
							clearInterval(int_24);
							
							$(this).find('#old_password').attr('type', 'text').val(old_password_placeholder).css('color', '#ccc');
							$(this).find('#new_password_1').attr('type', 'text').val(new_password_placeholder).css('color', '#ccc');
							$(this).find('#new_password_2').attr('type', 'text').val(new_password_again_placeholder).css('color', '#ccc');
							
							$(this).find('#old_password').focusin(function() {
									if($(this).val() == old_password_placeholder) {
											$(this).attr('type', 'password').val('').css('color', 'inherit');
									}
							});
							
							$(this).find('#old_password').focusout(function() {
									if($(this).val() == '') {
											$(this).attr('type', 'text').val(old_password_placeholder).css('color', '#ccc');
									}
							});
							
							$(this).find('#new_password_1').focusin(function() {
									if($(this).val() == new_password_placeholder) {
											$(this).attr('type', 'password').val('').css('color', 'inherit');
									}
							});
							
							$(this).find('#new_password_1').focusout(function() {
									if($(this).val() == '') {
											$(this).attr('type', 'text').val(new_password_placeholder).css('color', '#ccc');
									}
							});
							
							$(this).find('#new_password_2').focusin(function() {
									if($(this).val() == new_password_again_placeholder) {
											$(this).attr('type', 'password').val('').css('color', 'inherit');
									}
							});
							
							$(this).find('#new_password_2').focusout(function() {
									if($(this).val() == '') {
											$(this).attr('type', 'text').val(new_password_again_placeholder).css('color', '#ccc');
									}
							});
						});
						
						$('#change-password-dialog-modal').on('hidden.bs.modal', function () {
							int_1 = setInterval(load_totalagentscall,5000);
							int_2 = setInterval(load_totalagentspaused,5000);
							int_3 = setInterval(load_totalagentswaitingcall,5000);
							int_4 = setInterval(load_RingingCalls,15000);
							int_5 = setInterval(load_IncomingQueue,15000);
							int_6 = setInterval(load_AnsweredCalls,15000);
							int_7 = setInterval(load_DroppedCalls,15000);
							int_8 = setInterval(load_TotalInboundCalls,30000);
							int_9 = setInterval(load_TotalOutboundCalls,30000);
							int_10 = setInterval(load_LiveOutbound,30000);
							int_11 = setInterval(load_cluster_status,60000);
							int_12 = setInterval(load_campaigns_resources,30000);
							int_13 = setInterval(load_campaigns_monitoring,20000);
							int_14 = setInterval(load_agents_monitoring_summary,15000);
							int_15 = setInterval(load_realtime_agents_monitoring,3000);
							<?php if(REALTIME_CALLS_MONITORING === 'y'){ ?>
							int_16 = setInterval(load_realtime_calls_monitoring,3000);
							<?php } ?>
							<?php if(REALTIME_INBOUND_MONITORING === 'y'){ ?>
							//int_17 = setInterval(load_realtime_inbound_monitoring,3000,inbTable);
							<?php } ?>
							//int_17 = setInterval(load_realtime_sla_monitoring,10000);
							int_18 = setInterval(load_view_agent_information,3000);
							int_19 = setInterval(load_totalSales,30000);
							int_20 = setInterval(load_totalOutSales,30000);
							int_21 = setInterval(load_totalInSales,30000);
							int_22 = setInterval(load_INSalesHour,60000);
							int_23 = setInterval(load_OUTSalesPerHour,60000);
							int_24 = setInterval(load_DroppedCallsPercentage,15000);
						});
				}
		});
	</script>
<script type="text/javascript">
/*
    (function(w, d, s, u) {
        w.RocketChat = function(c) { w.RocketChat._.push(c) }; w.RocketChat._ = []; w.RocketChat.url = u;
        var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
        j.async = true; j.src = 'https://kuts.justgocloud.com/livechat/rocketchat-livechat.min.js?_=201903270000';
        h.parentNode.insertBefore(j, h);
    })(window, document, 'script', 'https://kuts.justgocloud.com/livechat');*/
</script>
    </body>
</html>
