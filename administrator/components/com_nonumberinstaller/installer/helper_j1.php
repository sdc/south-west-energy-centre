<?php
/**
 * Helper File (for Joomla! 1.5)
 *
 * @package         NoNumber Installer
 * @version         12.10.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Cleanup install files/folders
 */
function cleanupInstall()
{
	$installer = JInstaller::getInstance();
	$source = str_replace('\\', '/', $installer->getPath('source'));
	$config = JFactory::getConfig();
	$tmp = dirname(str_replace('\\', '/', $config->getValue('config.tmp_path') . '/x'));

	if (strpos($source, $tmp) === false || $source == $tmp) {
		return;
	}

	$package_folder = dirname($source);
	if ($package_folder == $tmp) {
		$package_folder = $source;
	}

	$package_file = '';
	switch (JRequest::getString('installtype')) {
		case 'url':
			$package_file = JRequest::getString('install_url');
			$package_file = str_replace(dirname($package_file), '', $package_file);
			break;
		case 'upload':
		default:
			if (isset($_FILES) && isset($_FILES['install_package']) && isset($_FILES['install_package']['name'])) {
				$package_file = $_FILES['install_package']['name'];
			}
			break;
	}
	if (!$package_file && $package_folder != $source) {
		$package_file = str_replace($package_folder . '/', '', $source) . '.zip';
	}

	$package_file = $tmp . '/' . $package_file;

	JInstallerHelper::cleanupInstall($package_file, $package_folder);
}

/**
 * Copies all files from install folder
 */
function installFiles($folder)
{
	if (JFolder::exists($folder . '/all')) {
		if (!copy_from_folder($folder . '/all', 1)) {
			return 0;
		}
	}
	if (JFolder::exists($folder . '/j1')) {
		if (!copy_from_folder($folder . '/j1', 1)) {
			return 0;
		}
	}
	if (JFolder::exists($folder . '/j1_optional')) {
		if (!copy_from_folder($folder . '/j1_optional', 0)) {
			return 0;
		}
	}
	if (JFolder::exists($folder . '/language')) {
		installLanguages($folder . '/language');
	}
	return 1;
}

/**
 * Copies language files to the specified path
 */
function installLanguagesByPath($folder, $path, $force = 1, $all = 1, $break = 1)
{
	if ($all) {
		$languages = JFolder::folders($path);
	} else {
		$lang = JFactory::getLanguage();
		$languages = array($lang->getTag());
	}
	$languages[] = 'en-GB'; // force to include the English files
	$languages = array_unique($languages);

	if (JFolder::exists($path . '/en-GB')) {
		folder_create($path . '/en-GB');
	}

	foreach ($languages as $lang) {
		if (!JFolder::exists($folder . '/' . $lang)) {
			continue;
		}
		$files = JFolder::files($folder . '/' . $lang);
		foreach ($files as $file) {
			$src = $folder . '/' . $lang . '/' . $file;
			$dest = $path . '/' . $lang . '/' . $file;
			if (!(strpos($file, '.sys.ini') === false)) {
				if (JFile::exists($dest)) {
					JFile::delete($dest);
				}
				continue;
			}
			if ($force || JFile::exists($src)) {
				if (!JFile::copy($src, $dest) && $break) {
					return 0;
				}
			}
		}
	}
	return 1;
}

