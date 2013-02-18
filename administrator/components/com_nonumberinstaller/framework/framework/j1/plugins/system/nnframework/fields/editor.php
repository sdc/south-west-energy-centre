<?php
/**
 * Element: Editor
 * Displays an HTML editor text field
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

class nnFieldEditor
{
	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		$label = $this->def('label');
		$description = $this->def('description');
		$width = $this->def('width', '100%');
		$height = $this->def('height', 400);
		$newline = $this->def('newline');

		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

		$option = JRequest::getCmd('option', '');
		if ($option == 'com_modules') {
			$name = $name . '';
		}

		$html = '';
		if ($newline) {
			$html .= JText::_($description);
			$html .= '</td></tr></table>';
			$html .= '</div></div></fieldset></div>';
			$html .= '<div class="clr"></div><div><fieldset class="adminform">';
			if ($label != '') {
				$html .= '<legend>' . JText::_($label) . '</legend>';
			}
			$html .= '<div><div><div><table width="100%" class="paramlist admintable" cellspacing="1"><tr><td colspan="2" class="paramlist_value">';
		} else {
			if ($label != '') {
				$html .= '<b>' . JText::_($label) . '</b><br />';
			}
			if ($description != '') {
				$html .= JText::_($description) . '<br />';
			}
		}

		$editor = JFactory::getEditor();
		$html .= $editor->display($name, $value, $width, $height, '60', '20', true);
		$html .= '<br clear="all" />';

		return $html;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_Editor extends JElement
{
	var $_name = 'Editor';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		$this->_nnfield = new nnFieldEditor;
		return;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
