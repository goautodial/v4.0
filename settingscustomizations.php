<?php
/**
 * @file        settingsmtp.php
 * @brief       Manage SMTP settings
 * @copyright   Copyright (c) 2019 GOautodial Inc.
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
                        <small><?php $lh->translateText("customization_settings"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("customization_settings"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <legend><?php $lh->translateText("customization_settings"); ?></legend>
					<?php
					// API to get Customizations
						
					?>
							<form id="modifyform">
								<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
								<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
								<fieldset>
									<div class="form-group mt">
										<div class="row">
											<div class="col-sm-6">
												<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("smtp_host_info"); ?>"></i> <?php $lh->translateText("host"); ?></label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="host" id="host" placeholder="<?php $lh->translateText("host"); ?>" value="<?php echo $output->data->host;?>" required />
												</div>
											</div>
											<div class="col-sm-6">
												<label for="port" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("smtp_port_info"); ?>"></i> <?php $lh->translateText("port"); ?></label>
												<div class="col-sm-10 mb">
													<input type="number" class="form-control" name="port" id="port" placeholder="<?php $lh->translateText("port"); ?>" value="<?php echo $output->data->port;?>" min="22" maxlength="4" required />
												</div>
											</div>
										</div>
									</div>
									<div class="form-group mt">
										<div class="row">
											<div class="col-sm-6">
												<label for="username" class="col-sm-2 control-label"><?php $lh->translateText("username"); ?></label>
												<div class="col-sm-10 mb">
													<input type="email" class="form-control" name="username" id="username" placeholder="<?php $lh->translateText("username"); ?>" value="<?php echo $output->data->username;?>" required />
												</div>
											</div>
											<div class="col-sm-6">
												<label for="password" class="col-sm-2 control-label"><?php $lh->translateText("password"); ?></label>
												<div class="col-sm-10 mb">
													<input type="password" class="form-control" name="password" id="password" placeholder="<?php $lh->translateText("password"); ?>" value="" />
												</div>
											</div>
										</div>
									</div>
									<div class="form-group mt">
										<div class="col-sm-6 mb">
											<div class="col-md-4">
												<label for="ipv6_support"><i class="fa fa-info-circle" title="<?php $lh->translateText("ipv6_support_info"); ?>"></i> <?php $lh->translateText("ipv6_support"); ?></label>
											</div>
											<div class="col-md-6">
												<label class="checkbox-inline c-checkbox" for="ipv6_support">
													<input id="ipv6_support" name="ipv6_support" type="checkbox" <?php if($output->data->ipv6_support == 1)echo "checked";?> >
													<span class="fa fa-check"></span>
												</label>
											</div>
										</div>
									</div>
								</fieldset>
								<fieldset>
									<div class="form-group mt">
										<label for="host" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("debug_info"); ?>"></i> <?php $lh->translateText("debug"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" name="debug" id="debug">
												<option value="0">OFF - Production use</option>
												<option value="1">Client Messages</option>
												<option value="2">Client and Server Messages</option>
												<option value="3">Timeout + Client and Server Messages</option>
											</select>
										</div>
									</div>
									<div class="form-group mt">
										<label for="smtp_security" class="col-sm-2 control-label"><i class="fa fa-info-circle" title="<?php $lh->translateText("smtp_security_info"); ?>"></i> <?php $lh->translateText("smtp_security"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" name="smtp_security" id="smtp_security">
												<option value="tls">TLS</option>
												<option value="ssl">SSL</option>
											</select>
										</div>
									</div>
									<div class="form-group mt">
										<div class="col-sm-6 mb">
											<div class="col-md-4">
												<label for="smtp_auth"><i class="fa fa-info-circle" title="<?php $lh->translateText("smtp_auth_info"); ?>"></i> <?php $lh->translateText("smtp_auth"); ?></label>
											</div>
											<div class="col-md-6">
												<label class="checkbox-inline c-checkbox" for="smtp_auth">
													<input id="smtp_auth" name="smtp_auth" type="checkbox" <?php if($output->data->smtp_auth == 1)echo "checked";?> >
													<span class="fa fa-check"></span>
												</label>
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
						<?php //} ?>
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
	                      $url = "./php/ModifySMTPSetting.php";
                      }else{
        	              $url = "./php/AddSMTPSetting.php";
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
								console.log($("#modifyvoicemail").serialize());
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
		
				/*********************
				** FILTERS
				*********************/  
		
				// disable special characters on Usergroup ID   
					$('#host').bind('keypress', function (event) {
						var regex = new RegExp("^[A-Za-z0-9.]+$");
						var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						if (!regex.test(key)) {
						   event.preventDefault();
						   return false;
						}
					});

				/********************
				** DROPDOWN VALUES **
				********************/
					$('#debug option[value="<?php echo $output->data->debug; ?>"]').attr('selected', 'selected');
					$('#smtp_security option[value="<?php echo $output->data->smtp_security; ?>"]').attr('selected', 'selected');
				
					
			}); // end of document ready
		</script>
		
        <?php print $ui->creamyFooter();?>
		
    </body>
</html>
