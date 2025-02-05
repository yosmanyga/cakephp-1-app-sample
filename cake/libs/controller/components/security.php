<?php
/* SVN FILE: $Id: security.php 4129 2006-12-22 22:49:47Z phpnut $ */
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
 * @subpackage		cake.cake.libs.controller.components
 * @since			CakePHP v 0.10.8.2156
 * @version			$Revision: 4129 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-12-22 16:49:47 -0600 (Fri, 22 Dec 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller.components
 */
class SecurityComponent extends Object {
/**
 * Holds an instance of the core Security object
 *
 * @var object Security
 * @access public
 */
	var $Security = null;
/**
 * The controller method that will be called if this request is black-hole'd
 *
 * @var string
 * @access public
 */
	var $blackHoleCallback = null;
/**
 * List of controller actions for which a POST request is required
 *
 * @var array
 * @access public
 * @see SecurityComponent::requirePost()
 */
	var $requirePost = array();
/**
 * List of actions that require an SSL-secured connection
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireSecure()
 */
	var $requireSecure = array();
/**
 * List of actions that require a valid authentication key
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireAuth()
 */
	var $requireAuth = array();
/**
 * List of actions that require an HTTP-authenticated login (basic or digest)
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireLogin()
 */
	var $requireLogin = array();
/**
 * Login options for SecurityComponent::requireLogin()
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireLogin()
 */
	var $loginOptions = array('type' => '');
/**
 * An associative array of usernames/passwords used for HTTP-authenticated logins.
 * If using digest authentication, passwords should be MD5-hashed.
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireLogin()
 */
	var $loginUsers = array();
/**
 * Controllers from which actions of the current controller are allowed to receive
 * requests.
 *
 * @var array
 * @see SecurityComponent::requireAuth()
 */
	var $allowedControllers = array();
/**
 * Actions from which actions of the current controller are allowed to receive
 * requests.
 *
 * @var array
 * @see SecurityComponent::requireAuth()
 */
	var $allowedActions = array();
/**
 * Other components used by the Security component
 *
 * @var array
 * @access public
 */
	var $components = array('RequestHandler', 'Session');
/**
 * Security class constructor
 */
	function __construct () {
		$this->Security =& Security::getInstance();
	}
/**
 * Component startup.  All security checking happens here.
 *
 * @param object $controller
 * @return unknown
 * @access public
 */
	function startup(&$controller) {
		// Check requirePost
		if (is_array($this->requirePost) && !empty($this->requirePost)) {

			if (in_array($controller->action, $this->requirePost) || $this->requirePost == array('*')) {

				if (!$this->RequestHandler->isPost()) {

					if (!$this->blackHole($controller, 'post')) {
						return null;
					}
				}
			}
		}
		// Check requireSecure
		if (is_array($this->requireSecure) && !empty($this->requireSecure)) {

			if (in_array($controller->action, $this->requireSecure) || $this->requireSecure == array('*')) {

				if (!$this->RequestHandler->isSSL()) {

					if (!$this->blackHole($controller, 'secure')) {
						return null;
					}
				}
			}
		}
		// Check requireAuth
		if (is_array($this->requireAuth) && !empty($this->requireAuth) && !empty($controller->params['form'])) {

			if (in_array($controller->action, $this->requireAuth) || $this->requireAuth == array('*')) {

				if (!isset($controller->params['data']['_Token'])) {

					if (!$this->blackHole($controller, 'auth')) {
						return null;
					}
				}
				$token = $controller->params['data']['_Token']['key'];

				if ($this->Session->check('_Token')) {
					$tData = unserialize($this->Session->read('_Token'));

					if ($tData['expires'] < time() || $tData['key'] !== $token) {

						if (!$this->blackHole($controller, 'auth')) {
							return null;
						}
					}
					if (!empty($tData['allowedControllers']) && !in_array($controller->params['controller'], $tData['allowedControllers']) ||!empty($tData['allowedActions']) && !in_array($controller->params['action'], $tData['allowedActions'])) {

						if (!$this->blackHole($controller, 'auth')) {
							return null;
						}
					}
				} else {
					if (!$this->blackHole($controller, 'auth')) {
						return null;
					}
				}
			}
		}
		// Check requireLogin
		if (is_array($this->requireLogin) && !empty($this->requireLogin)) {

			if (in_array($controller->action, $this->requireLogin) || $this->requireLogin == array('*')) {
				$login = $this->loginCredentials($this->loginOptions['type']);

				if ($login == null) {
					// User hasn't been authenticated yet
					header($this->loginRequest());
					if (isset($this->loginOptions['prompt'])) {
						$this->__callback($controller, $this->loginOptions['prompt']);
					} else {
						$this->blackHole($controller, 'login');
					}
				} else {
					if (isset($this->loginOptions['login'])) {
						$this->__callback($controller, $this->loginOptions['login'], array($login));
					} else {
						if (low($this->loginOptions['type']) == 'digest') {
							// Do digest authentication
						} else {
							if (!(in_array($login['username'], array_keys($this->loginUsers)) && $this->loginUsers[$login['username']] == $login['password'])) {
								$this->blackHole($controller, 'login');
							}
						}
					}
				}
			}
		}

		// Add auth key for new form posts
		$authKey = Security::generateAuthKey();
		$expires = strtotime('+'.Security::inactiveMins().' minutes');
		$token = array(
			'key' => $authKey,
			'expires' => $expires,
			'allowedControllers' => $this->allowedControllers,
			'allowedActions' => $this->allowedActions
		);

		if (!isset($controller->params['data'])) {
			$controller->params['data'] = array();
		}
		$controller->params['_Token'] = $token;
		$this->Session->write('_Token', serialize($token));
	}
/**
 * Black-hole an invalid request with a 404 error or custom callback
 *
 * @param object $controller
 * @param string $error
 * @return Controller blackHoleCallback
 * @access public
 */
	function blackHole(&$controller, $error = '') {
		if ($this->blackHoleCallback == null) {
			$code = 404;
			if ($error == 'login') {
				$code = 401;
			}
			$controller->redirect(null, $code);
			exit();
		} else {
			return $this->__callback($controller, $this->blackHoleCallback, array($error));
		}
	}
/**
 * Sets the actions that require a POST request, or empty for all actions
 *
 * @access public
 * @return void
 */
	function requirePost() {
		$this->requirePost = func_get_args();
		if (empty($this->requirePost)) {
			$this->requirePost = array('*');
		}
	}
/**
 * Sets the actions that require a request that is SSL-secured, or empty for all actions
 *
 * @access public
 * @return void
 */
	function requireSecure() {
		$this->requireSecure = func_get_args();
		if (empty($this->requireSecure)) {
			$this->requireSecure = array('*');
		}
	}
/**
 * Sets the actions that require an authenticated request, or empty for all actions
 *
 * @access public
 * @return void
 */
	function requireAuth() {
		$this->requireAuth = func_get_args();
		if (empty($this->requireAuth)) {
			$this->requireAuth = array('*');
		}
	}
/**
 * Sets the actions that require an HTTP-authenticated request, or empty for all actions
 *
 * @access public
 * @return void
 */
	function requireLogin() {
		$args = func_get_args();
		foreach ($args as $arg) {
			if (is_array($arg)) {
				$this->loginOptions = $arg;
			} else {
				$this->requireLogin[] = $arg;
			}
		}
		if (empty($this->requireLogin)) {
			$this->requireLogin = array('*');
		}
		if (isset($this->loginOptions['users'])) {
			$this->loginUsers =& $this->loginOptions['users'];
		}
	}
/**
 * Gets the login credentials for an HTTP-authenticated request
 *
 * @param string $type Either 'basic', 'digest', or null. If null/empty, will try both.
 * @return mixed If successful, returns an array with login name and password, otherwise null.
 * @access public
 */
	function loginCredentials($type = null) {

		if (empty($type) || low($type) == 'basic') {
			$login = array('username' => env('PHP_AUTH_USER'), 'password' => env('PHP_AUTH_PW'));
			if ($login['username'] != null) {
				return $login;
			}
		}

		if ($type == '' || low($type) == 'digest') {

			$digest = null;
			if (version_compare(phpversion(), '5.1') != -1) {
				$digest = env('PHP_AUTH_DIGEST');

			} elseif (function_exists('apache_request_headers')) {
				$headers = apache_request_headers();
				if (isset($headers['Authorization']) && !empty($headers['Authorization']) && substr($headers['Authorization'], 0, 7) == 'Digest ') {
					$digest = substr($headers['Authorization'], 7);
				}
			} else {
				// Server doesn't support digest-auth headers
				trigger_error(__('SecurityComponent::loginCredentials() - Server does not support digest authentication'), E_USER_WARNING);
				return null;
			}

			if ($digest == null) {
				return null;
			}
			$data = $this->parseDigestAuthData($digest);
		}

		return null;
	}
/**
 * Sets the default login options for an HTTP-authenticated request
 *
 * @param unknown_type $options
 * @access private
 */
	function __setLoginDefaults(&$options) {
		$options = am(
			array(
				'type' => 'basic',
				'realm' => env('SERVER_NAME'),
				'qop' => 'auth',
				'nonce' => uniqid()
			),
			array_filter($options)
		);
		$options = am(array('opaque' => md5($options['realm'])), $options);
	}
/**
 * Generates the text of an HTTP-authentication request header from an array of options..
 *
 * @param array $options
 * @return unknown
 * @access public
 */
	function loginRequest($options = array()) {
		$options = am($this->loginOptions, $options);
		$this->__setLoginDefaults($options);
		$data  = 'WWW-Authenticate: ' . ucfirst($options['type']) . ' realm="' . $options['realm'] . '"';

		return $data;
	}
/**
 * Parses an HTTP digest authentication response, and returns an array of the data, or null on failure.
 *
 * @param string $digest
 * @return array Digest authentication parameters
 * @access public
 */
	function parseDigestAuthData($digest) {
		if (substr($digest, 0, 7) == 'Digest ') {
			$digest = substr($digest, 7);
		}

		$keys = array();
		$match = array();
		$req = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./\_-]+)\2@', $digest, $match, PREG_SET_ORDER);

		foreach ($match as $i) {
			$keys[$i[1]] = $i[3];
			unset($req[$i[1]]);
		}
		if (empty($req)) {
			return $keys;
		} else {
			return null;
		}
	}
/**
 * Calls a controller callback method
 *
 * @param object $controller
 * @param string $method
 * @param array $params
 * @return Contrtoller callback method
 * @access private
 */
	function __callback(&$controller, $method, $params = array()) {
		if (is_callable(array($controller, $method))) {
			return call_user_func_array(array(&$controller, $method), empty($params) ? null : $params);
		} else {
			// Debug::warning('Callback method ' . $method . ' in controller ' . get_class($controller)
			return null;
		}
	}
}
?>