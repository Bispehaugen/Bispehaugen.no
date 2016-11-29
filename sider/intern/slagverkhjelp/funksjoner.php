<?php

function hent_slagverkhjelp($gruppeid = 0) {
	$sql = "SELECT gruppeid, medlemsid, gruppeleder FROM slagverkhjelp";
	if (!empty($gruppeid)) {
		$sql .= " WHERE gruppeid = ".$gruppeid;
	}
	$sql .=" ORDER BY gruppeid, gruppeleder DESC, medlemsid";
	$hjelpere = hent_og_putt_inn_i_array($sql, "medlemsid");

	$brukere = hent_brukerdata(array_keys($hjelpere));

	$grupper = Array();

	foreach($hjelpere as $h) {
		$hjelper = $brukere[$h['medlemsid']];
		if (array_key_exists($h['gruppeid'], $grupper)) {
			$grupper[$h['gruppeid']][$hjelper['medlemsid']] = $hjelper;
		} else {
			$grupper[$h['gruppeid']] = Array($hjelper['medlemsid'] => $hjelper);
		}
	}

	return $grupper;
}

function hent_slagverkgruppe_for_medlem($medlemsid) {
    global $dbh;
	$sql = "SELECT gruppeid, medlemsid, gruppeleder FROM slagverkhjelp WHERE medlemsid = " . $dbh->quote($medlemsid) . " LIMIT 1";
	return hent_og_putt_inn_i_array($sql);
}

function hent_slagverksgrupper() {
	$sql = "SELECT gruppeid FROM slagverkhjelp GROUP BY gruppeid";
	return array_keys(hent_og_putt_inn_i_array($sql, 'gruppeid'));
}
