<?php	

	###################################################
	### Name: telephonyusers.php 	   ###
	### Functions: Manage Users 	   ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	   ###
	### Version: 4.0 	   ###
	### Written by: Alexander Jim H. Abenoja	   ###
	### License: AGPLv2	   ###
	###################################################

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
        <title>Users</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>

        <?php print $ui->creamyThemeCSS(); ?>

		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	
    	<!-- Wizard Form style -->
		<link href="css/style.css" rel="stylesheet" type="text/css" />

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		
        <!-- CHOSEN-->
   		<link rel="stylesheet" href="theme_dashboard/chosen_v1.2.0/chosen.min.css">
        <!-- SELECT2-->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
   		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>

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
                        <small><?php $lh->translateText("users_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("users"); ?>
                    </ol>
                </section>
		
                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                    	<div class="panel-body">
                    		<legend><?php $lh->translateText("users"); ?></legend>

                    		<div role="tabpanel">
								<ul role="tablist" class="nav nav-tabs nav-justified">

								<!-- Users panel tab -->
									 <li role="presentation" class="active">
										<a href="#users_tab" aria-controls="users_tab" role="tab" data-toggle="tab" class="bb0">
										    Users </a>
									 </li>
									 <?php
										 if(isset($_SESSION['use_webrtc']) && $_SESSION['use_webrtc'] == 0){
									 ?>
									 
									 <!-- Phones panel tabs-->
									 <li role="presentation" >
										<a href="#phone_tab" aria-controls="phone_tab" role="tab" data-toggle="tab" class="bb0">
										    Phones</a>
									 </li>
									 
									 <?php	
										}
									 ?>
								

								  </ul>

								<!-- Tab panes-->
								<div class="tab-content bg-white">
									<!--==== users ====-->
									<div id="users_tab" role="tabpanel" class="tab-pane active">
										<?php print $ui->goGetAllUserList($_SESSION['user']); ?>
			                        </div>
									
									<?php
										if(isset($_SESSION['use_webrtc']) && $_SESSION['use_webrtc'] == 0){
									?>
									<!--==== Phones ====-->
									<div id="phone_tab" role="tabpanel" class="tab-pane">
										<?php print $ui->getPhonesList(); ?>
			                        </div>
									<?php
										}
									?>
								</div><!-- END tab content-->

							</div><!-- end of tabpanel -->
	                    </div><!-- /.box-body -->
                    </div><!-- /.box -->
				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
<?php
	if(isset($_SESSION['use_webrtc']) && $_SESSION['use_webrtc'] == 0){
?>
    <!-- FIXED ACTION BUTTON --> 
		<div class="bottom-menu skin-blue">
			<div class="action-button-circle" data-toggle="modal">
				<?php print $ui->getCircleButton("users", "plus"); ?>
			</div>
			<div class="fab-div-area" id="fab-div-area">
				<ul class="fab-ul" style="height: 170px;">
					<li class="li-style"><a class="fa fa-user-plus fab-div-item" data-toggle="modal" data-target="#user-wizard-modal" title="Add User"></a></li><br/>
					<li class="li-style"><a class="fa fa-phone fab-div-item" data-toggle="modal" data-target="#phone-wizard-modal" title="Add Phone"></a></li><br/>
				</ul>
			</div>
		</div>
<?php
	}else{
?>
	<div class="action-button-circle" data-toggle="modal" data-target="#user-wizard-modal">
		<?php print $ui->getCircleButton("calls", "user-plus"); ?>
	</div>
<?php
	}
?>
	
<!-- MODALS -->
<?php
	$output = $ui->API_goGetAllUserLists();
	$user_groups = $ui->API_goGetUserGroupsList();
	$phones = $ui->API_getPhonesList();
	$max = max($phones->extension);
	$suggested_extension = $max + 1;
?>
	<!-- ADD USER MODAL -->
	    <div class="modal fade" id="user-wizard-modal" aria-labelledby="T_User" >
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
					
					<div class="modal-header">
						
						<h4 class="modal-title animated bounceInRight" id="T_User">
							<i class="fa fa-info-circle" title="A step by step wizard that allows you to create users."></i> 
							<b>User Wizard » Add New User</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</h4>
					</div>
					<div class="modal-body" style="overflow:hidden;">
					
					<form id="wizard_form" action="#">
						<div class="row">
							<!--
	                        <h4>Getting Started
	                           <br>
	                           <small>Assign User to a Usergroup</small>
	                        </h4>
	                        <fieldset>
							
								<div class="form-group">		
									<label class="col-sm-5 control-label">Current Users </label>
									<div class="col-sm-7 mb">
										<div class="row">
											<h4 style="padding-left:20px;"><?php echo count($output->user); ?></h4>
										</div>
									</div>
								</div>
							-->
							<!-- ENABLE IF ADD MULTIPLE IS AVAILABLE 
								<div class="form-group">		
									<label class="col-sm-4 control-label" style="padding-top:15px;">Additional Seat(s): </label>
									<div class="col-sm-8" style="padding-top:10px;">
										<select name="seats" id="seats" class="form-control">
										<?php
											for($i=1; $i <= 9; $i++){ 
										?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php
											}
										?>
											<option value="custom_seats">Custom</option>
										</select>
									</div>
								</div>
								<div class="form-group" id="custom_seats" style="display:none;">
									<label class="col-sm-4 control-label" for="custom_num_seats">Number of Seats: </label>
									<div class="col-sm-6 mb">
										<input type="number" name="custom_num_seats" id="custom_num_seats" class="form-control" min="1" max="99" value="1">
									</div>
								</div>
							-->
	                        </fieldset>
	                        <h4>Account Details
	                           <br>
	                           <small>Assign then Enter Account and Login Details</small>
	                        </h4>
	                        <fieldset>
	                           <?php
								$agent_num = $output->last_count;

								$num_padded = sprintf("%03d", $agent_num);
								
								$fullname = "Agent ".$num_padded;
								$user_id_for_form = "agent".$num_padded;
								?>
								
								<div class="form-group">		
									<label class="col-sm-4 control-label"> Users ID </label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="user_form" id="user_form" placeholder="User ID (Mandatory)" 
											value="<?php echo $user_id_for_form;?>" title="Please enter alphanumeric characters only" maxlength="20" required>
										<label id="user-duplicate-error"></label>
									</div>
								</div>
								<div class="form-group mt">
									<label class="col-sm-4 control-label" for="user_group">User Group</label>
									<div class="col-sm-8 mb">
										<select id="user_group" class="form-control select2-1" name="user_group" style="width:100%;">
											<?php
												for($i=0;$i<count($user_groups->user_group);$i++){
											?>
												<option value="<?php echo $user_groups->user_group[$i];?>" <?php if($user_groups->user_group[$i] == "AGENT"){echo "selected";}?>>  <?php echo $user_groups->group_name[$i];?>  </option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group" id="phone_logins_form" style="display:none;">
									<label class="col-sm-4 control-label" for="phone_logins"> Phone Login </label>
									<div class="col-sm-8 mb">
										<input type="number" name="phone_logins" id="phone_logins" class="form-control" minlength="3" placeholder="Phone Login (Mandatory)" 
											value="<?php echo $output->last_phone_login;?>" pattern=".{3,}" title="Minimum of 3 characters" maxlength="20" required>
										<label id="phone_login-duplicate-error"></label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="fullname"> Fullname </label>
									<div class="col-sm-8 mb">
										<input type="text" name="fullname" id="fullname" class="form-control" placeholder="FullName (Mandatory)"
											   value="<?php echo $fullname;?>" title="Please enter alphanumeric characters only" maxlength="50" required>
									</div>
								</div>
								<div class="form-group">	
									<label class="col-sm-4 control-label" for="password"><i class="fa fa-info-circle" title="Default Password is: Go<?php echo date('Y');?>"></i>  Password </label>
									
									<div class="col-sm-8 mb">
										<input type="password" class="form-control" name="password" id="password" placeholder="Password (Mandatory)" value="Go<?php echo date('Y');?>" maxlength="20" required>
									</div>
								</div>
								<div class="form-group">		
									<label class="col-sm-4 control-label" for="confirm"> Confirm Password </label>
									<div class="col-sm-8 mb">
										<input type="password" class="form-control" id="confirm" name="confirm" placeholder="Re-enter password here (Mandatory)" value="Go2016" required>
									</div> 
								</div>
								<!--
								<div class="row">
									<p class="col-sm-12"><small class="pull-right" style="padding-right:20px;"><i><span id="pass_result" class="control-label"></span></i></small></p>
								</div>-->
								<input type="hidden" name="phone_pass" id="phone_pass" class="form-control">

								<div class="form-group">
									<label class="col-sm-4 control-label" for="status">Active </label>
									<div class="col-sm-8 mb">
										<select name="status" id="status" class="form-control">
											<option value="Y" selected>Yes</option>
											<option value="N" >No</option>						
										</select>
									</div>
								</div>
	                        </fieldset>
	                        <h4>Review & Submit
	                           <br>
	                           <small>Review Details Before Saving</small>
	                        </h4>
	                        <fieldset>
	                        	<div class="form-group">
	                           		<label class="col-lg-6 control-label">User Group: </label>
	                           		<div class="col-lg-6 reverse_control_label mb">
	                           			<span id="submit-usergroup"></span>
	                           		</div>
	                           	</div>
	                           	<div class="form-group">
	                           		<label class="col-lg-6 control-label">User ID: </label>
	                           		<div class="col-lg-6 reverse_control_label mb">
	                           			<span id="submit-userid"></span>
	                           		</div>
	                           	</div>
	                           	<div class="form-group">
	                           		<label class="col-lg-6 control-label">Full Name: </label>
	                           		<div class="col-lg-6 reverse_control_label mb">
	                           			<span id="submit-fullname"></span>
	                           		</div>
	                           	</div>
	                           	<div class="form-group">
	                           		<label class="col-lg-6 control-label">Password: </label>
	                           		<div class="col-lg-6 reverse_control_label mb">
	                           			<span id="submit-password"></span>
	                           		</div>
	                           	</div>
	                           	<div class="form-group">
	                           		<label class="col-lg-6 control-label">Active: </label>
	                           		<div class="col-lg-6 reverse_control_label mb">
	                           			<span id="submit-active"></span>
	                           		</div>
	                           	</div>

	                        </fieldset>
	                     </div>
					</form>
			
					</div> <!-- end of modal body -->
				</div>
			</div>
		</div>
	<!-- end of add user modal -->

 <?php
	if(isset($_SESSION['use_webrtc']) && $_SESSION['use_webrtc'] == 0){
?>
	<!-- ADD PHONE MODAL -->
	    <div class="modal fade" id="phone-wizard-modal" aria-labelledby="T_Phones" >
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
					<div class="modal-header">
						
						<h4 class="modal-title animated bounceInRight" id="T_Phones">
							<i class="fa fa-info-circle" title="A step by step wizard that allows you to create phones."></i> 
							<b>Phone Wizard » Add New Phone</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</h4>
					</div>
					<div class="modal-body wizard-content">
					
					<form name="create_form" id="create_form" role="form">
						<div class="row">
							<h4>
								Add Phones <br/>
								<small>Specify a number of phones to be added</small>
							</h4>
							<fieldset>
								<div class="form-group mt">
									<label class="col-sm-4 control-label" for="add_phones">Additional Phone(s):</label>
									<div class="col-sm-6 mb">
										<select class="form-control" name="add_phones" id="add_phones">
											<option value="1"> 1 </option>
											<option value="2"> 2 </option>
											<option value="3"> 3 </option>
											<option value="4"> 4 </option>
											<option value="5"> 5 </option>
											<option value="CUSTOM">CUSTOM</option>
										</select>
									</div>
									<div class="col-sm-2" id="custom_seats" style="display:none;">
										<input type="number" class="form-control" name="custom_seats" value="1" min="1" max="99" required>
									</div>
								</div>
								<div class="form-group">		
									<label class="col-sm-4 control-label" for="start_ext">Starting Extension</label>
									<div class="col-sm-8 mb">
										<input type="number" name="start_ext" id="start_ext" placeholder="Starting Phone Extension" value="<?php echo $output->last_phone_login;?>" class="form-control">
									</div>
								</div>
							</fieldset>
					<!-- end of step 1-->
					<!-- STEP 2 -->
							<h4>
								<small></small>
							</h4>
							<fieldset>
								<div class="form-group mt">
									<label class="col-sm-4 control-label" for="phone_ext">Phone Extension/Login</label>
									<div class="col-sm-8 mb">
										<input text="number" name="phone_ext" id="phone_ext" class="form-control" placeholder="Phone Login (Mandatory)" title="Must be 3 - 20 characters and contains only numerical values." minlength="3" maxlength="20" required/>
									</div>
								</div>
								<div class="form-group">		
									<label class="col-sm-4 control-label" for="phone_pass">Phone Login Password</label>
									<div class="col-sm-8 mb">
										<input type="text" value="G016gO" name="phone_pass" id="phone_pass" class="form-control"  placeholder="Phone Password (Mandatory)" title="Default Password is: Go<?php echo date('Y');?>" value="Go<?php echo date('Y');?>" maxlength="20" required>
									</div>
								</div>
								<div class="form-group">		
									<label class="col-sm-4 control-label" for="start_ext">User Group</label>
									<div class="col-sm-8 mb">
										<select name="user_group" id="user_group" class="form-control select2-1" style="width:100%;" required>
											<option value="ALL">ALL USER GROUPS</option>
											<?php
												for($i=0; $i < count($user_groups->user_group); $i++){
											?>
												<option value="<?php echo $user_groups->user_group[$i];?>"> <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i]; ?></option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">		
									<label class="col-sm-4 control-label" for="ip">Server IP	</label>
									<div class="col-sm-8 mb">
										<select name="ip" id="ip" class="form-control" required>
											<option value="69.46.6.35">
												<?php echo $_SERVER['REMOTE_ADDR'];?>
											</option>
										</select>
									</div>
								</div>

								<div class="form-group">		
									<label class="col-sm-4 control-label" for="pfullname">Full Name</label>
									<div class="col-sm-8 mb">
										<input type="text" name="pfullname" id="pfullname" placeholder="Full Name (Mandatory)" class="form-control" required>
									</div>
								</div>
								<div class="form-group">		
									<label class="col-sm-4 control-label" for="gmt">Local GMT	</label>
									<div class="col-sm-8 mb">
										<div class="row">
											<div class="col-sm-6">
												<select name="gmt" id="gmt" class="form-control" required>
													<option value="12:75"> 12:75 </option>
													<option value="12:00"> 12:00 </option>
													<option value="11:00"> 11:00 </option>
													<option value="10:00"> 10:00 </option>
													<option value="9:50"> 9:50 </option>
													<option value="9:00"> 9:00 </option>
													<option value="8:00"> 8:00 </option>
													<option value="7:00"> 7:00 </option>
													<option value="6:50"> 6:50 </option>
													<option value="6:00"> 6:00 </option>
													<option value="5:75"> 5:75 </option>
													<option value="5:50"> 5:50 </option>
													<option value="5:00"> 5:00 </option>
													<option value="4:50"> 4:50 </option>
													<option value="4:00"> 4:00 </option>
													<option value="3:50"> 3:50 </option>
													<option value="3:00"> 3:00 </option>
													<option value="2:00"> 2:00 </option>
													<option value="1:00"> 1:00 </option>
													<option value="0:00"> 0:00 </option>
													<option value="-1:00"> -1:00 </option>
													<option value="-2:00"> -2:00 </option>
													<option value="-3:00"> -3:00 </option>
													<option value="-4:00"> -4:00 </option>
													<option value="-5:00" selected> -5:00 </option>
													<option value="-6:00"> -6:00 </option>
													<option value="-7:00"> -7:00 </option>
													<option value="-8:00"> -8:00 </option>	
													<option value="-9:00"> -9:00 </option>
													<option value="-10:00"> -10:00 </option>
													<option value="-11:00"> -11:00 </option>
													<option value="-12:00"> -12:00 </option>
												</select>
											</div>
											<div class="col-sm-6">
												<p class="text-muted">( Do NOT adjust for DST)</p>
											</div>
										</div>
									</div>
								</div>
							</fieldset><!-- end of step 2-->
						</div><!-- end of row -->
					</form>

					</div> <!-- end of modal body -->
				</div> <!-- end of modal content -->
			</div> <!-- end of modal dialog -->
		</div>
	<!-- end of add phone modal -->
<?php
	}
?>
<!-- end of modals -->

		<?php print $ui->standardizedThemeJS();?>
		<!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>
  		<!-- SELECT2-->
        <script src="theme_dashboard/select2/dist/js/select2.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		
		/*********************
		** INITIALIZATION
		*********************/

			var checker = 0;

			/* init data tables */

				//users
				$('#T_users').dataTable({
					stateSave: true
				});
				//phones
				$('#T_phones').dataTable({
					stateSave: true
				});

			/* init wizards */
				var uform = $("#wizard_form"); // init user form wizard 
				var pform = $("#create_form"); // init phone form wizard 

			/* enable on hover event for FAB */
				// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			
		/*********
		** Add Wizard
		*********/



			//users form validate
		    uform.validate({
		        errorPlacement: function errorPlacement(error, element) { element.after(error); },
		        rules: {
		            confirm: {
		                equalTo: "#password"
		            }
		        }
		    });

		    //phones form validate
		    pform.validate({
		        errorPlacement: function errorPlacement(error, element) { element.after(error); }
		    });

		    //users
		    uform.children("div").steps({
		        headerTag: "h4",
		        bodyTag: "fieldset",
		        transitionEffect: "slideLeft",
		        onStepChanging: function (event, currentIndex, newIndex)
		        {
		        	// Allways allow step back to the previous step even if the current step is not valid!
			        if (currentIndex > newIndex) {
			        	checker = 0;
			            return true;
			        }

			        console.log(checker);
			        // Disable next if there are duplicates
			        if(checker > 0){
				        $(".body:eq(" + newIndex + ") .error", uform).addClass("error");
			        	return false;
			        }

			        // form review
					show_form_review();

					// Clean up if user went backward before
				    if (currentIndex < newIndex)
				    {
				        // To remove error styles
				        $(".body:eq(" + newIndex + ") label.error", uform).remove();
				        $(".body:eq(" + newIndex + ") .error", uform).removeClass("error");
				    }

		            uform.validate().settings.ignore = ":disabled,:hidden";
		            return uform.valid();
		        },
		        onFinishing: function (event, currentIndex)
		        {
		            uform.validate().settings.ignore = ":disabled";
		            return uform.valid();
		        },
		        onFinished: function (event, currentIndex)
		        {

		        	$('#finish').text("Loading...");
		        	$('#finish').attr("disabled", true);

		            // Submit form via ajax
		            $.ajax({
						url: "./php/CreateTelephonyUser.php",
						type: 'POST',
						data: $("#wizard_form").serialize(),
						success: function(data) {
						  // console.log(data);
							  if(data == 1){
							  	  swal(
									{
										title: "Success",
										text: "User Successfully Created!",
										type: "success"
									},
									function(){
										window.location.href = 'telephonyusers.php';
									}
								  );
							  }else{
							  	  sweetAlert("Oops...", "Something went wrong. "+data, "error");
							  	  $('#finish').val("Submit");
    							  $('#finish').attr("disabled", false);
							  }
						}
					});
		        }
		    });
			
			//phones
			pform.children("div").steps({
		        headerTag: "h4",
		        bodyTag: "fieldset",
		        transitionEffect: "slideLeft",
		        onStepChanging: function (event, currentIndex, newIndex)
		        {
		        	$("#phone_ext").val($("#start_ext").val());

		        	// Allways allow step back to the previous step even if the current step is not valid!
			        if (currentIndex > newIndex) {
			            return true;
			        }

					// Clean up if user went backward before
				    if (currentIndex < newIndex)
				    {
				        // To remove error styles
				        $(".body:eq(" + newIndex + ") label.error", pform).remove();
				        $(".body:eq(" + newIndex + ") .error", pform).removeClass("error");
				    }

		            pform.validate().settings.ignore = ":disabled,:hidden";
		            return pform.valid();
		        },
		        onFinishing: function (event, currentIndex)
		        {
		            pform.validate().settings.ignore = ":disabled";
		            return pform.valid();
		        },
		        onFinished: function (event, currentIndex)
		        {

		        	$('#finish').text("Loading...");
		        	$('#finish').attr("disabled", true);

		        	/*********
		        	** ADD EVENT
		        	*********/

		            // Submit form via ajax
			            $.ajax({
							url: "./php/AddSettingsPhones.php",
							type: 'POST',
							data: $("#create_form").serialize(),
							success: function(data) {
							  // console.log(data);
								  if(data == 1){
										swal({title: "Success",text: "Phone Successfully Created!",type: "success"},function(){window.location.href = 'settingsphones.php';});
										$('#finish').val("Submit");
										$('#finish').attr("disabled", false);
								  }else{
									  sweetAlert("Oops...", "Something went wrong! "+data, "error");
								  	  $('#finish').val("Submit");
								  	  $('#finish').attr("disabled", false);
								  }
							}
						});
		        }
		    });

	//--------------------

		/*********
		** Edit Event
		*********/
			//user edit event
				$(document).on('click','.edit-T_user',function() {
					var url = 'edittelephonyuser.php';
					var userid = $(this).attr('data-id');
					var role = $(this).attr('data-role');
					//alert(userid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="userid" value="'+userid+'" /><input type="hidden" name="role" value="'+role+'"></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
			//phone edit event
				$(document).on('click','.edit-phone',function() {
					var url = './editsettingsphones.php';
					var extenid = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="extenid" value="'+extenid+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

	//--------------------

		/*********
		** Emergency Logout Event
		*********/
			//user edit event
				$(document).on('click','.emergency-logout',function() {
					var userid = $(this).attr('data-emergency-logout-username');
					var name = $(this).attr('data-name');
					swal({   
	                	title: "Emergency Log Out : " + name,
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Log Out this agent",   
	                	cancelButtonText: "Cancel Emergency Log Out",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									type: 'POST',
									url: "php/emergency_logout.php",
									data: {goUserAgent: userid},
									cache: false,
									//dataType: 'json',
									success: function(data){
										if(data == "success"){
											sweetAlert("Agent Logged Out Successfully", "", "success");
										}else{
											sweetAlert("Emergency Logout",data, "warning");
										}
									}
								}); 
							} else {     
	                			swal("Cancelled", "No action has been done :)", "error");   
	                		} 
	                	}
	                );
					
				});
				
	// ------------------
				
		/*********
		** Delete Event
		*********/
			//delete user 
				 $(document).on('click','.delete-T_user',function() {
				 	var id = $(this).attr('data-id');
		                swal({   
		                	title: "Are you sure?",   
		                	text: "This action cannot be undone.",   
		                	type: "warning",   
		                	showCancelButton: true,   
		                	confirmButtonColor: "#DD6B55",   
		                	confirmButtonText: "Yes, delete this user!",   
		                	cancelButtonText: "No, cancel please!",   
		                	closeOnConfirm: false,   
		                	closeOnCancel: false 
		                	}, 
		                	function(isConfirm){   
		                		if (isConfirm) { 
		                			$.ajax({
										url: "./php/DeleteTelephonyUser.php",
										type: 'POST',
										data: { 
											userid:id,
										},
										success: function(data) {
										console.log(data);
									  		if(data == 1){
									  			swal({title: "Deleted",text: "User Successfully Deleted!",type: "success"},function(){window.location.href = 'telephonyusers.php';});
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
			
			//delete phone
				$(document).on('click','.delete-phone',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                    title: "Are you sure?",   
	                    text: "This action cannot be undone.",   
	                    type: "warning",   
	                    showCancelButton: true,   
	                    confirmButtonColor: "#DD6B55",   
	                    confirmButtonText: "Yes, delete this phones!",   
	                    cancelButtonText: "No, cancel please!",   
	                    closeOnConfirm: false,   
	                    closeOnCancel: false 
	                    }, 
	                    function(isConfirm){   
	                        if (isConfirm) { 
	                        	$.ajax({
								  url: "./php/DeleteSettingsPhones.php",
								  type: 'POST',
								  data: { 
								  	exten_id :id,
								  },
								  success: function(data) {
								  		// console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Phone Successfully Deleted!", "success");
											window.setTimeout(function(){location.reload()},3000)
										}else{
											sweetAlert("Oops...", "Something went wrong!"+data, "error");
										}
								    }
								});
	                        } else {     
	                                swal("Cancelled", "No action has been done :)", "error");   
	                        } 
	                    }
	                );
				});

	// -------------------------
		

		// disable special characters on User ID
		$('#user_form').bind('keypress', function (event) {
		    var regex = new RegExp("^[a-zA-Z0-9]+$");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});

		// disable special characters on Fullname for Users
		$('#fullname').bind('keypress', function (event) {
		    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});

		// disable special characters on phone extension
			$('#phone_ext').bind('keypress', function (event) {
			    var regex = new RegExp("^[0-9]+$");
			    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
			    if (!regex.test(key)) {
			       event.preventDefault();
			       return false;
			    }
			});

		// disable special characters on Fullname for Phones
			$('#pfullname').bind('keypress', function (event) {
			    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
			    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
			    if (!regex.test(key)) {
			       event.preventDefault();
			       return false;
			    }
			});

		/*********
		** validations
		*********/
		// check duplicates
			$("#user_form").keyup(function() {
				clearTimeout($.data(this, 'timer'));
				var wait = setTimeout(validate_user, 500);
				$(this).data('timer', wait);
			});

			function validate_user(){
				var user_form_value = $('#user_form').val();
				var phone_logins_value = "";
		        if(user_form_value != ""){
				    $.ajax({
					    url: "php/checkUser.php",
					    type: 'POST',
					    data: {
					    	user : user_form_value,
					    	phone_login : phone_logins_value
					    },
						success: function(data) {
							console.log(data);
							if(data == "success"){
								checker = 0;
								$( "#user_form" ).removeClass("error");
								$( "#user-duplicate-error" ).text( "User ID is available." ).removeClass("error").addClass("avail");
							}else{
								if(data == "user"){
									$( "#user_form" ).removeClass("valid").addClass( "error" );
									$( "#user-duplicate-error" ).text( "There are 1 or more users with this User ID." ).removeClass("avail").addClass("error");
								}
								
								checker = 1;
							}
						}
					});
				}
			}

		// form review
		function show_form_review(){
			$('#submit-usergroup').text($('#user_group').val());
			$('#submit-userid').text($('#user_form').val());
			$('#submit-fullname').text($('#fullname').val());
			$('#submit-password').text("******");

			if($('#status').val() == "Y"){
				$('#submit-active').text("YES");
			}else{
				$('#submit-active').text("NO");
			}
		}

	// -------------------------

		/*********
		** On Action Events
		*********/

		/* additional number custom */
			$('#seats').on('change', function() {
				if(this.value == "custom_seats") {
				  $('#custom_seats').show();
				}
				if(this.value != "custom_seats") {
				  $('#custom_seats').hide();
				}
			});

		/* initialize select2 */
			$('.select2-1').select2({
		        theme: 'bootstrap'
		    });
		   
		    //document.on("jqueryui-configure-dialog", function(e) { e.allowInteraction.push(".select2-1"); });
		
	});
	
</script>
		
		<?php print $ui->creamyFooter();?>
    </body>
</html>
