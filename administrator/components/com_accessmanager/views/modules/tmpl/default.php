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

$selected = 'selected="selected"'; 
	
JHtml::_('behavior.tooltip');

$am_modules_array = array();
foreach($this->items as $am_module){
	$am_module_id = $am_module->id;
	$am_module_title = $am_module->title;	
	$am_module_leveltitle = $am_module->leveltitle;	
	$am_module_published = $am_module->published;
	$am_modules_array[] = array($am_module_id, $am_module_title, $am_module_leveltitle, $am_module_published);	
}	

//make javascript array from components
$javascript_array_modules = 'var modules = new Array(';
for($n = 0; $n < count($am_modules_array); $n++){	
	if($n==0){
		$first = false;
	}else{
		$javascript_array_modules .= ',';
	}
	$javascript_array_modules .= "'".$am_modules_array[$n][0]."'";
}	
$javascript_array_modules .= ');';

//echo $javascript_array_modules;
//echo $n;
?>
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_modules."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;		
	for (i = 0; i < modules.length; i++){
		box_id = modules[i]+'__'+usergroup_id;
		hidden_id = modules[i]+'__'+usergroup_id+'__hidden';
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
	if (task=='modules_apply'){				
		submitform('modules_save');		
	}
	if (task=='modules_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('modules_save');		
	}
}

</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=modules'); ?>">	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />				
	<?php echo JHTML::_( 'form.token' ); ?>	
