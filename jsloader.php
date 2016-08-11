<?php
########################################################################################################
####  Name:             	gadv4_jsloader.php                                                  ####
####  Version:          	0.9                                                            	    ####
####  Copyright:        	GOAutoDial Inc. (c) 2011-2016 - GoAutoDial Open Source Community    ####
####  Written by:	        Demian Lizandro A. Biscocho                                         ####
####  License:          	AGPLv3                                                              ####
########################################################################################################

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');

## initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

?>

<script type="text/javascript">

<?php

$calls_ringing = $ui->API_goGetRingingCalls();
$answered_calls = $ui->API_goGetTotalAnsweredCalls();
$total_calls_today = $ui->API_goGetTotalCalls();
$dropped_calls_today = $ui->API_goGetTotalDroppedCalls();
$calls_per_hour = $ui->API_goGetCallsPerHour();
$total_agents_call = $ui->API_goGetTotalAgentsCall();
$realtimeAgents = $ui->API_getRealtimeAgent();
//var_dump($total_agents_call);    
//die(dd);
?>

function load_realtimemonitoring(){
   $.ajax({
     url: "<?php echo $realtimeAgents; ?>",
     cache: false,
     success: function(data){
        $("#agent_monitoring_table").html(data);
     } 
   });
}

function load_totalagentscall(){
   $.ajax({
     url: "<?php echo $total_agents_call; ?>",
     cache: false,
     success: function(data){
        $("#refresh_totalagentscall").html(data);
     } 
   });
}

function load_totalagentspaused(){
   $.ajax({
     url: "./php/APIs/API_GetTotalAgentsPaused.php",
     cache: false,
     success: function(data){
        $("#refresh_totalagentspaused").html(data);
     } 
   });
}

function load_totalagentswaitingcall(){
   $.ajax({
     url: "./php/APIs/API_getTotalAgentsWaitCalls.php",
     cache: false,
     success: function(data){
        $("#refresh_totalagentswaitcalls").html(data);
     } 
   });
}
/*
* Sales status box 
*/
function load_totalSales(){
   $.ajax({
     url: "./php/APIs/API_GetTotalSales.php",
     cache: false,
     success: function(data){
        $("#refresh_GetTotalSales").html(data);
     } 
   });
}

function load_INSalesHour(){
   $.ajax({
     url: "./php/APIs/API_GetINSalesHour.php",
     cache: false,
     success: function(data){
        $("#refresh_GetINSalesHour").html(data);
     } 
   });
}

function load_OUTSalesPerHour(){
   $.ajax({
     url: "./php/APIs/API_GetOUTSalesPerHour.php",
     cache: false,
     success: function(data){
        $("#refresh_GetOUTSalesPerHour").html(data);
     } 
   });
}
/*
* Leads status box 
*/
function load_TotalActiveLeads(){
   $.ajax({
     url: "./php/APIs/API_GetTotalActiveLeads.php",
     cache: false,
     success: function(data){
        $("#refresh_GetTotalActiveLeads").html(data);
     } 
   });
}

function load_LeadsinHopper(){
   $.ajax({
     url: "./php/APIs/API_GetLeadsinHopper.php",
     cache: false,
     success: function(data){
        $("#refresh_GetLeadsinHopper").html(data);
     } 
   });
}

function load_TotalDialableLeads(){
   $.ajax({
     url: "./php/APIs/API_GetTotalDialableLeads.php",
     cache: false,
     success: function(data){
        $("#refresh_GetTotalDialableLeads").html(data);
     } 
   });
}
/*
* Calls status box 
*/
function load_Totalcalls(){
   $.ajax({
     url: "./php/APIs/API_getTotalcalls.php",
     cache: false,
     success: function(data){
        $("#refresh_Totalcalls").html(data);
     } 
   });
}

function load_RingingCall(){
   $.ajax({
     url: "./php/APIs/API_GetRingingCall.php",
     cache: false,
     success: function(data){
        $("#refresh_RingingCall").html(data);
     } 
   });
}

function load_LiveOutbound(){
   $.ajax({
     url: "./php/APIs/API_GetLiveOutbound.php",
     cache: false,
     success: function(data){
        $("#refresh_LiveOutbound").html(data);
     } 
   });
}
</script>
