<?php
/**
 * Element: License
 * Displays the License state
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

class nnFieldLicense
{
	function getInput($params)
	{
		$this->params = $params;

		$extension = $this->def('extension');

		if (!strlen($extension)) {
			return;
		}

		// Import library dependencies
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
		$licenses = NNLicenses::getInstance();

		return $licenses->getMessage($extension);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}

/* For backward compatibility */
if (!function_exists('NoNumber_License_outputState')) {
	function NoNumber_License_outputState($extension)
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
		$licenses = NNLicenses::getInstance();

		return $licenses->getMessage($extension);
	}
}
if (!function_exists('NoNumber_License_getState')) {
	function NoNumber_License_getState($extension)
	{
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/licenses.php';
		$licenses = NNLicenses::getInstance();

		return $licenses->getState($extension);
	}
}

class JElementNN_License extends JElement
{
	var $_name = 'License';

	function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return;
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldLicense;
		return $this->_nnfield->getInput($node->attributes());
	}
}
