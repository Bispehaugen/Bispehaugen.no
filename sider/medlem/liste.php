<?php
    
    //SQL-sp�rringen som henter ut alt fra "instrumenter" og "medlemmer" i DB
    //sjekker om man er logget inn for å vise "begrensede" medlemmer (som ikke vil vises eksternt)
    if(er_logget_inn() && $_GET['alle']==1){
   		$sql="SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
    	medlemmer.instrument, instrument.* FROM medlemmer,instrument WHERE instrumentid LIKE instnr ORDER BY posisjon, 
    	grleder  desc, status, fnavn";
	}
	elseif(er_logget_inn()){
    	$sql="SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
    	medlemmer.instrument, instrument.* FROM medlemmer,instrument WHERE status!='sluttet' AND instrumentid LIKE instnr 
    	ORDER BY posisjon, grleder desc, status, fnavn";
	}else{
    	$sql="SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
    	medlemmer.instrument, instrument.* FROM medlemmer,instrument WHERE status!='sluttet' AND begrenset=0 AND 
    	instrumentid LIKE instnr ORDER BY posisjon, grleder desc, status, fnavn";
	};

	$mysql_result=mysql_query($sql);
    $medlemmer = Array();
	while($row=mysql_fetch_array($mysql_result)){
		//print_r($row);
    	$medlemmer[$row['medlemsid']] = $row;
	};

	//sp�rring som henter ut medlemsid til alle styrevervene
	$sql="SELECT vervid, tittel, medlemsid, epost FROM verv WHERE komiteid='3'";
	$mysql_result=mysql_query($sql) or die(mysql_error());
	$styreverv = Array();

	while($row=mysql_fetch_array($mysql_result)){
    	$styreverv[$row['medlemsid']] = $row;
	};
	
    
    #Det som printes p� sida
    
    //lager en link til å vise alle
    if(er_logget_inn() && $_GET['alle']==0){
    	echo" <a href='?side=medlem/liste&alle=1'>Vis sluttede også</a>";
    }if(er_logget_inn() && $_GET['alle']==1){
    	echo" <a href='?side=medlem/liste&alle=0'>Vis kun aktive</a>";
    }
    
    echo "<table>";
	#Brukes for � skrive ut en rad med instrumentnavn.
	$temp_instr="Test";

   	foreach($medlemmer as $medlem){
   		// sjekker på status så alle aktive medlemmer skrives ut først
   		if($medlem['status']=="Aktiv" || $medlem['status']=="Permisjon" || $medlem['status']=="Sluttet"){
   			$instr=$medlem['instrument'];
   			//sjekker om instrument er samme som forrige, hvis nei skives ut en headerlinje med instrumentnavn
   			if($temp_instr!=$instr){
   				echo "<tr><th colspan='5'>".$medlem['instrument']."</th></tr>";
				$temp_instr=$medlem['instrument'];
	   			};
       		echo "<tr><td><a href='?side=medlem/vis&id=".$medlem['medlemsid']."'>".$medlem['fnavn']." ".$medlem['enavn']."</a>";
				//sjekker om permisjon eller sluttet - i så fall printes en bokstav etter navnet
				if($medlem['status']=='Permisjon'){
   					echo " (P) ";
   				};
				if($medlem['status']=='Sluttet'){
   					echo " (S) ";
   				};
				//sjekker på gruppeleder og skriver ut dette etter navnet hvis ja
       			if($medlem['grleder']){
       				echo " - Gruppeleder";
       			};
				
       		echo "</td><td>";
				//sjekker om medlemmet er i styret, hvis ja kommer en "send mail" link bak navnet
       			if($styreverv[$medlem['medlemsid']]){
       				echo $styreverv[$medlem['medlemsid']]['tittel']." </td><td class='hoyrestilt'>
       				<a href='mailto:".$styreverv[$medlem['medlemsid']]['epost']."'>send e-post</a>";
				}else{
					echo "</td><td></td>";
				};
			if(er_logget_inn() && $_GET['alle']==0){
					//hvis man er logget inn vises mobilnummeret til alle medlemmer
					echo "<td>".$medlem ['tlfmobil']."</td>";
			}
			else{
				echo"<td></td>";	
			};
			
		}

		//hvis brukeren er admin kommer det opp endre/slette knapp på alle medlemmer
		if($_SESSION['rettigheter']>1){
				echo"<td><a href='?side=medlem/endre&id=".$medlem['medlemsid']."'>endre</a> / <a href=
				'?side=medlem/slette&id=".$medlem['medlemsid']."'onclick='slett_medlem(".$medlem['medlemsid'].
				",\"".$medlem['fnavn']."\")'>slett</a></td></tr>";
		}else{
			echo"<td></td></tr>";
		};
	};    

	if($_SESSION['rettigheter']>1){
			echo"
			<tr><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td></td><td></td><td></td><td></td><th><a href='?side=medlem/endre'>legg til ny</a></th></tr>";
		}
	echo "</table>";
    
?>