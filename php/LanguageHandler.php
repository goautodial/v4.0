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

require_once('CRMDefaults.php');
require_once('CRMUtils.php');
require_once('DatabaseConnectorFactory.php');
@include_once('Config.php');

define('CRM_LANGUAGE_DEFAULT_LOCALE', 'en_US');
define('CRM_LANGUAGE_BASE_DIR', DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR);

/**
 * Class to handle language and translations. LanguageHandler uses the Singleton pattern, thus gets instanciated by the LanguageHandler::getInstante().
 * This class is in charge of returning the right texts for the user's language.
 *
 * $lh = \creamy\LanguageHandler::getInstance();
 * $lh->translationFor("cancel"); --> returns "cancel" for en_US, "cancelar" for es_ES, etc...
 *
 * @author Ignacio Nieto Carvajal
 * @link URL http://digitalleaves.com
 */
 class LanguageHandler {
	
	/** Variables and constants */
	private $texts = array();
	private $locale;
	
	/** Creation and class lifetime management */
	
	/**
     * Returns the singleton instance of LanguageHandler.
     * @staticvar LanguageHandler $instance The LanguageHandler instance of this class.
     * @return LanguageHandler The singleton instance.
     */
    public static function getInstance($locale = null, $databaseConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL)
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static($locale, $databaseConnectorType);
        }

        return $instance;
    }
	
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($locale = null, $databaseConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL)
    {
		// initialize language and user locale
		if (isset($locale)) { $this->locale = $locale; } // if specified, set this locale.
		else if (\creamy\DatabaseConnectorFactory::instantiationAvailableForConnectorOfType($databaseConnectorType)) {
			// else if we have access to database, check the CRM locale setting.
			$dbConnector = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType($databaseConnectorType);
			$dbConnector->where("setting", CRM_SETTING_LOCALE);
			if ($settingRow = $dbConnector->getOne(CRM_SETTINGS_TABLE_NAME)) { $this->locale = $settingRow["value"]; }
		}
		// if locale could not be stablished, fallback to default.
		if (!isset($this->locale)) { $this->locale = CRM_LANGUAGE_DEFAULT_LOCALE; }
		
		// initialize map of language texts.
		$filepath = dirname(dirname(__FILE__)).CRM_LANGUAGE_BASE_DIR.$this->locale;
		$translations = $this->getTranslationsFromFile($filepath);
		if (!isset($translations)) { // fallback to en_US installation (everybody knows english, don't they?)
			$filepath = dirname(dirname(__FILE__)).CRM_LANGUAGE_BASE_DIR."en_US";
			$this->locale = "en_US";
			$translations = $this->getTranslationsFromFile($filepath);
		}
		$this->texts = $translations;
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
    
    /** Collection translation methods */
    
    /**
	 * Parses a translation file and returns the results as an array.
	 * @param String $filepath path for the file to parse.
	 * @return array an associative array containing the translations found in $filename or null if the file couldn't be found.
	 */
    protected function getTranslationsFromFile($filepath) {
	    if (file_exists($filepath)) { return parse_ini_file($filepath); }
	    else return null;
    }
    
    /**
	 * Adds a set of translations from a custom file to this language handler.
	 * If the file has conflicting or existing keys, they will be overwritten with the ones found in $filepath.
	 * @param String $filepath path for the file to parse and extract more translations.
	 * @return Bool true if the file was successfully added (even though it has no translations inside), false otherwise.
	 */
	public function addCustomTranslationsFromFile($filepath) {
		if (file_exists($filepath)) { 
			$translations = parse_ini_file($filepath);
			if (isset($translations) && is_array($translations)) {
				$this->texts = array_merge($this->texts, $translations);
				return true;
			}
		}
		return false;
	}
    
	/** Localization methods */
	
	/**
	 * Sets the locale of the LanguageHandler locale
	 * $locale String locale to set. If a language file for the specified language does not exists, the language will default to en_US.
	 */
	public function setLanguageHandlerLocale($locale) {
		$filepath = dirname(dirname(__FILE__)).CRM_LANGUAGE_BASE_DIR.$locale;
		if (!file_exists($filepath)) {
			// fallback to en_US installation (everybody knows english, don't they?)
			$filepath = dirname(dirname(__FILE__)).CRM_LANGUAGE_BASE_DIR."en_US";
			$this->locale = "en_US";
		} else {
			$this->locale = $locale;
		}
		$this->texts = parse_ini_file($filepath) or array();
	}
	
	/**
	 * Return the direct translation for the string term given as parameter, depending on the configured locale.
	 * @param $string String the string to search for in the translation table
	 * @return String the translated text.
	 */
	public function translationFor($string) {
		if (isset($this->texts[$string])) return $this->texts[$string];
		return $string;
	}
	
	/**
	 * Prints the direct translation for the string term given as parameter, depending on the configured locale.
	 * @param $string String the string to search for in the translation table
	 */
	public function translateText($string) {
		if (isset($this->texts[$string])) { 
			print $this->texts[$string]; 
		}
		else print $string;
	}
	
	/** 
	 * Gets the locale that's currently been used by this Language Handler.
	 */
	public function getLanguageHandlerLocale() { return $this->locale; }

	/**
	 * Returns the language name description for the LanguageHandler locale.
	 */
	public function getDisplayLanguage() { return \Locale::getDisplayLanguage($this->locale); }

	/**
	 * Returns the primary language code the LanguageHandler locale.
	 */
	public function getPrimaryLanguage() { return \Locale::getPrimaryLanguage($this->locale); }

	/**
	 * Gets the date format for the current locale, using "dd" for days, "mm" for months, "yyyy"
	 * for years, and "/" as separator (i.e: dd/mm/yyyy or yyyy/mm/dd).
	 */		
	public function getDateFormatForCurrentLocale() {
		// get basic format
		$df = new \IntlDateFormatter($this->locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
		$fmt = $df->getPattern();
		// apply needed transformations.
		$fmt = strtolower($fmt);
		if (substr_count($fmt, "m") == 1) $fmt = str_ireplace("m", "mm", $fmt);
		if (substr_count($fmt, "d") == 1) $fmt = str_ireplace("d", "dd", $fmt);
		if (substr_count($fmt, "y") == 1) $fmt = str_ireplace("y", "yyyy", $fmt);
		else if (substr_count($fmt, "y") == 2) $fmt = str_ireplace("yy", "yyyy", $fmt);
		return $fmt;
	}

	/**
	 * Translates a text, substituting all appearances of the terms passed in the "terms" parameter with their proper values in the translation table.
	 * @param $string String the text to translate.
	 * @param $terms Array an array of strings containing the terms to find and replace in the String $string.
	 */
	public function translationForTerms($string, $terms) {
		$translatedString = $string;
		// iterate through all the terms.
		foreach ($terms as $term) {
			$translation = $this->texts[$term];
			if (!empty($translation)) {
				$translatedString = str_replace($term, $translation, $translatedString);
			}
		}
		return $translatedString;
	}

	/**
	 * prints the translated text consisting on substituting all appearances of the terms passed in the "terms" parameter with their 
	 * proper values in the translation table.
	 * @param $string String the text to translate.
	 * @param $terms Array an array of strings containing the terms to find and replace in the String $string.
	 */
	public function translateTerms($string, $terms) {
		print $this->translationForTerms($string, $terms);
	}
	
	/**
	 * Gets an array with all the enabled languages in the CRM in the following form:
	 * [ "en_US" => "en_US (american english)", "es_ES" => "es_ES (spanish)", ... ]
	 */
	public static function getAvailableLanguages() {
		$files = scandir(\creamy\CRMUtils::creamyBaseDirectoryPath(false).CRM_LANGUAGE_BASE_DIR);
		$result = array();
		$ignoreLocales = array("datatables");
		foreach ($files as $file) {
			if (!is_dir($file) && (!\creamy\CRMUtils::startsWith($file, ".")) && (!in_array($file, $ignoreLocales))) {
				$localeCodeForFile = str_replace("_", "-", $file);
				$languageForLocale = utf8_decode(\Locale::getDisplayLanguage($localeCodeForFile));
				$result[$file] = "$file ($languageForLocale)";
			}
		}
		return $result;
	}
	
	/** Datatables */
	public function urlForDatatablesTranslation() {
		if ($language = $this->getDisplayLanguage()) {
			$fileindisk = \creamy\CRMUtils::creamyBaseDirectoryPath(false).CRM_LANGUAGE_BASE_DIR."datatables".DIRECTORY_SEPARATOR.$language.".json";
			if (file_exists($fileindisk)) {
				require_once('./php/CRMUtils.php');
				$langurl = \creamy\CRMUtils::creamyBaseURL()."/lang/datatables/".$language.".json";
				return $langurl;
			} 
		}
		return null;
	}
}
?>