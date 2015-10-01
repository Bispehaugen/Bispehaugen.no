<?php

function hent_mapper($ider, $hentUndermapper=false) {
	$id_type = $hentUndermapper ? "foreldreid" : "id";
	$id_verdi = $hentUndermapper ? "id" : "";
	$sql="SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE ".$id_type." IN (".mysql_real_escape_string($ider).")";
	return hent_og_putt_inn_i_array($sql, $id_verdi=$id_verdi);
}

function hent_filer($id) {
	$sql="SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, tid FROM filer WHERE id = ".intval(mysql_real_escape_string($id));
	return hent_og_putt_inn_i_array($sql, $id_verdi="id");
}

function hent_mappe($id) {
	if($id == 0) {
		return Array("id" => 0, "mappenavn" => "/", "tittel" => "Dokumenter", "idpath" => "/");
	}

	$mappe = hent_mapper($id, false);

	if(empty($mappe)) {
		die("Fant ikke mappe med id: " + $id);
	}
	return $mappe;
}

function hent_undermapper($id) {
	return hent_mapper($id, true);
}