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
        <title>Edit 
        	<?php 
        		if($groupid != NULL){echo "In-Group";}
        		if($ivr != NULL){echo "Interactive Voice Record";}
        		if($did != NULL){echo "DID/Phone Number";}
        	?>
        </title>
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
        <!-- Bootstrap Color Picker -->
  		<link rel="stylesheet" href="adminlte/colorpicker/bootstrap-colorpicker.min.css">

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
                    	Inbound
                        <small>Edit 
                        	<?php 
				        		if($groupid != NULL){echo "In-Group";}
				        		if($ivr != NULL){echo "Interactive Voice Record";}
				        		if($did != NULL){echo "DID/Phone Number";}
					        ?>
					    </small>
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
				<!-- Main content -->
                 <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend>MODIFY IN-GROUP : <u><?php echo $groupid;?></u></legend>

							<form id="modifyingroup">

							<div role="tabpanel">
								<ul role="tablist" class="nav nav-tabs">
								 <!-- Settings panel tabs-->
									 <li role="presentation" class="active">
										<a href="#settings" data-toggle="tab">
										<span class="fa fa-gear"></span> Basic Settings</a>
									 </li>
								<!-- Advanced settings tab -->
									 <li role="presentation">
										<a href="#advanced_settings" data-toggle="tab">
										<span class="fa fa-gears"></span> Advanced Settings </a>
									 </li>
								<!-- Agents tab -->
									 <li role="presentation">
										<a href="#agents" data-toggle="tab">
										<span class="fa fa-users"></span> Agents </a>
									 </li>
								</ul>		

								<!-- Tab panes-->
								<div class="tab-content">

									<!--==== Settings ====-->
									<div id="settings" class="tab-pane fade in active">
										<input type="hidden" name="modify_groupid" value="<?php echo $groupid;?>">
										
										<!-- BASIC SETTINGS -->
										<fieldset>
											<div class="form-group mt">
												<label for="description" class="col-sm-2 control-label">Description</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="desc" id="description" value="<?php echo $output->group_name[$i];?>">
												</div>
											</div>
											<div class="form-group">
												<?php $output->group_color[$i] = "#".$output->group_color[$i];?>
												<label for="color" class="col-sm-2 control-label">Color:</label>
												<div class="col-sm-10 mb">
									                <input type="text" class="form-control colorpicker" name="color" id="color" value="<?php echo $output->group_color[$i];?>">
												</div>
											</div>
											<div class="form-group">
												<label for="status" class="col-sm-2 control-label">Status</label>
												<div class="col-sm-10 mb">
													<select class="form-control" name="status" id="status">
													<?php
														$status = NULL;
														if($output->active[$i] == "Y"){
															$status .= '<option value="Y" selected> Active </option>';
														}else{
															$status .= '<option value="Y" > Active </option>';
														}
														
														if($output->active[$i] == "N"){
															$status .= '<option value="N" selected> Inactive </option>';
														}else{
															$status .= '<option value="N" > Inactive </option>';
														}
														echo $status;
													?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="webform" class="col-sm-2 control-label">Web Form</label>
												<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="" id="webform" value="<?php echo $output->web_form_address[$i];?>">
												</div>
											</div>
											<div class="form-group">
												<label for="nextagent" class="col-sm-2 control-label">Next Agent Call</label>
												<div class="col-sm-10 mb">
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
												</div>
											</div>
											<div class="form-group">
												<label for="priority" class="col-sm-2 control-label">Queue Priority</label>
												<div class="col-sm-10 mb">
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
												</div>
											</div>
											<div class="form-group">
												<label for="display" class="col-sm-2 control-label">Fronter Display</label>
												<div class="col-sm-10 mb">
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
												</div>
											</div>
											<div class="form-group">
												<label for="script" class="col-sm-2 control-label">Script</label>
												<div class="col-sm-10 mb">
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
												</div>
											</div>
									    </fieldset>
									</div>

									<!-- ADVANCED SETTINGS -->
									<div id="advanced_settings" class="tab-pane fade in">
										<fieldset>
							       			<div class="form-group mt">
							       				<label for="call_launch" class="col-sm-4 control-label">Get Call Launch</label>
							       				<div class="col-sm-8 mb">
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
												</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="accept_calls" class="col-sm-4 control-label">Accept Calls when there are No Available Agents?</label>
							       				<div class="col-sm-8 mb">
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
												</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="call_launch" class="col-sm-4 control-label">No Available Agents Routing</label>
							       				<div class="col-sm-8 mb">
													<input type="text" class="form-control" name="" id="call_launch" value="">
												</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="call_launch" class="col-sm-4 control-label">Welcome Message Filename</label>
							       				<div class="col-sm-8 mb">
													<input type="text" class="form-control" name="" id="call_launch" value="">
												</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="call_launch" class="col-sm-4 control-label">Music On Hold Context</label>
							       				<div class="col-sm-8 mb">
													<input type="text" class="form-control" name="" id="call_launch" value="">
												</div>
							       			</div>
							       			<div class="form-group">
							       				<label for="call_launch" class="col-sm-4 control-label">On Hold Prompt</label>
							       				<div class="col-sm-8 mb">
													<input type="text" class="form-control" name="" id="call_launch" value="">
												</div>
							       			</div>
							       		</fieldset>
									</div>

									<!--==== Agents ====-->
									<div id="agents" class="tab-pane fade in">
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
									</div>

									<!-- FOOTER BUTTONS -->
								   	<div id="modifyINGROUPresult"></div>

								   	<fieldset>
				                        <div class="box-footer">
				                           <div class="pull-right col-sm-3">
												<a href="telephonyinbound.php" type="button" class="btn btn-danger pull-left"><i class="fa fa-close"></i> Cancel </a>
				                           		
				                                <button type="submit" class="btn btn-primary pull-right" id="modifyInboundOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
											
				                           </div>
				                        </div>
				                    </fieldset>

								</div><!-- END tab content-->
							</div><!-- END of tabpanel -->
							</form>
						</div><!-- body -->
					</div><!-- body -->
                </section>
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
							
						?>

						<div class="panel-body">
							<legend>MODIFY IVR : <u><?php echo $output->menu_id[$i];?></u></legend>

							<form id="modifyivr" class="form-horizontal">

								<input type="hidden" name="modify_ivr" value="<?php echo $output->menu_id[$i];?>">
								<div class="col-lg-12">
									<!-- Custom Tabs -->
									<div class="nav-tabs-custom">
										<ul class="nav nav-tabs">
											<li class="active"><a href="#tab_1" data-toggle="tab">Basic</a></li>
											<li><a href="#tab_2" data-toggle="tab">Options</a></li>
											<li><a href="#tab_3" data-toggle="tab">Advance Settings</a></li>
											<!-- <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li> -->
										</ul>
										<div class="tab-content">

											<div class="tab-pane active" id="tab_1">
												<div class="form-group">
													<label class="col-sm-4 control-label" for="menu_id">Menu ID:</label>
													<div class="col-sm-8">
														<input type="text" name="menu_id" id="menu_id" class="form-control" placeholder="Menu ID" minlength="4" required title="No Spaces. Minimum of 4 characters" value="<?php echo $output->menu_id[$i];?>">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-4 control-label" for="description">Description:</label>
													<div class="col-sm-8">
														<input type="text" name="description" id="description" class="form-control" placeholder="Description" minlength="4" required>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_name">Menu Name: </label>
													<div class="col-sm-8">
														<input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Menu Name" required value="<?php echo $output->menu_name[$i];?>">
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_prompt">Menu Greeting: </label>
													<div class="col-sm-8">
														<select name="menu_prompt" id="menu_prompt" class="form-control">
															<option value="goWelcomeIVR" selected>-- Default Value --</option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++){
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>"><?php echo $file;?></option>		
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_timeout">Menu Timeout: </label>
													<div class="col-sm-8">
														<input type="number" name="menu_timeout" id="menu_timeout" class="form-control" value="10" min="0" required>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_timeout_prompt">Menu Timeout Greeting: </label>
													<div class="col-sm-8">
														<select name="menu_timeout_prompt " id="menu_timeout_prompt" class="form-control">
															<option value="" selected>-- Default Value --</option>
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++){
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>"><?php echo $file;?></option>		
															<?php
																}
															?>				
														</select>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_invalid_prompt">Menu Invalid Greeting: </label>
													<div class="col-sm-8">
														<select name="menu_invalid_prompt" id="menu_invalid_prompt" class="form-control">
															<option value="" selected>-- Default Value --</option>	
															<?php
																for($i=0;$i<count($voicefiles->file_name);$i++){
																	$file = substr($voicefiles->file_name[$i], 0, -4);
															?>
																<option value="<?php echo $file;?>"><?php echo $file;?></option>		
															<?php
																}
															?>				
														</select>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_repeat">Menu Repeat: </label>
													<div class="col-sm-8">
														<input type="number" name="menu_repeat" id="menu_repeat" class="form-control"value="<?php echo $output->menu_repeat[$i];?>" min="0" required>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_time_check">Menu Time Check: </label>
													<div class="col-sm-8">
														<select name="menu_time_check" id="menu_time_check" class="form-control">
															<option value="ADMIN" > Select Menu Time Check </option>		
														</select>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="call_time">Call Time: </label>
													<div class="col-sm-8">
														<select name="call_time" id="call_time" class="form-control">
															<option value="ADMIN" > Select Call Time </option>		
														</select>
													</div>
												</div>
												<div class="form-group">		
													<label class="col-sm-4 control-label" for="menu_repeat">Track call in realtime report: </label>
													<div class="col-sm-8"> 
														<select name="call_time" id="call_time" class="form-control">
															<option value="ADMIN" > Select Track Call </option>		
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-4 control-label" for="tracking_group">Tracking Groups: </label>
													<div class="col-sm-8">
														<select name="tracking_group" id="tracking_group" class="form-control">
														<?php
															for($i=0;$i<count($ingroups->group_id);$i++){
														?>
															<option value="<?php echo $ingroups->group_id[$i];?>">
																<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
															</option>									
														<?php
															}
														?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-4 control-label" for="user_groups">User Groups: </label>
													<div class="col-sm-8">
														<select name="user_groups" id="user_groups" class="form-control">
															<option value="ADMIN" > ADMIN - GOAUTODIAL ADMINISTRATORS </option>
															<option value="AGENTS" > AGENTS - GOAUTODIAL AGENTS </option>
															<option value="SUPERVISOR" > SUPERVISOR - SUPERVISOR </option>			
														</select>
													</div>
												</div>
											</div>
											<!-- /.tab-pane -->
											<div class="tab-pane" id="tab_2">
												<div class="form-group">
													<div class="col-lg-12">
														<div class="pull-right">
															<button type="button" class="btn btn-primary add-option">Add Option</button>
														</div>
													</div>
												</div>
												<div class="form-group to-clone-opt">
													<label class="col-sm-3 control-label" for="">Default Call Menu Entry:</label>
													<div class="col-lg-2">
														Option:
														<select class="form-control">
															<option selected>TIMEOUT</option>
														</select>
													</div>
													<div class="col-lg-2">
														Desription: 
														<input type="text" name="" id="" class="form-control" placeholder="Description" required value="Hangup">
													</div>
													<div class="col-lg-2">
														Route:
														<select class="form-control">
															<option selected>Hangup</option>
														</select>
													</div>
													<div class="col-lg-2">
														Audio File:
														<input type="text" name="" id="" class="form-control" placeholder="Description" required value="vm-goodbye">
													</div>
													<div class="col-lg-1 btn-remove"></div>
												</div>
												<div class="cloning-area"></div>
											</div>
											<!-- /.tab-pane -->
											<div class="tab-pane" id="tab_3">
												Advance Settings Here
											</div>

											<div id="modifyIVRresult"></div>
											<div class="form-group" style="padding:0px 50px;">
												<button type="button" class="btn btn-danger" id="modifyIVRDeleteButton" href=""><i class="fa fa-times"></i> Delete</button>

												<button type="submit" class="btn btn-primary pull-right" id="modifyIVROkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
											</div>
										</div>

									<!-- /.tab-content -->
									</div>
								</div>
							</form>
						</div>
						
						<?php
							}
						} else {
						# An error occured
							echo $output->result;
						}
                        
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}

	/*
	 * APIs for getting lists for the some of the forms
	 */
	$users = $ui->API_goGetAllUserLists();
	$ingroups = $ui->API_getInGroups();
	$voicemails = $ui->API_goGetVoiceMails();
	$phones = $ui->API_getPhonesList();
	$ivr = $ui->API_getIVR();
	$scripts = $ui->API_goGetAllScripts();
	$voicefiles = $ui->API_GetVoiceFilesList();


				// IF PHONE NUMBER / DID
					if($did != NULL) {
						$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
						$postfields["goUser"] = goUser; #Username goes here. (required)
						$postfields["goPass"] = goPass; #Password goes here. (required)
						$postfields["goAction"] = "goGetDIDInfo"; #action performed by the [[API:Functions]]. (required)
						$postfields["responsetype"] = responsetype; #json. (required)
						$postfields["did_id"] = $did; #Desired did. (required)
            
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
							for($i=0;$i<count($output->did_pattern);$i++){
						?>
					<script>
						$(window).ready(function() {
							var route = document.getElementById('route').value;

							if(route == "AGENT") {
							  $('#form_route_agent').show();
							  
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "IN_GROUP") {
							  $('#form_route_ingroup').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "PHONE") {
							  $('#form_route_phone').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "CALLMENU") {
							  $('#form_route_callmenu').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_exten').hide();
							}if(route == "VOICEMAIL") {
							  $('#form_route_voicemail').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_callmenu').hide();
							  $('#form_route_exten').hide();
							}if(route == "EXTEN") {
							  $('#form_route_exten').show();
							  
							  $('#form_route_agent').hide();
							  $('#form_route_ingroup').hide();
							  $('#form_route_phone').hide();
							  $('#form_route_voicemail').hide();
							  $('#form_route_callmenu').hide();
							}
						});
					</script>
				<!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<legend>MODIFY DID RECORD : <u><?php echo $output->did_id[$i];?></u></legend>
								
								<form id="modifydid">

							<!-- Custom Tabs -->
							<div role="tabpanel">
							<!--<div class="nav-tabs-custom">-->
								<ul role="tablist" class="nav nav-tabs">
									<li class="active"><a href="#tab_1" data-toggle="tab"><em class="fa fa-gear fa-lg"></em> Basic Settings</a></li>
									<li><a href="#tab_2" data-toggle="tab"><em class="fa fa-gears fa-lg"></em> Advanced Settings</a></li>
								</ul>
				               <!-- Tab panes-->
				               <div class="tab-content">

					               	<!-- BASIC SETTINGS -->
					                <div id="tab_1" class="tab-pane fade in active">

										<input type="hidden" name="modify_did" value="<?php echo $output->did_id[$i];?>">
									<fieldset>
										<div class="form-group mt">
											<label for="did_pattern" class="col-sm-2 control-label">DID NUMBER</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="did_pattern" id="did_pattern" value="<?php echo $output->did_pattern[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="desc" class="col-sm-2 control-label">Description</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="desc" id="desc" value="<?php echo $output->did_description[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label">Status</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="status" id="status">
												<?php
													$status = NULL;
													if($output->active[$i] == "Y"){
														$status .= '<option value="Y" selected> YES </option>';
													}else{
														$status .= '<option value="Y" > YES </option>';
													}
													
													if($output->active[$i] == "N" || $output->active[$i] == NULL){
														$status .= '<option value="N" selected> NO </option>';
													}else{
														$status .= '<option value="N" > NO </option>';
													}
													echo $status;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="route" class="col-sm-2 control-label">DID Route</label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="route" name="route">
													<?php
														$route = NULL;
														if($output->did_route [$i] == "AGENT"){
															$route .= '<option value="AGENT" selected> Agent </option>';
														}else{
															$route .= '<option value="AGENT" > Agent </option>';
														}
														
														if($output->did_route [$i] == "IN_GROUP"){
															$route .= '<option value="IN_GROUP" selected> In-group </option>';
														}else{
															$route .= '<option value="IN_GROUP" > In-group </option>';
														}
														
														if($output->did_route [$i] == "PHONE"){
															$route .= '<option value="PHONE" selected> Phone </option>';
														}else{
															$route .= '<option value="PHONE" > Phone </option>';
														}
														
														if($output->did_route [$i] == "CALLMENU "){
															$route .= '<option value="CALLMENU" selected> Call Menu / IVR </option>';
														}else{
															$route .= '<option value="CALLMENU" > Call Menu / IVR </option>';
														}
														
														if($output->did_route [$i] == "VOICEMAIL"){
															$route .= '<option value="VOICEMAIL" selected> Voicemail </option>';
														}else{
															$route .= '<option value="VOICEMAIL" > Voicemail </option>';
														}
														
														if($output->did_route [$i] == "EXTEN"){
															$route .= '<option value="EXTEN" selected> Custom Extension </option>';
														}else{
															$route .= '<option value="EXTEN" > Custom Extension </option>';
														}
														echo $route;
													?>
												</select>
											</div>
										</div>
									</fieldset>
									<fieldset>
										<!-- IF DID ROUTE = AGENT-->
										<div id="form_route_agent" style="display: none;">
											<div class="form-group">
												<label for="route_agentid" class="col-sm-3 control-label">Agent ID: </label>
												<div class="col-sm-9 mb">
													<select name="route_agentid" id="route_agentid" class="form-control">
														<option value="" > -- NONE -- </option>
														<?php
															for($i=0;$i<count($users->userno);$i++){
														?>
															<option value="<?php echo $users->userno[$i];?>">
																<?php echo $users->userno[$i].' - '.$users->full_name[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="route_unavail" class="col-sm-3 control-label">Agent Unavailable Action: </label>
												<div class="col-sm-9 mb">
													<select name="route_unavail" id="route_unavail" class="form-control">
														<option value="EXTEN" > Custom Extension </option>
														<option value="IN_GROUP" > In-group </option>
														<option value="PHONE" > Phone </option>
														<option value="VOICEMAIL" > Voicemail </option>												
													</select>
												</div>
											</div>
												<!-- FOR AGENT UNAVAILABLE ACTION -->
												<!--IF route_unavail = EXTEN -->
													<div class="form-group" id="ru_exten" style="display: none;">
														<label for="ru_exten" class="col-sm-3 control-label">Extension</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" name="ru_exten" id="ru_exten" value="<?php echo $output->did_pattern[$i];?>">
														</div>
													</div>
												<!--IF route_unavail = INGROUP -->
													<div class="form-group" id="ru_ingroup" style="display: none;">
														<label for="ru_ingroup" class="col-sm-3 control-label">Ingroup</label>
														<div class="col-sm-9 mb">
															<select name="ru_ingroup" id="ru_ingroup" class="form-control">
																<?php
																	for($i=0;$i<count($ingroups->group_id);$i++){
																?>
																	<option value="<?php echo $ingroups->group_id[$i];?>">
																		<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
																	</option>									
																<?php
																	}
																?>
															</select>
														</div>
													</div>
												<!--IF route_unavail = PHONE -->
													<div class="form-group" id="ru_phone" style="display: none;">
														<label for="exten" class="col-sm-3 control-label">Phone</label>
														<div class="col-sm-9 mb">
															<select name="ru_phone" id="ru_phone" class="form-control">
																<?php
																	for($i=0;$i<count($phones->extension);$i++){
																?>
																	<option value="<?php echo $phones->extension[$i];?>">
																		<?php echo $phones->extension[$i].' - '.$phones->server_ip[$i].' - '.$phones->dialplan_number[$i];?>
																	</option>									
																<?php
																	}
																?>
															</select>
														</div>
													</div>
												<!--IF route_unavail = VOICEMAIL -->
													<div class="form-group" id="ru_voicemail" style="display: none;">
														<label for="exten" class="col-sm-3 control-label">Voicemail</label>
														<div class="col-sm-9 mb">
															<input type="text" class="form-control" name="exten" id="exten" value="<?php echo $output->did_pattern[$i];?>">
														</div>
													</div>
											<div class="form-group">
												<label for="route_settings" class="col-sm-3 control-label">Agent Route Settings: </label>
												<div class="col-sm-9 mb">
													<select name="route_settings" id="route_settings" class="form-control">
														<option value="">
															---NONE---
														</option>	
													<?php
														for($i=0;$i<count($ingroups->group_id);$i++){
															if($ingroups->group_id[$i] != "AGENTDIRECT"){
													?>
														<option value="<?php echo $ingroups->group_id[$i];?>">
															<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
														</option>									
													<?php
															}
														}
													?>
													</select>
												</div>
											</div>
										</div><!-- end of div agent-->
										
									<!-- IF DID ROUTE = IN-GROUP-->
										<div id="form_route_ingroup" class="form-group" style="display: none;">										
											<label for="route_ingroupid" class="col-sm-3 control-label">In-Group ID: </label>
											<div class="col-sm-9 mb">
												<select name="route_ingroupid" id="route_ingroupid" class="form-control">
													<?php
														for($i=0;$i<count($ingroups->group_id);$i++){
													?>
														<option value="<?php echo $ingroups->group_id[$i];?>">
															<?php echo $ingroups->group_id[$i].' - '.$ingroups->group_name[$i];?>
														</option>									
													<?php
														}
													?>
												</select>
											</div>
										</div><!-- end of ingroup div -->
										
									<!-- IF DID ROUTE = PHONE -->

										<div id="form_route_phone" style="display: none;">
											<div class="form-group">
												<label  for="route_phone_exten" class="col-sm-3 control-label">Phone Extension: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_exten" id="route_phone_exten" class="form-control">
														<?php
															for($i=0;$i<count($phones->extension);$i++){
														?>
															<option value="<?php echo $phones->extension[$i];?>">
																<?php echo $phones->extension[$i].' - '.$phones->server_ip[$i].' - '.$phones->dialplan_number[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="route_phone_server" class="col-sm-3 control-label">Server IP: </label>
												<div class="col-sm-9 mb">
													<select name="route_phone_server" id="route_phone_server" class="form-control">
														<option value="" > -- NONE -- </option>
														<?php
															for($i=0;$i < 1;$i++){
														?>
															<option value="<?php echo $phones->server_ip[$i];?>">
																<?php echo 'GOautodial - '.$phones->server_ip[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div><!-- end of phone div -->
										
									<!-- IF DID ROUTE = IVR -->
										<div id="form_route_callmenu" style="display: none;">
											<div class="form-group">
												<label for="route_ivr" class="col-sm-3 control-label">Call Menu: </label>
												<div class="col-sm-9 mb">
													<select name="route_ivr" id="route_ivr" class="form-control">
														<?php
															for($i=0;$i<count($ivr->menu_id);$i++){
														?>
															<option value="<?php echo $ivr->menu_id[$i];?>">
																<?php echo $ivr->menu_id[$i].' - '.$ivr->menu_name[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div><!-- end of ivr div -->
										
									<!-- IF DID ROUTE = VoiceMail -->
										<div id="form_route_voicemail" style="display: none;">
											<div class="form-group">
												<label for="route_voicemail" class="col-sm-3 control-label">Voicemail Box: </label>
												<div class="col-sm-9 mb">
													<select name="route_voicemail" id="route_voicemail" class="form-control">
														<?php
															for($i=0;$i<count($voicemails->voicemail_id);$i++){
														?>
															<option value="<?php echo $voicemails->voicemail_id[$i];?>">
																<?php echo $voicemails->voicemail_id[$i].' - '.$voicemails->fullname[$i];?>
															</option>									
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div><!-- end of voicemail div -->
										
										<!-- IF DID ROUTE = Custom Extension -->
										<div id="form_route_exten" style="display: none;">
											<div class="form-group">
												<label for="route_exten" class="col-sm-3 control-label">Extension: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten" id="route_exten" placeholder="Extension" class="form-control" required>
												</div>
											</div>
											<div class="form-group">
												<label for="route_exten_context" class="col-sm-3 control-label">Extension Context: </label>
												<div class="col-sm-9 mb">
													<input type="text" name="route_exten_context" id="route_exten_context" placeholder="Extension Context" class="form-control" required>
												</div>
											</div>
										</div><!-- end of custom extension div -->
									</fieldset>

									</div><!-- end of basic settings-->
								

						       		<!-- ADVANCED SETTINGS -->
						       		<div id="tab_2" class="tab-pane fade in">
						       			<fieldset>
							       			<div class="form-group mt">
							       				<label for="cid_num" class="col-sm-2 control-label">Clean CID Number</label>
							       				<div class="col-sm-10 mb">
													<input type="text" class="form-control" name="cid_num" id="cid_num" value="">
												</div>
							       			</div>
							       		</fieldset>				       			
						       		</div>
							
								<!-- FOOTER BUTTONS -->
								   	<div id="modifyDIDresult"></div>

								   	<fieldset>
				                        <div class="box-footer">
				                           <div class="pull-right col-sm-3">
												<a href="telephonyinbound.php" type="button" class="btn btn-danger pull-left"><i class="fa fa-close"></i> Cancel </a>
				                           		
				                                <button type="submit" class="btn btn-primary pull-right" id="modifyDIDOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
												
				                           </div>
				                        </div>
				                    </fieldset>

								</div><!-- end of content -->
							</div>
							</form>	
						</div>
					</div><!-- body -->
                </section>
						<?php		
							}
						} else {
						# An error occured
							echo $output->result;
						}
                        
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
					
					?>
			
           
			
        </div><!-- ./wrapper -->
         <?php print $ui->creamyFooter(); ?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

	<!-- SLIMSCROLL-->
    <script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- bootstrap color picker -->
	<script src="adminlte/colorpicker/bootstrap-colorpicker.min.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {

			//Colorpicker
    		$(".colorpicker").colorpicker();
			
			//  alert( this.value ); // or $(this).val()

			$('#route_unavail').on('change', function() {
				//  alert( this.value ); // or $(this).val()
				if(this.value == "EXTEN") {
				  $('#ru_exten').show();
				  
				  $('#ru_phone').hide();
				  $('#ru_ingroup').hide();
				  $('#ru_voicemail').hide();
				}if(this.value == "IN_GROUP") {
				  $('#ru_ingroup').show();
				  
				  $('#ru_exten').hide();
				  $('#ru_phone').hide();
				  $('#ru_voicemail').hide();
				}if(this.value == "PHONE") {
				  $('#ru_phone').show();
				  
				  $('#ru_exten').hide();
				  $('#ru_ingroup').hide();
				  $('#ru_voicemail').hide();
				}if(this.value == "VOICEMAIL") {
				  $('#ru_voicemail').show();
				  
				  $('#ru_exten').hide();
				  $('#ru_ingroup').hide();
				  $('#ru_phone').hide();
				}
				
			});

			$('#route').on('change', function() {
				//  alert( this.value ); // or $(this).val()
				if(this.value == "AGENT") {
				  $('#form_route_agent').show();
				  
				  $('#form_route_ingroup').hide();
				  $('#form_route_phone').hide();
				  $('#form_route_callmenu').hide();
				  $('#form_route_voicemail').hide();
				  $('#form_route_exten').hide();
				}if(this.value == "IN_GROUP") {
				  $('#form_route_ingroup').show();
				  
				  $('#form_route_agent').hide();
				  $('#form_route_phone').hide();
				  $('#form_route_callmenu').hide();
				  $('#form_route_voicemail').hide();
				  $('#form_route_exten').hide();
				}if(this.value == "PHONE") {
				  $('#form_route_phone').show();
				  
				  $('#form_route_agent').hide();
				  $('#form_route_ingroup').hide();
				  $('#form_route_callmenu').hide();
				  $('#form_route_voicemail').hide();
				  $('#form_route_exten').hide();
				}if(this.value == "CALLMENU") {
				  $('#form_route_callmenu').show();
				  
				  $('#form_route_agent').hide();
				  $('#form_route_ingroup').hide();
				  $('#form_route_phone').hide();
				  $('#form_route_voicemail').hide();
				  $('#form_route_exten').hide();
				}if(this.value == "VOICEMAIL") {
				  $('#form_route_voicemail').show();
				  
				  $('#form_route_agent').hide();
				  $('#form_route_ingroup').hide();
				  $('#form_route_phone').hide();
				  $('#form_route_callmenu').hide();
				  $('#form_route_exten').hide();
				}if(this.value == "EXTEN") {
				  $('#form_route_exten').show();
				  
				  $('#form_route_agent').hide();
				  $('#form_route_ingroup').hide();
				  $('#form_route_phone').hide();
				  $('#form_route_voicemail').hide();
				  $('#form_route_callmenu').hide();
				}
				
			});

				/** 
				 * Modifies 
			 	 */
				//an ingroup
				$('#modifyInboundOkButton').click(function(){
					$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
					$('#modifyInboundOkButton').prop("disabled", true);

					$("#resultmessage").html();
					$("#resultmessage").fadeOut();
					$.ajax({
                        url: "./php/ModifyTelephonyInbound.php",
                        type: 'POST',
                        data: $("#modifyingroup").serialize(),
                        success: function(data) {
                          //if message is sent
							if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
							<?php 
								$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
								print $ui->fadingInMessageJS($errorMsg, "modifyINGROUPresult"); 
							?>
							window.setTimeout(function(){location.replace("./telephonyinbound.php")},2000);
							} else {
							<?php 
								$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
								print $ui->fadingInMessageJS($errorMsg, "modifyINGROUPresult");
							?>
							$('#update_button').html("<i class='fa fa-check'></i> Update");
							$('#modifyInboundOkButton').prop("disabled", false);	
							}
							//
                        }
                    });		
					return false; //don't let the form refresh the page...		
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
				$("#modifydid").validate({
                	submitHandler: function() {
						//submit the form
							$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
							$('#modifyDIDOkButton').prop("disabled", true);
							
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyInbound.php", //post
							$("#modifydid").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyDIDresult"); 
									?>
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyDIDOkButton').prop("disabled", false);
									window.setTimeout(function(){location.replace("./telephonyinbound.php")},2000);
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyDIDresult");
									?>
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyDIDOkButton').prop("disabled", false);	
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});

				$('.add-option').click(function(){
					var toClone = $('.to-clone-opt').clone();

					toClone.removeClass('to-clone-opt');
					toClone.find('label.control-label').text('');
					toClone.find('.btn-remove').append('<span class="fa fa-remove fa-2x text-red remove-row"></span>');

					$('.cloning-area').append(toClone);
				});

				$(document).on('click', '.remove-row', function(){
					var row = $(this).parent().parent();
					
					row.remove();
				});
			});
		</script>

    </body>
</html>
