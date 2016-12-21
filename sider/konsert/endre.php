<?php 
//TODO: mangler fortsatt test på tidsformat, og en liste for å koble slagverksbærere til medlemmer
global $dbh;

$feilmeldinger = Array();
//sjekker om man er admin
if(!tilgang_endre()){
	header('Location: ?side=nyheter/liste');
	die();
}
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
	$aktiv = 1; //post('aktiv');
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
			$sql1 = "UPDATE nyheter SET overskrift=?,sted=?,ingress=?,hoveddel=?,bilde=?
			,type='nestekonsert',aktiv=?,bildebredde=?,bilderamme=?,konsert_tid=?
			,normal_pris=?,student_pris=? WHERE nyhetsid=?";
            $stmt = $dbh->prepare($sql1);
            $stmt->execute(array($overskrift, $sted, $ingress, $hoveddel, $bilde, $aktiv, $bildebredde, $bilderamme, "$dato $konsertstart", $normal_pris, $student_pris, $nyhetsid));
			echo $sql1;
		} else {
			$skrevetavid = hent_brukerdata()["medlemsid"];
			$skerevet_tid = date("Y-m-d H:i:s");
			$sql2 = "INSERT INTO nyheter (overskrift,ingress,hoveddel,bilde,type,aktiv,bildebredde,bilderamme,konsert_tid
			,normal_pris,student_pris,skrevetavid,tid, sted) values (?,?,?,?,'nestekonsert',?,?,?,?,?,?,?,?,?)";
            $stmt = $dbh->prepare($sql2);
            $stmt->execute(array($overskrift, $ingress, $hoveddel, $bilde, $aktiv, $bildebredde, $bilderamme, "$dato $konsertstart", $normal_pris, $student_pris, $skrevetavid, $skerevet_tid, $sted));
			$nyhetsid = $dbh->lastInsertId();
		}
		
		if ($arrid){
			#sql som oppdaterer arrangementstabellen								
			$sql3 = "UPDATE arrangement SET tittel=?,sted=?,dato=?,oppmoetetid=?
			,start=?,slutt=?,ingress=?,public='1',type='Konsert',hjelpere=?
			,kakebaker=? WHERE arrid=?";
            $stmt = $dbh->prepare($sql3);
            $stmt->execute(array($overskrift, $sted, $dato, $oppmote, "$dato $konsertstart", "$dato $konsertslutt", $ingress, $hjelpere, $kakebaker, $arrid));
		} else {
			
			$sql4 = "INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
values (?,'Konsert',?,?,?,?,?,?,'','1',?,?)";
            $stmt = $dbh->prepare($sql4);
            $stmt->execute(array($overskrift, $sted, $dato, $oppmote, "$dato $konsertstart", "$dato $konsertslutt", $ingress, $hjelpere, $kakebaker));
			$arrid = $dbh->lastInsertId();
		}

		if(empty($konserttabellid)){
			$sql5 = "INSERT INTO konserter (arrid_konsert, nyhetsid_konsert) values (?,?)";
            $stmt = $dbh->prepare($sql5);
            $stmt->execute(array($arrid, $nyhetsid));
		}
		header('Location: ?side=aktiviteter/liste');
		die();
	}
}
$handling = "Ny";
//henter valgte nyhet fra databasen
if(has_get('id')||has_post('arrid')){	
	#Hente ut valgte nyhet hvis "endre"
	if (has_get('id')) {
		$arrid = get('id');
	}
	if (has_post('id')) {
		$arrid = post('id');
	}
	$sql = "SELECT * FROM arrangement WHERE arrid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($arrid));
	$konsert_arrangement = $stmt->fetch();

	$sql = "SELECT nyheter.* 
		    FROM nyheter, konserter, arrangement 
		    WHERE arrangement.arrid=? AND arrangement.arrid = konserter.arrid_konsert
			  AND nyheter.nyhetsid = konserter.nyhetsid_konsert
		    LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($arrid));
	$konsert = $stmt->fetch();
	$handling = "Endre";
}

//henter ut alle medlemmer som kakebaker
$sql = "SELECT medlemsid, fnavn, enavn FROM medlemmer WHERE status='Aktiv' ORDER BY fnavn";
$medlemmer = hent_og_putt_inn_i_array($sql);

$aktivChecked = (isset($aktiv) && $aktiv == 0) ? "" : "checked";

$normal_pris = $konsert['normal_pris'] == "" ? "0" : $konsert['normal_pris'];
$student_pris = $konsert['student_pris'] == "" ? "0" : $konsert['student_pris'];
		
$konsert_dato = dato("Y-m-d", $konsert_arrangement["dato"]);

echo "<h2>".$handling." konsert</h2>";
?>
<script>
$(function() {
    $('.datepicker').pickadate();
    $('.timepicker').pickatime({interval: 15});
});
</script>
<?php
echo feilmeldinger($feilmeldinger);
//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
echo "
<p>Legg merke til at siden dette er ny funksjonalitet er det ikke er en kobling for konserter før høsten 2014.</p>
<form method='post' action='?side=konsert/endre'>
	<table>
		<tr><td>Overskrift:</td><td><input type='text' class='overskrift' name='overskrift' value='".kanskje($konsert_arrangement, 'tittel')."'></td></tr>
		<tr><td>Ingress:</td><td><textarea class='ingress' name='ingress'>".kanskje($konsert_arrangement, 'ingress')."</textarea></td></tr>
		<tr><td>Hoveddel:</td><td><textarea class='hoveddel' name='hoveddel'>".kanskje($konsert, 'hoveddel')."</textarea></td></tr>
		<tr><td>Dato* for konsert:</td><td><input type='text' class='datepicker' name='dato' value='".$konsert_dato."'></td></tr>
		<tr><td></td><td>* dato oppgis på formen yyyy-mm-dd og tidpunkter oppgis på formen tt:mm.</td></tr>
		<tr><td>Oppmøtetid:</td><td><input type='text' class='timepicker' name='oppmote' value='".bare_tidspunkt(kanskje($konsert_arrangement, 'oppmoetetid'))."'></td></tr>
		<tr><td>Konsertstart:</td><td><input type='text' class='timepicker' name='konsertstart' value='".bare_tidspunkt(kanskje($konsert_arrangement, 'start'))."'></td></tr>
		<tr><td>Konsertslutt:</td><td><input type='text' class='timepicker' name='konsertslutt' value='".bare_tidspunkt(kanskje($konsert_arrangement, 'slutt'))."'></td></tr>
		<tr><td>Sted:</td><td><input type='text' name='sted' value='".kanskje($konsert_arrangement, 'sted')."'></td></tr>
		<tr><td>Slagverksbærere:</td><td><input type='text' name='hjelpere' value='".kanskje($konsert_arrangement, 'hjelpere')."'></td></tr>
		<tr><td>Kakebaker:</td><td>
			<select name='kakebaker'>
			<option value=''>Ingen</option>";
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
		<input type='hidden' name='arrid' value='".$arrid."'>
		<input type='hidden' name='nyhetsid' value='".$konsert['nyhetsid']."'>							
		<input type='hidden' name='konserttabellid' value='".$konsert['id']."'>							
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
