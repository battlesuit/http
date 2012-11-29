<?php
namespace http;

/**
 * Description of connection
 *
 * @author Tom
 */
class Connection {
  private $url;
  private $info = array();
  private $format;
  private $established = false;
  private $handle;
  
  public $timeout = 15;
  public $block_size = 255;
  public $cookies = array();
  public $request_body;
  
  function __construct($url, $format = 'xml') {
    $this->url = $url;
    $this->parse_url($url);
    $this->format = $format;
  }
  
  function established() {
    return $this->established;
  }
  
  protected function parse_url($url) {
    $info = parse_url($url);
    
    if(!isset($info['scheme'])) {
      $info['scheme'] = 'http';
    }
    
    if(!isset($info['host'])) {
      $info['host'] = '127.0.0.1';
    }
    
    if(!isset($info['port'])) {
      $info['port'] = ($info['scheme'] == 'https') ? 443 : 80;
    }
    
    $this->info = $info;
  }
  
  function establish() {
    if($this->established) return;
    
    $timeout = $this->timeout;
    extract($this->info);
    
    $handle = @fsockopen($host, $port, $error_number, $error, $timeout);
    if(!$handle) {
      throw new \ErrorException("Cannot open [$host:$port] with [$error] within [$timeout] seconds");
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
  
  function request($method, array $fields = array()) {
    $this->establish();
    $this->puts($this->write_request_line($method));
    $this->puts($this->write_host_line());
    $this->puts("Connection: close");
    $this->puts();
    
    foreach($fields as $line) {
      $this->puts($line);
    }
    
    if(!empty($this->cookies)) {
      $this->puts("Cookie: ".implode(";", $this->cookies));
    }
    
    //$socket->puts("Content-Length: ".(int)strlen($this->request_body));
    //$socket->puts("Content-Type: ".Request::mime_for($this->format));
    $this->puts();
    //$socket->puts($this->request_body);
    
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
  
  protected function write_request_line($method) {
    return strtoupper($method)." $this->url HTTP/1.0";
  }
  
  protected function write_host_line() {
    return "Host: ".$this->info['host'];
  }
  
  function get(array $header = array()) {
    return $this->request('get', $header);
  }
  
  function post(array $header = array()) {
    return $this->request('post', $header);
  }
  
  function put(array $header = array()) {
    return $this->request('put', $header);
  }
  
  function delete(array $header = array()) {
    return $this->request('delete', $header);
  }
}
?>