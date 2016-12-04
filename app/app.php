<?php

$app->register(new Silex\Provider\TwigServiceProvider(), [
    "twig.path" => __DIR__."/../views/"
]);
$app['twig']->addGlobal('root', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

