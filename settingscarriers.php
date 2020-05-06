<?php
/**
 * @file        settingscarriers.php
 * @brief       Manage Carriers
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho
 * @author		Noel Umandap
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
*/

	require_once('php/UIHandler.php');
	require_once('php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//$gopackage = $api->API_getGOPackage();
	$perm = $api->goGetPermissions('carriers', $_SESSION['usergroup']);
	
	/*if( ($gopackage->show_carrier_settings === "N" || $gopackage->show_carrier_settings === NULL) && ($_SESSION['usergroup'] !== "ADMIN" && $_SESSION !== "goAPI") ){
		header("location:index.php");
	}*/
	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("carriers"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
	
    	<!-- Wizard Form style -->
		<link href="css/style.css" rel="stylesheet" type="text/css" />
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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("carriers_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("carriers"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($perm->carriers_read !== 'N') { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="recording_table">
                            <legend><?php $lh->translateText("carriers"); ?></legend>
							<?php print $ui->getListAllCarriers($perm); ?>
                        </div>
                    </div>
				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

		<div class="action-button-circle <?php if ($perm->carriers_create == 'N') { echo "hidden"; } ?>" data-toggle="modal" data-target="#wizard-modal">
			<?php print $ui->getCircleButton("carriers", "plus"); ?>
		</div>
<?php
	/*
	* MODAL
	*/
	$user_groups = $api->API_getAllUserGroups();
	$carriers = $api->API_getAllCarriers();
	$servers = $api->API_getAllServers();
?>
	<!-- ADD WIZARD MODAL -->
	<div class="modal fade" id="wizard-modal" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight">
						<b><?php $lh->translateText('carrier_wizard'); ?> » <?php $lh->translateText('add_new_carrier'); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body">

				<form id="create_form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
					<div class="row">
				<!-- STEP 1 -->
						<h4><?php $lh->translateText('choose_carrier_type'); ?>
							<br>
							<small><?php $lh->translateText('choose_carrier_sub_header'); ?></small>
						</h4>
						<fieldset>
							<br/><br/><br/><br/><br/><br/>
							<div class="form-group mt">
								<label class="col-sm-3 control-label" for="carrier_type"><?php $lh->translateText('carrier_type'); ?></label>
								<div class="col-sm-7">
									<select id="carrier_type" class="form-control" name="carrier_type">
										<option value="justgo">GoAutodial - JustGoVoIP</option>
										<option value="manual">Manual</option>
										<option value="copy">Copy Carrier</option>
									</select>
								</div>
							</div>
						</fieldset>
	
				<!-- STEP 2 -->
						<h4><?php $lh->translateText('set_chosen_carrier'); ?>
							<br>
							<small><?php $lh->translateText('chosen_carrier_sub_header'); ?></small>
						</h4>
						<fieldset>
						<!-- IF MANUAL / COPY -->
							<div class="manual_copy_div" style="display:none;">
								<div class="form-group mt">
												<label for="carrier_id" class="col-sm-3 control-label"><?php $lh->translateText('carrier_id'); ?></label>
												<div class="col-sm-8 mb">
													<input type="text" class="form-control" name="carrier_id" id="carrier_id" placeholder="<?php $lh->translateText('carrier_id'); ?>" maxlength="15" required />
												</div>
											</div>
								<div class="form-group">
									<label for="carrier_name" class="col-sm-3 control-label"><?php $lh->translateText('carrier_name'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="carrier_name" id="carrier_name" placeholder="<?php $lh->translateText('carrier_name'); ?>" required />
									</div>
								</div>
							</div>
						<!-- IF MANUAL -->
							<div class="manual_div" style="display:none;">
								<div class="form-group">
									<label for="carrier_description" class="col-sm-3 control-label"><?php $lh->translateText('carrier_description'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="carrier_description" id="carrier_description" placeholder="<?php $lh->translateText('carrier_description'); ?>" />
									</div>
								</div>
								<div class="form-group">
									<label for="user_groups" class="col-sm-3 control-label"><?php $lh->translateText('user_groups'); ?></label>
									<div class="col-sm-8 mb">
										<select id="user_group" class="form-control" name="user_group">
												<option value="---ALL---"><?php $lh->translateText('all_usergroups'); ?>   </option>
											<?php
												for($i=0;$i<count($user_groups->user_group);$i++){
											?>
												<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group custom_protocol" style="display:none;">
									<label for="registration_string" class="col-sm-3 control-label"><?php $lh->translateText('registration_string'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="registration_string" value="" id="registration_string" maxlength="255" size="50">
									</div>
								</div>
								<div class="form-group custom_protocol" style="display:none;">
									<label for="account_entry" class="col-sm-3 control-label"><?php $lh->translateText('account_entry'); ?></label>
									<div class="col-sm-8 mb">
										<textarea name="account_entry" class="form-control note-editor valid" cols="55" rows="10" id="account_entry" style="resize: none;">
[]
disallow=all
allow=gsm
allow=ulaw
type=friend
dtmfmode=rfc2833
context=trunkinbound
qualify=yes
insecure=invite,port
nat=force_rport,comedia
host=</textarea>
									</div>
								</div>
								<div class="form-group not_custom_protocol">
									<label for="authentication" class="col-sm-3 control-label"><?php $lh->translateText('authentication'); ?></label>
									<div class="col-sm-8 mb">
										<div class="row mt">
											<label class="col-sm-1">
												&nbsp;
											</label>
											<label class="col-sm-4 radio-inline c-radio" for="auth_ip">
												<input id="auth_ip" type="radio" name="authentication" value="auth_ip" checked>
												<span class="fa fa-circle"></span><?php $lh->translateText('ip_based'); ?> 
											</label>
											<label class="col-sm-4 radio-inline c-radio" for="auth_reg">
												<input id="auth_reg" type="radio" name="authentication" value="auth_reg">
												<span class="fa fa-circle"></span><?php $lh->translateText('registration'); ?> 
											</label>
										</div>
									</div>
								</div>
								<div class="form-group registration_div" style="display:none;">
									<label for="username" class="col-sm-3 control-label"><?php $lh->translateText('username'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="username" id="username" placeholder="Username" required />
									</div>
								</div>
								<div class="form-group registration_div" style="display:none;">
									<label for="password" class="col-sm-3 control-label"><?php $lh->translateText('password'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="password" id="password" placeholder="Password" required />
									</div>
								</div>
								<div class="form-group registration_div" style="display:none;">
									<label for="server_ip" class="col-sm-3 control-label"><?php $lh->translateText('server_ip_host'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="reg_host" id="reg_host" placeholder="<?php $lh->translateText('server_ip_host'); ?>" />
									</div>
								</div>
								<div class="form-group registration_div" style="display:none;">
									<label for="reg_port" class="col-sm-3 control-label"><?php $lh->translateText('port'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="reg_port" id="reg_host" placeholder="<?php $lh->translateText('port'); ?>" maxlength="10" value="5060" required />
									</div>
								</div>
								<div class="form-group not_custom_protocol sip_server">
									<label for="server_ip" class="col-sm-3 control-label"><?php $lh->translateText('sip_server'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="sip_server_ip" id="sip_server_ip" placeholder="<?php $lh->translateText('sip_server'); ?>" />
									</div>
								</div>
								<div class="form-group not_custom_protocol">
									<label for="dialprefix" class="col-sm-3 control-label"><?php $lh->translateText('dial_prefix'); ?></label>
									<div class="col-sm-8 mb">
										<input type="number" class="form-control" name="dialprefix" id="dialprefix" placeholder="<?php $lh->translateText('dial_prefix'); ?>" maxlength="15" minlength="3" min="0" required />
									</div>
								</div>
								<div class="form-group not_custom_protocol">
									<label for="codecs" class="col-sm-3 control-label"><?php $lh->translateText('codecs'); ?></label>
									<div class="col-sm-8 mb">
										<div class="row mt">
											<label class="col-sm-1">
												&nbsp;
											</label>
											<label class="col-sm-2 checkbox-inline c-checkbox" for="gsm">
												<input type="checkbox" id="gsm" name="codecs[]" value="GSM">
												<span class="fa fa-check"></span>GSM
											</label>
											<label class="col-sm-2 checkbox-inline c-checkbox" for="ulaw">
												<input type="checkbox" id="ulaw" name="codecs[]" value="ULAW" checked>
												<span class="fa fa-check"></span>ULAW 
											</label>
											<label class="col-sm-2 checkbox-inline c-checkbox" for="alaw">
												<input type="checkbox" id="alaw" name="codecs[]" value="ALAW">
												<span class="fa fa-check"></span>ALAW 
											</label>
											<label class="col-sm-2 checkbox-inline c-checkbox" for="g729">
												<input type="checkbox" id="g729" name="codecs[]" value="G729">
												<span class="fa fa-check"></span>G729
											</label>
										</div>
									</div>
								</div>
								<div class="form-group not_custom_protocol">
									<label for="dtmf_mode" class="col-sm-3 control-label"><?php $lh->translateText('dtmf_mode'); ?></label>
									<div class="col-sm-8 mb">
										<div class="row mt">
											<label class="col-sm-1">
												&nbsp;
											</label>
											<label class="col-sm-3 radio-inline c-radio" for="dtmf_1">
												<input id="dtmf_1" type="radio" name="dtmf" value="rfc2833" checked>
												<span class="fa fa-circle"></span>RFC2833
											</label>
											<label class="col-sm-3 radio-inline c-radio" for="dtmf_2">
												<input id="dtmf_2" type="radio" name="dtmf" value="inband">
												<span class="fa fa-circle"></span>Inband
											</label>
											<label class="col-sm-3 radio-inline c-radio" for="dtmf_3">
												<input id="dtmf_3" type="radio" name="dtmf" value="custom">
												<span class="fa fa-circle"></span>Custom  
											</label>
										</div>
									</div>
								</div>
								<div class="form-group" id="input_custom_dtmf" style="display:none;">
									<label for="custom_dtmf" class="col-sm-3 control-label"></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" id="custom_dtmf" name="custom_dtmf" placeholder="Enter Custom DTMF" required />
									</div>
								</div>
								<div class="form-group">
									<label for="protocol" class="col-sm-3 control-label"><?php $lh->translateText('protocol'); ?></label>
									<div class="col-sm-8 mb">
										<div class="row">
											<div class="col-sm-6">
												<select class="form-control" name="protocol" id="protocol">
												<?php
													$protocol = NULL;
														$protocol .= '<option value="SIP" > SIP </option>';
														$protocol .= '<option value="IAX2" > IAX2 </option>';
														$protocol .= '<option value="CUSTOM" > CUSTOM </option>';
													echo $protocol;
												?>
												</select>
											</div>
											<div class="col-sm-6">
												<select name="cust_protocol" id="cust_protocol" class="form-control custom_protocol" style="display:none;">
													<option value="SIP">SIP</option>
													<option value="Zap">ZAP</option>
													<option value="IAX2">IAX2</option>
													<option value="EXTERNAL">EXTERNAL</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group custom_protocol" style="display:none;">
									<label for="globals_string" class="col-sm-3 control-label"><?php $lh->translateText('global_string'); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" name="globals_string" class="form-control" value="" id="globals_string" maxlength="255" size="50">
									</div>
								</div>
								<div class="form-group custom_protocol" style="display:none;">
									<label for="dialplan_entry" class="col-sm-3 control-label"><?php $lh->translateText('dialplan_entry'); ?></label>
									<div class="col-sm-8 mb">
										<textarea name="dialplan_entry" class="form-control note-editor valid"  cols="65" rows="10" id="dialplan_entry" style="resize: none;"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label for="server_ip" class="col-sm-3 control-label"><?php $lh->translateText('server_ip'); ?>
									</label>
									<div class="col-sm-8 mb">
										<select name="server_ip" class="form-control">
											<?php
												for($i=0;$i<count($servers->server_ip);$i++){
													echo "<option value=".$servers->server_ip[$i].">".$servers->server_ip[$i]." - ".$servers->server_description[$i]."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
						<!-- /.manual -->

						<!-- IF COPY -->
							<div class="copy_div" style="display:none;">
								<div class="form-group mt">
									<label for="server_ip" class="col-sm-3 control-label"><?php $lh->translateText('server_ip'); ?></label>
									<div class="col-sm-8 mb">
										<select class="form-control" name="copy_server_ip">
											<?php
												for($i=0;$i<count($servers->server_ip);$i++){
													echo "<option value=".$servers->server_ip[$i].">".$servers->server_ip[$i]." - ".$servers->server_description[$i]."</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="carrier_name" class="col-sm-3 control-label"><?php $lh->translateText('source_carrier'); ?></label>
									<div class="col-sm-8 mb">
										<select class="form-control" name="source_carrier">
											<?php
												for($i=0;$i<count($carriers->carrier_id);$i++){
											?>
												<option value="<?php echo $carriers->carrier_id[$i];?>"><?php echo $carriers->carrier_id[$i].' - '.$carriers->carrier_name[$i].' - '.$carriers->server_ip[$i];?></option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
							</div>
						<!-- /.copy -->

						<!-- IF MANUAL & COPY -->
							<div class="manual_copy_div" style="display:none;">
								<div class="form-group">
									<label for="status" class="col-sm-3 control-label"><?php $lh->translateText('active'); ?></label>
									<div class="col-sm-8 mb">
										<select class="form-control" name="active" id="active">
										<?php
											$active = NULL;
												$active .= '<option value="Y" > '.$lh->translationFor("go_yes").' </option>';
												$active .= '<option value="N" > '.$lh->translationFor("go_no").' </option>';
											echo $active;
										?>
										</select>
									</div>
								</div>
							</div>
						<!-- /.copy_manual -->

						<!-- IF JUST GO VOIP -->
							<div class="justgo_div" style="display:none;">
								<style type="text/css">
									.welcome-header{width:100%;text-align:center;}
									.sales-email{float:right;text-align:left;font-size:12px;margin-right: 30px}
								</style>
								<fieldset>
									<div class="form-group mb">
										<div class="welcome-header">
										  <span><?php $lh->translateText('justgo_welcome'); ?></span><br class="clear"><br class="clear">
										  <!-- <span><a href="https://webrtc.goautodial.com/justgocloud/" target="_new"><img src="https://webrtc.goautodial.com/img/goautodial_logo.png"></a></span><br class="clear"><br class="clear"> -->
										  <span><?php $lh->translateText('justgo_title'); ?></span><br>
										  <br>
										  <span align="center" style="padding-left: 100px;">
	
										<p style="width: 90%; padding-left: 40px; line-height: 17px;" align="justify">	GoAutoDial Cloud Call Center is an easy to set up and easy to use, do it yourself (DIY) cloud based telephony solution for any type of organization in wherever country you conduct your sales, marketing, service and support activites. Designed for large enterprise-grade call center companies but priced to fit the budget of the Small Business Owner, GoAutoDial Cloud Call Center uses intuitive graphical user interfaces so that deployment is quick and hassle-free, among its dozens of hot features. </p><br>
										<p style="width: 90%; padding-left: 40px; line-height: 17px;" align="justify">Using secure cloud infrastructures certified by international standards, GoAutoDial Cloud Call Center is a "Use Anywhere, Anytime" web app so that you can create more customers for life – in the office, at home or at the beach. </p>
										  </span>
										  <br>
										  <span class="sales-email">  **Email <a href="mailto:sales@goautodial.com">sales@goautodial.com</a> to get 120 free minutes (US, UK and Canada calls only).</span><br>
										</div>
									</div>
								</fieldset>
								<fieldset>
									<div class="form-group">
										<div class="col-sm-12 mb">
											<center class="mb text-muted"><?php $lh->translateText('justgo_fillout'); ?></center>
										</div>
									</div>
									<div class="form-group">
										<label for="company" class="col-sm-3 control-label"><?php $lh->translateText('justgo_company'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="company" name="company" placeholder="<?php $lh->translateText('justgo_company'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="firstname" class="col-sm-3 control-label"><?php $lh->translateText('carrier_first_name'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="firstname" name="firstname" placeholder="<?php $lh->translateText('first_name'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="lastname" class="col-sm-3 control-label"><?php $lh->translateText('carrier_last_name'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="lastname" name="lastname" placeholder="<?php $lh->translateText('last_name'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="address" class="col-sm-3 control-label"><?php $lh->translateText('address'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="address" name="address" placeholder="<?php $lh->translateText('address'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="city" class="col-sm-3 control-label"><?php $lh->translateText('city'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="city" name="city" placeholder="<?php $lh->translateText('city'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="state" class="col-sm-3 control-label"><?php $lh->translateText('state'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="state" name="state" placeholder="<?php $lh->translateText('state'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="postal" class="col-sm-3 control-label"><?php $lh->translateText('postal_code'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="postal" name="postal" placeholder="<?php $lh->translateText('postal_code'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="country" class="col-sm-3 control-label"><?php $lh->translateText('country'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="country" name="country" placeholder="<?php $lh->translateText('country'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="timezone" class="col-sm-3 control-label"><?php $lh->translateText('time_zone'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="timezone" name="timezone" placeholder="<?php $lh->translateText('time_zone'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="phone" class="col-sm-3 control-label"><?php $lh->translateText('phones'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="phone" name="phone" placeholder="<?php $lh->translateText('phones'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="mobilephone" class="col-sm-3 control-label"><?php $lh->translateText('mobile_phones'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="mobilephone" name="mobilephone" placeholder="<?php $lh->translateText('mobile_phones'); ?>" required />
										</div>
									</div>
									<div class="form-group">
										<label for="email" class="col-sm-3 control-label"><?php $lh->translateText('email'); ?></label>
										<div class="col-sm-8 mb">
											<input type="text" class="form-control" id="email" name="email" placeholder="<?php $lh->translateText('email'); ?>" required />
										</div>
									</div>
								</fieldset>
								<fieldset>
									<div class="form-group">
										<div class="col-lg-12 mb">
											<label class="col-sm-6"><?php $lh->translateText('terms_and_condition'); ?></label>
										</div>
										<div class="col-lg-12 pull-right">
											<div class="boxviewnew form-control text-muted" style="height:250px;overflow-y:scroll; overflow-x:hidden; text-align: justify;">
	
											  <table cellpadding="0" cellspacing="0">
													<tbody><tr>
														<td><p style="font-size: 14px;">
															This site is owned and operated by GoAutoDial, Inc. ("we", "us", "our" or "GoAutoDial").
																					GoAutoDial, Inc.provides its services to you ("Customer", "you" or "end user")
										  subject to the following conditions
																					.<br>
																					If you visit or shop at our website or any other affiliated
																					<a href="http://reversephonelookuppages.com/" class="faqlinka" style="color: green;">reverse phone lookup</a> websites,
												  you affirmatively accept the following conditions.
												  Continued use of the site and any of
																		GoAutoDial's services constitute the affirmative agreement to these terms and conditions.                                                    <br>
	
																					GoAutoDial reserves the right to change the terms, conditions and notices under which the                                                    GoAutoDial sites and services are offered,including but not limited to the charges associated with the use of the                                                    GoAutoDialsites and services.
															</p>
														</td>
													</tr>
													<tr><td><br><p style="font-size: 14px;"><b>1. Electronic Communications</b></p></td></tr>
													<tr><td><p style="font-size: 14px;">1.1. When you visit GoAutoDial's websites or send Email to us, you are communicating with us electronically. You consent to receive communications from us electronically. We will communicate with you by Email or by posting notices on this site. You agree that all agreements, notices, disclosures and other communications that we provide to you electronically satisfy any legal requirement that such communications be in writing</p></td></tr>
													<tr><td><br><p style="font-size: 14px;"><b>Trademarks and Copyright</b></p></td></tr>
													<tr><td><p style="font-size: 14px;">2.1. All content on this site, such as text, graphics, logos, button icons, images, trademarks or copyrights are the property of their respective owners. Nothing in this site should be construed as granting any right or license to use any Trademark without the written permission of its owner.</p></td></tr>
													<tr><td><br><p style="font-size: 14px;"><b>3. Services &amp; Conditions</b></p></td></tr>
													<tr><td><p style="font-size: 14px;">3.1. GoAutoDial shall not be held liable for any delay or failure to provide service(s) at any time. In no event shall GoAutoDial, its officers, Directors, Employees, Shareholders, Affiliates, Agents or Providers who furnishes services to customer in connection with this agreement or the service be liable for any direct, incident, indirect, special, punitive, exemplary or consequential damages, including but not limited to loss of data, lost of revenue, profits or anticipated profits, or damages arising out of or in connection to the use or inability to use the service. The limitations set forth herein apply to the claimed founded in Breach of Contract, Breach of Warranty, Product Liability and any and all other liability and apply weather or not GoAutoDial was informed of the likely hood of any particular type of damage.</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.2. GoAutoDial makes no warranties of any kind, written or implied, to the service in which it provides.</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.3. GoAutoDialprovides prepaid services only. You must keep a positive balance to retain services with GoAutoDial.You must pay all negative balances immediately. Customer agrees to keep a positive balance in customer's account at all times and agrees to pay the rate in which the customer signed up for any destinations. Customer agrees to pay any and all charges that customer incurs while using GoAutoDial's service.</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.4. GoAutoDial'sVOIP and Cloud services are not intended for use as a primary telephone source for business or residential users.  GoAutoDialdoes not provide e911 service. </p></td></tr>
													<tr><td><p style="font-size: 14px;">3.5. All calls placed through GoAutoDial's VOIP network to US48 destinations are billed at 6 second increments unless otherwise stated.</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.6. Customer agrees to the exclusive jurisdiction of the courts of Pasig City in the Republic of the Philippines for any and all legal matters.</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.7. Violation of any state or federal laws or laws for any other competent jurisdiction may result in immediate account termination and/or disconnection of the offending service.</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.8. GoAutoDial reserves the right to terminate service at any time with or without notice; especially if Customer is found to be in violation ofGoAutoDial'sTerms &amp; Conditions. You agree that  GoAutoDial Due to the nature of this industry and high credit card fraud rate,</p></td></tr>
													<tr><td><p style="font-size: 14px;">3.9.Due to the nature of this industry and high credit card fraud rate,  GoAutoDial reserves the right to request the following documentation for verification purposes; A copy of the credit card used to establish the account along with valid photo identification such as a Passport, Drivers License or other Government issued identification.</p></td>
													</tr><tr><td><p style="font-size: 14px;">3.10 DID and TFN (Toll Free Numbers ) Services and Subscriptions Activation and Deactivation</p></td>
													</tr><tr><td><p style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.10.1 DID/TFN monthly service fee shall be automatically deducted or debited from the customer's account balance or credits with or without prior notice; prior to activation of service its subscriptions agreement.</p></td>
													</tr><tr><td><p style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.10.2 Auto-debit of monthly payment shall commence once DID/TFN has been activated. </p></td>
													</tr><tr><td><p style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.10.3 Failure to pay the agreed DID/TFN monthly services and monthly subscription fee (having one [1] month unpaid bill) shall be subject to DID/TFN deactivation.</p></td>
													</tr><tr><td><p style="font-size: 14px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.10.4 A maximum one 1 month grace period shall be given to the customer to settle his/her account before DID/TFN deactivation and/or deletion. </p></td></tr>
													<tr><td><br><p style="font-size: 14px;"><b>4. Technical Support</b></p></td></tr>
													<tr><td><p style="font-size: 14px;">4.1. GoAutoDial Technical Support is available Mondays to Fridays 09:00 to 24:00 24/5 EST, all support concerns should be filed at GoAutoDial's ticketing system <a href="https://www.goautodial.com/supporttickets.php" class="faqlinka" style="color: green;">https://www.goautodial.com/supporttickets.php</a>.</p></td></tr>
													<tr><td><br><p style="font-size: 14px;"><b>4.2. Monthly Technical Support</b></p></td></tr>
													<tr><td><p style="font-size: 14px;">4.2.1. GoAutoDial'smonthly support subscriptions covers the configurations and troubleshooting for the following issues: </p></td><td></td></tr>
													<tr><td><p style="font-size: 14px; margin-left: 20px;">Campaigns – outbound, inbound and blended campaign creation and configurations
													Lists/Leads – creation of lists and loading of leads.
													Statuses/Dispositions configuration
													Call Times configuration
													IVR – Basic configuration (one level only)
													Basic tutorial for Campaign and Leads management.
	
	
	
																			</p></td></tr>
													<tr><td><br><p style="font-size: 14px;">4.2.2.All advance configurations not listed above will be charged with the regular hourly support rate of $80 per hour. </p></td></tr>
													<tr><td><br><p style="font-size: 14px;">4.2.3.We provide limited support and provide samples configurations for IP Phones/Softphones. It is the end users responsibility to properly configure their workstations and devices for use with GoAutoDial'sservices. </p></td></tr>
													<tr><td><br><p style="font-size: 14px;">4.2.4. Leads Management, Campaign Management, Agent Monitoring and Reports Generation are end users responsibility.</p></td></tr>
													<tr><td><br><b><p style="font-size: 14px;">4.3. Emergency Technical Support</p></b></td></tr>
													<tr><td><p style="font-size: 14px;">4.3.1. Emergency technical support outside the regular coverage of Monday to Friday 9:00 to 24:00 EST will be charged $80 per hour.</p></td></tr>
													<tr><td><br><p style="font-size: 14px;">4.3.2. Emergency technical support for Weekend and Holidays will be charged $160 per hour.</p></td></tr>
													<tr><td><br><b><p style="font-size: 14px;">5. Refund Policy</p></b></td></tr>
													<tr><td><p style="font-size: 14px;">5.1. VoIP and Cloud Services: We offer full refunds on remaining pre-paid balance on VoIP and Cloud services upon request for all payments made within 7 days.</p></td></tr>
													<tr><td><br><p style="font-size: 14px;">5.2. Monthly Subscriptions: We do not offer refunds for monthly subscriptions such as Hosted Dialer, DID's or Toll Free numbers</p></td></tr>
													<tr><td><br><p style="font-size: 14px;">5.3. Prepaid Technical Support and Consulting Services: We offer refunds only if no technical support or consulting service and has been rendered.</p></td></tr>
													<tr><td><br><p style="font-size: 14px;">5.4. There will be no refunds for one-time/setup fees</p></td></tr>
													<tr><td><br><b><p style="font-size: 14px;">6. Site Policies, Modification &amp; Severability</p></b></td></tr>
													<tr><td><p style="font-size: 14px;">6.1. We reserve the right to make changes to our site, policies, and these Terms &amp; Conditions at any time. If any of these conditions shall be deemed invalid, void, or for any reason unenforceable, that condition shall be deemed severable and shall not affect the validity and enforceability of any remaining condition.</p></td></tr>
													<tr><td><br><p style="font-size: 14px;"><b>7. General Complaints</b></p></td></tr>
													<tr><td><p style="font-size: 14px;">7.1. Please send reports of activity in violation of these Terms &amp; Conditions to <a href="mailto:cloud@goautodial.com">cloud@goautodial.com</a>. GoAutoDial will reasonably investigate incidents involving such violations. GoAutoDial may involve and will cooperate with law enforcement officials if any criminal activity is suspected. Violations may involve criminal and civil liability</p></td></tr>
													<tr><td><br><b><p style="font-size: 14px;">8. Paypal Payments</p></b></td></tr>
													<tr><td><p style="font-size: 14px;">8.1 In case of payment via PayPal.com, customer fully understands that there will be no tangible product shipping to any address. The customer understands that they are purchasing services for which GoAutoDialprovides online Call History (CDR) for VOIP/Cloud usage and/or outbound/inbound reports for the Dialer. In case of PayPal disputes the customer agrees to abide by  GoAutoDial’sonline Call History (CDR) for VOIP/Cloud usage and/or outbound/inbound reports for delivered service totaling the PayPal.com payment. </p></td></tr>
													<tr><td><br><b><p style="font-size: 14px;">9. Limitation of Liabilities</p></b></td></tr>
													<tr><td><p style="font-size: 14px;">9.1. In no event shall GoAutoDial, Inc.be liable to any party for any direct, indirect, incidental, special, exemplary or consequential damages of any type whatsoever related to or arising from this website or any use of this website, or any site or resource linked to, referenced, or access throught this website, or for the use or downloading of, or access to, any materials, information, products, or services, including withouth limitation, any lost profits, business interruption, lost savings or loss of programs or other data, even if  GoAutoDial, Inc.is expressly advised of the possiblity of such damages. </p></td></tr>
													<tr><td><br><b><p style="font-size: 14px;">10. Call Compliance</p></b></td></tr>
													<tr><td><p style="font-size: 14px;">10.1. GoAutoDial has full USA, UK and Canada regulatory compliance. Customer fully understands that it is their responsibility to follow these regulations. Failure to do so may result in immediate account suspension and/or disconnection.</p></td></tr>
	
																	  </tbody></table>
																  </div>
										</div>
									</div>
							</fieldset>
							</div>
						<!-- ./justgo -->
						</fieldset>
					<!--end of step2 -->
					</div>
				</form>

				</div> <!-- end of modal body -->
			</div>
		</div>
	</div><!-- end of modal -->

	<!-- Modal -->
	<div id="view-calltime-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText('call_time_details'); ?></b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			<div class="message_box"></div>
			<div class="form-group">
				<label class="control-label col-lg-4"><?php $lh->translateText('moh_name'); ?></label>
				<div class="col-lg-8">
					<input type="text" class="form-control moh_name">
				</div>
			</div>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-primary btn-update-calltime" data-id=""><?php $lh->translateText("modify"); ?></button>
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

		<!-- Forms and actions -->
		<?php print $ui->standardizedThemeJS(); ?>
		<!-- JQUERY STEPS-->
  		<script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>
		<script type="text/javascript">

			$(document).ready(function() {
				$('#carriers').dataTable();
				
				/* init wizards */
				var form = $("#create_form"); // init user form wizard 
				form.validate({
					errorPlacement: function errorPlacement(error, element) { element.after(error); }
				});
				form.children("div").steps({
					headerTag: "h4",
					bodyTag: "fieldset",
					transitionEffect: "slideLeft",
					onStepChanging: function (event, currentIndex, newIndex)
					{
						
						var carrier_type = document.getElementById('carrier_type').value;

						if(carrier_type == "manual" || carrier_type == "copy"){
							$('.manual_copy_div').show();
						}else{
							$('.manual_copy_div').hide();
						}

						if(carrier_type == "manual"){
							$('.manual_div').show();
							$('#active').val("Y").attr("selected");
						}else{
							$('.manual_div').hide();
							$('#active').val("N").attr("selected");
						}

						if(carrier_type == "copy"){
							$('.copy_div').show();
							$('#active').val("N").attr("selected");
						}else{
							$('.copy_div').hide();
							$('#active').val("Y").attr("selected");
						}

						if(carrier_type == "justgo"){
							$('.justgo_div').show();
						}else{
							$('.justgo_div').hide();
						}
						
						if (currentIndex > newIndex) {
							return true;
						}
						
						// Clean up if user went backward before
						if (currentIndex < newIndex)
						{
							// To remove error styles
							$(".body:eq(" + newIndex + ") label.error", form).remove();
							$(".body:eq(" + newIndex + ") .error", form).removeClass("error");
						}
	
						form.validate().settings.ignore = ":disabled,:hidden";
						return form.valid();
					},
					onFinishing: function (){
						form.validate().settings.ignore = "disabled,:hidden";
						return form.valid();
					},
					onFinished: function (){
						$('#finish').text("<?php $lh->translateText("loading"); ?>");
						$('#finish').attr("disabled", true);
	
						// Submit form via ajax
						$.ajax({
							url: "./php/AddCarrier.php",
							type: 'POST',
							data: $("#create_form").serialize(),
							success: function(data) {
								console.log(data);
								console.log($("#create_form").serialize());
								$('#finish').text("<?php $lh->translateText("submit"); ?>");
								$('#finish').attr("disabled", false);
								
								if (data == 1) {
									swal({	title: "<?php $lh->translateText("success"); ?>",
											text: "<?php $lh->translateText("add_carriers_success"); ?>",
											type: "success" },
										function(){ window.location.href = 'settingscarriers.php'; }
									);
								} else {
									sweetAlert("<?php $lh->translateText("oops"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
								}
							}
						});
					}
				});
				
				/* on authorization change */
				$('input[type=radio][name=authentication]').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "auth_reg") {
					  $('.registration_div').show();
					  $('.sip_server').hide();
					}
					if(this.value == "auth_ip") {
					  $('.registration_div').hide();
					  $('.sip_server').show();
					}
				});

				 /* on custom dtmf select */
				$('input[type=radio][name=dtmf]').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "custom") {
						$('#input_custom_dtmf').show();
					}else{
						$('#input_custom_dtmf').hide();
					}
				});
				
				 /* on protocol select */
				$('#protocol').on('change', function() {
					if(this.value == "CUSTOM") {
						$('.custom_protocol').show();
						$('.not_custom_protocol').hide();
						$('.registration_div').hide();
					}else{
						$('.custom_protocol').hide();
						$('.not_custom_protocol').show();
						$('.registration_div').show();
					}
				});
				
				/**
				  * Edit user details
				 */
				$(document).on('click','.edit-carrier',function() {
					var url = 'editsettingscarrier.php';
					var cid = $(this).attr('data-id');
					var role = $(this).attr('data-role');
					//alert(userid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="cid" value="'+cid+'" /><input type="hidden" name="role" value="'+role+'"></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				$(document).on('click','.delete-carrier',function() {
					var id = $(this).attr('data-id');
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
                    swal({
                        title: "<?php $lh->translateText("are_you_sure"); ?>",
                        text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php $lh->translateText("confirm_delete_carrier"); ?>",
                        cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",
                        closeOnConfirm: false,
                        closeOnCancel: false
                        },
                        function(isConfirm){
                            if (isConfirm) {
                            	$.ajax({
									url: "./php/DeleteCarrier.php",
									type: 'POST',
									data: {
									      carrier_id : id,
										  log_user: log_user,
										  log_group: log_group
									},
									dataType: 'json',
									success: function(data) {
										if(data == 1){
											swal("<?php $lh->translateText("deleted"); ?>", "<?php $lh->translateText("carrier_delete_success"); ?>", "success");
                                            window.setTimeout(function(){location.reload();},1000);
										}else{
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
										}
									}
								});

							 } else {
                                swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
                            }
                        }
                    );

				});
				
				// disable special characters on Carrier ID
				$('#carrier_id').bind('keypress', function (event) {
					var regex = new RegExp("^[a-zA-Z0-9]+$");
					var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					if (!regex.test(key)) {
					   event.preventDefault();
					   return false;
					}
				});
				
				//input mask
					$("[data-mask]").inputmask();
			});

		</script>

		<?php print $ui->creamyFooter();?>
    </body>
</html>
