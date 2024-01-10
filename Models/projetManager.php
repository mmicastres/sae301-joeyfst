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
	 * retourne l'ensemble des itinéraires présents dans la BD 
	 * @return Projets[]
	 */
	public function getListProjets()
	{
		// définitions des objets à récupérer
		$projets = array();
		$req = "SELECT idprojet, titre, description, demo, source, valideadmin FROM Projet";
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
	 * Recherche dans la BD d'un itineraire à partir de son id
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
			$ressources[] = $donnees;
		}
		if (empty($ressources)) {
			return null;
		} else {
			return $ressources;
		}
	}

	public function allRessources()
	{
		$req = "SELECT idressource, intitule FROM Ressource";
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

	public function projetImages(int $idprojet)
	{
		$req = "SELECT lienimage FROM Projet INNER JOIN Ajouterimg on Projet.idprojet = Ajouterimg.Id INNER JOIN Images on Ajouterimg.Id_1 = Images.idimage WHERE idprojet=?";
		$stmt = $this->_db->prepare($req);
		$stmt->execute(array($idprojet));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		// récup des données
		while ($donnees = $stmt->fetch()) {
			$images[] = $donnees;
		}
		if (empty($images)) {
			return null;
		} else {
			return $images;
		}
	}

	/**
	 * ajout d'un itineraire dans la BD
	 * @param itineraire à ajouter
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

	public function linkProjetCategories(int $idprojet, $idcategorie)
	{
		$req = "INSERT INTO Ajoutercat (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet, $idcategorie));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	public function linkProjetTags(int $idprojet, $idtag)
	{
		$req = "INSERT INTO Ajoutertag (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet, $idtag));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	public function linkProjetRessources(int $idprojet, $idressource)
	{
		$req = "INSERT INTO Contexte (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet, $idressource));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	public function linkProjetContributeurs(int $idprojet, $idmembre)
	{
		$req = "INSERT INTO A_contribue (Id, Id_1) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idprojet, $idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return;
	}

	public function addImgProjet(string $nomimg)
	{
		// calcul d'un nouvel id image non déja utilisé = Maximum + 1
		$stmt = $this->_db->prepare("SELECT max(idimage) AS maximum FROM Images");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC); // crée un tableau associatif 
		$idimg = $data["maximum"] + 1; // Incrémente le plus grand ID existant


		$req = "INSERT INTO Images (idimage, lienimage) VALUES (?,?)";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array($idimg, $nomimg));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $idimg;
	}

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

	public function modifProjetCategories($idprojet, $idcategorie)
	{
		$req = "UPDATE Ajoutercat SET Id_1 = :idcategorie WHERE Id = :idprojet";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array(':idprojet' => $idprojet, ':idcategorie' => $idcategorie));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	public function modifProjetTags($idprojet, $idtag)
	{
		$req = "UPDATE Ajoutertag SET Id_1 = :idtag WHERE Id = :idprojet";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array(':idprojet' => $idprojet, ':idtag' => $idtag));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	public function modifProjetRessources($idprojet, $idressource)
	{
		$req = "UPDATE Contexte SET Id_1 = :idressource WHERE Id = :idprojet";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array(':idprojet' => $idprojet, ':idressource' => $idressource));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	public function modifProjetContributeurs($idprojet, $idmembre)
	{
		$req = "UPDATE A_contribue SET Id_1 = :idmembre WHERE Id = :idprojet";
		$stmt = $this->_db->prepare($req);
		$res = $stmt->execute(array(':idprojet' => $idprojet, ':idmembre' => $idmembre));
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		return $res;
	}

	/**
	 * suppression d'un projet dans la base de données
	 * @param Projet
	 * @return boolean true si suppression, false sinon
	 */
	public function deleteProjet($idprojet): bool
	{
		$req = "DELETE FROM Projet WHERE idprojet = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	public function deleteProjetCategories($idprojet)
	{
		$req = "DELETE FROM Ajoutercat WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	public function deleteProjetTags($idprojet)
	{
		$req = "DELETE FROM Ajoutertag WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	public function deleteProjetRessources($idprojet)
	{
		$req = "DELETE FROM Contexte WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	public function deleteProjetContributeurs($idprojet)
	{
		$req = "DELETE FROM A_contribue WHERE Id = ?";
		$stmt = $this->_db->prepare($req);
		return $stmt->execute(array($idprojet));
	}

	/**
	 * nombre d'itinéraires dans la base de données
	 * @return int le nb d'itinéraires
	 */
	public function count(): int
	{
		$stmt = $this->_db->prepare('SELECT COUNT(*) FROM itineraire');
		$stmt->execute();
		return $stmt->fetchColumn();
	}

	/**
	 * méthode de recherche d'itinéraires dans la BD à partir des critères passés en paramètre
	 * @param string $lieudepart
	 * @param string $lieudepart
	 * @param string $datedepart
	 * @return Itineraire[]
	 */
	public function search(string $lieudepart, string $lieuarrivee, string $datedepart)
	{
		$req = "SELECT iditi,lieudepart,lieuarrivee,heuredepart,date_format(datedepart,'%d/%c/%Y')as datedepart,tarif,nbplaces,bagagesautorises,details FROM itineraire";
		$cond = '';

		if ($lieudepart <> "") {
			$cond = $cond . " lieudepart like '%" . $lieudepart . "%'";
		}
		if ($lieuarrivee <> "") {
			if ($cond <> "") $cond .= " AND ";
			$cond = $cond . " lieuarrivee like '%" . $lieuarrivee . "%'";
		}
		if ($datedepart <> "") {
			if ($cond <> "") $cond .= " AND ";
			$cond = $cond . " datedepart = '" . dateChgmtFormat($datedepart) . "'";
		}
		if ($cond <> "") {
			$req .= " WHERE " . $cond;
		}
		// execution de la requete				
		$stmt = $this->_db->prepare($req);
		$stmt->execute();
		// pour debuguer les requêtes SQL
		$errorInfo = $stmt->errorInfo();
		if ($errorInfo[0] != 0) {
			print_r($errorInfo);
		}
		$itineraires = array();
		while ($donnees = $stmt->fetch()) {
			$itineraires[] = new Itineraire($donnees);
		}
		return $itineraires;
	}

	/**
	 * modification d'un itineraire dans la BD
	 * @param Itineraire
	 * @return boolean 
	 */
	public function update(Itineraire $iti): bool
	{
		$req = "UPDATE itineraire SET lieudepart = :lieudepart, "
			. "lieuarrivee = :lieuarrivee, "
			. "heuredepart = :heuredepart, "
			. "datedepart  = :datedepart, "
			. "tarif = :tarif, "
			. "nbplaces = :nbplaces, "
			. "bagagesautorises= :bagages, "
			. "details = :details"
			. " WHERE iditi = :iditi";
		//var_dump($iti);

		$stmt = $this->_db->prepare($req);
		$stmt->execute(array(
			":lieudepart" => $iti->lieuDepart(),
			":lieuarrivee" => $iti->lieuArrivee(),
			":heuredepart" => $iti->heureDepart(),
			":datedepart" => dateChgmtFormat($iti->dateDepart()),
			":tarif" => $iti->tarif(),
			":nbplaces" => $iti->nbPlaces(),
			":bagages" => $iti->bagagesAutorises(),
			":details" => $iti->details(),
			":iditi" => $iti->idIti()
		));
		return $stmt->rowCount();
	}
}

// fontion de changement de format d'une date
// tranformation de la date au format j/m/a au format a/m/j
function dateChgmtFormat($date)
{
	//echo "date:".$date;
	list($j, $m, $a) = explode("/", $date);
	return "$a/$m/$j";
}
