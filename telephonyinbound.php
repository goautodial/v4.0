<?php
/**
 * @file        telephonyinbound.php
 * @brief       Manage Inbound, IVR & DID
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
	
	$perm = $api->goGetPermissions('inbound,ivr,did');
	$gopackage = $api->API_getGOPackage();

	if($gopackage->packagetype === "gosmall" && ($_SESSION['user'] !== "goautodial" && $_SESSION !== "goAPI") ){
		header("location:index.php");
	}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("inbound"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
		
		<!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
		
     	<!-- bootstrap color picker -->
		<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>

    </head>
    
    <?php print $ui->creamyBody(); ?>

        <div class="wrapper">	
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar(), $_SESSION["usergroup"]); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("inbound_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("inbound"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($perm->inbound->inbound_read !== 'N' || $perm->ivr->ivr_read !== 'N' || $perm->did->did_read !== 'N') { ?>

<?php
	/*
	 * APIs used
	 */

	$ingroup = $api->API_getAllInGroups();
	$ivr = $api->API_getAllIVRs();
	$phonenumber = $api->API_getAllDIDs();

	/*
	 * APIs for getting lists for the some of the forms
	 */
	$users = $api->API_getAllUsers();
	$user_groups = $api->API_getAllUserGroups();
	$campaign = $api->API_getAllCampaigns();
	$voicemails = $api->API_getAllVoicemails();
	$phones = $api->API_getAllPhones();
	$scripts = $api->API_getAllScripts();
	$voicefiles = $api->API_getAllVoiceFiles();
	$calltimes = $api->API_getAllCalltimes();
?>
			<div class="panel panel-default">
				<div class="panel-body">
					<legend><?php $lh->translateText("inbound"); ?>: <small><?php $lh->translateText("ingroup"); ?>, <?php $lh->translateText("call_menu"); ?>, <?php $lh->translateText('phone_numbers'); ?></small> </legend>

		            <div role="tabpanel">
						
						<ul role="tablist" class="nav nav-tabs nav-justified">

						<!-- In-group panel tabs-->
						<?php
						$toggleInbound = ' class="T_ingroup active"';
						$toggleIVR = '';
						$toggleDID = '';
						$activeInbound = ' active';
						$activeIVR = '';
						$activeDID = '';
						if ($perm->inbound->inbound_read === 'N') {
							$toggleInbound = ' class="T_ingroup hidden"';
							$activeInbound = '';
						}
						if ($perm->ivr->ivr_read === 'N') { $toggleIVR = ' class="T_ivr hidden"'; }
						if ($perm->ivr->ivr_read !== 'N' && $perm->inbound->inbound_read === 'N') {
							
							$toggleIVR = ' class="T_ivr active"';
							$activeIVR = ' active';
						}
						if ($perm->did->did_read === 'N') { $toggleDID = ' class="T_phonenumber hidden"'; }
						if ($perm->did->did_read !== 'N' && ($perm->inbound->inbound_read === 'N' && $perm->ivr->ivr_read === 'N')) {
							$toggleDID = ' class="T_phonenumber active"';
							$activeDID = ' active';
						}
						?>
							 <li role="presentation" class="T_ingroup <?=$activeInbound?>">
								<a href="#T_ingroup" aria-controls="T_ingroup" role="tab" data-toggle="tab" class="bb0">
								    <?php $lh->translateText('ingroups'); ?></a>
							 </li>
						<!-- IVR panel tab -->
							 <li role="presentation" class="T_ivr <?=$activeIVR?>">
								<a href="#T_ivr" aria-controls="T_ivr" role="tab" data-toggle="tab" class="bb0">
								    <?php $lh->translateText('interactive_voice_response'); ?> </a>
							 </li>
						<!-- DID panel tab -->
							 <li role="presentation" class="T_phonenumber <?=$activeDID?>">
								<a href="#T_phonenumber" aria-controls="T_phonenumber" role="tab" data-toggle="tab" class="bb0">
								    <?php $lh->translateText('phone_numbers_did_tfn'); ?> </a>
							 </li>
						  </ul>
						  
						<!-- Tab panes-->
						<div class="tab-content bg-white">

							<!--==== In-group ====-->
							<div id="T_ingroup" role="tabpanel" class="tab-pane T_ingroup <?=$activeInbound?>">
								<table class="responsive display no-wrap table table-striped table-bordered table-hover" width="100%" id="table_ingroup">
								   <thead>
									  <tr>
                                         <th style="color: white;">Pic</th>
										 <th><?php $lh->translateText('ingroup'); ?></th>
										 <th><?php $lh->translateText('description'); ?></th>
										 <th><?php $lh->translateText('priority'); ?></th>
										 <th><?php $lh->translateText('status'); ?></th>
										 <th><?php $lh->translateText('time'); ?></th>
										 <th><?php $lh->translateText('action'); ?></th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($ingroup->group_id);$i++){
							
												if($ingroup->active[$i] == "Y"){
													$ingroup->active[$i] = $lh->translationFor('active');
												}else{
													$ingroup->active[$i] = $lh->translationFor('inactive');
												}

											$action_INGROUP = $ui->getUserActionMenuForInGroups($ingroup->group_id[$i], $perm);

									   	?>	
											<tr>
                                                <td><avatar username='<?php echo $ingroup->group_name[$i];?>' :size='36'></avatar></td>
												<td><strong><?php if (($_SESSION['usergroup'] === "ADMIN" && $perm->inbound->inbound_update !== 'N') || ($_SESSION['usergroup'] !== "ADMIN" && $perm->inbound->inbound_update !== 'N' && !preg_match("/^AGENTDIRECT/", $ingroup->group_id[$i]))) { echo '<a class="edit-ingroup" data-id="'.$ingroup->group_id[$i].'">'; } ?><?php echo $ingroup->group_id[$i];?><?php if ($perm->inbound->inbound_update !== 'N') { echo '</a>'; } ?></strong></td>
												<td><?php echo $ingroup->group_name[$i];?></td>
												<td><?php echo $ingroup->queue_priority[$i];?></td>
												<td><?php echo $ingroup->active[$i];?></td>
												<td><?php echo $ingroup->call_time_id[$i];?></td>
												<td><?php if ($_SESSION['usergroup'] === "ADMIN" || ($_SESSION['usergroup'] !== "ADMIN" && !preg_match("/^AGENTDIRECT/", $ingroup->group_id[$i]))) echo $action_INGROUP;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>
							
							<!--==== IVR ====-->
							<div id="T_ivr" role="tabpanel" class="tab-pane T_ivr <?=$activeIVR?>">
								<table class="responsive display no-wrap table table-striped table-bordered table-hover" width="100%" id="table_ivr">
								   <thead>
									  <tr>
                                         <th style="color: white;">Pic</th>
										 <th><?php $lh->translateText('menu_id'); ?></th>
										 <th><?php $lh->translateText('description'); ?></th>
										 <th><?php $lh->translateText('prompt'); ?></th>
										 <th><?php $lh->translateText('timeout'); ?></th>
										 <th><?php $lh->translateText('action'); ?></th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($ivr->menu_id);$i++){

											$action_IVR = $ui->ActionMenuForIVR($ivr->menu_id[$i], $ivr->menu_name[$i], $perm);

									   	?>	
											<tr>
                                                <td><avatar username='<?php echo $ivr->menu_name[$i];?>' :size='36'></avatar></td>
												<td><strong><?php if ($perm->ivr->ivr_update !== 'N') { echo '<a class="edit-ivr" data-id="'.$ivr->menu_id[$i].'">'; } ?><?php echo $ivr->menu_id[$i];?><?php if ($perm->ivr->ivr_update !== 'N') { echo '</a>'; } ?></strong></td>
												<td><?php echo $ivr->menu_name[$i];?></td>
												<td><?php echo $ivr->menu_prompt[$i];?></td>
												<td><?php echo $ivr->menu_timeout[$i];?></td>
												<td><?php echo $action_IVR;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>

							<!--==== phonenumber / DID ====-->
							<div id="T_phonenumber" class="tab-pane T_phonenumber <?=$activeDID?>">
								<table class="responsive display no-wrap table table-striped table-bordered table-hover" width="100%" id="table_did">
								   <thead>
									  <tr>
                                         <th style="color: white;">Pic</th>
										 <th><?php $lh->translateText('phone_numbers'); ?></th>
										 <th><?php $lh->translateText('description'); ?></th>
										 <th><?php $lh->translateText('status'); ?></th>
										 <th><?php $lh->translateText('route'); ?></th>
										 <th><?php $lh->translateText('action'); ?></th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($phonenumber->did_pattern);$i++){

									   			if($phonenumber->active[$i] == "Y"){
													$phonenumber->active[$i] = $lh->translationFor('active');
												}else{
													$phonenumber->active[$i] = $lh->translationFor('inactive');
												}

												if($phonenumber->did_route[$i] == "IN_GROUP"){
													$phonenumber->did_route[$i] = "IN-GROUP";
												}
												if($phonenumber->did_route[$i] == "EXTEN"){
													$phonenumber->did_route[$i] = "CUSTOM EXTENSION";
												}

											$action_DID = $ui->getUserActionMenuForDID($phonenumber->did_id[$i], $phonenumber->did_description[$i], $perm);

									   	?>	
											<tr>
                                                <td><avatar username='<?php echo $phonenumber->did_description[$i];?>' :size='36'></avatar></td>
												<td><strong><?php if ($perm->did->did_update !== 'N') { echo '<a class="edit-phonenumber" data-id="'.$phonenumber->did_id[$i].'">'; } ?><?php echo $phonenumber->did_pattern[$i];?><?php if ($perm->inbound->inbound_update !== 'N') { echo '</a>'; } ?></strong></td>
												<td><?php echo $phonenumber->did_description[$i];?></td>
												<td><?php echo $phonenumber->active[$i];?></td>
												<td><?php echo $phonenumber->did_route[$i];?></td>
												<td><?php echo $action_DID;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>

						</div><!-- END tab content-->

							<!-- /fila con acciones, formularios y demás -->
							<?php
								} else {
									print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
								}
							?>
							
						<div class="bottom-menu skin-blue<?php if ($perm->inbound->inbound_create == 'N' && $perm->ivr->ivr_create == 'N' && $perm->did->did_create == 'N') { echo " hidden"; } ?>">
							<div class="action-button-circle" data-toggle="modal">
								<?php print $ui->getCircleButton("inbound", "plus"); ?>
							</div>
							<div class="fab-div-area" id="fab-div-area">
								<?php
								$menu = 3;
								$menuHeight = '250px';
								$hideInbound = '';
								$hideIVR = '';
								$hideDID = '';
								if ($perm->inbound->inbound_create === 'N') {
									$menu--;
									$hideInbound = ' hidden';
								}
								if ($perm->ivr->ivr_create === 'N') {
									$menu--;
									$hideIVR = ' hidden';
								}
								if ($perm->did->did_create === 'N') {
									$menu--;
									$hideDID = ' hidden';
								}
								if ($menu < 3) { $menuHeight = '170px'; }
								if ($menu < 2) { $menuHeight = '110px'; }
								?>
								<ul class="fab-ul" style="height: <?=$menuHeight?>;">
									<li class="li-style<?=$hideInbound?>"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_ingroups" title="Create an Ingroup"></a></li><?php if ($hideInbound === '') { echo '<br/>'; } ?>
									<li class="li-style<?=$hideIVR?>"><a class="fa fa-volume-control-phone fab-div-item" data-toggle="modal" aria-hidden="true" data-target="#add_ivr" title="Add an Interactive Voice Recording"></a></li><?php if ($hideIVR === '') { echo '<br/>'; } ?>
									<li class="li-style<?=$hideDID?>"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers" title="Add a Phone Number / DID / TFN"> </a></li>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- /. body -->
			</div><!-- /. panel -->
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
	<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
