<?php
	require "ProjectFunctions.php";
	
	$form_err = "";
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(!empty($_POST['Title']) && !empty($_POST['Code']) && !empty($_POST['Cost'])) {
			AddBook($_POST['Title'], $_POST['Code'], $_POST['Cost']);
			header('Location: DashboardAdmin.php');
		} else {
			$form_err = "Veuillez remplir tous les champs.";
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Ajout d'un livre</title>
	<link rel="stylesheet" type="text/css" href="ProjetRG.css"/>
</head>
<body>
	<h1>Ajout d'un livre</h1>
	<span id="FormErr"><?php echo $form_err; ?></span>
	<form action="AddBook.php" method="post">
		Titre (100 caractères maximum): <br/>
		<input type="text" name="Title" size="100" maxlength="100"/>
		<br/>
		Code (10 caractères maximum): <br/>
		<input type="text" name="Code" maxlength="10"/>
		<br/>
		Prix (veuillez suivre l'exemple ci-dessous): <br/>
		<input type="text" pattern="[0-9]{1,4}[,.][0-9]{2}" Title="Le coût doit avoir 1 à 4 chiffres avant une virgule ou un point et 2 chiffres après." placeholder="1234,56" name="Cost"/> CAD $
		<br/><br/>
		<input type="submit" value="Soumettre"/>
	</form>

</body>
</html>
