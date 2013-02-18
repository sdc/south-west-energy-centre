<?php
  jimport('joomla.application.module.helper');
    // this is where you want to load your module position
    $modules = JModuleHelper::getModules('McSlidy_McModule');
    foreach($modules as $module)
    {
      echo '<div class="rt-block">';
        echo JModuleHelper::renderModule($module);
      echo '</div>';
  }
?>
