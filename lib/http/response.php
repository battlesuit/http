<?php
namespace http;

/**
 * Standard http response message with some required helpers
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Response extends Message {
  
  /**
   * List of statuscodes associated with the reason phrase
   *
   * @static
   * @access public
   * @var array
   */
  static $reason_phrases = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
  );
  
  /**
   * Given status code
   *
   * @access public
   * @var int
   */
  public $status = 200;
  
  /**
   * Protocol to use
   *
   * @access public
   * @var string
   */
  public $protocol = 'HTTP/1.0';
  
  /**
   * Stored response body content written by body() method or constructor
   *
   * @access private
   * @var array
   */
  private $body = array();
  
  /**
   * Constructs a new response instance
   * Writes a default content_type = text/plain
   *
   * @access public
   * @param int $status
   * @param mixed $body
   * @param array $fields Headerfields
   */
  function __construct($status = 200, $body = null, array $fields = array()) {
    $this->status = (int)$status;
    $this->body($body);
    $this->fields($fields);
  }
  
  function status_line() {
    $code = $this->status;
    $info = array_key_exists($code, static::$reason_phrases) ? static::$reason_phrases[$code] : '';
    
    return "$this->protocol $code $info";
  }
  
  /**
   * Reads and writes the response body array
   *
   * @access public
   * @param mixed $content
   * @return array
   */
  function body($content = null) {
    if(isset($content)) $this->body = (array)$content;
    return $this->body;
  }
  
  /**
   * Writes a line to the body
   *
   * @access public
   * @param mixed $line_or_lines
   */
  function write($line_or_lines) {
    if(is_array($line_or_lines)) {
      $this->body = array_merge($this->body, $line_or_lines);
    } else $this->body[] = $line_or_lines;
  }
  
  /**
   * Returns a flat body string
   *
   * @access public
   * @return string
   */
  function flat_body() {
    return implode('', $this->body);
  }
  
  /**
   * Read and write location field
   *
   * @access public
   * @param string $url
   * @return string
   */
  function location($url = null) {
    if(isset($url)) return $this['location'] = $url;
    return $this['location'];
  }
  
  /**
   * To-array conversion returns the an array with [0] => status, [1] => headerfields, [2] => body
   *
   * @access public
   * @return array
   */  
  function to_array() {
    return array($this->status, $this->fields(), $this->body());
  }
  
  /**
   * To-string conversion returns the response body
   *
   * @access public
   * @return string
   */
  function __toString() {
    return $this->flat_body();
  }
}
?>