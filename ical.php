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
	$uid = "Bispehaugen.no/arr/".$id;
	$address = utf8_encode($aktivitet["sted"]);
	$uri = "http://bispehaugen.no";
	$title = utf8_encode($aktivitet["tittel"]);
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

	$description =  utf8_encode($aktivitet["ingress"]);

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

/*
$valarm = & $vevent->newComponent( "valarm" );
  // create an event alarm
$valarm->setProperty("action", "DISPLAY" );
$valarm->setProperty("description", $vevent->getProperty( "description" );
  // reuse the event description

$d = sprintf( "%04d%02d%02d %02d%02d%02d", 2007, 3, 31, 15, 0, 0 );
iCalUtilityFunctions::transformDateTime( $d, $tz, "UTC", "Ymd\THis\Z");
$valarm->setProperty( "trigger", $d );
  // create alarm trigger (in UTC datetime)
.. .

$vevent = & $v->newComponent( "vevent" );
  // create next event calendar component
$vevent->setProperty( "dtstart", "20070401", array("VALUE" => "DATE"));
  // alt. date format, now for an all-day event
$vevent->setProperty( "organizer" , "boss@icaldomain.com" );
$vevent->setProperty( "summary", "ALL-DAY event" );
$vevent->setProperty( "description", "A description for an all-day event" );
$vevent->setProperty( "resources", "COMPUTER PROJECTOR" );
$vevent->setProperty( "rrule", array( "FREQ" => "WEEKLY"
                                          , "count" => 4 ));
  // weekly, four occasions
$vevent->parse( "LOCATION:1CP Conference Room 4350" );
  // support parse of strict rfc2445/rfc5545 text
.. .
  // all calendar components are described in rfc5545
  // a complete method list in iCalcreator manual
.. .
iCalUtilityFunctions::createTimezone( $v, $tz, $xprops);
  // create timezone component(-s) opt. 2
  // based on all start dates in events (i.e. dtstart)
*/




/*

// Variables used in this script:
//   $filename    - the name of this file for saving (e.g. my-event-name.ics)
//
// Notes:
//  - the UID should be unique to the event, so in this case I'm just using
//    uniqid to create a uid, but you could do whatever you'd like.
//
//  - iCal requires a date format of "yyyymmddThhiissZ". The "T" and "Z"
//    characters are not placeholders, just plain ol' characters. The "T"
//    character acts as a delimeter between the date (yyyymmdd) and the time
//    (hhiiss), and the "Z" states that the date is in UTC time. Note that if
//    you don't want to use UTC time, you must prepend your date-time values
//    with a TZID property. See RFC 5545 section 3.3.5
//
//  - The Content-Disposition: attachment; header tells the browser to save/open
//    the file. The filename param sets the name of the file, so you could set
//    it as "my-event-name.ics" or something similar.
//
//  - Read up on RFC 5545, the iCalendar specification. There is a lot of helpful
//    info in there, such as formatting rules. There are also many more options
//    to set, including alarms, invitees, busy status, etc.
//
//      https://www.ietf.org/rfc/rfc5545.txt

// 1. Set the correct headers for this file
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

//http://www.google.com/calendar/render?cid=http://org.ntnu.no/buk/ny/ical.php?p=bukaros

// 2. Define helper functions

// Converts a unix timestamp to an ics-friendly format
// NOTE: "Z" means that this timestamp is a UTC timestamp. If you need
// to set a locale, remove the "\Z" and modify DTEND, DTSTAMP and DTSTART
// with TZID properties (see RFC 5545 section 3.3.5 for info)
//
// Also note that we are using "H" instead of "g" because iCalendar's Time format
// requires 24-hour time (see RFC 5545 section 3.3.12 for info).
function dateToCal($timestamp) {
  return date('Ymd\THis\Z', $timestamp);
}

// Escapes a string of characters
function escapeString($string) {
  return preg_replace('/([\,;])/','\\\$1', $string);
}

// 3. Echo out the ics file's contents
?>
BEGIN:VCALENDAR
VERSION:2.0
METHOD:PUBLISH
X-WR-CALNAME:Bispehaugen Ungdomskorps
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VTIMEZONE
TZID:CET
BEGIN:STANDARD
DTSTART:20001029T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20000326T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
END:VTIMEZONE
<?php
foreach($aktiviteter as $id => $aktivitet) {
	$uid = "Bispehaugen.no/arr/".$id;
	$address = $aktivitet["sted"];
	$uri = "http://bispehaugen.no";
	$title = $aktivitet["tittel"];
	$datestart = strtotime($aktivitet["dato"]." ".$aktivitet["starttid"]);
	$dateend = strtotime($aktivitet["dato"]." ".$aktivitet["sluttid"]);
	$description =  $aktivitet["ingress"];

	if (!empty($aktivitet["kakebaker"])) {
		$description = "Kakebaker: " . $aktivitet["kakebaker"] . "\r\n" . $description;
	}

	if (!empty($aktivitet["hjelpere"])) {
		$description = "Slagverkhjelpere: " . $aktivitet["hjelpere"] . "\r\n" . $description;
	}

	if (!empty($aktivitet["oppmoetetid"])) {
		$description = "Oppmøte kl. " . $aktivitet["oppmoetetid"] . "\r\n" . $description;
	}
	$description = str_replace("\r\n", "\\n\\r", $description);
?>
BEGIN:VEVENT
DTEND:<?= dateToCal($dateend) ?>

UID:<?= $uid ?>

DTSTAMP:<?= dateToCal(time()) ?>

LOCATION:<?= escapeString($address) ?>

DESCRIPTION:<?= escapeString($description) ?>

URL;VALUE=URI:<?= escapeString($uri) ?>

SUMMARY:<?= escapeString($title) ?>

DTSTART:<?= dateToCal($datestart) ?>

END:VEVENT
<?php
}
?>
END:VCALENDAR	
*/
