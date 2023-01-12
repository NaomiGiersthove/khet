<?php 

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

	// Select statement om data te verzamelen uit de database
    $nagerechten = $db->select(" SELECT menuitems.ID, menuitems.Code, menuitems.Naam, menuitems.Prijs ,gerechtsoorten.ID AS soort_ID ,gerechtcategorieen.Naam AS soort, gerechtsoorten.Naam AS soort_naam FROM menuitems 
    INNER JOIN gerechtsoorten ON menuitems.Gerechtsoort_ID = gerechtsoorten.ID
    INNER JOIN gerechtcategorieen ON gerechtsoorten.Gerechtcategorie_ID = gerechtcategorieen.ID
    WHERE Gerechtcategorie_ID = 4",[]);
    $columns = array_keys($nagerechten[0]);
    $row_data = array_values($nagerechten);

    $soorten = $db->select("SELECT * FROM gerechtsoorten WHERE Gerechtcategorie_ID = 4",[]);
    $row_data_soorten = array_values($soorten);

    // Function product editen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {

        $item_id = htmlspecialchars(trim($_POST['item_id']));
        $code = htmlspecialchars(trim($_POST['code']));
        $naam = htmlspecialchars(trim($_POST['naam']));
        $soort_id = htmlspecialchars(trim($_POST['soort_id']));
        $prijs = htmlspecialchars(trim($_POST['prijs']));

        $db->edit_items($item_id, $code, $naam, $soort_id, $prijs);
    }

    // Function product toevoegen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {

        $item_id = htmlspecialchars(trim($_POST['item_id']));
        $code = htmlspecialchars(trim($_POST['code']));
        $naam = htmlspecialchars(trim($_POST['naam']));
        $soort_id = htmlspecialchars(trim($_POST['soort_id']));
        $prijs = htmlspecialchars(trim($_POST['prijs']));

        $db->add_items($item_id, $code, $naam, $soort_id, $prijs);
    }

    // Function product verwijderen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {

        $item_id = htmlspecialchars(trim($_POST['item_id']));

        $db->select("DELETE FROM menuitems WHERE ID = :item_id",[':item_id' => $item_id]);
        header("refresh:1;");
        echo '<script>alert("Product is succesvol verwijderd!")</script>';
    }

?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Nagerechten</title>
</head>
<body>
	<?php include 'navigatie.php'; ?>
	<table>
		<thead>
			<tr>
				<th>Code</th>
				<th>Naam</th>
				<th>Prijs</th>
				<th>Soort</th>
				<th>Actie</th>
			</tr>
		</thead>
		<tbody><h1>Nagerechten Editen of Verwijderen</h1>
			<?php foreach ($row_data AS $data) { ?>
				<form method="post">
					<tr>
						<input type="hidden" name="item_id" value="<?php echo $data["ID"] ?>">

						<td><input type="text" maxlength="3" name="code" value="<?php echo $data["Code"] ?>"></td>
						<td><input type="text" name="naam" value="<?php echo $data["Naam"] ?>"></td>
						<td><input type="number" min="1" step=".01" name="prijs" value="<?php echo $data["Prijs"] ?>"></td>

						<input type="hidden" name="soort_id" value="<?php echo $data["soort_ID"] ?>">

						<td><input disabled type="text" name="gerechtsoort" value="<?php echo $data["soort_naam"] ?>"></td>
						<td><button type="sumbit" name="edit" onclick="return confirm('Doorgaan met editen');">Edit</button></td>
						<td><button type="sumbit" name="delete" onclick="return confirm('Doorgaan met verwijderen?');">Delete</button></td>
					</tr>
				</form>
			<?php } ?>	
		</tbody>
	</table>

	<form method="post">

		<h1>Nagerechten Toevoegen</h1>
	
		<input type="hidden" name="item_id">

		<h3>Subcategorie</h3>
	    <select name="soort_id">
	      <option></option>
	      <?php foreach($row_data_soorten AS $data_soorten) {?>
	        <option value="<?php echo $data_soorten["ID"]?>"><?php echo $data_soorten["Naam"] ?></option>
	      <?php } ?>
	    </select>

		<h3>Code</h3>
		<input type="text" maxlength="3" name="code">

		<h3>Naam</h3>
		<input type="text" name="naam">

		<h3>Prijs</h3>
		<input type="number" min="1" step=".01" name="prijs"><br><br>

		<button type="sumbit" name="add">Toevoegen</button>
	</form>
</body>
</html>