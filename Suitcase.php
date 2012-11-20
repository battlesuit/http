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
namespace suitcase {
  autoload_in('http', __DIR__."/lib/http");
  
  # import helper functions
  require_once __DIR__.'/lib/http/functions.php';
}
?>