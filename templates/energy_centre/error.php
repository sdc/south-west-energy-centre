<?php
/**
* @version   $Id: error.php 2970 2012-08-31 22:45:34Z kevin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
if (!isset($this->error)) {
	$this->error = JError::raiseWarning( 403, JText::_('ALERTNOTAUTH') );
	$this->debug = false;
}

// load and inititialize gantry class
require_once('lib/gantry/gantry.php');
$gantry->init();

$doc = JFactory::getDocument();
$doc->setTitle($this->error->getCode() . ' - '.$this->title);

$gantry->addStyle('grid-responsive.css', 5);
$gantry->addLess('global.less', 'master.css', 8, array('headerstyle'=> '"header-' . $gantry->get('headerstyle', 'dark') . '.less"'));
if ($gantry->browser->name == 'ie') {
	if ($gantry->browser->shortversion == 8) {
		$gantry->addScript('html5shim.js');
	}
}
$gantry->addScript('rokmediaqueries.js');

ob_start();
?>
<body <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-top-surround">
		<div id="rt-header">
			<div class="rt-container">
				<?php echo $gantry->displayModules('header','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<div class="rt-container">
		<div class="component-content">
			<div class="rt-grid-12">
				<div class="rt-block">
					<!--<div class="rt-error-rocket"></div>-->
          <div class="swec-error-image">
             <img src="images/template-images/logo.gif" alt="" height="87" width="299"> 
          </div>
					<div class="rt-error-content">
						<h1 class="error-title title">Error: <span><?php echo $this->error->getCode(); ?></span> - <?php echo $this->error->getMessage(); ?> - <br /><br />This is not the page you are looking for!</h1>
						<div class="error-content">
						<ol>
							<li>an out-of-date bookmark/favourite</li>
							<li>a search engine that has an out-of-date listing for this site</li>
							<li>a mistyped address</li>
							<li>you have no access to this page</li>
							<li>The requested resource was not found.</li>
							<li>An error has occurred while processing your request.</li>
						</ol>
						<p><a href="<?php echo $gantry->baseUrl; ?>" class="readon"><span>&larr; Home</span></a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<?php

$body = ob_get_clean();
$gantry->finalize();

require_once(JPATH_LIBRARIES.'/joomla/document/html/renderer/head.php');
$header_renderer = new JDocumentRendererHead($doc);
$header_contents = $header_renderer->render(null);
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<?php echo $header_contents; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="templates/energy_centre/css/layout.css" />    
</head>
<?php
$header = ob_get_clean();
echo $header.$body;;
