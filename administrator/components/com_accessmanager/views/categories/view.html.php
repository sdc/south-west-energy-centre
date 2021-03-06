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

class accessmanagerViewCategories extends JView{

	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null){
	
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
	
		$database = JFactory::getDBO();
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		//clean up rights in the table
		$helper->clean_access_table('category', 'categories', 'published');
		
		$plugin_search_categoriesaccessmanager_enabled = $this->helper->check_plugin_enabled('search', 'categoriesaccessmanager');
		$this->assignRef('plugin_search_categoriesaccessmanager_enabled', $plugin_search_categoriesaccessmanager_enabled);
		
		$plugin_search_categories_enabled = $this->helper->check_plugin_enabled('search', 'categories');
		$this->assignRef('plugin_search_categories_enabled', $plugin_search_categories_enabled);	
		
		//toolbar		
		JToolBarHelper::apply('access_categories_apply');
		JToolBarHelper::save('access_categories_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
	
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels(0, 0, 0, 1);
		$this->assignRef('am_grouplevels', $am_grouplevels);
		
		//get access from db
		$helper = new accessmanagerHelper();
		$access_categories = $helper->get_access_rights('category', $this->controller->am_config['based_on']);	
		$this->assignRef('access_categories', $access_categories);		
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS'), 'am_icon');	

		parent::display($tpl);
	}
}
?>