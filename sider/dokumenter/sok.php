<?php

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../../";

include_once $root."db_config.php";
include_once $root.'funksjoner.php';
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

if(!er_logget_inn()) {
	header('Location: ?side=ikke_funnet');
	die(json_response(HttpStatus::ERROR, "Ikke logget inn", 401));
}

header('Content-type: application/json');

$retur = Array();

$sokestreng = get('sok');

if (empty($sokestreng)) {
	die(json_response(HttpStatus::ERROR, "GET parameter sok mangler", 400));
}

$mappetype = has_get('type') ? get('type') : Mappetype::Noter;

$mapper = sok_mapper($sokestreng, $mappetype);
//$filer = sok_filer($sokestreng, $mappetype);

$dine_komiteer = hent_komiteer_for_bruker();

foreach($mapper as $mappe ) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		array_push($retur, Array("id" => $mappe['id'], "text" => $mappe['tittel']));
	}
}

/*
foreach($filer as $fil) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		array_push($retur, Array("id" => $fil['id'], "text" => $fil['tittel']));
	}
}
*/

echo json_encode($retur);
