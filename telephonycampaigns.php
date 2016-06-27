
<?php	
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
    	<!-- Wizard Form style -->
    	<link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
    	<link href="css/style.css" rel="stylesheet" type="text/css" />
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
				 <div role="tabpanel" class="panel panel-transparent">
				
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

										$action_CAMPAIGN = $ui->ActionMenuForCampaigns($campaign->campaign_id[$i]);

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

										$action_DISPOSITION = $ui->ActionMenuForDisposition($campaign->campaign_id[$i]);

								   	?>	
										<tr>
											<td><?php echo $campaign->campaign_id[$i];?></td>
											<td><a class=''><?php echo $campaign->campaign_name[$i];?></a></td>
											<td><?php echo $campaign->campaign_id[$i];?></td>
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

										$action_LEADFILTER = $ui->ActionMenuForLeadFilters($leadfilter->lead_filter_id[$i]);

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
				<?php print $ui->getCircleButton("calls", "plus"); ?>
			</div>
			<div class="fab-div-area" id="fab-div-area">
				<ul class="fab-ul" style="height: 250px;">
					<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_campaign"></a></li><br/>
					<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#add_ivr"></a></li><br/>
					<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers"> </a></li>
				</ul>
			</div>
		</div>

	</div><!-- ./wrapper -->


	

	<!-- Modal -->
	<div id="add_campaign" class="modal fade" role="dialog">
	  <div class="modal-dialog" style="width: 800px;">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Campaign Wizard >> <span class="wizard-type">Outbound</span></b></h4>
	      </div>
	      <div class="modal-body">
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
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab1" data-toggle="tab">Basic</a></li>
							<li><a href="#tab2" data-toggle="tab">Advance Settings</a></li>
						</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab1">
							<div class="form-horizontal">
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Campaign Type:</label>
				    				<div class="col-lg-8">
				    					<select id="campaignType" class="form-control">
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
									      <input id="campaign-id" name="campaign_id" type="text" class="form-control" placeholder="" readonly>
									      <span class="input-group-btn">
									        <button id="campaign-id-edit-btn" class="btn btn-default" type="button"><i class="fa fa-pencil"></i></button>
									      </span>
									    </div><!-- /input-group -->
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Campaign Name:</label>
				    				<div class="col-lg-8">
				    					<input id="campaign-name" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Description:</label>
				    				<div class="col-lg-8">
				    					<input id="campaign-description" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Status:</label>
				    				<div class="col-lg-8">
				    					<select id="status" class="form-control">
				    						<option>Active</option>
				    						<option>Inactive</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Call Recordings:</label>
				    				<div class="col-lg-8">
				    					<select id="status" class="form-control">
				    						<option>On</option>
				    						<option>Off</option>
				    						<option>on-demand</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Script:</label>
				    				<div class="col-lg-8">
				    					<select id="status" class="form-control">
				    						<option>List Here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Caller ID:</label>
				    				<div class="col-lg-8">
				    					<input id="campaign-caller-id" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Dial Method:</label>
				    				<div class="col-lg-8">
				    					<select id="status" class="form-control">
				    						<option>Predictive</option>
				    						<option>Autodial</option>
				    						<option>Manual</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Carrier to use:</label>
				    				<div class="col-lg-8">
				    					<select id="carrier-to-use" class="form-control">
				    						<option>List Here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Answering machine detection:</label>
				    				<div class="col-lg-8">
				    					<select id="answering-machine" class="form-control">
				    						<option>On</option>
				    						<option>Off</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group">
				    				<label class="control-label col-lg-4">Force Reset of Hopper:</label>
				    				<div class="col-lg-8">
				    					<select id="force-reset-hopper" class="form-control">
				    						<option>Y</option>
				    						<option>N</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group did-tfn-ext hide">
				    				<label class="control-label col-lg-4">DID / TFN Extension:</label>
				    				<div class="col-lg-8">
				    					<input id="did-tfn" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group call-route hide">
				    				<span class="control-label col-lg-4">Call route:</span>
				    				<div class="col-lg-8">
				    					<select id="call-route" class="form-control">
				    						<option value="NONE"></option>
				    						<option value="INGROUP">INGROUP (campaign)</option>
				    						<option value="IVR">IVR (callmenu)</option>
				    						<option value="AGENT">AGENT</option>
				    						<option value="VOICEMAIL">VOICEMAIL</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group surver-type hide">
				    				<span class="control-label col-lg-4">Survey Type:</span>
				    				<div class="col-lg-8">
				    					<select id="survey-type" class="form-control">
				    						<option value="BROADCAST">VOICE BROADCAST</option>
				    						<option value="PRESS1">SURVEY PRESS 1</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group no-channels hide">
				    				<span class="control-label col-lg-4">Number of Channels:</span>
				    				<div class="col-lg-8">
				    					<select id="no-channels" class="form-control">
				    						<option>1</option>
				    						<option>5</option>
				    						<option>10</option>
				    						<option>15</option>
				    						<option>20</option>
				    						<option>30</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group copy-from hide">
				    				<span class="control-label col-lg-4">Copy from:</span>
				    				<div class="col-lg-8">
				    					<select id="copy-from" class="form-control">
				    						<option>LIST HERE</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group upload-wav hide">
				    				<span class="control-label col-lg-4">Please Upload .wav file</span>
				    				<div class="col-lg-8">
				    					<div class="input-group">
									      <input type="text" class="form-control" placeholder="16 bit mono 8000 PCM WAV audio files only">
									      <span class="input-group-btn">
									        <button class="btn btn-primary" type="button">Browse</button>
									      </span>
									    </div><!-- /input-group -->
				    				</div>
				    			</div>
				    			<div class="lead-section hide">
				        			<div class="form-group">
				        				<label class="control-label col-lg-4">Lead File:</label>
				        				<div class="col-lg-8">
				        					<input type="text" class="form-control">
				        				</div>
				        			</div>
				        			<div class="form-group">
				        				<label class="control-label col-lg-4">List ID:</label>
				        				<div class="col-lg-8">
				        					<span>Auto Generated here range</span>
				        				</div>
				        			</div>
				        			<div class="form-group">
				        				<label class="control-label col-lg-4">Country:</label>
				        				<div class="col-lg-8">
				        					<select class="form-control">
				        						<option>LIST HERE</option>
				        					</select>
				        				</div>
				        			</div>
				        			<div class="form-group">
				        				<label class="control-label col-lg-4">Check For Duplicates:</label>
				        				<div class="col-lg-8">
				        					<select class="form-control">
				        						<option>LIST HERE</option>
				        					</select>
				        				</div>
				        			</div>
				        			<div class="form-group">
				        				<label class="control-label col-lg-4">&nbsp</label>
				        				<div class="col-lg-8">
				        					<button type="button" class="btn btn-default">UPLOAD LEADS</button>
				        				</div>
				        			</div>
				    			</div>
			    			</div>
						</div>
						<!-- /.tab-pane -->
						<div class="tab-pane" id="tab2">
							<div class="form-horizontal">
								<div class="form-group call-time hide">
				    				<label class="control-label col-lg-4">Call time:</label>
				    				<div class="col-lg-8">
				    					<select id="call-time" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group dial-status hide">
				    				<label class="control-label col-lg-4">Dial status:</label>
				    				<div class="col-lg-8">
				    					<select id="status" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group list-order hide">
				    				<label class="control-label col-lg-4">List order:</label>
				    				<div class="col-lg-8">
				    					<select id="list_order" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group lead-filter hide">
				    				<label class="control-label col-lg-4">Lead filter:</label>
				    				<div class="col-lg-8">
				    					<select id="lead_filter" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group reset-leads-on-hopper hide">
				    				<label class="control-label col-lg-4">Reset leads on hopper:</label>
				    				<div class="col-lg-8">
				    					<select id="reset_leads_on_hopper" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group dial-timeout hide">
				    				<label class="control-label col-lg-4">Dial timeout:</label>
				    				<div class="col-lg-8">
				    					<input id="dial_timeout" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group manual-dial-prefix hide">
				    				<label class="control-label col-lg-4">Manual dial prefix:</label>
				    				<div class="col-lg-8">
				    					<input id="cmanual_dial_prefix" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group call-launch hide">
				    				<label class="control-label col-lg-4">Get call launch:</label>
				    				<div class="col-lg-8">
				    					<select id="call_launch" class="form-control">
				    						<option>None</option>
				    						<option>Script</option>
				    						<option>Webform</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group answering-machine-message hide">
				    				<label class="control-label col-lg-4">Answering machine message:</label>
				    				<div class="col-lg-8">
				    					<input id="campaign-answering-machine-message" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group pause-codes hide">
				    				<label class="control-label col-lg-4">Pause codes:</label>
				    				<div class="col-lg-8">
				    					<select id="pause_codes" class="form-control">
				    						<option>Active</option>
				    						<option>Inactive</option>
				    					</select>
				    				</div>
				    			</div><div class="form-group manual-dial-filter hide">
				    				<label class="control-label col-lg-4">Manual dial filter:</label>
				    				<div class="col-lg-8">
				    					<select id="manual_dial_filter" class="form-control">
				    						<option>dnc only</option>
				    						<option>camplist only</option>
				    						<option>dnc & camplist</option>
				    						<option>dnc & camplist all</option>
				    						<option>none</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group manual-dial-list-id hide">
				    				<label class="control-label col-lg-4">Manual dial List ID:</label>
				    				<div class="col-lg-8">
				    					<select id="manual_dial_list_id" class="form-control">
				    						<option>Active</option>
				    						<option>Inactive</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group availability-only-tally hide">
				    				<label class="control-label col-lg-4">Availability only tally:</label>
				    				<div class="col-lg-8">
				    					<select id="availability_only_tally" class="form-control">
				    						<option>Active</option>
				    						<option>Inactive</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group recording-filename hide">
				    				<label class="control-label col-lg-4">Campaign Recording filename:</label>
				    				<div class="col-lg-8">
				    					<input id="campaign_recording_filename" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group next-agent-call hide">
				    				<label class="control-label col-lg-4">Next agent call:</label>
				    				<div class="col-lg-8">
				    					<select id="next_agent_call" class="form-control">
				    						<option>random</option>
				    						<option>oldest call start</option>
				    						<option>oldest call finish</option>
				    						<option>overall user level</option>
				    						<option>fewest callslongest waiting time</option>
				    						<option>longest waiting time</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group caller-id-3-way-call hide">
				    				<label class="control-label col-lg-4">Caller ID for 3-way calls:</label>
				    				<div class="col-lg-8">
				    					<select id="calle_id_3_way_call" class="form-control">
				    						<option>campaign</option>
				    						<option>customer</option>
				    						<option>agent phone</option>
				    						<option>custom</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group dial-prefix-3-way-call hide">
				    				<label class="control-label col-lg-4">Dial prefix for 3-way calls:</label>
				    				<div class="col-lg-8">
				    					<input id="dial_prefix_3_way_call" type="text" class="form-control">
				    				</div>
				    			</div>
				    			<div class="form-group 3-way-hangup-logging hide">
				    				<label class="control-label col-lg-4">Customer 3-way hangup logging:</label>
				    				<div class="col-lg-8">
				    					<select id="3_way_hangup_logging" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group 3-way-hangup-seconds hide">
				    				<label class="control-label col-lg-4">Customer 3-way hangup seconds:</label>
				    				<div class="col-lg-8">
				    					<select id="3_way_hangup_seconds" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
				    			<div class="form-group 3-way-hangup-action hide">
				    				<label class="control-label col-lg-4">Customer 3-way hangup action:</label>
				    				<div class="col-lg-8">
				    					<select id="3_way_hangup_action" class="form-control">
				    						<option>List here</option>
				    					</select>
				    				</div>
				    			</div>
							</div>
						</div>
						<!-- /.tab-pane -->
					</div>
					<!-- /.tab-content -->
					</div>
				<!-- nav-tabs-custom -->
			    
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="add-campaign-btn">Save</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

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

	<!-- Modal -->
	<div id="confirmation-delete-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
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
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->


	<script>
		$(document).ready(function(){
			$(".bottom-menu").on('mouseenter mouseleave', function () {
			  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
			});
		});
	</script>

	<!-- Script for wizard -->
	<script type="text/javascript">
		$(document).ready(function(){
			//load datatable functionalities
			$('#table_campaign').dataTable();
			$('#table_disposition').dataTable();
			$('#table_leadfilter').dataTable();

			// $('#add_campaign').modal('show');
			// $('#view-campaign-modal').modal('show');
			

			$('#campaign-id-edit-btn').click(function(){
				$('.campaign-id').find('input[name="campaign_id"]').prop('readonly',function(i,r){
			        return !r;
			    });
			});

			$('.lead-section').removeClass('hide');
			$('.call-time').removeClass('hide');
			$('.dial-status').removeClass('hide');
			$('.list-order').removeClass('hide');
			$('.lead-filter').removeClass('hide');
			$('.dial-timeout').removeClass('hide');
			$('.manual-dial-prefix').removeClass('hide');
			$('.call-launch').removeClass('hide');
			$('.answering-machine-message').removeClass('hide');
			$('.pause-codes').removeClass('hide');
			$('.manual-dial-filter').removeClass('hide');
			$('.manual-dial-list-id').removeClass('hide');
			$('.availability-only-tally').removeClass('hide');
			$('.recording-filename').removeClass('hide');
			$('.next-agent-call').removeClass('hide');
			$('.caller-id-3-way-call').removeClass('hide');
			$('.dial-prefix-3-way-call').removeClass('hide');
			$('.3-way-hangup-logging').removeClass('hide');
			$('.3-way-hangup-seconds').removeClass('hide');
			$('.3-way-hangup-action').removeClass('hide');
			$('#campaignType').change(function(){
				var selectedTypeText = $(this).find("option:selected").text();
				var selectedTypeVal = $(this).find("option:selected").val();
				$('.wizard-type').text(selectedTypeText);

				if(selectedTypeVal == 'inbound' || selectedTypeVal == 'blended'){
					$('.did-tfn-ext').removeClass('hide');
					$('.call-route').removeClass('hide');
					$('.surver-type').addClass('hide');
					$('.no-channels').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.upload-wav').addClass('hide');

					if(selectedTypeVal == 'inbound'){
						$('.lead-section').addClass('hide');
						$('.call-launch').removeClass('hide');
						$('.answering-machine-message').removeClass('hide');
						$('.pause-codes').removeClass('hide');
						$('.manual-dial-filter').removeClass('hide');
						$('.availability-only-tally').removeClass('hide');
						$('.recording-filename').removeClass('hide');
						$('.next-agent-call').removeClass('hide');
						$('.dial-timeout').removeClass('hide');
						$('.manual-dial-prefix').removeClass('hide');
						$('.caller-id-3-way-call').removeClass('hide');
						$('.dial-prefix-3-way-call').removeClass('hide');
						$('.3-way-hangup-logging').removeClass('hide');
						$('.3-way-hangup-seconds').removeClass('hide');
						$('.3-way-hangup-action').removeClass('hide');

						$('.lead-section').addClass('hide');
						$('.call-time').addClass('hide');
						$('.dial-status').addClass('hide');
						$('.list-order').addClass('hide');
						$('.lead-filter').addClass('hide');
						$('.manual-dial-list-id').addClass('hide');
						$('.reset-leads-on-hopper').addClass('hide');
					}else{	
						$('.lead-section').removeClass('hide');
						$('.call-time').removeClass('hide');
						$('.dial-status').removeClass('hide');
						$('.list-order').removeClass('hide');
						$('.lead-filter').removeClass('hide');
						$('.reset-leads-on-hopper').removeClass('hide');
						$('.dial-timeout').removeClass('hide');
						$('.manual-dial-prefix').removeClass('hide');
						$('.call-launch').removeClass('hide');
						$('.answering-machine-message').removeClass('hide');
						$('.pause-codes').removeClass('hide');
						$('.manual-dial-filter').removeClass('hide');
						$('.availability-only-tally').removeClass('hide');
						$('.recording-filename').removeClass('hide');
						$('.next-agent-call').removeClass('hide');
						$('.caller-id-3-way-call').removeClass('hide');
						$('.dial-prefix-3-way-call').removeClass('hide');
						$('.3-way-hangup-logging').removeClass('hide');
						$('.3-way-hangup-seconds').removeClass('hide');
						$('.3-way-hangup-action').removeClass('hide');

						$('.manual-dial-list-id').addClass('hide');
					}

				}else if(selectedTypeVal == 'survey'){
					$('.did-tfn-ext').addClass('hide');
					$('.call-route').addClass('hide');
					$('.surver-type').removeClass('hide');
					$('.no-channels').removeClass('hide');
					$('.copy-from').addClass('hide');
					$('.upload-wav').removeClass('hide');
					$('.lead-section').removeClass('hide');

					$('.call-time').removeClass('hide');
					$('.dial-status').removeClass('hide');
					$('.list-order').removeClass('hide');
					$('.lead-filter').removeClass('hide');
					$('.dial-timeout').removeClass('hide');
					$('.manual-dial-prefix').removeClass('hide');
					$('.call-launch').removeClass('hide');
					$('.answering-machine-message').removeClass('hide');
					$('.pause-codes').removeClass('hide');
					$('.manual-dial-filter').removeClass('hide');
					$('.manual-dial-list-id').removeClass('hide');
					$('.availability-only-tally').removeClass('hide');
					$('.recording-filename').removeClass('hide');
					$('.next-agent-call').removeClass('hide');
					$('.caller-id-3-way-call').removeClass('hide');
					$('.dial-prefix-3-way-call').removeClass('hide');
					$('.3-way-hangup-logging').removeClass('hide');
					$('.3-way-hangup-seconds').removeClass('hide');
					$('.3-way-hangup-action').removeClass('hide');
				}else if(selectedTypeVal == 'copy'){
					$('.did-tfn-ext').addClass('hide');
					$('.call-route').addClass('hide');
					$('.surver-type').addClass('hide');
					$('.no-channels').addClass('hide');
					$('.copy-from').removeClass('hide');
					$('.upload-wav').addClass('hide');
					$('.lead-section').addClass('hide');

					$('.call-time').removeClass('hide');
					$('.dial-status').removeClass('hide');
					$('.list-order').removeClass('hide');
					$('.lead-filter').removeClass('hide');
					$('.dial-timeout').removeClass('hide');
					$('.manual-dial-prefix').removeClass('hide');
					$('.call-launch').removeClass('hide');
					$('.answering-machine-message').removeClass('hide');
					$('.pause-codes').removeClass('hide');
					$('.manual-dial-filter').removeClass('hide');
					$('.manual-dial-list-id').removeClass('hide');
					$('.availability-only-tally').removeClass('hide');
					$('.recording-filename').removeClass('hide');
					$('.next-agent-call').removeClass('hide');
					$('.caller-id-3-way-call').removeClass('hide');
					$('.dial-prefix-3-way-call').removeClass('hide');
					$('.3-way-hangup-logging').removeClass('hide');
					$('.3-way-hangup-seconds').removeClass('hide');
					$('.3-way-hangup-action').removeClass('hide');
				}else{
					// default
					$('.did-tfn-ext').addClass('hide');
					$('.call-route').addClass('hide');
					$('.surver-type').addClass('hide');
					$('.no-channels').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.upload-wav').addClass('hide');
					$('.lead-section').removeClass('hide');
					$('.call-time').removeClass('hide');
					$('.dial-status').removeClass('hide');
					$('.list-order').removeClass('hide');
					$('.lead-filter').removeClass('hide');
					$('.dial-timeout').removeClass('hide');
					$('.manual-dial-prefix').removeClass('hide');
					$('.call-launch').removeClass('hide');
					$('.answering-machine-message').removeClass('hide');
					$('.pause-codes').removeClass('hide');
					$('.manual-dial-filter').removeClass('hide');
					$('.manual-dial-list-id').removeClass('hide');
					$('.availability-only-tally').removeClass('hide');
					$('.recording-filename').removeClass('hide');
					$('.next-agent-call').removeClass('hide');
					$('.caller-id-3-way-call').removeClass('hide');
					$('.dial-prefix-3-way-call').removeClass('hide');
					$('.3-way-hangup-logging').removeClass('hide');
					$('.3-way-hangup-seconds').removeClass('hide');
					$('.3-way-hangup-action').removeClass('hide');
				}
			});

			$('#add-campaign-btn').click(function(){
				$.ajax({
				  /*url: ".\php\AddCampaign.php",*/
				  url: "./php/AddCampaign.php",
				  type: 'POST',
				  data: { 
				  	campaign_type : $('#campaignType').val(),
				  	campaign_id : $('#campaign-id').val(),
				  	campaign_name : $('#campaign-name').val(),
				  	did_tfn : $('#did-tfn').val(),
				  	call_route : $('#call-route').val(),
				  	survey_type : $('#survey-type').val(),
				  	no_channels : $('#no-channels').val(),
				  	copy_from : $('#copy-from').val(),
				  	call_time : $('.call-time').val(),
					dial_status : $('.dial-status').val(),
					list_order : $('.list-order').val(),
					lead_filter : $('.lead-filter').val(),
					dial_timeout : $('.dial-timeout').val(),
					manual_dial_prefix : $('.manual-dial-prefix').val(),
					call_launch : $('.call-launch').val(),
					answering_machine_message : $('.answering-machine-message').val(),
					pause_codes : $('.pause-codes').val(),
					manual_dial_filter : $('.manual-dial-filter').val(),
					manual_dial_list_id : $('.manual-dial-list-id').val(),
					availability_only_tally : $('.availability-only-tally').val(),
					recording_filename : $('.recording-filename').val(),
					next_agent_call : $('.next-agent-call').val(),
					caller_id_3_way_call : $('.caller-id-3-way-call').val(),
					dial_prefix_3_way_call : $('.dial-prefix-3-way-call').val(),
					three_way_hangup_logging : $('.3-way-hangup-logging').val(),
					three_way_hangup_seconds : $('.3-way-hangup-seconds').val(),
					three_way_hangup_action : $('.3-way-hangup-action').val(),
					reset_leads_on_hopper : $('.reset-leads-on-hopper').val()
				  },
				  success: function(data) {
				  	// console.log(data);
						if(data == 1){
							$('.output-message-success').removeClass('hide');
							$('.output-message-error').addClass('hide');
						}else{
							$('.output-message-error').removeClass('hide');
							$('.output-message-success').addClass('hide');
						}
				    }
				});
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
			
			/*
			 *
			 * Edit Actions
			 *
			*/
			//EDIT CAMPAIGN
			 $(".edit-campaign").click(function(e) {
				e.preventDefault();
				var url = './edittelephonycampaign.php';
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="campaign" value="' + $(this).attr('data-id') + '" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			 });
			 //EDIT IVR
			 $(".edit-disposition").click(function(e) {
				e.preventDefault();
				var url = './edittelephonycampaign.php';
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="disposition" value="' + $(this).attr('data-id') + '" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			 });
			 //EDIT PHONENUMBER/DID
			 $(".edit-leadfilter").click(function(e) {
				e.preventDefault();
				var url = './edittelephonycampaign.php';
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="leadfilter" value="' + $(this).attr('data-id') + '" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			 });

			 //DELETE LEADFILTER
			 $(".delete-leadfilter").click(function(e) {
				var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
				e.preventDefault();
				if (r == true) {
					var leadfilter = $(this).attr('data-id');
					$.post("./php/DeleteTelephonyCampaign.php", { leadfilter: leadfilter } ,function(data){
						if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
						else { alert ("<?php $lh->translateText("unable_delete_leadfilter"); ?>"); }
					});
				}
			 });


			$('.delete-campaign').click(function(){
				var camp_id = $(this).attr('data-id');
				$('.camp-id-delete-label').text(camp_id);
				$('.camp-id-delete-label').attr( "data-id", camp_id);
				$('#confirmation-delete-modal').modal('show');
			});

			$('#delete-campaign-btn').click(function(){
				var camp_id = $('.camp-id-delete-label').attr('data-id');
				// console.log(camp_id);
				$.ajax({
				  /*url: ".\php\DeleteCampaign.php",*/
				  url: "./php/DeleteCampaign.php",
				  type: 'POST',
				  data: { 
				  	campaign_id :camp_id,
				  },
				  success: function(data) {
				  		// console.log(data);
				  		if(data == 1){
				  			var table = $('#campaigns').DataTable({
				  				"sAjaxSource": ""
				  			});
							alert('Success');
							$('#confirmation-delete-modal').modal('hide');
							table.fnDraw();
						}else{
							alert(data);
						}
				    }
				});
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
