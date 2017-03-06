<?php

include_once "sider/dokumenter/funksjoner.php";
include_once "sider/aktiviteter/funksjoner.php";

if(!has_get('arrid')){	
	header('Location: ?side=ikke_funnet');
	die();
}

$arrid=get('arrid');
$arrangement = hent_aktivitet($arrid);

if(
	($arrangement['public'] != 1 && !er_logget_inn()) ||
	($arrangement['public'] == 2 && !tilgang_full())
	){	
	header('Location: ?side=ikke_funnet');
	die();
}

if (!empty($arrangement['kakebakere'])) {
	$kakebakere = $arrangement['kakebakere'];
}

$ingress = $arrangement['ingress'];
$beskrivelsesdok = $arrangement['beskrivelsesdok'];

$oppmøteDatoTid = (isset($arrangement['oppmoetetid']) ? substr($arrangement['start'], 0, -8).$arrangement['oppmoetetid'] : $arrangement['start']);

?>


<section class="informasjonslinje">
	<h2 class="back-link"><a href="?side=aktiviteter/liste" title="Flere aktiviteter"><i class="fa fa-chevron-left"></i>Aktiviteter</a></h2>

	<?php

	if(er_logget_inn() && tilgang_endre()){
    	echo"<p><div class='verktoy'><a href='?side=aktiviteter/endre&id=".$arrid."'><i class='fa fa-edit' title='Klikk for å endre'></i>Endre</a></div></p>";
  	}
  	?>
</section>
		<article class="aktivitet vis-aktivitet">
		<?php
			if ($arrangement['slettet'] == 1) {
				echo "<div class='slettet'>NB! Denne aktiviteten har blitt slettet!</div>";
			}
		?>
			<aside class="sidebar-info">
				<?php echo fancyDato($oppmøteDatoTid, true); ?>

				<?php
				if (isset($arrangement['sted']) && !empty($arrangement['sted'])) {
					echo '<section class="sted">
							<p><b>Sted:</b> <a href="http://maps.google.com/maps?q='.$arrangement['sted'].'">'.$arrangement['sted'].' <i class="fa fa-map-marker"></i></a></p>
						</section>';
				}
				?>
			</aside>

		<h1><?php echo $arrangement['tittel']; ?></h1>
		
		<?php
		if(isset($ingress)) { 
			echo "<p class='ingress'>" . nl2br($ingress) . "</p>";
		}
		?>

		<?php
		if(isset($beskrivelsesdok)) { 
			echo "<p class='hoved'>" . nl2br($beskrivelsesdok) . "</p>";
		}
		?>

		<section class="info">

		<?php if(isset($oppmøteDatoTid) || isset($arrangement['start']) || isset($arrangement['slutt'])) { ?>
			<h2>Tidspunkter</h2>
			<?php
			if (isset($oppmøteDatoTid)) {
				echo "<p><b>Oppmøte:</b> " . formater_dato_tidspunkt($oppmøteDatoTid) . "</p>";
			}
			if (isset($arrangement['start'])) {
				echo "<p><b>Start:</b> " . formater_dato_tidspunkt($arrangement['start']) . "</p>";
			}
			if (isset($arrangement['slutt'])) {
				echo "<p><b>Slutt:</b> " . formater_dato_tidspunkt($arrangement['slutt']) . "</p>";
			}
		}

		if(er_logget_inn()) {
			if(!empty($arrangement['hjelpere']) || !empty($kakebakere)) {
				echo "<h2>Hjelpere</h2>";
				if (isset($arrangement['hjelpere']) && !empty($arrangement['hjelpere'])) {
					echo "<p><b>Slagværkbæring:</b> " . $arrangement['hjelpere'] . "</p>";
				}
				if (isset($kakebakere)) {
                    echo '<p><b>Kakebaker';
                    if (count($kakebakere) > 1) {
                        echo 'e';
                    }
                    echo ':</b> ';

                    $bakere = "";
                    foreach ($kakebakere as $kakebaker) {
                        if (!empty($bakere)) {
                            $bakere .= ', ';
                        }
                        $bakere .= brukerlenke($kakebaker, Navnlengde::FulltNavn, false);
                    }
                    echo $bakere . '</p>';
				}
			}
		}
		?>
		</section>


<!--
		<section class='notevelger'>
			<h1>Tilhørende noter</h1>

			--Legg til setter opp en session, videresender til noteliste, så får man opp "velg" istede enn "slett". Kan kan ikke gjøre annet å klikke seg rundt og søke. Man kan heller ikke se filer, bare mapper.

			Søk via select2!

			List ut alle mapper med arr id
		</section>
-->
	</article>
