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
    	<link href="css/style.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

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
	$disposition = $ui->API_getAllDispositions("custom");
	$leadfilter = $ui->API_getAllLeadFilters();
	$country_codes = $ui->getCountryCodes();
	$list = $ui->API_goGetAllLists();
?>
							 <div role="tabpanel">
								<ul role="tablist" class="nav nav-tabs nav-justified">

								 <!-- Campaign panel tabs-->
									 <li role="presentation" class="active">
										<a href="#T_campaign" aria-controls="T_campaign" role="tab" data-toggle="tab" class="bb0">
										   Campaigns </a>
									 </li>
								<!-- Disposition panel tab -->
									 <li role="presentation">
										<a href="#T_disposition" aria-controls="T_disposition" role="tab" data-toggle="tab" class="bb0">
										   Dispositions </a>
									 </li>
								<!-- LeadFilter panel tab
									 <li role="presentation">
										<a href="#T_leadfilter" aria-controls="T_leadfilter" role="tab" data-toggle="tab" class="bb0">
										   Lead Filters </a>
									 </li>
								-->
								  </ul>

								<!-- Tab panes-->
								<div class="tab-content bg-white">

								<!--==== Campaigns ====-->
								  <div id="T_campaign" role="tabpanel" class="tab-pane active">
										<table class="table table-striped table-bordered table-hover" id="table_campaign">
										   <thead>
											  <tr>
                                                 <th style="color: white;">Pic</th>
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
                                                        <td><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='36'></avatar></td>
														<td class='hide-on-medium hide-on-low'><strong><a class="edit-campaign" data-id="<?php echo $campaign->campaign_id[$i];?>" data-name="<?php echo $campaign->campaign_name[$i];?>"><?php echo $campaign->campaign_id[$i];?></strong></td>
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
                                                                                                 <th style="color: white;">Pic</th>
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
                                                        <td><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='36'></avatar></td>
														<td class='hide-on-medium hide-on-low'><strong><a class='edit_disposition' data-id="<?php echo $campaign->campaign_id[$i];?>" data-name="<?php echo $campaign->campaign_name[$i];?>"><?php echo $campaign->campaign_id[$i];?></strong></td>
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
                                                 <th style="color: white;">Pic</th>
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
                                                        <td><avatar username='<?php echo $leadfilter->lead_filter_name[$i];?>' :size='36'></avatar></td>
														<td><?php echo $leadfilter->lead_filter_id[$i];?></td>
														<td><strong><a class=''><?php echo $leadfilter->lead_filter_name[$i];?></a></strong></td>
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
				<ul class="fab-ul" style="height: 170px;">
					<li class="li-style"><a class="fa fa-dashboard fab-div-item" data-toggle="modal" data-target="#add_campaign" title="Add Campaign"></a></li><br/>
					<li class="li-style"><a class="fa fa-tty fab-div-item" data-toggle="modal" data-target="#add_disposition" title="Add Disposition"></a></li><br/>
					<!--<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_leadfilter" title="Add Phone Numbers"> </a></li>-->
				</ul>
			</div>
		</div>

	</div><!-- ./wrapper -->


<!-- View Campaign Modal -->
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

