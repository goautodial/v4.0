<?php

	###################################################
	### Name: edittelephonyscript.php 	   ###
	### Functions: Edit Scripts 	   ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	   ###
	### Version: 4.0 	   ###
	### Written by: Alexander Jim H. Abenoja	   ###
	### License: AGPLv2	   ###
	###################################################

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

	$script_id = NULL;
	if (isset($_POST["script_id"])) {
		$script_id = $_POST["script_id"];
	}else{
		header("location: telephonyscripts.php");
	}

	$user_groups = $ui->API_goGetUserGroupsList();
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
	 	$standard_fields = $ui->API_getAllStandardFields();
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

					//if(isset($extenid)) {
						$url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "getScriptInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["script_id"] = $script_id; #Desired exten ID. (required)

				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
						 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);
				         
				        // var_dump($output);

						if ($output->result=="success") {
						
						# Result was OK!
							for($i=0;$i<count($output->script_id);$i++){
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
						<input type="text" class="form-control" name="script_name" id="script_name" placeholder="<?php $lh->translateText("script_name"); ?> (<?php $lh->translateText("mandatory"); ?>)" value="<?php echo $output->script_name[$i];?>">
					</div>
				</div>
				<div class="form-group mt">
					<label for="script_comments" class="col-sm-2 control-label"><?php $lh->translateText("script_comment"); ?></label>
					<div class="col-sm-10 mb">
						<input type="text" class="form-control" name="script_comments" id="script_comments" placeholder="<?php $lh->translateText("script_comments"); ?>" value="<?php echo $output->script_comments[$i];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("active"); ?></label>
					<div class="col-sm-10 mb">
						<select class="form-control" name="active" id="active">
						<?php
							$active = NULL;
							if($output->active[$i] == "Y"){
								$active .= '<option value="Y" selected> '.$lh->translationFor("go_yes").' </option>';
							}else{
								$active .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
							}
							
							if($output->active[$i] == "N" || $output->active[$i] == NULL){
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
								$myGroup = $output->user_group[$i];
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
										<?php foreach($standard_fields as $sf) { ?>
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
						<div class="panel">
							<div class="panel-body">
								<textarea rows="14" class="form-control note-editor" id="script_text" name="script_text"><?php echo str_replace('Â', '', htmlspecialchars_decode($output->script_text, ENT_QUOTES));?></textarea>
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
					<?php
							}
						}	
                        
					?>
					
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
			 	$('#modifyOkButton').click(function(){
			 		$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#modifyOkButton').prop("disabled", true);
					$.ajax({
                        url: "./php/ModifyScript.php",
                        type: 'POST',
                        data: $("#modifyform").serialize() + '&script_text_value=' + encodeURIComponent(CKEDITOR.instances['script_text'].getData()),
                        success: function(data) {
                        	// console.log(data);
							$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
	                        $('#modifyOkButton').prop("disabled", false);
							
	                        if (data == "success") {
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

				// if (!txtarea) { return; }

				// var scrollPos = txtarea.scrollTop;
				// var strPos = 0;
				// var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
				// 	"ff" : (document.selection ? "ie" : false ) );
				// if (br == "ie") {
				// 	txtarea.focus();
				// 	var range = document.selection.createRange();
				// 	range.moveStart ('character', -txtarea.value.length);
				// 	strPos = range.text.length;
				// } else if (br == "ff") {
				// 	strPos = txtarea.selectionStart;
				// }

				// var front = (txtarea.value).substring(0, strPos);
				// var back = (txtarea.value).substring(strPos, txtarea.value.length);
				// txtarea.value = front + text + back;
				// strPos = strPos + text.length;
				// if (br == "ie") {
				// 	txtarea.focus();
				// 	var ieRange = document.selection.createRange();
				// 	ieRange.moveStart ('character', -txtarea.value.length);
				// 	ieRange.moveStart ('character', strPos);
				// 	ieRange.moveEnd ('character', 0);
				// 	ieRange.select();
				// } else if (br == "ff") {
				// 	txtarea.selectionStart = strPos;
				// 	txtarea.selectionEnd = strPos;
				// 	txtarea.focus();
				// }
				
				// txtarea.scrollTop = scrollPos;
			}
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
