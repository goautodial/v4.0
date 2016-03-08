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

namespace creamy;

// dependencies
require_once('CRMDefaults.php');
require_once('DbHandler.php');
require_once('ModuleHandler.php');
require_once('UIHandler.php');
require_once('LanguageHandler.php');

/**
 * Generates the JSON that shows the customer list using server-side processing in DataTables
 * Partly based on the jQuery datatables server-side processing found at 
 * http://datatables.net/development/server-side/php_mysql
 */
 
// constants
define ('CRM_CUSTOMER_DATATABLE_LIMIT', 'iDisplayLength');
define ('CRM_CUSTOMER_DATATABLE_OFFSET', 'iDisplayStart');
define ('CRM_CUSTOMER_DATATABLE_SORT_COLUMN', 'iSortCol_');
define ('CRM_CUSTOMER_DATATABLE_SORT_COLUMNS', 'iSortingCols');
define ('CRM_CUSTOMER_DATATABLE_SORT_DIRECTION', 'sSortDir_');
define ('CRM_CUSTOMER_DATATABLE_IS_SORTABLE', 'bSortable_');
define ('CRM_CUSTOMER_DATATABLE_SEARCH', 'sSearch');
define ('CRM_CUSTOMER_DATATABLE_SEARCHABLE', 'bSearchable_');
define ('CRM_CUSTOMER_DATATABLE_ECHO', 'sEcho');

//edited
//define ('CRM_CUSTOMER_COLUMN_FNAME', 'first_name');
//define ('CRM_CUSTOMER_COLUMN_MNAME', 'middle_initial');
//define ('CRM_CUSTOMER_COLUMN_LNAME', 'last_name');
$name = "CONCAT_WS(' ',first_name, middle_initial,last_name)";
//define("NAME", serialize(array("first_name", "middle_initial", "last_name")));
define ('CRM_CUSTOMER_COLUMN_ID', 'lead_id');
define ('CRM_CUSTOMER_COLUMN_NAME', $name);
//define ('CRM_CUSTOMER_COLUMN_ID', 'id');

/**
 * Error function, used to return a "unable to get data" message.
 */
function fatal_error($sErrorMessage = '') {
    header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
    die( $sErrorMessage );
}

/**
 * This function applies the module hooks for the list fields to be returned.
 */
function filteredResultsForCustomerColumn($customer, $customer_type) {
	$result = array();
	// Module Handler 
	$mh = \creamy\ModuleHandler::getInstance();

	// default filtering for column name: includes a link for editing the customer data.
	//edited
	if (isset($customer[$name]) && isset($customer[CRM_CUSTOMER_COLUMN_ID])) {
           $customer[CRM_CUSTOMER_COLUMN_NAME] = "<a href=\"editcustomer.php?customerid=".$customer[$name]."</a>";
	}
	//if (isset($customer[CRM_CUSTOMER_COLUMN_FNAME]) && isset($customer[CRM_CUSTOMER_COLUMN_ID])) {
		//$customer[CRM_CUSTOMER_COLUMN_FNAME] = "<a href=\"editcustomer.php?customerid=".$customer[CRM_CUSTOMER_COLUMN_ID]."&customer_type=".$customer_type."\" >".$customer[CRM_CUSTOMER_COLUMN_FNAME]."</a>";
	//	$customer[CRM_CUSTOMER_COLUMN_MNAME] = "<a href=\"editcustomer.php?customerid=".$customer[CRM_CUSTOMER_COLUMN_ID]."&customer_type=".$customer_type."\" >".$customer[CRM_CUSTOMER_COLUMN_MNAME]."</a>";
		//$customer[CRM_CUSTOMER_COLUMN_LNAME] = "<a href=\"editcustomer.php?customerid=".$customer[CRM_CUSTOMER_COLUMN_ID]."&customer_type=".$customer_type."\" >".$customer[CRM_CUSTOMER_COLUMN_LNAME]."</a>";
	
	//if (isset($customer[CRM_CUSTOMER_COLUMN_NAME]) && isset($customer[CRM_CUSTOMER_COLUMN_ID])) {
	//	$customer[CRM_CUSTOMER_COLUMN_NAME] = "<a href=\"editcustomer.php?customerid=".$customer[CRM_CUSTOMER_COLUMN_ID]."&customer_type=".$customer_type."\" >".$customer[CRM_CUSTOMER_COLUMN_NAME]."</a>";
	//}
	
	// Apply hook filters from modules, sequentially.
	$result = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_CUSTOMER_LIST_FIELDS, array(CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_FIELDS => $customer), CRM_MODULE_MERGING_STRATEGY_SEQUENCE);

	return isset($result) ? $result : $customer;
}

/**
 * This function generates the HTML code for the trailing button that allows the user to
 * perform custom actions for a given user.
 */
