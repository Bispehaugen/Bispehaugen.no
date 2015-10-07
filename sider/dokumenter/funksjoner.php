<?php

function hent_mapper($ider, $hentUndermapper=false, $mappetype = null) {
	$id_type = $hentUndermapper ? "foreldreid" : "id";
	$id_verdi = $hentUndermapper ? "id" : "";
	$sql="SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE ".$id_type." IN (".mysql_real_escape_string($ider).")";
	if(!is_null($mappetype)) {
		$sql .= " AND mappetype = ".$mappetype;
	}
	$sql .=" ORDER BY tittel DESC";
	return hent_og_putt_inn_i_array($sql, $id_verdi=$id_verdi);
}

function hent_filer($mappeid) {
	$sql="SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, mappetype, tid FROM filer WHERE mappeid = ".intval(mysql_real_escape_string($mappeid)) . " ORDER BY tittel DESC";
	return hent_og_putt_inn_i_array($sql, $id_verdi="id");
}

function hent_mappe($id, $mappetype = null) {
	if($id == 0) {
		return Array("id" => 0, "mappenavn" => "/", "tittel" => "Dokumenter");
	}
	$mappe = hent_mapper($id, false, $mappetype);

	if(empty($mappe)) {
		echo "<h1>Fant ikke mappe med id: ".$id."</h1>";
		echo "<a href='?side=dokumenter/liste'>Tilbake til dokumenter</a><br />";
		die();
	}
	return $mappe;
}

function hent_undermapper($id, $mappetype = Mappetype::Dokumenter) {
	return hent_mapper($id, true, $mappetype);
}

function hent_fil($filid) {
	$sql="SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, mappeid, mappetype, tid FROM filer WHERE id = ".intval(mysql_real_escape_string($filid));
	return hent_og_putt_inn_i_array($sql);
}

function hent_fil_med_mappeinfo($filid) {
	$sql = "SELECT m.mappenavn, f.filnavn, f.filtype, f.tittel, f.mappetype FROM filer AS f JOIN mapper AS m ON f.mappeid = m.id WHERE f.id = ".$filid;
	return hent_og_putt_inn_i_array($sql);
}

function hent_filpath($filMedMappeinfo) {
	$rootDir = strtolower(hent_mappetype_navn($filMedMappeinfo['mappetype']));
	return $rootDir . "/" . $filMedMappeinfo['mappenavn'] . "/" . $filMedMappeinfo['filnavn'];
}

function sok_mapper($sokestreng, $mappetype = Mappetype::Dokumenter) {
	$sql = "SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE  mappetype = ".$mappetype." ";
	$delstrenger = explode(" ", $sokestreng);
	foreach($delstrenger as $delstreng) {
		$sql .= "AND tittel LIKE '%" . mysql_real_escape_string($delstreng) . "%'";
	}
	$sql .=" ORDER BY tittel DESC";
	return hent_og_putt_inn_i_array($sql, $id_verdi="id");
}

function sok_filer($sokestreng, $mappetype = Mappetype::Dokumenter) {
	$sql = "SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, tid FROM filer WHERE mappetype = ".$mappetype." ";
	$delstrenger = explode(" ", $sokestreng);
	foreach($delstrenger as $delstreng) {
		$sql .= "AND tittel LIKE '%" . mysql_real_escape_string($delstreng) . "%'";
	}
	$sql .=" ORDER BY tittel DESC";
	return hent_og_putt_inn_i_array($sql, $id_verdi="id");
}

///////////// Liste funksjoner

