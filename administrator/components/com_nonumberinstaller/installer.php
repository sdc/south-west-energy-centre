<?php
/**
 * Installer File
 * Performs an install / update of NoNumber extensions
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

$app = JFactory::getApplication();
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

define('JV15', (version_compare(JVERSION, '1.6', 'l')));

$jv = JV15 ? 'j1' : 'j2';
$comp_folder = dirname(__FILE__);

require_once $comp_folder . '/installer/helper_' . $jv . '.php';

cleanupInstall();

// Install the Installer languages
installLanguages($comp_folder . '/installer/language', 1, 0);

// Load language for messaging
$lang = JFactory::getLanguage();
if ($lang->getTag() != 'en-GB') {
	// Loads English language file as fallback (for undefined stuff in other language file)
	$lang->load('com_nonumberinstaller', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('com_nonumberinstaller', JPATH_ADMINISTRATOR, null, 1);

if (version_compare(PHP_VERSION, '5.3.0', 'l')) {
	$app->enqueueMessage(JText::sprintf('NNI_NOT_COMPATIBLE_PHP', PHP_VERSION, '5.3'), 'error');
	uninstallInstaller();
}
if (round(JVERSION, 1) == 1.6 || round(JVERSION, 1) == 1.7) {
	$app->enqueueMessage(JText::sprintf('NNI_NOT_COMPATIBLE', round(JVERSION, 1)), 'error');
	uninstallInstaller();
}

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$install_file = $comp_folder . '/extensions.php';
if (!JFile::exists($install_file) || !is_readable($install_file)) {
	$app->enqueueMessage(JText::sprintf('NNI_CANNOT_READ_THE_REQUIRED_INSTALLATION_FILE', $install_file), 'error');
	uninstallInstaller();
} else if (!JFolder::exists($comp_folder . '/extensions/' . $jv)) {
	$app->enqueueMessage(JText::sprintf('NNI_NOT_COMPATIBLE', round(JVERSION, 1)), 'error');
	uninstallInstaller();
}

// Create database object
$db = JFactory::getDBO();

$states = array();
$ids = array();
$has_installed = 0;
$has_updated = 0;
$has_error = 0;

$ext = 'NNI_THE_EXTENSION'; // default value. Will be overruled in extensions.php
require_once $install_file;

if (is_array($states)) {
	foreach ($states as $state) {
		if (is_array($state)) {
			$ids[] = $state['1'];
			$state = $state['0'];
		}
		if ($state === 2) {
			$has_updated = 1;
		} else if ($state === 1) {
			$has_installed = 1;
		} else {
			$has_installed = $has_updated = 0;
			if ($state === -1) {
				$has_error = 1;
			}
			break;
		}
	}
}

if (!$has_installed && !$has_updated) {
	if (!$has_error) {
		$app->enqueueMessage(JText::_('NNI_SOMETHING_HAS_GONE_WRONG_DURING_INSTALLATION_OF_THE_DATABASE_RECORDS'), 'error');
	}
	uninstallInstaller();
}

if (!installFiles($comp_folder . '/extensions')) {
	$app->enqueueMessage(JText::_('NNI_COULD_NOT_COPY_ALL_FILES'), 'error error_nonumber');
	uninstallInstaller();
}

if (!JV15 && !empty($ids)) {
	$installer = JInstaller::getInstance();
	foreach ($ids as $id) {
		$installer->refreshManifestCache((int) $id);
	}
}

$txt_installed = ($has_installed) ? JText::_('NNI_INSTALLED') : '';
$txt_installed .= ($has_installed && $has_updated) ? ' / ' : '';
$txt_installed .= ($has_updated) ? JText::_('NNI_UPDATED') : '';
$app->set('_messageQueue', '');
$app->enqueueMessage(sprintf(JText::_('NNI_THE_EXTENSION_HAS_BEEN_INSTALLED_SUCCESSFULLY'), JText::_($ext), $txt_installed), 'message');
$app->enqueueMessage(JText::_('NNI_PLEASE_CLEAR_YOUR_BROWSERS_CACHE'), 'notice');

installFramework($comp_folder);

uninstallInstaller();

/* FUNCTIONS */

/**
 * Copies language files to the language folders
 */
function installLanguages($folder, $force = 1, $all = 1, $break = 1)
{
	if (JFolder::exists($folder . '/admin')) {
		$path = JPATH_ADMINISTRATOR . '/language';
		if (!installLanguagesByPath($folder . '/admin', $path, $force, $all, $break) && $break) {
			return 0;
		}
	}
	if (JFolder::exists($folder . '/site')) {
		$path = JPATH_SITE . '/language';
		if (!installLanguagesByPath($folder . '/site', $path, $force, $all, $break) && $break) {
			return 0;
		}
	}
	return 1;
}

