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

class accessmanagerModelMenuitemsBackend extends JModelList{	
	
	protected function populateState(){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');		
		
		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search', '');		
		$this->setState('filter.search', $search);		
		
		$type = $app->getUserStateFromRequest($this->context.'.type', 'filter_type', '');
		if($type=='all'){
			$type = '';
		}elseif($type==''){
			$type = 'mainmenu';
			//get default menu itemtype
			$database->setQuery("SELECT menutype "
			."FROM #__menu "
			."WHERE home='1' "
			);
			$rows = $database->loadObjectList();
			foreach($rows as $row){	
				$type = $row->menutype;
			}
		}
		$this->setState('filter.type', $type);

		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		$accesscolumn = $app->getUserStateFromRequest('accessmanager.accesscolumn', 'accesscolumn', 'yes');
		$this->setState('accesscolumn', $accesscolumn);

		// List state information.
		//parent::populateState('m.lft', 'asc');
		//workaround for disfuntional code	
		$listDirn = JRequest::getVar('filter_order', 'm.lft');			
		parent::populateState($listDirn, 'asc');
		
		
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
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
				'm.id, m.menutype, m.title, m.published, m.type, m.level, m.access'				
			)
		);
		$query->from('`#__menu` AS m');
			
		// join level titles
		$query->select('l.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS l ON m.access=l.id ');	
		
		//no backend menu items
		$query->where('m.client_id<>"1" ');	
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '') {
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
		
		// filter menu type
		if ($type = $this->getState('filter.type')) {
			$query->where('m.menutype = '.$db->quote($type));
		}
		
		// filter on access
		if ($access = $this->getState('filter.access')) {
			$query->where('m.access = ' . (int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('m.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(m.published IN (0, 1))');
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('m.language = '.$db->quote($language));
		}
		
		//not the root
		$query->where('m.id<>"1" ');		
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'm.lft')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		//echo $this->getState('list.ordering', 'm.lft');
		//echo nl2br($query);
		return $query;
		
	}
}
?>