<!-- MODAL WIZARDS -->

	<!-- Campaign Modal -->
		<div id="add_campaign" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">

		        <h4 class="modal-title animated bounceInRight">
		        	<i class="fa fa-info-circle" title="A step by step wizard that allows you to create campaigns."></i>
		        	<b>Campaign Wizard » <span class="wizard-type">Outbound</span></b>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
		       	</h4>
		      </div>
		      <div class="modal-body">
		        <div id="content">
							<div class="alert alert-danger campaign-checker-message hide">
							  <strong>Error!</strong> Campaign ID already exist. Eneter a new Campaign ID.
							</div>
					<!-- Custom Tabs (Pulled to the right) -->
					<form id="campaign_form" method="POST" action="./php/AddCampaign.php" enctype="multipart/form-data">
						<div class="row">
							<h4>Campaign Information
	                           <br>
	                           <small>Campaign Details</small>
	                        </h4>
	                        <fieldset>
								<div class="form-group">
				    				<label class="control-label col-lg-4">Campaign Type:</label>
				    				<div class="col-lg-8 mb">
				    					<select id="campaignType" name="campaign_type" class="form-control">
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
				    				<div class="col-lg-8 mb">
				    					<div class="input-group">
									      <input id="campaign-id" name="campaign_id" type="number" class="form-control" placeholder="" value="<?php echo str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT); ?>" maxlength="8" readonly>
									      <span class="input-group-btn">
									        <button id="campaign-id-edit-btn" class="btn btn-default" type="button" style="min-height: 34px;"><i class="fa fa-pencil"></i></button>
									      </span>
									    </div><!-- /input-group -->
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Campaign Name:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="campaign-name" name="campaign_name" type="text" class="form-control" title="Must be 6 to 40 characters in length." minlength="6" maxlength="40" required>
				    				</div>
				    			</div>
				    			<div class="form-group inbound blended hide">
				    				<label class="control-label col-lg-4">DID/TFN Extension:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="did-tfn-extension" name="did_tfn_extension" type="number" class="form-control" min="0" required>
				    				</div>
				    			</div>
				    			<div class="form-group inbound blended hide">
				    				<label class="control-label col-lg-4">Call Route:</label>
				    				<div class="col-lg-8 mb">
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
				    				<div class="col-lg-8 mb">
				    					<input id="group-color" name="group_color" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group survey hide">
				    				<label class="control-label col-lg-4">Survey Type:</label>
				    				<div class="col-lg-8 mb">
				    					<select id="survey-type" name="survey_type" class="form-control">
				                            <option value="BROADCAST">VOICE BROADCAST</option>
				                            <option value="PRESS1">SURVEY PRESS 1</option>
				                        </select>
				    				</div>
				    			</div>
				    			<div class="form-group survey hide">
				    				<label class="control-label col-lg-4">Number of Channels:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="no-channels" name="no_channels" type="number" value="1" min="1" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group copy-from hide">
				    				<label class="control-label col-lg-4">Copy from campaign:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="copy-from-campaign" name="copy_from_campaign" type="text" class="form-control">
				    				</div>
				    			</div>
				    		</fieldset>

				    		<!-- STEP 2 -->
							<h4>Selecting a Dial Method
	                           <br>
	                           <small>Assign then Enter Account and Login Details</small>
	                        </h4>
	                        <fieldset>
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
			    			<!--<div class="form-group">
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
			    					<select id="country" name="country" class="form-control select2">
			    						<?php if ($country_codes->result=="success") { ?>
											<?php for($i=0;$i < count($country_codes->country);$i++){ ?>
												<option value="<?php echo $country_codes->country_code[$i]?>">
													<?php echo $country_codes->country_code[$i]?> >> <?php echo $country_codes->country[$i]?>
												</option>
											<?php } ?>
										<?php } else { ?>
											No record found.
										<?php } ?>
			    					</select>
			    				</div>
			    			</div>
			    			<div class="form-group">
			    				<label class="control-label col-lg-4">Check for duplicates:</label>
			    				<div class="col-lg-8">
			    					<select id="check-for-duplicates" name="check_for_duplicates" class="form-control">
			    						<option value="NONE">NO DUPLICATE CHECK</option>
			    						<option value="CHECKLIST">CHECK FOR DUPLICATES BY PHONE IN LIST ID</option>
			    						<option value="CHECKCAMP">CHECK FOR DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>
			    					</select>
			    				</div>
			    			</div>-->
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
				    				<label class="control-label col-lg-5">Dial Method:</label>
				    				<div class="col-lg-7 mb">
				    					<select class="form-control" id="dial-method" name="dial_method">
				    						<option value="MANUAL">MANUAL</option>
				    						<option value="RATIO">AUTODIAL</option>
				    						<option value="ADAPT_TAPERED">PREDICTIVE</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group auto-dial-level hide">
				    				<label class="control-label col-lg-5">Auto Dial Level:</label>
				    				<div class="col-lg-7 mb">
				    					<select class="form-control" id="auto-dial-level" name="auto_dial_level">
				    						<option value="0">OFF</option>
				    						<option value="1.0">NORMAL</option>
				    						<option VALUE="2.0">MODERATE</option>
				    						<option VALUE="4.0">AGGRESIVE</option>
				    						<!-- <option value="CUSTOM">CUSTOM</option> -->
				    					</select>
				    				</div>
				    			</div>
				    			<?php $carriers = $ui->getCarriers(); ?>
				    			<div class="form-group carrier-to-use">
				    				<label class="control-label col-lg-5">Carrier to use for this campaign:</label>
				    				<div class="col-lg-7 mb">
				    					<div class="row">
				    						<div class="col-lg-12">
												<select name="dial_prefix" id="dial_prefix" class="form-control">
													<option value="CUSTOM" selected="selected">CUSTOM DIAL PREFIX</option>
													<?php for($i=0;$i<=count($carriers->carrier_id);$i++) { ?>
														<?php if(!empty($carriers->carrier_id[$i])) { ?>
															<option value="<?php echo $carriers->carrier_id[$i]; ?>" <?php if($campaign->data->dial_prefix == $carriers->carrier_id[$i]) echo "selected";?>><?php echo $carriers->carrier_name[$i]; ?></option>
														<?php } ?>
													<?php } ?>
												</select>
											</div>
											<div class="col-lg-12 mt custom-prefix">
												<input type="number" class="form-control" id="custom_prefix" name="custom_prefix" value="9" min="0" max="15" required>
											</div>
										</div>
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
				    				<label class="control-label col-lg-5">Campaign Recordings:</label>
				    				<div class="col-lg-7 mb">
				    					<select class="form-control" id="call-recordings" name="campaign_recording">
				    						<option value="NEVER">OFF</option>
				    						<option value="ALLFORCE">ON</option>
				    						<option value="ONDEMAND">ON-DEMAND</option>
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
				    				<label class="control-label col-lg-5">Answering machine detection:</label>
				    				<div class="col-lg-7 mb">
				    					<select class="form-control" id="answering-machine-detection" name="answering_machine_detection">
				    						<option value="8369">ON</option>
				    						<option value="8368">OFF</option>
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
				    			<!-- <div class="form-group inbound hide">
				    				<label class="control-label col-lg-5">Campaign Recording:</label>
				    				<div class="col-lg-7 mb">
				    					<input id="campaign-recording" name="campaign_recording" type="text" class="form-control">
				    				</div>
				    			</div> -->
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
				    				<label class="control-label col-lg-5">Upload WAV:</label>
				    				<div class="col-lg-7 mb">
				    					<div class="input-group">
											<input type="text" class="form-control" placeholder="WAV upload">
											<span class="input-group-btn">
												<button class="btn btn-default" type="button">Browse</button>
											</span>
										</div><!-- /input-group -->
				    				</div>
				    			</div>
				    		</fieldset><!-- end of step 2 -->
			    		</div><!-- ./row -->
	    			</form>
				</div>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>
	<!-- End of modal -->

	<!-- Disposition Modal -->
	    <div class="modal fade" id="add_disposition" tabindex="-1" aria-labelledby="add_disposition" >
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">

	            <!-- Header -->
	                <div class="modal-header">
	                    <h4 class="modal-title animated bounceInRight" id="ingroup_modal">
	                    	<b>Status Wizard » Create New Status</b>
	                    	<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
	                    </h4>
	                </div>
	                <div class="modal-body">

	                <form action="#" method="POST" id="create_disposition" role="form">
	                    <div class="row">
	                    	<h4>Create Disposition
	                           <br>
	                           <small>Assign a status in a campaign then fill up the information below </small>
	                        </h4>
	                        <fieldset>
		                    	<div class="form-group mt">
		                            <label class="col-sm-3 control-label" for="disposition_campaign">Campaign: </label>
		                            <div class="col-sm-9 mb">
		                                <select id="disposition_campaign" name="disposition_campaign" class="form-control">
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
		                                <input type="text" name="status" id="status" class="form-control" placeholder="Status (Mandatory)" minlength="3" maxlenght="6" required>
		                            	<label id="status-duplicate-error"></label>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="status_name">Status Name </label>
		                            <div class="col-sm-9 mb">
		                                <input type="text" name="status_name" id="status_name" class="form-control" placeholder="Status Name (Mandatory)" maxlenght="30" required>
		                            </div>
		                        </div>
		                        <div class="form-group">
				                        <!--<label class="col-sm-2 control-label" for="grouplevel" style="padding-top:15px;"> </label>-->
				                    <div class="col-lg-1">
				                   	</div>
				                    <div class="col-lg-11 mt">
				                    	<div class="row">
				                    		<label class="col-sm-3 checkbox-inline c-checkbox" for="selectable">
												<input type="checkbox" id="selectable" name="selectable" checked>
												<span class="fa fa-check"></span> Selectable
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="human_answered">
												<input type="checkbox" id="human_answered" name="human_answered">
												<span class="fa fa-check"></span> Human Answered
											</label>
											<label class="col-sm-3 checkbox-inline c-checkbox" for="sale">
												<input type="checkbox" id="sale" name="sale">
												<span class="fa fa-check"></span> Sale
											</label>
								        </div>
								        <div class="row">
								        	<label class="col-sm-3 checkbox-inline c-checkbox" for="dnc">
												<input type="checkbox" id="dnc" name="dnc">
												<span class="fa fa-check"></span> DNC
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="customer_contact">
												<input type="checkbox" id="customer_contact" name="customer_contact">
												<span class="fa fa-check"></span> Customer Contact
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="not_interested">
												<input type="checkbox" id="not_interested" name="not_interested">
												<span class="fa fa-check"></span> Not Interested
											</label>
							            </div>
								        <div class="row">
								        	<label class="col-sm-3 checkbox-inline c-checkbox" for="unworkable">
												<input type="checkbox" id="unworkable" name="unworkable">
												<span class="fa fa-check"></span> Unworkable
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="scheduled_callback">
												<input type="checkbox" id="scheduled_callback" name="scheduled_callback">
												<span class="fa fa-check"></span> Scheduled Callback
											</label>
							            </div>
				                    </div>
			                    </div>
	                        </fieldset>
	                    </div><!-- end of step -->
	                	<input type="hidden" id="disposition_checker" value="0">
	                </form>

	                </div> <!-- end of modal body -->
	            </div>
	        </div>
	    </div>
    <!-- end of modal -->


	<!-- End of modal -->

	<?php print $ui->standardizedThemeJS(); ?>
	<!-- JQUERY STEPS-->
  	<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>

    <!-- iCheck 1.0.1 -->
	<script src="js/plugins/iCheck/icheck.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function(){

			/******
			** Initializations
			******/

				//initialization of the datatables
					$('#table_campaign').dataTable();
					$('#table_disposition').dataTable();
					$('#table_leadfilter').dataTable();

				// FAB HOVER
					$(".bottom-menu").on('mouseenter mouseleave', function () {
					  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
					});

				//reloads page when modal closes
				/*	$('#add_campaign').on('hidden.bs.modal', function () {
						location.reload();
					});

					$('#add_campaign').on('shown.bs.modal', function () {
					   $('#campaign-name').keyup();
					});

					$('#add_disposition').on('hidden.bs.modal', function () {
						location.reload();
					});
				*/

			/*************
			** Campaign Events
			*************/
				//add + initialization of campaign
					var campaign_form = $("#campaign_form"); // init form wizard

				    campaign_form.validate({
				        errorPlacement: function errorPlacement(error, element) { element.after(error); }
				    });

				    campaign_form.children("div").steps({
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
						        $(".body:eq(" + newIndex + ") label.error", disposition_form).remove();
						        $(".body:eq(" + newIndex + ") .error", disposition_form).removeClass("error");
						    	}

				          campaign_form.validate().settings.ignore = ":disabled,:hidden";
				          return campaign_form.valid();
				        },
				        onFinishing: function (event, currentIndex)
				        {
				            //campaign_form.validate().settings.ignore = ":disabled";

				            return campaign_form.valid();
				        },
				        onFinished: function (event, currentIndex)
				        {
									var campaign_id = $('#campaign-id').val();
									var resultCheck = checkCampaign(campaign_id);
									console.log(resultCheck);
									if(resultCheck == 1){
										swal({
											title: "Proceed with saving campaign?",
											text: "This action cannot be undone.",
											type: "warning",
											showCancelButton: true,
											confirmButtonColor: "#DD6B55",
											confirmButtonText: "Yes, save Campaign!",
											cancelButtonText: "No",
											closeOnConfirm: false,
											closeOnCancel: false
											},
											function(isConfirm){
												if (isConfirm) {
													$('#finish').text("Loading...");
								        	$('#finish').attr("disabled", true);

								        	$('#campaign_form').submit();
												} else {
													swal("Cancelled", "Campaign not saved)", "error");
													$('#campaign-name').val('');
													campaign_form.children("div").steps("previous");
													$('#add_campaign').modal('hide');
												}
											});
											$('.campaign-checker-message').addClass('hide');
									}else{
										campaign_form.children("div").steps("previous");
										$('.campaign-checker-message').removeClass('hide');
										$('#campaign-id').focus();
									}

				        }
				    });
				/*
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
						$('#campaign_form').submit();
		            }

		        });
				*/
				//view campaign
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

				//edit campaign
					$(document).on('click','.edit-campaign',function() {
						var url = './edittelephonycampaign.php';
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="campaign" value="' + $(this).attr('data-id') + '" /></form>');
						//$('body').append(form);  // This line is not necessary
						$(form).submit();
					});

				//delete campaign
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
					                            	swal(
														{
															title: "Success",
															text: "Campaign Successfully Deleted!",
															type: "success"
														},
														function(){
															window.location.href = 'telephonycampaigns.php';
														}
													);
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
			//------------------ end of campaign

			/*************
			** Disposition Events
			*************/

				// initialization and add of disposition
					var disposition_form = $("#create_disposition"); // init form wizard

				    disposition_form.validate({
				        errorPlacement: function errorPlacement(error, element) { element.after(error); }
				    });

				    disposition_form.children("div").steps({
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
					        $(".body:eq(" + newIndex + ") label.error", disposition_form).remove();
					        $(".body:eq(" + newIndex + ") .error", disposition_form).removeClass("error");
					    }

			            disposition_form.validate().settings.ignore = ":disabled,:hidden";
			            return disposition_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            disposition_form.validate().settings.ignore = ":disabled";

			            var num_errors = $("#disposition_checker").val();

			            console.log(num_errors);
				        // Disable submit if there are duplicates
				        if(num_errors > 0){
					        $(".body:eq(" + currentIndex + ") .error", disposition_form).addClass("error");
				        	return false;
				        }

			            return disposition_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

						var selectable = "Y";
						var human_answered = "Y";
						var sale = "Y";
						var dnc = "Y";
						var scheduled_callback = "Y";
						var customer_contact = "Y";
						var not_interested = "Y";
						var unworkable = "Y";

			            	if(!$('#selectable').is(":checked")){
			            		selectable = "N";
			            	}
			            	if(!$('#human_answered').is(":checked")){
			            		human_answered = "N";
			            	}
			            	if(!$('#sale').is(":checked")){
			            		sale = "N";
			            	}
			            	if(!$('#dnc').is(":checked")){
			            		dnc = "N";
			            	}
			            	if(!$('#scheduled_callback').is(":checked")){
			            		scheduled_callback = "N";
			            	}
			            	if(!$('#customer_contact').is(":checked")){
			            		customer_contact = "N";
			            	}
			            	if(!$('#not_interested').is(":checked")){
			            		not_interested = "N";
			            	}
			            	if(!$('#unworkable').is(":checked")){
			            		unworkable = "N";
			            	}

			            // submit
		                $.ajax({
		                    url: "./php/AddDisposition.php",
		                    type: 'POST',
		                    data: {
			                    	campaign : $('#disposition_campaign').val(),
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
		                                swal(
											{
												title: "Success",
												text: "Disposition Statuses Successfully Created!",
												type: "success"
											},
											function(){
												window.location.href = 'telephonycampaigns.php';
												$(".preloader").fadeIn();
											}
										);
		                          }
		                          else{
		                              sweetAlert("Oops...", "Something went wrong!"+data, "error");
		                              $('#finish').val("Submit");
									  $('#finish').prop("disabled", false);
		                          }
		                    }
		                });

			        }
			    });

				//edit disposition
					$(document).on('click','.edit_disposition',function() {
						var url = './edittelephonycampaign.php';
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="disposition_id" value="' + $(this).attr('data-id') + '" /></form>');
						$(form).submit();
					});

				//delete disposition
			        $(document).on('click','.delete_disposition', function() {
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
				                            	swal({
														title: "Success",
														text: "Disposition Successfully Deleted!",
														type: "success"
													},
													function(){
														window.location.href = 'telephonycampaigns.php';
														$(".preloader").fadeIn();
													}
												);
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
			// ----------------- end of disposition

			/*************
			** Lead Filter Events
			*************/

				//edit leadfilter
					$(document).on('click','.edit-leadfilter',function() {
						var url = './edittelephonycampaign.php';
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="leadfilter" value="' + $(this).attr('data-id') + '" /></form>');
						//$('body').append(form);  // This line is not necessary
						$(form).submit();
					});

		        //delete leadfilter
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
				                            	swal(
													{
														title: "Success",
														text: "Lead Filter Successfully Deleted!",
														type: "success"
													},
													function(){
														window.location.href = 'telephonycampaigns.php';
													}
												);
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
			//------------------ end of leadfilter

			/********
			** Other Events
			********/

				/*** CAMPAIGNS ***/
					// result of Create
						<?php
							if($_GET['message'] == "Success") {
						?>
								swal(
									{
										title: "Success",
										text: "Campaign Successfully Created!",
										type: "success"
									},
									function(){
										window.location.href = 'telephonycampaigns.php';
									}
								);
						<?php
							}elseif($_GET['message'] == "error"){
						?>
								sweetAlert("Oops...", "Something went wrong.", "error");
						<?php

							}
						?>
					//name
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

					//dialmethod
						var dial_method = $('#dial-method').val();

						dialMethod(dial_method);

						$('#dial-method').change(function(){
							dialMethod($(this).val());
						});

					//dial prefix
						$('#dial_prefix').change(function(){
							var dial_prefix = $(this).val();

							if(dial_prefix == "CUSTOM"){
								$('.custom-prefix').removeClass('hide');
							}else{
								$('.custom-prefix').addClass('hide');
							}
						});

					//campaign id
						$('#campaign-id-edit-btn').click(function(){
							$('#campaign-id').prop('readonly',function(i,r){
						        return !r;
						    });
						});

					//lead file
						//onclick events
							$('.btn-lead-file').click(function(){
								$('#lead-file').click();
							});

							$('.btn-leads').click(function(){
								$('#leads').click();
							});

						//onchange events
							$('#lead-file').change(function(){
									var myFile = $(this).prop('files');
									var Filename = myFile[0].name;

									$('.lead-file-holder').val(Filename);
									console.log($(this).val());
							});

							$('#leads').change(function(){
								var myFile = $(this).prop('files');
								var Filename = myFile[0].name;

								$('.leads-holder').val(Filename);
								console.log($(this).val());
							});

					//campaign type
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
								$('.carrier-to-use').addClass('hide');
							}else if(selectedTypeVal == 'survey'){
								$('.outbound').addClass('hide');
								$('.blended').addClass('hide');
								$('.inbounce').addClass('hide');
								$('.copy-from').addClass('hide');
								$('.survey').removeClass('hide');
								$('.carrier-to-use').addClass('hide');
							}else if(selectedTypeVal == 'copy'){
								$('.outbound').addClass('hide');
								$('.blended').addClass('hide');
								$('.survey').addClass('hide');
								$('.inbound').addClass('hide');
								$('.copy-from').removeClass('hide');
								$('.carrier-to-use').addClass('hide');
							}else if(selectedTypeVal == 'blended'){
								$('.outbound').addClass('hide');
								$('.inbound').addClass('hide');
								$('.survey').addClass('hide');
								$('.copy-from').addClass('hide');
								$('.blended').removeClass('hide');
								$('.carrier-to-use').removeClass('hide');
							}else if(selectedTypeVal == 'outbound'){
								$('.inbound').addClass('hide');
								$('.blended').addClass('hide');
								$('.survey').addClass('hide');
								$('.copy-from').addClass('hide');
								$('.outbound').removeClass('hide');
								$('.carrier-to-use').removeClass('hide');
							}
						});
				/*** end of campaigns ***/

				/*** DISPOSITION ***/
					// check duplicates
						$("#status").keyup(function() {
							clearTimeout($.data(this, 'timer'));
							var wait = setTimeout(duplicate_status_check, 500);
							$(this).data('timer', wait);
						});

						$('#disposition_campaign').change(function(){
							duplicate_status_check();
						});
				/*** end of disposition ***/

			/********
			** Other Filters
			********/
				/*** CAMPAIGNS ***/
					// disable special characters on Campaign ID
						$('#campaign-id').bind('keypress', function (event) {
						    var regex = new RegExp("^[a-zA-Z0-9]+$");
						    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						    if (!regex.test(key)) {
						       event.preventDefault();
						       return false;
						    }
						});
					// disable special characters on Campaign Name
						$('#campaign-name').bind('keypress', function (event) {
						    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
						    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						    if (!regex.test(key)) {
						       event.preventDefault();
						       return false;
						    }
						});
				/*** end campaign ***/

				/*** DISPOSITION ***/
					// disable special characters on User ID
						$('#status').bind('keypress', function (event) {
						    var regex = new RegExp("^[a-zA-Z0-9]+$");
						    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						    if (!regex.test(key)) {
						       event.preventDefault();
						       return false;
						    }
						});
						$('#status_name').bind('keypress', function (event) {
						    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
						    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						    if (!regex.test(key)) {
						       event.preventDefault();
						       return false;
						    }
						});
				/*** end of disposition filters ***/




		}); // end of document ready


		function clear_form(){

		}

		function dialMethod(value){
			if(value == "RATIO"){
				$('#auto-dial-level').prop('disabled', false);
				$('#auto-dial-level option[value="1.0"]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
			}else if(value == "ADAPT_TAPERED"){
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value="0"]').prop('selected', true);
				$('div.auto-dial-level').addClass('hide');
			}else{
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value="0"]').prop('selected', true);
				$('div.auto-dial-level').addClass('hide');
			}
		}

		function checkCampaign(campaign_id){
			var status = '';
			$.ajax({
				/*url: ".\php\ViewCampaign.php",*/
				url: "./php/checkCampaign.php",
				type: 'POST',
				async: false,
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(data) {
					// console.log(data);
					var info = $.parseJSON(data);
					if(info.result == "success"){
						status = 1;
					}else{
						status = info.status;
					}
				}
			});

			return status;
		}

		function duplicate_status_check(){
			var status_form_value = $('#status').val();
			var campaign_form_value = $('#disposition_campaign').val();
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
							$("#disposition_checker").val("0");

							$( "#status" ).removeClass("error");
							$( "#status-duplicate-error" ).text( "Status is available." ).removeClass("error").addClass("avail");
						}else{
							$("#disposition_checker").val("1");

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
