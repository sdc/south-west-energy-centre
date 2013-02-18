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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

/**
 * PlainText Element
 */
class JFormFieldNN_PlainText extends JFormField
{
	public $type = 'PlainText';

	protected function getLabel()
	{
		$this->params = $this->element->attributes();
		$label = trim(NNText::html_entity_decoder(JText::_($this->def('label'))));
		if (!$label != '') {
			return '';
		}
		return $label;
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$description = (trim($this->value) != '') ? trim($this->value) : $this->def('description');

		if ($description == '') {
			return '';
		}

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

		if ($description != '' && $this->def('label') || $this->value) {
			// display as label if there is more than just a description
			$html = '<fieldset class="radio"><label>' . $html . '</label></fieldset>';
		}

		return $html;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}