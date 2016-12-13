<?php

function hent_mapper($ider, $hentUndermapper=false, $mappetype = null) {
	$id_type = $hentUndermapper ? "foreldreid" : "id";
	$id_verdi = $hentUndermapper ? "id" : "";

    if (is_string($ider)) {
        $ider = explode(" ", $ider);
    }

    $placeholders = implode(",", array_fill(0, count($ider), "?"));
	$sql="SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE :id_type IN ($placeholders)";
    $params = array(":id_type" => $id_type)
	if(!is_null($mappetype)) {
		$sql .= " AND mappetype = :mappetype".$mappetype;
        $params[":mappetype"] = $mappetype;
	}
	$sql .=" ORDER BY tittel ASC";

	return hent_og_putt_inn_i_array($sql, array_merge($params, $ider), $id_verdi);
}

function hent_filer($mappeid) {
    global $dbh;
	$sql="SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, mappetype, tid FROM filer WHERE mappeid = ? ORDER BY tittel ASC";
	return hent_og_putt_inn_i_array($sql, array($mappeid));
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
    global $dbh;
	$sql = "SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, mappeid, mappetype, tid FROM filer WHERE id = ?";
	return hent_og_putt_inn_i_array($sql, array($filid));
}

function hent_fil_med_mappeinfo($filid) {
    global $dbh;
	$sql = "SELECT m.mappenavn, f.filnavn, f.filtype, f.tittel, f.mappetype FROM filer AS f JOIN mapper AS m ON f.mappeid = m.id WHERE f.id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($filid));
    return $stmt->fetch();
}

function hent_filpath($filMedMappeinfo) {
	$rootDir = strtolower(hent_mappetype_navn($filMedMappeinfo['mappetype']));
	return $rootDir . "/" . $filMedMappeinfo['mappenavn'] . "/" . $filMedMappeinfo['filnavn'];
}

function sok_i_notesett($sokestreng) {
    global $dbh;

    $params = array();
	if (is_numeric($sokestreng)) {
		$sql = "SELECT mappeid 
			FROM noter_notesett 
			WHERE arkivnr = ?
			ORDER BY tittel ASC";
        $params[] = $sokestreng;
	} else {
		$sql = "SELECT mappeid 
				FROM noter_notesett 
				WHERE arrangor LIKE ?
				   OR komponist LIKE ?
				ORDER BY tittel ASC";
        $params[] = "%$sokestreng%";
	}

    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($sokestreng));
	if ($stmt->rowCount() == 0) return Array();
	
	$notesett = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $placeholders = implode(",", array_fill(0, count($notesett), "?"));

    $sql = "SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE :id_type IN ($placeholders) AND mappetype = :mappetype ORDER BY tittel ASC";

    $params[":id_type"] = $id_type;
    $params[":mappetype"] = Mappetype::Noter;

	return hent_og_putt_inn_i_array($sql, array_merge($notesett, $params));
}

function sok_mapper($sokestreng, $mappetype = Mappetype::Dokumenter) {
	$sql = "SELECT id, mappenavn, tittel, beskrivelse, mappetype, foreldreid, filid, komiteid FROM mapper WHERE mappetype = :mappetype";
    $params = array(":mappetype" => $mappetype);

	$delstrenger = explode(" ", $sokestreng);
	foreach($delstrenger as $delstreng) {
		$sql .= " AND tittel LIKE ?";
        $params[] = "%$delstreng%";
	}
	$sql .=" ORDER BY tittel ASC";

	$mapperesultat = hent_og_putt_inn_i_array($sql, $params);

	// Søk etter arkivid, arrangører og komponister i noter_notesett
	$notesettresultat = sok_i_notesett($sokestreng);

	if (empty($notesettresultat)) return $mapperesultat;
	if (empty($mapperesultat)) return $notesettresultat;

	$mergedArray = array_merge($notesettresultat, $mapperesultat);
	usort($mergedArray, function($a, $b) {
	    return $b['tittel'] - $a['tittel'];
	});
	return $mergedArray;
}

function sok_filer($sokestreng, $mappetype = Mappetype::Dokumenter) {
    global $dbh;
	$sql = "SELECT id, filnavn, tittel, beskrivelse, filtype, medlemsid, tid FROM filer WHERE mappetype = :mappetype";
    $params = array(":mappetype" => $mappetype);
	$delstrenger = explode(" ", $sokestreng);
	foreach($delstrenger as $delstreng) {
		$sql .= " AND tittel LIKE ?";
        $params[] = "%$delstreng%";
	}
	$sql .=" ORDER BY tittel ASC";
	return hent_og_putt_inn_i_array($sql, $params);
}

function hent_noteinfo($mappeid) {
	$sql = "SELECT noteid, tittel, komponist, arrangor, besetningstype, arkivnr, b.besetningsid
			FROM noter_notesett AS n
			JOIN noter_besetning b ON n.besetningsid = b.besetningsid
			WHERE mappeid = ?";
	return hent_og_putt_inn_i_array($sql, array($mappeid));
}

