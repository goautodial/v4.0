<?php

    ###########################################################
    ### Name: edittelephonyuser.php                         ###
    ### Functions: Edit Users                               ###
    ### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
    ### Version: 4.0                                        ###
    ### Written by: Alexander Abenoja                       ###
    ###             Noel Umandap                            ###
    ###             Demian Lizandro A. Biscocho             ###
    ### License: AGPLv2                                     ###
    ###########################################################
    
    require_once('./php/UIHandler.php');
    require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
    $user = \creamy\CreamyUser::currentUser();
    
    $userid = $user->getUserId();
    $userrole = $user->getUserRole(); 

?>

<html>
    <head>
        <meta charset="utf-8">
        <title>User Profile</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Bootstrap Admin App + jQuery">
        <meta name="keywords" content="app, responsive, jquery, bootstrap, dashboard, admin">
        
        <?php print $ui->standardizedThemeCSS(); ?>
        <?php print $ui->creamyThemeCSS(); ?>
        
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        
		<!-- javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>    
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
        
            <!-- dashboard status boxes -->
        <script src="js/bootstrap-editable.js" type="text/javascript"></script> 
        <script src="theme_dashboard/moment/min/moment-with-locales.min.js" type="text/javascript"></script>
        <script src="js/modules/now.js" type="text/javascript"></script>         
	    <!-- ChartJS 1.0.1 -->
        <script src="js/plugins/chartjs/Chart.min.js" type="text/javascript"></script>
        
            <!-- Creamy App -->
        <!--<script src="js/app.min.js" type="text/javascript"></script>-->
            
            <!-- Data Tables -->
        <!-- <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script> -->
        <script src="js/plugins/datatables/FROMjquery.dataTables.js" type="text/javascript"></script>
        <script src="js/fnProcessingIndicator.js" type="text/javascript"></script>        

        <!-- FONT AWESOME-->
        <link rel="stylesheet" href="theme_dashboard/fontawesome/css/font-awesome.min.css">
        <!-- SIMPLE LINE ICONS-->
        <link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">
        <!-- ANIMATE.CSS-->
        <link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">
        <!-- WHIRL (spinners)-->
        <link rel="stylesheet" href="theme_dashboard/whirl/dist/whirl.css">
        <!-- =============== PAGE VENDOR STYLES ===============-->
        <!-- =============== BOOTSTRAP STYLES ===============-->
        <link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
        <!-- =============== APP STYLES ===============-->
        <link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">
        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">
        
        <link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">
        
        <script type="text/javascript">
            $(window).ready(function() {
                    $(".preloader").fadeOut("slow");
            })
        </script>
        
        <link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
        <script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>
    

    </head>
        <section class="ng-scope">
    <?php print $ui->creamyBody(); ?>
        <div data-ui-view="" data-autoscroll="false" class="wrapper ng-scope">
            <!-- header logo: style can be found in header.less -->
                <?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
                <?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">
            <!-- Page content-->        
         
<?php
            $userobj = NULL;
            $errormessage = NULL;            
            $output = $ui->goGetUserInfo($userid, "user_id");
            $creamyAvatar = $ui->getSessionAvatar();
            //echo ("pre");
            //var_dump($output);

            $userid = $output->data->user_id;
            $agentid = $output->data->user;
            $agentname =  $output->data->full_name;
            $email = $output->data->email;
            $user_group = $output->data->user_group;
            $status = $output->data->active;
            $voicemail_id = $output->data->voicemail_id;
            
            $outcallstoday = $output->data->outcallstoday;
            $outsalestoday = $output->data->outsalestoday;
            $incallstoday = $output->data->incallstoday;
            $insalestoday = $output->data->insalestoday;            
            
            if ($incallstoday == NULL){
                $incallstoday = "0";
            }
            
            if ($outcallstoday == NULL){
                $outcallstoday = "0";
            }            
            
            $totalcallstoday = ($outcallstoday + $incallstoday);
            $totalsalestoday = ($outsalestoday + $insalestoday);            
            
            if ($status == "Y"){
                $status = "ACTIVE";
            }
            if ($status == "N"){
                $status = "INACTIVE";
            }

    $vmArray = $ui->API_goGetVoiceMails();
    //echo "<pre>";
    //print_r($vmArray);
    //die();    
    foreach($vmArray->voicemail_id as $key => $value) {
        //var_dump($key, $value);                       
    
            foreach($vmArray->messages as $key2 => $value2){
                //var_dump($key2, $value2);
                if ($value == $voicemail_id) break;
                $vmid = $value;
                $vm_message = $value2;
            }    
    }

