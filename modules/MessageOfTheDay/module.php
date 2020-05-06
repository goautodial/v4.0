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

require_once(CRM_MODULE_INCLUDE_DIRECTORY.'Module.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'CRMDefaults.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'LanguageHandler.php');
include(CRM_MODULE_INCLUDE_DIRECTORY.'Session.php');

/**
 * This module is an example of how to write a module for Creamy.
 * It will show a message of the day (message of the day).
 */
class MessageOfTheDay extends Module {

	// module meta-data (ModuleData interface implementation).
	
	static function getModuleName() { return "Message Of The Day"; }
	
	static function getModuleVersion() { return "1.0"; }
	
	static function getModuleDescription() { return "A simple module that shows a Message Of The Day."; }

	// lifecycle and respond to interactions.

	public function uponInit() {
		error_log("Module \"Message of the day\" initializing...");
		
		// add the message of the day translation files to our language handler.
		$customLanguageFile = $this->getModuleLanguageFileForLocale($this->lh()->getLanguageHandlerLocale());
		if (!isset($customLanguageFile)) { $customLanguageFile = $this->getModuleLanguageFileForLocale(CRM_LANGUAGE_DEFAULT_LOCALE); }
		$this->lh()->addCustomTranslationsFromFile($customLanguageFile);
	}
		
	public function uponActivation() {
		error_log("Module \"Message of the day\" activating...");
	}
		
	public function uponDeactivation() {
		error_log("Module \"Message of the day\" deactivating...");
	}

	public function uponUninstall() {
		error_log("Module \"Message of the day\" uninstalling...");
	}
	
	// Private functions for this module.
	
	private function searchQuoteURL($text) {
		return $url = "https://duckduckgo.com/?q=".urlencode($text)."&ia=quotations";
	}
	
	private function randomQuotes($number) {
		require_once(CRM_MODULE_INCLUDE_DIRECTORY.'RandomStringGenerator.php');
		$random = new \creamy\RandomStringGenerator();
		$quotes = array();
		for ($i = 0; $i < $number; $i++) {
			$rnd = $random->getRandomInteger(1, 10);
			$author = $this->lh()->translationFor("author_".$rnd);
			$quote = $this->lh()->translationFor("quote_".$rnd);
			$quotes[] = array("quote" => $quote, "author" => $author, "author_number" => $rnd);
		}
		return $quotes;
	}
	
	private function sectionWithRandomQuotes($number) {
		$quotes = $this->randomQuotes($number);
		$content = "";
		foreach ($quotes as $quote) {
			$showAuthor = $this->valueForModuleSetting("show_quote_author");
			$authorText = $showAuthor ? $quote["author"] : null;
			$quoteBox = $this->ui()->boxWithQuote($this->lh()->translationFor("message_of_the_day"), $quote["quote"], $authorText);
			$content .= $this->ui()->fullRowWithContent($quoteBox);			
		}
		return $content;
	}
	
	private function sectionWithCustomQuote($customQuote, $author) {
		// quote box
		$quoteBox = $this->ui()->boxWithQuote($this->lh()->translationFor("message_of_the_day"), $customQuote, $author);
		$result = $this->ui()->fullRowWithContent($quoteBox);
		return $result;
	}

	private function dateIsToday($date) {
		 $current = strtotime(date("Y-m-d"));
		
		 $datediff = $date - $current;
		 $differance = floor($datediff/(60*60*24));
		 if ($differance == 0) return true;
		 return false;
	}
	
	// views and code generation

	/** We return true here to indicate that we want access to the database */
	public function needsDatabaseFunctionality() { return true; }

	/** Our table will contain a favorite quote for our customers */
	public function databaseTableFields() {
		return array(
			"customer_id" => "INT(11) NOT NULL",
			"customer_type" => "VARCHAR(255) NOT NULL",
			"favorite_quote" => "TEXT DEFAULT NULL"
		);
	}

	/** We return true here to indicate that we want our main content view to be accessed by means of the sidebar */
	public function needsSidebarDisplay() { return true; }

