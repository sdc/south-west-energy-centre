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

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

JHtml::_('behavior.tooltip');

$components_db = array();

$components_options_gone = array();
foreach($this->items as $component_db_all){
	$component_name = $component_db_all->name;
	$component_option = $component_db_all->element;
	$component_id = $component_db_all->extension_id;
	$component_leveltitle = $component_db_all->leveltitle;
	//filter out com_cpanel
	if($component_option!='com_cpanel' && $component_option!=''){			
		if(!in_array($component_option, $components_options_gone)){
			$components_options_gone[] = $component_option;
			$component_name = str_replace('com_', '', $component_name);
			$components_db[] = array($component_name, $component_option, $component_id, $component_leveltitle);
		}
	}
}	

//make javascript array from components
$javascript_array_components = 'var components = new Array(';
for($n = 0; $n < count($components_db); $n++){	
	if($n==0){
		$first = false;
	}else{
		$javascript_array_components .= ',';
	}
	$javascript_array_components .= "'".$components_db[$n][1]."'";
}	
$javascript_array_components .= ');';

?>

<script>

<?php echo $javascript_array_components."\n"; ?>

function select_all(usergroup_id, select_all_id){
	new_value = document.getElementById(select_all_id).value;	
	if(new_value==''){
		right = 'i';
	}else if(new_value=='1'){
		right = 'a';
	}else if(new_value=='0'){
		right = 'd';
	}		
	for (i = 0; i < components.length; i++){
		select_id = components[i]+'__'+usergroup_id;			
		document.getElementById(select_id).value = new_value;
		document.getElementById('img_'+select_id).className = 'am_'+right;
		document.getElementById('target_'+select_id).innerHTML = '';
	}	
	document.getElementById(select_all_id).value = 'none';
}

function on_select_change(id){	
	temp = document.getElementById(id).value;	
	if(temp==''){
		right = 'i';
	}else if(temp=='1'){
		right = 'a';
	}else if(temp=='0'){
		right = 'd';
	}
	document.getElementById('img_'+id).className = 'am_'+right;
	document.getElementById('target_'+id).innerHTML = '';
}

Joomla.submitbutton = function(task){		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='componentsbackend_apply'){				
		submitform('componentsbackend_save');		
	}
	if (task=='componentsbackend_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('componentsbackend_save');		
	}
}

</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=componentsbackend'); ?>">	
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />		
	<?php echo JHTML::_( 'form.token' ); ?>
