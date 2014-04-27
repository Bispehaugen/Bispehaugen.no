<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
include_once "../db_config.php";
include_once "../funksjoner.php";
$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

				$command="rm -rf ../noter/";
				echo "<pre>".shell_exec($command)."</pre>";
?>
