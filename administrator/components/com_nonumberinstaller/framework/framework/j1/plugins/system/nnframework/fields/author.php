<?php
/**
 * Element: Author
 * Displays a selectbox of authors
 *
 * @package         NoNumber Framework
 * @version         12.10.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class nnFieldAuthor
{
	function getInput($name, $id, $value)
	{
		return JHtml::_('list.users', $name . '', $value, 1);
	}
}

class JElementNN_Author extends JElement
{
	var $_name = 'Author';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_nnfield = new nnFieldAuthor;
		return $this->_nnfield->getInput($control_name . '[' . $name . ']', $control_name . $name, $value);
	}
}
