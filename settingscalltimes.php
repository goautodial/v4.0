<?php	
	
	###################################################
	### Name: settingscalltimes.php 	   ###
	### Functions: Manage Calltimes 	   ###
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
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("call_times"); ?></title>
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
	       			<b><?php $lh->translateText("call_time_wizard"); ?> » <?php $lh->translateText("add_new_call_time"); ?> </b>
	       			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	       		</h4>
	      	</div>
	      	<div class="modal-body">
				<form id="form_calltimes">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
					<div class="row">
						<h4>
							<?php $lh->translateText("call_time_header"); ?><br/>
							<small><?php $lh->translateText("call_sub_time_header"); ?></small>
						</h4>
						<fieldset>
							<div class="form-group mt">
								<label class="control-label col-lg-4"><?php $lh->translateText("call_time_id"); ?></label>
								<div class="col-lg-8 mb">
									<label class="control-label call-time-id hide"></label>
									<input type="text" class="form-control call-time-id-textbox" name="call_time_id" id="call_time_id" placeholder="<?php $lh->translateText("call_time_id"); ?>" title="Must be 2-10 characters only." minlength="2" maxlength="10" required>
									<label id="calltime-duplicate-error"></label>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4"><?php $lh->translateText("call_time_name"); ?></label>
								<div class="col-lg-8 mb">
									<input type="text" class="form-control call-time-name" name="call_time_name" id="call_time_name" placeholder="<?php $lh->translateText("call_time_name"); ?>" maxlength="30" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4"><?php $lh->translateText("call_time_comments"); ?></label>
								<div class="col-lg-8 mb">
									<input type="text" class="form-control call-time-comments" name="call_time_comments" id="call_time_comments" placeholder="<?php $lh->translateText("call_time_comments"); ?>" maxlength="255" >
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4"><?php $lh->translateText("user_groups"); ?></label>
								<div class="col-lg-8 mb">
									<select class="form-control call-time-user-group select2-1" name="call_time_user_group" style="width:100%;">
										<option value="ALL"> <?php $lh->translateText("all_usergroups"); ?> </option>
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
							<?php $lh->translateText("voice_file_header"); ?><br/>
							<small><?php $lh->translateText("voice_file_sub_header"); ?></small>
						</h4>
						<fieldset>
							<div class="form-group mt">
								<label class="control-label col-lg-2">&nbsp;</label>
								<div class="col-lg-10 mb">
									<div class="row">
										<label class="col-lg-3"><?php $lh->translateText("start"); ?></label>
										<label class="col-lg-3"><?php $lh->translateText("stop"); ?></label>
										<label class="col-lg-6"><?php $lh->translateText("after_hours"); ?></label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2"><?php $lh->translateText("default"); ?></label>
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
												<option value="" selected disabled><?php $lh->translateText("audio_chooser"); ?></option>
												<option value=""><?php $lh->translateText("none"); ?></option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("sunday"); ?></label>
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
												<option value="" selected disabled><?php $lh->translateText("audio_chooser"); ?></option>
												<option value=""> <?php $lh->translateText("none"); ?> </option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("monday"); ?></label>
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
												<option value="" selected disabled> <?php $lh->translateText("audio_chooser"); ?></option>
												<option value=""><?php $lh->translateText("none"); ?></option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("tuesday"); ?></label>
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
												<option value="" selected disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
												<option value=""> <?php $lh->translateText("none"); ?> </option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("wednesday"); ?></label>
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
												<option value="" selected disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
												<option value=""><?php $lh->translateText("none"); ?> </option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("thursday"); ?></label>
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
												<option value="" selected disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
												<option value=""> <?php $lh->translateText("none"); ?> </option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("friday"); ?></label>
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
												<option value="" selected disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
												<option value=""> <?php $lh->translateText("none"); ?> </option>
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
								<label class="control-label col-lg-2"><?php $lh->translateText("saturday"); ?></label>
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
												<option value="" selected disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
												<option value=""> <?php $lh->translateText("none"); ?> </option>
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
		var checker = 0;
		
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
							checker = 0;
							return true;
						}
	
						console.log(checker);
						// Disable next if there are duplicates
						if(checker > 0){
							$(".body:eq(" + newIndex + ") .error", form).addClass("error");
							return false;
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
			        	$('#finish').text("<?php $lh->translateText("loading"); ?>");
			        	$('#finish').attr("disabled", true);

			            // Submit form via ajax
				            $.ajax({
								url: "./php/AddCalltime.php",
								type: 'POST',
								data: $('#form_calltimes').serialize(),
								success: function(data) {
								    console.log(data);
								    if(data == 1){
								    	swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("add_calltime_success"); ?>",type: "success"},function(){window.location.href = 'settingscalltimes.php';});
		                                $('#finish').val("<?php $lh->translateText("submit"); ?>");
		                                $('#finish').prop("disabled", false);
								    }else{
								    	sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>" + data, "error");
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
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});
				
		/*********************
		** DELETE EVENT
		*********************/
                 $(document).on('click','.delete-calltime',function() {
                    var id = $(this).attr('data-id');
					var log_ip = '<?=$_SERVER['REMOTE_ADDR']?>';
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
                    swal({
                        title: "<?php $lh->translateText("are_you_sure"); ?>",
                        text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php $lh->translateText("confirm_delete_calltime"); ?>",
                        cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",
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
										log_user: log_user,
										log_group: log_group,
										log_ip: log_ip
		                            },
		                            success: function(data) {
		                            console.log(data);
		                                if(data == 1){
		                                swal({title: "<?php $lh->translateText("deleted"); ?>",text: "<?php $lh->translateText("calltime_delete_success"); ?>",type: "success"},function(){window.location.href = 'settingscalltimes.php';});
		                                }else{
		                                sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
		                                }
		                            }
		                        });
							} else {
                                swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
                            }
                        }
                    );
				});
		
	/*********************
	** FILTERS
	*********************/

		// disable special characters on Fullname
			$('#call_time_id').bind('keypress', function (event) {
				var regex = new RegExp("^[a-zA-Z0-9_-]+$");
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
		
		// check duplicates
			$("#call_time_id").keyup(function() {
				clearTimeout($.data(this, 'timer'));
				var wait = setTimeout(validate_id, 500);
				$(this).data('timer', wait);
			});
		
			function validate_id(){
				var id_value = $('#call_time_id').val();
		        if(id_value != ""){
				    $.ajax({
					    url: "php/checkCalltime.php",
					    type: 'POST',
					    data: {
					    	id : id_value
					    },
						success: function(data) {
							console.log(data);
							if(data == "success"){
								checker = 0;
								$( "#call_time_id" ).removeClass("error");
								$( "#calltime-duplicate-error" ).text( "" ).removeClass("error").addClass("avail");
							}else{
								$( "#call_time_id" ).removeClass("valid").addClass( "error" );
								$( "#calltime-duplicate-error" ).text( data ).removeClass("avail").addClass("error");
								
								checker = 1;
							}
						}
					});
				}
			}
	}); // end of document ready
</script>
		
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
