<?php
/**
 * @file        edittelephonyinbound.php
 * @brief       Edit Inbound, IVR & DID
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
	require_once('./php/CRMDefaults.php');
	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/LanguageHandler.php');
	require('./php/Session.php');

	// initialize structures
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

$groupid = NULL;
if (isset($_POST["groupid"])) {
	$groupid = $_POST["groupid"];
}

$ivr_id = NULL;
if (isset($_POST["ivr"])) {
	$ivr_id = $_POST["ivr"];
}

$did = NULL;
if (isset($_POST["did"])) {
	$did = $_POST["did"];
}

if (!isset($_POST["groupid"]) && !isset($_POST["ivr"]) && !isset($_POST["did"])) {
	header("location: telephonyinbound.php");
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("edit"); ?>
        	<?php 
        		if ($groupid != NULL) {echo "In-Group";}
        		if ($ivr_id != NULL) {echo "Interactive Voice Record";}
        		if ($did != NULL) {echo "DID/Phone Number";}
        	?>
        </title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
        
        <!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">

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
                    	<?php $lh->translateText("inbound"); ?>
                        <small><?php $lh->translateText("edit"); ?> 
							<?php
								if ($groupid != NULL) {echo $lh->translateText('in_group');}
								if ($ivr_id != NULL) {echo $lh->translateText('interactive_voice_record');}
								if ($did != NULL) {echo $lh->translateText('did_phone_number');}
							?>
						</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("Telephony"); ?></li>
                        <?php
							if ($groupid != NULL || $ivr_id != NULL || $did != NULL) {
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
					if ($groupid != NULL) {

					/* APIs used for forms */
						$call_menu = $api->API_getAllIVRs();
						$call_time = $api->API_getAllCalltimes();
						$scripts = $api->API_getAllScripts();
						$voicemail = $api->API_getAllVoiceMails();
						$ingroup = $api->API_getAllInGroups();
						$voicefiles = $api->API_getAllVoiceFiles();
						$moh = $api->API_getAllMusicOnHold();
						$phonenumber = $api->API_getAllDIDs();

						$output = $api->API_getInGroupInfo($groupid);

						if ($output->result=="success") {

					?>			
				<!-- Main content -->
                 <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend><?php $lh->translateText("modify_ingroup"); ?> <u><?php echo $groupid;?></u></legend>

							<form id="modifyingroup">
								<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
								<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />

							<div role="tabpanel">
								<ul role="tablist" class="nav nav-tabs nav-justified">
								 <!-- Settings panel tabs-->
									 <li role="presentation" class="active">
										<a href="#settings" data-toggle="tab">
										<?php $lh->translateText("basic_settings"); ?></a>
									 </li>
								<!-- Advanced settings tab -->
									 <li role="presentation">
										<a href="#advanced_settings" data-toggle="tab">
										<?php $lh->translateText("advance_settings"); ?> </a>
									 </li>
								<!-- Agents tab -->
									 <li role="presentation">
										<a href="#agents" data-toggle="tab">
										<?php $lh->translateText("agents"); ?> </a>
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
												<label for="description" class="col-sm-3 control-label"> <?php $lh->translateText("description"); ?> </label>
												<div class="col-sm-9 mb">
													<input type="text" class="form-control" name="desc" id="description" value="<?php echo $output->data->group_name;?>">
												</div>
											</div>
											<div class="form-group">
												<?php $output->data->group_color = "#".$output->data->group_color;?>
												<label for="color" class="col-sm-3 control-label"><?php $lh->translateText("color"); ?> </label>
												<div class="col-sm-9 mb">
									                <input type="text" class="form-control colorpicker" name="color" id="color" value="<?php echo $output->data->group_color;?>">
												</div>
											</div>
											<div class="form-group">
												<label for="status" class="col-sm-3 control-label"><?php $lh->translateText("status"); ?> </label>
												<div class="col-sm-9 mb">
													<select class="form-control" name="status" id="status">
													<?php
														$status = NULL;
														if ($output->data->active == "Y") {
															$status .= '<option value="Y" selected> '.$lh->translationFor("active").' </option>';
														} else {
															$status .= '<option value="Y" > '.$lh->translationFor("active").' </option>';
														}
														
														if ($output->data->active == "N") {
															$status .= '<option value="N" selected> '.$lh->translationFor("inactive").' </option>';
														} else {
															$status .= '<option value="N" > '.$lh->translationFor("inactive").' </option>';
														}
														echo $status;
													?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="webform" class="col-sm-3 control-label"><?php $lh->translateText("web"); ?></label>
												<div class="col-sm-9 mb">
													<input type="text" class="form-control" name="webform" id="webform" value="<?php echo $output->data->web_form_address;?>">
												</div>
											</div>
											<div class="form-group">
												<label for="nextagent" class="col-sm-3 control-label"><?php $lh->translateText("next_agent_call"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="nextagent" name="nextagent">
														<?php
															$next = NULL;
															if ($output->data->next_agent_call == "random") {
																$next .= '<option value="random" selected> random </option>';
															} else {
																$next .= '<option value="random" > random </option>';
															}
															
															if ($output->data->next_agent_call == "oldest_call_start") {
																$next .= '<option value="oldest_call_start" selected> oldest_call_start </option>';
															} else {
																$next .= '<option value="oldest_call_start" > oldest_call_start </option>';
															}
															
															if ($output->data->next_agent_call == "oldest_call_finish") {
																$next .= '<option value="oldest_call_finish" selected> oldest_call_finish </option>';
															} else {
																$next .= '<option value="oldest_call_finish" > oldest_call_finish </option>';
															}
															
															if ($output->data->next_agent_call == "overall_user_level") {
																$next .= '<option value="overall_user_level" selected> overall_user_level </option>';
															} else {
																$next .= '<option value="overall_user_level" > overall_user_level </option>';
															}
															
															if ($output->data->next_agent_call == "ingroup_rank") {
																$next .= '<option value="ingroup_rank" selected> ingroup_rank </option>';
															} else {
																$next .= '<option value="ingroup_rank" > ingroup_rank </option>';
															}
															/*
															if ($output->data->next_agent_call == "campaign_rank") {
																$next .= '<option value="campaign_rank" selected> campaign_rank </option>';
															} else {
																$next .= '<option value="campaign_rank" > campaign_rank </option>';
															}
															*/
															if ($output->data->next_agent_call == "fewest_calls") {
																$next .= '<option value="fewest_calls" selected> fewest_calls </option>';
															} else {
																$next .= '<option value="fewest_calls" > fewest_calls </option>';
															}
															
															if ($output->data->next_agent_call == "fewest_calls_campaign") {
																$next .= '<option value="fewest_calls_campaign" selected> fewest_calls_campaign </option>';
															} else {
																$next .= '<option value="fewest_calls_campaign" > fewest_calls_campaign </option>';
															}
															
															if ($output->data->next_agent_call == "longest_wait_time") {
																$next .= '<option value="longest_wait_time" selected> longest_wait_time </option>';
															} else {
																$next .= '<option value="longest_wait_time" > longest_wait_time </option>';
															}
															
															if ($output->data->next_agent_call == "ring_all") {
																$next .= '<option value="ring_all" selected> ring_all </option>';
															} else {
																$next .= '<option value="ring_all" > ring_all </option>';
															}
															echo $next;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="priority" class="col-sm-3 control-label"><?php $lh->translateText("queue_priority"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="priority" name="priority">
														<?php
														$prio = NULL;
															for($a=99; $a >= -99; $a--) {
							                                    $a_desc = "";
							                                   
							                                   if ($a < 0) {
							                                       $a_desc = "Lower";
							                                   }
							                                   if ($a == 0) {
							                                       $a_desc = "Even";
							                                   }
							                                   if ($a > 0) {
							                                       $a_desc = "Higher";
							                                   }
							                                       if ($output->data->queue_priority == $a) {
							                                           $prio .= '<option value="'.$a.'" selected> '.$a.'  -  '.$a_desc.' </option>';
							                                       } else {
							                                           $prio .= '<option value="'.$a.'">'.$a.'  -  '.$a_desc.' </option>';
																}
							                                }
							                                echo $prio;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="display" class="col-sm-3 control-label"><?php $lh->translateText("fronter_display"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="display" name="display">
														<?php
														$display = NULL;
															if ($output->data->fronter_display == "Y") {
																$display .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
															} else {
																$display .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
															}
															
															if ($output->data->fronter_display == "N") {
																$display .= '<option value="N" selected> '.$lh->translationFor("go_no").' </option>';
															} else {
																$display .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
															}
														echo $display;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="script" class="col-sm-3 control-label"><?php $lh->translateText("script"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control select2" id="script" name="script">
														<?php
														$script = NULL;

															if ($output->data->ingroup_script == NULL) {
																$script .= '<option value="NONE" selected> '.$lh->translationFor("-none-").' </option>';
															} else {
																$script .= '<option value="NONE" > '.$lh->translationFor("-none-").' </option>';
															}

															for($x=0; $x<count($scripts->script_id);$x++) {
																if ($output->data->ingroup_script == $scripts->script_id[$x]) {
																	$script .= '<option value="'.$scripts->script_id[$x].'" selected> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
																} else {
																	$script .= '<option value="'.$scripts->script_id[$x].'"> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
																}

															}
														echo $script;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="drop_call_seconds" class="col-sm-3 control-label"><?php $lh->translateText("drop_call_seconds"); ?></label>
												<div class="col-sm-9 mb">
													<input type="number" class="form-control" name="drop_call_seconds" id="drop_call_seconds" value="<?php echo $output->data->drop_call_seconds;?>">
												</div>
											</div>
											<div class="form-group">
												<label for="drop_action" class="col-sm-3 control-label"><?php $lh->translateText("drop_action"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="drop_action" name="drop_action">
														<?php
														$drop_action = NULL;
															if ($output->data->drop_action == "HANGUP") {
																$drop_action .= '<option value="HANGUP" selected> HANGUP </option>';
															} else {
																$drop_action .= '<option value="HANGUP" > HANGUP </option>';
															}
															
															if ($output->data->drop_action == "MESSAGE") {
																$drop_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															} else {
																$drop_action .= '<option value="MESSAGE" > MESSAGE </option>';
															}

															if ($output->data->drop_action == "VOICEMAIL") {
																$drop_action .= '<option value="VOICEMAIL" selected> VOICEMAIL </option>';
															} else {
																$drop_action .= '<option value="VOICEMAIL" > VOICEMAIL </option>';
															}

															if ($output->data->drop_action == "IN_GROUP") {
																$drop_action .= '<option value="IN_GROUP" selected> IN_GROUP </option>';
															} else {
																$drop_action .= '<option value="IN_GROUP" > IN_GROUP </option>';
															}

															if ($output->data->drop_action == "CALLMENU") {
																$drop_action .= '<option value="CALLMENU" selected> CALLMENU </option>';
															} else {
																$drop_action .= '<option value="CALLMENU" > CALLMENU </option>';
															}
															/*
															if ($output->data->drop_action == "VMAIL_NO_INST") {
																$drop_action .= '<option value="VMAIL_NO_INST" selected> VMAIL_NO_INST </option>';
															} else {
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
														<div class="drop_exten_message" <?php if ($output->data->drop_action != "MESSAGE") {?> style="display:none;"<?php }?> >
															<label for="drop_exten" class="col-sm-3 control-label"><?php $lh->translateText("drop_exten"); ?></label>
															<div class="col-sm-9 mb">
																<input type="number" class="form-control" name="drop_exten" id="drop_exten" value="<?php echo $output->data->drop_exten;?>" />
															</div>
														</div><!-- /. message -->
													
													<!-- IF VOICEMAIL IS SELECTED -->
														<div class="drop_exten_voicemail" <?php if ($output->data->drop_action != "VOICEMAIL") {?> style="display:none;" <?php }?> >
															<label for="voicemail_ext" class="col-sm-3 control-label"><?php $lh->translateText("voicemail"); ?></label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="voicemail_ext" name="voicemail_ext" style="width:100%;">
																	<?php
																		$drop_action_voicemail = NULL;
																			for($x=0; $x < count($voicemail->voicemail_id);$x++) {
																				if ($output->data->voicemail_ext == $voicemail->voicemail_id[$x]) {
																					$drop_action_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'" selected> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																				} else {
																					$drop_action_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'"> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																				}
																			}
																		echo $drop_action_voicemail;
																	?>
																</select>
															</div>
														</div><!-- /. voicemail -->

													<!-- IF IN_GROUP IS SELECTED -->
														<div class="drop_exten_ingroup" <?php if ($output->data->drop_action != "IN_GROUP") { ?>style="display:none;"<?php }?>>
															<label for="drop_inbound_group" class="col-sm-3 control-label"><?php $lh->translateText("drop_transfer_group"); ?> </label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="drop_inbound_group" name="drop_inbound_group" style="width:100%;">
																	<?php
																		$drop_action_ingroup = NULL;
																			for($x=0; $x<count($ingroup->group_id);$x++) {
																				if ($output->data->drop_inbound_group == $ingroup->group_id[$x]) {
																					$drop_action_ingroup .= '<option value="'.$ingroup->group_id[$x].'" selected> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																				} else {
																					$drop_action_ingroup .= '<option value="'.$ingroup->group_id[$x].'"> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																				}
																			}
																		echo $drop_action_ingroup;
																	?>
																</select>
															</div>
														</div><!-- /. ingroup -->

													<!-- IF CALLMENU IS SELECTED -->
														<div class="drop_exten_callmenu" <?php if ($output->data->drop_action != "CALLMENU") { ?>style="display:none;"<?php }?>>
															<label for="drop_callmenu" class="col-sm-3 control-label"><?php $lh->translateText("drop_callmenu"); ?> </label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="drop_callmenu" name="drop_callmenu" style="width:100%;">
																	<?php
																		$drop_exten_callmenu = NULL;
																			for($x=0; $x < count($call_menu->menu_id);$x++) {
																				if ($output->data->drop_callmenu == $call_menu->menu_id[$x]) {
																					$drop_exten_callmenu .= '<option value="'.$call_menu->menu_id[$x].'" selected> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																				} else {
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
												<label for="call_time_id" class="col-sm-3 control-label"><?php $lh->translateText("call_time"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control select2" id="call_time_id" name="call_time_id">
														<?php
														$call_time_id = NULL;
															if ($call_time->call_time_id[0] == NULL) {
																$call_time_id .= '<option value="NONE" selected> '.$lh->translationFor("-none-").' </option>';
															} else {
																$call_time_id .= '<option value="NONE" > '.$lh->translationFor("-none-").' </option>';
																for($x=0; $x<count($call_time->call_time_id);$x++) {
																	if ($output->data->call_time_id == $call_time->call_time_id[$x]) {
																		$call_time_id .= '<option value="'.$call_time->call_time_id[$x].'" selected> '.$call_time->call_time_id[$x].' - '.$call_time->call_time_name[$x].' </option>';
																	} else {
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
												<label for="call_launch" class="col-sm-3 control-label"><?php $lh->translateText("get_call_launch"); ?></label>
												<div class="col-sm-9 mb">
													<select class="form-control" id="call_launch" name="call_launch">
														<?php
														$call_launch = NULL;
															if ($output->data->get_call_launch == "NONE") {
																$call_launch .= '<option value="NONE" selected> '.$lh->translationFor("-none-").' </option>';
															} else {
																$call_launch .= '<option value="NONE" > '.$lh->translationFor("-none-").' </option>';
															}
																
															if ($output->data->get_call_launch == "SCRIPT") {
																$call_launch .= '<option value="SCRIPT" selected> SCRIPT </option>';
															} else {
																$call_launch .= '<option value="SCRIPT" > SCRIPT </option>';
															}

															if ($output->data->get_call_launch == "WEBFORM") {
																$call_launch .= '<option value="WEBFORM" selected> WEBFORM </option>';
															} else {
																$call_launch .= '<option value="WEBFORM" > WEBFORM </option>';
															}

															if ($output->data->get_call_launch == "FORM") {
																$call_launch .= '<option value="FORM" selected> FORM </option>';
															} else {
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
																		for($x=0; $x<count($ingroup->group_id);$x++) {									
																			if ($output->data->afterhours_xfer_group == $ingroup->group_id[$x]) {
																				$after_hour_ingroup .= '<option value="'.$ingroup->group_id[$x].'" selected> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																			} else {
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
												<label for="welcome_message_filename" class="col-sm-4 control-label"><?php $lh->translateText("welcome_message_filename"); ?></label>
												<div class="col-sm-8 mb">
													<div class="input-group">
														<input type="text" class="form-control" class="" id="welcome_message_filename" name="welcome_message_filename" value="<?php if ($output->data->welcome_message_filename == NULL )echo "sip-silence"; else echo $output->data->welcome_message_filename;?>">
														<span class="input-group-btn">
															<button class="btn btn-default show_welcome_message_filename" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
														</span>
													</div><!-- /input-group -->
													<div class="row col-sm-12 select_welcome_message_filename">
														<select class="form-control select2" id="select_welcome_message_filename" style="width:100%;">
															<option value="sip-silence"><?php $lh->translateText("default"); ?></option>
															<?php
																$welcome_message_filename = NULL;
																	for($x=0; $x < count($voicefiles->file_name);$x++) {
																		$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																		if ($output->data->welcome_message_filename == $this_file_name) {
																			$welcome_message_filename .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																		} else {
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
												<label for="play_welcome_message" class="col-sm-4 control-label"><?php $lh->translateText("play_welcome_message"); ?></label>
												<div class="col-sm-8 mb">
													<select class="form-control" id="play_welcome_message" name="play_welcome_message">
														<?php
														$play_welcome_message = NULL;
															if ($output->data->play_welcome_message == "ALWAYS") {
																$play_welcome_message .= '<option value="ALWAYS" selected> ALWAYS </option>';
															} else {
																$play_welcome_message .= '<option value="ALWAYS" > ALWAYS </option>';
															}
															
															if ($output->data->play_welcome_message == "NEVER") {
																$play_welcome_message .= '<option value="NEVER" selected> NEVER </option>';
															} else {
																$play_welcome_message .= '<option value="NEVER" > NEVER </option>';
															}
	
															if ($output->data->play_welcome_message == "IF_WAIT_ONLY") {
																$play_welcome_message .= '<option value="IF_WAIT_ONLY" selected> IF_WAIT_ONLY </option>';
															} else {
																$play_welcome_message .= '<option value="IF_WAIT_ONLY" > IF_WAIT_ONLY </option>';
															}
	
															if ($output->data->play_welcome_message == "YES_UNLESS_NODELAY") {
																$play_welcome_message .= '<option value="YES_UNLESS_NODELAY" selected> YES_UNLESS_NODELAY </option>';
															} else {
																$play_welcome_message .= '<option value="YES_UNLESS_NODELAY" > YES_UNLESS_NODELAY </option>';
															}
	
														echo $play_welcome_message;
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="moh_context" class="col-sm-4 control-label"><?php $lh->translateText("music_on_hold_context"); ?></label>
												<div class="col-sm-8 mb">
													<div class="input-group">
														<input type="text" class="form-control" id="moh_context" name="moh_context" value="<?php if ($output->data->moh_context == NULL)echo "default"; else echo $output->data->moh_context;?>">
														<span class="input-group-btn">
															<button class="btn btn-default show_moh_context" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
														</span>
													</div><!-- /input-group -->
													<div class="row col-sm-12 select_moh_context">
														<select class="form-control select2" id="select_moh_context" style="width:100%;">
															<option value="default"><?php $lh->translateText('default'); ?></option>
															<?php
																$moh_context = NULL;
																	for($x=0; $x < count($moh->moh_id);$x++) {
																		if ($moh->moh_id[$x] != "default") {
																			if ($output->data->moh_context == $moh->moh_id[$x]) {
																				$moh_context .= '<option value="'.$moh->moh_id[$x].'" selected> '.$moh->moh_name[$x].' </option>';
																			} else {
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
												<label for="onhold_prompt_filename" class="col-sm-4 control-label"><?php $lh->translateText("on_hold_prompt_filename"); ?></label>
												<div class="col-sm-8 mb">
													<div class="input-group">
														<input type="text" class="form-control" id="onhold_prompt_filename" name="onhold_prompt_filename" value="<?php if ($output->data->onhold_prompt_filename == NULL)echo "generic_hold"; else echo $output->data->onhold_prompt_filename;?>">
														<span class="input-group-btn">
															<button class="btn btn-default show_onhold_prompt_filename" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
														</span>
													</div><!-- /input-group -->
													<div class="row col-sm-12 select_onhold_prompt_filename">
														<select class="form-control select2" id="select_onhold_prompt_filename" style="width:100%;">
															<option value="generic_hold"><?php $lh->translateText("default"); ?></option>
															<?php
																$onhold_prompt_filename = NULL;
																	for($x=0; $x < count($voicefiles->file_name);$x++) {
																		$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																		if ($output->data->onhold_prompt_filename == $this_file_name) {
																			$onhold_prompt_filename .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																		} else {
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
												<label for="after_hours_action" class="col-sm-4 control-label"><?php $lh->translateText("after_hours_action"); ?></label>
												<div class="col-sm-8 mb">
													<select class="form-control" id="after_hours_action" name="after_hours_action">
														<?php
														$after_hours_action = NULL;
															
															if ($output->data->after_hours_action == "MESSAGE") {
																$after_hours_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															} else {
																$after_hours_action .= '<option value="MESSAGE" > MESSAGE </option>';
															}
															
															if ($output->data->after_hours_action == "HANGUP") {
																$after_hours_action .= '<option value="HANGUP" selected> HANGUP </option>';
															} else {
																$after_hours_action .= '<option value="HANGUP" > HANGUP </option>';
															}
															
															if ($output->data->after_hours_action == "EXTENSION") {
																$after_hours_action .= '<option value="EXTENSION" selected> EXTENSION </option>';
															} else {
																$after_hours_action .= '<option value="EXTENSION" > EXTENSION </option>';
															}
															
															if ($output->data->after_hours_action == "VOICEMAIL") {
																$after_hours_action .= '<option value="VOICEMAIL" selected> VOICEMAIL </option>';
															} else {
																$after_hours_action .= '<option value="VOICEMAIL" > VOICEMAIL </option>';
															}
															
															if ($output->data->after_hours_action == "CALLMENU") {
																$after_hours_action .= '<option value="CALLMENU" selected> CALLMENU </option>';
															} else {
																$after_hours_action .= '<option value="CALLMENU" > CALLMENU </option>';
															}
															/*if ($output->data->after_hours_action == "IN_GROUP") {
																$after_hours_action .= '<option value="IN_GROUP" selected> IN_GROUP </option>';
															} else {
																$after_hours_action .= '<option value="IN_GROUP" > IN_GROUP </option>';
															}*/
													echo $after_hours_action;
													?>
												</select>
											</div>
										</div>
										<div class="after_hours after_hours_message_filename form-group" <?php if ($output->data->after_hours_action != "MESSAGE") { ?> style="display:none;"<?php }?>>
											<label for="after_hours_message_filename" class="col-sm-4 control-label"><?php $lh->translateText("after_hours_message_filename"); ?></label>
											<div class="col-sm-8 mb">
												<div class="input-group">
													<input type="text" class="form-control" id="after_hours_message_filename" name="after_hours_message_filename" value="<?php if ($output->data->after_hours_message_filename == NULL)echo "vm-goodbye"; else echo $output->data->after_hours_message_filename;?>">
													<span class="input-group-btn">
														<button class="btn btn-default show_after_hours_message_filename" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
													</span>
												</div><!-- /input-group -->
												<div class="row col-sm-12 select_after_hours_message_filename">
													<select class="form-control select2" id="select_after_hours_message_filename" style="width:100%;">
														<option value="vm-goodbye"><?php $lh->translateText("default"); ?></option>
														<?php
															$after_hours_message_filename = NULL;
																for($x=0; $x < count($voicefiles->file_name);$x++) {
																	$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																	if ($output->data->after_hours_message_filename == $this_file_name) {
																		$after_hours_message_filename .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																	} else {
																		$after_hours_message_filename .= '<option value="'.$this_file_name.'"> '.$this_file_name.' </option>';
																	}
																}
															echo $after_hours_message_filename;
														?>
													</select>
												</div>
												<br/>
											</div>
										</div>
										<div class="after_hours after_hours_exten form-group" <?php if ($output->data->after_hours_action != "EXTENSION") { ?> style="display:none;"<?php }?>>
											<label for="after_hours_exten" class="col-sm-4 control-label"><?php $lh->translateText("after_hours_extension"); ?></label>
											<div class="col-sm-8 mb">
												<input type="number" class="form-control" name="after_hours_exten" id="after_hours_exten" value="<?php if ($output->data->after_hours_exten != NULL)echo $output->data->after_hours_exten; else echo "8300";?>" />
											<br/>
											</div>
										</div>
										<div class="after_hours after_hours_voicemail form-group" <?php if ($output->data->after_hours_action != "VOICEMAIL") { ?> style="display:none;"<?php }?>>
											<label for="after_hours_voicemail" class="col-sm-4 control-label"><?php $lh->translateText("after_hours_voicemail"); ?></label>
											<div class="col-sm-8 mb">
												<div class="input-group">
													<input type="text" class="form-control" id="after_hours_voicemail" name="after_hours_voicemail" value="<?php if ($output->data->after_hours_voicemail == NULL)echo ""; else echo $output->data->after_hours_voicemail;?>">
													<span class="input-group-btn">
														<button class="btn btn-default show_after_hours_voicemail" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
													</span>
												</div><!-- /input-group -->
												<div class="row col-sm-12 select_after_hours_voicemail">
													<select class="form-control select2" id="select_after_hours_voicemail" style="width:100%;">
														<option value=""> <?php $lh->translateText("-none-"); ?> </option>
														<?php
															$after_hour_voicemail = NULL;
																for($x=0; $x < count($voicemail->voicemail_id);$x++) {
																	if ($output->data->after_hours_voicemail == $voicemail->voicemail_id[$x]) {
																		$after_hour_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'" selected> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																	} else {
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
										<div class="after_hours after_hours_callmenu form-group" <?php if ($output->data->after_hours_action != "CALLMENU") { ?> style="display:none;"<?php }?>>
											<label for="after_hours_callmenu" class="col-sm-4 control-label"><?php $lh->translateText("after_hours_callmenu"); ?> </label>
											<div class="col-sm-8 mb">
												<div class="input-group">
													<input type="text" class="form-control" id="after_hours_callmenu" name="after_hours_callmenu" value="<?php if ($output->data->after_hours_callmenu == NULL)echo ""; else echo $output->data->after_hours_callmenu;?>">
													<span class="input-group-btn">
														<button class="btn btn-default show_after_hours_callmenu" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
													</span>
												</div><!-- /input-group -->
												<div class="row col-sm-12 select_after_hours_callmenu">
													<select class="form-control select2-2" id="select_after_hours_callmenu" style="width:100%;">
														<option value=""><?php $lh->translateText("-none-"); ?></option>
														<?php
															$no_agents_callmenu = NULL;
																for($x=0; $x < count($call_menu->menu_id);$x++) {
																	if ($output->data->after_hours_callmenu == $call_menu->menu_id[$x]) {
																		$no_agents_callmenu .= '<option value="'.$call_menu->menu_id[$x].'" selected> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																	} else {
																		$no_agents_callmenu .= '<option value="'.$call_menu->menu_id[$x].'"> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																	}
																}
															echo $no_agents_callmenu;
														?>
													</select>
												</div>
												<br/>
											</div>
										</div><!-- /. callmenu -->
										
							       			<div class="form-group mt">
							       				<label for="no_agent_no_queue" class="col-sm-4 control-label"><?php $lh->translateText("accept_calls_when_no_available_agent"); ?></label>
							       				<div class="col-sm-8 mb">
												<select class="form-control" id="no_agent_no_queue" name="no_agent_no_queue">
													<?php
													$no_agent_no_queue = NULL;
														if ($output->data->no_agent_no_queue == "N") {
															$no_agent_no_queue .= '<option value="N" selected> '.$lh->translationFor("go_no").' </option>';
														} else {
															$no_agent_no_queue .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
														}
														
														if ($output->data->no_agent_no_queue == "Y") {
															$no_agent_no_queue .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
														} else {
															$no_agent_no_queue .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
														}

														if ($output->data->no_agent_no_queue == "NO_PAUSED") {
															$no_agent_no_queue .= '<option value="NO_PAUSED" selected> NO PAUSED </option>';
														} else {
															$no_agent_no_queue .= '<option value="NO_PAUSED" > NO PAUSED </option>';
														}
														if ($output->data->no_agent_no_queue == "NO_READY") {
															$no_agent_no_queue .= '<option value="NO_READY" selected> NO READY </option>';
														} else {
															$no_agent_no_queue .= '<option value="NO_READY" > NO READY </option>';
														}														
													echo $no_agent_no_queue;
													?>
												</select>
											</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="no_agent_action" class="col-sm-4 control-label"><?php $lh->translateText("no_available_agent_routing"); ?></label>
							       				<div class="col-sm-8 mb">
												<select class="form-control" id="no_agent_action" name="no_agent_action">
													<?php
														$no_agent_action = NULL;
															/*if ($output->data->no_agent_action == "HANGUP") {
																$no_agent_action .= '<option value="HANGUP" selected> HANGUP </option>';
															} else {
																$no_agent_action .= '<option value="HANGUP" > HANGUP </option>';
															}*/
															if ($output->data->no_agent_action == "DID") {
																$no_agent_action .= '<option value="DID" selected> DID </option>';
															} else {
																$no_agent_action .= '<option value="DID" > DID </option>';
															}
															if ($output->data->no_agent_action == "MESSAGE") {
																$no_agent_action .= '<option value="MESSAGE" selected> MESSAGE </option>';
															} else {
																$no_agent_action .= '<option value="MESSAGE" > MESSAGE </option>';
															}
															if ($output->data->no_agent_action == "VOICEMAIL") {
																$no_agent_action .= '<option value="VOICEMAIL" selected> VOICEMAIL </option>';
															} else {
																$no_agent_action .= '<option value="VOICEMAIL" > VOICEMAIL </option>';
															}
															if ($output->data->no_agent_action == "INGROUP") {
																$no_agent_action .= '<option value="INGROUP" selected> IN_GROUP </option>';
															} else {
																$no_agent_action .= '<option value="INGROUP" > IN_GROUP </option>';
															}
															if ($output->data->no_agent_action == "CALLMENU") {
																$no_agent_action .= '<option value="CALLMENU" selected> CALLMENU </option>';
															} else {
																$no_agent_action .= '<option value="CALLMENU" > CALLMENU </option>';
															}
															//added Feature #7292
															if ($output->data->no_agent_action == "EXTENSION") {
                                                                                                                                $no_agent_action .= '<option value="EXTENSION" selected> EXTENSION </option>';
                                                                                                                        } else {
                                                                                                                                $no_agent_action .= '<option value="EXTENSION" > EXTENSION </option>';
                                                                                                                        }
														echo $no_agent_action;
													?>
												</select>
											</div>
							       			</div>
											
							       			<!-- NO AGENTS EXTEN -->
												<div class="form-group no_agents_exten">
													<!-- IF MESSAGE IS SELECTED -->
														<div class="no_agents_message" <?php if ($output->data->no_agent_action != "MESSAGE") {?> style="display:none;"<?php }?> >
															<label for="no_agents_exten" class="col-sm-4 control-label"><?php $lh->translateText("audiofiles"); ?></label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_exten" name="no_agents_exten" value="<?php if ($output->data->no_agent_action_value == NULL || !in_array($output->data->no_agent_action_value, $voicefiles->file_name))echo "vm-goodbye"; else echo $output->data->no_agent_action_value;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_exten" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_exten">
																	<select class="form-control select2-2" id="select_no_agents_exten" style="width:100%;">
																		<option value="vm-goodbye"><?php $lh->translateText("default"); ?></option>
																		<?php
																			$no_agents_exten = NULL;
																				for($x=0; $x < count($voicefiles->file_name);$x++) {
																					$this_file_name = preg_replace("/\.(wav|mp3)$/", "", $voicefiles->file_name[$x]);
																					if ($output->data->no_agent_action_value == $this_file_name) {
																						$no_agents_exten .= '<option value="'.$this_file_name.'" selected> '.$this_file_name.' </option>';
																					} else {
																						$no_agents_exten .= '<option value="'.$this_file_name.'"> '.$this_file_name.' </option>';
																					}
																				}
																			echo $no_agents_exten;
																		?>
																	</select>
																</div>
															</div>
														</div><!-- /. message -->

													<!-- IF DID IS SELECTED -->
														<div class="no_agents_did" <?php if ($output->data->no_agent_action != "DID") {?> style="display:none;" <?php }?> >
															<label for="no_agents_did" class="col-sm-4 control-label"><?php $lh->translateText("did"); ?></label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_did" name="no_agents_did" value="<?php if ($output->data->no_agent_action != "DID" || $output->data->no_agent_action_value == NULL)echo ""; else echo $output->data->no_agent_action_value;?>" required>
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_did" type="button"><?php $lh->translateText("DID Chooser"); ?></button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_did">
																	<select class="form-control select2-2" id="select_no_agents_did" style="width:100%;">
																		<option value=""><?php $lh->translateText("-none-"); ?></option>
																		<?php
																			$no_agents_did = NULL;
																				for($x=0; $x < count($phonenumber->did_id);$x++) {
																					if ($output->data->no_agent_action_value == $phonenumber->did_id[$x]) {
																						$no_agents_did .= '<option value="'.$phonenumber->did_id[$x].'" selected> '.$phonenumber->did_id[$x].' - '.$phonenumber->did_pattern[$x].' </option>';
																					} else {
																						$no_agents_did .= '<option value="'.$phonenumber->did_id[$x].'"> '.$phonenumber->did_id[$x].' - '.$phonenumber->did_pattern[$x].' </option>';
																					}
																				}
																			echo $no_agents_did;
																		?>																		
																	</select>
																</div>
															</div>
														</div><!-- /. voicemail -->
														
													<!-- IF VOICEMAIL IS SELECTED -->
														<div class="no_agents_voicemail" <?php if ($output->data->no_agent_action != "VOICEMAIL") {?> style="display:none;" <?php }?> >
															<label for="no_agents_voicemail" class="col-sm-4 control-label"><?php $lh->translateText("voicemail"); ?></label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_voicemail" name="no_agents_voicemail" value="<?php if ($output->data->no_agent_action_value == NULL)echo ""; else echo $output->data->no_agent_action_value;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_voicemail" type="button"><?php $lh->translateText("audio_chooser"); ?></button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_voicemail">
																	<select class="form-control select2-2" id="select_no_agents_voicemail" style="width:100%;">
																		<option value=""><?php $lh->translateText("-none-"); ?></option>
																		<?php
																			$no_agents_voicemail = NULL;
																				for($x=0; $x < count($voicemail->voicemail_id);$x++) {
																					if ($output->data->no_agent_action_value == $voicemail->voicemail_id[$x]) {
																						$no_agents_voicemail .= '<option value="'.$voicemail->voicemail_id[$x].'" selected> '.$voicemail->voicemail_id[$x].' - '.$voicemail->fullname[$x].' </option>';
																					} else {
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
														<div class="no_agents_ingroup" <?php if ($output->data->no_agent_action != "INGROUP") { ?> style="display:none;"<?php }?>>
															<label for="no_agents_ingroup" class="col-sm-4 control-label"><?php $lh->translateText("ingroup"); ?></label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_ingroup" name="no_agents_ingroup" value="<?php if ($output->data->no_agent_action_value == NULL || !in_array($output->data->no_agent_action_value, $ingroup->group_id))echo ""; else echo $output->data->no_agent_action_value;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_ingroup" type="button"><?php $lh->translateText("ingroup_chooser"); ?></button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_ingroup">
																	<select class="form-control select2-2" id="select_no_agents_ingroup" style="width:100%;">
																		<option value=""><?php $lh->translateText("-none-"); ?></option>
																		<?php
																			$no_agents_ingroup = NULL;
																				for($x=0; $x<count($ingroup->group_id);$x++) {
																					if ($output->data->no_agent_action_value == $ingroup->group_id[$x]) {
																						$no_agents_ingroup .= '<option value="'.$ingroup->group_id[$x].'" selected> '.$ingroup->group_id[$x].' - '.$ingroup->group_name[$x].' </option>';
																					} else {
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
														<div class="no_agents_callmenu" <?php if ($output->data->no_agent_action != "CALLMENU") { ?> style="display:none;"<?php }?>>
															<label for="no_agents_callmenu" class="col-sm-4 control-label"><?php $lh->translateText("call_menu"); ?> </label>
															<div class="col-sm-8 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="no_agents_callmenu" name="no_agents_callmenu" value="<?php if ($output->data->no_agent_action_value == NULL || !in_array($output->data->no_agent_action_value, $call_menu->menu_id))echo ""; else echo $output->data->no_agent_action_value;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_no_agents_callmenu" type="button"><?php $lh->translateText("callmenu_chooser"); ?></button>
																	</span>
																</div><!-- /input-group -->
																<div class="row col-sm-12 select_no_agents_callmenu">
																	<select class="form-control select2-2" id="select_no_agents_callmenu" style="width:100%;">
																		<option value=""><?php $lh->translateText("-none-"); ?></option>
																		<?php
																			$no_agents_callmenu = NULL;
																				for($x=0; $x < count($call_menu->menu_id);$x++) {
																					if ($output->data->no_agent_action_value == $call_menu->menu_id[$x]) {
																						$no_agents_callmenu .= '<option value="'.$call_menu->menu_id[$x].'" selected> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																					} else {
																						$no_agents_callmenu .= '<option value="'.$call_menu->menu_id[$x].'"> '.$call_menu->menu_id[$x].' - '.$call_menu->menu_name[$x].' </option>';
																					}
																				}
																			echo $no_agents_callmenu;
																		?>
																	</select>
																</div>
															</div>
														</div><!-- /. callmenu -->

													<!-- IF EXTENSION IS SELECTED -->
														<div class="no_agents_extension form-group" <?php if ($output->data->no_agent_action != "EXTENSION") { ?> style="display:none;"<?php }?>>
														    <div class="col-sm-6">
				                                                                                        <label for="no_agents_extension" class="col-sm-4 control-label"><?php $lh->translateText("extension"); ?></label>
                                				                                                        <div class="col-sm-8 mb">
                                                                				                                <input type="number" class="form-control" name="no_agents_extension" id="no_agents_extension" maxlength="255" min="0" value="<?php if ($output->data->no_agent_action_value  != NULL && strpos($output->data->no_agent_action_value, '|')!==false)echo explode('|',$output->data->no_agent_action_value,2)[0]; else echo "8304";?>" />
                                                                                        					<br/>
                                                                              					        </div>
														    </div>
														    <div class="col-sm-6">
															<label for="no_agents_extension_context" class="col-sm-4 control-label"><?php $lh->translateText("context"); ?></label>
                                                                                                                        <div class="col-sm-8 mb">
                                                                                                                                <input type="text" class="form-control" name="no_agents_extension_context" id="no_agents_extension_context" maxlength="255" value="<?php if ($output->data->no_agent_action_value != NULL && strpos($output->data->no_agent_action_value, '|')!==false)echo explode('|',$output->data->no_agent_action_value,2)[1]; else echo "default";?>" />
                                                                                                                                <br/>
                                                                                                                          </div>
														    </div>
                                                                                				</div><!-- /. extension -->

												</div>
											<!-- /.NO AGENTS EXTEN -->
							       		</fieldset>
									</div>

									<fieldset class="footer-buttons" id="not_agent_rank">
			                           <div class="col-sm-3 pull-right">
										<a href="telephonyinbound.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?></a>
									    <a type="submit" class="btn btn-primary" id="modifyInboundOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></a>
			                           </div>
				                    </fieldset>
								</form>
								
									<!--==== Agent Rank Table ====-->
									<div id="agents" class="tab-pane fade in">

									<form id="agentrankform" class="form-horizontal">
										<?php
											$agents_rank = $api->API_getAllAgentRank($groupid);
										?>
										<table class="responsive display no-wrap table table-striped table-bordered table-hover" style="width:100%;" id="agent_rank_table">
										   <thead>
											  <tr>
												 <th><?php $lh->translateText("user"); ?></th>
												 <th><?php $lh->translateText("user_group"); ?></th>
												 <th><?php $lh->translateText("selected"); ?></th>
												 <th><?php $lh->translateText("Rank"); ?></th>
												 <th><?php $lh->translateText("grade"); ?></th>
												 <!--<th>Calls Today</th>-->
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		$count = count($agents_rank->user);
											   		//var_dump($agents_rank->dropdown_rankdefvalues[0]);

											   		for($a=0; $a < $count; $a++) {											   			
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
																	while($b >= -9) {
																?>
																	<option value="<?php echo $b;?>" <?php if ($rank_value == $b) { echo "selected";}?>> <?php echo $b;?></option>
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
																	while($c >= 0) {
																?>
																	<option value="<?php echo $c;?>" <?php if ($grade_value == $c) { echo "selected";}?>> <?php echo $c;?></option>
																<?php
																	$c--;
																	}
																?>
															</select>
														</td>
														<!--<td><?php //echo $agents_rank->call_today[$a];?></td>-->
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
													<a href="telephonyinbound.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?> </a>
											
													<a type="button" class="btn btn-primary" id="submit_agent_rank" data-id="<?php echo $groupid;?>"> <span id="submit_button"><i class="fa fa-check"></i> <?php $lh->translateText("submit"); ?></span></a>
												
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
					if ($ivr_id != NULL) {
						$output = $api->API_getIVRInfo($ivr_id);
						
						if ($output->result=="success") {
							$user_groups = $api->API_getAllUserGroups();
							$ingroups = $api->API_getAllInGroups();							
							$phonenumber = $api->API_getAllDIDs();							
							$campaign = $api->API_getAllCampaigns();
							$voicemails = $api->API_getAllVoicemails();
							$phone_extension = $api->API_getAllPhones();
							$scripts = $api->API_getAllScripts();
							$voicefiles = $api->API_getAllVoiceFiles();
							$calltimes = $api->API_getAllCalltimes();
	
							$ivr_options = $api->API_getIVROptions($ivr_id);
							$ivr = $api->API_getAllIVRs();
						# Result was OK!
					?>
						<section class="content">
							<div class="panel panel-default">
								<div class="panel-body">
									<legend><?php $lh->translateText("modify_ivr"); ?> : <u><?php echo $output->data->menu_id;?></u></legend>

									<form id="modifyivr" class="form-horizontal">
										<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
										<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />

									<div role="tabpanel">
										<ul role="tablist" class="nav nav-tabs nav-justified">
										 <!-- Settings panel tabs-->
											 <li role="presentation" class="active">
												<a href="#tab_1" data-toggle="tab">
												<?php $lh->translateText("basic_settings"); ?></a>
											 </li>
										<!-- Options tab -->
											 <li role="presentation">
												<a href="#tab_2" data-toggle="tab">
												<?php $lh->translateText("option"); ?> </a>
											 </li>
										</ul>
										<input type="hidden" name="modify_ivr" value="<?php echo $output->data->menu_id;?>">
										<div class="tab-content">
											<div class="tab-pane active" id="tab_1">
												<div class="form-group mt">
													<label class="col-sm-3 control-label" for="menu_id"><?php $lh->translateText("menu_id"); ?>:</label>
													<div class="col-sm-9">
														<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID" minlength="4" required title="No Spaces. Minimum of 4 characters" value="<?php echo $output->data->menu_id;?>">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_name"><?php $lh->translateText("menu_name"); ?>: </label>
													<div class="col-sm-9">
														<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name" required value="<?php echo $output->data->menu_name;?>">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_prompt"><?php $lh->translateText("menu_greeting"); ?>: </label>
													<div class="col-sm-9">
														<select name="menu_prompt" id="menu_prompt" class="form-control select2" style="width:100%;">
															<option value="goWelcomeIVR"><?php $lh->translateText("default_value"); ?></option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++) {
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>" <?php if ($file == $output->data->menu_prompt) {echo "selected";}?> ><?php echo $file;?></option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_timeout"><?php $lh->translateText("menu_timeout"); ?> </label>
													<div class="col-sm-9">
														<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="<?php echo $output->data->menu_timeout;?>" min="0" required>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_timeout_prompt"><?php $lh->translateText("menu_timeout_greeting"); ?>: </label>
													<div class="col-sm-9">
														<select name="menut_timeout_prompt" id="menu_timeout_prompt" class="form-control select2" style="width:100%;">
															<option value=""><?php $lh->translateText("default_value"); ?></option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++) {
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>" <?php if ($file == $output->data->menu_timeout_prompt) {echo "selected";}?> ><?php echo $file;?></option>
															<?php
																}
															?>				
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_invalid_prompt"><?php $lh->translateText("menu_invalid_greeting"); ?>: </label>
													<div class="col-sm-9">
														<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control select2" style="width:100%;">
															<option value=""><?php $lh->translateText("default_value"); ?></option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++) {
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>" <?php if ($file == $output->data->menu_invalid_prompt) {echo "selected";}?> ><?php echo $file;?></option>
															<?php
																}
															?>				
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_repeat"><?php $lh->translateText("menu_repeat"); ?>: </label>
													<div class="col-sm-9">
														<input type="number" name="menu_repeat" id="menu_repeat" class="form-control"value="<?php echo $output->data->menu_repeat;?>" min="0" required>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="menu_time_check"><?php $lh->translateText("menu_time_check"); ?>: </label>
													<div class="col-sm-9">
														<select name="menu_time_check" id="menu_time_check" class="form-control">
															<option value="0" <?php if ($output->data->menu_time_check == "0") {echo "selected";}?> >0 - <?php $lh->translateText("no_realtime_tracking"); ?> </option>
															<option value="1" <?php if ($output->data->menu_time_check == "1") {echo "selected";}?> >1 - <?php $lh->translateText("realtime_tracking"); ?> </option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="call_time_id"><?php $lh->translateText("call_time"); ?>: </label>
													<div class="col-sm-9">
														<select name="call_time_id" id="call_time_id" class="form-control select2" style="width:100%;">
															<?php
																for($x=0; $x<count($calltimes->call_time_id);$x++) {
															?>
																<option value="<?php echo $calltimes->call_time_id[$x];?>" <?php if ($calltimes->call_time_id[$x] == $output->data->call_time_id) {echo "selected";} ?> > <?php echo $calltimes->call_time_id[$x].' - '.$calltimes->call_time_name[$x]; ?> </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="track_in_vdac"><?php $lh->translateText("track_call_realtime_report"); ?>: </label>
													<div class="col-sm-9"> 
														<select name="track_in_vdac" id="track_in_vdac" class="form-control">
															<option value="0" <?php if ($output->data->track_in_vdac == "0") {echo "selected";}?> >0 - <?php $lh->translateText("no_realtime_tracking"); ?></option>
															<option value="1" <?php if ($output->data->track_in_vdac == "1") {echo "selected";}?> >1 - <?php $lh->translateText("realtime_tracking"); ?></option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="tracking_group"><?php $lh->translateText("tracking_group"); ?>: </label>
													<div class="col-sm-9">
														<select name="tracking_group" id="tracking_group" class="form-control select2" style="width:100%;">
															<option value="CALLMENU" <?php if ($output->data->tracking_group == 'CALLMENU') {echo "selected";}?> >
																CALLMENU - Default
															</option>
														<?php
															for($i=0;$i<count($ingroups->group_id);$i++) {
														?>
															<option value="<?php echo $ingroups->group_id[$i];?>" <?php if ($ingroups->group_id[$i] == $output->data->tracking_group) {echo "selected";}?> >
																<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
															</option>									
														<?php
															}
														?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="user_group"><?php $lh->translateText("user_groups"); ?>: </label>
													<div class="col-sm-9">
														<select id="user_group" class="form-control select2" name="user_group" style="width:100%;">
																<option value="---ALL---" <?php if ($output->data->user_group == "---ALL---") {echo "selected";}?> > <?php $lh->translateText("all_usergroups"); ?> </option>
															<?php
																for($i=0;$i<count($user_groups->user_group);$i++) {
															?>
																<option value="<?php echo $user_groups->user_group[$i];?>" <?php if ($output->data->user_group == $user_groups->user_group[$i]) {echo "selected";}?> >  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
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
													
													for($i=0;$i < 14; $i++) {
												?>
												<div class="option_div_<?php echo $i;?>">
													<div class="form-group">
														<div class="col-lg-12">
															<div class="col-lg-2">
																<?php $lh->translateText("option"); ?>:
																<select class="form-control route_option" name="option[]"
																<?php 
																	if ($ivr_options->option_value[$i] != "") {
																		if ($ivr_options->option_value[$i] == "#")
																			$ivr_options->option_value[$i] = "A";
																		if ($ivr_options->option_value[$i] == "*")
																			$ivr_options->option_value[$i] = "B";
																		if ($ivr_options->option_value[$i] == "TIMECHECK")
																			$ivr_options->option_value[$i] = "C";
																		if ($ivr_options->option_value[$i] == "TIMEOUT")
																			$ivr_options->option_value[$i] = "D";
																		if ($ivr_options->option_value[$i] == "INVALID")
																			$ivr_options->option_value[$i] = "E";

																		echo 'id="option_'.$ivr_options->option_value[$i].'" ';
																	}
																?> >
																	<option selected></option>
																	<?php
																	$option = '';
																		for($x=0; $x <= 9; $x++) {
																			$option .= '<option value="'.$x.'" ';
																			if ($ivr_options->option_value[$i] == $x && $ivr_options->option_value[$i] != "") {$option .= 'selected ';}
																			if (in_array($x, $ivr_options->option_value)) {$option .= 'disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';}
																			$option .= '>'.$x.'</option>';
																		}
																		echo $option;
																	?>
																	<option value="A" <?php if ($ivr_options->option_value[$i] == "A") {echo 'selected';}if (in_array("#", $ivr_options->option_value)) { echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >#</option>
																	<option value="B" <?php if ($ivr_options->option_value[$i] == "B") {echo 'selected';}if (in_array("*", $ivr_options->option_value)) { echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >*</option>
																	<option value="C" <?php if ($ivr_options->option_value[$i] == "C") {echo 'selected';}if (in_array("TIMECHECK", $ivr_options->option_value)) { echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >TIMECHECK</option>
																	<option value="D" <?php if ($ivr_options->option_value[$i] == "D") {echo 'selected';}if (in_array("TIMEOUT", $ivr_options->option_value)) { echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >TIMEOUT</option>
																	<option value="E" <?php if ($ivr_options->option_value[$i] == "E") {echo 'selected';}if (in_array("INVALID", $ivr_options->option_value)) { echo ' disabled style="background-color: rgb(193, 193, 193); color: rgb(255, 255, 255);"';} ?> >INVALID</option>
																</select>
															</div>
															<div class="col-lg-7">
																<?php $lh->translateText("description"); ?>: 
																<input type="text" name="route_desc[]" class="form-control route_desc_<?php echo $i;?>" placeholder="Description" value="<?php echo $ivr_options->option_description[$i]; ?>" />
															</div>
															<div class="col-lg-3">
																<?php $lh->translateText("route"); ?>:
																<select class="form-control route_menu_<?php echo $i;?>" name="route_menu[]">
																	<option selected value=""></option>
																	<option value="CALLMENU" <?php if ($ivr_options->option_route[$i] == "CALLMENU")echo "selected"; ?> ><?php $lh->translateText("call_menu_ivr"); ?></option>
																	<option value="INGROUP" <?php if ($ivr_options->option_route[$i] == "INGROUP")echo "selected"; ?> ><?php $lh->translateText("in_group"); ?></option>
																	<option value="DID" <?php if ($ivr_options->option_route[$i] == "DID")echo "selected"; ?> ><?php $lh->translateText("did"); ?></option>
																	<option value="HANGUP" <?php if ($ivr_options->option_route[$i] == "HANGUP")echo "selected"; ?> ><?php $lh->translateText("hangup"); ?></option>
																	<option value="EXTENSION" <?php if ($ivr_options->option_route[$i] == "EXTENSION")echo "selected"; ?> ><?php $lh->translateText("custom_extension"); ?></option>
																	<option value="PHONE" <?php if ($ivr_options->option_route[$i] == "PHONE")echo "selected"; ?> ><?php $lh->translateText("phone"); ?></option>
																	<option value="VOICEMAIL" <?php if ($ivr_options->option_route[$i] == "VOICEMAIL")echo "selected"; ?> ><?php $lh->translateText("voicemail"); ?></option>
																	<option value="AGI" <?php if ($ivr_options->option_route[$i] == "AGI")echo "selected"; ?> ><?php $lh->translateText("agi"); ?></option>
																</select>
															</div>
															<div class="col-lg-1 btn-remove"></div>
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 option_menu_<?php echo $i;?> mb mt">
															<!-- CALL MENU -->
																<div class="route_callmenu_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "CALLMENU")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label"><?php $lh->translateText("call_menu"); ?>: </label>
																	<div class="col-sm-6">
																		<select class="select2-2 form-control select2" name="option_callmenu_value[]" style="width:100%;">
																			<option value="" selected> <?php $lh->translateText("-none-"); ?> </option>
																		<?php
																			$callmenu_option = '';
																			for($x=0;$x < count($ivr->menu_id);$x++) {
																				$callmenu_option .= '<option value="'.$ivr->menu_id[$x].'"';
																					if ($ivr_options->option_route_value[$i] == $ivr->menu_id[$x]) {$callmenu_option .= ' selected';}
																				$callmenu_option .= '>'.$ivr->menu_id[$x].' - '.$ivr->menu_name[$x].'</option>';
																			}
																			echo $callmenu_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- IN GROUP -->
																<div class="route_ingroup_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "INGROUP")echo 'style="display:none;"'; ?> >
																	<div class="row mb">
																		<label class="col-sm-3 control-label"><?php $lh->translateText("ingroups"); ?>: </label>
																		<div class="col-sm-6">
																			<select class="select2-2 form-control select2" name="option_ingroup_value[]" style="width:100%;">
																				<option value="" > <?php $lh->translateText("-none-"); ?> </option>
																			<?php
																				$ingroup_option = '';
																				for($x=0;$x < count($ingroups->group_id);$x++) {
																					$ingroup_option .= '<option value="'.$ingroups->group_id[$x].'"';
																						if ($ivr_options->option_route[$i] == "INGROUP" && $ivr_options->option_route_value[$i] == $ingroups->group_id[$x]) {$ingroup_option .= ' selected';}
																					$ingroup_option .= '>'.$ingroups->group_id[$x].' - '.$ingroups->group_name[$x].'</option>';
																				}
																				echo $ingroup_option;
																			?>
																			</select>
																		</div>
																	</div>
																	<?php 
																		if ($ivr_options->option_route[$i] == "INGROUP" || !isset($ivr_options->option_route[$i]) ) {
																			$explode_ingroup_context = explode(",", $ivr_options->option_route_value_context[$i]);
																	?>
																	<div class="col-sm-11">
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label"><?php $lh->translateText("handle_method"); ?>:</label>
																			<div class="col-sm-7">
																				<select class="form-control" name="handle_method_<?php echo $i;?>" id="edit_handle_method_<?php echo $i;?>">
																					<option value="CID" <?php if ($explode_ingroup_context[0] == "CID")echo "selected"; ?> >CID</option>
																					<option value="CIDLOOKUP" <?php if ($explode_ingroup_context[0] == "CIDLOOKUP")echo "selected"; ?> >CIDLOOKUP</option>
																					<option value="CIDLOOKUPRL" <?php if ($explode_ingroup_context[0] == "CIDLOOKUPRL")echo "selected"; ?> >CIDLOOKUPRL</option>
																					<option value="CIDLOOKUPRC" <?php if ($explode_ingroup_context[0] == "CIDLOOKUPRC")echo "selected"; ?> >CIDLOOKUPRC</option>
																					<option value="ANI" <?php if ($explode_ingroup_context[0] == "ANI")echo "selected"; ?> >ANI</option>
																					<option value="ANILOOKUP" <?php if ($explode_ingroup_context[0] == "ANILOOKUP")echo "selected"; ?> >ANILOOKUP</option>
																					<option value="ANILOOKUPRL" <?php if ($explode_ingroup_context[0] == "ANILOOKUPRL")echo "selected"; ?> >ANILOOKUPRL</option>
																					<option value="VIDPROMPT" <?php if ($explode_ingroup_context[0] == "VIDPROMPT")echo "selected"; ?> >VIDPROMPT</option>
																					<option value="VIDPROMPTLOOKUP" <?php if ($explode_ingroup_context[0] == "VIDPROMPTLOOKUP")echo "selected"; ?> >VIDPROMPTLOOKUP</option>
																					<option value="VIDPROMPTLOOKUPRL" <?php if ($explode_ingroup_context[0] == "VIDPROMPTLOOKUPRL")echo "selected"; ?> >VIDPROMPTLOOKUPRL</option>
																					<option value="VIDPROMPTLOOKUPRC" <?php if ($explode_ingroup_context[0] == "VIDPROMPTLOOKUPRC")echo "selected"; ?> >VIDPROMPTLOOKUPRC</option>
																					<option value="CLOSER" <?php if ($explode_ingroup_context[0] == "CLOSER")echo "selected"; ?> >CLOSER</option>
																					<option value="3DIGITID" <?php if ($explode_ingroup_context[0] == "3DIGITID")echo "selected"; ?> >3DIGITID</option>
																					<option value="4DIGITID" <?php if ($explode_ingroup_context[0] == "4DIGITID")echo "selected"; ?> >4DIGITID</option>
																					<option value="5DIGITID" <?php if ($explode_ingroup_context[0] == "5DIGITID")echo "selected"; ?> >5DIGITID</option>
																					<option value="10DIGITID" <?php if ($explode_ingroup_context[0] == "10DIGITID")echo "selected"; ?> >10DIGITID</option>
																				</select>
																			</div>
																		</div>
																		<div class="row mb">
																			<div class="col-sm-7">
																				<label class="col-sm-4 control-label"><?php $lh->translateText("campaign_id"); ?>: </label>
																				<div class="col-sm-8">
																					<select class="form-control select2" name="campaign_id_<?php echo $i;?>" style="width:100%;">
																					<?php
																						$campaign_id_ingroup = '<option value="">'.$lh->translationFor("-none-").'</option>';
																						for($x=0;$x < count($campaign->campaign_id);$x++) {
																							$campaign_id_ingroup .= '<option value="'.$campaign->campaign_id[$x].'"';
																								if ($explode_ingroup_context[3] == $campaign->campaign_id[$x]) {$campaign_id_ingroup .= ' selected';}
																							$campaign_id_ingroup .= '>'.$campaign->campaign_id[$x].' - '.$campaign->campaign_name[$x].'</option>';
																						}
																						echo $campaign_id_ingroup;
																					?>
																					</select>
																				</div>
																			</div>
																			<div class="col-sm-5 ingroup_advanced_settings_<?php echo $i;?>">
																				<label class="col-sm-5 control-label"><?php $lh->translateText("phone_code"); ?>: </label>
																				<div class="col-sm-7">
																					<input type="text" class="form-control" name="phone_code<?php echo $i;?>" value="<?php 
																						if (isset($ivr_options->option_route[$i])) 
																							echo $explode_ingroup_context[4];
																						else
																							echo 1;
																					?>" id="edit_phone_code_<?php echo $i;?>" maxlength="14" size="4">
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<div class="col-sm-7">
																				<label class="col-sm-4 control-label"><?php $lh->translateText("search_method"); ?>:</label>
																				<div class="col-sm-8">
																					<select class="form-control" name="search_method_<?php echo $i;?>" id="edit_search_method_<?php echo $i;?>">
																						<option value="LB" <?php if ($explode_ingroup_context[1] == "LB")echo "selected"; ?> ><?php $lh->translateText("lb_load_balance"); ?></option>
																						<option value="LO" <?php if ($explode_ingroup_context[1] == "LO")echo "selected"; ?> ><?php $lh->translateText("lo_load_balance_overflow"); ?></option>
																						<option value="SO" <?php if ($explode_ingroup_context[1] == "SO")echo "selected"; ?> ><?php $lh->translateText("server_only"); ?></option>
																					</select>
																				</div>
																			</div>
																			<div class="col-sm-5">
																				<label class="col-sm-5 control-label" for="search_method_list_id"><?php $lh->translateText("list_id"); ?>: </label>
																				<div class="col-sm-7">
																					<input type="text" name="list_id_<?php echo $i;?>" value="<?php 
																						if (isset($ivr_options->option_route[$i])) 
																							echo $explode_ingroup_context[2];
																						else
																							echo 998;
																					?>" id="edit_list_id_<?php echo $i;?>" class="form-control" maxlength="14" size="8">
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label"><?php $lh->translateText("vid_digits"); ?>: </label>
																			<div class="col-sm-7">
																				<input type="text" class="form-control" name="vid_digits_<?php echo $i;?>" value="<?php 
																					if (isset($ivr_options->option_route[$i]))
																						echo $explode_ingroup_context[8];
																					else
																						echo 1;
																				?>" id="edit_validate_digits_<?php echo $i;?>" maxlength="3" size="3">
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label"><?php $lh->translateText("vid_enter_Filename"); ?>: </label>
																			<div class="col-sm-8">
																				<div class="col-sm-6">
																					<input type="text" name="enter_filename_<?php echo $i;?>" value="<?php 
																						if (isset($ivr_options->option_route[$i]))
																							echo $explode_ingroup_context[5];
																						else
																							echo "sip-silence";
																					?>" id="edit_enter_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
																				</div>
																				<div class="col-sm-6">
																					<select class="col-sm-6 form-control select2" style="width:100%;" id="enter_filename_select_<?php echo $i;?>">
																						<option value="sip-silence" <?php if ($explode_ingroup_context[5] == "sip-silence")echo "selected"; ?> > <?php $lh->translateText("default_value") ?> </option>
																					<?php
																						$vid_enter = '';
																						for($x=0;$x<count($voicefiles->file_name);$x++) {
																							$file = substr($voicefiles->file_name[$x], 0, -4);
																							$vid_enter .= '<option value="'.$file.'"';
																								if ($file == $explode_ingroup_context[5])echo $vid_enter.= 'selected';
																							$vid_enter .= '>'.$file.'</option>';
																							echo $vid_enter;
																						}
																					?>
																					</select>
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label"><?php $lh->translateText("vid_id_number_filename"); ?>: </label>
																			<div class="col-sm-8">
																				<div class="col-sm-6">
																					<input type="text" name="id_number_filename_<?php echo $i;?>" value="<?php 
																						if (isset($ivr_options->option_route[$i]))
																							echo $explode_ingroup_context[6];
																						else
																							echo "sip-silence";
																					?>" id="edit_id_number_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
																				</div>
																				<div class="col-sm-6">
																					<select class="col-sm-6 form-control select2" style="width:100%;" id="edit_id_number_filename_select_<?php echo $i;?>">
																						<option value="sip-silence" <?php if ($explode_ingroup_context[6] == "sip-silence")echo "selected"; ?> > <?php $lh->translateText("default_value"); ?> </option>
																					<?php
																						$vid_id = '';
																						for($x=0;$x<count($voicefiles->file_name);$x++) {
																							$file = substr($voicefiles->file_name[$x], 0, -4);
																							$vid_id .= '<option value="'.$file.'"';
																								if ($file == $explode_ingroup_context[6])echo $vid_id.= 'selected';
																							$vid_id .= '>'.$file.'</option>';
																							echo $vid_id;
																						}
																					?>
																					</select>
																				</div>
																			</div>
																		</div>
																		<div class="row mb ingroup_advanced_settings_<?php echo $i;?>">
																			<label class="col-sm-3 control-label"><?php $lh->translateText("vid_confirm_filename"); ?>: </label>
																			<div class="col-sm-8">
																				<div class="col-sm-6">
																					<input type="text" name="confirm_filename_<?php echo $i;?>" value="<?php 
																						if (isset($ivr_options->option_route[$i]))
																							echo $explode_ingroup_context[7];
																						else
																							echo "sip-silence";
																					?>" id="edit_confirm_filename_<?php echo $i;?>" class="form-control" maxlength="255" size="25">
																				</div>
																				<div class="col-sm-6">
																					<select class="col-sm-6 form-control select2" style="width:100%;" id="edit_confirm_filename_select_<?php echo $i;?>">
																						<option value="sip-silence" <?php if ($explode_ingroup_context[7] == "sip-silence")echo "selected"; ?> > <?php $lh->translateText("default_value"); ?> </option>
																					<?php
																						$vid_confirm = '';
																						for($x=0;$x<count($voicefiles->file_name);$x++) {
																							$file = substr($voicefiles->file_name[$x], 0, -4);
																							$vid_confirm .= '<option value="'.$file.'"';
																								if ($file == $explode_ingroup_context[7])echo $vid_confirm.= 'selected';
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
																<div class="route_did_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "DID")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label"><?php $lh->translateText("did"); ?>: </label>
																	<div class="col-sm-6">
																		<select class="col-sm-6 select2-2 form-control select2" name="option_did_value[]" style="width:100%;">
																			<option value="" selected> <?php $lh->translateText("-none-"); ?> </option>
																		<?php
																			$did_option = '';
																			for($x=0;$x < count($phonenumber->did_pattern);$x++) {
																				$did_option .= '<option value="'.$phonenumber->did_pattern[$x].'"';
																					if ($ivr_options->option_route_value[$i] == $phonenumber->did_pattern[$x]) { $did_option .= ' selected';}
																				$did_option .= '>'.$phonenumber->did_pattern[$x].' - '.$phonenumber->did_description[$x].'</option>';
																			}
																			echo $did_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- HANGUP -->
																<div class="route_hangup_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "HANGUP")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label"><?php $lh->translateText("audio_file"); ?>: </label>
																	<div class="col-sm-6">
																		<select class="select2-2 form-control select2" name="option_hangup_value[]" style="width:100%;">
																			<option value="vm-goodbye"> <?php $lh->translateText("vm-goodbye"); ?> </option>
																		<?php
																			//if ($ivr_options->option_route_value[$i] == "vm-goodbye") {echo '<option value="vm-goodbye" selected> vm-goodbye </option>';}
																			$hangup_option = '';
																			for($x=0;$x<count($voicefiles->file_name);$x++) {
																				$file = substr($voicefiles->file_name[$x], 0, -4);
																				$hangup_option .= '<option value="'.$file.'"';
																					if ($ivr_options->option_route_value[$i] == $file) {$hangup_option .= 'selected';}
																				$hangup_option .= '>'.$file.'</option>';
																			}
																			echo $hangup_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- EXTENSION -->
																<div class="route_exten_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "EXTENSION")echo 'style="display:none;"'; ?> >
																	<div class="col-sm-6">
																		<label class="col-sm-3 control-label"><?php $lh->translateText("custom_extension"); ?>: </label>
																		<div class="col-sm-9">
																			<input type="text" class="form-control" name="option_extension_value[]" id="option_route_value_<?php echo $i;?>" value="<?php if ($ivr_options->option_route[$i] == "EXTENSION") {echo $ivr_options->option_route_value[$i];} ?>" />
																		</div>
																	</div>
																	<div class="col-sm-6">
																		<label class="col-sm-3 control-label"><?php $lh->translateText("context"); ?>: </label>
																		<div class="col-sm-9">
																			<input type="text" class="form-control" name="option_route_value_context[]" id="option_route_value_context_<?php echo $i;?>" value="<?php if ($ivr_options->option_route[$i] == "EXTENSION") {echo $ivr_options->option_route_value_context[$i];} ?>" />
																		</div>
																	</div>
																</div>
															<!-- PHONE -->
																<div class="route_phone_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "PHONE")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label"><?php $lh->translateText("phone"); ?>: </label>
																	<div class="col-sm-6">
																		<select class="select2-2 form-control select2" name="option_phone_value[]" style="width:100%;">
																			<option value="" > <?php $lh->translateText("-none-"); ?> </option>
																		<?php
																			$phones_option = '';
																			for($x=0;$x < count($phone_extension->extension);$x++) {
																				$phones_option .= '<option value="'.$phone_extension->extension[$x].'"';
																					if ($ivr_options->option_route_value[$i] == $phone_extension->extension[$x]) { $phones_option .= ' selected';}
																				$phones_option .= '>'.$phone_extension->extension[$x]." - ".$phone_extension->server_ip[$x]." - ".$phone_extension->dialplan_number[$x]."</option>";
																			}
																			echo $phones_option;
																		?>
																		</select>
																	</div>
																</div>
															<!-- VOICEMAIL -->
																<div class="route_voicemail_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "VOICEMAIL")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label"><?php $lh->translateText("voicemail_box"); ?>: </label>
																	<div class="col-sm-9">
																		<div class="col-sm-6">
																			<input type="text" name="option_voicemail_value[]" class="form-control" id="option_voicemail_input_<?php echo $i;?>" value="<?php if ($ivr_options->option_route[$i] == "VOICEMAIL") {echo $ivr_options->option_route_value[$i];} ?>" maxlength="255" size="15">
																		</div>
																		<div class="col-sm-6">
																			<select class="col-sm-6 select2 form-control" style="width:100%;" id="option_voicemail_select_<?php echo $i;?>">
																				<option value="" > <?php $lh->translateText("-none-"); ?> </option>
																			<?php
																				$voicemail_option = '';
																				for($x=0;$x < count($voicemails->voicemail_id);$x++) {
																					$voicemail_option .= '<option value="'.$voicemails->voicemail_id[$x].'"';
																						if ($ivr_options->option_route_value[$i] == $voicemails->voicemail_id[$x]) { $voicemail_option .= ' selected';}
																					$voicemail_option .= '>'.$voicemails->voicemail_id[$x].' - '.$voicemails->fullname[$x].'</option>';
																				}
																				echo $voicemail_option;
																			?>
																			</select>
																		</div>
																	</div>
																</div>
															<!-- AGI -->
																<div class="route_agi_<?php echo $i;?>" <?php if ($ivr_options->option_route[$i] != "AGI")echo 'style="display:none;"'; ?> >
																	<label class="col-sm-3 control-label"><?php $lh->translateText("agi"); ?>: </label>
																	<div class="col-sm-6">
																		<input type="text" class="form-control" name="option_agi_value[]" maxlength="255" size="50" value="<?php if ($ivr_options->option_route[$i] == "AGI") {echo $ivr_options->option_route_value[$i];} ?>">
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
														<a href="telephonyinbound.php" type="button"  id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?> </a>
														<button type="submit" class="btn btn-primary" id="modifyIVROkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>
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
					if ($did != NULL) {

				/*
				* APIs for getting lists for the some of the forms
				*/
				$users = $api->API_getAllUsers();
				$ingroups = $api->API_getAllInGroups();
				$voicemails = $api->API_getAllVoicemails();
				$phone_extension = $api->API_getAllPhones();
				$ivr = $api->API_getAllIVRs();
				$scripts = $api->API_getAllScripts();
				$voicefiles = $api->API_getAllVoiceFiles();
				$phones = $api->API_getAllPhones();	
				$output = $api->API_getDIDInfo($did);
				//var_dump($ingroups);
	
				if ($output->result=="success") {
				# Result was OK!
				?>
					
				<!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend><?php $lh->translateText("modify_did_record"); ?> : <u><?php echo $output->data->did_pattern;?></u></legend>
								
								<form id="modifydid">
									<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
									<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />

							<!-- Custom Tabs -->
							<div role="tabpanel">
							<!--<div class="nav-tabs-custom">-->
								<ul role="tablist" class="nav nav-tabs nav-justified">
									<li class="active"><a href="#tab_1" data-toggle="tab"><?php $lh->translateText("basic_settings"); ?> </a></li>
									<li><a href="#tab_2" data-toggle="tab"> <?php $lh->translateText("advance_settings"); ?></a></li>
								</ul>
				               <!-- Tab panes-->
				               <div class="tab-content">

					               	<!-- BASIC SETTINGS -->
					                <div id="tab_1" class="tab-pane fade in active">

										<input type="hidden" name="modify_did" value="<?php echo $output->data->did_id;?>">
									<fieldset>
										<div class="form-group mt">
											<label for="did_pattern" class="col-sm-2 control-label"><?php $lh->translateText("did_number"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="did_pattern" id="did_pattern" value="<?php echo $output->data->did_pattern;?>">
											</div>
										</div>
										<div class="form-group">
											<label for="desc" class="col-sm-2 control-label"><?php $lh->translateText("description"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="desc" id="desc" value="<?php echo $output->data->did_description;?>">
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("status"); ?></label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="status" id="status">
												<?php
													$status = NULL;
													if ($output->data->did_active == "Y") {
														$status .= '<option value="Y" selected> '.$lh->translationFor("active").' </option>';
													} else {
														$status .= '<option value="Y" > '.$lh->translationFor("active").' </option>';
													}
													
													if ($output->data->did_active == "N" || $output->data->did_active == NULL) {
														$status .= '<option value="N" selected> '.$lh->translationFor("inactive").' </option>';
													} else {
														$status .= '<option value="N" > '.$lh->translationFor("inactive").' </option>';
													}
													echo $status;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="route" class="col-sm-2 control-label"><?php $lh->translateText("did_route"); ?></label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="route" name="route">
													<?php
														$route = NULL;
														if ($output->data->did_route  == "AGENT") {
															$route .= '<option value="AGENT" selected> Agent </option>';
														} else {
															$route .= '<option value="AGENT" > Agent </option>';
														}
														
														if ($output->data->did_route  == "IN_GROUP") {
															$route .= '<option value="IN_GROUP" selected> In-group </option>';
														} else {
															$route .= '<option value="IN_GROUP" > In-group </option>';
														}
														
														if ($output->data->did_route  == "PHONE") {
															$route .= '<option value="PHONE" selected> Phone </option>';
														} else {
															$route .= '<option value="PHONE" > Phone </option>';
														}
														
														if ($output->data->did_route  == "CALLMENU") {
															$route .= '<option value="CALLMENU" selected> Call Menu / IVR </option>';
														} else {
															$route .= '<option value="CALLMENU" > Call Menu / IVR </option>';
														}
														
														if ($output->data->did_route  == "VOICEMAIL") {
															$route .= '<option value="VOICEMAIL" selected> Voicemail </option>';
														} else {
															$route .= '<option value="VOICEMAIL" > Voicemail </option>';
														}
														
														if ($output->data->did_route  == "EXTEN") {
															$route .= '<option value="EXTEN" selected> Custom Extension </option>';
														} else {
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
										<div id="form_route_agent" <?php if ($output->data->did_route  != "AGENT") { ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_agentid" class="col-sm-3 control-label"><?php $lh->translateText("agent_id"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_agentid" id="route_agentid" class="form-control select2" style="width:100%;">
														<option value="" > <?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($i=0;$i<count($users->user);$i++) {
														?>
															<option value="<?php echo $users->user[$i];?>" <?php if ($output->data->user == $users->user[$i]) echo "selected";?> >
																<?php echo $users->user[$i].' - '.$users->full_name[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="route_unavail" class="col-sm-3 control-label"><?php $lh->translateText("agent_unavailable_action"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_unavail" id="route_unavail" class="form-control">
														<option value="VOICEMAIL"  <?php if ($output->data->user_unavailable_action == "VOICEMAIL") echo "selected";?> > Voicemail </option>
														<option value="PHONE"  <?php if ($output->data->user_unavailable_action == "PHONE") echo "selected";?> > Phone </option>
														<option value="IN_GROUP"  <?php if ($output->data->user_unavailable_action == "IN_GROUP") echo "selected";?> > In-group </option>
														<option value="EXTEN"  <?php if ($output->data->user_unavailable_action == "EXTEN") echo "selected";?> > Custom Extension </option>
													</select>
												</div>
											</div>
												<!-- FOR AGENT UNAVAILABLE ACTION -->
												<!--IF route_unavail = EXTEN -->
													<div class="form-group" id="ru_exten" <?php if ($output->data->user_unavailable_action  != "EXTEN") { ?> style="display: none;" <?php }?>>
															<div class="form-group">
                                                                                                <label for="ru_exten" class="col-sm-3 control-label"><?php $lh->translateText("custom_extension"); ?>: </label>
                                                                                                <div class="col-sm-9 mb">
                                                                                                        <input type="text" name="ru_exten" id="route_exten" placeholder="Extension" class="form-control" value="<?php echo $output->data->extension; ?>" required>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                                <label for="ru_exten_context" class="col-sm-3 control-label"><?php $lh->translateText("extension_context"); ?>: </label>
                                                                                                <div class="col-sm-9 mb">
                                                                                                        <input type="text" name="ru_exten_context" id="ru_exten_context" placeholder="Extension Context" class="form-control" value="<?php echo $output->data->exten_context;?>" required>
                                                                                                </div>
                                                                                        </div>
															<!--<input type="text" class="form-control" name="ru_exten" id="ru_exten" value="<?php //echo $output->data->did_pattern;?>">
														</div>-->
													</div>
												<!--IF route_unavail = INGROUP -->
													<div class="form-group" id="ru_ingroup" <?php if ($output->data->user_unavailable_action  != "IN_GROUP") { ?> style="display: none;" <?php }?>>
														<label for="ru_ingroup" class="col-sm-3 control-label">Ingroup</label>
														<div class="col-sm-9 mb">
															<select name="ru_ingroup" id="ru_ingroup" class="form-control">
																<?php
																	for($i=0;$i<count($ingroups->group_id);$i++) {
																?>
																	<option value="<?php echo $ingroups->group_id[$i];?>" <?php if ($ingroups->group_id[$i] == $output->data->group_id)echo "selected";?>>
																		<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
																	</option>									
																<?php
																	}
																?>
															</select>
														</div>
													</div>
												<!--IF route_unavail = PHONE -->
													<div class="form-group" id="ru_phone" <?php if ($output->data->user_unavailable_action  != "PHONE") { ?> style="display: none;" <?php }?>>
														<label for="exten" class="col-sm-3 control-label">Phone</label>
														<div class="col-sm-9 mb">
															<select name="ru_phone" id="ru_phone" class="form-control">
																<?php
																	for($i=0;$i<count($phones->extension);$i++) {
																?>
																	<option value="<?php echo $phones->extension[$i];?>" <?php if ($phones->extension[$i] == $output->data->phone)echo "selected";?>>
																		<?php echo $phones->extension[$i].' - '.$phones->server_ip[$i].' - '.$phones->dialplan_number[$i];?>
																	</option>									
																<?php
																	}
																?>
															</select>
														</div>
													</div>
												<!--IF route_unavail = VOICEMAIL -->
													<div class="form-group" id="ru_voicemail" <?php if ($output->data->user_unavailable_action  != "VOICEMAIL") { ?> style="display: none;" <?php }?>>
														<label for="exten" class="col-sm-3 control-label">Voicemail</label>
														<div class="col-sm-9 mb">
															<select name="ru_voicemail" id="voicemail_ext" class="form-control">
                                                                                                                                 <?php
                                                                                            			                 for($i=0;$i<count($voicemails->voicemail_id);$i++) {
                                                                                                                		 ?>
                                                                                                                        		<option value="<?php echo $voicemails->voicemail_id[$i];?>" <?php if ($voicemails->voicemail_id[$i] == $output->data->voicemail_ext)echo "selected";?>>
                                                                                                                                <?php echo $voicemails->voicemail_id[$i].' - '.$voicemails->fullname[$i];?>
                                                                                                                        </option>                                                                    
                                                                                                                <?php
                                                                                                                        }
                                                                                                                ?>

                                                                                                                        </select>
															<!-- <input type="text" class="form-control" name="exten" id="exten" value="<?php echo $output->data->did_pattern;?>"> -->
														</div>
													</div>
											<div class="form-group">
												<label for="user_route_settings_ingroup" class="col-sm-3 control-label"><?php $lh->translateText("agent_route_settings"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="user_route_settings_ingroup" id="user_route_settings_ingroup" class="form-control">
														<!--<option value="AGENTDIRECT"><?php $lh->translateText("AGENTDIRECT"); ?></option>-->
													<?php
														
														for($i=0;$i<count($ingroups->group_id);$i++) {
															//if ($ingroups->group_id[$i] != "AGENTDIRECT") {
													?>
														<option value="<?php echo $ingroups->group_id[$i];?>" <?php if ($output->data->user_route_settings_ingroup == $ingroups->group_id[$i]) echo "selected";?> >
															<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
														</option>
													<?php
															//}
														}
													?>
													</select>
												</div>
											</div>
										</div><!-- end of div agent-->
										
									<!-- IF DID ROUTE = IN-GROUP-->
										<div id="form_route_ingroup" class="form-group" <?php if ($output->data->did_route  != "IN_GROUP") { ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_ingroupid" class="col-sm-3 control-label"><?php $lh->translateText("ingroup_id"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_ingroupid" id="route_ingroupid" class="form-control">
														<?php
															for($i=0;$i<count($ingroups->group_id);$i++) {
														?>
															<option value="<?php echo $ingroups->group_id[$i];?>" <?php if ($ingroups->group_id[$i] == $output->data->group_id)echo "selected";?>>
																 <?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
															</option>				
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="route_ingroup_listid" class="col-sm-3 control-label"><?php $lh->translateText("ingroup_id_list_id"); ?>: </label>
												<div class="col-sm-9 mb">
													<input type="text" class="form-control" name="list_id" id="route_ingroup_listid" value="<?php echo $output->data->list_id;?>">
												</div>
											</div>
										</div>
									<!-- end of ingroup div -->
										
									<!-- IF DID ROUTE = PHONE -->
										<div id="form_route_phone" <?php if ($output->data->did_route  != "PHONE") { ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label  for="route_phone_exten" class="col-sm-3 control-label"><?php $lh->translateText("phone_extension"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_exten" id="route_phone_exten" class="form-control">
														<?php
															for($i=0;$i<count($phone_extension->extension);$i++) {
														?>
															<option value="<?php echo $phone_extension->extension[$i];?>" <?php if ($phone_extension->extension[$i] == $output->data->phone)echo "selected";?>>
																<?php echo $phone_extension->extension[$i].' - '.$phone_extension->server_ip[$i].' - '.$phone_extension->dialplan_number[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div><!-- end of phone div -->
										
									<!-- IF DID ROUTE = IVR -->
										<div id="form_route_callmenu" <?php if ($output->data->did_route  != "CALLMENU") { ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_ivr" class="col-sm-3 control-label"><?php $lh->translateText("call_menu"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_ivr" id="route_ivr" class="form-control">
														<?php
															for($i=0;$i<count($ivr->menu_id);$i++) {
														?>
															<option value="<?php echo $ivr->menu_id[$i];?>" <?php if ($ivr->menu_id[$i] == $output->data->menu_id)echo "selected";?>>
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
										<div id="form_route_voicemail" <?php if ($output->data->did_route  != "VOICEMAIL") { ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_voicemail" class="col-sm-3 control-label"><?php $lh->translateText("voicemail_box"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_voicemail" id="route_voicemail" class="form-control">
														<?php
															for($i=0;$i<count($voicemails->voicemail_id);$i++) {
														?>
															<option value="<?php echo $voicemails->voicemail_id[$i];?>" <?php if ($voicemails->voicemail_id[$i] == $output->data->voicemail_ext)echo "selected";?>>
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
										<div id="form_route_exten" <?php if ($output->data->did_route  != "EXTEN") { ?> style="display: none;" <?php }?> >
											<div class="form-group">
												<label for="route_exten" class="col-sm-3 control-label"><?php $lh->translateText("custom_extension"); ?>: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" value="<?php echo $output->data->extension; ?>" required>
												</div>
											</div>
											<div class="form-group">
												<label for="route_exten_context" class="col-sm-3 control-label"><?php $lh->translateText("extension_context"); ?>: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" value="<?php echo $output->data->exten_context;?>" required>
												</div>
											</div>
										</div><!-- end of custom extension div -->
									</fieldset>

									</div><!-- end of basic settings-->
								

						       		<!-- ADVANCED SETTINGS -->
						       		<div id="tab_2" class="tab-pane fade in">
						       			<fieldset>
							       			<div class="form-group mt">
							       				<label for="cid_num" class="col-sm-3 control-label"><?php $lh->translateText("clean_cid_number"); ?></label>
							       				<div class="col-sm-9 mb">
													<input type="text" class="form-control" name="cid_num" id="cid_num" value="<?php echo $output->data->filter_clean_cid_number;?>" maxlength="20">
												</div>
							       			</div>
											
											<div class="form-group">
												<label for="route_phone_server" class="col-sm-3 control-label"><?php $lh->translateText("server_ip"); ?>: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_server" id="route_phone_server" class="form-control">
														<option value="" > <?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($i=0;$i < 1;$i++) {
														?>
															<option value="<?php echo $phone_extension->server_ip[$i];?>" <?php if ($phone_extension->server_ip[$i] == $output->data->server_ip)echo "selected";?>>
																<?php echo 'GOautodial - '.$phone_extension->server_ip[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
											
											<div class="form-group">
												<label for="call_handle_method" class="col-sm-3 control-label"><?php $lh->translateText("call_handle_method"); ?>: </label>
												<div class="col-sm-9 mb">
													<select size="1" name="call_handle_method" id="call_handle_method" class="form-control">
														<option value="CID" <?php if ($output->data->call_handle_method == "CID") echo "selected"; ?>>CID</option>
														<option value="CIDLOOKUP" <?php if ($output->data->call_handle_method == "CIDLOOKUP") echo "selected"; ?>>CIDLOOKUP</option>
														<option value="CIDLOOKUPRL" <?php if ($output->data->call_handle_method == "CIDLOOKUPRL") echo "selected"; ?>>CIDLOOKUPRL</option>
														<option value="CIDLOOKUPRC" <?php if ($output->data->call_handle_method == "CIDLOOKUPRC") echo "selected"; ?>>CIDLOOKUPRC</option>
														<option value="CIDLOOKUPALT" <?php if ($output->data->call_handle_method == "CIDLOOKUPALT") echo "selected"; ?>>CIDLOOKUPALT</option>
														<option value="CIDLOOKUPRLALT" <?php if ($output->data->call_handle_method == "CIDLOOKUPRLALT") echo "selected"; ?>>CIDLOOKUPRLALT</option>
														<option value="CIDLOOKUPRCALT" <?php if ($output->data->call_handle_method == "CIDLOOKUPRCALT") echo "selected"; ?>>CIDLOOKUPRCALT</option>
														<option value="CIDLOOKUPADDR3" <?php if ($output->data->call_handle_method == "CIDLOOKUPADDR3") echo "selected"; ?>>CIDLOOKUPADDR3</option>
														<option value="CIDLOOKUPRLADDR3" <?php if ($output->data->call_handle_method == "CIDLOOKUPRLADDR3") echo "selected"; ?>>CIDLOOKUPRLADDR3</option>
														<option value="CIDLOOKUPRCADDR3" <?php if ($output->data->call_handle_method == "CIDLOOKUPRCADDR3") echo "selected"; ?>>CIDLOOKUPRCADDR3</option>
														<option value="CIDLOOKUPALTADDR3" <?php if ($output->data->call_handle_method == "CIDLOOKUPALTADDR3") echo "selected"; ?>>CIDLOOKUPALTADDR3</option>
														<option value="CIDLOOKUPRLALTADDR3" <?php if ($output->data->call_handle_method == "CIDLOOKUPRLALTADDR3") echo "selected"; ?>>CIDLOOKUPRLALTADDR3</option>
														<option value="CIDLOOKUPRCALTADDR3" <?php if ($output->data->call_handle_method == "CIDLOOKUPRCALTADDR3") echo "selected"; ?>>CIDLOOKUPRCALTADDR3</option>
														<option value="ANI" <?php if ($output->data->call_handle_method == "ANI") echo "selected"; ?>>ANI</option>
														<option value="ANILOOKUP" <?php if ($output->data->call_handle_method == "ANILOOKUP") echo "selected"; ?>>ANILOOKUP</option>
														<option value="ANILOOKUPRL" <?php if ($output->data->call_handle_method == "ANILOOKUPRL") echo "selected"; ?>>ANILOOKUPRL</option>
														<option value="VIDPROMPT" <?php if ($output->data->call_handle_method == "VIDPROMPT") echo "selected"; ?>>VIDPROMPT</option>
														<option value="VIDPROMPTLOOKUP" <?php if ($output->data->call_handle_method == "VIDPROMPTLOOKUP") echo "selected"; ?>>VIDPROMPTLOOKUP</option>
														<option value="VIDPROMPTLOOKUPRL" <?php if ($output->data->call_handle_method == "VIDPROMPTLOOKUPRL") echo "selected"; ?>>VIDPROMPTLOOKUPRL</option>
														<option value="VIDPROMPTLOOKUPRC" <?php if ($output->data->call_handle_method == "VIDPROMPTLOOKUPRC") echo "selected"; ?>>VIDPROMPTLOOKUPRC</option>
														<option value="CLOSER" <?php if ($output->data->call_handle_method == "CLOSER") echo "selected"; ?>>CLOSER</option>
														<option value="3DIGITID" <?php if ($output->data->call_handle_method == "3DIGITID") echo "selected"; ?>>3DIGITID</option>
														<option value="4DIGITID" <?php if ($output->data->call_handle_method == "4DIGITID") echo "selected"; ?>>4DIGITID</option>
														<option value="5DIGITID" <?php if ($output->data->call_handle_method == "5DIGITID") echo "selected"; ?>>5DIGITID</option>
														<option value="10DIGITID" <?php if ($output->data->call_handle_method == "10DIGITID") echo "selected"; ?>>10DIGITID</option>
													</select>
												</div>
											</div>
											
											<div class="form-group">
												<label for="agent_search_method" class="col-sm-3 control-label"><?php $lh->translateText("agent_search_method"); ?>: </label>
												<div class="col-sm-9 mb">
													<select size="1" name="agent_search_method" id="agent_search_method" class="form-control">
														<option value="LB" <?php if ($output->data->agent_search_method == "LB") echo "selected"; ?>>LB - Load Balanced</option>
														<option value="LO" <?php if ($output->data->agent_search_method == "LO") echo "selected"; ?>>LO - Load Balanced Overflow</option>
														<option value="SO" <?php if ($output->data->agent_search_method == "SO") echo "selected"; ?>>SO - Server Only</option>
													</select>
												</div>
											</div>
							       		</fieldset>				       			
						       		</div>
							
								<!-- FOOTER BUTTONS -->
								   	<div id="modifyDIDresult"></div>

				                    <fieldset class="footer-buttons">
				                        <div class="box-footer">
				                           <div class="col-sm-3 pull-right">
													<a href="telephonyinbound.php" type="button"  id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?></a>
				                           	
				                                	<button type="submit" class="btn btn-primary" id="modifyDIDOkButton" data-id="<?php echo $groupid;?>" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>
												
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

		<script type="text/javascript">
			$(document).ready(function() {

			//Initialize Select2 Elements
                $('.select2').select2({ theme: 'bootstrap' });
                $.fn.select2.defaults.set( "theme", "bootstrap" );
                
			// init datatables

				$('#agent_rank_table').DataTable({
					destroy:true, 
					responsive:true,
					stateSave:true,
					drawCallback:function(settings) {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					},
					columnDefs:[
						{ width: "15%", targets: [ 3, 4 ] },
						{ width: "10%", targets: 2 },
						{ searchable: false, targets: [  2, 3, 4 ] }
						//{ sortable: false, targets: [  0, 1 ] },
						//{ targets: -1, className: "dt-body-middle" }
					]
				});				
			// for cancelling
				$(document).on('click', '#cancel', function() {
					swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
				});

			//Colorpicker
    			$(".colorpicker").colorpicker({
					format: 'hex'
				});

				$(document).on("change","#route_unavail",function() {
					//  alert( this.value ); // or $(this).val()
					if (this.value == "EXTEN") {
					  $('#ru_exten').show();
					  
					  $('#ru_phone').hide();
					  $('#ru_ingroup').hide();
					  $('#ru_voicemail').hide();
					}if (this.value == "IN_GROUP") {
					  $('#ru_ingroup').show();
					  
					  $('#ru_exten').hide();
					  $('#ru_phone').hide();
					  $('#ru_voicemail').hide();
					}if (this.value == "PHONE") {
					  $('#ru_phone').show();
					  
					  $('#ru_exten').hide();
					  $('#ru_ingroup').hide();
					  $('#ru_voicemail').hide();
					}if (this.value == "VOICEMAIL") {
					  $('#ru_voicemail').show();
					  
					  $('#ru_exten').hide();
					  $('#ru_ingroup').hide();
					  $('#ru_phone').hide();
					}
					
				});
				
				$(document).on("change","#route",function() {
					//  alert( this.value ); // or $(this).val()
					if (this.value == "AGENT") {
					  $('#form_route_agent').show();
					  
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if (this.value == "IN_GROUP") {
					  $('#form_route_ingroup').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if (this.value == "PHONE") {
					  $('#form_route_phone').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if (this.value == "CALLMENU") {
					  $('#form_route_callmenu').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_voicemail').hide();
					  $('#form_route_exten').hide();
					}if (this.value == "VOICEMAIL") {
					  $('#form_route_voicemail').show();
					  
					  $('#form_route_agent').hide();
					  $('#form_route_ingroup').hide();
					  $('#form_route_phone').hide();
					  $('#form_route_callmenu').hide();
					  $('#form_route_exten').hide();
					}if (this.value == "EXTEN") {
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
					if (this.value == "HANGUP") {
					  $('.drop_action_exten').hide();
					}

					if (this.value == "MESSAGE") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_message').show();

					  $('.drop_exten_callmenu').hide();
					  $('.drop_exten_ingroup').hide();
					  $('.drop_exten_voicemail').hide();
					}

					if (this.value == "VOICEMAIL") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_voicemail').show();

					  $('.drop_exten_callmenu').hide();
					  $('.drop_exten_ingroup').hide();
					  $('.drop_exten_message').hide();
					}

					if (this.value == "IN_GROUP") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_ingroup').show();

					  $('.drop_exten_callmenu').hide();
					  $('.drop_exten_voicemail').hide();
					  $('.drop_exten_message').hide();
					}

					if (this.value == "CALLMENU") {
					  $('.drop_action_exten').show();
					  $('.drop_exten_callmenu').show();

					  $('.drop_exten_ingroup').hide();
					  $('.drop_exten_voicemail').hide();
					  $('.drop_exten_message').hide();
					}
					
				});

			//no_agent_action
				$(document).on("change","#no_agent_action",function() {
					if (this.value == "HANGUP") {
					  $('.no_agents_exten').hide();
					}

					if (this.value == "DID") {
					  $('.no_agents_exten').show();
					  $('.no_agents_did').show();

					  $('.no_agents_message').hide();
					  $('.no_agents_callmenu').hide();
					  $('.no_agents_ingroup').hide();
					  $('.no_agents_voicemail').hide();
					  $('.no_agents_extension').hide();
					}
					
					if (this.value == "MESSAGE") {
					  $('.no_agents_exten').show();
					  $('.no_agents_message').show();

					  $('.no_agents_callmenu').hide();
					  $('.no_agents_ingroup').hide();
					  $('.no_agents_voicemail').hide();
					  $('.no_agents_did').hide();
					  $('.no_agents_extension').hide();
					}

					if (this.value == "VOICEMAIL") {
					  $('.no_agents_exten').show();
					  $('.no_agents_voicemail').show();

					  $('.no_agents_callmenu').hide();
					  $('.no_agents_ingroup').hide();
					  $('.no_agents_message').hide();
					  $('.no_agents_did').hide();
					  $('.no_agents_extension').hide();
					  $('.show_no_agents_exten').txt("VM Chooser");
					}

					if (this.value == "INGROUP") {
					  $('.no_agents_exten').show();
					  $('.no_agents_ingroup').show();

					  $('.no_agents_callmenu').hide();
					  $('.no_agents_voicemail').hide();
					  $('.no_agents_message').hide();
					  $('.no_agents_did').hide();
					  $('.no_agents_extension').hide();
					  $('.show_no_agents_exten').txt("Ingroup Chooser");
					}

					if (this.value == "CALLMENU") {
					  $('.no_agents_exten').show();
					  $('.no_agents_callmenu').show();

					  $('.no_agents_ingroup').hide();
					  $('.no_agents_voicemail').hide();
					  $('.no_agents_message').hide();
					  $('.no_agents_did').hide();
					  $('.no_agents_extension').hide();
					  $('.show_no_agents_exten').txt("IVR Chooser");
					}
					
					if (this.value == "EXTENSION") {
                                          $('.no_agents_exten').show();
                                          $('.no_agents_extension').show();

                                          $('.no_agents_ingroup').hide();
                                          $('.no_agents_voicemail').hide();
                                          $('.no_agents_message').hide();
                                          $('.no_agents_did').hide();
					  $('.no_agents_callmenu').hide();
                                        }
		
				});
				
			//no_agent_action
				$(document).on("change","#after_hours_action",function() {
					if (this.value == "VOICEMAIL") {
						$('.after_hours').hide();
						$('.after_hours_voicemail').show();
					}else if (this.value == "MESSAGE") {
						$('.after_hours').hide();
						$('.after_hours_message_filename').show();
					}else if (this.value == "EXTENSION") {
						$('.after_hours').hide();
						$('.after_hours_exten').show();
					}else if (this.value == "CALLMENU") {
						$('.after_hours').hide();
						$('.after_hours_callmenu').show();
					} else {
						$('.after_hours').hide();
					}
					
				});
				
			// on tab change hide footer
				$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				  var target = $(e.target).attr("href"); // activated tab
				  if (target == "#agents") {
				  	$('#not_agent_rank').hide();
				  } else {
				  	$('#not_agent_rank').show();
				  }
				});

			/****** 
			** MODIFY Functions 
		 	******/

				//an ingroup
				$('#modifyInboundOkButton').click(function() {
					$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#modifyInboundOkButton').prop("disabled", true);

					$.ajax({
                        url: "./php/ModifyTelephonyInbound.php",
                        type: 'POST',
                        data: $("#modifyingroup").serialize(),
                        success: function(data) {
                          //if message is sent
							//console.log(data);
							//console.log($("#modifyingroup").serialize());
							$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
							$('#modifyInboundOkButton').prop("disabled", false);
							if (data == 1) {
								swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("inbound_update_success"); ?>",type: "success"},function() {window.location.href = 'telephonyinbound.php';});
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>","<?php $lh->translateText("something_went_wrong"); ?>" + data, "error");
							}
                        }
                    });	
                    return false;
				});
				
				// agent rank form submit
				$('#submit_agent_rank').click(function() {
					var groupID = $(this).attr('data-id');
					var itemdatas = $('#agentrankform').serialize();
					
	                $('input:checkbox[id^="CHECK"]').each(function() {
                        if (!this.checked) {
                                itemdatas += '&'+this.name+'=NO';
                        }
	                });
					
					console.log(itemdatas);
					
					$.ajax({
						url: "php/ModifyAgentRank.php",
						type: 'POST',
						data: {	itemrank: itemdatas, idgroup: groupID },
						success: function(data) {
						$('#submit_agent_rank').html("<i class='fa fa-check'></i> Submit");
						$('#submit_agent_rank').prop("disabled", false);
							console.log(data);
							if (data == 1) {
								swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("agent_rank_update_success"); ?>",type: "success"},function() {window.location.href = 'telephonyinbound.php';});

							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
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
						function(data) {
							//if message is sent
							console.log(data);
							console.log($("#modifyivr").serialize());							
							if (data == 1) {
								swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("ivr_update_success"); ?>",type: "success"},function() {window.location.href = 'telephonyinbound.php';});
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>","<?php $lh->translateText("something_went_wrong"); ?>" + data, "error");
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
						$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
						$('#modifyDIDOkButton').prop("disabled", true);
						
						$.post("./php/ModifyTelephonyInbound.php", //post
						$("#modifydid").serialize(), 
							function(data) {
								//if message is sent
								console.log(data);
								if (data == 1) {
									swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("did_update_success"); ?>",type: "success"},function() {window.location.href = 'telephonyinbound.php';});
									
								} else {
									sweetAlert("<?php $lh->translateText("oups"); ?>","<?php $lh->translateText("something_went_wrong"); ?>" + data, "error");
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyDIDOkButton').prop("disabled", false);	
								}
								//
							});
						return false; //don't let the form refresh the page...
						}					
					});

				$('.add-option').click(function() {
					var toClone = $('.to-clone-opt').clone();

					toClone.removeClass('to-clone-opt');
					toClone.find('label.control-label').text('');
					toClone.find('.btn-remove').append('<span class="fa fa-remove fa-2x text-red remove-row"></span>');

					$('.cloning-area').append(toClone);
				});

				$(document).on('click', '.remove-row', function() {
					var row = $(this).parent().parent();
					
					row.remove();
				});
			
			// INGROUP
				$('.select_welcome_message_filename').hide();
					$('.show_welcome_message_filename').on('click', function(event) {
						 $('.select_welcome_message_filename').toggle('show');
					});
					$(document).on('change', '#select_welcome_message_filename',function() {
						var val = $(this).val();
						$('#welcome_message_filename').val(val);
						$('.select_welcome_message_filename').toggle('hide');
					});
				$('.select_moh_context').hide();
					$('.show_moh_context').on('click', function(event) {
						 $('.select_moh_context').toggle('show');
					});
					$(document).on('change', '#select_moh_context',function() {
						var val = $(this).val();
						$('#moh_context').val(val);
						$('.select_moh_context').toggle('hide');
					});
				$('.select_onhold_prompt_filename').hide();
					$('.show_onhold_prompt_filename').on('click', function(event) {
						 $('.select_onhold_prompt_filename').toggle('show');
					});
					$(document).on('change', '#select_onhold_prompt_filename',function() {
						var val = $(this).val();
						$('#onhold_prompt_filename').val(val);
						$('.select_onhold_prompt_filename').toggle('hide');
					});
				$('.select_after_hours_message_filename').hide();
					$('.show_after_hours_message_filename').on('click', function(event) {
						 $('.select_after_hours_message_filename').toggle('show');
					});
					$(document).on('change', '#select_after_hours_message_filename',function() {
						var val = $(this).val();
						$('#after_hours_message_filename').val(val);
						$('.select_after_hours_message_filename').toggle('hide');
					});
				$('.select_after_hours_voicemail').hide();
					$('.show_after_hours_voicemail').on('click', function(event) {
						 $('.select_after_hours_voicemail').toggle('show');
					});
					$(document).on('change', '#select_after_hours_voicemail',function() {
						var val = $(this).val();
						$('#after_hours_voicemail').val(val);
						$('.select_after_hours_voicemail').toggle('hide');
					});
				$('.select_no_agents_did').hide();
					$('.show_no_agents_did').on('click', function(event) {
						 $('.select_no_agents_did').toggle('show');
					});
					$(document).on('change', '#select_no_agents_did',function() {
						var val = $(this).val();
						$('#no_agents_did').val(val);
						$('.select_no_agents_did').toggle('hide');
					});				
				$('.select_no_agents_exten').hide();
					$('.show_no_agents_exten').on('click', function(event) {
						 $('.select_no_agents_exten').toggle('show');
					});
					$(document).on('change', '#select_no_agents_exten',function() {
						var val = $(this).val();
						$('#no_agents_exten').val(val);
						$('.select_no_agents_exten').toggle('hide');
					});
				$('.select_no_agents_voicemail').hide();
					$('.show_no_agents_voicemail').on('click', function(event) {
						 $('.select_no_agents_voicemail').toggle('show');
					});
					$(document).on('change', '#select_no_agents_voicemail',function() {
						var val = $(this).val();
						$('#no_agents_voicemail').val(val);
						$('.select_no_agents_voicemail').toggle('hide');
					});
				$('.select_no_agents_ingroup').hide();
					$('.show_no_agents_ingroup').on('click', function(event) {
						 $('.select_no_agents_ingroup').toggle('show');
					});
					$(document).on('change', '#select_no_agents_ingroup',function() {
						var val = $(this).val();
						$('#no_agents_ingroup').val(val);
						$('.select_no_agents_ingroup').toggle('hide');
					});
				$('.select_no_agents_callmenu').hide();
					$('.show_no_agents_callmenu').on('click', function() {
						 $('.select_no_agents_callmenu').toggle('show');
					});
					$(document).on('change', '#select_no_agents_callmenu',function() {
						var val = $(this).val();
						$('#no_agents_callmenu').val(val);
						$('.select_no_agents_callmenu').toggle('hide');
					});
				$('.select_after_hours_callmenu').hide();
					$('.show_after_hours_callmenu').on('click', function() {
						 $('.select_after_hours_callmenu').toggle('show');
					});
					$(document).on('change', '#select_after_hours_callmenu',function() {
						var val = $(this).val();
						$('#after_hours_callmenu').val(val);
						$('.select_after_hours_callmenu').toggle('hide');
					});
			// IVR
				$(document).on('change', '.route_option',function() {
					//alert(this.value);
					var id = this.value;
					var old = $(this).attr('id');
					var object;
					if (typeof old != 'undefined') {
						$(this).attr('id', "option_"+id).attr('data-old', "option_"+old);
						
						object = "option_"+id;
					} else {
						$(this).attr('id', 'option_'+id);
						old = "option_";
					}
					
					showhide_option(object, id, old);
					
				});
				
				function showhide_option(object, id, old) {
					//var getId = object.attr('id');
					var lastChar;
					var old_lastChar;
					
					if (typeof object != 'undefined')
						lastChar = object[object.length -1];
					
					if (typeof old != 'undefined')
						old_lastChar = old[old.length -1];
					
					if (old_lastChar != "_") {
						$(".route_option option[value="+old_lastChar+"]").attr("disabled", false).css({"background-color": "white", "color": "#3a3f51"});
					} else {
						$(".route_option option[value="+id+"]").attr("disabled", true).css({"background-color": "#c1c1c1", "color": "white"});
					}
					
				}
				
				<?php for($i=0;$i < 14; $i++) { ?>
				$(document).on('change', '.route_menu_<?php echo $i;?>',function() {
					if (this.value == "CALLMENU") {
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
					
					}if (this.value == "INGROUP") {
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
						
					}if (this.value == "DID") {
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
						
					}if (this.value == "HANGUP") {
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
						
					}if (this.value == "EXTENSION") {
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
						
					}if (this.value == "PHONE") {
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
						
					}if (this.value == "VOICEMAIL") {
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
						
					}if (this.value == "AGI") {
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
					if (this.value == "") {
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
				$(document).on('change', '.route_menu_A',function() {
					if (this.value == "CALLMENU") {
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
					
					}if (this.value == "INGROUP") {
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
						
					}if (this.value == "DID") {
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
						
					}if (this.value == "HANGUP") {
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
						
					}if (this.value == "EXTENSION") {
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
						
					}if (this.value == "PHONE") {
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
						
					}if (this.value == "VOICEMAIL") {
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
						
					}if (this.value == "AGI") {
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
					if (this.value == "") {
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
				$(document).on('change', '.route_menu_B',function() {
					if (this.value == "CALLMENU") {
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
					
					}if (this.value == "INGROUP") {
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
						
					}if (this.value == "DID") {
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
						
					}if (this.value == "HANGUP") {
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
						
					}if (this.value == "EXTENSION") {
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
						
					}if (this.value == "PHONE") {
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
						
					}if (this.value == "VOICEMAIL") {
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
						
					}if (this.value == "AGI") {
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
					if (this.value == "") {
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
				$(document).on('change', '.route_menu_C',function() {
					if (this.value == "CALLMENU") {
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
					
					}if (this.value == "INGROUP") {
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
						
					}if (this.value == "DID") {
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
						
					}if (this.value == "HANGUP") {
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
						
					}if (this.value == "EXTENSION") {
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
						
					}if (this.value == "PHONE") {
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
						
					}if (this.value == "VOICEMAIL") {
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
						
					}if (this.value == "AGI") {
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
					if (this.value == "") {
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
				$(document).on('change', '.route_menu_D',function() {
					if (this.value == "CALLMENU") {
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
					
					}if (this.value == "INGROUP") {
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
						
					}if (this.value == "DID") {
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
						
					}if (this.value == "HANGUP") {
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
						
					}if (this.value == "EXTENSION") {
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
						
					}if (this.value == "PHONE") {
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
						
					}if (this.value == "VOICEMAIL") {
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
						
					}if (this.value == "AGI") {
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
					if (this.value == "") {
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
				$(document).on('change', '.route_menu_E',function() {
					if (this.value == "CALLMENU") {
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
					
					}if (this.value == "INGROUP") {
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
						
					}if (this.value == "DID") {
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
						
					}if (this.value == "HANGUP") {
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
						
					}if (this.value == "EXTENSION") {
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
						
					}if (this.value == "PHONE") {
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
						
					}if (this.value == "VOICEMAIL") {
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
						
					}if (this.value == "AGI") {
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
					if (this.value == "") {
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
					<?php for($i=0;$i < 14; $i++) { ?>
					$(document).on('change', '#option_voicemail_select_<?php echo $i;?>',function() {
						var val = $(this).val();
						$('#option_voicemail_input_<?php echo $i;?>').val(val);
					});
					<?php } ?>
					$(document).on('change', '#option_voicemail_select_A',function() {
						var val = $(this).val();
						$('#option_voicemail_input_A').val(val);
					});
					$(document).on('change', '#option_voicemail_select_B',function() {
						var val = $(this).val();
						$('#option_voicemail_input_B').val(val);
					});
					$(document).on('change', '#option_voicemail_select_C',function() {
						var val = $(this).val();
						$('#option_voicemail_input_C').val(val);
					});
					$(document).on('change', '#option_voicemail_select_D',function() {
						var val = $(this).val();
						$('#option_voicemail_input_D').val(val);
					});
					$(document).on('change', '#option_voicemail_select_E',function() {
						var val = $(this).val();
						$('#option_voicemail_input_E').val(val);
					});
				
				//advanced ingroup settings
					<?php for($i=0;$i < 14; $i++) { ?>
					$(document).on('change', '#enter_filename_select_<?php echo $i;?>',function() {
						var val = $(this).val();
						$('#edit_enter_filename_<?php echo $i;?>').val(val);
					});
					<?php } ?>
					
					<?php for($i=0;$i < 14; $i++) { ?>
					$(document).on('change', '#edit_id_number_filename_select_<?php echo $i;?>',function() {
						var val = $(this).val();
						$('#edit_id_number_filename_<?php echo $i;?>').val(val);
					});
					<?php } ?>
					
					<?php for($i=0;$i < 14; $i++) { ?>
					$(document).on('change', '#edit_confirm_filename_select_<?php echo $i;?>',function() {
						var val = $(this).val();
						$('#edit_confirm_filename_<?php echo $i;?>').val(val);
					});
					<?php } ?>
			
			/* $('.select2-2').select2({
				theme: 'bootstrap'
			});*/
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
							//console.log(data);
							if (data == "success") {
								swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("agent_rank_update_success"); ?>", "success");
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
							}
						}
					});
	    	}
		}
	</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
