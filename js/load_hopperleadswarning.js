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
     dataType: 'json',
     success: function(values){
        //$("#refresh_realtime_agents_monitoring").html(values);
            var JSONStringrealtime = values;
            var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
            //console.log(JSONStringrealtime);
            //console.log(JSONObjectrealtime); 
            var table = $('#monitoring_table').dataTable({ 
                            data:JSONObjectrealtime,
                            "destroy":true
                         });
            table.destroy();
            
                         $('#monitoring_table').dataTable({ 
                            data:JSONObjectrealtime,
                            "destroy":true
                         });        
                         //console.log(values);
     } 
   });
}
