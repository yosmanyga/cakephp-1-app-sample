<?php
/* SVN FILE: $Id: acl_base.php 4129 2006-12-22 22:49:47Z phpnut $ */

/**
 * Access Control List abstract class.
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
 * @subpackage		cake.cake.libs.controller.components
 * @since			CakePHP v 0.10.0.1232
 * @version			$Revision: 4129 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-12-22 16:49:47 -0600 (Fri, 22 Dec 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Access Control List abstract class. Not to be instantiated.
 * Subclasses of this class are used by AclComponent to perform ACL checks in Cake.
 *
 * @package 	cake
 * @subpackage	cake.cake.libs.controller.components
 */
class AclBase{

/**
 * This class should never be instantiated, just subclassed.
 *
 * @return AclBase
 */
	 function AclBase() {
		  //No instantiations or constructor calls (even statically)
		  if (strcasecmp(get_class($this), "AclBase") == 0 || !is_subclass_of($this, "AclBase")) {
				trigger_error(
					__(
						"[acl_base] The AclBase class constructor has been called, or the class was instantiated. This class must remain abstract. Please refer to the Cake docs for ACL configuration.", true),
					E_USER_ERROR);
				return NULL;
		  }
	 }

/**
 * Empty method to be overridden in subclasses
 *
 * @param unknown_type $aro
 * @param unknown_type $aco
 * @param string $action
 */
	 function check($aro, $aco, $action = "*") {
	 }
}
?>