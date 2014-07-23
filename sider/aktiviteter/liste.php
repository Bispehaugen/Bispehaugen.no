<?php
    setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
    //TODO legge inn googlecal og ical eksport
        
    #fuksjonalitet

    
    //spørring som henter ut alle aktiviteter
	$aktiviteter=hent_aktiviteter();
	
	$valgt_id = get('id');

	//henter kakebaker hvis det er noen
	if($valgt_id){
		$sql="SELECT fnavn, enavn, medlemsid, arrid, kakebaker FROM medlemmer, arrangement WHERE arrid = ".$valgt_id." AND kakebaker=medlemsid";
		$kakebaker=hent_og_putt_inn_i_array($sql);
	};
	
	echo "<h2 class='aktivitetsliste-overskrift'>Aktiviteter</h2>";

	if(session('rettigheter')>1){
		echo"<h3 class='legg-til-aktivitet'><a href='?side=aktiviteter/endre'><i class='fa fa-plus'></i> Legg til ny</a></h3>";
	}

	echo "<script type='text/javascript'>
			function slett_aktivitet(id,tittel){
				var ask = confirm('Vil du slette ..... ?');
				if(!!ask){
					window.location = '?side=aktiviteter/slette&id='+id;
				}
			}
		</script>";

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
 			if(dato("d", $aktivitet['slutt']) !== dato("d", $aktivitet['start'])){
 				echo " - ".strftime("%a %#d. %b", strtotime($aktivitet['slutt']));
			}
			echo "</td>";
 
   			if($aktivitet['start']=="0000-00-00 00:00:00"){
   				echo "<td></td><td>
   				<a href='?side=aktiviteter/liste&id=".$aktivitet['arrid']."'>".$aktivitet['tittel']."</a></td><td>".$aktivitet['sted']."</td>";
			}else{
				echo "<td>".strftime("%H:%M", strtotime($aktivitet['start']))."</td><td><a href='?side=aktiviteter/liste&id=".$aktivitet['arrid']."'>
   				".$aktivitet['tittel']."</a>
   				</td><td>".$aktivitet['sted']."</td>";
			}

			#Viser endre/slettkapper hvis man er admin
			if(session('rettigheter')>1){
				echo"<td><a href='?side=aktiviteter/endre&id=".$aktivitet['arrid']."'><i class='fa fa-edit' 
				title='Klikk for å endre'></i></a> / <a href='#' onclick='slett_aktivitet(".$aktivitet['arrid'].",\"
				".$aktivitet['tittel']."\")'><i class='fa fa-times' title='Klikk for å slette'></i></a></td></tr>";
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
					echo "<p>Kakebaker: ".$aktivitet['hjelpere'] . "</p>";
				}
				echo "</td></tr>";
			}
		}
		echo "</table>";