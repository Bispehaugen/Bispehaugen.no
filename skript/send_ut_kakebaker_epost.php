<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
global $dbh;

date_default_timezone_set('Europe/Oslo');
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = str_replace("skript", "", dirname(__FILE__));

include_once $root."lokal_config.php";
include_once $root.'funksjoner.php';

$_SESSION["medlemsid"] = -1; // Logget inn som bot

$sekunder_i_ett_dogn = 86400; //24*60*60;

$om_4_dager = date('Y-m-d', time() + (4*$sekunder_i_ett_dogn)) . " 23:59:59";
$neste_kakebaker_sql = "SELECT * FROM arrangement WHERE dato > NOW() and dato < ? ORDER BY dato";
$stmt = $dbh->prepare($neste_kakebaker_sql);
$stmt->execute(array($om_4_dager));

while ($arrangement = $stmt->fetch()) {
    // Sjekk om arrangement allerede er varslet
    $allerede_varslet_sql = "SELECT COUNT(id) as antall FROM varsling WHERE arrid = " . $arrangement['arrid'] . " AND type = " . Varslingstype::Kakebaker;
    $allerede_varlset = hent_og_putt_inn_i_array($allerede_varslet_sql);

    if ($allerede_varlset['antall'] == 0) {

        $bruker = hent_brukerdata($arrangement['kakebaker']);
        $tid = strtotime($arrangement['dato'] . " " . $arrangement['oppmoetetid']);

        $antall_dager = round(($tid - time()) / $sekunder_i_ett_dogn);

        $to = $bruker['email'];
        $replyto = "styret@bispehaugen.no";
        $subject = "Kakebaking - Bispehaugen.no";
        $message = "
    Hei " . $bruker['fnavn'] . "!

    Det er du som skal bake kake om " . $antall_dager ." dager for \"" . $arrangement['tittel'] . "\", kl. " . date("H:i d.m.Y", $tid) . ".
    Hvis det ikke passer må du selv sørge for å finne en stedfortreder.

    Les mer på http://bispehaugen.no/?side=aktiviteter/vis&arrid=".$arrangement['arrid']."

    Med vennlig hilsen
    Styret";

        if(epost($to, $replyto, $subject, $message)) {
            $sql_varling = "INSERT INTO varsling (arrid, type, medlemsid, tid) VALUES (?, ?, ?, ?)";
            $stmt = $dbh->prepare($sql_varling);
            $stmt->execute(array($arrangement["arrid"], Varslingstype::Kakebaker, $bruker["medlemsid"], date("Y-m-d H:i:s")));
        }
    }
}
