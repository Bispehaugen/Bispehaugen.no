<?php
$epost_sendt = false;
$feilmeldinger = Array();

if (has_post("epost")) {
	
	$antall_forsok = has_session("antall_forsok") ? session("antall_forsok") : 0;
	
	if ($antall_forsok > 3) {
		echo "<h2>Vent litt</h2>";
		echo "<p>Du har prøvd for mange gang på rad. Vent i 10 minutter</p>";
	}
	
	$epost = post("epost");
	
	$sql = sprintf("SELECT medlemsid, fnavn, enavn, email FROM medlemmer WHERE email = '%s' LIMIT 1", post("epost"));
	
	$query = mysql_query($sql);
	
	if (mysql_num_rows($query)==1) {
				
				
		while($b = mysql_fetch_assoc($query)) {
			
			$to = $b['email'];
			$replyto = "Reply-To: Webkom <webkom@bispehaugen.no>";
			$subject = "Bispehaugen.no - Glemt passord";
			
			$token = sha1(substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand( 0 ,50 ) ,1 ) .substr( md5( time() ), 1));
			
			$sql_token = "UPDATE medlemmer SET bytt_passord_token = '".$token."' WHERE medlemsid = '".$b['medlemsid']."' LIMIT 1";
			
			mysql_query($sql_token);
			
			$message = 
"Hei ".$b["fnavn"]."!

Bruk lenken under for å skifte passordet ditt på Bispehaugen.no.
http://bispehaugen.no/?side=bytt-passord&token=".$token."

Har du ikke brukt glemt passord funksjonen? Ta kontakt med webkom@bispehaugen.no!

Med vennlig hilsen
Webkom";
	
			if (epost($to,$replyto,$subject,$message)) {
				$epost_sendt = true;
			} else {
				$feilmeldinger[] = "Det oppstod en feil under sendelse av epost.";
			}
 		}
	} else {
		$epost_sendt = true;
	}
}

?>


<h2>Glemt passord</h2>
<?php if ($epost_sendt) { ?>
	
<h3>Takk!</h3>
<p>Vi har nå sendt en epost, sjekk spam-filteret ditt hvis den ikke har kommet innen 5 minutter</p>
	
<?php
} else {
?>
<p>Har du glemt passordet ditt?</p>
<p>Skriv inn eposten du bruker, så vil du få tilsendt en epost hvis du har en bruker med eposten.</p>
<p>Hvis du ikke får epost med en gang, sjekk Spam-filteret ditt, hvis ikke, prøv en annen epost.</p>
<?php
}

echo feilmeldinger($feilmeldinger);
?>

<form action="?side=glemt-passord" method="POST">
	<input type="email" name="epost" placeholder="E-post" />
	<input type="submit" value="Tilbakestill passord" />
</form>


<p>Hvis alt slår feil, ta kontakt med <a href="mailto:webkom@bispehaugen.no">webkom</a></p>