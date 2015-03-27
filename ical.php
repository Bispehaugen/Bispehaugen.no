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
	$uid = "Bispehaugen.no/Arrangement/".$id;
	$address = $aktivitet["sted"];
	$uri = "http://bispehaugen.no/?side=aktiviteter/liste&id=".$id;
	$title = $aktivitet["tittel"];
	$versjon = $aktivitet["versjon"];

	$datestart = date_parse($aktivitet["start"]);
	$dateend = date_parse($aktivitet["slutt"]);

	$description = $aktivitet["ingress"];

	if (!empty($aktivitet["kakebaker"])) {
		$description = "Kakebaker: " . $aktivitet["kakebaker"] . "\r\n" . $description;
	}

	if (!empty($aktivitet["hjelpere"])) {
		$description = "Slagverkhjelpere: " . $aktivitet["hjelpere"] . "\r\n" . $description;
	}

	if (!empty($aktivitet["oppmoetetid"]) && !$aktivitet["oppmoetetid"] == "00:00:00") {
		$description = "Oppmøte kl. " . $aktivitet["oppmoetetid"] . "\r\n" . $description;
	}
	$description = str_replace("\r\n", "\\n", $description);


	$vevent = & $v->newComponent( "vevent" );
	  // create an event calendar component
	$vevent->setProperty( "uid", $uid);
	$vevent->setProperty( "uri", $uri);

	$vevent->setProperty( "dtstart", $datestart );

	$vevent->setProperty( "dtend",   $dateend );
	$vevent->setProperty( "LOCATION", $address );
	  // property name - case independent
	$vevent->setProperty( "summary", $title);
	$vevent->setProperty( "description", $description );

}

//iCalUtilityFunctions::createTimezone( $v, $tz, $xprops);

logg("ical", "{bruker:'"+$brukerid+"', antall_arrangement: '"+count($aktiviteter)+"'}");

$v->returnCalendar();
