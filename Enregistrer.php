<?php 	
session_start();

if(isset($_POST['submit']))  // le formulaire vient d'être soumis
  { 
  	include("Parametres.php");
	include("Fonctions.inc.php");

	// Connexion au serveur MySQL
	$mysqli=mysqli_connect($host,$user,$pass) or die("Problème de création de la base :".mysqli_error());
	mysqli_select_db($mysqli,$base) or die("Impossible de sélectionner la base : $base");

	
// Vérification du formulaire
	$pseudoStatus='ok';
	$mdpStatus='ok';
	$mdp2Status='ok';		
	$messageErreur="";
	$formStatus='ok';
	
	
	// Vérification du pseudo (vaut 'f' ou 'h') 
    if( !isset($_POST['pseudo']) 			// la variable pseudo n'est pas positionnée
	  )
      { 
	    $pseudoStatus='error';
	  }
	 else
	 {
		$pseudo = mysqli_real_escape_string($mysqli, trim($_POST['pseudo']));
		$mdp = mysqli_real_escape_string($mysqli, $_POST['mdp']);
		$mdp2 = mysqli_real_escape_string($mysqli, $_POST['mdp2']);
		$nom = mysqli_real_escape_string($mysqli, trim($_POST['nom']));
		$prenom = mysqli_real_escape_string($mysqli, trim($_POST['prenom']));
		
		$requeteUtilisateurs = "SELECT * 
								FROM utilisateurs 
								WHERE pseudo = '$pseudo';";
		$resultat = query($mysqli,$requeteUtilisateurs);
		if(mysqli_num_rows ($resultat )>0)
		{
			$messageErreur ='le pseudo: '.$pseudo.' existe deja';
			$pseudoStatus='error';
		}
		else
		{
			if($mdp == $mdp2)
			{
				$requeteInsertion = "INSERT INTO utilisateurs (pseudo,mdp,nom,prenom) VALUES ('$pseudo','$mdp','$nom','$prenom')";
				$resultat = query($mysqli,$requeteInsertion)	or die("Problème d'insertion dans la base :".mysqli_error());
				$formStatus ='cache';
			}
			else
			{
				$messageErreur = 'les mots de passe ne sont pas identiques';
				$mdpStatus='error';	
				$mdp2Status='error';	
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
		
		<article id="RegForm">
			<fieldset id="formReg" class="<?php echo $formStatus; ?>">
				<legend><b>Enregistrement d'un utilisateur</b></legend>
				<form method="post" action="#" >
					Pseudo : <input id="pseudo" class="<?php echo $pseudoStatus; ?>" type="text" size="25" maxlength="25" name="pseudo" 
									value="<?php if(isset($_POST['pseudo']))  echo $_POST['pseudo']; ?>"required="required"/>	
					<br />
					mot de passe :<input id="mdp" class="<?php echo $mdpStatus; ?>" type="password" size="25" maxlength="25" name="mdp" 
					value="<?php if(isset($_POST['mdp']))  echo $_POST['mdp']; ?>"required="required"/>	
					<br />
					resaisissez mot de passe  : <input id="mdp2" class="<?php echo $mdp2Status; ?>" type="password" size="25" maxlength="25" name="mdp2" 
					value="<?php if(isset($_POST['mdp2']))  echo $_POST['mdp2']; ?>"required="required"/>	
					<br />
					Nom :<input id="nom" type="text" size="25" maxlength="25" name="nom" 
					value="<?php if(isset($_POST['nom']))  echo $_POST['nom']; ?>"required="required"/>	
					<br />
					Prenom : <input id="prenom" type="text" size="25" maxlength="25" name="prenom" 
					value="<?php if(isset($_POST['prenom']))  echo $_POST['prenom']; ?>"required="required"/>	
					<br />
	
					<br /> <input type="submit" name="submit" value="Enregistrer">		
				</form>
			</fieldset>
<?php 
if(isset($_POST['submit'])) 	// le formulaire a été soumis
  { 
	if(empty($messageErreur))
	{
		echo "<b><h2>Ajout de l'utilisateur ".$pseudo." reussi<h2></b>";
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