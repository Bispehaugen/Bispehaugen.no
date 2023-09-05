<?php
	include_once "funksjoner.php";

	$epost=post("epost");

    $update_hash = false;
	
	##Sjekker om passordet stemmer med eposten
	$sql = "SELECT medlemsid, rettigheter, passord FROM medlemmer WHERE email=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($epost));
    $row = $stmt->fetch();

	if (!password_verify(post("password"), $row["passord"])) {
        // Sjekker om passordet er lagret i det gamle formatet
        $hash = generer_passord_hash(post("password"));
        if($row['passord'] == $hash) {
            $update_password = true;
        } else {
            $_SESSION["Errors"] = "Kunne ikke logge inn, e-post eller passord er feil. Kontakt webkom hvis dette fortsetter :)";
            if (!has_get("ajax")){
                header('Location: ?side=login');
                die();
            } else {
                die("{} && {login: false}");
            }
        }
	}

    // Sjekker om det har kommet en ny passord_algoritme siden dette passordet ble lagret, og oppdaterer til den
    if ($update_password || password_needs_rehash($hash, $passord_algo)) {
        $hash = password_hash(post("password"), $passord_algo);
        $sql = "UPDATE medlemmer SET passord=:hash WHERE email=:email";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(":hash" => $hash, ":email" => $epost));
    }
	
	##Henter ut medlemsid og rettigheter
	$medlemsid = $row["medlemsid"];
	$rettigheter = $row["rettigheter"];
	
	if($rettigheter==0){
		$_SESSION["Errors"]="Du har ikke tilgang til internsidene. Vennligst kontakt webkom på 
		<a href='mailto:webkom@bispehaugen.no'>e-post</a> dersom du mener at du skulle hatt det. Dersom du nylig har 
		blitt medlem kan det være at brukeren ditt ikke har fått tilgang ennå.";
		header('Location: ?side=login');
		die();
	}

    $husk = (post("husk_meg") == "Ja");
	
	logg_inn($medlemsid, $rettigheter, $husk);
	
	if (!has_get("ajax")){
		header('Location: index.php?side=forside');
		die();
	} else {
		echo "{} && {login: true}";
	}
