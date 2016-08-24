<?php	

	###################################################
	### Name: telephonyusers.php 					###
	### Functions: Manage Users 			 		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
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
	                        <div class="table" id="users_table">
	                        <legend><?php $lh->translateText("users"); ?></legend>
								<?php print $ui->goGetAllUserList(); ?>
	                        </div>
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
        </div><!-- ./wrapper -->
	
	<div class="action-button-circle" data-toggle="modal" data-target="#wizard-modal">
		<?php print $ui->getCircleButton("calls", "user-plus"); ?>
	</div>
	
<!-- MODAL -->
<?php
	$output = $ui->API_goGetAllUserLists();
	$user_groups = $ui->API_goGetUserGroupsList();
	$phones = $ui->API_getPhonesList();
?>

    <div class="modal fade" id="wizard-modal" tabindex="-1"aria-labelledby="T_User" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="T_User">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create users."></i> 
						<b>User Wizard » Add New User</b></h4>
				</div>
				<div class="modal-body" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form id="wizard_form" action="#">
					<div class="row">
                        <h4>Getting Started
                           <br>
                           <small>Assign User to a Usergroup</small>
                        </h4>
                        <fieldset>
                           <div class="form-group mt">
								<label class="col-sm-5 control-label" for="user_group">User Group</label>
								<div class="col-sm-7 mb">
									<select id="user_group" class="form-control" name="user_group">
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-5 control-label">Current Users </label>
								<div class="col-sm-7 mb">
									<div class="row">
										<h4 style="padding-left:20px;"><?php echo count($output->user); ?></h4>
									</div>
								</div>
							</div>
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
							<div class="form-group">
								<label class="col-sm-5 control-label" for="generate_phone_logins">Generate Phone Logins </label>
								<div class="col-sm-7 mb">
									<select id="generate_phone_logins" name="generate_phone_logins" class="form-control">
										<option value="N" selected>No</option>
										<option value="Y">Yes</option>
									</select>
								</div>
							</div>
                        </fieldset>
                        <h4>Account Details
                           <br>
                           <small>Account and Login Details</small>
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
									<input type="text" class="form-control" name="user_form" id="user_form" placeholder="User ID (Mandatory)" value="<?php echo $user_id_for_form;?>" required>
									<label id="user-duplicate-error"></label>
								</div>
							</div>
							<div class="form-group" id="phone_logins_form" style="display:none;">
								<label class="col-sm-4 control-label" for="phone_logins"> Phone Login </label>
								<div class="col-sm-8 mb">
									<input type="number" name="phone_logins" id="phone_logins" class="form-control" minlength="3" placeholder="Phone Login (Mandatory)" value="<?php echo $output->last_phone_login;?>" pattern=".{3,}" title="Minimum of 3 characters" required>
									<label id="phone_login-duplicate-error"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="fullname"> Fullname </label>
								<div class="col-sm-8 mb">
									<input type="text" name="fullname" id="fullname" class="form-control" placeholder="FullName (Mandatory)"
										   value="<?php echo $fullname;?>" required>
								</div>
							</div>
							<div class="form-group">		
								<label class="col-sm-4 control-label" for="password"> Password </label>
								<div class="col-sm-8 mb">
									<input type="password" class="form-control" name="password" id="password" placeholder="Default Password is: Go2016 (Mandatory)" value="Go2016" required>
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
                           		<label class="col-lg-6 control-label">Phone Login: </label>
                           		<div class="col-lg-6 reverse_control_label mb">
                           			<span id="submit-phonelogin"></span>
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
	</div><!-- end of modal -->

		<?php print $ui->standardizedThemeJS();?>
		<!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>
		
