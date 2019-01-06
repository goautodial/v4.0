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
require_once('LanguageHandler.php');
require_once('CRMUtils.php');
require_once('ModuleHandler.php');
require_once('goCRMAPISettings.php');
//require_once('Session.php');

// constants
define ('CRM_UI_DEFAULT_RESULT_MESSAGE_TAG', "resultmessage");
error_reporting(E_ERROR | E_PARSE);

/**
 *  UIHandler.
 *  This class is in charge of generating the dynamic HTML code for the basic functionality of the system.
 *  Every time a page view has to generate dynamic contact, it should do so by calling some of this class methods.
 *  UIHandler uses the Singleton pattern, thus gets instanciated by the UIHandler::getInstante().
 *  This class is supposed to work as a ViewController, stablishing the link between the view (PHP/HTML view pages) and the Controller (DbHandler).
 */
 class UIHandler {

	// language handler
	private $lh;
	// Database handler
	private $db;
	// API handler
	private $api;
	/** Creation and class lifetime management */

	/**
     * Returns the singleton instance of UIHandler.
     * @staticvar UIHandler $instance The UIHandler instance of this class.
     * @return UIHandler The singleton instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }


    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        require_once dirname(__FILE__) . '/DbHandler.php';
        // opening db connection
        $this->db = new \creamy\DbHandler();
        $this->api = \creamy\APIHandler::getInstance();
        $this->lh = \creamy\LanguageHandler::getInstance();
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    /** Generic HTML structure */

	public function fullRowWithContent($content, $colStyle = "lg") {
		return '<div class="row"><div class="col-'.$colStyle.'-12">'.$content.'</div></div>';
	}

	public function rowWithVariableContents($spans, $columns, $columnsStyle = "lg") {
		// safety checks
		if ((!is_array($spans)) || (!is_array($columns))) { return ""; }
		if (count($spans) != count($columns)) { return ""; }
		// build the structure
		$result = '<div class="row">';
		for ($i = 0; $i < count($spans); $i++) {
			$result .= '<div class="col-'.$columnsStyle.'-'.$spans[$i].'">'.$columns[$i].'</div>';
		}
		$result .= '</div>';
		return $result;
	}

    public function boxWithContent($header_title, $body_content, $footer_content = NULL, $icon = NULL, $style = CRM_UI_STYLE_DEFAULT, $body_id = NULL, $additional_body_classes = "") {
	    // if icon is present, generate an icon item.
	    $iconItem = (empty($icon)) ? "" : '<i class="fa fa-'.$icon.'"></i>';
	    $bodyIdCode = (empty($body_id)) ? "" : 'id="'.$body_id.'"';
	    $boxStyleCode = empty($style) ? "" : "box-$style";
	    $footerDiv = empty($footer_content) ? "" : '<div class="box-footer">'.$footer_content.'</div>';

	    return '<div class="box '.$boxStyleCode.'">
					<div class="box-header">'.$iconItem.'
				        <h3 class="box-title">'.$header_title.'</h3>
				    </div>
					<div class="box-body '.$additional_body_classes.'" '.$bodyIdCode.'>'.$body_content.'</div>
					'.$footerDiv.'
				</div>';
    }

    public function collapsableBoxWithContent($header_title, $body_content, $footer_content = NULL, $icon = NULL, $style = CRM_UI_STYLE_DEFAULT, $body_id = NULL, $initiallyCollapsed = true) {
	   	// if icon is present, generate an icon item.
	    $iconItem = (empty($icon)) ? "" : '<i class="fa fa-'.$icon.'"></i>';
	    $bodyIdCode = (empty($body_id)) ? "" : 'id="'.$body_id.'"';
	    $boxStyleCode = empty($style) ? "" : " box-$style";
	    $collapsedCode = $initiallyCollapsed ? " collapsed-box" : "";
	    $footerDiv = empty($footer_content) ? "" : '<div class="box-footer">'.$footer_content.'</div>';

	    return '<div class="box'.$boxStyleCode.$collapsedCode.'">
					<div class="box-header">'.$iconItem.'
                        <div class="box-tools pull-right">
                            <button class="btn btn-sm" data-widget="collapse"><i class="fa fa-plus"></i></button>
                            <button class="btn btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
				        <h3 class="box-title">'.$header_title.'</h3>
				    </div>
					<div class="box-body" '.$bodyIdCode.'>'.$body_content.'</div>
					'.$footerDiv.'
				</div>';
    }

    public function responsibleTableBox($header_title, $table_content, $icon = NULL, $style = CRM_UI_STYLE_DEFAULT, $body_id = NULL) {
	    return $this->boxWithContent($header_title, $table_content, NULL, $icon, $style, $body_id, "table-responsive");
    }

    public function boxWithMessage($header_title, $message, $icon = NULL, $style = CRM_UI_STYLE_DEFAULT) {
	    $body_content = '<div class="callout callout-'.$style.'"><p>'.$message.'</p></div>';
	    return $this->boxWithContent($header_title, $body_content, NULL, $icon, $style);
    }

    public function boxWithForm($id, $header_title, $content, $submit_text = null, $style = CRM_UI_STYLE_DEFAULT, $messagetag = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG) {
	    if (empty($submit_text)) { $submit_text = $this->lh->translationFor("accept"); }
	    return '<div class="box box-default"><div class="box-header"><h3 class="box-title">'.$header_title.'</h3></div>
	    	   '.$this->formWithContent($id, $content, $submit_text, $style, $messagetag).'</div>';
    }

    public function boxWithQuote($title, $quote, $author, $icon = "quote-left", $style = CRM_UI_STYLE_DEFAULT, $body_id = null, $additional_body_classes = "") {
	    $body_content = '<blockquote><p>'.$quote.'</p>'.(empty($author) ? "" : '<small>'.$author.'</small>').'</blockquote>';
	    return $this->boxWithContent($title, $body_content, null, $icon, $style, $body_id, $additional_body_classes);
    }

    public function infoBox($title, $subtitle, $url, $icon, $color, $color2) {
	    /*return '<div class="col-md-'.$colsize.'"><div class="info-box"><a href="'.$url.'"><span class="info-box-icon bg-'.$color.'"><i class="fa fa-'.$icon.'"></i></span></a>
	    	<div class="info-box-content"><span class="info-box-text">'.$title.'</span>
			<span class="info-box-number">'.$subtitle.'</span></div></div></div>';
		*/
		return '<div class="col-lg-3 col-sm-6 animated fadeInUpShort">
				<a href="'.$url.'">
					<div class="panel widget bg-'.$color.'"  style="height: 87px;">
		    			<div class="col-xs-4 text-center bg-'.$color2.' pv-lg">
							<div class="h2 mt0 animated fadeInUpShort">
								<i class="fa fa-'.$icon.'" style="padding: 15px;"></i></span>
							</div>
						</div>
						<div class="col-xs-8 pv-lg">
							<div class="h2" style="margin-top: 15px;">
								<span class="info-box-text">'.$title.'</span>
								<span class="info-box-number">'.$subtitle.'</span>
							</div>
						</div>
					</div>
				</a>
				</div>';
    }

    public function boxWithSpinner($header_title, $body_content, $footer_content = NULL, $icon = NULL, $overlayId = "loading-overlay") {
	    $footerDiv = empty($footer_content) ? "" : '<div class="box-footer">'.$footer_content.'</div>';
	    $iconItem = (empty($icon)) ? "" : '<i class="fa fa-'.$icon.'"></i>';
	    return '<div class="box box-primary">
			<div class="box-header">'.$iconItem.'
		        <h3 class="box-title">'.$header_title.'</h3>
		    </div>
			<div class="box-body">'.$body_content.'</div>
			'.$footerDiv.'
			'.$this->spinnerOverlay($overlayId).'
		</div>';
    }


    public function spinnerOverlay($overlayId = "loading-overlay") {
	    return '<div id="'.$overlayId.'" class="overlay"><i class="fa fa-spinner fa-spin"></i></div>';
	}

    /** Tables */

    public function generateTableHeaderWithItems($items, $id, $styles = "", $needsTranslation = true, $hideHeading = false, $hideOnMedium = array(), $hideOnLow = array(), $idTbody) {
		$theadStyle = $hideHeading ? 'style="display: none!important;"' : '';
	    $table = "<table id=\"$id\" class=\"table $styles\" width=\"100%\"><thead $theadStyle><tr>";
	    if (is_array($items)) {
			foreach ($items as $item) {
		    	if ($item == "CONCAT_WS(' ', first_name, middle_initial, last_name)") {
		    		$item = "Name";
		    	}
			    // class modifiers for hiding classes in medium or low resolutions.
			    $classModifiers = "class=\"";
			    if (in_array($item, $hideOnMedium)) { $classModifiers .= " hide-on-medium "; }
			    if (in_array($item, $hideOnLow)) { $classModifiers .= " hide-on-low "; }
			    $classModifiers .= "\"";
			    // build header item
			    $table .= "<th $classModifiers>".($needsTranslation ? $this->lh->translationFor($item) : $item)."</th>";
		    }
	    }
		$table .= "</tr></thead><tbody id=\"$idTbody\">";
		return $table;
    }

    public function generateTableHeaderWithItemsResponsive($items, $id, $needsTranslation = true, $hideHeading = false) {
		$theadStyle = $hideHeading ? 'style="display: none!important;"' : '';
	    $table = "<table id=\"$id\" class=\"display responsive no-wrap\" width=\"100%\"><thead $theadStyle><tr>";
	    if (is_array($items)) {
			foreach ($items as $item) {
		    	if ($item == "CONCAT_WS(' ', first_name, middle_initial, last_name)") {
		    		$item = "Name";
		    	}
			    // build header item
			    $table .= "<th>".($needsTranslation ? $this->lh->translationFor($item) : $item)."</th>";
		    }
	    }
		$table .= "</tr></thead><tbody>";
		return $table;
    }
    
    public function generateTableFooterWithItems($items, $needsTranslation = true, $hideHeading = false, $hideOnMedium = array(), $hideOnLow = array()) {
	    $hideOnMedium = array("email", "Phone");
		$hideOnLow = array("email", "Phone");
		$theadStyle = $hideHeading ? 'style="display: none!important;"' : '';
	    $table = "</tbody><tfoot $theadStyle><tr>";
	    if (is_array($items)) {
		    foreach ($items as $item) {
			    // class modifiers for hiding classes in medium or low resolutions.
			    $classModifiers = "class=\"";
			    if (in_array($item, $hideOnMedium)) { $classModifiers .= " hide-on-medium "; }
			    if (in_array($item, $hideOnLow)) { $classModifiers .= " hide-on-low "; }
			    $classModifiers .= "\"";
				// build footer item
			    $table .= "<th $classModifiers>".($needsTranslation ? $this->lh->translationFor($item) : $item)."</th>";
		    }
	    }
		$table .= "</tr></tfoot></table>";
		return $table;
	}

    /** Style and color */

    /**

    /**

    /**

    /**
	 * Returns the array of creamy colors as an associative arrays.
	 * Keys are creamy tags (which can be used for css text-<color>)
	 * Values are their #rrggbb representation.
	 */
    public function creamyColors() {
	    return array(
		    "aqua" => "#00c0ef",
		    "blue" => "#0073b7",
		    "light-blue" => "#3c8dbc",
		    "teal" => "#39cccc",
		    "yellow" => "#f39c12",
		    "orange" => "#ff851b",
		    "green" => "#00a65a",
		    "lime" => "#01ff70",
		    "red" => "#dd4b39",
		    "purple" => "#605ca8",
		    "fuchsia" => "#f012be",
		    "navy" => "#001f3f",
		    "muted" => "#777"
	    );
    }

    /**
	 * Returns the rgb hex value (including #) string for the given creamy color.
	 * If $color is not found, returns CRM_UI_COLOR_DEFAULT_HEX.
	 */
    public function hexValueForCreamyColor($color) {
		$colors = $this->creamyColors();
		if (array_key_exists($color, $colors)) { return $colors[$color]; }
		else return CRM_UI_COLOR_DEFAULT_HEX;
    }

    /**
	 * Returns the creamy color for an hex value,
	 * or CRM_UI_COLOR_DEFAULT_NAME if the hex code doesn't translate
	 */
    public function creamyColorForHexValue($color) {
		$colors = $this->creamyColors();
		foreach ($colors as $creamy => $hex) { if ($hex == $color) return $creamy; }
		return CRM_UI_COLOR_DEFAULT_NAME;
    }

	/**
	 * Returns a random UI style to use for a notification, button, background element or such.
	 */
	public function getRandomUIStyle() {
		$number = rand(1,5);
		if ($number == 1) return CRM_UI_STYLE_INFO;
		else if ($number == 2) return CRM_UI_STYLE_DANGER;
		else if ($number == 3) return CRM_UI_STYLE_WARNING;
		else if ($number == 4) return CRM_UI_STYLE_SUCCESS;
		else return CRM_UI_STYLE_DEFAULT;
	}

    /** Messages */

    public function dismissableAlertWithMessage($message, $success, $includeResultData = false) {
	    $icon = $success ? "check" : "ban";
	    $color = $success ? "success" : "danger";
	    $title = $success ? $this->lh->translationFor("success") : $this->lh->translationFor("error");
	    $plusData = $includeResultData ? "'+ data+'" : "";
	    return '<div class="alert alert-dismissable alert-'.$color.'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="fa fa-'.$icon.'"></i> '.$title.'</h4><p>'.$message.' '.$plusData.'</p></div>';
    }

    public function emptyMessageDivWithTag($tagname) {
	    return '<div  id="'.$tagname.'" name="'.$tagname.'" style="display:none"></div>';
    }

    /**
	 * Generates a generic callout message with the given title, message and style.
	 * @param title String the title of the callout message.
	 * @param message String the message to show.
	 * @param style String a string containing the style (danger, success, primary...) or NULL if no style.
	 */
	public function calloutMessageWithTitle($title, $message, $style = NULL) {
		$styleCode = empty($style) ? "" : "callout-$style";
		return "<div class=\"callout $styleCode\"><h4>$title</h4><p>$message</p></div>";
	}

	/**
	 * Generates a generic message HTML box, with the given message.
	 * @param message String the message to show.
	 */
	public function calloutInfoMessage($message) {
		return $this->calloutMessageWithTitle($this->lh->translationFor("message"), $message, "info");
	}

	/**
	 * Generates a generic calls HTML box, with the given calls.
	 * @param calls String the calls to show.
	 */
	public function calloutInfoCall($call) {
		return $this->calloutMessageWithTitle("Call Logs", $call, "info");
	}

	/**
	 * Generates a warning message HTML box, with the given message.
	 * @param message String the message to show.
	 */
	public function calloutWarningMessage($message) {
		return $this->calloutMessageWithTitle($this->lh->translationFor("warning"), $message, "warning");
	}

	/**
	 * Generates a error message HTML box, with the given message.
	 * @param message String the message to show.
	 */
	public function calloutErrorMessage($message) {
		return $this->calloutMessageWithTitle($this->lh->translationFor("error"), $message, "danger");
	}

	/**
	 * Generates a error modal message HTML dialog, with the given message.
	 * @param message String the message to show.
	 */
	public function modalErrorMessage($message, $header) {
		$result = '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                <h4 class="modal-title"><i class="fa fa-envelope-o"></i> '.$header.'</h4>
		            </div><div class="modal-body">';
		$result = $result.$this->calloutErrorMessage($message);
		$result = $result.'</div><div class="modal-footer clearfix"><button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> '.
		$this->lh->translationFor("exit").'</button></div></div></div>';
		return $result;
	}

	/** Forms */

	public function formWithContent($id, $content, $submit_text = null, $submitStyle = CRM_UI_STYLE_DEFAULT, $messagetag = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG, $action = "") {
		if (empty($submit_text)) { $submit_text = $this->lh->translationFor("send"); }
		$button = '<button type="submit" class="btn btn-'.$submitStyle.'">'.$submit_text.'</button>';
		return $this->formWithCustomFooterButtons($id, $content, $button, $messagetag, $action);
	}

	public function formForCustomHook($id, $modulename, $hookname, $content, $submit_text = null, $messagetag = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG, $action = "") {
		$hiddenFields = $this->hiddenFormField("module_name", $modulename).$this->hiddenFormField("hook_name", $hookname);
		return $this->formWithContent($id, $hiddenFields.$content, $submit_text, CRM_UI_STYLE_DEFAULT, $messagetag, $action);
	}

	public function modalFormStructure($modalid, $formid, $title, $subtitle, $body, $footer, $icon = null, $messagetag = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG, $divClass) {
		$iconCode = empty($icon) ? '' : '<i class="fa fa-'.$icon.'"></i> ';
		$subtitleCode = empty($subtitle) ? '' : '<p>'.$subtitle.'</p>';

		return '<div class="modal fade" id="'.$modalid.'" name="'.$modalid.'" tabindex="-1" role="dialog" aria-hidden="true">
	        	<div class="modal-dialog '.$divClass.'"><div class="modal-content">
	                <div class="modal-header">
	                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                    <h4 class="modal-title">'.$iconCode.$title.'</h4>
	                    '.$subtitleCode.'
	                </div>
	                <form action="" method="post" class="form-horizontal" name="'.$formid.'" id="'.$formid.'">
	                    <div class="modal-body">
	                        '.$body.'
	                        <div id="'.$messagetag.'" style="display: none;"></div>
	                    </div>
	                    <div class="modal-footer clearfix">
							'.$footer.'
	                    </div>
	                </form>
				</div></div>
				</div>';
	}
	
	public function modalFormStructureAgentLog($modalid, $title, $subtitle, $body, $footer, $icon = null, $messagetag = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG) {
		$iconCode = empty($icon) ? '' : '<i class="fa fa-'.$icon.'"></i> ';
		$subtitleCode = empty($subtitle) ? '' : '<p>'.$subtitle.'</p>';

		return '<div class="modal fade" id="'.$modalid.'" name="'.$modalid.'" tabindex="-1" role="dialog" aria-hidden="true">
	        	<div class="modal-dialog modal-lg"><div class="modal-content">
	                <div class="modal-header">
					<h4 class="modal-title animated bounceInRight">
						<div class="col-sm-12 col-md-8">
							'.$iconCode.$title.' 
							<b>'.$subtitleCode.'</b>
						</div>
						<div class="col-sm-12 col-md-4 row" id="daterange-'.$title.'">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="input-group date">
										<input date-range-picker id="daterange_input-'.$title.'" name="date_agentlog" class="form-control date-picker" type="text" ng-model="dateRange" clearable="true" options="dateRangeOptions" />
										<span class="input-group-addon">
											<span class="fa fa-calendar"></span>
										</span>
										<input type="hidden" id="user_agentlog">
									</div>
								</div>
							</div>
						</div>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</h4>
	                </div>
					<div class="modal-body">
						'.$body.'
						<div id="'.$messagetag.'" style="display: none;"></div>
					</div>
					<div class="modal-footer clearfix">
						'.$footer.'
					</div>
				</div></div>
				</div>';
	}

	public function formWithCustomFooterButtons($id, $content, $footer, $messagetag = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG, $action = "") {
		return '<form role="form" id="'.$id.'" name="'.$id.'" method="post" action="'.$action.'" enctype="multipart/form-data">
            <div class="box-body">
            	'.$content.'
            </div>
            <div class="box-footer" id="form-footer">
            	'.$this->emptyMessageDivWithTag($messagetag).'
				'.$footer.'
            </div>
        </form>';
	}

    public function checkboxInputWithLabel($label, $id, $name, $enabled) {
	    return '<div class="checkbox"><label for="'.$id.'" class="checkbox-inline c-checkbox">
	    <input type="checkbox" id="'.$id.'" name="'.$name.'"'.($enabled ? "checked": "").'/><span class="fa fa-check"></span> '.$label.'</label></div>';
    }

    public function radioButtonInputGroup($name, $values, $labels, $ids = null, $checkedIndex = 0) {
	    $result = '<div class="form-group">';
	    $i = 0;
	    foreach ($values as $value) {
		    $idCode = ((isset($ids)) && (isset($ids[$i]))) ? 'id="'.$ids[$i].'"' : '';
		    $checkedCode = ($checkedIndex == $i) ? "checked" : "";
		    $result .= '<div class="radio"><label><input type="radio" name="'.$name.'" '.$idCode.' value="'.$value.'" '.$checkedCode.'>'.$labels[$i].'</label></div>';
			$i++;
	    }
	    $result .= '</div>';
	    return $result;
    }

    public function singleFormGroupWithSelect($label, $id, $name, $options, $selectedOption, $needsTranslation = false, $labelClass, $divClass, $selectClass) {
	    $labelCode = empty($label) ? '<div class="'.$divClass.'">' : '<label class="control-label '.$labelClass.'">'.$label.'</label><div class="'.$divClass.'">';
	    $selectCode = '<div class="form-group">'.$labelCode.'<select id="'.$id.'" name="'.$name.'" class="form-control '.$selectClass.'">';
	    foreach ($options as $key => $value) {
		    $isSelected = ($selectedOption == $key) ? " selected" : "";
		    $selectCode .= '<option value="'.$key.'" '.$isSelected.'>'.($needsTranslation ? $this->lh->translationFor($value) : $value).'</option>';
	    }
		$selectCode .= '</select></div></div>';
		return $selectCode;
    }
    
    public function singleFormGroupWithSelectHiddenInput($label, $id, $name, $options, $selectedOption, $needsTranslation = false, $labelClass, $divClass, $selectClass, $hiddeninput) {
	    $labelCode = empty($label) ? '<div class="'.$divClass.'">' : '<label class="control-label '.$labelClass.'">'.$label.'</label><div class="'.$divClass.'">';
	    if (!empty($hiddeninput)) {
			//foreach ($hiddeninput as $values) {
				$inputid = $hiddeninput["id"];
				$inputname = $hiddeninput["name"];
				$inputvalue = $hiddeninput["value"];
			//}
			$hiddenInputCode = '<input type="hidden" id="'.$inputid.'" name="'.$inputname.'" value="'.$inputvalue.'">';	    
	    } else {
			$hiddenInputCode = "";
	    }
	    $selectCode = '<div class="form-group">'.$labelCode.''.$hiddenInputCode.'<select id="'.$id.'" name="'.$name.'" class="form-control '.$selectClass.'">';
	    foreach ($options as $key => $value) {
		    $isSelected = ($selectedOption == $key) ? " selected" : "";
		    $selectCode .= '<option value="'.$key.'" '.$isSelected.'>'.($needsTranslation ? $this->lh->translationFor($value) : $value).'</option>';
	    }
		$selectCode .= '</select></div></div>';
		return $selectCode;
    }    

    public function singleFormGroupWithSelectInputGroup($label, $id, $name, $options, $selectedOption, $needsTranslation = false) {
	    $labelCode = empty($label) ? "" : '<label>'.$label.'</label>';
	    $selectCode = '<div class="form-group">'.$labelCode.'<div class="input-group"><select id="'.$id.'" name="'.$name.'" class="form-control">';
	    foreach ($options as $key => $value) {
		    $isSelected = ($selectedOption == $value) ? " selected" : "";
		    $selectCode .= '<option value="'.$value.'" '.$isSelected.'>'.($needsTranslation ? $this->lh->translationFor($value) : $value).'</option>';
	    }
		$selectCode .= '</select></div></div>';
		return $selectCode;
    }

    public function singleFormInputElement($id, $name, $type, $placeholder = "", $value = null, $icon = null, $required = false, $disabled = false, $readonly = false, $inputClass = "", $divClass = "") {
	    $iconCode = empty($icon) ? '' : '<span class="input-group-addon"><i class="fa fa-'.$icon.'"></i></span>';
	    $valueCode = empty($value) ? 'value=""' : ' value="'.$value.'"';
	    $requiredCode = $required ? "required" : "";
	    $disabledCode = $disabled ? "disabled" : "";
	    $readonlyCode = $readonly ? "readonly" : "";
	    return $iconCode.'<div class="'.$divClass.'"><input name="'.$name.'" id="'.$id.'" type="'.$type.'" class="form-control '.$inputClass.'" placeholder="'.$placeholder.'"'.$valueCode.' '.$requiredCode.' '.$disabledCode.' '.$readonlyCode.'></div>';
    }

    public function singleFormTextareaElement($id, $name, $placeholder = "", $text = "", $icon = null) {
	    $iconCode = empty($icon) ? '' : '<span class="input-group-addon"><i class="fa fa-'.$icon.'"></i></span>';
	    return $iconCode.'<textarea id="'.$id.'" name="'.$name.'" placeholder="'.$placeholder.'" class="form-control">'.$text.'</textarea>';
    }

    public function singleFormGroupWithFileUpload($id, $name, $currentFilePreview, $label, $bottomText) {
	    $labelCode = isset($label) ? '<label for="'.$id.'">'.$label.'</label>' : '';
	    return '<div class="form-group">'.$labelCode.'<br>'.$currentFilePreview.'<br><input type="file" id="'.$id.'" name="'.$id.'">
	                <p class="help-block">'.$bottomText.'</p></div>';
    }

	public function maskedDateInputElement($id, $name, $dateFormat = "dd/mm/yyyy", $value = null, $icon = null, $includeJS = false) {
		// date value
		$dateAsDMY = "";
        if (isset($value)) {
            $time = strtotime($value);
            $phpFormat = str_replace("dd", "d", $dateFormat);
            $phpFormat = str_replace("mm", "m", $phpFormat);
            $phpFormat = str_replace("yyyy", "Y", $phpFormat);
            $dateAsDMY = date($phpFormat, $time);
        }
        // icon and label
		$iconCode = empty($icon) ? '' : '<span class="input-group-addon"><i class="fa fa-'.$icon.'"></i></span>';

		// bild html code
		$htmlCode = '<input name="'.$name.'" id="'.$id.'" type="text" class="form-control" data-inputmask="\'alias\': \''.$dateFormat.'\'" data-mask value="'.$dateAsDMY.'" placeholder="'.$dateFormat.'"/>';
		// build JS code to turn an input text into a dateformat.
		$jsCode = "";
		if ($includeJS === true) {
			$jsCode = '<script src="js/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
		    <script src="js/plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
		    <script src="js/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>';
		}
		$jsCode .= $this->wrapOnDocumentReadyJS('$("#'.$id.'").inputmask("'.$dateFormat.'", {"placeholder": "'.$dateFormat.'"});');

		return $iconCode.$htmlCode."\n".$jsCode;
	}

	public function hiddenFormField($id, $value = "", $name = "") {
		$name = (empty($name)) ? $id : $name;
		return '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'">';
	}

	public function singleInputGroupWithContent($content) {
		return '<div class="input-group">'.$content.'</div>';
	}

	public function singleFormGroupWrapper($content, $label = null, $labelClass) {
		$labelCode = isset($label) ? '<label class="control-label '.$labelClass.'">'.$label.'</label>' : '';
		return '<div class="form-group">'.$labelCode.$content.'</div>';
	}

    public function singleFormGroupWithInputGroup($inputGroup, $label = null) {
	    $labelCode = isset($label) ? "<label>$label</label>" : "";
	    return '<div class="form-group">'.$labelCode.'<div class="input-group">'.$inputGroup.'</div></div>';
    }

    public function modalDismissButton($id, $message = null, $position = "right", $dismiss = true) {
	    if (empty($message)) { $message = $this->lh->translationFor("cancel"); }
	    $dismissCode = $dismiss ? 'data-dismiss="modal"' : '';
	    return '<button type="button" class="btn btn-danger pull-'.$position.'" '.$dismissCode.' id="'.$id.'">
	    		<i class="fa fa-times"></i> '.$message.'</button>';
    }

    public function modalSubmitButton($id, $message = null, $position = "left", $dismiss = false) {
	    if (empty($message)) { $message = $this->lh->translationFor("accept"); }
	    $dismissCode = $dismiss ? 'data-dismiss="modal"' : '';
	    return '<button type="submit" class="btn btn-primary pull-'.$position.'" '.$dismissCode.' id="'.$id.'"><i class="fa fa-check-circle"></i> '.$message.'</button>';
    }

    /** Global buttons */

    public function buttonWithLink($id, $link, $title, $type = "button", $icon = null, $style = CRM_UI_STYLE_DEFAULT, $additionalClasses = null, $additionalAttr = null) {
	    $iconCode = isset($icon) ? '<i class="fa fa-'.$icon.'"></i>' : '';
	    return '<button type="'.$type.'" class="btn btn-'.$style.' '.$additionalClasses.'" id="'.$id.'" href="'.$link.'" '.$additionalAttr.'>'.$iconCode.' '.$title.'</button>';
    }

    /** Task list buttons */

    /**
	 * Creates a hover action button to be put in a a task list, todo-list or similar.
	 * If modaltarget is specified, the button will open a custom dialog with the given id.
	 * @param String $classname name for the class.
	 * @param Array $parameters An associative array of parameters to include (i.e: "user_id" => "1231").
	 * @param String icon the font-awesome identifier for the icon.
	 * @param String modaltarget if specified, id of the destination modal dialog to open.
	 * @param String $linkClasses Additional classes for the HTML a link
	 * @param String $iconClasses Additional classes for the font awesome icon i.
	 */
    public function hoverActionButton($classname, $icon, $hrefValue = "", $modaltarget = null, $linkClasses = "", $iconClasses = "", $otherParameters = null) {
	    // build parameters and additional code
	    $paramCode = "";
	    if (isset($otherParameters)) foreach ($otherParameters as $key => $value) { $paramCode = "$key=\"$value\" "; }
	    $modalCode = isset($modaltarget) ? "data-toggle=\"modal\" data-target=\"#$modaltarget\"" : "";
	    // return the button action link
	    return '<a class="'.$classname.' '.$linkClasses.'" href="'.$hrefValue.'" '.$paramCode.' '.$modalCode.'>
	    		<i class="fa fa-'.$icon.' '.$iconClasses.'"></i></a>';
    }

    /** Pop-Up Action buttons */

    public function popupActionButton($title, $options, $style = CRM_UI_STYLE_DEFAULT) {
	    // style code
	    if (is_string($style)) { $styleCode = "btn btn-$style"; }
	    else if (is_array($style)) {
		    $styleCode = "btn";
		    foreach ($style as $class) { $styleCode .= " btn-$class"; }
	    } else { $styleCode = "btn btn-default"; }
	    // popup prefix code
	    $popup = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$title.'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button><ul class="dropdown-menu" role="menu">';
	    // options
	    foreach ($options as $option) { $popup .= $option; }
	    // popup suffix code.
	    $popup .= '</ul></div>';
	    return $popup;
    }

    public function actionForPopupButtonWithClass($class, $text, $parameter_value, $parameter_name = "href") {
	    return '<li><a class="'.$class.'" '.$parameter_name.'="'.$parameter_value.'">'.$text.'</a></li>';
    }

    public function actionForPopupButtonWithLink($url, $text, $class = null, $parameter_value = null, $parameter_name = null) {
	    // do we need to specify a class?
	    if (isset($class)) { $classCode = 'class="'.$class.'"'; } else { $classCode = ""; }
	    // do we have an optional parameter?
	    if (isset($parameter_value) && isset($parameter_name)) { $parameterCode = $parameter_name.'="'.$parameter_value.'"'; }
	    else { $parameterCode = ""; }
	    return '<li><a '.$classCode.' href="'.$url.'" '.$parameterCode.'>'.$text.'</a></li>';
    }

    public function actionForPopupButtonWithOnClickCode($text, $jsFunction, $parameters = null, $class = null) {
	    // do we need to specify a class?
	    if (isset($class)) { $classCode = 'class="'.$class.'"'; } else { $classCode = ""; }
	    // do we have an parameters?
	    if (isset($parameters) && is_array($parameters)) {
		    $parameterCode = "";
		    foreach ($parameters as $parameter) { $parameterCode .= "'$parameter',"; }
		    $parameterCode = rtrim($parameterCode, ",");
		} else { $parameterCode = ""; }
	    return '<li><a href="#" '.$classCode.' onclick="'.$jsFunction.'('.$parameterCode.');">'.$text.'</a></li>';
    }

    public function separatorForPopupButton() {
	    return '<li class="divider"></li>';
    }

    public function simpleLinkButton($id, $title, $url, $icon = null, $style = CRM_UI_STYLE_DEFAULT, $additionalClasses = null) {
	    // style code.
	    $styleCode = "";
	    if (!empty($style)) {
		    if (is_array($style)) { foreach ($style as $st) { $styleCode .= "btn-$style "; } }
		    else if (is_string($style)) { $styleCode = "btn-$style"; }
	    }
	    return '<a id="'.$id.'" class="btn '.$styleCode.'" href="'.$url.'">'.$title.'</a>';
    }

    /** Images */

    public function imageWithData($src, $class, $extraParams, $alt = "") {
	    $paramsCode = "";
	    if (is_array($extraParams) && count($extraParams) > 0) {
		    foreach ($extraParams as $key => $value) { $paramsCode .= " $key=\"$value\""; }
		}
	    return "<img src=\"$src\" class=\"$class\" $paramsCode alt=\"$alt\"/>";
    }

    /** Paragraphs */

    public function simpleParagraphWithText($text, $additionalClasses = "") {
	    return "<p class='$additionalClasses'>$text</p>";
    }

    /** Javascript HTML code generation */

    public function wrapOnDocumentReadyJS($content) {
	    return '<script type="text/javascript">$(document).ready(function() {
		    '.$content.'
		    });</script>';
    }

    public function formPostJS($formid, $phpfile, $successJS, $failureJS, $preambleJS = "", $successResult=CRM_DEFAULT_SUCCESS_RESPONSE) {
	    return $this->wrapOnDocumentReadyJS('$("#'.$formid.'").validate({
			submitHandler: function(e) {
				'.$preambleJS.'
				$.post("'.$phpfile.'", $("#'.$formid.'").serialize(), function(data) {
					if (data == "'.$successResult.'") {
						'.$successJS.'
					} else {
						'.$failureJS.'
					}
				}).fail(function() {
					'.$failureJS.'
  				});
			}
		 });');
    }

    public function fadingInMessageJS($message, $tagname = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG) {
	    return '$("#'.$tagname.'").html(\''.$message.'\');
				$("#'.$tagname.'").fadeIn();';
    }

    public function fadingOutMessageJS($animated = false, $tagname = CRM_UI_DEFAULT_RESULT_MESSAGE_TAG) {
	    if ($animated) { return '$("#'.$tagname.'").fadeOut();'; }
	    else { return '$("#'.$tagname.'").hide();'; }
    }

    public function reloadLocationJS() { return 'location.reload();'; }

    public function newLocationJS($url) { return 'window.location.href = "'.$url.'";'; }

    public function showRetrievedErrorMessageAlertJS() { return 'alert(data);'; }

    public function showCustomErrorMessageAlertJS($msg) { return 'alert("'.$msg.'");'; }

    public function clickableClassActionJS($className, $parameter, $container, $phpfile, $successJS, $failureJS, $confirmation = false, $successResult = CRM_DEFAULT_SUCCESS_RESPONSE, $additionalParameters = null, $parentContainer = null) {
	    // build the confirmation code if needed.
	    $confirmPrefix = $confirmation ? 'var r = confirm("'.$this->lh->translationFor("are_you_sure").'"); if (r == true) {' : '';
	    $confirmSuffix = $confirmation ? '}' : '';
	    $paramCode = empty($parentContainer) ? 'var paramValue = $(this).attr("'.$container.'");' :
	    			'var ele = $(this).parents("'.$parentContainer.'").first(); var paramValue = ele.attr("'.$container.'");';
	    // additional parameters
	    $additionalString = "";
	    if (is_array($additionalParameters) && count($additionalParameters) > 0) {
		    foreach ($additionalParameters as $apKey => $apValue) { $additionalString .= ", \"$apKey\": $apValue ";  }
	    }

	    // return the JS code
	    return $this->wrapOnDocumentReadyJS(
	    '$(".'.$className.'").click(function(e) {
			e.preventDefault();
			'.$confirmPrefix.'
				'.$paramCode.'
				$.post("'.$phpfile.'", { "'.$parameter.'": paramValue '.$additionalString.'} ,function(data) {
					if (data == "'.$successResult.'") { '.$successJS.' }
					else { '.$failureJS.' }
				}).fail(function() {
					'.$failureJS.'
  				});
			'.$confirmSuffix.'
		 });');
    }

    /**
	 * Creates a javascript javascript "reload with message" code, that will
	 * reload the current page passing a custom message tag.
	 */
	public function reloadWithMessageFunctionJS($messageVarName = "message") {
		return 'function reloadWithMessage(message) {
		    var url = window.location.href;
			if (url.indexOf("?") > -1) { url += "&'.$messageVarName.'="+message;
			} else{ url += "?'.$messageVarName.'="+message; }
			window.location.href = url;
		}'."\n";
	}

	/**
	 * Generates an javascript calling to ReloadWithMessage function, generated
	 * by reloadWithMessageFunctionJS() to reload the custom page sending a
	 * custom message parameter.
	 * Note: Message is not quoted inside call, you must do it yourself.
	 */
	public function reloadWithMessageCallJS($message) { return 'reloadWithMessage('.$message.');'; }

    /**
	 * This function generates the javascript for the messages mailbox actions.
	 * You must pass a class name for the button that triggers the action, a php
	 * url for the Ajax request, a completion resultJS code and, optionally, if
	 * you want to discern the failure from the success, a failureJS.
	 * The function does the following assumptions:
	 * - The result message div for showing the results has id messages-message-box
	 * - The parameters to send to any function invoked by the php ajax script are
	 *   messageids (containing a comma separated string array of message ids to act upon)
	 *   and folder (containing the current folder identifier).
	 * @param String $classname the name of the mailbox action class.
	 * @param String $url the URL for the PHP that will receive the Ajax POST request.
	 * @param String $resultJS The default javascript to execute (or the successful one if
	 *        failureJS is also specified).
	 * @param String $failureJS The failure javascript to execute, if left null, only the
	 *        resultJS will be applied, without taking into account the result data.
	 * @param Array $customParameters Associative array with custom parameters to add to the request.
	 * @param Bool $confirmation If true, confirmation will be asked before applying the action.
	 * @param Bool $checkSelectedMessages if true, no action will be taken if no messages are selected.
	 */
	public function mailboxAction($classname, $url, $resultJS, $failureJS = null, $customParameters = null, $confirmation = false,
								  $checkSelectedMessages = true) {
		// check selected messages count?
		$checkSelectedMessagesCode = $checkSelectedMessages ? 'if (selectedMessages.length < 1) { return; }' : '';
		// needs confirmation?
		$confirmPrefix = $confirmation ? 'var r = confirm("'.$this->lh->translationFor("are_you_sure").'"); if (r == true) {' : '';
		$confirmSuffix = $confirmation ? '}' : '';
		// success+failure or just result ?
		if (empty($failureJS)) { $content = $resultJS; }
		else { $content = 'if (data == "'.CRM_DEFAULT_SUCCESS_RESPONSE.'") { '.$resultJS.' } else { '.$failureJS.' }'; }
		// custom parameters
		$paramCode = "";
		if (is_array($customParameters) && count($customParameters)) {
			foreach ($customParameters as $key => $value) { $paramCode .= ", \"$key\": \"$value\" "; }
		}

		$result = '$("button.'.$classname.'").click(function (e) {
				    '.$checkSelectedMessagesCode.'
					e.preventDefault();
					'.$confirmPrefix.'
					$("#messages-message-box").hide();
					$.post("'.$url.'", { "messageids": selectedMessages, "folder": folder '.$paramCode.'}, function(data) {
						'.$content.'
					});
					'.$confirmSuffix.'
			    });';
		return $result;
	}

    // Assignment to variables from one place to a form destination.

    private function javascriptVarFromName($name, $prefix = "var") {
	    $result = str_replace("-", "", $prefix.$name);
	    $result = str_replace("_", "", $result);
	    return trim($result);
    }

    public function selfValueAssignmentJS($attr, $destination) {
	    $varName = $this->javascriptVarFromName($destination);
	    return 'var '.$varName.' = $(this).attr("'.$attr.'");
	    		$("#'.$destination.'").val('.$varName.');';
    }

    public function directValueAssignmentJS($source, $attr, $destination) {
	    $varName = $this->javascriptVarFromName($destination);
	    return 'var '.$varName.' = $("#'.$source.'").attr("'.$attr.'");
	    		$("#'.$destination.'").val('.$varName.');';
    }

    public function classValueFromParentAssignmentJS($classname, $parentContainer, $destination) {
	    $elementName = $this->javascriptVarFromName($destination, "ele");
	    $varName = $this->javascriptVarFromName($destination);
	    return 'var '.$elementName.' = $(this).parents("'.$parentContainer.'").first();
				var '.$varName.' = $(".'.$classname.'", '.$elementName.');
				$("#'.$destination.'").val('.$varName.'.text().trim());';
    }

    public function clickableFillValuesActionJS($classname, $assignments) {
	    $js = '$(".'.$classname.'").click(function(e) {'."\n".'e.preventDefault();';
		foreach ($assignments as $assignment) { $js .= "\n".$assignment; }
		$js .= '});'."\n";
		return $this->wrapOnDocumentReadyJS($js);
    }

    /** Hooks */

    /**
	 * Returns the hooks for the dashboard.
	 */
    public function hooksForDashboard() {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_DASHBOARD, null, CRM_MODULE_MERGING_STRATEGY_APPEND);
    }

	/**
	 * Generates the footer for the customer list screen, by invoking the different modules
	 * CRM_MODULE_HOOK_CUSTOMER_LIST_FOOTER hook.
	 */
	public function getCustomerListFooter($customer_type) {
		$mh = \creamy\ModuleHandler::getInstance();
		$footer = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_CUSTOMER_LIST_FOOTER, array(CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_TYPE => $customer_type), CRM_MODULE_MERGING_STRATEGY_APPEND);
		$js = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_CUSTOMER_LIST_ACTION, array(CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_TYPE => $customer_type), CRM_MODULE_MERGING_STRATEGY_APPEND);
		return $footer.$js;
	}

	/**
	 * Generates the footer for the messages list screen, by invoking the different modules
	 * CRM_MODULE_HOOK_MESSAGE_LIST_FOOTER & CRM_MODULE_HOOK_MESSAGE_LIST_ACTION hooks.
	 */
	public function getMessagesListActionJS($folder) {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_MESSAGE_LIST_ACTION, array(CRM_MODULE_HOOK_PARAMETER_MESSAGES_FOLDER => $folder), CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

	public function getComposeMessageFooter() {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_MESSAGE_COMPOSE_FOOTER, null, CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

	public function getComposeMessageActionJS() {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_MESSAGE_COMPOSE_ACTION, null, CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

	public function getMessageDetailFooter($messageid, $folder) {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_MESSAGE_DETAIL_FOOTER, array(CRM_MODULE_HOOK_PARAMETER_MESSAGE_ID => $messageid, CRM_MODULE_HOOK_PARAMETER_MESSAGES_FOLDER => $folder), CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

	public function getMessageDetailActionJS($messageid, $folder) {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_MESSAGE_DETAIL_ACTION, array(CRM_MODULE_HOOK_PARAMETER_MESSAGE_ID => $messageid, CRM_MODULE_HOOK_PARAMETER_MESSAGES_FOLDER => $folder), CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

    /**
	 * Returns the hooks for the customer detail/edition screen.
	 */
	public function customerDetailModuleHooks($customerid, $customerType) {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_CUSTOMER_DETAIL, array(CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_ID => $customerid, CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_TYPE => $customerType),
		 CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

    /* Administration & user management */

    /** Returns the HTML form for modyfing the system settings */
    public function getGeneralSettingsForm() {
		// current settings values
	    $baseURL = $this->db->getSettingValueForKey(CRM_SETTING_CRM_BASE_URL);
	    $tz = $this->db->getSettingValueForKey(CRM_SETTING_TIMEZONE);
	    $lo = $this->db->getSettingValueForKey(CRM_SETTING_LOCALE);
	    $ct = $this->db->getSettingValueForKey(CRM_SETTING_THEME);
	    if (empty($ct)) { $ct = CRM_SETTING_DEFAULT_THEME; }
	    $ce = $this->db->getSettingValueForKeyAsBooleanValue(CRM_SETTING_CONFIRMATION_EMAIL);
	    $cv = $this->db->getSettingValueForKeyAsBooleanValue(CRM_SETTING_EVENTS_EMAIL);
	    $cn = $this->db->getSettingValueForKey(CRM_SETTING_COMPANY_NAME);
	    $cl = $this->db->getSettingValueForKey(CRM_SETTING_COMPANY_LOGO);
	    $go = $this->db->getSettingValueForKey(CRM_SETTING_GOOGLE_API_KEY);
	    $slaveDB = $this->db->getSettingValueForKey(CRM_SETTING_SLAVE_DB_IP);
	    if (isset($cl)) { $cl = $this->imageWithData($cl, "", null); }
	    $tOpts = array("black" => "black", "blue" => "blue", "green" => "green", "minimalist" => "minimalist", "purple" => "purple", "red" => "red", "yellow" => "yellow");

	    // translation.
	    $em_text = $this->lh->translationFor("require_confirmation_email");
	    $ev_text = $this->lh->translationFor("send_event_email");
	    $es_text = $this->lh->translationFor("choose_theme");
	    $tz_text = $this->lh->translationFor("detected_timezone");
	    $lo_text = $this->lh->translationFor("choose_language");
	    $ok_text = $this->lh->translationFor("settings_successfully_changed");
	    $bu_text = $this->lh->translationFor("base_url");
	    $cn_text = $this->lh->translationFor("company_name");
	    $cl_text = $this->lh->translationFor("custom_company_logo");
	    $go_text = $this->lh->translationFor("google_api_key");
	    $db_text = $this->lh->translationFor("slave_db_ip");

	    // form
	    $form = '<form role="form" id="adminsettings" name="adminsettings" class="form" enctype="multipart/form-data">
			  '.$this->singleFormGroupWithInputGroup($this->singleFormInputElement("base_url", "base_url", "text", $bu_text, $baseURL, "globe"), $bu_text).'
	    	  <label>'.$this->lh->translationFor("messages").'</label>
			  '.$this->checkboxInputWithLabel($em_text, "confirmationEmail", "confirmationEmail", $ce).'
			  '.$this->checkboxInputWithLabel($ev_text, "eventEmail", "eventEmail", $cv).'
			  '.$this->singleFormGroupWithInputGroup($this->singleFormInputElement("company_name", "company_name", "text", $cn_text, $cn, "building-o"), $cn_text).'
			  '.$this->singleFormGroupWithFileUpload("company_logo", "company_logo", $cl, $cl_text, null).'
			  '.$this->singleFormGroupWithSelect($es_text, "theme", "theme", $tOpts, $ct, false).'
			  '.$this->singleFormGroupWithSelect($tz_text, "timezone", "timezone", \creamy\CRMUtils::getTimezonesAsArray(), $tz).'
			  '.$this->singleFormGroupWithSelect($lo_text, "locale", "locale", \creamy\LanguageHandler::getAvailableLanguages(), $lo).'
			  '.$this->singleFormGroupWithInputGroup($this->singleFormInputElement("google_api_key", "google_api_key", "text", $go_text, $go, "google"), $go_text).'
			  '.$this->singleFormGroupWithInputGroup($this->singleFormInputElement("slave_db_ip", "slave_db_ip", "text", $db_text, $slaveDB, "database"), $db_text).'
			  <div class="box-footer">
			  '.$this->emptyMessageDivWithTag(CRM_UI_DEFAULT_RESULT_MESSAGE_TAG).'
			  <button type="submit" class="btn btn-primary">'.$this->lh->translationFor("modify").'</button></div></form>';

		return $form;
    }

    /** Returns the HTML code for the input field associated with a module setting data type */
    public function inputFieldForModuleSettingOfType($setting, $type, $currentValue) {
	    if (is_array($type)) { // select type
		   return $this->singleFormGroupWithSelectInputGroup($this->lh->translationFor($setting), $setting, $setting, $type, $currentValue);
	    } else { // single input type: text, number, bool, date...
		   switch ($type) {
			   case CRM_SETTING_TYPE_STRING:
				   return $this->singleFormGroupWithInputGroup($this->singleFormInputElement($setting, $setting, "text", $this->lh->translationFor($setting), $currentValue), $this->lh->translationFor($setting));
					break;
				case CRM_SETTING_TYPE_INT:
				case CRM_SETTING_TYPE_FLOAT:
				    return $this->singleFormGroupWithInputGroup($this->singleFormInputElement($setting, $setting, "number", $this->lh->translationFor($setting), $currentValue), $this->lh->translationFor($setting));
					break;
				case CRM_SETTING_TYPE_BOOL:
					return $this->singleFormGroupWithInputGroup($this->checkboxInputWithLabel($this->lh->translationFor($setting), $setting, $setting, (bool) $currentValue));
					break;
				case CRM_SETTING_TYPE_DATE:
					$dateFormat = $this->lh->getDateFormatForCurrentLocale();
				   return $this->singleFormGroupWithInputGroup($this->maskedDateInputElement($setting, $setting, $dateFormat, $currentValue), $this->lh->translationFor($setting));
					break;
				case CRM_SETTING_TYPE_LABEL:
					return $this->singleFormGroupWithInputGroup($this->lh->translationFor($setting));
					break;
				case CRM_SETTING_TYPE_SELECT:
					return $this->singleFormGroupWithInputGroup($this->dropdownFormInputElement($setting[0], $setting[0], $setting[1], $currentValue), $this->lh->translationFor($setting[0]));
					break;
				case CRM_SETTING_TYPE_PASS:
					return $this->singleFormGroupWithInputGroup($this->singleFormInputElement($setting, $setting, "password", $this->lh->translationFor($setting), $currentValue), $this->lh->translationFor($setting));
					break;
		    }
	    }
    }


    /**
	 * Generates the HTML code for a select with the human friendly descriptive names for the user roles.
	 * @return String the HTML code for a select with the human friendly descriptive names for the user roles.
	 */
	public function getUserRolesAsFormSelect($selectedOption = CRM_DEFAULTS_USER_ROLE_MANAGER) {
		$selectedAdmin = $selectedOption == CRM_DEFAULTS_USER_ROLE_ADMIN ? " selected" : "";
		$selectedManager = $selectedOption == CRM_DEFAULTS_USER_ROLE_MANAGER ? " selected" : "";
		$selectedWriter = $selectedOption == CRM_DEFAULTS_USER_ROLE_WRITER ? " selected" : "";
		$selectedReader = $selectedOption == CRM_DEFAULTS_USER_ROLE_READER ? " selected" : "";
		$selectedGuest = $selectedOption == CRM_DEFAULTS_USER_ROLE_GUEST ? " selected" : "";

		$adminName = $this->lh->translationFor($this->getRoleNameForRole(CRM_DEFAULTS_USER_ROLE_ADMIN));
		$managerName = $this->lh->translationFor($this->getRoleNameForRole(CRM_DEFAULTS_USER_ROLE_MANAGER));
		$writerName = $this->lh->translationFor($this->getRoleNameForRole(CRM_DEFAULTS_USER_ROLE_WRITER));
		$readerName = $this->lh->translationFor($this->getRoleNameForRole(CRM_DEFAULTS_USER_ROLE_READER));
		$guestName = $this->lh->translationFor($this->getRoleNameForRole(CRM_DEFAULTS_USER_ROLE_GUEST));

		return '<select id="role" name="role">
				   <option value="'.CRM_DEFAULTS_USER_ROLE_ADMIN.'"'.$selectedAdmin.'>'.$adminName.'</option>
				   <option value="'.CRM_DEFAULTS_USER_ROLE_MANAGER.'"'.$selectedManager.'>'.$managerName.'</option>
				   <option value="'.CRM_DEFAULTS_USER_ROLE_WRITER.'"'.$selectedWriter.'>'.$writerName.'</option>
				   <option value="'.CRM_DEFAULTS_USER_ROLE_READER.'"'.$selectedReader.'>'.$readerName.'</option>
				   <option value="'.CRM_DEFAULTS_USER_ROLE_GUEST.'"'.$selectedGuest.'>'.$guestName.'</option>
			    </select>';
	}

    /**
     * Returns a HTML representation of the action associated with a user in the admin panel.
     * @param $userid Int the id of the user
     * @param $username String the name of the user
     * @param $status Int the status of the user (enabled=1, disabled=0)
     * @return String a HTML representation of the action associated with a user in the admin panel.
     */
    public function ActionMenuForContacts($lead_id) {
		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li><a class="edit-contact" data-id="'.$lead_id.'">'.$this->lh->translationFor("contact_details").'</a></li>
	                    <li class="divider"></li>
	                    <li><a class="delete-contact" data-id="'.$lead_id.'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div>';
	}
	private function getUserActionMenuForUser($userid, $username, $status) {
		$textForStatus = $status == "Y" ? $this->lh->translationFor("disable") : $this->lh->translationFor("enable");
		$actionForStatus = $status == "Y" ? "deactivate-user-action" : "activate-user-action";
		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li><a class="edit-action" href="'.$userid.'">'.$this->lh->translationFor("edit_data").'</a></li>
	                    <li><a class="change-password-action" href="'.$userid.'">'.$this->lh->translationFor("change_password").'</a></li>
	                    <li><a class="'.$actionForStatus.'" href="'.$userid.'">'.$textForStatus.'</a></li>
	                    <li class="divider"></li>
	                    <li><a class="delete-action" href="'.$userid.'">'.$this->lh->translationFor("delete_user").'</a></li>
	                </ul>
	            </div>';
	}
	//telephony menu for users
	private function getUserActionMenuForT_User($userid, $user, $role, $name, $current_user, $perm) {

		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li'.($perm->user_update === 'N' ? ' class="hidden"' : '').'><a class="edit-T_user" href="#" data-id="'.$userid.'" data-user="'.$current_user.'"  data-role="'.$role.'">'.$this->lh->translationFor("modify").'</a></li>
	                    <li'.($perm->user_view === 'N' ? ' class="hidden"' : '').'><a class="view-stats" href="#" data-user="'.$user.'" data-name="'.$name.'" data-agentlog="userlog">'.$this->lh->translationFor("agent_log").' - '.$this->lh->translationFor("agent_log").'</a></li>
						<li'.($perm->user_view === 'N' ? ' class="hidden"' : '').'><a class="view-stats" href="#" data-user="'.$user.'" data-name="'.$name.'" data-agentlog="outbound">'.$this->lh->translationFor("agent_log").' - '.$this->lh->translationFor("outbound").'</a></li>
						<li'.($perm->user_view === 'N' ? ' class="hidden"' : '').'><a class="view-stats" href="#" data-user="'.$user.'" data-name="'.$name.'" data-agentlog="inbound">'.$this->lh->translationFor("agent_log").' - '.$this->lh->translationFor("inbound").'</a></li>
	                    <li><a class="emergency-logout" href="#" data-emergency-logout-username="'.$user.'" data-name="'.$name.'">'.$this->lh->translationFor("emergency_logout").'</a></li>
	                    <li class="divider'.($perm->user_delete === 'N' ? ' hidden' : '').'"></li>
	                    <li'.(($perm->user_delete === 'N' || $user === $current_user) ? ' class="hidden"' : '').'><a class="delete-T_user" href="#" data-id="'.$userid.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div>';
			//<li><a class="info-T_user" href="'.$userid.'">'.$this->lh->translationFor("info").'</a></li>
	}
	//telephony menu for lists and call recordings
	public function getUserActionMenuForLists($listid, $listname, $perm) {

		   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.(($perm->list->list_update === 'N' || preg_match("/^(998|999)$/", $listid)) ? ' class="hidden"' : '').'><a class="edit-list" href="#" data-id="'.$listid.'" data-name="'.$listname.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.(($perm->customfields->customfields_create === 'N' || preg_match("/^(998|999)$/", $listid)) ? ' class="hidden"' : '').'><a class="copy-custom-fields" href="#" data-id="'.$listid.'" data-name="'.$listname.'">'.$this->lh->translationFor("copy_list_custom_fields").'</a></li>
			<li'.($perm->list->list_download === 'N' ? ' class="hidden"' : '').'><a class="download-list" href="#" data-id="'.$listid.'" data-name="'.$listname.'">'.$this->lh->translationFor("download").'</a></li>
			<li class="divider'.(($perm->list->list_delete === 'N' || preg_match("/^(998|999)$/", $listid)) ? ' hidden' : '').'"></li>
			<li'.(($perm->list->list_delete === 'N' || preg_match("/^(998|999)$/", $listid)) ? ' class="hidden"' : '').'><a class="delete-list" href="#" data-id="'.$listid.'" data-name="'.$listname.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
			//<li><a class="info-T_user" href="'.$userid.'">'.$this->lh->translationFor("info").'</a></li>
	}

	//telephony menu for INBOUNDS
		//ingroup
	public function getUserActionMenuForInGroups($groupid, $perm) {

		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li'.($perm->inbound->inbound_update === 'N' ? ' class="hidden"' : '').'><a class="edit-ingroup" href="#" data-id="'.$groupid.'">'.$this->lh->translationFor("modify").'</a></li>
	                    <li class="divider'.(($perm->inbound->inbound_update === 'N' || $perm->inbound->inbound_delete === 'N') ? ' hidden' : '').'"></li>
	                    <li'.($perm->inbound->inbound_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-ingroup" href="#" data-id="'.$groupid.'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div>';
			//<li><a class="info-T_user" href="'.$userid.'">'.$this->lh->translationFor("info").'</a></li>
	}
		//ivr
	public function ActionMenuForIVR($ivr, $desc, $perm) {

		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li'.($perm->ivr->ivr_update === 'N' ? ' class="hidden"' : '').'><a class="edit-ivr" href="#" data-id="'.$ivr.'" data-desc="'.$desc.'">'.$this->lh->translationFor("modify").'</a></li>
	                    <li class="divider'.(($perm->ivr->ivr_update === 'N' || $perm->ivr->ivr_delete === 'N') ? ' hidden' : '').'"></li>
	                    <li'.($perm->ivr->ivr_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-ivr" href="#" data-id="'.$ivr.'" data-desc="'.$desc.'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div>';
			//<li><a class="info-T_user" href="'.$userid.'">'.$this->lh->translationFor("info").'</a></li>
	}
		//did
	public function getUserActionMenuForDID($did, $desc, $perm) {

		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li'.($perm->did->did_update === 'N' ? ' class="hidden"' : '').'><a class="edit-phonenumber" href="#" data-id="'.$did.'" data-desc="'.$desc.'">'.$this->lh->translationFor("modify").'</a></li>
	                    <li class="divider'.(($perm->did->did_update === 'N' || $perm->did->did_delete === 'N') ? ' hidden' : '').'"></li>
	                    <li'.($perm->did->did_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-phonenumber" href="#" data-id="'.$did.'" data-desc="'.$desc.'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div>';
			//<li><a class="info-T_user" href="'.$userid.'">'.$this->lh->translationFor("info").'</a></li>
	}

	//telephony menu for settings > phones
	private function getUserActionMenuForPhones($exten) {

		return '<div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li><a class="edit-phone" href="#" data-id="'.$exten.'">'.$this->lh->translationFor("modify").'</a></li>
	                    <li class="divider"></li>
	                    <li><a class="delete-phone" href="#" data-id="'.$exten.'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div>';
			//<li><a class="info-T_user" href="'.$userid.'">'.$this->lh->translationFor("info").'</a></li>
	}

    /**
     * Returns a HTML Table representation containing all the user's in the system (only relevant data).
     * @return String a HTML Table representation of the data of all users in the system.
     */
	public function getAllUsersAsTable() {
       $users = $this->db->getAllUsers();
       // is null?
       if (is_null($users)) { // error getting contacts
	       return $this->calloutErrorMessage($this->lh->translationFor("unable_get_user_list"));
       } else if (empty($users)) { // no contacts found
	       return $this->calloutWarningMessage($this->lh->translationFor("no_users_in_list"));
       } else {
	       // we have some users, show a table
	       // $columns = array("id", "name", "email", "creation_date", "role", "status", "action");
       	   $columns = array("id", "name", "email", "role", "status", "action");
	       $hideOnMedium = array("email", "creation_date", "role");
	       $hideOnLow = array("email", "creation_date", "role", "status");
		   $result = $this->generateTableHeaderWithItems($columns, "users", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	       // iterate through all contacts
	       foreach ($users as $userData) {
	       	   // $status = $userData["status"] == 1 ? $this->lh->translationFor("enabled") : $this->lh->translationFor("disabled");
	       	   // $userRole = $this->lh->translationFor($this->getRoleNameForRole($userData["role"]));
	       	   $status = $userData["active"] == "Y" ? $this->lh->translationFor("enabled") : $this->lh->translationFor("disabled");
	       	   $userRole = $this->lh->translationFor($this->getRoleNameForRole($userData["user_level"]));

	       	   // $action = $this->getUserActionMenuForUser($userData["id"], $userData["name"], $userData["status"]);
	       	   $action = $this->getUserActionMenuForUser($userData["user_id"], $userData["user"], $userData["active"]);
		       // $result = $result."<tr>
	        //             <td>".$userData["id"]."</td>
	        //             <td><a class=\"edit-action\" href=\"".$userData["id"]."\">".$userData["name"]."</a></td>
	        //             <td class='hide-on-medium hide-on-low'>".$userData["email"]."</td>
	        //             <td class='hide-on-medium hide-on-low'>".$userData["creation_date"]."</td>
	        //             <td class='hide-on-medium hide-on-low'>".$userRole."</td>
	        //             <td class='hide-on-low'>".$status."</td>
	        //             <td nowrap>".$action."</td>
	        //         </tr>";
	           $result = $result."<tr>
	                    <td>".$userData["user_id"]."</td>
	                    <td><a class=\"edit-action\" href=\"".$userData["user_id"]."\">".$userData["user"]."</a></td>
	                    <td class='hide-on-medium hide-on-low'>".$userData["email"]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$userRole."</td>
	                    <td class='hide-on-low'>".$status."</td>
	                    <td nowrap>".$action."</td>
	                </tr>";
	       }

	       // print suffix
	       $result .= $this->generateTableFooterWithItems($columns, true, false, $hideOnMedium, $hideOnLow);
	       return $result;
       }
	}

	/**
	 * Retrieves the human friendly descriptive name for a role given its identifier number.
	 * @param $roleNumber Int number/identifier of the role.
	 * @return Human friendly descriptive name for the role.
	 */
	private function getRoleNameForRole($roleNumber) {
		switch ($roleNumber) {
			case CRM_DEFAULTS_USER_ROLE_ADMIN:
				return "administrator";
				break;
			case CRM_DEFAULTS_USER_ROLE_MANAGER:
				return "manager";
				break;
			case CRM_DEFAULTS_USER_ROLE_WRITER:
				return "writer";
				break;
			case CRM_DEFAULTS_USER_ROLE_READER:
				return "reader";
				break;
			case CRM_DEFAULTS_USER_ROLE_GUEST:
				return "guest";
				break;
		}
	}

	/**
	 * Returns a warning message in case the setting for email confirmation on new user accounts is activated.
	 */
	public function getUserActivationEmailWarning() {
		$confirmationEmailSetting = $this->db->getSettingValueForKey(CRM_SETTING_CONFIRMATION_EMAIL);
		if (filter_var($confirmationEmailSetting, FILTER_VALIDATE_BOOLEAN)) {
			return '<p>'.$this->lh->translationFor("confirmation_email_enabled").'</p>';
		} else { return '<p>'.$this->lh->translationFor("confirmation_email_disabled").'</p>'; }
	}

	/**
	 * Generates the HTML with a unauthorized access. It must be included inside a <section> section.
	 */
	public function getUnauthotizedAccessMessage() {
		return $this->boxWithMessage($this->lh->translationFor("access_denied"), $this->lh->translationFor("you_dont_have_permission"), "lock", "danger");
	}

	/** Modules */

	public function getModulesAsList() {
		// get all modules.
		$mh = \creamy\ModuleHandler::getInstance();
		$allModules = $mh->listOfAllModules();

		// generate a table with all elements.
		$items = array("name", "version", "enabled", "action");
		$table = $this->generateTableHeaderWithItems($items, "moduleslist", "table-striped", true, false, array(), array("version", "action"));
		// fill table
		foreach ($allModules as $moduleClass => $moduleDefinition) {
			// module data
			if ($mh->moduleIsEnabled($moduleClass)) { // module is enabled.
				$status = "<i class='fa fa-check-square-o'></i>";
				$enabled = true;
			} else { // module is disabled.
				$status = "<i class='fa fa-times-circle-o'></i>";
				$enabled = false;
			}
			$moduleName = $moduleDefinition->getModuleName();
			$moduleVersion = $moduleDefinition->getModuleVersion();
			$moduleDescription = $moduleDefinition->getModuleDescription();
			// module action
			$moduleShortName = $moduleDefinition->getModuleShortName();
			$action = $this->getActionButtonForModule($moduleShortName, $enabled);
			// add module row
			$table .= "<tr><td><b>$moduleName</b><br/><div class='small hide-on-low'>$moduleDescription</div></td><td class='small hide-on-low'>$moduleVersion</td><td class='small hide-on-low'>$status</td><td>$action</td></tr>";
		}
		//insert smtp_custom
		$smtp_status = $this->API_getSMTPActivation();
		if ($smtp_status == 1) { // module is enabled.
			$status = "<i class='fa fa-check-square-o'></i>";
		} else { // module is disabled.
			$status = "<i class='fa fa-times-circle-o'></i>";
		}
		$moduleName = $this->lh->translationFor("smtp_settings");
		$moduleDescription = $this->lh->translationFor("smtp_settings_desc");
		$action = $this->getActionButtonForSMTP($smtp_status);
		$table .= "<tr><td><b>$moduleName</b><br/><div class='small hide-on-low'>$moduleDescription</div></td><td class='small hide-on-low'>$moduleVersion</td><td class='small hide-on-low'>$status</td><td>$action</td></tr>";
		
		// close table
		$table .= $this->generateTableFooterWithItems($items, true, false, array(), array("version", "action"));

		// add javascript code.
		$enableJS = $this->clickableClassActionJS("enable_module", "module_name", "href", "./php/ModifyModule.php", $this->reloadLocationJS(), $this->showRetrievedErrorMessageAlertJS(), false, CRM_DEFAULT_SUCCESS_RESPONSE, array("enabled"=>"1"), null);
		$disableJS = $this->clickableClassActionJS("disable_module", "module_name", "href", "./php/ModifyModule.php", $this->reloadLocationJS(), $this->showRetrievedErrorMessageAlertJS(), false, CRM_DEFAULT_SUCCESS_RESPONSE, array("enabled"=>"0"), null);
		$deleteJS = $this->clickableClassActionJS("uninstall_module", "module_name", "href", "./php/DeleteModule.php", $this->reloadLocationJS(), $this->showRetrievedErrorMessageAlertJS(), true);
		$table .= $enableJS.$disableJS.$deleteJS;

		return $table;
	}

	public function getModuleHandlerLog() {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->getModuleHandlerLog();
	}

	private function getActionButtonForModule($moduleShortName, $enabled) {
		// build the options.
		$ed_option = $enabled ? $this->actionForPopupButtonWithClass("disable_module", $this->lh->translationFor("disable"), $moduleShortName) : $this->actionForPopupButtonWithClass("enable_module", $this->lh->translationFor("enable"), $moduleShortName);
		//$up_option = $this->actionForPopupButtonWithClass("update_module", $this->lh->translationFor("update"), $moduleShortName);
		$un_option = $this->actionForPopupButtonWithClass("uninstall_module", $this->lh->translationFor("uninstall"), $moduleShortName);
		$options = array($ed_option, $un_option);
		// build and return the popup action button.
		return $this->popupActionButton($this->lh->translationFor("choose_action"), $options);
	}

	/** Header */

	/**
	 * Returns the default creamy header for all pages.
	 */
	public function creamyHeader($user) {
		// module topbar elements
		$mh = \creamy\ModuleHandler::getInstance();
		$moduleTopbarElements = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_TOPBAR, null, CRM_MODULE_MERGING_STRATEGY_APPEND);
		// header elements
		$logo = $this->creamyHeaderLogo();
		$name = $this->creamyHeaderName();
			//<a href="./index.php" class="logo"><img src="'.$logo.'" width="auto" height="32"> '.$name.'</a>
		// return header
		$avatarElement = $this->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 22, true);
		return '<header class="main-header">
				<a href="./index.php" class="logo"><img src="'.$logo.'" width="auto" height="45" style="padding-top:10px;"></a>
	            <nav class="navbar navbar-static-top" role="navigation">
	                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
	                    <span class="sr-only">Toggle navigation</span>
	                    <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
	                </a>
	                <div class="navbar-custom-menu">
	                    <ul class="nav navbar-nav">
	                    		'.$moduleTopbarElements.'
	                    		'.$this->getTopbarMessagesMenu($user).'
		                    	'.$this->getTopbarNotificationsMenu($user).'
		                    	'.$this->getTopbarTasksMenu($user).'
		                    	<li>
			                    	<a href="#" class="visible-xs" data-toggle="control-sidebar" style="padding-top: 17px; padding-bottom: 18px;"><i class="fa fa-cogs"></i></a>
										<a href="#" class="hidden-xs" data-toggle="control-sidebar" style="padding-top: 14px; padding-bottom: 14px;">
											'.$avatarElement.'
											<span> '.$user->getUserName().' <i class="caret"></i></span>
										</a>
				               </li>
	                    </ul>
	                </div>
	            </nav>
	        </header>
	        <div class="preloader">
	        	<div class="pull-right close-preloader" style="display:none;">
    				<a type="button" class="close-preloader-button" aria-label="Close" style="color:white;"><i class="fa fa-close fa-lg"></i></a>
    			</div>
    			<center>
    					<img src="'.$logo.'" style="max-width:50%;"/>
    					<span class="dots">
    					<div class="circ1"></div><div class="circ2"></div><div class="circ3"></div><div class="circ4"></div>
    					</span>

    					<br/><br/>
		    			<div class="reload-page" style="display:none; color:white;">
		    				The page is taking too long to load. It probably failed. <br/> Please check your Internet Connection and click the button below to try again...<br/>
		    				<br/><button type="button" class="btn reload-button" style="display:none; color: #333333;"><i class="fa fa-refresh fa-3x"></i></button>
		    			</div>
    			</center>

    		</div>

    		<script type="text/javascript">

    			setTimeout( function() {
				    $(".close-preloader").fadeIn("slow");
				}, 10000 );

				setTimeout( function() {
				    $(".reload-page").fadeIn("slow");
				}, 30000 );

				setTimeout( function() {
				    $(".reload-button").fadeIn("slow");
				}, 32000 );

	    		$(window).ready(function() {
					$(".preloader").fadeOut("slow");
				})

				$(document).on("click", ".close-preloader-button", function() {
					$(".preloader").fadeOut("slow");
				});

				$(document).on("click", ".reload-button", function() {
					$(".reload-button").html("<i class=\"fa fa-refresh fa-spin fa-3x fa-fw\"></i><span class=\"sr-only\">Loading...</span>");
					window.location = window.location.href;
				});

				$(window).ready(function() {
					$(".preloader").fadeOut("slow");
					$(".reload-page").fadeOut("slow");
					$(".reload-button").fadeOut("slow");
				})

				$(window).load(function() {
					$(".reload-page").html("");
					$(".reload-button").html("");
				})



			</script>
			';
	}

    /**
	 * Returns the default creamy header for all pages.
	 */
	public function creamyAgentHeader($user) {
		// module topbar elements
		$mh = \creamy\ModuleHandler::getInstance();
		$moduleTopbarElements = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_TOPBAR_AGENT, null, CRM_MODULE_MERGING_STRATEGY_APPEND);
		// header elements
		$logo = $this->creamyHeaderLogo();
		$name = $this->creamyHeaderName();
			//<a href="./index.php" class="logo"><img src="'.$logo.'" width="auto" height="32"> '.$name.'</a>
		// return header
		// old img element : <img src="'.$user->getUserAvatar().'" width="12" height="auto"  class="user-image img-circle" alt="User Image" style="padding-bottom: 3px;" />
									//'.$this->getTopbarMessagesMenu($user).'
									//'.$this->getTopbarNotificationsMenu($user).'
									//'.$this->getTopbarTasksMenu($user).'

		$avatarElement = $this->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 22, true);
		return '<header class="main-header">
				<a href="./index.php" class="logo"><img src="'.$logo.'" width="auto" height="45" style="padding-top:10px;"></a>
	            <nav class="navbar navbar-static-top" role="navigation">
	                <a href="#" class="sidebar-toggle hidden" data-toggle="offcanvas" role="button">
	                    <span class="sr-only">Toggle navigation</span>
	                    <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
	                </a>
	                <div class="navbar-custom-menu">
	                    <ul class="nav navbar-nav">
	                    		'.$moduleTopbarElements.'
	                    		'.$this->getTopbarMessagesMenu($user).'
		                    	<li>
			                    	<a href="#" class="visible-xs" data-toggle="control-sidebar" style="padding-top: 17px; padding-bottom: 18px; margin-right: -15px;"><i class="fa fa-cogs"></i></a>
										<a href="#" class="hidden-xs" data-toggle="control-sidebar" style="padding-top: 14px; padding-bottom: 14px; margin-right: -15px;">
											'.$avatarElement.'
											<span> '.$user->getUserName().' <i class="caret"></i></span>
										</a>
				               </li>
	                    </ul>
	                </div>
	            </nav>
	        </header>
	        <div class="preloader">
    			<center>
    					<img src="'.$logo.'" class="img-responsive" style="display: inline-block;"/>
						<br class="visible-xs">
						<br class="visible-xs">
    					<span class="dots">
    					<div class="circ1"></div><div class="circ2"></div><div class="circ3"></div><div class="circ4"></div>
    					</span>
    			</center>
    		</div>';
	}
	/**
	 * Returns the creamy company custom logo. If no custom logo is defined, returns
	 * the default white creamy logo.
	 * @return String a string containing the relative URL for the header logo.
	 */
	public function creamyHeaderLogo() {
		$customLogo = $this->db->getSettingValueForKey(CRM_SETTING_COMPANY_LOGO);
		return (!empty($customLogo) ? $customLogo : CRM_DEFAULT_HEADER_LOGO);
	}

	/**
	 * Returns the creamy name for the header. If a custom company name is defined, it
	 * returns it, otherwise, it returns "Creamy".
	 * @return String a string containing the company custom name (if set) or "Creamy".
	 */
	public function creamyHeaderName() {
		$customName = $this->db->getSettingValueForKey(CRM_SETTING_COMPANY_NAME);
		return (!empty($customName) ? $customName : "GOautodial Inc.");
	}

	/**
	 * Returns the creamy body for a page including the theme.
	 * If no theme setting is found, it defaults to CRM_SETTING_DEFAULT_THEME
	 */
	public function creamyBody() {
		$theme = $this->db->getSettingValueForKey(CRM_SETTING_THEME);
		if (empty($theme)) { $theme = CRM_SETTING_DEFAULT_THEME; }
		return '<body class="skin-'.$theme.' sidebar-mini fixed ">';
	}
	public function creamyAgentBody() {
		$theme = $this->db->getSettingValueForKey(CRM_SETTING_THEME);
		if (empty($theme)) { $theme = CRM_SETTING_DEFAULT_THEME; }
		return '<body class="skin-'.$theme.' sidebar-collapse fixed">';
	}
	/**
	 * Returns the proper css style for the selected theme.
	 * If theme is not found, it defaults to CRM_SETTING_DEFAULT_THEME
	 */
	public function creamyThemeCSS() {
		$theme = $this->db->getSettingValueForKey(CRM_SETTING_THEME);
		if (empty($theme)) { $theme = CRM_SETTING_DEFAULT_THEME; }
		$return  = '<link href="css/skins/skin-'.$theme.'.min.css" rel="stylesheet" type="text/css" />';
		//$return .= "//{$_SERVER['SCRIPT_FILENAME']}\n";
		return $return;
	}

	/**
	 * Returns the default creamy footer for all pages.
	 */
	public function creamyFooter() {
		$version = $this->db->getSettingValueForKey(CRM_SETTING_CRM_VERSION);
		$company_name = $this->creamyHeaderName();
		if (empty($version)) { $version = "unknown"; }
		$version = "4.0";

		$footer = '<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> '.$version.'</div><strong>'.$this->lh->translationFor("copyright").' &copy; '.date("Y").' <a href="'.$this->lh->translationFor("company_url").'">'.$company_name.'</a> '.$this->lh->translationFor("all_rights_reserved").'.
			</div>
			</footer>';
		$footer .= '			<!-- Modal -->
			<!-- View Campaign -->
			<div id="view-campaign-modal" class="modal fade" role="dialog">
			  <div class="modal-dialog">

			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title"><b>Campaign Information</b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
			      </div>
			      <div class="modal-body">
			      	<div class="output-message-no-result hide">
				      	<div class="alert alert-warning alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Notice!</strong> There was an error retrieving details. Either error or no result.
						</div>
					</div>
			        <div id="content" class="view-form ">
					    <div class="form-horizontal">
                                                <div class="form-group">
					    		<label class="control-label col-lg-5">Campaign ID:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaignid"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Campaign Name:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaignname"></span></b>
					    	</div>
					    	<div class="output-message-no-result hide form-group">
					    		<label class="control-label col-lg-5">Campaign Description:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaigndesc"></span></b>
                                                </div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Call Recordings:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-callrecordings"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Campaign Caller ID:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-campaigncid"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Local Call Time:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-localcalltime"></span></b>
                                                </div>
                                            </div>
                                </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			      </div>
			    </div>
			    <!-- End of modal content -->
                           </div>
                         </div>
                        </div>
			<!-- End of View Campaign -->

			<!-- View Agent -->
			<div id="view-agent-modal" class="modal fade" role="dialog">
			  <div class="modal-dialog">

			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title"><b>Agent Information</b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
			      </div>
			      <div class="modal-body">
			      	<div class="output-message-no-result hide">
				      	<div class="alert alert-warning alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Notice!</strong> There was an error retrieving details. Either error or no result.
						</div>
					</div>
			        <div id="content" class="view-form ">
					    <div class="form-horizontal">
                                                <div class="form-group">
					    		<label class="control-label col-lg-5">Agent ID:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-userid"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Agent Name:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-user"></span></b>
					    	</div>
					    	<div class="output-message-no-result hide form-group">
					    		<label class="control-label col-lg-5">Email:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-email"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">User Group:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-usergroup"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">User Level:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-userlevel"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Active:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-active"></span></b>
                                                </div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Phone Login:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-phonelogin"></span></b>
					    	</div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Phone Password:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-phonepass"></span></b>
                                                </div>
					    	<div class="form-group">
					    		<label class="control-label col-lg-5">Voicemail:</label>
					    		<b class="control-label col-lg-7" style="text-align: left;"><span id="modal-voicemail"></span></b>
					    	</div>
                                            </div>
                                </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			      </div>
			    </div>
			    <!-- End of modal content -->
			  </div>
			 </div>
			</div>
			<!-- End of View Agent -->
			<!-- End of modal -->
                ';
		return $footer;
	}

	/** Topbar Menu elements */

	/**
	 * Generates the HTML for the message notifications of a user as a dropdown list element to include in the top bar.
	 * @param $userid the id of the user.
	 */
	public function getTopbarMessagesMenu($user) {
		if (!$user->userHasBasicPermission()) return '';
      $list = $this->db->getMessagesOfType($user->getUserId(), MESSAGES_GET_UNREAD_MESSAGES);
		$numMessages = count($list);

		$headerText = $this->lh->translationFor("you_have").' '.$numMessages.' '.$this->lh->translationFor("unread_messages");
		$result = $this->getTopbarMenuHeader("envelope-o", $numMessages, CRM_UI_TOPBAR_MENU_STYLE_COMPLEX, $headerText, null, CRM_UI_STYLE_SUCCESS, false);

      foreach ($list as $message) {
			$from = $this->db->getDataForUser($message['user_from']);
			//if (empty($message["remote_user"])) $remoteuser = $this->lh->translationFor("unknown");
			if (empty($from['user'])) {
			   $remoteuser = $this->lh->translationFor("unknown");
			}else{
			   //$remoteuser = $message["remote_user"];
			   $remoteuser = $from['user'];
			}

	      if (empty($message["remote_avatar"])) {
				$remoteavatar = CRM_DEFAULTS_USER_AVATAR;
			} else {
				$remoteavatar = $message["remote_avatar"];
	      }
	      $result .= $this->getTopbarComplexElement($remoteuser, $message["message"], $message["date"], $remoteavatar, "messages.php");
      }
		$result .= $this->getTopbarMenuFooter($this->lh->translationFor("see_all_messages"), "messages.php");
		return $result;
	}

	/**
	 * Generates the HTML for the main info boxes of the dashboard.
	 */
	public function dashboardInfoBoxes($userid) {
		$boxes = "";
		$firstCustomerType = $this->db->getFirstCustomerGroupTableName();
		$columnSize = isset($firstCustomerType) ? 3 : 4;

		// new contacts
		$contactsUrl = "./customerslist.php?customer_type=vicidial_list&customer_name=".urlencode($this->lh->translationFor("contacts"));
		$boxes .= $this->infoBox($this->lh->translationFor("new_contacts"), $this->db->getNumberOfNewContacts(), $contactsUrl, "user-plus", "purple", "purple-dark");
		// new customers
		if (isset($firstCustomerType)) {
			$customersURL = "./customerslist.php?customer_type=vicidial_list&customer_name=".urlencode($firstCustomerType["description"]);
			$boxes .= $this->infoBox($this->lh->translationFor("new_customers"), $this->db->getNumberOfNewCustomers(), $customersURL, "users", "purple", "purple-dark");
		}
		// notifications
		$numNotifications = intval($this->db->getNumberOfTodayNotifications($userid));
		$numEvents = intval($this->db->getNumberOfTodayEvents($userid));
		$num = $numNotifications + $numEvents;
		$boxes .= $this->infoBox($this->lh->translationFor("notifications"),$num , "notifications.php", "clock-o", "purple", "purple-dark");
		// events today // TODO: Change
		$boxes .= $this->infoBox($this->lh->translationFor("unfinished_tasks"), $this->db->getUnfinishedTasksNumber($userid), "tasks.php", "calendar", "green", "gray-dark");

		return $boxes;
	}

	/**
	/ * Generates the HTML for the alert notifications of a user as a dropdown list element to include in the top bar.
	 * @param $userid the id of the user.
	 */
	protected function getTopbarNotificationsMenu($user) {
		if (!$user->userHasBasicPermission()) return '';

		// get notifications number
		$notifications = $this->db->getTodayNotifications($user->getUserId());
		if (empty($notifications)) $notificationNum = 0;
		else $notificationNum = count($notifications);
		$eventsForToday = $this->db->getEventsForToday($user->getUserId());
		if (!empty($eventsForToday)) $notificationNum += count($eventsForToday);
		// build header
		$headerText = $this->lh->translationFor("you_have").' '.$notificationNum.' '.$this->lh->translationFor("notifications");
		$result = $this->getTopbarMenuHeader("calendar", $notificationNum, CRM_UI_TOPBAR_MENU_STYLE_SIMPLE, $headerText, null, CRM_UI_STYLE_WARNING, false);
		// build notifications
        foreach ($notifications as $notification) {
	        $result .= $this->getTopbarSimpleElement($notification["text"], $this->notificationIconForNotificationType($notification["type"]), "notifications.php", $this->getRandomUIStyle());
        }
        // build events.
        foreach ($eventsForToday as $event) {
	        $url = "events.php?initial_date=".urlencode($event["start_date"]);
	        $tint = $this->creamyColorForHexValue($event["color"]);
	        $result .= $this->getTopbarSimpleElement($event["title"], "calendar-o", $url, $tint);
        }

        // footer and result
        $result .= $this->getTopbarMenuFooter($this->lh->translationFor("see_all_notifications"), "notifications.php");
        return $result;
	}

	protected function getTopbarTasksMenu($user) {
		if (!$user->userHasBasicPermission()) return '';

		$list = $this->db->getUnfinishedTasks($user->getUserId());
		$numTasks = count($list);

		$headerText = $this->lh->translationFor("you_have").' '.$numTasks.' '.$this->lh->translationFor("pending_tasks");
		$result = $this->getTopbarMenuHeader("tasks", $numTasks, CRM_UI_TOPBAR_MENU_STYLE_DATE, $headerText, null, CRM_UI_STYLE_DANGER, false);

        foreach ($list as $task) {
	        $result .= $this->getTopbarSimpleElementWithDate($task["description"], $task["creation_date"], "clock-o", "tasks.php", CRM_UI_STYLE_WARNING);
        }

        $result .= $this->getTopbarMenuFooter($this->lh->translationFor("see_all_tasks"), "tasks.php");
        return $result;
    }

	/**
	 * Generates the HTML for the user's topbar menu.
	 * @param $userid the id of the user.
	 */
	protected function getTopbarUserMenu($user) {
		// menu actions & change my data(only for users with permissions).
		$menuActions = '';
		$changeMyData = '';
		if ($user->userHasBasicPermission()) {
			$menuActions = '<li class="user-body">
				<div class="text-center"><a href="" data-toggle="modal" id="change-password-toggle" data-target="#change-password-dialog-modal">'.$this->lh->translationFor("change_password").'</a></div>
				<div class="text-center"><a href="./messages.php">'.$this->lh->translationFor("messages").'</a></div>
				<div class="text-center"><a href="./notifications.php">'.$this->lh->translationFor("notifications").'</a></div>
				<div class="text-center"><a href="./tasks.php">'.$this->lh->translationFor("tasks").'</a></div>
			</li>';
			$changeMyData = '<div class="pull-left"><a href="./edituser.php" class="btn btn-default btn-flat">'.$this->lh->translationFor("my_profile").'</a></div>';
		}

		// old img element : <img src="'.$user->getUserAvatar().'" style="border-color:transparent;" alt="User Image" />
		// <img src="'.$user->getUserAvatar().'" width="auto" height="auto"  class="user-image" alt="User Image" />
		$avatarElement1 = $this->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 22, true);
		$avatarElement2 = $this->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 96, false, true, false);
		return '<li class="dropdown user user-menu">
	                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                    '.$avatarElement1.'
	                    <span>'.$user->getUserName().' <i class="caret"></i></span>
	                </a>
	                <ul class="dropdown-menu">
	                    <li class="user-header bg-light-blue"">
	                        '.$avatarElement2.'
	                        <p>'.$user->getUserName().'<small>'.$this->lh->translationFor("nice_to_see_you_again").'</small></p>
	                    </li>'.$menuActions.'
	                    <li class="user-footer">'.$changeMyData.'
	                        <div class="pull-right"><a href="./logout.php" class="btn btn-default btn-flat">'.$this->lh->translationFor("exit").'</a></div>
	                    </li>
	                </ul>
	            </li>';
	}

	public function getTopbarMenuHeader($icon, $badge, $menuStyle, $headerText = null, $headerLink = null, $badgeStyle = CRM_UI_STYLE_DEFAULT, $hideForLowResolution = true) {
		// header text and link
		if (!empty($headerText)) {
			$linkPrefix = isset($headerLink) ? '<a href="'.$headerLink.'">' : '';
			$linkSuffix = isset($headerLink) ? '</a>' : '';
			$headerCode = '<li class="header">'.$linkPrefix.$headerText.$linkSuffix.'</li>';
		} else { $headerCode = ""; }
		$hideCode = $hideForLowResolution? "hide-on-low" : "";

		// return the topbar menu header
		return '<li class="dropdown '.$menuStyle.'-menu '.$hideCode.'"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-'.$icon.'"></i><span class="label label-'.$badgeStyle.'">'.$badge.'</span></a>
					<ul class="dropdown-menu">'.$headerCode.'<li><ul class="menu">';
	}

	public function getTopbarMenuFooter($footerText, $footerLink = null) {
		$linkPrefix = isset($footerLink) ? '<a href="'.$footerLink.'">' : '';
		$linkSuffix = isset($footerLink) ? '</a>' : '';
		return '</ul></li><li class="footer">'.$linkPrefix.$footerText.$linkSuffix.'</li></ul></li>';
	}

	public function getTopbarSimpleElement($text, $icon, $link, $tint = "aqua") {
		//$shortText = $this->substringUpTo($text, 40);
		$shortText = strlen($text) > 40 ? substr($text,0,40)."..." : $text;
		return '<li style="text-align: left; !important;"><a href="'.$link.'"><i class="fa fa-'.$icon.' text-'.$tint.'"></i><b>'.$shortText.'</b></a></li>';
	}

	public function getTopbarSimpleElementWithDate($text, $date, $icon, $link, $tint = CRM_UI_STYLE_DEFAULT) {
		//$shortText = $this->substringUpTo($text, 30);
	    $shortText = strlen($text) > 25 ? substr($text,0,25)."..." : $text;
		$relativeTime = $this->relativeTime($date, 1);
		return '<li><a href="'.$link.'"><h3><p class="pull-left"><b>'.$shortText.'</b></p><small class="label label-'.$tint.' pull-right"><i class="fa fa-'.$icon.'"></i> '.$relativeTime.'</small></h3></a></li>';
	}

	public function getTopbarComplexElement($title, $text, $date, $image, $link) {
		$shortTitle = $this->substringUpTo($title, 20);
		$shortText = $this->substringUpTo($text, 40);
	    $relativeTime = $this->relativeTime($date, 1);
		return '<li><a href="'.$link.'">
                    <div class="pull-left">
                        <img src="'.$image.'" class="img-circle" alt="User Image"/>
                    </div>
                    <h4>'.$title.'
                    <small class="label"><i class="fa fa-clock-o"></i> '.$relativeTime.'</small>
                    </h4>
                    <p>'.$shortText.'</p>
                </a>
            </li>';
	}

	public function getTopbarCustomMenu($header, $elements, $footer) { return $header.$elements.$footer; }

	/** Sidebar */

	/**
	 * Generates the HTML for the sidebar of a user, given its role.
	 * @param $userid the id of the user.
	 */
	public function getSidebar($userid, $username, $userrole, $avatar, $usergroup = NULL) {
		$numMessages = $this->db->getUnreadMessagesNumber($userid);
		$numTasks = $this->db->getUnfinishedTasksNumber($userid);
		$numNotifications = $this->db->getNumberOfTodayNotifications($userid) + $this->db->getNumberOfTodayEvents($userid);
		$mh = \creamy\ModuleHandler::getInstance();
		$smtp_status = $this->API_getSMTPActivation(); // smtp_status
		$gopackage = $this->api->API_getGOPackage(); // smtp_status
		$usergroup = (!isset($usergroup) ? $_SESSION['usergroup'] : $usergroup);
		$perms = $this->api->goGetPermissions('sidebar', $usergroup);
		$perms = json_decode(stripslashes($perms->data->permissions));

		$adminArea = "";
		$telephonyArea = "";
		$settings = "";
		$callreports = "";
		$loadleads = "";
		$crm = "";
		$eventsArea = "";
		if ($userrole != CRM_DEFAULTS_USER_ROLE_AGENT) {

			$modulesWithSettings = $mh->modulesWithSettings();
			$adminArea = '<li class="treeview"><a href="#"><i class="fa fa-dashboard"></i> <span>'.$this->lh->translationFor("administration").'</span><i class="fa fa-angle-left pull-right"></i></a>
			<ul class="treeview-menu">';
			
			if ($_SESSION['user'] === "goautodial" || $_SESSION['user'] === "goAPI")
			$adminArea .= $this->getSidebarItem("./adminsettings.php", "gears", $this->lh->translationFor("settings")); // admin settings
			
			//$adminArea .= $this->getSidebarItem("./telephonyusers.php", "user", $this->lh->translationFor("users")); // admin settings
			$adminArea .= $this->getSidebarItem("./adminmodules.php", "archive", $this->lh->translationFor("modules")); // admin settings
			//$adminArea .= $this->getSidebarItem("./admincustomers.php", "users", $this->lh->translationFor("customers")); // admin settings
			foreach ($modulesWithSettings as $k => $m) { $adminArea .= $this->getSidebarItem("./modulesettings.php?module_name=".urlencode($k), $m->mainPageViewIcon(), $m->mainPageViewTitle()); }
			if ($smtp_status == 1)  // module is enabled.
				$adminArea .= $this->getSidebarItem("./settingssmtp.php", "envelope-square", $this->lh->translationFor("smtp_settings")); // smtp settings
			$adminArea .= '</ul></li>';
			$telephonyArea = '<li class="treeview"><a href="#"><i class="fa fa-phone"></i> <span>'.$this->lh->translationFor("telephony").'</span><i class="fa fa-angle-left pull-right"></i></a><ul class="treeview-menu">';
			if ($perms->user->user_read == 'R')
				$telephonyArea .= $this-> getSidebarItem("./telephonyusers.php", "users", $this->lh->translationFor("users"));
			if ($perms->campaign->campaign_read == 'R')
				$telephonyArea .= $this-> getSidebarItem("./telephonycampaigns.php", "fa fa-dashboard", $this->lh->translationFor("campaigns"));
			if ($perms->list->list_read == 'R')
				$telephonyArea .= $this-> getSidebarItem("./telephonylist.php", "list", $this->lh->translationFor("lists"));
			if ($perms->script->script_read == 'R')
				$telephonyArea .= $this-> getSidebarItem("./telephonyscripts.php", "comment", $this->lh->translationFor("scripts"));
			if ( ($perms->inbound->inbound_read == 'R' && $gopackage->packagetype !== "gosmall") || ($_SESSION['usergroup'] === "ADMIN") )
				$telephonyArea .= $this-> getSidebarItem("./telephonyinbound.php", "phone", $this->lh->translationFor("inbound"));
			if ($perms->voicefiles->voicefiles_upload == 'C') {
				$telephonyArea .= $this-> getSidebarItem("./audiofiles.php", "music", $this->lh->translationFor("audiofiles"));
				//$telephonyArea .= $this-> getSidebarItem("./telephonymusiconhold.php", "music", $this->lh->translationFor("music_on_hold"));
				//$telephonyArea .= $this-> getSidebarItem("./telephonyvoicefiles.php", "files-o", $this->lh->translationFor("voice_files"));
			}
			$telephonyArea .= '</ul></li>';

			if ($userrole == CRM_DEFAULTS_USER_ROLE_ADMIN) {
				$settings = '<li class="treeview"><a href="#"><i class="fa fa-gear"></i> <span>'.$this->lh->translationFor("settings").'</span><i class="fa fa-angle-left pull-right"></i></a><ul class="treeview-menu">';				
				$settings .= $this-> getSidebarItem("./settingscalltimes.php", "list-ol", $this->lh->translationFor("call_times"));
				$settings .= $this-> getSidebarItem("./settingsvoicemails.php", "envelope", $this->lh->translationFor("voice_mails"));
				$settings .= $this-> getSidebarItem("./settingsusergroups.php", "users", $this->lh->translationFor("user_groups"));
				
				if ($perms->carriers->carriers_read == 'R' || $userrole == CRM_DEFAULTS_USER_ROLE_ADMIN)
					$settings .= $this-> getSidebarItem("./settingscarriers.php", "signal", $this->lh->translationFor("carriers"));
				
				if ($perms->servers->servers_read == 'R' || $userrole == CRM_DEFAULTS_USER_ROLE_ADMIN)
					$settings .= $this-> getSidebarItem("./settingsservers.php", "server", $this->lh->translationFor("servers"));
					
				if ($userrole == CRM_DEFAULTS_USER_ROLE_ADMIN)
					$settings .= $this-> getSidebarItem("./settingsadminlogs.php", "book", $this->lh->translationFor("admin_logs"));
				
				$settings .= '</ul></li>';
			}

			$callreports = '<li class="treeview"><a href="#"><i class="fa fa-bar-chart-o"></i> <span>'.$this->lh->translationFor("call_reports").'</span><i class="fa fa-angle-left pull-right"></i></a><ul class="treeview-menu">';
			$callreports .= $this-> getSidebarItem("./callreports.php", "bar-chart", $this->lh->translationFor("reports_and_go_analytics"));
			
			if ($perms->recordings->recordings_display == 'Y') {
				$callreports .= $this-> getSidebarItem("./callrecordings.php", "phone-square", $this->lh->translationFor("call_recordings"));
			}
			
			$callreports .= '</ul></li>';

			$eventsArea .= $this->getSidebarItem("events.php", "calendar-o", $this->lh->translationFor("events"));

			$crm .= $this->getSidebarItem("crm.php", "group", $this->lh->translationFor("contacts"));
		}

		$agentmenu = NULL;
		if ($userrole == CRM_DEFAULTS_USER_ROLE_AGENT) {
			//$agentmenu .= $this-> getSidebarItem("", "book", $this->lh->translationFor("scripts"));
			//$agentmenu .= $this-> getSidebarItem("", "tasks", $this->lh->translationFor("Custom Form"));
			$agentmenu .= $this->getSidebarItem("customerslist.php", "users", $this->lh->translationFor("contacts"));
			$agentmenu .= $this->getSidebarItem("callbackslist.php", "calendar", $this->lh->translationFor("callbacks"), "0", "blue");
		}

		// get customer types
		$customerTypes = $this->db->getCustomerTypes();

		// prefix: structure and home link
		// old img element : <img src="'.$avatar.'" class="img-circle" alt="User Image" />
		$avatarElement = $this->getVueAvatar($username, $avatar, 40);
		$result = '<aside class="main-sidebar" sidebar-offcanvas"><section class="sidebar">
	            <div class="user-panel hidden">
	                <div class="pull-left image">
	                    <a href="edituser.php">'.$avatarElement.'</a>
	                </div>
	                <div class="pull-left info">
	                    <p>'.$this->lh->translationFor("hello").', '.$username.'</p>
	                    <a href="edituser.php"><i class="fa fa-circle text-success"></i> '.$this->lh->translationFor("online").'</a>
	                </div>
	            </div>
	            <ul class="sidebar-menu"><li class="header">'.strtoupper($this->lh->translationFor("menu")).'</li>';
	    // body: home and customer menus
	    if ($userrole != CRM_DEFAULTS_USER_ROLE_AGENT) {
			if ($perms->dashboard->dashboard_display === 'Y') {
				$result .= $this->getSidebarItem("./index.php", "dashboard", $this->lh->translationFor("Dashboard"));
			}
	    }
	    if ($userrole == CRM_DEFAULTS_USER_ROLE_AGENT) {
	    	$result .= $this->getSidebarItem("./agent.php", "dashboard", $this->lh->translationFor("Home"));
	    }

	    // menu for admin
		if ($perms->user->user_read == 'N' && $perms->campaign->campaign_read == 'N' && $perms->list->list_read == 'N'
			 && $perms->script->script_read == 'N' && $perms->inbound->inbound_read == 'N' && $perms->voicefiles->voicefiles_upload == 'N') {
			$telephonyArea = '';
		}
		$result .= $telephonyArea;
		if ($userrole != CRM_DEFAULTS_USER_ROLE_AGENT) {
			$result .= $settings;
		}
		$result .= $callreports;
		if ($userrole == CRM_DEFAULTS_USER_ROLE_ADMIN) {
			$result .= $adminArea;
		}
		$result .= $crm;
		$result .= $eventsArea;

        // ending: contacts, messages, notifications, tasks, events.

        //$result .= $this->getSidebarItem("customerslist.php", "users", $this->lh->translationFor("contacts"));

		// menu for agents
		$result .= $agentmenu;
		if ($userrole != CRM_DEFAULTS_USER_ROLE_AGENT) {
        $result .= $this->getSidebarItem("messages.php", "envelope", $this->lh->translationFor("messages"), $numMessages);
		//$result .= $this->getSidebarItem("calls.php", "phone", "Calls");
        $result .= $this->getSidebarItem("notifications.php", "exclamation", $this->lh->translationFor("notifications"), $numNotifications, "orange");
        $result .= $this->getSidebarItem("tasks.php", "tasks", $this->lh->translationFor("tasks"), $numTasks, "red");
		}

        // suffix: modules
        $activeModules = $mh->activeModulesInstances();
        foreach ($activeModules as $shortName => $module) {
			if ($module->mainPageViewTitle() != null && $module->needsSidebarDisplay()) {
				$result .= $this->getSidebarItem($mh->pageLinkForModule($shortName, null), $module->mainPageViewIcon(), $module->mainPageViewTitle(), $module->sidebarBadgeNumber());
			}
        }
 
  if($userrole != CRM_DEFAULTS_USER_ROLE_AGENT){
        $result .= $this->getSidebarItem("credits.php", "list-alt", $this->lh->translationFor("Credits"));
  }

		$result .= '</ul></section></aside>';

		return $result;
	}

	/**
	 * Right Sidebar
	 */
	public function getRightSidebar($userid, $username, $avatar, $tabs = array()) {
		$mh = \creamy\ModuleHandler::getInstance();
		$user = \creamy\CreamyUser::currentUser();

		// prefix: structure and home link
		// old img element : <img src="'.$avatar.'" class="img-circle" alt="User Image" />
		$result = '<aside class="control-sidebar control-sidebar-dark">'."\n";

		// Create Tabs
		if (count($tabs) < 1) {
			//$tabs = array('commenting-o'=>'messaging', 'phone'=>'dialer', 'user'=>'settings');
			$tabs = array('user'=>'settings');
		}
		$tabresult = '<ul class="nav nav-tabs nav-justified control-sidebar-tabs">'."\n";
		$tabpanes = '<div class="tab-content" style="border-width:0; overflow-y: hidden; padding-bottom: 30px;">'."\n";
		$x = 0;
		foreach ($tabs as $icon => $tabname) {
			$activeClass = ($x < 1) ? ' class="active"' : '';
			$isActive = ($x < 1) ? true : false;
			$isHidden = ($tabname == 'dialer') ? ' style="display: none;"' : '';
			$tabresult .= '<li id="'.$tabname.'-tab"'.$activeClass.$isHidden.'><a href="#control-sidebar-'.$tabname.'-tab" data-toggle="tab"><i class="fa fa-'.$icon.'"></i></a></li>'."\n";
			$tabpanes .= $this->getRightTabPane($user, $tabname, $isActive);
			$x++;
		}
		$tabresult .= '</ul>'."\n";
		$tabpanes .= "</div>\n";


		$result .= $tabresult;
		$result .= $tabpanes;
		$result .= "</aside>\n";
		$result .= "<div class='control-sidebar-bg' style='position: fixed; height: auto;'></div>\n";

		return $result;
	}

	protected function getRightTabPane($user, $tab, $active = false) {
		$avatarElement = $this->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 96, false, true, false);

		$isActive = ($active) ? ' active' : '';
		$tabpanes = '<div class="tab-pane'.$isActive.'" id="control-sidebar-'.$tab.'-tab">'."\n";

		if ($tab == 'settings') {
			$tabpanes .= '<ul class="control-sidebar-menu" id="go_tab_profile">
				<li>
					<div class="center-block" style="text-align: center; background: #181f23 none repeat scroll 0 0; margin: 0 10px; padding-bottom: 1px; padding-top: 10px;">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<p>'.$avatarElement.'</p>
							<p style="color:white;">'.$user->getUserName().'<br><small>'.$this->lh->translationFor("nice_to_see_you_again").'</small></p>
						</a>
					</div>
				</li>';
			if ($user->userHasBasicPermission()) {
				$tabpanes .= '<li>
					<div class="text-center"><a href="" data-toggle="modal" id="change-password-toggle" data-target="#change-password-dialog-modal">'.$this->lh->translationFor("change_password").'</a></div>
					<div class="text-center"><a href="./messages.php">'.$this->lh->translationFor("messages").'</a></div>
					<div class="text-center"><a href="./notifications.php">'.$this->lh->translationFor("notifications").'</a></div>
					<div class="text-center"><a href="./tasks.php">'.$this->lh->translationFor("tasks").'</a></div>
				</li>';
			}
			$tabpanes .= '</ul>
			  <ul class="control-sidebar-menu" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px;">
				<li>
					<div class="center-block" style="text-align: center">
						<a href="./profile.php" class="btn btn-warning"><i class="fa fa-user"></i> '.$this->lh->translationFor("my_profile").'</a>
						 &nbsp;
						<a href="./logout.php" id="cream-agent-logout" class="btn btn-warning"><i class="fa fa-sign-out"></i> '.$this->lh->translationFor("exit").'</a>
					</div>
				</li>
			  </ul>'."\n";
		}
		$tabpanes .= "</div>\n";

		return $tabpanes;
	}


	/**
	 * Generates the HTML code for a sidebar link.
	 */
	public function getSidebarItem($url, $icon, $title, $includeBadge = null, $badgeColor = "green", $listId = null) {
		$badge = (isset($includeBadge)) ? '<small class="badge pull-right bg-'.$badgeColor.'">'.$includeBadge.'</small>' : '';
		$thisID = (isset($listId)) ? ' id="'.$listId.'"' : '';
		return '<li'.$thisID.'><a href="'.$url.'"><i class="fa fa-'.$icon.'"></i> <span>'.$title.'</span>'.$badge.'</a></li>';
	}

	/** Customers */

   	/**
	 * Generates a HTML table with all customer types for the administration panel.
	 */
	public function getCustomerTypesAdminTable() {
		// generate table
		$items = array("Id", $this->lh->translationFor("name"));
		$table = $this->generateTableHeaderWithItems($items, "customerTypes", "table-bordered table-striped", true);
		if ($customerTypes = $this->db->getCustomerTypes()) {
			foreach ($customerTypes as $customerType) {
				$table .= "<tr><td>".$customerType["id"]."</td><td><span class='text'>".$customerType["description"].'
				</span><div class="tools pull-right">
				<a class="edit-customer" href="'.$customerType["id"].'" data-toggle="modal" data-target="#edit-customer-modal">
				<i class="fa fa-edit task-item"></i></a>
				<a class="delete-customer" href="'.$customerType["id"].'"><i class="fa fa-trash-o"></i></a>
				</div></td></tr>';
			}
		}
		$table .= $this->generateTableFooterWithItems($items, true);

		// generate companion JS code.
		// delete customer type
		$ec_ok = $this->reloadLocationJS();
		$ec_ko = $this->showRetrievedErrorMessageAlertJS();
		$deletephp = "./php/DeleteCustomerType.php";
		$deleteCustomerJS = $this->clickableClassActionJS("delete-customer", "customertype", "href", $deletephp, $ec_ok, $ec_ko, true);
		// edit customer type
		$idAssignment = $this->selfValueAssignmentJS("href", "customer-type-id");
		$textAssignment = $this->classValueFromParentAssignmentJS("text", "td", "newname");
		$editCustomerJS = $this->clickableFillValuesActionJS("edit-customer", array($idAssignment, $textAssignment));

		// edit customer modal form
		$modalTitle = $this->lh->translationFor("edit_customer_type");
		$modalSubtitle = $this->lh->translationFor("enter_new_name_customer_type");
		$name = $this->lh->translationFor("name");
		$newnameInput = $this->singleFormGroupWithInputGroup($this->singleFormInputElement("newname", "newname", "text required", $name));
		$hiddenidinput = $this->hiddenFormField("customer-type-id");
		$bodyInputs = $newnameInput.$hiddenidinput;
		$msgDiv = $this->emptyMessageDivWithTag("editcustomermessage");
		$modalFooter = $this->modalDismissButton("edit-customer-cancel").$this->modalSubmitButton("edit-customer-accept").$msgDiv;
		$modalForm = $this->modalFormStructure("edit-customer-modal", "edit-customer-form", $modalTitle, $modalSubtitle, $bodyInputs, $modalFooter, "user");

		// validate form javascript
		$successJS = $this->reloadLocationJS();
		$em_text = $this->lh->translationFor("error_editing_customer_name");
		$failureJS = $this->fadingInMessageJS($this->dismissableAlertWithMessage($em_text, false, true), "editcustomermessage");
		$preambleJS = $this->fadingOutMessageJS(false, "editcustomermessage");
		$javascript = $this->formPostJS("edit-customer-form", "./php/ModifyCustomerType.php", $successJS, $failureJS, $preambleJS);

		return $table."\n".$editCustomerJS."\n".$deleteCustomerJS."\n".$modalForm."\n".$javascript;
	}

	public function newCustomerTypeAdminForm() {
		// form
		$cg_text = $this->lh->translationFor("customer_group");
		$hc_text = $this->lh->translationFor("new_customer_group");
		$cr_text = $this->lh->translationFor("create");
		$inputfield = $this->singleFormInputElement("newdesc", "newdesc", "text", $cg_text);
		$formbox = $this->boxWithForm("createcustomergroup", $hc_text, $inputfield, $cr_text, CRM_UI_STYLE_DEFAULT, "creationmessage");

		// javascript form submit.
		$successJS = $this->reloadLocationJS();
		$ua_text = $this->lh->translationFor("unable_add_customer_group");
		$failureJS = $this->fadingInMessageJS($this->dismissableAlertWithMessage($ua_text, false, true), "creationmessage");
		$preambleJS = $this->fadingOutMessageJS(false, "creationmessage");
		$javascript = $this->formPostJS("createcustomergroup", "./php/CreateCustomerGroup.php", $successJS, $failureJS, $preambleJS);

		return $formbox."\n".$javascript;
	}

	/**
	 * Generates the HTML with an empty table for a list of contacts or customers.
	 */
	public function getEmptyCustomersList($customerType) {
	   // print prefix
	   $columns = $this->db->getCustomerColumnsToBeShownInCustomerList($customerType);
	   $columns[] = $this->lh->translationFor("action");
	   $hideOnMedium = array("email", "phone_number");
	   $hideOnLow = array("email", "phone_number");
	   $result = $this->generateTableHeaderWithItems($columns, "contacts", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

       // print suffix
       $result .= $this->generateTableFooterWithItems($columns, true, false, $hideOnMedium, $hideOnLow);
       return $result;
	}

	/**
	 * Generates the HTML with an empty table for a list of telephony users.
	 */
	public function getEmptyT_UsersList($customerType) {
	   // print prefix
	   $columns = array("user", "full_name", "user_level", "user_group", "active", "Action");
	   $columns[] = $this->lh->translationFor("action");
	   $hideOnMedium = array("user_level", "user_group");
	   $hideOnLow = array("user","user_level", "user_level");
	   $result = $this->generateTableHeaderWithItems($columns, "T_users", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

       // print suffix
       $result .= $this->generateTableFooterWithItems($columns, true, false, $hideOnMedium, $hideOnLow);
       return $result;
	}

	/** Tasks */

	/**
	 * Generates the HTML for a given task as a table row
	 * @param $task Array associative array representing the task object.
	 * @return String the HTML representation of the task as a row.
	 */
	private function getTaskAsIndividualRow($task) {
		// define progress and bar color
		$completed = $task["completed"];
		if ($completed < 0) $completed = 0;
		else if ($completed > 100) $completed = 100;
		$creationdate = $this->relativeTime($task["creation_date"]);
		// values dependent on completion of the task.
		$doneOrNot = $completed == 100 ? 'class="done"' : '';
		$completeActionCheckbox = $completed == 100 ? '' : '<input type="checkbox" value="" name="">';
		// modules hovers.
		$mh = \creamy\ModuleHandler::getInstance();
		$moduleTaskHoverActions = $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_TASK_LIST_HOVER, array("taskid" => $task["id"]), CRM_MODULE_MERGING_STRATEGY_APPEND);

		return '<li id="'.$task["id"].'" '.$doneOrNot.'>'.$completeActionCheckbox.'<span class="text">'.$task["description"].'</span>
				  <small class="label label-warning pull-right"><i class="fa fa-clock-o"></i> '.$creationdate.'</small>
				  <div class="tools">'.$moduleTaskHoverActions.'
				  	'.$this->hoverActionButton("edit-task-action", "edit", $task["id"], "edit-task-dialog-modal", null, "task-item").'
				  	'.$this->hoverActionButton("delete-task-action", "trash-o", $task["id"]).'
				  </div>
			 </li>';
	}

	/**
	 * Generates the HTML for a all tasks of a given user as a table row
	 * @param $userid Int id of the user to retrieve the tasks from.
	 * @return String the HTML representation of the user's tasks as a table.
	 */
	public function getCompletedTasksAsTable($userid, $userrole) {
		$tasks = $this->db->getCompletedTasks($userid);
		if (empty($tasks)) { return $this->calloutInfoMessage($this->lh->translationFor("you_dont_have_completed_tasks")); }
		else {
			$list = "<ul class=\"todo-list ui-sortable\">";
			foreach ($tasks as $task) {
				// generate row
				$taskHTML = $this->getTaskAsIndividualRow($task);
				$list = $list.$taskHTML;
			}

			$list = $list."</ul>";
	    	return $list;
		}
   	}

	/**
	 * Generates the HTML for a all tasks of a given user as a table row
	 * @param $userid Int id of the user to retrieve the tasks from.
	 * @return String the HTML representation of the user's tasks as a table.
	 */
	public function getUnfinishedTasksAsTable($userid) {
		$tasks = $this->db->getUnfinishedTasks($userid);
		if (empty($tasks)) { return $this->calloutInfoMessage($this->lh->translationFor("you_dont_have_pending_tasks")); }
		else {
			$list = "<ul class=\"todo-list ui-sortable\">";
			foreach ($tasks as $task) {
				// generate row
				$taskHTML = $this->getTaskAsIndividualRow($task);
				$list = $list.$taskHTML;
			}

			$list = $list."</ul>";
	    	return $list;
		}
   	}

	/**
	 * Returns the tasks footer action hooks for modules.
	 */
	public function getTasksActionFooter() {
		$mh = \creamy\ModuleHandler::getInstance();
		return $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_TASK_LIST_ACTION, null, CRM_MODULE_MERGING_STRATEGY_APPEND);
	}

	/**
	 * Generates the inner form code for the fields of the customer creation/edition form (the form part is not included,
	 * allowing it to be a modal or inline form). If $customerobj is specified, the values are loaded from it.
	 * @param Array $customerobj an associative array with the current customer values, or null.
	 * @return String a HTML generated code with the form fields without the form, ready to be wrapped in a form by using
	 * any of the form generation methods of this class.
	 */
	//edited
	public function customerFieldsForForm($customerobj = null, $customerType = null, $customerid = null) {
		// name
		$ph = $this->lh->translationFor("First Name").' ('.$this->lh->translationFor("mandatory").')';
		$vl = isset($customerobj["first_name"]) ? $customerobj["first_name"] : null;
		$fname_f = $this->singleInputGroupWithContent($this->singleFormInputElement("fname", "fname", "text", $ph, $vl, "user", "required"));

		$ph = $this->lh->translationFor("Middle Initial").' ('.$this->lh->translationFor("optional").')';
		$vl = isset($customerobj["middle_initial"]) ? $customerobj["middle_initial"] : null;
		$mi_f = $this->singleInputGroupWithContent($this->singleFormInputElement("mi", "mi", "text", $ph, $vl, "user"));

		$ph = $this->lh->translationFor("Last Name").' ('.$this->lh->translationFor("mandatory").')';
		$vl = isset($customerobj["last_name"]) ? $customerobj["last_name"] : null;
		$lname_f = $this->singleInputGroupWithContent($this->singleFormInputElement("lname", "lname", "text", $ph, $vl, "user", "required"));

		$name_row = $this->rowWithVariableContents(array("6", "6", "6"), array($fname_f, $mi_f, $lname_f));
		$name_f = $this->singleFormGroupWrapper($name_row);

		// phone & alt phone
		$ph = $this->lh->translationFor("Phone Number").' ('.$this->lh->translationFor("mandatory").')';
		$vl = isset($customerobj["phone_number"]) ? $customerobj["phone_number"]: null;
		$phone_f = $this->singleInputGroupWithContent($this->singleFormInputElement("phone", "phone", "text", $ph, $vl, "phone", "required"));

		$ph = $this->lh->translationFor("Alternate Phone Number");
		$vl = isset($customerobj["alt_phone"]) ? $customerobj["alt_phone"]: null;
		$altphone_f = $this->singleInputGroupWithContent($this->singleFormInputElement("alt_phone", "alt_phone", "text", $ph, $vl, "alt_phone", "required"));

		$phone_alt_row = $this->rowWithVariableContents(array("6", "6"), array($phone_f, $altphone_f));
		$phone_alt_field = $this->singleFormGroupWrapper($phone_alt_row);

		// email
		$ph = $this->lh->translationFor("email").' ('.$this->lh->translationFor("mandatory").')';
		$vl = isset($customerobj["email"]) ? $customerobj["email"] : null;
		$email_f = $this->singleFormGroupWithInputGroup($this->singleFormInputElement("email", "email", "email", $ph, $vl, "envelope", "required"));

		// address 1 2 3
		$ph = $this->lh->translationFor("Address 1").' ('.$this->lh->translationFor("mandatory").')';
		$vl = isset($customerobj["address1"]) ? $customerobj["address1"] : null;
		$address1_f = $this->singleFormGroupWithInputGroup($this->singleFormInputElement("address1", "address1", "text", $ph, $vl, "map-marker", "required"));

		$ph = $this->lh->translationFor("Address 2");
		$vl = isset($customerobj["address2"]) ? $customerobj["address2"] : null;
		$address2_f = $this->singleFormGroupWithInputGroup($this->singleFormInputElement("address2", "address2", "text", $ph, $vl, "map-marker"));

		$ph = $this->lh->translationFor("Address 3");
		$vl = isset($customerobj["address3"]) ? $customerobj["address3"] : null;
		$address3_f = $this->singleFormGroupWithInputGroup($this->singleFormInputElement("address3", "address3", "text", $ph, $vl, "map-marker"));

		// city & state & province
		$ph = $this->lh->translationFor("city");
		$vl = isset($customerobj["city"]) ? $customerobj["city"] : null;
		$city_f = $this->singleInputGroupWithContent($this->singleFormInputElement("city", "city", "text", $ph, $vl, "map-marker"));

		$ph = $this->lh->translationFor("state");
		$vl = isset($customerobj["state"]) ? $customerobj["state"] : null;
		$state_f = $this->singleInputGroupWithContent($this->singleFormInputElement("state", "state", "text", $ph, $vl, "map-marker"));

		$ph = $this->lh->translationFor("Province");
		$vl = isset($customerobj["province"]) ? $customerobj["province"] : null;
		$province_f = $this->singleInputGroupWithContent($this->singleFormInputElement("province", "province", "text", $ph, $vl, "map-marker"));

		$c_s_p_row = $this->rowWithVariableContents(array("6", "6", "6"), array($city_f, $state_f, $province_f));
		$c_s_p_field = $this->singleFormGroupWrapper($c_s_p_row);

		// zip code and country
		$ph = $this->lh->translationFor("Postal Code");
		$vl = isset($customerobj["postal_code"]) ? $customerobj["postal_code"] : null;
		$postal_f = $this->singleInputGroupWithContent($this->singleFormInputElement("postal_code", "postal_code", "text", $ph, $vl, "map-marker"));
		$ph = $this->lh->translationFor("country");
		$vl = isset($customerobj["country"]) ? $customerobj["country"] : null;
		$country_f = $this->singleInputGroupWithContent($this->singleFormInputElement("country", "country", "text", $ph, $vl, "map-marker"));

		$c_and_z_row = $this->rowWithVariableContents(array("6", "6"), array($postal_f, $country_f));
		$c_and_z_field = $this->singleFormGroupWrapper($c_and_z_row);

		// textarea
		$ph = $this->lh->translationFor("notes");
		$vl = isset($customerobj["comments"]) ? $customerobj["comments"] : null;
		$notes_f = $this->singleFormGroupWithInputGroup($this->singleFormTextareaElement("comments", "comments", $ph, $vl, "file-text-o"));

		// gender
        $currentGender = -1;
        if (isset($customerobj["gender"])) {
            $currentGender = $customerobj["gender"];
            if ($currentGender < 0 || $currentGender > 1) $currentGender = -1;
        }
        $genders = array("-1" => "choose_an_option", "0" => "female", "1" => "male");
		$gender_f = $this->singleFormGroupWithSelect($this->lh->translationFor("gender"), "gender", "gender", $genders, $currentGender, true);

		// birthdate
		//var_dump($customerobj["date_of_birth"]);
		$dateAsDMY = "";
        if (isset($customerobj["date_of_birth"])) {
            $time = strtotime($customerobj["date_of_birth"]);
            $dateAsDMY = date('d/m/Y', $time);
           // var_dump($dateAsDMY);

	}

		$md = $this->maskedDateInputElement("date_of_birth", "date_of_birth", "dd/mm/yyyy", $dateAsDMY, "calendar", true);
		$birth_f = $this->singleFormGroupWithInputGroup($md, $this->lh->translationFor("birthdate"));
		//$birth_f = $this->singleFormGroupWithInputGroup("date_of_birth", "date_of_birth", $this->lh->translationFor("birthdate"), $dateAsDMY, "calendar", true);
		// hidden fields: customer type and id
		$hidden = "";
		$hidden .= $this->hiddenFormField("customer_type", $customerType);
		$hidden .= $this->hiddenFormField("customerid", $customerid);

		// join all fields
		$formcontent = $name_f.$email_f.$phone_alt_field.$address1_f.$address2_f.$address3_f.$c_s_p_field.$c_and_z_field.$notes_f.$gender_f.$birth_f.$dnsm_f.$hidden;
		return $formcontent;
	}

	/** Messages */

	/**
	 * Generates the list of users $myuserid can send message to or assign a task to as a HTML form SELECT.
	 * @param Int $myuserid 		id of the user that wants to send messages, all other user's ids will be returned.
	 * @param Boolean $includeSelf 	if true, $myuserid will appear listed in the options. If false (default), $myuserid will not be included in the options. If this parameter is set to true, the default option will be the $myuserid
	 * @param String $customMessage The custom message to ask for a selection in the SELECT, default is "send this message to...".
	 * @param String $selectedUser	If defined, this user will appear as selected by default.
	 * @return the list of users $myuserid can send mail to (all valid users except $myuserid unless $includeSelf==true) as a HTML form SELECT.
	 */
	public function generateSendToUserSelect($myuserid, $includeSelf = false, $customMessage = NULL, $selectedUser = null) {
		// perform query of users.
		if (empty($customMessage)) $customMessage = $this->lh->translationFor("send_this_message_to");
		$usersarray = $this->db->getAllEnabledUsers();

		// iterate through all users and generate the select
		$response = '<select class="form-control required select2" id="touserid" name="touserid"><option value="0">'.$customMessage.'</option>';
		foreach ($usersarray as $userobj) {
			// don't include ourselves.
			//if ($userobj["id"] != $myuserid) {
			if ($userobj["user_id"] != $myuserid) {
				$selectedUserCode = "";
				//if (isset($selectedUser) && ($selectedUser == $userobj["id"])) { $selectedUserCode = 'selected="true"'; }
				//$response = $response.'<option value="'.$userobj["id"].'" '.$selectedUserCode.' >'.$userobj["name"].'</option>';
				if (isset($selectedUser) && ($selectedUser == $userobj["user_id"])) { $selectedUserCode = 'selected="true"'; }
				$response .= '<option value="'.$userobj["user_id"].'" '.$selectedUserCode.' >'.$userobj["full_name"].' ('.$userobj["user"].')</option>';
			} else if ($includeSelf === true) { // assign to myself by default unless another $selectedUser has been specified.
				$selfSelectedCode = isset($selectedUser) ? "" : 'selected="true"';
				//$response = $response.'<option value="'.$userobj["id"].'" '.$selfSelectedCode.'>'.$this->lh->translationFor("myself").'</option>';
				$response .= '<option value="'.$userobj["user_id"].'" '.$selfSelectedCode.'>'.$this->lh->translationFor("myself").'</option>';
			}
		}
		$response .= '</select>';
		return $response;
	}

	/**
	 * Generates the HTML of the given messages as a HTML table, from a table array
	 * @param Array $messages the list of messages.
	 * @return the HTML code with the list of messages as a HTML table.
	 */
	private function getMessageListAsTable($messages, $folder) {
		$columns = array("", "favorite", "name", "subject", "attachment", "date");
		$table = $this->generateTableHeaderWithItems($columns, "messagestable", "table-hover table-striped mailbox table-mailbox", true, true);
		$user = \creamy\CreamyUser::currentUser();
		foreach ($messages as $message) {
			$from = $this->db->getDataForUser($message['user_from']);
			if ($from['user_id'] == $user->getUserId()) {
			    $from_user = "me";
			}else{
			    $from_user = $from['user'];
			}
			if ($message["message_read"] == 0) $table .= '<tr class="unread">';
			else $table .= '<tr>';
			
			$attachments = $this->db->getMessageAttachments($message['id'], $folder);
			$showPaperClip = (!isset($attachments) || count($attachments) < 1) ? '' : '<i class="fa fa-paperclip" title="Has attachment"></i>';

			// variables and html text depending on the message
			$favouriteHTML = "-o"; if ($message["favorite"] == 1) $favouriteHTML = "";
			$messageLink = '<a href="readmail.php?folder='.$folder.'&message_id='.$message["id"].'">';

			$table .= '<td style="width: 5%; text-align: center;"><input type="checkbox" class="message-selection-checkbox" value="'.$message["id"].'"/></td>';
			$table .= '<td style="width: 5%; text-align: center;" class="mailbox-star"><i class="fa fa-star'.$favouriteHTML.'" id="'.$message["id"].'"></i></td>';
			// $table .= '<td class="mailbox-name">'.$messageLink.(isset($message["remote_user"]) ? $message["remote_user"] : $this->lh->translationFor("unknown")).'</a></td>';
			$table .= '<td class="mailbox-name" style="width: 20%; white-space: nowrap;">'.$messageLink.(isset($from_user) ? $from_user: $this->lh->translationFor("unknown")).'</a></td>';
			$table .= '<td class="mailbox-subject">'.$message["subject"].'</td>';
			$table .= '<td class="mailbox-attachment" style="width: 5%; text-align: center;">'.$showPaperClip.'</td>'; //<i class="fa fa-paperclip"></i></td>';
			$table .= '<td class="mailbox-date" style="width: 15%; white-space: nowrap; text-align: right; padding-right: 20px;">'.$this->relativeTime($message["date"]).'</td>';
			$table .= '</tr>';
		}
		$table .= $this->generateTableFooterWithItems($columns, true, true);
		return $table;
	}

	/**
	 * Generates the HTML of the given calls as a HTML table, from a table array
	 * @param Array $calls the list of calls.
	 * @return the HTML code with the list of calls as a HTML table.
	 */
	private function getCallListAsTable($calls, $folder) {
		$columns = array("", "name", "duration", "date", "playback");
		$table = $this->generateTableHeaderWithItems($columns, "callstable", "table-hover table-striped calls table-calls", true, true);
		foreach ($calls as $call) {
			//if ($call["message_read"] == 0) $table .= '<tr class="unread">';
			//else
			$table .= '<tr>';

			// variables and html text depending on the message

			$table .= '<td><input type="checkbox" class="message-selection-checkbox" value=""/></td>';
			$table .= '<td class="mailbox-name">'.$messageLink.(isset($call["user"]) ? $call["user"] : $this->lh->translationFor("unknown")).'</a></td>';
			$table .= '<td class="mailbox-duration">duration here</td>';
			$table .= '<td class="mailbox-date">'.$this->relativeTime($call["date"]).'</td>'; //<i class="fa fa-paperclip"></i></td>';
			$table .= '<td class="mailbox-playback "><a href="#"><span class="fa fa-play"></span></a></td>';
			$table .= '</tr>';
		}
		$table .= $this->generateTableFooterWithItems($columns, true, true);
		return $table;
	}

	/**
	 * Generates the HTML for a mailbox button.
	 */
	public function generateMailBoxButton($buttonClass, $icon, $param, $value) {
		return '<button class="btn btn-default btn-sm '.$buttonClass.'" '.$param.'="'.$value.'"><i class="fa fa-'.$icon.'"></i></button>';
	}

	/**
	 * Generates the button group for the mailbox messages table
	 */
	public function getMailboxButtons($folder, $canDelete = true, $showModuleButtons = true) {
		// send to trash or recover from trash ?
		if ($folder == MESSAGES_GET_DELETED_MESSAGES) {
			$trashOrRecover = '<button class="btn btn-default btn-sm messages-restore-message"><i class="fa fa-undo"></i></button>';
		} else {
			$trashOrRecover = '<button class="btn btn-default btn-sm messages-send-to-junk"><i class="fa fa-trash-o"></i></button>';
		}

		// basic buttons
		$buttons = '<button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></button>
		<div class="btn-group">
		  <button class="btn btn-default btn-sm messages-mark-as-favorite"><i class="fa fa-star"></i></button>
		  <button class="btn btn-default btn-sm messages-mark-as-read"><i class="fa fa-eye"></i></button>
		  <button class="btn btn-default btn-sm messages-mark-as-unread"><i class="fa fa-eye-slash"></i></button>';
		if ($canDelete) {
			$buttons .= $trashOrRecover.'
			  <button class="btn btn-default btn-sm messages-delete-permanently"><i class="fa fa-times"></i></button>';
		}
		// module buttons
		if ($showModuleButtons) {
			$mh = \creamy\ModuleHandler::getInstance();
			$buttons .= $mh->applyHookOnActiveModules(CRM_MODULE_HOOK_MESSAGE_LIST_FOOTER, array("folder" => $folder), CRM_MODULE_MERGING_STRATEGY_APPEND);
		}
		// chevrons
		$buttons .= '</div><div class="pull-right"><div class="btn-group">
			<button class="btn btn-default btn-sm mailbox-prev"><i class="fa fa-chevron-left"></i></button>
			<button class="btn btn-default btn-sm mailbox-next"><i class="fa fa-chevron-right"></i></button>
		</div></div>';

		return $buttons;
	}

	/**
	 * Generates the button group for the call logs list table
	 */
	public function getCallButtons($folder) {
		// send to trash or recover from trash ?
		// if ($folder == MESSAGES_GET_DELETED_MESSAGES) {
		// 	$trashOrRecover = '<button class="btn btn-default btn-sm messages-restore-message"><i class="fa fa-undo"></i></button>';
		// } else {
		// 	$trashOrRecover = '<button class="btn btn-default btn-sm messages-send-to-junk"><i class="fa fa-trash-o"></i></button>';
		// }

		// basic buttons
		 $buttons = '<button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></button>
		 <div class="btn-group">
		   <button class="btn btn-default btn-sm messages-mark-as-favorite"><i class="fa fa-star"></i></button>
		   <button class="btn btn-default btn-sm messages-mark-as-read"><i class="fa fa-eye"></i></button>
		   <button class="btn btn-default btn-sm messages-mark-as-unread"><i class="fa fa-eye-slash"></i></button>
		   '.$trashOrRecover.'
		   <button class="btn btn-default btn-sm messages-delete-permanently"><i class="fa fa-times"></i></button>';
		// chevrons
		$buttons .= '</div><div class="pull-right"><div class="btn-group">
			<button class="btn btn-default btn-sm mailbox-prev"><i class="fa fa-chevron-left"></i></button>
			<button class="btn btn-default btn-sm mailbox-next"><i class="fa fa-chevron-right"></i></button>
		</div></div>';

		return $buttons;
	}

	/**
	 * Generates a HTML table with all inbox messages of a user.
	 * @param Int $userid user to retrieve the messages from
	 */
	public function getInboxMessagesAsTable($userid) {
		$messages = $this->db->getMessagesOfType($userid, MESSAGES_GET_INBOX_MESSAGES);
		if ($messages == NULL) return $this->calloutInfoMessage($this->lh->translationFor("unable_get_messages"));
		else return $this->getMessageListAsTable($messages);
	}

	/**
	 * Generates a HTML table with the unread messages of the user.
	 * @param Int $userid user to retrieve the messages from
	 */
	public function getUnreadMessagesAsTable($userid) {
		$messages = $this->db->getMessagesOfType($userid, MESSAGES_GET_UNREAD_MESSAGES);
		if ($messages == NULL) return $this->calloutInfoMessage($this->lh->translationFor("no_messages_in_list"));
		else return $this->getMessageListAsTable($messages);
	}

	/**
	 * Generates a HTML table with with the junk messages of a user.
	 * @param Int $userid user to retrieve the messages from
	 */
	public function getJunkMessagesAsTable($userid) {
		$messages = $this->db->getMessagesOfType($userid, MESSAGES_GET_DELETED_MESSAGES);
		if ($messages == NULL) return $this->calloutInfoMessage($this->lh->translationFor("no_messages_in_list"));
		else return $this->getMessageListAsTable($messages);
	}

	/**
	 * Generates a HTML table with the sent messages of a user.
	 * @param Int $userid user to retrieve the messages from
	 */
	public function getSentMessagesAsTable($userid) {
		$messages = $this->db->getMessagesOfType($userid, MESSAGES_GET_SENT_MESSAGES);
		if ($messages == NULL) return $this->calloutInfoMessage($this->lh->translationFor("no_messages_in_list"));
		else return $this->getMessageListAsTable($messages);
	}

	/**
	 * Generates a HTML table with the favourite messages of a user.
	 * @param Int $userid user to retrieve the messages from
	 */
	public function getFavoriteMessagesAsTable($userid) {
		$messages = $this->db->getMessagesOfType($userid, MESSAGES_GET_FAVORITE_MESSAGES);
		if ($messages == NULL) return $this->calloutInfoMessage($this->lh->translationFor("no_messages_in_list"));
		else return $this->getMessageListAsTable($messages);
	}

	/**
	 * Generates a HTML table with the messages from given folder for a user.
	 * @param Int $userid user to retrieve the messages from
	 * @param Int $folder folder to retrieve the messages from
	 */
	public function getMessagesFromFolderAsTable($userid, $folder) {
		$messages = $this->db->getMessagesOfType($userid, $folder);
		if ($messages == NULL) return $this->calloutInfoMessage($this->lh->translationFor("no_messages_in_list"));
		else return $this->getMessageListAsTable($messages, $folder);
	}

	/**
	 * Generates a HTML table with the calls from given folder for a user.
	 * @param Int $userid user to retrieve the calls from
	 * @param Int $folder folder to retrieve the calls from
	 */
	public function getCallFromFolderAsTable($userid, $folder) {
		// $calls = $this->db->getCallsOfType($userid, $folder);
		$calls = $this->db->getMessagesOfType($userid, $folder);
		if ($calls == NULL) return $this->calloutInfoCall($this->lh->translationFor("no_messages_in_list"));
		else return $this->getCallListAsTable($calls, $folder);
	}

	/**
	 * Generates the HTML with the list of message folders as <li> items.
	 * @param $activefolder String current active folder the user is in.
	 * @return String the HTML with the list of message folders as <li> items.
	 */
	public function getMessageFoldersAsList($activefolder, $canDelete = true) {
		require_once('Session.php');
		$user = \creamy\CreamyUser::currentUser();
		$isHidden = (!$canDelete) ? 'hidden' : '';
		// info for active folder and unread messages
        $unreadMessages = $this->db->getUnreadMessagesNumber($user->getUserId());
        $aInbox = $activefolder == MESSAGES_GET_INBOX_MESSAGES ? 'class="active"' : '';
        $aSent = $activefolder == MESSAGES_GET_SENT_MESSAGES ? 'class="active"' : '';
        $aFav = $activefolder == MESSAGES_GET_FAVORITE_MESSAGES ? 'class="active"' : '';
        $aDel = $activefolder == MESSAGES_GET_DELETED_MESSAGES ? 'class="active '.$isHidden.'"' : 'class="'.$isHidden.'"';

        return '<ul class="nav nav-pills nav-stacked">
			<li '.$aInbox.'><a href="messages.php?folder=0">
				<i class="fa fa-inbox"></i> '.$this->lh->translationFor("inbox").'
				<span class="label label-primary pull-right">'.$unreadMessages.'</span></a>
			</li>
			<li '.$aSent.'><a href="messages.php?folder=3"><i class="fa fa-envelope-o"></i> '.$this->lh->translationFor("sent").'</a></li>
			<li '.$aFav.'><a href="messages.php?folder=4"><i class="fa fa-star"></i> '.$this->lh->translationFor("favorites").'</a></li>
			<li '.$aDel.'><a href="messages.php?folder=2"><i class="fa fa-trash-o"></i> '.$this->lh->translationFor("trash").'</a></li>
		</ul>';
	}

	/**
	 * Generates the HTML with the list of calls folders as <li> items.
	 * @param $activefolder String current active folder the user is in.
	 * @return String the HTML with the list of calls folders as <li> items.
	 */
	public function getCallFoldersAsList($activefolder) {
		require_once('Session.php');
		$user = \creamy\CreamyUser::currentUser();
		$aInbound = $activefolder == CALLS_GET_INBOUND_CALLS ? 'class="active"' : '';
		$aOutbound = $activefolder == CALLS_GET_OUTBOUND_CALLS ? 'class="active"' : '';

		return '<ul class="nav nav-pills nav-stacked">
			<li '.$aInbound.'><a href="calls.php?folder=0">
				<i class="fa fa-download"></i> Inbound</a>
			</li>
			<li '.$aOutbound.'><a href="calls.php?folder=1"><i class="fa fa-upload"></i> Outbound</a></li>
			</ul>';
	}

	/**
	 * Generates the HTML code for showing the attachments of a given message.
	 * @param Int $messageid 	identifier for the message.
	 * @param Int $folderid 	identifier for the folder.
	 * @param Int $userid 		identifier for the user.
	 * @return String The HTML code containing the code for the attachments.
	 */
	public function attachmentsSectionForMessage($messageid, $folderid, $fromAgent = false) {
		$attachments = $this->db->getMessageAttachments($messageid, $folderid);
		if (!isset($attachments) || count($attachments) < 1) { return ""; }

		$code = '<div class="box-footer non-printable"><ul class="mailbox-attachments clearfix">';
		foreach ($attachments as $attachment) {
			// icon/image
			$uploadPath = "";
			if ($fromAgent) {
				$uploadPath = "../../";
			}
			
			$icon = $this->getFiletypeIconForFile($uploadPath . $attachment["filepath"]);
			if ($icon != CRM_FILETYPE_IMAGE) {
				$hasImageCode = "";
				$iconCode = '<i class="fa fa-'.$icon.'"></i>';
				$attIcon = "paperclip";
			} else {
				$hasImageCode = "has-img";
					$thisPath = '//'.$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF']).'/'.$attachment["filepath"];
				if ($fromAgent) {
					$attachmentPath = str_replace('/modules/GOagent', '', $thisPath);
				} else {
					$attachmentPath = $thisPath;
				}
				$iconCode = '<img src="'.$attachmentPath.'" alt="'.$this->lh->translationFor("attachment").'"/>';
				$attIcon = "camera";
			}
			// code
			$basename = basename($attachment["filepath"]);
			$code .= '<li><span class="mailbox-attachment-icon '.$hasImageCode.'">'.$iconCode.'</span>
                      <div class="mailbox-attachment-info">
                        <a href="'.$attachment["filepath"].'" target="_blank" class="mailbox-attachment-name">
                        <i class="fa fa-'.$attIcon.'"></i> '.$basename.'</a>
                        <span class="mailbox-attachment-size">
                          '.$attachment["filesize"].'
                          <a href="'.$attachment["filepath"].'" target="_blank" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                      </div>
                    </li>';
		}
		$code .= '</ul></div>';
		return $code;
	}

	/**
	 * returns the filetype icon for a given file. This filetype can be used added to fa-
	 * for the icon representation of a file.
	 */
	public function getFiletypeIconForFile($filename) {
		$mimetype = mime_content_type($filename);
		if (\creamy\CRMUtils::startsWith($mimetype, "image/")) { return CRM_FILETYPE_IMAGE; }
		else if ($mimetype == "application/pdf") { return CRM_FILETYPE_PDF; }
		else if ($mimetype == "application/zip") { return CRM_FILETYPE_ZIP; }
		else if ($mimetype == "text/plain") { return CRM_FILETYPE_TXT; }
		else if ($mimetype == "text/html") { return CRM_FILETYPE_HTML; }
		else if (\creamy\CRMUtils::startsWith($mimetype, "video/")) { return CRM_FILETYPE_VIDEO; }
		else { return CRM_FILETYPE_UNKNOWN; }
	}

	/** Events */

	/**
	 * Returns a time selection for an event. It includes time periods of 15 min.
	 * Default value is "All day" with value 0. All other values contain the time
	 * string in HH:mm format.
	 */
	public function eventTimeSelect() {
		// build options
		$options = array("all_day" => $this->lh->translationFor("all_day"));
		for ($i = 0; $i < 24; $i++) {
			for ($j = 0; $j < 60; $j += 15) {
				$hour = sprintf("%02d", $i);
				$minute = sprintf("%02d", $j);
				$options["$hour:$minute"] = "$hour:$minute";
			}
		}

		// return select.
		return $this->singleFormGroupWithSelect(
		    null, 									// label
		    "time", 								// id
		    "time", 								// name
		    $options, 								// options
		    "all_day",								// selected option
		    false);									// needs translation
	}

	/**
	 * Returns the list of unassigned events as list.
	 */
	public function getUnassignedEventsList($userid) {
		$result = "<div id='external-events'>";
		$events = $this->db->getUnassignedEventsForUser($userid);
		foreach ($events as $event) {
			$urlCode = empty($event["url"]) ? '' : ' event-url="'.$event["url"].'" ';
			$result .= "<div event-id='".$event["id"]."' class='external-event bg-".$this->creamyColorForHexValue($event["color"])."' $urlCode>".$event["title"]."</div>";
		}
		$result .= "</div>";
		return $result;
	}

	//added for editing events
	public function getAssignedEventsAsTable($userid) {
		$events = $this->db->editAssignedEventsForUser($userid);
		if (empty($events)) { return $this->calloutInfoMessage($this->lh->translationFor("you_dont_have_pending_events")); }
		else {
			$list = "<ul class=\"todo-list ui-sortable\">";
			foreach ($events as $event) {
				// generate row
				$eventsHTML =
				'<li id="'.$event["id"].'" '.$doneOrNot.'><span class="text">'.$event["title"].'</span>
					<div class="tools">'.$moduleEventsHoverActions.'
				  		'.$this->hoverActionButton("edit-events-action", "edit", $event["id"], "edit-events-dialog-modal", null, "events-item").'
					</div>
			 	</li>';
				$list = $list.$eventsHTML;
			}

			$list = $list."</ul>";
	    	return $list;
		}
   	}

	/**
	 * Returns the list of date-assigned events as javascript full calendar list.
	 */
	public function getAssignedEventsListForCalendar($userid) {
		$result = "events: [ ";
		$events = $this->db->getAssignedEventsForUser($userid);
		foreach ($events as $event) {
			// id
			$eventId = $event["id"];
			// title
			$title = str_replace("'", "\\'", $event["title"]);
			// end and start date.
			$startDate = strtotime($event["start_date"]);
			if (empty($event["end_date"])) { continue; } // no end date? no way!
			$endDate = strtotime($event["end_date"]);
			// start date components
			$comp = getdate($startDate);
			$y = $comp["year"]; $m = $comp["mon"]-1; $d = $comp["mday"]; $H = $comp["hours"]; $M = $comp["minutes"];
			$startCode = ", start: new Date($y, $m, $d, $H, $M)";
			$comp = getdate($endDate);
			$y = $comp["year"]; $m = $comp["mon"]-1; $d = $comp["mday"]; $H = $comp["hours"]; $M = $comp["minutes"];
			$endCode = ", end: new Date($y, $m, $d, $H, $M)";
			// all day?
			$allDayCode = ", allDay: ".(($event["all_day"]) ? "true" : "false");
			// url
			if (isset($event["url"])) { $urlCode = ", url: '".$event["url"]."'"; }
			else $urlCode = "";
			// color
			$color = $event["color"];
			$colorCode = ", backgroundColor: '$color', borderColor: '$color'";

			$result .= "{ id: $eventId, title: '$title' $startCode $endCode $allDayCode $urlCode $colorCode},";
		}
		$result = rtrim($result, ",");
		$result .= "]";
		return $result;
	}

	public function getTimezoneForCalendar() {
		$timezone = $this->db->getTimezoneSetting();
		return "timezone: '$timezone', timezoneParam: '$timezone'";
	}

	/** Notifications */

	/**
	 * Returns the HTML font-awesome icon for notifications of certain type.
	 * @param $type String the type of notification.
	 * @return String the string with the font-awesome icon for this notification type.
	 */
	public function notificationIconForNotificationType($type) {
		if ($type == "contact") return "user";
		else if ($type == "event") return "calendar-o";
		else if ($type == "message") return "envelope";
		else return "calendar-o";
	}

	/**
	 * Returns the HTML UI color for notifications of certain type.
	 * @param $type String the type of notification.
	 * @return String the string with the UI color for this notification type.
	 */
	public function notificationColorForNotificationType($type) {
		if ($type == "contact") return "aqua";
		else if ($type == "message") return "blue";
		else return "yellow";
	}

	/**
	 * Returns the HTML action button text for notifications of certain type.
	 * @param $type String the type of notification.
	 * @return String the string with the action button text for this notification type.
	 */
	public function actionButtonTextForNotificationType($type) {
		if ($type == "contact") return $this->lh->translationFor("see_customer");
		else if ($type == "message") return $this->lh->translationFor("read_message");
		else if ($type == "event") return $this->lh->translationFor("see_details");
		else return $this->lh->translationFor("see_more");
	}

	/**
	 * Returns the HTML header text for notifications of certain type associated to certain action.
	 * @param $type String the type of notification.
	 * @param $action String a URL with the action to perform for this notification.
	 * @return String the string with the header text for this notification type.
	 */
	public function headerTextForNotificationType($type, $action) {
		if ($type == "contact")
		return empty($action) ? $this->lh->translationFor("you_have_a_new")." ".$this->lh->translationFor("contact") : $this->lh->translationFor("you_have_a_new")." <a href=".$action.">".$this->lh->translationFor("contact")."</a>";
		else if ($type == "message")
			return empty($action) ? $this->lh->translationFor("you_have_a_new")." ".$this->lh->translationFor("message") : $this->lh->translationFor("you_have_a_new")." <a href=".$action.">".$this->lh->translationFor("message")."</a>";

		return empty($action) ? $this->lh->translationFor("you_have_a_new")." ".$this->lh->translationFor("event") : $this->lh->translationFor("you_have_a_new")." <a href=".$action.">".$this->lh->translationFor("event")."</a>";
	}

	/**
	 * Generates the HTML code for a timeline item action button.
	 * @param String $url 		the url to launch when pushing the button.
	 * @param String $title 	title for the button.
	 * @param String $style		Style for the button, one of CRM_UI_STYLE_*
	 * @return String			The HTML for the button to include in the timeline item.
	 */
	public function timelineItemActionButton($url, $title, $style = CRM_UI_STYLE_DEFAULT) {
		return '<div class="timeline-footer"><a class="btn btn-'.$style.' btn-xs" href="'.$url.'">'.$title.'</a></div>';
	}


	/**
	 * Generates the HTML code for a timeline item with the given data.
	 * @param String $title 		Title for the timeline item
	 * @param String $content		Main content (text) for the timeline item.
	 * @param String $date			Recognizable date for strtotime (see http://php.net/manual/es/datetime.formats.date.php).
	 * @param String $url			If set, an action for the notification, use
	 * @param String $buttonTitle	Title for the button (if URL set).
	 * @param String $icon			Icon for the notification item (default calendar).
	 * @param String $buttonStyle	Style for the button, one of CRM_UI_STYLE_*
	 * @param String $badgeColor	Color for the badge notification bubble (default yellow).
	 * @return The HTML with the code of the timeline notification item to insert in the timeline list.
	 */
	public function timelineItemWithData($title, $content, $date, $url = null, $buttonTitle, $icon = "calendar-o", $buttonStyle = CRM_UI_STYLE_DEFAULT, $badgeColor = "yellow") {
		// parameters
		$relativeTime = $this->relativeTime($date, 1);
		$actionHTML = isset($url) ? $this->timelineItemActionButton($url, $buttonTitle, $buttonStyle) : "";
		// return code.
		return '<li><i class="fa fa-'.$icon.' bg-'.$badgeColor.'"></i>
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> '.$relativeTime.'</span>
                <h3 class="timeline-header no-border">'.$title.'</h3>
				<div class="timeline-body">'.$content.'</div>
                '.$actionHTML.'
            </div></li>';
	}

	/**
	 * Generates the HTML for the beginning of the timeline.
	 */
	protected function timelineStart($message, $includeInitialTimelineStructure = true, $color = "green") {
		$tlCode = $includeInitialTimelineStructure ? '<ul class="timeline">' : '';
		return $tlCode.'<li class="time-label"><span class="bg-'.$color.'">'.$message.'</span></li>';
	}

	/**
	 * Generates the HTML for a intermediate label in the timeline (used to
	 */
	public function timelineIntermediateLabel($message, $color = "purple") {
		return '<li class="time-label"><span class="bg-'.$color.'">'.$message.'</span></li>';
	}

	/**
	 * Generates the HTML for the timelabel ending section.
	 */
	public function timelineEnd($endingIcon = "clock-o") {
		return '<li><i class="fa fa-'.$endingIcon.'"></i></li></ul>';
	}

	/**
	 * Generates the HTML for an simple timeline item without icon, just a message.
	 * @param String $message the message for the timeline item.
	 */
	public function timelineItemWithMessage($title, $message, $style = CRM_UI_STYLE_INFO) {
		$content = $this->calloutMessageWithTitle($title, $message, $style);
		return '<li><div class="timeline-item">'.$content.'</div></li>';
	}

	/**
	 * Generates the HTML code for the given notification.
	 * @param $notification Array an associative array object containing the notification data.
	 * @return String a HTML representation of the notification.
	 */
	public function timelineItemForNotification($notification) {
		$type = $notification["type"];
		$action = isset($notification["action"]) ? $notification["action"]: NULL;
		$date = $notification["date"];
		$content = $notification["text"];

		$color = $this->notificationColorForNotificationType($type);
		$icon = $this->notificationIconForNotificationType($type);
		$title = $this->headerTextForNotificationType($type, $action);
		$buttonTitle = $this->actionButtonTextForNotificationType($type);

		return $this->timelineItemWithData($title, $content, $date, $action, $buttonTitle, $icon, CRM_UI_STYLE_SUCCESS, $color);
	}

	/**
	 * Generates the HTML code for the given event.
	 * @param event Array an associative array object containing the event data.
	 * @return String a HTML representation of the notification.
	 */
	public function timelineItemForEvent($event) {
		$type = "event";
		$action = isset($event["url"]) ? $event["url"]: "events.php?initial_date=".urlencode($event["start_date"]);
		$date = $event["start_date"];
		$content = $this->lh->translationFor("event_programmed_today").$event["title"];

		$color = $this->creamyColorForHexValue($event["color"]);
		$icon = $this->notificationIconForNotificationType($type);
		$title = $this->headerTextForNotificationType($type, $action);
		$buttonTitle = $this->actionButtonTextForNotificationType($type);
		return $this->timelineItemWithData($title, $content, $date, $action, $buttonTitle, $icon, CRM_UI_STYLE_DEFAULT, $color);
	}

	/**
	 * Generates the HTML code for the given notification.
	 * @param $notification Array an associative array object containing the notification data.
	 * @return String a HTML representation of the notification.
	 */
	public function getNotificationsAsTimeLine($userid) {
		$locale = $this->lh->getLanguageHandlerLocale();
		if (isset($locale)) { setlocale(LC_ALL, $locale); }
		$todayAsDate = strftime("%x");
		$todayAsText = $this->lh->translationFor(CRM_NOTIFICATION_PERIOD_TODAY)." ($todayAsDate)";

		// today
		$timeline = $this->timelineStart($todayAsText);
		// notifications for today
		$notifications = $this->db->getTodayNotifications($userid);
		// events for today
		$events = $this->db->getEventsForToday($userid);
		// module notifications for today
		$mh = \creamy\ModuleHandler::getInstance();
		$modNots = $mh->applyHookOnActiveModules(
			CRM_MODULE_HOOK_NOTIFICATIONS,
			array(CRM_NOTIFICATION_PERIOD => CRM_NOTIFICATION_PERIOD_TODAY),
			CRM_MODULE_MERGING_STRATEGY_APPEND);

		// generate timeline items for today.
		if (empty($notifications) && empty($events) && empty($modNots)) {
			$title = $this->lh->translationFor("message");
			$message = $this->lh->translationFor("no_notifications_today");
			$timeline .= $this->timelineItemWithMessage($title, $message);
		} else {
			// notifications
			foreach ($notifications as $notification) {
				$timeline .= $this->timelineItemForNotification($notification);
			}
			// events
			foreach ($events as $event) {
				$timeline .= $this->timelineItemForEvent($event);
			}
			if (isset($modNots)) { $timeline .= $modNots; }
		}

        // past week
        $pastWeek = $this->lh->translationFor(CRM_NOTIFICATION_PERIOD_PASTWEEK);
		$timeline .= $this->timelineIntermediateLabel($pastWeek);
		// notifications for past week.
        $notifications = $this->db->getNotificationsForPastWeek($userid);
		// module notifications for past week
		$modNots = $mh->applyHookOnActiveModules(
			CRM_MODULE_HOOK_NOTIFICATIONS,
			array(CRM_NOTIFICATION_PERIOD => CRM_NOTIFICATION_PERIOD_PASTWEEK),
			CRM_MODULE_MERGING_STRATEGY_APPEND);

		if (empty($notifications) && empty($modNots)) {
			$title = $this->lh->translationFor("message");
			$message = $this->lh->translationFor("no_notifications_past_week");
			$timeline .= $this->timelineItemWithMessage($title, $message);
		} else {
			foreach ($notifications as $notification) {
				$timeline .= $this->timelineItemForNotification($notification);
			}
			if (isset($modNots)) { $timeline .= $modNots; }
		}
		// end timeline
		$timeline .= $this->timelineEnd();

        return $timeline;
	}

	/** Statistics */

	protected function datasetWithLabel($label, $data, $color = null) {
		if (!isset($color)) $color = \creamy\CRMUtils::randomRGBAColor(false);
		return '{ label: "'.$label.'",
			fillColor: "'.$this->rgbaColorFromComponents($color, "0.9").'",
	        strokeColor: "'.$this->rgbaColorFromComponents($color, "0.9").'",
	        pointColor: "'.$this->rgbaColorFromComponents($color, "1.0").'",
	        pointStrokeColor: "'.$this->rgbaColorFromComponents($color, "1.0").'",
	        pointHighlightFill: "#fff",
	        pointHighlightStroke: "'.$this->rgbaColorFromComponents($color, "1.0").'",
	        data: ['.implode(",", $data).'] },';
	}

	public function generateLineChartStatisticsData($colors = null) {
		// initialize values
		$labels = "labels: [";
		$datasets = "datasets: [";
		$data = array();
		$statsArray = $this->db->getLastCustomerStatistics();

		$customerTypes = $this->db->getCustomerTypes();

		// create the empty data fields.
		foreach ($customerTypes as $customerType) { $data[$customerType["table_name"]] = array(); }

		// iterate through all customers
		foreach ($statsArray as $obj) {
			// store labels
			$formattedDate = date("Y-m-d",strtotime($obj['timestamp']));
			$labels .= '"'.$formattedDate.'",';

			// store customer number
			foreach ($customerTypes as $customerType) { $data[$customerType["table_name"]][] = $obj[$customerType["table_name"]] or 0; }
		}
		// finish data
		$labels = rtrim($labels, ",")."],";
		$i = 0;
		foreach ($customerTypes as $customerType) {
			$color = isset($colors[$i]) ? $colors[$i] : null;
			$datasets .= $this->datasetWithLabel($customerType["description"], $data[$customerType["table_name"]], $color);
			$i++;
		}
		$datasets = rtrim($datasets, ",")."]";

		return $labels."\n".$datasets;
	}

	protected function pieDataWithLabelAndNumber($label, $number, $color = null) {
		if (!isset($color)) $color = \creamy\CRMUtils::randomRGBAColor(false);
		return '{ value: '.$number.', color: "'.$this->rgbaColorFromComponents($color, "1.0").'", highlight: "'.$this->rgbaColorFromComponents($color, "1.0").'", label: "'.$label.'" },';
	}

	public function generatePieChartStatisticsData($colors = null) {
		$result = "";
		$customerTypes = $this->db->getCustomerTypes();
		$i = 0;
		foreach ($customerTypes as $customerType) {
			$num = $this->db->getNumberOfClientsFromTable($customerType["table_name"]);
			$color = isset($colors[$i]) ? $colors[$i] : null;
			$result .= $this->pieDataWithLabelAndNumber($customerType["description"], $num, $color);
			$i++;
		}
		return $result;
	}

	public function generateStatisticsColors() {
		$num = $this->db->getNumberOfCustomerTypes();
		$result = array();
		for ($i = 0; $i < $num; $i++) {
			$result[] = \creamy\CRMUtils::randomRGBAColor(false);
		}
		return $result;
	}

	public function rgbaColorFromComponents($components, $alpha = "1.0") {
		return "rgba(".$components["r"].", ".$components["g"].", ".$components["b"].", ".(isset($components["a"]) ? $components["a"] : $alpha).")";
	}

	/** Utility functions */

	/**
	 * Generates a relative time string for a given date, relative to the current time.
	 * @param $mysqltime String a string containing the time extracted from MySQL.
	 * @param $maxdepth Int the max depth to dig when representing the time,
	 *        i.e: 3 days, 4 hours, 1 minute and 20 seconds with $maxdepth=2 would be 3 days, 4 hours.
	 * @return String the string representation of the time relative to the current date.
	 */
	public function relativeTime($mysqltime, $maxdepth = 1) {
		$time = strtotime(str_replace('/','-', $mysqltime));
	    $d[0] = array(1,$this->lh->translationFor("second"));
	    $d[1] = array(60,$this->lh->translationFor("minute"));
	    $d[2] = array(3600,$this->lh->translationFor("hour"));
	    $d[3] = array(86400,$this->lh->translationFor("day"));
	    $d[4] = array(604800,$this->lh->translationFor("week"));
	    $d[5] = array(2592000,$this->lh->translationFor("month"));
	    $d[6] = array(31104000,$this->lh->translationFor("year"));

	    $w = array();

		$depth = 0;
	    $return = "";
	    $now = time();
	    $diff = ($now-$time);
	    $secondsLeft = $diff;

		if ($secondsLeft == 0) return "now";

	    for($i=6;$i>-1;$i--)
	    {
	         $w[$i] = intval($secondsLeft/$d[$i][0]);
	         $secondsLeft -= ($w[$i]*$d[$i][0]);
	         if ($w[$i]!=0)
	         {
	            $return.= abs($w[$i]) . " " . $d[$i][1] . (($w[$i]>1)?'s':'') ." ";
	            $depth += 1;
	            if ($depth >= $maxdepth) break;
	         }

	    }

	    $verb = ($diff>0)?"":"in ";
	    $return = $verb.$return;
	    return $return;
	}

	private function substringUpTo($string, $maxCharacters) {
		if (empty($maxCharacters)) $maxCharacters = 4;
		else if ($maxCharacters < 1) $maxCharacters = 4;
	}

	/**
	*
	* TELEPHONY - this area is for the APIs called from gadcs server and implemented through the use of CURL/
	*
	**/

	// get user info
	public function goGetUserInfo($userid, $type, $filter) {
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		if ($type == "user") {
			$postfields["user"] = $userid; #Desired User ID (required)
		} else {
			$postfields["user_id"] = $userid; #Desired User (required)
		}
		if ($filter == "userInfo") {
			$postfields["filter"] = $filter;
		}
		
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		$output = json_decode($data);

		return $output;
	}
	
	public function goGetUserInfoNew($userid) {
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetUserInfoNew"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_id"] = $userid; #Desired User (required)		
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		$output = json_decode($data);

		return $output;
	}	

	// get user list
	public function goGetAllUserList($output, $perm) {
		//$output = $this->api->API_getAllUsers();		
		$checkbox_all = $this->getCheckAll("user");
		
		if ($perm->user_delete !== 'N') {
       	    $columns = array("  ", $this->lh->translationFor("user_id"), $this->lh->translationFor("full_name"), $this->lh->translationFor("user_group"), $this->lh->translationFor("status"), $checkbox_all, $this->lh->translationFor("action"));
		} else {
			$columns = array("  ",$this->lh->translationFor("user_id"), $this->lh->translationFor("full_name"), $this->lh->translationFor("user_group"), $this->lh->translationFor("status"), $this->lh->translationFor("action"));
		}
		//$hideOnMedium = array($this->lh->translationFor("user_group"), $this->lh->translationFor("status"));
		//$hideOnLow = array($this->lh->translationFor("agent_id"), $this->lh->translationFor("user_group"), $this->lh->translationFor("status"));
		$result = $this->generateTableHeaderWithItems($columns, "T_users", "responsive display no-wrap table-bordered table-striped", true, false);
	
		// iterate through all users
		for($i=0;$i<count($output->user_id);$i++) {
			$user_id = $output->user_id[$i];
			$user = $output->user[$i];
			$full_name = $output->full_name[$i];
			$user_group = $output->user_group[$i];
			$user_level = $output->user_level[$i];	
			$active = $output->active[$i];
			
			if ($active == "Y") {
				$active = $this->lh->translationFor("active");
			}else{
				$active = $this->lh->translationFor("inactive");
			}
			$role = $user_level;
			$action = $this->getUserActionMenuForT_User($user_id, $user, $user_level, $full_name, $_SESSION['user'], $perm);
			$avatar = NULL;
			
			if ($this->db->getUserAvatar($user_id)) {
				$avatar = "./php/ViewImage.php?user_id=" . $user_id;
			}
			
			$sessionAvatar = $this->getVueAvatar($full_name, $avatar, 36);				
			$preFix = "<a class='edit-T_user' data-id=".$user_id." data-user=".$user." data-role=".$role.">"; 
			$sufFix = "</a>";
			
			if ($perm->user_update === 'N') {
				$preFix = '';
				$sufFix = '';
			}
			
			$checkbox = '<label for="'.$user_id.'"'.(($perm->user_delete === 'N' || $user === $_SESSION['user']) ? ' class="hidden"' : '').'><div class="checkbox c-checkbox"><label><input name="" class="check_user" id="'.$user_id.'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';				
			$result .= "<tr>
							<td style='width:5%;'>".$sessionAvatar."</a></td>";
				$result .= "<td>".$preFix."<strong>".$user."</strong>".$sufFix."</td>
							<td>".$full_name."</td>
							<td>".$user_group."</td>
							<td>".$active."</td>";
				if ($perm->user_delete !== 'N')							
				$result .= "<td style='width:5%;'>".$checkbox."</td>";
				$result .= "<td nowrap style='width:16%;'>".$action."</td>
						</tr>";
		}
		// print suffix
		//$result .= $this->generateTableFooterWithItems($columns, true, false, $hideOnMedium, $hideOnLow);
		return $result.'</table>';
	}

	private function ActionMenuForLists($id, $name) {

	   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-list" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li class="divider"></li>
			<li><a class="delete-list" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}


	// API to get usergroups
	public function API_goGetUserGroupsList() {
		require_once('Session.php');
		$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goGetAllUserGroups"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
		$postfields["group_id"] = $_SESSION['usergroup']; #json. (required)
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

         return $output;
        /*
        if ($output->result=="success") {
           # Result was OK!
                        for($i=0;$i<count($output->user_group);$i++) {
                                echo $output->user_group[$i]."</br>";
                                echo $output->group_name[$i]."</br>";
                                echo $output->group_type[$i]."</br>";
                                echo $output->forced_timeclock_login[$i]."</br>";
                        }
         } else {
           # An error occured
                echo "The following error occured: ".$results["message"];
        }
		*/
	}
	//USERGROUPS LIST
	public function goGetUserGroupsList() {
		$output = $this->api->API_getAllUserGroups();

		if ($output->result=="success") {
		# Result was OK!

		$columns = array($this->lh->translationFor('user_group'), $this->lh->translationFor('group_name'), $this->lh->translationFor('type'), $this->lh->translationFor('force_timeclock'), $this->lh->translationFor('action'));
	    $hideOnMedium = array($this->lh->translationFor('type'), $this->lh->translationFor('force_timeclock'));
	    $hideOnLow = array($this->lh->translationFor('user_group'), $this->lh->translationFor('type'), $this->lh->translationFor('force_timeclock'));
		$result = $this->generateTableHeaderWithItems($columns, "usergroups_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);


			for($i=0;$i < count($output->user_group);$i++) {

				if ($output->forced_timeclock_login[$i] == "Y") {
					$output->forced_timeclock_login[$i] = $this->lh->translationFor('go_yes');
				}else{
					$output->forced_timeclock_login[$i] = $this->lh->translationFor('go_no');
				}

				$action = $this->ActionMenuForUserGroups($output->user_group[$i], $output->group_name[$i]);

				$result = $result."<tr>
	                    <td class='hide-on-low'><a class='edit-usergroup' data-id='".$output->user_group[$i]."'>".$output->user_group[$i]."</a></td>
	                    <td>".$output->group_name[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->group_type[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->forced_timeclock_login[$i]."</td>
	                    <td nowrap>".$action."</td>
	                </tr>";

			}

			return $result.'</table>';

		} else {
		# An error occured
			return $this->calloutWarningMessage($this->lh->translationFor("No Entry in Database"));
		}

	}
	private function ActionMenuForUserGroups($id, $name) {

	   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-usergroup" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li class="divider"></li>
			<li><a class="delete-usergroup" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

// TELEPHONY INBOUND

	// Telephony IVR
	public function API_getIVR($user_group = '') {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetIVRMenusList"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $user_group;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;

	}

	//Telephony > phonenumber(DID)
	public function API_getPhoneNumber($user_group = '') {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetDIDsList"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $user_group;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;

	}

	public function API_goGetAllAgentRank($user_id, $group_id) {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllAgentRank"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_id"] = $user_id;
		$postfields["group_id"] = $group_id;
		//$postfields["goVarLimit"] = 10;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;

	}

	/*
	 *
	 * SETTINGS MENU
	 *
	*/
	// Settings > Admin Logs
	public function API_goGetAdminLogsList($group, $limit) {
		$url = gourl."/goAdminLogs/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAdminLogsList"; #action performed by the [[API:Functions]]. (required)
		$postfields["goUserGroup"] = $group;
		$postfields["limit"] = $limit;
		$postfields["responsetype"] = responsetype; #json. (required)

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		//var_dump($output);
		return $output;
	}

	public function getAdminLogsList() {
		//$output = $this->API_goGetAdminLogsList($group, $limit);
		$output = $this->api->API_getAdminLogsList();
		if ($output->result=="success") {
		# Result was OK!

			$columns = array($this->lh->translationFor('user'), $this->lh->translationFor('ip_address'), $this->lh->translationFor('date_and_time'), $this->lh->translationFor('action'), $this->lh->translationFor('details'), $this->lh->translationFor('sql_query'));
			$result = $this->generateTableHeaderWithItems($columns, "adminlogs_table", "table-bordered table-striped", true, false);
	
			foreach ($output->data as $log) {
				$details = stripslashes($log->details);
				$db_query = stripslashes($log->db_query);
				//$details = (strlen($details) > 30) ? substr($details, 0, 30) . "..." : $details;
				//$db_query = (strlen($db_query) > 30) ? substr($db_query, 0, 30) . "..." : $db_query;
				$result = $result."<tr>
					<td><span class='hidden-xs'>".$log->name. " ".$log->user."</span><span class='visible-xs'>".$log->user."</span></td>
					<td><a href='http://www.ip-tracker.org/locator/ip-lookup.php?ip=".$log->ip_address."' target='_new'>".$log->ip_address."</a></td>
					<td>".$log->event_date."</td>
					<td>".$log->action."</td>
					<td title=\"".stripslashes($log->details)."\">".$details."</td>
					<td title=\"".stripslashes($log->db_query)."\">".$db_query."</td></tr>";
			}

			return $result.'</table>';

		} else {
			return $output->result;
		}
	}

	// Settings > Phone
	public function API_getPhonesList() {
		$url = gourl."/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllPhones"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
	}

	public function getPhonesList() {
		$output = $this->api->API_getAllPhones();
		
		if ($output->result=="success") {
			# Result was OK!
			$checkbox_all = $this->getCheckAll("phone");
			$columns = array($this->lh->translationFor("extension"), $this->lh->translationFor("protocol"),$this->lh->translationFor("server_ip"), $this->lh->translationFor("status"), $this->lh->translationFor("voicemail"), $checkbox_all, $this->lh->translationFor("action"));
			$result = $this->generateTableHeaderWithItems($columns, "T_phones", "responsive display no-wrap table-bordered table-striped", true, false);

			for ($i=0;$i < count($output->extension);$i++) {
				if ($output->active[$i] == "Y") {
					$output->active[$i] = $this->lh->translationFor("active");
				} else{
					$output->active[$i] = $this->lh->translationFor("inactive");
				}

				if ($output->messages[$i] == NULL) {
					$output->messages[$i] = 0;
				}
				
				if ($output->old_messages[$i] == NULL) {
					$output->old_messages[$i] = 0;
				}
				
				$checkbox = '<label for="'.$output->extension[$i].'"><div class="checkbox c-checkbox"><label><input name="" class="check_phone" id="'.$output->extension[$i].'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';
				$action = $this->getUserActionMenuForPhones($output->extension[$i]);
                //$sessionAvatar = "<avatar username='".$output->messages[$i]."' :size='36'></avatar><td>".$sessionAvatar."</a></td>";
				
				$result = $result."<tr>
	                    <td><a class='edit-phone' data-id='".$output->extension[$i]."'><strong>".$output->extension[$i]."</strong></a></td>				
						<td>".$output->protocol[$i]."</td>
						<td>".$output->server_ip[$i]."</td>
	                    <td>".$output->active[$i]."</td>
						<td>".$output->messages[$i]."&nbsp;<font style='padding-left: 50px;'>".$output->old_messages[$i]."</font></td>";
				$result .= "<td style='width:5%;'>".$checkbox."</td>						
						<td nowrap style='width:16%;'>".$action."</td>
	                </tr>";
			}

			return $result.'</table>';
		} else {
			# An error occured
			return $this->calloutErrorMessage($this->lh->translationFor("Unable to get Phone List"));
		}
	}

	// VoiceMails
	public function API_goGetVoiceMails() {

		$url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goGetAllVoiceFiles"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)

         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);

		//var_dump($output);
		return $output;

	}

	public function getVoiceMails() {
		$output = $this->api->API_getAllVoiceMails();

		if ($output->result=="success") {
		# Result was OK!

		$columns = array($this->lh->translationFor('voicemail_id'), $this->lh->translationFor('name'), $this->lh->translationFor('status'), $this->lh->translationFor('new_message'), $this->lh->translationFor('old_message'), $this->lh->translationFor('delete'), $this->lh->translationFor('user_group'), $this->lh->translationFor('action'));
	    $hideOnMedium = array($this->lh->translationFor('status'), $this->lh->translationFor('new_message'), $this->lh->translationFor('old_message'), $this->lh->translationFor('delete'), $this->lh->translationFor('user_group'));
	    $hideOnLow = array($this->lh->translationFor('voicemail_id'), $this->lh->translationFor('status'), $this->lh->translationFor('new_message'), $this->lh->translationFor('old_message'), $this->lh->translationFor('delete'), $this->lh->translationFor('user_group'));
		$result = $this->generateTableHeaderWithItems($columns, "voicemails_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			for($i=0;$i < count($output->voicemail_id);$i++) {

				if ($output->active[$i] == "Y") {
					$output->active[$i] = $this->lh->translationFor('active');
				}else{
					$output->active[$i] = $this->lh->translationFor('inactive');
				}

				$action = $this->ActionMenuForVoicemail($output->voicemail_id[$i], $output->fullname[$i]);

				$result = $result."<tr>
	                    <td class='hide-on-low'><a class='edit-voicemail' data-id='".$output->voicemail_id[$i]."''>".$output->voicemail_id[$i]."</a></td>
	                    <td>".$output->fullname[$i]."</a></td>
						<td class='hide-on-medium hide-on-low'>".$output->active[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->messages[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->old_messages[$i]."</td>
						<td class='hide-on-medium hide-on-low'>".$output->delete_vm_after_email[$i]."</td>
						<td class='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
	                    <td nowrap>".$action."</td>
	                </tr>";

			}

			return $result.'</table>';

		}else{
			// if no entry in voicemails
			return $this->calloutWarningMessage($this->lh->translationFor("No Entry in Database"));
		}
	}

	private function ActionMenuForVoicemail($id, $name) {

	   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-voicemail" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li class="divider"></li>
			<li><a class="delete-voicemail" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Getting Circle Buttons */

	/**
	 * Generates action circle buttons for different pages/module
	 * @param page name of page/current page
	 * @param icon will determine what icon to be use for the button
	 */
	public function getCircleButton($page, $icon) {
	    $theme = $this->db->getSettingValueForKey(CRM_SETTING_THEME);
	    if (empty($theme)) {
	    	$theme = 'blue';
	    }

	    // this will be the output html
	    $button = "";
	    $button .= '<a button-area add-'.$page.'">';
	    $button .= '<div class="circle-button skin-'.$theme.'">';
	    $button .= '<em class="fa fa-'.$icon.' button-area add-'.$page.'"></em>';
	    $button .= '</div>';
	    $button .= '</a>';
	    return $button;
	}

	/** Campaigns API - Get all list of campaign */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function API_getRealtimeAgent($goUser, $goPass, $goAction, $responsetype) {
	    $url = gourl."/goBarging/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetAgentsOnCall"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

		return $output;
	}

	public function ActionMenuForCampaigns($id, $name, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->campaign->campaign_update === 'N' ? ' class="hidden"' : '').'><a class="edit-campaign" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->pausecodes->pausecodes_read === 'N' ? ' class="hidden"' : '').'><a class="view-pause-codes" href="#" data-id="'.$id.'">'.$this->lh->translationFor("pause_codes").'</a></li>
			<li'.($perm->hotkeys->hotkeys_read === 'N' ? ' class="hidden"' : '').'><a class="view-hotkeys" href="#" data-id="'.$id.'">'.$this->lh->translationFor("hotkeys").'</a></li>
			<li'.($perm->list->list_read === 'N' ? ' class="hidden"' : '').'><a class="view-lists" href="#" data-id="'.$id.'">'.$this->lh->translationFor("lists").'</a></li>
			<li class="divider'.($perm->campaign->campaign_delete === 'N' ? ' hidden' : '').'"></li>
			<li'.($perm->campaign->campaign_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-campaign" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Call Recordings API - Get all list of call recording */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */
	public function API_getListAllRecordings($search_phone, $start_filterdate, $end_filterdate, $agent_filter) {
		require_once('Session.php');
		$url = gourl."/goCallRecordings/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetCallRecordingList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
	    $postfields["requestDataPhone"] = $search_phone;
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["session_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
		
	    if (isset($start_filterdate))
	    $postfields["start_filterdate"] = $start_filterdate;

	    $postfields["end_filterdate"] = $end_filterdate;
	    $postfields["agent_filter"] = $agent_filter;

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}

	public function getListAllRecordings($search_phone, $start_filterdate, $end_filterdate, $agent_filter, $session_user) {
	    $output = $this->API_getListAllRecordings($search_phone, $start_filterdate, $end_filterdate, $agent_filter, $_SESSION['user']);

	    if ($output->result=="success") {

	    	$columns = array("Date", "Customer", "Phone Number", "Agent", "Duration", "Action");
	    	$hideOnMedium = array("Agent", "Duration");
	    	$hideOnLow = array("Customer", "Phone Number", "Agent", "Duration");
			$result = $this->generateTableHeaderWithItems($columns, "table_callrecordings", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			//$result .= "<tr><td colspan='6'>".$output->query."</tr>";

	    for($i=0; $i < count($output->uniqueid); $i++) {
			
			$details = "<strong>Phone</strong>: <i>".$output->phone_number[$i]."</i><br/>";
			$details .= "<strong>Date</strong>: <i>".date("M.d,Y h:i A", strtotime($output->end_last_local_call_time[$i]))."</i><br/>";
			
			//$action_Call = $output->query;
			$action_Call = $this->getUserActionMenuForCallRecording($output->uniqueid[$i], $output->location[$i], $details);

			$d1 = strtotime($output->start_last_local_call_time[$i]);
			$d2 = strtotime($output->end_last_local_call_time[$i]);

			$diff = abs($d2 - $d1);
			$duration = gmdate('H:i:s', $diff);
			
			$result .= "<tr>
				<td>".date("M.d,Y h:i A", strtotime($output->end_last_local_call_time[$i]))."</td>
				<td class='hide-on-low'>".$output->full_name[$i]."</td>
				<td class='hide-on-low'>".$output->phone_number[$i]."</td>
				<td class='hide-on-medium hide-on-low'>".$output->users[$i]."</td>
				<td class='hide-on-medium hide-on-low'>".$duration."</td>
				<td>".$action_Call."</td>
				</tr>";

	    }

			return $result."</table>";

	    } else {
		# An error occured
			return $output->result;

	    }
	}

	public function getUserActionMenuForCallRecording($id, $location, $details) {
	    return "<div class='btn-group'>
		    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>".$this->lh->translationFor('choose_action')."
		    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' style='height: 34px;'>
					    <span class='caret'></span>
					    <span class='sr-only'>Toggle Dropdown</span>
		    </button>
		    <ul class='dropdown-menu' role='menu'>
			<li><a class='play_audio' href='#' data-location='".$location."' data-details='".$details."'>".$this->lh->translationFor('play_call_recording')."</a></li>
			<li><a class='download-call-recording' href='".$location."' download>".$this->lh->translationFor('download_call_recording')."</a></li>
		    </ul>
		</div>";
	}

	/** Music On Hold API - Get all list of music on hold */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */
	public function API_goGetAllMusicOnHold() {
		$url = gourl."/goMusicOnHold/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetAllMusicOnHold"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    if ($output->result=="success") {
	    	return $output;
	    } else {
		# An error occured
			return $output->result;
	    }
	}

	public function getListAllMusicOnHold($user_group) {
		//require_once('Session.php');
		$perm = $this->api->goGetPermissions('moh', $user_group);
	    $output = $this->api->API_getAllMusicOnHold();

	    # Result was OK!
	    $columns = array($this->lh->translationFor('moh_name'), $this->lh->translationFor('status'), $this->lh->translationFor('random_order'), $this->lh->translationFor('group'), $this->lh->translationFor('action'));
	    $hideOnMedium = array("Random Order", "Group", "Status");
		$hideOnLow = array( "Random Order", "Group", "Status");
	    $result = $this->generateTableHeaderWithItems($columns, "music-on-hold_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	    for($i=0;$i<count($output->moh_id);$i++) {
			$action = $this->getUserActionMenuForMusicOnHold($output->moh_id[$i], $output->moh_name[$i], $perm);

			if ($output->active[$i] == "Y") {
				$output->active[$i] = "Active";
			}else{
				$output->active[$i] = "Inactive";
			}

			if ($output->random[$i] == "Y") {
				$output->random[$i] = "YES";
			}else{
				$output->random[$i] = "NO";
			}

			if ($output->user_group[$i] == "---ALL---") {
				$output->user_group[$i] = "ALL USER GROUPS";
			}

			$result .= "<tr>
				<td><a class='edit-moh' data-toggle='modal data-target='#view-moh-modal' data-id='".$output->moh_id[$i]."'>".$output->moh_name[$i]."</td>
				<td class ='hide-on-medium hide-on-low'>".$output->active[$i]."</td>
				<td class ='hide-on-medium hide-on-low'>".$output->random[$i]."</td>
				<td class ='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
				<td nowrap>".$action."</td>
				</tr>";
	    }
		return $result.'</table>';
	}

	private function getUserActionMenuForMusicOnHold($id, $name, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->moh_update === 'N' ? ' class="hidden"' : '').'><a class="edit-moh" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->moh_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-moh" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Voice Files API - Get all list of voice files */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */
	public function API_getAllVoiceFiles() {
	    $url = gourl."/goVoiceFiles/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetVoiceFilesList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}

	public function getListAllVoiceFiles($user_group) {
		//require_once('Session.php');
		$perm = $this->api->goGetPermissions('voicefiles', $user_group);
		$output = $this->api->API_getAllVoiceFiles();
	    //if ($output->result=="success") {
	    # Result was OK!
	    $columns = array($this->lh->translationFor('file_name'), $this->lh->translationFor('date'), $this->lh->translationFor('size'), $this->lh->translationFor('action'));
	    $hideOnMedium = array("Date");
		$hideOnLow = array( "Date");
		$result = $this->generateTableHeaderWithItems($columns, "voicefiles", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
	    $server_port = getenv("SERVER_PORT");
		//$web_ip = getenv("SERVER_ADDR");
		//$web_ip = $_SERVER['SERVER_NAME'];
		$web_ip = $log_ip;
		if (preg_match("/443/",$server_port)) {$HTTPprotocol = 'https://';}
		else {$HTTPprotocol = 'http://';}
	    for($i=0;$i<count($output->file_name);$i++) {
	    $file_link = $HTTPprotocol.$web_ip."/sounds/".$output->file_name[$i];
		 if (!$this->check_url($file_link)) {
			 $web_host = getenv("SERVER_NAME");
			 $file_link = "http://".$web_host."/sounds/".$output->file_name[$i];
		 }
	    //$file_link = "http://69.46.6.35/sounds/".$output->file_name[$i];

	    $details = "<strong>Filename</strong>: <i>".$output->file_name[$i]."</i><br/>";
	    $details .= "<strong>Date</strong>: <i>".$output->file_date[$i]."</i><br/>";

		$action = $this->getUserActionMenuForVoiceFiles($output->file_name[$i], $details, $perm, $HTTPprotocol, $web_ip);

		$preFix = "<a class='play_voice_file' data-location='".$file_link."' data-details='".$details."'>";
		$sufFix = "</a>";
		if ($perm->voicefiles_play === 'N') {
			$preFix = '';
			$sufFix = '';
		}
		
		$result .= "<tr>
			<td>{$preFix}".$output->file_name[$i]."{$sufFix}</td>
			<td class ='hide-on-medium hide-on-low'>".$output->file_date[$i]."</td>
			<td class ='hide-on-medium hide-on-low'>".$output->file_size[$i]."</td>
			<td nowrap>".$action."</td>
		    </tr>";
	    }
		return $result.'</table>';
	    //} else {
		# An error occured
		//return $output->result;
	    //}
	}

	private function getUserActionMenuForVoiceFiles($filename, $details, $perm, $protocol, $web_ip) {
	    $file_link = $protocol.$web_ip."/sounds/".$filename;
		 if (!$this->check_url($file_link)) {
			 $web_host = getenv("SERVER_NAME");
			 $file_link = "http://".$web_host."/sounds/".$filename;
		 }
	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->voicefiles_play === 'N' ? ' class="hidden"' : '').'><a class="play_voice_file" href="#" data-location="'.$file_link.'" data-details="'.$details.'">'.$this->lh->translationFor("play_voice_file").'</a></li>
		    </ul>
		</div>';
	}


	/** Scripts API - Get all list of scripts */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	// API Scripts

	public function getListAllScripts($userid, $perm) {
	    $output = $this->api->API_getAllScripts($userid);

	    if ($output->result=="success") {
	    # Result was OK!
	    $columns = array($this->lh->translationFor("script_id"), $this->lh->translationFor("script_name"), $this->lh->translationFor("status"), $this->lh->translationFor("type"), $this->lh->translationFor("user_group"), $this->lh->translationFor("action"));
	    //$hideOnMedium = array($this->lh->translationFor("type"), $this->lh->translationFor("status"), $this->lh->translationFor("user_group"));
	    //$hideOnLow = array($this->lh->translationFor("script_id"), $this->lh->translationFor("type"), $this->lh->translationFor("status"), $this->lh->translationFor("user_group"));

		$result = $this->generateTableHeaderWithItems($columns, "scripts_table", "display responsive no-wrap table-bordered table-striped", true, false);

	    for($i=0;$i<count($output->script_id);$i++) {
		$action = $this->getUserActionMenuForScripts($output->script_id[$i], $output->script_name[$i], $perm);

			if ($output->active[$i] == "Y") {
			    $active = $this->lh->translationFor("active");
			}else{
			    $active = $this->lh->translationFor("inactive");
			}
			
			$preFix = "<a class='edit_script' data-id='".$output->script_id[$i]."'>";
			$sufFix = "</a>";
			if ($perm->script_update === 'N') {
				$preFix = '';
				$sufFix = '';
			}

			$result .= "<tr>
				<td>".$preFix."".$output->script_id[$i]."".$sufFix."</td>
				<td>".$output->script_name[$i]."</td>
				<td>".$active."</td>
				<td>".$output->active[$i]."</td>
				<td>".$output->user_group[$i]."</td>
				<td>".$action."</td>
			    </tr>";
		    }
			return $result.'</table>';

	    } else {
		# An error occured
		return $output->result;
	    }
	}

	private function getUserActionMenuForScripts($id, $name, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->script_update === 'N' ? ' class="hidden"' : '').'><a class="edit_script" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->script_delete === 'N' ? ' class="hidden"' : '').'><a class="delete_script" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Call Times API - Get all list of call times */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function getCalltimes() {
		$url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "getAllCalltimes"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
		 $postfields["log_user"] = $_SESSION['user'];
		 $postfields["log_group"] = $_SESSION['usergroup'];
		 $postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}

	public function getListAllCallTimes() {
	    $output = $this->api->API_getAllCalltimes();
	    if ($output->result=="success") {
	    # Result was OK!
        //$columns = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('call_time_name'), $this->lh->translationFor('default_start'), $this->lh->translationFor('default_stop'), $this->lh->translationFor('user_group'), $this->lh->translationFor('action'));
        //$hideOnMedium = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('default_start'), $this->lh->translationFor('default_stop'), $this->lh->translationFor('user_group'));
		//$hideOnLow = array( $this->lh->translationFor('call_time_id'), $this->lh->translationFor('default_start'), $this->lh->translationFor('default_stop'), $this->lh->translationFor('user_group'));
		$columns = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('call_time_name'), $this->lh->translationFor('Schedule'), $this->lh->translationFor('user_group'), $this->lh->translationFor('action'));
        $hideOnMedium = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('user_group'));
		$hideOnLow = array( $this->lh->translationFor('call_time_id'), $this->lh->translationFor('Schedule'), $this->lh->translationFor('user_group'));
		
		$result = $this->generateTableHeaderWithItems($columns, "calltimes", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
		
	    for($i=0;$i<count($output->call_time_id);$i++) {
		    $action = $this->getUserActionMenuForCalltimes($output->call_time_id[$i], $output->call_time_name[$i]);
			$schedule = "NULL";
			if ($output->ct_default_start[$i] === $output->ct_default_stop[$i]) {
				$def = 'data-def="NULL"';
			}else{
				$default_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_default_start[$i])));
				$default_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_default_stop[$i])));
				$def = 'data-def="'.$default_start.' - '.$default_stop.'"';
				$schedule = $default_start.' - '.$default_stop;
			}
			if ($output->ct_sunday_start[$i] === $output->ct_sunday_stop[$i]) {
				$sun = 'data-sun="NULL"';
			}else{
				$sun_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_sunday_start[$i])));
				$sun_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_sunday_stop[$i])));
				$sun = 'data-sun="'.$sun_start.' - '.$sun_stop.'"';
				if ($schedule === "NULL")
					$schedule = $sun_start.' - '.$sun_stop;
			}
			if ($output->ct_monday_start[$i] === $output->ct_monday_stop[$i]) {
				$mon = 'data-mon="NULL"';
			}else{
				$mon_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_monday_start[$i])));
				$mon_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_monday_stop[$i])));
				$mon = 'data-mon="'.$mon_start.' - '.$mon_stop.'"';
				if ($schedule === "NULL")
					$schedule = $mon_start.' - '.$mon_stop;
			}
			if ($output->ct_tuesday_start[$i] === $output->ct_tuesday_stop[$i]) {
				$tue = 'data-tue="NULL"';
			}else{
				$tue_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_tuesday_start[$i])));
				$tue_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_tuesday_stop[$i])));
				$tue = 'data-tue="'.$tue_start.' - '.$tue_stop.'"';
				if ($schedule === "NULL")
					$schedule = $tue_start.' - '.$tue_stop;
			}
			if ($output->ct_wednesday_start[$i] === $output->ct_wednesday_stop[$i]) {
				$wed = 'data-wed="NULL"';
			}else{
				$wed_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_wednesday_start[$i])));
				$wed_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_wednesday_stop[$i])));
				$wed = 'data-wed="'.$wed_start.' - '.$wed_start.'"';
				if ($schedule === "NULL")
					$schedule = $wed_start.' - '.$wed_stop;
			}
			if ($output->ct_thursday_start[$i] === $output->ct_thursday_stop[$i]) {
				$thu = 'data-thu="NULL"';
			}else{
				$thu_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_thursday_start[$i])));
				$thu_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_thursday_stop[$i])));
				$thu = 'data-thu="'.$thu_start.' - '.$thu_stop.'"';
				if ($schedule === "NULL")
					$schedule = $thu_start.' - '.$thu_stop;
			}
			if ($output->ct_friday_start[$i] === $output->ct_friday_stop[$i]) {
				$fri = 'data-fri="NULL"';
			}else{
				$fri_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_friday_start[$i])));
				$fri_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_friday_stop[$i])));
				$fri = 'data-fri="'.$fri_start.' - '.$fri_stop.'"';
				if ($schedule === "NULL")
					$schedule = $fri_start.' - '.$fri_stop;
			}
			if ($output->ct_saturday_start[$i] === $output->ct_saturday_stop[$i]) {
				$sat = 'data-sat="NULL"';
			}else{
				$sat_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_saturday_start[$i])));
				$sat_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_saturday_stop[$i])));
				$sat = 'data-sat="'.$sat_start.' - '.$sat_stop.'"';
				if ($schedule === "NULL")
					$schedule = $sat_start.' - '.$sat_stop;
			}
            if ($output->user_group[$i] === "---ALL---") {
            	$output->user_group[$i] = "ALL USER GROUPS";
            }
			$scheds = $def.' '.$mon.' '.$tue.' '.$wed.' '.$thu.' '.$fri.' '.$sat.' '.$sun;
			//<td class ='hide-on-medium hide-on-low'>".$output->ct_default_start[$i]."</td>
			//<td class ='hide-on-medium hide-on-low'>".$output->ct_default_stop[$i]."</td>
			$view_modal = '<a class="view_sched" data-toggle="modal" data-target="#view-sched-modal" data-id="'.$output->call_time_id[$i].' - '.$output->call_time_name[$i].'" '.$scheds.'>'.$schedule.'</a>';
				$result .= "<tr>
					<td class ='hide-on-medium hide-on-low'><a class='edit-calltime' data-id='".$output->call_time_id[$i]."'>".$output->call_time_id[$i]."</a></td>
					<td>".$output->call_time_name[$i]."</td>
					<td class ='hide-on-medium hide-on-low'>".$view_modal."</td>
					<td class ='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
					<td nowrap>".$action."</td>
				</tr>";
        }
		    return $result.'</table>';
	    } else {
	       # An error occured
	       return $output->result;
	    }
	}

	private function getUserActionMenuForCalltimes($id, $name) {
	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-calltime" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li><a class="delete-calltime" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}
	
	private function getCalltimeScheds($id, $name) {
	    
	}
	
	/** Carriers API - Get all list of carriers */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function getServers() {
		$url = gourl."/goServers/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetServerList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}
	
	public function getServerList($perm) {
		$output = $this->api->API_getAllServers();

	    if ($output->result=="success") {
	    # Result was OK!
			$columns = array($this->lh->translationFor('server_id'), $this->lh->translationFor('server_name'), $this->lh->translationFor('server_ip'), $this->lh->translationFor('status'), $this->lh->translationFor('asterisk'), $this->lh->translationFor('trunks'), $this->lh->translationFor('gmt'), $this->lh->translationFor('action'));
			$hideOnMedium = array($this->lh->translationFor('asterisk'),$this->lh->translationFor('trunks'), $this->lh->translationFor('gmt'));
			$hideOnLow = array($this->lh->translationFor('server_ip'), $this->lh->translationFor('server_name'), $this->lh->translationFor('status'), $this->lh->translationFor('asterisk'),$this->lh->translationFor('trunks'),$this->lh->translationFor('gmt'));

			$result = $this->generateTableHeaderWithItems($columns, "servers_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

				for($i=0;$i<count($output->server_id);$i++) {

					$action = '';
					if ($perm->servers_update != 'N' || $perm->servers_delete != 'N') {
						$action = $this->ActionMenuForServers($output->server_id[$i], $perm);
					}

					if ($output->active[$i] == "Y") {
						$active = $this->lh->translationFor('active');
					}else{
						$active = $this->lh->translationFor('inactive');
					}
						$result .= "<tr>
							<td class ='hide-on-low'>".($perm->servers_update !== 'N' ? "<a class='edit-server' data-id='".$output->server_id[$i]."'>" : '')."".$output->server_id[$i]."</td>
							<td>".$output->server_description[$i]."</td>
							<td class ='hide-on-medium hide-on-low'>".$output->server_ip[$i]."</td>
							<td class ='hide-on-medium hide-on-low'>".$active."</td>
							<td class ='hide-on-low'>".$output->asterisk_version[$i]."</td>
							<td class ='hide-on-low'>".$output->max_vicidial_trunks[$i]."</td>
							<td class ='hide-on-low'>".$output->local_gmt[$i]."</td>
							<td nowrap>".$action."</td>
						</tr>";
				}

				return $result.'</table>';

	    } else {
	       # An error occured
	       return $output->result;
	    }
	}
	
	public function ActionMenuForServers($id, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->servers_update === 'N' ? ' class="hidden"' : '').'><a class="edit-server" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->servers_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-server" href="#" data-id="'.$id.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}
	
	/** Carriers API - Get all list of carriers */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */



	public function getListAllCarriers($perm) {
		$output = $this->api->API_getAllCarriers();

	    if ($output->result=="success") {
	    # Result was OK!

        $columns = array($this->lh->translationFor('carrier_id'), $this->lh->translationFor('carrier_name'), $this->lh->translationFor('server_ip'), $this->lh->translationFor('protocol'), $this->lh->translationFor('status'), $this->lh->translationFor('action'));
        $hideOnMedium = array($this->lh->translationFor('server_ip'), $this->lh->translationFor('protocol'));
		$hideOnLow = array( $this->lh->translationFor('carrier_id'), $this->lh->translationFor('server_ip'), $this->lh->translationFor('protocol'), $this->lh->translationFor('status'));

		$result = $this->generateTableHeaderWithItems($columns, "carriers", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	      for($i=0;$i<count($output->carrier_id);$i++) {

				$action = '';
				if ($perm->carriers_update != 'N' || $perm->carriers_delete != 'N') {
					$action = $this->getUserActionMenuForCarriers($output->carrier_id[$i], $perm);
				}

			    if ($output->active[$i] == "Y") {
				    $active = $this->lh->translationFor('active');
				}else{
				    $active = $this->lh->translationFor('inactive');
				}
            $result .= "<tr>
						<td class ='hide-on-low'>".($perm->carriers_update !== 'N' ? "<a class='edit-carrier' data-id='".$output->carrier_id[$i]."'>" : '')."".$output->carrier_id[$i]."</td>
						<td>".$output->carrier_name[$i]."</td>
						<td class ='hide-on-medium hide-on-low'>".$output->server_ip[$i]."</td>
						<td class ='hide-on-medium hide-on-low'>".$output->protocol[$i]."</td>
						<td class ='hide-on-low'>".$active."</td>
						<td nowrap>".$action."</td>
	            </tr>";
         }

		    return $result.'</table>';

	    } else {
	       # An error occured
	       return $output->result;
	    }
	}

	public function getUserActionMenuForCarriers($id, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->carriers_update === 'N' ? ' class="hidden"' : '').'><a class="edit-carrier" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->carriers_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-carrier" href="#" data-id="'.$id.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/**
     * Returns a HTML representation of the wizard form of campaign
     *
     */
	public function wizardFromCampaign() {
		return '
			<div class="form-horizontal">
    			<div class="form-group">
    				<label class="control-label col-lg-4">Campaign Type:</label>
    				<div class="col-lg-8">
    					<select id="campaignType" class="form-control">
    						<option value="outbound">Outbound</option>
    						<option value="inbound">Inbound</option>
    						<option value="blended">Blended</option>
    						<option value="survey">Survey</option>
    						<option value="copy">Copy Campaign</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group campaign-id">
    				<label class="control-label col-lg-4">Campaign ID:</label>
    				<div class="col-lg-8">
    					<div class="input-group">
					      <input id="campaign-id" name="campaign_id" type="text" class="form-control" placeholder="" readonly>
					      <span class="input-group-btn">
					        <button id="campaign-id-edit-btn" class="btn btn-default" type="button"><i class="fa fa-pencil"></i></button>
					      </span>
					    </div><!-- /input-group -->
    				</div>
    			</div>
    			<div class="form-group">
    				<label class="control-label col-lg-4">Campaign Name:</label>
    				<div class="col-lg-8">
    					<input id="campaign-name" type="text" class="form-control">
    				</div>
    			</div>
    			<div class="form-group did-tfn-ext hide">
    				<label class="control-label col-lg-4">DID / TFN Extension:</label>
    				<div class="col-lg-8">
    					<input id="did-tfn" type="text" class="form-control">
    				</div>
    			</div>
    			<div class="form-group call-route hide">
    				<span class="control-label col-lg-4">Call route:</span>
    				<div class="col-lg-8">
    					<select id="call-route" class="form-control">
    						<option value="NONE"></option>
    						<option value="INGROUP">INGROUP (campaign)</option>
    						<option value="IVR">IVR (callmenu)</option>
    						<option value="AGENT">AGENT</option>
    						<option value="VOICEMAIL">VOICEMAIL</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group surver-type hide">
    				<span class="control-label col-lg-4">Survey Type:</span>
    				<div class="col-lg-8">
    					<select id="survey-type" class="form-control">
    						<option value="BROADCAST">VOICE BROADCAST</option>
    						<option value="PRESS1">SURVEY PRESS 1</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group no-channels hide">
    				<span class="control-label col-lg-4">Number of Channels:</span>
    				<div class="col-lg-8">
    					<select id="no-channels" class="form-control">
    						<option>1</option>
    						<option>5</option>
    						<option>10</option>
    						<option>15</option>
    						<option>20</option>
    						<option>30</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group copy-from hide">
    				<span class="control-label col-lg-4">Copy from:</span>
    				<div class="col-lg-8">
    					<select id="copy-from" class="form-control">
    						<option>LIST HERE</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group upload-wav hide">
    				<span class="control-label col-lg-4">Please Upload .wav file</span>
    				<div class="col-lg-8">
    					<div class="input-group">
					      <input type="text" class="form-control" placeholder="16 bit mono 8000 PCM WAV audio files only">
					      <span class="input-group-btn">
					        <button class="btn btn-primary" type="button">Browse</button>
					      </span>
					    </div><!-- /input-group -->
    				</div>
    			</div>
    			<div class="lead-section hide">
        			<div class="form-group">
        				<label class="control-label col-lg-4">Lead File:</label>
        				<div class="col-lg-8">
        					<input type="text" class="form-control">
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">List ID:</label>
        				<div class="col-lg-8">
        					<span>Auto Generated here range</span>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">Country:</label>
        				<div class="col-lg-8">
        					<select class="form-control">
        						<option>LIST HERE</option>
        					</select>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">Check For Duplicates:</label>
        				<div class="col-lg-8">
        					<select class="form-control">
        						<option>LIST HERE</option>
        					</select>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">&nbsp</label>
        				<div class="col-lg-8">
        					<button type="button" class="btn btn-default">UPLOAD LEADS</button>
        				</div>
        			</div>
    			</div>
    		</div>
		';
	}

//--------- Disposition ---------

	public function ActionMenuForDisposition($id, $name, $perm) {
		 return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->disposition->disposition_update === 'N' ? ' class="hidden"' : '').'><a class="view_disposition" href="#" data-toggle="modal" data-target="#modal_view_dispositions" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->disposition->disposition_delete === 'N' ? ' class="hidden"' : '').'><a class="delete_disposition" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	public function ActionMenuForLeadRecycling($id) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="view_leadrecycling" href="#" data-toggle="modal" data-target="#modal_view_leadrecycling" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li><a class="delete_leadrecycling" href="#" data-id="'.$id.'" data-campaign="'.$id.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}	
//--------- Lead Filter ---------

	public function ActionMenuForLeadFilters($id, $name) {
		 return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="view_leadfilter" href="#" data-id="'.$id.'">'.$this->lh->translationFor("view").'</a></li>
				<li><a class="edit_leadfilter" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
				<li><a class="delete_leadfilter" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
			    </ul>
			</div>';
		}

		/*
		 * <<<<==================== END OF TELEPHONY APIs =====================>>>>
		 */

		/*
		 * APIs for Dashboard
		 *
		*/
			/*
			 * Displaying Total Sales
			 * [[API: Function]] - goGetTotalSales
			 * This application is used to get total number of total sales.
			*/
		public function API_goGetTotalSales($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalSales"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			//var_dump($data);
			/*$data = explode(";",$data);
			foreach ($data AS $temp) {
			  $temp = explode("=",$temp);
			  $results[$temp[0]] = $temp[1];
			}
			if ($results["result"]=="success") {
			  # Result was OK!
			  //var_dump($results); #to see the returned arrays.
				return $results["getTotalSales"];
			} else {
			  # An error occurred
				$vars = 0;
				return $vars;
			}*/
			return $output;
		}

		/*
		 * Displaying in Sales / Hour
		 * [[API: Function]] - goGetINSalesHour
		 * This application is used to get total number of in Sales per hour
		*/
		public function API_goGetINSalesPerHour($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetINSalesPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			$data = explode(";",$data);
			foreach ($data AS $temp) {
			  $temp = explode("=",$temp);
			  $results[$temp[0]] = $temp[1];
			}

			if ($results["result"]=="success") {
			  # Result was OK!
			  //var_dump($results); #to see the returned arrays.
				   return $results["getINSalesPerHour"];
			} else {
			  # An error occurred
				   $vars = 0;
				   return $vars;
			}

		}

		/*
		 * Displaying OUT Sales / Hour
		 * [[API: Function]] - goGetOutSalesPerHour
		 * This application is used to get OUT sales per hour.
		*/

		public function API_goGetOUTSalesPerHour($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetOutSalesPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
				return $results["getOutSalesPerHour"];
			 } else {
			   # An error occurred
				$vars = 0;
				return $vars;
			 }
		}
		
		/*
		 * Displaying inbound Sales
		 * [[API: Function]] - goGetTotalInboundSales
		 * This application is used to get total number of inbound sales
		*/
		public function API_goGetInboundSales($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalInboundSales"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_URL, $url);
			 curl_setopt($ch, CURLOPT_POST, 1);
			 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 $data = curl_exec($ch);
			 curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					return $results["InboundSales"];
			 } else {
			   # An error occurred
					$vars = 0;
					return $vars;
			 }

		}
		
		/*
		 * Displaying outbound Sales
		 * [[API: Function]] - goGetTotalOutboundSales
		 * This application is used to get total number of outbound sales
		*/
		public function API_goGetOutboundSales($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalOutboundSales"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			
			$data = explode(";",$data);
			foreach ($data AS $temp) {
			  $temp = explode("=",$temp);
			  $results[$temp[0]] = $temp[1];
			}

			if ($results["result"]=="success") {
			  # Result was OK!
			  //var_dump($results); #to see the returned arrays.
				   return $results["OutboundSales"];
			} else {
			  # An error occurred
				   $vars = 0;
				   return $vars;
			}
		}

		/*
		 * Displaying Agent(s) Waiting
		 * [[API: Function]] - getTotalAgentsWaitCalls
		 * This application is used to get total of agents waiting
		*/

		public function API_goGetTotalAgentsWaitCalls() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAgentsWaitCalls"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
            return($data);
		}

		/*
		 *Displaying Agent(s) on Paused
		 *[[API: Function]] - goGetTotalAgentsPaused
		 *This application is used to get total of agents paused
		*/

		public function API_goGetTotalAgentsPaused() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAgentsPaused"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			$output = json_decode($data);

			return $output;

		}

		/*
		 * Displaying Agent(s) on Call
		 * [[API: Function]] - goGetTotalAgentsCall
		 * This application is used to get total of agents on call
		*/

		public function API_goGetTotalAgentsCall() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAgentsCall"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			$output = json_decode($data);

                        return($data);
		}

		/*
		 * Displaying Leads in hopper
		 * [[API: Function]] - goGetLeadsinHopper
		 * This application is used to get total number of leads in hopper
		*/

		public function API_GetLeadsinHopper() {

			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetLeadsinHopper"; #action performed by the [[API:Functions]]

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					echo $results["getLeadsinHopper"];
			 } else {
			   # An error occured
			   echo "0";
			 }
		}

		/*
		 * Displaying Dialable Leads
		 * [[API: Function]] - goGetTotalDialableLeads
		 * This application is used to get total number of dialable leads.
		*/

		public function API_goGetTotalDialableLeads() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalDialableLeads"; #action performed by the [[API:Functions]]

			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_URL, $url);
			 curl_setopt($ch, CURLOPT_POST, 1);
			 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 $data = curl_exec($ch);
			 curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					return $results["getTotalDialableLeads"];
			 } else {
			   # An error occurred
					$vars = 0;
					return $vars;
			 }
		}

		/*
		 * Displaying Total Active Leads
		 * [[API: Function]] - goGetTotalActiveLeads
		 * This application is used to get total number of active leads
		*/

		public function API_goGetTotalActiveLeads() {

			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalActiveLeads"; #action performed by the [[API:Functions]]
            $postfields["responsetype"] = responsetype;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					return $results["getTotalActiveLeads"];
			 } else {
			   # An error occurred
					$vars = 0;
					return $vars;
			 }
		}

		/*
		 * Displaying Total Active Campaigns
		 * [[API: Function]] - goGetActiveCampaignsToday
		 * This application is used to get total number of active leads
		*/

		public function API_goGetActiveCampaignsToday() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetActiveCampaignsToday"; #action performed by the [[API:Functions]]
            $postfields["responsetype"] = responsetype;
			
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
            return($data);
		}
		/*
		 * Displaying Call(s) Ringing
		 * [[API: Function]] - goGetRingingCalls
		 * This application is used to get calls ringing
		*/

		public function API_goGetRingingCalls() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetRingingCalls"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		/*
		 * Displaying Hopper Leads Warning
		 * [[API: Function]] - goGetHopperLeadsWarning
		 * This application is used to get the list of campaigns < 100
		*/
		public function API_goGetHopperLeadsWarning() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetHopperLeadsWarning"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		/*
		 * Displaying Online Agents Statuses
		 * [[API: Function]] - gogoGetAgentsMonitoringSummary
		 * This application is used to get the list online agents
		*/

		public function API_goGetAgentsMonitoringSummary() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetAgentsMonitoringSummary"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		/*
		 * Displaying Online Agents Statuses
		 * [[API: Function]] - goGetRealtimeAgentsMonitoring
		 * This application is used to get the list online agents
		 * for realtime monitoring
		*/
		public function API_goGetRealtimeAgentsMonitoring() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetRealtimeAgentsMonitoring"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		public function API_goGetIncomingQueue($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetIncomingQueue"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			$postfields["session_user"] = $session_user; #current user
			
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		/*
		 * Displaying Total Calls
		 * [[API: Function]] - getTotalcalls
		 * This application is used to get total calls.
		*/

		public function API_goGetTotalCalls() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalCalls"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
            
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		/*
		 * Displaying Total Answered Calls
		 * [[API: Function]] - goGetTotalAnsweredCalls
		 * This application is used to get total calls.
		*/

		public function API_goGetTotalAnsweredCalls() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAnsweredCalls"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
            
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}
		/*
		 * Displaying Total Dropped Calls
		 * [[API: Function]] - goGetTotalDroppedCalls
		 * This application is used to get total calls.
		*/

		public function API_goGetTotalDroppedCalls($session_use) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalDroppedCalls"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}
		/*
		 * Displaying Live Outbound
		 * [[API: Function]] - goGetLiveOutbound
		 * This application is used to get live outbound..
		*/

		public function API_goGetLiveOutbound() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetLiveOutbound"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			return $output;
		}

		/*
		 * Displaying Calls / Hour
		 * [[API: Function]] - getPerHourCall
		 * This application is used to get calls per hour.
		*/

		public function API_goGetCallsPerHour($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetCallsPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #action performed by the [[API:Functions]]
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}

		/*
		 * Displaying Sales / Hour
		 * [[API: Function]] - getPerHourSales
		 * This application is used to get calls per hour.
		*/

		public function API_goGetSalesPerHour() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetSalesPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #action performed by the [[API:Functions]]

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}


		/*
		 * Display Dropped Percentage
		 * [[API: Function]] - goGetDroppedPercentage
		 * This application is used to get dropped call percentage.
		*/
		public function API_goGetDroppedPercentage($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetDroppedPercentage"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			$postfields["session_user"] = $session_user; #action performed by the [[API:Functions]]

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			$output = json_decode($data);

			return $output;
			
		}

		/*
		 * Display SLA Percentage
		 * [[API: Function]] - goGetSLAPercentage
		 * This application is used to get dropped call percentage.
		*/
        public function API_goGetSLAPercentage() {
		$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetSLAPercentage"; #action performed by the [[API:Functions]]
                $postfields["responsetype"] = responsetype;

		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 $data = curl_exec($ch);
		 curl_close($ch);

		 $output = json_decode($data);

		 return $output;

            }

	/*
	 * Displaying Cluster Status
	 * [[API: Function]] - goGetClusterStatus
	 * This application is used to get cluster status
	*/

	public function API_goGetClusterStatus() {
		$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetClusterStatus"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 $data = curl_exec($ch);
		 curl_close($ch);

		 $output = json_decode($data);

		 return $output;
	}


