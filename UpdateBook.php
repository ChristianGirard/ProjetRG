<?php
	require "ProjectFunctions.php";
	
	$form_err = "";
	
	if(empty($_GET["id"])){
		if($_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Location: http://localhost/projetrg/dashboardAdmin.php');
		}
		if (!empty($_POST["Title"]) && !empty($_POST["Code"]) && !empty($_POST["Cost"])) {
			UpdateBookInfo($_POST["Title"], $_POST["Code"], FormatCurrency($_POST["Cost"], TRUE), $_POST["Id"]);
			header('Location: http://localhost/projetrg/dashboardAdmin.php');
		} else {
			$form_err = "Veuillez remplir tous les champs.";
			$bookData = GetBookInfo($_POST["Id"]);
			$id = $_POST["Id"];
		}
	} else {
		$bookData = GetBookInfo($_GET["id"]);
		$id = $_GET["id"];
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Modification d'un livre</title>
	<link rel="stylesheet" type="text/css" href="ProjetRG.css"/>
</head>
<body>
	<h1>Modification d'un livre</h1>
	<span id="FormErr"><?php echo $form_err; ?></span>
	<form action="http://localhost/projetRG/UpdateBook.php" method="post">
		<input type="hidden" name="Id" value="<?php echo $id; ?>"/>
		Titre (100 caractères maximum): <br/>
		<input type="text" name="Title" value="<?php echo $bookData["BookName"]; ?>" size="100" maxlength="100"/>
		<br/><br/>
		Code (10 caractères maximum): <br/>
		<input type="text" name="Code" value="<?php echo $bookData["BookCode"]; ?>" size="10" maxlength="10"/>
		<br/><br/>
		Coût (ex.: 1234.56): <br/>
		<input type="text" pattern="[0-9]{1,4}[.][0-9]{2}" title="Le coût doit être un nombre à virgule et doit comporter 6 chiffres dont 2 après la virgule." 
		name="Cost" value="<?php echo RemoveLeadingZeros($bookData["BookCost"]); ?>"/>
		<br/><br/>
		<input type="submit" value="Soumettre la modification"/>
	</form>
</body>
</html>
