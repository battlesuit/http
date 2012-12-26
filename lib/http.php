<?php
/**
 * Initializes the http bundle
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
namespace {
  # import helper functions
  if(defined('loader\available')) require __DIR__."/http/autoload.php";
  require __DIR__."/http/functions.php";
  require __DIR__."/http/transaction/functions.php";
}
?>