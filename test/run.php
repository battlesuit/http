<?php
namespace http {
  require __DIR__.'/../init/test.php';
  
  set_include_path(get_include_path().PATH_SEPARATOR.__DIR__);
  
  # register default autoload functionality
  autoload_in('http', __DIR__."/http");
  autoload_in('server', __DIR__."/server");
  
  class PackageTestBench extends \test_bench\Base {
    function initialize() {
      
      # foundation testcases
      $this->add_test(new SessionTest());
      $this->add_test(new MessageTest());
      $this->add_test(new RequestTest());
      $this->add_test(new ResponseTest());      
      
      # transaction testcases
      $this->add_test(new transaction\EnvTest());
      $this->add_test(new transaction\ServerTest());
      
      # local server tests
      $this->add_test(new \server\ConnectionTest());
    }
  }
  
  $bench = new PackageTestBench();
  $bench->run_and_present_as_text();
}
?>