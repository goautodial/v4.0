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

require_once('DbHandler.php');
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require('Session.php');

$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

// check required fields
$validated = 1;
if (!isset($_POST["fname"])) {
	$validated = 0;
}
if (!isset($_POST["lname"])) {
	$validated = 0;
}
if (!isset($_POST["customer_type"])) {
	$validated = 0;
}

if ($validated == 1) {
	$db = new \creamy\DbHandler();

//alex
	// get name (mandatory)
	$first_name = $_POST["fname"];
	$first_name = stripslashes($first_name);
	$first_name = $db->escape_string($first_name);
	
	$last_name = $_POST["lname"];
	$last_name = stripslashes($last_name);
	$last_name = $db->escape_string($last_name);

	$customerType = $_POST["customer_type"];
	$customerType = stripslashes($customerType);
	$customerType = $db->escape_string($customerType);
	
	$createdByUser = $user->getUserId();
		
	// get optional values

	// middle initial
	$middle_initial = $_POST["mi"];
	$middle_initial = stripslashes($middle_initial);
	$middle_initial = $db->escape_string($middle_initial);

	// email
	$email = NULL; if (isset($_POST["email"])) { 
		$email = $_POST["email"]; 
		$email = stripslashes($email);
		$email = $db->escape_string($email);
	}
	// phone
	$phone = NULL; if (isset($_POST["phone"])) { 
		$phone = $_POST["phone"];
		$phone = stripslashes($phone);
		$phone = $db->escape_string($phone); 
	}
	// alt phone
	$alt_phone = NULL; if (isset($_POST["alt_phone"])) { 
		$alt_phone = $_POST["alt_phone"];
		$alt_phone = stripslashes($alt_phone);
		$alt_phone = $db->escape_string($alt_phone); 
	}
//--
	// address1
	$address1 = NULL; if (isset($_POST["address1"])) { 
		$address1 = $_POST["address1"]; 
		$address1 = stripslashes($address1);
		$address1 = $db->escape_string($address1);
	}
	// address2
	$address2 = NULL; if (isset($_POST["address2"])) { 
		$address2 = $_POST["address2"]; 
		$address2 = stripslashes($address2);
		$address2 = $db->escape_string($address2);
	}
	// address3
	$address3 = NULL; if (isset($_POST["address3"])) { 
		$address3 = $_POST["address3"]; 
		$address3 = stripslashes($address3);
		$address3 = $db->escape_string($address3);
	}
	// city
	$city = NULL; if (isset($_POST["city"])) { 
		$city = $_POST["city"]; 
		$city = stripslashes($city);
		$city = $db->escape_string($city);
	}
	
	// state
	$state = NULL; if (isset($_POST["state"])) { 
		$state = $_POST["state"]; 
		$state = stripslashes($state);
		$state = $db->escape_string($state);
	}
//--
	// province
	$province = NULL; if (isset($_POST["province"])) { 
		$province = $_POST["province"]; 
		$province = stripslashes($province);
		$province = $db->escape_string($province);
	}

	// ZIP code
	$postal_code = NULL; if (isset($_POST["postal_code"])) { 
		$postal_code = $_POST["postal_code"]; 
		$postal_code = stripslashes($postal_code);
		$postal_code = $db->escape_string($postal_code);
	}
	
	// country code
	$country_code = NULL; if (isset($_POST["country_code"])) { 
		$country_code = $_POST["country_code"]; 
		$country_code = stripslashes($country_code);
		$country_code = $db->escape_string($country_code);
	}
	
			
	// notes
	$comments = NULL; if (isset($_POST["comments"])) { 
		$comments = $_POST["comments"]; 
		$comments = stripslashes($comments);
		$comments = $db->escape_string($comments);
	}
	
	// fecha de nacimiento
	$date_of_birth = NULL; if (isset($_POST["date_of_birth"])) { 
		$date_of_birth = $_POST["date_of_birth"]; 
		$date_of_birth = stripslashes($date_of_birth);
		$date_of_birth = $db->escape_string($date_of_birth);
	}

	// gender
	$gender = NULL; if (isset($_POST["gender"])) { 
		$gender = $_POST["gender"]; 
		$gender = stripslashes($gender);
		$gender = $db->escape_string($gender);
	}
	if ($gender < 0 || $gender > 1) $gender = NULL;
	if ($gender == 0) $gender = "F";
	if ($gender == 1) $gender = "M";
	
	
	// create customer and return result.
	$result = $db->createCustomer($customerType, $first_name, $middle_initial, $last_name, $email, $phone, $alt_phone, 
		$address1, $address2, $address3, $city, $state, $province, $postal_code, $country_code, $date_of_birth, $createdByUser, $gender, $comments);
	//var_dump($result);
	if ($result === true) { ob_clean(); print CRM_DEFAULT_SUCCESS_RESPONSE; }
	else { ob_clean(); $lh->translateText("unable_create_customer"); } 
} else { ob_clean(); $lh->translateText("some_fields_missing"); }
?>