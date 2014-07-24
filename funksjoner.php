<?php

// Felles funksjoner som skal brukes mange plasser
// Ellers legg ting på samme side

session_start();

define("SPAMFILTER","/kukk|informative|\<\/a\>|site|seite|Beach|Hera|Estate|lugo|migliore|informativo|significo1|casino|poker|viagra|mortgage|porno|porn|\[URL=|discount|rental|Oprah|Obama|lhxtfozb|itrpgkf/i");

function koble_til_database($database_host, $database_user, $database_string, $database_database){
	
    $connection = mysql_connect($database_host, $database_user, $database_string);
	
    if (!$connection) {
        echo "Kunne ikke koble opp mot database.<br>Prøv igjen senere...";
        return false;
    }

	$select_db = mysql_select_db($database_database, $connection);
	
   	if(!$select_db){
   		echo "Kunne ikke velge database";
   		return false;
   	}
	
	$utf8_db = mysql_set_charset("utf8", $connection);
	
	if (!$utf8_db) {
		echo "Kunne ikke bruke utf8 mot databasen";
		return false;
	}

    return true;
}

function get($attributt) {
	return isset($_GET[$attributt]) ? mysql_real_escape_string($_GET[$attributt]) : null; 
}

function post($attributt) {
	return isset($_POST[$attributt]) ? mysql_real_escape_string($_POST[$attributt]) : null; 
}

function session($attributt) {
	return isset($_SESSION[$attributt]) ? mysql_real_escape_string($_SESSION[$attributt]) : null; 
}

function has_get($attributt) {
	return isset($_GET[$attributt]);
}

function has_post($attributt) {
	return isset($_POST[$attributt]);
}

function has_session($attributt) {
	return isset($_SESSION[$attributt]);
}

function kanskje($array, $key) {
	if (is_array($array)) {
		if (array_key_exists($key, $array)) {
			return $array[$key];
		}
	}
	return null;
}

function bare_tidspunkt($datetime) {
	if (!empty($datetime)) {
		$tid = strtotime($datetime);
		return date("H:i", $tid);
	} 
	return null;
}

function inkluder_side_fra_undermappe($sidenavn = "forside", $mappenavn = "sider"){
	
	$php_fil = $mappenavn."/".$sidenavn.".php";
	
	// Sjekk om siden fins i hovedmappen (vil ikke inkludere sider som er andre plasser)
	// hvis $page inneholder .. eller / så prøver noen å gå i undermapper, det vil vi ikke
	if( strpos($sidenavn,"..") === false || strpos($sidenavn,"/") === false || strpos($mappenavn,"..") === false ){
		
		if ( file_exists($php_fil) ) {
			include $php_fil;
		} else {
			include "sider/ikke_funnet.php";
		}
		
	} else {
		include "sider/forside.php";
	}
}

function hent_og_putt_inn_i_array($sql, $id_verdi=""){
	$query = mysql_query($sql);
	
	$array = Array();

	if ($query === false) {
		die("Oppsto en feil i sql: " . $sql);
	}

	while($row = mysql_fetch_assoc($query)){
		if(empty($id_verdi)) {
			$array = $row; 
		} else {
			if (array_key_exists($id_verdi, $row)) {
				$array[$row[$id_verdi]] = $row; 
			}
		}
	}
	
	return $array;
}

function hent_brukerdata($medlemid = ""){
	if(empty($medlemid)){
		if (isset($_SESSION['medlemsid'])) {
			$medlemid = $_SESSION['medlemsid'];
		} else {
			return Array();
		}
	}

	if(er_logget_inn()){
		$sql = "SELECT medlemsid, fnavn, enavn, instrument, status, grleder, foto, adresse, postnr, poststed, email, tlfmobil, fdato, studieyrke,
					   startetibuk_date, sluttetibuk_date, bakgrunn, ommegselv, kommerfra 
				FROM `medlemmer`";
	} else {
		$sql = "SELECT medlemsid, fnavn, enavn, status, instrument, grleder, foto, bakgrunn, kommerfra 
				FROM `medlemmer`";
	}

	if (is_array($medlemid)) {
		$medlemid = array_filter($medlemid);
		sort($medlemid);
		$sql .= " WHERE `medlemsid` IN (".implode(',',$medlemid).")";
	} else {
		$sql .= " WHERE `medlemsid`=".$medlemid;
	}
	$query = mysql_query($sql);

	if ($query === false) {
		die("Oppsto en feil i hent_brukerdata. Sql: " . $sql);
	}

	$medlemmer = Array();

	while($medlem = mysql_fetch_assoc($query)) {
		if(is_array($medlemid)) {
			$medlemmer[$medlem['medlemsid']] = $medlem;
		} else {
			return $medlem;
		}
	}

	$medlemmer[0] = Array("fnavn" => "Ukjent", "enavn" => "", "medlemsid" => 0);

	return $medlemmer;
}

