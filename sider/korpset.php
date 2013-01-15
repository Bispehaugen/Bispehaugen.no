<?php
include_once "funksjoner.php";

if ($_GET['id']=="en") {
echo "
<a href=\"?side=korpset\">Vis på norsk</a>
	<table width=\"97%\">
	<h1>About the orchestra</h1>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
Bispehaugen Symphonic Band was started in 1923, and is therefore one of Trondheim’s oldest amateur bands.  
Since it’s beginning, the band’s reputation has attracted above average musicians and conductors.  In fact many later 
professional musicians were members of Bispehaugen.  We like to play various kinds of quality music ranging from movie 
music to the classics. Currently we have around 30 members.
<p>


Since the fall of 2000 Tomas Carstensen has been our permanent conductor.  
He has studied both as a trumpeter and conductor at the Music Conservatory in Trondheim.
<p>

Over the past years, the band has reached the top of the 1st division of Norway’s Band Championship (NM janitsjar), 
and very recently secured a spot in the elite division (see the .  We hope 
to hold our position in the elite division, be Trondheim’s best band, and a natural choice for mature amateur 
musicians in the region.  Bispehaugen doesn’'t want to be placed in a specific category, but we want to represent a 
musically well roundedness and serious investment within multiple categories.  <p>

Bispehaugen emphasizes a good social environment, both in and out of the rehearsal hall.  The band has 
traditional activities that don’t require an instrument, such as cafe visits, parties, and weekend tours 
outside of Trondheim.  Most of our members are recruited from Trondheim’s student population, which explains why 
the average age is 25 years old.  

<p>

We practice on Mondays starting at 19:00 at Bispehaugen School in Møllenberg, centrally located in Trondheim.  
There are usually some extra practices before the concerts as well.  If you are interested in what
 we are up to, you can stop by one of our practices, or check out <a href=\"bli_medlem.php\">this page</a>.
		
	</tr>
	</table>
";

} else {
echo "
 <a href=\"?side=korpset&id=en\">View in english</a>
	<table width=\"97%\">
	<h1>Om korpset</h1>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
Bispehaugen Ungdomskorps ble startet i 1923, og er således et av Trondheims eldste amatørkorps. Korpset har helt siden starten vært
kjent for å ha dyktige musikere og dirigenter, og mang en senere profesjonell musiker har vært medlem av Bispehaugen. Vi liker å spille
allsidig musikk med kvalitet, fra underholdningsmusikk til større originalskrevne og klassiske verk. I dag teller vi
";

	$sql = "SELECT COUNT(medlemsid) as count FROM medlemmer WHERE instrument != 'Dirigent' AND status=\"aktiv\"";

	$sql_result=mysql_query($sql);

	$row=mysql_fetch_array($sql_result);

	$count = $row["count"];

echo "
$count medlemmer, men
vi satser stort på rekruttering og håper å få med deg som medlem! 
<p>


Siden høsten 2000 har Tomas Carstensen vært vår faste dirigent. Han har utdannelse som trompetist
og dirigent fra Musikkonservatoriet i Trondheim.
<p>

Korpset har i mange år hevdet seg i toppen av norsk 1. divisjon i NM janitsjar (se resultater). Under NM i 2006 vant 
korpset 1. divisjon, og konkurrerte i elitedivisjonen i to år. I perioden 2009-2011 konkurrerte vi i 1. divisjon, og fra 2012 har korpset konkurrert i 2. divisjon
Vår musikalske målsetting er å ha full Symphonic Band-besetning, og å være et ungdomskorps i ordets rette forstand. Bispehaugen ønsker ikke å bli plassert i en bestemt bås, men skal
kjennetegnes av allsidighet og seriøs satsing innen flere områder.
<p>


Bispehaugen legger vekt på et godt sosialt miljø både i og utenfor øvingslokalet. Korpset har utenommusikalske aktiviteter som
kafébesøk, fester og helgeturer utenbys. Medlemmene våre blir i stor grad rekruttert fra studentmiljøet i Trondheim, noe som setter standarden for våre utenommusikalske aktiviteter.
<p>

Vi øver mandager fra 19:30 på Bispehaugen skole på Møllenberg, sentralt i Trondheim. Det blir også noen ekstraøvelser før
konsertene. Dersom du er interessert i hva vi holder på med, kan du ta turen innom en av øvelsene, eller gå inn på <a
href=\"?side=bli_medlem\">kontaksskjema</a>.

	</tr>
	</table>
";
}