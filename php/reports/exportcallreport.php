<?php
/**
 * @file        exportcallreport.php
 * @brief       Handles report requests
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja
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

	require_once('APIHandler.php');
	$api = \creamy\APIHandler::getInstance();
		
	$session_user = $api->GetSessionUser();
	$session_group = $api->GetSessionGroup();	

	// GET CAMPAIGNS
		$campaigns = $api->API_getAllCampaigns();
		
	// GET INBOUND GROUPS
		$inbound = $api->API_getAllInGroups();
		
	// GET LISTS
		$list = $api->API_getAllLists();
			
	// GET STATUSES
		$disposition = $api->API_getAllDispositions();

		$display = '';
		$display .= '
				<form action="./php/ExportCallReport.php" id="export_callreport_form" method="POST">
					<input type="hidden" name="log_user" value="'.$session_user.'" />
					<input type="hidden" name="log_group" value="'.$session_group.'" />
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Campaigns:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_campaigns" name="campaigns[]" style="width:100%;">';
											if(EXPORTCALLREPORT_ALLCAMPAIGNS === "y"){
												$display .= '<option value="ALL" selected>--- ALL CAMPAIGNS ---</option>';
											}
											for($i=0; $i < count($campaigns->campaign_id);$i++) {
												$isSelected = '';
												if ($i < 1 && EXPORTCALLREPORT_ALLCAMPAIGNS !== "y") {
													$isSelected = ' selected';
												}
												$display .= '<option value="'.$campaigns->campaign_id[$i].'"'.$isSelected.'>'.$campaigns->campaign_id[$i].' - '.$campaigns->campaign_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Inbound Groups:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_inbounds" name="inbounds[]" style="width:100%;">';
											if(EXPORTCALLREPORT_ALLCAMPAIGNS === "y"){
                                                                                                $display .= '<option value="ALL">--- ALL ---</option>
';
                                                                                        }

											for($i=0; $i < count($inbound->group_id);$i++) {
												if ($session_group !== "ADMIN" && preg_match("/^AGENTDIRECT/", $inbound->group_id[$i])) continue;
												$display .= '<option value="'.$inbound->group_id[$i].'">'.$inbound->group_id[$i].' - '.$inbound->group_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Lists:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_lists" name="lists[]" style="width:100%;">';
										$display .= '<option value="ALL" selected>--- ALL ---</option>';
											for($i=0; $i < count($list->list_id);$i++) {
												$display .= '<option value="'.$list->list_id[$i].'">'.$list->list_id[$i].' - '.$list->list_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Statuses:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_statuses" name="statuses[]" style="width:100%;">';
										$display .= '<option value="ALL" selected>--- ALL ---</option>';
											for($i=0; $i < count($disposition->status);$i++) {
												if($disposition->campaign_id[$i] != NULL){
													if(in_array($disposition->status[$i], $campaigns->campaign_id)){
														$display .= '<option value="'.$disposition->status_name[$i].'">'.$disposition->status[$i].' - '.$disposition->status_name[$i].'</option>';
													}
												} else {
													$display .= '<option value="'.$disposition->status[$i].'">'.$disposition->status[$i].' - '.$disposition->status_name[$i].'</option>';
												}
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Custom Fields:</label>
								<div class="mb">
									<div class="">
										<select class="form-control" id="selected_custom_fields" name="custom_fields">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Per Call Notes:</label>
								<div class="mb">
									<div class="">
										<select class="form-control" id="selected_per_call_notes" name="per_call_notes">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Recording Location:</label>
								<div class="mb">
									<div class="">
										<select class="form-control" id="selected_rec_location" name="rec_location">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
					<div class="row">
						<center><button class="btn btn-info" name="submit_export" id="submit_export"><li class="fa fa-download"> Submit & Download</button>
					</div>
					<span id="result_export"></span>
		';
		echo $display;
	?>
		<script>
			// initialize multiple selecting
			$('.select2-3').select2({ theme: 'bootstrap' });	
			$.fn.select2.defaults.set( "theme", "bootstrap" );
			
			$(document).on('click','#submit_export',function() {
				$('#submit_export').html("Downloading.....");
				$('#submit_export').attr("disabled", true);
				
				fromDateVal = $('#start_filterdate').val();
				if ($('#export_callreport_form').find('input[name="fromDate"]').length < 1) {
					$('#export_callreport_form').append("<input type='hidden' name='fromDate' value='"+fromDateVal+"' />");
				} else {
					$('#export_callreport_form').find('input[name="fromDate"]').val(fromDateVal);
				}
				
				toDateVal = $('#end_filterdate').val();
				if ($('#export_callreport_form').find('input[name="toDate"]').length < 1) {
					$('#export_callreport_form').append("<input type='hidden' name='toDate' value='"+toDateVal+"' />");
				} else {
					$('#export_callreport_form').find('input[name="toDate"]').val(toDateVal);
				}
				
				//alert($("#toDate").val());
				
				$( "#export_callreport_form" ).submit();
				
				$('#submit_export').html('<li class="fa fa-download"> Submit & Download');
				$('#submit_export').attr("disabled", false);
			});

			$(document).on('change', '#selected_campaigns', function() {
				var selectedCampaigns = $('#selected_campaigns').val();
				var campaigns = <?php echo json_encode($campaigns); ?>;
				var dispo = <?php echo json_encode($disposition); ?>;
				var statOptions = "<option value='ALL' selected>--- ALL ---</option>";
				console.log(selectedCampaigns);
				var statusName = "";
				var customDispo = {};
				var statusesContainer = [];
				var statuses = [];
				var custom_statuses = [];
				<?php
					foreach ($disposition->custom_dispo as $cCamp => $cDispo){
						$dispoStatuses = array();
	        	                		foreach ($cDispo as $idx => $val) {
        	                                        	$dispoStatuses[] = $idx . " - " . $val;
                                                        }
				?>
						customDispo["<?php echo $cCamp;?>"] = "<?php echo implode(", ", $dispoStatuses); ?>";
				<?php
					}
				?>
				console.log(campaigns.campaign_id);
				console.log(customDispo);
				//if(selectedCampaigns != null){
					if( selectedCampaigns.includes("ALL") || selectedCampaigns == null) {
						for(i=0; i < campaigns.campaign_id.length; i++){
							if( customDispo[campaigns.campaign_id[i]] != null ) {
								statuses = customDispo[campaigns.campaign_id[i]].split(", ");
							}
							for( a=0; a < statuses.length; a++ ){ // custom dispositions
                                                                for( b=0; b < statuses.length; b++ ){
                                                                        if( !(statusesContainer.includes(statuses[b])) ){
                                                                	        statusesContainer.push(statuses[b]);
                                                                                custom_statuses = statuses[b].split(" - ");
                                                                 	        statOptions += "<option value='" + custom_statuses[0] + "'>" + custom_statuses[0] + " - " + custom_statuses[1] + "</option>";
                                                                        }
                                                                }
                                                        }
						}
						for(i=0; i < dispo.status.length; i++){
							if( !(dispo.status[i] in dispo.custom_dispo) ){ // default dispositions
                                                        	statOptions += "<option value='" + dispo.status[i] + "'>" + dispo.status[i] + " - " + dispo.status_name[i] + "</option>";
                                                       	}
						}
					} else {
						for(i=0; i < dispo.status.length; i++){
							for( a=0; a < selectedCampaigns.length; a++ ){ // custom dispositions
								if(selectedCampaigns[i] in customDispo){
									if( customDispo[campaigns.campaign_id[i]] != null ) {
										statuses = customDispo[selectedCampaigns[i]].split(", ");
									}
									for( b=0; b < statuses.length; b++ ){
										if( !(statusesContainer.includes(statuses[b])) ){
											statusesContainer.push(statuses[b]);
											custom_statuses = statuses[b].split(" - ");
											statOptions += "<option value='" + custom_statuses[0] + "'>" + custom_statuses[0] + " - " + custom_statuses[1] + "</option>";
										}	
									}
								}
							}
							if( !(dispo.status[i] in dispo.custom_dispo) ){ // default dispositions
								statOptions += "<option value='" + dispo.status[i] + "'>" + dispo.status[i] + " - " + dispo.status_name[i] + "</option>";					
							}
						}
					}
				//}
				
				$('#selected_statuses').html(statOptions);
			});
		</script>

