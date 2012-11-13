<?php
namespace http\cgi;
use http\Object;
use http\Session;

class Env extends Object implements \ArrayAccess, \Iterator {
  
  /**
   * Attribute defaults
   *
   * @static
   * @access public
   * @var array
   */
  static $defaults = array(
    
    # request defaults
    'REQUEST_METHOD' => 'GET',
    
    # server defaults
    'SERVER_NAME' => 'localhost',
    'SERVER_PORT' => 80,
    'SERVER_ADDR' => '127.0.0.1',
    
    # header defaults
    'HTTP_HOST' => 'localhost',
    'HTTP_ACCEPT' => '*/*',
    'HTTP_ACCEPT_LANGUAGE' => 'en,de',
    'HTTP_ACCEPT_ENCODING' => 'gzip',
    'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8',
    'HTTP_CONNECTION' => 'keep-alive',
    'HTTP_CACHE_CONTROL' => 'max-age=0'
  );
  
  protected $attributes = array();
  static $fix_pathinfo = false;
  
  static function load() {
    $env = new static();
    $server = $_SERVER;
    
    if(static::$fix_pathinfo) {
      if(($pos = strpos($server['QUERY_STRING'], '&'))) {
        $server['PATH_INFO'] = substr($server['QUERY_STRING'], 0, $pos);
      } else $server['PATH_INFO'] = $server['QUERY_STRING'];
      
      if(($pos = strpos($server['REQUEST_URI'], '?'))) {
        $server['QUERY_STRING'] = substr($server['REQUEST_URI'], $pos+1);
      } else $server['QUERY_STRING'] = ""; 
    }
    
    if(empty($server['PATH_INFO'])) {
      $server['PATH_INFO'] = '/';
    }
    
    $env->attributes = $server + array(
      'http.errors' => @file_get_contents('php://stderr'),
      //'session' => Session::start(),
      'cookies' => $_COOKIE
    ) + static::$defaults;
    
    return $env;
  }

  /**
   * Reads the server name
   *
   * @access public
   * @return string
   */
  function server_name() {
    return $this['SERVER_NAME'];
  }
  
  /**
   * Reads the server port
   *
   * @access public
   * @return int
   */
  function server_port() {
    return (int)$this['SERVER_PORT'];
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