<?php 
	session_start();	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Affichage des recettes</title>
	<link rel="stylesheet" href="style.css" />
	<meta charset="utf-8" />
	
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>        
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" /> 
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<script type="text/javascript">
	function autocompletion()
	{
			$('#rechercheList').autocomplete({
			source : 'ListesRecettesAdaptatives.php'
			});
	}
	</script>
</head>
<?php include ("Entete.php");?>
<body>

	<?php include ("Menu.php");
	include("Parametres.php");
	include("Fonctions.inc.php");

	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");
	
	
	function souscathegorie($cathegorie,$mysqli)
	{
		$cathegorie = mysqli_real_escape_string($mysqli, trim($cathegorie));
		$souscFinal= array();
		$sousc= array();
		$requete = "SELECT A.idAl 
					FROM aliments A 
					WHERE A.nom = '$cathegorie';";
		$resultat = query($mysqli,$requete);
		$nuplet = mysqli_fetch_assoc($resultat);
		
		//echo $idAl.':---->';
		if(!empty($nuplet ))
		{
			$idAl = mysqli_real_escape_string($mysqli, $nuplet['idAl']);
			$requete2 = "SELECT DISTINCT A.nom 
							FROM aliments A, relationcomposants R 
							WHERE R.idSous = A.idAl 
							AND R.idSup = $idAl ;";
			$resultat2 = query($mysqli,$requete2);
			while($nuplet2 = mysqli_fetch_assoc($resultat2))
			{
				//echo $nuplet2['nom'];
				array_push($sousc,$nuplet2['nom']);
				array_push($souscFinal,$nuplet2['nom']);
				//echo "VOICI LE NIVEAU INTER...<br/>";
				//print_r($souscFinal);
				//echo "<br/>";
			}
			if(!empty($sousc))
			{
				foreach($sousc as $elt)
				{
					$tab = souscathegorie($elt,$mysqli);
					if(!empty($tab));
					{
						foreach($tab as $elt)
						{
							array_push($souscFinal,$elt);
						}
					}
				}
			}
		}
		return $souscFinal;
		
	}
	
			function eltEtSousCat($tabDeExpReg,$mysqli)
			{
				$tabEltEtSousElt = array();
				foreach($tabDeExpReg as $elt)
				{
					if(!in_array($elt,$tabEltEtSousElt ))
					{
						array_push($tabEltEtSousElt ,$elt);
						$sousC = souscathegorie($elt,$mysqli);
						if(!empty($sousC))
						{
							foreach($sousC as $souscat)
							{
								if(!in_array($souscat,$tabEltEtSousElt ))
								{
									array_push($tabEltEtSousElt ,$souscat);
								}
							}
						}	
					}
				}
				return $tabEltEtSousElt;
			}
?>
	<article id="Recherche">
		<form action="#" method="post">
			<h2><b style="color:red;">Notice: </b></h2>
			<h3>Format de recherche de recette " +element souhaite dans la recette  -element non souhaite "</h3>
			<input type="text" size="100" id="rechercheList" name="recherche" onkeyup="autocompletion();" value ="<?php if(isset($_POST['recherche']))  echo $_POST['recherche']; ?>">
			<input type="submit" name="submit" value="Rechercher">
		</form>

		<div id="resultat">
	
<?php
		if(isset($_POST['submit']) && !empty($_POST['recherche']))
		{
			$composantVoulu = array();
			$tabCompVoulu = array();
			$composantNonVoulu = array();
			$tabCompNonVoulu = array();
			$recherche = $_POST['recherche'];
			preg_match_all('%\+([^\+\-]+)%',$recherche,$composantVoulu);
			preg_match_all('%\-([^\+\-]+)%',$recherche,$composantNonVoulu);
			$requeteDyn = 'SELECT DISTINCT R.idRec,R.titre 
							FROM aliments A,recettes R,composantsderecettes C 
							WHERE A.idAl = C.idAl 
							AND R.idRec = C.idRec';
			$requeteTest = $requeteDyn;
			$tabCompVoulu = eltEtSousCat($composantVoulu[1],$mysqli);
			$tabCompNonVoulu = eltEtSousCat($composantNonVoulu[1],$mysqli); 
			
			if (!empty($tabCompVoulu))
			{
				for ($i=0;$i<sizeof($tabCompVoulu);$i++)
				{
					if($i==0)
					{	
						$compVoulu =  " AND (A.nom = '".mysqli_real_escape_string($mysqli, trim($tabCompVoulu[0]))."'";
					}
					else
					{
						$compVoulu =  $compVoulu." OR A.nom = '".mysqli_real_escape_string($mysqli, trim($tabCompVoulu[$i]))."'";
					}	
				}
				$compVoulu =  $compVoulu.")";
				$requeteDyn = $requeteDyn.$compVoulu;
			}
			if (!empty($tabCompNonVoulu))
			{
				for ($i=0;$i<sizeof($tabCompNonVoulu);$i++)
				{
					if($i==0)
					{	
						$compNonVoulu =  " AND (A.nom <> '".mysqli_real_escape_string($mysqli, trim($tabCompNonVoulu[0]))."'";
					}
					else
					{
						$compNonVoulu =  $compNonVoulu." AND A.nom <> '".mysqli_real_escape_string($mysqli, trim($tabCompNonVoulu[$i]))."'";
					}	
				}
				$compNonVoulu =  $compNonVoulu.")";
				$requeteDyn = $requeteDyn.$compNonVoulu;
			}
		
			if($requeteTest == $requeteDyn)
			{
				$requeteDyn = 'SELECT nom 
								FROM aliments 
								WHERE idAl = -1;';
			}
			else
			{
				$requeteDyn = $requeteDyn.';';
		
			}
			echo $requeteDyn;


			$resultat = query($mysqli,$requeteDyn);
?>
			<center>
			<h1>Resultat de la recherche (par odre de satisfaction)<h1>
			</center>
<?php
			if(mysqli_num_rows ($resultat ) > 0)
			{
				$i = 0;
?>				

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
				
					$estContenu = false;
					$titre = mysqli_real_escape_string($mysqli, $nuplet['titre']);
					$requeteIng = "SELECT A.nom 
									FROM aliments A,recettes R,composantsderecettes C 
									WHERE R.titre = '$titre' 
									AND A.idAl = C.idAl 
									AND R.idRec = C.idRec;";
					$resultat3 = query($mysqli,$requeteIng);
					$listeIngerdients = array();
					While($nuplet2 = mysqli_fetch_assoc($resultat3) )
					{
						array_push($listeIngerdients,$nuplet2['nom']); 
					}
					if(!empty($tabCompNonVoulu))
					{
						foreach($tabCompNonVoulu as $elt)
						{
							if(in_array($elt,$listeIngerdients))
							{
								$estContenu = true;
							}						
						}
					}
					if($estContenu == false)
					{
						$i++;
						$idRec = mysqli_real_escape_string($mysqli, $nuplet['idRec']);
				
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
			<tr>
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
				}
?>
				<center>
				<h2><?php echo $i?> Recette(s) a(ont) ete trouvee(s)<h2>
				</center>
<?php
			}
			else
			{
			 echo "<h2> Aucune recette n'a ete trouve!!!!<h2>";	
			}
		}
?>

		</tbody>
		</table>
		</div>
	</article>	
</body>
	<?php include ("PiedsDePage.php");?>
</html>