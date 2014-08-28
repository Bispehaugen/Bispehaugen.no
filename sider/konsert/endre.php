<?php 
	//TODO: mangler fortsatt test på tidsformat, og en liste for å koble slagverksbærere til medlemmer
	
	$feilmeldinger = Array();
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=nyheter/liste');
	}
	$nyheter = Array();

	//hvis en nyhet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post()) {
		$arridid = post('id');
		$nyhetsid = post('nyhetsid');
		$overskrift = post('overskrift');
		$ingress = post('ingress');
		$hoveddel = post('hoveddel');
		$bilde = post('bilde');
		$nyhetstype = "nestekonsert";
		$aktiv = post('aktiv');
		$bildebredde = post('bildebredde');
		$bilderamme = post('bilderamme');
		$dato = (post('dato')!="") ? dato("Y-m-d", post('dato')) : "0000-00-00" ;
		$konsertstart = (post('konsertstart')!="") ? dato("H:i:s",post('konsertstart')) : "00:00:00" ;
		$konsertslutt = (post('konsertslutt')!="") ? dato("H:i:s",post('konsertslutt')) : "00:00:00" ;
		$oppmote = (post('konsert_oppmøte')!="") ? dato("H:i:s",post('konsert_oppmøte')) : "00:00:00" ;
		$normal_pris = post('normal_pris');
		$student_pris = post('student_pris');
		$sted = post('sted');
		$hjelpere = post('hjelpere');
		$kakebaker = post('kakebaker');

		if (!isset($overskrift) || $overskrift=="") { 
		   $feilmeldinger[] =  "Du må fylle inn overskrift"; 
		} 
		elseif (!isset($ingress) || $ingress=="") { 
		   $feilmeldinger[] =  "Du må fylle inn noe i ingressen"; 
		} 
		elseif (!isset($sted) || $sted=="") { 
		   $feilmeldinger[] =  "Du må fylle inn sted"; 
		}
		elseif (empty($dato) || !isset($oppmote) || $oppmote=="" || !isset($konsertstart) || $konsertstart=="" || !isset($sluttid) || $sluttid=="") { 
		   $feilmeldinger[] =  "Du må fylle inn dato, oppmøtetid, start og sluttid"; 
		} 
		elseif (strtotime($oppmote) > strtotime($starttid)) { 
		   $feilmeldinger[] =  "Oppmøte må være før starttiden"; 
		} 
		elseif (strtotime($starttid) > strtotime($sluttid)) { 
		   $feilmeldinger[] =  "Starttid må være før sluttiden"; 
		}
		
		if (empty($feilmeldinger)) {
			
			//sjekker om man vil legge til eller endre en konsert
			if ($id){
				#denne oppdatererer nyhetsoversikten
				$sql="UPDATE nyheter SET overskrift='".$overskrift."',ingress='".$ingress."',hoveddel='".$hoveddel."',bilde='".$bilde."'
				,type='nestekonsert',aktiv='".$aktiv.",bildebredde='".$bildebredde."',bilderamme='".$bilderamme."',konsert_tid='".$dato." ".$konsertstart."'
				,normal_pris='".$normal_pris."',student_pris='".$student_pris."' WHERE nyhetsid='".$nyhetsid."';";
				#mysql_query($sql);
				
				#her kommer en ny sql som oppdaterer arrangementstabellen								
				$sql="UPDATE arrangement SET tittel='".$overskrift."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
				,start='".$dato." ".$konsertstart."',slutt='".$dato." ".$konsertslutt."',ingress='".$ingress."',public='1',type='Konsert',hjelpere='".$hjelpere."'
				,kakebaker='".$kakebaker."' WHERE arrid='".$id."';";
				#mysql_query($sql);
				
				#header('Location: ?side=aktiviteter/liste');
				echo $sql;
			} else {
				$skrevetavid=$_SESSION["medlemsid"];
				$skerevet_tid=date("Y-m-d H:i:s");
				$sql="INSERT INTO nyheter (overskrift,ingress,hoveddel,bilde,type,aktiv,bildebredde,bilderamme,konsert_tid
				,normal_pris,student_pris,skrevetavid,tid)
				values ('$overskrift','$ingress','$hoveddel','$bilde','$type','$aktiv','$bildebredde','$bilderamme','$dato $konserttid'
				,'$normal_pris','$student_pris','$skrevetavid','$skerevet_tid');";
				
				#her kommer en ny sql som setter inn ny i arrangementstabellen
				$sql="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
	values ('$overskrift','nestekonsert','$sted','$dato','$oppmote','$dato $konsertstart','$dato $konsertslutt','$ingress','','1','$hjelpere','$kakebaker')";
				mysql_query($sql);
				#mysql_query($sql);
				
				$sql="INSERT INTO konserter (arrid_konsert, nyhetsid_konsert) values ('$xxx','$yyyy')";
				mysql_query($sql);
				
				echo ($sql);
				#header('Location: ?side=aktiviteter/liste');
			}
		}
	}
	$handling = "Ny";

	$nyhetsid = post('id');
	
	//henter valgte nyhet fra databasen
	if(has_get('id')){	
		#Hente ut valgte nyhet hvis "endre"
		$arrid=get('id');
		$sql="SELECT * FROM nyheter, konserter, arrangement WHERE arrangement.arrid=".$arrid." AND arrangement.arrid=konserter.arrid_konsert
		AND nyheter.nyhetsid=konserter.nyhetsid_konsert;";
		$mysql_result=mysql_query($sql);
		$konserter=mysql_fetch_array($mysql_result);
		$handling = "Endre";		
	}

	//henter ut alle medlemmer som kakebaker
	$sql="SELECT fnavn, enavn, medlemsid FROM medlemmer WHERE status='Aktiv' ORDER BY fnavn";
	$mysql_result=mysql_query($sql);
	while($row=mysql_fetch_array($mysql_result)){
		$medlemmer[$row['medlemsid']] = $row;
	}

