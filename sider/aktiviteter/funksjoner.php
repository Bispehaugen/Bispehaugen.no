<?php

require_once __DIR__ . '/../intern/funksjoner.php';

function hent_aktivitet($id) {
    global $dbh;
	$sql = "SELECT arrid, tittel, type, sted, start, slutt, dato, oppmoetetid, ingress, beskrivelsesdok, public, uthevet, hjelpere, slettet, versjon FROM `arrangement` WHERE `arrid`=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
	$arr = $stmt->fetch();
    $arr['kakebakere'] = kakebakere($id);
    return $arr;
}

function hent_konsert_nyhetsid($arrid) {
    global $dbh;
	$sql = "SELECT nyhetsid_konsert FROM `konserter` WHERE `arrid_konsert`=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($arrid));

	if($stmt->rowCount() == 0) {
		return "";
	}
	return $stmt->fetchColumn();
}
