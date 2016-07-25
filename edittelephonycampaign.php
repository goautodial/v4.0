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
						
						
						<form class="form-horizontal">
							<?php $errormessage = NULL; ?>

						<!-- IF CAMPAIGN -->
							<?php
							if($campaign_id != NULL) { 
								if ($campaign->result=="success") { 
							?>
							<div class="panel-body">
								<legend>MODIFY CAMPAIGN ID : <u><?php echo $campaign_id;?></u></legend>

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
															<input type="text" class="form-control">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Campaign Description:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Active:</label>
														<div class="col-sm-10 mb">
															<select class="form-control">
																<option value="Y">Y</option>
																<option value="N">N</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Dial Method:</label>
														<div class="col-sm-10 mb">
															<select name="dial_method" id="dial_method" class="form-control">
																<option value="MANUAL" selected="selected">MANUAL</option>
																<option value="AUTO_DIAL">AUTO DIAL</option>
																<option value="PREDICTIVE">PREDICTIVE</option>
																<option value="INBOUND_MAN">INBOUND MAN</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">AutoDial Level:</label>
														<div class="col-sm-10 mb">
															<div class="row">
																<div class="col-lg-8">
																	<select id="auto_dial_level" class="form-control">
																		<option>OFF</option>
																		<option>SLOW</option>
																		<option>NORMAL</option>
																		<option>HIGH</option>
																		<option>MAX</option>
																		<option>MAX_PREDICTIVE</option>
																		<option>ADVANCE</option>
																	</select>
																</div>
																<div class="col-lg-4">
																	<select id="auto_dial_level_adv" class="form-control">
																		<option>1.0</option>
																		<option>1.5</option>
																		<option>2.0</option>
																		<option>2.5</option>
																		<option>3.0</option>
																		<option>3.5</option>
																		<option>4.0</option>
																		<option>4.5</option>
																		<option>5.0</option>
																		<option>5.5</option>
																		<option>6.0</option>
																		<option>6.5</option>
																		<option>7.0</option>
																		<option>7.5</option>
																		<option>8.0</option>
																		<option>8.5</option>
																		<option>9.0</option>
																		<option>9.5</option>
																		<option>10.0</option>
																		<option>10.5</option>
																		<option>11.0</option>
																		<option>11.5</option>
																		<option>12.0</option>
																		<option>12.5</option>
																		<option>13.0</option>
																		<option>13.5</option>
																		<option>14.0</option>
																		<option>14.5</option>
																		<option>15.0</option>
																		<option>15.5</option>
																		<option>16.0</option>
																		<option>16.5</option>
																		<option>17.0</option>
																		<option>17.5</option>
																		<option>18.0</option>
																		<option>18.5</option>
																		<option>19.0</option>
																		<option>19.5</option>
																		<option>20.0</option>
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
																		<option value="--CUSTOM--" selected="selected">CUSTOM DIAL PREFIX</option>
																	</select>
																</div>
																<div class="col-lg-3">
																	<input type="text" class="form-control">
																</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Script:</label>
														<div class="col-sm-10 mb">
															<select class="form-control">
																<option>--- NONE ---</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Campaign Caller ID:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Campaign Recording:</label>
														<div class="col-sm-10 mb">
															<select id="campaign_recording" class="form-control">
																<option value="NEVER">OFF</option>
																<option value="ALLFORCE">ON</option>
																<option value="ONDEMAND">ONDEMAND</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Answer Machine Detection:</label>
														<div class="col-sm-10 mb">
															<select id="campaign_vdad_exten" class="form-control">
																<option value="8368">OFF</option>
																<option value="8369">ON</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Local Calltime:</label>
														<div class="col-sm-10 mb">
															<select class="form-control">
																<option>-- LIST HERE --</option>
															</select>
														</div>
													</div>
												<?php if($campaign->campaign_type == "OUTBOUND") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Force Reset of Hopper:</label>
														<div class="col-sm-10 mb">
															<select class="form-control">
																<option value="Y">Y</option>
																<option value="N">N</option>
															</select>
														</div>
													</div>
												<?php } elseif($campaign->campaign_type == "INBOUND") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Phone Numbers (DID/TFN) on this campaign:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Inbound Man:</label>
														<div class="col-sm-10 mb">
															<select class="form-control">
																<option value="Y">Yes</option>
																<option value="N">No</option>
															</select>
														</div>
													</div>
												<?php } elseif($campaign->campaign_type == "BLENDED") { ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">Phone Numbers (DID/TFN) on this campaign:</label>
														<div class="col-sm-10 mb">
															<input type="text" class="form-control">
														</div>
													</div>
												<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
													<!-- Nothing to do -->
												<?php } else { ?>
													<!-- Nothing to do -->
												<?php } ?>
												</fieldset><!-- /.fieldset -->
											</div><!-- /.tab-pane -->

												<div class="tab-pane" id="tab_2">
													<?php if($campaign->campaign_type == "OUTBOUND") { ?>
														Outbound
													<?php } elseif($campaign->campaign_type == "INBOUND") { ?>
														Inbound
													<?php } elseif($campaign->campaign_type == "BLENDED") { ?>
														Blended
													<?php } elseif($campaign->campaign_type == "SURVEY") { ?>
														Survey
													<?php } else { ?>
														Default
													<?php } ?>
												</div>
												<!-- /.tab-pane -->

											<!-- Notification -->
										   	<div id="modifyUSERresult"></div>

										   	<!-- FOOTER BUTTONS -->
										   	<fieldset>
						                        <div class="box-footer">
						                           <div class="pull-right col-sm-2">
						                           		<div class="col-sm-5">
															<a href="telephonyusers.php" type="button" class="btn btn-danger pull-right"><i class="fa fa-close"></i> Cancel </a>
						                           		</div>
						                           		
						                           		<div class="col-sm-6">
						                                	<button type="submit" class="btn btn-primary pull-left" id="modifyUserOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
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

			$(document).ready(function() {
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