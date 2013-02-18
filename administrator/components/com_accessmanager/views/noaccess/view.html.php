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

jimport( 'joomla.application.component.view');

class accessmanagerViewNoaccess extends JView{

	function display($tpl = null){	
		
		switch (JRequest::getVar('type', '')) {
			case 'module':
				$message = 'COM_ACCESSMANAGER_NO_PERMISSION_MODULE';
				break;
			case 'component':
				$message = 'COM_ACCESSMANAGER_NO_PERMISSION_COMPONENT';
				break;
			case 'menuitem':
				$message = 'COM_ACCESSMANAGER_NO_PERMISSION_MENUITEM';
				break;
			case 'plugin':
				$message = 'COM_ACCESSMANAGER_NO_PERMISSION_PLUGIN';
				break;
		}
		$this->assignRef('message', $message);

		parent::display($tpl);
	}
	
}
?>