?>
 
            <div class="unwrap ng-scope">
               <div style="background-image: url(img/profile-bg.jpg); padding-top: 20px;" class="bg-cover">
                  <div class="p-xl text-center text-white">
                     <span style="display:table; margin:0 auto; background-color: #ff902b; border: 3px solid #dadada; border-radius: 50%; margin-bottom: 10px; height: 128px; width: 128px;"><?=$ui->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 128)?></span>
                     <h3 class="m0"><?php echo $user->getUserName(); ?></h3>
                     <p><?php echo $_SESSION['user']; ?></p>
                     <p>Empowering the next generation contact centers.</p>
                  </div>
               </div>
               <div class="text-center bg-warning p-lg mb-xl">
                  <div class="row row-table" style="height: 47px">
                     <div class="col-xs-4 br">
                        <h3 class="m0"><?php echo $totalcallstoday; ?></h3>
                        <p class="m0">
                           <span class="hidden-xs">Calls Today</span>
                           <!-- <span>Views</span> -->
                        </p>
                     </div>                    
                     <div class="col-xs-4 br">
                        <h3 class="m0"><?php echo $totalsalestoday; ?></h3>
                        <p class="m0">Sales Today</p>
                     </div>
                     <div class="col-xs-4">
                        <h3 class="m0"><?php echo $vm_message; ?></h3>
                        <p class="m0">Voicemails</p>
                     </div>
                  </div>
               </div>
               <div class="p-lg">
                  <div class="row">
                     <div class="col-lg-9">
                        <!-- START timeline-->
                        <ul class="timeline">
                           <li data-datetime="Today" class="timeline-separator"></li>
                           <!-- START timeline item-->
                           <li>
                <div class="timeline-badge primary">
                     <em class="fa fa-users"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover">
                        <h4 class="popover-title">Outbound Calls</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Calls today: <?php echo $outcallstoday; ?>
                              <br>
                              <small>Click to display all outbound calls for this day.</small>
                           </p>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline item-->
               <li class="timeline-inverted">
                  <div class="timeline-badge warning">
                     <em class="fa fa-phone"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover right">
                        <h4 class="popover-title">Inbound Calls</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Michael <a href="tel:+011654524578">(+011) 6545 24578 ext. 132</a>
                              <br>
                              <small>Click to display all inbound and closer calls for this day.</small>
                           </p>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline separator-->
               <li data-datetime="Yesterday" class="timeline-separator"></li>
               <!-- END timeline separator-->
               <!-- START timeline item-->
               <li>
                  <div class="timeline-badge danger">
                     <em class="fa fa-video-camera"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover">
                        <h4 class="popover-title">Conference</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Join development group</p>
                           <small>
                              <a href="skype:echo123?call">
                                 <em class="fa fa-phone"></em>Call the Skype Echo</a>
                           </small>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline item-->
               <li class="timeline-inverted">
                  <div class="timeline-panel">
                     <div class="popover right">
                        <h4 class="popover-title">Appointment</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam.</p>
                           <div class="btn-group">
                              <a href="#" data-toggle="dropdown" data-play="fadeIn" class="dropdown-toggle">
                                 <em class="fa fa-paperclip"></em>
                              </a>
                              <ul class="dropdown-menu text-left">
                                 <li>
                                    <a href="#">
                                       <em class="fa fa-download"></em>Download</a>
                                 </li>
                                 <li>
                                    <a href="#">
                                       <em class="fa fa-share"></em>Send to</a>
                                 </li>
                                 <li class="divider"></li>
                                 <li>
                                    <a href="#">
                                       <em class="fa fa-times"></em>Delete</a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline item-->
               <li>
                  <div class="timeline-badge info">
                     <em class="fa fa-plane"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover">
                        <h4 class="popover-title">Fly</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline item-->
               <li>
                  <div class="timeline-panel">
                     <div class="popover">
                        <h4 class="popover-title">Appointment</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline separator-->
               <li data-datetime="2014-05-21" class="timeline-separator"></li>
               <!-- END timeline separator-->
               <!-- START timeline item-->
               <li class="timeline-inverted">
                  <div class="timeline-badge success">
                     <em class="fa fa-music"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover right">
                        <h4 class="popover-title">Relax</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Listen some music</p>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline item-->
               <li class="timeline-end">
                  <a href="#" class="timeline-badge">
                     <em class="fa fa-plus"></em>
                  </a>
               </li>
               <!-- END timeline item-->
            </ul>
            <!-- END timeline-->
                     </div>
                     <div class="col-lg-3">
                        <div class="panel panel-default">
                           <div class="panel-body">
                              <div class="text-center">
                                 <h3 class="mt0"><?php echo $agentname; ?></h3>
                                 <p><?php echo $agentid; ?></p>
                              </div>
                              <hr>
                              <ul class="list-unstyled ph-xl">
                                 <li>
                                    <em class="fa fa-home fa-fw mr-lg"></em>Group: <?php echo $user_group; ?></li>
                                 <li>
                                    <em class="fa fa-briefcase fa-fw mr-lg"></em><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
                                 </li>
                                 <li>
                                    <em class="fa fa-graduation-cap fa-fw mr-lg"></em>Status: <?php echo $status; ?></li>
                              </ul>
                           </div>
                        </div>
                        <div class="panel panel-default">
                           <div class="panel-heading">
                              <a class="pull-right">
                                 <em class="icon-plus text-muted"></em>
                              </a>Latest Calls</div>
                           <div class="list-group">
                              <!-- START Latest Calls summary widget -->
                                <a class="media p mt0 list-group-item">
                                
                                    <span id="refresh_agent_latest_calls_summary"></span>
                                  
                                </a>
                              
                              <!-- END User status-->
                                        <a href="#" data-toggle="modal" data-target="#agent_latest_calls" class="media p mt0 list-group-item text-center text-muted">View more</a>                              
                           </div>
                        </div>
                        <div class="panel panel-default">
                           <div class="list-group">
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-users fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">1000</h4>
                                 <p class="list-group-item-text">Friends</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-rss fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">3000</h4>
                                 <p class="list-group-item-text">Subscriptions</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-map-marker fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">100</h4>
                                 <p class="list-group-item-text">Places</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-briefcase fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">400</h4>
                                 <p class="list-group-item-text">Projects</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-twitter fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">17300</h4>
                                 <p class="list-group-item-text">Twees</p>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section><!-- /.content -->
            </aside><!-- /.right-side -->
                <?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
                <?php print $ui->creamyFooter();?>
        </div><!-- ./wrapper -->
        
