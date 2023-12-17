<?php
setlocale(LC_TIME, "nb_NO.utf8");
global $dbh;
// Vis enkelnyhet
if(!has_get('id') ){
    throw new Exception();  
}

$id = get('id');
$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, skrevetavid FROM `nyheter` WHERE nyhetsid=?";

// If not signed in, add news restrictions
if(er_logget_inn() === false){
    $sql .= " AND type='Public' ";
}

$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));

$nyhet = $stmt->fetch();

if ($nyhet['type'] == "nestekonsert") {
    // Konsert, redirecte til konsert/vis
    header('Location: ?side=konsert/vis&id='.$id);
    die();
}

$skrevet_av_id = isset($nyhet['skrevetavid']) ? $nyhet['skrevetavid'] : "";
$skrevet_av = hent_brukerdata($skrevet_av_id);

if (empty($nyhet)) {
    echo "Du må logge inn for å lese denne nyheten :)";
} else {

$bilde = isset($nyhet['bilde']) ? $nyhet['bilde'] : "";
?>

<section class="informasjonslinje">
    <h2 class="back-link"><a href="?side=nyheter/liste" title="Les flere nyheter"><i class="fa fa-chevron-left"></i>Nyheter</a></h2>
    
    <?php

    if(er_logget_inn() && tilgang_endre()){
        echo"<p><div class='verktoy'><a href='?side=nyheter/endre&id=".$id."'><i class='fa fa-edit' title='Klikk for å endre'></i>Endre</a></div></p>";
    }
    echo brukerlenke($skrevet_av, Navnlengde::Fornavn, false, "<span><time datetime='".$nyhet['tid']."'>kl. ".date("H:i d.m.Y", strtotime($nyhet['tid']))."</time> av ");  

    ?>
</section>

<article class="nyhet">
    <aside class="sidebar-info">
    <?php echo fancyDato($nyhet['tid']); ?>
    </aside>
    
    <?php if (!empty($bilde)) { ?>
    <div class="ingressbilde"><img src='<?php echo thumb($bilde, 400, 400); ?>' /></div>
    <?php } ?>
    
    <h1><?php echo $nyhet['overskrift']; ?></h1>
    
    <p><b><?php echo nl2br($nyhet['ingress']); ?></b></p>
    <p><?php echo nl2br($nyhet['hoveddel']); ?></p>

</article>

<div class="clearfix"></div>
<?php
}
