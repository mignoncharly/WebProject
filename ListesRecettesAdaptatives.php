<?php


/* veuillez bien à vous connecter à votre base de données */
  	include("Parametres.php");
	include("Fonctions.inc.php");
	
	
	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");

$term = $_GET['term'];
//echo $_GET['term'];
preg_match('%[\+|\-]([^\+\-]+)+$%',$_GET['term'],$result);
//print_r($result);
if(!empty($result[1]))
{ 
	$term = $result[1];
}
//$term = mysqli_real_escape_string($_GET['term']);
$term = mysqli_real_escape_string($term);
$before =""; 
$before = substr($_GET['term'],0,sizeof($_GET['term'])-(strlen($term)+1));
$term = '%'.$term.'%';

$requete = "SELECT * 
			FROM aliments 
			WHERE nom LIKE '$term';"; // j'effectue ma requête SQL grâce au mot-clé LIKE

$resultat = query($mysqli,$requete);

$array = array(); // on créé le tableau


while($donnee = mysqli_fetch_assoc($resultat)) // on effectue une boucle pour obtenir les données
{
	$nom = $before.$donnee['nom'];
    array_push($array,$nom); // et on ajoute celles-ci à notre tableau

}


echo json_encode($array); // il n'y a plus qu'à convertir en JSON
?>