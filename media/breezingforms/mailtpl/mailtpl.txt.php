<?php
/**
* BreezingForms - A Joomla Forms Application
* @version 1.7.3
* @package BreezingForms
* @copyright (C) 2008-2011 by Markus Bopp
* @license Released under the terms of the GNU General Public License
**/
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<?php echo $PROCESS_SUBMITTEDAT ?>: <?php echo $SUBMITTED ?><?php echo $NL ?>

<?php foreach ($MAILDATA as $DATA): ?>
<?php echo $DATA[_FF_DATA_TITLE]?>: <?php echo $DATA[_FF_DATA_VALUE]?><?php echo $NL ?>
<?php endforeach; ?>
