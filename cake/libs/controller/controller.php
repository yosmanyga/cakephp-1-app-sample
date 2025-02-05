<?php
/* SVN FILE: $Id: controller.php 4309 2007-01-20 21:26:42Z nate $ */
/**
 * Base controller class.
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.controller
 * @since			CakePHP v 0.2.9
 * @version			$Revision: 4309 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2007-01-20 15:26:42 -0600 (Sat, 20 Jan 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Include files
 */
	uses('controller' . DS . 'component', 'view' . DS . 'view');
/**
 * Controller
 *
 * Application controller (controllers are where you put all the actual code)
 * Provides basic functionality, such as rendering views (aka displaying templates).
 * Automatically selects model name from on singularized object class name
 * and creates the model object if proper class exists.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller
 *
 */
class Controller extends Object {
/**
 * Name of the controller.
 *
 * @var string
 * @access public
 */
	var $name = null;
/**
 * Stores the current URL (for links etc.)
 *
 * @var string
 * @access public
 */
	var $here = null;
/**
 * The webroot of the application
 *
 * @var string
 * @access public
 */
	var $webroot = null;
/**
 * Action to be performed.
 *
 * @var string
 * @access public
 */
	var $action = null;
/**
 * An array of names of models the particular controller wants to use.
 *
 * @var mixed A single name as a string or a list of names as an array.
 * @access protected
 */
	var $uses = false;
/**
 * An array of names of built-in helpers to include.
 *
 * @var mixed A single name as a string or a list of names as an array.
 * @access protected
 */
	var $helpers = array('Html');
/**
 * Parameters received in the current request, i.e. GET and POST data
 *
 * @var array
 * @access public
 */
	var $params = array();
/**
 * POST'ed model data
 *
 * @var array
 * @access public
 */
	var $data = array();
/**
 * Pagination defaults
 *
 * @var array
 * @access public
 */
	var $paginate = array('limit' => 20, 'page' => 1);
/**
 * Sub-path for view files
 *
 * @var string
 */
	var $viewPath = null;
/**
 * Sub-path for layout files
 *
 * @var string
 */
	var $layoutPath = null;
/**
 * Variables for the view
 *
 * @var array
 * @access public
 */
	var $viewVars = array();
/**
 * Web page title
 *
 * @var boolean
 * @access public
 */
	var $pageTitle = false;
/**
 * An array of model objects.
 *
 * @var array Array of model objects.
 * @access public
 */
	var $modelNames = array();
/**
 * Base url path
 *
 * @var string
 * @access public
 */
	var $base = null;
/**
 * Layout file to use (see /app/views/layouts/default.thtml)
 *
 * @var string
 * @access public
 */
	var $layout = 'default';
/**
 * Automatically render the view (the dispatcher checks for this variable before running render())
 *
 * @var boolean
 * @access public
 */
	var $autoRender = true;
/**
 * Automatically render the layout
 *
 * @var boolean
 * @access public
 */
	var $autoLayout = true;
/**
 * Array of components a controller will use
 *
 * @var array
 * @access public
 */
	var $components = array();
/**
 * The name of the View class a controller sends output to
 *
 * @var string
 * @access public
 */
	var $view = 'View';
/**
 * File extension for view templates. Defaults to Cake's conventional ".thtml".
 *
 * @var string
 * @access public
 */
	var $ext = '.ctp';
/**
 * Instance of $view class create by a controller
 *
 * @var object
 * @access private
 */
	var $__viewClass = null;
/**
 * The output of the requested action.  Contains either a variable
 * returned from the action, or the data of the rendered view;
 * You can use this var in Child classes afterFilter() to alter output.
 *
 * @var string
 * @access public
 */
	var $output = null;
/**
 * Automatically set to the name of a plugin.
 *
 * @var string
 * @access public
 */
	var $plugin = null;
/**
 * Used to set methods a controller will allow the View to cache
 *
 * @var mixed
 * @access public
 */
	var $cacheAction = false;
/**
 * Used to create cached instances of models a controller uses.
 * When set to true all models related to the controller will be cached,
 * this can increase performance in many cases
 *
 * @var boolean
 * @access public
 */
	var $persistModel = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $webservices = null;
/**
 * Enter description here...
 *
 * @var mixed
 */
	var $namedArgs = true;
/**
 * Enter description here...
 *
 * @var string
 */
	var $argSeparator = ':';
/**
 * Constructor.
 *
 */
	function __construct() {
		if ($this->name === null) {
			$r = null;

			if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
				die (__("Controller::__construct() : Can not get or parse my own class name, exiting."));
			}
			$this->name = $r[1];
		}

