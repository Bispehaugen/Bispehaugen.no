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
			array_push($grupper[$h['gruppeid']], $hjelper);
		} else {
			$grupper[$h['gruppeid']] = Array($hjelper);
		}
	}

	return $grupper;
}

function neste_gang_slagverkhjelp($medlemsid) {

}