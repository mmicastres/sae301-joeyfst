<?php
/**
* définition de la classe itineraire
*/
class Projet {
	private int $_idprojet;   
	private string $_titre;
	private string $_description;
	private $_demo;
	private $_source;
	private $_valideadmin;
	private $_imageprincipale;
		
	// contructeur
	public function __construct(array $donnees) {
	// initialisation d'un produit à partir d'un tableau de données
		if (isset($donnees['idprojet']))       { $this->_idprojet =       $donnees['idprojet']; }
		if (isset($donnees['titre']))    { $this->_titre =    $donnees['titre']; }
		if (isset($donnees['description']))  { $this->_description =  $donnees['description']; }
		if (isset($donnees['demo'])) { $this->_demo= $donnees['demo']; }
		if (isset($donnees['source'])) { $this->_source = $donnees['source']; }
		if (isset($donnees['valideadmin']))  { $this->_valideadmin =  $donnees['valideadmin'];}		
		if (isset($donnees['imageprincipale']))  { $this->_imageprincipale =  $donnees['imageprincipale'];}		
	}           
	// GETTERS //
	public function idprojet()       { return $this->_idprojet;}
	public function titre()    { return $this->_titre;}
	public function description()  { return $this->_description;}
	public function demo() { return $this->_demo;}
	public function source() { return $this->_source;}
	public function valideadmin()  { return $this->_valideadmin;}
	public function imageprincipale()  { return $this->_imageprincipale;}
		
	// SETTERS //
	public function setIdProjet(int $idprojet)             { $this->_idprojet = $idprojet; }
	public function setTitre(int $titre)       { $this->_titre = $titre; }
	public function setDescription(string $description)   { $this->_description= $description; }
	public function setDemo(string $demo) { $this->_demo = $demo; }
	public function setSource($source) { $this->_source = $source; }
	public function setValideadmin($valideadmin)   { $this->_valideadmin = $valideadmin; }
	public function setImagePrincipale($imageprincipale)   { $this->_imageprincipale = $imageprincipale; }

}