</div><!-- ./wrapper -->


<!-- TELEPHONY INBOUND MODALS -->

	<!-- ADD INGROUP MODAL -->
		<div class="modal fade" id="add_ingroups" aria-labelledby="ingroup_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">

			<!-- Header -->
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="ingroup_modal">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create ingroups."></i> 
						<b><?php $lh->translateText("in_group_wizard"); ?> » <?php $lh->translateText("create_new_ingroup"); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body wizard-content">
				
				<form action="" method="POST" id="create_ingroup" role="form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
					<div class="row">
						<h4><?php $lh->translateText("group_details"); ?>
                           <br>
                           <small><?php $lh->translateText("fill_up_group_details"); ?></small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-3 control-label" for="groupid"><?php $lh->translateText("group_id"); ?>:</label>
								<div class="col-sm-9 mb">
									<input type="text" name="groupid" id="groupid" class="form-control" placeholder="<?php $lh->translateText("group_id"); ?>" title="Must be 2-20 characters in length." maxlength="20" minlength="2" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="groupname"><?php $lh->translateText("group_name"); ?></label>
								<div class="col-sm-9 mb">
									<input type="text" name="groupname" id="groupname" class="form-control" placeholder="<?php $lh->translateText("group_name"); ?>" title="Must be 2-20 characters in length." maxlength="20" minlength="2" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="color"><?php $lh->translateText("group_color"); ?></label>
								<div class="col-sm-9 mb">
						            <input type="text" class="form-control colorpicker" name="color" id="color" value="#fffff">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="active"><?php $lh->translateText("active"); ?></label>
								<div class="col-sm-9 mb">
									<select name="active" id="active" class="form-control">
										<option value="Y" selected><?php $lh->translateText("go_yes"); ?></option>
										<option value="N"><?php $lh->translateText("go_no"); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="web_form"><?php $lh->translateText("web"); ?></label>
								<div class="col-sm-9 mb">
									<input type="url" name="web_form" id="web_form" class="form-control" placeholder="<?php $lh->translateText("web"); ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="user_group"><?php $lh->translateText("user_group"); ?></label>
								<div class="col-sm-9 mb">
									<select id="user_group" class="form-control select2-1" name="user_group" style="width:100%;">
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
												if (strtoupper($_SESSION['usergroup']) !== 'ADMIN' && strtoupper($user_groups->user_group[$i]) !== strtoupper($_SESSION['usergroup'])) {
													continue;
												}
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
						</fieldset>
						<h4><?php $lh->translateText("other_settings"); ?>
                           <br>
                           <small><?php $lh->translateText("settings_for_created_group"); ?></small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-3 control-label" for="ingroup_voicemail"><?php $lh->translateText("voicemail"); ?></label>
								<div class="col-sm-9 mb">
									<select name="ingroup_voicemail" id="ingroup_voicemail" class="form-control select2-1" style="width:100%;">
										<?php
											if($voicemails == NULL){
										?>
											<option value="" selected><?php $lh->translateText('no_voicemail_available'); ?></option>
										<?php
											}else{
										?>
											<option value="" selected><?php $lh->translateText('-none-'); ?></option>
										<?php
											for($i=0;$i<count($voicemails->voicemail_id);$i++){
										?>
												<option value="<?php echo $voicemails->voicemail_id[$i];?>">
													<?php echo $voicemails->voicemail_id[$i].' - '.$voicemails->fullname[$i];?>
												</option>									
										<?php
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="next_agent_call"><?php $lh->translateText("next_agent_call"); ?></label>
								<div class="col-sm-9 mb">
									<select name="next_agent_call" id="next_agent_call" class="form-control">
											<option value="random"> Random </option>
											<option value="oldest_call_start"> Oldest Call Start </option>
											<option value="oldest_call_finish"> Oldest Call Finish </option>
											<option value="overall_user_level"> Overall User Lever </option>
											<option value="inbound_group_rank"> Inbound Group Rank </option>
											<option value="campaign_rank"> Campaign Rank </option>
											<option value="fewest_calls"> Fewest Calls </option>
											<option value="fewest_calls_campaign"> Fewest Calls Campaign </option>
											<option value="longest_wait_time"> Longest Wait Time </option>
											<option value="ring_all"> Ring All </option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="display"><?php $lh->translateText("fronter_display"); ?></label>
								<div class="col-sm-9 mb">
									<select name="display" id="display" class="form-control">
										<option value="N" selected><?php $lh->translateText("go_no"); ?></option>
										<option value="Y"><?php $lh->translateText("go_yes"); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="script"><?php $lh->translateText("script"); ?></label>
								<div class="col-sm-9 mb">
									<select name="script" id="script" class="form-control select2-1" style="width:100%;">
										<option value="NONE"><?php $lh->translateText("-none-"); ?></option>
										<?php
											for($i=0;$i<count($scripts->script_id);$i++){
										?>
											<option value="<?php echo $scripts->script_id[$i];?>">
												<?php echo $scripts->script_id[$i].' - '.$scripts->script_name[$i];?>
											</option>									
										<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="call_launch"><?php $lh->translateText("get_call_launch"); ?></label>
								<div class="col-sm-9 mb">
									<select name="call_launch" id="call_launch" class="form-control">
											<option value="NONE"> NONE </option>
											<option value="SCRIPT"> SCRIPT </option>
											<option value="WEBFORM"> WEBFORM </option>
											<option value="WEBFORMTWO"> WEBFORMTWO </option>
											<option value="FORM"> FORM </option>
											<option value="EMAIL"> EMAIL </option>
									</select>
								</div>
							</div>
						</fieldset>
					</div><!-- end of step -->
				
				</form>

				</div> <!-- end of modal body -->
			</div>
		</div>
	</div><!-- end of modal -->
	
	<!-- ADD IVR MODAL -->
		<div class="modal fade" id="add_ivr" aria-labelledby="ivr_modal" >
        <div class="modal-dialog modal-lg" role="document" style="height:90%;">
            <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="ivr_modal">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create IVR."></i> 
						<b><?php $lh->translateText("call_menu_wizard"); ?> » <?php $lh->translateText("create_new_call_menu"); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body wizard-content">
				
				<form action="" method="POST" id="create_ivr" role="form">
					<div class="row">
					<h4><?php $lh->translateText("call_menu_details"); ?>
					   <br>
					   <small><?php $lh->translateText("enter_call_menu_details"); ?></small>
					</h4>
					<fieldset>
						<div class="form-group mt">
							<label class="col-sm-3 control-label" for="menu_id"><?php $lh->translateText("menu_id"); ?>:</label>
							<div class="col-sm-8 mb">
								<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="<?php $lh->translateText("menu_id"); ?>" minlength="4" title="Minimum of 4 characters" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="menu_name"><?php $lh->translateText("menu_name"); ?></label>
							<div class="col-sm-8 mb">
								<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="<?php $lh->translateText("menu_name"); ?>" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="menu_prompt"><?php $lh->translateText("menu_greeting"); ?></label>
							<div class="col-sm-8 mb">
								<select name="menu_prompt" id="menu_prompt" class="form-control select2-1" style="width:100%;">
									<option value="goWelcomeIVR" selected><?php $lh->translateText("default_value"); ?></option>
									<?php
										for($i=0;$i<count($voicefiles->file_name);$i++){
											$file = substr($voicefiles->file_name[$i], 0, -4);
									?>
										<option value="<?php echo $file;?>"><?php echo $file;?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="menu_timeout"><?php $lh->translateText("menu_timeout"); ?></label>
							<div class="col-sm-8 mb">
								<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="10" min="0" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="menu_timeout_prompt"><?php $lh->translateText("timeout_greeting"); ?></label>
							<div class="col-sm-8 mb">
								<select name="menu_timeout_prompt" id="menu_timeout_prompt" class="form-control select2-1" style="width:100%;">
									<option value="" selected><?php $lh->translateText("default_value"); ?></option>
									<?php
										for($i=0;$i<count($voicefiles->file_name);$i++){
											$file = substr($voicefiles->file_name[$i], 0, -4);
									?>
										<option value="<?php echo $file;?>"><?php echo $file;?></option>
									<?php
										}
									?>				
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="menu_invalid_prompt"><?php $lh->translateText("invalid_greeting"); ?></label>
							<div class="col-sm-8 mb">
								<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control select2-1" style="width:100%;">
									<option value="" selected><?php $lh->translateText("default_value"); ?></option>
									<?php
										for($i=0;$i<count($voicefiles->file_name);$i++){
											$file = substr($voicefiles->file_name[$i], 0, -4);
									?>
										<option value="<?php echo $file;?>"><?php echo $file;?></option>
									<?php
										}
									?>				
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="menu_repeat"><?php $lh->translateText("menu_repeat"); ?></label>
							<div class="col-sm-8 mb">
								<input type="number" name="menu_repeat" id="menu_repeat" class="form-control" value="2" min="0" required>
							</div>
						</div>
						
						<div class="form-group" style="display:none;">
							<label class="col-sm-3 control-label" for="menu_time_check"><?php $lh->translateText("menu_time_check"); ?></label>
							<div class="col-sm-8 mb">
								<select name="menu_time_check" id="menu_time_check" class="form-control">
									<option value="0" ><?php $lh->translateText("go_no"); ?> </option>
									<option value="1" > <?php $lh->translateText("go_yes"); ?> </option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="call_time_id"><?php $lh->translateText("call_time"); ?>: </label>
							<div class="col-sm-8 mb">
								<select name="call_time_id" id="call_time_id" class="form-control select2-1" style="width:100%;">
									<?php
										for($x=0; $x<count($calltimes->call_time_id);$x++){
									?>
											<option value="<?php echo $calltimes->call_time_id[$x];?>"> <?php echo $calltimes->call_time_id[$x].' - '.$calltimes->call_time_name[$x]; ?> </option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group" style="display:none;">
							<label class="col-sm-3 control-label" for="track_in_vdac"><?php $lh->translateText("track_call_realtime_report"); ?>: </label>
							<div class="col-sm-8 mb"> 
								<select name="track_in_vdac" id="track_in_vdac" class="form-control">
									<option value="0" >0 - No Realtime Tracking</option>
									<option value="1" selected>1 - Realtime Tracking</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="tracking_group"><?php $lh->translateText("tracking_group"); ?></label>
							<div class="col-sm-8 mb">
								<select name="tracking_group" id="tracking_group" class="form-control select2-1" style="width:100%;">
									<option value="CALLMENU"><?php $lh->translateText("callmenu_default"); ?></option>
								<?php
									for($i=0;$i<count($ingroup->group_id);$i++){
								?>
									<option value="<?php echo $ingroup->group_id[$i];?>">
										<?php echo $ingroup->group_id[$i].' - '.$ingroup->group_name[$i];?>
									</option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="user_groups"><?php $lh->translateText("user_groups"); ?></label>
							<div class="col-sm-8 mb">
								<select name="user_groups" id="user_groups" class="form-control select2-1" style="width:100%;">
								<?php
								if (strtoupper($_SESSION['usergroup']) === 'ADMIN') {
								?>
									<option value="---ALL---"><?php $lh->translateText("all_usergroups"); ?></option>
								<?php
								}
								for($i=0;$i<count($user_groups->user_group);$i++){
									if (strtoupper($_SESSION['usergroup']) !== 'ADMIN' && strtoupper($user_groups->user_group[$i]) !== strtoupper($_SESSION['usergroup'])) {
										continue;
									}
								?>
									<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->group_name[$i];?>  </option>
								<?php
								}
								?>		
								</select>
							</div>
						</div>
					</fieldset>
					
					<!-- STEP 2 -->
					<h4><?php $lh->translateText("call_menu_entry"); ?>
					   <br>
					   <small><?php $lh->translateText("set_default_call_menu_entry"); ?></small>
					</h4>
					<fieldset>
						<div class="form-group">
							<div class="col-lg-4"><hr/></div>
							<div class="col-lg-4 mt mb">
								<center><strong><?php $lh->translateText("add_new_call_options"); ?></strong></center>
							</div>
							<div class="col-lg-4"><hr/></div>
						</div>
						<div id="static_div">
							<?php
								for($i=0;$i < 10; $i++){
							?>
							<div class="option_div_<?php echo $i;?>">
								<div class="form-group">
									<div class="col-lg-12">
										<div class="col-lg-2">
											<?php $lh->translateText("option"); ?>:
											<select class="form-control route_option" name="option[]">
												<option selected></option>
												<?php
													for($x=0; $x <= 9; $x++){
														echo '<option value="'.$x.'">'.$x.'</option>';
													}
												?>
												<option value="A">#</option>
												<option value="B">*</option>
												<option value="C"><?php $lh->translateText("timecheck"); ?></option>
												<option value="D" readonly style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"><?php $lh->translateText("timeout"); ?></option>
												<option value="E"><?php $lh->translateText("invalid"); ?></option>
											</select>
										</div>
										<div class="col-lg-7">
											<?php $lh->translateText("description"); ?>: 
											<input type="text" name="route_desc[]" class="form-control route_desc_<?php echo $i;?>" placeholder="<?php $lh->translateText("description"); ?>"/>
										</div>
										<div class="col-lg-3">
											<?php $lh->translateText("route"); ?>:
											<select class="form-control route_menu_<?php echo $i;?>" name="route_menu[]">
												<option selected value=""></option>
												<option value="CALLMENU"><?php $lh->translateText("call_menu_ivr"); ?></option>
												<option value="INGROUP"><?php $lh->translateText("ingroup"); ?></option>
												<option value="DID"><?php $lh->translateText("did"); ?></option>
												<option value="HANGUP"><?php $lh->translateText("hangup"); ?></option>
												<option value="EXTENSION"><?php $lh->translateText("custom_extensions"); ?></option>
												<option value="PHONE"><?php $lh->translateText("phone"); ?></option>
												<option value="VOICEMAIL"><?php $lh->translateText("voicemail"); ?></option>
												<option value="AGI"><?php $lh->translateText("agi"); ?></option>
											</select>
										</div>
										<div class="col-lg-1 btn-remove"></div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-12 option_menu_<?php echo $i;?> mb mt">
										<!-- CALL MENU -->
											<div class="route_callmenu_<?php echo $i;?>" style="display:none;">
												<label class="col-sm-3 control-label"><?php $lh->translateText("call_menu"); ?>: </label>
												<div class="col-sm-6">
													<select class="select2-2 form-control" name="option_callmenu_value[]" style="width:100%;">
														<option value="" selected><?php $lh->translateText("-none-"); ?></option>
													<?php
														for($x=0;$x < count($ivr->menu_id);$x++){
															echo "<option value=".$ivr->menu_id[$x].">".$ivr->menu_id[$x]." - ".$ivr->menu_name[$x]."</option>";
														}
													?>
													</select>
												</div>
											</div>
										<!-- IN GROUP -->
											<div class="route_ingroup_<?php echo $i;?>" style="display:none;">
												<div class="row mb">
													<label class="col-sm-3 control-label"><?php $lh->translateText("ingroup"); ?>: </label>
													<div class="col-sm-6">
														<select class="select2-2 form-control" name="option_ingroup_value[]" style="width:100%;">
															<option value="" selected><?php $lh->translateText("-none-"); ?></option>
														<?php
															for($x=0;$x < count($ingroup->group_id);$x++){
																echo "<option value=".$ingroup->group_id[$x].">".$ingroup->group_id[$x]." - ".$ingroup->group_name[$x]."</option>";
															}
														?>
														</select>
													</div>
												</div>
												<div class="col-sm-11">
													<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
														<label class="col-sm-3 control-label"><?php $lh->translateText("handle_method"); ?>:</label>
														<div class="col-sm-7">
															<select class="form-control" name="handle_method_<?php echo $i;?>" id="edit_handle_method_<?php echo $i;?>">
																<option value="CID">CID</option>
																<option value="CIDLOOKUP" selected="selected">CIDLOOKUP</option>
																<option value="CIDLOOKUPRL">CIDLOOKUPRL</option>
																<option value="CIDLOOKUPRC">CIDLOOKUPRC</option>
																<option value="ANI">ANI</option>
																<option value="ANILOOKUP">ANILOOKUP</option>
																<option value="ANILOOKUPRL">ANILOOKUPRL</option>
																<option value="VIDPROMPT">VIDPROMPT</option>
																<option value="VIDPROMPTLOOKUP">VIDPROMPTLOOKUP</option>
																<option value="VIDPROMPTLOOKUPRL">VIDPROMPTLOOKUPRL</option>
																<option value="VIDPROMPTLOOKUPRC">VIDPROMPTLOOKUPRC</option>
																<option value="CLOSER">CLOSER</option>
																<option value="3DIGITID">3DIGITID</option>
																<option value="4DIGITID">4DIGITID</option>
																<option value="5DIGITID">5DIGITID</option>
																<option value="10DIGITID">10DIGITID</option>
															</select>
														</div>
													</div>
													<div class="row mb">
														<div class="col-sm-7">
															<label class="col-sm-4 control-label"><?php $lh->translateText("campaign_id"); ?>: </label>
															<div class="col-sm-8">
																<select class="form-control" name="campaign_id_<?php echo $i;?>" style="width:100%;">
																<?php
																echo '<option value="">'.$lh->translationFor("-none-").'</option>';
																	for($x=0;$x < count($campaign->campaign_id);$x++){
																		echo "<option value=".$campaign->campaign_id[$x].">".$campaign->campaign_id[$x]." - ".$campaign->campaign_name[$x]."</option>";
																	}
																?>
																</select>
															</div>
														</div>
														<div class="col-sm-5 ingroup_advanced_settings_<?php echo $i;?>">
															<label class="col-sm-5 control-label"><?php $lh->translateText("phone_code"); ?>: </label>
															<div class="col-sm-7">
																<input type="text" class="form-control" name="phone_code<?php echo $i;?>" value="1" id="edit_phone_code_<?php echo $i;?>" maxlength="14" size="4">
															</div>
														</div>
													</div>
													<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
														<div class="col-sm-7">
															<label class="col-sm-4 control-label"><?php $lh->translateText("search_method"); ?>:</label>
															<div class="col-sm-8">
																<select class="form-control" name="search_method_<?php echo $i;?>" id="edit_search_method_<?php echo $i;?>">
																	<option value="LB" selected="selected"><?php $lh->translateText("lb_load_balance"); ?></option>
																	<option value="LO"><?php $lh->translateText("lo_load_balance_overflow"); ?></option>
																	<option value="SO"><?php $lh->translateText("server_only"); ?></option>
																</select>
															</div>
														</div>
														<div class="col-sm-5">
															<label class="col-sm-5 control-label" for="search_method_list_id"><?php $lh->translateText("list_id"); ?>: </label>
															<div class="col-sm-7">
																<input type="text" name="list_id_<?php echo $i;?>" value="998" id="edit_list_id_<?php echo $i;?>" class="form-control" maxlength="14" size="8">
															</div>
														</div>
													</div>
													<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
														<label class="col-sm-3 control-label"><?php $lh->translateText("vid_digits"); ?>: </label>
														<div class="col-sm-7">
															<input type="text" class="form-control" name="vid_digits_<?php echo $i;?>" value="1" id="edit_validate_digits_<?php echo $i;?>" maxlength="3" size="3">
														</div>
													</div>
													<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
														<label class="col-sm-4 control-label"><?php $lh->translateText("vid_enter_filename"); ?>: </label>
														<div class="col-sm-8">
															<div class="col-sm-6">
																<input type="text" name="enter_filename_<?php echo $i;?>" value="sip-silence" id="edit_enter_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
															</div>
															<div class="col-sm-6">
																<select class="col-sm-6 form-control" style="width:100%;" id="enter_filename_select_<?php echo $i;?>">
																	<option value="sip-silence" selected> <?php $lh->translateText("default_value"); ?> </option>
																<?php
																	for($x=0;$x<count($voicefiles->file_name);$x++){
																		$file = substr($voicefiles->file_name[$x], 0, -4);
																		echo "<option value=".$file.">".$file."</option>";
																	}
																?>
																</select>
															</div>
														</div>
													</div>
													<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
														<label class="col-sm-4 control-label"><?php $lh->translateText("vid_id_number_filename"); ?>: </label>
														<div class="col-sm-8">
															<div class="col-sm-6">
																<input type="text" name="id_number_filename_<?php echo $i;?>" value="sip-silence" id="edit_id_number_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
															</div>
															<div class="col-sm-6">
																<select class="col-sm-6 form-control" style="width:100%;" id="edit_id_number_filename_select_<?php echo $i;?>">
																	<option value="sip-silence" selected> <?php $lh->translateText("default_value"); ?> </option>
																<?php
																	for($x=0;$x<count($voicefiles->file_name);$x++){
																		$file = substr($voicefiles->file_name[$x], 0, -4);
																		echo "<option value=".$file.">".$file."</option>";
																	}
																?>
																</select>
															</div>
														</div>
													</div>
													<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
														<label class="col-sm-4 control-label"><?php $lh->translateText("vid_confirm_filename"); ?>: </label>
														<div class="col-sm-8">
															<div class="col-sm-6">
																<input type="text" name="confirm_filename_<?php echo $i;?>" value="sip-silence" id="edit_confirm_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
															</div>
															<div class="col-sm-6">
																<select class="col-sm-6 form-control" style="width:100%;" id="edit_confirm_filename_select_<?php echo $i;?>">
																	<option value="sip-silence" selected> <?php $lh->translateText("default_value"); ?> </option>
																<?php
																	for($x=0;$x<count($voicefiles->file_name);$x++){
																		$file = substr($voicefiles->file_name[$x], 0, -4);
																		echo "<option value=".$file.">".$file."</option>";
																	}
																?>
																</select>
															</div>
														</div>
													</div>
												</div>
											</div>
										<!-- DID -->
											<div class="route_did_<?php echo $i;?>" style="display:none;">
												<label class="col-sm-3 control-label"><?php $lh->translateText("did"); ?>: </label>
												<div class="col-sm-6">
													<select class="col-sm-6 select2-2 form-control" name="option_did_value[]" style="width:100%;">
														<option value="" selected> <?php $lh->translateText("-none-"); ?> </option>
													<?php
														for($x=0;$x < count($phonenumber->did_pattern);$x++){
															echo "<option value=".$phonenumber->did_pattern[$x].">".$phonenumber->did_pattern[$x]." - ".$phonenumber->did_description[$x]."</option>";
														}
													?>
													</select>
												</div>
											</div>
										<!-- HANGUP -->
											<div class="route_hangup_<?php echo $i;?>" style="display:none;">
												<label class="col-sm-3 control-label"><?php $lh->translateText("audio_file"); ?>: </label>
												<div class="col-sm-6">
													<select class="select2-2 form-control" name="option_hangup_value[]" style="width:100%;">
														<option value="vm-goodbye" selected><?php $lh->translateText("default_value"); ?></option>
													<?php
														for($x=0;$x<count($voicefiles->file_name);$x++){
															$file = substr($voicefiles->file_name[$x], 0, -4);
															echo "<option value=".$file.">".$file."</option>";
														}
													?>
													</select>
												</div>
											</div>
										<!-- EXTENSION -->
											<div class="route_exten_<?php echo $i;?>" style="display:none;">
												<div class="col-sm-6">
													<label class="col-sm-3 control-label"><?php $lh->translateText("extension"); ?>: </label>
													<div class="col-sm-9">
														<input type="text" class="form-control" name="option_extension_value[]" value="" id="option_route_value_<?php echo $i;?>" />
													</div>
												</div>
												<div class="col-sm-6">
													<label class="col-sm-3 control-label"><?php $lh->translateText("context"); ?>: </label>
													<div class="col-sm-9">
														<input type="text" class="form-control" name="option_route_value_context[]" value="" id="option_route_value_context_<?php echo $i;?>" />
													</div>
												</div>
											</div>
										<!-- PHONE -->
											<div class="route_phone_<?php echo $i;?>" style="display:none;">
												<label class="col-sm-3 control-label"><?php $lh->translateText("phone"); ?>: </label>
												<div class="col-sm-6">
													<select class="select2-2 form-control" name="option_phone_value[]" style="width:100%;">
														<option value="" selected> <?php $lh->translateText("-none-"); ?> </option>
													<?php
														for($x=0;$x < count($phones->extension);$x++){
															echo "<option value=".$phones->extension[$x].">".$phones->extension[$x]." - ".$phones->server_ip[$x]." - ".$phones->dialplan_number[$x]."</option>";
														}
													?>
													</select>
												</div>
											</div>
										<!-- VOICEMAIL -->
											<div class="route_voicemail_<?php echo $i;?>" style="display:none;">
												<label class="col-sm-3 control-label"><?php $lh->translateText("voicemail_box"); ?>: </label>
												<div class="col-sm-9">
													<div class="col-sm-6">
														<input type="text" name="option_voicemail_value[]" value="" class="form-control" id="option_voicemail_input_<?php echo $i;?>" maxlength="255" size="15">
													</div>
													<div class="col-sm-6">
														<select class="col-sm-6 select2-2 form-control" style="width:100%;" id="option_voicemail_select_<?php echo $i;?>">
															<option value="" selected> <?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($x=0;$x < count($voicemails->voicemail_id);$x++){
																echo "<option value=".$voicemails->voicemail_id[$x].">".$voicemails->voicemail_id[$x]." - ".$voicemails->fullname[$x]."</option>";
															}
														?>
														</select>
													</div>
												</div>
											</div>
										<!-- AGI -->
											<div class="route_agi_<?php echo $i;?>" style="display:none;">
												<label class="col-sm-3 control-label"><?php $lh->translateText("agi"); ?>: </label>
												<div class="col-sm-6">
													<input type="text" class="form-control" name="option_agi_value[]" value="" maxlength="255" size="50">
												</div>
											</div>
									</div>
								</div>
							</div>
						<?php
							}
						?>
						</div><!--static div -->
					</fieldset>
					</div><!-- End of Step -->
				</form>
				</div> <!-- end of modal body -->
				
			</div>
		</div>
	</div><!-- end of modal -->

	
	<!-- ADD DID MODAL -->
	<div class="modal fade" id="add_phonenumbers" aria-labelledby="did_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="did_modal">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create DID/TFN."></i> 
						<b><?php $lh->translateText('did_wizard'); ?> » <?php $lh->translateText('create_new_did'); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body wizard-content">
				
				<form action="AddDID.php" method="POST" id="create_phonenumber" role="form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
					<div class="row">
						<!-- STEP 1 -->
						<h4><?php $lh->translateText('did_details'); ?>
                           <br>
                           <small><?php $lh->translateText('did_basic_details'); ?></small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-4 control-label" for="did_exten"><?php $lh->translateText('did_extension'); ?></label>
								<div class="col-sm-8 mb">
									<input type="text" name="did_exten" id="did_exten" class="form-control" placeholder="<?php $lh->translateText('did_extension'); ?>" maxlength="20" minlength="2" required title="Must be 2-20 characters in length." />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="desc"><?php $lh->translateText('did_description'); ?></label>
								<div class="col-sm-8 mb">
									<input type="text" name="desc" id="desc" class="form-control" placeholder="<?php $lh->translateText('did_description'); ?>" maxlength="20" minlength="2" title="Must be  2-20 characters in length"  required />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="active"><?php $lh->translateText('active'); ?></label>
								<div class="col-sm-8 mb">
									<select name="active" id="active" class="form-control">
										<option value="Y" selected><?php $lh->translateText("go_yes"); ?></option>
										<option value="N"><?php $lh->translateText("go_no"); ?></option>
									</select>
								</div>
							</div>
							<!--
							<div class="form-group">
								<label class="col-sm-4 control-label" for="route" ><?php $lh->translateText('did_route'); ?></label>
								<div class="col-sm-8 mb">
									<select class="form-control" id="route" name="route">
										<option value="AGENT"><?php $lh->translateText('agent'); ?>  </option>
										<option value="IN_GROUP"> <?php $lh->translateText('in_group'); ?> </option>
										<option value="PHONE"><?php $lh->translateText('phone'); ?>  </option>
										<option value="CALLMENU"><?php $lh->translateText('call_menu_ivr'); ?> </option>
										<option value="VOICEMAIL"><?php $lh->translateText('voicemail'); ?>  </option>
										<option value="EXTEN"><?php $lh->translateText('custom_extension'); ?> </option>
									</select>
								</div>
							</div>
							-->
							<div class="form-group">
								<label class="col-sm-4 control-label" for="user_groups"><?php $lh->translateText('user_groups'); ?></label>
								<div class="col-sm-8 mb">
									<select name="user_groups" id="user_groups" class="form-control select2-1" style="width:100%;">
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
												if (strtoupper($_SESSION['usergroup']) !== 'ADMIN' && strtoupper($user_groups->user_group[$i]) !== strtoupper($_SESSION['usergroup'])) {
													continue;
												}
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
						</fieldset>
						<?php /*?>
						<h4><?php $lh->translateText('route_settings'); ?>
                        			   <br>
			                           <small><?php $lh->translateText('fill_up_route'); ?></small>
			                        </h4>
			                        <fieldset>
						<!-- IF DID ROUTE = AGENT-->

							<div id="form_route_agent">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_agentid"><?php $lh->translateText('agent_id'); ?></label>
									<div class="col-sm-8 mb">
										<select name="route_agentid" id="route_agentid" class="form-control select2-1" style="width:100%;">
											<option value="" > <?php $lh->translateText("-none-"); ?></option>
											<?php
												for($i=0;$i<count($users->user);$i++){
											?>
												<option value="<?php echo $users->user[$i];?>">
													<?php echo $users->user[$i].' - '.$users->full_name[$i];?>
												</option>									
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_unavail"><?php $lh->translateText('agent_unavailable_action'); ?></label>
									<div class="col-sm-8 mb">
										<select name="route_unavail" id="route_unavail" class="form-control">
											<option value="VOICEMAIL" ><?php $lh->translateText('voicemail'); ?>  </option>
											<option value="PHONE" > <?php $lh->translateText('phone'); ?> </option>
											<option value="IN_GROUP" > <?php $lh->translateText('in_group'); ?> </option>
											<option value="EXTEN" > <?php $lh->translateText('custom_extension'); ?> </option>
										</select>
									</div>
								</div>
							</div><!-- end of div agent-->
							
						<!-- IF DID ROUTE = IN-GROUP-->
						
							<div id="form_route_ingroup" style="display: none;">
								<label class="col-sm-4 control-label" for="route_ingroupid"><?php $lh->translateText('agent_unavailable_action'); ?><?php $lh->translateText('ingroup_id'); ?></label>
								<div class="col-sm-8 mb">
									<select name="route_ingroupid" id="route_ingroupid" class="form-control select2-1" style="width:100%;">
										<?php
											for($i=0;$i<count($ingroup->group_id);$i++){
										?>
											<option value="<?php echo $ingroup->group_id[$i];?>">
												<?php echo $ingroup->group_id[$i].' - '.$ingroup->group_name[$i];?>
											</option>									
										<?php
											}
										?>
									</select>
								</div>
							</div><!-- end of ingroup div -->
							
						<!-- IF DID ROUTE = PHONE -->

							<div id="form_route_phone" style="display: none;">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_phone_exten"><?php $lh->translateText('phone_extension'); ?></label>
									<div class="col-sm-8 mb">
										<select name="route_phone_exten" id="route_phone_exten" class="form-control select2-1" style="width:100%;">
											<?php
												for($i=0;$i<count($phones->extension);$i++){
											?>
												<option value="<?php echo $phones->extension[$i];?>">
													<?php echo $phones->extension[$i].' - '.$phones->server_ip[$i].' - '.$phones->dialplan_number[$i];?>
												</option>									
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_phone_server"><?php $lh->translateText('server_ip'); ?></label>
									<div class="col-sm-8 mb">
										<select name="route_phone_server" id="route_phone_server" class="form-control select2-1" style="width:100%;">
											<option value="" ><?php $lh->translateText("-none-"); ?></option>
											<?php
												for($i=0;$i < 1;$i++){
											?>
												<option value="<?php echo $phones->server_ip[$i];?>">
													<?php echo 'GOautodial - '.$phones->server_ip[$i];?>
												</option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
							</div><!-- end of phone div -->
							
						<!-- IF DID ROUTE = IVR -->

							<div id="form_route_callmenu" style="display: none;">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_ivr"><?php $lh->translateText('call_menu'); ?></label>
									<div class="col-sm-8 mb">
										<select name="route_ivr" id="route_ivr" class="form-control select2-1" style="width:100%;">
											<?php
											if(count($ivr->menu_id) > 0){
												for($i=0;$i<count($ivr->menu_id);$i++){
											?>
												<option value="<?php echo $ivr->menu_id[$i];?>">
													<?php echo $ivr->menu_id[$i].' - '.$ivr->menu_name[$i];?>
												</option>									
											<?php
												}
											}else{
											?>
												<option value=""><?php $lh->translateText('no_available_call_menus'); ?> </option>
											<?php
											}
											?>
										</select>
									</div>
								</div>
							</div><!-- end of ivr div -->
							
						<!-- IF DID ROUTE = VoiceMail -->

							<div id="form_route_voicemail" style="display: none;">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_voicemail"><?php $lh->translateText('voicemail_box'); ?></label>
									<div class="col-sm-8 mb">
										<select name="route_voicemail" id="route_voicemail" class="form-control select2-1" style="width:100%;">
											
											<?php
												for($i=0;$i<count($voicemails->voicemail_id);$i++){
											?>
												<option value="<?php echo $voicemails->voicemail_id[$i];?>">
													<?php echo $voicemails->voicemail_id[$i].' - '.$voicemails->fullname[$i];?>
												</option>									
											<?php
												}
											?>
											
										</select>
									</div>
								</div>
							</div><!-- end of voicemail div -->
							
							<!-- IF DID ROUTE = Custom Extension -->

							<div id="form_route_exten" style="display: none;">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_exten"><?php $lh->translateText('custom_extension'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" name="route_exten" id="route_exten" placeholder="<?php $lh->translateText('custom_extension'); ?>" class="form-control" required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_exten_context"><?php $lh->translateText('extension_content'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" name="route_exten_context" id="route_exten_context" placeholder="<?php $lh->translateText('extension_content'); ?>" class="form-control" required>
									</div>
								</div>
							</div><!-- end of custom extension div -->
						</fieldset>
						<?php */?>
					</div><!-- End of Step -->
				
				

				</div> <!-- end of modal body -->
				</form>
			</div>
		</div>
	</div><!-- end of modal -->

<!-- END OF TELEPHONY INBOUND MODALS -->

		<?php print $ui->standardizedThemeJS(); ?>
        <!-- JQUERY STEPS-->
  		<script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	    

 <script type="text/javascript">
	$(document).ready(function() {
		if (window.location.href.indexOf("T_ingroup") > -1) {
			$(".T_ingroup").addClass("active");
			$(".T_ivr").removeClass("active");
			$(".T_phonenumber").removeClass("active");					
		}
		
		if (window.location.href.indexOf("T_ivr") > -1) {
			$(".T_ivr").addClass("active");
			$(".T_ingroup").removeClass("active");
			$(".T_phonenumber").removeClass("active");					
		}		
		
		if (window.location.href.indexOf("T_phonenumber") > -1) {
			$(".T_phonenumber").addClass("active");
			$(".T_ingroup").removeClass("active");
			$(".T_ivr").removeClass("active");					
		}		
		/*******************
		** INITIALIZATIONS
		*******************/
			// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			//loads datatable functions
				$('#table_ingroup').DataTable({
					destroy:true, 
					responsive:true,
					stateSave:true,
					drawCallback:function(settings) {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					},
					order: [[ 1, "asc"]],
					columnDefs:[
						{ width: "16%", targets: 6 },
						{ searchable: false, targets: [ 0, 6 ] },
						{ sortable: false, targets: [ 0, 6 ] },
						{ targets: -1, className: "dt-body-right" }
					]
				});
				
				$('#table_ivr').DataTable({
					destroy:true, 
					responsive:true,
					stateSave:true,
					drawCallback:function(settings) {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					},
					order: [[ 1, "asc"]],
					columnDefs:[
						{ width: "16%", targets: 5 },
						{ searchable: false, targets: [ 0, 5 ] },
						{ sortable: false, targets: [ 0, 5 ] },
						{ targets: -1, className: "dt-body-right" }
					]
				});
				$('#table_did').DataTable({
					destroy:true, 
					responsive:true,
					stateSave:true,
					drawCallback:function(settings) {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					},
					order: [[ 1, "asc"]],
					columnDefs:[
						{ width: "16%", targets: 5 },
						{ searchable: false, targets: [ 0, 5 ] },
						{ sortable: false, targets: [ 0, 5 ] },
						{ targets: -1, className: "dt-body-right" }
					]
				});

			//reloads page when modal closes
			/*
				$('#add_ingroups').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_ivr').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_phonenumbers').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});
			*/
			//-----------
		
		/*******************
		** INBOUND EVENTS
		*******************/

			/*********
			** INIT WIZARD
			*********/
				var ingroup_form = $("#create_ingroup"); // init form wizard 

			    ingroup_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    ingroup_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", ingroup_form).remove();
					        $(".body:eq(" + newIndex + ") .error", ingroup_form).removeClass("error");
					    }

			            ingroup_form.validate().settings.ignore = ":disabled,:hidden";
			            return ingroup_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            ingroup_form.validate().settings.ignore = ":disabled,:hidden";
			            return ingroup_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("<?php $lh->translateText("loading"); ?>");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            // Submit form via ajax
					            $.ajax({
									url: "./php/AddIngroup.php",
									type: 'POST',
									data: $("#create_ingroup").serialize(),
									success: function(data) {
										console.log(data);
										if (data == 1) {
											swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("add_ingroup_success"); ?>", "success");
											window.setTimeout(function(){window.location.href = 'telephonyinbound.php?T_ingroup';},1000);

											$('#finish').text("<?php $lh->translateText("submit"); ?>");
											$('#finish').attr("disabled", false);
										} else {
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");

											$('#finish').text("<?php $lh->translateText("submit"); ?>");
											$('#finish').attr("disabled", false);
										}
									}
								});
			        }
			    }); // end of wizard
			
			/*********
			** EDIT INGROUP
			*********/
				$(document).on("click",".edit-ingroup",function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="groupid" value="' + $(this).attr('data-id') + '" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE INGROUP
			*********/
				$(document).on('click','.delete-ingroup',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "<?php $lh->translateText("are_you_sure"); ?>",   
	                	text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "<?php $lh->translateText("confirm_delete_inbound"); ?>",   
	                	cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteInbound.php",
									type: 'POST',
									data: { 
										groupid: id
									},
									success: function(data) {
									console.log(data);
								  		if (data == 1){
											swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("inbound_delete_success"); ?>",type: "success"},function(){window.location.href = 'telephonyinbound.php?T_ingroup';});
										} else {
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
										}
									}
								});
							} else {     
		                			swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");   
		                	} 
	                	}
	                );
				});
		
		//-------------------- end of main ingroup events

		/*******************
		** IVR EVENTS
		*******************/

			/*********
			** INIT WIZARD
			*********/
				var ivr_form = $("#create_ivr"); // init form wizard 
				
			    ivr_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    ivr_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", ivr_form).remove();
					        $(".body:eq(" + newIndex + ") .error", ivr_form).removeClass("error");
					    }
						
						$("#create_ivr").find( ".content.clearfix" ).css( "height", "75%" );
						
			            ivr_form.validate().settings.ignore = ":disabled,:hidden";
			            return ivr_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            ivr_form.validate().settings.ignore = ":hidden";
			            return ivr_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {
					$('select option').prop('disabled', false);
					
			        	$('#finish').text("<?php $lh->translateText("loading"); ?>");
			        	$('#finish').attr("disabled", true);

						// Submit form via ajax
						$.ajax({
							url: "./php/AddIVR.php",
							type: 'POST',
							data: $("#create_ivr").serialize(),
							success: function(data) {
								console.log(data);
								$('#finish').text("<?php $lh->translateText("submit"); ?>");
								$('#finish').attr("disabled", false);
								if (data == 1) {
									swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("add_ivr_success"); ?>",type: "success"},function(){window.location.href = 'telephonyinbound.php?T_ivr';});
								} else {
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
								}
							}
						});
							
			        }
			    }); // end of wizard
			
			
			/*********
			** EDIT IVR
			*********/
				$(document).on("click",".edit-ivr",function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="ivr" value="' + $(this).attr('data-id') + '" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE IVR
			*********/

				$(document).on('click','.delete-ivr',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "<?php $lh->translateText("are_you_sure"); ?>",   
	                	text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "<?php $lh->translateText("confirm_delete_ivr"); ?>",   
	                	cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteInbound.php",
									type: 'POST',
									data: { 
										ivr: id
									},
									success: function(data) {
									console.log(data);
								  		if (data == 1) {
											swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("ivr_delete_success"); ?>",type: "success"},function(){window.location.href = 'telephonyinbound.php?T_ivr';});
										} else {
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
										}
									}
								});
							} else {     
		                			swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");   
		                	} 
	                	}
	                );
				});
			
		//-------------------- end of main ivr events

		/*******************
		** DID EVENTS
		*******************/

			/*********
			** DID WIZARD
			*********/
				var did_form = $("#create_phonenumber"); // init form wizard 

			    did_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    did_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", did_form).remove();
					        $(".body:eq(" + newIndex + ") .error", did_form).removeClass("error");
					    }

			            did_form.validate().settings.ignore = ":disabled,:hidden";
			            return did_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            did_form.validate().settings.ignore = ":disabled,:hidden";
			            return did_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("<?php $lh->translateText("loading"); ?>");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            $.ajax({
								url: "./php/AddDID.php",
								type: 'POST',
								data: $("#create_phonenumber").serialize(),
								success: function(data) {
								   console.log(data);
								   $('#submit_did').val("<?php $lh->translateText("submit"); ?>");
											$('#submit_did').attr("disabled", false);
									  if(data == 1){
									  		swal({title: "<?php $lh->translateText('success'); ?>",text: "<?php $lh->translateText('add_phone_number_success'); ?>",type: "success"},function(){window.location.href = 'telephonyinbound.php?T_phonenumber';});
									  }else{
											sweetAlert("<?php $lh->translateText('oups'); ?>", "<?php $lh->translateText('something_went_wrong'); ?>"+data, "error");
									  }
								}
							});
							
			        }
			    }); // end of wizard
			
			//------------------------

			/*********
			** EDIT DID
			*********/
	
				$(document).on('click','.edit-phonenumber',function() {
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="did" value="' + $(this).attr('data-id') + '" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE DID
			*********/

				$(document).on('click','.delete-phonenumber',function() {
				 	var id = $(this).attr('data-id');
	                swal({
	                	title: "<?php $lh->translateText('are_you_sure'); ?>",   
	                	text: "<?php $lh->translateText('action_cannot_be_undone'); ?>",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "<?php $lh->translateText('confirm_delete_phonenumber'); ?>",   
	                	cancelButtonText: "<?php $lh->translateText('cancel_please'); ?>",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
					}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteInbound.php",
									type: 'POST',
									data: { 
										modify_did: id
									},
									
									success: function(data) {
									//console.log(modify_did);
									console.log(data);
								  		if (data == 1) {
											swal({title: "<?php $lh->translateText('success'); ?>",text: "<?php $lh->translateText('phonenumber_delete_success'); ?>",type: "success"},function(){window.location.href = 'telephonyinbound.php?T_phonenumber';});
										} else {
											sweetAlert("<?php $lh->translateText('oups'); ?>", "<?php $lh->translateText('something_went_wrong'); ?> "+data, "error");
										}
									}
								});
	                		} else {     
		                			swal("<?php $lh->translateText('cancelled'); ?>", "<?php $lh->translateText('cancel_msg'); ?>", "error");   
		                	} 
	                	}
	                );
				});
		
		//-------------------- end of main did events

		/*******************
		** OTHER TRIGGER EVENTS and FILTERS
		*******************/
			/* loads colorpicker */
    			$(".colorpicker").colorpicker();

    		/* initialize select2 */
				$('.select2-1').select2({ theme: 'bootstrap' });
				$.fn.select2.defaults.set( "theme", "bootstrap" );
				
			/*** INGROUP ***/
				// disable special characters on Ingroup ID
					$('#groupid').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});
				// disable special characters on Ingroup Name
					$('#groupname').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});

			/*** IVR ***/
				// disable special characters on Ingroup ID
					$('#menu_id').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});
					$('#menu_name').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});
					
				$(document).on('change', '.route_option',function(){
					//alert(this.value);
					var id = this.value;
					var old = $(this).attr('id');
					var object;
					if(typeof old != 'undefined'){
						$(this).attr('id', "option_"+id).attr('data-old', "option_"+old);
						
						object = "option_"+id;
					}else{
						$(this).attr('id', 'option_'+id);
						old = "option_";
					}
					
					showhide_option(object, id, old);
					
				});
				
				function showhide_option(object, id, old){
					//var getId = object.attr('id');
					var lastChar;
					var old_lastChar;
					
					if (typeof object != 'undefined')
						lastChar = object[object.length -1];
					
					if (typeof old != 'undefined')
						old_lastChar = old[old.length -1];
					
					if(old_lastChar != "_"){
						$(".route_option option[value="+old_lastChar+"]").attr("disabled", false).css({"background-color": "white", "color": "#3a3f51"});
					}else{
						$(".route_option option[value="+id+"]").attr("disabled", true).css({"background-color": "#c1c1c1", "color": "white"});
					}
					
				}
				
				<?php for($i=0;$i < 10; $i++){ ?>
				$(document).on('change', '.route_menu_<?php echo $i;?>',function(){
					if(this.value == "CALLMENU") {
						$('.route_callmenu_<?php echo $i;?>').show();
						$(".route_callmenu_<?php echo $i;?> :input").prop('required',true);
						
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
					
					}if(this.value == "INGROUP") {
						$('.route_ingroup_<?php echo $i;?>').show();
						$(".route_ingroup_<?php echo $i;?> :input").prop('required',true);

						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
						
					}if(this.value == "DID") {
						$('.route_did_<?php echo $i;?>').show();
						$(".route_did_<?php echo $i;?> :input").prop('required',true);

						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
						
					}if(this.value == "HANGUP") {
						$('.route_hangup_<?php echo $i;?>').show();
						$(".route_hangup_<?php echo $i;?> :input").prop('required',true);
						
						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
						
					}if(this.value == "EXTENSION") {
						$('.route_exten_<?php echo $i;?>').show();
						$(".route_exten_<?php echo $i;?> :input").prop('required',true);
						
						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
						
					}if(this.value == "PHONE") {
						$('.route_phone_<?php echo $i;?>').show();
						$(".route_phone_<?php echo $i;?> :input").prop('required',true);
						
						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
						
					}if(this.value == "VOICEMAIL") {
						$('.route_voicemail_<?php echo $i;?>').show();
						$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						
						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
						
					}if(this.value == "AGI") {
						$('.route_agi_<?php echo $i;?>').show();
						$(".route_agi_<?php echo $i;?> :input").prop('required',true);
						
						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
					}
					if(this.value == "") {
						$('.route_callmenu_<?php echo $i;?>').hide();
							$(".route_callmenu_<?php echo $i;?> :input").prop('required',false);
						$('.route_ingroup_<?php echo $i;?>').hide();
							$(".route_ingroup_<?php echo $i;?> :input").prop('required',false);
						$('.route_did_<?php echo $i;?>').hide();
							$(".route_did_<?php echo $i;?> :input").prop('required',false);
						$('.route_hangup_<?php echo $i;?>').hide();
							$(".route_hangup_<?php echo $i;?> :input").prop('required',false);
						$('.route_exten_<?php echo $i;?>').hide();
							$(".route_exten_<?php echo $i;?> :input").prop('required',false);
						$('.route_phone_<?php echo $i;?>').hide();
							$(".route_phone_<?php echo $i;?> :input").prop('required',false);
						$('.route_voicemail_<?php echo $i;?>').hide();
							$(".route_voicemail_<?php echo $i;?> :input").prop('required',false);
						$('.route_agi_<?php echo $i;?>').hide();
							$(".route_agi_<?php echo $i;?> :input").prop('required',false);
					}
				});
				<?php } ?>
				$(document).on('change', '.route_menu_A',function(){
					if(this.value == "CALLMENU") {
						$('.route_callmenu_A').show();
						$(".route_callmenu_A :input").prop('required',true);
						
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
					
					}if(this.value == "INGROUP") {
						$('.route_ingroup_A').show();
						$(".route_ingroup_A :input").prop('required',true);

						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
						
					}if(this.value == "DID") {
						$('.route_did_A').show();
						$(".route_did_A :input").prop('required',true);

						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
						
					}if(this.value == "HANGUP") {
						$('.route_hangup_A').show();
						$(".route_hangup_A :input").prop('required',true);
						
						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
						
					}if(this.value == "EXTENSION") {
						$('.route_exten_A').show();
						$(".route_exten_A :input").prop('required',true);
						
						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
						
					}if(this.value == "PHONE") {
						$('.route_phone_A').show();
						$(".route_phone_A :input").prop('required',true);
						
						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
						
					}if(this.value == "VOICEMAIL") {
						$('.route_voicemail_A').show();
						$(".route_voicemail_A :input").prop('required',true);
						
						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
						
					}if(this.value == "AGI") {
						$('.route_agi_A').show();
						$(".route_agi_A :input").prop('required',true);
						
						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
					}
					if(this.value == "") {
						$('.route_callmenu_A').hide();
							$(".route_callmenu_A :input").prop('required',false);
						$('.route_ingroup_A').hide();
							$(".route_ingroup_A :input").prop('required',false);
						$('.route_did_A').hide();
							$(".route_did_A :input").prop('required',false);
						$('.route_hangup_A').hide();
							$(".route_hangup_A :input").prop('required',false);
						$('.route_exten_A').hide();
							$(".route_exten_A :input").prop('required',false);
						$('.route_phone_A').hide();
							$(".route_phone_A :input").prop('required',false);
						$('.route_voicemail_A').hide();
							$(".route_voicemail_A :input").prop('required',false);
						$('.route_agi_A').hide();
							$(".route_agi_A :input").prop('required',false);
					}
				});
				$(document).on('change', '.route_menu_B',function(){
					if(this.value == "CALLMENU") {
						$('.route_callmenu_B').show();
						$(".route_callmenu_B :input").prop('required',true);
						
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
					
					}if(this.value == "INGROUP") {
						$('.route_ingroup_B').show();
						$(".route_ingroup_B :input").prop('required',true);

						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
						
					}if(this.value == "DID") {
						$('.route_did_B').show();
						$(".route_did_B :input").prop('required',true);

						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
						
					}if(this.value == "HANGUP") {
						$('.route_hangup_B').show();
						$(".route_hangup_B :input").prop('required',true);
						
						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
						
					}if(this.value == "EXTENSION") {
						$('.route_exten_B').show();
						$(".route_exten_B :input").prop('required',true);
						
						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
						
					}if(this.value == "PHONE") {
						$('.route_phone_B').show();
						$(".route_phone_B :input").prop('required',true);
						
						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
						
					}if(this.value == "VOICEMAIL") {
						$('.route_voicemail_B').show();
						$(".route_voicemail_B :input").prop('required',true);
						
						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
						
					}if(this.value == "AGI") {
						$('.route_agi_B').show();
						$(".route_agi_B :input").prop('required',true);
						
						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
					}
					if(this.value == "") {
						$('.route_callmenu_B').hide();
							$(".route_callmenu_B :input").prop('required',false);
						$('.route_ingroup_B').hide();
							$(".route_ingroup_B :input").prop('required',false);
						$('.route_did_B').hide();
							$(".route_did_B :input").prop('required',false);
						$('.route_hangup_B').hide();
							$(".route_hangup_B :input").prop('required',false);
						$('.route_exten_B').hide();
							$(".route_exten_B :input").prop('required',false);
						$('.route_phone_B').hide();
							$(".route_phone_B :input").prop('required',false);
						$('.route_voicemail_B').hide();
							$(".route_voicemail_B :input").prop('required',false);
						$('.route_agi_B').hide();
							$(".route_agi_B :input").prop('required',false);
					}
				});
				$(document).on('change', '.route_menu_C',function(){
					if(this.value == "CALLMENU") {
						$('.route_callmenu_C').show();
						$(".route_callmenu_C :input").prop('required',true);
						
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
					
					}if(this.value == "INGROUP") {
						$('.route_ingroup_C').show();
						$(".route_ingroup_C :input").prop('required',true);

						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
						
					}if(this.value == "DID") {
						$('.route_did_C').show();
						$(".route_did_C :input").prop('required',true);

						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
						
					}if(this.value == "HANGUP") {
						$('.route_hangup_C').show();
						$(".route_hangup_C :input").prop('required',true);
						
						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
						
					}if(this.value == "EXTENSION") {
						$('.route_exten_C').show();
						$(".route_exten_C :input").prop('required',true);
						
						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
						
					}if(this.value == "PHONE") {
						$('.route_phone_C').show();
						$(".route_phone_C :input").prop('required',true);
						
						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
						
					}if(this.value == "VOICEMAIL") {
						$('.route_voicemail_C').show();
						$(".route_voicemail_C :input").prop('required',true);
						
						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
						
					}if(this.value == "AGI") {
						$('.route_agi_C').show();
						$(".route_agi_C :input").prop('required',true);
						
						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
					}
					if(this.value == "") {
						$('.route_callmenu_C').hide();
							$(".route_callmenu_C :input").prop('required',false);
						$('.route_ingroup_C').hide();
							$(".route_ingroup_C :input").prop('required',false);
						$('.route_did_C').hide();
							$(".route_did_C :input").prop('required',false);
						$('.route_hangup_C').hide();
							$(".route_hangup_C :input").prop('required',false);
						$('.route_exten_C').hide();
							$(".route_exten_C :input").prop('required',false);
						$('.route_phone_C').hide();
							$(".route_phone_C :input").prop('required',false);
						$('.route_voicemail_C').hide();
							$(".route_voicemail_C :input").prop('required',false);
						$('.route_agi_C').hide();
							$(".route_agi_C :input").prop('required',false);
					}
				});
				$(document).on('change', '.route_menu_D',function(){
					if(this.value == "CALLMENU") {
						$('.route_callmenu_D').show();
						$(".route_callmenu_D :input").prop('required',true);
						
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
					
					}if(this.value == "INGROUP") {
						$('.route_ingroup_D').show();
						$(".route_ingroup_D :input").prop('required',true);

						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
						
					}if(this.value == "DID") {
						$('.route_did_D').show();
						$(".route_did_D :input").prop('required',true);

						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
						
					}if(this.value == "HANGUP") {
						$('.route_hangup_D').show();
						$(".route_hangup_D :input").prop('required',true);
						
						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
						
					}if(this.value == "EXTENSION") {
						$('.route_exten_D').show();
						$(".route_exten_D :input").prop('required',true);
						
						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
						
					}if(this.value == "PHONE") {
						$('.route_phone_D').show();
						$(".route_phone_D :input").prop('required',true);
						
						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
						
					}if(this.value == "VOICEMAIL") {
						$('.route_voicemail_D').show();
						$(".route_voicemail_D :input").prop('required',true);
						
						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
						
					}if(this.value == "AGI") {
						$('.route_agi_D').show();
						$(".route_agi_D :input").prop('required',true);
						
						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
					}
					if(this.value == "") {
						$('.route_callmenu_D').hide();
							$(".route_callmenu_D :input").prop('required',false);
						$('.route_ingroup_D').hide();
							$(".route_ingroup_D :input").prop('required',false);
						$('.route_did_D').hide();
							$(".route_did_D :input").prop('required',false);
						$('.route_hangup_D').hide();
							$(".route_hangup_D :input").prop('required',false);
						$('.route_exten_D').hide();
							$(".route_exten_D :input").prop('required',false);
						$('.route_phone_D').hide();
							$(".route_phone_D :input").prop('required',false);
						$('.route_voicemail_D').hide();
							$(".route_voicemail_D :input").prop('required',false);
						$('.route_agi_D').hide();
							$(".route_agi_D :input").prop('required',false);
					}
				});
				$(document).on('change', '.route_menu_E',function(){
					if(this.value == "CALLMENU") {
						$('.route_callmenu_E').show();
						$(".route_callmenu_E :input").prop('required',true);
						
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
					
					}if(this.value == "INGROUP") {
						$('.route_ingroup_E').show();
						$(".route_ingroup_E :input").prop('required',true);

						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
						
					}if(this.value == "DID") {
						$('.route_did_E').show();
						$(".route_did_E :input").prop('required',true);

						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
						
					}if(this.value == "HANGUP") {
						$('.route_hangup_E').show();
						$(".route_hangup_E :input").prop('required',true);
						
						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
						
					}if(this.value == "EXTENSION") {
						$('.route_exten_E').show();
						$(".route_exten_E :input").prop('required',true);
						
						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
						
					}if(this.value == "PHONE") {
						$('.route_phone_E').show();
						$(".route_phone_E :input").prop('required',true);
						
						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
						
					}if(this.value == "VOICEMAIL") {
						$('.route_voicemail_E').show();
						$(".route_voicemail_E :input").prop('required',true);
						
						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
						
					}if(this.value == "AGI") {
						$('.route_agi_E').show();
						$(".route_agi_E :input").prop('required',true);
						
						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
					}
					if(this.value == "") {
						$('.route_callmenu_E').hide();
							$(".route_callmenu_E :input").prop('required',false);
						$('.route_ingroup_E').hide();
							$(".route_ingroup_E :input").prop('required',false);
						$('.route_did_E').hide();
							$(".route_did_E :input").prop('required',false);
						$('.route_hangup_E').hide();
							$(".route_hangup_E :input").prop('required',false);
						$('.route_exten_E').hide();
							$(".route_exten_E :input").prop('required',false);
						$('.route_phone_E').hide();
							$(".route_phone_E :input").prop('required',false);
						$('.route_voicemail_E').hide();
							$(".route_voicemail_E :input").prop('required',false);
						$('.route_agi_E').hide();
							$(".route_agi_E :input").prop('required',false);
					}
				});
				
			/*** DID ***/
				// disable special characters on DID Exten
					$('#did_exten').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});
				// disable special characters on DID Desc
					$('#desc').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});

				//route change
					$('#route').on('change', function() {
						if(this.value == "AGENT") {
						  $('#form_route_agent').show();
						  
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "IN_GROUP") {
						  $('#form_route_ingroup').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "PHONE") {
						  $('#form_route_phone').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "CALLMENU") {
						  $('#form_route_callmenu').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "VOICEMAIL") {
						  $('#form_route_voicemail').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "EXTEN") {
						  $('#form_route_exten').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_callmenu').hide();
						}
						
					});
				
			// for voicemail
				<?php for($i=0;$i < 10; $i++){ ?>
				$(document).on('change', '#option_voicemail_select_<?php echo $i;?>',function(){
					var val = $(this).val();
					$('#option_voicemail_input_<?php echo $i;?>').val(val);
				});
				<?php } ?>
				$(document).on('change', '#option_voicemail_select_A',function(){
					var val = $(this).val();
					$('#option_voicemail_input_A').val(val);
				});
				$(document).on('change', '#option_voicemail_select_B',function(){
					var val = $(this).val();
					$('#option_voicemail_input_B').val(val);
				});
				$(document).on('change', '#option_voicemail_select_C',function(){
					var val = $(this).val();
					$('#option_voicemail_input_C').val(val);
				});
				$(document).on('change', '#option_voicemail_select_D',function(){
					var val = $(this).val();
					$('#option_voicemail_input_D').val(val);
				});
				$(document).on('change', '#option_voicemail_select_E',function(){
					var val = $(this).val();
					$('#option_voicemail_input_E').val(val);
				});
			
			//advanced ingroup settings
				<?php for($i=0;$i < 10; $i++){ ?>
				$(document).on('change', '#enter_filename_select_<?php echo $i;?>',function(){
					var val = $(this).val();
					$('#edit_enter_filename_<?php echo $i;?>').val(val);
				});
				<?php } ?>
				
				<?php for($i=0;$i < 10; $i++){ ?>
				$(document).on('change', '#edit_id_number_filename_select_<?php echo $i;?>',function(){
					var val = $(this).val();
					$('#edit_id_number_filename_<?php echo $i;?>').val(val);
				});
				<?php } ?>
				
				<?php for($i=0;$i < 10; $i++){ ?>
				$(document).on('change', '#edit_confirm_filename_select_<?php echo $i;?>',function(){
					var val = $(this).val();
					$('#edit_confirm_filename_<?php echo $i;?>').val(val);
				});
				<?php } ?>
	});
</script>
		
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
