<?php

	###################################################
	### Name: editsettingsusergroups.php 	   ###
	### Functions: Edit Usergroups 	   ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	   ###
	### Version: 4.0    	   ###
	### Written by: Alexander Jim H. Abenoja	   ###
	### License: AGPLv2	   ###
	###################################################

	require_once('./php/CRMDefaults.php');
	require_once('./php/UIHandler.php');
	//require_once('./php/DbHandler.php');
	require_once('./php/LanguageHandler.php');
	require('./php/Session.php');
	require_once('./php/goCRMAPISettings.php');

	// initialize structures
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

$usergroup_id = NULL;
if (isset($_POST["usergroup_id"])) {
	$usergroup_id = $_POST["usergroup_id"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("edit_user_group"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       	
       	<?php print $ui->standardizedThemeCSS(); ?> 

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
                        <small><?php $lh->translateText("edit_user_group"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["usergroup_id"])){
						?>	
							<li><a href="./settingsusergroups.php"><?php $lh->translateText("User Groups"); ?></a></li>
                        <?php
	}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

						<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;
					
					//if(isset($extenid)) {
						$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetUserGroupInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["user_group"] = $usergroup_id; #Desired exten ID. (required)
						$postfields["log_user"] = $_SESSION['user'];
						$postfields["log_group"] = $_SESSION['usergroup'];
						$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
						 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);
						 
						if ($output->result=="success") {
							
						# Result was OK!
					?>

            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
					<legend><?php echo $lh->translationFor("modify_user_group"); ?> : <u><?php echo $usergroup_id;?></u></legend>
					
						<form id="modifyvoicemail">
							<input type="hidden" name="modifyid" value="<?php echo $usergroup_id;?>">
							<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
							<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
						
					<!-- Custom Tabs -->
					<div role="tabpanel">
					<!--<div class="nav-tabs-custom">-->
						<ul role="tablist" class="nav nav-tabs nav-justified">
							<li class="active"><a href="#tab_1" data-toggle="tab"><?php $lh->translateText("basic_settings"); ?></a></li>
							<li><a href="#tab_2" data-toggle="tab"><?php $lh->translateText("allowed_campaigns"); ?> </a></li>
							<li><a href="#tab_3" data-toggle="tab"><?php $lh->translateText("group_permissions"); ?> </a></li>
						</ul>
					   <!-- Tab panes-->
					   <div class="tab-content">
						
							<!-- BASIC SETTINGS -->
							<div id="tab_1" class="tab-pane fade in active">
								<fieldset>
									<div class="form-group row mt">
										<label for="group_name" class="col-sm-2 control-label"><?php $lh->translateText("group_name"); ?></label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name (Mandatory)" value="<?php echo $output->data->group_name;?>">
										</div>
									</div>
									<div class="form-group row">
										<label for="forced_timeclock_login" class="col-sm-2 control-label"><?php $lh->translateText("force_timelock"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" name="forced_timeclock_login" id="forced_timeclock_login">
											<?php
												$forced_timeclock_login = NULL;
												if($output->data->forced_timeclock_login == "N"){
													$forced_timeclock_login .= '<option value="N" selected> '.$lh->translationFor("go_no").' </option>';
												}else{
													$forced_timeclock_login .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
												}
												
												if($output->data->forced_timeclock_login == "Y"){
													$forced_timeclock_login .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
												}else{
													$forced_timeclock_login .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
												}
					
												if($output->data->forced_timeclock_login == "ADMIN_EXEMPT"){
													$forced_timeclock_login .= '<option value="ADMIN_EXEMPT" selected> ADMIN EXEMPT </option>';
												}else{
													$forced_timeclock_login .= '<option value="ADMIN_EXEMPT" > ADMIN EXEMPT </option>';
												}
												echo $forced_timeclock_login;
											?>
											</select>
										</div>
									</div>
							
									<div class="form-group row">
										<label for="shift_enforcement" class="col-sm-2 control-label"><?php $lh->translateText("shift_enforcement"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" id="shift_enforcement" name="shift_enforcement">
												<?php
													$shift_enforcement = NULL;
					
													if($output->data->shift_enforcement == "OFF" || $output->data->shift_enforcement == ""){
														$shift_enforcement .= '<option value="OFF" selected> OFF </option>';
													}else{
														$shift_enforcement .= '<option value="OFF" > OFF </option>';
													}
													
													if($output->data->shift_enforcement== "START"){
														$shift_enforcement .= '<option value="START" selected> START </option>';
													}else{
														$shift_enforcement .= '<option value="START" > START </option>';
													}
													
													if($output->data->shift_enforcement == "ALL"){
														$shift_enforcement .= '<option value="ALL" selected> ALL </option>';
													}else{
														$shift_enforcement .= '<option value="ALL" > ALL </option>';
													}
													
													echo $shift_enforcement;
												?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label for="group_level" class="col-sm-2 control-label"><?php $lh->translateText("group_level"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" name="group_level" id="group_level">
											<?php
												$group_level = NULL;
												for($o=1; $o <= 9; $o++){
													if($output->data->group_level == $o){
														$group_level .= '<option value="'.$o.'" selected> '.$o.' </option>';
													}else{
														$group_level .= '<option value="'.$o.'"> '.$o.' </option>';
													}
												}
					
												echo $group_level;
											?>
											</select>
										</div>
									</div>
									<!--
									<div class="form-group row">
										<label for="group_list_id" class="col-sm-2 control-label">Group List ID</label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="group_list_id" id="group_list_id" placeholder="Group List ID" value="<?php echo $output->data->group_list_id;?>" readonly>
										</div>
									</div>-->
								</fieldset>
							</div><!-- tab 1 -->
							
							<div id="tab_2" class="tab-pane fade in">
								<fieldset>
									<div class="form-group mt row">
										<label for="group_level" class="col-sm-2 control-label"><?php $lh->translateText("allowed_campaigns"); ?></label>
										<div class="col-sm-10 mb responsive" style="height: 50%;overflow-y: auto;">
											<div class="checkbox c-checkbox" style="margin-right: 15px;">
												<?php
												$checkAllCamp = (preg_match("/ALL-CAMPAIGNS/", $output->data->allowed_campaigns) ? ' checked' : '');
												?>
												<label><input id="camp-all" name="allowed_camp[]" type="checkbox" value="-ALL-CAMPAIGNS-"<?=$checkAllCamp?>><span class="fa fa-check"></span> <strong>ALL-CAMPAIGNS - USERS CAN VIEW ANY CAMPAIGN</strong></label>
											</div>
											<?php
											$camp_list = $ui->API_getListAllCampaigns();
											if (count($camp_list->campaign_id) > 0) {
												foreach ($camp_list->campaign_id as $k => $camp) {
													$checkCamp = (preg_match("/\s{$camp}\s/", $output->data->allowed_campaigns) ? ' checked' : '');
													echo '<div class="checkbox c-checkbox" style="margin-right: 15px;">';
													echo '<label><input id="camp-'.$camp.'" name="allowed_camp[]" type="checkbox" value="'.$camp.'"'.$checkCamp.'><span class="fa fa-check"></span> '.$camp.' - '.$camp_list->campaign_name[$k].'</label>';
													echo '</div>';
												}
											}
											?>
										</div>
									</div>
									<!--
									<div class="form-group row">
										<label for="group_list_id" class="col-sm-2 control-label">Group List ID</label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="group_list_id" id="group_list_id" placeholder="Group List ID" value="<?php echo $output->data->group_list_id;?>" readonly>
										</div>
									</div>-->
								</fieldset>
							</div><!-- tab 2 -->
							
							<!-- ADVANCE SETTINGS -->
							<div id="tab_3" class="tab-pane fade in">
								<fieldset>
								<?php
									$perms = json_decode($output->data->permissions);
									
									if (is_null($perms) || is_null($perms->dashboard->dashboard_display)) {
										$perms = json_decode('{"dashboard":{"dashboard_display":"N"},"user":{"user_create":"N","user_read":"N","user_update":"N","user_delete":"N"},"campaign":{"campaign_create":"N","campaign_read":"N","campaign_update":"N","campaign_delete":"N"},"disposition":{"disposition_create":"N","disposition_update":"N","disposition_delete":"N"},"pausecodes":{"pausecodes_create":"N","pausecodes_read":"N","pausecodes_update":"N","pausecodes_delete":"N"},"hotkeys":{"hotkeys_create":"N","hotkeys_read":"N","hotkeys_delete":"N"},"list":{"list_create":"N","list_read":"N","list_update":"N","list_delete":"N","list_upload":"N"},"customfields":{"customfields_create":"N","customfields_read":"N","customfields_update":"N","customfields_delete":"N"},"script":{"script_create":"N","script_read":"N","script_update":"N","script_delete":"N"},"inbound":{"inbound_create":"N","inbound_read":"N","inbound_update":"N","inbound_delete":"N"},"ivr":{"ivr_create":"N","ivr_read":"N","ivr_update":"N","ivr_delete":"N"},"did":{"did_create":"N","did_read":"N","did_update":"N","did_delete":"N"},"voicefiles":{"voicefiles_upload":"N","voicefiles_play":"N","voicefiles_download":"N"},"moh":{"moh_create":"N","moh_read":"N","moh_update":"N","moh_delete":"N"},"servers":{"servers_create":"N","servers_read":"N","servers_update":"N","servers_delete":"N"},"carriers":{"carriers_create":"N","carriers_read":"N","carriers_update":"N","carriers_delete":"N"},"reportsanalytics":{"reportsanalytics_statistical_display":"N","reportsanalytics_agent_time_display":"N","reportsanalytics_agent_performance_display":"N","reportsanalytics_dial_status_display":"N","reportsanalytics_agent_sales_display":"N","reportsanalytics_sales_tracker_display":"N","reportsanalytics_inbound_call_display":"N","reportsanalytics_export_call_display":"N"},"recordings":{"recordings_display":"N"},"support":{"support_display":"N"},"multi-tenant":{"tenant_create":"N","tenant_display":"N","tenant_update":"N","tenant_delete":"N","tenant_logs":"N","tenant_calltimes":"N","tenant_phones":"N","tenant_voicemails":"N"},"chat":{"chat_create":"N","chat_read":"N","chat_update":"N","chat_delete":"N"},"osticket":{"osticket_create":"N","osticket_read":"N","osticket_update":"N","osticket_delete":"N"}}');
									}
									
									$list_perms = '';
									foreach ($perms as $type => $perm) {
										$hiddenRow = '';
										if (preg_match("/support|multi-tenant|chat|osticket/", $type)) { $hiddenRow = ' hidden'; }
										$list_perms .= '<div class="form-group row mt'.$hiddenRow.'">';
										$list_perms .= '<label for="group_name" class="col-sm-4 control-label">'.$lh->translationFor($type).'</label>';
										$list_perms .= '<div class="col-sm-8 mb">';
										
										foreach ($perm as $idx => $value) {
											if (!preg_match("/^(moh_read)$/", $idx)) {
												$checkThis = '';
												if ($value !== 'N') { $checkThis = ' checked'; }
												$disableThis = '';
												if (preg_match("/^(ADMIN|AGENTS)$/", $usergroup_id)) { $disableThis = ' disabled'; }
												
												$defaultValue = 'Y';
												$list_options = '';
												$label = $idx;
												if (!preg_match("/dashboard|reportsanalytics|recordings|support/", $type)) {
													if (preg_match("/_create$/", $idx)) { $defaultValue = 'C'; $label = 'Create'; }
													if (preg_match("/_read$/", $idx)) { $defaultValue = 'R'; $label = 'Read'; }
													if (preg_match("/_update$/", $idx)) { $defaultValue = 'U'; $label = 'Update'; }
													if (preg_match("/_delete$/", $idx)) { $defaultValue = 'D'; $label = 'Delete'; }
													if (preg_match("/_upload$/", $idx)) { $defaultValue = 'C'; $label = 'Upload'; }
													if (preg_match("/_play/", $idx)) { $defaultValue = 'Y'; $label = 'Play'; }
													if (preg_match("/_download/", $idx)) { $defaultValue = 'Y'; $label = 'Download'; }
													$list_options = ' display: inline-block;';
													
													if ($type == 'multi-tenant' && !preg_match("/(_create|_display|_update|_delete)$/", $idx)) {
														$list_options = '';
													}
												}
												$list_perms .= '<div class="checkbox c-checkbox" style="margin-right: 15px;'.$list_options.'">';
												$list_perms .= '<label><input name="'.$idx.'" id="'.$idx.'" type="checkbox"'.$checkThis.' value="'.$defaultValue.'"'.$disableThis.'><span class="fa fa-check"></span> '.$lh->translationFor($label).'</label>';
												$list_perms .= '</div>';
											}
										}
										$list_perms .= '</div>';
										$list_perms .= '</div>';
									}
									echo $list_perms;
								?>
								</fieldset>
							</div><!-- tab 2 -->
	
	<!-- FOOTER BUTTONS -->
	<fieldset class="footer-buttons">
		<div class="box-footer">
		   <div class="col-sm-4 pull-right">
				<a href="settingsusergroups.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText('cancel'); ?> </a>
				<button type="submit" class="btn btn-primary" id="modifyUserGroupOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText('update'); ?> </span></button>
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
					?>
					
				<!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>

        </div><!-- ./wrapper -->

  		
		
		<!-- Modal Dialogs -->
		<?php print $ui->standardizedThemeJS(); ?> 
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {
				
				// for cancelling
				$(document).on('click', '#cancel', function(){
					swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
				});

				/** 
				 * Modifies a telephony list
			 	 */
				$("#modifyvoicemail").validate({
                	submitHandler: function() {
						//submit the form
							$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
							$('#modifyUserGroupOkButton').prop("disabled", true);

							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyUsergroup.php", //post
							$("#modifyvoicemail").serialize(), 
								function(data){
									//if message is sent
									console.log($("#modifyvoicemail").serialize());
									if (data == 1) {
										swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("usergroup_modify_success"); ?>", "success");
                                        window.setTimeout(function(){location.reload()},2000);
                                        $('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
                                        $('#modifyUserGroupOkButton').prop("disabled", false);
									} else {
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
										$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?> ");
										$('#modifyUserGroupOkButton').prop("disabled", false);
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				$("div.checkbox input[type='checkbox']").on('click', function() {
					console.log($(this).attr('id'));
				});
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
