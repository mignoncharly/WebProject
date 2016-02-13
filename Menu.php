<?php 

?>
<div id = "menu">
<?php
	if(!isset($_SESSION['init'])||$_SESSION['init']==false)
	{
		echo "<h3>Veuillez Initialiser la base de Donnees</h3>";
	}
	else
	{
?>
	<ul>
		<li/><h3><a href="Recettes.php">Liste des recettes</a></h3>
		<li/><h3><a href="Enregistrer.php">s'enregistrer</a></h3>
<?php
		if(empty($_SESSION['users'])||!isset($_SESSION['users']))
		{
?>
			<li/><h3><a href="Connexion.php">connexion</a></h3>	
<?php
		}
		else
		{
?>
			<br/>
			<br/>
			salut <?php echo $_SESSION['users'];?>
			<li/><h3><a href="Modification.php">Modifier vos Donnees personnelles</a></h3>
			<li/><h3><a href="Recherche.php">Recherche recettes</a></h3>
			<li/><h3><a href="Deconnexion.php">deconnexion</a></h3>
<?php
		}
?>
	</ul>
<?php
	}
?>
</div>
