<?php
require_once 'vendor/autoload.php';
header('Content-Type: application/json');


use GemLibrary\Http\ApacheRequest;
use Dotenv\Dotenv;
use App\Core\Bootstrap;
use GemLibrary\Helper\NoCors;

NoCors::NoCors();

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$serverRequest = new ApacheRequest();

$bootstrap = new Bootstrap($serverRequest->request);
$bootstrap->runApp();

