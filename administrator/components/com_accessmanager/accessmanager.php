<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 1.3.1
* @copyright Copyright (C) 2013 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	//joomla 3
	define('DS',DIRECTORY_SEPARATOR);
}

//ACL
if (!JFactory::getUser()->authorise('core.manage', 'com_accessmanager')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

//silly workaround for developers who install the trail version while totally ignoring 
//all warnings about that you need Ioncube installed or else it will criple the site
$am_trial_version = 0;

if($am_trial_version && !extension_loaded('ionCube Loader')){
	echo 'This trial version is encrypted. You need Ioncube installed and enabled on your server to use it. <a href="http://www.pages-and-items.com/faqs/ioncube" target="_blank">read more</a>';
	exit;
}

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new accessmanagerController();

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();

?>