		if ($this->viewPath == null) {
			$this->viewPath = Inflector::underscore($this->name);
		}

		$this->modelClass = Inflector::classify($this->name);
		$this->modelKey = Inflector::underscore($this->modelClass);

		if (is_subclass_of($this, 'AppController')) {
			$appVars = get_class_vars('AppController');
			$uses = $appVars['uses'];
			$merge = array('components', 'helpers');

			if ($uses == $this->uses && !empty($this->uses)) {
				array_unshift($this->uses, $this->modelClass);
			} elseif ($this->uses !== null || $this->uses !== false) {
				$merge[] = 'uses';
			}

			foreach($merge as $var) {
				if (isset($appVars[$var]) && !empty($appVars[$var]) && is_array($this->{$var})) {
					$this->{$var} = array_merge($this->{$var}, array_diff($appVars[$var], $this->{$var}));
				}
			}
		}
		parent::__construct();
	}

	function _initComponents() {
		$component = new Component();
		$component->init($this);
	}
/**
 * Loads and instantiates models required by this controller.
 * If Controller::persistModel; is true, controller will create cached model instances on first request,
 * additional request will used cached models
 *
 * @return mixed true when single model found and instance created error returned if models not found.
 * @access public
 */
	function constructClasses() {
		if($this->uses === null || ($this->uses === array())){
			return false;
		}
		if (empty($this->passedArgs) || !isset($this->passedArgs['0'])) {
			$id = false;
		} else {
			$id = $this->passedArgs['0'];
		}
		$cached = false;
		$object = null;

		if($this->uses === false) {
			if(!class_exists($this->modelClass)){
				loadModel($this->modelClass);
			}
		}

		if (class_exists($this->modelClass) && ($this->uses === false)) {
			if ($this->persistModel === true) {
				$cached = $this->_persist($this->modelClass, null, $object);
			}

			if (($cached === false)) {
				$model =& new $this->modelClass($id);
				$this->modelNames[] = $this->modelClass;
				$this->{$this->modelClass} =& $model;

				if ($this->persistModel === true) {
					$this->_persist($this->modelClass, true, $model);
					$registry = ClassRegistry::getInstance();
					$this->_persist($this->modelClass . 'registry', true, $registry->_objects, 'registry');
				}
			} else {
				$this->_persist($this->modelClass . 'registry', true, $object, 'registry');
				$this->_persist($this->modelClass, true, $object);
				$this->modelNames[] = $this->modelClass;
			}
			return true;
		} elseif ($this->uses === false) {
			return $this->cakeError('missingModel', array(array('className' => $this->modelClass, 'webroot' => '', 'base' => $this->base)));
		}

		if ($this->uses) {
			$uses = is_array($this->uses) ? $this->uses : array($this->uses);

			foreach($uses as $modelClass) {
				$id = false;
				$cached = false;
				$object = null;
				$modelKey = Inflector::underscore($modelClass);

				if(!class_exists($modelClass)){
					loadModel($modelClass);
				}

				if (class_exists($modelClass)) {
					if ($this->persistModel === true) {
						$cached = $this->_persist($modelClass, null, $object);
					}

					if (($cached === false)) {
						$model =& new $modelClass($id);
						$this->modelNames[] = $modelClass;
						$this->{$modelClass} =& $model;

						if ($this->persistModel === true) {
							$this->_persist($modelClass, true, $model);
							$registry = ClassRegistry::getInstance();
							$this->_persist($modelClass . 'registry', true, $registry->_objects, 'registry');
						}
					} else {
						$this->_persist($modelClass . 'registry', true, $object, 'registry');
						$this->_persist($modelClass, true, $object);
						$this->modelNames[] = $modelClass;
					}
				} else {
					return $this->cakeError('missingModel', array(array('className' => $modelClass, 'webroot' => '', 'base' => $this->base)));
				}
			}
			return true;
		}
	}
