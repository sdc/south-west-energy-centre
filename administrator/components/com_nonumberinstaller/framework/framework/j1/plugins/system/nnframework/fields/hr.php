<?php
/**
 * Element: HR
 * Displays a line
 *
 * @package         NoNumber Framework
 * @version         12.10.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class nnFieldHR
{
	var $_version = '12.10.4';

	function getInput()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/nnframework/css/style.css?v=' . $this->_version);

		return '<div class="nn_panel nn_hr"></div>';
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_HR extends JElement
{
	var $_name = 'HR';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		$this->_nnfield = new nnFieldHR;
		return;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $this->_nnfield->getInput();
	}
}
