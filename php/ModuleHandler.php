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
require_once('DbHandler.php');
require_once('CRMUtils.php');
require_once('Module.php');

// paths constants
define ('CRM_MODULES_MAIN_FILENAME', 'module.php');

// hooks constants
define ('CRM_MODULE_HOOK_DASHBOARD', 'dashboardHook');
define ('CRM_MODULE_HOOK_CUSTOMER_LIST_FIELDS', 'customerListFieldsHook');
define ('CRM_MODULE_HOOK_CUSTOMER_LIST_POPUP', 'customerListPopupHook');
define ('CRM_MODULE_HOOK_CUSTOMER_LIST_FOOTER', 'customerListFooterHook');
define ('CRM_MODULE_HOOK_CUSTOMER_LIST_ACTION', 'customerListActionHook');
define ('CRM_MODULE_HOOK_CUSTOMER_DETAIL', 'customerDetailHook');
define ('CRM_MODULE_HOOK_MESSAGE_LIST_FOOTER', 'messageListFooterHook');
define ('CRM_MODULE_HOOK_MESSAGE_LIST_ACTION', 'messageListActionHook');
define ('CRM_MODULE_HOOK_MESSAGE_DETAIL_FOOTER', 'messageDetailFooterHook');
define ('CRM_MODULE_HOOK_MESSAGE_DETAIL_ACTION', 'messageDetailActionHook');
define ('CRM_MODULE_HOOK_MESSAGE_COMPOSE_FOOTER', 'messageComposeFooterHook');
define ('CRM_MODULE_HOOK_MESSAGE_COMPOSE_ACTION', 'messageComposeActionHook');
define ('CRM_MODULE_HOOK_TASK_LIST_HOVER', 'taskListHoverHook');
define ('CRM_MODULE_HOOK_TASK_LIST_ACTION', 'taskListActionHook');
//edited for events
define ('CRM_MODULE_HOOK_EVENTS_LIST_HOVER', 'eventListHoverHook');
define ('CRM_MODULE_HOOK_EVENTS_LIST_ACTION', 'eventListActionHook');
define ('CRM_MODULE_HOOK_NOTIFICATIONS', 'notificationsHook');
define ('CRM_MODULE_HOOK_TOPBAR', 'topBarHook');
define ('CRM_MODULE_HOOK_TOPBAR_AGENT', 'topBarHookAgent');

// hook parameters
define ('CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_FIELDS', 'fields');
define ('CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_ID', 'customerid');
define ('CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_NAME', 'customername');
define ('CRM_MODULE_HOOK_PARAMETER_CUSTOMER_LIST_TYPE', 'customertype');
define ('CRM_MODULE_HOOK_PARAMETER_MESSAGES_FOLDER', 'folder');
define ('CRM_MODULE_HOOK_PARAMETER_MESSAGE_ID', 'messageid');

// hook result merging strategies
define ('CRM_MODULE_MERGING_STRATEGY_APPEND', 'append');
define ('CRM_MODULE_MERGING_STRATEGY_SEQUENCE', 'sequence');
define ('CRM_MODULE_MERGING_STRATEGY_SUM', 'sum');
define ('CRM_MODULE_MERGING_STRATEGY_JOIN', 'join');
define ('CRM_MODULE_MERGING_STRATEGY_AND', 'and');
define ('CRM_MODULE_MERGING_STRATEGY_OR', 'or');
define ('CRM_MODULE_MERGING_STRATEGY_FIRST', 'first');
define ('CRM_MODULE_MERGING_STRATEGY_LAST', 'last');
define ('CRM_MODULE_MERGING_STRATEGY_RANDOM', 'random');

// misc
define ('CRM_MODULE_JOB_SCHEDULING', 'scheduledJobForModule');

/**
 * ModuleReference. This class contains all data used to identify, instantiate and 
 */
