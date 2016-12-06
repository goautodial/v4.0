<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL)
*/
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

// check if Creamy has been installed.
require_once('./php/CRMDefaults.php');
if (!file_exists(CRM_INSTALLED_FILE)) { // check if already installed 
	header("location: ./install.php");
	die();
}

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

// initialize session and DDBB handler
include_once('./php/UIHandler.php');
require_once('./php/LanguageHandler.php');
require_once('./php/DbHandler.php');
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
//$colors = $ui->generateStatisticsColors();

$perms = $ui->goGetPermissions('dashboard', $_SESSION['usergroup']);
if ($perms->dashboard_display === 'N') {
	header("location: crm.php");
}

// calculate number of statistics and customers
$db = new \creamy\DbHandler();
$statsOk = $db->weHaveSomeValidStatistics();
$custsOk = $db->weHaveAtLeastOneCustomerOrContact();

$goAPI = (empty($_SERVER['HTTPS'])) ? str_replace('https:', 'http:', gourl) : str_replace('http:', 'https:', gourl);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
     
		<!--<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />-->
		
        <!-- Creamy style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->

		<!-- javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>    
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
        
            <!-- dashboard status boxes -->
        <script src="js/bootstrap-editable.js" type="text/javascript"></script> 
        <script src="theme_dashboard/moment/min/moment-with-locales.min.js" type="text/javascript"></script>
        <script src="js/modules/now.js" type="text/javascript"></script>         
	    <!-- ChartJS 1.0.1 -->
	    <script src="js/plugins/chartjs/Chart.min.js" type="text/javascript"></script>
		
        <!-- Creamy App -->
        <!--<script src="js/app.min.js" type="text/javascript"></script>-->
		
		<!-- Data Tables -->
        <!-- <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script> -->
        <script src="js/plugins/datatables/FROMjquery.dataTables.js" type="text/javascript"></script>
        <script src="js/fnProcessingIndicator.js" type="text/javascript"></script>

         <!-- theme_dashboard folder -->
					<!-- FONT AWESOME-->
			<link rel="stylesheet" href="theme_dashboard/fontawesome/css/font-awesome.min.css">
					<!-- SIMPLE LINE ICONS-->
			<link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">
					<!-- ANIMATE.CSS-->
			<link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">
					<!-- WHIRL (spinners)-->
			<link rel="stylesheet" href="theme_dashboard/whirl/dist/whirl.css">
				<!-- =============== PAGE VENDOR STYLES ===============-->
					<!-- WEATHER ICONS-->
			<link rel="stylesheet" href="theme_dashboard/weather-icons/css/weather-icons.min.css">
				<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">
		
            <link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">
		<!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
		</script>
	<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
	<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>
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
<?php
/*
 * APIs FOR FILTER LIST
 */

$campaign = $ui->API_getListAllCampaigns();
//var_dump($campaign);
$ingroup = $ui->API_getInGroups();
//$usersinfo = $ui->API_goGetUserInfo($user_id);

/*
 * API for call statistics - Demian
*/
 

$dropped_calls_today = $ui->API_goGetTotalDroppedCalls();
$calls_incoming_queue = $ui->API_goGetIncomingQueue();

