<?php 
	//TODO: mangler fortsatt test på tidsformat, og en liste for å koble slagverksbærere til medlemmer
	
	$feilmeldinger = Array();
	//sjekker om man er admin
	if(!tilgang_endre()){
		header('Location: ?side=nyheter/liste');
		die();
	}
	$nyheter = Array();

	//hvis en nyhet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post()) {
		$id = post('id');
		$overskrift = post('overskrift');
		$ingress = post('ingress');
		$hoveddel = post('hoveddel');
		$bilde = post('bilde');
		$type = post('type');
		$aktiv = post('aktiv');
		$bildebredde = post('bildebredde');
		$bilderamme = post('bilderamme');
		$dato = "0000-00-00" ;
		$konserttid = "00:00:00" ;
		$sted = "";


		if (!isset($overskrift) || $overskrift=="") { 
		   $feilmeldinger[] =  "Du må fylle inn overskrift"; 
		} 
		elseif (!isset($ingress) || $ingress=="") { 
		   $feilmeldinger[] =  "Du må fylle inn noe i ingressen"; 
		}
		
		if (empty($feilmeldinger)) {
			
			//sjekker om man vil legge til eller endre en aktivitet
			if ($id){
				$sql="UPDATE nyheter SET overskrift='".$overskrift."',ingress='".$ingress."',hoveddel='".$hoveddel."',bilde='".$bilde."'
				,type='".$type."',aktiv='".$aktiv."',bildebredde='".$bildebredde."',bilderamme='".$bilderamme."',konsert_tid='".$dato." ".$konserttid."'
				 WHERE nyhetsid='".$id."';";
				mysql_query($sql);
				header('Location: ?side=nyheter/liste');
				die();
			} else {
				$skrevetavid=$_SESSION["medlemsid"];
				$skerevet_tid=date("Y-m-d H:i:s");
				$sql="INSERT INTO nyheter (overskrift,ingress,hoveddel,bilde,type,aktiv,bildebredde,bilderamme,konsert_tid
				,skrevetavid,tid)
				values ('$overskrift','$ingress','$hoveddel','$bilde','$type','$aktiv','$bildebredde','$bilderamme','$dato $konserttid'
				,'$skrevetavid','$skerevet_tid');";
				mysql_query($sql);
				header('Location: ?side=nyheter/liste');
				die();
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

$gyldige_nyhetstyper = Array("Public", "Intern"); #legges til når beskjeder er implementert på forsida:wq, "Beskjed");

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