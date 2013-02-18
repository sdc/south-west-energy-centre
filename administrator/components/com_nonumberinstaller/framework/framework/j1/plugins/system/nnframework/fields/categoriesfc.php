<?php
/**
 * Element: CategoriesFC
 * Displays a multiselectbox of available Flexicontent categories
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

class nnFieldCategoriesFC
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_flexicontent/admin.flexicontent.php')) {
			return 'Flexicontent files not found...';
		}

		$db = JFactory::getDBO();
		$tables = $db->getTableList();
		if (!in_array($db->getPrefix() . 'flexicontent_cats_item_relations', $tables)) {
			return 'Flexicontent category-item relations table not found in database...';
		}

		$size = (int) $this->def('size');
		$multiple = $this->def('multiple');
		$get_categories = $this->def('getcategories', 1);
		$show_ignore = $this->def('show_ignore');

		if (!is_array($value)) {
			$value = explode(',', $value);
		}

		$flexicomp_params = JComponentHelper::getParams('com_flexicontent');
		$flexi_section = $flexicomp_params->get('flexi_section');

		$query = 'SELECT id, parent_id AS parent, title AS name'
			. ' FROM #__categories'
			. ' WHERE published = 1'
			. ' AND section = ' . $flexi_section
			. ' ORDER BY ordering';
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
		$list = JHTMLMenu::treerecurse(0, '', array(), $children, 9999, 0, 1);

		// assemble items to the array
		$options = array();
		if ($show_ignore) {
			if (in_array('-1', $value)) {
				$value = array('-1');
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NN_IGNORE') . ' -', 'value', 'text', 0);
		}
		foreach ($list as $item) {
			$item_name = NNText::prepareSelectItem($item->treename, 0);
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

class JElementNN_CategoriesFC extends JElement
{
	var $_name = 'CategoriesFC';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldCategoriesFC;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
