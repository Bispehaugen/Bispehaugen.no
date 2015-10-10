<?php

$er_produksjon = false;


function inkluder_lokal_config($lokal_config_plassering = "lokal_config.php") {
	for($dypde = 0; $dypde < 5; $dypde++) {
		if(file_exists($lokal_config_plassering)){
			include($lokal_config_plassering);
			break;
		} else {
			$lokal_config_plassering = "../".$lokal_config_plassering;
		}
	}
}

inkluder_lokal_config();
/*

Slik ser lokal_config.php ut
Lagrer alle passord i lokal fil på alle pcer, slik at man kan endre for seg selv + github repo er åpent for hvem som helst til å lese
<?php
	$database_host="localhost";
	$database_user="solfrih_bukdb";
	$database_string="passord her";
	$database_database = "solfrih_bukdb";

	DEFINE("MAIL_USERNAME", "");
	DEFINE("MAIL_PASSWORD", "");
*/
