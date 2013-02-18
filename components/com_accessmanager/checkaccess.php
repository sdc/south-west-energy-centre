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

//silly workaround for developers who install the trail version while totally ignoring 
//all warnings about that you need Ioncube installed or else it will criple the site
$am_trial_version = 0;

if(!$am_trial_version || ($am_trial_version && extension_loaded('ionCube Loader'))){
	require_once(JPATH_ROOT.DS.'components'.DS.'com_accessmanager'.DS.'checkaccess2.php');
}

?>