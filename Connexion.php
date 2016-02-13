<?php 	
session_start();
if(!isset($_SESSION['users']))
{
	$_SESSION['users']="";
}
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
	$formStatus='ok';
	$message="";
	$deconnecte='cache';
	$connecte='cache';
	// Vérification du pseudo (vaut 'f' ou 'h') 
    if( !isset($_POST['pseudo']))			// la variable pseudo n'est pas positionnée
      { 
	    $pseudoStatus='error';
	  }
	 else
	 {
		$pseudo = mysqli_real_escape_string(trim($_POST['pseudo']));
		$mdp = mysqli_real_escape_string($_POST['mdp']);
		
		$requeteUtilisateurs = "SELECT * 
								FROM utilisateurs 
								WHERE pseudo = '$pseudo' 
								AND mdp = '$mdp';";
		$resultat = query($mysqli,$requeteUtilisateurs);
		//echo mysqli_num_rows ($resultat );
		if(mysqli_num_rows ($resultat )== 0)
		{
			$messageErreur = 'login et/ou mot de passe incorrect';
			$pseudoStatus='error';	
			$mdpStatus='error';	

		}
		else
		{
			$message ='Bienvenu: '.$pseudo;
			$formStatus ='cache';
			?>			
<?php
		$_SESSION['users']=$pseudo;

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
				<legend><b>Connexion</b></legend>
				<form method="post" action="#">
					Login : <input id="pseudo" class="<?php echo $pseudoStatus; ?>" type="text" size="25" maxlength="25" name="pseudo" 
									value="<?php if(isset($_POST['pseudo']))  echo $_POST['pseudo']; ?>"required="required"/>	
					<br />
					mot de passe :<input id="mdp" class="<?php echo $mdpStatus; ?>" type="password" size="25" maxlength="25" name="mdp" 
					value="<?php if(isset($_POST['mdp']))  echo $_POST['mdp']; ?>"required="required"/>	
					<br />
	
					<br /> <input type="submit" name="submit" value="Connexion">		
				</form>
			</fieldset>
<?php 
if(isset($_POST['submit'])) 	// le formulaire a été soumis
  { 
	if(empty($messageErreur))
	{
		if(!empty($message))
		{
?>
			
<?php
			echo "<b>".$message."</b>";
		}
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