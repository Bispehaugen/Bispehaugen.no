<?php

include_once "funksjoner.php";


?>
<div class="spilleoppdrag gjennomsiktig-boks">
	<section class="informasjon">
    <?php echo innhold("spilleoppdrag", "div"); ?>
	</section>
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
	$to = 'buk-webskjema@stud.ntnu.no';
	$replyto = "Reply-To: $sNavn <$sEpost>";
	$subject = "Nytt spilleoppdrag registrert via web-skjema";
	
	 if (preg_match(SPAMFILTER, $sAnnet) || preg_match(SPAMFILTER, $sNavn) || preg_match(SPAMFILTER, $sOrganisasjon) || preg_match(SPAMFILTER, $sEpost) || preg_match(SPAMFILTER, $sTelefon)){
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
	//elseif (epost($to,$replyto,$subject,$message)) { // TODO: faktisk send mail igjen
	elseif (true) {
		echo "<h2>Takk for interessen!</h3><p>Vi vil ta kontakt så fort vi kan.</p>";
		$har_alle_feltene_utfylt_og_sendt_mail = true;
	}

}

if($har_alle_feltene_utfylt_og_sendt_mail == false){
?>

	<!--<form action="?side=spilleoppdrag/vis" method="post">
		<h2>Kontaktskjema</h2>

		<?php echo feilmeldinger($feilmeldinger); ?>
		<p>
			<input type="text" name="sNavn" placeholder="Navn" value="<?php echo $sNavn; ?>">
		</p>
		<p>
			<input type="text" name="sOrganisasjon" placeholder="Organisasjon" value="<?php echo $sOrganisasjon; ?>">
		</p>
		<p>
			<input type="text" name="sAdresse" placeholder="Adresse" value="<?php echo $sAdresse; ?>">
		</p>
		<p>
			<input type="text" name="sPoststed" placeholder="Poststed" value="<?php echo $sPoststed; ?>">
		</p>
		<p>
			<input type="text" name="sEpost" placeholder="E-post" value="<?php echo $sEpost; ?>">
		</p>
		<p>
			<input type="text" name="sTelefon" placeholder="Telefon" value="<?php echo $sTelefon; ?>">
		</p>
		<p>
			<textarea name="sAnnet" placeholder="Tanker om oppdraget"><?php echo $sAnnet; ?></textarea>
		</p>
		<p>
			<input class="right" type="submit" value="Send inn" />
		</p>
	</form>-->
	<div class="clearfix"></div>
</div>

<div class="clearfix"></div>
	<?php
	}
