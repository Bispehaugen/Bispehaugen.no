<?php

include_once "funksjoner.php";

$har_alle_feltene_utfylt_og_sendt_mail = false;
$feilmeldinger = array();

$sAnnet = "";
$sNavn = "";
$sInstrument = "";
$sHvorKjentMed = "";
$sEpost = "";
$sTelefon = "";


// Hvis bli_medlem mottar et skjema
if(isset($_POST["sEpost"])){
	
	$sAnnet = $_POST["sAnnet"];
	$sNavn = $_POST["sNavn"];
	$sInstrument = $_POST["sInstrument"];
	$sHvorKjentMed = $_POST["sHvorKjentMed"];
	$sEpost = $_POST["sEpost"];
	$sTelefon = $_POST["sTelefon"];

	# Definerer headere til mailen som skal sendes
	$from = "From: !!BUK web-skjema!! <buk-webskjema@stud.ntnu.no>";
	$to = 'buk-webskjema@stud.ntnu.no';
	$replyto = "Reply-To: $sNavn <$sEpost>";
	$realfrom_tmp = getenv("REMOTE_HOST") ? getenv("REMOTE_HOST") : getenv("REMOTE_ADDR");
	$realfrom = "Real-From: $realfrom_tmp";
	$subject = "Nytt BUK medlem registrert via web-skjema";
	
	$header="$from\r\n"."$replyto\r\n"."$realfrom";
	
	 if (preg_match(SPAMFILTER, $sAnnet) || preg_match(SPAMFILTER, $sNavn) || preg_match(SPAMFILTER, $sInstrument) || preg_match(SPAMFILTER, $sHvorKjentMed) || preg_match(SPAMFILTER, $sEpost) || preg_match(SPAMFILTER, $sTelefon)){
	     die("Beskjeden du skrev inneholder taggede ord og ble derfor ikke godkjent av spamfilteret.");
	 }
	
	$message="Denne personen har besøkt BUK sine websider og sendt inn skjemaet for
	nye medlemmer. Husk at Bispehaugen lover å ta kontakt innen en uke.
	
	NAVN:           $sNavn
	INSTRUMENT:     $sInstrument
	E-POST:         $sEpost
	TELEFON:        $sTelefon
	
	Musikalsk bakgrunn:
	$sAnnet
	
	Hvor fikk du høre om Bispehaugen:
	$sHvorBleKjent";
	
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
		echo "<br>Takk for interessen! En person fra korpset vil kontakte deg innen en uke.";
		$har_alle_feltene_utfylt_og_sendt_mail = true;
	}

}

if($har_alle_feltene_utfylt_og_sendt_mail == false){
?>

<h1>Bli Medlem!</h1>
<div>
	
	<b>Vil du være med oss og spille?</b>
	<p>
	Vi ønsker nye medlemmer velkomne! Våre sentrale verdier består i å være inkluderende, ambisiøse og lekne.
	<br>
	Dette innebærer at vi lover våre medlemmer å spille utfordrende og engasjerende musikk, samtidig som at vi stiller krav både til dirigent
	og musikere. Dessuten synes vi det er viktig å gi publikum gode konsertopplevelser. 
	</p>
	<p>
	Vil du være med oss og spille, lover vi å få deg til å føle deg velkommen! <b>Tar du utfordringen?</b>
	</p>

	<p>	
		Du kan også ta turen innom på en av våre øvelser, mandager kl 19:30 i gymsalen
		på <a href="http://kart.gulesider.no/query?what=map_yp&search_word=bispehaugen%2Bskole&q=bispehaugen%20skole">Bispehaugen skole (Nonnegt. 19</a>). Øvings- og konsertplanen finner du under
	<a href="?side=aktiviteter">aktiviteter</a>.
	</p>
	
	<p>
	<span class="viktig">Interessert i å være med i Bispehaugen?
	Fyll ut skjemaet så tar vi kontakt med deg!</span>
	</p>
</div>
<br />

<ul class="feilmeldinger">
	<?php 
		foreach($feilmeldinger as $feilmelding){
			echo "<li class='feil'>$feilmelding</li>";
		}
	?>
</ul>

<form action="?side=bli_medlem" method="post">
	<table>
		<tr>
			<td class="label">Navn:</td>
			<td>
			<input type="text" name="sNavn" value="<?php echo $sNavn; ?>"></td>
		</tr>
		<tr>
			<td class="label">Instrument:</td>
			<td>
			<input type="text" name="sInstrument" value="<?php echo $sInstrument; ?>"></td>
		</tr>
		<tr>
			<td class="label">E-post:</td>
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
				<span class="label">Musikalsk bakgrunn:</span><br />
				<textarea name="sAnnet"><?php echo $sAnnet; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<span class="label">Hvor fikk du høre om korpset?:</span><br />
			<textarea name="sHvorKjentMed"><?php echo $sHvorKjentMed; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="submit" colspan=2>
				<div style="float: left;"><input id="registrering" type="checkbox" value="registrer" />
					<label for="registrering"> Jeg ønsker å opprette bruker på internsiden med en gang</label></div>
				<div><input type="submit" name="btnSubmit" value="Send skjema"></div>
			</td>
		</tr>
	</table>
</form>
</td>
</tr>
</table>
<img src="bilder/forside/figurer_medtekst_stor.png" class="center" />

<?php 
}
