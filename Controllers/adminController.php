<?php
include "Models/adminManager.php";

/**
 * Définition d'une classe permettant de gérer les commandes administrateurs
 *   en relation avec la base de données	
 */
class adminController
{
    private $adminManager; // instance du manager
    private $twig;

    /**
     * Constructeur = initialisation de la connexion vers le SGBD
     */
    public function __construct($db, $twig)
    {
        $this->adminManager = new adminManager($db);
        $this->twig = $twig;
    }

    /**
     * affiche le panneau d'administration
     * @param admin
     * @return Projets, membrescatégories, tags et ressources
     */
    function paneladmin($admin, $message)
    {
        if ($admin == 0) { // vérification que le membre est bien administrateur
            $message = "Vous n'avez pas les permissions pour accéder à cette page";
            echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
        } else { // récupération de tous les projets et données
            $projets = $this->adminManager->projetsAValider();
            $membres = $this->adminManager->allUsersInfos();
            $projetsvalides = $this->adminManager->projetsValides();
            $categories = $this->adminManager->getCategories();
            $tags = $this->adminManager->getTags();
            $ressources = $this->adminManager->getRessources();
            foreach ($projets as $projet) {
                $image = $this->adminManager->projetImagePrincipale($projet->idprojet());
                if ($image != false) {
                    $projet->setImagePrincipale($image);
                }
            }
            foreach ($projetsvalides as $projet) {
                $image = $this->adminManager->projetImagePrincipale($projet->idprojet());
                if ($image != false) {
                    $projet->setImagePrincipale($image);
                }
            }
            echo $this->twig->render('panel_admin.html.twig', array('acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'projets' => $projets, 'membres' => $membres, 'categories' => $categories, 'tags' => $tags, 'ressources' => $ressources, 'projetsvalides' => $projetsvalides, 'message' => $message));
        }
    }

    /**
     * valide un projet dans la base de données pour qu'il soit affiché publiquement dans la liste des projets
     * @param idprojet
     * @return message
     */
    function validerProjet()
    {
        $idprojet = $_GET['idprojet'];
        $ok = $this->adminManager->changeValideadmin($idprojet);
        $message = $ok ? "Projet validé" : "Erreur lors de la validation du projet";
        $this->paneladmin(1, $message);
    }

    /**
     * ajout d'une catégorie, d'un tag ou d'une ressource
     * @param formulaire
     * @return message
     */
    function adminCategorie()
    {
        if (($_POST['intitule'] != "")) { // on regarde si le champ correspondant dans le formulaire est rempli
            $ok = $this->adminManager->ajoutRessource($_POST['semestre'], $_POST['intitule'], $_POST['nomcomplet']);
        }
        if (($_POST['categorie'] != "")) { // on regarde si le champ correspondant dans le formulaire est rempli
            $ok = $this->adminManager->ajoutCategorie($_POST['categorie']);
        }
        if (($_POST['tag'] != "")) { // on regarde si le champ correspondant dans le formulaire est rempli
            $ok = $this->adminManager->ajoutTag($_POST['tag']);
        }
        $message = $ok ? "Les données ont bien été ajoutées" : "Erreur lors de l'ajout des données";
        $this->paneladmin(1, $message);
    }

    /**
     * suppression d'une catégorie, d'un tag ou d'une ressource
     * @param formulaire
     * @return message
     */
    function supprCategorie()
    {
        if (isset($_POST['ressources'])) { // on regarde si le champ correspondant dans le formulaire est rempli
            $ok = $this->adminManager->deleteRessource($_POST['ressources']);
        }
        if (isset($_POST['categories'])) { // on regarde si le champ correspondant dans le formulaire est rempli
            $ok = $this->adminManager->deleteCategorie($_POST['categories']);
        }
        if (isset($_POST['tags'])) { // on regarde si le champ correspondant dans le formulaire est rempli
            $ok = $this->adminManager->deleteTag($_POST['tags']);
        }
        $message = $ok ? "Les données ont bien été supprimées" : "Erreur lors de l'ajout des données";
        $this->paneladmin(1, $message);
    }

    /**
     * rend le membre choisi administrateur
     * @param idmembre, admin
     * @return message
     */
    function promouvoirAdmin()
    {
        if ($_SESSION["admin"] == 1) { // on vérifie que le membre effectuant l'action est bien administrateur
            $ok = $this->adminManager->setAdmin($_GET['idmembre']);
            $message = $ok ? "Le membre a bien été promu administrateur" : "Erreur lors de la promotion";
            $this->paneladmin(1, $message);
        } else {
            $message = "Vous n'avez pas les droits pour effectuer cette action";
            echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
        }
    }

    /**
     * supprime définitivement le membre choisi
     * @param idmembre, admin
     * @return message
     */
    function supprimerMembre()
    {
        if ($_SESSION["admin"] == 1) { // on vérifie que le membre effectuant l'action est bien administrateur
            $idmembre = $_GET['idmembre'];
            $this->adminManager->deleteUserContributions($idmembre); // suppression de toutes les contributions, notes et commentaires du membre
            $this->adminManager->deleteUserNotes($idmembre);
            $this->adminManager->deleteUserCommentaires($idmembre);
            $ok = $this->adminManager->deleteUser($idmembre);
            $message = $ok ? "Le membre a bien été supprimé" : "Erreur lors de la suppression";
            $this->paneladmin(1, $message);
        } else {
            $message = "Vous n'avez pas les droits pour effectuer cette action";
            echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
        }
    }
}
