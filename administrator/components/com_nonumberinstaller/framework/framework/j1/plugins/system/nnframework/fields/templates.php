<?php
/**
 * Element: Templates
 * Displays a select box of templates
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

class nnFieldTemplates
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		$size = (int) $this->def('size');
		$multiple = $this->def('multiple');
		$subtemplates = $this->def('subtemplates', 1);
		$show_system = $this->def('show_system', 1);

		require_once JPATH_ADMINISTRATOR . '/components/com_templates/helpers/template.php';
		$rows = TemplatesHelper::parseXMLTemplateFiles(JPATH_ROOT . '/templates');
		$options = $this->createList($rows, JPATH_ROOT . '/templates', $subtemplates, $show_system);

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/html.php';
		return nnHTML::selectlist($options, $name, $value, $id, $size, $multiple);
	}

	function createList($rows, $templateBaseDir, $subtemplates = 1, $show_system = 1)
	{
		$options = array();

		if ($show_system) {
			$options[] = JHtml::_('select.option', 'system:component', JText::_('None') . ' (System - component)');
		}
		foreach ($rows as $row) {
			$options[] = JHtml::_('select.option', $row->directory, $row->name);

			if ($subtemplates) {
				$option = new stdClass;
				$option->value = $row->directory;
				$option->text = $row->name;
				$options_sub = $this->getSubTemplates($option, $templateBaseDir);
				$options = array_merge($options, $options_sub);
			}
		}
		return $options;
	}

	function getSubTemplates($option, $templateBaseDir)
	{
		$options = array();
		$templateDir = dir($templateBaseDir . '/' . $option->value);
		while (false !== ($file = $templateDir->read())) {
			if (is_file($templateDir->path . '/' . $file)) {
				if (!(strpos($file, '.php') === false) && $file != 'index.php') {
					$file_name = str_replace('.php', '', $file);
					if ($file_name != 'index' && $file_name != 'editor' && $file_name != 'error') {
						$options[] = JHtml::_('select.option', $option->value . ':' . $file_name, '&nbsp;&nbsp;' . $file_name);
					}
				}
			}
		}
		$templateDir->close();

		return $options;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_Templates extends JElement
{
	var $_name = 'Templates';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldTemplates;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
