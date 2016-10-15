<?php

$id = get('id');

if(has_get('arrid')) {
	$arrid = get('arrid');
	$nyhetsidSql = "SELECT nyhetsid_konsert FROM `konserter` WHERE arrid_konsert = ".$arrid." LIMIT 1";
	$id = hent_og_putt_inn_i_array($nyhetsidSql)['nyhetsid_konsert'];

	if (empty($id)) {
		header('Location: ?side=aktiviteter/vis&arrid='.$arrid);
		die();
	}
}

$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, skrevetavid, konsert_tid, normal_pris, student_pris, sted, aktiv FROM `nyheter` WHERE type='nestekonsert' AND nyhetsid = ".$id." LIMIT 1";

$konsert = hent_og_putt_inn_i_array($sql);

$skrevet_av_id = isset($konsert['skrevetavid']) ? $konsert['skrevetavid'] : "";
$skrevet_av = hent_brukerdata($skrevet_av_id);
$bilde = isset($konsert['bilde']) ? $konsert['bilde'] : "";

?>


<section class="informasjonslinje">
		<h2 class="back-link"><a href="?side=aktiviteter/liste" title="Flere aktiviteter"><i class="fa fa-chevron-left"></i> Aktiviteter</a></h2>

		<?php

		if(er_logget_inn() && tilgang_endre()){
        	echo"<p><div class='verktoy'><a href='?side=konsert/endre&id=".$konsert['nyhetsid']."'><i class='fa fa-edit' title='Klikk for å endre'></i>Endre</a></div></p>";
      	}
		echo brukerlenke($skrevet_av, Navnlengde::Fornavn, false, "<span><time datetime='".$$konsert['tid']."'>kl. ".date("H:i d.m.Y", strtotime($konsert['tid']))."</time> av ");  
	
      	?>
	</section>
	

		<article class="konsert vis-konsert">
		<?php
			if ($konsert['aktiv'] == 0) {
				echo "<div class='slettet'>NB! Denne aktiviteten har blitt slettet!</div>";
			}
		?>
			<aside class="sidebar-info">
                <?php if ($id == 1490) {?>
        <time class="fancy-date" datetime="2016-11-05T15:00:00+01:00" title="lø. 05. nov. 2016 kl. 15.00 +0100">
            <div class="boks">
                <div class="weekday">lø.</div>
                <div class="day">5.</div>
                <div class="month">nov.</div>
                <div class="year">2016</div>
            </div>
            <div class="time boks">kl. 15:00</div>
            <div class="time boks">kl. 18:00</div>
        </time>
                <?php } else { echo fancyDato($konsert['konsert_tid'], true); }?>

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
		<div class="ingressbilde"><img src='<?php echo thumb($bilde, 400, 400); ?>' /></div>
		<?php } ?>
		
		<h1><?php echo $konsert['overskrift']; ?></h1>
		
		<p><b><?php echo nl2br($konsert['ingress']); ?></b></p>

		<p><?php echo nl2br($konsert['hoveddel']); ?></p>
	
	</article>
