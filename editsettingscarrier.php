<?php
/**
 * @file        editsettingscarrier.php
 * @brief       Manage Carriers
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

	require_once('php/UIHandler.php');
	require_once('php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

$cid = NULL;
if (isset($_POST["cid"])) {
	$cid = $_POST["cid"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("Carrier Edit"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>
        <?php print $ui->creamyThemeCSS(); ?>

    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
    </style>
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
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("carrier_edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["cid"])){
						?>	
							<li><a href="./settingscarriers.php"><?php $lh->translateText("Carrier"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>
                <!-- Main content -->
	            <section class="content">
					<div class="panel panel-default">
	                    <div class="panel-body">
						<!-- standard custom edition form -->

						<?php
						$errormessage = NULL;						
						
						if(isset($cid)) {
							$output = $api->API_getCarrierInfo($cid);
							$servers = $api->API_getAllServers();
							//echo "<pre>";
							//var_dump($output->data->carrier_description);
							if ($output->result=="success") {							
						?>

					<legend>MODIFY CARRIER ID : <u><?php echo $cid;?></u></legend>
					
						<form id="modifycarrier">
							<input type="hidden" name="modifyid" value="<?php echo $cid;?>">
							<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
							<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
						
					<!-- Custom Tabs -->
					<div role="tabpanel">
					<!--<div class="nav-tabs-custom">-->
						<ul role="tablist" class="nav nav-tabs nav-justified">
							<li class="active"><a href="#tab_1" data-toggle="tab"><?php $lh->translateText("basic_settings"); ?></a></li>
							<!--<li><a href="#tab_2" data-toggle="tab"> Advanced Settings</a></li>-->
						</ul>
					   <!-- Tab panes-->
					   <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
				                	<fieldset>
						<div class="form-group mt">
							<label for="carrier_name" class="col-sm-2 control-label"><?php $lh->translateText("carrier_name"); ?></label>
							<div class="col-sm-10 mb">
								<input type="text" class="form-control" name="carrier_name" id="carrier_name" placeholder="Carrier Name" value="<?php echo $output->data->carrier_name;?>" required />
							</div>
						</div>
						<div class="form-group">
							<label for="carrier_description" class="col-sm-2 control-label"><?php $lh->translateText("carrier_description"); ?></label>
							<div class="col-sm-10 mb">
								<input type="text" class="form-control" name="carrier_description" id="carrier_description" placeholder="<?php $lh->translateText("carrier_description") ?>" value="<?php echo $output->data->carrier_description;?>">
							</div>
						</div>
						<!--
						<div class="form-group">
							<label for="carrier_desc" class="col-sm-2 control-label">Authentication</label>
							<div class="col-sm-10 mb">
								<div class="row mt">
									<label class="col-sm-1">
										&nbsp;
									</label>
									<label class="col-sm-2 radio-inline c-radio" for="auth_ip">
										<input id="auth_ip" type="radio" name="authentication" value="auth_ip" checked>
										<span class="fa fa-circle"></span> IP Based
									</label>
									<label class="col-sm-2 radio-inline c-radio" for="auth_reg">
										<input id="auth_reg" type="radio" name="authentication" value="auth_reg">
										<span class="fa fa-circle"></span> Registration
									</label>
								</div>
							</div>
						</div>-->
						<div class="form-group mt">
							<label for="registration_string" class="col-sm-2 control-label"><?php $lh->translateText("registration_string"); ?></label>
							<div class="col-sm-10 mb">
								<input type="text" class="form-control" name="registration_string" id="registration_string" placeholder="Registration String" value="<?php echo $output->data->registration_string;?>">
							</div>
						</div>
						<!--
						<div class="form-group registration_div" style="display:none;">
							<label for="username" class="col-sm-2 control-label">Username</label>
							<div class="col-sm-10 mb">
								<input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php //echo $output->data->username;?>">
							</div>
						</div>
						<div class="form-group registration_div" style="display:none;">
							<label for="password" class="col-sm-2 control-label">Password</label>
							<div class="col-sm-10 mb">
								<input type="text" class="form-control" name="password" id="password" placeholder="Password" value="<?php //echo $output->data->password;?>">
							</div>
						</div>-->
						<div class="form-group">
							<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("account_entry"); ?></label>
							<div class="col-sm-10 mb">
								<div class="panel">
									<div class="panel-body">
										<textarea rows="11" class="form-control note-editor" id="account_entry" name="account_entry" cols="55" rows="10"><?php echo $output->data->account_entry;?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="server_ip" class="col-sm-2 control-label"><?php $lh->translateText("server_ip_host"); ?></label>
							<div class="col-sm-10 mb">
								<select name="server_ip" class="form-control">
								<?php
								$sever_ip = "";
									for($i=0;$i<count($servers->server_ip);$i++){
										if($servers->server_ip[$i] == $output->data->server_ip)
										$server_ip .= "<option value=".$servers->server_ip[$i]." selected>".$servers->server_ip[$i]." - ".$servers->server_description[$i]."</option>";
										else
										$server_ip .= "<option value=".$servers->server_ip[$i].">".$servers->server_ip[$i]." - ".$servers->server_description[$i]."</option>";
									}
								echo $server_ip;
								?>
								</select>
							</div>
						</div>
						<!--
						<div class="form-group registration_div" style="display:none;">
							<label for="carrier_desc" class="col-sm-2 control-label">Port</label>
							<div class="col-sm-10 mb">
								<input type="text" class="form-control" name="carrier_desc" id="carrier_desc" placeholder="Carrier Description" value="<?php //echo $output->data->carrier_description;?>">
							</div>
						</div>
						<div class="form-group">
							<label for="carrier_desc" class="col-sm-2 control-label">Codecs</label>
							<div class="col-sm-10 mb">
								<div class="row mt">
									<label class="col-sm-1">
										&nbsp;
									</label>
									<label class="col-sm-2 checkbox-inline c-checkbox" for="gsm">
										<input type="checkbox" id="gsm" name="codecs" value="GSM" checked>
										<span class="fa fa-check"></span> GSM
									</label>
									<label class="col-sm-2 checkbox-inline c-checkbox" for="ulaw">
										<input type="checkbox" id="ulaw" name="codecs" value="ULAW" checked>
										<span class="fa fa-check"></span> ULAW
									</label>
									<label class="col-sm-2 checkbox-inline c-checkbox" for="alaw">
										<input type="checkbox" id="alaw" name="codecs" value="ALAW">
										<span class="fa fa-check"></span> ALAW
									</label>
									<label class="col-sm-2 checkbox-inline c-checkbox" for="g729">
										<input type="checkbox" id="g729" name="codecs" value="G729">
										<span class="fa fa-check"></span> G729
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="carrier_desc" class="col-sm-2 control-label">DTMF Mode</label>
							<div class="col-sm-10 mb">
								<div class="row mt">
									<label class="col-sm-1">
										&nbsp;
									</label>
									<label class="col-sm-2 radio-inline c-radio" for="dtmf_1">
										<input id="dtmf_1" type="radio" name="dtmf" value="RFC2833" checked>
										<span class="fa fa-circle"></span> RFC2833   
									</label>
									<label class="col-sm-2 radio-inline c-radio" for="dtmf_2">
										<input id="dtmf_2" type="radio" name="dtmf" value="inband">
										<span class="fa fa-circle"></span> Inband   
									</label>
									<label class="col-sm-2 radio-inline c-radio" for="dtmf_3">
										<input id="dtmf_3" type="radio" name="dtmf" value="custom">
										<span class="fa fa-circle"></span> Custom      
									</label>
									<span id="input_custom_dtmf" class="col-sm-4 mb" style="display:none;">
										<input type="text" class="form-control" name="custom_dtmf" placeholder="Enter Custom DTMF" >
									</span>
								</div>
							</div>
						</div>-->
						<div class="form-group">
							<label for="protocol" class="col-sm-2 control-label"><?php $lh->translateText("protocol"); ?></label>
							<div class="col-sm-10 mb">
								<select class="form-control" name="protocol" id="protocol">
									<?php
									   $protocol = NULL;
								
										if($output->data->protocol == "SIP"){
											$protocol .= '<option value="SIP" selected> SIP </option>';
										}else{
											$protocol .= '<option value="SIP" > SIP </option>';
										}
										
										if($output->data->protocol == "IAX2"){
											$protocol .= '<option value="IAX2" selected> IAX2 </option>';
										}else{
											$protocol .= '<option value="IAX2" > IAX2 </option>';
										}
	
													if($output->data->protocol == "Zap"){
														$protocol .= '<option value="Zap" selected> Zap </option>';
													}else{
														$protocol .= '<option value="Zap" > Zap </option>';
													}
													
													if($output->data->protocol == "EXTERNAL"){
														$protocol .= '<option value="EXTERNAL" selected> EXTERNAL </option>';
													}else{
														$protocol .= '<option value="EXTERNAL" > EXTERNAL </option>';
													}
													echo $protocol;
												?>
											</select>
										</div>
									</div>
									<div class="form-group mt">
										<label for="globals_string" class="col-sm-2 control-label"><?php $lh->translateText("global_string"); ?></label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="globals_string" id="globals_string" maxlength="255" size="50" placeholder="Global String" value="<?php echo $output->data->globals_string;?>">
										</div>
									</div>
									<div class="form-group">
										<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("dialplan_entry"); ?></label>
										<div class="col-sm-10 mb">
											<div class="panel">
												<div class="panel-body">
													<textarea rows="3" class="form-control note-editor" cols="65" rows="10" id="dialplan_entry" name="dialplan_entry"><?php echo $output->data->dialplan_entry;?></textarea>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("active"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" name="active" id="active">
											<?php
												$active = NULL;
												if($output->data->active == "Y"){
													$active .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
												}else{
													$active .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
												}
												
												if($output->data->active == "N" || $output->data->active == NULL){
													$active .= '<option value="N" selected> '.$lh->translationFor("go_no").' </option>';
												}else{
													$active .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
												}
												echo $active;
											?>
											</select>
										</div>
									</div>
								</fieldset>
							</div><!-- tab 1 -->

								<!-- ADVANCED SETTINGS --><!--
							<div id="tab_2" class="tab-pane fade in">
								<fieldset>
									<div class="form-group mt">
										<label for="registration_string" class="col-sm-2 control-label">Registration String</label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="registration_string" id="registration_string" placeholder="Registration String" value="<?php //echo $output->data->registration_string;?>">
										</div>
									</div>
									<div class="form-group mt">
										<label for="globals_string" class="col-sm-2 control-label">Global String</label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="globals_string" id="globals_string" placeholder="Global String" value="<?php //echo $output->data->globals_string;?>">
										</div>
									</div>
									<div class="form-group">
										<label for="status" class="col-sm-2 control-label">Account Entry</label>
										<div class="col-sm-10 mb">
											<div class="panel">
												<div class="panel-body">
													<textarea rows="11" class="form-control note-editor" id="account_entry" name="account_entry"><?php //echo $output->data->account_entry;?></textarea>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="status" class="col-sm-2 control-label">Dial Plan Entry</label>
										<div class="col-sm-10 mb">
											<div class="panel">
												<div class="panel-body">
													<textarea rows="3" class="form-control note-editor" id="dialplan_entry" name="dialplan_entry"><?php //echo $output->data->dialplan_entry;?></textarea>
												</div>
											</div>
										</div>
									</div>
								</fieldset>
							</div><!-- tab 2 -->

							<!-- NOTIFICATIONS -->
							<div id="notifications">
								<div class="output-message-success" style="display:none;">
									<div class="alert alert-success alert-dismissible" role="alert">
									  <strong><?php $lh->translateText("success"); ?></strong> <?php $lh->translateText("carrier_id"); ?> <?php echo $cid?> modified !
									</div>
								</div>
								<div class="output-message-error" style="display:none;">
									<div class="alert alert-danger alert-dismissible" role="alert">
									  <span id="modifyCarrierResult"></span>
									</div>
								</div>
							</div>

							<!-- FOOTER BUTTONS -->
							<fieldset class="footer-buttons">
								<div class="box-footer">
								   <div class="col-sm-3 pull-right">
											<a href="settingscarriers.php" type="button" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?> </a>
									
											<button type="submit" class="btn btn-primary" id="modifyCarrierOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>
										
								   </div>
								</div>
							</fieldset>

							</div><!-- end of tab content -->
	                    	</div><!-- tab panel -->
	                    </form>
	                </div><!-- body -->
	            </div>
            </section>
					<?php
							}
						}	
                        
					?>
					
				<!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

  		<?php print $ui->standardizedThemeJS();?>
		
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {
				// initialize selec2
				$('.select').select2({ theme: 'bootstrap' });
				$.fn.select2.defaults.set( "theme", "bootstrap" );		
				
			    /* on authorization change */
				$('input[type=radio][name=authentication]').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "auth_reg") {
					  $('.registration_div').show();
					}
					if(this.value == "auth_ip") {
					  $('.registration_div').hide();
					}
				});

				 /* on custom dtmf select */
				$('input[type=radio][name=dtmf]').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "custom") {
						$('#input_custom_dtmf').show();
					}else{
						$('#input_custom_dtmf').hide();
					}
				});

			$("#modifycarrier").validate({
				submitHandler: function() {
					//submit the form
					$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#modifyCarrierOkButton').prop("disabled", true);
						$("#resultmessage").html();
						$("#resultmessage").fadeOut();
						$.post("./php/ModifyCarrier.php", //post
						$("#modifycarrier").serialize(), 
						function(data){
							console.log(data);
							//console.log($("#modifycarrier").serialize());
							//if message is sent
							$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
							$('#modifyCarrierOkButton').prop("disabled", false);
							
							if (data == 1) {
								sweetAlert("<?php $lh->translateText("carrier_modify_success"); ?>", "<?php $lh->translateText("carrier_updated"); ?>", "success");
								window.setTimeout(function(){location.reload()},2000);
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
							}
							//
						});
					return false; //don't let the form refresh the page...
				}					
			});				
		});
		</script>

		<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
		<?php print $ui->creamyFooter();?>
    </body>
</html>
