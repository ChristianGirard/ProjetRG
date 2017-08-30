<!DOCTYPE html>

<?php
	require "ProjectFunctions.php";
?>

<html>
<head>
	<title>Admin Dashboard</title>
	<link rel="stylesheet" type="text/css" href="ProjetRG.css"/>
</head>
<body>
	<h1>Dashboard administrateur</h1>
	
	<a href="AddBook">Ajouter un livre</a>
	</br>
	<div id="books">
		<table border="2px solid black">
			<caption><h3>Gestion des livres</h3></caption>
			<thead>
				<tr>
					<td>Code</td>
					<td>Titre</td>
					<td>Coût</td>
				</tr>
			</thead>
			<tbody>
				<?php ShowBooks("", FALSE, TRUE); ?>
			</tbody>
		</table>
	</div>
	<div id="orders">
		<table id="orders" border="2px solid black">
			<caption><h3>Gestion des commandes</h3></caption>
			<thead>
				<tr>
					<td>Statut</td>
					<td>No°</td>
					<td>Date</td>
					<td>Nombre d'items</td>
					<td>Coût total</td>
				</tr>
			</thead>
			<tbody>
				<?php ShowActiveOrders(); ?>
			</tbody>
		</table>
	</div>
</body>
</html>
