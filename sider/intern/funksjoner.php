<?php

include_once "sider/intern/slagverkhjelp/funksjoner.php";

function hent_noter($konsertid, $bareAntall = false) {

	$felter = "*";
	if ($bareAntall) {
		$felter = "COUNT(noter_notesett.noteid)";
	}

	if(!empty($konsertid) && $konsertid!='alle'){
		$sql = "SELECT ".$felter." FROM noter_notesett, noter_konsert, noter_besetning 
			WHERE arrid=".$konsertid." AND noter_notesett.noteid=noter_konsert.noteid AND noter_notesett.besetningsid=noter_besetning.besetningsid ORDER BY tittel;";
	} else {
		$sql="SELECT ".$felter." FROM noter_notesett, noter_besetning WHERE noter_notesett.besetningsid=noter_besetning.besetningsid ORDER BY tittel;";
	}

	if ($bareAntall) {
		return hent_og_putt_inn_i_array($sql);
	}

	return hent_og_putt_inn_i_array($sql, "noteid");
}

function antall_noter($konsertid) {
	return hent_noter($konsertid, true);
}

function neste_kakebaking() {
	$bruker = hent_brukerdata();
	$sql = "SELECT arrid, tittel, dato, oppmoetetid FROM `arrangement` WHERE kakebaker = '".$bruker['medlemsid']."' AND slettet = 0 AND slutt > NOW() ORDER BY `start` ASC LIMIT 1";
	return hent_og_putt_inn_i_array($sql);
}

function neste_kakebakere() {
	$sql = "SELECT arrid, tittel, dato, kakebaker
			FROM arrangement
			WHERE kakebaker > 0 AND slettet = 0 AND slutt > NOW() ORDER BY `start` ASC";

	$arrangementer = hent_og_putt_inn_i_array($sql, "arrid");

	foreach($arrangementer as $arrid => $arrangement) {
		$arrangementer[$arrid]['kakebaker'] = hent_brukerdata($arrangement['kakebaker']);
	}
	return $arrangementer;
}

function neste_slagverkhjelp() {
	$bruker = hent_brukerdata();
	$gruppe = hent_slagverkgruppe_for_medlem($bruker['medlemsid']);
	if(empty($gruppe)) {
		return Array();
	}
	$sql = "SELECT arrid, tittel, dato, oppmoetetid, slagverk FROM `arrangement` WHERE slagverk = '".$gruppe['gruppeid']."' AND slettet = 0 AND slutt > NOW() ORDER BY `start` ASC LIMIT 1";
	return hent_og_putt_inn_i_array($sql);
}