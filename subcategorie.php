<?php

	// Database connectie en functies
    include 'database.php';

	// Database connectie om data op te halen uit de database
    $db = new database();

    // Select statement om data te verzamelen uit de database
    $categorien = $db->select("SELECT * FROM gerechtcategorien",[]);
    $columns = array_keys($categorien[0]);
    $row_data = array_values($categorien);

    // Function subcategorie verwijderen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {

        $soort_id = htmlspecialchars(trim($_POST['soort_id']));
        $db->select("DELETE FROM gerechtsoorten WHERE ID = :soort_id",[':soort_id' => $soort_id]);

        header("refresh:1;");
        echo '<script>alert("Subcategorie is succesvol verwijderd!")</script>';
    }

    // Function subcategorie toevoegen
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {

        $code = htmlspecialchars(trim($_POST['code']));
        $naam = htmlspecialchars(trim($_POST['naam']));
        $soort_id = htmlspecialchars(trim($_POST['categorie_id']));
    
        $db->add_subcategorie($code, $naam, $soort_id);
    }

?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Subcategorie</title>
</head>
<body>
    <?php include 'navigatie.php' ?>

    <h1>Subcategorie Verwijderen</h1>

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
          <form method="post">
            <input type="hidden" name="soort_id" value="<?php echo $data_soorten['ID']; ?>">
            <th><?php echo $data_soorten["Naam"] ?></th>
            <th><button type="sumbit" name="delete">Delete</button></th>
          </form>
          <tr>
          <?php foreach($row_data_menuitems AS $data_menuitems) {?>
            <tr><td><?php echo $data_menuitems["Naam"]." €<strong>".$data_menuitems['Prijs']."</strong>"?></td>
          <?php } ?>
          </tr>
        <?php } ?>
      </tr>
    </table>
  <?php } ?>

  <form method="post">
    <h1>Subcategorie Toevoegen</h1>

    <h3>Categorie</h3>
    <select required name="categorie_id">
        <option></option>
        <?php foreach($row_data AS $data) {?>
            <option value="<?php echo $data["ID"]?>"><?php echo $data["Naam"] ?></option>
        <?php } ?>
    </select>

    <h3>Code</h3>
    <input type="text" name="code">

    <h3>Naam</h3>
    <input type="text" name="naam"><br><br>

    <button type="submit" name="add">Toevoegen</button>
  </form>
</body>
</html>