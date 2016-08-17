<?php	

    ###################################################
    ### Name: settingsusergroups.php                ###
    ### Functions: Manage Usergroups                ###
    ### Copyright: GOAutoDial Ltd. (c) 2011-2016    ###
    ### Version: 4.0                                ###
    ### Written by: Alexander Jim H. Abenoja        ###
    ### License: AGPLv2                             ###
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
        <title>User Groups</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?> 

        <?php print $ui->creamyThemeCSS(); ?>
        
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
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
                        <small><?php $lh->translateText("user_groups_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("user_groups"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="scripts_table">
                            <legend><?php $lh->translateText("user_groups"); ?></legend>
							<?php print $ui->goGetUserGroupsList(); ?>
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

        <div class="action-button-circle" data-toggle="modal" data-target="#addusergroup-modal">
            <?php print $ui->getCircleButton("calls", "user-plus"); ?>
        </div>

    <!-- ADD USER GROUP MODAL -->
        <div class="modal fade" id="addusergroup-modal" tabindex="-1" aria-labelledby="addusergroup-modal" >
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="border-radius:5px;">

                <!-- Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title animate-header" id="ingroup_modal"><b>User Group Wizard » Add New User Group</b></h4>
                    </div>
                    <div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
                    
                    <form action="" method="POST" id="create_usergroup" class="form-horizontal " role="form">
                    <!-- STEP 1 -->
                        <div class="wizard-step">
                            <div class="row" style="padding-top:10px;padding-bottom:10px;">
                                <p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="usergroup_id">* User Group:</label>
                                <div class="col-sm-7">
                                    <input type="text" name="usergroup_id" id="usergroup_id" class="form-control" placeholder="User Group" minlength="3">
                                    <span  class="text-red"><small><i>* Minimum of 3 characters..</i></small></span>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="groupname">* Group Name: </label>
                                <div class="col-sm-7">
                                    <input type="text" name="groupname" id="groupname" class="form-control" placeholder="Group Name">
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="grouplevel" style="padding-top:15px;">Group Level: </label>
                                <div class="col-sm-3" style="padding-top:10px;">
                                    <select id="grouplevel" name="grouplevel" class="form-control">
                                        <option value="1"> 1 </option>
                                        <option value="2"> 2 </option> 
                                        <option value="3"> 3 </option> 
                                        <option value="4"> 4 </option> 
                                        <option value="5"> 5 </option>                                         
                                        <option value="6"> 6 </option> 
                                        <option value="7"> 7 </option> 
                                        <option value="8" selected> 8 </option> 
                                        <option value="9"> 9 </option> 
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
                              <strong>Success!</strong> New User Group added !
                            </div>
                        </div>
                        <div class="output-message-error" style="display:none;">
                            <div class="alert alert-danger alert-dismissible" role="alert">
                              <span id="usergroup_result"></span>
                            </div>
                        </div>
                        <div class="output-message-incomplete" style="display:none;">
                            <div class="alert alert-danger alert-dismissible" role="alert">
                              Please fill-up all the fields correctly and do not leave any fields with (<strong> * </strong>) blank.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- The wizard button will be inserted here. -->
                        <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                        <input type="submit" class="btn btn-primary" id="submit_usergroup" value="Submit" style="display: inline-block;">
                    </div>
                </div>
            </div>
        </div><!-- end of modal -->
        
    <!-- Forms and actions -->
        <?php print $ui->standardizedThemeJS(); ?> 
        <script src="js/easyWizard.js" type="text/javascript"></script> 

        <script>
            $(document).ready(function() {
                $('#usergroups_table').dataTable();
                $("#addusergroup-modal").wizard({});

                // ajax commands for modals -
                $('#submit_usergroup').click(function(){
                $('#submit_usergroup').val("Saving, Please Wait.....");
                $('#submit_usergroup').prop("disabled", true);

                var validate_usergroup = 0;
                var usergroup_id = $("#usergroup_id").val();
                var groupname = $("#groupname").val();
                var group = $("#grouplevel").val();
                
                if(usergroup_id == ""){
                    validate_usergroup = 1;
                }

                if(groupname == ""){
                    validate_usergroup = 1;
                }

                    if(validate_usergroup == 0){
                    //alert("Validated !");
                    
                        $.ajax({
                            url: "./php/AddUserGroup.php",
                            type: 'POST',
                            data: $("#create_usergroup").serialize(),
                            success: function(data) {
                              // console.log(data);
                                  if(data == 1){
                                        swal("Success!", "Usergroup Successfully Created!", "success");
                                        window.setTimeout(function(){location.reload()},3000)
                                        $('#submit_usergroup').val("Loading");
                                  }
                                  else{
                                      sweetAlert("Oops...", "Something went wrong! "+data, "error");
                                      $('#submit_usergroup').val("Submit");
                                      $('#submit_usergroup').prop("disabled", false);
                                  }
                            }
                        });
                    
                    }else{
                        sweetAlert("Oops...", "Something went wrong!", "error");
                        validate_usergroup = 0;
                        $('#submit_usergroup').val("Submit");
                        $('#submit_usergroup').prop("disabled", false);
                    }
                });
                
                /**
                  * Edit user group details
                 */
                $(document).on('click','.edit-usergroup',function() {
                    var url = './editsettingsusergroup.php';
                    var id = $(this).attr('data-id');
                    //alert(extenid);
                    var form = 
                    $('<form action="' + url + '" method="post"><input type="hidden" name="usergroup_id" value="'+id+'" /></form>');
                    //$('body').append(form);  // This line is not necessary
                    $(form).submit();
                });

                /**
                 * Delete validation modal
                 */
                 $(document).on('click','.delete-usergroup',function() {
                    var id = $(this).attr('data-id');
                    swal({   
                        title: "Are you sure?",   
                        text: "This action cannot be undone.",   
                        type: "warning",   
                        showCancelButton: true,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "Yes, delete this usergroup!",   
                        cancelButtonText: "No, cancel please!",   
                        closeOnConfirm: false,   
                        closeOnCancel: false 
                        }, 
                        function(isConfirm){   
                            if (isConfirm) { 
                                $.ajax({
                                url: "./php/DeleteUserGroup.php",
                                type: 'POST',
                                data: { 
                                    usergroup_id:id,
                                },
                                success: function(data) {
                                console.log(data);
                                    if(data == 1){
                                        swal("Success!", "Usergroup Successfully Deleted!", "success");
                                        window.setTimeout(function(){location.reload()},1000)
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
            });
        </script>

        <?php print $ui->creamyFooter();?>
    </body>
</html>
