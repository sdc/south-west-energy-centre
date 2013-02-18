<?php
/**
 * @version   $Id: StaticFile.php 7149 2013-02-01 23:03:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_Cache_StaticFile implements RokBooster_Compressor_ICache
{
	/**
	 * @var object
	 */
	protected $options;

	/**
	 * @param $checksum
	 *
	 * @return bool
	 */
	public function isCacheExpired($checksum)
	{
		//see if file is stale
		$expired    = true;
		$cache_file = $this->options->cache_path . $checksum . '.php';
		if (file_exists($cache_file)) {
			$expired = ((int)strtotime('now') > ((int)filectime($cache_file) + (int)$this->options->cache_time)) ? true : false;
		}
		return $expired;
	}

	/**
	 * @param $checksum
	 *
	 * @return bool
	 */
	public function doesCacheExist($checksum)
	{
		if (file_exists($this->options->cache_path . $checksum . '.php')) {
			return true;
		}
		return false;
	}

	/**
	 * @param $checksum
	 *
	 * @return string
	 */
	public function getCacheUrl($checksum)
	{
		return $this->options->cache_url . $checksum . '.php';
	}

	/**
	 *
	 * @param $checksum
	 *
	 * @return mixed
	 */
	public function setCacheAsValid($checksum)
	{
		$cache_file = $this->options->cache_path . $checksum . '.php';
		if (file_exists($cache_file)) {
			touch($cache_file);
		}
	}


	/**
	 * @param $checksum
	 *
	 * @return bool|string
	 */
	public function getCacheContent($checksum)
	{
		$cache_file = $this->options->cache_path . $checksum . '.php';
		if (file_exists($cache_file)) {

			return file_get_contents($cache_file);
		}
		return '';
	}


	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		$this->options = $options;
	}


	/**
	 * @return bool
	 */
	protected function isGzipEnabled()
	{
		//override param if gzip not available
		if ($this->options->use_gzip) {
			if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
				return false;
			}

			$encoding = false;

			if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				$encoding = 'gzip';
			}

			if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
				$encoding = 'x-gzip';
			}

			if (!$encoding) {
				return false;
			}

			if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
				return false;
			}

			return $encoding;
		}
		return false;
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	protected function getOutHeader($type)
	{
		$cache_time = $this->options->cache_time;
		$exp        = '"Expires: ' . gmdate("D, d M Y H:i:s", time() + (60 * $cache_time)) . ' GMT"';

		$encoding = $this->isGzipEnabled();
		$header   = '<?php ';
		if ($encoding !== false) {
			$header .= 'ob_start ("ob_gzhandler"); ';
		}
		$header .= 'header("Content-type: ' . $type . '; charset= UTF-8"); ';
		if ($encoding !== false) {
			$header .= 'header("Cache-Control: must-revalidate"); ';
			$header .= 'header(' . $exp . '); ';
			$header .= 'header("X-Content-Encoded-By: RokBooster"); ';
		}
		$header .= '?>';
		return $header;
	}

	/**
	 * @param       $checksum
	 * @param       $cont
	 * @param       $mimetype
	 *
	 * @param bool  $addheaders
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function write($checksum, $cont, $addheaders = true, $mimetype = 'application/x-javascript', $use_datafile = false)
	{
		$final_file      = $checksum . '.php';
		$final_data_file = $checksum . '_data.php';

		$output      = '';
		$data_output = '';
		$files = array();
		if ($addheaders) {
			$output .= $this->getOutHeader($mimetype, strlen($cont));
			$output .= "\n\n/*** " . $checksum . " ***/\n\n";
		}

		if ($use_datafile){
			$output .= '<?php echo file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.\'' . $final_data_file . '\');?>';
			$files[$final_data_file]=  $cont;
		} else {
			$output .= $cont;
		}
		$files[$final_file]=  $output;

		foreach ($files as $file_name => $file_contents) {
			if (!empty($file_contents)) {
            $dir = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $this->options->cache_path);
            $file_working = $dir . $file_name.'_working';

            if (!is_dir($dir))  @(mkdir($dir, 0775));

				if (($fh = @fopen($file_working, 'w')) === false) {
					die("can't open file");
				}

            umask(002);

				if (!is_writable($file_working)) {
					@chmod($dir . $file_name . '_working', 0664);
				}


			if (fwrite($fh, $file_contents)) {
				fclose($fh);
			} else {
				fclose($fh);
					throw new Exception("Can not write to file: `" . $file_working . "`");
			}

			if (file_exists($final_file)) {
				unlink($final_file);
			}
			rename($this->options->cache_path . $file_name.'_working', $this->options->cache_path . $file_name);
		}
		}
		return true;
	}

	/**
	 * @param RokBooster_Compressor_FileGroup $filegroup
	 */
	public function writeScriptFile(RokBooster_Compressor_FileGroup $filegroup)
	{
		$this->write($filegroup->getChecksum(), $filegroup->getResult(), true, 'application/x-javascript', true);
	}

	/**
	 * @param RokBooster_Compressor_InlineGroup $inlinegroup
	 */
	public function writeInlineScriptFile(RokBooster_Compressor_InlineGroup $inlinegroup)
	{
		$this->write($inlinegroup->getChecksum(), $inlinegroup->getResult(), false);
	}

	/**
	 * @param RokBooster_Compressor_FileGroup $filegroup
	 */
	public function writeStyleFile(RokBooster_Compressor_FileGroup $filegroup)
	{
		$this->write($filegroup->getChecksum(), $filegroup->getResult(), true, 'text/css', true);
	}

	/**
	 * @param RokBooster_Compressor_InlineGroup $inlinegroup
	 */
	public function writeInlineStyleFile(RokBooster_Compressor_InlineGroup $inlinegroup)
	{
		$this->write($inlinegroup->getChecksum(), $inlinegroup->getResult(), false);
	}

//	protected function fopen_recursive($path, $mode, $chmod = 0755)
//	{
//		preg_match('`^(.+)/([a-zA-Z0-9]+\.[a-z]+)$`i', $path, $matches);
//		$directory = $matches[1];
//		$file      = $matches[2];
//
//		if (!is_dir($directory)) {
//			if (!mkdir($directory, $chmod, 1)) {
//				return FALSE;
//			}
//		}
//		return fopen($path, $mode);
//	}
}
