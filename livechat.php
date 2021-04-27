<?php	
/**
 * @file        rocketchat.php
 * @brief       Loadup Rocketchat
 * @copyright   Copyright (c) 2021 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require_once('php/UIHandler.php');
	require_once('php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
	if(ROCKETCHAT_ENABLE === 'n'){
		header("location: index.php");
	}

	
        $target = "";
        if(isset($_GET['current_chats']))
               $target = "/omnichannel/current";
        elseif(isset($_GET['analytics']))
               $target = "/omnichannel/analytics";
        elseif(isset($_GET['realtime_monitoring']))
               $target = "/omnichannel/realtime-monitoring";
  	else
		header("location: index.php");
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("livechat"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
	?>
	<style>
		.rcx-sidebar{
			display:none !important;
		}
	</style>
    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side"><?php //if(isset($_SESSION['gad_authToken'])) echo $_SESSION['gad_authToken'];?>
                 <div id="rc_div">
			<!--<iframe src="<?php echo ROCKETCHAT_URL.$target;?>" id="rc_frame"  width="100%" height="100%">-->
		</div>
	    </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

	<!-- Forms and actions -->
	<?php print $ui->standardizedThemeJS(); ?>
<script type="text/javascript">
	$(document).ready(function() {

		$('<iframe>', {
                   src: '<?php echo ROCKETCHAT_URL.$target.'?layout=embedded';?>',
                   id:  'rc_frame',
		   name: 'rc_frame',
                   frameborder: 0,
                   width: '100%',
                   height: '100%',
                   scrolling: 'no',
                 }).appendTo('#rc_div');
		$("#rc_div").hide();
		var rcUser = '<?php echo $_SESSION['user']?>';
                var rcHandshake = '<?php echo $_SESSION['phone_this'];?>';
                $.ajax({
                      url: "./php/AdminLoginRocketChat.php",
                      type: 'POST',
                      dataType: "json",
                      data: {user: rcUser, pass: rcHandshake},
                      success: function(data) {
			$(".preloader").fadeIn("slow");
			setTimeout( function() {
                            $(".rc-loading-reports").fadeIn("slow");
                        }, 3000 );

                        setTimeout( function() {
                            $(".reload-button").fadeIn("slow");
                        }, 15000 );
			   //var rcToken = data.data.authToken;
			   console.log(data);
			   //console.log(rcToken);
			   var rcWin = document.getElementById('rc_frame').contentWindow;
			   setTimeout(function() {
				console.log("Loading Rocketchat...");
                              rcWin.postMessage({
                              event: 'log-me-in-iframe',
                              user: rcUser,
                              pass: rcHandshake
                              }, '<?php echo ROCKETCHAT_URL;?>');
				$(".rc-loading-reports").fadeOut("fast");
				$(".preloader").fadeOut("slow");
				setTimeout(function() {
				$("#rc_div").show();
				}, 1500);
                           }, 8000);
                      }
                });
	}); // end of document ready

	function loadIframe(iframeName, url) {
	    var $iframe = $('#' + iframeName);
	    if ($iframe.length) {
	        $iframe.attr('src',url);
	        return false;
	    }
	    alert(url);
	    return true;
	}

</script>
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
