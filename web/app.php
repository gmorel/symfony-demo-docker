<?php

// This is the front controller used when executing the application in the
// production environment ('prod'). See
//
//   * http://symfony.com/doc/current/cookbook/configuration/front_controllers_and_kernel.html
//   * http://symfony.com/doc/current/cookbook/configuration/environments.html

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../app/bootstrap.php.cache';

$host = 'example_com';

$env = getenv('SYMFONY_ENV') ?: 'prod';
$debug = 'dev' === $env;

if ($debug) {
    Debug::enable();
}

if ('prod' === $env) {
    $apcLoader = new ApcClassLoader($host . '.' . sha1(__FILE__), $loader);
    $loader->unregister();
    $apcLoader->register(true);
}

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();

// When using the HTTP Cache to improve application performance, the application
// kernel is wrapped by the AppCache class to activate the built-in reverse proxy.
// See http://symfony.com/doc/current/book/http_cache.html#symfony-reverse-proxy
$kernel = new AppCache($kernel);

// If you use HTTP Cache and your application relies on the _method request parameter
// to get the intended HTTP method, uncomment this line.
// See http://symfony.com/doc/current/reference/configuration/framework.html#http-method-override
// Request::enableHttpMethodParameterOverride();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
