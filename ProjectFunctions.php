<?php
	/*
		This file is used to store every function the website uses in order to properly function.
		The deletion of one function might break one or more pages.  Please be sure of what you are doing before deleting one.
	*/
	
	//	Creates a PDO object and attempts to connect to the database.
	function ConnectToBD(){
		$connection = new PDO('mysql:host=localhost;dbname=mydb;charset=utf8', 'rejean', 'RJN_Alanon', array(PDO::ATTR_PERSISTENT => true));
		
		return $connection;
	}
	
	/*
		Lists every books available in the database that matches the specified criterias. Displays them in a table format afterwards.
		-$filter specifies the current filter criteria with which the database will search.
		-$isUserSearch contains TRUE if the user used the search textbox.
		-$booksToSkip specifies a list of books that will not be listed in the displayed table.
		-$isAdminDisplay specifies if the table should show the options to update and delete an entry or add an entry to the current cart.
	*/
	function ShowBooks($filter, $isUserSearch, $isAdminDisplay = FALSE){
		try{
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("call ListBooks(?,?)");
			$stm->bindParam(1, $filter, PDO::PARAM_STR, 255);
			$stm->bindParam(2, $isUserSearch, PDO::PARAM_BOOL);
			$stm->execute();
			
			if($isAdminDisplay) {
				while($data = $stm->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
					echo '<tr><td>'.$data["BookCode"].'</td><td>'.$data["BookName"].'</td><td>'.FormatCurrency($data["BookCost"]).'</td>';
					echo '<td><a href="UpdateBook.php/?id='.$data["idBooks"].'">Modifier</a></td></tr>';
				}
			} else {
				while($data = $stm->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
					if(TRUE!==array_search($data["BookCode"], $_SESSION["Cart"], TRUE)) {
						echo '<tr><td>'.$data["BookCode"].'</td><td>'.$data["BookName"].'</td><td>'.FormatCurrency($data["BookCost"]).'</td>';
						echo '<td><button type="button" class="AddToCart" data-id="'.$data["idBooks"].'">Ajouter au panier</button>';
					}
				}
			}
			
			$stm = NULL;
			$connection = NULL;
		} catch(PDOException $e) {
			print "Erreur!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	/*
		Lists every book that got moved to the cart ($_COOKIE["booksInCart"]).
	*/
	function AddToCart($id) {
		$filter = "";
		$isUserSearch = FALSE;
		try{
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("call GetBookInfo(?)");
			$stm->bindParam(1, $id, PDO::PARAM_STR, 255);
			$stm->execute();
			
			$data = $stm->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
			$row = '<tr><td>'.$data["BookCode"].'<input type="hidden" name="Code[]" value="'.$data["BookCode"].'"/></td><td>'.$data["BookName"].'<input type="hidden" name="Name[]" value="'.$data["BookName"].'"/></td><td>'.FormatCurrency($data["BookCost"]).'<input type="hidden" name="Cost[]" value="'.$data["BookCost"].'"/></td><td><input type="number" name="Quantity[]" value="1"/></td><td><button type="button" onclick="MoveToLibrary('.$data["BookCode"].')" value="Supprimer du panier"/></td>';
			
			$stm = NULL;
			$connection = NULL;
			return $row;
		} catch (PDOException $e) {
			print "Erreur!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	/*
		Calculates the total cost of every book moved to the cart ($_COOKIE["booksInCart"]).
	*/
	function CalculateTotal() {
		$Cart = json_encode($_SESSION["Cart"], JSON_NUMERIC_CHECK);
		try{
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("select CalculateTotal(?)");
			$stm->bindParam(1, $Cart, PDO::PARAM_STR);
			$stm->execute();
			
			$data = $stm->fetch();
			/*echo '<tr><td colspan="4">'.FormatCurrency($data[0]).'</td></tr>';*/
			
			$stm = NULL;
			$connection = NULL;
			
			return FormatCurrency($data[0]);
		} catch(PDOException $e) {
			print "Erreur!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	/*
		Formats a decimal to a currency format or from currency format to decimal format (eg.1: 0011.00 = 11,00 $ | eg.2: 0,00 = Gratuit).
		-$decimal is the value to format.
		-$formatToBD determines if $decimal is to be formated in the MySQL decimal format (eg.: 0011.00).
			TRUE  = Formats from currency format to MySQL decimal format (6 digits with 2 precision digits).
			FALSE = Formats from MySQL decimal format to currency format 
	*/
	function FormatCurrency($decimal, $formatToBD = false) {
		if($formatToBD) {
			if($decimal == 'Gratuit' || $decimal == 'gratuit'){
				return 0.00;
			} else {
				return str_replace(',', '.', $decimal);
			}
		} else {
			if($decimal == 0.00) {
				return "Gratuit";
			} else {
				$value = strval($decimal);
				for($i = 0; $i < strlen($value); ++$i) {
					if ($value[$i] != '0') {
						if ($value[$i] == '.') {
							$value = substr($value, $i-1);
							str_replace('.', ',', $value);
							return ($value . ' $');
						}
						$value = substr($value, $i);
						str_replace('.', ',', $value);
						return ($value . ' $');
					}
				}
			}	
		}
	}
	
	/*
		Truncates every leading zeros until only one "0" is left before the "." character or until a digit other than "0" is met.
		-$decimal is the value from which the "0" characters are truncated.
	*/
	function RemoveLeadingZeros($decimal) {
		$value = strval($decimal);
		for($i = 0; $i < strlen($value); ++$i) {
			if ($value[$i] != '0') {
				if ($value[$i] == '.') {
					return substr($value, $i-1);
				}
				return substr($value, $i);
			}
		}
	}
	
	/*
		Lists every orders that are currently active (not "Canceled" or "Completed") in a table. 
	*/
	function ShowActiveOrders() {
		try {
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("call ListActiveOrders");
			$stm->execute();
			while($data = $stm->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
				echo "<tr><td>".$data["OrderStatus"]."</td><td>".$data["OrderNumber"]."</td><td>".$data["OrderDate"]."</td><td>".$data["NbItems"]."</td><td>".FormatCurrency($data["TotalCost"])."</td></tr>";
			}
			
			$stm = NULL;
			$connection = NULL;
		} catch(PDOException $e) {
			print "Erreur!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	/*
		Adds a new book in the database.
		-$bookTitle is the title of the new book to be registered.
		-$bookCode is the code for the new book to be registered.
		-$bookCost is the cost for the new book to be registered.
	*/
	function AddBook($bookTitle, $bookCode, $bookCost) {
		try{
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("call AddBook(?, ?, ?)");
			$stm->bindParam(1, $bookTitle, PDO::PARAM_STR, 100);
			$stm->bindParam(2, $bookCode, PDO::PARAM_STR, 10);
			$stm->bindParam(3, $bookCost, PDO::PARAM_STR, 7);
			$stm->execute();
			
			$stm = NULL;
			$connection = NULL;
		} catch(PDOException $e) {
			print 'Erreur!: ' . $e->getMessage() . '<br/>';
			die();
		}
	}
	
	/*
		Gets the title, the code and the cost of a book.  The book is determined by the given Id.
		-$bookId is the Id from which the search is performed.
	*/
	function GetBookInfo($bookId) {
		try{
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("call GetBookInfo(?)");
			$stm->bindParam(1, $bookId, PDO::PARAM_INT, 10);
			$stm->execute();
			$data = $stm->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
			
			$stm = NULL;
			$connection = NULL;
			return $data;
		} catch(PDOException $e) {
			print 'Erreur!: ' . $e->getMessage() . '<br/>';
			die();
		}
	}
	
	/*
		Updates the title, the code and the cost of a book.  The book is determined by the given Id.
		-$bookTitle is the new title of the book.
		-$bookCode is the new code for the book.
		-$bookCost is the new cost for the book.
		-$bookId is the Id on which the update is performed.
	*/
	function UpdateBookInfo($bookTitle, $bookCode, $bookCost, $bookId) {
		try{
			$connection = ConnectToBD();
			
			$stm = $connection->prepare("call UpdateBook(?, ?, ?, ?)");
			$stm->bindParam(1, $bookId, PDO::PARAM_INT, 10);
			$stm->bindParam(2, $bookTitle, PDO::PARAM_STR, 100);
			$stm->bindParam(3, $bookCode, PDO::PARAM_STR, 10);
			$stm->bindParam(4, $bookCost, PDO::PARAM_STR, 7);
			$stm->execute();
			
			$stm = NULL;
			$connection = NULL;
		} catch(PDOException $e) {
			print 'Erreur!: ' . $e->getMessage() . '<br/>';
			die();
		}
	}
?>
