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
		<!-- Wizard Form style -->
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
                        <small><?php $lh->translateText("phones"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("phones"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("phones"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="phone_table">
									<?php print $ui->getPhonesList(); ?>
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
		<div class="action-button-circle" data-toggle="modal" data-target="#wizard-modal">
			<?php print $ui->getCircleButton("calls", "plus"); ?>
		</div>
	
<!-- MODAL -->
    <div class="modal fade" id="wizard-modal" tabindex="-1"aria-labelledby="T_Phones" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:20px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="T_Phones">Phone Wizard >> Add New User</h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form class="form-horizontal" role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="add_phones">Additional Phone(s):</label>
							<div class="col-sm-8">
								<select class="form-control" id="add_phones">
									<option>CUSTOM</option>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext">Starting Extension:</label>
							<div class="col-sm-8">
								<input type="number" name="start_ext" id="start_ext" placeholder="e.g. 8001" class="form-control" required>
							</div>
						</div>
					</div>
				<!-- end of step 1-->
				<!-- STEP 2 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="add_phones">Phone Extension/Login:	</label>
							<div class="col-sm-8">
								<select class="form-control" id="add_phones">
									<option>CUSTOM</option>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext">Phone Login Password:</label>
							<div class="col-sm-8">
								<input type="number" value="Go2014" name="start_ext" id="start_ext" class="form-control" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="start_ext">User Group:</label>
							<div class="col-sm-8">
								<select name="ip" id="ip" class="form-control" required>
									<option>ALL USER GROUPS</option>
									<option>ADMIN - GOAUTODIAL ADMINISTRATORS</option>
									<option>AGENTS - GOAUTODIAL AGENTS </option>
									<option>SUPERVISOR - SUPERVISOR </option>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="ip">Server IP:	</label>
							<div class="col-sm-8">
								<select name="ip" id="ip" class="form-control" required>
									<option></option>
								</select>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="name">Full Name:</label>
							<div class="col-sm-8">
								<input type="text" name="name" id="name" placeholder="Full Name" class="form-control" required>
							</div>
						</div>
						<div class="form-group">		
							<label class="col-sm-4 control-label" for="gmt">Local GMT:	</label>
							<div class="col-sm-8">
								<select name="gmt" id="gmt" placeholder="e.g. 8001" class="form-control" required>
									<option></option>
								</select>
							</div>
						</div>
					</div>
				<!-- end of step 2-->
				</form>
				
				<!-- NOTIFICATIONS -->

					<div class="output-message-success hide">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Success!</strong> New Agent added.
						</div>
					</div>
					<div class="output-message-error hide">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Error!</strong> Something went wrong please see input data on form or if agent already exists.
						</div>
					</div>

				</div> <!-- end of modal body -->
				
				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
			</div>
		</div>
	</div><!-- end of modal -->
		
		
		<!-- for wizard -->
		<script src="js/easyWizard.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#T_phones').dataTable();
				
				$("#wizard-modal").wizard({
					onfinish:function(){
						console.log("User Added!");
					}
				});
				
				/**
				  * Edit user details
				 */
				 $(".edit-phone").click(function(e) {
					e.preventDefault();
					var url = './editsettingsphones.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="extenid" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				/**
				 * Delete user.
				 */
				 $(".delete-phone").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var extenid = $(this).attr('href');
						$.post("./php/DeleteSettingsPhones.php", { extenid: extenid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_list"); ?>"); }
						});
					}
				 });
				
			});
		</script>
    </body>
</html>
