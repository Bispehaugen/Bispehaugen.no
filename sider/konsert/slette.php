<?php 
global $dbh;
    
if(tilgang_endre() && has_get('arrid')){

    $arrid = get('arrid');

    $sql = "SELECT nyhetsid_konsert FROM konserter WHERE arrid_konsert=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($arrid));

    if($stmt->rowCount() == 0) {
        $melding = "Fant ikke kobling til konsert for arrid: " . $arrid;
        logg("slett-konsert", $melding);
        die($melding);
    }
    $nyhetsid = $stmt->fetchColumn();

    $sql_nyhet = "UPDATE nyheter SET aktiv=false WHERE nyhetsid = ? LIMIT 1";
    $stmt = $dbh->prepare($sql_nyhet);
    $stmt->execute(array($nyhetsid));

    $sql_arr = "UPDATE arrangement SET slettet=true WHERE arrid = ? LIMIT 1";
    $stmt = $dbh->prepare($sql_arr);
    $stmt->execute(array($arrid));
}
    
header('Location: ?side=aktiviteter/liste');
exit("Slettet konsert");
?>
