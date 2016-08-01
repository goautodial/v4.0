<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$dids = $ui->API_getAllDIDs($campaign->data->campaign_id);
$voicefiles = $ui->API_GetVoiceFilesList();

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
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
        <!-- iCheck for checkboxes and radio inputs -->
  		<link rel="stylesheet" href="css/iCheck/all.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        	<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
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
                        <?php $lh->translateText("telephony"); ?>
                        <small>Edit 
                        	<?php 
				        		if($campaign_id != NULL){echo "Campaign";}
				        		if($did != NULL){echo "Disposition";}
				        		if($leadfilter != NULL){echo "Lead Filter";}
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
	                			echo '<div class="col-lg-12" style="margin-top: 10px;">';
	                			if($_GET['message'] == "Success"){
	                				echo '<div class="alert alert-success">
									  <strong>Success!</strong> Campaign has been modified.
									</div>';
	                			}else{
	                				echo '<div class="alert alert-danger">
									  <strong>Error!</strong> Something went wrong please contact administrator.
									</div>';
	                			}
	                			echo '</div>';
	                		}
	                	?>
						<form id="campaign_form_edit" class="form-horizontal"  action="./php/ModifyTelephonyCampaign.php" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="campaign_id" value="<?php echo $campaign->data->campaign_id;?>">
							<?php $errormessage = NULL; ?>

						<!-- IF CAMPAIGN -->
							<?php
							if($campaign_id != NULL) { 
								if ($campaign->result=="success") { 
							?>
							<div class="panel-body">
								<legend>MODIFY CAMPAIGN ID : <u><?php echo $campaign_id." - ".$campaign->data->campaign_name;?></u></legend>

								<!-- Custom Tabs -->
								<div role="tabpanel">
								<!--<div class="nav-tabs-custom">-->
									<ul role="tablist" class="nav nav-tabs">
										<li class="active"><a href="#tab_1" data-toggle="tab"><em class="fa fa-gear fa-lg"></em> Basic Settings</a></li>
										<li><a href="#tab_2" data-toggle="tab"><em class="fa fa-gears fa-lg"></em> Advanced Settings</a></li>
									</ul>
					               		<!-- Tab contents-->
					               		<div class="tab-content">
						               	<!-- BASIC SETTINGS -->
						                	<div id="tab_1" class="tab-pane fade in active">
												<fieldset>
													<div class="form-group mt">
														<label class="col-sm-2 control-label">Campaign Name:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control" name="campaign_name" value="<?php echo $campaign->data->campaign_name; ?>">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Campaign Description:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control" name="campaign_desc" value="<?php echo $campaign->data->campaign_name; ?>">
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
																<option value="AUTO_DIAL" <?php if($campaign->data->dial_method == "AUTO_DIAL") echo "selected";?>>AUTO DIAL</option>
																<option value="PREDICTIVE" <?php if($campaign->data->dial_method == "PREDICTIVE") echo "selected";?>>PREDICTIVE</option>
																<option value="INBOUND_MAN" <?php if($campaign->data->dial_method == "INBOUND_MAN") echo "selected";?>>INBOUND MAN</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">AutoDial Level:</label>
														<div class="col-sm-10 mb">
															<div class="row">
																<div class="col-lg-8">
																	<select id="auto_dial_level" class="form-control" name="auto_dial_level">
																		<option value="OFF" <?php if($campaign->data->auto_dial_level == 0) echo "selected";?>>OFF</option>
																		<option value="SLOW" <?php if($campaign->data->auto_dial_level == 1) echo "selected";?>>SLOW</option>
																		<option value="NORMAL" <?php if($campaign->data->auto_dial_level == 2) echo "selected";?>>NORMAL</option>
																		<option value="HIGH" <?php if($campaign->data->auto_dial_level == 4) echo "selected";?>>HIGH</option>
																		<option value="MAX" <?php if($campaign->data->auto_dial_level == 6) echo "selected";?>>MAX</option>
																		<option value="MAX_PREDICTIVE" <?php if($campaign->data->auto_dial_level == 10) echo "selected";?>>MAX PREDICTIVE</option>
																		<option value="ADVANCE">ADVANCE</option>
																	</select>
																</div>
																<div class="col-lg-4">
																	<select id="auto_dial_level_adv" class="form-control hide" name="auto_dial_level_adv">
																		<option value="1.0">1.0</option>
																		<option value="1.5">1.5</option>
																		<option value="2.0">2.0</option>
																		<option value="2.5">2.5</option>
																		<option value="3.0">3.0</option>
																		<option value="3.5">3.5</option>
																		<option value="4.0">4.0</option>
																		<option value="4.5">4.5</option>
																		<option value="5.0">5.0</option>
																		<option value="5.5">5.5</option>
																		<option value="6.0">6.0</option>
																		<option value="6.5">6.5</option>
																		<option value="7.0">7.0</option>
																		<option value="7.5">7.5</option>
																		<option value="8.0">8.0</option>
																		<option value="8.5">8.5</option>
																		<option value="9.0">9.0</option>
																		<option value="9.5">9.5</option>
																		<option value="10.0">10.0</option>
																		<option value="10.5">10.5</option>
																		<option value="11.0">11.0</option>
																		<option value="11.5">11.5</option>
																		<option value="12.0">12.0</option>
																		<option value="12.5">12.5</option>
																		<option value="13.0">13.0</option>
																		<option value="13.5">13.5</option>
																		<option value="14.0">14.0</option>
																		<option value="14.5">14.5</option>
																		<option value="15.0">15.0</option>
																		<option value="15.5">15.5</option>
																		<option value="16.0">16.0</option>
																		<option value="16.5">16.5</option>
																		<option value="17.0">17.0</option>
																		<option value="17.5">17.5</option>
																		<option value="18.0">18.0</option>
																		<option value="18.5">18.5</option>
																		<option value="19.0">19.0</option>
																		<option value="19.5">19.5</option>
																		<option value="20.0">20.0</option>
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
																		<option value="CUSTOM" selected="selected">CUSTOM DIAL PREFIX</option>
																		<?php for($i=0;$i<=count($carriers->carrier_id);$i++) { ?>
																			<?php if(!empty($carriers->carrier_id[$i])) { ?>
																				<option value="<?php echo $carriers->carrier_id[$i]; ?>" <?php if($campaign->data->dial_prefix == $carriers->carrier_id[$i]) echo "selected";?>><?php echo $carriers->carrier_name[$i]; ?></option>
																			<?php } ?>
																		<?php } ?>
																	</select>
																</div>
																<div class="col-lg-3">
																	<input type="text" class="form-control" id="custom_prefix" name="custom_prefix">
																</div>
															</div>
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
																<option value="8368" <?php if ($campaign->data->campaign_vdad_exten == "8368") echo "selected"; ?>>OFF</option>
																<option value="8369" <?php if ($campaign->data->campaign_vdad_exten == "8369") echo "selected"; ?>>ON</option>
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
																	<p><?php echo $dids->did_id[$i]." - ".$dids->did_pattern[$i]." - ".$dids->did_description[$i]; ?></p>
																<?php }?>
															<?php } else { ?>
																No <b>DID/'s</b> found for this campaign.
															<?php } ?>
														</span>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Inbound Man:</label>
														<div class="col-sm-10 mb">
															<select class="form-control" id="inbound_man" name="inbound_man">
																<option value="Y" <?php if($campaign->data->dial_method == "INBOUND_MAN") echo "selected";?>>Yes</option>
																<option value="N" <?php if($campaign->data->dial_method == "AUTO_DIAL") echo "selected";?>>No</option>
															</select>
														</div>
													</div>
												<?php } elseif($campaign->campaign_type == "BLENDED") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Phone Numbers (DID/TFN) on this campaign:</label>
														<span class="col-sm-10 control-label" style="text-align: left; vertical-align: top;">
															<?php if(count($dids->did_id) != 0) {?>
																<?php for($i=0;$i<=count($dids->did_id);$i++) { ?>
																	<p><?php echo $dids->did_id[$i]." - ".$dids->did_pattern[$i]." - ".$dids->did_description[$i]; ?></p>
																<?php }?>
															<?php } else { ?>
																No <b>DID/'s</b> found for this campaign.
															<?php } ?>
														</span>
													</div>
												<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
													<!-- Nothing to do -->
												<?php } else { ?>
													<!-- Nothing to do -->
												<?php } ?>
												</fieldset><!-- /.fieldset -->
											</div><!-- /.tab-pane -->

											<div class="tab-pane fade in" id="tab_2">
												<fieldset>
													<?php if($campaign->campaign_type == "OUTBOUND") { ?>
														<div class="form-group">
															<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=0;?>
															<?php foreach($dial_statuses as $dial_status) { ?>
																<?php if(!empty($dial_status)) { ?>
																	<label class="col-sm-3 control-label">Active Dial Status <?php echo $i; ?>:</label>
																	<span class="col-sm-9 control-label" style="text-align: left;">
																		<label><?php echo $dial_status; ?></label> - <span><?php $lh->translateText($dial_status); ?></span>
																	</span>
																<?php } ?>
																<?php $i++; ?>
															<?php } ?>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Status:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="dial_status" name="dial_status">
																	<option value="NONE">NONE</option>
																	<?php for($i=0;$i<=count($dialStatus->status);$i++) { ?>
																		<option value="<?php echo $dialStatus->status[$i]?>" <?php if($campaign->data->dial_status_a == $dialStatus->status[$i]) echo "selected"; ?>>
																			<?php echo $dialStatus->status[$i]." - ".$dialStatus->status_name[$i]?>
																		</option>
																	<?php } ?>
																	<option value=""></option>
																</select>
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
																	<option value="NONE" <?php if($Campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($Campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($Campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Answering Machine Mesage:</label>
															<div class="col-sm-9 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="am_message_exten" name="am_message_exten" value="<?php echo $campaign->data->am_message_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_am_message_chooser" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<select class="form-control am_message_chooser" id="am_message_chooser" name="am_message_chooser">
																	<option value="" selected="">-- Default Value --</option>
																	<?php for($i=0;$i<=count($voicefiles->file_name);$i++) { ?>
																		<?php if(!empty($voicefiles->file_name[$i])) { ?>
																			<option value="<?php echo $carriers->file_name[$i]; ?>"><?php echo $voicefiles->file_name[$i]; ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Pause Codes:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																	<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																	<option value="NONE" <?php if($camapign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																	<option value="DNC_ONLY" <?php if($camapign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																	<option value="CAMPLIST_ONLY" <?php if($camapign->data->manual_dial_filter == "CAMPLIST_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																	<option value="DNC_AND_CAMPLIST" <?php if($camapign->data->manual_dial_filter == "DNC_AND_CAMPLIST") echo "selected";?>>DNC & CAMPLIST</option>
																	<option value="DNC_AND_CAMPLIST_ALL" <?php if($camapign->data->manual_dial_filter == "DNC_AND_CAMPLIST_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial List ID:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_list_id" name="manual_dial_list_id">
																	<option value="998" <?php if($campaign->data->manual_dial_list_id == 998 || $campaign->data->manual_dial_list_id == 0) echo "selected";?>>998</option>
																	<option value="999" <?php if($campaign->data->manual_dial_list_id == 999) echo "selected";?>>999</option>
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
																<input type="text" class="form-control" value="<?php if(!empty($campaign->data->three_way_dial_prefix)){echo $campaign->data->three_way_dial_prefix;}else{echo "88";}?>" id="three_way_dial_prefix" name="three_way_dial_prefix">
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
																<select class="form-control" id="customer_3way_hangup_seconds" name="customer_3way_hangup_seconds">
																	<option value="5" <?php if($campaign->data->customer_3way_hangup_seconds == "5") echo "selected";?>>5</option>
																</select>
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
														<div class="form-group">
															<label class="col-sm-3 control-label">Get Call Launch:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="get_call_launch" name="get_call_launch">
																	<option value="NONE" <?php if($Campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($Campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($Campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Answering Machine Mesage:</label>
															<div class="col-sm-9 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="am_message_exten" name="am_message_exten" value="<?php echo $campaign->data->am_message_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<select class="form-control am_message_chooser" id="am_message_chooser" name="am_message_chooser">
																	<option value="">-- DEFAULT VALUE --</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Pause Codes:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																	<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																	<option value="NONE" <?php if($camapign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																	<option value="DNC_ONLY" <?php if($camapign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																	<option value="CAMPLIST_ONLY" <?php if($camapign->data->manual_dial_filter == "CAMPLIST_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																	<option value="DNC_AND_CAMPLIST" <?php if($camapign->data->manual_dial_filter == "DNC_AND_CAMPLIST") echo "selected";?>>DNC & CAMPLIST</option>
																	<option value="DNC_AND_CAMPLIST_ALL" <?php if($camapign->data->manual_dial_filter == "DNC_AND_CAMPLIST_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
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
																	<input type="text" class="form-control" value="<?php if(!empty($campaign->data->three_way_dial_prefix)){echo $campaign->data->three_way_dial_prefix;}else{echo "88";}?>" id="three_way_dial_prefix" name="three_way_dial_prefix">
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
																	<select class="form-control" id="customer_3way_hangup_seconds" name="customer_3way_hangup_seconds">
																		<option value="5" <?php if($campaign->data->customer_3way_hangup_seconds == "5") echo "selected";?>>5</option>
																	</select>
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
														<div class="form-group">
															<?php $dial_statuses = explode(" ", rtrim($campaign->data->dial_statuses, " -")); $i=0;?>
															<?php foreach($dial_statuses as $dial_status) { ?>
																<?php if(!empty($dial_status)) { ?>
																	<label class="col-sm-3 control-label">Active Dial Status <?php echo $i; ?>:</label>
																	<span class="col-sm-9 control-label" style="text-align: left;">
																		<label><?php echo $dial_status; ?></label> - <span><?php $lh->translateText($dial_status); ?></span>
																	</span>
																<?php } ?>
																<?php $i++; ?>
															<?php } ?>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Dial Status:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="dial_status" name="dial_status">
																	<option value="NONE">NONE</option>
																	<?php for($i=0;$i<=count($dialStatus->status);$i++) { ?>
																		<option value="<?php echo $dialStatus->status[$i]?>" <?php if($campaign->data->dial_status_a == $dialStatus->status[$i]) echo "selected"; ?>>
																			<?php echo $dialStatus->status[$i]." - ".$dialStatus->status_name[$i]?>
																		</option>
																	<?php } ?>
																	<option value=""></option>
																</select>
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
																	<option value="NONE" <?php if($Campaign->data->get_call_launch == "NONE") echo "selected";?>>NONE</option>
																	<option value="SCRIPT" <?php if($Campaign->data->get_call_launch == "SCRIPT") echo "selected";?>>SCRIPT</option>
																	<option value="WEBFORM" <?php if($Campaign->data->get_call_launch == "WEBFORM") echo "selected";?>>WEBFORM</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Answering Machine Mesage:</label>
															<div class="col-sm-9 mb">
																<div class="input-group">
																	<input type="text" class="form-control" id="am_message_exten" name="am_message_exten" value="<?php echo $campaign->data->am_message_exten;?>">
																	<span class="input-group-btn">
																		<button class="btn btn-default show_am_message_chooser" type="button">[Audio Chooser...]</button>
																	</span>
																</div><!-- /input-group -->
																<select class="form-control am_message_chooser" id="am_message_chooser" name="am_message_chooser">
																	<option value="">-- DEFAULT VALUE --</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Pause Codes:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="agent_pause_codes_active" name="agent_pause_codes_active">
																	<option value="Y" <?php if($campaign->data->agent_pause_codes_active == "Y") echo "selected";?>>YES</option>
																	<option value="N" <?php if($campaign->data->agent_pause_codes_active == "N") echo "selected";?>>NO</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Manual Dial Filter:</label>
															<div class="col-sm-9 mb">
																<select class="form-control" id="manual_dial_filter" name="manual_dial_filter">
																	<option value="NONE" <?php if($camapign->data->manual_dial_filter == "NONE") echo "selected";?>>NONE</option>
																	<option value="DNC_ONLY" <?php if($camapign->data->manual_dial_filter == "DNC_ONLY") echo "selected";?>>DNC ONLY</option>
																	<option value="CAMPLIST_ONLY" <?php if($camapign->data->manual_dial_filter == "CAMPLIST_ONLY") echo "selected";?>>CAMPLIST ONLY</option>
																	<option value="DNC_AND_CAMPLIST" <?php if($camapign->data->manual_dial_filter == "DNC_AND_CAMPLIST") echo "selected";?>>DNC & CAMPLIST</option>
																	<option value="DNC_AND_CAMPLIST_ALL" <?php if($camapign->data->manual_dial_filter == "DNC_AND_CAMPLIST_ALL") echo "selected";?>>DNC & CAMPLIST ALL</option>
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
																<input type="text" class="form-control" value="<?php if(!empty($campaign->data->three_way_dial_prefix)){echo $campaign->data->three_way_dial_prefix;}else{echo "88";}?>" id="three_way_dial_prefix" name="three_way_dial_prefix">
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
																<select class="form-control" id="customer_3way_hangup_seconds" name="customer_3way_hangup_seconds">
																	<option value="5" <?php if($campaign->data->customer_3way_hangup_seconds == "5") echo "selected";?>>5</option>
																</select>
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
													<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
														Survey
													<?php } else { ?>
														Default
													<?php } ?>
												</fieldset>
											</div>
											<!-- /.tab-pane -->

											<!-- Notification -->
										   	<div id="modifyUSERresult"></div>

										   	<!-- FOOTER BUTTONS -->
										   	<fieldset>
						                        <div class="box-footer">
						                           <div class="pull-right col-sm-2">
						                           		<div class="col-sm-5">
															<a href="telephonycampaigns.php" type="button" class="btn btn-danger pull-right"><i class="fa fa-close"></i> Cancel </a>
						                           		</div>
						                           		
						                           		<div class="col-sm-6">
						                                	<button type="button" class="btn btn-primary pull-left" id="modifyCampaignButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
														</div>
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
											<a class="delete_disposition btn btn-danger" href="#" data-id="<?php echo $disposition->campaign_id[$i];?>" data-status ="<?php echo $disposition->status[$i];?>" data-name="<?php echo $disposition->status_name[$i];?>"><i class="fa fa-close"></i></a>
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
											<input type="text" name="add_status" id="add_status" class="" placeholder="Status">
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
											<a type="button" id="add_new_status" data-id="<?php echo $did;?>" class="btn btn-primary"><span id="add_button"><i class="fa fa-plus"></i> New Status</span></a>
										</td>
									</tr>
								<!------>

									</tbody>
					              </table>
					            </div>

					            <!-- NOTIFICATIONS -->
				                <div id="notifications">
				                    <div class="output-message-success" style="display:none;">
				                        <div class="alert alert-success alert-dismissible" role="alert">
				                          <strong>Success!</strong> New Disposition added !
				                        </div>
				                    </div>
				                    <div class="output-message-error" style="display:none;">
				                        <div class="alert alert-danger alert-dismissible" role="alert">
				                          <span id="disposition_result"></span>
				                        </div>
				                    </div>
				                    <div class="output-message-incomplete" style="display:none;">
				                        <div class="alert alert-danger alert-dismissible" role="alert">
				                          Please do not leave <u>status</u> and <u>status name</u> blank.
				                        </div>
				                    </div>
				                </div>


					            <div class="box-footer">
									<a href="telephonycampaigns.php" type="button" id="" class="btn btn-danger"><i class="fa fa-remove"></i> Cancel</a>
								</div>
								<!-- /.box-footer -->
							<?php
								} else { 
								echo $disposition->result; 
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
			
            <?php print $ui->creamyFooter(); ?>
			
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
	                <div class="row">
	                	<input type="hidden" name="edit_campaign" id="edit_campaign">
	                    <div class="col-lg-12">
	                        <label class="col-sm-4 control-label" for="status">* Status:</label>
	                        <div class="col-sm-7">
	                            <input type="text" name="edit_status" id="edit_status" class="form-control" placeholder="Status" minlength="3" maxlenght="6" disabled>
	                        </div>
	                    </div>
	                    <div class="col-lg-12" style="padding-top:10px;">        
	                        <label class="col-sm-4 control-label" for="status_name">* Status Name: </label>
	                        <div class="col-sm-7">
	                            <input type="text" name="edit_status_name" id="edit_status_name" class="form-control" placeholder="Status Name">
	                        </div>
	                    </div>
	                    <div class="col-lg-12" style="padding-top:10px;">        
		                        <!--<label class="col-sm-2 control-label" for="grouplevel" style="padding-top:15px;"> </label>-->
	                		<label class="col-sm-3" for="selectable">
				                  <input type="checkbox" id="edit_selectable" name="edit_selectable" class="flat-red">
				                  Selectable
			                </label>
			                <label class="col-sm-4" for="human_answered">
				                  <input type="checkbox" id="edit_human_answered" name="edit_human_answered" class="flat-red">
				                  Human Answered
					        </label>
					        <label class="col-sm-3" for="sale">
				                  <input type="checkbox" id="edit_sale" name="edit_sale" class="flat-red">
				                  Sale
				            </label>
				            <label class="col-sm-3" for="dnc">
				                  <input type="checkbox" id="edit_dnc" name="edit_dnc" class="flat-red">
				                  DNC
				            </label>
					          
			                <label class="col-sm-4" for="customer_contact">
				                  <input type="checkbox" id="edit_customer_contact" name="edit_customer_contact" class="flat-red">
				                  Customer Contact
			                </label>
			                <label class="col-sm-4" for="not_interested">
				                  <input type="checkbox" id="edit_not_interested" name="edit_not_interested" class="flat-red">
				                  Not Interested
			                </label>
			                <label class="col-sm-3" for="unworkable">
				                  <input type="checkbox" id="edit_unworkable" name="edit_unworkable" class="flat-red">
				                  Unworkable
			                </label>
			                <label class="col-sm-4" for="scheduled_callback">
				                  <input type="checkbox" id="edit_scheduled_callback" name="edit_scheduled_callback" class="flat-red">
				                  Scheduled Callback
			                </label>
		                       
	                    </div>
	                </div>
	            	</form>
                </div>
                <!-- NOTIFICATIONS -->
                <div id="edit_notifications">
                    <div class="output-message-success_edit" style="display:none;">
                        <div class="alert alert-success alert-dismissible" role="alert">
                          Successfully modified data!
                        </div>
                    </div>
                    <div class="output-message-error" style="display:none;">
                        <div class="alert alert-danger alert-dismissible" role="alert">
                          <span id="disposition_result"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                	<div class="col-sm-5 pull-right">
                		
                    	<button type="button" class="btn btn-primary" id="modify_disposition"><span id="update_button"><i class='fa fa-check'></i> Update</span></button>
                    	<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class='fa fa-remove'></i> Cancel</button>
              		</div>
              	</div>
              	
            </div>
        </div>
    </div>

    <!-- DELETE VALIDATION MODAL -->
    <div id="delete_validation_modal" class="modal modal-warning fade">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:5px;margin-top: 40%;">
                <div class="modal-header">
                    <h4 class="modal-title"><b>WARNING!</b>  You are about to <b><u>DELETE</u></b> a <span class="action_validation"></span>... </h4>
                </div>
                <div class="modal-body" style="background:#fff;">
                    <p>This action cannot be undone.</p>
                    <p>Are you sure you want to delete <span class="action_validation"></span>: <i><b style="font-size:20px;"><span class="delete_extension"></span></b></i> ?</p>
                </div>
                <div class="modal-footer" style="background:#fff;">
                    <button type="button" class="btn btn-primary id-delete-label" id="delete_yes">Yes</button>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">No</button>
              </div>
            </div>
        </div>
    </div>

    <!-- DELETE NOTIFICATION MODAL -->
    <div id="delete_notification" style="display:none;">
        <?php echo $ui->deleteNotificationModal('<span class="action_validation">','<span id="id_span"></span>', '<span id="result_span"></span>');?>
    </div>

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<!-- SLIMSCROLL-->
    	<script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
    	<!-- iCheck 1.0.1 -->
		<script src="js/plugins/iCheck/icheck.min.js"></script>

		<script type="text/javascript">
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
				if(value == "AUTO_DIAL"){
					$('#auto_dial_level').prop('disabled', false);
					$('#auto_dial_level option[value=SLOW]').prop('selected', true);
					$('#auto_dial_level_adv').removeClass('hide');
				}else if(value == "PREDICTIVE"){
					$('#auto_dial_level').prop('disabled', true);
					$('#auto_dial_level option[value=OFF]').prop('selected', true);
					$('#auto_dial_level_adv').addClass('hide');
				}else if(value == "INBOUND_MAN"){
					$('#auto_dial_level').prop('disabled', true);
					$('#auto_dial_level option[value=OFF]').prop('selected', true);
					$('#auto_dial_level_adv').addClass('hide');
				}else{
					$('#auto_dial_level').prop('disabled', true);
					$('#auto_dial_level option[value=OFF]').prop('selected', true);
					$('#auto_dial_level_adv').addClass('hide');
				}
			}

			function dialPrefix(value){
				if(value == "--CUSTOM--"){
					$('#custom_prefix').removeClass('hide');
				}else{
					$('#custom_prefix').addClass('hide');
				}
			}

			$(document).ready(function() {
				$('#modifyCampaignButton').click(function(){
					$('#campaign_form_edit').submit();
				});

				$('.am_message_chooser').hide();
				$('.show_am_message_chooser').on('click', function(event) {        
			        $('.am_message_chooser').toggle('show');
			    });

				var dial_method = $('#dial_method').val();
				dialMethod(dial_method);

				$('#dial_method').change(function(){
					dialMethod($(this).val());
				});

				var dial_prefix = $('#dial_prefix').val();
				dialPrefix(dial_prefix);

				$('#dial_prefix').change(function(){
					dialPrefix($(this).val());
				});

				//Flat red color scheme for iCheck
			    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			      checkboxClass: 'icheckbox_flat-green',
			      radioClass: 'iradio_flat-green'
			    });

			    var campaign_type = $('#campaignType').find("option:selected").val();
			    setElements(campaign_type);

			    $('#campaignType').change(function(){
					var selectedTypeText = $(this).find("option:selected").text();
					var selectedTypeVal = $(this).find("option:selected").val();
					

					setElements(selectedTypeVal);
				});

			   //Add Status
		        $('#add_new_status').click(function(){

		        $('#add_button').html("<i class='fa fa-check'></i> Saving, Please Wait.....");
				$('#add_new_status').attr("disabled", true);

		        var validate = 0;
		        var status = $("#add_status").val();
		        var status_name = $("#add_status_name").val();
		        
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
		                    },
		                    success: function(data) {
		                      // console.log(data);
		                          if(data == 1){
		                                $('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
		                                $('#add_button').html("<i class='fa fa-plus'></i> New Status");
										$('#add_new_status').attr("disabled", false);
		                                window.setTimeout(function(){location.reload()},1000)
		                          }
		                          else{
		                              $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
		                              $("#disposition_result").html(data); 
		                          	  $('#add_button').html("<i class='fa fa-plus'></i> New Status");
									  $('#add_new_status').attr("disabled", false);
		                          }
		                    }
		                });
		            	
		            }else{
		                $('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
		                $('#add_button').html("<i class='fa fa-plus'></i> New Status");
						$('#add_new_status').attr("disabled", false);
		                validate = 0;
		            }
		        });
				

				// GET DETAILS FOR EDIT DISPOSITION
				$(document).on('click','.edit_disposition',function() {
					var id = $(this).attr('data-id');
					var status = $(this).attr('data-status');

					$.ajax({
					  url: "./php/ViewDisposition.php",
					  type: 'POST',
					  data: { 
					  	campaign_id : id,
					  	status : status
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
					  		$('#edit_selectable').iCheck('check'); 
					  	}else{
					  		$('#edit_selectable').iCheck('uncheck'); 
					  	}
					  	if(data.human_answered == "Y"){
					  		$('#edit_human_answered').iCheck('check'); 
					  	}else{
					  		$('#edit_human_answered').iCheck('uncheck'); 
					  	}
					  	if(data.sale == "Y"){
					  		$('#edit_sale').iCheck('check'); 
					  	}else{
					  		$('#edit_sale').iCheck('uncheck'); 
					  	}
					  	if(data.dnc == "Y"){
					  		$('#edit_dnc').iCheck('check'); 
					  	}else{
					  		$('#edit_dnc').iCheck('uncheck'); 
					  	}
					  	if(data.scheduled_callback == "Y"){
					  		$('#edit_scheduled_callback').iCheck('check'); 
					  	}else{
					  		$('#edit_scheduled_callback').iCheck('uncheck'); 
					  	}
					  	if(data.customer_contact == "Y"){
					  		$('#edit_customer_contact').iCheck('check'); 
					  	}else{
					  		$('#edit_customer_contact').iCheck('uncheck'); 
					  	}
					  	if(data.not_interested == "Y"){
					  		$('#edit_not_interested').iCheck('check'); 
					  	}else{
					  		$('#edit_not_interested').iCheck('uncheck'); 
					  	}
					  	if(data.unworkable == "Y"){
					  		$('#edit_unworkable').iCheck('check'); 
					  	}else{
					  		$('#edit_unworkable').iCheck('uncheck'); 
					  	}
					  }
					});
				});

				/* 
				 * Modifies 
			 	 */
				//CAMPAIGN
				$("#modifycampaign").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyCampaign.php", //post
							$("#modifycampaign").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyCAMPAIGNresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyCAMPAIGNresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				//LEADFILTER
				$(document).on('click','#modify_disposition',function() {

				$('#update_button').html("<i class='fa fa-edit'></i> Updating...");
				$('#modify_disposition').attr("disabled", true);

					var selectable = "Y";
	            	if(!$('#edit_selectable').is(":checked")){
	            		selectable = "N"
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
	            		var unworkable = "Y"
	            	if(!$('#edit_unworkable').is(":checked")){
	            		unworkable = "N";
	            	}

                	$.ajax({
	                    url: "./php/ModifyTelephonyCampaign.php",
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
	                    },
	                    success: function(data) {
	                    console.log(data);
	                        if(data == 1){
                                $('.output-message-success_edit').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                $('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modify_disposition').attr("disabled", false);
                                window.setTimeout(function(){location.reload()},1000)
	                        }else{
	                            $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
	                            $("#disposition_result").html(data);
	                            $('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modify_disposition').attr("disabled", false);
	                        }
                    }
                });			
				});

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
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyLEADFILTERresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyLEADFILTERresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
		         * Delete validation modal
		         */
		         // CAMPAIGN
		         $(document).on('click','.delete-campaign',function() {
		            
		            var id = $(this).attr('data-id');
		            var name = $(this).attr('data-name');
		            var action = "Campaign";

		            $('.id-delete-label').attr("data-id", id);
		            $('.id-delete-label').attr("data-action", action);

		            $(".delete_extension").text(name);
		            $(".action_validation").text(action);

		            $('#delete_validation_modal').modal('show');
		         });
		         // DISPOSITION
		         $(document).on('click','.delete_disposition',function() {
		            
		            var id = $(this).attr('data-id');
		            var status = $(this).attr('data-status');
		            var name = $(this).attr('data-name');
		            var action = "STATUS";

		            $('.id-delete-label').attr("data-id", id);
		            $('.id-delete-label').attr("data-status", status);
		            $('.id-delete-label').attr("data-action", action);

		            $(".delete_extension").text(name);
		            $(".action_validation").text(action);

		            $('#delete_validation_modal').modal('show');
		         });
		         // LEAD FILTER
		         $(document).on('click','.delete_leadfilter',function() {
		            
		            var id = $(this).attr('data-id');
		            var name = $(this).attr('data-name');
		            var action = "LEAD FILTER";

		            $('.id-delete-label').attr("data-id", id);
		            $('.id-delete-label').attr("data-action", action);

		            $(".delete_extension").text(name);
		            $(".action_validation").text(action);

		            $('#delete_validation_modal').modal('show');
		         });

		        $(document).on('click','#delete_yes',function() {
                
	                var id = $(this).attr('data-id');
	                var status_id = $(this).attr('data-status');
	                var action = $(this).attr('data-action');

	                $('#id_span').html(status_id);

	                	if(action == "CAMPAIGN"){
	                		$.ajax({
		                        url: "./php/DeleteCampaign.php",
		                        type: 'POST',
		                        data: { 
		                            campaign_id:id,
		                        },
		                        success: function(data) {
		                        console.log(data);
		                            if(data == 1){
		                                $('#result_span').text(data);
		                                $('#delete_notification').show();
		                                $('#delete_notification_modal').modal('show');
		                                //window.setTimeout(function(){$('#delete_notification_modal').modal('hide');location.reload();}, 2000);
		                                window.setTimeout(function(){location.reload()},1000)
		                            }else{
		                                $('#result_span').html(data);
		                                $('#delete_notification').show();
		                                $('#delete_notification_modal_fail').modal('show');
		                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
		                            }
		                        }
		                    });
	                	}
	                	if(action == "STATUS"){
	                		$.ajax({
		                        url: "./php/DeleteDisposition.php",
		                        type: 'POST',
		                        data: { 
		                            disposition_id:id,
		                            status:status_id,
		                        },
		                        success: function(data) {
		                        console.log(data);
		                            if(data == 1){
		                                $('#result_span').text(data);
		                                $('#delete_notification').show();
		                                $('#delete_notification_modal').modal('show');
		                                //window.setTimeout(function(){$('#delete_notification_modal').modal('hide');location.reload();}, 2000);
		                                window.setTimeout(function(){location.reload()},1000)
		                            }else{
		                                $('#result_span').html(data);
		                                $('#delete_notification').show();
		                                $('#delete_notification_modal_fail').modal('show');
		                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
		                            }
		                        }
		                    });
	                	}
	                	if(action == "LEAD FILTER"){
	                		$.ajax({
		                        url: "./php/DeleteLeadFilter.php",
		                        type: 'POST',
		                        data: { 
		                            leadfilter_id:id,
		                        },
		                        success: function(data) {
		                        console.log(data);
		                            if(data == 1){
		                                $('#result_span').text(data);
		                                $('#delete_notification').show();
		                                $('#delete_notification_modal').modal('show');
		                                //window.setTimeout(function(){$('#delete_notification_modal').modal('hide');location.reload();}, 2000);
		                                window.setTimeout(function(){location.reload()},1000)
		                            }else{
		                                $('#result_span').html(data);
		                                $('#delete_notification').show();
		                                $('#delete_notification_modal_fail').modal('show');
		                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
		                            }
		                        }
		                    });
	                	}
	            });
			
			});
		</script>


    </body>
</html>

?>