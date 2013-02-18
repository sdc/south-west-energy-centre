<?php
/**
 * Element: Radio List
 * Displays a list of radio items with a break after each item
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

class nnFieldRadioList
{
	function getInput($name, $id, $value, $params, $children)
	{
		$this->params = $params;

		$options = array();
		foreach ($children as $option) {
			$val = $option->attributes('value');
			$text = $option->data();
			$options[] = JHtml::_('select.option', $val, JText::_($text) . '<br />');
		}

		return JHtml::_('select.radiolist', $options, '' . $name . '', '', 'value', 'text', $value, $id);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_RadioList extends JElement
{
	var $_name = 'RadioList';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldRadioList;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes(), $node->children());
	}
}
