<?php
	// Neste kakebaking
	$neste_kakebaking = neste_kakebaking();
	$neste_kakebaking_tid = strtotime($neste_kakebaking['dato']." ".$neste_kakebaking['oppmoetetid']);

?>
<section class="widget neste-kakebaking">
	
	<a href='?side=aktiviteter/vis&amp;arrid=<?php echo $neste_kakebaking["arrid"]; ?>'>
		<h3>
				<i class="fa fa-chevron-right"></i>Din neste kakebaking
		</h3>
		<p><i class="fa fa-bookmark fa-fw"></i><?php echo $neste_kakebaking['tittel']; ?></p>
	</a>
	
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
</section>