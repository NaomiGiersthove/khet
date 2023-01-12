<?php 

class database{
    
    private $db_server;
    private $db_username;
    private $db_password;
    private $db_name;
    private $db;

    // Function connecten met database
    function __construct(){

        $this->db_server = 'localhost';
        $this->db_username = 'root';
        $this->db_password = '';
        $this->db_name = 'excellenttaste';

        $dsn = "mysql:host=$this->db_server;dbname=$this->db_name;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
             $this->db = new PDO($dsn, $this->db_username, $this->db_password, $options);
        } catch (\PDOException $e) {
             throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    // Function voor het opzoeken van data
	public function select($statement, $named_placeholder){

        // prepared statement (Stuurt statement naar de server + checks syntax)
        $statement = $this->db->prepare($statement);

        $statement->execute($named_placeholder);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    // Function reserveringen toevoegen
    public function add_reservering($naam, $telefoon, $email, $bday, $tafel, $datum, $tijd, $aantal, $allergieen, $opmerkingen) {
    	try{

            $this->db->beginTransaction();

            $klanten_check = "SELECT * FROM klanten WHERE Naam = :naam AND Telefoon = :telefoon";
            $klanten_statement = $this->db->prepare($klanten_check);
            $klanten_statement->execute(['naam' => $naam, 'telefoon' => $telefoon]);
            $result = $klanten_statement->fetch();

            // Als de klant al eerder een reservering heeft gemaakt maar kwam niet opdagen
            if (is_array($result) && count($result) > 0) { 
                echo '<script>alert("Deze persoon heeft al eerder gereserveerd maar kwam niet opdagen!")</script>';
            }

    		$sql = "INSERT INTO klanten VALUES (NULL, :naam, :telefoon, :email, :bday)";

            // Zet data in de variables
    		$statement = $this->db->prepare($sql);
    		$statement->execute([
    		'naam' => $naam,
    		'telefoon' => $telefoon,
    		'email' => $email,
            'bday' => $bday
    		]);

            // Pakt de laatst ingevoerde ID uit de database
    		$klant_id = $this->db->lastInsertId();

    		if ($this->db->commit()) {

    			$sql = "INSERT INTO reserveringen VALUES (NULL, :tafel, :datum, :tijd, :klant_id, :aantal, :status, :datum_toegevoegd, :allergieen, :opmerkingen)";
    			$statement = $this->db->prepare($sql);
	    		$statement->execute([
	    		'tafel' => $tafel,
	    		'datum' => $datum,
	    		'tijd' => $tijd,
	    		'klant_id' => $klant_id,
	    		'aantal' => $aantal,
	    		'status' => 1,
	    		'datum_toegevoegd' => date('Y-m-d H:i:s'),
	    		'allergieen' => $allergieen,
	    		'opmerkingen' => $opmerkingen,
	    		]);

    			echo '<script>alert("Reservering is succesvol toegevoegd!")</script>';
    			header("refresh:1;");
    		}   

    	}catch (Exception $e){

    		$this->db->rollback();
    		echo $e->getMessage();

    	}
    }

    // Function reserveringen editen
    public function edit_reserveringen($reservering_id, $klant_id, $naam, $telefoon, $email, $bday, $tafel, $datum, $tijd, $aantal, $status, $datum_toegevoegd, $allergieen, $opmerkingen) {
        try{
            // Zoekt data op uit de database
            $sql = "UPDATE reserveringen INNER JOIN klanten ON reserveringen.Klant_ID = klanten.ID SET reserveringen.Tafel = :tafel, reserveringen.Datum = :datum, reserveringen.Tijd = :tijd, reserveringen.Aantal = :aantal, reserveringen.Status = :status, reserveringen.Datum_toegevoegd = :datum_toegevoegd, reserveringen.Allergieen = :allergieen, reserveringen.opmerkingen = :opmerkingen, klanten.Naam = :naam, klanten.Telefoon = :telefoon, klanten.Email = :email, klanten.Birthday = :bday WHERE reserveringen.ID = :reservering_id";

            $this->db->beginTransaction();

            //Zet data in de varibelen
            $statement = $this->db->prepare($sql);
            $statement->execute([
            'tafel' => $tafel,
            'datum' => $datum,
            'tijd' => $tijd,
            'aantal' => $aantal,
            'status' => $status,
            'datum_toegevoegd' => $datum_toegevoegd,
            'allergieen' => $allergieen,
            'opmerkingen' => $opmerkingen,
            'naam' => $naam,
            'telefoon' => $telefoon, 
            'email' => $email,
            'bday' => $bday,
            'reservering_id' => $reservering_id
            ]);

            if ($this->db->commit()) {
                echo '<script>alert("Reservering is succesvol gewijzigd!")</script>';
                header("refresh:1;");
            }
        }catch (Exception $e){
            $this->db->rollback();
            throw $e;
        }
    }

    // Function reserveringen verwijderen
    // zie reserveringen.php

    // Function subcategorie toevoegen
    public function add_subcategorie($code, $naam, $soort_id) {
        try{
            $sql = "INSERT INTO gerechtsoorten VALUES (NULL, :code, :naam, :soort_id)";

            $this->db->beginTransaction();

            //Zet data in de varibelen
            $statement = $this->db->prepare($sql);
            $statement->execute([
            'code' => $code,
            'naam' => $naam,
            'soort_id' => $soort_id
            ]);
            
            if ($this->db->commit()) {
                echo '<script>alert("Subcategorie is succesvol toegevoegd!")</script>';
                header("refresh:1;");
            }
        }catch (Exception $e){
            $this->db->rollback();
            throw $e;
        }
    }

    // Function subcategorie verwijderen
    // zie subcategorie.php

    // Function bestelling plaatsen
    public function add_bestelling($reservering_id, $item_id, $aantal) {
        try{
            $sql = "INSERT INTO bestellingen VALUES (NULL, :reservering_id, :item_id, :aantal,:klaar, :gereserveerd)";

            $this->db->beginTransaction();

            //Zet data in de varibelen
            $statement = $this->db->prepare($sql);
            $statement->execute([
            'reservering_id' => $reservering_id,
            'item_id' => $item_id,
            'aantal' => $aantal,
            'klaar' => 0,
            'gereserveerd' => 0
            ]);

            if ($this->db->commit()) {
                echo '<script>alert("Bestelling is succesvol geplaatst!")</script>';
                header("refresh:1;");
            }
        }catch (Exception $e){
            $this->db->rollback();
            throw $e;
        }
    }

    // Function bon afdrukken
    // zie bon.php

    // Function product toevoegen
    public function add_items($item_id, $code, $naam, $soort_id, $prijs) {
    	try{
            // Zoekt data op uit de database
    		$sql = "INSERT INTO menuitems VALUES (NULL, :code, :naam, :soort_ID, :prijs)";

    		$this->db->beginTransaction();

            //Zet data in de varibelen
    		$statement = $this->db->prepare($sql);
    		$statement->execute([
    		'code' => $code,
    		'naam' => $naam,
    		'soort_ID' => $soort_id,
    		'prijs' => $prijs
    		]);

            // Als de code is uitgevoerd word de message uitgevoerd
    		if ($this->db->commit()) {
    			echo '<script>alert("Product is succesvol toegevoegd!")</script>';
    			header("refresh:1;");
    		}
    	}catch (Exception $e){
    		$this->db->rollback();
    		throw $e;
    	}
    }

    // Function product editen
    public function edit_items($item_id, $code, $naam, $soort_id, $prijs) {
    	try{
            // Zoekt data op uit de database
    		$sql = "UPDATE menuitems SET code = :code, naam = :naam, prijs = :prijs WHERE ID = :item_id";

    		$this->db->beginTransaction();

            //Zet data in de varibelen
    		$statement = $this->db->prepare($sql);
    		$statement->execute([
    		'code' => $code,
    		'naam' => $naam, 
    		'prijs' => $prijs,
    		'item_id' => $item_id
    		]);

            // Als de code is uitgevoerd word de message uitgevoerd
    		if ($this->db->commit()) {
    			echo '<script>alert("Product is succesvol gewijzigd!")</script>';
    			header("refresh:1;");
    		}
    	}catch (Exception $e){
    		$this->db->rollback();
    		throw $e;
    	}
    }

    // Function porduct verwijderen
    // zie drinken.php, hapjes.php, hoofdgerechten.php en nagerechten.php

    // Function klantgegevens editen
    public function edit_klanten($klant_id,$naam,  $telefoon, $email, $bday) {
    	try{
            // Zoekt data op uit de database
    		$sql = "UPDATE klanten SET Naam = :naam, Telefoon = :telefoon, Email = :email, Birthday = :bday WHERE ID = :klant_id";

    		$this->db->beginTransaction();

            //Zet data in de varibelen
    		$statement = $this->db->prepare($sql);
    		$statement->execute([
    		'naam' => $naam,
    		'telefoon' => $telefoon, 
    		'email' => $email,
            'bday' => $bday,
    		'klant_id' => $klant_id
    		]);

    		if ($this->db->commit()) {
    			echo '<script>alert("Klantgegevens zijn succesvol gewijzigd!")</script>';
    			header("refresh:1;");
    		}
    	}catch (Exception $e){
    		$this->db->rollback();
    		throw $e;
    	}
    }

    // Function klantgegevens verwijderen
    // zie klanten.php

}

?>