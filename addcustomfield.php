<?php

	###################################################
	### Name: edittelephonylist.php 				###
	### Functions: Edit List Details 		  		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
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

$modifyid = NULL;
if (isset($_POST["modifyid"])) {
	$modifyid = $_POST["modifyid"];
}

$customFields = $ui->API_goGetAllCustomFields($modifyid);
$customs = $customFields->data;

// echo "<pre>";
// print_r($customs);
// die;

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit List</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />

				<!-- SELECT2-->
				<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
				<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">

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

        <!-- SWEETALERT-->
   		<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
		<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>

        	<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

				<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
				<!-- Data Tables -->
		        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
		        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

				<!-- SELECT2-->
	   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>
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

			fieldset .form-group {
			    margin-bottom: 15px;
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
                        <?php $lh->translateText("lists"); ?>
                        <small>Add Custom Fields</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
												<li><a href="./telephonylist.php"><?php $lh->translateText("lists"); ?></a></li>
												<li><a href="#" class="edit-list" data-id="<?php echo $modifyid; ?>"><?php $lh->translateText("modify"); ?></a></li>
                        <li class="active">Custom Fields</li>
                    </ol>
                </section>

            <!-- Main content -->
            <section class="content" style="padding-bottom: 60px;">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title col-lg-3">
									LIST ID : <u><?php echo $modifyid;?></u>
								</h3>
								<div class="col-lg-9">
									<div class="row">
										<div class="col-lg-1 pull-right">
											<button type="button" class="btn-delete-cf btn btn-danger" style="height: 35px;" data-id="<?php echo $modifyid; ?>"><i class="fa fa-trash"></i></button>
										</div>
										<div class="col-lg-1 pull-right">
											<button type="button" class="btn-field btn btn-success" style="height: 35px;" data-id="<?php echo $modifyid; ?>"><i class="fa fa-plus"></i></button>
										</div>
										<div class="col-lg-1 pull-right">
											<button type="button" class="btn-edit-fields btn btn-info" style="height: 35px;" data-id="<?php echo $modifyid; ?>"><i class="fa fa-pencil"></i></button>
										</div>
										<!-- <div class="col-lg-4 pull-right">
											<select class="select2-3 form-control custom-fields-selection" style="width:100%;">
												<option value="TEXT">TEXT</option>
												<option value="AREA">AREA</option>
												<option value="SELECT">SELECT</option>
												<option value="MULTI">MULTI</option>
												<option value="RADIO">RADIO</option>
												<option value="CHECKBOX">CHECKBOX</option>
												<option value="DATE">DATE</option>
												<option value="TIME">TIME</option>
												<option value="DISPLAY">DISPLAY</option>
												<option value="SCRIPT">SCRIPT</option>
											</select>
										</div> -->
									</div>
								</div>
							</div>
							<!-- /.box-header -->
							<!-- form start -->
							<form role="form">
								<div class="box-body">
									<div class="form-horizontal">
										<div class="row">
											<!-- <div class="col-lg-3">
												<div class="list-group">
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="TEXT"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add TEXT</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="AREA"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add AREA</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="SELECT"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add SELECT</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="MULTI"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add MULTI</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="RADIO"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add RADIO</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="CHECKBOX"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add CHECKBOX</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="DATE"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add DATE</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="TIME"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add TIME</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="DISPLAY"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add DISPLAY</button>
													<button type="button" class="btn-field list-group-item list-group-item-action" data-type="SCRIPT"><span class="fa fa-plus"></span>&nbsp;&nbsp;&nbsp;Add SCRIPT</button>
												</div>
											</div> -->
											<div class="col-lg-12">
												<h4 style="margin-top: 0px; border-bottom: 1px solid #777;">Form Preview</h4>
												<?php

													if(count($customs) > 0) {
														$viewall .= "<form>\n";
														$viewall .= "<TABLE class=\"table\" style=\"rgb(208,208,208);\">";
														$last_field_rank=0;
														$o=0;

														foreach($customs as $fieldsvalues){
															$A_field_id 					= $fieldsvalues->field_id;
															$A_field_label 				= $fieldsvalues->field_label;
															$A_field_name 				= $fieldsvalues->field_name;
															$A_field_description 	= $fieldsvalues->field_description;
															$A_field_rank 				=	$fieldsvalues->field_rank;
															$A_field_help 				=	$fieldsvalues->field_help;
															$A_field_type 				=	$fieldsvalues->field_type;
															$A_field_options 			= $fieldsvalues->field_options;
															$A_field_size 				=	$fieldsvalues->field_size;
															$A_field_max 					= $fieldsvalues->field_max;
															$A_field_default 			= $fieldsvalues->field_default;
															$A_field_cost 				=	$fieldsvalues->field_cost;
															$A_field_required 		= $fieldsvalues->field_required;
															$A_multi_position 		=	$fieldsvalues->multi_position;
															$A_name_position 			= $fieldsvalues->name_position;
															$A_field_order 				= $fieldsvalues->field_order;

															if ($last_field_rank=="$A_field_rank") {
																$viewall .= " &nbsp; &nbsp; &nbsp; &nbsp; ";
															} else {
																$viewall .= "</td></tr>\n";
																$viewall .= "<tr style='background-color:#FBFBFB; font-weight:normal; padding:1%;'><td align=";

																if ($A_name_position=='TOP') {
																	$viewall .= "left colspan=2";
																} else {
																	$viewall .= "right";
																}
																	$viewall .= "><font size=2>";
															}

															$viewall .= "<B>$A_field_name</B>";

															if ($A_name_position=='TOP'&& $helpcount>0 ) {
																if (strlen($A_field_help)<1){
																	$helpHTML .= '';
																}
																$viewall .= " &nbsp; <span style='position:static; font-weight:normal;'  id=P_HELP_$A_field_label></span><span style=\"position:static;background-color:#FBFBFB;\" id=HELP_$A_field_label> &nbsp; $helpHTML</span><BR>";
															} else {
																if ($last_field_rank=="$A_field_rank") {
																	$viewall .= " &nbsp;";
																} else {
																	$viewall .= "</td><td  style='background-color:#FBFBFB; padding:1%; ' align=left><font size=2>";
																}
															}

															$field_HTML='';

															if ($A_field_type=='SELECT') {
																$field_HTML .= "<select size=1 name=$A_field_label id=$A_field_label>\n";
															}

															if ($A_field_type=='MULTI'){
																$field_HTML .= "<select MULTIPLE size=$A_field_size name=$A_field_label id=$A_field_label>\n";
															}

															if ( ($A_field_type=='SELECT') or ($A_field_type=='MULTI') or ($A_field_type=='RADIO') or ($A_field_type=='CHECKBOX') )
															{
																$field_options_array = explode("\n",$A_field_options);

																$field_options_count = count($field_options_array);
																$te=0;
																while ($te < $field_options_count)
																{
																	if (preg_match("/,/",$field_options_array[$te])) {
																		$field_selected='';
																		$field_options_value_array = explode(",",$field_options_array[$te]);

																		if ( ($A_field_type=='SELECT') or ($A_field_type=='MULTI') )
																		{

																			if ($A_field_default == "$field_options_value_array[0]") {$field_selected = 'SELECTED';}
																			$field_HTML .= "<option value=\"$field_options_value_array[0]\" $field_selected>$field_options_value_array[0]</option>\n";
																		}

																		if ( ($A_field_type=='RADIO') or ($A_field_type=='CHECKBOX') )
																		{

																			if ($A_multi_position=='VERTICAL')
																			{
																				$field_HTML .= " &nbsp; ";
																			}

																			if ($A_field_default == "$field_options_value_array[0]") {$field_selected = 'CHECKED';}

																			$lblname = $A_field_label.'[]';

																			$field_HTML .= "<input type=$A_field_type name=$lblname id=$lblname value=\"$field_options_value_array[0]\" $field_selected> $field_options_value_array[1]\n";


																			if ($A_multi_position=='VERTICAL')
																			{
																				$field_HTML .= "<BR>\n";
																			}
																		}
																	}
																	$te++;
																}
															}

															if ( ($A_field_type=='SELECT') or ($A_field_type=='MULTI') )
															{
																$field_HTML .= "</select>\n";
															}
															if ($A_field_type=='TEXT')
															{
																if ($A_field_default=='NULL')
																{
																	$A_field_default='';
																}
																$field_HTML .= "<input type=text size=$A_field_size maxlength=$A_field_max name=$A_field_label id=$A_field_label value=\"$A_field_default\">\n";
															}
															if ($A_field_type=='AREA')
															{
																$field_HTML .= "<textarea name=$A_field_label id=$A_field_label ROWS=$A_field_max COLS=$A_field_size></textarea>";
															}
															if ($A_field_type=='DISPLAY')
															{
																if ($A_field_default=='NULL')
																{
																	$A_field_default='';
																}
																$field_HTML .= "$A_field_default\n";
															}
															if ($A_field_type=='SCRIPT')
															{
																if ($A_field_default=='NULL')
																{
																	$A_field_default='';
																}
																$field_HTML .= "$A_field_options\n";
															}
															if ($A_field_type=='DATE')
															{
																if ( (strlen($A_field_default)<1) or ($A_field_default=='NULL') ) {$A_field_default=0;}
																	$day_diff = $A_field_default;
																	$default_date = date("Y-m-d", mktime(date("H"),date("i"),date("s"),date("m"),date("d")+$day_diff,date("Y")));
																	$field_HTML .= "<input type=text size=11 maxlength=10 name=$A_field_label id=$A_field_label value=\"$default_date\">\n";
																	/*$field_HTML .= "<script language=\"JavaScript\">\n";
																	$field_HTML .= "var o_cal = new tcal ({\n";
																	$field_HTML .= "	'formname': 'form_custom_$listid',\n";
																	$field_HTML .= "	'controlname': '$A_field_label'});\n";
																	$field_HTML .= "o_cal.a_tpl.yearscroll = false;\n";
																	$field_HTML .= "</script>\n";*/
																	$baseurl = base_url();
																	$urlcalendar = $baseurl.'js/images/cal.gif';
																	$field_HTML .= "<img id=\"$A_field_label\" name=\"$A_field_label\" src=\"$urlcalendar\">";
															}

															if ($A_field_type=='TIME')
															{
																$minute_diff = $A_field_default;
																$default_time = date("H:i:s", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
																$default_hour = date("H", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
																$default_minute = date("i", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
																$field_HTML .= "<input type=hidden name=$A_field_label id=$A_field_label value=\"$default_time\">";
																$field_HTML .= "<SELECT name=HOUR_$A_field_label id=HOUR_$A_field_label>";
																$field_HTML .= "<option>00</option>";
																$field_HTML .= "<option>01</option>";
																$field_HTML .= "<option>02</option>";
																$field_HTML .= "<option>03</option>";
																$field_HTML .= "<option>04</option>";
																$field_HTML .= "<option>05</option>";
																$field_HTML .= "<option>06</option>";
																$field_HTML .= "<option>07</option>";
																$field_HTML .= "<option>08</option>";
																$field_HTML .= "<option>09</option>";
																$field_HTML .= "<option>10</option>";
																$field_HTML .= "<option>11</option>";
																$field_HTML .= "<option>12</option>";
																$field_HTML .= "<option>13</option>";
																$field_HTML .= "<option>14</option>";
																$field_HTML .= "<option>15</option>";
																$field_HTML .= "<option>16</option>";
																$field_HTML .= "<option>17</option>";
																$field_HTML .= "<option>18</option>";
																$field_HTML .= "<option>19</option>";
																$field_HTML .= "<option>20</option>";
																$field_HTML .= "<option>21</option>";
																$field_HTML .= "<option>22</option>";
																$field_HTML .= "<option>23</option>";
																$field_HTML .= "<OPTION value=\"$default_hour\" selected>$default_hour</OPTION>";
																$field_HTML .= "</SELECT>";
																$field_HTML .= "<SELECT name=MINUTE_$A_field_label id=MINUTE_$A_field_label>";
																$field_HTML .= "<option>00</option>";
																$field_HTML .= "<option>05</option>";
																$field_HTML .= "<option>10</option>";
																$field_HTML .= "<option>15</option>";
																$field_HTML .= "<option>20</option>";
																$field_HTML .= "<option>25</option>";
																$field_HTML .= "<option>30</option>";
																$field_HTML .= "<option>35</option>";
																$field_HTML .= "<option>40</option>";
																$field_HTML .= "<option>45</option>";
																$field_HTML .= "<option>50</option>";
																$field_HTML .= "<option>55</option>";
																$field_HTML .= "<OPTION value=\"$default_minute\" selected>$default_minute</OPTION>";
																$field_HTML .= "</SELECT>";
															}
															if ($A_name_position=='LEFT') {
																$helpHTML = "help+";
																if (strlen($A_field_help)<1) {
																	$helpHTML = '';
																}
																$viewall .= " $field_HTML <span style=\"position:static;\" id=P_HELP_$A_field_label></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label> &nbsp; $helpHTML</span>";
															} else {
																$viewall.= " $field_HTML\n";
															}
															$last_field_rank=$A_field_rank;
															$o++;
														}
														$viewall .= "</td></tr></table></form>\n";
														echo $viewall;
													}
												?>
											</div>
										</div>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<div class="row">
										<div class="pull-right">
											<div class="col-sm-12">
												<a href="telephonylist.php" type="button" class="btn btn-warning edit-list" data-id="<?php echo $modifyid; ?>"><i class="fa fa-reply"></i> Go Back </a>
												<!-- <button type="button" class="btn btn-primary" data-id="<?php echo $modifyid; ?>"> <span><i class="fa fa-check"></i> Save</span></button> -->
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
            </section>

				<!-- /.content -->
            </aside><!-- /.right-side -->

			<?php print $ui->creamyFooter(); ?>

        </div><!-- ./wrapper -->

				<!-- Custom Field Modal -->
				<div id="modal_custom_field" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Custom Field Wizard</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal wizard-form" style="margin-top: 10px;">
									<div class="form-group">
										<label class="control-label col-lg-3">List ID:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control list-id" name="list_id" value="<?php echo $modifyid; ?>" readonly>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Label:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-label" name="label" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Rank:</label>
										<div class="col-lg-3">
											<input type="text" class="form-control rank" name="rank" value="">
										</div>
										<label class="control-label col-lg-2">Order:</label>
										<div class="col-lg-3">
											<input type="text" class="form-control order" name="order" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Name:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control name" name="name" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Position:</label>
										<div class="col-lg-9">
											<select class="form-control position" name="position">
												<option value=""></option>
												<option value="TOP">TOP</option>
												<option value="LEFT">LEFT</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Description:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control description" name="description" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Type:</label>
										<div class="col-lg-9">
											<!-- <input type="text" class="form-control type" name="type" value="" readonly> -->
											<select class="select2-3 form-control type" style="width:100%;">
												<option value="TEXT">TEXT</option>
												<option value="AREA">AREA</option>
												<option value="SELECT">SELECT</option>
												<option value="MULTI">MULTI</option>
												<option value="RADIO">RADIO</option>
												<option value="CHECKBOX">CHECKBOX</option>
												<option value="DATE">DATE</option>
												<option value="TIME">TIME</option>
												<option value="DISPLAY">DISPLAY</option>
												<option value="SCRIPT">SCRIPT</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Options:</label>
										<div class="col-lg-9">
											<textarea class="form-control options" style="resize: none;" name="options"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Option Position:</label>
										<div class="col-lg-9">
											<select class="form-control option-position" name="option_position">
												<option value=""></option>
												<option value="HORIZONTAL">HORIZONTAL</option>
												<option value="VERTICAL">VERTICAL</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Field Size:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-size" name="field_size" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Field Max:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-max" name="field_max" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Field Default:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-default" name="field_default" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Field Required:</label>
										<div class="col-lg-9">
											<select class="form-control field-required" name="field_required">
												<option value="YES">YES</option>
												<option value="NO" selected>NO</option>
											</select>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary btn-create-field">Create Field</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<div id="modal_custom_field_list" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document" style="width: 70%;">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">List of Custom Fields for List ID: <?php echo $modifyid; ?></h4>
							</div>
							<div class="modal-body">
								<div class="box-body">
									<div class="table-responsive">
										<table id="custom_fields_table" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
											<thead>
												<tr>
													<th>Rank</th>
													<th>Label</th>
													<th>Name</th>
													<th>Type</th>
													<th style="width: 20%;">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
												foreach($customs as $fieldsvalues){
													$A_field_id 					= $fieldsvalues->field_id;
													$A_field_label 				= $fieldsvalues->field_label;
													$A_field_name 				= $fieldsvalues->field_name;
													$A_field_description 	= $fieldsvalues->field_description;
													$A_field_rank 				=	$fieldsvalues->field_rank;
													$A_field_help 				=	$fieldsvalues->field_help;
													$A_field_type 				=	$fieldsvalues->field_type;
													$A_field_options 			= $fieldsvalues->field_options;
													$A_field_size 				=	$fieldsvalues->field_size;
													$A_field_max 					= $fieldsvalues->field_max;
													$A_field_default 			= $fieldsvalues->field_default;
													$A_field_cost 				=	$fieldsvalues->field_cost;
													$A_field_required 		= $fieldsvalues->field_required;
													$A_multi_position 		=	$fieldsvalues->multi_position;
													$A_name_position 			= $fieldsvalues->name_position;
													$A_field_order 				= $fieldsvalues->field_order;
												?>
												<tr>
													<td><?php echo $A_field_rank." - ".$A_field_order;?></td>
													<td><?php echo $A_field_label; ?></td>
													<td><?php echo $A_field_name; ?></td>
													<td><?php echo $A_field_type; ?></td>
													<td style="width: 20%;">
														<button type="button" class="btn btn-primary"><span class="fa fa-pencil"></span></button>
														<button type="button" class="btn btn-danger"><span class="fa fa-trash"></span></button>
														<button type="button" class="btn btn-info"><span class="fa fa-eye"></span></button>
													</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- end of modal -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {
				$('.select2-3').select2({
		        theme: 'bootstrap'
		    });

				$('#custom_fields_table').dataTable({
					columnDefs: [
	            { width: '20%', targets: 0 }
	        ],
				});

				$(document).on('click','.edit-list',function() {
					var url = './edittelephonylist.php';
					var id = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

				$(document).on('click', '.btn-field', function(){
					var type = $('.custom-fields-selection').val();
					$('.type').val(type);
					$('#modal_custom_field').modal('show');
				});

				$(document).on('click', '.btn-edit-fields', function(){
					$('#modal_custom_field_list').modal('show');
				});

				$('#modal_custom_field').on('hidden.bs.modal', function () {
					$('.field-label').val("");
					$('.rank').val("");
					$('.order').val("");
					$('.name').val("");
					$('.description').val("");
					$('.type').val("");
					$('.options').val("");
					$('.field-size').val("");
					$('.field-max').val("");
					$('.field-default').val("");
					$('.position').val("").change();
					$('.option-position').val("").change();
					$('.field-required').val("NO").change();
				});

				$('.btn-create-field').click(function(){
					var formdata = $('.wizard-form').serializeFormJSON();
					var label 					= $('.field-label').val();
					var rank 						= $('.rank').val();
					var order 					= $('.order').val();
					var name 						= $('.name').val();
					var description 		= $('.description').val();
					var type 						= $('.type').val();
					var options 				= $('.options').val();
					var field_size 			= $('.field-size').val();
					var field_max 			= $('.field-max').val();
					var field_default 	= $('.field-default').val();
					var position 				= $('.position').val();
					var option_position = $('.option-position').val();
					var field_required 	= $('.field-required').val();

					// console.log(label);
					// console.log(rank);
					// console.log(order);
					// console.log(name);
					// console.log(description);
					// console.log(type);
					// console.log(options);
					// console.log(field_size);
					// console.log(field_max);
					// console.log(field_default);
					// console.log(position);
					// console.log(option_position);
					// console.log(field_required);

					console.log(formdata);
				});

				$(document).on('click', '.btn-add-field', function(){
					var row = $('div.to-copy').clone().removeClass('to-copy').addClass('cloned-row');
					var delete_button = '<span class="delete-row fa fa-trash fa-lg text-red" style="padding: 10px 20px;"></span>';
							row.find('div.btn-action').html(delete_button);

					$('div.form-custom-field').append(row);
				});

				$(document).on('click','.delete-row',function(){
            $(this).closest('div.cloned-row').remove();
        });
			});

			(function ($) {
        $.fn.serializeFormJSON = function () {

            var o = {};
            var a = this.serializeArray();
            $.each(a, function () {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || " ");
                } else {
                    o[this.name] = this.value || " ";
                }
            });
            return o;
        };
    })(jQuery);
		</script>

    </body>
</html>
