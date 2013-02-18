<?php
/**
 * Extension Install File
 * Does the stuff for the specific extensions
 *
 * @package         Articles Anywhere
 * @version         2.4.6
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$name = 'Articles Anywhere';
$alias = 'articlesanywhere';
$ext = $name . ' (editor button & system plugin)';

// SYSTEM PLUGIN
$states[] = installExtension($states, $alias, 'System - ' . $name, 'plugin', array('folder' => 'system'));

// EDITOR BUTTON PLUGIN
$states[] = installExtension($states, $alias, 'Editor Button - ' . $name, 'plugin', array('folder' => 'editors-xtd'));