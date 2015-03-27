<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
error_reporting(E_ERROR | E_PARSE);
include_once "db_config.php";
include_once "funksjoner.php";
require_once( "./icalendar.php" );
//header('Content-type: text/calendar; charset=utf-8');

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

$aktiviteter = hent_aktiviteter();
$filename = date("d-m-Y").".ics";

$tz     = "Europe/Oslo";
 // define time zone
$config = array( "unique_id" => "bispehaugen.no" );
  // set a (site) unique id
              // , "TZID" => $tz );
  // opt. "calendar" timezone
$v = new vcalendar( $config );
  // create a new calendar instance

$v->setProperty( "method", "PUBLISH" );
  // required of some calendar software
$v->setProperty( "x-wr-calname", "Bispehaugen Ungdomskorps" );
  // required of some calendar software
$v->setProperty( "X-WR-CALDESC", "Oversikt over aktivitetene for Bispehaugen Ungdomskorps" );
  // required of some calendar software
$v->setProperty( "X-WR-TIMEZONE", $tz );
  // required of some calendar software

$xprops = array( "X-LIC-LOCATION" => $tz );
  // required of some calendar software
//iCalUtilityFunctions::createTimezone( $v, $tz, $xprops );
  // create timezone component(-s) opt. 1
  // based on present date

$bruker = hent_brukerdata();
$brukerid = "";
if (array_key_exists('medlemsid', $bruker)) {
	$brukerid = $bruker['medlemsid'];
}

foreach($aktiviteter as $id => $aktivitet) {
	$uid = "?side=aktiviteter/liste&id=".$id;
	$address = $aktivitet["sted"];
	$uri = "http://bispehaugen.no";
	$title = $aktivitet["tittel"];
	$versjon = $aktivitet["versjon"];

	$startstreng = $aktivitet["dato"];
	if (!empty($aktivitet["starttid"])) {
		$startstreng .= " ".$aktivitet["starttid"];
	}
	$datestart = date_parse(str_replace(" 24:", " 00:", $startstreng));

    // Heldagseventer starter 00:00 og slutter 00:00 neste dag, legg på en dag
	if ($aktivitet["sluttid"]=="00:00:00") {
		$sluttstreng = date("Y-m-d H:i:s", strtotime($aktivitet["dato"]." 00:00:00") + 24*60*60);
	} else {
		$sluttstreng = $aktivitet["dato"];
	}

	if (!empty($aktivitet["sluttid"])) {
		$sluttstreng .= " ".$aktivitet["sluttid"];
	}
	$dateend = date_parse(str_replace(" 24:", " 00:", $sluttstreng));

	$description = $aktivitet["ingress"];

	if (!empty($aktivitet["kakebaker"])) {
		$description = "Kakebaker: " . $aktivitet["kakebaker"] . "\r\n" . $description;
	}

	if (!empty($aktivitet["hjelpere"])) {
		$description = "Slagverkhjelpere: " . $aktivitet["hjelpere"] . "\r\n" . $description;
	}

	if (!empty($aktivitet["oppmoetetid"])) {
		$description = "Oppmøte kl. " . $aktivitet["oppmoetetid"] . "\r\n" . $description;
	}
	$description = str_replace("\r\n", "\\n", $description);


	$vevent = & $v->newComponent( "vevent" );
	  // create an event calendar component
	$vevent->setProperty( "uid", $uid);
	$vevent->setProperty( "uri", $uri + "/" + $uid);

	$vevent->setProperty( "dtstart", $datestart );

	$vevent->setProperty( "dtend",   $dateend );
	$vevent->setProperty( "LOCATION", $address );
	  // property name - case independent
	$vevent->setProperty( "summary", $title);
	$vevent->setProperty( "description", $description );

}

iCalUtilityFunctions::createTimezone( $v, $tz, $xprops);

logg("ical", "{bruker:'"+$brukerid+"', antall_arrangement: '"+count($aktiviteter)+"'}");

$v->returnCalendar();
