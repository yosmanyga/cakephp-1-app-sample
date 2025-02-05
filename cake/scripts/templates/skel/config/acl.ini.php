;<?php die() ?>
; SVN FILE: $Id: acl.ini.php 2958 2006-05-26 05:29:17Z phpnut $
;/**
; * Short description for file.
; *
; *
; * PHP versions 4 and 5
; *
; * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
; * Copyright (c)	2006, Cake Software Foundation, Inc.
; *			1785 E. Sahara Avenue, Suite 490-204
; *			Las Vegas, Nevada 89104
; *
; *  Licensed under The MIT License
; *  Redistributions of files must retain the above copyright notice.
; *
; * @filesource
; * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
; * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
; * @package			cake
; * @subpackage		cake.app.config
; * @since			CakePHP v 0.10.0.1076
; * @version			$Revision: 2958 $
; * @modifiedby		$LastChangedBy: phpnut $
; * @lastmodified	$Date: 2006-05-26 00:29:17 -0500 (Fri, 26 May 2006) $
; * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
; */

; acl.ini.php - Cake ACL Configuration
; ---------------------------------------------------------------------
; Use this file to specify user permissions.
; aco = access control object (something in your application)
; aro = access request object (something requesting access)
;
; User records are added as follows:
;
; [uid]
; groups = group1, group2, group3
; allow = aco1, aco2, aco3
; deny = aco4, aco5, aco6
;
; Group records are added in a similar manner:
;
; [gid]
; allow = aco1, aco2, aco3
; deny = aco4, aco5, aco6
;
; The allow, deny, and groups sections are all optional.
; NOTE: groups names *cannot* ever be the same as usernames!
;
; ACL permissions are checked in the following order:
; 1. Check for user denies (and DENY if specified)
; 2. Check for user allows (and ALLOW if specified)
; 3. Gather user's groups
; 4. Check group denies (and DENY if specified)
; 5. Check group allows (and ALLOW if specified)
; 6. If no aro, aco, or group information is found, DENY
;
; ---------------------------------------------------------------------

;-------------------------------------
;Users
;-------------------------------------

[username-goes-here]
groups = group1, group2
deny = aco1, aco2
allow = aco3, aco4

;-------------------------------------
;Groups
;-------------------------------------

[groupname-goes-here]
deny = aco5, aco6
allow = aco7, aco8