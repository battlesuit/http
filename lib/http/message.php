<?php
namespace http;

/**
 * Common abstraction of an http message e.g. Request or Response
 * This class provides headerfield iteration and accessor functionality
 * Implements shortcuts like content_type()
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Suitcase
 * @subpackage Net
 */
abstract class Message extends Object implements \ArrayAccess, \Iterator {
  
  /**
   * Format/Mime mapping definitions
   *
   * @static
   * @access public
   * @var array
   */ 
  static $formats = array(
    'all' => '*/*',
    'txt' => 'text/plain',
    'js' => array('application/javascript', 'application/x-javascript', 'text/javascript'),
    'css' => 'text/css',
    'json' => array('application/json', 'application/x-json'),
    'xml' => array('text/xml', 'application/xml', 'application/x-xml'),
    'rdf' => 'application/rdf+xml',
    'atom' => 'application/atom+xml',
    'html' => 'text/html'
  );
  
  /**
   * List of headerfields
   *
   * @access protected
   * @var array
   */
  protected $fields = array();
  
  /**
   * Reads all headerfields
   *
   * @access public
   * @return array
   */
  function fields(array $fields = null) {
    if(isset($fields)) foreach($fields as $name => $value) $this->write_field($name, $value);
    return $this->fields;
  }
  
  /**
   * Reads or writes a specific headerfield
   *
   * @access public
   * @param string $name
   * @param string $value
   * @return string
   */
  function field($name, $value = null) {
    if(isset($value)) return $this->write_field($name, $value);
    return $this->read_field($name);
  }
  
  /**
   * Does a given headerfield exist?
   * 
   * @access public
   * @param string $name
   * @return boolean 
   */
  function field_exists($name) {
    return array_key_exists($name, $this->fields);
  }
  
  /**
   * Are there any fields?
   * 
   * @access public
   * @return boolean 
   */
  function any_fields() {
    return count($this->fields) > 0;
  }
  
  /**
   * Writes a headerfield
   *
   * @access public
   * @param string $name
   * @param string $value
   * @return string
   */
  function write_field($name, $value) {
    return $this->fields[str_replace('-', '_', strtolower($name))] = $value;
  }
  
  /**
   * Reads a headerfield
   *
   * @access public
   * @param string $name
   * @return string
   */
  function read_field($name) {
    return $this->fields[$name];
  }
  
  /**
   * Reads or writes the content mime type
   *
   * @access public
   * @param string $mime
   * @param string $charset with utf8 as default
   * @return string
   */
  function content_type($mime = null, $charset = 'utf8') {
    if(isset($mime)) return $this->write_field('content_type', "$mime; charset=$charset");
    return $this->field('content_type');
  }
 
  /**
   * Iterator::rewind() implementation
   * Initializes the iteration process
   *
   * @access public
   */
  function rewind() {
    reset($this->fields);
  }

  /**
   * Iterator::current() implementation
   * Returns the current pointers value
   *
   * @access public
   * @return string
   */
  function current() {
    return current($this->fields);
  }

  /**
   * Iterator::key() implementation
   * Returns the current pointers key
   *
   * @access public
   * @return string
   */
  function key() {
    return key($this->fields);
  }

  /**
   * Iterator::next() implementation
   * After the loop body of each iteration is processed this method is called
   * Afterwards the process jumps to valid() etc. etc.
   *
   * @access public
   */
  function next() {
    next($this->fields);
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
    return key($this->fields) !== null;
  }
  
  /**
   * ArrayAccess::offsetSet() implementation
   *
   * @access public
   * @param string $field_name
   * @param mixed $value
   */
  function offsetSet($field_name, $value) {
    $this->write_field($field_name, $value);
  }

  /**
   * ArrayAccess::offsetUnset() implementation
   *
   * @access public
   * @param string $field_name
   */
  function offsetUnset($field_name) {
    unset($this->fields[$field_name]);
  }

  /**
   * ArrayAccess::offsetGet() implementation
   *
   * @access public
   * @param string $field_name
   * @return string
   */
  function offsetGet($field_name) {
    return $this->field($field_name);
  }

  /**
   * ArrayAccess::offsetExists() implementation
   *
   * @access public
   * @param string $field_name
   * @return boolean
   */
  function offsetExists($field_name) {
    return $this->field_exists($field_name, $this->fields);
  }
}
?>