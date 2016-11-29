<?php

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

global $dbh;

$root = "../../";


include_once $root."db_config.php";
include_once $root.'funksjoner.php';
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

// Skriving starter

if(!tilgang_endre()) {
	die(json_response(HttpStatus::ERROR, "Ingen tilgang til å slette filer", 401));
}

if (has_get('mappe')) {
	// Sletter mappe
	$mappeid = get('mappe');
	$mappe = hent_mappe($mappeid);

	$sql_antall_filer_og_undermapper_i_mappe = "SELECT SUM(antall) AS antall FROM
(
(SELECT COUNT(f.id) AS antall FROM filer AS f WHERE f.mappeid = :mappeid)
UNION
(SELECT COUNT(m.id) AS antall FROM mapper AS m WHERE m.foreldreid = :mappeid)
) AS antall";

    $stmt = $dbh->prepare($sql_antall_filer_og_undermapper_i_mappe);
    $stmt->execute(array(":mappeid" => $mappeid));

	$antall_filer_og_undermapper_i_mappe = $stmt->fetchColumn();

	if($antall_filer_og_undermapper_i_mappe == 0){
		$sql = "DELETE FROM mapper WHERE id = ? LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($mappeid));

        $path = $root . "dokumenter/" . $mappe['mappenavn'] . "/";
        if(is_dir($path)) {
            rmdir($path);
        }
        die(json_response(HttpStatus::SUCCESS, "Mappe $mappeid slettet"));
	} else {
		die(json_response(HttpStatus::ERROR, "Du kan bare slette tomme mapper", 403));
	}

} else if (has_get('fil')) {
	// Sletter fil
	$filid = get('fil');

	$fil = hent_fil($filid);
	$mappe = hent_mappe($fil['mappeid']);

	$sql = "DELETE FROM filer WHERE id = ? LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($filid));

    $path = $root . "dokumenter/" . $mappe['mappenavn'] . "/" . $fil['filnavn'];
    if(file_exists($path)) {
        unlink($path);
    }
    die(json_response(HttpStatus::SUCCESS, "Fil $filid slettet"));

} else {
	// Finner ikke id
	die(json_response(HttpStatus::ERROR, "Id ikke sendt med. Må enten sende med mappe='id' eller fil='id'", 403));
}
