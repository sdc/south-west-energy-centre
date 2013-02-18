<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 1.3.1
* @copyright Copyright (C) 2013 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

class plgSystemAccessmanager extends JPlugin{

	protected $version_type = 'pro';	
	protected $am_config;
	protected $user_id;
	protected $is_super_user = 0;
	protected $option;
	protected $view;
	protected $layout;
	protected $login_url = '';
	protected $trial_valid = 1;
	protected $inherited_right = '';
	protected $access_script;	
	protected $fua_enabled = 0;
	protected $helper;
	public $subject;
	public $config;
	
	function plgSystemAccessmanager(& $subject, $config){
	
		$database = JFactory::getDBO();	
		$app = JFactory::getApplication();
		parent::__construct($subject, $config);		
		
		$this->subject = $subject;
		$this->config = $config;
		
		require_once(JPATH_ROOT.DS.'components'.DS.'com_accessmanager'.DS.'checkaccess2.php');
		$this->access_script = new accessmanagerAccessChecker();
		
		if($app->isAdmin()){
			require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_accessmanager'.DS.'helpers'.DS.'accessmanager.php');
			$this->helper = new accessmanagerHelper();
		}
		
		//get config		
		$this->am_config = $this->access_script->get_config();
		
		//check trial version
		if($this->version_type=='trial'){			
			$this->trial_valid = 0;		
			if($this->am_check_trial_version()){
				$this->trial_valid = 1;
			}			
		}	
		
		//get user id
		$user = JFactory::getUser();		
		$this->user_id = $user->get('id');	
		
		$this->is_super_user = $this->access_script->get_is_super_user();
		$this->fua_enabled = $this->access_script->get_fua_enabled();
		
		//dirty workaround to prevent site dying when used together with any of 
		//these plugins which load the module helper outside the plugin class
		if(file_exists(JPATH_PLUGINS.DS.'system'.DS.'advancedmodules'.DS.'advancedmodules.php') || 
		file_exists(JPATH_PLUGINS.DS.'system'.DS.'metamod'.DS.'metamod.php') ||
		file_exists(JPATH_PLUGINS.DS.'system'.DS.'plg_jamenuparams'.DS.'plg_jamenuparams.php') ||
		file_exists(JPATH_PLUGINS.DS.'system'.DS.'plg_gkextmenu'.DS.'plg_gkextmenu.php') ||
		file_exists(JPATH_PLUGINS.DS.'system'.DS.'jat3'.DS.'jat3.php') ||
		file_exists(JPATH_PLUGINS.DS.'system'.DS.'nnframework'.DS.'nnframework.php')
		){	
			if(JRequest::getVar('option', '')=='com_search' && file_exists(JPATH_PLUGINS.DS.'system'.DS.'nnframework'.DS.'nnframework.php')){
				$this->onAfterRoute();//to deal with the nnframework override on searches
			}		
			$this->onAfterInitialise();
		}
		
		$uri = JFactory::getURI();
		$request_url = $uri->toString();
		$return_url = base64_encode($request_url);	
		$this->login_url = JURI::root().'index.php?option=com_users&view=login&return='.$return_url;				
	}
	
	function onAfterRender(){	
	
		$app = JFactory::getApplication();
		$this->option = JRequest::getVar('option', '');		
		$this->view = JRequest::getVar('view', '');	
		$this->layout = JRequest::getVar('layout', '');	
		
		if($this->fua_enabled){
			return true;
		}
		
		if($this->am_config['am_enabled']){
			if(!$app->isAdmin()){	
				if($this->trial_valid){										
					$this->check_component_access_frontend();
					$this->check_menu_access();
					$this->check_article_view_access();				
				}							
			}else{				
				if($this->trial_valid){
					$this->check_module_access_backend();
					$this->check_menuitem_access_backend();
				}
				$this->check_component_access_backend();
				$this->check_plugin_access_backend();				
			}			
		}
		//parts access and dropdown menu
		$this->work_on_buffer();
	}	