class ModuleReference {
	/** Short name for the module. This name is equivalent to the directory name that hosts all the module structure */
	protected $moduleShortName;
	/** Module full path in disk (directory) */
	protected $moduleFullPath;
	/** The name of the main module class */
	protected $moduleClassName;
	/** The namespace this module belongs to */
	protected $moduleNamespace;
	/** The ReflectionClass of the module. */
	protected $reflectClass;
	/** The instance of the module. */
	protected $instance;

	/** Lifecycle */

	/** Constructor of the class
	 * @param $name String name of the module.
	 */
	public function __construct($shortName, $fullPath, $classname, $moduleNamespace = null) {
		// register an autoloader
		spl_autoload_register(array($this, "autoloader"));
		// module data.
		$this->moduleShortName = $shortName;
		$this->moduleFullPath = $fullPath;
		$this->moduleClassName = $classname;
		$this->moduleNamespace = $moduleNamespace;
		// try to instantiate the module.
		try {
			// create reflection class
			$fullclassPath = empty($moduleNamespace) ? $classname : $fullclassPath = $moduleNamespace."\\".$classname;
			$this->reflectClass = new \ReflectionClass($fullclassPath);
			// check that this is a submodule.
			if (!$this->reflectClass->isSubclassOf("\creamy\Module")) {
				throw new \Exception("Class $classname is not a valid Creamy module: $exception");
			}
		} catch (\Exception $exception) { // unable to instantiate the class.
			throw new \Exception("Class $classname not found: $exception");
		}
	}
		
	/**
	 * Tries to automatically load the class from the directory plugin.
	 */
	protected function autoloader($classname) {
		$mPath = realpath(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.CRM_MODULES_BASEDIR.DIRECTORY_SEPARATOR.$this->moduleClassName.DIRECTORY_SEPARATOR.CRM_MODULES_MAIN_FILENAME);
		@include_once($mPath);
	}
	
	/** Get and set variables */
	
	public function getModuleClassName() { return $this->moduleClassName; }
	public function getModuleNamespace() { return $this->moduleNamespace; }
	public function getModuleShortName() { return $this->moduleShortName; }
	public function getModuleName() { return $this->runMethodOnModule("getModuleName", null); }
	public function getModuleDescription() { return $this->runMethodOnModule("getModuleDescription", null); }
	public function getModuleVersion() { return $this->runMethodOnModule("getModuleVersion", null); }
	
	/** Interact with the module instance */
	
	/**
	 * Tries to execute the given method on the module.
	 * @return String a string containing the result if successful, or null if an error happened.
	 */
	public function runMethodOnModule($methodName, $args) {
		//error_log("running $methodName with args:\n".var_export($args, true));
		try {
			// check if the module has the method.
			if($this->reflectClass->hasMethod($methodName)) {
			   $reflectMethod = $this->reflectClass->getMethod($methodName);
			   // if the method is static, we don't need to instantiate the module
			   if($reflectMethod->isStatic()) {
				   if (is_array($args)) { 
					   $params = $this->generateParameterArrayForMethod($reflectMethod, $args);
					   $result = $reflectMethod->invokeArgs($params); 
				   } else { $result = $reflectMethod->invoke($args); }
			   }
			   else { // else we need to create an instance.
					$instance = $this->getModuleClassInstance();
					if (is_array($args)) { 
					    $params = $this->generateParameterArrayForMethod($reflectMethod, $args);
						$result = $reflectMethod->invokeArgs($instance, $params); 
					} else { $result = $reflectMethod->invoke($instance, $args); }
			   }
			   return $result;
			} else { return null; }			
		} catch (\Exception $exception) { return null; }	
	}
	
	/**
	 * Generates the array of parameters for a given method.
	 */
	protected function generateParameterArrayForMethod($reflectMethod, $args) {
		$params = array(); 
        foreach($reflectMethod->getParameters() as $param) { 
          	/* @var $param ReflectionParameter */ 
		  	if(isset($args[$param->getName()])) { 
			  	$params[$param->getName()] = $args[$param->getName()]; 
          	} else { 
            	$params[] = $param->getDefaultValue(); 
          	} 
        } 
		return $params;
	}
	
