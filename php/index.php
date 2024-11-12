<?php
////////////////////////////////////
// BLOC A METTRE DANS UN CONTROLLEUR
// Ajout du code commun à toutes les pages
require_once 'include.php';
require_once 'prerequis.php';

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


$events = getData::getActiveEvents($conn);


// Rendre le template Twig
echo $twig->render('index.html.twig', [
    'breadcrumbs' => $breadcrumbs,
    'title' => 'Accueil',
    // 'description' => 'un site de gestion evenementielle au Pays Basque du Groupe 7'
    'events' => $events,
    'actualites' => $actualite
]);
////////////////////////////////////