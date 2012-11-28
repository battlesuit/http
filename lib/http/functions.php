<?php
namespace http {
  
  /**
   * Alias for transaction\run()
   *
   * @param callable $processor
   * @param Request $request
   * @return transaction\Server
   */
  function run_transaction($processor, Request $request = null) {
    return transaction\run($processor, $request);
  }
  
  /**
   * Writes a location header
   *
   * @param string $url
   * @param int $code
   */
  function redirect_to($url, $code = 301) {
    header("Location: $url", true, $code);
  }
}
?>