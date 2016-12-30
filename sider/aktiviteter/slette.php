<?php 
global $dbh;

if(tilgang_endre() && has_get('id')){

	$id = get('id');

	$sql = "SELECT id AS antall FROM konserter WHERE arrid_konsert=? LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));

	if($stmt->rowCount() > 0) {
		header('Location: ?side=konsert/slette&arrid='.$id);
		die();
	}

	$sql = "UPDATE arrangement SET slettet=true WHERE arrid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));

}
header('Location: ?side=aktiviteter/liste');
exit();