/**
 * Removes language files from the language admin folders by filter
 */
function uninstallLanguages($filter)
{
	$languages = JFolder::folders(JPATH_ADMINISTRATOR . '/language');
	foreach ($languages as $lang) {
		$files = JFolder::files(JPATH_ADMINISTRATOR . '/language/' . $lang, $filter);
		foreach ($files as $file) {
			JFile::delete(JPATH_ADMINISTRATOR . '/language/' . $lang . '/' . $file);
		}
	}
}

/**
 * Copies all files from install folder
 */
function copy_from_folder($folder, $force = 0)
{
	if (!is_dir($folder)) {
		return 0;
	}

	// Copy files
	$folders = JFolder::folders($folder);

	$success = 1;

	foreach ($folders as $subfolder) {
		$dest = JPATH_SITE . '/' . $subfolder;
		$dest = str_replace(JPATH_SITE . '/plugins', JPATH_PLUGINS, $dest);
		$dest = str_replace(JPATH_SITE . '/administrator', JPATH_ADMINISTRATOR, $dest);
		if (!folder_copy($folder . '/' . $subfolder, $dest, $force)) {
			$success = 0;
		}
	}

	return $success;
}

/**
 * Copy a folder
 */
function folder_copy($src, $dest, $force = 0)
{
	$app = JFactory::getApplication();

	// Initialize variables
	jimport('joomla.client.helper');
	$ftpOptions = JClientHelper::getCredentials('ftp');

	// Eliminate trailing directory separators, if any
	$src = rtrim(str_replace('\\', '/', $src), '/');
	$dest = rtrim(str_replace('\\', '/', $dest), '/');

	if (!JFolder::exists($src)) {
		return 0;
	}

	$success = 1;

	// Make sure the destination exists
	if (!JFolder::exists($dest) && !folder_create($dest)) {
		$folder = str_replace(JPATH_ROOT, '', $dest);
		$app->enqueueMessage(JText::_('NNI_FAILED_TO_CREATE_DIRECTORY') . ': ' . $folder, 'error error_folders');
		$success = 0;
	}

	if (!($dh = @opendir($src))) {
		return 0;
	}

	$folders = array();
	$files = array();
	while (($file = readdir($dh)) !== false) {
		if ($file != '.' && $file != '..') {
			$file_src = $src . '/' . $file;
			switch (filetype($file_src)) {
				case 'dir':
					$folders[] = $file;
					break;
				case 'file':
					$files[] = $file;
					break;
			}
		}
	}
	sort($folders);
	sort($files);

	$curr_folder = array_pop(explode('/', $src));
	// Walk through the directory recursing into folders
	foreach ($folders as $folder) {
		$folder_src = $src . '/' . $folder;
		$folder_dest = $dest . '/' . $folder;
		if (!($curr_folder == 'language' && !JFolder::exists($folder_dest))) {
			if (!folder_copy($folder_src, $folder_dest, $force)) {
				$success = 0;
			}
		}
	}

	if ($ftpOptions['enabled'] == 1) {
		// Connect the FTP client
		jimport('joomla.client.ftp');
		$ftp = JFTP::getInstance(
			$ftpOptions['host'], $ftpOptions['port'], null,
			$ftpOptions['user'], $ftpOptions['pass']
		);

		// Walk through the directory copying files
		foreach ($files as $file) {
			$file_src = $src . '/' . $file;
			$file_dest = $dest . '/' . $file;
			// Translate path for the FTP account
			$file_dest = JPath::clean(str_replace(str_replace('\\', '/', JPATH_ROOT), $ftpOptions['root'], $file_dest), '/');
			if ($force || !JFile::exists($file_dest)) {
				if (!$ftp->store($file_src, $file_dest)) {
					$file_path = str_replace($ftpOptions['root'], '', $file_dest);
					$app->enqueueMessage(JText::_('NNI_ERROR_SAVING_FILE') . ': ' . $file_path, 'error error_files');
					$success = 0;
				}
			}
		}
	} else {
		foreach ($files as $file) {
			$file_src = $src . '/' . $file;
			$file_dest = $dest . '/' . $file;
			if ($force || !JFile::exists($file_dest)) {
				if (!@copy($file_src, $file_dest)) {
					$file_path = str_replace(JPATH_ROOT, '', $file_dest);
					$app->enqueueMessage(JText::_('NNI_ERROR_SAVING_FILE') . ': ' . $file_path, 'error error_files');
					$success = 0;
				}
			}
		}
	}

	return $success;
}

/**
 * Create a folder
 */
