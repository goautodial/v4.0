<?php
/**
 * @file        settingsmtp.php
 * @brief       Manage SMTP settings
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Alexander Jim Abenoja
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
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');
	require_once('./php/goCRMAPISettings.php');
	
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("smtp_settings"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php print $ui->standardizedThemeCSS(); ?>

        <?php print $ui->creamyThemeCSS(); ?>

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
                        <small><?php $lh->translateText("whatsapp_settings"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("whatsapp_settings"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <legend><?php $lh->translateText("whatsapp_settings"); ?></legend>
					<?php
					// API to get Whatsapp Settings
					$whatsapp = $api->API_getWhatsappSettings();
					?>
							<form id="modifyform">
								<fieldset>
									<div class="row">
										<div class="col-sm-6">
											<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("whatsapp_user"); ?>"></i> <?php $lh->translateText("whatsapp_user"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="GO_whatsapp_user" id="GO_whatsapp_user" placeholder="<?php $lh->translateText("whatsapp_user"); ?>" value="<?php echo $whatsapp->user;?>" required />
											</div>
										</div>
										<div class="col-sm-6">
											<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("whatsapp_token"); ?>"></i> <?php $lh->translateText("whatsapp_token"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="GO_whatsapp_token" id="GO_whatsapp_token" placeholder="<?php $lh->translateText("whatsapp_token"); ?>" value="<?php echo $whatsapp->token;?>" required />
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("whatsapp_instance"); ?>"></i> <?php $lh->translateText("whatsapp_instance"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="GO_whatsapp_instance" id="GO_whatsapp_instance" placeholder="<?php $lh->translateText("whatsapp_instance"); ?>" value="<?php echo $whatsapp->instance;?>" required />
											</div>
										</div>
										<div class="col-sm-6">
											<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("whatsapp_host"); ?>"></i> <?php $lh->translateText("whatsapp_host"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="GO_whatsapp_host" id="GO_whatsapp_host" placeholder="<?php $lh->translateText("whatsapp_host"); ?>" value="<?php echo $whatsapp->host;?>" required />
											</div>
										</div>
										<div class="col-sm-6">
											<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("callback_url"); ?>"></i> <?php $lh->translateText("callback_url"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="GO_whatsapp_callback_url" id="GO_whatsapp_callback_url" placeholder="<?php $lh->translateText("callback_url"); ?>" value="<?php echo $whatsapp->callback_url;?>" required />
											</div>
										</div>
									</div>
								</fieldset>
								
								<fieldset>
									<div class="col-sm-4 pull-right">
										 <a href="index.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText('cancel'); ?> </a>
										 <button type="submit" class="btn btn-primary" id="modifyOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText('update'); ?> </span></button>
									</div>
								</fieldset>
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
	
    <!-- Forms and actions -->
<?php print $ui->standardizedThemeJS(); ?>

		<script type="text/javascript">
			$(document).ready(function() {
			/*********************
			** INITIALIZATION
			*********************/
                <?php
                      if($output->result == 'success'){
	                      $url = "./php/ModifyWhatsappSetting.php";
                      }else{
        	              $url = "./php/AddWhatsappSetting.php";
                      }
                ?>
                var url = '<?php echo $url; ?>';
	
			$("#modifyform").validate({
				submitHandler: function() {
					//submit the form
						$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
						$('#modifyOkButton').prop("disabled", true);
						$.post(url, //post
						$("#modifyform").serialize(),
						function(data){
							//if message is sent
							console.log($("#modifyform").serialize());
							$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
							$('#modifyOkButton').prop("disabled", false);
							
							if (data == "success") {
								swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("smtp_modify_success"); ?>",type: "success"},function(){window.location.href = 'settingssmtp.php';});
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
							}
						});
					return false; //don't let the form refresh the page...
				}					
			});
				
					
			}); // end of document ready
		</script>
		
        <?php print $ui->creamyFooter();?>
		
    </body>
</html>
