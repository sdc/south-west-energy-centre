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

/*
if(!$this->controller->is_super_user){
	echo "<script> alert('you need to be logged in as a super administrator to edit the Access-Manager config.'); window.history.go(-1); </script>";
	exit();
}
*/

//make sure mootools is loaded for submitbutton script
JHTML::_('behavior.mootools');

$checked = ' checked="checked"';
$selected = ' selected="selected"';

?>

<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){
	if (task == 'config_save') {
		submitform('config_save');
	}
	if (task == 'config_apply') {	
		document.getElementById('sub_task').value = 'apply';
		submitform('config_save');
	}		
	if (task == 'cancel') {
		document.location.href = 'index.php?option=com_accessmanager';		
	}	
}

function check_latest_version(){
	document.getElementById('version_checker_target').innerHTML = document.getElementById('version_checker_spinner').innerHTML;
	ajax_url = 'index.php?option=com_accessmanager&task=ajax_version_checker&format=raw';
	var req = new Request.HTML({url:ajax_url, update:'version_checker_target' });	
	req.send();
}

</script>

<form name="adminForm" method="post" action="">
	<input type="hidden" name="option" value="com_accessmanager" />
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="sub_task" id="sub_task" value="" />	
	<?php echo JHTML::_( 'form.token' ); ?>	
	<div style="margin: 0 auto; text-align: left;">		
		<table class="adminlist am_table">				
			<tr>		
				<td width="230">
					Access-Manager		
				</td>
				<td>
					<label style="white-space: nowrap;"><input type="radio" name="am_enabled" value="1" class="radio" <?php if($this->controller->am_config['am_enabled']=='1'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_ENABLED'); ?></label><br />					
					<label style="white-space: nowrap;"><input type="radio" name="am_enabled" value="0" class="radio" <?php if(!$this->controller->am_config['am_enabled']){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_DISABLED'); ?></label>				
				</td>				
				<td>
					<?php 
					echo JText::_('COM_ACCESSMANAGER_IF').' \''.JText::_('COM_ACCESSMANAGER_DISABLED').'\', Access-Manager '.JText::_('COM_ACCESSMANAGER_ENABLE_INFO').'.'; 
					?>	
				</td>
			</tr>	
			<tr>		
				<td>
					<?php 
					echo JText::_('COM_ACCESSMANAGER_ACCESS_BASED_ON');
					?>	
				</td>
				<td class="nowrap">
					<label style="white-space: nowrap;"><input type="radio" name="based_on" value="group" class="radio" <?php if($this->controller->am_config['based_on']=='group'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); ?></label>
					<br />	
					<label style="white-space: nowrap;"><input type="radio" name="based_on" value="level" class="radio" <?php if($this->controller->am_config['based_on']=='level'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); ?></label>														
				</td>
				<td>
					<?php 
					echo JText::_('COM_ACCESSMANAGER_ACCESS_BASED_ON_INFO');
					?>.										
				</td>
			</tr>	
			<tr>		
				<td>
					<?php 
					echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS').' '.JText::_('COM_ACCESSMANAGER_SORT_ORDER');
					?>
				</td>
				<td class="nowrap">
					<select name="level_sort">
						<option value="ordering" <?php
						if($this->controller->am_config['level_sort']=='ordering'){
							echo $selected;
						}
						?>><?php echo $this->controller->am_strtolower(JText::_('JGRID_HEADING_ORDERING')); ?></option>
						<option value="title"<?php
						if($this->controller->am_config['level_sort']=='title'){
							echo $selected;
						}
						?>><?php echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS').' '.JText::_('COM_ACCESSMANAGER_NAME'); ?></option>
					</select>															
				</td>
				<td>															
				</td>
			</tr>			
			<tr>		
				<td>
					<?php echo JText::_('COM_ACCESSMANAGER_HEIGHT_SELECTOR'); ?>
				</td>
				<td>
					<?php			
						echo '<select name="height_multiselect">';
						echo '<option value="all">'.JText::_('COM_ACCESSMANAGER_DISPLAY_ALL_GROUP').'</option>';
						$multiselect_array = array(5, 10, 15, 20, 25);
						foreach($multiselect_array as $multiselect_number){		
							echo '<option value="'.$multiselect_number.'"';
							if($this->controller->am_config['height_multiselect']==$multiselect_number){
								echo $selected;
							}				
							echo '>'.$multiselect_number.'</option>';						
						}
						echo '</select>';						
					?>					
				</td>
				<td>
					<?php echo JText::_('COM_ACCESSMANAGER_HEIGHT_SELECTOR_INFO').'. '.JText::_('COM_ACCESSMANAGER_SHOW_SCROLLBAR'); ?>.					
				</td>
			</tr>
			<tr>		
				<td>
					<?php echo JText::_('COM_ACCESSMANAGER_CACHE'); ?>		
				</td>
				<td style="white-space: nowrap;">
					<?php 
					 $config =& JFactory::getConfig();
        			if ($config->getValue('caching')){					
						echo '<span class="am_red">'.JText::_('COM_ACCESSMANAGER_IS_ENABLED').'</span>';
					}else{
						echo '<span class="am_green">'.JText::_('COM_ACCESSMANAGER_IS_NOT_ENABLED').'</span>';
					}			
				?>					
				</td>
				<td>
					<?php 
					echo JText::_('COM_ACCESSMANAGER_CACHE_INFO').'<br />'; 
					echo JText::_('COM_ACCESSMANAGER_CACHE_INFO2'); 
					echo ' <a href="index.php?option=com_config">';
					echo JText::_('COM_ACCESSMANAGER_GLOBAL_CONFIG');
					echo '</a> ';
					echo JText::_('COM_ACCESSMANAGER_CACHE_INFO3').'.'; 
					?>	
				</td>
			</tr>					
			<tr>		
				<td>
					plugin 'System - Access-Manager'
				</td>
				<td style="white-space: nowrap;">
					<?php 
					if($this->controller->plugin_system_installed){
						echo '<div class="am_green">plugin '.JText::_('COM_ACCESSMANAGER_IS_INSTALLED').'</div>';				
					}else{
						echo '<div class="am_red">'.JText::_('COM_ACCESSMANAGER_IS_NOT_INSTALLED').'</div>';
					}										
					if($this->controller->plugin_system_enabled){
						echo '<div class="am_green">'.JText::_('COM_ACCESSMANAGER_IS_ENABLED').'</div>';		
					}else{
						echo '<span class="am_red">'.JText::_('COM_ACCESSMANAGER_IS_NOT_ENABLED').'</span>';
						echo ' <a href="index.php?option=com_accessmanager&task=enable_plugin&plugin=accessmanager&folder=system">'.JText::_('COM_ACCESSMANAGER_ENABLE_PLUGIN').'</a>';
					}	
					//check ordering of system plugin
					if($this->system_plugin_correct_order){
						echo '<div class="am_green">'.JText::_('COM_ACCESSMANAGER_CORRECT_ORDER').'</div>';				
					}else{						
						echo '<span class="am_red">'.JText::_('COM_ACCESSMANAGER_BAD_ORDER').'</span>';
						echo ' <a href="index.php?option=com_accessmanager&task=reorder_system_plugin">'.JText::_('COM_ACCESSMANAGER_REORDER_PLUGIN').'</a>';
					}			
					?>
				</td>
				<td>					
					<?php					
						echo JText::_('COM_ACCESSMANAGER_SYSTEM_PLUGIN_ORDER');
						echo ': \'System - One Click Action\' '.JText::_('COM_ACCESSMANAGER_AND').' \'System - Admin Tools\'. ';
						echo '<br />'.JText::_('COM_ACCESSMANAGER_ORDER_NOT_NULL').' 0. '.JText::_('COM_ACCESSMANAGER_RECOMMENDED').' -29000.';
					?>					
				</td>
			</tr>																				
			<tr>		
				<td>
					<?php echo $this->controller->am_strtolower(JText::_('JVERSION')); ?>	
				</td>
				<td style="white-space: nowrap;">
					<?php echo $this->controller->version.' ('.$this->controller->am_version_type.' '.$this->controller->am_strtolower(JText::_('JVERSION')).')'; ?>
				</td>
				<td>
					<input type="button" value="<?php echo JText::_('COM_ACCESSMANAGER_CHECK_LATEST_VERSION'); ?>" onclick="check_latest_version();" />					
					<div id="version_checker_target"></div>	
					<span id="version_checker_spinner"><img src="components/com_accessmanager/images/processing.gif" alt="processing" /></span>				
				</td>
			</tr>	
			<tr>		
				<td>
					<?php echo JText::_('COM_ACCESSMANAGER_VERSION_CHECKER'); ?>	
				</td>
				<td>
					<label><input type="checkbox" class="checkbox" name="version_checker" value="true" <?php if($this->controller->am_config['version_checker']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->am_strtolower(JText::_('COM_ACCESSMANAGER_ENABLE')); ?></label>
				</td>
				<td>
					<?php echo JText::_('COM_ACCESSMANAGER_VERSION_CHECKER_INFO'); ?>.				
				</td>
			</tr>			
			<tr>		
				<td colspan="3">&nbsp;
					
				</td>
			</tr>
			</table>					
		</div>
</form>
<?php
$this->controller->display_footer();
?>