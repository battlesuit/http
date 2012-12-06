<?php
namespace http\transaction;
use http\Request;
use http\transaction\server\Middleware;

/**
 * Server transaction
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Server extends Application {
  
  /**
   * Middleware instance
   *
   * @access private
   * @var Middleware
   */
  private $middleware;
  
  /**
   * Constructs a new server instance
   * 
   * @access public
   * @param callable $processor
   * @param Middleware $middleware
   */
  function __construct($processor, Middleware $middleware = null) {
    parent::__construct($processor);
    $this->middleware = isset($middleware) ? $middleware : static::Middleware();
  }
  
  /**
   * Creates, processes and returns a transaction instance
   *
   * @static
   * @access public
   * @param callable $processor
   * @param Request $request
   * @return Server
   */
  static function run($processor, Request $request) {
    $transaction = new static($processor, static::Middleware());
    $transaction->process($request);
    return $transaction;
  }
  
  /**
   * Access middleware collection
   *
   * @static
   * @access public
   * @param callable $block
   */
  static function Middleware($block = null) {
    static $collection;
    if(!isset($collection)) $collection = new Middleware();
    if(is_callable($block)) call_user_func($block, $collection);
    return $collection;
  }
  
  /**
   * Processes the transaction
   *
   * @access public
   * @param Request $request
   * @return Response
   */   
  function process(Request $request) {
    $this->processor = $this->middleware->compose($this->processor);
    return parent::process($request);
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
        $name = str_replace(' ', '-', ucwords(str_replace('_', ' ', $name)));
        header("$name: $value");
      }
    }
    
    print $response->flat_body();
  }
}
?>