/**
 * Redirects to given $url, after turning off $this->autoRender.
 * Please notice that the script execution is not stopped after the redirect.
 *
 * @param mixed $url A string or array-based URL pointing to another location
 *                   within the app, or an absolute URL
 * @param integer $status Optional HTTP status code
 * @param boolean $exit If true, exit() will be called after the redirect
 * @access public
 */
	function redirect($url, $status = null, $exit = false) {
		$this->autoRender = false;

		if (is_array($status)) {
			extract($status, EXTR_OVERWRITE);
		}

		if (function_exists('session_write_close')) {
			session_write_close();
		}

		if ($url !== null) {
			header('Location: ' . Router::url($url, true));
		}
		if (is_numeric($status) && $status > 0) {
			$codes = array(
				100 => "Continue",
				101 => "Switching Protocols",
				200 => "OK",
				201 => "Created",
				202 => "Accepted",
				203 => "Non-Authoritative Information",
				204 => "No Content",
				205 => "Reset Content",
				206 => "Partial Content",
				300 => "Multiple Choices",
				301 => "Moved Permanently",
				302 => "Found",
				303 => "See Other",
				304 => "Not Modified",
				305 => "Use Proxy",
				307 => "Temporary Redirect",
				400 => "Bad Request",
				401 => "Unauthorized",
				402 => "Payment Required",
				403 => "Forbidden",
				404 => "Not Found",
				405 => "Method Not Allowed",
				406 => "Not Acceptable",
				407 => "Proxy Authentication Required",
				408 => "Request Time-out",
				409 => "Conflict",
				410 => "Gone",
				411 => "Length Required",
				412 => "Precondition Failed",
				413 => "Request Entity Too Large",
				414 => "Request-URI Too Large",
				415 => "Unsupported Media Type",
				416 => "Requested range not satisfiable",
				417 => "Expectation Failed",
				500 => "Internal Server Error",
				501 => "Not Implemented",
				502 => "Bad Gateway",
				503 => "Service Unavailable",
				504 => "Gateway Time-out"
			);

			if (isset($codes[$status])) {
				header("HTTP/1.1 {$status} " . $codes[$status]);
			}
		}
		if ($exit) {
			exit();
		}
	}
/**
 * Saves a variable to use inside a template.
 *
 * @param mixed $one A string or an array of data.
 * @param mixed $two Value in case $one is a string (which then works as the key).
 * 				Unused if $one is an associative array, otherwise serves as the values to $one's keys.
 * @return void
 */
	function set($one, $two = null) {
		$data = array();

		if (is_array($one)) {
			if (is_array($two)) {
				$data = array_combine($one, $two);
			} else {
				$data = $one;
			}
		} else {
			$data = array($one => $two);
		}

		foreach($data as $name => $value) {
			if ($name == 'title') {
				$this->pageTitle = $value;
			} else {
				$this->viewVars[$name] = $value;
			}
		}
	}
/**
 * Internally redirects one action to another
 *
 * @param string $action The new action to be redirected to
 * @param mixed  Any other parameters passed to this method will be passed as
 *               parameters to the new action.
 */
	function setAction($action) {
		$this->action = $action;
		$args = func_get_args();
		unset($args[0]);
		call_user_func_array(array(&$this, $action), $args);
	}
/**
 * Returns number of errors in a submitted FORM.
 *
 * @return int Number of errors
 */
	function validate() {
		$args = func_get_args();
		$errors = call_user_func_array(array(&$this, 'validateErrors'), $args);

		if ($errors === false) {
			return 0;
		}
		return count($errors);
	}
/**
 * Validates a FORM according to the rules set up in the Model.
 *
 * @return int Number of errors
 */
	function validateErrors() {
		$objects = func_get_args();
		if (!count($objects)) {
			return false;
		}

		$errors = array();
		foreach($objects as $object) {
			$errors = array_merge($errors, $this->{$object->name}->invalidFields($object->data));
		}
		return $this->validationErrors = (count($errors) ? $errors : false);
	}