function installExtension($states, $alias, $name, $type = 'component', $extra = array(), $reinstall = 0)
{
	foreach ($states as $state) {
		if (is_array($state)) {
			$ids[] = $state['1'];
			$state = $state['0'];
		}
		if ($state < 1) {
			return -1;
		}
	}

	$app = JFactory::getApplication();

	// Create database object
	$db = JFactory::getDBO();

	// set main vars
	$element = $alias;
	$folder = '';
	switch ($type) {
		case 'component':
			$element = 'com_' . $element;
			break;
		case 'plugin':
			$folder = isset($extra['folder']) ? $extra['folder'] : 'system';

			// Clean up possible garbage first
			$query = 'DELETE FROM `#__plugins`'
				. ' WHERE `element` = ' . $db->quote($element)
				. ' AND `folder` = \'\'';
			$db->setQuery($query);
			$db->query();
			$query = 'ALTER TABLE `#__plugins` AUTO_INCREMENT = 1';
			$db->setQuery($query);
			$db->query();
			break;
		case 'module':
			$element = 'mod_' . $element;
			break;
	}
	unset($extra['folder']);

	// get ordering
	$ordering = '';
	switch ($type) {
		case 'plugin':
			$query = 'SELECT `ordering` FROM `#__plugins`'
				. ' WHERE `element` = ' . $db->quote($element)
				. ' AND `folder` = ' . $db->quote($folder)
				. ' LIMIT 1';
			$db->setQuery($query);
			$ordering = $db->loadResult();
			break;
		case 'module':
			$query = 'SELECT `ordering` FROM `#__modules`'
				. ' WHERE `module` = ' . $db->quote($element)
				. ' LIMIT 1';
			$db->setQuery($query);
			$ordering = $db->loadResult();
			break;
	}

	// get installed state
	$installed = 0;
	if ($reinstall) {
		switch ($type) {
			case 'component':
				$query = 'DELETE FROM `#__components`'
					. ' WHERE `option` = ' . $db->quote($element);
				$db->setQuery($query);
				$db->query();
				$query = 'ALTER TABLE `#__components` AUTO_INCREMENT = 1';
				$db->setQuery($query);
				$db->query();
				break;
			case 'plugin':
				$query = 'DELETE FROM `#__plugins`'
					. ' WHERE `element` = ' . $db->quote($element)
					. ' AND `folder` = ' . $db->quote($folder);
				$db->setQuery($query);
				$db->query();
				$query = 'ALTER TABLE `#__plugins` AUTO_INCREMENT = 1';
				$db->setQuery($query);
				$db->query();
				break;
			case 'module':
				$query = 'DELETE FROM `#__modules`'
					. ' WHERE `module` = ' . $db->quote($element);
				$db->setQuery($query);
				$db->query();
				$query = 'ALTER TABLE `#__modules` AUTO_INCREMENT = 1';
				$db->setQuery($query);
				$db->query();
				break;
		}
	} else {
		// get installed state
		switch ($type) {
			case 'component':
				$query = 'SELECT `id` FROM `#__components`'
					. ' WHERE `option` = ' . $db->quote($element)
					. ' LIMIT 1';
				$db->setQuery($query);
				$installed = (int) $db->loadResult();
				break;
			case 'plugin':
				$query = 'SELECT `id` FROM `#__plugins`'
					. ' WHERE `element` = ' . $db->quote($element)
					. ' AND `folder` = ' . $db->quote($folder)
					. ' LIMIT 1';
				$db->setQuery($query);
				$installed = (int) $db->loadResult();
				break;
			case 'module':
				$query = 'SELECT `id` FROM `#__modules`'
					. ' WHERE `module` = ' . $db->quote($element)
					. ' LIMIT 1';
				$db->setQuery($query);
				$installed = (int) $db->loadResult();
				break;
		}
	}


	// execute custom beforeInstall function
	if (function_exists('beforeInstall_j1')) {
		beforeInstall_j1($db);
	} else if (function_exists('beforeInstall')) {
		beforeInstall($db);
	}

	$id = $installed;

	// if not installed yet, create database entries
	if (!$installed) {
		$row = JTable::getInstance($type);
		switch ($type) {
			case 'component':
				$row->name = $name;
				$row->admin_menu_alt = $name;
				$row->option = $element;
				$row->link = 'option=' . $element;
				$row->admin_menu_link = 'option=' . $element;
				break;
			case 'plugin':
				$row->name = $name;
				$row->element = $element;
				$row->published = 1;
				$row->folder = $folder;
				break;
			case 'module':
				$row->title = $name;
				$row->module = $element;
				$row->client_id = 1;
				$row->published = 1;
				$row->position = 'status';
				$row->showtitle = 1;
				break;
		}
		if ($ordering) {
			$row->ordering = $ordering;
		}

		foreach ($extra as $key => $val) {
			if (property_exists($row, $key)) {
				$row->$key = $val;
			}
		}

		if ($type == 'module') {
			// set ordering
			$row->ordering = $row->getNextOrder("position='" . $row->position . "' AND client_id = " . $row->client_id);

			// Clean up possible garbage first
			$query = 'DELETE FROM `#__modules_menu` WHERE `moduleid` = ' . ( int ) $row->id;
			$db->setQuery($query);
			$db->query();

			// Time to create a menu entry for the module
			$query = 'INSERT INTO `#__modules_menu` VALUES ( ' . ( int ) $row->id . ', 0 )';
			$db->setQuery($query);
			$db->query();
		}

		// save extension to database
		if (!$row->store()) {
			$app->enqueueMessage($row->getError(), 'error');
			return 0;
		}

		$id = (int) $row->id;
	}

	// if no extension id is found, return 0 (=something wrong)
	if (!$id) {
		return 0;
	}

	// execute custom afterInstall function
	if (function_exists('afterInstall_j1')) {
		afterInstall_j1($db);
	} else if (function_exists('afterInstall')) {
		afterInstall($db);
	}

	// return 2 for already installed (=update) and 1 for not yet installed (=install)
	return ($installed) ? 2 : 1;
}

