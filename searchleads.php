<?php 
	// /** Search Leads API - get list Leads */
	// /**
	//  * Generates action circle buttons for different pages/module
	//  * @param goUser 
	//  * @param goPass 
	//  * @param goAction 
	//  * @param responsetype
	//  * @param lists
	//  */

	require_once('php/goCRMAPISettings.php');

	$lists = $_POST['lists'];
	$last_name = $_POST['last_name'];
	$phone_number = $_POST['phone_number'];

	$url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	$postfields["goAction"] = "goGetLeads"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype; #json. (required)
	$postfields['lists'] = $lists;
	$postfields['last_name'] = $last_name;
	$postfields['phone_number'] = $phone_number;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);

	$leads = json_decode($data);

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
	
	require_once ('./php/CRMDefaults.php');
	require_once ('./php/UIHandler.php');
	require_once ('./php/LanguageHandler.php');
    include ('./php/Session.php');

    $ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
	    <!-- iCheck for checkboxes and radio inputs -->
	    <link href="css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="./js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="./js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	    <!-- iCheck -->
	    <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <!-- JQuery Validate -->
        <script src ="js/jquery.validate.min.js" type="text/javascript"></script>
		<!-- Input mask for date textfields -->
		<script src="js/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side" style="min-height: 100%;">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Contacts
                        <small><?php $lh->translateText("Contacts"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-users"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active">Contacts</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
	                <!-- check permissions -->
	                <?php 
                    	$list = $ui->getAllowedList($user->getUserId());
                    	// print_r($list);
                    ?>
	                <?php if ($user->userHasBasicPermission()) { ?>
                        <div class="box box-info">
                          <div class="box-head">
                            <h4 class="col-lg-12">Search Parameter</h4>
                          </div>
                          <div class="box-body">
                            <div class="form-horizontal">
                              <div class="form-group">
                                <label class="control-label col-lg-2">Last Name:</label>
                                <label class="control-label col-lg-2" style="text-align: left;"><?php echo (!empty($last_name))? $last_name:"No last name input"; ?></label>
                                <label class="control-label col-lg-2">Phone Number:</label>
                                <label class="control-label col-lg-2" style="text-align: left;"><?php echo (!empty($phone_number))? $phone_number:"No phone number input"; ?></label>
                              </div>
                              <div class="form-group">
                                <label class="control-label col-lg-2">Lists Used:</label>
                                <label class="control-label col-lg-2" style="text-align: left;"><?php echo (!empty($lists))? $lists:"No list selected"; ?></label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="box">
                           <div class="box-body">	
                           		 <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                  <div class="row">
                                      <div class="col-lg-12">
                                          <div class="col-sm-12 table-responsive">
                                              <table id="leads" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                                                  <thead>
                                                      <tr>
                                                         <!--  <th class="no-sort-checkbox" style="padding:5px !important;">
                                                              <label>
                                                                  <input type="checkbox" class="minimal-red all">
                                                              </label>
                                                          </th> -->
                                                          <th rowspan="1" colspan="1">Lead ID</th>
                                                          <th rowspan="1" colspan="1">List ID</th>
                                                          <th rowspan="1" colspan="1">First Name</th>
                                                          <th rowspan="1" colspan="1">Middle Initial</th>
                                                          <th rowspan="1" colspan="1">Last Name</th>
                                                          <th rowspan="1" colspan="1">Phone Number</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody>
                                                    <?php for($i=0;$i<=count($leads->lead_id);$i++){ ?>
                                                      <tr>
                                                        <td><?php echo $leads->lead_id[$i]; ?></td>
                                                        <td><?php echo $leads->list_id[$i]; ?></td>
                                                        <td><?php echo $leads->first_name[$i]; ?></td>
                                                        <td><?php echo $leads->middle_initial[$i]; ?></td>
                                                        <td><?php echo $leads->last_name[$i]; ?></td>
                                                        <td><?php echo $leads->phone_number[$i]; ?></td>
                                                      </tr>
                                                    <?php } ?>
                                                  </tbody>
                                              </table>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                           </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    <?php } ?>
                    <!-- user not authorized -->
					<?php 
					if ($user->userHasWritePermission()) { ?>
					<?php } else { print $ui->getUnauthotizedAccessMessage(); } ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <!-- page script -->
        <script type="text/javascript">
			     $(document).ready(function(){
              $('#leads').DataTable({
                  "order": [],
                  "columnDefs": [ {
                      "targets"  : 'no-sort-checkbox',
                      "orderable": false,
                  }]
              });
           });
        </script>

    </body>
</html>
