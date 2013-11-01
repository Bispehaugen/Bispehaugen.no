<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
include_once "db_config.php";
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

$aktiviteter = hent_aktiviteter();
$filename = date("d-m-Y").".ical";


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
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
<?php
foreach($aktiviteter as $id => $aktivitet) {
	$uid = "Bispehaugen.no/arr/".$id;
	$address = $aktivitet["sted"];
	$uri = "http://bispehaugen.no";
	$title = $aktivitet["tittel"];
	$datestart = strtotime($aktivitet["dato"]." ".$aktivitet["starttid"]);
	$dateend = strtotime($aktivitet["dato"]." ".$aktivitet["sluttid"]);
	$description =  "Oppmøte kl. " . $aktivitet["oppmoetetid"] . "\n" .
					"Slagverkhjelpere: " . $aktivitet["hjelpere"] . "\n" .
					"Kakebaker: " . $aktivitet["hjelpere"] . "\n" .
					$aktivitet["ingress"];
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
