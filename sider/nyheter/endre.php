<?php 
//TODO: mangler fortsatt test på tidsformat, og en liste for å koble slagverksbærere til medlemmer
global $dbh;

$feilmeldinger = Array();
//sjekker om man er admin
if(!tilgang_endre()){
	header('Location: ?side=nyheter/liste');
	die();
}
$nyhet = Array();

include_once "sider/nyheter/funksjoner.php";

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
			$sql="UPDATE nyheter SET overskrift=?,ingress=?,hoveddel=?,bilde=?
			,type=?,aktiv=?,bildebredde=?,bilderamme=?,konsert_tid=? WHERE nyhetsid=?";
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array($overskrift, $ingress, $hoveddel, $bilde, $type, $aktiv, $bildebredde, $bilderamme, "$dato $konserttid", $id));
			header('Location: ?side=nyheter/vis&id='.$id);
			die();
		} else {
			$skrevetavid=$_SESSION["medlemsid"];
			$skerevet_tid=date("Y-m-d H:i:s");
			$sql="INSERT INTO nyheter (overskrift,ingress,hoveddel,bilde,type,aktiv,bildebredde,bilderamme,konsert_tid
			,skrevetavid,tid) values (?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array($overskrift, $ingress, $hoveddel, $bilde, $type, $aktiv, $bildebredde, $bilderamme, "$dato $konserttid", $skrevetavid, $skerevet_tid));
			$id = $dbh->lastInsertId();
			header('Location: ?side=nyheter/vis&id='.$id);
			die();
		}
	}
}
$handling = "Ny";

$nyhetsid = post('id');

//henter valgte nyhet fra databasen
if(has_get('id')){	
	#Hente ut valgte nyhet hvis "endre"
	$nyhetsid = get('id');
	$nyhet = hent_nyhet($nyhetsid);
	$handling = "Endre";		
}

$gyldige_nyhetstyper = Array("Public", "Intern"); #legges til når beskjeder er implementert på forsida:wq, "Beskjed");

$aktivChecked = (isset($nyhet['aktiv']) && $nyhet['aktiv'] == 0) ? "" : "checked";

echo "<h2>".$handling." nyhet</h2>";
		
echo feilmeldinger($feilmeldinger);
//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
echo "
	<form method='post' action='?side=nyheter/endre'>
		<table>
			<tr><td>Overskrift:</td><td><input type='text' class='overskrift' name='overskrift' value='".kanskje($nyhet, 'overskrift')."'></td></tr>
			<tr><td>Type:</td><td>
				<select name='type'>
				";
				
				foreach($gyldige_nyhetstyper as $type) {
					$selected = (kanskje($nyhet, 'type')=="$type") ? " selected=selected" : "";
					
					echo "<option value='".$type."'".$selected.">".$type."</option>";
				}
				echo "
				</select></td></tr>
			<tr><td>Ingress:</td><td><textarea class='ingress' name='ingress'>".kanskje($nyhet, 'ingress')."</textarea></td></tr>
			<tr><td>Hoveddel:</td><td><textarea class='hoveddel' name='hoveddel'>".kanskje($nyhet, 'hoveddel')."</textarea></td></tr>";
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
