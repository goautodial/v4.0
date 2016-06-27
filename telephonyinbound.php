<?php	
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
        <title>Goautodial</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
    	<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
		<!-- Circle Buttons style -->
    	<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
		
		<!-- Wizard Form style -->
    	<link rel="stylesheet" href="css/easyWizard.css">
		
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        
        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).load(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>

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
                        <small><?php $lh->translateText("inbound"); ?></small>
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
            <div role="tabpanel" class="panel panel-transparent">
				
				<ul role="tablist" class="nav nav-tabs">

				 <!-- In-group panel tabs-->
					 <li role="presentation" class="active">
						<a href="#T_ingroup" aria-controls="T_ingroup" role="tab" data-toggle="tab" class="bb0">
						   <sup><span class="fa fa-users"></span></sup> In-Groups</a>
					 </li>
				<!-- IVR panel tab -->
					 <li role="presentation">
						<a href="#T_ivr" aria-controls="T_ivr" role="tab" data-toggle="tab" class="bb0">
						   <sup><span class="fa fa-volume-up"></span></sup> Interactive Voice Response (IVR) Menus </a>
					 </li>
				<!-- DID panel tab -->
					 <li role="presentation">
						<a href="#T_phonenumber" aria-controls="T_phonenumber" role="tab" data-toggle="tab" class="bb0">
						   <sup><span class="fa fa-phone-square"></span></sup> Phone Numbers (DIDs/TFNs) </a>
					 </li>
				  </ul>
				  
				<!-- Tab panes-->
				<div class="tab-content p0 bg-white">


				<!--==== In-group ====-->
				  <div id="T_ingroup" role="tabpanel" class="tab-pane active" style="padding: 20px;">
						<table class="table table-striped table-bordered table-hover" id="table_ingroup">
						   <thead>
							  <tr>
								 <th>In-Group</th>
								 <th>Descriptions</th>
								 <th>Priority</th>
								 <th>Status</th>
								 <th>Time</th>
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
										<td><?php echo $ingroup->group_id[$i];?></td>
										<td><a class=''><?php echo $ingroup->group_name[$i];?></a></td>
										<td><?php echo $ingroup->queue_priority[$i];?></td>
										<td class='hide-on-medium hide-on-low'><?php echo $ingroup->active[$i];?></td>
										<td class='hide-on-medium hide-on-low'><?php echo $ingroup->call_time_id[$i];?></td>
										<td><?php echo $action_INGROUP;?></td>
									</tr>
								<?php
									}
								?>
						   </tbody>
						</table>
						<br/>
					<div class="panel-footer text-right">&nbsp;</div>
				 </div>
				
				<!--==== IVR ====-->
				  <div id="T_ivr" role="tabpanel" class="tab-pane" style="padding: 20px;">
						<table class="table table-striped table-bordered table-hover" id="table_ivr">
						   <thead>
							  <tr>
								 <th>Menu ID</th>
								 <th>Descriptions</th>
								 <th>Prompt</th>
								 <th class='hide-on-medium hide-on-low'>Timeout</th>
								 <th>Action</th>
							  </tr>
						   </thead>
						   <tbody>
							   	<?php
							   		for($i=0;$i < count($ivr->menu_id);$i++){

									$action_IVR = $ui->ActionMenuForIVR($ivr->menu_id[$i]);

							   	?>	
									<tr>
										<td><?php echo $ivr->menu_id[$i];?></td>
										<td><a class=''><?php echo $ivr->menu_name[$i];?></a></td>
										<td><?php echo $ivr->menu_prompt[$i];?></td>
										<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_timeout[$i];?></td>
										<td><?php echo $action_IVR;?></td>
									</tr>
								<?php
									}
								?>
						   </tbody>
						</table>
						<br/>
					<div class="panel-footer text-right">&nbsp;</div>
				 </div>

				 <!--==== phonenumber / DID ====-->
				  <div id="T_phonenumber" role="tabpanel" class="tab-pane" style="padding: 20px;">
						<table class="table table-striped table-bordered table-hover" id="table_did">
						   <thead>
							  <tr>
								 <th>Phone Numbers</th>
								 <th>Description</th>
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

									$action_DID = $ui->getUserActionMenuForDID($phonenumber->did_pattern[$i]);

							   	?>	
									<tr>
										<td><?php echo $phonenumber->did_pattern[$i];?></td>
										<td><a class=''><?php echo $phonenumber->did_description[$i];?></a></td>
										<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->active[$i];?></td>
										<td class='hide-on-medium hide-on-low'><?php echo $phonenumber->did_route[$i];?></td>
										<td><?php echo $action_DID;?></td>
									</tr>
								<?php
									}
								?>
						   </tbody>
						</table>
						<br/>
					<div class="panel-footer text-right">&nbsp;</div>
				 </div>

				</div><!-- END tab content-->
			</div>

				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
				
				<div class="bottom-menu skin-blue">
					<div class="action-button-circle" data-toggle="modal">
						<?php print $ui->getCircleButton("calls", "plus"); ?>
					</div>
					<div class="fab-div-area" id="fab-div-area">
						<ul class="fab-ul" style="height: 250px;">
							<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_ingroups"></a></li><br/>
							<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#add_ivr"></a></li><br/>
							<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers"> </a></li>
						</ul>
					</div>
				</div>
					
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