	public function mainPageViewContent($args) {
		// check if we have some arguments (desired quote).
		$authorNumber = 0;
		if (isset($args["author_number"])) {
			$authorNumber = intval($args["author_number"]);
		}

		// do we have a desired quote author to show?
		if ($authorNumber >= 1 && $authorNumber <= 10) {
			$quote = $this->lh()->translationFor("quote_$authorNumber");
			$authorName = $this->lh()->translationFor("author_$authorNumber");
			return $this->sectionWithCustomQuote($quote, $authorName);
		} else {
			// check if today we must show a custom quote.
			$customDate = $this->valueForModuleSetting("custom_quote_day");
			$customQuote = $this->valueForModuleSetting("custom_quote");
			if (!empty($customDate) && !empty($customQuote)) {
				if ($this->dateIsToday(strtotime($customDate))) {
					return $this->sectionWithCustomQuote($customQuote, \creamy\CreamyUser::currentUser()->getUserName());
				}
			}
			
			// random quotes.
			$number = intval($this->valueForModuleSetting("number_of_quotes"));
			if ($number < 1) { $number = 1; }
			if ($number > 5) { $number = 5; } // max quotes.
			return $this->sectionWithRandomQuotes($number);
		}
		
		
	}

	public function mainPageViewTitle() {
		return $this->lh()->translationFor("message_of_the_day");
	}
	
	public function mainPageViewSubtitle() { 
		return $this->lh()->translationFor("a_simple_creamy_module"); 
	}
	
	public function mainPageViewIcon() {
		return "quote-left";
	}
	
	// hooks
	
	public function dashboardHook($wantsFullRow = true) {
		// check if today we must show a custom quote.
		$customDate = $this->valueForModuleSetting("custom_quote_day");
		$customQuote = $this->valueForModuleSetting("custom_quote");
		if (!empty($customDate) && !empty($customQuote)) {
			if ($this->dateIsToday(strtotime($customDate))) {
				return $this->sectionWithCustomQuote($customQuote, \creamy\CreamyUser::currentUser()->getUserName());
			}
		}
		
		// else just one quote for the dashboard hook.
		return $this->sectionWithRandomQuotes(1);
	}
	
	public function customerListFieldsHook($fields) {
		if (array_key_exists("name", $fields)) {
			$authorNumbers = range(1, 10);
			foreach ($authorNumbers as $authorNumber) {
				$author = "author_$authorNumber";
				$authorName = $this->lh()->translationFor($author);
				if (stripos($fields["name"], $authorName)) {
					$href = $this->mainPageViewURL(array("author_number" => $authorNumber));
					$link = "<a style='color: red;' href='$href'> [".$this->lh()->translationFor("quote_author")."]</a>";
					$fields["name"] = str_ireplace($authorName, $authorName.$link, $fields["name"]);
				}
			}
			return $fields;			
		} else { return $fields; }

	}
	
	public function customerListPopupHook($customerid, $customername, $customertype) {
		$authorname = urlencode(trim($customername));
		$url = $this->searchQuoteURL($authorname);
		return $this->ui()->actionForPopupButtonWithLink($url, $this->lh()->translationFor("search_quotes"));
	}
	
	public function customerListFooterHook($customertype) {
		$url = $this->mainPageViewURL(null);
		return $this->ui()->simpleLinkButton("motd", $this->lh()->translationFor("message_of_the_day"), $url, $icon = "quote-left");
	}
	