<table id="am_subheader">
	<tr>
		<td>	
			<p>
				<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS'); ?></span>
				<?php echo JText::_('COM_ACCESSMANAGER_COMPONENTSBACKEND_INFO').'.<br />'.JText::_('COM_ACCESSMANAGER_DOES_NOT_OVERRIDE').' Joomla. '.JText::_('COM_ACCESSMANAGER_CB_ONLY_RESTRICTS_ACCESS').' Joomla ACL '.JText::_('COM_ACCESSMANAGER_GROUP_NEEDS_ACCESS').' Joomla, Access-Manager '.JText::_('COM_ACCESSMANAGER_GROUP_NEEDS_ACCESS_B').'. '.'\'Super Users\' '.JText::_('COM_ACCESSMANAGER_NO_RESTRICTIONS_FOR_SUPER_USERS').'. '.JText::_('COM_ACCESSMANAGER_DISPLAYED_ONLY_BACKEND_GROUPS'); ?>.
			</p>
			<?php			
					
			//message if access is not activated		
			if($this->controller->am_config['componentbackend_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_COMPONENT_ACTIVE').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}	
			
			
			echo JHtml::_('sliders.start','config-componentbackend-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>	
			<table class="adminlist am_table">	
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_USE_COMPONENTACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" name="componentbackend_active" value="true" <?php if($this->controller->am_config['componentbackend_active']){echo 'checked="checked"';} ?> />
					</td>
					<td><?php echo JText::_('COM_ACCESSMANAGER_COMPONENTSBACKEND_INFO').'.'; ?>
					</td>
				</tr>
				<tr>		
					<td>					
						<?php 
							echo JText::_('COM_ACCESSMANAGER_DEFAULT_TOPGROUP'); 									
						?>
					</td>
					<td>
						<label style="white-space: nowrap;"><input type="radio" name="componentbackend_default" value="1" class="radio" <?php if($this->controller->am_config['componentbackend_default']=='1'){echo 'checked="checked"';} ?> />
						<img src="components/com_accessmanager/images/right_allow.gif" alt="allowed" /> 
						<?php 				
							echo JText::_('COM_ACCESSMANAGER_ALLOWED'); 				
						?>
						</label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="componentbackend_default" value="0" class="radio" <?php if($this->controller->am_config['componentbackend_default']=='0'){echo 'checked="checked"';} ?> /> 
						<img src="components/com_accessmanager/images/right_deny.gif" alt="denied" />  
						<?php 
							echo JText::_('COM_ACCESSMANAGER_DENIED');				
						?>
						</label>
					</td>
					<td>
						<?php
							echo JText::_('COM_ACCESSMANAGER_DEFAULT_TOPGROUP_INFO').' \''.JText::_('COM_ACCESSMANAGER_INHERITED').'\''; 
						?>.					
					</td>
				</tr>
				<tr>		
					<td>
						<?php 
							echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT'); 
						?>
					</td>
					<td>
						<label style="white-space: nowrap;"><input type="radio" name="componentbackend_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['componentbackend_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						?></label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="componentbackend_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['componentbackend_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php
							echo JText::_('COM_ACCESSMANAGER_EVERY_GROUP');
						?></label>
					</td>
					<td>
						<?php
							echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT_INFO');
						?>.						
					</td>
				</tr>
			</table>
			<?php echo JHtml::_('sliders.end'); ?>
			<table style="width: 100%;">
				<tr>
					<td>
						<br />						
						<input type="text" name="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" />
						&nbsp;
						<button onclick="this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						&nbsp;
						<button onclick="document.adminForm.filter_search.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
					</td>
					<td class="align_right">
						<?php echo JText::_('COM_ACCESSMANAGER_DISPLAY_USERGROUPS'); ?>:
						<br />
						<button onclick="usergroups_to_cookie();this.form.submit();"><?php echo JText::_('COM_ACCESSMANAGER_GO'); ?></button>
					</td>
				</tr>
			</table>			
		</td>
		<td id="td_accesslevel_selector">
			<?php 			
			echo $this->controller->accesslevel_selector('backend', false, true); 
			?>
		</td>
	</tr>
</table>	
<table class="adminlist">
	<tr>		
		<th style="text-align: center; width: 20px;" class="ba_nowrap">
			id						
		</th>
		<th align="left">&nbsp;
			<?php 
			$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
			echo JHTML::_('grid.sort', $label, 'e.name', $listDirn, $listOrder); 			
			?>				
		</th>
		
		<?php	
				
			$this->controller->loop_accesslevels($this->am_grouplevels);			
		?>			
	</tr>
		
	<?php
		
		//row with select_all checkboxes
		echo '<tr class="row0">';
		echo '<td>&nbsp;</td>';
		echo '<td align="left" class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';		
		foreach($this->am_grouplevels as $usergroup){
			echo '<td style="text-align:center;">';
				echo $this->helper->get_access_select_all($usergroup->id);
			echo '</td>';
		}
		echo '</tr>';
				
		$k = 1;		
		$counter = 0;	
		for($n = 0; $n < count($components_db); $n++){			
			echo '<tr class="row'.$k.'">';
			echo '<td class="column_ids">'.$components_db[$n][2].'</td>';
			echo '<td>'.$components_db[$n][0].' ('.$components_db[$n][1].')</td>';	
					
			foreach($this->am_grouplevels as $usergroup){
				
				$access = '';
				$needle_1 = $components_db[$n][1].'__'.$usergroup->id.'__1';				
				if(in_array($needle_1, $this->access_components)){
					$access = '1';
				}
				$needle_0 = $components_db[$n][1].'__'.$usergroup->id.'__0';
				if(in_array($needle_0, $this->access_components)){
					$access = '0';
				}
				
				echo '<td align="center" class="access_selects">';							
					echo $this->helper->get_access_select($components_db[$n][1].'__'.$usergroup->id, $access, $this->access_components, 'componentbackend', $this->controller->am_config);				
				echo '</td>';
			}
			echo '</tr>';
			if($k==1){
				$k = 0;
			}else{
				$k = 1;
			}			
			if($counter==7){
				echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';				
				$this->controller->loop_accesslevels($this->am_grouplevels);
				echo '</tr>';
				$counter = 0;
			}
			$counter = $counter+1;	
		}	
	?>
			
</table>
<table class="adminlist">
	<tfoot>
		<tr>
			<td>
			<?php 
				echo $this->pagination->getListFooter();
			?>
			</td>
		</tr>
	</tfoot>
</table>
</form>
<?php
$this->controller->display_footer();
?>