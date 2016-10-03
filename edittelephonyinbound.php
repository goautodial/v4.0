<?php

	###################################################
	### Name: edittelephonyinbound.php 				###
	### Functions: Edit Inbound, IVR & DID  		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
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
																<select class="form-control select2" id="drop_exten" name="drop_exten" style="width:100%;">
																	<?php
																		$drop_action_exten = NULL;
																			for($x=0; $x < count($voicefiles->file_name);$x++){					
																				if($output->data->drop_exten == $voicefiles->file_name[$x]){
																					$drop_action_exten .= '<option value="'.$voicefiles->file_name[$x].'" selected> '.$voicefiles->file_name[$x].' </option>';
																				}else{
																					$drop_action_exten .= '<option value="'.$voicefiles->file_name[$x].'"> '.$voicefiles->file_name[$x].' </option>';
																				}
																			}
																		echo $drop_action_exten;
																	?>
																</select>
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
														<div class="drop_callmenu" <?php if($output->data->drop_action != "CALLMENU"){ ?>style="display:none;"<?php }?>>
															<label for="drop_exten_callmenu" class="col-sm-3 control-label">Drop Callmenu </label>
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
																if($output->data->call_time_id == NULL){
																	$call_time_id .= '<option value="NONE" selected> NONE </option>';
																}else{
																	$call_time_id .= '<option value="NONE" > NONE </option>';
																}
															for($x=0; $x<count($call_time->call_time_id);$x++){									
																if($output->data->call_time_id == $call_time->call_time_id[$x]){
																	$call_time_id .= '<option value="'.$call_time->call_time_id[$x].'" selected> '.$call_time->call_time_id[$x].' - '.$call_time->call_time_name[$x].' </option>';
																}else{
																	$call_time_id .= '<option value="'.$call_time->call_time_id[$x].'"> '.$call_time->call_time_id[$x].' - '.$call_time->call_time_name[$x].' </option>';
																}

															}
														echo $call_time_id;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="after_hours_action" class="col-sm-3 control-label">After Hours Action</label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="after_hours_action" name="after_hours_action">
														<?php
														$after_hours_action = NULL;
															if($output->data->after_hours_action == "HANGUP"){
																$after_hours_action .= '<option value="HANGUP" selected> HANGUP </option>';
															}else{
																$after_hours_action .= '<option value="HANGUP" > HANGUP </option>';
															}
															
															if($output->data->after_hours_action == "MESSAGE"){
																$after_hours_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															}else{
																$after_hours_action .= '<option value="MESSAGE" > MESSAGE </option>';
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

											<!-- AFTER HOURS  -->
												<div class="form-group">
													<!-- MESSAGE -->
														<label for="after_hours_exten" class="col-sm-3 control-label">After Hours Message Filename</label>
														<div class="col-sm-9 mb">
															<select class="form-control select2" id="after_hours_exten" name="after_hours_exten" style="width:100%;">
																<?php
																	$after_hours_exten = NULL;
																		for($x=0; $x < count($voicefiles->file_name);$x++){					
																			if($output->data->after_hours_exten == $voicefiles->file_name[$x]){
																				$after_hours_exten .= '<option value="'.$voicefiles->file_name[$x].'" selected> '.$voicefiles->file_name[$x].' </option>';
																			}else{
																				$after_hours_exten .= '<option value="'.$voicefiles->file_name[$x].'"> '.$voicefiles->file_name[$x].' </option>';
																			}
																		}
																	echo $after_hours_exten;
																?>
															</select>
														</div>
													<!-- /. message -->
												</div>

												<div class="form-group">
													<label for="after_hours_exten" class="col-sm-3 control-label">After Hours Extension</label>
													<div class="col-sm-9 mb">
														<input type="number" class="form-control" name="after_hours_exten" id="after_hours_exten" value="<?php echo $output->data->after_hours_exten;?>" />
													</div>
												</div>

												<div class="form-group">
													<!-- VOICEMAIL -->
														<label for="after_hours_voicemail" class="col-sm-3 control-label">After Hours Voicemail</label>
														<div class="col-sm-9 mb">
															<select class="form-control select2" id="after_hours_voicemail" name="after_hours_voicemail" style="width:100%;">
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
													<!-- /. voicemail -->
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
							       			<div class="form-group mt">
							       				<label for="call_launch" class="col-sm-4 control-label">Get Call Launch</label>
							       				<div class="col-sm-8 mb">
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
							       			</div>

							       			<!-- NO AGENTS EXTEN -->
												<div class="form-group no_agents_exten">

													<!-- IF MESSAGE IS SELECTED -->
														<div class="no_agents_message" <?php if($output->data->no_agent_action != "MESSAGE"){?> style="display:none;"<?php }?> >
															<label for="no_agents_exten" class="col-sm-4 control-label">Audio File</label>
															<div class="col-sm-8 mb">
																<select class="form-control select2" id="no_agents_exten" name="no_agents_exten" style="width:100%;">
																	<?php
																		$no_agents_exten = NULL;
																			for($x=0; $x < count($voicefiles->file_name);$x++){					
																				if($output->data->no_agent_action_value == $voicefiles->file_name[$x]){
																					$no_agents_exten .= '<option value="'.$voicefiles->file_name[$x].'" selected> '.$voicefiles->file_name[$x].' </option>';
																				}else{
																					$no_agents_exten .= '<option value="'.$voicefiles->file_name[$x].'"> '.$voicefiles->file_name[$x].' </option>';
																				}
																			}
																		echo $no_agents_exten;
																	?>
																</select>
															</div>
														</div><!-- /. message -->
													
													<!-- IF VOICEMAIL IS SELECTED -->
														<div class="no_agents_voicemail" <?php if($output->data->no_agent_action != "VOICEMAIL"){?> style="display:none;" <?php }?> >
															<label for="no_agents_voicemail" class="col-sm-4 control-label">Voicemail</label>
															<div class="col-sm-8 mb">
																<select class="form-control select2" id="no_agents_voicemail" name="no_agents_voicemail" style="width:100%;">
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
														</div><!-- /. voicemail -->

													<!-- IF IN_GROUP IS SELECTED -->
														<div class="no_agents_ingroup" <?php if($output->data->no_agent_action != "IN_GROUP"){ ?>style="display:none;"<?php }?>>
															<label for="no_agents_ingroup" class="col-sm-4 control-label">In-Group </label>
															<div class="col-sm-8 mb">
																<select class="form-control select2" id="no_agents_ingroup" name="no_agents_ingroup" style="width:100%;">
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
														</div><!-- /. ingroup -->

													<!-- IF CALLMENU IS SELECTED -->
														<div class="no_agents_callmenu" <?php if($output->data->no_agent_action != "CALLMENU"){ ?>style="display:none;"<?php }?>>
															<label for="no_agents_callmenu" class="col-sm-4 control-label">Callmenu </label>
															<div class="col-sm-8 mb">
																<select class="form-control select2" id="no_agents_callmenu" name="no_agents_callmenu" style="width:100%;">
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
														</div><!-- /. callmenu -->
												</div>

							       			<div class="form-group">
							       				<label for="welcome_message_filename" class="col-sm-4 control-label">Welcome Message Filename</label>
							       				<div class="col-sm-8 mb">
													<select class="form-control select2" id="welcome_message_filename" name="welcome_message_filename" style="width:100%;">
														<?php
															$welcome_message_filename = NULL;
																for($x=0; $x < count($voicefiles->file_name);$x++){					
																	if($output->data->welcome_message_filename == $voicefiles->file_name[$x]){
																		$welcome_message_filename .= '<option value="'.$voicefiles->file_name[$x].'" selected> '.$voicefiles->file_name[$x].' </option>';
																	}else{
																		$welcome_message_filename .= '<option value="'.$voicefiles->file_name[$x].'"> '.$voicefiles->file_name[$x].' </option>';
																	}
																}
															echo $welcome_message_filename;
														?>
													</select>
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
													<select class="form-control select2" id="moh_context" name="moh_context" style="width:100%;">
														<?php
															$moh_context = NULL;
																for($x=0; $x < count($moh->moh_id);$x++){					
																	if($output->data->moh_context == $moh->moh_id[$x]){
																		$moh_context .= '<option value="'.$moh->moh_id[$x].'" selected> '.$moh->moh_name[$x].' </option>';
																	}else{
																		$moh_context .= '<option value="'.$moh->moh_id[$x].'"> '.$moh->moh_name[$x].' </option>';
																	}
																}
															echo $moh_context;
														?>
													</select>
												</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="onhold_prompt_filename" class="col-sm-4 control-label">On Hold Prompt</label>
							       				<div class="col-sm-8 mb">
													<select class="form-control select2" id="onhold_prompt_filename" name="onhold_prompt_filename" style="width:100%;">
														<?php
															$onhold_prompt_filename = NULL;
																for($x=0; $x < count($voicefiles->file_name);$x++){					
																	if($output->data->onhold_prompt_filename == $voicefiles->file_name[$x]){
																		$onhold_prompt_filename .= '<option value="'.$voicefiles->file_name[$x].'" selected> '.$voicefiles->file_name[$x].' </option>';
																	}else{
																		$onhold_prompt_filename .= '<option value="'.$voicefiles->file_name[$x].'"> '.$voicefiles->file_name[$x].' </option>';
																	}
																}
															echo $onhold_prompt_filename;
														?>
													</select>
												</div>
							       			</div>
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

						$voicefiles = $ui->API_GetVoiceFilesList();

						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i < count($output->menu_id);$i++){
							
						?>

						<div class="panel-body">
							<legend>MODIFY IVR : <u><?php echo $output->menu_id[$i];?></u></legend>

							<form id="modifyivr" class="form-horizontal">

								<input type="hidden" name="modify_ivr" value="<?php echo $output->menu_id[$i];?>">
								<div class="col-lg-12">
									<!-- Custom Tabs -->
									<div class="nav-tabs-custom">
										<ul class="nav nav-tabs nav-justified">
											<li class="active"><a href="#tab_1" data-toggle="tab">Basic</a></li>
											<li><a href="#tab_2" data-toggle="tab">Options</a></li>
											<li><a href="#tab_3" data-toggle="tab">Advance Settings</a></li>
											<!-- <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li> -->
										</ul>
										<div class="tab-content">

											<div class="tab-pane active" id="tab_1">
												<div class="form-group">
													<label class="col-sm-4 control-label" for="menu_id">Menu ID:</label>
													<div class="col-sm-8">
														<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID" minlength="4" required title="No Spaces. Minimum of 4 characters" value="<?php echo $output->menu_id[$i];?>">
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
														<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name" required value="<?php echo $output->menu_name[$i];?>">
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
														<input type="number" name="menu_repeat" id="menu_repeat" class="form-control"value="<?php echo $output->menu_repeat[$i];?>" min="0" required>
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
											</div>
											<!-- /.tab-pane -->
											<div class="tab-pane" id="tab_2">
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
											</div>
											<!-- /.tab-pane -->
											<div class="tab-pane" id="tab_3">
												Advance Settings Here
											</div>

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
								</div>
							</form>
						</div>
						
						<?php
							}
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
						
						//var_dump($output);
						
						if ($output->result=="success") {
						# Result was OK!
						?>
					<script>
						$(window).ready(function() {
							var route = document.getElementById('route').value;

							if(route == "AGENT") {
							  $('#form_route_agent').show();
							  
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "IN_GROUP") {
							  $('#form_route_ingroup').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "PHONE") {
							  $('#form_route_phone').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "CALLMENU") {
							  $('#form_route_callmenu').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "VOICEMAIL") {
							  $('#form_route_voicemail').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_exten').hide();
							}if(route == "EXTEN") {
							  $('#form_route_exten').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_callmenu').hide();
							}
						});
					</script>
				<!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend>MODIFY DID RECORD : <u><?php echo $output->data->did_pattern;?></u></legend>
								
								<form id="modifydid">

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
														
														if($output->data->did_route  == "CALLMENU "){
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
										<div id="form_route_agent" style="display: none;">
											<div class="form-group">
												<label for="route_agentid" class="col-sm-3 control-label">Agent ID: </label>
												<div class="col-sm-9 mb">
													<select name="route_agentid" id="route_agentid" class="form-control select2">
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
												<label for="route_unavail" class="col-sm-3 control-label">Agent Unavailable Action: </label>
												<div class="col-sm-9 mb">
													<select name="route_unavail" id="route_unavail" class="form-control">
														<option value="EXTEN" > Custom Extension </option>
														<option value="IN_GROUP" > In-group </option>
														<option value="PHONE" > Phone </option>
														<option value="VOICEMAIL" > Voicemail </option>												
													</select>
												</div>
											</div>
												<!-- FOR AGENT UNAVAILABLE ACTION -->
												<!--IF route_unavail = EXTEN -->
													<div class="form-group" id="ru_exten" style="display: none;">
														<label for="ru_exten" class="col-sm-3 control-label">Extension</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" name="ru_exten" id="ru_exten" value="<?php echo $output->data->did_pattern;?>">
														</div>
													</div>
												<!--IF route_unavail = INGROUP -->
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
												<!--IF route_unavail = PHONE -->
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
												<!--IF route_unavail = VOICEMAIL -->
													<div class="form-group" id="ru_voicemail" style="display: none;">
														<label for="exten" class="col-sm-3 control-label">Voicemail</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" name="exten" id="exten" value="<?php echo $output->data->did_pattern;?>">
														</div>
													</div>
											<div class="form-group">
												<label for="route_settings" class="col-sm-3 control-label">Agent Route Settings: </label>
												<div class="col-sm-9 mb">
													<select name="route_settings" id="route_settings" class="form-control">
														<option value="">
															---NONE---
														</option>	
													<?php
														for($i=0;$i<count($ingroups->group_id);$i++){
															if($ingroups->group_id[$i] != "AGENTDIRECT"){
													?>
														<option value="<?php echo $ingroups->group_id[$i];?>">
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
										<div id="form_route_ingroup" class="form-group" style="display: none;">										
											<label for="route_ingroupid" class="col-sm-3 control-label">In-Group ID: </label>
											<div class="col-sm-9 mb">
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
												<label  for="route_phone_exten" class="col-sm-3 control-label">Phone Extension: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_exten" id="route_phone_exten" class="form-control">
														<?php
															for($i=0;$i<count($phones->extension);$i++){
														?>
															<option value="<?php echo $phones->extension;?>">
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
												<label for="route_ivr" class="col-sm-3 control-label">Call Menu: </label>
												<div class="col-sm-9 mb">
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
												<label for="route_voicemail" class="col-sm-3 control-label">Voicemail Box: </label>
												<div class="col-sm-9 mb">
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
												<label for="route_exten" class="col-sm-3 control-label">Extension: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" required>
												</div>
											</div>
											<div class="form-group">
												<label for="route_exten_context" class="col-sm-3 control-label">Extension Context: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" required>
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
    			$(".colorpicker").colorpicker();

				$('#route_unavail').on('change', function() {
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
			
			// drop action change
				$('#drop_action').on('change', function() {
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
				$('#no_agent_action').on('change', function() {
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
				  var target = $(e.target).attr("href") // activated tab
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
								window.setTimeout(function(){location.replace("./telephonyinbound.php")},2000);
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
					    	idgroup: groupID
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
				});

				//IVR
				$("#modifyivr").validate({
                	submitHandler: function() {
						//submit the form
							$.post("./php/ModifyTelephonyInbound.php", //post
							$("#modifyivr").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
										swal("Updated!", "IVR has been successfully updated.", "success");			
									} else {
										sweetAlert("Oops...","Something went wrong! " + data, "error");
									}
									//
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
										swal("Updated!", "DID has been successfully updated.", "success");
										$('#update_button').html("<i class='fa fa-check'></i> Update");
										$('#modifyDIDOkButton').prop("disabled", false);
										window.setTimeout(function(){location.replace("./telephonyinbound.php")},2000);
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
			});
		
			function checkdatas(groupID) {
		        if (groupID != undefined) {
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
						    	idgroup: groupID
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
