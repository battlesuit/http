<?php
namespace http;

class Env implements \ArrayAccess, \Iterator {
  
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
  public $request = array();
  
  function __construct(array $server = array(), array $cookie = array(), array $session = array()) {
    $this->server($server);
    $this->cookie($cookie);
    $this->session($session);
    $this->parse();
  }
  
  function server(array $server) {    
    $this->attributes = array_merge($this->attributes, static::$defaults, $server);
  }
  
  function cookie(array $cookie) {
    $this->attributes['cookies'] = $cookie;
  }
  
  function session(array $session) {
    $this->attributes['session'] = $session;
  }
  
  protected function parse() {
    $sapi = php_sapi_name();
    
    if($sapi === 'cli') {
      $path = null;
    } else {
      $path_info = isset($this['PATH_INFO']) ? $this['PATH_INFO'] : null;
      $path = $this['SCRIPT_NAME'].$path_info;
    }
    
    $scheme = (isset($this['HTTPS']) and $this['HTTPS'] == 'on') ? 'https' : 'http';
    $host = $this['HTTP_HOST'];
    $port = $this['SERVER_PORT'];
    
    $query_string = !empty($this['QUERY_STRING']) ? $this['QUERY_STRING'] : null;
    
    if(!empty($query_string)) {
      $path = "$path?$query_string";
    }
    
    $header = array();
    foreach($this as $name => $value) {
      if(strpos($name, 'HTTP_') === 0) $header[strtolower(substr($name, 5))] = $value;
    }
    
    $input = array();
    $http_input = @file_get_contents('php://input');
    if(isset($http_input)) {
      parse_str($http_input, $input);
    }

    $this->request = array($this['REQUEST_METHOD'], "$scheme://$host:$port$path", $input, $header);
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
   * Returns the attributes array
   *
   * @access public
   * @return array
   */
  function to_array() {
    return $this->attributes;
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