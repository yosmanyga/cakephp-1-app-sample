<?php
/* SVN FILE: $Id: overloadable_php4.php 4211 2006-12-26 16:17:37Z phpnut $ */
/**
 * Overload abstraction interface.  Merges differences between PHP4 and 5.
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
 * @since			CakePHP v 1.2
 * @version			$Revision: 4211 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-12-26 10:17:37 -0600 (Tue, 26 Dec 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Overloadable class selector
 *
 * @package		cake
 * @subpackage	cake.cake.libs
 */

/**
 * Load the interface class based on the version of PHP.
 *
 */
class Overloadable extends Object {

	function __construct() {
		$this->overload();
		parent::__construct();
	}

	function overload() {
		if (function_exists('overload')) {
			if (func_num_args() > 0) {
				foreach (func_get_args() as $class) {
					if (is_object($class)) {
						overload(get_class($class));
					} elseif (is_string($class)) {
						overload($class);
					}
				}
			} else {
				overload(get_class($this));
			}
		}
	}

	function __call($method, $params, &$return) {
		if(!method_exists($this, 'call__')) {
			trigger_error(sprintf(__('Magic method handler call__ not defined in %s', true), get_class($this)), E_USER_ERROR);
		}
		$return = $this->call__($method, $params);
		return true;
	}
}
Overloadable::overload('Overloadable');

class Overloadable2 extends Object {

	function __construct() {
		$this->overload();
		parent::__construct();
	}

	function overload() {
		if (function_exists('overload')) {
			if (func_num_args() > 0) {
				foreach (func_get_args() as $class) {
					if (is_object($class)) {
						overload(get_class($class));
					} elseif (is_string($class)) {
						overload($class);
					}
				}
			} else {
				overload(get_class($this));
			}
		}
	}

	function __call($method, $params, &$return) {
		if(!method_exists($this, 'call__')) {
			trigger_error(sprintf(__('Magic method handler call__ not defined in %s', true), get_class($this)), E_USER_ERROR);
		}
		$return = $this->call__($method, $params);
		return true;
	}

	function __get($name, &$value) {
		$value = $this->get__($name);
		return true;
	}

	function __set($name, $value) {
		$this->set__($name, $value);
		return true;
	}
}
Overloadable::overload('Overloadable2');

?>