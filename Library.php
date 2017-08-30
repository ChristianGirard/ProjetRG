<?php
	session_start();
	$_SESSION["Cart"] = array();
	require "ProjectFunctions.php";
?>

<!DOCTYPE html>
<html>
<head>
	<title>Magasin</title>
	<link rel="stylesheet" type="text/css" href="ProjetRG.css"/>
</head>
<body>
	<h1>Magasin</h1>
	
	<div id="Library">
		<table border="solid 2px black">
			<caption><h3>Librairie</h3></caption>
			<thead>
				<tr>
					<td>Code</td>
					<td>Titre</td>
					<td>Coût</td>
				</tr>
			</thead>
			<tbody>
				<?php ShowBooks("", FALSE); ?>
			</tbody>
		</table>
	</div>
	<div id="Cart">
		<table border="solid 2px black">
			<caption><h3>Panier d'achat</h3></caption>
			<thead>
				<tr>
					<td>Code</td>
					<td>Titre</td>
					<td>Coût</td>
					<td>Quantité</td>
				</tr>
			</thead>
			<tfoot>
				<tr><td id="cartTotal" colspan="4"></td></tr>
			</tfoot>
			<tbody id="cartBody">
			</tbody>
		</table>
	</div>
	
	<script src="jquery-3.2.1.js"></script>
	<script>
		$('.AddToCart').click(function(e) {
			e.preventDefault()
			var id = $(this).data('id');
			$.post('MoveToCart.php',
				{Id: id},
				function(response){
					var data = JSON.parse(response);
					$('#cartBody').append(data["row"]);
					$('#cartTotal').html(data["cartTotal"]);
				});
		})
	</script>
</body>
</html>
