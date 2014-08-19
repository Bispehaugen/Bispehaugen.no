
<?php 
	include_once "db_config.php";
	include_once "funksjoner.php";

	$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

	if ( $tilkobling === false ){
    	exit("tilkoblingsfeil");
	}
	
	$epost=post("epost");
	$password=generer_passord_hash(post("password"));
	
	#Sjekker om passordet finnes i medlemmer-tabellen
	$sql="SELECT COUNT(email) FROM medlemmer WHERE email='".$epost."' AND passord='".$password."'";
	$mysql_result=mysql_query($sql);
	
	$row=mysql_fetch_assoc($mysql_result);

	#Henter ut medlemsid (uavhengig av om passord er riktig)
	$sql="SELECT medlemsid FROM medlemmer WHERE email='".$epost."'";
	$mysql_result=mysql_query($sql);
	$medlemsid=mysql_result($mysql_result, 0);

	#Sjekker rettigheter
	$sql="SELECT rettigheter FROM medlemmer WHERE email='".$epost."'";
	$mysql_result=mysql_query($sql);

	$rettigheter=mysql_result($mysql_result, 0);
	if($rettigheter==0){
		$_SESSION["Errors"]="Du har ikke tilgang til internsidene. Vennligst kontakt webkom på 
		<a href='mailto:webkom@bispehaugen.no'>e-post</a> dersom du mener at du skulle hatt det. Dersom du nylig har 
		blitt medlem kan det være at brukeren ditt ikke har fått tilgang ennå.";
		header('Location: index.php');
		die();
	}
	
	logg_inn($medlemsid, $rettigheter);
	
	if (!has_post("ajax")){
		header('Location: index.php?side=forside');
	} else {
		echo "{} && {login: true}";
	}
	
	