$aktivChecked = (isset($nyheter['aktiv']) && $nyheter['aktiv'] == 0) ? "" : "checked";

$nyheter['normal_pris']=="" ? $normal_pris="Gratis!" : $normal_pris=$nyheter['normal_pris'];
$nyheter['student_pris']=="" ? $student_pris="Gratis!" : $student_pris=$nyheter['student_pris'];
		
$konsert_dato=dato("Y-m-d", $konserter["dato"]);

echo "<h2>".$handling." konsert</h2>";
		
echo feilmeldinger($feilmeldinger);
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
	echo "
		<form method='post' action='?side=forside'>
			<table>
				<tr><td>Overskrift:</td><td><input type='text' class='overskrift' name='overskrift' value='".kanskje($konserter, 'overskrift')."'></td></tr>
				<tr><td>Ingress:</td><td><textarea class='ingress' name='ingress'>".kanskje($konserter, 'ingress')."</textarea></td></tr>
				<tr><td>Hoveddel:</td><td><textarea class='hoveddel' name='hoveddel'>".kanskje($konserter, 'hoveddel')."</textarea></td></tr>
				<tr><td>Dato for konsert:</td><td><input type='text' class='datepicker' name='dato' value='".$konsert_dato."'></td></tr>
				<tr><td>Oppmøtetid:</td><td><input type='text' class='timepicker oppmote' name='oppmote' value='".bare_tidspunkt(kanskje($konserter, 'oppmote'))."'></td></tr>
				<tr><td>Konsertstart:</td><td><input type='text' class='timepicker konsertstart' name='konsertstart' value='".bare_tidspunkt(kanskje($konserter, 'start'))."'></td></tr>
				<tr><td>Konsertslutt:</td><td><input type='text' class='timepicker konsertslutt' name='konsertslutt' value='".bare_tidspunkt(kanskje($konserter, 'start'))."'></td></tr>
				<tr><td>Sted:</td><td><input type='text' name='sted' value='".kanskje($konserter, 'sted')."'></td></tr>
				<tr><td></td><td>* dato oppgis på formen yyyy-mm-dd og tidpunkter oppgis på formen tt:mm.</td></tr>
				<tr><td>Slagverksbærere:</td><td><input type='text' name='hjelpere' value='".kanskje($aktiviteter, 'hjelpere')."'></td></tr>
				<tr><td>Kakebaker:</td><td>
					<select name='kakebaker'>
					<option value=''</option>";
					foreach($medlemmer as $medlem){
						echo"<option value='".$medlem['medlemsid']."'";

						if ($medlem['medlemsid'] == kanskje($konserter, 'kakebaker')) {
							echo " selected=selected";
						}
						echo "'>".$medlem['fnavn']." ".$medlem['enavn']."</option>";
					}
					echo "</select></td></tr>
				<tr><td>Billettpris vanlig:</td><td><input type='text' name='normal_pris' value='".$normal_pris."'></td></tr>
				<tr><td>Billettpris student/honnør:</td><td><input type='text' name='student_pris' value='".$student_pris."'></td></tr>
				<tr><td>Bilde:</td><td>Kommer!!</td></tr>
				<tr><td></td><td><input type='checkbox' name='aktiv' value='1' ".$aktivChecked."/> Aktiv og vises på nett (fjern haken for å slette. Da slettes både aktiviteten i aktivitetslista og nestekonsert på hovedsida.)</td></tr>
				<input type='hidden' name='nyhetsid' value='".$konserter['nyhetsid']."'>							
					<tr>
						<td colspan=2>
							<p class='right'>
							<a href='?side=forside'>Avbryt</a>
							<input type='submit' name='endreNyhet' value='Lagre'>
							</p>
						</td>
					</tr>
			</table>
			<input type='hidden' name='id' value='".$nyhetsid."'>
		</form> 
	";
?>