
DROP DATABASE IF EXISTS excellenttaste;

CREATE DATABASE excellenttaste;

USE excellenttaste;

CREATE TABLE gerechtcategorieen (
	ID INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
	Code VARCHAR(3) UNIQUE,
	Naam VARCHAR(20),
	PRIMARY KEY (ID)
);

CREATE TABLE klanten (
	ID INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
	Naam VARCHAR(20) NOT NULL,
	Telefoon VARCHAR(11) NOT NULL, 
	Email VARCHAR(128) NOT NULL,
	Birthday DATE NOT NULL,
	PRIMARY KEY (ID)
);

CREATE TABLE gerechtsoorten (
	ID INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
	Code VARCHAR(3) UNIQUE,
	Naam VARCHAR(20),
	Gerechtcategorie_ID INT(11) NOT NULL,
	PRIMARY KEY (ID),
	FOREIGN KEY (Gerechtcategorie_ID) REFERENCES gerechtcategorieen(ID)
);

CREATE TABLE menuitems (
	ID INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
	Code VARCHAR(4) UNIQUE,
	Naam VARCHAR(30),
	Gerechtsoort_ID INT(11) NOT NULL,
	Prijs DECIMAL(5,2) NOT NULL,
	FOREIGN KEY (Gerechtsoort_ID) REFERENCES gerechtsoorten(ID)
);

CREATE TABLE reserveringen (
	ID INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
	Tafel INT(11) NOT NULL,
	Datum DATE NOT NULL,
	Tijd TIME NOT NULL,
	Klant_ID INT(11) NOT NULL,
	Aantal INT(11) NOT NULL,
	Status TINYINT(4) NOT NULL DEFAULT '1', 
	Datum_toegevoegd TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL ,
	Allergieen TEXT,
	Opmerkingen TEXT,
	PRIMARY KEY (ID),
	FOREIGN KEY (Klant_ID) REFERENCES klanten(ID)
);

CREATE TABLE bestellingen (
	ID INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
	Reservering_ID INT(11) NOT NULL,
	Menuitem_ID INT(11) NOT NULL,
	Aantal INT(11),
	Klaar TINYINT(4) DEFAULT '0',
	Gereserveerd TINYINT(4) DEFAULT '0',
	PRIMARY KEY (ID),
	FOREIGN KEY (Reservering_ID) REFERENCES reserveringen(ID),
	FOREIGN KEY (Menuitem_ID) REFERENCES menuitems(ID)
);


INSERT INTO `gerechtcategorieen` (`ID`, `Code`, `Naam`) VALUES 
	(NULL, 'DK', 'Dranken'), 
	(NULL, 'HP', 'Hapjes'), 
	(NULL, 'HG', 'Hoofdgerechten'), 
	(NULL, 'NG', 'Nagerechten')
;

INSERT INTO `gerechtsoorten` (`ID`, `Code`, `Naam`, `Gerechtcategorie_ID`) VALUES 
	(NULL, 'WAT', 'Water', '1'), 
	(NULL, 'WM', 'Warme hapjes', '2'), 
	(NULL, 'VL', 'Vlees', '3'), 
	(NULL, 'IJS', 'IJs', '4')
;

INSERT INTO `menuitems` (`ID`, `Code`, `Naam`, `Gerechtsoort_ID`, `Prijs`) VALUES 
    (NULL, 'CFR', 'Chaudfontaine rood', 1, 2.75),
    (NULL, 'KLP', 'Kip Loempia', 2, 3.50),
    (NULL, 'BSC', 'Biefstuk in champignonsaus', 3, 11.95),
    (NULL, 'VYS', 'Vruchtenijs', 4, 5.50);
    ;

INSERT INTO `klanten` (`ID`, `Naam`, `Telefoon`, `Email`, `Birthday`) VALUES 
    (NULL, 'Jeroen Krabbe', '0699998811', 'jeroenkrabbe@hotmail.com', CURRENT_DATE)
;

INSERT INTO `reserveringen` (`ID`, `Tafel`, `Datum`, `Tijd`, `Klant_ID`, `Aantal`, `Status`, `Datum_toegevoegd`, `Allergieen`, `Opmerkingen`) VALUES 
	(NULL, '6', CURRENT_DATE, '12:00:00', '1', '4', '1', current_timestamp(), NULL, NULL)
;

INSERT INTO `bestellingen` (`ID`, `Reservering_ID`, `Menuitem_ID`, `Aantal`, `Klaar`,`Gereserveerd`) VALUES 
	(NULL, '1', '2', '2', '0','0')
;
