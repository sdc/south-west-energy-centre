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

class accessmanagerViewMenuitemsBackend extends JView{

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
		
		//toolbar		
		JToolBarHelper::apply('menuitemsbackend_apply');
		JToolBarHelper::save('menuitemsbackend_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels('backend', false, true, 1);	
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db		
		$access_menuitems = $helper->get_access_rights_backend('menuitembackend', 'group');	
		$this->assignRef('access_menuitems', $access_menuitems);
		
		//clean up rights in the table
		$helper->clean_access_table('menuitembackend', 'menu', 'published');
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_MENUITEM_ACCESS'), 'am_icon');	
		
		parent::display($tpl);
	}
	
	static function get_menu_type_options(){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT menutype, title FROM #__menu_types ORDER BY title ASC"  );
		$menutypes = $database->loadObjectList();
		$options = array();
		foreach($menutypes as $menutype){
			$options[] = JHtml::_('select.option', $menutype->menutype, $menutype->title);					
		}		
		return $options;
	}
	
}
?>