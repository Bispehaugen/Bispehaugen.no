<?php 
	//funksjonalitet
		
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	$forumid=get('id');
	//henter ut alle temaene i valgte forum og henter ut siste innlegg
	$sql="SELECT forum_tema.temaid, forum_tema.forumid, tittel, sisteinnleggid, skrevetavid, tekst, enavn, fnavn, innleggid, skrevet
	FROM forum_tema, forum_innlegg_ny, medlemmer WHERE forum_tema.forumid=".$forumid." AND innleggid=sisteinnleggid AND
	medlemsid=skrevetavid ORDER BY sisteinnleggid DESC;";
	$forumtemaer = hent_og_putt_inn_i_array($sql, $id_verdi="temaid");

	//Henter ut alle temaer med uleste innlegg
	$medlemsid= $_SESSION["medlemsid"];
	$sql="SELECT forum_leste.temaid FROM forum_leste WHERE medlemsid=".$medlemsid.";";
	$uleste_innlegg = hent_og_putt_inn_i_array($sql, $id_verdi="temaid");

    #Det som printes pï¿½ sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();
    
    echo "<table class='forum'><tr><th></th><th>Tråd</th><th>Siste innlegg i tråd</th></tr>";
  
   	//skriver ut alle temaene i forumet sortet pÃ¥ sist oppdaterte med siste innlegg og av hvem
   	foreach($forumtemaer as $temaid => $forumtema){
   		if($uleste_innlegg[$forumtema['temaid']]){
   			echo "<tr class='ulest'>";
   		}else{
	   		echo "<tr>";
		};
   		echo"<td></td><td><a href='?side=forum/innlegg&id=".$forumtema['temaid']."'>".$forumtema['tittel']."</a></td>
   			<td><p>".$forumtema['tekst']."</p>".$forumtema['fnavn']." ".$forumtema['enavn']." - ".ant_dager_siden($forumtema['skrevet'])."</td></tr>";
	};	
	echo "</table>";
?>