<script type="text/javascript">

	$(document).ready(function() {
		var checker = 0;

		// initialize data table
		$('#T_users').dataTable();

		/*********
		** Add Wizard
		*********/
		    var form = $("#wizard_form"); // init form wizard 

		    form.validate({
		        errorPlacement: function errorPlacement(error, element) { element.after(error); },
		        rules: {
		            confirm: {
		                equalTo: "#password"
		            }
		        }
		    });

		    form.children("div").steps({
		        headerTag: "h4",
		        bodyTag: "fieldset",
		        transitionEffect: "slideLeft",
		        onStepChanging: function (event, currentIndex, newIndex)
		        {
		        	// Allways allow step back to the previous step even if the current step is not valid!
			        if (currentIndex > newIndex) {
			            return true;
			        }

			        console.log(checker);
			        // Disable next if there are duplicates
			        if(checker > 0){
				        $(".body:eq(" + newIndex + ") .error", form).addClass("error");
			        	return false;
			        }

			        // form review
					show_form_review();

					// Clean up if user went backward before
				    if (currentIndex < newIndex)
				    {
				        // To remove error styles
				        $(".body:eq(" + newIndex + ") label.error", form).remove();
				        $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
				    }

		            form.validate().settings.ignore = ":disabled,:hidden";
		            return form.valid();
		        },
		        onFinishing: function (event, currentIndex)
		        {
		            form.validate().settings.ignore = ":disabled";
		            return form.valid();
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
							  	  swal("Success!", "User Successfully Created!", "success");
								  window.setTimeout(function(){location.reload()},2000);
							  }else{
							  	  sweetAlert("Oops...", "Something went wrong. "+data, "error");
							  	  $('#finish').val("Submit");
    							  $('#finish').attr("disabled", false);
							  }
						}
					});
		        }
		    });

	//--------------------

		/*********
		** Edit user details
		*********/

				$(document).on('click','.edit-T_user',function() {
					var url = 'edittelephonyuser.php';
					var userid = $(this).attr('data-id');
					var role = $(this).attr('data-role');
					//alert(userid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="userid" value="'+userid+'" /><input type="hidden" name="role" value="'+role+'"></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });

	// ------------------
				
		/*********
		** Delete function
		*********/

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
									  			swal("Success!", "User Successfully Deleted!", "success");
									  			window.setTimeout(function(){location.reload()},1000)
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
		

	// -------------------------
		/*********
		** validations
		*********/
		// check duplicates
			$("#user_form").keyup(function() {
				clearTimeout($.data(this, 'timer'));
				var wait = setTimeout(validate_user, 500);
				$(this).data('timer', wait);
			});
			$("#phone_logins").keyup(function() {
				clearTimeout($.data(this, 'timer'));
				var wait = setTimeout(validate_user, 500);
				$(this).data('timer', wait);
			});

			function validate_user(){
				var user_form_value = $('#user_form').val();
				var phone_logins_value = $('#phone_logins').val();
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
								
								$( "#phone_logins" ).removeClass( "error" );
								$( "#phone_login-duplicate-error" ).text( "Phone Login is available." ).removeClass("error").addClass("avail");
							}else{
								if(data == "user"){
									$( "#user_form" ).removeClass("valid").addClass( "error" );
									$( "#user-duplicate-error" ).text( "There are 1 or more users with this User ID." ).removeClass("avail").addClass("error");
								}
									
								if(data == "phone_login"){
									$( "#phone_logins" ).removeClass( "valid" ).addClass( "error" );
									$( "#phone_login-duplicate-error" ).text( "There are 1 or more users with this Phone Login." ).removeClass("avail").addClass( "error" );
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
			$('#submit-phonelogin').text($('#phone_logins').val());
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

		/* user group */
			$('#user_group').on('change', function() {
				if(this.value == "AGENTS" || this.value == "ADMIN") {
					document.getElementById('generate_phone_logins').value = "Y";
					$('#phone_logins_form').show();
				}else{
					document.getElementById('generate_phone_logins').value = "N";
					$('#phone_logins_form').hide();
				}
			});
		
		/* generate phone logins*/
			$('#generate_phone_logins').on('change', function() {
				if(this.value == "Y") {
				  $('#phone_logins_form').show();
				}
				if(this.value == "N") {
				  $('#phone_logins_form').hide();
				}
			});
	});
	
</script>

		<?php print $ui->creamyFooter();?>
    </body>
</html>
