<?php
/* SVN FILE: $Id: missing_action.ctp 4152 2006-12-23 09:09:06Z phpnut $ */
/**
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
 * @subpackage		cake.cake.libs.view.templates.errors
 * @since			CakePHP v 0.10.0.1076
 * @version			$Revision: 4152 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-12-23 03:09:06 -0600 (Sat, 23 Dec 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>

<h1><?php echo sprintf(__('Missing Method in %s', true), $controller);?></h1>
<p class="error"><?php echo sprintf(__('You are seeing this error because the action <em>%1$s</em> is not defined in controller <em>%2$s</em>', true), $action, $controller);?></p>
<p><span class="notice"><?php echo sprintf(__('If you want to customize this error message, create %s.', true), APP_DIR.DS."views/errors/missing_action.thtml");?></span></p>
<p><span class="notice"><strong><?php __('Fatal'); ?></strong>
<?php echo sprintf(__(': Confirm you have created the %1$s::%2$s in file : %3$s.', true), $controller, $action, APP_DIR.DS."controllers".DS.Inflector::underscore($controller).".php");?></span></p>
<p>&lt;?php<br />
class <?php echo $controller;?> extends AppController {<br />
&nbsp;&nbsp;&nbsp;<strong>function <?php echo $action;?>() {<br />
&nbsp;&nbsp;&nbsp;}</strong><br />
}<br />
?&gt;<br />
</p>
