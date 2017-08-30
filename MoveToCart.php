<?php
	require "ProjectFunctions.php";

	if($_SERVER["REQUEST_METHOD"] != "POST") {
		header('Location: Library.php');
	}
	session_start();
	$_SESSION["Cart"][] = $_POST["Id"];
	$response["row"] = AddToCart($_POST["Id"]);
	$response["cartTotal"] = CalculateTotal();
	
	echo json_encode($response);
?>
