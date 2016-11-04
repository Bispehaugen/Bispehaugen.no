<?php
include_once "../../db_config.php";
include_once "funksjoner.php";
include_once "../../funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ( $tilkobling === false ){
    exit("tilkoblingsfeil");
}

if (tilgang_endre()) {
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
}
?>
