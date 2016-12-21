<?php

function hent_aktivitet($id) {
    global $dbh;
	$sql = "SELECT arrid, tittel, type, sted, start, slutt, dato, oppmoetetid, ingress, beskrivelsesdok, public, uthevet, hjelpere, kakebaker, slettet, versjon FROM `arrangement` WHERE `arrid`=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
	return $stmt->fetch();
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
