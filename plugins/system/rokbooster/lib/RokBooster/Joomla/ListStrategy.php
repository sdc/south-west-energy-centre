<?php
/**
 * @version   $Id: ListStrategy.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Joomla_ListStrategy extends RokBooster_Joomla_AbstractStrategy
{


	/**
	 * @var phpQueryObject
	 */
	protected $document;

	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		parent::__construct($options);
	}


	/**
	 *
	 */
	public function identify()
	{
		if ($this->options->minify_js) {
			$this->identifyScriptFiles();
		}
		if ($this->options->minify_css) {
			$this->identifyStyleFiles();
		}
		if ($this->options->inline_js) {
			$this->identifyInlineScripts();
		}
		if ($this->options->inline_css) {
			$this->identifyInlineStyles();
		}

	}

	public function processForImages()
	{
		if ($this->options->convert_page_images) {
			$this->document         = phpQuery::newDocumentHTML(JResponse::getBody());
			$this->identifyImageSources();
			$this->processImages();
			$this->populateImages();
			$markup = ($this->document->getDocument()->htmlOuter());
			//TODO fix so that regex works with data uris?
			$markup = preg_replace('/(<(base|img|br|meta|area|input|link|col|hr|param|frame|isindex)+([\s]+[\S]+[\s]*=[\s]*("([^"]*)"|\'([^\']*)\'))*[\s]*)>/imx', '$1/>', $markup);
			JResponse::setBody($markup);
		}
	}


	/**
	 *
	 */
	protected function identifyScriptFiles()
	{
		$doc = JFactory::getDocument();

		foreach ($doc->_scripts as $filename => $fileinfo) {

			$file = new RokBooster_Compressor_File($filename, $this->options->root_url, $this->options->root_path);
			$file->setMime($fileinfo['mime']);
			$file->setType('js');
			if ($fileinfo['defer']) $file->addAttribute('defer', 'defer');
			if ($fileinfo['async']) $file->addAttribute('async', 'async');
			if (in_array($file->file, $this->options->ignored_files)) {
				$file->setIgnored(true);
			}
			$this->script_file_sorter->addFile($file);
		}

		$script_file_groups = $this->script_file_sorter->getGroups();
		foreach ($script_file_groups as &$file_group) {
			if ($file_group->getStatus() != RokBooster_Compressor_IGroup::STATE_IGNORE && $this->isCacheExpired($file_group->getChecksum()) && !$this->isBeingRendered($file_group->getChecksum())) {
				$this->render_script_file_groups[] = $file_group;
				$this->setCurrentlyRendering($file_group->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function identifyStyleFiles()
	{
		$doc = JFactory::getDocument();
		foreach ($doc->_styleSheets as $filename => $fileinfo) {
			$file = new RokBooster_Compressor_File($filename, $this->options->root_url, $this->options->root_path);
			$file->setType('css');
			$file->setMime($fileinfo['mime']);
			$file->setAttributes($fileinfo['attribs']);
			$file->addAttribute('media', $fileinfo['media']);
			if (in_array($file->file, $this->options->ignored_files)) {
				$file->setIgnored(true);
			}
			$this->style_file_sorter->addFile($file);
		}
		$file_groups = $this->style_file_sorter->getGroups();
		foreach ($file_groups as &$file_group) {
			if ($this->isCacheExpired($file_group->getChecksum()) && !$this->isBeingRendered($file_group->getChecksum())) {
				$this->render_style_file_groups[] = $file_group;
				$this->setCurrentlyRendering($file_group->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	protected function identifyInlineScripts()
	{
		$doc = JFactory::getDocument();
		foreach ($doc->_script as $mime => $content) {
			$inlineGroup = new RokBooster_Compressor_InlineGroup(RokBooster_Compressor_InlineGroup::STATE_INCLUDE, $mime);
			$inlineGroup->setContent($content);
			$this->inline_scripts[] = $inlineGroup;

			if ($this->isCacheExpired($inlineGroup->getChecksum()) && !$this->isBeingRendered($inlineGroup->getChecksum())) {
				$this->render_inline_scripts[] = $inlineGroup;
				$this->setCurrentlyRendering($inlineGroup->getChecksum());
			}
		}
	}


	/**
	 *
	 */
	protected function identifyInlineStyles()
	{
		$doc = JFactory::getDocument();
		foreach ($doc->_style as $mime => $content) {
			$inlineGroup = new RokBooster_Compressor_InlineGroup(RokBooster_Compressor_InlineGroup::STATE_INCLUDE, $mime);
			$inlineGroup->setContent($content);
			$this->inline_styles[] = $inlineGroup;
			$this->setCurrentlyRendering($inlineGroup->getChecksum());

			if ($this->isCacheExpired($inlineGroup->getChecksum()) && !$this->isBeingRendered($inlineGroup->getChecksum())) {
				$this->render_inline_styles[] = $inlineGroup;
				$this->setCurrentlyRendering($inlineGroup->getChecksum());
			}
		}
	}

	protected function identifyImageSources()
	{
		$doc = JFactory::getDocument();

		$image_links = pq('img[src]', $this->document);
		foreach ($image_links as $image) {
			$attribs = pq($image, $this->document)->attr('*');
			$src     = $attribs['src'];
			unset($attribs['src']);

			$file = new RokBooster_Compressor_File($src, $this->options->root_url, $this->options->root_path);
			$file->setAttributes($attribs);

			$ext = strtolower(pathinfo($file->getPath(), PATHINFO_EXTENSION));
			$file->setMime(RokBooster_Compressor_File::mime_content_type($file->getPath()));
			if (!$file->isExternal() && is_file($file->getPath())) {
				list(, , , , , , , $size, , $mtime, $ctime, ,) = @stat($file->getPath());
				if (!in_array($file->file, $this->options->ignored_files) && $size <= $this->options->max_data_uri_image_size) {
					$this->images[$file->getFile()] = $file;
					$group                          = new RokBooster_Compressor_FileGroup(RokBooster_Compressor_FileGroup::STATE_INCLUDE, $file->getMime());
					$group->addItem($file);
					$this->imageFileGroups[$file->getFile()] = $group;
				}
			}
		}

		foreach ($this->imageFileGroups as $image_file_group) {
			if ($image_file_group->getStatus() != RokBooster_Compressor_IGroup::STATE_IGNORE && $this->isCacheExpired($image_file_group->getChecksum()) && !$this->isBeingRendered($image_file_group->getChecksum())) {
				$this->encode_image_file_groups[] = $image_file_group;
				$this->setCurrentlyRendering($image_file_group->getChecksum());
			}
		}
	}

	/**
	 *
	 */
	public function populate()
	{
		if ($this->options->minify_js) {
			$this->populateScriptFiles();
		}
		if ($this->options->minify_css) {
			$this->populateStyleFiles();
		}
		if ($this->options->inline_js) {
			$this->populateInlineScripts();
		}
		if ($this->options->inline_css) {
			$this->populateInlineStyles();
		}
	}


	/**
	 *
	 */
	protected function populateScriptFiles()
	{
		$doc           = JFactory::getDocument();
		$doc->_scripts = array();
		foreach ($this->script_file_sorter->getGroups() as $group) {
			/** @var $group RokBooster_Compressor_FileGroup */
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				$doc->addScript($this->options->cache_url . $group->getChecksum() . '.php', $group->getMime(), array_key_exists('defer', $group->getAttributes()), array_key_exists('async', $group->getAttributes()));
			} else {
				foreach ($group as $file) {
					/** @var $file RokBooster_Compressor_File */
					$doc->addScript($file->getFile(), $file->getMime(), array_key_exists('defer', $file->getAttributes()), array_key_exists('async', $file->getAttributes()));
				}
			}
		}
	}

	/**
	 *
	 */
	protected function populateStyleFiles()
	{
		$doc               = JFactory::getDocument();
		$doc->_styleSheets = array();
		foreach ($this->style_file_sorter->getGroups() as $group) {
			/** @var $group RokBooster_Compressor_FileGroup */
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				$attribs = $group->getAttributes();
				$media   = $attribs['media'];
				unset($attribs['media']);
				$doc->addStyleSheet($this->options->cache_url . $group->getChecksum() . '.php', $group->getMime(), $media, $attribs);
			} else {
				foreach ($group as $file) {
					/** @var $file RokBooster_Compressor_File */
					$attribs = $file->getAttributes();
					$media   = null;
					if (array_key_exists('media', $attribs)) {
						$media = $attribs['media'];
						unset($attribs['media']);
					}
					$doc->addStyleSheet($file->getFile(), $group->getMime(), $media, $attribs);
				}
			}
		}
	}

	/**
	 *
	 */
	protected function populateInlineScripts()
	{

		$doc          = JFactory::getDocument();
		$doc->_script = array();
		foreach ($this->inline_scripts as $group) {
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				/** @var $group RokBooster_Compressor_InlineGroup */
				$doc->addScriptDeclaration(file_get_contents($this->options->cache_path . $group->getChecksum() . '.php', $group->getMime()));
			} else {
				$doc->addScriptDeclaration($group->getContent(), $group->getMime());
			}
		}
	}

	/**
	 *
	 */
	protected function populateInlineStyles()
	{
		$doc         = JFactory::getDocument();
		$doc->_style = array();
		foreach ($this->inline_styles as $group) {
			if ($group->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $this->doesCacheExist($group->getChecksum())) {
				/** @var $group RokBooster_Compressor_InlineGroup */
				$doc->addStyleDeclaration(file_get_contents($this->options->cache_path . $group->getChecksum() . '.php', $group->getMime()));
			} else {
				$doc->addStyleDeclaration($group->getContent(), $group->getMime());
			}
		}
	}

	protected function populateImages()
	{
		if ($this->options->convert_page_images) {
			$image_links = pq('img[src]', $this->document);
			foreach ($image_links as $image) {
				$attribs = pq($image, $this->document)->attr('*');
				$src     = $attribs['src'];
				if (array_key_exists($src, $this->imageFileGroups) && $this->cache->doesCacheExist($this->imageFileGroups[$src]->getChecksum())) {
					$image_file_group = $this->imageFileGroups[$src];
					/** @var $image_file RokBooster_Compressor_File */
					$image_file  = $image_file_group[0];
					$fileattribs = array();
					foreach ($image_file->getAttributes() as $attrib_key => $attrib_value) {
						$fileattribs[] = $attrib_key . '="' . $attrib_value . '"';
					}
					pq($image)->replaceWith(sprintf('<img src="data:%s;base64,%s" %s />', $image_file_group->getMime(), $this->cache->getCacheContent($image_file_group->getChecksum()), implode(' ', $fileattribs)));
				}
			}
		}
	}
}
