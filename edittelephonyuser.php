<?php

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
//require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

$userid = NULL;
if (isset($_POST["userid"])) {
	$userid = $_POST["userid"];
	
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Goautodial Edit Telephony Users</title>
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
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("telephony_users_edition"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if(isset($_POST["userid"])){
						?>	
							<li><a href="./telephony_users.php"><?php $lh->translateText("telephony_users"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<!-- standard custom edition form -->
					<?php
					$userobj = NULL;
					$errormessage = NULL;
					
					if(isset($userid)) {
						//$db = new \creamy\DbHandler();
						//$customerobj = $db->getDataForCustomer($customerid, $customerType);
						
						$url = "https://encrypted.goautodial.com/goAPI/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = "admin"; #Username goes here. (required)
						$postfields["goPass"] = "goautodial"; #Password goes here. (required)
						$postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = "json"; #json. (required)
						$postfields["user_id"] = $userid; #Desired User ID (required)

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
						
						// print_r($data);
						
						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i<count($output->userno);$i++){
								
								$hidden_f = $ui->hiddenFormField("modifyid", $userid);
								
								$id_f = '<h4>Agent ID : <b>'.$userid.'</b></h4>';
								
								// name
								// $name_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("name", "name", "text", $lh->translationFor("name"), $userobj["name"], "user", true));
								//$name_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("name", "name", "text", $lh->translationFor("name"), $output->full_name[$i], "user", true));
								
								$name_l = '<h4>Full Name</h4>';
								$ph = $lh->translationFor("Full Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->full_name[$i]) ? $output->full_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "user", "required"));
								
								
								$usergroup_l = '<h4>User Group</h4>';
								$usergroup_f = '<select class="form-control" id="usergroup" name="usergroup">';
												
									if($output->user_group[$i] == "ADMINISTRATORS"){
										$usergroup_f .= '<option value="ADMINISTRATORS" selected>GOAUTODIAL ADMINISTRATORS</option>';
									}else{
										$usergroup_f .= '<option value="ADMINISTRATORS" >GOAUTODIAL ADMINISTRATORS</option>';
									}
									
									if($output->user_group[$i] == "AGENTS"){
										$usergroup_f .= '<option value="AGENTS" selected>GOAUTODIAL AGENTS</option>';
									}else{
										$usergroup_f .= '<option value="AGENTS" >GOAUTODIAL AGENTS</option>';
									}
									
									if($output->user_group[$i] == "SUPERVISOR"){
										$usergroup_f .= '<option value="SUPERVISOR" selected>SUPERVISOR</option>';
									}else{
										$usergroup_f .= '<option value="SUPERVISOR" >SUPERVISOR</option>';
									}
									
								$usergroup_f .= '</select>';
								
								
								$status_l = '<h4>Active</h4>';
								$status_f = '<select class="form-control" id="status" name="status">';
												
									if($output->active[$i] == "Y"){
										$status_f .= '<option value="Y" selected> YES </option>';
									}else{
										$status_f .= '<option value="Y" > YES </option>';
									}
									
									if($output->active[$i] == "N"){
										$status_f .= '<option value="N" selected> NO </option>';
									}else{
										$status_f .= '<option value="N" > NO </option>';
									}
									
								$status_f .= '</select>';
								
								
								$userlevel_l = '<h4>User Level</h4>';
								$userlevel_f = '<select class="form-control" id="userlevel" name="userlevel">';
								
										if($output->user_level[$i] == "1"){
											$userlevel_f .= '<option value="1" selected> 1 </option>';
										}else{
											$userlevel_f .= '<option value="1" > 1 </option>';
										}
										if($output->user_level[$i] == "2"){
											$userlevel_f .= '<option value="2" selected> 2 </option>';
										}else{
											$userlevel_f .= '<option value="2" > 2 </option>';
										}
										if($output->user_level[$i] == "3"){
											$userlevel_f .= '<option value="3" selected> 3 </option>';
										}else{
											$userlevel_f .= '<option value="3" > 3 </option>';
										}
										if($output->user_level[$i] == "4"){
											$userlevel_f .= '<option value="4" selected> 4 </option>';
										}else{
											$userlevel_f .= '<option value="4" > 4 </option>';
										}
										if($output->user_level[$i] == "5"){
											$userlevel_f .= '<option value="5" selected> 5 </option>';
										}else{
											$userlevel_f .= '<option value="5" > 5 </option>';
										}
										if($output->user_level[$i] == "6"){
											$userlevel_f .= '<option value="6" selected> 6 </option>';
										}else{
											$userlevel_f .= '<option value="6" > 6 </option>';
										}
										if($output->user_level[$i] == "7"){
											$userlevel_f .= '<option value="7" selected> 7 </option>';
										}else{
											$userlevel_f .= '<option value="7" > 7 </option>';
										}
										if($output->user_level[$i] == "8"){
											$userlevel_f .= '<option value="8" selected> 8 </option>';
										}else{
											$userlevel_f .= '<option value="8" > 8 </option>';
										}
										if($output->user_level[$i] == "9"){
											$userlevel_f .= '<option value="9" selected> 9 </option>';
										}else{
											$userlevel_f .= '<option value="9" > 9 </option>';
										}
											
								$userlevel_f .= '</select>';
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyT_userDeleteButton", $userid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
								// generate the form
								$fields = $hidden_f.$id_f.$name_l.$name_f.$usergroup_l.$usergroup_f.$status_l.$status_f.$userlevel_l.$userlevel_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyuser", $fields, $buttons, "modifyT_userresult");
								
								// generate and show the box
								//$box = $ui->boxWithForm("modifyuser", , $fields, $lh->translationFor("edit_user"));
								//print $box;
								
								// generate box
								$boxTitle = $lh->translationFor("insert_new_data");
								$formBox = $ui->boxWithContent($boxTitle, $form);
								// print our modifying customer box.
								print $formBox;
								
							}
						} else {
						# An error occured
							echo $output->result;
						}
                        
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
					
					?>
					
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			
            <?php print $ui->creamyFooter(); ?>
			
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {				
				/** 
				 * Modifies a telephony user
			 	 */
				//$("#modifycustomerform").validate({
				$("#modifyuser").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyUser.php", //post
							$("#modifyuser").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyT_userresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyT_userresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Deletes a customer
				 */
				 $("#modifyT_userDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var userid = $(this).attr('href');
						$.post("./php/DeleteTelephonyUser.php", { userid: userid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("user_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_user"); ?>: "+data); }
						});
					}
				 });
				 
			});
		</script>

    </body>
</html>