	/**
	 * Returns the instance of this module definition (if created). If not created, it creates a new one and returns it.
	 */
	public function getModuleClassInstance() {
		if (isset($this->instance)) { return $this->instance; }
		else {
			$this->instance = $this->reflectClass->newInstance();
			return $this->instance;
		}
	}
}

/**
 *  ModuleHandler.
 *  This class manages the loading and hooking of plugins. The ModuleHandler is used to load and invoke a module. 
 *  UIHandler uses the Singleton pattern, thus gets instanciated by the ModuleHandler::getInstante().
 */
class ModuleHandler {
	// variables.
	
	/** Module system enabled. */
	protected $enabled;
	
	/** Database handler */
	protected $db;
	
	/** An array containing the short names of all the modules. */
	protected $allModules = array();
	
	/** Active module names */
	protected $activeModules = array();
	
	/** Log of module loading */
	protected $moduleHandlerLog = "";
	
	// Lifecycle
	
	/**
     * Returns the singleton instance of ModuleHandler.
     * @staticvar ModuleHandler $instance The ModuleHandler instance of this class.
     * @return ModuleHandler The singleton instance.
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
    protected function __construct($dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL)
    {
	    $this->enabled = \creamy\ModuleHandler::moduleSystemEnabled($dbConnectorType);
		if ($this->enabled) {
		    // initialize database connector.
		    $this->db = new \creamy\DbHandler();
		    
			// initialize modules
			$this->loadModules();
		}
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

	/** Module listing and loading */

	/**
	 * Returns a list of all present modules (i.e: in the modules directory modules).
	 * @return Array an array of all module names as ModuleReference(s).
	 */
	public function listOfAllModules() {
		if (!$this->enabled) { return array(); }	    

		return $this->allModules;
	}

	/**
	 * Returns a list of all active modules.
	 * @return Array an array of active module names as strings.
	 */
	public function listOfActiveModules() {
		if (!$this->enabled) { return array(); }	    

		return $this->activeModuleNames;
	}

	/**
	 * Returns the module definition for the module named $name.
	 * @return Object an instance of a subclass of Module, if the module exists, is active and could be instantiated, null otherwise.
	 */
	public function getDefinitionOfModuleNamed($name) {
		if (!$this->enabled) { return null; }	    

		if (array_key_exists($name, $this->allModules)) {
			return $this->allModules[$name];
		} else { return null; }
	}

	/**
	 * Returns the instance of the module named $name.
	 * @return Object an instance of a subclass of Module, if the module exists, is active and could be instantiated, null otherwise.
	 */
	public function getInstanceOfModuleNamed($name) {
		if (!$this->enabled) { return null; }	    

		if (array_key_exists($name, $this->allModules)) {
			$moduleDefinition = $this->allModules[$name];
			return $moduleDefinition->getModuleClassInstance(); 
		} else { return null; }
	}

	/**
	 * Returns the module handler log.
	 */
	public function getModuleHandlerLog() { return $this->moduleHandlerLog; }

