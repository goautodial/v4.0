<?php

	###################################################
	### Name: editsettingsphones.php 				###
	### Functions: Edit Phones 				 		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

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

$extenid = NULL;
if (isset($_POST["extenid"])) {
	$extenid = $_POST["extenid"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Phone Extension</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       
       	<?php print $ui->standardizedThemeCSS(); ?>       

        <?php print $ui->creamyThemeCSS(); ?>

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
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("Phone Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["extenid"])){
						?>	
							<li><a href="./settingsphones.php"><?php $lh->translateText("phones"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">

						<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;
					
					//if(isset($extenid)) {
						$url = gourl."/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetPhoneInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["exten_id"] = $extenid; #Desired exten ID. (required)

				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);

						if ($output->result=="success") {
							
						# Result was OK!
							for($i=0;$i<count($output->extension);$i++){
					?>
                    
                    <div class="panel-body">
                    	<legend>MODIFY PHONE EXTENSION : <u><?php echo $output->extension[$i];?></u></legend>
                   
							<form id="modifyphones">
								<input type="hidden" name="modifyid" value="<?php echo $extenid;?>">
							
						<!-- BASIC SETTINGS -->
							<div role="tabpanel">
							<!--<div class="nav-tabs-custom">-->
								<ul role="tablist" class="nav nav-tabs nav-justified">
									<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
								</ul>
				               <!-- Tab panes-->
				               <div class="tab-content">

					               	<!-- BASIC SETTINGS -->
					                <div id="tab_1" class="tab-pane fade in active">

					                <fieldset>
										<div class="form-group">
											<label for="plan" class="col-sm-2 control-label">Dial Plan Number</label>
											<div class="col-sm-10 mb">
												<input type="number" class="form-control" name="plan" id="plan" placeholder="Dial Plan Number (Mandatory)" value="<?php echo $output->dialplan_number[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="vmid" class="col-sm-2 control-label">Voicemail ID</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="vmid" id="vmid" value="<?php echo $output->voicemail_id[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="ip" class="col-sm-2 control-label">Server IP</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="ip" id="ip" value="<?php echo $output->server_ip[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label">Active Account</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="active" id="active">
												<?php
													$active = NULL;
													if($output->active[$i] == "Y"){
														$active .= '<option value="Y" selected> YES </option>';
													}else{
														$active .= '<option value="Y" > YES </option>';
													}
													
													if($output->active[$i] == "N" || $output->active[$i] == NULL){
														$active .= '<option value="N" selected> NO </option>';
													}else{
														$active .= '<option value="N" > NO </option>';
													}
													echo $active;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label">Status</label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="status" name="status">
													<?php
														$status = NULL;

														if($output->status[$i] == "ACTIVE"){
															$status .= '<option value="ACTIVE" selected> ACTIVE </option>';
														}else{
															$status .= '<option value="ACTIVE" > ACTIVE </option>';
														}
														
														if($output->status[$i] == "SUSPENDED"){
															$status .= '<option value="SUSPENDED" selected> SUSPENDED </option>';
														}else{
															$status .= '<option value="SUSPENDED" > SUSPENDED </option>';
														}
					                                    
					                                    if($output->status[$i] == "CLOSED"){
															$status .= '<option value="CLOSED" selected> CLOSED </option>';
														}else{
															$status .= '<option value="CLOSED" > CLOSED </option>';
														}
					                                    
					                                    if($output->status[$i] == "PENDING"){
															$status .= '<option value="PENDING" selected> PENDING </option>';
														}else{
															$status .= '<option value="PENDING" > PENDING </option>';
														}
					                                    
					                                    if($output->status[$i] == "ADMIN "){
															$status .= '<option value="ADMIN " selected> ADMIN  </option>';
														}else{
															$status .= '<option value="ADMIN " > ADMIN  </option>';
														}

														echo $status;
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="fullname" class="col-sm-2 control-label">Fullname</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->fullname[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">New Messages: </label>
											<div class="col-sm-10 mb">
												<span style="padding-left:20px; font-size: 20;"><?php echo $output->messages[$i];?></span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label">Old Messages: </label>
											<div class="col-sm-10 mb">
												<span style="padding-left:20px; font-size: 20;"><?php echo $output->old_messages[$i];?></span>
											</div>
										</div>
										<div class="form-group">
											<label for="protocol" class="col-sm-2 control-label">Client Protocol</label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="protocol" name="protocol">
													<?php
														$protocol = NULL;

														if($output->protocol[$i] == "SIP"){
					                                        $protocol .= '<option value="SIP" selected> SIP </option>';
					                                    }else{
					                                        $protocol .= '<option value="SIP"> SIP </option>';
					                                    }
					                                    
					                                    if($output->protocol[$i] == "Zap"){
					                                        $protocol .= '<option value="Zap" selected> Zap </option>';
					                                    }else{
					                                        $protocol .= '<option value="Zap"> Zap </option>';
					                                    }
					                                    
					                                    if($output->protocol[$i] == "IAX2"){
					                                        $protocol .= '<option value="IAX2" selected> IAX2 </option>';
					                                    }else{
					                                        $protocol .= '<option value="IAX2"> IAX2 </option>';
					                                    }
					                                     
					                                    if($output->protocol[$i] == "EXTERNAL"){
					                                        $protocol .= '<option value="EXTERNAL" selected> EXTERNAL </option>';
					                                    }else{
					                                        $protocol .= '<option value="EXTERNAL"> EXTERNAL </option>';
					                                    }

														echo $protocol;
													?>
												</select>
											</div>
										</div>
									</fieldset>
		                			
		                			</div><!-- body -->

									<fieldset>
				                        <div class="box-footer">
				                           <div class="col-sm-3 pull-right">
													<a href="settingsphones.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
				                           	
				                                	<button type="submit" class="btn btn-primary" id="update_phones" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
												
				                           </div>
				                        </div>
				                    </fieldset>
						
								</div>
							</div>
							</form>								
							
						</div>

					<?php
							}
						}	
                        
					?>
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php print $ui->standardizedThemeJS(); ?>       
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {
				
				// for cancelling
				$(document).on('click', '#cancel', function(){
					swal("Cancelled", "No action has been done :)", "error");
				});

				/** 
				 * Modifies a telephony list
			 	 */
				$("#modifyphones").validate({
                	submitHandler: function() {
						//submit the form
							$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
							$('#update_phones').prop("disabled", true);

							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifySettingsPhones.php", //post
							$("#modifyphones").serialize(), 
								function(data){
									//if message is sent
									if (data == 1) {
										swal("Updated!", "Phone has been successfully updated.", "success");
                                        window.setTimeout(function(){location.replace('settingsphones.php')},2000)

                                        ('#update_button').html("<i class='fa fa-check'></i> Update");
										$('#update_phones').prop("disabled", false);		
									} else {
										sweetAlert("Oops...","Something went wrong! " + data, "error");
										('#update_button').html("<i class='fa fa-check'></i> Update");
										$('#update_phones').prop("disabled", false);	
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				
				 
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
