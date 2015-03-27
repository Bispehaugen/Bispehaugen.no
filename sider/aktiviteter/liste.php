<?php
    setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
    //TODO legge inn googlecal og ical eksport
        
    #fuksjonalitet

    
    //spørring som henter ut alle aktiviteter
    $alle=0;
    $alle=get('alle');
	$aktiviteter=hent_aktiviteter("","",$alle);
	
	$valgt_id = get('id');

	//henter kakebaker hvis det er noen
	if($valgt_id){
		$sql="SELECT fnavn, enavn, medlemsid, arrid, kakebaker FROM medlemmer, arrangement WHERE arrid = ".$valgt_id." AND kakebaker=medlemsid";
		$kakebaker=hent_og_putt_inn_i_array($sql);
	};
	
	//henter konserter
	if($valgt_id){
		$sql="SELECT * FROM konserter";
		$konserter=hent_og_putt_inn_i_array($sql, "arrid_konsert");
	};
	
	echo "<h2 class='overskrift-som-er-inline-block'>Aktiviteter</h2>";

	if(session('rettigheter')>1){
		echo"<h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=aktiviteter/endre'><i class='fa fa-plus'></i> Legg til ny aktivitet</a></h3>";
		echo"<h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=konsert/endre'><i class='fa fa-plus'></i> Legg til ny konsert</a></h3>";
	}
	if(get('alle')==0){
	    	echo" <h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=aktiviteter/liste&alle=1'><i class='fa fa-calendar'></i> Vis tidligere</a></h3>";
	 	} else {
	     	echo"<h3 class='lenke-som-er-inline-med-overskrift'> <a href='?side=aktiviteter/liste&alle=0'><i class='fa fa-calendar'></i> Vis bare kommende</a></h3>";
		}
	?>
	<h3 class='lenke-som-er-inline-med-overskrift'><a href='http://www.google.com/calendar/render?cid=http://bispehaugen.no/ical.php<?php if(er_logget_inn()) { echo "?p=bukaros"; } ?>'><i class='fa fa-cloud-download '></i> Legg til i google calendar</a></h3>
	
	<script type='text/javascript'>
		function slett_aktivitet(){
			var id = $(this).data("id");
			var tittel = $(this).data("title").replace(/\\/g, '');

			var ask = confirm('Vil du slette ' + tittel + '?');
			if(!!ask){
				window.location = '?side=aktiviteter/slette&id='+id;
			}
		}

		$(function() {
			$(".slett-aktivitet").click(slett_aktivitet);
		});
	</script>
	<?php

    #Det som printes på sida
    echo "<table class='aktivitetsliste'>
    <thead><tr><th colspan=2>Dato:</th><th>Tid:</th><th>Arrangement:</th><th colspan='2'>Sted:</th></tr></thead>";
	
	$forrigeAktivitetesAar = date("Y");
    
   	foreach($aktiviteter as $aktivitet){
   		
			$startdatosAar = date("Y", strtotime($aktivitet['start']));
			if ($startdatosAar != $forrigeAktivitetesAar) {
				echo "<tr><td colspan=6><h4 class='aarskille'>".$startdatosAar."</h4></td></tr>";
				$forrigeAktivitetesAar = $startdatosAar;
			}
		
 			#aktiviteten printes i bold hvis den er valgt
   			if($valgt_id==$aktivitet['arrid']){
   				echo "<tr class='valgt'>";
   			}else{
   				echo "<tr>";
   			};
 			echo "<td>".strftime("%a", strtotime($aktivitet['start']))."</td>";

 			echo "<td>".strftime("%#d. %b", strtotime($aktivitet['start']));
			
			#hvis tildato er satt eller lik
 			if((dato("d", $aktivitet['slutt']) == dato("d", $aktivitet['start']))||($aktivitet['slutt']=="0000-00-00 00:00:00")){
				echo "";
			}else{
 				echo " - ".strftime("%a %#d. %b", strtotime($aktivitet['slutt']));
			}
			echo "</td>";
 
   			if($aktivitet['start']=="0000-00-00 00:00:00"){
   				echo "<td></td><td>
   				<a href='?side=aktiviteter/liste&id=".$aktivitet['arrid']."'>".$aktivitet['tittel']."</a></td><td>".$aktivitet['sted']."</td>";
			}else{
				echo "<td>".strftime("%H:%M", strtotime($aktivitet['start']))."</td><td><a href='?side=aktiviteter/liste&id=".$aktivitet['arrid']."&alle=".$alle."'>
   				".$aktivitet['tittel']."</a>
   				</td><td>".$aktivitet['sted']."</td>";
			}

			#Viser endre/slettkapper hvis man er admin
			if(session('rettigheter')>1){
				if($aktivitet['type']=="Konsert"){
					echo"<td><a href='?side=konsert/endre&id=".$aktivitet['arrid']."'><i class='fa fa-edit' 
					title='Klikk for å endre'></i></a> </td></tr>";
				}else{
					echo"<td><a href='?side=aktiviteter/endre&id=".$aktivitet['arrid']."'><i class='fa fa-edit' 
					title='Klikk for å endre'></i></a> / <a href='#' class='slett-aktivitet' data-id='".$aktivitet['arrid']."' data-title='".addslashes($aktivitet['tittel'])."'><i class='fa fa-times' title='Klikk for å slette'></i></a></td></tr>";
				}
			}else{
				echo "<td></td></tr>";
			};

			//Viser mer info hvis trykket på en hendelse
			if($valgt_id==$aktivitet['arrid']){
				echo" <tr><td></td><td class='info' colspan='4'>";

				if ($aktivitet['start'] != $aktivitet['slutt']) {
					echo "<p>Varighet: kl " . dato("H:i", $aktivitet['start'])." til ";
					
					if(dato("d", $aktivitet['slutt']) == dato("d", $aktivitet['start'])){
						echo dato("H:i", $aktivitet['slutt']);
					} else {
						echo "kl. ".dato("H:m d.m.Y", $aktivitet['slutt']);
					}
					echo "</p>";
				}
				
				if (!empty($kakebaker)) {
					echo "<p>Kakebaker: " . brukerlenke($kakebaker) . "</p>";
				}

				if (!empty($aktivitet['hjelpere'])) {
					echo "<p>Slagverksbærere: ".$aktivitet['hjelpere'] . " <a href='?side=intern/organisasjon&slagverksgrupper=1'>(trykk her for å se slagverksbæregruppene)</a></p>";
				}
				
				if ($aktivitet['type']=="Konsert") {
					echo "<p>".$aktivitet['ingress']." - <a href='?side=konsert/vis&id=".$konserter[$aktivitet['arrid']]['nyhetsid_konsert']."'>les mer om konserten...</a></p>";
				}
				
				echo "</td></tr>";
			}
		}
		echo "</table>";