<!--================= MODALS =====================-->

    <!-- Agent Latest Calls -->

                    <div class="modal fade" id="agent_latest_calls" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4>Latest Phone Calls (100 limit)</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover display compact" id="agent_latest_calls_table" style="width: 100%">
                                            <thead>
                                                <th style="color: white;">Pic</th>
                                                <th style="font-size: small;">Lead ID</th>
                                                <th style="font-size: small;">Customer</th>
                                                <th style="font-size: small;">List ID</th>                                                
                                                <th style="font-size: small;">Campaign ID</th>                                                                
                                                <th style="font-size: small;">Phone Number</th>
                                                <th style="font-size: small;">Status</th>
                                                <!-- <th style="font-size: small;">Agent</th> -->
                                                <th style="font-size: small;">Call Date</th>
                                                <th style="font-size: small;">Duration</th>
                                            </thead>
                                            <tbody>
                                            
                                            </tbody>
                                        </table>
                                    <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>	
                    </div>
                     
    <!-- End of Agent Latest Calls -->  
    <!-- Lead Information -->
                        
    <div class="modal fade" id="view_lead_information" tabindex="-1" role="dialog" aria-hidden="true"> 
        <div class="modal-dialog"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                        <h4 class="modal-title">Telephone: <span id="modal-phone_number"></span></h4> 
                </div> 
                <div class="modal-body"> 
                    <center> 
                        <div id="modal-avatar"></div>
                        <!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
                            <h3 class="media-heading"><span id="modal-full_name"></span><small>  <span id="modal-country_code"></small></h3> 
                                <span>Address: </span> 
                                    <span class="label label-info" id="modal-address1"></span> 
                                    <span class="label label-info" id="modal-city"></span> 
                                    <span class="label label-waring" id="modal-state"></span>
                                    <span class="label label-success" id="modal-postal_code"></span><br/>                                
                    </center> <hr> 
                        <div class="responsive">
                            <table class="table table-striped table-hover" id="view_lead_information_table" style="width: 100%">
                                <thead>
                                    <th style="font-size: small;">Lead ID</th> 
                                    <th style="font-size: small;">List ID</th>
                                    <th style="font-size: small;">Campaign ID</th>                                                                
                                    <!-- <th style="font-size: small;">Phone Number</th> -->
                                    <th style="font-size: small;">Status</th>
                                    <th style="font-size: small;">Agent</th>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td><span id="modal-lead_id"></td>
                                    <td><span id="modal-list_id"></td>
                                    <td><span id="modal-campaign_id"></td>
                                    <!-- <td><span id="modal-phone_number"></td> -->
                                    <td><span id="modal-status"></td>
                                    <td><span id="modal-user"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                </div> 
                <div class="modal-footer">                                        
                    <!-- <center> 
                        <button type="button" class="btn btn-default" data-dismiss="modal">I'm done</button> 
                    </center> -->
                </div> 
            </div> 
        </div> 
    </div> 
                        
    <!-- End of Lead Information -->    
    
