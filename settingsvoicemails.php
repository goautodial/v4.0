
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
        <title>Goautodial</title>
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
                        <small><?php $lh->translateText("voice_mails"); ?></small>
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
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title">Voicemails</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="scripts_table">
									<?php print $ui->getVoiceMails(); ?>
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
                        <h4 class="modal-title animate-header" id="ingroup_modal"><b>Voice Mail Wizard » Add New User Group</b></h4>
                    </div>
                    <div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
                    
                    <form action="" method="POST" id="create_voicemail" name="create_voicemail" class="form-horizontal " role="form">
                    <!-- STEP 1 -->
                        <div class="wizard-step">
                            <div class="row" style="padding-top:10px;padding-bottom:10px;">
                                <p class="col-sm-12"><small><i> - - - All fields with ( </i></small> <b>*</b> <small><i> ) are Required Field.  - - -</i></small></p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="voicemail_id" style="padding-top:10px;">* Voicemail ID:</label>
                                <div class="col-sm-7">
                                    <input type="number" name="voicemail_id" id="voicemail_id" class="form-control" placeholder="Voicemail ID">
                                    <span  class="text-red"><small><i>* Minimum of 2 numbers..</i></small></span>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="password" style="padding-top:15px;">* Password: </label>
                                <div class="col-sm-7" style="padding-top:10px;">
                                    <input type="text" name="password" id="password" class="form-control" placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="name" style="padding-top:15px;">* Name: </label>
                                <div class="col-sm-7" style="padding-top:10px;">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="active" style="padding-top:15px;">Active: </label>
                                <div class="col-sm-5" style="padding-top:10px;">
                                    <select name="active" id="active" class="form-control">
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="email" style="padding-top:15px;">* Email: </label>
                                <div class="col-sm-7" style="padding-top:10px;">
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="user_group" style="padding-top:15px;">User Group: </label>
                                <div class="col-sm-7" style="padding-top:10px;">
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

                    <!-- NOTIFICATIONS -->
                    <div id="notifications">
                        <div class="output-message-success" style="display:none;">
                            <div class="alert alert-success alert-dismissible" role="alert">
                              <strong>Success!</strong> New Voice Mail added !
                            </div>
                        </div>
                        <div class="output-message-error" style="display:none;">
                            <div class="alert alert-danger alert-dismissible" role="alert">
                              <span id="voicemail_result"></span>
                            </div>
                        </div>
                        <div class="output-message-incomplete" style="display:none;">
                            <div class="alert alert-danger alert-dismissible" role="alert">                            
                              Please fill-up all the fields <u>correctly</u>, <u>input a valid email address</u> and do not leave any fields with (<strong> * </strong>) blank.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- The wizard button will be inserted here. -->
                        <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                        <input type="submit" class="btn btn-primary" id="submit_voicemail" value="Submit" style="display: inline-block;">
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
            $('#voicemails_table').dataTable();
            $("#addvoicemail-modal").wizard();

            // ADD FUNCTION
                $('#submit_voicemail').click(function(){
                
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
                                        $('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                        window.setTimeout(function(){location.reload()},3000)
                                  }
                                  else{
                                      $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                      $("#voicemail_result").html(data); 
                                  }
                            }
                        });
                    
                    }else{
                        $('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                        validate_usergroup = 0;
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
                
                var voicemail_id = $(this).attr('data-id');
                var voicemail_name = $(this).attr('data-name');
                var action = "Voice Mail";

                $('.id-delete-label').attr("data-id", voicemail_id);
                $('.id-delete-label').attr("data-action", action);

                $(".delete_extension").text(voicemail_name);
                $(".action_validation").text(action);

                $('#delete_validation_modal').modal('show');
             });

             $(document).on('click','#delete_yes',function() {
                
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action');

                $('#id_span').html(id);

                    $.ajax({
                        url: "./php/DeleteVoicemail.php",
                        type: 'POST',
                        data: { 
                            voicemail_id:id,
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
