/*
* campaign id
*/
    function load_campaigns_resources(){
    $.ajax({
        url: "./php/dashboard/API_getCampaignsResources.php",
        cache: false,
        success: function(data){
            $("#refresh_campaigns_resources").html(data);
            goAvatar._init(goOptions);
        }
    });
    }

    function load_campaigns_monitoring(){
    $.ajax({
        url: "./php/dashboard/API_getCampaignsMonitoring.php",
        cache: false,
        dataType: 'json',
        success: function(values){
			var JSONString = values;
			var JSONObject = JSON.parse(JSONString);
			$('#campaigns_monitoring_table').DataTable({
				destroy: true,
				data: JSONObject,
				responsive: true,
				stateSave: true,
				drawCallback: function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},
			});
			//table.fnProcessingIndicator();
			goAvatar._init(goOptions);
        }
    });
    }

    function load_agents_monitoring_summary(){
		$.ajax({
			url: "./php/dashboard/API_getAgentsMonitoringSummary.php",
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_agents_monitoring_summary").html(data);
				goAvatar._init(goOptions);
			}
		});
    }

    function load_totalAgentsStatistics(){
		$.ajax({
			url: "./php/dashboard/API_getTotalAgentsStatistics.php",
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_agents_statistics").html(data);
			}
		});
    }

    function load_agent_sales(){
		 $.ajax({
			url: "./php/dashboard/API_getSalesAgent.php",
			//cache: false,
			dataType: 'json',
			success: function(data){
				var JSONStringSalesAgent = data;
				var JSONObjectSalesAgent = JSON.parse(JSONStringSalesAgent);
				$('#agent-sales').DataTable({
						destroy: true,
						responsive: true,
						data: JSONObjectSalesAgent,
						searching: false,
						filter: false,
						info: false,
						paging: false,
						paginate: false,
						stateSave: true,
						drawCallback: function() {
								var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
								pagination.toggle(this.api().page.info().pages > 1);
						},
						columnDefs:[
								{ searchable: false, targets: 0 },
								{ sortable: false, targets: 0 }
						]
				});
				goAvatar._init(goOptions);
			}
		});
    }

    var agentInformationRequest = null;

    function load_view_agent_information(){
        var agentInformationModal = $("#modal_view_agent_information");
        var user = $.trim($("#modal-username").text());

        if (!agentInformationModal.hasClass("in") || user === "" || agentInformationRequest !== null) {
            return;
        }

        agentInformationRequest = $.ajax({
            type: "POST",
            url: "./php/dashboard/API_getAgentInformation.php",
            data: {
                user: user,
                filter: "userInfo"
            },
            cache: false,
            dataType: "json",
            success: function(data){
                if (!agentInformationModal.hasClass("in") || $.trim($("#modal-username").text()) !== user) {
                    return;
                }

                $("#view_agent_information_table").DataTable({
                    data: data,
                    "paging": false,
                    "bPaginate": false,
                    "searching": false,
                    "bInfo": false,
                    "destroy": true
                });
            },
            complete: function(){
                agentInformationRequest = null;
            }
        });
    }

    function load_cluster_status() {
		$.ajax({
			url: "./php/dashboard/API_getClusterStatus.php",
			//cache: false,
			dataType: 'json',
			success: function(values){
				var JSONStringcluster = values;
				var JSONObjectcluster = JSON.parse(JSONStringcluster);
				$('#cluster-status').DataTable({
					destroy: true,
					responsive: true,
					data: JSONObjectcluster,
					searching: false,
					filter: false,
					info: false,
					paging: false,
					paginate: false,
					stateSave: true,
					drawCallback: function() {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					},
					columnDefs:[
						{ searchable: false, targets: 0 },
						{ sortable: false, targets: 0 },
						{ className: "hidden-xs", targets: [ 1, 2, 3, 5 ] }
					]
				});
				goAvatar._init(goOptions);
			}
		});
    }

    var realtimeAgentsMonitoringRequest = null;
    var realtimeCallsMonitoringRequest = null;
    var realtimeInboundMonitoringRequest = null;

    function load_realtime_agents_monitoring(){
        var modal = $("#realtime_agents_monitoring");

        if (!modal.hasClass("in") || realtimeAgentsMonitoringRequest !== null) {
            return;
        }

        realtimeAgentsMonitoringRequest = $.ajax({
            url: "./php/dashboard/API_getRealtimeAgentsMonitoring.php",
            cache: false,
            dataType: 'json',
            success: function(values){
                if (!modal.hasClass("in")) {
                    return;
                }

                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                $('#realtime_agents_monitoring_table').DataTable({
                    destroy:true,
                    responsive:true,
                    data:JSONObjectrealtime,
                    stateSave: true,
                    drawCallback: function() {
                        var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                        pagination.toggle(this.api().page.info().pages > 1);
                    }
                });
                goAvatar._init(goOptions);
            },
            complete: function(){
                realtimeAgentsMonitoringRequest = null;
            }
        });
    }

    function load_realtime_calls_monitoring(){
        var modal = $("#realtime_calls_monitoring");

        if (!modal.hasClass("in") || realtimeCallsMonitoringRequest !== null) {
            return;
        }

        realtimeCallsMonitoringRequest = $.ajax({
            url: "./php/dashboard/API_getRealtimeCallsMonitoring.php",
            cache: false,
            dataType: 'json',
            success: function(values){
                if (!modal.hasClass("in")) {
                    return;
                }

                var JSONString = values;
                var JSONObject = JSON.parse(JSONString);
                $('#realtime_calls_monitoring_table').DataTable({
                    destroy:true,
                    responsive:true,
                    data:JSONObject,
                    stateSave: true,
                    drawCallback: function() {
                        var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                        pagination.toggle(this.api().page.info().pages > 1);
                    }
                });
                goAvatar._init(goOptions);
            },
            complete: function(){
                realtimeCallsMonitoringRequest = null;
            }
        });
    }

    function load_realtime_inbound_monitoring(inbTable){
        var modal = $("#realtime_inbound_monitoring");

        if (!modal.hasClass("in") || realtimeInboundMonitoringRequest !== null) {
            return;
        }

        realtimeInboundMonitoringRequest = $.ajax({
            url: "./php/dashboard/API_getRealtimeInboundMonitoring.php?ingroup=" + $("#inbound_filter").val(),
            cache: false,
            dataType: 'json',
            success: function(values){
                if (!modal.hasClass("in")) {
                    return;
                }

                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                $('#realtime_inbound_monitoring_table').DataTable({
                    destroy:true,
                    responsive:true,
                    searching: false,
                    order: [[ 5, "desc" ]],
                    data:JSONObjectrealtime,
                    stateSave: true,
                    drawCallback: function() {
                        var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                        pagination.toggle(this.api().page.info().pages > 1);
                    }
                });
                goAvatar._init(goOptions);
            },
            complete: function(){
                realtimeInboundMonitoringRequest = null;
            }
        });
    }

    function load_realtime_sla_monitoring(){
    $.ajax({
        url: "./php/dashboard/API_getRealtimeSLAMonitoring.php",
        cache: false,
        dataType: 'json',
        success: function(values){
            //$("#refresh_realtime_agents_monitoring").html(values);
			var JSONStringrealtimesla = values;
			var JSONObjectrealtimesla = JSON.parse(JSONStringrealtimesla);
			var table = $('#realtime_sla_monitoring_table').DataTable({
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
 * * WhatsApp box
 * */
    function load_whatsapp_realtime_monitoring(){
    $.ajax({
        url: "./php/dashboard/API_getWhatsAppRealtimeMonitoring.php",
        cache: false,
        success: function(data){
	    var data = JSON.parse(data);
            $("#refresh_totalagentschat").html(data.active_agents);
            $("#refresh_totalagentswaitchats").html(data.waiting_agents);
            $("#refresh_totalagentspausedchat").html(data.paused_agents);
            $("#refresh_totalunreadchats").html(data.unread_chats);
            $("#refresh_totalqueuechats").html(data.in_queue_chats);
            $("#refresh_totalactivechats").html(data.active_chats);
        }
    });
    }

    function load_whatsapp_agents_chat_monitoring(){
		$.ajax({
			url: "./php/dashboard/API_getWhatsAppUsersSummary.php",
			cache: false,
			dataType: 'json',
			success: function(values){
				var JSONStringrealtime = values;
				var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
				$('#realtime_agents_chat_monitoring_table').DataTable({
					destroy:true,
					responsive:true,
					data:JSONObjectrealtime,
					stateSave: true,
					drawCallback: function() {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					}
				});
				goAvatar._init(goOptions);
			}
		});
    }

    function load_whatsapp_chat_monitoring(){
		$.ajax({
			url: "./php/dashboard/API_getWhatsAppChatSummary.php",
			cache: false,
			dataType: 'json',
			success: function(values){
				var JSONStringrealtime = values;
				var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
				$('#realtime_chats_monitoring_table').DataTable({
					destroy:true,
					responsive:true,
					data:JSONObjectrealtime,
					stateSave: true,
					drawCallback: function() {
						var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
						pagination.toggle(this.api().page.info().pages > 1);
					}
				});
			}
		});
    }

    function load_whatsapp_assigned_chats(userid){
                $.ajax({
                        url: "./php/dashboard/API_getWhatsAppAssignedChats.php",
			type: "POST",
                        cache: false,
			data: {userid:userid},
                        dataType: 'json',
                        success: function(values){
                                var JSONStringrealtime = values;
                                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);
                                $('#assigned_chats_monitoring_table').DataTable({
                                        destroy:true,
                                        responsive:true,
                                        data:JSONObjectrealtime,
                                        stateSave: true,
                                        drawCallback: function() {
                                                var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                                                pagination.toggle(this.api().page.info().pages > 1);
                                        }
                                });
                        }
                 });
    }

    function load_whatsapp_queue(){
                $.ajax({
                        url: "./php/WhatsappQueue.php",
                        cache: false,
                        dataType: 'json',
                        success: function(values){
				//console.log();
                        }
                });
    }

/*
* Agents status box
*/
    function load_totalagentscall(){
    $.ajax({
        url: "./php/dashboard/API_getTotalAgentsCall.php",
        cache: false,
        success: function(data){
            $("#refresh_totalagentscall").html(data);
        }
    });
    }

    function load_totalagentspaused(){
    $.ajax({
        url: "./php/dashboard/API_getTotalAgentsPaused.php",
        cache: false,
        success: function(data){
            $("#refresh_totalagentspaused").html(data);
        }
    });
    }

    function load_totalagentswaitingcall(){
    $.ajax({
        url: "./php/dashboard/API_getTotalAgentsWaitCalls.php",
        cache: false,
        success: function(data){
            $("#refresh_totalagentswaitcalls").html(data);
        }
    });
    }
    var salesTotalsRequest = null;
    var callTotalsRequest = null;

    function setDashboardValue(selector, value){
        $(selector).html(value);
    }

    /*
    * Sales status box
    */
    function load_salesTotals(){
        if (salesTotalsRequest !== null) {
            return;
        }

        salesTotalsRequest = $.ajax({
            url: "./php/dashboard/API_getSalesTotals.php",
            cache: false,
            dataType: "json",
            success: function(data){
                setDashboardValue("#refresh_GetTotalSales", data.totalSales || 0);
                setDashboardValue("#refresh_GetTotalOutSales", data.outSales || 0);
                setDashboardValue("#refresh_GetTotalInSales", data.inSales || 0);
                setDashboardValue("#refresh_GetInSalesHour", data.inSalesHour || 0);
                setDashboardValue("#refresh_GetOutSalesHour", data.outSalesHour || 0);
            },
            complete: function(){
                salesTotalsRequest = null;
            }
        });
    }

    function load_totalSales(){
		$.ajax({
			type: "POST",
			url: "./php/dashboard/API_getTotalSales.php",
			data: { type: "all-daily" },
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_GetTotalSales").html(data);
			}
		});
    }

    function load_totalOutSales(){
		$.ajax({
			type: "POST",
			url: "./php/dashboard/API_getTotalSales.php",
			data: { type: "out-daily" },
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_GetTotalOutSales").html(data);
			}
		});
	}

	function load_totalInSales(){
		$.ajax({
			type: "POST",
			url: "./php/dashboard/API_getTotalSales.php",
			data: { type: "in-daily" },
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_GetTotalInSales").html(data);
			}
		});
	}

    function load_INSalesHour(){
		$.ajax({
			type: "POST",
			url: "./php/dashboard/API_getTotalSales.php",
			data: { type: "in-hourly" },
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_GetInSalesHour").html(data);
			}
		});
    }

    function load_OUTSalesPerHour(){
		$.ajax({
			type: "POST",
			url: "./php/dashboard/API_getTotalSales.php",
			data: { type: "out-hourly" },
			cache: false,
			success: function(data){
				//console.log(data);
				$("#refresh_GetOutSalesHour").html(data);
			}
		});
    }
    /*
    * Leads status box
    */
    function load_TotalActiveLeads(){
    $.ajax({
        url: "./php/dashboard/API_getTotalActiveLeads.php",
        cache: false,
        success: function(data){
            $("#refresh_GetTotalActiveLeads").html(data);
        }
    });
    }

    function load_LeadsinHopper(){
    $.ajax({
        url: "./php/dashboard/API_getLeadsinHopper.php",
        cache: false,
        success: function(data){
            $("#refresh_GetLeadsinHopper").html(data);
        }
    });
    }

    function load_TotalDialableLeads(){
    $.ajax({
        url: "./php/dashboard/API_getTotalDialableLeads.php",
        cache: false,
        success: function(data){
            $("#refresh_GetTotalDialableLeads").html(data);
        }
    });
    }
    /*
    * Calls status box
    */
    function load_callTotals(){
        if (callTotalsRequest !== null) {
            return;
        }

        callTotalsRequest = $.ajax({
            url: "./php/dashboard/API_getCallTotals.php",
            cache: false,
            dataType: "json",
            success: function(data){
                setDashboardValue("#refresh_RingingCalls", data.ringingCalls || 0);
                setDashboardValue("#refresh_IncomingQueue", data.incomingQueue || 0);
                setDashboardValue("#refresh_AnsweredCalls", data.answeredCalls || 0);
                setDashboardValue("#refresh_DroppedCalls", data.droppedCalls || 0);
                setDashboardValue("#refresh_TotalInCalls", data.inboundCalls || 0);
                setDashboardValue("#refresh_TotalOutCalls", data.outboundCalls || 0);
                setDashboardValue("#refresh_LiveOutbound", data.liveOutbound || 0);

                var droppedPercentage = parseFloat(data.droppedPercentage || 0);
                var droppedPercentageColor = droppedPercentage >= 10 ? "#f05050" : "#5d9cec";
                if (droppedPercentage > 100) {
                    droppedPercentage = 100;
                }

                $("#refresh_DroppedCallsPercentage").trigger("configure", { fgColor: droppedPercentageColor });
                $("#refresh_DroppedCallsPercentage").val(droppedPercentage);
                $("#refresh_DroppedCallsPercentage").trigger("change");
            },
            complete: function(){
                callTotalsRequest = null;
            }
        });
    }

    function load_TotalCalls(){
    $.ajax({
		type: "POST",
        url: "./php/dashboard/API_getTotalCalls.php",
		data: { type: "all" },
        cache: false,
        success: function(data){
			//console.log(data);
            $("#refresh_TotalCalls").html(data);
        }
    });
    }

    function load_TotalInboundCalls(){
    $.ajax({
		type: "POST",
		url: "./php/dashboard/API_getTotalCalls.php",
		data: { type: "inbound" },
        cache: false,
        success: function(data){
			//console.log(data);
            $("#refresh_TotalInCalls").html(data);
        }
    });
    }

    function load_TotalOutboundCalls(){
    $.ajax({
		type: "POST",
		url: "./php/dashboard/API_getTotalCalls.php",
		data: { type: "outbound" },
        cache: false,
        success: function(data){
			//console.log(data);
            $("#refresh_TotalOutCalls").html(data);
        }
    });
    }

    function load_RingingCalls(){
    $.ajax({
        url: "./php/dashboard/API_getTotalRingingCalls.php",
        cache: false,
        success: function(data){
            $("#refresh_RingingCalls").html(data);
        }
    });
    }
    function load_IncomingQueue(){
    $.ajax({
        url: "./php/dashboard/API_getIncomingQueue.php",
        cache: false,
        success: function(data){
            $("#refresh_IncomingQueue").html(data);
        }
    });
    }
    function load_AnsweredCalls(){
    $.ajax({
        url: "./php/dashboard/API_getTotalAnsweredCalls.php",
        cache: false,
        success: function(data){
            $("#refresh_AnsweredCalls").html(data);
        }
    });
    }
    function load_DroppedCalls(){
    $.ajax({
        url: "./php/dashboard/API_getTotalDroppedCalls.php",
        cache: false,
        success: function(data){
            $("#refresh_DroppedCalls").html(data);
        }
    });
    }
    function load_DroppedCallsPercentage(){
    $.ajax({
        url: "./php/dashboard/API_getDroppedPercentage.php",
        cache: false,
        success: function(data){
            $("#refresh_DroppedCallsPercentage").val(data);
	    $("#refresh_DroppedCallsPercentage").trigger("change");
        }
    });
    }
    function load_LiveOutbound(){
    $.ajax({
        url: "./php/dashboard/API_getLiveOutbound.php",
        cache: false,
        success: function(data){
            $("#refresh_LiveOutbound").html(data);
        }
    });
    }