<script>

    function load_agent_latest_calls(){    
        var userid = <?=$userid?>;         
        $.ajax({
            type: 'POST',
            url: "./php/APIs/API_GetAgentLatestCalls.php",
            cache: false,
            data: {user_id: userid},
            dataType: 'json',
            success: function(values){
                var JSONStringrealtime = values;
                var JSONObjectrealtime = JSON.parse(JSONStringrealtime);                
                //console.log(JSONStringrealtime);
                //console.log(JSONObjectrealtime); 
                var table = $('#agent_latest_calls_table').dataTable({
                    data:JSONObjectrealtime,
                    "destroy":true,
                    //"searching": false,
                    stateSave: true,
                    drawCallback: function(settings) {
                        var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                        pagination.toggle(this.api().page.info().pages > 1);
                    },
                    "columnDefs": [
                        {
                            className: "hidden-xs", 
                            "targets": [ 2, 4, 5, 7 ] 
                        }
                    ]                                    
                });
                goAvatar._init(goOptions);
            } 
        });
    }
    
    function load_agent_latest_calls_summary(){    
        var userid = <?=$userid?>;         
        $.ajax({
            type: 'POST',
            url: "./php/APIs/API_GetAgentLatestCallsSummary.php",
            cache: false,
            data: {user_id: userid},
            success: function(data){
                $("#refresh_agent_latest_calls_summary").html(data);
                goAvatar._init(goOptions);
            } 
        });
    }
    
    // Clear lead information
    function clear_lead_information_form(){

        //$('#modal-leadID').html("");
        $('#modal-list_id').html("");
        $('#modal-first_name').html("");
        $('#modal-last_name').html("");
        $('#modal-phone_number').html("");
        $('#modal-address1').html("");     
        $('#modal-city').html("");
        $('#modal-state').html("");
        $('#modal-postal_code').html("");
        $('#modal-country_code').html("");
        $('#modal-full_name').html("");
        $('#modal-avatar').html("");   
    }

    //demian
    $(document).ready(function(){
    
        // Clear previous lead info
        $('#view_lead_information').on('hidden.bs.modal', function () {
            clear_lead_information_form();
        }); 
                    
        // Get lead information 
        $(document).on('click','#onclick-leadinfo',function(){
            var leadid = $(this).attr('data-id');

            $.ajax({
                type: 'POST',
                url: "./php/ViewContact.php",
                data: {lead_id: leadid},
                cache: false,
                //dataType: 'json',
                    success: function(data){ 
                        //console.log(data);
                        var JSONStringleadinfo = data;
                        var JSONObjectleadinfo = JSON.parse(JSONStringleadinfo);                        
                        //console.log(JSONObjectleadinfo);
                        $('#modal-lead_id').html(JSONObjectleadinfo.data.lead_id);
                        $('#modal-list_id').html(JSONObjectleadinfo.data.list_id);                    
                        $('#modal-first_name').html(JSONObjectleadinfo.data.first_name);
                        $('#modal-last_name').html(JSONObjectleadinfo.data.last_name);
                        $('#modal-phone_number').html(JSONObjectleadinfo.data.phone_number);
                        $('#modal-address1').html(JSONObjectleadinfo.data.address1);  
                        $('#modal-city').html(JSONObjectleadinfo.data.city);
                        $('#modal-state').html(JSONObjectleadinfo.data.state);
                        $('#modal-postal_code').html(JSONObjectleadinfo.data.postal_code); 
                        $('#modal-country_code').html(JSONObjectleadinfo.data.country_code);
                        $('#modal-user').html(JSONObjectleadinfo.data.user);
                        $('#modal-status').html(JSONObjectleadinfo.data.status);
                        
                        var fname = JSONObjectleadinfo.data.first_name;
                        var lname = JSONObjectleadinfo.data.last_name;
                        var full_name = fname+' '+lname;
                        var avatar = '<avatar username="'+full_name+'" :size="160"></avatar>';
                        
                        $('#modal-full_name').html(full_name);
                        $('#modal-avatar').html(avatar);
                        goAvatar._init(goOptions);
                    }
            });  
        });         
           
        // ---- loads datatable functions                                                
            load_agent_latest_calls();
            load_agent_latest_calls_summary();
    });

    
