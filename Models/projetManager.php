<?php

/**
 * Définition d'une classe permettant de gérer les itinéraires 
 *   en relation avec la base de données	
 */
class ProjetManager
{

	private $_db; // Instance de PDO - objet de connexion au SGBD

	/**
	 * Constructeur = initialisation de la connexion vers le SGBD
	 */
	public function __construct($db)
	{
		$this->_db = $db;
	}

	/**
	 * retourne l'ensemble des projets présents dans la BD 
	 * @return Projets[]
	 */
	public function getListProjets()
	{
		// définitions des objets à récupérer
		$projets = array();
		$req = "SELECT idprojet, titre, description FROM Projet WHERE valideadmin = 1";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$projets[] = new Projet($donnees);
		}
		return $projets;
	}

	/**
	 * Recherche dans la BD d'un projet à partir de son id
	 * @param int $idprojet 
	 * @return Projet
	 */
	public function projetInfo(int $idprojet): Projet
	{
		$req = "SELECT idprojet, titre, description, demo, source, valideadmin FROM Projet WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		$projet = new Projet($stmt->fetch());
		return $projet;
	}

	/**
	 * Recherche dans la BD des catégories d'un projet à partir de son id
	 * @param int $idprojet 
	 * @return categories
	 */
	public function projetCategories(int $idprojet)
	{
		$req = "SELECT nomcategorie FROM Projet INNER JOIN Ajoutercat on Projet.idprojet = Ajoutercat.Id INNER JOIN Categories on Ajoutercat.Id_1 = Categories.idcategorie WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$categories[] = $donnees;
		}
		if (empty($categories)) {
			return null;
		} else {
			return $categories;
		}
	}

	/**
	 * Recherche dans la BD de toutes les catégories
	 * @param rien
	 * @return categories[]
	 */
	public function allCategories()
	{
		$req = "SELECT idcategorie, nomcategorie FROM Categories";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$categories[] = $donnees;
		}
		return $categories;
	}

	/**
	 * Recherche dans la BD des tags d'un projet à partir de son id
	 * @param int $idprojet 
	 * @return tags
	 */
	public function projetTags(int $idprojet)
	{
		$req = "SELECT nomtag FROM Projet INNER JOIN Ajoutertag on Projet.idprojet = Ajoutertag.Id INNER JOIN Tags on Ajoutertag.Id_1 = Tags.idtag WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$tags[] = $donnees;
		}
		if (empty($tags)) {
			return null;
		} else {
			return $tags;
		}
	}

	/**
	 * Recherche dans la BD de tous les tags 
	 * @param rien
	 * @return tags[]
	 */
	public function allTags()
	{
		$req = "SELECT idtag, nomtag FROM Tags";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$tags[] = $donnees;
		}
		return $tags;
	}

	/**
	 * Recherche dans la BD des ressources d'un projet à partir de son id
	 * @param int $idprojet 
	 * @return ressources
	 */
	public function projetRessources(int $idprojet)
	{
		$req = "SELECT semestre, intitule, identifiant FROM Projet INNER JOIN Contexte on Projet.idprojet = Contexte.Id INNER JOIN Ressource on Contexte.Id_1 = Ressource.idressource WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			mb_convert_encoding($donnees, "UTF-8");
			$ressources[] = $donnees;
		}
		if (empty($ressources)) {
			return null;
		} else {

			return $ressources;
		}
	}

	/**
	 * Recherche dans la BD de toutes les ressources
	 * @param 
	 * @return ressources[]
	 */
	public function allRessources()
	{
		$req = "SELECT idressource, intitule, identifiant FROM Ressource";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$donnees = mb_convert_encoding($donnees, "utf-8");
			$ressources[] = $donnees;
		}
		return $ressources;
	}

	/**
	 * Recherche dans la BD des contributeurs d'un projet à partir de son id
	 * @param int $idprojet 
	 * @return contributeurs
	 */
	public function projetContributeurs(int $idprojet)
	{
		$req = "SELECT idmembre, nom, prenom, photo FROM Projet INNER JOIN A_contribue on Projet.idprojet = A_contribue.Id INNER JOIN Utilisateur on A_contribue.Id_1 = Utilisateur.idmembre WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$contributeurs[] = $donnees;
		}
		if (empty($contributeurs)) {
			return null;
		} else {
			return $contributeurs;
		}
	}

	/**
	 * Recherche dans la BD de tous les utilisateurs
	 * @param 
	 * @return Membres
	 */
	public function allUsers()
	{
		$req = "SELECT idmembre, nom FROM Utilisateur";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$noms[] = $donnees;
		}
		return $noms;
	}

	/**
	 * Vérifie qu'un membre est contributeur d'un projet
	 * @param int $idmembre
	 * @return true
	 */
	public function verifContributeur($idprojet, $idmembre)
	{
		$estContributeur = false;
		$req = "SELECT Id_1 FROM A_contribue WHERE Id = :idprojet";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(":idprojet" => $idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			foreach ($donnees as $donnee) {
				if ($donnee == $idmembre) {
					$estContributeur = true;
				}
			}
		}
		return $estContributeur;
	}

	/**
	 * Recherche dans la BD la première image d'un projet à partir de son id
	 * @param int $idprojet 
	 * @return lienimage
	 */
	public function projetImagePrincipale(int $idprojet)
	{
		$req = "SELECT lienimage FROM Projet INNER JOIN Ajouterimg on Projet.idprojet = Ajouterimg.Id INNER JOIN Images on Ajouterimg.Id_1 = Images.idimage WHERE idprojet=? LIMIT 1";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		$image = "";
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}

		// récup des données
		$image = $stmt->fetchColumn();

		if ($image == "") {
			return false;
		} else {
			return $image;
		}
	}

	/**
	 * ajout d'un projet dans la BD
	 * @param Projet à ajouter
	 * @return int true si l'ajout a bien eu lieu, false sinon
	 */
	public function addProjet(Projet $projet)
	{
		// calcul d'un nouvel id projet non déja utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idprojet) AS maximum FROM Projet");
		$stmt->execute();
		$projet->setIdProjet($stmt->fetchColumn() + 1);

		// requete d'ajout dans la BD
		$req = "INSERT INTO Projet (idprojet, titre, description, demo, source, valideadmin) VALUES (?,?,?,?,?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($projet->idprojet(), $projet->titre(), $projet->description(), $projet->demo(), $projet->source(), $projet->valideadmin()));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	/**
	 * Lie une catégorie à un projet en ajoutant les deux id dans une table liant les deux entités
	 * @param int $idprojet, $idcategorie
	 * @return 
	 */
	public function linkProjetCategories(int $idprojet, $idcategorie)
	{
		$req = "INSERT INTO Ajoutercat (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idcategorie));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	/**
	 * Lie un tag à un projet en ajoutant les deux id dans une table liant les deux entités
	 * @param int $idprojet, $idtag
	 * @return 
	 */
	public function linkProjetTags(int $idprojet, $idtag)
	{
		$req = "INSERT INTO Ajoutertag (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idtag));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	/**
	 * Lie une ressource à un projet en ajoutant les deux id dans une table liant les deux entités
	 * @param int $idprojet, $idressource
	 * @return 
	 */
	public function linkProjetRessources(int $idprojet, $idressource)
	{
		$req = "INSERT INTO Contexte (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idressource));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	/**
	 * Lie un contributeur à un projet en ajoutant les deux id dans une table liant les deux entités
	 * @param int $idprojet, $idmembre
	 * @return 
	 */
	public function linkProjetContributeurs(int $idprojet, $idmembre)
	{
		$req = "INSERT INTO A_contribue (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	/**
	 * Ajoute une image dans la base de données
	 * @param $nomimg
	 * @return $idimg
	 */
	public function addImgProjet(string $nomimg)
	{
		// calcul d'un nouvel id image non déja utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idimage) AS maximum FROM Images");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC); // crée un tableau associatif 
		$idimg = $data["maximum"] + 1; // Incrémente le plus grand ID existant


		$req = "INSERT INTO Images (idimage, lienimage) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idimg, $nomimg));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $idimg;
	}

	/**
	 * Lie une image à un projet en ajoutant les deux id dans une table liant les deux entités
	 * @param int $idprojet, $idimg
	 * @return 
	 */
	public function linkProjetImages(int $idprojet, $idimg)
	{
		$req = "INSERT INTO Ajouterimg (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet, $idimg));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	/**
	 * Modifie les informations d'un projet dans la BDD
	 * @param Projet
	 * @return 
	 */
	public function modifProjet(Projet $projet)
	{
		$req = "UPDATE Projet SET titre = :titre, " . "description = :description, " . "demo = :demo, " . "source = :source " . "WHERE idprojet = :idprojet";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array(':idprojet' => $projet->idprojet(), ':titre' => $projet->titre(), ':description' => $projet->description(), 'demo' => $projet->demo(), 'source' => $projet->source()));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	/**
	 * suppression d'un projet dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjet($idprojet): bool
	{
		$req = "DELETE FROM Projet WHERE idprojet = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * suppression du lien entre un projet et ses catégories dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetCategories($idprojet)
	{
		$req = "DELETE FROM Ajoutercat WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * suppression du lien entre un projet et ses tags dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetTags($idprojet)
	{
		$req = "DELETE FROM Ajoutertag WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * suppression du lien entre un projet et ses ressources dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetRessources($idprojet)
	{
		$req = "DELETE FROM Contexte WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * suppression du lien entre un projet et ses contributeurs dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetContributeurs($idprojet)
	{
		$req = "DELETE FROM A_contribue WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * suppression du lien entre un projet et ses notes dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetNotes($idprojet)
	{
		$req = "DELETE FROM Peut_noter WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * suppression du lien entre un projet et ses commentaires dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetCommentaires($idprojet)
	{
		$req = "DELETE FROM Peut_commenter WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * récupère l'id des images d'un projet avec l'id du projet
	 * @param $idprojet
	 * @return idimg[]
	 */
	public function getIdImages($idprojet)
	{
		$req = "SELECT idimage FROM Projet INNER JOIN Ajouterimg on Projet.idprojet = Ajouterimg.Id INNER JOIN Images on Ajouterimg.Id_1 = Images.idimage WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet));
		if ($res == true) {
			return false;
		} else {
			return $res;
		}
	}

	/**
	 * suppression d'une image de la BDD
	 * @param $idimg
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteImages($idimg)
	{
		$req = "DELETE FROM Images WHERE idimage = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idimg));
	}

	/**
	 * suppression du lien entre un projet et ses images dans la base de données
	 * @param $idprojet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjetImages($idprojet)
	{
		$req = "DELETE FROM Ajouterimg WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * méthode de recherche de projets dans la BD à partir des critères passés en paramètre, fonction partiellement reprise du modèle des itinéraires
	 * @param formulaire
	 * @return Projet[]
	 */
	public function search($titre, $description, $categorie, $tag, $ressource)
	{
		$req = "SELECT idprojet, titre, description, valideadmin FROM Projet";
		$cond = " WHERE valideadmin = 1";
		if ($categorie != "" || $tag != "" || $ressource != "" || $titre != "" || $description != "") { // si au moins un champ du formulaire est rempli, on continue la requête
			$cond = $cond . " AND";
		}

		if ($categorie != "") {
			$req = $req . " INNER JOIN Ajoutercat on Projet.idprojet = Ajoutercat.Id INNER JOIN Categories on Ajoutercat.Id_1 = Categories.idcategorie";
			$cond = $cond . " idcategorie=" . $categorie; // on ajoute ce paramètre à la requête
			if ($tag != "" || $ressource != "" || $titre != "" || $description != "") { // si au moins un autre champ non traité du formulaire est rempli, on continue la requête
				$cond = $cond . " AND";
			}
		}
		if ($tag != "") {
			$req = $req . " INNER JOIN Ajoutertag on Projet.idprojet = Ajoutertag.Id INNER JOIN Tags on Ajoutertag.Id_1 = Tags.idtag";
			$cond = $cond . " idtag=" . $tag; // on ajoute ce paramètre à la requête
			if ($ressource != "" || $titre != "" || $description != "") { // si au moins un autre champ non traité du formulaire est rempli, on continue la requête
				$cond = $cond . " AND";
			}
		}
		if ($ressource != "") {
			$req = $req . " INNER JOIN Contexte on Projet.idprojet = Contexte.Id INNER JOIN Ressource on Contexte.Id_1 = Ressource.idressource";
			$cond = $cond . " idressource=" . $ressource; // on ajoute ce paramètre à la requête
			if ($titre != "" || $description != "") { // si au moins un autre champ non traité du formulaire est rempli, on continue la requête
				$cond = $cond . " AND";
			}
		}
		if ($titre != "") {
			$cond = $cond . " titre like '%" . $titre . "%'"; // on ajoute ce paramètre à la requête
			if ($description != "") { // si au moins un autre champ non traité du formulaire est rempli, on continue la requête
				$cond = $cond . " AND";
			}
		}
		if ($description != "") {
			$cond = $cond . " description like '%" . $description . "%'"; // on ajoute ce paramètre à la requête
		}
		$req = $req . $cond;

		// execution de la requete				
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		$projets = array();
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$projets[] = new Projet($donnees);
		}
		return $projets;
	}

	/**
	 * suppression la note d'un utilisateur d'un projet
	 * @param $idprojet, $idmembre
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteNote($idprojet, $idmembre)
	{
		$req = "DELETE FROM Peut_noter WHERE Id = ? AND Id_1 = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	/**
	 * ajoute la note d'un utilisateur d'un projet
	 * @param $idprojet, $idmembre, $note
	 * @return boolean true si suppression, false sinon
	 */
	public function addNote($idprojet, $idmembre, $note)
	{
		$req = "INSERT INTO Peut_noter (`Id`, `Id_1`, `note`) VALUES (?,?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idmembre, $note));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * récupère les notes d'un projet
	 * @param $idprojet
	 * @return notes[]
	 */
	public function getNotes($idprojet)
	{
		$req = "SELECT Id_1, note FROM Peut_noter WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$notes[] = $donnees;
		}
		if (!empty($notes)) {
			return $notes;
		} else {
			return false;
		}
	}

	/**
	 * récupère les commentaires d'un projet
	 * @param $idprojet
	 * @return commentaires[]
	 */
	public function getCommentaires($idprojet)
	{
		$req = "SELECT commentaire, idmembre, nom, prenom, photo FROM Peut_commenter INNER JOIN Utilisateur ON Peut_commenter.Id_1 = Utilisateur.idmembre WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$commentaires[] = $donnees;
		}
		if (!empty($commentaires)) {
			return $commentaires;
		} else {
			return false;
		}
	}

	/**
	 * ajoute le commentaire d'un utilisateur d'un projet
	 * @param $idprojet, $idmembre, $commentaire
	 * @return boolean true si suppression, false sinon
	 */
	public function addCommentaire($idprojet, $idmembre, $commentaire)
	{
		$req = "INSERT INTO Peut_commenter (`Id`, `Id_1`, `commentaire`) VALUES (?,?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet, $idmembre, $commentaire));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}
}
