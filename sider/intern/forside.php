<?php
	include_once "sider/forum/funksjoner.php";

?>
<section class="internside">
	<h1>Internsiden</h1>


	<?php
		$b = innlogget_bruker();

	?>
	<section class="widget profil">
		<h3><?php echo brukerlenke($b, Navnlengde::FulltNavn, true); ?></h3>
		<p><?php echo $b["adresse"]; ?></p>
		<p><?php echo $b["email"]; ?></p>
		<p><?php echo $b["tlfmobil"]; ?></p>
		<p class="endre right"><a href="?side=medlem/endre&id=<?php echo $b['medlemsid']; ?>"><i class="fa fa-edit"></i> Endre</a></p>
	</section>


	<?php
		// Neste øvelse
		$neste_ovelse = neste_ovelse();
		$neste_ovelse_tid = strtotime($neste_ovelse['dato']." ".$neste_ovelse['oppmoetetid']);
		$neste_ovelse_varsle_n_dager_for = 3;
		$neste_ovelse_markert = $neste_ovelse_tid - (86400 * $neste_ovelse_varsle_n_dager_for) < time();
		$neste_ovelse_har_noter = antall_noter($neste_ovelse["arrid"]) > 0;
	?>
	<section class="widget neste-ovelse<?php if($neste_ovelse_markert) echo " markert" ?>">
		<h3>
			<a href='?side=aktiviteter/liste&id=<?php echo $neste_ovelse["arrid"]; ?>'>
				<i class="fa fa-chevron-right"></i> Neste øvelse?
			</a>
		</h3>
		<p><i class="fa fa-calendar-o fa-fw"></i>
			<?php echo strftime("%A", $neste_ovelse_tid); ?>
			<?php echo date("d.", $neste_ovelse_tid); ?>
			<?php echo strftime("%B", $neste_ovelse_tid); ?>
			, kl. <?php echo date("H:i", $neste_ovelse_tid); ?>
		</p>
		<p>
			<a href="https://maps.google.com/maps?q=<?php echo $neste_ovelse["sted"]; ?>">
				<i class="fa fa-location-arrow fa-fw"></i> <?php echo $neste_ovelse["sted"]; ?>
			</a>
		</p>
		<?php if ($neste_ovelse_har_noter) { ?>
		<p>
			<a href="?side=noter/noter_oversikt&arrid=<?php echo $neste_ovelse["arrid"]; ?>">
				<i class="fa fa-files-o fa-fw"></i> Noter
			</a>
		</p>
		<?php } ?>
	</section>

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
			<a href='?side=aktiviteter/liste&id=<?php echo $neste_konsert["arrid"]; ?>'>
				<i class="fa fa-chevron-right"></i> Neste Konsert?
			</a>
		</h3>
		<p><i class="fa fa-calendar-o fa-fw"></i>
			<?php echo strftime("%A", $neste_konsert_tid); ?>
			<?php echo date("d.", $neste_konsert_tid); ?>
			<?php echo strftime("%B", $neste_konsert_tid); ?>
			, kl. <?php echo date("H:i", $neste_konsert_tid); ?></p>
		<p>
			<a href="https://maps.google.com/maps?q=<?php echo $neste_konsert["sted"]; ?>">
				<i class="fa fa-location-arrow fa-fw"></i> <?php echo $neste_konsert["sted"]; ?>
			</a>
		</p>
		
		<?php if ($neste_konsert_har_noter) { ?>
		<p>
			<a href="?side=noter/noter_oversikt&arrid=<?php echo $neste_konsert["arrid"]; ?>">
				<i class="fa fa-files-o fa-fw"></i> Noter
			</a>
		</p>
		<?php } ?>
	</section>


	<h2>Mer som kommer:</h2>
	<section>
	<h3>Bake kake?</h3>
		<!--<p>Du skal ha med kake den 4. september kl. 19. Det er bare 15 dager til!</p> //-->
	<h3>Bære slagværk?</h3>
	<h3>Kjøre med henger?</h3>
	</section>
</section>
</section>

	<section class="side siste-forum forum">
		
		<h2 class='overskrift-som-er-inline-block'>Siste på forumet</h2>
		<h3 class='lenke-som-er-inline-med-overskrift'><?php echo list_forum(); ?></h3>
		
		<?php
		$sql = siste_forumposter_sql(3);

		forum_innlegg_liste($sql, "forum-innlegg-liste siste-poster siste-poster-intern");
		?>

	</section>
	
	<section class="side nyheter">
		<?php
		global $antall_nyheter;
			$antall_nyheter = 3;
			inkluder_side_fra_undermappe("nyheter/liste");
		?>
	<div class="clearfix"></div>

