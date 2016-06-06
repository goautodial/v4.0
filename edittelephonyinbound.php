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
                        <?php $lh->translateText("Ingroup"); ?>
                        <small><?php $lh->translateText("Edit Ingroup"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("Telephony"); ?></li>
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
                <section class="content" style="padding:30px; padding-left:100px; padding-right:100px; margin-left: 0; margin-right: 0;">
					
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
					?>			

						<div role="tabpanel" class="panel panel-transparent" style="box-shadow: 5px 5px 8px #888888;">
							
							<h4 style="padding:15px;"><a type="button" class="btn" href="telephonyinbound.php"><i class="fa fa-arrow-left"></i> Cancel</a><center><b>MODIFY IN-GROUP: <?php echo $groupid;?></b></center></h4>

								<ul role="tablist" class="nav nav-tabs" style="padding: 5px 30px;">

								 <!-- Settings panel tabs-->
									 <li role="presentation" class="active">
										<a href="#settings" aria-controls="settings" role="tab" data-toggle="tab" class="bb0">
										<span class="fa fa-gear"></span> Settings</a>
									 </li>
								<!-- Agents tab -->
									 <li role="presentation">
										<a href="#agents" aria-controls="agents" role="tab" data-toggle="tab" class="bb0">
										<span class="fa fa-users"></span> Agents </a>
									 </li>								  
								<!-- Tab panes-->
								<div class="tab-content p0 bg-white">


								<!--==== Settings ====-->
								  <div id="settings" role="tabpanel" class="tab-pane active">
										
										<form id="modifyingroup">
											<input type="hidden" name="modify_groupid" value="<?php echo $groupid;?>">
										<!-- BASIC SETTINGS -->
											<div class="panel text-left" style="margin-top: 50px; padding: 0px 30px">
												<div class="form-group">
													<label for="description">Description</label>
													<input type="text" class="form-control" name="desc" id="description" value="<?php echo $output->group_name[$i];?>">
												</div>
												<div class="row">
													<label for="color" class="col-md-5">Color
													<input type="text" class="form-control" name="" id="color" value="<?php echo $output->group_color[$i];?>">
													</label>
												</div>
												<div class="row">
													<label for="status" class="col-md-3">Status
													<select class="form-control" name="status" id="status">
													<?php
														$status = NULL;
														if($output->active[$i] == "Y"){
															$status .= '<option value="Y" selected> YES </option>';
														}else{
															$status .= '<option value="Y" > YES </option>';
														}
														
														if($output->active[$i] == "N"){
															$status .= '<option value="N" selected> NO </option>';
														}else{
															$status .= '<option value="N" > NO </option>';
														}
														echo $status;
													?>
														
													</select>
													</label>
												</div>
												<div class="form-group">
													<label for="webform">Web Form</label>
													<input type="text" class="form-control" name="" id="webform" value="<?php echo $output->web_form_address[$i];?>">
												</div>
												<div class="row">
													<label for="nextagent" class="col-md-5">Next Agent Call
													<select class="form-control" id="nextagent" name="nextagent">
														<?php
															$next = NULL;
															if($output->next_agent_call[$i] == "random"){
																$next .= '<option value="random" selected> random </option>';
															}else{
																$next .= '<option value="random" > random </option>';
															}
															
															if($output->next_agent_call[$i] == "oldest_call_start"){
																$next .= '<option value="oldest_call_start" selected> oldest_call_start </option>';
															}else{
																$next .= '<option value="oldest_call_start" > oldest_call_start </option>';
															}
															
															if($output->next_agent_call[$i] == "oldest_call_finish"){
																$next .= '<option value="oldest_call_finish" selected> oldest_call_finish </option>';
															}else{
																$next .= '<option value="oldest_call_finish" > oldest_call_finish </option>';
															}
															
															if($output->next_agent_call[$i] == "overall_user_level"){
																$next .= '<option value="overall_user_level" selected> overall_user_level </option>';
															}else{
																$next .= '<option value="overall_user_level" > overall_user_level </option>';
															}
															
															if($output->next_agent_call[$i] == "ingroup_rank"){
																$next .= '<option value="ingroup_rank" selected> ingroup_rank </option>';
															}else{
																$next .= '<option value="ingroup_rank" > ingroup_rank </option>';
															}
															
															if($output->next_agent_call[$i] == "campaign_rank"){
																$next .= '<option value="campaign_rank" selected> campaign_rank </option>';
															}else{
																$next .= '<option value="campaign_rank" > campaign_rank </option>';
															}
															
															if($output->next_agent_call[$i] == "fewest_calls"){
																$next .= '<option value="fewest_calls" selected> fewest_calls </option>';
															}else{
																$next .= '<option value="fewest_calls" > fewest_calls </option>';
															}
															
															if($output->next_agent_call[$i] == "fewest_calls_campaign"){
																$next .= '<option value="fewest_calls_campaign" selected> fewest_calls_campaign </option>';
															}else{
																$next .= '<option value="fewest_calls_campaign" > fewest_calls_campaign </option>';
															}
															
															if($output->next_agent_call[$i] == "longest_wait_time"){
																$next .= '<option value="longest_wait_time" selected> longest_wait_time </option>';
															}else{
																$next .= '<option value="longest_wait_time" > longest_wait_time </option>';
															}
															
															if($output->next_agent_call[$i] == "ring_all"){
																$next .= '<option value="ring_all" selected> ring_all </option>';
															}else{
																$next .= '<option value="ring_all" > ring_all </option>';
															}
															echo $next;
														?>
													</select>
													</label>
												</div>
												<div class="row">
													<label for="priority" class="col-md-5">Queue Priority
													<select class="form-control" id="priority" name="prio">
														<?php
														$prio = NULL;
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
							                                           $prio .= '<option value="'.$a.'" selected> '.$a.'  -  '.$a_desc.' </option>';
							                                       }else{
							                                           $prio .= '<option value="'.$a.'">'.$a.'  -  '.$a_desc.' </option>';
																}
							                                }
							                                echo $prio;
														?>
													</select>
													</label>
												</div>
												<div class="row">
													<label for="display" class="col-md-3">Fronter Display
													<select class="form-control" id="display" name="display">
														<?php
														$display = NULL;
															if($output->fronter_display[$i] == "Y"){
																$display .= '<option value="Y" selected> YES </option>';
															}else{
																$display .= '<option value="Y" > YES </option>';
															}
															
															if($output->fronter_display[$i] == "N"){
																$display .= '<option value="N" selected> NO </option>';
															}else{
																$display .= '<option value="N" > NO </option>';
															}
														echo $display;
														?>
													</select>
													</label>
												</div>
												<div class="row">
													<label for="script" class="col-md-5">Script
													<select class="form-control" id="script" name="script">
														<?php
														$script = NULL;
														$scripts = $ui->API_goGetAllScripts();
																if($output->ingroup_script[$i] == NULL){
																	$script .= '<option value="NONE" selected> NONE </option>';
																}else{
																	$script .= '<option value="NONE" > NONE </option>';
																}
															for($x=0; $x<count($scripts->script_id);$x++){									
																if($output->ingroup_script[$i] == $scripts->script_id[$x]){
																	$script .= '<option value="'.$scripts->script_id[$x].'" selected> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
																}else{
																	$script .= '<option value="'.$scripts->script_id[$x].'"> '.$scripts->script_id[$x].' - '.$scripts->script_name[$x].' </option>';
																}

															}
														echo $script;
														?>
													</select>
													</label>
												</div>
												<div class="row" id="btn_show">
													<br/>
													<center>
													<a class="btn btn-app" style="padding:6px 20px; width:95%; height: 45px;" id="show_advanced_settings" >
										               <div id="show"><i class="fa fa-plus"></i></div>
										               <div id="hide" hidden><i class="fa fa-minus"></i></div>
										                Advanced Settings
										            </a>
										            </center>
									       		</div>
									       		<!-- ADVANCED SETTINGS -->
									       		<div id="advanced_settings_wrapper" style="background-color: #E4F3E8; padding: 25px 50px;" hidden>
									       			<div class="row">
									       				<label for="call_launch" class="col-md-5">Get Call Launch
														<select class="form-control" id="call_launch" name="call_launch">
														<?php
														$call_launch = NULL;
															if($output->fronter_display[$i] == "none"){
																$call_launch .= '<option value="none" selected> NONE </option>';
															}else{
																$call_launch .= '<option value="none" > NONE </option>';
															}
																
															if($output->fronter_display[$i] == "script"){
																$call_launch .= '<option value="script" selected> SCRIPT </option>';
															}else{
																$call_launch .= '<option value="script" > SCRIPT </option>';
															}

															if($output->fronter_display[$i] == "webform"){
																$call_launch .= '<option value="webform" selected> WEBFORM </option>';
															}else{
																$call_launch .= '<option value="webform" > WEBFORM </option>';
															}

															if($output->fronter_display[$i] == "form"){
																$call_launch .= '<option value="form" selected> FORM </option>';
															}else{
																$call_launch .= '<option value="form" > FORM </option>';
															}
														echo $call_launch;
														?>
													</select>
														</label>
									       			</div>
									       			<div class="row">
									       				<label for="accept_calls" class="col-md-3">Accept Calls when there are No Available Agents?
														<select class="form-control" id="accept_calls" name="accept_calls">
															<?php
															$accept_calls = NULL;
																if($output->fronter_display[$i] == "no"){
																	$accept_calls .= '<option value="no" selected> NO </option>';
																}else{
																	$accept_calls .= '<option value="no" > NO </option>';
																}
																
																if($output->fronter_display[$i] == "yes"){
																	$accept_calls .= '<option value="yes" selected> YES </option>';
																}else{
																	$accept_calls .= '<option value="yes" > YES </option>';
																}

																if($output->fronter_display[$i] == "no_paused"){
																	$accept_calls .= '<option value="no_paused" selected> NO PAUSED </option>';
																}else{
																	$accept_calls .= '<option value="no_paused" > NO PAUSED </option>';
																}
															echo $accept_calls;
															?>
														</select>
														</label>
									       			</div>
									       			<div class="row">
									       				<label for="call_launch" class="col-md-6">No Available Agents Routing
														<input type="text" class="form-control" name="" id="call_launch" value="">
														</label>
									       			</div>
									       			<div class="row">
									       				<label for="call_launch" class="col-md-7">Welcome Message Filename
														<input type="text" class="form-control" name="" id="call_launch" value="">
														</label>
									       			</div>
									       			<div class="row">
									       				<label for="call_launch" class="col-md-5">Music On Hold Context
														<input type="text" class="form-control" name="" id="call_launch" value="">
														</label>
									       			</div>
									       			<div class="row">
									       				<label for="call_launch" class="col-md-6">On Hold Prompt
														<input type="text" class="form-control" name="" id="call_launch" value="">
														</label>
									       			</div>
									       		</div>
											</div>
											
									<div id="modifyINGROUPresult"></div>
									<div class="row" style="padding:0px 20px;">
										<button type="button" class="btn btn-danger" id="modifyINGROUPDeleteButton" href=""><i class="fa fa-times"></i> Delete</button>

										<button type="submit" class="btn btn-primary pull-right" id="modifyInboundOkButton" href=""><i class="fa fa-check"></i> Save</button>
									</div>
									
									</form>
								 </div>
								
								<!--==== Agents ====-->
								  <div id="agents" role="tabpanel" class="tab-pane">
										<table class="table table-striped table-bordered table-hover" id="table_ivr">
										   <thead>
											  <tr>
												 <th>Menu ID</th>
												 <th>Descriptions</th>
												 <th>Prompt</th>
												 <th class='hide-on-medium hide-on-low'>Timeout</th>
												 <th>Action</th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		for($i=0;$i < count($ivr->menu_id);$i++){

													$action_IVR = $ui->ActionMenuForIVR($ivr->menu_id[$i]);

											   	?>	
													<tr>
														<td><?php echo $ivr->menu_id[$i];?></td>
														<td><a class=''><?php echo $ivr->menu_name[$i];?></a></td>
														<td><?php echo $ivr->menu_prompt[$i];?></td>
														<td class='hide-on-medium hide-on-low'><?php echo $ivr->menu_timeout[$i];?></td>
														<td><?php echo $action_IVR;?></td>
													</tr>
												<?php
													}
												?>
										   </tbody>
										</table>
										<br/>
									<div class="panel-footer text-right">&nbsp;</div>
								 </div>

								</div><!-- END tab content-->
							</div>

					<?php			
								/*
									INSERT OLD CODE HERE
								*/
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
						
						//var_dump($output);

						$voicefiles = $ui->API_GetVoiceFilesList();

						if ($output->result=="success") {
						# Result was OK!
							for($i=0;$i < count($output->menu_id);$i++){
								
								$hidden_f = $ui->hiddenFormField("modify_ivr", $ivr);
								

								$id_f = '<h4>Modify Call Menu : <b>'.$ivr.'</b>';
								
								$menu_id_f = '<h4>Menu ID : '.$ivr;

								$name_l = '<h4>Menu Name</h4>';
								$ph = $lh->translationFor("Name").' ('.$lh->translationFor("mandatory").')';
								$vl = isset($output->menu_name[$i]) ? $output->menu_name[$i] : null;
								$name_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("name", "name", "text", $ph, $vl, "tasks", "required"));
																
								$prompt_l = '<h4>Menu Prompt</h4>';
								$prompt_f = '<select class="form-control" id="menu_prompt" name="menu_prompt" required>';		
								
								if ($voicefiles->result=="success") {								
								# Result was OK!
								
									for($a=0;$a<count($voicefiles->file_name);$a++){
										$file = substr($voicefiles->file_name[$a], 0, -4);

										if($output->menu_prompt[$i] == $file){
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

										if($output->menu_timeout_prompt[$i] == $file){
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

										if($output->menu_invalid_prompt[$i] == $file){
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
								$ph = $lh->translationFor("Menu Repeat");
								$vl = isset($output->menu_repeat[$i]) ? $output->menu_repeat[$i] : null;
								$repeat_f = $ui->singleInputGroupWithContent($ui->singleFormInputElement("menu_repeat", "menu_repeat", "number", $ph, $vl, "clock", "required"));
								
								
								// buttons at bottom (only for writing+ permissions)
								$buttons = "";
								if ($user->userHasWritePermission()) {
									$buttons = $ui->buttonWithLink("modifyIVRDeleteButton", $menu_id, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
									$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
									$buttons = $ui->singleFormGroupWrapper($buttons);
								}
				
								// generate the form
								$fields = $hidden_f.$menu_id_f.$name_l.$name_f.$prompt_l.$prompt_f.$timeout_l.$timeout_f.$timeout_prompt_l.$timeout_prompt_f.$invalid_prompt_l.$invalid_prompt_f.$repeat_l.$repeat_f;
								
								// generate form: header
								$form = $ui->formWithCustomFooterButtons("modifyivr", $fields, $buttons, "modifyIVRresult");
								
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
								
								$hidden_f = $ui->hiddenFormField("modify_did", $did);
								
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

			$("#show_advanced_settings").click(function(){
			    $("#advanced_settings_wrapper").toggle();
			    if ($('#hide').is(":hidden")){
			    	$("#hide").show();
			   		$("#show").hide();
			    }else{
			    	$("#show").show();
			   		$("#hide").hide();
			    }
			    
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
				//IVR
				$("#modifyivr").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyInbound.php", //post
							$("#modifyivr").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyIVRresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyIVRresult");
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

				//delete_ingroup
				$("#modifyINGROUPDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var groupid = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { groupid: groupid } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("ingroup_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_ingroup"); ?>: "+data); }
						});
					}
				 });

				//delete_ivr
				  $("#modifyIVRDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var ivr = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { ivr: ivr } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("ivr_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_ivr"); ?>: "+data); }
						});
					}
				 });

				//delete_phonenumber
				  $("#modifyDIDDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var did = $(this).attr('href');
						$.post("./php/DeleteTelephonyInbound.php", { did: did } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("did_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_did"); ?>: "+data); }
						});
					}
				 });
			});
		</script>

    </body>
</html>
