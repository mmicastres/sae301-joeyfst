<?php

/**
 * Définition d'une classe permettant de gérer les commandes administrateurs
 * en relation avec la base de données
 *
 */

class adminManager
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
	 * récupère tous les projets n'étant pas encore validés
	 * @param 
	 * @return Projets
	 */
	public function projetsAValider()
	{
		$projets = array();
		$req = "SELECT idprojet, titre, description, demo, source FROM Projet WHERE valideadmin = 0";
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
	 * récupère tous les membres existant sur l'application
	 * @param 
	 * @return membres
	 */
	public function allUsersInfos()
	{
		$membres = array();
		$req = "SELECT idmembre, nom, prenom, photo, admin FROM Utilisateur";
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$noms[] = new Membre($donnees);
		}
		return $noms;
	}

	/**
	 * valide un projet n'étant pas encore validé
	 * @param idprojet
	 * @return true
	 */
	public function changeValideadmin($idprojet)
	{
		$req = "UPDATE Projet SET valideadmin = 1 WHERE idprojet = ?";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	/**
	 * récupère tous les projets déjà validés
	 * @param 
	 * @return Projets
	 */
	public function projetsValides()
	{
		$projets = array();
		$req = "SELECT idprojet, titre, description, demo, source FROM Projet WHERE valideadmin = 1";
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
	 * récupère l'image d'un projet
	 * @param idprojet
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
	 * récupère toutes les catégories de l'application
	 * @param 
	 * @return Categories
	 */
	public function getCategories()
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
	 * ajoute une catégorie sur la base de données
	 * @param nom catégorie
	 * @return true
	 */
	public function ajoutCategorie($categorie)
	{
		// calcul d'un nouvel id de ressource non utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idcategorie) AS maximum FROM Categories");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC); // crée un tableau associatif 
		$idcategorie = $data["maximum"] + 1; // Incrémente le plus grand ID existant

		$req = "INSERT INTO Categories (idcategorie, nomcategorie) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idcategorie, $categorie));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * supprime une catégorie sur la base de données
	 * @param idtag
	 * @return true
	 */
	public function deleteCategorie($categorie)
	{
		$req = "DELETE FROM Categories WHERE idcategorie = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($categorie));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * récupère tous les tags de l'application
	 * @param 
	 * @return Tags
	 */
	public function getTags()
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
	 * ajoute un tag sur la base de données
	 * @param nom du tag
	 * @return true
	 */
	public function ajoutTag($tag)
	{
		// calcul d'un nouvel id de ressource non utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idtag) AS maximum FROM Tags");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC); // crée un tableau associatif 
		$idtag = $data["maximum"] + 1; // Incrémente le plus grand ID existant

		$req = "INSERT INTO Tags (idtag, nomtag) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idtag, $tag));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * supprime un tag sur la base de données
	 * @param idtag
	 * @return true
	 */
	public function deleteTag($tag)
	{
		$req = "DELETE FROM Tags WHERE idtag = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($tag));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * récupère toutes les ressources de l'application
	 * @param 
	 * @return Ressources
	 */
	public function getRessources()
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
			$ressources[] = $donnees;
		}
		return $ressources;
	}

	/**
	 * ajoute une ressource sur la base de données
	 * @param semestre, intitulé, nom de la ressource
	 * @return true
	 */
	public function ajoutRessource($semestre, $intitule, $nomcomplet)
	{
		// calcul d'un nouvel id de ressource non utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idressource) AS maximum FROM Ressource");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC); // crée un tableau associatif 
		$idressource = $data["maximum"] + 1; // Incrémente le plus grand ID existant

		$req = "INSERT INTO Ressource (idressource, semestre, intitule, identifiant) VALUES (?,?,?,?)";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idressource, $semestre, $intitule, $nomcomplet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * supprime une ressource sur la base de données
	 * @param idressource
	 * @return true
	 */
	public function deleteRessource($ressource)
	{
		$req = "DELETE FROM Ressource WHERE idressource = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($ressource));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * récupère toutes les informations d'un membre en particulier
	 * @param idmembre
	 * @return Membre
	 */
	public function getInfosMembre($idmembre)
	{
		$req = "SELECT idmembre, nom, prenom, email, idiut, anneenaissance, photo FROM Utilisateur WHERE idmembre=:idmembre";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(":idmembre" => $idmembre));
		// debug requête SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// recup des données
		$infos = $stmt->fetch();
		return $infos;
	}

	/**
	 * rend un membre admin en changeant la valeur d'admin en 1
	 * @param idmembre
	 * @return 
	 */
	public function setAdmin($idmembre)
	{
		$req = "UPDATE Utilisateur SET admin = 1 WHERE idmembre = ?";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idmembre));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// recup des données
		$infos = $stmt->fetch();
		return $res;
	}

	/**
	 * supprime les contributions d'un membre sur tous les projets
	 * @param idmembre
	 * @return true
	 */
	public function deleteUserContributions($idmembre)
	{
		$req = "DELETE FROM A_contribue WHERE Id_1 = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * supprime les notes d'un membre sur tous les projets
	 * @param idmembre
	 * @return 
	 */
	public function deleteUserNotes($idmembre)
	{
		$req = "DELETE FROM Peut_noter WHERE Id_1 = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * supprime les commentaires d'un membre sur tous les projets
	 * @param idmembre
	 * @return 
	 */
	public function deleteUserCommentaires($idmembre)
	{
		$req = "DELETE FROM Peut_commenter WHERE Id_1 = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}

	/**
	 * supprime un membre de la base de données
	 * @param idmembre
	 * @return 
	 */
	public function deleteUser($idmembre)
	{
		$req = "DELETE FROM Utilisateur WHERE idmembre = ?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return true;
	}
}
