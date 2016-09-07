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

// calculate number of statistics and customers
$db = new \creamy\DbHandler();
$statsOk = $db->weHaveSomeValidStatistics();
$custsOk = $db->weHaveAtLeastOneCustomerOrContact();

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
        <script src="js/app.min.js" type="text/javascript"></script>
		
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

		<!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
	<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">'; // sweetalert
	<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>'; // sweetalert js
    </head>
    <?php print $ui->creamyBody(); ?>
        <div data-ui-view="" data-autoscroll="false" class="wrapper ng-scope">
	        <!-- header logo: style can be found in header.less -->
			<?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

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
                        <?php
                                $lh->translateText("Dashboard");
                        ?>
                        <small class="ng-binding animated fadeInUpShort">Welcome to Goautodial  !</small>
						
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
					
					<!-- Update (if needed) -->
                                        <?php
						require_once('./php/Updater.php');
						$upd = \creamy\Updater::getInstance();
						$currentVersion = $upd->getCurrentVersion();
						if (!$upd->CRMIsUpToDate()) {
					?>

                    <div class="row">
                        <section class="col-lg-12">
                            <!-- version -->
                            <div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-refresh"></i>
                                    <h3 class="box-title"><?php print $lh->translationFor("version")." ".number_format($currentVersion, 1); ?></h3>
                                </div>
                                <div class="box-body">
									<?php
									if ($upd->canUpdateFromVersion($currentVersion)) { // update needed
										$contentText = $lh->translationFor("you_need_to_update");
										print $ui->formWithContent(
											"update_form", 						// form id
											$contentText, 						// form content
											$lh->translationFor("update"), 		// submit text
											CRM_UI_STYLE_DEFAULT,				// submit style
											CRM_UI_DEFAULT_RESULT_MESSAGE_TAG,	// resulting message tag
											"update.php");						// form PHP action URL.
									} else { // we cannot update?
										$lh->translateText("crm_update_impossible");
									}
									?>
                                </div>
                            </div>
                        </section>
                    </div>  
					<?php } ?>   


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
	                		<div class="h2 m0"><span class="text-lg" id="refresh_RingingCalls"></span></div>
								<div class="text-muted">Outgoing Calls</div>
	                	</div>
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg" id="refresh_IncomingQueue"></span></div>
								<div class="text-muted">Incoming Calls</div>
	                	</div>	                	
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg" id="refresh_AnsweredCalls"></span></div>
								<div class="text-muted">Answered Calls</div>
	                	</div>
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg" id="refresh_DroppedCalls"></span></div>
								<div class="text-muted">Dropped Calls</div>
	                	</div>	                	
	                	<div class="panel widget col-md-2 col-sm-3 col-xs-6 br text-center info_sun_boxes">
	                		<div class="h2 m0"><span class="text-lg" id="refresh_TotalCalls"></span></div>
								<div class="text-muted" style="font-size: small;">Total Calls</div>
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
                                                                    <table id="cluster-status" class="table table-striped table-hover" style="width: 100%">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th style="color: white;">Pic</th>
                                                                                        <th>Server ID</th>
                                                                                        <th>Server IP</th>
                                                                                        <th>Active</th>
                                                                                        <th>Load</th>
                                                                                        <th>Channels</th>
                                                                                        <th>Disk</th>
                                                                                        <th>Date and Time</th>
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
                                                    
                                                    if ($dropped_percentage < "30"){
                                                        $color = "#5d9cec";
                                                    }
                                                    if ($dropped_percentage >= "30"){
                                                        $color = "orange";
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
									 <span>Total Dropped Percentage</span>
									 <span class="text-dark"><?php echo $dropped_percentage; ?></span>
								  </p>
							   </div>
							</div>
							<!-- END loader widget-->
						</div>

        <!--==== SERVICE LEVEL AGREEMENT ==== -->
                                                <div class="panel panel-default">
                                                    <?php
                                                    $slapercentage = $ui->API_goGetSLAPercentage();     
                                                    //echo ("pre");
                                                    //print_r($slapercentage);
                                                    $sla_percentage = $slapercentage->data[0]->SLA; 
                                                    //echo ("pre");
                                                    //print_r($sla_percentage);                                                    
                                                    if ($sla_percentage < "95"){
                                                        $color = "orange";
                                                    }                                                   
                                                    if ($sla_percentage >= "95"){
                                                        $color = "#5d9cec";
                                                    }
                                                    
                                                    ?>
						   <div class="panel-body">                                                        
								<div class="panel-title">Service Level Agreement Percentage</div>
								<center>
                                                                    <a data-toggle="modal" data-target="#realtime_sla_monitoring">
									<div width="200" height="200" style="margin-top: 40px;margin-bottom: 40px;">
										<input type="text"
										class="knob" value="<?php echo $sla_percentage; ?>" data-width="150" data-height="150" data-padding="21px"
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
                                                                    </a>
								</center>
							   <div class="panel-footer">
								  <p class="text-muted">
									 <em class="fa fa-upload fa-fw"></em>
									 <span>Service Level Agreement:</span>
									 <span class="text-dark"><?php echo $sla_percentage; ?></span>
								  </p>
							   </div>                                                        
							</div>
							<!-- END loader widget-->
						</div>
						
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
						</div><!-- end team messages -->
			<!--==== VECTOR MAP LOADER ======-->
						<div ng-controller="VectorMapController" class="col-lg-9">
							<div class="panel panel-transparent">
							   <div data-vector-map="" data-height="450" data-scale='0' data-map-name="world_mill_en"></div>
							</div>
						 </div>

					</div>
					
					<?php print $ui->hooksForDashboard(); ?>
					
				
                </section><!-- /.content -->
				
            </aside><!-- /.right-side -->
			
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
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover" id="realtime_agents_monitoring_table" style="width: 100%">
                                            <thead>                                            
                                                    <th style="color: white;">Pic</th>
                                                    <th>Agent Name</th>                                                    
                                                    <th>Group</th>
                                                    <th>Status</th>
                                                    <th>Phone Number</th>
                                                    <th>MM:SS</th>
                                                    <th>Campaign</th>                                                    
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
                                        <table class="table table-striped table-hover" id="realtime_calls_monitoring_table" style="width: 100%">
                                            <thead>
                                                    <th style="color: white;">Pic</th>
                                                    <th>Status</th>                                                    
                                                    <th>Phone Number </th>
                                                    <th>Call Type</th>                                                    
                                                    <th>Campaign</th>
                                                    <th>MM:SS</th>
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
                                        <table class="table table-striped table-hover" id="campaigns_monitoring_table" style="width: 100%">
                                            <thead>
                                                    <th style="color: white;">Pic</th>
                                                    <th>Campaign ID</th>                                                    
                                                    <th>Campaign Name</th>
                                                    <th>Leads on Hopper</th>
                                                    <th>Call Times</th>                                                  
                                                    <th>User Group</th>                                                    
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
                                        <table class="table table-striped table-hover" id="realtime_sla_monitoring_table" style="width: 100%">
                                            <thead>
                                                    <th style="color: white;">Pic</th>
                                                    <th>User Groups</th>                                                    
                                                    <th>Calls Today</th>
                                                    <th>Answered Calls</th>                                                    
                                                    <th>Ans Calls Less 20s</th>
                                                    <th>Abandon</th>
                                                    <th>SLA</th>
                                                    <th>AHT</th>                                                    
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
                                    <div class="modal-body"> 
                                        <center> 
                                            <div id="modal-avatar"></div>
                                            <!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
                                            <h3 class="media-heading"><span id="modal-fullname"></span> <small></small></h3> 
                                            <span><strong>Logged-in to:</strong></span> 
                                            <span class="label label-warning" id="modal-campaign"></span> 
                                            <span class="label label-info" id="modal-status"></span> 
                                            <span class="label label-info" id="modal-userlevel"></span> 
                                            <span class="label label-success" id="modal-usergroup"></span>
                                        </center> <hr> 
                                            <div class="responsive">
                                                    <table class="table table-striped table-hover" id="view_agent_information_table" style="width: 100%">
                                                        <thead>
                                                                <th>Agent ID</th> 
                                                                <th>Agent Phone</th>
                                                                <th>Status</th>                                                                
                                                                <th>Cust Phone</th>
                                                                <th>MM:SS</th>
                                                   
                                                        </thead>
                                                        <tbody>
                                                        
                                                        </tbody>
                                                    </table>
                                            </div>
                                    </div> 
                                        <div class="modal-footer"> 
                                            <a href="#" data-toggle="modal" data-target="#" class="pull-right text-danger" onClick="goGetModalUsernameValue();">
                                                <small>Emergency Logout</small> <em class="fa fa-arrow-right"></em>
                                            </a>                                        
                                            <!-- <center> 
                                                <button type="button" class="btn btn-default" data-dismiss="modal">I'm done</button> 
                                            </center> -->
                                        </div> 
                                </div> 
                            </div> 
                        </div> 
                        
                        <!-- End of Agent Information -->
			<!-- View Campaign -->
			
			<div id="view_campaign_information" class="modal fade" role="dialog">
			  <div class="modal-dialog">

			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title"><b>Campaign Information</b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
			      </div>
			      <div class="modal-body">
			      	<div class="output-message-no-result hide">
				      	<div class="alert alert-warning alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Notice!</strong> There was an error retrieving details. Either error or no result.
						</div>
					</div>
                                        <div id="content-campaign" class="view-form ">
					    <div class="form-horizontal">
                                                <div class="form-group">
					    		<label class="control-label col-lg-5">Campaign ID:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaignid"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Campaign Name:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaignname"></span></b>
					    	</div>
					    	<div class="output-message-no-result hide form-group">
					    		<label class="control-label col-lg-5">Campaign Description:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaigndesc"></span></b>
                                                </div>					    	
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Call Recordings:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-callrecordings"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Campaign Type:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-camptype"></span></b>
					    	</div>					    	
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Campaign Caller ID:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaigncid"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Local Call Time:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-localcalltime"></span></b>
                                                </div>                                             
                                            </div>
                                </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			      </div>
			    </div>
			    <!-- End of modal content -->
                           </div>
                         </div>
                        </div>
                        
			<!-- End of View Campaign -->
                        
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
		});
