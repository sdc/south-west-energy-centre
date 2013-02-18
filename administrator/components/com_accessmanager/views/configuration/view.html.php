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

class accessmanagerViewConfiguration extends JView{

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		$system_plugin_correct_order = $this->check_system_plugin_order();
		$this->assignRef('system_plugin_correct_order', $system_plugin_correct_order);		
		
		//toolbar	
		if (JFactory::getUser()->authorise('core.admin', 'com_accessmanager')) {
			JToolBarHelper::preferences('com_accessmanager');
		}		
		JToolBarHelper::custom( 'config_save', 'save.png', 'save_f2.png', JText::_('JSAVE'), false, false );	
		JToolBarHelper::custom( 'config_apply', 'apply.png', 'apply_f2.png', JText::_('JTOOLBAR_APPLY'), false, false );
		JToolBarHelper::custom( 'cancel', 'cancel.png', 'cancel_f2.png', JText::_('JTOOLBAR_CANCEL'), false, false );			
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_CONFIG'), 'am_icon');	

		parent::display($tpl);
	}
	
	function get_groups(){
		$database = JFactory::getDBO();
		$database->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' WHERE a.id<>8 AND a.id<>1 '.
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$groups = $database->loadObjectList();
		foreach ($groups as &$group) {
			$group->text = str_repeat('- ',$group->level).$group->text;
		}
		return $groups;
	}
	
	function check_system_plugin_order(){
	
		$database = JFactory::getDBO();	
				
		$system_plugin_correct_order = 0;
		
		$database->setQuery("SELECT element, ordering "
		." FROM #__extensions "
		." WHERE type='plugin' AND folder='system' "
		." ORDER BY ordering ASC "
		);
		$rows = $database->loadObjectList();
		$order = array();
		$am_order = 0;
		foreach($rows as $row){	
			//echo $row->element.'<br>';
			$order[] = $row->element;	
			if($row->element=='accessmanager'){
				$am_order = $row->ordering;
			}
		}
		
		//check order
		if(
		//first position all good
		($order[0]=='accessmanager') || 
		//if second and first is admintools or oneclickaction
		($order[1]=='accessmanager' && ($order[0]=='oneclickaction' || $order[0]=='admintools')) ||
		//if third and first and second are admintools or oneclickaction 
		($order[2]=='accessmanager' && ($order[0]=='oneclickaction' || $order[0]=='admintools') && ($order[1]=='oneclickaction' || $order[1]=='admintools'))
		){			
			$system_plugin_correct_order = 1;
		}		
		
		//check order is not 0
		if($am_order==0){
			//not installed or order is '0'
			$system_plugin_correct_order = 0;
		}
		
		return $system_plugin_correct_order;
	}
	
	
}
?>