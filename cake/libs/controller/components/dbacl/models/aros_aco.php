<?php
/* SVN FILE: $Id: aros_aco.php 3610 2006-10-09 15:47:50Z nate $ */

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
 * @subpackage		cake.cake.libs.controller.components.dbacl.models
 * @since			CakePHP v 0.10.0.1232
 * @version			$Revision: 3610 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2006-10-09 10:47:50 -0500 (Mon, 09 Oct 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller.components.dbacl.models
 */

class ArosAco extends AppModel {

	var $useDbConfig = ACL_DATABASE;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $cacheQueries = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	 var $name = 'ArosAco';
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	 var $useTable = 'aros_acos';
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	 var $belongsTo = 'Aro,Aco';
}
?>