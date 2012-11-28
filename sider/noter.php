<?php
	//funksjonalitet
	
	//TODO lage dynamisk sortering, gransesnitt for å legge inn nye noter
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	echo"<table>
			<form method='get'>
				<tr><td>Konsert:</td><td>
					<select name='instid'>
  							<option value='id'>Adventskonsert 2008</option>
  							<option value='id'>Høstkonsert 2008</option>
   							<option value='id'>NM 2008</option>
  							<option value='id'>TM 2010</option>
   							<option value='id'>Oppvarmingshefter</option>  							  							
  					</select></td>
			</form> 
			<form method='get'>
				<td>Type arr:</td><td>
					<select name='instid'>
  							<option value='id'>Fullt korps</option>
  							<option value='id'>Solostykker</option>
   							<option value='id'>Tyrolder</option>
  							<option value='id'>Storband</option>
  					</select></td></tr>
			</form> 
				<tr><td></td><td></td></tr>
				<tr><th>Tittel</th><th>Komponist:</th><th>Del av verk:</th><th>Type arr</th></th>
				<tr><td>Symfoni nr. 5</td><td></td><td></td><td>Fullt arr</td></tr>
				<tr><td>Glade jul</td><td></td><td></td><td>Julefest</td></tr>
				<tr><td>Intrada</td><td></td><td></td><td>Fullt arr</td></tr>
				<tr><td>Oppvarmingshefter</td><td></td><td></td><td>Fullt arr</td></tr>
				<tr><td>Trombonesolo</td><td></td><td></td><td>Fullt arr</td></tr>								
		</table>
		
		
	";
?>