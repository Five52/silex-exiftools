<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

use ExifTools\ExifTools;
use ExifTools\Image;
use ExifTools\ImageDAO;

$app->get('/', function(Request $request) use ($app) {

    return $app->render('index.html.twig', array(
        'images' => ImageDAO::getAll()
    ));
})
->bind('home');

$app->match('/map', function(Request $request) use ($app) {

    return $app->render('map.html.twig', array(
        'images' => ImageDAO::getAll()
    ));
})
->bind('map');

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
        if ($file !== null) {
            $image = new Image();
            $image->setExtension($file->guessExtension());
            do {
                $image->setId(substr(md5(uniqid()), 0, 8));
            } while (ImageDAO::exists($image));

            $file->move(Image::IMG_PATH, $image->getName());
            // ExifTools::generateImgMeta($fileName);
            return $app->redirect($app->path('update', [
                'id' => $image->getId(),
                'extension' => $image->getExtension()
            ]));
        }
    }

    return $app->render('add.html.twig', [
        'form' => $form->createView(),
    ]);
}, 'GET|POST')
->bind('add');

$app->match(
    '/{id}_{extension}',
    function($id, $extension, Request $request) use ($app) {
        $image = ImageDAO::get($id, $extension);
        $meta = $image->getLatestMeta();

        // Transform array data to a string for the form
        foreach ($meta as $key => $value) {
            if (is_array($value)) {
                $meta[$key] = implode(', ', $value);
            }
        }

        // Create form
        $formBuilder = $app->form($meta);
        foreach ($meta as $key => $value) {
            $formBuilder->add($key, TextType::class, [
                'label' => $key,
                'required' => false
            ]);
        }
        $formBuilder->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
            $data = $form->getData();
            $image->updateMeta($data);

            $app['session']->getFlashBag()->add('message', 'Métadonnées mises à jour !');
            return $app->redirect($app->path('update', [
                'id' => $image->getId(),
                'extension' => $image->getExtension()
            ]));
        }

        return $app->render('update.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
            'utilLinks' => generateUtilLinks($app, $image)
        ]);
    }, 'GET|POST'
)
->bind('update');

$app->match(
    '/{id}_{extension}/delete',
    function($id, $extension, Request $request) use ($app) {
        $image = ImageDAO::get($id, $extension);

        $form = $app->form()->getForm();
        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
            ImageDAO::delete($image);
            $app['session']->getFlashBag()->add('message', "L'image a été supprimée !");
            return $app->redirect($app->path('home'));
        }

        return $app->render('delete.html.twig', [
            'form' => $form->createView(),
            'image' => $image
        ]);
    }, 'GET|POST'
)
->bind('delete');

$app->match(
    '/{id}_{extension}/reset',
    function($id, $extension, Request $request) use ($app) {
        $image = ImageDAO::get($id, $extension);

        $form = $app->form()->getForm();
        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
            $image->resetOriginalMeta();
            $app['session']->getFlashBag()->add('message', "Les métadonnées ont été réinitialisées !");
            return $app->redirect($app->path('update', [
                'id' => $image->getId(),
                'extension' => $image->getExtension()
            ]));
        }

        return $app->render('reset.html.twig', [
            'form' => $form->createView(),
            'image' => $image
        ]);
    }, 'GET|POST'
)
->bind('reset');

$app->match(
    '/{id}_{extension}/undo',
    function($id, $extension, Request $request) use ($app) {
        $image = ImageDAO::get($id, $extension);

        $form = $app->form()->getForm();
        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
            $image->resetLastMeta();
            $app['session']->getFlashBag()->add('message', "Les métadonnées ont été remises à l'ancienne version !");
            return $app->redirect($app->path('update', [
                'id' => $image->getId(),
                'extension' => $image->getExtension()
            ]));
        }

        return $app->render('undo.html.twig', [
            'form' => $form->createView(),
            'image' => $image
        ]);
    }, 'GET|POST'
)
->bind('undo');

$app->get('/about', function(Request $request) use ($app) {
    return $app->render('about.html.twig');
})
->bind('about');

$app->get('/{id}_{extension}/flickr',
    function($id, $extension, Request $request) use ($app) {
        $image = ImageDAO::get($id, $extension);
        return $app->render('flickr.html.twig', [
            'image' => $image
        ]);
})
->bind('flickr');

/**
 * Generate util links
 * @param App application for creating paths
 * @param Image image
 */
function generateUtilLinks(App $app, Image $image) {
    $links = [
        [
            'path' => $app->path('flickr', [
                'id' => $image->getId(),
                'extension' => $image->getExtension()
            ]),
            'name' => "Recherche Flickr",
            'isDownload' => false
        ], [
            'path' => $image->getPath(),
            'name' => 'Image download',
            'isDownload' => true
        ], [
            'path' => $image->getXmpPath(),
            'name' => 'Metadata sidecar XMP download',
            'isDownload' => true
        ]
    ];
    if ($image->hasLatestMeta()) {
        $links[] = [
            'path' => $app->path('undo', [
                'id' => $image->getId(),
                'extension' => $image->getExtension()
            ]),
            'name' => 'Ré-insérer les dernières métadonnées',
            'isDownload' => false
        ];
    }
    $links[] = [
        'path' => $app->path('reset', [
            'id' => $image->getId(),
            'extension' => $image->getExtension()
        ]),
        'name' => 'Ré-insérer les metadonnées originales',
        'isDownload' => false
    ];
    $links[] = [
        'path' => $app->path('delete', [
            'id' => $image->getId(),
            'extension' => $image->getExtension()
        ]),
        'name' => "Supprimer l'image",
        'isDownload' => false
    ];
    return $links;
}
