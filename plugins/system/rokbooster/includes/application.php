<?php
/**
 * @version   $Id: application.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('JPATH_PLATFORM') or die;

class RTRenderer
{
	/**
	 * Display the application.
	 */
	public function render()
	{
		$user = JFactory::getUser();
		$conf = JFactory::getConfig();
		if ($user->id!=0) {
			if ($user->authorise('core.admin', 'com_cache')) {
				$file_cache = new JCache(array(
				                              'defaultgroup'   => 'rokbooster',
				                              'caching'        => true,
				                              'checkTime'      => true,
				                              'storage'        => 'file',
				                              'cachebase'      => JPATH_CACHE
				                         ));

				$file_info_cache = new JCache(array(
				                                   'defaultgroup'  => 'rokbooster',
				                                   'caching'       => true,
				                                   'checkTime'     => false,
				                              ));

				$generator_state_cache = new JCache(array(
									'cachebase' => $conf->get('cache_path', JPATH_CACHE),
									'lifetime' => 120,
									'storage' => $conf->get('cache_handler', 'file'),
									'defaultgroup' => 'rokbooster',
									'locking' => true,
									'locktime' => 15,
									'checkTime' => true,
									'caching' => true
									));
				$generator_state_cache->clean();
				$file_cache->clean();
				$file_info_cache->clean();

				$files  = $file_cache->getAll();
				$filecount = 0;
				if (is_array($files) && array_key_exists('rokbooster', $files)){
					$filecount = $files['rokbooster']->count;
				}
				echo sprintf('{"status":"success","message":"%d"}',$filecount);
			}
			else {
				echo '{"status": "error","message":"You do not have permissions to clear cache."}';
			}
		}
	}
}