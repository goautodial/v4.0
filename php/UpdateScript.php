<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser 
	 * @param goPass 
	 * @param goAction 
	 * @param responsetype
	 * @param script id
	 */
         
        $url = "https://encrypted.goautodial.com/goAPI/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)

        $postfields["goUser"] = "goautodial"; #Username goes here. (required)
        $postfields["goPass"] = "JUs7g0P455W0rD11214"; #Password goes here. (required)
        $postfields["goAction"] = "goEditScript"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
        $postfields["script_id"] = $_POST['script_id']; #Desired script id. (required)
        $postfields["script_name"] = $_POST['script_name'];
        $postfields["script_comments"] = $_POST['script_comments'];
        $postfields["script_text"] = $_POST['script_text'];
        $postfields["active"] = $_POST['active'];
        $postfields['hostname'] = $_SERVER['SERVER_ADDR'];

        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);
        
        echo $data;
?>