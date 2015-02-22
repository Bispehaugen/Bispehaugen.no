<?php

$er_produksjon = false;

if(file_exists("lokal_config.php")){
	include("lokal_config.php");
}

if(file_exists("../lokal_config.php")){
	include("../lokal_config.php");
}

/*

Slik ser lokal_config.php ut
Lagrer alle passord i lokal fil på alle pcer, slik at man kan endre for seg selv + github repo er åpent for hvem som helst til å lese
<?php
	$database_host="localhost";
	$database_user="solfrih_bukdb";
	$database_string="passord her";
	$database_database = "solfrih_bukdb";

	DEFINE("GMAIL_USERNAME", "");
	DEFINE("GMAIL_PASSWORD", "");
*/
