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
        <title>Goautodial Users</title>
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
		<!-- Circle Buttons style -->
    	<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    	<!-- Wizard Form style -->
		<link href="css/style.css" rel="stylesheet" type="text/css" />
    	<link rel="stylesheet" href="css/easyWizard.css">

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
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        	<!-- =============== BOOTSTRAP STYLES ===============--
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

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
                        <div class="panel-body table" id="users_table">
                        <legend><?php $lh->translateText("users"); ?></legend>
							<?php print $ui->goGetAllUserList(); ?>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
					</script>
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
					<h4 class="modal-title animate-header" id="T_User"><b>User Wizard >> Add New User</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="CreateTelephonyUser.php" method="POST" id="create_form" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="row" style="padding-top:10px;padding-bottom:0px;">
							<p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_group" style="padding-top:15px;">User Group:</label>
							<div class="col-sm-8" style="padding-top:10px;">
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
							<label class="col-sm-4 control-label" style="padding-top:15px;">Current Users: </label>
							<div class="col-sm-8" style="padding-top:10px;">
								<h4 style="padding-left:20px;"><?php echo count($output->userno); ?></h4>
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
							<label class="col-sm-4 control-label" for="custom_num_seats" style="padding-top:15px;">Number of Seats: </label>
							<div class="col-sm-6" style="padding-top:10px;">
								<input type="number" name="custom_num_seats" id="custom_num_seats" class="form-control" min="1" max="99" value="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="generate_phone_logins" style="padding-top:15px;">Generate Phone Logins: </label>
							<div class="col-sm-8" style="padding-top:10px;">
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
							<label class="col-sm-4 control-label" for="phone_logins" style="padding-top:15px;">* Phone Login: </label>
							<div class="col-sm-8" style="padding-top:10px;">
								<input type="number" name="phone_logins" id="phone_logins" class="form-control" minlength="3" value="<?php echo $latest_phone;?>" pattern=".{3,}" title="Minimum of 3 characters" required>
							</div>
						</div>
					</div>
				
			
				<!-- STEP 2 -->
					<div class="wizard-step">
						
						<div class="row" style="padding-top:10px;padding-bottom:0px;">
							<p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
						</div>

						<div class="row" style="margin-left:0;">
							<label class="control-label col-sm-4" style="padding-top:15px;">User Group:</label>
							<div class="col-sm-8" style="padding-top:15px; display: inline-flex;">
								<span id="display_user_group"></span>
							</div>
						</div>
						
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
							<label class="col-sm-4 control-label" style="padding-top:15px;">* Users ID: </label>
							<div class="col-sm-8 wizard-inline">
								<input type="text" class="form-control" name="user_form" id="user_form" placeholder="User ID" value="<?php echo $user_id_for_form;?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="fullname" style="padding-top:15px;">* Fullname: </label>
							<div class="col-sm-8 wizard-inline">
								<input type="text" name="fullname" id="fullname" class="form-control"
									   value="<?php echo $fullname;?>">
							</div>
						</div>
						<div class="row">
							<p class="col-sm-12" style="padding-top:20px;padding-bottom:0px;"><small><i> - - - Default Password is:</i></small> <b>Go2016</b><small><i> - - - </i></small></p>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="password" style="padding-top:0px;">* Password: </label>
							<div class="col-sm-8" style="display:inline-flex;">
								<input type="password" class="form-control" name="password" id="password" placeholder="Password" value="Go2016" required>
								
							</div> 
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="conf_password" style="padding-top:15px;">* Confirm Password: </label>
							<div class="col-sm-8 wizard-inline">
								<input type="password" class="form-control" id="conf_password" placeholder="Password" value="Go2016" required>
							</div> 
						</div>
						<div class="row">
							<p class="col-sm-12"><small class="pull-right" style="padding-right:20px;"><i><span id="pass_result"></span></i></small></p>
						</div>

						<div id="phone_div" style="display:none;">
							<div class="form-group">
								<label class="col-sm-4 control-label" for="phone_login1" style="padding-top:15px;">* Phone Login: </label>
								<div class="col-sm-8 wizard-inline">
									<input type="text" readonly name="phone_login1" id="phone_login1" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="phone_pass" style="padding-top:15px;">* Phone Password: </label>
								<div class="col-sm-8 wizard-inline">
									<input type="text" name="phone_pass" id="phone_pass" class="form-control" value="Go2016">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label" for="status" style="padding-top:15px;">Active: </label>
							<div class="col-sm-4 wizard-inline">
								<select name="status" class="form-control">
									<option value="Y" selected>Yes</option>		
			
									<option value="N" >No</option>						
								</select>
							</div>
						</div>
					</div><!--end of step2 -->
				</form>
		
				</div> <!-- end of modal body -->
				
				<!-- NOTIFICATIONS -->
				<div id="notifications">
					<div class="output-message-success" style="display:none;">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <strong>Success!</strong> New Agent added.
						</div>
					</div>
					<div class="output-message-error" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <strong>Error!</strong> Something went wrong please see input data on form or if agent already exists.
						</div>
					</div>
					<div class="output-message-incomplete" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  Please fill-up all the fields correctly and do not leave any fields with (<strong> * </strong>) blank.
						</div>
					</div>
				</div>

				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
			</div>
		</div>
	</div><!-- end of modal -->

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


		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="js/easyWizard.js" type="text/javascript"></script> 
		
	<!-- SLIMSCROLL-->
   		<script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
   
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

		// for easy wizard 
			var form = document.getElementById('create_form');
			var validate_wizard = 0;
			
			var generate_phone_logins = document.getElementById('generate_phone_logins').value;
			var phone_logins = document.getElementById('phone_logins').value;
			var phone_pass = document.getElementById('phone_pass').value;

			var password = document.getElementById('password').value;
			var conf_password = document.getElementById('conf_password').value;

			$("#wizard-modal").wizard({

				onnext:function(){		

					var generate_phone_logins = document.getElementById('generate_phone_logins').value;
					var phone_logins = document.getElementById('phone_logins').value;

					if(generate_phone_logins == "Y"){
						document.getElementById("phone_login1").value = phone_logins;
						$('#phone_div').show();
					}else{
						$('#phone_div').hide();
					}

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

					if(generate_phone_logins == "Y"){
						if(phone_logins == ""){
							validate_wizard = 1;
						}
						if(phone_pass == ""){
							validate_wizard = 1;
						}
					}

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
								  	  $('.output-message-success').show().focus().delay(2000);
									  window.setTimeout(function(){location.reload()},1000)
									  $('#add_button').val("Loading...");
								  }else{
								  	  $('#add_button').val("Submit");
        							  $('#add_button').attr("disabled", false);
									  $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
								  }
							}
						});
					}else{
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
				if(this.value == "AGENTS") {
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
				 * Delete validation modal
				 */
				 $(document).on('click','.delete-T_user',function() {
				 	
				 	var user_id = $(this).attr('data-id');
				 	var user_name = $(this).attr('data-name');
				 	var action = "User";

				 	$('.id-delete-label').attr("data-id", user_id);
					$('.id-delete-label').attr("data-action", action);

				 	$(".delete_extension").text(user_name);
					$(".action_validation").text(action);

				 	$('#delete_validation_modal').modal('show');
				 });

				 $(document).on('click','#delete_yes',function() {
				 	
				 	var id = $(this).attr('data-id');
				 	var action = $(this).attr('data-action');

				 	$('#id_span').html(id);

						$.ajax({
							url: "./php/DeleteTelephonyUser.php",
							type: 'POST',
							data: { 
								userid:id,
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
				 });
				
				
				
			});
			
		</script>
    </body>
</html>
