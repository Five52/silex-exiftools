<?php
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\CsrfServiceProvider;

ErrorHandler::register();
ExceptionHandler::register();

$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new CsrfServiceProvider());
$app->register(new TranslationServiceProvider(),  [
    'locale' => 'fr'
]);

$app->register(new TwigServiceProvider(), [
    "twig.path" => __DIR__."/../views/"
]);
$app['twig']->addGlobal('root', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
