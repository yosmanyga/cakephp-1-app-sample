<?php
/* SVN FILE: $Id: ini_acl.php 3084 2006-06-14 17:53:15Z phpnut $ */

/**
 * This is core configuration file.
 *
 * Use it to configure core behaviour ofCake.
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
 * @subpackage		cake.cake.libs.controller.componenets.iniacl
 * @since			CakePHP v 0.2.9
 * @version			$Revision: 3084 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-06-14 12:53:15 -0500 (Wed, 14 Jun 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

uses('controller/components/acl_base');

/**
 * In this file you can extend the AclBase.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller.componenets.iniacl
 */

class INI_ACL extends AclBase{

/**
 * Array with configuration, parsed from ini file
 */
	var $config = null;
/**
 * The constructor must be overridden, as AclBase is abstract.
 *
 */
	function __construct() {
	}

/**
 * Main ACL check function. Checks to see if the ARO (access request object) has access to the ACO (access control object).
 * Looks at the acl.ini.php file for permissions (see instructions in/config/acl.ini.php).
 *
 * @param string $aro
 * @param string $aco
 * @return boolean
 */
	function check($aro, $aco, $aco_action = null) {
		if ($this->config == null) {
			$this->config = $this->readConfigFile(CONFIGS . 'acl.ini.php');
		}
		$aclConfig = $this->config;

		//First, if the user is specifically denied, then DENY
		if (isset($aclConfig[$aro]['deny'])) {
			$userDenies = $this->arrayTrim(explode(",", $aclConfig[$aro]['deny']));

			if (array_search($aco, $userDenies)) {
				//echo "User Denied!";
				return false;
			}
		}

		//Second, if the user is specifically allowed, then ALLOW
		if (isset($aclConfig[$aro]['allow'])) {
			$userAllows = $this->arrayTrim(explode(",", $aclConfig[$aro]['allow']));

			if (array_search($aco, $userAllows)) {
				//echo "User Allowed!";
				return true;
			}
		}

		//Check group permissions
		if (isset($aclConfig[$aro]['groups'])) {
			$userGroups = $this->arrayTrim(explode(",", $aclConfig[$aro]['groups']));

			foreach($userGroups as $group) {
				//If such a group exists,
				if (array_key_exists($group, $aclConfig)) {
					//If the group is specifically denied, then DENY
					if (isset($aclConfig[$group]['deny'])) {
						$groupDenies=$this->arrayTrim(explode(",", $aclConfig[$group]['deny']));

						if (array_search($aco, $groupDenies)) {
							//echo("Group Denied!");
							return false;
						}
					}

					//If the group is specifically allowed, then ALLOW
					if (isset($aclConfig[$group]['allow'])) {
						$groupAllows = $this->arrayTrim(explode(",", $aclConfig[$group]['allow']));

						if (array_search($aco, $groupAllows)) {
							//echo("Group Allowed!");
							return true;
						}
					}
				}
			}
		}

		//Default, DENY
		//echo("DEFAULT: DENY.");
		return false;
	}

/**
 * Parses an INI file and returns an array that reflects the INI file's section structure. Double-quote friendly.
 *
 * @param string $fileName
 * @return array
 */
	function readConfigFile($fileName) {
		$fileLineArray = file($fileName);

		foreach($fileLineArray as $fileLine) {
				$dataLine = trim($fileLine);
				$firstChar = substr($dataLine, 0, 1);

				if ($firstChar != ';' && $dataLine != '') {
					if ($firstChar == '[' && substr($dataLine, -1, 1) == ']') {
						$sectionName = preg_replace('/[\[\]]/', '', $dataLine);
					} else {
						$delimiter = strpos($dataLine, '=');

						if ($delimiter > 0) {
							$key = strtolower(trim(substr($dataLine, 0, $delimiter)));
							$value = trim(substr($dataLine, $delimiter + 1));

							if (substr($value, 0, 1) == '"' && substr($value, -1) == '"') {
								$value = substr($value, 1, -1);
							}

							$iniSetting[$sectionName][$key]=stripcslashes($value);
						} else {
							if (!isset($sectionName)) {
								$sectionName = '';
							}

							$iniSetting[$sectionName][strtolower(trim($dataLine))]='';
						}
					}
				} else {
				}
		}

		return $iniSetting;
	}

/**
 * Removes trailing spaces on all array elements (to prepare for searching)
 *
 * @param array $array
 * @return array
 */
	function arrayTrim($array) {
		foreach($array as $element) {
			$element = trim($element);
		}

		//Adding this element keeps array_search from returning 0:
		//0 is the first key, which may be correct, but 0 is interpreted as false.
		//Adding this element makes all the keys be positive integers.
		array_unshift($array, "");
		return $array;
	}
}
?>