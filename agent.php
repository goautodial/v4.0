<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

// check if Creamy has been installed.
require_once('./php/CRMDefaults.php');
if (!file_exists(CRM_INSTALLED_FILE)) { // check if already installed 
	header("location: ./install.php");
	die();
}

// Try to get the authenticated user.
require_once('./php/Session.php');
try {
	$user = \creamy\CreamyUser::currentUser();
} catch (\Exception $e) {
	header("location: ./logout.php");
	die();
}

// proper user redirects
if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_AGENT){
	if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_ADMIN){
		header("location: index.php");
	}
}

// initialize session and DDBB handler
include_once('./php/UIHandler.php');
require_once('./php/LanguageHandler.php');
require_once('./php/DbHandler.php');
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$colors = $ui->generateStatisticsColors();

// calculate number of statistics and customers
$db = new \creamy\DbHandler();
$statsOk = $db->weHaveSomeValidStatistics();
$custsOk = $db->weHaveAtLeastOneCustomerOrContact();

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
     
		<!--<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />-->
		
        <!-- Creamy style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->

		<!-- javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
	    <!-- ChartJS 1.0.1 -->
	    <script src="js/plugins/chartjs/Chart.min.js" type="text/javascript"></script>
		
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		
		<!-- Circle Buttons style -->
		<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

         <!-- theme_dashboard folder -->
					<!-- FONT AWESOME-->
			<link rel="stylesheet" href="theme_dashboard/fontawesome/css/font-awesome.min.css">
					<!-- SIMPLE LINE ICONS-->
			<link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">
					<!-- ANIMATE.CSS-->
			<link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">
					<!-- WHIRL (spinners)-->
			<link rel="stylesheet" href="theme_dashboard/whirl/dist/whirl.css">
				<!-- =============== PAGE VENDOR STYLES ===============-->
					<!-- WEATHER ICONS-->
			<link rel="stylesheet" href="theme_dashboard/weather-icons/css/weather-icons.min.css">
				<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">
		<!-- fullCalendar 2.2.5-->
	    <link href="css/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
	    <link href="css/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
	    <!-- fullCalendar 2.2.5 -->
	    <script src="js/plugins/fullcalendar/moment.min.js" type="text/javascript"></script>
	    <script src="js/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
	    <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).load(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
    </head>
    <?php print $ui->creamyBody(); ?>
        <div data-ui-view="" data-autoscroll="false" class="wrapper ng-scope">
	        <!-- header logo: style can be found in header.less -->
			<?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

             <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-heading">
					<!-- Page title -->
                    <?php $lh->translateText("Agent Dashboard"); ?>
                    <small class="ng-binding animated fadeInUpShort">Welcome to Goautodial !</small>
                </section>

                <!-- Main content -->
                <section class="content">

					<!-- Update (if needed) -->
                    <?php
						require_once('./php/Updater.php');
						$upd = \creamy\Updater::getInstance();
						$currentVersion = $upd->getCurrentVersion();
						if (!$upd->CRMIsUpToDate()) {
					?>
                    <div class="row">
                        <section class="col-lg-12">
                            <!-- version -->
                            <div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-refresh"></i>
                                    <h3 class="box-title"><?php print $lh->translationFor("version")." ".number_format($currentVersion, 1); ?></h3>
                                </div>
                                <div class="box-body">
									<?php
									if ($upd->canUpdateFromVersion($currentVersion)) { // update needed
										$contentText = $lh->translationFor("you_need_to_update");
										print $ui->formWithContent(
											"update_form", 						// form id
											$contentText, 						// form content
											$lh->translationFor("update"), 		// submit text
											CRM_UI_STYLE_DEFAULT,				// submit style
											CRM_UI_DEFAULT_RESULT_MESSAGE_TAG,	// resulting message tag
											"update.php");						// form PHP action URL.
									} else { // we cannot update?
										$lh->translateText("crm_update_impossible");
									}
									?>
                                </div>
                            </div>
                        </section>
                    </div>   <!-- /.row -->
					<?php } ?>

                    <!-- Status boxes -->
					<div class="row">
						<?php print $ui->dashboardInfoBoxes($user->getUserId()); ?>
						<div class="col-lg-3 col-md-6 col-sm-12 animated fadeInUpShort">
							<!-- date widget    -->
							<div class="panel widget" style="height: 87px;">
								<div class="col-xs-4 text-center bg-green pv-lg">
								<!-- See formats: https://docs.angularjs.org/api/ng/filter/date-->
									<div class="text-sm"><?php echo date("F", time());?></div>
									<div class="h2 mt0"><?php echo date("d", time());?></div>
								</div>
								<div class="col-xs-8 pv-lg">
									<div class="text-uppercase"><?php echo date("l", time());?></div>
									<div class="h3 mt0"><?php echo date("h:i", time());?> 
										<span class="text-muted text-sm"><?php echo date("A", time());?></span>
									</div>
								</div>
							</div>
							<!-- END date widget    -->
						</div>
			        </div><!-- /.row -->                    
			       
                     <!-- Statistics -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-md-4"> 
				              <div class="box box-default">
				                <div class="box-body no-padding">
				                  <!-- THE CALENDAR -->
				                  <div id="calendar"></div>
				                </div><!-- /.box-body -->
				              </div><!-- /. box -->
                        </section><!-- /.Left col -->
						<!-- Left col -->
                        <section class="col-md-5"> 
	                    	<!-- Gráfica de clientes -->   
	                        <div class="box box-default">
	                            <div class="box-header">
	                                <i class="fa fa-bar-chart-o"></i>
	                                <h3 class="box-title"><?php $lh->translateText("New Contacts"); ?></h3>
	                            </div>
                                <div class="box-body" id="graph-box">
		                            <?php if ($custsOk) { ?>
	                                <div class="row">
										<div class="col-md-8">
											<canvas id="pieChart" height="250"></canvas>
		                            	</div>
		                            	<div class="col-md-4 chart-legend" id="customers-chart-legend">
		                            	</div>
	                                 </div>
		                            <?php } else { 
			                        	print $ui->calloutWarningMessage($lh->translationFor("no_customers_yet"));
			                        	print $ui->simpleLinkButton("no_customers_add_customer", $lh->translationFor("create_new"), "customerslist.php?customer_type=clients_1");
			                        } ?>
	                            </div>
	                        </div>
							
                        </section><!-- /.Left col -->
						
                    </div><!-- /.row (main row) -->
				
					<?php print $ui->hooksForDashboard(); ?>
					
					
					<div class="bottom-menu skin-blue">
						<?php print $ui->getCircleButton("calls", "plus"); ?>
						<div class="fab-div-area" id="fab-div-area">
								<ul class="fab-ul" style="height: 250px;">
									<li class="li-style"><a class="fa fa-dashboard fab-div-item" data-toggle="modal" data-target="#add_campaigns_modal"></a></li><br/>
									<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_users"> </a></li>
								</ul>
							</div>
					</div>
					<div class="modal fade" id="add_campaigns_modal" name="add_campaigns_modal" tabindex="-1" role="dialog" aria-hidden="true">
			        <div class="modal-dialog">
			            <div class="modal-content">
						
			                <div class="modal-header">
			                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translateText("Campaign Wizard"); ?></b></h4>
			                </div>

			                <form action="" method="post" name="" id="">
			                    <div class="modal-body">
			                        <div class="form-group">
										<center><h4><b><?php $lh->translateText("Step 1  » Outbound"); ?> </b></h4></center>
			                            <label for="campaign_type"><?php $lh->translateText("Campaign Type"); ?></label>
			                            <input type="text required" class="form-control" id="campaign_type" name="campaign_type" placeholder="<?php $lh->translateText("Campaign Type"); ?>">
										
										<label for="campaign_id"><?php $lh->translateText("Campaign ID"); ?></label>
			                            <input type="text required" class="form-control" id="campaign_id" name="campaign_id" placeholder="<?php $lh->translateText("Campaign ID"); ?>">
										
										<label for="campaign_name"><?php $lh->translateText("Campaign Name"); ?></label>
			                            <input type="text required" class="form-control" id="campaign_name" name="campaign_name" placeholder="<?php $lh->translateText("Campaign Name"); ?>">
										
									<hr/>
										<center><h4><b><?php $lh->translateText("Step 2  » Load Leads"); ?> </b></h4></center>
										<label for="lead_file"><?php $lh->translateText("Lead File"); ?></label>
			                            <input type="file" class="form-control" id="lead_file" name="lead_file" placeholder="<?php $lh->translateText("Lead File"); ?>">
										
										<label for="list_id"><?php $lh->translateText("List ID"); ?></label>
			                            <input type="text required" class="form-control" id="list_id" name="list_id" placeholder="<?php $lh->translateText("List ID"); ?>">
										
										<label for="country"><?php $lh->translateText("Country"); ?></label>
			                            <input type="text required" class="form-control" id="country" name="country" placeholder="<?php $lh->translateText("Country"); ?>">
										
										<label for="duplicate_check"><?php $lh->translateText("Check For Duplicates"); ?></label>
			                            <select id="duplicate_check" class="form-control">
											<option>NO DUPLICATE CHECK</option>
											<option>CHECK DUPLICATES BY PHONE IN LIST ID</option>
											<option>CHECK DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>
										</select><br/>
										<button type="button" class="btn"> U P L O A D   L E A D S</button>
										
									<hr/>
										<center><h4><b><?php $lh->translateText("Step 3  » Information"); ?> </b></h4></center>
										<label for="dial_method"><?php $lh->translateText("Dial Method"); ?></label>
										<select id="dial_method" class="form-control">
											<option>MANUAL</option>
											<option>AUTO DIAL</option>
											<option>PREDICTIVE</option>
											<option>INBOUND MAN</option>
										</select>	
											
										<label for="autodial_lvl"><?php $lh->translateText("AutoDial Level"); ?></label>
										<select id="autodial_lvl" class="form-control">
											<option>OFF</option>
											<option>SLOW</option>
											<option>NORMAL</option>
											<option>HIGH</option>
											<option>MAX</option>
											<option>MAX PREDICTIVE</option>
										</select>
										<label for="carrier_for_campaign"><?php $lh->translateText("Carrier to use for this Campaign"); ?></label>
										<select id="carrier_for_campaign" >
											<option>CUSTOM DIAL PREFIX</option>
										</select>
										<input type="number">
										<br/>
										<label for="answering_machine"><?php $lh->translateText("Answering Machine Detection"); ?></label>
										<select id="answering_machine" class="form-control">
											<option>ON</option>
											<option>OFF</option>
										</select>
									</div>
			                    </div>
			                    <div class="modal-footer clearfix">
			                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="changetaskCancelButton"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
			                        <button type="submit" class="btn btn-primary pull-right" id="changeeventsOkButton"><i class="fa fa-check"></i> <?php $lh->translateText("Add New Campaign"); ?></button>
								</div>
								
			                </form>
							
			            </div><!-- /.modal-content -->
			        </div><!-- /.modal-dialog -->
			    </div><!-- /.modal -->	
				
				
				<!-- USERS MODAL -->
				<div class="modal fade" id="add_users" name="add_users" tabindex="-1" role="dialog" aria-hidden="true">
			        <div class="modal-dialog">
			            <div class="modal-content">

			                <div class="modal-header">
			                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translateText("User Wizard"); ?></b></h4>
			                </div>
							
			                <form action="" method="post" name="" id="">
			                    <div class="modal-body">
									<div class="form-group">
										<center><h4><b><?php $lh->translateText("Step 1  » Add New User"); ?> </b></h4></center>
			                        
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="0"
										aria-valuemin="0" aria-valuemax="100" style="width:0%">
										  0%
										</div>
									</div>
									</div>
			                    </div>
			                    <div class="modal-footer clearfix">
			                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="changetaskCancelButton"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
			                        <button type="submit" class="btn btn-primary pull-right" id="changeeventsOkButton"><i class="fa fa-check"></i> <?php $lh->translateText("Add New Campaign"); ?></button>
								</div>
								
			                </form>
							
			            </div><!-- /.modal-content -->
			        </div><!-- /.modal-dialog -->
			    </div><!-- /.modal -->
				
				
                </section><!-- /.content -->
				
            </aside><!-- /.right-side -->
			
            <?php //print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
		
		
	<script>
		$(document).ready(function(){
			$(".bottom-menu").on('mouseenter mouseleave', function () {
			  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
			});
		});
	</script>
	<!-- Page specific script -->
	    <script type="text/javascript">
	      $(function () {
	
	        /* initialize the external events
	         -----------------------------------------------------------------*/
	        function ini_events(ele) {
	          ele.each(function () {
	
	            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
	            // it doesn't need to have a start or end
	            var eventObject = {
	              title: $.trim($(this).text()) // use the element's text as the event title
	            };
	
	            // store the Event Object in the DOM element so we can get to it later
	            $(this).data('eventObject', eventObject);
	
	            // make the event draggable using jQuery UI
	            $(this).draggable({
	              zIndex: 1070,
	              revert: true, // will cause the event to go back to its
	              revertDuration: 0  //  original position after the drag
	            });
	
	          });
	        }
	        ini_events($('#external-events div.external-event'));
	
	        /* initialize the calendar
	         -----------------------------------------------------------------*/
	        //Date for the calendar events (dummy data)
	        var date = new Date();
	        var d = date.getDate(),
	                m = date.getMonth(),
	                y = date.getFullYear();
	        $('#calendar').fullCalendar({
			  timeFormat: 'HH(:mm)',
 	          header: {
	            left: 'prev,next today',
	            center: 'title',
	            right: 'month,agendaWeek,agendaDay'
	          },
	          <?php
	          if (!empty($_GET["initial_date"])) {
		          $initialDate = $_GET["initial_date"];
		          print "defaultDate: moment('$initialDate'), defaultView: 'agendaDay',";
	          } 
			  ?>
		      defaultTimedEventDuration: '01:00:00',
		      forceEventDuration: true,
	          buttonText: {
	            today: '<?php $lh->translateText("today"); ?>',
	            month: '<?php $lh->translateText("month"); ?>',
	            week: '<?php $lh->translateText("week"); ?>',
	            day: '<?php $lh->translateText("day"); ?>'
	          },
	          //Random default events
	          <?php print $ui->getAssignedEventsListForCalendar($user->getUserId()); ?>,
	          <?php print $ui->getTimezoneForCalendar(); ?>,
	          editable: true,
	          ignoreTimezone: false,
	          droppable: true, // this allows things to be dropped onto the calendar !!!
	          drop: function (date, allDay, jsEvent, ui) { // this function is called when something is dropped
	            // retrieve the dropped element's stored Event Object
	            var originalEventObject = $(this).data('eventObject');
	            var eventId = $(this).attr("event-id");
                var eventUrl = $(this).attr("event-url");
				var endDate = date + 3600000; // 1 hour in milliseconds
				var jsObject = $(this);
	
				// request the update first.
				  $.post("./php/ModifyEvent.php", //post
				  {"start_date": date+"", "end_date": endDate+"", "event_id": eventId, "all_day": !date.hasTime()}, 
				  function(data) { // result is new event id or 0 if something went wrong.
					if (data != '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') { // error
						<?php print $ui->showCustomErrorMessageAlertJS($lh->translationFor("unable_modify_event")); ?>
					} else { // move the new configured event in the calendar.
			            // we need to copy it, so that multiple events don't have a reference to the same object
			            var copiedEventObject = $.extend({}, originalEventObject);
			            copiedEventObject.id = eventId;
						copiedEventObject.url = eventUrl;
			
			            // assign it the date that was reported
			            copiedEventObject.start = date;
			            copiedEventObject.allDay = !date.hasTime();
			            copiedEventObject.backgroundColor = jsObject.css("background-color");
			            copiedEventObject.borderColor = jsObject.css("border-color");
		
			            // render the event on the calendar
			            // the last `true` argument determines if the event "sticks" 
			            $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
						jsObject.remove(); // remove the element from the "Draggable Events" list
					}
				  });
	
	          },
	          eventResize: function( event, delta, revertFunc, jsEvent, ui, view ) {
			  	changeEventOrRevert(event, revertFunc);
	          },
	          dayClick: function (date, jsEvent, ui) { // Go to a day view by clicking on a day.
		          	$('#calendar').fullCalendar( 'changeView', 'agendaDay' );
				  	$('#calendar').fullCalendar( 'gotoDate', date );
	          },
	          eventDrop: function (event, delta, revertFunc, jsEvent, ui, view) { // drag/move an event.
			  	changeEventOrRevert(event, revertFunc);
	          },
			  eventDragStop: function (event, jsEvent) {
	  				var trashEl = jQuery('#delete-event-trash');
    				var ofs = trashEl.offset();
    				var x1 = ofs.left;
					var x2 = ofs.left + trashEl.outerWidth(true);
    				var y1 = ofs.top;
    				var y2 = ofs.top + trashEl.outerHeight(true);

					// dropped to trash?
    				if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 && jsEvent.pageY>= y1 && jsEvent.pageY <= y2) {
    					 // try to delete the event.
						 $.post("./php/DeleteEvent.php", //post
						 {"eventid": event.id}, 
						 function(data) { // result is new event id or 0 if something went wrong.
							  if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') { // success
		         				$('#calendar').fullCalendar('removeEvents', event.id);
							  } else { // error
									<?php print $ui->showCustomErrorMessageAlertJS($lh->translationFor("unable_modify_event")); ?>
							  }
						  });
    				}
	          }
	        });
	        
	        function changeEventOrRevert(event, revertFunc) {
				// request an event modification.
				$.post("./php/ModifyEvent.php", //post
				{"start_date": event.start+"", "end_date": event.end+"", "event_id": event.id, "all_day": event.allDay}, 
				function(data) { // result is new event id or 0 if something went wrong.
				if (data != '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') { // error
					<?php print $ui->showCustomErrorMessageAlertJS($lh->translationFor("unable_modify_event")); ?>
					revertFunc();
				}
				});
	        }
	
	        /* ADDING EVENTS */
	        var currColor = "#3c8dbc"; //blue by default
	        //Color chooser button
	        var colorChooser = $("#color-chooser-btn");
	        $("#color-chooser > li > a").click(function (e) {
	          e.preventDefault();
	          //Save color
	          currColor = $(this).css("color");
	          //Add color effect to button
	          $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
	        });
	        $("#add-new-event").click(function (e) {
	          e.preventDefault();
	          //Get value and make sure it is not null
	          var val = $("#new-event").val();
	          if (val.length == 0) {
	            return;
	          }
	          // loading spinner
	          var spinnerOverlay = '<?php print $ui->spinnerOverlay("creating-event-spinner"); ?>';
	          $('#new-event-box-body').after(spinnerOverlay);

			  // ajax call
	          var eventId = 0;
			  var color = currColor;
			  if ((/^rgb/).test(color)) { color = rgb2hex(color); }
			  $.post("./php/CreateEvent.php", //post
			  {"title": val, "color": rgb2hex(currColor)}, 
			  function(data) { // result is new event id or 0 if something went wrong.
					$("#creating-event-spinner").remove();
					if (data == '0') { // error
						<?php print $ui->showCustomErrorMessageAlertJS($lh->translationFor("unable_create_event")); ?>
					} else { // we have a new event id!
			          //Create events
			          eventId = data;
			          var event = $("<div />");
			          event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
			          event.html(val);
			          event.attr("event-id", eventId);
			          $('#external-events').prepend(event);
			
			          //Add draggable funtionality
			          ini_events(event);
			
			          //Remove event from text input
			          $("#new-event").val("");
					}
				});
	        });
	      });
