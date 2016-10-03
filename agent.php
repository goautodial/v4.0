<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
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


$lead_id = $_GET['lead_id'];
$output = $ui->API_GetLeadInfo($lead_id);
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
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <!-- Customized Style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

		<!-- javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
		
        <!-- Creamy App -->
        <!--<script src="js/app.min.js" type="text/javascript"></script>-->
		
        <!-- theme_dashboard folder -->
		<!-- FONT AWESOME-->
		<link rel="stylesheet" href="theme_dashboard/fontawesome/css/font-awesome.min.css">
		<!-- SIMPLE LINE ICONS-->
		<link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">
		<!-- ANIMATE.CSS-->
		<link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">
		<!-- WHIRL (spinners)-->
		<link rel="stylesheet" href="theme_dashboard/whirl/dist/whirl.css">
		<!-- =============== PAGE VENDOR STYLES ===============-->
		<!-- WEATHER ICONS-->
		<link rel="stylesheet" href="theme_dashboard/weather-icons/css/weather-icons.min.css">
		<!-- =============== BOOTSTRAP STYLES ===============-->
		<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
		<!-- =============== APP STYLES ===============-->
		<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">
		<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
		<!-- Datetime picker --> 
        <link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		
		<!-- DATA TABES SCRIPT -->
		<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
		<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		<!-- Bootstrap WYSIHTML5 -->
		<!--<script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>-->
		<!-- iCheck -->
		<!--<script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>-->
		<!-- SLIMSCROLL-->
		<script src="theme_dashboard/slimScroll/jquery.slimscroll.min.js"></script>
		<!-- SWEETALERT-->
		<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>
		<!-- FastClick -->
		<!--<script src="js/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>-->
		<!-- MD5 HASH-->
		<script src="js/jquery.md5.js" type="text/javascript"></script>
        <!-- Date Picker -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		
        <!-- X-Editable -->
        <link rel="stylesheet" href="theme_dashboard/x-editable/dist/css/bootstrap-editable.css">
        <script type="text/javascript" src="theme_dashboard/x-editable/dist/js/bootstrap-editable.min.js"></script>

  		<!-- Theme style -->
  		<link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

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
				
				$.each(country_codes, function(key, value) {
					$("#country_code").append('<option value="'+key+'">'+value+'</option>');
				});
			});
			
			$(function() {
				$("a[id='first_name'], a[id='middle_initial'], a[id='last_name']").on('hidden', function() {
					var thisID = $(this).attr('id');
					$('#'+thisID+'_label').addClass('hidden');
				});
				
				$("a[id='first_name'], a[id='middle_initial'], a[id='last_name']").on('shown', function() {
					var thisID = $(this).attr('id');
					var oldValue = $(this).editable('getValue', true);
					console.log(oldValue);
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
					title: 'Enter First Name',
					placeholder: 'Enter First Name',
					emptytext: '&nbsp;',
					unsavedclass: null
				});
				$("a[id='middle_initial']").editable({
					type: 'text',
					title: 'Enter Middle Initial',
					placeholder: 'Enter Middle Initial',
					emptytext: '&nbsp;',
					unsavedclass: null
				});
				$("a[id='last_name']").editable({
					type: 'text',
					value: '',
					title: 'Enter Last Name',
					placeholder: 'Enter Last Name',
					emptytext: '&nbsp;',
					unsavedclass: null
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
						<li class="active"><i class="fa fa-home"></i> Home</li>
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
									  <ul role="tablist" class="nav nav-tabs nav-justified">
									  <!-- Nav task panel tabs-->
										 <li role="presentation" class="active">
											<a href="#contact_info" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-user hidden"></span>
												<?=$lh->translationFor('contact_information')?></a>
										 </li>
										 <li role="presentation">
											<a href="#comments" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-comments-o hidden"></span>
											    <?=$lh->translationFor('comments')?></a>
										 </li>
										 <li role="presentation">
											<a href="#scripts" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-file-text-o hidden"></span>
												<?=$lh->translationFor('script')?></a>
										 </li>
										 <li role="presentation">
											<a href="#activity" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
												<span class="fa fa-calendar hidden"></span>
												<?=$lh->translationFor('activity')?></a>
										 </li>
									  </ul>
									</div>
									<!-- Tab panes-->
									<div class="tab-content bg-white">
										<div id="activity" role="tabpanel" class="tab-pane">
											<table class="table table-striped">
							                    <tr>
							                    	<td>
							                    		<center>
							                    		<em class="fa fa-user fa-fw"></em>
							                    		</center>
							                    	</td>
							                        <td>
							                           <div>1238: Outbound call to + 1650233332342</div>
							                           <div class="text-muted">
							                           		<small>March 10, 2015</small>
							                           </div>
							                        </td>
							                        <td class="text-muted text-center hidden-xs hidden-sm">
							                           <strong>254</strong>
							                        </td>
							                        <td class="hidden-xs hidden-sm"><a href="">Arnold Gray</a>
							                           <br>
							                           <small>March 10, 2015</small>
							                        </td>
							                    </tr>
							                    <tr>
							                    	<td>
							                    		<center>
							                    		<em class="fa fa-user fa-fw"></em>
							                    		</center>
							                    	</td>
							                        <td>
							                           <div>1238: Call missed from + 1273934031</div>
							                           <div class="text-muted">
							                           		<small>March 10, 2015</small>
							                           </div>
							                        </td>
							                        <td class="text-muted text-center hidden-xs hidden-sm">
							                           <strong>28</strong>
							                        </td>
							                        <td class="hidden-xs hidden-sm"><a href="">Erika Mckinney</a>
							                           <br>
							                           <small>March 10, 2015</small>
							                        </td>
							                    </tr>
							                    <tr>
							                    	<td>
							                    		<center>
							                    		<em class="fa fa-user fa-fw"></em>
							                    		</center>
							                    	</td>
							                        <td>
							                           <div>Latest udpates and news about this forum</div>
							                           <div class="text-muted">
							                           		<small>March 10, 2015</small>
							                           </div>
							                        </td>
							                        <td class="text-muted text-center hidden-xs hidden-sm">
							                           <strong>561</strong>
							                        </td>
							                        <td class="hidden-xs hidden-sm"><a href="">Annette Ruiz</a>
							                           <br>
							                           <small>March 10, 2015</small>
							                        </td>
							                    </tr>
								            </table>
										</div>
									
										<div id="contact_info" role="tabpanel" class="tab-pane active">

											<fieldset style="padding-bottom: 0px; margin-bottom: 0px;">
												<h4>
													<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile">Edit Information</a>
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
																<input id="phone_number" name="phone_number" type="number" min="0" width="auto" value="<?php echo $phone_number;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled required>
																<label for="phone_number">Phone Number</label>
																<!--
																<span class="mda-input-group-addon">
																	<em class="fa fa-phone fa-lg"></em>
																</span>-->
															</div>
														</div>
														<div class="col-sm-6">
															<div class="mda-form-group label-floating">
																<input id="alt_phone" name="alt_phone" type="number" min="0" width="100" value="<?php echo $alt_phone;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="alt_phone">Alternative Phone Number</label>
															</div>
														</div>
													</div>
													<!-- /.phonenumber & alt phonenumber -->
													
													<div class="mda-form-group label-floating">
														<input id="address1" name="address1" type="text" width="auto" value="<?php echo $address1;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
														<label for="address1">Address</label> 
														<!--<span class="mda-input-group-addon">
															<em class="fa fa-home fa-lg"></em>
														</span>-->
													</div>
													
													<div class="mda-form-group label-floating">
														<input id="address2" name="address2" type="text" value="<?php echo $address2;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
														<label for="address2">Address 2</label>
													</div>
													
													<div class="row">
														<div class="col-sm-4">
															<div class="mda-form-group label-floating">
																<input id="city" name="city" type="text" value="<?php echo $city;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="city">City</label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="mda-form-group label-floating">
																<input id="state" name="state" type="text" value="<?php echo $state;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="state">State</label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="mda-form-group label-floating">
																<input id="postal_code" name="postal_code" type="text" value="<?php echo $postal_code;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="postal_code">Postal Code</label>
															</div>
														</div>
													</div><!-- /.city,state,postalcode -->
												
													<div class="mda-form-group label-floating">
														<select id="country_code" name="country_code" type="text" maxlength="3"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select input-disabled" disabled>
															<option value=""></option>
														</select>
														<label for="country">Country Code</label>
													</div>
													<div class="mda-form-group label-floating"><!-- add "mda-input-group" if with image -->
														<input id="email" name="email" type="text" width="auto" value="<?php echo $email;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
														<label for="email">E-mail Address</label>
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
																<label for="title">Title</label>
															</div>
														</div>
														<div class="col-sm-3">
															<div class="mda-form-group label-floating">
																<select id="gender" name="gender" value="<?php echo $gender;?>"
																	class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select input-disabled" disabled>
																	<?php 
																		if ($gender == "M") {
																	?>
																		<option selected value="M">Male</option>
																		<option value="F">Female</option>
																	<?php
																		} else if($gender == "F") {
																	?>
																		<option selected value="F">Female</option>
																		<option value="M">Male</option>
																	<?php
																		} else {
																	?>
																		<option selected disabled value=""></option>
																		<option value="M">Male</option>
																		<option value="F">Female</option>
																	<?php
																		}
																	?>
																</select>
																<label for="gender">Gender</label>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="mda-form-group label-floating">
																<input type="date" id="date_of_birth" value="" name="date_of_birth" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" disabled>
																<label for="date_of_birth">Date Of Birth</label>
															</div>
														</div>
													</div><!-- /.gender & title -->                   
												</form>
											
							                <!-- NOTIFICATIONS -->
											<div id="notifications_list">
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
											</div>

							                <div class="hide_div">
							                	<button type="submit" name="submit" id="submit_edit_form" class="btn btn-primary btn-block btn-flat">Submit</button>
							                </div>
							               </fieldset>
										</div><!--End of Profile-->
										
										<div id="comments" role="tabpanel" class="tab-pane">
											<div class="row">
												<div class="col-sm-12">
													<h4><!--Comments-->
														<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile">Edit Information</a>
													</h4>
												
													<form role="form" id="comment_form" class="formMain form-inline" >
														<div class="mda-form-group hidden">
															<p style="padding-right:0px;padding-top: 20px;">Comments:</p> 
															<button id="ViewCommentButton" onClick="ViewComments('ON');" value="-History-" class="hidden"></button>
														</div>
														<div class="form-group" style="float: left; width:100%;">
															<textarea rows="10" id="comments" name="comments" class="form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea input-disabled note-editor note-editor-margin" style="resize:none; width: 100%;" disabled><?=$comments?></textarea>
															<label for="comments">Comments</label>
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
															<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="reload-script" style="padding: 5px;">Reload Script</a>
														</h4>
														<div id="ScriptContents" style="min-height: 100px; border: dashed 1px #c0c0c0; padding: 20px 5px 5px;">
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
						                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translateText("Script"); ?></b></h4>
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
									<div class="box-body no-padding">
										<?php //print $ui->getMessageFoldersAsList($folder); ?>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
							</div><!-- /.col -->
							
							<!-- main content right side column -->
							<div class="col-md-9">
								<div class="box box-default">
									<div class="box-header with-border">
										<h3 class="box-title"><?php $lh->translateText("messages"); ?></h3>
									</div><!-- /.box-header -->
									<div class="box-body no-padding">
										<div class="mailbox-controls">
											<?php //print $ui->getMailboxButtons($folder); ?>
										</div>
										<div class="table-responsive mailbox-messages">
											<?php //print $ui->getMessagesFromFolderAsTable($user->getUserId(), $folder); ?>
										</div><!-- /.mail-box-messages -->
									</div><!-- /.box-body -->
									<div class="box-footer no-padding">
										<div class="mailbox-controls">
											<div id="messages-message-box">
												<?php //if (!empty($message)) { print $ui->calloutInfoMessage($message); } ?>
											</div>
											<?php //print $ui->getMailboxButtons($folder); ?>
										</div>
									</div>
								</div><!-- /. box -->
							</div><!-- /.col -->
						</div><!-- /.row -->
						
						
						<div id="contents-callbacks" class="row" style="display: none;">
							<div class="card col-md-12" style="padding: 15px;">
								<table id="callback-list" class="display" style="border: 1px solid #f4f4f4">
									<thead>
										<tr>
											<th>
												Customer Name
											</th>
											<th>
												Phone Number
											</th>
											<th>
												Last Call Time
											</th>
											<th>
												Callback Time
											</th>
											<th>
												Campaign
											</th>
											<th>
												Comments
											</th>
											<th>
												Action
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
										<span class="hidden-xs">Calls Today</span>
										<!-- <span>Views</span> -->
									 </p>
								  </div>                    
								  <div class="col-xs-4 br">
									 <h3 class="m0"><?php echo $totalsalestoday; ?></h3>
									 <p class="m0">Sales Today</p>
								  </div>
								  <div class="col-xs-4">
									 <h3 class="m0">100</h3>
									 <p class="m0">Tickets</p>
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
												Lead ID
											</th>
											<th>
												Customer Name
											</th>
											<th>
												Phone Number
											</th>
											<th>
												Last Call Time
											</th>
											<th>
												Campaign
											</th>
											<th>
												Status
											</th>
											<th>
												Comments
											</th>
											<th>
												Action
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
      <li id="agents-tab" class="hidden"><a href="#control-sidebar-agents-tab" data-toggle="tab"><i class="fa fa-users"></i></a></li>
      <li id="settings-tab"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-user"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" style="border-width:0; overflow-y: hidden; padding-bottom: 30px;">
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
			<li id="toggleWebForms" style="padding: 0 5px 15px;">
				<button type="button" name="openWebForm" id="openWebForm" class="btn btn-warning btn-block disabled"><i class="fa fa-external-link"></i> <?=$lh->translationFor('webform')?></button>
			</li>
			<li style="padding: 0 5px 15px 0; display: none;">
				<div class="material-switch pull-right">
					<input id="LeadPreview" name="LeadPreview" value="0" type="checkbox"/>
					<label for="LeadPreview" class="label-primary"></label>
				</div>
				<div style="font-weight: bold;"><?=$lh->translateText('LEAD PREVIEW')?></div>
			</li>
			<li style="font-size: 5px;">
				&nbsp;
			</li>
			<li id="toggleHotkeys" style="padding: 0 5px 15px;">
				<div class="material-switch pull-right">
					<input id="enableHotKeys" name="enableHotKeys" type="checkbox"/>
					<label for="enableHotKeys" class="label-primary"></label>
				</div>
				<div style="font-weight: bold;"><?=$lh->translateText('ENABLE HOT KEYS')?></div>
			</li>
			<li id="toggleMute" style="padding: 0 5px 15px;">
				<div class="material-switch pull-right">
					<input id="muteMicrophone" name="muteMicrophone" type="checkbox" checked/>
					<label for="muteMicrophone" class="label-primary"></label>
				</div>
				<div style="font-weight: bold;"><?=$lh->translateText('MICROPHONE')?></div>
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
		
        <ul class="control-sidebar-menu" id="go_agent_logout" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px; text-align: center;">
			<li>
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
      <div class="tab-pane" id="control-sidebar-agents-tab">
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
			<?php
			if ($user->userHasBasicPermission()) {
				echo '<li>
					<div class="text-center"><a href="" data-toggle="modal" id="change-password-toggle" data-target="#change-password-dialog-modal">'.$lh->translationFor("change_password").'</a></div>
					<div class="text-center"><a href="./messages.php">'.$lh->translationFor("messages").'</a></div>
					<div class="text-center"><a href="./notifications.php">'.$lh->translationFor("notifications").'</a></div>
					<div class="text-center"><a href="./tasks.php">'.$lh->translationFor("tasks").'</a></div>
				</li>';
			}
			?>
		</ul>
		
        <ul class="control-sidebar-menu" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px;">
			<li>
				<div class="center-block" style="text-align: center">
					<a href="./profile.php" class="btn btn-warning"><i class='fa fa-user'></i> <?=$lh->translationFor("my_profile")?></a>
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
		
		<script type="text/javascript">
			$(document).ready(function() {
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
								$.ajax({
									url: "./php/ModifyCustomer.php",
									type: 'POST',
									data: $("#name_form, #gender_form, #contact_details_form, #comment_form").serialize(),
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
				
				/**
				 * Deletes a customer
				 */
				 $("#modifyCustomerDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r === true) {
						var customerid = $(this).attr('href');
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
			});	
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
		</script>
    </body>
</html>
