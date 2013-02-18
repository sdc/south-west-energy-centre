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

class accessmanagerViewPluginsBackend extends JView{

	protected $items;
	protected $pagination;
	protected $state;		

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_plugins'.DS.'helpers'.DS.'plugins.php');
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');	
		
		$this->controller->get_backend_usergroups();				
		
		//toolbar		
		JToolBarHelper::apply('pluginsbackend_apply');
		JToolBarHelper::save('pluginsbackend_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels('backend', false, true, 1);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db		
		$access_plugins = $helper->get_access_rights_backend('pluginbackend', 'group');	
		$this->assignRef('access_plugins', $access_plugins);
		
		//clean up rights in the table
		//$helper->clean_access_table('pluginsbackend', 'menu', 'published');
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_PLUGIN_ACCESS'), 'am_icon');	
		
		parent::display($tpl);
	}
	
}
?>