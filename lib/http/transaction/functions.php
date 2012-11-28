<?php
namespace http\transaction {
  
  /**
   * Starts a server transaction within a given block
   *
   * @param mixed $type
   * @param callable $application
   * @param Request $request
   * @return Transaction
   */
  function run($processor, Request $request = null) {
    if(!isset($request)) $request = new Request(\http\env());
    return Server::run($processor, $request);
  }
}
?>