function formater_mappe($mappe) {
	$mappenavn = $mappe['tittel'];
	$mappetype = $mappe['mappetype'];
	echo "<section class='mappe dokument'>";
	echo "  <a href='?side=dokumenter/liste&amp;type=".$mappetype."&amp;mappe=" . $mappe['id'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>";
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
		<a class='button' href='?side=dokumenter/liste&amp;type=".$mappe['mappetype']."&amp;mappe=" . $mappe['foreldreid'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-level-up fa-stack-2x'></i>
		</section>
		<p>Tilbake opp<br />en mappe</p>
		</a>
	</section>";
}

function formater_ny_knapp($id, $type, $javascriptknapp, $mappetype) {
	switch($mappetype) {
		case Mappetype::Bilder:
			$navn = ($type == "mappe") ? "album" : "bilder";
			$hovedIkon = ($type == "mappe") ? "fa-folder-o" : "fa-image";
			$plussIkon = ($type == "mappe") ? "fa-image" : "";
			break;
		case Mappetype::Noter:
			$navn = ($type == "mappe") ? "mappe" : "noter";
			$hovedIkon = ($type == "mappe") ? "fa-folder-o" : "fa-music";
			$plussIkon = ($type == "mappe") ? "fa-music" : "";
			break;
		default:
		case Mappetype::Dokumenter:
			$navn = ($type == "mappe") ? "mappe" : "filer";
			$hovedIkon = ($type == "mappe") ? "fa-folder-o" : "fa-file-o";
			$plussIkon = "fa-plus";
			break;
	}
echo "<section class='legg-til-".$type." legg-til-knapp'>
		<button class='button' onClick='".$javascriptknapp."()'>
		<section class='fa-stack fa-lg'>
		  <i class='fa ".$hovedIkon." fa-stack-2x'></i>
		  <i class='fa ".$plussIkon." fa-stack-1x'></i>
		</section>
		<p>Legg til ".$navn."</p>
		</button>
	</section>";
}

function formater_endre_mappe_knapp($mappeid, $javascriptknapp, $mappetype) {
		switch($mappetype) {
		case Mappetype::Bilder:
			$navn = "album";
			break;
		default:
		case Mappetype::Dokumenter:
		case Mappetype::Noter:
			$navn = "mappe";
			break;
	}
echo "<section class='legg-til-mappe legg-til-knapp'>
		<button class='button' onClick='".$javascriptknapp."()'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-edit fa-stack-2x'></i>
		</section>
		<p>Endre ".$navn."</p>
		</button>
	</section>";
}

function formater_soke_knapp() {
echo "<section class='soke-knapp legg-til-knapp'>
		<button class='button' onClick='vis_sokeknapp()'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-search fa-stack-2x'></i>
		</section>
		<p>Søk i dokumenter</p>
		</button>
	</section>";
}

function formater_legg_til_ny_mappe($foreldreId, $mappetype) {
echo "<section class='add-files-and-folder add-folder handlinger'>
	<form action='?side=dokumenter/ny-mappe' method='POST'>
		<h2>Legg til ny mappe</h2>
		<input type='text' class='text-input navn' name='navn' placeholder='Navn' />
		<input type='hidden' name='foreldreid' value='".$foreldreId."' />
		<input type='hidden' name='mappetype' value='".$mappetype."' />
		<input class='button' type='submit' value='Opprett' />
	</form>
	<a class='close' href='javascript:close_add()' title='Avbryt opprettingen av ny mappe'><i class='fa fa-remove'></i> Avbryt</a>
</section>";
}

function formater_endre_mappe($mappe) {
	echo "<section class='add-files-and-folder edit-folder handlinger'>
	<form action='?side=dokumenter/endre-mappe' method='POST'>
		<h2>Endre mappe</h2>
		<input type='text' class='text-input navn' name='navn' placeholder='Navn' value='".$mappe['tittel']."' />
		<input type='hidden' name='mappeid' value='".$mappe['id']."' />
		<input type='hidden' name='mappetype' value='".$mappe['mappetype']."' />
		<input class='button' type='submit' value='Endre' />
	</form>
	<a class='close' href='javascript:close_add()' title='Avbryt opprettingen av ny mappe'><i class='fa fa-remove'></i> Avbryt</a>
</section>";
}


function formater_legg_til_nye_filer($foreldreId, $mappetype) {
	switch($mappetype) {
		case Mappetype::Bilder:
			$filtype = "bilder";
			break;
		case Mappetype::Noter:
			$filtype = "noter";
			break;
		default:
		case Mappetype::Dokumenter:
			$filtype = "filer";
			break;
	}
echo "<section class='add-files-and-folder add-files dropzone handlinger'>
		<h2>Legg til nye ".$filtype."</h2>
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

function formater_sokeboks($mappetype) {
	$sokestreng = get('sok');
	$harSokestrengCss = !empty($sokestreng) ? "har-sokestreng" : "";
echo "<section class='sokeboks handlinger " . $harSokestrengCss . "'>
		<form method='get' action='?'>
			<input type='hidden' name='side' value='dokumenter/liste' />
			<input type='hidden' name='type' value='".$mappetype."' />
			<input class='sokeinput' type='text' name='sok' value='" . $sokestreng . "' placeholder='Søk...' />
			<button class='button sok' type='submit'>Søk</button>
			<a href='?side=dokumenter/liste&amp;type=".$mappetype."' class='avbryt' title='Avbry søk'><i class='fa fa-remove fa-2x'></i></a>
		</form>
	</section>";	
}