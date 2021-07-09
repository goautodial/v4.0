<?php
/**
 * @file 		edittelephonyscript.php
 * @brief 		Edit scripts
 * @copyright 	Copyright (c) 2020 GOautodial Inc. 
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
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

	$script_id = NULL;
	if (isset($_POST["script_id"])) {
		$script_id = $_POST["script_id"];
	}else{
		header("location: telephonyscripts.php");
	}

	$user_groups = $api->API_getAllUserGroups();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("portal_title"); ?> - <?php $lh->translateText("edit_script"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>
        <script src="js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
        <script src="js/plugins/ckeditor/styles.js" type="text/javascript"></script>
		
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
    </style>
    <?php print $ui->creamyBody(); ?>
    <?php 
	 	$standard_fields = $api->API_getStandardFields();
	?>
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
                        <small><?php $lh->translateText("edit_script"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["script_id"])){
						?>
							<li><a href="./telephonyscripts.php"><?php $lh->translateText("scripts"); ?></a></li>
                        <?php
							}
                        ?>
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

						<!-- standard custom edition form -->
					<?php
						$errormessage = NULL;
						$output = $api->API_getScriptInfo($script_id);
					?>
            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
		<legend><?php $lh->translateText("modify_script"); ?> : <u><?php echo $script_id;?></u></legend>
		<form id="modifyform">
				<input type="hidden" name="modifyid" value="<?php echo $script_id;?>">
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
					<label for="script_name" class="col-sm-2 control-label"><?php $lh->translateText("script_name"); ?></label>
					<div class="col-sm-10 mb">
						<input type="text" class="form-control" name="script_name" id="script_name" placeholder="<?php $lh->translateText("script_name"); ?> (<?php $lh->translateText("mandatory"); ?>)" value="<?php echo $output->script_name;?>">
					</div>
				</div>
				<div class="form-group mt">
					<label for="script_comments" class="col-sm-2 control-label"><?php $lh->translateText("script_comment"); ?></label>
					<div class="col-sm-10 mb">
						<input type="text" class="form-control" name="script_comments" id="script_comments" placeholder="<?php $lh->translateText("script_comments"); ?>" value="<?php echo $output->script_comments;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("active"); ?></label>
					<div class="col-sm-10 mb">
						<select class="form-control" name="active" id="active">
						<?php
							$active = NULL;
							if($output->active == "Y"){
								$active .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
							}else{
								$active .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
							}
							
							if($output->active == "N" || $output->active == NULL){
								$active .= '<option value="N" selected> '.$lh->translationFor("go_no").' </option>';
							}else{
								$active .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
							}
							echo $active;
						?>
						</select>
					</div>
				</div>
				<div class="form-group<?=($_SESSION['usergroup'] !== 'ADMIN' ? ' hidden' : '')?>">
					<label for="script_user_group" class="col-sm-2 control-label"><?php $lh->translateText("user_group"); ?>: </label>
					<div class="col-sm-10 mb">
						<select class="form-control" name="script_user_group" id="script_user_group">
							<option value="" disabled selected> - - - <?php $lh->translateText('Select User Group'); ?> - - -</option>
							<?php
							if ($user_groups->result == 'success') {
								$myGroup = $output->user_group;
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
					<label for="script_text" class="col-sm-2 control-label"><?php $lh->translateText("script_text"); ?></label>
					<div class="col-sm-10 mb">
						<div class="row">
							<div class="col-sm-12 mb">
								<div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-default" onClick="addtext();"><?php $lh->translateText("insert"); ?></button>
									</span>
									<select class="form-control" name="script_text_dropdown" id="script_text_dropdown">
										<option value="--A--fullname--B--">Agent Name (fullname)</option>
										<?php foreach($standard_fields->field_name as $sf) { ?>
											<option value="--A--<?php echo $sf; ?>--B--"><?php echo $sf; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-1">&nbsp;</div>
					<div class="col-sm-11">
						<?php //echo str_replace("\\\"", "", htmlspecialchars_decode($output->script_text, ENT_QUOTES)); ?>
						<div class="panel">
							<div class="panel-body">
									
								<textarea rows="14" class="form-control note-editor" id="script_text" name="script_text">
									<?php //echo str_replace("\\\"", "", htmlspecialchars_decode($output->script_text, ENT_QUOTES)); 
									$filtered_script = str_replace('Â', '', htmlspecialchars_decode($output->script_text, ENT_QUOTES));
									$filtered_script = stripslashes(str_replace("\"", "", $filtered_script));
									echo $filtered_script; ?>
								</textarea>
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
						<a href="telephonyscripts.php" id="cancel" type="button" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
						<button type="submit" class="btn btn-primary" id="modifyOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>						
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
		
		<script>
	  	$(function () {
		    // Replace the <textarea id="editor1"> with a CKEditor
		    // instance, using default configuration.
		    // CKEDITOR.replace('script_text');
		    //bootstrap WYSIHTML5 - text editor
		    // $(".textarea").wysihtml5();
		  });
		</script>
		<script language="javascript" type="text/javascript">
			$(document).ready(function() {
				CKEDITOR.replace("script_text",
					{
		                toolbar: [
		                    // { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
							{ name: 'clipboard', items: [ 'Source', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
							{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
							{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
							'/',
							{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
							{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
							{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
							{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
							'/',
							{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
							{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
							{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
							{ name: 'about', items: [ 'About' ] }
		                ],
		                allowedContent: {
			                script: true,
			                div: true,
			                span: true,
			                $1: {
			                    // This will set the default set of elements
			                    elements: CKEDITOR.dtd,
			                    attributes: true,
			                    styles: true,
			                    classes: true
			                }
			            }
	            	}
				);
				$(document).on('click', '#cancel', function(){
					sweetAlert({title: "<?php $lh->translateText("cancelled"); ?>",text: "<?php $lh->translateText("cancel_msg"); ?>", type: "error"}, function(){window.location.href = 'telephonyscripts.php';});
				});

				/** 
				 * Modifies a telephony script
			 	 */
			 	$(document).on('click', '#modifyOkButton', function(){
			 		$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#modifyOkButton').prop("disabled", true);
					$.ajax({
                        url: "./php/ModifyScript.php",
                        type: 'POST',
                        data: $("#modifyform").serialize() + '&script_text_value=' + encodeURIComponent(CKEDITOR.instances['script_text'].getData()),
                        success: function(data) {
                        	console.log(data);
                        	//console.log($("#modifyform").serialize() + '&script_text_value=' + encodeURIComponent(CKEDITOR.instances['script_text'].getData()));
							$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
	                        $('#modifyOkButton').prop("disabled", false);
							
	                        if (data == 1) {
								swal({title: "<?php $lh->translateText("edit_script_success"); ?>",text: "<?php $lh->translateText("edit_script_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonyscripts.php';});
							} else {
								sweetAlert("<?php $lh->translateText("oups"); ?>", data, "error");
							}
                        }
                    });
				});
				
			});

			function addtext() {
				var txtarea = document.getElementById('script_text');
				var text = document.getElementById('script_text_dropdown').value;
				CKEDITOR.instances.script_text.insertText( text );

				/* if (!txtarea) { return; }

				 var scrollPos = txtarea.scrollTop;
				 var strPos = 0;
				 var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
				 	"ff" : (document.selection ? "ie" : false ) );
				 if (br == "ie") {
				 	txtarea.focus();
				 	var range = document.selection.createRange();
				 	range.moveStart ('character', -txtarea.value.length);
				 	strPos = range.text.length;
				 } else if (br == "ff") {
				 	strPos = txtarea.selectionStart;
				 }

				 var front = (txtarea.value).substring(0, strPos);
				 var back = (txtarea.value).substring(strPos, txtarea.value.length);
				 txtarea.value = front + text + back;
				 strPos = strPos + text.length;
				 if (br == "ie") {
					txtarea.focus();
				 	var ieRange = document.selection.createRange();
				 	ieRange.moveStart ('character', -txtarea.value.length);
				 	ieRange.moveStart ('character', strPos);
				 	ieRange.moveEnd ('character', 0);
				 	ieRange.select();
				 } else if (br == "ff") {
				 	txtarea.selectionStart = strPos;
				 	txtarea.selectionEnd = strPos;
				 	txtarea.focus();
				 }
				
				 txtarea.scrollTop = scrollPos;*/
			}
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
