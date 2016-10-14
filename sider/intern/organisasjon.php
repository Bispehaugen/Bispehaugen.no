<?php
	//funksjonalitet
	
	//henter ut alle komiteer
	$komiteer = hent_komiteer();
	
	//henter ut info om medlemmer++ om valgte komité
    $valgtekomiteer = hent_styret();
	
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

		$message = "Fraværsmelding fra $navn\r\nØvelse: $ovelse\r\nGrunn:$grunn";
		
		$header="$from\r\n"."$replyto\r\n"."$realfrom";
		if(!epost($to, $replyto, $subject, $message, $header)) {
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
			Fravær skal meldes til besetningansvarlig i god tid på e-post <a href="mailto:besetningsansvarlig@bispehaugen.no?subject=Fravær">besetningsansvarlig@bispehaugen.no</a> eller sms 92829888. Sjekk aktivitetsplanen jevnlig og sørg for at du har gitt beskjed om du ikke kan møte slik at dirigent best mulig kan planlegge øvelsen.
			<!--Du kan også fylle ut skjemaet-->
		</p>
		</section>
		<!--<section class="halv">
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
		</section>-->
		
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
		Søknad om permisjon sendes på e-post til <a href="mailto:styret@bispehaugen.no">styret@bispehaugen.no</a>. Husk å oppgi periode og årsak til permisjonen.
	</p>
	
	<h3>Kakebaker</h3>
	<p>
		Korpsets medlemmer er glade i kake og hver øvelse er ett medlem ansvarlig for å ta med seg noe godt til øvelsen. Dette går på rundgang og du vil få automatisk påminnelse på e-post når det er din tur. Om du er nysgjerrig på når det er din tur, kan du ta en kikk på hovedsiden på internsiden, aktivitetsplanen eller <a href="?side=intern/kakeliste">her</a>. Dersom du ikke kan bake den dagen du er satt opp, er det bare å bytte med et annet medlem. Da blir alle glade og fornøyde.
	</p>
	
	<section class="slagverk">
		<h3>Slagverksbæregrupper</h3>
		<p>
			<b>- Hva er dette?</b>
		</p>
		<p>
			En slagverksbæregruppe bærer slagverket og det utstyret som skal brukes opp/ned fra kjelleren før og etter øvelsen. Dersom korpset skal øve et annet sted, spille konsert eller skal på oppdrag er en bæregruppe som er ansvarlig for å kjøre og bære slagverket  og utstyr som skal med
		</p>
		<p>
			Alle medlemmer av BUK er fordelt på seks bæregrupper som rullerer for hver aktivitet. Det er en eller to styremedlemmer på hver gruppe  og de har ansvaret for gruppa.
		</p>
		<p>
			<b>- Når skal jeg bære?</b>
		</p>
		<p>
			Du vil få en automatisk påminnelse på e-post når det nærmer seg bæring. Ansvarlig styremedlem vil også ta kontakt med deg om om oppmøtetidspunkt. Om du lurer på når du skal bære neste gang, kan du sjekke ut hovedsiden på internsiden eller aktivitetplanen for å finne ut hvilken gruppe som skal bære når. 
		</p>
		<p>
			<b>- Jeg er opptatt når det er min gruppe sin tur til å bære. hva gjør jeg?</b>
		</p>
		<p>
			Dersom du et aktivt medlem (altså ikke i permisjon), må du finne deg en vikar. Ved permisjon skal du ikke stå på bærgruppe. Gi beskjed til styret om du fått påminnelse om bæring og er i permisjon.
		</p>
	
		<p>
		<a href='?side=intern/slagverkhjelp/liste'>Vis slagverkbæregrupper</a>	
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

<h3>Styret</h3>
<p>
<b>Leder:</b> Mats Christensen
</p><p>
<b>Nestleder:</b> Siri Espe
</p><p>
<b>Økonomiansvarlig:</b>Venke Borander
</p><p>
<b>Materialforvalter:</b>Anette Fossum Morken
</p><p>
<b>Sekretær:</b>Oda Elise Strandås
</p><p>
<b>Musikkansvarlig:</b>Silje Engeland
</p><p>
<b>Sponsor- og dugnadsansvarlig:</b>Kristian Thinn Solheim
</p><p>
<b>Besetningsansvarlig:</b>Guro Rafoss Kverneland
</p>

 
<h3>Webkomiteen:</h3>

<p>Sekretær; Oda Elise Strandås (leder)</p>
<p>Stephanie Buadu (medlem)</p>
<p>Rasmus Høgberg (medlem)</p>
<p>Jørgen Olsen Finvik (medlem)</p>
<p>Sindre Stephansen (medlem)</p>
<p>Anne-Siri Borander (fullfører nettsiden)</p>
<p>Trond Klakken (fullfører nettsiden)</p>
<br />
<p>Komiteens arbeidsoppgaver omfavner i hovedsak å videreutvikle, drifte og oppdatere korpsets hjemmeside, facebookside og e-postlister. </p>

 
<h3>Økonomikomiteen:</h3>

<p>Sponsor og dugnadsansvarlig; Kristian Thinn Solheim (leder)</p>
<p>Økonomiansvarlig: Venke Borander</p>
<p>Lene Sevland (medlem)</p>
<p>Morten Hals (medlem)</p>
<p>Daniel Einskar Ellingsen (medlem)</p>
<br />
<p>
Komiteen arbeider med å skaffe korpset inntekter. Dette innebærer blant annet å organisere dugnader og spilleoppdrag.</p>


<h3>Musikkomiteen:</h3>

<p>Musikkansvarlig; Silje Engeland (Musikalsk leder)</p>
<p>Elise Andersen Solbakk (medlem)</p>
<p>Silje Aagaard (medlem)</p>
<p>Markus Skorpen (medlem)</p>
<br />
<p>Musikkomiteen skal fastsette korpsets repertoar og har ansvaret for korpsets noter, notearkiv og notedatabasen.</p>



<h3>Arrangementskomiteen:</h3>

<p>Materialforvalter; Anette Fossum Morken (leder)</p>
<p>Musikkansvarlig: Silje Engeland</p>
<p>Silje Margrethe Jørgensen (medlem)</p>
<p>Mari Raastad (medlem)</p>
<p>Ane Agdestein(medlem)</p>
<br />
<p>Komiteen har ansvaret for det tekniske ved alle konserter korpset med særlig fokus på høstkonserten.</p>

<h3>Besetningskomite:</h3>
<p>Besetningsansvarlig; Guro Rafoss Kverneland (leder)</p>
<p>Bente Skårholen Lomnes (medlem)</p>
<p>Torbjørn Moi (medlem)</p>
<br />
<p>Komiteen jobber med å ha kontroll på korpsets besetning til ulike konserter, arrangement og konkurranser, samt arrangere sosiale tilstelninger for korpsets medlemmer. </p>

<h3>Valgkomiteen:</h3>
<p>Martin Okstad (leder)</p>
<p>Maria Mauring (medlem)</p>
<p>Silje Aagaard (medlem)</p>
<br />
<p>Valgkomiteen skal innstille valgbare kandidater til tillitsverv.</p>

 

<h3>Revisjonskomiteen:</h3>
<p>Karianne Hansen (leder)</p>
<p>Øyvind Øksnes Dalheim (medlem)</p>
<p>Maria Mauring (vara)</p>
<br />
<p>Revisorene skal gjennomgå regnskapet for hver årsmøteperiode og legge fram beretning for årsmøtet.</p>
		
<?php
/*<p><a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'>Instruksen for komitéer</a></p>
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
						echo"<li>".brukerlenke($valgtekomite, Navnlengde::FulltNavn);
						if ($valgtekomite['tittel']!='Medlem'){
							echo" (".$valgtekomite['tittel'].")</li>";
						}
					}
				}
				echo "</ul>";

				echo "</section>";

			}
			
	echo "</section>";
	*/
