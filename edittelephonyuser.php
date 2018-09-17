<?php
/**
 * @file 		edittelephonyusers.php
 * @brief 		Modify user accounts
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com> 
 * @author		Demian Lizandro A. Biscocho <demian@goautodial.com>
 * @author     	Noel Umandap <noel@goautodial.com>
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
**/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

	$userid = NULL;
	if (isset($_POST["user_id"])) {
		$userid = $_POST["user_id"];
	}
	$current_user = NULL;
	if (isset($_POST["user"])) {
		$current_user = $_POST["user"];
	}
	if(isset($_POST["role"])){
		$userrole = $_POST["role"];
	}

	$output = $api->API_getUserInfo($current_user, "userInfo");
	$voicemails = $api->API_getAllVoiceMails();
	$user_groups = $api->API_getAllUserGroups();
	$perm = $api->goGetPermissions('user');
	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("edit_user"); ?></title>
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
            	<div>
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("Users"); ?>
                        <small><?php $lh->translateText("Edit Users"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if(isset($_POST["userid"])){
						?>	
							<li><a href="./telephonyusers.php"><?php $lh->translateText("users"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>
                <?php
					if ($perm->user_read !== 'R') {
						echo "<br/><br/>";
						print $ui->getUnauthotizedAccessMessage();
					} else {
                ?>
               <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
					<!-- standard custom edition form -->
						<?php
							$userobj = NULL;
							$errormessage = NULL;	
							if(isset($userid)) {
								if ($output->result=="success") {
								# Result was OK!

						?>
							<div class="panel-body">
							<legend><?php $lh->translateText("modify_user"); ?> : <u id="agent_name"><?php echo $output->data->user; ?></u></legend>
								<form id="modifyuser">
									<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
									<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
								<!-- Custom Tabs -->
								<div role="tabpanel">
								<!--<div class="nav-tabs-custom">-->
									<ul role="tablist" class="nav nav-tabs nav-justified">
										<li class="active"><a href="#tab_1" data-toggle="tab"><?php $lh->translateText("basic_settings"); ?></a></li>
										<li><a href="#tab_2" data-toggle="tab"><?php $lh->translateText("advance_settings"); ?> </a></li>
									</ul>
					               <!-- Tab panes-->
					               <div class="tab-content">
						               	<!-- BASIC SETTINGS -->
						                <div id="tab_1" class="tab-pane fade in active">
										<input type="hidden" name="modifyid" value="<?php echo $userid;?>" />
											<?php
												//echo "<pre>";
												//var_dump($output);											
											?>
											<fieldset>
												<div class="form-group mt">
													<label for="fullname" class="col-sm-2 control-label"><?php $lh->translateText("full_name"); ?></label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="fullname" id="fullname" 
															value="<?php echo $output->data->full_name;?>" maxlength="50" placeholder="<?php $lh->translateText("full_name"); ?>" />
													</div>
												</div>
												<div class="form-group">
													<label for="email" class="col-sm-2 control-label"><?php $lh->translateText("email"); ?></label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="email" id="email" 
															value="<?php echo $output->data->email;?>"  maxlength="100" placeholder="<?php $lh->translateText("email"); ?>" />
														<small><span id="email_check"></span></small>
													</div>
												</div>
												<div class="form-group">
													<label for="usergroup" class="col-sm-2 control-label"><?php $lh->translateText("user_group"); ?></label>
													<div class="col-sm-10 mb">
														<select class="form-control select2-1" id="usergroup" name="usergroup">
															<?php
																for($a=0;$a<count($user_groups->user_group);$a++){
															?>
																<option value="<?php echo $user_groups->user_group[$a];?>" 
																		<?php if($output->data->user_group == $user_groups->user_group[$a]){echo "selected";}?> />  
																	<?php echo $user_groups->user_group[$a].' - '.$user_groups->group_name[$a];?>  
																</option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("status"); ?></label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="status" id="status">
														<?php
															$status = NULL;
															if($output->data->active == "Y"){
																$status .= '<option value="Y" selected> Active </option>';
															}else{
																$status .= '<option value="Y" > Active </option>';
															}
															
															if($output->data->active == "N" || $output->data->active == NULL){
																$status .= '<option value="N" selected> Inactive </option>';
															}else{
																$status .= '<option value="N" > Inactive </option>';
															}
															echo $status;
														?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="userlevel" class="col-sm-2 control-label"><?php $lh->translateText("user_level"); ?></label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="userlevel" id="userlevel">
														<?php
															$userlevel = NULL;
																if($output->data->user_level == "1"){
																	$userlevel .= '<option value="1" selected> 1 </option>';
																}else{
																	$userlevel .= '<option value="1" > 1 </option>';
																}
																if($output->data->user_level == "2"){
																	$userlevel .= '<option value="2" selected> 2 </option>';
																}else{
																	$userlevel .= '<option value="2" > 2 </option>';
																}
																if($output->data->user_level == "3"){
																	$userlevel .= '<option value="3" selected> 3 </option>';
																}else{
																	$userlevel .= '<option value="3" > 3 </option>';
																}
																if($output->data->user_level == "4"){
																	$userlevel .= '<option value="4" selected disabled> 4 </option>';
																}else{
																	$userlevel .= '<option value="4"  disabled> 4 </option>';
																}
																if($output->data->user_level == "5"){
																	$userlevel .= '<option value="5" selected> 5 </option>';
																}else{
																	$userlevel .= '<option value="5" > 5 </option>';
																}
																if($output->data->user_level == "6"){
																	$userlevel .= '<option value="6" selected> 6 </option>';
																}else{
																	$userlevel .= '<option value="6" > 6 </option>';
																}
																if($output->data->user_level == "7"){
																	$userlevel .= '<option value="7" selected> 7 </option>';
																}else{
																	$userlevel .= '<option value="7" > 7 </option>';
																}
																if($output->data->user_level == "8"){
																	$userlevel .= '<option value="8" selected> 8 </option>';
																}else{
																	$userlevel .= '<option value="8" > 8 </option>';
																}
																if($output->data->user_level == "9"){
																	$userlevel .= '<option value="9" selected> 9 </option>';
																}else{
																	$userlevel .= '<option value="9" > 9 </option>';
																}
															echo $userlevel;
														?>
														</select>
													</div>
												</div>
											</fieldset>
											<fieldset>
												<div class="form-group">
													<label for="phone_login" class="col-sm-2 control-label"><?php if(isset($_SESSION['use_webrtc']) && $_SESSION['use_webrtc'] == 1){ echo "<i class='fa fa-info-circle' title='You cannot edit this field since WebRTC is enabled.'></i> ";} ?> Phone Login</label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="phone_login" id="phone_login"  <?php if(isset($_SESSION['use_webrtc']) && $_SESSION['use_webrtc'] == 1){ echo "disabled";} ?>
															value="<?php echo $output->data->phone_login;?>" maxlength="20" placeholder="<?php $lh->translateText("phone_login"); ?>" />
														<label id="phone_login-error"></label>
													</div>
												</div>
												<!--
												<div class="form-group">
													<label for="phone_password" class="col-sm-2 control-label">Phone Password</label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="phone_password" id="phone_password" 
															value="<?php //echo $output->data->phone_pass;?>" maxlength="20" placeholder="Phone Password" />
													</div>
												</div> -->									
												<div class="form-group">
													<label for="voicemail" class="col-sm-2 control-label"><?php $lh->translateText("voicemail"); ?></label>
													<div class="col-sm-10 mb">
														<select class="form-control select2-1" name="voicemail" id="voicemail">
															<?php
																if ($voicemails == NULL ){
															?>
																<option value="" selected>--No Voicemails Available--</option>
															<?php
																} else {
															?>
																<option value="">- - - NONE - - -</option>
																<?php
																	for($a=0;$a<count($voicemails->voicemail_id);$a++){
																		if ($voicemails->active[$a] == "Y") {
																			$voicemail_id = $voicemails->voicemail_id[$a];
																			$vm_status = $voicemails->active[$a];
																			$vmname	= $voicemails->fullname[$a];																		
																?>
																	<option value="<?php echo $voicemail_id;?>" 
																		<?php if($output->data->voicemail_id == $voicemail_id){echo "selected";}?> />
																	<?php echo $voicemail_id.' - '.$vmname;?>
																	</option>									
															<?php
																		}
																	}
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="change_pass" class="col-sm-2 control-label"><?php $lh->translateText("change_password"); ?></label>
													<div class="col-sm-10 mb">
														<select class="form-control " name="change_pass" id="change_pass">
															<option value="N" selected> No </option>
															<option value="Y" > Yes </option>
														</select>
													</div>
												</div>
												<div class="form-group form_password" style="display:none;">
													<label for="password" class="col-sm-2 control-label"><?php $lh->translateText("password"); ?></label>
													<div class="col-sm-10 mb">
														<input type="password" class="form-control" name="password" id="password" <?php if($output->data->user_level >= 8){echo 'maxlength="20"';}else{echo 'maxlength="10"';} ?> placeholder="<?php $lh->translateText("password"); ?>" />
														<small><i><span id="pass_result"></span></i></small>
													</div>
												</div>
												<div class="form-group form_password" style="display:none;">
													<label for="conf_password" class="col-sm-2 control-label"><?php $lh->translateText("confirm_password"); ?></label>
													<div class="col-sm-10 mb">
														<input type="password" class="form-control" id="conf_password" placeholder="<?php $lh->translateText("confirm_password"); ?>" required />
														<span id="pass_result"></span></i></small>
													</div> 
												</div>
											</fieldset>
									   	</div><!-- tab 1 -->

						<!-- ADVANCED SETTINGS -->
						<div id="tab_2" class="tab-pane fade in">
						<fieldset>
						<div class="row form-group mt">
							<label for="hotkeys" class="col-sm-3 control-label"><?php $lh->translateText("hotkeys"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="hotkeys" id="hotkeys">
								<?php
									$hotkeys = NULL;
									if ($output->data->hotkeys_active == "1") {
										$hotkeys .= '<option value="1" selected> Active </option>';
									} else {
										$hotkeys .= '<option value="1" > Active </option>';
									}
									
									if ($output->data->hotkeys_active == "0") {
										$hotkeys .= '<option value="0" selected> Inactive </option>';
									} else {
										$hotkeys .= '<option value="0" > Inactive </option>';
									}
									echo $hotkeys;
								?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="vicidial_recording_override" class="col-sm-3 control-label"><?php $lh->translateText("agent_recordings"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="vicidial_recording_override" id="vicidial_recording_override">
									<?php
										$agents_recordings = NULL;
										if($output->data->vicidial_recording_override == "DISABLED"){
											$agents_recordings .= '<option value="DISABLED" selected> DISABLED </option>';
										}else{
											$agents_recordings .= '<option value="DISABLED" > DISABLED </option>';
										}
										
										if($output->data->vicidial_recording_override == "NEVER"){
											$agents_recordings .= '<option value="NEVER" selected> NEVER </option>';
										}else{
											$agents_recordings .= '<option value="NEVER" > NEVER </option>';
										}
										if($output->data->vicidial_recording_override == "ONDEMAND"){
											$agents_recordings .= '<option value="ONDEMAND" selected> ONDEMAND </option>';
										}else{
											$agents_recordings .= '<option value="ONDEMAND" > ONDEMAND </option>';
										}
										if($output->data->vicidial_recording_override == "ALLCALLS"){
											$agents_recordings .= '<option value="ALLCALLS" selected> ALLCALLS </option>';
										}else{
											$agents_recordings .= '<option value="ALLCALLS" > ALLCALLS </option>';
										}
										if($output->data->vicidial_recording_override == "ALLFORCE"){
											$agents_recordings .= '<option value="ALLFORCE" selected> ALLFORCE </option>';
										}else{
											$agents_recordings .= '<option value="ALLFORCE" > ALLFORCE </option>';
										}
										echo $agents_recordings;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="vicidial_transfers" class="col-sm-3 control-label"><?php $lh->translateText("agent_transfers"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="vicidial_transfers" id="vicidial_transfers">
									<?php
										$vicidial_transfers = NULL;
										if($output->data->vicidial_transfers == "0"){
											$vicidial_transfers .= '<option value="0" selected> NO </option>';
										}else{
											$vicidial_transfers .= '<option value="0" > NO </option>';
										}
										
										if($output->data->vicidial_transfers == "1"){
											$vicidial_transfers .= '<option value="1" selected> YES </option>';
										}else{
											$vicidial_transfers .= '<option value="1" > YES </option>';
										}
										echo $vicidial_transfers;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="closer_default_blended" class="col-sm-3 control-label"><?php $lh->translateText("closer_default_blended"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="closer_default_blended" id="closer_default_blended">
									<?php
										$closer_default_blended = NULL;
										if($output->data->closer_default_blended == "0"){
											$closer_default_blended .= '<option value="0" selected> NO </option>';
										}else{
											$closer_default_blended .= '<option value="0" > NO </option>';
										}
										
										if($output->data->closer_default_blended == "1" ){
											$closer_default_blended .= '<option value="1" selected> YES </option>';
										}else{
											$closer_default_blended .= '<option value="1" > YES </option>';
										}
										echo $closer_default_blended;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="agentcall_manual" class="col-sm-3 control-label"><?php $lh->translateText("agent_call_manual"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="agentcall_manual" id="agentcall_manual">
									<?php
										$agentcall_manual = NULL;
										if($output->data->agentcall_manual == "0"){
											$agentcall_manual .= '<option value="0" selected> NO </option>';
										}else{
											$agentcall_manual .= '<option value="0" > NO </option>';
										}
										
										if($output->data->agentcall_manual == "1" ){
											$agentcall_manual .= '<option value="1" selected> YES </option>';
										}else{
											$agentcall_manual .= '<option value="1" > YES </option>';
										}
										echo $agentcall_manual;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="scheduled_callbacks" class="col-sm-3 control-label"><?php $lh->translateText("scheduled_callbacks"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="scheduled_callbacks" id="scheduled_callbacks">
									<?php
										$scheduled_callbacks = NULL;
										if($output->data->scheduled_callbacks == "0"){
											$scheduled_callbacks .= '<option value="0" selected> NO </option>';
										}else{
											$scheduled_callbacks .= '<option value="0" > NO </option>';
										}
										
										if($output->data->scheduled_callbacks == "1" ){
											$scheduled_callbacks .= '<option value="1" selected> YES </option>';
										}else{
											$scheduled_callbacks .= '<option value="1" > YES </option>';
										}
										echo $scheduled_callbacks;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="agentonly_callbacks" class="col-sm-3 control-label"><?php $lh->translateText("agent_only_callbacks"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="agentonly_callbacks" id="agentonly_callbacks">
									<?php
										$agentonly_callbacks = NULL;
										if($output->data->agentonly_callbacks == "0"){
											$agentonly_callbacks .= '<option value="0" selected> '.$lh->translationFor("go_no").' </option>';
										}else{
											$agentonly_callbacks .= '<option value="0" > '.$lh->translationFor("go_no").' </option>';
										}
										
										if($output->data->agentonly_callbacks == "1" ){
											$agentonly_callbacks .= '<option value="1" selected> YES </option>';
										}else{
											$agentonly_callbacks .= '<option value="1" > YES </option>';
										}
										echo $agentonly_callbacks;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="api_access" class="col-sm-3 control-label"><i class="fa fa-info-circle" title="If disabled, agent won't be able to take calls."></i> Allow API Access</label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="api_access" id="api_access">
									<?php
										$api_access = NULL;
										if($output->data->vdc_agent_api_access == "1"){
											$api_access .= '<option value="1" selected> Enabled </option>';
										}else{
											$api_access .= '<option value="1" > Enable </option>';
										}
										
										if($output->data->vdc_agent_api_access == "0" || $output->data->vdc_agent_api_access == NULL){
											$api_access .= '<option value="0" selected> Disabled </option>';
										}else{
											$api_access .= '<option value="0" > Disable </option>';
										}
										echo $api_access;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="choose_ingroup" class="col-sm-3 control-label"><?php $lh->translateText("agent_choose_ingroup"); ?> </label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="choose_ingroup" id="choose_ingroup">
									<?php
										$choose_ingroup = NULL;
										if($output->data->agent_choose_ingroups == "1"){
											$choose_ingroup .= '<option value="1" selected> YES </option>';
										}else{
											$choose_ingroup .= '<option value="1" > YES </option>';
										}
										
										if($output->data->agent_choose_ingroups == "0" || $output->data->agent_choose_ingroups == NULL){
											$choose_ingroup .= '<option value="0" selected> '.$lh->translationFor("go_no").' </option>';
										}else{
											$choose_ingroup .= '<option value="0" > '.$lh->translationFor("go_no").' </option>';
										}
										echo $choose_ingroup;
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<label for="agent_lead_search_override" class="col-sm-3 control-label"><?php $lh->translateText("agent_lead_search_override"); ?></label>
							<div class="col-sm-9 mb">
								<select class="form-control" name="agent_lead_search_override" id="agent_lead_search_override">
									<option value="ENABLED" <?php if ($output->data->agent_lead_search_override == 'ENABLED') { echo "selected"; }?>>ENABLED</option>
									<option value="DISABLED" <?php if ($output->data->agent_lead_search_override == 'DISABLED') { echo "selected"; }?>>DISABLED</option>
									<option value="NOT_ACTIVE" <?php if ($output->data->agent_lead_search_override == 'NOT_ACTIVE') { echo "selected"; }?>>NOT ACTIVE</option>
								</select>
							</div>
						</div>
						</fieldset>		
					</div>
					
					<!-- FOOTER BUTTONS -->
					<div id="modifyUSERresult"></div>
					
					<fieldset class="footer-buttons">
					<div class="box-footer">
					   <div class="col-sm-4 pull-right">
							<a href="telephonyusers.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
							<button type="submit" class="btn btn-primary" id="modifyUserOkButton" href="" data-id="<?php echo $output->data->user; ?>" <?=($perm->user_update !== 'U' ? 'disabled' : '')?>> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
					   </div>
					</div>
					</fieldset>
					
					</div>
					</div><!-- end of tab content -->
					</form>
					</div><!-- tab panel -->
							
	<?php
			
		} else {
		# An error occured
			echo $output->data->result;
		}
	}
		
	?>
	</div><!-- body -->

                </section>
				<!-- /.content -->

				<?php
					}
				?>
				</div>
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
			
        </div><!-- ./wrapper -->

        <?php print $ui->standardizedThemeJS(); ?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

<script type="text/javascript">
	$(document).ready(function() {
		/* initialize select2 */
		$('.select2-1').select2({ theme: 'bootstrap' });
		$.fn.select2.defaults.set( "theme", "bootstrap" );
		
		// for cancelling
		$(document).on('click', '#cancel', function(){
			swal("Cancelled", "No action has been done :)", "error");
		});

		// validations
		$('#change_pass').on('change', function() {
		//  alert( this.value ); // or $(this).val()
			if(this.value == "Y") 
				$('.form_password').show();
			
			if(this.value == "N") 
				$('.form_password').hide();			
		});

		// password
		$("#password").keyup(checkPasswordMatch);
		$("#conf_password").keyup(checkPasswordMatch);

		// phone login
		$("#phone_login").keyup(function() {
			clearTimeout($.data(this, 'timer'));
			var wait = setTimeout(validate_user, 500);
			$(this).data('timer', wait);
		});

		// modify user
		$('#modifyUserOkButton').click(function(){ // on click submit
			var user = $(this).attr('data-id');
			console.log(user);
			$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
			$('#modifyUserOkButton').prop("disabled", true);

			// variables for check password
			var validate_password = 0;
			var change_pass = document.getElementById('change_pass').value;
			var password = document.getElementById('password').value;
			var conf_password = document.getElementById('conf_password').value;
			
			// variables for check valid email
			var validate_email = 0;
			var email = document.getElementById('email').value;
            var x = document.forms["modifyuser"]["email"].value;
            var atpos = x.indexOf("@");
            var dotpos = x.lastIndexOf(".");
			
			// conditional statements
			if (change_pass == "Y") {
				if (password != conf_password) {
					validate_password = 1;
				}
				if (password == "") {
					validate_password = 2;
				}
			}
				
			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
				validate_email = 1;
			} else {
				validate_email = 0;
			}
			
			if (email == "") {
				validate_email = 0;
			}

			// validate results
			if (validate_email == 1) {
				$('#update_button').html("<i class='fa fa-check'></i> Update");
				$('#modifyUserOkButton').prop("disabled", false);	
				$("#email_check").html("<font color='red'>Input a Valid Email Address</font>");
				$('#email_check').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
			}
			if (validate_password == 1) {
				$('#update_button').html("<i class='fa fa-check'></i> Update");
				$('#modifyUserOkButton').prop("disabled", false);	
			}
			if (validate_password == 2) {
				$("#pass_result").html("<font color='red'><i class='fa fa-warning'></i> Input and Confirm Password, otherwise mark Change Password? as NO! </font>");
				$('#update_button').html("<i class='fa fa-check'></i> Update");
				$('#modifyUserOkButton').prop("disabled", false);
			}

			// validations
			if (validate_email == 0 && validate_password == 0 && <?=($perm->user_update === 'U')?>) {
				$("#phone_login").prop("disabled", false);				
				$.ajax({
					url: "./php/ModifyTelephonyUser.php",
					type: 'POST',
					data: $("#modifyuser").serialize() + '&user=' + user,
					success: function(data) {
					console.log($("#modifyuser").serialize() + '&user=' + user);
					$("#phone_login").prop("disabled", true);
						if (data == 1) {
							$('#update_button').html("<i class='fa fa-check'></i> Update");
							$('#modifyUserOkButton').prop("disabled", false);
							swal(
								{
									title: "<?php $lh->translateText("success"); ?>",
									text: "<?php $lh->translateText("user_update_success"); ?>",
									type: "success"
								},
								function(){
									location.replace("./telephonyusers.php");
								}
							);
						} else {
							sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?> " + data, "error");
							$('#update_button').html("<i class='fa fa-check'></i> Update");
							$('#modifyUserOkButton').prop("disabled", false);
						}
					}
				});
			}
			return false;
		});
		
		$(document).on('change','#userlevel',function() {
			if($("#userlevel").val() >= 8){
				$("#password").attr('maxlength','20');
				$("#password").val('');
			}else {
				$("#password").attr('maxlength','10');
				$("#password").val('');
			}
		});
		
		// disable special characters and allow spaces on full name
		$('#fullname').bind('keypress', function (event) {
		    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});

		// allow only numbers in phone_login
		$('#phone_login').bind('keypress', function (event) {
		    var regex = new RegExp("^[0-9]+$");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});
	});
	
	function validate_user(){
		var user_form_value = $('#agent_name').text();
		var phone_logins_value = $('#phone_login').val();
		console.log(user_form_value);
		if(phone_logins_value != ""){
			$.ajax({
				url: "php/checkUser.php",
				type: 'POST',
				data: {
					user : user_form_value,
					phone_login : phone_logins_value,
					type : "edit"
				},
				success: function(data) {
					console.log(data);
					if(data == 1){
						checker = 0;
						$( "#phone_login" ).removeClass("error");
						$( "#phone_login-error" ).text( "Phone extension is valid." ).removeClass("error").addClass("avail");
						$('#modifyUserOkButton').prop("disabled", false);
					}else{
						$( "#phone_login" ).addClass( "error" );
						$( "#phone_login-error" ).text( data ).removeClass("avail").addClass("error");
						$('#modifyUserOkButton').prop("disabled", true);
						
						checker = 1;
					}
				}
			});
		}
	}

	function checkPasswordMatch() {
		var password = $("#password").val();
		var confirmPassword = $("#conf_password").val();

		if (password != confirmPassword)
			$("#pass_result").html("<font color='red'>Passwords Do Not Match! <font size='5'>✖</font> </font>");
		else
				$("#pass_result").html("<font color='green'>Passwords Match! <font size='5'>✔</font> </font>");
	}
	
</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
