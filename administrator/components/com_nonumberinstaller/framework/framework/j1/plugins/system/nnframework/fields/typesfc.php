<?php
/**
 * Element: TypesFC
 * Displays a multiselectbox of available Flexicontent Types
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

class nnFieldTypesFC
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

		if (!is_array($value)) {
			$value = explode(',', $value);
		}

		$query = 'SELECT  id, name FROM #__flexicontent_types WHERE published = 1';
		$db->setQuery($query);
		$list = $db->loadObjectList();

		// assemble items to the array
		$options = array();
		foreach ($list as $item) {
			$item_name = NNText::prepareSelectItem($item->name, 1);
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

class JElementNN_TypesFC extends JElement
{
	var $_name = 'TypesFC';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldTypesFC;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
