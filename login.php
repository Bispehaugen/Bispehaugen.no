
<?php 
	include_once "db_config.php";
	include_once "funksjoner.php";

	$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

	if ( $tilkobling === false ){
    	exit("tilkoblingsfeil");
	}
	
	$username=post("username");
	$password=generer_passord_hash(post("password"));
	$password_md5=md5(post("password"));
	
	#Sjekker om passordet finnes i medlemmer-tabellen
	$sql="SELECT COUNT(brukernavn) FROM medlemmer WHERE brukernavn='".$username."' AND passord='".$password."'";
	$mysql_result=mysql_query($sql);
	
	$row=mysql_fetch_assoc($mysql_result);

	#Henter ut medlemsid (uavhengig av om passord er riktig)
	$sql="SELECT medlemsid FROM medlemmer WHERE brukernavn='".$username."'";
	$mysql_result=mysql_query($sql);
	$medlemsid=mysql_result($mysql_result, 0);
	
	

	#If setning for å sjekke mot medlemmer for md5passord (brukes kun hvis brukernavn/passord-kombinasjonen ikke finnes i medlemmer-tabellen)
	if($row["COUNT(brukernavn)"] == 0){
		$sql="SELECT COUNT(brukernavn) FROM registrering WHERE brukernavn='".$username."' AND passord='".$password_md5."'";
		$mysql_result=mysql_query($sql);
	
		$row=mysql_fetch_assoc($mysql_result);
		
		#Dersom kombinasjonen brukernavn/passord fortsatt ikke stemmer sendes bruker tilbake til hovedsiden med en feilmelding.	
		if($row["COUNT(brukernavn)"] == 0){
			$_SESSION["Errors"]="Feil brukernavn eller passord. Kunne ikke logge inn.";
			header('Location: index.php');
			die();
		};
		
		$sql="UPDATE medlemmer SET passord='".$password."' WHERE brukernavn='".$username."'";
		mysql_query($sql);
		
	}

	#Sjekker rettigheter
	$sql="SELECT rettigheter FROM medlemmer WHERE brukernavn='".$username."'";
	$mysql_result=mysql_query($sql);

	$rettigheter=mysql_result($mysql_result, 0);
	if($rettigheter==0){
		$_SESSION["Errors"]="Du har ikke tilgang til internsidene. Vennligst kontakt webkom på 
		<a href='mailto:webkom@bispehaugen.no'>e-post</a> dersom du mener at du skulle hatt det.";
		header('Location: index.php');
		die();
	}
	
	logg_inn($medlemsid, $rettigheter);
	
	if (!has_post("ajax")){
		header('Location: index.php?side=forside');
	} else {
		echo "{} && {login: true}";
	}
	
	