<?php	
/**
 * @file        settingsadminlogs.php
 * @brief       View admin logs
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
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
*/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText('admin_logs'); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>

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
			cursor: pointer;
		}
		
		.nowrap_text {
			white-space: nowrap;
		}
		
		.new-width {
			width: 750px;
			margin-left: -375px;
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
		
        <?php print $ui->creamyFooter(); ?>
		
        <?php print $ui->standardizedThemeJS(); ?> 

		<script type="text/javascript">
			$(document).ready(function() {
                $('#adminlogs_table').dataTable({
					"aoColumnDefs": [{
						"sClass": "log-details hidden-xs hidden-sm truncate_td",
						"sWidth": "20%",
						"aTargets": [ 4 ]
					},{
						"sClass": "log-query hidden-xs hidden-sm truncate_td",
						"sWidth": "30%",
						"aTargets": [ 5 ]
					},{
						"sClass": "hidden-xs",
						"aTargets": [ 1 ]
					},{
						"sClass": "nowrap_text",
						"aTargets": [ 0, 2, 3 ]
					}],
					"aaSorting": [[ 2, "desc" ]],
					"fnDrawCallback": function() {
						$(".log-details").click(function() {
							var log_details = $(this).attr('title');
							if (log_details.length > 0) {
								swal({
									title: "Log Details",
									text: log_details,
									type: "info",
									html: true
								});
							}
						});
						
						$(".log-query").click(function() {
							var log_query = $(this).attr('title');
							if (log_query.length > 0) {
								swal({
									title: "Log Query",
									text: log_query,
									type: "info",
									html: true,
									customClass: "new-width"
								});
							}
						});
					}
				});
			});
		</script>
    </body>
</html>
