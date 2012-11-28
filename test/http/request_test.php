<?php
namespace http;

class RequestTest extends MessageTester {  
  function test_blank_construction() {
    new Request();
  }
  
  function test_defaults() {
    $request = new Request();
    $this->assert_eq($request->method(), 'head');
    $this->assert_eq($request->url(), 'http://localhost:80');
    $this->assert_empty_array($request->data);
    $this->assert_empty_array($request->query);
    
    $url_comps = $request->url_components();
    $this->assert_eq($url_comps['scheme'], 'http');
    $this->assert_eq($url_comps['host'], 'localhost');
    $this->assert_eq($url_comps['port'], 80);
    
    $this->assert_eq($request->scheme(), 'http');
    $this->assert_eq($request->script_url(), 'http://localhost');
    $this->assert_eq($request->host_url(), 'http://localhost');
    $this->assert_empty_string($request->path_info());
    $this->assert_empty_string($request->path());
    $this->assert_empty_array($request->accepted_mimes());
    
    $this->assert_false($request->via_get());
    $this->assert_false($request->via_post());
    $this->assert_false($request->via_put());
    $this->assert_false($request->via_delete());
    $this->assert_true($request->via_head());
    
    $this->assert_eq("$request", 'HEAD http://localhost:80');
    $this->assert_eq($request->to_array(), array('head', 'http://localhost:80', array(), array()));
  }
  
  function test_method_lowercasion() {
    $r = new Request('POST');
    $this->assert_eq($r->method(), 'post');
  }
  
  function test_with_path() {
    $r = new Request('get', 'http://domain.de/foo/bar');
    $this->assert_eq($r->path(), '/foo/bar');
  }
  
  function test_with_path_including_file() {
    $r = new Request('get', 'http://domain.de/foo/bar/index.php/asf');
    
    $this->assert_eq($r->path(), '/foo/bar');
  }
  
  function test_with_path_and_query_string() {
    $r = new Request('get', 'http://domain.de/foo/bar?param=hallo');
    $this->assert_eq($r->path(), '/foo/bar');
  }
  
  function test_with_path_and_file() {
    $r = new Request('get', 'http://domain.de/foo/bar/index.php?param=hallo');
    $this->assert_eq($r->path(), '/foo/bar');
  }
  
  function test_script_url() {
    $r = new Request('get', 'http://domain.de/foo/bar/index.php/bla?param=hallo');
    $this->assert_eq($r->script_url(), 'http://domain.de/foo/bar/index.php');
  }
  
  function test_host_url() {
    $r = new Request('get', 'http://domain.de/foo/bar/index.php/bla?param=hallo#fragment');
    $this->assert_eq($r->host_url(), 'http://domain.de');
  }
  
  function test_host_url_with_port() {
    $r = new Request('get', 'http://domain.de:80/foo/bar/index.php/bla?param=hallo#fragment');
    $this->assert_eq($r->host_url(true), 'http://domain.de:80');
  }
  
  function test_path_info() {
    $r = new Request('get', 'http://domain.de/foo/bar/index.php/foo/bar?param=hallo');
    $this->assert_eq($r->path_info(), '/foo/bar');
  }
  
  function test_path_info_with_dotted_dir() {
    $r = new Request('get', 'http://domain.de/foo.mooh/bar.de/index.php/foo/bar?param=hallo');
    $this->assert_eq($r->path_info(), '/foo/bar');
  }
  
  function test_path_info_without_scriptfile() {
    $r = new Request('get', 'http://domain.de/foo.mooh/bar.de/foo/bar?param=hallo');
    $this->assert_eq($r->path_info(), '');
    $this->assert_eq($r->path(), '/foo.mooh/bar.de/foo/bar');
  }
  
  function test_method() {
    $r = new Request('delete');
    $this->assert_equality($r->method(), 'delete');
  }
  
  function test_method_by_query() {
    $request = new Request('post', 'http://domain.de?_method=put');
    $this->assert_equality($request->method(), 'put');
  }
  
  function test_method_by_data() {
    $r = new Request('post', 'http://domain.de', array('_method' => 'delete'));
    $this->assert_equality($r->method(), 'delete');
  }
  
