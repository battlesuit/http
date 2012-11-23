<?php
/**
 * Initializes the network suit
 * This core package can be used as an isolate unit from Battlesuit. Just apply
 * your own autoloading functionality if you will.
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
namespace bs {
  autoload_in('http', dirname(__DIR__)."/lib/http");
  
  # import helper functions
  require_once dirname(__DIR__).'/lib/http/functions.php';
}
?>