<?php 
	//TODO: mangler fortsatt test på tidsformat, timepicker, og en liste for å koble slagverksbærere til medlemmer
	
	
	//funksjonalitet
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=aktiviteter/liste');
	}

	//hvis en aktivitet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post('tittel') /*&& !empty(post('tittel')) && has_post('dato') && !empty(post('dato'))*/) {
		$id = post('id');
		$tittel = post('tittel');
		$public = post('public');
		$ingress = post('ingress');
		$sted = post('sted');
		$dato = post('dato');
		$oppmote = post('oppmoetetid');
		$starttid = post('starttid');
		$sluttid = post('sluttid');
		$hjelpere = post('hjelpere');
		$kakebaker = post('kakebaker');
		
		//sjekker om man vil legge til eller endre en aktivitet
		if ($id){
			$sql="UPDATE arrangement SET tittel='".$tittel."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
			,start='".$dato." ".$starttid."',slutt='".$dato." ".$sluttid."',ingress='".$ingress."',public='".$public."',hjelpere='".$hjelpere."'
			,kakebaker='".$kakebaker."' WHERE arrid='".$id."';";
			mysql_query($sql);
			header('Location: ?side=aktiviteter/liste');
		} else {			
			$sql="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
values ('$tittel','','$sted','$dato','$oppmote','$dato $starttid','$dato $sluttid','$ingress','','$public','$hjelpere','$kakebaker')";
			mysql_query($sql);
			header('Location: ?side=aktiviteter/liste');
		}
	}
	
	//henter valgte aktivitet fra databasen
	if(has_get('id')){	
		#Hente ut valgte nyhet hvis "endre"
		$arrid=get('id');
		$sql="SELECT * FROM `arrangement` WHERE `arrid`=".$arrid;
		$mysql_result=mysql_query($sql);
		$aktiviteter = Array();
		$aktiviteter=mysql_fetch_array($mysql_result);		
	};
	
	//henter ut alle medlemmer som kakebaker
		$sql="SELECT fnavn, enavn, medlemsid FROM medlemmer WHERE status='Aktiv' ORDER BY fnavn";
		$mysql_result=mysql_query($sql);
		while($row=mysql_fetch_array($mysql_result)){
    		$medlemmer[$row['medlemsid']] = $row;
		};
		
		
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
		
	echo "
    <script>
    $(function() {
        $('.datepicker').pickadate();
        $('.timepicker').pickatime({interval: 15});
    });
    </script>
		<form method='post' action='?side=aktiviteter/endre'>
			<table>
				<th>Endre aktivitet</th><th></th>
				<tr><td>Tittel:</td><td><input type='text' name='tittel' value='".kanskje($aktiviteter, 'tittel')."'></td></tr>
				<tr><td>public:</td><td>
					<select name='public'>
  						<option value='1'>Public</option>
  						<option value='0'>Intern</option>
  						<option value='2'>Admin</option>
					</select></td></tr>
				<tr><td>Ingress:</td><td><input type='text' name='ingress' value='".kanskje($aktiviteter, 'ingress')."'></td></tr>
				<tr><td>Sted:</td><td><input type='text' name='sted' value='".kanskje($aktiviteter, 'sted')."'></td></tr>
				<tr><td>Dato:</td><td><input type='text' class='datepicker' name='dato' value='".kanskje($aktiviteter, 'dato')."'></td></tr>
				<tr><td>Oppmøte kl:</td><td><input type='text' class='timepicker' name='oppmoetetid' value='".bare_tidspunkt(kanskje($aktiviteter, 'oppmoetetid'))."'></td></tr>
				<tr><td>Start kl:</td><td><input type='text' class='timepicker' name='starttid' value='".bare_tidspunkt(kanskje($aktiviteter, 'start'))."'></td></tr>
				<tr><td>Slutt kl:</td><td><input type='text' class='timepicker' name='sluttid' value='".bare_tidspunkt(kanskje($aktiviteter, 'slutt'))."'></td></tr>
				<tr><td>Slagverksbærere:</td><td><input type='text' name='hjelpere' value='".kanskje($aktiviteter, 'hjelpere')."'></td></tr>
				<tr><td>Kakebaker:</td><td>
					<select name='kakebaker'>
					<option value=''</option>";
					foreach($medlemmer as $medlem){
						echo"<option value='".$medlem['medlemsid']."'";

						if ($medlem['medlemsid'] == kanskje($aktiviteter, 'kakebaker')) {
							echo " selected=selected";
						}
						echo "'>".$medlem['fnavn']." ".$medlem['enavn']."</option>";
					}
					echo "</select></td></tr>

					<tr>
						<td colspan=2>
							<p class='right'>
							<a href='?side=aktiviteter/liste'>Avbryt</a>
							<input type='submit' name='endreNyhet' value='Lagre'>
							</p>
						</td>
					</tr>
			</table>
			<input type='hidden' name='id' value='".get('id')."'>
		</form> 
	";
?>