$callsperhour = $ui->API_goGetCallsPerHour();

	$max = 0;
	
	$callsperhour = explode(";",trim($callsperhour, ';'));
	 foreach ($callsperhour AS $temp) {
	   $temp = explode("=",$temp);
	   $results[$temp[0]] = $temp[1];  

         }

        $outbound_calls = max($results["Hour8o"],$results["Hour9o"], $results["Hour10o"], $results["Hour11o"], $results["Hour12o"], $results["Hour13o"], $results["Hour14o"], $results["Hour15o"], $results["Hour16o"], $results["Hour17o"], $results["Hour18o"], $results["Hour19o"], $results["Hour20o"], $results["Hour21o"]);		
	$inbound_calls = max($results["Hour8"],$results["Hour9"], $results["Hour10"], $results["Hour11"], $results["Hour12"], $results["Hour13"], $results["Hour14"], $results["Hour15"], $results["Hour16"], $results["Hour17"], $results["Hour18"], $results["Hour19"], $results["Hour20"], $results["Hour21"]);	
	$dropped_calls = max($results["Hour8d"],$results["Hour9d"], $results["Hour10d"], $results["Hour11d"], $results["Hour12d"], $results["Hour13d"], $results["Hour14d"], $results["Hour15d"], $results["Hour16d"], $results["Hour17d"], $results["Hour18d"], $results["Hour19d"], $results["Hour20d"], $results["Hour21d"]);
	
	$max = max($inbound_calls, $outbound_calls, $dropped_calls);
	
	if($max <= 5){
		$max = 5;
	}
	if($outbound_calls == NULL || $outbound_calls == 0){
		$outbound_calls = 0;
	}
    if($outbound_calls_today == NULL || $outbound_calls_today == 0){
		$outbound_calls_today = 0;
	}	
	if($inbound_calls == NULL || $inbound_calls == 0){
		$inbound_calls = 0;
	}
	if($calls_incoming_queue == NULL || $calls_incoming_queue == 0){
		$calls_incoming_queue = 0;
	}	
	if($dropped_calls == NULL || $dropped_calls == 0){
		$dropped_calls = 0;
	}
	if($dropped_calls_today == NULL || $dropped_calls_today == 0){
		$dropped_calls_today = 0;
	}	