	public function customerDetailHook($customerid, $customertype) {
		// get customer favorite quote (if any).
		$this->db()->where("customer_id", $customerid);
		$this->db()->where("customer_type", $customertype);
		$result = $this->db()->getOne($this->databaseTableName());
		$currentQuote = isset($result) ? $result["favorite_quote"] : "";
		// generate form with request. We have one quote input text and two hidden fields.
		$quoteField = $this->ui()->singleFormGroupWithInputGroup( $this->ui()->singleFormInputElement(
				"quote", 										// input id
				"quote", 										// input name
				"text", 										// input type (text).
				$this->lh()->translationFor("favorite_quote"), 	// input placeholder
				$currentQuote,									// value (if current quote set).
				"quote-left"), 									// input icon to be shown at the left
			$this->lh()->translationFor("favorite_quote"));		// label for the input 
		$customerIdField = $this->ui()->hiddenFormField("customerid", $customerid);
		$customerTypeField = $this->ui()->hiddenFormField("customertype", $customertype);
		
		
		$formCode = $this->ui()->formForCustomHook(
			"quote_form", 										// form id
			$this->getModuleShortName(), 						// module short name
			"setQuoteForFavoriteCustomer", 						// custom hook name (function name).
			$quoteField.$customerIdField.$customerTypeField,	// content for the form (inputs).
			$this->lh()->translationFor("set_quote"),			// submit button text.
			"quote_result");									// result message tag
		
		// generate javascript 
		$failureText = $this->ui()->calloutErrorMessage($this->lh()->translationFor("unable_set_quote"));
		$successText = $this->ui()->calloutInfoMessage($this->lh()->translationFor("quote_set"));
		$js = $this->ui()->formPostJS(
			"quote_form", 													// name of the form.
			$this->customActionModulePageURL(), 							// page to send the Ajax POST request
			$this->ui()->fadingInMessageJS($successText, "quote_result"),	// on success message
			$this->ui()->fadingInMessageJS($failureText, "quote_result"),	// on failure message
			$this->ui()->fadingOutMessageJS(false, "quote_result"));		// preamble JS: clean result message.
		// return HTML code + Javascript.
		return $this->ui()->boxWithContent($this->lh()->translationFor("favorite_quote"), $formCode).$js;
	}
	
	public function setQuoteForFavoriteCustomer($customerid, $customertype, $quote) {
		$success = false;
		// get customer favorite quote (if any).
		$this->db()->where("customer_id", $customerid)->where("customer_type", $customertype);
		$result = $this->db()->getOne($this->databaseTableName());
		if (isset($result) && isset($result["favorite_quote"])) {
			// try to update current value
			$this->db()->where("customer_id", $customerid);
			$this->db()->where("customer_type", $customertype);
			$data = array("favorite_quote" => $quote);
			$success = $this->db()->update($this->databaseTableName(), $data);
		} else {
			$data = array("customer_id" => $customerid, "customer_type" => $customertype, "favorite_quote" => $quote);
			$success = $this->db()->insert($this->databaseTableName(), $data);
		}
		
		return $success ? CRM_DEFAULT_SUCCESS_RESPONSE : $this->lh()->translationFor("unable_set_quote");
	}
		
	/*public function notificationsHook($period) {
		return $this->ui()->timelineItemWithData("Hola mundo", "Hola como estás mundo?", "2015-04-11 22:57:15", "http://duckduckgo.com", "Touch me!");
	}*/
	
	public function taskListHoverHook($taskid) {
		$this->db()->where("id", $taskid);
		$task = $this->db()->getOne(CRM_TASKS_TABLE_NAME);
		if (isset($task)) { 
			$text = $task["description"]; 
			$url = $this->searchQuoteURL($text);
		} else { $url = "http://duckduckgo.com"; }
		return $this->ui()->hoverActionButton("quote_task_action", "quote-left", $url);
	}
	
	public function topBarHook() {
		$numberOfQuotes = 3;
		$header = $this->ui()->getTopbarMenuHeader("quote-left", $numberOfQuotes, CRM_UI_TOPBAR_MENU_STYLE_SIMPLE, $this->lh()->translationFor("some_random_quotes"));
		$elements = "";
		$quotes = $this->randomQuotes($numberOfQuotes);
		foreach ($quotes as $quote) {
			$elements .= $this->ui()->getTopbarSimpleElement($quote["quote"]." -- ".$quote["author"], "quote-right", $this->mainPageViewURL(array("author_number" => $quote["author_number"])));
		}
		$footer = $this->ui()->getTopbarMenuFooter($this->lh()->translationFor("see_more_quotes"), $this->mainPageViewURL(null));
		return $this->ui()->getTopbarCustomMenu($header, $elements, $footer);
	}
	
	// settings
	
	public function moduleSettings() {
		$authors = array();
		for ($i = 1; $i <= 10; $i++) { $authors["author_$i"] = $this->lh()->translationFor("author_$i"); }
		return array("show_quote_author" => CRM_SETTING_TYPE_BOOL, "custom_quote" => CRM_SETTING_TYPE_STRING, "custom_quote_day" => CRM_SETTING_TYPE_DATE, "favorite_author" => $authors, "number_of_quotes" => CRM_SETTING_TYPE_INT); 
	}
	
}

?>