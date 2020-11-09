<?php
/**
 * @file 		edittelephonycampaign.php
 * @brief 		Modify Campaign settings
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja
 * @author		Noel Umandap
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
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	

	$campaign_id = NULL;
	if (isset($_POST["campaign"])) {
		$campaign_id = $_POST["campaign"];
	}

	$did = NULL;
	if (isset($_POST["disposition_id"])) {
		$did = $_POST["disposition_id"];
	}

	$leadrecycling_id = NULL;
	if (isset($_POST["leadrecycling_id"])) {
		$leadrecycling_id = $_POST["leadrecycling_id"];
	}

	$lf_id = NULL;
	if (isset($_POST["leadfilter"])) {
		$lf_id = $_POST["leadfilter"];
	}

	// get campaign values
	$campaign = $api->API_getCampaignInfo($campaign_id);
	//$disposition = $api->API_getCampaignDispositions($campaign_id);

	$calltimes = $api->API_getAllCalltimes();
	$scripts = $api->API_getAllScripts();
	$carriers = $api->API_getAllCarriers();
	$leadfilter = $api->API_getAllLeadFilters();
	$dialStatus = $api->API_getAllDialStatuses($campaign_id, '');
	$sdialStatus = $api->API_getAllDialStatusesSurvey($campaign_id);
	$campdialStatus = $api->API_getAllCampaignDialStatuses($campaign_id);
	$dids = $api->API_getAllDIDs();
	$voicefiles = $api->API_getAllVoiceFiles();
	$ingroups = $api->API_getAllInGroups();
	$ivr = $api->API_getAllIVRs();
	$lists = $api->API_getAllLists();
	$audiofiles = $api->API_getAllVoiceFiles();

	// for autodial level options
	$server_list = $api->API_getAllServers();
	$server_id = $server_list->server_id[0];
	$server = $api->API_getServerInfo($server_id);

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("campaigns"); ?>
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
		<!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
		<link rel="stylesheet" href="css/flags/flags.min.css">
		<!-- bootstrap color picker -->
		<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>   		
		<style type="text/css">
			.select2-container{
				width: 100% !important;
			}
			
			/*.select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
				padding-left: 0;
				padding-right: 0;
				height: auto;
				width: 100% !important;
				margin-top: -4px;
			}*/
			.add_color {
				padding: 0;
				border: 1px solid #888;
				cursor: pointer;
			}
			.disable-select {
				-webkit-user-select: none;  
				-moz-user-select: none;    
				-ms-user-select: none;      
				user-select: none;
			}
		</style>
        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
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
					
						<!-- <form id="campaign_form_edit" class="form-horizontal"  action="./php/ModifyTelephonyCampaign.php" method="POST" enctype="multipart/form-data"> -->
						<form id="campaign_form_edit" class="form-horizontal">
							<input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>">
							<input type="hidden" name="campaign_type" value="<?php echo $campaign->campaign_type;?>">
							<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
							<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
							<?php $errormessage = NULL; ?>

						<!-- IF CAMPAIGN -->
						<?php
							if($campaign_id != NULL) {
								if ($campaign->result=="success") {
						?>
							<div class="panel-body">
								<legend><?php $lh->translateText("modify_campaign_id"); ?> : <u><?php echo $campaign_id." - ".$campaign->data->campaign_name;?></u>
									<span class="pull-right"><?php $lh->translateText("manual_dial_list_id"); ?>: <u><?php echo $campaign->data->manual_dial_list_id;?></u></span>
								</legend>
								<!-- Custom Tabs -->
								<div role="tabpanel">
								<!--<div class="nav-tabs-custom">-->
									<ul role="tablist" class="nav nav-tabs nav-justified">
										<li class="active"><a href="#tab_1" data-toggle="tab"><?php $lh->translateText("basic_settings"); ?></a></li>
										<li><a id="advanced_settings_tab" href="#tab_2" data-toggle="tab" data-id="<?php echo $campaign_id;?>"><?php $lh->translateText("advanced_settings"); ?></a></li>
									</ul>
										<!-- Tab contents-->
										<div class="tab-content">
										<!-- BASIC SETTINGS -->
											<div id="tab_1" class="tab-pane fade in active">
											<fieldset>
												<div class="form-group mt">
													<label class="col-sm-3 control-label" for="campaign_name"><?php $lh->translateText("campaign_name"); ?>:</label>
													<div class="col-sm-9 mb">
														<input type="text" class="form-control" name="campaign_name" id="campaign_name" value="<?php echo $campaign->data->campaign_name; ?>" title="Must be 6 to 40 characters in length." minlength="6" maxlength="40" required>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="campaign_desc"><?php $lh->translateText("campaign_description"); ?>:</label>
													<div class="col-sm-9 mb">
														<input type="text" class="form-control" name="campaign_desc" id="campaign_desc" value="<?php echo $campaign->data->campaign_description; ?>" >
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("active"); ?>:</label>
													<div class="col-sm-9 mb">
														<select class="form-control" name="active">
															<option value="Y" <?php if($campaign->data->active == 'Y') echo "selected";?>><?php $lh->translateText("go_yes"); ?></option>
															<option value="N" <?php if($campaign->data->active == "N") echo "selected";?>><?php $lh->translateText("go_no"); ?></option>
														</select>
													</div>
												</div>
												<?php if($campaign->campaign_type != "SURVEY") { ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("dial_method"); ?>:</label>
													<div class="col-sm-9 mb">
														<select name="dial_method" id="dial_method" class="form-control" name="dial_method">
															<option value="MANUAL" <?php if($campaign->data->dial_method == "MANUAL") echo "selected";?>>MANUAL</option>
															<option value="RATIO" <?php if($campaign->data->dial_method == "RATIO") echo "selected";?>>AUTO DIAL</option>
															<option value="ADAPT_TAPERED" <?php if($campaign->data->dial_method == "ADAPT_TAPERED") echo "selected";?>>PREDICTIVE</option>
															<option value="INBOUND_MAN" <?php if($campaign->data->dial_method == "INBOUND_MAN") echo "selected";?>>INBOUND MAN</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("autodial_level"); ?>:</label>
													<div class="col-sm-9 mb">
														<div class="row">
															<?php
																$autodial_level = $campaign->data->auto_dial_level;
																$autodial_level_adv = $campaign->auto_dial_level;
															?>
															<div class="col-lg-8">
																<select id="auto_dial_level" class="form-control" name="auto_dial_level" <?php if($campaign->data->dial_method !== "RATIO") echo "disabled";?>>
																<option value="OFF" <?php if($campaign->data->dial_method == "MANUAL") echo "selected";?> disabled>OFF</option>
																<option value="SLOW"<?php if($autodial_level_adv !== 'ADVANCE' && $autodial_level == "1") echo "selected";?>>SLOW</option>
																<option VALUE="NORMAL" <?php if($autodial_level_adv !== 'ADVANCE' && $autodial_level == "2") echo "selected";?>>NORMAL</option>
																<option VALUE="HIGH" <?php if($autodial_level_adv !== 'ADVANCE' && $autodial_level == "4") echo "selected";?>>HIGH</option>
																<option VALUE="MAX"<?php if($autodial_level_adv !== 'ADVANCE' && $autodial_level == "6") echo "selected";?>>MAX</option>
																<option VALUE="MAX_PREDICTIVE"<?php if($autodial_level_adv !== 'ADVANCE' && $autodial_level == "10" || $campaign->data->dial_method == "ADAPT_TAPERED") echo "selected";?> disabled>MAX PREDICTIVE</option>
																<option value="ADVANCE" <?php if($autodial_level_adv === 'ADVANCE') echo "selected";?> >ADVANCE</option>
																</select>
															</div>
															<div class="col-lg-4">
																<select id="auto_dial_level_adv" class="form-control <?php if($autodial_level_adv !== 'ADVANCE') echo "hide";?> " name="auto_dial_level_adv">
<?php if($server->data->max_vicidial_trunks == NULL){ ?>
																	<option value="1" <?php if($autodial_level == "1") echo "selected"; ?> >1.0</option>
																	<option value="1.5" <?php if($autodial_level == "1.5") echo "selected"; ?> >1.5</option>
																	<option value="2" <?php if($autodial_level == "2") echo "selected"; ?> >2.0</option>
																	<option value="2.5" <?php if($autodial_level == "2.5") echo "selected"; ?> >2.5</option>
																	<option value="3.0" <?php if($autodial_level == "3.0") echo "selected"; ?> >3.0</option>
																	<option value="3.5" <?php if($autodial_level == "3.5") echo "selected"; ?> >3.5</option>
																	<option value="4" <?php if($autodial_level == "4") echo "selected"; ?> >4.0</option>
																	<option value="4.5" <?php if($autodial_level == "4.5") echo "selected"; ?> >4.5</option>
																	<option value="5.0" <?php if($autodial_level == "5.0") echo "selected"; ?> >5.0</option>
																	<option value="5.5" <?php if($autodial_level == "5.5") echo "selected"; ?> >5.5</option>
																	<option value="6" <?php if($autodial_level == "6") echo "selected"; ?> >6.0</option>
																	<option value="6.5" <?php if($autodial_level == "6.5") echo "selected"; ?> >6.5</option>
																	<option value="7.0" <?php if($autodial_level == "7.0") echo "selected"; ?> >7.0</option>
																	<option value="7.5" <?php if($autodial_level == "7.5") echo "selected"; ?> >7.5</option>
																	<option value="8.0" <?php if($autodial_level == "8.0") echo "selected"; ?> >8.0</option>
																	<option value="8.5" <?php if($autodial_level == "8.5") echo "selected"; ?> >8.5</option>
																	<option value="9.0" <?php if($autodial_level == "9.0") echo "selected"; ?> >9.0</option>
																	<option value="9.5" <?php if($autodial_level == "9.5") echo "selected"; ?> >9.5</option>
																	<option value="10" <?php if($autodial_level == "10") echo "selected"; ?> >10.0</option>
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

<?php
        } else {
                for($a=1; $a <= $server->data->max_vicidial_trunks; $a = $a+0.5){
                        $b = number_format((float)$a, 1, '.', '');
?>
                         <option value="<?php echo $b; ?>" <?php if($autodial_level == "$b") echo "selected"; ?> ><?php echo $b; ?></option>
<?php
                }
        }
?>

																</select>
															</div>
														</div>
													</div>
												</div>
												<?php } ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("carrier_to_use_for_campaign"); ?>:</label>
													<div class="col-sm-9 mb">
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
													<label class="col-sm-3 control-label"><?php $lh->translateText("omit_phone_code"); ?>:</label>
													<div class="col-sm-9 mb">
														<select name="omit_phone_code" id="omit_phone_code" class="form-control">
															<option value="N" <?php if($campaign->data->omit_phone_code == '' && $campaign->data->omit_phone_code == 'N'){ echo 'selected';}?>>N</option>
															<option value="Y" <?php if($campaign->data->omit_phone_code == 'Y'){ echo 'selected';}?>>Y</option>
														</select>
													</div>
												</div>
												<?php if($campaign->campaign_type != "SURVEY") { ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("web"); ?>:</label>
													<div class="col-sm-9 mb">
														<input type="text" id="web_form_address" name="web_form_address" class="form-control" value="<?php echo $campaign->data->web_form_address;?>">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("scripts"); ?>:</label>
													<div class="col-sm-9 mb">
														<select class="form-control" id="campaign_script" name="campaign_script">
															<option value="" <?php if(empty($campaign->data->campaign_script)) echo "selected"; ?>>--- NONE ---</option>
															<?php for($i=0;$i<count($scripts->script_id);$i++) { ?>
																<?php if(!empty($scripts->script_id[$i])) { ?>
																	<option value="<?php echo $scripts->script_id[$i]; ?>" <?php if($campaign->data->campaign_script == $scripts->script_id[$i]) echo "selected";?>><?php echo $scripts->script_name[$i]; ?></option>
																<?php } ?>
															<?php } ?>
														</select>
													</div>
												</div>
												<?php } ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("campaign_caller_id"); ?>:</label>
													<div class="col-sm-9 mb">
														<input type="text" class="form-control" id="campaign_cid" name="campaign_cid" value="<?php echo $campaign->data->campaign_cid; ?>">
													</div>
												</div>

												<div class="form-group">
                                                                                                   <label class="col-sm-3 control-label"><?php $lh->translateText("custom_caller_id"); ?>:</label>
                                                                                                      <div class="col-sm-9 mb">
                                                                                                          <select id="use_custom_cid" class="form-control" name="use_custom_cid">
                                                                                                              <option value="N" <?php if($campaign->data->use_custom_cid == "N") echo "selected";?>>NO</option>
                                                                                                              <option value="Y" <?php if($campaign->data->use_custom_cid == "Y") echo "selected";?>>YES</option>
													      <option value="AREACODE" <?php if($campaign->data->use_custom_cid == "AREACODE") echo "selected";?>>AREACODE</option>

                                                                                                          </select>
                                                                                                      </div>
                                                                                                </div>

												<?php if($campaign->campaign_type == "SURVEY") { ?>
                                                                                                <div class="form-group survey_method_agent_xfer_view">
                                                                                                        <label class="col-sm-3 control-label"><?php $lh->translateText("campaign_recordings"); ?>:</label>
                                                                                                        <div class="col-sm-9 mb">
                                                                                                                <select id="campaign_recording" class="form-control" name="campaign_recording">
                                                                                                                        <option value="NEVER" <?php if($campaign->data->campaign_recording == "NEVER") echo "selected";?>>OFF</option>
                                                                                                                        <option value="ALLFORCE" <?php if($campaign->data->campaign_recording == "ALLFORCE") echo "selected";?>>ON</option>
                                                                                                                        <option value="ONDEMAND" <?php if($campaign->data->campaign_recording == "ONDEMAND") echo "selected";?>>ONDEMAND</option>
                                                                                                                </select>
                                                                                                        </div>
                                                                                                </div>
												<?php } ?>
												<?php if($campaign->campaign_type != "SURVEY") { ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("campaign_recordings"); ?>:</label>
													<div class="col-sm-9 mb">
														<select id="campaign_recording" class="form-control" name="campaign_recording">
															<option value="NEVER" <?php if($campaign->data->campaign_recording == "NEVER") echo "selected";?>>OFF</option>
															<option value="ALLFORCE" <?php if($campaign->data->campaign_recording == "ALLFORCE") echo "selected";?>>ON</option>
															<option value="ONDEMAND" <?php if($campaign->data->campaign_recording == "ONDEMAND") echo "selected";?>>ONDEMAND</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("answer_machine_detection"); ?>:</label>
													<div class="col-sm-9 mb">
														<select id="campaign_vdad_exten" name="campaign_vdad_exten" class="form-control">
															<option value="<?php if($campaign->campaign_type == "SURVEY"){ echo '8366';}else{ echo '8368';} ?>" <?php if ($campaign->data->campaign_vdad_exten == "8368" || $campaign->data->campaign_vdad_exten == "8366") echo "selected"; ?>>OFF</option>
															<option value="<?php if($campaign->campaign_type == "SURVEY"){ echo '8373';}else{ echo '8369';}  ?>" <?php if ($campaign->data->campaign_vdad_exten == "8369" || $campaign->data->campaign_vdad_exten == "8373") echo "selected"; ?>>ON</option>
														</select>
													</div>
												</div>
												<?php } ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("local_calltime"); ?>:</label>
													<div class="col-sm-9 mb">
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
													<label class="col-sm-3 control-label"><?php $lh->translateText("minimum_hopper_level"); ?>:</label>
													<div class="col-sm-9 mb">
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
													<label class="col-sm-3 control-label"><?php $lh->translateText("force_reset_of_hopper"); ?>:</label>
													<div class="col-sm-9 mb">
														<select class="form-control" id="force_reset_hopper" name="force_reset_hopper">
															<option value="Y">Yes</option>
															<option value="N" selected>No</option>
														</select>
													</div>
												</div>
											<?php } elseif($campaign->campaign_type == "INBOUND") { ?>
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("phone_numbers_did/ftn_on_this_campaign"); ?>:</label>
													<span class="col-sm-9 control-label" style="text-align: left; vertical-align: top;">
														<?php if(count($dids->did_id) > 0) {?>
															<?php for($i=0;$i<=count($dids->did_id);$i++) { ?>
																<?php if(!empty($dids->did_id[$i])){ ?>
																	<p><?php echo $dids->did_pattern[$i]; ?></p>
																<?php } ?>
															<?php }?>
														<?php } else { ?>
															No <b>DID/'s</b> <?php $lh->translationFor("found_for_this_campaign"); ?>.
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
													<label class="col-sm-3 control-label"><?php $lh->translateText("minimum_hopper_level"); ?>:</label>
													<div class="col-sm-9 mb">
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
													<label class="col-sm-3 control-label"><?php $lh->translateText("phone_numbers_did/ftn_on_this_campaign"); ?>:</label>
													<span class="col-sm-9 control-label" style="text-align: left; vertical-align: top;">
														<?php if(count($dids->did_id) != 0) {?>
															<?php for($i=0;$i<=count($dids->did_id);$i++) { ?>
																<?php if(!empty($dids->did_id[$i])){ ?>
																	<p><?php echo $dids->did_pattern[$i]; ?></p>
																<?php } ?>
															<?php }?>
														<?php } else { ?>
															No <b>DID/'s</b> <?php $lh->translationFor("found_for_this_campaign"); ?>.
														<?php } ?>
													</span>
												</div>
											<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
												<!--<div class="form-group">
													<label class="col-sm-3 control-label">Audio File:</label>
													<div class="col-sm-7 mb">
														<input type="text" class="form-control" id="survey_first_audio_file" name="survey_first_audio_file" value="<?php echo $campaign->data->survey_first_audio_file; ?>">
													</div>
													<div class="col-sm-2 mb">
														<button type="button" class="view-audio-files btn btn-default" data-label="survey_first_audio_file">Audio</button>
													</div>
												</div>-->
												<div class="form-group">
													<label class="col-sm-3 control-label"><?php $lh->translateText("audiofiles"); ?>:</label>
													<div class="col-sm-9 mb">
														<div class="input-group">
															<input type="text" class="form-control" id="survey_first_audio_file" name="survey_first_audio_file" value="<?php echo $campaign->data->survey_first_audio_file;?>">
															<span class="input-group-btn">
																<button class="btn btn-default show-view-audio-files" data-label="survey_first_audio_file" type="button">[Audio Chooser...]</button>
															</span>
														</div><!-- /input-group -->
														<select class="form-control survey_first_audio_file_dropdown" id="survey_first_audio_file_dropdown" data-label="survey_first_audio_file">
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
													<label class="col-sm-3 control-label"><?php $lh->translateText("number_of_channels"); ?>:</label>
													<div class="col-sm-9">
														<input id="no-channels" name="no_channels" type="number" value="<?php echo $campaign->number_of_lines; ?>" min="1" max="200" class="form-control">
													</div>
												</div>
											<?php } else { ?>
												<!-- Nothing to do -->
											<?php } ?>
											</fieldset><!-- /.fieldset -->
								</div><!-- /.tab-pane -->
							
												<div class="tab-pane fade in" id="tab_2">
													<fieldset>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("default_country_code"); ?>:</label>
															<div class="col-sm-9 mb">
																<div id="flag" class="flag flag-<?php if(!empty($campaign->country_codes->{$campaign->default_country_code}->tld)) { echo $campaign->country_codes->{$campaign->default_country_code}->tld; } else { echo "us"; } ?>" style="position: absolute; top: 11px; left: 30px;"></div>
																<select class="form-control" id="default_country_code" name="default_country_code" style="padding-left: 35px;">
																	<?php foreach ($campaign->country_codes as $cKey => $cCode) { ?>
																		<option data-tld="<?php echo $cCode->tld; ?>" value="<?php echo $cKey; ?>" <?php if((empty($campaign->default_country_code) && $cKey === 'USA_1') || (!empty($campaign->default_country_code) && $campaign->default_country_code == $cKey)) echo "selected";?>><?php echo $cCode->name . " (+" . $cCode->code . ")"; ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<?php if($campaign->campaign_type != "SURVEY") { ?>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("allowed_inbound_and_blended"); ?>:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="campaign_allow_inbound" name="campaign_allow_inbound">
																	<option value="N" <?php if($campaign->data->campaign_allow_inbound == "N") echo "selected";?>>No</option>
																	<option value="Y" <?php if($campaign->data->campaign_allow_inbound == "Y") echo "selected";?>>Yes</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("launch_custom_fields"); ?>:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="custom_fields_launch" name="custom_fields_launch">
																	<option value="ONCALL" <?php if($campaign->custom_fields_launch == "ONCALL") echo "selected";?>>ONCALL</option>
																	<option value="LOGIN" <?php if($campaign->custom_fields_launch == "LOGIN") echo "selected";?>>LOGIN</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("custom_fields_list_id"); ?>:</label>
															<div class="col-sm-9 mb">
																<input type="text" class="form-control" value="<?php if(!empty($campaign->custom_fields_list_id)){echo $campaign->custom_fields_list_id;}?>" id="custom_fields_list_id" name="custom_fields_list_id">
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("call_notes_per_call"); ?>:</label>
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
														<div class="form-group<?=($campaign->dynamic_cid === '' ? ' hidden': '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("dynamic_cid"); ?>:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="dynamic_cid" name="dynamic_cid">
																	<option value="N" <?php if($campaign->dynamic_cid == "N") echo "selected";?>>DISABLED</option>
																	<option value="Y" <?php if($campaign->dynamic_cid == "Y") echo "selected";?>>ENABLED</option>
																</select>
															</div>
														</div>
														<div class="form-group<?=(empty($campaign->data->nextdial_seconds) ? ' hidden': '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("nextdial_seconds"); ?>:</label>
															<div class="col-sm-9 mb">
																<input type="number" min="3" max="60" class="form-control" value="<?php if(!empty($campaign->data->nextdial_seconds)){echo $campaign->data->nextdial_seconds;}?>" id="nextdial_seconds" name="nextdial_seconds">
															</div>
														</div>
														<?php } ?>
														<?php if($campaign->campaign_type == "OUTBOUND") { ?>
															<div class="form-group" style="margin-bottom: 10px;">
																<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=1;?>
																<?php foreach($dial_statuses as $dial_status) { ?>
																	<?php if(!empty($dial_status)) { ?>
																		<label class="col-sm-3 control-label"><?php $lh->translateText("active_dial_status"); ?> <?php echo $i; ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_status"); ?>:</label>
																<div class="col-sm-8 mb">
																	<select class="form-control" id="dial_status" name="dial_status">
																		<option value="" selected>NONE</option>
																		<optgroup label="System Statuses">
																			<?php for($i=0;$i<=count($dialStatus->status->system);$i++) { ?>
																				<?php if( !empty($dialStatus->status->system[$i]) && !in_array($dialStatus->status->system[$i], $dial_statuses) ){ ?>
																					<option value="<?php echo $dialStatus->status->system[$i]?>">
																						<?php echo $dialStatus->status->system[$i]." - ".$dialStatus->status_name->system[$i]?>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("list_order"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select size="1" name="lead_order" id="lead_order" class="form-control">
															            <option value="DOWN" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN") echo "selected"; ?>>DOWN</option>
															            <option value="UP" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP") echo "selected"; ?>>UP</option>
															            <option value="DOWN_PHONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE") echo "selected"; ?>>DOWN PHONE</option>
															            <option value="UP_PHONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE") echo "selected"; ?>>UP PHONE</option>
															            <option value="DOWN_LAST_NAME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME") echo "selected"; ?>>DOWN LAST NAME</option>
															            <option value="UP_LAST_NAME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME") echo "selected"; ?>>UP LAST NAME</option>
															            <option value="DOWN_COUNT" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT") echo "selected"; ?>>DOWN COUNT</option>
															            <option value="UP_COUNT" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT") echo "selected"; ?>>UP COUNT</option>
															            <option value="RANDOM" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM") echo "selected"; ?>>RANDOM</option>
															            <option value="DOWN_LAST_CALL_TIME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME") echo "selected"; ?>>DOWN LAST CALL TIME</option>
															            <option value="UP_LAST_CALL_TIME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME") echo "selected"; ?>>UP LAST CALL TIME</option>
															            <option value="DOWN_RANK" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK") echo "selected"; ?>>DOWN RANK</option>
															            <option value="UP_RANK" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK") echo "selected"; ?>>UP RANK</option>
															            <option value="DOWN_OWNER" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER") echo "selected"; ?>>DOWN OWNER</option>
															            <option value="UP_OWNER" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER") echo "selected"; ?>>UP OWNER</option>
															            <option value="DOWN_TIMEZONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE") echo "selected"; ?>>DOWN TIMEZONE</option>
															            <option value="UP_TIMEZONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE") echo "selected"; ?>>UP TIMEZONE</option>
															            <option value="DOWN_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_2nd_NEW") echo "selected"; ?>>DOWN 2nd NEW</option>
															            <option value="DOWN_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_3rd_NEW") echo "selected"; ?>>DOWN 3rd NEW</option>
															            <option value="DOWN_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_4th_NEW") echo "selected"; ?>>DOWN 4th NEW</option>
															            <option value="DOWN_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_5th_NEW") echo "selected"; ?>>DOWN 5th NEW</option>
															            <option value="DOWN_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_6th_NEW") echo "selected"; ?>>DOWN 6th NEW</option>
															            <option value="UP_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_2nd_NEW") echo "selected"; ?>>UP 2nd NEW</option>
															            <option value="UP_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_3rd_NEW") echo "selected"; ?>>UP 3rd NEW</option>
															            <option value="UP_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_4th_NEW") echo "selected"; ?>>UP 4th NEW</option>
															            <option value="UP_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_5th_NEW") echo "selected"; ?>>UP 5th NEW</option>
															            <option value="UP_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_6th_NEW") echo "selected"; ?>>UP 6th NEW</option>
															            <option value="DOWN_PHONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_2nd_NEW") echo "selected"; ?>>DOWN PHONE 2nd NEW</option>
															            <option value="DOWN_PHONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_3rd_NEW") echo "selected"; ?>>DOWN PHONE 3rd NEW</option>
															            <option value="DOWN_PHONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_4th_NEW") echo "selected"; ?>>DOWN PHONE 4th NEW</option>
															            <option value="DOWN_PHONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_5th_NEW") echo "selected"; ?>>DOWN PHONE 5th NEW</option>
															            <option value="DOWN_PHONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_6th_NEW") echo "selected"; ?>>DOWN PHONE 6th NEW</option>
															            <option value="UP_PHONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_2nd_NEW") echo "selected"; ?>>UP PHONE 2nd NEW</option>
															            <option value="UP_PHONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_3rd_NEW") echo "selected"; ?>>UP PHONE 3rd NEW</option>
															            <option value="UP_PHONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_4th_NEW") echo "selected"; ?>>UP PHONE 4th NEW</option>
															            <option value="UP_PHONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_5th_NEW") echo "selected"; ?>>UP PHONE 5th NEW</option>
															            <option value="UP_PHONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_6th_NEW") echo "selected"; ?>>UP PHONE 6th NEW</option>
															            <option value="DOWN_LAST_NAME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_2nd_NEW") echo "selected"; ?>>DOWN LAST NAME 2nd NEW</option>
															            <option value="DOWN_LAST_NAME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_3rd_NEW") echo "selected"; ?>>DOWN LAST NAME 3rd NEW</option>
															            <option value="DOWN_LAST_NAME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_4th_NEW") echo "selected"; ?>>DOWN LAST NAME 4th NEW</option>
															            <option value="DOWN_LAST_NAME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_5th_NEW") echo "selected"; ?>>DOWN LAST NAME 5th NEW</option>
															            <option value="DOWN_LAST_NAME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_6th_NEW") echo "selected"; ?>>DOWN LAST NAME 6th NEW</option>
															            <option value="UP_LAST_NAME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_2nd_NEW") echo "selected"; ?>>UP LAST NAME 2nd NEW</option>
															            <option value="UP_LAST_NAME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_3rd_NEW") echo "selected"; ?>>UP LAST NAME 3rd NEW</option>
															            <option value="UP_LAST_NAME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_4th_NEW") echo "selected"; ?>>UP LAST NAME 4th NEW</option>
															            <option value="UP_LAST_NAME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_5th_NEW") echo "selected"; ?>>UP LAST NAME 5th NEW</option>
															            <option value="UP_LAST_NAME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_6th_NEW") echo "selected"; ?>>UP LAST NAME 6th NEW</option>
															            <option value="DOWN_COUNT_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_2nd_NEW") echo "selected"; ?>>DOWN COUNT 2nd NEW</option>
															            <option value="DOWN_COUNT_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_3rd_NEW") echo "selected"; ?>>DOWN COUNT 3rd NEW</option>
															            <option value="DOWN_COUNT_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_4th_NEW") echo "selected"; ?>>DOWN COUNT 4th NEW</option>
															            <option value="DOWN_COUNT_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_5th_NEW") echo "selected"; ?>>DOWN COUNT 5th NEW</option>
															            <option value="DOWN_COUNT_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_6th_NEW") echo "selected"; ?>>DOWN COUNT 6th NEW</option>
															            <option value="UP_COUNT_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_2nd_NEW") echo "selected"; ?>>UP COUNT 2nd NEW</option>
															            <option value="UP_COUNT_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_3rd_NEW") echo "selected"; ?>>UP COUNT 3rd NEW</option>
															            <option value="UP_COUNT_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_4th_NEW") echo "selected"; ?>>UP COUNT 4th NEW</option>
															            <option value="UP_COUNT_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_5th_NEW") echo "selected"; ?>>UP COUNT 5th NEW</option>
															            <option value="UP_COUNT_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_6th_NEW") echo "selected"; ?>>UP COUNT 6th NEW</option>
															            <option value="RANDOM_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_2nd_NEW") echo "selected"; ?>>RANDOM 2nd NEW</option>
															            <option value="RANDOM_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_3rd_NEW") echo "selected"; ?>>RANDOM 3rd NEW</option>
															            <option value="RANDOM_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_4th_NEW") echo "selected"; ?>>RANDOM 4th NEW</option>
															            <option value="RANDOM_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_5th_NEW") echo "selected"; ?>>RANDOM 5th NEW</option>
															            <option value="RANDOM_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_6th_NEW") echo "selected"; ?>>RANDOM 6th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 2nd NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 3rd NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 4th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 5th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 6th NEW</option>
															            <option value="UP_LAST_CALL_TIME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>UP LAST CALL TIME 2nd NEW</option>
															            <option value="UP_LAST_CALL_TIME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>UP LAST CALL TIME 3rd NEW</option>
															            <option value="UP_LAST_CALL_TIME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>UP LAST CALL TIME 4th NEW</option>
															            <option value="UP_LAST_CALL_TIME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>UP LAST CALL TIME 5th NEW</option>
															            <option value="UP_LAST_CALL_TIME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>UP LAST CALL TIME 6th NEW</option>
															            <option value="DOWN_RANK_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_2nd_NEW") echo "selected"; ?>>DOWN RANK 2nd NEW</option>
															            <option value="DOWN_RANK_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_3rd_NEW") echo "selected"; ?>>DOWN RANK 3rd NEW</option>
															            <option value="DOWN_RANK_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_4th_NEW") echo "selected"; ?>>DOWN RANK 4th NEW</option>
															            <option value="DOWN_RANK_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_5th_NEW") echo "selected"; ?>>DOWN RANK 5th NEW</option>
															            <option value="DOWN_RANK_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_6th_NEW") echo "selected"; ?>>DOWN RANK 6th NEW</option>
															            <option value="UP_RANK_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_2nd_NEW") echo "selected"; ?>>UP RANK 2nd NEW</option>
															            <option value="UP_RANK_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_3rd_NEW") echo "selected"; ?>>UP RANK 3rd NEW</option>
															            <option value="UP_RANK_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_4th_NEW") echo "selected"; ?>>UP RANK 4th NEW</option>
															            <option value="UP_RANK_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_5th_NEW") echo "selected"; ?>>UP RANK 5th NEW</option>
															            <option value="UP_RANK_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_6th_NEW") echo "selected"; ?>>UP RANK 6th NEW</option>
															            <option value="DOWN_OWNER_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_2nd_NEW") echo "selected"; ?>>DOWN OWNER 2nd NEW</option>
															            <option value="DOWN_OWNER_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_3rd_NEW") echo "selected"; ?>>DOWN OWNER 3rd NEW</option>
															            <option value="DOWN_OWNER_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_4th_NEW") echo "selected"; ?>>DOWN OWNER 4th NEW</option>
															            <option value="DOWN_OWNER_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_5th_NEW") echo "selected"; ?>>DOWN OWNER 5th NEW</option>
															            <option value="DOWN_OWNER_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_6th_NEW") echo "selected"; ?>>DOWN OWNER 6th NEW</option>
															            <option value="UP_OWNER_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_2nd_NEW") echo "selected"; ?>>UP OWNER 2nd NEW</option>
															            <option value="UP_OWNER_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_3rd_NEW") echo "selected"; ?>>UP OWNER 3rd NEW</option>
															            <option value="UP_OWNER_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_4th_NEW") echo "selected"; ?>>UP OWNER 4th NEW</option>
															            <option value="UP_OWNER_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_5th_NEW") echo "selected"; ?>>UP OWNER 5th NEW</option>
															            <option value="UP_OWNER_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_6th_NEW") echo "selected"; ?>>UP OWNER 6th NEW</option>
															            <option value="DOWN_TIMEZONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_2nd_NEW") echo "selected"; ?>>DOWN TIMEZONE 2nd NEW</option>
															            <option value="DOWN_TIMEZONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_3rd_NEW") echo "selected"; ?>>DOWN TIMEZONE 3rd NEW</option>
															            <option value="DOWN_TIMEZONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_4th_NEW") echo "selected"; ?>>DOWN TIMEZONE 4th NEW</option>
															            <option value="DOWN_TIMEZONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_5th_NEW") echo "selected"; ?>>DOWN TIMEZONE 5th NEW</option>
															            <option value="DOWN_TIMEZONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_6th_NEW") echo "selected"; ?>>DOWN TIMEZONE 6th NEW</option>
															            <option value="UP_TIMEZONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_2nd_NEW") echo "selected"; ?>>UP TIMEZONE 2nd NEW</option>
															            <option value="UP_TIMEZONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_3rd_NEW") echo "selected"; ?>>UP TIMEZONE 3rd NEW</option>
															            <option value="UP_TIMEZONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_4th_NEW") echo "selected"; ?>>UP TIMEZONE 4th NEW</option>
															            <option value="UP_TIMEZONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_5th_NEW") echo "selected"; ?>>UP TIMEZONE 5th NEW</option>
															            <option value="UP_TIMEZONE_6TH_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_6TH_NEW") echo "selected"; ?>>UP TIMEZONE 6th NEW</option>
															        </select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Lead Order Secondary:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="lead_order_secondary" name="lead_order_secondary">
																		<option value="LEAD_ASCEND" <?php if($campaign->data->lead_order_secondary == "LEAD_ASCEND") echo "selected"; ?>>LEAD ASCEND</option>
																		<option value="LEAD_DESCEND" <?php if($campaign->data->lead_order_secondary == "LEAD_DESCEND") echo "selected"; ?>>LEAD DESCEND</option>
																		<option value="CALLTIME_ASCEND" <?php if($campaign->data->lead_order_secondary == "CALLTIME_ASCEND") echo "selected"; ?>>CALLTIME ASCEND</option>
																		<option value="CALLTIME_DESCEND" <?php if($campaign->data->lead_order_secondary == "CALLTIME_DESCEND") echo "selected"; ?>>CALLTIME DESCEND</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("lead_filter"); ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("call_count_limit"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="call_count_limit" name="call_count_limit" min="0" value="<?php echo $campaign->data->call_count_limit; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("call_count_target"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="call_count_target" name="call_count_target" min="0" value="<?php echo $campaign->data->call_count_target; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_timeout"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_prefix"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="manual_dial_prefix" name="manual_dial_prefix" value="<?php echo $campaign->data->manual_dial_prefix; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_min_digits"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="number" min="3" max="20" class="form-control" id="manual_dial_min_digits" name="manual_dial_min_digits" value="<?php echo $campaign->manual_dial_min_digits; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("get_call_launch"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="get_call_launch" name="get_call_launch">
																		<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																		<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																		<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("answering_machine_message"); ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("amd_send_to_action"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																		<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("waitforsilence_options"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("pause_code"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																		<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																		<option value="FORCE" <?php if($campaign->data->agent_pause_codes_active == "FORCE") echo "selected";?>>FORCE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_filter"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																		<option value="NONE" <?php if($campaign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																		<option value="DNC_ONLY" <?php if($campaign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																		<option value="CAMPLISTS_ONLY" <?php if($campaign->data->manual_dial_filter == "CAMPLISTS_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																		<option value="DNC_AND_CAMPLISTS" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLISTS") echo "selected";?>>DNC & CAMPLIST</option>
																		<option value="DNC_AND_CAMPLISTS_ALL" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLISTS_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_search_filter"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="manual_dial_search_filter" name="manual_dial_search_filter">
																		<option value="NONE" <?php if($campaign->data->manual_dial_search_filter == "NONE") echo "selected";?>>NONE</option>
																		<option value="CAMPLISTS_ONLY" <?php if($campaign->data->manual_dial_search_filter == "CAMPLISTS_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																		<option value="CAMPLISTS_ALL" <?php if($campaign->data->manual_dial_search_filter == "CAMPLISTS_ALL") echo "selected";?>>CAMPLIST ALL</option>
																	</select>
																</div>
															</div>															
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("use_internal_dnc"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="use_internal_dnc" name="use_internal_dnc">
																		<option value="Y" <?php if($campaign->data->use_internal_dnc == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->use_internal_dnc == "N") echo "selected";?>>NO</option>
																		<option value="AREACODE" <?php if($campaign->data->use_internal_dnc == "AREACODE") echo "selected";?>>AREACODE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("use_campaign_dnc"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="use_campaign_dnc" name="use_campaign_dnc">
																		<option value="Y" <?php if($campaign->data->use_campaign_dnc == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->use_campaign_dnc == "N") echo "selected";?>>NO</option>
																		<option value="AREACODE" <?php if($campaign->data->use_campaign_dnc == "AREACODE") echo "selected";?>>AREACODE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_list_id"); ?>:</label>
																<div class="col-sm-9 mb">
																	<!--<select class="form-control select2" id="manual_dial_list_id" name="manual_dial_list_id">-->
																		<!-- <option value="998" <?php //if($campaign->data->manual_dial_list_id == 998 || $campaign->data->manual_dial_list_id == 0) echo "selected";?>>998</option>
																		<option value="999" <?php //if($campaign->data->manual_dial_list_id == 999) echo "selected";?>>999</option> -->
																		<?php //for($i=0;$i<count($lists->list_id);$i++){ ?>
																			<!--<option value="<?php //echo $lists->list_id[$i];?>" <?php //if($lists->list_id[$i] == $campaign->data->manual_dial_list_id) echo "selected";?>><?php //echo $lists->list_id[$i]; ?></option>';-->
																		<?php //} ?>
																	<!--</select>-->
																	<input type="text" class="form-control" id="manual_dial_list_id" name="manual_dial_list_id" value="<?php if($campaign->data->manual_dial_list_id != ''){echo $campaign->data->manual_dial_list_id;}else{echo "998";} ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("alt_number_dialing"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="alt_number_dialing" name="alt_number_dialing">
																		<option value="N" <?php if($campaign->data->alt_number_dialing == "N") echo "selected";?>>No</option>
																		<option value="Y" <?php if($campaign->data->alt_number_dialing == "Y") echo "selected";?>>Yes</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("available_only_tally"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="available_only_ratio_tally" name="available_only_ratio_tally">
																		<option value="N" <?php if($campaign->data->available_only_ratio_tally == 'N') echo "selected";?>>NO</option>
																		<option value="Y" <?php if($campaign->data->available_only_ratio_tally == 'Y') echo "selected";?>>YES</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("campaign_recording_filename"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("next_agent_call");?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="next_agent_call" name="next_agent_call">
																		<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																		<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																		<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																		<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																		<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																		<option value="LONGEST_WAIT_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAIT_TIME") echo "selected";?>>LONGEST WAIT TIME</option>
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
																<label class="col-sm-3 control-label">Disable Alter Customer Data:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="disable_alter_custdata" name="disable_alter_custdata">
																		<option value="N" <?php if($campaign->data->disable_alter_custdata == 'N') echo "selected";?>>NO</option>
																		<option value="Y" <?php if($campaign->data->disable_alter_custdata == 'Y') echo "selected";?>>YES</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Disable Alter Customer Phone:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="disable_alter_custphone" name="disable_alter_custphone">
																		<option value="N" <?php if($campaign->data->disable_alter_custphone == 'N') echo "selected";?>>NO</option>
																		<option value="Y" <?php if($campaign->data->disable_alter_custphone == 'Y') echo "selected";?>>YES</option>
																		<option value="HIDE" <?php if($campaign->data->disable_alter_custphone == 'HIDE') echo "selected";?>>HIDE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Caller ID for 3-way Calls:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="three_way_call_cid" name="three_way_call_cid">
																		<option value="CUSTOM_CID" <?php if($campaign->data->three_way_call_cid == "CUSTOM_CID") echo "selected";?>>CUSTOM_CID</option>
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
																		<option value="NONE" <?php if($campaign->data->customer_3way_hangup_action == "NONE") echo "selected";?>>NONE</option>
																	</select>
																</div>
															</div>
														<?php } elseif($campaign->campaign_type == "INBOUND") { ?>
															<div class="form-group" style="margin-bottom: 10px;">
																<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=1;?>
																<?php foreach($dial_statuses as $dial_status) { ?>
																	<?php if(!empty($dial_status)) { ?>
																		<label class="col-sm-3 control-label"><?php $lh->translateText("active_dial_status"); ?> <?php echo $i; ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_status"); ?>:</label>
																<div class="col-sm-8 mb">
																	<select class="form-control" id="dial_status" name="dial_status">
																		<option value="" selected>NONE</option>
																		<optgroup label="System Statuses">
																		<?php for($i=0;$i<=count($dialStatus->status->system);$i++) { ?>
																			<?php if( !empty($dialStatus->status->system[$i]) && !in_array($dialStatus->status->system[$i], $dial_statuses) ){ ?>
																				<option value="<?php echo $dialStatus->status->system[$i]?>">
																					<?php echo $dialStatus->status->system[$i]." - ".$dialStatus->status_name->system[$i]?>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("get_call_launch"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="get_call_launch" name="get_call_launch">
																		<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																		<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																		<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("answering_machine_message"); ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("amd_send_to_action"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																		<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("waitforsilence_options"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("pause_codes"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																		<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																		<option value="FORCE" <?php if($campaign->data->agent_pause_codes_active == "FORCE") echo "selected";?>>FORCE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_filter"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																		<option value="NONE" <?php if($campaign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																		<option value="DNC_ONLY" <?php if($campaign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																		<option value="CAMPLISTS_ONLY" <?php if($campaign->data->manual_dial_filter == "CAMPLISTS_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																		<option value="DNC_AND_CAMPLISTS" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLISTS") echo "selected";?>>DNC & CAMPLIST</option>
																		<option value="DNC_AND_CAMPLISTS_ALL" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLISTS_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_search_filter"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="manual_dial_search_filter" name="manual_dial_search_filter">
																		<option value="NONE" <?php if($campaign->data->manual_dial_search_filter == "NONE") echo "selected";?>>NONE</option>
																		<option value="CAMPLISTS_ONLY" <?php if($campaign->data->manual_dial_search_filter == "CAMPLISTS_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																		<option value="CAMPLISTS_ALL" <?php if($campaign->data->manual_dial_search_filter == "CAMPLISTS_ALL") echo "selected";?>>CAMPLIST ALL</option>
																	</select>
																</div>
															</div>															
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_list_id"); ?>:</label>
																<div class="col-sm-9 mb">
																	<!--<select class="form-control select2" id="manual_dial_list_id" name="manual_dial_list_id">-->
																		<!-- <option value="998" <?php //if($campaign->data->manual_dial_list_id == 998 || $campaign->data->manual_dial_list_id == 0) echo "selected";?>>998</option>
																		<option value="999" <?php //if($campaign->data->manual_dial_list_id == 999) echo "selected";?>>999</option> -->
																		<?php //for($i=0;$i<count($lists->list_id);$i++){ ?>
																			<!--<option value="<?php //echo $lists->list_id[$i];?>" <?php //if($lists->list_id[$i] == $campaign->data->manual_dial_list_id) echo "selected";?>><?php //echo $lists->list_id[$i]; ?></option>';-->
																		<?php //} ?>
																	<!--</select>-->
																	<input type="text" class="form-control" id="manual_dial_list_id" name="manual_dial_list_id" value="<?php if($campaign->data->manual_dial_list_id != ''){echo $campaign->data->manual_dial_list_id;}else{echo "998";} ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("alt_number_dialing"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="alt_number_dialing" name="alt_number_dialing">
																		<option value="N" <?php if($campaign->data->alt_number_dialing == "N") echo "selected";?>>No</option>
																		<option value="Y" <?php if($campaign->data->alt_number_dialing == "Y") echo "selected";?>>Yes</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("available_only_tally"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="available_only_ratio_tally" name="available_only_ratio_tally">
																		<option value="N" <?php if($campaign->data->available_only_ratio_tally == 'N') echo "selected";?>>NO</option>
																		<option value="Y" <?php if($campaign->data->available_only_ratio_tally == 'Y') echo "selected";?>>YES</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("campaign_recording_filename"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("next_agent_call"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="next_agent_call" name="next_agent_call">
																		<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																		<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																		<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																		<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																		<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																		<option value="LONGEST_WAIT_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAIT_TIME") echo "selected";?>>LONGEST WAIT TIME</option>
																	</select>
																</div>
															</div>
															<?php if($campaign->data->dial_method == "INBOUND_MAN") { ?>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("get_call_launch"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<select class="form-control" id="get_call_launch" name="get_call_launch">
																			<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																			<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																			<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("dial_timeout"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_prefix"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="text" class="form-control" id="manual_dial_prefix" name="manual_dial_prefix" value="<?php echo $campaign->data->manual_dial_prefix; ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_min_digits"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" min="3" max="20" class="form-control" id="manual_dial_min_digits" name="manual_dial_min_digits" value="<?php echo $campaign->manual_dial_min_digits; ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label">Caller ID for 3-way Calls:</label>
																	<div class="col-sm-9 mb">
																		<select class="form-control" id="three_way_call_cid" name="three_way_call_cid">
																			<option value="CUSTOM_CID" <?php if($campaign->data->three_way_call_cid == "CUSTOM_CID") echo "selected";?>>CUSTOM_CID</option>
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
															<?php } ?>
														<?php } elseif($campaign->campaign_type == "BLENDED") { ?>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("get_call_launch"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="get_call_launch" name="get_call_launch">
																		<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																		<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																		<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("call_time"); ?>:</label>
																<div class="col-sm-9 mb">
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
																		<label class="col-sm-3 control-label"><?php $lh->translateText("active_dial_status"); ?> <?php echo $i; ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_status"); ?>:</label>
																<div class="col-sm-8 mb">
																	<select class="form-control" id="dial_status" name="dial_status">
																		<option value="" selected>NONE</option>
																		<optgroup label="System Statuses">
																		<?php for($i=0;$i<=count($dialStatus->status->system);$i++) { ?>
																			<?php if( !empty($dialStatus->status->system[$i]) && !in_array($dialStatus->status->system[$i], $dial_statuses) ){ ?>
																				<option value="<?php echo $dialStatus->status->system[$i]?>">
																					<?php echo $dialStatus->status->system[$i]." - ".$dialStatus->status_name->system[$i]?>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("list_order"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select size="1" name="lead_order" id="lead_order" class="form-control">
															            <option value="DOWN" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN") echo "selected"; ?>>DOWN</option>
															            <option value="UP" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP") echo "selected"; ?>>UP</option>
															            <option value="DOWN_PHONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE") echo "selected"; ?>>DOWN PHONE</option>
															            <option value="UP_PHONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE") echo "selected"; ?>>UP PHONE</option>
															            <option value="DOWN_LAST_NAME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME") echo "selected"; ?>>DOWN LAST NAME</option>
															            <option value="UP_LAST_NAME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME") echo "selected"; ?>>UP LAST NAME</option>
															            <option value="DOWN_COUNT" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT") echo "selected"; ?>>DOWN COUNT</option>
															            <option value="UP_COUNT" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT") echo "selected"; ?>>UP COUNT</option>
															            <option value="RANDOM" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM") echo "selected"; ?>>RANDOM</option>
															            <option value="DOWN_LAST_CALL_TIME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME") echo "selected"; ?>>DOWN LAST CALL TIME</option>
															            <option value="UP_LAST_CALL_TIME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME") echo "selected"; ?>>UP LAST CALL TIME</option>
															            <option value="DOWN_RANK" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK") echo "selected"; ?>>DOWN RANK</option>
															            <option value="UP_RANK" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK") echo "selected"; ?>>UP RANK</option>
															            <option value="DOWN_OWNER" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER") echo "selected"; ?>>DOWN OWNER</option>
															            <option value="UP_OWNER" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER") echo "selected"; ?>>UP OWNER</option>
															            <option value="DOWN_TIMEZONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE") echo "selected"; ?>>DOWN TIMEZONE</option>
															            <option value="UP_TIMEZONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE") echo "selected"; ?>>UP TIMEZONE</option>
															            <option value="DOWN_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_2nd_NEW") echo "selected"; ?>>DOWN 2nd NEW</option>
															            <option value="DOWN_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_3rd_NEW") echo "selected"; ?>>DOWN 3rd NEW</option>
															            <option value="DOWN_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_4th_NEW") echo "selected"; ?>>DOWN 4th NEW</option>
															            <option value="DOWN_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_5th_NEW") echo "selected"; ?>>DOWN 5th NEW</option>
															            <option value="DOWN_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_6th_NEW") echo "selected"; ?>>DOWN 6th NEW</option>
															            <option value="UP_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_2nd_NEW") echo "selected"; ?>>UP 2nd NEW</option>
															            <option value="UP_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_3rd_NEW") echo "selected"; ?>>UP 3rd NEW</option>
															            <option value="UP_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_4th_NEW") echo "selected"; ?>>UP 4th NEW</option>
															            <option value="UP_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_5th_NEW") echo "selected"; ?>>UP 5th NEW</option>
															            <option value="UP_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_6th_NEW") echo "selected"; ?>>UP 6th NEW</option>
															            <option value="DOWN_PHONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_2nd_NEW") echo "selected"; ?>>DOWN PHONE 2nd NEW</option>
															            <option value="DOWN_PHONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_3rd_NEW") echo "selected"; ?>>DOWN PHONE 3rd NEW</option>
															            <option value="DOWN_PHONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_4th_NEW") echo "selected"; ?>>DOWN PHONE 4th NEW</option>
															            <option value="DOWN_PHONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_5th_NEW") echo "selected"; ?>>DOWN PHONE 5th NEW</option>
															            <option value="DOWN_PHONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_6th_NEW") echo "selected"; ?>>DOWN PHONE 6th NEW</option>
															            <option value="UP_PHONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_2nd_NEW") echo "selected"; ?>>UP PHONE 2nd NEW</option>
															            <option value="UP_PHONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_3rd_NEW") echo "selected"; ?>>UP PHONE 3rd NEW</option>
															            <option value="UP_PHONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_4th_NEW") echo "selected"; ?>>UP PHONE 4th NEW</option>
															            <option value="UP_PHONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_5th_NEW") echo "selected"; ?>>UP PHONE 5th NEW</option>
															            <option value="UP_PHONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_6th_NEW") echo "selected"; ?>>UP PHONE 6th NEW</option>
															            <option value="DOWN_LAST_NAME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_2nd_NEW") echo "selected"; ?>>DOWN LAST NAME 2nd NEW</option>
															            <option value="DOWN_LAST_NAME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_3rd_NEW") echo "selected"; ?>>DOWN LAST NAME 3rd NEW</option>
															            <option value="DOWN_LAST_NAME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_4th_NEW") echo "selected"; ?>>DOWN LAST NAME 4th NEW</option>
															            <option value="DOWN_LAST_NAME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_5th_NEW") echo "selected"; ?>>DOWN LAST NAME 5th NEW</option>
															            <option value="DOWN_LAST_NAME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_6th_NEW") echo "selected"; ?>>DOWN LAST NAME 6th NEW</option>
															            <option value="UP_LAST_NAME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_2nd_NEW") echo "selected"; ?>>UP LAST NAME 2nd NEW</option>
															            <option value="UP_LAST_NAME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_3rd_NEW") echo "selected"; ?>>UP LAST NAME 3rd NEW</option>
															            <option value="UP_LAST_NAME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_4th_NEW") echo "selected"; ?>>UP LAST NAME 4th NEW</option>
															            <option value="UP_LAST_NAME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_5th_NEW") echo "selected"; ?>>UP LAST NAME 5th NEW</option>
															            <option value="UP_LAST_NAME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_6th_NEW") echo "selected"; ?>>UP LAST NAME 6th NEW</option>
															            <option value="DOWN_COUNT_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_2nd_NEW") echo "selected"; ?>>DOWN COUNT 2nd NEW</option>
															            <option value="DOWN_COUNT_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_3rd_NEW") echo "selected"; ?>>DOWN COUNT 3rd NEW</option>
															            <option value="DOWN_COUNT_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_4th_NEW") echo "selected"; ?>>DOWN COUNT 4th NEW</option>
															            <option value="DOWN_COUNT_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_5th_NEW") echo "selected"; ?>>DOWN COUNT 5th NEW</option>
															            <option value="DOWN_COUNT_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_6th_NEW") echo "selected"; ?>>DOWN COUNT 6th NEW</option>
															            <option value="UP_COUNT_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_2nd_NEW") echo "selected"; ?>>UP COUNT 2nd NEW</option>
															            <option value="UP_COUNT_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_3rd_NEW") echo "selected"; ?>>UP COUNT 3rd NEW</option>
															            <option value="UP_COUNT_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_4th_NEW") echo "selected"; ?>>UP COUNT 4th NEW</option>
															            <option value="UP_COUNT_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_5th_NEW") echo "selected"; ?>>UP COUNT 5th NEW</option>
															            <option value="UP_COUNT_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_6th_NEW") echo "selected"; ?>>UP COUNT 6th NEW</option>
															            <option value="RANDOM_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_2nd_NEW") echo "selected"; ?>>RANDOM 2nd NEW</option>
															            <option value="RANDOM_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_3rd_NEW") echo "selected"; ?>>RANDOM 3rd NEW</option>
															            <option value="RANDOM_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_4th_NEW") echo "selected"; ?>>RANDOM 4th NEW</option>
															            <option value="RANDOM_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_5th_NEW") echo "selected"; ?>>RANDOM 5th NEW</option>
															            <option value="RANDOM_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_6th_NEW") echo "selected"; ?>>RANDOM 6th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 2nd NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 3rd NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 4th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 5th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 6th NEW</option>
															            <option value="UP_LAST_CALL_TIME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>UP LAST CALL TIME 2nd NEW</option>
															            <option value="UP_LAST_CALL_TIME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>UP LAST CALL TIME 3rd NEW</option>
															            <option value="UP_LAST_CALL_TIME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>UP LAST CALL TIME 4th NEW</option>
															            <option value="UP_LAST_CALL_TIME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>UP LAST CALL TIME 5th NEW</option>
															            <option value="UP_LAST_CALL_TIME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>UP LAST CALL TIME 6th NEW</option>
															            <option value="DOWN_RANK_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_2nd_NEW") echo "selected"; ?>>DOWN RANK 2nd NEW</option>
															            <option value="DOWN_RANK_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_3rd_NEW") echo "selected"; ?>>DOWN RANK 3rd NEW</option>
															            <option value="DOWN_RANK_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_4th_NEW") echo "selected"; ?>>DOWN RANK 4th NEW</option>
															            <option value="DOWN_RANK_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_5th_NEW") echo "selected"; ?>>DOWN RANK 5th NEW</option>
															            <option value="DOWN_RANK_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_6th_NEW") echo "selected"; ?>>DOWN RANK 6th NEW</option>
															            <option value="UP_RANK_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_2nd_NEW") echo "selected"; ?>>UP RANK 2nd NEW</option>
															            <option value="UP_RANK_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_3rd_NEW") echo "selected"; ?>>UP RANK 3rd NEW</option>
															            <option value="UP_RANK_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_4th_NEW") echo "selected"; ?>>UP RANK 4th NEW</option>
															            <option value="UP_RANK_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_5th_NEW") echo "selected"; ?>>UP RANK 5th NEW</option>
															            <option value="UP_RANK_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_6th_NEW") echo "selected"; ?>>UP RANK 6th NEW</option>
															            <option value="DOWN_OWNER_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_2nd_NEW") echo "selected"; ?>>DOWN OWNER 2nd NEW</option>
															            <option value="DOWN_OWNER_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_3rd_NEW") echo "selected"; ?>>DOWN OWNER 3rd NEW</option>
															            <option value="DOWN_OWNER_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_4th_NEW") echo "selected"; ?>>DOWN OWNER 4th NEW</option>
															            <option value="DOWN_OWNER_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_5th_NEW") echo "selected"; ?>>DOWN OWNER 5th NEW</option>
															            <option value="DOWN_OWNER_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_6th_NEW") echo "selected"; ?>>DOWN OWNER 6th NEW</option>
															            <option value="UP_OWNER_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_2nd_NEW") echo "selected"; ?>>UP OWNER 2nd NEW</option>
															            <option value="UP_OWNER_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_3rd_NEW") echo "selected"; ?>>UP OWNER 3rd NEW</option>
															            <option value="UP_OWNER_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_4th_NEW") echo "selected"; ?>>UP OWNER 4th NEW</option>
															            <option value="UP_OWNER_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_5th_NEW") echo "selected"; ?>>UP OWNER 5th NEW</option>
															            <option value="UP_OWNER_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_6th_NEW") echo "selected"; ?>>UP OWNER 6th NEW</option>
															            <option value="DOWN_TIMEZONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_2nd_NEW") echo "selected"; ?>>DOWN TIMEZONE 2nd NEW</option>
															            <option value="DOWN_TIMEZONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_3rd_NEW") echo "selected"; ?>>DOWN TIMEZONE 3rd NEW</option>
															            <option value="DOWN_TIMEZONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_4th_NEW") echo "selected"; ?>>DOWN TIMEZONE 4th NEW</option>
															            <option value="DOWN_TIMEZONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_5th_NEW") echo "selected"; ?>>DOWN TIMEZONE 5th NEW</option>
															            <option value="DOWN_TIMEZONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_6th_NEW") echo "selected"; ?>>DOWN TIMEZONE 6th NEW</option>
															            <option value="UP_TIMEZONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_2nd_NEW") echo "selected"; ?>>UP TIMEZONE 2nd NEW</option>
															            <option value="UP_TIMEZONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_3rd_NEW") echo "selected"; ?>>UP TIMEZONE 3rd NEW</option>
															            <option value="UP_TIMEZONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_4th_NEW") echo "selected"; ?>>UP TIMEZONE 4th NEW</option>
															            <option value="UP_TIMEZONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_5th_NEW") echo "selected"; ?>>UP TIMEZONE 5th NEW</option>
															            <option value="UP_TIMEZONE_6TH_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_6TH_NEW") echo "selected"; ?>>UP TIMEZONE 6th NEW</option>
															        </select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Lead Order Secondary:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="lead_order_secondary" name="lead_order_secondary">
																		<option value="LEAD_ASCEND" <?php if($campaign->data->lead_order_secondary == "LEAD_ASCEND") echo "selected"; ?>>LEAD ASCEND</option>
																		<option value="LEAD_DESCEND" <?php if($campaign->data->lead_order_secondary == "LEAD_DESCEND") echo "selected"; ?>>LEAD DESCEND</option>
																		<option value="CALLTIME_ASCEND" <?php if($campaign->data->lead_order_secondary == "CALLTIME_ASCEND") echo "selected"; ?>>CALLTIME ASCEND</option>
																		<option value="CALLTIME_DESCEND" <?php if($campaign->data->lead_order_secondary == "CALLTIME_DESCEND") echo "selected"; ?>>CALLTIME DESCEND</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("lead_filter"); ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("call_count_limit"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="call_count_limit" name="call_count_limit" value="<?php echo $campaign->data->call_count_limit; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("call_count_target"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="number" class="form-control" id="call_count_target" name="call_count_target" value="<?php echo $campaign->data->call_count_target; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("force_reset_of_hopper"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="force_reset_hopper" name="force_reset_hopper">
																		<option value="Y">Y</option>
																		<option value="N" selected>N</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_timeout"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_prefix"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="manual_dial_prefix" name="manual_dial_prefix" value="<?php echo $campaign->data->manual_dial_prefix; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_min_digits"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="number" min="3" max="20" class="form-control" id="manual_dial_min_digits" name="manual_dial_min_digits" value="<?php echo $campaign->manual_dial_min_digits; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("get_call_launch"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="get_call_launch" name="get_call_launch">
																		<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																		<option value="SCRIPT" <?php if($campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																		<option value="WEBFORM" <?php if($campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("answering_machine_message"); ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("amd_send_to_action"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																		<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("waitforsilence_options"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("pause_codes"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																		<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																		<option value="FORCE" <?php if($campaign->data->agent_pause_codes_active == "FORCE") echo "selected";?>>FORCE</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_filter"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																		<option value="NONE" <?php if($campaign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																		<option value="DNC_ONLY" <?php if($campaign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																		<option value="CAMPLISTS_ONLY" <?php if($campaign->data->manual_dial_filter == "CAMPLISTS_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																		<option value="DNC_AND_CAMPLISTS" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLISTS") echo "selected";?>>DNC & CAMPLIST</option>
																		<option value="DNC_AND_CAMPLISTS_ALL" <?php if($campaign->data->manual_dial_filter == "DNC_AND_CAMPLISTS_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_search_filter"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="manual_dial_search_filter" name="manual_dial_search_filter">
																		<option value="NONE" <?php if($campaign->data->manual_dial_search_filter == "NONE") echo "selected";?>>NONE</option>
																		<option value="CAMPLISTS_ONLY" <?php if($campaign->data->manual_dial_search_filter == "CAMPLISTS_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																		<option value="CAMPLISTS_ALL" <?php if($campaign->data->manual_dial_search_filter == "CAMPLISTS_ALL") echo "selected";?>>CAMPLIST ALL</option>
																	</select>
																</div>
															</div>															
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("manual_dial_list_id"); ?>:</label>
																<div class="col-sm-9 mb">
																	<!--<select class="form-control select2" id="manual_dial_list_id" name="manual_dial_list_id">-->
																		<!-- <option value="998" <?php //if($campaign->data->manual_dial_list_id == 998 || $campaign->data->manual_dial_list_id == 0) echo "selected";?>>998</option>
																		<option value="999" <?php //if($campaign->data->manual_dial_list_id == 999) echo "selected";?>>999</option> -->
																		<?php //for($i=0;$i<count($lists->list_id);$i++){ ?>
																			<!--<option value="<?php //echo $lists->list_id[$i];?>" <?php //if($lists->list_id[$i] == $campaign->data->manual_dial_list_id) echo "selected";?>><?php //echo $lists->list_id[$i]; ?></option>';-->
																		<?php //} ?>
																	<!--</select>-->
																	<input type="text" class="form-control" id="manual_dial_list_id" name="manual_dial_list_id" value="<?php if($campaign->data->manual_dial_list_id != ''){echo $campaign->data->manual_dial_list_id;}else{echo "998";} ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("alt_number_dialing"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="alt_number_dialing" name="alt_number_dialing">
																		<option value="N" <?php if($campaign->data->alt_number_dialing == "N") echo "selected";?>>No</option>
																		<option value="Y" <?php if($campaign->data->alt_number_dialing == "Y") echo "selected";?>>Yes</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("available_only_tally"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="available_only_ratio_tally" name="available_only_ratio_tally">
																		<option value="N" <?php if($campaign->data->available_only_ratio_tally == 'N') echo "selected";?>>NO</option>
																		<option value="Y" <?php if($campaign->data->available_only_ratio_tally == 'Y') echo "selected";?>>YES</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("campaign_recording_filename"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("next_agent_call"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="next_agent_call" name="next_agent_call">
																		<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																		<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																		<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																		<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																		<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																		<option value="LONGEST_WAIT_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAIT_TIME") echo "selected";?>>LONGEST WAIT TIME</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Caller ID for 3-way Calls:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="three_way_call_cid" name="three_way_call_cid">
																		<option value="CUSTOM_CID" <?php if($campaign->data->three_way_call_cid == "CUSTOM_CID") echo "selected";?>>CUSTOM_CID</option>
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
															<div class="form-group" style="margin-bottom: 10px;">
																<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=1;?>
																<?php foreach($dial_statuses as $dial_status) { ?>
																	<?php if(!empty($dial_status)) { ?>
																		<label class="col-sm-3 control-label"><?php $lh->translateText("active_dial_status"); ?> <?php echo $i; ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_status"); ?>:</label>
																<div class="col-sm-8 mb">
																	<select class="form-control" id="dial_status" name="dial_status">
																		<option value="" selected>NONE</option>
																		<optgroup label="System Statuses">
																			<?php for($i=0;$i<=count($dialStatus->status->system);$i++) { ?>
																				<?php if( !empty($dialStatus->status->system[$i]) && !in_array($dialStatus->status->system[$i], $dial_statuses) ){ ?>
																					<option value="<?php echo $dialStatus->status->system[$i]?>">
																						<?php echo $dialStatus->status->system[$i]." - ".$dialStatus->status_name->system[$i]?>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("list_order"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select size="1" name="lead_order" id="lead_order" class="form-control">
															            <option value="DOWN" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN") echo "selected"; ?>>DOWN</option>
															            <option value="UP" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP") echo "selected"; ?>>UP</option>
															            <option value="DOWN_PHONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE") echo "selected"; ?>>DOWN PHONE</option>
															            <option value="UP_PHONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE") echo "selected"; ?>>UP PHONE</option>
															            <option value="DOWN_LAST_NAME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME") echo "selected"; ?>>DOWN LAST NAME</option>
															            <option value="UP_LAST_NAME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME") echo "selected"; ?>>UP LAST NAME</option>
															            <option value="DOWN_COUNT" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT") echo "selected"; ?>>DOWN COUNT</option>
															            <option value="UP_COUNT" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT") echo "selected"; ?>>UP COUNT</option>
															            <option value="RANDOM" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM") echo "selected"; ?>>RANDOM</option>
															            <option value="DOWN_LAST_CALL_TIME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME") echo "selected"; ?>>DOWN LAST CALL TIME</option>
															            <option value="UP_LAST_CALL_TIME" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME") echo "selected"; ?>>UP LAST CALL TIME</option>
															            <option value="DOWN_RANK" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK") echo "selected"; ?>>DOWN RANK</option>
															            <option value="UP_RANK" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK") echo "selected"; ?>>UP RANK</option>
															            <option value="DOWN_OWNER" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER") echo "selected"; ?>>DOWN OWNER</option>
															            <option value="UP_OWNER" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER") echo "selected"; ?>>UP OWNER</option>
															            <option value="DOWN_TIMEZONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE") echo "selected"; ?>>DOWN TIMEZONE</option>
															            <option value="UP_TIMEZONE" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE") echo "selected"; ?>>UP TIMEZONE</option>
															            <option value="DOWN_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_2nd_NEW") echo "selected"; ?>>DOWN 2nd NEW</option>
															            <option value="DOWN_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_3rd_NEW") echo "selected"; ?>>DOWN 3rd NEW</option>
															            <option value="DOWN_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_4th_NEW") echo "selected"; ?>>DOWN 4th NEW</option>
															            <option value="DOWN_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_5th_NEW") echo "selected"; ?>>DOWN 5th NEW</option>
															            <option value="DOWN_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_6th_NEW") echo "selected"; ?>>DOWN 6th NEW</option>
															            <option value="UP_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_2nd_NEW") echo "selected"; ?>>UP 2nd NEW</option>
															            <option value="UP_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_3rd_NEW") echo "selected"; ?>>UP 3rd NEW</option>
															            <option value="UP_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_4th_NEW") echo "selected"; ?>>UP 4th NEW</option>
															            <option value="UP_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_5th_NEW") echo "selected"; ?>>UP 5th NEW</option>
															            <option value="UP_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_6th_NEW") echo "selected"; ?>>UP 6th NEW</option>
															            <option value="DOWN_PHONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_2nd_NEW") echo "selected"; ?>>DOWN PHONE 2nd NEW</option>
															            <option value="DOWN_PHONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_3rd_NEW") echo "selected"; ?>>DOWN PHONE 3rd NEW</option>
															            <option value="DOWN_PHONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_4th_NEW") echo "selected"; ?>>DOWN PHONE 4th NEW</option>
															            <option value="DOWN_PHONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_5th_NEW") echo "selected"; ?>>DOWN PHONE 5th NEW</option>
															            <option value="DOWN_PHONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_PHONE_6th_NEW") echo "selected"; ?>>DOWN PHONE 6th NEW</option>
															            <option value="UP_PHONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_2nd_NEW") echo "selected"; ?>>UP PHONE 2nd NEW</option>
															            <option value="UP_PHONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_3rd_NEW") echo "selected"; ?>>UP PHONE 3rd NEW</option>
															            <option value="UP_PHONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_4th_NEW") echo "selected"; ?>>UP PHONE 4th NEW</option>
															            <option value="UP_PHONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_5th_NEW") echo "selected"; ?>>UP PHONE 5th NEW</option>
															            <option value="UP_PHONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_PHONE_6th_NEW") echo "selected"; ?>>UP PHONE 6th NEW</option>
															            <option value="DOWN_LAST_NAME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_2nd_NEW") echo "selected"; ?>>DOWN LAST NAME 2nd NEW</option>
															            <option value="DOWN_LAST_NAME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_3rd_NEW") echo "selected"; ?>>DOWN LAST NAME 3rd NEW</option>
															            <option value="DOWN_LAST_NAME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_4th_NEW") echo "selected"; ?>>DOWN LAST NAME 4th NEW</option>
															            <option value="DOWN_LAST_NAME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_5th_NEW") echo "selected"; ?>>DOWN LAST NAME 5th NEW</option>
															            <option value="DOWN_LAST_NAME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_NAME_6th_NEW") echo "selected"; ?>>DOWN LAST NAME 6th NEW</option>
															            <option value="UP_LAST_NAME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_2nd_NEW") echo "selected"; ?>>UP LAST NAME 2nd NEW</option>
															            <option value="UP_LAST_NAME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_3rd_NEW") echo "selected"; ?>>UP LAST NAME 3rd NEW</option>
															            <option value="UP_LAST_NAME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_4th_NEW") echo "selected"; ?>>UP LAST NAME 4th NEW</option>
															            <option value="UP_LAST_NAME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_5th_NEW") echo "selected"; ?>>UP LAST NAME 5th NEW</option>
															            <option value="UP_LAST_NAME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_NAME_6th_NEW") echo "selected"; ?>>UP LAST NAME 6th NEW</option>
															            <option value="DOWN_COUNT_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_2nd_NEW") echo "selected"; ?>>DOWN COUNT 2nd NEW</option>
															            <option value="DOWN_COUNT_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_3rd_NEW") echo "selected"; ?>>DOWN COUNT 3rd NEW</option>
															            <option value="DOWN_COUNT_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_4th_NEW") echo "selected"; ?>>DOWN COUNT 4th NEW</option>
															            <option value="DOWN_COUNT_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_5th_NEW") echo "selected"; ?>>DOWN COUNT 5th NEW</option>
															            <option value="DOWN_COUNT_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_COUNT_6th_NEW") echo "selected"; ?>>DOWN COUNT 6th NEW</option>
															            <option value="UP_COUNT_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_2nd_NEW") echo "selected"; ?>>UP COUNT 2nd NEW</option>
															            <option value="UP_COUNT_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_3rd_NEW") echo "selected"; ?>>UP COUNT 3rd NEW</option>
															            <option value="UP_COUNT_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_4th_NEW") echo "selected"; ?>>UP COUNT 4th NEW</option>
															            <option value="UP_COUNT_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_5th_NEW") echo "selected"; ?>>UP COUNT 5th NEW</option>
															            <option value="UP_COUNT_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_COUNT_6th_NEW") echo "selected"; ?>>UP COUNT 6th NEW</option>
															            <option value="RANDOM_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_2nd_NEW") echo "selected"; ?>>RANDOM 2nd NEW</option>
															            <option value="RANDOM_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_3rd_NEW") echo "selected"; ?>>RANDOM 3rd NEW</option>
															            <option value="RANDOM_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_4th_NEW") echo "selected"; ?>>RANDOM 4th NEW</option>
															            <option value="RANDOM_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_5th_NEW") echo "selected"; ?>>RANDOM 5th NEW</option>
															            <option value="RANDOM_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "RANDOM_6th_NEW") echo "selected"; ?>>RANDOM 6th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 2nd NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 3rd NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 4th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 5th NEW</option>
															            <option value="DOWN_LAST_CALL_TIME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>DOWN LAST CALL TIME 6th NEW</option>
															            <option value="UP_LAST_CALL_TIME_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_2nd_NEW") echo "selected"; ?>>UP LAST CALL TIME 2nd NEW</option>
															            <option value="UP_LAST_CALL_TIME_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_3rd_NEW") echo "selected"; ?>>UP LAST CALL TIME 3rd NEW</option>
															            <option value="UP_LAST_CALL_TIME_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_4th_NEW") echo "selected"; ?>>UP LAST CALL TIME 4th NEW</option>
															            <option value="UP_LAST_CALL_TIME_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_5th_NEW") echo "selected"; ?>>UP LAST CALL TIME 5th NEW</option>
															            <option value="UP_LAST_CALL_TIME_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_LAST_CALL_TIME_6th_NEW") echo "selected"; ?>>UP LAST CALL TIME 6th NEW</option>
															            <option value="DOWN_RANK_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_2nd_NEW") echo "selected"; ?>>DOWN RANK 2nd NEW</option>
															            <option value="DOWN_RANK_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_3rd_NEW") echo "selected"; ?>>DOWN RANK 3rd NEW</option>
															            <option value="DOWN_RANK_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_4th_NEW") echo "selected"; ?>>DOWN RANK 4th NEW</option>
															            <option value="DOWN_RANK_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_5th_NEW") echo "selected"; ?>>DOWN RANK 5th NEW</option>
															            <option value="DOWN_RANK_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_RANK_6th_NEW") echo "selected"; ?>>DOWN RANK 6th NEW</option>
															            <option value="UP_RANK_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_2nd_NEW") echo "selected"; ?>>UP RANK 2nd NEW</option>
															            <option value="UP_RANK_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_3rd_NEW") echo "selected"; ?>>UP RANK 3rd NEW</option>
															            <option value="UP_RANK_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_4th_NEW") echo "selected"; ?>>UP RANK 4th NEW</option>
															            <option value="UP_RANK_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_5th_NEW") echo "selected"; ?>>UP RANK 5th NEW</option>
															            <option value="UP_RANK_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_RANK_6th_NEW") echo "selected"; ?>>UP RANK 6th NEW</option>
															            <option value="DOWN_OWNER_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_2nd_NEW") echo "selected"; ?>>DOWN OWNER 2nd NEW</option>
															            <option value="DOWN_OWNER_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_3rd_NEW") echo "selected"; ?>>DOWN OWNER 3rd NEW</option>
															            <option value="DOWN_OWNER_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_4th_NEW") echo "selected"; ?>>DOWN OWNER 4th NEW</option>
															            <option value="DOWN_OWNER_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_5th_NEW") echo "selected"; ?>>DOWN OWNER 5th NEW</option>
															            <option value="DOWN_OWNER_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_OWNER_6th_NEW") echo "selected"; ?>>DOWN OWNER 6th NEW</option>
															            <option value="UP_OWNER_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_2nd_NEW") echo "selected"; ?>>UP OWNER 2nd NEW</option>
															            <option value="UP_OWNER_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_3rd_NEW") echo "selected"; ?>>UP OWNER 3rd NEW</option>
															            <option value="UP_OWNER_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_4th_NEW") echo "selected"; ?>>UP OWNER 4th NEW</option>
															            <option value="UP_OWNER_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_5th_NEW") echo "selected"; ?>>UP OWNER 5th NEW</option>
															            <option value="UP_OWNER_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_OWNER_6th_NEW") echo "selected"; ?>>UP OWNER 6th NEW</option>
															            <option value="DOWN_TIMEZONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_2nd_NEW") echo "selected"; ?>>DOWN TIMEZONE 2nd NEW</option>
															            <option value="DOWN_TIMEZONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_3rd_NEW") echo "selected"; ?>>DOWN TIMEZONE 3rd NEW</option>
															            <option value="DOWN_TIMEZONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_4th_NEW") echo "selected"; ?>>DOWN TIMEZONE 4th NEW</option>
															            <option value="DOWN_TIMEZONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_5th_NEW") echo "selected"; ?>>DOWN TIMEZONE 5th NEW</option>
															            <option value="DOWN_TIMEZONE_6th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "DOWN_TIMEZONE_6th_NEW") echo "selected"; ?>>DOWN TIMEZONE 6th NEW</option>
															            <option value="UP_TIMEZONE_2nd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_2nd_NEW") echo "selected"; ?>>UP TIMEZONE 2nd NEW</option>
															            <option value="UP_TIMEZONE_3rd_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_3rd_NEW") echo "selected"; ?>>UP TIMEZONE 3rd NEW</option>
															            <option value="UP_TIMEZONE_4th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_4th_NEW") echo "selected"; ?>>UP TIMEZONE 4th NEW</option>
															            <option value="UP_TIMEZONE_5th_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_5th_NEW") echo "selected"; ?>>UP TIMEZONE 5th NEW</option>
															            <option value="UP_TIMEZONE_6TH_NEW" <?php if(str_replace(" ", "_", $campaign->data->lead_order) == "UP_TIMEZONE_6TH_NEW") echo "selected"; ?>>UP TIMEZONE 6th NEW</option>
															        </select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Lead Order Secondary:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="lead_order_secondary" name="lead_order_secondary">
																		<option value="LEAD_ASCEND" <?php if($campaign->data->lead_order_secondary == "LEAD_ASCEND") echo "selected"; ?>>LEAD ASCEND</option>
																		<option value="LEAD_DESCEND" <?php if($campaign->data->lead_order_secondary == "LEAD_DESCEND") echo "selected"; ?>>LEAD DESCEND</option>
																		<option value="CALLTIME_ASCEND" <?php if($campaign->data->lead_order_secondary == "CALLTIME_ASCEND") echo "selected"; ?>>CALLTIME ASCEND</option>
																		<option value="CALLTIME_DESCEND" <?php if($campaign->data->lead_order_secondary == "CALLTIME_DESCEND") echo "selected"; ?>>CALLTIME DESCEND</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("force_reset_of_hopper"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="force_reset_hopper" name="force_reset_hopper">
																		<option value="Y">Y</option>
																		<option value="N" selected>N</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("minimum_hopper_level"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="hopper_level" name="hopper_level">
																		<?php
																		$hopper_level = array (1, 5, 10, 20, 50, 100, 200, 500, 700, 1000, 2000);
																		foreach ($hopper_level as $level) {
																			$selectThis = '';
																			if ($level == $campaign->data->hopper_level) { $selectThis = 'selected';} 
																			echo '<option value="'.$level.'" '.$selectThis.'>'.$level.'</option>';
																		}
																		?>
																	</select>
																</div>
															</div>
															<?php //if($campaign->data->campaign_vdad_exten != 8373) { ?>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("next_agent_call"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="next_agent_call" name="next_agent_call">
																		<option value="RANDOM" <?php if(strtoupper($campaign->data->next_agent_call) == "RANDOM") echo "selected";?>>RANDOM</option>
																		<option value="OLDEST_CALL_START" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_START") echo "selected";?>>OLDEST CALL START</option>
																		<option value="OLDEST_CALL_FINISH" <?php if(strtoupper($campaign->data->next_agent_call) == "OLDEST_CALL_FINISH") echo "selected";?>>OLDEST CALL FINISH</option>
																		<option value="OVERALL_USER_LEVEL" <?php if(strtoupper($campaign->data->next_agent_call) == "OVERALL_USER_LEVEL") echo "selected";?>>OVERALL USER LEVEL</option>
																		<option value="FEWEST_CALLS" <?php if(strtoupper($campaign->data->next_agent_call) == "FEWEST_CALLS") echo "selected";?>>FEWEST CALLS</option>
																		<option value="LONGEST_WAIT_TIME" <?php if(strtoupper($campaign->data->next_agent_call) == "LONGEST_WAIT_TIME") echo "selected";?>>LONGEST WAIT TIME</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("dial_timeout"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="dial_time_out" name="dial_timeout" value="<?php echo $campaign->data->dial_timeout; ?>">
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("agent_lead_search"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select id="agent_lead_search" name="agent_lead_search" class="form-control">
																		<option value="ENABLED" <?php if($campaign->data->agent_lead_search == "ENABLED") echo "selected";?>>ENABLED</option>
																		<option value="DISABLED" <?php if($campaign->data->agent_lead_search == "DISABLED") echo "selected";?>>DISABLED</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("agent_lead_search_method"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select id="agent_lead_search_method" name="agent_lead_search_method" class="form-control">
																		<option value="SYSTEM" <?php if($campaign->data->agent_lead_search_method == "SYSTEM") echo "selected";?>>SYSTEM</option>
																		<option value="CAMPLISTS_ALL" <?php if($campaign->data->agent_lead_search_method == "CAMPLISTS_ALL") echo "selected";?>>CAMPLISTS ALL</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label">Answering Machine Detection:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="campaign_vdad_exten" name="campaign_vdad_exten">
																		<option value="NONE" <?php if($campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																		<option value="8373" <?php if($campaign->data->campaign_vdad_exten == "8373") echo "selected";?>>YES</option>
																		<option value="8366" <?php if($campaign->data->campaign_vdad_exten == "8366") echo "selected";?>>NO</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("answering_machine_message"); ?>:</label>
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
																<label class="col-sm-3 control-label"><?php $lh->translateText("amd_send_to_action"); ?>:</label>
																<div class="col-sm-9 mb">
																	<select class="form-control" id="amd_send_to_vmx" name="amd_send_to_vmx">
																		<option value="Y" <?php if($campaign->data->amd_send_to_vmx == "Y") echo "selected";?>>YES</option>
																		<option value="N" <?php if($campaign->data->amd_send_to_vmx == "N") echo "selected";?>>NO</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("waitforsilence_options"); ?>:</label>
																<div class="col-sm-9 mb">
																	<input type="text" class="form-control" id="waitforsilence_options" name="waitforsilence_options" value="<?php echo $campaign->data->waitforsilence_options; ?>">
																</div>
															</div>
															<?php //} ?>
															<br /><br />
															<?php //if($campaign->data->campaign_vdad_exten != 8373) { ?>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_method"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<select class="form-control" id="survey_method" name="survey_method">
																			<option value="AGENT_XFER" <?php if($campaign->data->survey_method == "AGENT_XFER") echo "selected";?>>CAMPAIGN</option>
																			<option value="EXTENSION" <?php if($campaign->data->survey_method == "EXTENSION") echo "selected";?>>DID</option>
																			<option value="CALLMENU" <?php if($campaign->data->survey_method == "CALLMENU") echo "selected";?>>CALL MENU</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("dial_method"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<select name="dial_method" id="survey_dial_method" class="form-control" name="dial_method">
																			<option value="MANUAL" <?php if($campaign->data->dial_method == "MANUAL") echo "selected";?> disabled>MANUAL</option>
																			<option value="RATIO" <?php if($campaign->data->dial_method == "RATIO") echo "selected";?>>AUTO DIAL</option>
																			<option value="ADAPT_TAPERED" <?php if($campaign->data->dial_method == "ADAPT_TAPERED") echo "selected";?>>PREDICTIVE</option>
																			<option value="INBOUND_MAN" <?php if($campaign->data->dial_method == "INBOUND_MAN") echo "selected";?> disabled>INBOUND MAN</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("autodial_method"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<div class="row">
																			<?php
																				$autodial_level = $campaign->data->auto_dial_level;
																			?>
																			<div class="col-lg-8">
																				<select id="survey_auto_dial_level" class="form-control" name="auto_dial_level" <?php if($campaign->data->dial_method !== "RATIO") echo "disabled";?>>
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
																				<select id="survey_auto_dial_level_adv" class="form-control <?php if($autodial_level == "0" || $autodial_level == "1" || $autodial_level == "2" || $autodial_level == "4" || $autodial_level == "6" || $autodial_level == "10") echo "hide";?> " name="auto_dial_level_adv">

<?php if($server->data->max_vicidial_trunks == NULL){ ?>
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


<?php
        } else {
                for($a=1; $a <= $server->data->max_vicidial_trunks; $a = $a+0.5){
			$b = number_format((float)$a, 1, '.', '');
?>			
                         <option value="<?php echo $b; ?>" <?php if($autodial_level == "$b") echo "selected"; ?> ><?php echo $b; ?></option>
<?php
                }
        }
?>

																				</select>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_call_menu"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<select id="survey_menu_id" name="survey_menu_id" class="form-control">
																			<option value="" <?php if($campaign->data->survey_menu_id == "") echo "selected";?>>-- NONE --</option>
																			<?php for($i=0;$i < count($ivr->menu_id);$i++){ ?>
																					<option value="<?php echo $ivr->menu_id[$i]; ?>" <?php if($campaign->data->survey_menu_id == $ivr->menu_id[$i]) echo "selected";?>><?php echo $ivr->menu_id[$i]." - ".$ivr->menu_name[$i]; ?></option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("did"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_xfer_exten" name="survey_xfer_exten" min="0" value="<?php echo $campaign->data->survey_xfer_exten; ?>">
																	</div>
																</div>
																<?php //if($campaign->campaign_type == "SURVEY" && $campaign->data->survey_method == "AGENT_XFER"){ ?>
																<div class="form-group survey_method_agent_xfer_view">
                        	                                                                                                        <label class="col-sm-3 control-label"><?php $lh->translateText("campaign_recording_filename"); ?>:</label>
                	                                                                                                                <div class="col-sm-9 mb">
        	                                                                                                                                <input type="text" class="form-control" id="campaign_rec_filename" name="campaign_rec_filename" value="<?php echo $campaign->data->campaign_rec_filename; ?>">
	                                                                                                                                </div>
																</div>
																<?php //} ?>
																<br /><br />
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_dtmf_digits"); ?>:</label>
																	<div class="col-sm-5 mb">
																		<input type="number" class="form-control" id="survey_dtmf_digits" name="survey_dtmf_digits" min="0" value="<?php echo $campaign->data->survey_dtmf_digits; ?>">
																	</div>
																	<span class="col-sm-4 control-label">* Customer define key press e.g.0123456789*#</span>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_not_interested_digit"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_ni_digit" name="survey_ni_digit" min="0" maxlength="10" value="<?php if(!empty($campaign->data->survey_ni_digit)){echo $campaign->data->survey_ni_digit;}else{echo "8";} ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_wait_seconds"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_wait_sec" name="survey_wait_sec" min="0" maxlength="10" value="<?php if(!empty($campaign->data->survey_wait_sec)){echo $campaign->data->survey_wait_sec;}else{echo "10";} ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_no_response_action"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<select id="survey_no_response_action" name="survey_no_response_action" class="form-control select2">
																			<option value="OPTIN" <?php if($campaign->data->survey_no_response_action == "OPTIN") echo "selected";?>>OPTIN</option>
																			<option value="OPTOUT" <?php if($campaign->data->survey_no_response_action == "OPTOUT") echo "selected";?>>OPTOUT</option>
																			<option value="DROP" <?php if($campaign->data->survey_no_response_action == "DROP") echo "selected";?>>DROP</option>
																		</select>
																	</div>
																</div>
																
																<!--<div class="form-group">
																	<label class="col-sm-3 control-label">Survey Not interested audio file:</label>
																	<div class="col-sm-7 mb">
																		<input type="text" class="form-control" id="survey_ni_audio_file" name="survey_ni_audio_file" value="<?php if(!empty($campaign->data->survey_ni_audio_file)){echo $campaign->data->survey_ni_audio_file;}else{echo "sip-silence";} ?>">
																	</div>
																	<div class="col-sm-2 mb">
																		<button type="button" class="view-audio-files btn btn-default" data-label="survey_ni_audio_file">Audio</button>
																	</div>
																</div>-->
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_not_interested_audio_file"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<div class="input-group">
																			<input type="text" class="form-control" id="survey_ni_audio_file" name="survey_ni_audio_file" value="<?php if(!empty($campaign->data->survey_ni_audio_file)){echo $campaign->data->survey_ni_audio_file;}else{echo "sip-silence";} ?>">
																			<span class="input-group-btn">
																				<button class="btn btn-default show-view-audio-files" data-label="survey_ni_audio_file" type="button">[Audio Chooser...]</button>
																			</span>
																		</div><!-- /input-group -->
																		<select class="form-control select2 survey_ni_audio_file_dropdown" id="survey_ni_audio_file_dropdown" data-label="survey_ni_audio_file">
																			<option value="">-- Default Value --</option>
            <?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
                <?php if(!empty($voicefiles->file_name[$i])) { ?>
                    <option value="<?php echo substr($voicefiles->file_name[$i], 0, -4); ?>">
                        <?php echo substr($voicefiles->file_name[$i], 0, -4); ?>
                    </option>
<!--old audio chooser-->
<?php //for($i=0;$i<=count($audiofiles->data);$i++) { ?>
<?php //if(!empty($audiofiles->data[$i]) && (strpos($audiofiles->data[$i], "go_") !== false)) { ?>
<!--option value="<?php //echo substr($audiofiles->data[$i], 0, -4); ?>"><?php //echo substr($audiofiles->data[$i], 0, -4); ?></option-->

<?php } ?>
<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_not_interested_status"); ?>:</label>
																	<div class="col-sm-9 mb">
<input type="text" class="form-control" id="survey_ni_status" name="survey_ni_status" value="<?php echo $campaign->data->survey_ni_status; ?>">
<!--dropdown select-->
																	</div>
																</div>
																<br /><br />
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_third_digit"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_third_digit" name="survey_third_digit" min="0" maxlength="10" value="<?php echo $campaign->data->survey_third_digit; ?>">
																	</div>
																</div>
																<!--<div class="form-group">
																	<label class="col-sm-3 control-label">Survey Third Audio File:</label>
																	<div class="col-sm-7 mb">
																		<input type="text" class="form-control" id="survey_third_audio_file" name="survey_third_audio_file" value="<?php if(!empty($campaign->data->survey_third_audio_file)){echo $campaign->data->survey_third_audio_file;}else{echo "sip-silence";} ?>">
																	</div>
																	<div class="col-sm-2 mb">
																		<button type="button" class="view-audio-files btn btn-default" data-label="survey_third_audio_file">Audio</button>
																	</div>
																</div>-->
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_third_audio_file"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<div class="input-group">
																			<input type="text" class="form-control" id="survey_third_audio_file" name="survey_third_audio_file" value="<?php if(!empty($campaign->data->survey_third_audio_file)){echo $campaign->data->survey_third_audio_file;}else{echo "sip-silence";} ?>">
																			<span class="input-group-btn">
																				<button class="btn btn-default show-view-audio-files" data-label="survey_third_audio_file" type="button">[Audio Chooser...]</button>
																			</span>
																		</div><!-- /input-group -->
																		<select class="form-control select2 survey_third_audio_file_dropdown" id="survey_third_audio_file_dropdown" data-label="survey_third_audio_file">
																			<option value="">-- Default Value --</option>
            <?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
                <?php if(!empty($voicefiles->file_name[$i])) { ?>
                    <option value="<?php echo substr($voicefiles->file_name[$i], 0, -4); ?>">
                        <?php echo substr($voicefiles->file_name[$i], 0, -4); ?>
                    </option>
<?php //for($i=0;$i<=count($audiofiles->data);$i++) { ?>
<?php //if(!empty($audiofiles->data[$i]) && (strpos($audiofiles->data[$i], "go_") !== false)) { ?>
<!--option value="<?php //echo substr($audiofiles->data[$i], 0, -4); ?>"><?php //echo substr($audiofiles->data[$i], 0, -4); ?></option-->
<?php } ?>
<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_third_status"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="text" class="form-control" id="survey_third_status" name="survey_third_status" value="<?php echo $campaign->data->survey_third_status; ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_third_extension"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_third_exten" name="survey_third_exten" min="0" value="<?php if(!empty($campaign->data->survey_third_exten)){echo $campaign->data->survey_third_exten;}else{echo "8300";} ?>">
																	</div>
																</div>
																<br /><br />
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_fourth_digit"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_fourth_digit" maxlength="10" name="survey_fourth_digit" min="0" value="<?php echo $campaign->data->survey_fourth_digit; ?>">
																	</div>
																</div>
																<!--<div class="form-group">
																	<label class="col-sm-3 control-label">Survey Fourth Audio File:</label>
																	<div class="col-sm-7 mb">
																		<input type="text" class="form-control" id="survey_fourth_audio_file" name="survey_fourth_audio_file" value="<?php if(!empty($campaign->data->survey_fourth_audio_file)){echo $campaign->data->survey_fourth_audio_file;}else{echo "sip-silence";} ?>">
																	</div>
																	<div class="col-sm-2 mb">
																		<button type="button" class="view-audio-files btn btn-default" data-label="survey_fourth_audio_file">Audio</button>
																	</div>
																</div>-->
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_fourth_audio_file"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<div class="input-group">
																			<input type="text" class="form-control" id="survey_fourth_audio_file" name="survey_fourth_audio_file" value="<?php if(!empty($campaign->data->survey_fourth_audio_file)){echo $campaign->data->survey_fourth_audio_file;}else{echo "sip-silence";} ?>">
																			<span class="input-group-btn">
																				<button class="btn btn-default show-view-audio-files" data-label="survey_fourth_audio_file" type="button">[Audio Chooser...]</button>
																			</span>
																		</div><!-- /input-group -->
																		<select class="form-control select2 survey_fourth_audio_file_dropdown" id="survey_fourth_audio_file_dropdown" data-label="survey_fourth_audio_file">
																			<option value="">-- Default Value --</option>
            <?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
                <?php if(!empty($voicefiles->file_name[$i])) { ?>
                    <option value="<?php echo substr($voicefiles->file_name[$i], 0, -4); ?>">
                        <?php echo substr($voicefiles->file_name[$i], 0, -4); ?>
                    </option>
<?php //for($i=0;$i<=count($audiofiles->data);$i++) { ?>
<?php //if(!empty($audiofiles->data[$i]) && (strpos($audiofiles->data[$i], "go_") !== false)) { ?>
<!--option value="<?php //echo substr($audiofiles->data[$i], 0, -4); ?>"><?php //echo substr($audiofiles->data[$i], 0, -4); ?></option-->
<?php } ?>
																			<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_fourth_status"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="text" class="form-control" id="survey_fourth_status" name="survey_fourth_status" value="<?php echo $campaign->data->survey_fourth_status; ?>">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-3 control-label"><?php $lh->translateText("survey_fourth_extension"); ?>:</label>
																	<div class="col-sm-9 mb">
																		<input type="number" class="form-control" id="survey_fourth_exten" name="survey_fourth_exten" min="0" value="<?php if(!empty($campaign->data->survey_fourth_exten)){echo $campaign->data->survey_fourth_exten;}else{echo "8300";} ?>">
																	</div>
																</div>
															<?php //} ?>
														<?php } else { ?>
															<!--Default-->

														<?php }
														
														if ($campaign->campaign_type != "SURVEY") {
														?>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("agent_lead_search"); ?>:</label>
															<div class="col-sm-9 mb">
																<select id="agent_lead_search" name="agent_lead_search" class="form-control select2">
																	<option value="ENABLED" <?php if($campaign->data->agent_lead_search == "ENABLED") echo "selected";?>>ENABLED</option>
																	<option value="DISABLED" <?php if($campaign->data->agent_lead_search == "DISABLED") echo "selected";?>>DISABLED</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label"><?php $lh->translateText("agent_lead_search_method"); ?>:</label>
															<div class="col-sm-9 mb">
																<select id="agent_lead_search_method" name="agent_lead_search_method" class="form-control select2">
																	<option value="SYSTEM" <?php if($campaign->data->agent_lead_search_method == "SYSTEM") echo "selected";?>>SYSTEM</option>
																	<option value="CAMPLISTS_ALL" <?php if($campaign->data->agent_lead_search_method == "CAMPLISTS_ALL") echo "selected";?>>CAMPLISTS ALL</option>
																</select>
															</div>
														</div>
														<div class="form-group<?=($campaign->enable_callback_alert === '' ? ' hidden': '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("enable_callback_alert"); ?>:</label>
															<div class="col-sm-9 mb">
																<select id="enable_callback_alert" name="enable_callback_alert" class="form-control select2">
																	<option value="0" <?php if($campaign->enable_callback_alert == 0) echo "selected";?>>DISABLED</option>
																	<option value="1" <?php if($campaign->enable_callback_alert == 1) echo "selected";?>>ENABLED</option>
																</select>
															</div>
														</div>
														<div class="form-group<?=($campaign->cb_noexpire === '' ? ' hidden': '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("cb_noexpire"); ?>:</label>
															<div class="col-sm-9 mb">
																<select id="cb_noexpire" name="cb_noexpire" class="form-control select2">
																	<option value="0" <?php if($campaign->cb_noexpire == 0) echo "selected";?>>DISABLED</option>
																	<option value="1" <?php if($campaign->cb_noexpire == 1) echo "selected";?>>ENABLED</option>
																</select>
															</div>
														</div>
														<div class="form-group<?=($campaign->cb_sendemail === '' ? ' hidden': '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("cb_sendemail"); ?>:</label>
															<div class="col-sm-9 mb">
																<select id="cb_sendemail" name="cb_sendemail" class="form-control select2">
																	<option value="0" <?php if($campaign->cb_sendemail == 0) echo "selected";?>>DISABLED</option>
																	<option value="1" <?php if($campaign->cb_sendemail == 1) echo "selected";?>>ENABLED</option>
																</select>
															</div>
														</div>
														<?php $googleAPIKey = $ui->getSettingsAPIKey('google'); ?>
														<div class="form-group<?=(empty($googleAPIKey) ? ' hidden' : '')?>" style="margin-bottom: 10px;">
															<?php $google_sheet_ids = explode(" ", rtrim($campaign->google_sheet_ids, " -")); $i=1;?>
															<?php foreach($google_sheet_ids as $google_sheet_id) { ?>
																<?php if(!empty($google_sheet_id)) { ?>
																	<label class="col-sm-3 control-label"><?php $lh->translateText("google_sheet_id"); ?> <?php echo $i; ?>:</label>
																	<span class="col-sm-8 control-label" style="text-align: left;">
																		<span><?php echo $google_sheet_id; ?></span>
																	</span>
																	<span class="col-sm-1 control-label">
																		<a href="#" class="remove-this-google-sheet-id"  data-campaign="<?php echo $campaign_id; ?>" data-google-sheet-ids="<?php echo $campaign->google_sheet_ids;?>" data-selected-sheet-id="<?php echo $google_sheet_id; ?>">Remove</a>
																	</span>
																<?php $i++; ?>
																<?php } ?>
															<?php } ?>
														</div>
														<div class="form-group<?=((empty($googleAPIKey) || $i > 5) ? ' hidden' : '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("google_sheet_id"); ?>:</label>
															<div class="col-sm-8 mb">
																<input type="text" class="form-control" id="google_sheet_id" name="google_sheet_id" value="" placeholder="<?php $lh->translateText("enter_google_sheet_id"); ?>">
															</div>
															<div class="col-sm-1 mb">
																<button type="button" class="btn btn-default btn-add-google-sheet-id" data-campaign="<?php echo $campaign_id; ?>" data-google-sheet-ids="<?php echo $campaign->google_sheet_ids;?>">Add</button>
															</div>
														</div>
														<div class="form-group<?=(empty($googleAPIKey) ? ' hidden' : '')?>">
															<label class="col-sm-3 control-label"><?php $lh->translateText("google_sheet_list_id"); ?>:</label>
															<div class="col-sm-9 mb">
																<select id="google_sheet_list_id" name="google_sheet_list_id" class="form-control select2">
																	<option value="">--- NONE ---</option>
																	<?php
																	foreach ($campaign->campaign_list_ids as $list_id => $list_name) {
																		$isSelected = ($campaign->google_sheet_list_id == $list_id) ? ' selected' : '';
																		echo '<option value="'.$list_id.'"'.$isSelected.'>'.$list_id.' - '.$list_name.'</option>';
																	}
																	?>
																</select>
															</div>
														</div>
														<?php } ?>
														<div class="campaign_allow_inbound_div hide">
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("inbound_groups"); ?>:</label>
																<div class="col-sm-9 mb">
																	<?php for($i=0;$i<=count($ingroups->group_id);$i++) { ?>
																		<?php if(!empty($ingroups->group_id[$i])) {?>
																			<input type="checkbox" name="closer_campaigns[]" value="<?php echo $ingroups->group_id[$i]?>" <?php if(in_array($ingroups->group_id[$i],explode(" ", $campaign->data->closer_campaigns))) echo "checked";?>>&nbsp;<?php echo $ingroups->group_id[$i]." - ".$ingroups->group_name[$i]; ?><br />
																		<?php } ?>
																	<?php } ?>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-3 control-label"><?php $lh->translateText("allowed_transfer_groups"); ?>:</label>
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
											   	<div id="modifyCAMPAIGNresult"></div>

											   	<!-- FOOTER BUTTONS -->
							                    <fieldset class="footer-buttons">
							                        <div class="box-footer">
							                           <div class="col-sm-3 pull-right">
																<a href="telephonycampaigns.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i><?php $lh->translateText("cancel"); ?></a>

							                                	<button type="submit" class="btn btn-primary" id="modifyCampaignOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i><?php $lh->translateText("update"); ?></span></button>
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
								if ($disposition->result == "success") {
							?>

					            <!-- /.box-header -->
					            <div class="box-body table-responsive no-padding">
					              <table id="table_edit_disposition" class="table table-hover">
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
					                	<th><?php $lh->translateText("status"); ?> </th>
										<th><?php $lh->translateText("status_name"); ?></th>
										<!-- <th><?php $lh->translateText("priority"); ?></th>
										<th><?php $lh->translateText("color"); ?></th> -->
										<th class="head_custom_statuses"> <?php $lh->translateText("selectable"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("human_answered"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("sale"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("dnc"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("customer_contact"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("not_interested"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("unworkable"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("scheduled_callback"); ?> </th>
										<th class="head_custom_statuses"> <?php $lh->translateText("action"); ?> </th>
					                </tr>
					            	</thead>
					                <tbody>
								<?php
										for($i=0;$i < count($disposition->status);$i++){
								?>
									<tr>
										<td>
											<?php echo $disposition->status[$i];?>
										</td>
										<td>
											<?php echo $disposition->status_name[$i];?>
										</td>
										<!-- <td>
											<?php echo $disposition->priority[$i];?>
										</td>
										<td>
											<div style="border: 1px solid #888; background-color: <?php echo $disposition->color[$i];?>; width: 40px;">&nbsp; &nbsp;</div>
										</td> -->
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
											<a class="edit_disposition btn btn-primary" href="#" data-toggle="modal" data-target="#edit_disposition_modal" data-id="<?php echo $disposition->campaign_id;?>" data-status ="<?php echo $disposition->status[$i];?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
											<a class="delete_disposition btn btn-danger" href="#" data-id="<?php echo $disposition->campaign_id;?>" data-status ="<?php echo $disposition->status[$i];?>" data-name="<?php echo $disposition->status_name[$i];?>"><i class="fa fa-trash"></i></a>
											</center>
										</td>
									</tr>

								<?php
										}
								?>
								<!-- ADD A NEW STATUS -->
									<tr><td colspan="13" style="background: #ecf0f5; font-size: 5px;">&nbsp;</td></tr>
									<tr style="border-top: 1px solid #f4f4f4;">
										<td>
											<input type="text" name="add_status" id="add_status" class="" placeholder="Status" maxlength="6">
											<br/><small><label id="status-duplicate-error"></label></small>
										</td>
										<td>
											<input type="text" name="add_status_name" id="add_status_name" class="" placeholder="Status Name">
										</td>
										<!-- <td>
											<select name="add_priority" id="add_priority" class="" placeholder="Priority">
												<?php
												for ($x=1;$x<=10;$x++) {
													echo "<option value='$x'>$x</option>\n";
												}
												?>
											</select>
										</td>
										<td>
											<input type="text" name="add_color" id="add_color" spellcheck="false" value="#b5b5b5" style="background-color: #b5b5b5; color: #b5b5b5; padding: 0;" class="add_color" placeholder="Color" size="1">
										</td> -->
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
											<a type="button" id="add_new_status" data-id="<?php echo $did;?>" class="btn btn-primary" disabled><span id="add_button"><i class="fa fa-plus"></i> <?php $lh->translateText("create_new_status"); ?></span></a>
										</td>
									</tr>

									</tbody>
					              </table>

					              	<div class="box-footer pull-right">
										<a href="#" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-remove"></i> <?php $lh->translateText("cancel"); ?></a>
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
						?>
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

							                		<label class="col-sm-3 control-label" for="status"><?php $lh->translateText("status"); ?>:</label>
							                        <div class="col-sm-9">
							                            <input type="text" name="edit_status" id="edit_status" class="form-control" placeholder="Status" minlength="1" maxlenght="6" required readonly>
							                            <br/><small><label id="status-duplicate-error"></label></small>
							                    	</div>
								                </div>
								                <div class="form-group">
								                	<label class="col-sm-3 control-label" for="status_name"> <?php $lh->translateText("status_name"); ?>: </label>
							                        <div class="col-sm-9 mb">
							                            <input type="text" name="edit_status_name" id="edit_status_name" class="form-control" placeholder="Status Name" maxlenght="30" required>
							                        </div>
								                </div>
												<!-- <div class="form-group">
													<label class="col-sm-3 control-label" for="edit_priority"><?php $lh->translateText("priority"); ?></label>
													<div class="col-sm-9 mb">
														<select id="edit_priority" name="edit_priority" class="form-control">
															<?php
															for ($i=1; $i<=10; $i++) {
																echo "<option value='$i'>$i</option>\n";
															}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="edit_color"><?php $lh->translateText("color"); ?></label>
													<div class="col-sm-9 mb">
														<div id="status-color" data-format="alias" class="input-group colorpicker-component">
															<input type="text" name="edit_color" id="edit_color" class="form-control" placeholder="<?php $lh->translateText("color"); ?> (eg. #FFFFFF or white)" value="" maxlength="20" required>
															<span class="input-group-addon"><i></i></span>
														</div>
													</div>
												</div> -->
								                <div class="form-group">
										                        <!--<label class="col-sm-2 control-label" for="grouplevel" style="padding-top:15px;"> </label>-->
								                    <div class="col-lg-1">
								                   	</div>
								                    <div class="col-lg-11 mt">
								                    	<div class="row mb">
								                    		<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_selectable">
																<input type="checkbox" id="edit_selectable" name="edit_selectable" checked>
																<span class="fa fa-check"></span> <?php $lh->translateText("selectable"); ?> 
															</label>
															<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_human_answered">
																<input type="checkbox" id="edit_human_answered" name="edit_human_answered">
																<span class="fa fa-check"></span> <?php $lh->translateText("human_answered"); ?> 
															</label>
															<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_sale">
																<input type="checkbox" id="edit_sale" name="edit_sale">
																<span class="fa fa-check"></span> <?php $lh->translateText("sale"); ?> 
															</label>
												        </div>
												        <div class="row mb">
												        	<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_dnc">
																<input type="checkbox" id="edit_dnc" name="edit_dnc">
																<span class="fa fa-check"></span> <?php $lh->translateText("dnc"); ?> 
															</label>
															<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_customer_contact">
																<input type="checkbox" id="edit_customer_contact" name="edit_customer_contact">
																<span class="fa fa-check"></span> <?php $lh->translateText("customer_contact"); ?> 
															</label>
															<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_not_interested">
																<input type="checkbox" id="edit_not_interested" name="edit_not_interested">
																<span class="fa fa-check"></span> <?php $lh->translateText("not_interested"); ?> 
															</label>
											            </div>
												        <div class="row mb">
												        	<label class="col-sm-3 checkbox-inline c-checkbox" for="edit_unworkable">
																<input type="checkbox" id="edit_unworkable" name="edit_unworkable">
																<span class="fa fa-check"></span> <?php $lh->translateText("unworkable"); ?> 
															</label>
															<label class="col-sm-4 checkbox-inline c-checkbox" for="edit_scheduled_callback">
																<input type="checkbox" id="edit_scheduled_callback" name="edit_scheduled_callback">
																<span class="fa fa-check"></span> <?php $lh->translateText("scheduled_callback"); ?> 
															</label>
											            </div>
								                    </div>
							                    </div>
							            	</form>
						                </div>
						                <div class="modal-footer">
						                	<div class="col-sm-5 pull-right">
						                		<button type="button" class="btn btn-danger" id="cancel_edit" data-dismiss="modal"><i class='fa fa-remove'></i> <?php $lh->translateText("cancel"); ?></button>
						                    	<button type="button" class="btn btn-primary" id="modify_disposition"><span id="update_button"><i class='fa fa-check'></i> <?php $lh->translateText("update"); ?></span></button>
						              		</div>
						              	</div>

						            </div>
						        </div>
						    </div>
						<?php
							}// end of disposition

							// ---- IF Lead Recycling alex
							if($leadrecycling_id != NULL){
								$leadrecycling = $api->API_getLeadRecyclingInfo($leadrecycling_id);
						?>
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
						                	<th><?php $lh->translateText("recycle_id"); ?> </th>
											<th><?php $lh->translateText("status_name"); ?></th>
											<th class="head_custom_statuses"><?php $lh->translateText("attempt_delay"); ?></th>
											<th class="head_custom_statuses"><?php $lh->translateText("attempt_maximum"); ?></th>
											<th class="head_custom_statuses"> <?php $lh->translateText("active"); ?> </th>
											<th class="head_custom_statuses"> <?php $lh->translateText("action"); ?> </th>
						                </tr>
				            		</thead>
				               		<tbody>
							<?php
								for($i=0;$i < count($leadrecycling->data);$i++){
									if($leadrecycling->data->campaign_id === $leadrecycling_id){
							?>
									<tr>
										<td>
											<?php echo $leadrecycling->data->recycle_id;?>
										</td>
										<td>
											<?php echo $leadrecycling->data->status;?>
										</td>
										<td class="custom_statuses">
											<?php echo $leadrecycling->data->attempt_delay;?>
										</td>
										<td class="custom_statuses">
											<?php echo $leadrecycling->data->attempt_maximum;?>
										</td>
										<td class="custom_statuses">
											<?php if($leadrecycling->data->active == "Y")echo "Active"; else echo "Inactive";?>
										</td>

										
									<!-- ACTION BUTTONS -->
										<td><center>
											<a class="edit_leadrecycling btn btn-primary" href="#" data-toggle="modal" data-target="#edit_leadrecycling_modal" 
												data-id="<?php echo $leadrecycling->data->recycle_id;?>"
												data-status="<?php echo $leadrecycling->data->status;?>" 
												data-attemptdelay="<?php echo $leadrecycling->data->attempt_delay;?>"
												data-attemptmaximum="<?php echo $leadrecycling->data->attempt_maximum;?>" 
												data-active="<?php echo $leadrecycling->data->active;?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
											<a class="delete_leadrecycling btn btn-danger" href="#" data-id="<?php echo $leadrecycling->data->recycle_id;?>"><i class="fa fa-trash"></i></a>
											</center>
										</td>
									</tr>
							<?php
									}
								}//end loop
							?>
								</tbody>
				              	</table>
				              	<div class="box-footer pull-right">
									<a href="#" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-remove"></i> <?php $lh->translateText("cancel"); ?></a>
								</div>
				            </div>
							<!-- EDIT LEAD RECYCLING MODAL -->
						    <div id="edit_leadrecycling_modal" class="modal fade">
						        <div class="modal-dialog">
						            <div class="modal-content">
						                <div class="modal-header">
						                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
						                    <h4 class="modal-title animate-header" id="ingroup_modal"><b>Modify Lead Recycling ID: <span id="recycleid_edit"></span> </b></h4>
						                </div>
						                <div class="modal-body" style="background:#fff;">
						                	<form id="modifyleadrecycling_form">
						                		<div class="form-group mt mb">
						                			<div class="row">&nbsp;</div>
						                		</div>
								                <input type="hidden" name="recycleid" id="recycleid">
								                
								                <div class="form-group">
								                	<label class="col-sm-3 control-label" for="status"> <?php $lh->translateText("status"); ?>: </label>
							                        <div class="col-sm-9 mb">
							                            <input type="text" id="status" class="form-control" value="" disabled>
							                        </div>
								                </div>
								                <div class="form-group">
								                	<label class="col-sm-3 control-label" for="attempt_delay"> <?php $lh->translateText("attempt_delay"); ?>: </label>
							                        <div class="col-sm-9 mb">
							                            <input type="number" class="form-control" id="attempt_delay" name="attempt_delay" maxlength="5" min="120" max="32400" value="1800">
							                        </div>
								                </div>
								                
												<div class="form-group">
													<label class="col-sm-3 control-label" for="attempt_maximum"><?php $lh->translateText("attempt_maximum"); ?></label>
													<div class="col-sm-9 mb">
														<select id="attempt_maximum" name="attempt_maximum" class="form-control">
															<?php
															for ($i=1; $i<=10; $i++) {
																echo "<option value='$i'>$i</option>\n";
															}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label" for="active"><?php $lh->translateText("active"); ?></label>
													<div class="col-sm-9 mb">
														<select id="active" name="active" class="form-control">
															<option value="Y" id="active_y"><?php $lh->translateText("yes"); ?></option>
															<option value="N" id="active_n"><?php $lh->translateText("no"); ?></option>
														</select>
													</div>
												</div>
							            	</form>
						                </div>
						                <div class="modal-footer">
						                	<div class="col-sm-5 pull-right">
						                		<button type="button" class="btn btn-danger" id="cancel_edit" data-dismiss="modal"><i class='fa fa-remove'></i> <?php $lh->translateText("cancel"); ?></button>
						                    	<button type="button" class="btn btn-primary" id="modify_leadrecycling"><span id="update_button"><i class='fa fa-check'></i> <?php $lh->translateText("update"); ?></span></button>
						              		</div>
						              	</div>

						            </div>
						        </div>
						    </div>

							<?php
								}
							?>
									
							<?php
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
			
			function checkSurveyMethod(value) {
				if (value == "AGENT_XFER") {
					$("#survey_dial_method").prop("disabled", false);
					$("#survey_auto_dial_level").prop("disabled", false);
					$("#no-channels").prop("disabled", true);
					$(".survey_method_agent_xfer_view").removeClass('hide');
				}else{
					$("#survey_dial_method").val("RATIO").trigger('change');
					$("#survey_auto_dial_level").val("SLOW").trigger('change');
					$("#survey_dial_method").prop("disabled", true);
					$("#survey_auto_dial_level").prop("disabled", true);
					$("#survey_auto_dial_level_adv").addClass('hide');
					$("#no-channels").prop("disabled", false);
					$(".survey_method_agent_xfer_view").addClass('hide');
				}
				
            }
			
			$(document).ready(function() {
				$("#default_country_code").on('change', function(e) {
					var thisOption = e.target.options[e.target.selectedIndex];
					$("#flag").attr('class', 'flag flag-'+$(thisOption).data('tld'));
				});
			
			$('.select').select2({ theme: 'bootstrap' });
			$.fn.select2.defaults.set( "theme", "bootstrap" );
				
				// update dial_status entries
				$(document).on('click', '#advanced_settings_tab', function(){
					var campaign_id = $(this).data('id');
					console.log(campaign_id);				
					$.ajax({
						url: "./php/GetDialStatuses.php",
						type: 'POST',
						data: {
							campaign_id : campaign_id
						},
						dataType: 'json',
						success: function(data) {
							//console.log(data);
							var optNone = '<option value="" selected>NONE</option>';
							$('#dial_status').html(optNone + data);
							$('#dial_status').val("").trigger("change");
						}
					});
				});

				
				$("#add_color").colorpicker();
				$('#add_color').colorpicker().on('changeColor', function(e) {
					$('#add_color')[0].style.backgroundColor = e.color.toString('rgba');
					$('#add_color')[0].style.color = e.color.toString('rgba');
				});
				
				var surveyMethod = $('#survey_method').val();
				checkSurveyMethod(surveyMethod);
				
				$(document).on('change', '#survey_method', function(){
					var surveyMethod = $(this).val();
					checkSurveyMethod(surveyMethod);
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
							console.log(response);
							$('#modal_view_audio_file').modal('show');
							$('#audio_file_list_container').html(response);
							$('#audio_file_list_container').attr("data-target", label);
						}
					});
				});
				//add am_message_chooser
				$('.survey_first_audio_file_dropdown').hide();
				$('.survey_ni_audio_file_dropdown').hide();
				$('.am_message_chooser').hide();
				$('.survey_third_audio_file_dropdown').hide();
				$('.survey_fourth_audio_file_dropdown').hide();
				$('.show-view-audio-files').on('click', function(event) {
					var targetDropdown = $(this).data('label');
					$('.' + targetDropdown + '_dropdown').toggle('show');
				});
				
				$(document).on('change', '.survey_first_audio_file_dropdown, .survey_ni_audio_file_dropdown, .am_message_chooser, .survey_third_audio_file_dropdown, .survey_fourth_audio_file_dropdown', function(){
					var AudioText = $(this).val();
					var targetText = $(this).data('label');
					
					$('#' + targetText).val(AudioText);
					$(this).hide();
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
						title: "<?php $lh->translateText("are_you_sure"); ?>?",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
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
											campaign_id: campaign_id,
											dial_status: dial_status,
											old_dial_status: old_dial_status
									},
									dataType: 'json',
									success: function(data) {
									console.log(data);
										if(data == 1){
											swal({
													title: "<?php $lh->translateText("success"); ?>",
													text: "<?php $lh->translateText("campaign_dial_status_succesfully_updated"); ?>!",
													type: "success"
												},
												function(){
													location.reload();
											});
										}else{
											sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
										}
									}
								});
								} else {
										swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
								}
						}
					);
				});

				$(document).on('click', '.remove-this-dial-status', function(){
					var campaign_id = $(this).data('campaign');
					var dial_status = $(this).data('dial-status');
					var selected_status = $(this).data('selected-status');

					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>?",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",
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
														selected_status:selected_status
												},
												// dataType: 'json',
												success: function(data) {
												// console.log(data);
														if(data == 1){
															swal({
																	title: "<?php $lh->translateText("success"); ?>",
																	text: "<?php $lh->translateText("campaign_dial_status_succesfully_updated"); ?>!",
																	type: "success"
																},
																function(){
																	location.reload();
																}
															);
														}else{
																sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
														}
												}
										});
								} else {
										swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
								}
						}
					);
				});

				$(document).on('click', '.btn-add-google-sheet-id', function(){
					var google_sheet_id = $('#google_sheet_id').val();
					var campaign_id = $(this).data('campaign');
					var old_google_sheet_ids = $(this).data('google-sheet-ids');

					if (google_sheet_id !== '') {
						if (/^[0-9a-zA-Z_-]+$/g.test(google_sheet_id)) {
							swal({
								title: "<?php $lh->translateText("are_you_sure"); ?>?",
								text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
								type: "warning",
								showCancelButton: true,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes, add the google sheet id!",
								cancelButtonText: "No, cancel please!",
								closeOnConfirm: false,
								closeOnCancel: false
								},
								function(isConfirm){
									if (isConfirm) {
										$.ajax({
											url: "./php/AddGoogleSheet.php",
											type: 'POST',
											data: {
													campaign_id:campaign_id,
													google_sheet_id:google_sheet_id,
													old_google_sheet_ids:old_google_sheet_ids
											},
											dataType: 'json',
											success: function(data) {
											console.log(data);
												if(data == 1){
													swal({
															title: "<?php $lh->translateText("success"); ?>",
															text: "<?php $lh->translateText("campaign_google_sheet_ids_successfully_updated"); ?>!",
															type: "success"
														},
														function(){
															location.reload();
													});
												}else{
													sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
												}
											}
										});
										} else {
												swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
										}
								}
							);
						} else {
							swal("Error", "<?php $lh->translateText("you_have_entered_an_invalid_id"); ?> :)", "error");
						}
					} else {
						swal("Error", "<?php $lh->translateText("you_have_not_entered_an_id"); ?> :)", "error");
					}
				});

				$(document).on('click', '.remove-this-google-sheet-id', function(){
					var campaign_id = $(this).data('campaign');
					var google_sheet_ids = $(this).data('google-sheet-ids');
					var selected_sheet_id = $(this).data('selected-sheet-id');
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';

					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>?",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, remove the google sheet id!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
												url: "./php/DeleteGoogleSheet.php",
												type: 'POST',
												data: {
														campaign_id:campaign_id,
														google_sheet_ids:google_sheet_ids,
														selected_sheet_id:selected_sheet_id,
														log_user: log_user,
														log_group: log_group
												},
												// dataType: 'json',
												success: function(data) {
												// console.log(data);
														if(data == 1){
															swal({
																	title: "<?php $lh->translateText("success"); ?>",
																	text: "<?php $lh->translateText("campaign_google_sheet_ids_successfully_updated"); ?>!",
																	type: "success"
																},
																function(){
																	location.reload();
																}
															);
														}else{
																sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
														}
												}
										});
								} else {
										swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
								}
						}
					);
				});
				/**************
				** Init
				**************/

					//init cancel msg
						$(document).on('click', '#cancel_edit', function(){
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
						});
						$(document).on('click', '#cancel', function(){
							swal({title: "Cancelled",text: "<?php $lh->translateText("cancel_msg"); ?> :)",type: "error"},function(){window.location.href = 'telephonycampaigns.php';});
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
						/*$('#modifyCampaignOkButton').click(function(){
							$('#campaign_form_edit').submit();
						});*/

		$('#modifyCampaignOkButton').click(function(){ // on click submit
			$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
			$('#modifyCampaignOkButton').prop("disabled", true);

			// validations
			$.ajax({
				url: "./php/ModifyTelephonyCampaign.php",
				type: 'POST',
				data: $("#campaign_form_edit").serialize(),
				dataType: "json",
				success: function(data) {
					console.log(data);
					//console.log($("#campaign_form_edit").serialize());
					if (data == 1) {
						$('#update_button').html("<i class='fa fa-check'></i> Update");
						$('#modifyCampaignOkButton').prop("disabled", false);
						swal(
							{
								title: "<?php $lh->translateText("success"); ?>",
								text: "<?php $lh->translateText("user_update_success"); ?>",
								type: "success"
							},
							function(){
								location.replace("./telephonycampaigns.php");
							}
						);
					} else {
						sweetAlert("<?php $lh->translateText("oops"); ?>", "<?php $lh->translateText("something_went_wrong"); ?> " + data, "error");
						$('#update_button').html("<i class='fa fa-check'></i> Update");
						$('#modifyCampaignOkButton').prop("disabled", false);
					}
				}
			});
			return false;
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

					    $('#no-channels').focusout(function(){
                                                var noChannels = $(this).val();
                                                if(noChannels == ""){
                                                        $(this).val("1");
                                                }
                                            });


				/*************
				** Disposition Events
				*************/

			   		//Add Status
				    /*    $('#add_new_status').click(function(){

					        $('#add_button').html("<i class='fa fa-check'></i> Saving...");
							$('#add_new_status').attr("disabled", true);

					        var validate = 0;
					        var status = $("#add_status").val();
					        var status_name = $("#add_status_name").val();

					        if(status === ""){
					            validate = 1;
					        }

					        if(status_name === ""){
					            validate = 1;
					        }

							if(validate === 0){
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
										log_group: log_group,
										priority: $("#add_priority").val(),
										color: $("#add_color").val(),
										type: 'CUSTOM'
									},
									success: function(data) {
									  // console.log(data);
										  if(data == 1){
												swal(
													{
														title: "",
														text: "",
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
				        });*/

					// GET DETAILS FOR EDIT DISPOSITION
					/*	$(document).on('click','.edit_disposition',function() {
							var id = $(this).attr('data-id');
							var status = $(this).attr('data-status');


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
								
								$('#edit_priority').val(data.priority);
								$('#edit_color').val(data.color);
								$("#status-color").colorpicker();

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
						});*/
					
					//edit disposition
					/*	$(document).on('click','#modify_disposition',function() {
							$('#update_button').html("<i class='fa fa-edit'></i> Updating...");
							$('#modify_disposition').attr("disabled", true);

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
										log_group: log_group,
										// priority: $('#edit_priority').val(),
										// color: $('#edit_color').val(),
										type: 'CUSTOM'
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
						});*/
					
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

					$(document).on('change', '#auto_dial_level',function(){
						if($(this).val() == 'ADVANCE'){
							$('#auto_dial_level_adv').removeClass('hide');
						}else{
							$('#auto_dial_level_adv').addClass('hide');
						}
					});
					
					$(document).on('change', '#survey_auto_dial_level',function(){
						if($(this).val() == 'ADVANCE'){
							$('#survey_auto_dial_level_adv').removeClass('hide');
						}else{
							$('#survey_auto_dial_level_adv').addClass('hide');
						}
					});

					//delete disposition
				        $(document).on('click','.delete_disposition', function() {
				            var id = $(this).attr('data-id');
				            var status = $(this).attr('data-status');
				            var campaign_id = $(this).attr('data-campaign');
							var log_user = '<?=$_SESSION['user']?>';
							var log_group = '<?=$_SESSION['usergroup']?>';
				            swal({
				            	title: "<?php $lh->translateText("are_you_sure"); ?>?",
				            	text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
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
					                            campaign_id: campaign_id,
					                            status: status,
												log_user: log_user,
												log_group: log_group
					                        },
					                        success: function(data) {
					                        console.log(data);
					                            if(data == 1){
					                            	swal({
															title: "<?php $lh->translateText("success"); ?>",
															text: "<?php $lh->translateText("disposition_delete"); ?>!",
															type: "success"
														},
														function(){
															location.reload();
															$(".preloader").fadeIn();
														}
													);
					                            }else{
					                                sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
					                            }
					                        }
					                    });
													} else {
				                			swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
				                	}
				            	}
				            );
				        });
				// ----------------- end of disposition


			// GET DETAILS FOR LEAD RECYCLING
				$(document).on('click','.edit_leadrecycling',function() {
					var id = $(this).attr('data-id');
					var status = $(this).attr('data-status');
					var attempt_maximum = $(this).attr('data-attemptmaximum');
					var attempt_delay = $(this).attr('data-attemptdelay');
					var active = $(this).attr('data-active');
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';

				  	$('#recycleid').val(id);
				  	$('#recycleid_edit').text(id);
				  	$('#status').val(status);
				  	$('#attempt_maximum').val(attempt_maximum);
				  	$('#attempt_delay').val(attempt_delay);
				  	$('#active').val(active);
				  	
				});

			//edit leadrecycling
				$(document).on('click','#modify_leadrecycling',function() {
					$('#update_button').html("<i class='fa fa-edit'></i> Updating...");
					$('#modify_leadrecycling').attr("disabled", true);
					var session_user = '<?=$_SESSION['user']?>';

	                	$.ajax({
		                    url: "./php/ModifyLeadRecycling.php",
		                    type: 'POST',
		                    data: {
		                    	recycleid : $('#recycleid').val(),
		                        attempt_delay : $('#attempt_delay').val(),
					    		attempt_maximum : $('#attempt_maximum').val(),
					   			active : $('#active').val(),
					    		session_user : session_user
		                    },
		                    success: function(data) {
		                    console.log(data);
		                    $('#modify_leadrecycling').attr("disabled", false);
		                        if(data == "success"){
		                        	swal({
										title: "<?php $lh->translateText("success"); ?>",
										text: "<?php $lh->translateText("edit_recycling_success"); ?>!",
										type: "success"
									},
									function(){
										window.location.href = 'telephonycampaigns.php?leadrecycling';
									});
		                        }else{
		                        	sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>.", "error");
		                            $('#update_button').html("<i class='fa fa-check'></i> Update");
		                        }
	                    }
	                });
				});
			
			//delete disposition
		        $(document).on('click','.delete_leadrecycling', function() {
		            var id = $(this).attr('data-id');
					var session_user = '<?=$_SESSION['user']?>';
		            swal({
		            	title: "<?php $lh->translateText("are_you_sure"); ?>?",
		            	text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
		            	type: "warning",
		            	showCancelButton: true,
		            	confirmButtonColor: "#DD6B55",
		            	confirmButtonText: "Yes, delete this lead recycling!",
		            	cancelButtonText: "No, cancel please!",
		            	closeOnConfirm: false,
		            	closeOnCancel: false
		            	},
		            	function(isConfirm){
		            		if (isConfirm) {
		            			$.ajax({
			                        url: "./php/DeleteLeadRecycling.php",
			                        type: 'POST',
			                        data: {
			                            recycleid:id,
										session_user: session_user
			                        },
			                        success: function(data) {
			                        console.log(data);
			                            if(data == "success"){
			                            	swal({
													title: "<?php $lh->translateText("success"); ?>",
													text: "<?php $lh->translateText("leadrecycling_delete"); ?>!",
													type: "success"
												},
												function(){
													location.reload();
													$(".preloader").fadeIn();
												}
											);
			                            }else{
			                                sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
			                            }
			                        }
			                    });
											} else {
		                			swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?> :)", "error");
		                	}
		            	}
		            );
		        });



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
