<?php
if(file_exists("lokal_config.php")){
	include("lokal_config.php");
}

if(file_exists("../lokal_config.php")){
	include("../lokal_config.php");
}

/*

Slik ser lokal_config.php ut: 
<?php
	$database_host="localhost";
	$database_user="solfrih_bukdb";
	$database_string="passord her";
	$database_database = "solfrih_bukdb";

*/