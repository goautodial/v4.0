<?php	
	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reports and GO Analytics</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<link href="css/datatables/1.10.12/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
		<!--<link href="css/datatables/buttons/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />-->
        <!-- Data Tables -->
        <script src="js/plugins/datatables/1.10.12/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
			<!-- FOR EXPORT -->
			<!--<script src="js/plugins/datatables/bpampuch/pdfmake/vfs_fonts.js" type="text/javascript"></script>
			<script src="js/plugins/datatables/bpampuch/pdfmake/pdfmake.min.js" type="text/javascript"></script>-->
			<script src="js/plugins/datatables/buttons/buttons.html5.min.js" type="text/javascript"></script>
			<script src="js/plugins/datatables/buttons/buttons.print.min.js" type="text/javascript"></script>
			<script src="js/plugins/datatables/buttons/buttons.flash.min.js" type="text/javascript"></script>
			<script src="js/plugins/datatables/buttons/dataTables.buttons.min.js" type="text/javascript"></script>
			<script src="js/plugins/datatables/jszip.min.js" type="text/javascript"></script>
        <!-- Datetime picker --> 
        <link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
        <!-- Date Picker -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <!-- SELECT2 CSS -->
        <link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
        <link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">

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
                $campaigns = $ui->API_getListAllCampaigns();
				$ingroups = $ui->API_getInGroups();
				$disposition = $ui->API_getAllDispositions();
            ?>
                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <legend><?php $lh->translateText("call_reports"); ?></legend>

                                    <div class="report-loader" style="color:lightgray; display:none;">
                                        <center>
                                            <h3>
                                                <i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
                                                Loading...
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
                        <div class="col-lg-3">
                            <h3 class="m0 pb-lg">Filters</h3>
                            <form id="search_form">

                                <!-- HIDDEN POSTS -->
                                <input type="hidden" name="userID" id="userID" value="<?php echo $user->getUserName();?>">

                                <div class="form-group">
                                    <label for="filter_type">Type</label>
                                    <select class="form-control select2" id="filter_type" style="width:100%;">
                                        <option value="stats">Statistical Report</option>
                                        <option value="agent_detail">Agent Time Detail</option>
                                        <option value="agent_pdetail">Agent Performance Detail</option>
                                        <option value="dispo">Dial Statuses Summary</option>
                                        <option value="sales_agent">Sales Per Agent</option>
                                        <option value="sales_tracker">Sales Tracker</option>
                                        <option value="inbound_report">Inbound Call Report</option>
                                        <!--<option value="call_export_report">Export Call Report</option>
                                        <option value="dashboard">Dashboard</option>
                                        <option value="cdr">Call History (CDRs)</option>-->
                                    </select>
                                </div>
                                <div class="form-group campaign_div">
                                    <label for="campaign_id">Campaign</label>
                                    <select class="form-control select2" name="campaign_id" id="campaign_id" style="width:100%;">
                                        <?php
                                            for($i=0; $i < count($campaigns->campaign_id);$i++){
                                        ?>
                                            <option value="<?php echo $campaigns->campaign_id[$i];?>"><?php echo $campaigns->campaign_id[$i]." - ".$campaigns->campaign_name[$i];?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
								<div class="form-group ingroup_div" style="display:none;">
                                    <label for="ingroup_id">Ingroups</label>
                                    <select class="form-control select2" name="ingroup_id" id="ingroup_id" style="width:100%;">
                                        <?php
                                            for($i=0; $i < count($ingroups->group_id);$i++){
                                        ?>
                                            <option value="<?php echo $ingroups->group_id[$i];?>"><?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
								<div class="form-group ingroup_div" style="display:none;">
                                    <label for="statuses">Statuses</label>
                                    <select class="form-control select2" name="statuses" id="statuses" style="width:100%;">
										<option value="">- - - ALL - - -</option>
											<?php
												for($a=0; $a<count($disposition->status); $a++){
											?>
													<option value="<?php echo $disposition->status[$a];?>"><?php echo $disposition->status[$a].' - '.$disposition->status_name[$a];?></option>
											<?php
												}
											?>
                                    </select>
                                </div>
                                <div class="form-group request_div" style="display:none;">
                                    <label>Request</label>
                                    <div class="stats_request">
                                        <select class="form-control select2" name="request1" id="request1" style="width:100%;">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                    <div class="sales_agent_request">
                                        <select class="form-control select2" name="request2" id="request2" style="width:100%;">
                                            <option value="outbound">Outbound</option>
                                            <option value="inbound">Inbound</option>
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
                                    <label>Start Date</label>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker1'>
                                            <input type='text' class="form-control" id="start_filterdate" name="start_filterdate" placeholder="<?php echo date("m/d/Y H:i:s ");?>"/>
                                            <span class="input-group-addon">
                                                <!-- <span class="glyphicon glyphicon-calendar"></span>-->
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.start date -->

                                <div class="form-group">
                                    <label>End Date</label>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker2'>
                                            <input type='text' class="form-control" id="end_filterdate" name="end_filterdate" placeholder="<?php echo date("m/d/Y H:i:s");?>" value="<?php echo date("m/d/Y H:i:s");?>"/>
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

        <!-- SELECT2-->
            <script src="theme_dashboard/select2/dist/js/select2.js"></script>
        <!-- FLOT CHART-->
            <script src="theme_dashboard/js/Flot/jquery.flot.js"></script>
            <script src="theme_dashboard/js/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
            <script src="theme_dashboard/js/Flot/jquery.flot.resize.js"></script>
            <script src="theme_dashboard/js/Flot/jquery.flot.pie.js"></script>
            <script src="theme_dashboard/js/Flot/jquery.flot.time.js"></script>
            <script src="theme_dashboard/js/Flot/jquery.flot.categories.js"></script>
            <script src="theme_dashboard/js/flot-spline/js/jquery.flot.spline.min.js"></script>
        <script>
			$(document).on('click','.edit-contact',function() {
				var url = './editcontacts.php';
				var id = $(this).attr('data-id');
				//alert(extenid);
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			});
			
            $(function () {
                //Initialize Select2 Elements
                $('.select2').select2({
                    theme: 'bootstrap'
                });

                $('#datetimepicker1').datetimepicker({
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down"
                    }
                });
                $('#datetimepicker2').datetimepicker({
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down"
                    },
                    useCurrent: false,
                });
                $("#datetimepicker1").on("dp.change", function (e) {
                    $('#datetimepicker2').data("DateTimePicker").minDate(e.date);

                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";
					var campaignID = $("#campaign_id").val();
					
                    if($("#filter_type").val() == "stats"){
                        request = $("#request1").val()
                    }
                    if($("#filter_type").val() == "sales_agent"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "sales_tracker"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "inbound_report"){
                        campaignID = $("#ingroup_id").val()
                    }
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
                                campaignID : campaignID,
                                request : request,
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "stats"){
                                        $('.request_div').show();
                                        $('.stats_request').show();
                                        $('.sales_agent_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_detail"){
										var title = "Agent Time Detail";
                                        $('#agent_detail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_detail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_pdetail"){
										var title = "Agent Performance Detail";
                                        $('#agent_pdetail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_mid').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_bottom').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "dispo"){
										var title = "Dial Statuses Summary";
                                        $('#dispo').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_agent"){
										var title = "Sales Per Agent";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
                                        $('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_tracker"){
										var title = "Sales Tracker";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
										$('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }
                                    if($("#filter_type").val() == "call_export_report"){
										var title = "Export Call Report";
                                        $('#call_export_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });
                $("#datetimepicker2").on("dp.change", function (e) {
                    $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);

                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";
					var campaignID = $("#campaign_id").val();
					
                    if($("#filter_type").val() == "stats"){
                        request = $("#request1").val()
                    }
                    if($("#filter_type").val() == "sales_agent"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "sales_tracker"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "inbound_report"){
                        campaignID = $("#ingroup_id").val()
                    }
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
                                campaignID : campaignID,
                                request : request,
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "stats"){
                                        $('.request_div').show();
                                        $('.stats_request').show();
                                        $('.sales_agent_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_detail"){
										var title = "Agent Time Detail";
                                        $('#agent_detail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_detail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_pdetail"){
										var title = "Agent Performance Detail";
                                        $('#agent_pdetail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_mid').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_bottom').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "dispo"){
										var title = "Dial Statuses Summary";
                                        $('#dispo').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_agent"){
										var title = "Sales Per Agent";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
                                        $('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_tracker"){
										var title = "Sales Tracker";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
										$('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }
                                    if($("#filter_type").val() == "call_export_report"){
										var title = "Export Call Report";
                                        $('#call_export_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });

                
                 /* changing reports */
                $('#filter_type').on('change', function() {
                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";
					var campaignID = $("#campaign_id").val();
					
                    if($("#filter_type").val() == "stats"){
                        request = $("#request1").val()
                    }
                    if($("#filter_type").val() == "sales_agent"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "sales_tracker"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "inbound_report"){
                        campaignID = $("#ingroup_id").val()
                    }
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
                                campaignID : campaignID,
                                request : request,
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "stats"){
                                        $('.request_div').show();
                                        $('.stats_request').show();
                                        $('.sales_agent_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_detail"){
										var title = "Agent Time Detail";
                                        $('#agent_detail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_detail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_pdetail"){
										var title = "Agent Performance Detail";
                                        $('#agent_pdetail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_mid').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_bottom').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "dispo"){
										var title = "Dial Statuses Summary";
                                        $('#dispo').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_agent"){
										var title = "Sales Per Agent";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
                                        $('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_tracker"){
										var title = "Sales Tracker";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
										$('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }
                                    if($("#filter_type").val() == "call_export_report"){
										var title = "Export Call Report";
                                        $('#call_export_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });
                
                 /* changing reports */
                $('#campaign_id').on('change', function() {
                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");

                    var request = "";

                    if($("#filter_type").val() == "stats"){
                        request = $("#request1").val()
                    }
                    if($("#filter_type").val() == "sales_agent"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "sales_tracker"){
                        request = $("#request2").val()
                    }
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
                                campaignID : $("#campaign_id").val(),
                                request : request,
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "stats"){
                                        $('.request_div').show();
                                        $('.stats_request').show();
                                        $('.sales_agent_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_detail"){
										var title = "Agent Time Detail";
                                        $('#agent_detail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_detail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_pdetail"){
										var title = "Agent Performance Detail";
                                        $('#agent_pdetail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_mid').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_bottom').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "dispo"){
										var title = "Dial Statuses Summary";
                                        $('#dispo').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_agent"){
										var title = "Sales Per Agent";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
                                        $('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_tracker"){
										var title = "Sales Tracker";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
										$('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }
                                    if($("#filter_type").val() == "call_export_report"){
										var title = "Export Call Report";
                                        $('#call_export_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });

                $('#request1').on('change', function() {
                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";

                    if($("#filter_type").val() == "stats"){
                        request = $("#request1").val()
                    }
                    if($("#filter_type").val() == "sales_agent"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "sales_tracker"){
                        request = $("#request2").val()
                    }
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
                                campaignID : $("#campaign_id").val(),
                                request : request,
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "stats"){
                                        $('.request_div').show();
                                        $('.stats_request').show();
                                        $('.sales_agent_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_detail"){
										var title = "Agent Time Detail";
                                        $('#agent_detail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_detail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_pdetail"){
										var title = "Agent Performance Detail";
                                        $('#agent_pdetail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_mid').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_bottom').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "dispo"){
										var title = "Dial Statuses Summary";
                                        $('#dispo').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_agent"){
										var title = "Sales Per Agent";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
                                        $('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_tracker"){
										var title = "Sales Tracker";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
										$('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }
                                    if($("#filter_type").val() == "call_export_report"){
										var title = "Export Call Report";
                                        $('#call_export_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });
                
                $('#request2').on('change', function() {
                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";

                    if($("#filter_type").val() == "stats"){
                        request = $("#request1").val()
                    }
                    if($("#filter_type").val() == "sales_agent"){
                        request = $("#request2").val()
                    }
					if($("#filter_type").val() == "sales_tracker"){
                        request = $("#request2").val()
                    }
					
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
                                campaignID : $("#campaign_id").val(),
                                request : request,
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "stats"){
                                        $('.request_div').show();
                                        $('.stats_request').show();
                                        $('.sales_agent_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_detail"){
										var title = "Agent Time Detail";
                                        $('#agent_detail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_detail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "agent_pdetail"){
										var title = "Agent Performance Detail";
                                        $('#agent_pdetail_top').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_mid').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_bottom').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#agent_pdetail_login').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "dispo"){
										var title = "Dial Statuses Summary";
                                        $('#dispo').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_agent"){
										var title = "Sales Per Agent";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
                                        $('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "sales_tracker"){
										var title = "Sales Tracker";
										if ($("#request2").val() == "outbound") {
                                            title = title + " - Outbound";
                                        }else{
											title = title + " - Inbound";
										}
                                        $('#outbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('#inbound_table').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').show();
										$('.sales_agent_request').show();
                                        $('.stats_request').hide();
										$('.campaign_div').show();
										$('.ingroup_div').hide();
                                    }
                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }
                                    if($("#filter_type").val() == "call_export_report"){
										var title = "Export Call Report";
                                        $('#call_export_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });
				
				$('#ingroup_id').on('change', function() {
                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";
					
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
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

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });
				
				$('#statuses').on('change', function() {
                    $('#table').empty();
                    $(".report-loader").fadeIn("slow");
                    
                    var request = "";
					
                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : $("#filter_type").val(),
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

                                if(data != ""){
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html(data);

                                    if($("#filter_type").val() == "inbound_report"){
										var title = "Inbound Call Report";
                                        $('#inbound_report').dataTable({ dom: 'Bfrtip',  buttons: [ {extend: 'copy', title: title}, {extend: 'csv', title: title}, {extend: 'excel', title: title}, {extend: 'print', title: title} ] } );
                                        $('.request_div').hide();
										$('.campaign_div').hide();
										$('.ingroup_div').show();
                                    }

                                }else{
                                    $(".report-loader").fadeOut("slow");
                                    $('#table').html("NO DATA");
                                }
                            }
                        });
                });
                /*
                 * Inbound and Outbound Calls Per Hour Data
                */
                    (function(window, document, $, undefined){
                        $(function(){
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
                              if(chartv3.length)
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