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

?>

<script language="javascript" type="text/javascript">

<?php

//make javascript array from categories
$javascript_array_menuitems = 'var menuitems = new Array(';
$first = true;
foreach($this->items as $item){		
	if($first){
		$first = false;
	}else{
		$javascript_array_menuitems .= ',';
	}
	$javascript_array_menuitems .= "'".$item->id."'";
}	
$javascript_array_menuitems .= ');';

echo $javascript_array_menuitems."\n";

?>

function toggle_right(hidden_field_id){
	field = document.getElementById(hidden_field_id);
	if(field.value==hidden_field_id+'__1'){
		field.value = hidden_field_id+'__';
	}else{
		field.value = hidden_field_id+'__1';
	}
}

function select_all(usergroup_id, select_all_id){	
	action = document.getElementById(select_all_id).checked;	
	for (i = 0; i < menuitems.length; i++){		
		box_id = menuitems[i]+'_0_'+usergroup_id;		
		hidden_id = menuitems[i]+'__'+usergroup_id+'__hidden';
		if(action==true){			
			document.getElementById(box_id).checked = true;
			document.getElementById(hidden_id).value = hidden_id+'__1';
		}else{
			
			document.getElementById(box_id).checked = false;
			document.getElementById(hidden_id).value = hidden_id+'__';
		}
	}	
}

Joomla.submitbutton = function(task){		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='menuaccess_apply'){				
		submitform('menuaccess_save');		
	}
	if (task=='menuaccess_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('menuaccess_save');		
	}
}


</script>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=menuaccess'); ?>">	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />	
	<?php echo JHTML::_( 'form.token' ); ?>	
