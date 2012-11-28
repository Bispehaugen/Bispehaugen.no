<?php 
	//TODO: mangler fortsatt test på tidsformat, timepicker, og en liste for å koble slagverksbærere til medlemmer
	
	
	//funksjonalitet
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=aktiviteter/liste');
	};
		
	//hvis en aktivitet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(isset($_POST['tittel'])&& (! empty($_POST['tittel']))  && (! empty($_POST['dato']))){
		$id=$_POST['id'];
		$tittel=mysql_real_escape_string($_POST['tittel']);
		$public=mysql_real_escape_string($_POST['public']);
		$ingress=mysql_real_escape_string($_POST['ingress']);
		$sted=mysql_real_escape_string($_POST['sted']);
		$dato=mysql_real_escape_string($_POST['dato']);
		$oppmote=mysql_real_escape_string($_POST['oppm�te']);
		$starttid=mysql_real_escape_string($_POST['starttid']);
		$sluttid=mysql_real_escape_string($_POST['sluttid']);
		$slagverksbarere=mysql_real_escape_string($_POST['hjelpere']);
		$kakebaker=mysql_real_escape_string($_POST['kakebaker']);
		
		//sjekker om man vil legge til eller endre en aktivitet
		if ($id){
			$sql="UPDATE arrangement SET tittel='".$tittel."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
			,starttid='".$starttid."',sluttid='".$sluttid."',ingress='".$ingress."',public='".$public."',hjelpere='".$hjelpere."'
			,kakebaker='".$kakebaker."' WHERE arrid='".$id."';";
			mysql_query($sql);
			header('Location: ?side=aktiviteter/liste');
		}else{			
			$sql="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,starttid,sluttid,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
values ('$tittel','','$sted','$dato','$oppmote','$starttid','$sluttid','$ingress','','$public','$hjelpere','$kakebaker')";
			mysql_query($sql);
			header('Location: ?side=aktiviteter/liste');
		}
	}
	
	//henter valgte aktivitet fra databasen
	if(isset($_GET['id'])){	
		#Hente ut valgte nyhet hvis "endre"
		$arrid=mysql_real_escape_string($_GET['id']);
		$sql="SELECT * FROM `arrangement` WHERE `arrid`=".$arrid;
		$mysql_result=mysql_query($sql);
		$aktiviteter = Array();
		$aktiviteter=mysql_fetch_array($mysql_result);		
	}
	
	//henter ut alle medlemmer som kakebaker
		$sql="SELECT fnavn, enavn, medlemsid FROM medlemmer WHERE status='Aktiv' ORDER BY fnavn";
		$mysql_result=mysql_query($sql);
		while($row=mysql_fetch_array($mysql_result)){
			//print_r($row);
    		$medlemmer[$row['medlemsid']] = $row;
		};
		
		
	
	//printer ut skjema med forh�ndsutfylte verdier hvis disse eksisterer
		
	echo "
    <link rel='stylesheet' href='http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css' />
    <script src='http://code.jquery.com/jquery-1.8.2.js'></script>
    <script src='http://code.jquery.com/ui/1.9.0/jquery-ui.js'></script>
    <script>
    $(function() {
        $('#datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();;
    });
    </script>
		<form method='post' action='?side=aktiviteter/endre'>
			<table>
				<th>Endre aktivitet</th><th></th>
				<tr><td>Tittel:</td><td><input type='text' name='tittel' value=".$aktiviteter['tittel']."></td></tr>
				<tr><td>public:</td><td>
					<select name='public'>
  						<option value='1'>Public</option>
  						<option value='0'>Intern</option>
  						<option value='2'>Admin</option>
					</select></td></tr>
				<tr><td>Ingress:</td><td><input type='text' name='ingress' value=".$aktiviteter['ingress']."></td></tr>
				<tr><td>Sted:</td><td><input type='text' name='sted' value=".$aktiviteter['sted']."></td></tr>
				<tr><td>Dato:</td><td><input type='text' id='datepicker' name='dato' value=".$aktiviteter['dato']."></td></tr>
				<tr><td>Oppm�te kl:</td><td><input type='text' name='oppmote' value=".$aktiviteter['oppm�te']."></td></tr>
				<tr><td>Start kl:</td><td><input type='text' name='starttid' value=".$aktiviteter['starttid']."></td></tr>
				<tr><td>Slutt kl:</td><td><input type='text' name='sluttid' value=".$aktiviteter['sluttid']."></td></tr>
				<tr><td>Slagverksb�rere:</td><td><input type='text' name='slagverksbarere' value=".$aktiviteter['hjelpere']."></td></tr>
				<tr><td>Kakebaker:</td><td>
					<select name='kakebaker'>
					<option value=''</option>";
					foreach($medlemmer as $medlem){
						echo"
  							<option value=".$medlem['medlemsid'].">".$medlem['fnavn']." ".$medlem['enavn']."</option>";
						};
						echo "</select></td></tr>
			</table>
			<input type='hidden' name='id' value=".$_GET['id'].">
			<a href='?side=aktiviteter/liste'>Avbryt</a>
			<input type='submit' name='endreNyhet' value='Lagre'>
		</form> 
	";
?>