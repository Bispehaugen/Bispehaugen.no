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
		$arrid = post('arrid');
		$nyhetsid = post('nyhetsid');
		$konserttabellid = post('konserttabellid');
		$overskrift = post('overskrift');
		$ingress = post('ingress');
		$hoveddel = post('hoveddel');
		$bilde = post('bilde');
		$nyhetstype = "nestekonsert";
		$aktiv = post('aktiv');
		$bildebredde = post('bildebredde');
		$bilderamme = post('bilderamme');
		$dato = post('dato');
		$konsertstart = post('konsertstart');
		$konsertslutt = post('konsertslutt');
		$oppmote = post('oppmote');
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
		elseif (empty($dato) || !isset($oppmote) || $oppmote=="" || !isset($konsertstart) || $konsertstart=="" || !isset($konsertslutt) || $konsertslutt=="") { 
		   $feilmeldinger[] =  "Du må fylle inn dato, oppmøtetid, start og sluttid"; 
		} 
		elseif (strtotime($oppmote) > strtotime($konsertstart)) { 
		   $feilmeldinger[] =  "Oppmøte må være før starttiden"; 
		} 
		elseif (strtotime($starttid) > strtotime($konsertslutt)) { 
		   $feilmeldinger[] =  "Starttid må være før sluttiden"; 
		}
		
		if (empty($feilmeldinger)) {
			
			//sjekker om det finnes en oppføring i hhv. arrangement, nyhet, konserter og oppdaterer eller oppretter oppføringene
			if ($nyhetsid){
				#denne oppdatererer nyhetsoversikten
				$sql1="UPDATE nyheter SET overskrift='".$overskrift."',sted='".$sted."',ingress='".$ingress."',hoveddel='".$hoveddel."',bilde='".$bilde."'
				,type='nestekonsert',aktiv='".$aktiv."',bildebredde='".$bildebredde."',bilderamme='".$bilderamme."',konsert_tid='".$dato." ".$konsertstart."'
				,normal_pris='".$normal_pris."',student_pris='".$student_pris."' WHERE nyhetsid='".$nyhetsid."';";
				mysql_query($sql1);
				echo $sql1;
			} else {
				$skrevetavid=$_SESSION["medlemsid"];
				$skerevet_tid=date("Y-m-d H:i:s");
				$sql2="INSERT INTO nyheter (overskrift,ingress,hoveddel,bilde,type,aktiv,bildebredde,bilderamme,konsert_tid
				,normal_pris,student_pris,skrevetavid,tid, sted)
				values ('$overskrift','$ingress','$hoveddel','$bilde','nestekonsert','$aktiv','$bildebredde','$bilderamme','$dato $konsertstart'
				,'$normal_pris','$student_pris','$skrevetavid','$skerevet_tid','$sted');";
				mysql_query($sql2);
				$nyhetsid=mysql_insert_id();
			};
			
			if ($arrid){
				#sql som oppdaterer arrangementstabellen								
				$sql3="UPDATE arrangement SET tittel='".$overskrift."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
				,start='".$dato." ".$konsertstart."',slutt='".$dato." ".$konsertslutt."',ingress='".$ingress."',public='1',type='Konsert',hjelpere='".$hjelpere."'
				,kakebaker='".$kakebaker."' WHERE arrid='".$arrid."';";
				mysql_query($sql3);
			} else {
				
				$sql4="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
	values ('$overskrift','Konsert','$sted','$dato','$oppmote','$dato $konsertstart','$dato $konsertslutt','$ingress','','1','$hjelpere','$kakebaker')";
				mysql_query($sql4);
				$arrid=mysql_insert_id();
			};	
				
			if(empty($konserttabellid)){
				$sql5="INSERT INTO konserter (arrid_konsert, nyhetsid_konsert) values ('$arrid','$nyhetsid')";
				mysql_query($sql5);
			}
			header('Location: ?side=aktiviteter/liste');
		}
	}
	$handling = "Ny";
	//henter valgte nyhet fra databasen
	if(has_get('id')||has_post('arrid')){	
		#Hente ut valgte nyhet hvis "endre"
		has_get('id') ? $arrid=get('id') : "";
 		$sql="SELECT * FROM arrangement WHERE arrangement.arrid=".$arrid.";";
		$konsert_arrangement=hent_og_putt_inn_i_array($sql);
		$sql="SELECT * FROM nyheter, konserter, arrangement WHERE arrangement.arrid=".$arrid." AND arrangement.arrid=konserter.arrid_konsert
		AND nyheter.nyhetsid=konserter.nyhetsid_konsert;";
		$konserter=hent_og_putt_inn_i_array($sql);
		$handling = "Endre";
	}

	//henter ut alle medlemmer som kakebaker
	$sql="SELECT fnavn, enavn, medlemsid FROM medlemmer WHERE status='Aktiv' ORDER BY fnavn";
	$mysql_result=mysql_query($sql);
	while($row=mysql_fetch_array($mysql_result)){
		$medlemmer[$row['medlemsid']] = $row;
	}

