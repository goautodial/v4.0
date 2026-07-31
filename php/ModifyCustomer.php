<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once(__DIR__ . '/CRMDefaults.php');
require_once(__DIR__ . '/goCRMAPISettings.php');

$lead_id = $_POST['lead_id'];
$first_name = $_POST['first_name'];
$middle_initial = $_POST['middle_initial'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$alt_phone = $_POST['alt_phone'];
$address1 	= $_POST['address1'];
$address2 	= $_POST['address2'];
$address3 	= $_POST['address3'];
$city  = $_POST['city'];
$state = $_POST['state'];
$title = $_POST['title'];

	// get name (mandatory), customer id and customer type
	$first_name = $_POST['first_name'];
	$first_name = stripslashes((string) $first_name);

	$middle_initial = $_POST["middle_initial"];
	$middle_initial = stripslashes((string) $middle_initial);

	$last_name = $_POST["last_name"];
	$last_name = stripslashes((string) $last_name);

	$lead_id = $_POST['lead_id'];
	$lead_id = stripslashes((string) $lead_id);

	// email
	$email = NULL; if (isset($_POST["email"])) { 
		$email = $_POST["email"]; 
		$email = stripslashes((string) $email);
	}
	// phone
	$phone = NULL; if (isset($_POST["phone"])) { 
		$phone = $_POST["phone"];
		$phone = preg_replace('/[^0-9]/', '', (string) $phone);
		$phone = stripslashes($phone);
	}
	// alt phone
	$alt_phone = NULL; if (isset($_POST["alt_phone"])) { 
		$alt_phone = $_POST["alt_phone"];
		$alt_phone = preg_replace('/[^0-9]/', '', (string) $alt_phone);
		$alt_phone = stripslashes($alt_phone);
	}
	// address1
	$address1 = NULL; if (isset($_POST["address1"])) { 
		$address1 = $_POST["address1"]; 
		$address1 = stripslashes((string) $address1);
	}
	// address2
	$address2 = NULL; if (isset($_POST["address2"])) { 
		$address2 = $_POST["address2"]; 
		$address2 = stripslashes((string) $address2);
	}
	// address3
	$address3 = NULL; if (isset($_POST["address3"])) { 
		$address3 = $_POST["address3"]; 
		$address3 = stripslashes((string) $address3);
	}

	// city
	$city = NULL; if (isset($_POST["city"])) { 
		$city = $_POST["city"]; 
		$city = stripslashes((string) $city);
	}

	// state
	$state = NULL; if (isset($_POST["state"])) { 
		$state = $_POST["state"]; 
		$state = stripslashes((string) $state);
	}

	// province
	$province = NULL; if (isset($_POST["province"])) { 
		$province = $_POST["province"]; 
		$province = stripslashes((string) $province);
	}

	// ZIP code
	$postal_code = NULL; if (isset($_POST["postal_code"])) { 
		$postal_code = $_POST["postal_code"]; 
		$postal_code = stripslashes((string) $postal_code);
	}

	// country
	$country = NULL; if (isset($_POST["country"])) { 
		$country = $_POST["country"]; 
		$country = stripslashes((string) $country);
	}

	// date_of_birth
	$date_of_birth = NULL; if (isset($_POST["date_of_birth"])) { 
		$date_of_birth = $_POST["date_of_birth"]; 
		$date_of_birth = stripslashes((string) $date_of_birth);
	}

	// gender
	$gender = NULL; if (isset($_POST["gender"])) { 
		$gender = $_POST["gender"]; 
		$gender = stripslashes((string) $gender);
	}

	// comments
	$comments = NULL; if (isset($_POST["comments"])) { 
		$comments = $_POST["comments"]; 
		$comments = stripslashes($comments);
	}

	// no enviar email
	$donotsendemail = 0; if (isset($_POST["donotsendemail"])) { 
		$donotsendemail = 1;
	}

	// modify customer
	//$result = $db->modifyCustomer($customerType, $customerid, $fname, $mi, $lname, $email, $phone, $alt_phone, $address1, $address2, $address3, 
	//	$city, $state, $province, $postal_code, $country_code, $date_of_birth, $createdByUser, $gender, $comments);

	$url = gourl."/goGetLeads/goAPI.php"; # URL to GoAutoDial API file
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditLeads"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)

	$postfields["lead_id"] 			= $lead_id; #Desired value for user (required)
	$postfields["first_name"] 		= $first_name; #Desired value for user (required)
	$postfields["middle_initial"] 	= $middle_initial; #Desired value for user (required)
	$postfields["last_name"] 		= $last_name; #Desired value for user (required)
	$postfields["gender"] 			= $gender; #Desired value for user (required)
	$postfields["email"] 			= $email; #Desired value for user (required)
	$postfields["phone_number"] 	= $phone_number; #Desired value for user (required)
	$postfields["alt_phone"] 		= $alt_phone; #Desired value for user (required)
	$postfields["address1"]			= $address1; #Desired value for user (required)
	$postfields["address2"] 		= $address2; #Desired value for user (required)
	$postfields["address3"] 		= $address3; #Desired value for user (required)
	$postfields["city"] 			= $city; #Desired value for user (required)
	$postfields["province"] 		= $province; #Desired value for user (required)
	$postfields["postal_code"] 		= $postal_code; #Desired value for user (required)
	$postfields["country_code"] 	= $country; #Desired value for user (required)
	$postfields["date_of_birth"] 	= $date_of_birth; #Desired value for user (required)
	$postfields["title"] 			= $title; #Desired value for user (required)

    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $output = json_decode($data);
    var_dump($data);
	// return result
	/*
	if ($output->result == "success") {
		echo 1;
	}else{
		echo $output->result;
	}
	*/
?>
