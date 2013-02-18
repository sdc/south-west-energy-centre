<?php

/**

* @package Access-Manager (com_accessmanager)

* @version 1.3.0

* @copyright Copyright (C) 2012 Carsten Engel. All rights reserved.

* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 

* @author http://www.pages-and-items.com

*/


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class accessmanagerViewNoaccess extends JView{

	function display($tpl = null){
		
		parent::display($tpl);
	}
}
?>