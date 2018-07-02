<?php
/**
 * @file 		editsettingsphones.php
 * @brief 		Modify phone entries
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com> 
 * @author		Demian Lizandro A. Biscocho <demian@goautodial.com>
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

$extenid = NULL;
if (isset($_POST["extenid"])) {
	$extenid = $_POST["extenid"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Phone Extension</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       
       	<?php print $ui->standardizedThemeCSS(); ?>       

        <?php print $ui->creamyThemeCSS(); ?>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
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
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("Phone Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["extenid"])){
						?>	
							<li><a href="./telephonyusers.php?phone_tab"><?php $lh->translateText("phones"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">

						<!-- standard custom edition form -->
					<?php
						$errormessage = NULL;
						$output = $api->API_getPhoneInfo($extenid);
					?>
                    
                    <div class="panel-body">
                    	<legend>MODIFY PHONE EXTENSION : <u><?php echo $output->extension;?></u></legend>
		<form id="modifyphones">
			<input type="hidden" name="modifyid" value="<?php echo $extenid;?>">
			<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>" />
			<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>" />
	
	<!-- BASIC SETTINGS -->
		<div role="tabpanel">
		<!--<div class="nav-tabs-custom">-->
			<ul role="tablist" class="nav nav-tabs nav-justified">
				<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
			</ul>
		   <!-- Tab panes-->
		   <div class="tab-content">
			<!-- BASIC SETTINGS -->
			<div id="tab_1" class="tab-pane fade in active">
				<fieldset>
					<div class="form-group">
						<label for="dialplan" class="col-sm-2 control-label">Dialplan Number</label>
						<div class="col-sm-10 mb">
							<input type="number" class="form-control" name="dialplan" id="dialplan" placeholder="Dialplan Number (Mandatory)" value="<?php echo $output->dialplan_number;?>">
						</div>
					</div>
					<div class="form-group">
						<label for="vmid" class="col-sm-2 control-label">Voicemail ID</label>
						<div class="col-sm-10 mb">
							<input type="text" class="form-control" name="vmid" id="vmid" value="<?php echo $output->voicemail_id;?>">
						</div>
					</div>
					<div class="form-group">
						<label for="ip" class="col-sm-2 control-label">Server IP</label>
						<div class="col-sm-10 mb">
							<input type="text" class="form-control" name="ip" id="ip" value="<?php echo $output->server_ip;?>">
						</div>
					</div>
					<div class="form-group">
						<label for="status" class="col-sm-2 control-label">Active Account</label>
						<div class="col-sm-10 mb">
							<select class="form-control" name="active" id="active">
							<?php
								$active = NULL;
								if($output->active == "Y"){
									$active .= '<option value="Y" selected> YES </option>';
								}else{
									$active .= '<option value="Y" > YES </option>';
								}
								
								if($output->active == "N" || $output->active == NULL){
									$active .= '<option value="N" selected> NO </option>';
								}else{
									$active .= '<option value="N" > NO </option>';
								}
								echo $active;
							?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="status" class="col-sm-2 control-label">Status</label>
						<div class="col-sm-10 mb">
							<select class="form-control" id="status" name="status">
								<?php
									$status = NULL;
									if($output->status == "ACTIVE"){
										$status .= '<option value="ACTIVE" selected> ACTIVE </option>';
									}else{
										$status .= '<option value="ACTIVE" > ACTIVE </option>';
									}
									
									if($output->status == "SUSPENDED"){
										$status .= '<option value="SUSPENDED" selected> SUSPENDED </option>';
									}else{
										$status .= '<option value="SUSPENDED" > SUSPENDED </option>';
									}
									
									if($output->status == "CLOSED"){
										$status .= '<option value="CLOSED" selected> CLOSED </option>';
									}else{
										$status .= '<option value="CLOSED" > CLOSED </option>';
									}
									
									if($output->status == "PENDING"){
										$status .= '<option value="PENDING" selected> PENDING </option>';
									}else{
										$status .= '<option value="PENDING" > PENDING </option>';
									}
									
									if($output->status == "ADMIN "){
										$status .= '<option value="ADMIN " selected> ADMIN  </option>';
									}else{
										$status .= '<option value="ADMIN " > ADMIN  </option>';
									}
									echo $status;
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fullname" class="col-sm-2 control-label">Fullname</label>
						<div class="col-sm-10 mb">
							<input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $output->fullname;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">New Messages: </label>
						<div class="col-sm-10 mb">
							<span style="padding-left:20px; font-size: 20;"><?php echo $output->messages;?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Old Messages: </label>
						<div class="col-sm-10 mb">
							<span style="padding-left:20px; font-size: 20;"><?php echo $output->old_messages;?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="protocol" class="col-sm-2 control-label">Client Protocol</label>
						<div class="col-sm-10 mb">
							<select class="form-control" id="protocol" name="protocol">
								<?php
									$protocol = NULL;
									if($output->protocol == "SIP"){
										$protocol .= '<option value="SIP" selected> SIP </option>';
									}else{
										$protocol .= '<option value="SIP"> SIP </option>';
									}
									
									if($output->protocol == "Zap"){
										$protocol .= '<option value="Zap" selected> Zap </option>';
									}else{
										$protocol .= '<option value="Zap"> Zap </option>';
									}
									
									if($output->protocol == "IAX2"){
										$protocol .= '<option value="IAX2" selected> IAX2 </option>';
									}else{
										$protocol .= '<option value="IAX2"> IAX2 </option>';
									}
									 
									if($output->protocol == "EXTERNAL"){
										$protocol .= '<option value="EXTERNAL" selected> EXTERNAL </option>';
									}else{
										$protocol .= '<option value="EXTERNAL"> EXTERNAL </option>';
									}
									echo $protocol;
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="change_pass" class="col-sm-2 control-label"><?php $lh->translateText("change_password"); ?></label>
						<div class="col-sm-10 mb">
							<select class="form-control " name="change_pass" id="change_pass">
								<option value="N" selected> No </option>
								<option value="Y" > Yes </option>
							</select>
						</div>
					</div>
					<div class="form-group form_password" style="display:none;">
						<label for="password" class="col-sm-2 control-label"><?php $lh->translateText("password"); ?></label>
						<div class="col-sm-10 mb">
							<input type="password" class="form-control" name="password" id="password" maxlength="20" placeholder="<?php $lh->translateText("password"); ?>" />
							<small><i><span id="pass_result"></span></i></small>
						</div>
					</div>
					<div class="form-group form_password" style="display:none;">
						<label for="conf_password" class="col-sm-2 control-label"><?php $lh->translateText("confirm_password"); ?></label>
						<div class="col-sm-10 mb">
							<input type="password" class="form-control" id="conf_password" placeholder="<?php $lh->translateText("confirm_password"); ?>" required />
							<span id="pass_result"></span></i></small>
						</div> 
					</div>					
				</fieldset>
			</div><!-- body -->
				<fieldset>
					<div class="box-footer">
					   <div class="col-sm-3 pull-right">
							<a href="telephonyusers.php?phone_tab" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
							<button type="submit" class="btn btn-primary" id="modifyPhoneOkButton" href="" data-id="<?php echo $output->extension; ?>"> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
					   </div>
					</div>
				</fieldset>
			</div>
		</div>
		</form>
	</div>
<?php
	/*
		}
	}*/
