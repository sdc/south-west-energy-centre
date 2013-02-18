<?php
/**
 * RokAjaxSearch Module
 *
 * @package RocketTheme
 * @subpackage rokajaxsearch
 * @version   1.2 September 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 *
 * Inspired on PixSearch Joomla! module by Henrik Hussfelt <henrik@pixpro.net>
 */

defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . DS . 'helper.php');

modRokajaxsearchHelper::inizialize($params->get('include_css'), $params->get('offset_search_result'), $params);

require(JModuleHelper::getLayoutPath('mod_rokajaxsearch'));