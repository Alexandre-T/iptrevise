<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV']) || in_array($_SERVER['APP_ENV'], ['dev'])) {
    (new Dotenv())->load(__DIR__.'/../.env');
}

if ($_SERVER['APP_DEBUG'] ?? true) {
    // WARNING: You should setup permissions the proper way!
    // REMOVE the following PHP line and read
    // https://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup

    Debug::enable();
}

if ($_SERVER['APP_ENV'] ?? 'dev' && $_SERVER['APP_DEBUG'] ?? true)
{
    define('C3_CODECOVERAGE_ERROR_LOG_FILE', '../tests/_output/'); //Optional (if not set the default c3 output dir will be used)
    include '../c3.php';
    define('MY_APP_STARTED', true);
    // App::start();

}


$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', $_SERVER['APP_DEBUG'] ?? true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
