<?php
	// Neste konsert
	$neste_konsert = neste_konsert_arrangement();
	$neste_konsert_tid = strtotime($neste_konsert['dato']." ".$neste_konsert['oppmoetetid']);
	$neste_konsert_varsle_n_dager_for = 14;
	$neste_konsert_markert = $neste_konsert_tid - (86400 * $neste_konsert_varsle_n_dager_for) < time();
	$neste_konsert_har_noter = antall_noter($neste_konsert["arrid"]) > 0;
?>
<section class="widget neste-konsert<?php if($neste_konsert_markert) echo " markert" ?>">
	
	<h3>
		<a href='?side=aktiviteter/vis&arrid=<?php echo $neste_konsert["arrid"]; ?>'>
			<i class="fa fa-chevron-right"></i><?php echo $neste_konsert['tittel']; ?>
		</a>
	</h3>
	<p><i class="fa fa-calendar-o fa-fw"></i>
		<?php echo strftime("%A", $neste_konsert_tid); ?>
		<?php echo date("d.", $neste_konsert_tid); ?>
		<?php echo strftime("%B", $neste_konsert_tid); ?>
		, kl. <?php echo date("H:i", $neste_konsert_tid); ?></p>
	<p>
		<a href="https://maps.google.com/maps?q=<?php echo $neste_konsert["sted"]; ?>">
			<i class="fa fa-location-arrow fa-fw"></i><?php echo $neste_konsert["sted"]; ?>
		</a>
	</p>
	
	<?php if ($neste_konsert_har_noter) { ?>
	<p>
		<a href="?side=noter/noter_oversikt&arrid=<?php echo $neste_konsert["arrid"]; ?>">
			<i class="fa fa-files-o fa-fw"></i>Noter
		</a>
	</p>
	<?php } ?>
</section>