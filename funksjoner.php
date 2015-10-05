<?php

global $connection;

// Felles funksjoner som skal brukes mange plasser
// Ellers legg ting på samme side

session_start();

define("SPAMFILTER","/kukk|informative|\<\/a\>|site|seite|Beach|Hera|Estate|lugo|migliore|informativo|significo1|casino|poker|viagra|mortgage|porno|porn|\[URL=|discount|rental|Oprah|Obama|lhxtfozb|itrpgkf/i");

function koble_til_database($database_host, $database_user, $database_string, $database_database){

	global $connection;
	
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
	if (isset($_POST[$attributt])) {
		$data = $_POST[$attributt];
		if (is_array($data)) {
			return array_map('mysql_real_escape_string', $data);
		} else {
			return mysql_real_escape_string($data);
		}
	}
	return null; 
}

function session($attributt) {
	return isset($_SESSION[$attributt]) ? mysql_real_escape_string($_SESSION[$attributt]) : null; 
}

function has_get($attributt) {
	return isset($_GET[$attributt]);
}

function has_post($attributt = "") {
	if (empty($attributt)) {
		return isset($_POST) && !empty($_POST);
	}
	return isset($_POST[$attributt]);
}

function has_session($attributt) {
	if (empty($attributt)) {
		return isset($_SESSION) && !empty($_SESSION);
	}
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
	
	if (!er_logget_inn()) {
		$reservertePlasser = Array("intern/", "forum/", "bilder/", "noter/");
		
		foreach($reservertePlasser as $plass) {
			if ( strpos($sidenavn, $plass) === 0) {
				$sidenavn = "ikke_funnet";
				break;
			}
		}
	}
	
	$php_fil = $mappenavn."/".$sidenavn.".php";
	
	// Sjekk om siden fins i hovedmappen (vil ikke inkludere sider som er andre plasser)
	// hvis $page inneholder .. eller / så prøver noen å gå i undermapper, det vil vi ikke
	if( strpos($sidenavn,"..") === false || strpos($sidenavn,"/") === false || strpos($mappenavn,"..") === false ){
		
		if ( file_exists($php_fil) ) {
			try {
				include $php_fil;
			} catch (Exception $e) {
				logg("exception", $e);
				die($e);
			    include "sider/ikke_funnet.php";
			}
		} else {
			include "sider/ikke_funnet.php";
		}
		
	} else {
		include "sider/forside.php";
	}
}

function tilgang_full() {
	return $_SESSION['rettigheter'] == 3;
}

function tilgang_intern() {
	return $_SESSION['rettigheter'] > 0;
}

function tilgang_endre() {
	return $_SESSION['rettigheter'] > 1;
}

function hent_komiteer_for_bruker() {
	$bruker = hent_brukerdata();
	$sql = "SELECT komiteid FROM `verv` WHERE medlemsid = ".$bruker['medlemsid'];
	return hent_og_putt_inn_i_array($sql);
}

