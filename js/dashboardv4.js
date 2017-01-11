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
							},
							"columnDefs": [
								{
									className: "hidden-xs", 
									"targets": [ 1, 4, 5 ] 
								}
							]                                
			});
			table.fnProcessingIndicator();
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
				var JSONStringcluster = values;
				var JSONObjectcluster = JSON.parse(JSONStringcluster);
				//console.log(JSONStringrealtime);
				//console.log(JSONObjectrealtime); 
				var table = $('#cluster-status').dataTable({ 
								data:JSONObjectcluster,
								"paging":   false,
								"bPaginate": false,
								"searching": false,
								"bInfo" : false,
								"destroy":true,
								"columnDefs": [
									{
										className: "hidden-xs", 
										"targets": [ 1, 2, 3, 5 ] 
									}
								]                                                                 
				});
				goAvatar._init(goOptions);
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
					//"searching": false,
					stateSave: true,
					drawCallback: function(settings) {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					},                                
					//"oLanguage": {
							//"sLengthMenu": "",
							//"sEmptyTable": "No Agents Available",
							//"oPaginate": {
								//"sPrevious": "Prev",
								//"sNext": "Next"
							//}
					//},
					//"bFilter": false,
					//"bInfo": false,
					"columnDefs": [
						{
							className: "hidden-xs", 
							"targets": [ 2, 3, 4 ] 
						}
					]
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
                                //"searching": false,
                                stateSave: true,
                                drawCallback: function(settings) {
                                    var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                                    pagination.toggle(this.api().page.info().pages > 1);
                                },
                                //"oLanguage": {
                                        //"sLengthMenu": "",
                                        //"sEmptyTable": "No Agents Available",
                                        //"oPaginate": {
                                            //"sPrevious": "Prev",
                                            //"sNext": "Next"
                                        //}
                                //},
                                //"bFilter": false,
                                //"bInfo": false,                                
                                "columnDefs": [
                                    {
                                        className: "hidden-xs", 
                                        "targets": [ 1, 3, 4 ] 
                                    }
                                ]
                });
                goAvatar._init(goOptions);
        } 
    });
    } 
    
    function load_realtime_sla_monitoring(){
    $.ajax({
        url: "./php/APIs/API_GetRealtimeSLAMonitoring.php",
        cache: false,
        dataType: 'json',
        success: function(values){
            //$("#refresh_realtime_agents_monitoring").html(values);
                var JSONStringrealtimesla = values;
                var JSONObjectrealtimesla = JSON.parse(JSONStringrealtimesla);
                //console.log(JSONStringrealtimesla);
                //console.log(JSONObjectrealtimesla); 
                var table = $('#realtime_sla_monitoring_table').dataTable({ 
                                data:JSONObjectrealtimesla,
                                "destroy":true,
                                //"searching": false,
                                stateSave: true,
                                drawCallback: function(settings) {
                                    var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                                    pagination.toggle(this.api().page.info().pages > 1);
                                },
                                "oLanguage": {
                                        "sLengthMenu": "",
                                        "sEmptyTable": "No Data Available",
                                        "oPaginate": {
                                            "sPrevious": "Prev",
                                            "sNext": "Next"
                                        }
                                },
                                "bFilter": false,
                                "bInfo": false,                                
                                "columnDefs": [
                                    {
                                        className: "hidden-xs", 
                                        "targets": [ 1,2,3, 4 ] 
                                    }
                                ]
                });
                goAvatar._init(goOptions);
        } 
    });
    }     

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
    
    function load_TotalInboundCalls(){
    $.ajax({
        url: "./php/APIs/API_GetTotalInboundCalls.php",
        cache: false,
        success: function(data){
            $("#refresh_TotalInCalls").html(data);
        } 
    });
    }

    function load_TotalOutboundCalls(){
    $.ajax({
        url: "./php/APIs/API_GetTotalOutboundCalls.php",
        cache: false,
        success: function(data){
            $("#refresh_TotalOutCalls").html(data);
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
        
