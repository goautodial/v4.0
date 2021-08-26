<?php
 /**
 * @file 		telephonycampaigns.php
 * @brief 		Manage Campaigns, Dispositions & etc.
 * @copyright 	Copyright (c) 2020 GOautodial Inc.
 * @author		Alexander Jim H. Abenoja
 * @author     	Noel Umandap
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
*/

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

	$perm = $api->goGetPermissions('campaign,disposition,pausecodes,hotkeys,list', $_SESSION['usergroup']);
	$gopackage = $api->API_getGOPackage();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("campaigns"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>

    	<!-- iCheck for checkboxes and radio inputs -->
  		<link rel="stylesheet" href="css/iCheck/all.css">

    	<!-- Wizard Form style -->
    	<link href="css/style.css" rel="stylesheet" type="text/css" />		    
		
		<!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">
		<!-- bootstrap color picker -->
		<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>
   		
		<style type="text/css">
			.select2-container{
				width: 100% !important;
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
			@media (min-width: 992px) {
				.modal-lg {
					width: 900px;
				}
			}
			@media (min-width: 768px) {
				.modal-xl {
					width: 90%;
				max-width:1600px;
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
                			<legend><?php $lh->translateText("campaigns"); ?></legend>
							<?php if ($perm->campaign->campaign_read !== 'N') { ?>
							<?php

								/*
								* API used for display in tables
								*/
								$campaign = $api->API_getAllCampaigns();
								if($campaign->result !== "success"){
									// die("API ERROR: ".$campaign->result);
								}
								$disposition = $api->API_getAllDispositions();
								$leadrecycling = $api->API_getAllLeadRecycling();
								$dialStatus = $api->API_getAllDialStatuses('ALL', 1);
								$ingroup = $api->API_getAllInGroups();
								$ivr = $api->API_getAllIVRs();
								$voicemails = $api->API_getAllVoiceFiles();
								$users = $api->API_getAllUsers();
								$carriers = $api->API_getAllCarriers();
								$checkbox_all = $ui->getCheckAll("campaign");
								//$areacode = $api->API_getAllAreacodes();

								//echo "<pre>";
								//var_dump($areacodes);
							?>
							 <div role="tabpanel">
								<ul role="tablist" class="nav nav-tabs nav-justified">

								 <!-- Campaign panel tabs-->
									 <li role="presentation" <?php if(!isset($_GET['T_disposition']) && !isset($_GET['T_recycling']) && !isset($_GET['T_areacode']) ) echo 'class="active"'; ?> >
										<a href="#T_campaign" aria-controls="T_campaign" role="tab" data-toggle="tab" class="bb0">
										   <?php $lh->translateText("campaigns"); ?> </a>
									 </li>
								<!-- Disposition panel tab -->
									 <li role="presentation" <?php if(isset($_GET['T_disposition']))echo 'class="active"'; ?> >
										<a href="#T_disposition" aria-controls="T_disposition" role="tab" data-toggle="tab" class="bb0">
										   <?php $lh->translateText("disposition"); ?> </a>
									 </li>
								<!-- Lead Recycling panel tab -->
									 <li role="presentation" <?php if(isset($_GET['T_recycling']))echo 'class="active"'; ?>  >
										<a href="#T_recycling" aria-controls="T_recycling" role="tab" data-toggle="tab" class="bb0">
										   <?php $lh->translateText("Lead Recycling"); ?> </a>
									 </li>
								<!-- LeadFilter panel tab
									 <li role="presentation">
										<a href="#T_leadfilter" aria-controls="T_leadfilter" role="tab" data-toggle="tab" class="bb0">
										   Lead Filters </a>
									 </li>
								-->
								<!-- AC-CID panel tab -->
									<li role="presentation" <?php if(isset($_GET['T_areacode']))echo 'class="active"'; ?>  >
										<a href="#T_areacode" aria-controls="T_areacode" role="tab" data-toggle="tab" class="bb0">
										   <?php $lh->translateText("Areacode CID"); ?> </a>
									</li>
								  </ul>

								<!-- Tab panes-->
								<div class="tab-content bg-white">
									
								<!--==== Campaigns ====-->							
								  <div id="T_campaign" role="tabpanel" class="tab-pane <?php if(!isset($_GET['T_disposition']) && !isset($_GET['T_recycling']) && !isset($_GET['T_areacode'])) echo 'active'; ?> ">
										<table class="display responsive no-wrap table-bordered table-striped" width="100%" id="table_campaign">
										   <thead>
											  <tr>
												 <th></th>
												 <th><?php $lh->translateText("campaign_id"); ?></th>
												 <th><?php $lh->translateText("campaign_name"); ?></th>
												 <th><?php $lh->translateText("dial_method"); ?></th>
												 <th><?php $lh->translateText("status"); ?></th>
												 <th>
												 <?php if ($perm->campaign->campaign_delete !== 'N'){ ?>
												 <?php echo $checkbox_all;?>
												 <?php } ?>				
												 </th>
												 <th class='action_disposition'><?php $lh->translateText("action"); ?></th>											 
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
												if($campaign->result == 'success') {
											   		for($i=0;$i < count($campaign->campaign_id);$i++){

														if($campaign->active[$i] == "Y"){
															$campaign->active[$i] = $lh->translationFor("active");
														}else{
															$campaign->active[$i] = $lh->translationFor("inactive");
														}

														if($campaign->dial_method[$i] == $lh->translationFor("ratio")){
															$dial_method = "AUTO DIAL";
														}

														if($campaign->dial_method[$i] == $lh->translationFor("manual")){
															$dial_method = "MANUAL";
														}

														if($campaign->dial_method[$i] == $lh->translationFor("adapt_tapered")){
															$dial_method = "PREDICTIVE";
														}

														if($campaign->dial_method[$i] == $lh->translationFor("inbound_man")){
															$dial_method = "INBOUND MAN";
														}
														
													$action_CAMPAIGN = $ui->ActionMenuForCampaigns($campaign->campaign_id[$i], $campaign->campaign_name[$i], $perm);
													$checkbox = '<label for="'.$campaign->campaign_id[$i].'"><div class="checkbox c-checkbox"><label><input name="" class="check_campaign" id="'.$campaign->campaign_id[$i].'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';
											   	?>
													<tr>
														<td><?php if ($perm->campaign->campaign_update !== 'N') { echo '<a class="edit-campaign" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='32'></avatar><?php if ($perm->campaign->campaign_update !== 'N') { echo '</a>'; } ?></td>
														<td><strong><?php if ($perm->campaign->campaign_update !== 'N') { echo '<a class="edit-campaign" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><?php echo $campaign->campaign_id[$i];?><?php if ($perm->campaign->campaign_update !== 'N') { echo '</a>'; } ?></strong></td>
														<td><?php echo $campaign->campaign_name[$i];?></td>
														<td><?php echo $dial_method;?></td>
														<td><?php echo $campaign->active[$i];?></td>
														<td>
														<?php
															if ($perm->campaign->campaign_delete !== 'N'){
																echo $checkbox;
															}
														?>															
														</td>
														<td><?php echo $action_CAMPAIGN;?></td>													
													</tr>
												<?php
													}
												}
												?>
										   </tbody>
										</table>
								 </div>

								<!--==== Disposition ====-->

								  <div id="T_disposition" role="tabpanel" class="tab-pane <?php if(isset($_GET['T_disposition']))echo 'active'; ?>  ">
										<table class="display responsive no-wrap table-bordered table-striped" width="100%" id="table_disposition">
										   <thead>
											  <tr>
                         						 <th></th>
												 <th><?php $lh->translateText("campaign_id"); ?></th>
												 <th><?php $lh->translateText("campaign_name"); ?></th>
												 <th><?php $lh->translateText("custom_disposition"); ?></th>
												 <th class='action_disposition'><?php $lh->translateText("action"); ?></th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php			
													if (count($disposition->campaign_id) > 0){
														for($i=0;$i < count($campaign->campaign_id);$i++){
															$dispoStatuses = array();
															foreach ($disposition->custom_dispo as $cCamp => $cDispo){
																if($cCamp == $campaign->campaign_id[$i]){
																	foreach ($cDispo as $idx => $val) {
																		$dispoStatuses[] = $idx;
																	}
																}
															}
															if(!empty($dispoStatuses)){
											   	?>
													<tr>
														<td><?php 																
																if ($perm->disposition->disposition_update !== 'N') {
																	echo '<a class="view_disposition" data-toggle="modal" data-target="#modal_view_dispositions" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; 
																} ?><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='32'></avatar><?php if ($perm->disposition->disposition_update !== 'N') { echo '</a>'; } ?></td>
														<td><strong><?php if ($perm->disposition->disposition_update !== 'N') { echo '<a class="view_disposition" data-toggle="modal" data-target="#modal_view_dispositions" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><?php echo $campaign->campaign_id[$i];?><?php if ($perm->disposition->disposition_update !== 'N') { echo '</a>'; } ?></strong></td>
														<td><?php echo $campaign->campaign_name[$i];?></td>
														<td>
												<?php
															echo implode(", ", $dispoStatuses);
															
															$action_DISPOSITION = $ui->ActionMenuForDisposition($campaign->campaign_id[$i], $campaign->campaign_name[$i], $perm);
												?>
														</td>
														<td><?php echo $action_DISPOSITION;?></td>
													</tr>
												<?php	
															}
														}
													}
												?>
										   </tbody>
										</table>
								 </div>

								<!--==== Lead Recycling ====-->
								  <div id="T_recycling" role="tabpanel" class="tab-pane <?php if(isset($_GET['T_recycling']))echo 'active'; ?> ">
									<table class="display responsive no-wrap table-bordered table-striped" width="100%" id="table_leadrecycling">
									   <thead>
										  <tr>
                     						 <th></th>
											 <th><?php $lh->translateText("campaign_id"); ?></th>
											 <th><?php $lh->translateText("campaign_name"); ?></th>
											 <th><?php $lh->translateText("Lead Recycles"); ?></th>
											 <th class='action_disposition'><?php $lh->translateText("action"); ?></th>
										  </tr>
									   </thead>
									   <tbody>
										   	<?php
												if (count($leadrecycling->campaign_id) > 0){
													for($i=0;$i < count($campaign->campaign_id);$i++){													
										   	?>
											<tr>
												<td><?php if ($perm->disposition->disposition_update !== 'N') { echo '<a class="view_leadrecycling" data-toggle="modal" data-target="#modal_view_leadrecycling" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><avatar username='<?php echo $campaign->campaign_name[$i];?>' :size='32'></avatar><?php if ($perm->disposition->disposition_update !== 'N') { echo '</a>'; } ?></td>
												<td><strong><?php if ($perm->disposition->disposition_update !== 'N') { echo '<a class="view_leadrecycling" data-toggle="modal" data-target="#modal_view_leadrecycling" data-id="'.$campaign->campaign_id[$i].'" data-name="'.$campaign->campaign_name[$i].'">'; } ?><?php echo $campaign->campaign_id[$i];?><?php if ($perm->disposition->disposition_update !== 'N') { echo '</a>'; } ?></strong></td>
												<td><?php echo $campaign->campaign_name[$i];?></td>
												<td>
											<?php
												$leadrecycle = "";
												//if($disposition->campaign_id[$i] == $campaign->campaign_id[$i]){												
												for($a=0; $a<count($leadrecycling->campaign_id); $a++){
													$leadrecycles[] = $leadrecycling->status[$a];
													if($leadrecycling->campaign_id[$a] == $campaign->campaign_id[$i]){
														//$leadrecycles[] = $leadrecycling->status[$a];
														$leadrecycle	= $leadrecycles[$a];
														echo "<i>".$leadrecycle."</i>";
														
														if($leadrecycling->campaign_id[$a+1] == $campaign->campaign_id[$i]){																		
															echo ", ";
														}														
													}
												}
												$action_LeadRecycling = $ui->ActionMenuForLeadRecycling($campaign->campaign_id[$i]);
											?>
												</td>
												<td><?php echo $action_LeadRecycling;?></td>
											</tr>
											<?php
													}
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
												 <th><?php $lh->translateText("filter_id"); ?></th>
												 <th><?php $lh->translateText("filter_name"); ?></th>
												 <th><?php $lh->translateText("action"); ?></th>
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

								<!--==== AC-CID ====-->							
								 <div id="T_areacode" role="tabpanel" class="tab-pane <?php if(isset($_GET['T_areacode'])) echo 'active'; ?> ">
										<table class="display responsive no-wrap table-bordered table-striped" width="100%" id="table_areacode">
										   <thead>
											  <tr>
												 <th></th>
												 <th><?php $lh->translateText("campaign_id"); ?></th>
												 <th><?php $lh->translateText("campaign_name"); ?></th>
												 <th><?php $lh->translateText("areacode"); ?></th>
												 <th><?php $lh->translateText("caller_id"); ?></th>
												 <th><?php $lh->translateText("status"); ?></th>
												 <th class='action_areacode'><?php $lh->translateText("action"); ?></th>											 
											  </tr>
										   </thead>
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
									<?php
									$menu = 4;
									$menuHeight = '310px';
									$hideInbound = '';
									$hideIVR = '';
									$hideDID = '';
									if ($perm->campaign->campaign_create === 'N') {
										$menu--;
										$hideCampaign = ' hidden';
									}
									if ($perm->disposition->disposition_create === 'N') {
										$menu--;
										$hideDisposition = ' hidden';
									}
									if ($perm->disposition->disposition_create === 'N') {
										$menu--;
										$hideLeadRecycling = ' hidden';
									}
									if ($perm->disposition->disposition_create === 'N') {
										$menu--;
										$hideAreacode = ' hidden';
									}
									if ($menu < 4) { $menuHeight = '240px'; }
									if ($menu < 3) { $menuHeight = '180px'; }
									if ($menu < 2) { $menuHeight = '120px'; }
									?>
									<ul class="fab-ul" style="height: <?=$menuHeight?>;">
										<li class="li-style<?=$hideCampaign?>"><a class="fa fa-dashboard fab-div-item" data-toggle="modal" data-target="#add_campaign" title="Add Campaign"></a></li><br/>
										<li class="li-style<?=$hideDisposition?>"><a class="fa fa-tty fab-div-item" data-toggle="modal" data-target="#modal_add_disposition" title="Add Disposition"></a></li><br/>
										<li class="li-style<?=$hideLeadRecycling?>"><a class="fa fa-recycle fab-div-item" data-toggle="modal" data-target="#add_leadrecycling" title="Add Lead Recycling"></a></li><br/>
										<!--<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_leadfilter" title="Add Phone Numbers"> </a></li>-->
										<li class="li-style<?=$hideAreacode?>"><a class="fa fa-paper-plane fab-div-item" data-toggle="modal" data-target="#add_areacode" title="Add Areacode"></a></li>
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
	        <h4 class="modal-title"><b><?php $lh->translateText("campaign_information"); ?></b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="output-message-no-result hide">
		      	<div class="alert alert-warning alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong><?php $lh->translateText("there_was_an_error_retrieving_details"); ?></strong>
				</div>
			</div>
	        <div id="content" class="view-form hide">
			    <div class="form-horizontal">
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("campaign_id"); ?>:</label>
			    		<span class="info-camp-id control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("campaign_name"); ?>:</label>
			    		<span class="info-camp-name control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("campaign_description"); ?>:</label>
			    		<span class="info-camp-desc control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("allowed_inbound_blended"); ?>:</label>
			    		<span class="info-allowed control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("dial_method"); ?>:</label>
			    		<span class="info-dial-method control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("autodial_level"); ?>:</label>
			    		<span class="info-autodial-level control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5"><?php $lh->translateText("answering_machine_detection"); ?>:</label>
			    		<span class="info-ans-mach control-label align-left col-lg-7"></span>
			    	</div>
			    </div>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
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
		        	<b><?php $lh->translateText("wizard"); ?> » <span class="wizard-type"><?php $lh->translateText("outbound"); ?></span></b>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
		       	</h4>
		      </div>
		      <div class="modal-body">
		        <div id="content">
							<div class="alert alert-danger campaign-checker-message hide">
							  <strong><?php $lh->translateText("error"); ?>!</strong> <?php $lh->translateText("campaign_id_already_exist"); ?>
							</div>
					<!-- Custom Tabs (Pulled to the right) -->
					<form id="campaign_form" method="POST" action="./php/AddCampaign.php" enctype="multipart/form-data">
						<div class="row">
							<h4><?php $lh->translateText("campaign_information"); ?>
	                           <br>
	                           <small><?php $lh->translateText("campaign_details"); ?></small>
	                        </h4>
	                        <fieldset>
								<div class="form-group">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("campaign_details"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<select id="campaignType" name="campaign_type" class="form-control">
				    						<option value="outbound"><?php $lh->translateText("outbound"); ?></option>
				    						<?php if($gopackage->packagetype !== "gosmall" || ($_SESSION['user'] === "goautodial" || $_SESSION['user'] === "goAPI") ){ ?>
				    						<option value="inbound"><?php $lh->translateText("inbound"); ?></option>
				    						<option value="blended"><?php $lh->translateText("blended"); ?></option>
				    						<option value="survey"><?php $lh->translateText("survey"); ?></option>
				    						<option value="copy"><?php $lh->translateText("copy_from_campaign"); ?></option>
				    						<?php } ?>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group campaign-id">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("campaign_id"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<div class="input-group">
									      <input id="campaign-id" name="campaign_id" type="number" class="form-control" placeholder="" value="<?php echo str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT); ?>" min="0" minlength="3" maxlength="8" readonly onkeydown="return FilterInput(event)">
									      <span class="input-group-btn" style="vertical-align: top;">
									        <button id="campaign-id-edit-btn" class="btn btn-default" type="button" style="min-height: 34px;" disabled><i class="fa fa-pencil"></i></button>
									      </span>
									    </div><!-- /input-group -->
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("campaign_name"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="campaign-name" name="campaign_name" type="text" class="form-control" title="Must be 6 to 40 characters in length." minlength="6" maxlength="40" required>
				    				</div>
				    			</div>
				    			<div class="form-group inbound blended hide">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("did_tfn_extension"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="did-tfn-extension" name="did_tfn_extension" type="number" class="did-tfn-extension form-control" required>
				    				</div>
				    			</div>
				    			<div class="form-group inbound blended hide">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("call_route"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<select id="call-route" name="call_route" class="form-control">
				                            <option value="INGROUP"><?php $lh->translateText("ingroup"); ?> (default)</option>
				                            <option value="IVR"><?php $lh->translateText("ivr"); ?> (callmenu)</option>
				                            <!--<option value="AGENT">AGENT</option>-->
				                            <!--<option value="VOICEMAIL">VOICEMAIL</option>-->
				                        </select>
				    				</div>
				    			</div>
								<div class="form-group call-route-mode inbound blended hide">
									<label class="call-route-div-label control-label col-lg-4"><?php $lh->translateText("ingroup"); ?>:</label>
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
				    				<label class="control-label col-lg-4"><?php $lh->translateText("group_color"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="group-color" name="group_color" type="text" class="form-control colorpicker" val="#ffffff">
				    				</div>
				    			</div>
				    			<div class="form-group hide">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("survey_type"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<select id="survey-type" name="survey_type" class="form-control">
				                            <option value="BROADCAST"><?php $lh->translateText("voice"); ?></option>
				                            <option value="PRESS1"><?php $lh->translateText("survey"); ?></option>
				                        </select>
				    				</div>
				    			</div>
								<div class="form-group carrier-to-use">
									<label class="control-label col-lg-4"><small><?php $lh->translateText("carrier_use_for_campaign"); ?></small>:</label>
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
									<label class="control-label col-lg-4 "><?php $lh->translateText("custom_prefix"); ?>:</label>
									<div class="col-lg-8 mb">
										<input type="number" class="form-control" id="custom_prefix" name="custom_prefix" value="<?php if(($campaign->data->dial_prefix == "CUSTOM") && ($campaign->data->dial_prefix == 0) || ($campaign->data->dial_prefix == '')){echo 9;}else{echo $campaign->data->dial_prefix;} ?>" minlength="1" maxlength="20">
									</div>							
								</div>
				    			<div class="form-group survey hide">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("number_of_channels"); ?>:</label>
				    				<div class="col-lg-8 mb">
				    					<input id="no-channels" name="no_channels" type="number" value="1" min="1" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group copy-from hide">
				    				<label class="control-label col-lg-4"><?php $lh->translateText("copy_campaign"); ?>:</label>
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
							<h4><?php $lh->translateText("additional_information"); ?>
	                           <br>
	                           <small><?php $lh->translateText("assign_then_enter_account"); ?></small>
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
				    				<label class="control-label col-lg-5"><?php $lh->translateText("dial_method"); ?>:</label>
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
				    				<label class="control-label col-lg-5"><?php $lh->translateText("auto_dial"); ?>:</label>
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
				    				<label class="control-label col-lg-5"><?php $lh->translateText("campaign_recordings"); ?>:</label>
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
				    				<label class="control-label col-lg-5"><?php $lh->translateText("answering_machine_detection"); ?>:</label>
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
				    				<label class="control-label col-lg-5"><?php $lh->translateText("upload_wav"); ?>:</label>
				    				<div class="col-lg-7 mb">
				    					<div class="input-group">
											<input type="file" class="hide uploaded_wav" name="uploaded_wav" accept="audio/*">
											<input type="text" class="form-control wav-text-label" placeholder="WAV upload">
											<span class="input-group-btn">
												<button class="btn btn-default btn-browse-wav" type="button"><?php $lh->translateText("browse"); ?></button>
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
	    <div class="modal fade" id="modal_add_disposition" aria-labelledby="modal_add_disposition" >
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">

	            <!-- Header -->
	                <div class="modal-header">
	                    <h4 class="modal-title animated bounceInRight" id="ingroup_modal">
	                    	<b><?php $lh->translateText("status_wizard"); ?> » <?php $lh->translateText("create_new_status"); ?></b>
	                    	<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
	                    </h4>
	                </div>
	                <div class="modal-body">

	                <form action="#" method="POST" id="create_disposition" role="form">
	                	<input type="hidden" name="userid" id="userid" value="<?php echo $user->getUserId();?>"/>
	                    <div class="row">
	                    	<h4><?php $lh->translateText("create_disposition"); ?>
	                           <br>
	                           <small><?php $lh->translateText("assign_a_status_in_a_campaign"); ?></small>
	                        </h4>
	                        <fieldset>
		                    	<div class="form-group mt">
		                            <label class="col-sm-3 control-label" for="disposition_campaign"><?php $lh->translateText("campaign"); ?>: </label>
		                            <div class="col-sm-9 mb">
		                                <select id="disposition_campaign" name="disposition_campaign" class="form-control select2" style="width:100%;">
											<?php
											if (strtoupper($_SESSION['usergroup']) === 'ADMIN') {
											?>
		                                		<option value="ALL"> - - - ALL CAMPAIGNS - - - </option>
											<?php
											}
											
		                                   	for($i=0;$i < count($campaign->campaign_id);$i++){
											?>
		                                   		<option value='<?php echo $campaign->campaign_id[$i];?>'> <?php echo $campaign->campaign_id[$i] . " - " .$campaign->campaign_name[$i];?></option>
											<?php
		                                   	}
		                                   ?>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="disposition_status"><?php $lh->translateText("status"); ?></label>
		                            <div class="col-sm-9 mb">
		                                <input type="text" name="disposition_status" id="disposition_status" class="form-control" placeholder="<?php $lh->translateText("status_mandatory"); ?>" minlength="1" maxlength="6" required>
		                            	<label id="status-duplicate-error"></label>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="status_name"><?php $lh->translateText("status_capital"); ?></label>
		                            <div class="col-sm-9 mb">
		                                <input type="text" name="disposition_status_name" id="disposition_status_name" class="form-control" placeholder="<?php $lh->translateText("status_name"); ?>" maxlength="30" required>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="disposition_priority"><?php $lh->translateText("priority"); ?></label>
		                            <div class="col-sm-9 mb">
		                                <select id="disposition_priority" name="disposition_priority" class="form-control">
											<?php
											for ($i=1; $i<=10; $i++) {
												echo "<option value='$i'>$i</option>\n";
											}
											?>
										</select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="color"><?php $lh->translateText("color"); ?></label>
		                            <div class="col-sm-9 mb">
										<div id="status-color" data-format="alias" class="input-group colorpicker-component">
											<input type="text" name="disposition_status_color" id="disposition_status_color" class="form-control" placeholder="<?php $lh->translateText("color"); ?> (eg. #FFFFFF or white)" value="#B5B5B5" maxlength="20" required>
											<span class="input-group-addon"><i></i></span>
										</div>
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
												<span class="fa fa-check"></span> <?php $lh->translateText("selectable"); ?>
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="human_answered">
												<input type="checkbox" id="human_answered" name="human_answered">
												<span class="fa fa-check"></span> <?php $lh->translateText("human_answered"); ?>
											</label>
											<label class="col-sm-3 checkbox-inline c-checkbox" for="sale">
												<input type="checkbox" id="sale" name="sale">
												<span class="fa fa-check"></span> <?php $lh->translateText("sale"); ?>
											</label>
								        </div>
								        <div class="row">
								        	<label class="col-sm-3 checkbox-inline c-checkbox" for="dnc">
												<input type="checkbox" id="dnc" name="dnc">
												<span class="fa fa-check"></span> <?php $lh->translateText("dnc"); ?>
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="customer_contact">
												<input type="checkbox" id="customer_contact" name="customer_contact">
												<span class="fa fa-check"></span> <?php $lh->translateText("customer_contact"); ?>
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="not_interested">
												<input type="checkbox" id="not_interested" name="not_interested">
												<span class="fa fa-check"></span> <?php $lh->translateText("not_interested"); ?>
											</label>
							            </div>
								        <div class="row">
								        	<label class="col-sm-3 checkbox-inline c-checkbox" for="unworkable">
												<input type="checkbox" id="unworkable" name="unworkable">
												<span class="fa fa-check"></span> <?php $lh->translateText("unworkable"); ?>
											</label>
											<label class="col-sm-4 checkbox-inline c-checkbox" for="scheduled_callback">
												<input type="checkbox" id="scheduled_callback" name="scheduled_callback">
												<span class="fa fa-check"></span> <?php $lh->translateText("scheduled_callback"); ?>
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

    <!-- Lead Recycling Modal -->
	    <div class="modal fade" id="add_leadrecycling" aria-labelledby="add_leadrecycling" >
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">

	            <!-- Header -->
	                <div class="modal-header">
	                    <h4 class="modal-title animated bounceInRight" id="leadrecycle_modal">
	                    	<b><?php $lh->translateText("wizard"); ?> » <?php $lh->translateText("Create New Lead Recycle"); ?></b>
	                    	<button type="button" class="close" data-dismiss="modal" aria-label="close_leadrecycle"><span aria-hidden="true">&times;</span></button>
	                    </h4>
	                </div>
	                <div class="modal-body">

	                <form action="#" method="POST" id="create_leadrecycling" role="form">
	                	<input type="hidden" name="session_user" value="<?php echo $_SESSION['user'];?>"/>
	                    <div class="row">
	                    	<h4><?php $lh->translateText("Create New Lead Recycle"); ?>
	                           <br>
	                           <small><?php $lh->translateText("assign_a_status_in_a_campaign"); ?></small>
	                        </h4>
	                        <fieldset>
		                    	<div class="form-group mt">
		                            <label class="col-sm-3 control-label" for="leadrecycling_campaign"><?php $lh->translateText("campaign"); ?>: </label>
		                            <div class="col-sm-9 mb">
		                                <select id="leadrecycling_campaign" name="leadrecycling_campaign" class="form-control select2" style="width:100%;">
											<?php
											if (strtoupper($_SESSION['usergroup']) === 'ADMIN') {
											?>
		                                	<option value="ALL" selected> - - - ALL CAMPAIGNS - - - </option>
											<?php
											}
											?>
											<option value="" selected hidden disabled>PLEASE SELECT A CAMPAIGN</option>
											<?php
		                                   		for($i=0;$i < count($campaign->campaign_id);$i++){
		                                   			echo "<option value='".$campaign->campaign_id[$i]."'>".$campaign->campaign_id[$i]." - ".$campaign->campaign_name[$i]." </option>";
		                                   		}
											?>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="form-group mt">
		                            <label class="col-sm-3 control-label" for="leadrecycling_status"><?php $lh->translateText("status"); ?>: </label>
		                            <div class="col-sm-9 mb">
		                                <select id="leadrecycling_status" name="leadrecycling_status" class="form-control select2" 
						size="" onmousedown="if(this.options.length>8){this.size=8;}"  onchange='this.size=0;' onblur="this.size=0;"
						style="width:100%;">
											<optgroup label="System Statuses">
												<?php 
													//$dialStatus = $api->API_getAllDialStatuses('ALL', 1);
													 //foreach($output->status as key => $val){
													for($i=0;$i<=count($dialStatus->status->system);$i++) { 
												?>
													<?php if( !empty($dialStatus->status->system[$i]) && !in_array($dialStatus->status->system[$i], $dial_statuses) ){ ?>
														<option value="<?php echo $dialStatus->status->system[$i]?>" selected>
															<?php echo $dialStatus->status->system[$i]." - ".$dialStatus->status_name->system[$i]?>
														</option>
													<?php } ?>
												<?php } ?>
											</optgroup>
											<?php if(count($disposition) > 0){ ?>
											<optgroup label="Campaign Statuses">
											<?php
											foreach ($disposition->custom_dispo as $cCamp => $cDispo){
												foreach ($cDispo as $idx => $val) {
											?>
											<option value="<?php echo $idx;?>">
											<?php
											echo $cCamp." - ".$idx." - ".$val?>
											</option>
											<?php
												}
											}
											?>
											</optgroup>
											<?php
											}
											?>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="status"><?php $lh->translateText("Attempt Delay"); ?></label>
		                            <div class="col-sm-9 mb">
		                            	<input type="number" id="attempt_delay" name="attempt_delay" maxlength="5" min="120" max="32400" value="1800" class="form-control" required>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="attempt_maximum"><?php $lh->translateText("Attempt Maximum"); ?></label>
		                            <div class="col-sm-9 mb">
		                                <select id="attempt_maximum" name="attempt_maximum" class="form-control select2" style="width:100%;">
		                                   <?php
		                                   		for($i=1;$i <= 10;$i++){
		                                   			echo "<option value='".$i."' "; if($i == 2)echo "selected"; echo "> ".$i." </option>";
		                                   		}
		                                   ?>
		                                </select>
		                            </div>
		                        </div>
	                        </fieldset>
	                    </div><!-- end of step -->
	                </form>

	                </div> <!-- end of modal body -->
	            </div>
	        </div>
	    </div>
    <!-- end of modal -->

	<!-- AC-CID Modal -->
	<div id="add_areacode" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">

	            <!-- Header -->
	                <div class="modal-header">
	                    <h4 class="modal-title animated bounceInRight" id="ingroup_modal">
	                    	<b><?php $lh->translateText("areacode_wizard"); ?> » <?php $lh->translateText("create_new_areacode"); ?></b>
	                    	<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
	                    </h4>
	                </div>
	                <div class="modal-body">
			
	                <form action="#" method="POST" id="create_areacode" role="form">
	                	<input type="hidden" name="userid" id="userid" value="<?php echo $user->getUserId();?>"/>
	                    <div class="row">
	                    	<h4><?php $lh->translateText("create_areacode"); ?>
	                           <br>
	                           <small><?php $lh->translateText("assign_an_areacode_in_a_campaign"); ?></small>
	                        </h4>
	                        <fieldset>
		                    	<div class="form-group mt">
		                            <label class="col-sm-3 control-label" for="areacode_campaign"><?php $lh->translateText("campaign"); ?>: </label>
		                            <div class="col-sm-9 mb">
		                                <select id="areacode_campaign" name="areacode_campaign" class="form-control select2" style="width:100%;" required>
							<option value="" selected disabled> -- Choose Campaign -- </option>
						<?php
		                                   	for($i=0;$i < count($campaign->campaign_id);$i++){
								//if($campaign->use_custom_cid[$i] === 'AREACODE'){
						?>
		                                   		<option value='<?php echo $campaign->campaign_id[$i];?>'> <?php echo $campaign->campaign_id[$i] . " - " .$campaign->campaign_name[$i];?></option>
						<?php
								//}
		                                   	}
		                                ?>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="areacode"><?php $lh->translateText("areacode"); ?></label>
		                            <div class="col-sm-9 mb">
		                                <input type="text" name="areacode" id="areacode" class="form-control" placeholder="<?php $lh->translateText("areacode"); ?>" minlength="1" maxlength="5" required>
		                            	<label id="areacode-duplicate-error"></label>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="areacode_outbound_cid"><?php $lh->translateText("outbound_cid"); ?></label>
		                            <div class="col-sm-9 mb">
		                                <input type="text" name="areacode_outbound_cid" id="areacode_outbound_cid" class="form-control" placeholder="<?php $lh->translateText("outbound_cid"); ?>" maxlength="20" required>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-3 control-label" for="areacode_description"><?php $lh->translateText("description"); ?></label>
		                            <div class="col-sm-9 mb">
						<input type="text" name="areacode_description" id="areacode_description" class="form-control" placeholder="<?php $lh->translateText("description"); ?>" maxlength="50">
		                            </div>
		                        </div>
	                        </fieldset>
	                    </div><!-- end of step -->
	                	<input type="hidden" id="areacode_checker" value="0">
	                </form>

	                </div> <!-- end of modal body -->
	            </div>
		  </div>
		</div>
	<!-- End of modal -->
	
	<!-- Edit AC-CID Modal -->
	<div id="modal_edit_areacode" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">

			<!-- Header -->
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="ingroup_modal">
						<b><?php $lh->translateText("areacode_wizard"); ?> » <?php $lh->translateText("modify_areacode"); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body">
					<form action="#" method="POST" id="modify_areacode" role="form">
						<input type="hidden" name="userid" id="userid" value="<?php echo $user->getUserId();?>"/>
						<div class="row">
							<h4><?php $lh->translateText("modify_areacode"); ?>
								<br>
								<small><?php $lh->translateText("modifying_an_areacode"); ?></small>
							</h4>
							<fieldset>
								<div class="form-group mt">
									<label class="col-sm-3 control-label" for="areacode_campaign"><?php $lh->translateText("campaign"); ?>: </label>
									<div class="col-sm-9 mb">
										<input type="hidden" id="edit_areacode_campaign" name="areacode_campaign" required readonly>
										<select id="edit_areacode_campaign_select" name="areacode_campaign_select" class="form-control select2" style="width:100%;" disabled>
										<?php
											for($i=0;$i < count($campaign->campaign_id);$i++){
										?>
												<option value='<?php echo $campaign->campaign_id[$i];?>'> <?php echo $campaign->campaign_id[$i] . " - " .$campaign->campaign_name[$i];?></option>
										<?php
											}
										?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="areacode"><?php $lh->translateText("areacode"); ?></label>
									<div class="col-sm-9 mb">
										<input type="text" name="areacode" id="edit_areacode" class="form-control" placeholder="<?php $lh->translateText("areacode"); ?>" minlength="1" maxlength="5" readonly required>
										<label id="areacode-duplicate-error"></label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="edit_areacode_outbound_cid"><?php $lh->translateText("outbound_cid"); ?></label>
									<div class="col-sm-9 mb">
										<input type="text" name="areacode_outbound_cid" id="edit_areacode_outbound_cid" class="form-control" placeholder="<?php $lh->translateText("outbound_cid"); ?>" maxlength="20" required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="areacode_description"><?php $lh->translateText("description"); ?></label>
									<div class="col-sm-9 mb">
										<input type="text" name="areacode_description" id="edit_areacode_description" class="form-control" placeholder="<?php $lh->translateText("description"); ?>" maxlength="50">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="areacode_status"><?php $lh->translateText("Status"); ?></label>
									<div class="col-sm-9 mb">
										<select id="edit_areacode_status" name="areacode_status" class="form-control" style="width:100%;">
											<option value="Y">Active</option>
											<option value="N">Inactive</option>
										</select>
									</div>
								</div>
							</fieldset>
						</div><!-- end of step -->
						<input type="hidden" id="edit_areacode_checker" value="0">
					</form>
				</div> <!-- end of modal body -->
			</div>
		</div>
	</div>
	<!-- End of modal -->
	
	<?php
		// pause codes modal + datatable
		$modalTitle = $lh->translationFor("pause_codes");
		$modalSubtitle = "";
		$columns = array($lh->translationFor("pause_codes"), $lh->translationFor("pause_name"), $lh->translationFor("billable"), $lh->translationFor("action"));
		$result = $ui->generateTableHeaderWithItems($columns, "pause_codes_list", "display responsive no-wrap table-bordered table-striped", true, false, '', '', '');
		$bodyInputs = $result.'</tbody></table>';				
		$modalFooter = $ui->buttonWithLink("", "", "Create New", "button", null, "success", "btn-new-pause-code", "data-campaign=''");
		$modalFormPC = $ui->modalFormStructure('modal_view_pause_codes', '', $modalTitle, $modalSubtitle, $bodyInputs, $modalFooter, '', '', '');
		
		echo $modalFormPC;
		
		$modalTitle = $lh->translationFor("pause_codes");
		$modalSubtitle = "";		
		$campaignIdInput = $ui->singleFormGroupWrapper($ui->singleFormInputElement("", "campaign_id", "text", "", null, null, false, false, true, "campaign-id", "col-lg-9"), $lh->translationFor("campaign_id"), "col-lg-3");
		$pauseCodeInput = $ui->singleFormGroupWrapper($ui->singleFormInputElement("", "pause_code", "text", "", null, null, false, false, false, "pause-code", "col-lg-9"), $lh->translationFor("pause_code"), "col-lg-3");
		$pauseCodeNameInput = $ui->singleFormGroupWrapper($ui->singleFormInputElement("", "pause_code_name", "text", "", null, null, false, false, false, "pause-code-name", "col-lg-9"), $lh->translationFor("pause_name"), "col-lg-3");
		$options = array("YES" => "YES", "NO" => "NO", "HALF" => "HALF");
		$billableInput = $ui->singleFormGroupWithSelect($lh->translationFor("billable"), "", "billable", $options, "", true, "col-lg-3", "col-lg-9", "");
		//$hiddenidinput = $ui->hiddenFormField("customer-type-id");
		$bodyInputs = $campaignIdInput.$pauseCodeInput.$pauseCodeNameInput.$billableInput;
		$modalFooter = $ui->buttonWithLink("", "", $lh->translationFor("save"), "button", null, "success", "btn-save-pause-code", "data-id='$id'").$ui->buttonWithLink("", "", $lh->translationFor("update"), "button", null, "success", "btn-update-pause-code", "", "hide");		
		$modalFormPCF = $ui->modalFormStructure('modal_form_pause_codes', 'form_pause_codes', $modalTitle, $modalSubtitle, $bodyInputs, $modalFooter, '', '', '');
	
		echo $modalFormPCF;
		
	?>
		
	<?php
		// hotkeys modal + datatable
		$columnsHKT = array($lh->translationFor("hotkeys"), $lh->translationFor("status"), $lh->translationFor("description"), $lh->translationFor("action"));
		$resultHKT = $ui->generateTableHeaderWithItems($columnsHKT, "hotkeys_list", "display responsive no-wrap table-bordered table-striped", true, false, '', '', '');
		$bodyInputsHKT = $resultHKT.'</tbody></table>';				
		$modalFooterHKT = $ui->buttonWithLink("", "", $lh->translationFor("create_new"), "button", null, "success", "btn-new-hotkey", "data-campaign=''");
		$modalFormHKT = $ui->modalFormStructure('modal_view_hotkeys', '', $lh->translationFor("hotkeys"), "", $bodyInputsHKT, $modalFooterHKT, '', '', '');
		
		echo $modalFormHKT;
		
		$campaignIdInputHKF = $ui->singleFormGroupWrapper($ui->singleFormInputElement("", "campaign_id", "text", "", null, null, false, false, true, "campaign-id", "col-lg-9"), $lh->translationFor("campaign_id"), "col-lg-3");
		$optionsHKF = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9");
		$hotkeyInputHKF = $ui->singleFormGroupWithSelect($lh->translationFor("hotkeys"), "", "hotkey", $optionsHKF, "", true, "col-lg-3", "col-lg-9", "select2 hotkey");
		//$hiddenidinputHKF = $ui->hiddenFormField("hotkey_status_name", "", "status_name");
		$statusInputHKF = $ui->singleFormGroupWithSelectHiddenInput($lh->translationFor("status"), "", "status", "", "", true, "col-lg-3", "col-lg-9", "select2 status", array("id" => "hotkey_status_name", "name" => "status_name", "value" => ""));		
		$bodyInputsHKF = $campaignIdInputHKF.$hotkeyInputHKF.$statusInputHKF;
		$modalFooterHKF = $ui->buttonWithLink("", "", $lh->translationFor("save"), "button", null, "success", "btn-save-hotkey", "data-id='$id'");		
		$modalFormHKF = $ui->modalFormStructure('modal_form_hotkeys', 'form_hotkeys', $lh->translationFor("hotkeys"), "", $bodyInputsHKF, $modalFooterHKF, '', '', '');
	
		echo $modalFormHKF;
		
		// view lists modal + datatable
		$modalTitle = $lh->translationFor("lists");
		$modalSubtitle = "";
		$columns = array($lh->translationFor("list_id"), $lh->translationFor("list_name"), $lh->translationFor("description"), $lh->translationFor("leads_count"), $lh->translationFor("active"), $lh->translationFor("last_call_date"), $lh->translationFor("action"));
		$result = $ui->generateTableHeaderWithItems($columns, "lists_list", "display responsive no-wrap table-bordered table-striped", true, false, '', '', '');
		$bodyInputs = $result.'</tbody></table>';
		$appendToBody = '<div class="form-group pull-left" style="margin-left: 5px;"><p style="text-align: left;"> This Campaign has <b><span class="count_active"></span> active</b> lists and <b><span class="count_inactive"></span> inactive</b> lists<br/> This Campaign has <b><span class="count_leads"></span> leads</b> in the queue (hopper)<br/><a href="#" style="color: green;" class="view-leads-on-hopper" data-campaign="">'.$lh->translationFor("view_leads").'</a></p></div>';
		$modalFooter = $ui->modalDismissButton("", $lh->translationFor("close"));
		$modalFormLists = $ui->modalFormStructure('modal_view_lists', '', $modalTitle, $modalSubtitle, $bodyInputs, $appendToBody.$modalFooter, '', '', 'modal-lg');
		
		echo $modalFormLists;		
	?>
		
		<div id="modal_form_lists" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title"><b><?php $lh->translateText("lists"); ?></b> <small>(<?php $lh->translateText("form"); ?>)</small></h4>
		      </div>
		      <div class="modal-body">
				<form id="form_lists" class="form-horizontal" style="margin-top: 10px;">
					<input type="hidden" name="lists_id" class="lists-id" value="">
					<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> <?php $lh->translateText("basic_settings"); ?></a></li>
								<li><a href="#tab_2" data-toggle="tab"> <?php $lh->translateText("statuses"); ?></a></li>
								<li><a href="#tab_3" data-toggle="tab"> <?php $lh->translateText("timezones"); ?></a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">
			               		<div id="tab_1" class="tab-pane fade in active">
			               			<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("name"); ?>:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-name" name="lists_name" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("description"); ?>:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-description" name="lists_description" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("campaign"); ?>:</label>
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
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("reset_time"); ?>:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-reset-time" name="lists_reset_time" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("reset_lead"); ?>:</label>
										<div class="col-lg-3">
											<select name="lists_lead_called_status" class="form-control select2 lists-lead-called-status">
												<option value="N">N</option>
												<option value="Y">Y</option>
											</select>
										</div>
										<label class="control-label col-lg-2" style="text-align: left;"><?php $lh->translateText("active"); ?>:</label>
										<div class="col-lg-3">
											<select name="lists_active" class="form-control select2 lists-active">
												<option value="N">N</option>
												<option value="Y">Y</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("agent_script"); ?>:</label>
										<div class="col-lg-8">
											<select name="lists_agent_script_override" class="form-control lists-agent-script-override">
												<option value="" selected="selected">NONE - INACTIVE</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("campaign_override"); ?>:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-cid-override" name="lists_cid_override" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("drop_inbound_group_override"); ?>:</label>
										<div class="col-lg-3">
											<select name="lists_drop_inbound_group_override" class="form-control lists-drop-inbound-group-override">
												<option value="NONE">NONE</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("Web Form"); ?>:</label>
										<div class="col-lg-8">
											<input type="text" class="form-control lists-web-form" name="lists_web_form" placeholder="https://goautodial.org" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;"><?php $lh->translateText("TransferConf Numbers"); ?>:</label>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-a-number" name="xferconf_a_number" placeholder="xferconf_a_number" value="">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-b-number" name="xferconf_b_number" placeholder="xferconf_b_number" alue="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">&nbsp;</label>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-c-number" name="xferconf_c_number" placeholder="xferconf_c_number" value="">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-d-number" name="xferconf_d_number" placeholder="xferconf_d_number" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-4" style="text-align: left;">&nbsp;</label>
										<div class="col-lg-4">
											<input type="text" class="form-control lists-xferconf-e-number" name="xferconf_e_number" placeholder="xferconf_e_number" value="">
										</div>
									</div>
									<!-- for feature 6605 -->
									<input type="hidden" id="donotshow" />
			               		</div>
			               		<div id="tab_2" class="tab-pane">
			               			<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
										<div class="table-responsive">
											<table id="lists_statuses" class="table table-bordered" style="width: 100%;">
												<thead>
													<tr>
														<th><?php $lh->translateText("status"); ?></th>
														<th><?php $lh->translateText("description"); ?></th>
														<th><?php $lh->translateText("called"); ?></th>
														<th><?php $lh->translateText("not_called"); ?></th>
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
														<th><?php $lh->translateText("local_time"); ?></th>
														<th><?php $lh->translateText("called"); ?></th>
														<th><?php $lh->translateText("not_called"); ?></th>
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
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("closed"); ?></button>
				<button type="button" class="btn btn-success btn-update-lists" data-campaign=""><?php $lh->translateText("update"); ?></button>
		      </div>
		    </div>
		    <!-- End of modal content -->
		  </div>
		</div>

		<?php
			// view leads on hopper modal + datatable
			$modalTitle = $lh->translationFor("lists");
			$modalSubtitle = "";
			$columns = array($lh->translationFor("order"), $lh->translationFor("priority"), $lh->translationFor("lead_id"), $lh->translationFor("list_id"), $lh->translationFor("phone_number"), $lh->translationFor("state"), $lh->translationFor("status"), $lh->translationFor("count"), $lh->translationFor("gmt"), $lh->translationFor("alt_phone"), $lh->translationFor("source"));
			//$hideOnMedium = array($lh->translationFor("order"), $lh->translationFor("priority"), $lh->translationFor("state"), $lh->translationFor("count"), $lh->translationFor("gmt"));
			//$hideOnLow = array($lh->translationFor("order"), $lh->translationFor("priority"), $lh->translationFor("state"), $lh->translationFor("count"), $lh->translationFor("gmt"), $lh->translationFor("alt"), $lh->translationFor("source"));
			$result = $ui->generateTableHeaderWithItems($columns, "leads_on_hopper", "display responsive no-wrap table-bordered table-striped", true, false, '', '', '');
			$bodyInputs = $result.'</tbody></table>';
			$modalFooter = $ui->modalDismissButton("", $lh->translationFor("close"));
			$modalFormVLH = $ui->modalFormStructure('modal_view_leads_on_hopper', '', $modalTitle, $modalSubtitle, $bodyInputs, $modalFooter, '', '', 'modal-lg');
			
			echo $modalFormVLH;		
			
			// view campaign dispositions + datatable
			$modalTitle = $lh->translationFor("custom_disposition");
			$modalSubtitle = "";
			$columns = array($lh->translationFor("status"), $lh->translationFor("status_name"), $lh->translationFor("SEL"), $lh->translationFor("HA"), $lh->translationFor("sale"), "dnc", $lh->translationFor("CC"), "ni", $lh->translationFor("UW"), $lh->translationFor("SCB"), $lh->translationFor("action"));
			$result = $ui->generateTableHeaderWithItems($columns, "table_campaign_disposition", "display responsive compact table-bordered table-striped", true, false, '', '', '');
			$hiddenidinput = $ui->hiddenFormField("edit_campaign", "", "edit_campaign");
			$bodyInputs = $hiddenidinput.$result.'</tbody></table>';
			$appendToBody = '<div class="form-group pull-left" style="margin-left: 5px;"><h4>LEGEND</h4><p style="text-align: left;"><b>SEL:</b>  '.$lh->translationFor("selectable").'<br/><b>HA:</b>  '.$lh->translationFor("human_answered").'<br/><b>DNC:</b>  '.$lh->translationFor("DNC").'<br/><b>NI:</b>  '.$lh->translationFor("NI").'<br/><b>CC:</b>  '.$lh->translationFor("customer_contact").'<br/><b>UW:</b>  '.$lh->translationFor("unworkable").'<br/><b>SCB:</b>  '.$lh->translationFor("scheduled_callback").'</p></div>';
			$modalFooter = $ui->buttonWithLink("update_disposition_button", "", $lh->translationFor("update"), "button", "edit", "success", "btn-update-disposition", "data-id='$id'");
			$modalFormVCD = $ui->modalFormStructure('modal_view_dispositions', 'form_edit_dispositions', $modalTitle, $modalSubtitle, $bodyInputs, $appendToBody.$modalFooter, '', '', 'modal-lg');
			
			echo $modalFormVCD;
			
			// view campaign lead recycling + datatable
			$modalTitle = $lh->translationFor("Lead Recycling");
			$modalSubtitle = "";
			$columns = array($lh->translationFor("ID"), $lh->translationFor("status"), $lh->translationFor("Attempt Delay"), $lh->translationFor("Max Attempts"), $lh->translationFor("active"), $lh->translationFor("action"));
			$result = $ui->generateTableHeaderWithItems($columns, "table_campaign_leadrecycling", "display responsive compact no-wrap table-bordered table-striped", true, false, '', '', '');
			$hiddenidinput = $ui->hiddenFormField("edit_leadrecycling_campaign", "", "edit_leadrecycling_campaign");
			$hiddenidinputLR = $ui->hiddenFormField("edit_leadrecycling", "", "edit_leadrecycling");
			$bodyInputs = $hiddenidinput.$hiddenidinputLR.$result.'</tbody></table>';
			$modalFooter = $ui->buttonWithLink("update_leadrecycling_button", "", $lh->translationFor("update"), "button", "edit", "success", "btn-update-leadrecycling", "data-id='$id'");
			$modalForm = $ui->modalFormStructure('modal_view_leadrecycling', 'form_edit_leadrecycling', $modalTitle, $modalSubtitle, $bodyInputs, $modalFooter, '', '', 'modal-lg');
			
			echo $modalForm;
		?>

	<!-- End of modal -->

	<?php print $ui->standardizedThemeJS(); ?>
	<!-- JQUERY STEPS-->
  	<script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
        
    <!-- iCheck 1.0.1 -->
	<script src="js/plugins/iCheck/icheck.min.js"></script>

	<script type="text/javascript">
		function FilterInput(event) {
			var keyCode = ('which' in event) ? event.which : event.keyCode;
		
			isNotWanted = (keyCode == 69 || keyCode == 101);
			return !isNotWanted;
		}		

		$(document).ready(function(){
			// load cookies
			var cook_donotshow = "yes";

			$('.select').select2({ theme: 'bootstrap' });			
			$.fn.select2.defaults.set( "theme", "bootstrap" );
			
			// Datatables initialization
			var tableCampaign = $('#table_campaign').DataTable({
				destroy:true,    
				responsive:true,
				stateSave:true,
				drawCallback:function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},
				columnDefs:[
					{ width: "12%", targets: 6 },
					{ width: "4%", targets: 5 },
					//{ visible: false, targets: 1 },
					{ searchable: false, targets: [ 0, 5, 6 ] },
					{ sortable: false, targets: [ 0, 5, 6 ] },
					{ responsivePriority: 1, targets: 6 },
					{ responsivePriority: 2, targets: 5 },
					{ targets: -1, className: "dt-body-right" }
				]
			});
		
			var tableDisposition = $('#table_disposition').DataTable({
				destroy:true, 
				responsive:true,
				stateSave:true,
				drawCallback:function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},	
				rowCallback: function( row, data ) {
					//console.log(data[3]);
					if ( data[3] == "" ) {
						$(row).addClass('no_status_row');						
						$('.no_status_row').find($('li')).addClass('disabled');
						$('.no_status_row').find($('.edit_disposition')).removeClass('edit_disposition').addClass('disabled_edit_disposition');
						$('.no_status_row').find($('.view_disposition')).removeClass('view_disposition').addClass('disabled_view_disposition');
						$('.no_status_row').find($('.delete_disposition_modal')).removeClass('delete_disposition_modal').addClass('disabled_delete_disposition');
					}
				},	
				columnDefs:[
					{ width: "18%", targets: 4 },
					{ searchable: false, targets: [ 0, 4 ] },
					{ sortable: false, targets: [ 0, 4 ] },
					{ responsivePriority: 1, targets: 4 },
					{ responsivePriority: 2, targets: 2 },
					{ targets: -1, className: "dt-body-right" }
				]			
			});
			
			var tableLeadRecycling = $('#table_leadrecycling').DataTable({
				destroy:true,
				responsive:true,
				stateSave:true,
				drawCallback:function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},
				rowCallback: function( row, data ) {
					//console.log(data[3]);
					if ( data[3] == "" ) {
						$(row).addClass('no_leadrecycle_row');
						//console.log(row);
						$('.no_leadrecycle_row').find($('li')).addClass('disabled');
						$('.no_leadrecycle_row').find($('.edit-leadrecycling')).removeClass('edit-leadrecycling').addClass('disabled_edit-leadrecycling');
						$('.no_leadrecycle_row').find($('.delete-leadrecycling')).removeClass('delete-leadrecycling').addClass('disabled_delete-leadrecycling');
					}
				},				
				columnDefs:[
					{ width: "18%", targets: 4 },
					{ searchable: false, targets: [ 0, 4 ] },
					{ sortable: false, targets: [ 0, 4 ] },
					{ responsivePriority: 1, targets: 4 },
					{ responsivePriority: 2, targets: 3 },
					{ targets: -1, className: "dt-body-right" }
				]
			});

			//$('#table_areacode').dataTable();
			
			$('#table_leadfilter').dataTable();
			
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
							success: function(data) {
								console.log(data);
								if (data){ 
                                    response(JSON.parse(data));
									$('.call-route-mode').removeClass('hide');
									$('.group-color').removeClass('hide');
                                } else {
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
								console.log(response);
								$('#did-tfn-extension').val(did);
								if (response.did_route == "IN_GROUP") {
									$('#call-route').val("INGROUP").trigger('change');
									$('#ingroup-text').val(response.group_id).trigger('change');
									$('.group-color').removeClass('hide');
                                } else if (response.did_route == "CALLMENU") {
									$('#call-route').val("IVR").trigger('change');
									$('#ivr-text').val(response.menu_id).trigger('change');
									$('.group-color').addClass('hide');
                                } else if (response.did_route == "AGENT") {
									$('#call-route').val("AGENT").trigger('change');
                                    $('#agent-text').val(response.user).trigger('change');
                                } else if (response.did_route == "VOICEMAIL") {
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
                } else if (callroute == "IVR") {
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
                } else if (callroute == "AGENT") {
                    $('.call-route-div-label').html("AGENT:");
					$('.agent-div').removeClass('hide');
					$('.ingroup-div').addClass('hide');
					$('.ivr-div').addClass('hide');
					$('.voicemail-div').addClass('hide');
					$('.callroute-dummy-div').addClass('hide');
                } else if (callroute == "VOICEMAIL") {
                    $('.call-route-div-label').html("VOICEMAIL:");
					$('.voicemail-div').removeClass('hide');
					$('.ingroup-div').addClass('hide');
					$('.ivr-div').addClass('hide');
					$('.agent-div').addClass('hide');
					$('.callroute-dummy-div').addClass('hide');
                } else {
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
				cook_donotshow = Cookies.get('donotshow');
				
				var dataInfo = $(this).attr('data-info');
				dataInfo = window.atob(dataInfo);
				dataInfo = JSON.parse(dataInfo);
				//console.log(dataInfo);
				console.log(dataInfo);
				$('.lists-id').val(dataInfo.list_id);
				$('.lists-name').val(dataInfo.list_name);
				$('.lists-description').val(dataInfo.list_description);
				$('.lists-campaign').val(dataInfo.campaign_id).trigger('change');
				$('.lists-reset-time').val(dataInfo.reset_time);
				//$('.lists-lead-called-status').val(dataInfo.reset_called_lead_status).trigger('change');
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
				
				if(cook_donotshow === "yes"){
					$('.nav-tabs a[href="#tab_1"]').tab('show');
					$('.btn-update-lists').attr('data-campaign', dataInfo.campaign_id);
					$('#modal_view_lists').modal('hide');
					$('#modal_form_lists').modal('show');
					$('body').addClass('modal-open');
				}else{		
					swal({
						title: "<?php $lh->translateText("Warning!"); ?>",
						text: "<?php $lh->translateText("If you have large number of leads, this might take a few minutes."); ?>.<br/><br/><br/><div class='row'><input class='show' type='checkbox' id='donotshowbox' style='width: 20px!important;margin-left: 90px;margin-right:  10px;float: left;margin-top: -10px;' /><p style='float:left;'> Do not show this message again.</p></div>",
						type: "warning",
					html: true,
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("Continue"); ?>...",
					cancelButtonText: "<?php $lh->translateText("cancel"); ?>",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
					if (isConfirm) {
						swal.close();
						$('.nav-tabs a[href="#tab_1"]').tab('show');
							$('.btn-update-lists').attr('data-campaign', dataInfo.campaign_id);
							$('#modal_view_lists').modal('hide');
							$('#modal_form_lists').modal('show');
							$('body').addClass('modal-open');
						} else {
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
						}
					});
				}
			});

			$(document).on('click', '#donotshowbox', function(){
				if($(this).is(':checked')){
					Cookies.set('donotshow', 'yes', { expires: 30 });
				}
			});

			// Feature #6605	
			$("#modal_form_lists").on("hidden.bs.modal", function () {
				$('#lists_statuses_container').html("<br/><br/><center><i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw'></i> Loading...</center><br/><br/>");
				$('#lists_timezone_container').html("<br/><br/><center><i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw'></i> Loading...</center><br/><br/>");	
			});	
			
			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  				var target = $(e.target).attr("href") // activated tab
				var listid = $('.lists-id').val();
  				if(target === "#tab_2"){
					$.ajax({
						url: "./php/GetListsStatuses.php",
						type: 'POST',
						data: {
							list_id : listid,
						},
						dataType: 'json',
						success: function(response) {
							console.log(response);
							$('#lists_statuses_container').html(response);
						}
					});
				}
				if(target === "#tab_3"){
					$.ajax({
						url: "./php/GetListsTimezones.php",
						type: 'POST',
						data: {
							list_id : listid,
						},
						dataType: 'json',
						success: function(response) {
							//console.log(response);
							$('#lists_timezone_container').html(response);
						}
					});
				}
			});

			$(document).on('click', '.view-pause-codes', function(){
				$('#pause_codes_list').DataTable().clear().draw();
				$('#modal_view_pause_codes').modal("toggle");				
				var campaign_id = $(this).data('id');
				$('.btn-new-pause-code').attr('data-campaign', campaign_id);
				console.log(campaign_id);
				$.ajax({
					url: "./php/GetPauseCodes.php",
					type: 'POST',
					data: {
						campaign_id : campaign_id
					},
					dataType: 'json',
					success: function(data) {
						var JSONString = data;
						var JSONObject = JSON.parse(JSONString);
						//console.log(JSONObject);
						var tablePClist = $('#pause_codes_list').DataTable({
							data: JSONObject,
							destroy: true,
							responsive: true,
							stateSave: true,
							processing: true,
							drawCallback: function(settings) {
								var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
								pagination.toggle(this.api().page.info().pages > 1);
							},
							language: {
								processing: "Loading data... Please wait..."
							},
							columnDefs: [{ 
								width: "25%", 
								targets:  3 
							},{
								searchable: false,
								targets: [ 3 ]
							},{
								sortable: false,
								targets: [ 3 ]
							},{
								"targets": -1,
								"className": "dt-body-right"							
							}]
						});
					}
				});
			});

			$(document).on('click', '.view-hotkeys', function(){
				var campaign_id = $(this).data('id');
				console.log(campaign_id);
				$('#modal_view_hotkeys').modal('toggle');
				$('.btn-new-hotkey').attr('data-campaign', campaign_id);
				$.ajax({
					url: "./php/GetHotkeys.php",
					type: 'POST',
					data: {
						campaign_id : campaign_id,
					},
					dataType: 'json',
					success: function(data) {
						console.log(data);	
						var JSONObject = JSON.parse(data);
						var tableHKlist = $('#hotkeys_list').DataTable({
							data:JSONObject,
							destroy:true, 
							responsive:true,
							stateSave:true,
							drawCallback:function(settings) {
								var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
								pagination.toggle(this.api().page.info().pages > 1);
							},
							columnDefs: [{ 
								width: "25%", 
								targets: 3 
							},{
								searchable: false,
								targets: [ 3 ]
							},{
								sortable: false,
								targets: [ 3 ]
							},{
								"targets": -1,
								"className": "dt-body-right"							
							}]
						});
					}
				});
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
				var campaign_id = $(this).attr('data-campaign');
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
				var campaign_id = $(this).attr('data-campaign');
				$('.campaign-id').val(campaign_id);

				// populate status drop down select
				$.ajax({
					url: "./php/GetDialStatuses.php",
					type: 'POST',
					data: {
						campaign_id : campaign_id,
						add_hotkey : "1",
						is_selectable: "1"
					},
					dataType: 'json',
					success: function(response) {
						//console.log(response);
						$('.status').html(response);
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
				$('#modal_form_pause_codes').modal('toggle');
				$('body').addClass('modal-open');
			});

			$(document).on('click', '.btn-delete-pc', function(){
				var campaign_id = $(this).data('camp-id');
				var pause_code = $(this).data('code');
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("del_pause_code"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/DeletePauseCode.php",
								type: 'POST',
								data: {
									campaign_id: campaign_id,
									pause_code: pause_code
								},
								// dataType: 'json',
								success: function(data) {
									console.log(data);
									if (data == 1) {
										swal({
											title: "Success",
											text: "Pause Code Successfully Deleted",
											type: "success"
										},
										function(){
											get_pause_codes(campaign_id);
										}
										);
									} else {
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
									}
								}
							});
						} else {
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
						}
					}
				);
			});

			$(document).on('click', '.btn-delete-hk', function(){
				var campaign_id = $(this).data('camp-id');
				var hotkey = $(this).data('hotkey');
				var log_user = '<?=$_SESSION['user']?>';
				var log_group = '<?=$_SESSION['usergroup']?>';
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("delete_hotkey"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/DeleteHotkey.php",
								type: 'POST',
								data: {
									campaign_id: campaign_id,
									hotkey: hotkey,
									log_user: log_user,
									log_group: log_group
								},
								// dataType: 'json',
								success: function(data) {
								console.log(data);
									if (data == 1) {
										swal({
												title: "<?php $lh->translateText("success"); ?>",
												text: "<?php $lh->translateText("success_deleted"); ?>",
												type: "success"
											},
											function(){
												get_hotkeys(campaign_id);
											}
										);
									} else {
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
									}
								}
							});
						} else {
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
						}
					}
				);
			});

			$(document).on('click', '.btn-save-pause-code', function(){
				var form_data = new FormData($("#form_pause_codes")[0]);
				var campaign_id = $('.campaign-id').val();
				console.log(campaign_id);
				swal({
					title: "<?php $lh->translateText("pause_code_create_question"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("pause_code_create"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
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
									console.log(data);
									if (data == 1) {
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
									} else {
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
									}
								}
							});
						} else {
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
						}
					}
				);
			});
			
			
			$(document).on('click', '.btn-update-lists', function(){
				var campaign_id = $(this).data('campaign');
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("modify_list"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
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
									dataType: 'json',
									success: function(data) {
											console.log(data);
											if(data == 1){
												swal({
														title: "<?php $lh->translateText("success"); ?>",
														text: "<?php $lh->translateText("success_modified"); ?>",
														type: "success"
													},
													function(){
														$('#modal_form_lists').modal('hide');
														get_lists(campaign_id);
													}
												);
											}else{
													sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
											}
									}
								});
							} else {
									swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
					}
				);
			});

			$(document).on('change', '.status', function(){
				var stat_name = $(this).select2().find(":selected").data("name");
				console.log(stat_name);
				$('#hotkey_status_name').val(stat_name);
			});

			$(document).on('click', '.btn-save-hotkey', function(){
				var form_data = new FormData($("#form_hotkeys")[0]);
				var campaign_id = $('.campaign-id').val();
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("create_hotkey"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/AddHotkey.php",
								type: 'POST',
								data: form_data,
								cache: false,
								contentType: false,
								processData: false,
								success: function(data) {
									console.log(data);
									if (data == 1) {
										swal({
												title: "<?php $lh->translateText("success"); ?>",
												text: "<?php $lh->translateText("success_create"); ?>",
												type: "success"
											},
											function(){
												$('.hotkey').val('1').trigger('change');
												$('#modal_form_hotkeys').modal('hide');
												get_hotkeys(campaign_id);
											}
										);
									} else {
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");	
									}
								}
							});
						} else {
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
						}
					}
				);
			});

			$(document).on('click', '.btn-update-pause-code', function(){
				var form_data = new FormData($("#form_pause_codes")[0]);
				var campaign_id = $('.campaign-id').val();
				console.log(campaign_id);
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("pause_code"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
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
									console.log(data);
									if (data == 1) {
										swal({
											title: "Success",
											text: "<?php $lh->translateText("pause_success"); ?>",
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
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
									}
								}
							});
						} else {
							swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
						}
					}
				);
			});

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
						        $(".body:eq(" + newIndex + ") label.error", campaign_form).remove();
						        $(".body:eq(" + newIndex + ") .error", campaign_form).removeClass("error");
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
							if (resultCheck == 1) {
								swal({
									title: "<?php $lh->translateText("saving_campaign"); ?>?",
									text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
									type: "warning",
									showCancelButton: true,
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "<?php $lh->translateText("save_campaign"); ?>!",
									cancelButtonText: "<?php $lh->translateText("no"); ?>",
									closeOnConfirm: false,
									closeOnCancel: false
									},
									function(isConfirm){
										if (isConfirm) {
											$('#finish').text("Loading...");
											$('#finish').attr("disabled", true);
											$('#campaign_form').submit();
										} else {
											swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("save_not"); ?>", "error");
											$('#campaign-name').val('');
											campaign_form.children("div").steps("previous");
											$('#add_campaign').modal('hide');
										}
									});
									$('.campaign-checker-message').addClass('hide');
							} else {
								campaign_form.children("div").steps("previous");
								$('.campaign-checker-message').removeClass('hide');
								$('#campaign-id').focus();
							}

				        }
				    });

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
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

				//delete campaign
				$(document).on('click','.delete-campaign',function() {
					var id = [];
					id.push($(this).attr('data-id'));
					console.log(id);
					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "<?php $lh->translateText("delete_campaign"); ?>!",
						cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
						closeOnConfirm: false,
						closeOnCancel: false
					},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
									url: "./php/DeleteCampaign.php",
									type: 'POST',
									data: {
										campaign_id: id,
										action: "delete_selected"
									},
									success: function(data) {
									console.log(data);
										if (data == 1) {
											swal(
												{
													title: "<?php $lh->translateText("success"); ?>",
													text: "<?php $lh->translateText("campaign_deleted"); ?>!",
													type: "success"
												},
												function(){
													window.location.href = 'telephonycampaigns.php';
												}
											);
										} else {
											sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
											window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
										}
									}
								});
							} else {
								swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
						}
					);
				});
				
				$(document).on('click','.delete-multiple-campaign',function() {
					var arr = $('input:checkbox.check_campaign').filter(':checked').map(function () {
						return this.id;
					}).get();
					console.log(arr);
					swal({
							title: "<?php $lh->translateText("are_you_sure"); ?>",
							text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "<?php $lh->translateText("delete_multiple_campaign"); ?>!",
							cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
							closeOnConfirm: false,
							closeOnCancel: false
						},
							function(isConfirm){
								if (isConfirm) {
									$.ajax({
										url: "./php/DeleteCampaign.php",
										type: 'POST',
										data: {
											campaign_id: arr,
											action: "delete_selected"
										},
										success: function(data) {
										console.log(data);
											if(data == 1){
												swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("campaign_deleted"); ?>!",type: "success"},function(){window.location.href = 'telephonycampaigns.php';});
											}else{
												sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
												window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
											}
										}
									});
								} else {
										swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
								}
							}
			            );
			        });
			//------------------ end of campaign

			/*************
			** Disposition Events
			*************/

				// initialization and add of disposition
					$('#modal_add_disposition').on('shown.bs.modal', function () {
						$("#status-color").colorpicker();
					});
					
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

			        	var campaign_id = $('#disposition_campaign option:selected').val();
						var status_id = $('#disposition_status').val();
						var resultCheck = checkStatus(campaign_id, status_id);
						console.log(resultCheck);
						if (resultCheck == "1") {
							$.ajax({
								url: "./php/AddDisposition.php",
								type: 'POST',
								data: $("#create_disposition").serialize(),
								success: function(data) {
									console.log(data);
									console.log($("#create_disposition").serialize());
									if (data == 1) {
										swal({
											title: "<?php $lh->translateText("success"); ?>",
											text: "<?php $lh->translateText("success_statuses"); ?>!",
											type: "success"
											},
											function(){
												window.location.href = 'telephonycampaigns.php?T_disposition';
												$(".preloader").fadeIn();
											});
									} else {
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
										$('#finish').val("Submit");
										$('#finish').prop("disabled", false);
										disposition_form.children("div").steps("previous");
										$('#modal_add_disposition').modal('hide');									
									}
								}
							});
						} else {
							disposition_form.children("div").steps("previous");
						}


			        }
			    });

				//edit disposition
				$(document).on('click','.view_disposition,.delete_disposition_modal',function() {
					var campaign_id = $(this).attr('data-id');
					var view_type = $(this).attr('data-type');
					$('#edit_campaign').val(campaign_id);
					console.log(campaign_id);
					//$('#modal_view_dispositions').modal('toggle');
					$.ajax({
						url: "./php/ViewDisposition.php",
						type: 'POST',
						data: {
							campaign_id : campaign_id,
							dispo_update : '<?=$perm->disposition->disposition_update?>',
							dispo_delete : '<?=$perm->disposition->disposition_delete?>',
							view_type : view_type
						},
						dataType: 'json',
						success: function(data) {
							var JSONString = data;
							var JSONObject = JSON.parse(JSONString);
							var tableCP = $('#table_campaign_disposition').DataTable({
								data:JSONObject,
								destroy:true,   
								responsive:true,
								stateSave:true,
								processing:true,
								'language': {
									'loadingRecords': '&nbsp;',
									'processing': 'Loading...'
								},
								drawCallback:function(settings) {
									var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
									pagination.toggle(this.api().page.info().pages > 1);
								},
								rowCallback: function( row, data ) {
									var id = [];
									id.push(data[0]);									
									$(row).addClass('status_row');
									$(row).attr('id', 'status_row-'+id);
									$('#update_disposition_button').attr('disabled', true);
								},
								columnDefs:[
									{ width: "18%", targets: 10 },
									{ visible: false, targets: 0 },
									{ searchable: false, targets: 10 },
									{ sortable: false, targets: 10 },
									{ responsivePriority: 1, targets: 10 },
									{ responsivePriority: 2, targets: 1 },
									{ targets: -1, className: "dt-body-right" }
								]			
							});
							
							$(document).on('click','.btn-edit-disposition',function() {
								var status	= $(this).attr('data-status');
								var statusId	= "";
								if(status.indexOf(' ') >= 0){
									statusId = status.split(' ').join('-');
								} else {
									statusId = status;
								}

								$('#status_row-'+statusId).find($('input')).attr('disabled', false);
								$('.btn-edit-disposition').attr('disabled', true);
								$('#btn-cancel-disposition-'+statusId).attr('disabled', false);
								$('#update_disposition_button').attr('disabled', false);
								$('.btn-delete-disposition').attr('disabled', true);
									
								$(document).on('click','#update_disposition_button',function() {
									$.ajax({
										url: "./php/ModifyDisposition.php",
										type: 'POST',
										data: $('#form_edit_dispositions').serialize() + '&status=' + status,
										success: function(data) {
										console.log(data);
											if (data == 1) {
												swal("Success!", "Disposition Successfully Updated!", "success");
												$('#update_disposition_button').html("<i class='fa fa-check'></i> Update");
												$('#update_disposition_button').attr("disabled", true);
												window.setTimeout(function(){location.reload();},2000);
											} else {
												swal("Ooops!", "Something went wrong! "+ data, "error");
												$('#update_disposition_button').html("<i class='fa fa-check'></i> Update");
												$('#update_disposition_button').attr("disabled", false);
											}
										}
									});
								});									
							});
							
							$(document).on('click','.btn-cancel-disposition',function() {
								var status	= $(this).attr('data-status');
								console.log(status);
								$('.status_row').find($('input')).attr('disabled', true);
								$('.btn-edit-disposition').attr('disabled', false);
								$('.btn-cancel-disposition').attr('disabled', true);
								$('#update_disposition_button').attr('disabled', true);
								$('.btn-delete-disposition').attr('disabled', false);
							});	
						}
					});
				});
				
				$("#modal_view_dispositions").on("hidden.bs.modal", function() {
					$('#table_campaign_disposition').empty();
				});
				
				$(document).on('click','.delete_disposition', function() {
					var campaign_id = $(this).attr('data-id');
					var status_id = $(this).attr('data-status');
					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "<?php $lh->translateText("Yes"); ?>!",
						cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
									url: "./php/DeleteDisposition.php",
									type: 'POST',
									data: {
										disposition_id: campaign_id,
										status: status_id
									},
									success: function(data) {
									console.log(data);
										if(data == 1){
											swal({
													title: "<?php $lh->translateText("success"); ?>",
													text: "<?php $lh->translateText("Delete Success"); ?>!",
													type: "success"
												},
												function(){
													window.location.href = 'telephonycampaigns.php?T_disposition';
													$(".preloader").fadeIn();
												}
											);
										}else{
											sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
											window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
										}
									}
								});
											} else {
									swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
						}
					);
				});
						
				// view leads_recycling
				$(document).on('click','.view_leadrecycling',function() {
					var campaign_id = $(this).attr('data-id');
					$('#edit_leadrecycling_campaign').val(campaign_id);
					console.log(campaign_id);
					$.ajax({
						url: "./php/ViewLeadRecycling.php",
						type: 'POST',
						data: {
							campaign_id : campaign_id
						},
						dataType: 'json',
						success: function(data) {
							var JSONString = data;
							var JSONObject = JSON.parse(JSONString);
							var tableCP = $('#table_campaign_leadrecycling').DataTable({
								data:JSONObject,
								destroy:true,  
								responsive:true,
								stateSave:true,
								drawCallback:function(settings) {
									var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
									pagination.toggle(this.api().page.info().pages > 1);
								},
								rowCallback: function( row, data ) {
									var id = [];
									id.push(data[1]);									
									//console.log(id);
									$(row).addClass('status_row');
									$(row).attr('id', 'status_row-'+id);
									$('#update_leadrecycling_button').attr('disabled', true);
								},
								columnDefs: [{ 
									width: "18%", 
									targets: 5 
								},{
									searchable: false,
									targets: [ 0, 5 ]
								},{
									sortable: false,
									targets: [ 0, 5 ]
								},{
									"targets": -1,
									"className": "dt-body-right"							
								}]				
							});
							
							$(document).on('click','.btn-edit-leadrecycling',function() {
								var recycling_id = $(this).attr('data-id');
								var status = $(this).attr('data-status');
								$('#edit_leadrecycling').val(recycling_id);
								
								$('#status_row-'+status).find($('input')).attr('disabled', false);
								$('#status_row-'+status).find($('select')).attr('disabled', false);
								$('.btn-edit-leadrecycling').attr('disabled', true);
								$('#btn-cancel-leadrecycling-'+status).attr('disabled', false);
								$('#update_leadrecycling_button').attr('disabled', false);
								$('.btn-delete-leadrecycling').attr('disabled', true);
									
								$(document).on('click','#update_leadrecycling_button',function() {
									console.log($('#form_edit_leadrecycling').serialize());
									$.ajax({
										url: "./php/ModifyLeadRecycling.php",
										type: 'POST',
										data: $('#form_edit_leadrecycling').serialize(),
										success: function(data) {
											console.log(data);											
											if (data == 1) {
												swal("Success!", "Lead Recycling Status Successfully Updated!", "success");
												$('#update_leadrecycling_button').html("<i class='fa fa-check'></i> Update");
												$('#update_leadrecycling_button').attr("disabled", true);
												window.setTimeout(function(){
													window.location.href = 'telephonycampaigns.php?T_recycling';
													//location.reload();
												},2000);
											} else {
												swal("Ooops!", "Something went wrong! "+ data, "error");
												$('#update_leadrecycling_button').html("<i class='fa fa-check'></i> Update");
												$('#update_leadrecycling_button').attr("disabled", false);
											}
										}
									});
								});									
							});
							
							$(document).on('click','.btn-cancel-leadrecycling',function() {
								var status	= $(this).attr('data-status');
								console.log(status);
								$('.status_row').find($('input')).attr('disabled', true);
								$('#status_row-'+status).find($('select')).attr('disabled', true);
								$('.btn-edit-leadrecycling').attr('disabled', false);
								$('.btn-cancel-leadrecycling').attr('disabled', true);
								$('#update_leadrecycling_button').attr('disabled', true);
								$('.btn-delete-leadrecycling').attr('disabled', false);
							});						
						}
					});
				});

			// ----------------- end of disposition

			/*************
			** Lead Recycling Events
			*************/

				// initialization and add of disposition
					var leadrecycling_form = $("#create_leadrecycling"); // init form wizard

				    leadrecycling_form.validate({
				        errorPlacement: function errorPlacement(error, element) { element.after(error); }
				    });

				    leadrecycling_form.children("div").steps({
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
					        $(".body:eq(" + newIndex + ") label.error", leadrecycling_form).remove();
					        $(".body:eq(" + newIndex + ") .error", leadrecycling_form).removeClass("error");
					    }

			            leadrecycling_form.validate().settings.ignore = ":disabled,:hidden";
			            return leadrecycling_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            leadrecycling_form.validate().settings.ignore = ":disabled";

			            return disposition_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			            // submit
		                $.ajax({
		                    url: "./php/AddLeadRecycling.php",
		                    type: 'POST',
		                    data: $("#create_leadrecycling").serialize(),
		                    success: function(data) {
							console.log(data);
							//console.log($("#create_leadrecycling").serialize());
								if(data == 1){
									swal(
										{
											title: "<?php $lh->translateText("success"); ?>",
											text: "<?php $lh->translateText("New call status set for recycling"); ?>!",
											type: "success"
										},
										function(){
											window.location.href = 'telephonycampaigns.php?T_recycling';
											$(".preloader").fadeIn();
										}
									);
								}
								else{
									sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
									$('#finish').val("Submit");
									$('#finish').prop("disabled", false);
								}
		                    }
		                });

			        }
			    });
				
				$(document).on('click','.confirm',function() {
					$(this).attr('disabled', true);
					$('#finish').text("Loading...");
				});
				

		        //delete leadrecycling
				$(document).on('click','.delete_leadrecycling', function() {
					var recycle_id = $(this).attr('data-id');
					var campaign_id	= $(this).attr('data-campaign');
					
					if (campaign_id === recycle_id) {
						recycle_id = "";
					}
					
					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "<?php $lh->translateText("Yes"); ?>!",
						cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
									url: "./php/DeleteLeadRecycling.php",
									type: 'POST',
									data: {
										recycle_id: recycle_id,
										campaign_id: campaign_id
									},
									success: function(data) {
									console.log(data);
										if(data == 1){
											swal({
													title: "<?php $lh->translateText("success"); ?>",
													text: "<?php $lh->translateText("Delete Success"); ?>!",
													type: "success"
												},
												function(){
													window.location.href = 'telephonycampaigns.php?T_recycling';
													$(".preloader").fadeIn();
												}
											);
										}else{
											sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
											window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
										}
									}
								});
											} else {
									swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
						}
					);
				});
			/*************
			** Lead Filter Events
			*************/

				//edit leadfilter
					$(document).on('click','.edit-leadfilter',function() {
						/*var url = './edittelephonycampaign.php';
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="leadfilter" value="' + $(this).attr('data-id') + '" /></form>');
						$('body').append(form);  // This line is not necessary
						$(form).submit();*/
					});

		        //delete leadfilter
			        $(document).on('click','.delete_leadfilter',function() {
			            var id = $(this).attr('data-id');
						var log_user = '<?=$_SESSION['user']?>';
						var log_group = '<?=$_SESSION['usergroup']?>';
			            swal({
			            	title: "<?php $lh->translateText("are_you_sure"); ?>",
			            	text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
			            	type: "warning",
			            	showCancelButton: true,
			            	confirmButtonColor: "#DD6B55",
			            	confirmButtonText: "<?php $lh->translateText("leadfilter"); ?>!",
			            	cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
			            	closeOnConfirm: false,
			            	closeOnCancel: false
			            	},
			            	function(isConfirm){
			            		if (isConfirm) {
			            			$.ajax({
				                        url: "./php/DeleteLeadFilter.php",
				                        type: 'POST',
				                        data: {
				                            leadfilter_id: id,
											log_user: log_user,
											log_group: log_group
				                        },
				                        success: function(data) {
				                        console.log(data);
				                            if(data == 1){
				                            	swal(
													{
														title: "<?php $lh->translateText("success"); ?>",
														text: "<?php $lh->translateText("filter_success"); ?>!",
														type: "success"
													},
													function(){
														window.location.href = 'telephonycampaigns.php';
													}
												);
				                            }else{
				                                sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
				                                window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
				                            }
				                        }
				                    });
								} else {
			                			swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
			                	}
			            	}
			            );
			        });
			//------------------ end of leadfilter

			/*************
			** AC-CID Events
			*************/

				// initialization and add of areacode
				$('#modal_add_areacode').on('shown.bs.modal', function () {
						$("#status-color").colorpicker();
					});
					
					var areacode_form = $("#create_areacode"); // init form wizard

				    areacode_form.validate({
				        errorPlacement: function errorPlacement(error, element) { element.after(error); }
				    });

				    areacode_form.children("div").steps({
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
					        $(".body:eq(" + newIndex + ") label.error", areacode_form).remove();
					        $(".body:eq(" + newIndex + ") .error", areacode_form).removeClass("error");
					    }

			            areacode_form.validate().settings.ignore = ":disabled,:hidden";
			            return areacode_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            areacode_form.validate().settings.ignore = ":disabled";

			            var num_errors = $("#areacode_checker").val();

			            console.log(num_errors);
				        // Disable submit if there are duplicates
				        if(num_errors > 0){
					        $(".body:eq(" + currentIndex + ") .error", areacode_form).addClass("error");
				        	return false;
				        }

			            return areacode_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	var campaign_id = $('#areacode_campaign option:selected').val();
						var status_id = $('#areacode_status').val();
						//var resultCheck = checkStatus(campaign_id, status_id);
						//console.log(resultCheck);
						//if (resultCheck == "1") {
							$.ajax({
								url: "./php/AddAreacode.php",
								type: 'POST',
								data: $("#create_areacode").serialize(),
								success: function(data) {
									console.log(data);
									console.log($("#create_areacode").serialize());
									if (data == 1) {
										swal({
											title: "<?php $lh->translateText("success"); ?>",
											text: "<?php $lh->translateText("success_areacode"); ?>!",
											type: "success"
											},
											function(){
												window.location.href = 'telephonycampaigns.php?T_areacode';
												$(".preloader").fadeIn();
											});
									} else {
										sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
										$('#finish').val("Submit");
										$('#finish').prop("disabled", false);
										areacode_form.children("div").steps("previous");
										$('#modal_add_areacode').modal('hide');									
									}
								}
							});
						//} else {
						//	areacode_form.children("div").steps("previous");
						//}


			        }
			    });

				//Edit Areacode
				$('.view_areacode').on('click', function() {
					$(".preloader").fadeIn();
					var campaign_id = $(this).attr("data-camp");
					var areacode = $(this).attr("data-ac");
					$.ajax({
                                                url: "./php/ViewAreacode.php",
                                                type: 'POST',
                                                data:
                                                {
                                                        campaign_id : campaign_id,
                                                        areacode : areacode
                                                },
                                                dataType: 'json',
                                                success: function(data) {
                                                        if (data.result == 'success') {
                                                                console.log(data);
								$('#edit_areacode_campaign_select option[value="'+data.campaign_id+'"').attr('selected', 'selected');
								$('#edit_areacode_campaign').val(data.campaign_id);
                                                                $('#edit_areacode').val(data.areacode);
                                                                $('#edit_areacode_outbound_cid').val(data.outbound_cid);
                                                                $('#edit_areacode_description').val(data.cid_description);
                                                                $('#edit_areacode_status option[value="'+data.active+'"').attr('selected', 'selected');
								$(".preloader").fadeOut();
							}
                                                }
                                        });
				});

				$('#modal_edit_areacode').on('shown.bs.modal', function () {
					$("#status-color").colorpicker();
                        	});

	                        var edit_areacode_form = $("#modify_areacode"); // init form wizard

	                        edit_areacode_form.validate({
					errorPlacement: function errorPlacement(error, element) { element.after(error); }
				});

                	    	edit_areacode_form.children("div").steps({
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
					if (currentIndex < newIndex) {
						// To remove error styles
						$(".body:eq(" + newIndex + ") label.error", edit_areacode_form).remove();
						$(".body:eq(" + newIndex + ") .error", edit_areacode_form).removeClass("error");
					}
	
					edit_areacode_form.validate().settings.ignore = ":disabled,:hidden";
					return edit_areacode_form.valid();
        	                    },
                	            onFinishing: function (event, currentIndex)
                        	    {
					edit_areacode_form.validate().settings.ignore = ":disabled";
					var num_errors = $("#edit_areacode_checker").val();

					console.log(num_errors);
					// Disable submit if there are duplicates
					if(num_errors > 0){
						$(".body:eq(" + currentIndex + ") .error", edit_areacode_form).addClass("error");
						return false;
					}

					return edit_areacode_form.valid();
				    },
	  			    onFinished: function (event, currentIndex)
				    {
					$('#finish').text("Loading...");
					$('#finish').attr("disabled", true);

					var edit_campaign_id = $('#edit_areacode_campaign option:selected').val();
					var edit_status_id = $('#edit_areacode_status').val();
					//var resultCheck = checkStatus(edit_campaign_id, edit_status_id);
					//console.log(resultCheck);
					//if (resultCheck == "1") {
					$.ajax({
						url: "./php/ModifyAreacode.php",
						type: 'POST',
						data: $("#modify_areacode").serialize(),
						success: function(data) {
								console.log(data);
								console.log($("#modify_areacode").serialize());
								if (data == 1) {
									swal({
										title: "<?php $lh->translateText("success"); ?>",
										text: "<?php $lh->translateText("success_modified_areacode"); ?>!",
										type: "success"
									},
									function(){
										window.location.href = 'telephonycampaigns.php?T_areacode';
										$(".preloader").fadeIn();
									});
								} else {
									sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
									$('#finish').val("Submit");
									$('#finish').prop("disabled", false);
									edit_areacode_form.children("div").steps("previous");
									$('#modal_edit_areacode').modal('hide');
								}
							}
					});
						//} else {
						//      edit_areacode_form.children("div").steps("previous");
						//}
					}
				});

				//delete areacode
                                $(document).on('click','.delete-areacode',function() {
                                        var campId = $(this).attr('data-camp');
					var areacode = $(this).attr('data-ac');
                                        console.log(campId + " " + areacode);
                                        swal({
                                                title: "<?php $lh->translateText("are_you_sure"); ?>",
                                                text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
                                                type: "warning",
                                                showCancelButton: true,
                                                confirmButtonColor: "#DD6B55",
                                                confirmButtonText: "<?php $lh->translateText("delete_areacode"); ?>!",
                                                cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
                                                closeOnConfirm: false,
                                                closeOnCancel: false
                                        },
                                                function(isConfirm){
                                                        if (isConfirm) {
                                                                $.ajax({
                                                                        url: "./php/DeleteAreacode.php",
                                                                        type: 'POST',
                                                                        data: {
                                                                                campaign_id: campId,
										areacode: areacode,
                                                                                action: "delete_selected"
                                                                        },
                                                                        success: function(data) {
                                                                        console.log(data);
                                                                                if (data == 1) {
                                                                                        swal(
                                                                                                {
                                                                                                        title: "<?php $lh->translateText("success"); ?>",
                                                                                                        text: "<?php $lh->translateText("areacode_deleted"); ?>!",
                                                                                                        type: "success"
                                                                                                },
                                                                                                function(){
                                                                                                        window.location.href = 'telephonycampaigns.php?T_areacode';
                                                                                                }
                                                                                        );
                                                                                } else {
                                                                                    sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
                                                                                    window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
                                                                                }
                                                                        }
                                                                });
                                                        } else {
                                                                swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
                                                        }
                                                }
                                        );
                                });

			//-------- End of AC-CID 
			
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
										title: "<?php $lh->translateText("success"); ?>",
										text: "<?php $lh->translateText("campaign_success"); ?>!",
										type: "success"
									},
									function(){
										window.location.href = 'telephonycampaigns.php';
									}
								);
						<?php
							}elseif($_GET['message'] == "Error"){
						?>
								sweetAlert("Oops...", "<?php $lh->translateText("something_went_wrong"); ?>.", "error");
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
								
								/*$('#copy-from-campaign').select2({
									theme: 'bootstrap'
								});*/
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
					$('#no-channels').focusout(function(){
						var noChannels = $(this).val();
						if(noChannels == ""){	
							$(this).val("1");
						}
					});
				/*** end of campaigns ***/

				/*** DISPOSITION ***/
					// check duplicates
						$("#disposition_status").keyup(function() {
							var campaign_id = $('#disposition_campaign option:selected').val();
							var status_id =  $('#disposition_status').val();
							console.log(campaign_id);
							console.log(status_id);
							checkStatus(campaign_id, status_id);
						});

						$('#leadrecycling_campaign').change(function(){
							var campaign_id = $('#leadrecycling_campaign option:selected').val();
							var status_id =  $('#leadrecycling_status').val();
							console.log(campaign_id);
							console.log(status_id);		
							$.ajax({
								url: "./php/GetDialStatuses.php",
								type: 'POST',
								data: {
									campaign_id : campaign_id,
									add_hotkey : "0"
								},
								dataType: 'json',
								success: function(data) {
									//console.log(data);
									$('#leadrecycling_status').html(data);
									$('#leadrecycling_status').val("").trigger("change");
								}
							});							
							//checkStatus(campaign_id, status_id);
						});
				/*** end of disposition ***/

						$('#disposition_campaign').change(function(){
							var campaign_id = $('#disposition_campaign option:selected').val();
							var status_id =  $('#disposition_status').val();						
							checkStatus(campaign_id, status_id);
						});				
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

				/*** AREACODE ***/
                                        // disable special characters on adding areacode
                                                $('#areacode').bind('keypress', function (event) {
                                                    var regex = new RegExp("^[0-9]+$");
                                                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                                                    if (!regex.test(key)) {
                                                       event.preventDefault();
                                                       return false;
                                                    }
                                                });
                                        // disables pasting
                                                $('#areacode').bind("paste",function(e) {
                                                      e.preventDefault();
                                                });
                                        // disables special characters on adding outbound cid
                                                $('#areacode_outbound_cid').bind('keypress', function (event) {
                                                    var regex = new RegExp("^[0-9 ]+$");
                                                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                                                    if (!regex.test(key)) {
                                                       event.preventDefault();
                                                       return false;
                                                    }
                                                });
                                        // disables pasting
                                                $('#areacode_outbound_cid').bind("paste",function(e) {
                                                      e.preventDefault();
                                                });

					// disable special characters on edit areacode
                                                $('#edit_areacode').bind('keypress', function (event) {
                                                    var regex = new RegExp("^[0-9]+$");
                                                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                                                    if (!regex.test(key)) {
                                                       event.preventDefault();
                                                       return false;
                                                    }
                                                });
                                        // disables pasting
                                                $('#edit_areacode').bind("paste",function(e) {
                                                      e.preventDefault();
                                                });
                                        // disables special characters on edit outbound cid
                                                $('#edit_areacode_outbound_cid').bind('keypress', function (event) {
                                                    var regex = new RegExp("^[0-9 ]+$");
                                                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                                                    if (!regex.test(key)) {
                                                       event.preventDefault();
                                                       return false;
                                                    }
                                                });
                                        // disables pasting
                                                $('#edit_areacode_outbound_cid').bind("paste",function(e) {
                                                      e.preventDefault();
                                                });
                                /*** end of areacode filters ***/


				$('#auto-dial-level').change(function(){
					var val = $(this).val();
					if(val == 'ADVANCE') {
                        $('.auto-dial-level-adv').removeClass('hide');
                    }else{
						$('.auto-dial-level-adv').addClass('hide');
					}
				});
			
			$('#table_areacode').DataTable( {
				"processing": true,
				"serverSide": true,
				"ajax": {
					"url": './php/GetAreaCodes.php',
					"type": 'POST'
				},
				"order": [[ 1, "asc" ]],
				"columnDefs": [{
					"targets": [0, 6],
					"searchable": false,
					"orderable": false
				}, {
					"targets": 5,
					"render": function (data, type, row) {
						//console.log(data, type, row);
						return (data === 'Y' ? 'Active' : 'Inactive');
					}
				}, {
					"targets": 6,
					"render": function (data, type, row) {
						return '<div class="btn-group">\
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?php echo $lh->translationFor("choose_action") ?>\
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">\
								<span class="caret"></span>\
								<span class="sr-only">Toggle Dropdown</span>\
							</button>\
							<ul class="dropdown-menu" role="menu">\
								<li><a class="view_areacode" href="#" data-toggle="modal" data-target="#modal_edit_areacode" data-type="update" data-ac="'+row[3]+'" data-camp="'+row[1]+'"><?php echo $lh->translationFor("modify") ?></a></li>\
								<li><a class="delete-areacode" href="#" data-type="delete" data-ac="'+row[3]+'" data-camp="'+row[1]+'"><?php echo $lh->translationFor("delete") ?></a></li>\
							</ul>\
						</div>';
					}
				}],
				"columns": [
					{ "data": "avatar" },
					{ "data": "campaign_id" },
					{ "data": "campaign_name" },
					{ "data": "areacode" },
					{ "data": "outbound_cid" },
					{ "data": "active" },
					{ "data": "action" }
				],
				"drawCallback": function() {
					goAvatar._init(goOptions);
				}
			});
				
		}); // end of document ready

		function get_pause_codes(campaign_id){
			$('#modal_view_pause_codes').modal('show');
			$('.btn-new-pause-code').attr('data-campaign', campaign_id);
			$.ajax({
				url: "./php/GetPauseCodes.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(data) {
					var JSONObject = JSON.parse(data);
					var tablePClist = $('#pause_codes_list').DataTable({
						data:JSONObject,
						destroy:true,   
						responsive:true,
						stateSave:true,
						drawCallback:function(settings) {
							var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
							pagination.toggle(this.api().page.info().pages > 1);
						},
						columnDefs: [{ 
							width: "25%", 
							targets: 3 
						},{
							searchable: false,
							targets: [ 3 ]
						},{
							sortable: false,
							targets: [ 3 ]
						},{
							"targets": -1,
							"className": "dt-body-right"							
						}]
					});						
				}
			});
		}

		function get_hotkeys(campaign_id){
			$('#modal_view_hotkeys').modal('show');
			$('.btn-new-hotkey').attr('data-campaign', campaign_id);
			$.ajax({
				url: "./php/GetHotkeys.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id,
				},
				dataType: 'json',
				success: function(data) {
					console.log(data);	
					var JSONObject = JSON.parse(data);
					var tableHKlist = $('#hotkeys_list').DataTable({
						data:JSONObject,
						destroy:true,
						responsive:true,
						stateSave:true,
						drawCallback:function(settings) {
							var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
							pagination.toggle(this.api().page.info().pages > 1);
						},
						columnDefs: [{ 
							width: "25%", 
							targets: 3 
						},{
							searchable: false,
							targets: [ 3 ]
						},{
							sortable: false,
							targets: [ 3 ]
						},{
							"targets": -1,
							"className": "dt-body-right"							
						}]
					});
				}
			});
		}
		
		function get_lists(campaign_id) {
			$('#modal_view_lists').modal('show');
			$('.view-leads-on-hopper').attr('data-campaign', campaign_id);
			$.ajax({
				url: "./php/GetLists.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id
				},
				dataType: 'json',
				success: function(response) {
					console.log(response);	
					var JSONObject = JSON.parse(response.data);
					var tableLists = $('#lists_list').DataTable({
						data:JSONObject,
						destroy:true,  
						responsive:true,
						stateSave:true,
						drawCallback:function(settings) {
							var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
							pagination.toggle(this.api().page.info().pages > 1);
						},
						columnDefs: [{ 
							width: "16%", 
							targets: 6 
						},{
							searchable: false,
							targets: [ 6 ]
						},{
							sortable: false,
							targets: [ 6 ]							
						},{
							"targets": -1,
							"className": "dt-body-center"							
						}]
					});
					$('.count_active').text(response.count_active);
					$('.count_inactive').text(response.count_inactive);
				}
			});

			$.ajax({
				url: "./php/GetLeadsOnHopper.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id
				},
				dataType: 'json',
				success: function(response) {
					console.log(response);
					$('.count_leads').text(response.count);
				}
			});
		}

		function get_leads_on_hopper(campaign_id){
			$('#modal_view_lists').modal('hide');
			$('#modal_view_leads_on_hopper').modal('show');
			$('body').addClass('modal-open');		
			$.ajax({
				url: "./php/GetLeadsOnHopper.php",
				type: 'POST',
				data: {
					campaign_id : campaign_id
				},
				dataType: 'json',
				success: function(response) {
					console.log(response);
					var JSONObject = JSON.parse(response.data);
					var tableLeadsHopper = $('#leads_on_hopper').DataTable({
						data:JSONObject,
						destroy:true,
						responsive:true,
						stateSave:true,
						drawCallback:function(settings) {
							var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
							pagination.toggle(this.api().page.info().pages > 1);
						},
						columnDefs: [{
							searchable: false,
							targets: [ 0, 1 ]
						},{
							sortable: false,
							targets: [ 0 ]
						}]
					});
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

		function clear_form(){

		}
	
		function dialMethod(value){
			//console.log(value);
			if(value == "RATIO"){
				$('#auto-dial-level').prop('disabled', false);
				$('#auto-dial-level option[value=OFF]').prop('disabled', true);
				$('#auto-dial-level option[value=SLOW]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
				$("#answering-machine-detection").val("8369").change();
				$('#answering-machine-detection').prop('disabled', false);
			}else if(value == "ADAPT_TAPERED"){
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value=MAX_PREDICTIVE]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
				$("#answering-machine-detection").val("8369").change();
				$('#answering-machine-detection').prop('disabled', false);
			}else if(value == "INBOUND_MAN"){
				$('#auto-dial-level').prop('disabled', true);
				$("#answering-machine-detection").val("8368").change();
				$('#answering-machine-detection').prop('disabled', true);
				$('#auto-dial-level option[value=SLOW]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
			}else{
				$('#auto-dial-level').prop('disabled', true);
				$('#auto-dial-level option[value=OFF]').prop('selected', true);
				$('div.auto-dial-level').removeClass('hide');
				$('.auto-dial-level-adv').addClass('hide');
				$("#answering-machine-detection").val("8368").change();
				$('#answering-machine-detection').prop('disabled', true);
			}
			
		}

		function checkCampaign(campaign_id){
			var status = '';
			$.ajax({
				url: "./php/checkCampaign.php",
				type: 'POST',
				async: false,
				data: {
					campaign_id : campaign_id,
					status: status
				},
				dataType: 'json',
				success: function(data) {
					console.log(data);
					if (data == 1) {
						status = 1;
						$('#finish').attr("disabled", false);
						$( "#campaign_form" ).removeClass("error");
					} else {
						$('#finish').attr("disabled", true);
						$( "#campaign_form" ).removeClass("valid").addClass( "error" );						
						status = 0;
					}
				}
			});

			return status;
		}

		function checkStatus(campaign_id, status_id){
			$.ajax({
				url: "php/checkCampaign.php",
				type: 'POST',
				data: {
					status : status_id,
					campaign_id : campaign_id
				},
				dataType: 'json',
				success: function(data) {
					console.log(data);
					if (data == 1) {
						status = 1;
						$("#disposition_checker").val("0");
						$( "#status" ).removeClass("error");
						$( "#status-duplicate-error" ).text( "Status is available." ).removeClass("error").addClass("avail");
					} else {						
						$("#disposition_checker").val("1");
						$( "#status" ).addClass( "error" );
						$( "#status-duplicate-error" ).text( "Status is not available." ).removeClass("avail").addClass("error");
						status = 0;
					}
				}
			});
			
			return status;
		}

</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
