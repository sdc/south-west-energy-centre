<?php
/**
 * Element: Categories
 * Displays a (multiple) selectbox of available sections and categories
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

class nnFieldCats
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		$size = (int) $this->def('size');
		$multiple = $this->def('multiple');
		$show_ignore = $this->def('show_ignore');
		$auto_select_cats = $this->def('auto_select_cats', 1);

		if (!is_array($value)) {
			$value = explode(',', $value);
		}

		// assemble items to the array
		$options = array();
		if ($show_ignore) {
			if (in_array('-1', $value)) {
				$value = array('-1');
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NN_IGNORE') . ' -', 'value', 'text', 0);
		}
		$items = JHtml::_('category.options', 'com_content');
		foreach ($items as $item) {
			$item->text = NNText::prepareSelectItem($item->text);
			$options[] = $item;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';
		return nnHTML::selectlist($options, $name, $value, $id, $size, $multiple);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_Cats extends JElement
{
	var $_name = 'Cats';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldCats;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
