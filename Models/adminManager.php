<?php

/**
 * Définition d'une classe permettant de gérer les membres 
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
}
