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

function com_uninstall(){ 

	$database = JFactory::getDBO();	
	
	//delete system plugin
	$plugin_php = JPATH_PLUGINS.DS.'system'.DS.'accessmanager'.DS.'accessmanager.php';
	$plugin_xml = JPATH_PLUGINS.DS.'system'.DS.'accessmanager'.DS.'accessmanager.xml';
	$system_plugin_success = 0;
	if(file_exists($plugin_php) && file_exists($plugin_xml)){
		$system_plugin_success = JFile::delete($plugin_php);
		JFile::delete($plugin_xml);
	}
	if($system_plugin_success){
		echo '<p style="color: #5F9E30;">system plugin succesfully uninstalled</p>';		
	}else{
		echo '<p style="color: red;">could not uninstall system plugin</p>';
	}   
	$database->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='system' AND element='accessmanager' LIMIT 1");
    $database->query();	
	
	
	//delete content search plugin
	$plugin_php = JPATH_PLUGINS.DS.'search'.DS.'contentaccessmanager'.DS.'contentaccessmanager.php';
	$plugin_xml = JPATH_PLUGINS.DS.'search'.DS.'contentaccessmanager'.DS.'contentaccessmanager.xml';
	$plugin_html = JPATH_PLUGINS.DS.'search'.DS.'contentaccessmanager'.DS.'index.html';
	$language_path = JPATH_ROOT.DS.'administrator'.DS.'language'.DS.'en-GB'.DS;
	$plugin_language = $language_path.DS.'en-GB.plg_search_contentaccessmanager.ini';
	$plugin_language_sys = $language_path.DS.'en-GB.plg_search_contentaccessmanager.sys.ini';
	$content_plugin_success = 0;
	if(file_exists($plugin_php) && file_exists($plugin_xml)){
		$content_plugin_success = JFile::delete($plugin_php);
		JFile::delete($plugin_xml);
		JFile::delete($plugin_html);
		JFile::delete($plugin_language);
		JFile::delete($plugin_language_sys);
	}
	if($content_plugin_success){
		echo '<p style="color: #5F9E30;">content search plugin succesfully uninstalled</p>';		
	}else{
		echo '<p style="color: red;">could not uninstall content search plugin</p>';
	}   
	$database->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='search' AND element='contentaccessmanager' ");
    $database->query();	
	//enable the Joomla core content search plugin
	$database->setQuery( "UPDATE #__extensions SET enabled='1' WHERE type='plugin' AND element='content' AND folder='search' ");
	$database->query();
	echo '<p style="color: #5F9E30;">Joomla content search plugin enabled</p>';	
		
	//delete categories search plugin
	$plugin_php = JPATH_PLUGINS.DS.'search'.DS.'categoriesaccessmanager'.DS.'categoriesaccessmanager.php';
	$plugin_xml = JPATH_PLUGINS.DS.'search'.DS.'categoriesaccessmanager'.DS.'categoriesaccessmanager.xml';
	$plugin_html = JPATH_PLUGINS.DS.'search'.DS.'categoriesaccessmanager'.DS.'index.html';	
	$plugin_language = $language_path.DS.'en-GB.plg_search_categoriesaccessmanager.ini';
	$plugin_language_sys = $language_path.DS.'en-GB.plg_search_categoriesaccessmanager.sys.ini';
	$categories_plugin_success = 0;
	if(file_exists($plugin_php) && file_exists($plugin_xml)){
		$categories_plugin_success = JFile::delete($plugin_php);
		JFile::delete($plugin_xml);
		JFile::delete($plugin_html);
		JFile::delete($plugin_language);
		JFile::delete($plugin_language_sys);
	}
	if($categories_plugin_success){
		echo '<p style="color: #5F9E30;">categories search plugin succesfully uninstalled</p>';		
	}else{
		echo '<p style="color: red;">could not uninstall categories search plugin</p>';
	}   
	$database->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='search' AND element='categoriesaccessmanager' ");
    $database->query();	
	//enable the Joomla core categories search plugin
	$database->setQuery( "UPDATE #__extensions SET enabled='1' WHERE type='plugin' AND element='categories' AND folder='search' ");
	$database->query();
	echo '<p style="color: #5F9E30;">Joomla categories search plugin enabled</p>';	

	
}

?>
<div style="width: 500px; text-align: left;">
	<h2 style="padding-left: 10px;">Access-Manager</h2>	
	<p>
		Thank you for having used Access-Manager.
	</p>
	<p>
		Why did you uninstall Access-Manager? Missing any features? <a href="http://www.pages-and-items.com/" target="_blank">Let us know</a>.		
	</p>	
	<p>
		Check <a href="http://www.pages-and-items.com/" target="_blank">www.pages-and-items.com</a> for:
		<ul>
			<li><a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank">updates</a></li>
			<li><a href="http://www.pages-and-items.com/extensions/access-manager/faqs" target="_blank">FAQs</a></li>	
			<li><a href="http://www.pages-and-items.com/forum/37-access-manager" target="_blank">support forum</a></li>	
			<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
			<li><a href="http://www.pages-and-items.com/extensions/access-manager/update-notifications-for-access-manager" target="_blank">subscribe to RSS feed update notifications</a></li>			
		</ul>
	</p>	
</div>