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

$usergroup_id = NULL;
if (isset($_POST["usergroup_id"])) {
	$usergroup_id = $_POST["usergroup_id"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit User Group</title>
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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("User Group Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["usergroup_id"])){
						?>	
							<li><a href="./settingsusergroups.php"><?php $lh->translateText("User Groups"); ?></a></li>
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
						$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetUserGroupInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["agent_id"] = $usergroup_id; #Desired exten ID. (required)

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
							for($i=0;$i<count($output->user_group);$i++){
					?>
                    
                    <div role="tabpanel" class="panel panel-transparent" style="box-shadow: 5px 5px 8px #888888;">
							
						<h4 style="padding:15px;"><a type="button" class="btn" href="settingsusergroups.php"><i class="fa fa-arrow-left"></i> Cancel</a><center><b>MODIFY USER GROUP <?php echo $usergroup_id;?></b></center></h4>
								
							<form id="modifyvoicemail">
								<input type="hidden" name="modifyid" value="<?php echo $usergroup_id;?>">
							
						<!-- BASIC SETTINGS -->
							<div class="panel text-left" style="margin-top: 20px; padding: 0px 30px">
								<div class="form-group">
									<label>User Group: </label>
									<span style="padding-left:20px; font-size: 20;"><?php echo $usergroup_id;?></span>
								</div>
								<div class="form-group">
									<label for="group_name">Group Name</label>
									<input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name (Mandatory)" value="<?php echo $output->group_name[$i];?>">
								</div>
								<div class="form-group">
									<label for="forced_timeclock_login">Force Timeclock Login
									<select class="form-control" name="forced_timeclock_login" id="forced_timeclock_login">
									<?php
										$forced_timeclock_login = NULL;
										if($output->forced_timeclock_login[$i] == "N"){
											$forced_timeclock_login .= '<option value="N" selected> NO </option>';
										}else{
											$forced_timeclock_login .= '<option value="N" > NO </option>';
										}
										
										if($output->forced_timeclock_login[$i] == "Y"){
											$forced_timeclock_login .= '<option value="Y" selected> YES </option>';
										}else{
											$forced_timeclock_login .= '<option value="Y" > YES </option>';
										}

										if($output->forced_timeclock_login[$i] == "ADMIN_EXEMPT"){
											$forced_timeclock_login .= '<option value="ADMIN_EXEMPT" selected> ADMIN EXEMPT </option>';
										}else{
											$forced_timeclock_login .= '<option value="ADMIN_EXEMPT" > ADMIN EXEMPT </option>';
										}
										echo $forced_timeclock_login;
									?>
									</select>
									</label>
								</div>
								<div class="form-group">
									<label for="shift_enforcement">Shift Enforcement
									<select class="form-control" id="shift_enforcement" name="shift_enforcement">
										<?php
											$shift_enforcement = NULL;

											if($output->shift_enforcement[$i] == "OFF" || $output->shift_enforcement[$i] == ""){
												$shift_enforcement .= '<option value="OFF" selected> OFF </option>';
											}else{
												$shift_enforcement .= '<option value="OFF" > OFF </option>';
											}
											
											if($output->shift_enforcement[$i] == "START"){
												$shift_enforcement .= '<option value="START" selected> START </option>';
											}else{
												$shift_enforcement .= '<option value="START" > START </option>';
											}
		                                    
		                                    if($output->shift_enforcement[$i] == "ALL"){
												$shift_enforcement .= '<option value="ALL" selected> ALL </option>';
											}else{
												$shift_enforcement .= '<option value="ALL" > ALL </option>';
											}
		                                    
											echo $shift_enforcement;
										?>
									</select>
									</label>
								</div>
								<div class="form-group">
									<label for="group_level">Group Level
									<select class="form-control" name="group_level" id="group_level">
									<?php
										$group_level = NULL;

										for($o=1; $o <= 9; $o++){
											if($output->group_level[$i] == $o){
												$group_level .= '<option value="'.$o.'" selected> '.$o.' </option>';
											}else{
												$group_level .= '<option value="'.$o.'"> '.$o.' </option>';
											}
										}

										echo $group_level;
									?>
									</select>
									</label>
								</div>
								<br/>

							</div>
							
							<!-- NOTIFICATIONS -->
		                    <div id="notifications">
		                        <div class="output-message-success" style="display:none;">
		                            <div class="alert alert-success alert-dismissible" role="alert">
		                              <strong>Success!</strong> Phone <?php echo $usergroup_id?> modified !
		                            </div>
		                        </div>
		                        <div class="output-message-error" style="display:none;">
		                            <div class="alert alert-danger alert-dismissible" role="alert">
		                              <span id="modifyT_phonesresult"></span>
		                            </div>
		                        </div>
		                    </div>

							<div class="row" style="padding:0px 50px;">
								<button type="button" class="btn btn-danger delete-phone" id="modifyUSERDeleteButton" data-id="<?php echo $usergroup_id?>" data-name="<?php echo $usergroup_id;?>" href=""><i class="fa fa-times"></i> Delete</button>

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
				$("#modifyvoicemail").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyUsergroup.php", //post
							$("#modifyvoicemail").serialize(), 
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
	                                window.location.replace("settingsusergroups.php");
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
