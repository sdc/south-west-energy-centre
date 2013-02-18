<?php
/**
 * Element: DateTime
 * Element to display the date and time
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

class nnFieldDateTime
{
	function getInput($params)
	{
		$this->params = $params;

		$label = $this->def('label');
		$format = $this->def('format');

		$config = JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));

		if ($format) {
			$html = $date->toFormat($format, 1);
		} else {
			$html = $date->toFormat('', 1);
		}

		if ($label) {
			$html = JText::sprintf($label, $html);
		}

		return $html;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_DateTime extends JElement
{
	var $_name = 'DateTime';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldDateTime;
		return $this->_nnfield->getInput($node->attributes());
	}
}
