<?php
namespace http\cgi;
use http\Object;
use http\Response;

/**
 * CGI Transaction handler object
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Suitcase
 * @subpackage Net
 */
class Transactor extends Object {
  
  /**
   * 
   * 
   */
  private $response;
  
  /**
   * 
   * 
   */
  function __construct($response) {
    $this->response = $response;
  }
  
  /**
   * 
   * 
   */
  function __invoke() {
    $this->serve();
  }
  
  static function new_request() {
    return new Request(Env::load());
  }
  
  /**
   * Writes all headers and prints the response body
   *
   * @access public
   */
  function serve() {
    $response = $this->response;
    
    # introduce response
    header("$response->protocol $response->status");
    
    if($response->any_fields()) {
      foreach($response as $name => $value) { 
        header($this->normalize_field($name, $value));
      }
    }
    
    print $response->flat_body();
  }
  
  /**
   * Normalizes a fieldname => [Content-Type]: [value]
   *
   * @access protected
   * @param string $name
   * @param mixed $value
   * @return string
   */
  protected function normalize_field($name, $value) {
    $name = str_replace(' ', '-', ucwords(str_replace('_', ' ', $name)));
    return "$name: $value";
  }
}
?>