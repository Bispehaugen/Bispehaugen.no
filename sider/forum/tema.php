<?php 
	//funksjonalitet
		
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	$forumid=get('id');
	//henter ut alle temaene i valgte forum og henter ut siste innlegg
	$sql="SELECT forum_tema.temaid, forum_tema.forumid, tittel, sisteinnleggid, skrevetav, tekst, innleggid, skrevet
	FROM forum_tema, forum_innlegg WHERE forum_tema.forumid=".$forumid." AND innleggid=sisteinnleggid 
	ORDER BY sisteinnleggid DESC;";
	$forumtemaer = hent_og_putt_inn_i_array($sql, $id_verdi="temaid");

	//Henter ut siste leste innlegg og siste innlegg for alle temaer
	
	//Denne skal hente ut en tabell med alle temaene pÂ gitt forum i en tabell med tilh¯rende siste innlegg og siste leste innlegg
	$medlemsid= $_SESSION["medlemsid"];
	$sql = "SELECT forum_leste.temaid, forum_leste.sistelesteinnlegg, forum_innlegg.innleggid 
	FROM forum_innlegg, forum_leste, forum_tema WHERE forum_tema.temaid=forum_innlegg.temaid AND forum_tema.temaid=forum_leste.temaid
	AND forum_leste.medlemsid=".$medlemsid." ORDER BY innleggid LIMIT 1";
	
	$sist_leste_innlegg = hent_og_putt_inn_i_array($sql, $id_verdi="temaid");

    #Det som printes pÔøΩ sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();
    
    echo "<table class='forum'><tr><th></th><th>Tr√•d</th><th>Siste innlegg i tr√•d</th></tr>";
  
   	//skriver ut alle temaene i forumet sortet p√• sist oppdaterte med siste innlegg og av hvem
   	foreach($forumtemaer as $forumtema){
   		if($sist_leste_innlegg[$forumtema['temaid']]['sistelesteinnlegg'] < $sist_leste_innlegg[$forumtema['temaid']]['innleggid']){
   			echo "<tr class='ulest'>";
   		}else{
	   		echo "<tr>";
		};
   		echo"<td></td><td><a href='?side=forum/innlegg&id=".$forumtema['temaid']."'>".$forumtema['tittel']."</a></td>
   			<td><p>".$forumtema['tekst']."</p>".$forumtema['skrevetav']." - ".ant_dager_siden($forumtema['skrevet'])."</td></tr>";
	};	
	echo "</table>";
?>