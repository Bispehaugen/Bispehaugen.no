<?php
setlocale(LC_TIME, "nb_NO");
$siste_nyheter = hent_siste_nyheter(4);

//Henter ut mobilnummeret til leder
$leder = hent_og_putt_inn_i_array("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel='Leder'");

if (!er_logget_inn()) {
?>
	<section class="side nyheter" data-scroll-index='2'>
        <a name="nyheter"></a>
    	<div class='content'>
    		<h2><a href="?side=nyhet" title="Les flere nyheter">Nyheter</a></h2>
    		<?php
			foreach($siste_nyheter as $nyhet){
				
				$bilde = $nyhet['bilde'];
				if(empty($bilde)){
					$bilde = "icon_logo.png";
				}
				
			echo '
				<article class="box news">
	              <div class="date" datetime="'.date("c", strtotime($nyhet['tid'])).'">
	                <div class="weekday">'.strftime("%a", strtotime($nyhet['tid'])).'</div>
	                <div class="day">'.date("j", strtotime($nyhet['tid'])).'</div>
	                <div class="month">'.strftime("%b", strtotime($nyhet['tid'])).'</div>
	                <div class="year">'.date("Y", strtotime($nyhet['tid'])).'</div>
	              </div>
	              <div class="bilde-og-innhold">
	                <div class="bilde">
	                    <img src="'.$bilde.'" />
	                </div>
	                <div class="innhold">
	                    <h4>'.$nyhet['overskrift'].'</h4>
	                    <p class="ingress">'.$nyhet['ingress'].'</p>
	                </div>
	                <div class="clearfix"></div>
	              </div>
	              <div class="neste-pil"><a href="?side=nyhet&id='.$nyhet['nyhetsid'].'"><i class="fa fa-chevron-right"></i></a></div>
	            </article>
				';
			}
?>
            <div class="clearfix"></div>
        </div>
    	<div class="clearfix"></div>
    </section>
    <section class="side aktiviteter" data-scroll-index='3'>
      <a name="aktiviteter"></a>
      <div class='content'>        
        <?php
        	inkluder_side_fra_undermappe("aktiviteter/liste");
        ?>
    </div>
</section>
<section class="side spilleoppdrag coverflow" data-scroll-index='4'>
  <a name="spilleoppdrag"></a>
  <div class='content'>
    <h2>Spilleoppdrag</h2>
    <p>Tyrolerorkester, fanfareoppdrag</p>
    <p>Slider her, med siste slide er andre oppdrag og hvordan bestille :)</p>
