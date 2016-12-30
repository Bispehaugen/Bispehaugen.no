<?php

function hent_nyhet($nyhetsid) {
    global $dbh;
	$sql="SELECT * FROM `nyheter` WHERE `nyhetsid`=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($nyhetsid));
    return $stmt->fetch();
}
