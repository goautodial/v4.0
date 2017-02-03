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
        <title><?php $lh->translateText('portal_title'); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php print $ui->standardizedThemeCSS(); ?>

        <?php print $ui->creamyThemeCSS(); ?>

		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		
		<!-- SELECT2-->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
   		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>
		
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
                        <small><?php $lh->translateText("servers"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("servers"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="servers">
                            <legend><?php $lh->translateText("servers"); ?></legend>
							<?php print $ui->getServerList(); ?>
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
		
       <!-- Fixed Action Button -->
        <div class="action-button-circle" data-toggle="modal" data-target="#addserver-modal">
            <?php print $ui->getCircleButton("calls", "user-plus"); ?>
        </div>
		
	<?php
		$user_groups = $ui->API_goGetUserGroupsList();
	?>
    <!-- ADD USER GROUP MODAL -->
        <div class="modal fade" id="addserver-modal" tabindex="-1" aria-labelledby="addserver-modal" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title animated bounceInRight">
                            <b>Server Wizard » Add New Server</b>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </h4>
                    </div>
                    <div class="modal-body">
                    
                    <form action="" method="POST" id="create_server" role="form">
						<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
						<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
                        <div class="row">
                    <!-- STEP 1 -->
                            <h4>
                                Create a Server<br/>
                                <small>Enter basic settings</small>
                            </h4>
                            <fieldset>
                            <div class="form-group mt">
                                <label class="col-sm-4 control-label" for="server_id">Server ID</label>
                                <div class="col-sm-8 mb">
                                    <input type="text" name="server_id" id="server_id" class="form-control" placeholder="Server ID" maxlength="10" title="Must be 1-10 alphanumeric characters." required>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="server_description">Server Description</label>
                                <div class="col-sm-8 mb">
                                    <input type="text" name="server_description" id="server_description" class="form-control" placeholder="Server Description" maxlength="255" title="Must be 1-255 alphanumeric characters." required>
                                </div>
                            </div>
							<div class="form-group">        
                                <label class="col-sm-4 control-label" for="server_ip">Server IP</label>
                                <div class="col-sm-8 mb">
                                    <input type="text" name="server_ip" id="server_ip" class="form-control" data-inputmask="'alias': 'ip'" data-mask="" placeholder="Server IP" required>
								</div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="server_ip">Asterisk Version</label>
                                <div class="col-sm-8 mb">
									<input type="text" class="form-control" name="asterisk_version" id="asterisk_version" maxlength="20" size="20" placeholder="Asterisk Version" required>
								</div>
                            </div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="active">Active </label>
								<div class="col-sm-8 mb">
									<select name="active" id="active" class="form-control">
										<option value="Y" selected>Yes</option>
										<option value="N" >No</option>
									</select>
								</div>
							</div>
							<div class="form-group mt">
								<label class="col-sm-4 control-label" for="user_group">User Group</label>
								<div class="col-sm-8 mb">
									<select id="user_group" class="form-control select2" name="user_group" style="width:100%;">
										<option value="---ALL---">ALL USERGROUPS</option>
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>" <?php if($user_groups->user_group[$i] == "AGENT"){echo "selected";}?>>  <?php echo $user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
							<!-- end of step -->
							</fieldset>
						</div><!-- end of row -->
                    </form>

                    </div> <!-- end of modal body -->
                </div>
            </div>
        </div><!-- end of modal -->
		
		
    <!-- Forms and actions -->
        <?php print $ui->standardizedThemeJS(); ?> 
        <!-- JQUERY STEPS-->
        <script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	
		<script src="js/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				/*********************
				** INITIALIZATION
				*********************/
					// init data table
						$('#servers_table').dataTable({
							"aoColumnDefs": [{
								"bSearchable": false,
								"aTargets": [ 4 ]
							},{
								"bSortable": false,
								"aTargets": [ 4 ]
							}]
						});
		
					// init form wizard 
						var form = $("#create_server"); 
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
										url: "./php/AddServer.php",
										type: 'POST',
										data: $("#create_server").serialize(),
										success: function(data) {
										  // console.log(data);
										$('#finish').text("Submit");
										$('#finish').attr("disabled", false);
											if(data == "success"){
												swal({title: "Success",text: "Server Successfully Created!",type: "success"},function(){window.location.href = 'settingsservers.php';});
											}
											else{
												sweetAlert("Oops...", "Something went wrong! "+data, "error");
											}
										}
									});
							}
						});
						
				/*********************
				** EDIT EVENT
				*********************/
						$(document).on('click','.edit-server',function() {
							var url = './editsettingsserver.php';
							var id = $(this).attr('data-id');
							//alert(extenid);
							var form = 
							$('<form action="' + url + '" method="post"><input type="hidden" name="id" value="'+id+'" /></form>');
							//$('body').append(form);  // This line is not necessary
							$(form).submit();
						});
		
				/*********************
				** DELETE EVENT
				*********************/  
						 $(document).on('click','.delete-server',function() {
							var id = $(this).attr('data-id');
							swal({   
								title: "Are you sure?",   
								text: "This action cannot be undone.",   
								type: "warning",   
								showCancelButton: true,   
								confirmButtonColor: "#DD6B55",   
								confirmButtonText: "Yes, delete this server!",   
								cancelButtonText: "No, cancel please!",   
								closeOnConfirm: false,   
								closeOnCancel: false 
								}, 
								function(isConfirm){   
									if (isConfirm) { 
										$.ajax({
										url: "./php/DeleteServer.php",
										type: 'POST',
										data: {
											server_id: id,
											log_user: '<?=$_SESSION['user']?>',
											log_group: '<?=$_SESSION['usergroup']?>'
										},
										success: function(data) {
										console.log(data);
											if(data == "success"){
												swal({title: "Deleted",text: "Server Successfully Deleted!",type: "success"},function(){window.location.href = 'settingsservers.php';});
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
						$('#server_id').bind('keypress', function (event) {
							var regex = new RegExp("^[A-Za-z0-9]+$");
							var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							if (!regex.test(key)) {
							   event.preventDefault();
							   return false;
							}
						});
		
					// disable special characters on Usergroup Name
						$('#server_description').bind('keypress', function (event) {
							var regex = new RegExp("^[a-zA-Z0-9 ]+$");
							var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
							if (!regex.test(key)) {
							   event.preventDefault();
							   return false;
							}
						});
						
				//input mask
					$("[data-mask]").inputmask();
				
				/* initialize select2 */
					$('.select2').select2({
						theme: 'bootstrap'
					});
			}); // end of document ready
		</script>
		
        <?php print $ui->creamyFooter(); ?>
		
    </body>
</html>
