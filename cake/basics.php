<?php
/* SVN FILE: $Id: basics.php 4349 2007-01-30 16:34:22Z nate $ */
/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
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
 * @subpackage		cake.cake
 * @since			CakePHP v 0.2.9
 * @version			$Revision: 4349 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2007-01-30 10:34:22 -0600 (Tue, 30 Jan 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Basic defines for timing functions.
 */
	define('SECOND', 1);
	define('MINUTE', 60 * SECOND);
	define('HOUR', 60 * MINUTE);
	define('DAY', 24 * HOUR);
	define('WEEK', 7 * DAY);
	define('MONTH', 30 * DAY);
	define('YEAR', 365 * DAY);
/**
 * Patch for PHP < 5.0
 */
	if (version_compare(phpversion(), '5.0') < 0) {
		 eval ('
		function clone($object)
		{
			return $object;
		}');
	}
/**
 * Loads all models.
 */
	function loadModels() {
		if(!class_exists('Model')){
			require LIBS . 'model' . DS . 'model.php';
		}
		$path = Configure::getInstance();
		if (!class_exists('AppModel')) {
			if (file_exists(APP . 'app_model.php')) {
				require(APP . 'app_model.php');
			} else {
				require(CAKE . 'app_model.php');
			}
			Overloadable::overload('AppModel');
		}
		$loadedModels = array();

		foreach($path->modelPaths as $path) {
			foreach(listClasses($path) as $model_fn) {
				list($name) = explode('.', $model_fn);
				$className = Inflector::camelize($name);
				$loadedModels[$model_fn] = $model_fn;

				if (!class_exists($className)) {
					require($path . $model_fn);
					list($name) = explode('.', $model_fn);
					Overloadable::overload(Inflector::camelize($name));
				}
			}
		}
		return $loadedModels;
	}
/**
 * Loads all plugin models.
 *
 * @param  string  $plugin Name of plugin
 * @return
 */
	function loadPluginModels($plugin) {
		if(!class_exists('AppModel')){
			loadModel();
		}
		$pluginAppModel = Inflector::camelize($plugin . '_app_model');
		$pluginAppModelFile = APP . 'plugins' . DS . $plugin . DS . $plugin . '_app_model.php';
		if (!class_exists($pluginAppModel)) {
			if (file_exists($pluginAppModelFile)) {
				require($pluginAppModelFile);
			} else {
				die(sprintf(__("Plugins must have a class named %s", true), $pluginAppModel));
			}
			Overloadable::overload($pluginAppModel);
		}

		$pluginModelDir = APP . 'plugins' . DS . $plugin . DS . 'models' . DS;

		foreach(listClasses($pluginModelDir)as $modelFileName) {
			list($name) = explode('.', $modelFileName);
			$className = Inflector::camelize($name);

			if (!class_exists($className)) {
				require($pluginModelDir . $modelFileName);
				Overloadable::overload($className);
			}
		}
	}
/**
 * Loads custom view class.
 *
 */
	function loadView($viewClass) {
		if (!class_exists($viewClass . 'View')) {
			$paths = Configure::getInstance();
			$file = Inflector::underscore($viewClass) . '.php';

			foreach($paths->viewPaths as $path) {
				if (file_exists($path . $file)) {
					 return require($path . $file);
				}
			}

			if ($viewFile = fileExistsInPath(LIBS . 'view' . DS . $file)) {
				if (file_exists($viewFile)) {
					require($viewFile);
					return true;
				} else {
					return false;
				}
			}
		}
	}
/**
 * Loads a model by CamelCase name.
 */
	function loadModel($name = null) {
		if(!class_exists('Model')){
			require LIBS . 'model' . DS . 'model.php';
		}
		if (!class_exists('AppModel')) {
			if (file_exists(APP . 'app_model.php')) {
				require(APP . 'app_model.php');
			} else {
				require(CAKE . 'app_model.php');
			}
			Overloadable::overload('AppModel');
		}

		if(strpos($name, '.') !== false){
			list($plugin, $name) = explode('.', $name);
		}

		if (!is_null($name) && !class_exists($name)) {
			$className = $name;
			$name = Inflector::underscore($name);
			$models = Configure::read('Models');
			if(is_array($models)) {
				if(array_key_exists($className, $models)) {
					require($models[$className]['path']);
					Overloadable::overload($className);
					return true;
				} elseif(isset($models['Core']) && array_key_exists($className, $models['Core'])) {
					require($models['Core'][$className]['path']);
					Overloadable::overload($className);
					return true;
				}
			}

			$paths = Configure::getInstance();
			foreach($paths->modelPaths as $path) {
				if (file_exists($path . $name . '.php')) {
					Configure::store('Models', 'class.paths', array($className => array('path' => $path . $name . '.php')));
					require($path . $name . '.php');
					Overloadable::overload($className);
					return true;
				}
			}
			return false;
		} else {
			return true;
		}
	}

	function paths(){
		$directories = Configure::getInstance();
		$paths = array();

		foreach($directories->modelPaths as $path) {
			$paths['Models'][] = $path;
		}
		foreach($directories->behaviorPaths as $path) {
			$paths['Behaviors'][] = $path;
		}
		foreach($directories->controllerPaths as $path) {
			$paths['Controllers'][] = $path;
		}
		foreach($directories->componentPaths as $path) {
			$paths['Components'][] = $path;
		}
		foreach($directories->helperPaths as $path) {
			$paths['Helpers'][] = $path;
		}

		if(!class_exists('Folder')){
			uses('Folder');
		}

		$folder =& new Folder(APP.'plugins'.DS);
		$plugins = $folder->ls();
		$classPaths = array('models', 'models'.DS.'behaviors',  'controllers', 'controllers'.DS.'components', 'views'.DS.'helpers');

		foreach($plugins[0] as $plugin){
			foreach($classPaths as $path){
				if(strpos($path, DS) !== false){
					$key = explode(DS, $path);
					$key = $key[1];
				} else {
					$key = $path;
				}
				$folder->path = APP.'plugins'.DS.$plugin.DS.$path;
				$paths[Inflector::camelize($plugin)][Inflector::camelize($key)][] = $folder->path;
			}
		}
		return $paths;
	}
/**
 * Loads all controllers.
 */
	function loadControllers() {
		$paths = Configure::getInstance();
		if (!class_exists('AppController')) {
			if (file_exists(APP . 'app_controller.php')) {
				require(APP . 'app_controller.php');
			} else {
				require(CAKE . 'app_controller.php');
			}
		}
		$loadedControllers = array();

		foreach($paths->controllerPaths as $path) {
			foreach(listClasses($path) as $controller) {
				list($name) = explode('.', $controller);
				$className = Inflector::camelize($name);
				if (loadController($name)) {
					$loadedControllers[$controller] = $className;
				}
			}
		}
		return $loadedControllers;
	}
/**
 * Loads a controller and its helper libraries.
 *
 * @param  string  $name Name of controller
 * @return boolean Success
 */
	function loadController($name) {
		if (!class_exists('AppController')) {
			if (file_exists(APP . 'app_controller.php')) {
				require(APP . 'app_controller.php');
			} else {
				require(CAKE . 'app_controller.php');
			}
		}
		if ($name === null) {
			return true;
		}
		if(strpos($name, '.') !== false){
			list($plugin, $name) = explode('.', $name);
		}

		$className = $name . 'Controller';
		if (!class_exists($className)) {
			$name = Inflector::underscore($className);
			$controllers = Configure::read('Controllers');
			if(is_array($controllers)) {
				if(array_key_exists($className, $controllers)) {
					require($controllers[$className]['path']);
					return true;
				} elseif(isset($controllers['Core']) && array_key_exists($className, $controllers['Core'])) {
					require($controllers['Core'][$className]['path']);
					return true;
				}
			}

			$paths = Configure::getInstance();
			foreach($paths->controllerPaths as $path) {
				if (file_exists($path . $name . '.php')) {
					Configure::store('Controllers', 'class.paths', array($className => array('path' => $path . $name . '.php')));
					require($path . $name . '.php');
					return true;
				}
			}

			if ($controllerFilename = fileExistsInPath(LIBS . 'controller' . DS . $name . '.php')) {
				if (file_exists($controllerFilename)) {
					Configure::store('Controllers\'][\'Core', 'class.paths', array($className => array('path' => $controllerFilename)));
					require($controllerFilename);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
/**
 * Loads a plugin's controller.
 *
 * @param  string  $plugin Name of plugin
 * @param  string  $controller Name of controller to load
 * @return boolean Success
 */
	function loadPluginController($plugin, $controller) {
		$pluginAppController = Inflector::camelize($plugin . '_app_controller');
		$pluginAppControllerFile = APP . 'plugins' . DS . $plugin . DS . $plugin . '_app_controller.php';
		if (!class_exists($pluginAppController)) {
			if (file_exists($pluginAppControllerFile)) {
				require($pluginAppControllerFile);
			} else {
				return false;
			}
		}

		if (empty($controller)) {
			if (!class_exists($plugin . 'Controller')) {
				if (file_exists(APP . 'plugins' . DS . $plugin . DS . 'controllers' . DS . $plugin . '_controller.php')) {
					require(APP . 'plugins' . DS . $plugin . DS . 'controllers' . DS . $plugin . '_controller.php');
					return true;
				}
			}
		}

		if (!class_exists($controller . 'Controller')) {
			$controller = Inflector::underscore($controller);
			$file = APP . 'plugins' . DS . $plugin . DS . 'controllers' . DS . $controller . '_controller.php';

			if (file_exists($file)) {
				require($file);
				return true;
			} elseif (file_exists(APP . 'plugins' . DS . $plugin . DS . 'controllers' . DS . $plugin . '_controller.php')) {
				require(APP . 'plugins' . DS . $plugin . DS . 'controllers' . DS . $plugin . '_controller.php');
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
/**
 * Loads a helper
 *
 * @param  string  $name Name of helper
 * @return boolean Success
 */
	function loadHelper($name) {
		if (!class_exists('AppHelper')) {
			if (file_exists(APP . 'app_helper.php')) {
				require(APP . 'app_helper.php');
			} else {
				require(CAKE . 'app_helper.php');
			}
			Overloadable::overload('AppHelper');
		}

		if ($name === null) {
			return true;
		}
		if(strpos($name, '.') !== false){
			list($plugin, $name) = explode('.', $name);
		}

		$className = $name . 'Helper';
		if (!class_exists($className)) {
			$name = Inflector::underscore($name);
			$helpers = Configure::read('Helpers');

			if(is_array($helpers)) {
				if(array_key_exists($className, $helpers)) {
					require($helpers[$className]['path']);
					return true;
				} elseif(isset($helpers['Core']) && array_key_exists($className, $helpers['Core'])) {
					require($helpers['Core'][$className]['path']);
					return true;
				}
			}

			$paths = Configure::getInstance();
			foreach($paths->helperPaths as $path) {
				if (file_exists($path . $name . '.php')) {
					Configure::store('Helpers', 'class.paths', array($className => array('path' => $path . $name . '.php')));
					require($path . $name . '.php');
					return true;
				}
			}

			if ($helperFilename = fileExistsInPath(LIBS . 'view' . DS . 'helpers' . DS . $name . '.php')) {
				if (file_exists($helperFilename)) {
					Configure::store('Helpers\'][\'Core', 'class.paths', array($className => array('path' => $helperFilename)));
					require($helperFilename);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return true;
		}
	}
/**
 * Loads a plugin's helper
 *
 * @param  string  $plugin Name of plugin
 * @param  string  $helper Name of helper to load
 * @return boolean Success
 */
	function loadPluginHelper($plugin, $helper) {
		if (!class_exists($helper . 'Helper')) {
			$helper = Inflector::underscore($helper);
			$file = APP . 'plugins' . DS . $plugin . DS . 'views' . DS . 'helpers' . DS . $helper . '.php';
			if (file_exists($file)) {
				require($file);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
/**
 * Loads a component
 *
 * @param  string  $name Name of component
 * @return boolean Success
 */
	function loadComponent($name) {

		if ($name === null) {
			return true;
		}
		if(strpos($name, '.') !== false){
			list($plugin, $name) = explode('.', $name);
		}

		$className = $name . 'Component';
		if (!class_exists($className)) {
			$name = Inflector::underscore($name);
			$components = Configure::read('Components');

			if(is_array($components)) {
				if(array_key_exists($className, $components)) {
					require($components[$className]['path']);
					return true;
				} elseif(isset($components['Core']) && array_key_exists($className, $components['Core'])) {
					require($components['Core'][$className]['path']);
					return true;
				}
			}

			$paths = Configure::getInstance();
			foreach($paths->componentPaths as $path) {
				if (file_exists($path . $name . '.php')) {
					Configure::store('Components', 'class.paths', array($className => array('path' => $path . $name . '.php')));
					require($path . $name . '.php');
					return true;
				}
			}

			if ($componentFilename = fileExistsInPath(LIBS . 'controller' . DS . 'components' . DS . $name . '.php')) {
				if (file_exists($componentFilename)) {
					Configure::store('Components\'][\'Core', 'class.paths', array($className => array('path' => $componentFilename)));
					require($componentFilename);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return true;
		}
	}
/**
 * Loads a plugin's component
 *
 * @param  string  $plugin Name of plugin
 * @param  string  $helper Name of component to load
 * @return boolean Success
 */
	function loadPluginComponent($plugin, $component) {
		if (!class_exists($component . 'Component')) {
			$component = Inflector::underscore($component);
			$file = APP . 'plugins' . DS . $plugin . DS . 'controllers' . DS . 'components' . DS . $component . '.php';
			if (file_exists($file)) {
				require($file);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
/**
 * Loads a behavior
 *
 * @param  string  $name Name of component
 * @return boolean Success
 */
	function loadBehavior($name) {
		$paths = Configure::getInstance();

		if ($name === null) {
			return true;
		}

		if (!class_exists($name . 'Behavior')) {
			$name = Inflector::underscore($name);

			foreach($paths->behaviorPaths as $path) {
				if (file_exists($path . $name . '.php')) {
					require($path . $name . '.php');
					return true;
				}
			}

			if ($behavior_fn = fileExistsInPath(LIBS . 'model' . DS . 'behaviors' . DS . $name . '.php')) {
				if (file_exists($behavior_fn)) {
					require($behavior_fn);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return true;
		}
	}
/**
 * Returns an array of filenames of PHP files in given directory.
 *
 * @param  string $path Path to scan for files
 * @return array  List of files in directory
 */
	function listClasses($path) {
		$dir = opendir($path);
		$classes=array();
		while(false !== ($file = readdir($dir))) {
			if ((substr($file, -3, 3) == 'php') && substr($file, 0, 1) != '.') {
				$classes[] = $file;
			}
		}
		closedir($dir);
		return $classes;
	}
/**
 * Loads configuration files
 *
 * @return boolean Success
 */
	function config() {
		$args = func_get_args();
		foreach($args as $arg) {
			if (('database' == $arg) && file_exists(CONFIGS . $arg . '.php')) {
				include_once(CONFIGS . $arg . '.php');
			} elseif (file_exists(CONFIGS . $arg . '.php')) {
				include_once(CONFIGS . $arg . '.php');

				if (count($args) == 1) {
					return true;
				}
			} else {
				if (count($args) == 1) {
					return false;
				}
			}
		}
		return true;
	}
/**
 * Loads component/components from LIBS.
 *
 * Example:
 * <code>
 * uses('flay', 'time');
 * </code>
 *
 * @uses LIBS
 */
	function uses() {
		$args = func_get_args();
		$c = func_num_args();

		for ($i = 0; $i < $c; $i++) {
			require_once(LIBS . strtolower($args[$i]) . '.php');
		}
	}
/**
 * Require given files in the VENDORS directory. Takes optional number of parameters.
 *
 * @param string $name Filename without the .php part.
 *
 */
	function vendor($name) {
		$args = func_get_args();
		$c = func_num_args();

		for ($i = 0; $i < $c; $i++) {
			$arg = $args[$i];
			if (file_exists(APP . 'vendors' . DS . $arg . '.php')) {
				require_once(APP . 'vendors' . DS . $arg . '.php');
			} else {
				require_once(VENDORS . $arg . '.php');
			}
		}
	}
/**
 * Normalizes a string or array list
 *
 * @param mixed $list
 * @param boolean $assoc If true, $list will be converted to an associative array
 * @param string $sep If $list is a string, it will be split into an array with $sep
 * @param boolean $trim If true, separated strings will be trimmed
 * @return array
 */
	function normalizeList($list, $assoc = true, $sep = ',', $trim = true) {
		trigger_error('normalizeList Deprecated: use Set::normalize');
		if (is_string($list)) {
			$list = explode($sep, $list);
			if ($trim) {
				$list = array_map('trim', $list);
			}
			if ($assoc) {
				return normalizeList($list);
			}
		} elseif (is_array($list)) {
			$keys = array_keys($list);
			$count = count($keys);
			$numeric = true;

			if (!$assoc) {
				for ($i = 0; $i < $count; $i++) {
					if (!is_int($keys[$i])) {
						$numeric = false;
						break;
					}
				}
			}
			if (!$numeric || $assoc) {
				$newList = array();
				for ($i = 0; $i < $count; $i++) {
					if (is_int($keys[$i])) {
						$newList[$list[$keys[$i]]] = null;
					} else {
						$newList[$keys[$i]] = $list[$keys[$i]];
					}
				}
				$list = $newList;
			}
		}
		return $list;
	}
/**
 * Prints out debug information about given variable.
 *
 * Only runs if DEBUG level is non-zero.
 *
 * @param boolean $var		Variable to show debug information for.
 * @param boolean $show_html	If set to true, the method prints the debug data in a screen-friendly way.
 */
	function debug($var = false, $showHtml = false) {
		if (Configure::read() > 0) {
			print "\n<pre class=\"cake_debug\">\n";
			ob_start();
			print_r($var);
			$var = ob_get_clean();

			if ($showHtml) {
				$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
			}
			print "{$var}\n</pre>\n";
		}
	}
/**
 * Returns microtime for execution time checking
 *
 * @return integer
 */
	if (!function_exists('getMicrotime')) {
		function getMicrotime() {
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}
	}
/**
 * Sorts given $array by key $sortby.
 *
 * @param  array	$array
 * @param  string  $sortby
 * @param  string  $order  Sort order asc/desc (ascending or descending).
 * @param  integer $type
 * @return mixed
 */
	if (!function_exists('sortByKey')) {
		function sortByKey(&$array, $sortby, $order = 'asc', $type = SORT_NUMERIC) {
			if (!is_array($array)) {
				return null;
			}

			foreach($array as $key => $val) {
				$sa[$key] = $val[$sortby];
			}

			if ($order == 'asc') {
				asort($sa, $type);
			} else {
				arsort($sa, $type);
			}

			foreach($sa as $key => $val) {
				$out[] = $array[$key];
			}
			return $out;
		}
	}
/**
 * Combines given identical arrays by using the first array's values as keys,
 * and the second one's values as values. (Implemented for back-compatibility with PHP4)
 *
 * @param  array $a1
 * @param  array $a2
 * @return mixed Outputs either combined array or false.
 */
	if (!function_exists('array_combine')) {
		function array_combine($a1, $a2) {
			$a1 = array_values($a1);
			$a2 = array_values($a2);
			$c1 = count($a1);
			$c2 = count($a2);

			if ($c1 != $c2) {
				return false;
			}
			if ($c1 <= 0) {
				return false;
			}

			$output=array();
			for($i = 0; $i < $c1; $i++) {
				$output[$a1[$i]] = $a2[$i];
			}
			return $output;
		}
	}
/**
 * Convenience method for htmlspecialchars.
 *
 * @param string $text
 * @return string
 */
	function h($text) {
		if (is_array($text)) {
			return array_map('h', $text);
		}
		return htmlspecialchars($text);
	}
/**
 * Returns an array of all the given parameters.
 *
 * Example:
 * <code>
 * a('a', 'b')
 * </code>
 *
 * Would return:
 * <code>
 * array('a', 'b')
 * </code>
 *
 * @return array
 */
	function a() {
		$args = func_get_args();
		return $args;
	}
/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 * <code>
 * aa('a','b')
 * </code>
 *
 * Would return:
 * <code>
 * array('a'=>'b')
 * </code>
 *
 * @return array
 */
	function aa() {
		$args = func_get_args();
		for($l = 0, $c = count($args); $l < $c; $l++) {
			if ($l + 1 < count($args)) {
				$a[$args[$l]] = $args[$l + 1];
			} else {
				$a[$args[$l]] = null;
			}
			$l++;
		}
		return $a;
	}
/**
 * Convenience method for echo().
 *
 * @param string $text String to echo
 */
	function e($text) {
		echo $text;
	}
/**
 * Convenience method for strtolower().
 *
 * @param string $str String to lowercase
 */
	function low($str) {
		return strtolower($str);
	}
/**
 * Convenience method for strtoupper().
 *
 * @param string $str String to uppercase
 */
	function up($str) {
		return strtoupper($str);
	}
/**
 * Convenience method for str_replace().
 *
 * @param string $search String to be replaced
 * @param string $replace String to insert
 * @param string $subject String to search
 */
	function r($search, $replace, $subject) {
		return str_replace($search, $replace, $subject);
	}
/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see	debug()
 * @param array	$var
 */
	function pr($var) {
		if (Configure::read() > 0) {
			echo "<pre>";
			print_r($var);
			echo "</pre>";
		}
	}
/**
 * Display parameter
 *
 * @param  mixed  $p Parameter as string or array
 * @return string
 */
	function params($p) {
		if (!is_array($p) || count($p) == 0) {
			return null;
		} else {
			if (is_array($p[0]) && count($p) == 1) {
				return $p[0];
			} else {
				return $p;
			}
		}
	}
/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 */
	function am() {
		$r = array();
		foreach(func_get_args()as $a) {
			if (!is_array($a)) {
				$a = array($a);
			}
			$r = array_merge($r, $a);
		}
		return $r;
	}
/**
 * Returns the REQUEST_URI from the server environment, or, failing that,
 * constructs a new one, using the PHP_SELF constant and other variables.
 *
 * @return string URI
 */
	function setUri() {
		if (env('HTTP_X_REWRITE_URL')) {
			$uri = env('HTTP_X_REWRITE_URL');
		} elseif(env('REQUEST_URI')) {
			$uri = env('REQUEST_URI');
		} else {
			if (env('argv')) {
				$uri = env('argv');

				if (defined('SERVER_IIS')) {
					$uri = BASE_URL . $uri[0];
				} else {
					$uri = env('PHP_SELF') . '/' . $uri[0];
				}
			} else {
				$uri = env('PHP_SELF') . '/' . env('QUERY_STRING');
			}
		}
		return $uri;
	}
/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsisten environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 */
	function env($key) {

		if ($key == 'HTTPS') {
			if (isset($_SERVER) && !empty($_SERVER)) {
				return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
			} else {
				return (strpos(env('SCRIPT_URI'), 'https://') === 0);
			}
		}

		if ($key == 'SCRIPT_NAME') {
			if (env('CGI_MODE')) {
				$key = 'SCRIPT_URL';
			}
		}

		$val = null;
		if (isset($_SERVER[$key])) {
			$val = $_SERVER[$key];
		} elseif (isset($_ENV[$key])) {
			$val = $_ENV[$key];
		} elseif (getenv($key) !== false) {
			$val = getenv($key);
		}

		if ($key == 'REMOTE_ADDR' && $val == env('SERVER_ADDR')) {
			$addr = env('HTTP_PC_REMOTE_ADDR');
			if ($addr != null) {
				$val = $addr;
			}
		}

		if ($val !== null) {
			return $val;
		}

		switch ($key) {
			case 'DOCUMENT_ROOT':
				$offset = 0;
				if (!strpos(env('SCRIPT_NAME'), '.php')) {
					$offset = 4;
				}
				return substr(env('SCRIPT_FILENAME'), 0, strlen(env('SCRIPT_FILENAME')) - (strlen(env('SCRIPT_NAME')) + $offset));
			break;
			case 'PHP_SELF':
				return r(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
			case 'CGI_MODE':
				return (substr(php_sapi_name(), 0, 3) == 'cgi');
			break;
		}
		return null;
	}
/**
 * Writes data into file.
 *
 * If file exists, it will be overwritten. If data is an array, it will be join()ed with an empty string.
 *
 * @param string $fileName File name.
 * @param mixed  $data String or array.
 */
	if (!function_exists('file_put_contents')) {
		function file_put_contents($fileName, $data) {
			if (is_array($data)) {
				$data = join('', $data);
			}
			$res = @fopen($fileName, 'w+b');
			if ($res) {
				$write = @fwrite($res, $data);
				if($write === false) {
					return false;
				} else {
					return $write;
				}
			}
			return false;
		}
	}
/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path	File path within /tmp to save the file.
 * @param  mixed  $data	The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 */
	function cache($path, $data = null, $expires = '+1 day', $target = 'cache') {

		if (defined('DISABLE_CACHE')) {
			return null;
		}
		$now = time();

		if (!is_numeric($expires)) {
			$expires = strtotime($expires, $now);
		}

		switch(strtolower($target)) {
			case 'cache':
				$filename = CACHE . $path;
			break;
			case 'public':
				$filename = WWW_ROOT . $path;
			break;
			case 'tmp':
				$filename = TMP . $path;
			break;
		}

		$timediff = $expires - $now;
		$filetime = @filemtime($filename);

		if ($data == null) {
			// Read data from file
			if (file_exists($filename) && $filetime !== false) {
				if ($filetime + $timediff < $now) {
					// File has expired
					@unlink($filename);
				} else {
					$data = file_get_contents($filename);
				}
			}
		} else if(is_writable(dirname($filename))) {
			file_put_contents($filename, $data);
		}
		return $data;
	}
/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in directory will be deleted.
 *              If array, names to be searched for deletion.
 *              If clearCache() without params, all files in app/tmp/cache/views will be deleted
 *
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
	function clearCache($params = null, $type = 'views', $ext = '.php') {
		if (is_string($params) || $params === null) {
			$params = preg_replace('/\/\//', '/', $params);
			$cache = CACHE . $type . DS . $params;

			if (is_file($cache . $ext)) {
				@unlink($cache . $ext);
				return true;
			} else if(is_dir($cache)) {
				$files = glob("$cache*");

				if ($files === false) {
					return false;
				}

				foreach($files as $file) {
					if (is_file($file)) {
						@unlink($file);
					}
				}
				return true;
			} else {
				$cache = CACHE . $type . DS . '*' . $params . '*' . $ext;
				$files = glob($cache);

				if ($files === false) {
					return false;
				}
				foreach($files as $file) {
					if (is_file($file)) {
						@unlink($file);
					}
				}
				return true;
			}
		} elseif (is_array($params)) {
			foreach($params as $key => $file) {
				$file = preg_replace('/\/\//', '/', $file);
				$cache = CACHE . $type . DS . '*' . $file . '*' . $ext;
				$files[] = glob($cache);
			}

			if (!empty($files)) {
				foreach($files as $key => $delete) {
					if (is_array($delete)) {
						foreach($delete as $file) {
							if (is_file($file)) {
								@unlink($file);
							}
						}
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
/**
 * Recursively strips slashes from all values in an array
 *
 * @param unknown_type $value
 * @return unknown
 */
	function stripslashes_deep($value) {
		if (is_array($value)) {
			$return = array_map('stripslashes_deep', $value);
			return $return;
		} else {
			$return = stripslashes($value);
			return $return ;
		}
	}
/**
 *
 * Returns a translated string if one is found, or the submitted message if not found.
 *
 * @param string $singular
 * @param boolean $return
 * @return translated string if $return is false string will be echoed
 */
	function __($singular, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}
		$calledFrom = debug_backtrace();
		$dir = dirname($calledFrom[0]['file']);

		if($return === false) {
			echo I18n::translate($singular, null, null, 5, null, $dir);
		} else {
			return I18n::translate($singular, null, null, 5, null, $dir);
		}
	}
/**
 *
 * Returns correct plural form of message identified by $singular and $plural for count $count.
 * Some languages have more than one form for plural messages dependent on the count.
 *
 * @param string $singular
 * @param string $plural
 * @param integer $count
 * @param boolean $return
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __n($singular, $plural, $count, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}
		$calledFrom = debug_backtrace();
		$dir = dirname($calledFrom[0]['file']);

		if($return === false) {
			echo I18n::translate($singular, $plural, null, 5, $count, $dir);
		} else {
			return I18n::translate($singular, $plural, null, 5, $count, $dir);
		}
	}
/**
 *
 * Allows you to override the current domain for a single message lookup.
 *
 * @param string $domain
 * @param string $msg
 * @param string $return
 * @return translated string if $return is false string will be echoed
 */
	function __d($domain, $msg, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}

		if($return === false) {
			echo I18n::translate($msg, null, $domain);
		} else {
			return I18n::translate($msg, null, $domain);
		}
    }
/**
 *
 * Allows you to override the current domain for a single plural message lookup
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain
 *
 * @param string $domain
 * @param string $singular
 * @param string $plural
 * @param integer $count
 * @param boolean $return
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __dn($domain, $singular, $plural, $count, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}

		if($return === false) {
			echo I18n::translate($singular, $plural, $domain, 5, $count);
		} else {
			return I18n::translate($singular, $plural, $domain, 5, $count);
		}
	}
/**
 *
 * Allows you to override the current domain for a single message lookup.
 * It also allows you to specify a category.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 * LC_CTYPE     0
 * LC_NUMERIC   1
 * LC_TIME      2
 * LC_COLLATE   3
 * LC_MONETARY  4
 * LC_MESSAGES  5
 * LC_ALL       6
 *
 * @param string $domain
 * @param string $msg
 * @param integer $category
 * @param boolean $return
 * @return translated string if $return is false string will be echoed
 */
	function __dc($domain, $msg, $category, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}

		if($return === false) {
			echo I18n::translate($msg, null, $domain, $category);
		} else {
			return I18n::translate($msg, null, $domain, $category);
		}
	}
/**
 *
 * Allows you to override the current domain for a single plural message lookup.
 * It also allows you to specify a category.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 * LC_CTYPE     0
 * LC_NUMERIC   1
 * LC_TIME      2
 * LC_COLLATE   3
 * LC_MONETARY  4
 * LC_MESSAGES  5
 * LC_ALL       6
 *
 * @param string $domain
 * @param string $singular
 * @param string $plural
 * @param integer $count
 * @param integer $category
 * @param boolean $return
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __dcn($domain, $singular, $plural, $count, $category, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}

		if($return === false) {
			echo I18n::translate($singular, $plural, $domain, $category, $count);
		} else {
			return I18n::translate($singular, $plural, $domain, $category, $count);
		}
	}
/**
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 * LC_CTYPE     0
 * LC_NUMERIC   1
 * LC_TIME      2
 * LC_COLLATE   3
 * LC_MONETARY  4
 * LC_MESSAGES  5
 * LC_ALL       6
 *
 * @param string $msg
 * @param integer $category
 * @param string $return
 * @return translated string if $return is false string will be echoed
 */
	function __c($msg, $category, $return = false) {
		if(!class_exists('I18n')) {
			uses('i18n');
		}
		$calledFrom = debug_backtrace();
		$dir = dirname($calledFrom[0]['file']);

		if($return === false) {
			echo I18n::translate($msg, null, null, $category, null, $dir);
		} else {
			return I18n::translate($msg, null, null, $category, null, $dir);
		}
    }
/**
 * Computes the difference of arrays using keys for comparison
 *
 * @param array
 * @param array
 * @return array
 */
	if (!function_exists('array_diff_key')) {
		function array_diff_key() {
			$valuesDiff = array();

			if (func_num_args() < 2) {
				return false;
			}

			foreach (func_get_args() as $param) {
				if (!is_array($param)) {
					return false;
				}
			}

			$args = func_get_args();
			foreach ($args[0] as $valueKey => $valueData) {
				for ($i = 1; $i < func_num_args(); $i++) {
					if (isset($arg[$i][$valueKey])) {
						continue 2;
					}
				}
				$valuesDiff[$valueKey] = $valueData;
			}
			return $valuesDiff;
		}
	}
/**
 * Computes the intersection of arrays using keys for comparison
 *
 * @param array
 * @param array
 * @return array
 */
	if (!function_exists('array_intersect_key')) {
		function array_intersect_key($arr1, $arr2) {
			$res = array();
			foreach($arr1 as $key=>$value) {
				if(array_key_exists($key, $arr2)) {
					$res[$key] = $arr1[$key];
				}
			}
			return $res;
		}
	}
/**
 * @deprecated
 * @see Set::countDim
 */
	function countdim($array) {
		trigger_error(__('Deprecated: Use Set::countDim instead'), E_USER_WARNING);
		if (is_array(reset($array))) {
			$return = countdim(reset($array)) + 1;
		} else {
			$return = 1;
		}
		return $return;
	}
/**
 * Shortcut to Log::write.
 */
	function LogError($message) {
		if (!class_exists('CakeLog')) {
			uses('cake_log');
		}
		$bad = array("\n", "\r", "\t");
		$good = ' ';
		CakeLog::write('error', str_replace($bad, $good, $message));
	}
/**
 * Searches include path for files
 *
 * @param string $file
 * @return Full path to file if exists, otherwise false
 */
	function fileExistsInPath($file) {
		$paths = explode(PATH_SEPARATOR, ini_get('include_path'));
		foreach($paths as $path) {
			$fullPath = $path . DIRECTORY_SEPARATOR . $file;

			if (file_exists($fullPath)) {
				return $fullPath;
			} elseif (file_exists($file)) {
				return $file;
			}
		}
		return false;
	}
/**
 * Convert forward slashes to underscores and removes first and last underscores in a string
 *
 * @param string
 * @return string with underscore remove from start and end of string
 */
	function convertSlash($string) {
		$string = trim($string,"/");
		$string = preg_replace('/\/\//', '/', $string);
		$string = str_replace('/', '_', $string);
		return $string;
	}
/**
 * chmod recursively on a directory
 *
 * @param string $path
 * @param int $mode
 * @return boolean
 */
	function chmodr($path, $mode = 0755) {
		if (!is_dir($path)) {
			return chmod($path, $mode);
		}
		$dir = opendir($path);

		while($file = readdir($dir)) {
			if ($file != '.' && $file != '..') {
				$fullpath = $path . '/' . $file;

				if (!is_dir($fullpath)) {
					if (!chmod($fullpath, $mode)) {
						return false;
					}
				} else {
					if (!chmodr($fullpath, $mode)) {
						return false;
					}
				}
			}
		}
		closedir($dir);

		if (chmod($path, $mode)) {
			return true;
		} else {
			return false;
		}
	}
/**
 * Wraps ternary operations.  If $condition is a non-empty value, $val1 is returned, otherwise $val2.
 *
 * @param mixed $condition Conditional expression
 * @param mixed $val1
 * @param mixed $val2
 * @return mixed $val1 or $val2, depending on whether $condition evaluates to a non-empty expression.
 */
	function ife($condition, $val1 = null, $val2 = null) {
		if (!empty($condition)) {
			return $val1;
		}
		return $val2;
	}
?>