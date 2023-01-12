<?php 

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

	// Select statement om data te verzamelen uit de database
    $klanten = $db->select("SELECT * FROM klanten",[]);

    $columns = array_keys($klanten[0]);
    $row_data = array_values($klanten);

    // Function klantgegevens editen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {

        $klant_id = htmlspecialchars(trim($_POST['klant_id']));
        $naam = htmlspecialchars(trim($_POST['naam']));
        $telefoon = htmlspecialchars(trim($_POST['telefoon']));
        $email = htmlspecialchars(trim($_POST['email']));
        $bday = htmlspecialchars(trim($_POST['bday']));
        
        $db->edit_klanten($klant_id, $naam, $telefoon, $email, $bday);
    }

    // Function klantgegevens verwijderen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {

        $klant_id = htmlspecialchars(trim($_POST['klant_id']));

        $db->select("DELETE FROM klanten WHERE ID = :klant_id",[':klant_id' => $klant_id]);
        header("refresh:1;");
        echo '<script>alert("Klantgegevens zijn succesvol verwijderd!")</script>';
    }

?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Klanten</title>
</head>
<body>
	<?php include 'navigatie.php'; ?>
	<table>
		<thead>
			<tr>
				<th>Naam</th>
				<th>Email</th>
				<th>Telefoonnummer</th>
                <th>Geboortedatum</th>
			</tr>
		</thead>
		<tbody><h1>Klantgegevens Editen of Verwijderen</h1>
			<?php foreach ($row_data AS $data) { ?>
				<form method="post">				
					<tr>
						<input type="hidden" name="klant_id" value="<?php echo $data["ID"] ?>">

						<td><input type="text" name="naam" value="<?php echo $data["Naam"] ?>"></td>
						<td><input type="text" name="email" value="<?php echo $data["Email"] ?>"></td>
						<td><input type="number" name="telefoon" value="<?php echo $data["Telefoon"] ?>"></td>
						<td><input type="date" name="bday" value="<?php echo $data["Birthday"] ?>"></td>

						
						<td><button type="sumbit" name="edit" onclick="return confirm('Doorgaan met editen?');">Edit</button></td>
						<td><button type="sumbit" name="delete" onclick="return confirm('Doorgaan met verwijderen?');">Delete</button></td>
					</tr>
				</form>
			<?php } ?>	
		</tbody>
	</table>
</body>
</html>