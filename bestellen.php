<?php 

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

	// Select statement om data te verzamelen uit de database
    $bestellen = $db->select("SELECT * FROM reserveringen",[]);

    $columns = array_keys($bestellen[0]);
    $row_data = array_values($bestellen);

    $menuitems = $db->select("SELECT * FROM menuitems",[]);
    $columns_menuitems = array_keys($menuitems[0]);
    $row_data_menuitems = array_values($menuitems);

    // Function bestelling plaatsen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bestellen'])) {

        $reservering_id = htmlspecialchars(trim($_POST['reservering_id']));
        $item_id = htmlspecialchars(trim($_POST['item_id']));
        $aantal = htmlspecialchars(trim($_POST['aantal']));

        $db->add_bestelling($reservering_id, $item_id, $aantal);
    }

?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Bestellen</title>
</head>
<body>
	<?php include 'navigatie.php'; ?>

	<h1>Bestelling Plaatsen</h1>

	<form method="post">
		<h3>Tafel</h3>
		<select required type="number" name="reservering_id">
        <option>-----</option>
			<?php foreach($row_data AS $data) { ?>
				<option value="<?php echo $data["ID"] ?>"><?php echo $data["Tafel"] ?></option>
			<?php } ?>
		</select>

		<h3>Kies Een Product</h3>
		<select required type="text" name="item_id">
        <option>-----</option>
			<?php foreach($row_data_menuitems AS $data_menuitems) { ?>
				<option value="<?php echo $data_menuitems['ID'] ?>"><?php echo $data_menuitems["Naam"] ?></option>
			<?php } ?>
		</select>

		<h3>Aantal</h3>
		<input required type="number" name="aantal"><br><br>

		<button type="submit" name="bestellen">Bestelling Plaatsen</button>

	</form>
</body>
</html>