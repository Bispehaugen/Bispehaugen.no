<?php


function hent_aktivitet($id) {
	$sql = "SELECT arrid, tittel, type, sted, start, slutt, dato, oppmoetetid, ingress, beskrivelsesdok, public, uthevet, hjelpere, kakebaker, slettet, versjon FROM `arrangement` WHERE `arrid`=".mysql_real_escape_string($id);
	return hent_og_putt_inn_i_array($sql);
}

function hent_konsert_nyhetsid($arrid) {
	$sql = "SELECT nyhetsid_konsert FROM `konserter` WHERE `arrid_konsert`=".mysql_real_escape_string($arrid);
	$result = hent_og_putt_inn_i_array($sql);

	if(empty($result)) {
		return "";
	}
	return $result['nyhetsid_konsert'];
}