function hent_og_putt_inn_i_array($sql, $id_verdi=""){
	$query = mysql_query($sql);
	
	$array = Array();
	
	if ($query === false) {
		logg("sqlerror", "{fil: '".$_SERVER["SCRIPT_NAME"]."', query:'".$_SERVER['QUERY_STRING']."', sql:'".$sql."'}");
		
		if ($_SESSION['rettigheter']>1) {
			die("Feil i fil: ".$_SERVER["SCRIPT_NAME"]."?".$_SERVER['QUERY_STRING'].", sql: ".$sql);
		}
		die("Det oppstod en feil vi ikke kunne rette. Webkom er varslet!");
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
		if (has_session('medlemsid')) {
			$medlemid = session("medlemsid");
		} else {
			return Array();
		}
	}

	if(er_logget_inn()){
		$sql = "SELECT medlemsid, fnavn, enavn, brukernavn, I.instrument, I.instrumentid, I.instrumentid as instnr, status, grleder, foto, adresse, postnr, poststed, email, tlfmobil, fdato, studieyrke,
					   startetibuk_date, sluttetibuk_date, bakgrunn, ommegselv, kommerfra, begrenset, rettigheter
				FROM medlemmer AS M LEFT JOIN instrument AS I ON M.instnr=I.instrumentid";
	} else {
		$sql = "SELECT medlemsid, fnavn, enavn, status, I.instrument, I.instrumentid, I.instrumentid as instnr, grleder, foto, bakgrunn, kommerfra 
				FROM medlemmer AS M LEFT JOIN instrument AS I ON M.instnr=I.instrumentid";
	}

	if (is_array($medlemid)) {
		$medlemid = array_filter($medlemid);
		sort($medlemid);

		if (empty($medlemid)) {
			return Array();
		}

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

	return $medlemmer;
}

function hent_bruker($brukerdata, $id) {

	if (array_key_exists($id, $brukerdata)) {
		return $brukerdata[$id];
	}
	return Array("fnavn" => "Ukjent", "enavn" => "", "medlemsid" => 0);
}

function er_logget_inn(){
	return isset($_SESSION["medlemsid"]);
}

/*
 * Hent $antall siste nyheter av typen Public (default)
 */
function hent_siste_nyheter($antall, $type="Public"){
	#For å kunne hente ut interne og public nyheter samtidig
	if ($type=="Intern+Public"){
		$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=1 AND (type='Public' OR type='Intern') ORDER BY tid DESC LIMIT ".$antall;		
	} else{
		$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=1 AND type='".$type."' ORDER BY tid DESC LIMIT ".$antall;
	};
	return hent_og_putt_inn_i_array($sql, "nyhetsid");
}

function hent_konserter($antall = "", $type="nestekonsert"){
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, konsert_tid, normal_pris, student_pris, sted FROM `nyheter` WHERE type='".$type."' AND konsert_tid>now() ORDER BY konsert_tid";

	if (!empty($antall)) {
		$sql .= " LIMIT ".$antall;
	}

	$id_verdi = "nyhetsid";
	if ($antall == 1) {
		$id_verdi = "";
	}

	return hent_og_putt_inn_i_array($sql, $id_verdi);
}


// Innloggin og utlogging
function logg_inn($medlemsid, $rettigheter){
	$_SESSION["medlemsid"]   = $medlemsid;
	$_SESSION["rettigheter"] = $rettigheter;
	
	$bruker = innlogget_bruker();
	$navn = $bruker["fnavn"]." ".$bruker["enavn"];
	
	$melding = $navn . " logget nettopp inn med rettighetene: ".$rettigheter;
	
	logg("login", $melding);
}

function logg_ut() {
	$bruker = innlogget_bruker();
	$navn = $bruker["fnavn"]." ".$bruker["enavn"];
	
	$melding = $navn . " logget ut";
	
	logg("logout", $melding);
	
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
	} elseif ($dagersiden==1){
		$dagersiden_som_tekst = " i går";
	} elseif($dagersiden<7){
		$dagersiden_som_tekst = " for ".$dagersiden." dager siden";
	} elseif($dagersiden<31){
		$dagersiden_som_tekst = " for ".floor($dagersiden/7)." uker siden";
	} elseif($dagersiden<256){
		$dagersiden_som_tekst = " for ".floor($dagersiden/30)." måneder siden";
	} else {
		$dagersiden_som_tekst = date("d. M Y",strtotime(substr($dato,0,10)));
	};
	return "<i>".$dagersiden_som_tekst."</i>";
};

function hent_aktiviteter($skip = "", $take = "", $alle = "") {

	if ($alle==1){
		$sql = "SELECT * FROM `arrangement` WHERE slettet=false ";
	}else{
		$sql = "SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND slettet=false ";
	}

	if ( er_logget_inn() && $_SESSION['rettigheter']==0 || get("p") == "bukaros"){
		$sql .= "";
	} elseif ( er_logget_inn() && $_SESSION['rettigheter']==1){
		$sql .= " AND public < 2";
	} else {
		$sql .= " AND public = 1";
	}

	$sql .= " ORDER BY dato, start ";

	if ($skip != "" || $skip === 0) {
		$sql .= " LIMIT ".$skip;
		
		if ($take != "" || $take === 0) {
			$sql .= " , ".$take;
		}
	}

	$id_verdi = "arrid";
	if ($take == 1) {
		$id_verdi = "";
	}

	return hent_og_putt_inn_i_array($sql, $id_verdi);
}

