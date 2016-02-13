<?php 	
session_start();

if(isset($_POST['submit']))  // le formulaire vient d'être soumis
  { 
  	include("Parametres.php");
	include("Fonctions.inc.php");
	
	
	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");

	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");

	
// Vérification du formulaire
	$pseudoStatus='ok';
	$mdpStatus='ok';
	$mdp2Status='ok';		
	$messageErreur="";
	$formStatus='ok';
	$mdp1Status='ok';
	
	
	// Vérification du pseudo (vaut 'f' ou 'h') 

		$mdp1 = mysqli_real_escape_string($mysqli, $_POST['mdp1']);

		if(!empty($_POST['mdp']))
		{
			$mdp = mysqli_real_escape_string($mysqli, $_POST['mdp']);
		}
		if(!empty($_POST['mdp2']))
		{
			$mdp2 = mysqli_real_escape_string($mysqli, $_POST['mdp2']);
		}
		if(!empty($_POST['nom']))
		{
			$nom = mysqli_real_escape_string($mysqli, trim($_POST['nom']));
		}

		if(!empty($_POST['prenom']))
		{
			$prenom = mysqli_real_escape_string(trim($mysqli, $_POST['prenom']));
		}
		
		if(!empty($_POST['pseudo']))
		{
			$pseudo = mysqli_real_escape_string($mysqli, $_POST['pseudo']);
			$requeteUtilisateurs = "SELECT * 
									FROM utilisateurs 
									WHERE pseudo = '$pseudo';";
			$resultat = query($mysqli,$requeteUtilisateurs);
			if(mysqli_num_rows ($resultat )>0 && $pseudo != $_SESSION['users'])
			{
				$messageErreur ='le pseudo: '.$pseudo.' existe deja';
				$pseudoStatus='error';
			}
			else
			{
				$pseudo2 = mysqli_real_escape_string($mysqli, $_SESSION['users']);
				$requeteUtilisateurs = "SELECT * 
										FROM utilisateurs 
										WHERE pseudo = '$pseudo2';";
				$resultat = query($mysqli,$requeteUtilisateurs);
				$nuplet=mysqli_fetch_assoc($resultat);
				$ancienMdp = $nuplet['mdp'];
				
				if($mdp1!= $ancienMdp)
				{
					$messageErreur="Mot de passe errone";
					$mdp1Status='error';
				}
				else
				{
					if(!isset($mdp))
					{
						$mdp ="";
					}
					if(!isset($mdp2))
					{
						$mdp2 ="";
					}
				
					if($mdp == $mdp2)
					{
						$idUs = $nuplet['idUs'];
						if($mdp == "")
						{
							$mdp = mysqli_real_escape_string($mysqli, $nuplet['mdp']);
							$mdp2 = mysqli_real_escape_string($mysqli, $nuplet['mdp']);
						}
						if(!isset($nom))
						{
							$nom = mysqli_real_escape_string($mysqli, $nuplet['nom']);
						}
						if(!isset($prenom))
						{
							$prenom = mysqli_real_escape_string($mysqli, $nuplet['prenom']);
						}
						$requeteInsertion = "UPDATE utilisateurs 
											SET pseudo = '$pseudo',mdp='$mdp',nom ='$nom',prenom = '$prenom' 
											WHERE idUs ='$idUs'";
						$resultat = query($mysqli,$requeteInsertion)	or die("Problème d'insertion dans la base :".mysqli_error());
						$formStatus ='cache';
						if($pseudo != $pseudo2)
						{
							$_SESSION['users'] = $pseudo;
						}
					}
					else
					{
						$messageErreur = 'les mots de passe ne sont pas identique';
						$mdpStatus='error';	
						$mdp2Status='error';	
					}
				}
			}
		}
		else
		{
			$pseudo = mysqli_real_escape_string($mysqli, $_SESSION['users']);
			$requeteUtilisateurs = "SELECT * 
									FROM utilisateurs 
									WHERE pseudo = '$pseudo';";
			$resultat = query($mysqli,$requeteUtilisateurs);

		
			if(mysqli_num_rows ($resultat )==1)
			{
				$nuplet=mysqli_fetch_assoc($resultat);
				$ancienMdp = $nuplet['mdp'];
				if($mdp1!= $ancienMdp)
				{
					$messageErreur="Mot de passe errone";
					$mdp1Status='error';
				}
				else
				{
					if($mdp == $mdp2)
					{
						$idUs=mysqli_real_escape_string($mysqli, $nuplet['idUs']);
						if(!isset($nom))
						{
							$nom = mysqli_real_escape_string($mysqli, $nuplet['nom']);
						}
						if(!isset($prenom))
						{
							$prenom = mysqli_real_escape_string($mysqli, $nuplet['prenom']);
						}
						$requeteInsertion = "UPDATE utilisateurs 
										 	 SET pseudo = '$pseudo',mdp='$mdp',nom ='$nom',prenom = '$prenom' 
											 WHERE idUs ='$idUs';";
						$resultat = query($mysqli,$requeteInsertion)	or die("Problème d'insertion dans la base :".mysqli_error());
						$formStatus ='cache';
					}
					else
					{
						$messageErreur = 'les mots de passe ne sont pas identique';
						$mdpStatus='error';	
						$mdp2Status='error';	
					}
				}
			}
		
		}
	
  }
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

	<?php include ("Menu.php");?>
	<?php
	include("Parametres.php");
	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");

	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");
	
		$pseudo = mysqli_real_escape_string($mysqli, $_SESSION['users']);
		$requeteUtilisateurs = "SELECT * 
								FROM utilisateurs 
								WHERE pseudo = '$pseudo';";
		$resultat = mysqli_query($mysqli,$requeteUtilisateurs);
		$nuplet = mysqli_fetch_assoc($resultat);
		$mdp = $nuplet['mdp'];
		$nom = $nuplet['nom'];
		$prenom = $nuplet['prenom'];
		
	?>
		<article id="RegForm">
			<fieldset id="formReg" class="<?php echo $formStatus; ?>">
				<legend><b>Modification des donnees personnelles</b></legend>
				<form method="post" action="#" >
					Pseudo : <input id="pseudo" class="<?php echo $pseudoStatus; ?>" type="text" size="25" maxlength="25" name="pseudo" 
									value="<?php if(!empty($_POST['pseudo'])) {echo $_POST['pseudo'];} else {echo $pseudo;}?>"/>	
					<br />
					mot de passe :<input id="mdp1" class="<?php echo $mdp1Status; ?>" type="password" size="25" maxlength="25" name="mdp1" 
					value="<?php if(isset($_POST['mdp1']))  echo $_POST['mdp1']; ?>" required="required"/>	
					<br />
					nouveau mot de passe :<input id="mdp" class="<?php echo $mdpStatus; ?>" type="password" size="25" maxlength="25" name="mdp" 
					value="<?php if(isset($_POST['mdp']))  {echo $_POST['mdp'];} ?>"/>	
					<br />
					resaisissez mot de passe  : <input id="mdp2" class="<?php echo $mdp2Status; ?>" type="password" size="25" maxlength="25" name="mdp2" 
					value="<?php if(isset($_POST['mdp2']))  {echo $_POST['mdp2'];}  ?>"/>	
					<br />
					Nom :<input id="nom" type="text" size="25" maxlength="25" name="nom" 
					value="<?php if(!empty($_POST['nom']))  {echo $_POST['nom'];} else {echo $nom;}?>"/>	
					<br />
					Prenom : <input id="prenom" type="text" size="25" maxlength="25" name="prenom" 
					value="<?php if(!empty($_POST['prenom'])) { echo $_POST['prenom'];}  else {echo $prenom;}?>"/>	
					<br />
	
					<br /> <input type="submit" name="submit" value="Enregistrer">		
				</form>
			</fieldset>
<?php 
if(isset($_POST['submit'])) 	// le formulaire a été soumis
  { 
	if(empty($messageErreur))
	{
		echo "<b><h2>Modification des donnees de l'utilisateur ".$pseudo." reussie<h2></b>";
	}
	else
	{ 
		echo '<br /><b>Rapport d\'erreur :</b><ul> '.$messageErreur.'</ul>';
	}
  }
?>			
		</article>

</body>
	<?php include ("PiedsDePage.php");?>
</html>