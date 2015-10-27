<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
//TODO legge inn googlecal og ical eksport
    
#fuksjonalitet
include_once 'sider/aktiviteter/funksjoner.php';
include_once 'sider/intern/slagverkhjelp/funksjoner.php';


//spørring som henter ut alle aktiviteter
$alle=0;
$alle=get('alle');
$aktiviteter=hent_aktiviteter("","",$alle);

echo "<h2 class='overskrift-som-er-inline-block'>Aktiviteter</h2>";

if(tilgang_endre()){
	echo"<h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=aktiviteter/endre'><i class='fa fa-plus'></i>Legg til ny aktivitet</a></h3>";
	echo"<h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=konsert/endre'><i class='fa fa-plus'></i>Legg til ny konsert</a></h3>";
}
if(get('alle')==0){
    	echo" <h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=aktiviteter/liste&alle=1'><i class='fa fa-calendar'></i>Vis tidligere</a></h3>";
 	} else {
     	echo"<h3 class='lenke-som-er-inline-med-overskrift'><a href='?side=aktiviteter/liste&alle=0'><i class='fa fa-calendar'></i>Vis bare kommende</a></h3>";
	}
?>
<h3 class='lenke-som-er-inline-med-overskrift'><a href='http://www.google.com/calendar/render?cid=http://bispehaugen.no/ical.php<?php if(er_logget_inn()) { echo "?p=bukaros"; } ?>'><i class='fa fa-cloud-download '></i>Legg til i google calendar</a></h3>

<script type='text/javascript'>
	function slett_aktivitet(){
		var id = $(this).data("id");
		var tittel = $(this).data("title").replace(/\\/g, '');

		var ask = confirm('Vil du slette ' + tittel + '?');
		if(!!ask){
			window.location = '?side=aktiviteter/slette&id='+id;
		}
	}

	$(function() {
		$(".slett-aktivitet").click(slett_aktivitet);
	});
</script>
<?php

if (er_logget_inn()) {
	$innlogget_bruker_id = innlogget_bruker()['medlemsid'];
	$innlogget_brukers_slagverkergruppe = hent_slagverkgruppe_for_medlem($innlogget_bruker_id)['gruppeid'];

	$kakebakere = Array();
	$kakebakerIder = Array();

	foreach($aktiviteter as $aktivitet){
		$kakebaker = $aktivitet['kakebaker'];
		if(!empty($kakebaker)) {
			array_push($kakebakerIder, $kakebaker);
		}
	}
	$kakebakere = hent_brukerdata($kakebakerIder);
}

#Det som printes på sida
echo "<table class='aktivitetsliste'>
<thead><tr><th colspan=2>Dato:</th>
<th>Tid:</th><th>Arrangement:</th>
<th>Sted:</th>";
if(er_logget_inn()) {
		echo "<th>Bæring:</th>";
		echo "<th>Kakebaker:</th>";
}
if(tilgang_endre()) {
	echo "<th></th>";
}
echo "</tr></thead>";

$forrigeAktivitetesAar = date("Y");

	foreach($aktiviteter as $aktivitet){
		
	$startdatosAar = date("Y", strtotime($aktivitet['start']));
	if ($startdatosAar != $forrigeAktivitetesAar) {
		echo "<tr><td colspan=6><h4 class='aarskille'>".$startdatosAar."</h4></td></tr>";
		$forrigeAktivitetesAar = $startdatosAar;
	}

	echo "<tr>";
	echo "<td>".strftime("%a", strtotime($aktivitet['start']))."</td>";

	echo "<td>".strftime("%#d. %b", strtotime($aktivitet['start']));
	
	#hvis tildato er satt eller lik
	if((dato("d", $aktivitet['slutt']) == dato("d", $aktivitet['start']))||($aktivitet['slutt']=="0000-00-00 00:00:00")){
		echo "";
	} else {
		echo " - ".strftime("%a %#d. %b", strtotime($aktivitet['slutt']));
	}
	echo "</td>";

	if($aktivitet['start']=="0000-00-00 00:00:00"){
		echo "<td></td>";
	}else{
		echo "<td>".strftime("%H:%M", strtotime($aktivitet['start']))."</td>";
	}

	$aktivitetstype = "aktiviteter";
	$id_url = "arrid=".$aktivitet['arrid'];

	if ($aktivitet['type']=="Konsert") {
		$aktivitetstype = "konsert";
		$id_url = "id=".hent_konsert_nyhetsid($aktivitet['arrid']);
	}
	echo "<td><a href='?side=".$aktivitetstype."/vis&" . $id_url . "'><i class='fa fa-link'></i>".$aktivitet['tittel']."</a></td>";
		
	echo "<td>".$aktivitet['sted']."</td>";

	if(er_logget_inn()) {
		echo "<td>";
		$slagverk = $aktivitet['slagverk'];
		if (!empty($slagverk)) {
			echo "<a href='?side=intern/slagverkhjelp/liste'";
			if($slagverk == $innlogget_brukers_slagverkergruppe) {
				echo " class='din-gruppe' title='Din bæregruppe'";
			} else {
				echo " title='Gruppe ".$slagverk."'";
			}
			echo ">G".$slagverk."</a>";
		}
		echo "</td>";

		echo "<td>";
		$kakebaker = $aktivitet['kakebaker'];
		if (!empty($kakebaker)) {
			echo "<span'";
			if($kakebaker == $innlogget_bruker_id) {
				echo " class='din-gruppe' title='Din kakebaketur'";
			}
			echo ">".brukerlenke($kakebakere[$kakebaker], Navnlengde::Fornavn, false)."</span>";
		}
		echo "</td>";


	}

	#Viser endre/slettkapper hvis man er admin
	if(tilgang_endre()){
		echo "<td>";
		echo"<a href='?side=aktiviteter/vis&arrid=".$aktivitet['arrid']."'><i class='fa fa-music' title='Klikk for å legge til noter'></i></a> / ";
		$endre_url = ($aktivitet['type']=="Konsert") ? "konsert/endre" : "aktiviteter/endre";
		echo"<a href='?side=".$endre_url."&id=".$aktivitet['arrid']."'><i class='fa fa-edit' title='Klikk for å endre'></i></a>";
		echo " / ";
		echo "<a href='#' class='slett-aktivitet' data-id='".$aktivitet['arrid']."' data-title='".addslashes($aktivitet['tittel'])."'><i class='fa fa-times' title='Klikk for å slette'></i></a>";

		echo "</td></tr>";
	}else{
		echo "<td></td></tr>";
	}
}
echo "</table>";
