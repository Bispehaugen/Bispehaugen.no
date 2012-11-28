<?php
    
    //TODO legge inn googlecal og ical eksport
        
    #fuksjonalitet
    
    //sp�rring som henter ut alle aktiviteter
    if($_SESSION['rettigheter']==0){
		$sql="SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND public = 1 ORDER BY dato, starttid ";
	}elseif($_SESSION['rettigheter']==1){
		$sql="SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND public < 2 ORDER BY dato, starttid ";
	}else{
		$sql="SELECT * FROM `arrangement` WHERE dato >= CURDATE() ORDER BY dato, starttid ";		
	}
	$mysql_result=mysql_query($sql);
	$aktiviteter = Array();

	while($row=mysql_fetch_array($mysql_result)){
		//print_r($row);
    	$aktiviteter[$row['arrid']] = $row;
	};
		
    #Det som printes p� sida
    echo "<table><th>Dato:</th><th>Tid:</th><th>Arrangement:</th><th colspan='2'>Sted:</th>
    	
    	<script type='text/javascript'>
			function slett_aktivitet(id,tittel){
				var ask = confirm('Vil du slette ..... ?');
				if(ask){
					window.location = '?p=aktiviteter/slette&id='+id;
				}
			}
		</script>";
  
   	foreach($aktiviteter as $aktivitet){
   			if($aktivitet['starttid']=="00:00:00"){
   				echo "<tr><td>".strftime("%a %#d. %b", strtotime($aktivitet['dato']))."</td><td></td><td>".$aktivitet['tittel']."
   				</td><td>".$aktivitet['sted']."</td>";
			}else{
				echo "<tr><td>".strftime("%a %#d. %b", strtotime($aktivitet['dato']))."</td><td>".
   				strftime("%H:%M", strtotime($aktivitet['starttid']))."</td><td>".$aktivitet['tittel']."
   				</td><td>".$aktivitet['sted']."</td>";
			}
			#Viser endre/slettkapper hvis man er admin
			if($_SESSION['rettigheter']>1){
				echo"<td><a href='?side=aktiviteter/endre&id=".$aktivitet['arrid']."'>endre</a> / <a href=
				'?side=aktiviteter/slette&id=".$aktivitet['arrid']."'onclick='slett_aktivitet(".$aktivitet['arrid'].
				",\"".$aktivitet['tittel']."\")'>slett</a></td></tr>";
			};
		}

		if($_SESSION['rettigheter']>1){
			echo"
			<tr><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td></td><td></td><td></td><td></td><th><a href='?side=aktiviteter/endre'>legg til ny</a></th></tr>";
		}
		echo "</table>";
	
	
    
?>