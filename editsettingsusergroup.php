<?php

	###################################################
	### Name: editsettingsusergroups.php 			###
	### Functions: Edit Usergroups 			 		###
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

$usergroup_id = NULL;
if (isset($_POST["usergroup_id"])) {
	$usergroup_id = $_POST["usergroup_id"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit User Group</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       	
       	<?php print $ui->standardizedThemeCSS(); ?> 

        <?php print $ui->creamyThemeCSS(); ?>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("User Group Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["usergroup_id"])){
						?>	
							<li><a href="./settingsusergroups.php"><?php $lh->translateText("User Groups"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

						<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;
					
					//if(isset($extenid)) {
						$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetUserGroupInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["agent_id"] = $usergroup_id; #Desired exten ID. (required)

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
							for($i=0;$i<count($output->user_group);$i++){
					?>

            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
						<legend>MODIFY USER GROUP : <u><?php echo $usergroup_id;?></u></legend>
                    	
							<form id="modifyvoicemail">
								<input type="hidden" name="modifyid" value="<?php echo $usergroup_id;?>">
							
						<!-- Custom Tabs -->
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
										<div class="form-group row mt">
											<label for="group_name" class="col-sm-2 control-label">Group Name</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name (Mandatory)" value="<?php echo $output->group_name[$i];?>">
											</div>
										</div>
										<div class="form-group row">
											<label for="forced_timeclock_login" class="col-sm-2 control-label">Force Timeclock Login</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="forced_timeclock_login" id="forced_timeclock_login">
												<?php
													$forced_timeclock_login = NULL;
													if($output->forced_timeclock_login[$i] == "N"){
														$forced_timeclock_login .= '<option value="N" selected> NO </option>';
													}else{
														$forced_timeclock_login .= '<option value="N" > NO </option>';
													}
													
													if($output->forced_timeclock_login[$i] == "Y"){
														$forced_timeclock_login .= '<option value="Y" selected> YES </option>';
													}else{
														$forced_timeclock_login .= '<option value="Y" > YES </option>';
													}

													if($output->forced_timeclock_login[$i] == "ADMIN_EXEMPT"){
														$forced_timeclock_login .= '<option value="ADMIN_EXEMPT" selected> ADMIN EXEMPT </option>';
													}else{
														$forced_timeclock_login .= '<option value="ADMIN_EXEMPT" > ADMIN EXEMPT </option>';
													}
													echo $forced_timeclock_login;
												?>
												</select>
											</div>
										</div>
								
										<div class="form-group row">
											<label for="shift_enforcement" class="col-sm-2 control-label">Shift Enforcement</label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="shift_enforcement" name="shift_enforcement">
													<?php
														$shift_enforcement = NULL;

														if($output->shift_enforcement[$i] == "OFF" || $output->shift_enforcement[$i] == ""){
															$shift_enforcement .= '<option value="OFF" selected> OFF </option>';
														}else{
															$shift_enforcement .= '<option value="OFF" > OFF </option>';
														}
														
														if($output->shift_enforcement[$i] == "START"){
															$shift_enforcement .= '<option value="START" selected> START </option>';
														}else{
															$shift_enforcement .= '<option value="START" > START </option>';
														}
					                                    
					                                    if($output->shift_enforcement[$i] == "ALL"){
															$shift_enforcement .= '<option value="ALL" selected> ALL </option>';
														}else{
															$shift_enforcement .= '<option value="ALL" > ALL </option>';
														}
					                                    
														echo $shift_enforcement;
													?>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label for="group_level" class="col-sm-2 control-label">Group Level</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="group_level" id="group_level">
												<?php
													$group_level = NULL;
													for($o=1; $o <= 9; $o++){
														if($output->group_level[$i] == $o){
															$group_level .= '<option value="'.$o.'" selected> '.$o.' </option>';
														}else{
															$group_level .= '<option value="'.$o.'"> '.$o.' </option>';
														}
													}

													echo $group_level;
												?>
												</select>
											</div>
										</div>
										<div class="form-group row">
											<label for="group_list_id" class="col-sm-2 control-label">Group List ID</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="group_list_id" id="group_list_id" placeholder="Group List ID" value="<?php echo $output->group_list_id[$i];?>" readonly>
											</div>
										</div>
									</fieldset>
								</div><!-- tab 1 -->

			                    <!-- FOOTER BUTTONS -->
			                    <fieldset class="footer-buttons">
			                        <div class="box-footer">
			                           <div class="col-sm-3 pull-right">
												<a href="settingsusergroups.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
			                           	
			                                	<button type="submit" class="btn btn-primary" id="modifyUserGroupOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
											
			                           </div>
			                        </div>
			                    </fieldset>

				            	</div><!-- end of tab content -->
	                    	</div><!-- tab panel -->
	                    </form>
	                </div><!-- body -->
	            </div>
            </section>
					<?php
							}
						}	
                        
					?>
					
				<!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>

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
				$("#modifyvoicemail").validate({
                	submitHandler: function() {
						//submit the form
							$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
							$('#modifyUserGroupOkButton').prop("disabled", true);

							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyUsergroup.php", //post
							$("#modifyvoicemail").serialize(), 
								function(data){
									//if message is sent
									if (data == 1) {
										swal("Success!", "Usergroup Successfully Updated!", "success");
                                        window.setTimeout(function(){location.reload()},2000);
                                        $('#update_button').html("<i class='fa fa-check'></i> Update");
                                        $('#modifyUserGroupOkButton').prop("disabled", false);
									} else {
										sweetAlert("Oops...", "Something went wrong! "+data, "error");
										$('#update_button').html("<i class='fa fa-check'></i> Update");
										$('#modifyUserGroupOkButton').prop("disabled", false);
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
