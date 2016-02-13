<DOCTYPE html>
<?php
	session_start();
	if(isset($_SESSION['users']))
	{
		unset($_SESSION['users']);
	}
	if(!isset($_SESSION['init']))
	{
		$_SESSION['init'] = false;
	}
	else
	{
		$_SESSION['init'] = true;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Deconnection</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="style.css" />
</head>
<?php include ("Entete.php");?>
<body>
<?php
  include ("Menu.php");
?>

<article id="Recettes">
<fieldset>
    <legend>Initialisation de la base de données</legend>
	<form target="Resultat" action="CreationBdd.php">
	    <input type="submit" value="Initialiser">	
	</form>
</fieldset>
</article>

</body>
	<?php include ("PiedsDePage.php");?>
</html>