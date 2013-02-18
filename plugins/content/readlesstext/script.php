<?php
/**
 * @package readlesstext
 * @copyright 2008-2011 Parvus
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/userport/
 * @author Parvus
 *
 * readless is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * readless is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with readless. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @version $Id$
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentReadLessTextInstallerScript
{
  /**
   * Constructor
   * @param JAdapterInstance $adapter The object responsible for running this script1
   */
  public function __constructor(JAdapterInstance $adapter)
  {
    //void
  }

  /**
   * Called before any type of action
   * @param string $route Which action is happening (install|uninstall|discover_install)
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function preflight($route, JAdapterInstance $adapter)
  {
    return true;
  }

  /**
   * Called after any type of action
   * @param string $route Which action is happening (install|uninstall|discover_install)
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function postflight($route, JAdapterInstance $adapter)
  {
    return true;
  }

  /**
   * Called on installation
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function install(JAdapterInstance $adapter)
  {
    $this->_CreateTable();
    return true;
  }

  /**
   * Called on update
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function update(JAdapterInstance $adapter)
  {
    $this->_CreateTable();
    return true;
  }

  /**
   * Called on uninstallation
   * @param JAdapterInstance $adapter The object responsible for running this script
   */
  public function uninstall(JAdapterInstance $adapter)
  {
    $this->_DestroyTable();
    return true;
  }

  private function _CreateTable()
  {
    $db = JFactory::getDBO();
    $db->setQuery( self::_createTableSql );
    $db->query();
  }

  private function _DestroyTable()
  {
    $db = JFactory::getDBO();
    $db->setQuery( self::_destroyTableSql );
    $db->query();
  }

  const _createTableSql = "CREATE TABLE IF NOT EXISTS `#__readlesstext` (
    `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `rtable` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'The component name where the item resides in. Also called option',
    `rid` INT(10) NOT NULL COMMENT 'The unique id of the item in that component. e.g. the article id for com_content',
    `hash` VARCHAR(255) DEFAULT '' COMMENT 'The fingerprint of the full item text.',
    `char` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `word` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `sentence` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `paragraph` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `image_tag_start_pos` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Start position of the image tag where the thumbnail was created from.',
    `image_tag_length` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Length of the image tag in nr of UTF8 chars.',
    `image_url` VARCHAR(1023) NOT NULL DEFAULT '' COMMENT 'Url to the image where the thumbnail was created from.',
    `thumbnail_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Url to the thumbnail.',
    `last_update` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Debug information.',
    UNIQUE (`rtable`, `rid`) ) DEFAULT CHARSET=utf8;";

  const _destroyTableSql = "DROP TABLE `#__readlesstext`";
}

?>
