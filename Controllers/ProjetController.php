<?php
include "Modules/projet.php";
include "Models/projetManager.php";
/**
 * Définition d'une classe permettant de gérer les itinéraires 
 *   en relation avec la base de données	
 */
class ProjetController
{

	private $projetManager; // instance du manager
	private $twig;

	/**
	 * Constructeur = initialisation de la connexion vers le SGBD
	 */
	public function __construct($db, $twig)
	{
		$this->projetManager = new ProjetManager($db);
		$this->twig = $twig;
	}

	/**
	 * liste de tous les projets
	 * @param aucun
	 * @return rien
	 */
	public function listeProjets()
	{
		$projets = $this->projetManager->getListProjets();
		$categories = $this->projetManager->allCategories();
		$tags = $this->projetManager->allTags();
		$ressources = $this->projetManager->allRessources();
		$images[] = "";
		foreach ($projets as $projet) {
			$images[] = $this->projetManager->projetImages($projet->idprojet());
		}
		echo $this->twig->render('projet_liste.html.twig', array('projets' => $projets, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'categories' => $categories, 'tags' => $tags, 'ressources' => $ressources, 'images' => $images));
	}

	public function infoProjet($data)
	{
		$idprojet = $_GET['idprojet'];
		$projet = $this->projetManager->projetInfo($idprojet);
		$categories = $this->projetManager->projetCategories($idprojet);
		$tags = $this->projetManager->projetTags($idprojet);
		$images = $this->projetManager->projetImages($idprojet);
		$ressources = $this->projetManager->projetRessources($idprojet);
		$contributeurs = $this->projetManager->projetContributeurs($idprojet);

		// Calcul des notes
		$notes = $this->projetManager->getNotes($idprojet);
		if ($notes == false) {
			$notes = 0;
			$moyenne = "0";
		} else {
			$moyenne = 0;
			for ($i = 0; $i < count($notes); $i++) {
				$moyenne = $moyenne + $notes[$i]['note'];
			}
			$moyenne = $moyenne / count($notes);
		}

		$commentaires = $this->projetManager->getCommentaires($idprojet);
		if ($commentaires == false) {
			$commentaires = 0;
		}

		if (isset($_SESSION['idmembre'])) {
			$estContributeur = $this->projetManager->verifContributeur($idprojet, $_SESSION['idmembre']);
		} else {
			$estContributeur = false;
		}

		if (!isset($message)) {
			$message = "";
		}

		echo $this->twig->render('projet_info.html.twig', array('projet' => $projet, 'categories' => $categories, 'tags' => $tags, 'images' => $images, 'ressources' => $ressources, 'contributeurs' => $contributeurs, 'estContributeur' => $estContributeur, 'notes' => $notes, 'moyenne' => $moyenne, 'commentaires' => $commentaires, 'acces' => $_SESSION['acces'], 'idmembre' => $_SESSION['idmembre'], 'admin' => $_SESSION['admin'], 'message' => $message));
	}


