<?php
setlocale(LC_TIME, "norwegian"); 	
// Vis enkelnyhet
if(has_get('id') ){

	$id = get('id');
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, skrevetavid FROM `nyheter` WHERE nyhetsid=".$id;
	
    // If not signed in, add news restrictions
	if(er_logget_inn() === false){
		$sql .= " AND type='Public' ";
	}
	
	$nyhet = hent_og_putt_inn_i_array($sql);
	$skrevet_av_id = isset($nyhet['skrevetavid']) ? $nyhet['skrevetavid'] : "";
	$skrevet_av = hent_brukerdata($skrevet_av_id);
	
	if (empty($nyhet)) {
		echo "Du må logge inn for å lese denne nyheten :)";
	} else {
	
	$bilde = isset($nyhet['bilde']) ? $nyhet['bilde'] : "";
	?>
	
	<section class="informasjonslinje">
		<h2 class="back-link"><a href="?side=nyheter/liste" title="Les flere nyheter"><i class="fa fa-chevron-left"></i> Nyheter</a></h2>
		<?php echo brukerlenke($skrevet_av, false, true, "<time>".$nyhet['tid']."</time>"); ?>
	</section>
	
	<article class="nyhet">
		<?php echo nyhetsdato($nyhet['tid']); ?>
		
		<?php if (!empty($bilde)) { ?>
		<div class="ingressbilde"><img src='<?php echo $bilde; ?>' /></div>
		<?php } ?>
		
		<h1><?php echo $nyhet['overskrift']; ?></h1>
		
		<p><b><?php echo nl2br($nyhet['ingress']); ?></b></p>
		<p><?php echo nl2br($nyhet['hoveddel']); ?></p>
	
	</article>
	<div class="clearfix"></div>
	<?php
	}
} else {
	inkluder_side_fra_undermappe("ikke_funnet");
}
