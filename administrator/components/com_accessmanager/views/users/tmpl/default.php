<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 1.3.1
* @copyright Copyright (C) 2013 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if(task=='users_export'){	
		document.location.href = 'index.php?option=com_accessmanager&view=users&layout=csv';		
	} 
}

</script>

<form action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=users');?>" method="post" name="adminForm">
	<p><?php echo JText::_('COM_ACCESSMANAGER_SUBTITLE_USERS'); ?>.</p>	
	<fieldset id="filter-bar">
		<div class="filter-search fltlft" style="padding-left: 5px;">			
			<input type="text" name="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::sprintf('JSearch_Title', 'Users'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="this.form.filter_search.value=''; this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
				<option value=""> - <?php echo JText::_('COM_ACCESSMANAGER_USERGROUPS');?> - </option>
				<?php echo JHtml::_('select.options', $this->get_groups(), 'value', 'text', $this->state->get('filter.group_id'));?>
			</select>
			<select name="filter_level_id" class="inputbox" onchange="this.form.submit()">
				<option value=""> - <?php echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS');?> - </option>
				<?php echo JHtml::_('select.options', $this->get_levels(), 'value', 'text', $this->state->get('filter.level_id'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>				
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_ACCESSMANAGER_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="">
					<?php echo JHtml::_('grid.sort', 'COM_ACCESSMANAGER_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>							
				<th class="nowrap">
					<?php echo ucfirst(JText::_('COM_ACCESSMANAGER_USERGROUPS')); ?>								
				</th>				
				<th class="nowrap" width="">
					<?php echo ucfirst(JText::_('COM_ACCESSMANAGER_ACCESSLEVELS')); ?>
				</th>								
				<th class="nowrap" width="3%">
					<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>			
		</thead>		
		<tbody>		
		<?php 		
		foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo ($i+1) % 2; ?>">				
				<td>
					<?php echo $this->escape($item->name); ?>
				</td>
				<td class="center">					
					<a href="index.php?option=com_users&task=user.edit&id=<?php echo $item->id; ?>">					
					<?php echo $this->escape($item->username); ?>	
					</a>				
				</td>				
				<td class="center">
					<?php 	
					$group_ids_array = $this->get_users_groups($item->id);									
					foreach($this->groups_title_order_back as $temp){
						if(in_array($temp[0], $group_ids_array)){
							echo $temp[1];
							echo '<br />';
						}
					}
					?>
				</td>
				<td class="center">
					<?php 					
					$levels_ids_array = $this->get_groups_levels($group_ids_array);
						
					foreach($this->levels_title_order as $temp){
						if(in_array($temp->level_id, $levels_ids_array)){
							echo $temp->level_title;
							echo '<br />';
						}
					}
					
					?>
				</td>								
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>	
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php

$this->controller->display_footer();

?>