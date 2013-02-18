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
<fieldset>
	<legend>
		<?php echo JText::_('COM_ACCESSMANAGER_ACCESS_EDITTING'); ?>		
	</legend>
	<!--
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=articlesbackend">
				<span class="panel_subactive">				
					<img src="components/com_accessmanager/images/panels/articles.png" alt="" />
				</span>
				<span><?php //echo JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?></span>
			</a>
		</div>
	</div>
	-->
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=modulesbackend">
				<span class="panel<?php 
				if($this->controller->am_config['modulebackend_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/modules.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'); ?></span>
			</a>
		</div>
	</div>	
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=componentsbackend">
				<span class="panel<?php if($this->controller->am_config['componentbackend_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/components.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS'); ?></span>
			</a>
		</div>
	</div>	
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=menuitemsbackend">
				<span class="panel<?php 
				if($this->controller->am_config['menuitembackend_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/menu.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_MENUITEM_ACCESS'); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=pluginsbackend">
				<span class="panel<?php 
				if($this->controller->am_config['pluginbackend_active']){ echo '_active';} 				
				?>">
					<img src="components/com_accessmanager/images/panels/plugins.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_PLUGIN_ACCESS'); ?></span>
			</a>
		</div>
	</div>
</fieldset>