<?php

namespace Demo\TableOfContents;

use Blablacar\Twig\Extension\StampExtension;

$loader = require __DIR__.'/../../vendor/autoload.php';

$twig = new \Twig_Environment(
    new \Twig_Loader_Filesystem(__DIR__.'/view')
);

$extension = new StampExtension();
$twig->addExtension($extension);

$stamp = new TableOfContentsStamp($twig);
$extension->addStamp($stamp);

echo $twig->render('demo.twig');