//print_r($answered_calls_today);
//die("dd");	
?>		
                        <!-- Page title -->
                        <?=$lh->translateText("Dashboard")?>
                        <small class="ng-binding animated fadeInUpShort">Welcome to GOautodial !</small>
						
					<!--
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-bar-chart-o"></i> <?php $lh->translateText("home"); ?></a></li>
                    </ol>
					--> 	
                </section>

                <!-- Main content -->

                <section class="content">
					
	<!--====== STATUS BOXES =======-->
			<div class="row">
				<div class="col-lg-3 col-sm-6 animated fadeInUpShort">
					<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="ACTIVE" data-id="" style="text-decoration : none">
						<div class="panel widget bg-purple" style="height: 95px;">
							<div class="row status-box">
								<div class="col-xs-4 text-center bg-purple-dark pv-md animated fadeInUpShort">
									<em class="icon-earphones fa-3x"></em>
								</div>
								<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
									<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentscall"></span></div>
									<div class="text-sm">Agent(s) On Call</div>
								</div>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-3 col-md-6 animated fadeInUpShort">
					<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-id="" style="text-decoration : none">
						<div class="panel widget bg-purple" style="height: 95px;">
							<div class="row status-box">
								<div class="col-xs-4 text-center bg-purple-dark pv-md animated fadeInUpShort">
									<em class="icon-clock fa-3x"></em>
								</div>
								<div class="col-xs-8 pv-lg" style="padding-top:10px !important;">
									<div class="h2 mt0"><span class="text-lg" id="refresh_totalagentswaitcalls"></span></div>
									<div class="text-sm">Agent(s) Waiting</div>
								</div>
							</div>
						</div>
					</a>
				</div>
                                <div class="col-lg-3 col-md-6 col-sm-12 animated fadeInUpShort">
                                        <a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" data-status="PAUSED" data-id="" style="text-decoration : none">
                                                <div class="panel widget bg-green" style="height: 95px;">
                                                        <div class="row status-box">
                                                                <div class="col-xs-4 text-center bg-gray-dark pv-md animated fadeInUpShort">
                                                                    <em class="icon-hourglass fa-3x"></em>
                                                                </div>
                                                                    <div class="col-xs-8 pv-lg" style="padding-top:10px !important;">		                        
                                                                        <div class="h2 mt0">
                                                                            <span class="text-lg" id="refresh_totalagentspaused"></span>
                                                                        </div>
                                                                    <div class="text-sm">Agent(s) On Paused</div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </a>
                                </div>
                                                         <!-- date widget    -->
                                <div class="col-lg-3 col-md-6 col-sm-12 animated fadeInUpShort">
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
                                                        <!-- END date widget    -->
                                </div>
            </div>
				
                <!-- ROW FOR THE REST -->
                    <div class="row"> 
                            <div class="col-lg-9" id="row_for_rest">
				
	<!--===== CALLS PER HOUR CHART =======--> 
                    <div class="row">
	              <!-- START widget-->
	              <div id="panelChart9" ng-controller="FlotChartController" class="panel panel-default">
	                 <div class="panel-heading">
	                    <div class="panel-title">Calls Per Hour</div>
	                 </div>
	                 <div collapse="panelChart9" class="panel-wrapper">
	                    <div class="panel-body">
	                       <div class="chart-splinev3 flot-chart"></div> <!-- data is in JS -> demo-flot.js -> search (Overall/Home/Pagkain)--> 
	                    </div>
	                 </div>
	              </div>
	            </div>
	            <!-- END widget-->

	<!--===== Today's Phone Calls =======--> 
	            <div class="row">
	            	<div class="col-lg-12" style="padding: 0px;">
	                	<!-- demian -->
	                	<a href="#" data-toggle="modal" data-target="#realtime_calls_monitoring">
                                    <div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center bg-info pv-xl info_sun_boxes">                                        
					<em class="fa fa-sun-o fa-3x"></em><div class="h2 m0"><span class="text-lg"></span></div>                                            
                                                                <div class="text-white">Realtime Calls Monitoring</div></a>                                        
                                    </div>
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg text-muted" id="refresh_RingingCalls"></span></div>
								<div class="text">Ringing Calls</div>
	                	</div>
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg text-muted" id="refresh_IncomingQueue"></span></div>
								<div class="text">Incoming Calls</div>
	                	</div>	                	
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg text-muted" id="refresh_AnsweredCalls"></span></div>
								<div class="text">Answered Calls</div>
	                	</div>
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg text-muted" id="refresh_TotalInCalls"></span></div>
								<div class="text">Inbound Calls Today</div>
	                	</div>	                	
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg text-muted" id="refresh_TotalOutCalls"></span></div>
								<div class="text" style="font-size: small;">Outbound Calls Today</div>
	                	</div>
	                </div>
                </div>
        <!-- ====== CLUSTER STATUS ======= -->
   
                                                <div class="row">	
                                                    <div class="panel panel-default" tabindex="-1">
                                                        <div class="panel-heading">
                                                            <div class="panel-title"><h4>Cluster Status</h4></div>
                                                        </div>
                                                            <!-- START responsive-->
                                                            <div class="responsive">
                                                                <div class="col-sm-12">
                                                                    <table id="cluster-status" class="table table-striped table-hover display compact" style="width: 100%">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th style="color: white;">Pic</th>
                                                                                        <th style="font-size: small;">Server ID</th>
                                                                                        <th style="font-size: small;">Server IP</th>
                                                                                        <th style="font-size: small;">Active</th>
                                                                                        <th style="font-size: small;">Load</th>
                                                                                        <th style="font-size: small;">Channels</th>
                                                                                        <th style="font-size: small;">Disk</th>
                                                                                        <th style="font-size: small;">Date and Time</th>
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
                                                

            	</div><!-- END OF COLUMN 9 -->


            	<aside class="col-lg-3">

        <!--==== DROPPED PERCENTAGE  ==== -->
                                                <div class="panel panel-default">
                                                    <?php
                                                    $droppedpercentage = $ui->API_goGetDroppedPercentage();
                                                    //echo ("pre");
                                                    //print_r($droppedpercentage);                                                      
                                                    $dropped_percentage = $droppedpercentage->data->getDroppedPercentage; 

                                                    if ($dropped_percentage == NULL){
                                                        $dropped_percentage = "0";
                                                    }                                                   
                                                    
                                                    if ($dropped_percentage < "10"){
                                                        $color = "#5d9cec";
                                                    }
                                                    if ($dropped_percentage >= "10"){
                                                        $color = "#f05050";
                                                    }                                                    
                                                    if ($dropped_percentage > "100"){
                                                        $color = "#f05050";
                                                        $dropped_percentage = "100";
                                                    }                                                    
                                                    ?>
						   <div class="panel-body">
								<div class="panel-title">Dropped Calls Percentage</div>
								<center>
									<div width="200" height="200" style="margin-top: 40px;margin-bottom: 40px;">
										<input type="text"
										class="knob" value="<?php echo $dropped_percentage; ?>" data-width="150" data-height="150" data-padding="21px"
										data-fgcolor="<?php echo $color; ?>" data-readonly="true" readonly="readonly"
										style="
											width: 49px;
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
											background: none;
										">
									</div>
								</center>
							   <div class="panel-footer">
								  <p class="text-muted">
									 <em class="fa fa-upload fa-fw"></em>
									 <span>Dropped Calls: </span>
									 <span class="text-dark" id="refresh_DroppedCalls"></span>
								  </p>
							   </div>
							</div>
							<!-- END loader widget-->
						</div>

        <!--==== SERVICE LEVEL AGREEMENT ==== -->
                                                <!-- <div class="panel panel-default">
                                                    //<?php
                                                    //$slapercentage = $ui->API_goGetSLAPercentage();     
                                                    //echo ("pre");
                                                    //print_r($slapercentage);
                                                    //$sla_percentage = $slapercentage->data[0]->SLA; 
                                                    
                                                    //if ($sla_percentage == NULL){
                                                        //$sla_percentage = "100";
                                                    //}                                                    
                                                    //if ($sla_percentage < "95"){
                                                        //$color = "orange";
                                                    //}                                                   
                                                    //if ($sla_percentage >= "95"){
                                                        //$color = "#5d9cec";
                                                    //}
                                                    
                                                    //?>
						   <div class="panel-body">                                                        
								<div class="panel-title">Service Level Agreement Percentage</div>
								<center>
                                                                    <a data-toggle="modal" data-target="#realtime_sla_monitoring">
									<div width="200" height="200" style="margin-top: 40px;margin-bottom: 40px;">
										<input type="text"
										class="knob" value="<?php //echo $sla_percentage; ?>" data-width="150" data-height="150" data-padding="21px"
										data-fgcolor="<?php //echo $color; ?>" data-readonly="true" readonly="readonly"
										style="
											width: 49px;
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
											color: <?php //echo $color; ?>;
											padding: 0px;
											-webkit-appearance: none;
											background: none;
										">
									</div>
                                                                    </a>
								</center>
							   <div class="panel-footer">
								  <p class="text-muted">
									 <em class="fa fa-upload fa-fw"></em>
									 <span>Service Level Agreement:</span>
									 <span class="text-dark"><?php //echo $sla_percentage; ?></span>
								  </p>
							   </div>                                                        
							</div> -->
							<!-- END loader widget-->
						<!-- </div> -->
						
			<!-- ==== TASK ACTIVITIES ===== -->
			
                                <div class="panel panel-default">
		                     <div class="panel-heading">
		                        <div class="panel-title">Campaign Leads Resources</div>
		                     </div>         
		                     <!-- START list group-->
		                     <!--<span id="refresh_db_time">-->
		                     <div class="list-group">
		                        <!-- START list group item-->
		                        <div class="list-group-item">

		                              <span id="refresh_campaigns_resources">                                            
                                              </span>

		                        </div>
		                     </div>                     
                                    <div class="panel-footer clearfix">
                                        <a href="#" data-toggle="modal" data-target="#campaigns_monitoring" class="pull-right">
                                            <medium>View more</medium> <em class="fa fa-arrow-right"></em>
                                        </a>
                                    </div>		                     
		                </div>
		                
            	</aside><!-- END OF COLUMN 3 -->


            </div><!-- END OF ROW -->
           
	
							<!--
							<div class="panel panel-default">
								<div class="panel-heading text-blue">Service Level Agreement</div>
								<div class="panel-body text-center">
									<div data-label="90%" class="radial-bar radial-bar-100 radial-bar-lg"></div>
								</div>
								<div class="panel-footer">
								  <p class="text-muted">
									 <em class="fa fa-upload fa-fw"></em>
									 <span>Service Level Agreement Percentage</span>
									 <span class="text-dark">90% Gb</span>
								  </p>
							   </div>
							</div>
							-->

                                        <div class="row">
						<!-- Agent Monitoring Summary -->
						<div class="col-lg-3">
							<div class="panel panel-default">
							   <div class="panel-heading">
								  <div class="panel-title">Agent Monitoring Summary</div>
							   </div>
							   <!-- START list group-->
							   <div data-height="230" data-scrollable="yes" class="list-group">
								  <!-- START list group item-->

                                    <span id="refresh_agents_monitoring_summary"></span> 

								  <!-- END list group item-->
							   </div>
							   <!-- END list group-->
							   <!-- START panel footer-->
							   <div class="panel-footer clearfix">
									<a href="#" data-toggle="modal" data-target="#realtime_agents_monitoring" class="pull-right">
										<medium>View more</medium> <em class="fa fa-arrow-right"></em>
									</a>
							   </div>
							   <!-- END panel-footer-->
							</div>
						</div>
						<!-- End Agent Monitoring Summary -->
			<!--==== VECTOR MAP LOADER ======-->
						<div class="col-lg-9">
							<div class="panel panel-transparent">
							   <!--<div data-vector-map="" data-height="450" data-scale='0' data-map-name="world_mill"></div>-->
							   <div id="world-map" style="height: 390px"></div>
							</div>
						</div>

						<br>
					</div>
					
					<?php print $ui->hooksForDashboard(); ?>
					
				
                </section><!-- /.content -->
				
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
			
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->