/**
 * Gets an instance of the view object & prepares it for rendering the output, then
 * asks the view to actualy do the job.
 *
 * @param unknown_type $action
 * @param unknown_type $layout
 * @param unknown_type $file
 * @return unknown
 */
	function render($action = null, $layout = null, $file = null) {
		$viewClass = $this->view;
		if ($this->view != 'View') {
			$viewClass = $this->view . 'View';
			loadView($this->view);
		}
		$this->beforeRender();
		$this->params['models'] = $this->modelNames;

		$this->__viewClass =& new $viewClass($this);
		if (!empty($this->modelNames)) {
			$count = count($this->modelNames);
			for ($i = 0; $i < $count; $i++) {
				$model = $this->modelNames[$i];
				if (!empty($this->{$model}->validationErrors)) {
					$this->__viewClass->validationErrors[$model] = &$this->{$model}->validationErrors;
				}
			}
		}

		$this->autoRender = false;
		return $this->__viewClass->render($action, $layout, $file);
	}
/**
 * Gets the referring URL of this request
 *
 * @param string $default Default URL to use if HTTP_REFERER cannot be read from headers
 * @param boolean $local If true, restrict referring URLs to local server
 * @access public
 */
	function referer($default = null, $local = false) {
		$ref = env('HTTP_REFERER');
		$base = FULL_BASE_URL . $this->webroot;

		if ($ref != null && defined('FULL_BASE_URL')) {
			if (strpos($ref, $base) === 0) {
				return substr($ref, strlen($base) - 1);
			} elseif(!$local) {
				return $ref;
			}
		}

		if ($default != null) {
			return $default;
		} else {
			return '/';
		}
	}
/**
 * Tells the browser not to cache the results of the current request
 *
 * @return void
 * @access public
 */
	function disableCache() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
/**
 * @deprecated
 * @see Controller::set
 */
	function _setTitle($pageTitle) {
		trigger_error(__('Deprecated: Use Controller::set("title", "...") instead'), E_USER_WARNING);
		$this->pageTitle = $pageTitle;
	}
/**
 * Shows a message to the user $time seconds, then redirects to $url
 * Uses flash.thtml as a layout for the messages
 *
 * @param string $message Message to display to the user
 * @param string $url Relative URL to redirect to after the time expires
 * @param int $time Time to show the message
 */
	function flash($message, $url, $pause = 1) {
		$this->autoRender = false;
		$this->autoLayout = false;
		$this->set('url', Router::url($url));
		$this->set('message', $message);
		$this->set('pause', $pause);
		$this->set('page_title', $message);

		if (file_exists(VIEWS . 'layouts' . DS . 'flash.ctp')) {
			$flash = VIEWS . 'layouts' . DS . 'flash.ctp';
		} elseif (file_exists(VIEWS . 'layouts' . DS . 'flash.thtml')) {
			$flash = VIEWS . 'layouts' . DS . 'flash.thtml';
		} elseif ($flash = fileExistsInPath(LIBS . 'view' . DS . 'templates' . DS . "layouts" . DS . 'flash.ctp')) {
		}
		$this->render(null, false, $flash);
	}
