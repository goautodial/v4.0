/*
* campaign id
*/
    function load_campaigns_resources(){
    $.ajax({
        url: "./php/APIs/API_GetCampaignsResources.php",
        cache: false,
        success: function(data){
            $("#refresh_campaigns_resources").html(data);
        } 
    });
    }

    function load_agents_monitoring_summary(){
    $.ajax({
        url: "./php/APIs/API_GetAgentsMonitoringSummary.php",
        cache: false,
        success: function(data){
            //console.log(data);
            $("#refresh_agents_monitoring_summary").html(data);
        } 
    });
    }
    
    function load_cluster_status(){
    $.ajax({
        url: "./php/APIs/API_GetClusterStatus.php",
        cache: false,
        dataType: 'json',
        success: function(values){
            //console.log(data);
            //$("#refresh_cluster_status").html(values);
                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                //console.log(JSONStringrealtime);
                //console.log(JSONObjectrealtime); 
                var table = $('#cluster-status').dataTable({ 
                                data:JSONObjectrealtime,
                                "paging":   false,
                                "bPaginate": false,
                                "searching": false,
                                "bInfo" : false,
                                "destroy":true
                                
                            });
        } 
    });
    }    

//    Moved to index.php - onclick-userinfo
//    function load_view_agent_information(){
//    $.ajax({
//        url: "./php/ViewUserInfo.php",
//        data: {user_id: userid},
//        cache: false,
//        success: function(data){
//            console.log(data);
//            $("#refresh_view_agent_information").html(data);
//        } 
//    });
//    }    

    function load_realtime_agents_monitoring(){
    $.ajax({
        url: "./php/APIs/API_GetRealtimeAgentsMonitoring.php",
        cache: false,
        dataType: 'json',
        success: function(values){
            //$("#refresh_realtime_agents_monitoring").html(values);
                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                //console.log(JSONStringrealtime);
                //console.log(JSONObjectrealtime); 
                //var table = $('#monitoring_table').dataTable({ 
                                //data:JSONObjectrealtime,
                                //"destroy":true
                            //});
                //table.destroy();
                
                            $('#monitoring_table').dataTable({ 
                                data:JSONObjectrealtime,
                                "destroy":true
                            });        
                            //console.log(values);
        } 
    });
    }