  function test_url() {
    $request = new Request('post', 'http://domain.de/hello/world?destroy=true');
    $this->assert_equality($request->url(), 'http://domain.de/hello/world?destroy=true');
  }
  
  function test_to_string() {
    $request = new Request('post', 'http://domain.de/hello/world?destroy=true');
    $this->assert_equality("$request", "POST http://domain.de/hello/world?destroy=true");
  }
  
  function test_url_components() {
    $request = new Request('post', 'http://tom:552@domain.de:80/hello/world/call.php/path/info?id=12&name=mel#my-frag');
    $components = $request->url_components();
    $this->assert_equality($components, array(
      'scheme' => 'http',
      'host' => 'domain.de',
      'port' => 80,
      'user' => 'tom',
      'pass' => '552',
      'path' => '/hello/world/call.php/path/info',
      'query' => 'id=12&name=mel',
      'fragment' => 'my-frag',
      'file' => 'call.php',
      'path_info' => '/path/info'
    ));
  }
  
  function test_resource_path() {
    $request = new Request('get', 'http://domain.de:80/hello/world');
    $this->assert_eq($request->resource_path(), '/hello/world');
    
    $request = new Request('get', 'http://domain.de:80/hello/world/index.php/foo/bar');
    $this->assert_eq($request->resource_path(), '/foo/bar');
  }
  
  function test_content_length_default() {
    $request = new Request('post', 'http://domain.de:80/hello/world/call.php/path/info?id=12&name=mel', array('foo' => 'bar'));
    $this->assert_equality($request['content_length'], 22);
  }
  
  function test_via() {
    $r = new Request('get');
    $this->assert_true($r->via('get'));
  }
  
  function test_via_uppercased() {
    $r = new Request('get');
    $this->assert_true($r->via('GET'));
  }
  
  function test_via_get() {
    $r = new Request('get');
    $this->assert_true($r->via_get());
  }
  
  function test_via_post() {
    $r = new Request('post');
    $this->assert_true($r->via_post());
  }
  
  function test_via_del() {
    $r = new Request('delete');
    $this->assert_true($r->via_delete());
  }
  
  function test_via_put() {
    $r = new Request('put');
    $this->assert_true($r->via_put());
  }
  
  function test_accepted_mimes() {
    $request = new Request('post', 'http://domain.de:80', array(), array('accept' => 'text/xml,text/plain,application/*,*/javascript'));
    
    $mimes = $request->accepted_mimes();
    $this->assert_array($mimes);
    $this->assert_equality($mimes, array('text/xml', 'text/plain', 'application/*', '*/javascript'));
  }
  
  function test_accepts_mime() {
    $request = new Request('post', 'http://domain.de:80', array(), array('accept' => 'text/xml,text/plain,application/*,*/javascript'));
    
    $this->assert_true($request->accepts_mime('text/xml'));
    $this->assert_true($request->accepts_mime('text/plain'));
    $this->assert_false($request->accepts_mime('*/*'));
    $this->assert_true($request->accepts_mime('application/json'));
    $this->assert_true($request->accepts_mime('text/javascript'));
  }
  
  function test_accepts_format() {
    $request = new Request('post', 'http://domain.de:80', array(), array('accept' => 'text/xml,text/plain,application/*,*/javascript'));
    
    $this->assert_true($request->accepts_format('xml'));
    $this->assert_true($request->accepts_format('txt'));
    $this->assert_true($request->accepts_format('json'));
    $this->assert_false($request->accepts_format('css'));
  }
  
  function test_to_array() {
    $request = new Request('post', 'http://tom:552@domain.de:80/hello/world/call.php/path/info?id=12&name=mel#my-frag', array('simon' => 'says'), array('accept' => 'text/xml,text/plain,application/*,*/javascript'));
    $this->assert_eq($request->to_array(), array('post', 'http://tom:552@domain.de:80/hello/world/call.php/path/info?id=12&name=mel#my-frag', array('id' => '12', 'name' => 'mel', 'simon' => 'says'), array('accept' => 'text/xml,text/plain,application/*,*/javascript', 'content_length' => 25)));
  }
}
?>