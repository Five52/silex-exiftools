<?php
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

ErrorHandler::register();
ExceptionHandler::register();

$app->register(new Silex\Provider\TwigServiceProvider(), [
    "twig.path" => __DIR__."/../views/"
]);
$app['twig']->addGlobal('root', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
