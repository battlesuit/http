<?php
namespace http;

/**
 * Net package top-level class
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Object {
  
  /**
   * Calls to_string() method instead
   *
   * @final
   * @access public
   * @return string
   */
  final function __toString() {
    return $this->to_string();
  }
  
  /**
   * To-string conversion alias
   *
   * @access public
   * @return string
   */
  function to_string() {
    return "";
  }
}
?>