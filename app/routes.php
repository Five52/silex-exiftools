<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

use ExifTools\ExifTools;

define('FILEDIR', __DIR__.'/../web/files');

$app->match('/', function(Request $request) use ($app) {
    $dirname = "../web/files/";
    $images = glob($dirname . "*.jpg");

    return $app->render('index.html.twig', array(
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
    return $app->render('add.html.twig');
}, 'GET|POST')
->bind('add_image');

$app->get('/test', function(){

    try{
        $test = ExifTools::generateImgMeta("Montreal.jpg");
        // $array = ExifTools::getImgMeta("Montreal.jpg");
        // ExifTools::setImgMeta($array, "Montreal.jpg");
        // $test = "truc";
    } catch(Exception $e) {
        echo '<pre>';
        var_dump($e);
        echo '</pre>';
        $test = "fail";
    }

    echo '<pre>';
        var_dump($test);
    echo '</pre>';
    return "doky";
});

$app->match('/add', function(Request $request) use ($app) {
    // Create file upload form
    $data = [];
    $form = $app->form($data)
        ->add('file', FileType::class, [
            'constraints' => new Assert\Image(),
            'label' => 'Image',
            'required' => false
        ])
        ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
        ->getForm()
    ;

    $form->handleRequest($request);
    if ($request->isMethod('POST') && $form->isValid()) {
        $data = $form->getData();
        $file = $data['file'];
        do {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        } while (file_exists(FILEDIR . '/' . $fileName));

        $file->move(FILEDIR, $fileName);
        ExifTools::generateImgMeta($fileName);
        return $app->redirect($app->path('image_added'));
    }

    return $app->render('add.html.twig', [
        'form' => $form->createView(),
    ]);
}, 'GET|POST');
