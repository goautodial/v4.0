<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
//require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');
require_once('./php/goCRMAPISettings.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

$extenid = NULL;
if (isset($_POST["extenid"])) {
	$extenid = $_POST["extenid"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Phone Extension</title>
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
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

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
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("Phone Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["extenid"])){
						?>	
							<li><a href="./settingsphones.php"><?php $lh->translateText("phones"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content" style="padding:30px; padding-left:10%; padding-right:10%; margin-left: 0; margin-right: 0;">
					<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;
					
					//if(isset($extenid)) {
						$url = gourl."/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetPhoneInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["exten_id"] = $extenid; #Desired exten ID. (required)

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

						if ($output->result=="success") {
							
						# Result was OK!
							for($i=0;$i<count($output->extension);$i++){
					?>
                    
                    <div role="tabpanel" class="panel panel-transparent" style="box-shadow: 5px 5px 8px #888888;">
							
						<h4 style="padding:15px;"><a type="button" class="btn" href="settingsphones.php"><i class="fa fa-arrow-left"></i> Cancel</a><center><b>MODIFY PHONES</b></center></h4>
								
							<form id="modifyphones">
								<input type="hidden" name="modifyid" value="<?php echo $extenid;?>">
							
						<!-- BASIC SETTINGS -->
							<div class="panel text-left" style="margin-top: 20px; padding: 0px 30px">
								<div class="form-group">
									<label>Phone Extension: </label>
									<span style="padding-left:20px; font-size: 20;"><?php echo $extenid;?></span>
								</div>
								<div class="form-group">
									<label for="plan">Dial Plan Number</label>
									<input type="number" class="form-control" name="plan" id="plan" placeholder="Dial Plan Number (Mandatory)" value="<?php echo $output->dialplan_number[$i];?>">
								</div>
								<div class="form-group">
									<label for="vmid">Voicemail ID</label>
									<input type="text" class="form-control" name="vmid" id="vmid" value="<?php echo $output->voicemail_id[$i];?>">
								</div>
								<div class="form-group">
									<label for="ip">Server IP</label>
									<input type="text" class="form-control" name="ip" id="ip" value="<?php echo $output->server_ip[$i];?>">
								</div>
								<div class="form-group">
									<label for="status">Active Account</label>
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
								<div class="form-group">
									<label for="status">Status
									<select class="form-control" id="status" name="status">
										<?php
											$status = NULL;

											if($output->status[$i] == "ACTIVE"){
												$status .= '<option value="ACTIVE" selected> ACTIVE </option>';
											}else{
												$status .= '<option value="ACTIVE" > ACTIVE </option>';
											}
											
											if($output->status[$i] == "SUSPENDED"){
												$status .= '<option value="SUSPENDED" selected> SUSPENDED </option>';
											}else{
												$status .= '<option value="SUSPENDED" > SUSPENDED </option>';
											}
		                                    
		                                    if($output->status[$i] == "CLOSED"){
												$status .= '<option value="CLOSED" selected> CLOSED </option>';
											}else{
												$status .= '<option value="CLOSED" > CLOSED </option>';
											}
		                                    
		                                    if($output->status[$i] == "PENDING"){
												$status .= '<option value="PENDING" selected> PENDING </option>';
											}else{
												$status .= '<option value="PENDING" > PENDING </option>';
											}
		                                    
		                                    if($output->status[$i] == "ADMIN "){
												$status .= '<option value="ADMIN " selected> ADMIN  </option>';
											}else{
												$status .= '<option value="ADMIN " > ADMIN  </option>';
											}

											echo $status;
										?>
									</select>
									</label>
								</div>
								<div class="form-group">
									<label for="fullname">Fullname</label>
									<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->fullname[$i];?>">
								</div>
								<div class="form-group">
									<label>New Messages: </label>
									<span style="padding-left:20px; font-size: 20;"><?php echo $output->messages[$i];?></span>
								</div>
								<div class="form-group">
									<label>Old Messages: </label>
									<span style="padding-left:20px; font-size: 20;"><?php echo $output->old_messages[$i];?></span>
								</div>
								<div class="form-group">
									<label for="protocol">Client Protocol</label>
									<select class="form-control" id="protocol" name="protocol">
										<?php
											$protocol = NULL;

											if($output->protocol[$i] == "SIP"){
		                                        $protocol .= '<option value="SIP" selected> SIP </option>';
		                                    }else{
		                                        $protocol .= '<option value="SIP"> SIP </option>';
		                                    }
		                                    
		                                    if($output->protocol[$i] == "Zap"){
		                                        $protocol .= '<option value="Zap" selected> Zap </option>';
		                                    }else{
		                                        $protocol .= '<option value="Zap"> Zap </option>';
		                                    }
		                                    
		                                    if($output->protocol[$i] == "IAX2"){
		                                        $protocol .= '<option value="IAX2" selected> IAX2 </option>';
		                                    }else{
		                                        $protocol .= '<option value="IAX2"> IAX2 </option>';
		                                    }
		                                     
		                                    if($output->protocol[$i] == "EXTERNAL"){
		                                        $protocol .= '<option value="EXTERNAL" selected> EXTERNAL </option>';
		                                    }else{
		                                        $protocol .= '<option value="EXTERNAL"> EXTERNAL </option>';
		                                    }

											echo $protocol;
										?>
									</select>
								</div>

								<br/>

							</div>
							
							<!-- NOTIFICATIONS -->
		                    <div id="notifications">
		                        <div class="output-message-success" style="display:none;">
		                            <div class="alert alert-success alert-dismissible" role="alert">
		                              <strong>Success!</strong> Phone <?php echo $extenid?> modified !
		                            </div>
		                        </div>
		                        <div class="output-message-error" style="display:none;">
		                            <div class="alert alert-danger alert-dismissible" role="alert">
		                              <span id="modifyT_phonesresult"></span>
		                            </div>
		                        </div>
		                    </div>

							<div class="row" style="padding:0px 50px;">
								<button type="button" class="btn btn-danger delete-phone" id="modifyUSERDeleteButton" data-id="<?php echo $extenid?>" data-name="<?php echo $extenid;?>" href=""><i class="fa fa-times"></i> Delete</button>

								<button type="submit" class="btn btn-primary pull-right" id="modifyUserOkButton" href=""><i class="fa fa-check"></i> Update</button>
							</div>
							
							</form>								
							
						</div>

					<?php
							}
						}	
                        
					?>
                </section>
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
				$("#modifyphones").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifySettingsPhones.php", //post
							$("#modifyphones").serialize(), 
								function(data){
									//if message is sent
									if (data == 1) {
										$('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                        window.setTimeout(function(){location.reload()},2000)			
									} else {
									<?php 
										print $ui->fadingInMessageJS($errorMsg, "modifyT_phonesresult");
									?>
									
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
	             * Delete validation modal
	             */
	             $(document).on('click','.delete-phone',function() {
	                
	                var exten_id = $(this).attr('data-id');
	                var exten_name = $(this).attr('data-name');
	                var action = "Phone Extension";

	                $('.id-delete-label').attr("data-id", exten_id);
	                $('.id-delete-label').attr("data-action", action);

	                $(".delete_extension").text(exten_name);
	                $(".action_validation").text(action);

	                $('#delete_validation_modal').modal('show');
	             });

	             $(document).on('click','#delete_yes',function() {
	                
	                var id = $(this).attr('data-id');
	                var action = $(this).attr('data-action');

	                $('#id_span').html(id);

	                    $.ajax({
	                        url: "./php/DeleteSettingsPhones.php",
	                        type: 'POST',
	                        data: { 
	                            exten_id:id,
	                        },
	                        success: function(data) {
	                        console.log(data);
	                            if(data == 1){
	                                $('#result_span').text(data);
	                                $('#delete_notification').show();
	                                $('#delete_notification_modal').modal('show');
	                                //window.setTimeout(function(){$('#delete_notification_modal').modal('hide');location.reload();}, 2000);
	                                window.location.replace("./settingsphones.php");
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
