<?php
include_once "../../db_config.php";
include_once "../../funksjoner.php";
include_once "funksjoner.php";

global $dbh;

if (tilgang_full()) {
    if (!(isset($_POST["innhold"]) || isset($_POST["navn"]))) {
        logg("innhold", "Enten innhold eller navn er ikke satt under lagringen av innholdet på en side");
        die(json_encode(array("error" => "Det har oppstått en feil. Ta kontakt med webkom.")));
    }
    $navn = post("navn");
    $innhold = addslashes($_POST["innhold"]);
    $sql = "SELECT * FROM innhold WHERE navn=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($navn));
    if ($stmt->rowCount() == 0) {
        $sql = "INSERT INTO innhold (navn, tekst) VALUES (?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($navn, $innhold));
    } else {
        $sql = "UPDATE innhold SET tekst='$innhold' WHERE navn=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($navn));
    }
    die(json_encode(array("status" => "success", "innhold" => $innhold)));
}
?>
