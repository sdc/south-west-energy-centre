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
<form class="adminForm">
<p style="margin-left: 4px;">
	<?php echo JText::_('COM_ACCESSMANAGER_SUPPORT_INFO'); ?>:
</p>
<table class="adminlist am_table">	
	<tr>
		<td style="width: 10px;">
			1.
		</td>			
		<td>
			<a href="http://www.pages-and-items.com/extensions/access-manager/faqs" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_FAQS'); ?></a>
		</td>
		<td>
			<?php echo JText::_('COM_ACCESSMANAGER_FAQS_INFO'); ?>.
		</td>
	</tr>
	<tr>
		<td>
			2.
		</td>			
		<td>
			<a href="http://www.pages-and-items.com/forum/advsearch?catids=42" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_SEARCH_FORUM'); ?></a> 
		</td>
		<td>
			<?php echo JText::_('COM_ACCESSMANAGER_SEARCH_FORUM_INFO'); ?> 'Access-Manager'.
		</td>
	</tr>
	<tr>
		<td>
			3.
		</td>			
		<td>
			<a href="http://www.pages-and-items.com/forum/42-access-manager" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_POST_FORUM'); ?></a>
		</td>
		<td>
			<?php echo JText::_('COM_ACCESSMANAGER_POST_FORUM_INFO'); ?> 'Access-Manager'.
		</td>
	</tr>
	<tr>
		<td>
			4.
		</td>			
		<td>
			<a href="http://www.pages-and-items.com/contact" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_CONTACT'); ?></a>
		</td>
		<td>
			<?php echo JText::_('COM_ACCESSMANAGER_CONTACT_INFO'); ?>.
		</td>
	</tr>
</table>
<br /><br />
<p style="margin-left: 4px;">
<?php echo JText::_('COM_ACCESSMANAGER_UPDATE_NOTIFICATIONS'); ?>:
</p>
<table class="adminlist am_table">	
	<tr>
		<td style="width: 10px;">
			<img src="components/com_accessmanager/images/mail.png" alt="mail" />
		</td>
		<td>
			<a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_EMAIL_UPDATE_NOTIFICATIONS'); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<img src="components/com_accessmanager/images/rss.png" alt="rss" />
		</td>
		<td>
			<a href="http://www.pages-and-items.com/extensions/access-manager/update-notifications-for-access-manager" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_RSS'); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<img src="components/com_accessmanager/images/twitter.png" alt="twitter" />
		</td>
		<td>
			<a href="http://twitter.com/PagesAndItems" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_TWITTER'); ?> Twitter</a>
		</td>
	</tr>
</table>
<br /><br />
<p style="margin-left: 4px;">
<?php echo JText::_('COM_ACCESSMANAGER_PAID_CONTENT'); ?>:
</p>
<div style="margin-left: 4px; background: #fff;padding: 5px;">
<?php echo JText::_('COM_ACCESSMANAGER_PAID_CONTENT_INFO_A'); ?>:
	<ul>
		<li>
			<a href="http://extensions.joomla.org/extensions/e-commerce/subscriptions/10723" target="_blank">AEC</a> 							
		</li>
		<li>
			<a href="http://extensions.joomla.org/extensions/e-commerce/paid-membership-a-subscriptions/16566" target="_blank">Payplans</a>
		</li>
		<li>
			<a href="http://extensions.joomla.org/extensions/e-commerce/shopping-cart/11340" target="_blank">Tienda</a>
		</li>
	</ul>	
	<a href="http://www.pages-and-items.com/extensions/access-manager/subscriptions-and-paid-content" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_READ_MORE'); ?></a>
</div>	
</form>
<?php
$this->controller->display_footer();
?>