/**
 * This function creates a $fieldNames array for the view to use.
 * @todo Map more database field types to html form fields.
 * @todo View the database field types from all the supported databases.
 *
 */
	function generateFieldNames($data = null, $doCreateOptions = true) {
		$fieldNames = array();
		$model = $this->modelClass;
		$modelKey = $this->modelKey;
		$modelObj =& ClassRegistry::getObject($modelKey);

		foreach($modelObj->_tableInfo->value as $column) {
 			if ($modelObj->isForeignKey($column['name'])) {
				foreach($modelObj->belongsTo as $associationName => $assoc) {
					if($column['name'] == $assoc['foreignKey']) {
						$fkNames = $modelObj->keyToTable[$column['name']];
						$fieldNames[$column['name']]['table'] = $fkNames[0];
						$fieldNames[$column['name']]['label'] = Inflector::humanize($associationName);
						$fieldNames[$column['name']]['prompt'] = $fieldNames[$column['name']]['label'];
						$fieldNames[$column['name']]['model'] = Inflector::classify($associationName);
						$fieldNames[$column['name']]['modelKey'] = Inflector::underscore($modelObj->tableToModel[$fieldNames[$column['name']]['table']]);
						$fieldNames[$column['name']]['controller'] = Inflector::pluralize($fieldNames[$column['name']]['modelKey']);
						$fieldNames[$column['name']]['foreignKey'] = true;
						break;
					}
				}


			} else {
				$fieldNames[$column['name']]['label'] = Inflector::humanize($column['name']);
				$fieldNames[$column['name']]['prompt'] = $fieldNames[$column['name']]['label'];
			}
			$fieldNames[$column['name']]['tagName'] = $model . '/' . $column['name'];
			$fieldNames[$column['name']]['name'] = $column['name'];
			$fieldNames[$column['name']]['class'] = 'optional';
			$validationFields = $modelObj->validate;
			if (isset($validationFields[$column['name']])) {
				if (VALID_NOT_EMPTY == $validationFields[$column['name']]) {
					$fieldNames[$column['name']]['required'] = true;
					$fieldNames[$column['name']]['class'] = 'required';
					$fieldNames[$column['name']]['error'] = "Required Field";
				}
			}
			$lParenPos = strpos($column['type'], '(');
			$rParenPos = strpos($column['type'], ')');

			if (false != $lParenPos) {
				$type = substr($column['type'], 0, $lParenPos);
				$fieldLength = substr($column['type'], $lParenPos + 1, $rParenPos - $lParenPos - 1);
			} else {
				$type = $column['type'];
			}
			switch($type) {
				case "text":
					$fieldNames[$column['name']]['type'] = 'textarea';
					$fieldNames[$column['name']]['cols'] = '30';
					$fieldNames[$column['name']]['rows'] = '10';
				break;
				case "string":
					if (isset($fieldNames[$column['name']]['foreignKey'])) {
						$fieldNames[$column['name']]['type'] = 'select';
						$fieldNames[$column['name']]['options'] = array();
						$otherModelObj =& ClassRegistry::getObject($fieldNames[$column['name']]['modelKey']);
						if (is_object($otherModelObj)) {
							if ($doCreateOptions) {
								$fieldNames[$column['name']]['options'] = $otherModelObj->generateList();
							}
							$fieldNames[$column['name']]['selected'] = $data[$model][$column['name']];
						}
					} else {
						$fieldNames[$column['name']]['type'] = 'text';
					}
				break;
				case "boolean":
						$fieldNames[$column['name']]['type'] = 'checkbox';
				break;
				case "integer":
				case "float":
					if (strcmp($column['name'], $this->$model->primaryKey) == 0) {
						$fieldNames[$column['name']]['type'] = 'hidden';
					} else if(isset($fieldNames[$column['name']]['foreignKey'])) {
						$fieldNames[$column['name']]['type'] = 'select';
						$fieldNames[$column['name']]['options'] = array();

						$otherModelObj =& ClassRegistry::getObject($fieldNames[$column['name']]['modelKey']);
						if (is_object($otherModelObj)) {
							if ($doCreateOptions) {
								$fieldNames[$column['name']]['options'] = $otherModelObj->generateList();
							}
							$fieldNames[$column['name']]['selected'] = $data[$model][$column['name']];
						}
					} else {
						$fieldNames[$column['name']]['type'] = 'text';
					}

				break;
				case "enum":
					$fieldNames[$column['name']]['type'] = 'select';
					$fieldNames[$column['name']]['options'] = array();
					$enumValues = split(',', $fieldLength);

					foreach($enumValues as $enum) {
						$enum = trim($enum, "'");
						$fieldNames[$column['name']]['options'][$enum] = $enum;
					}
					$fieldNames[$column['name']]['selected'] = $data[$model][$column['name']];
				break;
				case "date":
				case "datetime":
				case "time":
				case "year":
					if (0 != strncmp("created", $column['name'], 7) && 0 != strncmp("modified", $column['name'], 8) && 0 != strncmp("updated", $column['name'], 7)) {
						$fieldNames[$column['name']]['type'] = $type;
						if (isset($data[$model][$column['name']])) {
							$fieldNames[$column['name']]['selected'] = $data[$model][$column['name']];
						} else {
							$fieldNames[$column['name']]['selected'] = null;
						}
					} else {
						unset($fieldNames[$column['name']]);
					}
				break;
				default:
				break;
			}
		}

		foreach($modelObj->hasAndBelongsToMany as $associationName => $assocData) {
			$otherModelKey = Inflector::underscore($assocData['className']);
			$otherModelObj = &ClassRegistry::getObject($otherModelKey);
			if ($doCreateOptions) {
				$fieldNames[$otherModelKey]['model'] = $associationName;
				$fieldNames[$otherModelKey]['label'] = "Related " . Inflector::humanize(Inflector::pluralize($associationName));
				$fieldNames[$otherModelKey]['prompt'] = $fieldNames[$otherModelKey]['label'];
				$fieldNames[$otherModelKey]['type'] = "select";
				$fieldNames[$otherModelKey]['multiple'] = "multiple";
				$fieldNames[$otherModelKey]['tagName'] = $associationName . '/' . $associationName;
				$fieldNames[$otherModelKey]['name'] = $associationName;
				$fieldNames[$otherModelKey]['class'] = 'optional';
				$fieldNames[$otherModelKey]['options'] = $otherModelObj->generateList();
				if (isset($data[$associationName])) {
					$fieldNames[$otherModelKey]['selected'] = $this->_selectedArray($data[$associationName], $otherModelObj->primaryKey);
				}
			}
		}

		return $fieldNames;
	}