<?php
	/*
	 * APIs for getting lists for the some of the forms
	 */
	$users = $ui->API_goGetAllUserLists();
	$ingroups = $ui->API_getInGroups();
	$voicemails = $ui->API_goGetVoiceMails();
	$phones = $ui->API_getPhonesList();
	$ivr = $ui->API_getIVR();
	$scripts = $ui->API_goGetAllScripts();
	$voicefiles = $ui->API_GetVoiceFilesList();
?>


<!-- TELEPHONY INBOUND MODALS -->

	<!-- ADD INGROUP MODAL -->
		<div class="modal fade" id="add_ingroups" tabindex="-1" aria-labelledby="ingroup_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:10px;">
			
			<!-- NOTIFICATIONS -->
				<div class="output-message-success hide">
					<div class="alert alert-success alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>Success!</strong> New Agent added.
					</div>
				</div>
				<div class="output-message-error hide">
					<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>Error: <div id="ingroup_result"></div></strong> Something went wrong please see input data on form or if agent already exists.
					</div>
				</div>
				<div class="output-message-incomplete hide">
					<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>Incomplete!</strong> Something went wrong, please complete all the fields below.
					</div>
				</div>
			<!-- Header -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ingroup_modal">In-Group Wizard » Create New Ingroup</h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyIngroup.php" method="POST" id="create_ingroup" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="groupid">Group ID:</label>
							<div class="col-sm-7">
								<input type="text" name="groupid" id="groupid" class="form-control" placeholder="Group ID" maxlength="20" minlength="2" required title="No Spaces. 2-20 characters in length.">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="groupname">Group Name: </label>
							<div class="col-sm-7">
								<input type="text" name="groupname" id="groupname" class="form-control" placeholder="Group Name" maxlength="20" minlength="2" required title="2-20 characters in length">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="color">Group Color: </label>
							<div class="col-sm-7">
								<input type="color" class="form-control" id="color" name="color" value="#FFFFFF">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_group">User Group: </label>
							<div class="col-sm-7">
								<select id="user_group" class="form-control" name="user_group">
									<option value="ADMIN">GOAUTODIAL ADMINISTRATORS</option>
									<option value="AGENTS">GOAUTODIAL AGENTS</option>
									<option value="SUPERVISOR">SUPERVISOR</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="active">Active: </label>
							<div class="col-sm-2">
								<select name="active" id="active" class="form-control">
									<option value="N" selected>No</option>
									<option value="Y">Yes</option>								
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="web_form">Web Form: </label>
							<div class="col-sm-7">
								<input type="url" name="web_form" id="web_form" class="form-control" placeholder=""  required title="Must be a valid URL">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="ingroup_voicemail">Voicemail: </label>
							<div class="col-sm-7">	
								<select name="ingroup_voicemail" id="ingroup_voicemail" class="form-control">
									<?php
										if($voicemails == NULL){
									?>
										<option value="TEST" selected>--No Voicemails Available--</option>
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
							<label class="col-sm-4 control-label" for="next_agent_call">Next Agent Call: </label>
							<div class="col-sm-4">	
								<select name="next_agent_call" id="next_agent_call" class="form-control">
										<option value="random" > random </option>
										<option value="oldest_call_start" > oldest_call_start </option>
										<option value="oldest_call_finish" > oldest_call_finish </option>
										<option value="overall_user_level" > overall_user_level </option>
										<option value="inbound_group_rank" > inbound_group_rank </option>
										<option value="campaign_rank" > campaign_rank </option>
										<option value="fewest_calls" > fewest_calls </option>
										<option value="fewest_calls_campaign" > fewest_calls_campaign </option>
										<option value="longest_wait_time" > longest_wait_time </option>
										<option value="ring_all" > ring_all </option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="display">Fronter Display: </label>
							<div class="col-sm-2">
								<select name="display" id="display" class="form-control">
									<option value="Y">Yes</option>	
									<option value="N" selected>No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="script">Script: </label>
							<div class="col-sm-5">	
								<select name="script" id="script" class="form-control">
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
							<label class="col-sm-4 control-label" for="call_launch">Get Call Launch: </label>
							<div class="col-sm-3">	
								<select name="call_launch" id="call_launch" class="form-control">
										<option value="NONE" > NONE </option>
										<option value="SCRIPT" > SCRIPT </option>
										<option value="WEBFORM" > WEBFORM </option>
										<option value="WEBFORMTWO" > WEBFORMTWO </option>
										<option value="FORM" > FORM </option>
										<option value="EMAIL" > EMAIL </option>
								</select>
							</div>
						</div>
					</div><!-- end of step -->
				
				</form>

				</div> <!-- end of modal body -->
				
				<div class="modal-footer">
					<!-- The wizard button will be inserted here. -->
					<button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
					<input type="submit" class="btn btn-primary" id="submit_ingroup" value="Submit" style="display: inline-block;">
				</div>
			</div>
		</div>
	</div><!-- end of modal -->
	
	<!-- ADD IVR MODAL -->
		<div class="modal fade" id="add_ivr" tabindex="-1" aria-labelledby="ivr_modal" >
        <div class="modal-dialog" role="document" style="width: 800px;">
            <div class="modal-content" style="border-radius:10px;">
            	<!-- NOTIFICATIONS -->

					<div class="output-message-success hide">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Success!</strong> New Call Menu added.
						</div>
					</div>
					<div class="output-message-error hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong><span id="ivr_result"></span></strong> Something went wrong, please see input data on form or if agent already exists.
						</div>
					</div>
					<div class="output-message-incomplete hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Incomplete!</strong> Something went wrong, please complete all the fields below.
						</div>
					</div>
					
				<div class="modal-header">					
					<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ivr_modal"><b>Call Menu Wizard » Create New Call Menu</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyIVR.php" method="POST" id="create_ivr" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step" style="display: block;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="menu_id">Menu ID:</label>
							<div class="col-sm-8">
								<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID" minlength="4" required title="No Spaces. Minimum of 4 characters">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="description">Description:</label>
							<div class="col-sm-8">
								<input type="text" name="description" id="description" class="form-control" placeholder="Description" minlength="4" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_name">Menu Name: </label>
							<div class="col-sm-8">
								<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name" required>
							</div>
						</div>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_prompt">Menu Greeting: </label>
							<div class="col-sm-8">
								<select name="menu_prompt" id="menu_prompt" class="form-control">
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
							<label class="col-sm-4 control-label" for="menu_timeout">Menu Timeout: </label>
							<div class="col-sm-8">
								<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="10" min="0" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_timeout_prompt">Menu Timeout Greeting: </label>
							<div class="col-sm-8">
								<select name="menu_timeout_prompt " id="menu_timeout_prompt" class="form-control">
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
							<label class="col-sm-4 control-label" for="menu_invalid_prompt">Menu Invalid Greeting: </label>
							<div class="col-sm-8">
								<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control">
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
							<label class="col-sm-4 control-label" for="menu_repeat">Menu Repeat: </label>
							<div class="col-sm-8">
								<input type="number" name="menu_repeat" id="menu_repeat" class="form-control" value="1" min="0" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_time_check">Menu Time Check: </label>
							<div class="col-sm-8">
								<select name="menu_time_check" id="menu_time_check" class="form-control">
									<option value="ADMIN" > Select Menu Time Check </option>		
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="call_time">Call Time: </label>
							<div class="col-sm-8">
								<select name="call_time" id="call_time" class="form-control">
									<option value="ADMIN" > Select Call Time </option>		
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="menu_repeat">Track call in realtime report: </label>
							<div class="col-sm-8"> 
								<select name="call_time" id="call_time" class="form-control">
									<option value="ADMIN" > Select Track Call </option>		
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="tracking_group">Tracking Groups: </label>
							<div class="col-sm-8">
								<select name="tracking_group" id="tracking_group" class="form-control">
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
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_groups">User Groups: </label>
							<div class="col-sm-8">
								<select name="user_groups" id="user_groups" class="form-control">
									<option value="ADMIN" > ADMIN - GOAUTODIAL ADMINISTRATORS </option>
									<option value="AGENTS" > AGENTS - GOAUTODIAL AGENTS </option>
									<option value="SUPERVISOR" > SUPERVISOR - SUPERVISOR </option>			
								</select>
							</div>
						</div>
					</div><!-- End of Step -->

				<!-- STEP 2 -->
					<div class="wizard-step" style="display: block;">
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
					</div><!-- End of Step -->
				

				</div> <!-- end of modal body -->
				</form>
				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
				
				
			</div>
		</div>
	</div><!-- end of modal -->

	
	<!-- ADD DID MODAL -->
		<div class="modal fade" id="add_phonenumbers" tabindex="-1" aria-labelledby="did_modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:10px;">
				<div class="modal-header">
					
					<!-- NOTIFICATIONS -->

					<div class="output-message-success hide">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Success!</strong> New Phone Number added.
						</div>
					</div>
					<div class="output-message-error hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Error: <span id="phonenumber_result"></span></strong> Something went wrong, please see input data on form or if agent already exists.
						</div>
					</div>
					<div class="output-message-incomplete hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Incomplete!</strong> Something went wrong, please complete all the fields below.
						</div>
					</div>
					
					<button type="button" class="close" data-dismiss="modal" aria-label="close_did"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="did_modal">DID Wizard » Create new DID</h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="AddTelephonyPhonenumber.php" method="POST" id="create_phonenumber" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step" style="display: block;">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="did_exten">DID Extention:</label>
							<div class="col-sm-8">
								<input type="text" name="did_exten" id="did_exten" class="form-control" placeholder="DID Extention" maxlength="20" minlength="2" required title="No Spaces. 2-20 characters in length.">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="desc">DID Description: </label>
							<div class="col-sm-8">
								<input type="text" name="desc" id="desc" class="form-control" placeholder="DID Description" maxlength="20" minlength="2" required title="2-20 characters in length">
							</div>
						</div>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="active">Active: </label>
							<div class="col-sm-8">
								<select name="active" id="active" class="form-control">
									<option value="N" selected>No</option>
									<option value="Y">Yes</option>								
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_group" >DID Route: </label>
							<div class="col-sm-8">
								<select class="form-control" id="route" name="route">
									<option value="AGENT" > Agent </option>
									<option value="IN_GROUP" > In-group </option>
									<option value="PHONE" > Phone </option>
									<option value="CALLMENU" > Call Menu / IVR </option>
									<option value="VOICEMAIL" > Voicemail </option>
									<option value="EXTEN" > Custom Extension </option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_groups">User Groups: </label>
							<div class="col-sm-8">
								<select name="user_groups" id="user_groups" class="form-control">
									<option value="ADMIN" > ADMIN - GOAUTODIAL ADMINISTRATORS </option>
									<option value="AGENTS" > AGENTS - GOAUTODIAL AGENTS </option>
									<option value="SUPERVISOR" > SUPERVISOR - SUPERVISOR </option>			
								</select>
							</div>
						</div>
						
				<!-- IF DID ROUTE = AGENT-->

					<div id="form_route_agent">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_agentid">Agent ID: </label>
							<div class="col-sm-8">	
								<select name="route_agentid" id="route_agentid" class="form-control">
									<option value="" > -- NONE -- </option>
									<?php
										for($i=0;$i<count($users->userno);$i++){
									?>
										<option value="<?php echo $users->userno[$i];?>">
											<?php echo $users->userno[$i].' - '.$users->full_name[$i];?>
										</option>									
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="route_unavail">Agent Unavailable Action: </label>
							<div class="col-sm-8">	
								<select name="route_unavail" id="route_unavail" class="form-control">
									<option value="" > Voicemail </option>
									<option value="" > Phone </option>
									<option value="" > In-group </option>
									<option value="" > Custom Extension </option>
								</select>
							</div>
						</div>
					</div><!-- end of div agent-->
					
				<!-- IF DID ROUTE = IN-GROUP-->
				
					<div id="form_route_ingroup" style="display: none;">
						<label class="col-sm-4 control-label" for="route_ingroupid">In-Group ID: </label>
						<div class="col-sm-8">	
							<select name="route_ingroupid" id="route_ingroupid" class="form-control">
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
							<label class="col-sm-4 control-label" for="route_phone_exten">Phone Extension: </label>
							<div class="col-sm-8">	
								<select name="route_phone_exten" id="route_phone_exten" class="form-control">
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
							<label class="col-sm-4 control-label" for="route_phone_server">Server IP: </label>
							<div class="col-sm-8">	
								<select name="route_phone_server" id="route_phone_server" class="form-control">
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
							<label class="col-sm-4 control-label" for="route_ivr">Call Menu: </label>
							<div class="col-sm-8">	
								<select name="route_ivr" id="route_ivr" class="form-control">
									<?php
										for($i=0;$i<count($ivr->menu_id);$i++){
									?>
										<option value="<?php echo $ivr->menu_id[$i];?>">
											<?php echo $ivr->menu_id[$i].' - '.$ivr->menu_name[$i];?>
										</option>									
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
							<label class="col-sm-4 control-label" for="route_voicemail">Voicemail Box: </label>
							<div class="col-sm-8">	
								<select name="route_voicemail" id="route_voicemail" class="form-control">
									
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
							<label class="col-sm-4 control-label" for="route_exten">Extension: </label>
							<div class="col-sm-8">
								<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" required>
							</div>
							<label class="col-sm-4 control-label" for="route_exten_context">Extension Context: </label>
							<div class="col-sm-8">
								<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" required>
							</div>
						</div>
					</div><!-- end of custom extension div -->
					
					</div><!-- End of Step -->
				
				

				</div> <!-- end of modal body -->
				</form>
				<div class="modal-footer">
					<!-- The wizard button will be inserted here. -->
					<button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
					<input type="submit" class="btn btn-primary" id="submit_did" value="Submit" style="display: inline-block;">
					
				</div>
			</div>
		</div>
	</div><!-- end of modal -->

<!-- END OF TELEPHONY INBOUND MODALS -->

        <!-- wizard -->
		<script src="js/easyWizard.js" type="text/javascript"></script>
 		<script type="text/javascript">
			$(document).ready(function() {

				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			/*preloader
            	$(".fakeloader").fakeLoader({
                    timeToHide:1200,
                    bgColor:"#9b59b6",
                    spinner:"spinner7"
                });
			*/
			//loads datatable functions
				$('#table_ingroup').dataTable();
				$('#table_ivr').dataTable();
				$('#table_did').dataTable();

			//reloads page when modal closes
				$('#add_ingroups').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_ivr').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_phonenumbers').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});
			//-----------
				
				$('#route').on('change', function() {
					//  alert( this.value ); // or $(this).val()
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
				
	
				// for easy wizard -
				$("#add_ingroups").wizard({
					onfinish:function(){
						console.log("Ingroup Added!");
					}
				});
				
				$("#add_ivr").wizard({
					onfinish:function(){
						console.log("Call Menu Added!");

						$.ajax({
							url: "./php/AddTelephonyIVR.php",
							type: 'POST',
							data: $("#create_ivr").serialize(),
							success: function(data) {
							  // console.log(data);
								  if(data == "success"){
									  $('.output-message-success').removeClass('hide');
									  $('.output-message-error').addClass('hide');
								  }
								  else{
									  $('.output-message-error').removeClass('hide');
									  $("#ivr_result").html(data); 
									  $('.output-message-success').addClass('hide');
								  }
							}
						});
					}
				});

				$("#add_phonenumbers").wizard({
					onfinish:function(){
					}
				});

				// ajax commands for modals -
				$('#submit_ingroup').click(function(){
				$.ajax({
					url: "./php/AddTelephonyIngroup.php",
					type: 'POST',
					data: $("#create_ingroup").serialize(),
					success: function(data) {
					  // console.log(data);
						  if(data == "success"){
							  $('.output-message-success').removeClass('hide');
							  $('.output-message-error').addClass('hide');
						  }
						  else{
							  $('.output-message-error').removeClass('hide');
							  $("#ingroup_result").html(data); 
							  $('.output-message-success').addClass('hide');
						  }
					}
				});
				});
				
				$('#submit_did').click(function(){
				$.ajax({
					url: "./php/AddTelephonyPhonenumber.php",
					type: 'POST',
					data: $("#create_phonenumber").serialize(),
					success: function(data) {
					  // console.log(data);
						  if(data == "success"){
							  $('.output-message-success').removeClass('hide');
							  $('.output-message-error').addClass('hide');
							  $('.output-message-incomplete').addClass('hide');
						  }
						  else if(data == "incomplete"){
							  $('.output-message-incomplete').removeClass('hide');
							  $('.output-message-success').addClass('hide');
							  $('.output-message-error').addClass('hide');
                          }
						  else{
							  $('.output-message-error').removeClass('hide');
							  $("#phonenumber_result").html(data); 
							  $('.output-message-success').addClass('hide');
							  $('.output-message-incomplete').addClass('hide');
						  }
					}
				});
				});
				

			      
				
				//EDIT INGROUP
				 $(".edit-ingroup").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="groupid" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				 //EDIT IVR
				 $(".edit-ivr").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="ivr" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				 //EDIT PHONENUMBER/DID
				 $(".edit-phonenumber").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="did" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				/**
				 * Delete Actions.
				 */
				//DELETE INGROUPS
				 $(".delete-ingroup").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var groupid = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { groupid: groupid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_list"); ?>"); }
						});
					}
				 });

				 //DELETE IVR
				 $(".delete-ivr").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var menu_id = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { ivr: menu_id } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_ivr"); ?>"); }
						});
					}
				 });

				 //DELETE PHONENUMBER/DID
				 $(".delete-phonenumber").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var did = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { did: did } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_phonenumber"); ?>"); }
						});
					}
				 });

				$('.add-option').click(function(){
					var toClone = $('.to-clone-opt').clone();

					toClone.removeClass('to-clone-opt');
					toClone.find('label.control-label').text('');
					toClone.find('.btn-remove').append('<span class="fa fa-remove fa-2x text-red remove-row"></span>');

					$('.cloning-area').append(toClone);
				});

				$(document).on('click', '.remove-row', function(){
					var row = $(this).parent().parent();
					
					row.remove();
				});

				
			});
		</script>
    </body>
</html>
