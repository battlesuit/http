<?php
namespace test_bench;

class PackageTestBench extends Base {
  function initialize() {
    $this->add_test(new \http\MessageTest());
    $this->add_test(new \http\RequestTest());
    $this->add_test(new \http\ResponseTest());
    $this->add_test(new \http\TransactionTest());
    $this->add_test(new \http\SessionTest());
  }
}
?>