<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
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
        <title>Goautodial Music On Hold</title>
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
        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
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
	<!-- Bootstrap Player -->
	<script src="js/bootstrap-player.js" type="text/javascript"></script>

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
                        <small><?php $lh->translateText("music_on_hold"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("music_on_hold"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("music_on_hold"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="recording_table">
					<?php print $ui->getListAllMusicOnHold(); ?>
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
	<div class="action-button-circle" data-toggle="modal" data-target="#moh-wizard">
		<?php print $ui->getCircleButton("moh", "plus"); ?>
	</div>

<?php
 /*
  * APIs needed for form
  */
   $user_groups = $ui->API_goGetUserGroupsList();
?>

	<!-- Modal -->
	<div id="view-moh-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Music On Hold Details</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			
			<div class="form-group">
				<label class="control-label col-lg-4">Music on Hold Name:</label>
				<div class="col-lg-7">
					<input type="text" class="form-control moh_name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4">Status:</label>
				<div class="col-lg-5">
					<select class="form-control moh_status">
						<option value="Y">Active</option>
						<option value="N">Inactive</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4">User Group:</label>
				<div class="col-lg-7">
					<select class="form-control moh_user_group">
						<?php
                            for($i=0;$i<count($user_groups->user_group);$i++){
                        ?>
                            <option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
                        <?php
                            }
                        ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4">Random Order:</label>
				<div class="col-lg-5">
					<select class="form-control moh_rand_order">
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</div>
			</div>
		</div>
	      </div>
          <div class="message_box"></div>
	      <div class="modal-footer">
	           <button type="button" class="btn btn-primary btn-update-moh-info" data-id=""><span id="update_button"><i class="fa fa-check"></i> Update</span></button>
	           <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- ADD USER GROUP MODAL -->
    <div class="modal fade" id="moh-wizard" tabindex="-1" aria-labelledby="moh-wizard" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">

            <!-- Header -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title animate-header"><b>Music On Hold Wizard » Add New Music On Hold</b></h4>
                </div>
                <div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
                
                <form action="" method="POST" id="create_moh" name="create_moh" class="form-horizontal " role="form">
                <!-- STEP 1 -->
                    <div class="wizard-step">
                        <div class="row" style="padding-top:10px;padding-bottom:10px;">
                            <p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="moh_id" style="padding-top:10px;">* Music On Hold ID:</label>
                            <div class="col-sm-7">
                                <input type="text" name="moh_id" id="moh_id" class="form-control" placeholder="Music on Hold ID">
                                <span  class="text-red"><small><i>* Minimum of 3 characters..</i></small></span>
                            </div>
                        </div>
                        <div class="form-group">        
                            <label class="col-sm-4 control-label" for="moh_name" style="padding-top:15px;">* Music On Hold Name: </label>
                            <div class="col-sm-7" style="padding-top:10px;">
                                <input type="text" name="moh_name" id="moh_name" class="form-control" placeholder="Music On Hold Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="active" style="padding-top:15px;">Status: </label>
                            <div class="col-sm-5" style="padding-top:10px;">
                                <select name="active" id="active" class="form-control">
                                    <option value="N" selected>INACTIVE</option>
                                    <option value="Y">ACTIVE</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="user_group" style="padding-top:15px;">User Group: </label>
                            <div class="col-sm-7" style="padding-top:10px;">
                                <select id="user_group" class="form-control" name="user_group">
                                	<!--<option value="---ALL---">  ALL USER GROUPS  </option>-->
                                    <?php
                                        for($i=0;$i<count($user_groups->user_group);$i++){
                                    ?>
                                        <option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="random" style="padding-top:15px;">Random Order: </label>
                            <div class="col-sm-5" style="padding-top:10px;">
                                <select name="random" id="random" class="form-control">
                                    <option value="N" selected>NO</option>
                                    <option value="Y">YES</option>
                                </select>
                            </div>
                        </div>
                        
                    </div><!-- end of step -->
                
                </form>

                </div> <!-- end of modal body -->

                <!-- NOTIFICATIONS -->
                <div id="notifications">
                    <div class="output-message-success" style="display:none;">
                        <div class="alert alert-success alert-dismissible" role="alert">
                          <strong>Success!</strong> New Music On Hold added !
                        </div>
                    </div>
                    <div class="output-message-error" style="display:none;">
                        <div class="alert alert-danger alert-dismissible" role="alert">
                          <span id="voicemail_result"></span>
                        </div>
                    </div>
                    <div class="output-message-incomplete" style="display:none;">
                        <div class="alert alert-danger alert-dismissible" role="alert">                            
                          Please fill-up all the fields <u>correctly</u> and do not leave any fields with (<strong> * </strong>) blank.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- The wizard button will be inserted here. -->
                    <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="submit_moh" value="Submit" style="display: inline-block;">
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
			$(document).ready(function() {
				$('#music-on-hold_table').dataTable();
				$('#moh-wizard').wizard();
				//$('#view-moh-modal').modal('show');

			// ADD FUNCTION
                $('#submit_moh').click(function(){
                
                $('#submit_moh').val("Saving, Please Wait.....");
                $('#submit_moh').prop("disabled", true);

                var validate = 0;

                var moh_id = $("#moh_id").val();
                var moh_name = $("#moh_name").val();

                if(moh_id == ""){
                    validate = 1;
                }

                if(moh_name == ""){
                    validate = 1;
                }

                    if(validate == 0){
                    //alert("Validated !");
                    
                        $.ajax({
                            url: "./php/AddMOH.php",
                            type: 'POST',
                            data: $("#create_moh").serialize(),
                            success: function(data) {
                              // console.log(data);
                                  if(data == 1){
                                        $('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                        window.setTimeout(function(){location.reload()},3000)
                                        $('#submit_moh').val("Submit");
                                        $('#submit_moh').prop("disabled", false);
                                  }
                                  else{
                                      $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                      $("#voicemail_result").html(data); 
                                      $('#submit_moh').val("Submit");
                                      $('#submit_moh').prop("disabled", false);
                                  }
                            }
                        });
                    
                    }else{
                        $('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                        validate_usergroup = 0;
                        $('#submit_moh').val("Submit");
                        $('#submit_moh').prop("disabled", false);
                    }
                });
                
                $(document).on('click','.edit-moh',function() {

					var moh_id = $(this).attr('data-id');

					$.ajax({
						url: "./php/ViewMOH.php",
						type: 'POST',
						data: { 
						      moh_id : moh_id,
						},
						dataType: 'json',
						success: function(data) {
						      $('.btn-update-moh-info').attr('data-id', data.moh_id);
						      $('.moh_name').val(data.moh_name);
						      $('.moh_status option[value="' + data.active + '"]').attr('selected','selected');
						      $('.moh_user_group option[value="' + data.user_group + '"]').attr('selected','selected');
						      $('.moh_rand_order option[value="' + data.random + '"]').attr('selected','selected');
						  
                              $('#view-moh-modal').modal('show');
						}
					});
				});
				
				$('.btn-update-moh-info').click(function(){
                    $('#update_button').html("<i class='fa fa-edit'></i> Updating...");
                    $('.btn-update-moh-info').attr("disabled", true);

					$.ajax({
						url: "./php/UpdateMOH.php",
						type: 'POST',
						data: { 
						      moh_id : $(this).attr('data-id'),
						      moh_name : $('.moh_name').val(),
						      user_group : $('.mog_user_group').val(),
						      active : $('.moh_status').val(),
						      random : $('.moh_rand_order').val(),
						},
						dataType: 'json',
						success: function(data) {
						      if (data.result == "success") {
							var message = '<div class="alert alert-success">';
							    message += '<strong>Success!</strong> Record successfully updated.';
							    message += '</div>';
                                $('.message_box').html(message);
                                $('.message_box').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                window.setTimeout(function(){location.reload()},2000)   
                                
                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      } else {
							var message = '<div class="alert alert-danger">';
							    message += '<strong>Error!</strong> Something went wrong with the update.';
							    message += '</div>';
                                $('.message_box').html(message);
                                $('.message_box').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                
                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      }
						      
						      
						}
					});
				});
				
				/**
				 * Delete validation modal
				 */
				 $(document).on('click','.delete-moh',function() {
				 	
				 	var id = $(this).attr('data-id');
				 	var name = $(this).attr('data-name');
				 	var action = "Music On Hold";

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
				 	//alert(id);
				 		
						$.ajax({
							url: "./php/DeleteMOH.php",
							type: 'POST',
							data: { 
								moh_id:id,
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
