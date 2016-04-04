<?php
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

// initialize session and DDBB handler
include_once('./php/UIHandler.php');
require_once('./php/LanguageHandler.php');
require_once('./php/DbHandler.php');
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$colors = $ui->generateStatisticsColors();

// calculate number of statistics and customers
$db = new \creamy\DbHandler();
$statsOk = $db->weHaveSomeValidStatistics();
$custsOk = $db->weHaveAtLeastOneCustomerOrContact();

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
     
		<!--<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />-->
        
		<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

		
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
	    <!-- ChartJS 1.0.1 -->
	    <script src="js/plugins/chartjs/Chart.min.js" type="text/javascript"></script>
		
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		
		<!-- Circle Buttons style -->
		  <link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
		
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
                    
					<!-- START Language list-->
					<div class="pull-right">
					   <div class="btn-group">
						  <button type="button" data-toggle="dropdown" class="btn btn-default">English</button>
						  <ul role="menu" class="dropdown-menu dropdown-menu-right animated fadeInUpShort">
							 <li><a href="#" data-set-lang="en">English</a>
							 </li>
							 <li><a href="#" data-set-lang="es">Spanish</a>
							 </li>
						  </ul>
					   </div>
					</div>
					<!-- END Language list    -->
					
					<!-- Page title -->
						<?php
							if ($user->userHasAdminPermission()) {
								$lh->translateText("Dashboard");
						?>
							<small class="ng-binding">Welcome to Goautodial  !</small>
						<?php
							}else{
								$lh->translateText("home");
						?>
							<small><?php $lh->translateText("your_creamy_dashboard"); ?></small>
						<?php
							}
						?>
                    
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
               <div class="col-lg-3 col-sm-6">
                  <!-- START widget-->
                  <div class="panel pt b0 widget">
                     <div class="ph">
						<div class="pull-right">
							<span class="fa-stack">
								<em class="fa fa-circle fa-stack-2x text-purple" style="margin-top: -5;"></em>
								<em class="icon-people fa-lg fa-stack-1x fa-inverse text-white pull-right"></em>
							</span>
						</div>
                        <div class="h2 mt0">
							<span class="text-lg "><?php echo $ui->API_GetTotalAgentsCall();?></span> <span class="text-sm">Agent(s) On Call</span><br/>
							<div style="padding-left: 5px;" class="text-purple">
								<span class="text-purple"><?php echo $ui->API_GetTotalAgentsPaused();?></span>	<span class="text-sm text-purple">Agent(s) On Paused</span><br/>
								<span class="text-blue"><?php echo $ui->API_getTotalAgentsWaitCalls();?></span>	<span class="text-sm text-purple">Agent(s) Waiting</span><br/>
							</div>
						</div>
                        <div class="text-uppercase">AGENTS</div>
                     </div>
                     <div data-sparkline="" data-type="line" data-width="100%" data-height="75px" data-line-color="#23b7e5" data-chart-range-min="0" data-fill-color="#23b7e5" data-spot-color="#23b7e5" data-min-spot-color="#23b7e5" data-max-spot-color="#23b7e5"
                     data-highlight-spot-color="#23b7e5" data-highlight-line-color="#23b7e5" values="2,5,3,7,4,5" style="margin-bottom: -2px" data-resize="true"></div>
					 <canvas style="display: inline-block; width: 287px; height: 10px; vertical-align: top;" width="287" height="75"></canvas>
				  </div>
               </div>
               <div class="col-lg-3 col-sm-6">
                  <!-- START widget-->
                  <div class="panel widget pt b0 widget">
                     <div class="ph">
						<div class="pull-right">
							<span class="fa-stack">
								<em class="fa fa-circle fa-stack-2x text-red" style="margin-top: -5;"></em>
								<em class="icon-basket fa-lg fa-stack-1x fa-inverse text-white "></em>
							</span>
						</div>
                        <div class="h2 mt0">
							<span class="text-lg"><?php echo $ui->API_GetTotalSales();?></span> <span class="text-sm">TOTAL Sales</span><br/>
							<div style="padding-left: 5px;">
								<span class="text-purple"><?php echo $ui->API_GetINSalesHour();?></span> <span class="text-sm text-purple">Inbound Sales</span><br/>
								<span class="text-blue"><?php echo $ui->API_GetOUTSalesPerHour();?></span> <span class="text-sm text-blue">Outbound Sales</span><br/>
							</div>
                        </div>
                        <div class="text-uppercase mb0">SALES</div>
                     </div>
                     <div data-sparkline="" data-type="line" data-width="100%" data-height="75px" data-line-color="#7266ba" data-chart-range-min="0" data-fill-color="#7266ba" data-spot-color="#7266ba" data-min-spot-color="#7266ba" data-max-spot-color="#7266ba"
                     data-highlight-spot-color="#7266ba" data-highlight-line-color="#7266ba" values="1,4,5,4,8,7,10" style="margin-bottom: -2px" data-resize="true"></div>
					 
					 <canvas style="display: inline-block; width: 287px; height: 10px; vertical-align: top;" width="287" height="75"></canvas>
				  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-12">
                  <!-- START widget-->
                  <div class="panel widget pt b0 widget">
                     <div class="ph">
						<div class="pull-right">
							<span class="fa-stack">
								<em class="fa fa-circle fa-stack-2x text-green" style="margin-top: -5;"></em>
								<em class="icon-note fa-lg fa-stack-1x fa-inverse text-white"></em>
							</span>
						</div>
                        <div class="h2 mt0">
							<span class="text-lg"><?php $ui->API_GetTotalActiveLeads(); ?></span>	<span class="text-sm">Total Active Leads</span><br/>
							<div style="padding-left: 5px;">
								<span class="text-purple"><?php $ui->API_GetLeadsinHopper(); ?></span>	<span class="text-sm text-purple">Leads in Hopper</span><br/>
								<span class="text-blue"><?php $ui->API_GetTotalDialableLeads(); ?>	<span class="text-sm text-blue">Dialable Leads</span><br/>
							</div>
						</div>
                        <div class="text-uppercase">LEAD</div>
                     </div>
                     <div data-sparkline="" data-type="line" data-width="100%" data-height="75px" data-line-color="#23b7e5" data-chart-range-min="0" data-fill-color="#23b7e5" data-spot-color="#23b7e5" data-min-spot-color="#23b7e5" data-max-spot-color="#23b7e5"
                     data-highlight-spot-color="#23b7e5" data-highlight-line-color="#23b7e5" values="4,5,3,10,7,15" style="margin-bottom: -2px" data-resize="true"></div>
					 <canvas style="display: inline-block; width: 287px; height: 10px; vertical-align: top;" width="287" height="75"></canvas>
				  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-12">
                  <!-- START widget-->
                  <div class="panel widget pt b0 widget">
                     <div class="ph">
						<div class="pull-right">
							<span class="fa-stack">
								<em class="fa fa-circle fa-stack-2x text-blue" style="margin-top: -5;"></em>
								<em class="icon-earphones-alt fa-lg fa-stack-1x fa-inverse text-white"></em>
							</span>
						</div>
                        <div class="h2 mt0" id="autoload_calls">
							<span class="text-lg" id="load_total_calls"> <?php echo $ui->API_getTotalcalls();?> </span>
								<span class="text-sm">Total Calls</span><br/>
							<div style="padding-left: 5px;">
								<span class="text-purple" id="load_calls_ringing"> <?php echo $ui->API_GetRingingCall();?> </span>
									<span class="text-sm text-purple">Call(s) Ringing</span><br/>
								<span class="text-blue" id="load_live_outbound"> <?php echo $ui->API_GetLiveOutbound();?></span>
									<span class="text-sm text-blue">Live Outbound</span><br/>
							</div>
						</div>
                        <div class="text-uppercase">Calls</div>
                     </div>
                     <div data-sparkline="" data-type="line" data-width="100%" data-height="75px" data-line-color="#7266ba" data-chart-range-min="0" data-fill-color="#7266ba" data-spot-color="#7266ba" data-min-spot-color="#7266ba" data-max-spot-color="#7266ba"
                     data-highlight-spot-color="#7266ba" data-highlight-line-color="#7266ba" values="1,3,4,5,7,8" style="margin-bottom: -2px" data-resize="true"></div>
					 <canvas style="display: inline-block; width: 287px; height: 10px; vertical-align: top;" width="287" height="75"></canvas>
				  </div>
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
                    </div>   <!-- /.row -->
					<?php } ?>          
	
	<!--===== CHART =======-->
                <!-- wrapper for chart and loader -->
                    <div class="row">
					<!-- calls per hour chart-->
                       <div class="col-lg-9">
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
                          <!-- END widget-->
                       </div>
	<!--==== DROP CALLS PERCENTAGE PIE LOADER ======-->
					   <div class="col-lg-3">
							<div class="panel panel-default">
							   <div class="panel-body">
								<a href="#" class="text-muted pull-right">
									 <em class="fa fa-arrow-right"></em>
								</a>
								<div class="text-info">Dropped Calls Percentage</div>
								<?php
									// GET DATA FROM UIHandler.php
									$data = $ui->API_GetDroppedPercentage();
									$droppedpercentage = explode(";",$data);
									foreach ($droppedpercentage AS $temp) {
									  $temp = explode("=",$temp);
									  $results[$temp[0]] = $temp[1];
									}
								   
								   $dropcall_result = NULL;
								   
									if ($results["result"]=="success") {
									  # Result was OK!
									  //var_dump($results); #to see the returned arrays.
										   if($reuslt["drop_call_per"] == NULL){
													$dropcall_result = 0;
										   }else {
													$dropcall_result = $results["drop_call_per"];
										   }
									}
								?>
								<center>
									<div width="200" height="200" style="margin-top: 40px;margin-bottom: 40px;">
										<input type="text"
										class="knob" value="<?php echo $dropcall_result;?>" data-width="150" data-height="150" data-padding="21px"
										data-fgcolor="#3c8dbc" data-readonly="true" readonly="readonly"
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
											color: <?php echo $theme;?>
											padding: 0px;
											-webkit-appearance: none;
											background: none;
										">
									</div>
								</center>
								<div class="panel-footer">
								   <p class="text-muted">
									  <em class="fa fa-upload fa-fw"></em>
									  <span>This day: <?php echo $dropcall_result;?>%</span>
								   </p>
								</div>
								</div>
							 </div>
						</div>
                    <!-- END chart-->
					</div><!-- end of 2nd row -->
		<!-- 3rd row -->
					<div class="row">
						<div class="col-lg-6">
						   <!-- START panel tab-->
						   <div role="tabpanel" class="panel panel-transparent">
							  <ul role="tablist" class="nav nav-tabs nav-justified">
							  
							  <!-- Nav task panel tabs-->
								 <li role="presentation" class="active">
									<a href="#home" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
									   <em class="fa fa-clock-o fa-fw"></em>Tasks Panel</a>
								 </li>
							<!-- transaction panel tab -->
								 <li role="presentation">
									<a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" class="bb0">
									   <em class="fa fa-money fa-fw"></em>Transactions Panel</a>
								 </li>
							  </ul>
							  <!-- Tab panes-->
							  <div class="tab-content p0 bg-white">
								 <div id="home" role="tabpanel" class="tab-pane active">
									<!-- START list group-->
									<div class="list-group mb0">
										<a href="#" class="list-group-item bt0">
										   <span class="label label-purple pull-right">just now</span>
										   <em class="fa fa-fw fa-calendar mr"></em>Calendar updated</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">4 minutes ago</span>
										   <em class="fa fa-fw fa-comment mr"></em>Commented on a post</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">23 minutes ago</span>
										   <em class="fa fa-fw fa-truck mr"></em>Order 392 shipped</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">46 minutes ago</span>
										   <em class="fa fa-fw fa-money mr"></em>Invoice 653 has been paid</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">1 hour ago</span>
										   <em class="fa fa-fw fa-user mr"></em>A new user has been added</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">2 hours ago</span>
										   <em class="fa fa-fw fa-check mr"></em>Completed task: "pick up dry cleaning"</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">yesterday</span>
										   <em class="fa fa-fw fa-globe mr"></em>Saved the world</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">two days ago</span>
										   <em class="fa fa-fw fa-check mr"></em>Completed task: "fix error on sales page"</a>
										<a href="#" class="list-group-item">
										   <span class="label label-purple pull-right">two days ago</span>
										   <em class="fa fa-fw fa-check mr"></em>Completed task: "fix error on sales page"</a>
									</div>
									<!-- END list group-->
									<div class="panel-footer text-right"><a href="#" class="btn btn-default btn-sm">View All Activity </a>
									</div>
								 </div>
								 <div id="profile" role="tabpanel" class="tab-pane">
									<!-- START table responsive-->
									<div class="table-responsive">
									   <table class="table table-bordered table-hover table-striped">
										  <thead>
											 <tr>
												<th>Order #</th>
												<th>Order Date</th>
												<th>Order Time</th>
												<th>Amount (USD)</th>
											 </tr>
										  </thead>
										  <tbody>
											 <tr>
												<td>3326</td>
												<td>10/21/2013</td>
												<td>3:29 PM</td>
												<td>$321.33</td>
											 </tr>
										  </tbody>
									   </table>
									</div>
									<!-- END table responsive-->
									<div class="panel-footer text-right"><a href="#" class="btn btn-default btn-sm">View All Transactions</a>
									</div>
								 </div>
							  </div>
						   </div>
						   <!-- END panel tab-->
						</div><!-- end of tasks and transaction panels -->
						
						<div class="col-lg-3"><!-- START messages and activity-->
							<div class="panel panel-default">
							   <div class="panel-heading">
								  <div class="panel-title">Latest activities</div>
							   </div>
							   <!-- START list group-->
							   <div class="list-group">
								  <!-- START list group item-->
								  <div class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <span class="fa-stack">
											  <em class="fa fa-circle fa-stack-2x text-purple"></em>
											  <em class="fa fa-cloud-upload fa-stack-1x fa-inverse text-white"></em>
										   </span>
										</div>
										<div class="media-box-body clearfix">
										   <small class="text-muted pull-right ml">15m</small>
										   <div class="media-box-heading"><a href="#" class="text-purple m0">NEW FILE</a>
										   </div>
										   <p class="m0">
											  <small><a href="#">Bootstrap.xls</a>
											  </small>
										   </p>
										</div>
									 </div>
								  </div>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <div class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <span class="fa-stack">
											  <em class="fa fa-circle fa-stack-2x text-info"></em>
											  <em class="fa fa-file-text-o fa-stack-1x fa-inverse text-white"></em>
										   </span>
										</div>
										<div class="media-box-body clearfix">
										   <small class="text-muted pull-right ml">2h</small>
										   <div class="media-box-heading"><a href="#" class="text-info m0">NEW DOCUMENT</a>
										   </div>
										   <p class="m0">
											  <small><a href="#">Bootstrap.doc</a>
											  </small>
										   </p>
										</div>
									 </div>
								  </div>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <div class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <span class="fa-stack">
											  <em class="fa fa-circle fa-stack-2x text-danger"></em>
											  <em class="fa fa-exclamation fa-stack-1x fa-inverse text-white"></em>
										   </span>
										</div>
										<div class="media-box-body clearfix">
										   <small class="text-muted pull-right ml">5h</small>
										   <div class="media-box-heading"><a href="#" class="text-danger m0">BROADCAST</a>
										   </div>
										   <p class="m0"><a href="#">Read</a>
										   </p>
										</div>
									 </div>
								  </div>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <div class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <span class="fa-stack">
											  <em class="fa fa-circle fa-stack-2x text-success"></em>
											  <em class="fa fa-clock-o fa-stack-1x fa-inverse text-white"></em>
										   </span>
										</div>
										<div class="media-box-body clearfix">
										   <small class="text-muted pull-right ml">15h</small>
										   <div class="media-box-heading"><a href="#" class="text-success m0">NEW MEETING</a>
										   </div>
										   <p class="m0">
											  <small>On
												 <em>10/12/2015 09:00 am</em>
											  </small>
										   </p>
										</div>
									 </div>
								  </div>
								  <!-- END list group item-->
							   </div>
							   <!-- END list group-->
							   <!-- START panel footer-->
								<div class="panel-footer clearfix">
									<a href="#" class="pull-left">
									   <small>Load more</small>
									</a>
								</div>
							</div> <!-- END panel-footer-->
						</div><!-- END messages and activity-->
						
					<!-- START loader widget-->
						<div class="col-lg-3">
							<div class="panel panel-default">
							   <div class="panel-body">
									<a href="#" class="text-muted pull-right">
										 <em class="fa fa-arrow-right">adwadwadwa</em>
									</a>
									<div class="text-info"></div>
									<center>
										<div width="200" height="200" style="margin-top: 40px;margin-bottom: 40px;">
											<input type="text"
											class="knob" value="30" data-width="150" data-height="150" data-padding="21px"
											data-fgcolor="#3c8dbc" data-readonly="true" readonly="readonly"
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
												color: rgb(60, 141, 188);
												padding: 0px;
												-webkit-appearance: none;
												background: none;
											">
										</div>
									</center>
								   <div class="panel-footer">
									  <p class="text-muted">
										 <em class="fa fa-upload fa-fw"></em>
										 <span>This Month</span>
										 <span class="text-dark">1000 Gb</span>
									  </p>
								   </div>
								</div>
								<!-- END loader widget-->
							</div>
						</div>
						
					</div><!-- end of row -->
						
					<div class="row">
						<!-- Team Messages -->
						<div class="col-lg-6">
							<div class="panel panel-default">
							   <div class="panel-heading">
								  <div class="pull-right label label-danger">5</div>
								  <div class="pull-right label label-success">12</div>
								  <div class="panel-title">Team messages</div>
							   </div>
							   <!-- START list group-->
							   <div data-height="230" data-scrollable="" class="list-group">
								  <!-- START list group item-->
								  <a href="#" class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <img src="theme_dashboard/img/user/02.jpg" alt="Image" class="media-box-object img-circle thumb32">
										</div>
										<div class="media-box-body clearfix">
										   <small class="pull-right">2h</small>
										   <strong class="media-box-heading text-primary">
											  <span class="circle circle-success circle-lg text-left"></span>Catherine Ellis</strong>
										   <p class="mb-sm">
											  <small>Goautodial, the best...</small>
										   </p>
										</div>
									 </div>
								  </a>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <a href="#" class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <img src="theme_dashboard/img/user/03.jpg" alt="Image" class="media-box-object img-circle thumb32">
										</div>
										<div class="media-box-body clearfix">
										   <small class="pull-right">3h</small>
										   <strong class="media-box-heading text-primary">
											  <span class="circle circle-success circle-lg text-left"></span>Jessica Silva</strong>
										   <p class="mb-sm">
											  <small>James is macho.</small>
										   </p>
										</div>
									 </div>
								  </a>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <a href="#" class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <img src="theme_dashboard/img/user/09.jpg" alt="Image" class="media-box-object img-circle thumb32">
										</div>
										<div class="media-box-body clearfix">
										   <small class="pull-right">4h</small>
										   <strong class="media-box-heading text-primary">
											  <span class="circle circle-danger circle-lg text-left"></span>Jessie Wells</strong>
										   <p class="mb-sm">
											  <small>DOTA tiiiime...</small>
										   </p>
										</div>
									 </div>
								  </a>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <a href="#" class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <img src="theme_dashboard/img/user/12.jpg" alt="Image" class="media-box-object img-circle thumb32">
										</div>
										<div class="media-box-body clearfix">
										   <small class="pull-right">1d</small>
										   <strong class="media-box-heading text-primary">
											  <span class="circle circle-danger circle-lg text-left"></span>Rosa Burke</strong>
										   <p class="mb-sm">
											  <small>Go letran!</small>
										   </p>
										</div>
									 </div>
								  </a>
								  <!-- END list group item-->
								  <!-- START list group item-->
								  <a href="#" class="list-group-item">
									 <div class="media-box">
										<div class="pull-left">
										   <img src="theme_dashboard/img/user/10.jpg" alt="Image" class="media-box-object img-circle thumb32">
										</div>
										<div class="media-box-body clearfix">
										   <small class="pull-right">2d</small>
										   <strong class="media-box-heading text-primary">
											  <span class="circle circle-danger circle-lg text-left"></span>Michelle Lane</strong>
										   <p class="mb-sm">
											  <small>Watching secret affair right now...</small>
										   </p>
										</div>
									 </div>
								  </a>
								  <!-- END list group item-->
							   </div>
							   <!-- END list group-->
							   <!-- START panel footer-->
							   <div class="panel-footer clearfix">
								  <div class="input-group">
									 <input type="text" placeholder="Search message .." class="form-control input-sm">
									 <span class="input-group-btn">
										<button type="submit" class="btn btn-default btn-sm"><i class="fa fa-search"></i>
										</button>
									 </span>
								  </div>
							   </div>
							   <!-- END panel-footer-->
							</div>
						</div><!-- end team messages -->
			<!--==== VECTOR MAP LOADER ======-->
						<div ng-controller="VectorMapController" class="col-lg-6">
							<div class="panel panel-transparent">
							   <div data-vector-map="" data-height="450" data-scale='0' data-map-name="world_mill_en"></div>
							</div>
						 </div>

					</div>
					
					<?php print $ui->hooksForDashboard(); ?>
					
					<div class="bottom-menu skin-blue">
						<?php print $ui->getCircleButton("calls", "plus"); ?>
						<div class="fab-div-area" id="fab-div-area">
								<ul class="fab-ul" style="height: 250px;">
									<li class="li-style"><a class="fa fa-dashboard fab-div-item" data-toggle="modal" data-target="#add_campaigns_modal"></a></li><br/>
									<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_users"> </a></li>
								</ul>
							</div>
					</div>
				
                </section><!-- /.content -->
				
            </aside><!-- /.right-side -->
			
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
		
		
	<script>
		$(document).ready(function(){
			$(".bottom-menu").on('mouseenter mouseleave', function () {
			  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
			});
		});
	</script>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

	<!-- get API data for chart from UIHandler.php -->
		<?php $callsperhour = $ui->API_getCallPerHour();
		//var_dump($data);
			 $data = explode(";",$callsperhour);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }
			
		?>
	
	<!-- Inbound and Outbound Calls Per Hour Data -->
	<script>
		(function(window, document, $, undefined){
			$(function(){
			  var datav3 = [{
				"label": "Inbound Calls",
				"color": "#F39C12",
				"data": [
				<?php
					echo '["9AM",'.$results["Hour9"].'],';
					echo '["10AM",'.$results["Hour10"].'],';
					echo '["11AM",'.$results["Hour11"].'],';
					echo '["12NN",'.$results["Hour12"].'],';
					echo '["1AM",'.$results["Hour13"].'],';
					echo '["2PM",'.$results["Hour14"].'],';
					echo '["3PM",'.$results["Hour15"].'],';
					echo '["4PM",'.$results["Hour16"].'],';
					echo '["5PM",'.$results["Hour17"].'],';
					echo '["6PM",'.$results["Hour18"].'],';
					echo '["7PM",'.$results["Hour19"].'],';
					echo '["8PM",'.$results["Hour20"].'],';
					echo '["9PM",'.$results["Hour21"].']';
				?>

			  ]},
				{
				"label": "Dropped Calls",
				"color": "#656565",
				"data": [
				<?php
					echo '["9AM",'.$results["Hour9d"].'],';
					echo '["10AM",'.$results["Hour10d"].'],';
					echo '["11AM",'.$results["Hour11d"].'],';
					echo '["12NN",'.$results["Hour12d"].'],';
					echo '["1AM",'.$results["Hour13d"].'],';
					echo '["2PM",'.$results["Hour14d"].'],';
					echo '["3PM",'.$results["Hour15d"].'],';
					echo '["4PM",'.$results["Hour16d"].'],';
					echo '["5PM",'.$results["Hour17d"].'],';
					echo '["6PM",'.$results["Hour18d"].'],';
					echo '["7PM",'.$results["Hour19d"].'],';
					echo '["8PM",'.$results["Hour20d"].'],';
					echo '["9PM",'.$results["Hour21d"].']';
				?>
				]
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
					content: function (label, x, y) { return x + ' : ' + y; }
				},
				xaxis: {
					tickColor: '#fcfcfc',
					mode: 'categories'
				},
				yaxis: {
					min: 0,
					max: 5, // optional: use it for a clear represetation
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
		
	/*
	 * JQuery Knob = need for dropped calls percentage and other pie loader
	 */
		
		$(function () {
		  /* jQueryKnob */
	  
		  $(".knob").knob({
			/*change : function (value) {
			 //console.log("change : " + value);
			 },
			 release : function (value) {
			 console.log("release : " + value);
			 },
			 cancel : function () {
			 console.log("cancel : " + this.value);
			 },*/
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
	
	<script>
		// autoloading of CALLS
			var auto_refresh = setInterval(
			function ()
			{
					console.log("calls div auto loaded");
				$('#autoload_calls');
			}, 5000); // refresh every 10000 milliseconds
			
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
   <!-- RTL demo--
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
   <!-- CLASSY LOADER-->
   <script src="theme_dashboard/js/jquery-classyloader/js/jquery.classyloader.min.js"></script>
   <!-- VECTOR MAP-->
   <script src="theme_dashboard/js/ika.jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
   <script src="theme_dashboard/js/ika.jvectormap/jquery-jvectormap-world-mill-en.js"></script>
   <script src="theme_dashboard/js/ika.jvectormap/jquery-jvectormap-us-mill-en.js"></script>
   <script src="theme_dashboard/js/demo/demo-vector-map.js"></script>
   <!-- =============== APP SCRIPTS ===============-->
    <script src="theme_dashboard/js/app.js"></script>
	<script src="theme_dashboard/js/jquery-knob/dist/jquery.knob.min.js"></script>
    </body>
</html>
