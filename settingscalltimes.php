<?php	
	
	###################################################
	### Name: settingscalltimes.php 				###
	### Functions: Manage Calltimes 				###
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
        <title>Call Times</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>

        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
    	<link rel="stylesheet" href="css/easyWizard.css">
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- datetime picker --> 
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

		<!-- Date Picker -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("call_times_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("call_times"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="recording_table">
                            <legend><?php $lh->translateText("call_times"); ?></legend>
							<?php print $ui->getListAllCallTimes(); ?>
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

        <!-- Fixed Action Button -->
		<div class="bottom-menu skin-blue">
			<div class="action-button-circle" data-toggle="modal" data-target="#view-calltime-modal">
				<?php print $ui->getCircleButton("calltimes", "plus"); ?>
			</div>
		</div>

<?php
	$user_groups = $ui->API_goGetUserGroupsList();
	$voicefiles = $ui->API_GetVoiceFilesList();
?>
	<!-- Modal -->

	<div id="view-calltime-modal" class="modal fade">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      	<div class="modal-header">
	       		<h4 class="modal-title animated bounceInRight">
	       			<b>Call Time Wizard » Add New Call Time</b>
	       			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	       		</h4>
	      	</div>
	      	<div class="modal-body">
				<form id="form_calltimes">
					<div class="row">
						<h4>
							Basic Settings<br/>
							<small>Enter your basic call time details and assign a usergroup.</small>
						</h4>
						<fieldset>
							<div class="form-group mt">
								<label class="control-label col-lg-4">Call Time ID</label>
								<div class="col-lg-8 mb">
									<label class="control-label call-time-id hide"></label>
									<input type="text" class="form-control call-time-id-textbox" name="call_time_id" id="call_time_id" placeholder="Call Time ID (Mandatory)" title="Must be 3-10 characters only." minlength="3" maxlength="10" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time Name</label>
								<div class="col-lg-8 mb">
									<input type="text" class="form-control call-time-name" name="call_time_name" id="call_time_name" placeholder="Call Time Name (Mandatory)" maxlength="30" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time Comments</label>
								<div class="col-lg-8 mb">
									<input type="text" class="form-control call-time-comments" name="call_time_comments" id="call_time_comments" placeholder="Call Time Comments (Mandatory)" maxlength="255" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">User Group</label>
								<div class="col-lg-8 mb">
									<select class="form-control call-time-user-group select2-1" name="call_time_user_group" style="width:100%;">
										<option value="ALL"> ALL USER GROUPS </option>
											<?php
												for($i=0;$i<count($user_groups->user_group);$i++){
											?>
												<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i]." - ".$user_groups->group_name[$i];?>  </option>
											<?php
												}
											?>
									</select>
								</div>
							</div>
						</fieldset>
						<h4>
							Add a Voice File<br/>
							<small>Apply a voice file in a specific day and the time.</small>
						</h4>
						<fieldset>
							<div class="form-group mt">
								<label class="control-label col-lg-2">&nbsp;</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<label class="col-lg-3">Start</label>
										<label class="col-lg-3">Stop</label>
										<label class="col-lg-6">After Hours Audio</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Default</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_default" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_default" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_default">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Sunday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_sunday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_sunday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_sunday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Monday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_monday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_monday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_monday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Tuesday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_tuesday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_tuesday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_tuesday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Wednesday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_wednesday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_wednesday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_wednesday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Thursday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_thursday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_thursday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_thursday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Friday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_friday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_friday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_friday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Saturday</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_saturday" value="">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_saturday" value="">
										</div>
										<div class="col-lg-6">
											<select class="form-control select2-1" style="width:100%;" name="audio_saturday">
												<option value="" selected> - - - Audio Chooser - - - </option>
												<?php
													for($i=0;$i<count($voicefiles->file_name);$i++){
												?>
													<option value="<?php echo $voicefiles->file_name[$i];?>">  <?php echo $voicefiles->file_name[$i];?>  </option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</form>
	      	</div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

		<!-- Forms and actions -->
		<?php print $ui->standardizedThemeJS(); ?>
		<!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>

<script type="text/javascript">
	$(document).ready(function() {

		/*********************
		** INITIALIZATION
		*********************/
			// $('#view-calltime-modal').modal('show');
				$('#calltimes').dataTable();

			// init form wizard 
				var form = $("#form_calltimes"); 
			    form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });

            /*********
			** Init Wizard
			*********/
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
								url: "./php/AddCalltime.php",
								type: 'POST',
								data: $('#form_calltimes').serialize(),
								success: function(data) {
								    console.log(data);
								    if(data == 1){
								    	swal({title: "Success",text: "Call Times Successfully Created!",type: "success"},function(){window.location.href = 'settingscalltimes.php';});
		                                $('#finish').val("Submit");
		                                $('#finish').prop("disabled", false);
								    }else{
								    	sweetAlert("Oops...", "Something went wrong! " + data, "error");
				                        $('#finish').val("Submit");
				                        $('#finish').prop("disabled", false);
								    }
								}
							});
			        }
			    });
		
		/*********************
		** EDIT EVENT
		*********************/
				$(document).on('click','.edit-calltime',function() {
					var url = './editsettingscalltimes.php';
					var id = $(this).attr('data-id');
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="cid" value="'+id+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});
				
		/*********************
		** DELETE EVENT
		*********************/	
                 $(document).on('click','.delete-calltime',function() {
                    var id = $(this).attr('data-id');
                    swal({
                        title: "Are you sure?",
                        text: "This action cannot be undone.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete this call time!",
                        cancelButtonText: "No, cancel please!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                        },
                        function(isConfirm){
                            if (isConfirm) {
                            	$.ajax({
		                            url: "./php/DeleteCalltime.php",
		                            type: 'POST',
		                            data: { 
		                                call_time_id:id,
		                            },
		                            success: function(data) {
		                            console.log(data);
		                                if(data == 1){
		                                	swal({title: "Deleted",text: "Call Time Successfully Deleted!",type: "success"},function(){window.location.href = 'settingscalltimes.php';});
		                                }else{
		                                	sweetAlert("Oops...", "Something went wrong! "+data, "error");
		                                }
		                            }
		                        });
							} else {
                                swal("Cancelled", "No action has been done :)", "error");
                            }
                        }
                    );
				});
		
		/*********************
		** FILTERS
		*********************/	

			// disable special characters on Fullname
				$('#call_time_id').bind('keypress', function (event) {
				    var regex = new RegExp("^[ A-Za-z0-9_-]*$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on Fullname
				$('#call_time_name').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

		/* initialize select2 */
			$('.select2-1').select2({
		        theme: 'bootstrap'
		    });

		//initialize timepicker
			$('.start_time').datetimepicker({
				defaultDate: '',
                format: 'LT'
               
            });
           	$('.end_time').datetimepicker({
           		defaultDate: '',
                format: 'LT'
            });
	}); // end of document ready
</script>
		
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