function installFramework($comp_folder)
{
	$framework_folder = $comp_folder . '/framework/framework';
	$xml_name = 'plugins/system/nnframework.xml';
	$xml_file = $framework_folder . '/j1/' . $xml_name;
	if (!JFile::exists($xml_file)) {
		return;
	}

	$do_install = 1;
	$app = JFactory::getApplication();

	$new_version = getXMLVersion($xml_file);
	if ($new_version) {
		$do_install = 1;
		$current_version = getXMLVersion('', 'nnframework', 'plugin');
		if ($current_version) {
			$do_install = version_compare($current_version, $new_version, '<=') ? 1 : 0;
		}
	}

	$success = 1;
	if ($do_install) {
		if (!installFiles($framework_folder)) {
			$app->enqueueMessage('Could not install the NoNumber Framework extension', 'error');
			$app->enqueueMessage('Could not copy all files', 'error');
			$success = 0;
		}
		if ($success) {
			$elements_folder = $comp_folder . '/framework/elements';
			if (JFolder::exists(JPATH_PLUGINS . '/system/nonumberelements') && JFolder::exists($elements_folder)) {
				uninstallLanguages('nonumberelements');
				if (installFiles($elements_folder)) {
					installExtension(array(), 'nonumberelements', 'System - NoNumber Elements', 'plugin', array('published' => '0'), 1);
				}
			}
		}
	}

	if ($success) {
		installExtension(array(), 'nnframework', 'System - NoNumber Framework', 'plugin', array(), 1);
	}
}

function uninstallInstaller($alias = 'nonumberinstaller')
{
	$app = JFactory::getApplication();
	// Create database object
	$db = JFactory::getDBO();

	$query = 'SELECT `id` FROM `#__components`'
		. ' WHERE `option` = ' . $db->quote('com_' . $alias)
		. ' AND `parent` = 0'
		. ' LIMIT 1';
	$db->setQuery($query);
	$id = (int) $db->loadResult();
	if ($id > 1) {
		$installer = JInstaller::getInstance();
		$installer->uninstall('component', $id);
	}
	$query = 'ALTER TABLE `#__components` AUTO_INCREMENT = 1';
	$db->setQuery($query);
	$db->query();

	// Delete language files
	$lang_folder = JPATH_ADMINISTRATOR . '/language';
	$languages = JFolder::folders($lang_folder);
	foreach ($languages as $lang) {
		$file = $lang_folder . '/' . $lang . '/' . $lang . '.com_' . $alias . '.ini';
		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	// Delete old language files
	$files = JFolder::files(JPATH_SITE . '/language', 'com_nonumberinstaller.ini');
	foreach ($files as $file) {
		JFile::delete(JPATH_SITE . '/language/' . $file);
	}

	// Redirect with message
	$app->redirect('index.php?option=com_installer');
}