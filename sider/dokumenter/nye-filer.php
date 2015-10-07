<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../../";

if (empty($_REQUEST) || empty($_REQUEST['foreldreId'])) {
	die("Må sende inn foreldreId og mappetype som parameter til Flow");
}

include_once $root."db_config.php";
include_once $root.'funksjoner.php';
include_once 'funksjoner.php'; // For dokumenter

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

require_once $root.'vendor/autoload.php';

if(!er_logget_inn()) {
	die("Du må være logget inn");
}

$medlemsid = innlogget_bruker()['medlemsid'];
$mappeid = intval($_REQUEST['foreldreId']);

if (!tilgang_endre()) {
	die("Du har ikke lov til å laste opp filer!");
}

$request = new \Flow\Request();

if (empty($request->getFileName())) {
	die("Fant ingen filnavn");
}
$mappe = hent_mappe($mappeid);

$tittel = $request->getFileName();
$filnavn = fornorske($tittel);
$filtype = finn_filtype($tittel);
$mappetype = $mappe['mappetype'];
$mappetype_path = strtolower(hent_mappetype_navn($mappetype));

$filepath = $root.$mappetype_path."/".$mappe['mappenavn']."/".$filnavn;

$temp = $root."temp";

if (\Flow\Basic::save( $filepath, $temp, $request)) {
	$sql = "INSERT INTO filer (filnavn, tittel, filtype, medlemsid, mappeid, mappetype) VALUES ('$filnavn', '$tittel', '$filtype', $medlemsid, $mappeid, $mappetype)";
	mysql_query($sql) or die(mysql_error());
	$fil_id = mysql_insert_id();

	$filnavn_med_id = $fil_id."-".$filnavn;
	$filepath_med_id = $root.$mappetype_path."/".$mappe['mappenavn']."/".$filnavn_med_id;
	rename($filepath, $filepath_med_id);

	$sql_updated = "UPDATE filer SET filnavn = '".$filnavn_med_id."' WHERE id = ".$fil_id." LIMIT 1";
	mysql_query($sql_updated) or die(mysql_error());
} else {
	logg("error-filopplasting", "Feilet under opplasting av filen ".$filnavn." for mappeId: ".$mappeid. ". Bruker: ".$medlemsid);
	die("Ett problem oppstod under opplastingen. Webkom er varslet!");
}
