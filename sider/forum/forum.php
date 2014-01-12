<?php 

	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: index.php');
	};
	
	//henter ut alle forumene og lister de opp sammen med nÃ¥r siste innlegg var
	//Denne skal egentlig brukse, men databasen er ikke tilstrekkelig oppdatert ennå
	//$sql="SELECT tittel, forum.forumid, pos, sisteinnleggid, innleggid, forum_innlegg.skrevetavid, forum_innlegg.skrevet, fnavn, enavn, medlemsid 
	//FROM forum, medlemmer, forum_innlegg WHERE innleggid=sisteinnleggid AND medlemsid=skrevetavid ORDER BY forumid;";
	
	$sql="SELECT tittel, forum.forumid, pos, sisteinnleggskrevet, sisteinnleggskrevetav
	FROM forum ORDER BY forumid;";
	$forumer = hent_og_putt_inn_i_array($sql, "forumid");
	
	//henter forumid til forum som har uleste innlegg
	$medlemsid= $_SESSION["medlemsid"];
	$sql="SELECT forum_tema.forumid FROM forum_leste, forum_tema WHERE medlemsid=".$medlemsid." AND forum_leste.temaid=forum_tema.temaid;";
	$uleste_forum = hent_og_putt_inn_i_array($sql, $id_verdi="forumid");
	
    #Det som printes pï¿½ sida
    
   //Her legges det inn en oversikt over alle forumene
    list_forum();
    
    echo "<table class='forum'><tr><th></th><th>Forum</th><th>Sist oppdatert av</th></tr>";
  
   	//skriver ut alle forumene samt hvem som la inn siste innlegg og hvor lenge siden.
   	foreach($forumer as $forum){
   		//sjekker om man er admin og dermed skal se styret, webkom og musikkomite-forumene
   		if($_SESSION['rettigheter']>2 || $forum['forumid']<3){
   			if($uleste_forum[$forum['forumid']]){
   				echo "<tr class='ulest'>";
			}else{
				echo "<tr>";
			};
			echo"<td></td><td><a href='?side=forum/tema&id=".$forum['forumid']."'>".$forum['tittel']."</a></td><td>
   			".$forum['sisteinnleggskrevetav']." - ";
   			echo ant_dager_siden($forum['sisteinnleggskrevet'])."</td></tr>";
		};
	};	

	echo "</table>";
?>