function hent_bruker($brukerdata, $id) {

	if (array_key_exists($id, $brukerdata)) {
		return $brukerdata[$id];
	}
	return $brukerdata[0];
}

function er_logget_inn(){
	return isset($_SESSION["medlemsid"]);
}

//lister opp alle forumene med link til hvert forum 
function list_forum(){
	echo"
	<table>
	<tr><td>Velg forum: </td>
	<td><a href='?side=forum/tema&id=2'>musikk & konserter</a></td>
	<td><a href='?side=forum/tema&id=1'>aktuelt</a></td>
    <td><a href=''>sosialt</a></td>
    <td><a href=''>påmeldinger</a></td>";  
    if($_SESSION['rettigheter']>1){
		echo"
    	<td><a href=''>musikkomitéen</a></td>
    	<td><a href='?side=forum/tema&id=4'>styret</a></td>
    	<td><a href='?side=forum/tema&id=3'>webkom</a></td></tr>";
	};
    echo"<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    </table>";


    /*



    //henter ut alle forumene og lister de opp sammen med når siste innlegg var
	//Denne skal egentlig brukse, men databasen er ikke tilstrekkelig oppdatert ennå
	//$sql="SELECT tittel, forum.forumid, pos, sisteinnleggid, innleggid, forum_innlegg.skrevetavid, forum_innlegg.skrevet, fnavn, enavn, medlemsid 
	//FROM forum, medlemmer, forum_innlegg WHERE innleggid=sisteinnleggid AND medlemsid=skrevetavid ORDER BY forumid;";
	
	$sql="SELECT tittel, forum.forumid, pos, sisteinnleggskrevet, sisteinnleggskrevetav
	FROM forum ORDER BY forumid;";
	$forumer = hent_og_putt_inn_i_array($sql, "forumid");
	
	//henter forumid til forum som har uleste innlegg
	$medlemsid= $_SESSION["medlemsid"];
	$sql="SELECT forum_tema.forumid FROM forum_leste, forum_tema WHERE medlemsid=".$medlemsid." AND forum_leste.temaid=forum_tema.temaid;";
	$uleste_forum = hent_og_putt_inn_i_array($sql, $id_verdi="forumid");
	
    #Det som printes på sida
    
   
    
    echo "<table class='forum'><tr><th></th><th>Forum</th><th>Sist oppdatert av</th></tr>";
  
   	//skriver ut alle forumene samt hvem som la inn siste innlegg og hvor lenge siden.
   	foreach($forumer as $forumid=>$forum){
   		//sjekker om man er admin og dermed skal se styret, webkom og musikkomite-forumene
   		if($_SESSION['rettigheter']>2 || $forum['forumid']<3){
   			if(array_key_exists($forumid, $uleste_forum) && $uleste_forum[$forumid]){
   				echo "<tr class='ulest'>";
			}else{
				echo "<tr>";
			};
			echo"<td></td><td><a href='?side=forum/tema&id=".$forum['forumid']."'>".$forum['tittel']."</a></td><td>
   			".$forum['sisteinnleggskrevetav']." - ";
   			echo ant_dager_siden($forum['sisteinnleggskrevet'])."</td></tr>";
		};
	};	

	echo "</table>";


 */
}

/*
 * Hent $antall siste nyheter av typen Public (default)
 */
function hent_siste_nyheter($antall, $type="Public"){
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=1 AND type='".$type."' ORDER BY tid DESC LIMIT ".$antall;

	return hent_og_putt_inn_i_array($sql, "nyhetsid");
}

function hent_konserter($antall = "", $type="nestekonsert"){
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, konsert_tid, normal_pris, student_pris, sted FROM `nyheter` WHERE type='".$type."' ORDER BY tid DESC";

	if (!empty($antall)) {
		$sql .= " LIMIT ".$antall;
	}

	return hent_og_putt_inn_i_array($sql, "nyhetsid");
}


// Innloggin og utlogging
function logg_inn($medlemsid, $rettigheter){
	$_SESSION["medlemsid"]   = $medlemsid;
	$_SESSION["rettigheter"] = $rettigheter;
}

function logg_ut() {
	// Slett sessions
	$_SESSION["medlemsid"]   = "";
	$_SESSION["rettigheter"] = "";
	
	session_unset();
    session_destroy();
}
	
