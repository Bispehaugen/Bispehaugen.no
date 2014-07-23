<?php

setlocale(LC_TIME, "nb_NO.utf8");
include_once "db_config.php";
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

if(has_get('side')){
	$side = get('side');
} else {
	die("Feil bruk");
}

echo inkluder_side_fra_undermappe($side, "sider");