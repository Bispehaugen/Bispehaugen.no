<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
global $dbh;

date_default_timezone_set('Europe/Oslo');
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = str_replace("skript", "", dirname(__FILE__));

include_once $root."lokal_config.php";
include_once $root.'funksjoner.php';
include_once $root."/sider/intern/slagverkhjelp/funksjoner.php";

$_SESSION["medlemsid"] = -1; // Logget inn som bot

$sekunder_i_ett_dogn = 86400; //24*60*60;

$om_4_dager = date('Y-m-d', time() + (4*$sekunder_i_ett_dogn)) . " 23:59:59";
$neste_slagverk_sql = "SELECT * FROM arrangement WHERE slettet=0 and dato > NOW() and dato < ? ORDER BY dato LIMIT 1";
$stmt = $dbh->prepare($neste_slagverk_sql);
$stmt->execute(array($om_4_dager));
$arrangement = $stmt->fetch();

// Sjekk om arrangement allerede er varslet
$allerede_varslet_sql = "SELECT COUNT(id) as antall FROM varsling WHERE arrid = ? AND type = ?";
$stmt = $dbh->prepare($allerede_varslet_sql);
$stmt->execute(array($arrangement['arrid'], Varslingstype::Slagverkhjelper));
$allerede_varlset = $stmt->fetch();
$stmt->closeCursor();

if ($allerede_varlset['antall'] == 0 && !empty($arrangement)) {
    $gruppeId = $arrangement['slagverk'];
    
    if (empty($gruppeId)) {
        logg("slagverk-epost-feil", "Ingen gruppeId satt for arrid: ".$arrangement['arrid']);
        die();
    }

    $grupper = hent_slagverkhjelp($gruppeId);

    if (empty($grupper)) {
        logg("slagverk-epost-feil", "Kunne ikke finne noen medlemmer for gruppe ".$gruppeId. " arrid: ".$arrangement['arrid']);
        die();
    }

    $brukere = $grupper[$gruppeId];
    
    $tid = strtotime($arrangement['dato'] . " " . $arrangement['oppmoetetid']);

    $antall_dager = round(($tid - time()) / $sekunder_i_ett_dogn);

    $replyto = "styret@bispehaugen.no";
    $subject = "Slagverkbæring - Bispehaugen.no";

    foreach ($brukere as $bruker) {
        $to = $bruker['email'];
        $message = "
    Hei " . $bruker['fnavn'] . "!

    Du er en av de som skal bære slagverk om " . $antall_dager ." dager for \"" . $arrangement['tittel'] . "\", kl. " . date("H:i d.m.Y", $tid) . ".
    Husk å møte minst 15 minutter tidligere enn oppmøte for å få bært opp slagverket.
    Husk også at du skal bære det ned etterpå!

    Hvis det ikke passer må du selv sørge for å finne en stedfortreder.

    Se slagverkbæregruppen din på http://bispehaugen.no/?side=intern/slagverkhjelp/liste

    Les mer om aktiviteten på http://bispehaugen.no/?side=aktiviteter/vis&arrid=".$arrangement['arrid']."

    Med vennlig hilsen
    Styret";

        if(epost($to, $replyto, $subject, $message)) {
            $sql_varling = "INSERT INTO varsling (arrid, type, medlemsid, tid) VALUES (?, ?, ?, ?)";

            $stmt = $dbh->prepare($sql_varling);
            $stmt->execute(array($arrangement["arrid"], Varslingstype::Slagverkhjelper, $bruker["medlemsid"], date("Y-m-d H:i:s")));
        }
    }
}
