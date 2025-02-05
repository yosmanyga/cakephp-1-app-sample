<?php
/* SVN FILE: $Id: session.php 3959 2006-11-25 08:55:16Z nate $ */
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
 * @subpackage		cake.cake.libs.view.helpers
 * @since			CakePHP v 1.1.7.3328
 * @version			$Revision: 3959 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2006-11-25 02:55:16 -0600 (Sat, 25 Nov 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Session Helper.
 *
 * Session reading from the view.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.view.helpers
 *
 */
class SessionHelper extends CakeSession {
/**
 * List of helpers used by this helper
 *
 * @var array
 */
	var $helpers = null;
/**
 * Used to determine if methods implementation is used, or bypassed
 *
 * @var boolean
 */
	var $__active = true;
/**
 * Class constructor
 *
 * @param string $base
 */
	function __construct($base = null) {
		if (!defined('AUTO_SESSION') || AUTO_SESSION === true) {
			parent::__construct($base, false);
		} else {
			$this->__active = false;
		}
	}
/**
 * Used to read a session values set in a controller for a key or return values for all keys.
 *
 * In your view: $session->read('Controller.sessKey');
 * Calling the method without a param will return all session vars
 *
 * @param string $name the name of the session key you want to read
 *
 * @return values from the session vars
 */
	function read($name = null) {
		if ($this->__active === true) {
			return $this->readSessionVar($name);
		}
		return false;
	}
/**
 * Used to check is a session key has been set
 *
 * In your view: $session->check('Controller.sessKey');
 *
 * @param string $name
 * @return boolean
 */
	function check($name) {
		if ($this->__active === true) {
			return $this->checkSessionVar($name);
		}
		return false;
	}
/**
 * Returns last error encountered in a session
 *
 * In your view: $session->error();
 *
 * @return string last error
 */
	function error() {
		if ($this->__active === true) {
			return $this->getLastError();
		}
		return false;
	}
/**
 * Used to render the message set in Controller::Session::setFlash()
 *
 * In your view: $session->flash('somekey');
 * 					Will default to flash if no param is passed
 *
 * @param string $key The [Message.]key you are rendering in the view.
 * @return string Will echo the value if $key is set, or false if not set.
 */
	function flash($key = 'flash') {
		if ($this->__active === true) {
			if ($this->checkSessionVar('Message.' . $key)) {
				e($this->readSessionVar('Message.' . $key));
				$this->delSessionVar('Message.' . $key);
			} else {
				return false;
			}
		}
		return false;
	}

/**
 * Used to check is a session is valid in a view
 *
 *
 * @return boolean
 */
	function valid() {
		if ($this->__active === true) {
		return $this->isValid();
		}
	}
}

?>