</script>


        
    <!-- =============== VENDOR SCRIPTS ===============-->
    <!-- MODERNIZR-->
    <!--<script src="./vendor/modernizr/modernizr.custom.js"></script>-->
    <!-- MATCHMEDIA POLYFILL-->
    <!--<script src="./vendor/matchMedia/matchMedia.js"></script>-->
    <!-- JQUERY-->
    <!--<script src="./vendor/jquery/dist/jquery.js"></script>-->
    <!-- BOOTSTRAP-->
    <!--<script src="./vendor/bootstrap/dist/js/bootstrap.js"></script>-->
    <!-- STORAGE API-->
    <!--<script src="./vendor/jQuery-Storage-API/jquery.storageapi.js"></script>-->
    <!-- JQUERY EASING-->
    <!--<script src="./vendor/jquery.easing/js/jquery.easing.js"></script>-->
    <!-- ANIMO-->
    <!--<script src="./vendor/animo.js/animo.js"></script>-->
    <!-- SLIMSCROLL-->
    <!--<script src="./vendor/slimScroll/jquery.slimscroll.min.js"></script>-->
    <!-- SCREENFULL-->
    <!--<script src="./vendor/screenfull/dist/screenfull.js"></script>-->
    <!-- LOCALIZE-->
    <!--<script src="./vendor/jquery-localize-i18n/dist/jquery.localize.js"></script>-->
    <!-- RTL demo-->
    <!-- <script src="js/demo/demo-rtl.js"></script> -->
    <!-- =============== PAGE VENDOR SCRIPTS ===============-->
    <!-- GOOGLE MAPS-->
    <!--<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>-->
    <!--<script src="./vendor/jQuery-gMap/jquery.gmap.min.js"></script>-->
    <!-- =============== APP SCRIPTS ===============-->
    <!--<script src="js/app.js"></script>        -->
	<!-- Vue Avatar -->
    <script src="js/vue-avatar/vue.min.js" type="text/javascript"></script>
    <script src="js/vue-avatar/vue-avatar.min.js" type="text/javascript"></script>    
    <script type='text/javascript'>
        var goOptions = {
            el: 'body',
            components: {
                'avatar': Avatar.Avatar,
                'rules': {
                    props: ['items'],
                    template: 'For example:' +
                        '<ul id="example-1">' +
                        '<li v-for="item in items"><b>{{ item.username }}</b> becomes <b>{{ item.initials }}</b></li>' +
                        '</ul>'
                }
            },
            data: {
                items: []
            },
            methods: {
                initials: function(username, initials) {
                    this.items.push({username: username, initials: initials});
                }
            }
        };
        var goAvatar = new Vue(goOptions);
    </script>    
        
    <?php print $ui->standardizedThemeJS();?>
    </body>
</html>
