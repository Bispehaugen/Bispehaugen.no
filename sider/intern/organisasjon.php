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
