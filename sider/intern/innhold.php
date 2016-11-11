<?php
include_once "../../db_config.php";
include_once "../../funksjoner.php";
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ( $tilkobling === false ){
    exit("tilkoblingsfeil");
}

if (tilgang_endre()) {
    if (!(isset($_POST["innhold"]) || isset($_POST["navn"]))) {
        logg("innhold", "Enten innhold eller navn er ikke satt under lagringen av innholdet på en side");
        die(json_encode(array("error" => "Det har oppstått en feil. Ta kontakt med webkom.")));
    }
    $navn = post("navn");
    $innhold = addslashes($_POST["innhold"]);
    $sql = "SELECT * FROM innhold WHERE navn='$navn'";
    $result = mysql_query($sql);
    if (!$result) sqlerror($sql);
    if (mysql_num_rows($result) == 0) {
        $sql = "INSERT INTO innhold (navn, tekst) VALUES ('$navn', '$innhold')";
        $result = mysql_query($sql);
        if (!$result) sqlerror($sql);
    } else {
        $sql = "UPDATE innhold SET tekst='$innhold' WHERE navn='$navn'";
        $result = mysql_query($sql);
        if (!$result) sqlerror($sql);
    }
    die(json_encode(array("status" => "success", "innhold" => $innhold)));
}
?>
