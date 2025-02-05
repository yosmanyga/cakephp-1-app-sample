<?php
/* SVN FILE: $Id: rss.php 4109 2006-12-19 19:27:19Z nate $ */
/**
 * RSS Helper class file.
 *
 * Simplifies the output of RSS feeds.
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
 * @since			CakePHP v 1.2
 * @version			$Revision: 4109 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2006-12-19 13:27:19 -0600 (Tue, 19 Dec 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * XML Helper class for easy output of XML structures.
 *
 * XmlHelper encloses all methods needed while working with XML documents.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.view.helpers
 */
uses('view' . DS . 'helpers' . DS . 'xml');

class RssHelper extends XmlHelper {

	var $Html = null;

	var $Time = null;

	var $helpers = array('Time');
/**
 * Base URL
 *
 * @access public
 * @var string
 */
	var $base = null;
/**
 * URL to current action.
 *
 * @access public
 * @var string
 */
	var $here = null;
/**
 * Parameter array.
 *
 * @access public
 * @var array
 */
	var $params = array();
/**
 * Current action.
 *
 * @access public
 * @var string
 */
	var $action = null;
/**
 * POSTed model data
 *
 * @access public
 * @var array
 */
	var $data = null;
/**
 * Name of the current model
 *
 * @access public
 * @var string
 */
	var $model = null;
/**
 * Name of the current field
 *
 * @access public
 * @var string
 */
	var $field = null;
/**
 * Default spec version of generated RSS
 *
 * @access public
 * @var string
 */
	var $version = '2.0';
/**
 * Returns an RSS document wrapped in <rss /> tags
 *
 * @param  array  $attrib <rss /> tag attributes
 * @return string An RSS document
 */
	function document($attrib = array(), $content = null) {
		if ($content === null) {
			$content = $attrib;
			$attrib = array();
		}
		if (!isset($attrib['version']) || empty($attrib['version'])) {
			$attrib['version'] = $this->version;
		}

		$attrib = array_reverse(am($this->__prepareNamespaces(), $attrib));
		return $this->elem('rss', $attrib, $content);
	}
/**
 * Returns an RSS <channel /> element
 *
 * @param  array  $attrib   <channel /> tag attributes
 * @param  mixed  $elements Named array elements which are converted to tags
 * @param  mixed  $content  Content (<item />'s belonging to this channel
 * @return string An RSS <channel />
 */
	function channel($attrib = array(), $elements = array(), $content = null) {
		$view =& ClassRegistry::getObject('view');

		if (!isset($elements['title']) && !empty($view->pageTitle)) {
			$elements['title'] = $view->pageTitle;
		}
		if (!isset($elements['link'])) {
			$elements['link'] = '/';
		} 
		$elements['link'] = $this->url($elements['link'], true);

		$elems = '';
		foreach ($elements as $elem => $data) {
			$elems .= $this->elem($elem, array(), $data);
		}
		return $this->elem('channel', $attrib, $elems . $this->__composeContent($content), !($content === null));
	}
/**
 * Transforms an array of data using an optional callback, and maps it to a set
 * of <item /> tags
 *
 * @param  array  $items    The list of items to be mapped
 * @param  mixed  $callback A string function name, or array containing an object
 *                          and a string method name
 * @return string A set of RSS <item /> elements
 */
	function items($items, $callback = null) {
		if ($callback != null) {
			$items = array_map($callback, $items);
		}

		$out = '';
		$c = count($items);

		for ($i = 0; $i < $c; $i++) {
			$out .= $this->item(array(), $items[$i]);
		}
		return $out;
	}
/**
 * Converts an array into an <item /> element and its contents
 *
 * @param  array  $attrib      The attributes of the <item /> element
 * @param  array  $elements    The list of elements contained in this <item />
 * @return string An RSS <item /> element
 */
	function item($attrib = array(), $elements = array()) {
		$content = null;
		foreach ($elements as $key => $val) {

			$attrib = array();
			switch ($key) {
				case 'pubDate' :
					$val = $this->time($val);
				break;
				case 'link':
				case 'guid':
				case 'comments':
					if (is_array($val) && isset($val['url'])) {
						$attrib = $val;
						unset($attrib['url']);
						$val = $val['url'];
					}
					$val = Router::url($val, true);
				break;
				case 'source':
					if (is_array($val) && isset($val['url'])) {
						$attrib['url'] = Router::url($val['url'], true);
						$val = $val['title'];
					} elseif (is_array($val)) {
						$attrib['url'] = Router::url($val[0], true);
						$val = $val[1];
					}
				break;
				case 'enclosure':
					if (is_string($val['url']) && is_file(WWW_ROOT . $val['url']) && file_exists(WWW_ROOT . $val['url'])) {
						if (!isset($val['length']) && strpos($val['url'], '://') === false) {
							$val['length'] = sprintf("%u", filesize(WWW_ROOT . $val['url']));
						}
						if (!isset($val['type']) && function_exists('mime_content_type')) {
							$val['type'] = mime_content_type(WWW_ROOT . $val['url']);
						}
					}
					$val['url'] = Router::url($val['url'], true);
					$attrib = $val;
					$val = null;
				break;
			}			
			if ($val != null) {
				$val = h($val);
			}
			$elements[$key] = $this->elem($key, $attrib, $val);
		}

		if (isset($elements['link']) && !isset($elements['guid'])) {
			$elements['guid'] = $elements['link'];
		}

		if (!empty($elements)) {
			$content = join('', $elements);
		}
		
		return $this->output($this->elem('item', $attrib, $content, !($content === null)));
	}
/**
 * Converts a time in any format to an RSS time
 *
 * @param  mixed  $time
 * @return string An RSS-formatted timestamp
 * @see TimeHelper::toRSS
 */
 	function time($time) {
		return $this->Time->toRSS($time);
 	}
}

?>