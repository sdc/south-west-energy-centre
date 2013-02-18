<?php
/**
* BreezingForms - A Joomla Forms Application
* @version 1.7.3
* @package BreezingForms
* @copyright (C) 2008-2011 by Markus Bopp
* @license Released under the terms of the GNU General Public License
**/
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

function com_uninstall(){

    jimport('joomla.version');
    $version = new JVersion();

    if(version_compare($version->getShortVersion(), '1.6', '>=')){
        $db = JFactory::getDBO();
        $db->setQuery("Delete From #__menu Where `link` Like 'index.php?option=com_breezingforms&act=run%'");
        $db->query();
    }

    jimport('joomla.filesystem.file');

    if (JFile::exists(JPATH_SITE . "/components/com_sh404sef/sef_ext/com_breezingforms.php")){
        JFile::delete(JPATH_SITE . "/components/com_sh404sef/sef_ext/com_breezingforms.php");
    }

    if(JFile::exists(JPATH_SITE . '/ff_secimage.php'))JFile::delete( JPATH_SITE . '/ff_secimage.php');
    if(JFile::exists(JPATH_SITE . '/templates/system/ff_secimage.php'))JFile::delete( JPATH_SITE . '/templates/system/ff_secimage.php');
    if(JFile::exists(JPATH_SITE . "/administrator/components/com_joomfish/contentelements/breezingforms_elements.xml"))JFile::delete( JPATH_SITE . "/administrator/components/com_joomfish/contentelements/breezingforms_elements.xml");
    if(JFile::exists(JPATH_SITE . "/administrator/components/com_joomfish/contentelements/translationFformFilter.php"))JFile::delete( JPATH_SITE . "/administrator/components/com_joomfish/contentelements/translationFformFilter.php");
    if(JFile::exists(JPATH_SITE . "/administrator/components/com_joomfish/contentelements/translationFformoptions_emptyFilter.php"))JFile::delete( JPATH_SITE . "/administrator/components/com_joomfish/contentelements/translationFformoptions_emptyFilter.php");

    
}