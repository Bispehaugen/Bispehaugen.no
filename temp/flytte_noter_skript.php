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
$sql="SELECT * FROM lenker WHERE type='dir' AND katalog=381;";
$notemapper=hent_og_putt_inn_i_array($sql, 'id');
foreach($notemapper as $notemappe){
	#echo $notemappe['tittel'];
	$sql="SELECT tittel, id FROM lenker WHERE type='dir' AND katalog=".$notemappe['id'].";";
	$undermapper=hent_og_putt_inn_i_array($sql, 'id');
	$foldertittel=clean($notemappe['tittel']);
		foreach($undermapper as $undermappe){
			$sql="SELECT tittel, id FROM lenker WHERE type='dir' AND katalog=".$undermappe['id'].";";
			$underundermapper=hent_og_putt_inn_i_array($sql, 'id');
			$undertittel=clean($undermappe['tittel']);
			#echo "<pre>".shell_exec($command)."</pre>";

			foreach($underundermapper as $underundermappe){
				$tittel=clean($underundermappe['tittel']);
				$command="mkdir ".escapeshellarg("../noter/".$foldertittel."/".$undertittel."/".$tittel);
				#print_r($command);
				echo "<pre>".shell_exec($command)."</pre>";
			};
		};
};
?>
