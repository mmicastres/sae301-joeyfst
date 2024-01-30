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
		//echo $login." : ".$password;
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
