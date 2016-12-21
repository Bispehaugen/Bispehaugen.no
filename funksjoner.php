<?php

require 'vendor/autoload.php';
require_once realpath(dirname(__FILE__)) . "/koble_til_database.php";

// Felles funksjoner som skal brukes mange plasser
// Ellers legg ting på samme side

session_start();

define("SPAMFILTER","/kukk|informative|\<\/a\>|site|seite|Beach|Hera|Estate|lugo|migliore|informativo|significo1|casino|poker|viagra|mortgage|porno|porn|\[URL=|discount|rental|Oprah|Obama|lhxtfozb|itrpgkf/i");

// Algoritmen som brukes for å hashe passord. Når den er satt til PASSWORD_DEFAULT
// vil algoritmen automatisk bli erstattet når en bedre blir tilgjengelig.
$passord_algo = PASSWORD_DEFAULT;

/*
 * Håndterer php-feil ved å passe på at feilmeldingen blir lagt til i loggen, og at
 * brukeren får en feilmelding hvis skriptet ikke kan fortsette.
 *
 * Denne funksjonen vil ikke kunne håndtere de mest alvorlige feilene, og hvis det er
 * databasefeil vil ikke feilmeldingene bli lagret i databasen. Feilmeldingene må da
 * leses fra /var/log/apache2/error.log
 */
function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
    // Hvis feilkoden ikke skal registreres
    if (!(error_reporting() & $errno)) {
        return;
    }

    $message = "$errfile:$errline [$errno] $errstr";

    switch ($errno) {
    case E_USER_ERROR:
        logg("error", "FATAL ERROR $message");
        if (tilgang_endre()) {
            echo "FATAL ERROR $message";
        } else {
            echo "Det oppstod en feil vi ikke kunne rette. Webkom er varslet!";
        }
        // Koden kan ikke fortsette etter en slik feil
        exit(1);
        break;

    case E_USER_WARNING:
        logg("warning", "WARNING $message");
        break;

    case E_USER_NOTICE:
        logg("notice", "NOTICE $message");
        break;

    default:
        break;
    }

    // Fortsett å kjøre
    return true;
}
set_error_handler("error_handler");

/*
 * Håndterer exceptions som ikke blir håndtert ellers, og passer på å skrive
 * informasjonen til loggen. Programmet slutter når denne funksjonen fullfører,
 * så hvis skriptet skal fortsette må den håndteres med try...catch.
 *
 * PDO er satt til å kaste exceptions når noe går galt, men med mindre det er
 * forutsett skal ikke disse håndteres med try...catch. Hvis det er noe galt med
 * databasen eller SQL-koden er det vaneligvis ikke noe vits i å fortsette programmet,
 * så det er best å bare krasje.
 */
function exception_handler($e) {
    if ($e instanceof PDOException) {
        sqlerror("", $e);
    } else {
        $message = "UNCAUGHT EXCEPTION: {$e->getMessage()}\n\nException backtrace:\n".print_r($e->getTrace(), true);
        logg("exception", $message);
        if (tilgang_webmaster()) {
            echo "<pre>$message</pre>";
        } else {
            echo "Det oppstod en feil vi ikke kunne rette. Webkom er varslet!";
        }
    }
}
set_exception_handler("exception_handler");

/*
 * Rapporterer sql-feil ved å legge inn en detaljert rapport i weblog i databasen,
 * og ved å si ifra til brukeren hvis den har de nødvendige rettighetene.
 *
 * Det som rapporteres er:
 * * SQL-koden som ble kjørt (hvis den er vedlagt, se under)
 * * PDO errorkode og feilmelding (igjen, se under)
 * * Backtrace
 *
 * Det er ikke nødvendig å legge ved sql-koden siden linjenummeret funksjonen blir
 * kalt fra blir lagt ved, men det kan være nyttig for å identifisere problemet
 * uten å lete gjennom koden først. Det er heller ikke nødvendig å legge ved exception-et,
 * men 
 */
function sqlerror($sql = "", $e = "") {
    $sql = empty($sql) ? "" : "<br />SQL:<br />$sql<br /><br />";

    $message = "SQL ERROR:".$sql;
    if ($e instanceof PDOException) {
        $message .= "<br />PDO error: {$e->getMessage()}<br /><br />";
        $message .= "Exception backtrace:<br /><pre>".print_r($e->getTrace(), true)."</pre>";
    }
    logg("sqlerror", $message);
    if (tilgang_webmaster()) {
        die($message);
    }
    die("Det oppstod en feil vi ikke kunne rette. Webkom er varslet!");
}