	/**
	 * formulaire ajout
	 * @param aucun
	 * @return rien
	 */
	public function formAjoutProjet()
	{
		// catégories tags images ressources contributeurs
		$categories = $this->projetManager->allCategories();
		$tags = $this->projetManager->allTags();
		$ressources = $this->projetManager->allRessources();
		$membres = $this->projetManager->allUsers();
		echo $this->twig->render('projet_ajout.html.twig', array('categories' => $categories, 'tags' => $tags, 'ressources' => $ressources, 'membres' => $membres, 'acces' => $_SESSION['acces'], 'idmembre' => $_SESSION['idmembre'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * ajout dans la BD d'un iti à partir du form
	 * @param aucun
	 * @return rien
	 */
	public function ajoutProjet()
	{
		$projet = new Projet($_POST);
		$ok = $this->projetManager->addProjet($projet);
		$idprojet = $projet->idprojet();

		$categories = $_POST['categories'];
		if ($categories[0] != "") {
			foreach ($categories as $categorie) {
				if ($categorie != "") {
					$this->projetManager->linkProjetCategories($idprojet, $categorie);
				}
			}
		}
		$tags = $_POST['tags'];
		if ($tags[0] != "") {
			foreach ($tags as $tag) {
				if ($tag != "") {
					$this->projetManager->linkProjetTags($idprojet, $tag);
				}
			}
		}
		$ressources = $_POST['ressources'];
		if ($ressources[0] != "") {
			foreach ($ressources as $ressource) {
				if ($ressource) {
					$this->projetManager->linkProjetRessources($idprojet, $ressource);
				}
			}
		}
		$contributeurs = $_POST['contributeurs'];
		if ($contributeurs[0] != "") {
			foreach ($contributeurs as $contributeur) {
				if ($contributeur != "") {
					$this->projetManager->linkProjetContributeurs($idprojet, $contributeur);
				}
			}
		}

		$this->projetManager->linkProjetContributeurs($idprojet, $_SESSION['idmembre']);

		// Traitement des images du projet
		// Premièrement, on l'ajoute sur le serveur
		$extensions = array('jpg', 'png', 'jpeg', 'gif');
		if (isset($_FILES['images']['name'])) {
			$file_name = explode('.', $_FILES["images"]["name"]);
			$extension = end($file_name);
			if (!in_array($extension, $extensions)) {
				$message = "Le fichier n'est pas une image";
				echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
				return;
			}
			if ($_FILES["images"]["error"] == UPLOAD_ERR_OK) {
				$uploaddir = "./Views/imgprojet/";
				$uploadfile = $uploaddir . basename($_FILES["images"]["name"]);
				if (!move_uploaded_file($_FILES["images"]["tmp_name"], $uploadfile)) {
					echo "pb lors du telechargement";
				}
			} else {
				echo "pas de fichier";
			}

			// Deuxièmement, on l'ajoute à la base de données

			$nomimg = $_FILES["images"]["name"];
			$idimg = $this->projetManager->addImgProjet($nomimg);
			$this->projetManager->linkProjetImages($idprojet, $idimg);
		}


		$message = $ok ? "Projet ajouté" : "probleme lors de l'ajout";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * form de choix du projet à modifier
	 * @param aucun
	 * @return rien
	 */
	public function formModifProjet()
	{
		$idprojet = $_GET['idprojet'];
		$projet = $this->projetManager->projetInfo($idprojet);
		$categories = $this->projetManager->projetCategories($idprojet);
		$allCategories = $this->projetManager->allCategories();
		$tags = $this->projetManager->projetTags($idprojet);
		$allTags = $this->projetManager->allTags();
		$images = $this->projetManager->projetImages($idprojet);
		$ressources = $this->projetManager->projetRessources($idprojet);
		$allRessources = $this->projetManager->allRessources();
		$contributeurs = $this->projetManager->projetContributeurs($idprojet);
		$allUsers = $this->projetManager->allUsers();
		$estContributeur = $this->projetManager->verifContributeur($idprojet, $_SESSION['idmembre']);
		echo $this->twig->render('projet_modification.html.twig', array('projet' => $projet, 'categories' => $categories, 'allCategories' => $allCategories, 'tags' => $tags, 'allTags' => $allTags, 'images' => $images, 'ressources' => $ressources, 'allRessources' => $allRessources, 'contributeurs' => $contributeurs, 'allUsers' => $allUsers, 'estContributeur' => $estContributeur, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}
	/**
	 * modification de l'itinéraire
	 * @param aucun
	 * @return rien
	 */
	public function modifProjet()
	{
		$projet = new Projet($_POST);
		$idprojet = $projet->idprojet();
		$ok = $this->projetManager->modifProjet($projet);
		$this->projetManager->deleteProjetCategories($idprojet);
		$this->projetManager->deleteProjetTags($idprojet);
		$this->projetManager->deleteProjetRessources($idprojet);
		$this->projetManager->deleteProjetContributeurs($idprojet);


		$categories = $_POST['categories'];
		if ($categories[0] != "") {
			foreach ($categories as $categorie) {
				if ($categorie != "") {
					$this->projetManager->linkProjetCategories($idprojet, $categorie);
				}
			}
		}
		$tags = $_POST['tags'];
		if ($tags[0] != "") {
			foreach ($tags as $tag) {
				if ($tag != "") {
					$this->projetManager->linkProjetTags($idprojet, $tag);
				}
			}
		}
		$ressources = $_POST['ressources'];
		if ($ressources[0] != "") {
			foreach ($ressources as $ressource) {
				if ($ressource) {
					$this->projetManager->linkProjetRessources($idprojet, $ressource);
				}
			}
		}
		$contributeurs = $_POST['contributeurs'];
		if ($contributeurs[0] != "") {
			foreach ($contributeurs as $contributeur) {
				if ($contributeur != "") {
					$this->projetManager->linkProjetContributeurs($idprojet, $contributeur);
				}
			}
		}
		$estcontributeur = $_POST['estcontributeur'];
		if ($estcontributeur == "oui") {
			$this->projetManager->linkProjetContributeurs($idprojet, $_SESSION['idmembre']);
		}

		var_dump($_FILES["images"]["name"][1]);

		$extensions = array('jpg', 'png', 'jpeg', 'gif');
		if (!is_uploaded_file($_FILES['images']['tmp_name'][0])) {
			$idimages = $this->projetManager->getIdImages($idprojet);
			$lienimages = $this->projetManager->projetImages($idprojet);
			if ($lienimages != null) {
				foreach ($lienimages as $image) {
					$image = "./Views/imgprojet/" . $image;
					if (is_file($image)) {
						unlink($image);
					}
				}
			}
			$this->projetManager->deleteImages($idimages);
			$this->projetManager->deleteProjetImages($idprojet);
			for ($i = 0; $i <= count($_FILES['images']); $i++) {
				$file_name = explode('.', $_FILES["images"][$i]["name"]);
				$extension = end($file_name);
				var_dump($_FILES["images"]["name"][$i]);
				var_dump($extension);
				if (!in_array($extension, $extensions)) {
					$message = "Le fichier n'est pas une image";
					echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message, 'admin' => $_SESSION['admin']));
					return;
				}

				if ($_FILES["images"]["error"][$i] == UPLOAD_ERR_OK) {
					$uploaddir = "./Views/imgprojet/";
					$uploadfile = $uploaddir . basename($_FILES["images"][$i]["name"]);
					if (!move_uploaded_file($_FILES["images"][$i]["tmp_name"], $uploadfile)) {
						echo "pb lors du telechargement";
					}
				} else {
					echo "pas de fichier";
				}

				// Deuxièmement, on l'ajoute à la base de données
				$nomimg = $_FILES["images"][$i]["name"];
				$idimg = $this->projetManager->addImgProjet($nomimg);
				$this->projetManager->linkProjetImages($idprojet, $idimg);
			}
		}

		if (isset($_POST['supprimg'])) {
			$idimages = $this->projetManager->getIdImages($idprojet);
			$lienimages = $this->projetManager->projetImages($idprojet);
			var_dump($idimages, $lienimages);
			foreach ($lienimages as $image) {
				$image = "./Views/imgprojet/" . $image;
				if (is_file($image)) {
					unlink($image);
				}
			}
		}

		$message = $ok ? "Projet mis à jour" : "probleme lors de la mise à jour";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * suppression dans la BD d'un projet à partir de l'id choisi dans le form précédent
	 * @param aucun
	 * @return rien
	 */
	public function supprProjet()
	{
		$idprojet = $_GET['idprojet'];
		$this->projetManager->deleteProjetCategories($idprojet);
		$this->projetManager->deleteProjetTags($idprojet);
		$this->projetManager->deleteProjetRessources($idprojet);
		$this->projetManager->deleteProjetContributeurs($idprojet);

		$idimages = $this->projetManager->getIdImages($idprojet);
		$this->projetManager->deleteImages($idimages);
		$this->projetManager->deleteProjetImages($idprojet);
		$ok = $this->projetManager->deleteProjet($idprojet);
		$message = $ok ?  "Projet supprimé" : "probleme lors de la supression";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * recherche dans la BD d'iti à partir des données du form précédent
	 * @param aucun
	 * @return rien
	 */
	public function rechercheProjet()
	{
		ini_set('display_errors', 0);
		$projets = $this->projetManager->search($_POST["titre"], $_POST["description"], $_POST["categories"], $_POST["tags"], $_POST["ressources"]);
		$categories = $this->projetManager->allCategories();
		$tags = $this->projetManager->allTags();
		$ressources = $this->projetManager->allRessources();
		$images[] = "";
		foreach ($projets as $projet) {
			$images[] = $this->projetManager->projetImages($projet->idprojet());
		}
		echo $this->twig->render('projet_liste.html.twig', array('projets' => $projets, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin'], 'categories' => $categories, 'tags' => $tags, 'ressources' => $ressources, 'images' => $images));
	}

	public function commenterProjet()
	{
		if ($_POST['note'] != "") {
			intval($_POST['note']);
			$this->projetManager->deleteNote($_POST['idprojet'], $_SESSION['idmembre']);
			$ok = $this->projetManager->addNote($_POST['idprojet'], $_SESSION['idmembre'], $_POST['note']);
		}
		if ($_POST['commentaire'] != "") {
			$ok = $this->projetManager->addCommentaire($_POST['idprojet'], $_SESSION['idmembre'], $_POST['commentaire']);
		}
		$message = $ok ? 'Vos données ont été ajoutées' : "Problème lors de l'envoi des données";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}
}
