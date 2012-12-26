<?php
/**
 * Loads testing environment
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
namespace loader {
  require "../loader.php";
  import('test', 'http');
  scope('http', __DIR__);
  scope('server', __DIR__);
}
?>