</div>
</section>
<section class="side bli-medlem" data-scroll-index='5'>
 	<a name="blimedlem"></a>
 	<div class='content'>

		<div class="bli-medlem">
		    <h2>Bli medlem!</h2>
		    
			<b>Vil du v�re med oss og spille?</b>
			<p>
			Vi �nsker nye medlemmer velkomne! V�re sentrale verdier best�r i � v�re inkluderende, ambisi�se og lekne.
			<br>
			Dette inneb�rer at vi lover v�re medlemmer � spille utfordrende og engasjerende musikk, samtidig som at vi stiller krav b�de til dirigent
			og musikere. Dessuten synes vi det er viktig � gi publikum gode konsertopplevelser. 
			</p>
			<p>
			Vil du v�re med oss og spille, lover vi � f� deg til � f�le deg velkommen!<br>
			<b>Tar du utfordringen?</b>
			</p>
		
			<p>	
				Du kan ogs� ta turen innom p� en av v�re �velser, mandager kl 19:30 i gymsalen
				p� <a href="http://kart.gulesider.no/query?what=map_yp&search_word=bispehaugen%2Bskole&q=bispehaugen%20skole">Bispehaugen skole (Nonnegt. 19</a>). �vings- og konsertplanen finner du under
			<a href="?side=aktiviteter">aktiviteter</a>.
			</p>
			
			<p>
			<span class="viktig">Interessert i � v�re med i Bispehaugen?
			Fyll ut skjemaet s� tar vi kontakt med deg!</span>
			</p>
		</div>
		
		<div class="stottemedlem">
			<h2>Eller bli st�ttemedlem!</h2>
			<p><b>Det er ressurskrevende � drive et ambisi�st korps som Bispehaugen.</b>
			  Vi har derfor etablert en st�ttemedlemskapsordning for � skaffe ekstra midler til korpset.
			  <p>
			  Vi tilbyr de som st�tter oss noen fordeler. Disse er:
			  </p><ul>
			    <li>Billetter til student/honn�rpris p� konserter arrangert av Bispehaugen</li>
			    <li>Personlig invitasjon til alle korpsets arrangementer, konserter, turer og fester</li>
			    <li>Personlig p�logging til v�re egne web-sider som gir adgang til informasjon som
			      vanligvis bare er tilgjengelig for medlemmer</li>
			  </ul>
			  St�ttemedlemsskap i Bispehaugen koster 300 kr i �ret, dersom du vil st�tte mer kan du 
			  legge p� 300 eller 600kr.
			  <br />
			  Ta kontakt p� mail p� <a href="mailto:styret@bispehaugen.no?subject=St�ttemedlemsskap">styret@bispehaugen.no</a>.
			</p>
		</div>
		<div class="clearfix"></div>
		
		<form>
	      <table>
	        <tr>
	          <td class="label">Navn</td>
	          <td><input type="text" name="sNavn"></td>
	        </tr>
	        <tr>
	          <td class="label">Instrument</td>
	          <td><input type="text" name="sInstrument"></td>
	        </tr>
	        <tr>
	          <td class="label">E-post</td>
	          <td><input type="text" name="sEpost"></td>
	        </tr>
	        <tr>
	          <td class="label">Telefon</td>
	          <td><input type="text" name="sTelefon"></td>
	        </tr>
	        <tr>
	          <td colspan=2>
	            <span class="label">Musikalsk bakgrunn</span><br />
	            <textarea name="sAnnet"></textarea>
	          </td>
	        </tr>
	        <tr>
	          <td colspan=2>
	            <span class="label">Hvor fikk du h�re om korpset?</span><br />
	          <textarea name="sHvorKjentMed"></textarea>
	          </td>
	        </tr>
	        <tr>
	          <td class="submit" colspan=2>
	            <input id="registrering" type="checkbox" value="registrer" />
	            <label for="registrering"> Jeg �nsker � opprette bruker p� internsiden med en gang</label>
	          </td>
	        </tr>
	        <tr class="passord-rad">
	          <td class="label">Passord</td>
	          <td><input type="password" name="sPassord"></td>
	        </tr>
	        <tr>
	          <td colspan=2>
	            <p class="right"><input type="submit" name="btnSubmit" value="Send skjema"></p>
	          </td>
	        </tr>
	      </table>
	    </form>

		<div class="clearfix"></div>
	</div>
</section>
<section class="side medlemmer side-invertert" data-scroll-index='6'>
  <a name="medlemmer"></a>
  <div class='content'>
    <?php
		inkluder_side_fra_undermappe("medlem/liste");
    ?>
