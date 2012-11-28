<?php 

	//funksjonalitet
	
	//TODO bredde på tabeller, og vise mer av siste innlegg, fargekoding el. av linjene så det blir mer lesbart
	
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
    
    echo "<table><tr><th></th><th>Tråd</th><th colspan = '2'>Siste innlegg i tråd</th></tr>";
  
   	//skriver ut alle temaene i forumet sortet på sist oppdaterte med siste innlegg og av hvem
   	foreach($forumtemaer as $forumtema){
   		echo "<tr><td></td><td><a href='?side=forum/innlegg&id=".$forumtema['temaid']."'>".$forumtema['tittel']."</a></td><td>".$forumtema['sisteinnleggskrevetav']." skrev </td>
   		<td>".substr($forumtema['tekst'],0,30)."[...]</td></tr>";
	};	
	echo "</table>";
?>