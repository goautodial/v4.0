
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

	//var_dump($user->getUserRole());
	if(!preg_match("/^(ADMIN)$/", $_SESSION['usergroup']) && $_SESSION['userrole'] > CRM_DEFAULTS_USER_ROLE_SUPERVISOR) {
		header("location: index.php");
	}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("general_settings"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php print $ui->standardizedThemeCSS(); ?>
		
        <?php print $ui->creamyThemeCSS(); ?>
		
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
                <?php if ($user->userHasManagerPermission()) { ?>
					<!-- Filas con acciones, formularios y demás -->
					
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
        <!-- Modal Dialogs -->
        <?php print $ui->standardizedThemeJS(); ?>
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
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
