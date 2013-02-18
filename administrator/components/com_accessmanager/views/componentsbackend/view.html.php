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

class accessmanagerViewComponentsbackend extends JView{

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');	
		
		$this->controller->get_backend_usergroups();	
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels('backend', false, true, 1);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db		
		$access_components = $helper->get_access_rights_backend('componentbackend', 'group');			
		$this->assignRef('access_components', $access_components);
				
		//toolbar			
		JToolBarHelper::apply('componentsbackend_apply');
		JToolBarHelper::save('componentsbackend_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );	
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS'), 'am_icon');		
		
		parent::display($tpl);
	}
	
	
}
?>