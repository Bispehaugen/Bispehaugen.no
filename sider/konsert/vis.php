<?php

if(!has_get('id') || empty(get('id'))) {
	throw new Exception();	
}

$id = get('id');

$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, skrevetavid, konsert_tid, normal_pris, student_pris, sted FROM `nyheter` WHERE type='nestekonsert' AND nyhetsid = ".$id." LIMIT 1";

$konsert = hent_og_putt_inn_i_array($sql);

$skrevet_av_id = isset($konsert['skrevetavid']) ? $konsert['skrevetavid'] : "";
$skrevet_av = hent_brukerdata($skrevet_av_id);
$bilde = isset($konsert['bilde']) ? $konsert['bilde'] : "";

?>


<section class="informasjonslinje">
		<h2 class="back-link"><a href="?side=konsert/liste" title="Flere konserter"><i class="fa fa-chevron-left"></i> Konserter</a></h2>
		<?php echo brukerlenke($skrevet_av, Navnlengde::Fornavn, true); ?>
	</section>
	

		<article class="konsert vis-konsert">
			<aside class="sidebar-info">
				<?php echo fancyDato($konsert['konsert_tid'], true); ?>

				<?php
				if (isset($konsert['sted']) && !empty($konsert['sted'])) {
					echo '<section class="sted">
							<p><b>Sted:</b> <a href="http://maps.google.com/maps?q='.$konsert['sted'].'">'.$konsert['sted'].'</a></p>
						</section>';
				}
				?>

				<?php if(isset($konsert['normal_pris']) || isset($konsert['student_pris'])) { ?>
				<section class="pris">
					<h2>Pris</h2>
					<?php
						if (isset($konsert['normal_pris'])) {
							echo "<p><b>Ordinær:</b> " . $konsert['normal_pris'] . ",-</p>";
						}

						if (isset($konsert['student_pris'])) {
							echo '<p><b>Barn/student:</b> ' . $konsert['student_pris'] . ',-</p>';
						}
					?>
					<p><b>Skolekorpsmusikanter:</b> gratis (ved framvisning av gyldig NMF-bevis)</p>
				</section>
				<?php } ?>
			</aside>

		<?php if (!empty($bilde)) { ?>
		<div class="ingressbilde"><img src='<?php echo $bilde; ?>' /></div>
		<?php } ?>
		
		<h1><?php echo $konsert['overskrift']; ?></h1>
		
		<p><b><?php echo nl2br($konsert['ingress']); ?></b></p>

		<p><?php echo nl2br($konsert['hoveddel']); ?></p>
	
	</article>
