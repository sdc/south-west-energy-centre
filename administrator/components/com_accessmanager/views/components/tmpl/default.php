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
	//filter out pi_itemtypes and com_cpanel
	if(!strpos($component_option, '_pi_itemtype_') && $component_option!='com_cpanel' && $component_option!='' && $component_option!='com_accessmanager' && $component_name!='Contact Categories' && $component_name!='Web Link Categories'){	
		//give com_category an option
		if($component_name=='Categories' || $component_name=='Manage Categories'){
			$component_option = 'com_categories';									
		}
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
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_components."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;		
	for (i = 0; i < components.length; i++){
		box_id = components[i]+'__'+usergroup_id;
		hidden_id = components[i]+'__'+usergroup_id+'__hidden';
		if(action==true){
			document.getElementById(box_id).checked = true;
			document.getElementById(hidden_id).value = hidden_id+'__1';
		}else{
			document.getElementById(box_id).checked = false;
			document.getElementById(hidden_id).value = hidden_id+'__';
		}
	}	
}

function toggle_right(hidden_field_id){
	field = document.getElementById(hidden_field_id);
	if(field.value==hidden_field_id+'__1'){
		field.value = hidden_field_id+'__';
	}else{
		field.value = hidden_field_id+'__1';
	}
}

Joomla.submitbutton = function(task){		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='components_apply'){				
		submitform('components_save');		
	}
	if (task=='components_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('components_save');		
	}
}

