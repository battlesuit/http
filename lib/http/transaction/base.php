<?php
namespace http\transaction;

/**
 * Base transaction *abstract*
 *
 * PHP Version 5.3+
 * @author Thomas Monzel <tm@apparat-hamburg.de>
 * @version $Revision$
 * @package Battlesuit
 * @subpackage http
 */
abstract class Base {
  
  /**
   * Process callback
   *
   * @access protected
   * @var callable
   */
  protected $processor;
  
  /**
   * Processed response
   *
   * @access protected
   * @var Response
   */
  protected $response;
  
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
  function __invoke(Request $request) {
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
   * Returns the response set by process()
   *
   * @access public
   * @return Response
   */
  function response() {
    return $this->response;
  }
  
  /**
   * Processes the transaction
   * Should return a response... must! :)
   *
   * @abstract
   * @access public
   * @param Request $request
   * @return Response $response
   */   
  abstract function process(Request $request);
}
?>