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

class accessmanagerModelComponentsbackend extends JModelList{	

	protected function populateState(){
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.		
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');					
		$this->setState('filter.search', $search);	

		// List state information.
		//parent::populateState('e.name', 'asc');
		//workaround for disfuntional code	
		$listDirn = JRequest::getVar('filter_order', 'e.name');			
		parent::populateState($listDirn, 'asc');
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){
	
		// Create a new query object.
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'e.extension_id, e.name, e.element'				
			)
		);
		$query->from('`#__extensions` AS e');
		
		// join level titles
		$query->select('l.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS l ON e.access=l.id ');	
		
		$query->where('e.type="component" ');		
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '') {
			// Escape the search token.
			$token	= $db->Quote('%'.$db->getEscaped($this->getState('filter.search')).'%');

			// Compile the different search clauses.
			$searches	= array();
			$searches[]	= 'e.name LIKE '.$token;
			$searches[]	= 'e.element LIKE '.$token;			

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}	
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'e.name')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
		
	}
}
?>