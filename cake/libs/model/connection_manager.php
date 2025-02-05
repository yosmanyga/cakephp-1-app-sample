<?php
/* SVN FILE: $Id: connection_manager.php 4129 2006-12-22 22:49:47Z phpnut $ */

/**
 * Short description for file.
 *
 * Long description for file
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
 * @subpackage		cake.cake.libs.model
 * @since			CakePHP v 0.10.x.1402
 * @version			$Revision: 4129 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-12-22 16:49:47 -0600 (Fri, 22 Dec 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Manages loaded instances of DataSource objects
 *
 * Long description for file
 *
 * @package		cake
 * @subpackage	cake.cake.libs.model
 */

uses ('model' . DS . 'datasources' . DS . 'datasource');
config('database');

class ConnectionManager extends Object {

/**
 * Holds a loaded instance of the Connections object
 *
 * @var class:Connections
 * @access public
 */
	var $config = null;
/**
 * Holds instances DataSource objects
 *
 * @var array
 * @access private
 */
	var $_dataSources = array();
/**
 * Contains a list of all file and class names used in Connection settings
 *
 * @var array
 * @access private
 */
	var $_connectionsEnum = array();
/**
 * Constructor.
 *
 */
	function __construct() {
		if (class_exists('DATABASE_CONFIG')) {
			$this->config = new DATABASE_CONFIG();
		}
	}
/**
 * Gets a reference to the ConnectionManger object instance
 *
 * @return object
 */
	function &getInstance() {
		static $instance = array();

		if (!isset($instance[0]) || !$instance[0]) {
			$instance[0] = &new ConnectionManager();
		}

		return $instance[0];
	}
/**
 * Gets a reference to a DataSource object
 *
 * @param string $name The name of the DataSource, as defined in app/config/connections
 * @return object
 */
	function &getDataSource($name) {
		$_this =& ConnectionManager::getInstance();

		if (in_array($name, array_keys($_this->_dataSources))) {
			return $_this->_dataSources[$name];
		}

		$connections = $_this->enumConnectionObjects();
		if (in_array($name, array_keys($connections))) {
			$conn = $connections[$name];
			$class = $conn['classname'];
			$_this->loadDataSource($name);
			$_this->_dataSources[$name] =& new $class($_this->config->{$name});
			$_this->_dataSources[$name]->configKeyName = $name;
		} else {
			trigger_error(sprintf(__("ConnectionManager::getDataSource - Non-existent data source %s", true), $name), E_USER_ERROR);
			return null;
		}

		return $_this->_dataSources[$name];
	}
/**
 * Gets a DataSource name from an object reference
 *
 * @param object $source
 * @return string
 */
	function getSourceName(&$source) {
		$_this =& ConnectionManager::getInstance();

		$names = array_keys($_this->_dataSources);
		for ($i = 0; $i < count($names); $i++) {
			if ($_this->_dataSources[$names[$i]] === $source) {
				return $names[$i];
			}
		}
		return null;
	}
/**
 * Loads the DataSource class for the given connection name
 *
 * @param mixed $connName A string name of the connection, as defined in Connections config,
 *                        or an array containing the file and class name of the object.
 * @return boolean True on success, null on failure or false if the class is already loaded
 */
	function loadDataSource($connName) {
		$_this =& ConnectionManager::getInstance();

		if (is_array($connName)) {
			$conn = $connName;
		} else {
			$connections = $_this->enumConnectionObjects();
			$conn = $connections[$connName];
		}

		if (isset($conn['parent']) && !empty($conn['parent'])) {
			$_this->loadDataSource($conn['parent']);
		}

		if (class_exists($conn['classname'])) {
			return false;
		}

		if(file_exists(MODELS . 'datasources' . DS . $conn['filename'] . '.php')) {
			require (MODELS . 'datasources' . DS . $conn['filename'] . '.php');
		} else if (fileExistsInPath(LIBS . 'model' . DS . 'datasources' . DS . $conn['filename'] . '.php')) {
			require (LIBS . 'model' . DS . 'datasources' . DS . $conn['filename'] . '.php');
		} else {
			trigger_error(sprintf(__('Unable to load DataSource file %s.php', true), $conn['filename']), E_USER_ERROR);
			return null;
		}
	}
/**
 * Gets a list of class and file names associated with the user-defined DataSource connections
 *
 * @return array An associative array of elements where the key is the connection name
 *               (as defined in Connections), and the value is an array with keys 'filename' and 'classname'.
 */
	function enumConnectionObjects() {
		$_this =& ConnectionManager::getInstance();

		if (!empty($_this->_connectionsEnum)) {
			return $_this->_connectionsEnum;
		}
		$connections = get_object_vars($_this->config);

		if ($connections != null) {
			foreach($connections as $name => $config) {
				$_this->_connectionsEnum[$name] = $_this->__getDriver($config);
			}
			return $this->_connectionsEnum;
		} else {
			$this->cakeError('missingConnection', array(array('className' => 'ConnectionManager')));
		}
	}
/**
 * Dynamically creates a DataSource object at runtime, with the given name and settings
 *
 * @param string $name The DataSource name
 * @param array $config The DataSource configuration settings
 * @return object A reference to the DataSource object, or null if creation failed
 */
	function &create($name = '', $config = array()) {
		$_this =& ConnectionManager::getInstance();

		if (empty($name) || empty($config) || array_key_exists($name, $_this->_connectionsEnum)) {
			return null;
		}

		$_this->config->{$name} = $config;
		$_this->_connectionsEnum[$name] = $_this->__getDriver($config);
		return $_this->getDataSource($name);
	}
/**
 * Private method
 *
 * Returns the file and class name for the given driver
 */
	function __getDriver($config) {
		$_this =& ConnectionManager::getInstance();

		if (!isset($config['datasource'])) {
			$config['datasource'] = 'dbo';
		}

		if (isset($config['driver']) && $config['driver'] != null && !empty($config['driver'])) {
			$filename = $config['datasource'] . DS . $config['datasource'] . '_' . $config['driver'];
			$classname = Inflector::camelize(strtolower($config['datasource'] . '_' . $config['driver']));
			$parent = $_this->__getDriver(array('datasource' => $config['datasource']));
		} else {
			$filename = $config['datasource'] . '_source';
			$classname = Inflector::camelize(strtolower($config['datasource'] . '_source'));
			$parent = null;
		}
		return array('filename'  => $filename, 'classname' => $classname, 'parent' => $parent);
	}
/**
 * Destructor.
 *
 */
	function __destruct() {
		if (CAKE_SESSION_SAVE == 'database' && function_exists('session_write_close')) {
			session_write_close();
		}
	}
}

?>