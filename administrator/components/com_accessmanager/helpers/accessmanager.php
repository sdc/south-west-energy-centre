<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 1.3.1
* @copyright Copyright (C) 2013 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;

class accessmanagerHelper{	

	public $inherited_right = '';
	public $backend_usergroups = array(8);

	public static function addSubmenu($vName = 'accessmanager', $am_config){		
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_CPANEL'),
			'index.php?option=com_accessmanager&view=panel',
			$vName == 'panel'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_CONFIG'),
			'index.php?option=com_accessmanager&view=configuration',
			$vName == 'configuration'
		);				
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_USERS'),
			'index.php?option=com_accessmanager&view=users',
			$vName == 'users'
		);	
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_ACCESS_VIEWING'),
			'index.php?option=com_accessmanager&view=accessvievving',
			$vName == 'accessvievving'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_ACCESS_EDITTING'),
			'index.php?option=com_accessmanager&view=accessedit',
			$vName == 'accessedit'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_TOOLS'),
			'index.php?option=com_accessmanager&view=tools',
			$vName == 'tools'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ACCESSMANAGER_INFO'),
			'index.php?option=com_accessmanager&view=info',
			$vName == 'info'
		);		
	}
	
	function get_access_rights($type, $based_on){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT item, `$based_on` ".		
		"FROM #__accessmanager_rights ".
		"WHERE `$based_on`<>0 ".		
		"AND type='$type' "
		);		
		$rows = $database->loadObjectList();	
		$rights = array();
		foreach($rows as $row){	
			if($based_on=='level'){
				$grouplevel = $row->level;
			}else{
				$grouplevel = $row->group;
			}			
			$rights[] = $row->item.'__'.$grouplevel;
		}		
		return $rights;
	}
	
	function get_access_rights_backend($type){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT item, `group`, access ".		
		"FROM #__accessmanager_rights ".
		"WHERE `group`<>0 ".		
		"AND type='$type' "
		);		
		$rows = $database->loadObjectList();	
		$rights = array();
		foreach($rows as $row){					
			$rights[] = $row->item.'__'.$row->group.'__'.$row->access;
		}		
		return $rights;
	}
	
	function get_access_select($id, $access, $rights, $type, $config){
		switch ($access) {
		case '':
			$img_class = 'i';
			break;
		case '1':
			default:
			$img_class = 'a';
			break;
		case '0':
			default:
			$img_class = 'd';
			break;
		}
		$selected = ' selected="selected"';
		$html = '<table cellspacing="0" cellpadding="0"><tr>';
			$html .= '<td class="am_'.$img_class.'" id="img_'.$id.'">';
			$html .= '<p class="am_width22"></p>';			
			$html .= '</td>';
			$html .= '<td>';
				$html .= '<select name="access_item[\''.$id.'\']" id="'.$id.'" onchange="on_select_change(\''.$id.'\');" >';
					$html .= '<option value=""';
					if($access==''){
						$html .= $selected;
					}
					$html .= ' class="am_i">'.JText::_('COM_ACCESSMANAGER_INHERITED').'</option>';
					$html .= '<option value="1"';
					if($access=='1'){
						$html .= $selected;
					}
					$html .= '>'.JText::_('COM_ACCESSMANAGER_ALLOWED').'</option>';
					$html .= '<option value="0"';
					if($access=='0'){
						$html .= $selected;
					}
					$html .= '>'.JText::_('COM_ACCESSMANAGER_DENIED').'</option>';
				$html .= '</select>';
			$html .= '</td>';
			$html .= '<td id="target_'.$id.'" class="target">';
			if($access==''){
				$html .= '=';
				$temp = explode('__', $id);
				$item = $temp[0];
				$group = $temp[1];				
				$this->inherited_right = '';				
				$this->get_inherited_right($item, $group, $rights, $type, $config);				
				$html .= '<img src="components/com_accessmanager/images/right_';
				if($this->inherited_right=='1'){
					$html .= 'allow';
				}else{
					$html .= 'deny';
				}
				$html .= '_calculated.gif" alt="" />';
			}else{
				$html .= '<p class="am_width22"></p>';
			}		
			$html .= '</td>';
		$html .= '</tr></table>';
		return $html;
	}
	
	function get_access_select_all($usergroup){
		$html = '<select name="checkall[]" id="checkall_'.$usergroup.'" onchange="select_all(\''.$usergroup.'\',this.id);" >';
			$html .= '<option value="none">'.JText::_('COM_ACCESSMANAGER_SELECTALL_B').'</option>';
			$html .= '<option value="">'.JText::_('COM_ACCESSMANAGER_INHERITED').'</option>';
			$html .= '<option value="1">'.JText::_('COM_ACCESSMANAGER_ALLOWED').'</option>';
			$html .= '<option value="0">'.JText::_('COM_ACCESSMANAGER_DENIED').'</option>';
		$html .= '</select>';
		return $html;
	}
	
	function get_inherited_right($item, $group, $rights, $type, $config){
		
		$groups = $this->get_all_usergroups();
				
		//get parent group
		$parent = 'no';
		foreach($groups as $row){	
			if($row->id==$group){
				$parent = $row->parent_id;
				break;
			}
		}
		
		//if parent is public, set to default
		if($type=='modulebackend' && $group=='1'){			
			$this->inherited_right = $config['modulebackend_default'];			
			return;
		}
		if($type=='componentbackend' && $group=='1'){			
			$this->inherited_right = $config['componentbackend_default'];			
			return;
		}
		if($type=='menuitembackend' && $group=='1'){			
			$this->inherited_right = $config['menuitembackend_default'];			
			return;
		}
		if($type=='pluginbackend' && $group=='1'){			
			$this->inherited_right = $config['pluginbackend_default'];			
			return;
		}
		
		//check access for this item in parent group
		$access = '';
		$needle_1 = $item.'__'.$group.'__1';				
		if(in_array($needle_1, $rights)){
			$access = '1';
		}
		$needle_0 = $item.'__'.$group.'__0';
		if(in_array($needle_0, $rights)){
			$access = '0';
		}
		
		//recurse or parse
		if($access=='' && $parent!='no'){
			//parent is also inheriting so go level up			
			$this->get_inherited_right($item, $parent, $rights, $type, $config);
		}else{
			$this->inherited_right = $access;
		}		
	}
	
	function get_all_usergroups(){
		static $all_usergroups;
		$database = JFactory::getDBO();		
		if(!$all_usergroups){		
			$database->setQuery("SELECT id, title, parent_id FROM #__usergroups ");
			$all_usergroups = $database->loadObjectList();
		}
		return $all_usergroups;
	}
	
	function check_plugin_enabled($folder, $plugin){
		$database = JFactory::getDBO();		
		$database->setQuery("SELECT enabled "
		." FROM #__extensions "
		." WHERE element='$plugin' AND type='plugin' AND folder='$folder' "
		." LIMIT 1 "
		);
		$rows = $database->loadObjectList();
		$enabled = 0;
		foreach($rows as $row){				
			$enabled = $row->enabled;			
		}
		return $enabled;
	}
	
	function clean_access_table($type, $table, $column_enabled){
	
		$database = JFactory::getDBO();
		
		//get items array
		$database->setQuery("SELECT id "
		." FROM #__$table "
		." WHERE $column_enabled<>'-2' "
		);
		$items_array = $database->loadResultArray();
		
		//get rights for this type
		$database->setQuery("SELECT id, item "
		." FROM #__accessmanager_rights "
		." WHERE type='$type' "
		);
		$rights = $database->loadObjectList();
		
		//check if item still exists
		$rights_to_delete = array();
		foreach($rights as $right){					
			if(!in_array($right->item, $items_array)){	
				//item does no longer exist, so delete right for it	
				$rights_to_delete[] = $right->id;					
			}			
		}
		
		//do the actual deleting
		if(count($rights_to_delete)){
			$rights_to_delete_string = implode(',', $rights_to_delete);
			$database->setQuery("DELETE FROM #__accessmanager_rights WHERE id IN ($rights_to_delete_string) ");
			$database->query();
		}
	}
	
	function check_for_code($file, $code){		
		if(!file_exists($file)){
			return 0;
		}
		if ($fp = @fopen($file, "rb")){	
			$null = NULL;					
			$file_string = file_get_contents($file, $null, $null, 0, 3000);				
			fclose ($fp);							
			if(strpos($file_string, $code)){			
				return 1;					
			}			
		}
	}
	
	function display_multigrouplevel_select_config($type, $am_config, $include_superadmin = 0, $backend_only = 0, $only_groups = 0){
	
		$db = JFactory::getDBO();
		
		//get groupslevels
		$query = $db->getQuery(true);
		if($am_config['based_on']=='level' && !$only_groups){			
			$query->select('id, title');
			$query->from('#__viewlevels');
			$query->order($am_config['level_sort']);	
		}else{		
			$query->select('a.id as id, a.title as title, COUNT(DISTINCT b.id) AS hyrarchy');
			$query->from('#__usergroups AS a');
			$query->leftJoin('#__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt');
			if($backend_only){
				$this->get_backend_usergroups();
				$backend_usergroups = implode(',', $this->backend_usergroups);
				$query->where('a.id in ('.$backend_usergroups.')');			
			}
			if(!$include_superadmin){
				$query->where('a.id <> '.$db->Quote('8'));	
			}			
			$query->group('a.id');
			$query->order('a.lft');				
		}	
		$accesslevels = $db->setQuery((string)$query);	
		$accesslevels = $db->loadObjectList();			
		
		$html = '<select name="'.$type.'_default_access[]" multiple="multiple" size="'.count($accesslevels).'">';
		foreach($accesslevels as $accesslevel){
			$html .= '<option value="'.$accesslevel->id.'"';
			if(in_array($accesslevel->id, $am_config[$type.'_default_access'])){
				$html .= ' selected="selected"';
			}
			$html .= '>';
			if($am_config['based_on']=='group' || $only_groups){	
				$html .= str_repeat('- ',$accesslevel->hyrarchy);	
			}						
			$html .= $accesslevel->title;						
			$html .= '</option>';
		}
		$html .= '</select>';		
		
		return $html;
	}	
	
	function give_authors_group_access($item, $type){
		$database = JFactory::getDBO();	
				
		//get authors groups
		require_once(JPATH_ROOT.DS.'components'.DS.'com_accessmanager'.DS.'checkaccess.php');
		$accessmanagerAccessChecker = new accessmanagerAccessChecker();
		$groups = $accessmanagerAccessChecker->get_user_grouplevels();
		//$groups = array(7);
		
		//inmsert right for each of the groups
		foreach($groups as $group){		
			if($group!='8'){
				$database->setQuery( "INSERT INTO #__accessmanager_rights SET item='$item', `group`='$group', type='$type', access='1' ");
				$database->query();
			}
		}
	}
	
	function get_backend_usergroups(){
	
		$db = JFactory::getDBO();
		
		//get main asset		
		$query = $db->getQuery(true);
		$query->select('rules');
		$query->from('#__assets');		
		$query->where('id='.$db->q('1'));		
		$asset = $db->setQuery($query);				
		$asset = $db->loadResult();	
		
		//make into array
		$registry = new JRegistry;
		$registry->loadJSON($asset);
		$asset_array = $registry->toArray();
		
		//get configured backend groups	
		$temp = $asset_array['core.login.admin'];		
		for($n = 0; $n < count($temp); $n++){
			$row = each($temp);
			if($row['value']=='1'){							
				$this->set_backend_usergroup($row['key']);
			}
		}
	}
	
	function set_backend_usergroup($group){
	
		$db = JFactory::getDBO();
			
		$this->backend_usergroups[] = $group;	

		//get child groups				
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__usergroups');
		$query->where('parent_id='.$group);
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
			
		foreach($rows as $row){		
			//recurse to get all children
			$this->set_backend_usergroup($row->id);
		}	
	}
	
	
	
	
	

}
?>