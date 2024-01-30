<?php
include "Models/adminManager.php";

/**
 * Définition d'une classe permettant de gérer les membres 
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

    function paneladmin($admin, $message)
    {
        if ($admin == 0) {
            $message = "Vous n'avez pas les permissions pour accéder à cette page";
            echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
        } else {
            $projets = $this->adminManager->projetsAValider();
            $membres = $this->adminManager->allUsersInfos();
            $projetsvalides = $this->adminManager->projetsValides();
            $categories = $this->adminManager->getCategories();
            $tags = $this->adminManager->getTags();
            $ressources = $this->adminManager->getRessources();
            echo $this->twig->render('panel_admin.html.twig', array('acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'projets' => $projets, 'membres' => $membres, 'categories' => $categories, 'tags' => $tags, 'ressources' => $ressources, 'projetsvalides' => $projetsvalides, 'message' => $message));
        }
    }

    function validerProjet()
    {
        $idprojet = $_GET['idprojet'];
        $ok = $this->adminManager->changeValideadmin($idprojet);
        $message = $ok ? "Projet validé" : "Erreur lors de la validation du projet";
        $this->paneladmin(1, $message);
    }

    function adminCategorie()
    {
        if (($_POST['intitule'] != "")) {
            $ok = $this->adminManager->ajoutRessource($_POST['semestre'], $_POST['intitule'], $_POST['nomcomplet']);
        }
        if (($_POST['categorie'] != "")) {
            $ok = $this->adminManager->ajoutCategorie($_POST['categorie']);
        }
        if (($_POST['tag'] != "")) {
            $ok = $this->adminManager->ajoutTag($_POST['tag']);
        }
        $message = $ok ? "Les données ont bien été ajoutées" : "Erreur lors de l'ajout des données";
        $this->paneladmin(1, $message);
    }

    function supprCategorie(){
        if (isset($_POST['ressources'])) {
            $ok = $this->adminManager->deleteRessource($_POST['ressources']);
        }
        if (isset($_POST['categories'])) {
            $ok = $this->adminManager->deleteCategorie($_POST['categories']);
        }
        if (isset($_POST['tags'])) {
            $ok = $this->adminManager->deleteTag($_POST['tags']);
        }
        $message = $ok ? "Les données ont bien été supprimées" : "Erreur lors de l'ajout des données";
        $this->paneladmin(1, $message);
    }

    function promouvoirAdmin(){
        $ok = $this->adminManager->setAdmin($_GET['idmembre']);
        $message = $ok ? "Le membre a bien été promu administrateur" : "Erreur lors de la promotion";
        $this->paneladmin(1, $message);
    }
}
