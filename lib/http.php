<?php
/**
 * Initializes the network suit
 * This core package can be used as an isolate unit from Suitcase. Just apply
 * your own autoloading functionality if you will.
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Suitcase
 * @subpackage http
 */
namespace {
  
  # register default autoload functionality  
  spl_autoload_register(function($class) {
    $underscored_class = preg_replace('/(\p{Ll})(\p{Lu})/', '$1_$2', $class);
    $file = __DIR__."/".str_replace('\\', '/', strtolower($underscored_class)).".php";
    
    if(file_exists($file)) require $file;
  });
  
  # import helper functions
  require_once __DIR__.'/http/functions.php';
}
?>