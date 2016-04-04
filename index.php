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
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
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
    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
	        <!-- header logo: style can be found in header.less -->
			<?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
						<!-- Page title -->
						<?php
							if ($user->userHasAdminPermission()) {
								header("location: test_dashboard.php");
							}
						?>
                        <?php $lh->translateText("home"); ?>
                        <small><?php $lh->translateText("your_creamy_dashboard"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-bar-chart-o"></i> <?php $lh->translateText("home"); ?></a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

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

                    <!-- Status boxes -->
					<div class="row">
						<?php print $ui->dashboardInfoBoxes($user->getUserId()); ?>
			        </div><!-- /.row -->                    

                     <!-- Statistics -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-md-7"> 
	                    	<!-- Gráfica de clientes -->   
	                        <div class="box box-default">
	                            <div class="box-header">
	                                <i class="fa fa-bar-chart-o"></i>
	                                <h3 class="box-title"><?php $lh->translateText("customer_statistics"); ?></h3>
	                            </div>
                                <div class="box-body" id="graph-box"><div>
	                            <?php if ($statsOk) { ?>
									<canvas id="lineChart" height="250"></canvas>
	                            <?php } else { 
		                        	print $ui->calloutWarningMessage($lh->translationFor("no_statistics_yet"));
		                        } ?>
	                            </div></div>
	                        </div>
                        </section><!-- /.Left col -->
						<!-- Left col -->
                        <section class="col-md-5"> 
	                    	<!-- Gráfica de clientes -->   
	                        <div class="box box-default">
	                            <div class="box-header">
	                                <i class="fa fa-bar-chart-o"></i>
	                                <h3 class="box-title"><?php $lh->translateText("current_customer_distribution"); ?></h3>
	                            </div>
                                <div class="box-body" id="graph-box">
		                            <?php if ($custsOk) { ?>
	                                <div class="row">
										<div class="col-md-8">
											<canvas id="pieChart" height="250"></canvas>
		                            	</div>
		                            	<div class="col-md-4 chart-legend" id="customers-chart-legend">
		                            	</div>
	                                 </div>
		                            <?php } else { 
			                        	print $ui->calloutWarningMessage($lh->translationFor("no_customers_yet"));
			                        	print $ui->simpleLinkButton("no_customers_add_customer", $lh->translationFor("create_new"), "customerslist.php?customer_type=clients_1");
			                        } ?>
	                            </div>
	                        </div>
							
                        </section><!-- /.Left col -->
						
                    </div><!-- /.row (main row) -->
				
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
					
					<div class="modal fade" id="add_campaigns_modal" name="add_campaigns_modal" tabindex="-1" role="dialog" aria-hidden="true">
			        <div class="modal-dialog">
			            <div class="modal-content">
						
			                <div class="modal-header">
			                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translateText("Campaign Wizard"); ?></b></h4>
			                </div>

			                <form action="" method="post" name="" id="">
			                    <div class="modal-body">
			                        <div class="form-group">
										<center><h4><b><?php $lh->translateText("Step 1  » Outbound"); ?> </b></h4></center>
			                            <label for="campaign_type"><?php $lh->translateText("Campaign Type"); ?></label>
			                            <input type="text required" class="form-control" id="campaign_type" name="campaign_type" placeholder="<?php $lh->translateText("Campaign Type"); ?>">
										
										<label for="campaign_id"><?php $lh->translateText("Campaign ID"); ?></label>
			                            <input type="text required" class="form-control" id="campaign_id" name="campaign_id" placeholder="<?php $lh->translateText("Campaign ID"); ?>">
										
										<label for="campaign_name"><?php $lh->translateText("Campaign Name"); ?></label>
			                            <input type="text required" class="form-control" id="campaign_name" name="campaign_name" placeholder="<?php $lh->translateText("Campaign Name"); ?>">
										
									<hr/>
										<center><h4><b><?php $lh->translateText("Step 2  » Load Leads"); ?> </b></h4></center>
										<label for="lead_file"><?php $lh->translateText("Lead File"); ?></label>
			                            <input type="file" class="form-control" id="lead_file" name="lead_file" placeholder="<?php $lh->translateText("Lead File"); ?>">
										
										<label for="list_id"><?php $lh->translateText("List ID"); ?></label>
			                            <input type="text required" class="form-control" id="list_id" name="list_id" placeholder="<?php $lh->translateText("List ID"); ?>">
										
										<label for="country"><?php $lh->translateText("Country"); ?></label>
			                            <input type="text required" class="form-control" id="country" name="country" placeholder="<?php $lh->translateText("Country"); ?>">
										
										<label for="duplicate_check"><?php $lh->translateText("Check For Duplicates"); ?></label>
			                            <select id="duplicate_check" class="form-control">
											<option>NO DUPLICATE CHECK</option>
											<option>CHECK DUPLICATES BY PHONE IN LIST ID</option>
											<option>CHECK DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>
										</select><br/>
										<button type="button" class="btn"> U P L O A D   L E A D S</button>
										
									<hr/>
										<center><h4><b><?php $lh->translateText("Step 3  » Information"); ?> </b></h4></center>
										<label for="dial_method"><?php $lh->translateText("Dial Method"); ?></label>
										<select id="dial_method" class="form-control">
											<option>MANUAL</option>
											<option>AUTO DIAL</option>
											<option>PREDICTIVE</option>
											<option>INBOUND MAN</option>
										</select>	
											
										<label for="autodial_lvl"><?php $lh->translateText("AutoDial Level"); ?></label>
										<select id="autodial_lvl" class="form-control">
											<option>OFF</option>
											<option>SLOW</option>
											<option>NORMAL</option>
											<option>HIGH</option>
											<option>MAX</option>
											<option>MAX PREDICTIVE</option>
										</select>
										<label for="carrier_for_campaign"><?php $lh->translateText("Carrier to use for this Campaign"); ?></label>
										<select id="carrier_for_campaign" >
											<option>CUSTOM DIAL PREFIX</option>
										</select>
										<input type="number">
										<br/>
										<label for="answering_machine"><?php $lh->translateText("Answering Machine Detection"); ?></label>
										<select id="answering_machine" class="form-control">
											<option>ON</option>
											<option>OFF</option>
										</select>
									</div>
			                    </div>
			                    <div class="modal-footer clearfix">
			                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="changetaskCancelButton"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
			                        <button type="submit" class="btn btn-primary pull-right" id="changeeventsOkButton"><i class="fa fa-check"></i> <?php $lh->translateText("Add New Campaign"); ?></button>
								</div>
								
			                </form>
							
			            </div><!-- /.modal-content -->
			        </div><!-- /.modal-dialog -->
			    </div><!-- /.modal -->	
				
				
				<!-- USERS MODAL -->
				<div class="modal fade" id="add_users" name="add_users" tabindex="-1" role="dialog" aria-hidden="true">
			        <div class="modal-dialog">
			            <div class="modal-content">

			                <div class="modal-header">
			                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translateText("User Wizard"); ?></b></h4>
			                </div>
							
			                <form action="" method="post" name="" id="">
			                    <div class="modal-body">
									<div class="form-group">
										<center><h4><b><?php $lh->translateText("Step 1  » Add New User"); ?> </b></h4></center>
			                        
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="0"
										aria-valuemin="0" aria-valuemax="100" style="width:0%">
										  0%
										</div>
									</div>
									</div>
			                    </div>
			                    <div class="modal-footer clearfix">
			                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="changetaskCancelButton"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
			                        <button type="submit" class="btn btn-primary pull-right" id="changeeventsOkButton"><i class="fa fa-check"></i> <?php $lh->translateText("Add New Campaign"); ?></button>
								</div>
								
			                </form>
							
			            </div><!-- /.modal-content -->
			        </div><!-- /.modal-dialog -->
			    </div><!-- /.modal -->
				
				
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

		<!-- Statistics -->
		<?php if ($statsOk) { ?>
		<script type="text/javascript">
			
			var lineChartData = {
			  <?php print $ui->generateLineChartStatisticsData($colors); ?>
	        };
			
		  var lineChartOptions = {
          //Boolean - If we should show the scale at all
          showScale: true,
          //Boolean - Whether grid lines are shown across the chart
          scaleShowGridLines: false,
          //String - Colour of the grid lines
          scaleGridLineColor: "rgba(0,0,0,.05)",
          //Number - Width of the grid lines
          scaleGridLineWidth: 1,
          //Boolean - Whether to show horizontal lines (except X axis)
          scaleShowHorizontalLines: true,
		  // String - Template string for multiple tooltips
		  multiTooltipTemplate: " <%= datasetLabel %> <%= value %>",
		  //Boolean - Whether to show vertical lines (except Y axis)
          scaleShowVerticalLines: true,
          //Boolean - Whether the line is curved between points
          bezierCurve: true,
          //Number - Tension of the bezier curve between points
          bezierCurveTension: 0.3,
          //Boolean - Whether to show a dot for each point
          pointDot: true,
          //Number - Radius of each point dot in pixels
          pointDotRadius: 4,
          //Number - Pixel width of point dot stroke
          pointDotStrokeWidth: 1,
          //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
          pointHitDetectionRadius: 20,
          //Boolean - Whether to show a stroke for datasets
          datasetStroke: true,
          //Number - Pixel width of dataset stroke
          datasetStrokeWidth: 2,
          //Boolean - Whether to fill the dataset with a color
          datasetFill: false,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
          //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: false,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true
        };

        //-------------
        //- LINE CHART -
        //--------------
        var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
        var lineChart = new Chart(lineChartCanvas);
        lineChart.Line(lineChartData, lineChartOptions);
		</script>
		<?php } ?>
		<?php if ($custsOk) { ?>
		<script type="text/javascript">

        //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
        var PieData = [
          <?php print $ui->generatePieChartStatisticsData($colors); ?>
        ];
        var pieOptions = {
          //Boolean - Whether we should show a stroke on each segment
          segmentShowStroke: true,
          //String - The colour of each segment stroke
          segmentStrokeColor: "#fff",
          //Number - The width of each segment stroke
          segmentStrokeWidth: 2,
          //Number - The percentage of the chart that we cut out of the middle
          percentageInnerCutout: 50, // This is 0 for Pie charts
          //Number - Amount of animation steps
          animationSteps: 100,
          //String - Animation easing effect
          animationEasing: "easeOutBounce",
          //Boolean - Whether we animate the rotation of the Doughnut
          animateRotate: true,
          //Boolean - Whether we animate scaling the Doughnut from the centre
          animateScale: false,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true,
          // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: false,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\" style=\"list-style-type: none;\"><% for (var i=0; i<segments.length; i++){%><li><i class=\"fa fa-circle-o\" style=\"color:<%=segments[i].fillColor%>\"> </i><%if(segments[i].label){%>  <%=segments[i].label%><%}%></li><%}%></ul>"
        };
        var pieChart = new Chart(pieChartCanvas).Doughnut(PieData, pieOptions);
		$('#customers-chart-legend').html(pieChart.generateLegend());

		</script>
		<?php } ?>
    </body>
</html>