///////////// Liste funksjoner

function formater_mappe($mappe) {
	$mappenavn = $mappe['tittel'];
	$mappetype = $mappe['mappetype'];
	echo "<section class='mappe dokument'>";
	echo "  <a href='?side=dokumenter/liste&type=".$mappetype."&mappe=" . $mappe['id'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>";
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
		<a class='button' href='?side=dokumenter/liste&type=".$mappe['mappetype']."&mappe=" . $mappe['foreldreid'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>
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
		case Mappetype::Noter:
			$navn = "notemappe";
			break;
		default:
		case Mappetype::Dokumenter:
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
		<p>Søk</p>
		</button>
	</section>";
}

function formater_vis_admin_knapp() {
echo "<section class='vis-admin-knapper legg-til-knapp'>
		<button class='button' onClick='show_admin_buttons()'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-edit fa-stack-2x'></i>
		</section>
		<p>Admin-verktøy</p>
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
	<a class='close' href='javascript:close_add()' title='Avbryt opprettingen av ny mappe'><i class='fa fa-remove'></i>Avbryt</a>
</section>";
}

function formater_endre_mappe($mappe, $noteinfo=Array()) {
	echo "<section class='add-files-and-folder edit-folder handlinger'>
	<form action='?side=dokumenter/endre-mappe' method='POST'>
		<h2>Endre mappe</h2>
		<input type='hidden' name='mappeid' value='".$mappe['id']."' />
		<input type='hidden' name='mappetype' value='".$mappe['mappetype']."' />
		<input type='text' class='text-input navn' name='navn' placeholder='Navn' value='".$mappe['tittel']."' />";

	if(!empty($noteinfo)) {
		echo "<h3>Noteinfo:</h3>";
		echo "<input type='hidden' name='noteid' value='".$noteinfo['noteid']."' />";
		echo "<input type='text' class='text-input arrangor' name='arrangor' placeholder='Arrangør' value='".$noteinfo['arrangor']."' />";
		echo "<br /><input type='text' class='text-input komponist' name='komponist' placeholder='Komponist' value='".$noteinfo['komponist']."' />";
		echo "<br /><input type='text' class='text-input arkivnr' name='arkivnr' placeholder='Arkivnr' value='".$noteinfo['arkivnr']."' />";
		echo "<br /><label>Besetningstype: <select name='besetningsid'>";
		$besetninger = hent_besetning();
		foreach($besetninger as $besetningsid => $besetning) {
			$selected_html = ($besetningsid == $noteinfo['besetningsid']) ? " selected='selected'" : "";
			echo "<option value='".$besetningsid."'".$selected_html.">".ucfirst($besetning['besetningstype'])."</option>";
		}
		echo "</select></label><br /><br />";
	}
	
	echo "	
		<input class='button' type='submit' value='Endre' />
	</form>
	<a class='close' href='javascript:close_add()' title='Avbryt opprettingen av ny mappe'><i class='fa fa-remove'></i>Avbryt</a>
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
	<a class='close' href='javascript:close_add()' title='Avbryt'><i class='fa fa-remove'></i>Avbryt</a>
</section>";
}

function formater_sokeboks($mappetype) {
	$sokestreng = get('sok');
	$harSokestrengCss = !empty($sokestreng) ? "har-sokestreng" : "";
echo "<section class='sokeboks handlinger " . $harSokestrengCss . "'>
		<form method='get' action='?'>
			<input type='hidden' name='side' value='dokumenter/liste' />
			<input type='hidden' name='type' value='".$mappetype."' />
			<input class='sokeinput' type='text' name='sok' value='" . $sokestreng . "' placeholder='Søk i tittel, arkivnr, arr/komp...' />
			<button class='button sok' type='submit'>Søk</button>
			<a href='?side=dokumenter/liste&type=".$mappetype."' class='avbryt' title='Avbry søk'><i class='fa fa-remove fa-2x'></i></a>
		</form>
	</section>";	
}

function formater_noteinfo($info) {
if(empty($info)) return;
echo "<section class='noteinfo'>";
formater_noteinfo_hvis_ikke_tom("tittel", $info);
formater_noteinfo_hvis_ikke_tom("besetningstype", $info);
formater_noteinfo_hvis_ikke_tom("arrangor", $info);
formater_noteinfo_hvis_ikke_tom("komponist", $info);
formater_noteinfo_hvis_ikke_tom("arkivnr", $info);
echo "</section>";
}

function formater_noteinfo_hvis_ikke_tom($type, $infoArray) {
	$info = $infoArray[$type];
	if(!empty($info)) {
		if ($type == "arrangor") $type = "Arrangør";
		echo "<span><b>".ucfirst($type).":</b> ".$info."</span>";
	}
}
