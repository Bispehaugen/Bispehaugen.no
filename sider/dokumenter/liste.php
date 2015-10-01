<?php

if(!er_logget_inn()) {
	header('Location: ?side=ikke_funnet');
	die();
}

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
	return hent_mapper($id, false);
}

function hent_undermapper($id) {
	return hent_mapper($id, true);
}

function formater_mappe($mappe) {
	$mappenavn = $mappe['tittel'];
	echo "<section class='mappe dokument'>";
	echo "  <a href='?side=dokumenter/liste&amp;mappe=" . $mappe['id'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>";
	echo "  <i class='fa fa-folder-o'></i>" . $mappenavn . "</a>";
	if (tilgang_endre()) {
		echo "  <a class='slett' a href='javascript:slett_mappe(".$mappe['id'].", \"".$mappenavn."\")' title='Slett mappen: ".$mappenavn."'><i class='fa fa-remove'></i></a>";
	}
	echo "</section>";
}

function formater_fil($fil) {
	$filtypeIkon = fil_ikon($fil['filtype']);
	$filnavn = $fil['tittel'].'.'.$fil['filtype'];

	echo "<section class='fil dokument'>";
	echo "	<a href='fil.php?fil=" . $fil['id'] . "' title='Klikk for å laste ned filen ".$filnavn."'>";
	echo "	<i class='fa ".$filtypeIkon."'></i>";
	echo 	$filnavn;
	echo "  </a>";
	if (tilgang_endre()) {
		echo "<a class='slett' a href='javascript:slett_fil(".$fil['id'].", \"".$filnavn."\")' title='Slett filen: ".$filnavn."'><i class='fa fa-remove'></i></a>";
	}
	echo "</section>";
}

function formater_tilbakeknapp($mappe) {
echo "<section class='tilbake legg-til-knapp'>
		<a class='button' href='?side=dokumenter/liste&amp;mappe=" . $mappe['foreldreid'] . "' title='Klikk for å åpne mappen ".$mappenavn."'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-level-up fa-stack-2x'></i>
		</section>
		<p>Tilbake opp en mappe</p>
		</a>
	</section>";
}

function formater_ny_knapp($id, $type) {
	$ikon = ($type == "mappe") ? "folder" : "file"; 
echo "<section class='legg-til-".$type." legg-til-knapp'>
		<button class='button'>
		<section class='fa-stack fa-lg'>
		  <i class='fa fa-".$ikon."-o fa-stack-2x'></i>
		  <i class='fa fa-plus fa-stack-1x'></i>
		</section>
		<p>Legg til ".$type."</p>
		</button>
	</section>";
}

$foreldreId = intval(has_get('mappe')) ? get('mappe') : 0;
if(empty($foreldreId)) {
	$foreldreId = 0;
}

?>
<script type="text/javascript">
function slett_fil(id, navn) {
	var skalSlette = confirm("Er du sikker du vil slette filen: "+navn);
	if (skalSlette) {
		$.post( "sider/dokumenter/slett.php?fil="+id, function() {
			//location.reload();
		})
		.fail(function(data) {
		    alert(data.responseJSON.message);
		});
	}
}
function slett_mappe(id, navn) {
	var skalSlette = confirm("Er du sikker du vil slette mappen: "+navn);
	if (skalSlette) {
		$.post( "sider/dokumenter/slett.php?mappe="+id, function() {
			//location.reload();
		})
		.fail(function(data) {
		    alert(data.responseJSON.message);
		});
	}
}
</script>
<?php

echo "<section class='dokumenter'>";

echo "<header class='header'>";
$tittel = "Dokumenter";

if ($foreldreId > 0) {
	$foreldremappe = hent_mappe($foreldreId);
	$tittel = $foreldremappe['tittel'];
}

echo "<h2 class='overskrift'><i class='fa fa-folder-open-o'></i> " . $tittel . "</h2>";

if ($foreldreId > 0) {
	formater_tilbakeknapp($foreldremappe);
}

formater_ny_knapp($foreldreId, "mappe");
formater_ny_knapp($foreldreId, "filer");

echo "</header>";

echo "<section class='mapper'>";

$mapper = hent_undermapper($foreldreId);

$filer = hent_filer($foreldreId);

foreach($mapper as $mappe ) {
	formater_mappe($mappe);
}

foreach($filer as $fil) {
	formater_fil($fil);
}

echo "</section>";
echo "</section>";
