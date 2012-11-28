<?php
namespace http {
  require __DIR__.'/../init/test.php';
  
  set_include_path(get_include_path().PATH_SEPARATOR.__DIR__);
  
  # register default autoload functionality
  autoload_in('http', __DIR__."/http");
  
  class PackageTestBench extends \test_bench\Base {
    function initialize() {
      
      # foundation testcases
      $this->add_test(new SessionTest());
      $this->add_test(new EnvTest());
      
      # transaction testcases
      $this->add_test(new transaction\MessageTest());
      $this->add_test(new transaction\RequestTest());
      $this->add_test(new transaction\ResponseTest());
      $this->add_test(new transaction\ServerTest());
    }
  }
  
  $bench = new PackageTestBench();
  $bench->run_and_present_as_text();
}
?>