<!--================= MODALS =====================-->

			<!-- Realtime Agent Monitoring -->
			
                    <div class="modal fade" id="realtime_agents_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4>Realtime Agents Monitoring</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="content table-responsive table-full-width">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover display compact" id="realtime_agents_monitoring_table" style="width: 100%">
                                            <thead>                                            
                                                    <th style="color: white;">Pic</th>
                                                    <th style="font-size: small;">Agent Name</th>                                                    
                                                    <th style="font-size: small;">Group</th>
                                                    <th style="font-size: small;">Status</th>
                                                    <th style="font-size: small;">Phone Number</th>
                                                    <th style="font-size: small;">MM:SS</th>
                                                    <th style="font-size: small;">Campaign</th>                                                    
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
                                    <h4>Realtime Calls Monitoring</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover display compact" id="realtime_calls_monitoring_table" style="width: 100%">
                                            <thead>
                                                    <th style="color: white;">Pic</th>
                                                    <th style="font-size: small;">Status</th>                                                    
                                                    <th style="font-size: small;">Phone Number </th>
                                                    <th style="font-size: small;">Call Type</th>                                                    
                                                    <th style="font-size: small;">Campaign</th>
                                                    <th style="font-size: small;">MM:SS</th>
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
			<!-- Campaigns Monitoring -->

                    <div class="modal fade" id="campaigns_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4>Campaigns Monitoring</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table id="campaigns_monitoring_table" class="table table-striped table-hover display compact" cellspacing="0" style="width: 100%">
                                            <thead>
                                                    <th style="color: white;">Pic</th>
                                                    <th style="font-size: small;">Campaign ID</th>                                                    
                                                    <th style="font-size: small;">Campaign Name</th>
                                                    <th style="font-size: small;">Leads on Hopper</th>
                                                    <th style="font-size: small;">Call Times</th>                                                  
                                                    <th style="font-size: small;">User Group</th>                                                    
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
			<!-- Realtime Service Level Monitoring -->

                    <div class="modal fade" id="realtime_sla_monitoring" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4>Service Level Agreement Monitoring</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover display compact" id="realtime_sla_monitoring_table" style="width: 100%">
                                            <thead>
                                                    <th style="color: white;">Pic</th>
                                                    <th style="font-size: small;">User Groups</th>                                                    
                                                    <th style="font-size: small;">Calls Today</th>
                                                    <th style="font-size: small;">Answered Calls</th>                                                    
                                                    <th style="font-size: small;">Ans Calls Less 20s</th>
                                                    <th style="font-size: small;">Abandon</th>
                                                    <th style="font-size: small;">SLA</th>
                                                    <th style="font-size: small;">AHT</th>                                                    
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
                        
                    <div class="modal fade" id="view_agent_information" tabindex="-1" role="dialog" aria-hidden="true"> 
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                                    <h4 class="modal-title">More about <span id="modal-username"></span>:</h4> 
                                </div> 
                                    <div class="modal-body" style="min-height: initial;"> 
                                        <center> 
                                            <div id="modal-avatar"></div>
                                            <!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
                                            <h3 class="media-heading"><span id="modal-fullname"></span> <small></small></h3>
                                            <span><strong>Logged-in to:</strong></span> 
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
                                                                <th style="font-size: small;">Agent ID</th> 
                                                                <!-- <th style="font-size: small;">Agent Phone</th> -->
                                                                <th style="font-size: small;">Status</th>                                                                
                                                                <th style="font-size: small;">Cust Phone</th>
                                                                <th style="font-size: small;">MM:SS</th>
                                                   
                                                        </thead>
                                                        <tbody>
                                                        
                                                        </tbody>
                                                    </table>
                                            </div>
                                    </div> 
                                        <div class="modal-footer">
                                            <a href="#" class="pull-right" onClick="goGetModalUsernameValue();">
                                                <button class="btn btn-danger btn-sm">Emergency Logout &nbsp;<i class="fa fa-arrow-right"></i></button>
                                            </a>
											
											<div class="pull-left">
												<a href="#" onClick="goGetInSession('BARGE');">
													<button class="btn btn-success btn-sm">Barge &nbsp;<i class="fa fa-microphone"></i></button>
												</a>
												<a href="#" onClick="goGetInSession('MONITOR');">
													<button class="btn btn-primary btn-sm">Listen &nbsp;<i class="fa fa-microphone-slash"></i></button>
												</a>
											</div>
                                            <!-- <center> 
                                                <button type="button" class="btn btn-default" data-dismiss="modal">I'm done</button> 
                                            </center> -->
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
                                            <h4 class="modal-title">More about campaign ID: <span id="modal-campaignid-mod"></span></h4> 
                                    </div> 
                                    <div class="modal-body"> 
                                        <center> 
                                            <div id="modal-avatar-campaign"></div>
                                            <!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
                                                <h3 class="media-heading"><span id="modal-campaignname-mod"></span></h3> 
                                                    <span>Campaign Details: </span> 
                                                        <span class="label label-info" id="modal-camptype"></span> 
                                                        <span class="label label-info" id="modal-dial_method"></span> 
                                                        <span class="label label-info" id="modal-auto_dial_level-mod"></span>                                    
                                                        <span class="label label-success" id="modal-next_agent_call"></span>                                                                           

                                        </center>
                                            <div class="responsive">
                                                <table class="table table-striped table-hover" id="view_campaign_information_table" style="width: 100%">
                                                    <thead>
                                                        <th style="font-size: small;">Campaign CID</th> 
                                                        <th style="font-size: small;">Call Recordings</th>
                                                        <!-- <th style="font-size: small;">Campaign ID</th> -->                                                            
                                                        <!-- <th style="font-size: small;">Phone Number</th> -->
                                                        <th style="font-size: small;">Calling Hours</th>
                                                        <th style="font-size: small;">Script</th>
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
        include_once ("./php/ModalPasswordDialogs.php");
