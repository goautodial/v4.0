<?php
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
	}

	// initialize session and DDBB handler
	require_once('./php/Session.php');
	include_once('./php/UIHandler.php');
	require_once('./php/LanguageHandler.php');
	require_once('./php/DbHandler.php');
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	$db = new \creamy\DbHandler();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	

// create new event with client id?
if (isset($_GET["customerid"]) && isset($_GET["customer_type"])) {
	$customerid = $_GET["customerid"];
	$customertype = $_GET["customer_type"];
	$db->createContactEventForCustomer($user->getUserId(), $customerid, $customertype);
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("portal_title"); ?> - <?php $lh->translateText("events"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	    <!-- fullCalendar 2.2.5-->
	    <link href="css/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
	    <link href="css/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>


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
        <!-- jQuery form validation -->
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
	    <!-- fullCalendar 2.2.5 -->
	    <script src="js/plugins/fullcalendar/moment.min.js" type="text/javascript"></script>
	    <script src="js/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
	    <?php
		// try to get the primary language from the LanguageHandler
		$primary = $lh->getPrimaryLanguage();
		if (!empty($primary)) {
			echo '<script src="js/plugins/fullcalendar/lang/'.$primary.'.js" type="text/javascript"></script>';
		}
	    ?>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
	        <!-- header logo: style can be found in header.less -->
			<?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("home"); ?>
                        <small><?php $lh->translateText("events"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-bar-chart-o"></i> <?php $lh->translateText("home"); ?></a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

		          <div class="row">
		            <div class="col-md-4">

					  <!-- Unassigned events -->
		              <div class="box box-default">
		                <div class="box-header with-border">
		                  <h4 class="box-title"><?php $lh->translateText("unassigned_events"); ?></h4>
		                </div>
		                <div class="box-body">
		                  <!-- unassigned events -->
						  <?php print $ui->getUnassignedEventsList($user->getUserId()); ?>
		                </div><!-- /.box-body -->
		              </div><!-- /. box -->

					  <!-- Create new events -->
		              <div class="box box-default">
		                <div class="box-header with-border">
		                  <h3 class="box-title"><?php $lh->translateText("create_new_event"); ?></h3>
		                </div>
		                <div class="box-body" id="new-event-box-body">
		                  <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
		                    <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
		                    <ul class="fc-color-picker" id="color-chooser">
			                  <?php
				              	$colors = $ui->creamyColors();
				              	unset($colors["navy"]); unset($colors["muted"]); unset($colors["light-blue"]);
				              	foreach (array_keys($colors) as $color) {
					              	print '<li><a class="text-'.$color.'" href="#"><i class="fa fa-square"></i></a></li>';
				              	}
				              ?>
		                    </ul>
		                  </div><!-- /btn-group -->
		                  <div class="input-group">
		                    <input id="new-event" type="text" class="form-control" placeholder="Event Title">
		                    <div class="input-group-btn">
		                      <button id="add-new-event" type="button" class="btn btn-primary btn-flat"><?php $lh->translateText("create") ?></button>
		                    </div><!-- /btn-group -->
		                  </div><!-- /input-group -->
		                </div>
		              </div>
		            
					<!-- Delete events -->
		              <div class="box box-default" id="delete-event-trash">
		                <div class="box-header with-border">
		                  <h4 class="box-title"><?php $lh->translateText("delete_events"); ?></h4>
		                </div>
		                <div class="box-body">
		                  <!-- delete events -->
								<p><i class="fa fa-trash"> </i> <?php $lh->translateText("drop_here_delete"); ?></p>
		                </div><!-- /.box-body -->
		              </div><!-- /. box -->
		           

		            <!-- EDIT events -->
		             <div class="box box-default">
		                <div class="box-header with-border">
		                  <h4 class="box-title"><?php $lh->translateText("edit_events"); ?></h4>
		                </div>
		                <div class="box-body">
		                  <!-- edit events -->
								<?php print $ui->getAssignedEventsAsTable($user->getUserId()); ?>
		                </div><!-- /.box-body -->
		              </div><!-- /. box -->
		               </div><!-- /.col -->
		               <!--try modal-->

		        <div class="modal fade" id="edit-events-modal" name="edit-events-modal" tabindex="-1" role="dialog" aria-hidden="true">
			        <div class="modal-dialog">
			            <div class="modal-content">
			                <div class="modal-header">
			                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                    <h4 class="modal-title"><i class="fa fa-edit"></i> <?php $lh->translateText("Modify Event"); ?></h4>
			                    <p><?php $lh->translateText("Insert the new title for the event and push 'modify event' button"); ?></p>
			                </div>
			                <form action="" method="post" name="edit-events-form" id="edit-events-form">
			                    <div class="modal-body">
			                        <div class="form-group">
			                            <label for="edit-events-title"><?php $lh->translateText("New Title"); ?></label>
			                            <input type="text required" class="form-control" id="edit-events-title" name="edit-events-title" placeholder="<?php $lh->translateText("New title"); ?>">
									</div>
									<input type="hidden" id="edit-events-eventsid" name="edit-events-eventsid" value="">
									<div id="changeeventsresult" name="changeeventsresult"></div>
			                    </div>
			                    <div class="modal-footer clearfix">
			                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="changetaskCancelButton"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
			                        <button type="submit" class="btn btn-primary pull-right" id="changeeventsOkButton"><i class="fa fa-check"></i> <?php $lh->translateText("Modify Event"); ?></button>
			                    </div>
			                </form>
			            </div><!-- /.modal-content -->
			        </div><!-- /.modal-dialog -->
			    </div><!-- /.modal -->		

				<!-- /CHANGE TASK MODAL -->
		            <div class="col-md-8">
		              <div class="box box-default">
		                <div class="box-body no-padding">
		                  <!-- THE CALENDAR -->
		                  <div id="calendar"></div>
		                </div><!-- /.box-body -->
		              </div><!-- /. box -->
		            </div><!-- /.col -->
					
		          </div><!-- /.row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

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
				$('#edit-events-modal').modal();
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
    </body>
</html>
