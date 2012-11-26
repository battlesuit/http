<?php
namespace http;

/**
 * Default transaction handler
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
class Transaction extends Object {
  
  /**
   * Process callback
   *
   * @access protected
   * @var callable
   */
  protected $processor;
  
  /**
   * Constructs a new transaction instance
   *
   * @access public
   * @param callable $processor
   */
  function __construct($processor) {
    $this->processor = $processor;
  }
  
  /**
   * Invocation processes a transaction
   *
   * @access public
   * @param Request $request
   * @return Response $response
   */  
  function __invoke($request) {
    return $this->process($request);
  }
  
  /**
   * Reads the transaction processor
   *
   * @access public
   * @return callable
   */
  function processor() {
    return $this->processor;
  }
  
  /**
   * Handles a full process statically
   *
   * @static
   * @access public
   * @param callable $processor
   * @param Request $request
   * @return Response
   */
  static function handle($processor, Request $request) {
    $transaction = new static($processor);
    return $transaction->process($request);
  }
  
  /**
   * Processes a transaction
   *
   * @access public
   * @param Request $request
   * @return Response $response
   */   
  function process(Request $request) {
    ob_start();
    try {
      $returned_response = call_user_func($this->processor, $request);
    } catch(\Exception $e) {
      ob_end_clean();
      throw $e;
    }
    $captured_response = ob_get_clean();
    
    
    if(!empty($captured_response)) {
      return new Response(200, $captured_response);
    }
    elseif(is_array($returned_response)) {
      return new Response($returned_response[0], $returned_response[2], $returned_response[1]);
    } elseif($returned_response instanceof Response) return $returned_response;
    elseif(is_string($returned_response)) {
      return new Response(200, $returned_response);
    }
    
    return new Response();
  }
}
?>