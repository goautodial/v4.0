<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');
require_once('./php/goCRMAPISettings.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
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
        <title>Edit Voicemail</title>
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

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        	<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

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
                        <?php $lh->translateText("Settings"); ?>
                        <small><?php $lh->translateText("Voice Mail Edit"); ?></small>
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
					
					//if(isset($extenid)) {
						$url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "getVoicemailInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["voicemail_id"] = $vmid; #Desired exten ID. (required)

				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);

				        //var_dump($data);

						if ($output->result=="success") {
							
						# Result was OK!
							for($i=0;$i<count($output->voicemail_id);$i++){
					?>
                <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
                    
                    <div class="panel-body">
						<legend>MODIFY VOICEMAIL ID: <u><?php echo $output->voicemail_id[$i];?></u></legend>
	
							<form id="modifyform">
								<input type="hidden" name="modifyid" value="<?php echo $vmid;?>">
							
						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs">
								<li class="active"><a href="#tab_1" data-toggle="tab"><em class="fa fa-gear fa-lg"></em> Basic Settings</a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
				                	<fieldset>
										<div class="form-group mt">
											<label for="password" class="col-sm-3 control-label">Your Password</label>
											<div class="col-sm-9 mb">
												<input type="text" class="form-control" name="password" id="password" placeholder="Password" value="<?php echo $output->password[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="fullname" class="col-sm-3 control-label">Name</label>
											<div class="col-sm-9 mb">
												<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->fullname[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="email" class="col-sm-3 control-label">Email</label>
											<div class="col-sm-9 mb">
												<input type="text" class="form-control" name="email" id="email" value="<?php echo $output->email[$i];?>">
											</div>
										</div>
									
										<div class="form-group">
											<label for="active" class="col-sm-3 control-label">Active</label>
											<div class="col-sm-9 mb">
												<select class="form-control" name="active" id="active">
												<?php
													$active = NULL;
													if($output->active[$i] == "Y"){
														$active .= '<option value="Y" selected> YES </option>';
													}else{
														$active .= '<option value="Y" > YES </option>';
													}
													
													if($output->active[$i] == "N" || $output->active[$i] == NULL){
														$active .= '<option value="N" selected> NO </option>';
													}else{
														$active .= '<option value="N" > NO </option>';
													}
													echo $active;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="delete_vm_after_email" class="col-sm-3 control-label">Delete Voicemail After Email</label>
											<div class="col-sm-9 mb">
												<select class="form-control" name="delete_vm_after_email" id="delete_vm_after_email">
												<?php
													$delete_vm_after_email = NULL;
													if($output->delete_vm_after_email[$i] == "Y"){
														$delete_vm_after_email .= '<option value="Y" selected> YES </option>';
													}else{
														$delete_vm_after_email .= '<option value="Y" > YES </option>';
													}
													
													if($output->delete_vm_after_email[$i] == "N" || $output->delete_vm_after_email[$i] == NULL){
														$delete_vm_after_email .= '<option value="N" selected> NO </option>';
													}else{
														$delete_vm_after_email .= '<option value="N" > NO </option>';
													}
													echo $delete_vm_after_email;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">New Messages: </label>
												<span style="padding-left:20px; font-size: 20;"><?php echo $output->messages[$i];?></span>
											
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Old Messages: </label>
												<span style="padding-left:20px; font-size: 20;"><?php echo $output->old_messages[$i];?></span>
											
										</div>
									</fieldset>
								</div><!-- end tab1 -->

							<!-- NOTIFICATIONS -->
		                    <div id="notifications">
		                        <div class="output-message-success" style="display:none;">
		                            <div class="alert alert-success alert-dismissible" role="alert">
		                              <strong>Success!</strong> Voicemail <?php echo $vmid?> modified !
		                            </div>
		                        </div>
		                        <div class="output-message-error" style="display:none;">
		                            <div class="alert alert-danger alert-dismissible" role="alert">
		                              <span id="modifyVoicemailresult"></span>
		                            </div>
		                        </div>
		                    </div>

						<!-- FOOTER BUTTONS -->
		                    <fieldset>
		                        <div class="box-footer">
		                           <div class="col-sm-4 col-sm-offset-2 pull-right">
											<a href="settingsvoicemails.php" type="button" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
		                           	
		                                	<button type="submit" class="btn btn-primary" id="modifyVoicemailOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
										
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
					}	
                    
				?>
					
				<!-- /.content -->
            </aside><!-- /.right-side -->
			
            <?php print $ui->creamyFooter(); ?>
			
        </div><!-- ./wrapper -->

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

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		
		<!-- SLIMSCROLL-->
   		<script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
	
				/** 
				 * Modifies a telephony list
			 	 */
				$("#modifyform").validate({
                	submitHandler: function() {
						//submit the form
						
							$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
							$('#modifyVoicemailOkButton').prop("disabled", true);

							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifySettingsVoicemail.php", //post
							$("#modifyform").serialize(), 
								function(data){
									//if message is sent
									if (data == 1) {
										$('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                        window.setTimeout(function(){location.replace("settingsvoicemails.php")},2000)
                                        $('#update_button').html("<i class='fa fa-check'></i> Update");
                                        $('#modifyVoicemailOkButton').prop("disabled", false);
									} else {
										$('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
										$('#update_button').html("<i class='fa fa-check'></i> Update");
										$('#modifyVoicemailOkButton').prop("disabled", false);
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				 
			});
		</script>

    </body>
</html>
