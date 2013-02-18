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

?>
<form name="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=tools'); ?>">
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>		
<div style="margin-left: 4px; background: #fff;padding: 5px;">
<p>
	<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS_INFO'); ?>.
</p>
<p>
	<label>
		<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_article_access" /> 
		<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?>
	</label>
	<br />
	<label>
		<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_category_access" /> 
		<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS'); ?>
	</label>
	<br />
	<label>
		<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_module_access" /> 
		<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'); ?>
	</label>
	<br />
	<label>
		<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_menuitem_access" /> 
		<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_MENU_ACCESS'); ?>
	</label>
	<br />
	<br />
	<select name="import_rights_to">
		<option value="level">
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_ACCESSLEVELS'); ?>
		</option>
		<option value="group" selected="selected">
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_GROUPS'); ?>
		</option>
	</select>
	<?php 	
		echo JText::_('COM_ACCESSMANAGER_ACCESS_CURRENTLY_BASED_ON').': '; 
		if($this->controller->am_config['based_on']=='group'){
			echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); 
		}else{
			echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); 
		}
	?>
	<br />
	<br />
	<input type="button" value="<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS'); ?>" onclick="Joomla.submitbutton('import_access_settings')" />
</p>
</div>
<div style="margin-left: 4px; margin-top: 10px; background: #fff;padding: 5px;">
<p>
	<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS_AMM').' '.JText::_('COM_ACCESSMANAGER_ADMINMENUMANAGER_ACCESS').' '.JText::_('COM_ACCESSMANAGER_FROM'); ?> component <a href="http://www.pages-and-items.com/extensions/admin-menu-manager" target="_blank">Admin-Menu-Manager</a>.
</p>
<p>
	<select name="import_rights_to_amm">
		<option value="level">
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_ACCESSLEVELS'); ?>
		</option>
		<option value="group" selected="selected">
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_GROUPS'); ?>
		</option>
	</select>
	<?php 	
		echo JText::_('COM_ACCESSMANAGER_ACCESS_CURRENTLY_BASED_ON').': '; 
		if($this->controller->am_config['based_on']=='group'){
			echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); 
		}else{
			echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); 
		}
	?>
	<br />
	<br />
	<input type="button" value="<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS'); ?>" onclick="Joomla.submitbutton('import_access_settings_amm')" />
</p>

	
</div>	
</form>
<?php
$this->controller->display_footer();
?>