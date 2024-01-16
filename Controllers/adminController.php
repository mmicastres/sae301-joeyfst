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

    function adminCategorie()
    {
        if (isset($_POST['intitule'])) {
            $ok = $this->adminManager->ajoutRessource($_POST['semestre'], $_POST['intitule'], $_POST['nomcomplet']);
        }
        if (isset($_POST['categorie'])) {
            $ok = $this->adminManager->ajoutCategorie($_POST['categorie']);
        }
        if (isset($_POST['tag'])) {
            $ok = $this->adminManager->ajoutTag($_POST['tag']);
        }
        $message = $ok ? "Les données ont bien été ajoutées" : "Erreur lors de l'ajout des données";
        $this->paneladmin(1, $message);
    }

    function formModifierMembre($admin)
    {
        if ($admin == 0) {
            $message = "Vous n'avez pas les permissions pour accéder à cette page";
            echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
        } else {
            $idmembre = $_GET['idmembre'];
            $infos = $this->adminManager->getInfosMembre($idmembre);
            echo $this->twig->render('modifier_membre.html.twig', array('acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'infos' => $infos));
        }
    }

    function modifierMembre()
    {
        var_dump($_POST);
        var_dump($_FILES);
        $extensions = array('jpg', 'png', 'jpeg', 'gif');
        if ($_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
            $_POST['photo'] = $_FILES["photo"]["name"];
            $file_name = explode('.', $_FILES["photo"]["name"]);
            $extension = end($file_name);
            if (!in_array($extension, $extensions)) {
                $message = "Le fichier n'est pas une image";
                echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
                return;
            }

            // récupération du nom de l'ancienne photo de profil et suppression sur le serveur
            $anciennepdp = "./Views/imgprofil/" . $_POST['anciennepdp'];
            if (is_file($anciennepdp)) {
                echo (unlink($anciennepdp)) ? "Fichier supprimé" : "Problème lors de la suppression du fichier";
            }

            // recupération du fichier photo
            if ($_FILES["photo"]["error"] == UPLOAD_ERR_OK) { // verif si pas d'erreur
                // déplacer le fichier temporaire sur un repertoire du serveur web
                $uploaddir = "./Views/imgprofil/"; // chemin du dossier où ranger les fichiers
                $uploadfile = $uploaddir . basename($_FILES["photo"]["name"]); // nom initial du fichier
                // on déplace le fichier du dossier temporaire du serveur web
                // vers le dossier approprié du site web et on renomme le fichier
                if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $uploadfile)) {
                    echo "pb lors du telechargement";
                }
            } else {
                // traitement des erreurs
                var_dump("test");
                echo "pas de fichier";
            }
        }

        // conversion des données de la date et du fichier
        $_POST['anneenaissance'] = intval(explode('-', $_POST['anneenaissance'])[0]);

        // insertion des nouvelles données dans la table
        $membre = new Membre($_POST);
        $ok = $this->adminManager->validerModifMembre($membre);
        $message = $ok ? "Le membre a bien été modifié" : "Erreur lors de la modification du membre";
        $this->paneladmin(1, $message);
    }
}
