<?php 
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Affichage des recettes</title>
	<link rel="stylesheet" href="style.css" />
	<meta charset="utf-8" />
</head>
<?php include ("Entete.php");?>
<body>
<?php

	
	include ("Menu.php");
	
	include("Parametres.php");
	include("Fonctions.inc.php");

	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");
	
	
	if(isset($_GET['idRec']))
	{
		$idRec = mysqli_real_escape_string($mysqli, $_GET['idRec']);
		$requeteElementsRecettes = "SELECT titre,ingredients,preparation 
									FROM recettes 
									WHERE idRec = $idRec;";
		$resultat = query($mysqli,$requeteElementsRecettes);
		$nuplet = mysqli_fetch_assoc($resultat);
		
		$titre = $nuplet['titre'];
		$ingredients = $nuplet['ingredients'];
		$preparation = $nuplet['preparation'];
?>
		<article id="Recettes2">
			
			<div id="div1">
				<center>
					<u><b><h3>Recette:</h3></u></b>
				</center>
				<?php 
					echo $titre;
					$titreImage = str_replace(' ','_',ucfirst(strtolower(trim($titre))));
				?>
				<br/>
				<img src="Photos/<?php echo $titreImage;?>.jpg" width="200px" height="200px" />
			</div>
			<div id="div2">
				<center>
					<u><b><h3>Ingredients:<h3></u></b>
				</center>
				<?php 
					$ingredientTab = explode('|',$ingredients);
					foreach($ingredientTab as $ingredient)
					{
						echo '>'.$ingredient."<br/>";
					}
				?>
			</div>
			<div id="div3">
				<center>
					<u><b><h3>Preparation:</h3></u></b>
				</center>
				<?php echo $preparation; ?>
			</div>
			<a href="Recettes.php">Revenir aux recettes</a>
		</article>
		
<?php
		
		
	}
	else
	{
		$requeteListeRecettes = 'SELECT idRec,titre FROM recettes ;';

		$resultat = query($mysqli,$requeteListeRecettes);
?>
		<article id="Recettes">
		<center>
			<h1>Liste des Recettes<h1>
			<h2><?php echo mysqli_num_rows ($resultat )?> Recette(s) a(ont) ete trouvee(s)<h2>
		</center>
	
		<table id = "table2" style="border:solid 1px black;">
				<thead> <!-- En-tête du tableau -->
					<tr>
						<th style="border:solid 1px black;">Titre</th>
						<th style="border:solid 1px black;">Composants</th>
					</tr>
				</thead>
				<tbody>
<?php
		while($nuplet=mysqli_fetch_assoc($resultat))
		{

			$idRec = mysqli_real_escape_string($mysqli, $nuplet['idRec']);
			$titre = mysqli_real_escape_string($mysqli, $nuplet['titre']);
			$listeComposants = "";
			$requeteListeComposants = "SELECT A.nom  
										FROM aliments A,ComposantsDeRecettes C 
										WHERE C.idRec = $idRec 
										AND C.idAl = A.idAl ;";
			$resultatComp = query($mysqli,$requeteListeComposants);
			
			$composant = mysqli_fetch_assoc($resultatComp);
			$listeComposants = $composant['nom'];
			//echo $listeComposants;
			while($composant = mysqli_fetch_assoc($resultatComp))
			{
				
				//print_r($composant);
				$listeComposants .= ", ".$composant['nom'];
				//echo $composant['nom'];
				//echo $listeComposants;
			}

?>
			<td style= "border:solid 1px black;">
				<h3><a href="Recettes.php?idRec=<?php echo $nuplet['idRec'];?>"><?php echo $nuplet['titre'];?></a></h3>
			</td>			
			<td style= "border:solid 1px black;">
			<h3>
<?php
			echo $listeComposants;
?>
			</h3>
			</td>
		</tr>
<?php
		}
?>
		</tbody>
		</table>
		</article>
<?php
	}
?>
</body>
	<?php include ("PiedsDePage.php");?>
</html>