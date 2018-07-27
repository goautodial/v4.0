<?php
/**
 * @file 		editsettingsvoicemail.php
 * @brief 		Modify Voicemail settings
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja
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
**/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

$vmid = NULL;
if (isset($_POST["vmid"])) {
	$vmid = $_POST["vmid"];
}else{
	header("location: settingsvoicemails.php");
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("edit_voice_mail"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       	
       	<?php print $ui->standardizedThemeCSS(); ?> 

        <?php print $ui->creamyThemeCSS(); ?>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
    </style>

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
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("edit_voice_mail"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["vmid"])){
						?>	
							<li><a href="./settingsvoicemails.php"><?php $lh->translateText("Voicemail"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                
					<!-- standard custom edition form -->
					<?php
						$errormessage = NULL;						
						$output = $api->API_getVoicemailInfo($vmid);
						if ($output->result=="success") {
					?>
                <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
                    
                    <div class="panel-body">
						<legend>MODIFY VOICEMAIL ID: <u><?php echo $output->data->voicemail_id;?></u></legend>
	
							<form id="modifyform">
								<input type="hidden" name="modifyid" value="<?php echo $vmid;?>">
								<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>" />
								<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>" />
							
						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> <?php $lh->translateText("basic_settings"); ?></a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
				                	<fieldset>
										<div class="form-group mt">
											<label for="password" class="col-sm-3 control-label"><?php $lh->translateText("your_password"); ?></label>
											<div class="col-sm-9 mb">
												<input type="text" class="form-control" name="password" id="password" placeholder="Password" value="<?php echo $output->data->password;?>">
											</div>
										</div>
										<div class="form-group">
											<label for="fullname" class="col-sm-3 control-label"><?php $lh->translateText("name"); ?></label>
											<div class="col-sm-9 mb">
												<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->data->fullname;?>">
											</div>
										</div>
										<div class="form-group">
											<label for="email" class="col-sm-3 control-label"><?php $lh->translateText("email"); ?></label>
											<div class="col-sm-9 mb">
												<input type="text" class="form-control" name="email" id="email" value="<?php echo $output->data->email;?>">
											</div>
										</div>
									
										<div class="form-group">
											<label for="active" class="col-sm-3 control-label"><?php $lh->translateText("active"); ?></label>
											<div class="col-sm-9 mb">
												<select class="form-control" name="active" id="active">
												<?php
													$active = NULL;
													if($output->data->active == "Y"){
														$active .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
													}else{
														$active .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
													}
													
													if($output->data->active == "N" || $output->data->active == NULL){
														$active .= '<option value="N" selected> '.$lh->translationFor("go_no").' </option>';
													}else{
														$active .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
													}
													echo $active;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="delete_vm_after_email" class="col-sm-3 control-label"><?php $lh->translateText("delete_voicemail_after_email"); ?></label>
											<div class="col-sm-9 mb">
												<select class="form-control" name="delete_vm_after_email" id="delete_vm_after_email">
												<?php
													$delete_vm_after_email = NULL;
													if($output->data->delete_vm_after_email == "Y"){
														$delete_vm_after_email .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
													}else{
														$delete_vm_after_email .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
													}
													
													if($output->data->delete_vm_after_email == "N" || $output->data->delete_vm_after_email == NULL){
														$delete_vm_after_email .= '<option value="N" selected>'.$lh->translationFor("go_no").'</option>';
													}else{
														$delete_vm_after_email .= '<option value="N" > '.$lh->translationFor("go_no").'</option>';
													}
													echo $delete_vm_after_email;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label"><?php $lh->translateText("new_message"); ?></label>
												<span style="padding-left:20px; font-size: 20;"><?php echo $output->data->messages;?></span>
											
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label"><?php $lh->translateText("old_message"); ?></label>
												<span style="padding-left:20px; font-size: 20;"><?php echo $output->data->old_messages;?></span>
											
										</div>
									</fieldset>
								</div><!-- end tab1 -->

						<!-- FOOTER BUTTONS -->
		                    <fieldset class="footer-buttons">
		                        <div class="box-footer">
		                           <div class="col-sm-3 pull-right">
											<a href="settingsvoicemails.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?></a>
		                           	
		                                	<button type="submit" class="btn btn-primary" id="modifyVoicemailOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>
										
		                           </div>
		                        </div>
		                    </fieldset>

			            	</div><!-- end of tab content -->
			            </div><!-- tab panel -->
			       	</form>
			       	</div><!-- body -->
                </div>
            </section>  	

				<?php
					}                    
				?>
					
				<!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php print $ui->standardizedThemeJS(); ?> 
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		

		<script type="text/javascript">
			$(document).ready(function() {
				$('.select').select2({ theme: 'bootstrap' });
				$.fn.select2.defaults.set( "theme", "bootstrap" );
				
				// for cancelling
				$(document).on('click', '#cancel', function(){
					swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
				});

				/** 
				 * Modifies a telephony list
			 	 */
				$("#modifyform").validate({
                	submitHandler: function() {
						//submit the form
						
							$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
							$('#modifyVoicemailOkButton').prop("disabled", true);

							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifySettingsVoicemail.php", //post
							$("#modifyform").serialize(), 
								function(data){
									//if message is sent
									if (data == 1) {
										swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("voicemail_modify_success"); ?>", "success");
                                        window.setTimeout(function(){location.replace("settingsvoicemails.php")},2000)
                                        $('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
                                        $('#modifyVoicemailOkButton').prop("disabled", false);
									} else {
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
										$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
										$('#modifyVoicemailOkButton').prop("disabled", false);
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				 
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
