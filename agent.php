<?php
/**
 * @file 		agent.php
 * @brief 		Agent application
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Chris Lomuntad 
 * @author		Demian Lizandro A. Biscocho
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

require_once('./php/APIHandler.php');
require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
require_once('./php/LanguageHandler.php');
require_once('./php/DbHandler.php');

define('GO_BASE_DIRECTORY', str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__)));

// initialize structures
require_once('./php/Session.php');
try {
	$api = \creamy\APIHandler::getInstance();
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$db = new \creamy\DbHandler();
	$user = \creamy\CreamyUser::currentUser();
} catch (\Exception $e) {
	header("location: ./logout.php");
	die();
}

if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_AGENT){
    header("location: index.php");
}

$lead_id = $_GET['lead_id'];
$output = $api->API_getLeadsInfo($lead_id);
$list_id_ct = count($output->list_id);

if ($list_id_ct > 0) {
	for($i=0;$i < $list_id_ct;$i++){
		$first_name 	= $output->first_name[$i];
		$middle_initial = $output->middle_initial[$i];
		$last_name 		= $output->last_name[$i];
		
		$email 			= $output->email[$i];
		$phone_number 	= $output->phone_number[$i];
		$alt_phone 		= $output->alt_phone[$i];
		$address1 		= $output->address1[$i];
		$address2 		= $output->address2[$i];
		$address3 		= $output->address3[$i];
		$city 			= $output->city[$i];
		$state 			= $output->state[$i];
		$country 		= $output->country[$i];
		$gender 		= $output->gender[$i];
		$date_of_birth 	= $output->date_of_birth[$i];
		$comments 		= $output->comments[$i];
		$title 			= $output->title[$i];
		$call_count 	= $output->call_count[$i];
		$last_local_call_time = $output->last_local_call_time[$i];
	}
}
$fullname = $title.' '.$first_name.' '.$middle_initial.' '.$last_name;
$date_of_birth = date('Y-m-d', strtotime($date_of_birth));
//var_dump($output);
 $output_script = $ui->getAgentScript($lead_id, $fullname, $first_name, $last_name, $middle_initial, $email, 
 									  $phone_number, $alt_phone, $address1, $address2, $address3, $city, $province, $state, $postal_code, $country);


if (isset($_GET["folder"])) {
	$folder = $_GET["folder"];
} else $folder = MESSAGES_GET_INBOX_MESSAGES;
if ($folder < 0 || $folder > MESSAGES_MAX_FOLDER) { $folder = MESSAGES_GET_INBOX_MESSAGES; }

if (isset($_GET["message"])) {
	$message = $_GET["message"];
} else $message = NULL;

$user_info = $api->API_getUserInfo($_SESSION['user'], "userInfo");
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title><?=CRM_GOAGENT_TITLE?> - <?=$lh->translateText('GOautodial')." ".CRM_GO_VERSION?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<!-- SnackbarJS -->
        <link href="css/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
        <link href="css/snackbar/material.css" rel="stylesheet" type="text/css" />
		<!-- bootstrap wysihtml5 - text editor -->
		<link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
		<!-- multiple emails plugin -->
		<link href="css/multiple-emails/multiple-emails.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <!-- Customized Style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>      

		<!-- javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<!-- Bootstrap WYSIHTML5 -->
		<script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
		<!-- Multi file upload -->
		<script src="js/plugins/multifile/jQuery.MultiFile.min.js" type="text/javascript"></script>
		<!-- Multiple emails -->
		<script src="js/plugins/multiple-emails/multiple-emails.js" type="text/javascript"></script>
		<!-- Print page -->
		<script src="js/plugins/printThis/printThis.js" type="text/javascript"></script>
		<!-- FONT AWESOME-->
		<link rel="stylesheet" src="js/dashboard/fontawesome/css/font-awesome.min.css">
		<!-- SIMPLE LINE ICONS-->
		<link rel="stylesheet" src="js/dashboard/simple-line-icons/css/simple-line-icons.css">
		<!-- ANIMATE.CSS-->
		<link rel="stylesheet" src="js/dashboard/animate.css/animate.min.css">
		<!-- WHIRL (spinners)-->
		<link rel="stylesheet" src="js/dashboard/whirl/dist/whirl.css">
		<!-- =============== PAGE VENDOR STYLES ===============-->
		<!-- WEATHER ICONS-->
		<link rel="stylesheet" src="js/dashboard/weather-icons/css/weather-icons.min.css">
		<!-- =============== BOOTSTRAP STYLES ===============-->
		<link rel="stylesheet" src="js/dashboard/css/bootstrap.css" id="bscss">
		<!-- =============== APP STYLES ===============-->
		<link rel="stylesheet" src="js/dashboard/css/app.css" id="maincss">
		<link rel="stylesheet" src="js/dashboard/sweetalert/dist/sweetalert.css">
		<!-- Datetime picker --> 
        <link rel="stylesheet" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
		<!-- iCheck for checkboxes and radio inputs -->
		<link href="css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
		<!-- iCheck -->
		<script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
		<!-- SLIMSCROLL-->
		<script src="js/dashboard/slimScroll/jquery.slimscroll.min.js"></script>
		<!-- SWEETALERT-->
		<script src="js/dashboard/sweetalert/dist/sweetalert.min.js"></script>
		<!-- FastClick -->
		<!--<script src="js/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>-->
		<!-- MD5 HASH-->
		<script src="js/jquery.md5.js" type="text/javascript"></script>
        <!-- Date Picker -->
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		
        <!-- X-Editable -->
        <link rel="stylesheet" src="js/dashboard/x-editable/dist/css/bootstrap-editable.css">
        <script type="text/javascript" src="js/dashboard/x-editable/dist/js/bootstrap-editable.min.js"></script>

  		<!-- Theme style -->
  		<link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">
		
		<!-- flag sprites -->
		<link rel="stylesheet" href="css/flags/flags.min.css">

        <script type="text/javascript">
			history.pushState('', document.title, window.location.pathname);
			
			$(window).load(function() {
				$(".preloader").fadeOut("slow", function() {
					if (use_webrtc && (!!$.prototype.snackbar) && phone.isConnected()) {
						$.snackbar({content: "<i class='fa fa-exclamation-circle fa-lg text-warning' aria-hidden='true'></i>&nbsp; Please wait while we register your phone extension to the dialer...", timeout: 3000, htmlAllowed: true});
					}
				});
				
				$('#callback-list')
					.removeClass( 'display' )
					.addClass('table table-striped table-bordered');
				
				if (typeof country_codes !== 'undefined') {
					$("#country_code").append('<option value="1">United States of America</option>');
					$("#country_code").append('<option value="1">Canada</option>');
					$("#country_code").append('<option value="63">Philippines</option>');
					$("#country_code").append('<option value="61">Australia</option>');
					$("#country_code").append('<option value="44">United Kingdom of Great Britain and Northern Ireland</option>');
					$.each(country_codes, function(key, value) {
						if (! /^(USA_1|CAN_1|PHL_63|AUS_61|GBR_44)$/g.test(key)) {
							$("#country_code").append('<option value="'+value.code+'">'+value.name+'</option>');
						}
					});
				}
			});
			
			$(function() {
				$("a[id='first_name'], a[id='middle_initial'], a[id='last_name']").on('hidden', function() {
					var thisID = $(this).attr('id');
					$('#'+thisID+'_label').addClass('hidden');
				});
				
				$("a[id='first_name'], a[id='middle_initial'], a[id='last_name']").on('shown', function() {
					var thisID = $(this).attr('id');
					var oldValue = $(this).editable('getValue', true);
					//console.log(oldValue);
					if ($(this).html() !== '&nbsp;') {
						//$('div.editable-input input').val($(this).text());
						//$(this).editable('setValue', oldValue, true);
					} else {
						//$('div.editable-input input').val('');
						//$(this).editable('setValue', '', true);
					}
					$('#'+thisID+'_label').removeClass('hidden');
				});
				
				$("a[id='first_name']").editable({
					type: 'text',
					title: '<?=$lh->translationFor('enter_first_name')?>',
					placeholder: '<?=$lh->translationFor('enter_first_name')?>',
					emptytext: '&nbsp;',
					unsavedclass: null,
					inputclass: 'text-color-black',
					onblur: 'submit'
				});
				$("a[id='middle_initial']").editable({
					type: 'text',
					title: '<?=$lh->translationFor('enter_middle_initial')?>',
					placeholder: '<?=$lh->translationFor('enter_middle_initial')?>',
					emptytext: '&nbsp;',
					unsavedclass: null,
					inputclass: 'text-color-black',
					onblur: 'submit'
				});
				$("a[id='last_name']").editable({
					type: 'text',
					value: '',
					title: '<?=$lh->translationFor('enter_last_name')?>',
					placeholder: '<?=$lh->translationFor('enter_last_name')?>',
					emptytext: '&nbsp;',
					unsavedclass: null,
					inputclass: 'text-color-black',
					onblur: 'submit'
				});
				
				//$("#callback-list").DataTable({"bDestroy": true, "aoColumnDefs": [{ "bSortable": false, "aTargets": [ 5 ] }, { "bSearchable": false, "aTargets": [ 2, 5 ] }] });
			});
			
			//turn to inline mode
			$.fn.editable.defaults.mode = 'inline';    //buttons
			$.fn.editableform.buttons =
				'<button type="submit" class="btn btn-primary btn-sm editable-submit" style="padding: 8px 10px;">'+
					'<i class="fa fa-check"></i>'+
				'</button>'+
				'<button type="button" class="btn btn-default btn-sm editable-cancel" style="padding: 8px 10px;">'+
					'<i class="fa fa-remove"></i>'+
				'</button>';
		</script>
		<style>
			.nav-tabs > li > a{
				font-weight: normal;
				border:0px;
				border-radius: 3px 3px 0px 0px;
			}
			.custom-tabpanel{
				padding-top: 20px;
				margin-right: 10px;
			}
			h3{
				font-weight: normal;
			}
			.custom-row{
				padding: 0px 50px;
				padding-bottom: 50px;
			}
			.panel{
				margin-bottom:0;
			}
			.required_div{
				background: rgba(158,158,158,0.30);
			}
			.textarea{
				border: none;
				border-bottom: .5px solid #dde6e9;
				width: 100%;
				-webkit-box-sizing: border-box;
				   -moz-box-sizing: border-box;
						box-sizing: border-box;
				padding-left: 0px;
			}
			
			.form-control[disabled], fieldset[disabled] .form-control{
				cursor: text;
				background-color: white;
			}
			.edit-profile-button{
				font-size:14px; 
				font-weight:normal;
			}
			.hide_div{
				display: none;
			}
			.btn.btn-raised {
				box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
			}
			button[id^='show-callbacks-']:hover, button[id^='show-callbacks-']:active {
				text-decoration: none;
			}
			#popup-hotkeys {
				position: absolute;
				top: 160px;
				left: 40px;
				display: none;
				box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
				min-width: 480px;
			}
			#popup-hotkeys .panel-heading {
				background-color: #2a2a2a;
				color: #fff;
			}
			#popup-hotkeys .panel-body dl {
				margin-bottom: 0px;
			}
			.control-label {
				padding-top: 0px;
			}
			.popover-title {
				font-size: 16px;
				font-weight: bold;
				color: #555;
				background-color: #f0f0f0;
			}
			.dataTables_empty {
				text-align: center;
			}
			.table > thead > tr > th {
				padding: 8px;
			}
			.modal-body {
				min-height: inherit;
				overflow-x: inherit;
				overflow-y: inherit;
				padding-top: 15px;
			}
			.form-group {
			  position: relative;
			  padding: 18px 0 24px 0;
			}
			.form-control {
			  position: relative;
			  z-index: 5;
			  width: 100%;
			  height: 34px;
			  padding: 2px;
			  color: inherit;
			  border: 0;
			  border-bottom: 1px solid #dde6e9;
			  border-radius: 0;
			  box-shadow: none;
			}
			.form-control:focus,
			.form-control.focus {
				padding-bottom: 1px;
				border-color: #3f51b5;
				border-bottom-width: 2px;
			}
			.form-control:focus ~ label,
			.form-control.focus ~ label {
				top: 0!important;
				font-size: .85em!important;
				color: #3f51b5;
				opacity: 1;
			}
			.form-control ~ label {
				position: absolute;
				top: 0;
				left: 0;
				z-index: 0;
				display: inline-block;
				font-size: .85em;
				opacity: .5;
				-webkit-transition: all 0.2s ease;
				-o-transition: all 0.2s ease;
				transition: all 0.2s ease;
			}
			.customform-label {
				position: absolute;
				top: 0;
				left: 0;
				z-index: 0;
				display: inline-block;
				font-size: .85em;
				opacity: .5;
				transition: all 0.2s ease;
				font-weight: 700;
			}
			.text-color-black {
				color: black;
			}
			body::-webkit-scrollbar, .tab-content::-webkit-scrollbar { 
				display: none;
			}
			.mail-preloader span.dots div, .cust-preloader span.dots div {
				background-color: #2196F3;
			}
			
			#contact_info label, #comments label, #custom_form label {
				color: #000;
				opacity: 0.75;
			}
			.scrollable-menu {
				width: 200px;
				height: auto;
				max-height: 200px;
				overflow-x: hidden;
				overflow-y: auto;
			}
		</style>
    </head>
    <?php print $ui->creamyAgentBody(); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyAgentHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-heading">
					<!-- Page title -->
                    <span><?php $lh->translateText("contact_information"); ?></span>
                    <ol class="breadcrumb hidden-xs pull-right">
						<li class="active"><i class="fa fa-home"></i> <?php $lh->translateText('home'); ?></li>
					</ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<!-- standard custom edition form -->
					<div id="cust_info" class="container-custom ng-scope">
						<div class="card">
							
								<div class="card-heading bg-inverse">
									<div class="row">
										<div id="cust_avatar" class="col-lg-1 col-md-1 col-sm-2 text-center hidden-xs" style="height: 64px;">
											<avatar username="Dialed Client" src="<?php echo CRM_DEFAULTS_USER_AVATAR;?>" :size="64"></avatar>
										</div>
										<div class="col-lg-11 col-md-11 col-sm-10">
						                <h4 id="cust_full_name" class="hidden">
											<span id="first_name_label" class="hidden"><?=$lh->translationFor('first_name')?>: </span><a href="#" id="first_name"></a> <span id="middle_initial_label" class="hidden"><?=$lh->translationFor('middle_initial')?>: </span><a href="#" id="middle_initial"></a> <span id="last_name_label" class="hidden"><?=$lh->translationFor('last_name')?>: </span><a href="#" id="last_name"></a>
										</h4>
						                <p class="ng-binding animated fadeInUpShort"><span id="cust_number"></span></p>
						            </div>
									</div>
								</div>
							<!-- /.card heading -->
								
							<!-- Card body -->
						        <div class="card-body custom-tabpanel">
				                	<div role="tabpanel" class="panel panel-transparent">
									  <ul id="agent_tablist" role="tablist" class="nav nav-tabs nav-justified">
									  <!-- Nav task panel tabs-->
										 <li role="presentation" class="active">
											<a href="#contact_info" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-user hidden"></span>
												<?=$lh->translationFor('contact_information')?></a>
										 </li>
										 <li role="presentation">
											<a href="#comments_tab" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-comments-o hidden"></span>
											    <?=$lh->translationFor('comments')?></a>
										 </li>
										 <li role="presentation">
											<a href="#scripts" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-file-text-o hidden"></span>
												<?=$lh->translationFor('script')?></a>
										 </li>
									  </ul>
									</div>
									<!-- Tab panes-->
									<div id="agent_tabs" class="tab-content bg-white">
										<div id="contact_info" role="tabpanel" class="tab-pane active">

											<fieldset style="padding-bottom: 0px; margin-bottom: 0px;">
												<h4>
													<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile"><?=$lh->translationFor('edit_information')?></a>
												</h4>
												<br/>
												<form role="form" id="name_form" class="formMain form-inline" >
												
												<!--LEAD ID-->
												<input type="hidden" value="<?php echo $lead_id;?>" name="lead_id">
												<!--LIST ID-->
												<input type="hidden" value="<?php echo $list_id;?>" name="list_id">
												<!--ENTRY LIST ID-->
												<input type="hidden" value="<?php echo $entry_list_id;?>" name="entry_list_id">
												<!--VENDOR ID-->
												<input type="hidden" value="<?php echo $vendor_lead_code;?>" name="vendor_lead_code">
												<!--GMT OFFSET-->
												<input type="hidden" value="<?php echo $gmt_offset_now;?>" name="gmt_offset_now">
												<!--SECURITY PHRASE-->
												<input type="hidden" value="<?php echo $security_phrase;?>" name="security_phrase">
												<!--RANK-->
												<input type="hidden" value="<?php echo $rank;?>" name="rank">
												<!--CALLED COUNT-->
												<input type="hidden" value="<?php echo $call_count;?>" name="called_count">
												<!--UNIQUEID-->
												<input type="hidden" value="<?php echo $uniqueid;?>" name="uniqueid">
												<!--SECONDS-->
												<input type="hidden" value="" name="seconds">
												<!--CUSTOM FORM LOADED-->
												<input type="hidden" value="0" name="FORM_LOADED">
												
												<!--<div class="row">
													<div class="col-sm-4">
														<div class="mda-form-group label-floating">
															<input id="first_name" name="first_name" type="text" maxlength="30"  value="<?php echo $first_name;?>"
																class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled required>
															<label for="first_name">First Name</label>
														</div>
													</div>
													<div class="col-sm-4">
														<div class="mda-form-group label-floating">
															<input id="middle_initial" name="middle_initial" type="text" maxlength="1" value="<?php echo $middle_initial;?>"
																class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
															<label for="middle_initial">Middle Name</label>
														</div>
													</div>
													<div class="col-sm-4">
														<div class="mda-form-group label-floating">
															<input id="last_name" name="last_name" type="text" maxlength="30" value="<?php echo $last_name;?>"
																class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled required>
															<label for="last_name">Last Name</label>
														</div>
													</div>
												</div>-->
												</form>
												
												<form id="contact_details_form" class="formMain">
													<!-- phone number & alternative phone number -->
													<div class="row">
														<div class="col-sm-6">
															<div class="mda-form-group label-floating">
																<span id="phone_numberDISP" class="hidden"></span>
																<input id="phone_code" name="phone_code" type="hidden" value="<?php echo $phone_code;?>">
																<input id="phone_number" name="phone_number" type="number" min="0" maxlength="18" width="auto" value="<?php echo $phone_number;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled required>
																<label for="phone_number"><?=$lh->translationFor('phone_number')?></label>
																<!--
																<span class="mda-input-group-addon">
																	<em class="fa fa-phone fa-lg"></em>
																</span>-->
															</div>
														</div>
														<div class="col-sm-6">
															<div class="mda-form-group label-floating">
																<input id="alt_phone" name="alt_phone" type="number" min="0" maxlength="12" width="100" value="<?php echo $alt_phone;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="alt_phone"><?=$lh->translationFor('alternative_phone_number')?></label>
															</div>
														</div>
													</div>
													<!-- /.phonenumber & alt phonenumber -->
													
													<div class="mda-form-group label-floating">
														<input id="address1" name="address1" type="text" maxlength="100" width="auto" value="<?php echo $address1;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
														<label for="address1"><?=$lh->translationFor('address')?></label> 
														<!--<span class="mda-input-group-addon">
															<em class="fa fa-home fa-lg"></em>
														</span>-->
													</div>
													
													<div class="mda-form-group label-floating">
														<input id="address2" name="address2" type="text" maxlength="100" value="<?php echo $address2;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
														<label for="address2"><?=$lh->translationFor('address2')?></label>
													</div>
													
													<div class="row">
														<div class="col-sm-4">
															<div class="mda-form-group label-floating">
																<input id="city" name="city" type="text" maxlength="50" value="<?php echo $city;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="city"><?=$lh->translationFor('city')?></label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="mda-form-group label-floating">
																<input id="state" name="state" type="text" maxlength="2" value="<?php echo $state;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="state"><?=$lh->translationFor('state')?></label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="mda-form-group label-floating">
																<input id="postal_code" name="postal_code" type="text" maxlength="10" value="<?php echo $postal_code;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="postal_code"><?=$lh->translationFor('postal_code')?></label>
															</div>
														</div>
													</div><!-- /.city,state,postalcode -->
												
													<div class="mda-form-group label-floating">
														<select id="country_code" name="country_code" type="text" maxlength="3"
															class="mda-form-control select2 ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select input-disabled" title="<?=$lh->translationFor('select_country_code')?>" disabled>
															<option value="">- - - <?=$lh->translationFor('select_country_code')?> - - -</option>
														</select>
														<label for="country"><?=$lh->translationFor('country_code')?></label>
													</div>
													<div class="mda-form-group label-floating"><!-- add "mda-input-group" if with image -->
														<input id="email" name="email" type="text" width="auto" value="<?php echo $email;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
														<label for="email"><?=$lh->translationFor('email_add')?></label>
														<!--<span class="mda-input-group-addon">
															<em class="fa fa-at fa-lg"></em>
														</span>-->
													</div>
												</form> 
												<form role="form" id="gender_form" class="formMain form-inline" >
													<div class="row">
														<div class="col-sm-3">
															<div class="mda-form-group label-floating">
																<input id="title" name="title" type="text" maxlength="4" value="<?php echo $title;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="title"><?=$lh->translationFor('title')?></label>
															</div>
														</div>
														<div class="col-sm-3">
															<div class="mda-form-group label-floating">
																<select id="gender" name="gender" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select input-disabled" disabled>
																	<option disabled value=""<?php if ($gender != 'M' && $gender != 'F') { echo " selected"; }?>></option>
																	<option value="M"<?php if ($gender == 'M') { echo " selected"; }?>><?=$lh->translationFor('male')?></option>
																	<option value="F"<?php if ($gender == 'F') { echo " selected"; }?>><?=$lh->translationFor('female')?></option>
																</select>
																<label for="gender"><?=$lh->translationFor('gender')?></label>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="mda-form-group label-floating">
																<input type="date" id="date_of_birth" value="" name="date_of_birth" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="date_of_birth"><?=$lh->translationFor('date_of_birth')?></label>
															</div>
														</div>
														<div id="call_notes_content" class="col-sm-12">
															<div class="form-group" style="float: left; width:100%;">
																<textarea rows="5" id="call_notes" name="call_notes" class="form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea note-editor note-editor-margin" style="resize:none; width: 100%;"></textarea>
																<label for="call_notes"><?=$lh->translationFor('call_notes')?></label>
															</div>
														</div>
													</div><!-- /.gender & title -->                   
												</form>
											
							                <!-- NOTIFICATIONS -->
											<!--<div id="notifications_list">
												<div class="output-message-success" style="display:none;">
													<div class="alert alert-success alert-dismissible" role="alert">
													  <strong>Success!</strong> Successfuly updated contact.
													</div>
												</div>
												<div class="output-message-error" style="display:none;">
													<div class="alert alert-danger alert-dismissible" role="alert">
													  <strong>Error!</strong> Something went wrong please see input data on form or if agent already exists.
													</div>
												</div>
												<div class="output-message-incomplete" style="display:none;">
													<div class="alert alert-danger alert-dismissible" role="alert">
													  Please fill-up all the fields correctly and do not leave any highlighted fields blank.
													</div>
												</div>
											</div>-->

							                <div class="hide_div">
							                	<button type="submit" name="submit" id="submit_edit_form" class="btn btn-primary btn-block btn-flat"><?=$lh->translationFor('submit')?></button>
							                </div>
							               </fieldset>
										</div><!--End of Profile-->
										
										<div id="comments_tab" role="tabpanel" class="tab-pane">
											<div class="row">
												<div class="col-sm-12">
													<h4><!--Comments-->
														<!--<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile">Edit Information</a>-->
													</h4>
												
													<form role="form" id="comment_form" class="formMain form-inline" >
														<div class="mda-form-group hidden">
															<p style="padding-right:0px;padding-top: 20px;"><?=$lh->translationFor('comments')?>:</p> 
															<button id="ViewCommentButton" onClick="ViewComments('ON');" value="-History-" class="hidden"></button>
														</div>
														<div class="form-group" style="float: left; width:100%;">
															<textarea rows="10" id="comments" name="comments" maxlength="255" class="form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea input-disabled note-editor note-editor-margin" style="resize:none; width: 100%;" disabled><?=$comments?></textarea>
															<label for="comments"><?=$lh->translationFor('comments')?></label>
														</div>
													</form>
												</div>
											</div>
										</div>
										
										<!-- Scripts -->
										<div id="scripts" role="tabpanel" class="tab-pane">
											<div class="row">
												<div class="col-sm-12">
													<fieldset style="padding-bottom: 5px; margin-bottom: 5px;">
														<h4>
															<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="reload-script" style="padding: 5px;"><?=$lh->translationFor('reload_script')?></a>
														</h4>
														<div id="ScriptContents" style="min-height: 200px; border: dashed 1px #c0c0c0; padding: 20px 5px 5px;">
															<?php echo $output_script;?>
														</div>
													</fieldset><!-- /.fieldset -->
												</div><!-- /.col-sm-12 -->
											</div><!-- /.row -->
										</div>
										<!-- End of Scripts -->
									</div>
								</div>
								
						        <div id="custom_fields_content" class="card-body" style="border: 1px solid rgb(221, 230, 233); margin: 0 32px 0 22px; display: none;">
									<h4 style="font-weight: 600;">
										<?=$lh->translationFor('custom_forms')?>
									</h4>
									<br>
									<form role="form" id="custom_form" class="formMain">
										<div id="custom_fields">
											
										</div>
									</form>
								</div>
								<br id="custom_br" style="display: none;">

					<!-- SCRIPT MODAL -->
							<div class="modal fade" id="script" name="script" tabindex="-1" role="dialog" aria-hidden="true">
						        <div class="modal-dialog">
						            <div class="modal-content">
									
						                <div class="modal-header">
						                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translationFor("script"); ?></b></h4>
						                </div>

						                    <div class="modal-body">
						                        
											</div>
										
						            </div><!-- /.modal-content -->
						        </div><!-- /.modal-dialog -->
						    </div><!-- /.modal -->

						</div>
					</div>
					
					<div id="loaded-contents" class="container-custom ng-scope" style="display: none;">
						<div id="contents-messages" class="row" style="display: none;">
							<!-- left side folder list column -->
							<div class="col-md-3">
								<a href="composemail.php" class="btn btn-primary btn-block margin-bottom"><?php $lh->translateText("new_message"); ?></a>
								<div class="box box-solid">
									<div class="box-header with-border">
										<h3 class="box-title"><?php print $lh->translationFor("folders"); ?></h3>
									</div>
									<div id="folders-list" class="box-body no-padding">
										<?php print $ui->getMessageFoldersAsList($folder); ?>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
							</div><!-- /.col -->
							
							<!-- main content right side column -->
							<div id="mail-messages" class="col-md-9">
								<div class="box box-default">
									<div class="box-header with-border">
										<h3 class="box-title"><?php $lh->translateText("messages"); ?></h3>
									</div><!-- /.box-header -->
									<div class="box-body no-padding">
										<div class="mailbox-controls">
											<?php print $ui->getMailboxButtons($folder); ?>
										</div>
										<div class="table-responsive mailbox-messages">
											<?php print $ui->getMessagesFromFolderAsTable($user->getUserId(), $folder); ?>
										</div><!-- /.mail-box-messages -->
										
										<div class="mail-preloader" style="margin: 30px 0 10px; text-align: center; display: none;">
											<span class="dots">
												<div class="circ1"></div><div class="circ2"></div><div class="circ3"></div><div class="circ4"></div>
											</span>
										</div>
									</div><!-- /.box-body -->
									<div class="box-footer no-padding">
										<div class="mailbox-controls">
											<?php print $ui->getMailboxButtons($folder); ?>
										</div>
									</div>
								</div><!-- /. box -->
							</div><!-- /.col -->
							
							<div id="mail-composemail" class="col-md-9" style="display: none;">
								<div class="box box-default">
									<form method="POST" id="send-message-form" enctype="multipart/form-data">
										<div class="box-header with-border">
											<h3 class="box-title"><?php $lh->translateText("compose_new_message"); ?></h3>
										</div><!-- /.box-header -->
										<div class="box-body">
											<input type="hidden" id="fromuserid" name="fromuserid" value="<?php print $user->getUserId(); ?>">
											<div class="form-group">
												<?php print $ui->generateSendToUserSelect($user->getUserId(), false, null, $reply_user); ?>
												<label for="touserid">Recipients</label>
											</div>
											<div class="form-group hidden">
												<input id="external_recipients" name="external_recipients" class="form-control" placeholder="<?php $lh->translateText("external_message_recipients"); ?>"/>
												<label for="external_recipients">External Recipients</label>
											</div>
											<div class="form-group">
												<input id="subject" name="subject" class="form-control required" value="<?php print $reply_subject; ?>"/>
												<label for="subject">Subject</label>
											</div>
											<div class="form-group">
												<textarea id="compose-textarea" name="message" class="form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea required" style="height: 200px" placeholder="<?php $lh->translateText("write_your_message_here"); ?>"></textarea>
												<!--<label for="compose-textarea">Message</label>-->
											</div>
											<div class="form-group" style="padding: 0px;">
												<div class="btn btn-default btn-file">
													<i class="fa fa-paperclip"></i> <?php $lh->translateText("attachment"); ?>
													<input type="file" class="attachment" name="attachment[]"/>
												</div>
												<p class="help-block"><?php print $lh->translationFor("max")." ".CRM_MAX_ATTACHMENT_FILESIZE; ?>MB</p>
											</div>
										</div><!-- /.box-body -->
										<div class="box-footer" id="attachment-list">
											<label><?php $lh->translateText("attachments"); ?>: </label>
										</div>
										<div class="box-footer" id="compose-mail-results">
										</div>
										<div class="box-footer">
											<div class="pull-right">
												<button class="btn btn-primary" id="compose-mail-submit"><i class="fa fa-envelope-o"></i> <?php $lh->translateText("send"); ?></button>
											</div>
											<button class="btn btn-default" id="compose-mail-discard"><i class="fa fa-times"></i> <?php $lh->translateText("discard"); ?></button>
											<!-- Module hook footer -->
											<?php print $ui->getComposeMessageFooter(); ?>
										</div><!-- /.box-footer -->
									</form> <!-- /.form -->
								</div><!-- /. box -->
							</div><!-- /.col -->
							
							<div id="mail-readmail" class="col-md-9" style="display: none;">
								<div class="box box-default" id="message-full-box">
									<div class="box-header with-border non-printable">
										<h3 class="box-title"><?php print $lh->translationFor("read_message"); ?></h3>
									</div><!-- /.box-header -->
									<div class="box-body no-padding">
										<div class="mailbox-read-info">
											<h3 id="read-message-subject"></h3>
											<h5><?php print $lh->translationFor("from"); ?> <span id="read-message-from"></span>
											<span id="read-message-from-id" class="hidden"></span>
											<span id="read-message-from-name" class="hidden"></span>
											<span id="read-message-date" class="mailbox-read-time pull-right"></span></h5>
										</div><!-- /.mailbox-read-info -->
										<div class="mailbox-controls with-border text-center non-printable">
											<div class="btn-group">
												<button class="btn btn-default btn-sm mail-delete" style="font-size: 12px;" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
												<button class="btn btn-default btn-sm mail-reply hidden" data-toggle="tooltip" title="Reply"><i class="fa fa-reply"></i></button>
												<button class="btn btn-default btn-sm mail-forward hidden" data-toggle="tooltip" title="Forward"><i class="fa fa-share"></i></button>
											</div><!-- /.btn-group -->
											<button class="btn btn-default btn-sm mail-print" data-toggle="tooltip" title="Print"><i class="fa fa-print"></i></button>
										</div><!-- /.mailbox-controls -->
										<div class="mailbox-read-message" id="mailbox-message-text">
											&nbsp;
										</div><!-- /.mailbox-read-message -->
										<div class="mail-preloader non-printable" style="margin: 30px 0 10px; text-align: center; display: none;">
											<span class="dots">
												<div class="circ1"></div><div class="circ2"></div><div class="circ3"></div><div class="circ4"></div>
											</span>
										</div>
									</div><!-- /.box-body -->
									<!-- Attachments (if any) -->
									<div id="read-message-attachment"></div>
									<div class="box-footer">
										<div class="pull-right">
											<button class="btn btn-default mail-reply hidden"><i class="fa fa-reply"></i> <?=$lh->translationFor('reply')?></button>
											<button class="btn btn-default mail-forward hidden"><i class="fa fa-share"></i> <?=$lh->translationFor('forward')?></button>
										</div>
										<button class="btn btn-default mail-delete"><i class="fa fa-trash-o"></i> <?=$lh->translationFor('delete')?></button>
										<button class="btn btn-default mail-print"><i class="fa fa-print"></i> <?=$lh->translationFor('print')?></button>
									</div><!-- /.box-footer -->
								</div><!-- /. box -->
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						
						<div id="contents-callbacks" class="row" style="display: none;">
							<div class="card col-md-12" style="padding: 15px;">
								<table id="callback-list" class="display" style="border: 1px solid #f4f4f4">
									<thead>
										<tr>
											<th>
												<?=$lh->translationFor('customer_name')?>
											</th>
											<th>
												<?=$lh->translationFor('phone_number')?>
											</th>
											<th>
												<?=$lh->translationFor('last_call_time')?>
											</th>
											<th>
												<?=$lh->translationFor('callback_time')?>
											</th>
											<th>
												<?=$lh->translationFor('campaign')?>
											</th>
											<th>
												<?=$lh->translationFor('comments')?>
											</th>
											<th>
												<?=$lh->translationFor('action')?>
											</th>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
						</div><!-- /.row -->
						
						<!-- Profile -->
						<div class="unwrap" style="display: none;">
							<div style="background-image: url(img/profile-bg.jpg)" class="bg-cover">
							   <div class="p-xl text-center text-white">
									<span style="display:table; margin:0 auto;"><?=$ui->getVueAvatar($_SESSION['user'], $user->getUserAvatar(), 128)?></span>
									<h3 class="m0"><?=$user->getUserName()?></h3>
									<p><?=$_SESSION['user']?></p>
									<p>Empowering the next generation contact centers.</p>
							   </div>
							</div>
							<div class="text-center bg-gray-dark p-lg mb-xl">
							   <div class="row row-table" style="height: 7%">
								  <div class="col-xs-4 br">
									 <h3 class="m0"><?php echo $totalcallstoday; ?></h3>
									 <p class="m0">
										<span class="hidden-xs"><?=$lh->translationFor('calls_today')?></span>
										<!-- <span>Views</span> -->
									 </p>
								  </div>                    
								  <div class="col-xs-4 br">
									 <h3 class="m0"><?php echo $totalsalestoday; ?></h3>
									 <p class="m0"><?=$lh->translationFor('sales_today')?></p>
								  </div>
								  <div class="col-xs-4">
									 <h3 class="m0">100</h3>
									 <p class="m0"><?=$lh->translationFor('tickets')?></p>
								  </div>
							   </div>
							</div>
							<div class="p-lg">
							   <div class="row">
								  <div class="col-lg-9">
									 <!-- START timeline-->
									 <ul class="timeline">
										<li data-datetime="Today" class="timeline-separator"></li>
										<!-- START timeline item-->
										<li>
										   <div class="timeline-badge primary">
											  <em class="fa fa-comment"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover left">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong>Aiden Curtis</strong>
															 </a>posted a comment</p>
													   </div>
													</div>
													<p>
													   <em>"Fusce pellentesque congue justo in rutrum. Praesent non nulla et ligula luctus mattis eget at lacus."</em>
													</p>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- END timeline item-->
										<!-- START timeline item-->
										<li class="timeline-inverted">
										   <div class="timeline-badge green">
											  <em class="fa fa-picture-o"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover right">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/04.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong>James Payne</strong>
															 </a>shared a new idea</p>
													   </div>
													</div>
													<a href="#">
													   <img src="img/mockup.png" alt="Img" class="img-responsive">
													</a>
													<p class="text-muted mv">3 Comments</p>
													<div class="media bb p">
													   <small class="pull-right text-muted">12m ago</small>
													   <div class="pull-left">
														  <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb32">
													   </div>
													   <div class="media-body">
														  <div class="media-heading">
															 <p class="m0">
																<a href="#">
																   <strong>Aiden Curtis</strong>
																</a>
															 </p>
															 <p class="m0 text-muted">Hey looks great!</p>
														  </div>
													   </div>
													</div>
													<div class="media bb p">
													   <small class="pull-right text-muted">30m ago</small>
													   <div class="pull-left">
														  <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb32">
													   </div>
													   <div class="media-body">
														  <div class="media-heading">
															 <p class="m0">
																<a href="#">
																   <strong>Samantha Murphy</strong>
																</a>
															 </p>
															 <p class="m0 text-muted">Excellento job!</p>
														  </div>
													   </div>
													</div>
													<div class="media bb p">
													   <small class="pull-right text-muted">30m ago</small>
													   <div class="pull-left">
														  <img src="img/user/04.jpg" alt="Image" class="media-object img-circle thumb32">
													   </div>
													   <div class="media-body">
														  <div class="media-heading">
															 <p class="m0">
																<a href="#">
																   <strong>James Payne</strong>
																</a>
															 </p>
															 <p class="m0 text-muted">WIP guys :)</p>
														  </div>
													   </div>
													</div>
													<form method="post" action="#" class="mt">
													   <textarea placeholder="Comment..." rows="1" class="form-control no-resize"></textarea>
													</form>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- START timeline item-->
										<li>
										   <div class="timeline-badge info">
											  <em class="fa fa-file-o"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover left">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong>Samantha Murphy</strong>
															 </a>shared new files</p>
													   </div>
													</div>
													<ul class="list-unstyled">
													   <li class="pb">
														  <em class="fa fa-file-o fa-fw mr"></em><a href="#" class="text-info">framework-docs-part1.pdf<em class="pull-right fa fa-download fa-fw"></em></a>
													   </li>
													   <li class="pb">
														  <em class="fa fa-file-o fa-fw mr"></em><a href="#" class="text-info">framework-docs-part2.pdf<em class="pull-right fa fa-download fa-fw"></em></a>
													   </li>
													   <li class="pb">
														  <em class="fa fa-file-o fa-fw mr"></em><a href="#" class="text-info">framework-docs-part3.pdf<em class="pull-right fa fa-download fa-fw"></em></a>
													   </li>
													</ul>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- END timeline item-->
										<!-- START timeline item-->
										<li>
										   <div class="timeline-badge purple">
											  <em class="fa fa-map-marker"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover left">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong>Samantha Murphy</strong>
															 </a>shared new location</p>
													   </div>
													</div>
													<p>
													   <em>"Hey guys! Please check the new location for tomorrows's meeting."</em>
													</p>
													<div data-gmap="" data-address="276 N TUSTIN ST, ORANGE, CA 92867" data-styled class="gmap"></div>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- END timeline item-->
										<!-- START timeline separator-->
										<li data-datetime="Yesterday" class="timeline-separator"></li>
										<!-- END timeline separator-->
										<!-- START timeline item-->
										<li>
										   <div class="timeline-badge success">
											  <em class="fa fa-ticket"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover left">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/12.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong>Dennis Green</strong>
															 </a>closed issue <a href="#">#548795</a>
														  </p>
														  <p class="m0">
															 <em>&mdash; bootstrap.js needs update</em>
														  </p>
													   </div>
													</div>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- END timeline item-->
										<li class="timeline-inverted">
										   <div class="timeline-badge warning">
											  <em class="fa fa-ticket"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover right">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/09.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong><?php echo $agentname; ?></strong>
															 </a>assigned
															 <a href="#" class="text-muted">
																<strong>Dennis Green</strong>
															 </a>to issue <a href="#">#548795</a>
														  </p>
														  <p class="m0">
															 <em>&mdash; bootstrap.js needs update</em>
														  </p>
													   </div>
													</div>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- END timeline item-->
										<!-- START timeline item-->
										<li>
										   <div class="timeline-badge danger">
											  <em class="fa fa-ticket"></em>
										   </div>
										   <div class="timeline-panel">
											  <div class="popover left">
												 <div class="arrow"></div>
												 <div class="popover-content">
													<div class="table-grid table-grid-align-middle mb">
													   <div class="col col-xs">
														  <img src="img/user/10.jpg" alt="Image" class="media-object img-circle thumb48">
													   </div>
													   <div class="col">
														  <p class="m0">
															 <a href="#" class="text-muted">
																<strong>Jon Perry</strong>
															 </a>opened issue <a href="#">#548795</a>
														  </p>
														  <p class="m0">
															 <em>&mdash; bootstrap.js needs update</em>
														  </p>
													   </div>
													</div>
												 </div>
											  </div>
										   </div>
										</li>
										<!-- END timeline item-->
										<!-- START timeline item-->
										<li class="timeline-end">
										   <a href="#" class="timeline-badge">
											  <em class="fa fa-plus"></em>
										   </a>
										</li>
										<!-- END timeline item-->
									 </ul>
									 <!-- END timeline-->
								  </div>
								  <div class="col-lg-3">
									 <div class="panel panel-default">
										<div class="panel-body">
										   <div class="text-center">
											  <h3 class="mt0"><?php echo $agentname; ?></h3>
											  <p><?php echo $agentid; ?></p>
										   </div>
										   <hr>
										   <ul class="list-unstyled ph-xl">
											  <li>
												 <em class="fa fa-home fa-fw mr-lg"></em>Group: <?php echo $user_group; ?></li>
											  <li>
												 <em class="fa fa-briefcase fa-fw mr-lg"></em><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
											  </li>
											  <li>
												 <em class="fa fa-graduation-cap fa-fw mr-lg"></em>Status: <?php echo $status; ?></li>
										   </ul>
										</div>
									 </div>
									 <div class="panel panel-default">
										<div class="panel-heading">
										   <a href="#" class="pull-right">
											  <em class="icon-plus text-muted"></em>
										   </a>Contacts</div>
										<div class="list-group">
										   <!-- START User status-->
										   <a href="#" class="media p mt0 list-group-item">
											  <span class="pull-right">
												 <span class="circle circle-success circle-lg"></span>
											  </span>
											  <span class="pull-left">
												 <!-- Contact avatar-->
												 <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb32">
											  </span>
											  <!-- Contact info-->
											  <span class="media-body">
												 <span class="media-heading">
													<strong>Juan Sims</strong>
													<br>
													<small class="text-muted">Designeer</small>
												 </span>
											  </span>
										   </a>
										   <!-- END User status-->
										   <!-- START User status-->
										   <a href="#" class="media p mt0 list-group-item">
											  <span class="pull-right">
												 <span class="circle circle-success circle-lg"></span>
											  </span>
											  <span class="pull-left">
												 <!-- Contact avatar-->
												 <img src="img/user/06.jpg" alt="Image" class="media-object img-circle thumb32">
											  </span>
											  <!-- Contact info-->
											  <span class="media-body">
												 <span class="media-heading">
													<strong>Maureen Jenkins</strong>
													<br>
													<small class="text-muted">Designeer</small>
												 </span>
											  </span>
										   </a>
										   <!-- END User status-->
										   <!-- START User status-->
										   <a href="#" class="media p mt0 list-group-item">
											  <span class="pull-right">
												 <span class="circle circle-danger circle-lg"></span>
											  </span>
											  <span class="pull-left">
												 <!-- Contact avatar-->
												 <img src="img/user/07.jpg" alt="Image" class="media-object img-circle thumb32">
											  </span>
											  <!-- Contact info-->
											  <span class="media-body">
												 <span class="media-heading">
													<strong>Billie Dunn</strong>
													<br>
													<small class="text-muted">Designeer</small>
												 </span>
											  </span>
										   </a>
										   <!-- END User status-->
										   <!-- START User status-->
										   <a href="#" class="media p mt0 list-group-item">
											  <span class="pull-right">
												 <span class="circle circle-warning circle-lg"></span>
											  </span>
											  <span class="pull-left">
												 <!-- Contact avatar-->
												 <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb32">
											  </span>
											  <!-- Contact info-->
											  <span class="media-body">
												 <span class="media-heading">
													<strong>Tomothy Roberts</strong>
													<br>
													<small class="text-muted">Designer</small>
												 </span>
											  </span>
										   </a>
										   <!-- END User status--><a href="#" class="media p mt0 list-group-item text-center text-muted">View all contacts</a>
										</div>
									 </div>
									 <div class="panel panel-default">
										<div class="list-group">
										   <a href="#" class="list-group-item">
											  <em class="pull-right fa fa-users fa-lg fa-fw text-muted mt"></em>
											  <h4 class="list-group-item-heading">1000</h4>
											  <p class="list-group-item-text">Friends</p>
										   </a>
										   <a href="#" class="list-group-item">
											  <em class="pull-right fa fa-rss fa-lg fa-fw text-muted mt"></em>
											  <h4 class="list-group-item-heading">3000</h4>
											  <p class="list-group-item-text">Subscriptions</p>
										   </a>
										   <a href="#" class="list-group-item">
											  <em class="pull-right fa fa-map-marker fa-lg fa-fw text-muted mt"></em>
											  <h4 class="list-group-item-heading">100</h4>
											  <p class="list-group-item-text">Places</p>
										   </a>
										   <a href="#" class="list-group-item">
											  <em class="pull-right fa fa-briefcase fa-lg fa-fw text-muted mt"></em>
											  <h4 class="list-group-item-heading">400</h4>
											  <p class="list-group-item-text">Projects</p>
										   </a>
										   <a href="#" class="list-group-item">
											  <em class="pull-right fa fa-twitter fa-lg fa-fw text-muted mt"></em>
											  <h4 class="list-group-item-heading">17300</h4>
											  <p class="list-group-item-text">Twees</p>
										   </a>
										</div>
									 </div>
								  </div>
							   </div>
							</div>
						</div>
						<!-- End Profile -->
						
						<!-- Contacts -->
						<div id="contents-contacts" class="row" style="display: none;">
							<div class="card col-md-12" style="padding: 15px;">
								<table id="contacts-list" class="display" style="border: 1px solid #f4f4f4; width: 100%;">
									<thead>
										<tr>
											<th>
												<?=$lh->translationFor('lead_id')?>
											</th>
											<th>
												<?=$lh->translationFor('customer_name')?>
											</th>
											<th>
												<?=$lh->translationFor('phone_number')?>
											</th>
											<th>
												<?=$lh->translationFor('last_call_time')?>
											</th>
											<th>
												<?=$lh->translationFor('campaign')?>
											</th>
											<th>
												<?=$lh->translationFor('status')?>
											</th>
											<th>
												<?=$lh->translationFor('comments')?>
											</th>
											<th>
												<?=$lh->translationFor('action')?>
											</th>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
							<?php
							//var_dump($ui->API_GetLeads($user->getUserName()));
							?>
						</div><!-- /.row -->
						<!-- End Contacts -->
					</div>
					
					<div id="popup-hotkeys" class="panel clearfix">
						<div class="panel-heading"><b><?=$lh->translationFor('available_hotkeys')?></b></div>
						<div class="panel-body"><?=$lh->translationFor('no_available_hotkeys')?></div>
						<div class="panel-footer clearfix">
							<div class="text-danger sidecolor" style="padding-right: 5px; background-color: inherit;">
								<small><b><?=$lh->translationFor('note')?>:</b> <?=$lh->translationFor('hotkeys_note')?></small>
							</div>
						</div>
					</div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->

            <?php //print $ui->creamyFooter(); ?>

            <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li id="dialer-tab" class="active"><a href="#control-sidebar-dialer-tab" data-toggle="tab"><i class="fa fa-phone"></i></a></li>
      <li id="agents-tab" class="hidden"><a href="#control-sidebar-users-tab" data-toggle="tab"><i class="fa fa-users"></i></a></li>
      <li id="settings-tab"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-user"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" style="border-width:0; overflow-y: auto; padding-bottom: 30px;">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-dialer-tab">
        <ul class="control-sidebar-menu" id="go_agent_dialer">
			
        </ul>
        <!-- /.control-sidebar-menu -->

        <ul class="control-sidebar-menu" id="go_agent_status" style="margin: 0 0 15px;padding: 0 0 10px;">
			
        </ul>
		
        <ul class="control-sidebar-menu" id="go_agent_manualdial" style="margin-top: -10px;padding: 0 15px;">
			
        </ul>

        <ul class="control-sidebar-menu hidden-xs" id="go_agent_dialpad" style="margin-top: 15px;padding: 0 15px;">
			
        </ul>

        <ul class="control-sidebar-menu hidden-xs" id="go_agent_other_buttons" style="margin-top: 15px;padding: 0 15px;">
			<li id="toggleWebForm" style="padding: 0 5px 15px;">
				<button type="button" name="openWebForm" id="openWebForm" class="btn btn-warning btn-block disabled"><i class="fa fa-external-link"></i> <?=$lh->translationFor('webform')?></button>
			</li>
			<li id="toggleWebFormTwo" style="padding: 0 5px 15px;" class="hidden">
				<button type="button" name="openWebFormTwo" id="openWebFormTwo" class="btn btn-warning btn-block disabled"><i class="fa fa-external-link"></i> <?=$lh->translationFor('webform_two')?></button>
			</li>
			<li style="font-size: 5px;">
				&nbsp;
			</li>
			<li style="padding: 0 5px 15px;">
				<div class="material-switch pull-right">
					<input id="LeadPreview" name="LeadPreview" value="0" type="checkbox"/>
					<label for="LeadPreview" class="label-primary"></label>
				</div>
				<div style="font-weight: bold; text-transform: uppercase;"><?=$lh->translationFor('lead_preview')?></div>
			</li>
			<li id="DialALTPhoneMenu" style="padding: 0 5px 15px; display: none;">
				<div class="material-switch pull-right">
					<input id="DialALTPhone" name="DialALTPhone" value="0" type="checkbox"/>
					<label for="DialALTPhone" class="label-primary"></label>
				</div>
				<div style="font-weight: bold; text-transform: uppercase;"><?=$lh->translationFor('alt_phone_dial')?></div>
			</li>
			<li id="toggleHotkeys" style="padding: 0 5px 15px;">
				<div class="material-switch pull-right">
					<input id="enableHotKeys" name="enableHotKeys" type="checkbox"/>
					<label for="enableHotKeys" class="label-primary"></label>
				</div>
				<div style="font-weight: bold; text-transform: uppercase;"><?=$lh->translationFor('enable_hotkeys')?></div>
			</li>
			<li id="toggleMute" style="padding: 0 5px 15px;">
				<div class="material-switch pull-right">
					<input id="muteMicrophone" name="muteMicrophone" type="checkbox" checked/>
					<label for="muteMicrophone" class="label-primary"></label>
				</div>
				<div style="font-weight: bold; text-transform: uppercase;"><?=$lh->translationFor('microphone')?></div>
			</li>
			<li style="font-size: 5px;">
				<div id="GOdebug" class="material-switch pull-right">&nbsp;</div>
			</li>
			<li class="hidden">
				<button type="button" id="show-callbacks-active" class="btn btn-link btn-block btn-raised"><?=$lh->translateText('Active Callback(s)')?> <span id="callbacks-active" class='badge pull-right bg-red'>0</span></button>
				<button type="button" id="show-callbacks-today" class="btn btn-link btn-block btn-raised"><?=$lh->translateText('Callbacks For Today')?> <span id="callbacks-today" class='badge pull-right bg-red'>0</span></button>
			</li>
        </ul>
		
        <ul class="control-sidebar-menu" id="go_agent_login" style="width: 100%; margin: 25px auto 15px; text-align: center;">
			
        </ul>
		
        <ul class="control-sidebar-menu" id="go_agent_logout" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 5px; text-align: center;">
			<li style="margin-bottom: -5px;">
				<p><strong><?=$lh->translateText("Call Duration")?>:</strong> <span id="SecondsDISP">0</span> <?=$lh->translationFor('second')?></p>
				<span id="session_id" class="hidden"></span>
				<span id="callchannel" class="hidden"></span>
				<input type="hidden" id="callserverip" value="" />
				<span id="custdatetime" class="hidden"></span>
			</li>
			
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Agents View tab content -->
      <div class="tab-pane" id="control-sidebar-users-tab">
		<h4><?=$lh->translationFor('other_agent_status')?></h4>
		<ul class="control-sidebar-menu" id="go_agent_view_list" style="padding: 0px 15px;">
			<li><div class="text-center"><?=$lh->translationFor('loading_agents')?>...</div></li>
		</ul>
	  </div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
		<ul class="control-sidebar-menu" id="go_agent_profile">
			<li>
				<div class="center-block" style="text-align: center; background: #181f23 none repeat scroll 0 0; margin: 0 10px; padding-bottom: 1px; padding-top: 10px;">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<p><?=$ui->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 96, false, true, false)?></p>
						<p style="color:white;"><?=$user->getUserName()?><br><small><?=$lh->translationFor("nice_to_see_you_again")?></small></p>
					</a>
				</div>
			</li>
			<li>
				<div>&nbsp;</div>
			</li>
			<?php
			if ($user->userHasBasicPermission()) {
				//echo '<li>
				//	<div class="text-center"><a href="" data-toggle="modal" id="change-password-toggle" data-target="#change-password-dialog-modal">'.$lh->translationFor("change_password").'</a></div>
				//	<div class="text-center"><a href="./messages.php">'.$lh->translationFor("messages").'</a></div>
				//	<div class="text-center"><a href="./notifications.php">'.$lh->translationFor("notifications").'</a></div>
				//	<div class="text-center"><a href="./tasks.php">'.$lh->translationFor("tasks").'</a></div>
				//</li>';
				//echo $ui->getSidebarItem("./agent.php", "", $lh->translationFor("Home"));
				$numMessages = $db->getUnreadMessagesNumber($user->getUserId());
				echo $ui->getSidebarItem("#messages", "", $lh->translationFor("messages"), $numMessages, "green");
				echo $ui->getSidebarItem("#callbackslist", "", $lh->translationFor("callbacks"), "0", "blue");
				if ($user_info->data->agent_lead_search_override != 'DISABLED') {
					echo $ui->getSidebarItem("#customerslist", "", $lh->translationFor("contacts"), null, "", "agent-lead-search");
				}
			}
			?>
			<li id="pause_code_link" class="hidden">
				<a onclick="PauseCodeSelectBox();"><i class="fa fa-"></i> <span><?=$lh->translationFor('enter_pause_code')?></span></a>
			</li>
		</ul>
		
        <ul class="control-sidebar-menu" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px;">
			<li>
				<div class="center-block" style="text-align: center">
					<a href="#profile" class="btn btn-warning"><i class='fa fa-user'></i> <?=$lh->translationFor("my_profile")?></a>
					 &nbsp; 
					<a href="./logout.php" id="cream-agent-logout" class="btn btn-warning"><i class='fa fa-sign-out'></i> <?=$lh->translationFor("exit")?></a>
				</div>
			</li>
        </ul>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg" style="position: fixed; height: auto;"></div>

        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<!-- AdminLTE App -->
		<script src="adminlte/js/app.min.js"></script>
		
		<!-- Select2 -->
        <script src="js/select2.js" type="text/javascript"></script>
		
		<script type="text/javascript">
			$("#compose-textarea").wysihtml5();
			
			var folder = <?php print $folder; ?>;
			var selectedAll = false;
			var selectedMessages = [];
			
			$(document).ready(function() {
				$("#contacts-list").DataTable();
				
			    //iCheck for checkbox and radio inputs
		        $('input[type="checkbox"].message-selection-checkbox').iCheck({
					checkboxClass: 'icheckbox_minimal-blue',
					radioClass: 'iradio_minimal-blue'
		        });
		        
			    // check individual message
				$('input[type=checkbox].message-selection-checkbox').on("ifUnchecked", ifUnchecked);
			    
			    // uncheck individual message
				$('input[type=checkbox].message-selection-checkbox').on("ifChecked", ifChecked);

			    // uncheck/check all messages
				$(".checkbox-toggle").click(function() {
					if (selectedAll) { $("input[type='checkbox'].message-selection-checkbox", ".mailbox").iCheck("uncheck"); }
					else { $("input[type='checkbox'].message-selection-checkbox", ".mailbox").iCheck("check"); }
					selectedAll = !selectedAll;
				});

				// next button for table.
				$(".mailbox-next").click(function() { datatable.fnPageChange('next'); });

				// previous button for table
				$(".mailbox-prev").click(function() { datatable.fnPageChange('previous'); });

			    // de-star a starred video / star a de-stared video.
			    $("td .fa-star, td .fa-star-o").click(function(e) {
			        e.preventDefault();
			        
			        // Detect type: e.currentTarget.id contains the message id.
					var starred = $(this).hasClass("fa-star");
					var favorite = 1;
					var selectedItem = this;
					
					if (starred) { // unmark message as favorite
						favorite = 0;   
					} // else mark message as favorite
					
					$("#messages-message-box").hide();
					$.post("./php/MarkMessagesAsFavorite.php", { "favorite": favorite, "messageids": [e.currentTarget.id], "folder": folder }, function(data) {
						if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
							// toggle visual change.
				            $(selectedItem).toggleClass("fa-star");
				            $(selectedItem).toggleClass("fa-star-o");
							updateMessages(<?=$user->getUserId()?>, folder);
						} else {
							<?php
								$msg = $ui->calloutErrorMessage($lh->translationFor("message")); 
								print $ui->fadingInMessageJS($msg, "messages-message-box");
							?>
						}
					});
			    });
				
				$("li a[href^='messages.php?']").click(function(e) {
					if (typeof e.target.search !== 'undefined') {
						var thisFolder = e.target.search.replace("?", "");
						thisFolder = thisFolder.split("=");
						updateMessages(<?=$user->getUserId()?>, thisFolder[1]);
					}
				});
				
				$("td a[href^='readmail.php?']").click(function(e) {
					if (typeof e.target.search !== 'undefined') {
						var thisURI = e.target.search.replace("?", "").split("&");
						thisFolder = thisURI[0].split("=");
						thisMessage = thisURI[1].split("=");
						readMessage(thisMessage[1], thisFolder[1]);
					}
				});
				
				
				<?php
				// mark messages as favorite.
				$unableFavoriteCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_favorites"));
				print $ui->mailboxAction(
					"messages-mark-as-favorite", 											// classname
					"php/MarkMessagesAsFavorite.php", 										// php to request
					'updateMessages('.$user->getUserId().', folder); for (i=0; i<selectedMessages.length; i++) { $("td.mailbox-star i#"+selectedMessages[i]).removeClass("fa-star-o").addClass("fa-star"); }', // success js
					$ui->fadingInMessageJS($unableFavoriteCode, "messages-message-box"),	// failure js
					array("favorite" => 1));												// custom parameters
				?>
				
				<?php
				// mark messages as read
				$unableReadCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_read"));
				print $ui->mailboxAction(
					"messages-mark-as-read", 												// classname
					"php/MarkMessagesAsRead.php", 											// php to request
					'updateMessages('.$user->getUserId().', folder); for (i=0; i<selectedMessages.length; i++) { $("td.mailbox-star i#"+selectedMessages[i]).parents("tr").removeClass("unread"); }', 												// success js
					$ui->fadingInMessageJS($unableReadCode, "messages-message-box")); 		// failure js
				?>
				
				<?php
				// mark messages as unread
				$unableUnreadCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_unread"));
				print $ui->mailboxAction(
					"messages-mark-as-unread", 												// classname
					"php/MarkMessagesAsUnread.php", 										// php to request
					'updateMessages('.$user->getUserId().', folder); for (i=0; i<selectedMessages.length; i++) { $("td.mailbox-star i#"+selectedMessages[i]).parents("tr").addClass("unread"); }', // success js
					$ui->fadingInMessageJS($unableUnreadCode, "messages-message-box")); 	// failure js
				?>
				
				<?php
				// send to junk mail
				$junkText = 'data+" '.$lh->translationFor("out_of").' "+selectedMessages.length+" '.
					$lh->translationFor("messages_sent_trash").'"';
				print $ui->mailboxAction(
					"messages-send-to-junk",					// classname
					"php/JunkMessages.php",						// php to request
					"updateMessages(".$user->getUserId().", folder); swal($junkText);");		// result js
				?>
				
				<?php
				// restore mail from junk
				$unjunkText = 'data+" '.$lh->translationFor("out_of").' "+selectedMessages.length+" '.
					$lh->translationFor("messages_recovered_trash").'"';
				print $ui->mailboxAction(
					"messages-restore-message",					// classname
					"php/UnjunkMessages.php",					// php to request
					"updateMessages(".$user->getUserId().", folder); swal($unjunkText);");		// result js
				?>
				
				<?php
				// delete messages.
				$unableDeleteCode = $ui->calloutErrorMessage($lh->translationFor("unable_delete_messages"));
				print $ui->mailboxAction(
					"messages-delete-permanently", 											// classname
					"php/DeleteMessages.php", 												// php to request
					"updateMessages(".$user->getUserId().", folder);", 												// success js
					$ui->fadingInMessageJS($unableDeleteCode, "messages-message-box")); 	// failure js
				?>
				
				$("#edit-profile").click(function(){
				    $('.input-disabled').prop('disabled', false);
				    //$('.hide_div').show();
				    $("input:required, select:required").addClass("required_div");
				    $('#edit-profile').addClass('hidden');
				    
				    var txtBox=document.getElementById("first_name" );
					txtBox.focus();
				    //$("#submit_div").focus(function() { $(this).select(); } );
				    //$('input[name="first_name"]').focus();
				});

				$("#submit_edit_form").click(function(){
				//alert("User Created!");
					var validate = 0;

					if($('#name_form')[0].checkValidity()) {
					    if($('#gender_form')[0].checkValidity()) {
					    	if($('#contact_details_form')[0].checkValidity()) {
								
								//alert("Form Submitted!");
								var log_user = '<?=$_SESSION['user']?>';
								var log_group = '<?=$_SESSION['usergroup']?>';
								$.ajax({
									url: "./php/ModifyCustomer.php",
									type: 'POST',
									data: $("#name_form, #gender_form, #contact_details_form, #comment_form").serialize() + '&log_user=' + log_user + '&log_group=' + log_group,
									success: function(data) {
									  // console.log(data);
										  if(data == 1){
										  	  $('.output-message-success').show().focus().delay(2000).fadeOut().queue(function(n){$(this).hide(); n();});
											  window.setTimeout(function(){location.reload();},2000);
										  }else{
											  $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
										  }
									}
								});

							}else{
								validate = 1;
							}
						}else{
							validate = 1;
						}
					}else{
						validate = 1;
					}

					if(validate == 1){
						$('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
						validate = 0;
					}
				
				});
				
				// Start Mail Composer
				// external recipients
				$('#external_recipients').multiple_emails();
	
				// attachments
				$('.attachment').MultiFile({
					max: 5,
					//accept: 'jpg|jpeg|png|gif|pdf|doc|pages|numbers|xls|docx|xlsx|mp4|mpg|mpeg|avi|m4v|txt|rdf|mp3|ogg|zip|html',
					list: '#attachment-list',
					STRING: {
						remove: '<i class="fa fa-times"></i>'
					}
				});
				
				// send a message
				$("#send-message-form").validate({
					errorElement: "small",
					rules: {
						mimeType: "multipart/form-data",
						subject: "required",
						message: "required",
						touserid: {
							required: true,
							min: 1,
							number: true
						}
					},
					messages: {
						touserid: "<?php $lh->translateText("you_must_choose_user"); ?>",
					},
					submitHandler: function() {
						// file uploads only allowed on modern browsers (sorry IE < 10).
						var form = $("#send-message-form");
						var formdata = false;
						if (window.FormData){
							formdata = new FormData(form[0]);
						}
						<?php
							$okMsg = $ui->dismissableAlertWithMessage($lh->translationFor("message_successfully_sent"), true, false);
							$koMsg = $ui->dismissableAlertWithMessage($lh->translationFor("unable_send_message"), false, true);
						?>
						//submit the form
						$("#compose-mail-results").html();
						$("#compose-mail-results").hide();
						$.ajax({
							url         : 'php/SendMessage.php',
							data        : formdata ? formdata : form.serialize(),
							cache       : false,
							contentType : false,
							processData : false,
							type        : 'POST',
							success     : function(data, textStatus, jqXHR){
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									$("#compose-mail-results").html('<?php print $okMsg; ?>');
									$("#compose-mail-results").fadeIn(); //show confirmation message
									$("#send-message-form")[0].reset();
									$(".MultiFile-label").remove();
									
									setTimeout(function() {
										$("#compose-mail-results").fadeOut();
									}, 3000);
								} else { // failure
									$("#compose-mail-results").html('<?php print $koMsg; ?>');
									$("#compose-mail-results").fadeIn(); //show confirmation message
								}
							}, error: function(jqXHR, textStatus, errorThrown) {
								$("#compose-mail-results").html('<?php print $koMsg; ?>');
								$("#compose-mail-results").fadeIn(); //show confirmation message
							}
						});
	
						return false; //don't let the form refresh the page...
					}					
				});
				
				// discard message
				$('#compose-mail-discard').click(function(e) {
					e.preventDefault();
					$("#mail-composemail").hide();
					$("#mail-messages").show();
					updateMessages(<?=$user->getUserId()?>, 0);
				});
				// End Mail Composer
				
				// Start Read Messages
				// print.
				$('.mail-print').click(function() {
					var headerLogo = $("header.main-header img").prop('src');
					$('#message-full-box').printThis({
						loadCSS: [
							"<?php print GO_BASE_DIRECTORY; ?>/css/printpage.css"
						],
						importCSS: false,
						pageTitle: $("#read-message-subject").html(),
						header: '<div class="print-logo"><img src="'+headerLogo+'" height="32"></div>'
					});
				});
				
				<?php 
				// delete
				print $ui->mailboxAction(
					"mail-delete", 																		// class name
					"php/DeleteMessages.php", 															// POST Request URL
					"$('#mail-readmail').hide(); $('#mail-messages').show(); updateMessages(".$user->getUserId().", folder); swal('".$lh->translationFor("message_successfully_deleted")."');", 													// Success JS				
					$ui->showCustomErrorMessageAlertJS($lh->translationFor("unable_delete_messages")),  // Failure JS
					null,																				// custom params 
					true,																				// confirmation ?
					true);																				// check selected messages?
				?>
				
				// reply
				$('.mail-reply').click(function () {
					var text = $('#mailbox-message-text').html();
					var reply_text = responseEncodedMessageText(text, $("#read-message-from-name").html());
					var reply_subject = "Re: " + $("#read-message-subject").html();
					var reply_user = $("#read-message-from-id").html();
					
					$("#touserid").val(reply_user);
					$("#subject").val(reply_subject);
					$("#compose-textarea").val(reply_text);
					$("#compose-textarea").show();
					$(".wysihtml5-sandbox").remove();
					$(".wysihtml5-toolbar").remove();
					$("input[name='_wysihtml5_mode']").remove();
					$("#compose-textarea").wysihtml5();
					
					setTimeout(function() {
						$("#mail-composemail").show();
						$("#mail-readmail").hide();
					}, 1000);
				});
				
				// forward
				$('.mail-forward').click(function () {
					var text = $('#mailbox-message-text').html();
					var forward_text = responseEncodedMessageText(text, $("#read-message-from-name").html());
					var forward_subject = "Fwd: " + $("#read-message-subject").html();
					
					$("#touserid").val(0);
					$("#subject").val(forward_subject);
					$("#compose-textarea").val(forward_text);
					$("#compose-textarea").show();
					$(".wysihtml5-sandbox").remove();
					$(".wysihtml5-toolbar").remove();
					$("input[name='_wysihtml5_mode']").remove();
					$("#compose-textarea").wysihtml5();
					
					setTimeout(function() {
						$("#mail-composemail").show();
						$("#mail-readmail").hide();
					}, 1000);
				});
				
				$("div a[href='composemail.php']").click(function() {
					$("#touserid").val(0);
					$("#subject").val('');
					$("#compose-textarea").val('');
					$("#compose-textarea").show();
					$(".wysihtml5-sandbox").remove();
					$(".wysihtml5-toolbar").remove();
					$("input[name='_wysihtml5_mode']").remove();
					$("#compose-textarea").wysihtml5();
					
					setTimeout(function() {
						$("#mail-composemail").show();
						$("#mail-readmail").hide();
						$("#mail-messages").hide();
					}, 1000);
				});
				
				/**
				 * Deletes a customer
				 */
				$("#modifyCustomerDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r === true) {
						//var customerid = $(this).attr('href');
						$.post("./php/DeleteContact.php", $("#modifycustomerform").serialize() ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("Contact Successfully Deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("Unable to Delete Contact"); ?>: "+data); }
						});
					}
				});
				
				$('.form-control').on('focus blur', function (e) {
					$(this).parents('.label-floating').toggleClass('focused', (e.type === 'focus' || this.value.length > 0));
				}).trigger('blur');
				
				$('.label-floating .form-control').change(function() {
					var thisVal = $(this).val();
					$(this).parents('.label-floating').toggleClass('focused', (thisVal.length > 0));
				});
				
				setInterval(function() {
					if (!$("#contents-messages").is(':visible') && (typeof refresh_interval !== 'undefined' && refresh_interval < 5000) && (typeof window_focus !== 'undefined' && window_focus)) {
						updateMessages(<?=$user->getUserId()?>, 0);
					}
				}, 5000);
				
				$('.select2').select2({ theme: 'bootstrap' });
				$.fn.select2.defaults.set( "theme", "bootstrap" );
				
			});
			
			// generates the reply-to or forward message text. This text will be suitable for placing in the reply-to/forward content
			// of a message. It will be:
			// 1. stripped of all html entities
			// 2. Added --- Original message from "replyUser" --- 
			// 3. cut down to 512 characters (added ...)
			// 4. wrapped in <pre>...</pre>
			// 5. encoded to be passed as URI
			function responseEncodedMessageText(text, replyUser) {
				result = text.trim().substr(0, 512);
				result = "-------- <?php $lh->translateText("original_message_from"); ?> "+replyUser+" --------\n"+result;
				result = "<br/><br/><pre>"+result+"</pre>";
				//result = encodeURI(result);
				return result;				
			}
			// End Read Messages
			
			function updateMessages(user_id, folder_id) {
				$("#mail-messages div.mailbox-messages").hide();
				$(".mail-preloader").show();
				
				var postData = {
					module_name: 'GOagent',
					action: 'UpdateMessages',
					user_id: user_id,
					folder: folder_id
				};
				
				$.ajax({
					type: 'POST',
					url: 'modules/GOagent/GOagentJS.php',
					processData: true,
					data: postData,
					dataType: "json",
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					}
				})
				.done(function (result) {
					if (result.result == 'success') {
						selectedMessages = [];
						selectedAll = false;
						folder = folder_id;
						
						$("#mail-messages div.mailbox-controls").html(result.controls);
						$("#folders-list").html(result.folders);
						var thisTopBar = result.topbar;
						$("li.messages-menu").html($(thisTopBar).html());
						$("li.messages-menu ul.menu").slimScroll({
							height: '200px'
						});
						$("div.mailbox-messages").html(result.messages);
						
						$(".mail-preloader").hide();
						$("#mail-messages div.mailbox-messages").slideDown();
						
						//iCheck for checkbox and radio inputs
						$('input[type="checkbox"].message-selection-checkbox').iCheck({
							checkboxClass: 'icheckbox_minimal-blue',
							radioClass: 'iradio_minimal-blue'
						});
						
						// check individual message
						$('input[type=checkbox].message-selection-checkbox').off("ifUnchecked", ifUnchecked).on("ifUnchecked", ifUnchecked);
						
						// uncheck individual message
						$('input[type=checkbox].message-selection-checkbox').off("ifChecked", ifChecked).on("ifChecked", ifChecked);
						
						// next button for table.
						$(".mailbox-next").off('click');
						$(".mailbox-next").click(function() { datatable.fnPageChange('next'); });
						
						// previous button for table
						$(".mailbox-prev").off('click');
						$(".mailbox-prev").click(function() { datatable.fnPageChange('previous'); });
						
						// uncheck/check all messages
						$("button.messages-mark-as-read").off('click');
						$(".checkbox-toggle").click(function() {
							if (selectedAll) { $("input[type='checkbox'].message-selection-checkbox", ".mailbox").iCheck("uncheck"); }
							else { $("input[type='checkbox'].message-selection-checkbox", ".mailbox").iCheck("check"); }
							selectedAll = !selectedAll;
						});
						
						// de-star a starred video / star a de-stared video.
						$("td .fa-star, td .fa-star-o").off('click');
						$("td .fa-star, td .fa-star-o").click(function(e) {
							e.preventDefault();
							
							// Detect type: e.currentTarget.id contains the message id.
							var starred = $(this).hasClass("fa-star");
							var favorite = 1;
							var selectedItem = this;
							
							if (starred) { // unmark message as favorite
								favorite = 0;   
							} // else mark message as favorite
							
							$("#messages-message-box").hide();
							$.post("./php/MarkMessagesAsFavorite.php", { "favorite": favorite, "messageids": [e.currentTarget.id], "folder": folder_id }, function(data) {
								if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
									// toggle visual change.
									$(selectedItem).toggleClass("fa-star");
									$(selectedItem).toggleClass("fa-star-o");
									updateMessages(user_id, folder);
								} else {
									<?php
										$msg = $ui->calloutErrorMessage($lh->translationFor("message")); 
										print $ui->fadingInMessageJS($msg, "messages-message-box");
									?>
								}
							});
						});
						
						$("li a[href^='messages.php?']").off('click');
						$("li a[href^='messages.php?']").click(function(e) {
							if (typeof e.target.search !== 'undefined') {
								var thisFolder = e.target.search.replace("?", "");
								thisFolder = thisFolder.split("=");
								updateMessages(<?=$user->getUserId()?>, thisFolder[1]);
							}
						});
						
						$("td a[href^='readmail.php?']").off('click');
						$("td a[href^='readmail.php?']").click(function(e) {
							if (typeof e.target.search !== 'undefined') {
								var thisURI = e.target.search.replace("?", "").split("&");
								thisFolder = thisURI[0].split("=");
								thisMessage = thisURI[1].split("=");
								readMessage(thisMessage[1], thisFolder[1]);
							}
						});
						
						$("button.messages-mark-as-favorite").off('click');
						<?php
						// mark messages as favorite.
						$unableFavoriteCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_favorites"));
						print $ui->mailboxAction(
							"messages-mark-as-favorite", 											// classname
							"php/MarkMessagesAsFavorite.php", 										// php to request
							'updateMessages('.$user->getUserId().', folder); for (i=0; i<selectedMessages.length; i++) { $("td.mailbox-star i#"+selectedMessages[i]).removeClass("fa-star-o").addClass("fa-star"); }', // success js
							$ui->fadingInMessageJS($unableFavoriteCode, "messages-message-box"),	// failure js
							array("favorite" => 1));												// custom parameters
						?>
						
						$("button.messages-mark-as-read").off('click');
						<?php
						// mark messages as read
						$unableReadCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_read"));
						print $ui->mailboxAction(
							"messages-mark-as-read", 												// classname
							"php/MarkMessagesAsRead.php", 											// php to request
							'updateMessages('.$user->getUserId().', folder); for (i=0; i<selectedMessages.length; i++) { $("td.mailbox-star i#"+selectedMessages[i]).parents("tr").removeClass("unread"); }', 												// success js
							$ui->fadingInMessageJS($unableReadCode, "messages-message-box")); 		// failure js
						?>
						
						$("button.messages-mark-as-unread").off('click');
						<?php
						// mark messages as unread
						$unableUnreadCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_unread"));
						print $ui->mailboxAction(
							"messages-mark-as-unread", 												// classname
							"php/MarkMessagesAsUnread.php", 										// php to request
							'updateMessages('.$user->getUserId().', folder); for (i=0; i<selectedMessages.length; i++) { $("td.mailbox-star i#"+selectedMessages[i]).parents("tr").addClass("unread"); }', // success js
							$ui->fadingInMessageJS($unableUnreadCode, "messages-message-box")); 	// failure js
						?>
						
						$("button.messages-send-to-junk").off('click');
						<?php
						// send to junk mail
						$junkText = 'data+" '.$lh->translationFor("out_of").' "+selectedMessages.length+" '.
							$lh->translationFor("messages_sent_trash").'"';
						print $ui->mailboxAction(
							"messages-send-to-junk",					// classname
							"php/JunkMessages.php",						// php to request
							"updateMessages(".$user->getUserId().", folder); swal($junkText);");		// result js
						?>
						
						$("button.messages-restore-message").off('click');
						<?php
						// restore mail from junk
						$unjunkText = 'data+" '.$lh->translationFor("out_of").' "+selectedMessages.length+" '.
							$lh->translationFor("messages_recovered_trash").'"';
						print $ui->mailboxAction(
							"messages-restore-message",					// classname
							"php/UnjunkMessages.php",					// php to request
							"updateMessages(".$user->getUserId().", folder); swal($unjunkText);");		// result js
						?>
						
						$("button.messages-delete-permanently").off('click');
						<?php
						// delete messages.
						$unableDeleteCode = $ui->calloutErrorMessage($lh->translationFor("unable_delete_messages"));
						print $ui->mailboxAction(
							"messages-delete-permanently", 											// classname
							"php/DeleteMessages.php", 												// php to request
							"updateMessages(".$user->getUserId().", folder);", 												// success js
							$ui->fadingInMessageJS($unableDeleteCode, "messages-message-box")); 	// failure js
						?>
						
						// Hijack links on left menu
						$("a:regex(href, messages|composemail|readmail)").off('click', hijackThisLink).on('click', hijackThisLink);
					}
				});
			}
			
			function readMessage(message_id, folder_id) {
				$("#mailbox-message-text, #read-message-attachment, #mail-readmail div.mailbox-read-info, #mail-readmail div.mailbox-controls").hide();
				$(".mail-preloader").show();
				
				var postData = {
					module_name: 'GOagent',
					action: 'ReadMessage',
					user_id: <?=$user->getUserId()?>,
					messageid: message_id,
					folder: folder_id
				};
				
				$.ajax({
					type: 'POST',
					url: 'modules/GOagent/GOagentJS.php',
					processData: true,
					data: postData,
					dataType: "json",
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					}
				})
				.done(function (result) {
					if (result.result == 'success') {
						selectedMessages = [message_id];
						$("#read-message-subject").html(result.message.subject);
						$("#read-message-from").html(result.from.user);
						$("#read-message-from-id").html(result.from.id);
						$("#read-message-from-name").html(result.from.name);
						$("#read-message-date").html(result.message.date);
						$("#mailbox-message-text").html(result.message.message);
						$("#read-message-attachment").html(result.attachments);
						
						$(".mail-preloader").hide();
						$("#mailbox-message-text, #read-message-attachment, #mail-readmail div.mailbox-read-info, #mail-readmail div.mailbox-controls").slideDown();
						
						if (result.from.user != '' || result.from.user != 'Unknown') {
							$("button.mail-reply, button.mail-forward").removeClass('hidden');
						}
					}
				});
			}
			
			function ifUnchecked(e) {
				var index = selectedMessages.indexOf(e.currentTarget.value);
				if (index >= 0) selectedMessages.splice(index, 1);
			}
			function ifChecked(e) {
				if (e.currentTarget.value != 'on') selectedMessages.push(e.currentTarget.value);
			}
		</script>
		<!-- SnackbarJS -->
        <script src="js/snackbar.js" type="text/javascript"></script>
		<!-- Vue Avatar -->
        <script src="js/vue-avatar/vue.min.js" type="text/javascript"></script>
        <script src="js/vue-avatar/vue-avatar.min.js" type="text/javascript"></script>
		<script type='text/javascript'>
			var goOptions = {
				el: 'body',
				components: {
					'avatar': Avatar.Avatar,
					'rules': {
						props: ['items'],
						template: 'For example:' +
							'<ul id="example-1">' +
							'<li v-for="item in items"><b>{{ item.username }}</b> becomes <b>{{ item.initials }}</b></li>' +
							'</ul>'
					}
				},
		
				data: {
					items: []
				},
		
				methods: {
					initials: function(username, initials) {
						this.items.push({username: username, initials: initials});
					}
				}
			};
			var goAvatar = new Vue(goOptions);
			
			goAvatar._init();
		</script>
    </body>
</html>
