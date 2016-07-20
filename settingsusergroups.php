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
        <title>User Groups</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
        <!-- Circle Buttons style -->
        <link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
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
                        <small><?php $lh->translateText("user_groups"); ?></small>
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
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("user_groups"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="scripts_table">
									<?php print $ui->goGetUserGroupsList(); ?>
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
                                        $('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                        window.setTimeout(function(){location.reload()},3000)
                                        $('#submit_usergroup').val("Loading");
                                  }
                                  else{
                                      $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                      $("#usergroup_result").html(data);
                                      $('#submit_usergroup').val("Submit");
                                      $('#submit_usergroup').prop("disabled", false);
                                  }
                            }
                        });
                    
                    }else{
                        $('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
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
                    var form = $('<form action="' + url + '" method="post"><input type="hidden" name="usergroup_id" value="'+id+'" /></form>');
                    //$('body').append(form);  // This line is not necessary
                    $(form).submit();
                });

                /**
                 * Delete validation modal
                 */
                 $(document).on('click','.delete-usergroup',function() {
                    
                    var usergroup_id = $(this).attr('data-id');
                    var usergroup_name = $(this).attr('data-name');
                    var action = "User Group";

                    $('.id-delete-label').attr("data-id", usergroup_id);
                    $('.id-delete-label').attr("data-action", action);

                    $(".delete_extension").text(usergroup_name);
                    $(".action_validation").text(action);

                    $('#delete_validation_modal').modal('show');
                 });

                 $(document).on('click','#delete_yes',function() {
                    
                    var id = $(this).attr('data-id');
                    var action = $(this).attr('data-action');

                    $('#id_span').html(id);

                        $.ajax({
                            url: "./php/DeleteUserGroup.php",
                            type: 'POST',
                            data: { 
                                usergroup_id:id,
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
