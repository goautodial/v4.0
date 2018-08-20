<?php	
/**
 * @file 		crm.php
 * @brief 		Manage leads and contacts
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
        <link rel="stylesheet" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
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
										if ($perm->reportsanalytics_display == 'Y') {
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
										}
									?>
                                        <!--<option value="dashboard">Dashboard</option>
                                        <option value="cdr">Call History (CDRs)</option>-->
                                    </select>
                                </div>
                                <div class="form-group campaign_div">
                                    <label for="campaign_id"><?php $lh->translateText("campaign"); ?></label>
                                    <select class="form-control select2" name="campaign_id" id="campaign_id" style="width:100%;">
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
											?>
													<option value="<?php echo $disposition->status[$a];?>"><?php echo $disposition->status[$a].' - '.$disposition->status_name[$a];?></option>
											<?php
												}
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
                                            <input type='text' class="form-control" id="end_filterdate" name="end_filterdate" placeholder="<?php echo date("m/d/Y H:i:s");?>" value="<?php echo date("m/d/Y H:i:s");?>" />
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
				var filter_type = $('#filter_type').val();
				console.log(e.date);
				
				if (filter_type != "call_export_report") {				
					var request = "";
					var campaignID = $("#campaign_id").val();
					var URL = 'reports.php';
					
					$('#table').empty();
					$(".report-loader").fadeIn("slow");					
					
					if (filter_type == "stats") {
						request = $("#request1").val();
						URL = './php/reports/statisticalreports.php';
					}
					
					if (filter_type == "sales_agent") {
						request = $("#request2").val();
					}
					
					if (filter_type == "sales_tracker") {
						request = $("#request2").val();
					}
					
					if (filter_type == "inbound_report") {
						campaignID = $("#ingroup_id").val();
					}
					
					$.ajax({
							url: URL,
							type: 'POST',
							data: {
								pageTitle : filter_type,
								campaignID : campaignID,
								request : request,
								userID : $("#userID").val(),
								userGroup : $("#userGroup").val(),
								fromDate : $("#start_filterdate").val(),
								toDate : $("#end_filterdate").val()
							},
							success: function(data) {
								//console.log(data);
								if (data !== "") {
									$(".report-loader").fadeOut("slow");
									$('#table').html(data);
									
									if (filter_type == "agent_detail") {
										var title = "<?php $lh->translateText("agent_detail"); ?>";
										
										$('#agent_detail_top').DataTable({
											destroy: true,
											responsive: true,
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
									}
									
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
										$('.campaign_div').hide();
										$('.ingroup_div').show();
									}
								} else {
									$(".report-loader").fadeOut("slow");
									$('#table').html("<?php $lh->translateText("no_data"); ?>");
								}
							}
						});
				}
			});
			
			$("#datetimepicker2").on("dp.change", function (e) {
				$('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
				console.log(e.date);
				
				if (filter_type != "call_export_report") {									
					var request = "";
					var campaignID = $("#campaign_id").val();
					var URL = 'reports.php';
					
					$('#table').empty();
					$(".report-loader").fadeIn("slow");
					
					if (filter_type == "stats") {
						request = $("#request1").val();
						URL = './php/reports/statisticalreports.php';
					}
					
					if (filter_type == "sales_agent") {
						request = $("#request2").val();
					}
					
					if (filter_type == "sales_tracker") {
						request = $("#request2").val();
					}
					
					if (filter_type == "inbound_report") {
						campaignID = $("#ingroup_id").val();
					}
					
					$.ajax({
							url: URL,
							type: 'POST',
							data: {
								pageTitle : filter_type,
								campaignID : campaignID,
								request : request,
								userID : $("#userID").val(),
								userGroup : $("#userGroup").val(),
								fromDate : $("#start_filterdate").val(),
								toDate : $("#end_filterdate").val(),
							},
							success: function(data) {
								//console.log(data);
								if (data != "") {
									$(".report-loader").fadeOut("slow");
									$('#table').html(data);

									if (filter_type == "agent_detail") {
										var title = "<?php $lh->translateText("agent_detail"); ?>";
										$('#agent_detail_top').DataTable(
										{
											destroy: true,
											responsive: true,											
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
										}
										//{ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } 
									);
										//$('#agent_detail_top').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
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
									}
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
									}

								} else {
									$(".report-loader").fadeOut("slow");
									$('#table').html("<?php $lh->translateText("no_data"); ?>");
								}
							}
						});
				}
			});

			
			/* changing reports */
			$('#filter_type').on('change', function() {
				var filter_type = $(this).val();
				var request = "";
				var campaignID = $("#campaign_id").val();					
				var URL = 'reports.php';
				
				$('#filter_type').val(filter_type);
				$('#table').empty();
				$(".report-loader").fadeIn("slow");
				console.log($('#filter_type').val());
				
				if (filter_type == "stats") {
					request = $("#request1").val();
					URL = './php/reports/statisticalreports.php';
					$('.request_div').show();
					$('.stats_request').show();
					$('.sales_agent_request').hide();
					$('.campaign_div').show();
					$('.ingroup_div').hide();
				}
				
				if (filter_type == "agent_detail") {
					$('.request_div').hide();
					$('.campaign_div').show();
					$('.ingroup_div').hide();
				}
				
				if (filter_type == "agent_pdetail") {
					$('.request_div').hide();
					$('.campaign_div').show();
					$('.ingroup_div').hide();
				}
				
				if (filter_type == "dispo") {
					$('.request_div').hide();
					$('.campaign_div').show();
					$('.ingroup_div').hide();
				}
				
				if (filter_type == "sales_agent") {
					request = $("#request2").val();
					$('.request_div').show();
					$('.sales_agent_request').show();
					$('.stats_request').hide();
					$('.campaign_div').show();
					$('.ingroup_div').hide();
				}
				
				if (filter_type == "sales_tracker") {
					request = $("#request2").val();
					$('.request_div').show();
					$('.sales_agent_request').show();
					$('.stats_request').hide();
					$('.campaign_div').show();
					$('.ingroup_div').hide();
				}
				
				if (filter_type == "inbound_report") {
					campaignID = $("#ingroup_id").val();
					$('.request_div').hide();
					$('.campaign_div').hide();
					$('.ingroup_div').show();
				}
				
				if (filter_type == "call_export_report") {
					$('.campaign_div').hide();
					$('.ingroup_div').hide();                                        
					$('.request_div').hide();
				}
				
				$.ajax({
						url: URL,
						type: 'POST',
						data: {
							pageTitle : filter_type,
							campaignID : campaignID,
							request : request,
							fromDate : $("#start_filterdate").val(),
							toDate : $("#end_filterdate").val()
						},
						success: function(data) {
							//console.log(data);
							if (data !== "") {
								$(".report-loader").fadeOut("slow");
								$('#table').html(data);

								if (filter_type == "agent_detail") {
									var title = "<?php $lh->translateText("agent_detail"); ?>";
									
									$('#agent_detail_top').DataTable({
										destroy: true,
										responsive: true,										
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
									//$('#agent_detail_top').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
									//$('#agent_detail_login').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
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
									//$('#agent_pdetail_mid').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
									//$('#agent_pdetail_bottom').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
									//$('#agent_pdetail_login').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
								}
								
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
								}
							} else {
								$(".report-loader").fadeOut("slow");
								$('#table').html("<?php $lh->translateText("no_data"); ?>");
							}
						}
					});
			});
			
			/* changing reports */
			$('#campaign_id').on('change', function() {
				var filter_type = $('#filter_type').val();
				var request = "";
				var URL = 'reports.php';
				
				$('#table').empty();
				$(".report-loader").fadeIn("slow");				

				if (filter_type == "stats") {
					request = $("#request1").val();
					URL = './php/reports/statisticalreports.php';
				}
				
				if (filter_type == "sales_agent") {
					request = $("#request2").val();
				}
				
				if (filter_type == "sales_tracker") {
					request = $("#request2").val();
				}
				
				$.ajax({
						url: URL,
						type: 'POST',
						data: {
							pageTitle : filter_type,
							campaignID : $("#campaign_id").val(),
							request : request,
							userID : $("#userID").val(),
							userGroup : $("#userGroup").val(),
							fromDate : $("#start_filterdate").val(),
							toDate : $("#end_filterdate").val()
						},
						success: function(data) {
							//console.log(data);
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
									//$('#agent_detail_login').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
									
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
										dom: 'Bfrtip',  
										buttons: [ 
											{ extend: 'copy', title: title }, 
											{ extend: 'csv', title: title }, 
											{ extend: 'excel', title: title }, 
											{ extend: 'print', title: title } 
										] 
									});
									
									$('#agent_pdetail_login').DataTable({
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
			});

			$('#request1').on('change', function() {
				var filter_type = $('#filter_type').val();				
				var request = "";
				var URL = 'reports.php';
				
				$('#table').empty();
				$(".report-loader").fadeIn("slow");				
				
				if (filter_type == "stats") {
					request = $("#request1").val();
					URL = './php/reports/statisticalreports.php';
				}
				
				if (filter_type == "sales_agent") {
					request = $("#request2").val();
				}
				
				if (filter_type == "sales_tracker") {
					request = $("#request2").val();
				}
				
				$.ajax({
						url: URL,
						type: 'POST',
						data: {
							pageTitle : filter_type,
							campaignID : $("#campaign_id").val(),
							request : request,
							userID : $("#userID").val(),
							userGroup : $("#userGroup").val(),
							fromDate : $("#start_filterdate").val(),
							toDate : $("#end_filterdate").val()
						},
						success: function(data) {
							//console.log(data);
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
									//$('#agent_detail_top').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
									//$('#agent_detail_login').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
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
										dom: 'Bfrtip',  
										buttons: [ 
											{ extend: 'copy', title: title }, 
											{ extend: 'csv', title: title }, 
											{ extend: 'excel', title: title }, 
											{ extend: 'print', title: title } 
										] 
									});
									
									$('#agent_pdetail_login').DataTable({
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
			});
			
			$('#request2').on('change', function() {
				var filter_type = $('#filter_type').val();				
				var request = "";
				var URL = 'reports.php';
				
				$('#table').empty();
				$(".report-loader").fadeIn("slow");
				
				if (filter_type == "stats") {
					request = $("#request1").val();
					URL = './php/reports/statisticalreports.php';
				}
				
				if (filter_type == "sales_agent") {
					request = $("#request2").val();
				}
				
				if (filter_type == "sales_tracker") {
					request = $("#request2").val();
				}
				
				$.ajax({
						url: URL,
						type: 'POST',
						//data: $('#search_form').serialize() + '&pageTitle=' + filter_type,
						data: {
							pageTitle : filter_type,
							campaignID : $("#campaign_id").val(),
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
									$('#agent_detail_top').DataTable(
										{
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
										}
										//{ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } 
									);
									//$('#agent_detail_top').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
									//$('#agent_detail_login').DataTable({ destroy: true, responsive: true, dom: 'Bfrtip',  buttons: [ { extend: 'copy', title: title }, { extend: 'csv', title: title }, { extend: 'excel', title: title }, { extend: 'print', title: title } ] });
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
										dom: 'Bfrtip',  
										buttons: [ 
											{ extend: 'copy', title: title }, 
											{ extend: 'csv', title: title }, 
											{ extend: 'excel', title: title }, 
											{ extend: 'print', title: title } 
										] 
									});
									$('#agent_pdetail_login').DataTable({
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
									$('.campaign_div').hide(); 										$('.ingroup_div').hide();                                        
									$('.request_div').hide();
								}

							} else {
								$(".report-loader").fadeOut("slow");
								$('#table').html("<?php $lh->translateText("no_data"); ?>");
							}
						}
					});
			});
			
			$('#ingroup_id').on('change', function() {
				var filter_type = $('#filter_type').val();				
				var request = "";
				
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
							//console.log(data);
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
        </script>

        <?php print $ui->creamyFooter(); ?>
    </body>
</html>
