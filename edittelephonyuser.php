<?php

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

$userid = NULL;
if (isset($_POST["userid"])) {
	$userid = $_POST["userid"];
	
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Users</title>
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
                    <h1>
                        <?php $lh->translateText("Users"); ?>
                        <small><?php $lh->translateText("Edit Users"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if(isset($_POST["userid"])){
						?>	
							<li><a href="./telephonyusers.php"><?php $lh->translateText("Users"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content" style="padding:30px; padding-left:100px; padding-right:100px; margin-left: 0; margin-right: 0;">
					<!-- standard custom edition form -->
					<?php
					$userobj = NULL;
					$errormessage = NULL;
					
					if(isset($userid)) {
						//$db = new \creamy\DbHandler();
						//$customerobj = $db->getDataForCustomer($customerid, $customerType);
						
						$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["user_id"] = $userid; #Desired User ID (required)

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
						
						// print_r($data);
						
						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i<count($output->userno);$i++){
					/*			
								$hidden_f = $ui->hiddenFormField("modifyid", $userid);
								
								$id_f = '<h4>Agent ID : <b>'.$userid.'</b></h4>';
								
								// name
								// $name_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("name", "name", "text", $lh->translationFor("name"), $userobj["name"], "user", true));
								//$name_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("name", "name", "text", $lh->translationFor("name"), $output->full_name[$i], "user", true));
								
								$name_l = '<h4>Full Name</h4>';
								$ph = $lh->translationFor("Full Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->full_name[$i]) ? $output->full_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "user", "required"));
								
								
								$usergroup_l = '<h4>User Group</h4>';
								$usergroup_f = '<select class="form-control" id="usergroup" name="usergroup">';
												
									if($output->user_group[$i] == "ADMIN"){
										$usergroup_f .= '<option value="ADMINISTRATORS" selected>GOAUTODIAL ADMINISTRATORS</option>';
									}else{
										$usergroup_f .= '<option value="ADMINISTRATORS" >GOAUTODIAL ADMINISTRATORS</option>';
									}
									
									if($output->user_group[$i] == "AGENTS"){
										$usergroup_f .= '<option value="AGENTS" selected>GOAUTODIAL AGENTS</option>';
									}else{
										$usergroup_f .= '<option value="AGENTS" >GOAUTODIAL AGENTS</option>';
									}
									
									if($output->user_group[$i] == "SUPERVISOR"){
										$usergroup_f .= '<option value="SUPERVISOR" selected>SUPERVISOR</option>';
									}else{
										$usergroup_f .= '<option value="SUPERVISOR" >SUPERVISOR</option>';
									}
									
								$usergroup_f .= '</select>';
								
								
								$status_l = '<h4>Active</h4>';
								$status_f = '<select class="form-control" id="status" name="status">';
												
									if($output->active[$i] == "Y"){
										$status_f .= '<option value="Y" selected> YES </option>';
									}else{
										$status_f .= '<option value="Y" > YES </option>';
									}
									
									if($output->active[$i] == "N"){
										$status_f .= '<option value="N" selected> NO </option>';
									}else{
										$status_f .= '<option value="N" > NO </option>';
									}
									
								$status_f .= '</select>';
								
								
								$userlevel_l = '<h4>User Level</h4>';
								$userlevel_f = '<select class="form-control" id="userlevel" name="userlevel">';
								
										if($output->user_level[$i] == "1"){
											$userlevel_f .= '<option value="1" selected> 1 </option>';
										}else{
											$userlevel_f .= '<option value="1" > 1 </option>';
										}
										if($output->user_level[$i] == "2"){
											$userlevel_f .= '<option value="2" selected> 2 </option>';
										}else{
											$userlevel_f .= '<option value="2" > 2 </option>';
										}
										if($output->user_level[$i] == "3"){
											$userlevel_f .= '<option value="3" selected> 3 </option>';
										}else{
											$userlevel_f .= '<option value="3" > 3 </option>';
										}
										if($output->user_level[$i] == "4"){
											$userlevel_f .= '<option value="4" selected> 4 </option>';
										}else{
											$userlevel_f .= '<option value="4" > 4 </option>';
										}
										if($output->user_level[$i] == "5"){
											$userlevel_f .= '<option value="5" selected> 5 </option>';
										}else{
											$userlevel_f .= '<option value="5" > 5 </option>';
										}
										if($output->user_level[$i] == "6"){
											$userlevel_f .= '<option value="6" selected> 6 </option>';
										}else{
											$userlevel_f .= '<option value="6" > 6 </option>';
										}
										if($output->user_level[$i] == "7"){
											$userlevel_f .= '<option value="7" selected> 7 </option>';
										}else{
											$userlevel_f .= '<option value="7" > 7 </option>';
										}
										if($output->user_level[$i] == "8"){
											$userlevel_f .= '<option value="8" selected> 8 </option>';
										}else{
											$userlevel_f .= '<option value="8" > 8 </option>';
										}
										if($output->user_level[$i] == "9"){
											$userlevel_f .= '<option value="9" selected> 9 </option>';
										}else{
											$userlevel_f .= '<option value="9" > 9 </option>';
										}
											
								$userlevel_f .= '</select>';
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyT_userDeleteButton", $userid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
								// generate the form
								$fields = $hidden_f.$id_f.$name_l.$name_f.$usergroup_l.$usergroup_f.$status_l.$status_f.$userlevel_l.$userlevel_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyuser", $fields, $buttons, "modifyT_userresult");
								
								// generate and show the box
								//$box = $ui->boxWithForm("modifyuser", , $fields, $lh->translationFor("edit_user"));
								//print $box;
								
								// generate box
								$boxTitle = $lh->translationFor("insert_new_data");
								$formBox = $ui->boxWithContent($boxTitle, $form);
								// print our modifying customer box.
								print $formBox;
						*/
						?>

							<div role="tabpanel" class="panel panel-transparent" style="box-shadow: 5px 5px 8px #888888;">
							
							<h4 style="padding:15px;"><a type="button" class="btn" href="telephonyusers.php"><i class="fa fa-arrow-left"></i> Cancel</a><center><b>MODIFY USER</b></center></h4>
									
								<form id="modifyuser">
									<input type="hidden" name="modifyid" value="<?php echo $userid;?>">
								
							<!-- BASIC SETTINGS -->
								<div class="panel text-left" style="margin-top: 20px; padding: 0px 30px">
									<div class="form-group">
										<label>AGENT ID: </label>
										<span style="padding-left:20px; font-size: 20;"><?php echo $userid;?></span>
									</div>
									<div class="form-group">
										<label for="fullname">Fullname</label>
										<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->full_name[$i];?>">
									</div>
									<div class="form-group">
										<label for="email">Email</label>
										<input type="text" class="form-control" name="email" id="email" value="<?php echo $output->email[$i];?>">
									</div>
									<div class="row">
										<label for="usergroup" class="col-md-5">User Group
										<select class="form-control" id="usergroup" name="usergroup">
											<?php
												$usergroup = NULL;

												if($output->user_group[$i] == "AGENTS"){
													$usergroup .= '<option value="AGENTS" selected>GOAUTODIAL AGENTS</option>';
												}else{
													$usergroup .= '<option value="AGENTS" >GOAUTODIAL AGENTS</option>';
												}

												if($output->user_group[$i] == "ADMIN"){
													$usergroup .= '<option value="ADMINISTRATORS" selected>GOAUTODIAL ADMINISTRATORS</option>';
												}else{
													$usergroup .= '<option value="ADMINISTRATORS" >GOAUTODIAL ADMINISTRATORS</option>';
												}												
												
												if($output->user_group[$i] == "SUPERVISOR"){
													$usergroup .= '<option value="SUPERVISOR" selected>SUPERVISOR</option>';
												}else{
													$usergroup .= '<option value="SUPERVISOR" >SUPERVISOR</option>';
												}
												echo $usergroup;
											?>
										</select>
										</label>
									</div>
									<div class="row">
										<label for="status" class="col-md-3">Status
										<select class="form-control" name="status" id="status">
										<?php
											$status = NULL;
											if($output->active[$i] == "Y"){
												$status .= '<option value="Y" selected> Active </option>';
											}else{
												$status .= '<option value="Y" > Active </option>';
											}
											
											if($output->active[$i] == "N" || $output->active[$i] == NULL){
												$status .= '<option value="N" selected> Inactive </option>';
											}else{
												$status .= '<option value="N" > Inactive </option>';
											}
											echo $status;
										?>
										</select>
										</label>
									</div>
									<div class="row">
										<label for="userlevel" class="col-md-2">User Level
										<select class="form-control" name="userlevel" id="userlevel">
										<?php
											$userlevel = NULL;
												if($output->user_level[$i] == "1"){
													$userlevel .= '<option value="1" selected> 1 </option>';
												}else{
													$userlevel .= '<option value="1" > 1 </option>';
												}
												if($output->user_level[$i] == "2"){
													$userlevel .= '<option value="2" selected> 2 </option>';
												}else{
													$userlevel .= '<option value="2" > 2 </option>';
												}
												if($output->user_level[$i] == "3"){
													$userlevel .= '<option value="3" selected> 3 </option>';
												}else{
													$userlevel .= '<option value="3" > 3 </option>';
												}
												if($output->user_level[$i] == "4"){
													$userlevel .= '<option value="4" selected> 4 </option>';
												}else{
													$userlevel .= '<option value="4" > 4 </option>';
												}
												if($output->user_level[$i] == "5"){
													$userlevel .= '<option value="5" selected> 5 </option>';
												}else{
													$userlevel .= '<option value="5" > 5 </option>';
												}
												if($output->user_level[$i] == "6"){
													$userlevel .= '<option value="6" selected> 6 </option>';
												}else{
													$userlevel .= '<option value="6" > 6 </option>';
												}
												if($output->user_level[$i] == "7"){
													$userlevel .= '<option value="7" selected> 7 </option>';
												}else{
													$userlevel .= '<option value="7" > 7 </option>';
												}
												if($output->user_level[$i] == "8"){
													$userlevel .= '<option value="8" selected> 8 </option>';
												}else{
													$userlevel .= '<option value="8" > 8 </option>';
												}
												if($output->user_level[$i] == "9"){
													$userlevel .= '<option value="9" selected> 9 </option>';
												}else{
													$userlevel .= '<option value="9" > 9 </option>';
												}
											echo $userlevel;
										?>
											
										</select>
										</label>
									</div>
									<div class="form-group">
										<label for="phone_login">Phone Login</label>
										<input type="text" class="form-control" name="phone_login" id="phone_login" value="<?php echo $output->full_name[$i];?>">
									</div>
									<div class="form-group">
										<label for="phone_password">Phone Password</label>
										<input type="text" class="form-control" name="phone_password" id="phone_password" value="<?php echo $output->full_name[$i];?>">
									</div>									
									<div class="row">
										<label for="voicemail" class="col-md-5">Voicemail
										<select class="form-control" name="voicemail" id="voicemail">
										</select>
										</label>
									</div>
									<div class="row">
										<label for="change_pass" class="col-md-2">Change Password?
										<select class="form-control" name="change_pass" id="change_pass">
											<option value="N" selected> No </option>
											<option value="Y" > Yes </option>
										</select>
										</label>
									</div>
									<div class="form-group" id="form_password" style="display:none;">
										<label for="password">Password</label>
										<input type="text" class="form-control" name="password" id="password" value="<?php echo $output->password[$i];?>">
									</div>

									<div class="row" id="btn_show">
										<br/>
										<center>
										<a class="btn btn-app" style="padding:6px 20px; width:95%; height: 45px;" id="show_advanced_settings" >
							               <div id="show"><i class="fa fa-plus"></i></div>
							               <div id="hide" hidden><i class="fa fa-minus"></i></div>
							                Advanced Settings
							            </a>
							            </center>
						       		</div>
						       		
						       		<!-- ADVANCED SETTINGS -->
						       		<div id="advanced_settings_wrapper" style="padding: 25px 0px;" hidden>
						       			<input type="hidden" name="agent_choose_ingroup" value="0">
						       			<input type="hidden" name="agent_choose_blended" value="0">
						       			<input type="hidden" name="scheduled_callbacks" value="1">
						       			<input type="hidden" name="agent_call_manual" value="1">
						       			<div class="row">
											<label for="hotkeys" class="col-md-2">HotKeys
											<select class="form-control" name="hotkeys" id="hotkeys">
											<?php
												$status = NULL;
												if($output->hot_keys[$i] == "1"){
													$status .= '<option value="1" selected> Active </option>';
												}else{
													$status .= '<option value="1" > Active </option>';
												}
												
												if($output->hot_keys[$i] == "0" || $output->hot_keys[$i] == NULL){
													$status .= '<option value="0" selected> Inactive </option>';
												}else{
													$status .= '<option value="0" > Inactive </option>';
												}
												echo $status;
											?>
												
											</select>
											</label>
										</div>
										<div class="row">
											<label for="agent_recordings" class="col-md-2">Agent Recordings
											<select class="form-control" name="agent_recordings" id="agent_recordings">
												<option value="0"> 0 </option>
											</select>
											</label>
										</div>
										<div class="row">
											<label for="agent_transfers" class="col-md-2">Agent Transfers
											<select class="form-control" name="agent_transfers" id="agent_transfers">
												<option value="1"> 1 </option>
											</select>
											</label>
										</div>
										<div class="row">
											<label for="closer_default_blended" class="col-md-2">Closer Default Blended
											<select class="form-control" name="closer_default_blended" id="closer_default_blended">
												<option value="1"> 1 </option>
											</select>
											</label>
										</div>    			
						       		</div>
									
									<br/>
								</div>
										
								<div id="modifyUSERresult"></div>
								<div class="row" style="padding:0px 50px;">
									<button type="button" class="btn btn-danger" id="modifyUSERDeleteButton" href=""><i class="fa fa-times"></i> Delete</button>

									<button type="submit" class="btn btn-primary pull-right" id="modifyUserOkButton" href=""><i class="fa fa-check"></i> Update</button>
								</div>
								
								</form>								
								
							</div>

						<?php
							}
						} else {
						# An error occured
							echo $output->result;
						}
                        
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
					
					?>
					
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			
            <?php print $ui->creamyFooter(); ?>
			
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {

				$("#show_advanced_settings").click(function(){
				    $("#advanced_settings_wrapper").toggle();
				    if ($('#hide').is(":hidden")){
				    	$("#hide").show();
				   		$("#show").hide();
				    }else{
				    	$("#show").show();
				   		$("#hide").hide();
				    }
				});

				$('#change_pass').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "Y") {
					  $('#form_password').show();
					}
					if(this.value == "N") {
					  $('#form_password').hide();
					}
				});

				/** 
				 * Modifies a telephony user
			 	 */
				//$("#modifycustomerform").validate({
				$("#modifyuser").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyUser.php", //post
							$("#modifyuser").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyUSERresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyUSERresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Deletes a customer
				 */
				 $("#modifyUSERDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var userid = $(this).attr('href');
						$.post("./php/DeleteTelephonyUser.php", { userid: userid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("user_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_user"); ?>: "+data); }
						});
					}
				 });
				 
			});
		</script>

    </body>
</html>
