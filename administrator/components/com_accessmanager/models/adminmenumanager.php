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

jimport('joomla.application.component.modellist');

class accessmanagerModelAdminmenumanager extends JModelList{	
	
	protected function populateState(){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');		
		
		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search', '');		
		$this->setState('filter.search', $search);		
		
		$type = $app->getUserStateFromRequest($this->context.'.type', 'filter_type', '');
		if($type=='all' || !file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_adminmenumanager'.DS.'controller.php')){
			$type = '';
		}elseif($type==''){			
			//get first menu			
			$query = $database->getQuery(true);
			$query->select('id');
			$query->from('#__adminmenumanager_menus');			
			$query->order('name');
			$rows = $database->setQuery($query);				
			$rows = $database->loadObjectList();
				
			foreach($rows as $row){		
				$type = $row->id;	
				break;
			}
		}
		$this->setState('filter.type', $type);		

		// List state information.		
		$listDirn = JRequest::getVar('filter_order', 'i.ordertotal');			
		parent::populateState($listDirn, 'asc');
		
		
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.type');	

		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){
	
		// Create a new query object.
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		
		if(!file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_adminmenumanager'.DS.'controller.php')){
			//admin-menu-manager is not installed, so parse bogus query or else the database object goes bananas
			//would be easyer if we could parse an empty query and get an empty object back
			$query->select('*');
			$query->from('#__accessmanager_config');
			$query->where('id='.$db->q('nothing'));
		}else{
			//admin-menu-manager is installed
		
			// Select the required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'i.*'				
				)
			);
			$query->from('`#__adminmenumanager_menuitems` AS i');				
					
			// Filter the items over the search string if set.
			if ($this->getState('filter.search') !== '') {
				// Escape the search token.
				$token	= $db->Quote('%'.$db->getEscaped($this->getState('filter.search')).'%');
				$search_id = intval($this->getState('filter.search'));
				// Compile the different search clauses.
				$searches	= array();
				$searches[]	= 'i.title LIKE '.$token;
				$searches[]	= 'i.id = '.$search_id;			
	
				// Add the clauses to the query.
				$query->where('('.implode(' OR ', $searches).')');
			}
			
			// filter menu type
			if ($type = $this->getState('filter.type')) {
				$query->where('i.menu = '.$db->quote($type));
			}				
			
			// Add the list ordering clause.
			$query->order($db->getEscaped($this->getState('list.ordering', 'i.ordertotal')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		}
		
		//echo nl2br($query);
		return $query;
		
	}
}
?>