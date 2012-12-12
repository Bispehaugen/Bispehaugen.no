<?php 
	//funksjonalitet
		
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	$forumid=$_GET['id'];
	//henter ut alle temaene i valgte forum og henter ut siste innlegg
	$sql="SELECT forum_tema.temaid, forum_tema.forumid, tittel, sisteinnleggid, sisteinnleggskrevetav, tekst, innleggid FROM forum_tema, forum_innlegg
	WHERE forum_tema.forumid=".$forumid." AND innleggid=sisteinnleggid ORDER BY sisteinnleggid DESC;";
	$mysql_result=mysql_query($sql);
	$forumtemaer = Array();

	while($row=mysql_fetch_array($mysql_result)){
    	$forumtemaer[$row['temaid']] = $row;
	};
		
    #Det som printes p� sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();
    
    echo "<table class='forum'><tr><th></th><th>Tråd</th><th>Siste innlegg i tråd</th></tr>";
  
   	//skriver ut alle temaene i forumet sortet på sist oppdaterte med siste innlegg og av hvem
   	foreach($forumtemaer as $forumtema){
   		echo "<tr><td></td><td><a href='?side=forum/innlegg&id=".$forumtema['temaid']."'>".$forumtema['tittel']."</a></td>
   			<td><p>".$forumtema['tekst']."</p>".$forumtema['sisteinnleggskrevetav']."</td></tr>";
	};	
	echo "</table>";
?>