	/**
	 * Reads all modules and initializes the main ones.
	 */
	public function loadModules() {
		if (!$this->enabled) { return; }	    

		// Initialize structures
		$this->moduleHandlerLog = "Loading modules...\n";
		$this->allModules = array();
		$this->activeModules = $this->db->getActiveModules(); // array
		
		// Iterate through the modules folder and generate the module references for the active modules.
		$basedir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.CRM_MODULES_BASEDIR;
		$this->moduleHandlerLog .= "Looking for modules in path $basedir\n";
		$files = scandir($basedir);
		foreach ($files as $filename) { // iterate throuhg files/directories.
			$realpath = $basedir.DIRECTORY_SEPARATOR.$filename;
			// If it's a directory (except for "." & "..")
			if (is_dir($realpath) && (substr($filename, 0, 1 ) !== '.' )) { // possible module.
				$this->moduleHandlerLog .= "Analyzing $filename...\n";
				$mainModuleFilePath = $realpath.DIRECTORY_SEPARATOR.CRM_MODULES_MAIN_FILENAME;
				$classHierarchy = \creamy\ModuleHandler::getClassHierarchyInFile($mainModuleFilePath);
				$this->moduleHandlerLog .= "Class hierarchy: ".var_export($classHierarchy, true)."\n";
				if (is_array($classHierarchy) && count($classHierarchy) > 0) {
					$classes = isset($classHierarchy["classes"]) ? $classHierarchy["classes"] : array();
					$namespace = isset($classHierarchy["namespace"]) ? $classHierarchy["namespace"] : null;
					
					// Look for a valid module class.
					foreach ($classes as $class) {
						if (strtolower($class["type"]) == "class") { // we have a class here.
							try { // try to generate the module definition and add it to the modules.
								$classname = $class["name"];
								$this->moduleHandlerLog .= "Instantiating module with short name: $filename, file path: $mainModuleFilePath, class name: $classname, namespace: $namespace\n";
								$def = new \creamy\ModuleReference($filename, $mainModuleFilePath, $classname, $namespace);
								$this->allModules[$classname] = $def;
								$this->moduleHandlerLog .= "Successfully loaded module $classname from $realpath\n";
								break;	 // success. We don't need to look any further in this file.					
							} catch (\Exception $exception) { // Log module loading failure.
								$this->moduleHandlerLog .= "Unable to load module ".$class["name"]." from $realpath: ".$exception->getMessage()."\n";
							}
						}
					}
				}
			}
		}
		//error_log("Module loading process:\n".$this->moduleHandlerLog);
	}
	
	/** Module activation and status */
	
	/** 
	 * Returns true if a module is active, false otherwise.
	 *
	 * @param $moduleClassName name of the module class to check. 
	 */
	public function moduleIsEnabled($moduleName) {
		if (!$this->enabled) { return false; }	    

		return in_array($moduleName, $this->activeModules, true);
	}

	/**
	 * Enables or disables a module, changing the database also.
	 * @param String $moduleName the name of the module to enable/disable.
	 * @param String/Bool $enabled 1/true if module should be enabled, 0/false otherwise.
	 * @return Bool true if module was successfully enabled/disabled, false otherwise.
	 */
	public function enableOrDisableModule($moduleName, $enabled) {
		if (!$this->enabled) { return false; }	    

		// avoid nasty things here...
	    $sanitized = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $moduleName);

