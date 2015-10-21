<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../../";

if (empty($_REQUEST) || empty($_REQUEST['medlemsid'])) {
	die(json_response(HttpStatus::ERROR, "Må sende inn medlemsid som parameter til Flow", 412));
}

include_once $root."db_config.php";
include_once $root.'funksjoner.php';

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die(json_response(HttpStatus::ERROR, "Ingen tilgang til databasen", 500));
}

require_once $root.'vendor/autoload.php';

if(!er_logget_inn()) {
	die(json_response(HttpStatus::ERROR, "Du må være logget inn", 401));
}

$innlogget_id = innlogget_bruker()['medlemsid'];
$medlemsid = intval($_REQUEST['medlemsid']);

if (!tilgang_full()) {
	$medlemsid = $innlogget_id;
}

$request = new \Flow\Request();


if (empty($request->getFileName())) {
	logg("error-profilbilde", "Feilet under opplasting av profilbilde for brukerId: ".$medlemsid. ". Ingen filnavn");
	die(json_response(HttpStatus::ERROR, "En ukjent feil oppstod under opplastingen", 500));
}

$dir = "/bilder/medlemsfoto/";
$filename = $medlemsid.".jpg";

$filepath = "..".$dir.$filename;

if (\Flow\Basic::save( $root . $dir . $filename, '../../temp', $request)) {
	$sql = "UPDATE medlemmer SET foto = '".addslashes($filepath)."' WHERE medlemsid = ".$medlemsid." LIMIT 1";
	if(!mysql_query($sql)) {
		logg("error-profilbilde", "Feilet under opplasting av profilbilde for brukerId: ".$medlemsid. " sql: ".$sql);
		die(json_response(HttpStatus::ERROR, "Ett problem oppstod under opplastingen. Webkom er varslet! 2", 500));
	}
	innlogget_bruker_oppdatert();
	die(json_response(HttpStatus::SUCCESS, "Fil opplastet", 200));
} else {
	logg("error-profilbilde", "Feilet under opplasting av profilbilde for brukerId: ".$medlemsid. " filnavn: ".$root . $dir . $filename);
	//die(json_response(HttpStatus::ERROR, "Ett problem oppstod under opplastingen. Webkom er varslet!", 500));
}
