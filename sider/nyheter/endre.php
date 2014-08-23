<?php 
	//TODO: mangler fortsatt test på tidsformat, og en liste for å koble slagverksbærere til medlemmer
	
	$feilmeldinger = Array();
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=nyheter/liste');
	}
	$nyheter = Array();

	//hvis en nyhet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(0){
	#if(has_post()) {
		$id = post('id');
		$tittel = post('tittel');
		$public = post('public');
		$type = post('type');
		$ingress = post('ingress');
		$sted = post('sted');
		$dato = has_post('dato') ? array_unique(post('dato')) : Array();
		$oppmote = post('oppmoetetid');
		$starttid = post('starttid');
		$sluttid = post('sluttid');
		$hjelpere = post('hjelpere');
		$kakebaker = post('kakebaker');

		if (!isset($tittel) || $tittel=="") { 
		   $feilmeldinger[] =  "Du må fylle inn tittel"; 
		} 
		elseif (!isset($sted) || $sted=="") { 
		   $feilmeldinger[] =  "Du må fylle inn sted"; 
		}
		elseif (empty($dato) || !isset($oppmote) || $oppmote=="" || !isset($starttid) || $starttid=="" || !isset($sluttid) || $sluttid=="") { 
		   $feilmeldinger[] =  "Du må fylle inn dato, oppmøtetid, start og sluttid"; 
		} 
		elseif (strtotime($oppmote) > strtotime($starttid)) { 
		   $feilmeldinger[] =  "Oppmøte må være før starttiden"; 
		} 
		elseif (strtotime($starttid) > strtotime($sluttid)) { 
		   $feilmeldinger[] =  "Starttid må være før sluttiden"; 
		}
		if (empty($feilmeldinger)) {

			//sjekker om man vil legge til eller endre en aktivitet
			if ($id){
				$dato = $dato[0];

				$sql="UPDATE arrangement SET tittel='".$tittel."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
				,start='".$dato." ".$starttid."',slutt='".$dato." ".$sluttid."',ingress='".$ingress."',public='".$public."',type='".$type."',hjelpere='".$hjelpere."'
				,kakebaker='".$kakebaker."' WHERE arrid='".$id."';";
				mysql_query($sql);
				header('Location: ?side=aktiviteter/liste');
			} else {
				print_r($dato);

				foreach($dato as $d) {
					$sql="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
	values ('$tittel','$type','$sted','$d','$oppmote','$d $starttid','$d $sluttid','$ingress','','$public','$hjelpere','$kakebaker')";
					mysql_query($sql);
				}
				
				header('Location: ?side=aktiviteter/liste');
			}
		}

		$aktiviteter = Array(
			"tittel" => $tittel,
			"public" => $public,
			"ingress" => $ingress,
			"sted" => $sted,
			"dato" => $dato,
			"oppmoetetid" => $oppmote,
			"start" => $starttid,
			"slutt" => $sluttid,
			"hjelpere" => $hjelpere,
			"kakebaker" => $kakebaker
		);
	}
	$handling = "Ny";

	$arrid = post('id');
	
	//henter valgte nyhet fra databasen
	if(has_get('id')){	
		#Hente ut valgte nyhet hvis "endre"
		$nyhetsid=get('id');
		$sql="SELECT * FROM `nyheter` WHERE `nyhetsid`=".$nyhetsid;
		$mysql_result=mysql_query($sql);
		$aktiviteter=mysql_fetch_array($mysql_result);
		$handling = "Endre";		
	}
		
echo feilmeldinger($feilmeldinger);

$gyldige_typer = Array("Øvelse", "Konsert", "Seminar", "Dugnad", "Sosialt", "Spilleoppdrag", "Møte", "Tur", "Annet");

$aktivitetsdato = kanskje($aktiviteter, 'dato');
$datoer = is_array($aktivitetsdato) ? $aktivitetsdato : Array(0 => $aktivitetsdato);
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
		
	echo "
		<form method='post' action='?side=nyheter/endre'>
			<table>
				<tr><td>Overskrift:</td><td><input type='text' class='overskrift' name='overskrift' value='".kanskje($nyheter, 'overskrift')."'></td></tr>
				<tr><td>Hvem kan se den:</td><td>
					<select name='type'>
  						<option value='Public'>Alle (Åpen på internett)</option>
  						<option value='Intern'>Intern (Bare korpsmedlemmer)</option>
  						<option value='Beskjed'>Beskjed (Bare korpsmedlemmer)</option>
  						<option value='nestekonsert'>Neste konsert</option>
					</select></td></tr>
				<tr><td>Ingress:</td><td><input type='text' name='ingress' value='".kanskje($aktiviteter, 'ingress')."'></td></tr>
				<tr><td>Sted:</td><td><input type='text' class='sted' name='sted' value='".kanskje($aktiviteter, 'sted')."'></td></tr>
				<tr><td></td><td>* dato oppgis på formen yyyy-mm-dd og tidpunkter oppgis på formen tt:mm.</td></tr>
				";
				$i = 0;
				foreach($datoer as $d) {
					echo "<tr class='dato'><td>Dato: ";
					if ($i > 0) {
						echo "<i class='fjern-dato fa fa-times'></i>";
					}
					echo "</td><td><input type='text' class='datepicker' name='dato[]' value='".$d."'></td></tr>";
					$i++;
				}
				
echo "
				<tr><td>Oppmøte kl:</td><td><input type='text' class='timepicker oppmoetetid' name='oppmoetetid' value='".bare_tidspunkt(kanskje($aktiviteter, 'oppmoetetid'))."'></td></tr>
				<tr><td>Start kl:</td><td><input type='text' class='timepicker starttid' name='starttid' value='".bare_tidspunkt(kanskje($aktiviteter, 'start'))."'></td></tr>
				<tr><td>Slutt kl:</td><td><input type='text' class='timepicker sluttid' name='sluttid' value='".bare_tidspunkt(kanskje($aktiviteter, 'slutt'))."'></td></tr>
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
			<input type='hidden' name='id' value='".$arrid."'>
		</form> 
	";
?>