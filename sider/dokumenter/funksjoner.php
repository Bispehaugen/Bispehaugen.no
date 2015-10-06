<?php

function hent_mapper($ider, $hentUndermapper=false) {
	$id_type = $hentUndermapper ? "foreldreid" : "id";
	$id_verdi = $hentUndermapper ? "id" : "";
	$sql="SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE ".$id_type." IN (".mysql_real_escape_string($ider).")";
	return hent_og_putt_inn_i_array($sql, $id_verdi=$id_verdi);
}

function hent_filer($mappeid) {
	$sql="SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, tid FROM filer WHERE mappeid = ".intval(mysql_real_escape_string($mappeid));
	return hent_og_putt_inn_i_array($sql, $id_verdi="id");
}

function hent_mappe($id) {
	if($id == 0) {
		return Array("id" => 0, "mappenavn" => "/", "tittel" => "Dokumenter", "idpath" => "/");
	}
	$mappe = hent_mapper($id, false);

	if(empty($mappe)) {
		echo "<h1>Fant ikke mappe med id: ".$id."</h1>";
		echo "<a href='?side=dokumenter/liste'>Tilbake til dokumenter</a><br />";
		die();
	}
	return $mappe;
}

function hent_undermapper($id) {
	return hent_mapper($id, true);
}


function hent_fil($filid) {
	$sql="SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, mappeid, tid FROM filer WHERE id = ".intval(mysql_real_escape_string($filid));
	return hent_og_putt_inn_i_array($sql);
}

function hent_fil_med_mappeinfo($filid) {
	$mysql = "SELECT m.mappenavn, f.filnavn, f.filtype FROM filer AS f JOIN mapper AS m ON f.mappeid = m.id WHERE f.id = ".$filid;
	$result = mysql_query($mysql);

	while ($file = mysql_fetch_assoc($result)) {
		return $file;
	}
	die("Fant ingen fil med ".$filid);
}

function hent_filpath($filMedMappeinfo) {
	return "dokumenter/" . $filMedMappeinfo['mappenavn'] . "/" . $filMedMappeinfo['filnavn'];
}

///////////// Liste funksjoner

function formater_mappe($mappe) {
	$mappenavn = $mappe['tittel'];
	echo "<section class='mappe dokument'>";
	echo "  <a href='?side=dokumenter/liste&amp;mappe=" . $mappe['id'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>";
	echo "  <i class='fa fa-folder-o'></i><p>" . $mappenavn . "</p></a>";
	if (tilgang_endre()) {
		echo "  <a class='slett' a href='javascript:slett_mappe(".$mappe['id'].", \"".$mappenavn."\")' title='Slett mappen: ".$mappenavn."'><i class='fa fa-remove'></i></a>";
	}
	echo "</section>";
}

function formater_fil($fil) {
	$filtype = $fil['filtype'];
	$filtypeIkon = fil_ikon($filtype);
	$filnavn = $fil['tittel'].'.'.$filtype;
	$filUrl = "fil.php?fil=" . $fil['id'];

	echo "<section class='fil dokument'>";
	echo "	<a href='".$filUrl."' title='Klikk for å laste ned filen ".$filnavn."'>";
	if(fil_er_bilde($filtype)) {
		echo "  <img class='bilde thumb' src='" . thumbFilid($fil['id'], "", 45) . "' />";
	} else {
		echo "	<i class='fa ".$filtypeIkon."'></i>";
	}
	echo "	<p>".$filnavn."</p>";
	echo "  </a>";
	if (tilgang_endre()) {
		echo "<a class='slett' a href='javascript:slett_fil(".$fil['id'].", \"".$filnavn."\")' title='Slett filen: ".$filnavn."'><i class='fa fa-remove'></i></a>";
	}
	echo "</section>";
}

function formater_tilbakeknapp($mappe, $vis) {
echo "<section class='tilbake legg-til-knapp" . ($vis ? "" : " skjul") . "'>
		<a class='button' href='?side=dokumenter/liste&amp;mappe=" . $mappe['foreldreid'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-level-up fa-stack-2x'></i>
		</section>
		<p>Tilbake opp<br />en mappe</p>
		</a>
	</section>";
}

function formater_ny_knapp($id, $type, $javascriptknapp) {
	$ikon = ($type == "mappe") ? "folder" : "file"; 
echo "<section class='legg-til-".$type." legg-til-knapp'>
		<button class='button' onClick='".$javascriptknapp."()'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-".$ikon."-o fa-stack-2x'></i>
		  <i class='fa fa-plus fa-stack-1x'></i>
		</section>
		<p>Legg til ".$type."</p>
		</button>
	</section>";
}

function formater_legg_til_ny_mappe($foreldreId) {
echo "<section class='add-files-and-folder add-folder'>
	<form action='?side=dokumenter/ny-mappe' method='POST'>
		<h2>Legg til ny mappe</h2>
		<input type='text' class='text-input navn' name='navn' placeholder='Navn' />
		<input type='hidden' name='foreldreid' value='".$foreldreId."' />
		<input class='button' type='submit' value='Opprett' />
	</form>
	<a class='close' href='javascript:close_add()' title='Avbryt opprettingen av ny mappe'><i class='fa fa-remove'></i> Avbryt</a>
</section>";
}


function formater_legg_til_nye_filer($foreldreId) {
echo "<section class='add-files-and-folder add-files dropzone'>
		<h2>Legg til nye filer</h2>
		<p>Dra nye filer til denne firkanten eller klikk på \"Velg filer\" lengre ned.</p>
		<ul class='filelist'></ul>
		<div class='status'>
		  <span class='bar' style='width: 0%'></span>
		</div>
		<button class='button velg-filer'>Velg filer</button>
		<button class='button last-opp' disabled='disabled'>Last opp</button>
	<a class='close' href='javascript:close_add()' title='Avbryt'><i class='fa fa-remove'></i> Avbryt</a>
</section>";
}