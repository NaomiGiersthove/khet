<?php 

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

	// Select statement om data te verzamelen uit de database
    $overzicht_kok = $db->select("SELECT bestellingen.ID AS bestelid, reserveringen.Tafel, bestellingen.Aantal, menuitems.Naam,bestellingen.Klaar, bestellingen.Gereserveerd FROM `bestellingen` INNER JOIN reserveringen ON bestellingen.Reservering_ID = reserveringen.ID INNER JOIN menuitems ON bestellingen.Menuitem_ID = menuitems.ID INNER JOIN gerechtsoorten ON menuitems.Gerechtsoort_ID = gerechtsoorten.ID INNER JOIN gerechtcategorieen ON gerechtsoorten.Gerechtcategorie_ID = gerechtcategorieen.ID WHERE gerechtcategorieen.ID IN (2 ,3, 4) AND bestellingen.Klaar = 0 ORDER BY reserveringen.Tijd",[]);

    if (!empty($overzicht_kok)) {
        $columns = array_keys($overzicht_kok[0]);
        $row_data = array_values($overzicht_kok);
    }

    // Als de kok op de button klikt, wordt het product verzonden naar de obers omgeving
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gereed'])) {

        $bestelid = htmlspecialchars(trim($_POST['bestelid']));
        $db->select("UPDATE `bestellingen` SET `Klaar` = '1' WHERE ID = :bestelid",[':bestelid' => $bestelid]);

        header("refresh:1;");
        echo '<script>alert("Product is doorgestuurd naar de ober!")</script>';
    }
?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Kok/Keuken</title>
</head>
<body>
	<?php include 'navigatie.php'; ?>

	<h1>Overzicht Bestelde Gerechten</h1>

	<?php if (!empty($overzicht_kok)) { ?>

	<table>
		<thead>
			<tr>
				<th>Tafel</th>
				<th>Aantal</th>
				<th>Gerecht</th>
				<th>Ready</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($row_data AS $data) { ?>
				<tr>
					<td><?php echo $data["Tafel"]?></td>
					<td><?php echo $data["Aantal"]?></td>
					<td><?php echo $data["Naam"]?></td>
					<?php if ($data["Gereserveerd"] == 0) { ?>
						<form method="post">
							<input type="hidden" name="bestelid" value="<?php echo $data['bestelid'] ?>">
							<td><button type="sumbit" name="gereed">Ja</button><td>
						</form>	
					<?php }?>
				</tr>
			<?php } ?>		
		</tbody>
	</table>
	<?php }else{
		echo "<h3>Er zijn nog geen bestellingen</h3>";
	} ?>
</body>
</html>