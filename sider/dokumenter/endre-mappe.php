<?php

include_once("sider/dokumenter/funksjoner.php");

if(!has_post('navn') || !has_post('mappeid') || !has_post('mappetype')) {
	die("Kan ikke endre mappe uten innsendt navn, mappetype eller mappeid");
}

if(!er_logget_inn()) {
	die("Du må være logget inn");
}
if (!tilgang_endre()) {
	die("Du har ikke lov til å endre mapper!");
}

$navn = post('navn');
$mappeid = post('mappeid');
$mappetype = intval(post('mappetype'));

// Endre mappe i sql
$sql = "UPDATE mapper SET tittel = '$navn' WHERE id = " . $mappeid . " LIMIT 1";
mysql_query($sql) or die(mysql_error());

echo "
<script type='text/javascript'>
	window.location = '?side=dokumenter/liste&mappe=" . $mappeid . "&type=" . $mappetype . "';
</script>";
