<?php
global $dbh;

include_once("sider/dokumenter/funksjoner.php");

if(!has_post('navn') || !has_post('mappeid') || !has_post('mappetype')) {
    die("Kan ikke endre mappe uten innsendt navn, mappetype eller mappeid");
}

if(!er_logget_inn()) {
    die("Du må være logget inn");
}
if (!tilgang_endre()) {
    die("Du har ikke lov til å endre mapper!");
}

$navn = post('navn');
$mappeid = post('mappeid');
$mappetype = intval(post('mappetype'));

// Endre mappe i sql
$sql = "UPDATE mapper SET tittel = ? WHERE id = ? LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($navn, $mappeid));


if (has_post('noteid')) {
    $noteid = post('noteid');
    $arrangor = post('arrangor');
    $komponist = post('komponist');
    $arkivnr = post('arkivnr');
    $besetningsid = post('besetningsid');

    $sql_update_notesett = "UPDATE noter_notesett SET komponist = ?, arrangor = ?, arkivnr = ?, besetningsid = ? WHERE noteid = ? AND mappeid = ?";
    $stmt = $dbh->prepare($sql_update_notesett);
    $stmt->execute(array($komponist, $arrangor, $arkivnr, $besetningsid, $noteid, $mappeid));

}

echo "
<script type='text/javascript'>
    window.location = '?side=dokumenter/liste&mappe=" . $mappeid . "&type=" . $mappetype . "';
</script>";
