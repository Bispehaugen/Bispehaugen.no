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
Since it’s beginning, the band’s reputation has attracted above average musicians and conductors. In fact many later 
professional musicians were members of Bispehaugen.  We like to play various kinds of quality music ranging from movie 
music to the classics. Currently we have around";

	$sql = "SELECT COUNT(medlemsid) as count FROM medlemmer WHERE instrument != 'Dirigent' AND status=\"aktiv\"";

	$sql_result=mysql_query($sql);

	$row=mysql_fetch_array($sql_result);

	$count = $row["count"];

echo "
$count members, but we always have room for more people.
<p>
"
?>
<div class="content">
	<img src="sider/bilder/Himmel.jpg" alt="smedtext" class="album-bilde-left">
	<img src="sider/bilder/Himmel.jpg" alt="smedtext" class="album-bilde-left">
</div>
<?php
echo"
Since the fall of 2000 Tomas Carstensen has been our permanent conductor.
He has studied both as a trumpeter and conductor at the Music Conservatory in Trondheim.
<p>
"
?>
<div class="album-bilde-right">
	<img src="sider/bilder/Himmel.jpg" alt="smedtext" class="album-bilde">
</div>
<?php
echo"
Over the past years, the band has reached the top of the 1st division of Norway’s Band Championship (NM janitsjar), 
and competed in the elite division. At the moment the orchestra is located in 2. division, but our goal is to regain our position 
in the 1. division.
<p>

Bispehaugen emphasizes a good social environment, both in and out of the rehearsal hall.  The band has 
traditional activities that don'’t require an instrument, such as cafe visits, parties, and weekend tours 
outside of Trondheim.  Most of our members are recruited from Trondheim’s student population, which explains why 
the average age is 25 years old.  

<p>

We practice on Mondays starting at 19:30 at Bispehaugen School in Møllenberg, centrally located in Trondheim.  
There are usually some extra practices before the concerts as well.  If you are interested in what
 we are up to, you can stop by one of our practices, or check out our <a href=\"bli_medlem.php\">sign-up page</a>.
		
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
"
?>
<div class="content">
	<img src="sider/bilder/Himmel.jpg" alt="smedtext" class="album-bilde-left">
	<img src="sider/bilder/Himmel.jpg" alt="smedtext" class="album-bilde-left">
</div>
<?php
echo"


Siden høsten 2000 har Tomas Carstensen vært vår faste dirigent. Han har utdannelse som trompetist
og dirigent fra Musikkonservatoriet i Trondheim.
<p>
"
?>
<div class="album-bilde-right">
	<img src="sider/bilder/Himmel.jpg" alt="smedtext" class="album-bilde">
</div>
<?php
echo"
<p>
Korpset har i mange år hevdet seg i toppen av norsk 1. divisjon i NM janitsjar (se resultater). Under NM i 2006 vant 
korpset 1. divisjon, og konkurrerte i elitedivisjonen i to år. I perioden 2009-2011 konkurrerte vi i 1. divisjon, og fra 2012 har korpset konkurrert i 2. divisjon
Vår musikalske målsetting er å ha full Symphonic Band-besetning, og å være et ungdomskorps i ordets rette forstand. Bispehaugen ønsker ikke å bli plassert i en bestemt bås, men skal
kjennetegnes av allsidighet og seriøs satsing innen flere områder.
</p>

<p>
Bispehaugen legger vekt på et godt sosialt miljø både i og utenfor øvingslokalet. Korpset har utenommusikalske aktiviteter som
kafébesøk, fester og helgeturer utenbys. Medlemmene våre blir i stor grad rekruttert fra studentmiljøet i Trondheim, noe som setter standarden for våre utenommusikalske aktiviteter.
</p>

Vi øver mandager fra 19:30 på Bispehaugen skole på Møllenberg, sentralt i Trondheim. Det blir også noen ekstraøvelser før
konsertene. Dersom du er interessert i hva vi holder på med, kan du ta turen innom en av øvelsene, eller gå inn på <a
href=\"?side=bli_medlem\">kontaksskjema</a>.

	</tr>
	</table>
";
}