/**
 * Converts POST'ed model data to a model conditions array, suitable for a find
 * or findAll Model query
 *
 * @param array $data POST'ed data organized by model and field
 * @param mixed $op A string containing an SQL comparison operator, or an array matching operators to fields
 * @param string $bool SQL boolean operator: AND, OR, XOR, etc.
 * @param boolean $exclusive If true, and $op is an array, fields not included in $op will not be included in the returned conditions
 * @return array An array of model conditions
 */
	function postConditions($data = array(), $op = null, $bool = 'AND', $exclusive = false) {
		if ((!is_array($data) || empty($data)) && empty($this->data)) {
			return null;
		} elseif (!empty($this->data)) {
			$data = $this->data;
		}

		$cond = array();
		if ($op === null) {
			$op = '';
		}

		foreach($data as $model => $fields) {
			foreach($fields as $field => $value) {
				$key = $model . '.' . $field;
				if (is_string($op)) {
					$cond[$key] = $this->__postConditionMatch($op, $value);
				} elseif (is_array($op)) {
					$opFields = array_keys($op);
					if (in_array($key, $opFields) || in_array($field, $opFields)) {
						if (in_array($key, $opFields)) {
							$cond[$key] = $this->__postConditionMatch($op[$key], $value);
						} else {
							$cond[$key] = $this->__postConditionMatch($op[$field], $value);
						}
					} elseif (!$exclusive) {
						$cond[$key] = $this->__postConditionMatch(null, $value);
					}
				}
			}
		}
		if ($bool != null && up($bool) != 'AND') {
			$cond = array($bool => $cond);
		}
		return $cond;
	}
/**
 * Private method used by postConditions
 *
 */
	function __postConditionMatch($op, $value) {

		if (is_string($op)) {
			$op = up(trim($op));
		}

		switch($op) {
			case '':
			case '=':
			case null:
				return $value;
			break;
			case 'LIKE':
				return 'LIKE %' . $value . '%';
			break;
			default:
				return $op . ' ' . $value;
			break;
		}
	}
