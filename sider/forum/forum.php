<?php 

	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: index.php');
	};
	
	//henter ut alle forumene og lister de opp sammen med når siste innlegg var
	//Denne skal egentlig brukse, men databasen er ikke tilstrekkelig oppdatert enn�
	//$sql="SELECT tittel, forum.forumid, pos, sisteinnleggid, innleggid, forum_innlegg.skrevetavid, forum_innlegg.skrevet, fnavn, enavn, medlemsid 
	//FROM forum, medlemmer, forum_innlegg WHERE innleggid=sisteinnleggid AND medlemsid=skrevetavid ORDER BY forumid;";
	
	$sql="SELECT tittel, forum.forumid, pos, sisteinnleggskrevet, sisteinnleggskrevetav
	FROM forum ORDER BY forumid;";
	$mysql_result=mysql_query($sql);
	$forum = Array();

	while($row=mysql_fetch_array($mysql_result)){
    	$forumer[$row['forumid']] = $row;
	};
	
    #Det som printes p� sida
    
   //Her legges det inn en oversikt over alle forumene
    list_forum();
    
    echo "<table class='forum'><tr><th></th><th>Forum</th><th>Sist oppdatert av</th></tr>";
  
   	//skriver ut alle forumene samt hvem som la inn siste innlegg og hvor lenge siden.
   	//TODO: skal også sjekke om siste gang man sjekket forumet var før siste innlegg
   	foreach($forumer as $forum){
   		//sjekker om man er admin og dermed skal se styret, webkom og musikkomite-forumene
   		if($_SESSION['rettigheter']>2 || $forum['forumid']<3){
   			//dager siden siste innlegg
   			$dagersiden= floor(abs(strtotime(date('Y-m-d'))-strtotime(substr($forum['sisteinnleggskrevet'],0,10)))/ (60*60*24));
   			echo "<tr><td></td><td><a href='?side=forum/tema&id=".$forum['forumid']."'>".$forum['tittel']."</a></td><td>
   			".$forum['sisteinnleggskrevetav'];
   			if ($dagersiden==0){
   				echo" i dag</td></tr>";
   			}
			elseif ($dagersiden==1){
   				echo" i går</td></tr>";
   			}
			elseif($dagersiden<7){
				echo" for ".$dagersiden." dager siden</td></tr>";
			}
			elseif($dagersiden<31){
				echo" for ".floor($dagersiden/7)." uker siden</td></tr>";
			}else{
				echo" for ".floor($dagersiden/30)." måneder siden</td></tr>";
			};
		};
	};	

	echo "</table>";
?>