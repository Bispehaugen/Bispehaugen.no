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
		$tekst=post('tekst');
		$sql="INSERT INTO forum_innlegg (temaid, tekst, skrevet, skrevetavid, sistredigert) 
			VALUES ('".$_POST['temaid']."','".$tekst."','".date('Y-m-d h:i:s')."','".$_POST['medlemsid']."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);
	};
	
	//hvis noen har skrevet seg p� en liste
	if(isset($_POST['listeinnlegg'])){
		//M� hente ut brukerid fra medlemmertabellen (atm medlemsid skrives og den er feil ift databasen) 
		//TODO: f� dette til � g� p� medlemsid
		$sql="SELECT id, medlemsid FROM registrering WHERE medlemsid='".$_POST['medlemsid']."';";
		$brukerid=hent_og_putt_inn_i_array($sql,$id_verdi="medlemsid");
		//fjerner alt skummelt fra kommentarfeltet og setter inn feltet
		$kommentar=post('kommentar');
		if($_POST['flagg']==1){$flagg=1;}else{$flagg=0;};
		//sql - databasen er s�nn at pdd. kan du ikke melde deg p� n�r du allerede er p�meldt
		$sql="INSERT INTO forum_listeinnlegg (listeid, flagg, brukerid, kommentar, tid) 
			VALUES ('".$_POST['listeinnlegg']."','".$flagg."','".$brukerid[$_POST['medlemsid']]['id']."','".$kommentar."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);
	};
	
	$temaid=$_GET['id'];
	//henter ut alle innleggene i valgte forum/tema 
	$sql="SELECT forum_tema.temaid, forum_innlegg.innleggid, forum_innlegg.tekst, forum_innlegg.skrevetav,  
	forum_innlegg.skrevet FROM forum_tema, forum_innlegg 
	WHERE forum_tema.temaid=".$temaid." AND forum_innlegg.temaid=".$temaid." ORDER BY skrevet;";
	$foruminnlegg=hent_og_putt_inn_i_array($sql, "innleggid");
	
	//henter listeid til alle innlegg i valgte forum og tema som det er en liste knyttet til
	//TODO: Merk at brukerid i forum_listeinnlegg er 'id' i registrering tabellen (!=medlemsid) - B�R FIKSES SENERE
	$sql="SELECT forum_liste.listeid, forum_liste.tittel FROM forum_liste, forum_innlegg
	WHERE forum_liste.listeid=forum_innlegg.innleggid;";
	$listeinnlegg=hent_og_putt_inn_i_array($sql, "listeid");	
	
	//henter ut alle aktuelle liste-oppf�ringer
	$sql="SELECT fnavn, enavn, forum_listeinnlegg.listeid, forum_listeinnlegg.tid, forum_listeinnlegg.innleggid, forum_listeinnlegg.kommentar, 
	forum_listeinnlegg.flagg, forum_liste.expires 
	FROM forum_liste, forum_innlegg, forum_listeinnlegg, forum_tema, medlemmer, registrering 
	WHERE forum_liste.listeid=forum_innlegg.innleggid AND forum_liste.listeid=forum_listeinnlegg.listeid AND
	 forum_tema.temaid=".$temaid." AND forum_innlegg.temaid=".$temaid." AND id=brukerid and registrering.medlemsid=medlemmer.medlemsid ORDER BY tid ;";
	$listeoppforinger=hent_og_putt_inn_i_array($sql, "innleggid");	
		
	//Henter ut siste uleste innlegg i tr�d
//kommentert ut siden databasen ikke er oppdatert
	//$medlemsid= $_SESSION["medlemsid"];
	//$sql="SELECT * FROM forum_leste WHERE temaid=".$temaid." AND medlemsid=".$medlemsid.";";
	//$mysql_result=mysql_query($sql);
	//$sisteleste = mysql_fetch_array($mysql_result);
	//$sisteleste= $sisteleste['sistelesteinnlegg'];
		
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
      	if($forum_innlegg['innleggid']>$sisteleste){
	      	echo "<tr class='ulest'>";
		}
		else{
			echo"<tr>";
		}
		echo "<td class='liten_tekst'>".strftime("%a %d. %b", strtotime($forum_innlegg['skrevet']))." skrev ".$forum_innlegg['skrevetav']." </td>
   			<td>".$forum_innlegg['tekst'];
		
      	//if som skriver ut liste hvis det h�rer en til innlegget
		if($listeinnlegg[$forum_innlegg['innleggid']]){
			echo "<table>
			<tr><th colspan='2'>".$listeinnlegg[$forum_innlegg['innleggid']]['tittel']."</th></tr>";
			foreach($listeoppforinger as $listeoppforing){
				if($listeoppforing['listeid']==$forum_innlegg['innleggid']){
					if($listeoppforing['flagg']==1){
						echo "<tr><td><strike>".$listeoppforing['fnavn']." ".$listeoppforing['enavn']."</strike>";
					}else{
						echo "<tr><td>".$listeoppforing['fnavn']." ".$listeoppforing['enavn'];
					};
					echo "</td><td>".$listeoppforing['kommentar']."</td></tr>";		
				};	
			};
			//Legger til tekstfelt for � melde seg p� hvis ikke lista har expired
			if(strtotime(date('Y-m-d'))/(60*60*24) <= strtotime(substr($listeoppforing['expires'],0,10))/(60*60*24) || $listeoppforing['expires']==NULL){
			echo "<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
				<tr><td>Kommentar (frivillig):<br><input type='text' name='kommentar' autofocus><br><input type='checkbox' name='flagg' value='1'> Stryk navnet</td>
				<td><input type='hidden' name='medlemsid' value=".$_SESSION['medlemsid'].">
				<input type='hidden' name='listeinnlegg' value='".$listeinnlegg[$forum_innlegg['innleggid']]['listeid']."'>
				<input type='submit' name='nyttListeInnlegg' value='Skriv meg p� lista'></td></tr>";
			}else{
				echo "<tr><td colspan='2'><b>Det er ikke lenger mulig � melde seg p� denne lista</b></td></tr> ";	
			};
			echo "</form></table>";
  		};
  		echo "</td><td><a href''>liker</a></td></tr>";
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