/**
 * Cleans up the date fields of current Model.
 *
 */
	function cleanUpFields($modelClass = null) {
		if ($modelClass == null) {
			$modelClass = $this->modelClass;
		}
		foreach($this->{$modelClass}->_tableInfo->value as $field) {
			if ('date' == $field['type'] && isset($this->data[$modelClass][$field['name'] . '_year'])) {
				$newDate = $this->data[$modelClass][$field['name'] . '_year'] . '-';
				$newDate .= $this->data[$modelClass][$field['name'] . '_month'] . '-';
				$newDate .= $this->data[$modelClass][$field['name'] . '_day'];
				unset($this->data[$modelClass][$field['name'] . '_year']);
				unset($this->data[$modelClass][$field['name'] . '_month']);
				unset($this->data[$modelClass][$field['name'] . '_day']);
				unset($this->data[$modelClass][$field['name'] . '_hour']);
				unset($this->data[$modelClass][$field['name'] . '_min']);
				unset($this->data[$modelClass][$field['name'] . '_meridian']);
				$this->data[$modelClass][$field['name']] = $newDate;
				$this->data[$modelClass][$field['name']] = $newDate;

			} elseif('datetime' == $field['type'] && isset($this->data[$modelClass][$field['name'] . '_year'])) {
				$hour = $this->data[$modelClass][$field['name'] . '_hour'];

				if ($hour != 12 && (isset($this->data[$modelClass][$field['name'] . '_meridian']) && 'pm' == $this->data[$modelClass][$field['name'] . '_meridian'])) {
					$hour = $hour + 12;
				}

				$newDate  = $this->data[$modelClass][$field['name'] . '_year'] . '-';
				$newDate .= $this->data[$modelClass][$field['name'] . '_month'] . '-';
				$newDate .= $this->data[$modelClass][$field['name'] . '_day'] . ' ';
				$newDate .= $hour . ':' . $this->data[$modelClass][$field['name'] . '_min'] . ':00';
				unset($this->data[$modelClass][$field['name'] . '_year']);
				unset($this->data[$modelClass][$field['name'] . '_month']);
				unset($this->data[$modelClass][$field['name'] . '_day']);
				unset($this->data[$modelClass][$field['name'] . '_hour']);
				unset($this->data[$modelClass][$field['name'] . '_min']);
				unset($this->data[$modelClass][$field['name'] . '_meridian']);
				$this->data[$modelClass][$field['name']] = $newDate;
				$this->data[$modelClass][$field['name']] = $newDate;

			} elseif('time' == $field['type'] && isset($this->data[$modelClass][$field['name'] . '_hour'])) {
				$hour = $this->data[$modelClass][$field['name'] . '_hour'];

				if ($hour != 12 && (isset($this->data[$modelClass][$field['name'] . '_meridian']) && 'pm' == $this->data[$modelClass][$field['name'] . '_meridian'])) {
					$hour = $hour + 12;
				}

				$newDate = $hour . ':' . $this->data[$modelClass][$field['name'] . '_min'] . ':00';
				unset($this->data[$modelClass][$field['name'] . '_hour']);
				unset($this->data[$modelClass][$field['name'] . '_min']);
				unset($this->data[$modelClass][$field['name'] . '_meridian']);
				$this->data[$modelClass][$field['name']] = $newDate;
				$this->data[$modelClass][$field['name']] = $newDate;
			}
		}
	}
