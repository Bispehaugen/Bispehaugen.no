<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
global $antall_nyheter;
$antall_nyheter = 4;

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
<section class="side spilleoppdrag" data-scroll-index='4'>
  <div class='content'>
    <?php
		inkluder_side_fra_undermappe("spilleoppdrag/vis");
	?>
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

<?php

inkluder_side_fra_undermappe("annet");

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
<h3>Dette er informasjonen vi har om deg:</h3>
</section>
	
<?php
}



