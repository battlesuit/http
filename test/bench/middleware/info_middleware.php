<?php
use http\Request;

class InfoMiddleware extends http\transaction\Application {
  function process(Request $request) {
    $response = parent::process($request);
    $response->write("Info: $request");
    return $response;
  }
}
?>