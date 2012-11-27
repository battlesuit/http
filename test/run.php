<?php
namespace http {
  require __DIR__.'/../init/test.php';
  
  set_include_path(get_include_path().PATH_SEPARATOR.__DIR__);
  
  # register default autoload functionality
  autoload_in('http', __DIR__."/http");
  
  class PackageTestBench extends \test_bench\Base {
    function initialize() {
      $this->add_test(new MessageTest());
      $this->add_test(new RequestTest());
      $this->add_test(new ResponseTest());
      $this->add_test(new TransactionTest());
      $this->add_test(new SessionTest());
    }
  }
  
  $bench = new PackageTestBench();
  $bench->run_and_present_as_text();
}
?>