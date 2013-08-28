<?php

// Felles funksjoner som skal brukes mange plasser
// Ellers legg ting pï¿½ samme side

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

    return true;
}

function get($attributt) {
	return isset($_GET[$attributt]) ? mysql_real_escape_string($_GET[$attributt]) : null; 
}

function post($attributt) {
	return isset($_POST[$attributt]) ? mysql_real_escape_string($_POST[$attributt]) : null; 
}

function has($attributt) {
	return isset($_GET[$attributt]);
}

function has_post($attributt) {
	return isset($_POST[$attributt]);
}

function inkluder_side_fra_undermappe($sidenavn = "forside", $mappenavn = "sider"){
	
	$php_fil = $mappenavn."/".$sidenavn.".php";
	
	// Sjekk om siden fins i hovedmappen (vil ikke inkludere sider som er andre plasser)
	// hvis $page inneholder .. eller / så prøver noen å gå i undermapper, det vil vi ikke
	if( strpos($sidenavn,"..") === false || strpos($sidenavn,"/") === false || strpos($mappenavn,"..") === false ){
		
		if ( file_exists($php_fil) ) {
			include $php_fil;
		} else {
			include "sider/forside.php";
		}
		
	} else {
		include "sider/forside.php";
	}
}

function hent_og_putt_inn_i_array($sql, $id_verdi=""){
	$query = mysql_query($sql);
	
	$array = Array();
	
	while($row = mysql_fetch_assoc($query)){
		if(empty($id_verdi)) {
			$array = $row; 
		} else {
			$array[$row[$id_verdi]] = $row; 
		}
	}
	
	return $array;
}

function hent_brukerdata($medlemid = ""){
	if(empty($medlemid)){
		$medlemid = $_SESSION['medlemsid'];
	}

	if(er_logget_inn()){
		$sql = "SELECT medlemsid, fnavn, enavn, instrument, status, grleder, foto, adresse, postnr, poststed, email, tlfmobil, fdato, studieyrke,
					   startetibuk_date, sluttetibuk_date, bakgrunn, ommegselv, kommerfra 
				FROM `medlemmer` 
				WHERE `medlemsid`=".$medlemid;
	} else {
		$sql = "SELECT medlemsid, fnavn, enavn, status, instrument, grleder, foto, bakgrunn, kommerfra 
				FROM `medlemmer` 
				WHERE `medlemsid`=".$medlemid;
	}
	$mysql_result = mysql_query($sql);
	
	while($medlem = mysql_fetch_assoc($mysql_result)) {
		return $medlem;
	}
	
	die("Fant ikke medlem");
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
    	<td><a href=''>styret</a></td>
    	<td><a href=''>webkom</a></td></tr>";
	};
    echo"<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    </table>";
}

/*
 * Hent $antall siste nyheter av typen Public (default)
 */
function hent_siste_nyheter($antall, $type="Public"){
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=1 AND type='".$type."' ORDER BY tid DESC LIMIT ".$antall;

	return hent_og_putt_inn_i_array($sql, "nyhetsid");
}

function hent_eldre_konserter($antall, $type="nestekonsert"){
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=0 AND type='".$type."' ORDER BY tid DESC LIMIT ".$antall;

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
	
?>
