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
<p>
<img src="components/com_accessmanager/images/checkbox_on.gif" alt="" /> = <?php echo JText::_('COM_ACCESSMANAGER_RESTRICTION_TYPE').' '.JText::_('COM_ACCESSMANAGER_ENABLED'); ?><br />
<img src="components/com_accessmanager/images/checkbox_off.gif" alt="" /> = <?php echo JText::_('COM_ACCESSMANAGER_RESTRICTION_TYPE').' '.JText::_('COM_ACCESSMANAGER_DISABLED'); ?>
<?php
if($this->controller->am_version_type=='free'){
?>
<br />
<img src="components/com_accessmanager/images/checkbox_red.gif" alt="" /> = <?php echo JText::_('COM_ACCESSMANAGER_NOT_IN_FREE').'.'; ?>
<?php
}
?>
</p>