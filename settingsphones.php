<?php	

	###################################################
	### Name: settingsphones.php 					###
	### Functions: Manage Phones 			 		###
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
        <title>Phones</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		
		<?php print $ui->standardizedThemeCSS(); ?>       

		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
		<!-- Wizard Form style -->
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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("phones_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("phones"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body" id="phone_table">
                            <legend><?php $lh->translateText("phones"); ?></legend>
							<?php print $ui->getPhonesList(); ?>
                        </div>
                    </div>
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
			<?php print $ui->getCircleButton("calls", "plus"); ?>
		</div>
<?php
	// API List
	$phones = $ui->API_getPhonesList();
	$user_groups = $ui->API_goGetUserGroupsList();
	$max = max($phones->extension);
	$suggested_extension = $max + 1;
?>
<!-- MODAL -->
    <div class="modal fade" id="wizard-modal" tabindex="-1"aria-labelledby="T_Phones" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="T_Phones"><b>Phone Wizard » Add New Phone</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form class="form-horizontal" name="create_form" id="create_form" role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
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
								<input type="number" class="form-control" name="custom_seats" value="1" min="1" max="99" style="padding:5px;">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext">Starting Extension</label>
							<div class="col-sm-8 mb">
								<input type="number" name="start_ext" id="start_ext" placeholder="e.g. 8001" value="<?php echo $suggested_extension;?>" class="form-control" required>
							</div>
						</div>
					</div>
				<!-- end of step 1-->
				<!-- STEP 2 -->
					<div class="wizard-step">
						<div class="form-group mt">
							<label class="col-sm-4 control-label" for="phone_ext">Phone Extension/Login</label>
							<div class="col-sm-8 mb">
								<input text="text" name="phone_ext" id="phone_ext" placeholder="Phone Login. This is a required field." class="form-control" required/>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="phone_pass">Phone Login Password</label>
							<div class="col-sm-8 mb">
								<input type="text" value="G016gO" name="phone_pass" id="phone_pass" class="form-control"  placeholder="Phone Password. This is a required field." required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext">User Group</label>
							<div class="col-sm-8 mb">
								<select name="user_group" id="user_group" class="form-control" required>
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
							<label class="col-sm-4 control-label" for="fullname">Full Name</label>
							<div class="col-sm-8 mb">
								<input type="text" name="fullname" id="fullname" placeholder="Full Name" class="form-control" required>
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
					</div>
				<!-- end of step 2-->
				</form>

				</div> <!-- end of modal body -->
				
				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
			</div>
		</div>
	</div><!-- end of modal -->
		
		<?php print $ui->standardizedThemeJS(); ?>
		<!-- for wizard -->
		<script src="js/easyWizard.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#T_phones').dataTable({
					stateSave: true
				});
				
				/* additional number custom */
				$('#add_phones').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "CUSTOM") {
					  $('#custom_seats').show();
					}
					if(this.value != "CUSTOM") {
					  $('#custom_seats').hide();
					}
				});


				$("#wizard-modal").wizard({
					onnext:function(){
						
						var ext = document.getElementById('start_ext').value;
						document.getElementById("phone_ext").value = ext;
						document.getElementById("fullname").value = ext;
						
					},
					onfinish:function(){

					var validate_wizard = 0;

						var phone_ext = document.getElementById('phone_ext').value;
						var fullname = document.getElementById('fullname').value;
						var phone_pass = document.getElementById('phone_pass').value;

					if(phone_ext == ""){
						validate_wizard = 1;
					}
					if(fullname == ""){
						validate_wizard = 1;
					}
					if(phone_pass == ""){
						validate_wizard = 1;
					}

						if(validate_wizard == 0){
							$.ajax({
								url: "./php/AddSettingsPhones.php",
								type: 'POST',
								data: $("#create_form").serialize(),
								success: function(data) {
								  // console.log(data);
									  if(data == 1){
										  swal("Success!", "Phone Successfully Created!", "success");
										  window.setTimeout(function(){location.reload()},3000)
										  $('#add_button').val("Loading...");
									  }else{
										  sweetAlert("Oops...", "Something went wrong! "+data, "error");
									  	  $('#add_button').val("Submit");
        							  	  $('#add_button').attr("disabled", false);
									  }
								}
							});
						}else{
							sweetAlert("Oops...", "Something went wrong!", "error");
							validate_wizard = 0;
							$('#add_button').val("Submit");
        					$('#add_button').attr("disabled", false);
						}
					}
				});
				
				/**
				  * Edit user details
				 */
				$(document).on('click','.edit-phone',function() {
					var url = './editsettingsphones.php';
					var extenid = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="extenid" value="'+extenid+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});
						
				
				/**
				 * Delete phones.
				 */
				$(document).on('click','.delete-phone',function() {
				 	var id = $(this).attr('data-id');
                    swal({   
                        title: "Are you sure?",   
                        text: "This action cannot be undone.",   
                        type: "warning",   
                        showCancelButton: true,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "Yes, delete this moh!",   
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
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
