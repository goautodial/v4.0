
<?php
	/**
		The MIT License (MIT)
		
		Copyright (c) 2015 Ignacio Nieto Carvajal
		
		Permission is hereby granted, free of charge, to any person obtaining a copy
		of this software and associated documentation files (the "Software"), to deal
		in the Software without restriction, including without limitation the rights
		to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
		copies of the Software, and to permit persons to whom the Software is
		furnished to do so, subject to the following conditions:
		
		The above copyright notice and this permission notice shall be included in
		all copies or substantial portions of the Software.
		
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
		THE SOFTWARE.
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
        <title>Creamy</title>
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
        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

	    <script type="text/javascript">
		    $(window).ready(function() {
		        $(".preloader").fadeOut("slow");
		    });
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
                        <?php $lh->translateText("administration"); ?>
                        <small><?php $lh->translateText("users_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-dashboard"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active"><?php $lh->translateText("administration"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("users"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="users_table">
									<?php print $ui->getAllUsersAsTable(); ?>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>


                    <div class="row">
                        <!-- left column -->
                        <section class="col-lg-12 connectedSortable">
                            <!-- general form elements -->
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("new_user"); ?></h3>
                                </div><!-- /.box-header -->
                                <!-- form start -->
                                <form role="form" id="createuser" name="createuser" method="post" action="" enctype="multipart/form-data">
                                    <div class="box-body">
	                                    <?php print $ui->getUserActivationEmailWarning(); ?>
										<div class="form-group">
											<div class="row">
											<div class="col-lg-6">
			                                    <div class="input-group">
			                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
			                                        <input type="text" id="name" name="name" class="form-control required" placeholder="<?php $lh->translateText("name"); ?>">
			                                    </div>
											</div><!-- /.col-lg-6 -->
											<div class="col-lg-6">
			                                    <div class="input-group">
			                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
			                                        <input type="text" id="email" name="email" class="form-control" placeholder="<?php $lh->translateText("email")." (".$lh->translationFor("optional").")"; ?>">
			                                    </div>
											</div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
											<div class="col-lg-6">
												<div class="input-group">
			                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
			                                        <input type="password" id="password1" name="password1" class="form-control required" placeholder="<?php $lh->translateText("password"); ?>">
			                                    </div>
											</div>
											<div class="col-lg-6">
			                                    <div class="input-group">
			                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
			                                        <input type="password" id="password2" name="password2" class="form-control required" placeholder="<?php $lh->translateText("repeat_password"); ?>">
			                                    </div>
											</div>
											</div>
										</div>
                                        <div class="form-group">
											<div class="row">
											<div class="col-lg-6">
	                                            <label for="exampleInputFile"><?php $lh->translateText("user_avatar")." (".$lh->translationFor("optional").")"; ?></label>
	                                            <input type="file" id="avatar" name="avatar">
	                                            <p class="help-block"><?php $lh->translateText("choose_image"); ?></p>
                                        	</div>
											<div class="col-lg-6">
	                                            <label for="role"><?php $lh->translateText("user_role"); ?></label>
												<p class="help-block"><?php print $ui->getUserRolesAsFormSelect(); ?></p>
                                        	</div>
											</div>
                                        </div>
	                                    <div  id="resultmessage" name="resultmessage" style="display:none">
	                                    </div>

                                    </div><!-- /.box-body -->

                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary"><?php $lh->translateText("create_user"); ?></button>
                                    </div>

                                </form>
                            </div><!-- /.box -->
                        </section><!--/.col (left) -->
                    </div>   <!-- /.row -->

				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

		<!-- change password of user from admin -->
	    <div class="modal fade" id="change-password-admin-dialog-modal" name="change-password-admin-dialog-modal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                    <h4 class="modal-title"><i class="fa fa-lock"></i> <?php $lh->translateText("change_password"); ?></h4>
	                </div>
	                <form action="" method="post" name="adminpasswordform" id="adminpasswordform">
	                    <div class="modal-body">
	                        <div class="form-group">
	                            <div class="input-group">
	                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
	                                <input name="new_password_1" id="new_password_1" type="password" class="form-control" placeholder="<?php $lh->translateText("insert_new_password"); ?>">
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <div class="input-group">
	                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
	                                <input name="new_password_2" id="new_password_2" type="password" class="form-control" placeholder="<?php $lh->translateText("insert_new_password_again"); ?>">
	                            </div>
	                        </div>
							<input type="hidden" id="usertochangepasswordid" name="usertochangepasswordid">
							<div id="changepasswordadminresult" name="changepasswordadminresult"></div>
	                    </div>
	                    <div class="modal-footer clearfix">
	                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="change-password-admin-cancel-button"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
	                    <button type="submit" class="btn btn-primary pull-left" id="change-password-admin-ok-button"><i class="fa fa-check-circle"></i> <?php $lh->translateText("change_password"); ?></button>
	                    </div>
	                </form>
	            </div><!-- /.modal-content -->
	        </div><!-- /.modal-dialog -->
	    </div><!-- /.modal -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			 $(document).ready(function() {
 			 	/** 
				 * Creates a new user.
			 	 */
				$("#createuser").validate({
					rules: {
						name: "required",
						//email: "required email",
						password1: "required",
					    password2: {
					      minlength: 8,
					      equalTo: "#password1"
					    }
			   		},
					submitHandler: function(e) {
							//submit the form
							$("#resultmessage").html();
							$("#resultmessage").hide();
							var formData = new FormData(e);
			
							$.ajax({
							  url: "./php/CreateUser.php",
							  data: formData,
							  processData: false,
							  contentType: false,
							  type: 'POST',
							  success: function(data) {
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
										<?php print $ui->reloadLocationJS(); ?>
									} else {
										<?php 
											$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("unable_create_user"), false, true);
											print $ui->fadingInMessageJS($errorMsg, "resultmessage"); 
										?>
									}
							    }
							});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Delete user.
				 */
				 $(".delete-action").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var user_id = $(this).attr('href');
						$.post("./php/DeleteUser.php", { userid: user_id } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_user"); ?>"); }
						});
					}
				 });
			
				 /**
				  * Edit user details
				  */
				 $(".edit-action").click(function(e) {
					e.preventDefault();
					var url = './edituser.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="userid" value="' + $(this).attr('href') + '" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
			
				 /**
				  * Deactivate user
				  */
				 $(".deactivate-user-action").click(function(e) {
					e.preventDefault();
					var user_id = $(this).attr('href');
					$.post("./php/SetUserStatus.php", { "userid": user_id, "status": 0 } ,function(data){
						if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
						else { alert ("<?php $lh->translateText("unable_set_user_status"); ?>"); }
					});
				 });
			
				 /**
				  * Activate user
				  */
				 $(".activate-user-action").click(function(e) {
					e.preventDefault();
					var user_id = $(this).attr('href');
					$.post("./php/SetUserStatus.php", { "userid": user_id, "status": 1 } ,function(data){
						if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
						else { alert ("<?php $lh->translateText("unable_set_user_status"); ?>"); }
					});
				 });
			
				 /**
				  * Show change user password.
				  */
				 $(".change-password-action").click(function(e) {
					e.preventDefault();
					var usertochangepasswordid = $(this).attr('href');
					$("#usertochangepasswordid").val(usertochangepasswordid);
					$("change-password-admin-ok-button").show();
					$("#changepasswordadminresult").html();
					$("#changepasswordadminresult").hide();
			
					$("#change-password-admin-dialog-modal").modal('show');
				 });
			
				 /**
				  * Change user password from admin.
				  */
				 $("#adminpasswordform").validate({
					rules: {
						new_password1: "required",
					    new_password2: {
						  required: true,
					      minlength: 8,
					      equalTo: "#password1"
					    }
			   		},
					submitHandler: function(e) {
						$.post("./php/ChangePasswordAdmin.php", //post
						$("#adminpasswordform").serialize(), 
							function(data){
								//if message is sent
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									$("#changepasswordadminresult").html('<div class="alert alert-success alert-dismissable"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b><?php $lh->translateText("success"); ?></b> <?php $lh->translateText("password_successfully_changed"); ?>');
									$("#changepasswordadminresult").fadeIn(); //show confirmation message
									$("change-password-admin-ok-button").fadeOut();
									$("#adminpasswordform")[0].reset();
			
								} else {
									$("#changepasswordadminresult").html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b><?php $lh->translateText("oups"); ?></b> <?php $lh->translateText("unable_change_password"); ?>: '+ data);
									$("#changepasswordadminresult").fadeIn(); //show confirmation message
								}
								//
							});
					}
				 });
			
			});
		</script>

        <script>
        	// load data.
            $(".textarea").wysihtml5();
		</script>

    </body>
</html>
