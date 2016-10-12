<?php 
	include_once "funksjoner.php";
    include_once "db_config.php";

    $tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);
    if ($tilkobling === false) {
        exit ;
    }

	logg_ut();
	header('Location: index.php');
	die();
