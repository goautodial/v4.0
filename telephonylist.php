<?php
/**
 * @file 		telephonylist.php
 * @brief 		Manage List and Upload Leads
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
	ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '600M');
	ini_set('post_max_size', '600M');
	ini_set('max_execution_time', 0);

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

	$perm = $api->goGetPermissions('list,customfields');
	$checkbox_all = $ui->getCheckAll("list");
	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("Lists"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>

        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />

        <!-- Datetime picker CSS -->
		<link rel="stylesheet" href="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Date Picker JS -->
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.71/jquery.csv-0.71.min.js"></script>

		<style type="text/css">
			#progress-wrp {
				border: 1px solid #0099CC;
				border-radius: 3px;
				position: relative;
				width: 100%;
				height: 30px;
				background-color: #367fa9;
			}
			
			#progress-wrp .progress-bar {
				border-radius: 3px;
				position: absolute;
				width: 1%;
				height: 100%;
				background-color: #00a65a;
			  /* background-color: #4CAF50; */
			}
			
			#progress-wrp .status {
				top:3px;
				left:50%;
				position:absolute;
				display:inline-block;
				color: white;
				font-style: bold;
				/* color: #000000; */
			}
		</style>

		<style type="text/css">
			.select2-container{
				width: 100% !important;
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
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("list_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-home"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("lists"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
				<?php
					if ($perm->list->list_read !== 'N') {
						/****
						** API to get data of tables
						****/
						$lists = $api->API_getAllLists();
						
						//echo "<!--\n";
						//var_dump($lists);
						//echo "\n-->\n";
				?>
                	<div class="row">
                        <div class="col-lg-9">
		                <div class="panel panel-default">
							<div class="panel-body">
							<legend id="legend_title"><?php $lh->translateText("lists"); ?></legend>
								<div role="tabpanel">
							
									<ul role="tablist" class="nav nav-tabs nav-justified">
			
									<!-- List panel tabs-->
										 <li role="presentation" <?php if(!isset($_GET['dnc_tab']))echo 'class="active"';?>>
											<a href="#list_tab" aria-controls="list_tab" role="tab" data-toggle="tab" class="bb0">
												<?php $lh->translateText("list"); ?></a>
										 </li>
									<!-- DNC panel tab -->
										 <li role="presentation" <?php if(isset($_GET['dnc_tab']))echo 'class="active"';?>>
											<a href="#dnc_tab" aria-controls="dnc_tab" role="tab" data-toggle="tab" class="bb0">
												<?php $lh->translateText("dnc"); ?> </a>
										 </li>
									</ul>
									  
									<!-- Tab panes-->
									<div class="tab-content bg-white">
										<!--==== List ====-->
										<div id="list_tab" role="tabpanel" class="tab-pane <?php if(!isset($_GET['dnc_tab']))echo 'active'?>">
											<table class="display responsive no-wrap table table-striped table-bordered" width="100%" id="table_lists">
												<thead>
													<tr>
													<!--<th style="color: white;">Pic</th>-->
													<th><?php $lh->translateText("list_id"); ?></th>
													<th><?php $lh->translateText("name"); ?></th>
													<th><?php $lh->translateText("status"); ?></th>
													<th><?php $lh->translateText("leads_count"); ?></th>
													<th><?php $lh->translateText("campaign"); ?></th>
													<th><?php $lh->translateText("field"); ?></th>
													<?php if ($perm->list->list_delete !== 'N'){ ?>
													<th><?php echo $checkbox_all;?></th>
													<?php } ?>													
													<th><?php $lh->translateText("action"); ?></th>
													</tr>
												</thead>
												<tbody>
												<?php
												for($i=0;$i < count($lists->list_id);$i++){
												// if no entry in user list
												
												if($lists->active[$i] == "Y"){
												$lists->active[$i] = $lh->translationFor("active");
												}else{
												$lists->active[$i] = $lh->translationFor("inactive");
												}
												
												$action_list = $ui->getUserActionMenuForLists($lists->list_id[$i], $lists->list_name[$i], $perm);
												if($lists->list_id[$i] === 998 || $lists->list_id[$i] === 999)
													$checkbox = "";
												else
													$checkbox = '<label for="'.$lists->list_id[$i].'"><div class="checkbox c-checkbox"><label><input name="" class="check_list" id="'.$lists->list_id[$i].'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';
												?>
												<tr>
												<!--<td><avatar username='<?php echo $lists->list_name[$i];?>' :size='36'></avatar></td>-->
												<td><strong>
													<?php
													if (($perm->list->list_update !== 'N' && !preg_match("/^(998|999)$/", $lists->list_id[$i]))) {
													?>
													<a class='edit-list' data-id='<?php echo $lists->list_id[$i];?>'>
													<?php
													}
													?><?php echo $lists->list_id[$i];?>
												</strong></td>
												<td><?php echo $lists->list_name[$i];?></td>
												<td><?php echo $lists->active[$i];?></td>
												<td><?php echo $lists->tally[$i];?></td>
												<td><?php echo $lists->campaign_name[$i];?></td>
												<td><?php echo $lists->cf_count[$i];?></td>
												<?php if ($perm->list->list_delete !== 'N'){ ?>
												<td><?php echo $checkbox;?></td>
												<?php } ?>												
												<td><?php echo $action_list;?></td>
												</tr>
												<?php
												
												}
												?>
												</tbody>
											</table>
										</div><!-- /.list-tab -->
										<!--==== DNC ====-->
										<div id="dnc_tab" role="tabpanel" class="tab-pane <?php if(isset($_GET['dnc_tab']))echo 'active'?>">
											<table class="display responsive no-wrap table table-striped table-bordered" width="100%" id="table_dnc">
												<thead>
													<tr>
													<th><?php $lh->translateText("phone_number"); ?></th>
													<th><?php $lh->translateText("campaign"); ?></th>
													<th><?php $lh->translateText("action"); ?></th>
													</tr>
												</thead>
												<tbody>
													<tr id="dnc_result">														
														<td colspan="3"><center><span id="dnc_error">- - - <?php $lh->translateText("search_filter_dnc");?> - - -</span></center></td>
														<td></td>
														<td></td>													
													</tr>
												</tbody>
											</table>
										</div><!-- /.dnc-tab -->
										
									</div><!-- /.tab-content -->
								</div><!-- /.tab-panel -->
							</div><!-- /.body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-9 -->

<?php
//if ($perm->list->list_upload !== 'N') {
?>
	<div class="col-lg-3" id="list_sidebar">
	<h3 class="m0 pb-lg"><?php $lh->translateText("upload_import"); ?></h3>
		<form action="./php/AddLoadLeads.php" method="POST" enctype="multipart/form-data" id="upload_form" name="upload_form">
			<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
			<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
			<div class="form-group">
				<label><?php $lh->translateText("list_id"); ?>:</label>
					<div class="form-group">
					<!-- <select id="select2-1" class="form-control" name="list_id"> -->
						<select id="list_id" class="form-control select2" name="list_id" required>
						<option value="" selected disabled></option>
						<?php
						for($i=0;$i<count($lists->list_id);$i++){
						echo '<option value="'.$lists->list_id[$i].'">'.$lists->list_id[$i].' - '.$lists->list_name[$i].'</option>';
						}
						?>
						</select>
					</div>
				<div class="form-group">
					<label><?php $lh->translateText("duplicate_check"); ?> :</label>
					<SELECT size="1" NAME="goDupcheck" ID="goDupcheck" TITLE="Duplicate Check - Will check phone numbers on the lead file and cross reference it with all phone numbers on a specific campaign or in all List ID or in the entire system." class="form-control select2">
					<OPTION value="NONE"><?php $lh->translateText("no_duplicate_check"); ?></OPTION>
					<OPTION value="DUPLIST"><?php $lh->translateText("check_phones_in_list_id"); ?></OPTION>
					<OPTION value="DUPCAMP"><?php $lh->translateText("check_phones_in_campaign-lists"); ?></OPTION>
					<?php
					// Customization
					if(LEADUPLOAD_CHECK_PHONES_IN_SYSTEM === 'y'){
					?>
					<OPTION value="DUPSYS"><?php $lh->translateText("check_phones_in_system"); ?></OPTION>
					<?php }//end customization ?>
					</SELECT>
				</div>
			</div>
			
			<div class="form-group">
                        <label><?php $lh->translateText("lead_mapping"); ?>  </label> &nbsp;&nbsp;
                        	<label class="switch">
  					<input type="checkbox" id="LeadMapSubmit" name="LeadMapSubmit" value="0" />
  					<span class="slider round"></span>
				</label>
			</div>
			
			<div class="form-group">			
			<label><?php $lh->translateText("csv_file"); ?>:</label>
			<div class="form-group" id="dvImportSegments">
				<div class="input-group">
				<input type="text" class="form-control file-name" name="file_name" placeholder="<?php $lh->translateText("csv_file"); ?>" required>
				<span class="input-group-btn">
				<button type="button" class="btn browse-btn  btn-primary" type="button"><?php $lh->translateText("browse"); ?></button>
				</span>
				</div>
				<input type="file" class="file-box hide" name="file_upload" id="txtFileUpload" accept=".csv">
			</div>
	
			<div id="LeadMappingContainer" class="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
 				<div class="modal-dialog" role="document">
    					<div class="modal-content">
      						<div class="modal-header">
        						<h4 class="modal-title">
							<div class="col-sm-12 col-md-8">
								<b>LEAD MAPPING</b>
							</div>
        							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          							<span aria-hidden="true">&times;</span>
        							</button>
							</h4>
      						</div>
      						<div class="modal-body">
        						<span id="lead_map_data"></span>
							<!--<input type="hidden" id="LeadMapSubmit" name="LeadMapSubmit" value="0"/>-->
							<span id="lead_map_fields"></span>
      						</div>
      						<div class="modal-footer">
      							<input type="button" id="btnUpload" name="btnUpload" value="<?php $lh->translateText("proceed"); ?>" class="btn btn-primary" onClick="goProgressBar();">
						</div>
    					</div>
  				</div>
			</div>

			<div id="goValuesContainer"></div>
			</div>
			
			<!-- Progress bar -->
			<div class="form-group">
				<div id="progress-wrp">
				<div class="progress-bar"></div >
				<div class="status">0%</div>
				</div>
				<div id="output"><!-- error or success results --></div>
				<br />
				<div>
				<div class="alert alert-success" style="display:none;" id="dStatus"> 
				<div id="qstatus">  </div>
				</div>
				</div>
			</div>
			<!-- End Progress bar -->
			
			<div class="form-group">
			<input type="button" id="btnUpload" name="btnUpload" value="<?php $lh->translateText("update"); ?>" class="btn btn-primary" onClick="goProgressBar();">
			<!--										<div class="col-lg-12" style="margin-top: 10px;">
			<div class="alert alert-success" style="display:none;" id="dStatus"> 
			<div id="qstatus">  </div>
			</div>
			</div>-->
			</div>
			
			<div id="jMapFieldsdiv">
			<span id="jMapFieldsSpan"></span>
			</div>
		</form>
	<?php
	if(isset($_GET['message'])){
	echo '<div class="col-lg-12" style="margin-top: 10px;">';
	if($_GET['message'] == "success"){
	echo '<div class="alert alert-success"> <strong>Succes: </strong>'.$_GET['RetMesg']." leads uploaded</div>";
	}else{
	echo '<div class="alert alert-success"> <strong>Error: </strong>'.$_GET['RetMesg']."</div>";
	}
	echo '</div>';
	}
	#var_dump($_GET);
	?>
	
	</div><!-- ./upload leads -->
	
	<div class="col-lg-3" id="dnc_sidebar" style="display:none;">
	<h3 class="m0 pb-lg"><?php $lh->translateText("filter_dnc"); ?></h3>
		<div class="form-group">
			<label for="search_dnc"><?php $lh->translateText("search"); ?></label>
			<div class="has-clear">
				<input type="text" placeholder="<?php $lh->translateText("search_phone"); ?>" id="search_dnc" class="form-control mb">
				<span class="form-control-clear fa fa-close form-control-feedback"></span>
			</div>
		</div>
		<div class="clearfix">
			<button type="button" class="pull-left btn btn-default" id="dnc_search_button"> <?php $lh->translateText("search"); ?></button>
		</div>
	</div><!-- ./ dnc search -->
<?php
//}
?>

</div>
<?php
} else {
print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
}
?>
</section><!-- /.content -->
</aside><!-- /.right-side -->
<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
</div><!-- ./wrapper -->

<!-- FIXED ACTION BUTTON -->
<div class="action-button-circle" data-toggle="modal" data-target="#list-modal" id="list_fab" title="<?php $lh->translateText("list_wizard"); ?>">
<?php print $ui->getCircleButton("list_and_call_recording", "plus"); ?>
</div>
<div class="action-button-circle" data-toggle="modal" data-target="#dnc-modal" id="dnc_fab" style="display:none;" title="<?php $lh->translateText("add_delete_dnc"); ?>">
<?php print $ui->getCircleButton("list_and_call_recording", "pencil-square-o"); ?>
</div>
<?php
	$campaign = $api->API_getAllCampaigns();
	
	$next_listname = "ListID ".$lists->next_listID;
	$datenow = date("j-n-Y");
	$next_listdesc = "Auto-generated - ListID - ".$datenow;
?>
	<div class="modal fade" id="list-modal" tabindex="-1" aria-labelledby="list-modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="scripts"><b><?php $lh->translateText("list_wizard"); ?> » <?php $lh->translateText("add_list"); ?></b></h4>
				</div>
				<div class="modal-body wizard-content">

				<form method="POST" id="create_form" role="form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
				<div class="row">
				<h4><?php $lh->translateText("list_info"); ?>
				   <br>
				   <small><?php $lh->translateText("list_details"); ?></small>
				</h4>
				<fieldset>
					<div class="form-group mt">
						<label class="col-sm-3 control-label" for="auto_generate"><?php $lh->translateText("auto_generated"); ?>:</label>
						<div class="col-sm-9 mb">
							<label class="col-sm-3 checkbox-inline c-checkbox" for="auto_generate">
								<input type="checkbox" id="auto_generate" checked>
								<span class="fa fa-check"></span>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="add_list_id"><?php $lh->translateText("list_id"); ?>:</label>
						<div class="col-sm-9 mb">
							<input type="number" class="form-control" name="add_list_id" id="add_list_id" placeholder="<?php $lh->translateText("list_id"); ?>" value="<?php echo $lists->next_listID;?>" minlength="1" maxlength="8" disabled required/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="list_name"><?php $lh->translateText("list_name"); ?>:</label>
						<div class="col-sm-9 mb">
							<input type="text" class="form-control" name="list_name" id="list_name" placeholder="<?php $lh->translateText("list_name"); ?>" value="<?php echo $next_listname;?>" maxlength="30" required/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label text-nowrap" for="list_desc"><?php $lh->translateText("list_description"); ?>:</label>
						<div class="col-sm-9 mb">
							<input type="text" class="form-control" name="list_desc" id="list_desc" placeholder="<?php $lh->translateText("list_description"); ?>"  value="<?php echo $next_listdesc;?>" maxlength="255" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="campaign_select"><?php $lh->translateText("campaign"); ?>: </label>
						<div class="col-sm-9 mb">
							<select name="campaign_select" class="form-control">
								<?php
								echo '<option value="">'.$lh->translationFor("-none-").'</option>';
									for($i=0; $i < count($campaign->campaign_id);$i++){
										echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_name[$i]." </option>";
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="status"><?php $lh->translateText("active"); ?>: </label>
						<div class="col-sm-9 mb">
							<select name="status" class="form-control">
								<option value="Y" selected><?php $lh->translateText("go_yes"); ?></option>
								<option value="N" ><?php $lh->translateText("go_no"); ?></option>
							</select>
						</div>
					</div>
				</fieldset>
				</div>
				</form>

				</div> <!-- end of modal body -->
			</div>
		</div>
	</div><!-- end of modal -->

	<div id="modal_custom_field_copy" class="modal fade" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php $lh->translateText("copy_custom_wizard"); ?></h4>
				</div>
				<div class="modal-body">
					<form id="copy_cf_form" class="form-horizontal" style="margin-top: 10px;">
						<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
						<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
						<div class="form-group">
							<label class="control-label col-lg-4"><?php $lh->translateText("copy_from_list_id"); ?>:</label>
							<div class="col-lg-8">
								<input type="hidden" class="form-control list-from" value="" name="list_from">
								<input type="text" class="form-control list-from-label" value="" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-4"><?php $lh->translateText("copy_to_list_id"); ?>:</label>
							<div class="col-lg-8">
								<select class="form-control select2" name="list_to">
									<?php for($i=0;$i < count($lists->list_id);$i++){ ?>
										<option value="<?php echo $lists->list_id[$i]; ?>"><?php echo $lists->list_id[$i].' - '.$lists->list_name[$i];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-4"><?php $lh->translateText("copy_option"); ?>:</label>
							<div class="col-lg-8">
								<select class="form-control select2" name="copy_option">
									<option value="APPEND"><?php $lh->translateText("append"); ?></option>
									<option value="UPDATE"><?php $lh->translateText("UPDATE"); ?></option>
									<option value="REPLACE"><?php $lh->translateText("replace"); ?></option>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
					<button type="button" class="btn btn-success btn-copy-cf" data-dismiss="modal"><?php $lh->translateText("copy"); ?></button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End of modal -->
	
	<!-- Modal -->
	<div id="dnc-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText("add_delete_dnc"); ?></b></h4>
	      </div>
	      <div class="modal-body">
			<form id="dnc_form">
				<input type="hidden" name="session_user" value="<?php echo $_SESSION['user'];?>">
				<div class="form-group mt">
					<label class="col-md-3 control-label"><?php $lh->translateText("list"); ?>:</label>
					<div class="col-md-9 mb">
						<select id="campaign_id" class="form-control select2" name="campaign_id" required>
							<option value="INTERNAL"><?php $lh->translateText("internal_dnc"); ?></option>
							<?php
								for($i=0;$i<count($campaign->campaign_id);$i++){
									echo '<option value="'.$campaign->campaign_id[$i].'">'.$campaign->campaign_id[$i].' - '.$campaign->campaign_name[$i].'</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group mt">
					<label class="col-md-3 control-label"><?php $lh->translateText("phone_number"); ?>:</label>
					<div class="col-md-9 mb">
						<textarea rows="15" cols="17" name="phone_numbers" id="phone_numbers" style="resize:none"></textarea><br/>
						<small class="text-danger"><?php $lh->translateText("limit25_per_submit"); ?></small>
					</div>
				</div>
				<div class="form-group mt">
					<label class="col-md-3 control-label"><?php $lh->translateText("add_delete_dnc"); ?>:</label>
					<div class="col-md-4">
						<select id="stageDNC" class="form-control" name="stageDNC" required>
							<option value="ADD"><?php $lh->translateText("ADD"); ?> DNC LIST</option>
							<option value="DELETE"><?php $lh->translateText("DELETE"); ?> DNC LIST</option>
						</select>
					</div>
				</div>
			</form>
	      </div>
		  
	      <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="submit_dnc"><?php $lh->translateText("submit"); ?></button>
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	
	<?php print $ui->standardizedThemeJS();?>
	<!-- JQUERY STEPS-->
	<script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>
		
	<script type="text/javascript">
		$(document).ready(function() {
			var list_create = <?php echo ($perm->list->list_create !== "N" ? 1 : 0 ) ?>;
			var list_read 	= <?php echo ($perm->list->list_create !== "N" ? 1 : 0 ) ?>;
			var list_update = <?php echo ($perm->list->list_create !== "N" ? 1 : 0 ) ?>;			
			var list_delete = <?php echo ($perm->list->list_delete !== "N" ? 1 : 0 ) ?>;
			var list_upload = <?php echo ($perm->list->list_upload !== "N" ? 1 : 0 ) ?>;

			if (list_create != 1) {
				$("#list_fab").attr("disabled", true);
				$("#list_fab").attr("hidden", true);
			} else {
				$("#list_fab").attr("disabled", false);
				$("#list_fab").attr("hidden", false);			
			}
			if (list_upload != 1) {
				//console.log(list_upload);
				$("#list_sidebar").find("select, textarea, input, .browse-btn").each(function() {
					//console.log($(this).attr('name'));
					$(this).attr("disabled", true);
				});
			} else {
				//console.log(list_upload);
				$("#list_sidebar").find("select, textarea, input, .browse-btn").each(function() {
					//console.log($(this).attr('name'));
					$(this).attr("disabled", false);
				});			
			}

				
			// on tab change, change sidebar
			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var target = $(e.target).attr("href"); // activated tab
				
				if(target == "#list_tab"){
					$("#list_sidebar").show();
					$("#list_fab").show();
					$("#dnc_sidebar").hide();
					$("#dnc_fab").hide();
					$("#legend_title").text("Lists");
				}
				if(target == "#dnc_tab"){
					$("#dnc_sidebar").show();
					$("#dnc_fab").show();
					$("#list_sidebar").hide();
					$("#list_fab").hide();
					$("#legend_title").text("DNC");
				}
			});
			
			$('body').on('keypress', '#search_dnc', function(args) {
				if (args.keyCode == 13) {
					$("#dnc_search_button").click();
					return false;
				}
			});
			
			// initialize datatable
			$('#table_lists').DataTable({
				destroy: true,
				responsive: true,
				select: true,
				stateSave: true,
				drawCallback:function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},
				columnDefs:[
					<?php if($perm->list->list_delete !== 'N'){?>
						{ width: "8%", targets: 7 },
						{ width: "5%", targets: 6 },
						{ width: "5%", targets: 0 },						
						{ searchable: false, targets: [ 6, 7 ] },
						{ sortable: false, targets: [ 6, 7 ] },
					<?php }else{ ?>
						{ width: "10%", targets: 6 },
						{ width: "5%", targets: 0 },					
						{ searchable: false, targets: 6 },
						{ sortable: false, targets: 6 },
					<?php } ?>
					{ targets: -1, className: "dt-body-right" }
				]
			});
			
			$('#table_dnc').DataTable({
				destroy: true,
				responsive: true,
				drawCallback:function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},
				columnDefs:[
					{ searchable: false, targets: 2 },
					{ sortable: false, targets: 2 },
					{ targets: -1, className: "dt-body-right" }
				]									
			});			
				
			// add list
			if (list_create == 1) {
				//console.log(list_create);
				var form = $("#create_form"); // init form wizard

				form.validate({
					errorPlacement: function errorPlacement(error, element) { element.after(error); }
				});
				
				form.children("div").steps({
					headerTag: "h4",
					bodyTag: "fieldset",
					transitionEffect: "slideLeft",
					onStepChanging: function (event, currentIndex, newIndex)
					{
						// Allways allow step back to the previous step even if the current step is not valid!
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
	
						form.validate().settings.ignore = "";
						return form.valid();
					},
					onFinishing: function (){
						form.validate().settings.ignore = "";
						return form.valid();
					},
					onFinished: function (){
						$('#finish').text("<?php $lh->translateText("loading"); ?>");
						$('#finish').attr("disabled", true);
						$('#add_list_id').attr("disabled", false);
						// Submit form via ajax
						$.ajax({
							url: "./php/AddList.php",
							type: 'POST',
							data: $('#create_form').serialize(),
							success: function(data) {
								console.log(data);
								$('#finish').text("<?php $lh->translateText("submit"); ?>");
								$('#finish').attr("disabled", false);
								if (data == 1) {
									swal({title: "<?php $lh->translateText("add_list_success"); ?>",text: "<?php $lh->translateText("add_list_success"); ?>",type: "success"},function(){window.location.href = 'telephonylist.php';});
								} else {
									sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>", "error");
								}
							}
						});
					}
				});
			}

			// edit
			if (list_update == 1) {
				$(document).on('click','.edit-list',function() {
					var url = './edittelephonylist.php';
					var id = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
					$('body').append(form);  // This line is not necessary
					
					$(form).submit();
				});
			}
				
			if (list_read == 1) {
				$(document).on('click','.download-list',function() {
					var url = 'php/ExportList.php';
					var id = $(this).attr('data-id');
					console.log(id);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="listid" value="'+id+'" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});
			}

			// delete
			if (list_delete == 1) {
				console.log(list_delete);
				$(document).on('click','.delete-list',function() {
					var listid = [];
					listid.push($(this).attr('data-id'));
					console.log(listid);
					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>?",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "<?php $lh->translateText("confirm_list_delete"); ?>!",
						cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
									url: "./php/DeleteList.php",
									type: 'POST',
									data: {
										listid: listid
									},
									success: function(data) {
									console.log(data);
										if(data == 1){
											swal({title: "<?php $lh->translateText("delete_list_success"); ?>",text: "<?php $lh->translateText("delete_list_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonylist.php';});
										}else{
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>!", "error");
										}
									}
								});

							} else {
								swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
						}
					);
				});
				
				$(document).on('click','.delete-multiple-list',function() {
				var arr = $('input:checkbox.check_list').filter(':checked').map(function () {
					return this.id;
				}).get();
				console.log(arr);
				swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "<?php $lh->translateText("delete_multiple_list"); ?>!",
						cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
						closeOnConfirm: false,
						closeOnCancel: false
					},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
									url: "./php/DeleteList.php",
									type: 'POST',
									data: {
										listid: arr
									},
									success: function(data) {
									console.log(data);
										if(data == 1){
											swal({title: "<?php $lh->translateText("delete_list_success"); ?>",text: "<?php $lh->translateText("delete_list_success_msg"); ?>!",type: "success"},function(){window.location.href = 'telephonylist.php';});
										}else{
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
										}
									}
								});
							} else {
									swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
						}
					);
				});
			}
					
			if (list_read == 1) {
				$(document).on('click', '.copy-custom-fields', function(){
					var list_id = $(this).data('id');
					var list_name = $(this).data('name');

					$('.list-from').val(list_id);
					$('.list-from-label').val(list_id + ' - ' + list_name);
					$('#modal_custom_field_copy').modal('show');
				});

				$(document).on('click', '.btn-copy-cf', function(){
					var form_data = new FormData($("#copy_cf_form")[0]);
					swal({
						title: "<?php $lh->translateText("are_you_sure"); ?>",
						text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "<?php $lh->translateText("yes_copy_custom_fields"); ?>",
						cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",
						closeOnConfirm: false,
						closeOnCancel: false
						},
						function(isConfirm){
							if (isConfirm) {
								$.ajax({
									url: "./php/CopyCustomFields.php",
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
													title: "<?php $lh->translateText("success"); ?>",
													text: "<?php $lh->translateText("custom_fields_copied"); ?>",
													type: "success"
												},
												function(){
													location.reload();
													$(".preloader").fadeIn();
												}
											);
										}else{
												sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
										}
									}
								});
							} else {
							swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
							}
						}
					);
				});
			}
					
			$(document).on('click', '#auto_generate', function(){
			// alert( this.value ); // or $(this).val()
				if($('#auto_generate').is(":checked")){
					$('#add_list_id').val("<?php echo $next_list;?>");
					$('#list_name').val("<?php echo $next_listname;?>");
					$('#list_desc').val("<?php echo $next_listdesc;?>");
					$('#add_list_id').prop("disabled", true);
				}
				if(!$('#auto_generate').is(":checked")){
					$('#add_list_id').val("");
					$('#list_name').val("");
					$('#list_desc').val("");
					$('#add_list_id').prop("disabled", false);
				}
			});

			//initialize single selecting
			$('#select2-1').select2({ theme: 'bootstrap' });
			$.fn.select2.defaults.set( "theme", "bootstrap" );


			$('.browse-btn').click(function(){
				$('.file-box').click();
			});

			$('.file-box').change(function(){
				var myFile = $(this).prop('files');
				var Filename = myFile[0].name;

				$('.file-name').val(Filename);
				console.log($(this).val());
			});

			// DNC Search
			if (list_read == 1) {
				$(document).on('click','#dnc_search_button',function() {
				//init_contacts_table.destroy();
					if ($('#search_dnc').val() != "") {
						$('#dnc_search_button').text("<?php $lh->translateText("searching"); ?>");
						$('#dnc_search_button').attr("disabled", true);
					} else {
						$('#dnc_search_button').text("<?php $lh->translateText("search"); ?>");
						$('#dnc_search_button').attr("disabled", false);
					}
					
					$.ajax({
						url: "search_dnc.php",
						type: 'POST',
						data: {
							search_dnc : $('#search_dnc').val()
						},
						success: function(data) {
							$('#dnc_search_button').text("<?php $lh->translateText("search"); ?>");
							$('#dnc_search_button').attr("disabled", false);
							//console.log(data);
							if (data != "") {
								$('#table_dnc').html(data);
								$('#table_dnc').DataTable({
									destroy: true,
									responsive: true,
									drawCallback:function(settings) {
										var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
										pagination.toggle(this.api().page.info().pages > 1);
									},
									columnDefs:[
										//{ width: "16%", targets: 7 },
										{ searchable: false, targets: 2 },
										{ sortable: false, targets: 2 },
										{ targets: -1, className: "dt-body-right" }
									]									
								});
								$('#dnc_error').html("");
							} else {
								$('#dnc_error').text("<?php $lh->translateText("no_results"); ?>");
							}
						}
					});
				});
			
				// DNC Submit
				$(document).on('click','#submit_dnc',function() {
					$('#submit_dnc').text("<?php $lh->translateText("submitting"); ?>");
					$('#submit_dnc').attr("disabled", true);
					var stageDNC = $('#stageDNC option:selected').val();
					console.log(stageDNC);
					if ($('#phone_numbers').val() !== ''){
						$.ajax({
							url: "php/ActionDNC.php",
							type: 'POST',
							data: $('#dnc_form').serialize(),
							//type: 'json',
							success: function(data) {
								console.log(data);
								$('#submit_dnc').text("<?php $lh->translateText("add_delete_dnc"); ?>");
								$('#submit_dnc').attr("disabled", false);										
								
								if (data == 1) {
									if (stageDNC == "ADD") {
										swal({title: "<?php $lh->translateText("added_new"); ?> DNC", text: "<?php $lh->translateText("add_dnc"); ?>", type: "success"},function(){window.location.href = 'telephonylist.php?dnc_tab';});
									}
									if (stageDNC == "DELETE") {
										swal({title: "<?php $lh->translateText("deleted"); ?> DNC", text: "<?php $lh->translateText("delete_dnc"); ?>", type: "success"},function(){window.location.href = 'telephonylist.php?dnc_tab';});
									}								
								} else {
									if (data == 10116) {
										sweetAlert("<?php echo $lh->translateText("oups"); ?>", "<?php echo $lh->translateText("dnc_already_exist"); ?>", "error");
									}		
									if (data == 10117) {
										sweetAlert("<?php echo $lh->translateText("oups"); ?>", "<?php echo $lh->translateText("dnc_do_not_exist"); ?>", "error");
									}									
								}
								
							}
						});
					} else {
						$('#submit_dnc').text("<?php $lh->translateText("submit"); ?>");
						$('#submit_dnc').attr("disabled", false);
						swal("<?php $lh->translateText("dnc_incomplete"); ?>", "<?php $lh->translateText("dnc_incomplete_msg"); ?>", "error");
					}
					
				});
			}
				
			// Delete DNC
			if (list_delete == 1) {				
				$(document).on('click','.delete-dnc',function() {
					var phone_number = $(this).data('id');
					var campaign = $(this).data('campaign');
					
					$.ajax({
						url: "php/ActionDNC.php",
						type: 'POST',
						data: {
							phone_numbers : phone_number,
							campaign_id : campaign,
							stageDNC : "DELETE"
						},
						//type: 'json',
						success: function(data) {
							console.log(data);
							
							if (data == 1) {
								swal({title: "<?php $lh->translateText("deleted"); ?> DNC", text: "<?php $lh->translateText("delete_dnc"); ?>", type: "success"},function(){window.location.href = 'telephonylist.php?dnc_tab';});								
								$("#dnc_sidebar").show();
								$("#dnc_fab").show();
								$("#list_sidebar").hide();
								$("#list_fab").hide();
								$("#legend_title").text("DNC");								
							} else {
								if (data == 10116) {
									sweetAlert("<?php echo $lh->translateText("oups"); ?>", "<?php echo $lh->translateText("dnc_already_exist"); ?>", "error");
								}		
								if (data == 10117) {
									sweetAlert("<?php echo $lh->translateText("oups"); ?>", "<?php echo $lh->translateText("dnc_do_not_exist"); ?>", "error");
								}									
							}
						}
					});
				});
			}
			
			$('#phone_numbers').keypress(function(event){
				if((event.ctrlKey === false && ((event.which < 48 || event.which > 57) && event.which !== 13 && event.which !== 8)) && (event.keyCode !== 9 && event.keyCode !== 46 && (event.keyCode < 37 || event.keyCode > 40)))
				return false;
			});
			
			var lines = 25;
			
			$('#phone_numbers').keydown(function(e) {
				newLines = $(this).val().split("\n").length;
			
				if(e.keyCode == 13 && newLines >= lines) {
					return false;
				}
			});

			$('#phone_numbers').blur(function() {
				this.value = this.value.replace('/[^0-9\r\n]/g','');
			});
			
			if (window.location.href.indexOf("dnc_tab") > -1) {
				$("#dnc_sidebar").show();
				$("#dnc_fab").show();
				$("#list_sidebar").hide();
				$("#list_fab").hide();
				$("#legend_title").text("DNC");						
			}			
			
			// RESET LEAD MAPPING CONTAINER ON CLOSE
			$('#LeadMappingContainer').on('hidden.bs.modal', function () {
				$('#LeadMapSubmit').val(0);
				$('#lead_map_data').html("");
				$('#lead_map_fields').html("");
			});			
	
		});
		
		// Progress bar function
		function goProgressBar() {
			
			var formData = new FormData($('#upload_form')[0]);
			var progress_bar_id 		= '#progress-wrp'; //ID of an element for response output
			var percent = 0;
			
			var result_output 			= '#output'; //ID of an element for response output
			var my_form_id 				= '#upload_form'; //ID of an element for response output
			var submit_btn  = $(this).find("input[type=button]"); //btnUpload

			formData.append('tax_file', $('input[type=file]')[0].files);
			
			$.ajax({
				url : "./php/AddLoadLeads.php",
				type: "POST",
				data : formData,
				contentType: false,
				cache: false,
				processData:false,
				maxChunkSize: 1000000000,
				maxRetries: 100000000,
				retryTimeout: 5000000000,
				timeout: 0,
				xhr: function(){
					//upload Progress
					var xhr = $.ajaxSettings.xhr();
					if (xhr.upload) {
						xhr.upload.addEventListener('progress', function(event) {
							
							var position = event.loaded || event.position;
							var total = event.total;
							if (event.lengthComputable) {
								percent = Math.ceil(position / total * 100);
							}
							
							//update progressbar
							$(progress_bar_id +" .progress-bar").css("width", + percent +"%");
							$(progress_bar_id + " .status").text(percent +"%");
							//$(progress_bar_id + " .status").innerHTML = percent + '%';
							
							if(percent === 100) {
								
								//$('#dStatus').css("display", "block");
								//$('#dStatus').css("color", "#4CAF50");
								//$('#qstatus').text("File Uploaded Successfully. Please wait for the TOTAL of leads uploaded.(Do not refresh the page)");
								//$('#qstatus').text("Data Processing. Please Wait.");
								//sweetAlert("Oops...", "Something went wrong!", "error");
								
								//var uploadMsgTotal = "Total Leads Uploaded: "+res;
				
								swal({
									title: "<?php $lh->translateText('csv_upload_complete'); ?>",
									text: "<?php $lh->translateText('data_processing'); ?>",
									type: "info",
									showCancelButton: false,
									closeOnConfirm: false
								});
								
							}
							
						}, true);
						
					}
					return xhr;
				},
				mimeType:"multipart/form-data",
				statusCode: {
					503: function(responseObject, textStatus, errorThrown) {
						//console.log(responseObject, textStatus, errorThrown);
				            // Service Unavailable (503)
				            // This code will be executed if the server returns a 503 response
					    //alert(responseObject + textStatus);
					    //$.ajax(this);
					    upload_timeout(JSON.stringify(textStatus));
					    return;
				        }
				}
			}).done(function(res){
				//console.log(res);
				var data = jQuery.parseJSON(res);
				
					<?php
						//if(LEADUPLOAD_LEAD_MAPPING === "y"){ // IF LEAD MAPPING IS ENABLED
					?>
						if($('#LeadMapSubmit').val() === "0" && $('#LeadMapSubmit').is(':checked')){
							lead_mapping(res);
						} else {
							upload_alert(data.result, data.msg);						
						}
					<?php
						/*}else{
					?>
						upload_success(uploadMsgTotal);
					<?php
						}*/
					?>	
			});								
		}//function end
		
		function upload_alert(res, msg){
		 if(res === "success")
			var uploadMsgTotal = msg;
		 else
			var uploadMsgTotal = msg;
		 swal({
                      title: "<?php $lh->translateText('data_processing_complete'); ?>",
                      text: uploadMsgTotal,
                      type: res
                      },
                      function(){
                      location.reload();
                      $(".preloader").fadeIn();
                    }
                 );
		}

		function upload_timeout(uploadMsg){
                                swal({
                      title: "<?php $lh->translateText('Request Timeout'); ?>",
                      text: uploadMsg,
                      type: "error"
                    }
                 );
                }

		function lead_mapping(res){
			var obj = JSON.parse(res);
			var data = obj.data;
			var sf = obj.standard_fields;
			var cf = obj.custom_fields;
			//console.log( obj );
			if(cf[0] !== "")
				var all = sf.concat(cf);
			else
				var all = sf;
			var i;
			console.log(cf[0]);	
			console.log(all.length);	
			for(i = 0; i < all.length;i++){
				$('#lead_map_data').append('<div class="form-group"><label>'+all[i]+'</label><span id="span_'+all[i]+'"></span></span></div>');
				$('<input>').attr({type: 'hidden',name: 'map_fields[]', value: all[i]}).appendTo('#lead_map_fields');
				var sel = $('<select name="map_data[]" class="form-control select2">').appendTo('#span_'+all[i]);
				sel.append($("<option>").attr('value',".").text("NONE"));
				for(x=0; x < data.length;x++){
					//if(data[i] === data[x])
					//sel.append($("<option>").attr('value',x).text(data[x]).prop('selected', true));
					//else
					sel.append($("<option>").attr('value',x).text(data[x]));
				}
			}
			$('#LeadMapSubmit').val("1");
			swal.close();
			$('#LeadMappingContainer').modal('show');
		}
			
	</script>
	<?php print $ui->creamyFooter();?>
    </body>
</html>
