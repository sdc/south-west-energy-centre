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

class accessmanagerModelmodulesBackend extends JModelList{	

	protected function populateState(){
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');		
		
		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '');
		$this->setState('filter.search', $search);	
		
		$siteadmin = $app->getUserStateFromRequest($this->context.'.filter.siteadmin', 'filter_siteadmin', 0, 'int', false);
		$this->setState('filter.siteadmin', $siteadmin);	
		
		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);
		
		$accessId = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);	

		$position = $app->getUserStateFromRequest($this->context.'.filter.position', 'filter_position', '', 'string');
		$this->setState('filter.position', $position);

		$module = $app->getUserStateFromRequest($this->context.'.filter.module', 'filter_module', '', 'string');
		$this->setState('filter.module', $module);		

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		$accesscolumn = $app->getUserStateFromRequest('accessmanager.accesscolumn', 'accesscolumn', 'yes');
		$this->setState('accesscolumn', $accesscolumn);

		// List state information.
		//parent::populateState('m.title', 'asc');
		//workaround for disfuntional code	
		$listDirn = JRequest::getVar('filter_order', 'm.title');			
		parent::populateState($listDirn, 'asc');
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');	
		$id	.= ':'.$this->getState('filter.siteadmin');		
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.position');
		$id	.= ':'.$this->getState('filter.module');
		$id	.= ':'.$this->getState('filter.language');

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
				'm.id, m.title, m.published'
			)
		);
		$query->from('#__modules AS m');	
		
		// join level titles
		$query->select('al.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS al ON m.access=al.id ');	
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== ''){			
			// Escape the search token.
			$token	= $db->Quote('%'.$db->getEscaped($this->getState('filter.search')).'%');
			$search_id = intval($this->getState('filter.search'));
			// Compile the different search clauses.
			$searches	= array();
			$searches[]	= 'm.title LIKE '.$token;
			$searches[]	= 'm.id = '.$search_id;			

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}
		
		// Filter by site or admin		
		$siteadmin = $this->getState('filter.siteadmin');
		if (is_numeric($siteadmin)) {
			$query->where('m.client_id = '.(int) $siteadmin);
		}
		
		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('m.published = '.(int) $state);
		}else if ($state === '') {
			$query->where('(m.published IN (0, 1))');
		}
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('m.access = ' . (int) $access);
		}
		
		// Filter by position
		$position = $this->getState('filter.position');
		if ($position) {
			$query->where('m.position = '.$db->Quote($position));
		}
		
		// Filter by module
		$module = $this->getState('filter.module');
		if ($module) {
			$query->where('m.module = '.$db->Quote($module));
		}		
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('m.language = '.$db->quote($language));
		}
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'm.title')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
		
	}
}
?>