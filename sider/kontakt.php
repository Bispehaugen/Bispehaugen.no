<?php
//Henter ut mobilnummeret til leder
//Denne skal det egentlig være
//$leder = hent_og_putt_inn_i_array("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel='Leder'");

//fiks akkurat når leder er utenlands
$leder = hent_og_putt_inn_i_array("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel = 'Leder'");
	
//henter ut info om medlemmer++ om styret
	$sql="SELECT foto, verv.komiteid, vervid, verv.posisjon, tittel, medlemmer.medlemsid, verv.medlemsid, epost, fnavn, enavn FROM verv, medlemmer WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3'  ORDER BY verv.posisjon;";
    $styremedlemmer=hent_og_putt_inn_i_array($sql,$id_verdi="vervid");
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
	<a href="https://twitter.com/Bispehaugen" title="Følg BUK på Twitter">
		<svg class='svg-icon' version="1.1" id="TwitterIcon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
 width="56.693px" height="56.693px" viewBox="0 0 56.693 56.693" enable-background="new 0 0 56.693 56.693" xml:space="preserve">
			<path d="M52.837,15.065c-1.811,0.805-3.76,1.348-5.805,1.591c2.088-1.25,3.689-3.23,4.444-5.592c-1.953,1.159-4.115,2-6.418,2.454
				c-1.843-1.964-4.47-3.192-7.377-3.192c-5.581,0-10.106,4.525-10.106,10.107c0,0.791,0.089,1.562,0.262,2.303
				c-8.4-0.422-15.848-4.445-20.833-10.56c-0.87,1.492-1.368,3.228-1.368,5.082c0,3.506,1.784,6.6,4.496,8.412
				c-1.656-0.053-3.215-0.508-4.578-1.265c-0.001,0.042-0.001,0.085-0.001,0.128c0,4.896,3.484,8.98,8.108,9.91
				c-0.848,0.23-1.741,0.354-2.663,0.354c-0.652,0-1.285-0.063-1.902-0.182c1.287,4.015,5.019,6.938,9.441,7.019
				c-3.459,2.711-7.816,4.327-12.552,4.327c-0.815,0-1.62-0.048-2.411-0.142c4.474,2.869,9.786,4.541,15.493,4.541
				c18.591,0,28.756-15.4,28.756-28.756c0-0.438-0.009-0.875-0.028-1.309C49.769,18.873,51.483,17.092,52.837,15.065z"/>
		</svg>
	</a>
</section>

<section class="kontakt-float">
	<h4>Postadresse</h4>
	<p>
		Bispehaugen Ungdomskorps<br />
		Postboks 9012<br />
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
