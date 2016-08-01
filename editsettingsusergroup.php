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

            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
						<legend>MODIFY USER GROUP : <u><?php echo $usergroup_id;?></u></legend>
                    	
							<form id="modifyvoicemail">
								<input type="hidden" name="modifyid" value="<?php echo $usergroup_id;?>">
							
						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
				                	<fieldset>
										<div class="form-group mt">
											<label for="group_name" class="col-sm-2 control-label">Group Name</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name (Mandatory)" value="<?php echo $output->group_name[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="forced_timeclock_login" class="col-sm-2 control-label">Force Timeclock Login</label>
											<div class="col-sm-10 mb">
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
											</div>
										</div>
								
										<div class="form-group">
											<label for="shift_enforcement" class="col-sm-2 control-label">Shift Enforcement</label>
											<div class="col-sm-10 mb">
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
											</div>
										</div>
										<div class="form-group">
											<label for="group_level" class="col-sm-2 control-label">Group Level</label>
											<div class="col-sm-10 mb">
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
											</div>
										</div>
									</fieldset>
								</div><!-- tab 1 -->

								
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

			                    <!-- FOOTER BUTTONS -->
			                    <fieldset class="footer-buttons">
			                        <div class="box-footer">
			                           <div class="col-sm-3 pull-right">
												<a href="settingsusergroups.php" type="button" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
			                           	
			                                	<button type="submit" class="btn btn-primary" id="modifyUserGroupOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
											
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
							$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
							$('#modifyUserGroupOkButton').prop("disabled", true);

							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyUsergroup.php", //post
							$("#modifyvoicemail").serialize(), 
								function(data){
									//if message is sent
									if (data == 1) {
										$('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
                                        window.setTimeout(function(){location.reload()},2000)
                                        $('#update_button').html("<i class='fa fa-check'></i> Update");
                                        $('#modifyUserGroupOkButton').prop("disabled", false);
									} else {
									<?php 
										print $ui->fadingInMessageJS($errorMsg, "modifyT_phonesresult");
									?>
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyUserGroupOkButton').prop("disabled", false);
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
