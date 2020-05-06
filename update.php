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
	
	require_once('./php/UIHandler.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
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

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->

		<!-- Javascript -->
		<script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
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
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("administration"); ?>
                        <small><?php $lh->translateText("crm_update"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-dashboard"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active"><?php $lh->translateText("administration"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
					<!-- Filas con acciones, formularios y demás -->
                    <div class="row">
                        <section class="col-lg-12">
                            <!-- version -->
                            <?php
								require_once('./php/Updater.php');
								$upd = \creamy\Updater::getInstance();
								$result = $upd->updateCRM($upd->getCurrentVersion());
							?>
                            <div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-refresh"></i>
                                    <h3 class="box-title"><?php $lh->translateText("results"); ?></h3>
                                </div>
                                <div class="box-body">
									<?php 
										// Show installation blog.
										print '<h3>'.$lh->translationFor("results").'</h3>';
										print '<pre>'.$upd->getUpdateLog().'</pre>'; 
												
										// success?								
										if ($result) {
											print '<p class="label label-success">'.$lh->translationFor("success").'</p>';
											// Talk about cronjobs
											print '<h3>'.$lh->translationFor("enable_creamy_events").'</h3>';
											print '<p>'.$lh->translationFor("creamy_events_description").'</p>';
											require_once('php/CRMUtils.php');
											# Creamy event job scheduling
											print '<pre>0 * * * *	php "'.\creamy\CRMUtils::creamyBaseDirectoryPath(true).'job-scheduler.php'.'" &>/dev/null</pre>';
											print '<p>'.$lh->translationFor("dont_know_cronjob").'</p><br>';
										} else {
											print '<p class="label label-danger">'.$lh->translationFor("error").'</p>';
										}

										// Accept button.
										$contentText = $result ? $lh->translationFor("crm_update_succeed") : $lh->translationFor("crm_update_failed");
										print $ui->formWithContent("go_back_form", 
											  $contentText, $lh->translationFor("accept"), CRM_UI_STYLE_DEFAULT,
											  CRM_UI_DEFAULT_RESULT_MESSAGE_TAG, "adminsettings.php");
									?>
                                </div>
                            </div>
                        </section>
                    </div>   <!-- /.row -->

				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
        <!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

    </body>
</html>
