<?php
/* SVN FILE: $Id: class_registry.php 3983 2006-11-27 01:04:19Z gwoo $ */
/**
 * Class collections.
 *
 * A repository for class objects, each registered with a key.
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
 * @subpackage		cake.cake.libs
 * @since			CakePHP v 0.9.2
 * @version			$Revision: 3983 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2006-11-26 19:04:19 -0600 (Sun, 26 Nov 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Class Collections.
 *
 * A repository for class objects, each registered with a key.
 * If you try to add an object with the same key twice, nothing will come of it.
 * If you need a second instance of an object, give it another key.
 *
 * @package		cake
 * @subpackage	cake.cake.libs
 */
class ClassRegistry{
/**
 * Names of classes with their objects.
 *
 * @var array
 * @access private
 */
	var $_objects = array();
/**
 * Return a singleton instance of the ClassRegistry.
 *
 * @return ClassRegistry instance
 */
	function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] = &new ClassRegistry;
		}
		 return $instance[0];
	}
/**
 * Add $object to the registry, associating it with the name $key.
 *
 * @param string $key
 * @param mixed $object
 */
	function addObject($key, &$object) {
		$_this =& ClassRegistry::getInstance();
		$key = Inflector::underscore($key);
		if (array_key_exists($key, $_this->_objects) === false) {
			$_this->_objects[$key] = &$object;
		}
	}
/**
 * Remove object which corresponds to given key.
 *
 * @param string $key
 * @return void
 */
	function removeObject($key) {
		$_this =& ClassRegistry::getInstance();
		$key = Inflector::underscore($key);
		if (array_key_exists($key, $_this->_objects) === true) {
			unset($_this->_objects[$key]);
		}
	}
/**
 * Returns true if given key is present in the ClassRegistry.
 *
 * @param string $key Key to look for
 * @return boolean Success
 */
	function isKeySet($key) {
		$_this =& ClassRegistry::getInstance();
		$key = Inflector::underscore($key);
		return array_key_exists($key, $_this->_objects);
	}
/**
 * Return object which corresponds to given key.
 *
 * @param string $key
 * @return mixed
 */
	function &getObject($key) {
		$_this =& ClassRegistry::getInstance();
		$key = Inflector::underscore($key);
		return $_this->_objects[$key];
	}
}
?>