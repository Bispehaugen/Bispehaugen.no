<?php
	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	//henter ut alle komiteer
	$sql="SELECT komiteid, navn, mail_alias FROM komite ORDER BY posisjon";
	$mysql_result=mysql_query($sql) or die(mysql_error());
	$komiteer = Array();

	while($row=mysql_fetch_array($mysql_result)){
    	$komiteer[$row['komiteid']] = $row;
	};
	
	//henter ut info om medlemmer++ om valgte komitÃ©
	if(isset($_GET['id'])){
		$sql="SELECT komite.komiteid, verv.komiteid, navn, vervid, verv.posisjon, komite.posisjon, tittel, medlemmer.medlemsid, 
		verv.medlemsid, epost, fnavn, enavn  FROM komite, verv, medlemmer WHERE medlemmer.medlemsid=verv.medlemsid AND 
		komite.komiteid=verv.komiteid AND komite.komiteid=".$_GET['id']." ORDER BY komite.posisjon, verv.posisjon";
		$mysql_result=mysql_query($sql) or die(mysql_error());
		$valgtekomiteer = Array();
	
		while($row=mysql_fetch_array($mysql_result)){
	    	$valgtekomiteer[$row['vervid']] = $row;
		};
	};
	
	//det som skrives ut pÃ¥ side	
	
	echo"<table>
			<tr><th colspan='4'>Kjekt å vite: </th></tr>
			<tr><td><b>Fravær:</b></td><td colspan='3'>Fravær meldes til nestleder på e-post eller sms. <br>
			Du kan også fylle ut skjemaet under så autogenereres en mail for deg</td></tr>
			<form method='post' action='?side=oranisasjon'>
				<tr><td></td><td>Hvilken øvelse:</td><td colspan='2'><input type='textfield' name='tekst'></td></tr>
				<tr><td></td><td>Grunn:</td><td colspan='2'><input type='textfield' name='tekst'></td></tr>
				<input type='hidden' name='medlemsid' value=".$_SESSION['medlemsid'].">
				<tr><td colspan='3'></td><td><input type='submit' name='nyttInnlegg' value='Send'></td></tr>
			</form></td><td colspan='2'></td></tr>
			<tr><td><b>Permisjon:</b></td><td colspan='3'>Søknad om permisjon sendes på e-post til styret. 
				Husk å oppgi periode og årsak til permisjonen.</td></tr>
								
			<tr><th colspan='4'>Kontaktinformasjon</th></tr>
			<tr><td><b>Adresse:</b></td><td colspan='3'>
				Bispehaugen Ungdomskorps<br>
				Postboks 9012<br>
				Rosenborg
				7455 Trondheim<br> </td></tr>
			<tr><td><b>Organisasjonnummer:</b></td><td colspan='2'>975.729.141</td></tr>
			<tr><td><b>Kontonummer:<br><br></b></td><td colspan='2'>4200 07 51280<br><br></td></tr>
			<tr><td colspan='3'><b>Vedtektene til korpset:<b></td><td><a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'><i>her kommer link til vedtektene<i></a></td></tr>
			<tr><th colspan='4'>Styret og komitéer</th></tr>";
			
			//skriver ut alle komiteene med link til komitevisning
			foreach($komiteer as $komite){
					if($komite['komiteid']==$_GET['id']){
						echo"<tr><td colspan='3'><a href='?side=organisasjon'><b>".$komite['navn']."</b></a></td>
						<td>".$komite['mail_alias']."</td></tr>";
						foreach($valgtekomiteer as $valgtekomite){
							echo"<tr><td></td><td>".$valgtekomite['tittel']."</td><td><a href='?side=medlem/vis&id=".$valgtekomite['medlemsid']."'>
						".$valgtekomite['fnavn']." ".$valgtekomite['enavn']."</a></td><td>".$valgtekomite['mail_alias']."</td></tr>";
						};
					echo"<tr><td colspan='3'></td><td><a href='http://org.ntnu.no/buk/filer/dokumenter/Vedlegg_A2.pdf'><i>her kommer link til instruksen for ".$komite['navn']."<i></a></td></tr>";

					}else{
						echo"<tr><td colspan='3'><a href='?side=organisasjon&id=".$komite['komiteid']."'>
						".$komite['navn']."</a></td><td>".$komite['mail_alias']."</td></tr>";
					};
			};
		echo"
		</table>";	


?>