function get($attributt) {
	return isset($_GET[$attributt]) ? $_GET[$attributt] : null; 
}

function post($attributt) {
	return isset($_POST[$attributt]) ? $_POST[$attributt] : null; 
}

function session($attributt) {
	return isset($_SESSION[$attributt]) ? $_SESSION[$attributt] : null; 
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

function formater_dato_tidspunkt($date) {
	return "kl. ".date("H:i m.d.Y", strtotime($date));
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

function tilgang_webmaster() {
	return $_SESSION['rettigheter'] == 10;
}

function tilgang_full() {
	return $_SESSION['rettigheter'] > 2;
}

function tilgang_intern() {
	return $_SESSION['rettigheter'] > 0;
}

function tilgang_endre() {
	return $_SESSION['rettigheter'] > 1;
}

function hent_komiteer_for_bruker() {
	$bruker = hent_brukerdata();
	$sql = "SELECT komiteid FROM `verv` WHERE medlemsid = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($bruker["medlemsid"]));
	return array_map("reset", $stmt->fetchAll());
}

function hent_medlemmer($alleMedlemmer = false, $hentStottemedlemmer = false) {
	//SQL-spørringen som henter ut alt fra "instrumenter" og "medlemmer" i DB
	//sjekker om man er logget inn for å vise "begrensede" medlemmer (som ikke vil vises eksternt)
	if(er_logget_inn() && $alleMedlemmer){
		$sql = "SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
		medlemmer.instrument, medlemmer.hengerfeste, medlemmer.bil, instrument.* FROM medlemmer,instrument WHERE instrumentid = instnr ORDER BY posisjon, 
		grleder  desc, status, fnavn";
	} else if(er_logget_inn() && $hentStottemedlemmer){
		$sql = "SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
		medlemmer.instrument, medlemmer.hengerfeste, medlemmer.bil, instrument.* FROM medlemmer,instrument WHERE status!='sluttet' AND instrumentid = instnr 
		ORDER BY posisjon, grleder desc, status, fnavn";
	} elseif(er_logget_inn()){
		$sql = "SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
		medlemmer.instrument, medlemmer.hengerfeste, medlemmer.bil, instrument.* FROM medlemmer,instrument WHERE status!='sluttet' AND instnr < 100 AND instrumentid = instnr 
		ORDER BY posisjon, grleder desc, status, fnavn";
	} else {
		$sql = "SELECT medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn, medlemmer.grleder, medlemmer.tlfmobil, medlemmer.status, 
		medlemmer.instrument, instrument.* FROM medlemmer,instrument WHERE status!='sluttet' AND begrenset=0 AND 
		instrumentid LIKE instnr ORDER BY posisjon, grleder desc, status, fnavn";
	}

	return hent_og_putt_inn_i_array($sql);
}

/*
 * Denne funksjonen kjører den gitte SQL-koden og returnerer alle resultatene i ett
 * array med en kolonne (id som standard) som indeks.
 *
 * Hvis det skal brukes variabler i SQL-koden skal disse legges inn som beskrevet
 * her: http://php.net/manual/en/pdo.prepared-statements.php
 * $params er ett array med alle verdiene av variablene, tilsvarende det første
 * argumentet til $stmt->execute($params). Les mer om det her:
 * http://php.net/manual/en/pdostatement.execute.php
 *
 * $index er hvilken kolonne som brukes som indeks for arrayet. Hvis ikke annet er
 * oppgitt brukes den første kolonnen i SQL-koden. For eksempel hvis SQLen er
 * 'SELECT id, fnavn, enavn FROM medlemmer' vil verdien av 'id' være indeks for
 * hver rad. Skriptet krasjer hvis $index ikke er en gyldig kolonne.
 */
function hent_og_putt_inn_i_array($sql, $params=array(), $index = ""){
    global $dbh;
	$array = Array();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    if (empty($index)) {
        return array_map("reset", $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
    } else {
        $result = array();
        while ($row = $stmt->fetch()) {
            // Hvis index ikke er i row er det noe veldig feil i koden, så det er hensiktsmessig å la den krasje
            $result[$row[$index]] = $row;
        }
        return $result;
    }
}

function hent_brukerdata($medlemid = ""){
    global $dbh;
	if(empty($medlemid)){
		if (has_session('medlemsid')) {
			$medlemid = session("medlemsid");
		} else {
			return Array();
		}
	}

	if(er_logget_inn()){
		$sql = "SELECT medlemsid, fnavn, enavn, brukernavn, I.instrument, I.instrumentid, I.instrumentid as instnr, status, grleder, foto, adresse, postnr, poststed, email, tlfmobil, fdato, studieyrke,
					   startetibuk_date, sluttetibuk_date, bakgrunn, ommegselv, kommerfra, begrenset, rettigheter, hengerfeste, bil
				FROM medlemmer AS M LEFT JOIN instrument AS I ON M.instnr=I.instrumentid";
	} else {
		$sql = "SELECT medlemsid, fnavn, enavn, status, I.instrument, I.instrumentid, I.instrumentid as instnr, grleder, foto, bakgrunn, kommerfra 
				FROM medlemmer AS M LEFT JOIN instrument AS I ON M.instnr=I.instrumentid";
	}

    $sql .= " WHERE `medlemsid`=?";

    if (is_array($medlemid)) {
        $medlemid = array_filter($medlemid);
        sort($medlemid);

        if (empty($medlemid)) {
            return Array();
        }
    } else {
        $medlemid = array($medlemid);
    }

	$medlemmer = Array();

    $stmt = $dbh->prepare($sql);
    foreach ($medlemid as $id) {
        $stmt->execute(array($id));
        $medlem = $stmt->fetch();
        if (count($medlemid) != 1) {
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

function generer_token() {
    return bin2hex(openssl_random_pseudo_bytes(16));
}

/*
 * Sjekker om brukeren er logget inn.
 *
 * Se funksjonen 'logg_inn' for en beskrivelse av hvordan 'husk_meg' funksjonen virker.
 */
function er_logget_inn(){
    // Simuler at man ikke er logget inn, for å kunne se forsiden uten å logge ut
    if (session("sim_logget_ut") && get("vis") == "intern") {
        $_SESSION["sim_logget_ut"] = false;
    } else if (get("vis") == "forside" || $_SESSION["sim_logget_ut"]) {
        $_SESSION["sim_logget_ut"] = true;
        return false;
    }
    return er_faktisk_logget_inn();
}

function er_faktisk_logget_inn(){
    global $dbh;
    if (isset($_SESSION["medlemsid"])) {
        // Sjekk at 'husk_meg' informasjonskapselen eksisterer hvis brukeren vil bli husket
        // Siden innloggingen skjer gjennom javascript er det sannsynligvis først her informasjons-
        // kapselen blir satt etter innlogging. Derfor må $_SESSION["login_token"] være tokenet og
        // ikke hashen for at det skal virke.
        if (isset($_SESSION["husk_meg"]) && $_SESSION["husk_meg"] && !isset($_COOKIE["husk_meg"])) {
            // Setter HttpOnly (den siste parameteren) til true for å beskytte mot XSS
            // Bruker setRAWcookie istedenfor setcookie for å ikke urlencode likehetstegnet
            setrawcookie("husk_meg", $_SESSION["login_serie"]."=".$_SESSION["login_token"], time()+3600*24*365,
                NULL, NULL, NULL, true);
        }
        return true;
    } else if (isset($_COOKIE["husk_meg"])) {
        $tmp = explode("=", $_COOKIE["husk_meg"]);
        if (count($tmp) != 2) {
            setcookie("husk_meg", "", time()-3600);
            return false;
        }
        $serie = $tmp[0];
        $token = hash("sha256", $tmp[1]);

        $sql = "SELECT 1 FROM `husk_meg` where `serie`=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($serie));
        if ($stmt->rowCount() > 0) {
            $sql = "SELECT medlemmer.medlemsid, medlemmer.rettigheter FROM husk_meg INNER JOIN medlemmer ON husk_meg.medlemsid=medlemmer.medlemsid WHERE husk_meg.serie=:serie AND husk_meg.token=:token";
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(":serie" => $serie, ":token" => $token));
            if ($stmt->rowCount() > 0) {
                // Logg inn
                $row = $stmt->fetch();
                $medlemsid = $row["medlemsid"];
                $rettigheter = $row["rettigheter"];
                // Vi setter 'husk' til false får å ikke lage en informasjonskapsel i en ny serie
                logg_inn($medlemsid, $rettigheter, false);

                // Lag en ny token og erstatt informasjonskapselen
                $sql = "DELETE FROM husk_meg WHERE serie=:serie AND token=:token";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array(":serie" => $serie, ":token" => $token));

                $new_token = generer_token();
                $token_hash = hash("sha256", $new_token);
                $sql = "INSERT INTO husk_meg (token, serie, sist_brukt, medlemsid) VALUES (:token_hash, :serie, NOW(), :medlemsid)";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array(":token_hash" => $token_hash, ":serie" => $serie, ":token" => $token,
                                     ":medlemsid" => $medlemsid));

                // Setter HttpOnly (den siste parameteren) til true for å beskytte mot XSS
                // Bruker setRAWcookie istedenfor setcookie for å ikke urlencode likehetstegnet
                setrawcookie("husk_meg", "$serie=$new_token", time()+3600*24*365, NULL, NULL, NULL, true);

                $_SESSION["husk_meg"] = true;
                $_SESSION["login_token"] = $new_token;
                $_SESSION["login_serie"] = $serie;

                return true;
            } else {
                /*
                 * Det brukes en token fra tidligere i rekken. Det betyr sannsynligvis at noen har
                 * stjålet informasjonskapselen og logget inn med den selv, slik at den originale
                 * ble ugyldig, og nå har brukeren prøvd å logge seg inn med den gamle informasjonskapselen.
                 * Ved å slette den fra databasen hindrer vi angriperen å komme inn med den, og brukeren kan
                 * logge inn med brukernavn og passord og bruke siden som vanlig.
                 */
                $stmt = $dbh->prepare("DELETE FROM husk_meg WHERE serie=?");
                $stmt->execute(array($serie));
                logg("Sikkerhet", "Mulig angrep på informasjonskapsel i serie '$serie'");
            }
        }

        // Informasjonskapselen er ugyldig, og blir derfor slettet
        setcookie("husk_meg", "", time()-3600);
    }

    return false;
}

/*
 * Hent $antall siste nyheter av typen Public (default)
 */
function hent_siste_nyheter($antall, $type="Public"){
	#For å kunne hente ut interne og public nyheter samtidig
    $params = array(":antall" => $antall);
	if ($type=="Intern+Public"){
		$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=1 AND (type='Public' OR type='Intern') ORDER BY tid DESC LIMIT :antall";
	} else{
		$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `nyheter` WHERE aktiv=1 AND type=:type ORDER BY tid DESC LIMIT :antall";
        $params[":type"] = $type;
	};
	return hent_og_putt_inn_i_array($sql, $params);
}

function hent_konserter($antall = "", $type="nestekonsert"){
    $params = array(":type" => $type);
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav, konsert_tid, normal_pris, student_pris, sted FROM `nyheter` WHERE type=:type AND konsert_tid>now() ORDER BY konsert_tid";

	if (!empty($antall)) {
		$sql .= " LIMIT :antall";
        $params[":antall"] = $antall;
	}

	return hent_og_putt_inn_i_array($sql, $params);
}


/*
 * Innlogging og utlogging.
 *
 * 'Husk meg'-funksjonen fungerer ved at det opprettes to tilfeldige verdier (token og serie),
 * som lagres i databasen og i en informasjonskapsel hos brukere. Så lenge PHPSESSIONen
 * er gyldig brukes denne til å identifisere brukerer, men når den går ut brukes
 * informasjonskapselen. Da vil den ene verdien (token) byttes mens den andre (serie) forblir.
 * Hvis informasjonskapselen blir snappet opp av en angriper og brukes til å logge inn vil tokenet
 * endres, så når brukeren prøver å logge inn senere vil det ikke gå, men siden serien er den samme
 * har vi oppdaget angrepet. Den serien blir da ugyldig slik at angriperen blir kastet ut, og
 * brukeren kan logge inn på nytt med brukernavn og passord. Tokens lagres i databasen som en hash,
 * slik at en som får tilgang til databasen ikke skal kunne bruke dem til å logge inn som noen andre.
 *
 * Dette er kanskje litt overkill, men det skader ikke å være på den sikre siden.
 */
function logg_inn($medlemsid, $rettigheter, $husk=false){
    global $dbh;
	$_SESSION["medlemsid"]   = $medlemsid;
	$_SESSION["rettigheter"] = $rettigheter;
	
	$bruker = innlogget_bruker();
	$navn = $bruker["fnavn"]." ".$bruker["enavn"];
	
	$melding = $navn . " logget nettopp inn med rettighetene: ".$rettigheter;

    if ($husk) {
        $token = generer_token();
        $token_hash = hash("sha256", $token);
        $serie = substr(generer_token(), 0, 12);
        $sql = "INSERT INTO `husk_meg` (token, serie, sist_brukt, medlemsid) VALUES (:token_hash, :serie, NOW(), :medlemsid)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(":token_hash" => $token_hash, ":serie" => $serie, ":medlemsid" => $medlemsid));

        // Setter HttpOnly (den siste parameteren) til true for å beskytte mot XSS
        // Bruker setRAWcookie istedenfor setcookie for å ikke urlencode likehetstegnet
        setrawcookie("husk_meg", "$serie=$token", time()+3600*24*365, NULL, NULL, NULL, true);
        $_SESSION["husk_meg"] = true;
        $_SESSION["login_token"] = $token;
        $_SESSION["login_serie"] = $serie;
    }
	
	logg("login", $melding);
}

function logg_ut() {
    global $dbh;
	$bruker = innlogget_bruker();
	$navn = $bruker["fnavn"]." ".$bruker["enavn"];
	
	$melding = $navn . " logget ut";
	
	logg("logout", $melding);

    if (isset($_SESSION["husk_meg"])) {
        $serie = $_SESSION["login_serie"];
        $sql = "DELETE FROM husk_meg WHERE serie=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($serie));

        setcookie("husk_meg", "", time()-3600);
        $_SESSION["husk_meg"] = "";
        $_SESSION["token"] = "";
        $_SESSION["serie"] = "";
    }
	
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
		$sql = "SELECT * FROM `arrangement` WHERE slettet=false";
	}else{
		$sql = "SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND slettet=false";
	}

	if ( er_logget_inn() && $_SESSION['rettigheter']==0 || get("p") == "bukaros"){
		$sql .= " AND public < 2";
	} elseif ( er_logget_inn() && tilgang_full()){
		$sql .= "";
	} elseif ( er_logget_inn() && tilgang_intern()){
		$sql .= " AND public < 2";
	} else {
		$sql .= " AND public = 1";
	}

	$sql .= " ORDER BY dato, start";

    $params = array();
	if ($skip != "" || $skip === 0) {
		$sql .= " LIMIT ?";
        if ($skip == 0 && $take > 0) {
            $params[] = $take;
        } else {
            $params[] = $skip;
            if ($take != "" || $take === 0) {
                $sql .= ", ?";
                $params[] = $take;
            }
        }
	}

    return hent_og_putt_inn_i_array($sql, $params);
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

abstract class Mappetype
{
    const Dokumenter = 1;
    const Bilder = 2;
    const Noter = 3;
}

abstract class Varslingstype
{
	const Kakebaker = 1;
	const Slagverkhjelper = 2;
}

function hent_mappetype_navn($mappetype) {
	switch($mappetype) {
		case Mappetype::Dokumenter:
			return "Dokumenter";
		case Mappetype::Noter:
			return "Noter";
		case Mappetype::Bilder:
			return "Bilder";
	}
	die("Fant ikke mappetype: " . $mappetype);
}

function thumb($bildePath, $width = "", $height = "") {
	$bildePath = str_replace("../", "", $bildePath);
	return "thumb.php?size=".$width."x".$height."&src=".$bildePath;
}

function thumbFilid($filid, $width = "", $height = "") {
	return "thumb.php?size=".$width."x".$height."&filid=".$filid;
}

function brukerlenke($bruker, $fulltNavn = Navnlengde::FulltNavn, $visBilde = false, $ekstra_info) {
	if (empty($bruker)) {
		return "";
	}
	
	$bilde = isset($bruker['foto']) ? $bruker['foto'] : "";

	$html = "<a class='brukerlenke' href='?side=medlem/vis&id=" . $bruker['medlemsid'] . "'>";
	if($visBilde && !empty($bilde)) {
		$html .= "<img src='".thumb($bilde, 250)."' />";
	}

	$html .= $ekstra_info;
	
	switch ($fulltNavn) {
		case Navnlengde::FulltNavn:
			$html .= "<span>".$bruker['fnavn'] ." ". $bruker['enavn'] ."</span>";
			break;
		case Navnlengde::FullInfo:
			$html .= "<span>". $bruker['fnavn'] ." ". $bruker['enavn'] ." <br> ". $bruker['tlfmobil'] ."</span>";
			break;
		case Navnlengde::Ingen:
			break;
		case Navnlengde::Fornavn:
		default:
			$html .= "<span title='".$bruker['fnavn'] ." ". $bruker['enavn'] ."'>". $bruker['fnavn'] ."</span>";
			break;
	}
	$html .= "";
	$html .= "</a>";
	
	return $html;
}

function dato($format, $tid) {
	$tid = empty($tid) ? time() : strtotime($tid);
	return date($format, $tid);
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
    global $dbh;
	$sql = "SELECT * FROM `arrangement` WHERE dato >= CURDATE() AND type='Konsert' AND slettet=false ORDER BY dato, start LIMIT 1";
    $stmt = $dbh->query($sql);
	return $stmt->fetch();
}

function neste_konsert_nyhet() {
	return hent_konserter(1);
}

function hent_styret() {
	$sql = "SELECT vervid, komite.komiteid, verv.komiteid, navn, verv.posisjon, komite.posisjon,
				   tittel, medlemmer.medlemsid, verv.medlemsid, epost, fnavn, enavn, foto
		    FROM komite, verv, medlemmer
		    WHERE medlemmer.medlemsid=verv.medlemsid 
		      AND komite.komiteid=verv.komiteid
		    ORDER BY komite.posisjon, verv.posisjon";
    return hent_og_putt_inn_i_array($sql);
}

function hent_komiteer() {
	$sql = "SELECT komiteid, navn, mail_alias FROM komite ORDER BY posisjon";
	$komiteer = hent_og_putt_inn_i_array($sql);

	return array_filter($komiteer, function($komite, $komiteid) {
	    return !empty($komite['navn']);
	}, ARRAY_FILTER_USE_BOTH);
}

function epost($to,$replyto,$subject,$message,$extra_header = "")  {

	$sendgrid = new SendGrid(SENDGRID_APIKEY);
	$email = new SendGrid\Email();
	$email
	    ->addTo($to)
	    ->setFrom('ikke-svar@bispehaugen.no')
	  	->setFromName('Bispehaugen.no')
	    ->setSubject($subject)
	    ->setText($message)
	    ->setHtml($message)
	    ->setReplyTo($replyto)
	    ->setHeaders(array('X-Sent-Using' => 'SendGrid-API', 'X-Transport' => 'web'))
	;

	// Or catch the error
	$melding = "To: ".$to." | Subject: ".$subject." | Message: ".$message;

	try {
	    $sendgrid->send($email);
		logg("epost", $melding);	
		return true;
	} catch(\SendGrid\Exception $e) {
		logg("error-epost", $melding);
	    echo $e->getCode();
	    foreach($e->getErrors() as $er) {
	        echo $er;
	    }
	    return false;
	}
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

/*
 * Denne funksjonen er utdatert og beholdes bare for kompabilitetsårsaker.
 * Den skal under ingen omstendighet brukes for å lagre nye passord.
 */
function generer_passord_hash($passord) {
	return sha1($passord);
}

function logg($type, $melding) {
    global $dbh;
	$sql = "INSERT INTO weblog (type, brukerid, melding, tid) VALUES (:type, :medlemsid, :melding, '".date("Y-m-d H:i:s")."')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array(":type" => $type, ":medlemsid" => session("medlemsid"), ":melding" => $melding));
}

function siste_sql_feil() {
	$enMaanedSiden = date("Y.m.d H:i:s", strtotime("-4 months"));
	$sql = "SELECT *, COUNT(*) AS telling FROM `weblog` WHERE type IN ('sqlerror', 'error', 'warning', 'notice') AND tid > '$enMaanedSiden' GROUP BY melding ORDER BY telling DESC LIMIT 200";
	return hent_og_putt_inn_i_array($sql);
}

function hent_besetning() {
	$sql = "SELECT besetningsid, besetningstype FROM noter_besetning";
	return hent_og_putt_inn_i_array($sql);
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


abstract class HttpStatus {
	const SUCCESS = "success";
	const ERROR = "error";
}

function json_response($status, $message, $errorStatusCode = 500) {
	if ($status == HttpStatus::ERROR) {
		http_response_code($errorStatusCode);
	}
	header('Content-type: application/json');
	echo json_encode(Array("status" => $status, "message" => $message));
}

function debug($array) {
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}

function innhold($navn, $tag="div", $class="", $id="") {
    global $dbh;
    $class = tilgang_full() ? "class='redigerbar $class'" : "";
    if (empty($id)) {
        $id = tilgang_full() ? "id='redigerbar-$navn'" : "";
    }
    $sql = "SELECT tekst FROM innhold WHERE navn=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($navn));
    if ($stmt->rowCount() == 1) {
        $innhold = stripslashes($stmt->fetchColumn());
        return "<$tag $id $class data-navn='$navn'>$innhold</$tag>";
    }
    if (tilgang_full()) {
        return "<$tag $id $class data-navn='$navn'>Skriv noe her...</$tag>";
    } else {
        return "";
    }
}
