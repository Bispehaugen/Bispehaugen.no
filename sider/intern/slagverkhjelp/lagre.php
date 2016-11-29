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
	if ($gruppeId == 0) {
		// Slett brukerplassering
		$sql = "DELETE FROM slagverkhjelp WHERE medlemsid = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($brukerId));
	} else {
		$sql = "INSERT INTO slagverkhjelp (gruppeid, medlemsid) 
			VALUES (?, ?)
			ON DUPLICATE KEY UPDATE gruppeid=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($gruppeId, $brukerId, $gruppeId));
	}
}

foreach($endredeBrukerSomErLeder as $gruppeId => $brukerId) {
	$sql_fjern_gammel_leder = "UPDATE slagverkhjelp SET gruppeleder = 0 WHERE gruppeid = ? AND gruppeleder = 1";
    $stmt = $dbh->prepare($sql_fjern_gammel_leder);
    $stmt->execute(array($gruppeId));

	$sql_update_ny_leder = "UPDATE slagverkhjelp SET gruppeleder = 1 WHERE medlemsid = ?";
    $stmt = $dbh->prepare($sql_update_ny_leder);
    $stmt->execute(array($brukerId));
}

die(json_response(HttpStatus::SUCCESS, "Lagret", 200));
