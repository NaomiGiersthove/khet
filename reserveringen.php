<?php 

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

    // Huidige datum in een variable stoppen
    $current_date = date('Y-m-d');

    // Select statement om data te verzamelen uit de database
    $reserveringen = $db->select("SELECT * FROM reserveringen INNER JOIN klanten on reserveringen.Klant_ID = klanten.ID WHERE reserveringen.Datum = :lala AND Status = 1",[':lala' => $current_date]);

    if (!empty($reserveringen)) {
        $columns = array_keys($reserveringen[0]);
        $row_data = array_values($reserveringen);
    }

    // Function reserveringen editen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {

        $reservering_id = htmlspecialchars(trim($_POST['reservering_id']));
        $klant_id = htmlspecialchars(trim($_POST['klant_id']));
        $naam = htmlspecialchars(trim($_POST['naam']));
        $telefoon = htmlspecialchars(trim($_POST['telefoon']));
        $email = htmlspecialchars(trim($_POST['email']));
        $bday = htmlspecialchars(trim($_POST['bday']));
        $tafel = htmlspecialchars(trim($_POST['tafel']));
        $datum = htmlspecialchars(trim($_POST['datum']));
        $tijd = htmlspecialchars(trim($_POST['tijd']));
        $aantal = htmlspecialchars(trim($_POST['aantal']));
        $status = htmlspecialchars(trim($_POST['status']));
        $datum_toegevoegd = htmlspecialchars(trim($_POST['datum_toegevoegd']));
        $allergieen = htmlspecialchars(trim($_POST['allergieen']));
        $opmerkingen = htmlspecialchars(trim($_POST['opmerkingen']));

        $db->edit_reserveringen($reservering_id, $klant_id, $naam, $telefoon, $email, $bday, $tafel, $datum, $tijd, $aantal, $status, $datum_toegevoegd, $allergieen, $opmerkingen);
    }

    // Function melding voor niet gekomen klanten
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['no_show'])) {

        $reservering_id = htmlspecialchars(trim($_POST['reservering_id']));

        $db->select("UPDATE reserveringen SET Status = :no_show WHERE ID = :reservering_id",[':no_show' => 0, ':reservering_id' => $reservering_id]);

        header("refresh:1;");
        echo '<script>alert("Klant is genoteerd!")</script>';
    }

    // Function reserveringen toevoegen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {

        $naam = htmlspecialchars(trim($_POST['naam']));
        $telefoon = htmlspecialchars(trim($_POST['telefoon']));
        $email = htmlspecialchars(trim($_POST['email']));
        $bday = htmlspecialchars(trim($_POST['bday']));
        $tafel = htmlspecialchars(trim($_POST['tafel']));
        $datum = htmlspecialchars(trim($_POST['datum']));
        $tijd = htmlspecialchars(trim($_POST['tijd']));
        $aantal = htmlspecialchars(trim($_POST['aantal']));
        $allergieen = htmlspecialchars(trim($_POST['allergieen']));
        $opmerkingen = htmlspecialchars(trim($_POST['opmerkingen']));

        $db->add_reservering($naam, $telefoon, $email, $bday, $tafel, $datum, $tijd, $aantal, $allergieen, $opmerkingen);
    }

    // Function reserveringen verwijderen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {

        $reservering_id = htmlspecialchars(trim($_POST['reservering_id']));

        $db->select("DELETE FROM reserveringen WHERE ID = :reservering_id",[':reservering_id' => $reservering_id]);

        header("refresh:1;");
        echo '<script>alert("Reservering is succesvol verwijderd!")</script>';
    }

?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Reserveringen</title>
</head>
<body>

	<?php include 'navigatie.php'; ?>

	<?php if (!empty($reserveringen)) { ?>
	<table>
		<thead>
			<tr>
				<th>Tafel</th>
				<th>Datum</th>
				<th>Tijd</th>
				<th>Klant</th>
				<th>Aantal</th>
				<th>Status</th>
				<th>Datum Toegevoegd</th>
				<th>Allergieen</th>
				<th>Opmerkingen</th>
			</tr>
		</thead>
		<tbody>
			<!-- Toont huidige datum aan -->
			<h1>Reserveringen van vandaag: <?php echo $current_date ?></h1>

			<?php foreach ($row_data AS $data) { ?>
				<form method="post">
					<tr>
						<input type="hidden" name="reservering_id" value="<?php echo $data["ID"] ?>">

						<td><input type="number" name="tafel" value="<?php echo $data["Tafel"] ?>"></td>

						<td><input type="date" name="datum" value="<?php echo $data["Datum"] ?>"></td>

						<td><input type="time" name="tijd" value="<?php echo $data["Tijd"] ?>"></td>

						<input type="hidden" name="klant_id" value="<?php echo $data["Klant_ID"] ?>">

						<td><input type="text" name="naam" value="<?php echo $data["Naam"] ?>"></td>

						<input type="hidden" name="telefoon" value="<?php echo $data["Telefoon"] ?>">

						<input type="hidden" name="bday" value="<?php echo $data["Birthday"] ?>">

						<input type="hidden" name="email" value="<?php echo $data["Email"] ?>">

						<td><input type="number" name="aantal" value="<?php echo $data["Aantal"] ?>"></td>

						<td><input type="number" name="status" value="<?php echo $data["Status"] ?>"></td>

						<td><input type="datetime" name="datum_toegevoegd" value="<?php echo $data["Datum_toegevoegd"] ?>"></td>

						<td><input type="text" name="allergieen" value="<?php echo $data["Allergieen"] ?>"></td>

						<td><input type="text" name="opmerkingen" value="<?php echo $data["Opmerkingen"] ?>"></td>

						<td><button type="sumbit" name="edit" onclick="return confirm('Doorgaan met editen?');">Edit</button></td>
						<td><button type="sumbit" name="no_show" onclick="return confirm('Is deze klant niet gekomen?');">No Show</button></td>
						<td><button type="sumbit" name="delete" onclick="return confirm('Doorgaan met verwijderen?');">Delete</button></td>

						<?php if ($data['Birthday'] == $current_date) {
							echo "<td>Deze klant is jarig!</td>";
						} ?>
					</tr>
				</form>
			<?php } ?>	
		</tbody>
	</table>
<?php }else{
	echo "<h1>Er zijn momenteel GEEN reserveringen vandaag</h1>";
} ?>

	<!-- Form voor het toevoegen van de reserveringen -->
	<form method="post">

		<h1>Reservering Toevoegen</h1>

		<h3>Naam</h3>
		<input required type="text" name="naam">

		<h3>Telefoonnummer</h3>
		<input required type="number" name="telefoon">

		<h3>Email</h3>
		<input required type="email" name="email">

		<h3>Geboortedatum</h3>
		<input type="date" name="bday">

		<h3>Tafel</h3>
		<input required type="number" name="tafel">

		<h3>Datum</h3>
		<input required type="date" name="datum">

		<h3>Tijd</h3>
		<input required type="time" name="tijd">

		<h3>Aantal</h3>
		<input required type="number" name="aantal">

		<h3>Allergieen</h3>
		<input type="text" name="allergieen">

		<h3>Opmerkingen</h3>
		<input type="text" name="opmerkingen"><br><br>

		<button type="sumbit" name="add" onclick="return confirm('Deze reservering plaatsen?');">Toevoegen</button>
	</form>
</body>
</html>