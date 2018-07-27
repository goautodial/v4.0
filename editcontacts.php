<?php
/**
 * @file 		editcontacts.php
 * @brief 		Modify customer accounts
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja 
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

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

	$lead_id = $_POST['modifyid'];
	$output = $api->API_getLeadsInfo($lead_id);
	$list_id_ct = $output->data->list_id;

	if($output->result !== "success"){
		die($output->result);
	}

	if ($list_id_ct != NULL) {
		$first_name 	= $output->data->first_name;
		$middle_initial 	= $output->data->middle_initial;
		$last_name 	= $output->data->last_name;
		$email 	= $output->data->email;
		$phone_number 	= $output->data->phone_number;
		$alt_phone 	= $output->data->alt_phone;
		$address1 	= $output->data->address1;
		$address2 	= $output->data->address2;
		$address3 	= $output->data->address3;
		$city 	= $output->data->city;
		$state 	= $output->data->state;
		$country 	= $output->data->country_code;
		$postal_code	= $output->data->postal_code;
		$gender 	= $output->data->gender;
		$date_of_birth 	= $output->data->date_of_birth;
		$comments 	= $output->data->comments;
		$title 	= $output->data->title;
		$call_count 	= $output->data->call_count;
		$last_local_call_time 	= $output->data->last_local_call_time;
		$is_customer 	= $output->is_customer;
	}
	
	if (empty($is_customer) || is_null($is_customer)) {
		$is_customer = 0;
	}
	$fullname 			= $title.' '.$first_name.' '.$middle_initial.' '.$last_name;
	$date_of_birth 			= date('m/d/Y', strtotime($date_of_birth));
	$output_script = $ui->getAgentScript($lead_id, $fullname, $first_name, $last_name, $middle_initial, $email, $phone_number, $alt_phone, $address1, $address2, $address3, $city, $province, $state, $postal_code, $country);
	$disposition = $api->API_getAllDispositions();
	
	$avatarHash = md5( strtolower( trim( $user->getUserId() ) ) );
	$avatarURL50 = "https://www.gravatar.com/avatar/{$avatarHash}?rating=PG&size=50&default=wavatar";
	$avatarURL96 = "https://www.gravatar.com/avatar/{$avatarHash}?rating=PG&size=96&default=wavatar";
	$custDefaultAvatar = "https://www.gravatar.com/avatar/{$avatarHash}?rating=PG&size=96&default=mm";
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("contact_information"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
		
        <!-- Customized Style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />

		<!-- Datetime picker CSS --> 
		<link rel="stylesheet" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
		<!-- DateTime Picker JS -->
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

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
			.panel{
				margin-bottom:0;
			}
			
			.edit-profile-button{
				font-size:14px; 
				font-weight:normal;
			}
			#popup-hotkeys {
				position: absolute;
				top: 160px;
				left: 40px;
				display: none;
				box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
				min-width: 480px;
			}
		</style>
    </head>
    <?php print $ui->creamyBody(); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-heading">
					<!-- Page title -->
                    <?php $lh->translateText("contact_information"); ?>
                    <small class="ng-binding animated fadeInUpShort"></small>
                </section>

                <!-- Main content -->
                <section class="content">
				<!-- standard custom edition form -->
				<div class="container-custom ng-scope">
					<div class="card">
					<div class="card-heading bg-inverse">
							<div class="row">
								<div class="col-md-2 text-center visible-md visible-lg">
									<?php echo $ui->getVueAvatar($fullname, null, 96);?>
								</div>
								<div class="col-md-10">
									<div class="row">
										<div class="col-md-6">
											<h4><span id="heading_full_name"></span></h4>
											<small class="ng-binding animated fadeInUpShort"><?php echo $phone_number;?></small>
										</div>
										<p class="ng-binding animated fadeInUpShort"><span id="cust_number"></span></p>
									</div>
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
									<a href="#profile" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
										<span class="fa fa-user hidden"></span>
										<?=$lh->translationFor('contact_information')?></a>
								 </li>
								 <li role="presentation">
									<a href="#comments_tab" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
										<span class="fa fa-comments-o hidden"></span>
										<?=$lh->translationFor('comments')?></a>
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
								<?php
									//var_dump($output);
								?>
								<div id="activity" role="tabpanel" class="tab-pane">
									<div class="box collapsed-box">
										<div class="box-header with-border btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Show <?php $lh->translateText("lead_calls"); ?>">
										  <em class="glyphicon glyphicon-earphone pull-left"></em><h3 class="box-title pull-left"> <?php $lh->translateText("lead_calls"); ?></h3>
										</div>
										<div class="box-body table-responsive">
											<table class="table table-hover table-striped" id="lead_calls">
												<thead>
													<tr>
														<th textwrap><?php $lh->translateText("call_date"); ?></th>
														<th textwrap><?php $lh->translateText("length_in_sec"); ?></th>
														<th textwrap><?php $lh->translateText("status"); ?></th>
														<th textwrap><?php $lh->translateText("TSR"); ?></th>
														<th textwrap><?php $lh->translateText("campaign_id"); ?></th>
														<th textwrap><?php $lh->translateText("list_id"); ?></th>
														<th textwrap><?php $lh->translateText("term_reason"); ?></th>
														<th textwrap><?php $lh->translateText("phone_number"); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php
														for($i=0;$i < count($output->calls->call_date);$i++){
															echo '
																<tr>
																	<td textwrap><small>'.$output->calls->call_date[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->length_in_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->status[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->user[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->campaign_id[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->list_id[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->term_reason[$i].'</small></td>
																	<td textwrap><small>'.$output->calls->phone_number[$i].'</small></td>
																</tr>
															';
														}
													?>
												</tbody>
											</table>
										</div>
										<!-- /.box-body -->
									</div>
									<div class="box collapsed-box">
										<div class="box-header with-border btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Show <?php $lh->translateText("lead_closer_records"); ?>">
										  <em class="glyphicon glyphicon-list-alt pull-left"></em><h3 class="box-title pull-left"> <?php $lh->translateText("lead_closer_records"); ?></h3>
										</div>
										<div class="box-body table-responsive">
											<table class="table table-hover table-striped" id="lead_closer_records">
												<thead>
													<tr>
														<th textwrap><?php $lh->translateText("call_date"); ?></th>
														<th textwrap><?php $lh->translateText("length_in_sec"); ?></th>
														<th textwrap><?php $lh->translateText("status"); ?></th>
														<th textwrap><?php $lh->translateText("TSR"); ?></th>
														<th textwrap><?php $lh->translateText("campaign_id"); ?></th>
														<th textwrap><?php $lh->translateText("list_id"); ?></th>
														<th textwrap><?php $lh->translateText("queue_seconds"); ?></th>
														<th textwrap><?php $lh->translateText("term_reason"); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php
														for($i=0;$i < count($output->closerlog->call_date);$i++){
															echo '
																<tr>
																	<td textwrap><small>'.$output->closerlog->call_date[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->length_in_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->status[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->user[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->campaign_id[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->list_id[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->queue_seconds[$i].'</small></td>
																	<td textwrap><small>'.$output->closerlog->term_reason[$i].'</small></td>
																</tr>
															';
														}
													?>
												</tbody>
											</table>
										</div>
										<!-- /.box-body -->
									</div>
									<div class="box collapsed-box">
										<div class="box-header with-border btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Show <?php $lh->translateText("lead_agent_log"); ?>">
										  <em class="glyphicon glyphicon-user pull-left"></em><h3 class="box-title pull-left"> <?php $lh->translateText("lead_agent_log"); ?></h3>
										</div>
										<div class="box-body table-responsive">
											<table class="table table-hover table-striped" id="lead_agent_log">
												<thead>
													<tr>
														<th textwrap><?php $lh->translateText("event_time"); ?></th>
														<th textwrap><?php $lh->translateText("campaign_id"); ?></th>
														<th textwrap><?php $lh->translateText("agent_log_id"); ?></th>
														<th textwrap><?php $lh->translateText("pause_sec"); ?></th>
														<th textwrap><?php $lh->translateText("wait_sec"); ?></th>
														<th textwrap><?php $lh->translateText("talk_sec"); ?></th>
														<th textwrap><?php $lh->translateText("dispo_sec"); ?></th>
														<th textwrap><?php $lh->translateText("status"); ?></th>
														<th textwrap><?php $lh->translateText("user_group"); ?></th>
														<th textwrap><?php $lh->translateText("sub_status"); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php
														for($i=0;$i < count($output->agentlog->campaign_id);$i++){
															echo '
																<tr>
																	<td textwrap><small>'.$output->agentlog->event_time[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->campaign_id[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->agent_log_id[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->pause_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->wait_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->talk_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->dispo_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->status[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->user_group[$i].'</small></td>
																	<td textwrap><small>'.$output->agentlog->sub_status[$i].'</small></td>
																</tr>
															';
														}
													?>
												</tbody>
											</table>
										</div>
										<!-- /.box-body -->
									</div>
									<div class="box collapsed-box">
										<div class="box-header with-border btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Show <?php $lh->translateText("lead_recordings"); ?>">
										  <em class="glyphicon glyphicon-play-circle pull-left"></em><h3 class="box-title pull-left"> <?php $lh->translateText("lead_recordings"); ?></h3>
										</div>
										<div class="box-body table-responsive">
											<table class="table table-hover table-striped" id="lead_recordings">
												<thead>
													<tr>
														<th textwrap><?php $lh->translateText("start_time"); ?></th>
														<th textwrap><?php $lh->translateText("length_in_sec"); ?></th>
														<th textwrap><?php $lh->translateText("recording_id"); ?></th>
														<th textwrap><?php $lh->translateText("filename"); ?></th>
														<th textwrap><?php $lh->translateText("location"); ?></th>
														<th textwrap><?php $lh->translateText("TSR"); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php
														for($i=0;$i < count($output->record->recording_id);$i++){
															echo '
																<tr>
																	<td textwrap><small>'.$output->record->start_time[$i].'</small></td>
																	<td textwrap><small>'.$output->record->length_in_sec[$i].'</small></td>
																	<td textwrap><small>'.$output->record->recording_id[$i].'</small></td>
																	<td textwrap><small>'.$output->record->filename[$i].'</small></td>
																	<td><a href="'.$output->record->location[$i].'" ><small>'.$output->record->location[$i].'</small></a></td>
																	<td textwrap><small>'.$output->record->user[$i].'</small></td>
																</tr>
															';
														}
													?>
												</tbody>
											</table>
										</div>
										<!-- /.box-body -->
									</div>
								</div>
								
								<div id="profile" role="tabpanel" class="tab-pane active">
									<fieldset>
										<h4><a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile">Edit Information</a></h4>
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
										
										<div class="row">
											<div class="col-sm-4">
												<div class="mda-form-group label-floating">
													<input id="first_name" name="first_name" type="text" maxlength="30"  value="<?php echo $first_name;?>"
														class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched" required>
													<label for="first_name"><?php $lh->translateText("first_name"); ?></label>
												</div>
												<label id="first_name-error" class="error hide" for="first_name">This field is required.</label>
											</div>
											<div class="col-sm-4">
												<div class="mda-form-group label-floating">
													<input id="middle_initial" name="middle_initial" type="text" maxlength="2" value="<?php echo $middle_initial;?>"
														class="mda-form-control ng-pristine ng-empty ng-invalid ng-touched">
													<label for="middle_initial"><?php $lh->translateText("middle_initial"); ?></label>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="mda-form-group label-floating">
													<input id="last_name" name="last_name" type="text" maxlength="30" value="<?php echo $last_name;?>"
														class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched" required>
													<label for="last_name"><?php $lh->translateText("last_name"); ?></label>
												</div>
												<label id="last_name-error" class="error hide" for="last_name">This field is required.</label>
											</div>
										</div>
										</form>
										
										<form id="contact_details_form" class="formMain">
											<!-- phone number & alternative phone number -->
											<div class="row">
												<div class="col-sm-6">
													<div class="mda-form-group label-floating">
														<span id="phone_numberDISP" class="hidden"></span>
														<input id="phone_code" name="phone_code" type="hidden" value="<?php echo $phone_code;?>">
														<input id="phone_number" name="phone_number" type="number" min="0" width="auto" value="<?php echo $phone_number;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched" required>
														<label for="phone_number"><?php $lh->translateText("phone_number"); ?></label>
														<!--
														<span class="mda-input-group-addon">
															<em class="fa fa-phone fa-lg"></em>
														</span>-->
													</div>
												</div>
												<div class="col-sm-6">
													<div class="mda-form-group label-floating">
														<input id="alt_phone" name="alt_phone" type="number" min="0" width="100" value="<?php echo $alt_phone;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
														<label for="alt_phone"><?php $lh->translateText("alternative_phone_number"); ?></label>
													</div>
												</div>
											</div>
											<!-- /.phonenumber & alt phonenumber -->
											
											<div class="mda-form-group label-floating">
												<input id="address1" name="address1" type="text" width="auto" value="<?php echo $address1;?>"
													class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
												<label for="address1"><?php $lh->translateText("address"); ?></label> 
												<!--<span class="mda-input-group-addon">
													<em class="fa fa-home fa-lg"></em>
												</span>-->
											</div>
											
											<div class="mda-form-group label-floating">
												<input id="address2" name="address2" type="text" value="<?php echo $address2;?>"
													class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
												<label for="address2"><?php $lh->translateText("address2"); ?></label>
											</div>

											<input type="hidden" name="address3" value="<?php echo $address3;?>">
											
											<div class="row">
												<div class="col-sm-4">
													<div class="mda-form-group label-floating">
														<input id="city" name="city" type="text" value="<?php echo $city;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
														<label for="city"><?php $lh->translateText("city"); ?></label>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="mda-form-group label-floating">
														<input id="state" name="state" type="text" value="<?php echo $state;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
														<label for="state"><?php $lh->translateText("state"); ?></label>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="mda-form-group label-floating">
														<input id="postal_code" name="postal_code" type="text" value="<?php echo $postal_code;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
														<label for="postal_code"><?php $lh->translateText("postal_code"); ?></label>
													</div>
												</div>
											</div><!-- /.city,state,postalcode -->
										
											<div class="mda-form-group label-floating">
												<input id="country" name="country" type="text" value="<?php echo $country;?>"
													class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
												<label for="country"><?php $lh->translateText("country"); ?></label>
											</div>
											<div class="mda-form-group label-floating"><!-- add "mda-input-group" if with image -->
												<input id="email" name="email" type="text" width="auto" value="<?php echo $email;?>"
													class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
												<label for="email"><?php $lh->translateText("email_add"); ?></label>
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
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
														<label for="title"><?php $lh->translateText("title"); ?></label>
													</div>
												</div>
												<div class="col-sm-3">
													<div class="mda-form-group label-floating">
														<select id="gender" name="gender" value="<?php echo $gender;?>"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select">
															<?php 
																if($gender == "M"){
															?>
																<option selected value="M"><?php $lh->translateText("male"); ?></option>
																<option value="F"><?php $lh->translateText("female"); ?></option>
															<?php
																}else
																if($gender == "F"){
															?>
																<option selected value="F"><?php $lh->translateText("female"); ?></option>
																<option value="M"><?php $lh->translateText("male"); ?></option>
															<?php
																}else{
															?>
																<option selected disabled value=""></option>
																<option value="M"><?php $lh->translateText("male"); ?></option>
																<option value="F"><?php $lh->translateText("female"); ?></option>
															<?php
																}
															?>
														</select>
														<label for="gender"><?php $lh->translateText("gender"); ?></label>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="mda-form-group label-floating">
														<input type="text" id="date_of_birth" value="<?php echo $date_of_birth;?>" name="date_of_birth"
															class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">
														<label for="date_of_birth"><?php $lh->translateText("date_of_birth"); ?></label>
													</div>
												</div>									
											</div><!-- /.gender & title -->
											
											<div class="mda-form-group label-floating">
												<select name="dispo" id="dispo" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select" >
													<option value=""><?php $lh->translateText("-none-"); ?></option>
													<?php
														for($a=0; $a<count($disposition->status); $a++){
													?>
														<option value="<?php echo $disposition->status[$a];?>" <?php if($disposition->status[$a] === $output->data-> status) echo "selected";?>><?php echo $disposition->status[$a].' - '.$disposition->status_name[$a];?></option>
													<?php
														}
													?>
												</select>
												<label for="gender"><?php $lh->translateText("disposition"); ?></label>
											</div><!-- /.dispo -->
										</form>
									<br/>

								   </fieldset>
								   <!-- FOOTER BUTTONS -->
									<fieldset class="footer-buttons">
										<div style="display: inline-block; width: 220px; padding-right: 70px;">
											<div class="material-switch pull-right" style="margin-left: 20px;">
												<input id="convert-customer" name="convert-customer" value="0" type="checkbox"/>
												<label for="convert-customer" class="label-primary" style="width: 0px;"></label>
											</div>
											<div style="font-weight: bold;"><?php $lh->translateText("convert_to_customer"); ?></div>
										</div>
									   <div class="col-sm-4 pull-right">
											<a href="crm.php" type="button" class="btn btn-danger" id="cancel"><i class="fa fa-close"></i> <?php $lh->translateText('cancel'); ?> </a>
											<button type="submit" class="btn btn-primary" name="submit" id="submit_edit_form"> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText('update'); ?> </span></button>
									   </div>
									</fieldset>
								</div><!--End of Profile-->
									
									<div id="comments_tab" role="tabpanel" class="tab-pane">
										<div class="row">
											<div class="col-sm-12">
												<h4>
													<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile"><?php $lh->translateText('edit_information'); ?></a>
												</h4>
												<form role="form" id="comment_form" class="formMain form-inline" >
													<div class="mda-form-group label-floating" style="float: left; width:100%;">
														<textarea rows="5" id="comments" name="comments" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea" style="resize:none; width: 100%;" ><?php echo $comments;?></textarea>
														<label for="comments"><?php $lh->translateText('comments'); ?></label>
													</div>
													<div style="clear:both;"></div>
													<br>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>

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

				<!-- WEBFORM MODAL -->
						<div class="modal fade" id="webform" name="webform" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<br/>
									<center><h2>Ok!</h2></center>
									<br/>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->

					</div>
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

	</div><!-- ./wrapper -->

        <!-- call for standardized JS -->
        <?php print $ui->standardizedThemeJS();?>

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#lead_calls').dataTable();
				$('#lead_closer_records').dataTable();
				$('#lead_agent_log').dataTable();
				$('#lead_recordings').dataTable();
				
				var is_customer = <?php echo $is_customer; ?>;
				if (is_customer > 0) {
					$('#convert-customer').prop('checked', true);
					$('#convert-customer').val("1");
					$('#convert-customer').prop('disabled', true);
				}
				
				$('#heading_full_name').text("<?php echo $fullname;?>");
				$('#heading_lead_id').text("<?php echo $lead_id;?>");
				$('#comments').text("<?php echo $comments;?>");

				$('#date_of_birth').datetimepicker({ //start date contacts
				icons: {
						//time: 'fa fa-clock-o',
						date: 'fa fa-calendar',
						up: 'fa fa-chevron-up',
						down: 'fa fa-chevron-down',
						previous: 'fa fa-chevron-left',
						next: 'fa fa-chevron-right',
						today: 'fa fa-crosshairs',
						clear: 'fa fa-trash'
					},
				format: 'MM/DD/YYYY'					
				});				

				$("#submit_edit_form").click(function(){
				//alert("User Created!");
					$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#submit_edit_form').prop("disabled", true);
					
					var validate = 0;
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';

					if($('#name_form')[0].checkValidity()) {
					    if($('#gender_form')[0].checkValidity()) {
							if($('#contact_details_form')[0].checkValidity()) {
								var postData = $("#name_form, #gender_form, #contact_details_form, #comment_form").serialize() + '&is_customer=' + $('#convert-customer').is(':checked') + '&user_id=' + <?php echo $user->getUserId(); ?> + '&log_user=' + log_user + '&log_group=' + log_group;
								$.ajax({
									url: "./php/ModifyContact.php",
									type: 'POST',
									data: postData,
									success: function(data) {
									  console.log(data);
									  $('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("update"); ?>");
									  $('#submit_edit_form').prop("disabled", false);
										if(data == 1){
											swal({title: "<?php $lh->translateText("success"); ?>",text: "<?php $lh->translateText("contact_update_success"); ?>",type: "success"},function(){location.reload();});
										}else{
											sweetAlert("<?php $lh->translateText("oops"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>", "error");
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
						sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("incomplete"); ?>", "error");
						validate = 0;
						$("#first_name-error").removeClass("hide");
						$("#last_name-error").removeClass("hide");
						$("#first_name").addClass("error");
						$("#last_name").addClass("error");
						$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("update"); ?>");
						$('#submit_edit_form').prop("disabled", false);
					}else{
						$("#first_name-error").addClass("hide");
						$("#last_name-error").addClass("hide");
						$("#first_name").removeClass("error");
						$("#last_name").removeClass("error");
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
		<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
		<?php print $ui->creamyFooter();?>
    </body>
</html>
