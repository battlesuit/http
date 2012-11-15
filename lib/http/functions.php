<?php
namespace http {
  
  /**
   * Append a new middleware application to the stack
   *
   * @param string $class
   */
  function use_middleware($class) {
    Module::$transaction_middleware[] = $class;
  }
  
  /**
   * Starts a server transaction within a given block
   *
   * @param mixed $type
   * @param callable $application
   * @return Transactor
   */
  function run_transaction($application, $type_or_request = 'cgi') {
    return Module::run_transaction($application, $type_or_request);
  }
  
  function redirect_to($url) {
    header("Location: $url");
    exit;
  }
}
?>