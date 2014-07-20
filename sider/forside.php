<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
global $antall_nyheter;
$antall_nyheter = 4;

//Henter ut mobilnummeret til leder
$leder = hent_og_putt_inn_i_array("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel='Leder'");

if (!er_logget_inn()) {
?>
<section class="side nyheter" data-scroll-index='2' data-scroll-url="?side=nyheter/liste">
	<div class='content'>
		
		<article class="box news" style="background-color: white; color: rgb(34, 41, 51);">
          <h2>Neste konsert</h2>
			<time class="fancy-date" datetime="2013-07-05T11:46:00+00:00" title="05.07.2013 11:46:00" style="color: rgb(34, 41, 51)">
				<div class="weekday">fre</div>
				<div class="day">5.</div>
				<div class="month">aug</div>
			</time>

          <div class="bilde-og-innhold">
            <div class="bilde">
                <img src="../bilder/Logo/jublogo2.jpg"  style="border-color: rgb(34, 41, 51)">
            </div>
            <div class="innhold">
                <h4>Høstkonsert i frimurerlogen</h4>
                <p class="ingress">Vi kjører igang med konsert!! WOOOP WOOOP</p>
                <p class="pris">BARN/STUDENT/HONØR/STØTTEMEDLEM: 50 kr, VOKSEN: 100 kr</p>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="neste-pil" title="Les nyhet"><a href="?side=nyheter/vis&amp;id=1383"><i class="fa fa-chevron-right"></i></a></div>
        </article>
		
		<?php
			inkluder_side_fra_undermappe("nyheter/liste");
		?>
        <div class="clearfix"></div>
    </div>
	<div class="clearfix"></div>
</section>
<section class="side aktiviteter" data-scroll-index='3' data-scroll-url="?side=aktiviteter/liste">
	<div class='content'>        
    <?php
    	inkluder_side_fra_undermappe("aktiviteter/liste");
    ?>
	</div>
</section>
<section class="side spilleoppdrag coverflow" data-scroll-index='4'>
  <div class='content'>
    <h2>Spilleoppdrag</h2>
    <p>Tyrolerorkester, fanfareoppdrag</p>
    <p>Slider her, med siste slide er andre oppdrag og hvordan bestille :)</p>
</div>
</section>
<section class="side" data-scroll-index='5' data-scroll-url="?side=bli-medlem">
 	<div class='content'>
		<?php
			inkluder_side_fra_undermappe("bli-medlem");
		?>
	</div>
</section>
<section class="side medlemmer side-invertert" data-scroll-index='6' data-scroll-url="?side=medlem/liste">
  <div class='content'>
    <?php
		inkluder_side_fra_undermappe("medlem/liste");
    ?>
</div>
</section>
<section class="side korpset" data-scroll-index='7' data-scroll-url="?side=annet">
  <div class='content'>
    <h2>Korpset</h2>
    <p>
    <img width="250" src="../bilder/nm2000scene.jpg" style="float:right;margin-left:8px" alt="Fra NM for janitsjar">
    Bispehaugen Ungdomskorps ble startet i 1923, og er således et av Trondheims eldste amatørkorps. Korpset har helt siden starten vært
    kjent for å ha dyktige musikere og dirigenter, og mang en senere profesjonell musiker har vært medlem av Bispehaugen. Vi liker å spille
    allsidig musikk med kvalitet, fra underholdningsmusikk til større originalskrevne og klassiske verk. I dag teller vi drøyt 66 medlemmer, 
    men vi satser stort på rekruttering og håper å få med deg som medlem! 
    </p><p>

    <img width="100" src="../bilder/medlemsfoto/Tomas_2.jpg" style="float:left;margin-right:8px" alt="Tomas Carstensen">
    Siden høsten 2000 har Tomas Carstensen vært vår faste dirigent. Han har utdannelse som trompetist
    og dirigent fra Musikkonservatoriet i Trondheim.
    </p><p>

    Korpset har i mange år hevdet seg i toppen av norsk 1. divisjon i NM janitsjar (<a href="meritter.php">se resultater</a>). Under NM i 2006 vant 
    korpset 1. divisjon, og konkurrerte i elitedivisjonen i to år. I perioden 2009-2011 konkurrerte vi i 1. divisjon, og fra 2012 har korpset konkurrert i 2. divisjon
    Vår musikalske målsetting er å ha full Symphonic Band-besetning, og å være et ungdomskorps i ordets rette forstand. Bispehaugen ønsker ikke å bli plassert i en bestemt bås, men skal
    kjennetegnes av allsidighet og seriøs satsing innen flere områder.
    </p><p>

    <img width="180" src="../bilder/frostaseminar.jpg" style="float:right;margin-left:7px;margin-bottom:8px" alt="Seminar på Frosta">
    Bispehaugen legger vekt på et godt sosialt miljø både i og utenfor øvingslokalet. Korpset har utenommusikalske aktiviteter som
    kafébesøk, fester og helgeturer utenbys. Medlemmene våre blir i stor grad rekruttert fra studentmiljøet i Trondheim, noe som setter standarden for våre utenommusikalske aktiviteter.
    </p><p>

    <img src="../bilder/fest.jpg" width="180" height="138" style="float:left;margin-right:8px">
    Vi øver mandager fra 19:30 på Bispehaugen skole på Møllenberg, sentralt i Trondheim. Det blir også noen ekstraøvelser før
    konsertene. Dersom du er interessert i hva vi holder på med, kan du ta turen innom en av øvelsene, eller gå inn på <a href="<?php if(!erForside()){echo "?side=forside";}?>#blimedlem">kontaksskjema</a>.
    </p>

    <div class="clearfix"></div>
</div>
</section>
    	
<section class="side kontakt" data-scroll-index='8' data-scroll-url="?side=annet">
	<div class="content">
		<h2>Kontakt oss</h2>
		
		<section class="kontakt-float">
			<h4>E-post</h4>
			<p><script type="text/javascript">document.write("<a href=\"mailto:sty" + "ret" + "@" + "bispe" + "haugen" + "." + ".no\">");</script>styre<span class="hidden">EAT THIS ROBOTS</span>@bispehaugen.no</a></p>
			
			<h4>Telefon leder</h4>
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
				Organisasjonnummer: 975.729.141<br />
				Kontonummer: 4200 07 51280
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
	</div>
</section>

<?php
} else {
?>


<section class="side side-invertert internside">
	<p>Sorter ettter dato, hvis du snart skal ha med kake blir den å finne høyt opp</p>
<h1>Internsiden</h1>

<h3>Neste øvelse?</h3>

<h3>Neste konsert?</h3>
<h3>Gjeldende noter?</h3>

<h3 style="color: red">Bake kake?
	<p>Du skal ha med kake den 4. september kl. 19. Det er bare 15 dager til!</p></h3>
<h3>Bære slagværk?</h3>
<h3>Kjøre med henger?</h3>

<h3>Siste på forum</h3>
<h3>Siste nytt</h3>
</section>
	
<?php
}



