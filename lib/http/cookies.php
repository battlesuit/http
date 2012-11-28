<?php
namespace http;

/**
 * Cookies collection helper
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Cookies implements \ArrayAccess, \Iterator {
  
  /**
   * Cookie variable collection
   *
   * @access private
   * @var array
   */
  private $collection = array();
  
  /**
   * Constructs a new cookies instance
   *
   * @access public
   * @param array $collection
   */
  function __construct(array $collection = array()) {
    $this->collection = $collection;
  }
  
  /**
   * Merges collection on destruction or script end 
   *
   * @access public
   */
  function __destruct() {
    if(!empty($this->collection)) static::merge($this->collection);
  }
  
  /**
   * Merges session attributes to the global array
   *
   * @static
   * @access public
   * @param array $collection
   */
  static function merge(array $collection) {
    $_COOKIE = array_merge($_COOKIE, $collection);
  }
  
  /**
   * Iterator::rewind() implementation
   * Initializes the iteration process
   *
   * @access public
   */
  function rewind() {
    reset($this->collection);
  }

  /**
   * Iterator::current() implementation
   * Returns the current pointers value
   *
   * @access public
   * @return string
   */
  function current() {
    return current($this->collection);
  }

  /**
   * Iterator::key() implementation
   * Returns the current pointers key
   *
   * @access public
   * @return string
   */
  function key() {
    return key($this->collection);
  }

  /**
   * Iterator::next() implementation
   * After the loop body of each iteration is processed this method is called
   * Afterwards the process jumps to valid() etc. etc.
   *
   * @access public
   */
  function next() {
    next($this->collection);
  }

  /**
   * Iterator::valid() implementation
   * Called before each iteration
   * If false is returned the loop instantly breaks
   * If true is returned current() and key() gets called afterwards
   *
   * @access public
   * @return boolean
   */
  function valid() {
    return key($this->collection) !== null;
  }
  
  /**
   * ArrayAccess::offsetSet() implementation
   *
   * @access public
   * @param string $name
   * @param mixed $value
   */
  function offsetSet($name, $value) {
    $this->collection[$name] = $value;
  }

  /**
   * ArrayAccess::offsetUnset() implementation
   *
   * @access public
   * @param string $name
   */
  function offsetUnset($name) {
    unset($this->collection[$name]);
  }

  /**
   * ArrayAccess::offsetGet() implementation
   *
   * @access public
   * @param string $name
   * @return string
   */
  function offsetGet($name) {
    return $this->collection[$name];
  }

  /**
   * ArrayAccess::offsetExists() implementation
   *
   * @access public
   * @param string $name
   * @return boolean
   */
  function offsetExists($name) {
    return array_key_exists($name, $this->collection);
  }
}
?>