<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

use ExifTools\ExifTools;
use ExifTools\Image;

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
        $image = new Image();
        $image->setExtension($file->guessExtension());
        do {
            $image->setId(md5(uniqid()));
        } while (file_exists($image->getPath()));

        $file->move(FILEDIR, $image->getName());
        // ExifTools::generateImgMeta($fileName);
        return $app->redirect($app->path('update', [
            'id' => $image->getId(),
            'extension' => $image->getExtension()
        ]));
    }

    return $app->render('add.html.twig', [
        'form' => $form->createView(),
    ]);
}, 'GET|POST')
->bind('add');

$app->match(
    '/{id}_{extension}',
    function($id, $extension, Request $request) use ($app) {
        $image = new Image();
        $image->setId($id)->setExtension($extension);
        // $meta = $image->getLatestMeta();

        // $formBuilder = $app->form($meta);
        // foreach ($meta as $key => $value) {
        //     $formBuilder->add($key, TextType::class);
        // }
        // $formBuilder->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);
        // $form = $formBuilder->getForm();

        // $form->handleRequest($request);
        // if ($request->isMethod('POST') && $form->isValid()) {
        //     var_dump($meta);
        //     return 'ok';
        // }

        return $app->render('update.html.twig', [
            'image' => $image
        ]);
    }, 'GET|POST'
)
->bind('update');
