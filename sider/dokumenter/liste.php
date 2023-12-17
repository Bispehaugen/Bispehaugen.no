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

$mappetype = has_get('type') ? get('type') : Mappetype::Dokumenter;
$mappetypeNavn = hent_mappetype_navn($mappetype);
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
function open_edit_folder() {
    $(".handlinger").hide();
    $(".edit-folder").show();
}
function show_admin_buttons() {
    $(".admin-knapper").toggleClass("skjul");
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

$tittel = $mappetypeNavn;
$noteinfo = Array();

if ($foreldreId > 0) {
    $foreldremappe = hent_mappe($foreldreId, $mappetype);
    $tittel = $foreldremappe['tittel'];
}

if (!empty($foreldremappe) && $foreldremappe['mappetype'] == Mappetype::Noter) {
    $noteinfo = hent_noteinfo($foreldremappe['id']);
}

echo "<section class='dokumenter'>";

echo "<header class='header'>";

echo "<h2 class='overskrift'><i class='fa fa-folder-open-o'></i>" . $tittel . "</h2>";

formater_tilbakeknapp($foreldremappe, $foreldreId > 0);
formater_soke_knapp();

if (!$sokemodus && tilgang_endre()) {
    formater_vis_admin_knapp();
}
echo "</header>";

if (!$sokemodus && tilgang_endre()) {
    echo "<section class='handlinger admin-knapper skjul'>";
    formater_endre_mappe_knapp($foreldreId, "open_edit_folder", $mappetype);
    formater_ny_knapp($foreldreId, "mappe", "open_new_folder", $mappetype);
    formater_ny_knapp($foreldreId, "filer", "open_new_files", $mappetype);
    echo "</section>";

    formater_endre_mappe($foreldremappe, $noteinfo);
    formater_legg_til_ny_mappe($foreldreId, $mappetype);
    formater_legg_til_nye_filer($foreldreId, $mappetype);
}
formater_sokeboks($mappetype);

formater_noteinfo($noteinfo);

echo "<section class='mapper'>";

if ($sokemodus) {
    $mapper = sok_mapper($sokestreng, $mappetype);
    $filer = sok_filer($sokestreng, $mappetype);
} else {
    $mapper = hent_undermapper($foreldreId, $mappetype);
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