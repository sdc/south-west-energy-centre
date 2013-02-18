<?php
/**
 * Element: PlainText
 * Displays plain text as element
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

class nnFieldPlainText
{
	var $_version = '12.10.4';

	function getInput($value, $params)
	{
		// Load common functions
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

		$this->params = $params;

		$description = ($value != '') ? $value : $this->def('description');

		// variables
		$v1 = JText::_($this->def('var1'));
		$v2 = JText::_($this->def('var2'));
		$v3 = JText::_($this->def('var3'));
		$v4 = JText::_($this->def('var4'));
		$v5 = JText::_($this->def('var5'));

		$html = JText::sprintf($description, $v1, $v2, $v3, $v4, $v5);
		$html = trim(NNText::html_entity_decoder($html));
		$html = str_replace('&quot;', '"', $html);
		$html = str_replace('span style="font-family:monospace;"', 'span class="nn_code"', $html);

		return $html;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_PlainText extends JElement
{
	var $_name = 'PlainText';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		$this->_nnfield = new nnFieldPlainText;
		if (!$node->attributes('label') != '') {
			return '';
		}
		return parent::fetchTooltip($label, $description, $node, $control_name, $name);
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $this->_nnfield->getInput($value, $node->attributes());
	}
}