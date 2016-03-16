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
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
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
                        <small><?php $lh->translateText("general_settings"); ?></small>
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
								$currentVersion = $upd->getCurrentVersion();
							?>
                            <div class="box box-default">
                                <div class="box-header">
                                    <i class="fa fa-refresh"></i>
                                    <h3 class="box-title"><?php print $lh->translationFor("version")." ".number_format($currentVersion, 1); ?></h3>
                                </div>
                                <div class="box-body">
									<?php
										if ($upd->CRMIsUpToDate()) { // CRM is up to date.
											$lh->translateText("crm_up_to_date");
										} else { // check if we can update.
											if ($upd->canUpdateFromVersion($currentVersion)) { // update needed
												$contentText = $lh->translationFor("crm_update_available")." [ ".
															   $lh->translationFor("version")." ".CRM_INSTALL_VERSION." ]";
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
										}
									?>
                                </div>
                            </div>
                        </section>
                    </div>   <!-- /.row -->

                    <div class="row">
                        <section class="col-lg-12 connectedSortable">
                            <!-- quick email widget -->
                            <div class="box box-default">
                                <div class="box-header">
                                    <i class="fa fa-wrench"></i>
                                    <h3 class="box-title"><?php $lh->translateText("settings"); ?></h3>
                                </div>
                                <div class="box-body">
	                                <?php print $ui->getGeneralSettingsForm(); ?>
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
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
        <!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		<script type="text/javascript">
		$(document).ready(function() {
			/** 
			 * modifies a user.
		 	 */
			$("#adminsettings").validate({
				submitHandler: function(e) {
					//submit the form
					$("#<?php print CRM_UI_DEFAULT_RESULT_MESSAGE_TAG; ?>").html();
					$("#<?php print CRM_UI_DEFAULT_RESULT_MESSAGE_TAG; ?>").hide();
					var formData = new FormData(e);
	
					$.ajax({
					  url: "./php/ModifySettings.php",
					  data: formData,
					  processData: false,
					  contentType: false,
					  type: 'POST',
					  success: function(data) {
							if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
							<?php 
								print $ui->reloadLocationJS();
							?>
							} else {
							<?php 
							    $ko_text = $lh->translationFor("error_changing_settings");
								print $ui->fadingInMessageJS($ko_text, CRM_UI_DEFAULT_RESULT_MESSAGE_TAG); 
							?>
							}
					    }
					});
					
					return false; //don't let the form refresh the page...
				}					
			});
			 
		});
		</script>

    </body>
</html>
