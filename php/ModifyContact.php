<?php
/**
 * @file        ModifyContacs.php
 * @brief       Modify customer information
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author		Jerico James F. Milo
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

	require_once( "APIHandler.php" );
	
	$api 											= \creamy\APIHandler::getInstance();

	$list_id 										= $_POST["list_id"];
	$lead_id 										= $_POST["lead_id"];
	$first_name 									= $_POST["first_name"];
	$middle_initial 								= $_POST["middle_initial"];
	$last_name 										= $_POST["last_name"];
	$email 											= $_POST["email"];
	$phone_number 									= $_POST["phone_number"];
	$alt_phone 										= $_POST["alt_phone"];
	$address1 										= $_POST["address1"];
	$address2 										= $_POST["address2"];
	$address3 										= $_POST["address3"];
	$city  											= $_POST["city"];
	$state 											= $_POST["state"];
	$title 											= $_POST["title"];
	$dispo 											= $_POST["dispo"];
	$is_customer 									= ( isset($_POST["is_customer"]) ) ? $_POST["is_customer"] : false;
	$user_id 										= $_POST["user_id"];

	// get name (mandatory), customer id and customer type
	$first_name 									= $_POST["first_name"];
	$first_name	 									= stripslashes( $first_name );
	
	$middle_initial 								= $_POST["middle_initial"];
	$middle_initial 								= stripslashes( $middle_initial );
	
	$last_name 										= $_POST["last_name"];
	$last_name 										= stripslashes( $last_name );
	
	$lead_id 										= $_POST["lead_id"];
	$lead_id 										= stripslashes( $lead_id );
	
	$list_id 										= $_POST["list_id"];
	$list_id 										= stripslashes( $list_id );
	
	// email
	$email 											= NULL; 
	if ( isset($_POST["email"]) ) { 
		$email 										= $_POST["email"]; 
		$email 										= stripslashes( $email );
	}
	// phone
	$phone 											= NULL; 
	if ( isset($_POST["phone_number"]) ) { 
		$phone 										= $_POST["phone_number"];
		$phone 										= preg_replace( "/[^0-9]/", "", $phone );
		$phone 										= stripslashes( $phone );
	}
	// alt phone
	$alt_phone 										= NULL; 
	if ( isset($_POST["alt_phone"]) ) { 
		$alt_phone 									= $_POST["alt_phone"];
		$alt_phone 									= preg_replace( "/[^0-9]/", "", $alt_phone );
		$alt_phone 									= stripslashes( $alt_phone );
	}
	// address1
	$address1 										= NULL; 
	if ( isset($_POST["address1"]) ) { 
		$address1 									= $_POST["address1"]; 
		$address1 									= stripslashes( $address1 );
	}
	// address2
	$address2 										= NULL; 
	if ( isset($_POST["address2"]) ) { 
		$address2 									= $_POST["address2"]; 
		$address2 									= stripslashes( $address2 );
	}
	// address3
	$address3 										= NULL; 
	if ( isset($_POST["address3"]) ) { 
		$address3 									= $_POST["address3"]; 
		$address3 									= stripslashes( $address3 );
	}
	
	// city
	$city 											= NULL; 
	if ( isset($_POST["city"]) ) { 
		$city 										= $_POST["city"]; 
		$city 										= stripslashes( $city );
	}
	
	// state
	$state 											= NULL;
	if ( isset($_POST["state"]) ) { 
		$state 										= $_POST["state"]; 
		$state 										= stripslashes( $state );
	}

	// province
	$province 										= NULL; 
	if ( isset($_POST["province"]) ) { 
		$province 									= $_POST["province"]; 
		$province 									= stripslashes( $province );
	}
	
	// ZIP code
	$postal_code 									= NULL; 
	if ( isset($_POST["postal_code"]) ) { 
		$postal_code 								= $_POST["postal_code"]; 
		$postal_code 								= stripslashes( $postal_code) ;
	}
	
	// country
	$country 										= NULL; 
	if ( isset($_POST["country"]) ) { 
		$country 									= $_POST["country"]; 
		$country 									= stripslashes( $country );
	}
	
	// date_of_birth
	$date_of_birth 									= NULL; 
	if ( isset($_POST["date_of_birth"]) ) { 
		$date_of_birth 								= $_POST["date_of_birth"]; 
		$date_of_birth 								= date( "Y-m-d h:i:s", strtotime($date_of_birth) );
		$date_of_birth 								= stripslashes( $date_of_birth );
	}

	// gender
	$gender 										= NULL; 
	if ( isset($_POST["gender"]) ) { 
		$gender 									= $_POST["gender"]; 
		$gender 									= stripslashes($gender);
	}
	
	// dispo
	$dispo 											= NULL; 
	if ( isset($_POST["dispo"]) ) { 
		$dispo 										= $_POST["dispo"]; 
		$dispo 										= stripslashes( $dispo );
	}

	// comments
	$comments 										= NULL; 
	if ( isset($_POST["comments"]) ) { 
		$comments 									= $_POST["comments"]; 
		$comments 									= stripslashes( $comments );
	}
	
	// no enviar email
	$donotsendemail 								= 0; 
	if ( isset($_POST["donotsendemail"]) ) { 
		$donotsendemail 							= 1;
	}

	//$is_customer 									= 0;
	if ( $is_customer === "true" ) {
		$is_customer 								= 1;
	} else {
		$is_customer = 0;
	}
	
	if ( isset($_POST["custom_fields"]) ) {
		$c_fields = explode(",", $_POST["custom_fields"]);
		foreach ($c_fields as $field) {
			$custom_fields[$field] = $_POST[$field];
		}
	}
		
	$postfields 									= array(	
		"goAction" 										=> "goEditLeads",
		"list_id"										=> $list_id,
		"lead_id" 										=> $lead_id, 
		"first_name" 									=> $first_name, 
		"middle_initial" 								=> $middle_initial, 
		"last_name" 									=> $last_name, 
		"gender" 										=> $gender, 
		"email" 										=> $email, 
		"phone_number" 									=> $phone_number, 
		"alt_phone" 									=> $alt_phone, 
		"address1"										=> $address1, 
		"address2" 										=> $address2, 
		"address3" 										=> $address3, 
		"city" 											=> $city,
		"state"											=> $state,
		"province" 										=> $province, 
		"postal_code" 									=> $postal_code, 
		"country_code" 									=> $country, 
		"date_of_birth" 								=> $date_of_birth, 
		"title" 										=> $title, 
		"status" 										=> $dispo,
		"comments"										=> $comments,		
		"avatar" 										=> "",
		"is_customer"									=> $is_customer,
		"user_id" 										=> $user_id,
		"custom_fields"									=> $custom_fields
    );

	$output 										= $api->API_editLeads($postfields);

	if ($output->result=="success") { 
		$status 									= 1; 
	} else { 
		$status 									= $output->result; 
	}

	echo json_encode($status);    

?>
