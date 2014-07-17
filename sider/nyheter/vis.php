<?php
setlocale(LC_TIME, "norwegian"); 	
// Vis enkelnyhet
if(has_get('id') ){

	$id = get('id');
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE nyhetsid=".$id;
	
    // If not signed in, add news restrictions
	if(er_logget_inn() === false){
		$sql .= " AND type='Public' ";
	}
	
	$nyhet = hent_og_putt_inn_i_array($sql);
	
	if (empty($nyhet)) {
		echo "Du må logge inn for å lese denne nyheten :)";
	} else {
	
	$bilde = $nyhet['bilde'];
	?>
	
	<article class="nyhet">
	
	<h1><?php echo $nyhet['overskrift']; ?></h1>
	<?php echo"<p>".$nyhet['skrevetav']." - ".$nyhet['tid']."</p>"; ?>
		
	<div class="ingressbilde"><?php if( !empty($bilde) ){ 
		echo "<img src='".$nyhet['bilde']."' />";
	};
	?></div>
	<p><b><?php echo nl2br($nyhet['ingress']); ?></b></p>
	<p><?php echo nl2br($nyhet['hoveddel']); ?></p>
	
	</article>
	<?php
	}
} else {
	inkluder_side_fra_undermappe("ikke_funnet");
}
