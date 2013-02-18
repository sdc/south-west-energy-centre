<?php
/**
 * @version   $Id: break.php 4590 2012-10-27 02:19:03Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_PLATFORM') or die;


/**
 *
 */
class JFormFieldBreak extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Break';

	/**
	 * @return string
	 */
	protected function getLabel()
	{
		$doc     = JFactory::getDocument();
		$version = new JVersion();
		$doc->addStyleDeclaration(".rok-break {border-bottom:1px solid #eee;font-size:16px;color:#0088CC;margin-top:15px;padding:2px 0;width:100%}");

		if (isset($this->element['label']) && !empty($this->element['label'])) {
			$label   = JText::_((string)$this->element['label']);
			$css     = (string)$this->element['class'];
			$version = new JVersion();
			if (version_compare($version->getShortVersion(), '3.0', '>=')) {
				return '<div class="rok-break ' . $css . '">' . $label . '</div>';
			} else {
				return '<label class="rok-break ' . $css . '">' . $label . '</label>';
			}
		} else {
			return;
		}
	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
		return;
	}

}