$aktivChecked = (isset($nyheter['aktiv']) && $nyheter['aktiv'] == 0) ? "" : "checked";

$konserter['normal_pris']=="" ? $normal_pris="0" : $normal_pris=$konserter['normal_pris'];
$konserter['student_pris']=="" ? $student_pris="0" : $student_pris=$konserter['student_pris'];
		
$konsert_dato=dato("Y-m-d", $konsert_arrangement["dato"]);

echo "<h2>".$handling." konsert</h2>";
		
echo feilmeldinger($feilmeldinger);
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	echo "
		<p>Legg merke til at siden dette er ny funksjonalitet er det ikke er en kobling for konserter før høsten 2014.</p>
		<form method='post' action='?side=konsert/endre'>
			<table>
				<tr><td>Overskrift:</td><td><input type='text' class='overskrift' name='overskrift' value='".kanskje($konsert_arrangement, 'tittel')."'></td></tr>
				<tr><td>Ingress:</td><td><textarea class='ingress' name='ingress'>".kanskje($konsert_arrangement, 'ingress')."</textarea></td></tr>
				<tr><td>Hoveddel:</td><td><textarea class='hoveddel' name='hoveddel'>".kanskje($konserter, 'hoveddel')."</textarea></td></tr>
				<tr><td>Dato for konsert:</td><td><input type='text' class='datepicker' name='dato' value='".$konsert_dato."'></td></tr>
				<tr><td>Oppmøtetid:</td><td><input type='text' class='timepicker' name='oppmote' value='".bare_tidspunkt(kanskje($konsert_arrangement, 'oppmoetetid'))."'></td></tr>
				<tr><td>Konsertstart:</td><td><input type='text' class='timepicker' name='konsertstart' value='".bare_tidspunkt(kanskje($konsert_arrangement, 'start'))."'></td></tr>
				<tr><td>Konsertslutt:</td><td><input type='text' class='timepicker' name='konsertslutt' value='".bare_tidspunkt(kanskje($konsert_arrangement, 'slutt'))."'></td></tr>
				<tr><td>Sted:</td><td><input type='text' name='sted' value='".kanskje($konsert_arrangement, 'sted')."'></td></tr>
				<tr><td></td><td>* dato oppgis på formen yyyy-mm-dd og tidpunkter oppgis på formen tt:mm.</td></tr>
				<tr><td>Slagverksbærere:</td><td><input type='text' name='hjelpere' value='".kanskje($konsert_arrangement, 'hjelpere')."'></td></tr>
				<tr><td>Kakebaker:</td><td>
					<select name='kakebaker'>
					<option value=''</option>";
					foreach($medlemmer as $medlem){
						echo"<option value='".$medlem['medlemsid']."'";

						if ($medlem['medlemsid'] == kanskje($konsert_arrangement, 'kakebaker')) {
							echo " selected=selected";
						}
						echo "'>".$medlem['fnavn']." ".$medlem['enavn']."</option>";
					}
					echo "</select></td></tr>
				<tr><td>Billettpris vanlig:</td><td><input type='text' name='normal_pris' value='".$normal_pris."'></td></tr>
				<tr><td>Billettpris student:</td><td><input type='text' name='student_pris' value='".$student_pris."'></td></tr>
				<tr><td>Bilde:</td><td>Kommer!!</td></tr>
				<tr><td></td><td><input type='checkbox' name='aktiv' value='1' ".$aktivChecked."/> Aktiv og vises på nett (fjern haken for å slette. Da slettes både aktiviteten i aktivitetslista og nestekonsert på hovedsida.)</td></tr>
				<input type='hidden' name='arrid' value='".$arrid."'>
				<input type='hidden' name='nyhetsid' value='".$konserter['nyhetsid']."'>							
				<input type='hidden' name='konserttabellid' value='".$konserter['id']."'>							
					<tr>
						<td colspan=2>
							<p class='right'>
							<a href='?side=forside'>Avbryt</a>
							<input type='submit' name='endreNyhet' value='Lagre'>
							</p>
						</td>
					</tr>
			</table>
		</form> 
	";
?>
