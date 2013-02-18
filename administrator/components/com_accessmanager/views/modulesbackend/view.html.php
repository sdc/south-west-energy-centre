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

class accessmanagerViewModulesBackend extends JView{

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
		JToolBarHelper::apply('modulesbackend_apply');
		JToolBarHelper::save('modulesbackend_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );	
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels('backend', false, true, 1);
		$this->assignRef('am_grouplevels', $am_grouplevels);			
		
		//get access from db		
		$access_modules = $helper->get_access_rights_backend('modulebackend', 'group');	
		$this->assignRef('access_modules', $access_modules);
		
		//clean up rights in the table
		$helper->clean_access_table('modulebackend', 'modules', 'published');	
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'), 'am_icon');
		
		parent::display($tpl);
	}
	
	static function getClientOptions(){
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '0', JText::_('JSITE'));
		$options[]	= JHtml::_('select.option', '1', JText::_('JADMINISTRATOR'));
		return $options;
	}
	
	static function getStateOptions(){
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	JText::_('JENABLED'));
		$options[]	= JHtml::_('select.option',	'0',	JText::_('JDISABLED'));
		$options[]	= JHtml::_('select.option',	'-2',	JText::_('JTRASH'));
		return $options;
	}
	
	static function getPositions($clientId){
	
		jimport('joomla.filesystem.folder');

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('DISTINCT(position)');
		$query->from('#__modules');
		$query->where('`client_id` = '.(int) $clientId);
		$query->order('position');

		$db->setQuery($query);
		$positions = $db->loadResultArray();
		$positions = (is_array($positions)) ? $positions : array();		

		// Build the list
		$options = array();
		foreach ($positions as $position) {
			if($position!=''){
				$options[]	= JHtml::_('select.option', $position, $position);
			}
		}
		return $options;
	}
	
	public static function getModules($clientId){
	
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('DISTINCT(m.module) AS value, e.name AS text');
		$query->from('#__modules AS m');
		$query->join('LEFT', '#__extensions AS e ON e.element=m.module');
		$query->where('m.`client_id` = '.(int)$clientId);

		$db->setQuery($query);
		$modules = $db->loadObjectList();
		foreach ($modules as $i=>$module) {
			$extension = $module->value;
			$path = $clientId ? JPATH_ADMINISTRATOR : JPATH_SITE;
			$source = $path . "/modules/$extension";
			$lang = JFactory::getLanguage();
				$lang->load("$extension.sys", $path, null, false, false)
			||	$lang->load("$extension.sys", $source, null, false, false)
			||	$lang->load("$extension.sys", $path, $lang->getDefault(), false, false)
			||	$lang->load("$extension.sys", $source, $lang->getDefault(), false, false);
			$modules[$i]->text = JText::_($module->text);
		}
		JArrayHelper::sortObjects($modules,'text');
		return $modules;
	}
}
?>