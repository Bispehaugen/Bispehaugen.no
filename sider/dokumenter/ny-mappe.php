<?php

include_once("sider/dokumenter/funksjoner.php");

if(!has_post('navn') || !has_post('foreldreid')) {
	die("Kan ikke opprette ny mappe uten innsendt navn eller foreldreid");
}

if(!er_logget_inn()) {
	die("Du må være logget inn");
}
if (!tilgang_endre()) {
	die("Du har ikke lov til å laste opp filer!");
}

$navn = post('navn');
$foreldreid = post('foreldreid');

// Opprett ny mappe i sql
$sql = "INSERT INTO mapper (mappenavn, tittel, mappetype, foreldreid) VALUES ('Kommer', '$navn', 1, $foreldreid)";
mysql_query($sql) or die(mysql_error());

// hent ID
$ny_id = mysql_insert_id();

// Opprett ny mappe i filstruktur
$mappenavn = $ny_id . "-" . $navn;
$path = "dokumenter/".$mappenavn;

if(!is_dir($path)) {
	mkdir($path, 0751);
}

// Oppdatert mappe med riktig filstruktur
$update_sql = "UPDATE mapper SET mappenavn = '".$mappenavn."' WHERE id = '".$ny_id."' LIMIT 1";
mysql_query($update_sql) or die(mysql_error());

echo "
<script type='text/javascript'>
	window.location = '?side=dokumenter/liste&mappe=" . $ny_id . "';
</script>";
