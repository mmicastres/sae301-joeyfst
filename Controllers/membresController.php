<?php
include "Modules/membre.php";
include "Models/membreManager.php";

/**
 * Définition d'une classe permettant de gérer les membres 
 *   en relation avec la base de données	
 */
class MembreController
{
	private $membreManager; // instance du manager
	private $twig;

	/**
	 * Constructeur = initialisation de la connexion vers le SGBD
	 */
	public function __construct($db, $twig)
	{
		$this->membreManager = new MembreManager($db);
		$this->twig = $twig;
	}

	/**
	 * connexion
	 * @param aucun
	 * @return rien
	 */
	function membreConnexion($data)
	{
		// verification du login et mot de passe
		$membre = $this->membreManager->verif_identification($_POST['email'], $_POST['mdp']);
		if ($membre != false) { // acces autorisé : variable de session acces = oui, on récupère l'idmembre et l'admin
			$_SESSION['acces'] = "oui";
			$_SESSION['admin'] = $membre->admin();
			$_SESSION['idmembre'] = $membre->idMembre();
			$message = "Bonjour " . $membre->prenom() . " " . $membre->nom() . " !";
			echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'message' => $message));
		} else { // acces non autorisé : variable de session acces = non, retour à la page d'accueil
			$message = "identification incorrecte";
			$_SESSION['acces'] = "non";
			echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
		}
	}

	function membreInscription($data)
	{
		// recupération du fichier photo
		$_POST['photo'] = $_FILES["photo"]["name"];

		$membre = new Membre($_POST);
		//vérification de l'adresse email au format etu.iut-tlse3.fr
		$email = $this->verif_email($membre->email());
		if ($email == true) {
			// insertion des nouvelles données dans la table
			$this->membreManager->new_inscription($membre);

			// filtre de sécurité pour empêcher n'importe qui d'envoyer du code exécutable (PHP notamment)
			$extensions = array('jpg', 'png', 'jpeg', 'gif');
			if (isset($_FILES["photo"]["name"])) {
				$file_name = explode('.', $_FILES["photo"]["name"]);
				$extension = end($file_name);
				// on compare la fin du nom du fichier (l'extension) à toutes les extensions acceptées (définies dans $extensions)
				if (!in_array($extension, $extensions)) {
					$message = "Le fichier n'est pas une image";
					echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
					return;
				}
			}

			// upload du fichier sur le serveur
			if ($_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
				$uploaddir = "./Views/imgprofil/";
				$uploadfile = $uploaddir . basename($_FILES["photo"]["name"]);
				if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $uploadfile)) {
					echo "pb lors du telechargement";
				}
			} else {
				echo "pas de fichier";
			}

			// une fois l'inscription réussie, connexion
			if ($membre != false) { // acces autorisé : variable de session acces = oui, on récupère l'idmembre et l'admin
				$_SESSION['acces'] = "oui";
				$_SESSION['idmembre'] = $membre->idMembre();
				$message = "Bienvenue " . $membre->prenom() . " " . $membre->nom() . " !";
				echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
			} else { // acces non autorisé : variable de session acces = non
				$message = "identification incorrecte";
				$_SESSION['acces'] = "non";
				echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
			}
		} else {
			// si le mail n'est pas bon, retour à la page d'inscription
			$message = "l'email n'est pas valide, il faut un email de l'académie de Toulouse";
			echo $this->twig->render('membre_inscription.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
		}
	}

	/**
	 * deconnexion
	 * @param aucun
	 * @return rien
	 */
	function membreDeconnexion()
	{
		$_SESSION['acces'] = "non"; // acces non autorisé
		$_SESSION['admin'] = 0; // plus de droits d'admin
		$_SESSION['idmembre'] = "";
		$message = "Vous avez été déconnecté";
		echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
	}

	/**
	 * formulaire de connexion
	 * @param aucun
	 * @return rien
	 */
	function membreFormulaireConnexion()
	{
		echo $this->twig->render('membre_connexion.html.twig', array('acces' => $_SESSION['acces']));
	}

	/**
	 * formulaire d'inscription
	 * @param aucun
	 * @return rien
	 */
	function membreFormulaireInscription()
	{
		echo $this->twig->render('membre_inscription.html.twig', array('acces' => $_SESSION['acces']));
	}

	/**
	 * accès espace utilisateur
	 * @param idmembre
	 * @return infos du membre
	 */

	function espaceUtilisateur($idmembre)
	{
		$infos = $this->membreManager->getInfosMembre($idmembre);
		$projets = $this->membreManager->getProjetsMembre($idmembre);
		if ($projets != null) { // on vérifie si l'utilisateur a déjà des projets ou non
			foreach ($projets as $projet) { // boucle qui permet de définir l'image de fond du projet sur la card
				$image = $this->membreManager->projetImagePrincipale($projet->idprojet());
				if ($image != false) {
					$projet->setImagePrincipale($image);
				}
			}
		}
		echo $this->twig->render('espace_utilisateur.html.twig', array('infos' => $infos, 'projets' => $projets, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * accès au formulaire de modification du membre, accessible en tant que membre pour ses infos ou en tant qu'admin pour tous
	 * @param idmembre ou admin
	 * @return infos du membre
	 */
	function formModifierMembre($admin)
	{
		if ($admin == 1) {
			$infos = $this->membreManager->getInfosMembre($_GET['idmembre']);
		} else {
			$infos = $this->membreManager->getInfosMembre($_SESSION['idmembre']);
		}
		if ($admin == 0 && $_SESSION['idmembre'] != $_GET['idmembre']) { // tentative d'accès à la page en n'étant ni admin ni le bon membre
			$message = "Vous n'avez pas les permissions pour accéder à cette page";
			echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
		} else {
			echo $this->twig->render('modifier_membre.html.twig', array('acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'infos' => $infos));
		}
	}

	/**
	 * modification des informations dans la base de données
	 * @param formulaire
	 * @return 
	 */
	function modifierMembre()
	{
		// insertion des nouvelles données dans la table
		$membre = new Membre($_POST);
		$email = $this->verif_email($membre->email());
		if ($email == true) { //vérification que l'email contient bien etu.iut-tlse3.fr
			$this->membreManager->validerModifMembre($membre);
			$this->espaceUtilisateur($_SESSION['idmembre']);
		} else {
			$message = "l'email n'est pas valide, il faut un email de l'académie de Toulouse";
			echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
		}
	}


	// vérification que l'email de l'utilisateur est bien une adresse @etu.iut-tlse3.fr, partiellement reprise à Safwa et Elsa avec leur accord, merci à elles !
	function verif_email($email)
	{
		if ($email !== false) {
			$email = (string) $email; // check que l'email soit a string 

			// Check si l'email utilise "etu.iut-tlse3.fr"
			$pattern = "/@etu.iut-tlse3.fr$/";

			if (preg_match($pattern, $email)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * changement de photo de profil du membre
	 * @param fichier
	 * @return 
	 */
	function changerPdp()
	{
		$extensions = array('jpg', 'png', 'jpeg', 'gif');
		if (isset($_FILES["photo"]["name"])) {
			$file_name = explode('.', $_FILES["photo"]["name"]);
			$extension = end($file_name); // on vérifie que l'extension du fichier est bien une image, en la séparant dans un tableau
			if (!in_array($extension, $extensions)) {
				$message = "Le fichier n'est pas une image";
				echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
				return;
			}

			// récupération du nom de l'ancienne photo de profil et suppression sur le serveur
			$anciennepdp = "./Views/imgprofil/" . $_POST['anciennepdp'];
			if (is_file($anciennepdp)) {
				unlink($anciennepdp);
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

			//changement sur la base de données
			$photo = $_FILES["photo"]["name"];
			$ok = $this->membreManager->changementPdp($photo, $_SESSION['idmembre']);
			if ($ok == true) {
				$this->espaceUtilisateur($_SESSION['idmembre']);
			}
		} else {
			$message = "pas de fichier";
			echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
		}
	}
}
