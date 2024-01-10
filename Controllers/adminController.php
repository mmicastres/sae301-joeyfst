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
            echo $this->twig->render('panel_admin.html.twig', array('acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'projets' => $projets, 'membres' => $membres, 'projetsvalides' => $projetsvalides, 'message' => $message));
        }
    }

    function validerProjet()
    {
        $idprojet = $_GET['idprojet'];
        $ok = $this->adminManager->changeValideadmin($idprojet);
        $message = $ok ? "Projet validé" : "Erreur lors de la validation du projet";
        $this->paneladmin(1, $message);
    }
}
