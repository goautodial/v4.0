

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
        <title>Call Times</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <!-- Circle Buttons style -->
        <link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
    	<link rel="stylesheet" href="css/easyWizard.css">

        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<!-- Bootstrap Player -->
	<link href="css/bootstrap-player.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

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

        <!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- datetime picker --> 
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

		<!-- Date Picker -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

		<!-- SWEETALERT-->
   		<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
		<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>

	<!-- Bootstrap Player -->
	<script src="js/bootstrap-player.js" type="text/javascript"></script>

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        <!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

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

?>
	<!-- Modal -->

	<div id="view-calltime-modal" class="modal fade">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content" style="border-radius:5px;">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title animate-header"><b>Call Time Details</b></h4>
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
									<input type="text" class="form-control call-time-id-textbox" name="call_time_id">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time Name:</label>
								<div class="col-lg-7 mb">
									<input type="text" class="form-control call-time-name" name="call_time_name">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-4">Call Time Comments:</label>
								<div class="col-lg-7 mb">
									<input type="text" class="form-control call-time-comments" name="call_time_comments">
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
											<input type="text" class="form-control" name="audio_default">
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
											<input type="text" class="form-control" name="audio_sunday">
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
											<input type="text" class="form-control" name="audio_monday">
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
											<input type="text" class="form-control" name="audio_tuesday">
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
											<input type="text" class="form-control" name="audio_wednesday">
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
											<input type="text" class="form-control" name="audio_thursday">
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
											<input type="text" class="form-control" name="audio_friday">
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
											<input type="text" class="form-control" name="audio_saturday">
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
	
	<!-- Modal --
	<div id="confirmation-delete-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content--
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Confirmation Box</b></h4>
	      </div>
	      <div class="modal-body">
	      	<p>Are you sure you want to delete Call Time ID: <span class="calltime-id-delete-label" data-id=""></span></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="delete-calltime-btn" data-id="">Yes</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
	      </div>
	    </div>
	    <!-- End of modal content --
	  </div>
	</div>
	<!-- End of modal -->

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
						    	sweetAlert("Oops...", "Something went wrong!", "error");
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
                    
                    var calltime_id = $(this).attr('data-id');
                    var calltime_name = $(this).attr('data-name');
                    var action = "Call Time";

                    $('.id-delete-label').attr("data-id", calltime_id);
                    $('.id-delete-label').attr("data-action", action);

                    $(".delete_extension").text(calltime_name);
                    $(".action_validation").text(action);

                    $('#delete_validation_modal').modal('show');
                 });

                 $(document).on('click','#delete_yes',function() {
                    
                    var id = $(this).attr('data-id');
                    var action = $(this).attr('data-action');

                    $('#id_span').html(id);
                    	//alert(id);
                        $.ajax({
                            url: "./php/DeleteCallTime.php",
                            type: 'POST',
                            data: { 
                                call_time_id:id,
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
                                    window.setTimeout(function(){$('#delete_notification_modal_fail').modal('hide');}, 3000);
                                }
                            }
                        });
                 });

				
			});
		</script>
    </body>
</html>
