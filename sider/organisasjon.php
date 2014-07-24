<?php
	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	//henter ut alle komiteer
	$sql="SELECT komiteid, navn, mail_alias FROM komite ORDER BY posisjon";
	$komiteer=hent_og_putt_inn_i_array($sql,$id_verdi="komiteid");
	
	//henter ut info om medlemmer++ om valgte komité
	$sql="SELECT komite.komiteid, verv.komiteid, navn, vervid, verv.posisjon, komite.posisjon, tittel, medlemmer.medlemsid, 
	verv.medlemsid, epost, fnavn, enavn, foto  FROM komite, verv, medlemmer WHERE medlemmer.medlemsid=verv.medlemsid AND 
	komite.komiteid=verv.komiteid ORDER BY komite.posisjon, verv.posisjon";
    $valgtekomiteer=hent_og_putt_inn_i_array($sql,$id_verdi="vervid");
	
	// Sjekker om du har sendt inn fraværsmelding
	if (has_post('ovelse')) {

	}

	//det som skrives ut på side

	echo "<h2>Praktisk</h2>";	
	
	echo"<table>
			<tr><td><b>Fravær:</b></td><td colspan='3'>
			";

	if (!has_post('ovelse')) {
		echo "
				Fravær meldes til nestleder på e-post eller sms. <br>
				Du kan også fylle ut skjemaet under så autogenereres en mail for deg</td></tr>
				<tr><td colspan=4>
					<form method='post' action='?side=organisasjon'>
						<table>
						<tr><td></td><td>Hvilken øvelse:</td><td colspan='2'><input type='text' name='ovelse'></td></tr>
						<tr><td></td><td>Grunn:</td><td colspan='2'><input type='text' name='grunn'></td></tr>
						<input type='hidden' name='medlemsid' value='".$_SESSION['medlemsid']."'>
						<tr><td colspan='3'></td><td><input class='right' type='submit' name='nyttInnlegg' value='Send'></td></tr>
						</table>
					</form>
				</td></tr>
		";
	} else {
		echo "<b>Takk for at du sa ifra!</b></td></tr>";
	}

	echo "
			<tr><td><b>Permisjon:</b></td><td colspan='3'>Søknad om permisjon sendes på e-post til styret. 
				Husk å oppgi periode og årsak til permisjonen.</td></tr>
			<tr><td><b>Kakebaker:</b></td><td colspan='3'>Det går på rundgang. Se på intern-hovedsiden når det er din tur eller trykk på en aktiviteten.</td></tr>
			<tr><td><b>Slagverksbæregrupper:</b></td><td colspan='3'>Det går på rundgang. Se på intern-hovedsiden når det er din tur eller trykk på en aktiviteten.</td></tr>";
			
			if(has_get('slagverksgrupper')){
				echo"<tr><td><a href='?side=organisasjon'>skjul grupper</a></td><td colspan='3'></td></tr>
				<tr><td></td><td><b>Gruppe 1</b></td><td colspan='2'>.......</td></tr>";

			}else{
				echo "<tr><td><a href='?side=organisasjon&slagverksgrupper=1'>vis grupper</a></td><td colspan='3'><a href=''></a></td></tr>";		
			}				
			echo "<tr><th colspan='4'>Kontaktinformasjon</th></tr>
			<tr><td><b>Adresse:</b></td><td colspan='3'>
				Bispehaugen Ungdomskorps<br>
				Postboks 9012<br>
				Rosenborg
				7455 Trondheim<br> </td></tr>
			<tr><td><b>Organisasjonnummer:</b></td><td colspan='3'>975.729.141</td></tr>
			<tr><td><b>Kontonummer:<br><br></b></td><td colspan='3'>4200 07 51280<br><br></td></tr>
			<tr><td colspan='3'><b>Vedtektene til korpset:<b></td><td><a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'><i>her kommer link til vedtektene<i></a></td></tr>
			</table>";

			echo "<br /><br /><br />";

			echo "<h2>Styret og komiteer</h2>";
			echo"<p><a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'>Instruksen for komitéer</a></p>";

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
						echo"<li>".brukerlenke($valgtekomite, Navnlengde::FulltNavn, true)."</li>";
					}
				}
				echo "</ul>";

				echo "</section>";

			}
