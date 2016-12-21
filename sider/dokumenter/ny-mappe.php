<?php
global $dbh;

include_once("sider/dokumenter/funksjoner.php");

if(!has_post('navn') || !has_post('foreldreid') || !has_post('mappetype')) {
	die("Kan ikke opprette ny mappe uten innsendt navn, mappetype eller foreldreid");
}

if(!er_logget_inn()) {
	die("Du må være logget inn");
}
if (!tilgang_endre()) {
	die("Du har ikke lov til å opprette mapper!");
}

$navn = post('navn');
$foreldreid = post('foreldreid');
$mappetype = intval(post('mappetype'));

// Opprett ny mappe i sql
$sql = "INSERT INTO mapper (mappenavn, tittel, mappetype, foreldreid) VALUES ('Kommer', ?, ?, ?)";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($navn, $mappetype, $foreldreid));

// hent ID
$ny_id = $dbh->lastInsertId();

// Opprett ny mappe i filstruktur
$mappenavn = $ny_id . "-" . $navn;
$mappetype_path = strtolower(hent_mappetype_navn($mappetype));
$path = $mappetype_path."/".$mappenavn;

if(!is_dir($path)) {
	mkdir($path, 0751);
}

// Oppdatert mappe med riktig filstruktur
$update_sql = "UPDATE mapper SET mappenavn = ? WHERE id = ? LIMIT 1";
$stmt = $dbh->prepare($update_sql);
$stmt->execute(array($mappenavn, $ny_id));

echo "
<script type='text/javascript'>
	window.location = '?side=dokumenter/liste&mappe=" . $ny_id . "&type=" . $mappetype . "';
</script>";
