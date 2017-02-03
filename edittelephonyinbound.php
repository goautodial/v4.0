<?php

	###################################################
	### Name: edittelephonyinbound.php 	  ###
	### Functions: Edit Inbound, IVR & DID  	  ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	  ###
	### Version: 4.0 	  ###
	### Written by: Alexander Jim H. Abenoja	  ###
	### License: AGPLv2	  ###
	###################################################
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
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

$groupid = NULL;
if (isset($_POST["groupid"])) {
	$groupid = $_POST["groupid"];
}

$ivr = NULL;
if (isset($_POST["ivr"])) {
	$ivr = $_POST["ivr"];
}

$did = NULL;
if (isset($_POST["did"])) {
	$did = $_POST["did"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit 
        	<?php 
        		if($groupid != NULL){echo "In-Group";}
        		if($ivr != NULL){echo "Interactive Voice Record";}
        		if($did != NULL){echo "DID/Phone Number";}
        	?>
        </title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       
       <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>
        
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
  		<!-- SELECT2 CSS -->
        <link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
        <link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
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
                    	Inbound
                        <small>Edit 
                        	<?php 
				        		if($groupid != NULL){echo "In-Group";}
				        		if($ivr != NULL){echo "Interactive Voice Record";}
				        		if($did != NULL){echo "DID/Phone Number";}
					        ?>
					    </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("Telephony"); ?></li>
                        <?php
							if($groupid != NULL || $ivr != NULL || $did != NULL){
						?>	
							<li><a href="./telephonyinbound.php"><?php $lh->translateText("inbound"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

					<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;
					
					// IF INGROUP
					if($groupid != NULL) {

					/* APIs used for forms */
						$call_menu = $ui->API_getIVR();
						$call_time = $ui->getCalltimes();
						$scripts = $ui->API_goGetAllScripts();
						$voicemail = $ui->API_goGetVoiceMails();
						$ingroup = $ui->API_getInGroups();
						$voicefiles = $ui->API_GetVoiceFilesList();
						$moh = $ui->API_goGetAllMusicOnHold();

						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetInboundInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["group_id"] = $groupid; #Desired list id. (required)
            
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_TIMEOUT, 100);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
						$data = curl_exec($ch);
						curl_close($ch);
						$output = json_decode($data);
						//var_dump($output);

						if ($output->result=="success") {
						# Result was OK!
					?>			
				<!-- Main content -->
                 <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend>MODIFY IN-GROUP : <u><?php echo $groupid;?></u></legend>

							<form id="modifyingroup">
								<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
								<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />

							<div role="tabpanel">
								<ul role="tablist" class="nav nav-tabs nav-justified">
								 <!-- Settings panel tabs-->
									 <li role="presentation" class="active">
										<a href="#settings" data-toggle="tab">
										Basic Settings</a>
									 </li>
								<!-- Advanced settings tab -->
									 <li role="presentation">
										<a href="#advanced_settings" data-toggle="tab">
										Advanced Settings </a>
									 </li>
								<!-- Agents tab -->
									 <li role="presentation">
										<a href="#agents" data-toggle="tab">
										Agents </a>
									 </li>
								</ul>		

								<!-- Tab panes-->
								<div class="tab-content">

									<!--==== Settings ====-->
									<div id="settings" class="tab-pane fade in active">
										<input type="hidden" name="modify_groupid" value="<?php echo $groupid;?>">
										
										<!-- BASIC SETTINGS -->
										<fieldset>
											<div class="form-group mt">
												<label for="description" class="col-sm-3 control-label"> Description </label>
												<div class="col-sm-9 mb">
													<input type="text" class="form-control" name="desc" id="description" value="<?php echo $output->data->group_name;?>">
												</div>
											</div>
											<div class="form-group">
												<?php $output->data->group_color = "#".$output->data->group_color;?>
												<label for="color" class="col-sm-3 control-label"> Color </label>
												<div class="col-sm-9 mb">
									                <input type="text" class="form-control colorpicker" name="color" id="color" value="<?php echo $output->data->group_color;?>">
												</div>
											</div>
											<div class="form-group">
												<label for="status" class="col-sm-3 control-label"> Status </label>
												<div class="col-sm-9 mb">
													<select class="form-control" name="status" id="status">
													<?php
														$status = NULL;
														if($output->data->active == "Y"){
															$status .= '<option value="Y" selected> Active </option>';
														}else{
															$status .= '<option value="Y" > Active </option>';
														}
														
														if($output->data->active == "N"){
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
												<label for="webform" class="col-sm-3 control-label">Web Form</label>
												<div class="col-sm-9 mb">
													<input type="text" class="form-control" name="webform" id="webform" value="<?php echo $output->data->web_form_address;?>">
												</div>
											</div>
											<div class="form-group">
												<label for="nextagent" class="col-sm-3 control-label">Next Agent Call</label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="nextagent" name="nextagent">
														<?php
															$next = NULL;
															if($output->data->next_agent_call == "random"){
																$next .= '<option value="random" selected> random </option>';
															}else{
																$next .= '<option value="random" > random </option>';
															}
															
															if($output->data->next_agent_call == "oldest_call_start"){
																$next .= '<option value="oldest_call_start" selected> oldest_call_start </option>';
															}else{
																$next .= '<option value="oldest_call_start" > oldest_call_start </option>';
															}
															
															if($output->data->next_agent_call == "oldest_call_finish"){
																$next .= '<option value="oldest_call_finish" selected> oldest_call_finish </option>';
															}else{
																$next .= '<option value="oldest_call_finish" > oldest_call_finish </option>';
															}
															
															if($output->data->next_agent_call == "overall_user_level"){
																$next .= '<option value="overall_user_level" selected> overall_user_level </option>';
															}else{
																$next .= '<option value="overall_user_level" > overall_user_level </option>';
															}
															
															if($output->data->next_agent_call == "ingroup_rank"){
																$next .= '<option value="ingroup_rank" selected> ingroup_rank </option>';
															}else{
																$next .= '<option value="ingroup_rank" > ingroup_rank </option>';
															}
															/*
															if($output->data->next_agent_call == "campaign_rank"){
																$next .= '<option value="campaign_rank" selected> campaign_rank </option>';
															}else{
																$next .= '<option value="campaign_rank" > campaign_rank </option>';
															}
															*/
															if($output->data->next_agent_call == "fewest_calls"){
																$next .= '<option value="fewest_calls" selected> fewest_calls </option>';
															}else{
																$next .= '<option value="fewest_calls" > fewest_calls </option>';
															}
															
															if($output->data->next_agent_call == "fewest_calls_campaign"){
																$next .= '<option value="fewest_calls_campaign" selected> fewest_calls_campaign </option>';
															}else{
																$next .= '<option value="fewest_calls_campaign" > fewest_calls_campaign </option>';
															}
															
															if($output->data->next_agent_call == "longest_wait_time"){
																$next .= '<option value="longest_wait_time" selected> longest_wait_time </option>';
															}else{
																$next .= '<option value="longest_wait_time" > longest_wait_time </option>';
															}
															
															if($output->data->next_agent_call == "ring_all"){
																$next .= '<option value="ring_all" selected> ring_all </option>';
															}else{
																$next .= '<option value="ring_all" > ring_all </option>';
															}
															echo $next;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="priority" class="col-sm-3 control-label">Queue Priority</label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="priority" name="priority">
														<?php
														$prio = NULL;
															for($a=99; $a >= -99; $a--){
							                                    $a_desc = "";
							                                   
							                                   if($a < 0){
							                                       $a_desc = "Lower";
							                                   }
							                                   if($a == 0){
							                                       $a_desc = "Even";
							                                   }
							                                   if($a > 0){
							                                       $a_desc = "Higher";
							                                   }
							                                       if($output->data->queue_priority == $a){
							                                           $prio .= '<option value="'.$a.'" selected> '.$a.'  -  '.$a_desc.' </option>';
							                                       }else{
							                                           $prio .= '<option value="'.$a.'">'.$a.'  -  '.$a_desc.' </option>';
																}
							                                }
							                                echo $prio;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="display" class="col-sm-3 control-label">Fronter Display</label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="display" name="display">
														<?php
														$display = NULL;
															if($output->data->fronter_display == "Y"){
																$display .= '<option value="Y" selected> YES </option>';
															}else{
																$display .= '<option value="Y" > YES </option>';
															}
															
															if($output->data->fronter_display == "N"){
																$display .= '<option value="N" selected> NO </option>';
															}else{
																$display .= '<option value="N" > NO </option>';
															}
														echo $display;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="script" class="col-sm-3 control-label">Script</label>
												<div class="col-sm-9 mb">
													<select class="form-control select2" id="script" name="script">
														<?php
														$script = NULL;

															if($output->data->ingroup_script == NULL){
																$script .= '<option value="NONE" selected> NONE </option>';
															}else{
																$script .= '<option value="NONE" > NONE </option>';
															}

															for($x=0; $x<count($scripts->script_id);$x++){
																if($output->data->ingroup_script == $scripts->script_id[$x]){
																	$script .= '<option value="'.$scripts->script_id[$x].'" selected> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
																}else{
																	$script .= '<option value="'.$scripts->script_id[$x].'"> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
																}

															}
														echo $script;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="drop_call_seconds" class="col-sm-3 control-label">Drop Call Seconds</label>
												<div class="col-sm-9 mb">
													<input type="number" class="form-control" name="drop_call_seconds" id="drop_call_seconds" value="<?php echo $output->data->drop_call_seconds;?>">
												</div>
											</div>
											<div class="form-group">
												<label for="drop_action" class="col-sm-3 control-label">Drop Action</label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="drop_action" name="drop_action">
														<?php
														$drop_action = NULL;
															if($output->data->drop_action == "HANGUP"){
																$drop_action .= '<option value="HANGUP" selected> HANGUP </option>';
															}else{
																$drop_action .= '<option value="HANGUP" > HANGUP </option>';
															}
															
															if($output->data->drop_action == "MESSAGE"){
																$drop_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															}else{
																$drop_action .= '<option value="MESSAGE" > MESSAGE </option>';
															}

															if($output->data->drop_action == "VOICEMAIL"){
																$drop_action .= '<option value="VOICEMAIL" selected> VOICEMAIL </option>';
															}else{
																$drop_action .= '<option value="VOICEMAIL" > VOICEMAIL </option>';
															}

															if($output->data->drop_action == "IN_GROUP"){
																$drop_action .= '<option value="IN_GROUP" selected> IN_GROUP </option>';
															}else{
																$drop_action .= '<option value="IN_GROUP" > IN_GROUP </option>';
															}

															if($output->data->drop_action == "CALLMENU"){
																$drop_action .= '<option value="CALLMENU" selected> CALLMENU </option>';
															}else{
																$drop_action .= '<option value="CALLMENU" > CALLMENU </option>';
															}
															/*
															if($output->data->drop_action == "VMAIL_NO_INST"){
																$drop_action .= '<option value="VMAIL_NO_INST" selected> VMAIL_NO_INST </option>';
															}else{
																$drop_action .= '<option value="VMAIL_NO_INST" > VMAIL_NO_INST </option>';
															}*/
														echo $drop_action;
														?>
													</select>
												</div>
											</div>

											<!-- DROP EXTEN -->
												<div class="form-group drop_action_exten">

													<!-- IF MESSAGE IS SELECTED -->
														<div class="drop_exten_message" <?php if($output->data->drop_action != "MESSAGE"){?> style="display:none;"<?php }?> >
															<label for="drop_exten" class="col-sm-3 control-label">Drop Exten</label>
															<div class="col-sm-9 mb">
																<input type="number" class="form-control" name="drop_exten" id="drop_exten" value="<?php echo $output->data->drop_exten;?>" />
															</div>
														</div><!-- /. message -->
													
													<!-- IF VOICEMAIL IS SELECTED -->
														<div class="drop_exten_voicemail" <?php if($output->data->drop_action != "VOICEMAIL"){?> style="display:none;" <?php }?> >
															<label for="voicemail_ext" class="col-sm-3 control-label">Voicemail</label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="voicemail_ext" name="voicemail_ext" style="width:100%;">
																	<?php
																		$drop_action_voicemail = NULL;
																			for($x=0; $x < count($voicemail->voicemail_id);$x++){
																				if($output->data->voicemail_ext == $voicemail->voicemail_id[$x]){
																					$drop_action_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'" selected> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																				}else{
																					$drop_action_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'"> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																				}
																			}
																		echo $drop_action_voicemail;
																	?>
																</select>
															</div>
														</div><!-- /. voicemail -->

													<!-- IF IN_GROUP IS SELECTED -->
														<div class="drop_exten_ingroup" <?php if($output->data->drop_action != "IN_GROUP"){ ?>style="display:none;"<?php }?>>
															<label for="drop_inbound_group" class="col-sm-3 control-label">Drop Transfer Group </label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="drop_inbound_group" name="drop_inbound_group" style="width:100%;">
																	<?php
																		$drop_action_ingroup = NULL;
																			for($x=0; $x<count($ingroup->group_id);$x++){
																				if($output->data->drop_inbound_group == $ingroup->voicemail_id[$x]){
																					$drop_action_ingroup .= '<option value="'.$ingroup->group_id[$x].'" selected> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																				}else{
																					$drop_action_ingroup .= '<option value="'.$ingroup->group_id[$x].'"> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																				}
																			}
																		echo $drop_action_ingroup;
																	?>
																</select>
															</div>
														</div><!-- /. ingroup -->

													<!-- IF CALLMENU IS SELECTED -->
														<div class="drop_exten_callmenu" <?php if($output->data->drop_action != "CALLMENU"){ ?>style="display:none;"<?php }?>>
															<label for="drop_callmenu" class="col-sm-3 control-label">Drop Callmenu </label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="drop_callmenu" name="drop_callmenu" style="width:100%;">
																	<?php
																		$drop_exten_callmenu = NULL;
																			for($x=0; $x < count($call_menu->menu_id);$x++){
																				if($output->data->drop_callmenu == $call_menu->menu_id[$x]){
																					$drop_exten_callmenu .= '<option value="'.$call_menu->menu_id[$x].'" selected> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																				}else{
																					$drop_exten_callmenu .= '<option value="'.$call_menu->menu_id[$x].'"> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																				}
																			}
																		echo $drop_exten_callmenu;
																	?>
																</select>
															</div>
														</div><!-- /. callmenu -->
												</div>

											<div class="form-group">
												<label for="call_time_id" class="col-sm-3 control-label">Call Time</label>
												<div class="col-sm-9 mb">
													<select class="form-control select2" id="call_time_id" name="call_time_id">
														<?php
														$call_time_id = NULL;
															if($call_time->call_time_id[0] == NULL){
																$call_time_id .= '<option value="NONE" selected> NONE </option>';
															}else{
																$call_time_id .= '<option value="NONE" > NONE </option>';
																for($x=0; $x<count($call_time->call_time_id);$x++){
																	if($output->data->call_time_id == $call_time->call_time_id[$x]){
																		$call_time_id .= '<option value="'.$call_time->call_time_id[$x].'" selected> '.$call_time->call_time_id[$x].' - '.$call_time->call_time_name[$x].' </option>';
																	}else{
																		$call_time_id .= '<option value="'.$call_time->call_time_id[$x].'"> '.$call_time->call_time_id[$x].' - '.$call_time->call_time_name[$x].' </option>';
																	}
																}
															}
														echo $call_time_id;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="call_launch" class="col-sm-3 control-label">Get Call Launch</label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="call_launch" name="call_launch">
														<?php
														$call_launch = NULL;
															if($output->data->get_call_launch == "NONE"){
																$call_launch .= '<option value="NONE" selected> NONE </option>';
															}else{
																$call_launch .= '<option value="NONE" > NONE </option>';
															}
																
															if($output->data->get_call_launch == "SCRIPT"){
																$call_launch .= '<option value="SCRIPT" selected> SCRIPT </option>';
															}else{
																$call_launch .= '<option value="SCRIPT" > SCRIPT </option>';
															}

															if($output->data->get_call_launch == "WEBFORM"){
																$call_launch .= '<option value="WEBFORM" selected> WEBFORM </option>';
															}else{
																$call_launch .= '<option value="WEBFORM" > WEBFORM </option>';
															}

															if($output->data->get_call_launch == "FORM"){
																$call_launch .= '<option value="FORM" selected> FORM </option>';
															}else{
																$call_launch .= '<option value="FORM" > FORM </option>';
															}
														echo $call_launch;
														?>
													</select>
												</div>
											</div>
												<!-- IN_GROUP  
													<div class="form-group">
														<label for="afterhours_xfer_group" class="col-sm-3 control-label">After Hours Transfer Group </label>
														<div class="col-sm-9 mb">
															<select class="form-control select2" id="afterhours_xfer_group" name="afterhours_xfer_group" style="width:100%;">
																<?php
																/*
																	$after_hour_ingroup = NULL;
																		for($x=0; $x<count($ingroup->group_id);$x++){									
																			if($output->data->afterhours_xfer_group == $ingroup->group_id[$x]){
																				$after_hour_ingroup .= '<option value="'.$ingroup->group_id[$x].'" selected> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																			}else{
																				$after_hour_ingroup .= '<option value="'.$ingroup->group_id[$x].'"> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																			}
																		}
																	echo $after_hour_ingroup;
																*/
																?>
															</select>
														</div>
													</div>
												<!-- /. ingroup -->
									    </fieldset>
									</div>

									<!-- ADVANCED SETTINGS -->
									<div id="advanced_settings" class="tab-pane fade in">
										<fieldset>
											<div class="form-group">
												<label for="welcome_message_filename" class="col-sm-4 control-label">Welcome Message Filename</label>
												<div class="col-sm-8 mb">
													<div class="input-group">
														<input type="text" class="form-control" class="" id="welcome_message_filename" name="welcome_message_filename" value="<?php if($output->data->welcome_message_filename == NULL )echo "sip-silence"; else echo $output->data->welcome_message_filename;?>">
														<span class="input-group-btn">
															<button class="btn btn-default show_welcome_message_filename" type="button">[Audio Chooser...]</button>
														</span>
													</div><!-- /input-group -->
													<div class="row col-sm-12 select_welcome_message_filename">
														<select class="form-control select2" id="select_welcome_message_filename" style="width:100%;">
															<option value="sip-silence">- - - Default - - -</option>
															<?php
																$welcome_message_filename = NULL;
																	for($x=0; $x < count($voicefiles->file_name);$x++){
																		$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																		if($output->data->welcome_message_filename == $this_file_name){
																			$welcome_message_filename .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																		}else{
																			$welcome_message_filename .= '<option value="'.$this_file_name.'"> '.$this_file_name.' </option>';
																		}
																	}
																echo $welcome_message_filename;
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="play_welcome_message" class="col-sm-4 control-label">Play Welcome Message</label>
												<div class="col-sm-8 mb">
													<select class="form-control" id="play_welcome_message" name="play_welcome_message">
														<?php
														$play_welcome_message = NULL;
															if($output->data->play_welcome_message == "ALWAYS"){
																$play_welcome_message .= '<option value="ALWAYS" selected> ALWAYS </option>';
															}else{
																$play_welcome_message .= '<option value="ALWAYS" > ALWAYS </option>';
															}
															
															if($output->data->play_welcome_message == "NEVER"){
																$play_welcome_message .= '<option value="NEVER" selected> NEVER </option>';
															}else{
																$play_welcome_message .= '<option value="NEVER" > NEVER </option>';
															}
	
															if($output->data->play_welcome_message == "IF_WAIT_ONLY"){
																$play_welcome_message .= '<option value="IF_WAIT_ONLY" selected> IF_WAIT_ONLY </option>';
															}else{
																$play_welcome_message .= '<option value="IF_WAIT_ONLY" > IF_WAIT_ONLY </option>';
															}
	
															if($output->data->play_welcome_message == "YES_UNLESS_NODELAY"){
																$play_welcome_message .= '<option value="YES_UNLESS_NODELAY" selected> YES_UNLESS_NODELAY </option>';
															}else{
																$play_welcome_message .= '<option value="YES_UNLESS_NODELAY" > YES_UNLESS_NODELAY </option>';
															}
	
														echo $play_welcome_message;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="moh_context" class="col-sm-4 control-label">Music On Hold Context</label>
												<div class="col-sm-8 mb">
													<div class="input-group">
														<input type="text" class="form-control" id="moh_context" name="moh_context" value="<?php if($output->data->moh_context == NULL)echo "default"; else echo $output->data->moh_context;?>">
														<span class="input-group-btn">
															<button class="btn btn-default show_moh_context" type="button">[Audio Chooser...]</button>
														</span>
													</div><!-- /input-group -->
													<div class="row col-sm-12 select_moh_context">
														<select class="form-control select2" id="select_moh_context" style="width:100%;">
															<option value="default">- - - Default - - -</option>
															<?php
																$moh_context = NULL;
																	for($x=0; $x < count($moh->moh_id);$x++){
																		if($moh->moh_id[$x] != "default"){
																			if($output->data->moh_context == $moh->moh_id[$x]){
																				$moh_context .= '<option value="'.$moh->moh_id[$x].'" selected> '.$moh->moh_name[$x].' </option>';
																			}else{
																				$moh_context .= '<option value="'.$moh->moh_id[$x].'"> '.$moh->moh_name[$x].' </option>';
																			}
																		}
																	}
																echo $moh_context;
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="onhold_prompt_filename" class="col-sm-4 control-label">On Hold Prompt Filename</label>
												<div class="col-sm-8 mb mb">
													<div class="input-group">
														<input type="text" class="form-control" id="onhold_prompt_filename" name="onhold_prompt_filename" value="<?php if($output->data->onhold_prompt_filename == NULL)echo "generic_hold"; else echo $output->data->onhold_prompt_filename;?>">
														<span class="input-group-btn">
															<button class="btn btn-default show_onhold_prompt_filename" type="button">[Audio Chooser...]</button>
														</span>
													</div><!-- /input-group -->
													<div class="row col-sm-12 select_onhold_prompt_filename">
														<select class="form-control select2" id="select_onhold_prompt_filename" style="width:100%;">
															<option value="generic_hold">- - - Default - - -</option>
															<?php
																$onhold_prompt_filename = NULL;
																	for($x=0; $x < count($voicefiles->file_name);$x++){
																		$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																		if($output->data->onhold_prompt_filename == $this_file_name){
																			$onhold_prompt_filename .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																		}else{
																			$onhold_prompt_filename .= '<option value="'.$this_file_name.'"> '.$this_file_name.' </option>';
																		}
																	}
																echo $onhold_prompt_filename;
															?>
														</select>
													</div>
													<br/>
												</div>
											</div>
											
											<div class="form-group mt">
												<label for="after_hours_action" class="col-sm-4 control-label">After Hours Action</label>
												<div class="col-sm-8 mb">
													<select class="form-control" id="after_hours_action" name="after_hours_action">
														<?php
														$after_hours_action = NULL;
															
															if($output->data->after_hours_action == "MESSAGE"){
																$after_hours_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															}else{
																$after_hours_action .= '<option value="MESSAGE" > MESSAGE </option>';
															}
															
															if($output->data->after_hours_action == "HANGUP"){
																$after_hours_action .= '<option value="HANGUP" selected> HANGUP </option>';
															}else{
																$after_hours_action .= '<option value="HANGUP" > HANGUP </option>';
															}
		
															if($output->data->after_hours_action == "EXTENSION"){
																$after_hours_action .= '<option value="EXTENSION" selected> EXTENSION </option>';
															}else{
																$after_hours_action .= '<option value="EXTENSION" > EXTENSION </option>';
															}
		
															if($output->data->after_hours_action == "VOICEMAIL"){
																$after_hours_action .= '<option value="VOICEMAIL" selected> VOICEMAIL </option>';
															}else{
																$after_hours_action .= '<option value="VOICEMAIL" > VOICEMAIL </option>';
															}
															/*
															if($output->data->after_hours_action == "IN_GROUP"){
																$after_hours_action .= '<option value="IN_GROUP" selected> IN_GROUP </option>';
															}else{
																$after_hours_action .= '<option value="IN_GROUP" > IN_GROUP </option>';
															}*/
													echo $after_hours_action;
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="after_hours_exten" class="col-sm-4 control-label">After Hours Message Filename</label>
											<div class="col-sm-8 mb">
												<div class="input-group">
													<input type="text" class="form-control" id="after_hours_message_filename" name="after_hours_message_filename" value="<?php if($output->data->after_hours_message_filename == NULL)echo "vm-goodbye"; else echo $output->data->after_hours_message_filename;?>">
													<span class="input-group-btn">
														<button class="btn btn-default show_after_hours_message_filename" type="button">[Audio Chooser...]</button>
													</span>
												</div><!-- /input-group -->
												<div class="row col-sm-12 select_after_hours_message_filename">
													<select class="form-control select2" id="select_after_hours_message_filename" style="width:100%;">
														<option value="vm-goodbye">- - - Default - - -</option>
														<?php
															$after_hours_message_filename = NULL;
																for($x=0; $x < count($voicefiles->file_name);$x++){
																	$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																	if($output->data->after_hours_message_filename == $this_file_name){
																		$after_hours_message_filename .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																	}else{
																		$after_hours_message_filename .= '<option value="'.$this_file_name.'"> '.$this_file_name.' </option>';
																	}
																}
															echo $after_hours_message_filename;
														?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group">
												<label for="after_hours_exten" class="col-sm-4 control-label">After Hours Extension</label>
												<div class="col-sm-8 mb">
													<input type="number" class="form-control" name="after_hours_exten" id="after_hours_exten" value="<?php if($output->data->after_hours_exten != NULL)echo $output->data->after_hours_exten; else echo "8300";?>" />
												</div>
											</div>
										<div class="form-group">
											<label for="after_hours_voicemail" class="col-sm-4 control-label">After Hours Voicemail</label>
											<div class="col-sm-8 mb">
												<div class="input-group">
													<input type="text" class="form-control" id="after_hours_voicemail" name="after_hours_voicemail" value="<?php if($output->data->after_hours_voicemail == NULL)echo ""; else echo $output->data->after_hours_voicemail;?>">
													<span class="input-group-btn">
														<button class="btn btn-default show_after_hours_voicemail" type="button">[Audio Chooser...]</button>
													</span>
												</div><!-- /input-group -->
												<div class="row col-sm-12 select_after_hours_voicemail">
													<select class="form-control select2" id="select_after_hours_voicemail" style="width:100%;">
														<option value=""> - - - NONE - - - </option>
														<?php
															$after_hour_voicemail = NULL;
																for($x=0; $x < count($voicemail->voicemail_id);$x++){
																	if($output->data->after_hours_voicemail == $voicemail->voicemail_id[$x]){
																		$after_hour_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'" selected> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																	}else{
																		$after_hour_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'"> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																	}
																}
															echo $after_hour_voicemail;
														?>
													</select>
												</div>
												<br/>
											</div>
										</div>
										
							       			<div class="form-group">
							       				<label for="no_agent_no_queue" class="col-sm-4 control-label">Accept Calls when there are No Available Agents?</label>
							       				<div class="col-sm-8 mb">
												<select class="form-control" id="no_agent_no_queue" name="no_agent_no_queue">
													<?php
													$no_agent_no_queue = NULL;
														if($output->no_agent_no_queue[$i] == "N"){
															$no_agent_no_queue .= '<option value="N" selected> NO </option>';
														}else{
															$no_agent_no_queue .= '<option value="N" > NO </option>';
														}
														
														if($output->no_agent_no_queue[$i] == "Y"){
															$no_agent_no_queue .= '<option value="Y" selected> YES </option>';
														}else{
															$no_agent_no_queue .= '<option value="Y" > YES </option>';
														}

														if($output->no_agent_no_queue[$i] == "NO_PAUSED"){
															$no_agent_no_queue .= '<option value="NO_PAUSED" selected> NO PAUSED </option>';
														}else{
															$no_agent_no_queue .= '<option value="NO_PAUSED" > NO PAUSED </option>';
														}
													echo $no_agent_no_queue;
													?>
												</select>
											</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="no_agent_action" class="col-sm-4 control-label">No Available Agents Routing</label>
							       				<div class="col-sm-8 mb">
												<select class="form-control" id="no_agent_action" name="no_agent_action">
													<?php
														$no_agent_action = NULL;
															if($output->data->no_agent_action == "HANGUP"){
																$no_agent_action .= '<option value="HANGUP" selected> HANGUP </option>';
															}else{
																$no_agent_action .= '<option value="HANGUP" > HANGUP </option>';
															}
															if($output->data->no_agent_action == "MESSAGE"){
																$no_agent_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															}else{
																$no_agent_action .= '<option value="MESSAGE" > MESSAGE </option>';
															}
															if($output->data->no_agent_action == "VOICEMAIL"){
																$no_agent_action .= '<option value="VOICEMAIL" selected> VOICEMAIL </option>';
															}else{
																$no_agent_action .= '<option value="VOICEMAIL" > VOICEMAIL </option>';
															}
															if($output->data->no_agent_action == "IN_GROUP"){
																$no_agent_action .= '<option value="IN_GROUP" selected> IN_GROUP </option>';
															}else{
																$no_agent_action .= '<option value="IN_GROUP" > IN_GROUP </option>';
															}
															if($output->data->no_agent_action == "CALLMENU"){
																$no_agent_action .= '<option value="CALLMENU" selected> CALLMENU </option>';
															}else{
																$no_agent_action .= '<option value="CALLMENU" > CALLMENU </option>';
															}
														echo $no_agent_action;
													?>
												</select>
											</div>
							       			</div
											
							       			<!-- NO AGENTS EXTEN -->
												<div class="form-group no_agents_exten">
													<!-- IF MESSAGE IS SELECTED -->
														<div class="no_agents_message" <?php if($output->data->no_agent_action != "MESSAGE"){?> style="display:none;"<?php }?> >
															<label for="no_agents_exten" class="col-sm-4 control-label">Audio File</label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_exten" name="no_agents_exten" value="<?php if($output->data->no_agents_exten == NULL)echo "vm-goodbye"; else echo $output->data->no_agents_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_exten" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_exten">
																	<select class="form-control select2-2" id="select_no_agents_exten" style="width:100%;">
																		<option value="vm-goodbye">- - - Default - - -</option>
																		<?php
																			$no_agents_exten = NULL;
																				for($x=0; $x < count($voicefiles->file_name);$x++){
																					$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																					if($output->data->no_agent_action_value == $this_file_name){
																						$no_agents_exten .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																					}else{
																						$no_agents_exten .= '<option value="'.$this_file_name.'"> '.$this_file_name.' </option>';
																					}
																				}
																			echo $no_agents_exten;
																		?>
																	</select>
																</div>
															</div>
														</div><!-- /. message -->
													
													<!-- IF VOICEMAIL IS SELECTED -->
														<div class="no_agents_voicemail" <?php if($output->data->no_agent_action != "VOICEMAIL"){?> style="display:none;" <?php }?> >
															<label for="no_agents_voicemail" class="col-sm-4 control-label">Voicemail</label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_voicemail" name="no_agents_voicemail" value="<?php if($output->data->no_agents_voicemail == NULL)echo ""; else echo $output->data->no_agents_voicemail;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_voicemail" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_voicemail">
																	<select class="form-control select2-2" id="select_no_agents_voicemail" style="width:100%;">
																		<option value="">- - - NONE - - -</option>
																		<?php
																			$no_agents_voicemail = NULL;
																				for($x=0; $x < count($voicemail->voicemail_id);$x++){
																					if($output->data->no_agent_action_value == $voicemail->voicemail_id[$x]){
																						$no_agents_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'" selected> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																					}else{
																						$no_agents_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'"> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																					}
																				}
																			echo $no_agents_voicemail;
																		?>
																	</select>
																</div>
															</div>
														</div><!-- /. voicemail -->

													<!-- IF IN_GROUP IS SELECTED -->
														<div class="no_agents_ingroup" <?php if($output->data->no_agent_action != "IN_GROUP"){ ?>style="display:none;"<?php }?>>
															<label for="no_agents_ingroup" class="col-sm-4 control-label">In-Group </label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_ingroup" name="no_agents_ingroup" value="<?php if($output->data->no_agents_ingroup == NULL)echo ""; else echo $output->data->no_agents_ingroup;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_ingroup" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_ingroup">
																	<select class="form-control select2-2" id="select_no_agents_ingroup" style="width:100%;">
																		<option value="">- - - NONE - - -</option>
																		<?php
																			$no_agents_ingroup = NULL;
																				for($x=0; $x<count($ingroup->group_id);$x++){
																					if($output->data->no_agent_action_value == $ingroup->group_id[$x]){
																						$no_agents_ingroup .= '<option value="'.$ingroup->group_id[$x].'" selected> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																					}else{
																						$no_agents_ingroup .= '<option value="'.$ingroup->group_id[$x].'"> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																					}
																				}
																			echo $no_agents_ingroup;
																		?>
																	</select>
																</div>
															</div>
														</div><!-- /. ingroup -->

													<!-- IF CALLMENU IS SELECTED -->
														<div class="no_agents_callmenu" <?php if($output->data->no_agent_action != "CALLMENU"){ ?>style="display:none;"<?php }?>>
															<label for="no_agents_callmenu" class="col-sm-4 control-label">Callmenu </label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_callmenu" name="no_agents_callmenu" value="<?php if($output->data->no_agents_callmenu == NULL)echo ""; else echo $output->data->no_agents_callmenu;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_callmenu" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_callmenu">
																	<select class="form-control select2-2" id="select_no_agents_callmenu" style="width:100%;">
																		<option value="">- - - NONE - - -</option>
																		<?php
																			$no_agents_callmenu = NULL;
																				for($x=0; $x < count($call_menu->menu_id);$x++){
																					if($output->data->no_agent_action_value == $call_menu->menu_id[$x]){
																						$no_agents_callmenu .= '<option value="'.$call_menu->menu_id[$x].'" selected> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																					}else{
																						$no_agents_callmenu .= '<option value="'.$call_menu->menu_id[$x].'"> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																					}
																				}
																			echo $no_agents_callmenu;
																		?>
																	</select>
																</div>
															</div>
														</div><!-- /. callmenu -->
												</div>
											<!-- /.NO AGENTS EXTEN -->
							       		</fieldset>
									</div>

									<fieldset class="footer-buttons" id="not_agent_rank">
			                           <div class="col-sm-3 pull-right">
										<a href="telephonyinbound.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
									    <a type="submit" class="btn btn-primary" id="modifyInboundOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></a>
			                           </div>
				                    </fieldset>
								</form>
								
									<!--==== Agent Rank Table ====-->
									<div id="agents" class="tab-pane fade in">

									<form id="agentrankform" class="form-horizontal">
										<?php
											$agents_rank = $ui->API_goGetAllAgentRank($user->getUserId(), $groupid);
										?>
										<table class="table table-striped table-bordered table-hover" id="agent_rank_table">
										   <thead>
											  <tr>
												 <th>User</th>
												 <th>User Group</th>
												 <th>Selected</th>
												 <th>Rank</th>
												 <th>Grade</th>
												 <!--<th>Calls Today</th>-->
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		$count = count($agents_rank->user);
											   		//var_dump($agents_rank->dropdown_rankdefvalues[0]);


											   		for($a=0; $a < $count; $a++){
											   			
											   			$checkbox_fields = $agents_rank->checkbox_fields[$a];
											   			$ischecked = $agents_rank->checkbox_ischecked[$a];
											   			
											   			$rank_fields = $agents_rank->rank_fields[$a];
											   			$rank_value = $agents_rank->values_rank[$a];

											   			$grade_fields = $agents_rank->grade_fields[$a];
											   			$grade_value = $agents_rank->values_grade[$a];
											   			
											   	?>	
													<tr>
														<td><?php echo $agents_rank->user[$a].' - '.$agents_rank->full_name[$a];?></td>
														<td><?php echo $agents_rank->user_group[$a];?></td>
														<td>
															<center>
																<label class="c-checkbox" for="<?php echo $checkbox_fields;?>">
																	<input type="checkbox" id="<?php echo $checkbox_fields;?>" name="<?php echo $checkbox_fields;?>" value="YES" <?php echo $ischecked;?> />
																	<span class="fa fa-check"></span>
																</label>
															</center>
														</td>
														<td>
															<select class="form-control" name="<?php echo $rank_fields;?>">
																<?php
																$b = 9;
																	while($b >= -9){
																?>
																	<option value="<?php echo $rank_value;?>" <?php if($rank_value == $b){ echo "selected";}?>> <?php echo $b;?></option>
																<?php
																	$b--;
																	}
																?>
															</select>
														</td>
														<td>
															<select class="form-control" name="<?php echo $grade_fields;?>">
																<?php
																$c = 10;
																	while($c >= 0){
																?>
																	<option value="<?php echo $grade_value;?>" <?php if($grade_value == $c){ echo "selected";}?>> <?php echo $c;?></option>
																<?php
																	$c--;
																	}
																?>
															</select>
														</td>
														<!--<td><?php echo $agents_rank->call_today[$a];?></td>-->
													</tr>
												<?php
													}
												?>
										   </tbody>
										</table>
									</form>
									<fieldset class="footer-buttons">
										<div class="box-footer">
										   <div class="col-sm-3 pull-right">
													<a href="telephonyinbound.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
											
													<a type="button" class="btn btn-primary" id="submit_agent_rank" data-id="<?php echo $groupid;?>"> <span id="submit_button"><i class="fa fa-check"></i> Submit</span></a>
												
										   </div>
										</div>
									</fieldset>
									</div>
								</div><!-- END tab content-->
							</div><!-- END of tabpanel -->
							
						</div><!-- body -->
					</div><!-- body -->
                </section>
					<?php
						} else {
						# An error occured
						echo $output->result;
						}
                        
					}else {
						$errormessage = $lh->translationFor("some_fields_missing");
					}
					
				// IF IVR
					if($ivr != NULL) {
						/*
						 * Displaying Interactive Voice Response Information
						 * [[API:Function]] – goGetIVRInfo
						 * Allows to retrieve some attributes of a given IVR menu. IVR menu should belong to the user that authenticated the request.
						 */

						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetIVRInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["menu_id"] = $ivr; #Desired menu id. (required)
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_TIMEOUT, 100);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
						$data = curl_exec($ch);
						curl_close($ch);
						$output = json_decode($data);

						//var_dump($output);

						if ($output->result=="success") {
							$user_groups = $ui->API_goGetUserGroupsList();
							$ingroups = $ui->API_getInGroups();
							$calltimes = $ui->getCalltimes();
							$ivr_options = $ui->API_getIVROptions($ivr);
							$campaign = $ui->API_getListAllCampaigns();
							$voicemails = $ui->API_goGetVoiceMails();
							$phones = $ui->API_getPhonesList();
							$ivr = $ui->API_getIVR();
							$scripts = $ui->API_goGetAllScripts();
							$voicefiles = $ui->API_GetVoiceFilesList();
							$calltimes = $ui->getCalltimes();
							$phonenumber = $ui->API_getPhoneNumber();
						# Result was OK!
					?>
						<section class="content">
							<div class="panel panel-default">
								<div class="panel-body">
									<legend>MODIFY IVR : <u><?php echo $output->data->menu_id;?></u></legend>

									<form id="modifyivr" class="form-horizontal">
										<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
										<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />

									<div role="tabpanel">
										<ul role="tablist" class="nav nav-tabs nav-justified">
										 <!-- Settings panel tabs-->
											 <li role="presentation" class="active">
												<a href="#tab_1" data-toggle="tab">
												Basic Settings</a>
											 </li>
										<!-- Options tab -->
											 <li role="presentation">
												<a href="#tab_2" data-toggle="tab">
												Options </a>
											 </li>
										</ul>
										<input type="hidden" name="modify_ivr" value="<?php echo $output->data->menu_id;?>">
										<div class="tab-content">
											<div class="tab-pane active" id="tab_1">
												<div class="form-group mt">
													<label class="col-sm-3 control-label" for="menu_id">Menu ID:</label>
													<div class="col-sm-9">
														<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID" minlength="4" required title="No Spaces. Minimum of 4 characters" value="<?php echo $output->data->menu_id;?>">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_name">Menu Name: </label>
													<div class="col-sm-9">
														<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name" required value="<?php echo $output->data->menu_name;?>">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_prompt">Menu Greeting: </label>
													<div class="col-sm-9">
														<select name="menu_prompt" id="menu_prompt" class="form-control select2" style="width:100%;">
															<option value="goWelcomeIVR">-- Default Value --</option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++){
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>" <?php if($file == $output->data->menu_prompt){echo "selected";}?> ><?php echo $file;?></option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_timeout">Menu Timeout: </label>
													<div class="col-sm-9">
														<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="<?php echo $output->data->menu_timeout;?>" min="0" required>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_timeout_prompt">Menu Timeout Greeting: </label>
													<div class="col-sm-9">
														<select name="menu_timeout_prompt" id="menu_timeout_prompt" class="form-control select2" style="width:100%;">
															<option value="">-- Default Value --</option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++){
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>" <?php if($file == $output->data->menu_timeout_prompt){echo "selected";}?> ><?php echo $file;?></option>
															<?php
																}
															?>				
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_invalid_prompt">Menu Invalid Greeting: </label>
													<div class="col-sm-9">
														<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control select2" style="width:100%;">
															<option value="">-- Default Value --</option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++){
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>" <?php if($file == $output->data->menu_invalid_prompt){echo "selected";}?> ><?php echo $file;?></option>
															<?php
																}
															?>				
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_repeat">Menu Repeat: </label>
													<div class="col-sm-9">
														<input type="number" name="menu_repeat" id="menu_repeat" class="form-control"value="<?php echo $output->data->menu_repeat;?>" min="0" required>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_time_check">Menu Time Check: </label>
													<div class="col-sm-9">
														<select name="menu_time_check" id="menu_time_check" class="form-control">
															<option value="0" <?php if($output->data->menu_time_check == "0"){echo "selected";}?> > 0 - No Realtime Tracking </option>
															<option value="1" <?php if($output->data->menu_time_check == "1"){echo "selected";}?> > 1 - Realtime Tracking </option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="call_time_id">Call Time: </label>
													<div class="col-sm-9">
														<select name="call_time_id" id="call_time_id" class="form-control select2" style="width:100%;">
															<?php
																for($x=0; $x<count($calltimes->call_time_id);$x++){
															?>
																<option value="<?php echo $calltimes->call_time_id[$x];?>" <?php if($calltimes->call_time_id[$x] == $output->data->call_time_id){echo "selected";} ?> > <?php echo $calltimes->call_time_id[$x].' - '.$calltimes->call_time_name[$x]; ?> </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="track_in_vdac">Track call in realtime report: </label>
													<div class="col-sm-9"> 
														<select name="track_in_vdac" id="track_in_vdac" class="form-control">
															<option value="0" <?php if($output->data->track_in_vdac == "0"){echo "selected";}?> >0 - No Realtime Tracking</option>
															<option value="1" <?php if($output->data->track_in_vdac == "1"){echo "selected";}?> >1 - Realtime Tracking</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="tracking_group">Tracking Groups: </label>
													<div class="col-sm-9">
														<select name="tracking_group" id="tracking_group" class="form-control select2" style="width:100%;">
														<?php
															for($i=0;$i<count($ingroups->group_id);$i++){
														?>
															<option value="<?php echo $ingroups->group_id[$i];?>" <?php if($ingroups->group_id[$i] == $output->data->tracking_group){echo "selected";}?> >
																<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
															</option>									
														<?php
															}
														?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="user_group">User Groups: </label>
													<div class="col-sm-9">
														<select id="user_group" class="form-control select2" name="user_group" style="width:100%;">
																<option value="---ALL---" <?php if($output->data->user_group == "---ALL---"){echo "selected";}?> > - - - ALL USER GROUPS - - - </option>
															<?php
																for($i=0;$i<count($user_groups->user_group);$i++){
															?>
																<option value="<?php echo $user_groups->user_group[$i];?>" <?php if($output->data->user_group == $user_groups->user_group[$i]){echo "selected";}?> >  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
											<!-- /.tab-pane -->
											<div class="tab-pane" id="tab_2">
												<div id="static_div">
												<?php
													
													 //echo "<pre>";
													 //var_dump($ivr_options);
													 //echo "</pre>";
													
													for($i=0;$i < 14; $i++){
												?>
												<div class="option_div_<?php echo $i;?>">
													<div class="form-group">
														<div class="col-lg-12">
															<div class="col-lg-2">
																Option:
																<select class="form-control route_option" name="option[]"
																<?php 
																	if($ivr_options->option_value[$i] != ""){
																		if($ivr_options->option_value[$i] == "#")
																			$ivr_options->option_value[$i] = "A";
																		if($ivr_options->option_value[$i] == "*")
																			$ivr_options->option_value[$i] = "B";
																		if($ivr_options->option_value[$i] == "TIMECHECK")
																			$ivr_options->option_value[$i] = "C";
																		if($ivr_options->option_value[$i] == "TIMEOUT")
																			$ivr_options->option_value[$i] = "D";
																		if($ivr_options->option_value[$i] == "INVALID")
																			$ivr_options->option_value[$i] = "E";

																		echo 'id="option_'.$ivr_options->option_value[$i].'" ';
																	}
																?> >
																	<option selected></option>
																	<?php
																	$option = '';
																		for($x=0; $x <= 9; $x++){
																			$option .= '<option value="'.$x.'" ';
																			if($ivr_options->option_value[$i] == $x && $ivr_options->option_value[$i] != ""){$option .= 'selected ';}
																			if (in_array($x, $ivr_options->option_value)){$option .= 'disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';}
																			$option .= '>'.$x.'</option>';
																		}
																		echo $option;
																	?>
																	<option value="A" <?php if($ivr_options->option_value[$i] == "A"){echo 'selected';}if (in_array("#", $ivr_options->option_value)){ echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >#</option>
																	<option value="B" <?php if($ivr_options->option_value[$i] == "B"){echo 'selected';}if (in_array("*", $ivr_options->option_value)){ echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >*</option>
																	<option value="C" <?php if($ivr_options->option_value[$i] == "C"){echo 'selected';}if (in_array("TIMECHECK", $ivr_options->option_value)){ echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >TIMECHECK</option>
																	<option value="D" <?php if($ivr_options->option_value[$i] == "D"){echo 'selected';}if (in_array("TIMEOUT", $ivr_options->option_value)){ echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >TIMEOUT</option>
																	<option value="E" <?php if($ivr_options->option_value[$i] == "E"){echo 'selected';}if (in_array("INVALID", $ivr_options->option_value)){ echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >INVALID</option>
																</select>
															</div>
															<div class="col-lg-7">
																Desription: 
																<input type="text" name="route_desc[]" id="" class="form-control route_desc_<?php echo $i;?>" placeholder="Description" value="<?php echo $ivr_options->option_description[$i]; ?>" />
															</div>
															<div class="col-lg-3">
																Route:
																<select class="form-control route_menu_<?php echo $i;?>" name="route_menu[]">
																	<option selected value=""></option>
																	<option value="CALLMENU" <?php if($ivr_options->option_route[$i] == "CALLMENU")echo "selected"; ?> >Call Menu / IVR</option>
																	<option value="INGROUP" <?php if($ivr_options->option_route[$i] == "INGROUP")echo "selected"; ?> >In-group</option>
																	<option value="DID" <?php if($ivr_options->option_route[$i] == "DID")echo "selected"; ?> >DID</option>
																	<option value="HANGUP" <?php if($ivr_options->option_route[$i] == "HANGUP")echo "selected"; ?> >Hangup</option>
																	<option value="EXTENSION" <?php if($ivr_options->option_route[$i] == "EXTENSION")echo "selected"; ?> >Custom Extension</option>
																	<option value="PHONE" <?php if($ivr_options->option_route[$i] == "PHONE")echo "selected"; ?> >Phone</option>
																	<option value="VOICEMAIL" <?php if($ivr_options->option_route[$i] == "VOICEMAIL")echo "selected"; ?> >Voicemail</option>
																	<option value="AGI" <?php if($ivr_options->option_route[$i] == "AGI")echo "selected"; ?> >AGI</option>
																</select>
															</div>
															<div class="col-lg-1 btn-remove"></div>
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 option_menu_<?php echo $i;?> mb mt">
															<!-- CALL MENU -->
																<div class="route_callmenu_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "CALLMENU")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label">Call Menu: </label>
																	<div class="col-sm-6">
																		<select class="select2-2 form-control select2" name="option_callmenu_value[]" style="width:100%;">
																			<option value="" selected> - - - NONE - - - </option>
																		<?php
																			$callmenu_option = '';
																			for($x=0;$x < count($ivr->menu_id);$x++){
																				$callmenu_option .= '<option value="'.$ivr->menu_id[$x].'"';
																					if($ivr_options->option_route_value[$i] == $ivr->menu_id[$x]){$callmenu_option .= ' selected';}
																				$callmenu_option .= '>'.$ivr->menu_id[$x].' - '.$ivr->menu_name[$x].'</option>';
																			}
																			echo $callmenu_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- IN GROUP -->
																<div class="route_ingroup_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "INGROUP")echo 'style="display:none;"'; ?> >
																	<div class="row mb">
																		<label class="col-sm-3 control-label">In Group: </label>
																		<div class="col-sm-6">
																			<select class="select2-2 form-control select2" name="option_ingroup_value[]" style="width:100%;">
																				<option value="" > - - - NONE - - - </option>
																			<?php
																				$ingroup_option = '';
																				for($x=0;$x < count($ingroups->group_id);$x++){
																					$ingroup_option .= '<option value="'.$ingroups->group_id[$x].'"';
																						if($ivr_options->option_route[$i] == "INGROUP" && $ivr_options->option_route_value[$i] == $ingroups->group_id[$x]){$ingroup_option .= ' selected';}
																					$ingroup_option .= '>'.$ingroups->group_id[$x].' - '.$ingroups->group_name[$x].'</option>';
																				}
																				echo $ingroup_option;
																			?>
																			</select>
																		</div>
																	</div>
																	<?php 
																		if($ivr_options->option_route[$i] == "INGROUP" || !isset($ivr_options->option_route[$i]) ){
																			$explode_ingroup_context = explode(",", $ivr_options->option_route_value_context[$i]);
																	?>
																	<div class="col-sm-11">
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label">Handle Method:</label>
																			<div class="col-sm-7">
																				<select class="form-control" name="handle_method_<?php echo $i;?>" id="edit_handle_method_<?php echo $i;?>">
																					<option value="CID" <?php if($explode_ingroup_context[0] == "CID")echo "selected"; ?> >CID</option>
																					<option value="CIDLOOKUP" <?php if($explode_ingroup_context[0] == "CIDLOOKUP")echo "selected"; ?> >CIDLOOKUP</option>
																					<option value="CIDLOOKUPRL" <?php if($explode_ingroup_context[0] == "CIDLOOKUPRL")echo "selected"; ?> >CIDLOOKUPRL</option>
																					<option value="CIDLOOKUPRC" <?php if($explode_ingroup_context[0] == "CIDLOOKUPRC")echo "selected"; ?> >CIDLOOKUPRC</option>
																					<option value="ANI" <?php if($explode_ingroup_context[0] == "ANI")echo "selected"; ?> >ANI</option>
																					<option value="ANILOOKUP" <?php if($explode_ingroup_context[0] == "ANILOOKUP")echo "selected"; ?> >ANILOOKUP</option>
																					<option value="ANILOOKUPRL" <?php if($explode_ingroup_context[0] == "ANILOOKUPRL")echo "selected"; ?> >ANILOOKUPRL</option>
																					<option value="VIDPROMPT" <?php if($explode_ingroup_context[0] == "VIDPROMPT")echo "selected"; ?> >VIDPROMPT</option>
																					<option value="VIDPROMPTLOOKUP" <?php if($explode_ingroup_context[0] == "VIDPROMPTLOOKUP")echo "selected"; ?> >VIDPROMPTLOOKUP</option>
																					<option value="VIDPROMPTLOOKUPRL" <?php if($explode_ingroup_context[0] == "VIDPROMPTLOOKUPRL")echo "selected"; ?> >VIDPROMPTLOOKUPRL</option>
																					<option value="VIDPROMPTLOOKUPRC" <?php if($explode_ingroup_context[0] == "VIDPROMPTLOOKUPRC")echo "selected"; ?> >VIDPROMPTLOOKUPRC</option>
																					<option value="CLOSER" <?php if($explode_ingroup_context[0] == "CLOSER")echo "selected"; ?> >CLOSER</option>
																					<option value="3DIGITID" <?php if($explode_ingroup_context[0] == "3DIGITID")echo "selected"; ?> >3DIGITID</option>
																					<option value="4DIGITID" <?php if($explode_ingroup_context[0] == "4DIGITID")echo "selected"; ?> >4DIGITID</option>
																					<option value="5DIGITID" <?php if($explode_ingroup_context[0] == "5DIGITID")echo "selected"; ?> >5DIGITID</option>
																					<option value="10DIGITID" <?php if($explode_ingroup_context[0] == "10DIGITID")echo "selected"; ?> >10DIGITID</option>
																				</select>
																			</div>
																		</div>
																		<div class="row mb">
																			<div class="col-sm-7">
																				<label class="col-sm-4 control-label">Campaign ID: </label>
																				<div class="col-sm-8">
																					<select class="form-control select2" name="campaign_id_<?php echo $i;?>" style="width:100%;">
																					<?php
																						$campaign_id_ingroup = '';
																						for($x=0;$x < count($campaign->campaign_id);$x++){
																							$campaign_id_ingroup .= '<option value="'.$campaign->campaign_id[$x].'"';
																								if($explode_ingroup_context[3] == $campaign->campaign_id[$x]){$campaign_id_ingroup .= ' selected';}
																							$campaign_id_ingroup .= '>'.$campaign->campaign_id[$x].' - '.$campaign->campaign_name[$x].'</option>';
																						}
																						echo $campaign_id_ingroup;
																					?>
																					</select>
																				</div>
																			</div>
																			<div class="col-sm-5 ingroup_advanced_settings_<?php echo $i;?>">
																				<label class="col-sm-5 control-label">Phone Code: </label>
																				<div class="col-sm-7">
																					<input type="text" class="form-control" name="phone_code<?php echo $i;?>" value="<?php 
																						if(isset($ivr_options->option_route[$i])) 
																							echo $explode_ingroup_context[4];
																						else
																							echo 1;
																					?>" id="edit_phone_code_<?php echo $i;?>" maxlength="14" size="4">
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<div class="col-sm-7">
																				<label class="col-sm-4 control-label">Search Method:</label>
																				<div class="col-sm-8">
																					<select class="form-control" name="search_method_<?php echo $i;?>" id="edit_search_method_<?php echo $i;?>">
																						<option value="LB" <?php if($explode_ingroup_context[1] == "LB")echo "selected"; ?> >LB - Load Balanced</option>
																						<option value="LO" <?php if($explode_ingroup_context[1] == "LO")echo "selected"; ?> >LO - Load Balanced Overflow</option>
																						<option value="SO" <?php if($explode_ingroup_context[1] == "SO")echo "selected"; ?> >Server Only</option>
																					</select>
																				</div>
																			</div>
																			<div class="col-sm-5">
																				<label class="col-sm-5 control-label" for="search_method_list_id">List ID: </label>
																				<div class="col-sm-7">
																					<input type="text" name="list_id_<?php echo $i;?>" value="<?php 
																						if(isset($ivr_options->option_route[$i])) 
																							echo $explode_ingroup_context[2];
																						else
																							echo 998;
																					?>" id="edit_list_id_<?php echo $i;?>" class="form-control" maxlength="14" size="8">
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label">VID Digits: </label>
																			<div class="col-sm-7">
																				<input type="text" class="form-control" name="vid_digits_<?php echo $i;?>" value="<?php 
																					if(isset($ivr_options->option_route[$i]))
																						echo $explode_ingroup_context[8];
																					else
																						echo 1;
																				?>" id="edit_validate_digits_<?php echo $i;?>" maxlength="3" size="3">
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label">VID Enter Filename: </label>
																			<div class="col-sm-8">
																				<div class="col-sm-6">
																					<input type="text" name="enter_filename_<?php echo $i;?>" value="<?php 
																						if(isset($ivr_options->option_route[$i]))
																							echo $explode_ingroup_context[5];
																						else
																							echo "sip-silence";
																					?>" id="edit_enter_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
																				</div>
																				<div class="col-sm-6">
																					<select class="col-sm-6 form-control select2" style="width:100%;" id="enter_filename_select_<?php echo $i;?>">
																						<option value="sip-silence" <?php if($explode_ingroup_context[5] == "sip-silence")echo "selected"; ?> > - - - DEFAULT VALUE - - - </option>
																					<?php
																						$vid_enter = '';
																						for($x=0;$x<count($voicefiles->file_name);$x++){
																							$file = substr($voicefiles->file_name[$x], 0, -4);
																							$vid_enter .= '<option value="'.$file.'"';
																								if($file == $explode_ingroup_context[5])echo $vid_enter.= 'selected';
																							$vid_enter .= '>'.$file.'</option>';
																							echo $vid_enter;
																						}
																					?>
																					</select>
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label">VID ID Number Filename: </label>
																			<div class="col-sm-8">
																				<div class="col-sm-6">
																					<input type="text" name="id_number_filename_<?php echo $i;?>" value="<?php 
																						if(isset($ivr_options->option_route[$i]))
																							echo $explode_ingroup_context[6];
																						else
																							echo "sip-silence";
																					?>" id="edit_id_number_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
																				</div>
																				<div class="col-sm-6">
																					<select class="col-sm-6 form-control select2" style="width:100%;" id="edit_id_number_filename_select_<?php echo $i;?>">
																						<option value="sip-silence" <?php if($explode_ingroup_context[6] == "sip-silence")echo "selected"; ?> > - - - DEFAULT VALUE - - - </option>
																					<?php
																						$vid_id = '';
																						for($x=0;$x<count($voicefiles->file_name);$x++){
																							$file = substr($voicefiles->file_name[$x], 0, -4);
																							$vid_id .= '<option value="'.$file.'"';
																								if($file == $explode_ingroup_context[6])echo $vid_id.= 'selected';
																							$vid_id .= '>'.$file.'</option>';
																							echo $vid_id;
																						}
																					?>
																					</select>
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label">VID Confirm Filename: </label>
																			<div class="col-sm-8">
																				<div class="col-sm-6">
																					<input type="text" name="confirm_filename_<?php echo $i;?>" value="<?php 
																						if(isset($ivr_options->option_route[$i]))
																							echo $explode_ingroup_context[7];
																						else
																							echo "sip-silence";
																					?>" id="edit_confirm_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
																				</div>
																				<div class="col-sm-6">
																					<select class="col-sm-6 form-control select2" style="width:100%;" id="edit_confirm_filename_select_<?php echo $i;?>">
																						<option value="sip-silence" <?php if($explode_ingroup_context[7] == "sip-silence")echo "selected"; ?> > - - - DEFAULT VALUE - - - </option>
																					<?php
																						$vid_confirm = '';
																						for($x=0;$x<count($voicefiles->file_name);$x++){
																							$file = substr($voicefiles->file_name[$x], 0, -4);
																							$vid_confirm .= '<option value="'.$file.'"';
																								if($file == $explode_ingroup_context[7])echo $vid_confirm.= 'selected';
																							$vid_confirm .= '>'.$file.'</option>';
																							echo $vid_confirm;
																						}
																					?>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<?php
																		}
																	?>
																</div>
															<!-- DID -->
																<div class="route_did_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "DID")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label">DID: </label>
																	<div class="col-sm-6">
																		<select class="col-sm-6 select2-2 form-control select2" name="option_did_value[]" style="width:100%;">
																			<option value="" selected> - - - NONE - - - </option>
																		<?php
																			$did_option = '';
																			for($x=0;$x < count($phonenumber->did_pattern);$x++){
																				$did_option .= '<option value="'.$phonenumber->did_pattern[$x].'"';
																					if($ivr_options->option_route_value[$i] == $phonenumber->did_pattern[$x]){ $did_option .= ' selected';}
																				$did_option .= '>'.$phonenumber->did_pattern[$x].' - '.$phonenumber->did_description[$x].'</option>';
																			}
																			echo $did_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- HANGUP -->
																<div class="route_hangup_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "HANGUP")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label">Audio File: </label>
																	<div class="col-sm-6">
																		<select class="select2-2 form-control select2" name="option_hangup_value[]" style="width:100%;">
																			<option value=""> - - - NONE - - - </option>
																		<?php
																			if($ivr_options->option_route_value[$i] == "vm-goodbye"){echo '<option value="vm-goodbye" selected> vm-goodbye </option>';}
																			$hangup_option = '';
																			for($x=0;$x<count($voicefiles->file_name);$x++){
																				$file = substr($voicefiles->file_name[$x], 0, -4);
																				$hangup_option .= '<option value="'.$file.'"';
																					if($ivr_options->option_route_value[$i] == $file){$hangup_option .= 'selected';}
																				$hangup_option .= '>'.$file.'</option>';
																			}
																			echo $hangup_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- EXTENSION -->
																<div class="route_exten_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "EXTENSION")echo 'style="display:none;"'; ?> >
																	<div class="col-sm-6">
																		<label class="col-sm-3 control-label">Extension: </label>
																		<div class="col-sm-9">
																			<input type="text" class="form-control" name="option_extension_value[]" id="option_route_value_<?php echo $i;?>" value="<?php if($ivr_options->option_route[$i] == "EXTENSION"){echo $ivr_options->option_route_value[$i];} ?>" />
																		</div>
																	</div>
																	<div class="col-sm-6">
																		<label class="col-sm-3 control-label">Context: </label>
																		<div class="col-sm-9">
																			<input type="text" class="form-control" name="option_route_value_context[]" id="option_route_value_context_<?php echo $i;?>" value="<?php if($ivr_options->option_route[$i] == "EXTENSION"){echo $ivr_options->option_route_value_context[$i];} ?>" />
																		</div>
																	</div>
																</div>
															<!-- PHONE -->
																<div class="route_phone_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "PHONE")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label">Phone: </label>
																	<div class="col-sm-6">
																		<select class="select2-2 form-control select2" name="option_phone_value[]" style="width:100%;">
																			<option value="" > - - - NONE - - - </option>
																		<?php
																			$phones_option = '';
																			for($x=0;$x < count($phones->extension);$x++){
																				$phones_option .= '<option value="'.$phones->extension[$x].'"';
																					if($ivr_options->option_route_value[$i] == $phones->extension[$x]){ $phones_option .= ' selected';}
																				$phones_option .= '>'.$phones->extension[$x]." - ".$phones->server_ip[$x]." - ".$phones->dialplan_number[$x]."</option>";
																			}
																			echo $phones_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- VOICEMAIL -->
																<div class="route_voicemail_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "VOICEMAIL")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label">Voicemail Box: </label>
																	<div class="col-sm-9">
																		<div class="col-sm-6">
																			<input type="text" name="option_voicemail_value[]" class="form-control" id="option_voicemail_input_<?php echo $i;?>" value="<?php if($ivr_options->option_route[$i] == "VOICEMAIL"){echo $ivr_options->option_route_value[$i];} ?>" maxlength="255" size="15">
																		</div>
																		<div class="col-sm-6">
																			<select class="col-sm-6 select2 form-control" style="width:100%;" id="option_voicemail_select_<?php echo $i;?>">
																				<option value="" > - - - NONE - - - </option>
																			<?php
																				$voicemail_option = '';
																				for($x=0;$x < count($voicemails->voicemail_id);$x++){
																					$voicemail_option .= '<option value="'.$voicemails->voicemail_id[$x].'"';
																						if($ivr_options->option_route_value[$i] == $voicemails->voicemail_id[$x]){ $voicemail_option .= ' selected';}
																					$voicemail_option .= '>'.$voicemails->voicemail_id[$x].' - '.$voicemails->fullname[$x].'</option>';
																				}
																				echo $voicemail_option;
																			?>
																			</select>
																		</div>
																	</div>
																</div>
															<!-- AGI -->
																<div class="route_agi_<?php echo $i;?>" <?php if($ivr_options->option_route[$i] != "AGI")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label">AGI: </label>
																	<div class="col-sm-6">
																		<input type="text" class="form-control" name="option_agi_value[]" maxlength="255" size="50" value="<?php if($ivr_options->option_route[$i] == "AGI"){echo $ivr_options->option_route_value[$i];} ?>">
																	</div>
																</div>
														</div>
													</div>
												</div>
												<?php
													}
												?>
											</div><!--static div -->
											</div>
											<!-- /.tab-pane -->

											<div id="modifyIVRresult"></div>
											<fieldset>
						                        <div class="box-footer">
						                           <div class="col-sm-3 pull-right">
														<a href="telephonyinbound.php" type="button"  id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
														<button type="submit" class="btn btn-primary" id="modifyIVROkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
						                           </div>
						                        </div>
						                    </fieldset>
											
										</div>
										<!-- /.tab-content -->
										
									</div>
								</form>
							</div>
						</section>
						<?php
						} else {
						# An error occured
							echo $output->result;
						}
                        
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}

				// IF PHONE NUMBER / DID
					if($did != NULL) {

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

						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetDIDInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["did_id"] = $did; #Desired did. (required)
            
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_TIMEOUT, 100);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
						$data = curl_exec($ch);
						curl_close($ch);
						$output = json_decode($data);
						//echo "<pre>";
						//var_dump($output);
						//echo "</pre>";
						if ($output->result=="success") {
						# Result was OK!
						?>
					
				<!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend>MODIFY DID RECORD : <u><?php echo $output->data->did_pattern;?></u></legend>
								
								<form id="modifydid">
									<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
									<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />

							<!-- Custom Tabs -->
							<div role="tabpanel">
							<!--<div class="nav-tabs-custom">-->
								<ul role="tablist" class="nav nav-tabs nav-justified">
									<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
									<li><a href="#tab_2" data-toggle="tab"> Advanced Settings</a></li>
								</ul>
				               <!-- Tab panes-->
				               <div class="tab-content">

					               	<!-- BASIC SETTINGS -->
					                <div id="tab_1" class="tab-pane fade in active">

										<input type="hidden" name="modify_did" value="<?php echo $output->data->did_id;?>">
									<fieldset>
										<div class="form-group mt">
											<label for="did_pattern" class="col-sm-2 control-label">DID NUMBER</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="did_pattern" id="did_pattern" value="<?php echo $output->data->did_pattern;?>">
											</div>
										</div>
										<div class="form-group">
											<label for="desc" class="col-sm-2 control-label">Description</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="desc" id="desc" value="<?php echo $output->data->did_description;?>">
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label">Status</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="status" id="status">
												<?php
													$status = NULL;
													if($output->data->did_active == "Y"){
														$status .= '<option value="Y" selected> Active </option>';
													}else{
														$status .= '<option value="Y" > Active </option>';
													}
													
													if($output->data->did_active == "N" || $output->data->did_active == NULL){
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
											<label for="route" class="col-sm-2 control-label">DID Route</label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="route" name="route">
													<?php
														$route = NULL;
														if($output->data->did_route  == "AGENT"){
															$route .= '<option value="AGENT" selected> Agent </option>';
														}else{
															$route .= '<option value="AGENT" > Agent </option>';
														}
														
														if($output->data->did_route  == "IN_GROUP"){
															$route .= '<option value="IN_GROUP" selected> In-group </option>';
														}else{
															$route .= '<option value="IN_GROUP" > In-group </option>';
														}
														
														if($output->data->did_route  == "PHONE"){
															$route .= '<option value="PHONE" selected> Phone </option>';
														}else{
															$route .= '<option value="PHONE" > Phone </option>';
														}
														
														if($output->data->did_route  == "CALLMENU"){
															$route .= '<option value="CALLMENU" selected> Call Menu / IVR </option>';
														}else{
															$route .= '<option value="CALLMENU" > Call Menu / IVR </option>';
														}
														
														if($output->data->did_route  == "VOICEMAIL"){
															$route .= '<option value="VOICEMAIL" selected> Voicemail </option>';
														}else{
															$route .= '<option value="VOICEMAIL" > Voicemail </option>';
														}
														
														if($output->data->did_route  == "EXTEN"){
															$route .= '<option value="EXTEN" selected> Custom Extension </option>';
														}else{
															$route .= '<option value="EXTEN" > Custom Extension </option>';
														}
														echo $route;
													?>
												</select>
											</div>
										</div>
									</fieldset>
									<fieldset>
										<!-- IF DID ROUTE = AGENT-->
										<div id="form_route_agent" <?php if($output->data->did_route  != "AGENT"){ ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_agentid" class="col-sm-3 control-label">Agent ID: </label>
												<div class="col-sm-9 mb">
													<select name="route_agentid" id="route_agentid" class="form-control select2" style="width:100%;">
														<option value="" > -- NONE -- </option>
														<?php
															for($i=0;$i<count($users->user);$i++){
														?>
															<option value="<?php echo $users->user[$i];?>" <?php if($output->data->user == $users->user[$i]) echo "selected";?> >
																<?php echo $users->user[$i].' - '.$users->full_name[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="route_unavail" class="col-sm-3 control-label">Agent Unavailable Action: </label>
												<div class="col-sm-9 mb">
													<select name="route_unavail" id="route_unavail" class="form-control">
														<option value="VOICEMAIL"  <?php if($output->data->user_unavailable_action == "VOICEMAIL") echo "selected";?> > Voicemail </option>
														<option value="PHONE"  <?php if($output->data->user_unavailable_action == "PHONE") echo "selected";?> > Phone </option>
														<option value="IN_GROUP"  <?php if($output->data->user_unavailable_action == "IN_GROUP") echo "selected";?> > In-group </option>
														<option value="EXTEN"  <?php if($output->data->user_unavailable_action == "EXTEN") echo "selected";?> > Custom Extension </option>
													</select>
												</div>
											</div>
												<!-- FOR AGENT UNAVAILABLE ACTION --
												<!--IF route_unavail = EXTEN --
													<div class="form-group" id="ru_exten" style="display: none;">
														<label for="ru_exten" class="col-sm-3 control-label">Extension</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" name="ru_exten" id="ru_exten" value="<?php /*echo $output->data->did_pattern;?>">
														</div>
													</div>
												<!--IF route_unavail = INGROUP --
													<div class="form-group" id="ru_ingroup" style="display: none;">
														<label for="ru_ingroup" class="col-sm-3 control-label">Ingroup</label>
														<div class="col-sm-9 mb">
															<select name="ru_ingroup" id="ru_ingroup" class="form-control">
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
												<!--IF route_unavail = PHONE --
													<div class="form-group" id="ru_phone" style="display: none;">
														<label for="exten" class="col-sm-3 control-label">Phone</label>
														<div class="col-sm-9 mb">
															<select name="ru_phone" id="ru_phone" class="form-control">
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
												<!--IF route_unavail = VOICEMAIL --
													<div class="form-group" id="ru_voicemail" style="display: none;">
														<label for="exten" class="col-sm-3 control-label">Voicemail</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" name="exten" id="exten" value="<?php echo $output->data->did_pattern;*/?>">
														</div>
													</div>-->
											<div class="form-group">
												<label for="user_route_settings_ingroup" class="col-sm-3 control-label">Agent Route Settings: </label>
												<div class="col-sm-9 mb">
													<select name="user_route_settings_ingroup" id="user_route_settings_ingroup" class="form-control">
														<option value="">---NONE---</option>
													<?php
														for($i=0;$i<count($ingroups->group_id);$i++){
															if($ingroups->group_id[$i] != "AGENTDIRECT"){
													?>
														<option value="<?php echo $ingroups->group_id[$i];?>" <?php if($output->data->user_route_settings_ingroup == $ingroups->group_id[$i]) echo "selected";?> >
															<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
														</option>
													<?php
															}
														}
													?>
													</select>
												</div>
											</div>
										</div><!-- end of div agent-->
										
									<!-- IF DID ROUTE = IN-GROUP-->
										<div id="form_route_ingroup" class="form-group" <?php if($output->data->did_route  != "IN_GROUP"){ ?> style="display: none;" <?php }?> >
										<label for="route_ingroupid" class="col-sm-3 control-label">In-Group ID: </label>
										<div class="col-sm-9 mb">
											<select name="route_ingroupid" id="route_ingroupid" class="form-control">
												<?php
													for($i=0;$i<count($ingroups->group_id);$i++){
												?>
													<option value="<?php echo $ingroups->group_id[$i];?>" <?php if($ingroups->group_id[$i] == $output->data->group_id)echo "selected";?>>
													     <?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
													</option>				
												<?php
													}
												?>
											</select>
										</div>
										</div><!-- end of ingroup div -->
										
									<!-- IF DID ROUTE = PHONE -->
										<div id="form_route_phone" <?php if($output->data->did_route  != "PHONE"){ ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label  for="route_phone_exten" class="col-sm-3 control-label">Phone Extension: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_exten" id="route_phone_exten" class="form-control">
														<?php
															for($i=0;$i<count($phones->extension);$i++){
														?>
															<option value="<?php echo $phones->extension[$i];?>" <?php if($phones->extension[$i] == $output->data->phone)echo "selected";?>>
																<?php echo $phones->extension[$i].' - '.$phones->server_ip[$i].' - '.$phones->dialplan_number[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="route_phone_server" class="col-sm-3 control-label">Server IP: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_server" id="route_phone_server" class="form-control">
														<option value="" > -- NONE -- </option>
														<?php
															for($i=0;$i < 1;$i++){
														?>
															<option value="<?php echo $phones->server_ip[$i];?>" <?php if($phones->server_ip[$i] == $output->data->server_ip)echo "selected";?>>
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
										<div id="form_route_callmenu" <?php if($output->data->did_route  != "CALLMENU"){ ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_ivr" class="col-sm-3 control-label">Call Menu: </label>
												<div class="col-sm-9 mb">
													<select name="route_ivr" id="route_ivr" class="form-control">
														<?php
															for($i=0;$i<count($ivr->menu_id);$i++){
														?>
															<option value="<?php echo $ivr->menu_id[$i];?>" <?php if($ivr->menu_id[$i] == $output->data->menu_id)echo "selected";?>>
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
										<div id="form_route_voicemail" <?php if($output->data->did_route  != "VOICEMAIL"){ ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_voicemail" class="col-sm-3 control-label">Voicemail Box: </label>
												<div class="col-sm-9 mb">
													<select name="route_voicemail" id="route_voicemail" class="form-control">
														<?php
															for($i=0;$i<count($voicemails->voicemail_id);$i++){
														?>
															<option value="<?php echo $voicemails->voicemail_id[$i];?>" <?php if($voicemails->voicemail_id[$i] == $output->data->voicemail_ext)echo "selected";?>>
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
										<div id="form_route_exten" <?php if($output->data->did_route  != "EXTEN"){ ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_exten" class="col-sm-3 control-label">Extension: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" value="<?php echo $ouput->data->extension; ?>" required>
												</div>
											</div>
											<div class="form-group">
												<label for="route_exten_context" class="col-sm-3 control-label">Extension Context: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" value="<?php echo $ouput->data->exten_context;?>" required>
												</div>
											</div>
										</div><!-- end of custom extension div -->
									</fieldset>

									</div><!-- end of basic settings-->
								

						       		<!-- ADVANCED SETTINGS -->
						       		<div id="tab_2" class="tab-pane fade in">
						       			<fieldset>
							       			<div class="form-group mt">
							       				<label for="cid_num" class="col-sm-2 control-label">Clean CID Number</label>
							       				<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="cid_num" id="cid_num" value="<?php echo $output->data->filter_clean_cid_number;?>" maxlength="20">
												</div>
							       			</div>
							       		</fieldset>				       			
						       		</div>
							
								<!-- FOOTER BUTTONS -->
								   	<div id="modifyDIDresult"></div>

				                    <fieldset class="footer-buttons">
				                        <div class="box-footer">
				                           <div class="col-sm-3 pull-right">
													<a href="telephonyinbound.php" type="button"  id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
				                           	
				                                	<button type="submit" class="btn btn-primary" id="modifyDIDOkButton" data-id="<?php echo $groupid;?>" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
												
				                           </div>
				                        </div>
				                    </fieldset>

								</div><!-- end of content -->
							</div>
							</form>	
						</div>
					</div><!-- body -->
                </section>
						<?php		
							
						} else {
						# An error occured
							echo $output->result;
						}
                        
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
					
					?>
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
         
         <?php print $ui->standardizedThemeJS();?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
    	<!-- bootstrap color picker -->
			<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>
		<!-- SELECT2-->
            <script src="theme_dashboard/select2/dist/js/select2.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {

			//Initialize Select2 Elements
                $('.select2').select2({
                    theme: 'bootstrap'
                });
			// init datatables
				$('#agent_rank_table').dataTable();

			// for cancelling
				$(document).on('click', '#cancel', function(){
					swal("Cancelled", "No action has been done :)", "error");
				});

			//Colorpicker
    			$(".colorpicker").colorpicker({
					format: 'hex'
				});

				$(document).on("change","#route_unavail",function() {
					//  alert( this.value ); // or $(this).val()
					if(this.value == "EXTEN") {
					  $('#ru_exten').show();
					  
					  $('#ru_phone').hide();
					  $('#ru_ingroup').hide();
					  $('#ru_voicemail').hide();
					}if(this.value == "IN_GROUP") {
					  $('#ru_ingroup').show();
					  
					  $('#ru_exten').hide();
					  $('#ru_phone').hide();
					  $('#ru_voicemail').hide();
					}if(this.value == "PHONE") {
					  $('#ru_phone').show();
					  
					  $('#ru_exten').hide();
					  $('#ru_ingroup').hide();
					  $('#ru_voicemail').hide();
					}if(this.value == "VOICEMAIL") {
					  $('#ru_voicemail').show();
					  
					  $('#ru_exten').hide();
					  $('#ru_ingroup').hide();
					  $('#ru_phone').hide();
					}
					
				});
				
				$(document).on("change","#route",function() {
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
			
			// drop action change
				$(document).on("change","#drop_action",function() {
					if(this.value == "HANGUP") {
					  $('.drop_action_exten').hide();
					}

					if(this.value == "MESSAGE") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_message').show();

					  $('.drop_exten_callmenu').hide();
					  $('.drop_exten_ingroup').hide();
					  $('.drop_exten_voicemail').hide();
					}

					if(this.value == "VOICEMAIL") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_voicemail').show();

					  $('.drop_exten_callmenu').hide();
					  $('.drop_exten_ingroup').hide();
					  $('.drop_exten_message').hide();
					}

					if(this.value == "IN_GROUP") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_ingroup').show();

					  $('.drop_exten_callmenu').hide();
					  $('.drop_exten_voicemail').hide();
					  $('.drop_exten_message').hide();
					}

					if(this.value == "CALLMENU") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_callmenu').show();

					  $('.drop_exten_ingroup').hide();
					  $('.drop_exten_voicemail').hide();
					  $('.drop_exten_message').hide();
					}
					
				});

			//no_agent_action
				$(document).on("change","#no_agent_action",function() {
					if(this.value == "HANGUP") {
					  $('.no_agents_exten').hide();
					}

					if(this.value == "MESSAGE") {
					  $('.no_agents_exten').show();
					  $('.no_agents_message').show();

					  $('.no_agents_callmenu').hide();
					  $('.no_agents_ingroup').hide();
					  $('.no_agents_voicemail').hide();
					}

					if(this.value == "VOICEMAIL") {
					  $('.no_agents_exten').show();
					  $('.no_agents_voicemail').show();

					  $('.no_agents_callmenu').hide();
					  $('.no_agents_ingroup').hide();
					  $('.no_agents_message').hide();
					}

					if(this.value == "IN_GROUP") {
					  $('.no_agents_exten').show();
					  $('.no_agents_ingroup').show();

					  $('.no_agents_callmenu').hide();
					  $('.no_agents_voicemail').hide();
					  $('.no_agents_message').hide();
					}

					if(this.value == "CALLMENU") {
					  $('.no_agents_exten').show();
					  $('.no_agents_callmenu').show();

					  $('.no_agents_ingroup').hide();
					  $('.no_agents_voicemail').hide();
					  $('.no_agents_message').hide();
					}
					
				});
	
			// on tab change hide footer
				$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				  var target = $(e.target).attr("href"); // activated tab
				  if(target == "#agents"){
				  	$('#not_agent_rank').hide();
				  }else{
				  	$('#not_agent_rank').show();
				  }
				});

			/****** 
			** MODIFY Functions 
		 	******/

				//an ingroup
				$('#modifyInboundOkButton').click(function(){
					$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
					$('#modifyInboundOkButton').prop("disabled", true);

					$.ajax({
                        url: "./php/ModifyTelephonyInbound.php",
                        type: 'POST',
                        data: $("#modifyingroup").serialize(),
                        success: function(data) {
                          //if message is sent
							if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
								swal("Updated!", "Inbound has been successfully updated.", "success");
								window.setTimeout(function(){location.replace("./telephonyinbound.php");},2000);
							} else {
								sweetAlert("Oops...","Something went wrong! " + data, "error");
								$('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modifyInboundOkButton').prop("disabled", false);
							}
                        }
                    });	
                    return false;
				});
				
				// agent rank form submit
				$('#submit_agent_rank').click(function(){
					var groupID = $(this).attr('data-id');
					var itemdatas = $('#agentrankform').serialize();
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
					
	                $('input:checkbox[id^="CHECK"]').each(function() {
                        if (!this.checked) {
                                itemdatas += '&'+this.name+'=NO';
                        }
	                });
					
					$.ajax({
						url: "php/ModifyAgentRank.php",
						type: 'POST',
						data: {	itemrank: itemdatas, idgroup: groupID, log_user: log_user, log_group: log_group },
						success: function(data) {
						$('#submit_agent_rank').html("<i class='fa fa-check'></i> Submit");
						$('#submit_agent_rank').prop("disabled", false);
							console.log(data);
							if(data == "success"){
								swal("Success!", "Agent Rank for this inbound Successfully Updated!", "success");
							}else{
								sweetAlert("Oops...", "Something went wrong! "+data, "error");
							}
						}
					});
				});

				//IVR
				
				$(document).on("click","#modifyIVROkButton",function() {
					$('.route_option :disabled').attr('disabled', false);
				});
				$("#modifyivr").validate({
					submitHandler: function() {
						$.post("./php/ModifyTelephonyInbound.php",
						$("#modifyivr").serialize(),
						function(data){
							//if message is sent
							if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
								swal({title: "Success!",text: "IVR has been successfully updated!",type: "success"},function(){window.location.href = 'telephonyinbound.php';});
							} else {
								sweetAlert("Oops...","Something went wrong! " + data, "error");
								//location.reload();
							}
							
						});
						return false; //don't let the form refresh the page...
					}
				});

				// phone number / DID
				$("#modifydid").validate({
					submitHandler: function() {
					//submit the form
						$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
						$('#modifyDIDOkButton').prop("disabled", true);
						
						$.post("./php/ModifyTelephonyInbound.php", //post
						$("#modifydid").serialize(), 
							function(data){
								//if message is sent
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									swal({title: "Success!",text: "DID has been successfully updated!",type: "success"},function(){window.location.href = 'telephonyinbound.php';});
									
								} else {
									sweetAlert("Oops...","Something went wrong! " + data, "error");
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyDIDOkButton').prop("disabled", false);	
								}
								//
							});
						return false; //don't let the form refresh the page...
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
			
			// INGROUP
				$('.select_welcome_message_filename').hide();
					$('.show_welcome_message_filename').on('click', function(event) {
						 $('.select_welcome_message_filename').toggle('show');
					});
					$(document).on('change', '#select_welcome_message_filename',function(){
						var val = $(this).val();
						$('#welcome_message_filename').val(val);
						$('.select_welcome_message_filename').toggle('hide');
					});
				$('.select_moh_context').hide();
					$('.show_moh_context').on('click', function(event) {
						 $('.select_moh_context').toggle('show');
					});
					$(document).on('change', '#select_moh_context',function(){
						var val = $(this).val();
						$('#moh_context').val(val);
						$('.select_moh_context').toggle('hide');
					});
				$('.select_onhold_prompt_filename').hide();
					$('.show_onhold_prompt_filename').on('click', function(event) {
						 $('.select_onhold_prompt_filename').toggle('show');
					});
					$(document).on('change', '#select_onhold_prompt_filename',function(){
						var val = $(this).val();
						$('#onhold_prompt_filename').val(val);
						$('.select_onhold_prompt_filename').toggle('hide');
					});
				$('.select_after_hours_message_filename').hide();
					$('.show_after_hours_message_filename').on('click', function(event) {
						 $('.select_after_hours_message_filename').toggle('show');
					});
					$(document).on('change', '#select_after_hours_message_filename',function(){
						var val = $(this).val();
						$('#after_hours_message_filename').val(val);
						$('.select_after_hours_message_filename').toggle('hide');
					});
				$('.select_after_hours_voicemail').hide();
					$('.show_after_hours_voicemail').on('click', function(event) {
						 $('.select_after_hours_voicemail').toggle('show');
					});
					$(document).on('change', '#select_after_hours_voicemail',function(){
						var val = $(this).val();
						$('#after_hours_voicemail').val(val);
						$('.select_after_hours_voicemail').toggle('hide');
					});
				
				$('.select_no_agents_exten').hide();
					$('.show_no_agents_exten').on('click', function(event) {
						 $('.select_no_agents_exten').toggle('show');
					});
					$(document).on('change', '#select_no_agents_exten',function(){
						var val = $(this).val();
						$('#no_agents_exten').val(val);
						$('.select_no_agents_exten').toggle('hide');
					});
				$('.select_no_agents_voicemail').hide();
					$('.show_no_agents_voicemail').on('click', function(event) {
						 $('.select_no_agents_voicemail').toggle('show');
					});
					$(document).on('change', '#select_no_agents_voicemail',function(){
						var val = $(this).val();
						$('#no_agents_voicemail').val(val);
						$('.select_no_agents_voicemail').toggle('hide');
					});
				$('.select_no_agents_ingroup').hide();
					$('.show_no_agents_ingroup').on('click', function(event) {
						 $('.select_no_agents_ingroup').toggle('show');
					});
					$(document).on('change', '#select_no_agents_ingroup',function(){
						var val = $(this).val();
						$('#no_agents_ingroup').val(val);
						$('.select_no_agents_ingroup').toggle('hide');
					});
				$('.select_no_agents_callmenu').hide();
					$('.show_no_agents_callmenu').on('click', function(event) {
						 $('.select_no_agents_callmenu').toggle('show');
					});
					$(document).on('change', '#select_no_agents_callmenu',function(){
						var val = $(this).val();
						$('#no_agents_callmenu').val(val);
						$('.select_no_agents_callmenu').toggle('hide');
					});
				
			// IVR
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
				
				<?php for($i=0;$i < 14; $i++){ ?>
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
				
				// for voicemail
					<?php for($i=0;$i < 14; $i++){ ?>
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
					<?php for($i=0;$i < 14; $i++){ ?>
					$(document).on('change', '#enter_filename_select_<?php echo $i;?>',function(){
						var val = $(this).val();
						$('#edit_enter_filename_<?php echo $i;?>').val(val);
					});
					<?php } ?>
					
					<?php for($i=0;$i < 14; $i++){ ?>
					$(document).on('change', '#edit_id_number_filename_select_<?php echo $i;?>',function(){
						var val = $(this).val();
						$('#edit_id_number_filename_<?php echo $i;?>').val(val);
					});
					<?php } ?>
					
					<?php for($i=0;$i < 14; $i++){ ?>
					$(document).on('change', '#edit_confirm_filename_select_<?php echo $i;?>',function(){
						var val = $(this).val();
						$('#edit_confirm_filename_<?php echo $i;?>').val(val);
					});
					<?php } ?>
			
			 $('.select2-2').select2({
				theme: 'bootstrap'
			});
		});
		
			function checkdatas(groupID) {
		        if (groupID !== undefined) {
						var log_user = '<?=$_SESSION['user']?>';
						var log_group = '<?=$_SESSION['usergroup']?>';
		                var itemdatas = $('#agentrankform').serialize();
		                $('input:checkbox[id^="CHECK"]').each(function() {
		                        if (!this.checked) {
		                                itemdatas += '&'+this.name+'=NO';
		                        }
		                });

		                $.ajax({
						    url: "php/ModifyAgentRank.php",
						    type: 'POST',
						    data: {
						    	itemrank: itemdatas,
						    	idgroup: groupID,
								log_user: log_user,
								log_group: log_group
						    },
							success: function(data) {
								$('#submit_agent_rank').html("<i class='fa fa-check'></i> Submit");
                				$('#submit_agent_rank').prop("disabled", false)
								console.log(data);
								if(data == "success"){
									swal("Success!", "Agent Rank for this inbound Successfully Updated!", "success");
								}else{
									sweetAlert("Oops...", "Something went wrong! "+data, "error");
								}
							}
						});
		    	}
			        /*else {
			                if ($("#selectAllAgents").is(':checked'))
			                {
		                        $('input:checkbox[id^="CHECK"]').each(function() {
		                                        $(this).attr('checked',true);
		                        });
			                }
			                else
			                {
		                        $('input:checkbox[id^="CHECK"]').each(function() {
		                                        $(this).removeAttr('checked');
		                        });
			                }
			        }*/
			}
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
