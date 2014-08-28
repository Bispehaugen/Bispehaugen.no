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
		$id = post('id');
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
			
			//sjekker om man vil legge til eller endre en aktivitet
			if ($id){
				$sql="UPDATE nyheter SET overskrift='".$overskrift."',ingress='".$ingress."',hoveddel='".$hoveddel."',bilde='".$bilde."'
				,type='".$type."',aktiv='".$aktiv.",bildebredde='".$bildebredde."',bilderamme='".$bilderamme."',konsert_tid='".$dato." ".$konserttid."'
				,normal_pris='".$normal_pris."',student_pris='".$student_pris."' WHERE nyhetsid='".$id."';";
				mysql_query($sql);
				#header('Location: ?side=aktiviteter/liste');
				echo $sql;
			} else {
				$skrevetavid=$_SESSION["medlemsid"];
				$skerevet_tid=date("Y-m-d H:i:s");
				$sql="INSERT INTO nyheter (overskrift,ingress,hoveddel,bilde,type,aktiv,bildebredde,bilderamme,konsert_tid
				,normal_pris,student_pris,skrevetavid,tid)
				values ('$overskrift','$ingress','$hoveddel','$bilde','$type','$aktiv','$bildebredde','$bilderamme','$dato $konserttid'
				,'$normal_pris','$student_pris','$skrevetavid','$skerevet_tid');";
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
		$nyhetsid=get('id');
		$sql="SELECT * FROM `nyheter` WHERE `nyhetsid`=".$nyhetsid;
		$mysql_result=mysql_query($sql);
		$nyheter=mysql_fetch_array($mysql_result);
		$handling = "Endre";		
	}

$gyldige_nyhetstyper = Array("Public", "Intern", "Beskjed", "nestekonsert");

$aktivChecked = (isset($nyheter['aktiv']) && $nyheter['aktiv'] == 0) ? "" : "checked";

echo "<h2>".$handling." nyhet</h2>";
		
echo feilmeldinger($feilmeldinger);
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
	echo "
		<form method='post' action='?side=nyheter/endre'>
			<table>
				<tr><td>Overskrift:</td><td><input type='text' class='overskrift' name='overskrift' value='".kanskje($nyheter, 'overskrift')."'></td></tr>
				<tr><td>Type:</td><td>
					<select name='type'>
					";
					
					foreach($gyldige_nyhetstyper as $type) {
						$selected = (kanskje($nyheter, 'type')=="$type") ? " selected=selected" : "";
						
						echo "<option value='".$type."'".$type.">".$type."</option>";
					}
					echo "
					</select></td></tr>
				<tr><td>Ingress:</td><td><textarea class='ingress' name='ingress'>".kanskje($nyheter, 'ingress')."</textarea></td></tr>
				<tr><td>Hoveddel:</td><td><textarea class='hoveddel' name='hoveddel'>".kanskje($nyheter, 'hoveddel')."</textarea></td></tr>";
				
				if($endre_konsert){
					if(isset($nyheter['konsert_tid'])){
						$konsert_dato=dato("Y-m-d",$nyheter['konsert_tid']);
						$konsert_tid=dato("H:i",$nyheter['konsert_tid']);
					};
					$nyheter['normal_pris']=="" ? $normal_pris="Gratis!" : $normal_pris=$nyheter['normal_pris'];
					$nyheter['student_pris']=="" ? $student_pris="Gratis!" : $student_pris=$nyheter['student_pris'];
					echo "<tr><td>Dato for konsert:</td><td><input type='text' class='datepicker' name='dato' value='".$konsert_dato."'></td></tr>
						<tr><td>Tidspunkt for konsert:</td><td><input type='text' class='timepicker konserttid' name='konserttid' value='".$konsert_tid."'></td></tr>
						<tr><td>Sted for konsert:</td><td><input type='text' name='sted' value='".kanskje($nyheter, 'sted')."'></td></tr>
						<tr><td>Billettpris vanlig:</td><td><input type='text' name='normal_pris' value='".$normal_pris."'></td></tr>
						<tr><td>Billettpris student/honnør:</td><td><input type='text' name='student_pris' value='".$student_pris."'></td></tr>
						<tr><td></td><td>* dato oppgis på formen yyyy-mm-dd og tidpunkter oppgis på formen tt:mm.</td></tr>";
				}
				
				echo "<tr><td>Bilde:</td><td>Kommer!!</td></tr>
					<tr><td></td><td><input type='checkbox' name='aktiv' value='1' ".$aktivChecked."/> Aktiv og vises på nett (fjern haken for å slette)</td></tr>
											
					<tr>
						<td colspan=2>
							<p class='right'>
							<a href='?side=nyheter/liste'>Avbryt</a>
							<input type='submit' name='endreNyhet' value='Lagre'>
							</p>
						</td>
					</tr>
			</table>
			<input type='hidden' name='id' value='".$nyhetsid."'>
		</form> 
	";
?>