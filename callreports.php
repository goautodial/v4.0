
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
        <title>Goautodial</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
        <!-- Theme style -->
        <link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">

        <!-- Select2 -->
        <link rel="stylesheet" href="adminlte/select2/select2.min.css">
        <!-- DATETIMEPICKER-->
        <link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
            
            <!-- =============== BOOTSTRAP STYLES ===============-->
            <link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
                <!-- =============== APP STYLES ===============-->
            <link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

        <!-- Creamy App --
        <script src="js/app.min.js" type="text/javascript"></script>
        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

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
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <div class="pull-left">
                                        <select class="form-control select2">
                                            <option>Statistical Report</option>
                                            <option>Agent Time Detail</option>
                                            <option>Agent Performance Detail</option>
                                            <option>Dial Statuses Summary</option>
                                            <option>Sales Per Agent</option>
                                            <option>Sales Tracker</option>
                                            <option>Inbound Call Report</option>
                                            <option>Export Call Report</option>
                                            <option>Dashboard</option>
                                            <option>Call History (CDRs)</option>
                                        </select>
                                        <select class="form-control select2">
                                            <?php
                                                for($i=0; $i < count($campaigns->campaign_id);$i++){
                                            ?>
                                                <option><?php echo $campaigns->campaign_name[$i];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="pull-right">
                                        <!--<button class="btn datetimepicker1"><i class="fa fa-calendar"></i></button>-->
                                        <div id="datetimepicker1" class="input-group date">
                                             <input type="text" class="form-control">
                                             <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                             </span>
                                        </div>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="">

                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
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
        <!-- Select2 -->
        <script src="adminlte/select2/select2.full.min.js"></script>
        <!-- DATETIMEPICKER-->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <script>
            $(function () {
                //Initialize Select2 Elements
                $(".select2").select2();

                // DATETIMEPICKER
                $('#datetimepicker1').datetimepicker({
                  icons: {
                      time: 'fa fa-clock-o',
                      date: 'fa fa-calendar',
                      up: 'fa fa-chevron-up',
                      down: 'fa fa-chevron-down',
                      previous: 'fa fa-chevron-left',
                      next: 'fa fa-chevron-right',
                      today: 'fa fa-crosshairs',
                      clear: 'fa fa-trash'
                    }
                });

                // WYSIWYG
                // ----------------------------------- 

                $('.wysiwyg').wysiwyg();
            });
        </script>
        <!-- =============== APP SCRIPTS ===============-->
        <script src="theme_dashboard/js/app.js"></script>
    </body>
</html>