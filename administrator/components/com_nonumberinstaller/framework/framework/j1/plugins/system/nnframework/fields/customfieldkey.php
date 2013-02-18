<?php
/**
 * Element: Custom Field Key
 * Displays a custom key field (use in combination with customfieldvalue)
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

class nnFieldCustomFieldKey
{
	var $_version = '12.10.4';

	function getInput($name, $id, $value, $params)
	{
		$this->params = $params;

		$size = ($this->def('size') ? 'size="' . $this->def('size') . '"' : '');
		$class = ($this->def('class') ? 'class="' . $this->def('class') . '"' : 'class="text_area"');
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		JHtml::_('behavior.mootools');
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true) . '/plugins/system/nnframework/js/script.js?v=' . $this->_version);

		$html = '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" ' . $class . ' ' . $size . ' />';
		$val_id = str_replace('_key', '_value', $id);
		$script = "
			window.addEvent( 'domready', function() {
				if ( $( 'span_" . $val_id . "' ) ) {
					//$( 'span_" . $id . "' ).injectInside( $( 'span_" . $val_id . "' ) );
				}
			});
		";
		$document->addScriptDeclaration($script);
		$html = '<span id="span_' . $id . '">' . $html . '</span>';
		$random = rand(100000, 999999);
		$html .= '<div id="end-' . $random . '"></div><script type="text/javascript">NNFrameworkHideTD( "end-' . $random . '" );</script>';

		return $html;
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_CustomFieldKey extends JElement
{
	var $_name = 'CustomFieldKey';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		$this->_nnfield = new nnFieldCustomFieldKey;
		return;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value, $node->attributes());
	}
}
