<?php
include_once "db_config.php";
$dbh;
try {
    $dbh = new PDO("mysql:host={$database_host};dbname={$database_database};charset=utf8",
        $database_user, $database_string, array(
            PDO::ATTR_PERSISTENT => true
    ));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    if ($er_produksjon) {
        echo "Kunne ikke koble opp mot database.<br>Prøv igjen senere...";
    } else {
        die($e->getMessage());
    }
}
?>