		// add or remove from active modules
		if (filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) { // enable module
			if (!in_array($sanitized, $this->activeModules)) { $this->activeModules[] = $sanitized; }
			$methodToCall = "activateModule";
		} else { // disable module
			if ($key = array_search($sanitized, $this->activeModules)) { unset($this->activeModules[$key]); }
			$methodToCall = "deactivateModule";
		}
		// invoke the activate/deactivate module method.
	    if (array_key_exists($sanitized, $this->allModules)) {
			$moduleDefinition = $this->allModules[$sanitized];
			$moduleDefinition->runMethodOnModule($methodToCall, null);
		}
		// remove from database
		return $this->db->changeModuleStatus($sanitized, $enabled);

	}
	
	/** Module modification, deletion or update */    
    
    /** 
	 * Deletes a module. 
     */
    public function deleteModule($shortName) {
		if (!$this->enabled) { return false; }	    

		// avoid nasty things here...
	    $sanitized = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $shortName);

   	    // remove from active modules.
		if ( ($key = array_search($shortName, $this->activeModules)) !== false) { unset($this->activeModules[$key]); } 

	    // remove from current entries.
	    if (array_key_exists($sanitized, $this->allModules)) { 
			$moduleDefinition = $this->allModules[$sanitized];
			$moduleDefinition->runMethodOnModule("uninstallModule", null);
		    unset($this->allModules[$sanitized]); 
		}
	    
	    // delete files and directory structure.
	    $path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.CRM_MODULES_BASEDIR.DIRECTORY_SEPARATOR.$sanitized;
		\creamy\CRMUtils::deleteDirectoryRecursively($path);
	    	    
	    return true;
    }
    
    /**
	 * Configures a module with a given set of settings.
	 * Returns true if settings were updated successfully, false otherwise.
	 */
    public function configureModule($moduleName, $settings) {
		if (!$this->enabled) { return null; }	    

	    if ($this->moduleIsEnabled($moduleName)) {
		    $instance = $this->getInstanceOfModuleNamed($moduleName);
		    if (isset($instance)) {
			    $moduleSettingDefinitions = $instance->moduleSettings();
			    foreach ($moduleSettingDefinitions as $setting => $type) {
				    if (array_key_exists($setting, $settings)) {
					    $newValue = $settings[$setting];
					    switch ($type) {
						    case CRM_SETTING_TYPE_STRING:
								break;
							case CRM_SETTING_TYPE_INT:
								$newValue = intval($newValue);
								break;
							case CRM_SETTING_TYPE_FLOAT:
								$newValue = floatval($newValue);
								break;
							case CRM_SETTING_TYPE_BOOL:
								$newValue = (bool) $newValue;
								break;
							case CRM_SETTING_TYPE_DATE:
								require_once('LanguageHandler.php');
								// transform date in current format to valid MySQL date.
								$dateFormat = \creamy\LanguageHandler::getInstance()->getDateFormatForCurrentLocale();
								$dateFormat = str_ireplace("dd", "d", $dateFormat);
								$dateFormat = str_ireplace("mm", "m", $dateFormat);
								$dateFormat = str_ireplace("yyyy", "Y", $dateFormat);
								$dateFormat = str_ireplace("yy", "Y", $dateFormat);
								$date = \DateTime::createFromFormat($dateFormat, $newValue);
								$newValue = $date->format("Y-m-d");
								break;
					    }
					    if (!$instance->setSettingValue($setting, $newValue)) { return false; }
				    } else if ($type == CRM_SETTING_TYPE_BOOL) { // catch unchecked boxes and unset the setting.
					    if (!$instance->setSettingValue($setting, "0")) { return false; }
				    }
			    }
			    return true;
		    }
	    }
	    return false;
    }
    
    /** Module interaction */
    
    public function activeModulesInstances() {
		if (!$this->enabled) { return array(); }	    

	    $result = array();
	    foreach ($this->activeModules as $activeModule) {
		    if (array_key_exists($activeModule, $this->allModules)) {
			    $moduleDefinition = $this->allModules[$activeModule];
			    $result[$activeModule] = $moduleDefinition->getModuleClassInstance();
		    }
	    }
	    return $result;
    }
    
    public function modulesWithSettings() {
		if (!$this->enabled) { return array(); }	    

	    $result = array();
	    foreach ($this->activeModules as $activeModule) {
		    if (array_key_exists($activeModule, $this->allModules)) {
			    $moduleDefinition = $this->allModules[$activeModule];
			    $settings = $moduleDefinition->runMethodOnModule("moduleSettings", null);
			    if (!empty($settings)) $result[$activeModule] = $moduleDefinition->getModuleClassInstance();
		    }
	    }
	    return $result;
    }
    
    public function applyHookOnActiveModules($hookname, $args, $mergeStrategy = CRM_MODULE_MERGING_STRATEGY_APPEND) {
		// safety checks.
		if (!$this->enabled) 					{ return null; } // module system is not enabled   
		if (count($this->activeModules) < 1) 	{ return null; } // no active modules, nothing to do.

	    // depending on the merge strategy we'll generate different results.
	    if ($mergeStrategy == CRM_MODULE_MERGING_STRATEGY_SEQUENCE) {
		    return $this->sequenceHookResults($hookname, $args);
	    } else {
		    $results = array();
			foreach ($this->activeModules as $modulename) {
				$result = $this->applyHookOnModule($modulename, $hookname, $args);
				if (isset($result)) { $results[] = $result; }
			}
			return $this->mergeHookResultsWithStrategy($results, $mergeStrategy);
		}
    }
    
    public function applyHookOnModule($modulename, $hookname, $args) {
		if (!$this->enabled) { return null; }	    
		
		$md = $this->getDefinitionOfModuleNamed($modulename);
		if (isset($md)) {
			return $md->runMethodOnModule($hookname, $args);
		}
		return null;
    }
   
    protected function mergeHookResultsWithStrategy($results, $mergeStrategy) {
	    switch ($mergeStrategy) {
		    case CRM_MODULE_MERGING_STRATEGY_APPEND:
		    	$appended = "";
		    	foreach ($results as $result) {
			    	if (is_string($result)) { $appended .= $result; }
			    	else { $appended .= var_export($result, true); }
		    	}
		    	return $appended;
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_SUM:
		    	$sum = 0.0;
		    	foreach ($results as $result) { $sum += floatval($result); }
		    	return $sum;
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_JOIN:
		    	$joined = array();
		    	foreach ($results as $result) { $joined = array_merge($joined, $result); }
		    	return $joined;		    	
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_AND:
		    	$andResult = true;
		    	foreach ($results as $result) { $andResult = $andResult and (bool)$result; }		    	
		    	return $andResult;
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_OR:
		    	$oredResult = true;
		    	foreach ($results as $result) { $oredResult = $oredResult or (bool)$result; }		    	
		    	return $oredResult;
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_FIRST:
				if (is_array($results) && count($results) > 0) { return reset($results); }
				else { return $results; }
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_LAST:
				if (is_array($results) && count($results) > 0) { return end($results); }
				else { return $results; }
		    	break;
		    case CRM_MODULE_MERGING_STRATEGY_RANDOM:
				if (is_array($results) && count($results) > 0) {
					require_once('RandomStringGenerator.php');
					$rnd = new \creamy\RandomStringGenerator();
					$nmb = $rnd->getRandomInteger(0, count($results)-1);
					return array_values($results)[$nmb];
				}
				else { return $results; }
		    	break;
	    }
    }
    
    protected function sequenceHookResults($hookname, $args) {
	    // initialize values
	    $result = $args;
	    $resultWrapped = false;
	    
	    // iterate through all modules.
	    foreach ($this->activeModules as $modulename) {
		    $temp = $this->applyHookOnModule($modulename, $hookname, $result);
		    if (!$this->arraysHaveSameKeys($args, $temp)) {
			    // in this case we only support wrapping in the first argument.
			    $key = array_keys($args)[0];
			    $temp = array($key => $temp);
			    $resultWrapped = true;
		    }
		    if (isset($temp)) { $result = $temp; }
	    }
	    // unwrap result if wrapped
	    if ($resultWrapped) { reset($result); $result = current($result); }
	    // return result.
	    return $result;
    }    
    
    /** Job scheduling */
    
    public function scheduleJobsOnActiveModules($period) {
		// safety checks.
		if (!$this->enabled) 					{ return null; } // module system is not enabled   
		if (count($this->activeModules) < 1) 	{ return null; } // no active modules, nothing to do.

		foreach ($this->activeModules as $modulename) {
			$md = $this->getDefinitionOfModuleNamed($modulename);
			if (isset($md)) { return $md->runMethodOnModule(CRM_MODULE_JOB_SCHEDULING, array("period" => $period)); }
		}
    }
    
    
    /** Custom utils */
    
    /**
	 * Helper function to decide if the output generated by a module can be
	 * feeded back as the input for the next module in sequence. This will
	 * only be feasible if outputArray has at least the same keys that $outputArray.
	 * @param Array $inputArray the array that contains the keys we want to check.
	 * @param Array $outputArray the array we want to look for keys.
	 * @return true if $outputArray has at least the same keys that $inputArray.
	 */
    protected function arraysHaveSameKeys($inputArray, $outputArray) {
	    $n1 = count($inputArray);
	    $n2 = count($outputArray);
	    if ($n1 === $n2) { // same number of keys
		    foreach (array_keys($inputArray) as $key) {
			    if (!array_key_exists($key, $outputArray)) { return false; }
		    }
		    return true;
	    } else { return false; }
    }

    	
	/** 
	 * Returns true if the module system is enabled. 
	 * @return true if the module system is enabled. False otherwise.
	 */
	public static function moduleSystemEnabled($dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL) {
		require_once('DatabaseConnectorFactory.php');
		if ($dbConnector = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType($dbConnectorType)) {
			$dbConnector->where("setting", CRM_SETTING_MODULE_SYSTEM_ENABLED);
			$result = $dbConnector->getOne(CRM_SETTINGS_TABLE_NAME);
			if (isset($result) && array_key_exists("value", $result)) { return $result["value"]; }
		}
		return false;
	}
	
	/**
	 * Returns the main module page link for a given module with given parameters.
	 * @param String $module 	name of the module.
	 * @param Array  $args 		arguments for the module main page.
	 * @param String $basedir	a relative path to be prefixed to the module page (for inclusion in different contexts).
	 */
	public static function pageLinkForModule($module, $args, $basedir = "") {
		return $basedir."modulepage.php?module_name=".urlencode($module)."&args=".\creamy\ModuleHandler::encodeModuleArguments($args);
	}
	
	/**
	 * Encodes some parameters for inclusion as arguments in the link for a module's main page.
	 * @param Array $args The arguments for a module, as an associative array "option" => "value".
	 * @return String a string to be passed to the link requesting a module main page.
	 */
	public static function encodeModuleArguments($args) {
		if (empty($args) || !is_array($args)) { return ""; }
		return urlencode(json_encode($args));
	}
	
	/**
	 * Decodes a url parameter string into module arguments.
	 * @param String $encodedString encoded string containing a urlencode(json_encode($array)) of an $array.
	 * @return array An array with the decoded parameters for the module contained in the encoded string.
	 */
	public static function decodeModuleArguments($encodedString) {
		if (empty($encodedString)) { return array(); }
		return json_decode(urldecode($encodedString), true);
	}
	
	/**
     * 
     * Looks what classes and namespaces are defined in that file and returns the first found
     * @param String $file Path to file
     * @author Tivie http://stackoverflow.com/users/295342/tivie
     * @author Ignacio Nieto Carvajal <contact@digitalleaves.com> (modifications)
     * @return Returns NULL if none is found or an array with namespaces and classes found in file: 
	  'namespace' => string 'this\is\a\really\big\namespace\for\testing\dont\you\think' (length=57)
	  'classes' => 
	    array
	      0 => 
	        array
	          'name' => string 'yes_it_is' (length=9)
	          'type' => string 'CLASS' (length=5)
	      1 => 
	        array
	          'name' => string 'damn_too_big' (length=12)
	          'type' => string 'ABSTRACT CLASS' (length=14)
	      2 => 
	        array
	          'name' => string 'fodass' (length=6)
	          'type' => string 'INTERFACE' (length=9)
     */
    protected static function getClassHierarchyInFile($file) {
        $classes = $nsPos = $final = array();
        $foundNS = FALSE;
        $ii = 0;

        if (!file_exists($file)) return NULL;

        $er = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);

        $php_code = file_get_contents($file);
        $tokens = token_get_all($php_code);
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) 
        {
            if(!$foundNS && $tokens[$i][0] == T_NAMESPACE)
            {
                $nsPos[$ii]['start'] = $i;
                $foundNS = TRUE;
            }
            elseif( $foundNS && ($tokens[$i] == ';' || $tokens[$i] == '{') )
            {
                $nsPos[$ii]['end']= $i;
                $ii++;
                $foundNS = FALSE;
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) 
            {
                if($i-4 >=0 && $tokens[$i - 4][0] == T_ABSTRACT)
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'ABSTRACT CLASS');
                }
                else
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'CLASS');
                }
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'INTERFACE');
            }
        }
        error_reporting($er);
        if (empty($classes)) return NULL;

        if(!empty($nsPos))
        {
            foreach($nsPos as $k => $p)
            {
                $ns = '';
                for($i = $p['start'] + 1; $i < $p['end']; $i++)
                    $ns .= $tokens[$i][1];

                $ns = trim($ns);
                $final[$k] = array('namespace' => $ns, 'classes' => $classes[$k+1]);
            }
            $classes = $final;
        }
        return $classes[0];
    }   
}
?>