?>
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
			url: "./php/APIs/API_EmergencyLogout.php",
			data: {goUserAgent: goModalUsername},
			cache: false,
			//dataType: 'json',
			success: function(data){
				clear_agent_form();
				sweetAlert("Emergency Logout",data, "warning");
				$('#view_agent_information').modal('hide');
			}
		});
	});

}

function goGetInSession(type) {
	if (phone_login.length > 0 && phone_pass.length > 0) {
		registerPhone(phone_login, phone_pass);
		
		if (typeof phone !== 'undefined') {
			phone.start();
			
			var who = document.getElementById("modal-username").innerText;
			var agent_session_id = document.getElementById("modal-conf-exten").innerText;
			var server_ip = document.getElementById("modal-server-ip").innerText;
			var thisTimer,
				bTitle,
				bText,
				isMonitoring = false,
				checkIfConnected;
			
			if (type == 'barge') {
				bTitle = "Barging...";
				bText = "You're currently barging "+who+"...";
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
				responsetype: 'json'
			};
			
			checkIfConnected = setInterval(function () {
				if (phone.isConnected()) {
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
							clearInterval(checkIfConnected);
						}
					});
				}
			}, 1000);
			
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
				phone.stop();
				swal.close();
			});
			
			thisTimer = setInterval(function() {
				if (phone.isConnected() && isMonitoring) {
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
		}
	} else {
		swal({
			title: "ERROR",
			text: "You're account doesn't have a phone login or pass set..."
		});
	}
}

                //demian
                $(document).ready(function(){
                    
                    // Clear previous agent info
                    $('#view_agent_information').on('hidden.bs.modal', function () {
                        clear_agent_form();
                    });
                    
                    //global varible
                    var global_userid = "";
                    
                    // Get user information and post results in view_agent_information modal
                    $(document).on('click','#onclick-userinfo',function(){
                        var userid = $(this).attr('data-id');
                        $.ajax({                            
                            type: 'POST',
                            url: "./php/ViewUserInfo.php",
                            data: {user: userid},
                            cache: false,
                            //dataType: 'json',
                                success: function(data){ 
                                    //console.log(data);
                                    var JSONString = data;
                                    var JSONObject = JSON.parse(JSONString);
                                    //console.log(JSONObject);
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
                                        
                                        var avatar = '<avatar username="'+ JSONObject.data[0].vu_full_name +'" :size="160"></avatar>';
                                        $('#modal-avatar').html(avatar);
                                        goAvatar._init(goOptions);
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
                                success: function(campaigndata){ 
                                    //console.log(campaigndata);
                                    var JSONStringcampaign = campaigndata;
                                    var JSONObjectcampaign = JSON.parse(JSONStringcampaign);                                    
                                    //console.log(JSONObjectcampaign);
                                    $('#modal-campaignid-mod').html(JSONObjectcampaign.data.campaign_id);                                    
                                    $('#modal-campaigndesc').html(JSONObjectcampaign.data.campaign_description);                                    
                                    $('#modal-camptype').html(JSONObjectcampaign.campaign_type);
                                    $('#modal-campaigncid-mod').html(JSONObjectcampaign.data.campaign_cid);                                        
                                    $('#modal-localcalltime-mod').html(JSONObjectcampaign.data.local_call_time);
                                    $('#modal-dial_method').html(JSONObjectcampaign.data.dial_method);
                                    $('#modal-auto_dial_level-mod').html(JSONObjectcampaign.data.auto_dial_level);
                                    //$('#modal-hopper_level-mod').html(JSONObjectcampaign.data.hopper_level);
                                    //$('#modal-campaignscript').html(JSONObjectcampaign.data.campaign_script);
                                    var campname = JSONObjectcampaign.data.campaign_name;
                                    var avatar = '<avatar username="'+campname+'" :size="160"></avatar>';
                                    $('#modal-avatar-campaign').html(avatar);
                                    $('#modal-campaignname-mod').html(campname);
                                    
                                    var callrecordings = JSONObjectcampaign.data.campaign_recording;
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
                                    
                                    var campaignscript = JSONObjectcampaign.data.campaign_script;
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
			load_INSalesHour();
			load_OUTSalesPerHour();
		// ---- leads
			load_TotalActiveLeads();
			load_LeadsinHopper();
			load_TotalDialableLeads();
		// ---- calls
			load_RingingCalls();
			load_IncomingQueue();
			load_AnsweredCalls();
			load_DroppedCalls();
			load_TotalCalls();
			load_TotalInboundCalls();
			load_TotalOutboundCalls();
			load_LiveOutbound();
                            
	// ---- clusterstatus table
                        load_cluster_status();
			
        // ---- agent and campaign resources
                        load_campaigns_resources();
                        load_campaigns_monitoring();
                        load_agents_monitoring_summary();
                        
        // ---- realtime monitoring
                        load_realtime_agents_monitoring();
                        load_realtime_calls_monitoring();
                        load_realtime_sla_monitoring();
                        
        // ---- view agent information modal
                        load_view_agent_information();
                
		});

	//Refresh functions() after 5000 millisecond
		// ... status boxes ...
		setInterval(load_totalagentscall,5000);
		setInterval(load_totalagentspaused,5000);
		setInterval(load_totalagentswaitingcall,5000);
		
		//setInterval(load_totalSales,5000);
		//setInterval(load_INSalesHour,5000);
		//setInterval(load_OUTSalesPerHour,5000);
		
		//setInterval(load_TotalActiveLeads,5000);
		//setInterval(load_LeadsinHopper,5000);
		//setInterval(load_TotalDialableLeads,5000);
		
		setInterval(load_RingingCalls,5000);
		setInterval(load_IncomingQueue,5000);
		setInterval(load_AnsweredCalls,5000);
		setInterval(load_DroppedCalls,5000);
		setInterval(load_TotalCalls,5000);
		setInterval(load_TotalInboundCalls,5000);
		setInterval(load_TotalOutboundCalls,5000);
		setInterval(load_LiveOutbound,5000);
		
		// ... cluster status table ...
		setInterval(load_cluster_status,10000);
		
		// ... agent and campaign resources ...
		setInterval(load_campaigns_resources,30000);
		setInterval(load_campaigns_monitoring,20000);
		setInterval(load_agents_monitoring_summary,5000);
		
		// ... realtime monitoring ...
                setInterval(load_realtime_agents_monitoring,3000);
                setInterval(load_realtime_calls_monitoring,3000);
                setInterval(load_realtime_sla_monitoring,10000);
		
		// ... view agent information modal  ...
		setInterval(load_view_agent_information,3000);
		
	</script>
	
    <!-- =============== VENDOR SCRIPTS ===============-->
   <!-- JQUERY EASING-->
   <script src="theme_dashboard/js/jquery.easing/js/jquery.easing.js"></script>
   <!-- ANIMO-->
   <script src="theme_dashboard/js/animo.js/animo.js"></script>
   <!-- SLIMSCROLL-->
   <script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
   <!-- SCREENFULL-->
   <script src="theme_dashboard/js/screenfull/dist/screenfull.js"></script>
   <!-- LOCALIZE-->
   <script src="theme_dashboard/js/jquery-localize-i18n/dist/jquery.localize.js"></script>
   <!-- RTL demo-->
   <script src="theme_dashboard/js/demo/demo-rtl.js"></script>
   <!-- =============== PAGE VENDOR SCRIPTS ===============-->
   <!-- SPARKLINE-->
   <script src="theme_dashboard/js/sparkline/index.js"></script>
   <!-- FLOT CHART-->
   <script src="theme_dashboard/js/Flot/jquery.flot.js"></script>
   <script src="theme_dashboard/js/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
   <script src="theme_dashboard/js/Flot/jquery.flot.resize.js"></script>
   <script src="theme_dashboard/js/Flot/jquery.flot.time.js"></script>
   <script src="theme_dashboard/js/Flot/jquery.flot.categories.js"></script>
   <script src="theme_dashboard/js/flot-spline/js/jquery.flot.spline.min.js"></script>
   <!-- VECTOR MAP-->
   <script src="theme_dashboard/js/ika.jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
   <script src="theme_dashboard/js/ika.jvectormap/jquery-jvectormap-world-mill-en.js"></script>
   <script src="theme_dashboard/js/ika.jvectormap/jquery-jvectormap-us-mill-en.js"></script>
   
   <!--<script src="theme_dashboard/js/demo/demo-vector-map.js"></script>-->
   
   <!-- CLASSY LOADER-->
   <script src="theme_dashboard/js/jquery-classyloader/js/jquery.classyloader.min.js"></script>
   
   <!-- =============== APP SCRIPTS ===============-->
    <!--<script src="theme_dashboard/js/app.js"></script>-->
	<script src="adminlte/js/app.min.js" type="text/javascript"></script>
    <script src="theme_dashboard/js/jquery-knob/dist/jquery.knob.min.js"></script>
			
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

    </body>
</html>
