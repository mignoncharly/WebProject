<?php
	session_start();
	$_SESSION['init'] = true;
?>
<DOCTYPE html>
<html>
<head>
	<title>Initialisation de la base de données</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="style.css" />
</head>
<?php include ("Entete.php");?>
<body>
<?php
  include ("Menu.php");
  include("Parametres.php");
  include("Fonctions.inc.php");
  include("Donnees.inc.php");

	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());

	// Suppression / Création / Sélection de la base de données : $base
	query($mysqli,'DROP DATABASE IF EXISTS '.$base);
	query($mysqli,'CREATE DATABASE '.$base);
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");
 
	//creation des tables
	$createAliments=query($mysqli,
	'CREATE TABLE IF NOT EXISTS `aliments` (
	`idAl` int(11) NOT NULL AUTO_INCREMENT,
	`nom` varchar(100) NOT NULL,
	PRIMARY KEY (`idAl`)
	)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;')
	or die("Problème de création de la base :".mysqli_error());
 
	$createRecettes=query($mysqli,
	'CREATE TABLE IF NOT EXISTS `recettes` (
	`idRec` int(11) NOT NULL AUTO_INCREMENT,
	`titre` varchar(100) NOT NULL,
	`ingredients` varchar(100) NOT NULL,
	`preparation` varchar(100) NOT NULL,
	PRIMARY KEY (`idRec`)
	)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;')
	or die("Problème de création de la base :".mysqli_error());
	
 	$createUsers=query($mysqli,
	'CREATE TABLE IF NOT EXISTS `utilisateurs` (
	`idUs` int(11) NOT NULL AUTO_INCREMENT,
	`pseudo` varchar(25) NOT NULL UNIQUE,
	`mdp` varchar(25) NOT NULL,
	`nom` varchar(25) NOT NULL,
	`prenom` varchar(25) NOT NULL,
	PRIMARY KEY (`idUs`)
	)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;')
	or die("Problème de création de la base :".mysqli_error());

	$createAlimentsDeRecettes=query($mysqli,
	'CREATE TABLE IF NOT EXISTS `ComposantsDeRecettes` (
	`idRec` int(11),
	`idAl` int(11),
	PRIMARY KEY (`idRec`,`idAl`),
	FOREIGN KEY (`idAl`) REFERENCES aliments (`idAl`),
	FOREIGN KEY (`idRec`) REFERENCES aliments (`idRec`)
	)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;')
	or die("Problème de création de la base :".mysqli_error());
	
	$createRelationsComposants=query($mysqli,
	'CREATE TABLE IF NOT EXISTS `RelationComposants` (
	`idSup` int(11),
	`idSous` int(11),
	PRIMARY KEY (`idSup`,`idSous`),
	FOREIGN KEY (`idSup`) REFERENCES aliments (`idAl`),
	FOREIGN KEY (`idSous`) REFERENCES aliments (`idAl`)
	)ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;')
	or die("Problème de création de la base :".mysqli_error());
 
	// insertion dans la table Aliments
	$composants = array();
	foreach($Hierarchie as $nomComposant => $composant) 
	{ 
		array_push($composants,' ("'.mysqli_real_escape_string($nomComposant).'") ');
	}
	$chaineComposant = implode(',',$composants);
	
  	$insertComposants=query($mysqli,
		"INSERT INTO `aliments` (`nom`) VALUES
		$chaineComposant;")
		or die("Problème de création de la base :".mysqli_error());
	//echo $chaineComposant;
	

	// insertion dans la table Recettes
	$ListeRecettes = array();
	foreach($Recettes as  $recette) 
	{ 
		array_push($ListeRecettes,' ("'.mysqli_real_escape_string($recette['titre']).'","'.mysqli_real_escape_string($recette['ingredients']).'","'.mysqli_real_escape_string($recette['preparation']).'") ');
	}
	$chaineListeRecettes = implode(',',$ListeRecettes);
	
  	$insertComposants=query($mysqli,
		"INSERT INTO `recettes` (`titre`,`ingredients`,`preparation`) VALUES
		$chaineListeRecettes;")
		or die("Problème de création de la base :".mysqli_error());
	//echo $chaineListeRecettes;
	
	//insertion dans la table relationsdecomposants
	$ListeRelations = array();
	foreach($Hierarchie as  $nomComposant => $composant) 
	{ 
		$nomComposant = "'".mysqli_real_escape_string($nomComposant)."'";
		$requeteidSousC = query($mysqli,
		"SELECT `idAl` 
		FROM `aliments` 
		WHERE `nom` = $nomComposant;")
		or die("Problème de création de la base :".mysqli_error());
		
		$resultat = mysqli_fetch_assoc($requeteidSousC);
		//print_r($resultat);
		$idSousc = $resultat['idAl'];
		//echo $idSousc.'
		//';
		if(isset($composant['super-categorie']))
		{
			foreach($composant['super-categorie'] as $superCategorie)
			{
				$nomComposant = "'".mysqli_real_escape_string($superCategorie)."'";
				$requeteidSousC = query($mysqli,
				"SELECT `idAl` 
				FROM `aliments` 
				WHERE `nom` = $nomComposant;")
				or die("Problème de création de la base :".mysqli_error());
				$resultat = mysqli_fetch_assoc($requeteidSousC);
				$idSuperc = $resultat['idAl'];
				
				array_push($ListeRelations,' ('.mysqli_real_escape_string($idSuperc).','.mysqli_real_escape_string($idSousc).') ');
				
			}
		}
		
	}
	$chaineListeRelations = implode(',',$ListeRelations);
	$insertComposants=query($mysqli,
	"INSERT INTO `RelationComposants` (`idSup`,`idSous`) VALUES
	$chaineListeRelations;")
	or die("Problème de création de la base :".mysqli_error());
	
	
	//insert dans la table AlimentsdeRecettes
	$ListeRelations = array();
	foreach($Recettes as  $recette) 
	{ 
		$titreRecette = "'".mysqli_real_escape_string($recette['titre'])."'";
		$requeteidRecette = query($mysqli,
		"SELECT `idRec` 
		FROM `recettes` 
		WHERE `titre` = $titreRecette;")
		or die("Problème de création de la base :".mysqli_error());
		
		$resultat = mysqli_fetch_assoc($requeteidRecette);
		//print_r($resultat);
		$idRec = $resultat['idRec'];
		//echo $idSousc.'
		//';
		foreach($recette['index'] as $composant)
		{
			$nomComposant = "'".mysqli_real_escape_string($composant)."'";
			$requeteidAl = query($mysqli,
			"SELECT `idAl` 
			FROM `aliments` 
			WHERE `nom` = $nomComposant;")
			or die("Problème de création de la base :".mysqli_error());
			$resultat = mysqli_fetch_assoc($requeteidAl);
			$idAl = $resultat['idAl'];
			
			array_push($ListeRelations,' ('.mysqli_real_escape_string($idRec).','.mysqli_real_escape_string($idAl).') ');
			
		}
	}
	
	
	$chaineListeRelations = implode(',',$ListeRelations);
	$insertComposants=query($mysqli,
	"INSERT INTO `ComposantsDeRecettes` (`idRec`,`idAl`) VALUES
	$chaineListeRelations;")
	or die("Problème de création de la base :".mysqli_error());	
	

	
?>

<article id="Recettes">
	<center>
		<h2>initialisation de la Base de donnees reussie!!!</h2>
	</center>
</article>

</body>
	<?php include ("PiedsDePage.php");?>
</html>