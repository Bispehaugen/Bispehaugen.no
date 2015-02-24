<?php

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