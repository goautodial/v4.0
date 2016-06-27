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

$listid = NULL;
if (isset($_POST["listid"])) {
	$listid = $_POST["listid"];
	
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
                        <small><?php $lh->translateText("telephony_lists_edition"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if(isset($_POST["listid"])){
						?>	
							<li><a href="./telephonylistandcallrecording.php"><?php $lh->translateText("list_and_call_recording"); ?></a></li>
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
					
					if(isset($listid)) {
						
						$url = "https://encrypted.goautodial.com/goAPI/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = "admin"; #Username goes here. (required)
						$postfields["goPass"] = "goautodial"; #Password goes here. (required)
						$postfields["goAction"] = "goGetListInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = "json"; #json. (required)
						$postfields["list_id"] = $listid; #Desired list id. (required)

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
							for($i=0;$i<count($output->list_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modifyid", $listid);
								
								$id_f = '<h4>Modify List ID : <b>'.$listid.'</b><h5><i> Last call date : '.$output->list_lastcalldate[$i].'</i></h5></h4>';
								
								$name_l = '<h4>Name</h4>';
								$ph = $lh->translationFor("Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->list_name[$i]) ? $output->list_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "tasks", "required"));
								
								$desc_l = '<h4>Description</h4>';
								$ph = $lh->translationFor("Description").' ('.$lh->translationFor("optional").')';
								$vl = isset($output->list_description[$i]) ? $output->list_description[$i] : null;
								$desc_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("desc", "desc", "text", $ph, $vl, "tasks", "required"));
								
								
								$url2 = "http://162.254.144.92/goAPI/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
								$postfields2["goUser"] = "goautodial"; #Username goes here. (required)
								$postfields2["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
								$postfields2["goAction"] = "getAllCampaigns"; #action performed by the [[API:Functions]]. (required)
								$postfields2["responsetype"] = "json"; #json. (required)						
								$ch2 = curl_init();								
								curl_setopt($ch2, CURLOPT_URL, $url2);								
								curl_setopt($ch2, CURLOPT_HTTPHEADER, $header2);								
								curl_setopt($ch2, CURLOPT_POST, 1);								
								curl_setopt($ch2, CURLOPT_TIMEOUT, 100);								
								curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);								
								curl_setopt($ch2, CURLOPT_POSTFIELDS, $postfields2);								
								$data2 = curl_exec($ch2);								
								curl_close($ch2);								
								$output2 = json_decode($data2);
								
								$campaign_l = '<h4>Campaign</h4>';
								$campaign_f = '<select class="form-control" id="campaign" name="campaign" required>';		
								
								
								if ($output2->result=="success") {								
								# Result was OK!
								
									for($a=0;$a<count($output2->campaign_id);$a++){
										if($output->campaign_id[$i] == $output2->campaign_id[$a]){
											$campaign_f .= '<option value="'.$output2->campaign_id[$a].'" selected>'.$output2->campaign_name[$a].'</option>';
										}else{
											$campaign_f .= '<option value="'.$output2->campaign_id[$a].'" >'.$output2->campaign_name[$a].'</option>';
										}
									
									}
								} else {
								# An error occured
									echo $output2->result;
								}
									
								$campaign_f .= '</select>';
								$reset_l = '<h4>Reset Lead-Called-Status</h4>';
								$reset_f = '<select class="form-control" id="reset" name="reset">';
												
									if($output->reset_list[$i] == "Y"){
										$reset_f .= '<option value="Y" selected> YES </option>';
									}else{
										$reset_f .= '<option value="Y" > YES </option>';
									}
									
									if($output->reset_list[$i] == "N"){
										$reset_f .= '<option value="N" selected> NO </option>';
									}else{
										$reset_f .= '<option value="N" > NO </option>';
									}
									
								$reset_f .= '</select>';
								 
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
								
								$reset_status_row = $ui->rowWithVariableContents(array("6", "6","6","6"), array($reset_l, $status_l, $reset_f, $status_f));
								$rs_f = $ui->singleFormGroupWrapper($reset_status_row);
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyT_listDeleteButton", $listid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
								// generate the form
								$fields = $hidden_f.$name_l.$name_f.$desc_l.$desc_f.$campaign_l.$campaign_f.$rs_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifylist", $fields, $buttons, "modifyT_listresult");
								
								// generate and show the box
								//$box = $ui->boxWithForm("modifyuser", , $fields, $lh->translationFor("edit_user"));
								//print $box;
								
								// generate box
								$boxTitle = $id_f;
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
				 * Modifies a telephony list
			 	 */
				$("#modifylist").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyList.php", //post
							$("#modifylist").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyT_listresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyT_listresult");
									?>
									
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Deletes a telephony list
				 */
				 $("#modifyT_listDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var listid = $(this).attr('href');
						$.post("./php/DeleteTelephonyList.php", { listid: listid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("list_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_list"); ?>: "+data); }
						});
					}
				 });
				 
			});
		</script>

    </body>
</html>
