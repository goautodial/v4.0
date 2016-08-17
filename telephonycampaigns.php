<?php	

	###########################################################
	### Name: telephonycampaigns.php 						###
	### Functions: Manage Campaigns, Disposition			###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016			###
	### Version: 4.0 										###
	### Written by: Alexander Abenoja & Noel Umandap		###
	### License: AGPLv2										###
	###########################################################

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
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
        <title>Campaigns</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        
    	<!-- iCheck for checkboxes and radio inputs -->
  		<link rel="stylesheet" href="css/iCheck/all.css">
    	
    	<!-- Wizard Form style -->
    	<link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
    	<link href="css/style.css" rel="stylesheet" type="text/css" />
    	<link rel="stylesheet" href="css/easyWizard.css">
        
        <?php print $ui->creamyThemeCSS(); ?>

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <script type="text/javascript">
			$(window).ready(function() {
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
                        <small><?php $lh->translateText("campaign_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("campaign_management"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                	<div class="panel panel-default">
                		<div class="panel-body">
                			<legend>Campaigns</legend>
                <?php if ($user->userHasAdminPermission()) { ?>
<?php

	/*
	 * API used for display in tables
	 */
	$campaign = $ui->API_getListAllCampaigns();
	$disposition = $ui->API_getAllDispositions();
	$leadfilter = $ui->API_getAllLeadFilters();
	$country_codes = $ui->getCountryCodes();
	$list = $ui->API_goGetAllLists();
?>			
							 <div role="tabpanel">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

										<?php if($_GET['message'] == "Success") { ?>
											<div class="callout callout-success">
								        		<h4>Success!</h4>
								        		You have successfully created a campaign.
								        		
								      		</div>

										<?php }elseif($_GET['message'] == "error"){ ?>

											<div class="callout callout-danger">
								        		<h4>Error!</h4>
								        		Something went wrong. Please contact administrator.
								        		
								      		</div>
										<?php } ?>
										 
									</div>
								<ul role="tablist" class="nav nav-tabs nav-justified">

								 <!-- In-group panel tabs-->
									 <li role="presentation" class="active">
										<a href="#T_campaign" aria-controls="T_campaign" role="tab" data-toggle="tab" class="bb0">
										   Campaigns </a>
									 </li>
								<!-- IVR panel tab -->
									 <li role="presentation">
										<a href="#T_disposition" aria-controls="T_disposition" role="tab" data-toggle="tab" class="bb0">
										   Dispositions </a>
									 </li>
								<!-- DID panel tab -->
									 <li role="presentation">
										<a href="#T_leadfilter" aria-controls="T_leadfilter" role="tab" data-toggle="tab" class="bb0">
										   Lead Filters </a>
									 </li>
								  </ul>
								  
								<!-- Tab panes-->
								<div class="tab-content bg-white">

								<!--==== Campaigns ====-->
								  <div id="T_campaign" role="tabpanel" class="tab-pane active">
										<table class="table table-striped table-bordered table-hover" id="table_campaign">
										   <thead>
											  <tr>
												 <th class='hide-on-medium hide-on-low'>Campaign ID</th>
												 <th >Campaign Name</th>
												 <th class='hide-on-medium hide-on-low'>Dial Method</th>
												 <th class='hide-on-medium hide-on-low'>Status</th>
												 <th>Action</th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		for($i=0;$i < count($campaign->campaign_id);$i++){
									
														if($campaign->active[$i] == "Y"){
															$campaign->active[$i] = "Active";
														}else{
															$campaign->active[$i] = "Inactive";
														}

														if($campaign->dial_method[$i] == "RATIO"){
															$campaign->dial_method[$i] = "AUTO DIAL";
														}
														
														if($campaign->dial_method[$i] == "MANUAL"){
															$campaign->dial_method[$i] = "MANUAL";
														}
														
														if($campaign->dial_method[$i] == "ADAPT_TAPERED"){
															$campaign->dial_method[$i] = "PREDICTIVE";
														}

														if($campaign->dial_method[$i] == "INBOUND_MAN"){
															$campaign->dial_method[$i] = "INBOUND_MAN";
														}

													$action_CAMPAIGN = $ui->ActionMenuForCampaigns($campaign->campaign_id[$i], $campaign->campaign_name[$i]);

											   	?>	
													<tr>
														<td class='hide-on-medium hide-on-low'><a class="edit-campaign" data-id="<?php echo $campaign->campaign_id[$i];?>" data-name="<?php echo $campaign->campaign_name[$i];?>"><?php echo $campaign->campaign_id[$i];?></td>
														<td><?php echo $campaign->campaign_name[$i];?></a></td>
														<td class='hide-on-medium hide-on-low'><?php echo $campaign->dial_method[$i];?></td>
														<td class='hide-on-medium hide-on-low'><?php echo $campaign->active[$i];?></td>
														<td><?php echo $action_CAMPAIGN;?></td>
													</tr>
												<?php
													}
												?>
										   </tbody>
										</table>
								 </div>
								
								<!--==== Disposition ====-->
								  <div id="T_disposition" role="tabpanel" class="tab-pane">
										<table class="table table-striped table-bordered table-hover" id="table_disposition">
										   <thead>
											  <tr>
												 <th class='hide-on-medium hide-on-low'>Campaign ID</th>
												 <th>Campaign Name</th>
												 <th class='hide-on-medium hide-on-low'>Custom Disposition</th>
												 <th>Action</th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		for($i=0;$i < count($campaign->campaign_id);$i++){

													$action_DISPOSITION = $ui->ActionMenuForDisposition($campaign->campaign_id[$i], $campaign->campaign_name[$i]);

											   	?>	
													<tr>
														<td class='hide-on-medium hide-on-low'><a class='edit_disposition' data-id="<?php echo $campaign->campaign_id[$i];?>" data-name="<?php echo $campaign->campaign_name[$i];?>"><?php echo $campaign->campaign_id[$i];?></td>
														<td><?php echo $campaign->campaign_name[$i];?></a></td>
														<td class='hide-on-medium hide-on-low'>
												<?php
												//if($disposition->campaign_id[$i] == $campaign->campaign_id[$i]){
													for($a=0; $a<count($disposition->status); $a++){
													
													if($disposition->campaign_id[$a] == $campaign->campaign_id[$i]){
													
												?>
														<?php echo "<i>".$disposition->status[$a]."</i>";?>
												<?php
															if($disposition->campaign_id[$a+1] == $campaign->campaign_id[$i]){
																echo ", ";
															}
														}
													}
												//}else{
												//	echo "- - - NONE - - -";
												//}
												?>
														</td>
														<td><?php echo $action_DISPOSITION;?></td>
													</tr>
												<?php
													}
												?>
										   </tbody>
										</table>
								 </div>

								 <!--==== Lead Filter ====-->
								  <div id="T_leadfilter" role="tabpanel" class="tab-pane">
										<table class="table table-striped table-bordered table-hover" id="table_leadfilter">
										   <thead>
											  <tr>
												 <th>Filter ID</th>
												 <th>Filter Name</th>
												 <th>Action</th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		for($i=0;$i < count($leadfilter->lead_filter_id);$i++){

													$action_LEADFILTER = $ui->ActionMenuForLeadFilters($leadfilter->lead_filter_id[$i], $leadfilter->lead_filter_name[$i]);

											   	?>	
													<tr>
														<td><?php echo $leadfilter->lead_filter_id[$i];?></td>
														<td><a class=''><?php echo $leadfilter->lead_filter_name[$i];?></a></td>
														<td><?php echo $action_LEADFILTER;?></td>
													</tr>
												<?php
													}
												?>
										   </tbody>
										</table>
								 </div>

								</div><!-- END tab content-->
							</div>
							<?php
								} else {
									print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
								}
							?>
						</div><!-- /.body -->
					</div><!-- /.panel -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
		
        <div class="bottom-menu skin-blue">
			<div class="action-button-circle" data-toggle="modal">
				<?php print $ui->getCircleButton("campaigns", "plus"); ?>
			</div>
			<div class="fab-div-area" id="fab-div-area">
				<ul class="fab-ul" style="height: 250px;">
					<li class="li-style"><a class="fa fa-dashboard fab-div-item" data-toggle="modal" data-target="#add_campaign"></a></li><br/>
					<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#add_disposition"></a></li><br/>
					<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers"> </a></li>
				</ul>
			</div>
		</div>

	</div><!-- ./wrapper -->


	

	<!-- Modal -->
	<div id="add_campaign" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Campaign Wizard >> <span class="wizard-type">Outbound</span></b></h4>
	      </div>
	      <div class="modal-body wizard-content">
	      	<div class="output-message-success hide">
		      	<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Success!</strong> New Campaign saved.
				</div>
			</div>
			<div class="output-message-error hide">
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Error!</strong> Something went wrong please see input data on form or campaign already exist.
				</div>
			</div>
	        <div id="content" class="wizard-form">
			    <?php //print $ui->wizardFromCampaign(); ?>
				<!-- Custom Tabs (Pulled to the right) -->
				<form id="campaign_form" class="form-horizontal" method="POST" action="./php/AddCampaign.php" enctype="multipart/form-data">
					<div class="wizard-step">
						<div class="form-group">
		    				<label class="control-label col-lg-4">Campaign Type:</label>
		    				<div class="col-lg-8">
		    					<select id="campaignType" class="form-control" name="campaign_type">
		    						<option value="outbound">Outbound</option>
		    						<option value="inbound">Inbound</option>
		    						<option value="blended">Blended</option>
		    						<option value="survey">Survey</option>
		    						<option value="copy">Copy Campaign</option>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group campaign-id">
		    				<label class="control-label col-lg-4">Campaign ID:</label>
		    				<div class="col-lg-8">
		    					<div class="input-group">
							      <input id="campaign-id" name="campaign_id" type="text" class="form-control" placeholder="" value="<?php echo str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT); ?>" readonly>
							      <span class="input-group-btn">
							        <button id="campaign-id-edit-btn" class="btn btn-default" type="button"><i class="fa fa-pencil"></i></button>
							      </span>
							    </div><!-- /input-group -->
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Campaign Name:</label>
		    				<div class="col-lg-8">
		    					<input id="campaign-name" name="campaign_name" type="text" class="form-control" title="Must be 6 to 40 characters in length.">
		    				</div>
		    			</div>
		    			<div class="form-group inbound blended hide">
		    				<label class="control-label col-lg-4">DID/TFN Extension:</label>
		    				<div class="col-lg-8">
		    					<input id="did-tfn-extension" name="did_tfn_extension" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group inbound blended hide">
		    				<label class="control-label col-lg-4">Call Route:</label>
		    				<div class="col-lg-8">
		    					<select id="call-route" name="call_route" class="form-control">
		                            <option value="NONE"></option>
		                            <option value="INGROUP">INGROUP (campaign)</option>
		                            <option value="IVR">IVR (callmenu)</option>
		                            <option value="AGENT">AGENT</option>
		                            <option value="VOICEMAIL">VOICEMAIL</option>
		                        </select>
		    				</div>
		    			</div>
		    			<div class="form-group inbound blended hide">
		    				<label class="control-label col-lg-4">Group Color:</label>
		    				<div class="col-lg-8">
		    					<input id="group-color" name="group_color" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group survey hide">
		    				<label class="control-label col-lg-4">Survey Type:</label>
		    				<div class="col-lg-8">
		    					<select id="survey-type" name="survey_type" class="form-control">
		                            <option value="BROADCAST">VOICE BROADCAST</option>
		                            <option value="PRESS1">SURVEY PRESS 1</option>
		                        </select>
		    				</div>
		    			</div>
		    			<div class="form-group survey hide">
		    				<label class="control-label col-lg-4">Number of Channels:</label>
		    				<div class="col-lg-8">
		    					<input id="no-channels" name="no_channels" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group copy-from hide">
		    				<label class="control-label col-lg-4">Copy from campaign:</label>
		    				<div class="col-lg-8">
		    					<input id="copy-from-campaign" name="copy_from_campaign" type="text" class="form-control">
		    				</div>
		    			</div>
					</div>
					<div class="wizard-step" onload="alert('step 2');">
		    			<!--<div class="form-group">
		    				<label class="control-label col-lg-4">Lead File:</label>
		    				<div class="col-lg-8">
		    					<div class="input-group">
		    						<input type="file" class="hide" id="lead-file" name="lead_file">
									<input type="text" class="form-control lead-file-holder" placeholder="Lead File">
									<span class="input-group-btn">
										<button class="btn btn-default btn-lead-file" type="button">Browse</button>
									</span>
								</div>
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">&nbsp;</label>
		    				<div class="col-lg-8">
		    					<button type="button" class="btn btn-default upload-leads">UPLOAD LEADS</button>
		    					<small class="text-green success hide">&nbsp;&nbsp;&nbsp;Leads successfully uploaded...</small>
		    					<small class="text-red error hide">&nbsp;&nbsp;&nbsp;Error. Something went wrong...</small>
		    				</div>
		    			</div>-->
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">List ID:</label>
		    				<label class="control-label col-lg-8" style="text-align: left;">
		    					<?php 
		    						$list_id = end($list->list_id) + 1;
		    						echo $list_id." >> List ".$list_id; 
		    					?>
		    				</label>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Country:</label>
		    				<div class="col-lg-8">
		    					<!-- <input id="country" name="country" type="text" class="form-control"> -->
		    					<select id="country" name="country" class="form-control select2">
		    						<?php if ($country_codes->result=="success") { ?>
									<!-- # Result was OK! -->
										<?php for($i=0;$i < count($country_codes->country);$i++){ ?>
											<option value="<?php echo $country_codes->country_code[$i]?>">
												<?php echo $country_codes->country_code[$i]?> >> <?php echo $country_codes->country[$i]?>
											</option>
										<?php } ?>
									<?php } else { ?>
									<!-- # An error occured -->
										No record found.
									<?php } ?>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Check for duplicates:</label>
		    				<div class="col-lg-8">
		    					<!-- <input id="check-for-duplicates" name="check_for_duplicates" type="text" class="form-control"> -->
		    					<select id="check-for-duplicates" name="check_for_duplicates" class="form-control">
		    						<option value="NONE">NO DUPLICATE CHECK</option>
		    						<option value="CHECKLIST">CHECK FOR DUPLICATES BY PHONE IN LIST ID</option>
		    						<option value="CHECKCAMP">CHECK FOR DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>
		    					</select>
		    				</div>
		    			</div>
		    			<!-- <div class="form-group">
		    				<label class="control-label col-lg-4">Upload Leads:</label>
		    				<div class="col-lg-8">
								<div class="input-group">
									<input type="file" class="hide" id="leads" name="leads">
									<input type="text" class="form-control leads-holder" placeholder="Upload Leads(eg. CSV File)">
									<span class="input-group-btn">
										<button class="btn btn-default btn-leads" type="button">Browse</button>
									</span>
								</div>
		    				</div>
		    			</div> -->
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Dial Method:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="dial-method" name="dial_method">
		    						<option value="MANUAL">MANUAL</option>
		    						<option value="RATIO">AUTODIAL</option>
		    						<option value="ADAPT_TAPERED">PREDICTIVE</option>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Auto Dial Level:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="auto-dial-level" name="auto_dial_level">
		    						<option value="0">OFF</option>
		    						<option value="1: 1 RATIO">CONSERVATIVE</option>
		    						<option VALUE="1: 2 RATIO">MODERATE</option>
		    						<option VALUE="3: 1 RATIO">AGGRESIVE</option>
		    						<option value="CUSTOM DIAL LEVEL RATIO">CUSTOM</option>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Carrier to use for this campaign:</label>
		    				<div class="col-lg-8">
		    					<input id="carrier-to-use" name="carrier_to_use" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<!-- <div class="form-group">
		    				<label class="control-label col-lg-4">Description:</label>
		    				<div class="col-lg-8">
		    					<input id="description" name="description" type="text" class="form-control">
		    				</div>
		    			</div> -->
		    			<!-- <div class="form-group">
		    				<label class="control-label col-lg-4">Status:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="status" name="status">
		    						<option VALUE="ACTIVE">ACTIVE</option>
		    						<option value="INACTIVE">INACTIVE</option>
		    					</select>
		    				</div>
		    			</div> -->
		    			<div class="form-group blended">
		    				<label class="control-label col-lg-4">Call Recordings:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="call-recordings" name="call_recordings">
		    						<option VALUE="ON">ON</option>
		    						<option value="OFF">OFF</option>
		    						<option value="ON-DEMAND">ON-DEMAND</option>
		    					</select>
		    				</div>
		    			</div>
		    			<!-- <div class="form-group">
		    				<label class="control-label col-lg-4">Script:</label>
		    				<div class="col-lg-8">
		    					<input id="script" name="script" type="text" class="form-control">
		    				</div>
		    			</div> -->
		    			<div class="form-group outbound blended">
		    				<label class="control-label col-lg-4">Answering machine detection:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="answering-machine-detection" name="answering_machine_detection">
		    						<option value="ON">ON</option>
		    						<option value="OFF">OFF</option>
		    					</select>
		    				</div>
		    			</div>
		    			<!-- <div class="form-group outbound blended">
		    				<label class="control-label col-lg-4">Caller ID:</label>
		    				<div class="col-lg-8">
		    					<input id="caller_id" name="caller_id" type="text" class="form-control">
		    				</div>
		    			</div> -->
		    			<!-- <div class="form-group outbound">
		    				<label class="control-label col-lg-4">Force reset hopper:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="force-reset-hopper" name="force_reset_hopper">
		    						<option value="Y">Y</option>
		    						<option value="N">N</option>
		    					</select>
		    				</div>
		    			</div> -->
		    			<div class="form-group inbound hide">
		    				<label class="control-label col-lg-4">Campaign Recording:</label>
		    				<div class="col-lg-8">
		    					<input id="campaign-recording" name="campaign_recording" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<!-- <div class="form-group inbound hide">
		    				<label class="control-label col-lg-4">Inbound Man:</label>
		    				<div class="col-lg-8">
		    					<input id="inbound-man" name="inbound_man" type="text" class="form-control">
		    				</div>
		    			</div> -->
		    			<!-- <div class="form-group inbound blended hide">
		    				<label class="control-label col-lg-4">Phone numbers(DID/TFN) on this campaign:</label>
		    				<div class="col-lg-8">
		    					<input id="phone-numbers" name="phone_numbers" type="text" class="form-control">
		    				</div>
		    			</div> -->
		    			<div class="form-group survey hide">
		    				<label class="control-label col-lg-4">Upload WAV:</label>
		    				<div class="col-lg-8">
		    					<div class="input-group">
									<input type="text" class="form-control" placeholder="WAV upload">
									<span class="input-group-btn">
										<button class="btn btn-default" type="button">Browse</button>
									</span>
								</div><!-- /input-group -->
		    				</div>
		    			</div>
		    		</div>
    			</form>
			</div>
	      </div>
	      <div class="modal-footer wizard-buttons">
			<!-- The wizard button will be inserted here. -->
		  </div>
	      <!-- <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="add-campaign-btn">Save</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      </div> -->
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- ADD DISPOSITION MODAL -->
    <div class="modal fade" id="add_disposition" tabindex="-1" aria-labelledby="add_disposition" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">

            <!-- Header -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title animate-header" id="ingroup_modal"><b>Status Wizard » Create New Status</b></h4>
                </div>
                <div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
                
                <form action="" method="POST" id="create_disposition" class="form-horizontal " role="form">
                <!-- STEP 1 -->
                    <div class="wizard-step">
                    	<div class="form-group mt">       
                            <label class="col-sm-3 control-label" for="campaign">Campaign: </label>
                            <div class="col-sm-9 mb">
                                <select id="campaign" name="campaign" class="form-control">
                                		<option value="ALL"> - - - ALL CAMPAIGNS - - - </option>
                                   <?php
                                   		for($i=0;$i < count($campaign->campaign_id);$i++){
                                   			echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_id[$i]." - ".$campaign->campaign_name[$i]." </option>";
                                   		}
                                   ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group"> 
                            <label class="col-sm-3 control-label" for="status">Status</label>
                            <div class="col-sm-9 mb">
                                <input type="text" name="status" id="status" class="form-control" placeholder="Status. This is a required field" minlength="3" maxlenght="6">
                                <span  class="text-red"><small><i>* For example: New</i></small></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="status_name">Status Name </label>
                            <div class="col-sm-9 mb">
                                <input type="text" name="status_name" id="status_name" class="form-control" placeholder="Status Name. This is a required field">
                            </div>
                        </div>
                        <div class="form-group">        
		                        <!--<label class="col-sm-2 control-label" for="grouplevel" style="padding-top:15px;"> </label>-->
		                    <div class="col-lg-1">
		                   	</div>
		                    <div class="col-lg-11">
		                    	<div class="row">
			                		<label class="col-sm-3 checkbox-inline" for="selectable">
						                  <input type="checkbox" id="selectable" name="selectable" checked class="flat-red">
						                  Selectable
					                </label>
					                <label class="col-sm-4 checkbox-inline" for="human_answered">
						                  <input type="checkbox" id="human_answered" name="human_answered" class="flat-red">
						                  Human Answered
							        </label>
							        <label class="col-sm-3 checkbox-inline" for="sale">
						                  <input type="checkbox" id="sale" name="sale" class="flat-red">
						                  Sale
						            </label>
						        </div>
						        <div class="row">
						            <label class="col-sm-3 checkbox-inline" for="dnc">
						                  <input type="checkbox" id="dnc" name="dnc" class="flat-red">
						                  DNC
						            </label>
							          
					                <label class="col-sm-4 checkbox-inline" for="customer_contact">
						                  <input type="checkbox" id="customer_contact" name="customer_contact" class="flat-red">
						                  Customer Contact
					                </label>
					                <label class="col-sm-4 checkbox-inline" for="not_interested">
						                  <input type="checkbox" id="not_interested" name="not_interested" class="flat-red">
						                  Not Interested
					                </label>
					            </div>
						        <div class="row">
					                <label class="col-sm-3 checkbox-inline" for="unworkable">
						                  <input type="checkbox" id="unworkable" name="unworkable" class="flat-red">
						                  Unworkable
					                </label>
					                <label class="col-sm-4 checkbox-inline" for="scheduled_callback">
						                  <input type="checkbox" id="scheduled_callback" name="scheduled_callback" class="flat-red">
						                  Scheduled Callback
					                </label>
					            </div>
		                    </div>
	                    </div>
                        
                    </div><!-- end of step -->
                
                </form>

                </div> <!-- end of modal body -->

                <div class="modal-footer">
                    <!-- The wizard button will be inserted here. -->
                    <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="submit_disposition" value="Submit" style="display: inline-block;">
                </div>
            </div>
        </div>
    </div><!-- end of modal -->

	<!-- Modal -->
	<div id="view-campaign-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Campaign Information</b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="output-message-no-result hide">
		      	<div class="alert alert-warning alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Notice!</strong> There was an error retrieving details. Either error or no result.
				</div>
			</div>
	        <div id="content" class="view-form hide">
			    <div class="form-horizontal">
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Campaign ID:</label>
			    		<span class="info-camp-id control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Campaign Name:</label>
			    		<span class="info-camp-name control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Campaign Description:</label>
			    		<span class="info-camp-desc control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Allowed Inbound and Blended:</label>
			    		<span class="info-allowed control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Dial Method:</label>
			    		<span class="info-dial-method control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">AutoDial Level:</label>
			    		<span class="info-autodial-level control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Answering Machine Detection:</label>
			    		<span class="info-ans-mach control-label align-left col-lg-7"></span>
			    	</div>
			    </div>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<?php print $ui->standardizedThemeJS(); ?>
	<script src="js/easyWizard.js" type="text/javascript"></script> 
	
    <!-- iCheck 1.0.1 -->
	<script src="js/plugins/iCheck/icheck.min.js"></script>

	<!-- Script for wizard -->
	<script type="text/javascript">
		function clear_form(){

		}

		$(document).ready(function(){
			
			$('#campaign-name').keyup(function(){
				var text = $(this).val();

				if(text.length < 6){
					$(this).attr('required', true);
					$('button.wizard-button-next').addClass('hide');
					$(this).parent().addClass('has-error');
				}else{
					$(this).attr('required', false);
					$('button.wizard-button-next').removeClass('hide');
					$(this).parent().removeClass('has-error');
				}
			});

			//Flat red color scheme for iCheck
		    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		      checkboxClass: 'icheckbox_flat-green',
		      radioClass: 'iradio_flat-green'
		    });

		    //reloads page when modal closes
				$('#add_campaign').on('hidden.bs.modal', function () {
					location.reload();
				});

				$('#add_campaign').on('shown.bs.modal', function () {
				   $('#campaign-name').keyup();
				});

				$('#add_disposition').on('hidden.bs.modal', function () {
					location.reload();
				});

				//$('#add_phonenumbers').on('hidden.bs.modal', function () {
				//	window.location = window.location.href;
				//});

			// FAB HOVER
			$(".bottom-menu").on('mouseenter mouseleave', function () {
			  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
			});

			//load datatable functionalities
			$('#table_campaign').dataTable();
			$('#table_disposition').dataTable();
			$('#table_leadfilter').dataTable();

			// $('#add_campaign').modal('show');
			// $('#view-campaign-modal').modal('show');

			$('#campaign-id-edit-btn').click(function(){
				$('#campaign-id').prop('readonly',function(i,r){
			        return !r;
			    });
			});

			$('.btn-lead-file').click(function(){
				$('#lead-file').click();
			});

			$('#lead-file').change(function(){
					var myFile = $(this).prop('files');
					var Filename = myFile[0].name;

					$('.lead-file-holder').val(Filename);
					console.log($(this).val());
			});

			$('.btn-leads').click(function(){
				$('#leads').click();
			});

			$('#leads').change(function(){
					var myFile = $(this).prop('files');
					var Filename = myFile[0].name;

					$('.leads-holder').val(Filename);
					console.log($(this).val());
			});

			$("#add_campaign").wizard({
				onnext:function(){
					//alert("Nexted!");
					var campaignType = document.getElementById('campaignType').value;
					var campaign_id = document.getElementById('campaign-id').value;
					var campaign_name = document.getElementById('campaign-name').value;

					if(campaignType == null || campaignType == "" && campaign_name == null || campaign_name == ""){
					  alert("Please Fill All Required Field");
					  return false;
					}
				},
                onfinish:function(){
    				// var form_data = $('#campaign_form').serialize();
					// $.ajax({
					//   /*url: ".\php\AddCampaign.php",*/
					//   url: "./php/AddCampaign.php",
					//   type: 'POST',
					//   data: { 
					//   	form_data : form_data,
					//   },
					//   success: function(data) {
					//   	console.log(data);
					// 		// if(data == 1){
					// 		// 	$('.output-message-success').removeClass('hide');
					// 		// 	$('.output-message-error').addClass('hide');
					// 		// }else{
					// 		// 	$('.output-message-error').removeClass('hide');
					// 		// 	$('.output-message-success').addClass('hide');
					// 		// }
					//     }
					// });
					$('#campaign_form').submit();
                }
				
            });
			
			$("#add_disposition").wizard();
			
			
			$('#campaignType').change(function(){
				var selectedTypeText = $(this).find("option:selected").text();
				var selectedTypeVal = $(this).find("option:selected").val();
				$('.wizard-type').text(selectedTypeText);

				if(selectedTypeVal == 'inbound'){
					$('.outbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.survey').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.inbound').removeClass('hide');
				}else if(selectedTypeVal == 'survey'){
					$('.outbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.inbounce').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.survey').removeClass('hide');
				}else if(selectedTypeVal == 'copy'){
					$('.outbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.survey').addClass('hide');
					$('.inbound').addClass('hide');
					$('.copy-from').removeClass('hide');
				}else if(selectedTypeVal == 'blended'){
					$('.outbound').addClass('hide');
					$('.inbound').addClass('hide');
					$('.survey').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.blended').removeClass('hide');
				}else if(selectedTypeVal == 'outbound'){
					$('.inbound').addClass('hide');
					$('.blended').addClass('hide');
					$('.survey').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.outbound').removeClass('hide');
				}
			});

			$('.view-campaign').click(function(){
				var camp_id = $(this).attr('data-id');
				// alert(camp_id);
				$.ajax({
				  /*url: ".\php\ViewCampaign.php",*/
				  url: "./php/ViewCampaign.php",
				  type: 'POST',
				  data: { 
				  	campaign_id :camp_id,
				  },
				  dataType: 'json',
				  success: function(data) {
				  		// console.log(data);
				  		if(data){
				  			// info-camp-id
							// info-camp-name
							// info-camp-desc
							// info-allowed
							// info-dial-method
							// info-autodial-level
							// info-ans-mach
							$('.output-message-no-result').addClass('hide');
							$('.view-form').removeClass('hide');

							// set info here
							$('.info-camp-id').text(data.campaign_id);
							$('.info-camp-name').text(data.campaign_name);
							$('.info-dial-method').text(data.dial_method);

							$('#view-campaign-modal').modal('show');

				  		}else{
							$('.output-message-no-result').removeClass('hide');
							$('.view-form').addClass('hide');
				  		}
				    }
				});
			});
			
			//submit_disposition
            $('#submit_disposition').click(function(){
            
            $('#submit_disposition').val("Saving, Please Wait.....");
			$('#submit_disposition').prop("disabled", true);

            var validate = 0;
            var status = $("#status").val();
            var status_name = $("#status_name").val();
            
            if(status == ""){
                validate = 1;
            }

            if(status_name == ""){
                validate = 1;
            }

                if(validate == 0){
							var selectable = "Y";
		            	if(!$('#selectable').is(":checked")){
		            		selectable = "N";
		            	}
		            		var human_answered = "Y";
		            	if(!$('#human_answered').is(":checked")){
		            		human_answered = "N";
		            	}
		            		var sale = "Y";
		            	if(!$('#sale').is(":checked")){
		            		sale = "N";
		            	}
		            		var dnc = "Y";
		            	if(!$('#dnc').is(":checked")){
		            		dnc = "N";
		            	}
		            		var scheduled_callback = "Y";
		            	if(!$('#scheduled_callback').is(":checked")){
		            		scheduled_callback = "N";
		            	}
		            		var customer_contact = "Y";
		            	if(!$('#customer_contact').is(":checked")){
		            		customer_contact = "N";
		            	}
		            		var not_interested = "Y";
		            	if(!$('#not_interested').is(":checked")){
		            		not_interested = "N";
		            	}
		            		var unworkable = "Y";
		            	if(!$('#unworkable').is(":checked")){
		            		unworkable = "N";
		            	}
                    $.ajax({
                        url: "./php/AddDisposition.php",
                        type: 'POST',
                        data: {
		                    	campaign : $('#campaign').val(),
		                    	status : $('#status').val(),
					    		status_name : $('#status_name').val(),
					   			selectable : selectable,
					    		human_answered : human_answered,
					    		sale : sale,
					    		dnc : dnc,
					    		scheduled_callback : scheduled_callback,
					    		customer_contact : customer_contact,
					    		not_interested : not_interested,
					    		unworkable : unworkable
		                    },
                        success: function(data) {
                          // console.log(data);
                              if(data == 1){
                                    swal("Success!", "Disposition Statuses Successfully Created!", "success")
                                    window.setTimeout(function(){location.reload()},3000)
                                    $('#submit_disposition').val("Loading...");
                              }
                              else{
                                  sweetAlert("Oops...", "Something went wrong!"+data, "error");
                                  $('#submit_disposition').val("Submit");
								  $('#submit_disposition').prop("disabled", false);
                              }
                        }
                    });
                	
                }else{
                    sweetAlert("Oops...", "Something went wrong!", "error");
                    validate = 0;
                    $('#submit_disposition').val("Submit");
					$('#submit_disposition').prop("disabled", false);
                }
            });
			/*
			 *
			 * Edit Actions
			 *
			*/
			//EDIT CAMPAIGN
			$(document).on('click','.edit-campaign',function() {
				var url = './edittelephonycampaign.php';
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="campaign" value="' + $(this).attr('data-id') + '" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			 });
			 //EDIT disposition
			 $(document).on('click','.edit_disposition',function() {
				var url = './edittelephonycampaign.php';
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="disposition_id" value="' + $(this).attr('data-id') + '" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			 });
			 //EDIT PHONENUMBER/DID
			 $(document).on('click','.edit-leadfilter',function() {
				var url = './edittelephonycampaign.php';
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="leadfilter" value="' + $(this).attr('data-id') + '" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			 });


			/**
             * Delete validation modal
             */
             // CAMPAIGN
             $(document).on('click','.delete-campaign',function() {
                var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this campaign!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
			                        url: "./php/DeleteCampaign.php",
			                        type: 'POST',
			                        data: { 
			                            campaign_id:id,
			                        },
			                        success: function(data) {
			                        console.log(data);
			                            if(data == 1){
			                                swal("Success!", "Campaign Successfully Deleted!", "success");
			                                window.setTimeout(function(){location.reload()},1000)
			                            }else{
			                                sweetAlert("Oops...", "Something went wrong! "+data, "error");
			                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
			                            }
			                        }
			                    });
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
             });
             // DISPOSITION
             $(document).on('click','.delete_disposition',function() {
                var id = $(this).attr('data-id');
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
			                        },
			                        success: function(data) {
			                        console.log(data);
			                            if(data == 1){
			                                swal("Success!", "Disposition Successfully Deleted!", "success");
			                                window.setTimeout(function(){location.reload()},1000)
			                            }else{
			                                sweetAlert("Oops...", "Something went wrong! "+data, "error");
			                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
			                            }
			                        }
			                    });
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
             });
             // LEAD FILTER
             $(document).on('click','.delete_leadfilter',function() {
                var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this leadfilter!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
			                        url: "./php/DeleteLeadFilter.php",
			                        type: 'POST',
			                        data: { 
			                            leadfilter_id:id,
			                        },
			                        success: function(data) {
			                        console.log(data);
			                            if(data == 1){
			                               	swal("Success!", "Lead Filter Successfully Deleted!", "success");
			                                window.setTimeout(function(){location.reload()},1000)
			                            }else{
			                                sweetAlert("Oops...", "Something went wrong! "+data, "error");
			                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
			                            }
			                        }
			                    });
							} else {     
		                			swal("Cancelled", "No action has been done :)", "error");   
		                	} 
	                	}
	                );
            });
		});
	</script>
	<!-- End of script -->

        <script>
        	// load data.
            $(".textarea").wysihtml5();
	</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
