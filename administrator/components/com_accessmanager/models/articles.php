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

class accessmanagerModelArticles extends JModelList{	

	protected function populateState(){
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.		
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'items_search', '', 'string');					
		$this->setState('filter.search', $search);	
		
		// filter access
		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', '0', 'int');					
		$this->setState('filter.access', $access);	
		
		// filter published
		$published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');					
		$this->setState('filter.published', $published);	
		
		// filter category
		$category = $app->getUserStateFromRequest($this->context.'.filter.category', 'items_category_filter', '', 'int');					
		$this->setState('filter.category', $category);	

		// filter language
		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');					
		$this->setState('filter.language', $language);	
		
		$accesscolumn = $app->getUserStateFromRequest('accessmanager.accesscolumn', 'accesscolumn', 'yes');
		$this->setState('accesscolumn', $accesscolumn);
		
		// List state information.
		//parent::populateState('c.title', 'asc');
		//workaround for disfuntional code	
		$listDirn = JRequest::getVar('filter_order', 'c.title');			
		parent::populateState($listDirn, 'asc');
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.category');
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
				'c.id, c.title, c.state '				
			)
		);
		$query->from('`#__content` AS c');
		
		// not content items from the trash
		//$query->where("(c.state='-1' OR c.state='0' OR c.state='1')");
		
		// join level titles
		$query->select('l.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS l ON c.access=l.id ');
		
		// join categories for category titles
		$query->select('d.title AS categorytitle');	
		$query->join('LEFT', '#__categories AS d ON d.id=c.catid ');
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '') {
			// Escape the search token.
			$token	= $db->Quote('%'.$db->getEscaped($this->getState('filter.search')).'%');
			$search_id = intval($this->getState('filter.search'));
			// Compile the different search clauses.
			$searches	= array();
			$searches[]	= 'c.title LIKE '.$token;
			$searches[]	= 'c.id = '.$search_id;			

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}
		
		// filter access
		if($this->getState('filter.access')){	
			$query->where("(c.access='".$this->getState('filter.access')."')");
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('c.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(c.state = 0 OR c.state = 1)');
		}
		
		// filter categories
		if($this->getState('filter.category')){	
			$query->where("(c.catid='".$this->getState('filter.category')."')");
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('c.language = '.$db->quote($language));
		}
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'c.title')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
		
	}
	
}
?>