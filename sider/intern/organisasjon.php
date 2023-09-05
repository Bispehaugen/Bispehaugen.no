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
<?php
	echo innhold("praktisk");

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
?>
