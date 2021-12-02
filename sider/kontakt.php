<?php
global $dbh;

//Henter ut mobilnummeret til leder
//Denne skal det egentlig være
//$leder = hent_og_putt_inn_i_array("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel='Leder'");

//fiks akkurat når leder er utenlands
$leder = $dbh->query("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel = 'Leder'")->fetch();
	
//henter ut info om medlemmer++ om styret
$sql="SELECT vervid, foto, verv.komiteid, verv.posisjon, tittel, medlemmer.medlemsid, verv.medlemsid, epost, fnavn, enavn FROM verv, medlemmer WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3'  ORDER BY verv.posisjon;";
$styremedlemmer=hent_og_putt_inn_i_array($sql);
?>

<h2>Kontakt oss</h2>
	<h4>Styret</h4>
	<section class="kontakt-styret">
		<?php
				foreach($styremedlemmer as $styremedlem){
							//hadde tenkt til å ha en egen klasse	
							//echo "<section class='kontakt-float'>";
							echo "<section class='styremedlem'>";
							echo "<p class='tittel'>".$styremedlem['tittel']."</p>";
							//formatteringa ble helt korka så utelater bilde inntil videre
							echo brukerlenke(hent_brukerdata($styremedlem['medlemsid']), Navnlengde::FulltNavn, false);
							echo "</section>";
				}
		?>	
		<div class="clearfix"></div>
	</section>		

<section class="kontakt-float">
	<h4>E-post</h4>
	<p><script type="text/javascript">document.write("<a href=\"mailto:sty" + "ret" + "@" + "bispe" + "haugen" + "." + ".no\">");</script>styret<span class="hidden">EAT THIS ROBOTS</span>@bispehaugen.no</a></p>
	
	<h4>Telefon styret</h4>
	<p><a href="tel:+47<?php echo $leder['tlfmobil']; ?>">+47 <?php echo $leder['tlfmobil']; ?></a></p>

	<a href="https://www.facebook.com/BispehaugenUngdomskorps" title="Besøk BUK på Facebook">
		<svg class='svg-icon' version="1.1" id="FacebookIcon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 width="56.693px" height="56.693px" viewBox="0 0 56.693 56.693" enable-background="new 0 0 56.693 56.693" xml:space="preserve">
			<path d="M40.43,21.739h-7.645v-5.014c0-1.883,1.248-2.322,2.127-2.322c0.877,0,5.395,0,5.395,0V6.125l-7.43-0.029
				c-8.248,0-10.125,6.174-10.125,10.125v5.518h-4.77v8.53h4.77c0,10.947,0,24.137,0,24.137h10.033c0,0,0-13.32,0-24.137h6.77
				L40.43,21.739z"/>
		</svg>

	</a>
    <style>
        .ig-b- { display: inline-block; }
        .ig-b- img { visibility: hidden; }
        .ig-b-:hover { background-position: 0 -60px; } .ig-b-:active { background-position: 0 -120px; }
        .ig-b-48 { width: 48px; height: 48px; background: url(//badges.instagram.com/static/images/ig-badge-sprite-48.png) no-repeat 0 0; }
        @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
        .ig-b-48 { background-image: url(//badges.instagram.com/static/images/ig-badge-sprite-48@2x.png); background-size: 60px 178px; } }
    </style>
    <a href="https://www.instagram.com/bispehaugenungdomskorps/?ref=badge" class="ig-b- ig-b-48"><img src="//badges.instagram.com/static/images/ig-badge-48.png" alt="Instagram" /></a>
</section>

<section class="kontakt-float">
	<h4>Postadresse</h4>
	<p>
		Bispehaugen Ungdomskorps<br />
		Postboks 9022<br />
		Rosenborg 7455 Trondheim<br />
	</p>
	
	<h4>Annet</h4>
	<p>
		Organisasjonnummer: <span class="tall">975.729.141</span><br />
		Kontonummer: <span class="tall">4200 07 51280</span>
	</p>
</section>


<div class="clearfix"></div>

<section class="besok-oss">
	<h4>Besøksadresse</h4>
	<p>
		Bispehaugen skole<br />
		Nonnegata 19 <br />
		7014 Trondheim
	</p>
	
	<div class="pil-ned"></div>
</section>
