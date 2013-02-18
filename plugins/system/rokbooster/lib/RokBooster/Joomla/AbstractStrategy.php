<?php
/**
 * @version   $Id: AbstractStrategy.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
abstract class RokBooster_Joomla_AbstractStrategy extends RokBooster_Compressor_AbstractStrategy
{

	/**
	 *
	 */
	const CACHE_GROUP = 'rokbooster';
	/**
	 *
	 */
	const GENERATOR_STATE_TIMEOUT = 2;

	/**
	 * @var JCache
	 */
	protected $generator_state_cache;

	/**
	 * @var JCache
	 */
	protected $file_info_cache;

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $render_script_file_groups;

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $render_style_file_groups;

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $render_inline_scripts;

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $render_inline_styles;

	/**
	 * @var RokBooster_Compressor_File[]
	 */
	protected $images = array();

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $encode_image_file_groups = array();

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $imageFileGroups = array();



	/**
	 * @var RokBooster_Compressor_ICache
	 */
	protected $cache;


	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		parent::__construct($options);
		$conf = JFactory::getConfig();

		$generator_state_options = array(
			'cachebase'    => $conf->get('cache_path', JPATH_CACHE),
			'lifetime'     => self::GENERATOR_STATE_TIMEOUT,
			'storage'      => $conf->get('cache_handler', 'file'),
			'defaultgroup' => self::CACHE_GROUP,
			'locking'      => true,
			'locktime'     => 15,
			'checkTime'    => true,
			'caching'      => true
		);

		$this->generator_state_cache = new JCache($generator_state_options);

		$file_info_options     = array(
			'cachebase'    => $conf->get('cache_path', JPATH_CACHE),
			'storage'      => $conf->get('cache_handler', 'file'),
			'defaultgroup' => self::CACHE_GROUP,
			'locking'      => true,
			'locktime'     => 15,
			'checkTime'    => false,
			'caching'      => true
		);
		$this->file_info_cache = new JCache($file_info_options);

		$this->cache = new RokBooster_Compressor_Cache_StaticFile($this->options);
	}


	/**
	 * @param $checksum
	 *
	 * @return bool|mixed
	 */
	protected function doesCacheExist($checksum)
	{
		return $this->cache->doesCacheExist($checksum);
	}

	/**
	 * @param $checksum
	 *
	 * @return bool|mixed
	 */
	protected function isBeingRendered($checksum)
	{
		if (($rendering = $this->generator_state_cache->get($checksum))) {
			return $rendering;
		}
		return false;
	}

	/**
	 * @param $checksum
	 */
	protected function setCurrentlyRendering($checksum)
	{
		$this->generator_state_cache->store(true, $checksum);
	}

	/**
	 * @param $checksum
	 */
	protected function finishedRendering($checksum)
	{
		$this->generator_state_cache->remove($checksum);
	}


	/**
	 * @param $checksum
	 *
	 * @return bool|mixed
	 */
	protected function isCacheExpired($checksum)
	{
		if (!$this->cache->doesCacheExist($checksum)) {
			return true;
		}
		if (($expired = $this->cache->isCacheExpired($checksum))) {
			$files_changed = false;
			if (($file_group = $this->file_info_cache->get($checksum . '_fileinfo'))) {
				$file_group = unserialize($file_group);
				/** @var $file RokBooster_Compressor_File */
				foreach ($file_group as $file) {
					if (file_exists($file->getPath()) && is_readable($file->getPath())) {
						if ($file->hasChanged()) {
							$files_changed = true;
							break;
						}
					} else {
						$this->file_info_cache->remove($checksum . '_fileinfo');
						$files_changed = true;
						break;
					}
				}
			} else {
				$files_changed = true;
			}
			if (!$files_changed) {
				$this->cache->setCacheAsValid($checksum);
				return false;
			}
		}
		return $expired;
	}

	/**
	 * @param RokBooster_Compressor_FileGroup $group
	 */
	protected function storeFileInfo(RokBooster_Compressor_FileGroup $group)
	{
		$group->cleanup();
		$this->file_info_cache->store(serialize($group), $group->getChecksum() . '_fileinfo');
	}


	/**
	 *
	 */
	public function process()
	{
		if ($this->options->minify_js) {
			$this->processScripts();
		}
		if ($this->options->minify_css) {
			$this->processStyles();
		}
		if ($this->options->inline_js) {
			$this->processInlineScripts();

		}
		if ($this->options->inline_css) {
			$this->processInlineStyles();
		}
	}


	/**
	 *
	 */
	protected function processScripts()
	{
		if (isset($this->render_script_file_groups) && is_array($this->render_script_file_groups)) {
			foreach ($this->render_script_file_groups as $filegroup) {
				parent::processScriptFiles($filegroup);
				$this->cache->writeScriptFile($filegroup);
				$this->storeFileInfo($filegroup);
				$this->finishedRendering($filegroup->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function processInlineScripts()
	{
		if (isset($this->render_inline_scripts) && is_array($this->render_inline_scripts)) {
			foreach ($this->render_inline_scripts as $inlinegroup) {
				parent::processInlineScript($inlinegroup);
				$this->cache->writeInlineScriptFile($inlinegroup);
				$this->finishedRendering($inlinegroup->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function processStyles()
	{
		if (isset($this->render_style_file_groups) && is_array($this->render_style_file_groups)) {
			foreach ($this->render_style_file_groups as $filegroup) {
				parent::processStyleFiles($filegroup);
				$this->cache->writeStyleFile($filegroup);
				$this->storeFileInfo($filegroup);
				$this->finishedRendering($filegroup->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function processInlineStyles()
	{
		if (isset($this->render_inline_styles) && is_array($this->render_inline_styles)) {
			foreach ($this->render_inline_styles as $inlinegroup) {
				parent::processInlineStyle($inlinegroup);
				$this->cache->writeInlineStyleFile($inlinegroup);
				$this->finishedRendering($inlinegroup->getChecksum());
			}
		}
	}

	protected function processImages()
	{
		foreach ($this->encode_image_file_groups as $image_group) {
			/** @var $file RokBooster_Compressor_File */
			$file = $image_group[0];
			$image_group->setResult(base64_encode(file_get_contents($file->getPath())));
			$this->cache->write($image_group->getChecksum(), $image_group->getResult(), false);
			$this->storeFileInfo($image_group);
			$this->finishedRendering($image_group->getChecksum());
		}
	}

}
