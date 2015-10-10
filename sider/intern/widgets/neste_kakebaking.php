<?php
	// Neste kakebaking
	$neste_kakebaking_varsle_n_dager_for = 6;

	$neste_kakebaking = neste_kakebaking();

	if (!empty($neste_kakebaking)) {
	$neste_kakebaking_tid = strtotime($neste_kakebaking['dato']." ".$neste_kakebaking['oppmoetetid']);
	$neste_kakebaking_markert = $neste_kakebaking_tid - (86400 * $neste_kakebaking_varsle_n_dager_for) < time();
?>
<section class="widget neste-kakebaking<?php if($neste_kakebaking_markert) echo " markert" ?>">
	
	<a href='?side=aktiviteter/vis&arrid=<?php echo $neste_kakebaking["arrid"]; ?>'>
		<h3>
				<i class="fa fa-chevron-right"></i>Din neste kakebaking
		</h3>
		<p><i class="fa fa-bookmark fa-fw"></i><?php echo $neste_kakebaking['tittel']; ?></p>
	</a>
	
	<p><i class="fa fa-calendar-o fa-fw"></i>
		<?php echo strftime("%A", $neste_kakebaking_tid); ?>
		<?php echo date("d.", $neste_kakebaking_tid); ?>
		<?php echo strftime("%B", $neste_kakebaking_tid); ?>
		, kl. <?php echo date("H:i", $neste_kakebaking_tid); ?></p>
	<p>
		<a href="https://maps.google.com/maps?q=<?php echo $neste_kakebaking_tid["sted"]; ?>">
			<i class="fa fa-location-arrow fa-fw"></i><?php echo $neste_kakebaking_tid["sted"]; ?>
		</a>
	</p>
</section>

<?php
}