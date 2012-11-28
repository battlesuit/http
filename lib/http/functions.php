<?php
namespace http {
  
  /**
   * HTTP environment accessor
   *
   * Example:
   *  env(); # => only returns the environment
   *  env('PATH_INFO'); # => returns the environment variable PATH_INFO
   *  env('REQUEST_URI', '/my/path'); # => sets the environment variable REQUEST_URI to /my/path
   *
   * @param string $name
   * @param mixed $value
   * @return Env
   */
  function env($name = null, $value = null) {
    static $instance;
    if(!isset($instance)) $instance = Env::dump();
    
    if(isset($name)) {
      if(isset($value)) {
        return $instance[$name] = $value;
      } else return $instance[$name];
    }
    
    return $instance;
  }
  
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
   * Writes all headers and prints the response body
   *
   * @access public
   */
  function serve(Response $response) {
    
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