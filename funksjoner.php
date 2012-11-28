<?php

// Felles funksjoner som skal brukes mange plasser
// Ellers legg ting pï¿½ samme side

session_start();

define("SPAMFILTER","/kukk|informative|\<\/a\>|site|seite|Beach|Hera|Estate|lugo|migliore|informativo|significo1|casino|poker|viagra|mortgage|porno|porn|\[URL=|discount|rental|Oprah|Obama|lhxtfozb|itrpgkf/i");

function koble_til_database($database_host, $database_user, $database_string, $database_database){
	
    $connection = mysql_connect($database_host, $database_user, $database_string);
	
    if (!$connection) {
        echo "Kunne ikke koble opp mot database.<br>Prï¿½v igjen senere...";
        return false;
    }

	$select_db = mysql_select_db($database_database, $connection);
	
   	if(!$select_db){
   		echo "Kunne ikke velge database";
   		return false;
   	}

    return true;
}


function inkluder_side_fra_undermappe($sidenavn, $mappenavn){
	
	if( empty($sidenavn) ) $sidenavn = "forside";
	if( empty($mappenavn) ) $mappenavn = "sider";
 	
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

function hent_og_putt_inn_i_array($sql, $id_verdi){
	$query = mysql_query($sql);
	
	$array = Array();
	
	while($row = mysql_fetch_assoc($query)){
		$array[$row[$id_verdi]] = $row; 
	}
	
	return $array;
}

function hent_brukerdata($medlemid=""){
	if(empty($medlemid)){
		$medlemid = $_SESSION['medlemsid'];
	}
	return array($medlemid);
}

function er_logget_inn(){
	return isset($_SESSION["medlemsid"]);
}

//lister opp alle forumene med link til hvert forum 
function list_forum(){
	echo"
	<table>
	(TODO: skal være dynamisk...)
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
	

?>
