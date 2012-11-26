<?php
namespace http;

/**
 * Session helper
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Session extends Object implements \ArrayAccess, \Iterator {
  
  /**
   * Session attributes
   *
   * @access private
   * @var array
   */
  private $attributes = array();
  
  /**
   * Constructs a new session instance
   *
   * @access public
   * @param array $attributes
   */
  function __construct(array $attributes = array()) {
    $this->attributes = $attributes;
  }
  
  /**
   * Merges all attributes on destruction or script end 
   *
   * @access public
   */
  function __destruct() {
    if(session_id() !== "") static::merge($this->attributes);
  }
  
  /**
   * Merges session attributes to the global array
   *
   * @static
   * @access public
   * @param array $attributes
   */
  static function merge(array $attributes) {
    $_SESSION = array_merge($_SESSION, $attributes);
  }
  
  /**
   * Starts a new session
   * Instanciates and returns a session instance
   *
   * @static
   * @access public
   * @return Session
   */
  static function start() {
    session_start();
    $session = new static($_SESSION);
    return $session;
  }
  
  /**
   * Iterator::rewind() implementation
   * Initializes the iteration process
   *
   * @access public
   */
  function rewind() {
    reset($this->attributes);
  }

  /**
   * Iterator::current() implementation
   * Returns the current pointers value
   *
   * @access public
   * @return string
   */
  function current() {
    return current($this->attributes);
  }

  /**
   * Iterator::key() implementation
   * Returns the current pointers key
   *
   * @access public
   * @return string
   */
  function key() {
    return key($this->attributes);
  }

  /**
   * Iterator::next() implementation
   * After the loop body of each iteration is processed this method is called
   * Afterwards the process jumps to valid() etc. etc.
   *
   * @access public
   */
  function next() {
    next($this->attributes);
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
    return key($this->attributes) !== null;
  }
  
  /**
   * ArrayAccess::offsetSet() implementation
   *
   * @access public
   * @param string $field_name
   * @param mixed $value
   */
  function offsetSet($name, $value) {
    $this->attributes[$name] = $value;
  }

  /**
   * ArrayAccess::offsetUnset() implementation
   *
   * @access public
   * @param string $name
   */
  function offsetUnset($name) {
    unset($this->attributes[$name]);
  }

  /**
   * ArrayAccess::offsetGet() implementation
   *
   * @access public
   * @param string $name
   * @return string
   */
  function offsetGet($name) {
    return $this->attributes[$name];
  }

  /**
   * ArrayAccess::offsetExists() implementation
   *
   * @access public
   * @param string $name
   * @return boolean
   */
  function offsetExists($name) {
    return array_key_exists($name, $this->attributes);
  }
}
?>