<?php 
	
if(tilgang_endre() && has_get('arrid')){

	$arrid = get('arrid');

	$sql = "SELECT nyhetsid_konsert FROM konserter WHERE arrid_konsert=" . $arrid;
	$konserter = hent_og_putt_inn_i_array($sql);

	if(empty($konserter)) {
		$melding = "Fant ikke kobling til konsert for arrid: " . $arrid;
		logg("slett-konsert", $melding);
		die($melding);
	}
	$nyhetsid = $konserter['nyhetsid_konsert'];

	$sql_nyhet = "UPDATE nyheter SET aktiv=false WHERE nyhetsid = '" . $nyhetsid . "' LIMIT 1";
	mysql_query($sql_nyhet);

	$sql_arr = "UPDATE arrangement SET slettet=true WHERE arrid = '".$arrid."' LIMIT 1";
	mysql_query($sql_arr);
}
	
header('Location: ?side=aktiviteter/liste');
exit("Slettet konsert");
?>