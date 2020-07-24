<?php	
/**
 * @file        settingsservers.php
 * @brief       Manage servers
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

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
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
	
	$perm = $api->goGetPermissions('servers', $_SESSION['usergroup']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("servers"); ?></title>
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
                <?php if ($perm->servers_read !== 'N') { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="servers">
                            <legend><?php $lh->translateText("servers"); ?></legend>
							<?php print $ui->getServerList($perm); ?>
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
        <div class="action-button-circle <?php if ($perm->servers_create == 'N') { echo "hidden"; } ?>" data-toggle="modal" data-target="#addserver-modal">
            <?php print $ui->getCircleButton("calls", "user-plus"); ?>
        </div>
		
	<?php
		$user_groups = $api->API_getAllUserGroups();
	?>
    <!-- ADD USER GROUP MODAL -->
        <div class="modal fade" id="addserver-modal" tabindex="-1" aria-labelledby="addserver-modal" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title animated bounceInRight">
                            <b><?php $lh->translateText("server_wizard"); ?> » <?php $lh->translateText("add_server_wizard"); ?> </b>
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
                                <?php $lh->translateText("create_server_header"); ?><br/>
                                <small><?php $lh->translateText("create_server_sub_header"); ?></small>
                            </h4>
                            <fieldset>
                            <div class="form-group mt">
                                <label class="col-sm-4 control-label" for="server_id"><?php $lh->translateText("server_id"); ?></label>
                                <div class="col-sm-8 mb">
                                    <input type="text" name="server_id" id="server_id" class="form-control" placeholder="<?php $lh->translateText("server_id"); ?>" maxlength="10" title="Must be 1-10 alphanumeric characters." required>
                                </div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="server_description"><?php $lh->translateText("server_description"); ?></label>
                                <div class="col-sm-8 mb">
                                    <input type="text" name="server_description" id="server_description" class="form-control" placeholder="<?php $lh->translateText("server_description"); ?>" maxlength="255" title="Must be 1-255 alphanumeric characters." required>
                                </div>
                            </div>
							<div class="form-group">        
                                <label class="col-sm-4 control-label" for="server_ip"><?php $lh->translateText("server_ip"); ?></label>
                                <div class="col-sm-8 mb">
                                    <input type="text" name="server_ip" id="server_ip" class="form-control" data-inputmask="'alias': 'ip'" data-mask="" placeholder="<?php $lh->translateText("server_ip"); ?>" required>
								</div>
                            </div>
                            <div class="form-group">        
                                <label class="col-sm-4 control-label" for="server_ip"><?php $lh->translateText("asterisk_version"); ?></label>
                                <div class="col-sm-8 mb">
									<input type="text" class="form-control" name="asterisk_version" id="asterisk_version" maxlength="20" size="20" placeholder="<?php $lh->translateText("asterisk_version"); ?>" required>
								</div>
                            </div>
							<div class="form-group">
								<label class="col-sm-4 control-label" for="active"><?php $lh->translateText("active"); ?> </label>
								<div class="col-sm-8 mb">
									<select name="active" id="active" class="form-control">
										<option value="Y" selected><?php $lh->translateText("go_yes"); ?></option>
										<option value="N" ><?php $lh->translateText("go_no"); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group mt">
								<label class="col-sm-4 control-label" for="user_group"><?php $lh->translateText("user_groups"); ?></label>
								<div class="col-sm-8 mb">
									<select id="user_group" class="form-control select2" name="user_group" style="width:100%;">
										<option value="---ALL---"><?php $lh->translateText("all_usergroups"); ?></option>
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
        <script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	
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
								$('#finish').text("<?php $lh->translateText("loading"); ?>");
								$('#finish').attr("disabled", true);
		
								// Submit form via ajax
								$.ajax({
									url: "./php/AddServer.php",
									type: 'POST',
									data: $("#create_server").serialize(),
									success: function(data) {
										console.log(data);
										$('#finish').text("<?php $lh->translateText("submit"); ?>");
										$('#finish').attr("disabled", false);
										if(data == 1){
											swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("add_server_success"); ?>",type: "success"},function(){window.location.href = 'settingsservers.php';});
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
						$(document).on('click','.edit-server',function() {
							var url = './editsettingsserver.php';
							var id = $(this).attr('data-id');
							//alert(extenid);
							var form = 
							$('<form action="' + url + '" method="post"><input type="hidden" name="server_id" value="'+id+'" /></form>');
							$('body').append(form);  // This line is not necessary
							$(form).submit();
						});
		
				/*********************
				** DELETE EVENT
				*********************/  
						 $(document).on('click','.delete-server',function() {
							var id = $(this).attr('data-id');
							swal({   
								title: "<?php $lh->translateText("are_you_sure"); ?>",   
								text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",   
								type: "warning",   
								showCancelButton: true,   
								confirmButtonColor: "#DD6B55",   
								confirmButtonText: "<?php $lh->translateText("confirm_delete_server"); ?>",   
								cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",   
								closeOnConfirm: false,   
								closeOnCancel: false 
								}, 
								function(isConfirm){   
									if (isConfirm) { 
										$.ajax({
										url: "./php/DeleteServer.php",
										type: 'POST',
										data: { server_id: id },
										success: function(data) {
										console.log(data);
											if(data == 1){
												swal({title: "<?php $lh->translateText("deleted"); ?>",text: "<?php $lh->translateText("server_delete_success"); ?>",type: "success"},function(){window.location.href = 'settingsservers.php';});
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
					$('.select2').select2({ theme: 'bootstrap' });
					$.fn.select2.defaults.set( "theme", "bootstrap" );
			}); // end of document ready
		</script>
		
        <?php print $ui->creamyFooter(); ?>
		
    </body>
</html>
