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
include_once('./php/UIHandler.php');//die(print_r($user));
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

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
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
