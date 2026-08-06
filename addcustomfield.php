<?php
/**
 * @file        addcustomfield.php
 * @brief       Handles Add Custom Field Request
 * @copyright 	Copyright (c) 2025 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho <demian@goautodial.com>*
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	// initialize structures
	$api = \creamy\APIHandler::getInstance();
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

	$modifyid = NULL;
	if (isset($_POST["modifyid"])) {
		$modifyid = ($_POST["modifyid"] ?? '');
	}

	$customFields = $api->API_getAllCustomFields($modifyid);
	$customs = $customFields->data;

	//echo "<pre>";
	//print_r($customs);
	// die;

	$perm = $ui->goGetPermissions('customfields', $_SESSION['usergroup']);
	if ($perm->customfields_read === 'N' && $perm->customfields_update === 'N' && $perm->customfields_delete === 'N') {
		header("location: telephonylist.php");
	}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit List</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php print $ui->standardizedThemeCSS();?>
        <?php print $ui->creamyThemeCSS(); ?>

				<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
				<!-- Data Tables -->
		        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
		        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		        <!-- SELECT2-->
   		<link rel="stylesheet" src="js/dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" src="js/dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
				<!-- SELECT2-->
	   		<script src="js/dashboard/select2/dist/js/select2.js"></script>
        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
		</script>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}

			fieldset .form-group {
			    margin-bottom: 15px;
			}

			#modal_custom_field .modal-dialog {
				width: 640px;
				max-width: calc(100% - 30px);
			}

			#modal_custom_field .modal-body {
				padding: 18px 28px;
			}

			#modal_custom_field .form-group {
				margin-bottom: 12px;
			}

			#modal_custom_field .control-label {
				position: relative;
				padding-top: 7px;
				padding-right: 18px;
				font-weight: 600;
				white-space: nowrap;
			}

			#modal_custom_field .control-label .text-danger {
				position: absolute;
				right: 5px;
				width: 8px;
				text-align: left;
			}

			#modal_custom_field .custom-field-help {
				font-size: 12px;
			}

			#modal_custom_field .custom-field-help-text {
				margin: 4px 0 0;
				font-size: 11px;
				line-height: 1.35;
				color: #777;
			}

			#modal_custom_field .custom-field-validation-message {
				padding: 10px 12px;
				font-size: 13px;
			}

			#modal_custom_field .type-dependent-muted {
				display: none;
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
										<div class="col-lg-1 pull-right<?=($perm->customfields_delete === 'N' ? ' hidden' : '')?>">
											<button type="button" class="btn-delete-all-cf btn btn-danger" style="height: 35px;" data-id="<?php echo $modifyid; ?>"><i class="fa fa-trash"></i></button>
										</div>
										<div class="col-lg-1 pull-right<?=($perm->customfields_create === 'N' ? ' hidden' : '')?>">
											<button type="button" class="btn-field btn btn-success" style="height: 35px;" data-id="<?php echo $modifyid; ?>"><i class="fa fa-plus"></i></button>
										</div>
										<div class="col-lg-1 pull-right<?=($perm->customfields_update === 'N' ? ' hidden' : '')?>">
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
							<!-- <form role="form"> -->
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

													$viewall = '';
													$helpHTML = '';
													$helpcount = 0;

													if((is_countable($customs) ? count($customs) : 0) > 0) {
														$viewall .= "<form id=\"form_custom_{$modifyid}\">\n";
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
																	//$viewall .= "left colspan=2";
																	$viewall .= "left";
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
																$A_field_options = str_replace("\r\n", "\n", $A_field_options);
																$field_options_array = explode("\n", (string) ($A_field_options ?? ''));

																$field_options_count = (is_countable($field_options_array) ? count($field_options_array) : 0);
																$te=0;
																while ($te < $field_options_count)
																{
																	if (trim((string) $field_options_array[$te]) !== '') {
																		$field_selected='';
																		$field_options_value_array = explode(",", $field_options_array[$te], 2);
																		$field_option_value = trim((string) ($field_options_value_array[0] ?? ''));
																		$field_option_text = trim((string) ($field_options_value_array[1] ?? $field_option_value));

																		if ( ($A_field_type=='SELECT') or ($A_field_type=='MULTI') )
																		{

																			if ($A_field_default == "$field_option_value") {$field_selected = 'SELECTED';}
																			$field_HTML .= "<option value=\"$field_option_value\" $field_selected>" . trim($field_option_text) . "</option>\n";
																		}

																		if ( ($A_field_type=='RADIO') or ($A_field_type=='CHECKBOX') )
																		{

																			if ($A_multi_position=='VERTICAL')
																			{
																				$field_HTML .= " &nbsp; ";
																			}

																			if ($A_field_default == "$field_option_value") {$field_selected = 'CHECKED';}

																			$lblname = $A_field_label.'[]';

																			$field_HTML .= "<input type=$A_field_type name=$lblname id=\"{$lblname}_{$field_option_value}\" value=\"$field_option_value\" $field_selected> $field_option_text\n";


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
																$field_HTML .= "<textarea name=$A_field_label id=$A_field_label maxlength=$A_field_max rows=$A_field_size style='min-width: 90%'></textarea>";
															}
															if ($A_field_type=='DISPLAY')
															{
																if ($A_field_default=='NULL')
																{
																	$A_field_default='';
																}
																$field_HTML .= nl2br($A_field_default) . "\n";
															}
															if ($A_field_type=='SCRIPT')
															{
																if ($A_field_options=='NULL')
																{
																	$A_field_options='';
																}
																$field_HTML .= nl2br($A_field_options) . "\n";
															}
															if ($A_field_type=='DATE')
															{
																if ( (strlen($A_field_default)<1) or ($A_field_default=='NULL') ) {$A_field_default=0;}
																	$day_diff = is_numeric($A_field_default) ? (int) $A_field_default : 0;
																	$default_date = date("Y-m-d", mktime((int) date("H"),(int) date("i"),(int) date("s"),(int) date("m"),(int) date("d") + $day_diff,(int) date("Y")));
																	$field_HTML .= "<input type=text size=11 maxlength=10 name=$A_field_label id=$A_field_label value=\"$default_date\">\n";
																	$field_HTML .= "<script language=\"JavaScript\">\n";
																	$field_HTML .= "var o_cal = new tcal ({\n";
																	$field_HTML .= "	'formname': 'form_custom_{$modifyid}',\n";
																	$field_HTML .= "	'controlname': '$A_field_label'});\n";
																	$field_HTML .= "o_cal.a_tpl.yearscroll = false;\n";
																	$field_HTML .= "</script>\n";
																	// $baseurl = base_url();
																	 //$urlcalendar = './css/images/cal.gif';
																	//$field_HTML .= "<img id=\"$A_field_label\" name=\"$A_field_label\" src=\"$urlcalendar\">";
															}

															if ($A_field_type=='TIME')
															{
																$minute_diff = is_numeric($A_field_default) ? (int) $A_field_default : 0;
																$default_timestamp = mktime((int) date("H"),(int) date("i") + $minute_diff,(int) date("s"),(int) date("m"),(int) date("d"),(int) date("Y"));
																$default_time = date("H:i:s", $default_timestamp);
																$default_hour = date("H", $default_timestamp);
																$default_minute = date("i", $default_timestamp);
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
						<!--	</form> -->
						</div>
            </section>

				<!-- /.content -->
            </aside><!-- /.right-side -->
            <?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>

			<?php print $ui->creamyFooter(); ?>
			<?php print $ui->standardizedThemeJS();?>
        </div><!-- ./wrapper -->

				<!-- Custom Field Modal -->
				<div id="modal_custom_field_view" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">View Modal</h4>
							</div>
							<div class="modal-body">
								<div id="viewContainer" style="margin-top: 10px;">
									<!-- Custom Field Container -->
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<div id="modal_custom_field" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Custom Field Wizard</h4>
							</div>
							<div class="modal-body">
								<p class="text-muted custom-field-help" style="margin-bottom: 15px;">
									<span class="text-danger">*</span> Required fields. Requirements may change based on field type.
								</p>
								<div class="alert alert-danger custom-field-validation-message hide" style="margin-bottom: 15px;"></div>
								<form id="wizard-form" class="form-horizontal" style="margin-top: 10px;">
									<input type="hidden" name="field_id" class="field-id" value="">
									<input type="hidden" name="field_label_old" class="form-control field-label" id="field_label_old" value="">
									<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
									<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
									<div class="form-group">
										<label class="control-label col-lg-3">List ID:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control list-id" name="list_id" value="<?php echo $modifyid; ?>" readonly>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Label <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-label" name="field_label" value="" required>
											<span class="help-block custom-field-help-text">Use letters, numbers, or underscores only.</span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Rank <span class="text-danger">*</span></label>
										<div class="col-lg-3">
											<input type="number" class="form-control field-rank" name="field_rank" value="" required>
										</div>
										<label class="control-label col-lg-2">Order <span class="text-danger">*</span></label>
										<div class="col-lg-3">
											<input type="number" class="form-control field-order" name="field_order" value="" required>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Name <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-name" name="field_name" value="" required>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Position <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select class="form-control field-position" name="field_position" required>
												<option value=""></option>
												<option value="TOP">TOP</option>
												<option value="LEFT">LEFT</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Description</label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-description" name="field_description" value="">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Type <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<!-- <input type="text" class="form-control type" name="type" value="" readonly> -->
											<select name="field_type" class="select2-3 form-control field-type" style="width:100%;">
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
									<div class="form-group options-group">
										<label class="control-label col-lg-3"><span class="field-options-label">Options</span> <span class="text-danger option-required-marker hide">*</span></label>
										<div class="col-lg-9">
											<textarea class="form-control field-options" style="resize: none;" name="field_options"></textarea>
											<span class="help-block custom-field-help-text option-help-text">One option per line for SELECT, MULTI, RADIO, or CHECKBOX.</span>
										</div>
									</div>
									<div class="form-group option-position-group">
										<label class="control-label col-lg-3">Option Position <span class="text-danger option-position-required-marker hide">*</span></label>
										<div class="col-lg-9">
											<select class="form-control field-option-position" name="field_option_position">
												<option value=""></option>
												<option value="HORIZONTAL">HORIZONTAL</option>
												<option value="VERTICAL">VERTICAL</option>
											</select>
										</div>
									</div>
									<div class="form-group field-size-group">
										<label class="control-label col-lg-3">Field Size <span class="text-danger text-field-required-marker">*</span></label>
										<div class="col-lg-9">
											<input type="number" class="form-control field-size" name="field_size" value="">
										</div>
									</div>
									<div class="form-group field-max-group">
										<label class="control-label col-lg-3">Field Max <span class="text-danger text-field-required-marker">*</span></label>
										<div class="col-lg-9">
											<input type="number" class="form-control field-max" name="field_max" value="">
										</div>
									</div>
									<div class="form-group field-default-group">
										<label class="control-label col-lg-3">Field Default <span class="text-danger display-field-required-marker hide">*</span></label>
										<div class="col-lg-9">
											<input type="text" class="form-control field-default" name="field_default" value="">
											<span class="help-block custom-field-help-text display-field-help-text hide">For DISPLAY fields, this text appears in the preview.</span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-3">Field Required <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select class="form-control field-requireds" name="field_required" required>
												<option value=""></option>
												<option value="N">NO</option>
												<option value="Y">YES</option>
											</select>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary btn-create-field">Create Field</button>
								<button type="button" class="btn btn-success btn-update-field hide">Update Field</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<div id="modal_custom_field_list" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
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
												<tr class="field-row">
													<td><?php echo $A_field_rank." - ".$A_field_order;?></td>
													<td><?php echo $A_field_label; ?></td>
													<td><?php echo $A_field_name; ?></td>
													<td><?php echo $A_field_type; ?></td>
													<td style="width: 20%;">
														<button type="button" class="btn btn-primary btn-edit-cf" data-list-id="<?php echo $modifyid; ?>" data-info="<?php echo htmlspecialchars(json_encode($fieldsvalues), ENT_QUOTES, 'UTF-8'); ?>"><span class="fa fa-pencil"></span></button>
														<button type="button" class="btn btn-danger btn-delete-cf<?=($perm->customfields_delete === 'N' ? ' hidden' : '')?>" data-list-id="<?php echo $modifyid; ?>" data-field-label="<?php echo $A_field_label; ?>" data-field-id="<?php echo $A_field_id; ?>"><span class="fa fa-trash"></span></button>
														<button type="button" class="btn btn-info btn-view-cf" data-info="<?php echo htmlspecialchars(json_encode($fieldsvalues), ENT_QUOTES, 'UTF-8'); ?>"><span class="fa fa-eye"></span></button>
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
				var openingCustomFieldView = false;

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
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

				$(document).on('click', '.btn-field', function(){
					//var type = $('.custom-fields-selection').val();
					//$('.type').val(type);
					$('.field-id').val("");
//						$('.field-id').val("");
					$('.field-type').val('TEXT').change();
					$('.btn-create-field').removeClass('hide');
					$('.btn-update-field').addClass('hide');
					clearCustomFieldValidation();
					updateCustomFieldRequirementHints();
					$('#modal_custom_field').modal('show');
					$('#wizard-form input[name="field_label"]').removeAttr('readonly');
				});

				$(document).on('click', '.btn-view-cf', function(){
					var data = $(this).data('info');
					console.log(data);
					var viewHTML = '<table class="table"';
							viewHTML += '<tr>';

							//if(data.field_type != "DISPLAY" && data.field_type != "SCRIPT") {
								viewHTML += '<td><B>' + data.field_name + '</B></td>';

								viewHTML += '<td>';
							//} else {
							//	viewHTML += '<td colspan="2">';
							//}

							if(data.field_type == "SELECT") {
								viewHTML += '<select size=1 name="'+ data.field_label +'" id="'+data.field_label+'">';
							}

							if(data.field_type == "MULTI") {
								viewHTML += '<select size=1 name="'+ data.field_label +'" id="'+data.field_label+'">';
							}

							if ( (data.field_type == "SELECT") || (data.field_type == "MULTI") || (data.field_type == "RADIO") || (data.field_type == "CHECKBOX") ){
								var option_array = data.field_options.split("\n");

								$.each(option_array, function(index, value){
										if ($.trim(value) === '') {
											return;
										}
            var option_value_array = value.split(",");
										var optionValue = $.trim(option_value_array[0] || '');
										var optionText = $.trim(option_value_array[1] || optionValue);

										if ( (data.field_type == "SELECT") || (data.field_type == "MULTI") ){
											if (data.field_default == optionValue) {
												var selected = 'SELECTED';
											}else{
												var selected = '';
											}

											viewHTML += '<option value="'+ optionValue +'" '+ selected +'>'+ optionText +'</option>';
										}

										if ( (data.field_type == "RADIO") || (data.field_type == "CHECKBOX") ){
											if (data.multi_position == "VERTICAL"){
												viewHTML += " &nbsp; ";
											}

											if (data.field_default == optionValue) {
												var selected = 'CHECKED';
											}else{
												var selected = '';
											}

											var label_name = data.field_label + '[]';

											viewHTML += '<input type="'+ data.field_type +'" name="'+ label_name +'" id="'+ label_name +'" value="'+ optionValue +'" '+ selected +'> '+ optionText +'\n';

											if (data.multi_position == "VERTICAL"){
												viewHTML += "<br />\n";
											}
										}
				        });
							}

							if((data.field_type == "SELECT") || (data.field_type == "MULTI")){
								viewHTML += '</select>';
							}

							if (data.field_type == "TEXT"){
								var default_value = '';
								if (data.field_default != 'NULL'){
									default_value = data.field_default;
								}
								viewHTML += '<input type="text" size="'+ data.field_size +'" maxlength="'+ data.field_max +'" name="'+ data.field_label +'" id="'+ data.field_label +'" value="'+ default_value +'">\n';
							}

							if (data.field_type == "AREA"){
								viewHTML += '<textarea name="'+ data.field_label +'" id="'+ data.field_label +'" ROWS="'+ data.field_size +'" maxlength="'+ data.field_max +'" style="min-width: 90%;"></textarea>';
							}

							if (data.field_type == "DISPLAY"){
								viewHTML += data.field_options + "\n";
							}

							if (data.field_type == "SCRIPT"){
								viewHTML += data.field_options + "\n";
							}

							if (data.field_type == "DATE"){
								if ( (data.field_default.length < 1) || (data.field_default == "NULL") ) {
									data.field_default = 0;
								}
									var d = new Date();
									var newDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
									viewHTML += '<input type=text size=11 maxlength=10 name="'+ data.field_label +'" id="'+ data.field_label +'" value="'+ newDate +'">\n';
							}

							if (data.field_type  == "TIME"){
								var d = new Date();
								var newDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
								var newTime = d.getHours() + ":" + d.getMinutes();
								viewHTML += '<input type="hidden" name="'+ data.field_label +'" id="'+ data.field_label +'" value="'+ newTime +'">';
								viewHTML += '<SELECT name="HOUR_'+ data.field_label +'" id="HOUR_'+ data.field_label +'">';
								viewHTML += '<option>00</option>';
								viewHTML += '<option>01</option>';
								viewHTML += '<option>02</option>';
								viewHTML += '<option>03</option>';
								viewHTML += '<option>04</option>';
								viewHTML += '<option>05</option>';
								viewHTML += '<option>06</option>';
								viewHTML += '<option>07</option>';
								viewHTML += '<option>08</option>';
								viewHTML += '<option>09</option>';
								viewHTML += '<option>10</option>';
								viewHTML += '<option>11</option>';
								viewHTML += '<option>12</option>';
								viewHTML += '<option>13</option>';
								viewHTML += '<option>14</option>';
								viewHTML += '<option>15</option>';
								viewHTML += '<option>16</option>';
								viewHTML += '<option>17</option>';
								viewHTML += '<option>18</option>';
								viewHTML += '<option>19</option>';
								viewHTML += '<option>20</option>';
								viewHTML += '<option>21</option>';
								viewHTML += '<option>22</option>';
								viewHTML += '<option>23</option>';
								viewHTML += '<OPTION value="'+ d.getHours() +'" selected>'+ d.getHours() +'</OPTION>';
								viewHTML += '</SELECT>';
								viewHTML += '<SELECT name="MINUTE_'+ data.field_label +'" id="MINUTE_'+ data.field_label +'">';
								viewHTML += '<option>00</option>';
								viewHTML += '<option>05</option>';
								viewHTML += '<option>10</option>';
								viewHTML += '<option>15</option>';
								viewHTML += '<option>20</option>';
								viewHTML += '<option>25</option>';
								viewHTML += '<option>30</option>';
								viewHTML += '<option>35</option>';
								viewHTML += '<option>40</option>';
								viewHTML += '<option>45</option>';
								viewHTML += '<option>50</option>';
								viewHTML += '<option>55</option>';
								viewHTML += '<OPTION value="'+ d.getMinutes() +'" selected>'+ d.getMinutes() +'</OPTION>';
								viewHTML += '</SELECT>';
							}

							viewHTML += '</td>';
							viewHTML += '</tr>';
							viewHTML += '</table>';

					$('#viewContainer').html(viewHTML);
					openingCustomFieldView = true;
					$('#modal_custom_field_list').one('hidden.bs.modal', function(){
						$('#modal_custom_field_view').modal('show');
						$('body').addClass('modal-open');
					});
					$('#modal_custom_field_list').modal('hide');
				});

				$(document).on('click', '.btn-edit-fields', function(){
					$('#modal_custom_field_list').modal('show');
				});

				$('#modal_custom_field_list').on('hidden.bs.modal', function () {
					if (openingCustomFieldView) {
						openingCustomFieldView = false;
						return;
					}

					var field_id = $('.field-id').val();
					if(field_id !== ""){
						$('body').addClass('modal-open');
					}else{
						$('.field-id').val("");
						$('.field-label').val("");
						$('.field-rank').val("");
						$('.field-order').val("");
						$('.field-name').val("");
						$('.field-description').val("");
						$('.field-type').val("");
						$('.field-options').val("");
						$('.field-size').val("");
						$('.field-max').val("");
						$('.field-default').val("");
						$('.field-position').val("").change();
						$('.field-option-position').val("").change();
						$('.field-requireds').val("").change();
						$('body').removeClass('modal-open');

						location.reload();
					}
				});

				$('#modal_custom_field').on('hidden.bs.modal', function (e) {
						var field_id = $('.field-id').val();
						$('.field-id').val("");
						if(field_id !== ""){
							$('#modal_custom_field_list').modal('show');
							$('body').addClass('modal-open');
						}else{
							$('body').removeClass('modal-open');
							$('.field-id').val("");
							$('.field-label').val("");
							$('.field-rank').val("");
							$('.field-order').val("");
							$('.field-name').val("");
							$('.field-description').val("");
							$('.field-type').val("");
							$('.field-options').val("");
							$('.field-size').val("");
							$('.field-max').val("");
							$('.field-default').val("");
							$('.field-position').val("").change();
							$('.field-option-position').val("").change();
							$('.field-requireds').val("").change();
						}
				});

				$('#modal_custom_field_view').on('hidden.bs.modal', function (e) {
						$('#modal_custom_field_list').modal('show');
						$('body').addClass('modal-open');
				});

				$(document).on('click', '.btn-edit-cf', function(){
					var data = $(this).data('info');
					var list_id = $(this).data('list-id');
					$('.list-id').val(list_id);
					$('.field-id').val(data.field_id);
					$('.field-label').val(data.field_label);
					$('.field-rank').val(data.field_rank);
					$('.field-order').val(data.field_order);
					$('.field-name').val(data.field_name);
					if (data.field_description !== null) {
						$('.field-description').val(data.field_description.split('\\r\\n').join('\r\n'));
					}
					$('.field-type').val(data.field_type).change();
					if (data.field_options !== null) {
						$('.field-options').val(data.field_options.split('\\r\\n').join('\r\n'));
					}
					$('.field-size').val(data.field_size);
					$('.field-max').val(data.field_max);
					$('.field-default').val(data.field_default);
					$('.field-position').val(data.name_position).change();
					$('.field-option-position').val(data.multi_position).change();
					$('.field-requireds').val(data.field_required).change();
					$('#wizard-form input[name="field_label"]').attr('readonly', true);

					$('#modal_custom_field_list').modal('hide');
					$('.btn-create-field').addClass('hide');
					$('.btn-update-field').removeClass('hide');
					$('#modal_custom_field').modal('show');
					$('body').addClass('modal-open');
				});

				$('#modal_custom_field').on('hidden.bs.modal', function () {
					$('.field-label').val("");
					$('.field-rank').val("");
					$('.field-order').val("");
					$('.field-name').val("");
					$('.field-description').val("");
					$('.field-type').val("TEXT").change();
					$('.field-options').val("");
					$('.field-size').val("");
					$('.field-max').val("");
					$('.field-default').val("");
					$('.field-position').val("").change();
					$('.field-option-position').val("").change();
					$('.field-requireds').val("").change();
					clearCustomFieldValidation();
				});

				function clearCustomFieldValidation() {
					$('#wizard-form .form-group').removeClass('has-error');
					$('#wizard-form .custom-field-error').remove();
					$('.custom-field-validation-message').addClass('hide').empty();
				}

				function addCustomFieldError($field, message) {
					var $group = $field.closest('.form-group');
					$group.addClass('has-error');
					if (!$group.find('.custom-field-error').length) {
						$field.closest('[class*="col-lg-"]').append('<span class="help-block custom-field-error">'+message+'</span>');
					}
				}

				function updateCustomFieldRequirementHints() {
					var type = $('.field-type').val();
					var optionType = ['SELECT', 'MULTI', 'RADIO', 'CHECKBOX'].indexOf(type) !== -1;
					var textType = ['TEXT', 'AREA'].indexOf(type) !== -1;
					var displayType = type === 'DISPLAY';
					var scriptType = type === 'SCRIPT';

					$('.option-required-marker').toggleClass('hide', !(optionType || scriptType));
					$('.option-position-required-marker').toggleClass('hide', !optionType);
					$('.text-field-required-marker').toggleClass('hide', !textType);
					$('.display-field-required-marker, .display-field-help-text').toggleClass('hide', !displayType);
					$('.field-options-label').text(scriptType ? 'Script' : 'Options');
					$('.option-help-text').text(scriptType ? 'Text shown in the form preview for SCRIPT fields.' : 'One option per line for SELECT, MULTI, RADIO, or CHECKBOX.');
					$('.options-group').toggleClass('type-dependent-muted', !(optionType || scriptType));
					$('.option-position-group').toggleClass('type-dependent-muted', !optionType);
					$('.field-size-group, .field-max-group').toggleClass('type-dependent-muted', !textType);
				}

				function validateCustomFieldForm() {
					clearCustomFieldValidation();

					var errors = [];
					var $form = $('#wizard-form');
					var $fieldLabel = $form.find('[name="field_label"]');
					var $fieldRank = $form.find('[name="field_rank"]');
					var $fieldOrder = $form.find('[name="field_order"]');
					var $fieldName = $form.find('[name="field_name"]');
					var $fieldPosition = $form.find('[name="field_position"]');
					var $fieldType = $form.find('[name="field_type"]');
					var $fieldOptions = $form.find('[name="field_options"]');
					var $fieldOptionPosition = $form.find('[name="field_option_position"]');
					var $fieldSize = $form.find('[name="field_size"]');
					var $fieldMax = $form.find('[name="field_max"]');
					var $fieldRequired = $form.find('[name="field_required"]');
					var $fieldDefault = $form.find('[name="field_default"]');
					var type = $fieldType.val();
					var optionType = ['SELECT', 'MULTI', 'RADIO', 'CHECKBOX'].indexOf(type) !== -1;
					var textType = ['TEXT', 'AREA'].indexOf(type) !== -1;
					var displayType = type === 'DISPLAY';
					var scriptType = type === 'SCRIPT';
					var requiredFields = [
						{ field: $fieldLabel, name: 'Label' },
						{ field: $fieldRank, name: 'Rank' },
						{ field: $fieldOrder, name: 'Order' },
						{ field: $fieldName, name: 'Name' },
						{ field: $fieldPosition, name: 'Position' },
						{ field: $fieldType, name: 'Type' },
						{ field: $fieldRequired, name: 'Field Required' }
					];

					if (optionType) {
						requiredFields.push({ field: $fieldOptions, name: 'Options' });
						requiredFields.push({ field: $fieldOptionPosition, name: 'Option Position' });
					}

					if (scriptType) {
						requiredFields.push({ field: $fieldOptions, name: 'Script Text' });
					}

					if (textType) {
						requiredFields.push({ field: $fieldSize, name: 'Field Size' });
						requiredFields.push({ field: $fieldMax, name: 'Field Max' });
					}

					if (displayType) {
						requiredFields.push({ field: $fieldDefault, name: 'Display Text' });
					}

					$.each(requiredFields, function(_, item) {
						if ($.trim(item.field.val()) === '') {
							errors.push(item.name);
							addCustomFieldError(item.field, item.name + ' is required.');
						}
					});

					if ($.trim($fieldLabel.val()) !== '' && !/^[A-Za-z0-9_]+$/.test($fieldLabel.val())) {
						errors.push('Label format');
						addCustomFieldError($fieldLabel, 'Use letters, numbers, or underscores only.');
					}

					if (errors.length) {
						$('.custom-field-validation-message')
							.removeClass('hide')
							.html('<strong>Please complete the highlighted fields:</strong> ' + errors.join(', '));
						$('#modal_custom_field .modal-body').animate({ scrollTop: 0 }, 200);
						$('#wizard-form .has-error:first').find('input, select, textarea').first().focus();
						return false;
					}

					return true;
				}

				$('.field-type').on('change', updateCustomFieldRequirementHints);
				$('#wizard-form').on('input change', 'input, select, textarea', function(){
					$(this).closest('.form-group').removeClass('has-error').find('.custom-field-error').remove();
					if (!$('#wizard-form .has-error').length) {
						$('.custom-field-validation-message').addClass('hide').empty();
					}
				});
				updateCustomFieldRequirementHints();

				$(document).on('click', '.btn-create-field', function(){
					if (!validateCustomFieldForm()) {
						return;
					}

					var form_data = new FormData($("#wizard-form")[0]);

					swal({
						title: "Are you sure?",
						text: "This action cannot be undone.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, create custom field!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
												url: "./php/AddCustomField.php",
												type: 'POST',
												data: form_data,
												// dataType: 'json',
												cache: false,
                        contentType: false,
                        processData: false,
												success: function(data) {
														// console.log(data);
														if(data == "success"){
															swal({
																	title: "Success",
																	text: "Custom Field Successfully Created",
																	type: "success"
																},
																function(){
																	location.reload();
																	$(".preloader").fadeIn();
																}
															);
														}else{
																sweetAlert("Oops...", "Something went wrong! "+ data, "error");
														}
												}
									});
								} else {
										swal("Cancelled", "No action has been done :)", "error");
								}
						}
					);
				});

				$(document).on('click', '.btn-update-field', function(){
					if (!validateCustomFieldForm()) {
						return;
					}

					var form_data = new FormData($("#wizard-form")[0]);
					var field_label_old = $('#field_label_old').val();
					form_data.append('field_label_old',field_label_old);

					swal({
						title: "Are you sure?",
						text: "This action cannot be undone.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, Update this custom field!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
					}, function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/ModifyCustomField.php",
								type: 'POST',
								data: form_data,
								// dataType: 'json',
								cache: false,
								contentType: false,
								processData: false,
								success: function(data) {
									console.log(data);
									if(data == 1){
										$('#wizard-form input[name="field_label"]').removeAttr('readonly');
										swal({
											title: "Success",
											text: "Custom Field Successfully Updated/Modified",
											type: "success"
										}, function() {
											location.reload();
											$(".preloader").fadeIn();
										});
									}else{
										sweetAlert("Oops...", "Something went wrong! "+ data, "error");
									}
								}
							});
						} else {
								swal("Cancelled", "No action has been done :)", "error");
						}
					});
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

				$(document).on('click', '.btn-delete-cf', function(){
					var list_id = $(this).data('list-id');
					var field_label = $(this).data('field-label');
					var field_id = $(this).data('field-id');
					var field_row = $(this).closest('tr.field-row');

					swal({
						title: "Are you sure?",
						text: "This action cannot be undone.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, delete this custom field!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
												url: "./php/DeleteCustomField.php",
												type: 'POST',
												data: {
														list_id:list_id,
														field_label:field_label,
														field_id:field_id
												},
												// dataType: 'json',
												success: function(data) {
												// console.log(data);
														if(data == "success"){
															swal({
																title: "Success",
																text: "Custom Field Successfully Deleted!",
																type: "success"
															});
															field_row.remove();
														}else{
																sweetAlert("Oops...", "Something went wrong! "+ data, "error");
														}
												}
									});
								} else {
										swal("Cancelled", "No action has been done :)", "error");
								}
						}
					);
				});

				$(document).on('click', '.btn-delete-all-cf', function(){
					var list_id = $(this).data('id');

					swal({
						title: "Are you sure?",
						text: "This action cannot be undone.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, delete this all custom field!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
												url: "./php/DeleteAllCustomField.php",
												type: 'POST',
												data: {
														list_id:list_id
												},
												// dataType: 'json',
												success: function(data) {
												console.log(data);
														if(data == "success"){
															swal({
																	title: "Success",
																	text: "All Custom Field Successfully Deleted!",
																	type: "success"
																},
																function(){
																	location.reload();
																	$(".preloader").fadeIn();
																}
															);
														}else{
																sweetAlert("Oops...", "Something went wrong! "+ data, "error");
														}
												}
									});
								} else {
										swal("Cancelled", "No action has been done :)", "error");
								}
						}
					);
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
