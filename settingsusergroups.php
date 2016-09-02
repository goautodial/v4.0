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
                        <div class="panel-body table" id="table_usergroups">
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


        <?php print $ui->creamyFooter(); ?>
            

        <!-- Fixed Action Button -->
        <div class="action-button-circle" data-toggle="modal" data-target="#addusergroup-modal">
            <?php print $ui->getCircleButton("calls", "user-plus"); ?>
        </div>

    <!-- ADD USER GROUP MODAL -->
        <div class="modal fade" id="addusergroup-modal" tabindex="-1" aria-labelledby="addusergroup-modal" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                <!-- Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title animated bounceInRight" id="ingroup_modal"><b>User Group Wizard » Add New User Group</b></h4>
                    </div>
                    <div class="modal-body">
                    
                    <form action="" method="POST" id="create_usergroup" role="form">
                        <div class="row">
                    <!-- STEP 1 -->
                            <h4>
                                <small></small>
                            </h4>
                            <fieldset>
                            <div class="form-group mt">
                                <label class="col-sm-3 control-label" for="usergroup_id">User Group</label>
                                <div class="col-sm-9 mb">
                                    <input type="text" name="usergroup_id" id="usergroup_id" class="form-control" placeholder="User Group" minlength="3" maxlength="20" title="Must be 3-40 alphanumeric characters." required>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-3 control-label" for="groupname">Group Name</label>
                                <div class="col-sm-9 mb">
                                    <input type="text" name="groupname" id="groupname" class="form-control" placeholder="Group Name" minlength="3" maxlength="40" title="Must be 3-40 alphanumeric characters." required>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-3 control-label" for="grouplevel">Group Level</label>
                                <div class="col-sm-9 mb">
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
                            <!-- end of step -->
                        </fieldset><!-- end of row -->
                    </form>

                    </div> <!-- end of modal body -->
                </div>
            </div>
        </div><!-- end of modal -->
        
    <!-- Forms and actions -->
        <?php print $ui->standardizedThemeJS(); ?> 
        <!-- JQUERY STEPS-->
        <script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>

<script type="text/javascript">
    $(document).ready(function() {

        /*********************
        ** INITIALIZATION
        *********************/
            // init data table
                $('#usergroups_table').dataTable();

            // init form wizard 
                var form = $("#create_usergroup"); 
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
                                url: "./php/AddUserGroup.php",
                                type: 'POST',
                                data: $("#create_usergroup").serialize(),
                                success: function(data) {
                                  // console.log(data);
                                      if(data == 1){
                                            swal({title: "Success",text: "Usergroup Successfully Created!",type: "success"},function(){window.location.href = 'settingsvoicemails.php';});
                                            $('#finish').val("Submit");
                                      }
                                      else{
                                          sweetAlert("Oops...", "Something went wrong! "+data, "error");
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
                $(document).on('click','.edit-usergroup',function() {
                    var url = './editsettingsusergroup.php';
                    var id = $(this).attr('data-id');
                    //alert(extenid);
                    var form = 
                    $('<form action="' + url + '" method="post"><input type="hidden" name="usergroup_id" value="'+id+'" /></form>');
                    //$('body').append(form);  // This line is not necessary
                    $(form).submit();
                });

        /*********************
        ** DELETE EVENT
        *********************/  
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
                                        swal({title: "Deleted",text: "Usergroup Successfully Deleted!",type: "success"},function(){window.location.href = 'settingsusergroups.php';});
                                        
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

            // disable special characters on Usergroup ID   
                $('#usergroup_id').bind('keypress', function (event) {
                    var regex = new RegExp("^[ A-Za-z0-9]+$");
                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                    if (!regex.test(key)) {
                       event.preventDefault();
                       return false;
                    }
                });

            // disable special characters on Usergroup Name
                $('#groupname').bind('keypress', function (event) {
                    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                    if (!regex.test(key)) {
                       event.preventDefault();
                       return false;
                    }
                });
    }); // end of document ready
</script>

        
    </body>
</html>
