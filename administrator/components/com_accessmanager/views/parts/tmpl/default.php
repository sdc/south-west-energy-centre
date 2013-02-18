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

//make javascript array from parts
$javascript_array_parts = 'var parts = new Array(';
$first = true;
foreach($this->items as $part){		
	if($first){
		$first = false;
	}else{
		$javascript_array_parts .= ',';
	}
	$javascript_array_parts .= "'".$part->id."'";
}	
$javascript_array_parts .= ');';
		
?>
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_parts."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;	
	for (i = 0; i < parts.length; i++){
		box_id = parts[i]+'__'+usergroup_id;
		hidden_id = parts[i]+'__'+usergroup_id+'__hidden';
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
	if (task == 'part') {			
		document.location.href = 'index.php?option=com_accessmanager&view=part&sub_task=new';		
	}		
	if (task == 'part_delete') {
		if (document.adminForm.boxchecked.value == '0') {						
			alert('<?php echo addslashes(JText::_('COM_ACCESSMANAGER_NOSELECTPART')); ?>');
			return;
		} else {
			if(confirm("<?php echo addslashes(JText::_('COM_ACCESSMANAGER_SUREDELETEPART')); ?>")){
				submitform('part_delete');
			}
		}
	}		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='access_parts_apply'){				
		submitform('access_parts_save');		
	}
	if (task=='access_parts_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('access_parts_save');		
	}
}

</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=parts'); ?>">	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />	
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />	
	<input type="hidden" name="boxchecked" value="0" />	
	<?php echo JHTML::_( 'form.token' ); ?>	
<table id="am_subheader">
	<tr>
		<td>
			<p>
				<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_PART_ACCESS'); ?></span>
				<?php echo JText::_('COM_ACCESSMANAGER_PARTS_INFO'); ?>.
			</p>
			
			<?php
			
			echo '<p>';
			echo JText::_('COM_ACCESSMANAGER_EXAMPLE');
			?>
			1 : <input type="text" name="parts_example" value="{am_part id=3} <?php echo JText::_('COM_ACCESSMANAGER_CONTENT_WHEN_ACCESS'); ?> {/am_part}" class="long_text_field"  style="border:0; font-weight: bold; width: 550px;" /><br />
			<?php
			
			echo JText::_('COM_ACCESSMANAGER_EXAMPLE');
			?>
			2 : <input type="text" name="parts_example2" value="{am_part id=3} <?php echo JText::_('COM_ACCESSMANAGER_CONTENT_WHEN_ACCESS'); ?> {else} <?php echo JText::_('COM_ACCESSMANAGER_CONTENT_WHEN_NO_ACCESS'); ?> {/am_part}" class="long_text_field" style="border:0; font-weight: bold; width: 550px;" />
			<?php
			echo JText::_('COM_ACCESSMANAGER_PARTS_INFO_FOUR').'.';
			echo '<br /><br />';
			echo JText::_('COM_ACCESSMANAGER_PARTS_INFO_TWO').'.';
			echo JText::_('COM_ACCESSMANAGER_PARTS_INFO_THREE').'.';
			echo '</p>';
						
			//message if item access is not activated		
			if($this->controller->am_config['part_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_PARTS_ACTIVE').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}	
			
			echo JHtml::_('sliders.start','config-parts-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>
			<table class="adminlist am_table">	
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_PARTS_ACTIVATE'); ?>
					</td>
					<td>
						<input type="checkbox" name="part_active" value="true" <?php if($this->controller->am_config['part_active']){echo 'checked="checked"';} ?> />
					</td>
					<td>&nbsp;
					</td>
				</tr>
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="part_reverse_access" value="true" <?php if($this->controller->am_config['part_reverse_access']){echo 'checked="checked"';} ?> />
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
						<label style="white-space: nowrap;"><input type="radio" name="part_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['part_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						}
						?></label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="part_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['part_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
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
						<?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE'); ?>
					</td>
					<td colspan="2">	
						<?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE_INFO'); ?>.<br />
						<label><input type="radio" name="parts_not_active" value="as_access" class="radio" <?php if($this->controller->am_config['parts_not_active']=='as_access'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE_ACCESS'); ?>.</label><br />
						<label><input type="radio" name="parts_not_active" value="as_no_access" class="radio" <?php if($this->controller->am_config['parts_not_active']=='as_no_access'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE_NOACCESS'); ?>.</label><br />
						<label><input type="radio" name="parts_not_active" value="nothing" class="radio" <?php if($this->controller->am_config['parts_not_active']=='nothing'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE_NOTHING'); ?>.</label><br />					
						<label><input type="radio" name="parts_not_active" value="code" class="radio" <?php if($this->controller->am_config['parts_not_active']=='code'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE_SHOWCODE'); ?>. (<?php echo JText::_('COM_ACCESSMANAGER_PARTS_NOT_ACTIVATE_NOTHING_TWO'); ?>).</label><br />					
						
					</td>				
				</tr>	
			</table>
			<?php echo JHtml::_('sliders.end'); ?>
			<table style="width: 100%;">
				<tr>
					<td>
						<?php 
			
						//legend and message if reverse access	
						$this->controller->reverse_access_warning('part_reverse_access');	
						
						?>
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area"  />			
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
		<th width="5" align="left">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />			
		</th>	
		<th style="text-align: center;">
			id			
		</th>	
		<th align="left">				
			<?php 
			$label = ucfirst(JText::_('COM_ACCESSMANAGER_NAME')).' '; 			
			echo JHTML::_('grid.sort', $label, 'p.name', $listDirn, $listOrder); 			
			?>				
		</th>	
		<?php				
			$this->controller->loop_accesslevels($this->am_grouplevels);			
		?>		
		
	</tr>
		
	<?php
							
		$k = 1;		
		
		//row with select_all checkboxes
		echo '<tr class="row1">';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';		
		foreach($this->am_grouplevels as $am_accesslevel){
			echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
		}
		echo '</tr>';
		
		$counter = 0;	
		foreach($this->items as $part){	
			if($k==1){
				$k = 0;
			}else{
				$k = 1;
			}				
			echo '<tr class="row'.$k.'">';
			echo '<td><input type="checkbox" id="cb'.$counter.'" name="cid[]" value="'.$part->id.'" onclick="isChecked(this.checked);" /></td>';
			echo '<td class="column_ids">'.$part->id.'</td>';			
			echo '<td>';			
			echo '<a href="index.php?option=com_accessmanager&view=part&id='.$part->id.'">';	
			if($part->description!=''){
				echo '<label class="hasTip" title="'.$part->name.'::'.$part->description.'">';
			}			
			echo $part->name;
			if($part->description!=''){
				echo '</label>';
			}
			echo '</a></td>';					
			foreach($this->am_grouplevels as $am_accesslevel){
				$checked = '';
				$checked_hidden = '';
				if (in_array($part->id.'__'.$am_accesslevel->id, $this->access_parts)) {
					$checked = 'checked="checked"';
					$checked_hidden = '1';
				}
				echo '<td style="text-align:center;"><input type="hidden" name="part_access_hidden[]" id="'.$part->id.'__'.$am_accesslevel->id.'__hidden" value="'.$part->id.'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="part_access[]" id="'.$part->id.'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$part->id.'__'.$am_accesslevel->id.'__hidden\');" value="'.$part->id.'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
			}
			echo '</tr>';			
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