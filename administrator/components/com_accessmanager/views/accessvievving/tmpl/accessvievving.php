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
		<?php echo JText::_('COM_ACCESSMANAGER_ACCESS_VIEWING'); ?> 		
	</legend>
	<div style="float: left;">
		<div class="icon">						
			<a href="index.php?option=com_accessmanager&view=articles">							
				<span class="panel<?php if($this->controller->am_config['article_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/articles.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?></span>
			</a>			
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=categories">
				<span class="panel<?php 
				if($this->controller->am_config['category_active']){ echo '_active';}
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/categories.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS'); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=modules">
				<span class="panel<?php 
				if($this->controller->am_config['module_active']){ echo '_active';} 
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
			<a href="index.php?option=com_accessmanager&view=components">
				<span class="panel<?php if($this->controller->am_config['component_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/components.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS'); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=menuaccess">
				<span class="panel<?php 
				if($this->controller->am_config['menuitem_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/menu.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_MENU_ACCESS'); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=parts">
				<span class="panel<?php if($this->controller->am_config['part_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/parts.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_PART_ACCESS'); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=adminmenumanager">
				<span class="panel<?php 
				if($this->controller->am_config['adminmenumanager_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">				
					<img src="components/com_accessmanager/images/panels/adminmenumanager.png" alt="" />
				</span>
				<span><?php echo JText::_('COM_ACCESSMANAGER_ADMINMENUMANAGER_ACCESS'); ?></span>
			</a>
		</div>
	</div>
</fieldset>