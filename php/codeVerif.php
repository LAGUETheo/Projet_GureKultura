<?php
////////////////////////////////////
// BLOC A METTRE DANS UN CONTROLLEUR
// Ajout du code commun à toutes les pages
require_once 'include.php';
$pdo = Bd::getInstance()->getPdo();
$managerActualite = new ActualiteDao($pdo);
$actualite = $managerActualite->findAll();


// Rendre le template Twig
echo $twig->render('codeVerif.html.twig', [
    'title' => 'Code de vérification',
    'actualites' => $actualite
]);
////////////////////////////////////
