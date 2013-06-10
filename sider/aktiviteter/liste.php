<?php
    
    //TODO legge inn googlecal og ical eksport
        
    #fuksjonalitet
    
    //spï¿½rring som henter ut alle aktiviteter
    if($_SESSION['rettigheter']==0 || !er_logget_inn()){
		$sql="SELECT * FROM medlemmer, `arrangement` WHERE dato >= CURDATE() AND slettet=false AND public = 1 ORDER BY dato, starttid; ";
	}elseif($_SESSION['rettigheter']==1){
		$sql="SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND slettet=false AND public < 2 ORDER BY dato, starttid; ";
	}else{
		$sql="SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND slettet=false ORDER BY dato, starttid; ";		
	}
	$aktiviteter=hent_og_putt_inn_i_array($sql, $id_verdi="arrid");
	
	$valgt_id=$_GET['id'];

	//henter kakebaker hvis det er noen
	if(isset($valgt_id)){
		$sql="SELECT fnavn, enavn, medlemsid, arrid, kakebaker FROM medlemmer, arrangement WHERE arrid = ".$valgt_id." AND kakebaker=medlemsid";
		$kakebaker=hent_og_putt_inn_i_array($sql);
	};

    #Det som printes pï¿½ sida
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
   			if($aktivitet['starttid']=="00:00:00"){
   				echo "<tr><td>".strftime("%a %#d. %b", strtotime($aktivitet['dato']))."</td><td></td><td>
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
				echo" <tr><td ></td><td class='aktivitet' colspan='4'> Beskrivelse: ".strftime("%a %#d. %b", strtotime($aktivitet['oppmote']))."
					<br> Varighet: ".strftime("%a %#d. %b", strtotime($aktivitet['starttid']))." til ".strftime("%a %#d. %b", strtotime($aktivitet['sluttid']))."
					<br> Oppmøte: ".strftime("%a %#d. %b", strtotime($aktivitet['oppmote']))."
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