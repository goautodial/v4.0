<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$campaign = NULL;
if (isset($_POST["campaign"])) {
	$campaign = $_POST["campaign"];
}

$disposition = NULL;
if (isset($_POST["disposition"])) {
	$disposition = $_POST["disposition"];
}

$leadfilter = NULL;
if (isset($_POST["leadfilter"])) {
	$leadfilter = $_POST["leadfilter"];
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Goautodial Edit In-Group</title>
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
                        <small><?php $lh->translateText("telephony_lists_edition"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if($campaign != NULL || $disposition != NULL || $leadfilter != NULL){
						?>	
							<li><a href="./telephonycampaign.php"><?php $lh->translateText("Campaign"); ?></a></li>
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
					$errormessage = NULL;
					
					// IF CAMPAIGN
					if($campaign != NULL) {
						$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "getCampaignInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["campaign_id"] = $campaign; #Desired list id. (required)
            
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
						
						//var_dump($output);
						
						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i < count($output->campaign_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modify_campaign", $campaign);
								
								$title_f = '<h4><b>Modify Campaign : '.$campaign.' - '.$output->campaign_name[$i].'</b>';
								
								$id_f = '<h4>Campaign ID : '.$campaign.'</h4>';

								$name_l = '<h4>Campaign Name</h4>';
								$ph = $lh->translationFor("Campaign Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->campaign_name[$i]) ? $output->campaign_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "tasks", "required"));
								
								$status_l = '<h4>Status</h4>';
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
								
                                $dial_l = '<h4>Dial Method</h4>';
								$dial_f = '<select class="form-control" id="dial_method" name="dial_method">';
												
									if($output->dial_method[$i] == "RATIO"){
										$dial_f .= '<option value="RATIO" selected> AUTO DIAL </option>';
									}else{
										$dial_f .= '<option value="RATIO" > AUTO DIAL </option>';
									}
									
									if($output->dial_method[$i] == "MANUAL"){
										$dial_f .= '<option value="MANUAL" selected> MANUAL </option>';
									}else{
										$dial_f .= '<option value="MANUAL" > MANUAL </option>';
									}
									
									if($output->dial_method[$i] == "ADAPT_TAPERED"){
										$dial_f .= '<option value="ADAPT_TAPERED" selected> PREDICTIVE </option>';
									}else{
										$dial_f .= '<option value="ADAPT_TAPERED" > PREDICTIVE </option>';
									}

									if($output->dial_method[$i] == "INBOUND_MAN"){
										$dial_f .= '<option value="INBOUND_MAN" selected> INBOUND_MAN </option>';
									}else{
										$dial_f .= '<option value="INBOUND_MAN" > INBOUND_MAN </option>';
									}
								$dial_f .= '</select>';
                                
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyINGROUPDeleteButton", $groupid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyInboundOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
							// generate the form
							$fields = $hidden_f.$id_f.$name_l.$name_f.$status_l.$status_f.$dial_l.$dial_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifycampaign", $fields, $buttons, "modifyCAMPAIGNresult");
								
								// generate and show the box
								//$box = $ui->boxWithForm("modifyuser", , $fields, $lh->translationFor("edit_user"));
								//print $box;
								
								// generate box
								$boxTitle = $title_f;
								$formBox = $ui->boxWithContent($boxTitle, $form);
								// print our modifying customer box.
								print $formBox;
								
							}
						} else {
						# An error occured
							echo $output->result;
						}
                        
					}else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
				
				// IF LEADFILTER
					if($leadfilter != NULL) {
						/*
						 * Displaying Lead Filter Information
						 * [[API:Function]] – getLeadFilterInfo
						 * Allows to retrieve some attributes of a given lead filter. Lead filter should belong to the user that authenticated the request

						 */
						$url = gourl."/goLeadFilters/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "getLeadFilterInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["lead_filter_id"] = $leadfilter;


				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);

						//var_dump($output);

						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i < count($output->lead_filter_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modify_leadfilter", $leadfilter);
								
								$id_f = '<h4><b> Modify Lead Filter : '.$leadfilter.'</b>';
								
								$leadfilter_id_f = '<h4>Lead Filter ID : '.$leadfilter;

								$name_l = '<h4>Lead Filter Name</h4>';
								$ph = $lh->translationFor("Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->lead_filter_name[$i]) ? $output->lead_filter_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "tasks", "required"));
								
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyLEADFILTERDeleteButton", $menu_id, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
				
								// generate the form
								$fields = $hidden_f.$leadfilter_id_f.$name_l.$name_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyleadfilter", $fields, $buttons, "modifyLEADFILTERresult");
								
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

			$(function(){
				$('.demo1').colorpicker();
			});
	
				/** 
				 * Modifies 
			 	 */
				//CAMPAIGN
				$("#modifycampaign").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyCampaign.php", //post
							$("#modifycampaign").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyCAMPAIGNresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyCAMPAIGNresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				//LEADFILTER
				$("#modifyleadfilter").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyCampaign.php", //post
							$("#modifyleadfilter").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyLEADFILTERresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyLEADFILTERresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Delete
				 */

				//delete_campaign
				$("#modifyCAMPAIGNDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var campaign = $(this).attr('href');
						$.post("./php/DeleteTelephonyCampaign.php", { campaign: campaign } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("campaign_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_campaign"); ?>: "+data); }
						});
					}
				 });

				//delete_leadfilter
				  $("#modifyLEADFILTERDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var leadfilter = $(this).attr('href');
						$.post("./php/DeleteTelephonyCampaign.php", { leadfilter: leadfilter } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("leadfilter_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_leadfilter"); ?>: "+data); }
						});
					}
				 });

			});
		</script>

    </body>
</html>
