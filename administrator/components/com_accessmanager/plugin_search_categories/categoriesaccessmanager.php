<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 1.3.1
* @copyright Copyright (C) 2013 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die;

if(!defined('DS')){
	//joomla 3
	define('DS',DIRECTORY_SEPARATOR);
}

if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_accessmanager'.DS.'plugin_search_categories'.DS.'plugin_search_categories.php')){			
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_accessmanager'.DS.'plugin_search_categories'.DS.'plugin_search_categories.php');
}

?>