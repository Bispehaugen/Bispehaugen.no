<?php 
	//TODO: mangler fortsatt test på tidsformat, timepicker, og en liste for å koble slagverksbærere til medlemmer
	
	
	//funksjonalitet
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=aktiviteter/liste');
	};
		
	//hvis en aktivitet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post('tittel') && !empty(post('tittel')) && !empty(post('dato'))) {
		$id=post('id');
		$tittel=post('tittel');
		$public=post('public');
		$ingress=post('ingress');
		$sted=post('sted');
		$dato=post('dato');
		$oppmote=post('oppmoetetid');
		$starttid=post('starttid');
		$sluttid=post('sluttid');
		$hjelpere=post('hjelpere');
		$kakebaker=post('kakebaker');
		
		//sjekker om man vil legge til eller endre en aktivitet
		if ($id){
			$sql="UPDATE arrangement SET tittel='".$tittel."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
			,start='".$dato." ".$starttid."',slutt='".$dato." ".$sluttid."',ingress='".$ingress."',public='".$public."',hjelpere='".$hjelpere."'
			,kakebaker='".$kakebaker."' WHERE arrid='".$id."';";
			mysql_query($sql);
			header('Location: ?side=aktiviteter/liste');
		}else{			
			$sql="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
values ('$tittel','','$sted','$dato','$oppmote','$dato $starttid','$dato $sluttid','$ingress','','$public','$hjelpere','$kakebaker')";
			mysql_query($sql);
			header('Location: ?side=aktiviteter/liste');
		};
	};
	
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
        $('#datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();;
    });
    </script>
		<form method='post' action='?side=aktiviteter/endre'>
			<table>
				<th>Endre aktivitet</th><th></th>
				<tr><td>Tittel:</td><td><input type='text' name='tittel' value='".$aktiviteter['tittel']."'></td></tr>
				<tr><td>public:</td><td>
					<select name='public'>
  						<option value='1'>Public</option>
  						<option value='0'>Intern</option>
  						<option value='2'>Admin</option>
					</select></td></tr>
				<tr><td>Ingress:</td><td><input type='text' name='ingress' value='".$aktiviteter['ingress']."'></td></tr>
				<tr><td>Sted:</td><td><input type='text' name='sted' value='".$aktiviteter['sted']."'></td></tr>
				<tr><td>Dato:</td><td><input type='text' id='datepicker' name='dato' value='".$aktiviteter['dato']."'></td></tr>
				<tr><td>Oppmøte kl:</td><td><input type='text' name='oppmoetetid' value='".$aktiviteter['oppmoetetid']."'></td></tr>
				<tr><td>Start kl:</td><td><input type='text' name='starttid' value='".$aktiviteter['starttid']."'></td></tr>
				<tr><td>Slutt kl:</td><td><input type='text' name='sluttid' value='".$aktiviteter['sluttid']."'></td></tr>
				<tr><td>Slagverksbærere:</td><td><input type='text' name='hjelpere' value='".$aktiviteter['hjelpere']."'></td></tr>
				<tr><td>Kakebaker:</td><td>
					<select name='kakebaker'>
					<option value='".$aktiviteter['kakebaker']."'>".$medlemmer[$aktiviteter['kakebaker']]['fnavn']." ".$medlemmer[$aktiviteter['kakebaker']]['enavn']."</option>
					<option value=''</option>";
					foreach($medlemmer as $medlem){
						echo"
  							<option value='".$medlem['medlemsid']."'>".$medlem['fnavn']." ".$medlem['enavn']."</option>";
						};
						echo "</select></td></tr>
			</table>
			<input type='hidden' name='id' value='".get('id')."'>
			<a href='?side=aktiviteter/liste'>Avbryt</a>
			<input type='submit' name='endreNyhet' value='Lagre'>
		</form> 
	";
?>