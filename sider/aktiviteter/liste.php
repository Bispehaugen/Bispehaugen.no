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
}

#Det som printes på sida
echo "<table class='aktivitetsliste'>
<thead><tr><th colspan=2>Dato:</th>";
if (er_logget_inn()) {
    echo "<th>Oppmøte:</th>";
}

echo "<th>Starttid:</th>
<th>Arrangement:</th><th class='sted'>Sted:</th>";

if(er_logget_inn()) {
	echo "<th>Bæring:</th>";
	echo "<th>Kakebaker:</th>";
}
if(tilgang_endre()){
	echo "<th></th>";
}
echo "</tr></thead>";

$forrigeAktivitetesAar = date("Y");

	foreach($aktiviteter as $arrid => $aktivitet){

	$startdatosAar = date("Y", strtotime($aktivitet['start']));
	if ($startdatosAar != $forrigeAktivitetesAar) {
		echo "<tr><td colspan=6><h4 class='aarskille'>".$startdatosAar."</h4></td></tr>";
		$forrigeAktivitetesAar = $startdatosAar;
	}

	echo "<tr>";
	echo "<td class='day-of-week'>".strftime("%a", strtotime($aktivitet['start']))."</td>";

	echo "<td>".strftime("%#d. %b", strtotime($aktivitet['start']));

	#hvis tildato er satt eller lik
	if((dato("d", $aktivitet['slutt']) == dato("d", $aktivitet['start']))||($aktivitet['slutt']=="0000-00-00 00:00:00")){
		echo "";
	} else {
		echo " - ".strftime("%a %#d. %b", strtotime($aktivitet['slutt']));
	}
	echo "</td>";

	if (er_logget_inn()) {
	    if($aktivitet['oppmoetetid']=="0000-00-00 00:00:00"){
		    echo "<td></td>";
	    }else{
		    echo "<td>".strftime("%H:%M", strtotime($aktivitet['oppmoetetid']))."</td>";
	    }
	}

	if($aktivitet['start']=="0000-00-00 00:00:00"){
		echo "<td></td>";
	}else{
		echo "<td>".strftime("%H:%M", strtotime($aktivitet['start']))."</td>";
	}

	$aktivitetstype = "aktiviteter";
	$id_url = "arrid=".$arrid;

	if ($aktivitet['type']=="Konsert") {
		$aktivitetstype = "konsert";
		$id_url = "id=".hent_konsert_nyhetsid($arrid);
	}
	echo "<td>
			<a href='?side=".$aktivitetstype."/vis&" . $id_url . "'><i class='fa fa-link'></i>".$aktivitet['tittel']."</a>
			<p class='inline-sted'>".$aktivitet['sted']."</p>
		  </td>";

	echo "<td class='sted'>".$aktivitet['sted']."</td>";

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
		$kakebakere = kakebakere($arrid);
        $bakere = "";
        foreach ($kakebakere as $kakebaker) {
            if (!empty($bakere)) {
                $bakere .= " og ";
            }
			$bakere .= "<span";
			if($kakebaker['medlemsid'] == $innlogget_bruker_id) {
				$bakere .= " class='din-gruppe' title='Din kakebaketur'";
			}
			$bakere .= ">".brukerlenke($kakebaker, Navnlengde::Fornavn, false)."</span>";
		}
		echo $bakere . "</td>";
	}

	#Viser endre/slettkapper hvis man er admin
	if(tilgang_endre()){
		echo "<td>";
		echo"<a href='?side=aktiviteter/vis&arrid=".$arrid."'><i class='fa fa-music' title='Klikk for å legge til noter'></i></a> / ";
		$endre_url = ($aktivitet['type']=="Konsert") ? "konsert/endre" : "aktiviteter/endre";
		echo"<a href='?side=".$endre_url."&arrid=".$arrid."'><i class='fa fa-edit' title='Klikk for å endre'></i></a>";
		echo " / ";
		echo "<a href='#' class='slett-aktivitet' data-id='".$arrid."' data-title='".addslashes($aktivitet['tittel'])."'><i class='fa fa-times' title='Klikk for å slette'></i></a>";

		echo "</td>";
	}

	echo "</tr>";
}
echo "</table>";
