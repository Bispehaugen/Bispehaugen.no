<?php

include_once "sider/intern/slagverkhjelp/funksjoner.php";

function hent_noter($konsertid, $bareAntall = false) {
    $params = array();
	if(!empty($konsertid) && $konsertid!='alle'){
		$sql = "SELECT * FROM noter_notesett, noter_konsert, noter_besetning 
			WHERE arrid=? AND noter_notesett.noteid=noter_konsert.noteid AND noter_notesett.besetningsid=noter_besetning.besetningsid ORDER BY tittel;";
        $params[] = $konsertid;
	} else {
		$sql="SELECT * FROM noter_notesett, noter_besetning WHERE noter_notesett.besetningsid=noter_besetning.besetningsid ORDER BY tittel;";
	}

	$result = hent_og_putt_inn_i_array($sql, $params);

	if ($bareAntall) {
		return count($result);
    } else {
        return $result;
    }
}

function antall_noter($konsertid) {
	return hent_noter($konsertid, true);
}

function neste_kakebaking() {
    global $dbh;
	$bruker = hent_brukerdata();
	$sql = "SELECT arrid, tittel, dato, oppmoetetid FROM `arrangement` WHERE kakebaker = ? AND slettet = 0 AND slutt > NOW() ORDER BY `start` ASC LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($bruker["medlemsid"]));
    return $stmt->fetch();
}

function neste_kakebakere() {
	$sql = "SELECT arrid, tittel, dato, kakebaker
			FROM arrangement
			WHERE kakebaker > 0 AND slettet = 0 AND slutt > NOW() ORDER BY `start` ASC";

	$arrangementer = hent_og_putt_inn_i_array($sql);

	foreach($arrangementer as $arrid => $arrangement) {
		$arrangementer[$arrid]['kakebaker'] = hent_brukerdata($arrangement['kakebaker']);
	}
	return $arrangementer;
}

function neste_slagverkhjelp() {
    global $dbh;
	$bruker = hent_brukerdata();
	$gruppe = hent_slagverkgruppe_for_medlem($bruker['medlemsid']);
	if(empty($gruppe)) {
		return Array();
	}
	$sql = "SELECT arrid, tittel, dato, oppmoetetid, slagverk FROM `arrangement` WHERE slagverk = ? AND slettet = 0 AND slutt > NOW() ORDER BY `start` ASC LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($gruppe["gruppeid"]));
    return $stmt->fetch();
}
