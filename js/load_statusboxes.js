/*
* Agents status box 
*/
function load_totalagentscall(){
   $.ajax({
     url: "./php/APIs/API_GetTotalAgentsCall.php",
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
function load_TotalCalls(){
   $.ajax({
     url: "./php/APIs/API_GetTotalCalls.php",
     cache: false,
     success: function(data){
        $("#refresh_TotalCalls").html(data);
     } 
   });
}

function load_RingingCalls(){
   $.ajax({
     url: "./php/APIs/API_GetTotalRingingCalls.php",
     cache: false,
     success: function(data){
        $("#refresh_RingingCalls").html(data);
     } 
   });
}
function load_IncomingQueue(){
   $.ajax({
     url: "./php/APIs/API_GetIncomingQueue.php",
     cache: false,
     success: function(data){
        $("#refresh_IncomingQueue").html(data);
     } 
   });
}
function load_AnsweredCalls(){
   $.ajax({
     url: "./php/APIs/API_GetTotalAnsweredCalls.php",
     cache: false,
     success: function(data){
        $("#refresh_AnsweredCalls").html(data);
     } 
   });
}
function load_DroppedCalls(){
   $.ajax({
     url: "./php/APIs/API_GetTotalDroppedCalls.php",
     cache: false,
     success: function(data){
        $("#refresh_DroppedCalls").html(data);
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
