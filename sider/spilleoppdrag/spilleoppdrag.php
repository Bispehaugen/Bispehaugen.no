<?php

include_once "funksjoner.php";


?>
<h1>Levende musikk for anledningen?</h1>
<div>
	Ønsker du underholdning til ditt arrangement? Bispehaugen Ungdomskorps er alltid klare til å heve stemningen i selskapet!
</div>
<br>
<div>
	
	<b>Vi skaper den rette stemninge ved mange ulike arrangement, som:</b>
	<ul>
		<li>Juletrefester og julebord</li>
		<li>Kundearrangement</li>
		<li>Fester/selskap</li>
		<li>Åpningssermonier</li>
		<li>Eksamenssermonier</li>
		<li>Bryllyp</li>
	</ul>
	
	<b>Vi kan stille med:</b>
	<ul>
		<li>Fullt musikkorps</li>
		<li>Mindre ensembler</li>
		<li>Tyrolerorkester</li>
		<li>Fanfarebesetning</li>
	</ul>

	<p>
	<b>Kontakt oss på <a href="mailto:styret@bispehaugen.no"> styret@bispehaugen.no</a>,
		så finner vi noe som kan passe for din anledning </b>
	</p>
	<p><b>Du kan eventuelt fylle ut bestillingsskjemaet nedenfor.</b></p>
</div>
<?php

$har_alle_feltene_utfylt_og_sendt_mail = false;
$feilmeldinger = array();

$sAnnet = "";
$sNavn = "";
$sOrganisasjon= "";
$sAdresse= "";
$sTelefon= "";
$sEpost= "";
$sPoststed = "";


// Hvis Spilleoppdrag mottar et skjema
if(has_post("sEpost")){
	
	$sAnnet = post("sAnnet");
	$sNavn = post("sNavn");
	$sOrganisasjon = post("sOrganisasjon");
	$sAdresse = post("sAdresse");
	$sEpost = post("sEpost");
	$sTelefon = post("sTelefon");

	# Definerer headere til mailen som skal sendes
	$from = "From: !!BUK web-skjema!! <buk-webskjema@stud.ntnu.no>";
	$to = 'buk-webskjema@stud.ntnu.no';
	$replyto = "Reply-To: $sNavn <$sEpost>";
	$realfrom_tmp = getenv("REMOTE_HOST") ? getenv("REMOTE_HOST") : getenv("REMOTE_ADDR");
	$realfrom = "Real-From: $realfrom_tmp";
	$subject = "Nytt spilleoppdrag registrert via web-skjema";
	
	$header="$from\r\n"."$replyto\r\n"."$realfrom";
	
	 if (preg_match(SPAMFILTER, $sAnnet) || preg_match(SPAMFILTER, $sNavn) || preg_match(SPAMFILTER, $sOrganisasjon) || preg_match(SPAMFILTER, $sHvorKjentMed) || preg_match(SPAMFILTER, $sEpost) || preg_match(SPAMFILTER, $sTelefon)){
	     die("Beskjeden du skrev inneholder taggede ord og ble derfor ikke godkjent av spamfilteret.");
	 }
	
	$message="Denne personen har besøkt BUK sine websider og sendt inn skjemaet for
	spilleoppdrag. BUK lover å ta kontakt for en uforpliktende samtale.
	
	NAVN:           $sNavn
	ORGANISASJON     $sOrganisasjon
	E-POST:         $sEpost
	TELEFON:        $sTelefon
	
	INFORMASJON
	$sAnnet";

	if (!isset($sNavn) || $sNavn=="" || ($sTelefon=="" && $sEpost=="")) { 
	   $feilmeldinger[] =  "<font color=red>Du må fylle inn navn og kontaktinformasjon</font>"; 
	} 
	elseif (preg_match("/@mail.com/",$sEpost)>0){ 
	   $feilmeldinger[] =  "<font color=red>Forespørsler fra @mail.com e-post adresser er dessverre blokkert p.g.a. problemer med spam.</font>";
	}
	elseif ($sEpost!="" && preg_match("/@/",$sEpost)==0){
	   $feilmeldinger[] =  "<font color=red>Ugyldig e-post adresse. Forespørselen er blitt blokkert p.g.a. problemer med spam.</font>";
	}
	elseif ($sTelefon!="" && preg_match("/\d\d/",$sTelefon)==0) {
	   $feilmeldinger[] =  "<font color=red>Ugyldig telefonnummer. Forespørselen er blitt blokkert p.g.a. problemer med spam.</font>";		    
	} 
	//elseif (mail($to,$subject,$message,$header)) {
	elseif (true) {
		#echo "<div class=\"header1\">Nye medlemmer</div>";
		echo "<br>Takk for interessen! Vi vil ta kontakt så fort vi kan.";
		$har_alle_feltene_utfylt_og_sendt_mail = true;
	}

}

if($har_alle_feltene_utfylt_og_sendt_mail == false){
?>

<form action="?side=bli_medlem" method="post">
	<table>
		<tr>
			<td class="label">Navn:</td>
			<td>
			<input type="text" name="sNavn" value="<?php echo $sNavn; ?>"></td>
		</tr>
		<tr>
			<td class="label">Organisasjon:</td>
			<td>
			<input type="text" name="sOrganisasjon" value="<?php echo $sOrganisasjon; ?>"></td>
		</tr>
		<tr>
			<td class="label">Adresse:</td>
			<td>
			<input type="text" name="sAdresse" value="<?php echo $sAdresse; ?>"></td>
		</tr>
		<tr>
			<td class="label">Poststed:</td>
			<td>
			<input type="text" name="sPoststed" value="<?php echo $sPoststed; ?>"></td>
		</tr>
		<tr>
			<td class="label">E-Post:</td>
			<td>
			<input type="text" name="sEpost" value="<?php echo $sEpost; ?>"></td>
		</tr>
		<tr>
			<td class="label">Telefon:</td>
			<td>
			<input type="text" name="sTelefon" value="<?php echo $sTelefon; ?>"></td>
		</tr>
		<tr>
			<td colspan=2>
				<span class="label">Tanker om oppdraget:</span><br />
				<textarea name="sAnnet"><?php echo $sAnnet; ?></textarea>
			</td>
		</tr>
		</table>
	</form>
	<?php
	}
