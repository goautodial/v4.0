<?php

	###################################################
	### Name: telephonyinbound.php 					###
	### Functions: Manage Inbound, IVR & DID  		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

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
        <title>Inbound</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>
    	
    	<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
       
        <?php print $ui->creamyThemeCSS(); ?>
		
		<!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
		
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

     	<!-- bootstrap color picker -->
		<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>

     	<!-- SELECT2-->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
   		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>
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
                <?php if ($user->userHasAdminPermission()) { ?>

<?php
	/*
	 * APIs used
	 */

	$ingroup = $ui->API_getInGroups();

	$ivr = $ui->API_getIVR();
	
	$phonenumber = $ui->API_getPhoneNumber();

?>
			<div class="panel panel-default">
				<div class="panel-body">
					<legend>Inbound: <small>Ingroups, Call Menus, Phone Numbers</small> </legend>

		            <div role="tabpanel">
						
						<ul role="tablist" class="nav nav-tabs nav-justified">

						 <!-- In-group panel tabs-->
							 <li role="presentation" class="active">
								<a href="#T_ingroup" aria-controls="T_ingroup" role="tab" data-toggle="tab" class="bb0">
								    In-Groups</a>
							 </li>
						<!-- IVR panel tab -->
							 <li role="presentation">
								<a href="#T_ivr" aria-controls="T_ivr" role="tab" data-toggle="tab" class="bb0">
								    Interactive Voice Response (IVR) Menus </a>
							 </li>
						<!-- DID panel tab -->
							 <li role="presentation">
								<a href="#T_phonenumber" aria-controls="T_phonenumber" role="tab" data-toggle="tab" class="bb0">
								    Phone Numbers (DIDs/TFNs) </a>
							 </li>
						  </ul>
						  
						<!-- Tab panes-->
						<div class="tab-content bg-white">

							<!--==== In-group ====-->
							<div id="T_ingroup" role="tabpanel" class="tab-pane active">
								<table class="table table-striped table-bordered table-hover" id="table_ingroup">
								   <thead>
									  <tr>
                                         <th style="color: white;">Pic</th>
										 <th>In-Group</th>
										 <th class='hide-on-low hide-on-medium'>Descriptions</th>
										 <th class='hide-on-low hide-on-medium'>Priority</th>
										 <th class='hide-on-low'>Status</th>
										 <th class='hide-on-low hide-on-medium'>Time</th>
										 <th>Action</th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($ingroup->group_id);$i++){
							
												if($ingroup->active[$i] == "Y"){
													$ingroup->active[$i] = "Active";
												}else{
													$ingroup->active[$i] = "Inactive";
												}

											$action_INGROUP = $ui->getUserActionMenuForInGroups($ingroup->group_id[$i]);

									   	?>	
											<tr>
                                                <td><avatar username='<?php echo $ingroup->group_name[$i];?>' :size='36'></avatar></td>
												<td><strong><a class='edit-ingroup' data-id="<?php echo $ingroup->group_id[$i];?>"><?php echo $ingroup->group_id[$i];?></a></strong></td>
												<td class='hide-on-low hide-on-medium'><?php echo $ingroup->group_name[$i];?></td>
												<td class='hide-on-low hide-on-medium'><?php echo $ingroup->queue_priority[$i];?></td>
												<td class='hide-on-low'><?php echo $ingroup->active[$i];?></td>
												<td class='hide-on-low hide-on-medium'><?php echo $ingroup->call_time_id[$i];?></td>
												<td><?php echo $action_INGROUP;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>
							
							<!--==== IVR ====-->
							<div id="T_ivr" role="tabpanel" class="tab-pane">
								<table class="table table-striped table-bordered table-hover" id="table_ivr">
								   <thead>
									  <tr>
                                         <th style="color: white;">Pic</th>
										 <th>Menu ID</th>
										 <th class='hide-on-medium hide-on-low'>Descriptions</th>
										 <th class='hide-on-medium hide-on-low'>Prompt</th>
										 <th class='hide-on-medium hide-on-low'>Timeout</th>
										 <th>Action</th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($ivr->menu_id);$i++){

											$action_IVR = $ui->ActionMenuForIVR($ivr->menu_id[$i], $ivr->menu_name[$i]);

									   	?>	
											<tr>
                                                <td><avatar username='<?php echo $ivr->menu_name[$i];?>' :size='36'></avatar></td>
												<td><strong><a class='edit-ivr' data-id="<?php echo $ivr->menu_id[$i];?>"><?php echo $ivr->menu_id[$i];?></a></strong></td>
												<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_name[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_prompt[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_timeout[$i];?></td>
												<td><?php echo $action_IVR;?></td>
											</tr>
										<?php
											}
										?>
								   </tbody>
								</table>
							</div>

							<!--==== phonenumber / DID ====-->
							<div id="T_phonenumber" class="tab-pane">
								<table class="table table-striped table-bordered table-hover" id="table_did">
								   <thead>
									  <tr>
                                         <th style="color: white;">Pic</th>
										 <th>Phone Numbers</th>
										 <th class='hide-on-medium hide-on-low'>Description</th>
										 <th class='hide-on-medium hide-on-low'>Status</th>
										 <th class='hide-on-medium hide-on-low'>Route</th>
										 <th>Action</th>
									  </tr>
								   </thead>
								   <tbody>
									   	<?php
									   		for($i=0;$i < count($phonenumber->did_pattern);$i++){

									   			if($phonenumber->active[$i] == "Y"){
													$phonenumber->active[$i] = "Active";
												}else{
													$phonenumber->active[$i] = "Inactive";
												}

												if($phonenumber->did_route[$i] == "IN_GROUP"){
													$phonenumber->did_route[$i] = "IN-GROUP";
												}
												if($phonenumber->did_route[$i] == "EXTEN"){
													$phonenumber->did_route[$i] = "CUSTOM EXTENSION";
												}

											$action_DID = $ui->getUserActionMenuForDID($phonenumber->did_id[$i], $phonenumber->did_description[$i]);

									   	?>	
											<tr>
                                                <td><avatar username='<?php echo $phonenumber->did_description[$i];?>' :size='36'></avatar></td>
												<td><strong><a class='edit-phonenumber' data-id="<?php echo $phonenumber->did_id[$i];?>"><?php echo $phonenumber->did_pattern[$i];?></a></strong></td>
												<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->did_description[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->active[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->did_route[$i];?></td>
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
							
						<div class="bottom-menu skin-blue">
							<div class="action-button-circle" data-toggle="modal">
								<?php print $ui->getCircleButton("inbound", "plus"); ?>
							</div>
							<div class="fab-div-area" id="fab-div-area">
								<ul class="fab-ul" style="height: 250px;">
									<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_ingroups" title="Create an Ingroup"></a></li><br/>
									<li class="li-style"><a class="fa fa-volume-control-phone fab-div-item" data-toggle="modal" aria-hidden="true" data-target="#add_ivr" title="Add an Interactive Voice Recording"></a></li><br/>
									<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers" title="Add a Phone Number / DID / TFN"> </a></li>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- /. body -->
			</div><!-- /. panel -->
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->

<?php
	/*
	 * APIs for getting lists for the some of the forms
	 */
	$users = $ui->API_goGetAllUserLists();
	$user_groups = $ui->API_goGetUserGroupsList();
	$ingroups = $ui->API_getInGroups();
	$voicemails = $ui->API_goGetVoiceMails();
	$phones = $ui->API_getPhonesList();
	$ivr = $ui->API_getIVR();
	$scripts = $ui->API_goGetAllScripts();
	$voicefiles = $ui->API_GetVoiceFilesList();
?>


<!-- TELEPHONY INBOUND MODALS -->

	<!-- ADD INGROUP MODAL -->
		<div class="modal fade" id="add_ingroups" aria-labelledby="ingroup_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">

			<!-- Header -->
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="ingroup_modal">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create ingroups."></i> 
						<b>In-Group Wizard » Create New Ingroup</b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body wizard-content">
				
				<form action="AddTelephonyIngroup.php" method="POST" id="create_ingroup" role="form">
					<div class="row">
						<h4>Group Details
                           <br>
                           <small>Fill up group details then assign to a user group.</small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-3 control-label" for="groupid">Group ID:</label>
								<div class="col-sm-9 mb">
									<input type="text" name="groupid" id="groupid" class="form-control" placeholder="Group ID (Mandatory)" title="Must be 2-20 characters in length." maxlength="20" minlength="2" required>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-3 control-label" for="groupname">Group Name</label>
								<div class="col-sm-9 mb">
									<input type="text" name="groupname" id="groupname" class="form-control" placeholder="Group Name (Mandatory)" title="Must be 2-20 characters in length." maxlength="20" minlength="2" required>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-3 control-label" for="color">Group Color</label>
								<div class="col-sm-9 mb">
						            <input type="text" class="form-control colorpicker" name="color" id="color" value="#fffff">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="user_group">User Group</label>
								<div class="col-sm-9 mb">
									<select id="user_group" class="form-control select2-1" name="user_group" style="width:100%;">
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="active">Active</label>
								<div class="col-sm-9 mb">
									<select name="active" id="active" class="form-control">
										<option value="Y" selected>Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="web_form">Web Form</label>
								<div class="col-sm-9 mb">
									<input type="url" name="web_form" id="web_form" class="form-control" placeholder="Place a valid URL here... ">
								</div>
							</div>
						</fieldset>
						<h4>Other Settings
                           <br>
                           <small>Settings for the created group</small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-3 control-label" for="ingroup_voicemail">Voicemail</label>
								<div class="col-sm-9 mb">	
									<select name="ingroup_voicemail" id="ingroup_voicemail" class="form-control select2-1" style="width:100%;">
										<?php
											if($voicemails == NULL){
										?>
											<option value="" selected>--No Voicemails Available--</option>
										<?php
											}else{
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
								<label class="col-sm-3 control-label" for="next_agent_call">Next Agent Call</label>
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
								<label class="col-sm-3 control-label" for="display">Fronter Display</label>
								<div class="col-sm-9 mb">
									<select name="display" id="display" class="form-control">
										<option value="N" selected>No</option>
										<option value="Y">Yes</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="script">Script</label>
								<div class="col-sm-9 mb">	
									<select name="script" id="script" class="form-control select2-1" style="width:100%;">
										<option value="NONE">--- NONE --- </option>
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
								<label class="col-sm-3 control-label" for="call_launch">Get Call Launch</label>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="ivr_modal">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create IVR."></i> 
						<b>Call Menu Wizard » Create New Call Menu</b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyIVR.php" method="POST" id="create_ivr" role="form">
					<div class="row">
						<h4>Account Details
                           <br>
                           <small>Assign then Enter Account and Login Details</small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-4 control-label" for="menu_id">Menu ID:</label>
								<div class="col-sm-8 mb">
									<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID (Mandatory)" minlength="4" title="Minimum of 4 characters" required>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="menu_name">Menu Name</label>
								<div class="col-sm-8 mb">
									<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name (Mandatory)" required>
								</div>
							</div>
							
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="menu_prompt">Menu Greeting</label>
								<div class="col-sm-8 mb">
									<select name="menu_prompt" id="menu_prompt" class="form-control select2-1" style="width:100%;">
										<option value="goWelcomeIVR" selected>-- Default Value --</option>
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
								<label class="col-sm-4 control-label" for="menu_timeout">Menu Timeout</label>
								<div class="col-sm-8 mb">
									<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="10" min="0" required>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="menu_timeout_prompt">Menu Timeout Greeting</label>
								<div class="col-sm-8 mb">
									<select name="menu_timeout_prompt " id="menu_timeout_prompt" class="form-control select2-1" style="width:100%;">
										<option value="" selected>-- Default Value --</option>
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
								<label class="col-sm-4 control-label" for="menu_invalid_prompt">Menu Invalid Greeting</label>
								<div class="col-sm-8 mb">
									<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control select2-1" style="width:100%;">
										<option value="" selected>-- Default Value --</option>	
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
								<label class="col-sm-4 control-label" for="menu_repeat">Menu Repeat</label>
								<div class="col-sm-8 mb">
									<input type="number" name="menu_repeat" id="menu_repeat" class="form-control" value="1" min="0" required>
								</div>
							</div>
							
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="menu_time_check">Menu Time Check</label>
								<div class="col-sm-8 mb">
									<select name="menu_time_check" id="menu_time_check" class="form-control">
										<option value="ADMIN" > Select Menu Time Check </option>		
									</select>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="call_time">Call Time</label>
								<div class="col-sm-8 mb">
									<select name="call_time" id="call_time" class="form-control">
										<option value="ADMIN" > Select Call Time </option>		
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="user_groups">User Groups</label>
								<div class="col-sm-8 mb">
									<select name="user_groups" id="user_groups" class="form-control select2-1" style="width:100%;">
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>		
									</select>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-5 control-label" for="menu_repeat">Track call in realtime report</label>
								<div class="col-sm-7 mb"> 
									<select name="call_time" id="call_time" class="form-control">
										<option value="ADMIN" > Select Track Call </option>		
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-5 control-label" for="tracking_group">Tracking Group</label>
								<div class="col-sm-7 mb">
									<select name="tracking_group" id="tracking_group" class="form-control select2-1" style="width:100%;">
									<?php
										for($i=0;$i<count($ingroups->group_id);$i++){
									?>
										<option value="<?php echo $ingroups->group_id[$i];?>">
											<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
										</option>									
									<?php
										}
									?>
									</select>
								</div>
							</div>
							
						</fieldset>

				<!-- STEP 2 -->
						<h4>Account Details
	                       <br>
	                       <small>Assign then Enter Account and Login Details</small>
	                    </h4>
	                    <fieldset>
							<div class="form-group">
								<div class="col-lg-12">
									<div class="pull-right">
										<button type="button" class="btn btn-primary add-option">Add Option</button>
									</div>
								</div>
							</div>
							<div class="form-group to-clone-opt">
								<label class="col-sm-3 control-label" for="">Default Call Menu Entry:</label>
								<div class="col-lg-2">
									Option:
									<select class="form-control">
										<option selected>TIMEOUT</option>
									</select>
								</div>
								<div class="col-lg-2">
									Desription: 
									<input type="text" name="" id="" class="form-control" placeholder="Description" required value="Hangup">
								</div>
								<div class="col-lg-2">
									Route:
									<select class="form-control">
										<option selected>Hangup</option>
									</select>
								</div>
								<div class="col-lg-2">
									Audio File:
									<input type="text" name="" id="" class="form-control" placeholder="Description" required value="vm-goodbye">
								</div>
								<div class="col-lg-1 btn-remove"></div>
							</div>
							<div class="cloning-area"></div>
						</fieldset>
					</div><!-- End of Step -->
				

				</div> <!-- end of modal body -->
				</form>
				
				
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
						<b>DID Wizard » Create new DID</b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body wizard-content">
				
				<form action="AddTelephonyPhonenumber.php" method="POST" id="create_phonenumber" role="form">
					<div class="row">
						<!-- STEP 1 -->
						<h4>DID Details
                           <br>
                           <small>Enter the basic details of your DID then assign it to a user group</small>
                        </h4>
                        <fieldset>
							<div class="form-group mt">
								<label class="col-sm-4 control-label" for="did_exten">DID Extention:</label>
								<div class="col-sm-8 mb">
									<input type="text" name="did_exten" id="did_exten" class="form-control" placeholder="DID Extention (Mandatory)" maxlength="20" minlength="2" required title="Must be 2-20 characters in length." />
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="desc">DID Description</label>
								<div class="col-sm-8 mb">
									<input type="text" name="desc" id="desc" class="form-control" placeholder="DID Description (Mandatory)" maxlength="20" minlength="2" title="Must be  2-20 characters in length"  required />
								</div>
							</div>
							
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="active">Active</label>
								<div class="col-sm-8 mb">
									<select name="active" id="active" class="form-control">
										<option value="Y" selected>Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="route" >DID Route</label>
								<div class="col-sm-8 mb">
									<select class="form-control" id="route" name="route">
										<option value="AGENT"> Agent </option>
										<option value="IN_GROUP"> In-group </option>
										<option value="PHONE"> Phone </option>
										<option value="CALLMENU"> Call Menu / IVR </option>
										<option value="VOICEMAIL"> Voicemail </option>
										<option value="EXTEN"> Custom Extension </option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="user_groups">User Groups</label>
								<div class="col-sm-8 mb">
									<select name="user_groups" id="user_groups" class="form-control select2-1" style="width:100%;">
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
						</fieldset>
						<h4>Route Settings
                           <br>
                           <small>Fill up details needed for the chosen route.</small>
                        </h4>
                        <fieldset>
						<!-- IF DID ROUTE = AGENT-->

							<div id="form_route_agent">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_agentid">Agent ID</label>
									<div class="col-sm-8 mb">	
										<select name="route_agentid" id="route_agentid" class="form-control select2-1" style="width:100%;">
											<option value="" > -- NONE -- </option>
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
									<label class="col-sm-4 control-label" for="route_unavail">Agent Unavailable Action</label>
									<div class="col-sm-8 mb">	
										<select name="route_unavail" id="route_unavail" class="form-control">
											<option value="VOICEMAIL" > Voicemail </option>
											<option value="PHONE" > Phone </option>
											<option value="IN_GROUP" > In-group </option>
											<option value="EXTEN" > Custom Extension </option>
										</select>
									</div>
								</div>
							</div><!-- end of div agent-->
							
						<!-- IF DID ROUTE = IN-GROUP-->
						
							<div id="form_route_ingroup" style="display: none;">
								<label class="col-sm-4 control-label" for="route_ingroupid">In-Group ID</label>
								<div class="col-sm-8 mb">	
									<select name="route_ingroupid" id="route_ingroupid" class="form-control select2-1" style="width:100%;">
										<?php
											for($i=0;$i<count($ingroups->group_id);$i++){
										?>
											<option value="<?php echo $ingroups->group_id[$i];?>">
												<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
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
									<label class="col-sm-4 control-label" for="route_phone_exten">Phone Extension</label>
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
									<label class="col-sm-4 control-label" for="route_phone_server">Server IP</label>
									<div class="col-sm-8 mb">	
										<select name="route_phone_server" id="route_phone_server" class="form-control select2-1" style="width:100%;">
											<option value="" > -- NONE -- </option>
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
									<label class="col-sm-4 control-label" for="route_ivr">Call Menu</label>
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
												<option value="">- - - No Available Call Menu - - - </option>
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
									<label class="col-sm-4 control-label" for="route_voicemail">Voicemail Box</label>
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
									<label class="col-sm-4 control-label" for="route_exten">Extension</label>
									<div class="col-sm-8 mb">
										<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="route_exten_context">Extension Context</label>
									<div class="col-sm-8 mb">
										<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" required>
									</div>
								</div>
							</div><!-- end of custom extension div -->
						</fieldset>
					</div><!-- End of Step -->
				
				

				</div> <!-- end of modal body -->
				</form>
			</div>
		</div>
	</div><!-- end of modal -->

<!-- END OF TELEPHONY INBOUND MODALS -->

		<?php print $ui->standardizedThemeJS(); ?>
        <!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	    

 <script type="text/javascript">
	$(document).ready(function() {

		/*******************
		** INITIALIZATIONS
		*******************/
			// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			//loads datatable functions
				$('#table_ingroup').dataTable();
				$('#table_ivr').dataTable();
				$('#table_did').dataTable();

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

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            // Submit form via ajax
					            $.ajax({
									url: "./php/AddTelephonyIngroup.php",
									type: 'POST',
									data: $("#create_ingroup").serialize(),
									success: function(data) {
									  // console.log(data);
										  if(data == "success"){
												swal("Success!", "Ingroup Successfully Created!", "success");
										  		window.setTimeout(function(){location.reload()},1000);

										  		$('#finish').text("Submit");
												$('#finish').attr("disabled", false);
										  }
										  else{
											  sweetAlert("Oops...", "Something went wrong! "+data, "error");

											  $('#finish').text("Submit");
											  $('#finish').attr("disabled", false);
										  }
									}
								});
			        }
			    }); // end of wizard
			
			//------------------------

			/*********
			** EDIT INGROUP
			*********/

				$(".edit-ingroup").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="groupid" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE INGROUP
			*********/
				//DELETE INGROUPS
				$(document).on('click','.delete-ingroup',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this inbound!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										groupid:id,
									},
									success: function(data) {
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Inbound Successfully Deleted!", "success");
											window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
										}
									}
								});
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
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

			            ivr_form.validate().settings.ignore = ":disabled,:hidden";
			            return ivr_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            ivr_form.validate().settings.ignore = ":disabled,:hidden";
			            return ivr_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            
						sweetAlert("Oops...", "NO ADD FUNCTION YET. Sorry for the inconvenience, but this function is still under construction", "error");

						$('#finish').text("Submit");
						$('#finish').attr("disabled", false);
							
			        }
			    }); // end of wizard
			
			//------------------------

			/*********
			** EDIT IVR
			*********/

				$(".edit-ivr").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="ivr" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE IVR
			*********/

				$(document).on('click','.delete-ivr',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this IVR!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										ivr:id,
									},
									success: function(data) {
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "IVR Successfully Deleted!", "success");
											//window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
										}
									}
								});
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
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

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            $.ajax({
								url: "./php/AddTelephonyPhonenumber.php",
								type: 'POST',
								data: $("#create_phonenumber").serialize(),
								success: function(data) {
								   console.log(data);
									  if(data == 1){
											swal("Success!", "Phone Number Successfully Created!", "success");
									  		window.setTimeout(function(){location.reload()},1000)
									  		$('#submit_did').val("Submit");
											$('#submit_did').attr("disabled", false);
									  }else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
											$('#submit_did').val("Submit");
											$('#submit_did').attr("disabled", false);
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
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE DID
			*********/

				$(document).on('click','.delete-phonenumber',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this phonenumber!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										modify_did:id,
									},
									
									success: function(data) {
									//console.log(modify_did);
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Phonenumber Successfully Deleted!", "success");
											//window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
										}
									}
								});
	                		} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
				});
		
		//-------------------- end of main did events

		/*******************
		** OTHER TRIGGER EVENTS and FILTERS
		*******************/
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
				//add option
					$('.add-option').click(function(){
						var toClone = $('.to-clone-opt').clone();

						toClone.removeClass('to-clone-opt');
						toClone.find('label.control-label').text('');
						toClone.find('.btn-remove').append('<span class="fa fa-remove fa-2x text-red remove-row"></span>');

						$('.cloning-area').append(toClone);
					});

				//remove option
					$(document).on('click', '.remove-row', function(){
						var row = $(this).parent().parent();
						
						row.remove();
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
		

			/* loads colorpicker */
    			$(".colorpicker").colorpicker();

    		/* initialize select2 */
				$('.select2-1').select2({
			        theme: 'bootstrap'
			    });
	});
</script>
		
		<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
