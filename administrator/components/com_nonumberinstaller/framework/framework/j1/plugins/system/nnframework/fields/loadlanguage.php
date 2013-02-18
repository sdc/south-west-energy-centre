<?php
/**
 * Element: Load Language
 * Loads the English language file as fallback
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

class nnFieldLoadLanguage
{
	var $_version = '12.10.4';

	function getInput($params)
	{
		$this->params = $params;

		JHtml::_('behavior.mootools');
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true) . '/plugins/system/nnframework/js/script.js?v=' . $this->_version);

		$extension = $this->def('extension');
		$admin = $this->def('admin', 1);

		$path = $admin ? JPATH_ADMINISTRATOR : JPATH_SITE;
		// load the admin language file
		$lang = JFactory::getLanguage();
		if ($lang->getTag() != 'en-GB') {
			// Loads English language file as fallback (for undefined stuff in other language file)
			$lang->load($extension, $path, 'en-GB');
		}
		$lang->load($extension, $path, null, 1);

		$random = rand(100000, 999999);
		return '<div id="end-' . $random . '"></div><script type="text/javascript">NNFrameworkHideTD( "end-' . $random . '" );</script>';
	}

	function loadLanguage($extension, $admin = 1)
	{
		if ($extension) {
			if ($admin) {
				$path = JPATH_ADMINISTRATOR;
			} else {
				$path = JPATH_SITE;
			}
			$lang = JFactory::getLanguage();
			if ($lang->getTag() != 'en-GB') {
				// Loads English language file as fallback (for undefined stuff in other language file)
				$lang->load($extension, $path, 'en-GB');
			}
			$lang->load($extension, $path, null, 1);
		}
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

class JElementNN_LoadLanguage extends JElement
{
	var $_name = 'LoadLanguage';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldLoadLanguage;
		return $this->_nnfield->getInput($node->attributes());
	}
}
