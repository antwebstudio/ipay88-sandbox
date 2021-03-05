<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\View;
use App\Validator;

// Load config
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

Validator::setTranslationPath(__DIR__);

$request = Request::capture();

// Sandbox
$sandbox = new \Ant\Sandbox\Ipay88Sandbox($request);
$sandbox->config([
    'merchantCode' => $_ENV['MERCHANT_CODE'],
    'merchantKey' => $_ENV['MERCHANT_KEY'],
]);
$sandbox->process();

// dd($sandbox);
// dd($request->all());

// Rendering output
$view = View::make('views', 'storage/views');
echo $view->render('index', [
    'request' => $request,
    'errors' => $sandbox->errors(),
    'sandbox' => $sandbox,
    'successResponse' => $sandbox->successResponse(),
    'cancelResponse' => $sandbox->cancelResponse(),
    'errorResponse' => $sandbox->errorResponse(),
]);