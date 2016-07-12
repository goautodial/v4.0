<?php	
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
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
		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
		<!-- Circle Buttons style -->
    	<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
		<!-- Wizard Form style -->
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
                        <small><?php $lh->translateText("phones"); ?></small>
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
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("phones"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="phone_table">
									<?php print $ui->getPhonesList(); ?>
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
            <div class="modal-content" style="border-radius:20px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="T_Phones"><b>Phone Wizard >> Add New Phone</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form class="form-horizontal" name="create_form" id="create_form" role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="add_phones">Additional Phone(s):</label>
							<div class="col-sm-6">
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
								<input type="number" name="custom_seats" value="1" min="1" max="99" style="padding:5px;">
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext" style="padding-top:15px;">Starting Extension:</label>
							<div class="col-sm-6" style="padding-top:10px;">
								<input type="number" name="start_ext" id="start_ext" placeholder="e.g. 8001" value="<?php echo $suggested_extension;?>" class="form-control" required>
							</div>
						</div>
					</div>
				<!-- end of step 1-->
				<!-- STEP 2 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="phone_ext" style="padding-top:15px;">Phone Extension/Login: <b> * </b>	</label>
							<div class="col-sm-6 wizard-inline">
								<input text="text" name="phone_ext" id="phone_ext" placeholder="e.g. 8001" class="form-control" required/>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="phone_pass" style="padding-top:15px;">Phone Login Password: <b> * </b></label>
							<div class="col-sm-6 wizard-inline">
								<input type="text" value="G016gO" name="phone_pass" id="phone_pass" class="form-control" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext" style="padding-top:15px;">User Group:</label>
							<div class="col-sm-8 wizard-inline">
								<select name="user_group" id="user_group" class="form-control" required>
									<option value="---ALL---">ALL USER GROUPS</option>
									<?php
										for($i=0; $i < count($user_groups->user_group); $i++){
									?>
										<option value="<?php echo $user_groups->user_group[$i];?>"> <?php echo $user_groups->group_name[$i]; ?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="ip" style="padding-top:15px;">Server IP:	</label>
							<div class="col-sm-8 wizard-inline">
								<select name="ip" id="ip" class="form-control" required>
									<option value="69.46.6.35">
										<?php echo $_SERVER['REMOTE_ADDR'];?>
									</option>
								</select>
							</div>
						</div>

						<div class="form-group">		
							<label class="col-sm-4 control-label" for="fullname" style="padding-top:15px;">Full Name: <b> * </b></label>
							<div class="col-sm-7 wizard-inline">
								<input type="text" name="fullname" id="fullname" placeholder="Full Name" class="form-control" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="gmt" style="padding-top:15px;">Local GMT:	</label>
							<div class="col-sm-8 wizard-inline">
								<select name="gmt" id="gmt" class="col-sm-4" required>
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
								<p class="text-muted col-sm-8">( Do NOT adjust for DST)</p>
							</div>
						</div>
					</div>
				<!-- end of step 2-->
				</form>

				</div> <!-- end of modal body -->
				
				<!-- NOTIFICATIONS -->

					<div class="output-message-success" style="display:none;">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Success!</strong> New Phone added.
						</div>
					</div>
					<div class="output-message-error" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Error!</strong> Something went wrong, please see if phone extension already exists or there might be a problem in you internet connection.
						</div>
					</div>
					<div class="output-message-incomplete" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  Please fill-up all the fields correctly and do not leave any fields with (<strong> * </strong>) blank.
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
					<h4 class="modal-title">Confirm Deletion of Phone Extension ?</h4>
				</div>
				<div class="modal-body" style="background:#fff;">
					<p>Are you sure you want to delete Phone Extension <i><b style="font-size:20px;"><span id="delete_extension"></span></b></i> ?</p>
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
		<?php echo $ui->deleteNotificationModal('Phone Extension','<span id="id_span"></span>', '<span id="result_span"></span>');?>
	</div>
		
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
										  $('.output-message-success').show().focus().delay(2000).fadeOut().queue(function(n){$(this).hide(); n();});
										  window.setTimeout(function(){location.reload()},3000)
									  }else{
										  $('.output-message-error').show().focus().delay(8000).fadeOut().queue(function(n){$(this).hide(); n();});
									  }
								}
							});
						}else{
							$('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
							validate_wizard = 0;
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
				 	
				 	var extenid = $(this).attr('data-id');
				 	
				 	$("#delete_extension").html(extenid);
				 	
					$('.id-delete-label').attr( "data-id", extenid);

				 	$('#delete_validation_modal').modal('show');

				 });

				 /**
				 * Delete action.
				 */
				 $(document).on('click','#delete_yes',function() {
				 	
				 	var extenid = $(this).attr('data-id');
				 	var success = "success";
				 	var failed = "";

				 	$('#id_span').html(extenid);
				 	

					$.ajax({
					  url: "./php/DeleteSettingsPhones.php",
					  type: 'POST',
					  data: { 
					  	exten_id :extenid,
					  },
					  success: function(data) {
					  		// console.log(data);
					  		if(data == 1){
					  			$('#result_span').html(success);
					  			$('#delete_notification').show();
							 	$('#delete_notification_modal').modal('show');
							 	window.setTimeout(function(){$('#delete_notification_modal').modal('hide');location.reload();}, 2000);
								//window.setTimeout(function(){location.reload()},3000)
							}else{
								$('#result_span').html(failed);
								$('#delete_notification').show();
							 	$('#delete_notification_modal').modal('show');
							 	window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 4000);
							}
					    }
					});
					
				 });
				 
				
			});
		</script>
    </body>
</html>
