<?php
namespace http;

/**
 * Establishes http connections
 *
 * Example:
 *  $con = new Connection('localhost', 80);
 *  $response = $con->get('/path/to/resource');
 *  echo $response;
 *  
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Connection {
  
  /**
   * Connection host name e.g. my.domain.de, localhost
   *
   * @access private
   * @var string
   */
  private $host;
  
  /**
   * Connection port
   * Default 80
   *
   * @access private
   * @var int
   */
  private $port;
  
  /**
   * Establishment options
   *
   * @access private
   * @var array 
   */
  private $options = array(
    
    # requests over https?
    'https' => false,
    
    # timeout after seconds
    'timeout' => 15,
    
    # block size for fgets
    'block_size' => 255
  );
  
  /**
   * Status of establishment
   * A successful establish() turns this to true
   *
   * @access private
   * @var boolean
   */
  private $established = false;
  
  /**
   * fsockopen handle resource
   *
   * @access private
   * @var resource
   */
  private $handle;

  /**
   * Constructs a new connection instance
   *
   * @access public
   * @param string $host
   * @param int $port
   * @param array $options
   */
  function __construct($host, $port = 80, array $options = array()) {
    $this->host = $host;
    $this->port = (int)$port;
    $this->options = array_merge($this->options, $options);
  }
  
  /**
   * Returns the current establishment status
   *
   * @access public
   * @return boolean
   */
  function established() {
    return $this->established;
  }
  
  /**
   * Returns the host url including port number
   *
   * @access public
   * @return string
   */
  function host_url() {
    $scheme = $this->options['https'] ? 'https' : 'http';
    return "$scheme://$this->host:$this->port";
  }
  
  /**
   * Establishes the connection (sets the handle resource)
   * request() auto calls this if there is no connection established
   *
   * @access public
   */
  function establish() {
    if($this->established) return;
    $timeout = $this->options['timeout'];
    
    $handle = @fsockopen($this->host, $this->port, $error_number, $error, $timeout);
    if(!$handle) {
      throw new Error("Cannot open [$this->host:$this->port] with [$error] within [$timeout] seconds");
    } else {
      $this->established = true;
      $this->handle = $handle;
    }
  }
  
  /**
   * Closes the connection if established
   *
   * @access public
   * @return boolean
   */
  function close() {
    if(!$this->established) return false;
    
    $this->established = false;
    return fclose($this->handle);
  }
  
  /**
   * Send request to given host
   *
   * @access public
   * @param string $path
   * @param string $method
   * @param array $fields
   * @return Response
   */
  function request($path, $method = 'get', array $fields = array()) {
    $this->establish();
    $this->puts_request_line($method, $path);
    $this->puts_host_line();
    $this->puts("Connection: close");
    $this->puts();
    
    # write headerfields
    foreach($fields as $line) $this->puts($line);
    
    $this->puts();
    
    $body = '';
    $line_index = 0;
    $response_fields = array();
    $body_starts = false;
    $status = 200;
    
    while(!feof($this->handle)) {
        // receive the results of the request
      $line = $this->read();
      
      if($body_starts) {
        $body .= $line;
        continue;
      }
      
      if($line_index !== 0 and (strpos($line, ':') === false)) {
        $body_starts = true;
        continue;
      }
      
      if($line_index === 0) {
        list($protocol, $status) = explode(' ', $line);
      } else {
        list($n, $v) = explode(': ', $line);
        $response_fields[strtolower(str_replace('-', '_', $n))] = str_replace("\\r\\n", "", trim($v));
      }
      
      $line_index++;
    }
    
    $response = new Response((int)$status, $body, $response_fields);
    $response->protocol = $protocol;
    return $response;
  }

  /**
   * Shortcut for sending a GET request
   *
   * @access public
   * @param string $path
   * @param array $fields
   * @return Response
   */
  function get($path, array $fields = array()) {
    return $this->request($path, 'get', $fields);
  }
  
  /**
   * Shortcut for sending a POST request
   *
   * @access public
   * @param string $path
   * @param array $fields
   * @return Response
   */  
  function post($path, array $fields = array()) {
    return $this->request($path, 'post', $fields);
  }
  
  /**
   * Shortcut for sending a PUT request
   *
   * @access public
   * @param string $path
   * @param array $fields
   * @return Response
   */  
  function put($path, array $fields = array()) {
    return $this->request($path, 'put', $fields);
  }
  
  /**
   * Shortcut for sending a DELETE request
   *
   * @access public
   * @param string $path
   * @param array $fields
   * @return Response
   */  
  function delete($path, array $fields = array()) {
    return $this->request($path, 'delete', $fields);
  }
  
  /**
   * Writes a returned line to the connection handle
   *
   * @access protected
   * @param string $text
   */
  protected function puts($text = null) {
    $count = fputs($this->handle, "$text\r\n");
    
    if(!$count) {
      $this->close();
      throw new Error("Cannot write to connection handle");
    }
  }
  
  /**
   * Helper for putting the requests main line
   *
   * @access protected
   * @param string $method
   * @param string $path
   */
  protected function puts_request_line($method, $path) {
    if(!empty($path) and $path[0] != '/') $path = "/$path";
    $url = $this->host_url().$path;
    
    $this->puts(strtoupper($method)." $url HTTP/1.0");
  }
  
  /**
   * Helper for putting the requests host line
   *
   * @access protected
   */  
  protected function puts_host_line() {
    $this->puts("Host: $this->host");
  }
  
  /**
   * Reads a line from the current connection handle
   *
   * @access protected
   * @return string
   */
  protected function read() {
    $text = @fgets($this->handle, $this->options['block_size']);
    
    if(!$text) {
      $this->close();
      throw new Error("Cannot read from connection handle");
    }
    
    return $text;
  }
}
?>