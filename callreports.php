<?php	
/**
 * @file 		callreports.php
 * @brief 		Reports and Analytics
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja 
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
**/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
	
	$perm = $api->goGetPermissions('reportsanalytics');
	
	$allowed_page = 0;
	foreach ($perm as $key => $value) {
		if ($value == 'Y') {
			$allowed_page++;
		}
	}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php $lh->translateText("reports_and_go_analytics"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->DataTablesTheme();
		?>
		
		<!-- FOR EXPORT -->
		<!--<script src="js/plugins/datatables/bpampuch/pdfmake/vfs_fonts.js" type="text/javascript"></script>
		<script src="js/plugins/datatables/bpampuch/pdfmake/pdfmake.min.js" type="text/javascript"></script>-->
		<script src="js/plugins/datatables/buttons/buttons.html5.min.js" type="text/javascript"></script>
		<script src="js/plugins/datatables/buttons/buttons.print.min.js" type="text/javascript"></script>
		<script src="js/plugins/datatables/buttons/buttons.flash.min.js" type="text/javascript"></script>
		<script src="js/plugins/datatables/buttons/dataTables.buttons.min.js" type="text/javascript"></script>
		<script src="js/plugins/datatables/jszip.min.js" type="text/javascript"></script>

        <!-- Datetime picker --> 
        <link rel="stylesheet" href="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
        <!-- Date Picker -->
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		
		<!-- CHOSEN-->
   		<link rel="stylesheet" src="js/dashboard/chosen_v1.2.0/chosen.min.css">

    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("reports_and_go_analytics"); ?>
                        <small><?php $lh->translateText("call_reports"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("call_reports"); ?></li>
						<li class="active"><?php $lh->translateText("reports_and_go_analytics"); ?>
                    </ol>
                </section>
            
            <?php
                $campaigns = $api->API_getAllCampaigns();
				$ingroups = $api->API_getAllInGroups();
				$disposition = $api->API_getAllDispositions();
            ?>
                <!-- Main content -->
                <section class="content">
                <?php if ($allowed_page > 0) { ?>
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <legend><?php $lh->translateText("call_reports"); ?></legend>

                                    <div class="report-loader" style="color:lightgray; display:none;">
                                        <center>
                                            <h3>
                                                <i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
                                                <?php $lh->translateText("loading..."); ?>
                                            </h3>
                                        </center>
                                    </div>
                                    <div class="box-body" id="table">
                                        <div collapse="panelChart9" class="panel-wrapper">
                                            <div class="panel-body">
                                               <div class="chart-splinev3 flot-chart"></div> <!-- data is in JS -> demo-flot.js -> search (Overall/Home/Pagkain)--> 
                                            </div>
                                        </div>
                                    </div><!-- /.box-body -->

                                </div><!-- /.panel-body -->
                            </div><!--/.panel-->
                        </div>
						<form id="search_form">
                        <div class="col-lg-3">
                            <h3 class="m0 pb-lg"><?php $lh->translateText("filters"); ?></h3>                           

                                <!-- HIDDEN POSTS -->
                                <!-- <input type="hidden" name="userID" id="userID" value="<?php //echo $user->getUserName();?>"> deprecated -->
                                <div class="form-group">
                                    <label for="filter_type"><?php $lh->translateText("type"); ?></label>
                                    <select class="form-control select2" id="filter_type" style="width:100%;">
									<?php
										if ($perm->reportsanalytics_display == 'Y' && $user->getUserRole() == CRM_DEFAULTS_USER_ROLE_ADMIN) {
									?>
											<option value="stats" selected><?php echo $lh->translationFor("stats"); ?></option>
											<option value="agent_detail"><?php echo $lh->translationFor("agent_detail"); ?></option>
											<option value="agent_pdetail"><?php echo $lh->translationFor("agent_pdetail"); ?></option> 
											<option value="dispo"><?php echo $lh->translationFor("dispo"); ?></option>
											<option value="sales_agent"><?php echo $lh->translationFor("sales_agent"); ?></option>
											<option value="sales_tracker"><?php echo $lh->translationFor("sales_tracker"); ?></option>
											<option value="inbound_report"><?php echo $lh->translationFor("inbound_call_report"); ?></option>
											<option value="call_export_report"><?php echo$lh->translationFor("export_call_report"); ?></option>
									<?php
										} else {
											if ($perm->reportsanalytics_statistical_display == 'Y') { echo '<option value="stats">'.$lh->translationFor("stats").'</option>'; }
											if ($perm->reportsanalytics_agent_time_display == 'Y') { echo '<option value="agent_detail">'.$lh->translationFor("agent_detail").'</option>'; }

										        if ($perm->reportsanalytics_agent_time_display == 'Y') { echo '<option value="agent_pdetail">'.$lh->translationFor("agent_pdetail").'</option>'; }
											if ($perm->reportsanalytics_agent_time_display == 'Y' && REPORTS_SM_AGENT_PERFORMANCE_DETAIL === 'y') { echo '<option value="agent_pdetailSM">'.$lh->translationFor("agent_pdetail").' SM</option>'; }
											if ($perm->reportsanalytics_dial_status_display == 'Y') { echo '<option value="dispo">'.$lh->translationFor("dispo").'</option>'; }
											if ($perm->reportsanalytics_agent_sales_display == 'Y') { echo '<option value="sales_agent">'.$lh->translationFor("sales_agent").'</option>'; }
											if ($perm->reportsanalytics_sales_tracker_display == 'Y') { echo '<option value="sales_tracker">'.$lh->translationFor("sales_tracker").'</option>'; }
											if ($perm->reportsanalytics_inbound_call_display == 'Y') { echo '<option value="inbound_report">'.$lh->translationFor("inbound_call_report").'</option>'; }
											if ($perm->reportsanalytics_export_call_display == 'Y') { echo '<option value="call_export_report">'.$lh->translationFor("export_call_report").'</option>'; }
										}
									?>
                                        <!--<option value="dashboard">Dashboard</option>
                                        <option value="cdr">Call History (CDRs)</option>-->
                                    </select>
                                </div>
                                <div class="form-group campaign_div">
                                    <label for="campaign_id"><?php $lh->translateText("campaign"); ?></label>
                                    <select class="form-control select2" name="campaign_id" id="campaign_id" style="width:100%;">
					<option selected disabled></option>
					<option value="ALL"><?php $lh->translateText("all_campaigns"); ?></option>
                                        <?php
                                            for($i=0; $i < count($campaigns->campaign_id);$i++) {
                                        ?>
                                            <option value="<?php echo $campaigns->campaign_id[$i];?>"><?php echo $campaigns->campaign_id[$i]." - ".$campaigns->campaign_name[$i];?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
								<div class="form-group ingroup_div" style="display:none;">
                                    <label for="ingroup_id"><?php $lh->translateText("ingroups"); ?></label>
                                    <select class="form-control select2" name="ingroup_id" id="ingroup_id" style="width:100%;">
                                        <?php
                                            for($i=0; $i < count($ingroups->group_id);$i++) {
												if ($_SESSION['usergroup'] !== "ADMIN" && preg_match("/^AGENTDIRECT/", $ingroups->group_id[$i])) continue;
                                        ?>
                                            <option value="<?php echo $ingroups->group_id[$i];?>"><?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
				<div class="form-group ingroup_div" style="display:none;">
                                    <label for="statuses"><?php $lh->translateText("statuses"); ?></label>
                                    <select class="form-control select2" name="statuses" id="statuses" style="width:100%;">
					<option value="">- - - ALL - - -</option>
                                        <?php
                                                for($a=0; $a<count($disposition->status); $a++) {
                                                        if($disposition->campaign_id[$a] != NULL){
                                                                if(in_array($disposition->status[$a], $campaigns->campaign_id)){
									echo '<option value="'.$disposition->status_name[$a].'">'.$disposition->status[$a].' - '.$disposition->status_name[$a].'</option>';
                                                                }
                                                        } else {
                                                        	echo '<option value="'.$disposition->status[$a].'">'.$disposition->status[$a].' - '.$disposition->status_name[$a].'</option>';
                                                        }
                                                }
                                        ?>

					<?php
						/*for($a=0; $a<count($disposition->status); $a++) {
					?>
						<option value="<?php echo $disposition->status[$a];?>"><?php echo $disposition->status[$a].' - '.$disposition->status_name[$a];?></option>
					<?php
						}*/
					?>
                                    </select>
                                </div>
                                <div class="form-group request_div" style="display:none;">
                                    <label><?php $lh->translateText("request"); ?></label>
                                    <div class="stats_request">
                                        <select class="form-control select2" name="request1" id="request1" style="width:100%;">
                                            <option value="daily"><?php $lh->translateText("daily"); ?></option>
                                            <option value="weekly"><?php $lh->translateText("weekly"); ?></option>
                                            <option value="monthly"><?php $lh->translateText("monthly"); ?></option>
                                        </select>
                                    </div>
                                    <div class="sales_agent_request">
                                        <select class="form-control select2" name="request2" id="request2" style="width:100%;">
                                            <option value="outbound"><?php $lh->translateText("outbound"); ?></option>
                                            <option value="inbound"><?php $lh->translateText("inbound"); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <!-- /. daily weekly monthly -->

                                    <!-- USING DATERANGE
                                    <div class="form-group">
                                        <button class="btn datetimepicker1"><i class="fa fa-calendar"></i></button>
                                        <div class='input-group date'>
                                            <input type='text' class="form-control" id="date_range"/>
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                        
                                    </div>
                                    -->

                                <div class="form-group">
                                    <label><?php $lh->translateText("start_date"); ?></label>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker1'>
                                            <input type='text' class="form-control" id="start_filterdate" name="start_filterdate" placeholder="<?php echo date("m/d/Y");?> 12:00 AM" value="<?php echo date("m/d/Y");?> 00:00:00" />
                                            <span class="input-group-addon">
                                                <!-- <span class="glyphicon glyphicon-calendar"></span>-->
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.start date -->

                                <div class="form-group">
                                    <label><?php $lh->translateText("end_date"); ?></label>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker2'>
                                            <input type='text' class="form-control" id="end_filterdate" name="end_filterdate" placeholder="<?php echo date("m/d/Y H:i:s");?>" value="<?php echo date("m/d/Y");?> 11:59 PM" />
                                            <span class="input-group-addon">
                                                <!-- <span class="glyphicon glyphicon-calendar"></span>-->
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.end date -->

                            </form>
                        </div>
                    </div>
				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
        
        <?php print $ui->standardizedThemeJS();?>

	<!-- FLOT CHART-->
	<script src="js/dashboard/js/Flot/jquery.flot.js"></script>
	<script src="js/dashboard/js/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
	<script src="js/dashboard/js/Flot/jquery.flot.resize.js"></script>
	<script src="js/dashboard/js/Flot/jquery.flot.pie.js"></script>
	<script src="js/dashboard/js/Flot/jquery.flot.time.js"></script>
	<script src="js/dashboard/js/Flot/jquery.flot.categories.js"></script>
	<script src="js/dashboard/js/flot-spline/js/jquery.flot.spline.min.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			$(document).on('click','.edit-contact',function() {
				var url = './editcontacts.php';
				var id = $(this).attr('data-id');
				//alert(extenid);
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
				$('body').append(form);  // This line is not necessary
				$(form).submit();
			});
			filterchange();
			$('#agent_detail_login').DataTable();
			$('#table_agent_pdetailSM').DataTable();
	
			$('.select2-3').select2({ theme: 'bootstrap' });
			$.fn.select2.defaults.set( "theme", "bootstrap" );

			$('#datetimepicker1').datetimepicker({
				icons: {
					//time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-crosshairs',
					clear: 'fa fa-trash'
				}
				//format: 'MM/DD/YYYY'
			});
			
			$('#datetimepicker2').datetimepicker({
				icons: {
					//time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-crosshairs',
					clear: 'fa fa-trash'
				},
				//format: 'MM/DD/YYYY',
				useCurrent: false
			});
			
			$("#datetimepicker1").on("dp.change", function (e) {				
				$('#datetimepicker2').data("DateTimePicker").minDate(e.date);
				filterchange();
			});
			
			$("#datetimepicker2").on("dp.change", function (e) {
				$('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
				filterchange();
			});
			
			/* changing reports */
			$('#filter_type').on('change', function() {
				filterchange();
			});
			
			/* changing reports */
			$('#campaign_id').on('change', function() {
				filterchange();
			});

			$('#request1').on('change', function() {
				filterchange();
			});
			
			$('#request2').on('change', function() {
				filterchange();
			});
			
			$('#ingroup_id').on('change', function() {
				var filter_type = $('#filter_type').val();				
				var request = "";
				var URL = "./php/reports/inboundreport.php";
				
				$('#table').empty();
				$(".report-loader").fadeIn("slow");
				
				$.ajax({
						url: URL,
						type: 'POST',
						data: {
							pageTitle : filter_type,
							campaignID : $("#ingroup_id").val(),
							request : request,
							userID : $("#userID").val(),
							userGroup : $("#userGroup").val(),
							fromDate : $("#start_filterdate").val(),
							toDate : $("#end_filterdate").val(),
							statuses : $("#statuses").val()
						},
						success: function(data) {
							console.log(data);
							if (data !== "") {
								$(".report-loader").fadeOut("slow");
								$('#table').html(data);
								
								if (filter_type == "inbound_report") {
									var title = "<?php $lh->translateText("inbound"); ?> Call Report";
									
									$('#inbound_report').DataTable({
										destroy: true,
										responsive: true,
										dom: 'Bfrtip',  
										buttons: [ 
											{ extend: 'copy', title: title }, 
											{ extend: 'csv', title: title }, 
											{ extend: 'excel', title: title }, 
											{ extend: 'print', title: title } 
										] 
									});
									
									$('.request_div').hide();
									$('.campaign_div').hide();
									$('.ingroup_div').show();
								}

							} else {
								$(".report-loader").fadeOut("slow");
								$('#table').html("<?php $lh->translateText("no_data"); ?>");
							}
						}
					});
			});
			
			$('#statuses').on('change', function() {
				var filter_type = $('#filter_type').val();	
				var request = "";
				var URL = "./php/reports/inboundreport.php";

				$('#table').empty();
				$(".report-loader").fadeIn("slow");
				
				$.ajax({
						url: URL,
						type: 'POST',
						data: {
							pageTitle : filter_type,
							campaignID : $("#ingroup_id").val(),
							request : request,
							userID : $("#userID").val(),
							userGroup : $("#userGroup").val(),
							fromDate : $("#start_filterdate").val(),
							toDate : $("#end_filterdate").val(),
							statuses : $("#statuses").val()
						},
						success: function(data) {
							console.log(data);
							if (data !== "") {
								$(".report-loader").fadeOut("slow");
								$('#table').html(data);

								if (filter_type == "inbound_report") {
									var title = "<?php $lh->translateText("inbound"); ?> Call Report";
									$('#inbound_report').DataTable({
										destroy: true,
										responsive: true,
										dom: 'Bfrtip',  
										buttons: [ 
											{ extend: 'copy', title: title }, 
											{ extend: 'csv', title: title }, 
											{ extend: 'excel', title: title }, 
											{ extend: 'print', title: title } 
										] 
									});
									$('.request_div').hide();
									$('.campaign_div').hide();
									$('.ingroup_div').show();
								}

							} else {
								$(".report-loader").fadeOut("slow");
								$('#table').html("<?php $lh->translateText("no_data"); ?>");
							}
						}
					});
			});
				
		/*
			* <?php $lh->translateText("inbound"); ?> and <?php $lh->translateText("outbound"); ?> Calls Per Hour Data
		*/
			(function(window, document, $, undefined) {
				$(function() {
					var datav3 = [
						{
						"label": "",
						"color": "#009688",
						"data": [
						<?php
						
							echo '["12 MN", 0],';
							echo '["12 MN", 0],';
							echo '["1 AM", 0],';
							echo '["1 AM", 0]';
						?>]
						}];
						
					var options = { series: { lines: {show: false}, points: {show: true,radius: 4},
							splines: {show: true,tension: 0.4,lineWidth: 1,fill: 0.5}
						},
						grid: { borderColor: '#eee', borderWidth: 1, hoverable: true, backgroundColor: '#fcfcfc' },
						tooltip: true, 
						tooltipOpts: { content: function (label, x, y) {  return y + ' Calls / Day';  } },
						xaxis: { tickColor: '#fcfcfc', mode: 'categories' },
						yaxis: { min: 0, max: 4, // optional: use it for a clear represetation
							tickColor: '#eee',
							//position: 'right' or 'left',
							tickFormatter: function (v) {
								return v/* + ' visitors'*/;
							}
						},
						shadowSize: 0
						};
						var chartv3 = $('.chart-splinev3');
						if (chartv3.length)
						$.plot(chartv3, datav3, options);
				});
			})(window, document, window.jQuery);

			/* Daterange
			$('#date_range').daterangepicker({
				"autoApply": true,
				"endDate": "08/17/2016"
			}, function(start, end, label) {
				console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
			});
			*/
            });
            
            function filterchange() {
				var filter_type = $('#filter_type').val();				
				var request = "";
				var URL = 'reports.php';
				var campaign_ID = $("#campaign_id").val();

                                $('.campaign_div').show();
	
				$('#table').empty();
				$(".report-loader").fadeIn("slow");				
				
				if (filter_type == "stats") {
					request = $("#request1").val();
					URL = './php/reports/statisticalreports.php';
				}
				
				if (filter_type == "agent_detail") {
					URL = './php/reports/agenttimedetails.php';
				}

                                if (filter_type == "agent_pdetail") {
                                        URL = './php/reports/agentperformancedetails.php';
                                }
			<?php if(REPORTS_SM_AGENT_PERFORMANCE_DETAIL === 'y'){ ?>	
				if (filter_type == "agent_pdetailSM") {
                                        URL = './php/reports/SM_agentperformancedetails.php';
                                }
			<?php } ?>
				if (filter_type == "dispo") {
		                    URL = './php/reports/dispo.php';
                		}

				if (filter_type == "sales_agent") {
                		    URL = './php/reports/salesagent.php';
				    request = $("#request2").val();
				}
				
				if (filter_type == "sales_tracker") {
		                    URL = './php/reports/salestracker.php';
				    request = $("#request2").val();
				}

				if (filter_type == "inbound_report"){
				    URL = './php/reports/inboundreport.php';
				    campaign_ID = $("#ingroup_id").val();
				}

				if(filter_type === "call_export_report"){
				     URL = './php/reports/exportcallreport.php';
				}
				
				$.ajax({
					url: URL,
					type: 'POST',
					data: {
						pageTitle : filter_type,
						campaignID: campaign_ID,
						request : request,
						userID : $("#userID").val(),
						userGroup : $("#userGroup").val(),
						fromDate : $("#start_filterdate").val(),
						toDate : $("#end_filterdate").val()
					},
					success: function(data) {
						console.log(data);
						if (data !== "") {
							$(".report-loader").fadeOut("slow");
							$('#table').html(data);

							if (filter_type == "stats") {
								$('.request_div').show();
								$('.stats_request').show();
								$('.sales_agent_request').hide();
								$('.campaign_div').show();
								$('.ingroup_div').hide();
							}
							
							if (filter_type == "agent_detail") {
								var title = "<?php $lh->translateText("agent_detail"); ?>";
								$('#agent_detail_top').DataTable({
									destroy: true,
									responsive: true,
									stateSave:true,
									drawCallback:function(settings) {
										var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
										pagination.toggle(this.api().page.info().pages > 1);
									},										
									dom: 'Bfrtip',
									buttons: [
										{
											text: 'Export Agent Time Detail',
											action: function ( ) {
												console.log("Exporting...");
												$( "#export_agentdetail_form" ).submit();
											}
										}
									]
								});

								var goTable = $('#agent_detail_login').DataTable({ 
									destroy: true, 
									responsive: true, 
									//stateSave: true,
									//sort: false,
									//pagination: false,
									drawCallback: function() {
										var api = this.api();

										api.columns({
											page: 'current'
										}).every(function() {
											var sum = this
											.data()
											.reduce(function(a, b) {
												var x = parseFloat(a) || 0;
												var y = parseFloat(b) || 0;
												return x + y;
											}, 0);
											$(this.footer()).html(sformat(sum));
											$('#tfoottotal').html('TOTAL');
											
										});
										
										var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
										pagination.toggle(this.api().page.info().pages > 1);
										
										$('#agent_detail_tbody').find('tr.odd, tr.even').hide();
										
									},								
									orderFixed: [[0, 'asc']],
									rowGroup: {
										startRender: null,
										endRender: function ( rows, group ) {
											var colcount = rows.columns().indexes().length;
											
											var statuses = new Array();
											for (var count = 1; count < colcount; count++) {										
												var status = rows
													.data()
													.pluck(count)
													.reduce( function (a, b) {
														return a + b*1;
													}, 0) / 1;	
												statuses.push('<td>'+sformat(status)+'</td>');
												//console.log(status);
											}
																						
											return $('<tr/>').append('<td>'+group+'</td>'+statuses);
											
										},
										
										dataSrc: 0
									}								
								});							
								
								// Change the fixed ordering when the data source is updated
								goTable.on( 'rowgroup-datasrc', function ( e, dt, val ) {
									goTable.order.fixed( {pre: [[ val, 'asc' ]]} ).draw();
								} );
							
								$('a.group-by').on( 'click', function (e) {
									e.preventDefault();
							
									goTable.rowGroup().dataSrc( $(this).data('column') );
								} );
								
								$('#agent_detail_tbody').find('tr.odd, tr.even').hide();																				
		
								$('.request_div').hide();
								$('.campaign_div').show();
								$('.ingroup_div').hide();
							}
							
							if (filter_type == "agent_pdetail") {
								var title = "<?php $lh->translateText("agent_pdetail"); ?>";
								
								$('#agent_pdetail_top').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('#agent_pdetail_mid').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('#agent_pdetail_bottom').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip'  
									//buttons: [ 
									//	{ extend: 'copy', title: title }, 
									//	{ extend: 'csv', title: title }, 
									//	{ extend: 'excel', title: title }, 
									//	{ extend: 'print', title: title } 
									//]
								});
								
								$('#agent_pdetail_login').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip'  
									//buttons: [ 
									//	{ extend: 'copy', title: title }, 
									//	{ extend: 'csv', title: title }, 
									//	{ extend: 'excel', title: title }, 
									//	{ extend: 'print', title: title } 
									//] 
								});
								
								$('.request_div').hide();
								$('.campaign_div').show();
								$('.ingroup_div').hide();
							}
							
							// SERVICE MONKEY AGENT PERFORMANCE DETAIL
						<?php //if(REPORTS_SM_AGENT_PERFORMANCE_DETAIL === 'y'){ ?>
							if (filter_type == "agent_detailSM") {
                                                                var title = "<?php $lh->translateText("agent_detail"); ?> SM";
                                                                $('#table_agent_pdetailSM').DataTable({
                                                                        destroy: true,
                                                                        responsive: true,
                                                                        stateSave:true,
                                                                        dom: 'Bfrtip'
                                                                        //buttons: [
									//	{ extend: 'csv', title: title },
                                                                        //        {
                                                                        //                text: 'Export Agent Performance Detail',
                                                                        //                action: function ( ) {
                                                                        //                        console.log("Exporting...");
                                                                        //                        $( "#export_agentPdetailSM_form" ).submit();
                                                                        //                }
                                                                        //        }
                                                                        //]
                                                                });
                                                                $('.request_div').hide();
                                                                $('.campaign_div').show();
                                                                $('.ingroup_div').hide();
                                                        }
						<?php //} ?>
							if (filter_type == "dispo") {
								var title = "<?php $lh->translateText("dispo"); ?>";
								
								$('#dispo').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('.request_div').hide();
								$('.campaign_div').show();
								$('.ingroup_div').hide();
							}
							
							if (filter_type == "sales_agent") {
								var title = "<?php $lh->translateText("sales_agent"); ?>";
								
								if ($("#request2").val() == "outbound") {
									title = title + " - <?php $lh->translateText("outbound"); ?>";
								} else {
									title = title + " - <?php $lh->translateText("inbound"); ?>";
								}
								
								$('#outbound').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('#inbound').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('.request_div').show();
								$('.sales_agent_request').show();
								$('.stats_request').hide();
								$('.campaign_div').show();
								$('.ingroup_div').hide();
							}
							
							if (filter_type == "sales_tracker") {
								var title = "<?php $lh->translateText("sales_tracker"); ?>";
								
								if ($("#request2").val() == "outbound") {
									title = title + " - <?php $lh->translateText("outbound"); ?>";
								} else {
									title = title + " - <?php $lh->translateText("inbound"); ?>";
								}
								
								$('#outbound_table').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('#inbound_table').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('.request_div').show();
								$('.sales_agent_request').show();
								$('.stats_request').hide();
								$('.campaign_div').show();
								$('.ingroup_div').hide();
							}
							
							if (filter_type == "inbound_report") {
								var title = "<?php $lh->translateText("inbound"); ?> Call Report";
								$('#inbound_report').DataTable({
									destroy: true,
									responsive: true,
									dom: 'Bfrtip',  
									buttons: [ 
										{ extend: 'copy', title: title }, 
										{ extend: 'csv', title: title }, 
										{ extend: 'excel', title: title }, 
										{ extend: 'print', title: title } 
									] 
								});
								
								$('.request_div').hide();
								$('.campaign_div').hide();
								$('.ingroup_div').show();
							}
							
							if (filter_type == "call_export_report") {
								var title = "Export Call Report";
								$('.campaign_div').hide(); 										
								$('.ingroup_div').hide();                                        
								$('.request_div').hide();
							}
						} else {
							$(".report-loader").fadeOut("slow");
							$('#table').html("<?php $lh->translateText("no_data"); ?>");
						}
					}
				});            
            }
            
			function sformat(seconds) {
				if (seconds >= 86400) {
					var fm = [
							Math.floor(seconds / 60 / 60 / 24), // DAYS
							Math.floor(seconds / 60 / 60) % 24, // HOURS
							Math.floor(seconds / 60) % 60, // MINUTES
							seconds % 60 // SECONDS
					];
				} else {
					var fm = [
							Math.floor(seconds / 60 / 60) % 24, // HOURS
							Math.floor(seconds / 60) % 60, // MINUTES
							seconds % 60 // SECONDS
					];				
				}				
				
				return $.map(fm, function(v, i) { return ((v < 10) ? '0' : '') + v; }).join(':');
			}
            
        </script>
        <?php print $ui->creamyFooter(); ?>
    </body>
</html>
