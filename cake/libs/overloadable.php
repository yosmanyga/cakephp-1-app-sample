<?php
/* SVN FILE: $Id: overloadable.php 3381 2006-08-07 07:46:14Z dho $ */
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
 * @version			$Revision: 3381 $
 * @modifiedby		$LastChangedBy: dho $
 * @lastmodified	$Date: 2006-08-07 02:46:14 -0500 (Mon, 07 Aug 2006) $
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
if (!PHP5) {
	require(LIBS . 'overloadable_php4.php');
} else {
	require(LIBS . 'overloadable_php5.php');
}
?>