	function work_on_buffer(){
		
		$app = JFactory::getApplication();		
		$buffer = JResponse::getBody();	
		
		if(!$app->isAdmin() && $this->am_config['am_enabled']){
			//frontend
				
			//check for any parts to process for performance			
			$pos = strpos($buffer, '{am_part');
			if($pos){				
				$regex = "/{am_part(.*?){\/am_part}/is";			
				preg_match_all($regex, $buffer, $matches);						
				$regex_id = "/id=(.*?)}/is";
				$part_tags = array_unique($matches[1]);
				
				foreach($part_tags as $part_tag){
				
					//take it apart
					$whole_tag = '{am_part'.$part_tag.'{/am_part}';	
					$tag_array = explode('{else}', $part_tag);
					$first_bit = $tag_array[0];
					preg_match_all($regex_id, $first_bit, $matches);					
					$part_id = $matches[1][0];
					$id_bit = 'id='.$part_id.'}';
					$content_with_access = str_replace($id_bit, '', $first_bit);
					$content_no_access = '';
					if(isset($tag_array[1])){
						$content_no_access = $tag_array[1];
					}												
					
					//check if parts restrictions is enabled
					if(!$this->am_config['part_active']){
						//parts restrictions is not enabled
						//check in config what to do
						if($this->am_config['parts_not_active']=='as_access'){
							//show as if user has access
							$buffer = str_replace($whole_tag, $content_with_access, $buffer);	
						}elseif($this->am_config['parts_not_active']=='as_no_access'){
							//show as if user has no access
							$buffer = str_replace($whole_tag, $content_no_access, $buffer);	
						}elseif($this->am_config['parts_not_active']=='nothing'){
							//take complete tag out
							$buffer = str_replace($whole_tag, '', $buffer);	
						}
						//when option is code, do no replacing at all										
					}else{
						//parts restrictions is enabled	
					
						$has_access_part = true;
						if(!$this->access_script->check_access($part_id, 'part', $this->am_config['part_multigroup_access_requirement'], $this->am_config['part_reverse_access'])){	
							$has_access_part = false;
						}
						
						//replace tag with access or no access content
						if($has_access_part || $this->is_super_user){
							//show content with access						
							$buffer = str_replace($whole_tag, $content_with_access, $buffer);														
						}else{
							//show content no access
							$buffer = str_replace($whole_tag, $content_no_access, $buffer);
						}
					
					}				
					
				}
			}//end parts restrictions	
			
			
			if($this->option=='com_content' && $this->view=='form' && $this->am_config['article_active']){					
				$buffer = $this->display_multigrouplevel_select($buffer, 'article');
			}						
						
		}else{
			//backend
			
			//dropdown menu access manager
			if($this->option=='com_accessmanager'){				
				$regex = "/\<ul id=\"submenu\"\>(.*?)\<\/ul\>/is";			
				preg_match_all($regex, $buffer, $matches);	
				if(isset($matches[1][0])){			
					$ori = $matches[1][0];
					$accessvievving = array();		
					$accessvievving[JText::_('COM_ACCESSMANAGER_ITEM_ACCESS')] = 'articles';
					$accessvievving[JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS')] = 'categories';
					$accessvievving[JText::_('COM_ACCESSMANAGER_MODULE_ACCESS')] = 'modules';
					$accessvievving[JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS')] = 'components';
					$accessvievving[JText::_('COM_ACCESSMANAGER_MENU_ACCESS')] = 'menuaccess';
					$accessvievving[JText::_('COM_ACCESSMANAGER_PART_ACCESS')] = 'parts';
					$accessvievving[JText::_('COM_ACCESSMANAGER_ADMINMENUMANAGER_ACCESS')] = 'adminmenumanager';							
					$accessedit = array();	
					$accessedit[JText::_('COM_ACCESSMANAGER_MODULE_ACCESS')] = 'modulesbackend';					
					$accessedit[JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS')] = 'componentsbackend';	
					$accessedit[JText::_('COM_ACCESSMANAGER_MENUITEM_ACCESS')] = 'menuitemsbackend';					
					$accessedit[JText::_('COM_ACCESSMANAGER_PLUGIN_ACCESS')] = 'pluginsbackend';									
					$new = '<li>';
					$new .= '<a';
					if($this->view=='panel'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=panel">'.JText::_('COM_ACCESSMANAGER_CPANEL').'</a>';
					$new .= '</li>';
					$new .= '<li>';
					$new .= '<a';
					if($this->view=='configuration'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=configuration">'.JText::_('COM_ACCESSMANAGER_CONFIG').'</a>';
					$new .= '</li>';
					$new .= '<li>';
					$new .= '<a';
					if($this->view=='users'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=users">'.JText::_('COM_ACCESSMANAGER_USERS').'</a>';
					$new .= '</li>';
					$new .= '<li>';
					$new .= '<a';
					if(in_array($this->view, $accessvievving) || $this->view=='accessvievving'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=accessvievving">'.JText::_('COM_ACCESSMANAGER_ACCESS_VIEWING').'</a>';
					$new .= '<ul>';
					for($n = 0; $n < count($accessvievving); $n++){
						$row = each($accessvievving);
						$new .= '<li';
						if($this->view==$row['value']){
							$new .= ' class="on"';
						}
						$new .= '>';
						$new .= '<a';
						if($this->view==$row['value']){
							$new .= ' class="active"';
						}
						$new .= ' style="background-image: url(components/com_accessmanager/images/menu/accessmanager_'.$row['value'].'.png);"';
						$new .= ' href="index.php?option=com_accessmanager&view='.$row['value'].'">'.$row['key'].'</a>';
						$new .= '</li>';
					}
					$new .= '</ul>';
					$new .= '</li>';
					$new .= '<li>';
					$new .= '<a';
					if(in_array($this->view, $accessedit) || $this->view=='accessedit'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=accessedit">'.JText::_('COM_ACCESSMANAGER_ACCESS_EDITTING').'</a>';
					$new .= '<ul>';
					for($n = 0; $n < count($accessedit); $n++){
						$row = each($accessedit);
						$new .= '<li';
						if($this->view==$row['value']){
							$new .= ' class="on"';
						}
						$new .= '>';
						$new .= '<a';
						if($this->view==$row['value']){
							$new .= ' class="active"';
						}
						$new .= ' style="background-image: url(components/com_accessmanager/images/menu/accessmanager_'.$row['value'].'.png);"';
						$new .= ' href="index.php?option=com_accessmanager&view='.$row['value'].'">'.$row['key'].'</a>';
						$new .= '</li>';
					}
					$new .= '</ul>';
					$new .= '</li>';
					$new .= '<li>';
					$new .= '<a';
					if($this->view=='tools'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=tools">'.JText::_('COM_ACCESSMANAGER_TOOLS').'</a>';
					$new .= '</li>';
					$new .= '<li>';
					$new .= '<a';
					if($this->view=='info'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=info">'.JText::_('COM_ACCESSMANAGER_INFO').'</a>';
					$new .= '</li>';				
			
					$buffer = str_replace($ori, $new, $buffer);
				}
			}			
			//end dropdown menu access manager
			
			if($this->am_config['am_enabled']){
				if($this->option=='com_content' && $this->view=='article' && $this->am_config['article_active']){				
					$buffer = $this->display_multigrouplevel_select($buffer, 'article');
				}
				
				if($this->option=='com_categories' && $this->view=='category' && $this->am_config['category_active']){				
					$buffer = $this->display_multigrouplevel_select($buffer, 'category');				
				}
				
				if((($this->option=='com_modules' && $this->view=='module') || ($this->option=='com_advancedmodules' && $this->view=='module')) && $this->am_config['module_active']){			
					$buffer = $this->display_multigrouplevel_select($buffer, 'module');				
				}
				
				if($this->option=='com_menus' && $this->view=='item' && $this->am_config['menuitem_active']){							
					$buffer = $this->display_multigrouplevel_select($buffer, 'menuitem');				
				}
				
				if($this->option=='com_adminmenumanager' && $this->view=='menuitem' && $this->am_config['adminmenumanager_active']){											
					$buffer = $this->display_multigrouplevel_select($buffer, 'adminmenumanager');				
				}
			}
		}	
		//write buffer
		JResponse::setBody($buffer);	 
	}
	
	function display_multigrouplevel_select($buffer, $type){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			$id = JRequest::getVar('id', '');
		}else{
			$id = JRequest::getVar('a_id', '');//articles from the frontend
		}	
			
		//get grouplevels with rights for item (reverse or not)
		$groups_levels = array();
		if($id){			
			$based_on = $this->am_config['based_on'];		
			$database->setQuery("SELECT `".$based_on."` "
			." FROM #__accessmanager_rights "
			." WHERE item='$id' "		
			." AND type='$type' "
			);		
			$groups_levels = $database->loadResultArray();
		}else{
			//new item so get default access for that type
			$groups_levels = $this->am_config[$type.'_default_access'];
		}
		
		//get groupslevels
		if($this->am_config['based_on']=='level'){	
			$level_sort = $this->am_config['level_sort'];		
			$database->setQuery("SELECT id, title "
			."FROM #__viewlevels "		
			."ORDER BY $level_sort "
			);	
		}else{						
			$database->setQuery("SELECT a.id as id, a.title as title, COUNT(DISTINCT b.id) AS hyrarchy "
			."FROM #__usergroups AS a "
			."LEFT JOIN #__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt "
			."WHERE a.id<>'8' "			
			."GROUP BY a.id "		
			."ORDER BY a.lft ASC "		
			);	
		}	
		$accesslevels = $database->loadObjectList();
		
		//make select
		$height = $this->am_config['height_multiselect'];
		if($height=='all'){	
			$height = count($accesslevels);
		}
		$new_select = '<select name="am_levelsgroups_access[]" multiple="multiple" size="'.$height.'">';
		foreach($accesslevels as $accesslevel){
			$new_select .= '<option value="'.$accesslevel->id.'"';
			if(in_array($accesslevel->id, $groups_levels)){
				$new_select .= ' selected="selected"';
			}
			$new_select .= '>';
			if($this->am_config['based_on']=='group'){	
				$new_select .= str_repeat('- ',$accesslevel->hyrarchy);	
			}						
			$new_select .= $accesslevel->title;						
			$new_select .= '</option>';
		}
		$new_select .= '</select>';	
		
		//get it all in there
		$regex = "/id=\"jform_access-lbl\"(.*?)id=\"jform_access\"/is";	
		if($type=='adminmenumanager'){
			$regex = "/td colspan=\"2\"(.*?)name=\"access\"/is";	
		}		
		preg_match_all($regex, $buffer, $matches);			
		if(isset($matches[0][0])){
			$old = $matches[0][0];			
			$new = str_replace(' id="jform_access"',' style="display: none;" id="jform_access"', $old);
			if($type=='adminmenumanager'){
				$new = str_replace(' name="access"',' style="display: none;" name="access"', $old);
			}
			if($this->am_config[$type.'_reverse_access']){
				$lang = JFactory::getLanguage();
				$lang->load('com_accessmanager', JPATH_ADMINISTRATOR, null, false);
				if($this->am_config['based_on']=='group'){
					$message = 'COM_ACCESSMANAGER_REVERSE_MESSAGE_GROUPS';
				}else{
					$message = 'COM_ACCESSMANAGER_REVERSE_MESSAGE_LEVELS';
				}				
				//$new = str_replace('</label>','</label><input type="text" name="bogus" value="'.JText::_($message).'" class="readonly" size="50" readonly="readonly"/><label>&nbsp;</label>', $new);
				if($app->isAdmin()){
					$new = str_replace('</label>','</label><span style="float: left; width: auto; margin: 5px 5px 5px 0;">'.JText::_($message).'</span><label>&nbsp;</label>', $new);
				}else{
					$new = str_replace('</label>','</label><span style="display: inline-block; padding-left: 2px;">'.JText::_($message).'</span></div><div class="formelm"><label>&nbsp;</label>', $new);
				}
			}			
			$new = str_replace('<select', $new_select.'<select', $new);			
			$buffer = str_replace($old, $new, $buffer);
		}
		
		return $buffer;
	}	
	
	function check_module_access_backend(){
	
		$app = JFactory::getApplication();	
		
		if($this->am_config['am_enabled']
			&& $this->am_config['modulebackend_active'] 
			&& !$this->is_super_user 
			&& (($this->option=='com_modules' && $this->view=='module') || ($this->option=='com_advancedmodules' && $this->view=='module'))
			){	
			$id = intval(JRequest::getVar('id', ''));				
			if(!$this->check_access_backend($id, 'modulebackend', $this->am_config['modulebackend_multigroup_access_requirement'])){									
				$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component&type=module', false);										
				$app->redirect($url);
			}
		}		
	}
	
	function check_component_access_backend(){
	
		$app = JFactory::getApplication();	
		
		if($this->am_config['am_enabled']
			&& $this->am_config['componentbackend_active'] 
			&& !$this->is_super_user 
			&& $this->option!='com_login' 
			&& $this->option!='com_cpanel' 
			&& !($this->option=='com_accessmanager' && $this->view=='noaccess')
			){	
			if(!$this->check_access_backend($this->option, 'componentbackend', $this->am_config['componentbackend_multigroup_access_requirement'])){									
				$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component&type=component', false);										
				$app->redirect($url);
			}
		}		
	}
	
	function check_menuitem_access_backend(){
	
		$app = JFactory::getApplication();			
		$id = intval(JRequest::getVar('id', ''));
		
		if($this->am_config['am_enabled']
			&& $this->am_config['menuitembackend_active'] 
			&& !$this->is_super_user 
			&& $this->option=='com_menus' && $this->view=='item' && $id!=''
			){				
			if(!$this->check_access_backend($id, 'menuitembackend', $this->am_config['menuitembackend_multigroup_access_requirement'])){									
				$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component&type=menuitem', false);										
				$app->redirect($url);
			}
		}		
	}
	
	function check_plugin_access_backend(){
	
		$app = JFactory::getApplication();	
		
		if($this->am_config['am_enabled']
			&& $this->am_config['pluginbackend_active'] 
			&& !$this->is_super_user 
			&& $this->option=='com_plugins'	&& $this->view=='plugin'	
			){	
			$id = intval(JRequest::getVar('extension_id', ''));				
			if(!$this->check_access_backend($id, 'pluginbackend', $this->am_config['pluginbackend_multigroup_access_requirement'])){									
				$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component&type=plugin', false);										
				$app->redirect($url);
			}
		}		
	}
	
	function check_access_backend($item, $type, $multigroups){
	
		$database = JFactory::getDBO();	
		
		$groups = $this->access_script->get_user_grouplevels('group');			
		$database->setQuery("SELECT `group`, access "
		." FROM #__accessmanager_rights "		
		." WHERE item='$item' "		
		." AND type='$type' "
		);
		$rights_rows = $database->loadObjectList();		
		$rights = array();
		foreach($rights_rows as $right){					
			$rights[] = $item.'__'.$right->group.'__'.$right->access;
		}
		$access_array = array();
		foreach($groups as $group_row){			
			$temp = '';
			foreach($rights_rows as $right_row){
				if($right_row->group==$group_row){
					$temp = $right_row->access;
					break;
				}				
			}
			if($temp==''){
				//get inherited access
				$groups_backend = $this->get_all_grouplevels('backend');				
				$this->inherited_right = '';
				$this->get_inherited_right_backend($item, $group_row, $rights, $groups_backend, $type);
				$temp = $this->inherited_right;
			}
			$access_array[] = $temp;
		}							
		if($multigroups=='every_group'){
			if(in_array('0', $access_array)){
				$access = false;
			}else{
				$access = true;
			}
		}else{
			if(in_array('1', $access_array)){
				$access = true;
			}else{
				$access = false;
			}				
		}		
		return $access;
	}
	
	function get_inherited_right_backend($item, $group, $rights, $groups, $type){		
		
		//if parent is public, set to default
		if($type=='modulebackend' && $group=='1'){
			$this->inherited_right = $this->am_config['modulebackend_default'];			
			return;
		}
		if($type=='componentbackend' && $group=='1'){
			$this->inherited_right = $this->am_config['componentbackend_default'];			
			return;
		}
		if($type=='menuitembackend' && $group=='1'){
			$this->inherited_right = $this->am_config['menuitembackend_default'];			
			return;
		}
		if($type=='pluginbackend' && $group=='1'){
			$this->inherited_right = $this->am_config['pluginbackend_default'];			
			return;
		}
		
		//get parent group
		$parent = 'no';
		foreach($groups as $row){	
			if($row->id==$group){
				$parent = $row->parent_id;
				break;
			}
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
			$this->get_inherited_right_backend($item, $parent, $rights, $groups, $type);
		}else{					
			$this->inherited_right = $access;
		}		
	}
	
	function get_all_grouplevels($backend=0){
		
		$database = JFactory::getDBO();
		
		$where = '';
		if($this->am_config['based_on']=='group' || $backend){
			$where .= "WHERE id<>'8' ";
		}
		if($this->am_config['based_on']=='level' && !$backend){			
			$database->setQuery("SELECT id, title "
			."FROM #__viewlevels "
			.$where
			."ORDER BY title "
			);	
		}else{
			$database->setQuery("SELECT id, title, parent_id "
			."FROM #__usergroups "
			.$where			
			."ORDER BY title "
			);	
		}
		$grouplevels = $database->loadObjectList();		
		return $grouplevels;
	}
	
	function check_menu_access(){		
		
		$app = JFactory::getApplication();
		$menu_id = JRequest::getVar('Itemid', '');		

		if($menu_id && $this->am_config['menuitem_active'] && !$this->is_super_user){			
			if(!$this->access_script->check_access($menu_id, 'menuitem', $this->am_config['menuitem_multigroup_access_requirement'], $this->am_config['menuitem_reverse_access'])){								
				if($this->am_config['menuaccess_message_type']=='alert'){					
					$this->do_alert();							
				}elseif($this->am_config['menuaccess_message_type']=='only_text'){							
					$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component', false);										
					$app->redirect($url);		
				}elseif($this->am_config['menuaccess_message_type']=='redirect'){						
					$url = JURI::root().$this->am_config['no_menu_access_url'];								
					$app->redirect($url);		
				}elseif($this->am_config['menuaccess_message_type']=='login'){																		
					$url = $this->login_url;								
					$app->redirect($url);	
				}
			}		
		}		
	}	
	
	function check_article_view_access(){
		
		$app = JFactory::getApplication();			
		
		//get vars			
		$item_id_temp = JRequest::getVar('id', '');	
		if(strpos($item_id_temp, ':')){
			$pos_item_id = strpos($item_id_temp, ':');
			$item_id = intval(substr($item_id_temp, 0, $pos_item_id));	
		}else{
			$item_id = intval($item_id_temp);	
		}					
			
		//start checking item full view access		
		if($this->option=='com_content' &&
		($this->view=='article' && ($this->layout=='default' || $this->layout=='')) &&
		($this->am_config['article_active'] || $this->am_config['category_active']) &&
		(!$this->is_super_user) 
		){	
		
			//if no access
			if(!$this->access_script->check_article_access($item_id)){								
				if($this->am_config['items_message_type']=='alert'){
					//javascript alert	
					$this->do_alert();
				}elseif($this->am_config['items_message_type']=='redirect'){
					//redirect
					$url = JURI::root().$this->am_config['no_item_access_full_url'];																		
					$app->redirect($url);	
				}elseif($this->am_config['items_message_type']=='login'){						
					$url = $this->login_url;
					$app->redirect($url);	
				}else{								
					//white page with message													
					$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component', false);										
					$app->redirect($url);									
				}				
			}				
							
				
		}//end if anything needs checking
			
	}
	
	function do_alert(){	
		$lang = JFactory::getLanguage();
		$lang->load('com_accessmanager', JPATH_ROOT, null, false);	
		$message = addslashes(JText::_('COM_ACCESSMANAGER_NO_ACCESS_PAGE'));			
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo "<script>alert('".html_entity_decode($message)."'); window.history.go(-1); </script>";
		exit('<html><body><noscript>'.$message.'</noscript></body></html>');
	}	
	
	function check_component_access_frontend(){	
			
		$app = JFactory::getApplication();	
			
		if($this->am_config['am_enabled']
			&& $this->am_config['component_active'] 
			&& !$this->is_super_user				
			&& !($this->option=='com_accessmanager' && $this->view=='noaccess')
			){						
			if(!$this->access_script->check_access($this->option, 'component', $this->am_config['component_multigroup_access_requirement'], $this->am_config['component_reverse_access'])){								
				if($this->am_config['components_message_type']=='alert'){					
					$this->do_alert();	
				}elseif($this->am_config['components_message_type']=='redirect'){							
					$url = JURI::root().$this->am_config['no_component_access_url'];
					$app->redirect($url);
				}elseif($this->am_config['components_message_type']=='login'){							
					$url = $this->login_url;
					$app->redirect($url);
				}else{					
					$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component', false);						
					$app->redirect($url);						
				}
			}				
		}		
	}		
			
	function onAfterRoute(){
	
		static $on_after_route;		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option', '');
		$view = JRequest::getVar('view', '');
		
		if($this->fua_enabled || !$this->am_config['am_enabled']){
			return true;
		}
		
		if(!$on_after_route){			
			
			if(!$app->isAdmin() && 
				!$this->is_super_user &&
				($this->am_config['article_active'] || $this->am_config['category_active'] || $this->am_config['component_active'] || $this->am_config['menuitem_active'] || $this->am_config['adminmenumanager_active'])
			){				
				
				$declare_array = array();
				
				
				if($this->am_config['article_active'] || $this->am_config['category_active']){	
					
					//model articles
					$file = 'components'.DS.'com_content'.DS.'models'.DS.'articles.php';
					$code_replace = array();	
					//get cat-access out (when it gets integrated in the core)
					//http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=8103&tracker_item_id=27819		
					$code_old = '$query->where(\'c.access IN (\'.$groups.\')\');';																		
					$code_new = '';									
					$code_replace[] = array($code_old, $code_new);								
					//where by articles and categories
					$code_old = '$query->where(\'a.access IN (\'.$groups.\')\');';																			
					$code_new = '$query->where(\' '.$this->access_script->where_articles_categories('a', 'c', 'a.access IN (\'.$groups.\')', 'c.access IN (\'.$groups.\')').' \');';									
					$code_replace[] = array($code_old, $code_new);						
					
					//no secondairy filtering
					$code_old = 'if ($access) {';																		
					$code_new = 'if(1==1){';									
					$code_replace[] = array($code_old, $code_new);					
					$declare_array[] = array($file, $code_replace);							
					
					
					//model featured 
					if($option=='com_content' && $view=='featured'){				
						$file = 'components'.DS.'com_content'.DS.'models'.DS.'featured.php';
						$code_replace = array();		
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';						
						$code_new = '';					
						$code_replace[] = array($code_old, $code_new);	
						//extra for joomla 1.6
						$code_old = 'require_once dirname(__FILE__) . DS . \'articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);						
						//and they changed it again in joomla 2.5.4
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);	
						$declare_array[] = array($file, $code_replace);
					}
					
					//model archive 
					if($option=='com_content' && $view=='archive'){
						$file = 'components'.DS.'com_content'.DS.'models'.DS.'archive.php';
						$code_replace = array();		
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';						
						$code_new = '';					
						$code_replace[] = array($code_old, $code_new);	
						//extra for joomla 1.6
						$code_old = 'require_once dirname(__FILE__) . DS . \'articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);
						//and they changed it again in joomla 2.5.4
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);	
						$declare_array[] = array($file, $code_replace);	
					}
				}//end if article and category access active
				
				//categories
				if($option=='com_content' && ($view=='categories' || $view=='category') && $this->am_config['category_active']){
					$file = 'libraries'.DS.'joomla'.DS.'application'.DS.'categories.php';				
					$code_replace = array();		
					$code_old = '$query->where(\'c.access IN (\'.implode(\',\', $user->getAuthorisedViewLevels()).\')\');';								
					$code_new = 'if($extension==\'com_content\'){$query->where(" c.id '.$this->access_script->where_categories().' ");}';						
					$code_replace[] = array($code_old, $code_new); 	
					$code_old = 'if (!is_null($this->_parent)) {';								
					$code_new = 'if(is_null($parent)){
						$parent = new JCategoryNode();
						}'.$code_old;						
					$code_replace[] = array($code_old, $code_new); 						
					$declare_array[] = array($file, $code_replace);				
				}
				
				//search
				if($option=='com_search'){										
					$file = 'components'.DS.'com_search'.DS.'models'.DS.'search.php';
					$code_replace = array();
					//filter components and menuitems						
					$code_old = '$this->_total	= count($rows);';				
					$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();				
					$rows = $accessmanagerAccessChecker->filter_search_results($rows);'.$code_old;						
					$code_replace[] = array($code_old, $code_new);			
					$declare_array[] = array($file, $code_replace);			
				}			
				
				if($option=='com_finder'){
					$file = 'components'.DS.'com_finder'.DS.'models'.DS.'search.php';
					$code_replace = array();
					//rip access filter out of query
					$code_old = '$query->where($db->quoteName(\'l.access\') . \' IN (\' . $groups . \')\');';				
					$code_new = '';					
					$code_replace[] = array($code_old, $code_new);	
					//add filter
					$code_old = '// Switch to a non-associative array.';				
					$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();				
								$results = $accessmanagerAccessChecker->filter_search_results_finder($results);';
					$code_replace[] = array($code_old, $code_new);
					//add var to count results
					$code_old = 'protected $requiredTerms = array();';				
					$code_new = $code_old.'public $number_of_results;';
					$code_replace[] = array($code_old, $code_new);
					//parse number of results 
					$code_old = '$this->store($store, $results);';				
					$code_new = '$this->number_of_results = count($results);'.$code_old;
					$code_replace[] = array($code_old, $code_new);
					//return number of results
					$code_old = '$store = $this->getStoreId(\'getTotal\');';				
					$code_new = 'return $this->number_of_results;'.$code_old;
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);
				}
							
				
				$this->declare_methods($declare_array);
			}
			
			if($app->isAdmin()){
				//backend
				
				$declare_array = array();
				
				if(!$this->is_super_user){
				
					//modulebackend
					if($this->am_config['modulebackend_active'] && 
						($option=='com_modules' || $option=='com_advancedmodules') && 
						($view=='' || $view=='modules')){					
						$file = 'administrator'.DS.'components'.DS.'com_modules'.DS.'models'.DS.'modules.php';
						$code_old = 'return $query;';				
						$code_new = '$query->where(\'a.id '.$this->where_modules_backend().'\');'.$code_old;
						$code_replace[] = array($code_old, $code_new);
						$declare_array[] = array($file, $code_replace);
						$file = 'administrator'.DS.'components'.DS.'com_advancedmodules'.DS.'models'.DS.'modules.php';					
						$declare_array[] = array($file, $code_replace);
					}
					
					//menuitembackend
					if($this->am_config['menuitembackend_active'] && $option=='com_menus' && $view=='items'){					
						$file = 'administrator'.DS.'components'.DS.'com_menus'.DS.'models'.DS.'items.php';
						$code_old = 'return $query;';				
						$code_new = '$query->where(\'a.id '.$this->where_menuitem_backend().'\');'.$code_old;
						$code_replace[] = array($code_old, $code_new);
						$declare_array[] = array($file, $code_replace);					
					}
					
					//pluginbackend
					if($this->am_config['pluginbackend_active'] && $option=='com_plugins' && ($view=='plugins' || $view=='')){					
						$file = 'administrator'.DS.'components'.DS.'com_plugins'.DS.'models'.DS.'plugins.php';
						$code_old = 'return $query;';				
						$code_new = '$query->where(\'a.extension_id '.$this->where_plugin_backend().'\');'.$code_old;
						$code_replace[] = array($code_old, $code_new);
						$declare_array[] = array($file, $code_replace);					
					}
				
				}
				
				//menuitem save
				if(($this->am_config['menuitem_active'] || $this->am_config['menuitembackend_active']) && $option=='com_menus'){									
					$file = 'administrator'.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php';
					$code_old = '$this->setState(\'item.id\', $table->id);';					
					$code_new = $code_old;
					if($this->am_config['menuitem_active']){
						$code_new .= 'plgSystemAccessmanager::save_rights($table->id, \'menuitem\', \'group\');';						
					}
					if($this->am_config['menuitembackend_active'] && $this->am_config['menuitembackend_author_access']){						
						$code_new .= 'if($isNew){
										$accessmanagerHelper = new accessmanagerHelper();
										$accessmanagerHelper->give_authors_group_access($table->id, \'menuitembackend\');
										}';
					}
					$code_replace[] = array($code_old, $code_new);
					$code_old = 'JPATH_COMPONENT';
					$code_new = 'JPATH_ROOT.DS.\'administrator\'.DS.\'components\'.DS.\'com_menus\'';
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);					
				}				
				
				//adminmenumanager menuitem save				
				if($this->am_config['adminmenumanager_active'] && $option=='com_adminmenumanager'){
					
					//if menuitem was saved at previous pageload					
					if($app->getUserState("com_accessmanager.new_adminmenuitem_was_saved", '')=='1'){					
					
						//get the previous known menuitems
						$query = $db->getQuery(true);
						$query->select('config');
						$query->from('#__accessmanager_config');
						$query->where('id='.$db->q('temp_index'));					
						$rows = $db->setQuery($query);				
						$rows = $db->loadObjectList();	
						$temp_index = array();						
						foreach($rows as $row){		
							$temp_index = explode(',',$row->config);	
						}						
				
						//get the previous selected rights
						$query = $db->getQuery(true);
						$query->select('config');
						$query->from('#__accessmanager_config');
						$query->where('id='.$db->q('temp_rights'));					
						$rows = $db->setQuery($query);				
						$rows = $db->loadObjectList();	
						$temp_rights = array();						
						foreach($rows as $row){		
							$temp_rights = explode(',',$row->config);	
						}
					
						//check which menuitem is the new one
						$query = $db->getQuery(true);
						$query->select('id');
						$query->from('#__adminmenumanager_menuitems');						
						$rows = $db->setQuery($query);				
						$rows = $db->loadObjectList();							
						foreach($rows as $row){		
							if(!in_array($row->id, $temp_index)){
								//set rights
								$this->save_rights($row->id, 'adminmenumanager', $this->am_config['based_on'], $temp_rights);
							}
						}	
						
						//clean temp table rows
						$query = $db->getQuery(true);		
						$query->update('#__accessmanager_config');
						$query->set('config='.$db->q(''));								
						$query->where('id='.$db->q('temp_index'));
						$db->setQuery((string)$query);
						$db->query();
						$query = $db->getQuery(true);		
						$query->update('#__accessmanager_config');
						$query->set('config='.$db->q(''));								
						$query->where('id='.$db->q('temp_rights'));
						$db->setQuery((string)$query);
						$db->query();					
										
						//reset session
						$app->setUserState("com_accessmanager.new_adminmenuitem_was_saved", '');
					}
										
					//when a adminmenumanager menuitem is saved
					if(JRequest::getVar('task', null, 'default', 'cmd')=='menuitem_save' || 
					JRequest::getVar('task', null, 'default', 'cmd')=='menuitem_save_as_copy'){
					
						$item_rights_array = JRequest::getVar('am_levelsgroups_access', array(), 'post', 'array');
						
						if(!JRequest::getVar('menuitem_id', 0, '', 'int') || JRequest::getVar('task', null, 'default', 'cmd')=='menuitem_save_as_copy'){
							//new menuitem save
										
							//make index of current menuitems
							$query = $db->getQuery(true);
							$query->select('id');
							$query->from('#__adminmenumanager_menuitems');						
							$rows = $db->setQuery($query);				
							$rows = $db->loadResultArray();							
							$items_id_string = implode(',', $rows);
							
							//save index to temp table
							$query = $db->getQuery(true);		
							$query->update('#__accessmanager_config');
							$query->set('config='.$db->q($items_id_string));								
							$query->where('id='.$db->q('temp_index'));
							$db->setQuery((string)$query);
							$db->query();
							
							//save rights to temp table									
							$item_rights_string = implode(',', $item_rights_array);
							$query = $db->getQuery(true);		
							$query->update('#__accessmanager_config');
							$query->set('config='.$db->q($item_rights_string));								
							$query->where('id='.$db->q('temp_rights'));
							$db->setQuery((string)$query);
							$db->query();
							
							//set session
							$app->setUserState("com_accessmanager.new_adminmenuitem_was_saved", '1');
						}else{
							//edit menuitem save
							
							$this->save_rights(JRequest::getVar('menuitem_id', 0, '', 'int'), 'adminmenumanager', $this->am_config['based_on'], $item_rights_array);
							
						}						
					}
				}//end if adminmenumanager menuitem save
				
				
				$this->declare_methods($declare_array);
				
			}	
			
			$on_after_route = 1;
		}	
	}
	
	function where_modules_backend(){		
		$database = JFactory::getDBO();		
		//not for super-admins and only when valid trial
		if($this->is_super_user || !$this->trial_valid){
			return ' NOT IN (0)';
		}		
		$rights = $this->helper->get_access_rights_backend('modulebackend');
		$database->setQuery("SELECT id FROM #__modules ");
		$modules = $database->loadResultArray();							
		return $this->get_where_backend('modulebackend', $rights, $modules);
	}	
	
	function where_menuitem_backend(){		
		$database = JFactory::getDBO();		
		//not for super-admins and only when valid trial
		if($this->is_super_user || !$this->trial_valid){
			return ' NOT IN (0)';
		}		
		$rights = $this->helper->get_access_rights_backend('menuitembackend');
		$database->setQuery("SELECT id FROM #__menu ");
		$items = $database->loadResultArray();
		return $this->get_where_backend('menuitembackend', $rights, $items);
	}
	
	function where_plugin_backend(){		
		$database = JFactory::getDBO();		
		//not for super-admins and only when valid trial
		if($this->is_super_user || !$this->trial_valid){
			return ' NOT IN (0)';
		}		
		$rights = $this->helper->get_access_rights_backend('pluginbackend');
		$database->setQuery("SELECT extension_id FROM #__extensions WHERE type='plugin' ");
		$items = $database->loadResultArray();
		return $this->get_where_backend('pluginbackend', $rights, $items);
	}	
	
	function get_where_backend($type, $rights, $items){
		$groups = $this->access_script->get_user_grouplevels('group');		
				
		$rights_array = array();
		foreach($rights as $right){
			$temp = explode('__', $right);			
			$rights_array[] = array($temp[0], $temp[1], $temp[2]);//item group access		
		}	
		
		$items_no_access = array();
		foreach($items as $item){
			$access_array = array();
			foreach($groups as $group_row){			
				$temp = '';
				foreach($rights_array as $right){
					if($right[0]==$item && $right[1]==$group_row){
						$temp = $right[2];
						break;
					}				
				}
				if($temp==''){
					//get inherited access
					$groups_backend = $this->get_all_grouplevels('backend');				
					$this->inherited_right = '';
					$this->get_inherited_right_backend($item, $group_row, $rights, $groups_backend, $type);
					$temp = $this->inherited_right;
				}
				$access_array[] = $temp;
			}							
			if($this->am_config[$type.'_multigroup_access_requirement']=='every_group'){
				if(in_array('0', $access_array)){
					$access = false;
				}else{
					$access = true;
				}
			}else{
				if(in_array('1', $access_array)){
					$access = true;
				}else{
					$access = false;
				}				
			}
			if(!$access){				
				$items_no_access[] = $item;
			}
		}				
		$where = ' NOT IN (0';						
		for($n = 0; $n < count($items_no_access); $n++){
			$where .= ','.$items_no_access[$n];
		}
		$where .= ') ';				
		return $where;
	}
   
	function onAfterInitialise(){		
		
		if($this->fua_enabled){
			return true;
		}
  
   		static $onAfterInitialise;			
		
		if(!$onAfterInitialise){
		
			$app = JFactory::getApplication();
			$database = JFactory::getDBO();			
			
			//if($app->isAdmin() || !$this->am_config['am_enabled'] || $this->is_super_user){
			if(!$this->am_config['am_enabled']){
				return true;
			}				
			
			$declare_array = array();			
			
			//menuitems
			if($this->am_config['menuitem_active'] && !$app->isAdmin() && !$this->is_super_user){
				$file = 'libraries'.DS.'joomla'.DS.'application'.DS.'menu.php';				
				$code_replace = array();
				//take out Joomla access filter part 1
				$code_old = 'return in_array((int) $menu->access, $user->getAuthorisedViewLevels());';								
				$code_new = 'return true;';							
				$code_replace[] = array($code_old, $code_new);	
				//take out Joomla access filter part 2
				$code_old = '$values = (array) $values;';								
				$code_new = $code_old.'for($n = 0; $n < count($attributes); $n++){
									if($attributes[$n]==\'access\'){
									unset($attributes[$n]);
									unset($values[$n]);
									break;
									}
									}';							
				$code_replace[] = array($code_old, $code_new);				
				//filter menuitems via access manager
				$code_old = 'return $items;';								
				$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();
							$items = $accessmanagerAccessChecker->filter_menu_items($items);'.$code_old;
				$code_replace[] = array($code_old, $code_new);					
				$declare_array[] = array($file, $code_replace);
			}
			
			if(!class_exists('JModuleHelper')){
				
				
				//modules	
				//check if we need to override the advanced module manager or MetaMod
				$am_order = 0;
				if(file_exists(JPATH_PLUGINS.DS.'system'.DS.'advancedmodules'.DS.'advancedmodules.php') || file_exists(JPATH_PLUGINS.DS.'system'.DS.'metamod'.DS.'metamod.php')){
				
					
					//check which order the AM system plugin has
					$database->setQuery("SELECT ordering "
					." FROM #__extensions "
					." WHERE element='accessmanager' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$am_order = $row->ordering;
					}
				}				
				
				
				//advanced module manager
				$advanced_module_manager_published = 0;
				$advanced_module_manager_order = 0;	
				if(file_exists(JPATH_PLUGINS.DS.'system'.DS.'advancedmodules'.DS.'advancedmodules.php')){
					//check if enabled and which order
					$database->setQuery("SELECT enabled, ordering "
					." FROM #__extensions "
					." WHERE element='advancedmodules' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$advanced_module_manager_published = $row->enabled;
						$advanced_module_manager_order = $row->ordering;
					}				
				}
				
				
				
				//MetaMod
				//seems not to be for 1.7 leave in for a while to make sure it does not surprise me when making a comeback
				$metamod_published = 0;
				$metamod_order = 0;	
				if(file_exists(JPATH_PLUGINS.DS.'system'.DS.'metamod'.DS.'metamod.php')){
					//check if enabled and which order
					$database->setQuery("SELECT enabled, ordering "
					." FROM #__extensions "
					." WHERE element='metamod' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$metamod_published = $row->enabled;
						$metamod_order = $row->ordering;
					}				
				}
				
				//jat3				
				$jat3_enabled = 0;				
				if(file_exists(JPATH_PLUGINS.DS.'system'.DS.'jat3'.DS.'jat3.php')){				
					//check if enabled and which order
					$database->setQuery("SELECT enabled "
					." FROM #__extensions "
					." WHERE element='jat3' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$jat3_enabled = $row->enabled;						
					}				
				}				
				
				$got_module_helper = 0;
				if($jat3_enabled){	
					//if metamod is enabled AND FUA is loaded first in order
					//load the metamod helper and alter it
					$file = 'plugins'.DS.'system'.DS.'jat3'.DS.'jat3'.DS.'core'.DS.'joomla'.DS.'modulehelper.php';
					$got_module_helper = 1;
				}
				if(!$got_module_helper && $advanced_module_manager_published && ($am_order < $advanced_module_manager_order)){	
					//if advanced_module_manager is enabled AND AM is loaded first in order
					//load the advanced module managers module helper and alter it
					
					$file = 'plugins'.DS.'system'.DS.'advancedmodules'.DS.'modulehelper.php';
					$got_module_helper = 1;
				}
				if(!$got_module_helper && $metamod_published && ($am_order < $metamod_order)){	
					//if metamod is enabled AND AM is loaded first in order
					//load the metamod helper and alter it
					$file = 'plugins'.DS.'system'.DS.'metamod'.DS.'modulehelper.php';
					$got_module_helper = 1;
				}
				if(!$got_module_helper){
					$file = 'libraries'.DS.'joomla'.DS.'application'.DS.'module'.DS.'helper.php';
				}
				
				$code_replace = array();
				
				
				//if($this->am_config['module_active'] && !$this->is_super_user){
				if($this->am_config['module_active']){
					if($jat3_enabled){					
						$code_old = '$query->where[] = \'m.access IN (\'.$groups.\')\';';
						$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();
										$where_in_modules = $accessmanagerAccessChecker->where_modules();
										if($where_in_modules!=\'\'){
											$query->where[] = \'m.id \'.$where_in_modules.\' \';
										}';
						$code_replace[] = array($code_old, $code_new);
					}else{										
						$code_old = '$query->where(\'m.access IN (\'.$groups.\')\');';					
						$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();
										$where_in_modules = $accessmanagerAccessChecker->where_modules();
										if($where_in_modules!=\'\'){
											$query->where(" m.id $where_in_modules ");
										}';									
						$code_replace[] = array($code_old, $code_new);
						//spaces added for joomla 2.5
						$code_old = '$query->where(\'m.access IN (\' . $groups . \')\');';
						$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();
										$where_in_modules = $accessmanagerAccessChecker->where_modules();
										if($where_in_modules!=\'\'){
											$query->where(" m.id $where_in_modules ");
										}';									
						$code_replace[] = array($code_old, $code_new);
						//advanced module manager						
						$code_old = '$query->where[] = \'m.access IN (\'.$groups.\')\';';					
						$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();
										$where_in_modules = $accessmanagerAccessChecker->where_modules();
										if($where_in_modules!=\'\'){
											$query->where[] = " m.id $where_in_modules ";
										}';									
						$code_replace[] = array($code_old, $code_new);
					}
				}
				
				$code_old = 'require $path;';
				$end_code = '						
					}else{				
						require $path;
					}';	
				if(!$app->isAdmin()){					
					//frontend				
									
					$code_new = '
					if(strpos($path, \'mod_articles_categories.php\')){
						$list = modArticlesCategoriesHelper::getList($params);
						if (!empty($list)) {
							$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
							$startLevel = reset($list)->getParent()->level;
							require JModuleHelper::getLayoutPath(\'mod_articles_categories\', $params->get(\'layout\', \'default\'));
						}					
					}elseif(strpos($path, \'mod_articles_category.php\')){
						// Prep for Normal or Dynamic Modes
						$mode = $params->get(\'mode\', \'normal\');
						$idbase = null;
						switch($mode)
						{
							case \'dynamic\':
								$option = JRequest::getCmd(\'option\');
								$view = JRequest::getCmd(\'view\');
								if ($option === \'com_content\') {
									switch($view)
									{
										case \'category\':
											$idbase = JRequest::getInt(\'id\');
											break;
										case \'categories\':
											$idbase = JRequest::getInt(\'id\');
											break;
										case \'article\':
											if ($params->get(\'show_on_article_page\', 1)) {
												$idbase = JRequest::getInt(\'catid\');
											}
											break;
									}
								}
								break;
							case \'normal\':
							default:
								$idbase = $params->get(\'catid\');
								break;
						}
						$cacheid = md5(serialize(array ($idbase,$module->module)));
						$cacheparams = new stdClass;
						$cacheparams->cachemode = \'id\';
						$cacheparams->class = \'modArticlesCategoryHelper\';
						$cacheparams->method = \'getList\';
						$cacheparams->methodparams = $params;
						$cacheparams->modeparams = $cacheid;					
						$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
						if (!empty($list)) {
							$grouped = false;
							$article_grouping = $params->get(\'article_grouping\', \'none\');
							$article_grouping_direction = $params->get(\'article_grouping_direction\', \'ksort\');
							$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
							$item_heading = $params->get(\'item_heading\');					
							if ($article_grouping !== \'none\') {
								$grouped = true;
								switch($article_grouping){
									case \'year\':
									case \'month_year\':
										$list = modArticlesCategoryHelper::groupByDate($list, $article_grouping, $article_grouping_direction, $params->get(\'month_year_format\', \'F Y\'));
										break;
									case \'author\':
									case \'category_title\':
										$list = modArticlesCategoryHelper::groupBy($list, $article_grouping, $article_grouping_direction);
										break;
									default:
										break;
								}
							}
							require JModuleHelper::getLayoutPath(\'mod_articles_category\', $params->get(\'layout\', \'default\'));
						}				
					}elseif(strpos($path, \'mod_articles_latest.php\')){
						$list = modArticlesLatestHelper::getList($params);
						$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
						require JModuleHelper::getLayoutPath(\'mod_articles_latest\', $params->get(\'layout\', \'default\'));	
					}elseif(strpos($path, \'mod_related_items.php\')){
						$cacheparams = new stdClass;
						$cacheparams->cachemode = \'safeuri\';
						$cacheparams->class = \'modRelatedItemsHelper\';
						$cacheparams->method = \'getList\';
						$cacheparams->methodparams = $params;
						$cacheparams->modeparams = array(\'id\'=>\'int\',\'Itemid\'=>\'int\');					
						$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);					
						if (!count($list)) {
							return;
						}					
						$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
						$showDate = $params->get(\'showDate\', 0);					
						require JModuleHelper::getLayoutPath(\'mod_related_items\', $params->get(\'layout\', \'default\'));								
					';					
															
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					//same but then for joomla 2.5 ('require' changed to 'include')
					$code_old = 'include $path;';					 
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					$declare_array[] = array($file, $code_replace);				
				
				
				
					//module articles archive
					/*
					this module does not filter for access at all, not on article and categories
					and does not have a query identifier, so AM does no filtering till Joomla does					
					*/				
				
					//module articles categories				
					$file = 'modules'.DS.'mod_articles_categories'.DS.'helper.php';				
					$code_replace = array();	
					$code_old = '$items = $category->getChildren();';	
					$code_new = $code_old.'$accessmanagerAccessChecker = new accessmanagerAccessChecker();				
											$items = $accessmanagerAccessChecker->filter_categories($items);';							
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);					
						
					//module articles category				
					$file = 'modules'.DS.'mod_articles_category'.DS.'helper.php';				
					$code_replace = array();	
					//articles already filtered in articles model
					//module does not filter for categories, so leave that till Joomla fixes this
					$code_old = 'if ($access || in_array($item->access, $authorised)) {';	
					$code_new = 'if (1==1) {';				
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);					
				
					//module articles latest				
					$file = 'modules'.DS.'mod_articles_latest'.DS.'helper.php';				
					$code_replace = array();
					//take out second filter		
					$code_old = 'if ($access || in_array($item->access, $authorised)) {';					
					$code_new = 'if (1==1) {';																	
					$code_replace[] = array($code_old, $code_new); 					
					$declare_array[] = array($file, $code_replace);
				
					/*
					//module articles news	
					//article and category filtering is already done in the article model				
					*/
					
					/*	
					//module articles popular
					//article and category filtering is already done in the article model
					*/	
					
					//module related items		
					$file = 'modules'.DS.'mod_related_items'.DS.'helper.php';				
					$code_replace = array();		
					$code_old = '$query->where(\'a.access IN (\' . $groups . \')\');';					
					$code_new = '$query->where(\' '.$this->access_script->where_articles_categories('a', 'cc', 'a.access IN (\'.$groups.\')', 'cc.access IN (\'.$groups.\')').' \');';											
					$code_replace[] = array($code_old, $code_new); 					
					$declare_array[] = array($file, $code_replace);						
						
				}else{
					//backend					
					
					if(file_exists(JPATH_ROOT.DS.'administrator'.DS.'modules'.DS.'mod_adminmenumanager'.DS.'helper.php') && $this->am_config['adminmenumanager_active']){
						//need to override WITH adminmenumodule
						$code_new = '
						if(strpos($path, \'mod_adminmenumanager.php\') && file_exists(JPATH_ROOT.DS.\'administrator\'.DS.\'components\'.DS.\'com_adminmenumanager\'.DS.\'controller.php\')){													
							//require_once(JPATH_ROOT.DS.\'administrator\'.DS.\'modules\'.DS.\'mod_adminmenumanager\'.DS.\'helper.php\');							
							$adminmenumanagermenuhelper = new ModAdminMenuManagerHelper();
							$amm_menuitems = $adminmenumanagermenuhelper->get_menu_items($params);
							$class_sfx = htmlspecialchars($params->get(\'class_sfx\'));	
							$adminmenumanagerdisable = $params->get(\'adminmenumanagerdisable\');
							require JModuleHelper::getLayoutPath(\'mod_adminmenumanager\', \'default\');
							
						';
					}else{
						//overwrite WITHOUT adminmenumodule override
						$code_new = '
						if(\'pigs\'==\'fly\'){							
						';
					
					}
					
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					//same but then for joomla 2.5 ('require' changed to 'include')
					$code_old = 'include $path;';					 
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					$declare_array[] = array($file, $code_replace);	
					
					
					//module adminmenumanager	
					$file = 'administrator'.DS.'modules'.DS.'mod_adminmenumanager'.DS.'helper.php';		
					if(file_exists(JPATH_ROOT.DS.$file) && $this->am_config['adminmenumanager_active']){								
						$code_replace = array();		
						$code_old = 'if(!($amm_config[\'super_user_sees_all\'] && in_array(8, $groups_array))){
			$query->where($access_column.\' IN (\'.$groups_levels.\')\');
		}';					
						$code_new = '$query->where(\' id '.$this->access_script->where_adminmenumanager().' \');';																
						$code_replace[] = array($code_old, $code_new); 						
						//amm free version
						$code_replace = array();		
						$code_old = '$query->where(\'published=1\');';					
						$code_new = $code_old.'$query->where(\' id '.$this->access_script->where_adminmenumanager().' \');';																
						$code_replace[] = array($code_old, $code_new); 					
						$declare_array[] = array($file, $code_replace);	
					}
					
				}				
				  
			}
			$this->declare_methods($declare_array);
			$onAfterInitialise = 1;
		}		
	}
   
	function declare_methods($declare_array){				
		for($n = 0; $n < count($declare_array); $n++){					
			$file = JPATH_ROOT.DS.$declare_array[$n][0];
			if(file_exists($file)){					
				$handle = fopen($file, 'r');
				$code = fread($handle, filesize($file));
				$code = str_replace('<?php', '', $code);
				$code = str_replace('?>', '', $code);			
				$code_replace = $declare_array[$n][1];
				for($p = 0; $p < count($code_replace); $p++){					
					$code = str_replace($code_replace[$p][0], $code_replace[$p][1], $code);											
				}								
				eval($code);
				//echo $code;
				//echo '<br /><br /><br /><br /><br /><br /><br />';
			}			
		}
	}
	
	//save article rights
	//save category rights
	function onContentAfterSave($context, $article, $isNew){			
		if($context=='com_content.article' || $context=='com_content.form' && $this->am_config['article_active']){	
			//frontend and backend			
			$this->save_rights($article->id, 'article', $this->am_config['based_on']);			
		}
		if($context=='com_categories.category' && $this->am_config['category_active']){				
			$this->save_rights($article->id, 'category', $this->am_config['based_on']);	
		}
	}
	
	//save module rights
	function onExtensionAfterSave($context, $table, $isNew){		
		if($context=='com_modules.module' || $context=='com_advancedmodules.module'){
			if($this->am_config['module_active']){
				$this->save_rights($table->id, 'module', $this->am_config['based_on']);
			}
			if($isNew && $this->am_config['modulebackend_active'] && $this->am_config['modulebackend_author_access']){
				$this->helper->give_authors_group_access($table->id, 'modulebackend');
			}
		}		
	}
	
	function save_rights($item, $type, $based_on, $groupslevels=0){
		$database = JFactory::getDBO();	
		//$based_on = $this->am_config['based_on'];not here because when saving menuitem the menu tries to get the config in its own class context	
		
		//get rights from select if not parsed as var
		if(!$groupslevels){		
			$groupslevels = JRequest::getVar('am_levelsgroups_access', null, 'post', 'array');
		}
		
			
		if($groupslevels){
			//delete all current rights for this item
			$database->setQuery("DELETE FROM #__accessmanager_rights WHERE item='$item' AND type='$type' AND `$based_on`<>0 ");
			$database->query();					
			
			//add rights 		
			foreach($groupslevels as $grouplevel){
				$database->setQuery( "INSERT INTO #__accessmanager_rights SET item='$item', `$based_on`='$grouplevel', type='$type' ");
				$database->query();			
			}
		}
	}
	
	protected function am_check_trial_version(){
		//config		
		$am_trial_valid_until = 1301412743;
		//check trial time left		
		$am_trial_seconds_left = $am_trial_valid_until-time();		
		//check the trialtime
		$am_trial_still_valid = false;	
		if(
		//check localhost
		($_SERVER['SERVER_NAME']==='localhost' || $_SERVER['SERVER_NAME']==='127.0.0.1') ||
		//check demo time 
		$am_trial_seconds_left >= 0
		){					
			$am_trial_still_valid = true;								
		}
		return $am_trial_still_valid;
	}
	
	
	
	
}

?>