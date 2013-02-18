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

class accessmanagerViewAdminmenumanager extends JView{

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
		
		//toolbar		
		JToolBarHelper::apply('adminmenumanager_apply');
		JToolBarHelper::save('adminmenumanager_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels(true, true, false, true);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db
		$helper = new accessmanagerHelper();
		$access_menuitems = $helper->get_access_rights('adminmenumanager', $this->controller->am_config['based_on']);	
		$this->assignRef('access_menuitems', $access_menuitems);
		
		//clean up rights in the table
		$helper->clean_access_table('adminmenumanager', 'adminmenumanager_menuitems', 'published');
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_ADMINMENUMANAGER_ACCESS'), 'am_icon');	
		
		parent::display($tpl);
	}
	
	static function get_menu_type_options(){		
		
		$db = JFactory::getDBO();
		
		$options = array();
		if(file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_adminmenumanager'.DS.'controller.php')){
			$query = $db->getQuery(true);
			$query->select('id, name');
			$query->from('#__adminmenumanager_menus');
			$query->order('name');
			$menutypes = $db->setQuery($query);				
			$menutypes = $db->loadObjectList();	
			
			foreach($menutypes as $menutype){
				$options[] = JHtml::_('select.option', $menutype->id, $menutype->name);					
			}
		}			
				
		return $options;

	}
	
}
?>