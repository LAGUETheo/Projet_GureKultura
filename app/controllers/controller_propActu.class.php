<?php
// inclure la classe validator
require_once '../app/controllers/validator.class.php';


/**
 * @class ControllerPropActu
 * @extends parent<Controller>
 * @details Permet de gérer les actions liées à la page "Proposition d'actualité"
 */
class ControllerPropActu extends Controller
{

    /**
     * @constructor ControllerPropActu
     * @details Constructeur de la classe ControllerPropActu
     * @param Twig\Environment $twig
     * @param Twig\Loader\FileSystemLoader $loader
     * @return void
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FileSystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }


    /**
     * @function lister
     * @details Fonction permettant d'afficher la page "Proposition d'actualité" de base
     * @uses ActualiteDao
     * @uses CategorieDao
     * @uses Bd
     * @uses findAllWithCategorie
     * @uses findAll
     * @return void
     */
    public function lister()
    {
        $pdo = Bd::getInstance()->getPdo();

        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader);

        $managerActualite = new ActualiteDao($this->getPdo());
        $actualite = $managerActualite->findAllWithCategorie();

        $managerCategorie = new CategorieDao($this->getPdo());
        $categories = $managerCategorie->findAll();

        // Rendre le template Twig
        echo $this->getTwig()->render('propActu.html.twig', [
            'title' => 'Proposisition d\'actualité',
            'actualites' => $actualite,
            'categories' => $categories
        ]);
    }

    /**
     * @function validerFormulairePropActu
     * @details Fonction permettant de valider le formulaire de proposition d'actualité
     * @uses Validator
     * @uses ActualiteDao
     * @uses CategorieDao
     * @uses Bd
     * @uses findAllWithCategorie
     * @uses findAll
     * @uses insererDonneesDansLaBase
     * @return void
     */
    public function validerFormulairePropActu()
    {
        $userCo = $_SESSION['user'];
        if (isset($userCo) && !empty($userCo) && $userCo->getEstAdmin() == true) {
            // L'utilisateur est connecté, continuez
            // definition des regles de validations que l'on souhaite verifier pour chaque champs du formulaire
            $regleValidation = [
                'titre' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueurMin' => 5,
                    'longueurMax' => 50,
                    'format' => '/^[a-zA-Z0-9\s]+$/'
                ],
                'cateId' => [
                    'obligatoire' => true,
                    'type' => 'integer',
                    'longueurMin' => 1,
                    'longueurMax' => 100,
                    'format' => '/^[a-zA-Z0-9\s]+$/'
                ],
                'resume' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueurMin' => 30,
                    'longueurMax' => 500,
                    'format' => '/^[a-zA-Z0-9\s]+$/'
                ],
                'contenu' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueurMin' => 30,
                    'longueurMax' => 500,
                    'format' => '/^[a-zA-Z0-9\s]+$/'
                ],
                'image' => [
                    'obligatoire' => false,
                    'type' => 'string',
                    'longueurMin' => 5,
                    'longueurMax' => 100,
                    'format' => '/^[a-zA-Z0-9\s]+$/'
                ]
            ];

            // instanciation de la classe de validation
            $validator = new Validator($regleValidation);

            // recuperation des donnees du formulaire
            $donnees = $_POST;

            // boucle de nettoyage des donnees
            foreach ($donnees as $key => $value) {
                $donnees[$key] = htmlentities($value);
            }

            $user = $_SESSION['user'];
            $donnees['userId'] = $user->getUserId();

            // validation des donnees du formulaire
            $donneesValides = $validator->valider($donnees);

            if (!$donneesValides) {
                $messageErreurs = $validator->getMessageErreurs();
            }

            // recuperation des erreurs

            // Rendre le template Twig
            $pdo = Bd::getInstance()->getPdo();

            $loader = new \Twig\Loader\FilesystemLoader('../templates');
            $twig = new \Twig\Environment($loader);

            $managerActualite = new ActualiteDao($this->getPdo());
            $actualite = $managerActualite->findAllWithCategorie();

            $managerCategorie = new CategorieDao($this->getPdo());
            $categories = $managerCategorie->findAll();

            if (!empty($messageErreurs)) {
                // Les données ne sont pas valides, affichez les erreurs
                echo $this->getTwig()->render('propActu.html.twig', [
                    'title' => 'Proposition d\'actualité',
                    'messageErreurs' => $messageErreurs,
                    'donnees' => $donnees,
                    'actualites' => $actualite,
                    'categories' => $categories
                ]);
            } else {
                echo $this->getTwig()->render('propActu.html.twig', [
                    'title' => 'Proposition d\'actualité',
                    'donnees' => $donnees,
                    'actualites' => $actualite,
                    'categories' => $categories

                ]);


                // Les données sont valides, insérez-les dans la base de données
                $this->insererDonneesDansLaBase($donnees);
            }
        } else {
            // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
            header('Location: index.php?controlleur=connexion&methode=lister');
        }

    }


    /**
     * @function insererDonneesDansLaBase
     * @details Fonction permettant d'insérer les données du formulaire dans la base de données
     * @param array $donnees
     * @uses ActualiteDao
     * @uses Bd
     * @uses Actualite
     * @uses insert
     * @return void
     */
    private function insererDonneesDansLaBase(array $donnees)
    {
        try {
            $pdo = Bd::getInstance()->getPdo();
            $managerActualite = new ActualiteDao($pdo);

            // Créez un nouvel objet Evenement avec les données du formulaire
            $actualite = new Actualite(
                null,
                $donnees['titre'],
                $donnees['resume'],
                $donnees['contenu'],
                null,
                $donnees['image'] ?? null,
                $donnees['userId'],
                $donnees['cateId']
            );

            // Log the event data for debugging
            error_log(print_r($actualite, true));
            // Insérez l'événement dans la base de données
            $managerActualite->insert($actualite);
        } catch (Exception $e) {
            // Log the error message
            error_log("Error inserting event: " . $e->getMessage());
            throw $e; // Re-throw the exception if needed
        }
    }
}