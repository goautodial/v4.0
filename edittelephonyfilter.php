<?php
/**
 * @file 		edittelephonyfilter.php
 * @brief 		Edit scripts
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author		Christopher P. Lomuntad
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	

	$filter_id = NULL;
	if (isset($_POST["filter_id"])) {
		$filter_id = $_POST["filter_id"];
	}else{
		header("location: telephonyfilters.php");
	}

	$user_groups = $api->API_getAllUserGroups();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("portal_title"); ?> - <?php $lh->translateText("edit_filter"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>
		
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
                        <small><?php $lh->translateText("edit_filter"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["filter_id"])){
						?>
							<li><a href="./telephonyfilters.php"><?php $lh->translateText("filters"); ?></a></li>
                        <?php
							}
                        ?>
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

						<!-- standard custom edition form -->
					<?php
						$errormessage = NULL;
						$output = $api->API_getFilterInfo($filter_id);
					?>
            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
		<legend><?php $lh->translateText("modify_filter"); ?> : <u><?php echo $filter_id;?></u></legend>
		<form id="modifyform">
				<input type="hidden" name="modifyid" value="<?php echo $filter_id;?>">
				<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
				<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
		<!-- Custom Tabs -->
		<div role="tabpanel">
		<!--<div class="nav-tabs-custom">-->
			<ul role="tablist" class="nav nav-tabs nav-justified">
				<li class="active"><a href="#tab_1" data-toggle="tab"> <?php $lh->translateText("basic_settings"); ?></a></li>
			</ul>
		   <!-- Tab panes-->
		   <div class="tab-content">
		<!-- BASIC SETTINGS -->
		<div id="tab_1" class="tab-pane fade in active">
			<fieldset>
				<div class="form-group mt">
					<label for="filter_name" class="col-sm-2 control-label"><?php $lh->translateText("filter_name"); ?></label>
					<div class="col-sm-10 mb">
						<input type="text" class="form-control" name="filter_name" id="filter_name" placeholder="<?php $lh->translateText("filter_name"); ?> (<?php $lh->translateText("mandatory"); ?>)" value="<?php echo $output->filter_name;?>" <?php if ($_SESSION['usergroup'] !== "ADMIN" && $filter_id === "FILTEMP") { echo "readonly"; } ?>>
					</div>
				</div>
				<div class="form-group mt">
					<label for="filter_comments" class="col-sm-2 control-label"><?php $lh->translateText("filter_comments"); ?></label>
					<div class="col-sm-10 mb">
						<input type="text" class="form-control" name="filter_comments" id="filter_comments" placeholder="<?php $lh->translateText("filter_comments"); ?>" value="<?php echo $output->filter_comments;?>" <?php if ($_SESSION['usergroup'] !== "ADMIN" && $filter_id === "FILTEMP") { echo "readonly"; } ?>>
					</div>
				</div>
				<div class="form-group<?=($_SESSION['usergroup'] !== 'ADMIN' ? ' hidden' : '')?>">
					<label for="filter_user_group" class="col-sm-2 control-label"><?php $lh->translateText("user_group"); ?> </label>
					<div class="col-sm-10 mb">
						<select class="form-control" name="filter_user_group" id="filter_user_group">
							<option value="" disabled selected> - - - <?php $lh->translateText('Select User Group'); ?> - - -</option>
							<?php
							if ($user_groups->result == 'success') {
								$myGroup = $output->user_group;
								$isSelected = '';
								if ($myGroup === '---ALL---') {
									$isSelected = ' selected';
								}
								echo '<option value="---ALL---"'.$isSelected.'> - - - ALL - - -</option>';
								
								foreach ($user_groups->user_group as $x => $group) {
									$isSelected = '';
									if ($group == $myGroup) {
										$isSelected = ' selected';
									}
									$group_name = (strlen($user_groups->group_name[$x]) > 0) ? $user_groups->group_name[$x] : $group;
									echo '<option value="'.$group.'"'.$isSelected.'>'.$group_name.'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="filter_sql" class="col-sm-2 control-label"><?php $lh->translateText("filter_sql"); ?></label>
					<div class="col-sm-10">
						<div class="panel">
							<div class="panel-body">
								<textarea rows="14" class="form-control note-editor" id="filter_sql" name="filter_sql" <?php if ($_SESSION['usergroup'] !== "ADMIN" && $filter_id === "FILTEMP") { echo "readonly"; } ?>><?php echo str_replace('Â', '', htmlspecialchars_decode($output->filter_sql, ENT_QUOTES));?></textarea>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div><!-- tab 1 -->
				
			<!-- FOOTER BUTTONS -->
			<fieldset class="footer-buttons">
				<div class="box-footer">
				   <div class="col-sm-3 pull-right">
						<a href="telephonyfilters.php" id="cancel" type="button" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
						<?php
						if ($_SESSION['usergroup'] === "ADMIN" || ($_SESSION['usergroup'] !== "ADMIN" && $filter_id !== "FILTEMP")) {
						?>
						<button type="submit" class="btn btn-primary" id="modifyOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>
						<?php
						}
						?>
				   </div>
				</div>
			</fieldset>
			</div><!-- end of tab content -->
	                    	</div><!-- tab panel -->
	                    </form>
	                </div><!-- body -->
	            </div>
            </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
			
        </div><!-- ./wrapper -->

  		
		<?php print $ui->standardizedThemeJS();?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		
		<script language="javascript" type="text/javascript">
			$(document).ready(function() {
				$(document).on('click', '#cancel', function(){
					sweetAlert({title: "<?php $lh->translateText("cancelled"); ?>",text: "<?php $lh->translateText("cancel_msg"); ?>", type: "error"}, function(){window.location.href = 'telephonyfilters.php';});
				});

				/** 
				 * Modifies a telephony script
			 	 */
			 	$(document).on('click', '#modifyOkButton', function(){
			 		$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#modifyOkButton').prop("disabled", true);
					$.ajax({
                        url: "./php/ModifyFilter.php",
                        type: 'POST',
                        //data: $("#modifyform").serialize() + '&filter_sql_value=' + encodeURIComponent($('#filter_sql').text()),
                        data: $("#modifyform").serialize(),
                        success: function(data) {
                        	console.log(data);
                        	//console.log($("#modifyform").serialize() + '&script_text_value=' + encodeURIComponent(CKEDITOR.instances['script_text'].getData()));
							$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
	                        $('#modifyOkButton').prop("disabled", false);
							
	                        if (data == 1) {
								swal({title: "<?php $lh->translateText("edit_filter_success"); ?>",text: "<?php $lh->translateText("edit_filter_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonyfilters.php';});
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>", data, "error");
							}
                        }
                    });
				});
				
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
