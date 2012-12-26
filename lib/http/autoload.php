<?php
namespace loader {
  
  /**
   * All the autoloading is done here
   * This function is getting called by the loader\Bundles::autoload
   *
   */
  
  scope('http', __DIR__."/..");
}
?>