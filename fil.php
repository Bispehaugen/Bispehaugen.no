<?php

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "./";

include_once $root."db_config.php";
include_once $root.'funksjoner.php';

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

if(!er_logget_inn() || !has_get("fil")) {
	header('Location: index.php?side=ikke_funnet');
	die();
}

$filid = intval(get("fil"));

if (!is_int($filid)) {
	header('Location: index.php?side=ikke_funnet');
	die("Fil id må sendes inn via GET parameteret fil");
}

$mysql = "SELECT m.mappenavn, f.filnavn, f.filtype FROM filer AS f JOIN mapper AS m ON f.mappeid = m.id WHERE f.id = ".$filid;
$result = mysql_query($mysql);

while ($file = mysql_fetch_assoc($result)) {
	$dir = "dokumenter/".$file['mappenavn']."/";

	$filename = $file['filnavn'];
	$filepath = $dir.$filename;

	if (file_exists($filepath)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.$filename.'"');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($filepath));
	    readfile($filepath);
	    exit;
	} else {
		die("Fant ikke filen ".$filename);
	}
}

