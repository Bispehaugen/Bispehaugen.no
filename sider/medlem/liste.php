<?php
    
    //SQL-spørringen som henter ut alt fra "instrumenter" og "medlemmer" i DB
    //sjekker om man er logget inn for å vise "begrensede" medlemmer (som ikke vil vises eksternt)
    if(er_logget_inn() && get('alle')==1){
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

    $medlemmer = hent_og_putt_inn_i_array($sql, $id_verdi="medlemsid");

	//spørring som henter ut medlemsid til alle styrevervene
	$sql="SELECT vervid, tittel, medlemsid, epost FROM verv WHERE komiteid='3'";
	$styreverv = hent_og_putt_inn_i_array($sql, $id_verdi="medlemsid");
    
    #Det som printes p� sida
    
    //lager en link til å vise alle
    if(er_logget_inn() && get('alle')==0){
    	echo" <a href='?side=medlem/liste&alle=1'>Vis sluttede også</a>";
    }if(er_logget_inn() && get('alle')==1){
    	echo" <a href='?side=medlem/liste&alle=0'>Vis kun aktive</a>";
    }
    
    echo "<h2>Medlemmer</h2>";
    
    echo "<section class='medlemsliste'>";
	#Brukes for å skrive ut en rad med instrumentnavn.
	$temp_instr="Test";

   	foreach($medlemmer as $medlem){
   		// sjekker på status så alle aktive medlemmer skrives ut først
   		if($medlem['status']=="Aktiv" || $medlem['status']=="Permisjon" || $medlem['status']=="Sluttet"){
   			$instr=$medlem['instrument'];
   			//sjekker om instrument er samme som forrige, hvis nei skives ut en headerlinje med instrumentnavn
   			if($temp_instr!=$instr){
   				echo "<h3>".$medlem['instrument']."</h3>";
				$temp_instr=$medlem['instrument'];
   			}
       		echo "<section class='medlem'>";
       		
       		echo "<span class='navn'><a href='?side=medlem/vis&id=".$medlem['medlemsid']."'>".$medlem['fnavn']." ".$medlem['enavn']."</a></span>";
				//sjekker om permisjon eller sluttet - i så fall printes en bokstav etter navnet
				if($medlem['status']!='Aktiv'){
   					echo "<span class='tag permisjon'>".$medlem['status']."</span>";
   				}
				//sjekker på gruppeleder og skriver ut dette etter navnet hvis ja
       			if($medlem['grleder'] == true){
       				echo "<span class='tag gruppeleder'>Gruppeleder</span>";
       			}

			//sjekker om medlemmet er i styret, hvis ja kommer en "send mail" link bak navnet
			$medlemsid = $medlem['medlemsid'];
   			if(!empty($medlemsid) && !empty($styreverv) && !empty($styreverv[$medlemsid])){
   				echo "<span class='epost-lenke'><a href='mailto:".$styreverv[$medlemsid]['epost']."'><i class='fa fa-envelope-alt' title='Send e-post'></i> ".$styreverv[$medlemsid]['tittel']."</a></span>";
			}


			if(!er_logget_inn() && get('alle')==0 && $medlem ['tlfmobil']){
				$flere_telefonnummer = explode("/", $medlem['tlfmobil']);
				
				foreach($flere_telefonnummer as $telefonnummer) {
					//hvis man er logget inn vises mobilnummeret til alle medlemmer
					echo "<span class='telefon'><a href='tel:".$telefonnummer."'><i class='fa fa-phone'></i> ".$telefonnummer."</a></span>";
				}
			}
		}

		//hvis brukeren er admin kommer det opp endre/slette knapp på alle medlemmer

		if(isset($_SESSION['rettigheter']) && $_SESSION['rettigheter']>1){
				echo"<span class='verktoy'><a href='?side=medlem/endre&id=".$medlem['medlemsid']."'><i class='fa fa-edit' title='Klikk for å endre'></i></a></span>";
		}
		echo "<div class='clearfix'></div>";
		echo "</section>";
	}    

	if(isset($_SESSION['rettigheter']) && $_SESSION['rettigheter']>1){
			echo "<a href='?side=medlem/endre'><i class='fa fa-plus'></i> Legg til ny</a>";
		}
	echo "</section>";
    
?>