<?php

	###########################################################
	### Name: telephonycampaigns.php 		   ###
	### Functions: Manage Campaigns, Disposition		   ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016		   ###
	### Version: 4.0 		   ###
	### Written by: Alexander Abenoja & Noel Umandap		   ###
	### License: AGPLv2		   ###
	###########################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	$perm = $ui->goGetPermissions('campaign,disposition,pausecodes,hotkeys,list', $_SESSION['usergroup']);
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
		<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> <<< THIS IS CAUSING THE TOOLTIP PROBLEM -->
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		<!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
		<!-- bootstrap color picker -->
		<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>
        <!-- SELECT2-->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
   		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>
		
			<style type="text/css">
				.select2-container{
					width: 100% !important;
				}
				
				.select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
					margin-top: 1px;
				}
				
				.ui-autocomplete {
					position: absolute;
					top: 100%;
					left: 0;
					z-index: 1000;
					float: left;
					display: none;
					min-width: 160px;
					_width: 160px;
					padding: 5px 4px;
					/*margin: 2px 0 0 0;*/
					list-style: none;
					background-color: #ffffff;
					border-color: #ccc;
					border-color: rgba(0, 0, 0, 0.2);
					border-style: solid;
					border-width: 1px;
					-webkit-border-radius: 5px;
					-moz-border-radius: 5px;
					border-radius: 5px;
					-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
					-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
					box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
					-webkit-background-clip: padding-box;
					-moz-background-clip: padding;
					background-clip: padding-box;
					/**border-right-width: 2px;*/
					/**border-bottom-width: 2px;*/
				  
					.ui-menu-item > a.ui-corner-all {
					  display: block;
					  padding: 3px 15px;
					  clear: both;
					  font-weight: normal;
					  line-height: 18px;
					  color: #555555;
					  white-space: nowrap;
				  
					  &.ui-state-hover, &.ui-state-active {
						color: #ffffff;
						text-decoration: none;
						background-color: #0088cc;
						border-radius: 0px;
						-webkit-border-radius: 0px;
						-moz-border-radius: 0px;
						background-image: none;
					  }
					}
				}
			</style>
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
                <?php if ($perm->campaign->campaign_read !== 'N') { ?>
<?php

	/*
	 * API used for display in tables
	 */
	$campaign = $ui->API_getListAllCampaigns();
	$disposition = $ui->API_getAllDispositions("custom");
	$leadfilter = $ui->API_getAllLeadFilters();
	$country_codes = $ui->getCountryCodes();
	$list = $ui->API_goGetAllLists();
	$ingroup = $ui->API_getInGroups();
	$ivr = $ui->API_getIVR();
	$voicemails = $ui->API_goGetVoiceMails();
	$users = $ui->API_goGetAllUserLists();
	$carriers = $ui->getCarriers();
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
												 <th class='hide-on-medium hide-on-low' style='width:0px;'>Campaign ID</th>
												 <th >Campaign Name</th>
												 <th class='hide-on-medium hide-on-low'>Dial Method</th>
												 <th class='hide-on-medium hide-on-low'>Status</th>
												 <th style="width:16%;">Action</th>
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
															$dial_method = "AUTO DIAL";
														}

														if($campaign->dial_method[$i] == "MANUAL"){
															$dial_method = "MANUAL";
														}

														if($campaign->dial_method[$i] == "ADAPT_TAPERED"){
															$dial_method = "PREDICTIVE";
														}

														if($campaign->dial_method[$i] == "INBOUND_MAN"){
															$dial_method = "INBOUND MAN";
														}

													$action_CAMPAIGN = $ui->ActionMenuForCampaigns($campaign->campaign_id[$i], $campaign->campaign_name[$i], $perm);

											   	?>
													<tr>
														<td><?php if ($perm->campaign->campaign_update !== 'N') { echo '<a class="edit-campaign" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='32'></avatar><?php if ($perm->campaign->campaign_update !== 'N') { echo '</a>'; } ?></td>
														<td class='hide-on-medium hide-on-low'><strong><?php if ($perm->campaign->campaign_update !== 'N') { echo '<a class="edit-campaign" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><?php echo $campaign->campaign_id[$i];?><?php if ($perm->campaign->campaign_update !== 'N') { echo '</a>'; } ?></strong></td>
														<td><?php echo $campaign->campaign_name[$i];?></td>
														<td class='hide-on-medium hide-on-low'><?php echo $dial_method;?></td>
														<td class='hide-on-medium hide-on-low'><?php echo $campaign->active[$i];?></td>
														<td nowrap><?php echo $action_CAMPAIGN;?></td>
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
												 <th class='action_disposition'>Action</th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		for($i=0;$i < count($campaign->campaign_id);$i++){

													$action_DISPOSITION = $ui->ActionMenuForDisposition($campaign->campaign_id[$i], $campaign->campaign_name[$i], $perm);

											   	?>
													<tr>
														<td><?php if ($perm->disposition->disposition_update !== 'N') { echo '<a class="edit_disposition" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='32'></avatar><?php if ($perm->disposition->disposition_update !== 'N') { echo '</a>'; } ?></td>
														<td class='hide-on-medium hide-on-low'><strong><?php if ($perm->disposition->disposition_update !== 'N') { echo '<a class="edit_disposition" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><?php echo $campaign->campaign_id[$i];?><?php if ($perm->disposition->disposition_update !== 'N') { echo '</a>'; } ?></strong></td>
														<td><?php echo $campaign->campaign_name[$i];?></td>
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
														<td style="width:16%;"><?php echo $action_DISPOSITION;?></td>
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
														<td nowrap><?php echo $action_LEADFILTER;?></td>
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
							<div class="bottom-menu skin-blue <?php if ($perm->campaign->campaign_create == 'N' && $perm->disposition->disposition_create == 'N') { echo "hidden"; } ?>">
								<div class="action-button-circle" data-toggle="modal">
									<?php print $ui->getCircleButton("campaigns", "plus"); ?>
								</div>
								<div class="fab-div-area" id="fab-div-area">
									<ul class="fab-ul" style="height: <?php if ($perm->campaign->campaign_create == 'N' || $perm->disposition->disposition_create == 'N') { echo "110px"; } else { echo "170px"; } ?>;">
										<li class="li-style<?=($perm->campaign->campaign_create == 'N' ? ' hidden' : '')?>"><a class="fa fa-dashboard fab-div-item" data-toggle="modal" data-target="#add_campaign" title="Add Campaign"></a></li><br/>
										<li class="li-style<?=($perm->disposition->disposition_create == 'N' ? ' hidden' : '')?>"><a class="fa fa-tty fab-div-item" data-toggle="modal" data-target="#add_disposition" title="Add Disposition"></a></li><br/>
										<!--<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_leadfilter" title="Add Phone Numbers"> </a></li>-->
									</ul>
								</div>
							</div>
						</div><!-- /.body -->
					</div><!-- /.panel -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
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
						<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
						<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
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
									      <input id="campaign-id" name="campaign_id" type="number" class="form-control" placeholder="" value="<?php echo str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT); ?>" min="0" minlength="3" maxlength="8" readonly onkeydown="return FilterInput(event)">
									      <span class="input-group-btn" style="vertical-align: top;">
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
				    					<input id="did-tfn-extension" name="did_tfn_extension" type="number" class="did-tfn-extension form-control" required>
				    				</div>
				    			</div>
				    			<div class="form-group inbound blended hide">
				    				<label class="control-label col-lg-4">Call Route:</label>
				    				<div class="col-lg-8 mb">
				    					<select id="call-route" name="call_route" class="form-control">
				                            <option value="INGROUP">INGROUP (default)</option>
				                            <option value="IVR">IVR (callmenu)</option>
				                            <!--<option value="AGENT">AGENT</option>-->
				                            <!--<option value="VOICEMAIL">VOICEMAIL</option>-->
				                        </select>
				    				</div>
				    			</div>
								<div class="form-group call-route-mode inbound blended hide">
									<label class="call-route-div-label control-label col-lg-4">INGROUP:</label>
									<!--<div class="callroute-dummy-div col-lg-8 mb">-->
									<!--	<input name="call_route_text" type="text" class="form-control">-->
									<!--</div>-->
									<div class="ingroup-div col-lg-8 mb">
										<select id="ingroup-text" name="ingroup_text" class="form-control">
											<?php
												for($i=0;$i < count($ingroup->group_id);$i++){
													echo '<option value="'.$ingroup->group_id[$i].'">'.$ingroup->group_name[$i].'</option>';
												}
											?>
										</select>
									</div>
									<div class="ivr-div col-lg-8 mb hide">
										<select id="ivr-text" name="ivr_text" class="form-control">
											<?php
												for($i=0;$i < count($ivr->menu_id);$i++){
													echo '<option value="'.$ivr->menu_id[$i].'">'.$ivr->menu_name[$i].'</option>';
												}
											?>
										</select>
									</div>
									<div class="agent-div col-lg-8 mb hide">
										<select id="agent-text" name="agent_text" class="form-control">
											<?php
												for($i=0;$i < count($users->user_id);$i++){
													echo '<option value="'.$users->user_id[$i].'">'.$users->full_name[$i].'</option>';
												}
											?>
										</select>
									</div>
									<div class="voicemail-div col-lg-8 mb hide">
										<select id="voicemail-text" name="voicemail_text" class="form-control">
											<?php
												for($i=0;$i < count($voicemails->voicemail_id);$i++){
													echo '<option value="'.$voicemails->voicemail_id[$i].'">'.$voicemails->fullname[$i].'</option>';
												}
											?>
										</select>
									</div>
								</div>
				    			<div class="form-group group-color inbound blended hide">
				    				<label class="control-label col-lg-4">Group Color:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="group-color" name="group_color" type="text" class="form-control colorpicker" val="#ffffff">
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
								<div class="form-group carrier-to-use">
									<label class="control-label col-lg-4"><small>Carrier use for Campaign</small>:</label>
									<div class="col-lg-8 mb">
										<select name="dial_prefix" id="dial_prefix" class="form-control">
											<option value="CUSTOM" <?php if($campaign->data->dial_prefix == "CUSTOM"){echo "selected";}?>>CUSTOM DIAL PREFIX</option>
											<?php for($i=0;$i<=count($carriers->carrier_id);$i++) { ?>
												<?php if(!empty($carriers->carrier_id[$i])  && $carriers->active[$i] == 'Y') {
													$prefixes = explode("\n", $carriers->dialplan_entry[$i]);
													$prefix = explode(",", $prefixes[0]);
													$dial_prefix = substr(ltrim($prefix[0], "exten => _ "), 0, (strpos(".",$prefix[0]) - 1));
													$dial_prefix = str_replace("N", "", str_replace("X", "", $dial_prefix));
												?>
													<option value="<?php echo $dial_prefix; ?>" <?php if($campaign->data->dial_prefix == $carriers->carrier_id[$i]) echo "selected";?>><?php echo $carriers->carrier_name[$i]; ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group carrier-to-use custom-prefix">
									<label class="control-label col-lg-4 ">Custom Prefix:</label>
									<div class="col-lg-8 mb">
										<input type="number" class="form-control" id="custom_prefix" name="custom_prefix" value="<?php if(($campaign->data->dial_prefix == "CUSTOM") && ($campaign->data->dial_prefix == 0) || ($campaign->data->dial_prefix == '')){echo 9;}else{echo $campaign->data->dial_prefix;} ?>" minlength="1" maxlength="20">
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
				    					<!--<input id="copy-from-campaign" name="copy_from_campaign" type="text" class="form-control">-->
										<select id="copy-from-campaign" name="copy_from_campaign" class="form-control">
											<?php
												for($i=0;$i < count($campaign->campaign_id);$i++){
													echo '<option value="'.$campaign->campaign_id[$i].'">'.$campaign->campaign_id[$i].' - '.$campaign->campaign_name[$i].'</option>';
												}
											?>
										</select>
				    				</div>
				    			</div>
				    		</fieldset>

				    		<!-- STEP 2 -->
							<h4>Additional Information
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
				    			<div class="form-group dial-method-row">
				    				<label class="control-label col-lg-5">Dial Method:</label>
				    				<div class="col-lg-7 mb">
				    					<select class="form-control" id="dial-method" name="dial_method">
				    						<option value="MANUAL">MANUAL</option>
				    						<option value="RATIO">AUTODIAL</option>
				    						<option value="ADAPT_TAPERED">PREDICTIVE</option>
											<option value="INBOUND_MAN">INBOUND MAN</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group auto-dial-level hide">
				    				<label class="control-label col-lg-5">Auto Dial Level:</label>
				    				<div class="col-lg-7 mb">
				    					<select class="form-control" id="auto-dial-level" name="auto_dial_level">
				    						<option value="OFF">OFF</option>
											<option value="SLOW">SLOW</option>
											<option VALUE="NORMAL">NORMAL</option>
											<option VALUE="HIGH">HIGH</option>
											<option VALUE="MAX">MAX</option>
											<option VALUE="MAX_PREDICTIVE">MAX_PREDICTIVE</option>
											<option value="ADVANCE">ADVANCE</option>
				    					</select>
				    				</div>
				    			</div>
								<div class="form-group auto-dial-level-adv hide">
				    				<label class="control-label col-lg-5"></label>
				    				<div class="col-lg-7 mb">
				    					<select id="auto_dial_level_adv" class="form-control" name="auto_dial_level_adv">
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
											<input type="file" class="hide uploaded_wav" name="uploaded_wav" accept="audio/*">
											<input type="text" class="form-control wav-text-label" placeholder="WAV upload">
											<span class="input-group-btn">
												<button class="btn btn-default btn-browse-wav" type="button">Browse</button>
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
	    <div class="modal fade" id="add_disposition" aria-labelledby="add_disposition" >
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
	                	<input type="hidden" name="userid" id="userid" value="<?php echo $user->getUserId();?>"/>
	                    <div class="row">
	                    	<h4>Create Disposition
	                           <br>
	                           <small>Assign a status in a campaign then fill up the information below </small>
	                        </h4>
	                        <fieldset>
		                    	<div class="form-group mt">
		                            <label class="col-sm-3 control-label" for="disposition_campaign">Campaign: </label>
		                            <div class="col-sm-9 mb">
		                                <select id="disposition_campaign" name="disposition_campaign" class="form-control select2-1" style="width:100%;">
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
		                                <input type="text" name="status" id="status" class="form-control" placeholder="Status (Mandatory)" minlength="1" maxlength="6" required>
		                            	<label id="status-duplicate-error"></label>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="status_name">Status Name </label>
		                            <div class="col-sm-9 mb">
		                                <input type="text" name="status_name" id="status_name" class="form-control" placeholder="Status Name (Mandatory)" maxlength="30" required>
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

		<div id="modal_view_pause_codes" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Pause Codes</b></h4>
		      </div>
		      <div class="modal-body">
						<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
							<div class="table-responsive">
								<table id="pause_codes_list" class="table table-bordered" style="width: 100%;">
	                <thead>
	                    <tr>
	                        <th>Pause Code</th>
	                        <th>Pause Code Name</th>
	                        <th>Billable</th>
	                        <th>Action</th>
	                    </tr>
	                </thead>
									<tbody id="pause_code_data_container">
										<!-- Data Here -->
									</tbody>
								</table>
							</div>
						</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-success btn-new-pause-code<?=($perm->pausecodes->pausecodes_create === 'N' ? ' hidden' : '')?>" data-campaign="">Create New</button>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>

		<div id="modal_form_pause_codes" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Pause Codes</b></h4>
		      </div>
		      <div class="modal-body">
						<form id="form_pause_codes" class="form-horizontal" style="margin-top: 10px;">
							<div class="form-group">
								<label class="control-label col-lg-3">Campaign ID:</label>
								<div class="col-lg-9">
									<input type="text" class="form-control campaign-id" name="campaign_id" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Pause Code:</label>
								<div class="col-lg-9">
									<input type="text" class="form-control pause-code" name="pause_code">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Pause Code Name:</label>
								<div class="col-lg-9">
									<input type="text" class="form-control pause-code-name" name="pause_code_name">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">Billable:</label>
								<div class="col-lg-9">
									<select class="form-control billable" name="billable">
										<option value="YES">YES</option>
										<option value="NO">NO</option>
										<option value="HALF">HALF</option>
									</select>
								</div>
							</div>
						</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary btn-save-pause-code">Save</button>
						<button type="button" class="btn btn-success btn-update-pause-code hide">Update</button>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>

		<div id="modal_view_hotkeys" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Hotkeys</b></h4>
		      </div>
		      <div class="modal-body">
						<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
							<div class="table-responsive">
								<table id="hotkeys_list" class="table table-bordered" style="width: 100%;">
	                <thead>
	                    <tr>
	                        <th>Hotkey</th>
	                        <th>Status</th>
	                        <th>Description</th>
	                        <th>Action</th>
	                    </tr>
	                </thead>
									<tbody id="hotkey_data_container">
										<!-- Data Here -->
									</tbody>
								</table>
							</div>
						</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-success btn-new-hotkey<?=($perm->hotkeys->hotkeys_create === 'N' ? ' hidden' : '')?>" data-campaign="">Create New</button>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>

		<div id="modal_form_hotkeys" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Hotkeys</b></h4>
		      </div>
		      <div class="modal-body">
						<form id="form_hotkeys" class="form-horizontal" style="margin-top: 10px;">
							<div class="form-group">
								<label class="control-label col-lg-3" style="text-align: left;">Campaign ID:</label>
								<div class="col-lg-9">
									<input type="text" class="form-control campaign-id" name="campaign_id" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3" style="text-align: left;">Hotkey:</label>
								<div class="col-lg-9">
									<select class="form-control select2 hotkey" name="hotkey">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3" style="text-align: left;">Status:</label>
								<div class="col-lg-9">
									<input type="hidden" id="hotkey_status_name" name="status_name" value="">
									<select class="form-control select2 status" name="status">
										<option>Select a Status</option>
									</select>
								</div>
							</div>
						</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary btn-save-hotkey">Save</button>
						<button type="button" class="btn btn-success btn-update-hotkey hide">Update</button>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>
		
		<div id="modal_view_lists" class="modal fade" role="dialog">
		  <div class="modal-dialog" style="width: 70%;">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Lists</b></h4>
		      </div>
		      <div class="modal-body">
		      		<div class="form-group">
						<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
							<div class="table-responsive">
								<table id="lists_list" class="table table-bordered" style="width: 100%;">
									<thead>
										<tr>
											<th>List ID</th>
											<th>List Name</th>
											<th>Description</th>
											<th>Leads Count</th>
											<th>Active</th>
											<th>Last Call Date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody id="lists_data_container">
										<!-- Data Here -->
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="form-group">
						<p>
							This Campaign has <b><span class="count_active"></span> active</b> lists and <b><span class="count_inactive"></span> inactive</b> lists
						</p>
						<p>
							This Campaign has <b><span class="count_leads"></span> leads</b> in the queue (hopper dial)
						</p>
						<p>
							<a href="#" style="color: green;" class="view-leads-on-hopper" data-campaign="">
								View Leads in the hopper for this campaign
							</a>
						</p>
					</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<!--<button type="button" class="btn btn-success btn-new-lists" data-campaign="">Create New</button>-->
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>
		
		<div id="modal_form_lists" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Lists</b> <small>(Form)</small></h4>
		      </div>
		      <div class="modal-body">
				<form id="form_lists" class="form-horizontal" style="margin-top: 10px;">
					<input type="hidden" name="lists_id" class="lists-id">
					<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
								<li><a href="#tab_2" data-toggle="tab"> Statuses</a></li>
								<li><a href="#tab_3" data-toggle="tab"> Timezones</a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">
			               		<div id="tab_1" class="tab-pane fade in active">
			               			<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Name:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-name" name="lists_name">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Description:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-description" name="lists_description">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Campaign:</label>
										<div class="col-lg-8">
											<select name="lists_campaign" class="form-control select2 lists-campaign">
												<?php
													for($i=0;$i < count($campaign->campaign_id);$i++){
														echo '<option value="'.$campaign->campaign_id[$i].'">'.$campaign->campaign_id[$i].' - '.$campaign->campaign_name[$i].'</option>';
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Reset Time:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-reset-time" name="lists_reset_time">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Reset Lead Called Status:</label>
										<div class="col-lg-3">
											<select name="lists_lead_called_status" class="form-control select2 lists-lead-called-status">
												<option value="N">N</option>
												<option value="Y">Y</option>
											</select>
										</div>
										<label class="control-label col-lg-2" style="text-align: left;">Active:</label>
										<div class="col-lg-3">
											<select name="lists_active" class="form-control select2 lists-active">
												<option value="N">N</option>
												<option value="Y">Y</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Agent Script Override:</label>
										<div class="col-lg-8">
											<select name="lists_agent_script_override" class="form-control lists-agent-script-override">
												<option value="" selected="selected">NONE - INACTIVE</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Campaign CID Override:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-cid-override" name="lists_cid_override">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"></label>
										<div class="col-lg-3">
											<select name="lists_drop_inbound_group_override" class="form-control lists-drop-inbound-group-override">
												<option value="NONE">NONE</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Web Form:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-web-form" name="lists_web_form">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">Transfer Conf No. Override:</label>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-a-number" name="xferconf_a_number">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-b-number" name="xferconf_b_number">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">&nbsp;</label>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-c-number" name="xferconf_c_number">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-d-number" name="xferconf_d_number">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">&nbsp;</label>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-e-number" name="xferconf_e_number">
										</div>
									</div>
			               		</div>
			               		<div id="tab_2" class="tab-pane">
			               			<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
										<div class="table-responsive">
											<table id="lists_statuses" class="table table-bordered" style="width: 100%;">
												<thead>
													<tr>
														<th>Status</th>
														<th>Description</th>
														<th>Called</th>
														<th>Not Called</th>
													</tr>
												</thead>
												<tbody id="lists_statuses_container">

												</tbody>
											</table>
										</div>
									</div>
			               		</div>
			               		<div id="tab_3" class="tab-pane">
			               			<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
										<div class="table-responsive">
											<table id="lists_statuses" class="table table-bordered" style="width: 100%;">
												<thead>
													<tr>
														<th>GMT OFF SET NOW (local time)</th>
														<th>Called</th>
														<th>Not Called</th>
													</tr>
												</thead>
												<tbody id="lists_timezone_container">

												</tbody>
											</table>
										</div>
									</div>
			               		</div>
			               </div>
			        </div>
					
				</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success btn-update-lists" data-campaign="">Update</button>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>

		<div id="modal_view_leads_on_hopper" class="modal fade" role="dialog">
		  <div class="modal-dialog" style="width: 70%;">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b>Lists</b></h4>
		      </div>
		      <div class="modal-body">
					<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
						<div class="table-responsive">
							<table id="leads_on_hopper" class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th>Order</th>
										<th>Priority</th>
										<th>Lead ID</th>
										<th>List ID</th>
										<th>Phone Number</th>
										<th>State</th>
										<th>Status</th>
										<th>Count</th>
										<th>GMT</th>
										<th>ALT</th>
										<th>Source</th>
									</tr>
								</thead>
								<tbody id="leads_hopper_container">
									<!-- Data Here -->
								</tbody>
							</table>
						</div>
					</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<!--<button type="button" class="btn btn-success btn-new-lists" data-campaign="">Create New</button>-->
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>


	<!-- End of modal -->

	<?php print $ui->standardizedThemeJS(); ?>
	<!-- JQUERY STEPS-->
  	<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>

    <!-- iCheck 1.0.1 -->
	<script src="js/plugins/iCheck/icheck.min.js"></script>

	<script type="text/javascript">
		function FilterInput(event) {
			var keyCode = ('which' in event) ? event.which : event.keyCode;
		
			isNotWanted = (keyCode == 69 || keyCode == 101);
			return !isNotWanted;
		}

		function get_pause_codes(campaign_id){
			$.ajax({
				url: "./php/GetPauseCodes.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(response) {
						// var values = JSON.parse(response.result);
						// console.log(response);
						$('.btn-new-pause-code').attr('data-campaign', campaign_id);
						$('#modal_view_pause_codes').modal('show');
						var table = $('#pause_codes_list').DataTable();
						table.fnClearTable();
						table.fnDestroy();
						$('#pause_code_data_container').html(response);
						$('#pause_codes_list').DataTable({
							"searching": true,
							bFilter: true,
							"aoColumnDefs": [{
								"bSearchable": false,
								"aTargets": [ 3 ]
							},{
								"bSortable": false,
								"aTargets": [ 3 ]
							}]
						});
						$("#pause_codes_list").css("width","100%");
					}
			});
		}

		function get_hotkeys(campaign_id){
			$.ajax({
				url: "./php/GetHotkeys.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(response) {
						// var values = JSON.parse(response.result);
						// console.log(response);
						$('.btn-new-hotkey').attr('data-campaign', campaign_id);
						$('#modal_view_hotkeys').modal('show');
						var table = $('#hotkeys_list').DataTable();
						table.fnClearTable();
						table.fnDestroy();
						$('#hotkey_data_container').html(response);
						$('#hotkeys_list').DataTable({
							"searching": true,
							bFilter: true,
							"aoColumnDefs": [{
								"bSearchable": false,
								"aTargets": [ 3 ]
							},{
								"bSortable": false,
								"aTargets": [ 3 ]
							}]
						});
						$("#hotkeys_list").css("width","100%");
					}
			});
		}
		
		function get_lists(campaign_id){
			$.ajax({
				url: "./php/GetLists.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(response) {
						// var values = JSON.parse(response.result);
						// console.log(response);
						// $('.btn-new-lists').attr('data-campaign', campaign_id);
						$('#modal_view_lists').modal('show');
						var table = $('#lists_list').DataTable();
						table.fnClearTable();
						table.fnDestroy();
						$('#lists_data_container').html(response.data);
						$('.count_active').text(response.count_active);
						$('.count_inactive').text(response.count_inactive);
						
						$('.view-leads-on-hopper').attr('data-campaign', campaign_id);
						$('#lists_list').DataTable({
							"searching": true,
							bFilter: true
						});
						$("#lists_list").css("width","100%");
					}
			});

			$.ajax({
				url: "./php/GetLeadsOnHopper.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(response) {
						//console.log(response);
						$('.count_leads').text(response.count);
					}
			});
		}

		function get_leads_on_hopper(campaign_id){
			$.ajax({
				url: "./php/GetLeadsOnHopper.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(response) {
						// console.log(response);
						$('#modal_view_lists').modal('hide');
						$('#modal_view_leads_on_hopper').modal('show');
						$('body').addClass('modal-open');
						var table = $('#leads_on_hopper').DataTable();
						table.fnClearTable();
						table.fnDestroy();
						$('#leads_hopper_container').html(response.data);
						$('#leads_on_hopper').DataTable({
							"searching": true,
							bFilter: true
						});
						$("#leads_on_hopper").css("width","100%");
					}
			});
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

		$(document).ready(function(){
			//$('#modal_form_lists').modal('show');
				$('.select2').select2({
					theme: 'bootstrap'
				});
			
			// FAB HOVER
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});
		
			var dial_prefix = $('#dial_prefix').val();
			dialPrefix(dial_prefix);

			$('#dial_prefix').change(function(){
				dialPrefix($(this).val());
			});
			$(".colorpicker").colorpicker();
			$('#add_campaign').on('shown.bs.modal', function () {
				$(".colorpicker").colorpicker();
				$('#did-tfn-extension').autocomplete({
					//source: "php/searchDID.php",
					source: function(request,response) {
						$.ajax({
							url: "./php/searchDID.php",
							type: 'POST',
							data: {
								term : request.term
							},
							dataType: 'json',
							success: function(responsedata) {
								//console.log(responsedata);
								if (responsedata){ 
                                    response($.parseJSON(responsedata));
									$('.call-route-mode').removeClass('hide');
									$('.group-color').removeClass('hide');
                                }else{
									response('');
									$('.call-route-mode').addClass('hide');
									$('.group-color').addClass('hide');
								}
								
							}
						});
					},
					minLength: 2,
					select: function( event, ui ) {
						var did = ui.item.value;
						$.ajax({
							url: "./php/getDIDSettings.php",
							type: 'POST',
							data: {
								did : did
							},
							dataType: 'json',
							success: function(response) {
								//console.log(response);
								$('#did-tfn-extension').val(did);
								if (response.did_route == "IN_GROUP") {
									$('#call-route').val("INGROUP").trigger('change');
									$('#ingroup-text').val(response.group_id).trigger('change');
									$('.group-color').removeClass('hide');
                                }else if (response.did_route == "CALLMENU") {
									$('#call-route').val("IVR").trigger('change');
									$('#ivr-text').val(response.menu_id).trigger('change');
									$('.group-color').addClass('hide');
                                }else if (response.did_route == "AGENT") {
									$('#call-route').val("AGENT").trigger('change');
                                    $('#agent-text').val(response.user).trigger('change');
                                }else if (response.did_route == "VOICEMAIL") {
									$('#call-route').val("VOICEMAIL").trigger('change');
                                    $('#voicemail-text').val(response.voicemail_ext).trigger('change');
                                }
								$('#group-color').val(response.group_color);
							}
						});
						return false;
					}
				});
				$( "#did-tfn-extension" ).autocomplete( "option", "appendTo", "#campaign_form" );
			});
			//#did-tfn-extension
			//$(document).on('keypress', '#did-tfn-extension', function(){
			//	console.log('test');
			//});
			
			$(document).on('change', '#call-route', function(){
				var callroute = $(this).val();
				
				if (callroute == "INGROUP") {
                    $('.call-route-div-label').html("INGROUP:");
					$('.ingroup-div').removeClass('hide');
					$('.ivr-div').addClass('hide');
					$('.agent-div').addClass('hide');
					$('.voicemail-div').addClass('hide');
					$('.callroute-dummy-div').addClass('hide');
					$('.group-color').removeClass('hide');
					$.ajax({
						url: "./php/searchDID.php",
						type: 'POST',
						data: {
							term : $('#did-tfn-extension').val()
						},
						dataType: 'json',
						success: function(responsedata) {
							//console.log(responsedata);
							if (responsedata){ 
								$('.call-route-mode').removeClass('hide');
								$('.group-color').removeClass('hide');
							}else{
								$('.call-route-mode').addClass('hide');
								$('.group-color').addClass('hide');
							}
							
						}
					});
                }else if (callroute == "IVR") {
                    $('.call-route-div-label').html("IVR:");
					$('.ivr-div').removeClass('hide');
					$('.ingroup-div').addClass('hide');
					$('.agent-div').addClass('hide');
					$('.voicemail-div').addClass('hide');
					$('.callroute-dummy-div').addClass('hide');
					$('.group-color').addClass('hide');
					$.ajax({
						url: "./php/searchDID.php",
						type: 'POST',
						data: {
							term : $('#did-tfn-extension').val()
						},
						dataType: 'json',
						success: function(responsedata) {
							//console.log(responsedata);
							if (responsedata){ 
								$('.call-route-mode').removeClass('hide');
								$('.group-color').addClass('hide');
							}else{
								$('.call-route-mode').addClass('hide');
								$('.group-color').addClass('hide');
							}
							
						}
					});
                }else if (callroute == "AGENT") {
                    $('.call-route-div-label').html("AGENT:");
					$('.agent-div').removeClass('hide');
					$('.ingroup-div').addClass('hide');
					$('.ivr-div').addClass('hide');
					$('.voicemail-div').addClass('hide');
					$('.callroute-dummy-div').addClass('hide');
                }else if (callroute == "VOICEMAIL") {
                    $('.call-route-div-label').html("VOICEMAIL:");
					$('.voicemail-div').removeClass('hide');
					$('.ingroup-div').addClass('hide');
					$('.ivr-div').addClass('hide');
					$('.agent-div').addClass('hide');
					$('.callroute-dummy-div').addClass('hide');
                }else{
					$('.call-route-div-label').html("Select Call Route");
					$('.ingroup-div').addClass('hide');
					$('.ivr-div').addClass('hide');
					$('.agent-div').addClass('hide');
					$('.voicemail-div').addClass('hide');
					$('.callroute-dummy-div').removeClass('hide');
				}
			});
			
			$(document).on('click', '.btn-browse-wav', function(){
				$('.uploaded_wav').click();
			});
			
			$(document).on('change', '.uploaded_wav', function(){
				var myFile = $(this).prop('files');
				var Filename = myFile[0].name;
				
				$('.wav-text-label').val(Filename.slice(0, -4));
				//console.log(myFile);
			});
			
			$(document).on('click','.edit-list',function() {
				var dataInfo = $(this).data('info');
				//console.log(dataInfo);
				$('.lists-id').val(dataInfo.list_id);
				$('.lists-name').val(dataInfo.list_name);
				$('.lists-description').val(dataInfo.list_description);
				$('.lists-campaign').val(dataInfo.campaign_id).trigger('change');
				$('.lists-reset-time').val(dataInfo.reset_time);
				$('.lists-lead-called-status').val(dataInfo.reset_called_lead_status).trigger('change');
				$('.lists-active').val(dataInfo.active).trigger('change');
				$('.lists-agent-script-override').val(dataInfo.agent_script_override).trigger('change');
				$('.lists-cid-override').val(dataInfo.campaign_cid_override);
				$('.lists-drop-inbound-group-override').val(dataInfo.drop_inbound_group_override).trigger('change');
				$('.lists-web-form').val(dataInfo.web_from_address);
				$('.lists-xferconf-a-number').val(dataInfo.xferconf_a_number);
				$('.lists-xferconf-b-number').val(dataInfo.xferconf_b_number);
				$('.lists-xferconf-c-number').val(dataInfo.xferconf_c_number);
				$('.lists-xferconf-d-number').val(dataInfo.xferconf_d_number);
				$('.lists-xferconf-e-number').val(dataInfo.xferconf_e_number);

				$.ajax({
					url: "./php/GetListsStatuses.php",
					type: 'POST',
					data: {
						list_id : dataInfo.list_id,
					},
					dataType: 'json',
					success: function(response) {
							//console.log(response);
							$('#lists_statuses_container').html(response);
						}
				});

				$.ajax({
					url: "./php/GetListsTimezones.php",
					type: 'POST',
					data: {
						list_id : dataInfo.list_id,
					},
					dataType: 'json',
					success: function(response) {
							//console.log(response);
							$('#lists_timezone_container').html(response);
						}
				});

				$('.btn-update-lists').attr('data-campaign', dataInfo.campaign_id);
				$('#modal_view_lists').modal('hide');
				$('#modal_form_lists').modal('show');
				$('body').addClass('modal-open');
			});

			$(document).on('click', '.view-pause-codes', function(){
				var campaign_id = $(this).data('id');
				// alert(campaign_id);
				get_pause_codes(campaign_id);
			});

			$(document).on('click', '.view-hotkeys', function(){
				var campaign_id = $(this).data('id');
				// alert(campaign_id);
				get_hotkeys(campaign_id);
			});
			
			$(document).on('click', '.view-lists', function(){
				var campaign_id = $(this).data('id');
				// alert(campaign_id);
				get_lists(campaign_id);
			});
			
			$(document).on('click', '.view-leads-on-hopper', function(){
				var campaign_id = $(this).data('campaign');
				// alert(campaign_id);
				get_leads_on_hopper(campaign_id);
				$('body').addClass('modal-open');
			});

			$('#modal_view_leads_on_hopper').on('shown.bs.modal', function (e) {
			  $('body').addClass('modal-open');
			});

			$(document).on('click', '.btn-new-pause-code', function(){
				var campaign_id = $(this).data('campaign');
				$('.campaign-id').val(campaign_id);

				$('.pause-code').val('');
				$('.pause-code').removeAttr("readonly");
				$('.pause-code-name').val('');
				$('.billable').val('YES').trigger('change');
				$('.btn-save-pause-code').removeClass('hide');
				$('.btn-update-pause-code').addClass('hide');
				$('#modal_view_pause_codes').modal('hide');
				$('#modal_form_pause_codes').modal('show');
				$('body').addClass('modal-open');
			});

			$(document).on('click', '.btn-new-hotkey', function(){
				var campaign_id = $(this).data('campaign');
				$('.campaign-id').val(campaign_id);

				$.ajax({
					url: "./php/GetDialStatuses.php",
					type: 'POST',
					data: {
						campaign_id : campaign_id,
						hotkeys_only: 1
					},
					dataType: 'json',
					success: function(response) {
							// console.log(response);
							$('.status').html(response);
							$('.status').select2({
								theme: 'bootstrap'
							});
							$('.status').val("").trigger("change");
						}
				});

				$('.hotkey').val('1').trigger('change');
				$('.btn-save-hotkey').removeClass('hide');
				$('.btn-update-hotkey').addClass('hide');
				$('#modal_view_hotkeys').modal('hide');
				$('#modal_form_hotkeys').modal('show');
				$('body').addClass('modal-open');
			});

			$('#modal_form_pause_codes').on('hidden.bs.modal', function () {
				// var campaign_id = $('.camapaign-id').val();
				$('#modal_form_pause_codes').modal('hide');
				$('#modal_view_pause_codes').modal('show');
				$('body').addClass('modal-open');
			});

			$('#modal_form_hotkeys').on('hidden.bs.modal', function () {
				// var campaign_id = $('.camapaign-id').val();
				$('#modal_form_hotkeys').modal('hide');
				$('#modal_view_hotkeys').modal('show');
				$('body').addClass('modal-open');
			});

			$('#modal_view_pause_codes').on('hidden.bs.modal', function () {
				if($('#modal_form_pause_codes').hasClass('in')){
					$('body').addClass('modal-open');
				}
			});

			$('#modal_view_hotkeys').on('hidden.bs.modal', function () {
				if($('#modal_form_hotkeys').hasClass('in')){
					$('body').addClass('modal-open');
				}
			});
		
			$('#modal_view_lists').on('hidden.bs.modal', function () {
				if($('#modal_form_lists').hasClass('in')){
					$('body').addClass('modal-open');
				}
			});
			
			$('#modal_form_lists').on('hidden.bs.modal', function () {
				$('#modal_view_lists').modal('show');
				$('body').addClass('modal-open');
			});
			

			$('#modal_view_leads_on_hopper').on('hidden.bs.modal', function () {
				$('#modal_view_lists').modal('show');
				$('body').addClass('modal-open');
			});

			$(document).on('click', '.btn-edit-pc', function(){
				var campaign_id = $(this).data('camp-id');
				var code = $(this).data('code');
				var name = $(this).data('name');
				var billable = $(this).data('billable');

				$('.campaign-id').val(campaign_id);
				$('.pause-code').val(code);
				$('.pause-code').attr('readonly', true);
				$('.pause-code-name').val(name);
				$('.billable').val(billable).trigger('change');
				$('.btn-save-pause-code').addClass('hide');
				$('.btn-update-pause-code').removeClass('hide');
				$('#modal_view_pause_codes').modal('hide');
				$('#modal_form_pause_codes').modal('show');
				$('body').addClass('modal-open');
			});

			$(document).on('click', '.btn-delete-pc', function(){
				var campaign_id = $(this).data('camp-id');
				var pause_code = $(this).data('code');
				swal({
					title: "Are you sure?",
					text: "This action cannot be undone.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, delete pause code!",
					cancelButtonText: "No, cancel please!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
											url: "./php/DeletePauseCode.php",
											type: 'POST',
											data: {
												campaign_id:campaign_id,
												pause_code:pause_code
											},
											// dataType: 'json',
											success: function(data) {
													// console.log(data);
													if(data == "success"){
														swal({
																title: "Success",
																text: "Pause Code Successfully Deleted",
																type: "success"
															},
															function(){
																get_pause_codes(campaign_id);
															}
														);
													}else{
															sweetAlert("Oops...", "Something went wrong! "+ data, "error");
													}
											}
								});
							} else {
									swal("Cancelled", "No action has been done :)", "error");
							}
					}
				);
			});

			$(document).on('click', '.btn-delete-hk', function(){
				var campaign_id = $(this).data('camp-id');
				var hotkey = $(this).data('hotkey');
				swal({
					title: "Are you sure?",
					text: "This action cannot be undone.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, delete hotkey!",
					cancelButtonText: "No, cancel please!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
											url: "./php/DeleteHotkey.php",
											type: 'POST',
											data: {
												campaign_id:campaign_id,
												hotkey:hotkey
											},
											// dataType: 'json',
											success: function(data) {
													// console.log(data);
													if(data == "success"){
														swal({
																title: "Success",
																text: "Hotkey Successfully Deleted",
																type: "success"
															},
															function(){
																get_hotkeys(campaign_id);
															}
														);
													}else{
															sweetAlert("Oops...", "Something went wrong! "+ data, "error");
													}
											}
								});
							} else {
									swal("Cancelled", "No action has been done :)", "error");
							}
					}
				);
			});

			$(document).on('click', '.btn-save-pause-code', function(){
				var form_data = new FormData($("#form_pause_codes")[0]);
				var campaign_id = $('.campaign-id').val();
				swal({
					title: "Are you sure?",
					text: "This action cannot be undone.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, create pause code!",
					cancelButtonText: "No, cancel please!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
											url: "./php/AddPauseCode.php",
											type: 'POST',
											data: form_data,
											// dataType: 'json',
											cache: false,
											contentType: false,
											processData: false,
											success: function(data) {
													// console.log(data);
													if(data == "success"){
														swal({
																title: "Success",
																text: "Pause Code Successfully Created",
																type: "success"
															},
															function(){
																$('.pause-code').val('');
																$('.pause-code-name').val('');
																$('.billable').val('YES').trigger('change');
																$('#modal_form_pause_codes').modal('hide');
																get_pause_codes(campaign_id);
															}
														);
													}else{
															sweetAlert("Oops...", "Something went wrong! "+ data, "error");
													}
											}
								});
							} else {
									swal("Cancelled", "No action has been done :)", "error");
							}
					}
				);
			});
			
			
			$(document).on('click', '.btn-update-lists', function(){
				var campaign_id = $(this).data('campaign');
				swal({
					title: "Are you sure?",
					text: "This action cannot be undone.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, modify list!",
					cancelButtonText: "No, cancel please!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
								$.ajax({
									url: "./php/ModifyTelephonyList.php",
									type: 'POST',
									data: {
										'modifyid': $('.lists-id').val(),
										'name': $('.lists-name').val(),
										'desc': $('.lists-description').val(),
										'campaign': $('.lists-campaign').val(),
										'active': $('.lists-active').val(),
										'reset_list': $('.lists-lead-called-status').val(),
										'reset_time' : $('.lists-reset-time').val(),
										'agent_script_override' : $('.lists-agent-script-override').val(),
										'campaign_cid_override' : $('.lists-cid-override').val(),
										'drop_inbound_group_override' : $('.lists-drop-inbound-group-override').val(),
										'web_form' : $('.lists-web-form').val(),
										'xferconf_a_number' : $('.lists-xferconf-a-number').val(),
										'xferconf_b_number' : $('.lists-xferconf-b-number').val(),
										'xferconf_c_number' : $('.lists-xferconf-c-number').val(),
										'xferconf_d_number' : $('.lists-xferconf-d-number').val(),
										'xferconf_e_number' : $('.lists-xferconf-e-number').val()
									},
									// dataType: 'json',
									success: function(data) {
											console.log(data);
											if(data == "success"){
												swal({
														title: "Success",
														text: "List Successfully Modified",
														type: "success"
													},
													function(){
														$('#modal_form_lists').modal('hide');
														get_lists(campaign_id);
													}
												);
											}else{
													sweetAlert("Oops...", "Something went wrong! "+ data, "error");
											}
									}
								});
							} else {
									swal("Cancelled", "No action has been done :)", "error");
							}
					}
				);
			});

			$(document).on('change', '.status', function(){
				var stat_name = $(this).select2({theme: 'bootstrap'}).find(":selected").data("name");
				// console.log(stat_name);
				$('#hotkey_status_name').val(stat_name);
			});

			$(document).on('click', '.btn-save-hotkey', function(){
				var form_data = new FormData($("#form_hotkeys")[0]);
				var campaign_id = $('.campaign-id').val();
				swal({
					title: "Are you sure?",
					text: "This action cannot be undone.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, create hotkey!",
					cancelButtonText: "No, cancel please!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
											url: "./php/AddHotkey.php",
											type: 'POST',
											data: form_data,
											// dataTbtn-delete-hkype: 'json',
											cache: false,
											contentType: false,
											processData: false,
											success: function(data) {
													// console.log(data);
													if(data == "success"){
														swal({
																title: "Success",
																text: "Hotkey Successfully Created",
																type: "success"
															},
															function(){
																$('.hotkey').val('1').trigger('change');
																$('#modal_form_hotkeys').modal('hide');
																get_hotkeys(campaign_id);
															}
														);
													}else{
														sweetAlert("Oops...", "Something went wrong! "+ data, "error");	
													}
											}
								});
							} else {
									swal("Cancelled", "No action has been done :)", "error");
							}
					}
				);
			});

			$(document).on('click', '.btn-update-pause-code', function(){
				var form_data = new FormData($("#form_pause_codes")[0]);
				var campaign_id = $('.campaign-id').val();
				swal({
					title: "Are you sure?",
					text: "This action cannot be undone.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, update pause code!",
					cancelButtonText: "No, cancel please!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
											url: "./php/ModifyPauseCode.php",
											type: 'POST',
											data: form_data,
											// dataType: 'json',
											cache: false,
											contentType: false,
											processData: false,
											success: function(data) {
													// console.log(data);
													if(data == "success"){
														swal({
																title: "Success",
																text: "Pause Code Successfully Updated",
																type: "success"
															},
															function(){
																$('.pause-code').val('');
																$('.pause-code-name').val('');
																$('.billable').val('YES').trigger('change');
																$('#modal_form_pause_codes').modal('hide');
																get_pause_codes(campaign_id);
															}
														);
													}else{
															sweetAlert("Oops...", "Something went wrong! "+ data, "error");
													}
											}
								});
							} else {
									swal("Cancelled", "No action has been done :)", "error");
							}
					}
				);
			});
			/******
			** Initializations
			******/

				//initialization of the datatables
					$('#table_campaign').dataTable({
						"aaSorting": [[ 1, "asc" ]],
						"aoColumnDefs": [{
							"bSearchable": false,
							"aTargets": [ 0, 5 ]
						},{
							"bSortable": false,
							"aTargets": [ 0, 5 ]
						}]
					});
					$('#table_disposition').dataTable({
						columnDefs: [
						    { width: "16%", targets: "action_disposition" }
						],
						"aaSorting": [[ 1, "asc" ]],
						"aoColumnDefs": [{
							"bSearchable": false,
							"aTargets": [ 0, 4 ]
						},{
							"bSortable": false,
							"aTargets": [ 0, 4 ]
						}]
					});
					$('#table_leadfilter').dataTable();

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
						var log_user = '<?=$_SESSION['user']?>';
						var log_group = '<?=$_SESSION['usergroup']?>';
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
												log_user: log_user,
												log_group: log_group
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
						var log_user = '<?=$_SESSION['user']?>';
						var log_group = '<?=$_SESSION['usergroup']?>';

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
		                    		userid : $('#userid').val(),
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
		                              sweetAlert("Oops...", "Something went wrong! "+data, "error");
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
							}elseif($_GET['message'] == "Error"){
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
								$('#dial-method').val("RATIO").trigger('change');
								dialMethod("RATIO");
							}else if(selectedTypeVal == 'survey'){
								$('.outbound').addClass('hide');
								$('.blended').addClass('hide');
								$('.inbounce').addClass('hide');
								$('.copy-from').addClass('hide');
								$('.survey').removeClass('hide');
								$('.carrier-to-use').removeClass('hide');
								$('.dial-method-row').addClass('hide');
								$('.auto-dial-level').addClass('hide');
							}else if(selectedTypeVal == 'copy'){
								$('.outbound').addClass('hide');
								$('.blended').addClass('hide');
								$('.survey').addClass('hide');
								$('.inbound').addClass('hide');
								$('.copy-from').removeClass('hide');
								$('.carrier-to-use').addClass('hide');
								
								$('#copy-from-campaign').select2({
									theme: 'bootstrap'
								});
							}else if(selectedTypeVal == 'blended'){
								$('.outbound').addClass('hide');
								$('.inbound').addClass('hide');
								$('.survey').addClass('hide');
								$('.copy-from').addClass('hide');
								$('.blended').removeClass('hide');
								$('.carrier-to-use').removeClass('hide');
								$('#dial-method').val("RATIO").trigger('change');
								dialMethod("RATIO");
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
							duplicate_status_check();
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
					// disables pasting
						$('#campaign-id').bind("paste",function(e) {
						      e.preventDefault();
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
					// disables pasting
						$('#campaign-name').bind("paste",function(e) {
						      e.preventDefault();
						});
				/*** end campaign ***/

				/*** DISPOSITION ***/
					// disable special characters on status
						$('#status').bind('keypress', function (event) {
						    var regex = new RegExp("^[a-zA-Z0-9]+$");
						    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						    if (!regex.test(key)) {
						       event.preventDefault();
						       return false;
						    }
						});
					// disables pasting
						$('#status').bind("paste",function(e) {
						      e.preventDefault();
						});
					// disables special characters on status name
						$('#status_name').bind('keypress', function (event) {
						    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
						    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
						    if (!regex.test(key)) {
						       event.preventDefault();
						       return false;
						    }
						});
					// disables pasting
						$('#status_name').bind("paste",function(e) {
						      e.preventDefault();
						});
				/*** end of disposition filters ***/

			/* initialize select2 */
				$('.select2-1').select2({
			        theme: 'bootstrap'
			    });

				$('#auto-dial-level').change(function(){
					var val = $(this).val();
					if(val == 'ADVANCE') {
                        $('.auto-dial-level-adv').removeClass('hide');
                    }else{
						$('.auto-dial-level-adv').addClass('hide');
					}
				});
				
		}); // end of document ready


		function clear_form(){

		}
	
		function dialMethod(value){
			//console.log(value);
			if(value == "RATIO"){
				$('#auto-dial-level').prop('disabled', false);
				$('#auto-dial-level option[value=OFF]').prop('disabled', true);
				$('#auto-dial-level option[value=SLOW]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
			}else if(value == "ADAPT_TAPERED"){
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value=MAX_PREDICTIVE]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
			}else if(value == "INBOUND_MAN"){
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value=SLOW]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
			}else{
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value=OFF]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
				$('.auto-dial-level-adv').addClass('hide');
			}
			
		}
		//function dialMethod(value){
		//	if(value == "RATIO"){
		//		$('#auto-dial-level').prop('disabled', false);
		//		$('#auto-dial-level option[value="1.0"]').prop('selected', true);
		//		$('div.auto-dial-level').removeClass('hide');
		//	}else if(value == "ADAPT_TAPERED"){
		//		$('#auto-dial-level').prop('disabled', true);
		//		$('#auto-dial-level option[value="0"]').prop('selected', true);
		//		$('div.auto-dial-level').addClass('hide');
		//	}else{
		//		$('#auto-dial-level').prop('disabled', true);
		//		$('#auto-dial-level option[value="0"]').prop('selected', true);
		//		$('div.auto-dial-level').addClass('hide');
		//	}
		//}

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
