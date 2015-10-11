<?php 
	

if(tilgang_endre() && has_get('id')){

	$id = get('id');

	$sql = "SELECT COUNT(id) AS antall FROM konserter WHERE arrid_konsert=" . $id . " LIMIT 1";
	$konserter = hent_og_putt_inn_i_array($sql);

	if($konserter['antall'] > 0) {
		header('Location: ?side=konsert/slette&arrid='.$id);
		die();
	}

	$sql = "UPDATE arrangement SET slettet=true WHERE arrid = '".$id."';";
	mysql_query($sql);

}
header('Location: ?side=aktiviteter/liste');
exit();