<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
include_once "../db_config.php";
include_once "../funksjoner.php";
$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

$sql="SELECT * FROM lenker WHERE type='dir' AND katalog=381;";
$notemapper=hent_og_putt_inn_i_array($sql, 'id');
foreach($notemapper as $notemappe){
	$command="mkdir '../noter/".$notemappe['tittel']."'";
	echo "<pre>".shell_exec($command)."</pre>";
};
?>