/** EDIT EVENTS **/

			/**
			 * Show the edit events dialog, filling the edit fields properly.
			 */
			$(".edit-events-action").click(function(e) {
				// Set ID of the events to edit
				e.preventDefault();
		        var ele = $(this).parents("li").first();
				var events_id = ele.attr("id"); // events ID is contained in the ID element of the li object.
				$('#edit-events-eventsid').val(events_id);
				
				// set the previous description of events.
				var current_text = $('.text', ele);
				$('#edit-events-title').val(current_text.text());
			});
		
			/**
			 * Edit the title of an events
			 */
			$("#edit-events-form").validate({
				submitHandler: function() {
					//submit the form
						$("#resultmessage").html();
						$("#resultmessage").fadeOut();
						$.post("./php/EditEvent.php", //post
						$("#edit-events-form").serialize(), 
							function(data){
								//if message is sent
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									location.reload();
								} else {
									$("#resultmessage").html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b><?php $lh->translateText("oups"); ?></b> <?php $lh->translateText("unable_modify_task"); ?>: '+ data);
									$("#resultmessage").fadeIn(); //show confirmation message
								}
								//
							});
					return false; //don't let the form refresh the page...
				}					
			});


	      /** Auxiliary functions */
	      var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 
	      //Function to convert hex format to a rgb color
	      function rgb2hex(rgb) {
		      rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		      if (rgb == null) { return "<?php print CRM_UI_COLOR_DEFAULT_HEX; ?>"; }
			  return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
		  }
		
		function hex(x) { return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16]; }
	</script>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<?php if ($custsOk) { ?>
		<script type="text/javascript">

        //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
        var PieData = [
          <?php print $ui->generatePieChartStatisticsData($colors); ?>
        ];
        var pieOptions = {
          //Boolean - Whether we should show a stroke on each segment
          segmentShowStroke: true,
          //String - The colour of each segment stroke
          segmentStrokeColor: "#fff",
          //Number - The width of each segment stroke
          segmentStrokeWidth: 2,
          //Number - The percentage of the chart that we cut out of the middle
          percentageInnerCutout: 50, // This is 0 for Pie charts
          //Number - Amount of animation steps
          animationSteps: 100,
          //String - Animation easing effect
          animationEasing: "easeOutBounce",
          //Boolean - Whether we animate the rotation of the Doughnut
          animateRotate: true,
          //Boolean - Whether we animate scaling the Doughnut from the centre
          animateScale: false,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true,
          // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: false,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\" style=\"list-style-type: none;\"><% for (var i=0; i<segments.length; i++){%><li><i class=\"fa fa-circle-o\" style=\"color:<%=segments[i].fillColor%>\"> </i><%if(segments[i].label){%>  <%=segments[i].label%><%}%></li><%}%></ul>"
        };
        var pieChart = new Chart(pieChartCanvas).Doughnut(PieData, pieOptions);
		$('#customers-chart-legend').html(pieChart.generateLegend());

		</script>
		<?php } ?>
    </body>
</html>
