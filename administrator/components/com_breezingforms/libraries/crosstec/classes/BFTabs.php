<?php
/**
* BreezingForms - A Joomla Forms Application
* @version 1.7.3
* @package BreezingForms
* @copyright (C) 2008-2011 by Markus Bopp
* @license Released under the terms of the GNU General Public License
**/
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

JLoader::register('JPaneTabs', JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'pane.php');
class BFTabs extends JPaneTabs {
	var $useCookies = false;

	function __construct( $useCookies, $xhtml = null) {
		parent::__construct( array('useCookies' => $useCookies) );
	}

	function startTab( $tabText, $paneid ) {
		echo $this->startPanel( $tabText, $paneid);
	}

	function endTab() {
		echo $this->endPanel();
	}

	function startPane( $tabText ){
		echo parent::startPane( $tabText );
	}

	function endPane(){
		echo parent::endPane();
	}
}