<?php
/**
 * Element: CategoriesK2
 * Displays a multiselectbox of available K2 categories
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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

class nnFieldCategoriesK2
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		if (NNFrameworkFunctions::extensionInstalled('k2')) {
			return 'K2 files not found...';
		}

		$db = JFactory::getDBO();
		$tables = $db->getTableList();
		if (!in_array($db->getPrefix() . 'k2_categories', $tables)) {
			return 'K2 category table not found in database...';
		}

		$size = (int) $this->def('size');
		$multiple = $this->def('multiple');
		$get_categories = $this->def('getcategories', 1);
		$show_ignore = $this->def('show_ignore');

		if (!is_array($value)) {
			$value = explode(',', $value);
		}

		$where = 'published = 1';
		if (!$get_categories) {
			$where .= ' AND parent = 0';
		}

		$query = "SELECT id, parent, name FROM #__k2_categories WHERE " . $where;
		$db->setQuery($query);
		$menuItems = $db->loadObjectList();

		// establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems) {
			// first pass - collect children
			foreach ($menuItems as $v) {
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		require_once JPATH_LIBRARIES . '/joomla/html/html/menu.php';
		$list = JHTMLMenu::treerecurse(0, '', array(), $children, 9999, 0, 0);

		// assemble items to the array
		$options = array();
		if ($show_ignore) {
			if (in_array('-1', $value)) {
				$value = array('-1');
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NN_IGNORE') . ' -', 'value', 'text', 0);
		}
		foreach ($list as $item) {
			$item_name = NNText::prepareSelectItem($item->treename, 1);
			$options[] = JHtml::_('select.option', $item->id, $item_name, 'value', 'text', 0);
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';
		return nnHTML::selectlist($options, $name, $value, $id, $size, $multiple);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_CategoriesK2 extends JElement
{
	var $_name = 'CategoriesK2';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldCategoriesK2;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
