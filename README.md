bs.http
=======

This minimalistic bundle should serve as a fundation for local http server transactions with middleware integration.  
Run tests under `test/run.php` (59/59 completed, 116 passes)

###The Environment Accessor
Use the `http\transaction\env()` function to access and/or initialize the environment.
By default the environment includes the `$_SERVER` array and `$_COOKIE` wrapped in `http\Cookies` class. 
If you want to extend the environment just do the following:

    namespace http\transaction {
      use http\Session;
    
      Env::init(function($env) {
        $env['session'] = Session::start();
      });
      
      $env = env(); # includes 'session' object
    }

###The Request
######Manual creation

    namespace http {
      $r = new Request('get', 'http://domain.de:80/path/to/resource', array('post_var' => 'foo'))
      $r->port; # => 80
    }
    
######Create under transaction environment

    namespace http\transaction {
      $r = new Request(env());
    }
    
###Server transactions
######Manual transaction

    namespace http {
      
      # first we create the processor object which handles the transaction
      # Any kind of callable is supported
      $p = function($request) {
        return new Response(200, $request->data['var']);
      };
      
      # So now we wrap our processor into a transaction server instance
      $t = new transaction\Server($p);
      
      # next its time to process a request
      $t(new Request('get', 'http://my.domain.com/resource?var=foo'));
      
      # finally we got a response to serve
      $t->serve(); # writes headers and prints body => "foo"
    }
    
######Shorter writing

    namespace http {
      transaction\run(function($request) {
        return new Response(200, $request->data['var']);
      }, new Request('get', 'http://my.domain.com/resource?var=foo'))->serve();     
    }
    
###Environment transaction
Leaving out the request will auto-create one with `http\transaction\env()`

    namespace http {
      transaction\run(function($request) {
        return new Response(200, $request->data['var']);
      })->serve();
    }

###Middleware integration
So if you want to integrate some of your own middleware classes you have to write a very short initor for the server transaction before running it:

    namespace http {
      transaction\Server::init(function($t) {
        $t->integrate('my/own/MiddlewareClass');
      });
      
      transaction\run(function($request) {
        return new Response(200, $request->data['var']);
      })->serve();
    }