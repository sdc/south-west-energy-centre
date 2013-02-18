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

class accessmanagerViewPart extends JView
{
	function display($tpl = null)
	{
		$database = JFactory::getDBO();
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		//toolbar		
		JToolBarHelper::custom( 'part_save', 'save.png', 'save_f2.png', JText::_('JSAVE'), false, false );
		JToolBarHelper::custom( 'cancel', 'cancel.png', 'cancel_f2.png', JText::_('JTOOLBAR_CANCEL'), false, false );
		
		$sub_task = JRequest::getVar('sub_task', '');
		$this->assignRef('sub_task', $sub_task);
		
		parent::display($tpl);
	}
}
?>