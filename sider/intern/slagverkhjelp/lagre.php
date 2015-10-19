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

$endredeBrukereErIGruppe = post('endredeBrukereErIGruppe');
$endredeBrukerSomErLeder = post('endredeBrukerSomErLeder');

foreach($endredeBrukereErIGruppe as $brukerId => $gruppeId) {
	$gruppeId = mysql_real_escape_string($gruppeId);
	$brukerId = mysql_real_escape_string($brukerId);

	if ($gruppeId == 0) {
		// Slett brukerplassering
		$sql = "DELETE FROM slagverkhjelp WHERE medlemsid = $brukerId";
		if(!mysql_query($sql)) {
			logg("delete-slagverkhjelpere", $sql." | ".mysql_error());
			die(json_response(HttpStatus::ERROR, "Ukjent lagringsproblem for medlemsid: ".$brukerId, 500));
		}
	} else {
		$sql = "INSERT INTO slagverkhjelp (gruppeid, medlemsid) 
			VALUES ($gruppeId, $brukerId)
			ON DUPLICATE KEY UPDATE gruppeid=$gruppeId";
		if(!mysql_query($sql)) {
			logg("update-slagverkhjelpere", $sql." | ".mysql_error());
			die(json_response(HttpStatus::ERROR, "Ukjent lagringsproblem for medlemsid: ".$brukerId, 500));
		}
	}
}

foreach($endredeBrukerSomErLeder as $gruppeId => $brukerId) {
	$gruppeId = mysql_real_escape_string($gruppeId);
	$brukerId = mysql_real_escape_string($brukerId);

	$sql_fjern_gammel_leder = "UPDATE slagverkhjelp SET gruppeleder = 0 WHERE gruppeid = ".$gruppeId. " AND gruppeleder = 1";
	if(!mysql_query($sql_fjern_gammel_leder)) {
		logg("update-slagverkhjelpere", $sql_fjern_gammel_leder." | ".mysql_error());
		die(json_response(HttpStatus::ERROR, "Ukjent lagringsproblem for fjern gammel leder medlemsid: ".$brukerId, 500));
	}
	$sql_update_ny_leder = "UPDATE slagverkhjelp SET gruppeleder = 1 WHERE medlemsid = ".$brukerId;

	if(!mysql_query($sql_update_ny_leder)) {
		logg("update-slagverkhjelpere", $sql_update_ny_leder." | ".mysql_error());
		die(json_response(HttpStatus::ERROR, "Ukjent lagringsproblem for oppdater ny leder medlemsid: ".$brukerId, 500));
	}
}

die(json_response(HttpStatus::SUCCESS, "Lagret", 200));
