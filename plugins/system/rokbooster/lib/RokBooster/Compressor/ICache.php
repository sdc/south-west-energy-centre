<?php
/**
 * @version   $Id: ICache.php 4883 2012-11-01 02:31:37Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

interface RokBooster_Compressor_ICache
{
    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function isCacheExpired($checksum);

    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function doesCacheExist($checksum);

	/**
	 * @abstract
	 *
	 * @param        $checksum
	 * @param        $cont
	 * @param bool   $addheaders
	 *
	 * @param string $mimetype
	 *
	 * @internal param string $type
	 * @return mixed
	 */
    public function write($checksum, $cont, $addheaders = true, $mimetype ='application/x-javascript', $user_datafile = false);

    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function getCacheUrl($checksum);

    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function getCacheContent($checksum);


	/**
	 * @abstract
	 *
	 * @param $checksum
	 *
	 * @return mixed
	 */
	public function setCacheAsValid($checksum);
}
