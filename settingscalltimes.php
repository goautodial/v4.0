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
        </div><!-- ./wrapper -->
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
	    <div class="modal-content" style="border-radius:5px;">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title animate-header"><b>Call Time Wizard » Add New Call Time</b></h4>
	      </div>
	      <div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
			<div class="form-horizontal">
				<form id="form_calltimes" class="">

					<div class="wizard-step" style="display: block;">

						<div class="message_box"></div>
						<fieldset>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time ID:</label>
								<div class="col-lg-7 mb">
									<label class="control-label call-time-id hide"></label>
									<input type="text" class="form-control call-time-id-textbox" name="call_time_id" placeholder="Call Time ID. This is a required field.">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time Name:</label>
								<div class="col-lg-7 mb">
									<input type="text" class="form-control call-time-name" name="call_time_name" placeholder="Call Time Name. This is a required field.">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time Comments:</label>
								<div class="col-lg-7 mb">
									<input type="text" class="form-control call-time-comments" name="call_time_comments" placeholder="Call Time ID. This is a required field.">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">User Group:</label>
								<div class="col-lg-7 mb">
									<select class="form-control call-time-user-group" name="call_time_user_group">
										<!--<option value="ALL"> ALL USER GROUPS </option>-->
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
						<fieldset>
							<div class="form-group mt">
								<label class="control-label col-lg-2">&nbsp;</label>
								<div class="col-lg-10">
									<div class="row">
										<label class="col-lg-3">Start</label>
										<label class="col-lg-3">Stop</label>
										<label class="col-lg-6">After Hours Audio</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Dafault:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_default" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_default" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_default">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Sunday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_sunday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_sunday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_sunday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Monday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_monday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_monday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_monday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Tuesday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_tuesday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_tuesday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_tuesday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Wednesday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_wednesday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_wednesday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_wednesday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Thursday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_thursday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_thursday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_thursday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Friday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_friday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_friday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_friday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
								<label class="control-label col-lg-2">Saturday:</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<div class="col-lg-3">
											<input type="text" class="form-control start_time" name="start_saturday" value="0">
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control end_time" name="stop_saturday" value="0">
										</div>
										<div class="col-lg-6">
											<select class="form-control" name="audio_saturday">
												<option value="" selected disabled> - - - Audio Chooser - - - </option>
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
	      <div class="modal-footer">
			<!-- The wizard button will be inserted here. -->
			<button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
			<input type="submit" class="btn btn-primary add-calltimes" id="submit_calltime" value="Submit" style="display: inline-block;">
		  </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

		<!-- Forms and actions -->
		<?php print $ui->standardizedThemeJS(); ?>
		<!-- wizard -->
		<script src="js/easyWizard.js" type="text/javascript"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				// $('#view-calltime-modal').modal('show');
				$('#calltimes').dataTable();

				//initialize wizard
				$("#view-calltime-modal").wizard();

				//initialize timepicker
				$('.start_time').datetimepicker({
                    format: 'LT'
                });
                $('.end_time').datetimepicker({
                    format: 'LT'
                });
				
				$('#submit_calltime').click(function(){
					$('#submit_calltime').val("Saving, Please Wait.....");
	                $('#submit_calltime').prop("disabled", true);

					var form_data = $('#form_calltimes').serialize();

					$.ajax({
						url: "./php/AddCalltime.php",
						type: 'POST',
						data: $('#form_calltimes').serialize(),
						success: function(data) {
						    console.log(data);
						    if(data == 1){
						    	swal("Success!", "Call Times Successfully Created!", "success")
                                window.setTimeout(function(){location.reload()},3000)
                                $('#submit_calltime').val("Loading");
						    }else{
						    	sweetAlert("Oops...", "Something went wrong! " + data, "error");
		                        $('#submit_calltime').val("Submit");
		                        $('#submit_calltime').prop("disabled", false);
						    }
						}
					});
					
				});
				
				/**
				  * Edit call time details
				 */
				$(document).on('click','.edit-calltime',function() {
					var url = './editsettingscalltimes.php';
					var id = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="cid" value="'+id+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});
				
				/**
                 * Delete validation modal
                 */
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
		                            url: "./php/DeleteCallTime.php",
		                            type: 'POST',
		                            data: { 
		                                call_time_id:id,
		                            },
		                            success: function(data) {
		                            console.log(data);
		                                if(data == 1){
		                                    swal("Success!", "Call Time Successfully Deleted!", "success");
		                                    window.setTimeout(function(){location.reload()},1000)
		                                }else{
		                                	sweetAlert("Oops...", "Something went wrong! "+data, "error");
		                                    window.setTimeout(function(){$('#delete_notification_modal_fail').modal('hide');}, 3000);
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
