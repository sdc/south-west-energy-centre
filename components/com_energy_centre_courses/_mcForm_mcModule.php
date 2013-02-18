<?php
  jimport('joomla.application.module.helper');
    // this is where you want to load your module position
    $modules = JModuleHelper::getModules('McForm_McModule');
    foreach($modules as $module)
    {
    echo '<div id="rt-sidebar-a">';
      echo '<div class="rt-block request_form">';
        echo JModuleHelper::renderModule($module);
      echo '</div>';
    echo '</div>';
  }
?>
