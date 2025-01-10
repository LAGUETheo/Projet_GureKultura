<?php 

class ControllerCategorie extends Controller {

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FileSystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function afficher() {
        echo "afficher categorie";
    }

    public function lister() {
        $pdo = Bd::getInstance()->getPdo();

        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader);

        $managerActualite = new ActualiteDao($this->getPdo());
        $actualite = $managerActualite->findAllWithCategorie();    

        $managerCategorie = new CategorieDao($this->getPdo());
        $categorie = $managerCategorie->findAll();        
        $evtSport = $managerCategorie->findEvtSport();
        $evtCult = $managerCategorie->findEvtCult();
        $actuSport = $managerCategorie->findActuSport();
        $actuCult = $managerCategorie->findActuCult();

        // Rendre le template Twig
        echo $this->getTwig()->render('categorie.html.twig', [
            'title' => 'Categorie',
            'actualites' => $actualite,
            'categories' => $categorie,
            'EvtSport' => $evtSport,
            'EvtCult' => $evtCult,
            'ActuSport' => $actuSport,
            'ActuCult' => $actuCult,
        ]);
    }   
}