<?php
/**
 * Helper File
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
		$tmp = dirname(str_replace('\\', '/', JFactory::getConfig()->get('tmp_path') . '/x'));

		if (strpos($source, $tmp) === false || $source == $tmp) {
			return;
		}

		$package_folder = dirname($source);
		if ($package_folder == $tmp) {
			$package_folder = $source;
		}

		$package_file = '';
		switch (JFactory::getApplication()->input->getString('installtype')) {
			case 'url':
				$package_file = JFactory::getApplication()->input->getString('install_url');
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
		if (JFolder::exists($folder . '/j2')) {
			if (!copy_from_folder($folder . '/j2', 1)) {
				return 0;
			}
		}
		if (JFolder::exists($folder . '/j2_optional')) {
			if (!copy_from_folder($folder . '/j2_optional', 0)) {
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
				if (!(strpos($file, '.menu.ini') === false)) {
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
		$folder = ($type == 'plugin') ? (isset($extra['folder']) ? $extra['folder'] : 'system') : '';
		unset($extra['folder']);

		// set main database where clauses
		$where = array();
		$where[] = $db->qn('type') . ' = ' . $db->q($type);
		switch ($type) {
			case 'component':
				$element = 'com_' . $element;
				break;
			case 'plugin':
				$where[] = $db->qn('folder') . ' = ' . $db->q($folder);
				break;
			case 'module':
				$element = 'mod_' . $element;
				break;
		}
		$where[] = $db->qn('element') . ' = ' . $db->q($element);
		$where = implode(' AND ', $where);

		// get ordering
		$ordering = '';
		switch ($type) {
			case 'plugin':
				$query = $db->getQuery(true);
				$query->select('ordering');
				$query->from('#__extensions');
				$query->where($where);
				$db->setQuery($query);
				$ordering = $db->loadResult();
				break;
			case 'module':
				$query = $db->getQuery(true);
				$query->select('m.ordering');
				$query->from('#__modules AS m');
				$query->where('m.module = ' . $db->q($element) . ' OR m.module = ' . $db->q('mod_' . $element));
				$db->setQuery($query);
				$ordering = $db->loadResult();
				break;
		}

		// get installed state
		$installed = 0;
		if ($reinstall) {
			// remove extension(s) from database
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__extensions');
			$query->where($where);
			$db->setQuery($query);
			$db->query();
			if (in_array($db->name, array('mysql', 'mysqli'))) {
				// reset auto increment
				$query = 'ALTER TABLE `#__extensions` AUTO_INCREMENT = 1';
				$db->setQuery($query);
				$db->query();
			}
			if ($type == 'module') {
				// remove module(s) from database
				$query = $db->getQuery(true);
				$query->delete();
				$query->from('#__modules');
				$query->where('module = ' . $db->q($element) . ' OR module = ' . $db->q('mod_' . $element));
				$db->setQuery($query);
				$db->query();
				if (in_array($db->name, array('mysql', 'mysqli'))) {
					// reset auto increment
					$query = 'ALTER TABLE `#__modules` AUTO_INCREMENT = 1';
					$db->setQuery($query);
					$db->query();
				}
			}
		} else {
			// get installed state
			$query = $db->getQuery(true);
			$query->select('extension_id');
			$query->from('#__extensions');
			$query->where($where);
			$db->setQuery($query);
			$installed = (int) $db->loadResult();
		}


		// execute custom beforeInstall function
		if (function_exists('beforeInstall')) {
			beforeInstall($db);
		}

		$id = $installed;

		// if not installed yet, create database entries
		if (!$installed) {
			if ($type == 'module') {
				// create module database object
				$row = JTable::getInstance('module');
				$row->title = $name;
				$row->module = $element;
				$row->client_id = 1;
				$row->published = 1;
				$row->position = 'status';
				$row->showtitle = 1;
				$row->language = '*';
				foreach ($extra as $key => $val) {
					if (property_exists($row, $key)) {
						$row->$key = $val;
					}
				}
				if ($ordering) {
					$row->ordering = $ordering;
				} else {
					$row->ordering = $row->getNextOrder("position='" . $row->position . "' AND client_id = " . $row->client_id);
				}
				// save module to database
				if (!$row->store()) {
					JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
					return 0;
				}

				// clean up possible garbage first
				$query = $db->getQuery(true);
				$query->delete();
				$query->from('#__modules_menu');
				$query->where('moduleid = ' . (int) $row->id);
				$db->setQuery($query);
				$db->query();

				// create a menu entry for the module
				$query = $db->getQuery(true);
				$query->insert('#__modules_menu');
				$query->values((int) $row->id . ', 0');
				$db->setQuery($query);
				$db->query();
			}

			// create extension database object
			$row = JTable::getInstance('extension');
			$row->name = strtolower($alias);
			$row->element = $alias;
			$row->type = $type;
			$row->enabled = 1;
			$row->client_id = 0;
			$row->access = 1;
			switch ($type) {
				case 'component':
					$row->name = strtoupper('com_' . $row->name);
					$row->element = 'com_' . $row->element;
					$row->access = 0;
					$row->client_id = 1;
					break;
				case 'plugin':
					$row->name = strtoupper('plg_' . $folder . '_' . $row->name);
					$row->folder = $folder;
					if ($ordering) {
						$row->ordering = $ordering;
					}
					break;
				case 'module':
					$row->name = strtoupper('mod_' . $row->name);
					$row->element = 'mod_' . $row->element;
					$row->client_id = 1;
					break;
			}
			foreach ($extra as $key => $val) {
				if (property_exists($row, $key)) {
					$row->$key = $val;
				}
			}

			// save extension to database
			if (!$row->store()) {
				JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
				return 0;
			}
			$id = (int) $row->extension_id;
		}

		// if no extension id is found, return 0 (=not installed)
		if (!$id) {
			return 0;
		}

		// remove manifest cache
		$query = $db->getQuery(true);
		$query->update('#__extensions AS e');
		$query->set('e.manifest_cache = ' . $db->q(''));
		$query->where('e.extension_id = ' . (int) $id);
		$db->setQuery($query);
		$db->query();

		// update manifest cache
		$installer = JInstaller::getInstance();
		$installer->refreshManifestCache((int) $id);

		// add menus for components
		if ($type == 'component') {
			// delete old menu entries
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__menu');
			$query->where('link = ' . $db->q('index.php?option=com_' . $alias));
			$query->where('client_id = 1');
			$db->setQuery($query);
			$db->query();

			// find menu details in xml file
			$file = dirname(dirname(__FILE__)) . '/extensions/j2/administrator/components/com_' . $alias . '/' . $alias . '.xml';
			$xml = JFactory::getXML($file);

			if (isset($xml->administration) && isset($xml->administration->menu)) {
				$menuElement = $xml->administration->menu;

				if ($menuElement) {
					// create menu database object
					$data = array();
					$data['menutype'] = 'menu';
					$data['client_id'] = 1;
					$data['title'] = (string) $menuElement;
					$data['alias'] = $alias;
					$data['link'] = 'index.php?option=' . 'com_' . $alias;
					$data['type'] = 'component';
					$data['published'] = 1;
					$data['parent_id'] = 1;
					$data['component_id'] = $id;
					$attribs = $menuElement->attributes();
					$data['img'] = ((string) $attribs->img) ? (string) $attribs->img : 'class:component';
					$data['home'] = 0;
					$data['language'] = '*';
					$table = JTable::getInstance('menu');

					// save menu to database
					if (!$table->setLocation(1, 'last-child') || !$table->bind($data) || !$table->check() || !$table->store()) {
						JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
						return 0;
					}
				}
			}
		}

		// execute custom afterInstall function
		if (function_exists('afterInstall')) {
			afterInstall($db);
		}

		if ($alias != 'nnframework') {
			$url = 'http://download.nonumber.nl/updates.php?e=' . $alias;
			addUpdateSite($id, $name, 'extension', $url . '&');
		}

		// return 2 for already installed (=update) and 1 for not yet installed (=install)
		return array((($installed) ? 2 : 1), $id);
	}

	function addUpdateSite($id, $name, $type, $location, $enabled = true)
	{
		$name = preg_replace('#^.*? - #', '', $name);

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__update_sites');
		$query->where('(name = ' . $db->quote($name) . ' AND location != ' . $db->quote($location) . ')');
		$query->where('(name != ' . $db->quote($name) . ' AND location = ' . $db->quote($location) . ')', 'OR');
		$db->setQuery($query);
		$db->query();

		$query->clear();
		$query->delete();
		$query->from('#__update_sites');
		$query->where('name != ' . $db->quote($name))->where('location = ' . $db->quote($location));
		$db->setQuery($query);
		$db->query();

		$query->clear();
		$query->select('update_site_id')->from('#__update_sites')->where('location = ' . $db->quote($location));
		$db->setQuery($query);
		$update_site_id = (int) $db->loadResult();

		if (!$update_site_id) {
			$query->clear();
			$query->insert('#__update_sites');
			$query->columns(array($db->quoteName('name'), $db->quoteName('type'), $db->quoteName('location'), $db->quoteName('enabled')));
			$query->values($db->quote($name) . ', ' . $db->quote($type) . ', ' . $db->quote($location) . ', ' . (int) $enabled);
			$db->setQuery($query);
			$db->query();
			$update_site_id = $db->insertid();
		}

		$query->clear();
		$query->delete();
		$query->from('#__updates');
		$query->where('update_site_id = ' . $update_site_id);
		$db->setQuery($query);
		$db->query();

		$query->clear();
		$query->delete();
		$query->from('#__update_sites_extensions');
		$query->where('extension_id = ' . $id);
		$db->setQuery($query);
		$db->query();

		$query->clear();
		$query->insert('#__update_sites_extensions');
		$query->columns(array($db->quoteName('update_site_id'), $db->quoteName('extension_id')));
		$query->values($update_site_id . ', ' . $id);
		$db->setQuery($query);
		$db->query();
	}

	function installFramework($comp_folder)
	{
		$framework_folder = $comp_folder . '/framework/framework';
		$xml_name = 'plugins/system/nnframework/nnframework.xml';
		$xml_file = $framework_folder . '/j2/' . $xml_name;
		if (!JFile::exists($xml_file)) {
			return;
		}

		$do_install = 1;

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
				JFactory::getApplication()->enqueueMessage('Could not install the NoNumber Framework extension', 'error');
				JFactory::getApplication()->enqueueMessage('Could not copy all files', 'error');
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
		// Create database object
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__menu');
		$query->where('title = ' . $db->q('com_' . $alias));
		$db->setQuery($query);
		$db->query();
		if (in_array($db->name, array('mysql', 'mysqli'))) {
			$query = 'ALTER TABLE `#__menu` AUTO_INCREMENT = 1';
			$db->setQuery($query);
			$db->query();
		}

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
		$files = JFolder::files(JPATH_SITE . '/language', 'com_' . $alias . '.ini');
		foreach ($files as $file) {
			JFile::delete(JPATH_SITE . '/language/' . $file);
		}

		// Redirect with message
		JFactory::getApplication()->redirect('index.php?option=com_installer');
	}