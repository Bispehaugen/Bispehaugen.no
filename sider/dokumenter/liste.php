<?php

include_once("sider/dokumenter/funksjoner.php");

if(!er_logget_inn()) {
	header('Location: ?side=ikke_funnet');
	die();
}


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
	$filtypeIkon = fil_ikon($fil['filtype']);
	$filnavn = $fil['tittel'].'.'.$fil['filtype'];

	echo "<section class='fil dokument'>";
	echo "	<a href='fil.php?fil=" . $fil['id'] . "' title='Klikk for å laste ned filen ".$filnavn."'>";
	echo "	<i class='fa ".$filtypeIkon."'></i>";
	echo "	<p>".$filnavn."</p>";
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
function open_new_folder() {
	$(".add-folder").toggle();
}
function open_new_files() {
	$(".add-files").toggle();
}
function close_add() {
	$(".add-files-and-folder").hide();
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

formater_ny_knapp($foreldreId, "mappe", "open_new_folder");
formater_ny_knapp($foreldreId, "filer", "open_new_files");

echo "</header>";

echo "<section class='add-files-and-folder add-folder'>
	<form action='?side=dokumenter/ny-mappe' method='POST'>
		<h2>Legg til ny mappe</h2>
		<input type='text' class='text-input' name='navn' placeholder='Navn' />
		<input type='hidden' name='foreldreid' value='".$foreldreId."' />
		<input type='submit' value='Opprett' />
	</form>
	<a class='close' href='javascript:close_add()' title='Avbryt opprettingen av ny mappe'><i class='fa fa-remove'></i> Avbryt</a>
</section>";

echo "<section class='mapper'>";

$mapper = hent_undermapper($foreldreId);

$filer = hent_filer($foreldreId);

$dine_komiteer = hent_komiteer_for_bruker();

foreach($mapper as $mappe ) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		formater_mappe($mappe);
	}
}

foreach($filer as $fil) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		formater_fil($fil);
	}
}

echo "</section>";
echo "</section>";
