<?php
	include_once "sider/forum/funksjoner.php";
	include_once "sider/intern/funksjoner.php";

?>
<section class="internside">
	<h1>Internsiden</h1>

	<?php
		include_once "widgets/profil.php";

		include_once "widgets/neste_ovelse.php";

		include_once "widgets/neste_konsert.php";

		include_once "widgets/neste_kakebaking.php";

		include_once "widgets/neste_slagverkhjelp.php";
	?>

	<h2>Praktiske lenker</h2>
	<section>
	<h3><a href="?side=intern/kakeliste">Kakebakerliste</a></h3>
	<h3><a href="?side=intern/slagverkhjelp/liste">Slagverkbæreliste</a></h3>
	
	<?php
		$sisteFeilmeldinger = $sisteSqlFeil = siste_sql_feil();
		$antallFeil = 0;
		
		foreach($sisteFeilmeldinger as $feil) {
			$antallFeil += $feil['telling'];
		}
		
		if (session("rettigheter") >= 3) {
	?>
	<section class="widget neste-konsert<?php if($neste_konsert_markert) echo " markert" ?>">
		
		<h3>
			<a href='?side=feilmeldinger'>
				<?php echo $antallFeil; ?> Feilmeldinger
			</a>
		</h3>
	</section>
	<?php } ?>
	
	</section>
</section>
</section>

<section class="side">
	<section class="nyheter siste-nyheter halv-side-ved-stor-skjerm">
		<?php
		global $antall_nyheter;
			$antall_nyheter = 3;
			inkluder_side_fra_undermappe("nyheter/liste");
		?>
	</section>

	<section class="siste-forum forum halv-side-ved-stor-skjerm">
		
		<h2>Siste på forumet</h2>
		<h3><?php echo list_forum(); ?></h3>
		<div class="clearfix"></div>
		<?php
		$sql = siste_forumposter_sql(3);

		forum_innlegg_liste($sql, "forum-innlegg-liste siste-poster siste-poster-intern");
		?>

	</section>
	
	<div class="clearfix"></div>
</section>
<section class="side kontakt" data-scroll-index='8' data-scroll-url="?side=annet">
	<div class="content">
	<?php
		inkluder_side_fra_undermappe("kontakt");
	?>
	</div>
</section>