<table id="am_subheader">
	<tr>
		<td>	
			<p>
				<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'); ?></span>
				<?php echo JText::_('COM_ACCESSMANAGER_MODULES_INFO'); ?>.
			</p>
			<?php
						
			//message if item access is not activated		
			if($this->controller->am_config['module_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_MODULES_ACTIVE').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}	
			
			echo JHtml::_('sliders.start','config-module-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>	
			<table class="adminlist am_table">		
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_USE_MODULEACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" name="module_active" value="true" <?php if($this->controller->am_config['module_active']){echo 'checked="checked"';} ?> />
					</td>
					<td><?php
					echo JText::_('COM_ACCESSMANAGER_MODULES_INFO').'.';
					echo '<br />'.JText::_('COM_ACCESSMANAGER_FALLBACK').'.';
					?>
					</td>
				</tr>
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="module_reverse_access" value="true" <?php if($this->controller->am_config['module_reverse_access']){echo 'checked="checked"';} ?> />
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
						<label style="white-space: nowrap;"><input type="radio" name="module_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['module_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						}
						?></label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="module_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['module_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
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
					<td>
						<?php echo JText::_('JACTION_ADMIN_GLOBAL').' '.$this->controller->am_strtolower(JText::_('JGRID_HEADING_ACCESS')); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="module_superadmins" value="true" <?php if($this->controller->am_config['module_superadmins']){echo 'checked="checked"';} ?> />
					</td>
					<td>
						<?php echo JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('COM_ACCESSMANAGER_ALWAYS_HAVE_ACCESS'); ?>.	
						<?php 
						if(!$this->controller->am_config['module_superadmins']){
							echo '<a href="index.php?option=com_accessmanager&task=give_super_admin_module_rights_back">';
							echo JText::_('COM_ACCESSMANAGER_GIVE').' '.JText::_('JACTION_ADMIN_GLOBAL').' '.$this->controller->am_strtolower(JText::_('JGRID_HEADING_ACCESS'));
							echo '</a>';
						}
						?>				
					</td>
				</tr>
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_BACKEND').' '.$this->controller->am_strtolower(JText::_('JGRID_HEADING_ACCESS')); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="module_backend" value="true" <?php if($this->controller->am_config['module_backend']){echo 'checked="checked"';} ?> />
					</td>
					<td>
						<?php echo JText::_('JTOOLBAR_ENABLE').' '.JText::_('COM_ACCESSMANAGER_BACKEND').' '.$this->controller->am_strtolower(JText::_('JGRID_HEADING_ACCESS')); ?>.
						<?php echo JText::_('COM_ACCESSMANAGER_MODULEBACKEND');	?>.							
					</td>
				</tr>			
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS'); ?>
					</td>
					<td colspan="2">
						<?php			
							echo $this->helper->display_multigrouplevel_select_config('module', $this->controller->am_config, 1);				
						?>					
					
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS_INFO_MODULE'); ?>.					
					</td>
				</tr>
			</table>
			<?php echo JHtml::_('sliders.end'); ?>
			<table style="width: 100%;">
				<tr>
					<td>
						<?php				
							//legend and message if reverse access	
							$this->controller->reverse_access_warning('module_reverse_access');
							
							//message in free version that these restrictions will not work in free version
							$this->controller->not_in_free_version();
											
							?>
							<input type="text" name="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area"  />
							&nbsp;
							<button onclick="this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
							&nbsp;
							<button onclick="document.adminForm.filter_search.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
							&nbsp;				
							<select name="filter_siteadmin" class="inputbox" onchange="this.form.submit()">
								<option value="0" <?php if($this->state->get('filter.siteadmin')){echo $selected;} ?>><?php echo JText::_('JSITE');?></option>
								<option value="1" <?php if($this->state->get('filter.siteadmin')){echo $selected;} ?>><?php echo JText::_('JADMINISTRATOR');?></option>
							</select>
							&nbsp;				
							<select name="filter_state" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
								<?php echo JHtml::_('select.options', $this->getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
							</select>
							&nbsp;							
							<select name="filter_position" class="inputbox" onchange="this.form.submit()">
								<option value=""> - <?php echo JText::_('COM_ACCESSMANAGER_SELECT_POSITION');?> - </option>
								<?php echo JHtml::_('select.options', $this->getPositions(0), 'value', 'text', $this->state->get('filter.position'));?>
							</select>
							&nbsp;
							<select name="filter_module" class="inputbox" onchange="this.form.submit()">
								<option value=""> - <?php echo JText::_('COM_ACCESSMANAGER_SELECT_MODULE');?> - </option>
								<?php echo JHtml::_('select.options', $this->getModules(0), 'value', 'text', $this->state->get('filter.module'));?>
							</select>
							&nbsp;
							<select name="filter_language" class="inputbox" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
								<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
							</select>
										
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
			echo $this->controller->accesslevel_selector(0, 1); 
			?>
		</td>
	</tr>
</table>	
<table class="adminlist">
	<tr>		
		<th style="text-align: center;">
			id			
		</th>	
		<th align="left">
			<?php 
				$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
				echo JHTML::_('grid.sort', $label, 'm.title', $listDirn, $listOrder); 			
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
		foreach($this->am_grouplevels as $am_accesslevel){
			echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
		}
		echo '</tr>';
				
		$k = 1;		
		$counter = 0;	
		for($n = 0; $n < count($am_modules_array); $n++){			
			echo '<tr class="row'.$k.'"><td class="column_ids">'.$am_modules_array[$n][0].'</td>';
			$has_superscript = '';
			if($am_modules_array[$n][3]=='0'){
				$has_superscript = ' class="has_superscript"';
			}
			echo '<td'.$has_superscript.'>'.$am_modules_array[$n][1];
			if($am_modules_array[$n][3]=='0'){
				echo '<sup class="am_superscript">1</sup>';
			}
			echo '</td>';						
			foreach($this->am_grouplevels as $am_accesslevel){
				$checked = '';
				$checked_hidden = '';
				if (in_array($am_modules_array[$n][0].'__'.$am_accesslevel->id, $this->access_modules)) {
					$checked = 'checked="checked"';					
					$checked_hidden = '1';
				}
				echo '<td align="center"><input type="hidden" name="module_access_hidden[]" id="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'__hidden" value="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="module_access[]" id="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'__hidden\');" value="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
			}
			echo '</tr>';
			if($k==1){
				$k = 0;
			}else{
				$k = 1;
			}			
			if($counter==7){
				echo '<tr><th colspan="2">&nbsp;</th>';					
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
<table>
	<tr>
		<td class="am_red">1
		</td>
		<td>=
		</td>
		<td><?php echo JText::_('COM_ACCESSMANAGER_NOT_PUBLISHED_B'); ?>.
		</td>
	</tr>	
</table>
</form>
<?php
$this->controller->display_footer();
?>