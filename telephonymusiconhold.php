<?php

    ###########################################################
    ### Name: telephonymusiconhold.php                      ###
    ### Functions: Manage MOH                               ###
    ### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
    ### Version: 4.0                                        ###
    ### Written by: Alexander Abenoja & Noel Umandap        ###
    ### License: AGPLv2                                     ###
    ###########################################################

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
        <title>Music On Hold</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>

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

        <!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	    <!-- Bootstrap Player -->
	    <script src="js/bootstrap-player.js" type="text/javascript"></script>
        <!-- SELECT2-->
        <link rel="stylesheet" src="js/dashboard/select2/dist/css/select2.css">
        <link rel="stylesheet" src="js/dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
        <!-- SELECT2-->
        <script src="js/dashboard/select2/dist/js/select2.js"></script>
        
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
                        <small><?php $lh->translateText("music_on_hold_management"); ?></small>
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
                    <div class="panel panel-default">
                        <div class="panel-body table" id="recording_table">
                            <legend><?php $lh->translateText("music_on_hold"); ?></h3></legend>
					       <?php print $ui->getListAllMusicOnHold(); ?>
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
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
                <!-- STEP 1 -->
                    <div class="wizard-step">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="moh_id">Music On Hold ID:</label>
                            <div class="col-sm-7 mb">
                                <input type="text" name="moh_id" id="moh_id" class="form-control" placeholder="Music on Hold ID. This is a required field.">
                                <span  class="text-red"><small><i>* Minimum of 3 characters..</i></small></span>
                            </div>
                        </div>
                        <div class="form-group">        
                            <label class="col-sm-4 control-label" for="moh_name">Music On Hold Name: </label>
                            <div class="col-sm-7 mb">
                                <input type="text" name="moh_name" id="moh_name" class="form-control" placeholder="Music On Hold Name. This is a required field.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="active">Status: </label>
                            <div class="col-sm-5 mb">
                                <select name="active" id="active" class="form-control">
                                    <option value="N" selected>INACTIVE</option>
                                    <option value="Y">ACTIVE</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="user_group" style="padding-top:15px;">User Group: </label>
                            <div class="col-sm-7 mb">
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
                            <label class="col-sm-4 control-label" for="random">Random Order: </label>
                            <div class="col-sm-5 mb">
                                <select name="random" id="random" class="form-control">
                                    <option value="N" selected>NO</option>
                                    <option value="Y">YES</option>
                                </select>
                            </div>
                        </div>
                        
                    </div><!-- end of step -->
                
                </form>

                </div> <!-- end of modal body -->

                <div class="modal-footer">
                    <!-- The wizard button will be inserted here. -->
                    <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="submit_moh" value="Submit" style="display: inline-block;">
                </div>
            </div>
        </div>
    </div><!-- end of modal -->

		<!-- Forms and actions -->
        <?php print $ui->standardizedThemeJS(); ?>
		<script src="js/easyWizard.js" type="text/javascript"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#music-on-hold_table').dataTable();
				$('#moh-wizard').wizard();

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
                        $.ajax({
                            url: "./php/AddMOH.php",
                            type: 'POST',
                            data: $("#create_moh").serialize(),
                            success: function(data) {
                              // console.log(data);
                                  if(data == 1){
                                        swal("Success!", "Music On Hold Successfully Created!", "success");
                                        window.setTimeout(function(){location.reload()},3000)
                                        $('#submit_moh').val("Submit");
                                        $('#submit_moh').prop("disabled", false);
                                  }
                                  else{
                                      sweetAlert("Oops...", "Something went wrong!"+data, "error");
                                      $('#submit_moh').val("Submit");
                                      $('#submit_moh').prop("disabled", false);
                                  }
                            }
                        });
                    
                    }else{
                        sweetAlert("Oops...", "Something went wrong!", "error");
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
							  log_user: '<?=$_SESSION['user']?>',
							  log_group: '<?=$_SESSION['usergroup']?>'
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
							  log_user: '<?=$_SESSION['user']?>',
							  log_group: '<?=$_SESSION['usergroup']?>'
						},
						dataType: 'json',
						success: function(data) {
						      if (data.result == "success") {
							    swal("Success!", "Music On Hold Successfully Updated!", "success");
                                window.setTimeout(function(){location.reload();},2000);
                                
                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      } else {
    							sweetAlert("Oops...", "Something went wrong! "+data, "error");
                                
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
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
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
                                    url: "./php/DeleteMOH.php",
                                    type: 'POST',
                                    data: { 
                                        moh_id: id,
										log_user: log_user,
										log_group: log_group
                                    },
                                    success: function(data) {
                                    console.log(data);
                                        if(data == "<?=CRM_DEFAULT_SUCCESS_RESPONSE?>"){
                                           swal("Success!", "Music On Hold Successfully Deleted!", "success");
                                           window.setTimeout(function(){location.reload();},1000);
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
                
                /* initialize select2 */
                    $('.select2-1').select2({
                        theme: 'bootstrap'
                    });
			});
		</script>

        <?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        <?php print $ui->creamyFooter(); ?>
    </body>
</html>
