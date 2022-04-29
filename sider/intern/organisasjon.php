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
<?php echo innhold("praktisk"); ?>
</section>

	<section class="styret-og-komiteer">
		<h2>Styret og komiteer</h2>

<h3>Styret</h3>
<p>
<b>Leder:</b> Kamilla Edvardsen
</p><p>
<b>Nestleder:</b> Ole Edvard Kolvik Valøy
</p><p>
<b>Økonomiansvarlig:</b> Vegard Jacobsen
</p><p>
<b>Materialforvalter:</b> Silje Aagaard
</p><p>
<b>Sekretær:</b> Henriette Brustad
</p><p>
<b>Musikkansvarlig:</b> Sander Krokengen
</p><p>
<b>Sponsor- og dugnadsansvarlig:</b> Silje Margrethe Jørgensen
</p><p>
<b>Besetningsansvarlig:</b> Andreas Bjørkavoll
</p>


<h3>Mediekomiteen:</h3>

<p>Sekretær; Henriette Brustad (leder)</p>
<p>Signe Sveen Gussgard (medlem)</p>
<p>Solveig Skavnes (medlem)</p>
<p>Silje Bratshaug Mehus (medlem)</p>
<br />
<p>Komiteens arbeidsoppgaver omfavner i hovedsak å videreutvikle, drifte og oppdatere korpsets hjemmeside, facebookside og e-postlister. </p>


<h3>Sponsor- og dugnadskomiteen:</h3>

<p>Sponsor og dugnadsansvarlig; Silje Margrethe Jørgensen (leder)</p>
<p>Økonomiansvarlig: Vegard Jacobsen</p>
<p>Hedda Fossum (medlem)</p>
<p>Andreas Bernhus (medlem)</p>
<p>Nora Røhnebæk Aasen (medlem)</p>
<br />
<p>
Komiteen arbeider med å skaffe korpset inntekter. Dette innebærer blant annet å organisere dugnader og spilleoppdrag.</p>


<h3>Musikkomiteen:</h3>

<p>Musikkansvarlig; Sander Krokengen (leder)</p>
<p>Joachim Spange (medlem)</p>
<p>Arnhild Pedersen (medlem)</p>
<br />
<p>Musikkomiteen skal fastsette korpsets repertoar og har ansvaret for korpsets noter, notearkiv og notedatabasen.</p>



<h3>Arrangementskomiteen:</h3>

<p>Nestleder; Ole Edvard Kolvik Valøy (leder)</p>
<p>Marie Kristoffersen (medlem)</p>
<p>Kjersti Magnussen Føllesdal (medlem)</p>
<br />
<p>Komiteen har ansvaret for det tekniske ved alle konserter korpset med særlig fokus på høstkonserten.</p>

<h3>Besetningskomite:</h3>
<p>Besetningsansvarlig; Andreas Bjørkavoll (leder)</p>
<p>Marie Solberg (medlem)</p>
<p>Åsmund Vågslid (medlem)</p>
<br />
<p>Komiteen jobber med å ha kontroll på korpsets besetning til ulike konserter, arrangement og konkurranser, samt arrangere sosiale tilstelninger for korpsets medlemmer. </p>

<h3>Valgkomiteen:</h3>
<p>Merete Hvattum (leder)</p>
<p>Øyvor Skoglund (medlem)</p>
<p>Synne Solli Nygjelten (medlem)</p>
<p>Birgitte Ellingsen (vara)</p>
<br />
<p>Valgkomiteen skal innstille valgbare kandidater til tillitsverv.</p>



<h3>Revisjonskomiteen:</h3>
<p>Bente Lomnes (leder)</p>
<p>Roar Bustad (medlem)</p>
<p>Mari Flønes (vara)</p>
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
