<?php

if(!er_logget_inn()){
    header('Location: ../index.php');
};

$hentAlle = get('alle') == 1;
$medlemmer = hent_medlemmer($hentAlle, $hentStottemedlemmer = true);

//spørring som henter ut medlemsid til alle styrevervene
$sql="SELECT medlemsid, vervid, tittel, epost FROM verv WHERE komiteid='3'";
$styreverv = hent_og_putt_inn_i_array($sql);

#Det som printes p� sida

echo "<h2 class='overskrift-som-er-inline-block'>Medlemmer</h2>";

echo "<h3 class='lenke-som-er-inline-med-overskrift'>";
//lager en link til å vise alle

if(tilgang_full()){
    echo"<a href='?side=medlem/ny'><i class='fa fa-plus'></i>Legg til ny</a>";
}
if($hentAlle){
    echo" <a href='?side=medlem/liste&alle=0'><i class='fa fa-user'></i>Vis kun aktive</a>";
} else {
    echo" <a href='?side=medlem/liste&alle=1'><i class='fa fa-users'></i>Vis også sluttede</a>";
}

echo "</h3>";

echo "<section class='medlemsliste'>";
#Brukes for å skrive ut en rad med instrumentnavn.
$temp_instr="Instrumentoverskrift";

foreach($medlemmer as $medlem){
    // sjekker på status så alle aktive medlemmer skrives ut først
    if($medlem['status']=="Aktiv" || $medlem['status']=="Permisjon" || $medlem['status']=="Sluttet"){
        $instr = $medlem['instrument'];
        //sjekker om instrument er samme som forrige, hvis nei skives ut en headerlinje med instrumentnavn
        if($temp_instr != $instr){
            echo "<h3>".$medlem['instrument']."</h3>";
            $temp_instr = $medlem['instrument'];
        }
        echo "<section class='medlem'>";

        echo "<span class='navn'><a href='?side=medlem/vis&id=".$medlem['medlemsid']."'>".$medlem['fnavn']." ".$medlem['enavn']."</a></span>";
        //sjekker om permisjon eller sluttet - i så fall printes en bokstav etter navnet
        if($medlem['status'] != 'Aktiv'){
            echo "<span class='tag ".$medlem['status']."'>".$medlem['status']."</span>";
        }
        //sjekker på gruppeleder og skriver ut dette etter navnet hvis ja
        if($medlem['grleder']){
            echo "<span class='tag gruppeleder'>Gruppeleder</span>";
        }

        //sjekker om medlemmet er i styret, hvis ja kommer en "send mail" link bak navnet
        $medlemsid = $medlem['medlemsid'];

        if(!empty($medlemsid) && !empty($styreverv) && !empty($styreverv[$medlemsid])){
            echo "<span class='epost-lenke'><a href='mailto:".$styreverv[$medlemsid]['epost']."'><i class='fa fa-envelope-o' title='Send e-post'></i>".$styreverv[$medlemsid]['tittel']."</a></span>";
        }

        if(tilgang_full()){
            echo"<span class='verktoy'><a href='?side=medlem/endre&id=".$medlem['medlemsid']."'><i class='fa fa-edit' title='Klikk for å endre'></i></a></span>";
        }

        if($hentAlle==0 && $medlem['tlfmobil']){
            $flere_telefonnummer = explode("/", $medlem['tlfmobil']);

            foreach($flere_telefonnummer as $telefonnummer) {
                //hvis man er logget inn vises mobilnummeret til alle medlemmer
                echo "<span class='telefon'><a href='tel:".$telefonnummer."'><i class='fa fa-phone'></i>".$telefonnummer."</a></span>";
            }
        }
        //hvis brukeren er admin kommer det opp endre/slette knapp på alle medlemmer
        echo "<div class='clearfix'></div>";
        echo "</section>";
    }
}
echo "</section>";
