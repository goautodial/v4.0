<?php	
/**
 * @file        settingsvoicemails.php
 * @brief       Manage Voicemails
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require_once('php/UIHandler.php');
	require_once('php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("portal_title"); ?> - <?php $lh->translateText("voice_mails"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
        
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
                            <legend><?php $lh->translateText("voice_mails"); ?></legend>
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

        <div class="action-button-circle" data-toggle="modal" data-target="#addvoicemail-modal">
            <?php print $ui->getCircleButton("voicemails", "plus"); ?>
        </div>

<?php
 /*
  * APIs needed for form
  */
	$user_groups = $api->API_getAllUserGroups();
?>
    <!-- ADD USER GROUP MODAL -->
        <div class="modal fade" id="addvoicemail-modal" tabindex="-1" aria-labelledby="addvoicemail-modal" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title animated bounceInRight" id="ingroup_modal">
                            <b><?php $lh->translateText("voice_mail_wizard"); ?> » <?php $lh->translateText("add_voice_mail"); ?> </b>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </h4>
                    </div>
                    <div class="modal-body">
                    
                    <form action="" method="POST" id="create_voicemail" name="create_voicemail" role="form">
						<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
						<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
                        <div class="row">
                    <!-- STEP 1 -->
                            <h4>
                                <?php $lh->translateText("create_voice_mail"); ?><br/>
                                <small><?php $lh->translateText("voicemail_sub_header"); ?></small>
                            </h4>
                            <fieldset>
                                <div class="form-group mt">
                                    <label class="col-sm-3 control-label" for="voicemail_id"><?php $lh->translateText("voicemail_id"); ?></label>
                                    <div class="col-sm-9 mb">
                                        <input type="number" name="voicemail_id" min="1" id="voicemail_id" class="form-control" placeholder="<?php $lh->translateText("Numbers Only"); ?>" minlength="2" maxlength="10">
                                    </div>
                                </div>
                                <div class="form-group">        
                                    <label class="col-sm-3 control-label" for="password"><?php $lh->translateText("password"); ?> </label>
                                    <div class="col-sm-9 mb">
                                        <input type="text" name="password" id="password" class="form-control" placeholder="<?php $lh->translateText("password"); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">        
                                    <label class="col-sm-3 control-label" for="name"><?php $lh->translateText("name"); ?></label>
                                    <div class="col-sm-9 mb">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="<?php $lh->translateText("name"); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="active"><?php $lh->translateText("active"); ?> </label>
                                    <div class="col-sm-9 mb">
                                        <select name="active" id="active" class="form-control">
                                            <option value="N" selected><?php $lh->translateText("go_no"); ?></option>
                                            <option value="Y"><?php $lh->translateText("go_yes"); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">        
                                    <label class="col-sm-3 control-label" for="email"><?php $lh->translateText("email"); ?> </label>
                                    <div class="col-sm-9 mb">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="<?php $lh->translateText("email"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="user_group"><?php $lh->translateText("user_groups"); ?> </label>
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
                            </fieldset>
                        </div><!-- end of step -->
                    
                    </form>

                    </div> <!-- end of modal body -->
                </div>
            </div>
        </div><!-- end of modal -->
        
    <!-- Forms and actions -->
        <?php print $ui->standardizedThemeJS(); ?>
        <!-- JQUERY STEPS-->
        <script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>

<script>
    $(document).ready(function() {

        /*********************
        ** INITIALIZATION
        *********************/

            // init data table
                $('#voicemails_table').dataTable();

            // init form wizard 
                var form = $("#create_voicemail"); 
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
                        $('#finish').text("<?php $lh->translateText("loading"); ?>");
                        $('#finish').attr("disabled", true);

                        // Submit form via ajax
                            $.ajax({
                                url: "./php/AddVoicemail.php",
                                type: 'POST',
                                data: $("#create_voicemail").serialize(),
                                success: function(data) {
									console.log(data);
									$('#finish').text("<?php $lh->translateText("submit"); ?>");
									$('#finish').prop("disabled", false);
									if(data == 1){
										  swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("add_voicemail_success"); ?>",type: "success"},function(){window.location.href = 'settingsvoicemails.php';});
									}
									else{
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
									}
                                }
                            });
                    }
                });
 
        /*********************
        ** EDIT EVENT
        *********************/
            $(document).on('click','.edit-voicemail',function() {
                var url = './editsettingsvoicemail.php';
                var vmid = $(this).attr('data-id');
                var form = $('<form action="' + url + '" method="post"><input type="hidden" name="vmid" value="'+vmid+'" /></form>');
                $('body').append(form);  // This line is not necessary
                $(form).submit();
            });

        /*********************
        ** DELETE EVENT
        *********************/  
            $(document).on('click','.delete-voicemail',function() {
                var id = $(this).attr('data-id');
                    swal({   
                        title: "<?php $lh->translateText("are_you_sure"); ?>",   
                        text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",   
                        type: "warning",   
                        showCancelButton: true,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "<?php $lh->translateText("confirm_delete_voicemail"); ?>",   
                        cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",   
                        closeOnConfirm: false,   
                        closeOnCancel: false 
                        }, 
                        function(isConfirm){   
                            if (isConfirm) { 
                                $.ajax({
                                    url: "./php/DeleteVoicemail.php",
                                    type: 'POST',
                                    data: { 
                                        voicemail_id: id,
										log_user: '<?=$_SESSION['user']?>',
										log_group: '<?=$_SESSION['usergroup']?>'
                                    },
                                    success: function(data) {
                                    //console.log(data);
                                        if(data == 1){
                                            swal({title: "<?php $lh->translateText("deleted"); ?>",text: "<?php $lh->translateText("voicemail_delete_success"); ?>",type: "success"},function(){window.location.href = 'settingsvoicemails.php';});
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

            // disable special characters on Usergroup ID   
                $('#voicemail_id').bind('keypress', function (event) {
                    var regex = new RegExp("^[ A-Za-z0-9]+$");
                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                    if (!regex.test(key)) {
                       event.preventDefault();
                       return false;
                    }
                });

            // disable special characters on Usergroup Name
                $('#name').bind('keypress', function (event) {
                    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                    if (!regex.test(key)) {
                       event.preventDefault();
                       return false;
                    }
                });
    });
</script>
        
        <?php print $ui->creamyFooter();?>
    </body>
</html>
