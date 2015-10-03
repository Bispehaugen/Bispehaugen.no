<?php

include_once("sider/dokumenter/funksjoner.php");

if(!has_post('navn') || !has_post('foreldreid')) {
	die("Kan ikke opprette ny mappe uten innsendt navn eller foreldreid");
}

$navn = post('navn');
$foreldreid = post('foreldreid');

// Opprett ny mappe i sql
$sql = "INSERT INTO mapper (mappenavn, idpath, tittel, mappetype, foreldreid) VALUES ('Kommer', '?', '$navn', 1, $foreldreid)";
mysql_query($sql) or die(mysql_error());

// hent ID
$ny_id = mysql_insert_id();

// Hent foreldremappe for å få idpath
$foreldremappe = hent_mappe($foreldreid);

// Opprett ny mappe i filstruktur
$mappenavn = $ny_id . "-" . $navn;
$path = "dokumenter/".$mappenavn;

if(!is_dir($path)) {
	mkdir($path, 0751);
}

$ny_id_path = $foreldreid == 0 ? "/" : $foreldremappe['idpath'].$foreldreid."/";

// Oppdatert mappe med riktig filstruktur
$update_sql = "UPDATE mapper SET mappenavn = '".$mappenavn."', idpath = '".$ny_id_path."' WHERE id = '".$ny_id."' LIMIT 1";
mysql_query($update_sql) or die(mysql_error());

echo "
<script type='text/javascript'>
	window.location = '?side=dokumenter/liste&mappe=" . $ny_id . "';
</script>";
