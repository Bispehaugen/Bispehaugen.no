<?php
	//funksjonalitet
	
	//henter ut alle komiteer
	$sql="SELECT komiteid, navn, mail_alias FROM komite ORDER BY posisjon";
	$komiteer=hent_og_putt_inn_i_array($sql,$id_verdi="komiteid");
	
	//henter ut info om medlemmer++ om valgte komité
	$sql="SELECT komite.komiteid, verv.komiteid, navn, vervid, verv.posisjon, komite.posisjon, tittel, medlemmer.medlemsid, 
	verv.medlemsid, epost, fnavn, enavn, foto  FROM komite, verv, medlemmer WHERE medlemmer.medlemsid=verv.medlemsid AND 
	komite.komiteid=verv.komiteid ORDER BY komite.posisjon, verv.posisjon";
    $valgtekomiteer=hent_og_putt_inn_i_array($sql,$id_verdi="vervid");
	
	$ovelse_utfylt = false;
	// Sjekker om du har sendt inn fraværsmelding
	if (has_post('ovelse') && post('ovelse') != "") {
		$ovelse = post('ovelse');
		$grunn = post('grunn');
		$bruker = innlogget_bruker();
		$navn = $bruker['fnavn']." ".$bruker['enavn'];
		$epost = $bruker['email'];
		
		$from = "From: Bispehaugen.no<ikke-svar@bispehaugen.no>";
		$to = 'nestleder@bispehaugen.no';
		$replyto = "Reply-To: $navn <$epost>";
		$realfrom_tmp = getenv("REMOTE_HOST") ? getenv("REMOTE_HOST") : getenv("REMOTE_ADDR");
		$realfrom = "Real-From: $realfrom_tmp";
		$subject = "Fraværsmelding - $navn";
		
		$header="$from\r\n"."$replyto\r\n"."$realfrom";
		
		if(!mail($to, $subject, $message, $header)) {
			$feil_under_sending_av_mail = true;
		}
		
		$ovelse_utfylt = true;
	}

	//det som skrives ut på side

	?>
	
<section class="praktisk">
	<h2>Praktisk</h2>

	<h3>Fravær</h3>
	<?php
	if (!$ovelse_utfylt) {
	?>
		<section class="halv">
		<p>
			Fravær meldes til nestleder på e-post eller sms. <br />
			Du kan også fylle ut skjemaet
		</p>
		</section>
		<section class="halv">
			<form method='post' action='?side=intern/organisasjon'>
				<input type='hidden' name='medlemsid' value='<?php echo $_SESSION['medlemsid']; ?>'>
				<p>
					<input type='text' name='ovelse' placeholder="Hvilken øvelse" />
				</p>
				<p>
					<textarea name='grunn' placeholder="Grunn"></textarea>
					<input class='submit right' type='submit' value='Send' />
				</p>
			</form>
		</section>
		
		<?php
	} else {
		if($feil_under_sending_av_mail) {
			?>
			<p>
				<b>Beklager</b>, men det har oppstått en feil under sending av epost til nestleder.
				<a href="mailto:nestleder@bispehaugen.no?subject=Fravær&body=Jeg, <?php echo $navn; ?>, melder fravær på øvelse: <?php echo $ovelse; ?>.%0AGrunn: <?php echo $grunn; ?>">Klikk her for å sende mail</a>. Takk :)
			</p>
			<?php
		} else {
			echo "<p><b>Takk for at du sa ifra!</b></p>";
		}
	}
	
	?>
	
	<h3>Permisjon</h3>
	<p>
		Søknad om permisjon sendes på e-post til styret. <br />
		Husk å oppgi periode og årsak til permisjonen.
	</p>
	
	<h3>Kakebaker</h3>
	<p>
		Det går på rundgang. Se på intern-hovedsiden når det er din tur eller trykk på en aktiviteten.
	</p>
	
	<section class="slagverk">
		<h3>Slagverksbæregrupper</h3>
		<p>
			Det går på rundgang. Se på intern-hovedsiden når det er din tur eller trykk på en aktiviteten.
		</p>
	
		<p>
	<?php
		if(has_get('slagverksgrupper')){
	?>
			<a href='?side=intern/organisasjon'>skjul grupper</a>
			<ul>
				<li>Gruppe 1</li>
				<li>Gruppe ......</li>
			</ul>
	<?php } else { ?>
		<a href='?side=intern/organisasjon&slagverksgrupper=1'>vis grupper</a>	
	<?php } ?>		
		</p>
	</section>
	
	

	<h3>Kontaktinformasjon</h3>
	<p>
		<b>Adresse:</b> <br />
		Bispehaugen Ungdomskorps<br>
		Postboks 9012<br>
		Rosenborg <br />
		7455 Trondheim
	</p>
	<p>
		<b>Organisasjonnummer:</b> 975.729.141
	</p>
	<p>
		<b>Kontonummer:</b> 4200 07 51280
	</p>
	
	<h3>Vedtektene til korpset:</h3>
	<p>
		<a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'>Lenke til vedtektene</a>
    </p>

	</section>

	<section class="styret-og-komiteer">
		<h2>Styret og komiteer</h2>
		<p><a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'>Instruksen for komitéer</a></p>
<?php
			//skriver ut alle komiteene med link til komitevisning
			foreach($komiteer as $komite){
				if (empty($komite) || empty($komite["navn"])) {
					continue;
				}
				echo "<section class='komite'>";

				echo "<h3><a href='mailto:".$komite['mail_alias']."'>".$komite['navn']." <i class='fa fa-envelope-o'></i></a></h3>";
				echo "<ul>";
				foreach($valgtekomiteer as $valgtekomite){
					if ($valgtekomite["komiteid"] == $komite["komiteid"]) {
						echo"<li>".brukerlenke($valgtekomite, Navnlengde::FulltNavn)."</li>";
					}
				}
				echo "</ul>";

				echo "</section>";

			}
			
	echo "</section>";