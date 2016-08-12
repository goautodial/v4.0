<?php
function GetOnlineAgents(){
    require_once('../goCRMAPISettings.php');
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetOnlineAgents"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);
    return $output;
}

function GetUserInfo(){
    $url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["user_id"] = $userid; #Desired User ID (required)

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    
    $output = json_decode($data);
    return $output;

}

$output = GetOnlineAgents();
if ($output == NULL){
    echo '<strong class="media-box-heading text-primary">
    <span class="circle circle-danger circle-lg text-left"></span>There are no available agents.
    </strong>
    <br/>
    <strong class=""style="padding-left:20px;"></strong>
    <small class="text-muted pull-right ml" style="padding-right:20px;"></small>
    </p>';
    echo 'Showing sample data:<br>
	<a href="#" class="list-group-item">
		<div class="media-box">
			 <div class="pull-left">
			 <img src="theme_dashboard/img/user/03.jpg" alt="Image" class="media-box-object img-circle thumb32">
			 </div>
			 <div class="media-box-body clearfix">
                                <strong class="media-box-heading text-primary">
				<span class="circle circle-success circle-lg text-left"></span>Jackie "Baby boy" Alfonso</strong>
			 	<br/>
			 	<strong class=""style="padding-left:20px;">CS HOTLINE</strong>
			 	<small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
			 </div>
		</div>
	</a>
	<!-- END list group item-->
	<!-- START list group item-->
	<a href="#" class="list-group-item">
		<div class="media-box">
			 <div class="pull-left">
			 <img src="theme_dashboard/img/user/09.jpg" alt="Image" class="media-box-object img-circle thumb32">
			 </div>
			 <div class="media-box-body clearfix">
			 <strong class="media-box-heading text-primary">
				<span class="circle circle-danger circle-lg text-left"></span>Kim Takahashi</strong>
			 	<br/>
			 	<strong class=""style="padding-left:20px;">CS HOTLINE</strong>
			 	<small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
			 </div>
		</div>
	</a>
	<!-- END list group item-->
	<!-- START list group item-->
	<a href="#" class="list-group-item">
		<div class="media-box">
			 <div class="pull-left">
			 <img src="theme_dashboard/img/user/12.jpg" alt="Image" class="media-box-object img-circle thumb32">
			 </div>
			 <div class="media-box-body clearfix">
			 <strong class="media-box-heading text-primary">
				<span class="circle circle-danger circle-lg text-left"></span>Khristel Tonolete</strong>
			 	<br/>
			 	<strong class=""style="padding-left:20px;">CS HOTLINE</strong>
			 	<small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
			 </div>
		</div>
	</a>
	<!-- END list group item-->
	<!-- START list group item-->
	<a href="#" class="list-group-item">
		<div class="media-box">
			 <div class="pull-left">
			 <img src="theme_dashboard/img/user/10.jpg" alt="Image" class="media-box-object img-circle thumb32">
			 </div>
			 <div class="media-box-body clearfix">
			 <strong class="media-box-heading text-primary">
				<span class="circle circle-danger circle-lg text-left"></span>Andrew Gwaltney</strong>
			 	<br/>
			 	<strong class=""style="padding-left:20px;">CS HOTLINE</strong>
			 	<small class="text-muted pull-right ml" style="padding-right:20px;">1:49</small>
			 </div>
		</div>
	</a>
	<!-- END list group item-->
        </div>';
}
//var_dump($output);
//die("dd");
//milo
for($i=0;$i < count($output->agent_full_name);$i++){
    $campname = $output->campaign[$i];
    $status = $output->status[$i];
    $userid = $output->user_id[$i];
    $agentname =  $output->agent_full_name[$i];
    $last_call_time = $output->last_call_time[$i];
    $last_state_change = $output->last_state_change[$i];
    $lead_id = $output->lead_id[$i];
    $STARTtime = date("U");
    
    if (eregi("READY|CLOSER",$status)){
        $last_call_time=$last_state_change;
        $class = "circle circle-warning circle-lg text-left";
            if ($lead_id>0){ 
                $status="DISPO";
            }
    }
    if (eregi("PAUSED",$status)){
        $class = "circle circle-danger circle-lg text-left";
            if ($lead_id>0){ 
                $status="DISPO";
            }
    }    
    if (!eregi("INCALL|QUEUE|PARK|3-WAY",$status)){
        $call_time_S = ($STARTtime - $last_state_change);
    }
    else if (eregi("3-WAY",$status)){
        $call_time_S = ($STARTtime - $call_mostrecent);
    }
    else{
        $call_time_S = ($STARTtime - $last_call_time);
        $class = "circle circle-success circle-lg text-left";
    }
        
        $call_time_M = ($call_time_S / 60);
        $call_time_M = round($call_time_M, 2);
        $call_time_M_int = intval("$call_time_M");
        $call_time_SEC = ($call_time_M - $call_time_M_int);
        $call_time_SEC = ($call_time_SEC * 60);
        $call_time_SEC = round($call_time_SEC, 0);
        if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
        $call_time_MS = "$call_time_M_int:$call_time_SEC";
        $G = '';		$EG = '';        
    //var_dump($class);
    echo '<strong class="media-box-heading text-primary">
    <span class="'.$class.'"></span><a data-toggle="modal" data-target="#view-agent-modal" class="text-danger m0">'.$agentname.'</a>
    </strong>
    <br/>
    <strong class=""style="padding-left:20px;">'.$campname.'</strong>
    <small class="text-muted pull-right ml" style="padding-right:20px;">'.$call_time_MS.'</small>
    </p>';    
}
      
$output = GetUserInfo();
for($i=0;$i < count($output->agent_full_name);$i++){
    $user = $output->user[$i];
    $fullname = $output->full_name[$i];
    $userlevel = $output->user_level[$i];
    $usergroup = $output->user_group[$i];
    $active = $output->active[$i];
    $email = $output->email[$i];
    $voicemail = $output->voicemail_id[$i];
    $phonelogin = $output->phone_login[$i];
    $phonepass = $output->phone_pass[$i];

}
?>
