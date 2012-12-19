<?php 

	//funksjonalitet
	
	//TODO bredde på tabellen, bare hente opp de 10 siste og ha en knapp med 'vis alle' som printer smtlige innlegg
	//og blar til bunnen, format og ev bilde i 'skrevet av' kolonnen
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	//hvis det er lagt til et nytt innlegg legges dette inn i databasen
	if(isset($_POST['temaid'])){
		$tekst=mysql_real_escape_string($_POST['tekst']);
		$sql="INSERT INTO forum_innlegg (temaid, tekst, skrevet, skrevetavid, sistredigert) 
			VALUES ('".$_POST['temaid']."','".$tekst."','".date('Y-m-d h:i:s')."','".$_POST['medlemsid']."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);
	};
	
	$temaid=$_GET['id'];
	//henter ut alle innleggene i valgte forum/tema 
	$sql="SELECT forum_tema.temaid, forum_innlegg.innleggid, forum_innlegg.tekst, forum_innlegg.skrevetavid, 
	forum_innlegg.skrevet, fnavn, enavn, medlemsid FROM forum_tema, forum_innlegg, medlemmer 
	WHERE forum_tema.temaid=".$temaid." AND forum_innlegg.temaid=".$temaid." AND medlemsid=forum_innlegg.skrevetavid ORDER BY skrevet;";
	$foruminnlegg=hent_og_putt_inn_i_array($sql, "innleggid");
		
	//Henter ut tema-tittel
	$sql="SELECT tittel, temaid FROM forum_tema WHERE temaid=".$temaid.";";
	$mysql_result=mysql_query($sql);
	$tema = mysql_fetch_array($mysql_result);
	
    #Det som printes p� sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();
	//skriver ut temaet for denne tråden
    echo "<table class='forum'><tr><th colspan = '3'>".$tema['tittel']."</th></tr>";

   	//skriver ut alle innleggene valgte forum og tema i forumet sortet på sist oppdaterte med siste innlegg og av hvem
   	foreach($foruminnlegg as $forum_innlegg){
   		echo "<tr><td class='liten_tekst'>".strftime("%a %d. %b", strtotime($forum_innlegg['skrevet']))." skrev ".$forum_innlegg['fnavn']." ".$forum_innlegg['enavn']." </td>
   		<td>".$forum_innlegg['tekst']."</td><td><a href''>liker</a></td></tr>";
	};	
	echo "
	<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
			<tr><td>Svar på innlegg:</td><td><textarea name='tekst' autofocus></textarea></td>
			<td><input type='hidden' name='medlemsid' value=".$_SESSION['medlemsid'].">
			<input type='hidden' name='temaid' value=".$temaid.">
			<input type='submit' name='nyttInnlegg' value='Lagre'></td></tr>
		</form> 
	</table>";
?>