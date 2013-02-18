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

class accessmanagerModelPluginsBackend extends JModelList{	
	
	protected function populateState(){
	
		// Initialise variables.
		$app = JFactory::getApplication('administrator');		
		
		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search', '');		
		$this->setState('filter.search', $search);	
			
		$enabled = $app->getUserStateFromRequest($this->context.'.enabled', 'filter_enabled', '');
		$this->setState('filter.enabled', $enabled);
		
		$type = $app->getUserStateFromRequest($this->context.'.type', 'filter_type', '');		
		$this->setState('filter.type', $type);
		
		// List state information.
		//parent::populateState('m.lft', 'asc');
		//workaround for disfuntional code	
		$listDirn = JRequest::getVar('filter_order', 'p.name');			
		parent::populateState($listDirn, 'asc');
		
		
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.enabled');
		$id	.= ':'.$this->getState('filter.type');
		
		return parent::getStoreId($id);
	}
	
	protected function _getList($query, $limitstart=0, $limit=0){
		$database = JFactory::getDBO();
		$search = $this->getState('filter.search');
		$ordering = $this->getState('list.ordering', 'ordering');
		if ($ordering == 'name' || (!empty($search) && stripos($search, 'id:') !== 0)) {
			$database->setQuery($query);
			$result = $database->loadObjectList();
			$this->translate($result);
			if (!empty($search)) {
				foreach($result as $i=>$item) {
					if (!preg_match("/$search/i", $item->name)) {
						unset($result[$i]);
					}
				}
			}
			$lang = JFactory::getLanguage();
			JArrayHelper::sortObjects($result, 'name', $this->getState('list.direction') == 'desc' ? -1 : 1, true, $lang->getLocale());
			$total = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;
			if ($total < $limitstart) {
				$limitstart = 0;
				$this->setState('list.start', 0);
			}
			return array_slice($result, $limitstart, $limit ? $limit : null);
		}
		else {
			if ($ordering == 'ordering') {
				$query->order('a.folder ASC');
				$ordering = 'a.ordering';
			}
			$query->order($database->quoteName($ordering) . ' ' . $this->getState('list.direction'));
			if($ordering == 'folder') {
				$query->order('a.ordering ASC');
			}
			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);
			return $result;
		}
	}
	
	protected function getListQuery(){
	
		// Create a new query object.
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'p.extension_id, p.name, p.type, p.element, p.folder, p.enabled'				
			)
		);
		$query->from('`#__extensions` AS p');		
				
		//only plugins
		$query->where('p.type="plugin" ');	
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '') {
			// Escape the search token.
			$token	= $db->Quote('%'.$db->getEscaped($this->getState('filter.search')).'%');
			$search_id = intval($this->getState('filter.search'));
			// Compile the different search clauses.
			$searches	= array();
			$searches[]	= 'p.name LIKE '.$token;
			$searches[]	= 'p.id = '.$search_id;			

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}
		
		// filter menu type
		if ($type = $this->getState('filter.type')) {
			$query->where('p.folder = '.$db->quote($type));
		}
		
		// Filter by published state
		$enabled = $this->getState('filter.enabled');
		if (is_numeric($enabled)) {
			$query->where('p.enabled = ' . (int) $enabled);
		} else if ($enabled === '') {
			$query->where('(p.enabled IN (0, 1))');
		}
		
		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'p.name')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		
		//echo $this->getState('list.ordering', 'm.lft');
		//echo nl2br($query);
		return $query;
		
	}
	
	protected function translate(&$items){
		$lang = JFactory::getLanguage();
		foreach($items as &$item) {
			$source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
			$extension = 'plg_' . $item->folder . '_' . $item->element;
				$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($extension . '.sys', $source, null, false, false)
			||	$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
			$item->name = JText::_($item->name);
		}
	}
}
?>