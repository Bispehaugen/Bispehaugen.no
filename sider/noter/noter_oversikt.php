<?php
	//funksjonalitet
	
	//TODO lage dynamisk sortering, gransesnitt for å legge inn nye noter
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	//henter ut alle stykkene i db sortert etter tittel og evt. filtrert på konsert hvis konsert er valgt
	//(Bruker tabellene noter_konsert og noter_notesett)
	$konsertid= has_get('arrid') ? get('arrid') : "alle";
	
	$notesett = hent_noter($konsertid);
	
	//henter alle konsertene
	$sql="SELECT DISTINCT noter_konsert.arrid, tittel, dato FROM noter_konsert, arrangement 
	WHERE noter_konsert.arrid=arrangement.arrid ORDER BY dato DESC;";
	$konserter=hent_og_putt_inn_i_array($sql,'arrid');
	
	echo "<h2>Noter</h2>";

	//printer ut det som skal vises på sida
	
	//form med muligheter for å velge ut noter til en konsert
	echo" <form class='forum' method='get' action='?side=noter/noter_oversikt'>
			<input type='hidden' name='side' value='noter/noter_oversikt' />
				<p>Vis noter for en bestemt konsert:
					<select name='arrid' onchange='this.form.submit()'>
							<option value='alle'>Vis alle noter</option>";
					foreach($konserter as $konsert){
						echo"<option value=".$konsert['arrid'];
							//sjekk for om det er valgte konsert
							if($konsertid==$konsert['arrid']){echo " selected ";}
						echo">".$konsert['tittel']." ".date('Y', strtotime($konsert['dato']))."</option>";};
	echo "												  							
  					</select></p>
			</form> ";

	echo"<table>
				<tr><th>Tittel</th><th>Komponist:</th><th>Arrangør:</th><th>Besetning:</th><th>Arkivnr.</th><th></th></tr>";
	//liste med notesettene
	foreach($notesett as $sett){
		echo"<tr>
				<td><a href='..".$sett['filpath']."'>".$sett['tittel']."<a></td>
				<td>".$sett['komponist']."</td>
				<td>".$sett['arrangor']."</td>
				<td>".$sett['besetningstype']."</td>
				<td>";
					if($sett['arkivnr']!='0'){
						echo $sett['arkivnr'];
					};
					echo "</td>";
				if(isset($_SESSION['rettigheter']) && $_SESSION['rettigheter']>1){
					echo"<td><a href='?side=noter/noter_endre&noteid=".$sett['noteid']."'><i class='fa fa-edit' 
					title='Klikk for å endre'></i></a></td></tr>";
				}else{
					echo "<td></td></tr>";
				};
			echo"
			</tr>"
		;
	};
	if(isset($_SESSION['rettigheter']) && $_SESSION['rettigheter']>1){
			echo"
			<tr><td colspan='5'></td></tr>
			<tr><td colspan='5'></td></tr>
			<tr><td colspan='5'><a href='?side=noter/noter_endre'><i class='fa fa-plus'></i>Legg til nytt notesett</a></td></tr>";
	}
		
	echo"</table>";
?>