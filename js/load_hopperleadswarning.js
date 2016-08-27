/*
* campaign id
*/
    function load_campaigns_resources(){
    $.ajax({
        url: "./php/APIs/API_GetCampaignsResources.php",
        cache: false,
        success: function(data){
            $("#refresh_campaigns_resources").html(data);
            goAvatar._init(goOptions);
        } 
    });
    }
    
    function load_campaigns_monitoring(){
    $.ajax({        
        url: "./php/APIs/API_GetCampaignsMonitoring.php",
        cache: false,
        dataType: 'json',
        success: function(values){
            //console.log(values);
            //$("#refresh_agents_monitoring_summary").html(data);
                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                //console.log(JSONStringrealtime);
                //console.log(JSONObjectrealtime); 
                var table = $('#campaigns_monitoring_table').dataTable({ 
                                data:JSONObjectrealtime,
                                "destroy":true,    
                                stateSave: true,
                                drawCallback: function(settings) {
                                    var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                                    pagination.toggle(this.api().page.info().pages > 1);
                                }                              
                });
                goAvatar._init(goOptions);
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
            goAvatar._init(goOptions);
        } 
    });
    }
    
    function load_view_agent_information(){
        
    var agentiformationid = document.getElementById("modal-username").innerText;
    
    $.ajax({        
        type: 'POST',
        url: "./php/APIs/API_GetAgentInformation.php",
        data: {user: agentiformationid},
        cache: false,
        dataType: 'json',
        success: function(values){
            //console.log(values);            
            //$("#refresh_agents_monitoring_summary").html(data);
                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                //console.log(JSONStringrealtime);
                //console.log(JSONObjectrealtime); 
                var table = $('#view_agent_information_table').dataTable({ 
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
                var table = $('#realtime_agents_monitoring_table').dataTable({ 
                                data:JSONObjectrealtime,
                                "destroy":true,
                                "searching": false,
                                stateSave: true,
                                drawCallback: function(settings) {
                                    var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                                    pagination.toggle(this.api().page.info().pages > 1);
                                },                                
                                "oLanguage": {
                                        "sLengthMenu": "",
                                        "sEmptyTable": "No Agents Available",
                                        "oPaginate": {
                                            "sPrevious": "Prev",
                                            "sNext": "Next"
                                        }
                                },
                                "bFilter": false,
                                "bInfo": false                             
                });
                goAvatar._init(goOptions);
        } 
    });
    }

    function load_realtime_calls_monitoring(){
    $.ajax({
        url: "./php/APIs/API_GetRealtimeCallsMonitoring.php",
        cache: false,
        dataType: 'json',
        success: function(values){
            //$("#refresh_realtime_agents_monitoring").html(values);
                var JSONStringrealtimecalls = values;
                var JSONObjectrealtimecalls = JSON.parse(JSONStringrealtimecalls);
                //console.log(JSONStringrealtime);
                //console.log(JSONObjectrealtimecalls); 
                var table = $('#realtime_calls_monitoring_table').dataTable({ 
                                data:JSONObjectrealtimecalls,
                                "destroy":true,
                                "searching": false,
                                stateSave: true,
                                drawCallback: function(settings) {
                                    var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                                    pagination.toggle(this.api().page.info().pages > 1);
                                }                                
//                                "oLanguage": {
//                                        "sLengthMenu": "",
//                                        "sEmptyTable": "No Calls Available",
//                                        "oPaginate": {
//                                            "sPrevious": "Prev",
//                                            "sNext": "Next"
//                                        }
//                                },
//                                "bFilter": false
//                                "bInfo": false                                                               
//                                "columnDefs": [
//                                    {
//                                        "targets": [ 6 ],
//                                        "visible": false,
//                                        "searchable": false
//                                    }
//                                ]
                });
                goAvatar._init(goOptions);
        } 
    });
    }    
