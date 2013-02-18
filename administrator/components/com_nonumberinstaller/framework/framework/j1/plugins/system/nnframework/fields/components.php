<?php
/**
 * Element: Components
 * Displays a list of components with check boxes
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

class nnFieldComponents
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		$frontend = $this->def('frontend', 1);
		$admin = $this->def('admin', 1);
		$show_content = $this->def('show_content', 0);
		$size = (int) $this->def('size');

		if (!$frontend && !$admin) {
			return '';
		}

		$components = $this->getComponents($frontend, $admin, $show_content);

		$options = array();

		foreach ($components as $component) {
			$options[] = JHtml::_('select.option', $component->element, $component->name, 'value', 'text');
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';
		return nnHTML::selectlist($options, $name, $value, $id, $size, 1);
	}

	function getComponents($frontend = 1, $admin = 1, $show_content = 0)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$db = JFactory::getDBO();

		$where = array();
		$where[] = $db->nameQuote('name') . ' != ""';
		$where[] = $db->nameQuote('option') . ' != ""';
		$where[] = $db->nameQuote('parent') . ' = 0';
		$where2 = array();
		if ($admin) {
			$where2[] = $db->nameQuote('admin_menu_link') . ' != ""';
		}
		if ($frontend) {
			$where2[] = $db->nameQuote('link') . ' != ""';
		}
		$where[] = '(' . implode(' OR ', $where2) . ')';
		$query = 'SELECT ' . $db->nameQuote('name') . ', ' . $db->nameQuote('option') . ' AS ' . $db->nameQuote('element')
			. ' FROM #__components'
			. ' WHERE ' . implode(' AND ', $where)
			. ' GROUP BY ' . $db->nameQuote('element')
			. ' ORDER BY ' . $db->nameQuote('element') . ', ' . $db->nameQuote('name');
		$db->setQuery($query);
		$components = $db->loadObjectList();

		$comps = array();
		$lang = JFactory::getLanguage();

		foreach ($components as $i => $component) {
			// return if there is no main component folder
			if (!($frontend && JFolder::exists(JPATH_SITE . '/components/' . $component->element))
				&& !($admin && JFolder::exists(JPATH_ADMINISTRATOR . '/components/' . $component->element))
			) {
				continue;
			}
			$comps[preg_replace('#[^a-z0-9_]#i', '', $component->name . '_' . $component->element)] = $component;
		}
		ksort($comps);

		return $comps;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_Components extends JElement
{
	var $_name = 'Components';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldComponents;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
