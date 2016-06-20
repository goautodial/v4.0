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
                    <div class="row">
                        <div class="col-xs 12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("users"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="users_table">
									<?php print $ui->goGetAllUserList(); ?>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>
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
		<?php print $ui->getCircleButton("calls", "plus"); ?>
	</div>
	
<!-- MODAL -->
<?php $output = $ui->API_goGetAllUserLists();?>

    <div class="modal fade" id="wizard-modal" tabindex="-1"aria-labelledby="T_User" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:10px;">
				
				<!-- NOTIFICATIONS -->

				<div class="output-message-success hide">
					<div class="alert alert-success alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>Success!</strong> New Agent added.
					</div>
				</div>
				<div class="output-message-error hide">
					<div class="alert alert-danger alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>Error!</strong> Something went wrong please see input data on form or if agent already exists.
					</div>
				</div>
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="T_User">User Wizard >> Add New User</h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="CreateTelephonyUser.php" method="POST" id="create_form" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="user_group" style="padding-top:15px;">User Group:</label>
							<div class="col-sm-8" style="padding-top:10px;">
								<select id="user_group" class="form-control" name="user_group" onchange="session()">
									<option value="AGENTS">GOAUTODIAL AGENTS</option>
									<option value="ADMIN">GOAUTODIAL ADMINISTRATORS</option>
									<option value="SUPERVISOR">SUPERVISOR</option>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" style="padding-top:15px;">Current Users: </label>
							<div class="col-sm-8" style="padding-top:10px;">
								<h4 style="padding-left:20px;"><?php echo count($output->userno); ?></h4>
							</div>
						</div>
						
						<div class="form-group">		
							<label class="col-sm-4 control-label" style="padding-top:15px;">Additional Seat(s): </label>
							<div class="col-sm-8" style="padding-top:10px;">
								<select name="seats" id="seats" class="form-control">
								<?php
								for($i=1; $i <= count($output->userno); $i++){ ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php
								}
								?>
									<option value="custom_seats">Custom</option>
								</select>
							</div>
						</div>
						<div class="form-group" id="custom_seats" style="display:none;">
							<label class="col-sm-4 control-label" for="custom_num_seats" style="padding-top:15px;">Number of Seats: </label>
							<div class="col-sm-8" style="padding-top:10px;">
								<input type="number" name="custom_num_seats" id="custom_num_seats" class="form-control" min="1" max="99" placeholder="Number of Seats">
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
						<div class="form-group" id="phone_logins_form" style="display:none;">
							<label class="col-sm-4 control-label" for="phone_logins" style="padding-top:15px;">Phone Login: </label>
							<div class="col-sm-8" style="padding-top:10px;">
								<input type="number" name="phone_logins" id="phone_logins" class="form-control" placeholder="eg. 8001" pattern=".{3,}"  required title="Minimum of 3 characters">
							</div>
						</div>
					</div>
				
			
				<!-- STEP 2 -->
					<div class="wizard-step" onload="alert('step 2');">
						
						<div class="form-group">
							<label class="control-label col-sm-4">User Group:</label>
							<div class="col-sm-8">
								<h4> AGENT</h4>
							</div>
						</div>
						
						<?php
						$max = count($output->userno);
						$x = 0;
						for($i=0; $i < $max; $i++){
							//echo $max-$x;
							$agent = substr($output->full_name[$max-$x], 0, 5);
							if($agent == "Agent"){
								$get_last = substr($output->full_name[$max-$x], -2);
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
							<label class="control-label col-sm-4">Users ID: </label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="user_form" id="user_form" placeholder="User ID" value="<?php echo $user_id_for_form;?>" required>
							</div>
						</div>
						
						<div class="form-group">		
							<label class="control-label col-sm-4" for="password">Password: </label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="password" id="password" placeholder="Password" value="Go2016" required>
							</div> 
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="phone_login1">Phone Login: </label>
							<div class="col-sm-8">
								<input type="text" readonly name="phone_login1" id="phone_login1" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="phone_pass">Phone Password: </label>
							<div class="col-sm-8">
								<input type="text" name="phone_pass" id="phone_pass" class="form-control" value="Go2016">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label" for="fullname">Fullname: </label>
							<div class="col-sm-8">
								<input type="text" name="fullname" id="fullname" class="form-control"
									   value="<?php echo $fullname;?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="status">Active: </label>
							<div class="col-sm-8">
								<select name="status" class="form-control">
									<option value="N" selected>No</option>
									<option value="Y">Yes</option>								
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

		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="js/easyWizard.js" type="text/javascript"></script> 
		<script type="text/javascript">
			$(document).ready(function() {
				$('#T_users').dataTable();
		
		// for easy wizard -
			$("#wizard-modal").wizard({
				onnext:function(){
					//alert("Nexted!");
					var phone_logins = document.getElementById('phone_logins').value;
					document.getElementById("phone_login1").value = phone_logins;
					
					if(phone_logins == null || phone_logins == ""){
					  alert("Please Fill All Required Field");
					  return false;
						
						
					}
				},
                onfinish:function(){
					$.ajax({
						/*url: ".\php\AddCampaign.php",*/
						url: "./php/CreateTelephonyUser.php",
						type: 'POST',
						data: $("#create_form").serialize(),
						success: function(data) {
						  // console.log(data);
							  if(data == 1){
								  $('.output-message-success').removeClass('hide');
								  $('.output-message-error').addClass('hide');
								  window.location = window.location.href;
							  }else{
								  $('.output-message-error').removeClass('hide');
								  $('.output-message-success').addClass('hide');
							  }
						}
					});
                }
				
            });
		
		/* additional number custom*/
			$('#seats').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "custom_seats") {
				  $('#custom_seats').show();
				}
				if(this.value != "custom_seats") {
				  $('#custom_seats').hide();
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
				 $(".edit-T_user").click(function(e) {
					e.preventDefault();
					var url = 'edittelephonyuser.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="userid" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				/**
				 * Delete user.
				 */
				 $(".delete-T_user").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var user_id = $(this).attr('href');
						$.post("./php/DeleteTelephonyUser.php", { userid: user_id } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_user"); ?>"); }
						});
					}
				 });
				
				
				
			});
			
		</script>
    </body>
</html>