/**
 * Handles automatic pagination of model records
 *
 * @param mixed $object
 * @param mixed $scope
 * @param array $whitelist
 * @return array Model query results
 */
	function paginate($object = null, $scope = array(), $whitelist = array()) {

		if (is_array($object)) {
			$whitelist = $scope;
			$scope = $object;
			$object = null;
		}

		if (is_string($object)) {
			if (isset($this->{$object})) {
				$object = $this->{$object};
			} elseif (isset($this->{$this->modelClass}) && isset($this->{$this->modelClass}->{$object})) {
				$object = $this->{$this->modelClass}->{$object};
			} elseif (!empty($this->uses)) {
				for ($i = 0; $i < count($this->uses); $i++) {
					$model = $this->uses[$i];
					if (isset($this->{$model}->{$object})) {
						$object = $this->{$model}->{$object};
						break;
					}
				}
			}
		} elseif (empty($object) || $object == null) {
			if (isset($this->{$this->modelClass})) {
				$object = $this->{$this->modelClass};
			} else {
				$object = $this->{$this->uses[0]};
			}
		}

		if (!is_object($object)) {
			// Error: can't find object
			return array();
		}

		$options = am($this->params, $this->params['url'], $this->passedArgs);
		if (isset($this->paginate[$object->name])) {
			$defaults = $this->paginate[$object->name];
		} else {
			$defaults = $this->paginate;
		}

		if (isset($options['show'])) {
			$options['limit'] = $options['show'];
		}

		if (isset($options['sort']) && isset($options['direction'])) {
			$options['order'] = array($options['sort'] => $options['direction']);
		} elseif (isset($options['sort'])) {
			$options['order'] = array($options['sort'] => 'asc');
		}

		if (!empty($options['order']) && is_array($options['order'])) {
			$key = key($options['order']);
			if (strpos($key, '.') === false && $object->hasField($key)) {
				$options['order'][$object->name . '.' . $key] = $options['order'][$key];
				unset($options['order'][$key]);
			}
		}

		$vars = array('fields', 'order', 'limit', 'page', 'recursive');
		$keys = array_keys($options);
		$count = count($keys);

		for($i = 0; $i < $count; $i++) {
			if (!in_array($keys[$i], $vars)) {
				unset($options[$keys[$i]]);
			}
			if (empty($whitelist) && ($keys[$i] == 'fields' || $keys[$i] == 'recursive')) {
				unset($options[$keys[$i]]);
			} elseif (!empty($whitelist) && !in_array($keys[$i], $whitelist)) {
				unset($options[$keys[$i]]);
			}
		}

		$conditions = $fields = $order = $limit = $page = $recursive = null;
		$options = am($defaults, $options);
		if (isset($this->paginate[$object->name])) {
			$defaults = $this->paginate[$object->name];
		} else {
			$defaults = $this->paginate;
		}
		if (!isset($defaults['conditions'])) {
			$defaults['conditions'] = array();
		}

		extract(am(array('page' => 1, 'limit' => 20), $defaults, $options));
		if (is_array($scope) && !empty($scope)) {
			$conditions = am($conditions, $scope);
		} elseif (is_string($scope)) {
			$conditions = array($conditions, $scope);
		}
		$results = $object->findAll($conditions, $fields, $order, $limit, $page, $recursive);

		$count = $object->findCount($conditions);
		$paging = array(
			'page'		=> $page,
			'current'	=> count($results),
			'count'		=> $count,
			'prevPage'	=> ($page > 1),
			'nextPage'	=> ($count > ($page * $limit)),
			'pageCount'	=> ceil($count / $limit),
			'defaults'	=> am(array('limit' => 20, 'step' => 1), $defaults),
			'options'	=> $options
		);
		$this->params['paging'][$object->name] = $paging;

		if (!in_array('Paginator', $this->helpers) && !array_key_exists('Paginator', $this->helpers)) {
			$this->helpers[] = 'Paginator';
		}

		return $results;
	}
/**
 * Called before the controller action.  Overridden in subclasses.
 *
 */
	function beforeFilter() {
	}
/**
 * Called after the controller action is run, but before the view is rendered.  Overridden in subclasses.
 *
 */
	function beforeRender() {
	}
/**
 * Called after the controller action is run and rendered.  Overridden in subclasses.
 *
 */
	function afterFilter() {
	}
/**
 * This method should be overridden in child classes.
 *
 * @param string $method name of method called example index, edit, etc.
 * @return boolean
 */
	function _beforeScaffold($method) {
		return true;
	}
/**
 * This method should be overridden in child classes.
 *
 * @param string $method name of method called either edit or update.
 * @return boolean
 */
	function _afterScaffoldSave($method) {
		return true;
	}
/**
 * This method should be overridden in child classes.
 *
 * @param string $method name of method called either edit or update.
 * @return boolean
 */
	function _afterScaffoldSaveError($method) {
		return true;
	}
/**
 * This method should be overridden in child classes.
 * If not it will render a scaffold error.
 * Method MUST return true in child classes
 *
 * @param string $method name of method called example index, edit, etc.
 * @return boolean
 */
	function _scaffoldError($method) {
		return false;
	}
/**
 * Enter description here...
 *
 * @param unknown_type $data
 * @param unknown_type $key
 * @return unknown
 */
	function _selectedArray($data, $key = 'id') {
		if(!is_array($data)) {
			$model = $data;
			if(!empty($this->data[$model][$model])) {
				return $this->data[$model][$model];
			}
			if(!empty($this->data[$model])) {
				$data = $this->data[$model];
			}
		}
		$array = array();
		if(!empty($data)) {
			foreach($data as $var) {
				$array[$var[$key]] = $var[$key];
			}
		}
		return $array;
	}
}

?>