</script>
	
<!--========== REFRESH DIVS ==============-->
	<script src="theme_dashboard/js/demo/demo-vector-map.js"></script>
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
    $('#modal-status').html("");
    $('#modal-campaign').html("");
    $('#modal-usergroup').html("");     
    $('#modal-userlevel').html("");
    $('#modal-phonelogin').html("");
    $('#modal-cust_phone').html("");
    //$('#modal-voicemail').html("");   
}

// Clear campaign information
 function clear_campaign_form(){

    $('#modal-campaignid').html("");
    $('#modal-campaignname').html("");
    $('#modal-campaigndesc').html("");
    $('#modal-callrecordings').html("");
    //$('#modal-amd').html("");
    $('#modal-campaigncid').html("");
    $('#modal-localcalltime').html("");
}

function goGetModalUsernameValue(){
   
   var goModalUsername = document.getElementById("modal-username").innerText;

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
                                    //console.log(JSONString);
                                    console.log(JSONObject);      // Dump all data of the Object in the console
                                    //console.log(JSONObject.data[0].vu_user_id);
                                        $('#modal-userid').html(JSONObject.data[0].vu_user_id);
                                        //global_userid = JSONObject.data[0].vu_user_id;
                                        //console.log(global_userid);
                                        $('#modal-username').html(JSONObject.data[0].vla_user);
                                        $('#modal-fullname').html(JSONObject.data[0].vu_full_name);
                                        $('#modal-status').html(JSONObject.data[0].vla_status);
                                        $('#modal-campaign').html(JSONObject.data[0].vla_campaign_id);
                                        $('#modal-usergroup').html(JSONObject.data[0].vu_user_group);     
                                        $('#modal-userlevel').html(JSONObject.data[0].vu_user_level);                                        
                                        $('#modal-phonelogin').html(JSONObject.data[0].vu_phone_login);
                                        $('#modal-custphone').html(JSONObject.data[0].vl_phone_number);
                                        
                                        var avatar = '<avatar username="'+ JSONObject.data[0].vu_full_name +'" :size="160"></avatar>';
                                        $('#modal-avatar').html(avatar);
                                        goAvatar._init(goOptions);
                                }
                        });                        
                       // load_view_agent_information(userid);
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
                                    //console.log(JSONObjectcampaign.data.campaign_id);
                                    console.log(JSONObjectcampaign);
                                        $('#modal-campaignid').html(JSONObjectcampaign.data.campaign_id);
                                        $('#modal-campaignname').html(JSONObjectcampaign.data.campaign_name);
                                        $('#modal-campaigndesc').html(JSONObjectcampaign.data.campaign_description);
                                        $('#modal-callrecordings').html(JSONObjectcampaign.data.campaign_recording);
                                        $('#modal-camptype').html(JSONObjectcampaign.data.campaign_type);
                                        $('#modal-campaigncid').html(JSONObjectcampaign.data.campaign_cid);                                        
                                        $('#modal-localcalltime').append(JSONObjectcampaign.data.local_call_time);                                       
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
		
		setInterval(load_totalSales,5000);
		setInterval(load_INSalesHour,5000);
		setInterval(load_OUTSalesPerHour,5000);
		
		setInterval(load_TotalActiveLeads,5000);
		setInterval(load_LeadsinHopper,5000);
		setInterval(load_TotalDialableLeads,5000);
		
		setInterval(load_RingingCalls,5000);
		setInterval(load_IncomingQueue,5000);
		setInterval(load_AnsweredCalls,5000);
		setInterval(load_DroppedCalls,5000);
		setInterval(load_TotalCalls,5000);
		setInterval(load_LiveOutbound,5000);
		
		// ... cluster status table ...
		setInterval(load_cluster_status,10000);
		
		// ... agent and campaign resources ...
		setInterval(load_campaigns_resources,10000);
		setInterval(load_campaigns_monitoring,10000);
		setInterval(load_agents_monitoring_summary,5000);
		
		// ... realtime monitoring ...
                setInterval(load_realtime_agents_monitoring,3000);
                setInterval(load_realtime_calls_monitoring,3000);
                setInterval(load_realtime_sla_monitoring,3000);
		
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
   
   <!-- CLASSY LOADER-->
   <script src="theme_dashboard/js/jquery-classyloader/js/jquery.classyloader.min.js"></script>
   
   <!-- =============== APP SCRIPTS ===============-->
    <script src="theme_dashboard/js/app.js"></script>
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
