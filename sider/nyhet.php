<?php
setlocale(LC_TIME, "norwegian"); 	
// Vis enkelnyhet
if( has('id') ){

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
	
	<h3><?php echo $nyhet['overskrift']; ?></h3>
	<?php echo"Skrevet den ".$nyhet['tid']." av ".$nyhet['skrevetav']; ?>
		
	<div class="ingressbilde"><?php if( !empty($bilde) ){ 
		echo "<img src='".$nyhet['bilde']."' />";
	};
	?></div>
	<p><b><?php echo nl2br($nyhet['ingress']); ?></b></p>
	<p><?php echo nl2br($nyhet['hoveddel']); ?></p>
	
	
	<?php
	}
} else {
	// Vis nyhetsoversikt
	$nyheter = hent_siste_nyheter(30, "Public");
	
	?>
	
	<h3>Nyheter:</h3>
	<table class='news_list'>
	<?php
		foreach($nyheter as $nyhet){
			
			$bilde = $nyhet['bilde'];
			if(empty($bilde)){
				$bilde = "bilder/forside/logo.png";
			}
			
			echo "<tr>
					<td class='news_list'>
						<img class='news_list' src='".$bilde."' />
					</td>
					<td class='news_list'>
						<a href='?side=nyhet&id=".$nyhet['nyhetsid']."'>".$nyhet['overskrift']."</a>
					</td>
				  </tr>";
		}
	?>	
	</table>
	<?php
}
