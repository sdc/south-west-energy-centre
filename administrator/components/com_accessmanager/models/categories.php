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

class accessmanagerModelCategories extends JModelList{	

	protected function populateState(){
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');		
		
		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search', '');
		$this->setState('filter.search', $search);

		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		$accesscolumn = $app->getUserStateFromRequest('accessmanager.accesscolumn', 'accesscolumn', 'yes');		
		$this->setState('accesscolumn', $accesscolumn);

		// List state information.
		//parent::populateState('c.lft', 'asc');
		//workaround for disfuntional code	
		$listDirn = JRequest::getVar('filter_order', 'c.lft');			
		parent::populateState($listDirn, 'asc');
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
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
				'c.id, c.title, c.published, c.level'
			)
		);
		$query->from('#__categories AS c');
		
		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = c.language');
		
		// join level titles
		$query->select('al.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS al ON c.access=al.id ');

		//where extension is com_content
		$query->where("c.extension = 'com_content' ");
		
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
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('c.access = ' . (int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('c.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(c.published IN (0, 1))');
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('c.language = '.$db->quote($language));
		}
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'c.lft')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
		
	}
}
?>