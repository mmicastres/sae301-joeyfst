<?php
// utilisation des sessions
session_start();

include "moteurtemplate.php";
include "connect.php";

include "Controllers/ProjetController.php";
include "Controllers/membresController.php";
include "Controllers/adminController.php";
$projetController = new ProjetController($bdd, $twig);
$memController = new MembreController($bdd, $twig);
$adminController = new adminController($bdd, $twig);


// texte du message
$message = "";

// ============================== connexion / deconnexion - sessions ==================

// si la variable de session n'existe pas, on la crée
if (!isset($_SESSION['acces'])) {
  $_SESSION['acces'] = "non";
  $_SESSION['admin'] = 0;
  $_SESSION['idmembre'] = "";
}

// accéder au formulaire de connexion
if (isset($_GET["action"])  && $_GET["action"] == "login") {
  $memController->membreFormulaireConnexion();
}

// click sur le bouton connexion de la page de connexion
if (isset($_POST["connexion"])) {
  $message = $memController->membreConnexion($_POST);
}

// accéder au formulaire d'inscription
if (isset($_GET["action"])  && $_GET["action"] == "subscribe") {
  $memController->membreFormulaireInscription();
}

// click sur le bouton inscription de la page d'inscription
if (isset($_POST["inscription"])) {
  $message = $memController->membreInscription($_POST);
}

// deconnexion : click sur le bouton deconnexion
if (isset($_GET["action"]) && $_GET['action'] == "logout") {
  $message = $memController->membreDeconnexion();
}

// ============================== page d'accueil ==================

// cas par défaut = page d'accueil
if (!isset($_GET["action"]) && empty($_POST)) {
  $message="empty";
  echo $twig->render('index.html.twig', array('message' => $message, 'acces' => $_SESSION['acces'], 'admin' => $_SESSION['admin']));
}

// ============================== gestion utilisateur ==================

// espace personnel avec mes infos et mes projets
if (isset($_GET["action"]) && $_GET["action"] == "monespace") {
  $memController->espaceUtilisateur($_SESSION['idmembre']);
}

// accès au formulaire de modification de ses informations, accessible en tant qu'utilisateur ou admin
if (isset($_GET['action']) && $_GET['action'] == "modifiermembre") {
  $memController->formModifierMembre($_SESSION['admin']);
}

// modification des informations personnelles du membre
if (isset($_POST['validmodifmembre'])) {
  $memController->modifierMembre();
}

// changement de photo de profil
if (isset($_POST["changementpdp"])) {
  $memController->changerPdp($_POST);
}

// ============================== gestion des projets ==================

// liste des projets, création de card bootstrap
//  https://.../index/php?action=liste
if (isset($_GET["action"]) && $_GET["action"] == "liste") {
  $projetController->listeProjets();
}

// toutes les infos d'un projet
//  https://.../index/php?action=projetinfo
if (isset($_GET["action"]) && $_GET["action"] == "infoprojet") {
  $projetController->infoProjet($_GET);
}

// formulaire ajout d'un projet : saisie des caractéristiques à ajouter dans la BD
//  https://.../index/php?action=ajout
// le projet est rattaché automatiquement à un membre déjà présent dans la BD
if (isset($_GET["action"]) && $_GET["action"] == "ajoutprojet") {
  $projetController->formAjoutProjet($_SESSION['idmembre']);
}

// ajout du projet dans la base
// --> au clic sur le bouton "valider_ajout" du form précédent
if (isset($_POST["valider_ajout"])) {
  $projetController->ajoutProjet();
}

// modification d'un projet : saisie des nouvelles valeurs
if (isset($_GET["action"]) && $_GET["action"] == "modifprojet") {
  $projetController->formModifProjet();
}

//modification d'un projet : enregistrement dans la bd
// --> au clic sur le bouton "Valider" du form précédent
if (isset($_POST["valider_modif"])) {
  $projetController->modifProjet();
}

// supression d'un projet dans la base
// --> au clic sur le bouton "supprimerprojet" du form précédent
if (isset($_GET["action"]) && $_GET["action"] == "supprimerprojet") {
  $projetController->supprProjet();
}

// // recherche des projets : construction de la requete SQL en fonction des critères 
// // de recherche et affichage 
// // --> au clic sur le bouton "Valider" du form précédent
if (isset($_POST["recherche"])) {
  $projetController->rechercheProjet();
}

// Commenter et noter un projet (les deux passent par la même fonction)
if (isset($_POST['envoyercommentaire'])) {
  $projetController->commenterProjet();
}

// ============================== section admin ==================

// accès au pannel admin
if (isset($_GET['action']) && $_GET['action'] == "paneladmin") {
  $adminController->paneladmin($_SESSION['admin'], $message);
}

// Validation d'un projet depuis le pannel admin
if (isset($_GET['action']) && $_GET['action'] == "validerprojet") {
  $adminController->validerProjet($_GET);
}

// ajouter une catégorie, un tag ou une ressource à la BD
if (isset($_POST["ajoutercategorie"])) {
  $adminController->adminCategorie();
}

// supprimer une catégorie, un tag ou une ressource de la BD
// cela supprime les liens entre catégories/tags/ressources et les projets correspondants
if (isset($_POST['supprimercategorie'])) {
  $adminController->supprCategorie();
}

// promouvoir un membre admin et lui donner accès aux commandes administrateurs
if (isset($_GET['action']) && $_GET['action'] == "promouvoiradmin") {
  $adminController->promouvoirAdmin($_GET);
}

// supprimer un membre de la BD, supprime également ses contributions
if (isset($_GET['action']) && $_GET['action'] == "supprimermembre") {
  $adminController->supprimerMembre($_GET);
}