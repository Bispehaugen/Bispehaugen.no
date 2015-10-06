<?php

include_once("sider/dokumenter/funksjoner.php");

if(!er_logget_inn()) {
	header('Location: ?side=ikke_funnet');
	die();
}

$foreldreId = intval(has_get('mappe')) ? get('mappe') : 0;
if(empty($foreldreId)) {
	$foreldreId = 0;
}

$sokestreng = get('sok');

$sokemodus = has_get('sok');

?>
<script type="text/javascript">
function slett_fil(id, navn) {
	var skalSlette = confirm("Er du sikker du vil slette filen: "+navn);
	if (skalSlette) {
		$.post( "sider/dokumenter/slett.php?fil="+id, function() {
			location.reload();
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
			location.reload();
		})
		.fail(function(data) {
		    alert(data.responseJSON.message);
		});
	}
}
function open_new_folder() {
	$(".handlinger").hide();
	$(".add-folder").show();
	$(".add-folder .navn").focus();
}
function open_new_files() {
	$(".handlinger").hide();
	$(".add-files").show();
}
function close_add() {
	$(".add-files-and-folder").hide();
}
function vis_sokeknapp() {
	$(".handlinger").hide();
	$(".sokeboks").show();
	$(".sokeboks .sokeinput").focus();
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

formater_tilbakeknapp($foreldremappe, $foreldreId > 0);

if (!$sokemodus) {
	formater_ny_knapp($foreldreId, "mappe", "open_new_folder");
	formater_ny_knapp($foreldreId, "filer", "open_new_files");	
}

formater_soke_knapp();

echo "</header>";

if (!$sokemodus && tilgang_endre()) {
	formater_legg_til_ny_mappe($foreldreId);
	formater_legg_til_nye_filer($foreldreId);
}
formater_sokeboks();

echo "<section class='mapper'>";

if ($sokemodus) {
	$mapper = sok_mapper($sokestreng);
	$filer = sok_filer($sokestreng);
} else {
	$mapper = hent_undermapper($foreldreId);
	$filer = hent_filer($foreldreId);
}

$dine_komiteer = hent_komiteer_for_bruker();

$antall_mapper_og_filer = 0;

foreach($mapper as $mappe ) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		formater_mappe($mappe);
		$antall_mapper_og_filer++;
	}
}

foreach($filer as $fil) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		formater_fil($fil);
		$antall_mapper_og_filer++;
	}
}
echo "</section>";

if ($antall_mapper_og_filer == 0) {
	if ($sokemodus) {
		echo "<h3>Søket etter \"" . $sokestreng . "\" ga ingen resultat.</h3>";
	} else {
		echo "<h3>Mappen er tom.</h3>";
	}
}
echo "</section>";


if (tilgang_endre()) {
	include_once "flow.php";
}