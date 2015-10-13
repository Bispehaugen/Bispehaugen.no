<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../../../";

include_once $root."db_config.php";
include_once $root.'funksjoner.php';
include_once "funksjoner.php";


$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die(json_response(HttpStatus::ERROR, "Ingen databasetilkobling", 500));
}

if(!er_logget_inn()) {
	die(json_response(HttpStatus::ERROR, "Ikke logget inn", 403));
}
if(!tilgang_endre()) {
	die(json_response(HttpStatus::ERROR, "Ingen tilgang til å slette filer", 401));
}
if(!has_post('endredeBrukereErIGruppe')) {
	die(json_response(HttpStatus::ERROR, "Request inneholdt ikke endredeBrukereErIGruppe array", 400));
}

$endredeBrukereErIGruppe = post('endredeBrukereErIGruppe');


foreach($endredeBrukereErIGruppe as $brukerId => $gruppeId) {
	$sql = "INSERT INTO slagverkhjelp (gruppeid, medlemsid) 
			VALUES ($gruppeId, $brukerId)
			ON DUPLICATE KEY UPDATE gruppeid=$gruppeId";
	if(!mysql_query($sql)) {
		logg("update-slagverkhjelpere", mysql_error());
		die(json_response(HttpStatus::ERROR, "Ukjent lagringsproblem for medlemsid: ".$brukerId, 500));
	}
}

die(json_response(HttpStatus::SUCCESS, "Lagret", 200));
