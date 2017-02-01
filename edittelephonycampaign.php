<?php

	###########################################################
	### Name: edittelephonycampaigns.php		  ###
	### Functions: Edit Campaings, Disposition 		  ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016		  ###
	### Version: 4.0 		  ###
	### Written by: Alexander Abenoja & Noel Umandap		  ###
	### License: AGPLv2		  ###
	###########################################################

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

$campaign_id = NULL;
if (isset($_POST["campaign"])) {
	$campaign_id = $_POST["campaign"];
}else{
	$campaign_id = $_GET["campaign"];
}

$did = NULL;
if (isset($_POST["disposition_id"])) {
	$did = $_POST["disposition_id"];
}

$lf_id = NULL;
if (isset($_POST["leadfilter"])) {
	$lf_id = $_POST["leadfilter"];
}

/*
 * APIs for forms
 */
$campaign = $ui->API_getCampaignInfo($campaign_id);
$disposition = $ui->API_getDispositionInfo($did);

$calltimes = $ui->getCalltimes();
$scripts = $ui->API_goGetAllScripts();
$carriers = $ui->getCarriers();
$leadfilter = $ui->API_getAllLeadFilters();
$dialStatus = $ui->API_getAllDialStatuses($campaign->data->campaign_id);
$campdialStatus = $ui->API_getAllCampaignDialStatuses($campaign->data->campaign_id);
$dids = $ui->API_getAllDIDs($campaign->data->campaign_id);
$voicefiles = $ui->API_GetVoiceFilesList();
$ingroups = $ui->API_getInGroups();
$ivr = $ui->API_getIVR();
$lists = $ui->API_goGetAllLists();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Goautodial Edit
        	<?php
        		if($campaign_id != NULL){echo "Campaign";}
        		if($did != NULL){echo "Disposition";}
        		if($lf_id != NULL){echo "Lead Filter";}
        	?>
        </title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>
        <!-- iCheck for checkboxes and radio inputs -->
  		<link rel="stylesheet" href="css/iCheck/all.css">
			<!-- SELECT2-->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
		<style type="text/css">
			.select2-container{
				width: 100% !important;
			}
		</style>
        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
		</script>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
		.select2-container{
			width: 100% !important;
		}
		
		.select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
			margin-top: 1px;
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
                        <?php $lh->translateText("telephony"); ?>
                        <small>Edit
                        	<?php
				        		if($campaign_id != NULL){echo "Campaign";}
				        		if($did != NULL){echo "Disposition";}
				        		if($lf_id != NULL){echo "Lead Filter";}
					        ?>
					    </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if($campaign_id != NULL || $did != NULL || $lf_id != NULL){
						?>
							<li><a href="./telephonycampaigns.php"><?php $lh->translateText("Campaign"); ?></a></li>
                        <?php
							}
                        ?>
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">

					<?php
						if(isset($_GET['message'])){
					?>
					<script>
						$(document).ready(function() {
					<?php if($_GET['message'] == "Success"){ ?>
						swal({	title: "Success",
							text: "Campaign Modified Successfully!",
							type: "success"
						},function(){
								window.location.href = 'telephonycampaigns.php';
						});
					<?php }else{ ?>
							sweetAlert("Oops...", "Something went wrong.", "error");
					<?php	}	?>
						});
					</script>
					<?php
						}
					?>
					
						<form id="campaign_form_edit" class="form-horizontal"  action="./php/ModifyTelephonyCampaign.php" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="campaign_id" value="<?php echo $campaign->data->campaign_id;?>">
							<input type="hidden" name="campaign_type" value="<?php echo $campaign->campaign_type;?>">
							<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
							<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
							<?php $errormessage = NULL; ?>

						<!-- IF CAMPAIGN -->
							<?php
							if($campaign_id != NULL) {
								if ($campaign->result=="success") {
									//echo "<pre>";
									//var_dump($campaign);
									//echo "</pre>";
							?>
							<div class="panel-body">
								<legend>MODIFY CAMPAIGN ID : <u><?php echo $campaign_id." - ".$campaign->data->campaign_name;?></u>
									<span class="pull-right">MANUAL DIAL LIST ID: <u><?php echo $campaign->data->manual_dial_list_id;?></u></span>
								</legend>
								<!-- Custom Tabs -->
								<div role="tabpanel">
								<!--<div class="nav-tabs-custom">-->
									<ul role="tablist" class="nav nav-tabs nav-justified">
										<li class="active"><a href="#tab_1" data-toggle="tab">Basic Settings</a></li>
										<li><a href="#tab_2" data-toggle="tab">Advanced Settings</a></li>
									</ul>
					               		<!-- Tab contents-->
					               		<div class="tab-content">
						               	<!-- BASIC SETTINGS -->
						                	<div id="tab_1" class="tab-pane fade in active">
												<fieldset>
													<div class="form-group mt">
														<label class="col-sm-2 control-label" for="campaign_name">Campaign Name:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control" name="campaign_name" id="campaign_name" value="<?php echo $campaign->data->campaign_name; ?>" title="Must be 6 to 40 characters in length." minlength="6" maxlength="40" required>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label" for="campaign_desc">Campaign Description:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control" name="campaign_desc" id="campaign_desc" value="<?php echo $campaign->data->campaign_name; ?>" >
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Active:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" name="active">
																<option value="Y" <?php if($campaign->data->active == 'Y') echo "selected";?>>Y</option>
																<option value="N" <?php if($campaign->data->active == "N") echo "selected";?>>N</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Dial Method:</label>
														<div class="col-sm-10 mb">
															<select name="dial_method" id="dial_method" class="form-control" name="dial_method">
																<option value="MANUAL" <?php if($campaign->data->dial_method == "MANUAL") echo "selected";?>>MANUAL</option>
																<option value="RATIO" <?php if($campaign->data->dial_method == "RATIO") echo "selected";?>>AUTO DIAL</option>
																<option value="ADAPT_TAPERED" <?php if($campaign->data->dial_method == "ADAPT_TAPERED") echo "selected";?>>PREDICTIVE</option>
																<option value="INBOUND_MAN" <?php if($campaign->data->dial_method == "INBOUND_MAN") echo "selected";?>>INBOUND MAN</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">AutoDial Level:</label>
														<div class="col-sm-10 mb">
															<div class="row">
																<?php
																	$autodial_level = $campaign->data->auto_dial_level;
																	
																?>
																<div class="col-lg-8">
																	<select id="auto_dial_level" class="form-control" name="auto_dial_level" <?php if($campaign->data->dial_method !== "RATIO") echo "disabled";?>>
																	<option value="OFF" <?php if($campaign->data->dial_method == "MANUAL") echo "selected";?> disabled>OFF</option>
										    						<option value="SLOW"<?php if($autodial_level == "1") echo "selected";?>>SLOW</option>
										    						<option VALUE="NORMAL" <?php if($autodial_level == "2") echo "selected";?>>NORMAL</option>
																	<option VALUE="HIGH" <?php if($autodial_level == "4") echo "selected";?>>HIGH</option>
										    						<option VALUE="MAX"<?php if($autodial_level == "6") echo "selected";?>>MAX</option>
										    						<option VALUE="MAX_PREDICTIVE"<?php if($autodial_level == "10" || $campaign->data->dial_method == "ADAPT_TAPERED") echo "selected";?> disabled>MAX_PREDICTIVE</option>
																	<option value="ADVANCE" <?php if($autodial_level != "0" && $autodial_level != "1" && $autodial_level != "2" && $autodial_level != "4" && $autodial_level != "6" && $autodial_level != "10") echo "selected";?> >ADVANCE</option>
																	</select>
																</div>
																<div class="col-lg-4">
																	<select id="auto_dial_level_adv" class="form-control <?php if($autodial_level == "0" || $autodial_level == "1" || $autodial_level == "2" || $autodial_level == "4" || $autodial_level == "6" || $autodial_level == "10") echo "hide";?> " name="auto_dial_level_adv">
																		<option value="1">1.0</option>
																		<option value="1.5" <?php if($autodial_level == "1.5") echo "selected"; ?> >1.5</option>
																		<option value="2">2.0</option>
																		<option value="2.5" <?php if($autodial_level == "2.5") echo "selected"; ?> >2.5</option>
																		<option value="3.0" <?php if($autodial_level == "3.0") echo "selected"; ?> >3.0</option>
																		<option value="3.5" <?php if($autodial_level == "3.5") echo "selected"; ?> >3.5</option>
																		<option value="4">4.0</option>
																		<option value="4.5" <?php if($autodial_level == "4.5") echo "selected"; ?> >4.5</option>
																		<option value="5.0" <?php if($autodial_level == "5.0") echo "selected"; ?> >5.0</option>
																		<option value="5.5" <?php if($autodial_level == "5.5") echo "selected"; ?> >5.5</option>
																		<option value="6">6.0</option>
																		<option value="6.5" <?php if($autodial_level == "6.5") echo "selected"; ?> >6.5</option>
																		<option value="7.0" <?php if($autodial_level == "7.0") echo "selected"; ?> >7.0</option>
																		<option value="7.5" <?php if($autodial_level == "7.5") echo "selected"; ?> >7.5</option>
																		<option value="8.0" <?php if($autodial_level == "8.0") echo "selected"; ?> >8.0</option>
																		<option value="8.5" <?php if($autodial_level == "8.5") echo "selected"; ?> >8.5</option>
																		<option value="9.0" <?php if($autodial_level == "9.0") echo "selected"; ?> >9.0</option>
																		<option value="9.5" <?php if($autodial_level == "9.5") echo "selected"; ?> >9.5</option>
																		<option value="10">10.0</option>
																		<option value="10.5" <?php if($autodial_level == "10.5") echo "selected"; ?> >10.5</option>
																		<option value="11.0" <?php if($autodial_level == "11.0") echo "selected"; ?> >11.0</option>
																		<option value="11.5" <?php if($autodial_level == "11.5") echo "selected"; ?> >11.5</option>
																		<option value="12.0" <?php if($autodial_level == "12.0") echo "selected"; ?> >12.0</option>
																		<option value="12.5" <?php if($autodial_level == "12.5") echo "selected"; ?> >12.5</option>
																		<option value="13.0" <?php if($autodial_level == "13.0") echo "selected"; ?> >13.0</option>
																		<option value="13.5" <?php if($autodial_level == "13.5") echo "selected"; ?> >13.5</option>
																		<option value="14.0" <?php if($autodial_level == "14.0") echo "selected"; ?> >14.0</option>
																		<option value="14.5" <?php if($autodial_level == "14.5") echo "selected"; ?> >14.5</option>
																		<option value="15.0" <?php if($autodial_level == "15.0") echo "selected"; ?> >15.0</option>
																		<option value="15.5" <?php if($autodial_level == "15.5") echo "selected"; ?> >15.5</option>
																		<option value="16.0" <?php if($autodial_level == "16.0") echo "selected"; ?> >16.0</option>
																		<option value="16.5" <?php if($autodial_level == "16.5") echo "selected"; ?> >16.5</option>
																		<option value="17.0" <?php if($autodial_level == "17.0") echo "selected"; ?> >17.0</option>
																		<option value="17.5" <?php if($autodial_level == "17.5") echo "selected"; ?> >17.5</option>
																		<option value="18.0" <?php if($autodial_level == "18.0") echo "selected"; ?> >18.0</option>
																		<option value="18.5" <?php if($autodial_level == "18.5") echo "selected"; ?> >18.5</option>
																		<option value="19.0" <?php if($autodial_level == "19.0") echo "selected"; ?> >19.0</option>
																		<option value="19.5" <?php if($autodial_level == "19.5") echo "selected"; ?> >19.5</option>
																		<option value="20.0" <?php if($autodial_level == "20.0") echo "selected"; ?> >20.0</option>
																	</select>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Carrier to use for Campaign:</label>
														<div class="col-sm-10 mb">
															<div class="row">
																<div class="col-lg-9">
																	<select name="dial_prefix" id="dial_prefix" class="form-control">
																		<option value="CUSTOM" <?php if($campaign->data->dial_prefix == "CUSTOM"){echo "selected";}?>>CUSTOM DIAL PREFIX</option>
																		<?php for($i=0;$i<=count($carriers->carrier_id);$i++) { ?>
																			<?php
																			if(!empty($carriers->carrier_id[$i])  && $carriers->active[$i] == 'Y') {
																				$prefixes = explode("\n", $carriers->dialplan_entry[$i]);
																				$prefix = explode(",", $prefixes[0]);
																				$dial_prefix = substr(ltrim($prefix[0], "exten => _ "), 0, (strpos(".",$prefix[0]) - 1));
																				$dial_prefix = str_replace("N", "", str_replace("X", "", $dial_prefix));
																			?>
																				<option value="<?php echo $dial_prefix; ?>" <?php if($campaign->data->dial_prefix == $dial_prefix) echo "selected";?>><?php echo $carriers->carrier_name[$i]; ?></option>
																			<?php } ?>
																		<?php } ?>
																	</select>
																</div>
																<div class="col-lg-3">
																	<input type="number" class="form-control" id="custom_prefix" name="custom_prefix" min="0" value="<?php if(($campaign->data->dial_prefix == "CUSTOM") && ($campaign->data->dial_prefix == 0) || ($campaign->data->dial_prefix == '')){echo 9;}else{echo $campaign->data->dial_prefix;} ?>" minlength="9" maxlength="20">
																</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Web Form:</label>
														<div class="col-sm-10 mb">
															<input type="text" id="web_form_address" name="web_form_address" class="form-control" value="<?php echo $campaign->data->web_form_address;?>">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Script:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="campaign_script" name="campaign_script">
																<option value="" <?php if(empty($campaign->data->campaign_script)) echo "selected"; ?>>--- NONE ---</option>
																<?php for($i=0;$i<=count($scripts->script_id);$i++) { ?>
																	<?php if(!empty($scripts->script_id[$i])) { ?>
																		<option value="<?php echo $scripts->script_id[$i]; ?>" <?php if($campaign->data->campaign_script == $scripts->script_id[$i]) echo "selected";?>><?php echo $scripts->script_name[$i]; ?></option>
																	<?php } ?>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Campaign Caller ID:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control" id="campaign_cid" name="campaign_cid" value="<?php echo $campaign->data->campaign_cid; ?>">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Campaign Recording:</label>
														<div class="col-sm-10 mb">
															<select id="campaign_recording" class="form-control" name="campaign_recording">
																<option value="NEVER" <?php if($campaign->data->campaign_recording == "NEVER") echo "selected";?>>OFF</option>
																<option value="ALLFORCE" <?php if($campaign->data->campaign_recording == "ALLFORCE") echo "selected";?>>ON</option>
																<option value="ONDEMAND" <?php if($campaign->data->campaign_recording == "ONDEMAND") echo "selected";?>>ONDEMAND</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Answer Machine Detection:</label>
														<div class="col-sm-10 mb">
															<select id="campaign_vdad_exten" name="campaign_vdad_exten" class="form-control">
																<option value="<?php if($campaign->campaign_type == "SURVEY"){ echo '8366';}else{ echo '8368';} ?>" <?php if ($campaign->data->campaign_vdad_exten == "8368" || $campaign->data->campaign_vdad_exten == "8366") echo "selected"; ?>>OFF</option>
																<option value="<?php if($campaign->campaign_type == "SURVEY"){ echo '8373';}else{ echo '8369';}  ?>" <?php if ($campaign->data->campaign_vdad_exten == "8369" || $campaign->data->campaign_vdad_exten == "8373") echo "selected"; ?>>ON</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Local Calltime:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="local_call_time" name="local_call_time">
																<?php for($i=0;$i<=count($calltimes->call_time_id);$i++) { ?>
																	<?php if(!empty($calltimes->call_time_id[$i])) { ?>
																		<option value="<?php echo $calltimes->call_time_id[$i]; ?>"<?php if($campaign->data->local_call_time == $calltimes->call_time_id[$i]) echo "selected"; ?>><?php echo $calltimes->call_time_name[$i]; ?></option>
																	<?php } ?>
																<?php } ?>
															</select>
														</div>
													</div>
												<?php if($campaign->campaign_type == "OUTBOUND") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Minimum Hopper Level:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="hopper_level" name="hopper_level">
																<?php
																$hopper_level = array (1, 5, 10, 20, 50, 100, 200, 500, 700, 1000, 2000);
																foreach ($hopper_level as $level) {
																	$selectThis = '';
																	if ($level == $campaign->data->hopper_level) { $selectThis = 'selected'; }
																	echo '<option value="'.$level.'" '.$selectThis.'>'.$level.'</option>';
																}
																?>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Force Reset of Hopper:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="force_reset_hopper" name="force_reset_hopper">
																<option value="Y">Y</option>
																<option value="N" selected>N</option>
															</select>
														</div>
													</div>
												<?php } elseif($campaign->campaign_type == "INBOUND") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Phone Numbers (DID/TFN) on this campaign:</label>
														<span class="col-sm-10 control-label" style="text-align: left; vertical-align: top;">
															<?php if(count($dids->did_id) != 0) {?>
																<?php for($i=0;$i<=count($dids->did_id);$i++) { ?>
																	<?php if(!empty($dids->did_id[$i])){ ?>
																		<p><?php echo $dids->did_pattern[$i]; ?></p>
																	<?php } ?>
																<?php }?>
															<?php } else { ?>
																No <b>DID/'s</b> found for this campaign.
															<?php } ?>
														</span>
													</div>
													<!--<div class="form-group">
														<label class="col-sm-2 control-label">Inbound Man:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="inbound_man" name="inbound_man">
																<option value="Y" <?php if($campaign->data->dial_method == "INBOUND_MAN") echo "selected";?>>Yes</option>
																<option value="N" <?php if($campaign->data->dial_method == "AUTO_DIAL") echo "selected";?>>No</option>
															</select>
														</div>
													</div>-->
												<?php } elseif($campaign->campaign_type == "BLENDED") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Phone Numbers (DID/TFN) on this campaign:</label>
														<span class="col-sm-10 control-label" style="text-align: left; vertical-align: top;">
															<?php if(count($dids->did_id) != 0) {?>
																<?php for($i=0;$i<=count($dids->did_id);$i++) { ?>
																	<?php if(!empty($dids->did_id[$i])){ ?>
																		<p><?php echo $dids->did_pattern[$i]; ?></p>
																	<?php } ?>
																<?php }?>
															<?php } else { ?>
																No <b>DID/'s</b> found for this campaign.
															<?php } ?>
														</span>
													</div>
												<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Audio File:</label>
														<div class="col-sm-8 mb">
															<input type="text" class="form-control" id="survey_first_audio_file" name="survey_first_audio_file" value="<?php echo $campaign->data->survey_first_audio_file; ?>">
														</div>
														<div class="col-sm-2 mb">
															<button type="button" class="view-audio-files btn btn-default" data-label="survey_first_audio_file">Audio</button>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Survey Method:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="survey_method" name="survey_method">
																<option value="AGENT_XFER" <?php if($campaign->data->survey_method == "AGENT_XFER") echo "selected";?>>CAMPAIGN</option>
																<option value="EXTENSION" <?php if($campaign->data->survey_method == "EXTENSION") echo "selected";?>>DID</option>
																<option value="CALLMENU" <?php if($campaign->data->survey_method == "CALLMENU") echo "selected";?>>CALLMENU</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Survey Call Menu:</label>
														<div class="col-sm-10 mb">
															<select id="survey_menu_id" name="survey_menu_id" class="form-control">
																<option value="" <?php if($campaign->data->survey_menu_id == "") echo "selected";?>>-- NONE --</option>
																<?php for($i=0;$i < count($ivr->menu_id);$i++){ ?>
																		<option value="<?php echo $ivr->menu_id[$i]; ?>" <?php if($campaign->data->survey_menu_id == $ivr->menu_id[$i]) echo "selected";?>><?php echo $ivr->menu_id[$i]." - ".$ivr->menu_name[$i]; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
												<?php } else { ?>
													<!-- Nothing to do -->
												<?php } ?>
												</fieldset><!-- /.fieldset -->
											</div><!-- /.tab-pane -->

											<div class="tab-pane fade in" id="tab_2">
												<fieldset>
													<?php if($campaign->campaign_type != "SURVEY") { ?>
													<div class="form-group">
														<label class="col-sm-3 control-label">Allowed Inbound and Blended:</label>
														<div class="col-sm-9 mb">
															<select class="form-control" id="campaign_allow_inbound" name="campaign_allow_inbound">
																<option value="N" <?php if($campaign->data->campaign_allow_inbound == "N") echo "selected";?>>N</option>
																<option value="Y" <?php if($campaign->data->campaign_allow_inbound == "Y") echo "selected";?>>Y</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">Launch Custom Fields:</label>
														<div class="col-sm-9 mb">
															<select class="form-control" id="custom_fields_launch" name="custom_fields_launch">
																<option value="ONCALL" <?php if($campaign->custom_fields_launch == "ONCALL") echo "selected";?>>ONCALL</option>
																<option value="LOGIN" <?php if($campaign->custom_fields_launch == "LOGIN") echo "selected";?>>LOGIN</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">Custom Fields List ID:</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" value="<?php if(!empty($campaign->custom_fields_list_id)){echo $campaign->custom_fields_list_id;}?>" id="custom_fields_list_id" name="custom_fields_list_id">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">Call Notes Per Call:</label>
														<div class="col-sm-9 mb">
															<select class="form-control" id="per_call_notes" name="per_call_notes">
																<option value="DISABLED" <?php if($campaign->data->per_call_notes == "DISABLED") echo "selected";?>>DISABLED</option>
																<option value="ENABLED" <?php if($campaign->data->per_call_notes == "ENABLED") echo "selected";?>>ENABLED</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">URL Tab 1:</label>
														<div class="col-sm-3 mb">
															<input type="text" class="form-control" placeholder="Enter Title" value="<?php if(!empty($campaign->url_tab_first_title)){echo $campaign->url_tab_first_title;}?>" id="url_tab_first_title" name="url_tab_first_title">
														</div>
														<div class="col-sm-6 mb">
															<input type="text" class="form-control" placeholder="Enter URL (eg. https://www.goautodial.com, URL must be served over HTTPS)" value="<?php if(!empty($campaign->url_tab_first_url)){echo $campaign->url_tab_first_url;}?>" id="url_tab_first_url" name="url_tab_first_url">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label">URL Tab 2:</label>
														<div class="col-sm-3 mb">
															<input type="text" class="form-control" placeholder="Enter Title" value="<?php if(!empty($campaign->url_tab_second_title)){echo $campaign->url_tab_second_title;}?>" id="url_tab_second_title" name="url_tab_second_title">
														</div>
														<div class="col-sm-6 mb">
															<input type="text" class="form-control" placeholder="Enter URL (eg. https://www.goautodial.com, URL must be served over HTTPS)" value="<?php if(!empty($campaign->url_tab_second_url)){echo $campaign->url_tab_second_url;}?>" id="url_tab_second_url" name="url_tab_second_url">
														</div>
													</div>
													<?php } ?>
													<?php if($campaign->campaign_type == "OUTBOUND") { ?>
														<div class="form-group" style="margin-bottom: 10px;">
															<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=1;?>
															<?php foreach($dial_statuses as $dial_status) { ?>
																<?php if(!empty($dial_status)) { ?>
																	<label class="col-sm-3 control-label">Active Dial Status <?php echo $i; ?>:</label>
																	<span class="col-sm-8 control-label" style="text-align: left;">
																		<label><?php echo $dial_status; ?></label> - <span><?php $lh->translateText($dial_status); ?></span>
																	</span>
																	<span class="col-sm-1 control-label">
																		<a href="#" class="remove-this-dial-status"  data-campaign="<?php echo $campaign_id; ?>" data-dial-status="<?php echo $campaign->data->dial_statuses;?>" data-selected-status="<?php echo $dial_status; ?>">Remove</a>
																	</span>
																<?php $i++; ?>
																<?php } ?>
															<?php } ?>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Status:</label>
															<div class="col-sm-8 mb">
																<select class="form-control" id="dial_status" name="dial_status">
																	<option value="" selected>NONE</option>
																	<optgroup label="System Statuses">
																		<?php for($i=0;$i<=count($dialStatus->status);$i++) { ?>
																			<?php if( !empty($dialStatus->status[$i]) && !in_array($dialStatus->status[$i], $dial_statuses) ){ ?>
																				<option value="<?php echo $dialStatus->status[$i]?>">
																					<?php echo $dialStatus->status[$i]." - ".$dialStatus->status_name[$i]?>
																				</option>
																			<?php } ?>
																		<?php } ?>
																	</optgroup>
																	<?php if(count($campdialStatus->status) > 0){ ?>
																		<optgroup label="Campaign Statuses">
																		<?php for($i=0;$i<=count($campdialStatus->status);$i++) { ?>
																			<?php if( !empty($campdialStatus->status[$i])  && !in_array($campdialStatus->status[$i], $dial_statuses) ){ ?>
																				<option value="<?php echo $campdialStatus->status[$i]?>">
																					<?php echo $campdialStatus->status[$i]." - ".$campdialStatus->status_name[$i]?>
																				</option>
																			<?php } ?>
																		<?php } ?>
																		</optgroup>
																	<?php } ?>
																</select>
															</div>
															<div class="col-sm-1 mb">
																<button type="button" class="btn btn-default btn-add-dial-status" data-campaign="<?php echo $campaign_id; ?>" data-dial-status="<?php echo $campaign->data->dial_statuses;?>">Add</button>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">List Order:</label>
															<div class="col-sm-9 mb">
																<select size="1" name="lead_order" id="lead_order" class="form-control">
														            <option value="DOWN" <?php if($campaign->data->lead_order == "DOWN") echo "selected"; ?>>DOWN</option>
														            <option value="UP" <?php if($campaign->data->lead_order == "UP") echo "selected"; ?>>UP</option>
														            <option value="DOWN_PHONE" <?php if($campaign->data->lead_order == "DOWN_PHONE") echo "selected"; ?>>DOWN PHONE</option>
														            <option value="UP_PHONE" <?php if($campaign->data->lead_order == "UP_PHONE") echo "selected"; ?>>UP PHONE</option>
														            <option value="DOWN_LAST_NAME" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME") echo "selected"; ?>>DOWN LAST NAME</option>
														            <option value="UP_LAST_NAME" <?php if($campaign->data->lead_order == "UP_LAST_NAME") echo "selected"; ?>>UP LAST NAME</option>
														            <option value="DOWN_COUNT" <?php if($campaign->data->lead_order == "DOWN_COUNT") echo "selected"; ?>>DOWN COUNT</option>
														            <option value="UP_COUNT" <?php if($campaign->data->lead_order == "UP_COUNT") echo "selected"; ?>>UP COUNT</option>
														            <option value="RANDOM" <?php if($campaign->data->lead_order == "RANDOM") echo "selected"; ?>>RANDOM</option>
														            <option value="DOWN_LAST_CALL_TIME" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME") echo "selected"; ?>>DOWN LAST CALL TIME</option>
														            <option value="UP_LAST_CALL_TIME" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME") echo "selected"; ?>>UP LAST CALL TIME</option>
														            <option value="DOWN_RANK" <?php if($campaign->data->lead_order == "DOWN_RANK") echo "selected"; ?>>DOWN RANK</option>
														            <option value="UP_RANK" <?php if($campaign->data->lead_order == "UP_RANK") echo "selected"; ?>>UP RANK</option>
														            <option value="DOWN_OWNER" <?php if($campaign->data->lead_order == "DOWN_OWNER") echo "selected"; ?>>DOWN OWNER</option>
														            <option value="UP_OWNER" <?php if($campaign->data->lead_order == "UP_OWNER") echo "selected"; ?>>UP OWNER</option>
														            <option value="DOWN_TIMEZONE" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE") echo "selected"; ?>>DOWN TIMEZONE</option>
														            <option value="UP_TIMEZONE" <?php if($campaign->data->lead_order == "UP_TIMEZONE") echo "selected"; ?>>UP TIMEZONE</option>
														            <option value="DOWN_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_2nd_NEW") echo "selected"; ?>>DOWN 2nd NEW</option>
														            <option value="DOWN_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_3rd_NEW") echo "selected"; ?>>DOWN 3rd NEW</option>
														            <option value="DOWN_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_4th_NEW") echo "selected"; ?>>DOWN 4th NEW</option>
														            <option value="DOWN_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_5th_NEW") echo "selected"; ?>>DOWN 5th NEW</option>
														            <option value="DOWN_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_6th_NEW") echo "selected"; ?>>DOWN 6th NEW</option>
														            <option value="UP_2nd_NEW" <?php if($campaign->data->lead_order == "UP_2nd_NEW") echo "selected"; ?>>UP 2nd NEW</option>
														            <option value="UP_3rd_NEW" <?php if($campaign->data->lead_order == "UP_3rd_NEW") echo "selected"; ?>>UP 3rd NEW</option>
														            <option value="UP_4th_NEW" <?php if($campaign->data->lead_order == "UP_4th_NEW") echo "selected"; ?>>UP 4th NEW</option>
														            <option value="UP_5th_NEW" <?php if($campaign->data->lead_order == "UP_5th_NEW") echo "selected"; ?>>UP 5th NEW</option>
														            <option value="UP_6th_NEW" <?php if($campaign->data->lead_order == "UP_6th_NEW") echo "selected"; ?>>UP 6th NEW</option>
														            <option value="DOWN_PHONE_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_2nd_NEW") echo "selected"; ?>>DOWN PHONE 2nd NEW</option>
														            <option value="DOWN_PHONE_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_3rd_NEW") echo "selected"; ?>>DOWN PHONE 3rd NEW</option>
														            <option value="DOWN_PHONE_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_4th_NEW") echo "selected"; ?>>DOWN PHONE 4th NEW</option>
														            <option value="DOWN_PHONE_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_5th_NEW") echo "selected"; ?>>DOWN PHONE 5th NEW</option>
														            <option value="DOWN_PHONE_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_6th_NEW") echo "selected"; ?>>DOWN PHONE 6th NEW</option>
														            <option value="UP_PHONE_2nd_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_2nd_NEW") echo "selected"; ?>>UP PHONE 2nd NEW</option>
														            <option value="UP_PHONE_3rd_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_3rd_NEW") echo "selected"; ?>>UP PHONE 3rd NEW</option>
														            <option value="UP_PHONE_4th_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_4th_NEW") echo "selected"; ?>>UP PHONE 4th NEW</option>
														            <option value="UP_PHONE_5th_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_5th_NEW") echo "selected"; ?>>UP PHONE 5th NEW</option>
														            <option value="UP_PHONE_6th_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_6th_NEW") echo "selected"; ?>>UP PHONE 6th NEW</option>
														            <option value="DOWN_LAST_NAME_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_2nd_NEW") echo "selected"; ?>>DOWN LAST NAME 2nd NEW</option>
														            <option value="DOWN_LAST_NAME_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_3rd_NEW") echo "selected"; ?>>DOWN LAST NAME 3rd NEW</option>
														            <option value="DOWN_LAST_NAME_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_4th_NEW") echo "selected"; ?>>DOWN LAST NAME 4th NEW</option>
														            <option value="DOWN_LAST_NAME_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_5th_NEW") echo "selected"; ?>>DOWN LAST NAME 5th NEW</option>
														            <option value="DOWN_LAST_NAME_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_6th_NEW") echo "selected"; ?>>DOWN LAST NAME 6th NEW</option>
														            <option value="UP_LAST_NAME_2nd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_2nd_NEW") echo "selected"; ?>>UP LAST NAME 2nd NEW</option>
														            <option value="UP_LAST_NAME_3rd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_3rd_NEW") echo "selected"; ?>>UP LAST NAME 3rd NEW</option>
														            <option value="UP_LAST_NAME_4th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_4th_NEW") echo "selected"; ?>>UP LAST NAME 4th NEW</option>
														            <option value="UP_LAST_NAME_5th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_5th_NEW") echo "selected"; ?>>UP LAST NAME 5th NEW</option>
														            <option value="UP_LAST_NAME_6th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_6th_NEW") echo "selected"; ?>>UP LAST NAME 6th NEW</option>
														            <option value="DOWN_COUNT_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_2nd_NEW") echo "selected"; ?>>DOWN COUNT 2nd NEW</option>
														            <option value="DOWN_COUNT_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_3rd_NEW") echo "selected"; ?>>DOWN COUNT 3rd NEW</option>
														            <option value="DOWN_COUNT_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_4th_NEW") echo "selected"; ?>>DOWN COUNT 4th NEW</option>
														            <option value="DOWN_COUNT_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_5th_NEW") echo "selected"; ?>>DOWN COUNT 5th NEW</option>
														            <option value="DOWN_COUNT_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_6th_NEW") echo "selected"; ?>>DOWN COUNT 6th NEW</option>
														            <option value="UP_COUNT_2nd_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_2nd_NEW") echo "selected"; ?>>UP COUNT 2nd NEW</option>
														            <option value="UP_COUNT_3rd_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_3rd_NEW") echo "selected"; ?>>UP COUNT 3rd NEW</option>
														            <option value="UP_COUNT_4th_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_4th_NEW") echo "selected"; ?>>UP COUNT 4th NEW</option>
														            <option value="UP_COUNT_5th_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_5th_NEW") echo "selected"; ?>>UP COUNT 5th NEW</option>
														            <option value="UP_COUNT_6th_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_6th_NEW") echo "selected"; ?>>UP COUNT 6th NEW</option>
														            <option value="RANDOM_2nd_NEW" <?php if($campaign->data->lead_order == "RANDOM_2nd_NEW") echo "selected"; ?>>RANDOM 2nd NEW</option>
														            <option value="RANDOM_3rd_NEW" <?php if($campaign->data->lead_order == "RANDOM_3rd_NEW") echo "selected"; ?>>RANDOM 3rd NEW</option>
														            <option value="RANDOM_4th_NEW" <?php if($campaign->data->lead_order == "RANDOM_4th_NEW") echo "selected"; ?>>RANDOM 4th NEW</option>
														            <option value="RANDOM_5th_NEW" <?php if($campaign->data->lead_order == "RANDOM_5th_NEW") echo "selected"; ?>>RANDOM 5th NEW</option>
														            <option value="RANDOM_6th_NEW" <?php if($campaign->data->lead_order == "RANDOM_6th_NEW") echo "selected"; ?>>RANDOM 6th NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 2nd NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 3rd NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 4th NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 5th NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 6th NEW</option>
														            <option value="UP_LAST_CALL_TIME_2nd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>UP LAST CALL TIME 2nd NEW</option>
														            <option value="UP_LAST_CALL_TIME_3rd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>UP LAST CALL TIME 3rd NEW</option>
														            <option value="UP_LAST_CALL_TIME_4th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>UP LAST CALL TIME 4th NEW</option>
														            <option value="UP_LAST_CALL_TIME_5th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>UP LAST CALL TIME 5th NEW</option>
														            <option value="UP_LAST_CALL_TIME_6th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>UP LAST CALL TIME 6th NEW</option>
														            <option value="DOWN_RANK_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_2nd_NEW") echo "selected"; ?>>DOWN RANK 2nd NEW</option>
														            <option value="DOWN_RANK_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_3rd_NEW") echo "selected"; ?>>DOWN RANK 3rd NEW</option>
														            <option value="DOWN_RANK_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_4th_NEW") echo "selected"; ?>>DOWN RANK 4th NEW</option>
														            <option value="DOWN_RANK_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_5th_NEW") echo "selected"; ?>>DOWN RANK 5th NEW</option>
														            <option value="DOWN_RANK_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_6th_NEW") echo "selected"; ?>>DOWN RANK 6th NEW</option>
														            <option value="UP_RANK_2nd_NEW" <?php if($campaign->data->lead_order == "UP_RANK_2nd_NEW") echo "selected"; ?>>UP RANK 2nd NEW</option>
														            <option value="UP_RANK_3rd_NEW" <?php if($campaign->data->lead_order == "UP_RANK_3rd_NEW") echo "selected"; ?>>UP RANK 3rd NEW</option>
														            <option value="UP_RANK_4th_NEW" <?php if($campaign->data->lead_order == "UP_RANK_4th_NEW") echo "selected"; ?>>UP RANK 4th NEW</option>
														            <option value="UP_RANK_5th_NEW" <?php if($campaign->data->lead_order == "UP_RANK_5th_NEW") echo "selected"; ?>>UP RANK 5th NEW</option>
														            <option value="UP_RANK_6th_NEW" <?php if($campaign->data->lead_order == "UP_RANK_6th_NEW") echo "selected"; ?>>UP RANK 6th NEW</option>
														            <option value="DOWN_OWNER_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_2nd_NEW") echo "selected"; ?>>DOWN OWNER 2nd NEW</option>
														            <option value="DOWN_OWNER_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_3rd_NEW") echo "selected"; ?>>DOWN OWNER 3rd NEW</option>
														            <option value="DOWN_OWNER_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_4th_NEW") echo "selected"; ?>>DOWN OWNER 4th NEW</option>
														            <option value="DOWN_OWNER_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_5th_NEW") echo "selected"; ?>>DOWN OWNER 5th NEW</option>
														            <option value="DOWN_OWNER_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_6th_NEW") echo "selected"; ?>>DOWN OWNER 6th NEW</option>
														            <option value="UP_OWNER_2nd_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_2nd_NEW") echo "selected"; ?>>UP OWNER 2nd NEW</option>
														            <option value="UP_OWNER_3rd_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_3rd_NEW") echo "selected"; ?>>UP OWNER 3rd NEW</option>
														            <option value="UP_OWNER_4th_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_4th_NEW") echo "selected"; ?>>UP OWNER 4th NEW</option>
														            <option value="UP_OWNER_5th_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_5th_NEW") echo "selected"; ?>>UP OWNER 5th NEW</option>
														            <option value="UP_OWNER_6th_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_6th_NEW") echo "selected"; ?>>UP OWNER 6th NEW</option>
														            <option value="DOWN_TIMEZONE_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_2nd_NEW") echo "selected"; ?>>DOWN TIMEZONE 2nd NEW</option>
														            <option value="DOWN_TIMEZONE_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_3rd_NEW") echo "selected"; ?>>DOWN TIMEZONE 3rd NEW</option>
														            <option value="DOWN_TIMEZONE_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_4th_NEW") echo "selected"; ?>>DOWN TIMEZONE 4th NEW</option>
														            <option value="DOWN_TIMEZONE_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_5th_NEW") echo "selected"; ?>>DOWN TIMEZONE 5th NEW</option>
														            <option value="DOWN_TIMEZONE_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_6th_NEW") echo "selected"; ?>>DOWN TIMEZONE 6th NEW</option>
														            <option value="UP_TIMEZONE_2nd_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_2nd_NEW") echo "selected"; ?>>UP TIMEZONE 2nd NEW</option>
														            <option value="UP_TIMEZONE_3rd_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_3rd_NEW") echo "selected"; ?>>UP TIMEZONE 3rd NEW</option>
														            <option value="UP_TIMEZONE_4th_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_4th_NEW") echo "selected"; ?>>UP TIMEZONE 4th NEW</option>
														            <option value="UP_TIMEZONE_5th_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_5th_NEW") echo "selected"; ?>>UP TIMEZONE 5th NEW</option>
														            <option value="UP_TIMEZONE_6TH_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_6TH_NEW") echo "selected"; ?>>UP TIMEZONE 6th NEW</option>
														        </select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Lead Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="lead_filter" name="lead_filter">
																	<option value="" <?php if($campaign->data->lead_filter_id == "") echo "selected";?>>NONE</option>
																	<?php for($i=0;$i<=count($leadfilter->lead_filter_id);$i++) { ?>
																		<?php if(!empty($leadfilter->lead_filter_id[$i])) { ?>
																			<option value="<?php echo $leadfilter->lead_filter_id[$i]; ?>" <?php if($campaign->data->lead_filter_id == $leadfilter->lead_filter_id[$i]) echo "selected";?>><?php echo $leadfilter->lead_filter_name[$i]; ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial timeout:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Prefix:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="manual_dial_prefix" name="manual_dial_prefix" value="<?php echo $campaign->data->manual_dial_prefix; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Get Call Launch:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="get_call_launch" name="get_call_launch">
																	<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Answering Machine Message:</label>
															<div class="col-sm-9 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="am_message_exten" name="am_message_exten" value="<?php echo $campaign->data->am_message_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_am_message_chooser" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<select class="form-control am_message_chooser" id="am_message_chooser" name="am_message_chooser">
																	<option value="">-- Default Value --</option>
																	<?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
																		<?php if(!empty($voicefiles->file_name[$i])) { ?>
																			<option value="<?php echo substr($voicefiles->file_name[$i], 0, -4); ?>"><?php echo substr($voicefiles->file_name[$i], 0, -4); ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">AMD send to Action:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																	<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">WaitForSilence Options:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Pause Codes:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																	<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																	<option value="FORCE" <?php if($campaign->data->agent_pause_codes_active == "FORCE") echo "selected";?>>FORCE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																	<option value="NONE" <?php if($campaign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																	<option value="DNC_ONLY" <?php if($campaign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																	<option value="CAMPLIST_ONLY" <?php if($campaign->data->manual_dial_filter == "CAMPLIST_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																	<option value="DNC_AND_CAMPLIST" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLIST") echo "selected";?>>DNC & CAMPLIST</option>
																	<option value="DNC_AND_CAMPLIST_ALL" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLIST_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Use Internal DNC:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="use_internal_dnc" name="use_internal_dnc">
																	<option value="Y" <?php if($campaign->data->use_internal_dnc == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->use_internal_dnc == "N") echo "selected";?>>NO</option>
																	<option value="AREACODE" <?php if($campaign->data->use_internal_dnc == "AREACODE") echo "selected";?>>AREACODE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Use Campaign DNC:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="use_campaign_dnc" name="use_campaign_dnc">
																	<option value="Y" <?php if($campaign->data->use_campaign_dnc == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->use_campaign_dnc == "N") echo "selected";?>>NO</option>
																	<option value="AREACODE" <?php if($campaign->data->use_campaign_dnc == "AREACODE") echo "selected";?>>AREACODE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial List ID:</label>
															<div class="col-sm-9 mb">
																<select class="form-control select2" id="manual_dial_list_id" name="manual_dial_list_id">
																	<!-- <option value="998" <?php //if($campaign->data->manual_dial_list_id == 998 || $campaign->data->manual_dial_list_id == 0) echo "selected";?>>998</option>
																	<option value="999" <?php //if($campaign->data->manual_dial_list_id == 999) echo "selected";?>>999</option> -->
																	<?php for($i=0;$i<count($lists->list_id);$i++){ ?>
								                        <option value="<?php echo $lists->list_id[$i];?>" <?php if($lists->list_id[$i] == $campaign->data->manual_dial_list_id) echo "selected";?>><?php echo $lists->list_id[$i]; ?></option>';
								                  <?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Available Only Tally:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="available_only_ratio_tally" name="available_only_ratio_tally">
																	<option value="N" <?php if($campaign->data->available_only_ratio_tally == 'N') echo "selected";?>>NO</option>
																	<option value="Y" <?php if($campaign->data->available_only_ratio_tally == 'Y') echo "selected";?>>YES</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Campaign Recording Filename:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Next Agent Call:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="next_agent_call" name="next_agent_call">
																	<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																	<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																	<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																	<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																	<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																	<option value="LONGEST_WAITING_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAITING_TIME") echo "selected";?>>LONGEST WAITING TIME</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Transfer-Conf Number 1:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" value="<?php if(!empty($campaign->data->xferconf_a_number)){echo $campaign->data->xferconf_a_number;}else{echo "";}?>" id="xferconf_a_number" name="xferconf_a_number">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Transfer-Conf Number 2:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" value="<?php if(!empty($campaign->data->xferconf_b_number)){echo $campaign->data->xferconf_b_number;}else{echo "";}?>" id="xferconf_b_number" name="xferconf_b_number">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Caller ID for 3-way Calls:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="three_way_call_cid" name="three_way_call_cid">
																	<option value="CUSTOM" <?php if($campaign->data->three_way_call_cid == "CUSTOM") echo "selected";?>>CUSTOM</option>
																	<option value="CAMPAIGN" <?php if($campaign->data->three_way_call_cid == "CAMPAIGN") echo "selected";?>>CAMPAIGN</option>
																	<option value="CUSTOMER" <?php if($campaign->data->three_way_call_cid == "CUSTOMER") echo "selected";?>>CUSTOMER</option>
																	<option value="AGENT_PHONE" <?php if($campaign->data->three_way_call_cid == "AGENT_PHONE") echo "selected";?>>AGENT PHONE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Prefix for 3-way Calls:</label>
															<div class="col-sm-9 mb">
																<?php
																// old condition
																// if(!empty($campaign->data->three_way_dial_prefix)){echo $campaign->data->three_way_dial_prefix;}else{echo "88";}
																?>	
																<input type="text" class="form-control" value="<?php echo $campaign->data->three_way_dial_prefix; ?>" id="three_way_dial_prefix" name="three_way_dial_prefix" maxlength="20">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Customer 3-way Hangup Logging:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="customer_3way_hangup_logging" name="customer_3way_hangup_logging">
																	<option value="ENABLED" <?php if($campaign->data->customer_3way_hangup_logging == "ENABLED") echo "selected";?>>ENABLED</option>
																	<option value="DISABLED" <?php if($campaign->data->customer_3way_hangup_logging == "DISABLED") echo "selected";?>>DISABLED</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Customer 3-way Hangup Seconds:</label>
															<div class="col-sm-9 mb">
																<input type="number" class="form-control" id="customer_3way_hangup_seconds" name="customer_3way_hangup_seconds" min="0" value="<?php if(!empty($campaign->data->customer_3way_hangup_seconds)){echo $campaign->data->customer_3way_hangup_seconds;}else{echo "5";} ?>" onkeydown="return FilterInput(event)">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Customer 3-way Hangup Action:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="customer_3way_hangup_action" name="customer_3way_hangup_action">
																	<option value="DISPO" <?php if($campaign->data->customer_3way_hangup_action == "DISPO") echo "selected";?>>DISPO</option>
																	<option value="NONE"> <?php if($campaign->data->customer_3way_hangup_action == "NONE") echo "selected";?></option>
																</select>
															</div>
														</div>
													<?php } elseif($campaign->campaign_type == "INBOUND") { ?>
														<div class="form-group" style="margin-bottom: 10px;">
															<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=1;?>
															<?php foreach($dial_statuses as $dial_status) { ?>
																<?php if(!empty($dial_status)) { ?>
																	<label class="col-sm-3 control-label">Active Dial Status <?php echo $i; ?>:</label>
																	<span class="col-sm-8 control-label" style="text-align: left;">
																		<label><?php echo $dial_status; ?></label> - <span><?php $lh->translateText($dial_status); ?></span>
																	</span>
																	<span class="col-sm-1 control-label">
																		<a href="#" class="remove-this-dial-status"  data-campaign="<?php echo $campaign_id; ?>" data-dial-status="<?php echo $campaign->data->dial_statuses;?>" data-selected-status="<?php echo $dial_status; ?>">Remove</a>
																	</span>
																	<?php $i++; ?>
																<?php } ?>
															<?php } ?>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Status:</label>
															<div class="col-sm-8 mb">
																<select class="form-control" id="dial_status" name="dial_status">
																	<option value="" selected>NONE</option>
																	<optgroup label="System Statuses">
																	<?php for($i=0;$i<=count($dialStatus->status);$i++) { ?>
																		<?php if( !empty($dialStatus->status[$i]) && !in_array($dialStatus->status[$i], $dial_statuses) ){ ?>
																			<option value="<?php echo $dialStatus->status[$i]?>">
																				<?php echo $dialStatus->status[$i]." - ".$dialStatus->status_name[$i]?>
																			</option>
																		<?php } ?>
																	<?php } ?>
																	</optgroup>
																	<?php if(count($campdialStatus->status) > 0){ ?>
																		<optgroup label="Campaign Statuses">
																		<?php for($i=0;$i<=count($campdialStatus->status);$i++) { ?>
																			<?php if( !empty($campdialStatus->status[$i])  && !in_array($campdialStatus->status[$i], $dial_statuses) ){ ?>
																				<option value="<?php echo $campdialStatus->status[$i]?>">
																					<?php echo $campdialStatus->status[$i]." - ".$campdialStatus->status_name[$i]?>
																				</option>
																			<?php } ?>
																		<?php } ?>
																		</optgroup>
																	<?php } ?>
																</select>
															</div>
															<div class="col-sm-1 mb">
																<button type="button" class="btn btn-default btn-add-dial-status" data-campaign="<?php echo $campaign_id; ?>" data-dial-status="<?php echo $campaign->data->dial_statuses;?>">Add</button>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Get Call Launch:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="get_call_launch" name="get_call_launch">
																	<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Answering Machine Message:</label>
															<div class="col-sm-9 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="am_message_exten" name="am_message_exten" value="<?php echo $campaign->data->am_message_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<select class="form-control am_message_chooser" id="am_message_chooser" name="am_message_chooser">
																	<option value="">-- Default Value --</option>
																	<?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
																		<?php if(!empty($voicefiles->file_name[$i])) { ?>
																			<option value="<?php echo substr($voicefiles->file_name[$i], 0, -4); ?>"><?php echo substr($voicefiles->file_name[$i], 0, -4); ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">AMD send to Action:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																	<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">WaitForSilence Options:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Pause Codes:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																	<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																	<option value="FORCE" <?php if($campaign->data->agent_pause_codes_active == "FORCE") echo "selected";?>>FORCE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																	<option value="NONE" <?php if($campaign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																	<option value="DNC_ONLY" <?php if($campaign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																	<option value="CAMPLIST_ONLY" <?php if($campaign->data->manual_dial_filter == "CAMPLIST_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																	<option value="DNC_AND_CAMPLIST" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLIST") echo "selected";?>>DNC & CAMPLIST</option>
																	<option value="DNC_AND_CAMPLIST_ALL" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLIST_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Available Only Tally:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="available_only_ratio_tally" name="available_only_ratio_tally">
																	<option value="N" <?php if($campaign->data->available_only_ratio_tally == 'N') echo "selected";?>>NO</option>
																	<option value="Y" <?php if($campaign->data->available_only_ratio_tally == 'Y') echo "selected";?>>YES</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Campaign Recording Filename:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Next Agent Call:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="next_agent_call" name="next_agent_call">
																	<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																	<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																	<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																	<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																	<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																	<option value="LONGEST_WAITING_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAITING_TIME") echo "selected";?>>LONGEST WAITING TIME</option>
																</select>
															</div>
														</div>
														<?php if($campaign->data->dial_method == "INBOUND_MAN") { ?>
															<div class="form-group">
																<label class="col-sm-3 control-label">Get Call Launch:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="get_call_launch" name="get_call_launch">
																		<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																		<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																		<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Dial timeout:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Manual Dial Prefix:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="manual_dial_prefix" name="manual_dial_prefix" value="<?php echo $campaign->data->manual_dial_prefix; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Caller ID for 3-way Calls:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="three_way_call_cid" name="three_way_call_cid">
																		<option value="CUSTOM" <?php if($campaign->data->three_way_call_cid == "CUSTOM") echo "selected";?>>CUSTOM</option>
																		<option value="CAMPAIGN" <?php if($campaign->data->three_way_call_cid == "CAMPAIGN") echo "selected";?>>CAMPAIGN</option>
																		<option value="CUSTOMER" <?php if($campaign->data->three_way_call_cid == "CUSTOMER") echo "selected";?>>CUSTOMER</option>
																		<option value="AGENT_PHONE" <?php if($campaign->data->three_way_call_cid == "AGENT_PHONE") echo "selected";?>>AGENT PHONE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Dial Prefix for 3-way Calls:</label>
																<div class="col-sm-9 mb">
																	<?php
																	// old condition
																	// if(!empty($campaign->data->three_way_dial_prefix)){echo $campaign->data->three_way_dial_prefix;}else{echo "88";}
																	?>
																	<input type="text" class="form-control" value="<?php echo $campaign->data->three_way_dial_prefix; ?>" id="three_way_dial_prefix" name="three_way_dial_prefix">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Customer 3-way Hangup Logging:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="customer_3way_hangup_logging" name="customer_3way_hangup_logging">
																		<option value="ENABLED" <?php if($campaign->data->customer_3way_hangup_logging == "ENABLED") echo "selected";?>>ENABLED</option>
																		<option value="DISABLED" <?php if($campaign->data->customer_3way_hangup_logging == "DISABLED") echo "selected";?>>DISABLED</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Customer 3-way Hangup Seconds:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="customer_3way_hangup_seconds" name="customer_3way_hangup_seconds" min="0" value="<?php if(!empty($campaign->data->customer_3way_hangup_seconds)){echo $campaign->data->customer_3way_hangup_seconds;}else{echo "5";} ?>" onkeydown="return FilterInput(event)">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Customer 3-way Hangup Action:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="customer_3way_hangup_action" name="customer_3way_hangup_action">
																		<option value="DISPO" <?php if($campaign->data->customer_3way_hangup_action == "DISPO") echo "selected";?>>DISPO</option>
																		<option value="NONE"> <?php if($campaign->data->customer_3way_hangup_action == "NONE") echo "selected";?></option>
																	</select>
																</div>
															</div>
														<?php } ?>
													<?php } elseif($campaign->campaign_type == "BLENDED") { ?>
														<div class="form-group">
															<label class="col-sm-3 control-label">Get Call Launch:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="get_call_launch" name="get_call_launch">
																	<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-2 control-label">Call Time:</label>
															<div class="col-sm-10 mb">
																<select class="form-control" id="local_call_time" name="local_call_time">
																	<?php for($i=0;$i<=count($calltimes->call_time_id);$i++) { ?>
																		<?php if(!empty($calltimes->call_time_id[$i])) { ?>
																			<option value="<?php echo $calltimes->call_time_id[$i]; ?>"<?php if($campaign->data->local_call_time == $calltimes->call_time_id[$i]) echo "selected"; ?>><?php echo $calltimes->call_time_name[$i]; ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group" style="margin-bottom: 10px;">
															<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=1;?>
															<?php foreach($dial_statuses as $dial_status) { ?>
																<?php if(!empty($dial_status)) { ?>
																	<label class="col-sm-3 control-label">Active Dial Status <?php echo $i; ?>:</label>
																	<span class="col-sm-8 control-label" style="text-align: left;">
																		<label><?php echo $dial_status; ?></label> - <span><?php $lh->translateText($dial_status); ?></span>
																	</span>
																	<span class="col-sm-1 control-label">
																		<a href="#" class="remove-this-dial-status"  data-campaign="<?php echo $campaign_id; ?>" data-dial-status="<?php echo $campaign->data->dial_statuses;?>" data-selected-status="<?php echo $dial_status; ?>">Remove</a>
																	</span>
																	<?php $i++; ?>
																<?php } ?>
															<?php } ?>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Status:</label>
															<div class="col-sm-8 mb">
																<select class="form-control" id="dial_status" name="dial_status">
																	<option value="" selected>NONE</option>
																	<optgroup label="System Statuses">
																	<?php for($i=0;$i<=count($dialStatus->status);$i++) { ?>
																		<?php if( !empty($dialStatus->status[$i]) && !in_array($dialStatus->status[$i], $dial_statuses) ){ ?>
																			<option value="<?php echo $dialStatus->status[$i]?>">
																				<?php echo $dialStatus->status[$i]." - ".$dialStatus->status_name[$i]?>
																			</option>
																		<?php } ?>
																	<?php } ?>
																	</optgroup>
																	<?php if(count($campdialStatus->status) > 0){ ?>
																		<optgroup label="Campaign Statuses">
																		<?php for($i=0;$i<=count($campdialStatus->status);$i++) { ?>
																			<?php if( !empty($campdialStatus->status[$i])  && !in_array($campdialStatus->status[$i], $dial_statuses) ){ ?>
																				<option value="<?php echo $campdialStatus->status[$i]?>">
																					<?php echo $campdialStatus->status[$i]." - ".$campdialStatus->status_name[$i]?>
																				</option>
																			<?php } ?>
																		<?php } ?>
																		</optgroup>
																	<?php } ?>
																</select>
															</div>
															<div class="col-sm-1 mb">
																<button type="button" class="btn btn-default btn-add-dial-status" data-campaign="<?php echo $campaign_id; ?>" data-dial-status="<?php echo $campaign->data->dial_statuses;?>">Add</button>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">List Order:</label>
															<div class="col-sm-9 mb">
																	<select size="1" name="lead_order" id="lead_order" class="form-control">
														            <option value="DOWN" <?php if($campaign->data->lead_order == "DOWN") echo "selected"; ?>>DOWN</option>
														            <option value="UP" <?php if($campaign->data->lead_order == "UP") echo "selected"; ?>>UP</option>
														            <option value="DOWN_PHONE" <?php if($campaign->data->lead_order == "DOWN_PHONE") echo "selected"; ?>>DOWN PHONE</option>
														            <option value="UP_PHONE" <?php if($campaign->data->lead_order == "UP_PHONE") echo "selected"; ?>>UP PHONE</option>
														            <option value="DOWN_LAST_NAME" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME") echo "selected"; ?>>DOWN LAST NAME</option>
														            <option value="UP_LAST_NAME" <?php if($campaign->data->lead_order == "UP_LAST_NAME") echo "selected"; ?>>UP LAST NAME</option>
														            <option value="DOWN_COUNT" <?php if($campaign->data->lead_order == "DOWN_COUNT") echo "selected"; ?>>DOWN COUNT</option>
														            <option value="UP_COUNT" <?php if($campaign->data->lead_order == "UP_COUNT") echo "selected"; ?>>UP COUNT</option>
														            <option value="RANDOM" <?php if($campaign->data->lead_order == "RANDOM") echo "selected"; ?>>RANDOM</option>
														            <option value="DOWN_LAST_CALL_TIME" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME") echo "selected"; ?>>DOWN LAST CALL TIME</option>
														            <option value="UP_LAST_CALL_TIME" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME") echo "selected"; ?>>UP LAST CALL TIME</option>
														            <option value="DOWN_RANK" <?php if($campaign->data->lead_order == "DOWN_RANK") echo "selected"; ?>>DOWN RANK</option>
														            <option value="UP_RANK" <?php if($campaign->data->lead_order == "UP_RANK") echo "selected"; ?>>UP RANK</option>
														            <option value="DOWN_OWNER" <?php if($campaign->data->lead_order == "DOWN_OWNER") echo "selected"; ?>>DOWN OWNER</option>
														            <option value="UP_OWNER" <?php if($campaign->data->lead_order == "UP_OWNER") echo "selected"; ?>>UP OWNER</option>
														            <option value="DOWN_TIMEZONE" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE") echo "selected"; ?>>DOWN TIMEZONE</option>
														            <option value="UP_TIMEZONE" <?php if($campaign->data->lead_order == "UP_TIMEZONE") echo "selected"; ?>>UP TIMEZONE</option>
														            <option value="DOWN_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_2nd_NEW") echo "selected"; ?>>DOWN 2nd NEW</option>
														            <option value="DOWN_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_3rd_NEW") echo "selected"; ?>>DOWN 3rd NEW</option>
														            <option value="DOWN_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_4th_NEW") echo "selected"; ?>>DOWN 4th NEW</option>
														            <option value="DOWN_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_5th_NEW") echo "selected"; ?>>DOWN 5th NEW</option>
														            <option value="DOWN_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_6th_NEW") echo "selected"; ?>>DOWN 6th NEW</option>
														            <option value="UP_2nd_NEW" <?php if($campaign->data->lead_order == "UP_2nd_NEW") echo "selected"; ?>>UP 2nd NEW</option>
														            <option value="UP_3rd_NEW" <?php if($campaign->data->lead_order == "UP_3rd_NEW") echo "selected"; ?>>UP 3rd NEW</option>
														            <option value="UP_4th_NEW" <?php if($campaign->data->lead_order == "UP_4th_NEW") echo "selected"; ?>>UP 4th NEW</option>
														            <option value="UP_5th_NEW" <?php if($campaign->data->lead_order == "UP_5th_NEW") echo "selected"; ?>>UP 5th NEW</option>
														            <option value="UP_6th_NEW" <?php if($campaign->data->lead_order == "UP_6th_NEW") echo "selected"; ?>>UP 6th NEW</option>
														            <option value="DOWN_PHONE_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_2nd_NEW") echo "selected"; ?>>DOWN PHONE 2nd NEW</option>
														            <option value="DOWN_PHONE_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_3rd_NEW") echo "selected"; ?>>DOWN PHONE 3rd NEW</option>
														            <option value="DOWN_PHONE_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_4th_NEW") echo "selected"; ?>>DOWN PHONE 4th NEW</option>
														            <option value="DOWN_PHONE_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_5th_NEW") echo "selected"; ?>>DOWN PHONE 5th NEW</option>
														            <option value="DOWN_PHONE_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_PHONE_6th_NEW") echo "selected"; ?>>DOWN PHONE 6th NEW</option>
														            <option value="UP_PHONE_2nd_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_2nd_NEW") echo "selected"; ?>>UP PHONE 2nd NEW</option>
														            <option value="UP_PHONE_3rd_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_3rd_NEW") echo "selected"; ?>>UP PHONE 3rd NEW</option>
														            <option value="UP_PHONE_4th_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_4th_NEW") echo "selected"; ?>>UP PHONE 4th NEW</option>
														            <option value="UP_PHONE_5th_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_5th_NEW") echo "selected"; ?>>UP PHONE 5th NEW</option>
														            <option value="UP_PHONE_6th_NEW" <?php if($campaign->data->lead_order == "UP_PHONE_6th_NEW") echo "selected"; ?>>UP PHONE 6th NEW</option>
														            <option value="DOWN_LAST_NAME_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_2nd_NEW") echo "selected"; ?>>DOWN LAST NAME 2nd NEW</option>
														            <option value="DOWN_LAST_NAME_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_3rd_NEW") echo "selected"; ?>>DOWN LAST NAME 3rd NEW</option>
														            <option value="DOWN_LAST_NAME_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_4th_NEW") echo "selected"; ?>>DOWN LAST NAME 4th NEW</option>
														            <option value="DOWN_LAST_NAME_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_5th_NEW") echo "selected"; ?>>DOWN LAST NAME 5th NEW</option>
														            <option value="DOWN_LAST_NAME_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_NAME_6th_NEW") echo "selected"; ?>>DOWN LAST NAME 6th NEW</option>
														            <option value="UP_LAST_NAME_2nd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_2nd_NEW") echo "selected"; ?>>UP LAST NAME 2nd NEW</option>
														            <option value="UP_LAST_NAME_3rd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_3rd_NEW") echo "selected"; ?>>UP LAST NAME 3rd NEW</option>
														            <option value="UP_LAST_NAME_4th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_4th_NEW") echo "selected"; ?>>UP LAST NAME 4th NEW</option>
														            <option value="UP_LAST_NAME_5th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_5th_NEW") echo "selected"; ?>>UP LAST NAME 5th NEW</option>
														            <option value="UP_LAST_NAME_6th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_NAME_6th_NEW") echo "selected"; ?>>UP LAST NAME 6th NEW</option>
														            <option value="DOWN_COUNT_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_2nd_NEW") echo "selected"; ?>>DOWN COUNT 2nd NEW</option>
														            <option value="DOWN_COUNT_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_3rd_NEW") echo "selected"; ?>>DOWN COUNT 3rd NEW</option>
														            <option value="DOWN_COUNT_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_4th_NEW") echo "selected"; ?>>DOWN COUNT 4th NEW</option>
														            <option value="DOWN_COUNT_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_5th_NEW") echo "selected"; ?>>DOWN COUNT 5th NEW</option>
														            <option value="DOWN_COUNT_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_COUNT_6th_NEW") echo "selected"; ?>>DOWN COUNT 6th NEW</option>
														            <option value="UP_COUNT_2nd_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_2nd_NEW") echo "selected"; ?>>UP COUNT 2nd NEW</option>
														            <option value="UP_COUNT_3rd_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_3rd_NEW") echo "selected"; ?>>UP COUNT 3rd NEW</option>
														            <option value="UP_COUNT_4th_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_4th_NEW") echo "selected"; ?>>UP COUNT 4th NEW</option>
														            <option value="UP_COUNT_5th_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_5th_NEW") echo "selected"; ?>>UP COUNT 5th NEW</option>
														            <option value="UP_COUNT_6th_NEW" <?php if($campaign->data->lead_order == "UP_COUNT_6th_NEW") echo "selected"; ?>>UP COUNT 6th NEW</option>
														            <option value="RANDOM_2nd_NEW" <?php if($campaign->data->lead_order == "RANDOM_2nd_NEW") echo "selected"; ?>>RANDOM 2nd NEW</option>
														            <option value="RANDOM_3rd_NEW" <?php if($campaign->data->lead_order == "RANDOM_3rd_NEW") echo "selected"; ?>>RANDOM 3rd NEW</option>
														            <option value="RANDOM_4th_NEW" <?php if($campaign->data->lead_order == "RANDOM_4th_NEW") echo "selected"; ?>>RANDOM 4th NEW</option>
														            <option value="RANDOM_5th_NEW" <?php if($campaign->data->lead_order == "RANDOM_5th_NEW") echo "selected"; ?>>RANDOM 5th NEW</option>
														            <option value="RANDOM_6th_NEW" <?php if($campaign->data->lead_order == "RANDOM_6th_NEW") echo "selected"; ?>>RANDOM 6th NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 2nd NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 3rd NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 4th NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 5th NEW</option>
														            <option value="DOWN_LAST_CALL_TIME_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 6th NEW</option>
														            <option value="UP_LAST_CALL_TIME_2nd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>UP LAST CALL TIME 2nd NEW</option>
														            <option value="UP_LAST_CALL_TIME_3rd_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>UP LAST CALL TIME 3rd NEW</option>
														            <option value="UP_LAST_CALL_TIME_4th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>UP LAST CALL TIME 4th NEW</option>
														            <option value="UP_LAST_CALL_TIME_5th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>UP LAST CALL TIME 5th NEW</option>
														            <option value="UP_LAST_CALL_TIME_6th_NEW" <?php if($campaign->data->lead_order == "UP_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>UP LAST CALL TIME 6th NEW</option>
														            <option value="DOWN_RANK_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_2nd_NEW") echo "selected"; ?>>DOWN RANK 2nd NEW</option>
														            <option value="DOWN_RANK_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_3rd_NEW") echo "selected"; ?>>DOWN RANK 3rd NEW</option>
														            <option value="DOWN_RANK_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_4th_NEW") echo "selected"; ?>>DOWN RANK 4th NEW</option>
														            <option value="DOWN_RANK_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_5th_NEW") echo "selected"; ?>>DOWN RANK 5th NEW</option>
														            <option value="DOWN_RANK_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_RANK_6th_NEW") echo "selected"; ?>>DOWN RANK 6th NEW</option>
														            <option value="UP_RANK_2nd_NEW" <?php if($campaign->data->lead_order == "UP_RANK_2nd_NEW") echo "selected"; ?>>UP RANK 2nd NEW</option>
														            <option value="UP_RANK_3rd_NEW" <?php if($campaign->data->lead_order == "UP_RANK_3rd_NEW") echo "selected"; ?>>UP RANK 3rd NEW</option>
														            <option value="UP_RANK_4th_NEW" <?php if($campaign->data->lead_order == "UP_RANK_4th_NEW") echo "selected"; ?>>UP RANK 4th NEW</option>
														            <option value="UP_RANK_5th_NEW" <?php if($campaign->data->lead_order == "UP_RANK_5th_NEW") echo "selected"; ?>>UP RANK 5th NEW</option>
														            <option value="UP_RANK_6th_NEW" <?php if($campaign->data->lead_order == "UP_RANK_6th_NEW") echo "selected"; ?>>UP RANK 6th NEW</option>
														            <option value="DOWN_OWNER_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_2nd_NEW") echo "selected"; ?>>DOWN OWNER 2nd NEW</option>
														            <option value="DOWN_OWNER_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_3rd_NEW") echo "selected"; ?>>DOWN OWNER 3rd NEW</option>
														            <option value="DOWN_OWNER_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_4th_NEW") echo "selected"; ?>>DOWN OWNER 4th NEW</option>
														            <option value="DOWN_OWNER_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_5th_NEW") echo "selected"; ?>>DOWN OWNER 5th NEW</option>
														            <option value="DOWN_OWNER_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_OWNER_6th_NEW") echo "selected"; ?>>DOWN OWNER 6th NEW</option>
														            <option value="UP_OWNER_2nd_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_2nd_NEW") echo "selected"; ?>>UP OWNER 2nd NEW</option>
														            <option value="UP_OWNER_3rd_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_3rd_NEW") echo "selected"; ?>>UP OWNER 3rd NEW</option>
														            <option value="UP_OWNER_4th_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_4th_NEW") echo "selected"; ?>>UP OWNER 4th NEW</option>
														            <option value="UP_OWNER_5th_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_5th_NEW") echo "selected"; ?>>UP OWNER 5th NEW</option>
														            <option value="UP_OWNER_6th_NEW" <?php if($campaign->data->lead_order == "UP_OWNER_6th_NEW") echo "selected"; ?>>UP OWNER 6th NEW</option>
														            <option value="DOWN_TIMEZONE_2nd_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_2nd_NEW") echo "selected"; ?>>DOWN TIMEZONE 2nd NEW</option>
														            <option value="DOWN_TIMEZONE_3rd_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_3rd_NEW") echo "selected"; ?>>DOWN TIMEZONE 3rd NEW</option>
														            <option value="DOWN_TIMEZONE_4th_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_4th_NEW") echo "selected"; ?>>DOWN TIMEZONE 4th NEW</option>
														            <option value="DOWN_TIMEZONE_5th_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_5th_NEW") echo "selected"; ?>>DOWN TIMEZONE 5th NEW</option>
														            <option value="DOWN_TIMEZONE_6th_NEW" <?php if($campaign->data->lead_order == "DOWN_TIMEZONE_6th_NEW") echo "selected"; ?>>DOWN TIMEZONE 6th NEW</option>
														            <option value="UP_TIMEZONE_2nd_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_2nd_NEW") echo "selected"; ?>>UP TIMEZONE 2nd NEW</option>
														            <option value="UP_TIMEZONE_3rd_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_3rd_NEW") echo "selected"; ?>>UP TIMEZONE 3rd NEW</option>
														            <option value="UP_TIMEZONE_4th_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_4th_NEW") echo "selected"; ?>>UP TIMEZONE 4th NEW</option>
														            <option value="UP_TIMEZONE_5th_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_5th_NEW") echo "selected"; ?>>UP TIMEZONE 5th NEW</option>
														            <option value="UP_TIMEZONE_6TH_NEW" <?php if($campaign->data->lead_order == "UP_TIMEZONE_6TH_NEW") echo "selected"; ?>>UP TIMEZONE 6th NEW</option>
														        </select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Lead Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="lead_filter" name="lead_filter">
																	<option value="" <?php if($campaign->data->lead_filter_id == "") echo "selected";?>>NONE</option>
																	<?php for($i=0;$i<=count($leadfilter->lead_filter_id);$i++) { ?>
																		<?php if(!empty($leadfilter->lead_filter_id[$i])) { ?>
																			<option value="<?php echo $leadfilter->lead_filter_id[$i]; ?>" <?php if($campaign->data->lead_filter_id == $leadfilter->lead_filter_id[$i]) echo "selected";?>><?php echo $leadfilter->lead_filter_name[$i]; ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-2 control-label">Reset Leads on Hopper:</label>
															<div class="col-sm-10 mb">
																<select class="form-control" id="force_reset_hopper" name="force_reset_hopper">
																	<option value="Y">Y</option>
																	<option value="N" selected>N</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial timeout:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Prefix:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="manual_dial_prefix" name="manual_dial_prefix" value="<?php echo $campaign->data->manual_dial_prefix; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Get Call Launch:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="get_call_launch" name="get_call_launch">
																	<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Answering Machine Message:</label>
															<div class="col-sm-9 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="am_message_exten" name="am_message_exten" value="<?php echo $campaign->data->am_message_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_am_message_chooser" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<select class="form-control am_message_chooser" id="am_message_chooser" name="am_message_chooser">
																	<option value="">-- Default Value --</option>
																	<?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
																		<?php if(!empty($voicefiles->file_name[$i])) { ?>
																			<option value="<?php echo substr($voicefiles->file_name[$i], 0, -4); ?>"><?php echo substr($voicefiles->file_name[$i], 0, -4); ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">AMD send to Action:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																	<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">WaitForSilence Options:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Pause Codes:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																	<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																	<option value="FORCE" <?php if($campaign->data->agent_pause_codes_active == "FORCE") echo "selected";?>>FORCE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																	<option value="NONE" <?php if($campaign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																	<option value="DNC_ONLY" <?php if($campaign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																	<option value="CAMPLIST_ONLY" <?php if($campaign->data->manual_dial_filter == "CAMPLIST_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																	<option value="DNC_AND_CAMPLIST" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLIST") echo "selected";?>>DNC & CAMPLIST</option>
																	<option value="DNC_AND_CAMPLIST_ALL" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLIST_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Available Only Tally:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="available_only_ratio_tally" name="available_only_ratio_tally">
																	<option value="N" <?php if($campaign->data->available_only_ratio_tally == 'N') echo "selected";?>>NO</option>
																	<option value="Y" <?php if($campaign->data->available_only_ratio_tally == 'Y') echo "selected";?>>YES</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Campaign Recording Filename:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Next Agent Call:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="next_agent_call" name="next_agent_call">
																	<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																	<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																	<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																	<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																	<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																	<option value="LONGEST_WAITING_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAITING_TIME") echo "selected";?>>LONGEST WAITING TIME</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Caller ID for 3-way Calls:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="three_way_call_cid" name="three_way_call_cid">
																	<option value="CUSTOM" <?php if($campaign->data->three_way_call_cid == "CUSTOM") echo "selected";?>>CUSTOM</option>
																	<option value="CAMPAIGN" <?php if($campaign->data->three_way_call_cid == "CAMPAIGN") echo "selected";?>>CAMPAIGN</option>
																	<option value="CUSTOMER" <?php if($campaign->data->three_way_call_cid == "CUSTOMER") echo "selected";?>>CUSTOMER</option>
																	<option value="AGENT_PHONE" <?php if($campaign->data->three_way_call_cid == "AGENT_PHONE") echo "selected";?>>AGENT PHONE</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Prefix for 3-way Calls:</label>
															<div class="col-sm-9 mb">
																	<?php
																	// old condition
																	// if(!empty($campaign->data->three_way_dial_prefix)){echo $campaign->data->three_way_dial_prefix;}else{echo "88";}
																	?>
																<input type="text" class="form-control" value="<?php echo $campaign->data->three_way_dial_prefix; ?>" id="three_way_dial_prefix" name="three_way_dial_prefix">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Customer 3-way Hangup Logging:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="customer_3way_hangup_logging" name="customer_3way_hangup_logging">
																	<option value="ENABLED" <?php if($campaign->data->customer_3way_hangup_logging == "ENABLED") echo "selected";?>>ENABLED</option>
																	<option value="DISABLED" <?php if($campaign->data->customer_3way_hangup_logging == "DISABLED") echo "selected";?>>DISABLED</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Customer 3-way Hangup Seconds:</label>
															<div class="col-sm-9 mb">
																<input type="number" class="form-control" id="customer_3way_hangup_seconds" name="customer_3way_hangup_seconds" min="0" value="<?php if(!empty($campaign->data->customer_3way_hangup_seconds)){echo $campaign->data->customer_3way_hangup_seconds;}else{echo "5";} ?>" onkeydown="return FilterInput(event)">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Customer 3-way Hangup Action:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="customer_3way_hangup_action" name="customer_3way_hangup_action">
																	<option value="DISPO" <?php if($campaign->data->customer_3way_hangup_action == "DISPO") echo "selected";?>>DISPO</option>
																	<option value="NONE" <?php if($campaign->data->customer_3way_hangup_action == "NONE") echo "selected";?>>NONE</option>
																</select>
															</div>
														</div>
													<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
														<?php if($campaign->data->campaign_vdad_exten != 8373) { ?>
															<div class="form-group">
																<label class="col-sm-3 control-label">Survey DTMF Digits:</label>
																<div class="col-sm-5 mb">
																	<input type="number" class="form-control" id="survey_dtmf_digits" name="survey_dtmf_digits" min="0" value="<?php echo $campaign->data->survey_dtmf_digits; ?>">
																</div>
																<span class="col-sm-4 control-label">* Customer define key press e.g.0123456789*#</span>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">DID:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="survey_xfer_exten" name="survey_xfer_exten" min="0" value="<?php echo $campaign->data->survey_xfer_exten; ?>">
																</div>
															</div>
															<br />
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 8 Not interested digit:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="survey_ni_digit" name="survey_ni_digit" min="0" maxlength="10" value="<?php echo $campaign->data->survey_ni_digit; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 8 Not interested audio file:</label>
																<div class="col-sm-7 mb">
																	<input type="text" class="form-control" id="survey_ni_audio_file" name="survey_ni_audio_file" value="<?php echo $campaign->data->survey_ni_audio_file; ?>">
																</div>
																<div class="col-sm-2 mb">
																	<button type="button" class="view-audio-files btn btn-default" data-label="survey_ni_audio_file">Audio</button>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 8 Not interested status:</label>
																<div class="col-sm-9 mb">
																	<select id="survey_ni_status" name="survey_ni_status" class="form-control">
																		<option value="NI" <?php if($campaign->data->survey_ni_status == "NI") echo "selected";?>>NI - Not Interested</option>
																		<option value="DNC" <?php if($campaign->data->survey_ni_status == "DNC") echo "selected";?>>DNC - Do Not Call</option>
																	</select>
																</div>
															</div>
															<br />
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 3 digit:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="survey_third_digit" name="survey_third_digit" min="0" maxlength="10" value="<?php echo $campaign->data->survey_third_digit; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 3 Audio File:</label>
																<div class="col-sm-7 mb">
																	<input type="text" class="form-control" id="survey_third_audio_file" name="survey_third_audio_file" value="<?php echo $campaign->data->survey_third_audio_file; ?>">
																</div>
																<div class="col-sm-2 mb">
																	<button type="button" class="view-audio-files btn btn-default" data-label="survey_third_audio_file">Audio</button>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 3 Status:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="survey_third_status" name="survey_third_status" value="<?php echo $campaign->data->survey_third_status; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 3 DID:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="survey_third_exten" name="survey_third_exten" min="0" value="<?php echo $campaign->data->survey_third_exten; ?>">
																</div>
															</div>
															<br />
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 4 Digit:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="survey_fourth_digit" maxlength="10" name="survey_fourth_digit" min="0" value="<?php echo $campaign->data->survey_fourth_digit; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 4 Audio File:</label>
																<div class="col-sm-7 mb">
																	<input type="text" class="form-control" id="survey_fourth_audio_file" name="survey_fourth_audio_file" value="<?php echo $campaign->data->survey_fourth_audio_file; ?>">
																</div>
																<div class="col-sm-2 mb">
																	<button type="button" class="view-audio-files btn btn-default" data-label="survey_fourth_audio_file">Audio</button>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 4 Status:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="survey_fourth_status" name="survey_fourth_status" value="<?php echo $campaign->data->survey_fourth_status; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Press 4 DID:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="survey_fourth_exten" name="survey_fourth_exten" min="0" value="<?php echo $campaign->data->survey_fourth_exten; ?>">
																</div>
															</div>
														<?php } ?>
													<?php } else { ?>
														<!--Default-->
													<?php } ?>
													<div class="campaign_allow_inbound_div hide">
														<div class="form-group">
															<label class="col-sm-3 control-label">Inbound Groups:</label>
															<div class="col-sm-9 mb">
																<?php for($i=0;$i<=count($ingroups->group_id);$i++) { ?>
																	<?php if(!empty($ingroups->group_id[$i])) {?>
																		<input type="checkbox" name="closer_campaigns[]" value="<?php echo $ingroups->group_id[$i]?>" <?php if(in_array($ingroups->group_id[$i],explode(" ", $campaign->data->closer_campaigns))) echo "checked";?>>&nbsp;<?php echo $ingroups->group_id[$i]." - ".$ingroups->group_name[$i]; ?><br />
																	<?php } ?>
																<?php } ?>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Allowed transfer groups:</label>
															<div class="col-sm-9 mb">
																<?php for($i=0;$i<=count($ingroups->group_id);$i++) { ?>
																	<?php if(!empty($ingroups->group_id[$i])) {?>
																		<input type="checkbox" name="xfer_groups[]" value="<?php echo $ingroups->group_id[$i]?>"<?php if(in_array($ingroups->group_id[$i],explode(" ", $campaign->data->xfer_groups))) echo "checked";?>>&nbsp;<?php echo $ingroups->group_id[$i]." - ".$ingroups->group_name[$i]; ?><br />
																	<?php } ?>
																<?php } ?>
															</div>
														</div>
													</div>
												</fieldset>
											</div>
											<!-- /.tab-pane -->

											<!-- Notification -->
										   	<div id="modifyUSERresult"></div>

										   	<!-- FOOTER BUTTONS -->
						                    <fieldset class="footer-buttons">
						                        <div class="box-footer">
						                           <div class="col-sm-3 pull-right">
															<a href="telephonycampaigns.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>

						                                	<button type="submit" class="btn btn-primary" id="modifyUserOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
						                           </div>
						                        </div>
						                    </fieldset>

										</div>
										<!-- /.tab-content -->
								</div>
								<!-- /.tab-panel -->
							</div>
							<!-- /.panel-body -->
								<?php } else {  ?>
								 	<?php echo $campaign->result; ?>
								<?php } ?>
							<?php } ?>

							<?php
							// ---- IF DID
							if($did != NULL){
								//var_dump($disposition->result);

								//var_dump($did);
								if ($disposition->result == "success") {
							?>

					            <!-- /.box-header -->
					            <div class="box-body table-responsive no-padding">
					              <table class="table table-hover">
					              	<thead>
					              		<style>
					              		.head_custom_statuses{
					              			font-size: 14px;
										    text-align: center;
										    padding: 0px 20px;
										}
										input[type="text"]{
										    font-size: 15px;
    										padding-left: 10px;
										}
										.custom_statuses{
										    text-align: center;
										    pointer-events: none;
										}
										.add_custom_statuses{
										    text-align: center;
										}
					              		</style>
					                <tr>
					                	<th> STATUS </th>
										<th> STATUS NAME </th>
										<th class="head_custom_statuses"> Selectable </th>
										<th class="head_custom_statuses"> Human Answered </th>
										<th class="head_custom_statuses"> Sale </th>
										<th class="head_custom_statuses"> DNC </th>
										<th class="head_custom_statuses"> Customer Contact </th>
										<th class="head_custom_statuses"> Not Interested </th>
										<th class="head_custom_statuses"> Unworkable </th>
										<th class="head_custom_statuses"> Scheduled Callback </th>
										<th class="head_custom_statuses"> Action </th>
					                </tr>
					            	</thead>
					                <tbody>
								<?php
										for($i=0;$i < count($disposition->campaign_id);$i++){
								?>
									<tr>
										<td>
											<?php echo $disposition->status[$i];?>
										</td>
										<td>
											<?php echo $disposition->status_name[$i];?>
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->selectable[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->human_answered[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->sale[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->dnc[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" id="customer_contact" class="flat-red" <?php if($disposition->customer_contact[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->not_interested[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->unworkable[$i] == "Y"){echo checked;}?> />
										</td>
										<td class="custom_statuses">
											<input type="checkbox" class="flat-red" <?php if($disposition->scheduled_callback[$i] == "Y"){echo checked;}?> />
										</td>
									<!-- ACTION BUTTONS -->
										<td><center>
											<a class="edit_disposition btn btn-primary" href="#" data-toggle="modal" data-target="#edit_disposition_modal" data-id="<?php echo $disposition->campaign_id[$i];?>" data-status ="<?php echo $disposition->status[$i];?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
											<a class="delete_disposition btn btn-danger" href="#" data-id="<?php echo $disposition->campaign_id[$i];?>" data-status ="<?php echo $disposition->status[$i];?>" data-name="<?php echo $disposition->status_name[$i];?>"><i class="fa fa-trash"></i></a>
											</center>
										</td>
									</tr>

								<?php
										}
								?>
								<!-- ADD A NEW STATUS -->
									<tr><td colspan="11" style="background: #ecf0f5;">&nbsp;</td></tr>
									<tr style="border-top: 1px solid #f4f4f4;">
										<td>
											<input type="text" name="add_status" id="add_status" class="" placeholder="Status" maxlength="6">
											<br/><small><label id="status-duplicate-error"></label></small>
										</td>
										<td>
											<input type="text" name="add_status_name" id="add_status_name" class="" placeholder="Status Name">
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_selectable" id="add_selectable" class="flat-red" value="Y" checked />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_human_answered" id="add_human_answered" class="flat-red" value="Y" />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_sale" id="add_sale" class="flat-red" value="Y" />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_dnc" id="add_dnc" class="flat-red" value="Y" />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_customer_contact" id="add_customer_contact" class="flat-red" value="Y" />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_not_interested" id="add_not_interested" class="flat-red" value="Y" />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_unworkable" id="add_unworkable" class="flat-red" value="Y" />
										</td>
										<td class="add_custom_statuses">
											<input type="checkbox" name="add_scheduled_callback" id="add_scheduled_callback" class="flat-red" value="Y" />
										</td>
										<td>
											<a type="button" id="add_new_status" data-id="<?php echo $did;?>" class="btn btn-primary" disabled><span id="add_button"><i class="fa fa-plus"></i> New Status</span></a>
										</td>
									</tr>
								<!------>

									</tbody>
					              </table>

					              	<div class="box-footer pull-right">
										<a href="#" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-remove"></i> Cancel</a>
									</div>
					            </div>


								<!-- /.box-footer -->
							<?php
								} else {
							?>
								 <script>
								    $(function(){
								        empty_statuses();
								    });
								 </script>
							<?php

								}

							}
							/*
					// ---- IF LEADFILTER
							if($lf_id != NULL){
								echo "Under Construction";
							}else {
									echo $errormessage = $lh->translationFor("some_fields_missing");
							}
								*/
							?>


						</form>

					</div>
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>

        </div><!-- ./wrapper -->

    <!-- EDIT DISPOSITION MODAL -->
    <div id="edit_disposition_modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title animate-header" id="ingroup_modal"><b>Modify Status <span id="status_id_edit"></span> in » Campaign <span id="campaign_id_edit"></span></b></h4>
                </div>
                <div class="modal-body" style="background:#fff;">
                	<form id="modifydisposition_form">
                		<div class="form-group mt mb">
                			<div class="row">&nbsp;</div>
                		</div>
	                	<div class="form-group mt">
		                	<input type="hidden" name="edit_campaign" id="edit_campaign">

	                		<label class="col-sm-3 control-label" for="status">Status:</label>
	                        <div class="col-sm-9">
	                            <input type="text" name="edit_status" id="edit_status" class="form-control" placeholder="Status" minlength="1" maxlenght="6" required readonly>
	                            <br/><small><label id="status-duplicate-error"></label></small>
	                    	</div>
		                </div>
		                <div class="form-group">
		                	<label class="col-sm-3 control-label" for="status_name"> Status Name: </label>
	                        <div class="col-sm-9 mb">
	                            <input type="text" name="edit_status_name" id="edit_status_name" class="form-control" placeholder="Status Name" maxlenght="30" required>
	                        </div>
		                </div>
		                <div class="form-group">
				                        <!--<label class="col-sm-2 control-label" for="grouplevel" style="padding-top:15px;"> </label>-->
		                    <div class="col-lg-1">
		                   	</div>
		                    <div class="col-lg-11 mt">
		                    	<div class="row mb">
		                    		<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_selectable">
										<input type="checkbox" id="edit_selectable" name="edit_selectable" checked>
										<span class="fa fa-check"></span> Selectable
									</label>
									<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_human_answered">
										<input type="checkbox" id="edit_human_answered" name="edit_human_answered">
										<span class="fa fa-check"></span> Human Answered
									</label>
									<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_sale">
										<input type="checkbox" id="edit_sale" name="edit_sale">
										<span class="fa fa-check"></span> Sale
									</label>
						        </div>
						        <div class="row mb">
						        	<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_dnc">
										<input type="checkbox" id="edit_dnc" name="edit_dnc">
										<span class="fa fa-check"></span> DNC
									</label>
									<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_customer_contact">
										<input type="checkbox" id="edit_customer_contact" name="edit_customer_contact">
										<span class="fa fa-check"></span> Customer Contact
									</label>
									<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_not_interested">
										<input type="checkbox" id="edit_not_interested" name="edit_not_interested">
										<span class="fa fa-check"></span> Not Interested
									</label>
					            </div>
						        <div class="row mb">
						        	<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_unworkable">
										<input type="checkbox" id="edit_unworkable" name="edit_unworkable">
										<span class="fa fa-check"></span> Unworkable
									</label>
									<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_scheduled_callback">
										<input type="checkbox" id="edit_scheduled_callback" name="edit_scheduled_callback">
										<span class="fa fa-check"></span> Scheduled Callback
									</label>
					            </div>
		                    </div>
	                    </div>
	            	</form>
                </div>
                <div class="modal-footer">
                	<div class="col-sm-5 pull-right">
                		<button type="button" class="btn btn-danger" id="cancel_edit" data-dismiss="modal"><i class='fa fa-remove'></i> Cancel</button>
                    	<button type="button" class="btn btn-primary" id="modify_disposition"><span id="update_button"><i class='fa fa-check'></i> Update</span></button>
              		</div>
              	</div>

            </div>
        </div>
    </div>
	
	<div id="modal_view_audio_file" class="modal fade" role="dialog">
		<div class="modal-dialog">
		  <!-- Modal content-->
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
			  <h4 class="modal-title"><b>Audio Files</b></h4>
			</div>
			<div class="modal-body">
				<ul id="audio_file_list_container" data-target="" style="max-height: 250px;"></ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		  </div>
		  <!-- End of modal content -->
		</div>
	</div>

    	<?php print $ui->standardizedThemeJS();?>

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

    	<!-- iCheck 1.0.1 -->
		<script src="js/plugins/iCheck/icheck.min.js"></script>
		!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>

		<script type="text/javascript">
			
			function FilterInput(event) {
				var keyCode = ('which' in event) ? event.which : event.keyCode;
			
				isNotWanted = (keyCode == 69 || keyCode == 101);
				return !isNotWanted;
			};
		
			function check_campaign_allow_inbound(value){
				if(value == "Y"){
					$('.campaign_allow_inbound_div').removeClass('hide');
				}else{
					$('.campaign_allow_inbound_div').addClass('hide');
				}
			}
			$(document).ready(function() {
			
				$('.select2').select2({
					theme: 'bootstrap'
				});
				
				$('#dial_status').select2({
					theme: 'bootstrap'
				});
				
				$(document).on('click', '.view-audio-files', function(){
					var label = $(this).data('label');
					$.ajax({
						url: "./php/GetListAudioFiles.php",
						type: 'POST',
						data: {
							
						},
						dataType: 'json',
						success: function(response) {
								// var values = JSON.parse(response.result);
								//console.log(response);
								$('#modal_view_audio_file').modal('show');
								$('#audio_file_list_container').html(response);
								$('#audio_file_list_container').attr("data-target", label);
							}
					});
				});
				
				$(document).on('click', '.file-list', function(){
					var name = $(this).data('name');
					var target = $('#audio_file_list_container').data("target");
					
					$('#' + target).val(name.slice(0, -4));
				});

				var campaign_allow_inbound = $('#campaign_allow_inbound').val();
				check_campaign_allow_inbound(campaign_allow_inbound);

				$(document).on('change', '#campaign_allow_inbound', function(){
					var value = $(this).val();
					check_campaign_allow_inbound(value);
				});

				$(document).on('click', '.btn-add-dial-status', function(){
					var dial_status = $('#dial_status').val();
					var campaign_id = $(this).data('campaign');
					var old_dial_status = $(this).data('dial-status');

					swal({
						title: "Are you sure?",
						text: "This action cannot be undone.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, add " + dial_status + " dial status!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
												url: "./php/AddDialStatus.php",
												type: 'POST',
												data: {
														campaign_id:campaign_id,
														dial_status:dial_status,
														old_dial_status:old_dial_status
												},
												dataType: 'json',
												success: function(data) {
												console.log(data);
														if(data == 1){
															swal({
																	title: "Success",
																	text: "Campaign Dial Status Successfully Updated!",
																	type: "success"
																},
																function(){
																	location.reload();
																}
															);
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

				$(document).on('click', '.remove-this-dial-status', function(){
					var campaign_id = $(this).data('campaign');
					var dial_status = $(this).data('dial-status');
					var selected_status = $(this).data('selected-status');
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';

					swal({
						title: "Are you sure?",
						text: "This action cannot be undone.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, remove " + selected_status + " dial status!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
												url: "./php/DeleteDialStatus.php",
												type: 'POST',
												data: {
														campaign_id:campaign_id,
														dial_status:dial_status,
														selected_status:selected_status,
														log_user: log_user,
														log_group: log_group
												},
												// dataType: 'json',
												success: function(data) {
												// console.log(data);
														if(data == 1){
															swal({
																	title: "Success",
																	text: "Campaign Dial Status Successfully Updated!",
																	type: "success"
																},
																function(){
																	location.reload();
																}
															);
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
				/**************
				** Init
				**************/

					//init cancel msg
						$(document).on('click', '#cancel_edit', function(){
							swal("Cancelled", "No action has been done :)", "error");
						});
						$(document).on('click', '#cancel', function(){
							swal({title: "Cancelled",text: "No action has been done :)",type: "error"},function(){window.location.href = 'telephonycampaigns.php';});
						});

					//Flat red color scheme for iCheck
					    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
					      checkboxClass: 'icheckbox_flat-green',
					      radioClass: 'iradio_flat-green'
					    });

				/*************
				** Campaign Events
				*************/

					//edit campaign
						$('#modifyCampaignButton').click(function(){
							$('#campaign_form_edit').submit();
						});

						$('.am_message_chooser').hide();
						$('.show_am_message_chooser').on('click', function(event) {
					        $('.am_message_chooser').toggle('show');
					    });
						
						$(document).on('change', '.am_message_chooser', function(){
							var AMmessage = $(this).val();
							
							$('#am_message_exten').val(AMmessage);
							$(this).hide();
						});

						//var dial_method = $('#dial_method').val();
						//dialMethod(dial_method);

						$('#dial_method').change(function(){
							dialMethod($(this).val());
						});

						var dial_prefix = $('#dial_prefix').val();
						dialPrefix(dial_prefix);

						$('#dial_prefix').change(function(){
							dialPrefix($(this).val());
						});


					    var campaign_type = $('#campaignType').find("option:selected").val();
					    setElements(campaign_type);

					    $('#campaignType').change(function(){
							var selectedTypeText = $(this).find("option:selected").text();
							var selectedTypeVal = $(this).find("option:selected").val();
							setElements(selectedTypeVal);
						});

				/*************
				** Disposition Events
				*************/

			   		//Add Status
				        $('#add_new_status').click(function(){

					        $('#add_button').html("<i class='fa fa-check'></i> Saving...");
							$('#add_new_status').attr("disabled", true);

					        var validate = 0;
					        var status = $("#add_status").val();
					        var status_name = $("#add_status_name").val();
							var log_user = '<?=$_SESSION['user']?>';
							var log_group = '<?=$_SESSION['usergroup']?>';

					        if(status == ""){
					            validate = 1;
					        }

					        if(status_name == ""){
					            validate = 1;
					        }

					            if(validate == 0){
					            		var selectable = "Y";
					            	if(!$('#add_selectable').is(":checked")){
					            		selectable = "N";
					            	}
					            		var human_answered = "Y";
					            	if(!$('#add_human_answered').is(":checked")){
					            		human_answered = "N";
					            	}
					            		var sale = "Y";
					            	if(!$('#add_sale').is(":checked")){
					            		sale = "N";
					            	}
					            		var dnc = "Y";
					            	if(!$('#add_dnc').is(":checked")){
					            		dnc = "N";
					            	}
					            		var scheduled_callback = "Y";
					            	if(!$('#add_scheduled_callback').is(":checked")){
					            		scheduled_callback = "N";
					            	}
					            		var customer_contact = "Y";
					            	if(!$('#add_customer_contact').is(":checked")){
					            		customer_contact = "N";
					            	}
					            		var not_interested = "Y";
					            	if(!$('#add_not_interested').is(":checked")){
					            		not_interested = "N";
					            	}
					            		var unworkable = "Y";
					            	if(!$('#add_unworkable').is(":checked")){
					            		unworkable = "N";
					            	}
					                $.ajax({
					                    url: "./php/AddDisposition.php",
					                    type: 'POST',
					                    data: {
					                    	campaign : $(this).attr('data-id'),
					                    	status : $('#add_status').val(),
								    		status_name : $('#add_status_name').val(),
								   			selectable : selectable,
								    		human_answered : human_answered,
								    		sale : sale,
								    		dnc : dnc,
								    		scheduled_callback : scheduled_callback,
								    		customer_contact : customer_contact,
								    		not_interested : not_interested,
								    		unworkable : unworkable,
											log_user: log_user,
											log_group: log_group
					                    },
					                    success: function(data) {
					                      // console.log(data);
					                          if(data == 1){
					                          		swal(
														{
															title: "Success",
															text: "New Status Successfully Added!",
															type: "success"
														},
														function(){
															location.reload();
															$(".preloader").fadeIn();
														}
													);
					                                $('#add_button').html("<i class='fa fa-plus'></i> New Status");
													$('#add_new_status').attr("disabled", false);
					                          }
					                          else{
					                              sweetAlert("Oops...", "Something went wrong! " + data, "error");
					                              $("#disposition_result").html(data);
					                          	  $('#add_button').html("<i class='fa fa-plus'></i> New Status");
												  $('#add_new_status').attr("disabled", false);
					                          }
					                    }
					                });

					            }else{
					                sweetAlert("Oops...", "Something went wrong!", "error");
					                $('#add_button').html("<i class='fa fa-plus'></i> New Status");
									$('#add_new_status').attr("disabled", false);
					                validate = 0;
					            }
				        });

					// GET DETAILS FOR EDIT DISPOSITION
						$(document).on('click','.edit_disposition',function() {
							var id = $(this).attr('data-id');
							var status = $(this).attr('data-status');
							var log_user = '<?=$_SESSION['user']?>';
							var log_group = '<?=$_SESSION['usergroup']?>';

							$.ajax({
							  url: "./php/ViewDisposition.php",
							  type: 'POST',
							  data: {
							  	campaign_id : id,
							  	status : status,
								log_user: log_user,
								log_group: log_group
							  },
							  dataType: 'json',
							  success: function(data) {
							  	console.log(data);

							  	$('#status_id_edit').text(data.status);
							  	$('#campaign_id_edit').text(data.campaign_id);
							  	$('#edit_campaign').val(data.campaign_id);

							  	$('#edit_campaign_id').val(data.campaign_id);
							  	$('#edit_status').val(data.status);
							  	$('#edit_status_name').val(data.status_name);

							  	$('#edit_selectable').val(data.selectable);
							  	$('#edit_human_answered').val(data.human_answered);
							  	$('#edit_sale').val(data.sale);
							  	$('#edit_dnc').val(data.dnc);
							  	$('#edit_scheduled_callback').val(data.scheduled_callback);
							  	$('#edit_customer_contact').val(data.customer_contact);
							  	$('#edit_not_interested').val(data.not_interested);
							  	$('#edit_unworkable').val(data.unworkable);

							  	if(data.selectable == "Y"){
							  		$('#edit_selectable').prop("checked", true);
							  	}else{
							  		$('#edit_selectable').prop("checked", false);
							  	}
							  	if(data.human_answered == "Y"){
							  		$('#edit_human_answered').prop("checked", true);
							  	}else{
							  		$('#edit_human_answered').prop("checked", false);
							  	}
							  	if(data.sale == "Y"){
							  		$('#edit_sale').prop("checked", true);
							  	}else{
							  		$('#edit_sale').prop("checked", false);
							  	}
							  	if(data.dnc == "Y"){
							  		$('#edit_dnc').prop("checked", true);
							  	}else{
							  		$('#edit_dnc').prop("checked", false);
							  	}
							  	if(data.scheduled_callback == "Y"){
							  		$('#edit_scheduled_callback').prop("checked", true);
							  	}else{
							  		$('#edit_scheduled_callback').prop("checked", false);
							  	}
							  	if(data.customer_contact == "Y"){
							  		$('#edit_customer_contact').prop("checked", true);
							  	}else{
							  		$('#edit_customer_contact').prop("checked", false);
							  	}
							  	if(data.not_interested == "Y"){
							  		$('#edit_not_interested').prop("checked", true);
							  	}else{
							  		$('#edit_not_interested').prop("checked", false);
							  	}
							  	if(data.unworkable == "Y"){
							  		$('#edit_unworkable').prop("checked", true);
							  	}else{
							  		$('#edit_unworkable').prop("checked", false);
							  	}
							  }
							});
						});

					//edit disposition
						$(document).on('click','#modify_disposition',function() {
							$('#update_button').html("<i class='fa fa-edit'></i> Updating...");
							$('#modify_disposition').attr("disabled", true);
							var log_user = '<?=$_SESSION['user']?>';
							var log_group = '<?=$_SESSION['usergroup']?>';

								var selectable = "Y";
				            	if(!$('#edit_selectable').is(":checked")){
				            		selectable = "N";
				            	}
				            		var human_answered = "Y";
				            	if(!$('#edit_human_answered').is(":checked")){
				            		human_answered = "N";
				            	}
				            		var sale = "Y";
				            	if(!$('#edit_sale').is(":checked")){
				            		sale = "N";
				            	}
				            		var dnc = "Y";
				            	if(!$('#edit_dnc').is(":checked")){
				            		dnc = "N";
				            	}
				            		var scheduled_callback = "Y";
				            	if(!$('#edit_scheduled_callback').is(":checked")){
				            		scheduled_callback = "N";
				            	}
				            		var customer_contact = "Y";
				            	if(!$('#edit_customer_contact').is(":checked")){
				            		customer_contact = "N";
				            	}
				            		var not_interested = "Y";
				            	if(!$('#edit_not_interested').is(":checked")){
				            		not_interested = "N";
				            	}
				            		var unworkable = "Y";
				            	if(!$('#edit_unworkable').is(":checked")){
				            		unworkable = "N";
				            	}

			                	$.ajax({
				                    url: "./php/ModifyDisposition.php",
				                    type: 'POST',
				                    data: {
				                    	disposition : $('#edit_campaign').val(),
				                        status : $('#edit_status').val(),
							    		status_name : $('#edit_status_name').val(),
							   			selectable : selectable,
							    		human_answered : human_answered,
							    		sale : sale,
							    		dnc : dnc,
							    		scheduled_callback : scheduled_callback,
							    		customer_contact : customer_contact,
							    		not_interested : not_interested,
							    		unworkable : unworkable,
										log_user: log_user,
										log_group: log_group
				                    },
				                    success: function(data) {
				                    console.log(data);
				                        if(data == 1){
			                                swal("Success!", "Disposition Successfully Updated!", "success");
			                                $('#update_button').html("<i class='fa fa-check'></i> Update");
											$('#modify_disposition').attr("disabled", false);
			                                window.setTimeout(function(){location.reload();},2000);
				                        }else{
				                        	swal("Ooops!", "Something went wrong! "+ data, "error");
				                            $('#update_button').html("<i class='fa fa-check'></i> Update");
											$('#modify_disposition').attr("disabled", false);
				                        }
			                    }
			                });
						});

				/*************
				** Lead Filter Events
				*************/
					//LEADFILTER
						$("#modifyleadfilter").validate({
		                	submitHandler: function() {
								//submit the form
									$("#resultmessage").html();
									$("#resultmessage").fadeOut();
									$.post("./php/ModifyTelephonyCampaign.php", //post
									$("#modifyleadfilter").serialize(),
										function(data){
											//if message is sent
											if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
												swal("Success!", "Lead Filter Successfully Updated!", "success");
											} else {
												sweetAlert("Oops...", "Something went wrong! "+ data, "error");
											}
											//
										});
								return false; //don't let the form refresh the page...
							}
						});

				/***********
				** Form Filters
				***********/
					/*** CAMPAIGNS ***/

						// disable special characters on Campaign Name
							$('#campaign_name').bind('keypress', function (event) {
							    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
							    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							    if (!regex.test(key)) {
							       event.preventDefault();
							       return false;
							    }
							});
						// disable special characters on Campaign Desc
							$('#campaign_desc').bind('keypress', function (event) {
							    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
							    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							    if (!regex.test(key)) {
							       event.preventDefault();
							       return false;
							    }
							});
					/*** end campaign ***/

					/*** DISPOSITION ***/

						// check duplicates
							$("#add_status").keyup(function() {
								clearTimeout($.data(this, 'timer'));
								var wait = setTimeout(duplicate_status_check($("#add_status").val(), "add"), 500);
								$(this).data('timer', wait);
							});
						// check duplicates
							$("#edit_status").keyup(function() {
								clearTimeout($.data(this, 'timer'));
								var wait = setTimeout(duplicate_status_check($("#edit_status").val(), "edit"), 500);
								$(this).data('timer', wait);
							});
						// disable special characters on User ID
							$('#add_status').bind('keypress', function (event) {
							    var regex = new RegExp("^[a-zA-Z0-9]+$");
							    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							    if (!regex.test(key)) {
							       event.preventDefault();
							       return false;
							    }
							});
							$('#add_status_name').bind('keypress', function (event) {
							    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
							    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							    if (!regex.test(key)) {
							       event.preventDefault();
							       return false;
							    }
							});
							$('#edit_status').bind('keypress', function (event) {
							    var regex = new RegExp("^[a-zA-Z0-9]+$");
							    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							    if (!regex.test(key)) {
							       event.preventDefault();
							       return false;
							    }
							});
							$('#edit_status_name').bind('keypress', function (event) {
							    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
							    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							    if (!regex.test(key)) {
							       event.preventDefault();
							       return false;
							    }
							});
					/*** end of disposition filters ***/

					$('#auto_dial_level').change(function(){
						if($(this).val() == 'ADVANCE'){
							$('#auto_dial_level_adv').removeClass('hide');
						}else{
							$('#auto_dial_level_adv').addClass('hide');
						}
					});

					//delete disposition
				        $(document).on('click','.delete_disposition', function() {
				            var id = $(this).attr('data-id');
				            var status = $(this).attr('data-status');
							var log_user = '<?=$_SESSION['user']?>';
							var log_group = '<?=$_SESSION['usergroup']?>';
				            swal({
				            	title: "Are you sure?",
				            	text: "This action cannot be undone.",
				            	type: "warning",
				            	showCancelButton: true,
				            	confirmButtonColor: "#DD6B55",
				            	confirmButtonText: "Yes, delete this disposition!",
				            	cancelButtonText: "No, cancel please!",
				            	closeOnConfirm: false,
				            	closeOnCancel: false
				            	},
				            	function(isConfirm){
				            		if (isConfirm) {
				            			$.ajax({
					                        url: "./php/DeleteDisposition.php",
					                        type: 'POST',
					                        data: {
					                            disposition_id:id,
					                            status: status,
												log_user: log_user,
												log_group: log_group
					                        },
					                        success: function(data) {
					                        console.log(data);
					                            if(data == 1){
					                            	swal({
															title: "Success",
															text: "Disposition Successfully Deleted!",
															type: "success"
														},
														function(){
															location.reload();
															$(".preloader").fadeIn();
														}
													);
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
				// ----------------- end of disposition



			});// end of document ready

			function empty_statuses(){
			 	console.log();
			 	swal({   title: "Oops...",   text: "This campaign has no existing disposition. You are going to be redirected after a few seconds!",   timer: 4000,   showConfirmButton: false });
				window.setTimeout(function(){location.replace("./telephonycampaigns.php")},4000);
			}

			function setElements(type){
				if(type == 'inbound'){
					$('.outbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.survey').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.inbound').removeClass('hide');
				}else if(type == 'survey'){
					$('.outbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.inbounce').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.survey').removeClass('hide');
				}else if(type == 'copy'){
					$('.outbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.survey').addClass('hide');
					$('.inbound').addClass('hide');
					$('.copy-from').removeClass('hide');
				}else if(type == 'blended'){
					$('.outbound').addClass('hide');
					$('.inbound').addClass('hide');
					$('.survey').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.blended').removeClass('hide');
				}else if(type == 'outbound'){
					$('.inbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.survey').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.outbound').removeClass('hide');
				}
			}

			function dialMethod(value){
				if(value == "RATIO"){
					$('#auto_dial_level').prop('disabled', false);
					$('#auto_dial_level option[value=SLOW]').prop('selected', true);
					$('#auto_dial_level option[value=OFF]').prop('disabled', true);
				}else if(value == "ADAPT_TAPERED"){
					$('#auto_dial_level').prop('disabled', true);
					$('#auto_dial_level option[value=MAX_PREDICTIVE]').prop('selected', true);
					// $('#auto_dial_level_adv').addClass('hide');
				}else if(value == "INBOUND_MAN"){
					$('#auto_dial_level').prop('disabled', true);
					$('#auto_dial_level option[value=SLOW]').prop('selected', true);
					// $('#auto_dial_level_adv').addClass('hide');
				}else{
					$('#auto_dial_level').prop('disabled', true);
					$('#auto_dial_level option[value=OFF]').prop('selected', true);
					// $('#auto_dial_level_adv').addClass('hide');
				}
				
				$('#auto_dial_level_adv').addClass('hide');
			}

			function dialPrefix(value){
				if(value == "CUSTOM"){
					$('#custom_prefix').removeClass('hide');
					$("#custom_prefix").attr("required", true);
				}else{
					$('#custom_prefix').addClass('hide');
					$("#custom_prefix").attr("required", false);
				}
			}

			function duplicate_status_check(status_id, addoredit){
				console.log(status_id);
				var status_form_value = status_id;
				var campaign_form_value = "<?php if(isset($did)){echo $did;}else{echo "";}?>";
		        if(status_form_value != ""){
				    $.ajax({
					    url: "php/checkCampaign.php",
					    type: 'POST',
					    data: {
					    	status : status_form_value,
					    	campaign_id : campaign_form_value
					    },
					    dataType: 'json',

						success: function(data) {
							var returndata = $.parseJSON(data);
							if(returndata.result == "success"){

									if(addoredit == "add")
										$("#add_new_status").attr("disabled", false);

									if(addoredit == "edit")
										$("#modify_disposition").attr("disabled", false);

								$( "#status" ).removeClass("error");
								$( "#status-duplicate-error" ).text("").removeClass("error").addClass("avail");

							}else{

									if(addoredit == "add")
										$("#add_new_status").attr("disabled", true);
									if(addoredit == "edit")
										$("#modify_disposition").attr("disabled", true);

								$( "#status" ).addClass( "error" );
								$( "#status-duplicate-error" ).text( returndata.status ).removeClass("avail").addClass("error");

							}
						}
					});
				}
			}
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