function ant_dager_siden($dato){
	//dager siden siste innlegg
   			$dagersiden= floor(abs(strtotime(date('Y-m-d'))-strtotime(substr($dato,0,10)))/ (60*60*24));
   			
   			if ($dagersiden==0){
   				$dagersiden_som_tekst = " i dag";
   			}
			elseif ($dagersiden==1){
   				$dagersiden_som_tekst = " i går";
   			}
			elseif($dagersiden<7){
				$dagersiden_som_tekst = " for ".$dagersiden." dager siden";
			}
			elseif($dagersiden<31){
				$dagersiden_som_tekst = " for ".floor($dagersiden/7)." uker siden";
			}elseif($dagersiden<256){
				$dagersiden_som_tekst = " for ".floor($dagersiden/30)." måneder siden";
			}else{
				$dagersiden_som_tekst = date("d. M Y",strtotime(substr($dato,0,10)));
			};
			return "<i>".$dagersiden_som_tekst."</i>";
};

function hent_aktiviteter($skip = "", $take = "") {

	$sql = "SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND slettet=false ";

	if ( er_logget_inn() && $_SESSION['rettigheter']==0 || get("p") == "bukaros"){
		$sql .= "";
	} elseif ( er_logget_inn() && $_SESSION['rettigheter']==1){
		$sql .= " AND public < 2";
	} else {
		$sql .= " AND public = 1";
	}

	$sql .= " ORDER BY dato, start ";

	if (!empty($skip)) {
		$sql .= " LIMIT ".$skip;
		
		if (!empty($take)) {
			$sql .= " , ".$take;
		}
	}

	return hent_og_putt_inn_i_array($sql, $id_verdi="arrid");
}
	
function sett_sisteinnleggid($temaid){
	//oppdaterer sisteinnleggid i forum_tema-tabellen
	$sql="SELECT innleggid FROM forum_innlegg_ny WHERE temaid=".$temaid." ORDER BY innleggid DESC LIMIT 1";
	$result=mysql_query($sql);
	$sisteinnleggid=mysql_result($result, '0');
	
	$sql="UPDATE forum_tema SET sisteinnleggid=".$sisteinnleggid." WHERE temaid=".$temaid;
	mysql_query($sql);
		
	//oppdaterer sisteinnleggig i forum-tabellen
	$sql="SELECT sisteinnleggid, forumid FROM forum_tema ORDER BY sisteinnleggid DESC LIMIT 1";
	$sisteinnlegg = hent_og_putt_inn_i_array($sql, "sisteinnleggid");
	
	foreach($sisteinnlegg as $sisteinnleggid){
		$sql="UPDATE forum SET sisteinnleggid=".$sisteinnleggid['sisteinnleggid']." WHERE forumid=".$sisteinnleggid['forumid'];
	};
	mysql_query($sql);
}

function innlogget_bruker() {
	if (isset($_SESSION['innlogget_bruker'])){
		$bruker = $_SESSION['innlogget_bruker'];
	} else {
		$bruker = hent_brukerdata();
		$_SESSION['innlogget_bruker'] = $bruker;
	}

	return $bruker;
}

abstract class Navnlengde
{
    const FulltNavn = 0;
    const Fornavn = 1;
    const Ingen = 2;
}

function brukerlenke($bruker, $fulltNavn = Navnlengde::FulltNavn, $visBilde = false) {
	if (empty($bruker)) {
		return "";
	}
	
	$bilde = isset($bruker['foto']) ? $bruker['foto'] : "";

	$html = "<a class='brukerlenke' href='?side=medlem/vis&id=" . $bruker['medlemsid'] . "'>";
	if($visBilde && !empty($bilde)) {
		$html .= "<img src='".$bilde."' />";
	}
	
	switch ($fulltNavn) {
		case Navnlengde::FulltNavn:
			$html .= "<span>". $bruker['fnavn'] ." ". $bruker['enavn'] ."</span>";
			break;
		case Navnlengde::Ingen:
			break;
		case Navnlengde::Fornavn:
		default:
			$html .= "<span>". $bruker['fnavn'] ."</span>";
			break;
	}
	$html .= "";
	$html .= "</a>";
	
	return $html;
}

function dato($format, $tid) {
	return date($format, strtotime($tid));
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function erForside() {
	return (!has_get("side") || strtolower(get('side')) == "forside") && !er_logget_inn();
}

function fancyDato($tid, $visTimer = false) {
	$time = strtotime($tid); 

	if($time == 0) {
		return '';
	}

	$html = '<time class="fancy-date" datetime="'.date("c", $time).'" title="'.strftime("%c", $time).'">';

	$html .= '
		<div class="boks">
			<div class="weekday">'.strftime("%a", $time).'</div>
			<div class="day">'.date("j", $time).'.</div>
			<div class="month">'.strftime("%b", $time).'</div>
			<div class="year">'.date("Y", $time).'</div>
		</div>
	';

	if ($visTimer === true) {
		$html .= '<div class="time boks">kl. '.date("H:i", $time).'</div>';
	}

	$html .= '</time>';

	return $html;
}

function visKartNederst() {
	return (erForside() || get('side') === "annet" ) && !er_logget_inn();
}