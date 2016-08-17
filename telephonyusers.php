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
    	<link rel="stylesheet" href="css/easyWizard.css">

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
					<h4 class="modal-title animate-header" id="T_User"><b>User Wizard » Add New User</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="CreateTelephonyUser.php" method="POST" id="create_form" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group mt">
							<label class="col-sm-4 control-label" for="user_group">User Group:</label>
							<div class="col-sm-8 mb">
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
							<label class="col-sm-4 control-label">Current Users: </label>
							<div class="col-sm-8 mb">
								<div class="row">
									<h4 style="padding-left:20px;"><?php echo count($output->userno); ?></h4>
								</div>
							</div>
						</div>
					<!-- ENABLE IF ADD MULTIPLE IS READY --
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
					-->
						<div class="form-group" id="custom_seats" style="display:none;">
							<label class="col-sm-4 control-label" for="custom_num_seats">Number of Seats: </label>
							<div class="col-sm-6 mb">
								<input type="number" name="custom_num_seats" id="custom_num_seats" class="form-control" min="1" max="99" value="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="generate_phone_logins">Generate Phone Logins: </label>
							<div class="col-sm-8 mb">
								<select id="generate_phone_logins" name="generate_phone_logins" class="form-control">
									<option value="N" selected>No</option>
									<option value="Y">Yes</option>
								</select>
							</div>
						</div>
						<?php
							$latest_phone = max($phones->extension);
							//var_dump($latest_phone);
							//var_dump($phones->extension);
							$latest_phone = $latest_phone + 1;
							
						?>
						<div class="form-group" id="phone_logins_form" style="display:none;">
							<label class="col-sm-4 control-label" for="phone_logins"> Phone Login: </label>
							<div class="col-sm-8 mb">
								<input type="number" name="phone_logins" id="phone_logins" class="form-control" minlength="3" placeholder="Phone Login. This is a required field" value="<?php echo $latest_phone;?>" pattern=".{3,}" title="Minimum of 3 characters" required>
							</div>
						</div>
					</div>
				
			
				<!-- STEP 2 -->
					<div class="wizard-step">
						<!--
						<div class="form-group mt">
							<label class="control-label col-sm-4">User Group:</label>
							<div class="col-sm-8 mb">
								<span id="display_user_group"></span>
							</div>
						</div>
						-->
						<?php
						$max = count($output->userno);
						$x = 0;
						for($i=0; $i < $max; $i++){
							//echo $max-$x;
							$agent = substr($output->userno[$max-$x], 0, 5);
							if($agent == "agent"){
								$get_last = substr($output->userno[$max-$x], -2);
							}else{
								$x = $x+1;
							}
						}

						$agent_num = $get_last + 1;

						$num_padded = sprintf("%03d", $agent_num);
						
						$fullname = "Agent ".$num_padded;
						$user_id_for_form = "agent".$num_padded;
						?>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label"> Users ID: </label>
							<div class="col-sm-8 mb">
								<input type="text" class="form-control" name="user_form" id="user_form" placeholder="User ID. This is a required field." value="<?php echo $user_id_for_form;?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="fullname"> Fullname: </label>
							<div class="col-sm-8 mb">
								<input type="text" name="fullname" id="fullname" class="form-control" placeholder="FullName. This is a required field."
									   value="<?php echo $fullname;?>">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="password"> Password: </label>
							<div class="col-sm-8 mb">
								<input type="password" class="form-control" name="password" id="password" placeholder="Default Password is: Go2016. This is a required field." value="Go2016" required>
								
							</div> 
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="conf_password"> Confirm Password: </label>
							<div class="col-sm-8 mb">
								<input type="password" class="form-control" id="conf_password" placeholder="Re-enter password here. This is a required field" value="Go2016" required>
							</div> 
						</div>
						<div class="row">
							<p class="col-sm-12"><small class="pull-right" style="padding-right:20px;"><i><span id="pass_result"></span></i></small></p>
						</div>
						<!--
						<div id="phone_div" style="display:none;">
							<div class="form-group">
								<label class="col-sm-4 control-label" for="phone_login1"> Phone Login: </label>
								<div class="col-sm-8 mb">
									<input type="text" readonly name="phone_login1" id="phone_login1" class="form-control">
								</div>
							</div>
						
							<div class="form-group">
								<label class="col-sm-4 control-label" for="phone_pass"> Phone Password: </label>
								<div class="col-sm-8 mb">
									<input type="text" name="phone_pass" id="phone_pass" class="form-control" value="Go2016">
								</div>
							</div>
						
						</div>
						-->
						<input type="hidden" name="phone_pass" id="phone_pass" class="form-control">

						<div class="form-group">
							<label class="col-sm-4 control-label" for="status">Active: </label>
							<div class="col-sm-4 mb">
								<select name="status" class="form-control">
									<option value="Y" selected>Yes</option>		
			
									<option value="N" >No</option>						
								</select>
							</div>
						</div>
					</div><!--end of step2 -->
				</form>
		
				</div> <!-- end of modal body -->

				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
			</div>
		</div>
	</div><!-- end of modal -->

		<?php print $ui->standardizedThemeJS();?>
		<script src="js/easyWizard.js" type="text/javascript"></script> 
		
	<script type="text/javascript">

		function checkPasswordMatch() {
		    var password = $("#password").val();
		    var confirmPassword = $("#conf_password").val();

		    if (password != confirmPassword)
		        $("#pass_result").html("<font color='red'>Passwords Do Not Match! <font size='5'>✖</font> </font>");
		    else
		    	 $("#pass_result").html("<font color='green'>Passwords Match! <font size='5'>✔</font> </font>");
		}

	//-------------------------

			$(document).ready(function() {
				$('#T_users').dataTable();

			// for cancelling
				$(document).on('click', '#cancel', function(){
					swal("Cancelled", "No action has been done :)", "error");
				});

		// for easy wizard 
			var form = document.getElementById('create_form');
			var validate_wizard = 0;
			
			var generate_phone_logins = document.getElementById('generate_phone_logins').value;
			var phone_logins = document.getElementById('phone_logins').value;
			//var phone_pass = document.getElementById('phone_pass').value;

			var password = document.getElementById('password').value;
			var conf_password = document.getElementById('conf_password').value;

			$("#wizard-modal").wizard({

				onnext:function(){		

					var generate_phone_logins = document.getElementById('generate_phone_logins').value;
					var phone_logins = document.getElementById('phone_logins').value;

					if(generate_phone_logins == "Y"){
						//document.getElementById("phone_login1").value = phone_logins;
						$('#phone_div').show();
					}else{
						$('#phone_div').hide();
					}
					/*
					var user_group = document.getElementById('user_group').value;
					if(user_group == "AGENTS"){
						user_group = "GOAUTODIAL AGENTS";
					}
					if(user_group == "ADMIN"){
						user_group = "GOAUTODIAL ADMINISTRATORS";
					}
					if(user_group == "SUPERVISOR"){
						user_group = "SUPERVISOR";
					}
					document.getElementById("display_user_group").innerHTML =  user_group;
					*/
				},
                onfinish:function(){
                
                /* validate required fields
					- phone_logins if generate_phone_logins = Y
					- phone_password if generate_phone_logins = Y
					- password and conf_password confirmation are equal
					- 
				*/
				var generate_phone_logins = document.getElementById('generate_phone_logins').value;
				var phone_logins = document.getElementById('phone_logins').value;
				var phone_pass = document.getElementById('phone_pass').value;

				var user_form = document.getElementById('user_form').value;
				var fullname = document.getElementById('fullname').value;
				var password = document.getElementById('password').value;
				var conf_password = document.getElementById('conf_password').value;

					/*
					if(generate_phone_logins == "Y"){
						if(phone_logins == ""){
							validate_wizard = 1;
						}
						if(phone_pass == ""){
							validate_wizard = 1;
						}
					}*/

					phone_pass = password; //matches the phone password with the password.

					if(user_form == ""){
						validate_wizard = 1;
					}

					if(fullname == ""){
						validate_wizard = 1;
					}
					
					if(password != conf_password || password == ""){
						validate_wizard = 1;
					}

					if(validate_wizard == 0){
						//alert("User Created!");
						$.ajax({
							url: "./php/CreateTelephonyUser.php",
							type: 'POST',
							data: $("#create_form").serialize(),
							success: function(data) {
							  // console.log(data);
								  if(data == 1){
								  	  swal("Success!", "User Successfully Created!", "success")
									  window.setTimeout(function(){location.reload()},1000)
									  $('#add_button').val("Loading...");
								  }else{
								  	  sweetAlert("Oops...", "Something went wrong. "+data, "error");
								  	  $('#add_button').val("Submit");
        							  $('#add_button').attr("disabled", false);
									  $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
								  }
							}
						});
					}else{
						sweetAlert("Oops...", "Something went wrong", "error");
						$('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
						validate_wizard = 0;
					}

                }
				
            });
		
		
		/* password confirmation */
		$("#password").keyup(checkPasswordMatch);
		$("#conf_password").keyup(checkPasswordMatch);

		/* additional number custom */
			$('#seats').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "custom_seats") {
				  $('#custom_seats').show();
				}
				if(this.value != "custom_seats") {
				  $('#custom_seats').hide();
				}
			});

		/* user group */
			$('#user_group').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "AGENTS" || this.value == "ADMIN") {
					document.getElementById('generate_phone_logins').value = "Y";
					$('#phone_logins_form').show();
					//$('#generate_phone_logins').val() = "Y";
				}else{
					document.getElementById('generate_phone_logins').value = "N";
					$('#phone_logins_form').hide();
					//$('#generate_phone_logins').val() = "N";
				}
			});
		
		/* generate phone logins*/
			$('#generate_phone_logins').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "Y") {
				  $('#phone_logins_form').show();
				}
				if(this.value == "N") {
				  $('#phone_logins_form').hide();
				}
			});

				/**
				  * Edit user details
				 */
				$(document).on('click','.edit-T_user',function() {
					var url = 'edittelephonyuser.php';
					var userid = $(this).attr('data-id');
					var role = $(this).attr('data-role');
					//alert(userid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="userid" value="'+userid+'" /><input type="hidden" name="role" value="'+role+'"></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				/**
				 * Delete function
				 */
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
			});
			
		</script>

		<?php print $ui->creamyFooter();?>
    </body>
</html>
