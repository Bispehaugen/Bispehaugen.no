<?php

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../../";


include_once $root."db_config.php";
include_once $root.'funksjoner.php';

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

abstract class Status {
	const SUCCESS = "success";
	const ERROR = "error";
}

function response($status, $message, $errorStatusCode = 500) {
	if ($status == Status::ERROR) {
		http_response_code($errorStatusCode);
	}
	echo json_encode(Array("status" => $status, "message" => $message));
}


// Skriving starter

header('Content-type: application/json');

if(!tilgang_endre()) {
	die(response(Status::ERROR, "Ingen tilgang til å slette filer", 401));
}

if (has_get('mappe')) {
	// Sletter mappe
	$mappeid = get('mappe');

	$sql_antall_filer_og_undermapper_i_mappe = "SELECT SUM(antall) AS antall FROM
(
(SELECT COUNT(f.id) AS antall FROM filer AS f WHERE f.mappeid = $mappeid)
UNION
(SELECT COUNT(m.id) AS antall FROM mapper AS m WHERE m.foreldreid = $mappeid)
) AS antall";

	$antall_filer_og_undermapper_i_mappe = mysql_result(mysql_query($sql_antall_filer_og_undermapper_i_mappe), 0);

	if($antall_filer_og_undermapper_i_mappe == 0){
		$sql = "DELETE FROM mapper WHERE id = " . $mappeid . " LIMIT 1";

		if(mysql_query($sql)) {
			die(response(Status::SUCCESS, "Mappe $mappeid slettet"));
		} else {
			die(response(Status::ERROR, "Feil under sletting av mappe. Prøv på nytt"));
		}
	} else {
		die(response(Status::ERROR, "Du kan bare slette tomme mapper", 403));
	}

} else if (has_get('fil')) {
	// Sletter fil
	$filid = get('fil');

	$sql = "DELETE FROM filer WHERE id = " . $filid . " LIMIT 1";

	if(mysql_query($sql)) {
		die(response(Status::SUCCESS, "Fil $filid slettet"));
	} else {
		die(response(Status::ERROR, "Feil under sletting av fil. Prøv på nytt"));
	}

} else {
	// Finner ikke id
	die(response(Status::ERROR, "Id ikke sendt med. Må enten sende med mappe='id' eller fil='id'", 403));
}