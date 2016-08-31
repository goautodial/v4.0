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
        <title>Call Reports</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>

            <!-- Daterang picker CSS --
            <link rel="stylesheet" type="text/css" media="all" href="theme_dashboard/bootstrap-daterangepicker/daterangepicker.css" />
            <!-- Daterange Picker JS --
            <script type="text/javascript" src="theme_dashboard/bootstrap-daterangepicker/moment.js"></script>
            <script type="text/javascript" src="theme_dashboard/bootstrap-daterangepicker/daterangepicker.js"></script>
        
        <!-- Datetime picker --> 
        <link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
        <!-- Date Picker -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <!-- SELECT2 CSS -->
        <link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
        <link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">

        <script type="text/javascript">
            $(window).ready(function() {
                $(".preloader").fadeOut("slow");
            })
        </script>
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
            ?>
            
                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <legend><?php $lh->translateText("call_reports"); ?></legend>

                                    <div class="box-body table" id="table">
                                        </table>
                                    </div><!-- /.box-body -->

                                </div><!-- /.panel-body -->
                            </div><!--/.panel-->
                        </div>
                        <div class="col-lg-3">
                            <h3 class="m0 pb-lg">Filters</h3>
                            <form id="search_form">
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
                                        <option value="call_export_report">Export Call Report</option>
                                        <option value="dashboard">Dashboard</option>
                                        <option value="cdr">Call History (CDRs)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="campaign_id">Campaign</label>
                                    <select class="form-control select2" id="campaign_id" style="width:100%;">
                                        <?php
                                            for($i=0; $i < count($campaigns->campaign_id);$i++){
                                        ?>
                                            <option><?php echo $campaigns->campaign_name[$i];?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
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
                                    <label>Start Date:</label>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker1'>
                                            <input type='text' class="form-control" id="start_filterdate" placeholder="<?php echo date("m/d/Y H:i:s ");?>"/>
                                            <span class="input-group-addon">
                                                <!-- <span class="glyphicon glyphicon-calendar"></span>-->
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.start date -->

                                <div class="form-group">
                                    <label>End Date:</label>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker2'>
                                            <input type='text' class="form-control" id="end_filterdate" placeholder="<?php echo date("m/d/Y H:i:s");?>" value="<?php echo date("m/d/Y H:i:s");?>"/>
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
        </div><!-- ./wrapper -->
        
        <?php print $ui->standardizedThemeJS();?>

        <!-- SELECT2-->
        <script src="theme_dashboard/select2/dist/js/select2.js"></script>

        <script>
            $(function () {
                //Initialize Select2 Elements
                $('.select2').select2({
                    theme: 'bootstrap'
                });

                $('#datetimepicker1').datetimepicker({
                    format: 'DD/MM/YYYY'
                });
                $('#datetimepicker2').datetimepicker({
                    useCurrent: false, //Important! See issue #1075
                    format: 'DD/MM/YYYY'
                });
                $("#datetimepicker1").on("dp.change", function (e) {
                    $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
                });
                $("#datetimepicker1").on("dp.change", function (e) {
                    $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
                });

                 /* changing reports */
                $('#filter_type').on('change', function() {

                        $.ajax({
                            url: "reports.php",
                            type: 'POST',
                            data: {
                                pageTitle : this.value,
                                campaignID : $("#campaign_id").val(),
                                request : $("#request").val(),
                                userID : $("#userID").val(),
                                userGroup : $("#userGroup").val(),
                                fromDate : $("#start_filterdate").val(),
                                toDate : $("#end_filterdate").val()
                            },
                            success: function(data) {
                                console.log(data);

                                if(data != ""){
                                    $('#table').html(data);
                                }else{
                                    $('#table').html("NO DATA");
                                }
                            }
                        });

                });

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
    </body>
</html>