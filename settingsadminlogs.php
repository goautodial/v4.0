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
        <title><?php $lh->translateText('portal_title'); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php print $ui->standardizedThemeCSS(); ?>

        <?php print $ui->creamyThemeCSS(); ?>

		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(window).ready(function() {
                $(".preloader").fadeOut("slow");
            });
        </script>
		
		<style>
		.truncate_td {
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			max-width:1px;
		}
		</style>
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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("admin_logs"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("admin_logs"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="admin_logs">
                            <legend><?php $lh->translateText("admin_logs"); ?></legend>
							<?php print $ui->getAdminLogsList($_SESSION['usergroup']); ?>
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
		
        <?php print $ui->standardizedThemeJS(); ?> 

		<script type="text/javascript">
			$(document).ready(function() {
                $('#adminlogs_table').dataTable({
					"aoColumnDefs": [{
						"sClass": "hidden-xs hidden-sm truncate_td",
						"aTargets": [ 4, 5 ]
					},{
						"sClass": "hidden-xs truncate_td",
						"aTargets": [ 1 ]
					}],
					"aaSorting": [[ 2, "desc" ]]
				});
			});
		</script>
    </body>
</html>
