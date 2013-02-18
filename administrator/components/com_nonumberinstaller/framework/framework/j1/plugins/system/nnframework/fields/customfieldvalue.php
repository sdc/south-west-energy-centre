<?php
/**
 * Element: Custom Field Value
 * Displays a custom key field (use in combination with customfieldkey)
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

class nnFieldCustomFieldValue
{
	function getLabel($name, $id, $label, $description, $params)
	{
		$this->params = $params;

		$html = '<span id="span_' . $id . '"></span>';
		return $html;
	}

	function getInput($name, $id, $value, $params, $children)
	{
		$this->params = $params;

		$size = ($this->def('size') ? 'size="' . $this->def('size') . '"' : '');
		$class = ($this->def('class') ? 'class="' . $this->def('class') . '"' : 'class="text_area"');
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" ' . $class . ' ' . $size . ' />';
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_CustomFieldValue extends JElement
{
	var $_name = 'CustomFieldValue';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		$this->_nnfield = new nnFieldCustomFieldValue;
		return $this->_nnfield->getLabel($control_name . '[' . $name . ']', $control_name . $name, JText::_($label), $description, $node->attributes(), 1);
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes(), $node->children());
	}
}