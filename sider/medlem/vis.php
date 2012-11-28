<?php 
//TODO Bilde og fikse kolonnebredde
	
	//funksjonalitet

	//henter valgte medlem fra databasen
	if(isset($_GET['id'])){	
		$id=mysql_real_escape_string($_GET['id']);
		if(er_logget_inn()){
			$sql="SELECT fnavn, enavn, instrument, status, grleder, foto, adresse, postnr, poststed, email, tlfmobil, fdato, studieyrke,
			startetibuk_date, sluttetibuk_date, bakgrunn, ommegselv, kommerfra FROM `medlemmer` WHERE `medlemsid`=".$id;
		}
		else{
			$sql="SELECT fnavn, enavn, status, instrument, grleder, foto, bakgrunn, kommerfra FROM `medlemmer` WHERE `medlemsid`=".$id;
		};
		$mysql_result=mysql_query($sql);
		$medlemmer = Array();
		$medlemmer=mysql_fetch_array($mysql_result);
	}
	
	
	
	//printer ut skjema med forh�ndsutfylte verdier hvis disse eksisterer
		
	echo "
  
			<table>
				<tr><th>".$medlemmer['fnavn']." ".$medlemmer['enavn']."</th><th>";
				if(($_SESSION['medlemsid']==$_GET['id']) || ($_SESSION['rettigheter']>2)){
						echo"<a href='?side=medlem/endre&id=".$id."'>endre</a>";
				};
				echo"</th></tr>
				<tr><td>".$medlemmer['instrument'];
				if($medlemmer['grleder']==1){
					echo" - gruppeleder";
				};
				if(!($medlemmer['status']=='Aktiv')){
					echo " (".$medlemmer['status'].")";
				};
				echo"</td><td rowspan='15'>HER KOMMER BILDE</td></tr>
				<tr><td></td></tr>";
				if(er_logget_inn()){
					echo"<tr><td><b>Født</b>: </td></tr>
					<tr><td></td></tr>
					<tr><td>".$medlemmer['fdato']."</td></tr>
					<tr><td></td></tr>
					<tr><td><b>Kontaktinfo:</b></td></tr>
					<tr><td>".$medlemmer['tlfmobil']."</td></tr>
					<tr><td>".$medlemmer['email']."</td></tr>
					<tr><td></td></tr>				
					<tr><td></td></tr>
					<tr><td>".$medlemmer['adresse']."</td></tr>
					<tr><td>".$medlemmer['postnr']." ".$medlemmer['poststed']."</td></tr>
					<tr><td></td></tr>
					<tr><td></td></tr>
					<tr><td><b>Startet i BUK:</b> ".$medlemmer['startetibuk_date']."</td></tr>";
					if($medlemmer['sluttetibuk_date']>0){
						echo"<tr><td><b>Sluttet i BUK:</b>".$medlemmer['sluttetibuk_date']."</td></tr>";
					}
					if(!empty($medlemmer['studieyrke'])){
						echo" <tr><td><b>Studie/yrke:</b></td></tr>
						<tr><td>".$medlemmer['studieyrke']."</td></tr>";
					};
					if(!empty($medlemmer['ommegselv'])){
						echo"<tr><td><b>Litt om meg:</b></td></tr>
						<tr><td>".$medlemmer['ommegselv']."</td></tr>";
					};	
				};
				if(!empty($medlemmer['bakgrunn'])){
						echo"<tr><td><b>Musikalsk bakgrunn:</b></td></tr>
						<tr><td>".$medlemmer['bakgrunn']."</td></tr>";
					};
				if(!empty($medlemmer['kommerfra'])){			
					echo"<tr><td><b>Kommer fra:</b></td></tr>
					<tr><td>".$medlemmer['kommerfra']."</td></tr>";
				};
				echo"
				</table>
	";	
?>