</div>
</section>
<section class="side korpset" data-scroll-index='7'>
  <a name="korpset"></a>
  <div class='content'>
    <h2>Korpset</h2>
    <p>
    <img width="250" src="../bilder/nm2000scene.jpg" style="float:right;margin-left:8px" alt="Fra NM for janitsjar">
    Bispehaugen Ungdomskorps ble startet i 1923, og er s�ledes et av Trondheims eldste amat�rkorps. Korpset har helt siden starten v�rt
    kjent for � ha dyktige musikere og dirigenter, og mang en senere profesjonell musiker har v�rt medlem av Bispehaugen. Vi liker � spille
    allsidig musikk med kvalitet, fra underholdningsmusikk til st�rre originalskrevne og klassiske verk. I dag teller vi dr�yt

    66 medlemmer, men
    vi satser stort p� rekruttering og h�per � f� med deg som medlem! 
    </p><p>

    <img width="100" src="../bilder/medlemsfoto/Tomas_2.jpg" style="float:left;margin-right:8px" alt="Tomas Carstensen">
    Siden h�sten 2000 har Tomas Carstensen v�rt v�r faste dirigent. Han har utdannelse som trompetist
    og dirigent fra Musikkonservatoriet i Trondheim.
    </p><p>

    Korpset har i mange �r hevdet seg i toppen av norsk 1. divisjon i NM janitsjar (<a href="meritter.php">se resultater</a>). Under NM i 2006 vant 
    korpset 1. divisjon, og konkurrerte i elitedivisjonen i to �r. I perioden 2009-2011 konkurrerte vi i 1. divisjon, og fra 2012 har korpset konkurrert i 2. divisjon
    V�r musikalske m�lsetting er � ha full Symphonic Band-besetning, og � v�re et ungdomskorps i ordets rette forstand. Bispehaugen �nsker ikke � bli plassert i en bestemt b�s, men skal
    kjennetegnes av allsidighet og seri�s satsing innen flere omr�der.
    </p><p>

    <img width="180" src="../bilder/frostaseminar.jpg" style="float:right;margin-left:7px;margin-bottom:8px" alt="Seminar p� Frosta">
    Bispehaugen legger vekt p� et godt sosialt milj�� b�de i og utenfor �vingslokalet. Korpset har utenommusikalske aktiviteter som
    kaf�bes�k, fester og helgeturer utenbys. Medlemmene v�re blir i stor grad rekruttert fra studentmilj�et i Trondheim, noe som setter standarden for v�re utenommusikalske aktiviteter.
    </p><p>

    <img src="../bilder/fest.jpg" width="180" height="138" style="float:left;margin-right:8px">
    Vi �ver mandager fra 19:30 p� Bispehaugen skole p� M�llenberg, sentralt i Trondheim. Det blir ogs� noen ekstra�velser f�r
    konsertene. Dersom du er interessert i hva vi holder p� med, kan du ta turen innom en av �velsene, eller g� inn p� <a href="../kontakt/blimedlem.php">kontaksskjema</a>.
    </p>

    <div class="clearfix"></div>
</div>
</section>
    	
<section class="side kontakt" data-scroll-index='8'>
	<a name="kontakt"></a>
	<div class="content">
		<h2>Kontakt oss</h2>
		
		
		<section class="kontakt-float">
			<h4>E-post</h4>
			<p><script type="text/javascript">document.write("<a href=\"mailto:sty" + "ret" + "@" + "bispe" + "haugen" + "." + ".no\">");</script>styre<span class="hidden">EAT THIS ROBOTS</span>@bispehaugen.no</a></p>
			
			<h4>Telefon leder</h4>
			<p><a href="tel:+47<?php echo $leder['tlfmobil']; ?>">+47 <?php echo $leder['tlfmobil']; ?></a></p>
		
			<a href="https://www.facebook.com/BispehaugenUngdomskorps" title="Bes�k BUK p� Facebook">
				<svg class='svg-icon' version="1.1" id="FacebookIcon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 width="56.693px" height="56.693px" viewBox="0 0 56.693 56.693" enable-background="new 0 0 56.693 56.693" xml:space="preserve">
					<path d="M40.43,21.739h-7.645v-5.014c0-1.883,1.248-2.322,2.127-2.322c0.877,0,5.395,0,5.395,0V6.125l-7.43-0.029
						c-8.248,0-10.125,6.174-10.125,10.125v5.518h-4.77v8.53h4.77c0,10.947,0,24.137,0,24.137h10.033c0,0,0-13.32,0-24.137h6.77
						L40.43,21.739z"/>
				</svg>
	
			</a>
			<a href="https://twitter.com/Bispehaugen" title="F�lg BUK p� Twitter">
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
			<h4>Bes�ksadresse</h4>
			<p>
				Bispehaugen skole<br />
				7014 Trondheim
			</p>
			
			<div class="pil-ned"></div>
		</section>
	</div>
</section>

<?php
}