function innlogget_bruker() {
	if (has_session('innlogget_bruker') && !empty($_SESSION['innlogget_bruker'])){
		$bruker = $_SESSION['innlogget_bruker'];
	} else {
		$bruker = hent_brukerdata();
		$_SESSION['innlogget_bruker'] = $bruker;
	}

	return $bruker;
}

function innlogget_bruker_oppdatert() {
	unset($_SESSION['innlogget_bruker']);
}

abstract class Navnlengde
{
    const FulltNavn = 0;
    const Fornavn = 1;
    const Ingen = 2;
    const FullInfo = 3;
}

function thumb($bildePath, $width = "", $height = "") {
	$bildePath = str_replace("../", "", $bildePath);
	return "thumb.php?size=".$width."x".$height."&src=".$bildePath;
}

function thumbFilid($filid, $width = "", $height = "") {
	return "thumb.php?size=".$width."x".$height."&filid=".$filid;
}

function brukerlenke($bruker, $fulltNavn = Navnlengde::FulltNavn, $visBilde = false, $stilling) {
	if (empty($bruker)) {
		return "";
	}
	
	$bilde = isset($bruker['foto']) ? $bruker['foto'] : "";

	$html = "<a class='brukerlenke' href='?side=medlem/vis&id=" . $bruker['medlemsid'] . "'>";
	if($visBilde && !empty($bilde)) {
		$html .= "<img src='".thumb($bilde, 250)."' />";
	}
	
	switch ($fulltNavn) {
		case Navnlengde::FulltNavn:
			$html .= "<span>".$bruker['fnavn'] ." ". $bruker['enavn'] ."</span>";
			break;
		case Navnlengde::FullInfo:
			$html .= "<span>".$stilling." <br> ". $bruker['fnavn'] ." ". $bruker['enavn'] ." <br> ". $bruker['tlfmobil'] ."</span>";
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

function neste_ovelse() {
	return hent_aktiviteter(0, 1);
}

function neste_konsert_arrangement() {
	$sql = "SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND type='Konsert' AND slettet=false ORDER BY dato, start LIMIT 1";
	return hent_og_putt_inn_i_array($sql);
}

function neste_konsert_nyhet() {
	return hent_konserter(1);
}

function epost_gammel($to,$replyto,$subject,$message,$extra_header = "") {
	$eol = PHP_EOL;

	$headers = "";

	$headers .= "Message-ID: <".time()." ikke-svar@bispehaugen.no>".$eol;
	$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
	# Boundry for marking the split & Multitype Headers
	$mime_boundary=md5(time());
	$headers .= 'MIME-Version: 1.0'.$eol;
	$headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

	$realfrom_tmp = getenv("REMOTE_HOST") ? getenv("REMOTE_HOST") : getenv("REMOTE_ADDR");
	$headers .= "Real-From: ".$realfrom_tmp.$eol;
	$headers .= "From: Bispehaugen.no <ikke-svar@bispehaugen.no>".$eol;
	$headers .= "Reply-To: ".$replyto.$eol;
	$headers .= "Return-Path: ".$replyto.$eol;    // these two to set reply address

	if (!empty($extra_header)) {
		$headers .= $eol.$extra_header;
	}

	$epostBleSendt = mail($to,$subject,$message,$headers);

	$melding = "To: ".$to." | Subject: ".$subject." | Message: ".$message." | Headers: ".$headers;

	if ($epostBleSendt) {
		logg("epost", $melding);	
	} else {
		logg("error-epost", $melding);
	}
	return $epostBleSendt;
}

function epost($to,$replyto,$subject,$message,$extra_header = "")  {

	require_once 'mail/swift_required.php';

	$transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587)
	  ->setUsername(MAIL_USERNAME)
	  ->setPassword(MAIL_PASSWORD);

	$mailer = Swift_Mailer::newInstance($transport);

	$email = Swift_Message::newInstance('SendEpost')
	  ->setFrom(array('ikke-svar@bispehaugen.no' => 'Bispehaugen.no'))
	  ->setTo(array($to))
	  ->setSubject($subject)
	  ->setBody($message);

	$melding = "To: ".$to." | Subject: ".$subject." | Message: ".$message;

	$epostBleSendt = $mailer->send($email);

	if ($epostBleSendt) {
		logg("epost", $melding);	
	} else {
		logg("error-epost", $melding);
	}
	return $epostBleSendt;
}

function feilmeldinger($feilmeldinger) {
	$html = "";
	
	if(!empty($feilmeldinger)) {
		
		$html = '<ul class="feilmeldinger">';
		
		foreach($feilmeldinger as $feilmelding){
			$html .= "<li class='feil'>".$feilmelding."</li>";
		}
		$html .= "</ul>";
	}
	return $html;
}

function generer_passord_hash($passord) {
	return sha1($passord);
}

function logg($type, $melding) {
	$sql = "INSERT INTO weblog (type, brukerid, melding, tid) VALUES ('$type', '".mysql_real_escape_string(session("medlemsid"))."', '".mysql_real_escape_string($melding)."', '".date("Y-m-d H:i:s")."')";
	mysql_query($sql);
}

function siste_sql_feil() {
	$enMaanedSiden = date("Y.m.d H:i:s", strtotime("-4 months"));
	$sql = "SELECT *, COUNT(*) AS telling FROM `weblog` WHERE type IN ('sqlerror') AND tid > '$enMaanedSiden' GROUP BY melding ORDER BY telling DESC LIMIT 200";
	return hent_og_putt_inn_i_array($sql, 'id');
}

function finn_filtype($filnavn) {
	$fileNameArray = preg_split("/\./", $filnavn);
	if (count($fileNameArray) > 1) {
		return $fileNameArray[count($fileNameArray)-1];
	}
	return false;
}

function fil_ikon($filtype) {
	$filtypeIkon = 'fa-file-o';
	switch($filtype) {
		case 'pdf':
			$filtypeIkon = 'fa-file-pdf-o';
			break;
		case 'jpg':
		case 'jpeg':
		case 'tif':
		case 'bmp':
		case 'png':
		case 'gif':
			$filtypeIkon = 'fa-file-image-o';
			break;
		case 'ppt':
		case 'pptx':
			$filtypeIkon = 'fa-file-powerpoint-o';
			break;
		case 'doc':
		case 'docx':
			$filtypeIkon = 'fa-file-word-o';
			break;
		case 'xls':
		case 'xlsx':
			$filtypeIkon = 'fa-file-excel-o';
			break;
		case 'zip':
		case 'tar':
		case 'gz':
		case '7z':
			$filtypeIkon = 'fa-file-zip-o';
			break;
		case 'mp3':
		case 'midi':
		case 'wav':
		case 'm4a':
			$filtypeIkon = 'fa-file-audio-o';
			break;
		case 'avi':
		case 'mp4':
		case 'wmv':
		case 'mov':
			$filtypeIkon = 'fa-file-video-o';
			break;
	}
	return $filtypeIkon;
}

function fil_er_bilde($filtype) {
	switch(strtolower($filtype)) {
		case 'jpg':
		case 'jpeg':
		case 'tif':
		case 'bmp':
		case 'png':
		case 'gif':
			return true;
		}
	return false;
}

function fornorske($navn) {
	$navnUtenNorskeTegn = preg_replace("/[^A-ZÆØÅa-zæøå0-9\-.]/", '_', $navn);	
	$navnUtenNorskeTegn = str_replace("Æ", 'AE', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("æ", 'ae', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("Ø", 'O', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("ø", 'o', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("Å", 'AA', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("å", 'aa', $navnUtenNorskeTegn);	
	return $navnUtenNorskeTegn;
}

