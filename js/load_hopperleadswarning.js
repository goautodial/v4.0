/*
* campaign id
*/
function load_campaign_name(){
   $.ajax({
     url: "./php/APIs/API_GetHopperLeadsWarning.php",
     cache: false,
     success: function(data){
        $("#refresh_campaign_name").html(data);
     } 
   });
}

function load_online_agents(){
   $.ajax({
     url: "./php/APIs/API_GetOnlineAgents.php",
     cache: false,
     success: function(data){
        $("#refresh_online_agents").html(data);
     } 
   });
}



function load_agent_info(){
   $.ajax({
     type: 'POST',
     url: "./php/ViewUserInfo.php",
     data: {userid: id},
     cache: false,
     success: function(data){
        $("#refresh_agent_info").html(data);
     } 
   });
}

function load_realtime_agents_monitoring(){
   $.ajax({
     url: "./php/APIs/API_GetAgentsMonitoring.php",
     cache: false,
     success: function(data){
        $("#refresh_agents_monitoring").html(data);
     } 
   });
}
