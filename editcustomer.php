<?php
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

//$customerType = NULL;
$lead_id = NULL;
//if (isset($_GET["customer_type"])) { $customerType = $_GET["customer_type"]; }
if (isset($_POST['lead_id'])) { $lead_id = $_POST["lead_id"]; }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
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
                        <?php $lh->translateText("customers_and_contacts_management"); ?>
                        <small><?php $lh->translateText("personal_data_edition"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <?php 
	                        if (isset($customerType)) {
	                        	print ('<li><a href="customerslist.php?customer_type='.$customerType.'"> '.$lh->translationFor("customer_list").'</a></li>');
	                        }
                        ?>
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<!-- standard custom edition form -->
					<?php
					//$customerobj = NULL;
					$errormessage = NULL;
					
					/*if (isset($customerid) && isset($customerType)) {
						$db = new \creamy\DbHandler();
						$customerobj = $db->getDataForCustomer($customerid, $customerType);
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}*/
					
					if (isset($lead_id)) {
						$url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetLeadsInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["lead_id"] = $lead_id; #Desired exten ID. (required)

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
				         	for($i=0;$i<count($output->lead_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modifyid", $lead_id);
								
								$id_f = '<h4>Modify Phone <b>'.$lead_id.'</b>';
								
                                $plan_l = '<h4>Dial Plan Number</h4>';
								$ph = $lh->translationFor("Dial Plan Number").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->dialplan_number[$i]) ? $output->dialplan_number[$i] : null;
								$plan_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("plan", "plan", "text", $ph, $vl, "tasks", "required"));
								
                                 $vmid_l = '<h4>Voicemail ID</h4>';
								$ph = $lh->translationFor("Voicemail ID").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->voicemail_id[$i]) ? $output->voicemail_id[$i] : null;
								$vmid_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("vmid", "vmid", "text", $ph, $vl, "tasks", "required"));
                                                                
                                $ip_l = '<h4>Server IP</h4>';
								$ph = $lh->translationFor("Server IP").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->server_ip[$i]) ? $output->server_ip[$i] : null;
								$ip_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("ip", "ip", "text", $ph, $vl, "tasks", "required"));
								
								$active_l = '<br/><h4>Active Account</h4>';
								$active_f = '<br/><select class="form-control" id="active" name="active">';
												
									if($output->active[$i] == "Y"){
										$active_f .= '<option value="Y" selected> YES </option>';
									}else{
										$active_f .= '<option value="Y" > YES </option>';
									}
									
									if($output->active[$i] == "N"){
										$active_f .= '<option value="N" selected> NO </option>';
									}else{
										$active_f .= '<option value="N" > NO </option>';
									}
									
								$active_f .= '</select>';
								
                                $status_l = '<br/><h4>Status</h4>';
								$status_f = '<br/><select class="form-control" id="status" name="status">';
												
									if($output->status[$i] == "ACTIVE"){
										$status_f .= '<option value="ACTIVE" selected> ACTIVE </option>';
									}else{
										$status_f .= '<option value="ACTIVE" > ACTIVE </option>';
									}
									
									if($output->status[$i] == "SUSPENDED"){
										$status_f .= '<option value="SUSPENDED" selected> SUSPENDED </option>';
									}else{
										$status_f .= '<option value="SUSPENDED" > SUSPENDED </option>';
									}
                                    
                                    if($output->status[$i] == "CLOSED"){
										$status_f .= '<option value="CLOSED" selected> CLOSED </option>';
									}else{
										$status_f .= '<option value="CLOSED" > CLOSED </option>';
									}
                                    
                                    if($output->status[$i] == "PENDING"){
										$status_f .= '<option value="PENDING" selected> PENDING </option>';
									}else{
										$status_f .= '<option value="PENDING" > PENDING </option>';
									}
                                    
                                    if($output->status[$i] == "ADMIN "){
										$status_f .= '<option value="ADMIN " selected> ADMIN  </option>';
									}else{
										$status_f .= '<option value="ADMIN " > ADMIN  </option>';
									}
									
								$status_f .= '</select>';
                                
                                $active_status_row = $ui->rowWithVariableContents(array("2", "3","2","3"), array($active_l, $active_f, $status_l, $status_f));
								$as_f = $ui->singleFormGroupWrapper($active_status_row);
                                
                                
								$name_l = '<h4>Full Name</h4>';
								$ph = $lh->translationFor("Full Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->fullname[$i]) ? $output->fullname[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("fullname", "fullname", "text", $ph, $vl, "tasks"));
								
								$new_msg = '<h4>New Messages: <b>'.$output->messages[$i].'</b></h4>';
                                $old_msg = '<h4>Old Messages: <b>'.$output->old_messages[$i].'</b></h4>';
								
                                $protocol_l = '<h4>Client Protocol</h4>';
								$protocol_f = '<select class="form-control" id="protocol" name="protocol">';
								
                                    if($output->protocol[$i] == "SIP"){
                                        $protocol_f .= '<option value="SIP" selected> SIP </option>';
                                    }else{
                                        $protocol_f .= '<option value="SIP"> SIP </option>';
                                    }
                                    
                                    if($output->protocol[$i] == "Zap"){
                                        $protocol_f .= '<option value="Zap" selected> Zap </option>';
                                    }else{
                                        $protocol_f .= '<option value="Zap"> Zap </option>';
                                    }
                                    
                                    if($output->protocol[$i] == "IAX2"){
                                        $protocol_f .= '<option value="IAX2" selected> IAX2 </option>';
                                    }else{
                                        $protocol_f .= '<option value="IAX2"> IAX2 </option>';
                                    }
                                     
                                    if($output->protocol[$i] == "EXTERNAL"){
                                        $protocol_f .= '<option value="EXTERNAL" selected> EXTERNAL </option>';
                                    }else{
                                        $protocol_f .= '<option value="EXTERNAL"> EXTERNAL </option>';
                                    }
									
								$protocol_f .= '</select>';
				         }

						// buttons at bottom (only for writing+ permissions)
						$buttons = "";
						if ($user->userHasWritePermission()) {
							$buttons = $ui->buttonWithLink("modifyCustomerDeleteButton", $customerid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
							$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
							$buttons = $ui->singleFormGroupWrapper($buttons);
						}
						// form fields
							//$formcontent = $ui->customerFieldsForForm($customerobj, $customerType, $customerid);
							$formcontent = 

						// generate form: header
						$form = $ui->formWithCustomFooterButtons("modifycustomerform", $formcontent, $buttons, "modifycustomerresult");
						
						// generate box
						$boxTitle = $lh->translationFor("insert_new_data");
						$formBox = $ui->boxWithContent($boxTitle, $form);
						// print our modifying customer box.
						print $formBox;
					} else {
						print $ui->boxWithMessage($lh->translationFor("error"), $lh->translationFor("some_fields_missing"));
					}
					?>
					<!-- modules addons via hooks -->
					<?php print $ui->customerDetailModuleHooks($customerid, $customerType); ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {				
				/** 
				 * Modifies a customer
			 	 */
				$("#modifycustomerform").validate({
					submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyCustomer.php", //post
							$("#modifycustomerform").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
									print $ui->fadingInMessageJS($errorMsg, "modifycustomerresult"); 
									?>				
									} else {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
									print $ui->fadingInMessageJS($errorMsg, "modifycustomerresult"); 
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
				 $("#modifyCustomerDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var customerid = $(this).attr('href');
						$.post("./php/DeleteCustomer.php", $("#modifycustomerform").serialize() ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("customer_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_customer"); ?>: "+data); }
						});
					}
				 });
				 
			});
		</script>

    </body>
</html>
