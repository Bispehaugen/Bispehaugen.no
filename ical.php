<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
error_reporting(E_ERROR | E_PARSE);
include_once "funksjoner.php";
require_once( "./icalendar.php" );
//header('Content-type: text/calendar; charset=utf-8');

$aktiviteter = hent_aktiviteter(0, 200, 1, true);
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

	if (!empty($aktivitet["kakebakere"])) {
        $kakebakere = $aktivitet['kakebakere'];
        $bakere = "";
        if (count($kakebakere) == 1) {
            $bakere = "Kakebaker: " . $kakebakere[0]['fnavn'] . ' ' . $kakebakere[0]['enavn'];
        } else {
            $bakere = "Kakebakere: ";
            for ($i = 0; $i < count($kakebakere); $i++) {
                $bakere .= $kakebakere[$i]['fnavn'] . ' ' . $kakebakere[$i]['enavn'];
                if ($i < count($kakebakere)-1) {
                    $bakere .= ", ";
                }
            }
        }

        $description = $bakere . "\r\n" . $description;
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

logg("ical", "{bruker:'".$brukerid."', antall_arrangement: '".count($aktiviteter)."'}");

$v->returnCalendar();
