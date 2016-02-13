<DOCTYPE html>
<?php
	session_start();
	if(isset($_SESSION['users']))
	{
		unset($_SESSION['users']);
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
	<center>
		<h2>Vous vous etes deconnecte avec succes!!!</h2>
	</center>
</article>

</body>
	<?php include ("PiedsDePage.php");?>
</html>