<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../../";

if (empty($_REQUEST) || empty($_REQUEST['medlemsid'])) {
	die("Må sende inn medlemsid som parameter til Flow");
}

include_once $root."db_config.php";
include_once $root.'funksjoner.php';

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

require_once $root.'vendor/autoload.php';

if(!er_logget_inn()) {
	die("Du må være logget inn");
}

$innlogget_id = innlogget_bruker()['medlemsid'];
$medlemsid = intval($_REQUEST['medlemsid']);

if (!tilgang_full()) {
	$medlemsid = $innlogget_id;
}

$request = new \Flow\Request();


if (empty($request->getFileName())) {
	logg("error-profilbilde", "Feilet under opplasting av profilbilde for brukerId: ".$medlemsid. ". Ingen filnavn");
	die("Fant ingen filnavn");
}

$dir = "/bilder/medlemsfoto/";
$filename = $medlemsid.".jpg";

$filepath = "..".$dir.$filename;

if (\Flow\Basic::save( $root . $dir . $filename, '../../temp', $request)) {
	$sql = "UPDATE medlemmer SET foto = '".addslashes($filepath)."' WHERE medlemsid = ".$medlemsid." LIMIT 1";
	mysql_query($sql);
	innlogget_bruker_oppdatert();
} else {
	logg("error-profilbilde", "Feilet under opplasting av profilbilde for brukerId: ".$medlemsid. " filnavn: ".$filename);
	die("Ett problem oppstod under opplastingen. Webkom er varslet!");
}