function folder_create($path = '', $mode = 0755)
{
	// Initialize variables
	jimport('joomla.client.helper');
	$ftpOptions = JClientHelper::getCredentials('ftp');

	// Check to make sure the path valid and clean
	$path = JPath::clean($path);

	// Check if dir already exists
	if (JFolder::exists($path)) {
		return true;
	}

	// Check for safe mode
	if ($ftpOptions['enabled'] == 1) {
		// Connect the FTP client
		jimport('joomla.client.ftp');
		$ftp = JFTP::getInstance(
			$ftpOptions['host'], $ftpOptions['port'], null,
			$ftpOptions['user'], $ftpOptions['pass']
		);

		// Translate path to FTP path
		$path = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $path), '/');
		$ret = $ftp->mkdir($path);
		$ftp->chmod($path, $mode);
	} else {
		// We need to get and explode the open_basedir paths
		$obd = ini_get('open_basedir');

		// If open_basedir is set we need to get the open_basedir that the path is in
		if ($obd != null) {
			if (JPATH_ISWIN) {
				$obdSeparator = ";";
			} else {
				$obdSeparator = ":";
			}
			// Create the array of open_basedir paths
			$obdArray = explode($obdSeparator, $obd);
			$inBaseDir = false;
			// Iterate through open_basedir paths looking for a match
			foreach ($obdArray as $test) {
				$test = JPath::clean($test);
				if (strpos($path, $test) === 0) {
					$inBaseDir = true;
					break;
				}
			}
			if ($inBaseDir == false) {
				// Return false for JFolder::create because the path to be created is not in open_basedir
				JError::raiseWarning(
					'SOME_ERROR_CODE',
					'JFolder::create: ' . JText::_('NNI_PATH_NOT_IN_OPEN_BASEDIR_PATHS')
				);
				return false;
			}
		}

		// First set umask
		$origmask = @umask(0);

		// Create the path
		if (!$ret = @mkdir($path, $mode)) {
			@umask($origmask);
			return false;
		}

		// Reset umask
		@umask($origmask);
	}

	return $ret;
}

function getXMLVersion($file = '', $alias = '', $type = '', $folder = 'system')
{
	if (!$file) {
		if (!$alias || !$type) {
			return 0;
		}
		switch ($type) {
			case 'component':
				if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_' . $alias . '/' . $alias . '.xml')) {
					$file = JPATH_ADMINISTRATOR . '/components/com_' . $alias . '/' . $alias . '.xml';
				} else if (JFile::exists(JPATH_SITE . '/components/com_' . $alias . '/' . $alias . '.xml')) {
					$file = JPATH_SITE . '/components/com_' . $alias . '/' . $alias . '.xml';
				} else if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_' . $alias . '/com_' . $alias . '.xml')) {
					$file = JPATH_ADMINISTRATOR . '/components/com_' . $alias . '/com_' . $alias . '.xml';
				} else if (JFile::exists(JPATH_SITE . '/components/com_' . $alias . '/com_' . $alias . '.xml')) {
					$file = JPATH_SITE . '/components/com_' . $alias . '/com_' . $alias . '.xml';
				}
				break;
			case 'plugin':
				if (JFile::exists(JPATH_PLUGINS . '/' . $folder . '/' . $alias . '/' . $alias . '.xml')) {
					$file = JPATH_PLUGINS . '/' . $folder . '/' . $alias . '/' . $alias . '.xml';
				} else if (JFile::exists(JPATH_PLUGINS . '/' . $folder . '/' . $alias . '.xml')) {
					$file = JPATH_PLUGINS . '/' . $folder . '/' . $alias . '.xml';
				}
				break;
			case 'module':
				if (JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $alias . '/' . $alias . '.xml')) {
					$file = JPATH_ADMINISTRATOR . '/modules/mod_' . $alias . '/' . $alias . '.xml';
				} else if (JFile::exists(JPATH_SITE . '/modules/mod_' . $alias . '/' . $alias . '.xml')) {
					$file = JPATH_SITE . '/modules/mod_' . $alias . '/' . $alias . '.xml';
				} else if (JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $alias . '/mod_' . $alias . '.xml')) {
					$file = JPATH_ADMINISTRATOR . '/modules/mod_' . $alias . '/mod_' . $alias . '.xml';
				} else if (JFile::exists(JPATH_SITE . '/modules/mod_' . $alias . '/mod_' . $alias . '.xml')) {
					$file = JPATH_SITE . '/modules/mod_' . $alias . '/mod_' . $alias . '.xml';
				}
				break;
		}
	}

	if (!$file || !JFile::exists($file)) {
		return 0;
	}

	$xml = JApplicationHelper::parseXMLInstallFile($file);

	if (!$xml || !isset($xml['version'])) {
		return 0;
	}
	return $xml['version'];
}