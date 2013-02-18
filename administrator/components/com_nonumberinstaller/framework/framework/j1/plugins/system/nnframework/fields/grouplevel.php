<?php
/**
 * Element: Group Level
 * Displays a select box of backend group levels
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

class nnFieldGroupLevel
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		$root = $this->def('root', 'USERS');
		$showroot = $this->def('showroot');
		if (strtoupper($root) == 'USERS' && $showroot == '') {
			$showroot = 0;
		}
		$multiple = $this->def('multiple');
		$notregistered = $this->def('notregistered');

		$control = $name . '';

		$acl = JFactory::getACL();
		$options = $acl->get_group_children_tree(null, $root, $showroot);
		if ($notregistered) {
			$option = new stdClass;
			$option->value = 0;
			$option->text = JText::_('NN_NOT_LOGGED_IN');
			$option->disable = '';
			array_unshift($options, $option);
		}
		foreach ($options as $i => $option) {
			$item_name = $option->text;

			$padding = 0;
			if (strpos($item_name, '&nbsp; ') === 0 || strpos($item_name, '-&nbsp; ') === 0) {
				$item_name = preg_replace('#^-?&nbsp; #', '', $item_name);
			} else if (strpos($item_name, '.&nbsp;') === 0 || strpos($item_name, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') === 0) {
				$item_name = preg_replace('#^\.&nbsp;#', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $item_name);
				while (strpos($item_name, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') === 0) {
					$padding++;
					$item_name = substr($item_name, 36);
				}
				$item_name = preg_replace('#^-&nbsp;#', '', $item_name);
			}
			for ($p = 0; $p < $padding; $p++) {
				$item_name = '&nbsp;&nbsp;' . $item_name;
			}

			$options[$i]->text = $item_name;
		}

		if ($multiple) {
			$control .= '[]';

			if (!is_array($value)) {
				$value = explode(',', $value);
			}

			if (in_array(29, $value)) {
				$value[] = 18;
				$value[] = 19;
				$value[] = 20;
				$value[] = 21;
			}
			if (in_array(30, $value)) {
				$value[] = 23;
				$value[] = 24;
				$value[] = 25;
			}
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';
		return nnHTML::selectlist($options, $control, $value, $id, 5, $multiple);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_GroupLevel extends JElement
{
	var $_name = 'GroupLevel';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldGroupLevel;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
