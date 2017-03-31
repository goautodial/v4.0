<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('goCRMAPISettings.php');
        
        // collect new user data.       
        $modifyid = $_POST["modifyid"];
    
        $script_name = NULL; if (isset($_POST["script_name"])) { 
                $script_name = $_POST["script_name"]; 
                $script_name = stripslashes($script_name);
        }
        
        $script_comments = NULL; if (isset($_POST["script_comments"])) { 
                $script_comments = $_POST["script_comments"];
                $script_comments = stripslashes($script_comments);
        }

        $script_text = NULL; if (isset($_POST["script_text"])) { 
                $script_text = $_POST["script_text"]; 
                $script_text = stripslashes($script_text);
        }

        $active = NULL; if (isset($_POST["active"])) { 
                $active = $_POST["active"]; 
                $active = stripslashes($active);
        }

        $script_user_group = NULL; if (isset($_POST["script_user_group"])) { 
                $script_user_group = $_POST["script_user_group"]; 
                $script_user_group = stripslashes($script_user_group);
        }

        $url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"]           = goUser; #Username goes here. (required)
        $postfields["goPass"]           = goPass; #Password goes here. (required)
        $postfields["goAction"]         = "goEditScript"; #action performed by the [[API:Functions]]
        $postfields["responsetype"]     = responsetype; #json. (required)
        $postfields["hostname"] = $_SERVER['SERVER_ADDR']; #Default value
        $postfields["script_id"]        = $modifyid; #Desired script id. (required)
        $postfields["script_name"]      = $script_name;
        $postfields["script_comments"]  = $script_comments;
        $postfields["script_text"]      = $script_text;
        $postfields["active"]           = $active;
        $postfields["user_group"]       = $script_user_group;
        
        $postfields["log_user"]         = $_POST['log_user'];
        $postfields["log_group"]        = $_POST['log_group'];

         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);
        
//      print_r($data);

        if ($output->result=="success") {
           # Result was OK!
                echo "success";  
         } else {
           # An error occured
                echo $output->result;
        }
?>