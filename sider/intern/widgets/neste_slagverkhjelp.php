<?php
	// Neste slagverkhjelp
	$slagverkhjelp_varsle_n_dager_for = 6;

	$neste_slagverkhjelp = neste_slagverkhjelp();

	if (!empty($neste_slagverkhjelp)) {
	$neste_slagverkhjelp_tid = strtotime($neste_slagverkhjelp['dato']." ".$neste_slagverkhjelp['oppmoetetid']);
	$neste_slagverkhjelp_markert = $neste_slagverkhjelp_tid - (86400 * $neste_slagverkhjelp_varsle_n_dager_for) < time();
?>
<section class="widget neste-slagverkhjelp<?php if($neste_slagverkhjelp_markert) echo " markert" ?>">
	
	<a href='?side=aktiviteter/vis&arrid=<?php echo $neste_slagverkhjelp["arrid"]; ?>'>
		<h3>
				<i class="fa fa-chevron-right"></i>Din neste slagverkbæring
		</h3>
		<p><i class="fa fa-bookmark fa-fw"></i><?php echo "Gruppe ".$neste_slagverkhjelp['slagverk']." - " . $neste_slagverkhjelp['tittel']; ?></p>
	</a>
	
	<p><i class="fa fa-calendar-o fa-fw"></i>
		<?php echo strftime("%A", $neste_slagverkhjelp_tid); ?>
		<?php echo date("d.", $neste_slagverkhjelp_tid); ?>
		<?php echo strftime("%B", $neste_slagverkhjelp_tid); ?>,
		kl. <?php echo date("H:i", $neste_slagverkhjelp_tid); ?>
	</p>
	<?php if (!empty($neste_slagverkhjelp_tid["sted"])) { ?>
	<p>
		<a href="https://maps.google.com/maps?q=<?php echo $neste_slagverkhjelp_tid["sted"]; ?>">
			<i class="fa fa-location-arrow fa-fw"></i><?php echo $neste_slagverkhjelp_tid["sted"]; ?>
		</a>
	</p>
	<?php } ?>

	<p><a href="?side=intern/slagverkhjelp/liste">Vis slagverkbærehjelplisten</a></p>
</section>

<?php
}