<?php

use Symfony\Component\HttpFoundation\Request;

use ExifTools\ExifTools;

$app->match('/', function(Request $request) use ($app) {
    $dirname = "../web/files/";
    $images = glob($dirname . "*.jpg");

    return $app['twig']->render('index.html.twig', array(
        'images' => $images
        ));
})
->bind('home');

$app->get('/image', function() {
    return 'Image added';
})
->bind('image_added');

$app->match('/ajout-image', function(Request $request) use ($app) {
    $file = $request->files->get('upload');
    if ($file !== null) {
        $fileName = md5(uniqid()) . $file->guessExtension();
        $file->move(__DIR__."/../web/files", $fileName);
        return $app->redirect($app['url_generator']->generate('image_added'));
    }
    return $app['twig']->render('add.html.twig');
}, 'GET|POST')
->bind('add_image');

$app->get('/test', function(){
    $test = ExifTools::getImgDetails("Montreal.jpg");
    echo '<pre>';
        var_dump($test);
    // foreach ($test as $key => $tab) {
    //     echo $key.':<br/>';
    //     foreach ($tab as $column => $value) {
    //         echo "    ".$column." : ".$value."<br/>";
    //     }
    // }
    echo '</pre>';
    return "doky";
});
