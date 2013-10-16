<?php
    
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

    #Det som printes på sida
    echo "<table><th>Dato:</th><th>Tid:</th><th>Arrangement:</th><th colspan='2'>Sted:</th>
    	
    	<script type='text/javascript'>
			function slett_aktivitet(id,tittel){
				var ask = confirm('Vil du slette ..... ?');
				if(!!ask){
					window.location = '?side=aktiviteter/slette&id='+id;
				}
			}
		</script>";
  
   	foreach($aktiviteter as $aktivitet){
 			#aktiviteten printes i bold hvis den er valgt
   			if($valgt_id==$aktivitet['arrid']){
   				echo "<tr class='valgt'>";
   			}else{
   				echo "<tr>";
   			};
 
 			echo "<td>".strftime("%a %#d. %b", strtotime($aktivitet['dato']));
			
			#hvis tildato er satt eller lik
 			if($aktivitet['tildato'] > $aktivitet['dato']){
 				echo " - ".strftime("%a %#d. %b", strtotime($aktivitet['tildato']));
 			}
 
   			if($aktivitet['starttid']=="00:00:00"){
   				echo "</td><td></td><td>
   				<a href='?side=aktiviteter/liste&id=".$aktivitet['arrid']."'>".$aktivitet['tittel']."</a></td><td>".$aktivitet['sted']."</td>";
			}else{
				echo "<tr><td>".strftime("%a %#d. %b", strtotime($aktivitet['dato']))."</td><td>".
   				strftime("%H:%M", strtotime($aktivitet['starttid']))."</td><td><a href='?side=aktiviteter/liste&id=".$aktivitet['arrid']."'>
   				".$aktivitet['tittel']."</a>
   				</td><td>".$aktivitet['sted']."</td>";
			}

			#Viser endre/slettkapper hvis man er admin
			if($_SESSION['rettigheter']>1){
				echo"<td><a href='?side=aktiviteter/endre&id=".$aktivitet['arrid']."'>endre</a> / <a href='#' onclick='slett_aktivitet(".$aktivitet['arrid'].",\"
				".$aktivitet['tittel']."\")'>slett</a></td></tr>";
			}else{
				echo "<td></td></tr>";
			};
			
			//Viser mer info hvis trykket på en hendelse
			if($valgt_id==$aktivitet['arrid']){
				echo" <tr><td></td><td class='info' colspan='4'> 
					Varighet: ".strftime("%H:%M", strtotime($aktivitet['dato']))."kl ".strftime("%H:%M", strtotime($aktivitet['dato']))." til ";
					
					if($aktivitet['tildato'] > $aktivitet['dato']){echo strftime("%H:%M", strtotime($aktivitet['dato']));};
					
					echo strftime("%a %#d. %b", strtotime($aktivitet['sluttid']))."
					<br>Kakebaker: ".$kakebaker['fnavn']."
					<br>Bæregruppe: ".$aktivitet['hjelpere']."</td></tr>";
			};
		}
		
		if($_SESSION['rettigheter']>1){
			echo"
			<tr><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td></td><td></td><td></td><td></td><th><a href='?side=aktiviteter/endre'>legg til ny</a></th></tr>";
		}
		echo "</table>";
	
	
    
?>