</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=components'); ?>">	
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
				<?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_INFO'); ?>.
			</p>
			<?php			
					
			//message if item access is not activated		
			if($this->controller->am_config['component_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_COMPONENT_ACTIVE').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}	
			
			echo JHtml::_('sliders.start','config-component-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>	
			<table class="adminlist am_table">
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_USE_COMPONENTACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" name="component_active" value="true" <?php if($this->controller->am_config['component_active']){echo 'checked="checked"';} ?> />
					</td>
					<td><?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_INFO').'.'; ?>
					</td>
				</tr>
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="component_reverse_access" value="true" <?php if($this->controller->am_config['component_reverse_access']){echo 'checked="checked"';} ?> />
					</td>
					<td>
						<?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS_INFO_L'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS_INFO'); 
						}
						
						?>.					
					</td>
				</tr>
				<tr>		
					<td>					
						<?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_MULTILEVEL_ACCESS_REQUIREMENT'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT'); 
						}					
						?>
					</td>
					<td>
						<label style="white-space: nowrap;"><input type="radio" name="component_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['component_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						}
						?></label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="component_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['component_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_EVERY_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_EVERY_GROUP'); 
						}
						?></label>
					</td>
					<td>
						<?php 					
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_MULTILEVEL_ACCESS_REQUIREMENT_INFO'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT_INFO'); 
						}
						?>.					
					</td>
				</tr>
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_MESSAGE_TYPE'); ?>
					</td>
					<td colspan="2">					
						<label><input type="radio" name="components_message_type" value="alert" class="radio" <?php if($this->controller->am_config['components_message_type']=='alert'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_MESSAGE_TYPE_ALERT'); ?></label><br />					
						<label><input type="radio" name="components_message_type" value="only_text" class="radio" <?php if($this->controller->am_config['components_message_type']=='only_text'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_MESSAGE_TYPE_ONLY_TEXT'); ?></label><br />
						<label><input type="radio" name="components_message_type" value="redirect" class="radio" <?php if($this->controller->am_config['components_message_type']=='redirect'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_REDIRECT_TO_URL'); ?></label>:
						<?php
						$no_component_access_url = '';
						if($this->controller->am_config['no_component_access_url']){
							$no_component_access_url = $this->controller->am_config['no_component_access_url'];
						}
						?>
						<input type="text" name="no_component_access_url" class="long_text_field" value="<?php echo $no_component_access_url; ?>" />
						<br />
						<label><input type="radio" name="components_message_type" value="login" class="radio" <?php if($this->controller->am_config['components_message_type']=='login'){echo 'checked="checked"';} ?> /><?php echo $this->controller->am_strtolower(JText::_('JLOGIN')); ?></label>
					</td>				
				</tr>				
			</table>
			<?php echo JHtml::_('sliders.end'); ?>
			<table style="width: 100%;">
				<tr>
					<td>
						<?php 
			
						//legend and message if reverse access	
						$this->controller->reverse_access_warning('component_reverse_access');
									
						 ?>
						<input type="text" name="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" />
						&nbsp;
						<button onclick="this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						&nbsp;
						<button onclick="document.adminForm.filter_search.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
					</td>
					<td class="align_right">
						<?php
							if($this->controller->am_config['based_on']=='level'){		
								echo JText::_('COM_ACCESSMANAGER_DISPLAY_LEVELS');
							}else{
								echo JText::_('COM_ACCESSMANAGER_DISPLAY_USERGROUPS');
							}
						?>:
						<br />
						<button onclick="usergroups_to_cookie();this.form.submit();"><?php echo JText::_('COM_ACCESSMANAGER_GO'); ?></button>
					</td>
				</tr>
			</table>			
		</td>
		<td id="td_accesslevel_selector">
			<?php 			 
			echo $this->controller->accesslevel_selector(); 
			?>
		</td>
	</tr>
</table>	
<table class="adminlist">
	<tr>		
		<th style="text-align: center;">
			id						
		</th>
		<th align="left">&nbsp;
			<?php 
			$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
			echo JHTML::_('grid.sort', $label, 'e.name', $listDirn, $listOrder); 			
			?>				
		</th>
		<?php
			if($this->state->get('accesscolumn')=='yes' && 'temp_disabled'=='true'){
		?>
		<th>
			<?php
				echo '<label class="hasTip" title="'.JText::_('COM_ACCESSMANAGER_JOOMLA_ACCESS_LEVEL').'::Access-Manager '.JText::_('COM_ACCESSMANAGER_JOOMLA_ACCESS_LEVELS_INFO_A').'. Access-Manager '.JText::_('COM_ACCESSMANAGER_JOOMLA_ACCESS_LEVELS_INFO_B').'. Access-Manager '.JText::_('COM_ACCESSMANAGER_JOOMLA_ACCESS_LEVELS_INFO_C').'.">';
				$label = ucfirst(JText::_('JFIELD_ACCESS_LABEL')).' '; 			
				echo JHTML::_('grid.sort', $label, 'l.title', $listDirn, $listOrder); 	
				echo '</label>';
			?>
		</th>
		<?php	
			}//end if show accesscolumn		
			$this->controller->loop_accesslevels($this->am_grouplevels);			
		?>			
	</tr>
		
	<?php
		
		//row with select_all checkboxes
		echo '<tr class="row0">';
		echo '<td>&nbsp;</td>';
		echo '<td align="left" class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';
		if($this->state->get('accesscolumn')=='yes' && 'temp_disabled'=='true'){	
			echo '<td>&nbsp;</td>';
		}
		foreach($this->am_grouplevels as $am_accesslevel){
			echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
		}
		echo '</tr>';
				
		$k = 1;		
		$counter = 0;	
		for($n = 0; $n < count($components_db); $n++){			
			echo '<tr class="row'.$k.'">';
			echo '<td class="column_ids">'.$components_db[$n][2].'</td>';
			echo '<td style="white-space: nowrap;">'.$components_db[$n][0].' ('.$components_db[$n][1].')</td>';	
			if($this->state->get('accesscolumn')=='yes' && 'temp_disabled'=='true'){		
				echo '<td>';	
				echo $components_db[$n][3];
				echo '</td>';
			}		
			foreach($this->am_grouplevels as $am_accesslevel){
				$checked = '';
				$checked_hidden = '';
				if (in_array($components_db[$n][1].'__'.$am_accesslevel->id, $this->access_components)) {
					$checked = 'checked="checked"';
					$checked_hidden = '1';
				}
				echo '<td align="center"><input type="hidden" name="components_access_hidden[]" id="'.$components_db[$n][1].'__'.$am_accesslevel->id.'__hidden" value="'.$components_db[$n][1].'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="componentsAccess[]" id="'.$components_db[$n][1].'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$components_db[$n][1].'__'.$am_accesslevel->id.'__hidden\');" value="'.$components_db[$n][1].'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
			}
			echo '</tr>';
			if($k==1){
				$k = 0;
			}else{
				$k = 1;
			}			
			if($counter==7){
				echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';	
				if($this->state->get('accesscolumn')=='yes' && 'temp_disabled'=='true'){	
					echo '<th>&nbsp;</th>';	
				}
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