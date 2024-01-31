<?php

/**
 * Définition d'une classe permettant de gérer les membres 
 * en relation avec la base de données
 *
 */

class MembreManager
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
	 * verification de l'identité d'un membre (Login/password)
	 * @param string $login
	 * @param string $password
	 * @return membre si authentification ok, false sinon
	 */
	public function verif_identification($email, $password)
	{
		$req = "SELECT idmembre, nom, prenom, admin FROM Utilisateur WHERE email=:email and mdp=:mdp ";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(":email" => $email, ":mdp" => $password));
		if ($data = $stmt->fetch()) {
			$membre = new Membre($data);
			return $membre;
		} else return false;
	}

	/**
	 * ajout d'un nouvel utilisateur dans la BD
	 * @param membre à ajouter
	 * @return int true si l'ajout a bien eu lieu, false sinon
	 */
	public function new_inscription($membre)
	{
		// calcul d'un nouvel id de membre non utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idmembre) AS maximum FROM Utilisateur");
		$stmt->execute();
		$membre->setIdMembre($stmt->fetchColumn() + 1);

		$req = "INSERT INTO Utilisateur (idmembre, nom, prenom, email, idiut, mdp, anneenaissance, photo, admin) VALUES (?,?,?,?,?,?,?,?,?)";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($membre->idMembre(), $membre->nom(), $membre->prenom(), $membre->email(), $membre->idIut(), $membre->mdp(), $membre->anneeNaissance(), $membre->photo(), $membre->admin()));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	/**
	 * récupération des informations du membre pour les ajouter sur le formulaire
	 * @param idmembre
	 * @return infos du membre
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
	 * changement du nom de la photo de profil sur la base de données
	 * @param idmembre, photo
	 * @return 
	 */
	public function changementPdp($photo, $idmembre)
	{
		$req = "UPDATE Utilisateur SET photo = :photo WHERE idmembre = :idmembre";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(':photo' => $photo, ':idmembre' => $idmembre));
		// debug requête SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
			$ok = false;
		} else {
			$ok = true;
		}
		return $ok;
	}

	/**
	 * récupération des projets où le membre est contributeur
	 * @param idmembre
	 * @return projets
	 */
	public function getProjetsMembre($idmembre)
	{
		$req = "SELECT idprojet, titre, description, demo, source, valideadmin FROM Projet INNER JOIN A_contribue on Projet.idprojet = A_contribue.Id INNER JOIN Utilisateur on A_contribue.Id_1 = Utilisateur.idmembre WHERE idmembre=:idmembre";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(":idmembre" => $idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$projets[] = new Projet($donnees);
		}
		if (empty($projets)) {
			return null;
		} else {
			return $projets;
		}
	}

	/**
	 * récupère l'image principale des projets ayant des images
	 * @param idprojet
	 * @return lienimage
	 */
	public function projetImagePrincipale (int $idprojet)
	{
		$req = "SELECT lienimage FROM Projet INNER JOIN Ajouterimg on Projet.idprojet = Ajouterimg.Id INNER JOIN Images on Ajouterimg.Id_1 = Images.idimage WHERE idprojet=? LIMIT 1";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		$image ="";
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
	 * modification des nouvelles informations du membre sur la BDD
	 * @param Membre
	 * @return 
	 */
	public function validerModifMembre($membre)
	{
		$req = "UPDATE Utilisateur SET nom = ?, prenom = ?, email = ?, idiut = ?, anneenaissance = ? WHERE idmembre = ?";
		$stmt = $this->_db->prepare($req);
		$res  = $stmt->execute(array($membre->nom(), $membre->prenom(), $membre->email(), $membre->idIut(), $membre->anneeNaissance(), $membre->idMembre()));
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

}
