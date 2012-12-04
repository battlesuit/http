<?php
namespace http;

/**
 * Description of connection
 *
 * @author Tom
 */
class Connection {
  private $https;
  
  private $host;
  private $port;
  private $established = false;
  private $handle;
  
  public $timeout = 15;
  public $block_size = 255;
  public $cookies = array();
  public $request_body;
  
  function __construct($host, $port = 80, $https = false) {
    $this->host = $host;
    $this->port = $port;
    $this->https = $https;
  }
  
  function established() {
    return $this->established;
  }
  
  function domain_url() {
    $scheme = $this->https ? 'https' : 'http';
    return "$scheme://$this->host:$this->port";
  }
  
  function establish() {
    if($this->established) return;
    
    $handle = @fsockopen($this->host, $this->port, $error_number, $error, $this->timeout);
    if(!$handle) {
      throw new \ErrorException("Cannot open [$this->host:$this->port] with [$error] within [$this->timeout] seconds");
    } else {
      $this->established = true;
      $this->handle = $handle;
    }
  }
  
  function close() {
    if(!$this->established) return;
    
    $this->established = false;
    return fclose($this->handle);
  }
  
  function request($path, $method = 'get', array $fields = array()) {
    $this->establish();
    $this->puts($this->write_request_line($method, $path));
    $this->puts($this->write_host_line());
    $this->puts("Connection: close");
    $this->puts();
    
    # write headerfields
    foreach($fields as $line) $this->puts($line);
    
    if(!empty($this->cookies)) {
      $this->puts("Cookie: ".implode(";", $this->cookies));
    }
    
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
  
  protected function puts($text = null) {
    $count = fputs($this->handle, "$text\r\n");
    
    if(!$count) {
      $this->close();
      throw new \ErrorException("Cannot write to socket");
    }
  }
  
  function read() {
    $text = @fgets($this->handle, $this->block_size);
    
    if(!$text) {
      $this->close();
      throw new \ErrorException("Cannot read from socket");
    }
    
    return $text;
  }
  
  protected function read_till_end($each = null) {
    $result = '';
    while(!feof($this->handle)) {
        // receive the results of the request
      $line = $this->read();
      if(is_callable($each)) $each($line);
      $result .= $line;
    }
    
    return $result;
  }
  
  protected function write_request_line($method, $path) {
    if(!empty($path) and $path[0] != '/') $path = "/$path";
    $url = $this->domain_url().$path;
    
    return strtoupper($method)." $url HTTP/1.0";
  }
  
  protected function write_host_line() {
    return "Host: $this->host";
  }
  
  function get($path, array $header = array()) {
    return $this->request($path, 'get', $header);
  }
  
  function post($path, array $header = array()) {
    return $this->request($path, 'post', $header);
  }
  
  function put($path, array $header = array()) {
    return $this->request($path, 'put', $header);
  }
  
  function delete($path, array $header = array()) {
    return $this->request($path, 'delete', $header);
  }
}
?>