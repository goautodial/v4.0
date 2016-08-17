<?php	

    ###################################################
    ### Name: settingsvoicemails.php                ###
    ### Functions: Manage Voicemails                ###
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
        <title>Voicemails</title>
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
                        <small><?php $lh->translateText("voice_mails_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("voice_mails"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="scripts_table">
                            <legend>Voicemails</legend>
							<?php print $ui->getVoiceMails(); ?>
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

        <div class="action-button-circle" data-toggle="modal" data-target="#addvoicemail-modal">
            <?php print $ui->getCircleButton("voicemails", "plus"); ?>
        </div>

<?php
 /*
  * APIs needed for form
  */
   $user_groups = $ui->API_goGetUserGroupsList();
?>
    <!-- ADD USER GROUP MODAL -->
        <div class="modal fade" id="addvoicemail-modal" tabindex="-1" aria-labelledby="addvoicemail-modal" >
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="border-radius:5px;">

                <!-- Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title animate-header" id="ingroup_modal"><b>Voice Mail Wizard » Add New Voice Mail</b></h4>
                    </div>
                    <div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
                    
                    <form action="" method="POST" id="create_voicemail" name="create_voicemail" class="form-horizontal " role="form">
                    <!-- STEP 1 -->
                        <div class="wizard-step">
                            <div class="form-group mt">
                                <label class="col-sm-3 control-label" for="voicemail_id">Voicemail ID</label>
                                <div class="col-sm-9 mb">
                                    <input type="number" name="voicemail_id" id="voicemail_id" class="form-control" placeholder="Voicemail ID. This is a required field. Minimum of 2 numbers">
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-3 control-label" for="password">Password: </label>
                                <div class="col-sm-9 mb">
                                    <input type="text" name="password" id="password" class="form-control" placeholder="Password. This is a required field.">
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-3 control-label" for="name">Name </label>
                                <div class="col-sm-9 mb">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name. This is a required field.">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="active">Active </label>
                                <div class="col-sm-9 mb">
                                    <select name="active" id="active" class="form-control">
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-3 control-label" for="email">Email </label>
                                <div class="col-sm-9 mb">
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="user_group">User Group </label>
                                <div class="col-sm-9 mb">
                                    <select id="user_group" class="form-control" name="user_group">
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
                        </div><!-- end of step -->
                    
                    </form>

                    </div> <!-- end of modal body -->

                    <div class="modal-footer">
                        <!-- The wizard button will be inserted here. -->
                        <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                        <input type="submit" class="btn btn-primary" id="submit_voicemail" value="Submit" style="display: inline-block;">
                    </div>
                </div>
            </div>
        </div><!-- end of modal -->
        
    <!-- Forms and actions -->
        <?php print $ui->standardizedThemeJS(); ?>
        <script src="js/easyWizard.js" type="text/javascript"></script> 

    <script>
        $(document).ready(function() {
            $('#voicemails_table').dataTable();
            $("#addvoicemail-modal").wizard();

            // ADD FUNCTION
                $('#submit_voicemail').click(function(){
                $('#submit_voicemail').val("Saving, Please Wait.....");
                $('#submit_voicemail').prop("disabled", true);

                var validate_voicemail = 0;
                var voicemail_id = $("#voicemail_id").val();
                var name = $("#name").val();
                var password = $("#password").val();
                var email = $("#email").val();

                if(voicemail_id == ""){
                    validate_voicemail = 1;
                }

                if(name == ""){
                    validate_voicemail = 1;
                }

                if(password == ""){
                    validate_voicemail = 1;
                }

                if(email == ""){
                    validate_voicemail = 1;
                }

                var x = document.forms["create_voicemail"]["email"].value;
                var atpos = x.indexOf("@");
                var dotpos = x.lastIndexOf(".");
                if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
                    validate_voicemail = 1;
                }

                    if(validate_voicemail == 0){
                    //alert("Validated !");
                    
                        $.ajax({
                            url: "./php/AddVoicemail.php",
                            type: 'POST',
                            data: $("#create_voicemail").serialize(),
                            success: function(data) {
                              // console.log(data);
                                  if(data == 1){
                                        swal("Success!", "Voicemail Successfully Created!", "success");
                                        window.setTimeout(function(){location.reload()},3000)
                                        $('#submit_voicemail').val("Loading...");
                                  }
                                  else{
                                      sweetAlert("Oops...", "Something went wrong! "+data, "error");
                                      $('#submit_voicemail').val("Submit");
                                      $('#submit_voicemail').prop("disabled", false);
                                  }
                            }
                        });
                    
                    }else{
                        sweetAlert("Oops...", "Something went wrong!", "error");
                        validate_usergroup = 0;
                        $('#submit_voicemail').val("Submit");
                        $('#submit_voicemail').prop("disabled", false);
                    }
                });
            
            /**
              * Edit user details
             */
            $(document).on('click','.edit-voicemail',function() {
                var url = './editsettingsvoicemail.php';
                var vmid = $(this).attr('data-id');
                var form = $('<form action="' + url + '" method="post"><input type="hidden" name="vmid" value="'+vmid+'" /></form>');
                //$('body').append(form);  // This line is not necessary
                $(form).submit();
            });

            /**
             * Delete validation modal
             */
            $(document).on('click','.delete-voicemail',function() {
                var id = $(this).attr('data-id');
                    swal({   
                        title: "Are you sure?",   
                        text: "This action cannot be undone.",   
                        type: "warning",   
                        showCancelButton: true,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "Yes, delete this voicemail!",   
                        cancelButtonText: "No, cancel please!",   
                        closeOnConfirm: false,   
                        closeOnCancel: false 
                        }, 
                        function(isConfirm){   
                            if (isConfirm) { 
                                $.ajax({
                                    url: "./php/DeleteVoicemail.php",
                                    type: 'POST',
                                    data: { 
                                        voicemail_id:id,
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
