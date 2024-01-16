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
		echo $this->twig->render('projet_liste.html.twig', array('projets' => $projets, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
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
		if (isset($_SESSION['idmembre'])) {
			$estContributeur = $this->projetManager->verifContributeur($idprojet, $_SESSION['idmembre']);
		}else{
			$estContributeur = false;
		}
		echo $this->twig->render('projet_info.html.twig', array('projet' => $projet, 'categories' => $categories, 'tags' => $tags, 'images' => $images, 'ressources' => $ressources, 'contributeurs' => $contributeurs, 'estContributeur' => $estContributeur, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
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
		$this->projetManager->linkProjetCategories($idprojet, $_POST['categories']);
		$this->projetManager->linkProjetTags($idprojet, $_POST['tags']);
		$this->projetManager->linkProjetRessources($idprojet, $_POST['ressources']);
		$this->projetManager->linkProjetContributeurs($idprojet, $_POST['contributeurs']);

		// Traitement des images du projet
		// Premièrement, on l'ajoute sur le serveur
		$extensions = array('jpg', 'png', 'jpeg', 'gif');
		if (isset($_FILES["images"]["name"])) {
			$file_name = explode('.', $_FILES["images"]["name"]);
			$extension = end($file_name);
			if (!in_array($extension, $extensions)) {
				$message = "Le fichier n'est pas une image";
				echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
				return;
			}
			if ($_FILES["images"]["error"] == UPLOAD_ERR_OK) {
				$uploaddir = "./Views/imgprojet/";
				$uploadfile = $uploaddir . basename($_FILES["images"]["name"]);
				if (!move_uploaded_file($_FILES["images"]["tmp_name"], $uploadfile)) {
					echo "pb lors du telechargement";
				}
			} else {
				var_dump("test");
				echo "pas de fichier";
			}
		}
		// Deuxièmement, on l'ajoute à la base de données

		$nomimg = $_FILES["images"]["name"];
		$idimg = $this->projetManager->addImgProjet($nomimg);
		$this->projetManager->linkProjetImages($idprojet, $idimg);

		$message = $ok ? "Projet ajouté" : "probleme lors de l'ajout";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * form de choix de l'iti à modifier
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
	 * form de saisi des nouvelles valeurs de l'iti à modifier
	 * @param aucun
	 * @return rien
	 */
	public function modifProjet()
	{
		$projet = new Projet($_POST);
		$idprojet = $projet->idprojet();
		$ok = $this->projetManager->modifProjet($projet);
		$this->projetManager->modifProjetCategories($idprojet, $_POST['categories']);
		$this->projetManager->modifProjetTags($idprojet, $_POST['tags']);
		$this->projetManager->modifProjetRessources($idprojet, $_POST['ressources']);
		$this->projetManager->modifProjetContributeurs($idprojet, $_POST['contributeurs']);


		// // Traitement des images du projet
		// // Premièrement, récupération du nom des anciennes images du projet et suppression sur le serveur
		// $ancienneimg = "./Views/imgprojet/" . $_POST['ancienneimg'];
		// if (is_file($ancienneimg)) {
		// 	echo (unlink($ancienneimg)) ? "Fichier supprimé" : "Problème lors de la suppression du fichier";
		// }

		// // Deuxièmement, on l'ajoute sur le serveur
		// $extensions = array('jpg', 'png', 'jpeg', 'gif');
		// if (isset($_FILES["images"]["name"])) {
		// 	$file_name = explode('.', $_FILES["images"]["name"]);
		// 	$extension = end($file_name);
		// 	if (!in_array($extension, $extensions)) {
		// 		$message = "Le fichier n'est pas une image";
		// 		echo $this->twig->render('index.html.twig', array('acces' => $_SESSION['acces'], 'message' => $message));
		// 		return;
		// 	}

		// 	if ($_FILES["images"]["error"] == UPLOAD_ERR_OK) {
		// 		$uploaddir = "./Views/imgprojet/";
		// 		$uploadfile = $uploaddir . basename($_FILES["images"]["name"]);
		// 		if (!move_uploaded_file($_FILES["images"]["tmp_name"], $uploadfile)) {
		// 			echo "pb lors du telechargement";
		// 		}
		// 	} else {
		// 		var_dump("test");
		// 		echo "pas de fichier";
		// 	}
		// }
		// // Enfin, on l'ajoute à la base de données

		// $nomimg = $_FILES["images"]["name"];
		// $idimg = $this->projetManager->addImgProjet($nomimg);
		// $this->projetManager->linkProjetImages($idprojet, $idimg);

		$message = $ok ? "Projet mis à jour" : "probleme lors de la mise à jour";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	/**
	 * suppression dans la BD d'un iti à partir de l'id choisi dans le form précédent
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
		$ok = $this->projetManager->deleteProjet($idprojet);
		$message = $ok ?  "Projet supprimé" : "probleme lors de la supression";
		echo $this->twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
	}

	// 	/**
	// 	* form de saisie des criteres
	// 	* @param aucun
	// 	* @return rien
	// 	*/
	// 	public function formRechercheItineraire() {
	// 		echo $this->twig->render('itineraire_recherche.html.twig',array('acces'=> $_SESSION['acces'])); 
	// 	}

	// 	/**
	// 	* recherche dans la BD d'iti à partir des données du form précédent
	// 	* @param aucun
	// 	* @return rien
	// 	*/
	// 	public function rechercheItineraire() {
	// 		$itineraires = $this->itiManager->search($_POST["lieudepart"], $_POST["lieuarrivee"], $_POST["datedepart"]);
	// 		echo $this->twig->render('itineraire_liste.html.twig',array('itis'=>$itineraires,'acces'=> $_SESSION['acces'])); 
	// 	}
}
