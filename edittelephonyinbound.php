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

$groupid = NULL;
if (isset($_POST["groupid"])) {
	$groupid = $_POST["groupid"];
}

$ivr = NULL;
if (isset($_POST["ivr"])) {
	$ivr = $_POST["ivr"];
}

$did = NULL;
if (isset($_POST["did"])) {
	$did = $_POST["did"];
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
							if($groupid != NULL || $ivr != NULL || $did != NULL){
						?>	
							<li><a href="./telephonyinbound.php"><?php $lh->translateText("inbound"); ?></a></li>
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
					
					// IF INGROUP
					if($groupid != NULL) {
						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetInboundInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["group_id"] = $groupid; #Desired list id. (required)
            
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
							for($i=0;$i < count($output->group_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modify_groupid", $groupid);
								
								$id_f = '<h4>Modify In-group : <b>'.$groupid.'</b>';
								
								$desc_l = '<h4>Description</h4>';
								$ph = $lh->translationFor("Description").' ('.$lh->translationFor("optional").')';
								$vl = isset($output->group_name[$i]) ? $output->group_name[$i] : null;
								$desc_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("desc", "desc", "text", $ph, $vl, "tasks", "required"));
								
								$color="";				
								if($output->group_color == NULL){
									$color = "#FFFFFF";
								}else{
									$color = "#".$output->group_color;
								}
								
                                $color_l = '<h4>Color</h4>';
								//$ph = $lh->translationFor("Color").' ('.$lh->translationFor("mandatory").')';
								//$vl = isset($output->group_color[$i]) ? $output->group_color[$i] : null;
								//$color_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("color", "color", "color", $ph, $vl, "tasks", "required"));
                                $color_f = '<input type="color" class="form-control" name="color" value="'.$color.'" />';
								
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
								
                                $color_status_row = $ui->rowWithVariableContents(array("1","3","1","3"), array($color_l, $color_f, $status_l, $status_f));
								$cs_f = $ui->singleFormGroupWrapper($color_status_row);
                                
								$web_l = '<h4>Webform</h4>';
								$ph = $lh->translationFor("insert_url").' ('.$lh->translationFor("optional").')';
								$vl = isset($output->web_form_address[$i]) ? $output->web_form_address[$i] : null;
								$web_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("webform", "webform", "text", $ph, $vl, "tasks"));
								
								$next_l = '<h4>Next Agent Call</h4>';
								$next_f = '<select class="form-control" id="nextagent" name="nextagent">';
									
									if($output->next_agent_call[$i] == "random"){
										$next_f .= '<option value="random" selected> random </option>';
									}else{
										$next_f .= '<option value="random" > random </option>';
									}
									
									if($output->next_agent_call[$i] == "oldest_call_start"){
										$next_f .= '<option value="oldest_call_start" selected> oldest_call_start </option>';
									}else{
										$next_f .= '<option value="oldest_call_start" > oldest_call_start </option>';
									}
									
									if($output->next_agent_call[$i] == "oldest_call_finish"){
										$next_f .= '<option value="oldest_call_finish" selected> oldest_call_finish </option>';
									}else{
										$next_f .= '<option value="oldest_call_finish" > oldest_call_finish </option>';
									}
									
									if($output->next_agent_call[$i] == "overall_user_level"){
										$next_f .= '<option value="overall_user_level" selected> overall_user_level </option>';
									}else{
										$next_f .= '<option value="overall_user_level" > overall_user_level </option>';
									}
									
									if($output->next_agent_call[$i] == "inbound_group_rank"){
										$next_f .= '<option value="inbound_group_rank" selected> inbound_group_rank </option>';
									}else{
										$next_f .= '<option value="inbound_group_rank" > inbound_group_rank </option>';
									}
									
									if($output->next_agent_call[$i] == "campaign_rank"){
										$next_f .= '<option value="campaign_rank" selected> campaign_rank </option>';
									}else{
										$next_f .= '<option value="campaign_rank" > campaign_rank </option>';
									}
									
									if($output->next_agent_call[$i] == "fewest_calls"){
										$next_f .= '<option value="fewest_calls" selected> fewest_calls </option>';
									}else{
										$next_f .= '<option value="fewest_calls" > fewest_calls </option>';
									}
									
									if($output->next_agent_call[$i] == "fewest_calls_campaign"){
										$next_f .= '<option value="fewest_calls_campaign" selected> fewest_calls_campaign </option>';
									}else{
										$next_f .= '<option value="fewest_calls_campaign" > fewest_calls_campaign </option>';
									}
									
									if($output->next_agent_call[$i] == "longest_wait_time"){
										$next_f .= '<option value="longest_wait_time" selected> longest_wait_time </option>';
									}else{
										$next_f .= '<option value="longest_wait_time" > longest_wait_time </option>';
									}
									
									if($output->next_agent_call[$i] == "ring_all"){
										$next_f .= '<option value="ring_all" selected> ring_all </option>';
									}else{
										$next_f .= '<option value="ring_all" > ring_all </option>';
									}

								$next_f .= '</select>';
								
                                $prio_l = '<h4>Queue Priority</h4>';
								$prio_f = '<select class="form-control" id="prio" name="prio">';
								
                                for($a=99; $a >= -99; $a--){
                                    
                                    $a_desc = "";
                                   
                                   if($a < 0){
                                       $a_desc = "Lower";
                                   }
                                   if($a == 0){
                                       $a_desc = "Even";
                                   }
                                   if($a > 0){
                                       $a_desc = "Higher";
                                   }
                                       if($output->queue_priority[$i] == $a){
                                           $prio_f .= '<option value="'.$a.'" selected> '.$a.'  -  '.$a_desc.' </option>';
                                       }else{
                                           $prio_f .= '<option value="'.$a.'">'.$a.'  -  '.$a_desc.' </option>';
									}
                                }
									
								$prio_f .= '</select>';
								
								$next_prio_row = $ui->rowWithVariableContents(array("2","3","2","3"), array($next_l, $next_f, $prio_l, $prio_f));
								$np_f = $ui->singleFormGroupWrapper($next_prio_row);
								
								$display_l = '<h4>Fronter Display</h4>';
								$display_f = '<select class="form-control" id="display" name="display">';
												
									if($output->fronter_display[$i] == "Y"){
										$display_f .= '<option value="Y" selected> YES </option>';
									}else{
										$display_f .= '<option value="Y" > YES </option>';
									}
									
									if($output->fronter_display[$i] == "N"){
										$display_f .= '<option value="N" selected> NO </option>';
									}else{
										$display_f .= '<option value="N" > NO </option>';
									}
									
								$display_f .= '</select>';
								
								$script_l = '<h4>Script</h4>';
								$script_f = '<select class="form-control" id="script" name="script">';
							
							$scripts = $ui->API_goGetAllScripts();
									if($output->ingroup_script[$i] == NULL){
										$script_f .= '<option value="NONE" selected> NONE </option>';
									}else{
										$script_f .= '<option value="NONE" > NONE </option>';
									}
								for($x=0; $x<count($scripts->script_id);$x++){									
									if($output->ingroup_script[$i] == $scripts->script_id[$x]){
										$script_f .= '<option value="'.$scripts->script_id[$x].'" selected> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
									}else{
										$script_f .= '<option value="'.$scripts->script_id[$x].'"> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
									}

								}
								$script_f .= '</select>';
								
								$display_script_row = $ui->rowWithVariableContents(array("2","3","2","3"), array($display_l, $display_f, $script_l, $script_f));
								$ds_f = $ui->singleFormGroupWrapper($display_script_row);
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyINGROUPDeleteButton", $groupid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyInboundOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
							// generate the form
							$fields = $hidden_f.$desc_l.$desc_f.'<br/>'.$cs_f.$web_l.$web_f.'<br/>'.$np_f.$ds_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyingroup", $fields, $buttons, "modifyINGROUPresult");
								
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
                        
					}else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
				
				// IF IVR
					if($ivr != NULL) {
						/*
						 * Displaying Interactive Voice Response Information
						 * [[API:Function]] – goGetIVRInfo
						 * Allows to retrieve some attributes of a given IVR menu. IVR menu should belong to the user that authenticated the request.
						 */

						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetIVRInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["menu_id"] = $ivr; #Desired menu id. (required)
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
						
						//print_r($data);
						
						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i<count($output->menu_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modifyid", $menu_id);
								$voicefiles = $ui->API_GetVoiceFilesList();

								$id_f = '<h4>Modify Call Menu : <b>'.$menu_id.'</b>';
								
								$menu_id_f = '<h4>Menu ID : '.$menu_id;

								$name_l = '<h4>Menu Name</h4>';
								$ph = $lh->translationFor("Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->list_name[$i]) ? $output->list_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "tasks", "required"));
																
								$prompt_l = '<h4>Menu Prompt</h4>';
								$prompt_f = '<select class="form-control" id="menu_prompt" name="menu_prompt" required>';		
								
								if ($voicefiles->result=="success") {								
								# Result was OK!
								
									for($a=0;$a<count($voicefiles->file_name);$a++){
										$file = substr($voicefiles->file_name[$a], 0, -4);

										if($voicefiles->menu_prompt[$i] == $file){
											$prompt_f .= '<option value="'.$file.'" selected>'.$file.'</option>';
										}else{
											$prompt_f .= '<option value="'.$file.'" >'.$file.'</option>';
										}
									}
								} else {
								# An error occured
									$prompt_f = '<option value="" selected disabled>-- NO AVAILABLE VOICE FILE --</option>';;
								}
									
								$prompt_f .= '</select>';

								$timeout_l = '<h4>Menu Timeout</h4>';
								$ph = $lh->translationFor("Menu Timeout").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->menu_timeout[$i]) ? $output->menu_timeout[$i] : null;
								$timeout_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("menu_timeout", "menu_timeout", "number", $ph, $vl, "clock", "required"));
								
								$timeout_prompt_l = '<h4>Menu Timeout Prompt</h4>';
								$timeout_prompt_f = '<select class="form-control" id="menu_timeout_prompt" name="menu_timeout_prompt" required>';		
								
								if ($voicefiles->result=="success") {								
								# Result was OK!
								
									for($a=0;$a<count($voicefiles->file_name);$a++){
										$file = substr($voicefiles->file_name[$a], 0, -4);

										if($voicefiles->menu_timeout_prompt[$i] == $file){
											$timeout_prompt_f .= '<option value="'.$file.'" selected>'.$file.'</option>';
										}else{
											$timeout_prompt_f .= '<option value="'.$file.'" >'.$file.'</option>';
										}
									}
								} else {
								# An error occured
									$timeout_prompt_f = '<option value="" selected disabled>-- NO AVAILABLE VOICE FILE --</option>';;
								}
									
								$timeout_prompt_f .= '</select>';

								$invalid_prompt_l = '<h4>Menu Invalid Prompt</h4>';
								$invalid_prompt_f = '<select class="form-control" id="menu_invalid_prompt" name="menu_invalid_prompt" required>';		
								
								if ($voicefiles->result=="success") {								
								# Result was OK!
								
									for($a=0;$a<count($voicefiles->file_name);$a++){
										$file = substr($voicefiles->file_name[$a], 0, -4);

										if($voicefiles->menu_invalid_prompt[$i] == $file){
											$invalid_prompt_f .= '<option value="'.$file.'" selected>'.$file.'</option>';
										}else{
											$invalid_prompt_f .= '<option value="'.$file.'" >'.$file.'</option>';
										}
									}
								} else {
								# An error occured
									$invalid_prompt_f = '<option value="" selected disabled>-- NO AVAILABLE VOICE FILE --</option>';;
								}
									
								$invalid_prompt_f .= '</select>';
								
								$repeat_l = '<h4>Menu Repeat</h4>';
								$ph = $lh->translationFor("Menu Repeat").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->menu_repeat[$i]) ? $output->menu_repeat[$i] : null;
								$repeat_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("menu_repeat", "menu_repeat", "number", $ph, $vl, "clock", "required"));
								
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyT_ivrDeleteButton", $menu_id, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
								// generate the form
								$fields = $hidden_f.$id_f.$menu_id_f.$name_l.$name_f.$prompt_l.$prompt_f.$timeout_l.$timeout_f.$timeout_prompt_l.$timeout_prompt_f.$invalid_prompt_l.$invalid_prompt_f.$repeat_l.$repeat_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyivr", $fields, $buttons, "modifyT_ivrresult");
								
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

				// IF PHONE NUMBER / DID
					if($did != NULL) {
						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetDIDInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["did_pattern"] = $did; #Desired did. (required)
            
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
							for($i=0;$i<count($output->did_pattern);$i++){
								
								$hidden_f = $ui->hiddenFormField("did", $did);
								
								$id_f = '<h4>Modify Record : <b>'.$did.'</b>';
								
								$newid_l = '<h4>DID ID</h4>';
								$ph = $lh->translationFor("DID Extension").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->did_pattern[$i]) ? $output->did_pattern[$i] : null;
								$newid_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("modify_did", "modify_did", "text", $ph, $vl, "tasks", "required"));

								$exten_l = '<h4>DID Extension</h4>';
								$ph = $lh->translationFor("DID Extension").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->did_pattern[$i]) ? $output->did_pattern[$i] : null;
								$exten_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("exten", "exten", "text", $ph, $vl, "tasks", "required"));
								
								$desc_l = '<h4>DID Description</h4>';
								$ph = $lh->translationFor("DID Extension").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->did_description[$i]) ? $output->did_description[$i] : null;
								$desc_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("desc", "desc", "text", $ph, $vl, "tasks", "required"));
								
								$route_l = '<h4>DID Route</h4>';
								$route_f = '<select class="form-control" id="route" name="route">';
												
									if($output->did_route [$i] == "AGENT"){
										$route_f .= '<option value="AGENT" selected> Agent </option>';
									}else{
										$route_f .= '<option value="AGENT" > Agent </option>';
									}
									
									if($output->did_route [$i] == "IN_GROUP"){
										$route_f .= '<option value="IN_GROUP" selected> In-group </option>';
									}else{
										$route_f .= '<option value="IN_GROUP" > In-group </option>';
									}
									
									if($output->did_route [$i] == "PHONE"){
										$route_f .= '<option value="PHONE" selected> Phone </option>';
									}else{
										$route_f .= '<option value="PHONE" > Phone </option>';
									}
									
									if($output->did_route [$i] == "CALLMENU "){
										$route_f .= '<option value="CALLMENU " selected> Call Menu / IVR </option>';
									}else{
										$route_f .= '<option value="CALLMENU " > Call Menu / IVR </option>';
									}
									
									if($output->did_route [$i] == "VOICEMAIL "){
										$route_f .= '<option value="VOICEMAIL " selected> Voicemail </option>';
									}else{
										$route_f .= '<option value="VOICEMAIL " > Voicemail </option>';
									}
									
									if($output->did_route [$i] == "EXTEN "){
										$route_f .= '<option value="EXTEN " selected> Custom Extension </option>';
									}else{
										$route_f .= '<option value="EXTEN " > Custom Extension </option>';
									}

								$route_f .= '</select>';
								
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
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyDIDDeleteButton", $did, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
		
							// generate the form
							$fields = $hidden_f.$newid_l.$newid_f.$exten_l.$exten_f.$desc_l.$desc_f.$status_l.$status_f.$route_l.$route_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyphonenumber", $fields, $buttons, "modifyDIDresult");
								
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
				//an ingroup
				$("#modifyingroup").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyInbound.php", //post
							$("#modifyingroup").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyINGROUPresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyINGROUPresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				//a phone number / DID
				$("#modifyphonenumber").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyInbound.php", //post
							$("#modifyphonenumber").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyDIDresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyDIDresult");
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
				 $("#modifyINGROUPDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var listid = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { listid: listid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("inbound_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_list"); ?>: "+data); }
						});
					}
				 });
				//phone number
				  $("#modifyDIDDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var listid = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { listid: listid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("inbound_successfully_deleted"); ?>");
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