function actionButtonToCustomer($customer, $customer_type, $db) {
	// Language Handler
	$lh = \creamy\LanguageHandler::getInstance();
	// Module Handler 
	$mh = \creamy\ModuleHandler::getInstance();
	// UI Handler
	$ui = \creamy\UIHandler::getInstance();
	
	// Build basic button options: modify, delete.
	$modifyLink = "editcustomer.php?customerid=".$customer[CRM_CUSTOMER_COLUMN_ID]."&customer_type=$customer_type";
	$modifyOption = $ui->actionForPopupButtonWithLink($modifyLink, $lh->translationFor("modify"));
	$deleteOption = $ui->actionForPopupButtonWithOnClickCode($lh->translationFor("delete"), "deleteCustomer", array($customer["id"], $customer_type));
	$eventOption = $ui->actionForPopupButtonWithOnClickCode($lh->translationFor("create_reminder_event"), "createEventForCustomer", array($customer["id"], $customer_type));
	$separator = $ui->separatorForPopupButton();
	
	// change customer type. One for every customer type except current one.
	$typeOptions = "";
	$customerTypes = $db->getCustomerTypes();
	foreach ($customerTypes as $otherCustomerType) {
		if ($otherCustomerType["table_name"] != $customer_type) { // add a change to... button.
			$changeTypeText = $lh->translationFor("move_to")." ".$otherCustomerType["description"];
			$typeOptions .= $ui->actionForPopupButtonWithOnClickCode($changeTypeText, "changeCustomerType", array($customer["id"], $customer_type, $otherCustomerType["table_name"]));
		}
	}
	
	// add any module action.
	$args = array(
		//edited
		//CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_ID => $customer["lead_id"], 
		//CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_NAME => $customer["first_name"],
		CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_ID => $customer["id"], 
		CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_NAME => $customer["name"], 
		CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_TYPE => $customer_type);
	$moduleActions = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_CUSTOMER_LIST_POPUP, $args, CRM_MODULE_MERGING_STRATEGY_APPEND);
	
	// build the pop up action button and return it.
	$result = $ui->popupActionButton($lh->translationFor("choose_action"), array($modifyOption, $moduleActions, $eventOption, $separator, $typeOptions, $separator, $deleteOption), array("default"));
	return $result;
}

// initialize database
$db = new \creamy\DbHandler();

// get customer_type and customerid for selecting the right customers.
if (!isset($_GET["customer_type"])) {
	fatal_error("Wrong request. Missing customer_type");
}
$customer_type = $_GET["customer_type"];
if($customer_type == "clients_1")$customer_type = "vicidial_list";
// paging
$length = isset($_GET[CRM_CUSTOMER_DATATABLE_LIMIT]) ? intval($_GET[CRM_CUSTOMER_DATATABLE_LIMIT]) : 10;
$offset = isset($_GET[CRM_CUSTOMER_DATATABLE_OFFSET]) ? intval($_GET[CRM_CUSTOMER_DATATABLE_OFFSET]) : null;
$numRows = isset($offset) ? array($offset, $length) : $length;
 
// Ordering
$columns = $db->getCustomerColumnsToBeShownInCustomerList($customer_type);
$sorting = array();
if (isset($_GET[CRM_CUSTOMER_DATATABLE_SORT_COLUMN."0"])) {
    for ($i=0 ; $i<intval($_GET[CRM_CUSTOMER_DATATABLE_SORT_COLUMNS]); $i++) {
        if ($_GET[CRM_CUSTOMER_DATATABLE_IS_SORTABLE.intval($_GET[CRM_CUSTOMER_DATATABLE_SORT_COLUMN.$i])] == "true") {
	        $columnToSort = $columns[intval($_GET[CRM_CUSTOMER_DATATABLE_SORT_COLUMN.$i])];
	        $sortType = ($_GET[CRM_CUSTOMER_DATATABLE_SORT_DIRECTION.$i]==='asc' ? 'asc' : 'Desc');
	        $sorting[$columnToSort] = $sortType;
        }
    }
}
 
// filtering
$filtering = array();
if (isset($_GET[CRM_CUSTOMER_DATATABLE_SEARCH]) && $_GET[CRM_CUSTOMER_DATATABLE_SEARCH] != "") {
	$wordToSearch = $db->escape_string($_GET[CRM_CUSTOMER_DATATABLE_SEARCH]);
	for ($i=0; $i < count($columns); $i++) {
        if (isset($_GET[CRM_CUSTOMER_DATATABLE_SEARCHABLE.$i]) && $_GET[CRM_CUSTOMER_DATATABLE_SEARCHABLE.$i] == "true") {
			$columnToSearch = $columns[$i];
			$filtering[$columnToSearch] = $wordToSearch;
        }
    }
}
//edited
//var_dump($customer_type);
if($customer_type == "vicidial_list"){
	// getdata
	$result = $db->getAllContactOfType_ASTERISK($customer_type, $numRows, $sorting, $filtering);
	if (!isset($result)) { fatal_error("Error retrieving data from the database."); }
}else{
// get data

	$result = $db->getAllCustomersOfType($customer_type, $numRows, $sorting, $filtering);
	if (!isset($result)) { fatal_error("Error retrieving data from the database."); }
}

// data set length after filtering:
$filteredRows = $db->unlimitedRowCount();

// total data set length
$totalCustomers = $db->getNumberOfClientsFromTable($customer_type); 

// output
$output = array(
    "sEcho" => intval($_GET[CRM_CUSTOMER_DATATABLE_ECHO]),
    "iTotalRecords" => $totalCustomers,
    "iTotalDisplayRecords" => $filteredRows,
    "aaData" => array()
);

// build the data array
foreach ($result as $customer) {
	$filtered = filteredResultsForCustomerColumn($customer, $customer_type);
	$filtered = array_values($filtered);
	$filtered[] = actionButtonToCustomer($customer, $customer_type, $db);
	$output['aaData'][] = $filtered;
}
ob_clean();
echo json_encode($output);
?>
