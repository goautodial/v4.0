<?php	
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
        <title>Goautodial</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <!-- Circle Buttons style -->
    	<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
    	<!-- iCheck for checkboxes and radio inputs -->
  		<link rel="stylesheet" href="css/iCheck/all.css">
    	<!-- Wizard Form style -->
    	<link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
    	<link href="css/style.css" rel="stylesheet" type="text/css" />
    	<link rel="stylesheet" href="css/easyWizard.css">
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

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
                <?php if ($user->userHasAdminPermission()) { ?>
<?php

	/*
	 * API used for display in tables
	 */
	$campaign = $ui->API_getListAllCampaigns();
	$disposition = $ui->API_getAllDispositions();
	$leadfilter = $ui->API_getAllLeadFilters();
?>			
				 <div role="tabpanel" class="panel panel-transparent" style="border: 0px;">
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
					<ul role="tablist" class="nav nav-tabs">

					 <!-- In-group panel tabs-->
						 <li role="presentation" class="active">
							<a href="#T_campaign" aria-controls="T_campaign" role="tab" data-toggle="tab" class="bb0">
							   <sup><span class="fa fa-users"></span></sup> Campaigns </a>
						 </li>
					<!-- IVR panel tab -->
						 <li role="presentation">
							<a href="#T_disposition" aria-controls="T_disposition" role="tab" data-toggle="tab" class="bb0">
							   <sup><span class="fa fa-volume-up"></span></sup> Dispositions </a>
						 </li>
					<!-- DID panel tab -->
						 <li role="presentation">
							<a href="#T_leadfilter" aria-controls="T_leadfilter" role="tab" data-toggle="tab" class="bb0">
							   <sup><span class="fa fa-phone-square"></span></sup> Lead Filters </a>
						 </li>
					  </ul>
					  
					<!-- Tab panes-->
					<div class="tab-content p0 bg-white">

					<!--==== Campaigns ====-->
					  <div id="T_campaign" role="tabpanel" class="tab-pane active" style="padding: 20px;">
							<table class="table table-striped table-bordered table-hover" id="table_campaign">
							   <thead>
								  <tr>
									 <th>Campaign ID</th>
									 <th>Campaign Name</th>
									 <th class='hide-on-medium hide-on-low'>Dial Method</th>
									 <th>Status</th>
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
											<td><?php echo $campaign->campaign_id[$i];?></td>
											<td><a class=''><?php echo $campaign->campaign_name[$i];?></a></td>
											<td class='hide-on-medium hide-on-low'><?php echo $campaign->dial_method[$i];?></td>
											<td><?php echo $campaign->active[$i];?></td>
											<td><?php echo $action_CAMPAIGN;?></td>
										</tr>
									<?php
										}
									?>
							   </tbody>
							</table>
							<br/>
						<div class="panel-footer text-right">&nbsp;</div>
					 </div>
					
					<!--==== Disposition ====-->
					  <div id="T_disposition" role="tabpanel" class="tab-pane" style="padding: 20px;">
							<table class="table table-striped table-bordered table-hover" id="table_disposition">
							   <thead>
								  <tr>
									 <th>Campaign ID</th>
									 <th>Campaign Name</th>
									 <th>Custom Disposition</th>
									 <th>Action</th>
								  </tr>
							   </thead>
							   <tbody>
								   	<?php
								   		for($i=0;$i < count($campaign->campaign_id);$i++){

										$action_DISPOSITION = $ui->ActionMenuForDisposition($campaign->campaign_id[$i], $campaign->campaign_name[$i]);

								   	?>	
										<tr>
											<td><?php echo $campaign->campaign_id[$i];?></td>
											<td><a class=''><?php echo $campaign->campaign_name[$i];?></a></td>
											<td>
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
							<br/>
						<div class="panel-footer text-right">&nbsp;</div>
					 </div>

					 <!--==== Lead Filter ====-->
					  <div id="T_leadfilter" role="tabpanel" class="tab-pane" style="padding: 20px;">
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
							<br/>
						<div class="panel-footer text-right">&nbsp;</div>
					 </div>

					</div><!-- END tab content-->
				</div>
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
		
        <div class="bottom-menu skin-blue">
			<div class="action-button-circle" data-toggle="modal">
				<?php print $ui->getCircleButton("campaigns", "plus"); ?>
			</div>
			<div class="fab-div-area" id="fab-div-area">
				<ul class="fab-ul" style="height: 250px;">
					<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_campaign"></a></li><br/>
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
		    					<input id="campaign-name" name="campaign_name" type="text" class="form-control">
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
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Lead File:</label>
		    				<div class="col-lg-8">
		    					<div class="input-group">
		    						<input type="file" class="hide" id="lead-file" name="lead_file">
									<input type="text" class="form-control lead-file-holder" placeholder="Lead File">
									<span class="input-group-btn">
										<button class="btn btn-default btn-lead-file" type="button">Browse</button>
									</span>
								</div><!-- /input-group -->
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">List ID:</label>
		    				<div class="col-lg-8">
		    					<input id="list-id" name="list_id" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Country:</label>
		    				<div class="col-lg-8">
		    					<input id="country" name="country" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Check for duplicates:</label>
		    				<div class="col-lg-8">
		    					<input id="check-for-duplicates" name="check_for_duplicates" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Upload Leads:</label>
		    				<div class="col-lg-8">
								<div class="input-group">
									<input type="file" class="hide" id="leads" name="leads">
									<input type="text" class="form-control leads-holder" placeholder="Upload Leads(eg. CSV File)">
									<span class="input-group-btn">
										<button class="btn btn-default btn-leads" type="button">Browse</button>
									</span>
								</div><!-- /input-group -->
		    				</div>
		    			</div>
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
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Description:</label>
		    				<div class="col-lg-8">
		    					<input id="description" name="description" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Status:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="status" name="status">
		    						<option VALUE="ACTIVE">ACTIVE</option>
		    						<option value="INACTIVE">INACTIVE</option>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Call Recordings:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="call-recordings" name="call_recordings">
		    						<option VALUE="ON">ON</option>
		    						<option value="OFF">OFF</option>
		    						<option value="ON-DEMAND">ON-DEMAND</option>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group">
		    				<label class="control-label col-lg-4">Script:</label>
		    				<div class="col-lg-8">
		    					<input id="script" name="script" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group outbound blended">
		    				<label class="control-label col-lg-4">Answering machine detection:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="answering-machine-detection" name="answering_machine_detection">
		    						<option value="ON">ON</option>
		    						<option value="OFF">OFF</option>
		    					</select>
		    				</div>
		    			</div>
		    			<div class="form-group outbound blended">
		    				<label class="control-label col-lg-4">Caller ID:</label>
		    				<div class="col-lg-8">
		    					<input id="caller_id" name="caller_id" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group outbound">
		    				<label class="control-label col-lg-4">Force reset hopper:</label>
		    				<div class="col-lg-8">
		    					<select class="form-control" id="force-reset-hopper" name="force_reset_hopper">
		    						<option value="Y">Y</option>
		    						<option value="N">N</option>
		    					</select>
		    				</div>
		    			</div>
		    			
		    			
		    			<div class="form-group inbound hide">
		    				<label class="control-label col-lg-4">Campaign Recording:</label>
		    				<div class="col-lg-8">
		    					<input id="campaign-recording" name="campaign_recording" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group inbound hide">
		    				<label class="control-label col-lg-4">Inbound Man:</label>
		    				<div class="col-lg-8">
		    					<input id="inbound-man" name="inbound_man" type="text" class="form-control">
		    				</div>
		    			</div>
		    			<div class="form-group inbound blended hide">
		    				<label class="control-label col-lg-4">Phone numbers(DID/TFN) on this campaign:</label>
		    				<div class="col-lg-8">
		    					<input id="phone-numbers" name="phone_numbers" type="text" class="form-control">
		    				</div>
		    			</div>
		    			
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
                        <div class="row" style="padding-bottom:10px;">
                            <p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
                        </div>
                        <div class="form-group">        
                            <label class="col-sm-4 control-label" for="campaign" style="padding-top:15px;">Campaign: </label>
                            <div class="col-sm-7" style="padding-top:10px;">
                                <select id="campaign" name="campaign" class="form-control">
                                		<option> - - - ALL CAMPAIGNS - - - </option>
                                   <?php
                                   		for($i=0;$i < count($campaign->campaign_id);$i++){
                                   			echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_id[$i]." - ".$campaign->campaign_name[$i]." </option>";
                                   		}
                                   ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="status">* Status:</label>
                            <div class="col-sm-7">
                                <input type="text" name="status" id="status" class="form-control" placeholder="Status" minlength="3" maxlenght="6">
                                <span  class="text-red"><small><i>* For example: New</i></small></span>
                            </div>
                        </div>
                        <div class="form-group">        
                            <label class="col-sm-4 control-label" for="status_name">* Status Name: </label>
                            <div class="col-sm-7">
                                <input type="text" name="status_name" id="status_name" class="form-control" placeholder="Status Name">
                            </div>
                        </div>
                        <div class="form-group">        
                            <!--<label class="col-sm-2 control-label" for="grouplevel" style="padding-top:15px;"> </label>-->
                            <div class="row-sm-12" style="padding-top:10px;">
                        		<label class="col-sm-4 control-label" for="selectable">
		                				Selectable
						                  <input type="checkbox" id="selectable" name="selectable" value="Y" class="flat-red" checked>
				                </label>
				                <label class="col-sm-4 control-label" for="human_answered">
						                Human Answered
						                  <input type="checkbox" id="human_answered" name="human_answered" value="Y" class="flat-red">
						        </label>
						        <div class="col-sm-4">
							        <label class="col-sm-6 control-label" for="sale">
						                Sale
						                  <input type="checkbox" id="sale" name="sale" value="Y" class="flat-red">
						            </label>
						            <label class="col-sm-6 control-label" for="dnc">
						               	DNC
						                  <input type="checkbox" id="dnc" name="dnc" value="Y" class="flat-red">
						            </label>
					        	</div>
				            </div>
				            <div class="row-sm-12" style="padding-top:10px;">
				                <label class="col-sm-4 control-label" for="customer_contact">Customer Contact
				                  <input type="checkbox" id="customer_contact" name="customer_contact" value="Y" class="flat-red">
				                </label>
				                <label class="col-sm-4 control-label" for="not_interested">Not Interested
				                  <input type="checkbox" id="not_interested" name="not_interested" value="Y" class="flat-red">
				                </label>
				                <label class="col-sm-3 control-label" for="unworkable">Unworkable
				                  <input type="checkbox" id="unworkable" name="unworkable" value="Y" class="flat-red">
				                </label>
				                <label class="col-sm-4 control-label" for="scheduled_callback">Scheduled Callback
				                  <input type="checkbox" id="scheduled_callback" name="scheduled_callback" value="Y" class="flat-red">
				                </label>
                            </div>
                        </div>
                        
                    </div><!-- end of step -->
                
                </form>

                </div> <!-- end of modal body -->

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
                          Please fill-up all the fields correctly and do not leave any fields with (<strong> * </strong>) blank.
                        </div>
                    </div>
                </div>

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

	<!-- Modal --
	<div id="confirmation-delete-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content--
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Confirmation Box</b></h4>
	      </div>
	      <div class="modal-body">
	      	<p>Are you sure you want to delete Campaign ID: <span class="camp-id-delete-label" data-id=""></span></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="delete-campaign-btn" data-id="">Yes</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
	      </div>
	    </div>
	    <!-- End of modal content --
	  </div>
	</div>
	<!-- End of modal -->

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
   
	

	<script src="js/jquery.validate.min.js" type="text/javascript"></script>
	<script src="js/easyWizard.js" type="text/javascript"></script> 
	<!-- SLIMSCROLL-->
    <script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- iCheck 1.0.1 -->
	<script src="js/plugins/iCheck/icheck.min.js"></script>

	<!-- Script for wizard -->
	<script type="text/javascript">
		function clear_form(){

		}

		$(document).ready(function(){
			//Flat red color scheme for iCheck
		    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
		      checkboxClass: 'icheckbox_flat-green',
		      radioClass: 'iradio_flat-green'
		    });

		    //reloads page when modal closes
				$('#add_campaign').on('hidden.bs.modal', function () {
					window.location = window.location.href;
				});

				$('#add_disposition').on('hidden.bs.modal', function () {
					window.location = window.location.href;
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
            
            var validate = 0;
            var status = $("#status").val();
            var status_name = $("#status_name").val();
            
            if(status == ""){
                validate = 1;
            }

            if(status_name == ""){
                validate = 1;
            }
            /*
            if($('#selectable').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#human_answered').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#sale').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#dnc').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#scheduled_callback').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#customer_contact').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#not_interested').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}

			if($('#unworkable').is(":unchecked")){
			  $("input[type='checkbox']").val() = "N";
			}
*/
                if(validate == 0){

					
                    $.ajax({
                        url: "./php/AddDisposition.php",
                        type: 'POST',
                        data: $("#create_disposition").serialize(),
                        success: function(data) {
                          // console.log(data);
                              if(data == 1){
                                    $('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                    window.setTimeout(function(){location.reload()},3000)
                              }
                              else{
                                  $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                  $("#disposition_result").html(data); 
                              }
                        }
                    });
                	
                }else{
                    $('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                    validate = 0;
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
                var name = $(this).attr('data-name');
                var action = "DISPOSITION";

                $('.id-delete-label').attr("data-id", id);
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
                var action = $(this).attr('data-action');

                $('#id_span').html(id);

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
                	if(action == "DISPOSITION"){
                		$.ajax({
	                        url: "./php/DeleteDisposition.php",
	                        type: 'POST',
	                        data: { 
	                            disposition_id:id,
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
	<!-- End of script -->

        <script>
        	// load data.
            $(".textarea").wysihtml5();
	</script>

    </body>
</html>
