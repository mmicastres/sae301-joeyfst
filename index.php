<?php
// utilisation des sessions
session_start();

include "moteurtemplate.php";
include "connect.php";

include "Controllers/ProjetController.php";
include "Controllers/membresController.php";
include "Controllers/adminController.php";
$projetController = new ProjetController($bdd,$twig);
$memController = new MembreController($bdd,$twig);
$adminController = new adminController($bdd, $twig);


// texte du message
$message = "";

// ============================== connexion / deconnexion - sessions ==================

// si la variable de session n'existe pas, on la crée
if (!isset($_SESSION['acces'])) {
   $_SESSION['acces']="non";
   $_SESSION['admin']=0;
}

// accéder au formulaire de connexion
if (isset($_GET["action"])  && $_GET["action"]=="login") {
  $memController->membreFormulaireConnexion(); 
}

// click sur le bouton connexion de la page de connexion
if (isset($_POST["connexion"]))  {  
  $message = $memController->membreConnexion($_POST);  
}

// accéder au formulaire d'inscription
if (isset($_GET["action"])  && $_GET["action"]=="subscribe") {
  $memController->membreFormulaireInscription(); 
}

// click sur le bouton inscription de la page d'inscription
if (isset($_POST["inscription"]))  {  
  $message = $memController->membreInscription($_POST);  
}

// deconnexion : click sur le bouton deconnexion
if (isset($_GET["action"]) && $_GET['action']=="logout") { 
    $message = $memController->membreDeconnexion(); 
 } 

// ============================== page d'accueil ==================

// cas par défaut = page d'accueil
if (!isset($_GET["action"]) && empty($_POST)) {
  echo $twig->render('index.html.twig',array('acces'=> $_SESSION['acces'], 'admin'=> $_SESSION['admin'])); 
}

// espace personnel avec mes infos et mes projets
if (isset($_GET["action"]) && $_GET["action"]=="monespace") { 
  $memController->espaceUtilisateur($_SESSION['idmembre']);
}

 // changement de photo de profil
 if (isset($_POST["changementpdp"]))  {
  $memController->changerPdp($_POST);  
}

// ============================== gestion des projets ==================

// liste des projets dans un tableau HTML
//  https://.../index/php?action=liste
if (isset($_GET["action"]) && $_GET["action"]=="liste") {
  $projetController->listeProjets();
}

// toutes les infos d'un projet
//  https://.../index/php?action=projetinfo
if (isset($_GET["action"]) && $_GET["action"]=="infoprojet"){
  $projetController->infoProjet($_GET);
}

// formulaire ajout d'un projet : saisie des caractéristiques à ajouter dans la BD
//  https://.../index/php?action=ajout
// version 0 : le projet est rattaché automatiquement à un membre déjà présent dans la BD
//              l'idmembre est en champ caché dans le formulaire
if (isset($_GET["action"]) && $_GET["action"]=="ajoutprojet") {
  $projetController->formAjoutProjet($_SESSION['idmembre']);
 }

// ajout de l'itineraire dans la base
// --> au clic sur le bouton "valider_ajout" du form précédent
if (isset($_POST["valider_ajout"])) {
  $projetController->ajoutProjet();
}

// modification d'un projet : saisie des nouvelles valeurs
if (isset($_GET["action"]) && $_GET["action"]=="modifprojet") {   
  $projetController->formModifProjet();
}

//modification d'un projet : enregistrement dans la bd
// --> au clic sur le bouton "valider_modif" du form précédent
if (isset($_POST["valider_modif"])) {
  $projetController->modifProjet();
}

// supression d'un itineraire dans la base
// --> au clic sur le bouton "supprimerprojet" du form précédent
if (isset($_GET["action"]) && $_GET["action"]=="supprimerprojet") { 
  $projetController->supprProjet();
}

// // recherche des itineraires : construction de la requete SQL en fonction des critères 
// // de recherche et affichage du résultat dans un tableau HTML 
// // --> au clic sur le bouton "valider_recher" du form précédent
// if (isset($_POST["valider_recher"])) { 
//   $projetController->rechercheItineraire();
// }

// ============================== section admin ==================

// accès au pannel admin
if (isset($_GET['action']) && $_GET['action']=="paneladmin") {
  $adminController->paneladmin($_SESSION['admin'], $message);
}

if (isset($_GET['action']) && $_GET['action']=="validerprojet"){
  $adminController->validerProjet($_GET);
}

if (isset($_POST["admin_categorie"])){
  $adminController->adminCategorie();
}

if (isset($_GET['action']) && $_GET['action']=="modifiermembre") {
  $adminController->formModifierMembre($_SESSION['admin']);
}

if (isset($_POST['adminmembre'])){
  $adminController->modifierMembre();
}
?>