<table id="am_subheader">
	<tr>
		<td>
			<p>
				<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_MENU_ACCESS'); ?></span>
				<?php echo JText::_('COM_ACCESSMANAGER_MENU_INFO2'); ?>.
			</p>
			<?php
						
			//message if item access is not activated		
			if($this->controller->am_config['menuitem_active']==false){				
				echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_ACTIVE_MENU').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
			}	
			
			echo JHtml::_('sliders.start','config-menuitem-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
			echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
			?>
			<table class="adminlist am_table">
				<tr>		
					<td width="300">
						<?php echo JText::_('COM_ACCESSMANAGER_USE_MENU_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" name="menuitem_active" value="true" <?php if($this->controller->am_config['menuitem_active']){echo 'checked="checked"';} ?> />
					</td>
					<td>
					<?php 
						echo JText::_('COM_ACCESSMANAGER_MENU_INFO4').'.';	
						echo '<br />'.JText::_('COM_ACCESSMANAGER_FALLBACK').'.';				
						?>				
						<br /><br />
						<?php
						echo JText::_('COM_ACCESSMANAGER_MAKE_YOUR_MENU_COMPATIBLE').': <a href="http://www.pages-and-items.com/extensions/access-manager/faqs/hide-menu-items-in-other-menu-modules-then-the-joomla-menu-module" target="_blank">'.JText::_('COM_ACCESSMANAGER_MAKE_YOUR_MENU_WORK_WITH_AM').'</a>.';
						$config =& JFactory::getConfig();
						if(!$config->getValue('sef')){
							echo '<br /><br />'; 
							echo '<span class="am_red">'.JText::_('COM_ACCESSMANAGER_SEF_NOT_ENABLED').'</span>. ';
							echo JText::_('COM_ACCESSMANAGER_MENU_INFO3').'. ';
							echo JText::_('COM_ACCESSMANAGER_CACHE_INFO2');
							echo ' <a href="index.php?option=com_config">';
							echo JText::_('COM_ACCESSMANAGER_GLOBAL_CONFIG');
							echo '</a> ';
							echo JText::_('COM_ACCESSMANAGER_ON_TAB_SITE').'.';			
							
							
						}				
					?>
					</td>
				</tr>			
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
					</td>
					<td>
						<input type="checkbox" class="checkbox" name="menuitem_reverse_access" value="true" <?php if($this->controller->am_config['menuitem_reverse_access']){echo 'checked="checked"';} ?> />
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
						<label style="white-space: nowrap;"><input type="radio" name="menuitem_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['menuitem_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
						if($this->controller->am_config['based_on']=='level'){
							echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
						}else{
							echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
						}
						?></label><br />					
						<label style="white-space: nowrap;"><input type="radio" name="menuitem_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['menuitem_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
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
						<?php echo JText::_('COM_ACCESSMANAGER_MENUACCESS_MESSAGE_TYPE'); 
						
						//set default when updating
						if(!$this->controller->am_config['menuaccess_message_type']){
							$this->controller->am_config['menuaccess_message_type'] = 'text';
						}
						
						?>
					</td>
					<td colspan="2">	
						<label><input type="radio" name="menuaccess_message_type" value="alert" class="radio" <?php if($this->controller->am_config['menuaccess_message_type']=='alert'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_MESSAGE_TYPE_ALERT'); ?></label><br />							
						<label><input type="radio" name="menuaccess_message_type" value="only_text" class="radio" <?php if($this->controller->am_config['menuaccess_message_type']=='only_text'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_MESSAGE_TYPE_ONLY_TEXT'); ?>.</label><br />
						<label><input type="radio" name="menuaccess_message_type" value="redirect" class="radio" <?php if($this->controller->am_config['menuaccess_message_type']=='redirect'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_REDIRECT_TO_URL'); ?></label>:
						<?php
						$no_menu_access_url = '';
						if($this->controller->am_config['no_menu_access_url']){
							$no_menu_access_url = $this->controller->am_config['no_menu_access_url'];
						}
						?>
						<input type="text" name="no_menu_access_url" class="long_text_field" value="<?php echo $no_menu_access_url; ?>" />
						<br />
						<label><input type="radio" name="menuaccess_message_type" value="login" class="radio" <?php if($this->controller->am_config['menuaccess_message_type']=='login'){echo 'checked="checked"';} ?> /><?php echo $this->controller->am_strtolower(JText::_('JLOGIN')); ?></label>						
					</td>				
				</tr>					
				<tr>		
					<td>
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS'); ?>
					</td>
					<td colspan="2">
						<?php			
							echo $this->helper->display_multigrouplevel_select_config('menuitem', $this->controller->am_config);				
						?>					
					
						<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS_INFO_MENUITEM'); ?>.					
					</td>
				</tr>					
			</table>	
			<?php echo JHtml::_('sliders.end'); ?>
			<table style="width: 100%;">
				<tr>
					<td>
						<?php 
										
			
							//legend and message if reverse access	
							$this->controller->reverse_access_warning('menuitem_reverse_access');
							
							//message in free version that these restrictions will not work in free version
							$this->controller->not_in_free_version();
									
						?>
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area"  />			
						&nbsp;
						<button onclick="this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						&nbsp;
						<button onclick="document.adminForm.filter_search.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>			
						&nbsp;			
						<select name="filter_type" class="inputbox" onchange="this.form.submit()">
							<option value="all"> - <?php echo JText::_('COM_ACCESSMANAGER_SELECT_MENU_TYPE');?> - </option>
							<?php echo JHtml::_('select.options', $this->get_menu_type_options(), 'value', 'text', $this->state->get('filter.type'), true);?>
						</select>
						<!--
						&nbsp;
						<select name="filter_access" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
						</select>
						-->
						&nbsp;
						<select name="filter_published" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
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
		<th>
			<?php 
			$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
			echo JHTML::_('grid.sort', $label, 'm.title', $listDirn, $listOrder); 			
			?>	
			&nbsp;	
			<?php 
			$label = ucfirst(JText::_('JFIELD_ORDERING_LABEL')).' '; 			
			echo JHTML::_('grid.sort', $label, 'm.lft', $listDirn, $listOrder); 			
			?>							
		</th>
		<?php					
			$this->controller->loop_accesslevels($this->am_grouplevels);			
		?>
	</tr>
		
	<?php


$k = 1;		
			
//row with select_all checkboxes
echo '<tr class="row0">';
echo '<td>&nbsp;</td>';
echo '<td class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';
foreach($this->am_grouplevels as $am_accesslevel){
	echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
}
echo '</tr>';

$counter = 0;
foreach($this->items as $item){
	if($k==1){
		$k = 0;
	}else{
		$k = 1;
	}
	echo '<tr class="row'.$k.'">';					
	echo '<td class="column_ids">'.$item->id.'</td>';
	$has_superscript = '';
	if($item->published=='0' || $item->type=='alias'){
		$has_superscript = ' has_superscript';
	}
	echo '<td class="indent-'.(intval(($item->level-1)*15)+4).$has_superscript.'">';	
	echo $item->title;
	if($item->published=='0'){
		echo '<sup class="am_superscript">1</sup>';
	}
	if($item->type=='alias'){
		echo '<sup class="am_superscript">2</sup>';
	}		
	echo '</td>';	
	foreach($this->am_grouplevels as $accesslevel){
		$checked = '';
		$checked_hidden = '';
		if (in_array($item->id.'__'.$accesslevel->id, $this->access_menuitems)){
			$checked = 'checked="checked"';
			$checked_hidden = '1';
		}
		echo '<td align="center"><input type="hidden" name="menu_access_hidden[]" id="'.$item->id.'__'.$accesslevel->id.'__hidden" value="'.$item->id.'__'.$accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="menu_access[]" id="'.$item->id.'_0_'.$accesslevel->id.'" onclick="toggle_right(\''.$item->id.'__'.$accesslevel->id.'__hidden\');" value="'.$item->id.'__'.$accesslevel->id.'" '.$checked.' /></td>';
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
<table>
	<tr>
		<td class="am_red">1
		</td>
		<td>=
		</td>
		<td><?php echo JText::_('COM_ACCESSMANAGER_NOT_PUBLISHED_B'); ?>.
		</td>
	</tr>
	<tr>
		<td class="am_red">2
		</td>
		<td>=
		</td>
		<td><?php echo JText::_('COM_ACCESSMANAGER_ALIAS_WARNING'); ?>.
		</td>
	</tr>
</table>
</form>
<?php

$this->controller->display_footer();

?>