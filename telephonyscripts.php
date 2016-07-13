
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
        <title>Scripts</title>
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
		<link href="css/style.css" rel="stylesheet" type="text/css" />
    	<link rel="stylesheet" href="css/easyWizard.css">
        <?php print $ui->creamyThemeCSS(); ?>
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
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

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

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
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("scripts"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
			<li class="active"><?php $lh->translateText("scripts"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("scripts"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="campaign_table">
					<?php print $ui->getListAllScripts(); ?>
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

	<!-- FIXED ACTION BUTTON -->
	<div class="action-button-circle" data-toggle="modal" data-target="#scripts-modal">
		<?php print $ui->getCircleButton("scripts", "plus"); ?>
	</div>
<?php
	/*
	* APIs for add form
	*/
	$scripts = $ui->API_goGetAllScripts();
?>
	<div class="modal fade" id="scripts-modal" tabindex="-1"aria-labelledby="scripts" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="scripts"><b>Script Wizard >> Add New Script</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="CreateTelephonyUser.php" method="POST" id="create_form" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="row" style="padding-top:10px;padding-bottom:0px;">
							<p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
						</div>
					<?php
						$max = count($scripts->script_id);
						$x = 0;
						for($i=0; $i < $max; $i++){
							//echo $max-$x;
							$agent = substr($scripts->script_id[$max-$x], 0, 6);
							if($agent == "script"){
								$get_last = substr($scripts->script_id[$max-$x], -2);
							}else{
								$x = $x+1;
							}
						}

						$script_num = $get_last + 1;

						$num_padded = sprintf("%03d", $script_num);
						
						//$fullname = "Agent ".$num_padded;
						$script_id_for_form = "script".$num_padded;
					?>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="script_id" style="padding-top:15px;">Script ID:</label>
							<div class="col-sm-8" style="padding-top:10px;">
								<input type="text" class="form-control" name="script_id" id="script_id" value="<?php echo $script_id_for_form;?>" disabled />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="script_name" style="padding-top:15px;">Script Name:</label>
							<div class="col-sm-8" style="padding-top:10px;">
								<input type="text" class="form-control" name="script_name" id="script_name" placeholder="Script Name" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="script_comments" style="padding-top:15px;">Script Comments:</label>
							<div class="col-sm-8" style="padding-top:10px;">
								<input type="text" class="form-control" name="script_comments" id="script_comments" placeholder="Script Comments" />
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

	<!-- Modal -->
	<div id="script-form-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Script Information</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-lg-3">Script ID:</label>
				<div class="col-lg-9">
					<input type="text" class="script_id form-control" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Script Name:</label>
				<div class="col-lg-9">
					<input type="text" class="form-control script_name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Script Comments:</label>
				<div class="col-lg-9">
					<input type="text" class="form-control script_comments">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Active:</label>
				<div class="col-lg-9">
					<select class="form-control script_status"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Script Text:</label>
				<div class="col-lg-9">
					<input type="text" class="form-control script_text">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">&nbsp;</label>
				<div class="col-lg-9">
					<textarea class="form-control"></textarea>
				</div>
			</div>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default btn-update-script">Update</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
		<div class="message-box hide">
			<div class="alert alert-success hide">
				<strong>Success!</strong> Script has been succsfully deleted.
			</div>
			
			<div class="alert alert-danger hide">
				<strong>Error!</strong> Something went wrong while deleting record.
			</div>
		</div>
	      	<p>Are you sure you want to delete Script ID: <span class="script-id-delete-label" data-id=""></span></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="delete-script-btn" data-id="">Yes</button>
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
		<script src="js/easyWizard.js" type="text/javascript"></script> 
	<!-- SLIMSCROLL-->
   		<script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>

    <script>
        	// load data.
            $(".textarea").wysihtml5();
	</script>
	
	<script>
		$(document).ready(function(){
			$('#scripts_table').dataTable();

			$("#scripts-modal").wizard({

			});
			//$('#script-form-modal').modal('show');
			
			$('.edit_script').click(function(){
				var id = $(this).attr('data-id');
				$.ajax({
				  url: "./php/ViewScript.php",
				  type: 'POST',
				  data: { 
				  	script_id : id
				  },
				  dataType: 'json',
				  success: function(data) {
				  	console.log(data);

					$('.script_id').val(data.script_id);
					$('.script_name').val(data.script_name);
					$('.script_comments').val();
					
					if (data.active == "Y"){
						var option = '<option value="Y" selected>Yes</option>';
						    option += '<option value="N">No</option>';
					}else{
						var option = '<option value="Y">Yes</option>';
						    option += '<option value="N" selected>No</option>';
					}
					$('.script_status').append(option);
					
					$('.script_text').val();
					
					$('#script-form-modal').modal('show');
				  }
				});
			});
			
			$('.btn-update-script').click(function(){
				$.ajax({
				  url: "./php/UpdateScript.php",
				  type: 'POST',
				  data: { 
				  	script_id : $('.script_id').val(),
					script_name : $('.script_name').val(),
					script_comments : $('.script_comments').val(),
					script_text : $('.script_text').val(),
					active : $('.script_status').val(),
				  },
				  //dataType: 'json',
				  success: function(data) {
				  	console.log(data);
				  }
				});
			});
			


/*
			$('.delete_script').click(function(){
				var id = $(this).attr('data-id');
				$('.script-id-delete-label').text(id);
				$('.script-id-delete-label').attr( "data-id", id );
				$('#confirmation-delete-modal').modal('show');
			});
			
			$('#delete-script-btn').click(function(){
				var id = $('.script-id-delete-label').attr( "data-id");

				$.ajax({
				  url: "./php/DeleteScript.php",
				  type: 'POST',
				  data: { 
				  	script_id : id
				  },
				  dataType: 'json',
				  success: function(data) {
				  	//console.log(data);
					
					if (data.result == "success") {
						$('.message-box, .alert-success').removeClass('hide');
						$('#delete-script-btn').addClass('hide');
					}else{
						$('.message-box, .alert-danger').removeClass('hide');
					}
				  }
				});
			});
*/

			/**
			 * Delete validation modal
			 */
			 $(document).on('click','.delete_script',function() {
			 	
			 	var id = $(this).attr('data-id');
			 	var name = $(this).attr('data-name');
			 	var action = "Script";

			 	$('.id-delete-label').attr("data-id", id);
				$('.id-delete-label').attr("data-action", action);

			 	$(".delete_extension").text(name);
				$(".action_validation").text(action);

			 	$('#delete_validation_modal').modal('show');
			 });

			 $(document).on('click','#delete_yes',function() {
			 	
			 	var id = $(this).attr('data-id');
			 	var action = $(this).attr('data-action');

			 	$('#id_span').html(id);

					$.ajax({
						url: "./php/DeleteScript.php",
						type: 'POST',
						data: { 
							script_id:id,
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