// <<<=================== END OF DASHBOARD APIs =============>>>


	// get contact list
	public function GetContacts($userid, $search, $disposition_filter, $list_filter, $address_filter, $city_filter, $state_filter, $limit = 500, $search_customers = 0) {
		//$limit = 10;
		$output = $this->API_GetLeads($userid, $search, $disposition_filter, $list_filter, $address_filter, $city_filter, $state_filter, $limit, $search_customers);
	       if ($output->result=="success") {

       	   $columns = array($this->lh->translationFor('lead_id'), $this->lh->translationFor('full_name'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('status'), $this->lh->translationFor('action'));
	       $hideOnMedium = array($this->lh->translationFor('lead_id'), $this->lh->translationFor('status'));
	       $hideOnLow = array( $this->lh->translationFor('lead_id'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('status'));
		   $result = $this->generateTableHeaderWithItems($columns, "table_contacts", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			for($i=0;$i<=count($output->list_id);$i++) {
		   	//for($i=0;$i<=500;$i++) {
				if ($output->phone_number[$i] != "") {

				$action = $this->ActionMenuForContacts($output->lead_id[$i]);
				$result .= '<tr>
								<td><a class="edit-contact" data-id="'.$output->lead_id[$i].'">' .$output->lead_id[$i]. '</a></td>
								<td class="hide-on-low">' .$output->first_name[$i].' '.$output->middle_initial[$i].' '.$output->last_name[$i].'</td>
								<td class="hide-on-low">' .$output->phone_number[$i].'</td>
								<td class="hide-on-low hide-on-medium">' .$output->status[$i].'</td>
								<td>' .$action.'</td>
							</tr> ';
				}
			}

			return $result.'</table>';
       }else{
       		//display nothing
       }
	}

	public function getAllowedList($user_id) {
		$url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetAllowedList"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields['user_id'] = $user_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output->lists;
	}

	// get script
	public function getAgentScript($lead_id, $fullname, $first_name, $last_name, $middle_initial, $email, $phone_number, $alt_phone,
		$address1, $address2, $address3, $city, $province, $state, $postal_code, $country_code) {
		$url = gourl."/goViewScripts/goAPI.php"; # URL to GoAutoDial API filem (required)
         $postfields["goUser"] = goUser; #Username goes here. (required)
         $postfields["goPass"] = goPass; #Password goes here. (required)
         $postfields["goAction"] = "goViewAgentScript"; #action performed by the [[API:Functions]] (required0
         $postfields["responsetype"] = responsetype; #response type by the [[API:Functions]] (required)

         #required fields
         $postfields["lead_id"] = $lead_id; #Agent full anme(required)
         $postfields["fullname"] = $fullname; #Agent full anme(required)
         $postfields["first_name"] = $first_name; #Lead first_name (required)
         $postfields["last_name"] = $last_name; #Lead last_name (required)
         $postfields["middle_initial"] = $middle_initial; #Lead middle_initial (required)
         $postfields["email"] = $email; #Lead email (required)
         $postfields["phone_number"] = $phone_number; #Lead phone_number (required)
         $postfields["alt_phone"] = $alt_phone; #Lead alt_phone (required)
         $postfields["address1"] = $address1; #Lead address1 (required)
         $postfields["address2"] = $address2; #Lead address2 (required)
         $postfields["address3"] = $address3; #Lead address3 (required)
         $postfields["city"] = $city; #Lead city (required)
         $postfields["province"] = $province; #Lead province (required)
         $postfields["state"] = $state; #Lead state (required)
         $postfields["postal_code"] = $postal_code; #Lead postal_code (required)
         $postfields["country_code"] = $country_code; #Lead country_code(required)

         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);
        // var_dump($output);
        if ($output->result=="success") {
           # Result was OK!
                return $output->gocampaignScript;
         } else {
           # An error occured
                return $output->result;
        }

	}

	public function dropdownFormInputElement($id, $name, $options = array(), $currentValue, $required = false) {
		$requiredCode = $required ? "required" : "";
		$optionList = "";
		if (count($options) > 0) {
			foreach ($options as $k => $opt) {
				$isSelected = ($currentValue == $opt) ? "selected" : "";
				$optionList .= '<option value="'.$opt.'" '.$isSelected.'>'.$opt.'</option>';
			}
		}
		return '<select name="'.$name.'" id="'.$id.'" class="form-control '.$requiredCode.'">'.$optionList.'</select></div>';
	}

	public function getSessionAvatar() {
		$sessionAvatar = $_SESSION['avatar'];
		return $sessionAvatar;
	}

   public function API_goGetReports($pageTitle) {
		$url = gourl."/goJamesReports/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllDIDs"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["pageTitle"] = $pageTitle;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
	}


	/**
	 * Returns the standardized theme css for all pages.
	 */
	public function standardizedThemeCSS() {
		$css = "";
		$css .= '<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />'."\n"; // bootstrap basic css
		$css .= '<link href="css/creamycrm.css" rel="stylesheet" type="text/css" />'."\n"; // creamycrm css
		$css .= '<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />'."\n"; // circle buttons css
		$css .= '<link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />'."\n"; // ionicons
		$css .= '<link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />'."\n"; // bootstrap3 css
		$css .= '<link rel="stylesheet" href="css/fontawesome/css/font-awesome.min.css">'."\n"; // font-awesome css
		$css .= '<link rel="stylesheet" href="css/dashboard/simple-line-icons/css/simple-line-icons.css">'; // line css
		$css .= '<link rel="stylesheet" href="css/dashboard/animate.css/animate.min.css">'."\n"; // animate css
		$css .= '<link rel="stylesheet" href="css/dashboard/css/bootstrap.css" id="bscss">'; // bootstrap css
		$css .= '<link rel="stylesheet" href="css/dashboard/css/app.css" id="maincss">'."\n"; // app css
		$css .= '<link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">'."\n";
		$css .= '<link href="css/bootstrap-glyphicons.css" rel="stylesheet">'."\n";
		$css .= '<link rel="stylesheet" href="css/customizedLoader.css">'."\n"; // preloader css
		$css .= '<link rel="stylesheet" href="js/dashboard/sweetalert/dist/sweetalert.css">'."\n"; // sweetalert
   		$css .= '<link href="css/select2/select2.min.css" rel="stylesheet" type="text/css"/>'."\n";
   		$css .= '<link href="css/select2/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>'."\n";
   		$css .= '<link href="css/calendar.css" rel="stylesheet" type="text/css"/>'."\n";
   		
		/* JS that needs to be declared first */
		$css .= '<script src="js/jquery.min.js"></script>'."\n"; // required JS
		$css .= '<script src="js/bootstrap.min.js" type="text/javascript"></script>'."\n"; // required JS
		$css .= '<script src="js/jquery-ui.min.js" type="text/javascript"></script>'."\n"; // required JS
		$css .= '<script src="js/calendar_db.js" type="text/javascript" ></script>'."\n";

		return $css;
	}

	public function dataTablesTheme() {
		$css = "";
        $css .= '<link href="css/datatables/1.10.19/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />'."\n";
        $css .= '<link href="css/datatables/1.10.19/dataTables.jqueryui.min.css" rel="stylesheet" type="text/css" />'."\n";
        $css .= '<link href="css/datatables/1.10.19/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />'."\n";
		$css .= '<link href="css/datatables/1.10.19/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />'."\n";
		$css .= '<link href="css/datatables/1.10.19/responsive.jqueryui.min.css" rel="stylesheet" type="text/css" />'."\n";
		$css .= '<link href="css/datatables/1.10.19/rowGroup.dataTables.min.css" rel="stylesheet" type="text/css" />'."\n";
		$css .= '<style rel="stylesheet" type="text/css"> .content { padding-bottom: 75px; } </style>'."\n";		
		$css .= '<script src="js/datatables/1.10.19/jquery.dataTables.min.js" type="text/javascript"></script>'."\n";
		$css .= '<script src="js/datatables/1.10.19/dataTables.jqueryui.min.js" type="text/javascript"></script>'."\n";
		$css .= '<script src="js/datatables/1.10.19/dataTables.bootstrap.min.js" type="text/javascript"></script>'."\n";
		$css .= '<script src="js/datatables/1.10.19/dataTables.responsive.min.js" type="text/javascript"></script>'."\n";       
		$css .= '<script src="js/datatables/1.10.19/responsive.jqueryui.min.js" type="text/javascript"></script>'."\n";
		$css .= '<script src="js/datatables/1.10.19/dataTables.rowGroup.min.js" type="text/javascript"></script>'."\n";
		$css .= '<script src="js/datatables/1.10.19/sum.js" type="text/javascript"></script>'."\n";

		return $css;
	}	
	/**
	 * Returns the standardized theme js for all pages.
	 */
	public function standardizedThemeJS() {
		$js = '';
		$js .= '<script src="js/jquery.validate.min.js" type="text/javascript"></script>'."\n"; // forms and action js
		$js .= '<script src="js/dashboard/sweetalert/dist/sweetalert.min.js"></script>'."\n"; // sweetalert js
		$js .= '<script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>'."\n"; // bootstrap 3 js
		$js .= '<script src="adminlte/js/app.min.js" type="text/javascript"></script>'."\n"; // creamy app js
		$js .= '<script src="js/vue-avatar/vue.min.js" type="text/javascript"></script>'."\n";
		$js .= '<script src="js/vue-avatar/vue-avatar.min.js" type="text/javascript"></script>'."\n";
		$js .= '<script src="js/select2/select2.full.min.js" type="text/javascript" ></script>'."\n";
		$js .= "<script type='text/javascript'>

			var goOptions = {
				el: 'body',
				components: {
					'avatar': Avatar.Avatar,
					'rules': {
						props: ['items'],
						template: 'For example:' +
							'<ul id=\"example-1\">' +
							'<li v-for=\"item in items\"><b>{{ item.username }}</b> becomes <b>{{ item.initials }}</b></li>' +
							'</ul>'
					}
				},

				data: {
					items: []
				},

				methods: {
					initials: function(username, initials) {
						this.items.push({username: username, initials: initials});
					}
				}
			};
			var goAvatar = new Vue(goOptions);
		</script>\n";

		return $js;
	}

	/**
	 * Returns an Vue Avatar
	 */
	public function getVueAvatar($username, $avatar, $size, $topBar = false, $sideBar = false, $rounded = true) {
		$showAvatar = '';
		$initials = '';
		if (isset($avatar)) {
			if (preg_match("/(agent|goautodial)/i", $username) && preg_match("/defaultAvatar/i", $avatar)) {
				$showAvatar = '';
				$initials = 'initials="GO"';
			} else {
				$showAvatar = 'src="'.$avatar.'"';
				$initials = '';
			}
		}
		$topBarStyle = ($topBar) ? 'style="float: left; padding-right: 5px;"' : '';
		$sideBarStyle = ($sideBar) ? 'style="width: 100%; text-align: center;" display="inline-block"' : '';
		$roundedImg = (!$rounded) ? ':rounded="false"' : '';

		return '<avatar username="'.$username.'" '.$showAvatar.' '.$initials.' '.$topBarStyle.' '.$sideBarStyle.' '.$roundedImg.' :size="'.$size.'"></avatar>';
	}

	public function API_goGetAllCustomFields($list_id) {
		$url = gourl."/goCustomFields/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllCustomFields"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["list_id"] = $list_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
	}

	public function API_EmergencyLogout($username) {
		$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goEmergencyLogout"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		$postfields["goUserAgent"] = $username;
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		$output = json_decode($data);

		if ($output->result=="success") {
		   # Result was OK!
		    $status = "success";
		 } else {
		   # An error occured
			$status = $output->result;
		}

		return $status;
	}
	
	public function API_goGetGroupPermission($group) {
		$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetUserGroupInfo"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $group; #json. (required)
		$postfields["session_user"] = $_SESSION['user'];		
		
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 $data = curl_exec($ch);
		 curl_close($ch);
		 $output = json_decode($data);

		 return $output;
	}
	
	public function goGetPermissions($type = 'dashboard', $group) {
		$permissions = $this->API_goGetGroupPermission($group);
		if (!is_null($permissions)) {
			$types = explode(",", $type);
			if (count($types) > 1) {
				foreach ($types as $t) {
					if (array_key_exists($t, $permissions)) {
						$return->{$t} = $permissions->{$t};
					}
				}
			} else {
				if ($type == 'sidebar') {
					$return = $permissions;
				} else if (array_key_exists($type, $permissions)) {
					$return = $permissions->{$type};
				} else {
					$return = null;
				}
			}
		} else {
			$return = null;
		}
		return $return;
	}

	public function API_ListsStatuses($list_id) {
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetStatusesWithCountCalledNCalled"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		$postfields["list_id"] = $list_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		$output = json_decode($data);

		return $output;
	}

	public function API_ListsTimezone($list_id) {
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetTZonesWithCountCalledNCalled"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		$postfields["list_id"] = $list_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		$output = json_decode($data);

		return $output;
	}
	
	// Call Menu Options
	public function API_getIVROptions($menu_id) {

		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goGetIVROptions"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
		$postfields["menu_id"] = $menu_id;
		
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);

		//var_dump($output);
		return $output;

	}
	
	private function check_url($url) {
		$headers = @get_headers( $url);
		$headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;
		
		return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
	}
	

	// get dnc table
	public function GetDNC($search) {
		//$limit = 10;
		$output = $this->api->API_GetDNC($search);
	       if ($output->result=="success") {
			$columns = array($this->lh->translationFor("phone_number"), $this->lh->translationFor("campaign"), $this->lh->translationFor("action"));
			$hideOnMedium = array();
			$hideOnLow = array( $this->lh->translationFor("campaign") );
			$result = $this->generateTableHeaderWithItems($columns, "table_dnc", "display responsive no-wrap table-bordered table-striped", true, false);

			for($i=0;$i < count($output->phone_number);$i++) {
				$result .= '<tr>
								<td>' .$output->phone_number[$i]. '</td>
								<td>' .$output->campaign[$i].'</td>
								<td><div class="btn-group">
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
	                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
	                </button>
	                <ul class="dropdown-menu" role="menu">
	                    <li><a class="delete-dnc" href="#" data-id="'.$output->phone_number[$i].'" data-campaign="'.$output->campaign[$i].'">'.$this->lh->translationFor("delete").'</a></li>
	                </ul>
	            </div></td>
							</tr>';
			}

			return $result.'</table>';
       }else{
       		//display nothing
       }
	}
	
	public function API_LogActions($action, $user, $ip, $event_date, $details, $user_group, $db_query = '') {
		$url = gourl."/goAdminLogs/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goLogActions"; #action performed by the [[API:Functions]]. (required)
		$postfields["action"] = $action; #get DNC by this list_id search
		$postfields["user"] = $user;
		$postfields["ip_address"] = $ip;
		$postfields["details"] = $details;
		$postfields["user_group"] = $user_group;
		$postfields["db_query"] = $db_query;
		$postfields["responsetype"] = responsetype; #json. (required)
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
	
	public function API_getListAudioFiles() {
		$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllAudioFiles"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
	}
	
	public function API_getSMTPActivation() {
		$url = gourl."/goSMTP/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetSMTPActivation"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		if ($output->result == "success")
			return $output->data->value;
		else
			return '0';
	}
	
	private function getActionButtonForSMTP($status) {
	   $return = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">';
			if ($status == 1) {
				$return .= '<li><a class="activate-smtp" href="#" data-id="0" >'.$this->lh->translationFor("disable").'</a></li>';
			}else{
				$return .= '<li><a class="activate-smtp" href="#" data-id="1" >'.$this->lh->translationFor("enable").'</a></li>';
			}
		$return .= '</ul>
		</div>';
		return $return;
	}
	
	public function getCheckAll($action) {
		$return = '<div class="btn-group">
					<div class="checkbox c-checkbox" style="margin-right: 0; margin-left: 0;">
							<label><input class="check-all_'.$action.'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label>
						</div>
						<div>
							<a type="button" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 20px;">
							<center><span class="caret"></span></center>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a class="delete-multiple-'.$action.'" href="#" >Delete Selected</a></li>
							</ul>
						</div>
					</div>';
		$return .= '
		<script>
			$(document).ready(function() {
				$(document).on("change",".check-all_'.$action.'",function() {
					var box = $(this);
					if (box.is(":checked")) {
						$(".check_'.$action.'").prop("checked", true);
					}else{
						$(".check_'.$action.'").prop("checked", false);
					}
				});
			});
		</script>';
		return $return;
	}
	
	public function getAgentLog($user, $sdate, $edate) {
		$output = $this->api->API_getAgentLog($user, $sdate, $edate);
		//var_dump($output);
		if ($output->result=="success") {
			$columns = array($this->lh->translationFor('event_time'), $this->lh->translationFor('status'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('campaign'), $this->lh->translationFor('group'), $this->lh->translationFor('list_id'), $this->lh->translationFor('lead_id'), $this->lh->translationFor('term_reason'));
			$hideOnMedium = array();
			$hideOnLow = array( );
			$outbound = "";
			$outbound = $this->generateTableHeaderWithItems($columns, "table_outbound", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
			
			for($i=0;$i < count($output->outbound->campaign_id);$i++) {
				$outbound .= '<tr>
								<td>' .$output->outbound->event_time[$i]. '</a></td>
								<td>' .$output->outbound->status[$i].'</td>
								<td>' .$output->outbound->phone_number[$i].'</td>
								<td>' .$output->outbound->campaign_id[$i].'</td>
								<td>' .$output->outbound->user_group[$i].'</td>
								<td>' .$output->outbound->list_id[$i].'</td>
								<td>' .$output->outbound->lead_id[$i].'</td>
								<td>' .$output->outbound->term_reason[$i].'</td>
							</tr>';
			}
			$outbound .= "</table>";
			
			$columns = array($this->lh->translationFor('event_time'), $this->lh->translationFor('status'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('campaign'), $this->lh->translationFor('group'), $this->lh->translationFor('list_id'), $this->lh->translationFor('lead_id'), $this->lh->translationFor('term_reason'));
			$hideOnMedium = array();
			$hideOnLow = array( );
			$inbound = "";
			$inbound = $this->generateTableHeaderWithItems($columns, "table_inbound", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
			
			for($i=0;$i < count($output->inbound->campaign_id);$i++) {
				$inbound .= '<tr>
								<td>' .$output->inbound->call_date[$i]. '</a></td>
								<td>' .$output->inbound->queue_seconds[$i].'</td>
								<td>' .$output->inbound->status[$i].'</td>
								<td>' .$output->inbound->campaign_id[$i].'</td>
								<td>' .$output->inbound->user_group[$i].'</td>
								<td>' .$output->inbound->list_id[$i].'</td>
								<td>' .$output->inbound->lead_id[$i].'</td>
								<td>' .$output->inbound->term_reason[$i].'</td>
							</tr>';
			}
			$inbound .= "</table>";
			
			$columns = array($this->lh->translationFor('event_time'), $this->lh->translationFor('event'), $this->lh->translationFor('campaign'), $this->lh->translationFor('group'));
			$hideOnMedium = array();
			$hideOnLow = array( );
			$userlog = "";
			$userlog = $this->generateTableHeaderWithItems($columns, "table_userstat", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
			
			for($i=0;$i < count($output->userlog->user_log_id);$i++) {
				$userlog .= '<tr>
								<td>' .$output->userlog->event_date[$i]. '</a></td>
								<td>' .$output->userlog->event[$i].'</td>
								<td>' .$output->userlog->campaign_id[$i].'</td>
								<td>' .$output->userlog->user_group[$i].'</td>
							</tr>';
			}
			$userlog .= "</table>";
			
			$result = array($outbound, $inbound, $userlog);
		}else{
			$result = "";
		}
		
		return json_encode($result);
	}

	// Getting all Standard Fields
	public function API_getAllStandardFields() {
        $url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetStandardFields"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		if ($output->result == "success") {
			return $output->field_name;
		}else{
			return "EMPTY";
		}
	}

	public function API_getGOPackage() {
		$url = gourl."/goPackages/goAPI.php"; //URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; //Username goes here. (required)
		$postfields["goPass"] = goPass; //Password goes here. (required)
		$postfields["goAction"] = "goGetPackage"; //action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; //json. (required)

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
		
	}
	

	
	public function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c", "	");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", " ");
		$result = str_replace($escapers, $replacements, $value);

		return $result;
	}
	
	public function getSettingsAPIKey($type) {
		switch ($type) {
			case 'google':
				$return = $this->db->getSettingValueForKey(CRM_SETTING_GOOGLE_API_KEY);
				break;
			default:
				$return = false;
		}
		
		return $return;
	}
}

?>
