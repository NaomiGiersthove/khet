<?php 

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

    // Select statement om data te verzamelen uit de database
    $categorieen = $db->select("SELECT * FROM gerechtcategorieen",[]);

    $columns = array_keys($categorieen[0]);
    $row_data = array_values($categorieen);

?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Menukaart</title>
</head>
<body>
	<?php include 'navigatie.php' ?>

	<h1>Menu</h1>

	<?php foreach ($row_data AS $data) { ?>

		<?php 
		$categorie_id = $data['ID'];

		$soorten = $db->select("SELECT * FROM gerechtsoorten WHERE Gerechtcategorie_ID = :categorie_id",[':categorie_id' => $categorie_id]);
		$row_data_soorten = array_values($soorten);
		
		?>

		<h3><?php echo $data["Naam"]?></h3>

		<table style="text-align: left;">
			<tr>
				<?php foreach($row_data_soorten AS $data_soorten) {?>
					<?php 
					$soorten_id = $data_soorten['ID'];

					$menuitems = $db->select("SELECT * FROM menuitems WHERE Gerechtsoort_ID = :soorten_id",[':soorten_id' => $soorten_id]);
					$row_data_menuitems = array_values($menuitems);
					?>

					<th><?php echo $data_soorten["Naam"] ?></th>
					<tr>
						<?php foreach($row_data_menuitems AS $data_menuitems) {?>
							<tr><td><?php echo $data_menuitems["Naam"]." €<strong>".$data_menuitems['Prijs']."</strong>"?></td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tr>			
		</table>
	<?php } ?>
</body>
</html>