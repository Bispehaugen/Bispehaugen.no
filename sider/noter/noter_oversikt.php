<?php
	//funksjonalitet
	
	//TODO lage dynamisk sortering, gransesnitt for Ã¥ legge inn nye noter
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	//henter ut alle stykkene i db sortert etter tittel og evt. filtrert på konsert hvis konsert er valgt
	//(Bruker tabellene noter_konsert og noter_notesett)
	$konsertid=post('arrid');
	
	if(has_post('arrid') && $konsertid!='alle'){
		
		$sql="SELECT * FROM noter_notesett, noter_konsert, noter_besetning 
		WHERE arrid=".$konsertid." AND noter_notesett.noteid=noter_konsert.noteid AND noter_notesett.besetningsid=noter_besetning.besetningsid ORDER BY tittel;";
	}
	else{
		$sql="SELECT * FROM noter_notesett, noter_besetning WHERE noter_notesett.besetningsid=noter_besetning.besetningsid ORDER BY tittel;";
	};
	$notesett=hent_og_putt_inn_i_array($sql,'noteid');
	
	//henter alle konsertene
	$sql="SELECT DISTINCT noter_konsert.arrid, tittel, dato FROM noter_konsert, arrangement 
	WHERE noter_konsert.arrid=arrangement.arrid ORDER BY dato DESC;";
	$konserter=hent_og_putt_inn_i_array($sql,'arrid');
	
	//printer ut det som skal vises på sida
	echo"<table>";
	
	//form med muligheter for å velge ut noter til en konsert
	echo" <form class='forum' method='post' action='?side=noter/noter_oversikt'>
				<tr><td>Konsert:</td><td>
					<select name='arrid'>
							<option value='alle'>alle noter</option>";
					foreach($konserter as $konsert){
						echo"<option value=".$konsert['arrid'];
							//sjekk for om det er valgte konsert
							if($konsertid==$konsert['arrid']){echo " selected ";}
						echo">".$konsert['tittel']." ".$konsert['dato']."</option>";};
	echo "												  							
  					</select></td>
  				<td><input type='submit' name='nyttInnlegg' value='finn noter!'></td></tr>
			</form> 
				<tr><td></td><td></td></tr>
				<tr><th>Tittel</th><th>Komponist:</th><th>Besetning:</th><th>Arkivnummer</th></tr>";
	//liste med notesettene
	foreach($notesett as $sett){
		echo"<tr>
				<td><a href='../..".$sett['filpath']."'>".$sett['tittel']."<a></td>
				<td>".$sett['komponist']."</td>
				<td>".$sett['besetningstype']."</td>
				<td>".$sett['arkivnr']."</td>
			</tr>
		";
	};
	echo"</table>";
?>