?>
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

	<!-- Modal Dialogs -->
	<?php print $ui->standardizedThemeJS(); ?>       
	<?php include_once "./php/ModalPasswordDialogs.php" ?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			
			// for cancelling
			$(document).on('click', '#cancel', function(){
				swal("Cancelled", "No action has been done :)", "error");
			});
			
			$('#change_pass').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "Y") 
				  $('.form_password').show();
				
				if(this.value == "N") 
				  $('.form_password').hide();
				
			});

			// password
			$("#password").keyup(checkPasswordMatch);
			$("#conf_password").keyup(checkPasswordMatch);
			
			$('#modifyPhoneOkButton').click(function(){ // on click submit
				$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
				$('#modifyPhoneOkButton').prop("disabled", true);

				// variables for check password
				var validate_password = 0;
				var change_pass = document.getElementById('change_pass').value;
				var password = document.getElementById('password').value;
				var conf_password = document.getElementById('conf_password').value;
				
				// conditional statements
				if(change_pass == "Y"){
					if(password != conf_password){
					validate_password = 1;
					}
					if(password == ""){
					validate_password = 2;
					}
				}

				// validate results
				if(validate_password == 1){
					$('#update_button').html("<i class='fa fa-check'></i> Update");
					$('#modifyPhoneOkButton').prop("disabled", false);	
				}
				if(validate_password == 2){
					$("#pass_result").html("<font color='red'><i class='fa fa-warning'></i> Input and Confirm Password, otherwise mark Change Password? as NO! </font>");
					$('#update_button').html("<i class='fa fa-check'></i> Update");
					$('#modifyPhoneOkButton').prop("disabled", false);
				}
				
				// validations
				if(validate_password == 0){
					$("#update_phones").prop("disabled", false);				
					$.ajax({
						url: "./php/ModifySettingsPhones.php",
						type: 'POST',
						data: $("#modifyphones").serialize(),
						success: function(data) {
							console.log(data);
							$("#update_phones").prop("disabled", true);
							if (data == 1) {
								$('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modifyPhoneOkButton').prop("disabled", false);
								swal(
									{
										title: "<?php $lh->translateText("success"); ?>",
										text: "<?php $lh->translateText("phone_update_success"); ?>",
										type: "success"
									},
									function(){
										location.replace("./telephonyusers.php?phone_tab");
									}
								);
							} else {
								sweetAlert("<?php $lh->translateText("oops"); ?>", "<?php $lh->translateText("something_went_wrong"); ?> " + data, "error");
								$('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modifyPhoneOkButton').prop("disabled", false);
							}
						}
					});
				}
				return false;	
			});
	});
	
	/**************
	** password validation
	**************/
	function checkPasswordMatch() {
		var password = $("#password").val();
		var confirmPassword = $("#conf_password").val();

		if (password != confirmPassword)
			$("#pass_result").html("<font color='red'>Passwords Do Not Match! <font size='5'>✖</font> </font>");
		else
			$("#pass_result").html("<font color='green'>Passwords Match! <font size='5'>✔</font> </font>");
	}	
</script>

<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
<?php print $ui->creamyFooter(); ?>
</body>
</html>
