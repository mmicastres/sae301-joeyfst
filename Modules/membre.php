<?php
/** 
* définition de la classe itineraire
*/
class Membre {
        private int $_idmembre;
        private string $_nom;
        private string $_prenom;
		private string $_email;
		private string $_idiut;
		private string $_mdp;
		private int $_anneenaissance;
		private string $_photo;
		private int $_admin;
		
        // contructeur
        public function __construct(array $donnees) {
		// initialisation d'un produit à partir d'un tableau de données
			if (isset($donnees['idmembre'])) { $this->_idmembre = $donnees['idmembre']; }
			if (isset($donnees['nom'])) { $this->_nom = $donnees['nom']; }
			if (isset($donnees['prenom'])) { $this->_prenom = $donnees['prenom']; }
			if (isset($donnees['email'])) { $this->_email = $donnees['email']; }
			if (isset($donnees['idiut'])) { $this->_idiut = $donnees['idiut']; }
			if (isset($donnees['mdp'])) { $this->_mdp = $donnees['mdp']; }
			if (isset($donnees['anneenaissance'])) { $this->_anneenaissance = $donnees['anneenaissance']; }
			if (isset($donnees['photo'])) { $this->_photo = $donnees['photo']; }
			if (isset($donnees['admin'])) { $this->_admin = $donnees['admin']; }
        }           
        // GETTERS //
		public function idMembre() { return $this->_idmembre;}
		public function nom() { return $this->_nom;}
		public function prenom() { return $this->_prenom;}
		public function email() { return $this->_email;}
		public function idIut() { return $this->_idiut;}
		public function mdp() { return $this->_mdp;}
		public function anneeNaissance() { return $this->_anneenaissance;}
		public function photo() { return $this->_photo;}
		public function admin() { return $this->_admin;}
		public function getAge() { return (date('Y')- $this->_anneenaissance) ; }
		
		// SETTERS //
		public function setIdMembre(int $idmembre) { $this->_idmembre = $idmembre; }
        public function setNom(string $nom) { $this->_nom= $nom; }
		public function setPrenom(string $prenom) { $this->_prenom = $prenom; }
		public function setEmail(string $email) { $this->_email = $email; }
		public function setIdIut(string $idiut) { $this->_idiut = $idiut; }
		public function setMdp(string $mdp) { $this->_mdp = $mdp; }
		public function setAnneeNaissance(int $anneenaissance) { $this->_anneenaissance = $anneenaissance; }
		public function setPhoto(string $photo) { $this->_photo = $photo; }			
		public function setAdmin(int $admin) { $this->_admin = $admin; }

    }

?>